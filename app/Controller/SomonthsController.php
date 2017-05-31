<?php

// Attach lib cal_price
App::import('Vendor','unit_convertor/unit_convertor');

App::import('Vendor', 'cal_price/cal_price');
App::import('Vendor', '2dcutting/2dCutting');

App::uses('AppController', 'Controller');

class SomonthsController extends AppController {

	var $name = 'Somonths';
	public $helpers = array();
	public $opm; //Option Module
	public $cal_price; //Option cal_price
	var $modelName = 'Somonth';

	public function beforeFilter() {
		if(!isset($_SESSION['is_system_admin']))
			parent::beforeFilter();
		else {
			if(!$this->Session->check('format_currency')){
				$this->selectModel('Stuffs');
				$format_currency = $this->Stuffs->select_one(array('value'=>'Format Currency'));
				$format_date = $this->Stuffs->select_one(array('value'=>'Format Date'));
				$this->Session->write('format_currency', (isset($format_currency['format_currency']) ? $format_currency['format_currency'] : 2));
				$this->Session->write('format_date', (isset($format_date['format_date']) ? $format_date['format_date'] : 'd M, Y'));
			}
		}
		$this->set_module_before_filter('Somonth');

		$this->set('title_entry', 'Sales Order');
	}

	public function rebuild_setting($arr_setting=array()){
		parent::rebuild_setting();
		$arr_settings = $this->opm->arr_settings;
		if(!$this->check_permission('shippings_@_entry_@_view'))
			unset($arr_settings['relationship']['ship_invoice']['block']['shipping']);
		if(!$this->check_permission('salesinvoices_@_entry_@_view'))
			unset($arr_settings['relationship']['ship_invoice']['block']['invoice']);
		$this->opm->arr_settings = $arr_settings;
		$params = isset($this->params->params['pass'][0]) ? $this->params->params['pass'][0] : null;
		$valid = false;
		if($this->params->params['action'] == 'entry' || $valid = in_array($params,array('line_entry','text_entry'))){
			if(!$this->check_permission('salesorders_@_entry_@_edit')){
				if($valid){
                    $this->opm->set_lock_option('line_entry', 'products');
                    $this->opm->set_lock_option('text_entry', 'products');
                    $this->set('lock_product', true);
                } else {
                    $this->opm->set_lock(array('name'), 'out');
                    $this->set('address_lock', '1');
                }
                return;
			}
			$query = $this->opm->select_one(array('_id'=> new MongoId($this->get_id())),array('job_id','status'));
			$continue = true;
			if(isset($query['job_id'])&&is_object($query['job_id'])){
				$this->selectModel('Job');
                $job = $this->Job->select_one(array('_id'=> new MongoId($query['job_id'])),array('status','work_end'));
		   		$_id = $this->checkClosingMonth($job);
                if(!$this->Session->check('JobsOpen_'.$_id)){
                    if($_id){
                        if($valid){
                            $this->opm->set_lock_option('line_entry', 'products');
                            $this->opm->set_lock_option('text_entry', 'products');
                            $this->set('lock_product', true);
                        } else {
                            $this->opm->set_lock(array(), 'out');
                            $this->set('address_lock', '1');
                            $this->set('closing_month',1);
                        }
                        $continue = false;
        				$this->set('closing_month_id',$_id);
                    }
                }
                if($continue && isset($job['status']) && $job['status'] == 'Completed'){
                    if($valid){
                        $this->opm->set_lock_option('line_entry', 'products');
                        $this->opm->set_lock_option('text_entry', 'products');
                        $this->set('lock_product', true);
                    } else {
                        $this->opm->set_lock(array('name'), 'out');
                        $this->set('address_lock', '1');
                    }
                    $continue = false;
                }
			}
			if($continue && isset($query['status'])&& !in_array(  $query['status'], array('New', 'INVOICING') )) {
				if($valid){
					$this->opm->set_lock_option('line_entry', 'products');
					$this->opm->set_lock_option('text_entry', 'products');
					$this->set('lock_product', true);
					unset($this->opm->arr_settings['relationship']['line_entry']['block']['products']['custom_box_top']);
                    unset($this->opm->arr_settings['relationship']['text_entry']['block']['products']['custom_box_top']);
				} else {
					$this->opm->set_lock(array('status'), 'out');
					$this->set('address_lock', '1');
				}
                $continue = false;
			}
		}
	}
	function ajax_save(){
		if(!$this->check_cond_process_payment()){
			echo 'ask_send_email_csr';
			die;
		} else if( isset($_POST['field'])){
			if($_POST['func']=='update'&&!$this->check_permission($this->name.'_@_entry_@_edit')){
				echo 'You do not have permission on this action.';
				die;
			}
			if( $_POST['field'] == 'code') {
				$values = $_POST['value'];
				$value = $values['value'];
				$password = $values['password'];
				$this->selectModel('Stuffs');
				$change = $this->Stuffs->select_one(array('value'=>'Changing Code'));
				if(md5($password)!=$change['password']){
					echo 'wrong_pass';
					die;
				}
				$old = $this->opm->select_one(array('code'=>$value),array('_id'));
				if(isset($old['_id'])){
					echo 'code_existed';
					die;
				}
				$ids = $this->opm->update($_POST['ids'], $_POST['field'], $value);
				die;
			} else if(  $_POST['field'] == 'status' && $_POST['value'] != 'Completed'){
				$id = $this->get_id();
				$query = $this->opm->select_one(array('_id'=> new MongoId($id)),array('status', 'job_id'));
				if($query['status'] == 'Completed' && !$this->check_permission($this->name.'_@_change_status_@_edit')){
					echo 'You do not have permission to do this.';
					die;
				} else if($_POST['value'] == 'Cancelled'){
					$this->opm->save(array('_id' => new MongoId($id), 'status' => 'Cancelled' ));
					$this->selectModel('Task');
					$this->Task->collection->update(array('salesorder_id' => new MongoId($id)), array('$set'=> array('status' => 'Cancelled', 'status_id' => 'Cancelled')), array("multiple" => true));
					if( isset($query['job_id']) && is_object($query['job_id']) ) {
						$this->selectModel('Job');
						$this->Job->save(array('_id' => $query['job_id'], 'status' => 'Cancelled', 'status_id' => 'Cancelled'));
					}
				}
			} else if ($_POST['field'] == 'mail_sent' && $_POST['value']) {
				$order = $this->opm->select_one(array('_id'=> new MongoId($this->get_id())), array('code', 'company_id', 'company_name', 'contact_id', 'contact_name', 'email', 'our_csr_id', 'our_rep', 'mail_send_to', 'heading', 'asset_status', 'status', 'salesorder_date', 'payment_due_date', 'salesorder_type', 'our_rep', 'oure_rep_id', 'invoice_address', 'shipping_address', 'tax'));

				if (!isset($order['mail_send_to'])) {
					echo 'Email Customer or Email CSR must be selected to do this action.';
					die;
				}
				$this->selectModel('Contact');
				if ($order['mail_send_to'] == 'our_csr') {
					if (!is_object($order['our_csr_id'])){
						echo 'This order must have CSR to send email.';
						die;
					}
					$contact = $this->Contact->select_one(array('_id' => $order['our_csr_id']), array('email'));
					if (!isset($contact['email']) || !filter_var($contact['email'], FILTER_VALIDATE_EMAIL)) {
						echo 'Our CSR\'s email is not valid.';
						die;
					}
					$emailData = array('email' => $contact['email'], 'name' => $order['our_rep'], 'template_name' => 'Call for pickup - Our CSR');
				} else if ($order['mail_send_to'] == 'customer') {
					$email = isset($order['email']) ? $order['email'] : '';
						if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
						if (is_object($order['contact_id'])){
							$contact = $this->Contact->select_one(array('_id' => $order['our_csr_id']), array('email'));
							$email = isset($contact['email']) ? $contact['email'] : '';
						}
					}
					if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
						if (is_object($order['company_id'])){
							$this->selectModel('Company');
							$company = $this->Company->select_one(array('_id' => $order['our_csr_id']), array('email', 'contact_default_id'));
							if (isset($company['email']) && filter_var($company['email'], FILTER_VALIDATE_EMAIL)) {
								$email = $company['email'];
							} else if (isset($company['contact_default_id']) && is_object($company['contact_default_id'])) {
								$contact = $this->Contact->select_one(array('_id' => $company['contact_default_id']), array('email'));
								$email = isset($contact['email']) ? $contact['email'] : '';
							}
						}
					}
					if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
						echo 'Customer\'s email is not valid.';
						die;
					}
					$emailData = array('email' => $email, 'name' => $order['company_name'], 'template_name' => 'Call for pickup - Customer');
				}

				if (!isset($emailData)) {
					echo 'No email data found.';
					die;
				}

				$subject = 'Jobtraq - Sales Order Ref No: '.$order['code'];
				$email = array(
						   'contact_name' 	=> 	$emailData['name'],
						   'contact_id'		=>	'',
						   'template_name'	=>	$emailData['template_name'],
						   'content'		=> 	'',
						   );
				$email = array_merge($email, $order);
				$email['to'] = $emailData['email'];
				$email['template'] = $this->get_email_template($email);
				$email['subject'] = $subject;
				//Gửi mail cho our_csr
				$this->auto_send_email($email);
				$this->add_from_module($this->get_id(),'Email', array(
																	'not_redirect'	=>	true,
																	'name'			=>	$email['subject'],
																	'content'		=>	$email['template'],
																	'comms_status'	=> 	'Sent',
																	'contact_name'	=>	$email['contact_name'],
																	'contact_id'	=>	$email['contact_id'],
																	'email'			=>	$email['to'],
																	'contact_from'	=> 	$this->opm->user_name(),
																	'identity'		=> 	'Auto Send',
																	'sign_off'		=> 	'',
																	));
			} else if ($_POST['field'] == 'delivery_method' && $_POST['value'] == 'Call for Pick Up') {
				$id = new MongoId($this->get_id());
				$order = $this->opm->select_one(array('_id'=> new MongoId($this->get_id())), array('company_id', 'email', 'our_csr_id'));
				if (empty($order['email']) && empty($order['company_id']) && empty($order['our_csr_id'])) {
					echo 'Customer or CSR must be filled to do this action.';
					die;
				}

				$this->opm->save(array(
						'_id' => $order['_id'],
						'delivery_method' => 'Call for Pick Up',
						'shipper' => '',
						'shipper_id' => '',
						'shipper_account'	=> ''
					));
				echo (string)$id.'||delivery_method';
				die;
			}

		}
		parent::ajax_save();
	}
	function save_data($field = '', $value = '', $ids = '', $valueid = ''){
		if(isset($_POST['field'])){
			if($_POST['field'] == 'job_name'){
				$ids = new MongoId($this->get_id());
				$query = $this->opm->select_one(array('_id'=> $ids),array('shipping_id'));
				$this->selectModel('Shipping');
				$arr_shippings = $this->Shipping->select_all(array(
				                            'arr_where'=>array('salesorder_id'=>$ids),
				                            'arr_field'=>array('id')
				                            ));
				if($arr_shippings->count()){
					$this->selectModel('Job');
					$job = $this->Job->select_one(array('_id'=> new MongoId($_POST['valueid'])),array('name','no'));
					$arr_save = array(
					                  'job_id' => $job['_id'],
					                  'job_name' => isset($job['name']) ? $job['name'] : '',
					                  'job_number' => $job['no'],
					                  );
					if(!empty($job)){
						foreach($arr_shippings as $shipping){
							$arr_save = array_merge($arr_save,array('_id'=>$shipping['_id']));
							$this->Shipping->save($arr_save);
						}
					}
				}
			}
		}
		parent::save_data();
	}
	function check_cond_process_payment(){
		$id = $this->get_id();
		$salesorder = $this->opm->select_one(array('_id'=> new MongoId($id)),array('payment_terms','our_csr_id'));
		$name = 'Email CSR (payment term)';
		$name = str_replace(")",".*.",$name);
		$name = str_replace("(",".*.",$name);
		$this->selectModel('AutoProcess');
		$process= $this->AutoProcess->select_one(array('controller'=> new MongoRegex('/'.$this->name.'/i'),'name'=>new MongoRegex('/'.$name.'/i')));
		if($_POST['value']=='Completed'
		   &&isset($salesorder['payment_terms'])&&$salesorder['payment_terms']==0){
			if(!empty($process)){
				$user_id = (string)$this->opm->user_id();
				//Nếu người chuyển status là Accountant không cần xét
				$this->selectModel('Stuffs');
				$accountant = $this->Stuffs->select_one(array('value'=>'Accountant'));
				if(isset($accountant['accountant_id'])
				   &&$user_id==(string)$accountant['accountant_id'])
					return true;
				//Nếu SO do người đang chuyển status tạo, không cần xét
				$this->selectModel('Task');
				$task = $this->Task->select_one(array('_id'=>new MongoId($id),'type'=>'SO','our_rep_type'=>'contacts'),array('our_rep_id'));
				if(isset($task['our_rep_id'])
				   &&$user_id==(string)$task['our_rep_id'])
					return true;
				if($user_id!=(string)$salesorder['our_csr_id'])
					return false;
			}

		}
		return true;
	}
	function send_our_csr(){
		$name = 'Email CSR (payment term)';
		$name = str_replace(array(')','('),".*.",$name);
		$this->selectModel('AutoProcess');
		$process= $this->AutoProcess->select_one(array('controller'=> new MongoRegex('/'.$this->name.'/i'),'name'=>new MongoRegex('/'.$name.'/i')));
		if(!empty($process)){
			$salesorder = $this->opm->select_one(array('_id'=> new MongoId($this->get_id())));
			if(!is_object($salesorder['our_csr_id'])){
				echo 'This record must have our CSR to send email.';
				die;
			}
			$content = '';//'<p>Please change the status of <a href="'.URL.'/'.strtolower($this->name).'/entry/'.$salesorder['_id'].'">Sales Order Ref No: '.$salesorder['code'].'</a> to Completed.</p>';
			$subject = 'Jobtraq - Sales Order Ref No: '.$salesorder['code'];
			$this->selectModel('Contact');
			$our_csr = $this->Contact->select_one(array('_id'=>new MongoId($salesorder['our_csr_id'])));
			if(!isset($our_csr['email']) || !filter_var($our_csr['email'], FILTER_VALIDATE_EMAIL)){
				echo 'The email of our crs is not valid.';
				die;
			}
			$email = array(
					   'contact_name' 	=> 	$our_csr['full_name'],
					   'contact_id'		=>	new MongoId($our_csr['_id']),
					   'template_id'	=>	$process['email_template_id'],
					   'content'		=> 	$content,
					   );
			if(isset($process['extra_email'])&&filter_var($process['extra_email'], FILTER_VALIDATE_EMAIL)){
				$email['cc'] = $process['extra_email'];
			}
			$email['to'] = $our_csr['email'];
			$email['template'] = $this->get_email_template($email);
			$email['subject'] = $subject;
			//Gửi mail cho our_csr
			$this->auto_send_email($email);
			$option = array(
							'not_redirect'	=>	true,
							'name'			=>	$email['subject'],
							'content'		=>	$email['template'],
							'comms_status'	=> 	'Sent',
							'contact_name'	=>	$email['contact_name'],
							'contact_id'	=>	$email['contact_id'],
							'email'			=>	$email['to'],
							'contact_from'	=> 	$this->opm->user_name(),
							'identity'		=> 	'Auto Send',
							'sign_off'		=> 	'',
							);
			$this->add_from_module($this->get_id(),'Email',$option);
			$comms_id = $this->Communication->mongo_id_after_save;
			$this->Session->setFlash('<span>An email has just been sent to '.$email['to'].' by jobtraq.mail@gmail.com.</span><a id="notifyTopsub" style="right: 35px;position: absolute;" href="'.URL.'/communications/entry/'.$comms_id.'">View</a>','default',array('class'=>'flash_message'));
			echo 'ok';
		}
		die;
	}
	function send_accountant($accountant_id){
		$name = 'Email Accounting (Completed)';
		$name = str_replace(array(')','('),".*.",$name);
		$this->selectModel('AutoProcess');
		$process= $this->AutoProcess->select_one(array('controller'=> new MongoRegex('/'.$this->name.'/i'),'name'=>new MongoRegex('/'.$name.'/i')));
		if(!empty($process)){
			$salesorder = $this->opm->select_one(array('_id'=> new MongoId($this->get_id())));
			$content = '';//'<p>All tasks of <a href="'.URL.'/'.strtolower($this->name).'/entry/'.$salesorder['_id'].'">Sales Order Ref No: '.$salesorder['code'].'</a> have been set status to Completed. Please check.</p>';
			$subject = 'Jobtraq - Sales Order Ref No: '.$salesorder['code'];
			$this->selectModel('Contact');
			$accountant = $this->Contact->select_one(array('_id'=>new MongoId($accountant_id)));
			if(!isset($accountant['email']) || !filter_var($accountant['email'], FILTER_VALIDATE_EMAIL)){
				echo 'The email of accountant is not valid.';
				die;
			}
			$email = array(
					   'contact_name' 	=> 	$accountant['full_name'],
					   'contact_id'		=>	new MongoId($accountant['_id']),
					   'template_id'	=>	$process['email_template_id'],
					   'content'		=> 	$content,
					   );
			if(isset($process['extra_email'])&&filter_var($process['extra_email'], FILTER_VALIDATE_EMAIL)){
				$email['cc'] = $process['extra_email'];
			}
			$email['to'] = $accountant['email'];
			$email['template'] = $this->get_email_template($email);
			$email['subject'] = $subject;
			//Gửi mail cho accountant
			$this->auto_send_email($email);
			$option = array(
							'not_redirect'	=>	true,
							'name'			=>	$email['subject'],
							'content'		=>	$email['template'],
							'comms_status'	=> 	'Sent',
							'contact_name'	=>	$email['contact_name'],
							'contact_id'	=>	$email['contact_id'],
							'email'			=>	$email['to'],
							'contact_from'	=> 	$this->opm->user_name(),
							'identity'		=> 	'Auto Send',
							'sign_off'		=> 	'',
							);
			$this->add_from_module($this->get_id(),'Email',$option);
			$comms_id = $this->Communication->mongo_id_after_save;
			$this->Session->setFlash('<span>An email has just been sent to '.$email['to'].' by jobtraq.mail@gmail.com.</span><a id="notifyTopsub" style="right: 35px;position: absolute;" href="'.URL.'/communications/entry/'.$comms_id.'">View</a>','default',array('class'=>'flash_message'));
			die;
		}
	}
	public function entry() {
		$mod_lock = '0';

		$arr_set = $this->opm->arr_settings;
		// Get value id
		$iditem = $this->get_id();
		if ($iditem == '')
			$iditem = $this->get_last_id();

		$this->set('iditem', $iditem);
		//Load record by id
		$arr_tmp = array();
		if ($iditem != '') {
			$arr_tmp = $this->opm->select_one(array('_id' => new MongoId($iditem)),array('code','sales_order_type','date_modified','created_by','modified_by','description','company_name','company_id','contact_name','contact_id','phone','email','salesorder_date','payment_due_date','our_rep','our_rep_id','our_csr','our_csr_id','invoice_address','shipping_address','status','payment_terms','tax','taxval','customer_po_no','heading','name','job_name','job_number','job_id','quotation_name','quotation_number','quotation_id','delivery_method','shipper','shipper_id','shipper_account','sum_amount','sum_sub_total','modified_by_name','created_by_name', 'asset_status', 'mail_send_to', 'mail_sent','datetime_pickup','datetime_delivery'));
			if(!isset($arr_tmp['_id'])){
                $query = $this->opm->select_one(array('deleted' => 'no_search', '_id' => new MongoId($iditem)),array('deleted','date_modified','modified_by','code'));
                if( $query['deleted'] ){
                    $this->selectModel('Contact');
                    $contact = $this->Contact->select_one(array('_id'=> $query['modified_by']),array('full_name'));
                    if(!isset($contact['full_name']))
                        $contact['full_name'] = 'System Admin';
                    echo "This Sales order #{$query['code']} has been deleted by {$contact['full_name']} at ".date('d M, Y h:i:s',$query['date_modified']->sec).'. Click this <a href="'.URL.'/salesorders/lasts'.'" style="color: blue;">link</a> to go to the lastest Sales order.';
                    $this->autoRender = false;
                    return;
                }
            }
			foreach ($arr_set['field'] as $ks => $vls) {
				foreach ($vls as $field => $values) {
					if (isset($arr_tmp[$field])) {
						$arr_set['field'][$ks][$field]['default'] = $arr_tmp[$field];
						if (in_array($field, $arr_set['title_field']))
							$item_title[$field] = $arr_tmp[$field];

						if ((preg_match("/_date$/", $field) || $field == 'date_in' || $field == 'work_start' || $field == 'work_end') && is_object($arr_tmp[$field]))
							$arr_set['field'][$ks][$field]['default'] = date('m/d/Y ', $arr_tmp[$field]->sec);

						else if (isset($arr_tmp[$field]) && is_object($arr_tmp[$field]) && preg_match("/^datetime/", $field)){
							$arr_set['field'][$ks][$field]['default'] = date('m/d/Y H:i:s', $arr_tmp[$field]->sec);
						}

						//chế độ lock, hiện name của các relationship custom
						else if (($field == 'company_name' || $field == 'contact_name') && $mod_lock == '1')
							$arr_set['field'][$ks][$field]['default'] = $arr_tmp[$field];
						/*else if ($this->opm->check_field_link($ks, $field)) {
							$field_id = $arr_set['field'][$ks][$field]['id'];
							if (!isset($arr_set['field'][$ks][$field]['syncname']))
								$arr_set['field'][$ks][$field]['syncname'] = 'name';
							$arr_set['field'][$ks][$field]['default'] = $this->get_name($this->ModuleName($arr_set['field'][$ks][$field]['cls']), $arr_tmp[$field_id], $arr_set['field'][$ks][$field]['syncname']);
							if($arr_set['field'][$ks][$field]['default']==''){
								$arr_set['field'][$ks][$field]['default'] = $arr_tmp[$field];
							}
						}else if ($field == 'company_name' && isset($arr_tmp['company_id']) && $arr_tmp['company_id'] != '') {
							$arr_set['field'][$ks][$field]['default'] = $this->get_name('Company', $arr_tmp['company_id']);
						} else if ($field == 'contact_name' && isset($arr_tmp['contact_id']) && $arr_tmp['contact_id'] != '') {
							$arr_set['field'][$ks][$field]['default'] = $this->get_name('Contact', $arr_tmp['contact_id']);
							$item_title[$field] = $this->get_name('Contact', $arr_tmp['contact_id']);
						}*/
					}
				}
			}
			$arr_set['field']['panel_1']['mongo_id']['default'] = $iditem;
			$this->Session->write($this->name . 'ViewId', $iditem);

			//BEGIN custom
			if (isset($arr_set['field']['panel_1']['no']['default']))
				$item_title['no'] = $arr_set['field']['panel_1']['no']['default'];
			else
				$item_title['no'] = '1';
			$this->set('item_title', $item_title);

			//custom list tax
			$arr_options_custom['tax'] = '';
			$this->selectModel('Tax');
			$arr_options_custom['tax'] = $this->Tax->tax_select_list();
			$this->set('arr_options_custom', $arr_options_custom);
			//END custom
			//show footer info
			$this->show_footer_info($arr_tmp);


			//add, setup field tự tăng
		}else {
			$this->redirect(URL.'/salesorders/add');
		}
		$this->set('query', $arr_tmp);
		$this->set('arr_settings', $arr_set);
		$this->sub_tab_default = 'line_entry';
		$this->sub_tab('', $iditem);
		$this->set_entry_address($arr_tmp, $arr_set);

		parent::entry();
	}

	public function lists(){
		$this->set('_controller',$this);
		$this->selectModel('Salesorder');
		$limit = LIST_LIMIT;
		$skip = 0;
		$sort_field = '_id';
		$sort_type = 1;
		if( isset($_POST['sort']) && strlen($_POST['sort']['field']) > 0 ){
			if( $_POST['sort']['type'] == 'desc' ){
				$sort_type = -1;
			}
			$sort_field = $_POST['sort']['field'];
			$this->Session->write('Salesorders_lists_search_sort', array($sort_field, $sort_type));

		}elseif( $this->Session->check('Salesorders_lists_search_sort') ){
			$session_sort = $this->Session->read('Salesorders_lists_search_sort');
			$sort_field = $session_sort[0];
			$sort_type = $session_sort[1];
		}
		$arr_order = array($sort_field => $sort_type);
		$this->set('sort_field', $sort_field);
		$this->set('sort_type', ($sort_type === 1)?'asc':'desc');

		// dùng cho điều kiện
		$cond = $where_query = $this->arr_search_where();
		// dùng cho phân trang
		$page_num = 1;
		if( isset($_POST['pagination']) && $_POST['pagination']['page-num'] > 0){
			$page_num = $_POST['pagination']['page-num'];
			$limit = $_POST['pagination']['page-list'];
			$skip = $limit*($page_num - 1);
		}
		$this->set('page_num', $page_num);
		$this->set('limit', $limit);
		if(!$this->check_permission('salesorders_@_view_worktraq_@_view')){
	        $cond['code'] = array('$not' => new MongoRegex('/WT-/'));
        }
		// query
		$arr_orders = $this->Somonth->select_all(array(
			'arr_where' => $cond,
			'arr_order' => $arr_order,
			'arr_field'	=> array('code','company_name','company_id','our_csr','our_csr_id','salesorder_date','payment_due_date','heading','quotation_id','job_number','job_id','sum_sub_total','status', 'asset_status', 'shipping_cost'),
			'limit' => $limit,
			'skip' => $skip
		));
		$this->selectModel('Task');
		$arrOrders = array();
		foreach ($arr_orders as $key => $order) {
			$order = array_merge(array('asset_status' => $order['status']), $order);
			/*if ($order['status'] != 'Completed') {
				$status = '';
				$task = $this->Task->select_one(array(
											'salesorder_id' => $order['_id'],
											'our_rep_type' => 'assets',
											'status' => array(
												'$nin' => array(
														'New',
														'Cancelled'
													)
												)
										), array(
											'our_rep', 'status'
										), array(
											'date_modified' => -1
										));
				if (!empty($task)) {
					$task = array_merge(array('our_rep' => '', 'status' => ''), $task);
					$status = $task['our_rep'].' - '.$task['status'];
				}
				if (!empty($status)) {
					$order['status'] = $status;
				}
			}*/
			$arrOrders[$key] = $order;
		}
		$this->set('arr_orders', $arrOrders);


		$total_page = $total_record = $total_current = 0;
		if( is_object($arr_orders) ){
			$total_current = $arr_orders->count(true);
			$total_record = $arr_orders->count();
			if( $total_record%$limit != 0 ){
				$total_page = floor($total_record/$limit) + 1;
			}else{
				$total_page = $total_record/$limit;
			}
		}
		$this->set('total_current', $total_current);
		$this->set('total_page', $total_page);
		$this->set('total_record', $total_record);

		if ($this->request->is('ajax')) {
			$this->render('lists_ajax');
		}
		$this->set('sum', $total_record);
	}


	function arr_associated_data($field = '', $value = '', $valueid = '' , $fieldopt='') {
		$arr_return = array();
		$arr_return[$field] = $value;
		if(isset($_POST['arr']) && is_string($_POST['arr']) && $_POST['arr']!='')
			$tmp_data = (array)json_decode($_POST['arr']);
		if(isset($tmp_data['keys'])&&$tmp_data['keys']=='update'
			&&!$this->check_permission($this->name.'_@_entry_@_edit')){
			echo 'You do not have permission on this action.';
			die;
		} else if (isset($tmp_data['keys'])&&$tmp_data['keys']=='add'
			&&!$this->check_permission($this->name.'_@_entry_@_add')){
			echo 'You do not have permission on this action.';
			die;
		}
		if ($field == 'company_name' && $valueid != '') {

			$arr_return = array();
			$arr_return['company_name'] = $value;
			$arr_return['company_id'] = new MongoId($valueid);
			$this->selectModel('Company');
			$query = $this->opm->select_one(array('_id' => new MongoId($this->get_id())));
			$arr_return['name'] = $query['code'].'-'.$value;
			$this->selectModel('Salesaccount');
			$salesaccount = $this->Salesaccount->select_one(array('company_id' => $arr_return['company_id']));
			$arr_return['payment_terms'] = (isset($salesaccount['payment_terms']) ? $salesaccount['payment_terms'] : 0);
			$arr_return['payment_terms_id'] = (isset($salesaccount['payment_terms_id']) ? $salesaccount['payment_terms_id'] : 0);
			$this->selectModel('Contact');
			$arr_contact = $arrtemp = array();
			$arr_company = $this->Company->select_one(array('_id'=>new MongoId($arr_return['company_id'])));
			if (isset($arr_company['contact_default_id']) && is_object($arr_company['contact_default_id'])) {
				$arr_contact = $this->Contact->select_one(array('_id' => new MongoId($arr_company['contact_default_id'])));
			}
			elseif($fieldopt!='')
			{
				$contact_id = $fieldopt;
				$arr_contact = $this->Contact->select_one(array('_id' => new MongoId($contact_id)));
			}
			else
			{
				$arr_contact = $this->Contact->select_all(array(
					'arr_where' => array('company_id' => new MongoId($valueid)),
					'arr_order' => array('_id' => -1),
				));
				$arrtemp = iterator_to_array($arr_contact);
				if (count($arrtemp) > 0) {
					$arr_contact = current($arrtemp);
				} else
					$arr_contact = array();
			}


			if (isset($arr_contact['_id'])) {
				$arr_return['contact_name'] = $arr_contact['first_name'] . ' ' . $arr_contact['last_name'];
				$arr_return['contact_id'] = $arr_contact['_id'];
			} else {
				$arr_return['contact_name'] = '';
				$arr_return['contact_id'] = '';
			}

			if (isset($arr_company['our_rep_id']) && is_object($arr_company['our_rep_id'])) {
				$arr_return['our_rep_id'] = $arr_company['our_rep_id'];
				$arr_return['our_rep'] = $arr_company['our_rep'];
			} else {
				$arr_return['our_rep_id'] = $this->Company->user_id();
				$arr_return['our_rep'] = $this->Company->user_name();
			}




			if (isset($arr_company['our_csr_id']) && $arr_company['our_csr_id'] != '' && $arr_company['our_csr_id']!=null) {
				$arr_return['our_csr_id'] = $arr_company['our_csr_id'];
				$arr_return['our_csr'] = $arr_company['our_csr'];
			} else {
				$arr_return['our_csr_id'] = $this->Company->user_id();
				$arr_return['our_csr'] = $this->Company->user_name();
			}

			$arr_return['phone'] = '';
			if (isset($arr_company['phone']))
				$arr_return['phone'] = $arr_company['phone'];
			if (isset($arr_contact['direct_dial']) && $arr_contact['direct_dial'] != '')
				$arr_return['phone'] = $arr_contact['direct_dial'];

			$arr_return['company_phone'] = '';
			if (isset($arr_company['phone']))
				$arr_return['company_phone'] = $arr_company['phone'];


			$arr_return['direct_phone'] = '';
			if (isset($arr_contact['direct_dial']))
				$arr_return['direct_phone'] = $arr_contact['direct_dial'];

			$arr_return['mobile'] = '';
			if (isset($arr_contact['mobile']))
				$arr_return['mobile'] = $arr_contact['mobile'];


			$arr_return['home_phone'] = '';
			if (isset($arr_contact['home_phone']))
				$arr_return['home_phone'] = $arr_contact['home_phone'];

			$arr_return['email'] = '';
			if (isset($arr_company['email']))
				$arr_return['email'] = $arr_company['email'];
			if (isset($arr_contact['email']) && $arr_contact['email'] != '')
				$arr_return['email'] = $arr_contact['email'];


			$arr_return['fax'] = '';
			if (isset($arr_company['fax']))
				$arr_return['fax'] = $arr_company['fax'];
			if (isset($arr_contact['fax']) && $arr_contact['fax'] != '')
				$arr_return['fax'] = $arr_contact['fax'];

			//change address
			if (isset($arr_company['addresses_default_key'])){
				$add_default = $arr_company['addresses_default_key'];
				$arr_return['addresses_default_key']= $arr_company['addresses_default_key'];
			}
			if (isset($add_default) && isset($arr_company['addresses'][$add_default])) {
				foreach ($arr_company['addresses'][$add_default] as $ka => $va) {
					if ($ka != 'deleted')
						$arr_return['invoice_address'][0]['invoice_' . $ka] = $va;
					else
						$arr_return['invoice_address'][0][$ka] = $va;
				}
			}
			$this->selectModel('Salesaccount');
			//change tax
			$salesaccount = $this->Salesaccount->select_one(
			                                                array('company_id' => $arr_return['company_id']),
			                                                array('tax_code','tax_code_id')
			                                                );
			if(isset($salesaccount['tax_code_id'])){
				$keytax = $salesaccount['tax_code_id'];
				$this->selectModel('Tax');
				$arr_tax = $this->Tax->tax_list();
				$arr_tax_text = $this->Tax->tax_select_list();
				$arr_return['tax'] = isset($arr_tax_text[$keytax]) ? $keytax : 'Not used';
				$arr_return['taxval'] = (float)(isset($arr_tax[$keytax]) ? $arr_tax[$keytax] : 0);
				$arr_return['taxtext'] = isset($arr_tax_text[$keytax]) ? $arr_tax_text[$keytax] : '';
			}else if(isset($arr_return['invoice_address'][0]['invoice_province_state_id'])){
				$keytax = $arr_return['invoice_address'][0]['invoice_province_state_id'];
				$this->selectModel('Tax');
				$arr_tax = $this->Tax->tax_list();
				$arr_tax_text = $this->Tax->tax_select_list();
				$arr_return['tax'] = $keytax;
				$arr_return['taxval'] = (float)$arr_tax[$keytax];
				$arr_return['taxtext'] = $arr_tax_text[$keytax];
			}
            $arr_return['products'] = $this->update_all_option('products',array('taxper'=>$arr_return['taxval']),true);

			$query = array_merge($query, $arr_return);
			$products = isset($query['products'])&&is_array($query['products']) ? $query['products'] : array();
			foreach($products as $key => $product) {
				if( isset($product['deleted']) && $product['deleted'] ) continue;
				if( isset($product['same_parent']) && $product['same_parent'] ) continue;
                $data = $this->cal_price_line(array('data'=>array('id'=>$key),'fieldchange'=>'quantity'), true, true, $query);
				$query = array_merge($query, $data);
			}
			$arr_return['products'] = $products;
			$arr_return['sum_sub_total'] = $query['sum_sub_total'];
			$arr_return['sum_tax'] = $query['sum_tax'];
			$arr_return['sum_amount'] = $query['sum_amount'];
		} else if($field == 'job_name'){
			$this->selectModel('Job');
			$job = $this->Job->select_one(array('_id'=> new MongoId($valueid)),array('no','name','custom_po_no'));
			$arr_return['job_number'] = $job['no'];
			$arr_return['job_name'] = (isset($job['name']) ? $job['name'] : '');
			$arr_return['job_id'] = new MongoId($job['_id']);
			$arr_return['customer_po_no'] = (isset($job['custom_po_no']) ? $job['custom_po_no'] : '');
		} else if ($field == 'contact_name' && $valueid != '') {
			$arr_return['contact_id'] = new MongoId($valueid);
			$this->selectModel('Contact');
			$arr_contact = $this->Contact->select_one(array('_id'=>new MongoId($valueid)));
			//change phone
			if(isset($arr_contact['direct_dial']) && $arr_contact['direct_dial']!='')
				$arr_return['phone'] = $arr_contact['direct_dial'];
			//change email
			if(isset($arr_contact['email']))
				$arr_return['email'] = $arr_contact['email'];
		} else if ($field == 'our_rep' && $valueid != '') {
			$arr_return['our_rep_id'] = new MongoId($valueid);
		} else if ($field == 'our_csr' && $valueid != '') {
			$arr_return['our_csr_id'] = new MongoId($valueid);
		} else if ($field == 'shipper' && $valueid != '') {
			$arr_return['shipper_id'] = new MongoId($valueid);
		} else if($field == 'products'){
			if(isset($value[$valueid])
				&& isset($value[$valueid]['products_id'])
				&& is_object($value[$valueid]['products_id'])
				&& $fieldopt!='code'
				&& $fieldopt!='deleted'){
				//change size other


			//giam gia cho product parrent neu la xoa item option
			} else if(isset($value[$valueid])
						&& isset($value[$valueid]['products_id'])
						&& is_object($value[$valueid]['products_id'])
						&& $fieldopt=='deleted'){
				$vv = $value[$valueid];

				if(isset($vv['option_for']) && $vv['option_for']!='' && isset($vv['same_parent']) && $vv['same_parent']==1 && isset($value[$vv['option_for']])){
					$query = $this->opm->select_one(array('_id' => new MongoId($this->get_id())),array('company_id'));
					$option_for = $vv['option_for'];
					if(!isset($query['company_id']))
						$query['company_id'] = '';

					$result = array();
					$arr_plus_temp = $value[$option_for];
					//tinh gia theo price list
					$arr_plus_temp['plus_sell_price'] = 0;
					$cal_price = new cal_price;
					$cal_price->arr_product_items = $arr_plus_temp;
					$result = $this->change_sell_price_company($query['company_id'],$vv['products_id']);
					$cal_price->price_break_from_to = $result;
					$cal_price->field_change = '';
					$arr_plus_temp = $cal_price->cal_price_items();

					//loai bo gia option
					$value[$option_for]['sell_price'] -= (float)$arr_plus_temp['sell_price'];
					$value[$option_for]['plus_sell_price'] -= (float)$arr_plus_temp['sell_price'];
					//tinh lai unit price
					$cal_price2 = new cal_price;
					$cal_price2->arr_product_items = $value[$option_for];
					$cal_price2->field_change = 'sell_price';
					$value[$option_for] = $cal_price2->cal_price_items();

				}
				$value[$valueid]['deleted'] = true;
				//pr($value);die;



			//truong hop thay Code
			} else if(isset($value[$valueid])
						&& isset($value[$valueid]['products_id'])
						&& is_object($value[$valueid]['products_id'])
						&& $fieldopt=='code'){
                $query = $this->opm->select_one(array('_id' => new MongoId($this->get_id())),array('products','options','company_id','shipping_cost'));
                if( isset($query['products'][$valueid]['sku']) && preg_match('/^SHP/', $query['products'][$valueid]['sku']) ) {
                    $query['shipping_cost'] -= $query['products'][$valueid]['sub_total'];
                    $save = true;
                }
				//remove cac option cu cua $valueid
				foreach($value as $kks=>$vvs){
					if(isset($vvs['option_for']) && $vvs['option_for']==$valueid)
					   $value[$kks] = array('deleted' => true);
				}
				if(isset($query['options']))
	                foreach($query['options'] as $options_key=>$options){
	                    if(isset($options['parent_line_no']) && $options['parent_line_no']==$valueid)
	                        $query['options'][$options_key] = array('deleted' => true);
	                }

				//tim data option cua product
				$this->selectModel('Product');
				$parent = $this->Product->select_one(array('_id'=>$value[$valueid]['products_id']));
				if(isset($parent['sku']))
					$value[$valueid]['sku'] = $parent['sku'];
				else
					$value[$valueid]['sku'] = '';
				$value[$valueid]['origin_options'] = array();

				//lay danh sach option va luu lai
				$products = $this->Product->options_data((string)$value[$valueid]['products_id']);
				if(isset($products['productoptions']) && is_array($products['productoptions']) && count($products['productoptions'])>0){
                    $total_sub_total = 0;
                    if(!isset($query['options']))
                        $query['options']= array();
                    $arr_return['options'] = $query['options'];
                    $options_num = count($arr_return['options']);
                    $line_num = count($value);
					foreach($products['productoptions'] as $kk=>$vv){
						//loop va tao moi items
						$new_array = array();
						$new_array['code'] 			= $vv['code'];
						$new_array['sku'] 			= $vv['sku'];
                        $new_array['products_name'] = $vv['product_name'];
						$new_array['product_name'] 	= $vv['product_name'];
                        $new_array['products_id']   = $vv['product_id'];
						$new_array['product_id'] 	= $vv['product_id'];
						$new_array['quantity'] 		= $vv['quantity'];
						$new_array['sub_total'] 	= $vv['sub_total'];
                        $new_array['option_group']  = (isset($vv['option_group']) ? $vv['option_group'] : '');
						if(isset($value[$valueid]['sizew']))
							$new_array['sizew'] 		= $value[$valueid]['sizew'];
						else
							$new_array['sizew'] 		= $vv['sizew'];

						if(isset($value[$valueid]['sizew_unit']))
							$new_array['sizew_unit'] 		= $value[$valueid]['sizew_unit'];
						else
							$new_array['sizew_unit'] 		= $vv['sizew_unit'];

						if(isset($value[$valueid]['sizeh']))
							$new_array['sizeh'] 		= $value[$valueid]['sizeh'];
						else
							$new_array['sizeh'] 		= $vv['sizeh'];

						if(isset($value[$valueid]['sizeh_unit']))
							$new_array['sizeh_unit'] 		= $value[$valueid]['sizeh_unit'];
						else
							$new_array['sizeh_unit'] 		= $vv['sizeh_unit'];


						$new_array['sell_by'] 		= $vv['sell_by'];
						$new_array['oum'] 		= $vv['oum'];

						if(isset($vv['same_parent']))
							$new_array['same_parent'] 	= (int)$vv['same_parent'];
						else
							$new_array['same_parent'] 	= 0;
						$more_discount 				= (float)$vv['unit_price']*((float)$vv['discount']/100);
						$new_array['sell_price'] 	= (float)$vv['unit_price'] - $more_discount;

						$new_array['taxper'] 		= (isset($value[$valueid]['taxper']) ? (float)$value[$valueid]['taxper'] : 0);
						$new_array['tax'] 			= $value[$valueid]['tax'];
						$new_array['option_for'] 	= $valueid;
						$new_array['deleted'] 		= false;
						$new_array['proids'] 		= $value[$valueid]['products_id'].'_'.$options_num;

						$this->cal_price = new cal_price;
						//truyen data vao cal_price de tinh gia
						$this->cal_price->arr_product_items = $new_array;
						//lay thong tin khach hang de tinh chiec khau/giam gia
						$result = array();
						if(!isset($query['company_id']))
							$query['company_id'] = '';
						if(isset($new_array['products_id']))
							$result = $this->change_sell_price_company($query['company_id'],$new_array['products_id']);
						//truyen bang chiec khau va gia giam vao
						$this->cal_price->price_break_from_to = $result;
						$tmp_sell_price = 0;
						if(isset($result['sell_price'])) {
	                    	$tmp_sell_price = $result['sell_price'];
	                    }
						//kiem tra field nao dang thay doi
						$this->cal_price->field_change = '';
						//chay tinh gia
						$arr_ret = $this->cal_price->cal_price_items();

                        //
                        if(isset($vv['line_no']))
                        	unset($vv['line_no']);
                        if(isset($arr_ret['same_parent']) && $arr_ret['same_parent'] == 0 && (isset($arr_ret['company_price_break']) && $arr_ret['company_price_break'] ) ) {
                        	$arr_ret['custom_unit_price'] = $arr_ret['sell_price'];
        					$arr_ret['sell_price'] = $arr_ret['unit_price'] = $tmp_sell_price;
        					$vv['sell_price'] = $vv['unit_price'] = $tmp_sell_price;
						}
                        $arr_return['options'][$options_num] = $vv;
                        $arr_return['options'][$options_num]['hidden'] = isset($vv['hidden'])&&$vv['hidden'] ? 1 : 0;
                        $arr_return['options'][$options_num]['this_line_no'] = $options_num;
                        $arr_return['options'][$options_num]['parent_line_no'] = $valueid;
                        $arr_return['options'][$options_num]['choice'] = 0;
                        $arr_return['options'][$options_num]['user_custom'] = 0;
                        if(isset($vv['require']) && (int)$vv['require']==1){
							$value[$valueid]['origin_options'][] = array(
									'_id' 	=> $vv['product_id'],
									'name'	=> $vv['product_name']
								);
							$value[$line_num] = array_merge((array)$new_array,(array)$arr_ret);
                            $value[$line_num]['user_custom'] = 0;
                            $value[$line_num]['hidden'] = $arr_return['options'][$options_num]['hidden'];
                            $arr_return['options'][$options_num]['line_no'] = $line_num;
                            $arr_return['options'][$options_num]['choice'] = 1;
                            $line_num++;
                        }
                        $options_num++;
					}
                    //=============================================
				    $query['options'] = $arr_return['options'];
                    $save = true;
                }
                $query['products'] = $value;
                if( isset($save) && $save ) {
                    $this->opm->save($query);
                    echo $valueid;
                    die;
                }
			}
			$arr_return[$field] = $value;
		}
		return $arr_return;
	}




	public function entry_search() {
		//parent class
		$arr_set = $this->opm->arr_settings;
		$arr_set['field']['panel_1']['code']['lock'] = '';
		$arr_set['field']['panel_1']['sales_order_type']['default'] = '';
		$arr_set['field']['panel_1']['our_rep']['not_custom'] = '0';
		$arr_set['field']['panel_1']['our_csr']['not_custom'] = '0';
		$arr_set['field']['panel_4']['status']['default'] = '';
		$arr_set['field']['panel_4']['payment_terms']['default'] = '';
		$arr_set['field']['panel_4']['taxval']['default'] = '';
		$arr_set['field']['panel_4']['tax']['default'] = '';
		$arr_set['field']['panel_4']['job_number']['lock'] = '';
		$arr_set['field']['panel_4']['job_name']['not_custom'] = '';
		$arr_set['field']['panel_4']['quotation_number']['lock'] = '';
		$arr_set['field']['panel_4']['quotation_name']['not_custom'] = '';
		$arr_set['field']['panel_1']['products_name'] = array(
                                                        'name' => 'Description',
                                                        'type' => 'text',
                                                        'moreclass' => 'fixbor2',
                                                        );

		$this->set('search_class', 'jt_input_search');
		$this->set('search_class2', 'jt_select_search');
		$this->set('search_flat', 'placeholder="1"');
		$where = array();
		// if ($this->Session->check($this->name . '_where'))
			// $where = $this->Session->read($this->name . '_where');
		if (count($where) > 0) {
			foreach ($arr_set['field'] as $ks => $vls) {
				foreach ($vls as $field => $values) {
					if (isset($where[$field])) {
						$arr_set['field'][$ks][$field]['default'] = $where[$field]['values'];
					}
				}
			}
		}
		unset($arr_set['field']['panel_1']['asset_status'],
				$arr_set['relationship']['line_entry']['block']['products']['add'],
				$arr_set['relationship']['line_entry']['block']['products']['custom_box_top'],
				$arr_set['relationship']['line_entry']['block']['products']['custom_box_bottom'],
				$arr_set['relationship']['line_entry']['block']['products']['delete'],
				$arr_set['relationship']['line_entry']['block']['products']['field']['sizew_unit'],
				$arr_set['relationship']['line_entry']['block']['products']['field']['sizeh_unit'],
				$arr_set['relationship']['line_entry']['block']['products']['field']['details'],
				$arr_set['relationship']['line_entry']['block']['products']['field']['option'],
				$arr_set['relationship']['line_entry']['block']['products']['field']['vip'],
				$arr_set['relationship']['line_entry']['block']['products']['field']['adj_qty'],
				$arr_set['relationship']['line_entry']['block']['products']['field']['docket_check'],
				$arr_set['relationship']['line_entry']['block']['products']['field']['oum']
			);
		$arr_set['relationship']['line_entry']['block']['products']['field']['sku']['type'] = 'text';
		$arr_set['relationship']['line_entry']['block']['products']['field']['sizew']['type'] = 'text';
		$arr_set['relationship']['line_entry']['block']['products']['field']['sizeh']['type'] = 'text';
		$arr_set['relationship']['line_entry']['block']['products']['field']['sell_price']['type'] = 'text';
		$arr_set['relationship']['line_entry']['block']['products']['field']['sell_by']['type'] = 'text';
		$arr_set['relationship']['line_entry']['block']['products']['field']['quantity']['type'] = 'text';
		$arr_set['relationship']['line_entry']['block']['products']['field']['unit_price']['type'] = 'text';
		$arr_set['relationship']['line_entry']['block']['products']['field']['custom_unit_price']['type'] = 'text';
		$this->set('subdatas', array(
			'products' => array(
				array(
					'deleted' => false,
					'code' => '',
					'sku' => '',
					'products_name' => '',
					'sizew' => '',
					'sizeh' => '',
					'quantity' => '',
				)
			)
		));
		//end parent class
		$this->set('arr_settings', $arr_set);
        $this->set('shipping_contact_name','');

		//set address
		$address_label = array('Invoice Address', 'Shipping address');
		$this->set('address_label', $address_label);
		$address_controller =  $address_key = array('invoice', 'shipping');
		$this->set('address_key', $address_key); //set
		$this->set('address_controller', $address_controller); //set
		$address_conner[0]['top'] = 'hgt fixbor';
		$address_conner[0]['bottom'] = 'fixbor2 jt_ppbot';
		$address_conner[1]['top'] = 'hgt';
		$address_conner[1]['bottom'] = 'fixbor3 jt_ppbot';
		$this->set('address_conner', $address_conner);
		$this->set('address_more_line', 3); //set
		$address_hidden_field = array('invoice_address', 'shipping_address');
		$this->set('address_hidden_field', $address_hidden_field); //set
		$address_country = $this->country();
		$this->set('address_country', $address_country); //set
		$this->set('address_country_id', ''); //set
		$address_province['invoice'] = $address_province['shipping'] = $this->province("CA");
		$this->set('address_province', ""); //set
		$this->set('address_province_id', ""); //set
		$this->set('address_onchange', "save_address_pr('\"+keys+\"');");
		$address_hidden_value = array('', '');
		$this->set('address_hidden_value', $address_hidden_value);
		$this->set('address_mode', 'search');
	}

	// Options list
	public function swith_options($keys) {
		if(!$this->check_permission('salesorders_@_view_worktraq_@_view')){
			$arr_where = array(
					'code' => array(
							'operator' => 'other',
							'values' => array('code' => array('$not' => new MongoRegex('/WT-/')))
						)

			);
		}
		parent::swith_options($keys);
		if ($keys == 'in_progress')
		{
			$arr_where = array(
					'status' => array(
							'operator' => 'other',
							'values' => array('status' => array('$in' => array('New', 'INVOICING') ) )
						)
			);
			$this->Session->write($this->name . '_where', $arr_where);
			echo URL . '/' . $this->params->params['controller'] . '/lists';
		}
		else if ($keys == 'complete')
		{
			$arr_where['status'] = array('values' => 'Completed', 'operator' => '=');
			$this->Session->write($this->name . '_where', $arr_where);
			echo URL . '/' . $this->params->params['controller'] . '/lists';
		}
		else if ($keys == 'cancelled')
		{
			$arr_where['status'] = array('values' => 'Cancelled', 'operator' => '=');
			$this->Session->write($this->name . '_where', $arr_where);
			echo URL . '/' . $this->params->params['controller'] . '/lists';
		}
		else if ($keys == 'due_today') {
			$current_date = strtotime(date("Y-m-d"));
			$current_date_end = $current_date + DAY - 1;
			$arr_where['payment_due_date']['>='] = array('values' => new MongoDate($current_date), 'operator' => 'day>=');
			$arr_where['payment_due_date']['<='] = array('values' => new MongoDate($current_date_end), 'operator' => 'day<=');
			$this->Session->write($this->name . '_where', $arr_where);
			echo URL . '/' . $this->params->params['controller'] . '/lists';
		}
		else if ($keys == 'late') {
			$current_date = strtotime(date('Y-m-d'));
			$arr_where['status'] = array('values' => array('Completed','Cancelled'), 'operator' => 'nin');
			$arr_where['payment_due_date']['operator'] = 'other';
			$arr_where['payment_due_date']['values'] = array(
				'payment_due_date' => array(
					'$lte' => new MongoDate($current_date - DAY)
					)
				);
			$this->Session->write($this->name . '_where', $arr_where);
			echo URL . '/' . $this->params->params['controller'] . '/lists';
		}
		else if($keys == 'shipped_but_not_fully_invoiced')
		{
			$arr_where['invoice_not_full'] = array('values' => true, 'operator' => '=');
			$this->Session->write($this->name . '_where', $arr_where);
			echo URL . '/' . $this->params->params['controller'] . '/lists';
		}
		else if ($keys == 'existing')
			echo URL . '/' . $this->params->params['controller'] . '/entry';

		else if ($keys == 'print_sales_order')
			echo URL . '/' . $this->params->params['controller'] . '/view_pdf';
		else if ($keys == 'report_by_customer_summary')
			echo URL . '/' . $this->params->params['controller'] . '/option_summary_customer_find';
		else if ($keys == 'report_by_customer_detailed')
			echo URL . '/' . $this->params->params['controller'] . '/option_detailed_customer_find';
		else if ($keys == 'report_by_area_summary')
			echo URL . '/' . $this->params->params['controller'] . '/option_summary_customer_find/area';
		else if ($keys == 'report_by_area_detailed')
			echo URL . '/' . $this->params->params['controller'] . '/option_detailed_customer_find/area';
		else if ($keys == 'report_by_product_summary')
			echo URL . '/' . $this->params->params['controller'] . '/option_summary_product_find';
		else if ($keys == 'report_by_product_detailed')
			echo URL . '/' . $this->params->params['controller'] . '/option_detailed_product_find';
		else if ($keys == 'report_by_category_detailed')
            echo URL . '/' . $this->params->params['controller'] . '/option_detailed_product_find/category';
        else if ($keys == 'report_by_category_summary')
            echo URL . '/' . $this->params->params['controller'] . '/option_summary_product_find/category';
		else if ($keys == 'print_sales_orders')
			echo URL . '/' . $this->params->params['controller'] . '/view_minilist';
		else if ($keys == 'email_sales_order')
            echo URL . '/' . $this->params->params['controller'] . '/create_email_pdf/0/group';
		else if ($keys == 'create_email')
            echo URL . '/' . $this->params->params['controller'] . '/add_from_module/'.$this->get_id().'/Email';
        else if ($keys == 'create_letter')
            echo URL . '/' . $this->params->params['controller'] . '/add_from_module/'.$this->get_id().'/Letter';
        else if ($keys == 'create_fax')
            echo URL . '/' . $this->params->params['controller'] . '/add_from_module/'.$this->get_id().'/Fax';
        else if ($keys == 'summary_commission_report_by_name_found_set_of_records')
            echo URL . '/' . $this->params->params['controller'] . '/commission_summary/';
        else if ($keys == 'detailed_commission_report_by_name_found_set_of_records')
            echo URL . '/' . $this->params->params['controller'] . '/commission_detailed/';
        else if ($keys == 'print_late_salesorders')
            echo URL . '/' . $this->params->params['controller'] . '/print_late_salesorders/';
        else if ($keys == 'print_mini_list')
            echo URL . '/' . $this->params->params['controller'] . '/view_minilist/';
        else if ($keys == 'online_wholesale_order')
            echo URL . '/' . $this->params->params['controller'] . '/online_wholesale_order_report/';
		else
			echo '';
		die;
	}
	function create_full_shipping(){
		if(!$this->check_permission('shippings_@_entry_@_add')){
			echo 'You do not have permission on this action.';
			die;
		}
		$arr_shipping=array();
		$ids = $this->get_id();
		$this->selectModel('Shipping');
		$arr_salesorder=$this->opm->select_one(array('_id'=>new MongoId($ids)));

		$arr_shipping = $this->Shipping->select_all(array(
			'arr_where' => array('salesorder_id' => new MongoId($ids)),
			'arr_order' => array('_id' => -1),
		));
		$this->selectModel('Company');
		$arr_company=array();
		if(is_object($arr_salesorder['company_id']))
			$arr_company=$this->Company->select_one(array('_id'=>new MongoId($arr_salesorder['company_id'])));
		if(isset($arr_company['account'])&&is_array($arr_company['account'])){
			if(isset($arr_company['account']['credit_limit'])&&isset($arr_salesorder['sum_amount'])&&$arr_company['account']['credit_limit']!=0){
				if($arr_salesorder['sum_amount']>$arr_company['account']['credit_limit']){
					echo 'over';die;
				}
			}
		}
		if(!is_object($arr_salesorder['company_id'])){
			echo 'no_company';die;
		}
		$v_have_product=0;
		if(is_array($arr_salesorder)){
			if(is_array($arr_salesorder['products'])){
				foreach($arr_salesorder['products'] as $key1=>$value1){
					if(isset($value1['quantity'])&&!$value1['deleted'])
						$v_have_product+=(int)$value1['quantity'];
				}
			}
			if($v_have_product==0)
			{
				echo 'no_product';die;
			}
		}
		if(is_object($arr_shipping)){
			$v_have_shipping=0;
			foreach($arr_shipping as $key=>$value){
				if(is_array($value['products'])){
					foreach($value['products'] as $key1=>$value1){
						if(isset($value1['shipped'])&&!$value1['deleted'])
							$v_have_shipping+=(int)$value1['shipped'];
					}
				}
			}
			if($v_have_shipping!=0){
				echo 'full_shipping';die;
			}
			else{
				echo URL . '/shippings/create_shipping_from_salesorder/'.$this->get_id ();
				die;
			}
		}
		die;
	}

	function create_full_salesinvoice($saleorder_id=''){
		if(!$this->check_permission('salesinvoices_@_entry_@_add')){
			echo 'You do not have permission on this action.';
			die;
		}
		$arr_salesinvoice=array();
		$ids = $this->get_id();
		if($saleorder_id != '')
		{
			$ids = $saleorder_id;
		}
		$this->selectModel('Salesinvoice');
		$arr_salesorder=$this->opm->select_one(array('_id'=>new MongoId($ids)));
		if(!is_object($arr_salesorder['company_id'])){
			echo 'no_company';die;
		}
		$v_have_product=0;
		if(isset($arr_salesorder['products'])&&is_array($arr_salesorder['products']))
			foreach($arr_salesorder['products'] as $products){
				if(!$products['deleted']){
					$v_have_product=1;
					break;
				}
			}
		if($v_have_product==0){
			echo 'no_product';die;
		}
		if(isset($arr_salesorder['salesinvoice_id'])&&is_object($arr_salesorder['salesinvoice_id'])){
			$salesinvoice = $this->Salesinvoice->select_one(array('_id'=>$arr_salesorder['salesinvoice_id']),array('invoice_status'));
			if(isset($salesinvoice['invoice_status']) && $salesinvoice['invoice_status'] != 'Cancelled'){
				echo 'full_invoiced';die;
			} else {
				echo URL . '/salesinvoices/create_salesinvoice_from_salesorder/'.$ids;
				die;
			}
		}
		else{
			echo URL . '/salesinvoices/create_salesinvoice_from_salesorder/'.$ids;
			die;
		}
	}
	function append_salesinvoice(){
		if(!$this->check_permission('salesinvoices_@_entry_@_edit')){
			echo 'You do not have permission on this action.';
			die;
		}
		if(!isset($_POST['ids']) || strlen(trim($_POST['ids']))!=24)
			die;
		$salesinvoice_id = $_POST['ids'];
		$arr_salesinvoice=array();
		$ids = $this->get_id();
		$this->selectModel('Salesinvoice');
		$arr_salesorder=$this->opm->select_one(array('_id'=>new MongoId($ids)));
		if(!is_object($arr_salesorder['company_id'])){
			echo 'no_company';die;
		}
		$v_have_product=0;
		if(isset($arr_salesorder['products'])&&is_array($arr_salesorder['products']))
			foreach($arr_salesorder['products'] as $products){
				if(!$products['deleted']){
					$v_have_product=1;
					break;
				}
			}
		if($v_have_product==0){
			echo 'no_product';die;
		}
		if($this->append_build_salesorder($salesinvoice_id,$arr_salesorder))
			echo URL.'/salesinvoices/entry/'.$salesinvoice_id;
		die;
	}
	function replace_salesinvoice(){
		if(!$this->check_permission('salesinvoices_@_entry_@_add')){
			echo 'You do not have permission on this action.';
			die;
		}
		$arr_salesinvoice=array();
		$ids = $this->get_id();
		$this->selectModel('Salesinvoice');
		$arr_salesorder=$this->opm->select_one(array('_id'=>new MongoId($ids)));
		if(!is_object($arr_salesorder['company_id'])){
			echo 'This function cannot be performed as there is no company or contact linked to this record.';die;
		}
		$v_have_product=0;
		if(isset($arr_salesorder['products'])&&is_array($arr_salesorder['products']))
			foreach($arr_salesorder['products'] as $products){
				if(!$products['deleted']){
					$v_have_product=1;
					break;
				}
			}
		if($v_have_product==0){
			echo 'No items have been entered on this transaction yet.';die;
		}
		if(!isset($arr_salesorder['salesinvoice_id']) || !is_object($arr_salesorder['salesinvoice_id'])){
			echo 'This cannot be performed as there is no sales invoice linked to this record.';die;
		} else {
			$this->selectModel('Salesinvoice');
			$salesinvoice = $this->Salesinvoice->select_one(array('_id'=>new MongoId($arr_salesorder['salesinvoice_id'])),array('_id'));
			$arr_save = $this->set_salesinvoice_before_save($arr_salesorder);
			if(!empty($salesinvoice)){
				unset($arr_save['code'],$arr_save['invoice_date'],$arr_save['invoice_status'],$arr_save['invoice_type']);
				$salesinvoice_id = $arr_save['_id'] = $salesinvoice['_id'];
				if(isset($salesinvoice['sum_amount']) && $salesinvoice['sum_amount'] > 0){
					$this->selectModel('Salesaccount');
					if(isset($salesinvoice['company_id']) && is_object($salesinvoice['company_id'])){
						$this->Salesaccount->update_account($salesinvoice['company_id'], array(
															'model' => 'Company',
															'balance' => -$salesinvoice['sum_amount'],
															'invoices_credits' => -$salesinvoice['sum_amount'],
															));
					}elseif(isset($salesinvoice['contact_id'])){
						$this->Salesaccount->update_account($salesinvoice['contact_id'], array(
															'model' => 'Contact',
															'balance' => -$salesinvoice['sum_amount'],
															'invoices_credits' => -$salesinvoice['sum_amount'],
															));
					}
				}
				$salesinvoice = array_merge($salesinvoice,$arr_save);
				$this->Salesinvoice->save($salesinvoice);
			} else {
				$salesinvoice = $arr_save;
				$this->Salesinvoice->save($salesinvoice);
				$salesinvoice_id = $this->Salesinvoice->mongo_id_after_save;
	            $arr_salesorder['salesinvoice_id'] = new MongoId($id);
	            $arr_salesorder['salesinvoice_code'] = $arr_save['code'];
			}
			if(isset($arr_salesorder['sum_amount']) && $arr_salesorder['sum_amount'] > 0){
				$this->selectModel('Salesaccount');
				if(isset($arr_salesorder['company_id']) && is_object($arr_salesorder['company_id'])){
					$this->Salesaccount->update_account($arr_salesorder['company_id'], array(
														'model' => 'Company',
														'balance' => $arr_salesorder['sum_amount'],
														'invoices_credits' => $arr_salesorder['sum_amount'],
														));
				}elseif(isset($arr_salesorder['contact_id'])){
					$this->Salesaccount->update_account($arr_salesorder['contact_id'], array(
														'model' => 'Contact',
														'balance' => $arr_salesorder['sum_amount'],
														'invoices_credits' => $arr_salesorder['sum_amount'],
														));
				}
			}
			$this->redirect(URL.'/salesinvoices/entry/'.$salesinvoice_id);
		}
	}
	public function set_salesinvoice_before_save($arr_salesorder){
		if(!$this->check_permission('salesinvoices_@_entry_@_add'))
			$this->error_auth();
		$this->selectModel('Salesorder');
		$this->selectModel('Salesinvoice');
        $si = $arr_salesorder;
        $si['code'] = $this->opm->get_auto_code('code');
        $si['invoice_date'] = new MongoDate();
        $si['payment_due_date'] =  new MongoDate($si['invoice_date']->sec + (isset($si['payment_terms']) ? (int)$si['payment_terms'] : 0)*DAY);
        $si['invoice_status'] = 'In Progress';
        $si['invoice_type'] = 'Invoice';
        $si['salesorder_id'] = new MongoId($arr_salesorder['_id']);
        $si['salesorders'][0] = $si['salesorder_id'];
        $si['salesorder_number'] = $arr_salesorder['code'];
        $si['salesorder_name'] = $arr_salesorder['name'];
        unset($si['_id']);
        unset($si['created_by']);
        unset($si['date_modified']);
        unset($si['modified_by']);
        unset($si['salesorder_date']);
        unset($si['salesorder_status']);
        unset($si['salesorder_type']);
        return $si;
    }
	public function set_cal_price() {
		$this->cal_price = new cal_price; //Option cal_price
		//set arr_price_break default
		$this->cal_price->arr_price_break = array();
		//set arr_product default
		$this->cal_price->arr_product = array();
		//set arr_product item default
		$this->cal_price->arr_product_items = array();
	}

	//Sử dụng thư viện cal_price để tính
	 public function ajax_cal_line($arr_data = array()) {
        $arr_ret = $arr_product_items = array();
        if(!isset($arr_data['arr'])&&isset($_POST['arr'])){
            $arr_data['arr'] = $_POST['arr'];
        }
        if(!isset($arr_data['field'])&&isset($_POST['field']))
            $arr_data['field'] = $_POST['field'];
        if (isset($arr_data['arr'])) {
            $getdata = $arr_data['arr'];
            $getdata = (array) $getdata;
            if(isset($getdata['custom_unit_price'])){
                $getdata['custom_unit_price'] = (float)$getdata['custom_unit_price'];
            }
            //truong hop co id

            if (isset($getdata['id'])) {
                $get_id = $getdata['id'];
                $query  = $this->opm->select_one(array('_id' => new MongoId($this->get_id())));
                if(isset($getdata['custom_unit_price'])){
                    $getdata['custom_unit_price'] = (float)$getdata['custom_unit_price'];
                    if($query['products'][$get_id]['unit_price'] > $getdata['custom_unit_price']
                       &&!$this->check_permission($this->name.'_@_custom_unit_price_@_add')){
                        echo 'You do not have permission to change this value.';
                        die;
                    }
                }
                if(isset($arr_data['company_id'])&&is_object($arr_data['company_id']))
                        $query['company_id'] = $arr_data['company_id'];
                if (isset($query['products']))
                    $arr_pro = $arr_insert['products'] = (array) $query['products'];
                if (is_array($arr_pro) && count($arr_pro) > 0 && isset($arr_pro[$get_id]) && !$arr_pro[$get_id]['deleted']) {
                    $arr_pro = array_merge((array) $arr_pro[$get_id], (array) $getdata);

					if(isset($query['tax']) && $query['tax']!='')
						$arr_pro['tax'] = $this->get_tax($query['tax']);
					//tim va luu them cac thay doi phu thuoc

					if(isset($arr_data['field']))
						$fieldchage = $arr_data['field'];
					else
						$fieldchage = '';
					if($fieldchage=='sell_price' || $fieldchage=='custom')
						$arr_pro['plus_unit_price'] = 0;
					if(!isset($query['company_id']))
						$query['company_id'] = '';
                    $is_special = false;

					//tinh lai plus sell price neu thay doi lien quan den gia
                    $total_sub_total = 0;
                    $product_data = $query['products'][$get_id];
					if($fieldchage!='sell_price'){
                        $parent_no = $get_id;
                        $parent_id = $query['products'][$parent_no]['products_id'];
                        if($fieldchage=='options'&&isset($getdata['data'])){
                            $options_change = true;
                            $parent_no = $getdata['data']['parent_line_no'];
                            if(isset($getdata['data']['line_no']))
                                $this_line_no = $getdata['data']['line_no'];
                            $parent_id = $query['products'][$parent_no]['products_id'];
                            $get_id = $parent_no;
                            $arr_pro = $query['products'][$parent_no];
                        }
						$arr_pro['plus_sell_price'] = 0;
						if(!isset($arr_pro['sell_price']))
							$arr_pro['sell_price'] = 0;
                        if(strpos($fieldchage, 'size')!==false){
                            $size_tmp = $fieldchage;
                        }

						//tinh lai gia option
						$option = $this->option_list_data($parent_id,$parent_no);
						foreach($option['option'] as $value){
							if(isset($value['choice'])&&$value['choice']==1){
                                if(isset($value['same_parent'])&&$value['same_parent']==1){
                                    $is_special = true;
									 if(isset($value['is_custom']) && $value['is_custom']==1)
									 	$value['sell_price'] = $value['unit_price'];
                                    $value['sizew'] = $arr_pro['sizew'];
                                    $value['sizew_unit'] = $arr_pro['sizew_unit'];
                                    $value['sizeh'] = $arr_pro['sizeh'];
                                    $value['sizeh_unit'] = $arr_pro['sizeh_unit'];
                                    $value['quantity'] = (isset($value['quantity']) ? (float)$value['quantity'] : 1);
                                    if(isset($size_tmp))
                                        $value[$size_tmp] = $getdata[$size_tmp];
                                    $value['plus_sell_price'] = 0;
                                    $cal_price = new cal_price;


                                    $cal_price->arr_product_items = $value;
									$cal_price->price_break_from_to = $this->change_sell_price_company($query['company_id'],$value['product_id']);
									if(isset($value['is_custom']) && $value['is_custom']==1){
										$cal_price->field_change = 'sell_price';
									}else{
										$cal_price->field_change = $arr_data['field'];
										$cal_price->arr_product_items['quantity'] *= $arr_pro['quantity'];
										$cal_price->cal_price_items();
										$value['sell_price'] = $cal_price->arr_product_items['sell_price'];
										$cal_price->arr_product_items = $value;
										$cal_price->field_change = 'sell_price';
									}

                                    $value = $cal_price->cal_price_items();
                                    $arr_pro['plus_sell_price'] += $value['sub_total'];
                                    $total_sub_total += (isset($value['sub_total']) ? (float)$value['sub_total'] : 0);
                                    $fieldchage = '';
								//neu khong phai la S.P thi xet loai combination
                                } else if($product_data['sell_by']=='combination'
                                          && (!isset($value['same_parent']) || $value['same_parent'] == 0)
										  && isset($value['choice'])&&$value['choice']==1){
                                    $total_sub_total += (isset($value['sub_total']) ? (float)$value['sub_total'] : 0);
                                }
                            }
						}

						//dau check cuoi cung cua
						if(!$is_special
							&&isset($getdata['data']['same_parent'])
							&&$getdata['data']['same_parent']==0){
							$is_special = true;
						}


					}
					$cal_price = new cal_price;
					$cal_price->arr_product_items = $arr_pro;
					$result = array();
                    //Kiem tra neu nhu product cha la custom(ko co id) thi line nay ko can phai tinh bang pricebreak
                    if(isset($arr_pro['option_for'])&&$arr_pro['option_for']!=''){
                        $option_for = $arr_pro['option_for'];
                        if(!isset($arr_pro['same_parent']) || $arr_pro['same_parent']==0){
                            if(isset($query['products'][$option_for])&&!is_object($query['products'][$option_for]['products_id'])){
                                $is_custom = true;
                            }
                        }
                    }
					if(isset($arr_pro['products_id'])&&!isset($is_custom)){
                    	$result = $this->change_sell_price_company($query['company_id'],$arr_pro['products_id']);
					}

					//truyen bang chiec khau va gia giam vao
					$cal_price->price_break_from_to = $result;
					$cal_price->field_change = $fieldchage;

					//chay tinh gia, neu la combination
                    if($product_data['sell_by']=='combination'){
                        $arr_ret = $cal_price->combination_cal_price();
                        $arr_ret['unit_price'] += $total_sub_total;

                        $arr_ret['sell_price'] = $arr_ret['unit_price'];
                        if($total_sub_total>0){
                            $arr_ret['sub_total'] = round((float)$arr_ret['unit_price']*(float)$arr_ret['quantity'],2);
                            $arr_ret['tax'] = round(((float)(isset($arr_ret['taxper']) ? $arr_ret['taxper'] : 0)/100)*(float)$arr_ret['sub_total'],3);
                            $arr_ret['amount'] = round((float)$arr_ret['sub_total']+(float)$arr_ret['tax'],2);
                        }


					//nếu như có sp thì là loại special, thì tính diện tích rồi nhân vào line chính mới cộng cho plus_sell_price không thì ngược lại
					}else{
					   $arr_ret = $cal_price->cal_price_items($is_special);
                       //Kiem tra neu nhu product cha la custom(ko co id) update thong tin thay doi qua option
                       if(isset($is_custom)){
                            $orginal_query = $query;
                            if(isset($query['options'])&&!empty($query['options'])){
                                foreach($query['options'] as $option_key=>$option_value){
                                    if(isset($option_value['deleted'])&&$option_value['deleted']) continue;
                                    if(!isset($option_value['parent_line_no']) || $option_value['parent_line_no']!=$option_for) continue;
                                    if(!isset($option_value['product_id']) || (string)$option_value['product_id']!=(string)$arr_ret['products_id']) continue;
                                    $query['options'][$option_key]['unit_price'] = $arr_ret['sell_price'];
                                    $query['options'][$option_key]['quantity'] = $arr_ret['quantity'];
                                    $query['options'][$option_key]['sub_total'] = $arr_ret['sub_total'];
                                    break;
                                }
                            }
                            if($query['options']!=$orginal_query['options']){
                                $this->opm->save($query);
                            }
                        }
                       if(isset($arr_ret['option_for']) && isset($arr_ret['same_parent']) && $arr_ret['same_parent']==1){
                            $ids = $arr_ret['option_for'];
                            if(is_array($query['products'][$ids])&&!$query['products'][$ids]['deleted']){
                                $sub_total = (float)$query['products'][$get_id]['sub_total'];
                                $new_sub_total = (float)$arr_ret['sub_total'];
                                $query['products'][$ids]['sell_price'] = $query['products'][$ids]['unit_price'] = (float)$query['products'][$ids]['unit_price'] - $sub_total + $new_sub_total;
                                $query['products'][$ids]['sub_total'] = round((float)$query['products'][$ids]['unit_price']*(float)$query['products'][$ids]['quantity'],2);
                                $query['products'][$ids]['tax'] = round(((float)(isset($query['products'][$ids]['taxper']) ? $query['products'][$ids]['taxper'] : 0)/100)*(float)$query['products'][$ids]['sub_total'],3);
                                $query['products'][$ids]['amount'] = round((float)$query['products'][$ids]['sub_total']+(float)$query['products'][$ids]['tax'],2);
                                $arr_insert['products'][$ids] = $query['products'][$ids];
                                $query['products'][$ids]['ids'] = $ids;
                            }
                       }

                    }
					//Save all data
                    $arr_insert['products'][$get_id] = array_merge((array) $arr_pro, (array) $arr_ret);
                     //custom unit price
                    if(isset($arr_insert['products'][$get_id]['custom_unit_price'])){
                        $product = $arr_insert['products'][$get_id];
                        $is_reverse_update = false;
                        if(!is_object($product['products_id']))
                            $is_reverse_update = true;
                        $is_admin_edit = false;
                        if($this->check_permission($this->name.'_@_custom_unit_price_@_add'))
                            $is_admin_edit = true;
                        if((float)$product['custom_unit_price']<(float)$product['unit_price']){
                            if($arr_data['field']=='custom_unit_price'
                               &&!$this->check_permission($this->name.'_@_custom_unit_price_@_add'))
                               $product['custom_unit_price'] = $product['unit_price'];
                            else if($arr_data['field']!='custom_unit_price')
                                $product['custom_unit_price'] = $product['unit_price'];
                        }
                        if($is_reverse_update)
                            $product['sell_price'] = $product['unit_price'] = $product['custom_unit_price'];
                        $unit_price = $product['custom_unit_price'];
                        $product['sub_total'] = round((float)$unit_price*(float)$product['quantity'],2);
                        $product['tax'] = round(((float)(isset($product['taxper']) ? $product['taxper'] : 0)/100)*(float)$product['sub_total'],3);
                        $product['amount'] = round((float)$product['sub_total']+(float)$product['tax'],2);
                        $arr_insert['products'][$get_id] = $product;
                        $arr_ret = $product;
                    }
                    //end custom
					$arr_insert = $this->arr_associated_data('products',$arr_insert['products'],$get_id,$fieldchage);
					$arr_insert['_id'] = new MongoId($this->get_id());
                    $this->opm->save($arr_insert);
					//update sum
                    $keyfield = array(
                        "sub_total" 	=> "sub_total",
                        "tax" 			=> "tax",
                        "amount" 		=> "amount",
                        "sum_sub_total" => "sum_sub_total",
                        "sum_tax" 		=> "sum_tax",
                        "sum_amount" 	=> "sum_amount"
                    );
                    $arr_sum = $this->update_sum('products', $keyfield);
                    $arr_ret = array_merge((array) $arr_ret, (array) $arr_sum);
                    if(isset($ids)){
                        $arr_ret = array('self'=>$arr_ret,'parent'=>$query['products'][$ids]);
                    }
                    else if(isset($options_change)&&isset($this_line_no)&&$this_line_no!=''){
                        foreach($query['options'] as $option){
                            if($option['deleted']) continue;
                            if(!isset($option['line_no']) || $option['line_no']!=$this_line_no) continue;
                            $query['products'][$this_line_no] = array_merge($query['products'][$this_line_no],$option);
                            break;
                        }
                        $arr_ret = array('parent'=>$arr_ret,'self'=>$query['products'][$this_line_no]);
                    }
                    //Return data for display
                    if(!isset($arr_data['company_id']))
                        echo json_encode($arr_ret);
                }

                //truong hop khong chon id nao
            } else {
                if(!isset($arr_data['company_id']))
                    echo '';
            }
        }
        if(!isset($arr_data['company_id']))
            die;
    }
	var $is_text = 0;
	public function line_entry() {
		$is_text = $this->is_text;
		$subdatas = $arr_ret = array();
		$codeauto = 0;
		$opname = 'products';
		$sum_sub_total = $sum_tax = 0;
		$subdatas[$opname] = array();
		$ids = $this->get_id();
		if ($ids != '') {
			//get entry data
            $order = $this->opm->select_one(array('_id'=>new MongoId($ids)),array('date_modified', 'status'));
            $prefix_cache_name = 'line_salesorder_'.$ids.'_';
            $cache_name = $prefix_cache_name.$order['date_modified']->sec;
            $arr_ret = Cache::read($cache_name);
            if(!$arr_ret){
                $arr_ret = $this->line_entry_data($opname, $is_text);
                Cache::write($cache_name,$arr_ret);
                $old_cache = $this->get_cache_keys_diff($cache_name,$prefix_cache_name);
                foreach($old_cache as $cache){
                    Cache::delete($cache);
                }
            }            
           	if(isset($arr_ret[$opname])){
           		$minimum = $this->get_minimum_order();
                if($arr_ret['sum_sub_total']<$minimum){
	                $arr_ret = $this->get_minimum_order_adjustment($arr_ret,$minimum);
                }
                $subdatas[$opname] = $arr_ret[$opname];
            }
            if(isset($order['status']) && $order['status'] == 'Cancelled' )
            	$arr_ret['sum_sub_total'] = $arr_ret['sum_tax'] = $arr_ret['sum_amount'] = 0;
        }
		$this->set('subdatas', $subdatas);
		$codeauto = $this->opm->get_auto_code('code');
		$this->set('nextcode', $codeauto);
		$this->set('file_name', 'salesorder_' . $ids);
		$this->set('sum_sub_total', $arr_ret['sum_sub_total']);
		$this->set('sum_amount', $arr_ret['sum_amount']);
		$this->set('sum_tax', $arr_ret['sum_tax']);
		$link_add_atction['option'] = 'option_list';
		$this->set('link_add_atction', $link_add_atction);
		$this->set_select_data_list('relationship', 'line_entry');
		$this->set('icon_link_id', $ids);
		$this->set('mongo_id', $ids);
		$this->selectModel('Stuffs');
		if( $this->Stuffs->count(array('value' => 'Default Prefer Customer Search', 'default_search' => true)) ) {
            $this->set('default_prefer_customer', true);
        }
	}

	//check and cal for Line Entry
	public function line_entry_data($opname = 'products', $is_text = 0,$mod = '', $ids = '') {
        $arr_ret = array(); $option_for = '';
        $this->selectModel('Setting');
        if($ids == '')
        	$ids = (string)$this->get_id();
        if ($ids != '') {
            $newdata = $option_select_dynamic = array();
            $query = $this->opm->select_one(array('_id' => new MongoId($ids)));
            if(!isset($query['options']))
                $query['options'] = array();
            //set sum
            $arr_ret['sum_sub_total'] = $arr_ret['sum_amount'] = $arr_ret['sum_tax'] = '0.00';
            if (isset($query['sum_sub_total']) && $query['sum_sub_total'] != '')
                $arr_ret['sum_sub_total'] = $query['sum_sub_total'];
            if (isset($query['sum_amount']) && $query['sum_amount'] != '')
                $arr_ret['sum_amount'] = $query['sum_amount'];
            if (isset($query['sum_tax']) && $query['sum_tax'] != '')
                $arr_ret['sum_tax'] = $query['sum_tax'];
            $this->selectModel('Product');
            $arr_product_special = Cache::read('arr_product_special');
            if(!$arr_product_special){
                $arr_product_special = $this->Product->select_all(array(
                                           'arr_where'=>array('special_order'=>1),
                                           'arr_field'=>array('_id')
                                           ));
                $arr_product_special = iterator_to_array($arr_product_special);
                Cache::write('arr_product_special',$arr_product_special);
            }
            $arr_product_approved = Cache::read('arr_product_approved');
            if(!$arr_product_approved){
                $arr_product_approved = $this->Product->select_all(array(
                                           'arr_where'=>array('approved'=>1),
                                           'arr_field'=>array('_id')
                                           ));
                $arr_product_approved = iterator_to_array($arr_product_approved);
                Cache::write('arr_product_approved',$arr_product_approved);
            }
            /*$arr_product_rfq = Cache::read('arr_product_rfq');
            if(!$arr_product_rfq){
                $arr_product_rfq = $this->Product->select_all(array(
                                           'arr_where'=>array('is_rfq'=>1),
                                           'arr_field'=>array('_id')
                                           ));
                $arr_product_rfq = iterator_to_array($arr_product_rfq);
                Cache::write('arr_product_rfq',$arr_product_rfq);
            }*/
            $option_for_sort = array();
            if (isset($query[$opname]) && is_array($query[$opname])) {
            	$options = array();
                if(isset($query['options']) && !empty($query['options']) )
                    $options = $query['options'];
                foreach ($query[$opname] as $key => $arr) {
                	if(!isset($arr['deleted']) || $arr['deleted']) continue;
                    if (!$arr['deleted']) {
                        $newdata[$key] = $arr;
                        if( isset($arr['bleed']) && $arr['bleed'] ) {
                            $newdata[$key]['xclass'] = 'bleed';
                        }
                        if(!isset($arr['option_for'])){
                        	$origin_options = array();
                            if (isset($arr['origin_options'])) {
                                $origin_options = $arr['origin_options'];
                            }
                            $difference_option = false;
                            $option_qty = 0;
	                        $option = $this->new_option_data(array('key'=>$key,'products_id'=>$arr['products_id'],'options'=>$query['options'],'date'=>$query['_id']->getTimeStamp()),$query['products']);
	                        //Khoa sell_by,oum neu nhu line nay co option
	                        //Khoa tiep sell_price neu line nay co option same_parent
	                        if(isset($option['option'])&&!empty($option['option'])){
	                            foreach($option['option'] as $value){
	                                if($value['deleted']) continue;
                                    if(isset($value['choice'])&&$value['choice']==0&&isset($value['require']) && $value['require']!=1) continue;
	                                if(isset($value['oum']) && $value['oum']!=$arr['oum'])
	                                    $newdata[$key]['oum'] = 'Mixed';
	                                $newdata[$key]['xlock']['sell_by'] = '1';
	                                $newdata[$key]['xlock']['oum'] = '1';
	                                if(IS_LOCAL && $newdata[$key]['oum'] = 'Mixed'){
	                                	unset($newdata[$key]['xlock']['sell_by'],$newdata[$key]['xlock']['oum']);
	                                }
                                    $option_qty++;
	                                if(!isset($value['same_parent']) || $value['same_parent']==0) continue;
	                                $newdata[$key]['xlock']['sell_price'] = '1';
	                                if (!$difference_option && array_search($value['product_id'], array_column($origin_options, '_id')) === false ) {
	                        			$difference_option = true;
	                                }
	                            }
	                        }
	                        if ($option_qty != count($origin_options)) {
	                        	$difference_option = true;
                            }
                            if ($difference_option) {
                                $newdata[$key]['xstyle_element']['details']= 'background-color: red !important;width: 69%;margin-left: 5px;';
                            }
	                        if(isset($arr['products_id']) ) {
                                if(isset($arr_product_special[(string)$arr['products_id']]))
                                    $newdata[$key]['xstyle_element']['sku']= 'color: blue !important;';
                                else if(!isset($arr_product_approved[(string)$arr['products_id']]))
                                    $newdata[$key]['xcss_element']['sku']= 'approved_product';
                                /*if(isset($arr_product_rfq[(string)$arr['products_id']]))
                                    $newdata[$key]['xstyle_element']['receipts']= 'background-color: #852020 !important;';*/
                            }
                            if(!isset($arr['company_price_break']) || !$arr['company_price_break'])
                                $newdata[$key]['xhidden']['vip'] = '1';
						} else {
                            $newdata[$key]['xlock']['sell_by'] = '1';
                            $newdata[$key]['xlock']['oum'] = '1';
                            $newdata[$key]['xempty']['vip'] = '1';
                        }
                        $newdata[$key]['option'] = 1;
                        $get_name_only = false;
                        $newdata[$key]['option_group'] = '';
                        if (isset($newdata[$key]['products_name'])) {
                            if($is_text != 1){
                                $arrtmp = explode("\n", $newdata[$key]['products_name']);
                                $newdata[$key]['products_name'] = $arrtmp[0];
                                if(isset($arr['same_parent']) && $arr['same_parent']==1)
                                    $get_name_only = true;
                            }
                            if(!empty($option)){
                                foreach($options as $k=>$val){
                                    if(isset($val['deleted']) && $val['deleted']) continue;
                                    if(!isset($val['line_no']) || $val['line_no']!=$key) continue;
                                    $newdata[$key]['option_group'] = (isset($val['option_group']) ? $val['option_group'] : '');
                                    if($get_name_only){
                                        if(!isset($val['quantity'])) continue;
                                        if($val['quantity']==1) continue;
                                        $newdata[$key]['products_name'] .= ' ('.$val['quantity'].')';
                                        unset($options[$k]);
                                    }
                                }
                            }
                        }
                       	if( /*(isset($newdata[$key]['option_group'])
                            && (strpos(strtolower($newdata[$key]['option_group']), 'cutting') !== false
                                 ||strpos(strtolower($newdata[$key]['option_group']), 'packing') !== false
                                 ||strpos(strtolower($newdata[$key]['option_group']), 'cut') !== false) )
                            || */(isset($this->export_pdf)&&isset($arr['hidden'])&&$arr['hidden']) ){
                            if(isset($this->export_pdf) && (!isset($arr['same_parent']) || !$arr['same_parent']) && isset($arr['option_for'])){
                                $newdata[$arr['option_for']]['sub_total'] = str_replace(',', '', $newdata[$arr['option_for']]['sub_total']);
                                $newdata[$arr['option_for']]['sub_total'] += $arr['sub_total'];
                                $newdata[$arr['option_for']]['tax'] = str_replace(',', '', $newdata[$arr['option_for']]['tax']);
                                $newdata[$arr['option_for']]['tax'] += $arr['tax'];
                                $newdata[$arr['option_for']]['amount'] = str_replace(',', '', $newdata[$arr['option_for']]['amount']);
                                $newdata[$arr['option_for']]['amount'] += $arr['amount'];
                                $newdata[$arr['option_for']]['custom_unit_price'] =  round($newdata[$arr['option_for']]['sub_total'] /  $newdata[$arr['option_for']]['quantity'],3);
                                $option_for_sort['parent'][$arr['option_for']] = array_merge($option_for_sort['parent'][$arr['option_for']],$newdata[$arr['option_for']]);
                            }
                            unset($newdata[$key]);
                            continue;
                        }
                        if(isset($newdata[$key]['products_name']) && $is_text == 1){
                        	$newdata[$key]['products_costing_name'] = '';
                        	if(isset($arr['details']))
                        		$newdata[$key]['products_costing_name'] .= htmlentities('<p style="margin-left:15px;font-style:italic;">'.nl2br($arr['details']).'</p>');
                            $newdata[$key]['xlock']['products_name']= '1';
							$newdata[$key]['xlock']['sell_by'] 	= '1';
							$newdata[$key]['xlock']['sell_price'] 	= '1';
							$newdata[$key]['xlock']['oum'] 		= '1';
							$newdata[$key]['xlock']['quantity'] 	= '1';
							$newdata[$key]['xlock']['sizew'] 		= '1';
							$newdata[$key]['xlock']['sizew_unit'] 	= '1';
							$newdata[$key]['xlock']['sizeh'] 		= '1';
							$newdata[$key]['xlock']['sizeh_unit'] 	= '1';
							$newdata[$key]['xlock']['unit_price'] 	= '1';
							$newdata[$key]['xlock']['adj_qty'] 	= '1';
							$newdata[$key]['xlock']['sub_total'] 	= '1';
							$newdata[$key]['xlock']['tax'] 		= '1';
							$newdata[$key]['xlock']['amount'] 		= '1';
							$newdata[$key]['xlock']['option'] 		= '1';
							// $newdata[$key]['xlock']['receipts'] 	= '1';
                        }
                        //set all price in display
                        if (isset($arr['area']))
                            $newdata[$key]['area'] = (float) $arr['area'];
                        $newdata[$key]['custom_unit_price'] = $this->opm->format_currency((isset($arr['custom_unit_price']) ? $arr['custom_unit_price'] : 0), 3);
                        if (isset($arr['unit_price'])){
                            $newdata[$key]['unit_price'] = $this->opm->format_currency( $arr['unit_price'], 3);
                            if(!isset($arr['custom_unit_price']))
                                $newdata[$key]['custom_unit_price'] = $newdata[$key]['unit_price'];
                        }
                        else
                            $newdata[$key]['unit_price'] = '0.000';
                        if (isset($arr['sub_total']))
                            $newdata[$key]['sub_total'] = $this->opm->format_currency( $arr['sub_total']);
                        else
                            $newdata[$key]['sub_total'] = '0.00';
                        if (isset($arr['tax']))
                            $newdata[$key]['tax'] = $this->opm->format_currency( $arr['tax'], 3);
                        else
                            $newdata[$key]['tax'] = '0.000';
                        if (isset($arr['amount']))
                            $newdata[$key]['amount'] = $this->opm->format_currency( $arr['amount']);
                        else
                            $newdata[$key]['amount'] = '0.00';
						unset($newdata[$key]['id']);
						$newdata[$key]['_id'] = $key;
						$newdata[$key]['sort_key'] = $this->opm->num_to_string($key).'-'.'0';

						$option_for = '';
						if(isset($arr['option_for']) && is_numeric($arr['option_for'])){
                            $newdata[$key]['xempty']['option']      = '1';
                            $newdata[$key]['xempty']['view_costing']      = '1';
                            if(isset($arr['same_parent'])&&$arr['same_parent']==1)
                                $newdata[$key]['xempty']['custom_unit_price']   = '1';
                            $newdata[$key]['_id'] = $key;
							$newdata[$key]['sku_disable'] = '1';
							$newdata[$key]['sku'] = '';
							$newdata[$key]['remove_deleted'] = '1';
							$newdata[$key]['icon']['products_name'] = (is_object($arr['products_id']) ? '/products/entry/'.$arr['products_id'] : '#');
							$newdata[$key]['sort_key'] = $this->opm->num_to_string($arr['option_for']).'-'.$key;
							if($mod!='options_list')
							     unset($newdata[$key]['products_id']);
						}

                        //data RFQ's
                        // $receipts = 0;
                        // if (isset($query['rfqs']) && is_array($query['rfqs']) && count($query['rfqs']) > 0) {
                        //     foreach ($query['rfqs'] as $rk => $rv) {
                        //         if (!$rv['deleted'] && isset($rv['rfq_code']) && (int) $rv['rfq_code'] == $key)
                        //             $receipts = 1;
                        //     }
                        //     $newdata[$key]['receipts'] = $receipts;
                        // } else
                        //     $newdata[$key]['receipts'] = 0;

                        //chặn không cho custom size nếu is_custom_size = 1
                        if(isset($arr['products_id']) && is_object($arr['products_id'])){
                            $product_custom_size = Cache::read('product_custom_size');
                            if($product_custom_size && isset($product_custom_size[(string)$arr['products_id']]))
                                $is_custom_size = $product_custom_size[(string)$arr['products_id']];
                            else{
                                $product = $this->Product->select_one(array('_id'=>new MongoId($arr['products_id'])),array('is_custom_size'));
                                $is_custom_size = isset($product['is_custom_size'])&&$product['is_custom_size'] == 1 ? 1 : 0;
                                if(!is_array($product_custom_size))
                                    $product_custom_size = array();
                                $product_custom_size[(string)$arr['products_id']] = $is_custom_size;
                                Cache::write('product_custom_size',$product_custom_size);
                            }
                            if($is_custom_size==1){
                                $newdata[$key]['xlock']['sizeh'] = '1';
                                $newdata[$key]['xlock']['sizew'] = '1';
                                $newdata[$key]['xlock']['sizeh_unit'] = '1';
                                $newdata[$key]['xlock']['sizew_unit'] = '1';
                                $newdata[$key]['xlock']['sell_by'] = '1';
                            }
                        }


						//empty neu same_parent = 1
						if(isset($arr['same_parent']) && $arr['same_parent']==1){
							$newdata[$key]['xempty']['sell_by'] 	= '1';
							$newdata[$key]['xempty']['sell_price'] 	= '1';
							$newdata[$key]['xempty']['oum'] 		= '1';
							$newdata[$key]['xempty']['quantity'] 	= '1';
							$newdata[$key]['xempty']['sizew'] 		= '1';
							$newdata[$key]['xempty']['sizew_unit'] 	= '1';
							$newdata[$key]['xempty']['sizeh'] 		= '1';
							$newdata[$key]['xempty']['sizeh_unit'] 	= '1';
							$newdata[$key]['xempty']['unit_price'] 	= '1';
							$newdata[$key]['xempty']['custom_unit_price'] = '1';
							$newdata[$key]['xempty']['adj_qty'] 	= '1';
							$newdata[$key]['xempty']['sub_total'] 	= '1';
							$newdata[$key]['xempty']['tax'] 		= '1';
							$newdata[$key]['xempty']['amount'] 		= '1';
							$newdata[$key]['xempty']['option'] 		= '1';
							// $newdata[$key]['xempty']['receipts'] 	= '1';
							$newdata[$key]['xempty']['docket_check'] 	= '1';
						}


						//empty neu sell_by parent = combination
						if($option_for!='' && isset($query[$opname][$option_for]['sell_by']) && $query[$opname][$option_for]['sell_by']=='combination'){
							//$newdata[$key]['xempty']['sub_total'] = '1';
							$newdata[$key]['xempty']['tax'] 		= '1';
							$newdata[$key]['xempty']['amount'] 		= '1';
						}

						//khoa Sold by neu la combination
						if (isset($newdata[$key]['sell_by']) && $newdata[$key]['sell_by']=='combination') {
							$newdata[$key]['xlock']['sell_by']= '1';
							$newdata[$key]['xlock']['sell_price']= '1';
							$newdata[$key]['xlock']['oum']= '1';
						}



                        //set lại select dựa vào loại sell_by
                        if (isset($newdata[$key]['sell_by'])) {
                            $option_select_dynamic['oum_' . $key] = $this->Setting->select_option_vl(array('setting_value' => 'product_oum_' . strtolower($arr['sell_by'])));
                        }
                        if(isset($arr['docket_repair'])&&!empty($arr['docket_repair'])){
                        	$docket_repair = end($arr['docket_repair']);
                        	if($docket_repair['quantity']>0)
                        		$newdata[$key]['repair_quantity'] = $docket_repair['quantity'];

                        }
                        if(!isset($arr['completed_docket']) || $arr['completed_docket'] == false)
                        	$newdata[$key]['docket_check'] = 0;
                        else
                        	$newdata[$key]['docket_check'] = 1;
                        if(isset($arr['option_for']))
                            $option_for_sort['option'][$arr['option_for']][$key] = $newdata[$key];
                        else
                            $option_for_sort['parent'][$key] = $newdata[$key];
                    } //end if
                }
            }
            $arr_ret[$opname] =array();
            $this->selectModel('Product');
            if(isset($option_for_sort['parent'])){
                foreach($option_for_sort['parent'] as $p_key=>$parent){
                    $arr_ret[$opname][] = $parent;
                    if(!isset($option_for_sort['option'][$p_key])) continue;
                    if(is_object($parent['products_id'])){
                        $p_product = $this->Product->select_one(array('_id'=> new MongoId($parent['products_id'])),array('options'));
                        if(isset($p_product['options'])&&!empty($p_product['options'])){
                            foreach($option_for_sort['option'][$p_key] as $k_opt=>$opt){
                                if(!isset($opt['proids'])) continue;
                                $opt_key = str_replace((string)$parent['products_id'].'_', '', $opt['proids']);
                                $option_for_sort['option'][$p_key][$k_opt]['option_group'] = (isset($p_product['options'][$opt_key]['option_group']) ? $p_product['options'][$opt_key]['option_group'] : '');
                                $option_for_sort['option'][$p_key][$k_opt]['option_group_for_sort'] = $option_for_sort['option'][$p_key][$k_opt]['option_group'].'_'.$option_for_sort['option'][$p_key][$k_opt]['products_name'];
                            }
                        }
                    }
                    if(isset($option_for_sort['option'])){
                        if(isset($query['options'])&&!empty($query['options'])){
                            foreach($option_for_sort['option'][$p_key] as $k_opt=>$opt){
                                $line_no = $opt['_id'];
                                foreach($query['options'] as  $custom_opt_k=>$custom_opt_v){
                                    if(isset($custom_opt_v['deleted'])&&$custom_opt_v['deleted']){
                                        unset($query['options'][$custom_opt_k]);
                                        continue;
                                    }
                                    if(!isset($custom_opt_v['line_no']) || $custom_opt_v['line_no']!=$line_no) continue;
                                    if(!isset($custom_opt_v['option_group']))
                                        $custom_opt_v['option_group'] = '';
                                    $option_for_sort['option'][$p_key][$k_opt]['option_group'] = $custom_opt_v['option_group'];
                                    $option_for_sort['option'][$p_key][$k_opt]['option_group_for_sort'] = $option_for_sort['option'][$p_key][$k_opt]['option_group'].'_'.$option_for_sort['option'][$p_key][$k_opt]['products_name'];
                                    unset($query['options'][$custom_opt_k]); break;
                                }
                            }
                        }
                        $option_for_sort['option'][$p_key] = $this->opm->aasort($option_for_sort['option'][$p_key],'option_group_for_sort');
                        foreach($option_for_sort['option'][$p_key] as $value)
                            array_push($arr_ret[$opname], $value);
                    }
                }
            }
        }
        $this->set('option_select_dynamic', $option_select_dynamic);
        return $arr_ret;
    }
	//subtab Text entry
	public function text_entry() {
		$this->is_text = 1;
		$this->line_entry();
	}
	 public function find_sub_line_entry($keysearch = 'proids',$keys='') {
		 $arr = array();
		 if ($this->get_id() != '') {
			$query = $this->opm->select_one(array('_id' => new MongoId($this->get_id())));
			if(isset($query['products']) && is_array($query['products']) && count($query['products'])>0){
				foreach($query['products'] as $kk=>$vv){
					if(isset($vv['deleted']) && $vv['deleted']==false && isset($vv[$keysearch]) && $vv[$keysearch]!=''){
						if($keys=='')
							$arr[] = $vv[$keysearch];
						else if($keys=='swap')
							$arr[$vv[$keysearch]] = $kk;
						else
							$arr[$kk] = $vv[$keysearch];
					}
				}
			}
		 }
		 return $arr;
	 }
	 public function find_sub_line_entry_for_line($option_for='',$keysearch = 'proids',$keys='') {
		 $arr = array();
		 if ($this->get_id() != '' && $option_for!='') {
			$query = $this->opm->select_one(array('_id' => new MongoId($this->get_id())));
			if(isset($query['products']) && is_array($query['products']) && count($query['products'])>0){
				foreach($query['products'] as $kk=>$vv){
					if(isset($vv['deleted']) && $vv['deleted']==false && isset($vv[$keysearch]) && $vv[$keysearch]!='' && $vv['option_for'] == $option_for){
						if($keys=='')
							$arr[] = $vv[$keysearch];
						else if($keys=='swap')
							$arr[$vv[$keysearch]] = $kk;
						else
							$arr[$kk] = $vv[$keysearch];
					}
				}
			}
		 }
		 return $arr;
	 }
	public function option_list_data($products_id='',$idsub=-1) {
		$data = $option_group = array(); $groupstr = '';

		if($idsub<0)
			return $data;

		if(is_object($products_id))
			$products_id = (string)$products_id;

		if($products_id!=''){
			$this->selectModel('Product');
			$products = $this->Product->options_data($products_id);
		}
		$custom_option = $this->salesorder_options_data($idsub);
		if(isset($products['productoptions']) && count($products['productoptions'])>0){
			$data = $products['productoptions'];
			foreach($data as $kk=>$vv){
				if(isset($custom_option[$kk])){
                    $option = $custom_option[$kk];
                    if(isset($option['quantity'])&&$option['quantity']!=$vv['quantity']
                       || isset($option['unit_price'])&&$option['unit_price']!=$vv['unit_price']
                       || isset($option['discount'])&&$option['discount']!=$vv['discount']){
					   $data[$kk] = array_merge($vv,array_merge($custom_option[$kk],array('is_custom'=>true)));
                    }
                }
			}
		}else{
            foreach($custom_option as $key=>$value)
                $custom_option[$key]['is_custom'] = true;
			$data = $custom_option;
        }
        if(!empty($data)){
            foreach($data as $k=>$v)
                $data[$k]['_id'] = $k;
            $this->opm->aasort($data,'option_group');
        }

		//pr($products['productoptions']);pr($custom_option); pr($data);die;
		//tim danh sach field proids trong cac option cua line dang xu ly
		$arr_lineid = $this->find_sub_line_entry_for_line($idsub,'proids','swap');
		//tim danh sach cac product id
		$arr_proid = $this->find_sub_line_entry_for_line($idsub,'proids');

		//pr($arr_lineid);pr($arr_proid);die;echo $proids."</br>";

		foreach($data as $kks=>$vvs){
			if(!isset($vvs['product_id']))
				continue;

			$proids = $products_id.'_'.$kks;
			$data[$kks] = $vvs;
			if(in_array($proids,$arr_proid))
				$data[$kks]['choice'] = 1;
			else
				$data[$kks]['choice'] = 0;

			if(isset($arr_lineid[$products_id.'_'.$kks]))
				$data[$kks]['line_no'] = $arr_lineid[$products_id.'_'.$kks];
			else
				$data[$kks]['line_no'] = '';

			if(isset($vvs['require']) && (int)$vvs['require']==1){
				if(isset($vvs['group_type']) && $vvs['group_type']=='Exc')
					$m=0;
				//else
					//$data[$kks]['xlock']['choice'] = 1;
			}

			if(!isset($vvs['proline_no']))
				$data[$kks]['proline_no'] = $kks;

			if(!isset($vvs['parent_line_no']))
				$data[$kks]['parent_line_no'] = $idsub;

			if(isset($vvs['option_group'])){
				$option_group[$vvs['option_group']] = (string)$vvs['option_group'];
				$groupstr.= (string)$vvs['option_group'].',';
			}
		}

		$arr_return = array();
		$arr_return['option'] = $data;
		$arr_return['groupstr'] = $groupstr;
		$arr_return['option_group'] = $option_group;
		return $arr_return;

	}
	public function option_list() {
		if(isset($_POST['submit'])){
            $this->option_cal_price($_POST);
        }
        $opname = 'products';
        $arr_set = $this->opm->arr_settings;
        $subdatas = $arr_subsetting = $custom_option_group = array();
        $salesorder_code = $sumrfq = 0; $products_id = $idsub = $groupstr = '';
		//neu idopt khac rong
        if ($this->params->params['pass'][1] != '') {
            //DATA: salesorder line details
            $idsub = $this->params->params['pass'][1];
            $arr_ret = $this->opm->select_one(array('_id'=> new MongoId($this->get_id())),array('products','options','salesorder_date'));
            if(!isset($arr_ret['options']))
            	$arr_ret['options'] = array();
            $subdatas['salesorder_line_details'] = array();
            $products_id = '';
            if(!empty($arr_ret[$opname])){
                if(isset($arr_ret[$opname][$idsub])&&!$arr_ret[$opname][$idsub]['deleted']){
                    $products_note = '';
                    $subdatas['salesorder_line_details'] = $arr_ret[$opname][$idsub];
                    $this->set('products_name',$arr_ret[$opname][$idsub]['products_name']);
                    $products_id = $arr_ret[$opname][$idsub]['products_id'];
                    if(is_object($products_id)){
                        $this->selectModel('Product');
                        $notes = $this->Product->select_one(array('_id'=>$products_id),array('otherdetails'));
                        if(isset($notes['otherdetails'])){
                            foreach($notes['otherdetails'] as $note){
                                if(isset($note['deleted']) && $note['deleted']) continue;
                                $products_note = '<b>'.$note['heading'].'</b> '.$note['details'];
                            }
                        }
                    }
                    $this->set('products_name',$arr_ret[$opname][$idsub]['products_name']);
                    $this->set('products_note',$products_note);
                }
            }
            //DATA: option list
            $arr_ret[$opname][$idsub]['products_id'] = $products_id;
            $option_list_data = $this->new_option_data(array('key'=>$idsub,'products_id'=>$products_id,'options'=>$arr_ret['options'],'date'=>$arr_ret['salesorder_date']),$arr_ret['products']);
            $subdatas['option'] = $option_list_data['option'];

        }

        //VIEW: option list
        $arr_field_options['option']['option'] = array(
                'title'     => "Options for this item",
                'type'      => 'listview_box',
                'link'      => array('w' => '1', 'cls' => 'products','field'=>'product_id'),
                'css'       => 'width:100%;',
                'height'    => '420',
                'reltb' => 'tb_product@options',
                'footlink'  => array('label' => 'Click to view and edit in this product', 'link' => ''.URL.'/products/entry/'.$products_id),
                'field'     => array(
                        'choice' => array(
                            'name' => __('Choice'),
                            'width' => '5',
                            'type'=>'checkbox',
                            'edit'=>'1',
                            'default'=>0,
                        ),
                        'code' => array(
                            'name' => __('Code'),
                            'type' => 'text',
                            'width' => 3,
                            'align' => 'center',
                        ),
                        'product_name' => array(
                            'name' => __('Name'),
                            'width' => 22,
                        ),
                        'product_id' => array(
                            'name' => __('ID'),
                            'type'=>'hidden',
                        ),
                        'require' => array(
                            'name' => __('Req'),
                            'width' => 3,
                            'align'=>'center',
                            'type'=>'checkbox',
                        ),
                        'same_parent' => array(
                            'name' => __('S.P.'),
                            'width' => 3,
                            'align'=>'center',
                            'type'=>'checkbox',
                        ),
                        'user_custom' => array(
                            'name' => __('Custom Price'),
                            'width' => 3,
                            'align'=>'center',
                            'type'=>'checkbox',
                            'edit'  => '1',
                        ),
                        'unit_price' => array(
                            'name' => __('Unit cost'),
                            'width' => '7',
                            'type' => 'price',
                            'edit'  => '1',
                            'align' => 'right',
                            'numformat'=>3,
                        ),
                        'oum' => array(
                            'name' => __('UOM'),
                            'width' => '5',
                            'type'      => 'text',
                            'droplist'  => 'product_oum_area',
                            'align' => 'center',
                        ),
                        'sell_by' => array(
                            'type'      => 'hidden',
                        ),
                        'discount' => array(
                            'name' => __('%Discount'),
                            'width' => '8',
                            'edit'  => '1',
                            'type' => 'hidden',
                            'align' => 'right',
                        ),
                        'quantity' => array(
                            'name' => __('Quantity'),
                            'width' => '5',
                            'edit'  => '1',
                            'type' => 'price',
                            'align' => 'right',
                            'numformat'=>0,
                        ),
                        'sub_total' => array(
                            'name' => __('Sub total'),
                            'width' => '7',
                            'align' => 'right',
                            'type' => 'price',
                        ),
                        'group_type' => array(
                            'name' => __('Type'),
                            'width' => '7',
                            'type'=>'text',
                            'droplist' => 'product_group',
                        ),
                        'option_group' => array(
                            'name' => __('Group'),
                            'width' => '7',
                            'type'=>'select',
                            'droplist' => 'product_group',
                        ),
                        //so thu tu line entry cua option nay
                        'line_no' => array(
                            'width' => '0',
                            'type'=>'hidden',
                        ),
                        //so thu tu option ben product
                        'proline_no' => array(
                            'width' => '0',
                            'type'=>'hidden',
                        ),
                        //so thu tu trong option cua quota
                        'this_line_no' => array(
                            'type'=>'hidden',
                        ),
                        'thisline_no' => array(
                            'type'=>'hidden',
                        ),
                        //so thu tu line entry cha
                        'parent_line_no' => array(
                            'width' => '0',
                            'type'=>'hidden',
                        ),
                        'hidden' => array(
                            'name' => __('Hidden'),
                            'title' => 'Hidden from line PDFs',
                            'edit'  => 1,
                            'width' => 3,
                            'align'=>'center',
                            'type'=>'checkbox',
                        ),
                        'delete' => array(
                            'type'  => 'delete_icon',
                            'rev'   => 'option',
                            'node'   => 'options',
                            'width' => 2
                        )
                ),
        );
        if(!isset($arr_ret[$opname][$idsub]['products_id']) || !is_object($arr_ret[$opname][$idsub]['products_id'])){
             $arr_field_options['option']['option']['add'] = 'Add more option';
			 $arr_field_options['option']['option']['delete'] = '1';
			 $arr_field_options['option']['option']['field']['require']['edit'] = '1';
			 $arr_field_options['option']['option']['field']['same_parent']['edit'] = '1';
			 $arr_field_options['option']['option']['field']['group_type']['type'] = 'select';
			 $arr_field_options['option']['option']['field']['group_type']['edit'] = '1';
			 $arr_field_options['option']['option']['field']['option_group']['edit'] = '1';
			 $option_select_custom = array();
			 $option_select_custom['option_group'] = $option_list_data['option_group'];
			 $option_select_custom['group_type'] = array('Inc'=>'Inc','Exc'=>'Exc');
			 $this->set('option_select_custom', $option_select_custom);
			 $this->set('groupstr', $option_list_data['groupstr']);
        }
        $this->set('subdatas', $subdatas);
        $this->set('arr_subsetting', $arr_subsetting);
		$this->set('arr_field_options', $arr_field_options);
        $this->set('line_sum', 18);
        $this->set('salesorderline', 'salesorder_line_details');
        $this->set('salesorder_code', $salesorder_code);
        $this->set('sumrfq', $sumrfq);
		$this->set('products_id', (string)$arr_ret[$opname][$idsub]['products_id']);
        $this->set('subitems', $idsub);
        $this->set('employee_id', $this->opm->user_id());
        $this->set('employee_name', $this->opm->user_name());
        if(!isset($arr_ret[$opname][$idsub]['is_saved']))
		  $this->set('custom_product2', '1');

    }

    public function salesorder_options_data($parent_line_no=0){
		$arr_op = array();
		$ids = $this->get_id();
		if($ids!=''){
			$query = $this->opm->select_one(array('_id'=>new MongoId($ids)),array('products','options'));
			if(isset($query['options']) && is_array($query['options']) && count($query['options'])>0){
				foreach($query['options'] as $k=>$vs){
					//lay thong tin cac option custom cua line entry cha $parent_line_no
					if(isset($vs['deleted']) && !$vs['deleted'] &&  isset($vs['parent_line_no']) && $vs['parent_line_no']==$parent_line_no ){
						if(!isset($vs['proline_no']) || $vs['proline_no']=='')
							$vs['proline_no'] = $k;
						$arr_op[$vs['proline_no']] = $vs;
						$arr_op[$vs['proline_no']]['thisline_no'] = $k;
						$arr_op[$vs['proline_no']]['proline_no'] = $k;
					}
				}
			}
		}
		return $arr_op;
	}

	// Luu line entry dang option dua vao product id va ids note option
	public function save_new_line_entry_option($product_id='',$option_id='',$option_for=''){
		if(isset($_POST['product_id']))
			$product_id = $_POST['product_id'];
		if(isset($_POST['option_id']))
			$option_id = $_POST['option_id'];
		if(isset($_POST['option_for']))
			$option_for = $_POST['option_for'];

		$ids = $this->get_id();
		if($ids!=''){
			$arr_insert = $line_entry = $parent_line = array();
			//lay note products hien co
			$query = $this->opm->select_one(array('_id'=>new MongoId($ids)),array('products','company_id'));
			if(isset($query['products']) && is_array($query['products']) && count($query['products'])>0){
				$line_entry = $query['products'];
                $key = count($line_entry);
			}
			//khởi tạo line entry mới
			$option_line_data = $this->option_list_data($product_id,$option_for);
			$options_data = $option_line_data['option'];
			if(isset($options_data[$option_id])){
				$vv = $options_data[$option_id];

				if(isset($line_entry[$option_for]))
					$parent_line = $line_entry[$option_for];

				$new_line = array();
				$new_line['code'] 			= $vv['code'];
				$new_line['sku'] 			= (isset($vv['sku']) ? $vv['sku'] : '');
				$new_line['products_name'] 	= $vv['product_name'];
				$new_line['products_id'] 	= $vv['product_id'];
				$new_line['quantity'] 		= $vv['quantity'];
				$new_line['sub_total'] 		= $vv['sub_total'];
				$new_line['sizew'] 			= isset($parent_line['sizew']) ? $parent_line['sizew'] : $vv['sizew'];
				$new_line['sizew_unit'] 	= isset($parent_line['sizew_unit'])?$parent_line['sizew_unit']:$vv['sizew_unit'];
				$new_line['sizeh'] 			= isset($parent_line['sizeh']) ? $parent_line['sizeh'] : $vv['sizeh'];
				$new_line['sizeh_unit'] 	= isset($parent_line['sizeh_unit'])?$parent_line['sizeh_unit']:$vv['sizeh_unit'];
				$new_line['sell_by'] 		= (isset($vv['sell_by']) ? $vv['sell_by'] : 'unit');
				$new_line['oum'] 			= $vv['oum'];
				$new_line['same_parent'] 	= isset($vv['same_parent']) ? (int)$vv['same_parent'] : 0;
				$new_line['sell_price'] 	= (float)$vv['unit_price'] - (float)$vv['unit_price']*((float)$vv['discount']/100);

				if(isset($query['products'][$option_for]['taxper']))
					$new_line['taxper'] 	= $query['products'][$option_for]['taxper'];
				if(isset($query['products'][$option_for]['tax']))
					$new_line['tax'] 		= $query['products'][$option_for]['tax'];
				$new_line['option_for'] 	= $option_for;
				$new_line['deleted'] 		= false;
				$new_line['proids'] 		= $product_id.'_'.$option_id;

				if(!isset($query['company_id']))
					$query['company_id']='';

				$cal_price = new cal_price;
				$cal_price->arr_product_items = $new_line;
				$cal_price->price_break_from_to = $this->change_sell_price_company($query['company_id'],$new_line['products_id']);
				$cal_price->field_change = '';
				$cal_price->cal_price_items();
				$new_line = array_merge((array)$new_line,(array)$cal_price->arr_product_items);

				//neu la same_parent thi thay gia cua parent va tinh lai gia
				if($new_line['same_parent']==1){
					$cal_price = new cal_price;
					$cal_price->arr_product_items = $new_line;
					$cal_price->price_break_from_to = $this->change_sell_price_company($query['company_id'],$new_line['products_id']);
					$cal_price->field_change = '';
					$cal_price->cal_price_items();
					$new_line['sell_price'] = $cal_price->arr_product_items['sell_price'];
					if(!isset($line_entry[$option_for]['plus_sell_price']))
						$line_entry[$option_for]['plus_sell_price'] = 0;

					$line_entry[$option_for]['sell_price'] += (float)$new_line['sell_price'];
					$line_entry[$option_for]['plus_sell_price'] += (float)$new_line['sell_price'];
					$cal_price2 = new cal_price;
					$cal_price2->arr_product_items = $line_entry[$option_for];
					$cal_price2->field_change = 'sell_price';
					$cal_price2->cal_price_items();
					$line_entry[$option_for] = $cal_price2->arr_product_items;
					$new_line['sell_price'] = '';
				}

				$line_entry[] = $new_line;


				//neu la nhom Exc thi xoa cac item khac cung nhom
				if(isset($vv['option_group']) && isset($vv['group_type']) &&  $vv['group_type']=='Exc'){
					foreach ($line_entry as $k=>$vs){
						if(isset($vs['deleted']) && !$vs['deleted'] && isset($vs['proids']) && $vs['proids'] !=$product_id.'_'.$option_id){
							$proids = explode("_",$vs['proids']);
							$proids = $proids[1];
							//neu cung nhom
							if(isset($options_data[$proids]['option_group']) && $options_data[$proids]['option_group']==$vv['option_group'] && isset($vs['option_for']) && $vs['option_for']==$option_for){
								//xoa item
								$line_entry[$k]['deleted'] = true;

								//tru ra neu la loai SP
								if($vs['same_parent']==1){
									$cal_price = new cal_price;
									$cal_price->arr_product_items = $line_entry[$option_for];
									$cal_price->price_break_from_to = $this->change_sell_price_company($query['company_id'],$vs['products_id']);
									$cal_price->field_change = '';
									$cal_price->cal_price_items();
									$sellprice = $cal_price->arr_product_items['sell_price'];

									if(!isset($line_entry[$option_for]['plus_sell_price']))
										$line_entry[$option_for]['plus_sell_price'] = 0;

									$line_entry[$option_for]['sell_price'] -= $sellprice;;
									$line_entry[$option_for]['plus_sell_price'] -= $sellprice;

									$cal_price2 = new cal_price;
									$cal_price2->arr_product_items = $line_entry[$option_for];
									$cal_price2->field_change = 'sell_price';
									$cal_price2->cal_price_items();
									$line_entry[$option_for] = $cal_price2->arr_product_items;
								}



							}

						}
					}
				}
                $keyfield = array(
                        "sub_total"     => "sub_total",
                        "tax"           => "tax",
                        "amount"        => "amount",
                        "sum_sub_total" => "sum_sub_total",
                        "sum_tax"       => "sum_tax",
                        "sum_amount"    => "sum_amount"
                    );
                $this->update_sum('products', $keyfield);
				//save lai
				$arr_insert['products'] = $line_entry;
				$arr_insert['_id'] = new MongoId($ids);
				if($this->opm->save($arr_insert)){
					//output
					if(isset($_POST['product_id'])){
                        $new_line['key'] = $key;
						echo json_encode($new_line);die;
					}else
						return $new_array;
				}else{
					if(isset($_POST['product_id'])){
						echo 'error'; die;
					}else
						return false;
				}
			}
		}die;
	}
	public function costing_for_line($idopt='') {
		$costing_list = array(); $merge = 1;
		if($idopt!=''){
			$ids = $this->get_id();
			if($ids!=''){
				$query = $this->opm->select_one(array('_id'=>new MongoId($ids)),array('costing'));
				if(isset($query['costing']) && is_array($query['costing']) && count($query['costing'])>0){
					foreach($query['costing'] as $keys=>$values){
						if(isset($values['deleted']) && !$values['deleted'] && isset($values['for_line']) && $values['for_line']==$idopt){
							$costing_list[$keys] = $values;
							$costing_list[$keys]['costing_id'] = $keys;
							if(is_object($values['product_id']))
								$costing_list[$keys]['xlock']['oum'] = '1';
							if(isset($values['markup']) && $merge==1){
								$merge = 0;
							}
						}

					}
				}
			}
		}

		$return['costing_list'] = $costing_list;
		$return['merge'] = $merge;
		//pr($return);die;
		return $return;
	}
	public function costing_list(){
		if(isset($_POST['submit'])){
            $this->option_cal_price($_POST);
        }
        $opname = 'products';
        $arr_set = $this->opm->arr_settings;
        $subdatas = $arr_subsetting = $custom_option_group = array();
        $salesorder_code = $sumrfq = 0; $products_id = $idsub = $groupstr = '';
		//neu idopt khac rong
		if ($this->params->params['pass'][1] != '') {
            //DATA: salesorder line details
            $idsub = $this->params->params['pass'][1];
            $arr_ret = $this->opm->select_one(array('_id'=> new MongoId($this->get_id())),array('products','options','salesorder_date'));
            if(!isset($arr_ret['options']))
            	$arr_ret['options'] = array();
            $subdatas['salesorder_line_details'] = array();
            $products_id = '';
            if(!empty($arr_ret[$opname])){
                foreach($arr_ret[$opname] as $key => $value){
                    if($key!=$idsub) continue;
                    if(isset($value['deleted'])&&$value['deleted']) continue;
                    $subdatas['salesorder_line_details'] = $value;
                    $subdatas['salesorder_line_details']['key'] = $key;
                    $products_id = $value['products_id'];
                    break;
                }
            }
            //DATA: option list
            $arr_ret[$opname][$idsub]['products_id'] = $products_id;
            $option_list_data = $this->new_option_data(array('key'=>$idsub,'products_id'=>$products_id,'options'=>$arr_ret['options'],'date'=>$arr_ret['salesorder_date']),$arr_ret['products']);
            $subdatas['option'] = $option_list_data['option'];
        }
        //VIEW: option list
        $arr_field_options['option']['option'] = array(
                'title'     => "Making for this item",
                'type'      => 'listview_box',
                'link'      => array('w' => '1', 'cls' => 'products','field'=>'product_id'),
                'css'       => 'width:100%;',
                'height'    => '420',
                'reltb'     => 'tb_product@options',
                'footlink'  => array('label' => 'Click to view and edit in this product', 'link' => ''.URL.'/products/entry/'.$products_id),
                'field'     => array(
                        'code' => array(
                            'name' => __('Code'),
                            'type' => 'text',
                            'width' => '5',
                            'align' => 'center',
                        ),
                        'product_name' => array(
                            'name' => __('Name'),
                            'width' => '28',
                            'edit'  => 1,
                        ),
                        'product_id' => array(
                            'name' => __('ID'),
                            'type'=>'hidden',
                        ),
                        'require' => array(
                            'width' => '5',
                            'align'=>'center',
                            'type'=>'hidden',
                        ),
                        'choice' => array(
                            'width' => '5',
                            'align'=>'center',
                            'type'=>'hidden',
                        ),
                        'same_parent' => array(
                            'name' => __('<span title="Same info as parent product">S.P.</span>'),
                            'width' => '3',
                            'align'=>'center',
                            'type'=>'checkbox',
                        ),
                        'unit_price' => array(
                            'name' => __('Unit cost'),
                            'width' => '7',
                            'type' => 'price',
                            'edit'  => '1',
                            'align' => 'right',
                            'numformat'=>3,
                        ),
                        'oum' => array(
                            'name' => __('UOM'),
                            'width' => '5',
                            'type'      => 'select',
                            'droplist'  => 'product_oum_area',
                            'edit'  => 1
                        ),
                        'discount' => array(
                            'name' => __('%Discount'),
                            'width' => '8',
                            'edit'  => '1',
                            'type' => 'hidden',
                            'align' => 'right',
                        ),
                        'quantity' => array(
                            'name' => __('Quantity'),
                            'width' => '5',
                            'edit'  => '1',
                            'type' => 'text',
                            'align' => 'right',
                        ),
                        'sub_total' => array(
                            'name' => __('Sub total'),
                            'width' => '7',
                            'align' => 'right',
                            'type' => 'price',
                        ),
                        'group_type' => array(
                            'name' => __('Type'),
                            'width' => '5',
                            'type'=>'text',
                            'droplist' => 'product_group',
                        ),
                        'option_group' => array(
                            'name' => __('Group'),
                            'width' => '7',
                            'type'=>'select',
                            'droplist' => 'product_group',
                        ),
                        //so thu tu line entry cua option nay
                        'line_no' => array(
                            'width' => '0',
                            'type'=>'hidden',
                        ),
                        //so thu tu option ben product
                        'proline_no' => array(
                            'width' => '0',
                            'type'=>'hidden',
                        ),
                        //so thu tu trong option cua quota
                        'thisline_no' => array(
                            'type'=>'hidden',
                        ),
                        'this_line_no' => array(
                            'type'=>'hidden',
                        ),
                        //so thu tu line entry cha
                        'parent_line_no' => array(
                            'width' => '0',
                            'type'=>'hidden',
                        ),
                        'delete' => array(
                            'type'  => 'delete_icon',
                            'rev'   => 'option',
                            'node'   => 'options',
                            'width' => 2
                        )
                ),
        );
        if(!is_object($arr_ret[$opname][$idsub]['products_id']) || $this->Product->count(array('_id'=>$arr_ret[$opname][$idsub]['products_id'],'options.deleted'=>false))==0){
             $arr_field_options['option']['option']['add'] = 'Add more option';
			 $arr_field_options['option']['option']['field']['require']['edit'] = '1';
			 $arr_field_options['option']['option']['field']['same_parent']['edit'] = '1';
			 $arr_field_options['option']['option']['field']['group_type']['type'] = 'select';
			 $arr_field_options['option']['option']['field']['group_type']['edit'] = '1';
			 $arr_field_options['option']['option']['field']['option_group']['edit'] = '1';
			 $option_select_custom = array();
			 $option_select_custom['option_group'] = $option_list_data['option_group'];
			 $option_select_custom['group_type'] = array('Inc'=>'Inc','Exc'=>'Exc');
			 $this->selectModel('Setting');
             $option_select_custom['oum'] = $this->Setting->uom_option_list(true);
             $this->set('option_select_custom', $option_select_custom);
			 $this->set('groupstr', $option_list_data['groupstr']);
        }
        $this->set('subdatas', $subdatas);
        $this->set('arr_subsetting', $arr_subsetting);
		$this->set('arr_field_options', $arr_field_options);
        $this->set('line_sum', 18);
        $this->set('salesorderline', 'salesorder_line_details');
        $this->set('salesorder_code', $salesorder_code);
        $this->set('sumrfq', $sumrfq);
		$this->set('products_id', (string)$arr_ret[$opname][$idsub]['products_id']);
        $this->set('subitems', $idsub);
        $this->set('employee_id', $this->opm->user_id());
        $this->set('employee_name', $this->opm->user_name());
        if(!isset($arr_ret[$opname][$idsub]['is_saved']))
		  $this->set('custom_product2', '1');
	}
	public function costing_list2() {
		if(isset($_POST)&&!empty($_POST)){
			$key = $this->params->params['pass'][1];
			$this->save_costing_list($key);
		}
		$add_custom_product = true;
		$this->set('return_mod', true);
		$this->set('return_title', 'Making for this item');
		$this->set('return_link', URL . '/salesorders/entry');
		$opname = 'products';
		$arr_set = $this->opm->arr_settings;
		$subdatas = $arr_subsetting = array();
		$salesorder_code = $sumrfq = 0; $products_id = $idsub = '';
		$costing_lock = '0';
		if($this->params->params['pass'][1] != ''){
			//DATA: salesorder line details
			$idsub = $this->params->params['pass'][1];
			$arr_ret = $this->line_entry_data($opname,0,'options_list');
			$costing_for_line = $this->costing_for_line($idsub);
			//pr($costing_for_line);die;
			if (isset($arr_ret[$opname][$idsub]) && !($arr_ret[$opname][$idsub]['deleted']))
				$subdatas['salesorder_line_details'] = $arr_ret[$opname][$idsub];
			else
				$subdatas['salesorder_line_details'] = array();

			//DATA: costing list
			$subdatas['costing'] = $products = array();
			if(isset($arr_ret[$opname][$idsub]['products_id']) && is_object($arr_ret[$opname][$idsub]['products_id'])){
				$products_id = (string)$arr_ret[$opname][$idsub]['products_id'];
				$products = $this->requestAction('/products/costings_data/'.$products_id);
				//pr($products);die;
			}
			//merge data trong QT với Product
			if(count($costing_for_line['costing_list'])>0 && $costing_for_line['merge']==1 && isset($arr_ret[$opname][$idsub]['products_id']) && is_object($arr_ret[$opname][$idsub]['products_id'])){

				 $subdatas['costing'] = $products['madeup'];
				 foreach($costing_for_line['costing_list'] as $kks=>$vvs){
					 if(isset($vvs['product_id']) && isset($vvs['view_in_detail'])){
						$view_in_detail[(string)$vvs['product_id'].'_'.$vvs['for_line']] = $vvs['view_in_detail'];
						$costing_id[(string)$vvs['product_id'].'_'.$vvs['for_line']] = $kks;
					 }
				 }
				 //pr($view_in_detail);die;
				 foreach($subdatas['costing'] as $keys=>$values){
					if(isset($values['product_id']) && isset($view_in_detail[(string)$values['product_id'].'_'.$idsub])){
						$subdatas['costing'][$keys]['view_in_detail'] = $view_in_detail[(string)$values['product_id'].'_'.$idsub];
						$subdatas['costing'][$keys]['costing_id'] = $costing_id[(string)$values['product_id'].'_'.$idsub];
					}
				 }

				$costing_lock = '1';
				$add_custom_product = false;
				//pr($subdatas['costing']);die;


			//chi dung data trong QT
			}else if(count($costing_for_line['costing_list'])>0 && $costing_for_line['merge']!=1){
				 $subdatas['costing'] = $costing_for_line['costing_list'];


			//chi dung data trong Product
			}else if(isset($arr_ret[$opname][$idsub]['products_id']) && is_object($arr_ret[$opname][$idsub]['products_id'])){

				if(isset($products['madeup']) && is_array($products['madeup']) && count($products['madeup'])>0){
					$subdatas['costing'] = $products['madeup'];
					$arr_lineid = $this->find_sub_line_entry('proids','swap');
					$arr_proid = $this->find_sub_line_entry('proids');
					foreach($subdatas['costing'] as $kks=>$vvs){
						$proids = '';
						if(isset($vvs['product_id']))
							$proids = (string)$arr_ret[$opname][$idsub]['products_id'].'_'.$kks;
						if(in_array($proids,$arr_proid))
							$subdatas['costing'][$kks]['choice'] = 1;
						else
							$subdatas['costing'][$kks]['choice'] = 0;
						if(isset($arr_lineid[$products_id.'_'.$kks]))
							$subdatas['costing'][$kks]['idline'] = $arr_lineid[$products_id.'_'.$kks];
						else
							$subdatas['costing'][$kks]['idline'] = '';

						$subdatas['costing'][$kks]['costing_id'] = '';
					}
					$costing_lock = '1';
					$add_custom_product = false;
				}
			}
		}
		//VIEW: salesorder line details
		$strs = $arr_set['relationship']['line_entry']['block']['products']['field'];
		$arr_subsetting['salesorder_line_details'] = array(
			'code'          => $strs['code']['name'],
			'products_name' => 'Description',
			'sell_price'    => $strs['sell_price']['name'],
			'oum'           => $strs['oum']['name'],
			'quantity'      => $strs['quantity']['name'],
			'sub_total'     => $strs['sub_total']['name'],
			'taxper'        => $strs['taxper']['name'],
			'tax'           => $strs['tax']['name'],
			'amount'        => $strs['amount']['name'],
		);

		//VIEW: costing list
		$arr_field_options['costing']['costing'] = array(
				'title' 	=> "Making for this item",
				'type' 		=> 'listview_box',
				'link' 		=> array('w' => '1', 'cls' => 'products','field'=>'product_id'),
				'css'  		=> 'width:79%;float:right;',
				'add' 		=> 'Add cost / item',
				'height'  	=> '420',
				'delete'	=> '2',
				'reltb' 	=> 'tb_salesorder@costing',
				'footlink'  => array('label' => 'Click to view and edit in this product', 'link' => ''.URL.'/products/entry/'.$products_id),
				'field'		=> array(
						'code' => array(
							'name' => __('Code'),
							'type' => 'text',
							'width' => '3',
							'align' => 'center',
						),
						'sku' => array(
							'name' => __('SKU'),
							'type' => 'text',
							'width' => '5',
						),
						'product_name' => array(
							'name'  => __('Name'),
							'width' => '25',
							'edit'  => '1',
						),
						'product_id' => array(
							'name' => __('ID'),
							'type'=>'hidden',
						),
						'product_type' => array(
							'name' => __('Type'),
							'type'=>'select',
							'width' => '8',
							'edit'  => '1',
							'droplist' => 'product_type',
						),
						'category' => array(
							'name' => __('Category'),
							'width' => '0',
							'type' => 'hidden',
							'droplist' => 'product_category',
						),
						'company_id' => array(
							'type' => 'id',
							'width' => '0',
						),
						'company_name' => array(
							'name' => __('Supplier'),
							'type' => 'text',
							'align' => 'left',
							'width' => '11',
							'title' => 'Specify Current supplier',
							'para' => ",'?is_supplier=1'",
							'indata' => '0',
						),
						'unit_price' => array(
							'name' => __('Unit cost'),
							'width' => '5',
							'type' => 'price',
							'align' => 'right',
							'numformat'=>3,
							'edit'  => '1',
						),
						'oum' => array(
							'name' => __('UOM'),
							'width' => '3',
							'type' 		=> 'select',
							'droplist'	=> 'product_oum_area',
							'edit'  => '1',
						),
						'markup' => array(
							'name' => __('%Markup'),
							'width' => '5',
							'type' => 'price',
							'align' => 'right',
							'default' => '0',
							'edit'  => '1',
						),
						'margin' => array(
							'name' => __('%Margin'),
							'width' => '5',
							'type' => 'price',
							'align' => 'right',
							'default' => '0',
							'edit'  => '1',
						),
						'quantity' => array(
							'name' => __('Quantity'),
							'width' => '5',
							'type' => 'text',
							'align' => 'right',
							'default' => '1',
							'edit'  => '1',
						),
						'sub_total' => array(
							'name' => __('Sub total'),
							'width' => '5',
							'align' => 'right',
							'type' => 'price',
						),
						'view_in_detail' => array(
						   'name' => __('<span title="List this costing in Text entry">Detail List</span>'),
						   'width' => '4',
						   'align' => 'center',
						   'type' => 'checkbox',
						   'edit'  => '1',
						),
						'costing_id' => array(
						   'name' => __('Costing ID'),
						   'width' => '0',
						   'type' => 'hidden',
						),
				),
		);

		if($costing_lock=='1'){
			$field_costing = $arr_field_options['costing']['costing']['field'];
			unset($field_costing['product_name']['edit']);
			unset($field_costing['product_type']['edit']);
			unset($field_costing['unit_price']['edit']);
			unset($field_costing['oum']['edit']);
			unset($field_costing['markup']['edit']);
			unset($field_costing['margin']['edit']);
			unset($field_costing['quantity']['edit']);

			$arr_field_options['costing']['costing']['field'] = $field_costing;
			unset($arr_field_options['costing']['costing']['delete']);
		}else{
			$field_costing = $arr_field_options['costing']['costing']['field'];
			unset($field_costing['company_name']);
			$field_costing['product_name']['width'] = '25';
			$field_costing['product_type']['width'] = '10';
			$field_costing['oum']['width'] = '10';
			$arr_field_options['costing']['costing']['field'] = $field_costing;
		}

		$option_select_custom = array();
		$option_select_custom['product_type'] = $this->Setting->select_option_vl(array('setting_value'=>'product_type'));
		$option_select_custom['oum'] = array_merge(
			 $this->Setting->select_option_vl(array('setting_value'=>'product_oum_area'))
			 ,$this->Setting->select_option_vl(array('setting_value'=>'product_oum_lengths'))
			 ,$this->Setting->select_option_vl(array('setting_value'=>'product_oum_unit'))
		);
		$this->set('option_select_custom', $option_select_custom);
		$this->set('subdatas', $subdatas);
		$this->set('arr_subsetting', $arr_subsetting);
		$this->set('arr_field_options', $arr_field_options);
		$this->set('line_sum', 15);
		$this->set('custom_product',$add_custom_product);
		$this->set('lock_add', $costing_lock);
		$this->set('salesorderline', 'salesorder_line_details');
		$this->set('salesorder_code', $salesorder_code);
		$this->set('line_details_width', 20);
		$this->set('sumrfq', $sumrfq);
		$this->set('products_id', $products_id);
		$this->set('subitems', $idsub);
		$this->set('employee_id', $this->opm->user_id());
		$this->set('employee_name', $this->opm->user_name());
	}
	 function save_costing_list($key){
        $salesorder = $this->opm->select_one(array('_id'=>new MongoId($this->get_id())));
        $this->selectModel('Product');
        $this->selectModel('Setting');
        //Lay default
        $this->Product->arrfield();
        $default_field = $this->Product->arr_temp;
        $arr_line_entry = $default_field;
        $product_line_entry = $salesorder['products'][$key];
        //Truong hop Product line entry chua co ID
        if(!isset($product_line_entry['products_id']) || !is_object($product_line_entry['products_id'])){
            //Add line entry vao Product truoc
            $arr_line_entry['code']     =   $this->Product->get_auto_code('code');
            $arr_line_entry['name'] =   $product_line_entry['products_name'];
            $arr_line_entry['product_type'] =   'Custom Product';
            $arr_line_entry['company_name'] = (isset($salesorder['company_name']) ? $salesorder['company_name'] : '' );
            $arr_line_entry['company_id']   = (isset($salesorder['company_id'])&&is_object(($salesorder['company_id'])) ? new MongoId($salesorder['company_id']) : '' );
            $arr_line_entry['sizew']    = (isset($product_line_entry['sizew']) ? $product_line_entry['sizew'] : '');
            $arr_line_entry['sizew_unit']   = (isset($product_line_entry['sizew_unit']) ? $product_line_entry['sizew_unit'] : 'in');
            $arr_line_entry['sizeh']    = (isset($product_line_entry['sizeh']) ? $product_line_entry['sizeh'] : '');
            $arr_line_entry['sizeh_unit']   = (isset($product_line_entry['sizeh_unit']) ? $product_line_entry['sizeh_unit'] : 'in');
            $arr_line_entry['sell_by']  = (isset($product_line_entry['sell_by']) ? $product_line_entry['sell_by'] : 'unit');
            $arr_line_entry['sell_price']   = (isset($product_line_entry['sell_price']) ? $product_line_entry['sell_price'] : 0);
            $arr_line_entry['oum']  = (isset($product_line_entry['oum']) ? $product_line_entry['oum'] : 'unit');
            $arr_line_entry['oum_depend']   = '';
            $arr_line_entry['unit_price']   = '';
            $arr_line_entry['markup']   = (isset($product_line_entry['markup']) ? $product_line_entry['markup'] : 0);
            $arr_line_entry['margin']  = (isset($product_line_entry['margin']) ? $product_line_entry['margin'] : 0);
            $this->Product->save($arr_line_entry);
            $line_entry_id = new MongoId($this->Product->mongo_id_after_save);
            $arr_line_entry['_id'] = $line_entry_id;
            //End add line entry
        } else {
            //Truong hop Product line entry da co ID
            $line_entry_id = new MongoId($product_line_entry['products_id']);
            $arr_line_entry = $this->Product->select_one(array('_id'=>$line_entry_id));
        }
        //Add costing to Product
        if(isset($salesorder['costing'])){
            $i = 0;
            if(isset($arr_line_entry['madeup']))
                $i = count($arr_line_entry['madeup']);
            $line_entry_madeup = array();
            foreach($salesorder['costing'] as $costing_key=>$costing){
                if($costing['for_line']!=$key) continue;
                if(!isset($costing['product_id']) || $costing['product_id'] == ''){ //Truong hop custom product thi save vao
                    $arr_save = $default_field;
                    $arr_save['code']   =   $this->Product->get_auto_code('code');
                    $arr_save['sku'] = '';
                    $arr_save['name']   =   $costing['product_name'];
                    $arr_save['product_type'] = (isset($costing['product_type']) ? $costing['product_type'] : 'Product');
                    $arr_save['company_name']   = (isset($salesorder['company_name']) ? $salesorder['company_name'] : '' );
                    $arr_save['company_id']     = (isset($salesorder['company_id'])&&is_object($salesorder['company_id']) ? new MongoId($salesorder['company_id']) : '' );
                    $arr_save['sizew']  = 12;
                    $arr_save['sizew_unit'] = 'in';
                    $arr_save['sizeh']  = 12;
                    $arr_save['sizeh_unit'] = 'in';
                    $arr_save['oum']    = (isset($costing['oum']) ? $costing['oum'] : 'unit');
                    $arr_save['sell_by']    = (isset($costing['sell_by']) ? $costing['sell_by'] : 'unit');
                    $arr_save['sell_price'] = (isset($costing['unit_price']) ? $costing['unit_price'] : 0);
                    $arr_save['oum_depend']    = (isset($costing['oum']) ? $costing['oum'] : 0);
                    $arr_save['unit_price'] = (isset($costing['unit_price']) ? $costing['unit_price'] : 0);
                    $arr_save['cost_price'] = (isset($costing['unit_price']) ? $costing['unit_price'] : 0);
                    $arr_save['markup'] = (isset($costing['markup']) ? $costing['markup'] : 0);
                    $arr_save['margin'] = (isset($costing['margin']) ? $costing['margin'] : 0);
                    $arr_save['quantity'] = (isset($costing['quantity']) ? $costing['quantity'] : 1);
                    $arr_save['view_in_detail'] = (isset($costing['view_in_detail'])&&$costing['view_in_detail']==1 ? 1 : 0);
                    $this->Product->save($arr_save);
                    $id = new MongoId($this->Product->mongo_id_after_save);
                    $salesorder['costing'][$costing_key]['product_id'] = $id;
                    $salesorder['costing'][$costing_key]['code'] = $arr_save['code'];
                } else { //Truong hop product co ID
                    $id = new MongoId($costing['product_id']);
                    $arr_save['sku'] = (isset($costing['sku']) ? $costing['sku'] : '');
                    $arr_save['name'] = (isset($costing['product_name']) ? $costing['product_name'] : '');
                    $arr_save['code'] = (isset($costing['code']) ? $costing['code'] : '');
                    $arr_save['product_type'] = (isset($costing['product_type']) ? $costing['product_type'] :'Product');
                    $arr_save['category'] = (isset($costing['category']) ? $costing['category'] : '');
                    $arr_save['company_id'] = (isset($costing['company_id'])&&is_object($costing['company_id']) ? $costing['company_id'] : '');
                    $arr_save['unit_price'] = (isset($costing['unit_price']) ?(float)$costing['unit_price'] : 0);
                    $arr_save['oum'] = (isset($costing['oum']) ? $costing['oum'] : 'unit');
                    $arr_save['markup'] = (isset($costing['markup']) ? $costing['markup'] : 0);
                    $arr_save['margin'] = (isset($costing['margin']) ? $costing['margin'] : 0);
                    $arr_save['quantity'] = (isset($costing['quantity']) ? $costing['quantity'] : 1);
                }
                $line_entry_madeup[$i] = array(
                                            'deleted'       => false,
                                            'sku'           => $arr_save['sku'],
                                            'product_name'  => $arr_save['name'],
                                            'product_id'    => $id,
                                            'product_type'  => $arr_save['product_type'],
                                            'product_code'  => $arr_save['code'],
                                            'category'      => $arr_save['category'],
                                            'company_id'    => $arr_save['company_id'],
                                            'unit_price'    => $arr_save['unit_price'],
                                            'oum'           => $arr_save['oum'],
                                            'markup'        => $arr_save['markup'],
                                            'margin'        => $arr_save['margin'],
                                            'quantity'      => $arr_save['quantity']
                                            );
                unset($salesorder['costing'][$costing_key]['unit_price']);
                unset($salesorder['costing'][$costing_key]['oum']);
                unset($salesorder['costing'][$costing_key]['oum']);
                unset($salesorder['costing'][$costing_key]['markup']);
                unset($salesorder['costing'][$costing_key]['margin']);
                unset($salesorder['costing'][$costing_key]['sub_total']);
                unset($salesorder['costing'][$costing_key]['sell_by']);
                $i++;

            }
        }
        //End add costing to Product
        if(!empty($line_entry_madeup))
            $arr_line_entry['madeup'] = $line_entry_madeup;
        $this->Product->save($arr_line_entry);
        $salesorder['products'][$key]['code'] = $arr_line_entry['code'];
        $salesorder['products'][$key]['products_id'] = $line_entry_id;
        $this->opm->save($salesorder);
    }
	public function email_pdf() {
		$this->layout = 'pdf';
		$info_data = (object) array();
		$ids = $this->get_id();
		if ($ids != '') {
			$query = $this->opm->select_one(array('_id' => new MongoId($ids)));
			$arrtemp = $query;
			//set header
			$this->set('logo_link', 'img/logo_anvy.jpg');
			$this->set('company_address', '3145 - 5th Ave NE<br />Calgary  AB  T2A  6A3<br />');

			//customer address
			$customer = '';
			if (isset($arrtemp['company_id']) && strlen($arrtemp['company_id']) == 24)
				$customer .= '<b>' . $this->get_name('Company', $arrtemp['company_id']) . '</b><br />';
			else if (isset($arrtemp['company_name']))
				$customer .= '<b>' . $arrtemp['company_name'] . '</b><br />';
			if (isset($arrtemp['contact_id']) && strlen($arrtemp['contact_id']) == 24)
				$customer .= $this->get_name('Contact', $arrtemp['contact_id']) . '<br />';
			else if (isset($arrtemp['contact_name']))
				$customer .= $arrtemp['contact_name'] . '<br />';

			//loop 2 address
			$arradd = array('invoice', 'shipping');
			foreach ($arradd as $vvs) {
				$kk = $vvs;
				$customer_address = '';
				if (isset($arrtemp[$kk . '_address']) && isset($arrtemp[$kk . '_address'][0]) && count($arrtemp[$kk . '_address']) > 0) {
					$temp = $arrtemp[$kk . '_address'][0];
					if (isset($temp[$kk . '_address_1']) && $temp[$kk . '_address_1'] != '')
						$customer_address .= $temp[$kk . '_address_1'] . ' ';
					if (isset($temp[$kk . '_address_2']) && $temp[$kk . '_address_2'] != '')
						$customer_address .= $temp[$kk . '_address_2'] . ' ';
					if (isset($temp[$kk . '_address_3']) && $temp[$kk . '_address_3'] != '')
						$customer_address .= $temp[$kk . '_address_3'] . '<br />';
					else
						$customer_address .= '<br />';
					if (isset($temp[$kk . '_town_city']) && $temp[$kk . '_town_city'] != '')
						$customer_address .= $temp[$kk . '_town_city'];

					if (isset($temp[$kk . '_province_state']))
						$customer_address .= ' ' . $temp[$kk . '_province_state'] . ' ';
					else if (isset($temp[$kk . '_province_state_id']) && isset($temp[$kk . '_country_id'])) {
						$keytemp = $temp[$kk . '_province_state_id'];
						$provkey = $this->province($temp[$kk . '_country_id']);
						if (isset($provkey[$temp]))
							$customer_address .= ' ' . $provkey[$temp] . ' ';
					}


					if (isset($temp[$kk . '_zip_postcode']) && $temp[$kk . '_zip_postcode'] != '')
						$customer_address .= $temp[$kk . '_zip_postcode'];

					if (isset($temp[$kk . '_country']) && isset($temp[$kk . '_country_id']) && (int) $temp[$kk . '_country_id'] != "CA")
						$customer_address .= ' ' . $temp[$kk . '_country'] . '<br />';
					else
						$customer_address .= '<br />';
					$arr_address[$kk] = $customer_address;
				}
			}


			if (isset($arrtemp['name']) && $arrtemp['name'] != '')
				$heading = $arrtemp['name'];
			else
				$heading = '';
			if (!isset($arr_address['invoice']))
				$arr_address['invoice'] = '';
			$this->set('customer_address', $customer . $arr_address['invoice']);
			if (!isset($arr_address['shipping']))
				$arr_address['shipping'] = '';

			$this->set('shipping_address', $arr_address['shipping']);
			$this->set('ref_no', $arrtemp['code']);

			$info_data->contact_name = $arrtemp['contact_name'];
			$info_data->no = $arrtemp['code'];
			$info_data->job_no = $arrtemp['job_number'];
			$info_data->date = $this->opm->format_date($arrtemp['salesorder_date']);
			$info_data->po_no = $arrtemp['customer_po_no'];
			$info_data->ac_no = '';
			$info_data->terms = $arrtemp['payment_terms'];
			$info_data->required_date = $this->opm->format_date($arrtemp['payment_due_date']);

			$this->set('info_data', $info_data);
			/*             * Nội dung bảng giá */
			$date_now = date('Ymd');
			$time=time();
			$filename = 'SOR' . $date_now .$time. '-' . $info_data->no;


			$thisfolder = 'upload'.DS.date("Y_m");
			$thisfolder_1='upload'.','.date("Y_m");

			$folder = ROOT.DS.APP_DIR.DS.WEBROOT_DIR.DS.$thisfolder;
			if (!file_exists($folder)) {
				mkdir($folder, 0777, true);
			}





			$this->set('filename', $filename);
			$this->set('heading', $heading);
			$html_cont = '';
			if (isset($arrtemp['products']) && is_array($arrtemp['products']) && count($arrtemp['products']) > 0) {
				$line = 0;
				$colum = 7;
				foreach ($arrtemp['products'] as $keys => $values) {
					if (!$values['deleted']) {
						if ($line % 2 == 0)
							$bgs = '#fdfcfa';
						else
							$bgs = '#eeeeee';
						//code
						$html_cont .= '<tr style="background-color:' . $bgs . ';"><td class="first">';
						if (isset($values['code']))
							$html_cont .= '  ' . $values['code'];
						else
							$html_cont .= '  #' . $keys;
						//desription
						$html_cont .= '</td><td>';
						if (isset($values['products_name']))
							$html_cont .= str_replace("\n", "<br />", $values['products_name']);
						else
							$html_cont .= 'Empty';
						//width
						$html_cont .= '</td><td align="right">';
						if (isset($values['sizew']) && $values['sizew'] != '' && isset($values['sizew_unit']) && $values['sizew_unit'] != '')
							$html_cont .= $values['sizew'] . ' (' . $values['sizew_unit'] . ')';
						else if (isset($values['sizew']) && $values['sizew'] != '')
							$html_cont .= $values['sizew'] . ' (in.)';
						else
							$html_cont .= '';
						//height
						$html_cont .= '</td><td align="right">';
						if (isset($values['sizeh']) && $values['sizeh'] != '' && isset($values['sizeh_unit']) && $values['sizeh_unit'] != '')
							$html_cont .= $values['sizeh'] . ' (' . $values['sizeh_unit'] . ')';
						else if (isset($values['sizeh']) && $values['sizeh'] != '')
							$html_cont .= $values['sizeh'] . ' (in.)';
						else
							$html_cont .= '';
						//Unit price
						$html_cont .= '</td><td align="right">';
						if (isset($values['unit_price']))
							$html_cont .= $this->opm->format_currency($values['unit_price']);
						else
							$html_cont .= '0.00';
						//Qty
						$html_cont .= '</td><td align="right">';
						if (isset($values['quantity']))
							$html_cont .= $values['quantity'];
						else
							$html_cont .= '';
						//line total
						$html_cont .= '</td><td align="right" class="end">';
						if (isset($values['sub_total']))
							$html_cont .= $this->opm->format_currency($values['sub_total']);
						else
							$html_cont .= '';


						$html_cont .= '</td></tr>';
						$line++;
					}//end if deleted
				}//end for


				if ($line % 2 == 0) {
					$bgs = '#fdfcfa';
					$bgs2 = '#eeeeee';
				} else {
					$bgs = '#eeeeee';
					$bgs2 = '#fdfcfa';
				}

				$sub_total = $total = $taxtotal = 0.00;
				if (isset($arrtemp['sum_sub_total']))
					$sub_total = $arrtemp['sum_sub_total'];
				if (isset($arrtemp['sum_tax']))
					$taxtotal = $arrtemp['sum_tax'];
				if (isset($arrtemp['sum_amount']))
					$total = $arrtemp['sum_amount'];
				//Sub Total
				$html_cont .= '<tr style="background-color:' . $bgs . ';">
									<td colspan="' . ($colum - 1) . '" style="text-align: right;font-weight:bold;border-top:2px solid #aaa;" class="first">Sub Total:</td>
									<td style="text-align: right;border-top:2px solid #aaa;" class="end">' . $this->opm->format_currency($sub_total) . '</td>
							   </tr>';
				//GST
				$html_cont .= '<tr style="background-color:' . $bgs2 . ';">
									<td colspan="' . ($colum - 1) . '" style="text-align: right;font-weight:bold;" class="first">HST/GST:</td>
									<td align="right" class="end">' . $this->opm->format_currency($taxtotal) . '</td>
							   </tr>';
				//Total
				$html_cont .= '<tr style="background-color:' . $bgs . ';">
									<td colspan="' . ($colum - 1) . '" style="text-align: right;font-weight:bold;" class="first bottom">Total:</td>
									<td align="right" class="end bottom">' . $this->opm->format_currency($total) . '</td>
							   </tr>';
			}//end if


			$this->set('html_cont', $html_cont);
			if (isset($arrtemp['our_csr'])) {
				$this->set('user_name', ' ' . $arrtemp['our_csr']);
			} else
				$this->set('user_name', ' ' . $this->opm->user_name());
			//end set content
			//set footer
			$this->set('link_this_folder',$thisfolder);
			$this->render('email_pdf');
			$v_link_pdf= $thisfolder_1.','.$filename.'.pdf';
			$v_file_name=$filename.'.pdf';

			$this->redirect('/docs/add_from_option/'.$this->ModuleName().'/'.$this->get_id().'/'.$v_link_pdf.'/'.$v_file_name.'/'.$this->params->params['controller'].'');

		}
		die;
	}
	//Export pdf
	function _view_pdf($getfile=false,$type='', $ids = '', $is_html = 0)  {
		if($type=='group')
			$this->export_pdf = true;
		$this->layout = 'pdf';
		$info_data = (object) array();
		if($ids == '')
			$ids = $this->get_id();
		if ($ids != '') {
			$query = $this->opm->select_one(array('_id' => new MongoId($ids)));
			$arrtemp = $query;
			//set header
			$this->set('logo_link', 'img/logo_anvy.jpg');
			$this->set('company_address', '3145 - 5th Ave NE<br />Calgary  AB  T2A  6A3<br />');

			//customer address
			$customer = '';
			if (isset($arrtemp['company_id']) && strlen($arrtemp['company_id']) == 24)
				$customer .= '<b>' . $this->get_name('Company', $arrtemp['company_id']) . '</b><br />';
			else if (isset($arrtemp['company_name']))
				$customer .= '<b>' . $arrtemp['company_name'] . '</b><br />';
			if (isset($arrtemp['contact_id']) && strlen($arrtemp['contact_id']) == 24)
				$customer .= $this->get_name('Contact', $arrtemp['contact_id']) . '<br />';
			else if (isset($arrtemp['contact_name']))
				$customer .= $arrtemp['contact_name'] . '<br />';

			//loop 2 address
			$arradd = array('invoice', 'shipping');
			foreach ($arradd as $vvs) {
				$kk = $vvs;
				$customer_address = '';
				if (isset($arrtemp[$kk . '_address']) && isset($arrtemp[$kk . '_address'][0]) && count($arrtemp[$kk . '_address']) > 0) {
					$temp = $arrtemp[$kk . '_address'][0];
					if (isset($temp[$kk . '_address_1']) && $temp[$kk . '_address_1'] != '')
						$customer_address .= $temp[$kk . '_address_1'] . ', ';
					if (isset($temp[$kk . '_address_2']) && $temp[$kk . '_address_2'] != '')
						$customer_address .= $temp[$kk . '_address_2'] . ', ';
					if (isset($temp[$kk . '_address_3']) && $temp[$kk . '_address_3'] != '')
						$customer_address .= $temp[$kk . '_address_3'] . '<br />';
					else
						$customer_address .= '<br />';
					if (isset($temp[$kk . '_town_city']) && $temp[$kk . '_town_city'] != '')
						$customer_address .= $temp[$kk . '_town_city'].', ';

					if (isset($temp[$kk . '_province_state']))
						$customer_address .= ' ' . $temp[$kk . '_province_state'] . '<br/> ';
					else if (isset($temp[$kk . '_province_state_id']) && isset($temp[$kk . '_country_id'])) {
						$keytemp = $temp[$kk . '_province_state_id'];
						$provkey = $this->province($temp[$kk . '_country_id']);
						if (isset($provkey[$temp]))
							$customer_address .= ' ' . $provkey[$temp] . ', ';
					}


					if (isset($temp[$kk . '_zip_postcode']) && $temp[$kk . '_zip_postcode'] != '')
						$customer_address .= $temp[$kk . '_zip_postcode'].', ';

					if (isset($temp[$kk . '_country']) && isset($temp[$kk . '_country_id']) && $temp[$kk . '_country_id'] != "CA")
						$customer_address .= ' ' . $temp[$kk . '_country'] . '<br />';
					else
						$customer_address .= '<br />';
					$arr_address[$kk] = $customer_address;
				}
			}


			if (isset($arrtemp['heading']) && $arrtemp['heading'] != '')
				$heading = $arrtemp['heading'];
			else
				$heading = '';
			if (!isset($arr_address['invoice']))
				$arr_address['invoice'] = '';
			$this->set('customer_address', $customer . $arr_address['invoice']);
			if (!isset($arr_address['shipping']))
				$arr_address['shipping'] = '';
			if(isset($arrtemp['shipping_address'][0]['shipping_contact_name']))
				$this->set('ship_to',$arrtemp['shipping_address'][0]['shipping_contact_name']);
			$this->set('shipping_address', $arr_address['shipping']);
			$this->set('ref_no', $arrtemp['code']);

			$info_data->contact_name = $arrtemp['contact_name'];
			$info_data->no = $arrtemp['code'];
			$info_data->job_no = $arrtemp['job_number'];
			$info_data->date = $this->opm->format_date($arrtemp['salesorder_date']);
			$info_data->po_no = $arrtemp['customer_po_no'];
			$info_data->ac_no = '';
			$info_data->terms = $arrtemp['payment_terms'];
			$info_data->required_date = $this->opm->format_date($arrtemp['payment_due_date']);

			$this->set('info_data', $info_data);
			/*             * Nội dung bảng giá */
			$date_now = date('Ymd');
			$numkey = explode("-",$info_data->no);
			$filename = 'SO-'.$numkey[count($numkey)-1];
			$other_comment = '';
			if(isset($arrtemp['other_comment']))
				$other_comment = str_replace("\n","<br />",'<br />'.$arrtemp['other_comment']);
			$this->set('other_comment',$other_comment);
			$this->set('filename', $filename);
			$this->set('heading', $heading);
			$html_cont = '';
			$line_entry_data = $this->line_entry_data('products',  0, '', $ids);
			$minimum = $this->get_minimum_order();
			if($line_entry_data['sum_sub_total']<$minimum){
	            $line_entry_data = $this->get_minimum_order_adjustment($line_entry_data,$minimum, $ids);
            }
			if (isset($line_entry_data['products']) && is_array($line_entry_data['products']) && count($line_entry_data['products']) > 0) {
				$line = 0;
				$colum = 7;
				$options = array();
				if(isset($arrtemp['options']) && !empty($arrtemp['options']) )
					$options = $arrtemp['options'];
				if($type == 'group'){
					$arr_price = array();
					$arr_option = isset($query['options']) ? $query['options'] : array();
                    foreach($line_entry_data['products'] as $product){
                        if(!isset($product['option_for'])) continue;
                        if(!isset($product['same_parent']) || $product['same_parent'] == 1) continue;
                        if (!isset($values['custom_unit_price']))
                            $product['custom_unit_price'] = (isset($product['unit_price']) ? $product['unit_price'] : 0);
                        if(!isset($arr_price[$product['option_for']]))
                            $arr_price[$product['option_for']]['unit_price'] = $arr_price[$product['option_for']]['sub_total'] = 0;
                        $product['custom_unit_price'] = (float)str_replace(',', '', $product['custom_unit_price']);
                        $product['sub_total'] = (float)str_replace(',', '', $product['sub_total']);
                        $arr_price[$product['option_for']]['unit_price'] += $product['custom_unit_price'];
                        $arr_price[$product['option_for']]['sub_total'] += $product['sub_total'];
                    }

					foreach ($line_entry_data['products'] as $values) {
                        if (isset($values['deleted']) && !$values['deleted']) {
                            if ($line % 2 == 0)
                                $bgs = '#fdfcfa';
                            else
                                $bgs = '#eeeeee';
                            //code
	                    	if( isset($values['option_for']) && isset($query['products'][$values['option_for']] ) ){
							    $pro = $query['products'][$values['option_for']];
							    if(isset($pro['sku']) && strpos(str_replace(' ', '', $pro['sku']), 'DCP-') !== false)
							        continue;
							}


                            $html_cont .= '<tr style="background-color:' . $bgs . ';" nobr="true"><td class="first" style="width: 12%;">';
                            if(isset($values['option_for'])&&is_numeric($values['option_for'])){
                            	$values['sku'] = '';
                            	$values['products_name'] = '&nbsp;&nbsp;&nbsp;•'.$values['products_name'];
                            }
                             if (isset($values['sku']))
                                $html_cont .= '  ' . $values['sku'];
                            else
                                $html_cont .= '  #' . $values['code'];
                            //desription
                            $html_cont .= '</td><td style="width: 30%;">';
                            if (isset($values['products_name']))
                                $html_cont .= str_replace("\n", "<br />", $values['products_name']);
                            else
                                $html_cont .= 'Empty';

							//clear các dòng phía sau nếu là same product parent
							if(isset($values['same_parent']) && $values['same_parent']==1){
								$html_cont .= '</td><td></td><td></td><td></td><td></td><td class="end"></td></tr>';
								 $line++;
								continue;
							} else {
	                            foreach($arr_option as $k_op => $v_op){
	                            	if(!isset($v_op['line_no'])){
	                            		unset($arr_option[$k_op]);
	                            		continue;
	                            	}
	                            	if($v_op['line_no'] != $values['_id']) continue;
	                            	if(isset($v_op['require']) && $v_op['require']){
	                            		unset($arr_option[$k_op]);
	                            		continue;
	                            	}
	                            	$values['quantity'] = '';
	                            }
							}


							//width
                            $html_cont .= '</td><td style="width: 9%; text-align: right;">';
                            if (isset($values['sizew']) && $values['sizew'] != '' && isset($values['sizew_unit']) && $values['sizew_unit'] != '')
                                $html_cont .= $values['sizew'] . ' (' . $values['sizew_unit'] . ')';
                            else if (isset($values['sizew']) && $values['sizew'] != '')
                                $html_cont .= $values['sizew'] . ' (in.)';
                            else
                                $html_cont .= '';
                            //height
                            $html_cont .= '</td><td style="width: 9%; text-align: right;">';
                            if (isset($values['sizeh']) && $values['sizeh'] != '' && isset($values['sizeh_unit']) && $values['sizeh_unit'] != '')
                                $html_cont .= $values['sizeh'] . ' (' . $values['sizeh_unit'] . ')';
                            else if (isset($values['sizeh']) && $values['sizeh'] != '')
                                $html_cont .= $values['sizeh'] . ' (in.)';
                            else
                                $html_cont .= '';
                            if(!in_array($values['_id'],array(-1,'Extra_Row'))&&isset($values['same_parent']) && $values['same_parent']==0){
                                $html_cont .= '</td><td></td><td align="right">'.$values['quantity'].'</td><td class="end"></td></tr>';
                                $line++;
                                continue;
                            }
                            if(isset($arr_price[$values['_id']])){
                                $values['custom_unit_price'] += $arr_price[$values['_id']]['unit_price'];
                                $values['sub_total'] = str_replace(',', '', (string)$values['sub_total']);
                                $values['sub_total'] += $arr_price[$values['_id']]['sub_total'];
                            }
                            //Unit price
                            $html_cont .= '</td><td style="width: 15%; text-align: right;">';
                            if(isset($arr_price[$values['_id']]))
                                $html_cont .= $this->opm->format_currency($values['sub_total'] / $values['quantity'], 3);
                            else
                                $html_cont .= $this->opm->format_currency($values['custom_unit_price'], 3);
                            //Qty
                            $html_cont .= '</td><td style="width: 10%; text-align: right;">';
                            if (isset($values['quantity']))
                                $html_cont .= $values['quantity'];
                            else
                                $html_cont .= '';
                            //line sub_total
                            $html_cont .= '</td><td style="width: 15%; text-align: right;" class="end">';
                            if (isset($values['sub_total']))
                                $html_cont .= $this->opm->format_currency($values['sub_total']);
                            else
                                $html_cont .= '';
                            $html_cont .= '</td></tr>';
                            $line++;
                        }//end if deleted
                	}//end for
				} else {
					foreach ($line_entry_data['products'] as $values) {
						$keys = $values['_id'];
                        if (isset($values['deleted']) && !$values['deleted']) {
                            if ($line % 2 == 0)
                                $bgs = '#fdfcfa';
                            else
                                $bgs = '#eeeeee';
                            //code
                            $html_cont .= '<tr style="background-color:' . $bgs . ';" nobr="true"><td class="first" style="width: 12%;">';
                            if(isset($values['option_for'])&&is_numeric($values['option_for'])){
                            	$values['sku'] = '';
                            	$values['products_name'] = '&nbsp;&nbsp;&nbsp;•'.$values['products_name'];
                            }
                             if (isset($values['sku']))
                                $html_cont .= '  ' . $values['sku'];
                            else
                                $html_cont .= '  #' . $keys;
                            //desription
                            $html_cont .= '</td><td style="width: 30%;">';
                            if (isset($values['products_name']))
                                $html_cont .= str_replace("\n", "<br />", $values['products_name']);
                            else
                                $html_cont .= 'Empty';

							//clear các dòng phía sau nếu là same product parent
							if(isset($values['same_parent']) && $values['same_parent']==1){
								$html_cont .= '</td><td></td><td></td><td></td><td></td><td class="end"></td></tr>';
								 $line++;
								continue;
							}


							//width
                            $html_cont .= '</td><td style="width: 9%; text-align: right;">';
                            if (isset($values['sizew']) && $values['sizew'] != '' && isset($values['sizew_unit']) && $values['sizew_unit'] != '')
                                $html_cont .= $values['sizew'] . ' (' . $values['sizew_unit'] . ')';
                            else if (isset($values['sizew']) && $values['sizew'] != '')
                                $html_cont .= $values['sizew'] . ' (in.)';
                            else
                                $html_cont .= '';
                            //height
                            $html_cont .= '</td><td style="width: 9%; text-align: right;">';
                            if (isset($values['sizeh']) && $values['sizeh'] != '' && isset($values['sizeh_unit']) && $values['sizeh_unit'] != '')
                                $html_cont .= $values['sizeh'] . ' (' . $values['sizeh_unit'] . ')';
                            else if (isset($values['sizeh']) && $values['sizeh'] != '')
                                $html_cont .= $values['sizeh'] . ' (in.)';
                            else
                                $html_cont .= '';
                            //Unit price
                            $html_cont .= '</td><td style="width: 15%; text-align: right;">';
                            if(!isset($values['company_price_break']) || !$values['company_price_break']) {
                            	if (!isset($values['custom_unit_price']))
	                                $values['custom_unit_price'] = (isset($values['unit_price']) ? $values['unit_price'] : 0);
	                            $html_cont .= $this->opm->format_currency($values['custom_unit_price'], 3);
                            } else {
	                            $html_cont .= $this->opm->format_currency($values['unit_price'], 3);
                            }
                            //Qty
                            $html_cont .= '</td><td style="width: 10%; text-align: right;">';
                            if (isset($values['quantity']))
                                $html_cont .= $values['quantity'];
                            else
                                $html_cont .= '';
                            //line sub_total
                            $html_cont .= '</td><td style="width: 15%; text-align: right;" class="end">';
                            if (isset($values['sub_total']))
                                $html_cont .= $this->opm->format_currency($values['sub_total']);
                            else
                                $html_cont .= '';
                            $html_cont .= '</td></tr>';
                            $line++;
                        }//end if deleted
                	}//end for
				}

				if ($line % 2 == 0) {
					$bgs = '#fdfcfa';
					$bgs2 = '#eeeeee';
				} else {
					$bgs = '#eeeeee';
					$bgs2 = '#fdfcfa';
				}

				$sub_total = $total = $taxtotal = 0.00;
				if (isset($line_entry_data['sum_sub_total']))
					$sub_total = $line_entry_data['sum_sub_total'];
				if (isset($line_entry_data['sum_tax']))
					$taxtotal = $line_entry_data['sum_tax'];
				if (isset($line_entry_data['sum_amount']))
					$total = $line_entry_data['sum_amount'];
				//Sub Total
				$html_cont .= '<tr style="background-color:' . $bgs . ';">
									<td colspan="' . ($colum - 1) . '" style="text-align: right;font-weight:bold;border-top:2px solid #aaa;" class="first">Sub Total:</td>
									<td style="text-align: right;border-top:2px solid #aaa;" class="end">' . $this->opm->format_currency($sub_total) . '</td>
							   </tr>';
				//GST
				$html_cont .= '<tr style="background-color:' . $bgs2 . ';">
									<td colspan="' . ($colum - 1) . '" style="text-align: right;font-weight:bold;" class="first">HST/GST:</td>
									<td align="right" class="end">' . $this->opm->format_currency($taxtotal) . '</td>
							   </tr>';
				//Total
				$html_cont .= '<tr style="background-color:' . $bgs . ';">
									<td colspan="' . ($colum - 1) . '" style="text-align: right;font-weight:bold;" class="first bottom">Total:</td>
									<td align="right" class="end bottom">' . $this->opm->format_currency($total) . '</td>
							   </tr>';
			}//end if

			$this->set('html_cont', $html_cont);
			$this->set('type',$type);
			if (isset($arrtemp['our_csr'])) {
				$this->set('user_name', ' ' . $arrtemp['our_csr']);
			} else
				$this->set('user_name', ' ' . $this->opm->user_name());
			$this->set('qr_image','https://chart.googleapis.com/chart?chs=100x100&cht=qr&chl='.URL.'/salesorders/entry/'.$this->get_id().'&choe=UTF-8');
			//end set content
			//set footer
			$this->set('get_file',$getfile);
			$this->set('is_html',$is_html);
			$this->render('view_pdf');
			if($getfile){
				return $filename.'.pdf';
			} else
				$this->redirect('/upload/' . $filename . '.pdf');
		}
	}

	public function view_pdf($getfile = false, $type = '', $ids = '', $is_html = 0)
	{
	    if ($is_html) {
	        $this->_view_pdf($getfile, $type, $ids, $is_html);
	    } else {
	        if ($type == 'group') $this->export_pdf = true;
	        $this->layout = 'pdf';
	        if (empty($ids)) $ids = $this->get_id();
	        else $this->module_id = $ids;
	        if ($ids != '') {
	            $query = $this->opm->select_one(array(
	                '_id' => new MongoId($ids)
	            ));
	            if (!isset($_GET['print_pdf'])) {
	                $filename = 'SO-' . $query['code'] . (empty($type) ? '-detailed' : ($type == 'group' ? '' : '-' . $type));
	                if ($this->print_pdf(array(
	                    'report_file_name' => $filename,
	                    'report_url' => URL . '/salesorders/view_pdf/' . $getfile . '/' . $type . '/' . $ids,
	            		'custom_footer' => '<li class="footer_li"> </li>',
	            		//'<li class="footer_li">This is an estimate based on the supplied information. Quotation is valid for 30 days.</li>'

	                ))) {
	                    if ($getfile) {
	                        return $filename . '.pdf';
	                    }

	                    $this->redirect(URL . '/upload/' . $filename . '.pdf');
	                }
	                else {
	                    if ($getfile) {
	                        return false;
	                    }

	                    echo 'Please contact IT for this issue.';
	                    die;
	                }
	            }

	            $arrtemp = $query;

	            // customer address

	            $customer = '';
	            if (isset($arrtemp['company_id']) && strlen($arrtemp['company_id']) == 24) $customer.= '<b>' . $this->get_name('Company', $arrtemp['company_id']) . '</b><br />';
	            else
	            if (isset($arrtemp['company_name'])) $customer.= '<b>' . $arrtemp['company_name'] . '</b><br />';
	            if (isset($arrtemp['contact_id']) && strlen($arrtemp['contact_id']) == 24) $customer.= '<p>' . $this->get_name('Contact', $arrtemp['contact_id']) . '</p>';
	            else
	            if (isset($arrtemp['contact_name'])) $customer.= '<p>' . $arrtemp['contact_name'] . '</p>';

	            // loop 2 address

	            $arradd = array(
	                'invoice',
	                'shipping'
	            );
	            foreach($arradd as $vvs) {
	                $kk = $vvs;
	                $customer_address = '';
	                if (isset($arrtemp[$kk . '_address']) && isset($arrtemp[$kk . '_address'][0]) && count($arrtemp[$kk . '_address']) > 0) {
	                    $temp = $arrtemp[$kk . '_address'][0];
	                    if (isset($temp[$kk . '_address_1']) && $temp[$kk . '_address_1'] != '') $customer_address.= $temp[$kk . '_address_1'] . '<br />';
	                    if (isset($temp[$kk . '_address_2']) && $temp[$kk . '_address_2'] != '') $customer_address.= $temp[$kk . '_address_2'] . '<br />';
	                    if (isset($temp[$kk . '_address_3']) && $temp[$kk . '_address_3'] != '') $customer_address.= $temp[$kk . '_address_3'] . '<br />';
	                    if (isset($temp[$kk . '_town_city']) && $temp[$kk . '_town_city'] != '') $customer_address.= $temp[$kk . '_town_city'] . ', ';
	                    if (isset($temp[$kk . '_province_state'])) $customer_address.= ' ' . $temp[$kk . '_province_state'] . ', ';
	                    else
	                    if (isset($temp[$kk . '_province_state_id']) && isset($temp[$kk . '_country_id'])) {
	                        $keytemp = $temp[$kk . '_province_state_id'];
	                        $provkey = $this->province($temp[$kk . '_country_id']);
	                        if (isset($provkey[$temp])) $customer_address.= ' ' . $provkey[$temp] . '<br/>';
	                    }

	                    if (isset($temp[$kk . '_zip_postcode']) && $temp[$kk . '_zip_postcode'] != '') $customer_address.= $temp[$kk . '_zip_postcode'] . '<br/>';
	                    if (isset($temp[$kk . '_country']) && isset($temp[$kk . '_country_id']) && $temp[$kk . '_country_id'] != "CA") $customer_address.= ' ' . $temp[$kk . '_country'] . '<br />';
	                    else $customer_address.= '<br />';
	                    $arr_address[$kk] = $customer_address;
	                }
	            }

	            if (isset($arrtemp['heading']) && $arrtemp['heading'] != '') $arrData['heading'] = $arrtemp['heading'];
	            if (!isset($arr_address['invoice'])) $arr_address['invoice'] = '';
	            if (!empty($customer)) $customer = "<p>{$customer}</p>";
	            $arrData['customer_address'] = $customer . $arr_address['invoice'];
	            if (!isset($arr_address['shipping'])) $arr_address['shipping'] = '';
	            if ($arr_address['shipping'] == '') $arr_address['shipping'] = $arr_address['invoice'];
	            $ship_to = '';
	            if (isset($arrtemp['shipping_address'][0]['shipping_contact_name'])) $ship_to = $arrtemp['shipping_address'][0]['shipping_contact_name'];
	            if ($ship_to == '') $ship_to = $this->get_name('Contact', $arrtemp['contact_id']);
	            if (!empty($ship_to)) $ship_to = "<p>{$ship_to}</p>";
	            $arrData['pdf_name'] = 'Salesorder';
	            $arrData['shipping_address'] = $ship_to . $arr_address['shipping'];
	            $arrData['right_info'] = array(
	                'Sales Order' => $arrtemp['code'],
	                'Job no' => $arrtemp['job_number'],
	                'Date' => $this->opm->format_date($arrtemp['salesorder_date']) ,
	                'Customer PO no' => $arrtemp['customer_po_no'],
	                'A/c no' => '',
	                'Terms' => $arrtemp['payment_terms'],
	                'Required date' => $this->opm->format_date($arrtemp['payment_due_date']) ,
	            );
	            /*             * Ná»™i dung báº£ng giÃ¡ */

	            // comments note

	            if (isset($arrtemp['other_comment'])) $arrData['note'] = nl2br($arrtemp['other_comment']);
	            $html_cont = '';
	            $line_entry_data = $this->line_entry_data('products', 0, '', $ids);
	            $minimum = $this->get_minimum_order('Salesorder', $ids);
	            if ($line_entry_data['sum_sub_total'] < $minimum) {
	                $line_entry_data = $this->get_minimum_order_adjustment($line_entry_data, $minimum, $ids);
	            }

	            if (isset($line_entry_data['products']) && is_array($line_entry_data['products']) && count($line_entry_data['products']) > 0) {
	            	$arrData['content'] = '';
                    $arrData['title'] = array(
                        'SKU',
                        'Description',
                        'Width' => 'text-align: right;',
                        'Height' => 'text-align: right;',
                        'Unit price' => 'text-align: right;',
                        'Qty' => 'text-align: right;',
                        'Line total' => 'text-align: right;'
                    );
	                $options = array();
	                if (isset($arrtemp['options']) && !empty($arrtemp['options'])) $options = $arrtemp['options'];
	                if ($type == 'group') {
	                    $arr_price = array();
	                    foreach($line_entry_data['products'] as $product) {
	                        if (!isset($product['option_for'])) continue;
	                        if (!isset($product['same_parent']) || $product['same_parent'] == 1) continue;
	                        if (!isset($values['custom_unit_price'])) $product['custom_unit_price'] = (isset($product['unit_price']) ? $product['unit_price'] : 0);
	                        if (!isset($arr_price[$product['option_for']])) $arr_price[$product['option_for']]['unit_price'] = $arr_price[$product['option_for']]['sub_total'] = 0;
	                        $product['custom_unit_price'] = (float)str_replace(',', '', $product['custom_unit_price']);
	                        $product['sub_total'] = (float)str_replace(',', '', $product['sub_total']);
	                        $arr_price[$product['option_for']]['unit_price']+= $product['custom_unit_price'];
	                        $arr_price[$product['option_for']]['sub_total']+= $product['sub_total'];
	                    }

	                    foreach($line_entry_data['products'] as $keys => $values) {
	                        if (!isset($values['deleted']) || !$values['deleted']) {
	                            if (isset($values['option_for']) && isset($query['products'][$values['option_for']])) {
	                                $pro = $query['products'][$values['option_for']];
	                                if (isset($pro['sku']) && strpos(str_replace(' ', '', $pro['sku']) , 'DCP-') !== false) continue;
	                            }
	                            if (isset($values['products_name'])) $values['products_name'] = nl2br($values['products_name']);
	                            else $values['products_name'] = 'Empty';
	                            if (isset($values['sku'])) $values['sku'] = '  ' . $values['sku'];
	                            else $values['sku'] = '  #' . $keys;
	                            if (isset($values['sizew']) && $values['sizew'] != '' && isset($values['sizew_unit']) && $values['sizew_unit'] != '') $values['sizew'] = $values['sizew'] . ' (' . $values['sizew_unit'] . ')';
	                            else
	                            if (isset($values['sizew']) && $values['sizew'] != '') $values['sizew'] = $values['sizew'] . ' (in.)';
	                            else $values['sizew'] = '';
	                            if (isset($values['sizeh']) && $values['sizeh'] != '' && isset($values['sizeh_unit']) && $values['sizeh_unit'] != '') $values['sizeh'] = $values['sizeh'] . ' (' . $values['sizeh_unit'] . ')';
	                            else
	                            if (isset($values['sizeh']) && $values['sizeh'] != '') $values['sizeh'] = $values['sizeh'] . ' (in.)';
	                            else $values['sizeh'] = '';
	                            if (isset($arr_price[$values['_id']])) {
	                                $values['custom_unit_price']+= $arr_price[$values['_id']]['unit_price'];
	                                $values['sub_total'] = str_replace(',', '', (string)$values['sub_total']);
	                                $values['sub_total']+= $arr_price[$values['_id']]['sub_total'];
	                            }

	                            if (isset($arr_price[$values['_id']])) $values['unit_price'] = $this->opm->format_currency($values['sub_total'] / $values['quantity'], 3);
	                            else $values['unit_price'] = $this->opm->format_currency(isset($values['custom_unit_price']) ? $values['custom_unit_price'] : 0, 3);
	                            if (isset($values['sub_total'])) $values['sub_total'] = $this->opm->format_currency($values['sub_total']);
	                            else $values['sub_total'] = '';
	                            $arrData['content'].= '<tr>';
	                            if ($values['_id'] === 'Extra_Row' ) {
	                                $arrData['content'].= '
		                                                <td></td>
		                                                <td>' . $values['products_name'] . '</td>
		                                                <td class="right_text"></td>
		                                                <td class="right_text"></td>
		                                                <td></td>
		                                                <td class="right_text">' . $values['quantity'] . '</td>
		                                                <td class="right_text">' . $values['sub_total'] . '</td>
		                                    ';
	                            } else if (isset($values['option_for']) && is_numeric($values['option_for'])) {
	                                if (isset($values['same_parent']) && $values['same_parent']) {
	                                    $arrData['content'].= '
		                                                <td></td>
		                                                <td class="option_product"><span class="bullet"></span>' . $values['products_name'] . '</td>
		                                                <td colspan="5"></td>
		                                    ';
	                                }
	                                else {
	                                    $arrData['content'].= '
		                                                <td></td>
		                                                <td class="option_product"><span class="bullet"></span>' . $values['products_name'] . '</td>
		                                                <td class="right_text"></td>
		                                                <td class="right_text"></td>
		                                                <td class="right_text"></td>
		                                                <td class="right_text">' . $values['quantity'] . '</td>
		                                                <td class="right_text"></td>
		                                    ';
	                                }
	                            }
	                            else {
	                                $arrData['content'].= '
		                                            <td>' . $values['sku'] . '</td>
		                                            <td>' . $values['products_name'] . '</td>
		                                            <td class="right_text">' . $values['sizew'] . '</td>
		                                            <td class="right_text">' . $values['sizeh'] . '</td>
		                                            <td class="right_text">' . $values['unit_price'] . '</td>
		                                            <td class="right_text">' . $values['quantity'] . '</td>
		                                            <td class="right_text">' . $values['sub_total'] . '</td>
		                                ';
	                            }

	                            $arrData['content'].= '</tr>';
	                        } //end if deleted
	                    }
	                }
	                else {
	                    foreach($line_entry_data['products'] as $keys => $values) {
	                        if (!isset($values['deleted']) || !$values['deleted']) {
	                            if (isset($values['products_name'])) $values['products_name'] = nl2br($values['products_name']);
	                            else $values['products_name'] = 'Empty';
	                            if (isset($values['sku'])) $values['sku'] = '  ' . $values['sku'];
	                            else $values['sku'] = '  #' . $keys;
	                            if (isset($values['sizew']) && $values['sizew'] != '' && isset($values['sizew_unit']) && $values['sizew_unit'] != '') $values['sizew'] = $values['sizew'] . ' (' . $values['sizew_unit'] . ')';
	                            else
	                            if (isset($values['sizew']) && $values['sizew'] != '') $values['sizew'] = $values['sizew'] . ' (in.)';
	                            else $values['sizew'] = '';
	                            if (isset($values['sizeh']) && $values['sizeh'] != '' && isset($values['sizeh_unit']) && $values['sizeh_unit'] != '') $values['sizeh'] = $values['sizeh'] . ' (' . $values['sizeh_unit'] . ')';
	                            else
	                            if (isset($values['sizeh']) && $values['sizeh'] != '') $values['sizeh'] = $values['sizeh'] . ' (in.)';
	                            else $values['sizeh'] = '';
	                            if(!isset($values['company_price_break']) || !$values['company_price_break']) {
	                            	if (!isset($values['custom_unit_price']))
		                                $values['custom_unit_price'] = (isset($values['unit_price']) ? $values['unit_price'] : 0);
		                            $values['unit_price'] = $this->opm->format_currency(isset($values['custom_unit_price']) ? $values['custom_unit_price'] : 0, 3);
	                            } else {
		                            $values['unit_price'] = isset($values['custom_unit_price']) ? $this->opm->format_currency($values['custom_unit_price'], 3) : $this->opm->format_currency($values['unit_price'], 3);
	                            }

	                            if (isset($values['sub_total']))
	                            	$values['sub_total'] = $this->opm->format_currency($values['sub_total']);
	                            else
	                            	$values['sub_total'] = '';
	                            $arrData['content'].= '<tr>';
	                            if ($values['_id'] === 'Extra_Row') {
	                                $arrData['content'].= '
		                                                <td></td>
		                                                <td>' . $values['products_name'] . '</td>
		                                                <td class="right_text">' . $values['sizew'] . '</td>
		                                                <td class="right_text">' . $values['sizeh'] . '</td>
		                                                <td></td>
		                                                <td class="right_text">' . $values['quantity'] . '</td>
		                                                <td class="right_text">' . $values['sub_total'] . '</td>
		                                    ';
	                            } else if (isset($values['option_for']) && is_numeric($values['option_for'])) {
		                            if (isset($values['same_parent']) && $values['same_parent']) {
		                                $arrData['content'].= '
								                            <td></td>
								                            <td class="option_product"><span class="bullet"></span>' . $values['products_name'] . '</td>
								                            <td colspan="' . ($type == '' ? 5 : 4) . '"></td>';
		                            }
		                            else {
		                                $arrData['content'].= '
								                            <td></td>
								                            <td class="option_product"><span class="bullet"></span>' . $values['products_name'] . '</td>
								                            <td class="right_text">' . $values['sizew'] . '</td>
								                            <td class="right_text">' . $values['sizeh'] . '</td>
											                <td class="right_text">' . $values['unit_price'] . '</td>
								                            <td class="right_text">' . $values['quantity'] . '</td>
												            <td class="right_text">' . $values['sub_total'] . '</td>';
		                            }
		                        } else {
		                            $arrData['content'].= '
								                        <td>' . $values['sku'] . '</td>
								                        <td>' . $values['products_name'] . '</td>
								                        <td class="right_text">' . $values['sizew'] . '</td>
								                        <td class="right_text">' . $values['sizeh'] . '</td>
								            			<td class="right_text">' . $values['unit_price'] . '</td>
								                        <td class="right_text">' . $values['quantity'] . '</td>
								            			<td class="right_text">' . $values['sub_total'] . '</td>';
		                        }
		                        $arrData['content'].= '</tr>';
	                        }
	                    } //end if deleted
	                } //end for
	            }
	            $sub_total = $total = $taxtotal = 0.00;
                if (isset($line_entry_data['sum_sub_total']))
                    $sub_total = $line_entry_data['sum_sub_total'];
                if (isset($line_entry_data['sum_tax']))
                    $taxtotal = $line_entry_data['sum_tax'];
                if (isset($line_entry_data['sum_amount']))
                    $total = $line_entry_data['sum_amount'];
                //Sub Total
                $arrData['sum_sub_total'] = $this->opm->format_currency($sub_total);
                $arrData['sum_tax'] = $this->opm->format_currency($taxtotal);
                $arrData['sum_amount'] = $this->opm->format_currency($total);
	        }
	        $arrData['render_path'] = '../Elements/view_pdf';
            $this->render_pdf($arrData);
	    }
	}

	//address
	public function set_entry_address($arr_tmp, $arr_set) {
		$address_fset = array('address_1', 'address_2', 'address_3', 'town_city', 'country', 'province_state', 'zip_postcode');
		$address_value = $address_province_id = $address_country_id = $address_province = $address_country = array();
		$address_controller = array('invoice', 'shipping');
		$address_value['invoice'] = $address_value['shipping'] = array('', '', '', '', "CA", '', '');
		$this->set('address_controller', $address_controller); //set
		$address_key = array('invoice', 'shipping');
		$this->set('address_key', $address_key); //set
		$address_country = $this->country();
		$this->set('shipping_contact_name', (isset($arr_tmp['shipping_address'][0]['shipping_contact_name']) ? $arr_tmp['shipping_address'][0]['shipping_contact_name'] : ''));
		foreach ($address_key as $kss => $vss) {
			//neu ton tai address trong data base
			if (isset($arr_tmp[$vss . '_address'][0])) {
				$arr_temp_op = $arr_tmp[$vss . '_address'][0];
				for ($i = 0; $i < count($address_fset); $i++) { //loop field and set value for display
					if (isset($arr_temp_op[$vss . '_' . $address_fset[$i]])) {
						$address_value[$vss][$i] = $arr_temp_op[$vss . '_' . $address_fset[$i]];
					} else {
						$address_value[$vss][$i] = '';
					}
				}
				//get province list and country list

				if (isset($arr_temp_op[$vss . '_country_id']))
					$address_province[$vss] = $this->province($arr_temp_op[$vss . '_country_id']);
				else
					$address_province[$vss] = $this->province();
				//set province
				if (isset($arr_temp_op[$vss . '_province_state_id']) && $arr_temp_op[$vss . '_province_state_id'] != '' && isset($address_province[$vss][$arr_temp_op[$vss . '_province_state_id']]))
					$address_province_id[$kss] = $arr_temp_op[$vss . '_province_state_id'];
				else if (isset($arr_temp_op[$vss . '_province_state']))
					$address_province_id[$kss] = $arr_temp_op[$vss . '_province_state'];
				else
					$address_province_id[$kss] = '';

				//set country
				if (isset($arr_temp_op[$vss . '_country_id'])) {
					$address_country_id[$kss] = $arr_temp_op[$vss . '_country_id'];
					$address_province[$vss] = $this->province($arr_temp_op[$vss . '_country_id']);
				} else {
					$address_country_id[$kss] = "CA";
					$address_province[$vss] = $this->province("CA");
				}

				$address_add[$vss] = '0';
				//chua co address trong data
			} else {
				$address_country_id[$kss] = "CA";
				$address_province[$vss] = $this->province("CA");
				$address_add[$vss] = '1';
			}
		}
		$this->set('address_value', $address_value);
		$address_hidden_field = array('invoice_address', 'shipping_address');
		$this->set('address_hidden_field', $address_hidden_field); //set
		$address_label[0] = $arr_set['field']['panel_2']['invoice_address']['name'];
		$address_label[1] = $arr_set['field']['panel_2']['shipping_address']['name'];
		$this->set('address_label', $address_label); //set
		$address_conner[0]['top'] = 'hgt fixbor';
		$address_conner[0]['bottom'] = 'fixbor2 jt_ppbot';
		$address_conner[1]['top'] = 'hgt';
		$address_conner[1]['bottom'] = 'fixbor3 jt_ppbot';
		$this->set('address_conner', $address_conner); //set
		$this->set('address_country', $address_country); //set
		$this->set('address_country_id', $address_country_id); //set
		$this->set('address_province', $address_province); //set
		$this->set('address_province_id', $address_province_id); //set
		$this->set('address_more_line', 4); //set
		$this->set('address_onchange', "save_address_pr('\"+keys+\"');");
		if (isset($arr_tmp['company_id']) && strlen($arr_tmp['company_id']) == 24)
			$this->set('address_company_id', 'company_id');
		if (isset($arr_tmp['contact_id']) && strlen($arr_tmp['contact_id']) == 24)
			$this->set('address_contact_id', 'contact_id');
		$this->set('address_add', $address_add);
	}

	/**
	 * PHẦN NAM CODE ========================================
	 */
// Popup form orther module
	function popup($key = "") {
		$this->set('key', $key);
        $limit = 100;
        $skip = 0;
        $cond = array();
        $this->identity($cond);
        // Nếu là search GET
        if (!empty($_GET)) {

            $tmp = $this->data;

            if (isset($_GET['company_id'])) {
                $cond['company_id'] = new MongoId($_GET['company_id']);
                $tmp['Quotation']['company'] = $_GET['company_name'];
            }

            $this->data = $tmp;
        }

        // Nếu là search theo phân trang
        $page_num = 1;
        if (isset($_POST['pagination']) && $_POST['pagination']['page-num'] > 0) {
            // $limit = $_POST['pagination']['page-list'];
            $page_num = $_POST['pagination']['page-num'];
            $limit = $_POST['pagination']['page-list'];
            $skip = $limit * ($page_num - 1);
        }
        $this->set('page_num', $page_num);
        $this->set('limit', $limit);
        $arr_order = array('first_name' => 1);
        if (isset($_POST['sort']) && strlen($_POST['sort']['field']) > 0) {
            $sort_type = 1;
            if ($_POST['sort']['type'] == 'desc') {
                $sort_type = -1;
            }
            $arr_order = array($_POST['sort']['field'] => $sort_type);

            $this->set('sort_field', $_POST['sort']['field']);
            $this->set('sort_type', ($sort_type === 1) ? 'asc' : 'desc');
            $this->set('sort_type_change', ($sort_type === 1) ? 'desc' : 'asc');
        }

        // search theo submit $_POST kèm điều kiện
        if (!empty($this->data) && !empty($_POST) && isset($this->data['Salesorder'])) {
            $arr_post = $this->Common->strip_search($this->data['Salesorder']);
            if (isset($arr_post['contact_name']) && strlen($arr_post['contact_name']) > 0) {
                $cond['contact_name'] = new MongoRegex('/' . trim($arr_post['contact_name']) . '/i');
            }

            if (isset($arr_post['company']) && strlen($arr_post['company']) > 0) {
                $cond['company_name'] = new MongoRegex('/' . trim($arr_post['company']) . '/i');
            }
            if (isset($arr_post['code']) && strlen($arr_post['code']) > 0) {
                $cond['code'] = trim($arr_post['code']);
            }
        }
        unset($_GET['_']);
        $cache_key = md5(serialize($_GET));
        $no_cache = true;
        if(empty($_POST) ){
            $arr_salesorders = Cache::read('popup_salesorder_'.$cache_key);
            if($arr_salesorders)
                $no_cache = false;
        }
        $this->selectModel('Salesorder');
        if($no_cache){
	        $arr_salesorders = $this->Somonth->select_all(array(
	            'arr_where' => $cond,
	            'arr_order' => $arr_order,
	            'limit' => $limit,
	            'skip' => $skip,
	            'arr_field' => array('company_id', 'company_name', 'our_rep', 'our_rep_id', 'contact_name','contact_id','salesorder_date','name','heading','status','code','payment_terms','payment_due_date')
	        ));
	        if(empty($_POST))
                Cache::write('popup_salesorder_'.$cache_key,iterator_to_array($arr_salesorders));
        }

        $this->set('arr_salesorders', $arr_salesorders);

        $total_page = $total_record = $total_current = 0;
        if (is_object($arr_salesorders)) {
            $total_current = $arr_salesorders->count(true);
            $total_record = $arr_salesorders->count();
            if ($total_record % $limit != 0) {
                $total_page = floor($total_record / $limit) + 1;
            } else {
                $total_page = $total_record / $limit;
            }
        } else if(is_array($arr_salesorders)){
            $total_current = count($arr_salesorders);
            $total_record = $this->Somonth->count($cond);
            if( $total_record%$limit != 0 ){
                $total_page = floor($total_record/$limit) + 1;
            }else{
                $total_page = $total_record/$limit;
            }
        }
        $this->set('total_current', $total_current);
        $this->set('total_page', $total_page);
        $this->set('total_record', $total_record);

        $this->layout = 'ajax';
	}

	function tasks($salesorder_id) {
		// if(!$this->check_permission('tasks_@_entry_@_view')){
		// 	echo 'You dont have permission to view this tab';die;
		// }

		$arr_order = array('system_default' => -1, 'work_start'=>1);
		if( isset($_POST['field']) && strlen($_POST['field']) > 0 && isset($_POST['type']) && strlen($_POST['type']) > 0 ){
			$arr_order = array( $_POST['field'] => (($_POST['type'] == 'asc')?1:-1 ));
			$this->set('remove_js', 1);
		}
		$order = $this->opm->select_one(array('_id' => new MongoId($salesorder_id)), array('task_date_modified', 'date_modified'));
		if (!isset($order['task_date_modified']) || $order['task_date_modified']->sec < $order['date_modified']->sec) {
			// $this->create_task_default_SO($salesorder_id);
			$this->_create_task_belongto_product($salesorder_id);
			// $this->_create_accountant_task($salesorder_id);
			$order['task_date_modified'] = new MongoDate();
			$this->opm->save($order);
		}

		$this->selectModel('Task');
		$arr_task = $this->Task->select_all(array(
			'arr_where' => array('salesorder_id' => new MongoId($salesorder_id)),
			'arr_order' => $arr_order
		));
		$this->set('arr_task', $arr_task);
		$this->set('salesorder_id', $salesorder_id);
		$this->selectModel('Setting');
		$this->set('arr_tasks_type', $this->Setting->select_option(array('setting_value' => 'tasks_type'), array('option')));
		$this->set('arr_tasks_status', $this->Setting->select_option(array('setting_value' => 'tasks_status'), array('option')));

		$this->selectModel('Noteactivity');
		$this->set('model_noteactivity', $this->Noteactivity);
	}

	function _create_accountant_task($salesorder_id) {
		$this->selectModel('Task');
		$salesorder = $this->opm->select_one(array('_id' => new MongoId($salesorder_id)),array('payment_terms','name'));
		if(!$salesorder['payment_terms']) {
			$count = $this->Task->collection->count(array('type' => 'Accountant','salesorder_id'=>new MongoId($salesorder_id)));
			if(!$count){
				$this->selectModel('Stuffs');
				$accountant = $this->Stuffs->select_one(array('value'=>'Accountant'));
				if(isset($accountant['accountant_id'])){
					$current_date = strtotime(date('Y-m-d H:00:00'));
					$arr_save = array();
					$arr_save['our_rep_type'] = 'contacts';
					$arr_save['salesorder_id'] = new MongoId($salesorder_id);
					$arr_save['type_id'] = '';
					$arr_save['type'] = 'Accountant';
					$arr_save['name'] = (isset($salesorder['name']) ? $salesorder['name'] : '');
					$arr_save['our_rep'] = $accountant['accountant'];
					$arr_save['our_rep_id'] = $accountant['accountant_id'];
					$arr_save['work_start'] = new MongoDate($current_date);
					$arr_save['work_end'] = new MongoDate($current_date + HOUR);
					$this->Task->arr_default_before_save = $arr_save;
					$this->Task->add();
				}
			}
		}
	}

	// hàm này để lưu task mặc định cho SO để track theo due date trên calendar tab Salesorder
	public function create_task_default_SO($salesorder_id){
		// if(!$this->check_permission('tasks_@_entry_@_edit')){
		// 	echo 'You dont have permission to view this tab';die;
		// }

		// Kiem tra xem task default cho SO đã được tạo chưa
		$this->selectModel('Task');
		$arr_tmp = $this->Task->select_one(array('system_default' => true, 'our_rep_type' => 'contacts', 'salesorder_id' => new MongoId($salesorder_id)));
		if( !isset($arr_tmp['_id']) ){
			$arr_save = $this->_get_default($salesorder_id);
			$arr_save['system_default'] = true;
			$arr_save['our_rep_type'] = 'contacts';
			$this->Task->arr_default_before_save = $arr_save;
			if (!$this->Task->add()){
				echo 'Error: save new Task ' . $this->Task->arr_errors_save[1]; die;
			}
		}
	}

	public function _get_default($salesorder_id){
		$this->selectModel('Salesorder');
		$arr_salesorder = $this->Somonth->select_one(array('_id' => new MongoId($salesorder_id)),array('created_by','name','payment_due_date'));
		$arr_save = array();
		$arr_save['salesorder_id'] = new MongoId( $salesorder_id );
		$arr_save['salesorder_name'] = '';
		$arr_save['name'] = (isset($arr_salesorder['name'])?$arr_salesorder['name']:'');
		$this->selectModel('Task');
		$arr_tmp = $this->Task->select_one(array(), array(), array('no' => -1));
		$arr_save['no'] = 1;
		if (isset($arr_tmp['no'])) {
			$arr_save['no'] = $arr_tmp['no'] + 1;
		}
		// +++++++++++++++++
		if( date('i') <= 30 ){
			$arr_save['work_end'] = new MongoDate( $arr_salesorder['payment_due_date']->sec + date('H')*3600 );
			$arr_save['work_start'] = new MongoDate( $arr_save['work_end']->sec - 3600 );
		}else{
			$arr_save['work_end'] = new MongoDate( $arr_salesorder['payment_due_date']->sec + date('H')*3600 + 3600 );
			$arr_save['work_start'] = new MongoDate( $arr_save['work_end']->sec - 3600 );
		}
		$arr_save['status_id'] = $arr_save['status'] = 'New';
		$arr_save['type_id'] = $arr_save['type'] = 'SO';
		$arr_save['our_rep_id'] = new MongoId($arr_salesorder['created_by']);
		$this->selectModel('Contact');
		$contact = $this->Contact->select_one(array('_id'=>$arr_save['our_rep_id']),array('full_name'));
		$arr_save['our_rep'] = isset($contact['full_name']) ? $contact['full_name'] : '';
		return $arr_save;
	}

	// hàm này để kiểm tra xem có product nào được chọn trong line-try của SO mà chưa có tạo task thì tạo task
	function _create_task_belongto_product($salesorder_id){
		$arr_save_orgirin = $this->_get_default($salesorder_id);
		$this->selectModel('Equipment');
		$name = $arr_save_orgirin['name'];
		// $arr_tmp = $this->_get_production($salesorder_id);

		$asset_tag = $this->asset_tags_data($salesorder_id); // hàm function asset_tags_data() này Tùng làm
		$arr_asset = array();
		foreach($asset_tag as $key=>$value){
			if(!isset($value['is_line']) && (!isset($value['deleted']) || !$value['deleted']) ){
				$arr_asset[(string)$value['tag_key']]['tag_name'] = $value['tag'];
				if(!isset($arr_asset[(string)$value['tag_key']]['production_time']))
					$arr_asset[(string)$value['tag_key']]['production_time'] = $value['production_time'];
				else
					$arr_asset[(string)$value['tag_key']]['production_time'] += $value['production_time'];
			}
		}
		$this->selectModel('Task');
		$arr_asset_id = array();
		foreach($arr_asset as $asset_id => $asset){
			$arr_save = $arr_save_orgirin;
			$arr_tmp = $this->Task->select_one(array(
				'our_rep_id'    => new MongoId($asset_id),
				'salesorder_id' => $arr_save['salesorder_id'],
				// 'deleted'       => 'no_search'
			));
			if( isset($arr_tmp['_id']) ){
				$arr_asset_id[] = $arr_tmp['_id'];
				if( !isset($arr_tmp['production_time']) || $asset['production_time'] == $arr_tmp['production_time'] )
					continue; // nếu tồn tại rồi thì không tạo Task nữa
				//Tung, 05/03/2014, chua co thi tim xem da co cai cu ko, co thi continue
				/*$arr_tmp = $this->Task->select_one(array('our_rep_id_old' => new MongoId($asset_id), 'salesorder_id' => $arr_save['salesorder_id'], 'deleted' => 'no_search'));
				if( !isset($arr_tmp['production_time']) || $asset['production_time'] == $arr_tmp['production_time'] )
					continue;
				// chưa có thì tạo mới luôn
				if( isset($arr_save['_id']) )unset($arr_save['_id']);*/
				$arr_save = $arr_tmp;
				$arr_save['production_time'] = $asset['production_time']; // cập nhật production_time mới
				// +++++++++++++++++
				$arr_save['work_start'] = new MongoDate( $arr_save['work_end']->sec - $asset['production_time']*3600 ); // cập nhật thời gian mới khác
				if (!$this->Task->save($arr_save)){
					echo $key.': Error: save new Task ' . $this->Task->arr_errors_save[1]; die;
				}
			}else{
				// chưa có thì tạo mới luôn
				if( isset($arr_save['_id']) )unset($arr_save['_id']);

				// xử lý đối với dữ liệu cũ chưa lưu "line"
				$arr_check_line = $this->Task->select_one(array(
					'our_rep_id'    => new MongoId($asset_id),
					'salesorder_id' => $arr_save['salesorder_id']
				));
				$last_task = $this->Task->select_one(array(), array('no'), array('no' => -1));
				// ----------------------- code cũ trước đây ----------------------------------------
				$arr_save['no']                   = $last_task['no'] + 1;
				$arr_save['our_rep_type']         = 'assets';
				$arr_save['type_id']              = $arr_save['type'] = 'Production';
				$arr_save['name']                 = $name.' - '.$asset['tag_name'];
				$arr_save['our_rep']              = $asset['tag_name'];
				$arr_save['our_rep_id']           = new MongoId($asset_id);
				$arr_save['production_time']      = $asset['production_time']; // lưu lại production_time để biết nếu có thay đổi production_time thì sẽ cập nhật lại task tương ứng
				// +++++++++++++++++ // $arr_save = $this->_get_default($salesorder_id); có default work_end ở trên rồi
				$arr_save['work_start']           = new MongoDate( $arr_save['work_end']->sec - $asset['production_time']*3600 );
				$this->Task->arr_default_before_save = $arr_save;
				if (!$this->Task->add()){
					echo $key.': Error: save new Task ' . $this->Task->arr_errors_save[1]; die;
				}
				// -------- end ---------- code cũ trước đây ----------------------------------------
				$arr_asset_id[] = $this->Task->mongo_id_after_save;
			}
		}
		if(!empty($arr_asset_id)){
			$this->Task->collection->update(
				array(
					'salesorder_id'=>new MongoId($salesorder_id),
					'our_rep_type' => 'assets',
					'deleted' => false,
					'custom_by_user' => array('$exists' => false),
					'_id' => array('$nin' => $arr_asset_id),
					),
				array( '$set' => array('deleted' => true)),
				array('multiple'=>true)
				);
		} else {
			$this->Task->collection->update(
				array(
					'salesorder_id'=>new MongoId($salesorder_id),
					'our_rep_type' => 'assets',
					'custom_by_user' => array('$exists' => false),
					'deleted' => false,
					),
				array( '$set' => array('deleted' => true)),
				array('multiple'=>true)
				);
		}
	}

	// lấy ra danh sách product + time tương ứng với từng dòng trong line-entry của SO
	public function _get_production($id, $check_tag = '')
	{
		if($id=='')
			$id = $this->get_id();
		$group = array();
		$so = $this->opm->select_one(array('_id'=>new MongoId($id)),array('products','asset_tags'));
		$asset_tags = array();
        if(!empty($so['asset_tags'])){
            $asset_tags = $so['asset_tags'];
        }
		if(!empty($so['products']))
		{
			$cal_price = new cal_price;
			$this->selectModel('Product');
			foreach($so['products'] as $product_key=>$product)
			{
				if( isset($product['deleted'])&&$product['deleted'] || !is_object($product['products_id']) ) continue;
				$production = $this->Product->get_product_asset($product['products_id']);
				if(empty($production)) continue;
				foreach($production as $value){
					if(!isset($value['tag']) || isset($value['deleted'])&&$value['deleted']) continue;
					$tag = '(empty)';
					if($value['tag']!='')
						$tag = $value['tag'];
					if(!empty($check_tag) && strcmp($check_tag,$tag) !==0) continue;
					preg_match("/<a ?.*>(.*)<\/a>/", $value['from'], $matches);
                    if(isset($matches[1]))
                        $code = $matches[1];
                    else
                        $code = $value['from'];
                    $key = (isset($product['products_id']) ? $product['products_id'] : '').'_@_'.$product_key.'_@_'.$code.'_@_'.(string)$value['tag_key'];
					$quantity = isset($product['quantity'])&&$product['quantity']!='' ? $product['quantity'] : 0;

					$sizew = isset($product['sizew'])&&$product['sizew']!='' ? $product['sizew'] : 0;
					$sizeh = isset($product['sizeh'])&&$product['sizeh']!='' ? $product['sizeh'] : 0;
					$sizew_unit = isset($product['sizew_unit'])&&$product['sizew_unit']!='' ? $product['sizew_unit'] : 'unit';
					$sizeh_unit = isset($product['sizeh_unit'])&&$product['sizeh_unit']!='' ? $product['sizeh_unit'] : 'unit';

					$factor = isset($value['factor'])&&$value['factor']!='' ? $value['factor'] : 0;
					$total_factor = $quantity*$factor;
					$value['name'] = '';
					$arr_data  = array(
										'product_name'  =>  $value['product_name'],
										'product_id'    =>  $value['_id'],
										'name'          =>  $value['name'],
										'deleted'       =>  $value['deleted'],
										'product_type'  =>  $value['product_type'],
										'sell_by'       =>  strtolower($value['sell_by']),
										'oum'          	=>  $value['oum'],
										'sizew'         =>  $sizew,
										'sizeh'         =>  $sizeh,
										'sizew_unit'    =>  $sizew_unit,
										'line_entry_key'=>  $product_key,
										'sizeh_unit'    =>  $sizeh_unit,
										'quantity'      =>  $quantity,
										'tag_key'     	=>  $value['tag_key'],
										'factor'        =>  $factor,
										'min_of_uom'	=>  $value['min_of_uom'],
										'total_factor'  =>  $total_factor,
									);
					if(!empty($asset_tags)){
                        foreach($asset_tags as $asset_key=>$assettag){
                            if(!isset($assettag['key'])) continue;
                            if($assettag['key']==$key){
                                if(isset($assettag['factor']))
                                    $arr_data['factor'] = (float)$assettag['factor'];
                                if(isset($assettag['min_of_uom']))
                                    $arr_data['min_of_uom'] = (float)$assettag['min_of_uom'];
                                break;
                            }
                        }
                    }
					$arr_data['production_time'] = $this->cal_production_time($arr_data);
					$group[$tag]['product'][] = $arr_data;
				}
			}
		}
		return $group;
	}

	function tasks_auto_save($field = '', $force_update = 0) {
		if(!$this->check_permission('tasks_@_entry_@_edit')){
			echo 'You do not have permission on this action.';
			die;
		}
		$refresh = false;
		$field = str_replace(array('data[Task][', ']'), '', $field);
		$arr_save = $this->data['Task'];

		// kiem tra field update
		if( $field == 'content' ){
			$arr_save_noteactivity = array();
			if( strlen($arr_save['noteactivity_id']) )
				$arr_save_noteactivity['_id'] = $arr_save['noteactivity_id'];
			else{
				$arr_save_noteactivity['type'] = 'Note';
				$arr_save_noteactivity['module'] = 'Task';
				$arr_save_noteactivity['module_id'] = new MongoId($arr_save['_id']);
			}

			$arr_save_noteactivity['content'] = $arr_save['content'];
			$this->selectModel('Noteactivity');
			if ($this->Noteactivity->save($arr_save_noteactivity)) {
				echo 'ok';die;
			} else {
				echo 'Error: ' . $this->Noteactivity->arr_errors_save[1];
			}
			die;
		}else{
			unset($arr_save['noteactivity_id'], $arr_save['content']);
		}

		$work_start = $this->Common->strtotime($arr_save['work_start'] . ' ' . $arr_save['work_start_hour'] . ':00');
		$work_end = $this->Common->strtotime($arr_save['work_end'] . ' ' . $arr_save['work_end_hour'] . ':00');

		$so = $this->Somonth->select_one(array('_id' => new MongoId($arr_save['salesorder_id'])), array('_id','name', 'salesorder_date', 'payment_due_date','status','payment_terms','job_id','asset_tags'));

		// Kiểm tra xem work_start có thay đổi work_start không,
		if ($field != '' && ( $field == 'work_start' || $field == 'work_start_hour' ) ) {
			// nếu có thì có thay đổi đúng không
			if ($work_start < strtotime('now')) {
				echo 'error_work_start';
				die;
			}

			// Kiểm tra salesorder_id
			if ($work_start < $so['salesorder_date']->sec) {
				echo 'work_start_salesorder_date';
				die;
			}

			if ($work_start > ( $so['payment_due_date']->sec + 23*3600 + 1800 ) ) {
				echo 'work_start_due_date';
				die;
			}
			if($field == 'work_start' || $field == 'work_start_hour'   ){
				$this->selectModel('Task');
				if(!isset($_POST['is_multi_task'])){
					$in_task = $this->Task->select_one(array(
											'$or' => array(
													array(
														'work_start' => array('$lt' => new MongoDate($work_start)),
														'work_end' => array('$gt' => new MongoDate($work_start)),
														),
													array(
														'work_start' =>  new MongoDate($work_start),
														'work_end' => new MongoDate($work_start),
														),
													array(
														'work_start' => array('$lt' => new MongoDate($work_end)),
														'work_end' => array('$gt' => new MongoDate($work_end)),
													),
													array(
														'work_start' => new MongoDate($work_start),
														'work_end' =>  new MongoDate($work_end),
													),
												),
											'status' => array('$nin'=>array('Cancelled','DONE')),
											'salesorder_id' => new MongoId($arr_save['salesorder_id']),
											'our_rep_id' => new MongoId($arr_save['our_rep_id']),
											'our_rep_type' => 'contacts',
											'_id' => array('$ne' => new MongoId($arr_save['_id']))
											),array('_id','work_start','work_end'));
					if(!empty($in_task)){
						echo 'multi_task||'.date('H:i', $in_task['work_start']->sec).'||'.date('H:i', $in_task['work_end']->sec);
						die;
					}
				}
				$task = $this->Task->select_one(array('_id' => new MongoId($arr_save['_id'])));
				$production = $this->_get_production($this->get_id(),$task['our_rep']);
				$production_time = 0;
				if(isset($production[$task['our_rep']]['product']) && !empty($production[$task['our_rep']]['product'])){
					foreach($production[$task['our_rep']]['product'] as $value){
						$production_time += $value['production_time'];
					}
				}
				$production_time *= 3600;
				$work_end = $work_start +$production_time;
			}
			/* else {
				if ($work_start > $work_end) {
					$work_end = $work_start + 3600;
				}
			}*/

		}

		// Kiểm tra xem có thay đổi work_end không,
		if ($field != '' && ( $field == 'work_end' || $field == 'work_end_hour' ) ) {
			// nếu có thì có thay đổi đúng không
			if ($work_end < strtotime('now')) {
				echo 'error_work_end';
				die;
			}

			// Kiểm tra xem có thay đổi work_end không,
			if ($work_end < $work_start) {
				echo 'error_time';
				die;
			}

			if ($work_end > ($so['payment_due_date']->sec + 23*3600 + 1800)) {
				echo 'work_end_payment_due_date';
				die;
			}

			$this->selectModel('Task');
			if(!isset($_POST['is_multi_task'])){
				$in_task = $this->Task->select_one(array(
										'$or' => array(
												array(
													'work_start' => array('$lt' => new MongoDate($work_start)),
													'work_end' => array('$gt' => new MongoDate($work_start)),
													),
												array(
													'work_start' =>  new MongoDate($work_start),
													'work_end' => new MongoDate($work_start),
													),
												array(
													'work_start' => array('$lt' => new MongoDate($work_end)),
													'work_end' => array('$gt' => new MongoDate($work_end)),
													),
												array(
														'work_start' => new MongoDate($work_start),
														'work_end' =>  new MongoDate($work_end),
													),
											),
										'status' => array('$nin'=>array('Cancelled','DONE')),
										'salesorder_id' => new MongoId($arr_save['salesorder_id']),
										'our_rep_id' => new MongoId($arr_save['our_rep_id']),
										'our_rep_type' => 'contacts',
										'_id' => array('$ne' => new MongoId($arr_save['_id']))
										),array('_id','work_start','work_end'));
				if(!empty($in_task)){
					echo 'multi_task||'.date('H:i', $in_task['work_start']->sec).'||'.date('H:i', $in_task['work_end']->sec);
					die;
				}
			}
		}
		if($field == 'status' && $arr_save['status'] == 'DONE'){
			$this->selectModel('Task');
			$so_num = $this->Task->count(array(
			                             'salesorder_id'=>new MongoId($arr_save['salesorder_id']),
			                             'status'=>array('$nin'=>array('Cancelled'))
			                             ));
			$completed_so = $this->Task->count(array(
			                             'salesorder_id'=>new MongoId($arr_save['salesorder_id']),
			                             'status'=> 'DONE'
			                             ));
			if($completed_so+1 == $so_num){
				$refresh = true;
				$this->opm->save(array('_id'=>new MongoId($arr_save['salesorder_id']),'status'=>'Completed'));
				if(is_object($so['job_id'])){
					$this->selectModel('Job');
					$salesorder = $this->opm->count(array('job_id'=> new MongoId($so['job_id'])));
					$salesorder_completed = $this->opm->count(array('job_id'=> new MongoId($so['job_id']),'status'=>'Completed'));
					if($salesorder > ($salesorder_completed+1)){
						$this->Session->setFlash('Sales Orders are not Completed all yet. So that Job&#039;s status will not be changed.','default',array('class'=>'flash_message'));
					} else {
						$this->selectModel('Salesinvoice');
						$salesinvoice = $this->Salesinvoice->count(array('job_id'=> new MongoId($so['job_id'])));
						$salesinvoice_invoiced = $this->Salesinvoice->count(array('job_id'=> new MongoId($so['job_id']),'invoice_status'=>'Invoiced'));
						if($salesinvoice > $salesinvoice_invoiced){
							$this->Session->setFlash('Sales Invoices are not Invoiced all yet. So that Job&#039;s status will not be changed.','default',array('class'=>'flash_message'));
						} else {
							$salesorder_sum = $this->opm->sum('sum_amount','tb_salesorder',array('job_id'=>new MongoId($so['job_id']),'deleted'=>false));
							$salesinvoice_sum = $this->Salesinvoice->sum('sum_amount','tb_salesinvoice',array('job_id'=>new MongoId($so['job_id']),'deleted'=>false));
							if(abs(($salesorder_sum - $salesinvoice_sum) / ($salesinvoice_sum==0? 1 :$salesinvoice_sum) ) > 0.001) {
								$this->Session->setFlash('Sales Invoices total: '.$this->opm->format_currency($salesinvoice_sum).'<br />Sales Orders total: '.$this->opm->format_currency($salesorder_sum).'<br/>So that Job&#039;s status will not be changed.','default',array('class'=>'flash_message'));
							} else {
								$this->Job->save(array('_id'=>$so['job_id'],'status'=>'Completed'));
							}
						}
					}
				}
			} else {
				$refresh = true;
			}
			//update asset when $arr_save['status'] == 'Completed'
			if(isset($so['asset_tags']) && count($so['asset_tags'])>0 && isset($arr_save['_id'])){
				$arr_update_asset = array();
				$arr_update_asset['asset_tags'] = $so['asset_tags'];
				foreach ($so['asset_tags'] as $key => $value) {
					if(isset($value['tag_key']) && (string)$value['tag_key']==$arr_save['our_rep_id'])
						$arr_update_asset['asset_tags'][$key]['completed'] = '1';

				}
				if(isset($arr_save['salesorder_id'])){
					$arr_update_asset['_id'] = new MongoId($arr_save['salesorder_id']);
					$this->Somonth->save($arr_update_asset);
				}

			}

		}
		if (strlen(trim($arr_save['our_rep'])) > 0)
			$arr_save['our_rep_id'] = new MongoId($arr_save['our_rep_id']);
		// if(!IS_LOCAL){
		// 	if( date('h', $work_end) == 0 ){
		// 		$work_end = $work_end - 1800;
		// 	}
		// 	if( $work_start == $work_end ){
		// 		$work_start = $work_start - 1800;
		// 	}
		// }
		$arr_save['work_start'] = new MongoDate($work_start);
		$arr_save['work_end'] = new MongoDate($work_end);

		// Kiem tra working hours
		if( $force_update == 0 && $arr_save['our_rep_type'] == 'contacts' ){
			$this->selectModel('Contact');
			$arr_contact_check = $this->Contact->select_one( array('_id' => $arr_save['our_rep_id']), array('_id', 'working_hour') );
			if( isset($arr_contact_check['working_hour']) ){
				$this->_task_check_working_hour($arr_contact_check['working_hour'][date('N', $arr_save['work_start']->sec)], $arr_save);
			}
		}

		unset($arr_save['salesorder_id']);
		// khong save field nay
		$this->selectModel('Task');
		$arr_save['status_id'] = $arr_save['status'];

		$old = $this->Task->select_one(array('_id' => new MongoId($arr_save['_id'])));
		if ((string)$old['our_rep_id'] != $arr_save['our_rep_id']  ){
			if(!isset($old['our_rep_id_old']))//Tung, 05/03/2014 Muc dich them vao de, neu da thay doi 1 lan ko can update lai old_id
				$arr_save['our_rep_id_old'] = $old['our_rep_id'];
			$arr_save['name'] = str_replace($old['our_rep'], $arr_save['our_rep'], $arr_save['name'] ); //update lai name
		}

		if ($this->Task->save($arr_save)){
			if(!$refresh)
				echo 'ok';
			else echo 'refresh';
		}
		else
			echo 'Error: ' . $this->Task->arr_errors_save[1];

		die;
	}

	function _task_check_working_hour($working_hour, $arr_save){

		$day = '2013-12-06 ';
		$time1 = strtotime($day.$working_hour['time1'].':00');
		$time2 = strtotime($day.$working_hour['time2'].':00');

		$check = true;
		if( !($arr_save['work_start']->sec > $time1 && $arr_save['work_end']->sec < $time2) ){
			$check = false;
		}

		if( isset($working_hour['time3']) && isset($working_hour['time4']) ){
			$time3 = strtotime($day.$working_hour['time3'].':00');
			$time4 = strtotime($day.$working_hour['time4'].':00');
			if( !($arr_save['work_start']->sec > $time3 && $arr_save['work_end']->sec < $time4) ){
				$check = false;
			}else{
				$check = true;
			}
		}

		if( isset($working_hour['time5']) && isset($working_hour['time6']) ){
			$time5 = strtotime($day.$working_hour['time5'].':00');
			$time6 = strtotime($day.$working_hour['time6'].':00');
			if( !($arr_save['work_start']->sec > $time5 && $arr_save['work_end']->sec < $time6) ){
				$check = false;
			}else{
				$check = true;
			}
		}

		if( !$check ){
			echo 'out_of_working_hour'; die;
		}
	}

	function tasks_add($salesorder_id, $our_rep_type = 'contacts', $rep_id, $rep) {
		if(!$this->check_permission('tasks_@_entry_@_add')){
			echo 'You do not have permission on this action.';
			die;
		}
		$salesorder_id = $this->get_id();
		$this->selectModel('Salesorder');
		$arr_salesorder = $this->Somonth->select_one(array('_id' => new MongoId($salesorder_id)));

		$arr_save = array();
		$arr_save['our_rep_type'] = $our_rep_type;
		$arr_save['name'] = (isset($arr_salesorder['name'])?$arr_salesorder['name']:'');
		$arr_save['salesorder_id'] = new MongoId($salesorder_id);
		$arr_save['salesorder_name'] = 'nothing is ok';
		$arr_save['our_rep'] = $rep;
		$arr_save['our_rep_id'] = new MongoId($rep_id);

		// Kiểm tra xem ngày SO có lớn hơn ngày của hôm nay không, nếu có thì phải lấy ngày bắt đầu của SO
		$time = strtotime('now');
		if( $arr_salesorder['salesorder_date']->sec > $time ){
			$time = $arr_salesorder['salesorder_date']->sec;
		}

		if (date('i') <= 30) {
			$arr_save['work_start'] = new MongoDate(strtotime(date('Y-m-d H', $time) . ':30:00'));
			$arr_save['work_end'] = new MongoDate(strtotime(date('Y-m-d H', $time) . ':30:00') + 3600);
		} else {
			$arr_save['work_start'] = new MongoDate(strtotime(date('Y-m-d H', $time) . ':00:00') + 3600);
			$arr_save['work_end'] = new MongoDate(strtotime(date('Y-m-d H', $time) . ':00:00') + 7200);
		}

		$arr_save['salesorder_id'] = new MongoId($salesorder_id);
		$arr_save['salesorder_name'] = $arr_salesorder['name'];
		$arr_save['company_name'] = $arr_salesorder['company_name'];
		$arr_save['contact_name'] = $arr_salesorder['contact_name'];
		$arr_save['custom_by_user'] = true;
		$this->selectModel('Task');
		$this->Task->arr_default_before_save = $arr_save;
		if ($id = $this->Task->add()) {
			$this->tasks($salesorder_id);
			$this->render('tasks');
			// $this->redirect('/tasks/entry/' . $this->Task->mongo_id_after_save);
		} else {
			echo 'Error: ' . $this->Task->arr_errors_save[1];
			die;
		}
	}

	function tasks_delete($id) {
		if(!$this->check_permission('tasks_@_entry_@_delete')){
			echo 'You do not have permission on this action.';
			die;
		}
		$arr_save = $this->Task->select_one(array('_id' => new MongoId($id)));
		$arr_save['deleted'] = true;
		$this->selectModel('Task');
		if ($this->Task->save($arr_save))
			echo 'ok';
		else
			echo 'Error: ' . $this->Task->arr_errors_save[1];
		die;
	}
	function general_auto_save($id) {
		$arr_save=array();
		if (!empty($_POST)) {
			$arr_save['_id'] = new MongoId($id);
			$arr_save['other_comment'] = $_POST['content'];
			$error = 0;

			if (!$error) {
				if ($this->opm->save($arr_save)) {
					echo 'ok';
				} else {
					echo 'Error: ' . $this->opm->arr_errors_save[1];
				}
			}
		}
		die;
	}


	function delete_all_associate($idopt='',$key=''){
		if($key=='products'){ // update cac line entry option cua products
			$this->Session->write($this->name.'ViewAssetTag','');
			$ids = $this->get_id();
			if($ids!=''){
				$arr_insert = $line_entry = array();
				//lay note products hien co
				$query = $this->opm->select_one(array('_id'=>new MongoId($ids)),array('products','options'));
				if(isset($query['products']) && is_array($query['products']) && !empty($query['products'])){
					$line_entry = $query['products'];
					$line_entry[$idopt] =  array('deleted'=>true);
					foreach($query['products'] as $keys=>$values){
						if(isset($values['option_for']) && $values['option_for']==$idopt){
                            $line_entry[$keys] = array('deleted'=>true);
						}
					}
				}
				if(isset($query['options']) && is_array($query['options']) && !empty($query['options'])){
					$options = $query['options'];
					foreach($options as $key=>$option){
						if(!isset($option['parent_line_no'])) continue;
						if($option['parent_line_no']!= $idopt) continue;
						$options[$key] = array('deleted'=>true);
					}
					$arr_insert['options'] = $options;
				}
				$arr_insert['products'] = $line_entry;//pr($line_entry);die;
				$arr_insert['_id'] 		= new MongoId($ids);
				$arr_insert = array_merge($arr_insert,$this->new_cal_sum($line_entry));
				$this->opm->save($arr_insert);
			}
		}else if($key=='costing'){
			$ids = $this->get_id(); $items = ''; $total_area = $total_unit = 0;
			if($ids!=''){
				$salesorder = $this->opm->select_one(array('_id'=>new MongoId($ids)),array('costing','products'));
				//lấy id opt của line entry
				if(isset($salesorder['costing']) &&  isset($salesorder['costing'][$idopt]) &&  isset($salesorder['costing'][$idopt]['for_line'])){
					$items = $salesorder['costing'][$idopt]['for_line'];
					$salesorder['costing'][$idopt]['deleted'] = true;
				}
				// cap nhat lai gia cho line entry
				if(isset($salesorder['products'][$items]) && $items!=''){
					$arr_return['products'] = $salesorder['products'];
					$update = $salesorder['products'][$items];
					//lap vong de tinh total
					foreach($salesorder['costing'] as $kks=>$vvs){
						if(isset($vvs['deleted']) && !$vvs['deleted'] && isset($vvs['for_line']) && $vvs['for_line']==$items){
							if(isset($vvs['sell_by']) && $vvs['sell_by']=='area'){
								$total_area += (float)$vvs['sub_total'];
							}elseif(isset($vvs['sell_by']) && $vvs['sell_by']=='unit'){
								$total_unit += (float)$vvs['sub_total'];
							}
						}

					}
					//tinh lai sell_price
					if(isset($update['sell_by']) && $update['sell_by'] =='area'){
						$update['sell_price'] = $total_area;
						$update['plus_unit_price'] = $total_unit;
						$cal_price = new cal_price;
						$cal_price->arr_product_items = $update;
						$cal_price->field_change = 'plus_unit_price';
						$cal_price->cal_price_items();
						$update = $cal_price->arr_product_items;

					}elseif(isset($update['sell_by']) && $update['sell_by'] =='unit'){
						$update['sell_price'] = $total_area;
						$update['plus_unit_price'] = $total_unit;
						$cal_price = new cal_price;
						$cal_price->arr_product_items = $update;
						$cal_price->field_change = 'plus_unit_price';
						$cal_price->cal_price_items();
						$update = $cal_price->arr_product_items;
					}

					$arr_return['products'][$items] = $update;
					$arr_return['_id'] = new MongoId($ids);
					$this->opm->save($arr_return);

				}
			}
		}else if($key=='options'){
            $id = $this->get_id();
            $query = $this->opm->select_one(array('_id'=>new MongoId($id)),array('products','options'));
            if(isset($query['options'][$idopt]['line_no'])&&$query['options'][$idopt]['line_no']!=''){
                $line_no = $query['options'][$idopt]['line_no'];
                $parent_no = $query['options'][$idopt]['parent_line_no'];
                $query['options'][$idopt] = array('deleted'=> true);
                if(isset($query['products'][$line_no])){
                    $query['products'][$line_no] = array('deleted'=> true);
                    if(isset($query['products'][$line_no]['same_parent'])&&$query['products'][$line_no]['same_parent']==1){
                    	$this->opm->save($query);
                        $this->cal_price_line(array('data'=>array('id'=>$parent_no),'fieldchange'=>''));
                    } else {
	                    $query = array_merge($query,$this->new_cal_sum($query['products']));
	                    $this->opm->save($query);
                    }
                }
            }
        }else if(empty($key)){
			// Delete all Tasks
			$this->selectModel('Task');
			if( !$this->Task->update_all( array('salesorder_id' => new MongoId($this->get_id())), array( 'deleted' => true ) ) ){
				echo 'Error: ' . $this->Task->arr_errors_save[1]; die;
			}
        }
		return true;
	}

	// $arr_where['salesorder_name'] = array('$ne' => '');
	// ================================== CALENDAR ====================================
	public function calendar($type = 'contacts'){
		$this->set('set_footer', '../Salesorders/calendar_footer');
		$this->Session->write('calendar_last_visit', '/' . $this->request->url);

		$arr_option = $this->get_option_status_color('tasks_status');
		$arr_status = $arr_option[0];
		$arr_status_color = $arr_option[1];

		$this->set( 'arr_status', $arr_status );
		$this->set( 'arr_status_color', $arr_status_color );

		// Nếu task đang xem là Contact
		if( $type == 'contacts' ){

			$this->selectModel('Contact');
			$arr_employees = $this->Contact->select_all(array(
				'arr_where' => array('is_employee' => 1),
				'arr_field' => array('_id', 'first_name', 'last_name', 'color'),
				'arr_order' => array('first_name' => 1),
			));
			// $this->set( 'arr_employees', $arr_employees );
			$this->set( 'arr_responsible_task', $arr_employees );
		}

		// Nếu task đang xem là Equipment
		if( $type == 'assets' ){

			$this->selectModel('Equipment');
			$arr_equipment = $this->Equipment->select_all( array(
				'arr_field' => array('_id', 'name'),
				'arr_order' => array('name' => 1)
			));

			$arr_tmp = array();
			foreach ($arr_equipment as $value) {
				$arr_tmp[(string)$value['_id']] = $value['name'];
			}
			// $this->set('arr_asset', $arr_tmp);
			$this->set( 'arr_responsible_task', $arr_tmp );
		}

		$this->set('type', $type);
		$this->layout = 'calendar';
	}


	public function calendar_json($type = 'contacts', $date_from = '', $date_to = '', $get_more = 'no'){

		$arr_option = $this->get_option_status_color('tasks_status');
		$arr_status = $arr_option[0];
		$arr_status_color = $arr_option[1];
		$arr_status_move = $arr_option[2];

		// kiem tra xem co phai la load them data vao Calendar View khong
		$arr_where = array('our_rep_type' => $type);
		$arr_where['$or'] = array(
			array(
				'work_start' => array( '$lte' => new MongoDate(strtotime($date_from)) ),
				'work_end' => array( '$gte' => new MongoDate(strtotime($date_from)))
			),
			array(
				'work_start' => array( '$lte' => new MongoDate(strtotime($date_to)) ),
				'work_end' => array( '$gte' => new MongoDate(strtotime($date_to)))
			),
			array(
				'work_start' => array( '$gte' => new MongoDate(strtotime($date_from)) ),
				'work_end' => array( '$lte' => new MongoDate(strtotime($date_to)))
			)
		);

		$arr_where['salesorder_id'] = array('$ne' => '');

		if( $type == 'contacts' )
			$arr_where['system_default'] = true;

		$this->selectModel('Task');
		$arr_tasks = $this->Task->select_all(array(
			'arr_where' => $arr_where,
		));

		// parse like a data jsonp to client
		$str = '<data>';
		$this->selectModel('Contact');
		$this->selectModel('Salesorder');
		$arr_employee_color = array();
		foreach ($arr_tasks as $value) {
			if (isset($value['salesorder_id']) && is_object($value['salesorder_id'])) {
				$tmp_salesorder = $this->Somonth->select_one(array('_id' => $value['salesorder_id']), array('_id', 'name', 'our_rep', 'our_csr'));
				if (isset($tmp_salesorder['_id'])) {
					$salesorder_id = $tmp_salesorder['_id'];
					$salesorder = $tmp_salesorder['name'];
					$salesorder_our_rep = $tmp_salesorder['our_rep'];
					$salesorder_our_csr = $tmp_salesorder['our_csr'];
				}else{
					continue;
				}
			}

			$str .= '<event';
			$str .= ' id="' . $value['_id'] . '"';
			$str .= ' rep_id="' . $value['our_rep_id'] . '"';
			$str .= ' status="' . $value['status_id'] . '"';

			// Nếu sales order bị trễ thì chuyển sang màu đỏ
			if ($value['status'] == 'DONE'){
				$str .= ' color="black"';
			}elseif ($value['work_end']->sec < strtotime('now') && $value['status'] != 'DONE'){
				$str .= ' color="red"';
			}elseif ($value['status_id'] == 'New'){
				// $str .= ' color="green"';
				if($type == 'contacts') {
					if(!isset($arr_employee_color[(string)$value['our_rep_id']])) {
						$employee = $this->Contact->select_one(array('_id' => new MongoId($value['our_rep_id'])),array('color'));
						$arr_employee_color[(string)$value['our_rep_id']] = $employee['color'];
					}
					$str .= ' color="' . $arr_employee_color[(string)$value['our_rep_id']] . '"';
				}
			}elseif ($value['status_id'] == 'Confirmed'){
				$str .= ' color="blue"';
			}elseif ($value['status_id'] == 'On Hold'){
				$str .= ' color="#FFA500"';
			}else{
				if($type == 'contacts') {
					if(!isset($arr_employee_color[(string)$value['our_rep_id']])) {
						$employee = $this->Contact->select_one(array('_id' => new MongoId($value['our_rep_id'])),array('color'));
						$arr_employee_color[(string)$value['our_rep_id']] = $employee['color'];
					}
					$str .= ' color="' . $arr_employee_color[(string)$value['our_rep_id']] . '"';
				} else
					$str .= ' color="' . $arr_status_color[$value['status_id']] . '"';
			}

			if (isset($arr_status_move[$value['status_id']]) && $arr_status_move[$value['status_id']]) {
				$str .= ' can_move="1"';
			} else {
				$str .= ' can_move="0"';
			}

			// insert them du lieu vao data view cua calendar
			if ($get_more == 'yes') {
				$str .= ' type="inserted"';
			}

			$str .= ' text_so_id="' . $salesorder_id . '"';
			$str .= ' text_so="' . str_replace('&', "_1_", $salesorder) . '"';
			$str .= ' text_job="' . ( isset($value['job_name']) ? $value['job_name'] : '' ) . '"';
			$str .= ' text_responsible="' . $value['our_rep'] . '"';
			$str .= ' text_so_rep="' . $salesorder_our_rep . '"';
			$str .= ' text_so_csr="' . $salesorder_our_csr . '"';
			$str .= ' text_status="' . $value['status'] . '"';
			$str .= ' text_type="' . ( isset($value['type']) ? $value['type'] : '' ) . '"';
			$str .= ' text_contact_name="' . ( isset($value['contact_name']) ? $value['contact_name'] : '' ) . '"';

			if( date("H", $value['work_end']->sec) >= 7 ){
				if( isset($value['system_default']) ){
					$str .= ' start_date="' . date('Y-m-d H:i', ($value['work_end']->sec - 3600) ) . '"';
				}else{
					$str .= ' start_date="' . date('Y-m-d H:i', $value['work_start']->sec ) . '"';
				}
				$str .= ' end_date="' . date('Y-m-d H:i', $value['work_end']->sec) . '"';
			}else{
				if( isset($value['system_default']) ){
					$str .= ' start_date="' . date('Y-m-d', $value['work_end']->sec ).' 08:00' . '"';
				}else{
					$str .= ' start_date="' . date('Y-m-d', $value['work_end']->sec ).' 08:00' . '"';
				}
				$str .= ' end_date="' . date('Y-m-d', $value['work_end']->sec).' 09:00' . '"';
			}
			$str .= ' text="' . str_replace('&', "_1_", $value['name']) . '">';
			$str .= '</event>';
		}
		$str .= '</data>';
		echo $str;
		// echo json_encode($arr_tmp);
		die;
	}

	public function calendar_change() {
		$arr_post_data = $this->data;
		$arr_save['work_start'] = new MongoDate(strtotime($arr_post_data['work_start']));
		$arr_save['work_end'] = new MongoDate(strtotime($arr_post_data['work_end']));

		$this->selectModel('Task');
		$this->selectModel('Salesorder');

		// Kiểm tra salesorder_id
		$task = $this->Task->select_one(array('_id' => new MongoId($arr_post_data['id'])), array('_id', 'salesorder_id'));
		$so = $this->Somonth->select_one(array('_id' => new MongoId($task['salesorder_id'])), array('_id', 'salesorder_date', 'payment_due_date'));

		if ($arr_save['work_start']->sec < $so['salesorder_date']->sec) {
			echo 'Work start can not less than Order date of SO';
			die;
		}
		if ($arr_save['work_end']->sec > ($so['payment_due_date']->sec + 23*3600 + 1800)) {
			echo 'Work End can not greater than Due date of this SO';
			die;
		}

		$arr_save['_id'] = $arr_post_data['id'];

		if ($this->Task->save($arr_save)) {
			echo 'ok';
		} else {
			echo 'Error: ' . $this->Task->arr_errors_save[1];
		}
		die;
	}

	// $id = id quotation;
	public function create_sale_order($id = '') {
		if(!$this->check_permission('salesorders_@_entry_@_add')){
			echo 'You do not have permission on this action.';
			die;
		}
		$id = new MongoId($id);
		$this->selectModel('Quotation');
		// khai bao data la object;
		$data = (object) array();
		// get quotation by id
		$quotation = $this->Quotation->select_one(array('_id' => $id));
		// chuyen quotation sang object
		$quotation = (object) $quotation;
		// check quotation exiting
		if ($quotation) {
			$data->code = $this->opm->get_auto_code('code');

			// $this->opm->arr_default_before_save
			$arr_default_before_save['company_id'] = $quotation->company_id;
			$arr_default_before_save['company_name'] = $quotation->company_name;
			$arr_default_before_save['contact_id'] = $quotation->contact_id;
			$arr_default_before_save['contact_name'] = $quotation->contact_name;
			$arr_default_before_save['customer_po_no'] = $quotation->customer_po_no;
			$arr_default_before_save['description'] = $quotation->description;
			$arr_default_before_save['email'] = $quotation->email;
			$arr_default_before_save['invoice_address'] = $quotation->invoice_address;
			$arr_default_before_save['job_id'] = $quotation->job_id;
			$arr_default_before_save['job_name'] = $quotation->job_name;
			$arr_default_before_save['job_number'] = $quotation->job_number;
			$arr_default_before_save['name'] = $quotation->name;
			$arr_default_before_save['our_csr'] = $quotation->our_csr;
			$arr_default_before_save['our_csr_id'] = $quotation->our_csr_id;
			$arr_default_before_save['our_rep'] = $quotation->our_rep;
			$arr_default_before_save['our_rep_id'] = $quotation->our_rep_id;
			$arr_default_before_save['payment_terms'] = $quotation->payment_terms;
			$arr_default_before_save['phone'] = $quotation->phone;
			$arr_default_before_save['products'] = $quotation->products;
			$arr_default_before_save['quotation_id'] = $quotation->_id;
			$arr_default_before_save['quotation_name'] = (isset($quotation->name)&&$quotation->name!=''?$quotation->name:(isset($quotation->company_name) ? $quotation->company_name : '' ));
			$arr_default_before_save['quotation_number'] = $quotation->code;
			$arr_default_before_save['quotation_id'] = $quotation->_id;
			$arr_default_before_save['sales_order_type'] = 'Sales Order';
			$arr_default_before_save['shipping_address'] = $quotation->shipping_address;
			$arr_default_before_save['status'] = 'New';
			$arr_default_before_save['sales_order_type'] = 'Sales Order';

			$arr_default_before_save['sum_amount'] = isset($quotation->sum_amount)?$quotation->sum_amount:'';
			$arr_default_before_save['sum_sub_total'] = isset($quotation->sum_sub_total)?$quotation->sum_sub_total:'';
			$arr_default_before_save['sum_tax'] = isset($quotation->sum_tax)?$quotation->sum_tax:'';
			$arr_default_before_save['tax'] = isset($quotation->tax)?$quotation->tax:'';
			$arr_default_before_save['taxval'] = isset($quotation->taxval)?$quotation->taxval:'';
			$arr_default_before_save['sales_order_type'] = 'Sales Order';

			$this->Somonth->arr_default_before_save = $arr_default_before_save;

			$this->selectModel('Salesorder');
			// save sale order success
			if ($this->Somonth->add()) {
				// convert $data array to object
				$data = (object) $data;
				// return id sale order after save;
				$return_id = $this->Somonth->mongo_id_after_save;
				// update quotation.
				$data_quotation = $quotation;
				$data_quotation->quotation_status = 'Approved';
				$data_quotation->salesorder_id = $return_id;
				$data_quotation->salesorder_number = $data->code;
				// covert $data_quotation object to array
				$data_quotation = (array) $data_quotation;
				// update quotation success
				if ($this->Quotation->save($data_quotation)) {
					$this->redirect('entry/' . $return_id);
				} else {
					echo 'Error: ' . $this->Quotation->arr_errors_save[1];
				}
			} else {
				echo 'Error: ' . $this->Somonth->arr_errors_save[1];
			}
		}
		die();
	}




	//
	//  Tung Report
	//

	public function report_pdf($data)
	{

		App::import('Vendor','xtcpdf');
		$pdf = new XTCPDF();
		$textfont = 'freesans'; // looks better, finer, and more condensed than 'dejavusans'

		// set document information
		$pdf->SetCreator(PDF_CREATOR);
		$pdf->SetAuthor('Anvy Digital');
		$pdf->SetTitle('Anvy Digital Salesorder');
		$pdf->SetSubject('Salesorder');
		$pdf->SetKeywords('Salesorder, PDF');

		// set default header data
		$pdf->setPrintHeader(false);
		$pdf->setPrintFooter(true);

		// set default monospaced font
		$pdf->SetDefaultMonospacedFont(2);

		// set margins
		$pdf->SetMargins(10, 3, 10);
		$pdf->SetHeaderMargin(PDF_MARGIN_HEADER);
		$pdf->SetFooterMargin(PDF_MARGIN_FOOTER);

		// set auto page breaks
		$pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);

		// set image scale factor
		$pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);

		// set some language-dependent strings (optional)
		if (@file_exists(dirname(__FILE__).'/lang/eng.php')) {
			require_once(dirname(__FILE__).'/lang/eng.php');
			$pdf->setLanguageArray($l);
		}

		// ---------------------------------------------------------

		// set font
		$pdf->SetFont($textfont, '', 9);

		// add a page
		$pdf->AddPage();


		// writeHTML($html, $ln=true, $fill=false, $reseth=false, $cell=false, $align='')
		// writeHTMLCell($w, $h, $x, $y, $html='', $border=0, $ln=0, $fill=0, $reseth=true, $align='', $autopadding=true)

		// create some HTML content


		$html = '
		<table cellpadding="2" cellspacing="0" style="width:100%; margin: 0px auto">
		   <tbody>
			  <tr>
				 <td width="32%" valign="top" style="color:#1f1f1f;">
					<img src="img/logo_anvy.jpg" alt="" margin-bottom:0px>
					<p style="margin-bottom:5px; margin-top:0px;">3145 - 5th Ave NE<br/ >Calgary  AB  T2A  6A3</p>
				 </td>
				 <td width="68%" valign="top" align="right">
					<table>
					   <tbody>
						  <tr>
							 <td width="20%">&nbsp;</td>
							 <td width="80%">
								<span style="text-align:right; font-size:21px; font-weight:bold; color: #919295;">
									'.$data['title'].'<br />';
		if(isset($data['date_equals']))
		  $date = '<span style="font-size:12px; font-weight:normal">'.$data['date_equals'].'</span>';
		else
		{
			if(isset($data['date_from'])&&isset($data['date_to']))
			  $date = '<span style="font-size:12px; font-weight:normal">( '.$data['date_from'].' - '.$data['date_to'].' )</span>';
			else if(isset($data['date_from']))
			  $date = '<span style="font-size:12px; font-weight:normal">From '.$data['date_from'].'</span>';
			else if(isset($data['date_to']))
			  $date = '<span style="font-size:12px; font-weight:normal">To '.$data['date_to'].'</span>';
			else
			  $date = '';
		}
		$html .= $date;
		$html .=                    '
								</span>
								<div style=" border-bottom: 1px solid #cbcbcb;height:5px">&nbsp;</div>
							 </td>
						  </tr>
						  <tr>
							 <td colspan="2">
									<span style="font-weight:bold;">Printed at: </span>'.$data['current_time'].'
							 </td>
						  </tr>
					   </tbody>
					</table>
				 </td>
			  </tr>
		   </tbody>
		</table>
		<div class="option">'.@$data['heading'].'</div>
		<br />
		<br />
		<div style="border-bottom: 1px dashed #9f9f9f; height:1px; clear:both"></div>
		<br />
		<style>
		   td{
		   line-height:2px;
		   }
		   td.first{
			text-align: center;
		   border-left:1px solid #e5e4e3;
		   }
		   td.end{
		   border-right:1px solid #e5e4e3;
		   }
		   td.top{
		   color:#fff;
		   text-align: center;
		   font-weight:bold;
		   background-color:#911b12;
		   border-top:1px solid #e5e4e3;
		   }
		   td.bottom{
		   border-bottom:1px solid #e5e4e3;
		   }
		   td.content{
			border-right: 1px solid #E5E4E3;
			text-align: center;
		   }
		   .option{
		   color: #3d3d3d;
		   font-weight:bold;
		   font-size:20px;
		   text-align: center;
		   width:100%;
		   }
		   table.maintb{
		   }
		</style>
		<br />
		';
		$html .= $data['html_loop'];

		$pdf->writeHTML($html, true, false, true, false, '');



		// reset pointer to the last page
		$pdf->lastPage();



		// ---------------------------------------------------------
		// Close and output PDF document
		// This method has several options, check the source code documentation for more information.
		//$pdf->Output('example_001.pdf', 'I');




		$pdf->Output('upload/'.$data['filename'].'.pdf', 'F');


	}
	public function option_summary_customer_find($type = ''){
		if(!$this->check_permission($this->name.'_@_options_@_report_by_customer_summary'))
			$this->error_auth();
		$arr_data['salesorders_status'] = $this->Setting->select_option_vl(array('setting_value'=>'salesorders_status'));
		$arr_data['salesorders_type'] = $this->Setting->select_option_vl(array('setting_value'=>'salesorders_type'));
		$this->set('arr_data',$arr_data);
        $report_type = 'summary';
		if($type=='area')
            $report_type = 'area_summary';
        $this->set('report_type',$report_type);
	}
	public function option_detailed_customer_find($type = ''){
		if(!$this->check_permission($this->name.'_@_options_@_report_by_customer_detailed'))
			$this->error_auth();
		$arr_data['salesorders_status'] = $this->Setting->select_option_vl(array('setting_value'=>'salesorders_status'));
		$arr_data['salesorders_type'] = $this->Setting->select_option_vl(array('setting_value'=>'salesorders_type'));

		$this->set('arr_data',$arr_data);
		$report_type = 'detailed';
        if($type=='area')
            $report_type = 'area_detailed';
        $this->set('report_type',$report_type);
	}
	public function customer_report($type = ''){
		$arr_data = array();
        if(isset($_GET['print_pdf'])){
            $arr_data = Cache::read('salesorders_customer_report_'.$type);
            Cache::delete('salesorders_customer_report_'.$type);
        } else {
			if(isset($_POST) && !empty($_POST)){
				$arr_post = $_POST;
	            $arr_post = $this->Common->strip_search($arr_post);
                $arr_where = array('company_id'=>array('$exists' => true,'$ne'=>''));
                $arr_where['deleted'] = false;
	            if(!$this->check_permission('salesorders_@_view_worktraq_@_view')){
	            	$arr_where['code'] = array('$not' => new MongoRegex('/WT-/'));
	            }
				if(isset($arr_post['status'])&&$arr_post['status']!='')
					$arr_where['status'] = $arr_post['status'];
				//Check loại trừ cancel thì bỏ các status bên dưới
				if(isset($arr_post['is_not_cancel']) && $arr_post['is_not_cancel']==1){
					// $arr_where['status'] = array('$nin'=> array('Completed','Cancelled'));
					//Tuy nhiên nếu ở ngoài combobox nếu có chọn, thì ưu tiên nó, set status lại
					if(isset($arr_post['status'])&&$arr_post['status']!='')
						$arr_where['status'] = $arr_post['status'];

				}
				if(isset($arr_post['company']) && $arr_post['company']!='')
					$arr_where['company_name'] = new MongoRegex('/'.trim($arr_post['company']).'/i');
				if(isset($arr_post['contact']) && $arr_post['contact']!='')
					$arr_where['contact_name'] = new MongoRegex('/'.trim($arr_post['contact']).'/i');
				if(isset($arr_post['job_no']) && $arr_post['job_no']!='')
					$arr_where['job_number'] = new MongoRegex('/'.trim($arr_post['job_no']).'/i');
				//tim chinh xac ngay
				if(isset($arr_post['date_equals']) && $arr_post['date_equals']!=''){
					$arr_where['salesorder_date']['$gte'] = new MongoDate($this->Common->strtotime($arr_post['date_equals'] . '00:00:00'));
					$arr_where['salesorder_date']['$lt'] = new MongoDate($this->Common->strtotime($arr_post['date_equals'] . '23:59:59'));
				} else { //ngay nam trong khoang
					//neu chi nhap date from
					if(isset($arr_post['date_from']) && $arr_post['date_from']){
						$arr_where['salesorder_date']['$gte'] = new MongoDate($this->Common->strtotime($arr_post['date_from'] . '00:00:00'));
					}
					//neu chi nhap date to
					if( isset($arr_post['date_to']) && $arr_post['date_to'] ){
						$arr_where['salesorder_date']['$lte'] = new MongoDate($this->Common->strtotime($arr_post['date_to'] . '23:59:59'));
					}
				}
				if(isset($arr_post['our_rep']) && $arr_post['our_rep']!='')
					$arr_where['our_rep'] = new MongoRegex('/'.$arr_post['our_rep'].'/i');
				if(isset($arr_post['our_csr']) && $arr_post['our_csr']!='')
					$arr_where['our_csr'] = new MongoRegex('/'.$arr_post['our_csr'].'/i');
				$this->selectModel('Salesorder');
				//lay het salesorder, voi where nhu tren va lay sum_amount giam dan
				/*$salesorder = $this->Somonth->select_all(array(
						'arr_where'=>$arr_where,
						'arr_order'=>array(
										'sum_sub_total'=>-1
										),
						'arr_field'=>array('code','sales_order_type','salesorder_date','heading','status','our_rep','sum_sub_total','company_id','company_name'),
						'limit' => 99999999
					));*/
                $count = $this->Somonth->count($arr_where,array('limit' => 9999999));
				if($count == 0) {
					echo 'empty';
					die;
				} else {
					$minimum = 50;
	                $this->selectModel('Stuffs');
	                $product = $this->Stuffs->select_one(array('value'=>"Minimum Order Adjustment"),array('product_id'));
	                if(isset($product['product_id'])&&is_object($product['product_id'])){
	                    $this->selectModel('Product');
	                    $product = $this->Product->select_one(array('_id'=> new MongoId($product['product_id'])),array('sell_price'));
	                    $minimum = $product['sell_price'];
	                }

	                if ($arr_post['report_type'] == 'summary')
                        $arr_data = $this->summary_customer_report($arr_post, $arr_where,$minimum,$product['_id']);
					else if ($arr_post['report_type'] == 'detailed')
	                    $arr_data = $this->detailed_customer_report($arr_post, $arr_where,$minimum,$product['_id']);
	                else if ($arr_post['report_type'] == 'area_summary')
                        $arr_data = $this->summary_area_customer_report($arr_post, $arr_where,$minimum,$product['_id']);
                    else if ($arr_post['report_type'] == 'area_detailed')
                        $arr_data = $this->detailed_area_customer_report($arr_post, $arr_where,$minimum,$product['_id']);
	                Cache::write('salesorders_customer_report_'.$type, $arr_data);

				}
			}
		}
		if($this->request->is('ajax'))
            die;
        else
            $this->render_pdf($arr_data);
	}
	function get_sum_sub_total($product_id, $origin_minimum, $arr_where){
        $arr_where['deleted'] = false;
        $arr_companies = $this->opm->collection->group(array('company_id' => true), array('companies' => array()), 'function (obj, prev) { prev.companies.push({_id : obj.company_id}); }',array('condition' => $arr_where));
        if( $arr_companies['ok'] && !empty($arr_companies['retval']) ) {
            $arr_companies = $arr_companies['retval'];
        }
        $arr_data = array();
        foreach($arr_companies as $company){
            $_id = $company['company_id'];
            $sum = 0;
            $minimum = $origin_minimum;
            $company = $this->Company->select_one(array('_id'=>$_id),array('name','our_rep','addresses','addresses_default_key', 'pricing'));
	        if(isset($company['pricing'])){
	            foreach($company['pricing'] as $pricing){
	                if(isset($pricing['deleted'])&&$pricing['deleted']) continue;
	                if((string)$pricing['product_id']!=(string)$product_id) continue;
	                if(!isset($pricing['price_break']) || empty($pricing['price_break'])) continue;
	                $price_break = reset($pricing['price_break']);
	                $minimum =  (float)$price_break['unit_price']; break;
	            }
	        }
	        $data = $this->db->command(array(
	            'mapreduce'     => 'tb_salesorder',
	            'map'           => new MongoCode('
	                                            function() {
	                                                if( this.sum_sub_total < '.$minimum.') {
	                                                    this.sum_sub_total = '.$minimum.';
	                                                }
	                                                emit("total", this.sum_sub_total)
	                                            }
	                                            '),
	            'reduce'        => new MongoCode('
	                                            function(k, v) {
	                                                var i, sum = 0;
	                                                for(i in v) {
	                                                    sum += v[i];
	                                                }
	                                                return sum;
	                                            }
	                                        '),
	            'query'         => array(
	                                   'company_id' => new MongoId($_id),
	                                   'deleted'    => false,
	                                   'invoice_status'  => array('$ne' => 'Cancelled'),
	                                ),
	            'out'           => array('merge' => 'tb_result')
	        ));
			if( isset($data['ok']) ) {
			    $result = $this->db->selectCollection('tb_result')->findOne();
			    $this->db->tb_sum_result->remove(array('_id' => 'total'));
			    $sum = isset($result['value']) ? $result['value'] : 0;

			}
            $_id = (string)$_id;
            $arr_data[$_id]['sum_sub_total'] = $sum;
            $arr_data[$_id]['company_name'] = (isset($company['name']) ? $company['name'] : '');
            $arr_data[$_id]['our_rep'] = (isset($company['our_rep']) ? $company['our_rep'] : '');
            $arr_data[$_id]['addresses'] = (isset($company['addresses']) ? $company['addresses'] : '');
            $arr_data[$_id]['addresses_default_key'] = (isset($company['addresses_default_key']) ? $company['addresses_default_key'] : 0);
            $arr_data[$_id]['minimum'] = $minimum;
            $arr_data[$_id]['number_of_salesorders'] = $this->opm->count(array_merge($arr_where,array('company_id'=>new MongoId($_id))));
        }
        return $arr_data;
    }
	public function summary_customer_report($data, $arr_where, $minimum, $product_id) {

		//--------------------------------------
		$html = '';
        $i = $sum = 0;
        $arr_company = array();
        $this->selectModel('Company');
        $arr_company = $this->get_sum_sub_total($product_id,$minimum, $arr_where);
        foreach ($arr_company as $value) {
            $html .= '
                <tr style="background-color:' . ( $i%2==0 ? '#eeeeee' : '#fdfcfa'). ';">
                     <td>' . $value['company_name'] . '</td>
                     <td>' . $value['our_rep'] . '</td>
                     <td class="right_text">' . $value['number_of_salesorders'] . '</td>
                     <td colspan="3" class="right_text">' . $this->opm->format_currency($value['sum_sub_total']) . '</td>
                </tr>
            ';
            $sum += ($value['sum_sub_total'] ? $value['sum_sub_total'] : 0);
            $i++;
        }
        $html .= '
                    <tr style="background-color:' . ( $i%2==0 ? '#eeeeee' : '#fdfcfa') . ';">
                         <td colspan="2" class="bold_text right_none">' . $i . ' record(s) listed</td>
                         <td class="bold_text right_none right_text" >Total</td>
                         <td class="bold_text right_text">' . $this->opm->format_currency($sum) . '</td>
                    </tr>
                </table>
                ';
        //========================================
        //set header
        if($data['heading']!='')
            $arr_data['report_heading'] = $data['heading'];
        $arr_data['date_from_to'] = '';
        if(isset($data['date_from'])&&$data['date_from']!='')
            $arr_data['date_from_to'] .= '<span class="color_red bold_text">From</span> '.$data['date_from'].' ';
        if(isset($data['date_to'])&&$data['date_to']!='')
            $arr_data['date_from_to'] .= ' <span class="color_red bold_text">To</span> '.$data['date_to'];
        if(isset($data['date_equals'])&&$data['date_equals']!='')
            $arr_data['date_from_to'] .= $data['date_equals'];
        $arr_data['title'] = array('Company'=>'text-align: left','Our Rep'=>'text-align: left','No. of SO'=>'text-align: right;','Ex. Tax total'=>'text-align: right');
        $arr_data['content'] = $html;
        $arr_data['report_name'] = 'Sales Order Report By Customer (Summary)';
        $arr_data['report_file_name'] = 'SO_'.md5(time());
        return $arr_data;
	}
	public function summary_area_customer_report($data, $arr_where, $minimum, $product_id) {
        //--------------------------------------
        $html = '';
        $i = $sum = 0;
        $arr_company = array();
        $this->selectModel('Company');
        $arr_company = $this->get_sum_sub_total($product_id,$minimum, $arr_where, true);
        $arr_province = $this->province('CA');
        $arr_area = array();
        foreach ($arr_company as $company_id=>$value) {
            $value['company_id'] = new MongoId($company_id);
            $province_state = '';
            $addresses_default_key = 0;
            if(isset($value['addresses_default_key']))
                $addresses_default_key = $value['addresses_default_key'];
            $province = '(empty)';
            if( isset($value['addresses'][$addresses_default_key]['province_state_id']) )
	            $province = (isset($arr_province[$value['addresses'][$addresses_default_key]['province_state_id']]) ? $arr_province[$value['addresses'][$addresses_default_key]['province_state_id']] : '(empty)');
            // unset($value['addresses']);
            if(!isset($arr_area[$province]['sum_sub_total']))
                $arr_area[$province]['sum_sub_total'] = 0;
            $arr_area[$province]['sum_sub_total'] += $value['sum_sub_total'];
            if(!isset($arr_area[$province]['number_of_salesorders']))
                $arr_area[$province]['number_of_salesorders'] = 0;
            $arr_area[$province]['number_of_salesorders'] += $value['number_of_salesorders'];
            if(!isset($arr_area[$province]['number_of_companies']))
                $arr_area[$province]['number_of_companies'] = 0;
            $arr_area[$province]['number_of_companies']++;
        }
        ksort($arr_area);
        foreach ($arr_area as $province_name=>$value) {
            $html .= '
                <tr style="background-color:' . ( $i%2==0 ? '#eeeeee' : '#fdfcfa'). ';">
                     <td class="first content" align="left">' . $province_name . '</td>
                     <td class="content">' . $value['number_of_salesorders'] . '</td>
                     <td class="content" align="left">' . $value['number_of_companies'] . '</td>

                     <td colspan="3" class="content"  align="right" class="end">' . $this->opm->format_currency($value['sum_sub_total']) . '</td>
                </tr>
            ';
            $sum += ($value['sum_sub_total'] ? $value['sum_sub_total'] : 0);
            $i++;
        }
        $html .= '
                    <tr style="background-color:' . ( $i%2==0 ? '#eeeeee' : '#fdfcfa') . ';">
                         <td colspan="2" class="bold_text right_none">' . $i . ' record(s) listed</td>
                         <td class="bold_text right_none right_text" >Total</td>
                         <td class="bold_text right_text">' . $this->opm->format_currency($sum) . '</td>
                    </tr>
                </table>
                ';
        //========================================
        //set header
        if($data['heading']!='')
            $arr_data['report_heading'] = $data['heading'];
        $arr_data['date_from_to'] = '';
        if(isset($data['date_from'])&&$data['date_from']!='')
            $arr_data['date_from_to'] .= '<span class="color_red bold_text">From</span> '.$data['date_from'].' ';
        if(isset($data['date_to'])&&$data['date_to']!='')
            $arr_data['date_from_to'] .= ' <span class="color_red bold_text">To</span> '.$data['date_to'];
        if(isset($data['date_equals'])&&$data['date_equals']!='')
            $arr_data['date_from_to'] .= $data['date_equals'];
        $arr_data['title'] = array('Province'=>'text-align: left','No. of SO'=>'text-align: left','No. of Companies'=>'text-align: left','Ex. Tax total'=>'text-align: right');
        $arr_data['content'] = $html;
        $arr_data['report_name'] = 'SO Report By Area Customer (Summary)';
        $arr_data['report_file_name'] = 'SO_'.md5(time());
        return $arr_data;
    }
	public function detailed_customer_report($data, $arr_where, $minimum, $product_id) {
        $i = $sum = 0;
        $html = '';
        //Loc ra nhung salesorder group theo company id
        $group_company = array();
        $this->selectModel('Company');
        $arr_company = $this->get_sum_sub_total($product_id,$minimum, $arr_where);
        $total_num_of_salesorders = $total_sum_sub_total = 0;
        $arr_salesorders = $this->opm->collection->group(array('company_id' => true), array('salesorders' => array()), 'function (obj, prev) { prev.salesorders.push({_id : obj._id, code: obj.code, sales_order_type : obj.sales_order_type, salesorder_date: obj.salesorder_date, heading: obj.heading, status : obj.status, our_rep : obj.our_rep, sum_sub_total : obj.sum_sub_total, shipping_cost : obj.shipping_cost}); }',array('condition' => $arr_where));
        if( $arr_salesorders['ok'] && !empty($arr_salesorders['retval']) ) {
            $arr_salesorders = $arr_salesorders['retval'];
        }
        foreach($arr_company as $company_id => $value) {
        	$total_num_of_salesorders += $value['number_of_salesorders'];
			$total_sum_sub_total += $value['sum_sub_total'];
            foreach($arr_salesorders as $k => $v) {
                if( (string)$v['company_id'] != $company_id ) continue;
                if( !empty($v['salesorders']) ) {
                    usort($v['salesorders'], function($a, $b){
                        return $a['_id']->getTimestamp() < $b['_id']->getTimestamp();
                    });
                    foreach($v['salesorders'] as $salesorder) {
                    	$salesorder['sum_sub_total'] = $salesorder['sum_sub_total'] < $value['minimum']  ? $value['minimum'] : $salesorder['sum_sub_total'];
                        $arr_company[$company_id]['salesorders'][$salesorder['code']] = array(
                        														'_id'				=> $salesorder['_id'],
                                                                                'salesorder_code'    =>$salesorder['code'],
                                                                                'salesorder_type'    =>$salesorder['sales_order_type'],
                                                                                'salesorder_date'    =>$this->opm->format_date($salesorder['salesorder_date']->sec),
                                                                                'salesorder_heading'    =>(isset($salesorder['heading']) ? $salesorder['heading'] : ''),
                                                                                'salesorder_status'    =>$salesorder['status'],
                                                                                'salesorder_our_rep'    =>$salesorder['our_rep'],
                                                                                'sum_sub_total'     => $salesorder['sum_sub_total']
                                                                               );
                    }
                }
                unset($arr_salesorders[$k]);
                break;
            }
        }
        foreach ($arr_company as $key => $company) {
            $html .= '
            <table class="table_content">
               <tbody>
                  <tr class="tr_right_none" style="background: #911b12;color: white;height: 29px;line-height: 29px;font-weight: bold;">
                     <td width="35%">
                        Company
                     </td>
                     <td width="25%">
                        Our Rep
                     </td>
                     <td class="right_text" width="15%">
                        No. of SO
                     </td>
                     <td class="right_text" colspan="3">
                        Group total (ex. tax)
                     </td>
                  </tr>
                  <tr class="bg_2">
                     <td>' . $company['company_name'] . '</td>
                     <td>' . $company['our_rep'] . '</td>
                     <td class="right_text">' . $company['number_of_salesorders'] . '</td>
                     <td colspan="3" class="right_text">' . $this->opm->format_currency($company['sum_sub_total']) . '</td>
                  </tr>
               </tbody>
            </table>';
            $html .= '<table class="table_content" >
                        <tbody>
                          <tr class="tr_right_none" style="background: #979797;color: white;height: 29px;line-height: 29px;font-weight: bold;">
                             <td width="7%">
                                SO#
                             </td>
                             <td width="15%">
                                Type
                             </td>
                             <td width="15%">
                                Date
                             </td>
                             <td width="15%">
                                Status
                             </td>
                             <td width="15%">
                                Heading
                             </td>
                             <td width="15%">
                                Our Rep
                             </td>
                             <td class="right_text" colspan="3" width="18%">
                                Ex. Tax total
                             </td>
                          </tr>';
            $i = 0;
            $sum = 0;
            usort($company['salesorders'], function($a, $b){
                return $a['_id']->getTimestamp() < $b['_id']->getTimestamp();
            });
            foreach ($company['salesorders'] as $key => $salesorder) {
            	if( $salesorder['salesorder_status'] == 'Cancelled' ) {
            		$salesorder['sum_sub_total'] = 0;
            	}
                $sum += $salesorder['sum_sub_total'];
                $html .= '
                      <tr class="content_asset bg_' . ($i % 2 == 0 ? '1' : '2') . '">
                         <td>' . $salesorder['salesorder_code'] . '</td>
                         <td>' . $salesorder['salesorder_type'] . '</td>
                         <td>' . $salesorder['salesorder_date'] . '</td>
                         <td>' . $salesorder['salesorder_status'] . '</td>
                         <td class="left_text">' . $salesorder['salesorder_heading'] . '</td>
                         <td class="left_text">' . $salesorder['salesorder_our_rep'] . '</td>
                         <td colspan="3" class="right_text">' . $this->opm->format_currency($salesorder['sum_sub_total']) . '</td>
                      </tr>';
                $i++;
            }
            $html .= '
                            <tr class="bg_' . ($i % 2 == 0 ? '1' : '2') . '">
                             <td colspan="5" class="left_text bold_text right_none">' . $i . ' record(s) listed</td>
                             <td class="bold_text right_text right_none">Total</td>
                             <td colspan="3" class="bold_text right_text">' . $this->opm->format_currency($sum) . '</td>
                          </tr>
                        </tbody>
                    </table>
                    <br />
                    <br />';
        }
        $html .= '
            	<div class="line" style="margin-bottom: 5px;"></div>
        		<table class="table_content">
        			<tr style="background-color: #333; color: white">
        				<td class="bold_text right_none" width="70%">'.$total_num_of_salesorders.' record(s) listed</td>
        				<td class="right_text bold_text right_none" >Totals</td>
        				<td class="right_text bold_text" width="15%">'.$this->opm->format_currency($total_sum_sub_total).'</td>
        			</tr>
        		</table>';
        //========================================
        //set header
        if($data['heading']!='')
            $arr_data['report_heading'] = $data['heading'];
        $arr_data['date_from_to'] = '';
        if(isset($data['date_from'])&&$data['date_from']!='')
            $arr_data['date_from_to'] .= '<span class="color_red bold_text">From</span> '.$data['date_from'].' ';
        if(isset($data['date_to'])&&$data['date_to']!='')
            $arr_data['date_from_to'] .= ' <span class="color_red bold_text">To</span> '.$data['date_to'];
        if(isset($data['date_equals'])&&$data['date_equals']!='')
            $arr_data['date_from_to'] .= $data['date_equals'];
        $arr_data['content'][]['html'] = $html;
        $arr_data['is_custom'] = true;
        $arr_data['image_logo'] = true;
        $arr_data['report_name'] = 'SO Report By Customer (Detailed)';
        $arr_data['report_file_name'] = 'SO_'.md5(time());
        return $arr_data;
    }
    public function detailed_area_customer_report($data, $arr_where, $minimum, $product_id) {
        $i = $sum = 0;
        $html = '';
        //Loc ra nhung quotation group theo company id
        $group_company = array();
        $this->selectModel('Company');
        $arr_company = $this->get_sum_sub_total($product_id,$minimum, $arr_where);
        $arr_province = $this->province('CA');
        foreach ($arr_company as $company_id=>$value) {
            $value['company_id'] = new MongoId($company_id);
            $province_state = '';
            $addresses_default_key = 0;
            if(isset($value['addresses_default_key']))
                $addresses_default_key = $value['addresses_default_key'];
            $province = '(empty)';
            if( isset($value['addresses'][$addresses_default_key]['province_state_id']) )
	            $province = (isset($arr_province[$value['addresses'][$addresses_default_key]['province_state_id']]) ? $arr_province[$value['addresses'][$addresses_default_key]['province_state_id']] : '(empty)');
            // unset($value['addresses']);
            if(!isset($arr_area[$province]['sum_sub_total']))
                $arr_area[$province]['sum_sub_total'] = 0;
            $arr_area[$province]['sum_sub_total'] += $value['sum_sub_total'];
            if(!isset($arr_area[$province]['number_of_salesorders']))
                $arr_area[$province]['number_of_salesorders'] = 0;
            $arr_area[$province]['number_of_salesorders'] += $value['number_of_salesorders'];
            $arr_area[$province]['companies'][] = $value;
        }
        $arr_salesorders = $this->opm->collection->group(array('company_id' => true), array('salesorders' => array()), 'function (obj, prev) { prev.salesorders.push({_id : obj._id, code: obj.code, sales_order_type : obj.sales_order_type, salesorder_date: obj.salesorder_date, heading: obj.heading, status : obj.status, our_rep : obj.our_rep, sum_sub_total : obj.sum_sub_total, shipping_cost : obj.shipping_cost, company_name: obj.company_name}); }',array('condition' => $arr_where));
        if( $arr_salesorders['ok'] && !empty($arr_salesorders['retval']) ) {
            $arr_salesorders = $arr_salesorders['retval'];
        }
        foreach($arr_area as $province=>$value){
            foreach($value['companies'] as $company){
                $company_id = $company['company_id'];
                foreach($arr_salesorders as $k => $v) {
                    if( (string)$v['company_id'] != $company_id ) continue;
                    if( !empty($v['salesorders']) ) {
                        foreach($v['salesorders'] as $salesorder) {
                        	$salesorder['sum_sub_total'] = $salesorder['sum_sub_total']<$company['minimum']  ? $company['minimum'] : $salesorder['sum_sub_total'];
                            $arr_area[$province]['salesorders'][$salesorder['code']] = array(
                            												'_id'				=> $salesorder['_id'],
                                                                            'salesorder_code'    =>$salesorder['code'],
                                                                            'company_name'      =>(isset($salesorder['company_name']) ? $salesorder['company_name'] : ''),
                                                                            'salesorder_type'    =>$salesorder['sales_order_type'],
                                                                            'salesorder_date'    =>$this->opm->format_date($salesorder['salesorder_date']->sec),
                                                                            'salesorder_heading'    =>(isset($salesorder['heading']) ? $salesorder['heading'] : ''),
                                                                            'salesorder_status'    =>$salesorder['status'],
                                                                            'salesorder_our_rep'    =>$salesorder['our_rep'],
                                                                            'sum_sub_total'     => $salesorder['sum_sub_total']
                                                                           );
                        }
                    }
                    unset($arr_salesorders[$k]);
                    break;
                }
            }
        }
        foreach ($arr_area as $province => $value) {
            $html .= '
            <table class="table_content">
               <tbody>
                  <tr class="tr_right_none" style="background: #911b12;color: white;height: 29px;line-height: 29px;font-weight: bold;">
                     <td width="35%">
                        Province
                     </td>
                     <td class="right_text" width="15%">
                        No. of SO
                     </td>
                     <td class="right_text" colspan="3">
                        Group total (ex. tax)
                     </td>
                  </tr>
                  <tr class="bg_2">
                     <td>' . $province . '</td>
                     <td class="right_text">' . $value['number_of_salesorders'] . '</td>
                     <td colspan="3" class="right_text">' . $this->opm->format_currency($value['sum_sub_total']) . '</td>
                  </tr>
               </tbody>
            </table>';
            $html .= '<table class="table_content" >
                        <tbody>
                          <tr class="tr_right_none" style="background: #911b12;color: white;height: 29px;line-height: 29px;font-weight: bold;">
                             <td width="7%">
                                SO#
                             </td>
                             <td width="10%">
                                Type
                             </td>
                             <td width="15%">
                                Company
                             </td>
                             <td width="15%">
                                Date
                             </td>
                             <td width="15%">
                                Status
                             </td>
                             <td width="15%">
                                Heading
                             </td>
                             <td width="15%">
                                Our Rep
                             </td>
                             <td class="right_text" colspan="3" width="18%">
                                Ex. Tax total
                             </td>
                          </tr>';
            $i = 0;
            $sum = 0;
            usort($value['salesorders'], function($a, $b){
                return $a['_id']->getTimestamp() < $b['_id']->getTimestamp();
            });
            foreach ($value['salesorders'] as $key => $salesorder) {
            	if( $salesorder['salesorder_status'] == 'Cancelled' )
            		$salesorder['sum_sub_total'] = 0;
                $sum += $salesorder['sum_sub_total'];
                $html .= '
                      <tr class="content_asset bg_' . ($i % 2 == 0 ? '1' : '2') . '">
                         <td>' . $salesorder['salesorder_code'] . '</td>
                         <td>' . $salesorder['salesorder_type'] . '</td>
                         <td>' . $salesorder['company_name'] . '</td>
                         <td>' . $salesorder['salesorder_date'] . '</td>
                         <td>' . $salesorder['salesorder_status'] . '</td>
                         <td class="left_text">' . $salesorder['salesorder_heading'] . '</td>
                         <td class="left_text">' . $salesorder['salesorder_our_rep'] . '</td>
                         <td colspan="3" class="right_text">' . $this->opm->format_currency((float)$salesorder['sum_sub_total']) . '</td>
                      </tr>';
                $i++;
            }
            $html .= '
                            <tr class="bg_' . ($i % 2 == 0 ? '1' : '2') . '">
                             <td colspan="5" class="left_text bold_text right_none">' . $i . ' record(s) listed</td>
                             <td class="bold_text right_text right_none">Total</td>
                             <td colspan="3" class="bold_text right_text">' . $this->opm->format_currency($sum) . '</td>
                          </tr>
                        </tbody>
                    </table>
                    <br />
                    <br />
                    <div class="line" style="margin-bottom: 10px;"></div>';
        }
        //========================================
        //set header
        if($data['heading']!='')
            $arr_data['report_heading'] = $data['heading'];
        $arr_data['date_from_to'] = '';
        if(isset($data['date_from'])&&$data['date_from']!='')
            $arr_data['date_from_to'] .= '<span class="color_red bold_text">From</span> '.$data['date_from'].' ';
        if(isset($data['date_to'])&&$data['date_to']!='')
            $arr_data['date_from_to'] .= ' <span class="color_red bold_text">To</span> '.$data['date_to'];
        if(isset($data['date_equals'])&&$data['date_equals']!='')
            $arr_data['date_from_to'] .= $data['date_equals'];
        $arr_data['content'][]['html'] = $html;
        $arr_data['is_custom'] = true;
        $arr_data['image_logo'] = true;
        $arr_data['report_name'] = 'SO Report By Area Customer (Detailed)';
        $arr_data['report_file_name'] = 'SO_'.md5(time());
        return $arr_data;
    }
	public function option_detailed_product_find($type = ''){
		if(!$this->check_permission($this->name.'_@_options_@_report_by_product_detailed'))
			$this->error_auth();
		$arr_data['salesorders_status'] = $this->Setting->select_option_vl(array('setting_value'=>'salesorders_status'));
		$arr_data['salesorders_type'] = $this->Setting->select_option_vl(array('setting_value'=>'sales_order_type'));
		$arr_data['product_category'] = $this->Setting->select_option_vl(array('setting_value'=>'product_category'));
		$this->set('arr_data',$arr_data);
		if($type=='category')
            $this->render('../Salesorders/option_detailed_category_find');
	}
	public function option_summary_product_find($type = ''){
		if(!$this->check_permission($this->name.'_@_options_@_report_by_product_summary'))
			$this->error_auth();
		$arr_data['salesorders_status'] = $this->Setting->select_option_vl(array('setting_value'=>'salesorders_status'));
		$arr_data['salesorders_type'] = $this->Setting->select_option_vl(array('setting_value'=>'sales_order_type'));
		$arr_data['product_category'] = $this->Setting->select_option_vl(array('setting_value'=>'product_category'));
		$this->set('arr_data',$arr_data);
		if($type=='category')
            $this->render('../Salesorders/option_summary_category_find');
	}
	public function get_cate_product($value) {
		$cate = $this->Setting->select_option_vl(array('setting_value' => 'product_category'));
		if(isset($cate[$value]))
			echo $cate[$value];
		else
			echo '';
		die();
	}
	public function product_report($type = ''){
        $arr_data = array();
        if(isset($_GET['print_pdf'])){
            $arr_data = Cache::read('salesorders_product_report_'.$type);
            Cache::delete('salesorders_product_report_'.$type);
        } else {
            if(isset($_POST)){
                $data['product_category'] = $this->Setting->select_option_vl(array('setting_value'=>'product_category'));
                $arr_post = $_POST;
                $arr_post = $this->Common->strip_search($arr_post);
                $arr_where = array();
                $arr_where['products']['$ne'] = '';
                if(!$this->check_permission('salesorders_@_view_worktraq_@_view')){
	            	$arr_where['code'] = array('$not' => new MongoRegex('/WT-/'));
	            }
                if(isset($arr_post['status']) && $arr_post['status'] != '')
                    $arr_where['status'] = $arr_post['status'];
                //Check loại trừ cancel thì bỏ các status bên dưới
                if(isset($arr_post['is_not_cancel'])&&$arr_post['is_not_cancel']==1){
                    $arr_where['status'] = array('$nin'=>array('Cancelled'));
                    //Tuy nhiên nếu ở ngoài combobox nếu có chọn, thì ưu tiên nó, set status lại
                    if(isset($arr_post['status'])&&$arr_post['status']!='')
                        $arr_where['status'] = $arr_post['status'];
                }
                if(isset($arr_post['type']) && $arr_post['type']!= '')
                    $arr_where['sales_order_type'] = $arr_post['type'];
                if(isset($arr_post['company']) && $arr_post['company']!='')
                    $arr_where['company_name'] = new MongoRegex('/'.trim($arr_post['company']).'/i');
                if(isset($arr_post['contact']) &&$arr_post['contact']!='')
                    $arr_where['contact_name'] = new MongoRegex('/'.trim($arr_post['contact']).'/i');
                if(isset($arr_post['job_no']) && $arr_post['job_no']!='')
                    $arr_where['job_number'] = trim($arr_post['job_no']);
                if(isset($arr_post['employee']) && trim($arr_post['employee'])!=''){
                    $arr_where['$or'][]['our_rep'] = new MongoRegex('/'.trim($arr_post['employee']).'/i');
                    $arr_where['$or'][]['our_csr'] = new MongoRegex('/'.trim($arr_post['employee']).'/i');
                }
                //Tìm chính xác ngày
                //Vì để = chỉ tìm đc 01/01/1969 00:00:00 nên phải cộng cho 23:59:59 rồi tìm trong khoảng đó
                if(isset($arr_post['date_equals'])&&$arr_post['date_equals']!=''){
                    $arr_where['salesorder_date']['$gte'] = new MongoDate($this->Common->strtotime($arr_post['date_equals'] . '00:00:00'));
                    $arr_where['salesorder_date']['$lt'] = new MongoDate($this->Common->strtotime($arr_post['date_equals'] . '23:59:59'));
                } else{  //Ngày nằm trong khoảng
                    //neu chi nhap date from
                    if(isset($arr_post['date_from']) && $arr_post['date_from'] != ''){
                        $arr_where['salesorder_date']['$gte'] = new MongoDate($this->Common->strtotime($arr_post['date_from'] . '00:00:00'));
                    }
                    //neu chi nhap date to
                    if(isset($arr_post['date_to']) && $arr_post['date_to'] != ''){
                        $arr_where['salesorder_date']['$lte'] = new MongoDate($this->Common->strtotime($arr_post['date_to'] . '23:59:59'));
                    }
                }
                //Kiểm tra nếu có thông tin liên quan đến product tồn tại
                $pro_where = array();
                if(isset($arr_post['product'])&&$arr_post['product']!='')
                    $pro_where['code'] = trim($arr_post['product']);
                if(isset($arr_post['name'])&&$arr_post['name']!='')
                    $pro_where['name'] = new MongoRegex('/' . trim($arr_post['name']) . '/i');
                if(isset($arr_post['category_id'])&&$arr_post['category_id']!='')
                    $pro_where['category'] = new MongoRegex('/'.$arr_post['category_id'].'/i');
                $pro_list = array();
                $arr_products_where = array();
                $arr_products_where['products.deleted'] = $arr_where['deleted'] = false;
                if(isset($arr_post['sell_price_from'])&&$arr_post['sell_price_from']!=''){
                    $arr_where['products']['$elemMatch']['sell_price']['$gte'] = (float)$arr_post['sell_price_from'];
                    $arr_products_where['products.unit_price']['$gte'] = (float)$arr_post['sell_price_from'];
                }
                if(isset($arr_post['sell_price_to'])&&$arr_post['sell_price_to']!=''){
                    $arr_where['products']['$elemMatch']['sell_price']['$lte'] = (float)$arr_post['sell_price_to'];
                    $arr_products_where['products.unit_price']['$lte'] = (float)$arr_post['sell_price_to'];
                }
                if(!empty($pro_where)){
                    //Lấy ra _id của Product phù hợp với điều kiện trên
                    $this->selectModel('Product');
                    $pro_list = $this->Product->select_all(array(
                                            'arr_where'=>$pro_where,
                                            'arr_field'=>array('_id')
                        ));
                    foreach($pro_list as $p_id){
                       $arr_where['products']['$elemMatch']['products_id']['$in'][] = new MongoId($p_id['_id']);
                       $arr_products_where['products.products_id']['$in'][] = new MongoId($p_id['_id']);
                    }
                }
                $arr_where['products']['$elemMatch']['deleted'] = false;
                $arr_salesorders = $this->opm->collection->aggregate(
                        array(
                            '$match'=>$arr_where,
                        ),
                        array(
                            '$unwind'=>'$products',
                        ),
                         array(
                            '$match'=>$arr_products_where
                        ),
                        array(
                            '$project'=>array('status'=>'$status','code'=>'$code','company_name'=>'$company_name','company_id'=>'$company_id','salesorder_date'=>'$salesorder_date','sum_sub_total'=>'$sum_sub_total','products'=>'$products')
                        ),
                        array(
                            '$group'=>array(
                                          '_id'=>array('_id'=>'$_id','status'=>'$status','code'=>'$code','company_name'=>'$company_name','company_id'=>'$company_id','salesorder_date'=>'$salesorder_date','sum_sub_total'=>'$sum_sub_total'),
                                          'products'=>array('$push'=>'$products')
                                        )
                        )
                    );
                if(empty($arr_salesorders['result'])) {
                    echo 'empty';
                    die;
                } else {
                    $arr_salesorders = $arr_salesorders['result'];
                    if ($arr_post['report_type'] == 'summary'){
                        $arr_data = $this->summary_product_report($arr_salesorders,$arr_post);
                        Cache::write('salesorders_product_report_'.$type, $arr_data);
                    }
                    else if ($arr_post['report_type'] == 'detailed'){
                        $arr_data = $this->detailed_product_report($arr_salesorders,$arr_post);
                        Cache::write('salesorders_product_report_'.$type, $arr_data);
                    }
                    else if($arr_post['report_type'] == 'category_summary'){
                        $arr_data = $this->summary_category_product_report($arr_salesorders,$arr_post);
                        Cache::write('salesorders_product_report_'.$type, $arr_data);
                    }
                    else if($arr_post['report_type'] == 'category_detailed'){
                        $arr_data = $this->detailed_category_product_report($arr_salesorders,$arr_post);
                        Cache::write('salesorders_product_report_'.$type, $arr_data);
                    }

                }

            }
        }
        if($this->request->is('ajax'))
            die;
        else
            $this->render_pdf($arr_data);
    }
	public function summary_product_report($arr_salesorders,$data){
        $html = '';
        $i = $sum = 0;
        $this->selectModel('Product');
        $category = $this->Setting->select_option_vl(array('setting_value' => 'product_category'));
        $arr_data = array();
        foreach($arr_salesorders as $salesorder){
            foreach($salesorder['products'] as $product){
                $product['code'] = (isset($product['code']) ? $product['code'] : 'empty');
                $arr_data[$product['code']]['products_name'] = $product['products_name'];
                $arr_data[$product['code']]['code'] = $product['code'];
                $arr_data[$product['code']]['products_id'] = $product['products_id'];
                if(!isset($arr_data[$product['code']]['quantity']))
                    $arr_data[$product['code']]['quantity'] = 0;
                $arr_data[$product['code']]['quantity'] += $product['quantity'];
                if(!isset($arr_data[$product['code']]['sub_total']))
                    $arr_data[$product['code']]['sub_total'] = 0;
                $arr_data[$product['code']]['sub_total'] += (isset($product['sub_total']) ? (float)$product['sub_total'] : 0);
            }
        }
        foreach ($arr_data as $value) {
            if(is_object($value['products_id']))
                $product = $this->Product->select_one(array('_id'=>new MongoId($value['products_id'])),array('category'));
            if (!isset($product['category']))
                $product['category'] = '';
            $html .= '
                <tr style="background-color:' . ( $i%2==0 ? '#eeeeee' : '#fdfcfa'). ';">
                     <td>' . $value['code'] . '</td>
                     <td>' . $value['products_name'] . '</td>
                     <td>' . (isset($category[$product['category']]) ? $category[$product['category']] : '') . '</td>
                     <td class="right_text">' . $value['quantity'] . '</td>
                     <td colspan="3" class="right_text">' . $this->opm->format_currency($value['sub_total']) . '</td>
                </tr>
            ';
            $sum += ($value['sub_total'] ? $value['sub_total'] : 0);
            $i++;
        }
        $html .= '
                    <tr style="background-color:' . ( $i%2==0 ? '#eeeeee' : '#fdfcfa') . ';">
                         <td colspan="3" class="bold_text right_none">' . $i . ' record(s) listed</td>
                         <td class="bold_text right_none right_text" >Total</td>
                         <td class="bold_text right_text">' . $this->opm->format_currency($sum) . '</td>
                    </tr>
                </table>
                ';
        //========================================
        //set header
        if($data['heading']!='')
            $arr_pdf['report_heading'] = $data['heading'];
        $arr_pdf['date_from_to'] = '';
        if(isset($data['date_from'])&&$data['date_from']!='')
            $arr_pdf['date_from_to'] .= '<span class="color_red bold_text">From</span> '.$data['date_from'].' ';
        if(isset($data['date_to'])&&$data['date_to']!='')
            $arr_pdf['date_from_to'] .= ' <span class="color_red bold_text">To</span> '.$data['date_to'];
        if(isset($data['date_equals'])&&$data['date_equals']!='')
            $arr_pdf['date_from_to'] .= $data['date_equals'];
        $arr_pdf['title'] = array('P. Code'=>'text-align: left','Product Name'=>'text-align: left','Category'=>'text-align: left','Qty'=>'text-align: right;','Ex. Tax total'=>'text-align: right');
        $arr_pdf['content'] = $html;
        $arr_pdf['report_name'] = 'SO Report By Product (Summary)';
        $arr_pdf['report_file_name'] = 'SO_'.md5(time());
        return $arr_pdf;
    }
    public function summary_category_product_report($arr_salesorders,$data){
        $html = '';
        $i = $sum = 0;
        $this->selectModel('Product');
        $category = $this->Setting->select_option_vl(array('setting_value' => 'product_category'));
        $arr_data = array();
        foreach($arr_salesorders as $salesorder){
            foreach($salesorder['products'] as $product){
                $product_category = '(empty)';
                if(is_object($product['products_id'])){
                    $_product = $this->Product->select_one(array('_id'=> new MongoId($product['products_id'])),array('category'));
                    if (isset($_product['category']))
                        $product_category = (isset($category[$_product['category']]) ? $category[$_product['category']] : '(empty)');
                }
                $arr_data[$product_category]['category_name'] = $product_category;
                if(!isset($arr_data[$product_category]['quantity']))
                    $arr_data[$product_category]['quantity'] = 0;
                $arr_data[$product_category]['quantity'] += $product['quantity'];
                if(!isset($arr_data[$product_category]['sub_total']))
                    $arr_data[$product_category]['sub_total'] = 0;
                $arr_data[$product_category]['sub_total'] += (isset($product['sub_total']) ? (float)$product['sub_total'] : 0);
            }
        }
        foreach ($arr_data as $value) {
            $html .= '
                <tr style="background-color:' . ( $i%2==0 ? '#eeeeee' : '#fdfcfa'). ';">
                     <td>' . $value['category_name'] . '</td>
                     <td class="right_text">' . $value['quantity'] . '</td>
                     <td colspan="2" class="right_text">' . $this->opm->format_currency($value['sub_total']) . '</td>
                </tr>
            ';
            $sum += ($value['sub_total'] ? $value['sub_total'] : 0);
            $i++;
        }
        $html .= '
                    <tr style="background-color:' . ( $i%2==0 ? '#eeeeee' : '#fdfcfa') . ';">
                         <td class="bold_text right_none">' . $i . ' record(s) listed</td>
                         <td class="bold_text right_none right_text" >Total</td>
                         <td class="bold_text right_text">' . $this->opm->format_currency($sum) . '</td>
                    </tr>
                </table>
                ';
        //========================================
        //set header
        if($data['heading']!='')
            $arr_pdf['report_heading'] = $data['heading'];
        $arr_pdf['date_from_to'] = '';
        if(isset($data['date_from'])&&$data['date_from']!='')
            $arr_pdf['date_from_to'] .= '<span class="color_red bold_text">From</span> '.$data['date_from'].' ';
        if(isset($data['date_to'])&&$data['date_to']!='')
            $arr_pdf['date_from_to'] .= ' <span class="color_red bold_text">To</span> '.$data['date_to'];
        if(isset($data['date_equals'])&&$data['date_equals']!='')
            $arr_pdf['date_from_to'] .= $data['date_equals'];
        $arr_pdf['title'] = array('Category Name'=>'text-align: left','Qty'=>'text-align: right;','Ex. Tax total'=>'text-align: right');
        $arr_pdf['content'] = $html;
        $arr_pdf['report_name'] = 'SO Report By Category Product (Summary)';
        $arr_pdf['report_file_name'] = 'SO_'.md5(time());
        return $arr_pdf;
    }
	public function detailed_product_report($arr_salesorders,$data){
        $i = $sum = 0;
        $html = '';
        $this->selectModel('Product');
        $category = $this->Setting->select_option_vl(array('setting_value' => 'product_category'));
        $total_num_of_salesorders = $total_sum_sub_total = 0;
        $arr_data = $arr_pdf = array();
        foreach($arr_salesorders as $quotation){
            foreach($quotation['products'] as $product){
                $product['code'] = (isset($product['code']) ? $product['code'] : '(empty)');
                $arr_data[$product['code']]['products_name'] = $product['products_name'];
                $arr_data[$product['code']]['code'] = $product['code'];
                $arr_data[$product['code']]['products_id'] = $product['products_id'];
                if(!isset($arr_data[$product['code']]['quantity']))
                    $arr_data[$product['code']]['quantity'] = 0;
                $arr_data[$product['code']]['quantity'] += $product['quantity'];
                if(!isset($arr_data[$product['code']]['sub_total']))
                    $arr_data[$product['code']]['sub_total'] = 0;
                $arr_data[$product['code']]['sub_total'] += (isset($product['sub_total']) ? (float)$product['sub_total'] : 0);
                if(!isset($arr_data[$product['code']]['quantity']))
                    $arr_data[$product['code']]['quantity'] = 0;
                $arr_data[$product['code']]['quantity'] += (isset($product['quantity']) ? (float)$product['quantity'] : 0);
                $arr_data[$product['code']]['salesorders'][] = array_merge($quotation['_id'], array('unit_price'=>$product['unit_price'],'quantity'=>$product['quantity'],'sub_total'=>$product['sub_total']));
                if(!isset( $arr_data[$product['code']]['no_of_so']))
                    $arr_data[$product['code']]['no_of_so'] = array();
                $arr_data[$product['code']]['no_of_so'][$quotation['_id']['code']] = 1;
            }
        }

        foreach ($arr_data as $value) {
            $total_num_of_salesorders += count($value['salesorders']);
            if(is_object($value['products_id']))
                $product = $this->Product->select_one(array('_id'=>new MongoId($value['products_id'])),array('category'));
            if (!isset($product['category']))
                $product['category'] = '';
            $html .= '
            <table class="table_content">
               <tbody>
                  <tr class="tr_right_none" style="background: #911b12;color: white;height: 29px;line-height: 29px;font-weight: bold;">
                     <td width="10%">
                        P. Code
                     </td>
                     <td>
                        Product Name
                     </td>
                     <td width="15%">
                        Category
                     </td>
                     <td class="right_text" width="15%">
                        No. of SO
                     </td>
                     <td class="right_text" colspan="3" width="20%">
                        Group total (ex. tax)
                     </td>
                  </tr>
                  <tr class="bg_2">
                     <td>' . $value['code'] . '</td>
                     <td>' . $value['products_name'] . '</td>
                     <td>' . (isset($category[$product['category']]) ? $category[$product['category']] : '') . '</td>
                     <td class="right_text">' . count($value['no_of_so']) . '</td>
                     <td colspan="3" class="right_text">' . $this->opm->format_currency($value['sub_total']) . '</td>
                  </tr>
               </tbody>
            </table>';
            $html .= '<table class="table_content" >
                        <tbody>
                          <tr class="tr_right_none" style="background: #979797;color: white;height: 29px;line-height: 29px;font-weight: bold;">
                             <td width="10%">
                                SO#
                             </td>
                             <td width="30%">
                                Company
                             </td>
                             <td width="15%" class="center_text">
                                Date
                             </td>
                             <td width="15%" class="right_text">
                                Unit Price
                             </td>
                             <td width="15%" class="right_text">
                                Quantity
                             </td>
                             <td class="right_text" colspan="3" width="18%">
                                Ex. Tax total
                             </td>
                          </tr>';
            $i = 0;
            $sum = $total_quantity = 0;
            foreach ($value['salesorders'] as $salesorder) {
                $sum += $salesorder['sub_total'];
                $total_quantity += $salesorder['quantity'];
                $html .= '
                      <tr class="content_asset bg_' . ($i % 2 == 0 ? '1' : '2') . '">
                         <td>' . $salesorder['code'] . '</td>
                         <td>' . $salesorder['company_name'] . '</td>
                         <td class="center_text">' . $this->opm->format_date($salesorder['salesorder_date']->sec) . '</td>
                         <td class="right_text">' . $this->opm->format_currency((float)$salesorder['unit_price']) . '</td>
                         <td class="right_text">' . $salesorder['quantity'] . '</td>
                         <td colspan="3" class="right_text">' . $this->opm->format_currency((float)$salesorder['sub_total']) . '</td>
                      </tr>';
                $i++;
            }
            $html .= '
                            <tr class="bg_' . ($i % 2 == 0 ? '1' : '2') . '">
                             <td colspan="3" class="left_text bold_text right_none">' . $i . ' record(s) listed</td>
                             <td class="bold_text right_text right_none">Totals</td>
                             <td class="bold_text right_text right_none">' . $total_quantity . '</td>
                             <td colspan="4" class="bold_text right_text">' . $this->opm->format_currency($sum) . '</td>
                          </tr>
                        </tbody>
                    </table>
                    <br />
                    <br />';
            $total_sum_sub_total += $sum;
        }
        $html .= '
                <div class="line" style="margin-bottom: 5px;"></div>
                <table class="table_content">
                    <tr style="background-color: #333; color: white">
                        <td class="bold_text right_none" width="70%">'.$total_num_of_salesorders.' record(s) listed</td>
                        <td class="right_text bold_text right_none" >Totals</td>
                        <td class="right_text bold_text" width="15%">'.$this->opm->format_currency($total_sum_sub_total).'</td>
                    </tr>
                </table>';

        //========================================
        //set header
        if($data['heading']!='')
            $arr_pdf['report_heading'] = $data['heading'];
        $arr_pdf['date_from_to'] = '';
        if(isset($data['date_from'])&&$data['date_from']!='')
            $arr_pdf['date_from_to'] .= '<span class="color_red bold_text">From</span> '.$data['date_from'].' ';
        if(isset($data['date_to'])&&$data['date_to']!='')
            $arr_pdf['date_from_to'] .= ' <span class="color_red bold_text">To</span> '.$data['date_to'];
        if(isset($data['date_equals'])&&$data['date_equals']!='')
            $arr_pdf['date_from_to'] .= $data['date_equals'];
        $arr_pdf['content'][]['html'] = $html;
        $arr_pdf['is_custom'] = true;
        $arr_pdf['image_logo'] = true;
        $arr_pdf['report_name'] = 'SO Report By Product (Detailed)';
        $arr_pdf['report_file_name'] = 'SO_'.md5(time());
        return $arr_pdf;
    }
    public function detailed_category_product_report($arr_salesorders,$data){
        $i = $sum = 0;
        $html = '';
        $this->selectModel('Product');
        $category = $this->Setting->select_option_vl(array('setting_value' => 'product_category'));
        $total_num_of_salesorders = $total_sum_sub_total = 0;
        $arr_data = $arr_pdf = array();
        foreach($arr_salesorders as $salesorder){
            foreach($salesorder['products'] as $product){
                $product_category = '(empty)';
                if(is_object($product['products_id'])){
                    $_product = $this->Product->select_one(array('_id'=> new MongoId($product['products_id'])),array('category'));
                    if (isset($_product['category']))
                        $product_category = (isset($category[$_product['category']]) ? $category[$_product['category']] : '(empty)');
                }
                 $arr_data[$product_category]['category_name'] = $product_category;
                if(!isset($arr_data[$product_category]['quantity']))
                    $arr_data[$product_category]['quantity'] = 0;
                $arr_data[$product_category]['quantity'] += $product['quantity'];
                if(!isset($arr_data[$product_category]['sub_total']))
                    $arr_data[$product_category]['sub_total'] = 0;
                $arr_data[$product_category]['sub_total'] += (isset($product['sub_total']) ? (float)$product['sub_total'] : 0);
                $arr_data[$product_category]['quantity'] += (isset($product['quantity']) ? (float)$product['quantity'] : 0);
                $arr_data[$product_category]['quotations'][] = array_merge($salesorder['_id'], array('unit_price'=>$product['unit_price'],'quantity'=>$product['quantity'],'sub_total'=>$product['sub_total']));
                if(!isset( $arr_data[$product_category]['no_of_qt']))
                    $arr_data[$product_category]['no_of_qt'] = array();
                $arr_data[$product_category]['no_of_qt'][$salesorder['_id']['code']] = 1;
            }
        }

        foreach ($arr_data as $value) {
            $total_num_of_salesorders += count($value['quotations']);
            $html .= '
            <table class="table_content">
               <tbody>
                  <tr class="tr_right_none" style="background: #911b12;color: white;height: 29px;line-height: 29px;font-weight: bold;">
                     <td width="25%">
                        Category Name
                     </td>
                     <td class="right_text" width="15%">
                        No. of SO
                     </td>
                     <td class="right_text" colspan="3" width="20%">
                        Group total (ex. tax)
                     </td>
                  </tr>
                  <tr class="bg_2">
                     <td>' . $value['category_name'] . '</td>
                     <td class="right_text">' . count($value['no_of_qt']) . '</td>
                     <td colspan="3" class="right_text">' . $this->opm->format_currency($value['sub_total']) . '</td>
                  </tr>
               </tbody>
            </table>';
            $html .= '<table class="table_content" >
                        <tbody>
                          <tr class="tr_right_none" style="background: #979797;color: white;height: 29px;line-height: 29px;font-weight: bold;">
                             <td width="10%">
                                SO#
                             </td>
                             <td width="30%">
                                Company
                             </td>
                             <td width="15%" class="center_text">
                                Date
                             </td>
                             <td width="15%" class="right_text">
                                Unit Price
                             </td>
                             <td width="15%" class="right_text">
                                Quantity
                             </td>
                             <td class="right_text" colspan="3" width="18%">
                                Ex. Tax total
                             </td>
                          </tr>';
            $i = 0;
            $sum = $total_quantity = 0;
            foreach ($value['quotations'] as $salesorder) {
                $sum += $salesorder['sub_total'];
                $total_quantity += $salesorder['quantity'];
                $html .= '
                      <tr class="content_asset bg_' . ($i % 2 == 0 ? '1' : '2') . '">
                         <td>' . $salesorder['code'] . '</td>
                         <td>' . $salesorder['company_name'] . '</td>
                         <td class="center_text">' . $this->opm->format_date($salesorder['salesorder_date']->sec) . '</td>
                         <td class="right_text">' . $this->opm->format_currency((float)$salesorder['unit_price']) . '</td>
                         <td class="right_text">' . $salesorder['quantity'] . '</td>
                         <td colspan="3" class="right_text">' . $this->opm->format_currency((float)$salesorder['sub_total']) . '</td>
                      </tr>';
                $i++;
            }
            $html .= '
                            <tr class="bg_' . ($i % 2 == 0 ? '1' : '2') . '">
                             <td colspan="3" class="left_text bold_text right_none">' . $i . ' record(s) listed</td>
                             <td class="bold_text right_text right_none">Totals</td>
                             <td class="bold_text right_text right_none">' . $total_quantity . '</td>
                             <td colspan="4" class="bold_text right_text">' . $this->opm->format_currency($sum) . '</td>
                          </tr>
                        </tbody>
                    </table>
                    <br />
                    <br />';
            $total_sum_sub_total += $sum;
        }
        $html .= '
                <div class="line" style="margin-bottom: 5px;"></div>
                <table class="table_content">
                    <tr style="background-color: #333; color: white">
                        <td class="bold_text right_none" width="70%">'.$total_num_of_salesorders.' record(s) listed</td>
                        <td class="right_text bold_text right_none" >Totals</td>
                        <td class="right_text bold_text" width="15%">'.$this->opm->format_currency($total_sum_sub_total).'</td>
                    </tr>
                </table>';

        //========================================
        //set header
        if($data['heading']!='')
            $arr_pdf['report_heading'] = $data['heading'];
        $arr_pdf['date_from_to'] = '';
        if(isset($data['date_from'])&&$data['date_from']!='')
            $arr_pdf['date_from_to'] .= '<span class="color_red bold_text">From</span> '.$data['date_from'].' ';
        if(isset($data['date_to'])&&$data['date_to']!='')
            $arr_pdf['date_from_to'] .= ' <span class="color_red bold_text">To</span> '.$data['date_to'];
        if(isset($data['date_equals'])&&$data['date_equals']!='')
            $arr_pdf['date_from_to'] .= $data['date_equals'];
        $arr_pdf['content'][]['html'] = $html;
        $arr_pdf['is_custom'] = true;
        $arr_pdf['image_logo'] = true;
        $arr_pdf['report_name'] = 'SO Report By Category Product (Detailed)';
        $arr_pdf['report_file_name'] = 'SO_'.md5(time());
        return $arr_pdf;
    }

	//
	// End report
	//
	public function check_condition_salesorder()
	{
		$this->selectModel('Salesorder');
		$id = $this->get_id();
		$salesorder = $this->Somonth->select_one(array('_id' => new MongoId($id)));
		if($salesorder!='')
		{
			if($salesorder['company_id']==''&&$salesorder['contact_id']=='')
				return array('err1');
			else if($salesorder['products']=='')
				return array('err2');
			else if(isset($salesorder['shipping_id'])&&$salesorder['shipping_id']!='')
				return array('err3');
			return $salesorder;
		}
		return false;
	}

	public function create_shipping(){
		$this->autoRender = FALSE;
		$check = $this->check_condition_salesorder();
		if(@$check[0]=='err1')
			echo json_encode(array('status'=>'error','mess'=>'This function cannot be performed as there no company or contact linked to this record.'));
		else if(@$check[0]=='err2')
			echo json_encode(array('status'=>'error','mess'=>'No items have been entered on this transaction yet.'));
		else if(@$check[0]=='err3')
			echo json_encode(array('status'=>'error','mess'=>'This sales order has been part or fully shipped.'));
		else if(is_array($check))
		{
			if( !$check['payment_terms'] ) {
				if( !isset($_POST['password']) ) {
					echo json_encode(array('status'=>'need_pass'));
					die;
				} else {
					$this->selectModel('Stuffs');
					$change = $this->Stuffs->select_one(array('value' => 'Changing Code'));
					if(md5($_POST['password']) != $change['password']){
					   	echo json_encode(array('status'=>'error', 'message' => 'Wrong password.'));
						die;
					}
				}
			}
			$arr_save = $check;
			$arr_save['salesorder_id'] = $check['_id'];
			$arr_save['salesorder_number'] = $arr_save['code'];
			if(isset($arr_save['contact_id']) && is_object($arr_save['contact_id'])){
				$this->selectModel('Contact');
				$contact = $this->Contact->select_one(array('_id'=>$arr_save['contact_id']),array('full_name'));
				if(isset($contact['full_name']))
					$arr_save['contact_name'] = $contact['full_name'];
			}
			$this->selectModel('Shipping');
			$arr_save['code'] = $this->Shipping->get_auto_code('code');

			$arr_save['carrier_name'] = '';
			$arr_save['carrier_id'] = '';
			if($check['shipping_address']=='')
				$arr_save['shipping_address'] = $arr_save['invoice_address'];
			$arr_save['received_date'] = '';
			$arr_save['return_status'] = 0;
			$arr_save['shipping_date'] = new MongoDate();
			$arr_save['shipping_status'] = "In progress";
			$arr_save['shipping_type'] = "Out";
			$arr_save['tracking_no'] = '';
			$arr_save['traking'] = '';
			$arr_save['other_comment'] = isset($check['shipping_comment']) ? $check['shipping_comment'] : '';
			unset($arr_save['_id']);
			unset($arr_save['date_modified']);
			unset($arr_save['payment_due_date']);
			unset($arr_save['sales_order_type']);
			unset($arr_save['salesorder_date']);

			$this->Shipping->arr_default_before_save = $arr_save;
			if($this->Shipping->add())
			{
				$id = $this->Shipping->mongo_id_after_save;
				$check['shipping_number'] = $arr_save['code'];
				$check['shipping_id'] = $id;
				$this->selectModel('Salesorder');
				$this->Somonth->save($check);
				echo json_encode(array('status'=>'ok','url'=>URL.'/shippings/entry/'.$id));
			}
		}
		die;
	}

	public function check_condition_back_order($type = 'invoice')
	{

		$this->selectModel('Salesorder');
		if($type == 'invoice')
			$arr_where['invoice_not_full'] = true;
		else if($type == 'shipping')
			$arr_where['shipping_not_full'] = true;
		$back_order = $this->Somonth->select_all(array(
											'arr_where'=>$arr_where,
											'arr_field'=>array('_id','products','company_name','code','salesorder_date')
				));
		if($back_order->count()>0)
			return $back_order_invoice;
		return false;
	}
	public function back_order_invoice()
	{
		$status = $this->check_condition_back_order('invoice');
		if($status == false)
		{
			echo json_encode(array('status'=>'error','mess'=>'There are currently no outstading back orders for invoicing.'));
			die;
		}
		else
		{
			$group = array();
			foreach($status as $BOI)
			{
				foreach($BOI['products'] as $products)
				{
					if($products['deleted']==false&&isset($products['invoice_balance']))
					{
						$group[(string)$BOI['_id']]['company_name'] = $BOI['company_name'];
						$group[(string)$BOI['_id']]['salesorder'][$BOI['code']]['salesorder_date'] = $BOI['salesorder_date'];
						$group[(string)$BOI['_id']]['salesorder'][$BOI['code']]['products'][$products['code']]['product_name'] = $products['products_name'];
						$group[(string)$BOI['_id']]['salesorder'][$BOI['code']]['products'][$products['code']]['product_quantity'] = $products['quantity'];
						$group[(string)$BOI['_id']]['salesorder'][$BOI['code']]['products'][$products['code']]['product_balance'] = $products['ship_balance'];
						$group[(string)$BOI['_id']]['salesorder'][$BOI['code']]['products'][$products['code']]['product_shipped'] = $products['quantity'] - $products['ship_balance'];
						$group[(string)$BOI['_id']]['salesorder'][$BOI['code']]['products'][$products['code']]['balance_value'] = 0;
						if(isset($products['sell_price'])&&$products['sell_price']!='')
							$group[(string)$BOI['_id']]['salesorder'][$BOI['code']]['products'][$products['code']]['balance_value'] = $products['ship_balance'] * $products['sell_price'];
						if(!isset($group[(string)$BOI['_id']]['salesorder'][$BOI['code']['SO_total']]))
							$group[(string)$BOI['_id']]['salesorder'][$BOI['code']]['SO_total'] = 0;
						$group[(string)$BOI['_id']]['salesorder'][$BOI['code']]['SO_total'] += $group[(string)$BOI['_id']]['salesorder'][$BOI['code']]['products'][$products['code']]['balance_value'];
						if(!isset($group[(string)$BOI['_id']]['total']))
							$group[(string)$BOI['_id']]['total'] = 0;
						$group[(string)$BOI['_id']]['total'] += $group[(string)$BOI['_id']]['salesorder'][$BOI['code']]['SO_total'];

					}
				}
			}
			if($group!='')
			{
				$html = '';
				foreach($group as $value)
				{
					$html .= '
						<table cellpadding="3" cellspacing="0" class="maintb">
							<tr>
								<td width="30%" class="first top">
									Company Name
								</td>
								<td width="70%" class="top">
									Total Balance Value
								</td>
							</tr>
							<tr style="background-color:#fdfcfa;">
								<td class="content bottom">
									'.$value['company_name'].'
								</td>
								<td class="content bottom" align="right" >
									'.$this->opm->format_currency($value['total']).'
								</td>
							</tr>
						</table>
						<div style="border-bottom: 1px solid #ffffff; height:1px;width: 70% ; clear:both"></div><br />';
					foreach($value['salesorder'] as $code=>$salesorder)
					{
						$html .= '
						<table cellpadding="3" cellspacing="0" class="maintb">
							<tr>
								<td width="10%" class="first top">SO#</td>
								<td width="50%" class="top">Date</td>
								<td width="40%" class="top">SO Balance Value</td>
							</tr>
							<tr style="background-color:#fdfcfa;">
								<td class="content bottom">'.$code.'</td>
								<td class="content bottom">'.$this->opm->format_date($salesorder['salesorder_date']).'</td>
								<td align="right" class="content bottom">'.$this->opm->format_currency($salesorder['SO_total']).'</td>
							</tr>
						</table>
						';
						$html .='
						<table  cellpadding="3" cellspacing="0" class="maintb">
							<tr>
								<td width="15%" class="first top">Product Code</td>
								<td width="30%" class="top">Product Name</td>
								<td width="10%" class="top">Quantity</td>
								<td width="10%" class="top">Shipping</td>
								<td width="10%" class="top">Balance</td>
								<td width="25%" class="top" colspan="2">Balance Value</td>
							</tr>
						';
						$j = 0;
						foreach($salesorder['products'] as $p_code=>$products)
						{
							$color = '#fdfcfa;';
							if($j%2==0)
								$color = '#eeeeee;';
							$html .= '
							<tr style="background-color:'.$color.'">
								<td class="first content">'.$p_code.'</td>
								<td class="content" align="left">'.$products['product_name'].'</td>
								<td class="content">'.$products['product_quantity'].'</td>
								<td class="content">'.$products['product_balance'].'</td>
								<td class="content">'.$products['product_shipped'].'</td>
								<td class="content"  align="right" class="end" colspan="2">'.$this->opm->format_currency($products['balance_value']).'</td>
							</tr>
							';
							$j++;
						}
						$color = '#fdfcfa;';
						if($j%2==0)
							$color = '#eeeeee;';
						$html .= '
							<tr style="background-color:'.$color.'">
								<td colspan="3" align="left" class="first bottom">'.$j.' record(s) listed.</td>
								<td class="bottom">&nbsp;</td>
								<td class="bottom">&nbsp;</td>
								<td align="left" class="bottom"><span style="font-weight:bold; padding-left:20px">Total value:   </span></td>
								<td align="right" class="content bottom">'.$this->opm->format_currency($salesorder['SO_total']).'</td>
							</tr>
						</table>

						';
					}
					$html .= '<div style="border-bottom: 1px dashed #eeeeee; height:1px;width: 70% ; clear:both"></div><br />';

				}
				 //========================================
				$pdf['current_time'] = date('h:i a m/d/Y');
				$pdf['title'] = '<span style="color:#b32017">B</span>ack <span style="color:#b32017">O</span>rder <span style="color:#b32017">I</span>voicing <span style="color:#b32017">R</span>eport <br />';
				$this->layout = 'pdf';
					//set header
				$pdf['logo_link'] = 'img/logo_anvy.jpg';
				$pdf['company_address'] = '3145 - 5th Ave NE<br />Calgary  AB  T2A  6A3<br />';
				$pdf['html_loop'] = $html;
				$pdf['filename'] = 'SO_'.md5($pdf['current_time']);

				$this->report_pdf($pdf);
				echo json_encode(array('status'=>'ok','url'=>URL.'/upload/'.$pdf['filename'].'.pdf'));
			}
		}
		die;

	}
	public function back_order_shipping()
	{
		$status = $this->check_condition_back_order('shipping');

		if($status == false)
		{
			echo json_encode(array('status'=>'error','mess'=>'There are currently no outstading back orders for shipping.'));
			return false;
		}
		else
		{
			$group = array();
			foreach($status as $BOS)
			{
				foreach($BOS['products'] as $products)
				{
					if($products['deleted']==false&&isset($products['ship_balance']))
					{
						$group[(string)$BOS['_id']]['company_name'] = $BOS['company_name'];
						$group[(string)$BOS['_id']]['salesorder'][$BOS['code']]['salesorder_date'] = $BOS['salesorder_date'];
						$group[(string)$BOS['_id']]['salesorder'][$BOS['code']]['products'][$products['code']]['product_name'] = $products['products_name'];
						$group[(string)$BOS['_id']]['salesorder'][$BOS['code']]['products'][$products['code']]['product_quantity'] = $products['quantity'];
						$group[(string)$BOS['_id']]['salesorder'][$BOS['code']]['products'][$products['code']]['product_balance'] = $products['ship_balance'];
						$group[(string)$BOS['_id']]['salesorder'][$BOS['code']]['products'][$products['code']]['product_shipped'] = $products['quantity'] - $products['ship_balance'];
						$group[(string)$BOS['_id']]['salesorder'][$BOS['code']]['products'][$products['code']]['balance_value'] = 0;
						if(isset($products['sell_price'])&&$products['sell_price']!='')
							$group[(string)$BOS['_id']]['salesorder'][$BOS['code']]['products'][$products['code']]['balance_value'] = $products['ship_balance'] * $products['sell_price'];
						if(!isset($group[(string)$BOS['_id']]['salesorder'][$BOS['code']]['SO_total']))
							$group[(string)$BOS['_id']]['salesorder'][$BOS['code']]['SO_total'] = 0;
						$group[(string)$BOS['_id']]['salesorder'][$BOS['code']]['SO_total'] += $group[(string)$BOS['_id']]['salesorder'][$BOS['code']]['products'][$products['code']]['balance_value'];
						if(!isset($group[(string)$BOS['_id']]['total']))
							$group[(string)$BOS['_id']]['total'] = 0;
						$group[(string)$BOS['_id']]['total'] += $group[(string)$BOS['_id']]['salesorder'][$BOS['code']]['products'][$products['code']]['balance_value'];

					}
				}
			}
			if($group!='')
			{
				$html = '';
				foreach($group as $value)
				{
					$html .= '
						<table cellpadding="3" cellspacing="0" class="maintb">
							<tr>
								<td width="30%" class="first top">
									Company Name
								</td>
								<td width="70%" class="top">
									Total Balance Value
								</td>
							</tr>
							<tr style="background-color:#fdfcfa;">
								<td class="content bottom">
									'.$value['company_name'].'
								</td>
								<td class="content bottom" align="right" >
									'.$this->opm->format_currency($value['total']).'
								</td>
							</tr>
						</table>
						<div style="border-bottom: 1px solid #ffffff; height:1px;width: 70% ; clear:both"></div><br />';
					foreach($value['salesorder'] as $code=>$salesorder)
					{
						$html .= '
						<table cellpadding="3" cellspacing="0" class="maintb">
							<tr>
								<td width="10%" class="first top">SO#</td>
								<td width="50%" class="top">Date</td>
								<td width="40%" class="top">SO Balance Value</td>
							</tr>
							<tr style="background-color:#fdfcfa;">
								<td class="content bottom">'.$code.'</td>
								<td class="content bottom">'.$this->opm->format_date($salesorder['salesorder_date']).'</td>
								<td align="right" class="content bottom">'.$this->opm->format_currency($salesorder['SO_total']).'</td>
							</tr>
						</table>
						';
						$html .='
						<table  cellpadding="3" cellspacing="0" class="maintb">
							<tr>
								<td width="15%" class="first top">Product Code</td>
								<td width="30%" class="top">Product Name</td>
								<td width="10%" class="top">Quantity</td>
								<td width="10%" class="top">Shipping</td>
								<td width="10%" class="top">Balance</td>
								<td width="25%" class="top" colspan="2">Balance Value</td>
							</tr>
						';
						$j = 0;
						foreach($salesorder['products'] as $p_code=>$products)
						{
							$color = '#fdfcfa;';
							if($j%2==0)
								$color = '#eeeeee;';
							$html .= '
							<tr style="background-color:'.$color.'">
								<td class="first content">'.$p_code.'</td>
								<td class="content" align="left">'.$products['product_name'].'</td>
								<td class="content">'.$products['product_quantity'].'</td>
								<td class="content">'.$products['product_balance'].'</td>
								<td class="content">'.$products['product_shipped'].'</td>
								<td class="content"  align="right" class="end" colspan="2">'.$this->opm->format_currency($products['balance_value']).'</td>
							</tr>
							';
							$j++;
						}
						$color = '#fdfcfa;';
						if($j%2==0)
							$color = '#eeeeee;';
						$html .= '
							<tr style="background-color:'.$color.'">
								<td colspan="3" align="left" class="first bottom">'.$j.' record(s) listed.</td>
								<td class="bottom">&nbsp;</td>
								<td class="bottom">&nbsp;</td>
								<td align="left" class="bottom"><span style="font-weight:bold; padding-left:20px">Total value:   </span></td>
								<td align="right" class="content bottom">'.$this->opm->format_currency($salesorder['SO_total']).'</td>
							</tr>
						</table>

						';
					}
					$html .= '<div style="border-bottom: 1px dashed #eeeeee; height:1px;width: 70% ; clear:both"></div><br />';

				}
				 //========================================
				$pdf['current_time'] = date('h:i a m/d/Y');
				$pdf['title'] = '<span style="color:#b32017">B</span>ack <span style="color:#b32017">O</span>rder <span style="color:#b32017">S</span>hipping <span style="color:#b32017">R</span>eport <br />';
				$this->layout = 'pdf';
					//set header
				$pdf['logo_link'] = 'img/logo_anvy.jpg';
				$pdf['company_address'] = '3145 - 5th Ave NE<br />Calgary  AB  T2A  6A3<br />';
				$pdf['html_loop'] = $html;
				$pdf['filename'] = 'SO_'.md5($pdf['current_time']);

				$this->report_pdf($pdf);
				echo json_encode(array('status'=>'ok','url'=>URL.'/upload/'.$pdf['filename'].'.pdf'));
			}
		}
		die;
	}


	public function check_full_balance(){
		$salesorder_id = $this->get_id();
		if($salesorder_id!=''){
			$arr_salesorder = $this->opm->select_one(array('_id'=>new MongoId($salesorder_id)));
		}
		$arr_product=isset($arr_salesorder['products'])?$arr_salesorder['products']:array();
		$post = $_POST['shipped'];
		if(isset($_POST['shipped']) && count($_POST['shipped'])>0 && isset($arr_product) && is_array($arr_product) && count($arr_product)>0){
			foreach($arr_salesorder['products'] as $key=>$values){
				if(isset($post[$key])){
					if($_POST['type_sales']=='shipping'){
						if($post[$key]>$arr_salesorder['products'][$key]['balance_shipped']){
							echo 'invalid_shipped_'.$key;
						}
					}
					elseif($_POST['type_sales']=='invoice'){
						if($post[$key]>$arr_salesorder['products'][$key]['balance_invoiced']){
							echo 'invalid_invoiced_'.$key;
						}
					}
					elseif($_POST['type_sales']=='both'){
						if(($post[$key]>$arr_salesorder['products'][$key]['balance_invoiced'])||($post[$key]>$arr_salesorder['products'][$key]['balance_shipped'])){
							echo 'invalid_all_'.$key;
						}
					}
				}echo ',';
			}
			die;
		}
		die;
	}

	public function part_shipping()
	{
		$line_data = $this->line_entry_data();
		foreach($line_data['products'] as $key => $product) {
			if( isset($product['same_parent']) && $product['same_parent'] ) {
				$line_data['products'][$key]['xempty']['ship_qty'] = true;
				$line_data['products'][$key]['xempty']['shipped'] = true;
				$line_data['products'][$key]['xempty']['balance_shipped'] = true;
			} else if( isset($product['balance_shipped']) && !$product['balance_shipped'] ) {
				$line_data['products'][$key]['xempty']['ship_qty'] = true;
			}
		}
		$this->set('subdatas', array('part_shipping' => $line_data['products']) );
		$this->set('sub_tab','part_shipping');
	}

	public function create_part_shipping()
	{
		if(!empty($_POST)) {
			$arr_post = array();
			foreach($_POST as $key => $value) {
				$value = (float)$value;
				if( !$value ) continue;
				$arr_post[str_replace('ship_qty_', '', $key)] = $value;
			}
			$order = $this->opm->select_one(array('_id' => new MongoId($this->get_id())));
			$products = array();
			foreach($order['products'] as $key => $product) {
				if(isset($arr_post[$key])) {
					$order['products'][$key]['shipped'] = $arr_post[$key];
					$order['products'][$key]['balance_shipped'] = $order['products'][$key]['quantity'] - $arr_post[$key];
					$product['quantity'] = $arr_post[$key];
					$products[$key] = $product;
				} else {
					$products[$key] = ['deleted' => true];
				}
			}
			$tmp = $order['products'];
			foreach($products as $key => $product){
				if( $product['deleted'] || isset($product['option_for']) ) continue;
				foreach($tmp as $k => $v){
					if( $v['deleted'] || !isset($v['option_for']) || !isset($v['same_parent']) || !$v['same_parent'] ) {
						unset($tmp[$k]);
						continue;
					}

					if( $v['option_for'] == $key ) {
						$products[$k] = $v;
					}

				}
			}
			if( !empty($products) ) {
				$this->selectModel('Shipping');
				$new_array = $order;
				$new_array['deleted'] = false;
				$new_array['code'] = $this->Shipping->get_auto_code('code');
				$new_array['products'] = $products;
				$new_array['shipping_type'] = 'Out';
				$new_array['shipping_date'] = new MongoDate();
				$new_array['return_status'] = 0;
				$new_array['shipping_status'] = 'In progress';
				$new_array['shipping_cost'] = '';
				$new_array['shipper'] = '';
				$new_array['shipper_id'] = '';
				$new_array['tracking_no'] = '';
				$new_array['received_date'] = '';
				$new_array['carrier_id'] = '';
				$new_array['carrier_name'] = '';
				$new_array['salesorder_id'] = $order['_id'];
				$new_array['salesorder_number'] = $order['code'];
				$new_array['salesorder_name'] = $order['name'];
				unset($new_array['_id']);
				$this->Shipping->save($new_array);
				$this->opm->save($order);
			}
		}
		echo 'ok';
		die;
	}

	public function receive_item() {
        $arr_ret = $this->line_entry_data();
        //pr($arr_ret['products']);die;
		$this->set('subdatas', array('part_invoice' =>$arr_ret['products']) );
		$this->set('sub_tab','part_invoice');

		$salesorder_id = $this->get_id();
		$this->selectModel('Salesorder');
		$arr_salesorders = $this->Somonth->select_one(array('_id' => new MongoId($salesorder_id)),array('products'));

		$salesorder_id = $this->get_id();
		$arr_salesorder = $this->opm->select_one(array('_id'=>new MongoId($salesorder_id)));
		if(!empty($_POST)){
			if($_POST['type_sales']=='shipping'){
				//tao shipping
				$arr_shipped=array();
				$this->selectModel('Shipping');
				if(isset($arr_salesorder['company_id']))
					$arr_shipped=$this->arr_associated_data('company_name',$arr_salesorder['company_name'],$arr_salesorder['company_id']);

				$arr_shipped['salesorder_id']=isset($salesorder_id)?new MongoId($salesorder_id):'';
				$arr_shipped['code']=$this->Shipping->get_auto_code('code');
				$arr_shipped['shipper']=isset($arr_salesorder['shipper'])?$arr_salesorder['shipper']:'';
				$arr_shipped['shipper_id']=isset($arr_salesorder['shipper_id'])?$arr_salesorder['shipper_id']:'';
				$arr_shipped['shipping_type']='Out';
				$arr_shipped['shipping_status']='In progress';
				$arr_shipped['shipping_date']=new MongoDate(time());
				$arr_shipped['salesorder_number']=isset($arr_salesorder['code'])?$arr_salesorder['code']:'';
				$arr_shipped['salesorder_name']=isset($arr_salesorder['name'])?$arr_salesorder['name']:'';
				if(isset($arr_salesorder['our_rep']) && isset($arr_salesorder['our_rep_id']) && $arr_salesorder['our_rep_id']!=''){
					$arr_shipped['our_rep_id'] = $arr_salesorder['our_rep_id'];
					$arr_shipped['our_rep'] = $arr_salesorder['our_rep'];
				}else{
					$arr_shipped['our_rep_id'] = $this->opm->user_id();
					$arr_shipped['our_rep'] = $this->opm->user_name();
				}
				if(isset($arr_salesorder['our_csr']) && isset($arr_salesorder['our_csr_id']) && $arr_salesorder['our_csr_id']!=''){
					$arr_shipped['our_csr_id'] = $arr_salesorder['our_csr_id'];
					$arr_shipped['our_csr'] = $arr_salesorder['our_csr'];
				}else{
					$arr_shipped['our_csr_id'] = $this->opm->user_id();
					$arr_shipped['our_csr'] = $this->opm->user_name();
				}
				$arr_shipped['heading']=isset($arr_salesorder['heading'])?$arr_salesorder['heading']:'';
				$arr_shipped['job_name']=isset($arr_salesorder['job_name'])?$arr_salesorder['job_name']:'';
				$arr_shipped['job_number']=isset($arr_salesorder['job_number'])?$arr_salesorder['job_number']:'';
				$arr_shipped['job_id']=isset($arr_salesorder['job_id'])?$arr_salesorder['job_id']:'';
				$arr_shipped['products']= $arr_salesorders['products'];
				$arr_shipped['payment_terms']=isset($arr_salesorder['payment_terms'])?$arr_salesorder['payment_terms']:0;
				if(isset($arr_shipped['payment_terms']))
					$arr_shipped['payment_due_date'] = new MongoDate((int)$arr_shipped['payment_terms']*86400 + (int)time());
				if(isset($arr_salesorder['shipping_address'])&&is_array($arr_salesorder['shipping_address'])){
					if(isset($arr_shipped['addresses_default_key']))
						$v_default=$arr_shipped['addresses_default_key'];
					else
						$v_default=0;
					if((isset($arr_salesorder['shipping_address'][0]['shipping_address_1'])&&$arr_salesorder['shipping_address'][0]['shipping_address_1']!=''&&$arr_salesorder['shipping_address'][0]['shipping_address_1']!=0)
						||(isset($arr_salesorder['shipping_address'][0]['shipping_address_2'])&&$arr_salesorder['shipping_address'][0]['shipping_address_2']!=''&&$arr_salesorder['shipping_address'][0]['shipping_address_2']!=0)
						||(isset($arr_salesorder['shipping_address'][0]['shipping_address_3'])&&$arr_salesorder['shipping_address'][0]['shipping_address_3']!=''&&$arr_salesorder['shipping_address'][0]['shipping_address_3']!=0)
						||(isset($arr_salesorder['shipping_address'][0]['shipping_town_city'])&&$arr_salesorder['shipping_address'][0]['shipping_town_city']!=''&&$arr_salesorder['shipping_address'][0]['shipping_town_city']!=0)
						||(isset($arr_salesorder['shipping_address'][0]['shipping_province_state'])&&$arr_salesorder['shipping_address'][0]['shipping_province_state']!=''&&$arr_salesorder['shipping_address'][0]['shipping_province_state']!=0)
						||(isset($arr_salesorder['shipping_address'][0]['shipping_province_state_id'])&&$arr_salesorder['shipping_address'][0]['shipping_province_state_id']!=''&&$arr_salesorder['shipping_address'][0]['shipping_province_state_id']!=0)
						||(isset($arr_salesorder['shipping_address'][0]['shipping_zip_postcode'])&&$arr_salesorder['shipping_address'][0]['shipping_zip_postcode']!=''&&$arr_salesorder['shipping_address'][0]['shipping_zip_postcode']!=0)
					){
						$arr_shipped['shipping_address']=$arr_salesorder['shipping_address'];
					}
					else{
						if(is_array($arr_shipped['invoice_address'])){
							foreach($arr_shipped['invoice_address'][$v_default] as $ka=>$va){
								if($ka!='deleted'){
									$ka1=substr($ka, 8);
									$arr_shipped['shipping_address'][0]['shipping_'.$ka1] = $va;
								}
								else
									$arr_shipped['shipping_address'][0][$ka] = $va;
							}
						}
					}
				}
				if ($this->Shipping->save($arr_shipped)){
					//$this->opm->save($arr_temp);
					echo '/shippings/entry/'.$this->Shipping->mongo_id_after_save;
					die;
				}
			}elseif($_POST['type_sales']=='invoice'){
				$arr_invoiced=array();
				$this->selectModel('Salesinvoice');
				if(isset($arr_salesorder['company_id']))
					$arr_invoiced=$this->arr_associated_data('company_name',$arr_salesorder['company_name'],$arr_salesorder['company_id']);
				$arr_invoiced['salesorder_id']=isset($salesorder_id)?new MongoId($salesorder_id):'';
				$arr_invoiced['code']=$this->Salesinvoice->get_auto_code('code');
				$arr_invoiced['invoice_type']='Invoice';
				$arr_invoiced['invoice_status']='Invoiced';
				$arr_invoiced['invoice_date']=new MongoDate(time());
				$arr_invoiced['payment_due_date']=new MongoDate(time());
				$arr_invoiced['salesorder_number']=isset($arr_salesorder['code'])?$arr_salesorder['code']:'';
				$arr_invoiced['salesorder_name']=isset($arr_salesorder['name'])?$arr_salesorder['name']:'';
				$arr_invoiced['job_name']=isset($arr_salesorder['job_name'])?$arr_salesorder['job_name']:'';
				$arr_invoiced['job_number']=isset($arr_salesorder['job_number'])?$arr_salesorder['job_number']:'';
				$arr_invoiced['job_id']=isset($arr_salesorder['job_id'])?$arr_salesorder['job_id']:'';
				$arr_invoiced['is_part_invoice'] = '1';
				if(isset($arr_salesorder['our_rep']) && isset($arr_salesorder['our_rep_id']) && $arr_salesorder['our_rep_id']!=''){
					$arr_invoiced['our_rep_id'] = $arr_salesorder['our_rep_id'];
					$arr_invoiced['our_rep'] = $arr_salesorder['our_rep'];
				}else{
					$arr_invoiced['our_rep_id'] = $this->opm->user_id();
					$arr_invoiced['our_rep'] = $this->opm->user_name();
				}

				if(isset($arr_salesorder['our_csr']) && isset($arr_salesorder['our_csr_id']) && $arr_salesorder['our_csr_id']!=''){
					$arr_invoiced['our_csr_id'] = $arr_salesorder['our_csr_id'];
					$arr_invoiced['our_csr'] = $arr_salesorder['our_csr'];
				}else{
					$arr_invoiced['our_csr_id'] = $this->opm->user_id();
					$arr_invoiced['our_csr'] = $this->opm->user_name();
				}
				$arr_invoiced['products']=$arr_salesorders['products'];
				//$arr_invoiced['sum_amount']=$total_amount;
				$arr_invoiced['payment_terms']=isset($arr_salesorder['payment_terms'])?$arr_salesorder['payment_terms']:0;
				if(isset($arr_invoiced['payment_terms']))
					$arr_invoiced['payment_due_date'] = new MongoDate((int)$arr_invoiced['payment_terms']*86400 + (int)time());
				if ($this->Salesinvoice->save($arr_invoiced)) {
					// BaoNam: cập nhật lại Sales Account
					if(isset($arr_invoiced['sum_amount']) && $arr_invoiced['sum_amount'] > 0){
						$this->selectModel('Salesaccount');
						if(isset($arr_invoiced['company_id']) && is_object($arr_invoiced['company_id'])){
							$this->Salesaccount->update_account($arr_invoiced['company_id'], array(
																'model' => 'Company',
																'balance' => $arr_invoiced['sum_amount'],
																'invoices_credits' => $arr_invoiced['sum_amount'],
																));
						}elseif(isset($arr_invoiced['contact_id'])){
							$this->Salesaccount->update_account($arr_invoiced['contact_id'], array(
																'model' => 'Contact',
																'balance' => $arr_invoiced['sum_amount'],
																'invoices_credits' => $arr_invoiced['sum_amount'],
																));
						}
					}
					//$this->opm->save($arr_temp);
					echo '/salesinvoices/entry/'.$this->Salesinvoice->mongo_id_after_save;
					die;

				}
			}elseif($_POST['type_sales']=='both'){
				$arr_shipped=array();
				$this->selectModel('Shipping');
				if(isset($arr_salesorder['company_id']))
					$arr_shipped=$this->arr_associated_data('company_name',$arr_salesorder['company_name'],$arr_salesorder['company_id']);
				$arr_shipped['salesorder_id']=isset($salesorder_id)?new MongoId($salesorder_id):'';
				$arr_shipped['code']=$this->Shipping->get_auto_code('code');
				$arr_shipped['shipper']=isset($arr_salesorder['shipper'])?$arr_salesorder['shipper']:'';
				$arr_shipped['shipper_id']=isset($arr_salesorder['shipper_id'])?$arr_salesorder['shipper_id']:'';
				$arr_shipped['shipping_type']='Out';
				$arr_shipped['shipping_status']='In progress';
				$arr_shipped['shipping_date']=new MongoDate(time());
				$arr_shipped['salesorder_number']=isset($arr_salesorder['code'])?$arr_salesorder['code']:'';
				$arr_shipped['salesorder_name']=isset($arr_salesorder['name'])?$arr_salesorder['name']:'';
				$arr_shipped['job_name']=isset($arr_salesorder['job_name'])?$arr_salesorder['job_name']:'';
				$arr_shipped['job_number']=isset($arr_salesorder['job_number'])?$arr_salesorder['job_number']:'';
				$arr_shipped['job_id']=isset($arr_salesorder['job_id'])?$arr_salesorder['job_id']:'';
				$arr_shipped['products']=$arr_salesorders['products'];
				if(isset($arr_salesorder['our_rep']) && isset($arr_salesorder['our_rep_id']) && $arr_salesorder['our_rep_id']!=''){
					$arr_shipped['our_rep_id'] = $arr_salesorder['our_rep_id'];
					$arr_shipped['our_rep'] = $arr_salesorder['our_rep'];
				}else{
					$arr_shipped['our_rep_id'] = $this->opm->user_id();
					$arr_shipped['our_rep'] = $this->opm->user_name();
				}
				if(isset($arr_salesorder['our_csr']) && isset($arr_salesorder['our_csr_id']) && $arr_salesorder['our_csr_id']!=''){
					$arr_shipped['our_csr_id'] = $arr_salesorder['our_csr_id'];
					$arr_shipped['our_csr'] = $arr_salesorder['our_csr'];
				}else{
					$arr_shipped['our_csr_id'] = $this->opm->user_id();
					$arr_shipped['our_csr'] = $this->opm->user_name();
				}
				if(isset($arr_salesorder['shipping_address'])&&is_array($arr_salesorder['shipping_address'])){
					if(isset($arr_shipped['addresses_default_key']))
						$v_default=$arr_shipped['addresses_default_key'];
					else
						$v_default=0;
					if((isset($arr_salesorder['shipping_address'][0]['shipping_address_1'])&&$arr_salesorder['shipping_address'][0]['shipping_address_1']!=''&&$arr_salesorder['shipping_address'][0]['shipping_address_1']!=0)
						||(isset($arr_salesorder['shipping_address'][0]['shipping_address_2'])&&$arr_salesorder['shipping_address'][0]['shipping_address_2']!=''&&$arr_salesorder['shipping_address'][0]['shipping_address_2']!=0)
						||(isset($arr_salesorder['shipping_address'][0]['shipping_address_3'])&&$arr_salesorder['shipping_address'][0]['shipping_address_3']!=''&&$arr_salesorder['shipping_address'][0]['shipping_address_3']!=0)
						||(isset($arr_salesorder['shipping_address'][0]['shipping_town_city'])&&$arr_salesorder['shipping_address'][0]['shipping_town_city']!=''&&$arr_salesorder['shipping_address'][0]['shipping_town_city']!=0)
						||(isset($arr_salesorder['shipping_address'][0]['shipping_province_state'])&&$arr_salesorder['shipping_address'][0]['shipping_province_state']!=''&&$arr_salesorder['shipping_address'][0]['shipping_province_state']!=0)
						||(isset($arr_salesorder['shipping_address'][0]['shipping_province_state_id'])&&$arr_salesorder['shipping_address'][0]['shipping_province_state_id']!=''&&$arr_salesorder['shipping_address'][0]['shipping_province_state_id']!=0)
						||(isset($arr_salesorder['shipping_address'][0]['shipping_zip_postcode'])&&$arr_salesorder['shipping_address'][0]['shipping_zip_postcode']!=''&&$arr_salesorder['shipping_address'][0]['shipping_zip_postcode']!=0)
					){
						$arr_shipped['shipping_address']=$arr_salesorder['shipping_address'];
					}else{
						if(is_array($arr_shipped['invoice_address'])){
							foreach($arr_shipped['invoice_address'][$v_default] as $ka=>$va){
								if($ka!='deleted'){
									$ka1=substr($ka, 8);
									$arr_shipped['shipping_address'][0]['shipping_'.$ka1] = $va;
								}
								else{
									$arr_shipped['shipping_address'][0][$ka] = $va;
								}

							}
						}
					}
				}
				$arr_shipped['payment_terms']=isset($arr_salesorder['payment_terms'])?$arr_salesorder['payment_terms']:0;
				if(isset($arr_shipped['payment_terms']))
					$arr_shipped['payment_due_date'] = new MongoDate((int)$arr_shipped['payment_terms']*86400 + (int)time());
				$arr_invoiced=array();
				$this->selectModel('Salesinvoice');
				if(isset($arr_salesorder['company_id']))
					$arr_invoiced=$this->arr_associated_data('company_name',$arr_salesorder['company_name'],$arr_salesorder['company_id']);
				$arr_invoiced['salesorder_id']=isset($salesorder_id)?new MongoId($salesorder_id):'';
				$arr_invoiced['code']=$this->Salesinvoice->get_auto_code('code');
				$arr_invoiced['invoice_type']='Invoice';
				$arr_invoiced['invoice_status']='Invoiced';
				$arr_invoiced['invoice_date']=new MongoDate(time());
				$arr_invoiced['payment_due_date']=new MongoDate(time());
				$arr_invoiced['salesorder_number']=isset($arr_salesorder['code'])?$arr_salesorder['code']:'';
				$arr_invoiced['salesorder_name']=isset($arr_salesorder['name'])?$arr_salesorder['name']:'';
				$arr_invoiced['job_name']=isset($arr_salesorder['job_name'])?$arr_salesorder['job_name']:'';
				$arr_invoiced['job_number']=isset($arr_salesorder['job_number'])?$arr_salesorder['job_number']:'';
				$arr_invoiced['job_id']=isset($arr_salesorder['job_id'])?$arr_salesorder['job_id']:'';
				if(isset($arr_salesorder['our_rep']) && isset($arr_salesorder['our_rep_id']) && $arr_salesorder['our_rep_id']!=''){
					$arr_invoiced['our_rep_id'] = $arr_salesorder['our_rep_id'];
					$arr_invoiced['our_rep'] = $arr_salesorder['our_rep'];
				}else{
					$arr_invoiced['our_rep_id'] = $this->opm->user_id();
					$arr_invoiced['our_rep'] = $this->opm->user_name();
				}
				if(isset($arr_salesorder['our_csr']) && isset($arr_salesorder['our_csr_id']) && $arr_salesorder['our_csr_id']!=''){
					$arr_invoiced['our_csr_id'] = $arr_salesorder['our_csr_id'];
					$arr_invoiced['our_csr'] = $arr_salesorder['our_csr'];
				}else{
					$arr_invoiced['our_csr_id'] = $this->opm->user_id();
					$arr_invoiced['our_csr'] = $this->opm->user_name();
				}
				$arr_invoiced['products']=$arr_salesorders['products'];
				//$arr_invoiced['sum_amount']=$total_amount;
				$arr_invoiced['payment_terms']=isset($arr_salesorder['payment_terms'])?$arr_salesorder['payment_terms']:0;
				if(isset($arr_invoiced['payment_terms']))
					$arr_invoiced['payment_due_date'] = new MongoDate((int)$arr_invoiced['payment_terms']*86400 + (int)time());
				if ($this->Salesinvoice->save($arr_invoiced)) {
					// BaoNam: cập nhật lại Sales Account
					if(isset($arr_invoiced['sum_amount']) && $arr_invoiced['sum_amount'] > 0){
						$this->selectModel('Salesaccount');
						if(isset($arr_invoiced['company_id']) && is_object($arr_invoiced['company_id'])){
							$this->Salesaccount->update_account($arr_invoiced['company_id'], array(
																'model' => 'Company',
																'balance' => $arr_invoiced['sum_amount'],
																'invoices_credits' => $arr_invoiced['sum_amount'],
																));
						}elseif(isset($arr_invoiced['contact_id'])){
							$this->Salesaccount->update_account($arr_invoiced['contact_id'], array(
																'model' => 'Contact',
																'balance' => $arr_invoiced['sum_amount'],
																'invoices_credits' => $arr_invoiced['sum_amount'],
																));
						}
					}
					$arr_shipped['salesinvoice_id']=isset($this->Salesinvoice->mongo_id_after_save)?$this->Salesinvoice->mongo_id_after_save:'';
					$arr_shipped['salesinvoice_number']=isset($arr_invoiced['code'])?$arr_invoiced['code']:'';
					$arr_shipped['salesinvoice_name']=isset($arr_invoiced['name'])?$arr_invoiced['name']:'';
					if($this->Shipping->save($arr_shipped)){
						//$this->opm->save($arr_temp);
						echo '/salesorders/entry/'.$salesorder_id;
						die;
					}
				}
			}
		}
	}

	public function ship_invoice(){
		if(!$this->check_permission('shippings_@_entry_@_view')
			&&!$this->check_permission('salesinvoices_@_entry_@_view'))
			$this->error_auth();
		$subdatas = array();
		$subdatas['invoice'] = array();
		$subdatas['shipping'] = array();

		$this->selectModel('Salesinvoice');
		$this->selectModel('Shipping');

		$ids = $this->get_id();
		$total_invoice = 0;
		$order = $invoices = $shippings = array();
		if ($ids != '') {
			$order = $this->opm->select_one(array('_id'=>new MongoId($ids)),array('sum_amount', 'status'));

			$invoices = $this->Salesinvoice->select_all(array(
				'arr_where' => array('salesorder_id' => new MongoId($ids)),
				'arr_order' => array('_id' => -1),
				'arr_field' => array('sum_amount', 'code', 'invoice_type', 'invoice_date', 'invoice_status', 'our_rep', 'other_comment')
			));

			foreach($invoices as $invoice) {
				$total_invoice += $invoice['sum_amount'];
			}

			$shippings = $this->Shipping->select_all(array(
				'arr_where' => array('salesorder_id' => new MongoId($ids)),
				'arr_order' => array('_id' => -1),
				'arr_field' => array('code', 'shipping_type', 'return_status', 'shipping_date', 'shipping_status', 'our_rep', 'shipper')
			));
		}
		$sum_amount = 0;
		if( isset($order['sum_amount']) ){
			if($order['status'] == 'Cancelled')
				$order['sum_amount'] = 0;
			$sum_amount = $order['sum_amount'];
		}
		$this->set('sum_amount', $sum_amount);
		$this->set('total_invoice', $total_invoice);
		$subdatas['invoice'] = $invoices;
		$subdatas['shipping'] = $shippings;
		$this->set('subdatas', $subdatas);
	}

	public function shippings()
	{
		if(!$this->check_permission('shippings_@_entry_@_view')) {
			$this->error_auth();
		}
		$ids = $this->get_id();
		$this->selectModel('Shipping');
		$shippings = $this->Shipping->select_all(array(
			'arr_where' => array('salesorder_id' => new MongoId($ids)),
			'arr_order' => array('_id' => -1),
			'arr_field' => array('code', 'shipping_type', 'return_status', 'shipping_date', 'shipping_status', 'our_rep', 'shipper', 'other_comment')
		));
		$shipping_comment = '';
		$order = $this->opm->select_one(array('_id' => new MongoId($ids)), array('shipping_comment'));
		if (isset($order['shipping_comment'])) {
			$shipping_comment = $order['shipping_comment'];
		}
		$this->set('subdatas', array('shippings' => $shippings, 'shipping_comment' => array('shipping_comment' => $shipping_comment)));
	}

	public function invoices()
	{
		if(!$this->check_permission('salesinvoices_@_entry_@_view')) {
			$this->error_auth();
		}
		$ids = $this->get_id();
		$this->selectModel('Salesinvoice');
		$total_invoice = 0;
		$invoices = $this->Salesinvoice->select_all(array(
			'arr_where' => array('salesorder_id' => new MongoId($ids)),
			'arr_order' => array('_id' => -1),
			'arr_field' => array('sum_amount', 'code', 'invoice_type', 'invoice_date', 'invoice_status', 'our_rep', 'other_comment')
		));

		foreach($invoices as $invoice) {
			$total_invoice += $invoice['sum_amount'];
		}
		$order = $this->opm->select_one(array('_id'=>new MongoId($ids)),array('sum_amount', 'status'));
		$sum_amount = 0;
		if( isset($order['sum_amount']) ){
			if($order['status'] == 'Cancelled')
				$order['sum_amount'] = 0;
			$sum_amount = $order['sum_amount'];
		}
		$this->set('sum_amount', $sum_amount);
		$this->set('total_invoice', $total_invoice);
		$this->set('subdatas', array('invoices' => $invoices));
	}

	function save_asset_tag(){
		if(isset($_POST)){
			$query = $this->opm->select_one(array('_id' => new MongoId($this->get_id())),array('asset_tags'));
			if($_POST['last_change_field'] == 'completed')
				unset($_POST['last_change_field']);
			$query['asset_tags'][$_POST['asset_key']] = array_merge($query['asset_tags'][$_POST['asset_key']] , $_POST);
			if(isset($_POST['last_change_field']) && $_POST['last_change_field'] != 'production_time') {
				$query['asset_tags'][$_POST['asset_key']]['production_time'] = $this->cal_production_time($query['asset_tags'][$_POST['asset_key']]);
			}
			echo $query['asset_tags'][$_POST['asset_key']]['production_time'];
			$this->opm->save($query);
			die;
		}
		die;
	}

	function create_custom_asset(){
		if(isset($_POST['key']) && !empty($_POST['key'])){
			$key = $_POST['key'];
			$key = explode( '_@_', $key);
			if(strlen($_POST['id']) == 24){
				$this->selectModel('Equipment');
				$equipment = $this->Equipment->select_one(array('_id' => new MongoId($_POST['id'])));
				if(!empty($equipment)) {
					$query = $this->opm->select_one(array('_id' => new MongoId($this->get_id())),array('products','asset_tags'));
					if(!isset($query['asset_tags']))
						$query['asset_tags'] = array();
					if(!empty($query['asset_tags'])) {
						end($query['asset_tags']);
						$this_key = key($query['asset_tags']) +1;
					} else {
						$this_key = 0;
					}
					$query['asset_tags'][] = array(
									'deleted' => false,
									'from' => $this_key,
									'name' => 'Custom',
									'product_id' => '',
									'product_type' => '',
									'oum' => '',
									'tag_key' => $equipment['_id'],
									'factor' => '',
									'min_of_uom' => '',
									'cost_per_hour' => '',
									'tag' => $equipment['name'],
									'_id' => $this_key,
									'for_line_no' => $key[1],
									'product_name' => 'Custom',
									'sell_by' => 'unit',
									'sizew' => '',
									'sizeh' => '',
									'sizew_unit' => '',
									'sizeh_unit' => '',
									'key' => $key[0].'_@_'.$key[1].'_@_'.$this_key.'_@_'.(string)$equipment['_id'],
									'remove_deleted' => 0,
									'id_delete' => 1,
									'is_custom' => 1,
								);
					$this->opm->save($query);
				}
			}
		}
		die;
	}

	function delete_asset(){
		if(isset($_POST['key']) ){
			$query = $this->opm->select_one(array('_id' => new MongoId($this->get_id())),array('asset_tags'));
			if(isset($query['asset_tags'][$_POST['key']])) {
				$key = $query['asset_tags'][$_POST['key']]['key'];
				$query['asset_tags'][$_POST['key']] = array('deleted' => true, 'key' => $key);
			}
			$this->opm->save($query);
			echo 'ok';
		}
		die;
	}

	public function asset_tags($ids = ''){
		if($ids=='')
            $ids = $this->get_id();
		$subdatas['asset_tags'] = array();
		if($ids!=''){
            $key = '';
            if(isset($_POST['data'])){
                $key = $_POST['data'];
                $this->Session->write($this->name.'ViewAssetTag',$key);
            } else if(isset($_SESSION[$this->name.'ViewAssetTag'])){
                $key = $_SESSION[$this->name.'ViewAssetTag'];
            }
            if($key!='all'&&$key!=''){
                $key = explode('_', $key);
                (string)$key = $key[1];
            } else
                $key = '';

            $subdatas['asset_tags'] = $this->asset_tags_data($ids,$key);
        }
		$this->set('subdatas', $subdatas);
		$this->set_select_data_list('relationship', 'asset_tags');

		$list_line_entry = array('all'=>'All');
		$salesorder = $this->opm->select_one(array('_id'=>new MongoId($ids)),array('products'));
		if(!empty($salesorder['products'])){
			foreach($salesorder['products'] as $kk => $product){
				if( isset($product['deleted']) && $product['deleted'] || !is_object($product['products_id']) ) continue;
                if( isset($product['option_for'])&&$product['option_for']!='') continue;
				$list_line_entry['xm_'.$kk] = (isset($product['sku'])&&$product['sku']!='' ? $product['sku'] : 'CODE - '.$product['code'])."  (";
				$list_line_entry['xm_'.$kk] .= $product['sizew']." ".$product['sizew_unit'];
				$list_line_entry['xm_'.$kk] .= " x ".$product['sizeh']." ".$product['sizeh_unit'];
				$list_line_entry['xm_'.$kk] .= ")";

			}
		}
		$line_entry_value = 'All';
        $line_entry_id = 'all';
        if(isset($_POST['data'])){
            $line_entry_value = $list_line_entry[$_POST['data']];
            $line_entry_id = $_POST['data'];
        } else if(isset($_SESSION[$this->name.'ViewAssetTag'])){
            $line_entry_value = 'All';
            $line_entry_id = 'all';
            if(isset($list_line_entry[$_SESSION[$this->name.'ViewAssetTag']])){
                $line_entry_value = $list_line_entry[$_SESSION[$this->name.'ViewAssetTag']];
                $line_entry_id = $_SESSION[$this->name.'ViewAssetTag'];
            }
        }
		//pr($list_line_entry);die;
		$this->set('list_line_entry', json_encode($list_line_entry));
		$this->set('line_entry_value', $line_entry_value);


		$option_select_custom['oum'] = array_merge(
		     $this->Setting->select_option_vl(array('setting_value'=>'product_oum_area'))
		     ,$this->Setting->select_option_vl(array('setting_value'=>'product_oum_lengths'))
		     ,$this->Setting->select_option_vl(array('setting_value'=>'product_oum_unit'))
		);
		$this->selectModel('Equipment');
		$option_select_custom['tag'] = $this->Equipment->select_combobox_asset_old();
		$this->set('option_select_custom',$option_select_custom);
	}


	public function asset_tags_data($ids='',$key='',$report = false){
		if($key=='all')
			$key='';
        $group = array();
        if($ids!=''){
        	$save = false;
        	$this->selectModel('Task');
            $salesorder = $this->opm->select_one(array('_id'=>new MongoId($ids)),array('asset_tags','options','products'));
            $original_products = $salesorder['products'];
            $tmp = $this->line_entry_data('products',0,'options_list');
            $salesorder['products'] = $tmp['products'];
            if(!empty($salesorder['products'])){
                //$cal_price = new cal_price;
                $this->selectModel('Product');$total_time = $i = 0;
                $num_field = array('quantity','sizew','sizeh');
                $asset_tags = array();
                $custom_assets_key = array();
                if(!empty($salesorder['asset_tags'])){
                    $asset_tags = $salesorder['asset_tags'];
                }
             	if(!empty($asset_tags)){
             		$custom_assets_key = array_keys($asset_tags);
             	}
                //loop note products in tb_salesorder
                foreach($salesorder['products'] as $product){
                	$product_key = $product['_id']; //Vì product của line entry đã đc sort, thứ tự phân biệt bằng _id (để hiển thị đúng [stt ẩn]])
                    if( isset($product['deleted']) && $product['deleted'] ) continue;
                    //Dùng cho check print docket _ uncheck có nghĩa là in
                    if(!isset($product['completed_docket']) || !$product['completed_docket'])
                    	$print = true;
                    else if(isset($product['completed_docket'])&&$product['completed_docket'])
                   		$print = false;
                   	//end
                    $cond = '';
                    if(is_object($product['products_id']))
                        $cond = $product['products_id'];
                    else if(isset($salesorder['options']) && !empty($salesorder['options']))
                        $cond = array($salesorder['options'],$product_key);
                    $extra_info = array('line_no'=>$product_key);
                    if(isset($product['option_for']))
                    	$extra_info['for_line'] = $product['option_for'];
                    $production = $this->Product->get_product_asset($cond,$extra_info);
                    //Tạo li đỏ nếu chọn all và ko có  option for, hoặc chọn $key
                    if( empty($key) &&(!isset($product['option_for']) || !is_numeric($product['option_for']))
                       || $key == $product_key){
	                    $custom_production = array_filter($asset_tags, function($arr) use($product_key){
	                    	return isset($arr['is_custom']) && isset($arr['for_line_no']) && $arr['for_line_no'] == $product_key;
	                    });
	                    if(!empty($custom_production)){
	                    	$production = array_merge($production, $custom_production);
	                    }
                       	$tmp_key = (isset($product['products_id']) ? $product['products_id'] : '').'_@_'.$product_key;
                       	$product['products_name'] .= '<span class="icon_emp7" onclick="open_custom_asset(\''.$tmp_key.'\')"></span>';
                        $group[$i] = array(
                                            '_id'           => '-1',
                                            'asset_key'     =>  '',
                                            'product_key'	=>	$product_key,
                                            'products_name' =>  $product['products_name'],
                                            'product_id'    =>  (isset($product['products_id']) ? $product['products_id'] : ''),
                                            'key'           =>  '',
                                            'product_type'  =>  '',
                                            'code'          =>  (isset($product['code']) ? $product['code'] : ''),
											'sku'           =>  (isset($product['sku']) ? $product['sku'] : ''),
                                            'oum'           =>  $product['oum'],
                                            'tag_key'       =>  '',
                                            'tag'           =>  '',
                                            'min_of_uom'    =>  '',
                                            'print'			=>	$print
                                           );
                        $group[$i]['sizew'] = (isset($product['sizew']) ? $product['sizew'] : '').' '.(isset($product['sizew_unit'])&&$product['sizew_unit']!=''? $product['sizew_unit'] : 'in');
                        $group[$i]['sizeh'] = (isset($product['sizeh']) ? $product['sizeh'] : '').' '.(isset($product['sizeh_unit'])&&$product['sizeh_unit']!=''? $product['sizeh_unit'] : 'in');
                        $group[$i]['quantity'] = $product['quantity'];
                        $group[$i]['xempty']['factor'] = '1';
                        $group[$i]['xempty']['min_of_uom'] = '1';
                        $group[$i]['xempty']['production_time'] = '1';
                        $group[$i]['xempty']['completed'] = '1';
                        $group[$i]['xempty']['delete'] = '1';
                        $group[$i]['xlock']['products_name'] = '1';
                        $group[$i]['xcss'] = 'background-color:#816060;color:white;font-weight:bold';
                        $group[$i]['is_line'] = 1;
                        $i++;
                    }
                    //loop list asset tag of a product
                    foreach($production as $production_key=>$value){
                        if(!isset($value['tag']) || isset($value['deleted'])&&$value['deleted']) continue;
                        if( ($key!='' && ((int)$product_key==(int)$key)) || (isset($product['option_for'])&&(int)$product['option_for']==(int)$key)  || $key=='' ){
                            //Tim code
                            preg_match("/<a ?.*>(.*)<\/a>/", $value['from'], $matches);
                            if(isset($matches[1]))
                                $code = $matches[1];
                            else
                                $code = $value['from'];
                            if(!empty($custom_assets_key)){
                            	while(in_array($i, $custom_assets_key)){
                            		$i++;
                            	}
                            }
                            $group[$i] = array(
                                            '_id'           =>  $i,
                                            'asset_key'     =>  $i,
                                            'products_name' =>  $value['product_name'],
                                            'product_id'    =>  $product['products_id'],
                                            'key'           =>  (isset($product['products_id']) ? $product['products_id'] : '').'_@_'.$product_key.'_@_'.$code.'_@_'.(string)$value['tag_key'],
                                            'product_type'  =>  $value['product_type'],
                                            'code'          =>  $value['from'],
											'sku'           =>  (isset($product['sku']) ? $product['sku'] : ''),
                                            'sell_by'       =>  $value['sell_by'],
                                            'oum'           =>  $value['oum'],
                                            'tag_key'       =>  $value['tag_key'],
                                            'tag'           =>  $value['tag'],
                                            'min_of_uom'    =>  $value['min_of_uom'],
                                            );
							//Xoa button delete
                        	// $group[$i]['xempty']['delete'] = '1';
                        	$group[$i]['xlock']['products_name'] = '1';
							//Lấy asset tag có thay đổi gán vào product hiện tại
							$new_tag = $this->Task->select_one(array(
							                        'salesorder_id'	=> new MongoId($ids),
							                        'our_rep_id_old'=>new MongoId($group[$i]['tag_key']),
							                        'our_rep_type'	=> 'assets',
							                        ),array('our_rep','our_rep_id'));
							if(isset($new_tag['our_rep']) && $new_tag['our_rep'] != '' && $new_tag['our_rep_id']!=$group[$i]['tag_key']){
								 $group[$i]['tag'] = $new_tag['our_rep'];
								 $group[$i]['tag_key'] = $new_tag['our_rep_id'];
							}
							if(isset($value['for_line_no']))
								$group[$i]['for_line_no'] = $value['for_line_no'];
							if(isset($value['line_no'])){
								$group[$i]['line_no'] = $value['line_no'];
								if(isset($original_products[$value['line_no']]['products_name'])
									&&$original_products[$value['line_no']]!=$group[$i]['products_name'])
									$group[$i]['products_name'] = $original_products[$value['line_no']]['products_name'];
							}
							if(isset($value['for_line'])){
								$group[$i]['for_line'] = $value['for_line'];
							}
                            if(!empty($asset_tags)){
                                foreach($asset_tags as $asset_key=>$assettag){
                                    if(!isset($assettag['key'])) continue;
                                    if($assettag['key']==$group[$i]['key']){
                                    	if(isset($assettag['deleted']) && $assettag['deleted']){
                                    		$group[$i] = array('deleted' => true, 'key' => $group[$i]['key']);
                                    		continue 2;
                                    	}
                                        if(isset($assettag['factor']))
                                            $group[$i]['factor'] = (float)$assettag['factor'];
                                        if(isset($assettag['min_of_uom']))
                                            $group[$i]['min_of_uom'] = (float)$assettag['min_of_uom'];
                                        if(isset($assettag['production_time']))
                                            $group[$i]['production_time'] = (float)$assettag['production_time'];
                                       	if(isset($assettag['completed']))
                                            $group[$i]['completed'] = (float)$assettag['completed'];
                                        if(isset($assettag['last_change_field']))
                                            $group[$i]['last_change_field'] = $assettag['last_change_field'];
                                        if(isset($assettag['is_custom'])) {
                                            $group[$i]['is_custom'] = $assettag['is_custom'];
                                        	$group[$i]['code']  = '';
                                        	$group[$i]['product_id'] = '';
                                        	unset($group[$i]['xempty']['delete'], $group[$i]['xlock']['products_name'] );
                                        }
                                    	if(isset($assettag['products_name']))
                                        	$group[$i]['products_name'] = $assettag['products_name'];
                                        $group[$i]['asset_key'] = $asset_key;
                                        break;
                                    }
                                }
                            }
                            //custom factor
                            if(!isset($group[$i]['factor'])&&isset($value['factor']))
                                $group[$i]['factor'] = (float)$value['factor'];
                            else if(!isset($group[$i]['factor']))
                                $group[$i]['factor'] = 0;

                            //custom min_of_uom
                            if(!isset($group[$i]['min_of_uom'])&&isset($value['min_of_uom']))
                                $group[$i]['min_of_uom'] = (float)$value['min_of_uom'];
                            else if(!isset($group[$i]['factor']))
                                $group[$i]['min_of_uom'] = 0;

                            foreach($num_field as $keys){
                                if(isset($product[$keys]))
                                    $group[$i][$keys] = (float)$product[$keys];
                                else
                                    $group[$i][$keys] = 0;
                            }
                            //sizew
                            $group[$i]['sizew'] = (isset($value['sizew']) ? (float)$value['sizew'] : '0' );
                            $group[$i]['sizeh'] = (isset($value['sizeh']) ? (float)$value['sizeh'] : '0' );
                            $group[$i]['sizew_unit'] = (isset($value['sizew_unit']) ? $value['sizew_unit'] : 'in' );
                            $group[$i]['sizeh_unit'] = (isset($value['sizeh_unit']) ? $value['sizeh_unit'] : 'in' );
                            if(isset($product['same_parent'])&&$product['same_parent']==1
                                        &&isset($product['option_for'])&&$product['option_for']!=''){
                                if( $value['oum']=='area'|| $value['sell_by']=='lengths' || (string)$value['product_id']==(string)$product['products_id']){
                                	$parent_product = array();
                                    $option_for = $product['option_for'];
                            		foreach($salesorder['products'] as $pro){
                            			if($pro['_id']!=$option_for) continue;
                            			$parent_product = $pro;
                            		}
                                    $group[$i]['sizew'] = (isset($parent_product['sizew']) ? $parent_product['sizew'] : '0' );
                                    $group[$i]['sizew_unit'] = (isset($parent_product['sizew_unit']) ? $parent_product['sizew_unit'] : 'in' );
                                    $group[$i]['sizeh'] = (isset($parent_product['sizeh']) ? $parent_product['sizeh'] : '0' );
                                    $group[$i]['sizeh_unit'] = (isset($parent_product['sizeh_unit']) ? $parent_product['sizeh_unit'] : 'in' );
                                    $group[$i]['quantity'] = ($report&&isset($parent_product['repair_quantity']) ? $parent_product['repair_quantity'] : $parent_product['quantity']);
                                }
                            }else if( $value['sell_by']=='area' || $value['sell_by']=='lengths' || (string)$value['product_id']==(string)$product['products_id'] ){
                                $group[$i]['sizew'] = (isset($product['sizew']) ? $product['sizew'] : '0' );
                                $group[$i]['sizew_unit'] = (isset($product['sizew_unit']) ? $product['sizew_unit'] : 'in' );
                                $group[$i]['sizeh'] = (isset($product['sizeh']) ? $product['sizeh'] : '0' );
                                $group[$i]['sizeh_unit'] = (isset($product['sizeh_unit']) ? $product['sizeh_unit'] : 'in' );
                                $group[$i]['quantity'] = ($report&&isset($product['repair_quantity']) ? $product['repair_quantity'] : $product['quantity']);
                            }
                            $arr_data = $group[$i];
                            if(!isset($group[$i]['last_change_field']) || $group[$i]['last_change_field'] != 'production_time') {
                            	$group[$i]['production_time'] = $this->cal_production_time($arr_data);
                            } else {
                            	$group[$i]['xclass'] = 'production_time_custom';
                            }
                            if(isset($group[$i]['is_custom'])){
                            	$group[$i]['xcss'] = 'background-color: rgb(247, 215, 134);';
                            }
	                        if(!isset($asset_tags[$group[$i]['asset_key']]) || ksort($asset_tags[$group[$i]['asset_key']]) != ksort($group[$i])) {
	                        	$save = true;
	                        	$asset_tags[$group[$i]['asset_key']] = $group[$i];
	                        }
                            $group[$i]['sizew'] .= ' '.$group[$i]['sizew_unit'];
                            $group[$i]['sizeh'] .= ' '.$group[$i]['sizeh_unit'];
                            if(strtolower($value['sell_by'])=='unit'){
                            	$group[$i]['xempty']['sizew'] = '1';
                        		$group[$i]['xempty']['sizeh'] = '1';
                            }
                            $total_time += (float)$group[$i]['production_time'];

                        }
                        $i++;
                    }//end for
                }//end for
                $this->set('total_time',$total_time);

            }
            if($save){
            	$salesorder['asset_tags'] = $asset_tags;
            	if(isset($salesorder['products']))
            		unset($salesorder['products']);
            	if(isset($salesorder['options']))
            		unset($salesorder['options']);
            	$this->opm->save($salesorder);
            }

        }
        // pr($group);die;
        return $group;
    }

    public function costings(){
        $query = $this->line_entry_data('products',0,'options_list');
        $total_amount = 0;
        $option = $this->opm->select_one(array('_id' => new MongoId($this->get_id())),array('options'));
        $query['options'] = array();
        if(isset($option['options']))
        	$query['options'] = $option['options'];
        if(isset($query['products'])&&!empty($query['products'])){
            foreach($query['products'] as $key=>$value){
                if(!isset($value['products_id']) || !is_object($value['products_id'])){
                    unset($query['products'][$key]);
                    continue;
                }
                $this_product = $query['products'][$key];
                if(isset($this_product['xempty']))
                    unset($this_product['xempty']);
                if(isset($this_product['xlock']))
                    unset($this_product['xlock']);
                $this_product['oum'] = ucfirst($value['oum']);
                $this_product['sell_by'] = ucfirst($value['sell_by']);
                if(isset($value['same_parent']) && $value['same_parent']) {
                    foreach($query['products'] as $k => $v) {
                        if($v['_id'] != $value['option_for']) continue;
                        $this_product['sizew'] = $v['sizew'];
                        $this_product['sizeh'] = $v['sizeh'];
                        $this_product['sizew_unit'] = $v['sizew_unit'];
                        $this_product['sizeh_unit'] = $v['sizeh_unit'];
                        $this_product['quantity'] *= $v['quantity'];
                        break;
                    }
                }
               	/*if(strtolower($value['sell_by'])=='unit')
                    $this_product['sizew'] = $this_product['sizew_unit'] = $this_product['sizeh'] = $this_product['sizeh_unit'] = '';*/
                if(isset($value['icon']['products_name']))
                    $this_product['icon']['products_name'] = URL.'/products/entry/'.$value['products_id'];
                $product = $this->requestAction('/products/costings_data/'.$value['products_id']);
                $this_product['cost_price'] =  (float)str_replace(',', '', $product['pricingsummary']['cost_price']);
                if($value['sell_by']=='unit') {
                    $this_product['cost_amount'] = $this_product['cost_price'] * $this_product['quantity'];
                    $this_product['adj_qty'] = $this_product['quantity'];
                }
                else if($value['sell_by']=='area') {
                    // if(!isset($this_product['area'])) {
                        $cal_price = new cal_price;
                        $cal_price->arr_product_items = $this_product;
                        $cal_price->cal_area();
                        $cal_price->cal_adj_qty();
                        $this_product['area'] = $cal_price->arr_product_items['area'];
                		$this_product['adj_qty'] = $cal_price->arr_product_items['adj_qty'];
                    // }
                    $this_product['cost_amount'] = $this_product['cost_price'] * $this_product['quantity'] * $this_product['area'];
                }
                else if($value['sell_by']=='lengths') {
                    // if(!isset($this_product['perimeter'])) {
                        $cal_price = new cal_price;
                        $cal_price->arr_product_items = $this_product;
                        $cal_price->cal_perimeter();
                		$cal_price->cal_adj_qty();
                        $this_product['perimeter'] = $cal_price->arr_product_items['perimeter'];
                        $this_product['adj_qty'] = $cal_price->arr_product_items['adj_qty'];
                    // }
                    $this_product['cost_amount'] = $this_product['cost_price'] * $this_product['quantity'] * $this_product['perimeter'];
                }
                $total_amount += $this_product['cost_amount'];
                $this_product['sales_amount'] = $value['sub_total'];
                if(isset($value['same_parent']) && $value['same_parent']){
                	foreach($query['options'] as $k => $option){
                		if(isset($option['deleted']) && $option['deleted']){
                			unset($query['options'][$k]);
                			continue;
                		}
                		if( !isset($option['line_no']) ){
                			unset($query['options'][$k]);
                			continue;
                		}
                		if( $option['line_no'] == $value['_id'] ){
                			$this_product['sales_amount'] = $option['sub_total'];
                			unset($query['options'][$k]);
                			break;
                		}
                	}
                }
                $query['products'][$key] = $this_product;
            }
        }
    	$this->selectModel('Shipping');

    	$shipping = $this->Shipping->select_one(array(
        										'salesorder_id' => new MongoId($this->get_id()),
        										'shipping_status' => array('$ne' => 'Cancelled')
        									),array('shipping_cost','shipper_id'));
    	if( !empty($shipping) ){
    		$product = array();
    		if(isset($shipping['shipper_id']) && is_object($shipping['shipper_id'])){
				$this->selectModel('Product');
				$product = $this->Product->select_one(array('company_id' => $shipping['shipper_id'], 'sku' => new MongoRegex('/^SHP/')), array('code','sku','name'));
			}
			if( isset($product['_id']) ){
				$found = false;
				foreach($query['products'] as $key => $p){
					if(isset($p['deleted']) && $p['deleted']) continue;
					if($product['_id'] != $p['products_id']) continue;
					$found = true;
					break;
				}
				$name = $product['name'];
				$name = '<a href="'.URL.'/shippings/entry/'.$shipping['_id'].'" target="_blank">'.$name.'</a>';
				if($found){
					$total_amount -= $query['products'][$key]['cost_amount'];
					$query['products'][$key]['cost_amount'] = $shipping['shipping_cost'];
					$query['products'][$key]['products_name'] = $name;
				} else {
		        	$query['products'][] = array(
		        				'code' 			=> $product['code'],
		        				'sku'			=> $product['sku'],
		        				'products_name' => $name,
		        				'products_id'	=> $product['_id'],
		        				'sizew'			=> 0,
		        				'sizew_unit'	=> 'in',
		        				'sizeh'			=> 0,
		        				'sizeh_unit'	=> 'in',
		        				'area'			=> 0,
		        				'sell_by'		=> 'unit',
		        				'oum'			=> 'unit',
		        				'cost_price'	=> $shipping['shipping_cost'],
		        				'quantity'		=> 1,
		        				'adj_qty'		=> 1,
		        				'cost_amount'	=> $shipping['shipping_cost'],
		        		);
				}
				$total_amount += $shipping['shipping_cost'];
			} else {
				$name = 'Shipping and Handling';
				$name = '<a href="'.URL.'/shippings/entry/'.$shipping['_id'].'" target="_blank">'.$name.'</a>';
	        	$query['products'][] = array(
	        				'code' 			=> '',
	        				'sku'			=> '',
	        				'products_name' => $name,
	        				'products_id'	=> '',
	        				'sizew'			=> 0,
	        				'sizew_unit'	=> 'in',
	        				'sizeh'			=> 0,
	        				'sizeh_unit'	=> 'in',
	        				'area'			=> 0,
	        				'sell_by'		=> 'unit',
	        				'oum'			=> 'unit',
	        				'cost_price'	=> $shipping['shipping_cost'],
	        				'quantity'		=> 1,
	        				'adj_qty'		=> 1,
	        				'cost_amount'		=> $shipping['shipping_cost'],
	        		);
	        	$total_amount += $shipping['shipping_cost'];
			}
    	}
        $subdatas['costings']= $query['products'];
        $this->set('subdatas', $subdatas);
        $total_profit = $query['sum_sub_total'] - $total_amount;
        $this->set('total_profit', $this->opm->format_currency($total_profit));
        $this->set('total_costs', $this->opm->format_currency($total_amount));
        $this->set('total_sales', $this->opm->format_currency($query['sum_sub_total']));
        $this->set('margin_total',$this->opm->format_currency(100*($total_profit/($total_amount==0?1:$total_amount)),2).'%');
    }
    function view_minilist(){
    	if(!isset($_GET['print_pdf'])){
	    	$arr_where = $this->arr_search_where();
	    	$salesorders = $this->opm->select_all(array(
	    											'arr_where' => $arr_where,
	    											'arr_field' => array('code','customer_po_no','company_name','date_modified','our_rep','job_number','status','sum_amount','sum_sub_total','sum_tax'),
	    											'arr_order' => array('_id'=>1),
	    											));
	    	if($salesorders->count()>0){
	    		$group = array();
	    		$html = '';
	    		$i = 0;
	    		$arr_data = array();
	    		$total_amount = 0;
		    	$total_tax = 0;
		    	$total_sub = 0;
	    		foreach($salesorders as $key => $salesorder){
	    			if( $salesorder['status'] == 'Cancelled' )
	    				$salesorder['sum_amount'] = 0;
		    		$total_amount += $sum_amount = (isset($salesorder['sum_amount']) ? (float)$salesorder['sum_amount'] : 0);
		    		$total_tax += $sum_tax = (isset($salesorder['sum_tax']) ? (float)$salesorder['sum_tax'] : 0);
		    		$total_sub += $sum_sub_total = (isset($salesorder['sum_sub_total']) ? (float)$salesorder['sum_sub_total'] : 0);
	    			$html .= '<tr class="'.($i%2==0 ? 'bg_2' : 'bg_1').'">';
	    			$html .= '<td>'.(isset($salesorder['code']) ? $salesorder['code'] : '') .'</td>';
	    			$html .= '<td>'.(isset($salesorder['customer_po_no']) ? $salesorder['customer_po_no'] : '') .'</td>';
	    			$html .= '<td>'.(isset($salesorder['company_name']) ? $salesorder['company_name'] : '') .'</td>';
	    			$html .= '<td>'.(isset($salesorder['date_modified']) ? date('m/d/Y',$salesorder['date_modified']->sec) : '') .'</td>';
	    			$html .= '<td>'.(isset($salesorder['our_rep']) ? $salesorder['our_rep'] : '') .'</td>';
	    			$html .= '<td>'.(isset($salesorder['job_number']) ? $salesorder['job_number'] : '') .'</td>';
	    			$html .= '<td>'.(isset($salesorder['status']) ? $salesorder['status'] : '') .'</td>';
	    			$html .= '<td class="right_text">'.$this->opm->format_currency($sum_sub_total) .'</td>';
	    			$html .= '<td class="right_text">'.$this->opm->format_currency($sum_tax) .'</td>';
	    			$html .= '<td class="right_text">'.$this->opm->format_currency($sum_amount) .'</td>';
	                $html .= '</tr>';
	                $i++;
	    		}
		    	$html .='<tr class="last">
				            <td colspan="6" class="bold_text right_none">'.$i.' record(s) listed.</td>
				            <td class="bold_text right_none">Totals:</td>
				            <td class="bold_text right_none right_text">'.$this->opm->format_currency($total_sub).'</td>
				            <td class="bold_text right_none right_text">'.$this->opm->format_currency($total_tax).'</td>
				            <td class="bold_text right_none right_text">'.$this->opm->format_currency($total_amount).'</td>
				        </tr>';
		        $arr_data['title'] = array('Ref No'=>'text-align: left','Po No'=>'text-align: left','Customer'=>'text-align: left','Date'=>'text-align: left','Our Rep'=>'text-align: left','Job No'=>'text-align: left','Status'=>'text-align: left','Total bf. Tax'=>'text-align: right','Tax'=>'text-align: right','Total Amount'=>'text-align: right');
		    	$arr_data['content'] = $html;
		    	$arr_data['report_name'] = 'Sales order Mini Listing';
		    	$arr_data['report_file_name'] = 'SO_'.md5(time());
		    	$arr_data['report_orientation'] = 'landscape';
		    	Cache::write('salesorders_minilist', $arr_data);
	    	}
    	} else {
    		$arr_data = Cache::read('salesorders_minilist');
    		Cache::delete('salesorders_minilist');
    	}
    	$this->render_pdf($arr_data);
    }
    function create_new_custom_product(){
        if(isset($_POST['product_line'])){
            $product_line = $_POST['product_line'];
            $query = $this->opm->select_one(array('_id'=>new MongoId($this->get_id())));
            if(!isset($query['products']) || empty($query['products'])
                || !isset($query['products'][$product_line]) //Neu khong ton tai product nay
                || (isset($query['products'][$product_line]['deleted'])&&$query['products'][$product_line]['deleted']) /*product nay da bi xoa*/ ){
                echo 'This record is deleted or does not exist!';
                die;
            }
            $this_product = $query['products'][$product_line];
            $product_id = '';
            $product = array();
            $this->selectModel('Product');
            //Neu product co ID
            if( isset($query['products'][$product_line]['products_id'])&&is_object($query['products'][$product_line]['products_id']) ){
                $product_id = $query['products'][$product_line]['products_id'];
                $product = $this->Product->select_one(array('_id'=>new MongoId($product_id)));
            } else { //Custom product
                //Lay default
                $this->Product->arrfield();
                $default_field = $this->Product->arr_temp;
                $product = $default_field;
                $product['sell_price'] = $product['unit_price'] = $product['cost_price'] =  0;
                $product['product_type'] = 'Custom Product';
            }
			//$option = $this->option_list_data($product_id,$product_line);
			$costing_for_line = $this->costing_for_line($product_line);
            foreach($costing_for_line['costing_list'] as $key=>$value){
                if(isset($value['xlock']))
                    unset($costing_for_line['costing_list'][$key]['xlock']);
                $costing_for_line['costing_list'][$key]['require'] = 1;
                $costing_for_line['costing_list'][$key]['same_parent'] = 1;
            }
            $product['code'] = $this->Product->get_auto_code('code');
            $product['name'] = $this_product['products_name'];
            $product['sizeh'] = $this_product['sizeh'];
            $product['sizeh_unit'] = $this_product['sizeh_unit'];
            $product['sizew'] = $this_product['sizew'];
            $product['sizew_unit'] = $this_product['sizew_unit'];
            $product['sku'] = $this_product['sku'];
            $product['sell_by'] = $this_product['sell_by'];
            $product['oum'] = $this_product['oum'];
            $product['options'] = $costing_for_line['costing_list'];
            $product['created_by'] = new MongoId($this->opm->user_id());
            unset($product['_id']);
            unset($product['modified_by']);
            $this->Product->save($product);
            $new_product_id = $this->Product->mongo_id_after_save;
            $query['products'][$product_line]['products_id'] = $new_product_id;
            $query['products'][$product_line]['code'] = $product['code'];
            $query['products'][$product_line]['is_saved'] = true;
            if($this->opm->save($query)){
                echo 'ok';
                die;
            } else {
                echo $this->opm->arr_errors_save[1];
                die;
            }
        }
        die;
    }


	function save_over_older_custom_product(){
        if(isset($_POST['product_line'])){
            if(!isset($_POST['replace_id']) || strlen($_POST['replace_id'])!=24){
                echo 'There is something wrong. Please refresh and try again!';
                die;
            }
            $product_line = $_POST['product_line'];
            $query = $this->opm->select_one(array('_id'=>new MongoId($this->get_id())));
            if(!isset($query['products']) || empty($query['products'])
                || !isset($query['products'][$product_line]) //Neu khong ton tai product nay
                || (isset($query['products'][$product_line]['deleted'])&&$query['products'][$product_line]['deleted']) /*product nay da bi xoa*/ ){
                echo 'This record is deleted or does not exist!';
                die;
            }
            $this_product = $query['products'][$product_line];
            $product_id = '';
            $product = array();
            $this->selectModel('Product');
            //Lay default
            $this->Product->arrfield();
            $default_fields = $this->Product->arr_temp;
            //Neu product co ID
            if( isset($query['products'][$product_line]['products_id'])&&is_object($query['products'][$product_line]['products_id']) ){
                $product_id = $query['products'][$product_line]['products_id'];
                $product = $this->Product->select_one(array('_id'=>new MongoId($product_id)));
                $arr_tmp['sellprices'] = (isset($product['sellprices']) ? $product['sellprices'] : array());
                $arr_tmp['pricebreaks'] = (isset($product['pricebreaks']) ? $product['pricebreaks'] : array());
                $arr_tmp['price_note'] = (isset($product['price_note']) ? $product['price_note'] : '');
                if(isset($product['pricing_method'])&&$product['pricing_method']!='')
                    $arr_tmp['pricing_method'] = $product['pricing_method'];
                $arr_tmp['sell_price'] = (isset($product['sell_price']) ? (float)$product['sell_price'] : 0);
                $arr_tmp['code'] = $product['code'];
                $product = $default_fields;
                $product = array_merge($product,$arr_tmp);
            } else { //Custom product
                $product_id = $_POST['replace_id'];
                $product_replace = $this->Product->select_one(array('_id'=>new MongoId($product_id)));
                $product_id = '';
                $product = $default_fields;
                $product['sell_price'] = $product['unit_price'] = $product['cost_price'] =  0;
                $product['product_type'] = 'Custom Product';
                $product['code'] = $product_replace['code'];
            }
            //$option = $this->option_list_data($product_id,$product_line);
			$option = $this->costing_for_line($product_line);
            foreach($option['costing_list'] as $key=>$value){
                if(isset($value['xlock']))
                    unset($costing_for_line['costing_list'][$key]['xlock']);
                $costing_for_line['costing_list'][$key]['require'] = 1;
                $costing_for_line['costing_list'][$key]['same_parent'] = 1;
            }
            $product['name'] = $this_product['products_name'];
            $product['sizeh'] = $this_product['sizeh'];
            $product['sizeh_unit'] = $this_product['sizeh_unit'];
            $product['sizew'] = $this_product['sizew'];
            $product['sizew_unit'] = $this_product['sizew_unit'];
            $product['sku'] = $this_product['sku'];
            $product['sell_by'] = $this_product['sell_by'];
            $product['oum'] = $this_product['oum'];
            $product['options'] = (isset($option['costing_list'])&&!empty($option['costing_list']) ? $option['costing_list'] : array());
            $product['created_by'] = new MongoId($this->opm->user_id());
            $product['_id'] = new MongoId($_POST['replace_id']);
            unset($product['modified_by']);
            $this->Product->save($product);
            $new_product_id = $product['_id'];
            $query['products'][$product_line]['products_id'] = $new_product_id;
            $query['products'][$product_line]['code'] = $product['code'];
            $query['products'][$product_line]['is_saved'] = true;
            if($this->opm->save($query)){
                echo 'ok';
                die;
            } else {
                echo $this->opm->arr_errors_save[1];
                die;
            }
        }
        die;

	}

    public function asset_tag_report($id){
    	if(!isset($_GET['print_pdf'])){
	    	$query = $this->opm->select_one(array('_id'=>new MongoId($id)));
	    	$this->selectModel('Contact');
	    	if(isset($query['our_rep_id']) && is_object($query['our_rep_id'])){
	    		$our_rep = $this->Contact->select_one(array('_id'=> new MongoId($query['our_rep_id'])),array('first_name','last_name'));
	    		$query['our_rep'] = (isset($our_rep['first_name']) ? $our_rep['first_name'] : '').' '.(isset($our_rep['last_name']) ? $our_rep['last_name'] : '');

	    	}
	    	if(isset($query['our_csr_id']) && is_object($query['our_csr_id'])){
	    		$our_csr = $this->Contact->select_one(array('_id'=> new MongoId($query['our_csr_id'])),array('first_name','last_name'));
	    		$query['our_csr'] = (isset($our_csr['first_name']) ? $our_csr['first_name'] : '').' '.(isset($our_csr['last_name']) ? $our_csr['last_name'] : '');

	    	}
	    	$arr_data = array();
	    	$arr_tmp = array();
	    	$arr_html = array();
	    	//Lấy address
	    	//Các thông tin cơ bản hiện thị ở header report
	    	$arr_address = array('invoice', 'shipping');
			foreach ($arr_address as $value) {
				$address = '';
				if (isset($query[$value . '_address']) && isset($query[$value . '_address'][0]) && count($query[$value . '_address']) > 0) {
					$temp = $query[$value . '_address'][0];
					if (isset($temp[$value . '_address_1']) && $temp[$value . '_address_1'] != '')
						$address .= $temp[$value . '_address_1'] . ' ';
					if (isset($temp[$value . '_address_2']) && $temp[$value . '_address_2'] != '')
						$address .= $temp[$value . '_address_2'] . ' ';
					if (isset($temp[$value . '_address_3']) && $temp[$value . '_address_3'] != '')
						$address .= $temp[$value . '_address_3'] . '<br />';
					else
						$address .= '<br />';
					if (isset($temp[$value . '_town_city']) && $temp[$value . '_town_city'] != '')
						$address .= $temp[$value . '_town_city'];

					if (isset($temp[$value . '_province_state']))
						$address .= ' ' . $temp[$value . '_province_state'] . ' ';
					else if (isset($temp[$value . '_province_state_id']) && isset($temp[$value . '_country_id'])) {
						$keytemp = $temp[$value . '_province_state_id'];
						$provkey = $this->province($temp[$value . '_country_id']);
						if (isset($provkey[$temp]))
							$address .= ' ' . $provkey[$temp] . ' ';
					}


					if (isset($temp[$value . '_zip_postcode']) && $temp[$value . '_zip_postcode'] != '')
						$address .= $temp[$value . '_zip_postcode'];

					if (isset($temp[$value . '_country']) && isset($temp[$value . '_country_id']) && (int) $temp[$value . '_country_id'] != "CA")
						$address .= ' ' . $temp[$value . '_country'] . '<br />';
					else
						$address .= '<br />';
					$arr_address[$value] = $address;
				}
			}
			$shipping_contact_name = isset($query['shipping_address'][0]['shipping_contact_name']) ? $query['shipping_address'][0]['shipping_contact_name'] : '';
	    	$arr_pdf['left_info'] = array(
	    	                               'label'=> 'Shipping address: ',
	    	                               'name'=>$shipping_contact_name ? $shipping_contact_name : (isset($query['company_name']) ? $query['company_name'] : (isset($query['contact_name']) ? $query['contact_name'] : '')),
	    	                               'address' => $arr_address['invoice'],
	    	                               );

			if(isset($arr_address['shipping'])&&strlen($arr_address['shipping'])>20)
				$arr_pdf['left_info']['address'] = $arr_address['shipping'];

	    	$arr_pdf['right_info'] = array(
	    	                               'name'=>(isset($query['our_rep']) ? '<span class="bold_text">Our Rep: </span>'.$query['our_rep'] : ''),
	    	                               'address' => (isset($query['our_csr']) ? '<span class="bold_text">Our CSR: </span>'.$query['our_csr'] : '')
	    	                               );
	    	$arr_pdf['main_info'] = array(
	    	                              'Sales Order: '=>$query['code'],
	    	                              'Date: '		=>date('M d, Y',$query['salesorder_date']->sec),
	    	                              'Term: '		=>$query['payment_terms'],
	    	                              'Required date: '=>date('M d, Y',$query['payment_due_date']->sec),
	    	                              );
	    	if($query['job_number']!='')
	    		$arr_pdf['main_info']['Job no: '] = $query['job_number'];
	    	//End basic info
	    	//Lấy Asset Tag từ hàm asset_tags_data(), hàm này đã lọc và tính production time
	    	$asset_tag = $this->asset_tags_data($id,'all',true); //true de chi in repair quantity
	    	if(!empty($asset_tag)){
	    		//Những record có xcss là những record cha, xem ở tab Asset Tag SO
	    		//Lọc ra đưa riêng vào 1 mảng
	    		//$arr_parent là mảng chứa các product cha
	    		//Giờ cấu trúc mảng $arr_tmp sẽ là array('asset_tag1'=>array(),'asset_tag2'=>array())
	    		$arr_parent = array();
	    		foreach($asset_tag as $key=>$value){
	    			//Tách riêng $arr_tmp vào $arr_parent, với khoá là số thứ tự trong line entry
	    			if(isset($value['xcss']) ){
	    				$key = $value['product_key'];
	    				$arr_parent[$key] = $value;
	    			}
	    			else
	    				$arr_tmp[$value['tag']][] = $value;
	    		}
	    		if(!empty($arr_parent)){
    				//================================Lấy line entry trước================================================================
    				if(!empty($arr_parent)){
    					$options = array();
						if(isset($query['options']) && !empty($query['options']) )
							$options = $query['options'];
    					$arr_product_option = array();
    					$i = 0;
    					$count = count($arr_parent);
    					$html = '
				    			<table class="table_content">
				    				<tr class="tr_right_none" style="background: #911b12;color: white;height: 29px;line-height: 29px;font-weight: bold;">
			    						<td>SKU</td>
				    					<td>Name</td>
				    					<td class="right_text">Width</td>
				    					<td class="right_text">Height</td>
				    					<td class="right_text">Quantity</td>
				    				</tr>';
    					foreach($arr_parent as $parent_key=>$value){
    						$product_key = $value['product_key'];
    						//Xem có details không để gắn thêm vào name
    						if(isset($query['products'])){
    							$value['products_name'] .= (isset($query['products'][$product_key]['details']) ? '<br /><span style="font-style:italic;font-size: 12px;">'.nl2br($query['products'][$product_key]['details']).'</span>' : '');
    							$arr_parent[$parent_key] = $value;
    						}
    						$html .= '
				    				<tr class="'.($i%2==0 ? 'bg_2' : 'bg_1').' content_asset">
				    					<td>'.$value['sku'].'</td>
				    					<td>'.$value['products_name'].'</td>
				    					<td class="right_text">'.$value['sizew'].'</td>
				    					<td class="right_text">'.$value['sizeh'].'</td>
				    					<td class="right_text">'.$value['quantity'].'</td>
				    				</tr>';
				    		$i++;
				    		//Tìm special_order từ PRODUCTS
			    			if(isset($value['product_id'])&&is_object($value['product_id'])){
			    				$product = $this->Product->select_one(array('_id'=>new MongoId($value['product_id'])),array('special_order'));
			    				if(isset($product['special_order'])&&$product['special_order']==1)
			    					$arr_product_option[$product_key][] = $value;
			    			}
			    			//End special order
				    		//Từ $arr_parent tìm các product option để hiển thị kèm theo
				    		foreach($query['products'] as $key=>$opt_value){
				    			if(isset($opt_value['deleted'])&&$opt_value['deleted']){
				    				continue;
				    			}
				    			if(!isset($opt_value['option_for'])||!is_numeric($opt_value['option_for'])){
				    				continue;
				    			}
				    			if($opt_value['option_for']!=$product_key) continue;
				    			$extra_name = '';
				    			//Xem có details không để gắn thêm vào name
			    				if(isset($opt_value['details']))
                            		$extra_name = '<br /><span style="margin-left:15px;font-style:italic;font-size: 12px;">'.nl2br($opt_value['details']).'</span>';
                            	//Nếu là option same_parent thì số lượng custom sẽ gắn thêm vào name
				    			if(isset($opt_value['same_parent']) && $opt_value['same_parent']==1){
				    				$opt_value['sizew'] = $value['sizew'];
				    				$opt_value['sizeh'] = $value['sizeh'];
                               		if(!empty($options)){
	                                	foreach($options as $k=>$val){
	                                		if(isset($val['deleted']) && $val['deleted']) continue;
	                                		if(!isset($val['line_no']) || $val['line_no']!=$key) continue;
	                                		if(!isset($val['quantity'])||$val['quantity']==1) continue;
	                                		$query['products'][$key]['products_name'] = $opt_value['products_name'] .= ' ('.$val['quantity'].')';
	                                		$opt_value['quantity'] *= $val['quantity'];
	                                		unset($options[$k]);
	                                		$options['filter_name'][$key] = ' ('.$val['quantity'].')';
	                                		break;
	                                	}
	                                }
                                }
                                //Tìm special_order từ PRODUCTS - option
				    			if(isset($opt_value['products_id'])&&is_object($opt_value['products_id'])){
				    				$product = $this->Product->select_one(array('_id'=>new MongoId($opt_value['products_id'])));
				    				if(isset($product['special_order'])&&$product['special_order']==1)
				    					$arr_product_option[$product_key][] = $opt_value;
				    			}
				    			//End special order
                                $opt_value['products_name'] .= $extra_name;
                                //================================================================================================
				    			$html .= '
				    				<tr class="'.($i%2==0 ? 'bg_2' : 'bg_1').' content_asset">
				    					<td></td>
				    					<td>&nbsp;&nbsp;&nbsp;•'.$opt_value['products_name'];
				    			$html .= '</td>';
				    			if(!isset($opt_value['same_parent']) || $opt_value['same_parent']==0){
				    				$html .= '
				    					<td class="right_text">'.($opt_value['oum']!='unit' ? $opt_value['sizew'].' '.$opt_value['sizew_unit'] : '').'</td>
				    					<td class="right_text">'.($opt_value['oum']!='unit' ? $opt_value['sizeh'].' '.$opt_value['sizeh_unit'] : '').'</td>
				    					<td class="right_text">'.$opt_value['quantity'].'</td>
				    				</tr>';
				    			} else {
				    				$html .= '
				    					<td class="right_text"></td>
				    					<td class="right_text"></td>
				    					<td class="right_text"></td>
				    				</tr>';
				    			}
				    			$i++;
				    		}
			    		}
			    		$html .= '</table>';
			    		$html .= '
						    		<div style="clear:right;padding-bottom:25px"></div>
						    		<div style="page-break-after:always;"></div>';
			    		$arr_html[] = array(
			    		                    'report_name'=>'
                                				Docket #: <span class="color_red">'.$query['code'].'</span>
                                				<div class="bold_txt" style="margin-top:5px;">(Line entry)</div>',
                                			'html'=>$html,
                                			'qr_url'=>'https://chart.googleapis.com/chart?chs=150x150&cht=qr&chl='.URL.'/salesorders/entry/'.$id.'&choe=UTF-8',
                                			);
			    		if(!empty($arr_product_option)){

			    			$html = '
				    			<table class="table_content asset_table">
				    				<tr>
				    					<td class="center_text">SPECIAL ORDER</td>
				    				</tr>
				    			</table><br />
				    			<table class="table_content">
				    				<tr class="tr_right_none" style="background: #911b12;color: white;height: 29px;line-height: 29px;font-weight: bold;">
			    						<td>SKU</td>
				    					<td style="width:50%">Name</td>
				    					<td class="right_text">Width</td>
				    					<td class="right_text">Height</td>
				    					<td class="right_text">Quantity</td>
				    				</tr>';
		    				$i = 0;
			    			foreach($arr_product_option as $value){
			    				foreach($value as $val){
			    					$val['oum'] = strtolower($val['oum']);
			    					$html .= '
				    				<tr class="'.($i%2==0 ? 'bg_2' : 'bg_1').' content_asset">
				    					<td>'.$val['sku'].'</td>
				    					<td>'.$val['products_name'].'</td>
				    					<td class="right_text">'.(strpos($val['sizew'], @$val['sizew_unit'])===false ? $val['sizew'].' '.@$val['sizew_unit'] : $val['sizew']).'</td>
				    					<td class="right_text">'.(strpos($val['sizeh'], @$val['sizeh_unit'])===false ? $val['sizeh'].' '.@$val['sizeh_unit'] : $val['sizeh']).'</td>
				    					<td class="right_text">'.$val['quantity'].'</td>
				    				</tr>';
				    				$i++;
			    				}
			    			}
			    			$html .= '
		    					</table>
				    			<div style="clear:right;padding-bottom:25px"></div>
							    <div style="page-break-after:always;"></div>';
	    					$arr_html[] = array(
	    					                    'report_name'=>'
                                    				Docket #: <span class="color_red">'.$query['code'].'</span>
                                    				<div class="bold_txt" style="margin-top:5px;">(Special Order)</div>',
                                    			'html'=>$html,
                                    			'qr_url'=>'https://chart.googleapis.com/chart?chs=150x150&cht=qr&chl='.URL.'/salesorders/entry/'.$id.'&choe=UTF-8',
	    					                    );
			    		}
    				}
	    			//$arr_tmp là mảng của các Asset Tag
	    			if(!empty($arr_tmp)){
	    				ksort($arr_tmp);
						foreach($arr_tmp as $asset_key=>$value){
							foreach($value as $val){
								//Nếu có for_line tức là product option, có mặt ở line entry
								//Nếu có for_line_no tức là asset tag lấy từ đệ quy, với product không nằm trong line entry
								//Nếu là line_no tức là asset của chính nó
								$key = (isset($val['for_line']) ? $val['for_line'] : (isset($val['for_line_no']) ? $val['for_line_no'] : (isset($val['line_no']) ? $val['line_no'] : -1)));
								//Dựa vào key của arr_parent để tách ra
								//Trùng key thì có liên quan
								//$arr_parent[$key]['print'] chỉ in những uncheck line entry
								if(isset($arr_parent[$key])&&$arr_parent[$key]['print']){
									$arr_data[$asset_key][$key]['child'][] = $val;
									$arr_data[$asset_key][$key]['parent'] = $arr_parent[$key];
								}
							}
						}
	    			}
	    		}
	    	}
	    	$count = count($arr_data);
			$num = 0;
			$no = 0;
	    	//==============================Lọc theo ASSET TAG================================================================
			//Lấy Task để đưa vào QRCODE
	    	$this->selectModel('Task');
	    	foreach($arr_data as $key=>$value){
	    		$html = '
		    			<table class="table_content asset_table">
		    				<tr>
		    					<td style="border-right:none !important;">'.$key.'</td>
		    					<td class="right_text">Ref no: '.$query['code'].' - '.++$num.'</td>
		    				</tr>
		    			</table><br />
		    			<table class="table_content asset_product">
		    				<tr style="background: #979797;color: white;height: 29px;line-height: 29px;font-weight: bold;">
		    						<td>SKU</td>
			    					<td style="width:50%">Name</td>
			    					<td class="right_text">Width</td>
			    					<td class="right_text">Height</td>
			    					<td class="right_text">Quantity</td>
			    					<td class="right_text">Production time</td>
		    					</tr>';
		    	$total = 0;
	    		foreach($value as $val){
		    		$i = 0;
		    			$html .=
		    				'<tr class="'.($i%2==0 ? 'bg_2' : 'bg_1').' content_asset parent_product" style="height: 35px;line-height: 35px;">
		    					<td>'.$val['parent']['sku'].'</td>
		    					<td>'.$val['parent']['products_name'].'</td>
		    					<td class="right_text">'.$val['parent']['sizew'].'</td>
		    					<td class="right_text">'.$val['parent']['sizeh'].'</td>
		    					<td></td>
		    					<td></td>
		    				</tr>';
		    		$i++;
		    		if(isset($val['child'])&&!empty($val['child'])){
			    		foreach($val['child'] as $child){
			    			if(isset($child['line_no'])){
			    				if(isset($options['filter_name'][$child['line_no']]))
			    					$child['products_name'] .= $options['filter_name'][$child['line_no']];
				    			if(isset($query['products'][$child['line_no']])){
				    				$this_line = $query['products'][$child['line_no']];
				    				if(isset($this_line['details']))
				    					$child['products_name'] .='<br /><span style="font-style:italic;font-size: 12px;">'.nl2br($this_line['details']).'</span>';
				    			}
				    		}
			    			$html .= '
			    					<tr class="'.($i%2==0 ? 'bg_2' : 'bg_1').' content_asset">
			    						<td>'.$child['sku'].'</td>
			    						<td>'.$child['products_name'].'</td>
			    						<td class="right_text">'.$child['sizew'].'</td>
			    						<td class="right_text">'.$child['sizeh'].'</td>
			    						<td class="right_text">'.$child['quantity'].'</td>
			    						<td class="right_text">'.$child['production_time'].'</td>
			    					</tr>';
			    			$total += $child['production_time'];
			    			$i++;
			    		}
		    		}

	    		}
	    		$html .= '
	    				<tr class="'.($i%2==0 ? 'bg_2' : 'bg_1').'">
	    					<td class="bold_text" colspan="4"></td>
	    					<td class="bold_text right_text">Total:</td>
	    					<td class="bold_text right_text">'.$total.'</td>
	    				</tr>
	    			</table>';
	    		//Nếu là table cuối cùng thì ko cần page-break
	    		$html .= '<div '.($num<$count ? 'style="page-break-after:always;"' : '').'></div>';
	    		$arr_tmp = array('html'=>$html);
	    		$task = $this->Task->select_one(array('salesorder_id'=>new MongoId($id),'our_rep'=>$key));
	    		if(!empty($task)&&is_object($task['_id'])){
	    			$url = 'https://chart.googleapis.com/chart?chs=150x150&cht=qr&chl='.URL.'/mobile/tasks/entry/'.$task['_id'].'&choe=UTF-8';
				    $arr_tmp['qr_url'] = $url;
				    $arr_tmp['custom_main_info'] = '<table >
	    										<tr>
	    											<td class="bold_text">Job no:</td><td>'.$query['job_number'].'</td>
	    										</tr>
	    										<tr>
	    											<td class="bold_text">Date in:</td><td>'.date('D. M d, Y',$query['salesorder_date']->sec).'</td>
	    										</tr>
	    										<tr>
	    											<td class="bold_text">Date due:</td><td>'.date('H:i, D. M d, Y',$task['work_end']->sec).'</td>
	    										</tr>
	    										<tr>
	    											<td class="bold_text">Term:</td><td>'.$query['payment_terms'].' day'.($query['payment_terms'] > 1 ? 's' : '').'</td>
	    										</tr>
	    										<tr>
	    											<td class="bold_text">Delivery:</td><td>'.(isset($query['delivery_method']) ? $query['delivery_method'] : '').'</td>
	    										</tr>
	    									</table>';
				}
				$arr_tmp['report_name'] = '
								Dkt. <span class="color_red">'.$query['code'].' - '.++$no.' of '.$count.'</span>
                                <div class="bold_txt" style="margin-top:5px;">(Asset tags)</div>';
	    		$arr_html[] = $arr_tmp;
	    	}
	    	$client_name = (isset($query['company_name']) ? $query['company_name'] : (isset($query['contact_name']) ? $query['contact_name'] : ''));
	    	$arr_pdf['custom_top_left_info'] = '<table>
	    											<tr>
	    												<td class="bold_text">Client:</td><td class="bold_text">'.strtoupper($client_name).'</td>
	    											</tr>
	    											<tr>
	    												<td class="bold_text">Contact:</td><td>'.(isset($query['contact_name']) ? $query['contact_name'] : '').'</td>
	    											</tr>
	    											<tr>
	    												<td class="bold_text">Phone:</td><td>'.(isset($query['phone']) ? $query['phone'] : '').'</td>
	    											</tr>
	    											<tr>
	    												<td class="bold_text">PO No:</td><td>'.(isset($query['customer_po_no']) ? $query['customer_po_no'] : '').'</td>
	    											</tr>
	    										</table>';
	    	$arr_pdf['custom_main_info'] = '<table >
	    										<tr>
	    											<td class="bold_text">Job no:</td><td>'.$query['job_number'].'</td>
	    										</tr>
	    										<tr>
	    											<td class="bold_text">Date in:</td><td>'.date('D. M d, Y',$query['salesorder_date']->sec).'</td>
	    										</tr>
	    										<tr>
	    											<td class="bold_text">Date due:</td><td>'.date('D. M d, Y',$query['payment_due_date']->sec).'</td>
	    										</tr>
	    										<tr>
	    											<td class="bold_text">Term:</td><td>'.$query['payment_terms'].' day'.($query['payment_terms'] > 1 ? 's' : '').'</td>
	    										</tr>
	    										<tr>
	    											<td class="bold_text">Delivery:</td><td>'.(isset($query['delivery_method']) ? $query['delivery_method'] : '').'</td>
	    										</tr>
	    									</table>';
	    	$arr_pdf['is_custom'] = true;
	    	$arr_pdf['content'] = $arr_html;
	        $arr_pdf['report_name'] = 'SO Dockets';
	        $arr_pdf['report_size'] = '8.5in*11in';
	        if(isset($query['heading']) && !empty($query['heading']))
	        	$arr_pdf['report_heading'] = $query['heading'];
			$numkey = explode("-",$query['code']);
	        $arr_pdf['report_file_name']='DKT-'.$numkey[count($numkey)-1];
	        //$arr_pdf['report_orientation'] = 'landscape';
	        Cache::write('asset_tag_report', $arr_pdf);
	    } else {
	    	//Vì quá trình xử lý khá lâu, nhiều khâu nên phải đưa vào cached, tăng tốc độ render cho PhantomJS generate PDF đầy đủ ko thiêu thông tin
	    	$arr_pdf = Cache::read('asset_tag_report');
	    	Cache::delete('asset_tag_report');
	    }
        $this->render_pdf($arr_pdf);
    }
    function save_docket_check(){
    	$id = $this->get_id();
    	if(isset($_POST)){
    		$origin_query = $query = $this->opm->select_one(array('_id'=> new MongoId($id)),array('products'));
    		if(isset($query['products'])&&!empty($query['products'])){
    			if(isset($_POST['all'])){
	    			if($_POST['all']=='true')
	    				foreach($query['products'] as $key=>$value){
			    			if($value['deleted']) continue;
			    			if(isset($value['option_for'])) continue;
			    			$query['products'][$key]['completed_docket'] = true;
			    		}
	    			else
	    				foreach($query['products'] as $key=>$value){
			    			if($value['deleted']) continue;
			    			if(isset($value['option_for'])) continue;
			    			$query['products'][$key]['completed_docket'] = false;
			    		}
	    		} else {
	    			if(isset($query['products'][$_POST['key']])){
	    				if($_POST['value']==1)
	    					$query['products'][$_POST['key']]['completed_docket'] = true;
	    				else
	    					$query['products'][$_POST['key']]['completed_docket'] = false;
	    			}
	    		}
	    		if($origin_query != $query){
	    			if($this->opm->save($query)){
		    			echo 'ok';
		    		} else {
		    			echo $this->opm->arr_errors_save[1];
		    		}
		    		die;
	    		}
	    		echo 'ok';
		    	die;
	    	}
    	}echo 'ok';die;
    }
    function get_uncompleted_docket(){
    	$id = $this->get_id();
    	$query = $this->opm->select_one(array('_id'=> new MongoId($id)),array('products'));
    	$arr_data = array();
    	if(isset($query['products'])&&!empty($query['products'])){
    		foreach($query['products'] as $key=>$value){
    			if($value['deleted']) continue;
    			if(isset($value['option_for'])) continue;
    			if(isset($value['completed_docket'])&&$value['completed_docket']) continue;
    			$arr_data[] = array(
    			                                'key'=>$key,
    			                                'product_name'=>$value['products_name'],
    			                                'quantity'=>$value['quantity'],
    			                                'sizew'=>$value['sizew'].' '.(isset($value['sizew_unit']) ? $value['sizew_unit'] : ''),
    			                                'sizeh'=>$value['sizeh'].' '.(isset($value['sizeh_unit']) ? $value['sizeh_unit'] : ''),
    			                                );
    		}
    		if(!empty($arr_data))
    			$this->opm->aasort($arr_data,'product_name',1,true);
    	}
    	echo json_encode($arr_data);
    	die;
    }
    function docket_repair_save(){
    	if(isset($_POST)&&!empty($_POST)){
	    	$id = $this->get_id();
	    	$query = $this->opm->select_one(array('_id'=> new MongoId($id)),array('products'));
	    	$arr_post = $_POST;
	    	$user_id = $this->opm->user_id();
	    	$current_date = new MongoDate();
	    	foreach($arr_post as $key=>$value){
	    		$key = str_replace('repair_qty_', '', $key);
	    		if(isset($query['products'][$key])){
	    			$query['products'][$key]['docket_repair'][] = array(
	    			                                                    'quantity'=>(int)$value,
	    			                                                    'created_by'=>$user_id,
	    			                                                    'created_date'=>$current_date
	    			                                                    );
	    		}
	    	}
	    	if($this->opm->save($query))
	    		echo 'ok';
	    	else
	    		echo $this->opm->arr_errors_save[1];
    	}
    	die;
    }
    function rebuild_job_link(){
    	$query = $this->opm->select_all(array(
    	                       	'arr_where' => array('job_id'=>array('$ne'=>'')),
    							'arr_field'	=> array('job_id'),
    							'limit'=>99999
    	                       ));
		$this->selectModel('Job');
		$i = 0;
		foreach($query as $salesorder){
			if(!is_object($salesorder['job_id'])) continue;
			$arr_data = array('_id'=> new MongoId($salesorder['_id']));
			$job = $this->Job->select_one(array('_id'=> new MongoId($salesorder['job_id'])),array('name','custom_po_no','no'));
			$arr_data['customer_po_no'] = (isset($job['custom_po_no']) ? $job['custom_po_no'] : '');
			$arr_data['name'] = (isset($job['name']) ? $job['name'] : '');
			$arr_data['job_number'] = (isset($job['no']) ? $job['no'] : '');
    		$this->opm->rebuild_collection($arr_data);
    		$i++;
		}
		echo $query->count().'/'.$i;
		echo '<br/>Xong.';
		die;
    }
    function create_salesorder_from_worktraq(){

    	if(isset($_POST['company_id'])){
    		$arr_save = $_POST;
    		$arr_save['company_id'] = new MongoId($arr_save['company_id']);
	    	$arr_contact['contact_id'] = new MongoId('100000000000000000000000');
			$arr_contact['contact_name'] = $arr_contact['full_name'] = 'System Admin';
			$arr_contact['first_name'] = 'System';
			$arr_contact['last_name'] = 'Admin';
			$_SESSION['arr_user'] = $arr_contact;
			//===================================
    		$arr_save['code'] = $this->opm->get_auto_code('code');
    		$arr_save['job_id'] = $arr_save['job_name'] = $arr_save['job_number'] = $arr_save['quotation_id'] = $arr_save['quotation_id'] = $arr_save['quotation_name'] = $arr_save['quotation_number'] = '';
			$arr_save['customer_po_no'] = $arr_save['delivery_method']  = $arr_save['shipper'] = $arr_save['shipper_account'] = $arr_save['shipper_id'] = '';
			//Address=================================================
			$arr_save['invoice_address'][0] = $arr_save['shipping_address'][0] = array(
			                                      'deleted'=>false,
			                                      'shipping_country'=>'Canada',
			                                      'shipping_country_id'=>'CA',
			                                      );
			//========================================================
    		if(is_object($arr_save['company_id'])){
    			$this->selectModel('Company');
    			$company = $this->Company->select_one(array('_id'=> $arr_save['company_id']),array('contact_default_id','contact_id','our_rep','our_rep_id','our_csr','our_csr_id','name','email','phone','addresses','addresses_default_key'));
    		}
    		//Company info===========================================================================
    		$arr_save['contact_id'] = $arr_save['contact_name'] = '';
    		if(isset($arr_save['contact_default_id']) && is_object($arr_save['contact_default_id'])){
    			$arr_save['contact_id'] = $arr_save['contact_default_id'];
    			$this->selectModel('Contact');
    			$contact = $this->Contact->select_one(array('_id'=>$arr_save['contact_default_id']),array('first_name','last_name'));
    			$arr_save['contact_name'] = (isset($contact['first_name']) ? $contact['first_name'].' ' : '');
    			$arr_save['contact_name'] .= (isset($contact['last_name']) ? $contact['last_name'] : '');
    		}
    		$arr_save['contact_name'] = isset($company['contact_name']) ? $company['contact_name'] : '';
			$arr_save['our_rep'] = isset($company['our_rep']) ? $company['our_rep'] : '';
			$arr_save['our_rep_id'] = isset($company['our_rep_id']) ? $company['our_rep_id'] : '';
			$arr_save['our_rep_id'] = isset($company['our_rep_id']) ? $company['our_rep_id'] : '';
			$arr_save['our_csr'] = isset($company['our_csr']) ? $company['our_csr'] : '';
			$arr_save['our_csr_id'] = isset($company['our_csr_id']) ? $company['our_csr_id'] : '';
			$arr_save['company_name'] = isset($company['name']) ? $company['name'] : '';
			$arr_save['email'] = isset($company['email']) ? $company['email'] : '';
			$arr_save['phone'] = isset($company['phone']) ? $company['phone'] : '';
			$addresses_default_key = 0;
			if(isset($company['addresses_default_key']))
				$addresses_default_key = $company['addresses_default_key'];
			if(isset($company['addresses'][$addresses_default_key])){
				foreach($company['addresses'][$addresses_default_key] as $field => $value){
					if($field == 'name' || $field == 'deleted') continue;
					$arr_save['invoice_address'][0]['invoice_'.$field] = $value;
				}
			}
			//========================================================================================
			$arr_save['sales_order_type'] = 'Sales Order';
    		$arr_save['status_id'] = $arr_save['status'] = 'New';
			$arr_save['salesorder_date'] =  $arr_save['payment_due_date'] = new MongoDate(strtotime(date('Y-m-d')));
			$arr_save['payment_terms'] = 0;
			$arr_save['heading'] = $arr_save['description'] = 'Created from WorkTraq at '.date('Y-m-d H:i:s');
			//Tax===============================================================
			$taxper = 5;
			$this->selectModel('Tax');
			$arr_tax = $this->Tax->tax_select_list();
			$key_tax = 'AB';
			if(isset($arr_save['invoice_address'][0]['invoice_province_state_id']) && $arr_save['invoice_address'][0]['invoice_province_state_id']!='')
				$key_tax = $arr_save['invoice_address'][0]['invoice_province_state_id'];
			if(isset($arr_tax[$key_tax])){
				$prov_key = explode("%",$arr_tax[$key_tax]);
				$taxper = $prov_key[0];
			}
			$arr_save['taxval'] = $taxper;
			$arr_save['tax'] = $key_tax;
			//==================================================================
			//Price=============================================================
			$arr_save['sum_sub_total'] = $arr_save['sum_amount'] = (float)0;
			if(!isset($arr_save['products']))
				$arr_save['products'] = array();
			foreach($arr_save['products'] as $key=>$product){
				$arr_save['products'][$key]['deleted'] = false;
				$arr_save['products'][$key]['sell_price'] = 0;
				$arr_save['products'][$key]['code'] = $arr_save['products'][$key]['products_id'] = $arr_save['products'][$key]['option_group'] = '';
				$arr_save['products'][$key]['unit_price'] = $arr_save['products'][$key]['custom_unit_price'] = (isset($product['custom_unit_price']) ? (float)$product['custom_unit_price'] : 0);
				$arr_save['products'][$key]['quantity'] = isset($product['quantity']) ? $product['quantity'] : 1;
				$arr_save['products'][$key]['sub_total'] = round( ($arr_save['products'][$key]['quantity'] * $arr_save['products'][$key]['unit_price']),3);
				$arr_save['products'][$key]['taxper'] = $taxper;
				$arr_save['products'][$key]['tax'] = round( ($arr_save['products'][$key]['sub_total'] * $taxper / 100), 3 );
				$arr_save['products'][$key]['amount'] = round( ($arr_save['products'][$key]['tax'] + $arr_save['products'][$key]['sub_total']), 3 );
				$arr_save['sum_sub_total']+= $arr_save['products'][$key]['sub_total'];
				$arr_save['sum_amount']+= $arr_save['products'][$key]['amount'];
			}
			$arr_save['sum_tax'] = (float)($arr_save['sum_amount'] - $arr_save['sum_sub_total']);
			//==================================================================
			$arr_save['name'] = $arr_save['code'].' - '.(isset($arr_save['company_name']) ? $arr_save['company_name'] : '');
			$this->opm->save($arr_save);
			//=============================================================================================================
			$this->selectModel('Stuffs');
			$accountant = $this->Stuffs->select_one(array('value'=>'Accountant'));
			if(isset($accountant['accountant_id'])){
				$current_date = strtotime(date('Y-m-d H:00:00'));
				$arr_save = array();
				$arr_save['our_rep_type'] = 'contacts';
				$arr_save['salesorder_id'] = new MongoId($this->opm->mongo_id_after_save);
				$arr_save['type_id'] = '';
				$arr_save['type'] = 'Accountant';
				$arr_save['name'] = (isset($so['name']) ? $so['name'] : '');
				$arr_save['our_rep'] = $accountant['accountant'];
				$arr_save['our_rep_id'] = $accountant['accountant_id'];
				$arr_save['work_start'] = new MongoDate($current_date);
				$arr_save['work_end'] = new MongoDate($current_date + HOUR);
				$this->selectModel('Task');
				$this->Task->arr_default_before_save = $arr_save;
				$this->Task->add();
				echo 'ok';
			}
    	} else
    		 echo 'wrong company_id';
    	die;
    }


    /*
	  Commission
	*/
	public function commission(){
		$qry = $this->opm->select_one(
				array(
					'_id'=>new MongoId($this->get_id())
				)
			);
		$subdatas = array();
		$subdatas['commission'] = $commission = array();

		if(isset($qry['our_rep_id']) && strlen((string)$qry['our_rep_id']) == 24 ){
			$commission[0]['employee_id'] = $qry['our_rep_id'];
			$commission[0]['no'] = 1;
			$commission[0]['group'] = 'Our rep';
		}
		if(isset($qry['our_csr_id']) && strlen((string)$qry['our_csr_id']) == 24  && $qry['our_csr_id']!=$qry['our_rep_id']){
			$commission[1]['employee_id'] = $qry['our_csr_id'];
			$commission[1]['no'] = 2;
			$commission[1]['group'] = 'Our CSR';
		}


		if(isset($qry['commission']) && is_array($qry['commission']) && count($qry['commission'])>0){
			foreach ($qry['commission'] as $key => $value) {
				if($value['employee_id'] != $qry['our_csr_id'] && $value['employee_id'] != $qry['our_rep_id'] && strlen((string)$qry['employee_id']) == 24){
					$commission[] = $value;
				}
			}
		}

		$this->selectModel('Contact');

		if(!isset($qry['sum_amount']))
			$qry['sum_amount'] = 0;

		foreach ($commission as $key => $value) {
			$contact = $this->Contact->select_one(
				array(
					'_id'=>$value['employee_id']
				)
			);
			if(isset( $contact['first_name']))
				$commission[$key]['fullname'] = $contact['first_name'];
			if(isset( $contact['last_name']))
				$commission[$key]['fullname'] .= ' '.$contact['last_name'];
			if(isset($contact['commission']))
				$commission[$key]['per_com'] = (float)$contact['commission'];
			else
				$commission[$key]['per_com'] = 1;

			$commission[$key]['price_com'] = (float)$qry['sum_amount']*($commission[$key]['per_com']/100);
		}
		$subdatas['commission'] = $commission;
		//pr($commission);

		$this->set('subdatas', $subdatas);
		$arr_options_custom = $this->set_select_data_list('relationship','commission');
		$this->set('arr_options_custom', $arr_options_custom);
	}


  	 function print_late_salesorders(){
    	if(!isset($_GET['print_pdf'])){
    		$current_date = strtotime(date('Y-m-d'));
			$arr_where['status'] = array('$nin' => array('Completed','Cancelled'));

			$arr_where['payment_due_date'] = array('$lte' => new MongoDate($current_date - DAY));
	    	$salesorders = $this->opm->select_all(array(
	    											'arr_where' => $arr_where,
	    											'arr_field' => array('code','customer_po_no','company_name','phone','salesorder_date','payment_due_date','our_rep','job_number','status'),
	    											'arr_order' => array('code'=>1),
	    											'limit' => 2000
	    											));
	    	if($salesorders->count()>0){
	    		$group = array();
	    		$html = '';
	    		$i = 0;
	    		$arr_data = array();
		    	$arr_orders = array();
	    		foreach($salesorders as $key => $salesorder){
	    			$arr_orders[] = array(
	    				'code' => (isset($salesorder['code']) ? $salesorder['code'] : ''),
	    				'customer_po_no' => (isset($salesorder['customer_po_no']) ? $salesorder['customer_po_no'] : ''),
	    				'company_name' => (isset($salesorder['company_name']) ? $salesorder['company_name'] : ''),
	    				'phone' => (isset($salesorder['phone']) ? $salesorder['phone'] : ''),
	    				'salesorder_date' => (isset($salesorder['salesorder_date']) ? $this->opm->format_date($salesorder['salesorder_date']->sec) : '') ,
	    				'payment_due_date' => (isset($salesorder['payment_due_date']) ? $this->opm->format_date($salesorder['payment_due_date']->sec) : ''),
	    				'status' => (isset($salesorder['status']) ? $salesorder['status'] : ''),
	    				);
	    		}
	    		foreach($arr_orders as $salesorder){
	    			$html .= '<tr class="'.($i%2==0 ? 'bg_2' : 'bg_1').'">';
	    			$html .= '<td>'.$salesorder['code'].'</td>';
	    			$html .= '<td>'.$salesorder['customer_po_no'] .'</td>';
	    			$html .= '<td>'.$salesorder['company_name'].'</td>';
	    			$html .= '<td>'.$salesorder['phone'] .'</td>';
	    			$html .= '<td class="center_text">'.$salesorder['salesorder_date'] .'</td>';
	    			$html .= '<td class="center_text">'.$salesorder['payment_due_date'].'</td>';
	    			$html .= '<td>'.$salesorder['status'] .'</td>';
	                $html .= '</tr>';
	                $i++;
	    		}
		    	$html .='<tr class="last">
				            <td colspan="8" class="bold_text right_none">'.$i.' record(s) listed.</td>
				        </tr>';
		        $arr_data['title'] = array('Docket No'=>'text-align: left; width: 8%','Po No'=>'text-align: left','Customer'=>'text-align: left','Phone'=>'text-align: left','Date in'=>'text-align: center; width: 10%','Due Date'=>'text-align: center; width: 10%','Status'=>'text-align: left; width: 10%;');
		    	$arr_data['content'] = $html;
		    	$arr_data['report_name'] = 'Late Dockets';
		    	$arr_data['report_file_name'] = 'SO_'.md5(time());
		    	$arr_data['report_orientation'] = 'landscape';
		    	$arr_data['excel_url'] = URL .'/salesorders/view_late_excel';
		    	Cache::write('print_late_salesorders', $arr_data);
		    	Cache::write('print_late_salesorders_excel', $arr_orders);
	    	}
    	} else {
    		$arr_data = Cache::read('print_late_salesorders');
    		Cache::delete('print_late_salesorders');
    	}
    	$this->render_pdf($arr_data);
    }

    function view_late_excel(){
        $arr_orders = Cache::read('print_late_salesorders_excel');
        if(!$arr_orders){
            echo 'No data';die;
        }
        App::import('Vendor', 'phpexcel/PHPExcel');
        $objPHPExcel = new PHPExcel();
        // Set document properties
        $objPHPExcel->getProperties()->setCreator("")
                                     ->setLastModifiedBy("")
                                     ->setTitle("Late Orders")
                                     ->setSubject("Late Orders")
                                     ->setDescription("Late Orders")
                                     ->setKeywords("Late Orders")
                                     ->setCategory("Late Orders");
        $worksheet = $objPHPExcel->getActiveSheet();
        $worksheet->setCellValue('A1',"Docket No")
                                        ->setCellValue('B1',"Po No")
                                        ->setCellValue('C1',"Customer")
                                        ->setCellValue('D1',"Phone")
                                        ->setCellValue('E1',"Date in")
                                        ->setCellValue('F1',"Due Date")
                                        ->setCellValue('G1',"Status");
        $worksheet->freezePane('H2');
        $i = 2;
        foreach($arr_orders as $order){
            $worksheet->setCellValue('A'.$i,$order['code'])
                        ->setCellValue('B'.$i,$order['customer_po_no'])
                        ->setCellValue('C'.$i,$order['company_name'])
                        ->setCellValue('D'.$i,$order['phone'])
                        ->setCellValue('E'.$i,$order['salesorder_date'])
                        ->setCellValue('F'.$i,$order['payment_due_date'])
                        ->setCellValue('G'.$i,$order['status']);
            $i ++;
        }
        $worksheet->setCellValue('A'.$i,($i-2).' record(s) listed');
        $worksheet->getRowDimension(8)->setRowHeight(-1);
        $worksheet->mergeCells("A$i:C$i");
        $worksheet->getStyle('A1:G1')->getFont()->setBold(true);
        $worksheet->getStyle('A1:A'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
        $worksheet->getStyle('E1:E'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        $worksheet->getStyle('F1:F'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
         $styleArray = array(
			    'borders' => array(
			       'allborders' => array(
			    	   'style' => PHPExcel_Style_Border::BORDER_THIN
			        )
			    ),
		      	'font'  => array(
			        'size'  => 12,
			        'name'  => 'Century Gothic'
			    )
		);
		$worksheet->getStyle('A1:G'.($i-1))->applyFromArray($styleArray);
        for($i = 'A'; $i !== 'G'; $i++){
        	$worksheet->getColumnDimension($i)
				        	->setAutoSize(true);
        }
        $objPHPExcel->setActiveSheetIndex(0);
        $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
        $objWriter->save(APP.DS.'webroot'.DS.'upload'.DS.'Late_Order.xlsx');
        Cache::delete('print_late_salesorders_excel');
        $this->redirect('/upload/Late_Order.xlsx');
        die;
    }

    function combine_orders_popup($key = ''){
    	$this->set('key',$key);
    	$id = $this->get_id();
    	$order = $this->opm->select_one(array('_id'=> new MongoId($id)),array('job_id'));
    	if(!isset($order['job_id']) || !is_object($order['job_id'])){
    		echo '<span style="padding: 36%; font-weight: bold;font-size: 20px;">This order has no Job</span>';
    		die;
    	}
    	$limit = 100;
        $skip = 0;
        $cond = array();
        // Nếu là search GET
        // Nếu là search theo phân trang
        $page_num = 1;
        if (isset($_POST['pagination']) && $_POST['pagination']['page-num'] > 0) {
            // $limit = $_POST['pagination']['page-list'];
            $page_num = $_POST['pagination']['page-num'];
            $limit = $_POST['pagination']['page-list'];
            $skip = $limit * ($page_num - 1);
        }
        $arr_order = array('_id'=>-1);
        $this->set('page_num', $page_num);
        $this->set('limit', $limit);
        if (isset($_POST['sort']) && strlen($_POST['sort']['field']) > 0) {
            $sort_type = 1;
            if ($_POST['sort']['type'] == 'desc') {
                $sort_type = -1;
            }
            $arr_order = array($_POST['sort']['field'] => $sort_type);

            $this->set('sort_field', $_POST['sort']['field']);
            $this->set('sort_type', ($sort_type === 1) ? 'asc' : 'desc');
            $this->set('sort_type_change', ($sort_type === 1) ? 'desc' : 'asc');
        } else {
        	$arr_order = array('salesorder_date' => -1);
        }
        // search theo submit $_POST kèm điều kiện
        $cond['job_id'] = $order['job_id'];
        $cond['_id'] = array('$ne' => $order['_id']);
        if (!empty($this->data) && !empty($_POST) && isset($this->data['Salesorder'])) {
            $arr_post = $this->Common->strip_search($this->data['Salesorder']);
            if (isset($arr_post['code']) && strlen($arr_post['code']) > 0) {
                $cond['code'] = new MongoRegex('/' . trim($arr_post['code']) . '/i');
            }

            if (isset($arr_post['status']) && strlen($arr_post['status']) > 0) {
             	$this->set('status',$arr_post['status']);
                $cond['status'] = new MongoRegex('/' . trim($arr_post['status']) . '/i');
            }
            if (isset($arr_post['company']) && strlen($arr_post['company']) > 0) {
                $cond['company_name'] = new MongoRegex('/' . trim($arr_post['company']) . '/i');
            }
        }
        $this->selectModel('Setting');
		$this->set('arr_order_status', $this->Setting->select_option(array('setting_value' => 'salesorders_status'), array('option')));
        $this->selectModel('Salesorder');
        $arr_salesorders = $this->Somonth->select_all(array(
            'arr_where' => $cond,
            'arr_order' => $arr_order,
            'arr_field' => array('heading', 'code', 'job_number', 'job_name', 'salesorder_date', 'company_id', 'company_name','status'),
            'limit' => $limit,
            'skip' => $skip,
        ));
        $this->set('arr_salesorders', $arr_salesorders);

        $total_page = $total_record = $total_current = 0;
        if (is_object($arr_salesorders)) {
            $total_current = $arr_salesorders->count(true);
            $total_record = $arr_salesorders->count();
            if ($total_record % $limit != 0) {
                $total_page = floor($total_record / $limit) + 1;
            } else {
                $total_page = $total_record / $limit;
            }
        }
        $this->set('total_current', $total_current);
        $this->set('total_page', $total_page);
        $this->set('total_record', $total_record);

        $this->layout = 'ajax';
    }


    function combine_orders(){
    	$arr_post = $this->data;
    	if(isset($arr_post['combine_salesorders']) && !empty($arr_post['combine_salesorders'])){
			$this->selectModel('Task');
    		$id = $this->get_id();
    		$order = $this->opm->select_one(array('_id'=> new MongoId($id)),array('job_id','products','options'));
	    	if(!isset($order['job_id']) || !is_object($order['job_id'])){
	    		echo 'This Sales order has no Job.';
	    		die;
	    	}
	    	$arr_orders_id = array();
    		foreach($arr_post['combine_salesorders'] as $id=>$value){
    			if(strlen($id)!=24) continue;
    			$arr_orders_id[] = new MongoId($id);
    		}
    		$arr_orders = $this->opm->select_all(array(
    		                       'arr_where' => array(
    		                                            '_id' => array('$in'=>$arr_orders_id)
    		                                            ),
    		                       'arr_order' => array('_id'=>1),
    		                       'arr_field' => array('options','products')
    		                       ));
    		if(!isset($order['products']))
    			$order['products'] = array();
    		if(!isset($order['options']))
    			$order['options'] = array();
			$products_num = count($order['products']);
			$options_num = count($order['options']);
    		foreach($arr_orders as $value){
    			if(!isset($value['products']))
	    			$value['products'] = array();
	    		if(!isset($value['options']))
	    			$value['options'] = array();
    			$arr_data = $this->build_salesorder($value,$products_num,$options_num);
				$order['options'] = array_merge($order['options'],$arr_data['options']);
				$order['products'] = array_merge($order['products'],$arr_data['products']);
				$order['salesorders'][] = $value['_id'];
				$this->Task->update_all( array('salesorder_id' => $value['_id']), array( 'deleted' => true ) );
				$this->opm->save(array('_id'=>$value['_id'],'deleted'=>true));
    		}
    		$arr_sum = $this->new_cal_sum($order['products']);
			$order = array_merge($order,$arr_sum);
			$this->opm->save($order);
			echo 'ok';
    	} else
    		echo 'You must choose at least one Sales order.';
    	die;
    }

    function rebuild_email(){
    	$arr_orders = $this->opm->select_all(array(
    						'arr_where' => array(
    								'email' => array('$in' => array('' ,null)),
    								'company_id' => array('$nin' => array('' ,null)),
    								'contact_id' => array('$nin' => array('' ,null))
    							),
    						'arr_field' => array('email','company_id','contact_id')
    		));
    	echo $arr_orders->count().' records found.<br />';
    	$i = 0;
    	$arr_contact = array();
    	$this->selectModel('Contact');
    	foreach($arr_orders as $order){
    		echo '<a href="'.URL.'/salesorders/entry/'.$order['_id'].'">'.$order['_id'].'</a><br />';
    	}
    	foreach($arr_orders as $order){
    		if(!is_object($order['contact_id'])) continue;
    		if(!isset($arr_contact[(string)$order['contact_id']])){
    			$contact = $this->Contact->select_one(array('_id' => $order['contact_id']),array('email'));
    			$arr_contact[(string)$order['contact_id']] = $contact['email'];
    		} else {
    			$contact['email'] = $arr_contact[(string)$order['contact_id']];
    		}
    		if(!isset($contact['email']) || empty($contact['email'])) continue;
    		$order['email'] = $contact['email'];
    		$this->opm->rebuild_collection($order);
    		$i++;
    	}
    	echo $i.' records are rebuild.';
    	die;
    }

    function first_page_docket($id) {
    	if(!isset($_GET['print_pdf'])){
	    	$query = $this->opm->select_one(array('_id'=>new MongoId($id)));
	    	$this->selectModel('Contact');
	    	if(isset($query['our_rep_id']) && is_object($query['our_rep_id'])){
	    		$our_rep = $this->Contact->select_one(array('_id'=> new MongoId($query['our_rep_id'])),array('first_name','last_name'));
	    		$query['our_rep'] = (isset($our_rep['first_name']) ? $our_rep['first_name'] : '').' '.(isset($our_rep['last_name']) ? $our_rep['last_name'] : '');

	    	}
	    	if(isset($query['our_csr_id']) && is_object($query['our_csr_id'])){
	    		$our_csr = $this->Contact->select_one(array('_id'=> new MongoId($query['our_csr_id'])),array('first_name','last_name'));
	    		$query['our_csr'] = (isset($our_csr['first_name']) ? $our_csr['first_name'] : '').' '.(isset($our_csr['last_name']) ? $our_csr['last_name'] : '');

	    	}
	    	$arr_data = array();
	    	$arr_tmp = array();
	    	$arr_html = array();
	    	//Lấy address
	    	//Các thông tin cơ bản hiện thị ở header report
	    	$arr_address = array('invoice', 'shipping');
			foreach ($arr_address as $value) {
				$address = '';
				if (isset($query[$value . '_address']) && isset($query[$value . '_address'][0]) && count($query[$value . '_address']) > 0) {
					$temp = $query[$value . '_address'][0];
					if (isset($temp[$value . '_address_1']) && $temp[$value . '_address_1'] != '')
						$address .= $temp[$value . '_address_1'] . '<br />';
					if (isset($temp[$value . '_address_2']) && $temp[$value . '_address_2'] != '')
						$address .= $temp[$value . '_address_2'] . '<br />';
					if (isset($temp[$value . '_address_3']) && $temp[$value . '_address_3'] != '')
						$address .= $temp[$value . '_address_3'] . '<br />';
					else
						$address .= '<br />';
					if (isset($temp[$value . '_town_city']) && $temp[$value . '_town_city'] != '')
						$address .= $temp[$value . '_town_city'];

					if (isset($temp[$value . '_province_state']))
						$address .= ' ' . $temp[$value . '_province_state'] . ' ';
					else if (isset($temp[$value . '_province_state_id']) && isset($temp[$value . '_country_id'])) {
						$keytemp = $temp[$value . '_province_state_id'];
						$provkey = $this->province($temp[$value . '_country_id']);
						if (isset($provkey[$temp]))
							$address .= ' ' . $provkey[$temp] . ' ';
					}


					if (isset($temp[$value . '_zip_postcode']) && $temp[$value . '_zip_postcode'] != '')
						$address .= $temp[$value . '_zip_postcode'];

					if (isset($temp[$value . '_country']) && isset($temp[$value . '_country_id']) && (int) $temp[$value . '_country_id'] != "CA")
						$address .= ' ' . $temp[$value . '_country'] . '<br />';
					else
						$address .= '<br />';
					$arr_address[$value] = $address;
				}
			}
			$shipping_contact_name = isset($query['shipping_address'][0]['shipping_contact_name']) ? $query['shipping_address'][0]['shipping_contact_name'] : '';
	    	$arr_pdf['left_info'] = array(
	    	                               'label'=> 'Shipping address: ',
	    	                               'name'=>$shipping_contact_name ? $shipping_contact_name : (isset($query['company_name']) ? $query['company_name'] : (isset($query['contact_name']) ? $query['contact_name'] : '')),
	    	                               'address' => $arr_address['invoice'],
	    	                               );

			if(isset($arr_address['shipping'])&&strlen($arr_address['shipping'])>20)
				$arr_pdf['left_info']['address'] = $arr_address['shipping'];

	    	$arr_pdf['right_info'] = array(
	    	                               'name'=>(isset($query['our_rep']) ? '<span class="bold_text">Our Rep: </span>'.$query['our_rep'] : ''),
	    	                               'address' => (isset($query['our_csr']) ? '<span class="bold_text">Our CSR: </span>'.$query['our_csr'] : '')
	    	                               );
	    	$arr_pdf['main_info'] = array(
	    	                              'Sales Order: '=>$query['code'],
	    	                              'Date: '		=>date('M d, Y',$query['salesorder_date']->sec),
	    	                              'Term: '		=>$query['payment_terms'],
	    	                              'Required date: '=>date('M d, Y',$query['payment_due_date']->sec),
	    	                              );
	    	if($query['job_number']!='')
	    		$arr_pdf['main_info']['Job no: '] = $query['job_number'];
	    	//End basic info
	    	//Lấy Asset Tag từ hàm asset_tags_data(), hàm này đã lọc và tính production time
	    	$asset_tag = $this->asset_tags_data($id,'all',true); //true de chi in repair quantity
	    	if(!empty($asset_tag)){
	    		//Những record có xcss là những record cha, xem ở tab Asset Tag SO
	    		//Lọc ra đưa riêng vào 1 mảng
	    		//$arr_parent là mảng chứa các product cha
	    		//Giờ cấu trúc mảng $arr_tmp sẽ là array('asset_tag1'=>array(),'asset_tag2'=>array())
	    		$arr_parent = array();
	    		foreach($asset_tag as $key=>$value){
	    			//Tách riêng $arr_tmp vào $arr_parent, với khoá là số thứ tự trong line entry
	    			if(isset($value['is_line']) ){
	    				$key = $value['product_key'];
	    				$arr_parent[$key] = $value;
	    			}
	    			else
	    				$arr_tmp[$value['tag']][] = $value;
	    		}
	    		if(!empty($arr_parent)){
    				//================================Lấy line entry trước============================================
					$options = array();
					if(isset($query['options']) && !empty($query['options']) )
						$options = $query['options'];
					$arr_product_option = array();
					$i = 0;
					$count = count($arr_parent);
					$html = '
			    			<table class="table_content">
			    				<tr class="tr_right_none" style="background: #911b12;color: white;height: 29px;line-height: 29px;font-weight: bold;">
		    						<td>SKU</td>
			    					<td>Name</td>
			    					<td class="right_text">Width</td>
			    					<td class="right_text">Height</td>
			    					<td class="right_text">Quantity</td>
			    				</tr>';
					foreach($arr_parent as $parent_key=>$value) {
						$product_key = $value['product_key'];
						//Xem có details không để gắn thêm vào name
						if(isset($query['products'])){
							$value['products_name'] .= (isset($query['products'][$product_key]['details']) ? '<br /><span style="font-style:italic;font-size: 12px;">'.nl2br($query['products'][$product_key]['details']).'</span>' : '');
							$arr_parent[$parent_key] = $value;
						}
						$html .= '
			    				<tr class="'.($i%2==0 ? 'bg_2' : 'bg_1').' content_asset">
			    					<td>'.$value['sku'].'</td>
			    					<td>'.$value['products_name'].'</td>
			    					<td class="right_text">'.$value['sizew'].'</td>
			    					<td class="right_text">'.$value['sizeh'].'</td>
			    					<td class="right_text">'.$value['quantity'].'</td>
			    				</tr>';
			    		$i++;
			    		//Từ $arr_parent tìm các product option để hiển thị kèm theo
			    		foreach($query['products'] as $key=>$opt_value){
			    			if(isset($opt_value['deleted'])&&$opt_value['deleted']){
			    				continue;
			    			}
			    			if(!isset($opt_value['option_for'])||!is_numeric($opt_value['option_for'])){
			    				continue;
			    			}
			    			if($opt_value['option_for']!=$product_key) continue;
			    			$extra_name = '';
			    			//Xem có details không để gắn thêm vào name
		    				if(isset($opt_value['details']))
                        		$extra_name = '<br /><span style="margin-left:15px;font-style:italic;font-size: 12px;">'.nl2br($opt_value['details']).'</span>';
                        	//Nếu là option same_parent thì số lượng custom sẽ gắn thêm vào name
			    			if(isset($opt_value['same_parent']) && $opt_value['same_parent']==1){
			    				$opt_value['sizew'] = $value['sizew'];
			    				$opt_value['sizeh'] = $value['sizeh'];
                           		if(!empty($options)){
                                	foreach($options as $k=>$val){
                                		if(isset($val['deleted']) && $val['deleted']) continue;
                                		if(!isset($val['line_no']) || $val['line_no']!=$key) continue;
                                		if(!isset($val['quantity'])||$val['quantity']==1) continue;
                                		$query['products'][$key]['products_name'] = $opt_value['products_name'] .= ' ('.$val['quantity'].')';
                                		$opt_value['quantity'] *= $val['quantity'];
                                		unset($options[$k]);
                                		$options['filter_name'][$key] = ' ('.$val['quantity'].')';
                                		break;
                                	}
                                }
                            }
                            //Tìm special_order từ PRODUCTS - option
			    			if(isset($opt_value['products_id'])&&is_object($opt_value['products_id'])){
			    				$product = $this->Product->select_one(array('_id'=>new MongoId($opt_value['products_id'])));
			    				if(isset($product['special_order'])&&$product['special_order']==1)
			    					$arr_product_option[$product_key][] = $opt_value;
			    			}
			    			//End special order
                            $opt_value['products_name'] .= $extra_name;
                            //================================================================================================
			    			$html .= '
			    				<tr class="'.($i%2==0 ? 'bg_2' : 'bg_1').' content_asset">
			    					<td></td>
			    					<td>&nbsp;&nbsp;&nbsp;•'.$opt_value['products_name'];
			    			$html .= '</td>';
			    			if(!isset($opt_value['same_parent']) || $opt_value['same_parent']==0){
			    				$html .= '
			    					<td class="right_text">'.($opt_value['oum']!='unit' ? $opt_value['sizew'].' '.$opt_value['sizew_unit'] : '').'</td>
			    					<td class="right_text">'.($opt_value['oum']!='unit' ? $opt_value['sizeh'].' '.$opt_value['sizeh_unit'] : '').'</td>
			    					<td class="right_text">'.$opt_value['quantity'].'</td>
			    				</tr>';
			    			} else {
			    				$html .= '
			    					<td class="right_text"></td>
			    					<td class="right_text"></td>
			    					<td class="right_text"></td>
			    				</tr>';
			    			}
			    			$i++;
			    		}
		    		} ///sfafasd
		    		$html .= '</table><div style="clear:right;padding-bottom:25px"></div>';
		    		$arr_html[] = array(
	                    'report_name'=>'
            				Docket #: <span class="color_red">'.$query['code'].'</span>
            				<div class="bold_txt" style="margin-top:5px;">(Line entry)</div>',
            			'html'=>$html,
            			'qr_url'=>'https://chart.googleapis.com/chart?chs=150x150&cht=qr&chl='.URL.'/salesorders/entry/'.$id.'&choe=UTF-8',
            			);
			    }
			}
			$client_name = (isset($query['company_name']) ? $query['company_name'] : (isset($query['contact_name']) ? $query['contact_name'] : ''));
	    	$arr_pdf['custom_top_left_info'] = '<table>
	    											<tr>
	    												<td class="bold_text">Client:</td><td class="bold_text">'.strtoupper($client_name).'</td>
	    											</tr>
	    											<tr>
	    												<td class="bold_text">Contact:</td><td>'.(isset($query['contact_name']) ? $query['contact_name'] : '').'</td>
	    											</tr>
	    											<tr>
	    												<td class="bold_text">Phone:</td><td>'.(isset($query['phone']) ? $query['phone'] : '').'</td>
	    											</tr>
	    											<tr>
	    												<td class="bold_text">PO No:</td><td>'.(isset($query['customer_po_no']) ? $query['customer_po_no'] : '').'</td>
	    											</tr>
	    										</table>';
	    	$arr_pdf['custom_main_info'] = '<table >
	    										<tr>
	    											<td class="bold_text">Job no:</td><td>'.$query['job_number'].'</td>
	    										</tr>
	    										<tr>
	    											<td class="bold_text">Date in:</td><td>'.date('D. M d, Y',$query['salesorder_date']->sec).'</td>
	    										</tr>
	    										<tr>
	    											<td class="bold_text">Date due:</td><td>'.date('D. M d, Y',$query['payment_due_date']->sec).'</td>
	    										</tr>
	    										<tr>
	    											<td class="bold_text">Term:</td><td>'.$query['payment_terms'].' day'.($query['payment_terms'] > 1 ? 's' : '').'</td>
	    										</tr>
	    										<tr>
	    											<td class="bold_text">Delivery:</td><td>'.(isset($query['delivery_method']) ? $query['delivery_method'] : '').'</td>
	    										</tr>
	    									</table>';
	    	$arr_pdf['is_custom'] = true;
	    	$arr_pdf['content'] = $arr_html;
	        $arr_pdf['report_name'] = 'SO Dockets';
	        $arr_pdf['report_size'] = '8.5in*11in';
	        if(isset($query['heading']) && !empty($query['heading']))
	        	$arr_pdf['report_heading'] = $query['heading'];
			$numkey = explode("-",$query['code']);
	        //$arr_pdf['report_orientation'] = 'landscape';
	        Cache::write('first_page_docket', $arr_pdf);
	    } else {
	    	//Vì quá trình xử lý khá lâu, nhiều khâu nên phải đưa vào cached, tăng tốc độ render cho PhantomJS generate PDF đầy đủ ko thiêu thông tin
	    	$arr_pdf = Cache::read('first_page_docket');
	    	Cache::delete('first_page_docket');
	    }
        $this->render_pdf($arr_pdf);
    }

    function docket_report($id) {
    	if(!isset($_GET['print_pdf'])){
	    	$query = $this->opm->select_one(array('_id'=>new MongoId($id)));
	    	$this->selectModel('Contact');
	    	if(isset($query['our_rep_id']) && is_object($query['our_rep_id'])){
	    		$our_rep = $this->Contact->select_one(array('_id'=> new MongoId($query['our_rep_id'])),array('first_name','last_name'));
	    		$query['our_rep'] = (isset($our_rep['first_name']) ? $our_rep['first_name'] : '').' '.(isset($our_rep['last_name']) ? $our_rep['last_name'] : '');
	    	}
	    	if(isset($query['our_csr_id']) && is_object($query['our_csr_id'])){
	    		$our_csr = $this->Contact->select_one(array('_id'=> new MongoId($query['our_csr_id'])),array('first_name','last_name'));
	    		$query['our_csr'] = (isset($our_csr['first_name']) ? $our_csr['first_name'] : '').' '.(isset($our_csr['last_name']) ? $our_csr['last_name'] : '');
	    	}
	    	$arr_data = $arr_tmp = $arr_html = $arr_pages = array();
	    	//Lấy address
	    	//Các thông tin cơ bản hiện thị ở header report
	    	$arr_address = array('invoice', 'shipping');
			foreach ($arr_address as $value) {
				$address = '';
				if (isset($query[$value . '_address']) && isset($query[$value . '_address'][0]) && count($query[$value . '_address']) > 0) {
					$temp = $query[$value . '_address'][0];
					if (isset($temp[$value . '_address_1']) && !empty($temp[$value . '_address_1']))
						$address .= $temp[$value . '_address_1'] . '<br />';
					if (isset($temp[$value . '_address_2']) && !empty($temp[$value . '_address_2']))
						$address .= $temp[$value . '_address_2'] . '<br />';
					if (isset($temp[$value . '_address_3']) && $temp[$value . '_address_3'] != '')
						$address .= $temp[$value . '_address_3'] . '<br />';
					if (isset($temp[$value . '_town_city']) && $temp[$value . '_town_city'] != '')
						$address .= $temp[$value . '_town_city'];

					if (isset($temp[$value . '_province_state']))
						$address .= ' ' . $temp[$value . '_province_state'] . ' ';
					else if (isset($temp[$value . '_province_state_id']) && isset($temp[$value . '_country_id'])) {
						$keytemp = $temp[$value . '_province_state_id'];
						$provkey = $this->province($temp[$value . '_country_id']);
						if (isset($provkey[$temp]))
							$address .= ' ' . $provkey[$temp] . ' ';
					}


					if (isset($temp[$value . '_zip_postcode']) && $temp[$value . '_zip_postcode'] != '')
						$address .= $temp[$value . '_zip_postcode'];

					if (isset($temp[$value . '_country']) && isset($temp[$value . '_country_id']) && (int) $temp[$value . '_country_id'] != "CA")
						$address .= ' ' . $temp[$value . '_country'] . '<br />';
					$arr_address[$value] = $address;
				}
			}
			if (empty($arr_address['shipping'])) {
				$arr_address['shipping'] = $arr_address['invoice'];
			}
			$shipping_contact_name = isset($query['shipping_address'][0]['shipping_contact_name']) ? $query['shipping_address'][0]['shipping_contact_name'] : '';
	    	$arr_pdf['left_info'] = array(
	    	                               'label'=> 'Shipping address: ',
	    	                               'name'=>$shipping_contact_name ? $shipping_contact_name : (isset($query['company_name']) ? $query['company_name'] : (isset($query['contact_name']) ? $query['contact_name'] : '')),
	    	                               'address' => $arr_address['invoice'],
	    	                               );

			if(isset($arr_address['shipping'])&&strlen($arr_address['shipping'])>20)
				$arr_pdf['left_info']['address'] = $arr_address['shipping'];

	    	$arr_pdf['right_info'] = array(
	    	                               'name'=>(isset($query['our_rep']) ? '<span class="bold_text">Our Rep: </span>'.$query['our_rep'] : ''),
	    	                               'address' => (isset($query['our_csr']) ? '<span class="bold_text">Our CSR: </span>'.$query['our_csr'] : '')
	    	                               );
	    	$arr_pdf['main_info'] = array(
	    	                              'Sales Order: '=>$query['code'],
	    	                              'Date: '		=>date('M d, Y',$query['salesorder_date']->sec),
	    	                              'Term: '		=>$query['payment_terms'],
	    	                              'Required date: '=>date('M d, Y',$query['payment_due_date']->sec),
	    	                              );
	    	if($query['job_number']!='')
	    		$arr_pdf['main_info']['Job no: '] = $query['job_number'];
	    	//End basic info
	    	$arr_order = $arr_task_status = array();
	    	$this->selectModel('Task');
			$arr_task = $this->Task->select_all(array(
				'arr_where' => array('salesorder_id' => new MongoId($id)),
				'arr_order' => $arr_order
			));
			foreach ($arr_task as $key => $value) {
				$arr_task_status[(string)$value['our_rep_id']] = $value['status'];
			}
	    	//Lấy Asset Tag từ hàm asset_tags_data(), hàm này đã lọc và tính production time
	    	$asset_tag = $this->asset_tags_data($id,'all',true); //true de chi in repair quantity
	    	if(!empty($asset_tag)){
	    		//Những record có xcss là những record cha, xem ở tab Asset Tag SO
	    		//Lọc ra đưa riêng vào 1 mảng
	    		//$arr_parent là mảng chứa các product cha
	    		//Giờ cấu trúc mảng $arr_tmp sẽ là array('asset_tag1'=>array(),'asset_tag2'=>array())
	    		$arr_parent = $arr_count = $arr_completed = $dont_view = array();
	    		foreach($asset_tag as $key=>$value){
	    			if (in_array($value['tag'], array('05.Finishing', '05. Finishing', '06. Fullfilment'))) {
	    				continue;
	    			}
	    			//Tách riêng $arr_tmp vào $arr_parent, với khoá là số thứ tự trong line entry
	    			$tmp_tag_key = (string)$value['tag_key'];
	    			if(isset($arr_task_status[$tmp_tag_key]) && $arr_task_status[$tmp_tag_key]=='Completed'){
	    				if(isset($arr_count[$value['tag']]))
	    					$arr_count[$value['tag']] +=1;//dem so luong asset co task completed
	    				else
	    					$arr_count[$value['tag']] =1;
	    			}
	    			if(isset($value['completed']) && $value['completed']==1){
	    				$dont_view[] = $value['tag'];
	    				continue;
	    			}
	    			if(isset($value['is_line']) ){
	    				$key = $value['product_key'];
	    				$arr_parent[$key] = $value;
	    			}
	    			else
	    				$arr_tmp[$value['tag']][] = $value;
	    		}
	    		//unset task completed
	    		foreach ($arr_count as $key => $value){
	    			if($value>1 && isset($arr_tmp[$key][0]))
	    				continue;
	    			unset($arr_tmp[$key]);
	    			//$dont_view[] = $key;
	    		}
	    		if(!empty($arr_parent)){
    				//================================Lấy line entry trước================================================================
    				if(!empty($arr_parent)){
    					$options = array();
						if(isset($query['options']) && !empty($query['options']) )
							$options = $query['options'];
    					$arr_product_option = array();
    					$i = 0;
    					$count = count($arr_parent);
    					$html = '';
    					foreach($arr_parent as $parent_key=>$value){
    						$product_key = $value['product_key'];
    						//Xem có details không để gắn thêm vào name
    						if(isset($query['products'])){
    							$value['products_name'] .= (isset($query['products'][$product_key]['details']) ? '<br /><span style="font-style:italic;font-size: 12px;">'.nl2br($query['products'][$product_key]['details']).'</span>' : '');
    							$arr_parent[$parent_key] = $value;
    						}
				    		//Tìm special_order từ PRODUCTS
			    			if(isset($value['product_id'])&&is_object($value['product_id'])){
			    				$product = $this->Product->select_one(array('_id'=>new MongoId($value['product_id'])),array('special_order'));
			    				if(isset($product['special_order'])&&$product['special_order']==1)
			    					$arr_product_option[$product_key][] = $value;
			    			}
			    			//End special order
				    		//Từ $arr_parent tìm các product option để hiển thị kèm theo
				    		foreach($query['products'] as $key=>$opt_value){
				    			if(isset($opt_value['deleted'])&&$opt_value['deleted']){
				    				continue;
				    			}
				    			if(!isset($opt_value['option_for'])||!is_numeric($opt_value['option_for'])){
				    				continue;
				    			}
				    			if($opt_value['option_for']!=$product_key) continue;
				    			$extra_name = '';
				    			//Xem có details không để gắn thêm vào name
                            	//Nếu là option same_parent thì số lượng custom sẽ gắn thêm vào name
                                //Tìm special_order từ PRODUCTS - option
				    			if(isset($opt_value['products_id'])&&is_object($opt_value['products_id'])){
				    				$product = $this->Product->select_one(array('_id'=>new MongoId($opt_value['products_id'])));
				    				if(isset($product['special_order'])&&$product['special_order']==1)
				    					$arr_product_option[$product_key][] = $opt_value;
				    			}
				    			//End special order
                                $opt_value['products_name'] .= $extra_name;
                                //================================================================================================
				    		}
			    		}
			    		if(!empty($arr_product_option)){

			    			$html = '
				    			<table class="table_content asset_table">
				    				<tr>
				    					<td class="center_text">SPECIAL ORDER</td>
				    				</tr>
				    			</table><br />
				    			<table class="table_content">
				    				<tr class="tr_right_none" style="background: #911b12;color: white;height: 29px;line-height: 29px;font-weight: bold;">
			    						<td>SKU</td>
				    					<td style="width:50%">Name</td>
				    					<td class="right_text">Width</td>
				    					<td class="right_text">Height</td>
				    					<td class="right_text">Quantity</td>
				    				</tr>';
		    				$i = 0;
			    			foreach($arr_product_option as $value){
			    				foreach($value as $val){
			    					$val['oum'] = strtolower($val['oum']);
			    					$html .= '
				    				<tr class="'.($i%2==0 ? 'bg_2' : 'bg_1').' content_asset">
				    					<td>'.$val['sku'].'</td>
				    					<td>'.$val['products_name'].'</td>
				    					<td class="right_text">'.(strpos($val['sizew'], @$val['sizew_unit'])===false ? $val['sizew'].' '.@$val['sizew_unit'] : $val['sizew']).'</td>
				    					<td class="right_text">'.(strpos($val['sizeh'], @$val['sizeh_unit'])===false ? $val['sizeh'].' '.@$val['sizeh_unit'] : $val['sizeh']).'</td>
				    					<td class="right_text">'.$val['quantity'].'</td>
				    				</tr>';
				    				$i++;
			    				}
			    			}
			    			$html .= '
		    					</table>
				    			<div style="clear:right;padding-bottom:25px"></div>
							    <div style="page-break-after:always;"></div>';
	    					$arr_html[] = array(
	    					                    'report_name'=>'
                                    				Docket #: <span class="color_red">'.$query['code'].'</span>
                                    				<div class="bold_txt" style="margin-top:5px;">(Special Order)</div>',
                                    			'html'=>$html,
                                    			'qr_url'=>'https://chart.googleapis.com/chart?chs=150x150&cht=qr&chl='.URL.'/salesorders/entry/'.$id.'&choe=UTF-8',
	    					                    );
	    					$arr_pages[] = 'Special Order';
			    		}
    				}
	    			//$arr_tmp là mảng của các Asset Tag
	    			if(!empty($arr_tmp)){
	    				ksort($arr_tmp);
						foreach($arr_tmp as $asset_key=>$value){
							if(isset($value[0]))
							foreach($value as $val){
								//Nếu có for_line tức là product option, có mặt ở line entry
								//Nếu có for_line_no tức là asset tag lấy từ đệ quy, với product không nằm trong line entry
								//Nếu là line_no tức là asset của chính nó
								$key = (isset($val['for_line']) ? $val['for_line'] : (isset($val['for_line_no']) ? $val['for_line_no'] : (isset($val['line_no']) ? $val['line_no'] : -1)));
								//Dựa vào key của arr_parent để tách ra
								//Trùng key thì có liên quan
								//$arr_parent[$key]['print'] chỉ in những uncheck line entry
								if(isset($arr_parent[$key])&&$arr_parent[$key]['print']){
									$arr_data[$asset_key][$key]['child'][] = $val;
									$arr_data[$asset_key][$key]['parent'] = $arr_parent[$key];
								}
							}
						}
	    			}
	    		}
	    	}
	    	$count = count($arr_data);
	    	$arr_asset_task = array();
			$num = 0;
			$no = 0;
	    	//==============================Lọc theo ASSET TAG================================================================
			//Lấy Task để đưa vào QRCODE
	    	$this->selectModel('Task');
	    	foreach($arr_data as $key => $value){
	    		$arr_asset_task[] = $key;
	    		$html = '
		    			<table class="table_content asset_table">
		    				<tr>
		    					<td style="border-right:none !important;">'.$key.'</td>
		    					<td class="right_text">Ref no: '.$query['code'].' - '.++$num.'</td>
		    				</tr>
		    			</table><br />
		    			<table class="table_content asset_product">
		    				<tr style="background: #979797;color: white;height: 29px;line-height: 29px;font-weight: bold;">
		    						<td>SKU</td>
			    					<td style="width:50%">Name</td>
			    					<td class="right_text">Width</td>
			    					<td class="right_text">Height</td>
			    					<td class="right_text">Quantity</td>
			    					<td class="right_text">Production time</td>
		    					</tr>';
		    	$total = 0;
	    		foreach($value as $val){
		    		$i = 0;
		    			$html .=
		    				'<tr class="'.($i%2==0 ? 'bg_2' : 'bg_1').' content_asset parent_product" style="height: 35px;line-height: 35px;">
		    					<td>'.$val['parent']['sku'].'</td>
		    					<td>'.$val['parent']['products_name'].'</td>
		    					<td class="right_text">'.$val['parent']['sizew'].'</td>
		    					<td class="right_text">'.$val['parent']['sizeh'].'</td>
		    					<td></td>
		    					<td></td>
		    				</tr>';
		    		$i++;
		    		if(isset($val['child'])&&!empty($val['child'])){
			    		foreach($val['child'] as $child){
			    			if(isset($child['line_no'])){
			    				if(isset($options['filter_name'][$child['line_no']]))
			    					$child['products_name'] .= $options['filter_name'][$child['line_no']];
				    			if(isset($query['products'][$child['line_no']])){
				    				$this_line = $query['products'][$child['line_no']];
				    				if(isset($this_line['details']))
				    					$child['products_name'] .='<br /><span style="font-style:italic;font-size: 12px;">'.nl2br($this_line['details']).'</span>';
				    			}
				    		}
			    			$html .= '
			    					<tr class="'.($i%2==0 ? 'bg_2' : 'bg_1').' content_asset">
			    						<td>'.$child['sku'].'</td>
			    						<td>'.$child['products_name'].'</td>
			    						<td class="right_text">'.$child['sizew'].'</td>
			    						<td class="right_text">'.$child['sizeh'].'</td>
			    						<td class="right_text">'.$child['quantity'].'</td>
			    						<td class="right_text">'.$child['production_time'].'</td>
			    					</tr>';
			    			$total += $child['production_time'];
			    			$i++;
			    		}
		    		}

	    		}
	    		$html .= '
	    				<tr class="'.($i%2==0 ? 'bg_2' : 'bg_1').'">
	    					<td class="bold_text" colspan="4"></td>
	    					<td class="bold_text right_text">Total:</td>
	    					<td class="bold_text right_text">'.$total.'</td>
	    				</tr>
	    			</table>';
	    		//Nếu là table cuối cùng thì ko cần page-break
	    		$html .= '<div style="page-break-after:always;"></div>';
	    		$arr_tmp = array('html'=>$html);
	    		$task = $this->Task->select_one(array('salesorder_id'=>new MongoId($id),'our_rep'=>$key));
	    		if(!empty($task)&&is_object($task['_id'])){
	    			$url = 'https://chart.googleapis.com/chart?chs=150x150&cht=qr&chl='.URL.'/mobile/tasks/entry/'.$task['_id'].'&choe=UTF-8';
				    $arr_tmp['qr_url'] = $url;
				    $arr_tmp['custom_main_info'] = '<table >
	    										<tr>
	    											<td class="bold_text">Job no:</td><td>'.$query['job_number'].'</td>
	    										</tr>
	    										<tr>
	    											<td class="bold_text">Date in:</td><td>'.date('D. M d, Y',$query['salesorder_date']->sec).'</td>
	    										</tr>
	    										<tr>
	    											<td class="bold_text">Date due:</td><td>'.date('H:i, D. M d, Y',$task['work_end']->sec).'</td>
	    										</tr>
	    										<tr>
	    											<td class="bold_text">Term:</td><td>'.$query['payment_terms'].' day'.($query['payment_terms'] > 1 ? 's' : '').'</td>
	    										</tr>
	    										<tr>
	    											<td class="bold_text">Delivery:</td><td>'.(isset($query['delivery_method']) ? $query['delivery_method'] : '').'</td>
	    										</tr>
	    									</table>';
				}
				$arr_tmp['report_name'] = '
								Dkt. <span class="color_red">'.$query['code'].' - '.++$no.' of [@COUNT]</span>
                                <div class="bold_txt" style="margin-top:5px;">(Asset tags)</div>';
	    		$arr_html[] = $arr_tmp;
	    		$arr_pages[] = $key;

	    	}
	    	$dont_view = array_merge((array)$dont_view,(array)$arr_asset_task, array('05.Finishing', '05. Finishing', '06. Fulfillment'));
	    	$arr_custom_tasks = $this->Task->select_all(array(
	    			'arr_where' => array(
	    					'our_rep' => array('$nin' => $dont_view),
	    					'our_rep_type' => 'assets',
	    					'salesorder_id' => new MongoId($id),
	    					'status' => array('$nin' => array('DONE')),
	    				),
	    			'arr_order' => array('our_rep' => 1)
	    		));
	    	if($arr_custom_tasks->count()) {
	    		$arr_html[($count -1)]['html'] .= '<div style="page-break-after:always;"></div>';
	    		$count += $arr_custom_tasks->count();
	    		foreach($arr_custom_tasks as $custom_task) {
	    			$contact = isset($custom_task['contacts']) ? $custom_task['contacts'] : array();
	    			if(!empty($contact)) {
	    				$contact = array_filter($contact, function($arr){
	    					return isset($arr['deleted'])&&!$arr['deleted']&&$arr['default'];
	    				});
	    				if(!empty($contact)) {
		    				$contact = reset($contact);
		    				$contact = $contact['contact_name'];
	    				}
	    			}
	    			if(empty($contact)) {
	    				$contact = $this->Contact->select_one(array('_id' => $custom_task['created_by']),array('first_name','last_name'));
	    				$contact = (isset($contact['first_name']) ? $contact['first_name'].' ' : '' ).(isset($contact['last_name']) ? $contact['last_name'] : '' );
	    			}
	    			$html = '
		    			<table class="table_content asset_table">
		    				<tr>
		    					<td style="border-right:none !important;">'.$custom_task['our_rep'].'</td>
		    					<td class="right_text">Ref no: '.$query['code'].' - '.++$num.'</td>
		    				</tr>
		    			</table><br />
		    			<table class="table_content asset_product">
		    				<tr style="background: #979797;color: white;height: 29px;line-height: 29px;font-weight: bold;">
		    					<td style="width:50%">Contact</td>
		    					<td>Type</td>
		    					<td class="right_text">Production time</td>
	    					</tr>
	    					<tr class="'.($i%2==0 ? 'bg_2' : 'bg_1').' content_asset">
		    					<td style="width:50%">'.$contact.'</td>
		    					<td>'.(isset($custom_task['type']) ? $custom_task['type'] : '').'</td>
		    					<td class="right_text">'.$this->sec2hour($custom_task['work_end']->sec - $custom_task['work_start']->sec).'</td>
	    					</tr>
	    				</table>
	    				<div style="page-break-after:always;"></div>';
		    		$i++;
		    		$arr_tmp = array('html'=>$html);
		    		$url = 'https://chart.googleapis.com/chart?chs=150x150&cht=qr&chl='.URL.'/mobile/tasks/entry/'.$custom_task['_id'].'&choe=UTF-8';
				    $arr_tmp['qr_url'] = $url;
				    $arr_tmp['custom_main_info'] = '<table>
	    										<tr>
	    											<td class="bold_text">Job no:</td><td>'.$query['job_number'].'</td>
	    										</tr>
	    										<tr>
	    											<td class="bold_text">Date in:</td><td>'.date('D. M d, Y',$query['salesorder_date']->sec).'</td>
	    										</tr>
	    										<tr>
	    											<td class="bold_text">Date due:</td><td>'.date('H:i, D. M d, Y',$custom_task['work_end']->sec).'</td>
	    										</tr>
	    										<tr>
	    											<td class="bold_text">Term:</td><td>'.$query['payment_terms'].' day'.($query['payment_terms'] > 1 ? 's' : '').'</td>
	    										</tr>
	    										<tr>
	    											<td class="bold_text">Delivery:</td><td>'.(isset($query['delivery_method']) ? $query['delivery_method'] : '').'</td>
	    										</tr>
	    									</table>';
	    			$arr_tmp['report_name'] = '
								Dkt. <span class="color_red">'.$query['code'].' - '.++$no.' of [@COUNT]</span>
                                <div class="bold_txt" style="margin-top:5px;">(Asset tags)</div>';
	    			$arr_html[] = $arr_tmp;
	    			$arr_pages[] = $custom_task['our_rep'];
	    		}
	    	}
	    	//=========================Last page========================================
			$i = 0;
			$html = '
	    			<table class="table_content">
	    				<tr class="tr_right_none" style="background: #911b12;color: white;height: 29px;line-height: 29px;font-weight: bold;">
    						<td>SKU</td>
	    					<td>Description</td>
	    					<td class="right_text">Width</td>
	    					<td class="right_text">Height</td>
	    					<td class="right_text">Qty</td>
	    					<td class="right_text" width="5">Count</td>
	    				</tr>';
			foreach($arr_parent as $parent_key=>$value) {
				$product_key = $value['product_key'];
				//Xem có details không để gắn thêm vào name
				if(isset($query['products'])){
					$value['products_name'] .= (isset($query['products'][$product_key]['details']) ? '<br /><span style="font-style:italic;font-size: 12px;">'.nl2br($query['products'][$product_key]['details']).'</span>' : '');
					$arr_parent[$parent_key] = $value;
				}
				$html .= '
	    				<tr class="'.($i%2==0 ? 'bg_2' : 'bg_1').' content_asset">
	    					<td>'.$value['sku'].'</td>
	    					<td>'.$value['products_name'].'</td>
	    					<td class="right_text">'.$value['sizew'].'</td>
	    					<td class="right_text">'.$value['sizeh'].'</td>
	    					<td class="right_text">'.$value['quantity'].'</td>
	    					<td>_________</td>
	    				</tr>';
	    		$i++;
	    		//Từ $arr_parent tìm các product option để hiển thị kèm theo
	    		foreach($query['products'] as $key=>$opt_value){
	    			if(isset($opt_value['deleted'])&&$opt_value['deleted']){
	    				continue;
	    			}
	    			if(!isset($opt_value['option_for'])||!is_numeric($opt_value['option_for'])){
	    				continue;
	    			}
	    			if(isset($opt_value['hidden']) && $opt_value['hidden']) {
	    				continue;
	    			}
	    			if($opt_value['option_for']!=$product_key) continue;
	    			$extra_name = '';
	    			//Xem có details không để gắn thêm vào name
    				if(isset($opt_value['details']))
                		$extra_name = '<br /><span style="margin-left:15px;font-style:italic;font-size: 12px;">'.nl2br($opt_value['details']).'</span>';
                    $opt_value['products_name'] .= $extra_name;
                    //================================================================================================
	    			$html .= '
	    				<tr class="'.($i%2==0 ? 'bg_2' : 'bg_1').' content_asset">
	    					<td></td>
	    					<td>&nbsp;&nbsp;&nbsp;•'.$opt_value['products_name'];
	    			$html .= '</td>';
	    			if(!isset($opt_value['same_parent']) || $opt_value['same_parent']==0){
	    				$html .= '
	    					<td class="right_text">'.($opt_value['oum']!='unit' ? $opt_value['sizew'].' '.$opt_value['sizew_unit'] : '').'</td>
	    					<td class="right_text">'.($opt_value['oum']!='unit' ? $opt_value['sizeh'].' '.$opt_value['sizeh_unit'] : '').'</td>
	    					<td class="right_text">'.$opt_value['quantity'].'</td>
	    					<td>_________</td>
	    				</tr>';
	    			} else {
	    				$html .= '
	    					<td class="right_text"></td>
	    					<td class="right_text"></td>
	    					<td class="right_text"></td>
	    					<td></td>
	    				</tr>';
	    			}
	    			$i++;
	    		}
    		} ///sfafasd
    		$comment = isset($query['shipping_comment']) ? $query['shipping_comment'] : '';
		    $html .= '</table>
                	<div class="row" id="note">
                    	<strong>Note:</strong>
                    	'. $comment .'
                		<div></div>
                		<div></div>
                		<div></div>
                	</div>
		    		<div style="clear:right;padding-bottom:25px"></div>';
	    	$arr_tmp = array(
	    		'default_top_left_info' => true,
	    		'custom_left_info' => '<div style="padding: 10px 0 20px;">
                                       	<p style="font-weight:bold; text-decoration: underline;">Shipping to: </p>'.
                                       	(isset($shipping_contact_name) && !empty($shipping_contact_name) ? '<p class="bold_text">'.$shipping_contact_name.'</p>' : '<p class="bold_text">'.$query['company_name'].'</p>')
	    								. $arr_address['shipping'] .
                                    '</div>
                                    <table>
	    									<tr>
	    										<td style="border-bottom: 1px solid #cbcbcb;" colspan="2">&nbsp;</td>
    										</tr>
    										<tr>
    											<td style="padding-top: 15px;" class="bold_text">Docket #:</td><td>'. $query['code'] .'</td>
    										</tr>
    										<tr>
    											<td class="bold_text">Method of shipping:</td><td>'.(isset($query['delivery_method']) ? $query['delivery_method'] : '').'</td>
    										</tr>
	    									<tr>
	    										<td colspan="2">&nbsp;</td>
    										</tr>
	    									<tr>
    											<td style="padding-top: 15px;" height="35px">Package:</td><td>_________ of _________</td>
    										</tr>
    										<tr>
    											<td height="35px" style="">Packed by:</td><td>____________________</td>
    										</tr>
    								</table>',
	    		'custom_main_info' => '<table style="float: right; margin-top: 15px;">
    										<tr>
    											<td class="bold_text">Job no:</td><td>'.$query['job_number'].'</td>
    										</tr>
    										<tr>
    											<td class="bold_text">Date:</td><td>'.date('d M, Y',$query['salesorder_date']->sec).'</td>
    										</tr>
    										<tr>
    											<td class="bold_text">Customer PO no:</td><td>'. (isset($query['customer_po_no']) ? $query['customer_po_no'] : '') .'</td>
    										</tr>
    									</table>
    									<p style="clear: right;"></p>
    									<div style="padding: 10px 0 20px; padding-right: 90px; float: right; text-align: left; border-bottom: 1px solid #cbcbcb;">
	                                       	<p style="font-weight:bold; text-decoration: underline;">Billing address: </p>' .
	                                       	(isset($query['company_name']) ? '<p class="bold_text">'.$query['company_name'].'</p>' : '')
		    								. $arr_address['invoice'] .
	                                    '</div>',
    			'report_name'	=>	'Packing Slip',
	    		'html' => $html
	    		);
	    	$arr_html[] = $arr_tmp;
	    	$arr_pages[] = 'Packing Slip';
	    	foreach($arr_html as $key => $html){
	    		$arr_html[$key]['report_name'] = str_replace('[@COUNT]', $no, $arr_html[$key]['report_name']);
	    	}
	    	$client_name = (isset($query['company_name']) ? $query['company_name'] : (isset($query['contact_name']) ? $query['contact_name'] : ''));
	    	$arr_pdf['custom_top_left_info'] = '<table>
	    											<tr>
	    												<td class="bold_text">Client:</td><td class="bold_text">'.strtoupper($client_name).'</td>
	    											</tr>
	    											<tr>
	    												<td class="bold_text">Contact:</td><td>'.(isset($query['contact_name']) ? $query['contact_name'] : '').'</td>
	    											</tr>
	    											<tr>
	    												<td class="bold_text">Phone:</td><td>'.(isset($query['phone']) ? $query['phone'] : '').'</td>
	    											</tr>
	    											<tr>
	    												<td class="bold_text">PO No:</td><td>'.(isset($query['customer_po_no']) ? $query['customer_po_no'] : '').'</td>
	    											</tr>
	    										</table>';
	    	$arr_pdf['custom_main_info'] = '<table >
	    										<tr>
	    											<td class="bold_text">Job no:</td><td>'.$query['job_number'].'</td>
	    										</tr>
	    										<tr>
	    											<td class="bold_text">Date in:</td><td>'.date('D. M d, Y',$query['salesorder_date']->sec).'</td>
	    										</tr>
	    										<tr>
	    											<td class="bold_text">Date due:</td><td>'.date('D. M d, Y',$query['payment_due_date']->sec).'</td>
	    										</tr>
	    										<tr>
	    											<td class="bold_text">Term:</td><td>'.$query['payment_terms'].' day'.($query['payment_terms'] > 1 ? 's' : '').'</td>
	    										</tr>
	    										<tr>
	    											<td class="bold_text">Delivery:</td><td>'.(isset($query['delivery_method']) ? $query['delivery_method'] : '').'</td>
	    										</tr>
	    									</table>';
	    	$arr_pdf['is_custom'] = true;
	    	$arr_pdf['content'] = $arr_html;
	        $arr_pdf['report_name'] = 'SO Dockets';
	        $arr_pdf['report_size'] = '8.5in*11in';
	        $arr_pdf['pages'] = $arr_pages;
	        if(isset($query['heading']) && !empty($query['heading']))
	        	$arr_pdf['report_heading'] = $query['heading'];
			$numkey = explode("-",$query['code']);
	        $arr_pdf['report_file_name']='DKT-'.$numkey[count($numkey)-1];
	        //$arr_pdf['report_orientation'] = 'landscape';
	        Cache::write('docket_report', $arr_pdf);
	    } else {
	    	//Vì quá trình xử lý khá lâu, nhiều khâu nên phải đưa vào cached, tăng tốc độ render cho PhantomJS generate PDF đầy đủ ko thiêu thông tin
	    	$arr_pdf = Cache::read('docket_report');
	    	if (isset($_GET['pages'])) {
	    		foreach($arr_pdf['content'] as $index => $html) {
	    			if (!in_array($index, $_GET['pages'])) {
	    				unset($arr_pdf['content'][$index]);
	    			}
	    		}
	    	}
	    	if (isset($arr_pdf['pages'])) {
	    		unset($arr_pdf['pages']);
	    	}
	    	Cache::delete('docket_report');
	    }
        $this->render_pdf($arr_pdf);
    }

    function sec2hour($time){
		$hours = floor($time / 3600);
		$mins = floor(($time - ($hours*3600)) / 60);
		$mins = $mins == 30 ? 5 : 0;
		return "$hours.$mins";
	}

	function missing_tax()
    {
    	$orders = $this->opm->select_all(array(
    					'arr_where' => array(
    							'_id' => array( '$gt' => new MongoId( str_pad(dechex(strtotime('2015-04-01 00:00:00')), 8, '0', STR_PAD_LEFT).'0000000000000000'  ) ),
    							'taxval' => array( '$gt' => 0 ),
    							'products' => array('$elemMatch' => array(
    																	'deleted' => false,
    																	'taxper'  => 0
    								))
    						),
    					'arr_field' => array('code'),
    					'arr_order' => array('_id' => 1)
    		));
    	$html = '';
    	foreach($orders as $order) {
    		$html .= 'Order <a href="'.URL.'/salesorders/entry/'.$order['_id'].'" target="_blank">#'.$order['code'].'</a><br/>';
    	}
    	echo $html;
    	die;
    }

    function get_sum_list()
    {
		$sort_field = '_id';
		$sort_type = 1;
		$arr_where = $this->arr_search_where();
		$arr_where = array_merge($arr_where, array( 'status' => array('$nin' => array('Cancelled'))));
		$orders = $this->opm->select_all(array(
			'arr_where' => $arr_where,
			'arr_field'	=> array('quotation_id', 'company_id', 'sum_sub_total'),
		));
		$sum_quotation = $sum_order = 0;
		$arr_companies = Cache::read('arr_companies');
	    if(!$arr_companies)
	        $arr_companies = array();
	    $count = count($arr_companies);
	    $original_minimum = Cache::read('minimum');
	    $product = Cache::read('minimum_product');
	    $this->selectModel('Company');
	    if(!$original_minimum){
	        $this->selectModel('Product');
	        $this->selectModel('Stuffs');
	        $original_minimum = 50;
	        $product = $this->Stuffs->select_one(array('value'=>"Minimum Order Adjustment"),array('product_id'));
	        $p = $this->Product->select_one(array('_id'=> new MongoId($product['product_id'])),array('sell_price'));
	        if(isset($p['sell_price']))
	            $original_minimum = (float)$p['sell_price'];
	        Cache::write('minimum',$original_minimum);
	        Cache::write('minimum_product',$product);
	    }
		if($orders->count()){
			$this->selectModel('Quotation');
			foreach($orders as $order){
				if(!isset($order['company_id']))
                	$order['company_id'] = '';
				if(!isset($arr_companies[(string)$order['company_id']])){
	                $minimum = $original_minimum;
	                if(is_object($order['company_id'])){
	                    $company = $this->Company->select_one(array('_id'=>new MongoId($order['company_id'])),array('pricing'));
	                    if(isset($company['pricing'])&&!empty($company['pricing'])){
	                        foreach($company['pricing'] as $pricing){
	                            if(isset($pricing['deleted'])&&$pricing['deleted']) continue;
	                            if((string)$pricing['product_id']!=(string)$product['product_id']) continue;
	                            if(!isset($pricing['price_break']) || empty($pricing['price_break'])) continue;
	                            $price_break = reset($pricing['price_break']);
	                            $minimum = (float)$price_break['unit_price']; break;
	                        }
	                    }
	                } else {
	                    $minimum = $original_minimum;
	                }
	                $arr_companies[(string)$order['company_id']] = $minimum;
	            } else
	                $minimum = $arr_companies[(string)$order['company_id']];
	            $order['sum_sub_total'] = (float)$order['sum_sub_total'];
	            if($order['sum_sub_total'] < $minimum){
	            	$order['sum_sub_total'] = $minimum;
	            }
	            $sum_order += $order['sum_sub_total'];
				//====================================================================================
				if(!isset($order['quotation_id']) || !is_object($order['quotation_id'])) continue;
				$quotes = $this->Quotation->select_all(array(
							'arr_where' => array(
										'_id' => $order['quotation_id'],
										'status' => array('$nin' => array('Cancelled')),
										),
							'arr_field' => array('company_id','sum_sub_total'),
					));
				foreach($quotes as $quote){
					if(!isset($quote['company_id']))
	                	$quote['company_id'] = '';
					if(!isset($arr_companies[(string)$quote['company_id']])){
		                $minimum = $original_minimum;
		                if(is_object($quote['company_id'])){
		                    $company = $this->Company->select_one(array('_id'=>new MongoId($quote['company_id'])),array('pricing'));
		                    if(isset($company['pricing'])&&!empty($company['pricing'])){
		                        foreach($company['pricing'] as $pricing){
		                            if(isset($pricing['deleted'])&&$pricing['deleted']) continue;
		                            if((string)$pricing['product_id']!=(string)$product['product_id']) continue;
		                            if(!isset($pricing['price_break']) || empty($pricing['price_break'])) continue;
		                            $price_break = reset($pricing['price_break']);
		                            $minimum = (float)$price_break['unit_price']; break;
		                        }
		                    }
		                } else {
		                    $minimum = $original_minimum;
		                }
		                $arr_companies[(string)$quote['company_id']] = $minimum;
		            } else
		                $minimum = $arr_companies[(string)$quote['company_id']];
		            $quote['sum_sub_total'] = (float)$quote['sum_sub_total'];
		            if($quote['sum_sub_total'] < $minimum){
		            	$quote['sum_sub_total'] = $minimum;
		            }
		            $sum_quotation += $quote['sum_sub_total'];
				}
			}
			if(count($arr_companies) > $count){
	   	 		Cache::write('arr_companies',$arr_companies);
			}
		}
		echo json_encode(array('quotation' => $this->opm->format_currency($sum_quotation), 'sales' => $this->opm->format_currency($sum_order)));
		die;
    }

    public function get_last_asset_status()
    {
    	$id = new MongoId($this->get_id());
    	$status = '';
    	$this->selectModel('Task');
    	$arrWhere = array(
    		'delete' 		=> false,
    		'salesorder_id' => $id
    	);
    	if (!$this->opm->collection->count(array('_id' => $id, 'status' => 'Completed'))) {
    		$task = $this->Task->select_one(array(
    									'salesorder_id' => $id,
    									'our_rep_type' => 'assets',
    									'status' => array(
    										'$nin' => array(
    												'New',
    												'Cancelled'
    											)
    										)
    								), array(
    									'our_rep', 'status'
    								), array(
    									'date_modified' => -1
    								));
    		if (!empty($task)) {
    			$task = array_merge(array('our_rep' => '', 'status' => ''), $task);
    			$status = $task['our_rep'].' - '.$task['status'];
    		}
    	}
    	echo $status;
    	die;

    }

    public function find_incomplete_sku()
    {
    	if(!$this->check_permission($this->name.'_@_options_@_find_selected_incomplete_sku'))
			$this->error_auth();
    }

    public function production_report()
    {
    	$arr_data = array();
        if(isset($_GET['print_pdf'])){
            $arr_data = Cache::read('production_report');
            Cache::delete('production_report');
        } else {
			if(isset($_POST) && !empty($_POST)){
				$arr_post = $_POST;
	            $arr_post = $this->Common->strip_search($arr_post);
                $arr_where = array();
                $arr_where['deleted'] = false;
	            if(!$this->check_permission('salesorders_@_view_worktraq_@_view')){
	            	$arr_where['code'] = array('$not' => new MongoRegex('/WT-/'));
	            }
				$arr_where['status'] = array(
    										'$nin' => array(
    												'Completed',
    												'Cancelled'
    											)
    										);
				if(isset($arr_post['company']) && !empty($arr_post['company']))
					$arr_where['company_name'] = new MongoRegex('/'.trim($arr_post['company']).'/i');
				if(isset($arr_post['contact']) && !empty($arr_post['contact']))
					$arr_where['contact_name'] = new MongoRegex('/'.trim($arr_post['contact']).'/i');
				if(isset($arr_post['job_no']) && $arr_post['job_no']!='')
					$arr_where['job_number'] = $arr_post['job_no'];
				//tim chinh xac ngay
				if(isset($arr_post['date_equals']) && !empty($arr_post['date_equals'])){
					$arr_where['salesorder_date']['$gte'] = new MongoDate($this->Common->strtotime($arr_post['date_equals'] . '00:00:00'));
					$arr_where['salesorder_date']['$lt'] = new MongoDate($this->Common->strtotime($arr_post['date_equals'] . '23:59:59'));
				} else { //ngay nam trong khoang
					//neu chi nhap date from
					if(isset($arr_post['date_from']) && !empty($arr_post['date_from'])){
						$arr_where['salesorder_date']['$gte'] = new MongoDate($this->Common->strtotime($arr_post['date_from'] . '00:00:00'));
					}
					//neu chi nhap date to
					if( isset($arr_post['date_to']) && !empty($arr_post['date_to']) ){
						$arr_where['salesorder_date']['$lte'] = new MongoDate($this->Common->strtotime($arr_post['date_to'] . '23:59:59'));
					}
				}
				if(isset($arr_post['our_rep']) && !empty($arr_post['our_rep']))
					$arr_where['our_rep'] = new MongoRegex('/'.$arr_post['our_rep'].'/i');
				if(isset($arr_post['our_csr']) && !empty($arr_post['our_csr']))
					$arr_where['our_csr'] = new MongoRegex('/'.$arr_post['our_csr'].'/i');
				$arr_where['products'] = array(
					'$elemMatch' => array(
							'deleted' => false,
							'$or' => array(
								array(
									'completed_docket' => array(
										'$exists' => false
									),
								),
								array(
									'completed_docket' => 0
								)
							)
						)
					);
				if (!empty($arr_post['product_code'])) {
					$arrCode = explode('; ', $arr_post['product_code']);
					foreach ($arrCode as $key => $code) {
						$arrCode[$key] = (int)$code;
					}
					$arr_where['products']['$elemMatch']['code']['$in'] = $arrCode;

				}
				if (!empty($arr_post['product_sku'])) {
					$arrSKU = explode('; ', $arr_post['product_sku']);
					$arr_where['products']['$elemMatch']['sku']['$in'] = $arrSKU;
				}
				$this->selectModel('Salesorder');
                $count = $this->Somonth->count($arr_where,array('limit' => 9999999));
				if($count == 0) {
					echo 'empty';
					die;
				} else {
					if($this->request->is('ajax')) {
            			die;
					}
					$orders = $this->Somonth->select_all(array(
							'arr_where' => $arr_where,
							'arr_field' => array('code', 'company_name', 'asset_status', 'payment_due_date', 'products'),
							'arr_order' => array('salesorder_date' => -1)
						));
					$html = '';
					foreach($orders as $order) {
						$order = array_merge(array('code' => '', 'company_name' => '', 'asset_status' => '', 'products' => array()), $order);
						$html .= '<table class="table_content">
					               	<tbody>
					                  	<tr class="tr_right_none" style="background: #911b12; color: white; height: 29px; line-height: 29px; font-weight: bold;">
					                     	<td width="65%"><span style="font-size: 16px; font-weight: bolder;">' . $order['code'] . '</span> - ' . $order['company_name'] . '</td>
					                     	<td>' . $order['asset_status'] . '</td>
					                     	<td class="center_text">' . $this->opm->format_date($order['payment_due_date']->sec) . '</td>
					                  	</tr>
					               </tbody>
					            </table>';
			            $html .= '<table class="table_content" style="page-break-inside:avoid;" >
			                        <tbody>
			                          	<tr class="tr_right_none no_break" style="background: #979797;color: white;height: 29px;line-height: 29px;font-weight: bold;" >
				                            <td width="10%">
				                                SKU
				                            </td>
				                            <td width="55%">
				                                Name
				                            </td>
				                            <td class="right_text" width="7%">
				                                SizeW
				                            </td>
				                            <td class="right_text" width="7%">
				                                SizeH
				                            </td>
				                            <td width="8%">
				                                Sold by
				                            </td>
				                            <td class="right_text">
				                                Quantity
				                            </td>
			                          	</tr>';
			            $i = 0;
			            foreach ($order['products'] as $product) {
			            	if ($product['deleted']) continue;
			            	if ($product['quantity'] < 0) continue;
			            	if (isset($product['completed_docket']) && $product['completed_docket']) continue;
			            	if (isset($product['same_parent']) && $product['same_parent']
			            		&& isset($order['products'][ $product['option_for'] ]['deleted'])
			            		&& !$order['products'][ $product['option_for'] ]['deleted']
			            		&& isset($order['products'][ $product['option_for'] ]['completed_docket'])
			            		&& $order['products'][ $product['option_for'] ]['completed_docket'] ) {
			            		continue;
			            	}
			            	if (isset($arr_where['products']['$elemMatch']['sku']['$in'])) {
			            		if (!isset($product['option_for'])) {
			            			if( !in_array($product['sku'], $arr_where['products']['$elemMatch']['sku']['$in'])) {
				            			continue;
				            		}
			            		} else if (isset($order['products'][ $product['option_for'] ]['deleted'])
				            		&& !$order['products'][ $product['option_for'] ]['deleted']
				            		&& !in_array($order['products'][ $product['option_for'] ]['sku'], $arr_where['products']['$elemMatch']['sku']['$in'] ) ) {
					            	continue;
			            		}
			            	}
			            	if (isset($arr_where['products']['$elemMatch']['code']['$in'])) {
			            		if (!isset($product['option_for'])) {
			            			if( !in_array($product['code'], $arr_where['products']['$elemMatch']['code']['$in'])) {
				            			continue;
				            		}
			            		} else if (isset($order['products'][ $product['option_for'] ]['deleted'])
				            		&& !$order['products'][ $product['option_for'] ]['deleted']
				            		&& !in_array($order['products'][ $product['option_for'] ]['code'], $arr_where['products']['$elemMatch']['code']['$in'] ) ) {
					            	continue;
			            		}
			            	}
			            	$product = array_merge(array(
			            								'sku'	=> '',
			            								'products_name' => '',
			            								'sizew'	=> 0,
			            								'sizeh'	=> 0,
			            								'sell_by'	=> '',
			            								'quantity'	=> 0,
			            							), $product);
			            	$sku = $product['sku'];
			            	$name = $product['products_name'];
			            	$sizew = $sizeh = '';
			            	if ($product['sizew']) {
			            		$sizew = (float)$product['sizew'].' '.$product['sizew_unit'];
			            	}
			            	if ($product['sizeh']) {
			            		$sizeh = (float)$product['sizeh'].' '.$product['sizeh_unit'];
			            	}
			            	if (isset($product['option_for']) && is_numeric($product['option_for'])) {
			            		$sku = '';
			            		$name = '&nbsp;&nbsp;&nbsp;•'.$name ;
			            	}
			            	$html .= '
			                      <tr class="content_asset no_break bg_' . ($i % 2 == 0 ? '1' : '2') . '">
			                         <td>' . $sku . '</td>
			                         <td>' . $name . '</td>
			                         <td class="right_text">' . $sizew . '</td>
			                         <td class="right_text">' . $sizeh . '</td>
			                         <td>' . ucfirst($product['sell_by']) . '</td>
			                         <td class="right_text">' . $product['quantity'] . '</td>
			                      </tr>';
			                $i++;
			            }
					    $html .=	'</tbody>
			                    </table>
			                    <br />
			                    <br />
			           			<div class="line" style="margin-bottom: 10px;"></div>
			           			<div style="page-break-before: avoid;page-break-after: auto;"></div>';
					}
        			$arr_data['is_custom'] = true;
			        $arr_data['content'][]['html'] = $html;
			        $arr_data['report_name'] = 'Incomplete SKU';
			        $arr_data['report_file_name'] = 'Incomplete_SKU_'.md5(time());
	                Cache::write('production_report', $arr_data);
				}
			}
		}
        $this->render_pdf($arr_data);
    }

    public function update_asset_status()
    {
    	$this->selectModel('Task');
    	$orders = $this->opm->select_all(array(
    			'arr_field' => array(
    					'status'
    				)
    		));
    	$i = 0;
    	foreach ($orders as $order) {
    		$status = $order['status'];
	    	if ($order['status'] != 'Completed') {
				$task = $this->Task->select_one(array(
											'salesorder_id' => $order['_id'],
											'our_rep_type' => 'assets',
											'status' => array(
												'$nin' => array(
														'New',
														'Cancelled'
													)
												)
										), array(
											'our_rep', 'status'
										), array(
											'date_modified' => -1
										));
				if (!empty($task)) {
					$task = array_merge(array('our_rep' => '', 'status' => ''), $task);
					$status = $task['our_rep'].' - '.$task['status'];
				}
			}
			$this->opm->collection->update(array('_id' => $order['_id']), array('$set' => array('asset_status' => $status)));
			$i++;
    	}
    	echo $i; die;
    }

    public function find_same_material_sku()
    {
    	if(!$this->check_permission($this->name.'_@_options_@_find_same_material_sku'))
			$this->error_auth();
    }

    public function same_material_report()
    {
    	$arr_data = array();
        if (isset($_GET['print_pdf'])){
            $arr_data = Cache::read('same_material_report');
            Cache::delete('same_material_report');
        } else {
			if(isset($_POST) && !empty($_POST)){
				$arr_post = $_POST;
	            $arr_post = $this->Common->strip_search($arr_post);
	            if (empty($arr_post['product_code']) && empty($arr_post['product_sku']) && empty($arr_post['product_name'])) {
	            	echo 'empty';
	            	die;
	            }
                $arr_where = array(
                	'deleted' => false,
                	'madeup' 	=> array(
                					'$elemMatch' => array(
                						'deleted' => false,
                						'product_id' => array(
                								'$exists' => true
                							)
                					)
                				)
                );
                if (!empty($arr_post['product_code'])) {
                	$arrCode = explode('; ', $arr_post['product_code']);
                	foreach ($arrCode as $key => $code) {
                		$arrCode[$key] = (int)$code;
                	}
                	$arr_where['code']['$in'] = $arrCode;
                }

                if (!empty($arr_post['product_sku'])) {
	            	$arrSKU = explode('; ', $arr_post['product_sku']);
	            	$arr_where['sku']['$in'] = $arrSKU;
                }

                if (!empty($arr_post['product_name'])) {
	            	$arrName = explode('; ', $arr_post['product_name']);
	            	foreach ($arrName as $key => $name) {
	            		$arrName[$key] = new MongoRegex('/'.$name.'/i');
	            	}
	            	$arr_where['name']['$in'] = $arrName;
                }

            	$this->selectModel('Product');
            	$products = $this->Product->collection->aggregate(
	        	                    array(
	        	                        '$match'=>	$arr_where,
	        	                    ),
	        	                    array(
	        	                        '$unwind'=>	'$madeup',
	        	                    ),
	        	                    array(
	        	                        '$match'=>	array(
	        	                        	'madeup.deleted' => false
	        	                        )
	        	                    ),
	        	                    array(
	        	                        '$project'=>array(
	        	                    				'madeup'		=> '$madeup',
	        	                    				'sku'			=> '$sku'
	        	                    			)
	        	                    ),
	        	                    array(
	        	                        '$group'=>array(
	        	                                      	'_id'=>array(
	        	                                      		'_id'			=> '$_id',
	        	                    						'sku'			=> '$sku'
	        	                                      	),
	        	                                      	'madeup'=>array('$push' => '$madeup')
	        	                                    )
	        	                    )
	        	                );
				if (empty($products['result'])) {
                	echo 'empty';
                	die;
                }
                $arrFoundSKU = $arrMadeupId = array();
                foreach($products['result'] as $result) {
                	$arrFoundSKU[] = $result['_id']['sku'];
                	foreach($result['madeup'] as $product) {
                		if (isset($product['delete']) && $product['delete']) continue;
                		if (!isset($product['product_id'])) continue;
                		if (!is_object($product['product_id'])) continue;
                		$arrMadeupId[] = $product['product_id'];
                	}
            	}
				$products = $this->Product->select_all(array(
                		'arr_where' => array(
                				'_id' => array(
                						'$in' => $arrMadeupId
                					),
                				'product_type' => array(
                						'$nin' => array('Internal Use', 'not used')
                					),
                				'status' => 1
                			),
                		'arr_field' => array(
                				'_id'
                			)
                	));
				if (!$products->count()) {
                	echo 'empty';
                	die;
                }
                $arrMadeupId = array();
                foreach ($products as $product) {
            		$arrMadeupId[] = $product['_id'];
                }
                $products = $this->Product->select_all(array(
                	'arr_where' => array(
                			'product_type' => array(
                					'$nin' => array('Internal Use', 'not used', 'Options')
                				),
                			'madeup' => array(
                					'$elemMatch' => array(
                							'deleted' => false,
                							'product_id' => array(
                									'$in' => $arrMadeupId
                								)
                						)
                				),
                			'status' => 1
                		),
                	'arr_field' => array(
                			'_id', 'sku'
                		)
                ));
                if (!$products->count()) {
                	echo 'empty';
                	die;
                }
                $arrSKU = $arrId = array();
                foreach ($products as $product) {
                	$arrSKU[] = $product['sku'];
            		$arrId[] = $product['_id'];
                }
                $arr_where = array();
				$arr_where['status'] = array(
    										'$nin' => array(
    												'Completed',
    												'Cancelled'
    											)
    										);
				if(isset($arr_post['company']) && !empty($arr_post['company']))
					$arr_where['company_name'] = new MongoRegex('/'.trim($arr_post['company']).'/i');
				if(isset($arr_post['contact']) && !empty($arr_post['contact']))
					$arr_where['contact_name'] = new MongoRegex('/'.trim($arr_post['contact']).'/i');
				if(isset($arr_post['job_no']) && $arr_post['job_no']!='')
					$arr_where['job_number'] = $arr_post['job_no'];
				//tim chinh xac ngay
				if(isset($arr_post['date_equals']) && !empty($arr_post['date_equals'])){
					$arr_where['salesorder_date']['$gte'] = new MongoDate($this->Common->strtotime($arr_post['date_equals'] . '00:00:00'));
					$arr_where['salesorder_date']['$lt'] = new MongoDate($this->Common->strtotime($arr_post['date_equals'] . '23:59:59'));
				} else { //ngay nam trong khoang
					//neu chi nhap date from
					if(isset($arr_post['date_from']) && !empty($arr_post['date_from'])){
						$arr_where['salesorder_date']['$gte'] = new MongoDate($this->Common->strtotime($arr_post['date_from'] . '00:00:00'));
					}
					//neu chi nhap date to
					if( isset($arr_post['date_to']) && !empty($arr_post['date_to']) ){
						$arr_where['salesorder_date']['$lte'] = new MongoDate($this->Common->strtotime($arr_post['date_to'] . '23:59:59'));
					}
				}
				if(isset($arr_post['our_rep']) && !empty($arr_post['our_rep']))
					$arr_where['our_rep'] = new MongoRegex('/'.$arr_post['our_rep'].'/i');
				if(isset($arr_post['our_csr']) && !empty($arr_post['our_csr']))
					$arr_where['our_csr'] = new MongoRegex('/'.$arr_post['our_csr'].'/i');
				$arr_where['products']['$elemMatch'] = array(
						'deleted' 		=> false,
						'option_for' 	=> array(
								'$exists' => false
							),
						'products_id'			=> array(
								'$in' => $arrId
							)
					);
                $orders = $this->opm->select_all(array(
                		'arr_where' => $arr_where,
	                	'arr_field' => array(
	                			'code', 'company_name', 'asset_status', 'salesorder_date', 'payment_due_date'
	                		),
	                	'arr_order' => array('code' => 1)
                	));
               	if (!$orders->count()) {
               		echo 'empty';
               		die;
               	}
                if($this->request->is('ajax')) {
					die;
				}
               	$i = 0;
               	$html = '';
                foreach ($orders as $order) {
                	$order = array_merge(array('code' => '', 'company_name' => '', 'asset_status' => '', 'salesorder_date' => '', 'payment_due_date' => ''), $order);
            		$html .= '
            	          <tr class="content_asset no_break bg_' . ($i % 2 == 0 ? '1' : '2') . '">
            	             <td><a href="'. URL .'/salesorders/entry/'. $order['_id'] .'" target="_blank">' . $order['code'] . '</a></td>
            	             <td>' . $order['company_name'] . '</td>
            	             <td>' . $order['asset_status'] . '</td>
            	             <td class="center_text">' . $this->opm->format_date($order['salesorder_date']->sec) . '</td>
            	             <td class="center_text">' . $this->opm->format_date($order['payment_due_date']->sec) . '</td>
            	          </tr>';
            	    $i++;
                }

				$arr_data = array(
					'title' => array('#' => 'text-align: left; width: 10%', 'Company Name'=> 'text-align: left;', 'Status'=> 'text-align: left; width: 15%', 'Order date' => 'width: 15%', 'Payment due date' => 'width: 15%'),
					'content' 	=> $html,
					'report_heading' => implode(', ', $arrFoundSKU),
					'report_name'		=> 'Same Material SKU',
					'report_file_name'	=> 'Same_Material_SKU_'.md5(time())
				);
	            Cache::write('same_material_report', $arr_data);
            }
	    }
        $this->render_pdf($arr_data);
    }

    public function check_generate_docket()
    {
    	$id = new MongoId($this->get_id());
    	$arr_return = array('error' => 1);
    	$order = $this->opm->select_one(array('_id' => $id), array('delivery_method', 'mail_send_to','contact_name', 'heading', 'customer_po_no', 'delivery_method', 'payment_due_date', 'salesorder_date'));
    	if ($order['delivery_method'] == 'Call for Pick Up' && (!isset($order['mail_send_to']) || !in_array($order['mail_send_to'], array('customer', 'our_csr')))) {
    		$arr_return['message'] = 'You must choose customer or our CSR to send email.';
    	} else if (!isset($order['contact_name']) || empty($order['contact_name'])) {
    		$arr_return['message'] = 'Contact cannot be empty.';
    	} else if (!isset($order['heading']) || empty($order['heading'])) {
    		$arr_return['message'] = 'Heading cannot be empty.';
    	} else if (!isset($order['customer_po_no']) || empty($order['customer_po_no'])) {
    		$arr_return['message'] = 'Customer PO cannot be empty.';
    	} else if (!isset($order['delivery_method']) || empty($order['delivery_method'])) {
    		$arr_return['message'] = 'Delivery Method cannot be empty.';
    	} else {
    		$this->selectModel('Stuffs');
			$production_time = $this->Stuffs->select_one(array('value'=> 'Production Time'));
			$production_day = isset($production_time['day']) ? $production_time['day'] : 0;
			if (strtotime(date('d-m-Y 10:00:00', $order['payment_due_date']->sec)) - strtotime(date('d-m-Y 10:00:00', $order['salesorder_date']->sec)) < $production_day * DAY) {
				$arr_return['confirm'] = '<p style="height: 54px;">The Production Time requested is less then Regular Production Turn Around, Rush Charges is recommend to applied to Sales Order.<br />Do you want to continue?<p>';
			} else {
    			$arr_return = array('error' => 0);
			}
    	}
    	echo json_encode($arr_return);
    	die;

    }

    public function productions()
    {
    	$order = $this->opm->select_one(array('_id' => new MongoId($this->get_id())),
    							array('products', 'productions'));
    	if (!isset($order['productions']) || empty($order['productions'])) {
    		$order = $this->generate_productions($order);

    	}
		$this->set('subdatas', array('productions' => $order['productions']));
    }

    public function generate_productions($order = array())
    {
    	$isReturn = true;
    	if (empty($order)) {
    		$order = $this->opm->select_one(array('_id' => new MongoId($this->get_id())),
    								array('products', 'productions'));
    		$isReturn = false;
    	}
		$productions = array();
		$products = array();
		$convert = new unit_convertor;
		if (isset($order['products']) && is_array($order['products'])) {
			$this->selectModel('Product');
			foreach ($order['products'] as $key => $product) {
				if (isset($product['deleted']) && $product['deleted']) continue;
				if (isset($product['option_for']) && is_numeric($product['option_for'])) continue;
				if (!isset($product['sell_by']) || $product['sell_by'] != 'area') continue;
				$id = (string)$product['products_id'];
				$materials = array(
											'name'	=> $product['sizew'].' '.$product['sizew_unit'].' x '.$product['sizeh'].' '.$product['sizeh_unit'],
											'width' => $convert->unit_convertor($product['sizew'], $product['sizew_unit'], 'in', 5),
											'height' => $convert->unit_convertor($product['sizeh'], $product['sizeh_unit'], 'in', 5),
											'quantity' => $product['quantity']
											);
				if (!isset($products[$id])) {
					$i = 1;
					$product['materials'] = array();
					$product['vendor_stock'] = '';
					$p = $this->Product->select_one(array('_id' => $product['products_id']), array('madeup'));
					if (isset($p['madeup']) && is_array($p['madeup'])) {
						$arrMadeupId = array();
						foreach ($p['madeup'] as $madeup) {
							if (isset($madeup['deleted']) && $madeup['deleted']) continue;
							if (!isset($madeup['product_id']) || !is_object($madeup['product_id'])) continue;
							$arrMadeupId[] = $madeup['product_id'];
						}
						if (!empty($arrMadeupId)) {
							$p = $this->Product->select_one(array(
														'_id' => array(
																'$in' => $arrMadeupId
															),
														'product_type' => 'Vendor Stock'
													), array('name'));
							if (isset($p['name'])) {
								$product['vendor_stock'] = $p['name'];
							}
						}
					}
					$products[$id] = $product;
				} else {
					$i++;
					$products[$id]['adj_qty'] += $product['adj_qty'];
				}
				$materials['id'] = $i;
				array_push($products[$id]['materials'], $materials);
			}
		}

		foreach($products as $product) {
			$vendor_stock = '';
			$productions[] = array(
					'products_id' => $product['products_id'],
					'products_name' => $product['products_name'],
					'vendor_stock' => $product['vendor_stock'],
					'code' => $product['code'],
					'sku' => $product['sku'],
					'total_oum'	=> $product['adj_qty'],
					'materials'	=> $product['materials'],
					'material_width' => 0,
					'material_length' => 0,
					'material_needed'	=> 0
				);
		}

		$order['productions'] = $productions;

		$this->opm->collection->update(array(
										'_id' => $order['_id']
									), array(
										'$set' => array(
											'productions' => $order['productions']
											)
									));

		if (!$isReturn) {
			echo 'ok';
			die;
		}
		return $order;

    }

    public function productions_auto_save()
    {
    	if (!$this->request->is('ajax')) {
    		$this->redirect('/');
    		die;
    	}
    	if (!isset($_POST['key']) || !isset($_POST['name']) || !isset($_POST['value'])) {
    		echo json_encode(array('status' => 'error', 'message' => 'Missing field'));
    		die;
    	}
    	$order = $this->opm->select_one(array('_id' => new MongoId($this->get_id())),
    								array('productions'));
    	if (!isset($order['productions']) && !isset($order['productions'][$_POST['key']])) {
    		echo json_encode(array('status' => 'error', 'message' => 'Production does not exist.'));
    		die;
    	}
    	$production = $order['productions'][$_POST['key']];
    	$production[ $_POST['name'] ] = (float)str_replace(',', '', $_POST['value']);

    	$production['cutting_policy'] = '';
    	if (!$production['material_width'] || !$production['material_length']) {
    		$production['material_needed'] = 0;
    	} else {
    		$materials = $production['materials'];
    		if (isset($production['bleed']) && $production['bleed']) {
    			foreach ($materials as $key => $material) {
    				$materials[$key]['width'] += $production['bleed'];
    				$materials[$key]['height'] += $production['bleed'];
    			}
    		}
	        cutting($production['material_width'], $production['material_length'], $materials, $skyArray, $bestPolicy, $leastSheets, $sheets, $gaps);

	    	$production['material_needed'] = $leastSheets;
    		$production['cutting_policy'] = $skyArray[$bestPolicy]['policy'];
    	}

    	$order['productions'][$_POST['key']] = $production;
    	$this->opm->collection->update(array(
										'_id' => $order['_id']
									), array(
										'$set' => array(
											'productions' => $order['productions']
											)
									));

    	echo json_encode(array('status' => 'ok', 'material_needed' => $this->opm->format_currency($production['material_needed'], 0), 'cutting_policy' => $production['cutting_policy']));
    	die;

    }

    public function online_wholesale_order_report()
    {
    	$arrPDF = array ();
    	if(!isset($_GET['print_pdf'])){
    	    $arr_where = array();
    	    $orders = $this->opm->select_all(array(
    	                               'arr_where'  =>  array (
    	                               		'status' => array (
    	                               			'$nin' => array (
    	                               				'Completed',
    	                               				'Cancelled'
    	                               				)
    	                               			),
    	                               		'$or'=> array(
    	                               			array(
    	                               					'asset_status' => array(
    	                               						'$nin' => array (
					    	                               				'Completed',
					    	                               				'06. Fulfillment - DONE'
					    	                               				)
    	                               					),
    	                               					'heading' => array(
    	                               						'$ne' => 'Create from POS'
			    	                               		),
			    	                               		'create_from' => array(
			    	                               			'$ne' => 'Create from POS'
			    	                               		),
    	                               				),
    	                               			array(
    	                               					'create_from' => 'Create from POS',
    	                               					'complete' => 1
    	                               				)
    	                               		)
    	                               		
    	                               	),
    	                               'arr_field'  => array ('contact_id', 'products', 'code'),
    	                               'arr_order'	=> array ('code' => 1)
    	                               ));
    	   	$this->selectModel('Product');
    	   	$wholeSaleProducts = $this->Product->select_all(array(
    	   			'arr_where' => array (
    	   				'wholesale' => 1
    	   				),
    	   			'arr_field' => array ('_id', 'name'),
    	   			'arr_order' => array ('name' => 1)
    	   		));
    	   	$arrProducts = $sales = [];
    	   	foreach ($wholeSaleProducts as $product) {
    	   		$arrProducts[ (string)$product['_id'] ] = $product['_id'];
    	   		$sales[ (string)$product['_id'] ] = 0;
    	   	}
    	   	$arrData = [];
    	   	$html = '';
    	   	if (!empty($arrProducts)) {
    	   		$arrdata = [];
    	   		foreach ($orders as $order) {
    	   			$order = array_merge(array('contact_id' => '', 'products' => array()), $order);
    	   			if (empty($order['contact_id'])) {
    	   				continue;
    	   			}
    	   			if (isset($arrData[(string)$order['contact_id']]['saleProducts'])) {
    	   				$saleProducts = $arrData[(string)$order['contact_id']]['saleProducts'];
    	   			} else {
    	   				$saleProducts = $sales;
    	   			}
    	   			$contactId = (string)$order['contact_id'];
    	   			$products = array_filter($order['products'], function ($array) use ($arrProducts) {
    	   				return isset($array['deleted']) && !$array['deleted'] && in_array($array['products_id'], $arrProducts);
    	   			});
    	   			if (empty($products)) {
    	   				continue;
    	   			}
    	   			foreach ($products as $product) {
    	   				if (isset($saleProducts[ (string)$product['products_id'] ])) {
    	   					$saleProducts[ (string)$product['products_id'] ] += $product['quantity'];
    	   				}
    	   			}
    	   			if (isset($arrData[(string)$order['contact_id']]['orders'])) {
    	   				$arrOrders = $arrData[(string)$order['contact_id']]['orders'];
    	   			} else {
    	   				$arrOrders = [];
    	   			}
    	   			$arrOrders[(string)$order['_id']] = array (
    	   					'_id' => $order['_id'],
    	   					'code'=> $order['code']
    	   				);
    	   			$arrData[(string)$order['contact_id']] = array (
    	   					'contact_id' 	=> $order['contact_id'],
    	   					'saleProducts' 	=> $saleProducts,
    	   					'orders'		=> $arrOrders
    	   				);
    	   		}
    	   		$i = 0;
    	        $this->selectModel('Contact');
    	   		foreach ($arrData as $data) {
    	   			$contact = $this->Contact->select_one(array ('_id' => $data['contact_id']), array('company_id', 'company', 'first_name', 'last_name'));
    	   			$contact = array_merge(array('company_id' => '', 'company' => '', 'first_name' => '', 'last_name' => ''), $contact);
    	   			$html .= '<tr class="'. ($i%2==0 ? 'bg_2' : 'bg_1') .'">
    	   						<td><a href="'. URL .'/contacts/entry/'. $contact['_id'] .'" target="_blank">'. ( $contact['first_name'] . ' ' . $contact['last_name'] ) .'</a></td>
    	   						<td><a href="'.( is_object($contact['company_id']) ? URL .'/companies/entry/'. $contact['company_id'] . '" target="_blank' : '#' ).'">'. $contact['company'] .'</a></td>';
    	   			foreach ($data['saleProducts'] as $productId => $quantity) {
    	   				$sales[$productId] += $quantity;
    	   				$html .= '<td class="right_text">'. $quantity .'</td>';
    	   			}

    	   			$orderCode = array ();
    	   			foreach ($data['orders'] as $order) {
    	   				$orderCode[] = '<a href="'. URL . '/salesorders/entry/'.$order['_id'] .'" target="_blank">'. $order['code'] .'</a>';
    	   			}
    	   			$html .= '<td>'. implode(', ', $orderCode) .'</td>
    	   					</tr>';
    	   			$i++;
    	   		}
    	   		$html .= '<tr class="last">
                                <td class="bold_text right_none">'.$i.' record(s) listed.</td>
                                <td class="bold_text right_text right_none">Totals:</td>';
                foreach ($sales as $quantity) {
	   				$html .= 	'<td class="bold_text right_text right_none">'. $quantity .'</td>';
	   			}
                $html .= 		'<td class="bold_text right_text right_none"></td>
                            </tr>';
    	   		$arrPDF = array (
    	   			'title' => array(
	   						'Customer'	=>	'text-align: left;',
	   						'Company'	=>	'text-align: left;',
	   					),
    	   			'content' => $html,
    	   			'report_name' => 'Outstanding Wholesale Orders',
    	   			'report_file_name' => 'outstanding_whole_order'
    	   		);
    	   		foreach ($wholeSaleProducts as $product) {
    	   			$arrPDF['title'][$product['name']] = 'text-align: right;';
    	   		}
    	   		$arrPDF['title']['SO Number'] = 'text-align: left;';
    	   		Cache::write('outstanding_whole_order', $arrPDF);
    	   	}
    	} else {
    	    $arrPDF = Cache::read('outstanding_whole_order');
    	    Cache::delete('outstanding_whole_order');
    	}
    	$this->render_pdf($arrPDF);
    }

    function fix_order_no($id=0){
    	$orders = $this->opm->select_all(array(
	    	                               'arr_where'  =>  array (
	    	                               		'code' => '16-05-1000'
	    	                               					),
	    	                               'arr_field'  => array ('_id','code'),
	    	                               'arr_order'	=> array ('_id' => 1)
    	                               ));
    	if($id>0)
    		$begin = $id;
    	else
    		$begin = 1000;
    	foreach ($orders as $key => $value) {
			echo $begin.'<br>';
    		$this->opm->collection->update(array('_id' => $value['_id']), array('$set'=> array('code' => '16-05-'.$begin)), array("multiple" => true));
    		$begin++;
    	}
    	die;
    }
    function draft_fix(){
    	$this->opm->collection->update(array('_id' => new MongoId('577493d3124dcaaf6903e7f3')), array('$set'=> array('code' => '16-06-1921')), array("multiple" => true));
    }
    function resort_number_order(){
    	$orders = $this->opm->select_all(array(
	    	                               'arr_where'  =>  array (
	    	                               			"code"=> new MongoRegex("/16-06/i")
	    	                               					),
	    	                               'arr_field'  => array ('_id','code','date_modified'),
	    	                               'arr_order'	=> array ('_id'=>1)
    	                               ));
    	$no = 1;
    	foreach ($orders as $key => $value) {
    		$new_code = '16-06-'.str_pad($no, 4, "0", STR_PAD_LEFT);
    		$this->opm->collection->update(array('_id' => $value['_id']), array('$set'=> array('old_code' =>$value['code'],'code' => $new_code)), array("multiple" => true));
    		$no++;
    	}
    	die;
    }
    function fix_online_station(){
    	$orders = $this->opm->select_all(array(
	    	                               'arr_where'  =>  array (
	    	                               			"create_from"=> "Online",
	    	                               			"status"=>"In progress"
	    	                               					),
	    	                               'arr_field'  => array ('_id','code','date_modified','status'),
	    	                               'arr_order'	=> array ('_id'=>1)
    	                               ));
    	$no = 1;
    	foreach ($orders as $key => $value) {
    		$this->opm->collection->update(array('_id' => $value['_id']), array('$set'=> array('status' =>'In production','status_id'=>'In production','asset_status'=>'In production')), array("multiple" => true));
    		echo  $value['code'].'<br>';
    		$no++;
    	}
    	die;
    }
    function fix_time_delivery(){
    	$orders = $this->opm->select_all(array(
	    	                               'arr_where'  =>  array (
	    	                               			"create_from"=> "Online"
	    	                               					),
	    	                               'arr_field'  => array ('_id','code','date_modified','time_delivery'),
	    	                               'arr_order'	=> array ('_id'=>1)
    	                               ));
    	$no = 1;
    	foreach ($orders as $key => $value) {
    		if(isset($value['time_delivery']))
    			continue;
    		$this->opm->collection->update(array('_id' => $value['_id']), array('$set'=> array('time_delivery' =>$value['date_modified'])), array("multiple" => true));
    		echo  $value['code'].'<br>';
    		$no++;
    	}
    	die;
    }
    function fix_order_no_many_clone($max=1080){
    	$new_order = $this->opm->select_one(array("code"=>new MongoRegex("/16-06/i")),array('_id','code'),array('_id'=>-1));
    	if(isset($new_order['code'])){
    		$tmp = explode("-",$new_order['code']);
    		$max = (int)(end($tmp));
    	}
    	$arr_save = $arr_code = $arr_data_group = $arr_data = array();
    	//tao array code bi trung
    	for($m=1000;$m<$max;$m++){
    		$arr_code[] = '16-06-'.$m;
    	}
    	//tim 1 lan tat ca cac item bi trung va sap theo ngay thang
    	$orders = $this->opm->select_all(array(
	    	                               'arr_where'  =>  array (
	    	                               			"code"=> array('$in'=>$arr_code)
	    	                               					),
	    	                               'arr_field'  => array ('_id','code','date_modified'),
	    	                               'arr_order'	=> array ('_id'=>1)
    	                               ));
    	//sap lai theo nhom code
    	foreach ($orders as $key => $value){
    		$arr_data_group[$value['code']][(string)$value['_id']] = $value;  		
    	}
    	$n=1; $tmp_code = '';
    	//lap va tao ra 1 array chung cac item can chinh lai code
    	foreach ($arr_data_group as $ck => $cval) {
    		foreach ($cval as $key => $value){
    			if($tmp_code!='' && $tmp_code = $value['code']){
    				$value['new_code'] = '16-06-'.$max;
    				$arr_data[$key] = $value;
    				$n++; $max++;
    			}
    			$tmp_code = $value['code'];
    		}
    	}
    	foreach ($arr_data as $key => $value) {
    		echo $value['code'].'====>>'.$value['new_code'].'<br>';
    		$this->opm->collection->update(array('_id' => $value['_id']), array('$set'=> array('old_code' =>$value['code'],'code' => $value['new_code'])), array("multiple" => true));
    	}
    	//update cai cuoi len vi tri moi nhat, vi no co _id moi nhat
    	$this->opm->collection->update(array('_id' => $new_order['_id']), array('$set'=> array('old_code' =>$new_order['code'],'code' => '16-06-'.$max)), array("multiple" => true));
    	die;
    }

    function paid_payment_on_account(){
    	$orders = $this->opm->select_all(array(
					                           'arr_where'  =>  array (
					                           						'$or'=>array(
												                        array('paid_by'=>'On Account'),
												                        array('paid_by'=>'Multipay')
												                    ),
					                           						'status_id'=>array('$ne'=>'Cancelled')
					                           					),
					                           'arr_field'  => array ('_id','code','had_paid'),
					                           'arr_order'	=> array ('_id' => 1)
					                       ));
    	foreach ($orders as $key => $value) {
			echo $begin.$value['code'].'<br>';
			if(isset($value['had_paid']) && $value['had_paid']==1)
				continue;
    		$this->opm->collection->update(array('_id' => $value['_id']), array('$set'=> array('had_paid' => 1)), array("multiple" => true));
    		$begin++;
    	}
    	die;
    }

}
