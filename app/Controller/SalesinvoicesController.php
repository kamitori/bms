<?php
// Attach lib cal_price
App::import('Vendor','cal_price/cal_price');

App::uses('AppController', 'Controller');
class SalesinvoicesController extends AppController {

	var $name = 'Salesinvoices';
	public $helpers = array();
	public $opm; //Option Module
	public $cal_price; //Option cal_price
	var $modelName = 'Salesinvoice';

	public function beforeFilter(){
		parent::beforeFilter();
		$this->set_module_before_filter('Salesinvoice');
        $this->set('title_entry', 'Sales Invoice');

	}

	//Các điều kiện mở/khóa field trong entry
	public function check_lock(){
		$id = $this->get_id();
		if($id!=''){
            $arr_tmp = $this->opm->select_one(array('_id' => new MongoId($id)),array('invoice_status'));
			if(isset($arr_tmp['invoice_status'])&&$arr_tmp['invoice_status']!='In Progress')
				return true;

		}else
			return false;
	}

	/*
	 * Hàm dùng để điều chỉnh setting(giao diện)/phân quyền
	 */
	public function rebuild_setting($arr_setting=array()){
		parent::rebuild_setting($arr_setting=array());
		$params = isset($this->params->params['pass'][0]) ? $this->params->params['pass'][0] : null;
		$valid = false;
		if($this->params->params['action'] == 'entry' || $valid = in_array($params,array('line_entry','text_entry'))){
			if($valid){
				if(IS_LOCAL){
					$this->opm->arr_settings['relationship']['line_entry']['block']['products']['field']['currency']['type'] = 'select';
					$option_currency = $this->Setting->select_option_vl(array('setting_value'=> 'currency_type'));
					$this->set('option_currency', $option_currency);
				}
			}
			if(!$this->check_permission('salesinvoices_@_entry_@_edit')){
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
			$arr_setting = $this->opm->arr_settings;
			$iditem = $this->get_id();
			$this->opm->arr_settings = $arr_setting;
			$query = $this->opm->select_one(array('_id'=> new MongoId($iditem)),array('job_id','invoice_status'));
            $continue = true;
            if($continue && isset($query['job_id'])&&is_object($query['job_id'])){
                $this->selectModel('Job');
                $job = $this->Job->select_one(array('_id'=> new MongoId($query['job_id'])),array('status','work_end'));
            	$_id = $this->checkClosingMonth($job );
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
            } else if( in_array($query['invoice_status'], ['Paid', 'Invoiced']) ) {
            	 if($valid){
                    $this->opm->set_lock_option('line_entry', 'products');
                    $this->opm->set_lock_option('text_entry', 'products');
                    $this->set('lock_product', true);
                } else {
                    $this->opm->set_lock(array('invoice_status'), 'out');
                    $this->set('address_lock', '1');
                }
            }
		}
	}

	function ajax_save(){
		if( isset($_POST['field'])){
			if($_POST['field'] == 'code'){
				if($_POST['func']=='update'&&!$this->check_permission($this->name.'_@_entry_@_edit')){
					echo 'You do not have permission on this action.';
					die;
				}
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
			} else if($_POST['field'] == 'invoice_status'){
				$query = $this->opm->select_one(array('_id'=> new MongoId($this->get_id())),array('company_id','sum_amount','sum_sub_total','taxval','job_id','invoice_status', 'shipping_cost'));
				$this->opm->update($_POST['ids'], 'invoice_status_old', $query['invoice_status']);
				if(is_object($query['job_id'])){
					$this->selectModel('Job');
					if($_POST['value'] == 'Paid'){
						if($this->opm->count(array('job_id'=> $query['job_id'])) == $this->opm->count(array('job_id'=> $query['job_id'],'invoice_status' => 'Paid')) +1 ){
							$this->Job->save(array('_id'=>$query['job_id'],'status'=>'Paid','status_id'=>'Paid'));
						} else{
							$this->Session->setFlash('Sales Invoices are not all paid yet. So that Job&#039;s status will not be changed.','default',array('class'=>'flash_message'));
						}
					} else if( in_array($_POST['value'], array('Invoiced', 'Credit')) ){
						$this->selectModel('Salesorder');
						$salesorder = $this->Salesorder->count(array('job_id'=> new MongoId($query['job_id'])));
						$salesorder_completed = $this->Salesorder->count(array('job_id'=> new MongoId($query['job_id']),'status'=>'Completed'));
						if($salesorder > $salesorder_completed){
							$this->Session->setFlash('Sales Orders are not Completed all yet. So that Job&#039;s status will not be changed.','default',array('class'=>'flash_message'));
						} else {
							$this->selectModel('Salesinvoice');
							$salesinvoice = $this->Salesinvoice->count(array('job_id'=> new MongoId($query['job_id'])));
							$salesinvoice_invoiced = $this->Salesinvoice->count(array('job_id'=> new MongoId($query['job_id']),'invoice_status'=> array('$in' => array('Invoiced', 'Credit')) ));
							if($salesinvoice > ($salesinvoice_invoiced + 1)){
								$this->Session->setFlash('Sales Invoices are not Invoiced all yet. So that Job&#039;s status will not be changed.','default',array('class'=>'flash_message'));
							} else {
								$salesorder_sum = $this->Salesorder->sum('sum_amount','tb_salesorder',array('job_id'=>new MongoId($query['job_id']),'deleted'=>false));
								$salesinvoice_sum = $this->Salesinvoice->sum('sum_amount','tb_salesinvoice',array('job_id'=>new MongoId($query['job_id']),'deleted'=>false));
								if(abs(($salesorder_sum - $salesinvoice_sum) / ($salesinvoice_sum==0? 1 :$salesinvoice_sum) ) > 0.001) {
									$this->Session->setFlash('Sales Invoices total: '.$this->opm->format_currency($salesinvoice_sum).'<br />Sales Orders total: '.$this->opm->format_currency($salesorder_sum).'<br/>So that Job&#039;s status will not be changed.','default',array('class'=>'flash_message'));
								} else {
									$this->Job->save(array('_id'=>$query['job_id'],'status'=>'Completed'));
								}
							}
						}
					}
				}
				if($_POST['value'] == 'Invoiced')
					$this->update_balace_credit_salesaccount($query);
				else
					$this->update_balace_credit_salesaccount($query,'minus');
			} else if($_POST['field'] == 'invoice_type') {
				$query = $this->opm->select_one(array('_id'=> new MongoId($this->get_id())),array('products', 'invoice_status', 'invoice_status_old'));
				if( $_POST['value'] == 'Credit' ) {
					$query['invoice_type'] = 'Credit';
					$arr_sum = $this->new_cal_sum($query['products']);
           	 		$query = array_merge($query, $arr_sum);
					$this->opm->save($query);
					echo 'refresh';
					die;
				}
			}
		}
		parent::ajax_save();
	}

	public function create_salesinvoice_from_salesorder($salesorder_id, $combine_from_job=''){
		if(!$this->check_permission('salesinvoices_@_entry_@_add'))
			$this->error_auth();
		$this->selectModel('Salesorder');
		$arr_salesorder=array();
		if($salesorder_id!=''){
			$arr_salesorder = $this->Salesorder->select_one(array('_id' => new MongoId($salesorder_id)));
			$this->selectModel('Salesinvoice');
            $si = $arr_salesorder;
            $si['code'] = $this->opm->get_auto_code('code');
            $si['invoice_date'] = new MongoDate();
            $si['payment_due_date'] =  new MongoDate($si['invoice_date']->sec + (isset($si['payment_terms']) ? (int)$si['payment_terms'] : 0)*DAY);
            $si['invoice_status'] = 'In Progress';
            $si['invoice_type'] = 'Invoice';
            $si['salesorder_id'] = new MongoId($salesorder_id);
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
            
            //remove product option, group by product_id
            if($combine_from_job == 'combine_from_job') {
            	foreach ($si['products'] as $key => $value) {
	            	if(isset($value['option_for'])) {
	            		unset($si['products'][$key]);
	            	}
	            }
	            $si['products'] = array_values($si['products']);
	            $arr_products_new = array();
	            foreach ($si['products'] as $key => $value) {
	            	$p_exist = $this->get_product_existed_in_array($arr_products_new, $value);
	            	if($p_exist) {
	            		$arr_products_new[$p_exist['key']]['quantity'] += $value['quantity'];
	            		$arr_products_new[$p_exist['key']]['adj_qty'] += $value['adj_qty'];
	            		$arr_products_new[$p_exist['key']]['sub_total'] += $value['sub_total'];
	            		$arr_products_new[$p_exist['key']]['amount'] += $value['amount'];
	            		$arr_products_new[$p_exist['key']]['tax'] += $value['tax'];
	            		$unit_price = $arr_products_new[$p_exist['key']]['sub_total'] / $arr_products_new[$p_exist['key']]['quantity'];
	            		$arr_products_new[$p_exist['key']]['unit_price'] = $unit_price;
	            		$arr_products_new[$p_exist['key']]['sell_price'] = $unit_price;
	            		$arr_products_new[$p_exist['key']]['custom_unit_price'] = $unit_price;
	            	}
	            	else {
	            		$arr_products_new[] = $value;	
	            	}
	            	
	            }
	            // pr($si);
	            // pr($arr_products_new);exit;
	            $si['products'] = $arr_products_new;
            }	

            if($this->opm->save($si)){
                $id = $this->Salesinvoice->mongo_id_after_save;
                $arr_salesorder['salesinvoice_id'] = new MongoId($id);
                $arr_salesorder['salesinvoice_code'] = $si['code'];
                if(is_array($arr_salesorder['products'])){
					foreach($arr_salesorder['products'] as $key=>$value){
						if($value['deleted']) continue;
						$arr_salesorder['products'][$key]['invoiced']=(int)$value['quantity'];
						$arr_salesorder['products'][$key]['balance_invoiced']=0;

					}
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
                $this->Salesorder->save($arr_salesorder);
                $this->redirect('/salesinvoices/entry/'.$id);
            }
		}
	}
	function get_product_existed_in_array($arr_product, $product)
	{
		foreach ($arr_product as $key => $value) {
			// $id = $value['products_id']->id;
			// $product_id = $product['products_id']->id;
			if($value['products_id'] == $product['products_id']) {
				return ['key'=>$key];
			}
		}
		return false;
	}
	public function entry(){
		$mod_lock = '0';
		ini_set('memory_limit', '-1');
		if($this->check_lock()){
			$this->opm->set_lock(array('invoice_status'),'out');
			$mod_lock = '1';
			$this->set('address_lock','1');
			$this->opm->set_lock_option('line_entry','products');
			$this->opm->set_lock_option('text_entry','products');

		}

		$arr_set = $this->opm->arr_settings;
		// Get value id
		$iditem = $this->get_id();
		if($iditem=='')
			$iditem = $this->get_last_id();

		$this->set('iditem',$iditem);
		//Load record by id
		$arr_tmp = array();
		if($iditem!=''){
			$arr_tmp = $this->opm->select_one(array('_id' => new MongoId($iditem)),array('code','date_modified','created_by','modified_by','invoice_type','company_name','company_id','company_id','contact_name','contact_id','phone','email','invoice_date','our_rep','our_rep_id','our_csr','our_csr_id','invoice_address','shipping_address','invoice_status','payment_terms','paid_date','payment_due_date','tax','taxval','customer_po_no','heading','name','job_name','job_number','job_id','salesorder_name','salesorder_number','salesorder_id','salesorder_name','sum_amount','sum_sub_total','sum_tax','is_part_invoice','invoice_status_old','modified_by_name', 'quotation_id', 'quotation_code'));
			if(!isset($arr_tmp['_id'])){
                $query = $this->opm->select_one(array('deleted' => 'no_search', '_id' => new MongoId($iditem)),array('deleted','date_modified','modified_by','code'));
                if( $query['deleted'] ){
                    $this->selectModel('Contact');
                    $contact = $this->Contact->select_one(array('_id'=> $query['modified_by']),array('full_name'));
                    if(!isset($contact['full_name']))
                        $contact['full_name'] = 'System Admin';
                    echo "This Invoice #{$query['code']} has been deleted by {$contact['full_name']} at ".date('d M, Y h:i:s',$query['date_modified']->sec).'. Click this <a href="'.URL.'/salesinvoices/lasts'.'" style="color: blue;">link</a> to go to the lastest Invoice.';
                    $this->autoRender = false;
                    return;
                }
            }
			if(isset($arr_tmp['shipping_id']) && is_object($arr_tmp['shipping_id']))
				$this->set('shipping_id',$arr_tmp['shipping_id']);
			foreach($arr_set['field'] as $ks => $vls){
				foreach($vls as $field => $values){
					if(isset($arr_tmp[$field])){
						$arr_set['field'][$ks][$field]['default'] = $arr_tmp[$field];
						if(in_array($field,$arr_set['title_field']))
							$item_title[$field] = $arr_tmp[$field];

						if(preg_match("/_date$/",$field) && is_object($arr_tmp[$field]))
							$arr_set['field'][$ks][$field]['default'] = date('m/d/Y',$arr_tmp[$field]->sec);
						//chế độ lock, hiện name của các relationship custom
						else if(($field=='company_name' || $field=='contact_name') && $mod_lock=='1')
							$arr_set['field'][$ks][$field]['default'] = $arr_tmp[$field];

						/*else if($this->opm->check_field_link($ks,$field)){
							$field_id = $arr_set['field'][$ks][$field]['id'];
							if(!isset($arr_set['field'][$ks][$field]['syncname']))
								$arr_set['field'][$ks][$field]['syncname'] = 'name';
							$arr_set['field'][$ks][$field]['default'] = $this->get_name($this->ModuleName($arr_set['field'][$ks][$field]['cls']),$arr_tmp[$field_id],$arr_set['field'][$ks][$field]['syncname']);

						}else if($field=='company_name' && isset($arr_tmp['company_id']) && $arr_tmp['company_id']!=''){
							$arr_set['field'][$ks][$field]['default'] = $this->get_name('Company',$arr_tmp['company_id']);

						}else if($field=='contact_name' && isset($arr_tmp['contact_id']) && $arr_tmp['contact_id']!=''){
							$arr_set['field'][$ks][$field]['default'] = $this->get_name('Contact',$arr_tmp['contact_id']);
							$item_title[$field] = $this->get_name('Contact',$arr_tmp['contact_id']);
						}*/
					}
				}
			}
			$this->set('invoice_status',(isset($arr_set['field']['panel_4']['invoice_status']['default']) ? $arr_set['field']['panel_4']['invoice_status']['default'] : ''));
			$arr_set['field']['panel_1']['mongo_id']['default'] = $iditem;
			$this->Session->write($this->name.'ViewId',$iditem);

			//BEGIN custom
			if(isset($arr_set['field']['panel_1']['code']['default']))
				$item_title['code'] = $arr_set['field']['panel_1']['code']['default'];
			else
				$item_title['code'] = '1';
			$this->set('item_title',$item_title);

			//custom list tax
			$arr_options_custom['tax'] = '';
			$this->selectModel('Tax');
			$arr_options_custom['tax'] = $this->Tax->tax_select_list();
			$this->set('arr_options_custom',$arr_options_custom);
			//END custom

			//show footer info
			$this->show_footer_info($arr_tmp);


		//add, setup field tự tăng
		}else{
			$this->redirect(URL.'/salesinvoices/add');
		}
		$this->set('query',$arr_tmp);
		$this->set('arr_settings',$arr_set);
		$this->sub_tab_default = 'line_entry';
		$this->sub_tab('',$iditem);
		$this->set_entry_address($arr_tmp,$arr_set);

		parent::entry();
	}
	public function lists(){
		$this->set('_controller',$this);
		$this->selectModel('Salesinvoice');
		$limit = LIST_LIMIT;
		$skip = 0;
		$sort_field = '_id';
		$sort_type = 1;
		if( isset($_POST['sort']) && strlen($_POST['sort']['field']) > 0 ){
			if( $_POST['sort']['type'] == 'desc' ){
				$sort_type = -1;
			}
			$sort_field = $_POST['sort']['field'];
			$this->Session->write('Salesinvoices_lists_search_sort', array($sort_field, $sort_type));

		}elseif( $this->Session->check('Salesinvoices_lists_search_sort') ){
			$session_sort = $this->Session->read('Salesinvoices_lists_search_sort');
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

		// query
		$arr_invoices = $this->Salesinvoice->select_all(array(
			'arr_where' => $cond,
			'arr_order' => $arr_order,
			'arr_field'	=> array('code','company_name','company_id','contact_name','contact_id','invoice_date','heading','salesorder_id','job_number','job_id','sum_sub_total','invoice_status', 'shipping_cost'),
			'limit' => $limit,
			'skip' => $skip
		));
		$this->set('arr_invoices', $arr_invoices);


		$total_page = $total_record = $total_current = 0;
		if( is_object($arr_invoices) ){
			$total_current = $arr_invoices->count(true);
			$total_record = $arr_invoices->count();
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
		$this->set('sum23', $total_record);
	}

	function get_sum_orders(){
		$sort_field = '_id';
		$sort_type = 1;
		if( $this->Session->check('Salesinvoices_lists_search_sort') ){
			$session_sort = $this->Session->read('Salesinvoices_lists_search_sort');
			$sort_field = $session_sort[0];
			$sort_type = $session_sort[1];
		}
		$arr_order = array($sort_field => $sort_type);
		$arr_where = $this->arr_search_where();
		$arr_where = array_merge($arr_where, array( 'invoice_status' => array('$nin' => array('Cancelled'))));
	    $this->selectModel('Salesinvoice');
		$arr_invoices = $this->Salesinvoice->select_all(array(
			'arr_where' => $arr_where,
			'arr_order' => $arr_order,
			'arr_field'	=> array('invoice_status','salesorder_id', 'shipping_cost'),
		));
		$sum = 0;
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
		if($arr_invoices->count()){
			$this->selectModel('Salesorder');
			foreach($arr_invoices as $invoice){
				if(!isset($invoice['invoice_status'])) continue;
				if(!isset($invoice['salesorder_id']) || !is_object($invoice['salesorder_id'])) continue;
				$arr_orders = $this->Salesorder->select_all(array(
							'arr_where' => array(
										'_id' => $invoice['salesorder_id'],
										'status' => array('$nin' => array('Cancelled')),
										),
							'arr_field' => array('company_id','sum_sub_total'),
							'arr_order'	=> array('code' => 1)
					));
				foreach($arr_orders as $order){
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
		            if(in_array($invoice['invoice_status'], array('Credit')) && $order['sum_sub_total'] > 0)
		            	$order['sum_sub_total'] *= -1;
		            $sum += $order['sum_sub_total'];
				}
			}
			if(count($arr_companies) > $count)
	   	 		Cache::write('arr_companies',$arr_companies);
		}
		echo $this->Salesinvoice->format_currency($sum);
		die;
	}

	function arr_associated_data($field = '', $value = '', $valueid = '' , $fieldopt='') {
		$arr_return = array();
		$arr_return[$field]=$value;
		/**
		* Chọn Company
		*/
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
		if($field == 'company_name' && $valueid !=''){
			$arr_return = array(
				'company_name'	=>'',
				'company_id'	=>'',
				'contact_name'	=>'',
				'contact_id'	=>'',
				'our_csr'		=>'',
				'our_csr_id'	=>'',
				'our_rep'		=>'',
				'our_rep_id'	=>'',
				'phone'			=>'',
				'email'			=>'',
				'invoice_address' => array(),
				'shipping_address' => array(),
			);
			//change company
			$arr_return['company_name'] = $value;
			$arr_return['company_id'] = new MongoId($valueid);
            $query = $this->opm->select_one(array('_id' => new MongoId($this->get_id())));

			$this->selectModel('Salesaccount');
			$salesaccount = $this->Salesaccount->select_one(array('company_id' => $arr_return['company_id']));
			$arr_return['payment_terms'] = (isset($salesaccount['payment_terms']) ? $salesaccount['payment_terms'] : 0);
			$arr_return['payment_terms_id'] = (isset($salesaccount['payment_terms_id']) ? $salesaccount['payment_terms_id'] : 0);

			$salesinvoice = $this->opm->select_one(array('_id'=>new MongoId($this->get_id())));
			$arr_return['name'] = $salesinvoice['code'].'-'.$value;
			$arr_return['payment_due_date'] = (int)$arr_return['payment_terms_id']*86400 + (int)$salesinvoice['invoice_date']->sec;
			$arr_return['payment_due_date'] = new MongoDate($arr_return['payment_due_date']);

			//find contact and more from Company
			$this->selectModel('Company');
			$arr_company = $this->Company->select_one(array('_id'=>new MongoId($valueid)));

			$this->selectModel('Contact');
			$arr_contact = $arrtemp = array();
			// is set contact_default_id
			if(isset($arr_company['contact_default_id']) && is_object($arr_company['contact_default_id'])){
				$arr_contact = $this->Contact->select_one(array('_id'=>$arr_company['contact_default_id']));

			// not set contact_default_id
			}else{
				$arr_contact = $this->Contact->select_all(array(
					'arr_where' => array('company_id'=>new MongoId($valueid)),
					'arr_order' => array('_id'=>-1),
				));
				$arrtemp = iterator_to_array($arr_contact);
				if(count($arrtemp)>0){
					$arr_contact = current($arrtemp);
				}else
					$arr_contact = array();
			}
			//change contact
			if(isset($arr_contact['_id']))
			{
				$arr_return['contact_name']=$arr_contact['first_name'].' '.$arr_contact['last_name'];
				$arr_return['contact_id'] = $arr_contact['_id'];
			}
			else
			{
				$arr_return['contact_name']='';
				$arr_return['contact_id'] = '';
			}




			//change our_csr
			if(isset($arr_company['our_csr']) && isset($arr_company['our_csr_id']) && $arr_company['our_csr_id']!=''){
				$arr_return['our_csr_id'] = $arr_company['our_csr_id'];
				$arr_return['our_csr'] = $arr_company['our_csr'];
			}else{
				$arr_return['our_csr_id'] = $this->opm->user_id();
				$arr_return['our_csr'] = $this->opm->user_name();
			}


			//change our_rep
			if(isset($arr_company['our_responsible']) && isset($arr_company['our_responsible_id']) && $arr_company['our_responsible_id']!=''){
				$arr_return['our_rep_id'] = $arr_company['our_responsible_id'];
				$arr_return['our_rep'] = $arr_company['our_responsible'];
			}else{
				$arr_return['our_rep_id'] = $this->opm->user_id();
				$arr_return['our_rep'] = $this->opm->user_name();
			}

			//change our_rep
			if(isset($arr_company['our_rep']) && isset($arr_company['our_rep_id']) && $arr_company['our_rep_id']!=''){
				$arr_return['our_rep_id'] = $arr_company['our_rep_id'];
				$arr_return['our_rep'] = $arr_company['our_rep'];
			}else{
				$arr_return['our_rep_id'] = $this->opm->user_id();
				$arr_return['our_rep'] = $this->opm->user_name();
			}


			//change phone
			if(isset($arr_company['phone']))
				$arr_return['phone'] = $arr_company['phone'];
			else  // neu khong co phone thi lay phone cua contact mac dinh
			{
				if(isset($arr_contact['direct_dial']))
					$arr_return['phone']=$arr_contact['direct_dial'];
				elseif(!isset($arr_contact['direct_dial'])&&isset($arr_contact['mobile']))
					$arr_return['phone']=$arr_contact['mobile'];
				elseif(!isset($arr_contact['direct_dial'])&&!isset($arr_contact['mobile']))
					$arr_return['phone']='';  //bat buoc phai co dong nay khong thi no se lay du lieu cua cty truoc
			}


			if(isset($arr_company['email'])  && $arr_company['email']!='')
				$arr_return['email'] = $arr_company['email'];
			elseif (isset($arr_contact['email']))
				$arr_return['email']=$arr_contact['email'];
			elseif  (!isset($arr_contact['email']))
				$arr_return['email']='';

			if(isset($arr_company['fax']))
				$arr_return['fax'] = $arr_company['fax'];
			elseif (isset($arr_contact['fax']))
				$arr_return['fax']=$arr_contact['fax'];
			elseif  (!isset($arr_contact['fax']))
				$arr_return['fax']='';


			//change address
			$add_default = 0;
			if(isset($arr_company['addresses_default_key']))
				$add_default = $arr_company['addresses_default_key'];
			if(isset($add_default) && isset($arr_company['addresses'][$add_default])){
				foreach($arr_company['addresses'][$add_default] as $ka=>$va){
					if($ka!='deleted')
						$arr_return['invoice_address'][0]['invoice_'.$ka] = $va;
					else
						$arr_return['invoice_address'][0][$ka] = $va;
				}
			}
			//change tax
            if(isset($salesaccount['tax_code_id'])&& $salesaccount['tax_code_id']!=''){
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
			$products = isset($query['products']) ? (array)$query['products'] : array();
			foreach($products as $key => $product) {
				if( !isset($product['deleted']) ) continue;
				if( isset($product['deleted']) && $product['deleted'] ) continue;
				if( isset($product['same_parent']) && $product['same_parent'] ) continue;
                $data = $this->cal_price_line(array('data'=>array('id'=>$key),'fieldchange'=>'quantity'), true, true, $query);
				$query = array_merge($query, $data);
			}
			$arr_return['products'] = $query['products'];
			$arr_return['sum_sub_total'] = $query['sum_sub_total'];
			$arr_return['sum_tax'] = $query['sum_tax'];
			$arr_return['sum_amount'] = $query['sum_amount'];

		/**
		* Chọn Contact
		*/
		}else if($field == 'contact_name' && $valueid !=''){
			$arr_return = array(
				'contact_name'	=>'',
				'contact_id'	=>'',
				'phone'			=>'',
				'email'			=>'',
			);
			//change company
			$arr_return['contact_name'] = $value;
			$arr_return['contact_id'] = new MongoId($valueid);
			//find more from contact
			$this->selectModel('Contact');
			$arr_contact = $this->Contact->select_one(array('_id'=>new MongoId($valueid)));
			//change phone
			if(isset($arr_contact['direct_dial']) && $arr_contact['direct_dial']!='')
				$arr_return['phone'] = $arr_contact['direct_dial'];
			//change email
			if(isset($arr_contact['email']))
				$arr_return['email'] = $arr_contact['email'];
			//nếu company khác hiện có
			if(isset($arr_contact['company_id'])){
				echo '';
			}

		/**
		* Thay đổi Payment terms
		*/
		}else if($field == 'payment_terms' && $this->get_id()!=''){
			$salesinvoice = $this->opm->select_one(array('_id'=>new MongoId($this->get_id())));
			if(isset($salesinvoice['invoice_date'])){
				$arr_return['payment_due_date'] = (int)$value*86400 + (int)$salesinvoice['invoice_date']->sec;
				$arr_return['payment_due_date'] = new MongoDate($arr_return['payment_due_date']);
			}

		/**
		* Thay đổi invoice_date
		*/
		}else if($field == 'invoice_date' && $this->get_id()!=''){
			$salesinvoice = $this->opm->select_one(array('_id'=>new MongoId($this->get_id())));
			if(empty($salesinvoice['payment_terms']))
				$salesinvoice['payment_terms'] = 0;
			$arr_return['payment_due_date'] = (int)$salesinvoice['payment_terms']*86400 + (int)$salesinvoice['invoice_date'];
			$arr_return['payment_due_date'] = new MongoDate($arr_return['payment_due_date']);

		}
		if($field == 'products'){
			if(!isset($value[$valueid]['currency']))
				$value[$valueid]['currency'] = 'cad';
			if(isset($value[$valueid]) && isset($value[$valueid]['products_id']) && is_object($value[$valueid]['products_id']) && $fieldopt!='code' && $fieldopt!='deleted'){
				//change size other


			//giam gia cho product parrent neu la xoa item option
			}else if(isset($value[$valueid]) && isset($value[$valueid]['products_id']) && is_object($value[$valueid]['products_id']) && $fieldopt=='deleted'){
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
			}else if(isset($value[$valueid]) && isset($value[$valueid]['products_id']) && is_object($value[$valueid]['products_id']) && $fieldopt=='code'){
                $query = $this->opm->select_one(array('_id' => new MongoId($this->get_id())),array('products','options','company_id','currency','shipping_cost'));
                if( isset($query['products'][$valueid]['sku']) && preg_match('/^SHP/', $query['products'][$valueid]['sku']) ) {
                    $query['shipping_cost'] -= $query['products'][$valueid]['sub_total'];
                    $save = true;
                }
                if(!isset($query['currency']))
                	$query['currency'] ='cad';
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
						$new_array['currency'] = 'cad';
						$new_array['code'] 			= $vv['code'];
						$new_array['sku'] 			= $vv['sku'];
                        $new_array['products_name'] = $vv['product_name'];
						$new_array['product_name'] = $vv['product_name'];
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

						$cal_price = new cal_price;
						//truyen data vao cal_price de tinh gia
						$cal_price->arr_product_items = $new_array;
						//lay thong tin khach hang de tinh chiec khau/giam gia
						$result = array();
						if(!isset($query['company_id']))
							$query['company_id'] = '';
						if(isset($new_array['products_id']))
							$result = $this->change_sell_price_company($query['company_id'],$new_array['products_id']);
						$tmp_sell_price = 0;
						if(isset($result['sell_price'])) {
	                    	$tmp_sell_price = $result['sell_price'];
	                    }
						//truyen bang chiec khau va gia giam vao
						$cal_price->price_break_from_to = $result;
						//kiem tra field nao dang thay doi
						$cal_price->field_change = '';
						//chay tinh gia
						$arr_ret = $cal_price->cal_price_items();
						if(IS_LOCAL && $query['currency'] != 'cad') {
							$arr_ret = $cal_price->cal_price_currency('cad',$query['currency'], $this->arr_currency);
							$cal_price = new cal_price;
							$cal_price->arr_product_items = $vv;
							$vv = $cal_price->cal_price_currency('cad',$query['currency'], $this->arr_currency);
						}
                        //
                        if(isset($vv['line_no']))
                        	unset($vv['line_no']);
                        if(isset($arr_ret['same_parent']) && $arr_ret['same_parent'] == 0 && (isset($arr_ret['company_price_break']) && $arr_ret['company_price_break']) ) {
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
                                '_id'   => $vv['product_id'],
                                'name'  => $vv['product_name']
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

	public  function add_doc_from_module(){
		$arr_save=array();
		$this->redirect('/docs/entry/');
	}
	public function entry_search(){
		//parent class
		$arr_set = $this->opm->arr_settings;
		$arr_set['field']['panel_1']['code']['lock'] = '';
		$arr_set['field']['panel_1']['invoice_type']['element_input'] = '';
		$arr_set['field']['panel_1']['our_rep']['not_custom'] = '0';
		$arr_set['field']['panel_1']['our_csr']['not_custom'] = '0';
		$arr_set['field']['panel_4']['job_name']['not_custom'] = '0';
		$arr_set['field']['panel_4']['job_number']['lock'] = '0';
		$arr_set['field']['panel_4']['salesorder_name']['not_custom'] = '0';
		$arr_set['field']['panel_4']['salesorder_number']['lock'] = '0';
		$arr_set['field']['panel_1']['invoice_type']['default'] = '';
		$arr_set['field']['panel_1']['invoice_date']['default'] = '';
		$arr_set['field']['panel_4']['invoice_status']['default'] = '';
		$arr_set['field']['panel_4']['payment_due_date']['default'] = '';
		$arr_set['field']['panel_4']['payment_terms']['default'] = '';
		$arr_set['field']['panel_4']['taxval']['default'] = '';
		$arr_set['field']['panel_4']['tax']['default'] = '';

		$this->set('search_class','jt_input_search');
		$this->set('search_class2','jt_select_search');
		$this->set('search_flat','placeholder="1"');
		$where = array();
		if($this->Session->check($this->name.'_where'))
			$where = $this->Session->read($this->name.'_where');
		if(count($where)>0){
			foreach($arr_set['field'] as $ks => $vls){
				foreach($vls as $field => $values){
					if(isset($where[$field])){
						$arr_set['field'][$ks][$field]['default'] = $where[$field]['values'];
					}
				}
			}
		}
        unset($arr_set['relationship']['line_entry']['block']['products']['add'],
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
	public function swith_options($keys){
		parent::swith_options($keys);
		if($keys=='existing')
			echo URL.'/'.$this->params->params['controller'].'/entry';
		else if($keys=='under')
			echo URL.'/'.$this->params->params['controller'].'/entry';
		else if($keys=='over')
			echo URL.'/'.$this->params->params['controller'].'/entry';
		else if($keys == 'invoiced')
		{
			$arr_where['invoice_status'] = array('values' => 'Invoiced', 'operator' => '=');
			$this->Session->write($this->name . '_where', $arr_where);
			echo URL.'/'.$this->params->params['controller'].'/lists';
		}
		else if($keys== 'outstanding')
		{
            $current_time = strtotime(date('Y-m-d'));
			$arr_where['payment_due_date'] = array(
					'operator' 	=> 'other',
					'values'		=>	array(
								'invoice_status' 	=> array('$in' => array('In progress','Invoiced')),
								'payment_due_date' 	=> array('$lt' => new MongoDate($current_time - 30*DAY))
						)

				);
			$this->Session->write($this->name . '_where', $arr_where);
			echo URL . '/' . $this->params->params['controller'] . '/lists';
		}
		else if($keys=='finished_products')
			echo URL.'/'.$this->params->params['controller'].'/entry';
		else if($keys=='find_out_sync')
			echo URL.'/'.$this->params->params['controller'].'/entry';
		else if($keys=='find_history')
			echo URL.'/'.$this->params->params['controller'].'/history';
		else if($keys=='sync_stock_current')
			echo URL.'/'.$this->params->params['controller'].'/entry';
		else if($keys=='sync_stock_found')
			echo URL.'/'.$this->params->params['controller'].'/entry';
		else if ($keys == 'report_by_customer_summary')
			echo URL . '/' . $this->params->params['controller'] . '/option_summary_customer_find';
		else if ($keys == 'report_by_customer_detailed')
			echo URL . '/' . $this->params->params['controller'] . '/option_detailed_customer_find';
		else if ($keys == 'report_by_area_summary')
			echo URL . '/' . $this->params->params['controller'] . '/option_summary_customer_find/area';
		else if ($keys == 'report_by_area_detailed')
			echo URL . '/' . $this->params->params['controller'] . '/option_detailed_customer_find/area';
		else if ($keys == 'report_by_customer_summary_highest_first')
			echo URL . '/' . $this->params->params['controller'] . '/option_summary_highest_customer_find';
		else if ($keys == 'report_by_customer_detailed_tax_amounts')
			echo URL . '/' . $this->params->params['controller'] . '/option_detailed_tax_customer_find';
		else if ($keys == 'report_by_product_summary')
			echo URL . '/' . $this->params->params['controller'] . '/option_summary_product_find';
		else if ($keys == 'report_by_product_detailed')
			echo URL . '/' . $this->params->params['controller'] . '/option_detailed_product_find';
		else if ($keys == 'report_by_category_detailed')
            echo URL . '/' . $this->params->params['controller'] . '/option_detailed_product_find/category';
        else if ($keys == 'report_by_category_summary')
            echo URL . '/' . $this->params->params['controller'] . '/option_summary_product_find/category';
		else if($keys=='create_po')
			echo URL.'/purchaseorders/add/Product@'.$this->get_id();
		else if($keys == 'create_receipt')
			echo URL . '/' . $this->params->params['controller'] . '/create_receipt';
		else if($keys=='print_price_list')
			echo URL.'/'.$this->params->params['controller'].'/entry';
		else if($keys=='print_minilist')
			//echo URL.'/'.$this->params->params['controller'].'/entry';
			echo URL . '/' . $this->params->params['controller'] . '/view_minilist';
		else if($keys=='print_invoices')
			echo URL . '/' . $this->params->params['controller'] . '/view_invoices_exclude_cancellations';		
		else if($keys=='print_invoice')
			echo URL.'/'.$this->params->params['controller'].'/view_pdf';
		else if($keys=='statement_outstanding_for_current_company')
			echo URL.'/'.$this->params->params['controller'].'/outstanding_current_company';
		else if ($keys == 'email_invoice')
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
        else if($keys == 'accounts_receivable_all_outstanding')
            echo URL . '/' . $this->params->params['controller'] . '/accounts_receivable_all_outstanding/';
		else
			echo '';
		die;
	}

	public function set_cal_price(){
		$cal_price = new cal_price; //Option cal_price
		//set arr_price_break default
		$cal_price->arr_price_break = array();
		//set arr_product default
		$cal_price->arr_product = array();
		//set arr_product item default
		$cal_price->arr_product_items = array();
	}

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
                $old_balance=isset($query['sum_amount'])?$query['sum_amount']:0;
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
                    $new_balance=isset($arr_sum['sum_amount'])?$arr_sum['sum_amount']:0;
					// BaoNam: cập nhật lại Sales Account
					if($new_balance != $old_balance){
						$this->selectModel('Salesaccount');
						if(isset($arr_sales_invoice['company_id']) && is_object($arr_sales_invoice['company_id'])){
							$this->Salesaccount->update_account($arr_sales_invoice['company_id'], array(
																'model' => 'Company',
																'balance' =>  $new_balance - $old_balance,
																'invoices_credits' => $new_balance - $old_balance,
																));
						}elseif(isset($arr_sales_invoice['contact_id'])){
							$this->Salesaccount->update_account($arr_sales_invoice['contact_id'], array(
																'model' => 'Contact',
																'balance' => $new_balance - $old_balance,
																'invoices_credits' => $new_balance - $old_balance,
																));
						}
					}
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
	//Sử dụng thư viện cal_price để tính
	public function ajax_cal_line2(){
		$this->set_cal_price();
		$arr_ret = $arr_product_items = array();
		if(isset($_POST['arr'])){
			$getdata = $_POST['arr'];
			$getdata = (array)$getdata;
			//truong hop co id
			if(isset($getdata['id'])){
				$get_id = $getdata['id'];
				$qr= $this->opm->select_one(array('_id' =>new MongoId($this->get_id())));
				$query = $qr;
				if(isset($query['products']))
					$arr_pro = $arr_insert['products'] = (array)$query['products'];

				if(is_array($arr_pro) && count($arr_pro)>0 && isset($arr_pro[$get_id]) && !$arr_pro[$get_id]['deleted']){
					$arr_pro = array_merge((array)$arr_pro[$get_id],(array)$getdata);

					$tmp_qty = $arr_pro['quantity'];
					$arr_pro['quantity'] = isset($arr_pro['invoiced'])?$arr_pro['invoiced']:0;


					$ids=$this->get_id();
					$arr_sales_invoice=$this->opm->select_one(array('_id'=>new MongoId($ids)));
					$old_balance=isset($arr_sales_invoice['sum_amount'])?$arr_sales_invoice['sum_amount']:0;

					$cal_price->arr_product_items = $arr_pro;
					$arr_ret = $cal_price->cal_price_items();

					$arr_ret['invoiced']=$arr_ret['quantity'];
					$arr_ret['quantity'] = $tmp_qty;


					//Save all data
					$arr_insert['_id'] = new MongoId($this->get_id());
					$arr_insert['products'][$get_id] = array_merge((array)$arr_pro,(array)$arr_ret);
					$this->opm->save($arr_insert);
					//update sum
					$keyfield = array(
									"sub_total"		=> "sub_total",
									"tax"			=> "tax",
									"amount"		=> "amount",
									"sum_sub_total"	=> "sum_sub_total",
									"sum_tax"		=> "sum_tax",
									"sum_amount"	=> "sum_amount"
								);

					$arr_sum = $this->update_sum('products',$keyfield);
					$new_balance=isset($arr_sum['sum_amount'])?$arr_sum['sum_amount']:0;
					$arr_ret = array_merge((array)$arr_ret,(array)$arr_sum);
					//Return data for display

					// BaoNam: cập nhật lại Sales Account
					if($new_balance != $old_balance){
						$this->selectModel('Salesaccount');
						if(isset($arr_sales_invoice['company_id']) && is_object($arr_sales_invoice['company_id'])){
							$this->Salesaccount->update_account($arr_sales_invoice['company_id'], array(
																'model' => 'Company',
																'balance' =>  $new_balance - $old_balance,
																'invoices_credits' => $new_balance - $old_balance,
																));
						}elseif(isset($arr_sales_invoice['contact_id'])){
							$this->Salesaccount->update_account($arr_sales_invoice['contact_id'], array(
																'model' => 'Contact',
																'balance' => $new_balance - $old_balance,
																'invoices_credits' => $new_balance - $old_balance,
																));
						}
					}

					echo json_encode($arr_ret);
				}
			//truong hop khong chon id nao
			}else{
				echo '';
			}
		}
		die;
	}


	var $is_text = 0;

	//subtab line entry
	public function line_entry() {
		$is_text = $this->is_text;
		if ($this->check_lock()) {
			$this->opm->set_lock_option('line_entry', 'products');
			$this->opm->set_lock_option('text_entry', 'products');
		}
		$subdatas = $arr_ret = array();
		$codeauto = 0;
		$opname = 'products';
		$sum_sub_total = $sum_tax = 0;
		$subdatas[$opname] = array();
		$ids = $this->get_id();
		if ($ids != '') {
			//get entry data
			$invoice = $this->opm->select_one(array('_id'=>new MongoId($ids)),array('date_modified', 'shipping_cost','invoice_status','invoice_status_old', 'invoice_type'));
            $prefix_cache_name = 'line_salesinvoice_'.$ids.'_';
            $cache_name = $prefix_cache_name.$invoice['date_modified']->sec;
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
            	if($invoice['invoice_status'] != 'Credit' && (!isset($invoice['invoice_status_old']) || $invoice['invoice_status_old'] != 'Credit') && $invoice['invoice_type'] != 'Credit'){
	            	$minimum = $this->get_minimum_order();
	                if($arr_ret['sum_sub_total']<$minimum){
	                	$arr_ret = $this->get_minimum_order_adjustment($arr_ret,$minimum);
	                }
            	}
                $subdatas[$opname] = $arr_ret[$opname];
            }
            if(isset($invoice['invoice_status']) && $invoice['invoice_status'] == 'Cancelled' )
                $arr_ret['sum_sub_total'] = $arr_ret['sum_tax'] = $arr_ret['sum_amount'] = 0;
        }
		$this->set('subdatas', $subdatas);
		$codeauto = $this->opm->get_auto_code('code');
		$this->set('nextcode', $codeauto);
		$this->set('file_name', 'salesorder_' . $ids);
		$this->set('sum_sub_total', $arr_ret['sum_sub_total']);
		$this->set('sum_amount', $arr_ret['sum_amount']);
		$this->set('sum_tax', $arr_ret['sum_tax']);
		$this->set('currency', $arr_ret['currency']);
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
	 public function line_entry_data($opname = 'products', $is_text = 0,$mod = '') {
        $arr_ret = array(); $option_for = '';
        $this->selectModel('Setting');
        if(isset($this->module_id))
        	$ids = $this->module_id;
        else
        	$ids = $this->get_id();
        if ($ids != '') {
            $newdata = $option_select_dynamic = array();
            $query = $this->opm->select_one(array('_id' => new MongoId($ids)),array('options','products','sum_sub_total','sum_amount','sum_tax','rfqs','invoice_date','currency'));
            if(!isset($query['currency']))
            	$query['currency'] = 'cad';
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
            $arr_ret['currency'] = $query['currency'];
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
            $arr_product_rfq = Cache::read('arr_product_rfq');
            if(!$arr_product_rfq){
                $arr_product_rfq = $this->Product->select_all(array(
                                           'arr_where'=>array('is_rfq'=>1),
                                           'arr_field'=>array('_id')
                                           ));
                $arr_product_rfq = iterator_to_array($arr_product_rfq);
                Cache::write('arr_product_rfq',$arr_product_rfq);
            }
            $option_for_sort = array();
            if (isset($query[$opname]) && is_array($query[$opname])) {
                $options = array();
                if(isset($query['options']) && !empty($query['options']) )
                    $options = $query['options'];
                foreach ($query[$opname] as $key => $arr) {
                    if (!$arr['deleted']) {
                        $newdata[$key] = $arr;
                        if(IS_LOCAL){
                        	$newdata[$key]['currency'] = $query['currency'];
                        	$newdata[$key]['xlock']['currency'] = '1';
                        }
                        if( isset($arr['bleed']) && $arr['bleed'] ) {
                            $newdata[$key]['xclass'] = 'bleed';
                        }
						//set default Unit price
						if(!isset($arr['custom_unit_price']) && isset($arr['unit_price']))
							$newdata[$key]['custom_unit_price'] = $arr['unit_price'];

                        if(!isset($arr['option_for'])){
                            $origin_options = array();
                            if (isset($arr['origin_options'])) {
                                $origin_options = $arr['origin_options'];
                            }
                            $difference_option = false;
                            $option_qty = 0;
                            $option = $this->new_option_data(array('key'=>$key,'products_id'=>$arr['products_id'],'options'=>$query['options'],'products'=>$query['products'],'date'=>$query['invoice_date']),$query['products']);
                            //Khoa sell_by,oum neu nhu line nay co option
                            //Khoa tiep sell_price neu line nay co option same_parent
                            if(isset($option['option'])&&!empty($option['option'])){
                                foreach($option['option'] as $value){
                                    if($value['deleted']) continue;
                                    if(isset($value['choice'])&&$value['choice']==0&&isset($value['require']) && $value['require']!=1) continue;
                                    if(isset($value['oum']) && isset($arr['oum']) && $value['oum']!=$arr['oum'])
                                        $newdata[$key]['oum'] = 'Mixed';
                                    $newdata[$key]['xlock']['sell_by'] = '1';
                                    $newdata[$key]['xlock']['oum'] = '1';
                                    if (!$difference_option && array_search($value['product_id'], array_column($origin_options, '_id')) === false ) {
                                        $difference_option = true;
                                    }
                                    $option_qty++;
                                    // if(!isset($value['same_parent']) || $value['same_parent']==0) continue;
                                    // $newdata[$key]['xlock']['sell_price'] = '1';
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
                                if(isset($arr_product_rfq[(string)$arr['products_id']]))
                                    $newdata[$key]['xstyle_element']['receipts']= 'background-color: #852020 !important;';
                            }
                            if(!isset($arr['company_price_break']) || !$arr['company_price_break'])
                                $newdata[$key]['xhidden']['vip'] = '1';
                        } else {
                            $newdata[$key]['xlock']['sell_by'] = '1';
                            $newdata[$key]['xlock']['oum'] = '1';
                            $newdata[$key]['xempty']['vip'] = '1';
                        }
						$newdata[$key]['option'] = 1;
                        $newdata[$key]['option_group'] = '';
						if (isset($newdata[$key]['products_id']) && $newdata[$key]['products_id']!='')
							$newdata[$key]['xlock']['unit_price']   = '1';
						else
							$newdata[$key]['xlock']['unit_price']   = '0';
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
                                    if(isset($get_name_only)){
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
                            	 ||strpos(strtolower($newdata[$key]['option_group']), 'cut') !== false))
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
                            if(isset($option['option'])&&!empty($option['option'])){
                                foreach($option['option'] as $value){
                                    if(isset($value['deleted']) && $value['deleted']) continue;
                                    if(!isset($value['product_name']) || $value['product_name']=='') continue;
                                    if(!isset($value['view_in_detail'])|| $value['view_in_detail']==0)continue;
                                    $newdata[$key]['products_costing_name'] .= htmlentities($value['product_name']).' ('.(isset($value['quantity']) ? $value['quantity'] : 0).')<br />';
                                }
                            }
                            $newdata[$key]['xlock']['products_name']= '1';
                            $newdata[$key]['xlock']['sell_by']  = '1';
                            $newdata[$key]['xlock']['sell_price']   = '1';
                            $newdata[$key]['xlock']['oum']      = '1';
                            $newdata[$key]['xlock']['quantity']     = '1';
                            $newdata[$key]['xlock']['sizew']        = '1';
                            $newdata[$key]['xlock']['sizew_unit']   = '1';
                            $newdata[$key]['xlock']['sizeh']        = '1';
                            $newdata[$key]['xlock']['sizeh_unit']   = '1';
                            $newdata[$key]['xlock']['unit_price']   = '1';
                            $newdata[$key]['xlock']['adj_qty']  = '1';
                            $newdata[$key]['xlock']['sub_total']    = '1';
                            $newdata[$key]['xlock']['tax']      = '1';
                            $newdata[$key]['xlock']['amount']       = '1';
                            $newdata[$key]['xlock']['option']       = '1';
                            $newdata[$key]['xlock']['receipts']     = '1';
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
                            $newdata[$key]['sub_total'] = (string)$this->opm->format_currency( $arr['sub_total']);
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
                            if(isset($arr['same_parent'])&&$arr['same_parent']==1) {
                                $newdata[$key]['xempty']['custom_unit_price']   = '1';
                            	$newdata[$key]['xempty']['currency'] = '1';
                            }
                            $newdata[$key]['_id'] = $key;
							$newdata[$key]['sku_disable'] = '1';
							$newdata[$key]['sku'] = '';
							$newdata[$key]['remove_deleted'] = '1';
							$newdata[$key]['icon']['products_name'] = (is_object($arr['products_id']) ? URL.'/products/entry/'.$arr['products_id'] : '#');
							$newdata[$key]['sort_key'] = $this->opm->num_to_string($arr['option_for']).'-'.$key;
							if($mod!='options_list')
							     unset($newdata[$key]['products_id']);
						}


                        //data RFQ's
                        $receipts = 0;
                        if (isset($query['rfqs']) && is_array($query['rfqs']) && count($query['rfqs']) > 0) {
                            foreach ($query['rfqs'] as $rk => $rv) {
                                if (!$rv['deleted'] && isset($rv['rfq_code']) && (int) $rv['rfq_code'] == $key) {
                                    $receipts = 1;
                                }
                            }
                            $newdata[$key]['receipts'] = $receipts;
                        } else
                            $newdata[$key]['receipts'] = 0;

                        //chặn không cho custom size nếu is_custom_size = 1
                        if(is_object($arr['products_id'])){
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
							$newdata[$key]['xempty']['adj_qty'] 	= '1';
							$newdata[$key]['xempty']['sub_total'] 	= '1';
							$newdata[$key]['xempty']['tax'] 		= '1';
							$newdata[$key]['xempty']['amount'] 		= '1';
							$newdata[$key]['xempty']['option'] 		= '1';
							$newdata[$key]['xempty']['receipts'] 	= '1';
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
	public function view_product_option(){
		echo '';
		die;
	}

	//subtab Text entry
	public function text_entry(){
		$this->is_text = 1;
		$this->line_entry();
	}

	//Text pdf
	public function test_pdf(){
		$this->layout = 'pdf';
		//set footer
		$this->render('test_pdf');
	}

	public function email_pdf(){
		$this->layout = 'pdf';
		$ids = $this->get_id();
		if($ids!=''){
			$query = $this->opm->select_one(array('_id' =>new MongoId($ids)));
			$arrtemp = $query;
			//set header
			$this->set('logo_link','img/logo_anvy.jpg');
			$this->set('company_address','3145 - 5th Ave NE<br />Calgary  AB  T2A  6A3<br />');

			//customer address
			$customer = '';
			if(isset($arrtemp['company_id']) && strlen($arrtemp['company_id'])==24)
				$customer .= '<b>'.$this->get_name('Company',$arrtemp['company_id']).'</b><br />';
			else if(isset($arrtemp['company_name']))
				$customer .= '<b>'.$arrtemp['company_name'].'</b><br />';
			if(isset($arrtemp['contact_id']) && strlen($arrtemp['contact_id'])==24)
				$customer .= $this->get_name('Contact',$arrtemp['contact_id']).'<br />';
			else if(isset($arrtemp['contact_name']))
				$customer .= $arrtemp['contact_name'].'<br />';

			//loop 2 address
			$arradd = array('invoice','shipping');
			foreach($arradd as $vvs){
				$kk = $vvs; $customer_address = '';
				if(isset($arrtemp[$kk.'_address']) && isset($arrtemp[$kk.'_address'][0]) && count($arrtemp[$kk.'_address'])>0){
					$temp = $arrtemp[$kk.'_address'][0];
					if(isset($temp[$kk.'_address_1']) && $temp[$kk.'_address_1']!='')
						$customer_address .= $temp[$kk.'_address_1'].' ';
					if(isset($temp[$kk.'_address_2']) && $temp[$kk.'_address_2']!='')
						$customer_address .= $temp[$kk.'_address_2'].' ';
					if(isset($temp[$kk.'_address_3']) && $temp[$kk.'_address_3']!='')
						$customer_address .= $temp[$kk.'_address_3'].'<br />';
					else
						$customer_address .= '<br />';
					if(isset($temp[$kk.'_town_city']) && $temp[$kk.'_town_city']!='')
						$customer_address .= $temp[$kk.'_town_city'];

					if(isset($temp[$kk.'_province_state']))
						$customer_address .= ' '.$temp[$kk.'_province_state'].' ';
					else if(isset($temp[$kk.'_province_state_id']) && isset($temp[$kk.'_country_id'])){
						$keytemp = $temp[$kk.'_province_state_id'];
						$provkey = $this->province($temp[$kk.'_country_id']);
						if(isset($provkey[$temp]))
							$customer_address .= ' '.$provkey[$temp].' ';
					}


					if(isset($temp[$kk.'_zip_postcode']) && $temp[$kk.'_zip_postcode']!='')
						$customer_address .= $temp[$kk.'_zip_postcode'];

					if(isset($temp[$kk.'_country']) && isset($temp[$kk.'_country_id']) && (int)$temp[$kk.'_country_id']!="CA")
						$customer_address .= ' '.$temp[$kk.'_country'].'<br />';
					else
						$customer_address .= '<br />';
					$arr_address[$kk] = $customer_address;
				}
			}

			if(isset($arrtemp['name']) && $arrtemp['name']!='')
				$heading = $arrtemp['name'];
			else
				$heading = '';
			if(!isset($arr_address['invoice']))
				$arr_address['invoice'] = '';
			$this->set('customer_address',$customer.$arr_address['invoice']);
			if(!isset($arr_address['shipping']))
				$arr_address['shipping'] = '';
			$this->set('shipping_address',$arr_address['shipping']);

			// info data
			$info_data = (object) array();
			$info_data->contact_name = $arrtemp['contact_name'];
			$info_data->no = $arrtemp['code'];
			$info_data->job_no = $arrtemp['job_number'];
			$info_data->date = $this->opm->format_date($arrtemp['invoice_date']);
			$info_data->po_no = isset($arrtemp['customer_po_no'])?$arrtemp['customer_po_no']:'';
			$info_data->ac_no = '';
			$info_data->terms = isset($arrtemp['payment_terms'])?$arrtemp['payment_terms']:'';
			$info_data->required_date = $this->opm->format_date($arrtemp['payment_due_date']);
			$this->set('info_data', $info_data);


			//$this->set('quote_date',$this->opm->format_date($arrtemp['quotation_date']));
			/**Nội dung bảng giá */
			$date_now = date('Ymd');
			$time=time();
			$filename = 'SIN'.$date_now.$time.'-'.$info_data->no;


			$thisfolder = 'upload'.DS.date("Y_m");
			$thisfolder_1='upload'.','.date("Y_m");

			$folder = ROOT.DS.APP_DIR.DS.WEBROOT_DIR.DS.$thisfolder;
			if (!file_exists($folder)) {
				mkdir($folder, 0777, true);
			}

			$this->set('filename', $filename);
			$this->set('heading',$heading);
			$html_cont = '';
			if(isset($arrtemp['products']) && is_array($arrtemp['products']) && count($arrtemp['products'])>0){
				$line = 0; $colum = 7;
				foreach($arrtemp['products'] as $keys=>$values){
					if(!$values['deleted']){
						if($line%2==0)
							$bgs = '#fdfcfa';
						else
							$bgs = '#eeeeee';
						//code
						$html_cont .= '<tr style="background-color:'.$bgs.';"><td class="first">';
						if(isset($values['code']))
							$html_cont .= '  '.$values['code'];
						else
							$html_cont .= '  #'.$keys;
						//desription
						$html_cont .= '</td><td>';
						if(isset($values['products_name']))
							$html_cont .= str_replace("\n","<br />",$values['products_name']);
						else
							$html_cont .= 'Empty';
						//width
						$html_cont .= '</td><td style="text-align: right;">';
						if(isset($values['sizew']) && $values['sizew']!='' && isset($values['sizew_unit']) && $values['sizew_unit']!='')
							$html_cont .= $values['sizew'].' ('.$values['sizew_unit'].')';
						else if(isset($values['sizew'])&& $values['sizew']!='')
							$html_cont .= $values['sizew'].' (in.)';
						else
							$html_cont .= '';
						//height
						$html_cont .= '</td><td style="text-align: right;">';
						if(isset($values['sizeh']) && $values['sizeh']!='' && isset($values['sizeh_unit']) && $values['sizeh_unit']!='')
							$html_cont .= $values['sizeh'].' ('.$values['sizeh_unit'].')';
						else if(isset($values['sizeh']) && $values['sizeh']!='' )
							$html_cont .= $values['sizeh'].' (in.)';
						else
							$html_cont .= '';
						//Unit price
						$html_cont .= '</td><td style="text-align: right;">';
						if(isset($values['unit_price']))
							$html_cont .= $this->opm->format_currency($values['unit_price']);
						else
							$html_cont .= '0.00';
						//Qty
						$html_cont .= '</td><td style="text-align: right;">';
						if(isset($values['quantity']))
							$html_cont .= $values['quantity'];
						else
							$html_cont .= '';
						//line total
						$html_cont .= '</td><td style="text-align: right;" class="end">';
						if(isset($values['sub_total']))
							$html_cont .= $this->opm->format_currency($values['sub_total']);
						else
							$html_cont .= '';


						$html_cont .= '</td></tr>';
						$line++;
					}//end if deleted
				}//end for


				if($line%2==0){
					$bgs = '#fdfcfa';$bgs2 = '#eeeeee';
				}else{
					$bgs = '#eeeeee';$bgs2 = '#fdfcfa';
				}

				$sub_total = $total = $taxtotal = 0.00;
				if(isset($arrtemp['sum_sub_total']))
					$sub_total = $arrtemp['sum_sub_total'];
				if(isset($arrtemp['sum_tax']))
					$taxtotal = $arrtemp['sum_tax'];
				if(isset($arrtemp['sum_amount']))
					$total = $arrtemp['sum_amount'];
				//Sub Total
				$html_cont .= '<tr style="background-color:'.$bgs.';">
									<td colspan="'.($colum-1).'" style="text-align: right;" style="font-weight:bold;border-top:2px solid #aaa;" class="first">Sub Total:</td>
									<td style="text-align: right;" style="border-top:2px solid #aaa;" class="end">'.$this->opm->format_currency($sub_total).'</td>
							   </tr>';
				//GST
				$html_cont .= '<tr style="background-color:'.$bgs2.';">
									<td colspan="'.($colum-1).'" style="text-align: right;" style="font-weight:bold;" class="first">HST/GST:</td>
									<td style="text-align: right;" class="end">'.$this->opm->format_currency($taxtotal).'</td>
							   </tr>';
				//Total
				$html_cont .= '<tr style="background-color:'.$bgs.';">
									<td colspan="'.($colum-1).'" style="text-align: right;" style="font-weight:bold;" class="first bottom">Total:</td>
									<td style="text-align: right;" class="end bottom">'.$this->opm->format_currency($total).'</td>
							   </tr>';

			}//end if


			$this->set('html_cont',$html_cont);
			if(isset($arrtemp['our_csr'])){
				$this->set('user_name',' '.$arrtemp['our_csr']);
			}else
				$this->set('user_name',' '.$this->opm->user_name());
			//end set content
			$this->set('link_this_folder',$thisfolder);
			$this->render('email_pdf');
			$v_link_pdf= $thisfolder_1.','.$filename.'.pdf';
			$v_file_name=$filename.'.pdf';

			$this->redirect('/docs/add_from_option/'.$this->ModuleName().'/'.$this->get_id().'/'.$v_link_pdf.'/'.$v_file_name.'/'.$this->params->params['controller'].'');



		}
		die;
	}
	public function view_pdf($getfile=false,$type='',$ids = '') {
		if($type=='group')
			$this->export_pdf = true;
		$this->layout = 'pdf';
		if($ids == '')
			$ids = $this->get_id();
		else
			$this->module_id = $ids;
		if ($ids != '') {
			$query = $this->opm->select_one(array('_id' => new MongoId($ids)));
			if (!isset($_GET['print_pdf'])) {
                $filename = 'SI-' . $query['code'] . (empty($type) ? '-detailed' : ($type == 'group' ? '' : '-' . $type));
                if ($this->print_pdf(array(
                    'report_file_name' => $filename,
                    'report_url' => URL . '/salesinvoices/view_pdf/' . $getfile . '/' . $type . '/' . $ids,

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
			if(!isset($query['currency']))
				$query['currency'] = 'cad';
			$arrtemp = $query;

            // customer address

            $customer = '';
            if (isset($arrtemp['company_id']) && strlen($arrtemp['company_id']) == 24) $customer.= '<b>' . $this->get_name('Company', $arrtemp['company_id']) . '</b><br />';
            else if (isset($arrtemp['company_name'])) $customer.= '<b>' . $arrtemp['company_name'] . '</b>';
            if (isset($arrtemp['contact_id']) && strlen($arrtemp['contact_id']) == 24) $customer.=  '<p>'.$this->get_name('Contact', $arrtemp['contact_id']).'</p>' ;
            else if (isset($arrtemp['contact_name'])) $customer.= '<p>'.$arrtemp['contact_name'].'</p>';

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
                    else if (isset($temp[$kk . '_province_state_id']) && isset($temp[$kk . '_country_id'])) {
                        $keytemp = $temp[$kk . '_province_state_id'];
                        $provkey = $this->province($temp[$kk . '_country_id']);
                        if (isset($provkey[$temp])) $customer_address.= ' ' . $provkey[$temp] . '<br/>';
                    }

                    if (isset($temp[$kk . '_zip_postcode']) && $temp[$kk . '_zip_postcode'] != '') $customer_address.= $temp[$kk . '_zip_postcode'] . '<br/>';
                    if (isset($temp[$kk . '_country']) && isset($temp[$kk . '_country_id']) && $temp[$kk . '_country_id'] != "CA") $customer_address.= ' ' . $temp[$kk . '_country'] . '<br />';
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
            $arrData['pdf_name'] = 'Sales invoice';
            $arrData['shipping_address'] = $ship_to . $arr_address['shipping'];

	        if (isset($arrtemp['other_comment'])) $arrData['note'] = nl2br($arrtemp['other_comment']);
			$html_cont = '';
			$line_entry_data = $this->line_entry_data();
			if($query['invoice_type'] != 'Credit'){
				$minimum = $this->get_minimum_order('Salesinvoice', $ids);
				if($line_entry_data['sum_sub_total']<$minimum){
	                $line_entry_data =  $this->get_minimum_order_adjustment($line_entry_data,$minimum, $ids);
	            }
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
				$this->selectModel('Setting');
				$arr_currency = $this->Setting->select_option_vl(array('setting_value'=>'currency_type'));
				if(isset($arrtemp['options']) && !empty($arrtemp['options']) )
					$options = $arrtemp['options'];
				if($type == 'group'){
					$arr_price = array();
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
						if (!isset($values['deleted']) || !$values['deleted']) {
                            if (isset($values['option_for']) && isset($query['products'][$values['option_for']])) {
                                $pro = $query['products'][$values['option_for']];
                                if (isset($pro['sku']) && strpos(str_replace(' ', '', $pro['sku']) , 'DCP-') !== false) continue;
                            }
                            if( !isset($values['custom_unit_price']) )
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
                        } //
                    }//end for
				} else {

					foreach ($line_entry_data['products'] as $values) {
						$keys = $values['_id'];
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
                    }//end for
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
			}//end if.
				$contact_id = ''; $company = array();
			if(!empty($query['our_rep_id']))
				$contact_id = $query['our_rep_id'];
			if(!empty($query['our_csr_id']))
				$contact_id = $query['our_csr_id'];
			if($contact_id!=''){
				$this->selectModel('Contact');
				$arr_contact = $this->Contact->select_one(array('_id' => $contact_id),array('company_id','company'));
				$company['_id'] = $arr_contact['company_id'];
				$company['name'] = $arr_contact['company'];
			}else{
				$this->selectModel('Company');
				$company = $this->Company->select_one(array('system' => true),array('_id','name'));
			}

			$this->selectModel('Salesaccount');
			$salesaccount = $this->Salesaccount->select_one(array('company_id' => $company['_id']),array('tax_no'));
			if(!isset($salesaccount['tax_no']))
				$salesaccount['tax_no'] = '';
			$arrData['right_info'] = array();
			if( $query['invoice_type'] == 'Credit' ) {
            	$arrData['right_info']['Credit Note']	= $arrtemp['code'];
			} else {
            	$arrData['right_info']['Sales Invoice']	= $arrtemp['code'];

			}
            $arrData['custom_footer'] = '<tr class="sum_title">
                                            <td colspan="5" style="border-top: 1pt solid #ABABAB; font-size: 16pt;font-weight: bold; text-align: left;">Thank you for your business.</td>
                                            <td style="border-top: 1pt solid #ABABAB;">
                                                Sub Total:
                                            </td>
                                            <td style="border-top: 1pt solid #ABABAB;">
                                                '. $arrData['sum_sub_total'] .'
                                            </td>
                                        </tr>
                                        <tr class="sum_title">
                                            <td colspan="5" style="font-weight: normal; text-align: left;" >
                                                Please make cheque payable to <span style="font-size: 16px; font-weight: bold">'.
                                                ( isset($company['nametest']) ? $company['name'] : 'BanhMi SUB Ltd.'). '
                                            </td>
                                            <td>
                                                HST/GST:
                                            </td>
                                            <td>
                                                '. $arrData['sum_tax'] .'
                                            </td>
                                        </tr>
                                        <tr class="sum_title">
                                            <td colspan="5" style="text-align: left;" >GST / HST#: '.$salesaccount['tax_no'].'</td>
                                            <td>
                                                Total:
                                            </td>
                                            <td>
                                                '. $arrData['sum_amount'] .'
                                            </td>
                                        </tr>';
			$arrData['right_info']['Job no']	= $arrtemp['job_number'];
			$arrData['right_info']['Date']	= $this->opm->format_date($arrtemp['invoice_date']);
			$arrData['right_info']['PO No']	= $arrtemp['customer_po_no'];
			$arrData['right_info']['A/c No']	= '';
			$arrData['right_info']['Terms']	= ($arrtemp['payment_terms']!= 0 ? $arrtemp['payment_terms'].' days' : '<span style="color: red; font-weight: bold;">DUE ON RECEIPT</span>');
			$arrData['right_info']['Due Date']	= $this->opm->format_date($arrtemp['payment_due_date']);
			$arrData['render_path'] = '../Elements/view_pdf';
			$arrData['no_note'] = true;
			if($query['invoice_status'] == 'Cancelled') {
				$arrData['is_cancelled'] = true;
			} else if ($query['invoice_status'] == 'Paid') {
                $arrData['is_paid'] = true;
            }
            $this->render_pdf($arrData);
		}
	}



	//address
	public function set_entry_address($arr_tmp,$arr_set){
		$address_fset = array('address_1','address_2','address_3','town_city','country','province_state','zip_postcode');
		$address_value = $address_province_id = $address_country_id = $address_province = $address_country = array();
		$address_controller = array('invoice','shipping');
		$address_value['invoice'] = $address_value['shipping']= array('','','','',"CA",'','');
		$this->set('address_controller',$address_controller);//set
		$address_key 	= array('invoice','shipping');
		$this->set('address_key',$address_key);//set
		$this->set('shipping_contact_name', (isset($arr_tmp['shipping_address'][0]['shipping_contact_name']) ? $arr_tmp['shipping_address'][0]['shipping_contact_name'] : ''));
		$address_country 	= $this->country();
		foreach($address_key as $kss=>$vss){
			//neu ton tai address trong data base
			if(isset($arr_tmp[$vss.'_address'][0])){
				$arr_temp_op = $arr_tmp[$vss.'_address'][0];
				for($i=0;$i<count($address_fset);$i++){ //loop field and set value for display
					if(isset($arr_temp_op[$vss.'_'.$address_fset[$i]])){
						$address_value[$vss][$i] = $arr_temp_op[$vss.'_'.$address_fset[$i]];
					}else{
						$address_value[$vss][$i] = '';
					}
				}//pr($arr_temp_op);die;
				//get province list and country list

				if(isset($arr_temp_op[$vss.'_country_id']))
					$address_province[$vss] = $this->province($arr_temp_op[$vss.'_country_id']);
				else
					$address_province[$vss] = $this->province();
				//set province
				if(isset($arr_temp_op[$vss.'_province_state_id']) && $arr_temp_op[$vss.'_province_state_id']!='' && isset($address_province[$vss][$arr_temp_op[$vss.'_province_state_id']]) )
					$address_province_id[$kss] = $arr_temp_op[$vss.'_province_state_id'];
				else if(isset($arr_temp_op[$vss.'_province_state']))
					$address_province_id[$kss] = $arr_temp_op[$vss.'_province_state'];
				else
					$address_province_id[$kss] = '';

				//set country
				if(isset($arr_temp_op[$vss.'_country_id'])){
					$address_country_id[$kss] = $arr_temp_op[$vss.'_country_id'];
					$address_province[$vss] = $this->province($arr_temp_op[$vss.'_country_id']);
				}else{
					$address_country_id[$kss] = "CA";
					$address_province[$vss] = $this->province("CA");
				}

				$address_add[$vss] = '0';
			//chua co address trong data
			}else{
				$address_country_id[$kss] = "CA";
				$address_province[$vss] = $this->province("CA");
				$address_add[$vss] = '1';
			}
		}
		//pr($address_province);
		$this->set('address_value',$address_value);
		$address_hidden_field = array('invoice_address','shipping_address');
		$this->set('address_hidden_field',$address_hidden_field);//set
		$address_label[0] = $arr_set['field']['panel_2']['invoice_address']['name'];
		$address_label[1] = $arr_set['field']['panel_2']['shipping_address']['name'];
		$this->set('address_label',$address_label);//set
		$address_conner[0]['top'] 		= 'hgt fixbor';
		$address_conner[0]['bottom'] 	= 'fixbor2 jt_ppbot';
		$address_conner[1]['top'] 		= 'hgt';
		$address_conner[1]['bottom'] 	= 'fixbor3 jt_ppbot';
		$this->set('address_conner',$address_conner);//set
		$this->set('address_country',$address_country);//set
		$this->set('address_country_id',$address_country_id);//set
		$this->set('address_province',$address_province);//set
		$this->set('address_province_id',$address_province_id);//set
		$this->set('address_more_line',1);//set
		$this->set('address_onchange',"save_address_pr('\"+keys+\"');");
		if(isset($arr_tmp['company_id']) && strlen($arr_tmp['company_id'])==24)
		$this->set('address_company_id','company_id');
		if(isset($arr_tmp['contact_id']) && strlen($arr_tmp['contact_id'])==24)
		$this->set('address_contact_id','contact_id');
		$this->set('address_add',$address_add);
	}


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
                $tmp['Salesinvoice']['company'] = $_GET['company_name'];
            }
            if (isset($_GET['job_id']))
                $cond['job_id'] = new MongoId($_GET['job_id']);

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
        $arr_order = array('invoice_date' => -1);
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
        if (!empty($this->data) && !empty($_POST) && isset($this->data['Salesinvoice'])) {
            $arr_post = $this->Common->strip_search($this->data['Salesinvoice']);
            if (isset($arr_post['contact_name']) && strlen($arr_post['contact_name']) > 0) {
                $cond['contact_name'] = new MongoRegex('/' . trim($arr_post['contact_name']) . '/i');
            }

            if (strlen($arr_post['company']) > 0) {
                $cond['company_name'] = new MongoRegex('/' . trim($arr_post['company']) . '/i');
            }
        }
        unset($_GET['_']);
        $cache_key = md5(serialize($_GET));
        $no_cache = true;
        if(empty($_POST) && !isset($_GET['new']) ){
            $arr_salesinvoices = Cache::read('popup_salesinvoice_'.$cache_key);
            if($arr_salesinvoices)
                $no_cache = false;
        }
        $this->selectModel('Salesinvoice');
        if($no_cache){
        	$arr_salesinvoices = $this->Salesinvoice->select_all(array(
	            'arr_where' => $cond,
	            'arr_order' => $arr_order,
	            'arr_field' => array('code', 'company_id', 'company_name', 'contact_id', 'contact_name','our_rep','our_rep_id','our_csr','our_csr_id','invoice_status','invoice_date'),
	            'limit' => $limit,
	            'skip' => $skip
	        ));
	        if(empty($_POST))
                Cache::write('popup_salesinvoice_'.$cache_key,iterator_to_array($arr_salesinvoices));
        }

        $this->set('arr_salesinvoices', $arr_salesinvoices);

        $total_page = $total_record = $total_current = 0;
        if (is_object($arr_salesinvoices)) {
            $total_current = $arr_salesinvoices->count(true);
            $total_record = $arr_salesinvoices->count();
            if ($total_record % $limit != 0) {
                $total_page = floor($total_record / $limit) + 1;
            } else {
                $total_page = $total_record / $limit;
            }
        } else if(is_array($arr_salesinvoices)){
            $total_current = count($arr_salesinvoices);
            $total_record = $this->Salesinvoice->count($cond);
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

	public function receipt(){
		$invoice = $this->opm->select_one(array('_id'=> new MongoId($this->get_id())),array('company_id','invoice_status'));
		$this->set('invoice_status',(isset($invoice['invoice_status']) ? $invoice['invoice_status'] : 'In Progress'));
		$outstanding_invoice = array();
		if(isset($invoice['company_id']) && is_object($invoice['company_id']))
			$outstanding_invoice = $this->requestAction('receipts/outstanding/'.$invoice['company_id']);
		$this->set('outstanding_invoice', $outstanding_invoice);
	}
	public function receipt_overall_total($total_receipt=0)
	{
		if($this->check_permission('salesinvoices_@_entry_@_view')){
			$id = $this->get_id();
			$this->selectModel('Salesinvoice');
			$salesinvoice = $this->Salesinvoice->select_one(array('_id'=> new MongoId($id)),array('sum_amount','company_id','taxval','sum_sub_total', 'shipping_cost', 'invoice_status', 'invoice_type'));
			$minimum = 50;
	        $this->selectModel('Stuffs');
	        $product = $this->Stuffs->select_one(array('value'=>"Minimum Order Adjustment"),array('product_id'));
	        $product_id = $product['product_id'];
	        if(isset($product['product_id'])&&is_object($product['product_id'])){
	            $this->selectModel('Product');
	            $product = $this->Product->select_one(array('_id'=> new MongoId($product_id)),array('sell_price'));
	            $minimum = $product['sell_price'];
	            $product_id = $product['_id'];
	        }
			if(is_object($salesinvoice['company_id'])){
					$company_id = $salesinvoice['company_id'];
					$this->selectModel('Company');
					$company = $this->Company->select_one(array('_id'=>$company_id),array('pricing'));
				if(isset($company['pricing'])){
					foreach($company['pricing'] as $pricing){
						if(isset($pricing['deleted'])&&$pricing['deleted']) continue;
						if((string)$pricing['product_id']!=(string)$product_id) continue;
						if(!isset($pricing['price_break']) || empty($pricing['price_break'])) continue;
						$price_break = reset($pricing['price_break']);
						$minimum =  (float)$price_break['unit_price']; break;
					}
				}
			}
			if($salesinvoice['invoice_type'] != 'Credit'
				&& $salesinvoice['invoice_status'] != 'Credit'
				&& $salesinvoice['sum_sub_total']<$minimum){
				$salesinvoice['taxval'] = (isset($salesinvoice['taxval']) ? $salesinvoice['taxval'] : 0);
				$salesinvoice['sum_amount'] = $minimum + ($minimum*$salesinvoice['taxval']/100);
			}
			$salesinvoice['total_invoice'] =  $salesinvoice['sum_amount'];
			$salesinvoice['balance'] = $salesinvoice['total_invoice'];
			$salesinvoice['total_receipt'] = 0;
			if($total_receipt!=0){
				$total_receipt = str_replace(',', '', $total_receipt);
				$salesinvoice['total_receipt'] = $total_receipt;
				$salesinvoice['balance'] = (float)($salesinvoice['total_invoice'] - $salesinvoice['total_receipt']);
			}
			$salesinvoice['total_invoice'] = $this->opm->format_currency($salesinvoice['total_invoice']);
			$salesinvoice['total_receipt'] = $this->opm->format_currency($salesinvoice['total_receipt']);
			$salesinvoice['balance'] = $this->opm->format_currency($salesinvoice['balance']);
			echo json_encode($salesinvoice);
			die;
		}
	}
	public function receipt_content()
	{
		if($this->check_permission('salesinvoices_@_entry_@_view')){
			$id = $this->get_id();
			$this->selectModel('Salesinvoice');
			$si = $this->Salesinvoice->select_one(array('_id'=>new MongoId($id)));
			$this->selectModel('Receipt');
			$receipts = $this->Receipt->select_all(array(
												'arr_where'=> array(
																'allocation'=>array(
																				'$elemMatch'=>array(
																					'salesinvoice_id'=>new MongoId($id),
																					'deleted'		=>false,
																								)
																				)
																	),
												'arr_order'=>array('code'=>-1),
												'arr_field'=>array('_id','code','notes','account','amount_received','receipt_date','paid_by','our_bank_account','allocation')
									));
			$arr_data = array();
			$arr_receipt = array();
			foreach($receipts as $value)
				foreach($value['allocation'] as $key=>$val){
					if(!isset($val['salesinvoice_id']) || $val['salesinvoice_id']=='' ) continue;
					if($val['salesinvoice_id']==$id && !$val['deleted'] ){
						$arr_receipt[$value['code']]['_id'] = $value['_id'];
						$arr_receipt[$value['code']]['code'] = $value['code'];
						$arr_receipt[$value['code']]['date'] = $value['receipt_date'];
						$arr_receipt[$value['code']]['notes'] = $value['notes'];
						$arr_receipt[$value['code']]['paid_by'] = (isset($value['paid_by']) ? $value['paid_by'] : '');
						$arr_receipt[$value['code']]['our_bank_account'] = (isset($value['our_bank_account']) ? $value['our_bank_account'] : '');
						$arr_receipt[$value['code']]['receipts'][$key]['write_off'] = (isset($val['write_off']) ? $val['write_off'] : 0);
						$arr_receipt[$value['code']]['receipts'][$key]['key'] = $key;
						$arr_receipt[$value['code']]['receipts'][$key]['note'] = (isset($val['note']) ? $val['note'] : '');
						$arr_receipt[$value['code']]['receipts'][$key]['amount'] = $val['amount'];
					}
				}
			$this->selectModel('Setting');
			$arr_data['paid_by'] =$this->Setting->select_option_vl(array('setting_value'=>'receipts_paid_by'));
			$arr_data['account'] = $this->Setting->select_option_vl(array('setting_value'=>'receipts_our_bank_account'));
			$this->set('arr_data',$arr_data);
			$this->set('arr_receipt',$arr_receipt);
		}
	}
	public function update_receipt()
	{
		if($this->check_permission('salesinvoices_@_entry_@_edit')){
			if(empty($_POST))
			{
				echo 'error';
				die;
			}
			$id = $this->get_id();
			$receipt_id = $_POST['id'];
			$this->selectModel('Receipt');
			$receipt = $this->Receipt->select_one(array('_id'=>new MongoId($receipt_id)));
			$this->selectModel('Salesinvoice');
			$invoice = $this->Salesinvoice->select_one(array('_id'=>new MongoId($this->get_id())));

			$key = $_POST['key'];

			$total_receipt = 0;
			if(isset($invoice['total_receipt']))$total_receipt = $invoice['total_receipt'];
			$_POST['amount'] = (isset($_POST['amount']) ? (float)str_replace(',', '', $_POST['amount']) : 0);
			$invoice['total_receipt'] = (isset($invoice['total_receipt']) ? (float)$invoice['total_receipt'] : 0);
			$receipt['allocation'][$key]['amount'] = (isset($receipt['allocation'][$key]['amount']) ? (float)$receipt['allocation'][$key]['amount'] : 0);
			$invoice['total_receipt'] = $invoice['total_receipt'] - $receipt['allocation'][$key]['amount'] + $_POST['amount'];

			//Update lại balance và receipt cho SA
			$this->selectModel('Salesaccount');
			if(isset($invoice['company_id']) && is_object($invoice['company_id'])){
				$this->Salesaccount->update_account($invoice['company_id'], array(
													'model' => 'Company',
													'balance' => $total_receipt - $invoice['total_receipt'],
													'receipts' => $invoice['total_receipt'] - $total_receipt,
													));
			}
			$this->Salesinvoice->save($invoice);
			$key = $_POST['key'];
			$receipt['our_bank_account'] = $_POST['our_bank_account'];
			$receipt['notes'] = $_POST['notes'];
			$receipt['receipt_date'] = new MongoDate(strtotime($_POST['receipt_date']));
			$receipt['allocation'][$key]['write_off'] = (isset($_POST['write_off']) ? 1 : 0);
			$receipt['allocation'][$key]['note'] = $_POST['note'];
			$receipt['allocation'][$key]['amount'] = $_POST['amount'];
			if($this->Receipt->save($receipt))
				echo 'ok';
		}
		die;
	}

	public function option_summary_customer_find($type = '')
	{
		if(!$this->check_permission($this->name.'_@_options_@_report_by_customer_summary'))
			$this->error_auth();
		$this->selectModel('Setting');
		$arr_data['salesinvoices_status'] = $this->Setting->select_option_vl(array('setting_value'=>'salesinvoices_status'));
		$arr_data['salesinvoices_type'] = $this->Setting->select_option_vl(array('setting_value'=>'salesinvoices_type'));
		$this->selectModel('Tax');
		$arr_data['salesinvoices_tax'] = $this->Tax->tax_select_list();
		$this->set('arr_data',$arr_data);
		$report_type = 'summary';
		if($type=='area')
            $report_type = 'area_summary';
        $this->set('report_type',$report_type);
	}
	public function customer_report($type='') {
        $arr_data = array();
        if(isset($_GET['print_pdf'])){
            $arr_data = Cache::read('salesinvoices_customer_report_'.$type);
            Cache::delete('salesinvoices_customer_report_'.$type);
        } else {
            if (isset($_POST) && !empty($_POST)) {
                $arr_post = $_POST;
                $arr_post = $this->Common->strip_search($arr_post);
                $arr_where = array('company_id'=>array('$exists' => true,'$ne'=>''), 'deleted'=>array('$ne'=>true));
                if($arr_post['status'] && $arr_post['status'] != '')
                    $arr_where['invoice_status'] = $arr_post['status'];
                //Check loại trừ cancel thì bỏ các status bên dưới
                if ($arr_post['type'] && $arr_post['type'] != '')
                    $arr_where['invoice_type'] = $arr_post['type'];
                if (isset($arr_post['company']) &&$arr_post['company'] != '')
                    $arr_where['company_name'] = new MongoRegex('/' . trim($arr_post['company']) . '/i');
                if (isset($arr_post['contact'])&&$arr_post['contact'] != '')
                    $arr_where['contact_name'] = new MongoRegex('/' . trim($arr_post['contact']) . '/i');
                if (isset($arr_post['job_no'])&&$arr_post['job_no'] != '')
                    $arr_where['job_number'] = new MongoRegex('/' . trim($arr_post['job_no']) . '/i');
                //tim chinh xac ngay
                if (isset($arr_post['date_equals'])&&$arr_post['date_equals'] != '') {
                    $arr_where['invoice_date']['$gte'] = new MongoDate($this->Common->strtotime($arr_post['date_equals'] . '00:00:00'));
                    $arr_where['invoice_date']['$lt'] = new MongoDate($this->Common->strtotime($arr_post['date_equals'] . '23:59:59'));
                } else { //ngay nam trong khoang
                    //neu chi nhap date from
                    if (isset($arr_post['date_from'])&&$arr_post['date_from']!='') {
                        $arr_where['invoice_date']['$gte'] = new MongoDate($this->Common->strtotime($arr_post['date_from'] . '00:00:00'));
                    }
                    //neu chi nhap date to
                    if (isset($arr_post['date_to'])&&$arr_post['date_to']!='') {
                        $arr_where['invoice_date']['$lte'] = new MongoDate($this->Common->strtotime($arr_post['date_to'] . '23:59:59'));
                    }
                }
                if (isset($arr_post['tax'])&&$arr_post['tax'] != '')
                    $arr_where['tax'] = new MongoRegex('/' . $arr_post['tax'] . '/i');
                if (isset($arr_post['employee'])&&$arr_post['employee'] != '')
                    $arr_where['employee'] = new MongoRegex('/' . $arr_post['employee'] . '/i');
                $this->selectModel('Salesinvoice');
                //lay het salesinvoice, voi where nhu tren va lay sum_amount giam dan
                /*$salesinvoice = $this->Salesinvoice->select_all(array(
                    'arr_where' => $arr_where,
                    'arr_order' => array(
                        'invoice_date' => 1
                    ),
                    'arr_field' => array('invoice_type','code','invoice_date','heading','invoice_status','tax','sum_sub_total','company_id','our_rep','company_name')
                ));*/
                $count = $this->Salesinvoice->count($arr_where,array('limit' => 9999999));
                if ($count == 0) {
                    echo 'empty';
                } else if(!$this->request->is('ajax')) {
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
                    else if ($arr_post['report_type'] == 'detailed') {
                        $arr_data = $this->detailed_customer_report($arr_post, $arr_where,$minimum,$product['_id']);
                        Cache::write('salesinvoices_customer_report_detailed_excel', $arr_data['excel']);
                        unset($arr_data['excel']);
                    }
                   	else if ($arr_post['report_type'] == 'area_summary')
                        $arr_data = $this->summary_area_customer_report($arr_post, $arr_where,$minimum,$product['_id']);
                    else if ($arr_post['report_type'] == 'area_detailed') {
                        $arr_data = $this->detailed_area_customer_report($arr_post, $arr_where,$minimum,$product['_id']);
                        Cache::write('salesinvoices_area_report_detailed_excel', $arr_data['excel']);
                        unset($arr_data['excel']);
                    }
	                Cache::write('salesinvoices_customer_report_'.$type, $arr_data);

                }
            }
        }
        if($this->request->is('ajax'))
            die;
        else
            $this->render_pdf($arr_data);
    }
    function get_sum_sub_total($product_id,$origin_minimum, $arr_where){
        $arr_where['deleted'] = false;
        $arr_companies = $this->opm->collection->group(array('company_id' => true), array('companies' => array()), 'function (obj, prev) { prev.companies.push({_id : obj.company_id}); }',array('condition' => $arr_where));
        if( $arr_companies['ok'] && !empty($arr_companies['retval']) ) {
            $arr_companies = $arr_companies['retval'];
        }
        $arr_data = array();
        $arr_query = array();
        foreach($arr_companies as $company){
            $sum = 0;
            $minimum = $origin_minimum;
            $_id = $company['company_id'];
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
            $query = array(
                           'company_id' => new MongoId($_id),
                           'deleted'    => false,
                           'invoice_status'  => array('$ne' => 'Cancelled'),
                        );
            $query = array_merge($arr_where, $query);
            $data = $this->db->command(array(
                'mapreduce'     => 'tb_salesinvoice',
                'map'           => new MongoCode('
                                                function() {
                                                    if( this.invoice_status != "Credit"
                                                    	&& this.invoice_type != "Credit"
                                                    	&& this.sum_sub_total < '.$minimum.') {
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
                'query'         => $query,
                'out'           => array('merge' => 'tb_result')
            ));
            if( isset($data['ok']) && isset($data['counts']['input']) && $data['counts']['input'] ) {
                $result = $this->db->selectCollection('tb_result')->findOne();
                $this->db->tb_sum_result->remove(array('_id' => $result['_id']));
                $sum = isset($result['value']) ? $result['value'] : 0;

            }
            $_id = (string)$_id;
            $arr_data[$_id]['sum_sub_total'] = $sum;
            $arr_data[$_id]['company_name'] = (isset($company['name']) ? $company['name'] : '');
            $arr_data[$_id]['our_rep'] = (isset($company['our_rep']) ? $company['our_rep'] : '');
            $arr_data[$_id]['minimum'] = $minimum;
            $arr_data[$_id]['addresses'] = (isset($company['addresses']) ? $company['addresses'] : '');
            $arr_data[$_id]['addresses_default_key'] = (isset($company['addresses_default_key']) ? $company['addresses_default_key'] : 0);
            $arr_data[$_id]['number_of_salesinvoices'] = $this->opm->count(array_merge($arr_where,array('company_id'=>new MongoId($_id))));
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
        //pr($arr_company);die;/////////////////////////////////////////////////////////////////////////////////////////////////////// gon summary
        foreach ($arr_company as $value) {
            $html .= '
                <tr style="background-color:' . ( $i%2==0 ? '#eeeeee' : '#fdfcfa'). ';">
                     <td class="first content" align="left">' . $value['company_name'] . '</td>
                     <td class="content" align="left">' . $value['our_rep'] . '</td>
                     <td class="content">' . $value['number_of_salesinvoices'] . '</td>
                     <td colspan="3" class="content"  style="text-align: right;" class="end">' . $this->opm->format_currency($value['sum_sub_total']) . '</td>
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
        $arr_data['title'] = array('Customer'=>'text-align: center','Our Rep'=>'text-align: center','No. of SI'=>'text-align: center','Ex. Tax total'=>'text-align: center');
        $arr_data['content'] = $html;
        $arr_data['report_name'] = 'Salesinvoice Report By Customer (Summary)';
        $arr_data['report_file_name'] = 'SI_'.md5(time());
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
            if(!isset($arr_area[$province]['number_of_salesinvoices']))
                $arr_area[$province]['number_of_salesinvoices'] = 0;
            $arr_area[$province]['number_of_salesinvoices'] += $value['number_of_salesinvoices'];
            if(!isset($arr_area[$province]['number_of_companies']))
                $arr_area[$province]['number_of_companies'] = 0;
            $arr_area[$province]['number_of_companies']++;
        }
        ksort($arr_area);
        foreach ($arr_area as $province_name=>$value) {
            $html .= '
                <tr style="background-color:' . ( $i%2==0 ? '#eeeeee' : '#fdfcfa'). ';">
                     <td class="first content" align="left">' . $province_name . '</td>
                     <td class="content">' . $value['number_of_salesinvoices'] . '</td>
                     <td class="content" align="left">' . $value['number_of_companies'] . '</td>

                     <td colspan="3" class="content"  style="text-align: right;" class="end">' . $this->opm->format_currency($value['sum_sub_total']) . '</td>
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
        $arr_data['title'] = array('Province'=>'text-align: left','No. of SI'=>'text-align: left','No. of Companies'=>'text-align: left','Ex. Tax total'=>'text-align: right');
        $arr_data['content'] = $html;
        $arr_data['report_name'] = 'SI Report By Area Customer (Summary)';
        $arr_data['report_file_name'] = 'SI_'.md5(time());
        return $arr_data;
    }
	public function option_detailed_customer_find($type = ''){
		if(!$this->check_permission($this->name.'_@_options_@_report_by_customer_detailed'))
			$this->error_auth();
		$this->selectModel('Setting');
		$arr_data['salesinvoices_status'] = $this->Setting->select_option_vl(array('setting_value'=>'salesinvoices_status'));
		$arr_data['salesinvoices_type'] = $this->Setting->select_option_vl(array('setting_value'=>'salesinvoices_type'));
		$this->selectModel('Tax');
		$arr_data['salesinvoices_tax'] = $this->Tax->tax_select_list();
		$this->set('arr_data',$arr_data);
		$report_type = 'detailed';
        if($type=='area')
            $report_type = 'area_detailed';
        $this->set('report_type',$report_type);
	}
	public function detailed_customer_report($data, $arr_where, $minimum, $product_id) {
        $i = $sum = 0;
        $html = '';
        //Loc ra nhung quotation group theo company id
        $group_company = array();
        $this->selectModel('Company');
        $arr_company = $this->get_sum_sub_total($product_id,$minimum, $arr_where);
        $arr_invoices = $this->opm->collection->group(array('company_id' => true), array('sale_invoices' => array()), 'function (obj, prev) { prev.sale_invoices.push({_id : obj._id, code: obj.code, invoice_type : obj.invoice_type, invoice_date: obj.invoice_date, heading: obj.heading, invoice_status : obj.invoice_status, our_rep : obj.our_rep, sum_sub_total : obj.sum_sub_total, shipping_cost : obj.shipping_cost}); }',array('condition' => $arr_where));
        if( $arr_invoices['ok'] && !empty($arr_invoices['retval']) ) {
            $arr_invoices = $arr_invoices['retval'];
        }
        foreach($arr_company as $company_id => $value) {
            foreach($arr_invoices as $k => $v) {
                if( (string)$v['company_id'] != $company_id ) continue;
                if( !empty($v['sale_invoices']) ) {
                    usort($v['sale_invoices'], function($a, $b){
                        return $a['_id']->getTimestamp() < $b['_id']->getTimestamp();
                    });
                    foreach($v['sale_invoices'] as $invoice) {
                    	$invoice['sub_sub_total'] =  $invoice['sum_sub_total']< $value['minimum']  ? $value['minimum'] : $invoice['sum_sub_total'];
                        $arr_company[$company_id]['sale_invoices'][$invoice['code']] = array(
                                                                                'salesinvoice_code'    =>$invoice['code'],
                                                                                'salesinvoice_type'    =>$invoice['invoice_type'],
                                                                                'salesinvoice_date'    =>$this->opm->format_date($invoice['invoice_date']->sec),
                                                                                'salesinvoice_heading'    =>(isset($invoice['heading']) ? $invoice['heading'] : ''),
                                                                                'salesinvoice_status'    =>$invoice['invoice_status'],
                                                                                'salesinvoice_our_rep'    =>$invoice['our_rep'],
                                                                                'sum_sub_total'     => $invoice['sub_sub_total']
                                                                               );
                    }
                }
                unset($arr_invoices[$k]);
                break;
            }
        }
        $arr_data['excel'] = $arr_company;
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
                        No. of SI
                     </td>
                     <td class="right_text" colspan="3">
                        Group total (ex. tax)
                     </td>
                  </tr>
                  <tr class="bg_2">
                     <td>' . $company['company_name'] . '</td>
                     <td>' . $company['our_rep'] . '</td>
                     <td class="right_text">' . $company['number_of_salesinvoices'] . '</td>
                     <td colspan="3" class="right_text">' . $this->opm->format_currency($company['sum_sub_total']) . '</td>
                  </tr>
               </tbody>
            </table>
            <br />
            <br />
            <div class="line" style="margin-bottom: 10px;"></div>';
            $html .= '<table class="table_content" >
                        <tbody>
                          <tr class="tr_right_none" style="background: #911b12;color: white;height: 29px;line-height: 29px;font-weight: bold;">
                             <td width="7%">
                                SI#
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
            foreach ($company['sale_invoices'] as $key => $invoice) {
            	if( $invoice['salesinvoice_status'] == 'Cancelled' ) {
            		$invoice['sum_sub_total'] = 0;
            	}
                $sum += $invoice['sum_sub_total'];
                $html .= '
                      <tr class="content_asset bg_' . ($i % 2 == 0 ? '1' : '2') . '">
                         <td>' . $invoice['salesinvoice_code'] . '</td>
                         <td>' . $invoice['salesinvoice_type'] . '</td>
                         <td>' . $invoice['salesinvoice_date'] . '</td>
                         <td>' . $invoice['salesinvoice_status'] . '</td>
                         <td class="left_text">' . $invoice['salesinvoice_heading'] . '</td>
                         <td class="left_text">' . $invoice['salesinvoice_our_rep'] . '</td>
                         <td colspan="3" class="right_text">' . $this->opm->format_currency((float)$invoice['sum_sub_total']) . '</td>
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
        $arr_data['excel_url'] = URL.'/salesinvoices/customer_report_excel';
        $arr_data['report_name'] = 'SI Report By Customer (Detailed)';
        $arr_data['report_file_name'] = 'QT_'.md5(time());
        return $arr_data;
    }

    public function customer_report_excel()
    {
        $arr_data = Cache::read('salesinvoices_customer_report_detailed_excel');
        Cache::delete('salesinvoices_customer_report_detailed_excel');
        if(!$arr_data){
            echo 'No data';die;
        }
        App::import('Vendor', 'phpexcel/PHPExcel');
        $objPHPExcel = new PHPExcel();
        // Set document properties
        $objPHPExcel->getProperties()->setCreator('')
                                     ->setLastModifiedBy('')
                                     ->setTitle('Customer Report')
                                     ->setSubject('Customer Report')
                                     ->setDescription('Customer Report')
                                     ->setKeywords('Customer Report')
                                     ->setCategory('Customer Report');
        $worksheet = $objPHPExcel->getActiveSheet();
        $i = 2;
        $worksheet->setCellValue("A$i",'#')
                        ->setCellValue("B$i",'Type')
                        ->setCellValue("C$i",'Date')
                        ->setCellValue("D$i",'Status')
                        ->setCellValue("E$i",'Heading')
                        ->setCellValue("F$i",'Our Rep')
                        ->setCellValue("G$i",'Ex. Tax total');
        $styleArray = array(
            'fill' => array(
                'type' => PHPExcel_Style_Fill::FILL_SOLID,
                'color' => array('rgb' => '538DD5')
            ),
            'font'  => array(
                'size'  => 12,
                'name'  => 'Century Gothic',
                'bold'  => true,
                'color' => array('rgb' => 'FFFFFF'),
            )
        );
        $worksheet->getStyle("A$i:G$i")->applyFromArray($styleArray);
        $worksheet->freezePane('H3');
        foreach($arr_data as $company){
            $i++;
            $worksheet->setCellValue("A$i", $company['company_name'])
                        ->mergeCells("A$i:D$i")
                        ->setCellValue("E$i", $company['our_rep'])
                        ->setCellValue("F$i", 'No. of SI: '.$company['number_of_salesinvoices'])
                        ->setCellValue("G$i", $company['sum_sub_total']);
            $worksheet->getStyle("A$i:G$i")->applyFromArray(array(
                                                            'fill' => array(
                                                                'type' => PHPExcel_Style_Fill::FILL_SOLID,
                                                                'color' => array('rgb' => '4BACC6')
                                                            ),
                                                            'font'  => array(
                                                                'bold'  => true,
                                                                'color' => array('rgb' => 'FFFFFF'),
                                                            )
                                                        ));
            $from = $i+1;
            foreach($company['sale_invoices'] as $invoice) {
                if( $invoice['salesinvoice_status'] == 'Cancelled' ) {
                    $invoice['sum_sub_total'] = 0;
                }
                $i++;
                $worksheet->setCellValue("A$i", $invoice['salesinvoice_code'])
                                ->setCellValue("B$i", $invoice['salesinvoice_type'])
                                ->setCellValue("C$i", $invoice['salesinvoice_date'])
                                ->setCellValue("D$i", $invoice['salesinvoice_status'])
                                ->setCellValue("E$i", $invoice['salesinvoice_heading'])
                                ->setCellValue("F$i", $invoice['salesinvoice_our_rep'])
                                ->setCellValue("G$i", $invoice['sum_sub_total']);
            }
            $i++;
            $worksheet->setCellValue("A$i", $company['number_of_salesinvoices'].' record(s) listed.')
                                ->setCellValue("F$i", 'Total')
                                ->setCellValue("G$i", "=SUM(G$from:G".($i-1).')')
                                ->mergeCells("A$i:E$i");
            $worksheet->getStyle("A$i:G$i")->applyFromArray(array(
                                                            'font'  => array(
                                                                'bold'  => true,
                                                            )
                                                        ));
            $i++;
            $worksheet->mergeCells("A$i:J$i");
        }
        $worksheet->getStyle("G3:G$i")->getNumberFormat()->setFormatCode("#,##0.00");
        $worksheet->getStyle("A3:G$i")->applyFromArray(array(
                                                            'borders' => array(
                                                               'allborders' => array(
                                                                   'style' => PHPExcel_Style_Border::BORDER_THIN
                                                                )
                                                            ),
                                                            'font'  => array(
                                                                'size'  => 11,
                                                                'name'  => 'Century Gothic',
                                                            )
                                                        ));
        $worksheet->getStyle("C2:C$i")->applyFromArray(array(
                                                            'alignment'  => array(
                                                                'horizontal'  => PHPExcel_Style_Alignment::HORIZONTAL_LEFT,
                                                            )
                                                        ));
        $worksheet->getStyle("G2:G$i")->applyFromArray(array(
                                                            'alignment'  => array(
                                                                'horizontal'  => PHPExcel_Style_Alignment::HORIZONTAL_RIGHT,
                                                            )
                                                        ));
        for($i = 'A'; $i !== 'H'; $i++){
            $worksheet->getColumnDimension($i)
                            ->setAutoSize(true);
        }
        $objPHPExcel->setActiveSheetIndex(0);
        $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
        $objWriter->save(APP.DS.'webroot'.DS.'upload'.DS.'customer_report.xlsx');
        $this->redirect('/upload/customer_report.xlsx');
        die;
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
            if(!isset($arr_area[$province]['number_of_salesinvoices']))
                $arr_area[$province]['number_of_salesinvoices'] = 0;
            $arr_area[$province]['number_of_salesinvoices'] += $value['number_of_salesinvoices'];
            $arr_area[$province]['companies'][] = $value;
        }
        $arr_invoices = $this->opm->collection->group(array('company_id' => true), array('sale_invoices' => array()), 'function (obj, prev) { prev.sale_invoices.push({_id : obj._id, code: obj.code, company_name: obj.company_name, invoice_type : obj.invoice_type, invoice_date: obj.invoice_date, heading: obj.heading, invoice_status : obj.invoice_status, our_rep : obj.our_rep, sum_sub_total : obj.sum_sub_total, shipping_cost : obj.shipping_cost}); }',array('condition' => $arr_where));
        if( $arr_invoices['ok'] && !empty($arr_invoices['retval']) ) {
            $arr_invoices = $arr_invoices['retval'];
        } else {
        	$arr_invoices = array();
        }
        foreach($arr_area as $province=>$value){
            foreach($value['companies'] as $company){
                $company_id = $company['company_id'];
                foreach($arr_invoices as $k => $v) {
                    if( (string)$v['company_id'] != $company_id ) continue;
                    if( !empty($v['sale_invoices']) ) {
                        foreach($v['sale_invoices'] as $salesinvoice) {
                        	$salesinvoice['sum_sub_total'] = $salesinvoice['sum_sub_total'] < $company['minimum']  ? $company['minimum'] : $salesinvoice['sum_sub_total'];
                            $arr_area[$province]['salesinvoices'][$salesinvoice['code']] = array(
                            												'_id'				=>$salesinvoice['_id'],
                                                                    		'company_name'      =>(isset($salesinvoice['company_name']) ? $salesinvoice['company_name'] : ''),
                                                                            'salesinvoice_code'    =>$salesinvoice['code'],
                                                                            'salesinvoice_type'    =>$salesinvoice['invoice_type'],
                                                                            'salesinvoice_date'    =>$this->opm->format_date($salesinvoice['invoice_date']->sec),
                                                                            'salesinvoice_status'    =>$salesinvoice['invoice_status'],
                                                                            'salesinvoice_our_rep'    =>$salesinvoice['our_rep'],
                                                                            'sum_sub_total'     => $salesinvoice['sum_sub_total']
                                                                           );
                        }
                    }
                    unset($arr_invoices[$k]);
                    break;
                }
            }
        }
        $arr_data['excel'] = $arr_area;
        foreach ($arr_area as $province => $value) {
            $html .= '
            <table class="table_content">
               <tbody>
                  <tr class="tr_right_none" style="background: #911b12;color: white;height: 29px;line-height: 29px;font-weight: bold;">
                     <td width="35%">
                        Province
                     </td>
                     <td class="right_text" width="15%">
                        No. of SI
                     </td>
                     <td class="right_text" colspan="3">
                        Group total (ex. tax)
                     </td>
                  </tr>
                  <tr class="bg_2">
                     <td>' . $province . '</td>
                     <td class="right_text">' . $value['number_of_salesinvoices'] . '</td>
                     <td colspan="3" class="right_text">' . $this->opm->format_currency($value['sum_sub_total']) . '</td>
                  </tr>
               </tbody>
            </table>';
            $html .= '<table class="table_content" >
                        <tbody>
                          <tr class="tr_right_none" style="background: #911b12;color: white;height: 29px;line-height: 29px;font-weight: bold;">
                             <td width="10%">
                                SI#
                             </td>
                             <td width="10%">
                                Type
                             </td>
                             <td width="25%">
                                Company
                             </td>
                             <td width="15%">
                                Date
                             </td>
                             <td width="15%">
                                Status
                             </td>
                             <td width="15%">
                                Our Rep
                             </td>
                             <td class="right_text" colspan="3" width="20%">
                                Ex. Tax total
                             </td>
                          </tr>';
            $i = 0;
            $sum = 0;
            usort($value['salesinvoices'], function($a, $b){
            	return $a['_id']->getTimestamp() < $b['_id']->getTimestamp();
            });
            foreach ($value['salesinvoices'] as $key => $salesinvoice) {
            	if(  $salesinvoice['salesinvoice_status'] == 'Cancelled' ){
            		$salesinvoice['sum_sub_total'] = 0;
            	}
                $sum += $salesinvoice['sum_sub_total'];
                $html .= '
                      <tr class="content_asset bg_' . ($i % 2 == 0 ? '1' : '2') . '">
                         <td>' . $salesinvoice['salesinvoice_code'] . '</td>
                         <td>' . $salesinvoice['salesinvoice_type'] . '</td>
                         <td>' . $salesinvoice['company_name'] . '</td>
                         <td>' . $salesinvoice['salesinvoice_date'] . '</td>
                         <td>' . $salesinvoice['salesinvoice_status'] . '</td>
                         <td class="left_text">' . $salesinvoice['salesinvoice_our_rep'] . '</td>
                         <td colspan="3" class="right_text">' . $this->opm->format_currency((float)$salesinvoice['sum_sub_total']) . '</td>
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
        $arr_data['excel_url'] = URL.'/salesinvoices/area_report_excel';
        $arr_data['report_name'] = 'SI Report By Area Customer (Detailed)';
        $arr_data['report_file_name'] = 'SO_'.md5(time());
        return $arr_data;
    }

    public function area_report_excel()
    {
        $arr_data = Cache::read('salesinvoices_area_report_detailed_excel');
        Cache::delete('salesinvoices_area_report_detailed_excel');
        if(!$arr_data){
            echo 'No data';die;
        }
        App::import('Vendor', 'phpexcel/PHPExcel');
        $objPHPExcel = new PHPExcel();
        // Set document properties
        $objPHPExcel->getProperties()->setCreator('')
                                     ->setLastModifiedBy('')
                                     ->setTitle('Area Report')
                                     ->setSubject('Area Report')
                                     ->setDescription('Area Report')
                                     ->setKeywords('Area Report')
                                     ->setCategory('Area Report');
        $worksheet = $objPHPExcel->getActiveSheet();
        $i = 2;
        $worksheet->setCellValue("A$i",'#')
                        ->setCellValue("B$i",'Type')
                        ->setCellValue("C$i",'Company')
                        ->setCellValue("D$i",'Date')
                        ->setCellValue("E$i",'Status')
                        ->setCellValue("F$i",'Our Rep')
                        ->setCellValue("G$i",'Ex. Tax total');
        $styleArray = array(
            'fill' => array(
                'type' => PHPExcel_Style_Fill::FILL_SOLID,
                'color' => array('rgb' => '538DD5')
            ),
            'font'  => array(
                'size'  => 12,
                'name'  => 'Century Gothic',
                'bold'  => true,
                'color' => array('rgb' => 'FFFFFF'),
            )
        );
        $worksheet->getStyle("A$i:G$i")->applyFromArray($styleArray);
        $worksheet->freezePane('H3');
        foreach($arr_data as $province => $data){
            $i++;
            $worksheet->setCellValue("A$i", $province)
                        ->mergeCells("A$i:E$i")
                        ->setCellValue("F$i", 'No. of SI: '.$data['number_of_salesinvoices'])
                        ->setCellValue("G$i", $data['sum_sub_total']);
            $worksheet->getStyle("A$i:G$i")->applyFromArray(array(
                                                            'fill' => array(
                                                                'type' => PHPExcel_Style_Fill::FILL_SOLID,
                                                                'color' => array('rgb' => '4BACC6')
                                                            ),
                                                            'font'  => array(
                                                                'bold'  => true,
                                                                'color' => array('rgb' => 'FFFFFF'),
                                                            )
                                                        ));
            $from = $i+1;
            usort($data['salesinvoices'], function($a, $b){
            	return $a['_id']->getTimestamp() < $b['_id']->getTimestamp();
            });
            foreach($data['salesinvoices'] as $invoice) {
                if( $invoice['salesinvoice_status'] == 'Cancelled' ) {
                    $invoice['sum_sub_total'] = 0;
                }
                $i++;
                $worksheet->setCellValue("A$i", $invoice['salesinvoice_code'])
                                ->setCellValue("B$i", $invoice['salesinvoice_type'])
                                ->setCellValue("C$i", $invoice['company_name'])
                                ->setCellValue("D$i", $invoice['salesinvoice_date'])
                                ->setCellValue("E$i", $invoice['salesinvoice_status'])
                                ->setCellValue("F$i", $invoice['salesinvoice_our_rep'])
                                ->setCellValue("G$i", $invoice['sum_sub_total']);
            }
            $i++;
            $worksheet->setCellValue("A$i", $data['number_of_salesinvoices'].' record(s) listed.')
                                ->setCellValue("F$i", 'Total')
                                ->setCellValue("G$i", "=SUM(G$from:G".($i-1).')')
                                ->mergeCells("A$i:E$i");
            $worksheet->getStyle("A$i:G$i")->applyFromArray(array(
                                                            'font'  => array(
                                                                'bold'  => true,
                                                            )
                                                        ));
            $i++;
            $worksheet->mergeCells("A$i:J$i");
        }
        $worksheet->getStyle("G3:G$i")->getNumberFormat()->setFormatCode("#,##0.00");
        $worksheet->getStyle("A3:G$i")->applyFromArray(array(
                                                            'borders' => array(
                                                               'allborders' => array(
                                                                   'style' => PHPExcel_Style_Border::BORDER_THIN
                                                                )
                                                            ),
                                                            'font'  => array(
                                                                'size'  => 11,
                                                                'name'  => 'Century Gothic',
                                                            )
                                                        ));
        $worksheet->getStyle("C2:C$i")->applyFromArray(array(
                                                            'alignment'  => array(
                                                                'horizontal'  => PHPExcel_Style_Alignment::HORIZONTAL_LEFT,
                                                            )
                                                        ));
        $worksheet->getStyle("G2:G$i")->applyFromArray(array(
                                                            'alignment'  => array(
                                                                'horizontal'  => PHPExcel_Style_Alignment::HORIZONTAL_RIGHT,
                                                            )
                                                        ));
        for($i = 'A'; $i !== 'H'; $i++){
            $worksheet->getColumnDimension($i)
                            ->setAutoSize(true);
        }
        $objPHPExcel->setActiveSheetIndex(0);
        $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
        $objWriter->save(APP.DS.'webroot'.DS.'upload'.DS.'area_report.xlsx');
        $this->redirect('/upload/area_report.xlsx');
        die;
    }


	public function option_summary_highest_customer_find(){
		if(!$this->check_permission($this->name.'_@_options_@_report_by_customer_summary_highest_first'));
			$this->error_auth();
		$arr_data['salesinvoices_status'] = $this->Setting->select_option_vl(array('setting_value'=>'salesinvoices_status'));
		$arr_data['salesinvoices_type'] = $this->Setting->select_option_vl(array('setting_value'=>'salesinvoices_type'));
		$this->selectModel('Tax');
		$arr_data['salesinvoices_tax'] = $this->Tax->tax_select_list();

		$this->set('arr_data',$arr_data);
	}
	public function option_detailed_tax_customer_find(){
		if(!$this->check_permission($this->name.'_@_options_@_report_by_customer_detailed_tax_amounts'))
			$this->error_auth();
		$arr_data['salesinvoices_status'] = $this->Setting->select_option_vl(array('setting_value'=>'salesinvoices_status'));
		$arr_data['salesinvoices_type'] = $this->Setting->select_option_vl(array('setting_value'=>'salesinvoices_type'));
		$this->selectModel('Tax');
		$arr_data['salesinvoices_tax'] = $this->Tax->tax_select_list();

		$this->set('arr_data',$arr_data);
	}
	public function check_exist_customer()
	{
		$data = $_POST;
		parse_str($data['data'],$data);
		if(!empty($data))
		{
			$data = $this->Common->strip_search($data);
			$arr_where = array();
			$arr_where['deleted'] = false;
			if($data['status'])
				$arr_where['invoice_status'] = $data['status'];
			if($data['type'])
				$arr_where['invoice_type'] = $data['type'];
			if(isset($data['company'])&&$data['company']!='')
				$arr_where['company_name'] = new MongoRegex('/'.trim($data['company']).'/i');
			if(isset($data['contact'])&&$data['contact']!='')
				$arr_where['contact_name'] = new MongoRegex('/'.trim($data['contact']).'/i');
			if(isset($data['job_no'])&&$data['job_no']!='')
				$arr_where['job_number'] = new MongoRegex('/'.trim($data['job_no']).'/i');
			//tim chinh xac ngay
			if($data['date_equals']!=''){
				$date_equals = $data['date_equals'];
				$date_equals = new MongoDate(strtotime(@date('Y-m-d',strtotime($date_equals))));
				$date_equals_to = new MongoDate($date_equals->sec + DAY -1);
				$arr_where['invoice_date']['$gte'] = $date_equals;
				$arr_where['invoice_date']['$lt'] = $date_equals_to;
			}
			//ngay nam trong khoang
			else if($data['date_equals'] == ''){
				//neu chi nhap date from
				if($date_from = $data['date_from']){
					$date_from = new MongoDate(strtotime(@date('Y-m-d',strtotime(@$date_from))));
					$arr_where['invoice_date']['$gte'] = $date_from;
				}
				//neu chi nhap date to
				if($date_to = $data['date_to']){
					$date_to = new MongoDate(strtotime(@date('Y-m-d',strtotime(@$date_to))));
					$date_to = new MongoDate($date_to->sec + DAY -1);
					$arr_where['invoice_date']['$lte'] = $date_to;
				}
			}
			if(@$data['employee']!=''){
				$arr_where['$or'][]['our_rep'] = new MongoRegex('/'.trim($data['employee']).'/i');
				$arr_where['$or'][]['our_csr'] = new MongoRegex('/'.trim($data['employee']).'/i');
			}
			$this->selectModel('Salesinvoice');
			if(isset($data['highest'])&&$data['highest']==true){
				//lay het salesinvoice, voi where nhu tren va lay sum_sub_total giam dan
				$salesinvoice = $this->Salesinvoice->select_all(array(
					'arr_where'=>$arr_where,
					'arr_order'=>array(
									'sum_sub_total'=>-1
									)
				));
			}
			else{
				//lay het salesinvoice, voi where nhu tren va lay ten company giam dan
				$salesinvoice = $this->Salesinvoice->select_all(array(
					'arr_where'=>$arr_where,
					'arr_order'=>array(
									'company_name'=>1
									),
					'arr_field'=>array('_id','code','company_id','company_name','invoice_date','invoice_type','invoice_status','sum_sub_total','name','sum_tax_total','sum_tax')
				));
			}
			if($salesinvoice->count()==0){
				echo 'empty';
				die;
			}
			else
			{
				$url = '';
				if($data['report_type']=='summary')
					$url = $this->summary_customer_report_pdf($salesinvoice,$data);
				else if($data['report_type'] == 'detailed')
				{
					if(isset($data['tax'])&&$data['tax']==true)
						$url = $this->detailed_tax_customer_report_pdf($salesinvoice,$data);
					else
						$url = $this->detailed_customer_report_pdf($salesinvoice,$data);
				}
				else
					$url = $this->summary_customer_report_pdf($salesinvoice,$data);

				echo URL.$url;
				die;
			}
		}
	}
	public function summary_customer_report_pdf($salesinvoice,$data)
	{
		//--------------------------------------
		$sum = 0;
		$html_loop = '';
		$i = 0;
		$color = '';
		$arr_status = array();
		$arr_company = array();
		$this->selectModel('Company');
		foreach($salesinvoice as $value)
		{
			if($value['company_id'])
			{
				$company = $this->Company->select_one(array('_id'=> new MongoId($value['company_id'])));
				$arr_company[(string)$value['company_id']]['company_name'] = $value['company_name'];
				$arr_company[(string)$value['company_id']]['our_rep'] = @$company['our_rep'];
				if(!isset($arr_company[(string)$value['company_id']]['number_of_salesorder']))
					$arr_company[(string)$value['company_id']]['number_of_salesorder'] = 0;
				$arr_company[(string)$value['company_id']]['number_of_salesorder']++;
				if(!isset($arr_company[(string)$value['company_id']]['sum_sub_total']))
					$arr_company[(string)$value['company_id']]['sum_sub_total'] = 0;
				$arr_company[(string)$value['company_id']]['sum_sub_total'] += (@$value['sum_sub_total']!=''?$value['sum_sub_total']: 0);
			}
		}
		$html_loop = '
				<table cellpadding="3" cellspacing="0" class="maintb">
				  <tr>
					 <td width="30%" class="first top">
						Customer
					 </td>
					 <td width="20%" class="top">
						Our Rep
					 </td>
					 <td width="20%" class="top">
						No. of SI
					 </td>
					<td colspan="3" width="30%" class="end top">
					   Ex. Tax total
					 </td>
				  </tr>
			';
		foreach($arr_company as $value)
		{
			$color = '#fdfcfa';
			if($i%2==0)
				$color = '#eeeeee';
			$html_loop .= '
				<tr style="background-color:'.$color.';">
					 <td class="first content" align="left">'.@$value['company_name'].'</td>
					 <td class="content" align="left">'.@$value['our_rep'].'</td>
					 <td class="content">'.@$value['number_of_salesorder'].'</td>
					 <td colspan="3" class="content"  style="text-align: right;" class="end">'.$this->opm->format_currency($value['sum_sub_total']).'</td>
				</tr>
			';
			$sum += (@$value['sum_sub_total']? $value['sum_sub_total']: 0);
			$i++;
		}
		$color = '#fdfcfa';
		if($i%2==0)
			$color = '#eeeeee';
		$html_loop .= '
					<tr style="background-color:'.$color.';">
						 <td align="left" class="first bottom">'.$i.' record(s) listed</td>
						 <td class="bottom">&nbsp;</td>
						 <td class="bottom">&nbsp;</td>
						 <td align="left" class="bottom"><span style="font-weight:bold; padding-left:20px">Total:</span></td>
						 <td colspan="2" style="text-align: right;" class="content bottom">'.$this->opm->format_currency($sum).'</td>
					</tr>
				</table>
				';
		//========================================
		$pdf['current_time'] = date('h:i a m/d/Y');
		$pdf['title'] = '<span style="color:#b32017">S</span>ales <span style="color:#b32017">O</span>der <span style="color:#b32017">R</span>eport <span style="color:#b32017">B</span>y <span style="color:#b32017">C</span>ustomer<br /> (Summary)';
		$this->layout = 'pdf';
			//set header
		$pdf['logo_link'] = 'img/logo_anvy.jpg';
		$pdf['company_address'] = '3145 - 5th Ave NE<br />Calgary  AB  T2A  6A3<br />';
		$pdf['heading'] = $data['heading'];
		if(isset($data['date_equals'])&&$data['date_equals']!='')
		{
			$pdf['date_equals'] = $data['date_equals'];
		}
		else
		{
			if(isset($data['date_from'])&&$data['date_from']!='')
				$pdf['date_from']  = $data['date_from'];
			if(isset($data['date_to'])&&$data['date_to']!='')
				$pdf['date_to'] = $data['date_to'];
		}
		$pdf['html_loop'] = $html_loop;
		$pdf['filename'] = 'SO_'.md5($pdf['current_time']);
		//pr($html_loop);die;
		$this->report_pdf($pdf);
		return '/upload/'.$pdf['filename'].'.pdf';
	}
	public function detailed_customer_report_pdf($salesinvoice,$data)
	{

		$sum = 0;
		$html_loop = '';
		$i = 0;
		$color = '';
		$group_company = array();
		$this->selectModel('Company');
		foreach($salesinvoice as $value)
		{
			if($value['company_id'])
			{
				$company = $this->Company->select_one(array('_id'=> new MongoId($value['company_id'])));
				//Group chung
				$group_company[(string)$value['company_id']]['company_name'] = @$value['company_name'];
				$group_company[(string)$value['company_id']]['our_rep'] = @$company['our_rep'];
				if(!isset($group_company[(string)$value['company_id']]['number_of_salesinvoices']))
					$group_company[(string)$value['company_id']]['number_of_salesinvoices'] = 0;
				$group_company[(string)$value['company_id']]['number_of_salesinvoices'] ++;
				if(!isset($group_company[(string)$value['company_id']]['total']))
					$group_company[(string)$value['company_id']]['total'] = 0;
				$group_company[(string)$value['company_id']]['total'] += (isset($value['sum_sub_total'])&&$value['sum_sub_total'] != '' ? $value['sum_sub_total']: 0);
				//Tách theo SI code
				$group_company[(string)$value['company_id']]['salesinvoice'][$value['code']]['salesinvoice_code'] = $value['code'];
				$group_company[(string)$value['company_id']]['salesinvoice'][$value['code']]['salesinvoice_type'] = $value['invoice_type'];
				$group_company[(string)$value['company_id']]['salesinvoice'][$value['code']]['salesinvoice_date'] = $this->opm->format_date($value['invoice_date']);
				$group_company[(string)$value['company_id']]['salesinvoice'][$value['code']]['salesinvoice_heading'] = (isset($value['name']) ? $value['name'] : '');
				$group_company[(string)$value['company_id']]['salesinvoice'][$value['code']]['salesinvoice_status'] = $value['invoice_status'];
				$group_company[(string)$value['company_id']]['salesinvoice'][$value['code']]['sum_sub_total'] = (@$value['sum_sub_total']!='' ? $value['sum_sub_total'] : 0);

			}
		}
		foreach($group_company as $key=>$company)
		{
			$html_loop .= '
			<table cellpadding="3" cellspacing="0" class="maintb">
			   <tbody>
				  <tr>
					 <td width="35%" class="first top">
						Company
					 </td>
					 <td width="25%" class="top">
						Contact
					 </td>
					 <td width="15%" class="top">
						No. of SI
					 </td>
					 <td colspan="3" width="25%" class="top">
						Group total (ex. tax)
					 </td>
				  </tr>
				  <tr style="background-color:#eeeeee;">
					 <td class="first content" align="left">'.@$company['company_name'].'</td>
					 <td class="content">'.$company['our_rep'].'</td>
					 <td class="content">'.$company['number_of_salesinvoices'].'</td>
					 <td colspan="3" class="content" style="text-align: right;">'.$this->opm->format_currency($company['total']).'</td>
				  </tr>
			   </tbody>
			</table>
			<div class="option"></div><br />';
			$html_loop .= '<table cellpadding="3" cellspacing="0" class="maintb">
						<tbody>
						  <tr>
							 <td width="10%" class="first top">
								SI#
							 </td>
							 <td width="15%" class="top">
								Type
							 </td>
							 <td width="15%" class="top">
								Date
							 </td>
							 <td width="15%" class="top">
								Status
							 </td>
							 <td width="25%" class="top">
								Heading
							 </td>
							 <td colspan="3" width="20%" class="end top">
								Ex. Tax total
							 </td>
						  </tr>';

			$i = 0;
			if(is_array($company))
			{
				foreach($company['salesinvoice'] as $key=>$value)
				{
					$color = '#fdfcfa';
					if($i%2==0)
						$color = '#eeeeee';
					$html_loop .= '
						  <tr style="background-color:'.$color.';">
							 <td class="first content">'.$value['salesinvoice_code'].'</td>
							 <td class="content">'.$value['salesinvoice_type'].'</td>
							 <td class="content">'.$value['salesinvoice_date'].'</td>
							 <td class="content">'.$value['salesinvoice_status'].'</td>
							 <td class="content" align="left">'.@$value['salesinvoice_heading'].'</td>
							 <td colspan="3" class="content"  style="text-align: right;" class="end">'.$this->opm->format_currency($value['sum_sub_total']).'</td>
						  </tr>';
					$i++;
				}
			}
			$color = '#fdfcfa';
			if($i%2==0)
				$color = '#eeeeee';
			$html_loop .= '
							<tr style="background-color:'.$color.'">
							 <td colspan="2" align="left" class="first bottom">'.$i.' record(s) listed</td>
							 <td class="bottom">&nbsp;</td>
							 <td class="bottom">&nbsp;</td>
							 <td class="bottom">&nbsp;</td>
							 <td  align="left" class="bottom"><span style="font-weight:bold; padding-left:20px">Total:</span></td>
							 <td colspan="2" style="text-align: right;" class="content bottom">'.$this->opm->format_currency($company['total']).'</td>
						  </tr>
						</tbody>
					</table>
					<br />
					<div style="border-bottom: 1px dashed #9f9f9f; height:1px; clear:both"></div>
					<br />';

		}
		//========================================
		$pdf['current_time'] = date('h:i a m/d/Y');
		$pdf['title'] = '<span style="color:#b32017">S</span>ales <span style="color:#b32017">I</span>voice <span style="color:#b32017">R</span>eport <span style="color:#b32017">B</span>y <span style="color:#b32017">C</span>ustomer<br /> (Detail)';
		$this->layout = 'pdf';
			//set header
		$pdf['logo_link'] = 'img/logo_anvy.jpg';
		$pdf['company_address'] = '3145 - 5th Ave NE<br />Calgary  AB  T2A  6A3<br />';
		$pdf['heading'] = $data['heading'];
		if(isset($data['date_equals'])&&$data['date_equals']!='')
		{
			$pdf['date_equals'] = $data['date_equals'];
		}
		else
		{
			if(isset($data['date_from'])&&$data['date_from']!='')
				$pdf['date_from']  = $data['date_from'];
			if(isset($data['date_to'])&&$data['date_to']!='')
				$pdf['date_to'] = $data['date_to'];
		}
		$pdf['html_loop'] = $html_loop;
		$pdf['filename'] = 'SI_'.md5($pdf['current_time']);

		$this->report_pdf($pdf);
		return '/upload/'.$pdf['filename'].'.pdf';

	}
	public function detailed_tax_customer_report_pdf($salesinvoice,$data)
	{

		$sum = 0;
		$html_loop = '';
		$i = 0;
		$color = '';
		$group_company = array();
		$this->selectModel('Company');
		foreach($salesinvoice as $value)
		{
			if($value['company_id'])
			{
				$company = $this->Company->select_one(array('_id'=> new MongoId($value['company_id'])));
				//Group chung
				$group_company[(string)$value['company_id']]['company_name'] = @$value['company_name'];
				$group_company[(string)$value['company_id']]['our_rep'] = @$company['our_rep'];
				if(!isset($group_company[(string)$value['company_id']]['number_of_salesinvoices']))
					$group_company[(string)$value['company_id']]['number_of_salesinvoices'] = 0;
				$group_company[(string)$value['company_id']]['number_of_salesinvoices'] ++;
				if(!isset($group_company[(string)$value['company_id']]['total']))
					$group_company[(string)$value['company_id']]['total'] = 0;
				$group_company[(string)$value['company_id']]['total'] += (isset($value['sum_sub_total'])&&$value['sum_sub_total'] != '' ? $value['sum_sub_total']: 0);
				if(!isset($group_company[(string)$value['company_id']]['total_with_tax']))
					$group_company[(string)$value['company_id']]['total_with_tax'] = 0;
				$group_company[(string)$value['company_id']]['total_with_tax'] += ($value['sum_amount'] != '' ? $value['sum_amount']: 0);
				if(!isset($group_company[(string)$value['company_id']]['total_tax']))
					$group_company[(string)$value['company_id']]['total_tax'] = 0;
				$group_company[(string)$value['company_id']]['total_tax'] += (isset($value['sum_tax'])&&$value['sum_tax'] != '' ? $value['sum_tax']: 0);
				//Tách theo SI code
				$group_company[(string)$value['company_id']]['salesinvoice'][$value['code']]['salesinvoice_code'] = $value['code'];
				$group_company[(string)$value['company_id']]['salesinvoice'][$value['code']]['salesinvoice_type'] = $value['invoice_type'];
				$group_company[(string)$value['company_id']]['salesinvoice'][$value['code']]['salesinvoice_date'] = $this->opm->format_date($value['invoice_date']);
				$group_company[(string)$value['company_id']]['salesinvoice'][$value['code']]['salesinvoice_heading'] = (isset($value['name']) ? $value['name'] : '');
				$group_company[(string)$value['company_id']]['salesinvoice'][$value['code']]['salesinvoice_status'] = $value['invoice_status'];
				$group_company[(string)$value['company_id']]['salesinvoice'][$value['code']]['sum_tax'] = (isset($value['sum_tax'])&&$value['sum_tax']!= '' ? $value['sum_tax'] : 0);
				$group_company[(string)$value['company_id']]['salesinvoice'][$value['code']]['sum_sub_total'] = (isset($value['sum_sub_total'])&&$value['sum_sub_total']!='' ? $value['sum_sub_total'] : 0);
				$group_company[(string)$value['company_id']]['salesinvoice'][$value['code']]['sum_amount'] = ($value['sum_amount']!='' ? $value['sum_amount'] : 0);

			}
		}
		foreach($group_company as $key=>$company)
		{
			$html_loop .= '
			<table cellpadding="3" cellspacing="0" class="maintb">
			   <tbody>
				  <tr>
					 <td width="25%" class="first top">
						Company
					 </td>
					 <td width="20%" class="top">
						Contact
					 </td>
					 <td width="15%" class="top">
						No. of SI
					 </td>
					 <td width="20%" class="top">
						Group total (ex. tax)
					 </td>
					 <td colspan="3" width="20%" class="top">
						Group total (inc. tax)
					 </td>
				  </tr>
				  <tr style="background-color:#eeeeee;">
					 <td class="first content" align="left">'.@$company['company_name'].'</td>
					 <td class="content">'.$company['our_rep'].'</td>
					 <td class="content">'.$company['number_of_salesinvoices'].'</td>
					 <td class="content" style="text-align: right;">'.$this->opm->format_currency($company['total']).'</td>
					 <td colspan="3" class="content" style="text-align: right;">'.$this->opm->format_currency($company['total_with_tax']).'</td>
				  </tr>
			   </tbody>
			</table>
			<div class="option"></div><br />';
			$html_loop .= '<table cellpadding="3" cellspacing="0" class="maintb">
						<tbody>
						  <tr>
							 <td width="10%" class="first top">
								SI#
							 </td>
							 <td width="15%" class="top">
								Type
							 </td>
							 <td width="15%" class="top">
								Date
							 </td>
							 <td width="10%" class="top">
								Status
							 </td>
							 <td width="15%" class="top">
								Ex. Tax total
							 </td>
							 <td width="15%" class="top">
								Tax
							 </td>
							 <td width="20%" class="end top">
								Total inc. Tax
							 </td>
						  </tr>';

			$i = 0;
			if(is_array($company))
			{
				foreach($company['salesinvoice'] as $key=>$value)
				{
					$color = '#fdfcfa';
					if($i%2==0)
						$color = '#eeeeee';
					$html_loop .= '
						  <tr style="background-color:'.$color.';">
							 <td class="first content">'.$value['salesinvoice_code'].'</td>
							 <td class="content">'.$value['salesinvoice_type'].'</td>
							 <td class="content">'.$value['salesinvoice_date'].'</td>
							 <td class="content">'.$value['salesinvoice_status'].'</td>
							 <td class="content"  style="text-align: right;">'.$this->opm->format_currency($value['sum_sub_total']).'</td>
							 <td class="content"  style="text-align: right;">'.$this->opm->format_currency($value['sum_tax']).'</td>
							 <td class="content"  style="text-align: right;" class="end">'.$this->opm->format_currency($value['sum_amount']).'</td>
						  </tr>';
					$i++;
				}
			}
			$color = '#fdfcfa';
			if($i%2==0)
				$color = '#eeeeee';
			$html_loop .= '
							<tr style="background-color:'.$color.'">
							 <td colspan="2" align="left" class="first bottom">'.$i.' record(s) listed</td>
							 <td class="bottom">&nbsp;</td>
							 <td align="left" class="bottom"><span style="font-weight:bold; padding-left:20px">Total:</span></td>
							 <td style="text-align: right;" class="content bottom">'.$this->opm->format_currency($company['total']).'</td>
							 <td style="text-align: right;" class="content bottom">'.$this->opm->format_currency($company['total_tax']).'</td>
							 <td style="text-align: right;" class="content bottom">'.$this->opm->format_currency($company['total_with_tax']).'</td>
						  </tr>
						</tbody>
					</table>
					<br />
					<div style="border-bottom: 1px dashed #9f9f9f; height:1px; clear:both"></div>
					<br />';

		}
		//========================================
		$pdf['current_time'] = date('h:i a m/d/Y');
		$pdf['title'] = '<span style="color:#b32017">S</span>ales <span style="color:#b32017">I</span>voice <span style="color:#b32017">R</span>eport <span style="color:#b32017">B</span>y <span style="color:#b32017">C</span>ustomer<br /> (Detail inc. tax)';
		$this->layout = 'pdf';
			//set header
		$pdf['logo_link'] = 'img/logo_anvy.jpg';
		$pdf['company_address'] = '3145 - 5th Ave NE<br />Calgary  AB  T2A  6A3<br />';
		$pdf['heading'] = $data['heading'];
		if(isset($data['date_equals'])&&$data['date_equals']!='')
		{
			$pdf['date_equals'] = $data['date_equals'];
		}
		else
		{
			if(isset($data['date_from'])&&$data['date_from']!='')
				$pdf['date_from']  = $data['date_from'];
			if(isset($data['date_to'])&&$data['date_to']!='')
				$pdf['date_to'] = $data['date_to'];
		}
		$pdf['html_loop'] = $html_loop;
		$pdf['filename'] = 'SI_'.md5($pdf['current_time']);

		$this->report_pdf($pdf);
		return '/upload/'.$pdf['filename'].'.pdf';
	}
	public function option_summary_product_find($type = ''){
		if(!$this->check_permission($this->name.'_@_options_@_report_by_product_summary'))
			$this->error_auth();
        $this->selectModel('Setting');
		$arr_data['salesinvoices_status'] = $this->Setting->select_option_vl(array('setting_value'=>'salesinvoices_status'));
		$arr_data['salesinvoices_type'] = $this->Setting->select_option_vl(array('setting_value'=>'salesinvoices_type'));
		$this->selectModel('Tax');
		$arr_data['salesinvoices_tax'] = $this->Tax->tax_select_list();
		$arr_data['product_category'] = $this->Setting->select_option_vl(array('setting_value'=>'product_category'));
		$this->set('arr_data',$arr_data);
		if($type=='category')
            $this->render('../Salesinvoices/option_summary_category_find');
	}
	public function option_detailed_product_find($type = ''){
		if(!$this->check_permission($this->name.'_@_options_@_report_by_product_detailed'))
			$this->error_auth();
        $this->selectModel('Setting');
		$arr_data['salesinvoices_status'] = $this->Setting->select_option_vl(array('setting_value'=>'salesinvoices_status'));
		$arr_data['salesinvoices_type'] = $this->Setting->select_option_vl(array('setting_value'=>'salesinvoices_type'));
		$this->selectModel('Tax');
		$arr_data['salesinvoices_tax'] = $this->Tax->tax_select_list();
		$arr_data['product_category'] = $this->Setting->select_option_vl(array('setting_value'=>'product_category'));
		$this->set('arr_data',$arr_data);
		if($type=='category')
            $this->render('../Salesinvoices/option_detailed_category_find');
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
            $arr_data = Cache::read('salesinvoices_product_report_'.$type);
            Cache::delete('salesinvoices_product_report_'.$type);
        } else {
            if(isset($_POST)){
                $data['product_category'] = $this->Setting->select_option_vl(array('setting_value'=>'product_category'));
                $arr_post = $_POST;
                $arr_post = $this->Common->strip_search($arr_post);
                $arr_where = array();
                $arr_where['products']['$ne'] = '';
                if(isset($arr_post['status']) && $arr_post['status'] != '')
                    $arr_where['invoice_status'] = $arr_post['status'];
                //Check loại trừ cancel thì bỏ các status bên dưới
                if(isset($arr_post['is_not_cancel'])&&$arr_post['is_not_cancel']==1){
                    $arr_where['invoice_status'] = array('$nin'=>array('Cancelled'));
                    //Tuy nhiên nếu ở ngoài combobox nếu có chọn, thì ưu tiên nó, set status lại
                    if(isset($arr_post['status'])&&$arr_post['status']!='')
                        $arr_where['invoice_status'] = $arr_post['status'];
                }
                if(isset($arr_post['type']) && $arr_post['type']!= '')
                    $arr_where['invoice_type'] = $arr_post['type'];
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
                    $arr_where['invoice_date']['$gte'] = MongoDate($this->Common->strtotime($arr_post['date_equals'] . '00:00:00'));
                    $arr_where['invoice_date']['$lt'] = new MongoDate($this->Common->strtotime($arr_post['date_equals'] . '23:59:59'));
                } else{  //Ngày nằm trong khoảng
                    //neu chi nhap date from
                    if(isset($arr_post['date_from']) && $arr_post['date_from'] != ''){
                        $arr_where['invoice_date']['$gte'] = new MongoDate($this->Common->strtotime($arr_post['date_from'] . '00:00:00'));
                    }
                    //neu chi nhap date to
                    if(isset($arr_post['date_to']) && $arr_post['date_to'] != ''){
                        $arr_where['invoice_date']['$lte'] = new MongoDate($this->Common->strtotime($arr_post['date_to'] . '23:59:59'));
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
                $arr_salesinvoices = $this->opm->collection->aggregate(
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
                            '$project'=>array('invoice_status'=>'$invoice_status','code'=>'$code','company_name'=>'$company_name','company_id'=>'$company_id','invoice_date'=>'$invoice_date','sum_sub_total'=>'$sum_sub_total','products'=>'$products')
                        ),
                        array(
                            '$group'=>array(
                                          '_id'=>array('_id'=>'$_id','invoice_status'=>'$invoice_status','code'=>'$code','company_name'=>'$company_name','company_id'=>'$company_id','invoice_date'=>'$invoice_date','sum_sub_total'=>'$sum_sub_total'),
                                          'products'=>array('$push'=>'$products')
                                        )
                        )
                    );
                if(empty($arr_salesinvoices['result'])) {
                    echo 'empty';
                    die;
                } else {
                    $arr_salesinvoices = $arr_salesinvoices['result'];
                    if ($arr_post['report_type'] == 'summary'){
                        $arr_data = $this->summary_product_report($arr_salesinvoices,$arr_post);
                        Cache::write('salesinvoices_product_report_'.$type, $arr_data);
                    }
                    else if ($arr_post['report_type'] == 'detailed'){
                        $arr_data = $this->detailed_product_report($arr_salesinvoices,$arr_post);
                        Cache::write('salesinvoices_product_report_'.$type, $arr_data);
                    }
                    else if($arr_post['report_type'] == 'category_summary'){
                        $arr_data = $this->summary_category_product_report($arr_salesinvoices,$arr_post);
                        Cache::write('salesinvoices_product_report_'.$type, $arr_data);
                    }
                    else if($arr_post['report_type'] == 'category_detailed'){
                        $arr_data = $this->detailed_category_product_report($arr_salesinvoices,$arr_post);
                        Cache::write('salesinvoices_product_report_'.$type, $arr_data);
                    }
                }

            }
        }
        if($this->request->is('ajax'))
            die;
        else
            $this->render_pdf($arr_data);
    }
	public function summary_product_report($arr_salesinvoices,$data){
        $html = '';
        $i = $sum = 0;
        $this->selectModel('Product');
        $category = $this->Setting->select_option_vl(array('setting_value' => 'product_category'));
        $arr_data = array();
        foreach($arr_salesinvoices as $salesinvoice){
            foreach($salesinvoice['products'] as $product){
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
        $arr_pdf['report_name'] = 'SI Report By Product (Summary)';
        $arr_pdf['report_file_name'] = 'SI_'.md5(time());
        return $arr_pdf;
    }
    public function summary_category_product_report($arr_salesinvoices,$data){
        $html = '';
        $i = $sum = 0;
        $this->selectModel('Product');
        $this->selectModel('Setting');
        $category = $this->Setting->select_option_vl(array('setting_value' => 'product_category'));
        $arr_data = array();
        foreach($arr_salesinvoices as $salesinvoice){
            foreach($salesinvoice['products'] as $product){
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
        $arr_pdf['report_name'] = 'SI Report By Category Product (Summary)';
        $arr_pdf['report_file_name'] = 'SI_'.md5(time());
        return $arr_pdf;
    }
	public function detailed_product_report($arr_salesinvoices,$data){
        $i = $sum = 0;
        $html = '';
        $this->selectModel('Product');
        $category = $this->Setting->select_option_vl(array('setting_value' => 'product_category'));
        $total_num_of_salesinvoices = $total_sum_sub_total = 0;
        $arr_data = $arr_pdf = array();
        foreach($arr_salesinvoices as $salesinvoice){
            foreach($salesinvoice['products'] as $product){
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
                $arr_data[$product['code']]['salesorders'][] = array_merge($salesinvoice['_id'], array('unit_price'=>$product['unit_price'],'quantity'=>$product['quantity'],'sub_total'=>$product['sub_total']));
                if(!isset( $arr_data[$product['code']]['no_of_si']))
                    $arr_data[$product['code']]['no_of_si'] = array();
                $arr_data[$product['code']]['no_of_si'][$salesinvoice['_id']['code']] = 1;
            }
        }

        foreach ($arr_data as $value) {
            $total_num_of_salesinvoices += count($value['salesorders']);
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
                        No. of SI
                     </td>
                     <td class="right_text" colspan="3" width="20%">
                        Group total (ex. tax)
                     </td>
                  </tr>
                  <tr class="bg_2">
                     <td>' . $value['code'] . '</td>
                     <td>' . $value['products_name'] . '</td>
                     <td>' . (isset($category[$product['category']]) ? $category[$product['category']] : '') . '</td>
                     <td class="right_text">' . count($value['no_of_si']) . '</td>
                     <td colspan="3" class="right_text">' . $this->opm->format_currency($value['sub_total']) . '</td>
                  </tr>
               </tbody>
            </table>';
            $html .= '<table class="table_content" >
                        <tbody>
                          <tr class="tr_right_none" style="background: #979797;color: white;height: 29px;line-height: 29px;font-weight: bold;">
                             <td width="10%">
                                SI#
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
                         <td class="center_text">' . $this->opm->format_date($salesorder['invoice_date']->sec) . '</td>
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
                        <td class="bold_text right_none" width="70%">'.$total_num_of_salesinvoices.' record(s) listed</td>
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
        $total_num_of_salesinvoices = $total_sum_sub_total = 0;
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
            $total_num_of_salesinvoices += count($value['quotations']);
            $html .= '
            <table class="table_content">
               <tbody>
                  <tr class="tr_right_none" style="background: #911b12;color: white;height: 29px;line-height: 29px;font-weight: bold;">
                     <td width="25%">
                        Category Name
                     </td>
                     <td class="right_text" width="15%">
                        No. of QT
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
                                SI#
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
            foreach ($value['quotations'] as $salesinvoice) {
                $sum += $salesinvoice['sub_total'];
                $total_quantity += $salesinvoice['quantity'];
                $html .= '
                      <tr class="content_asset bg_' . ($i % 2 == 0 ? '1' : '2') . '">
                         <td>' . $salesinvoice['code'] . '</td>
                         <td>' . $salesinvoice['company_name'] . '</td>
                         <td class="center_text">' . $this->opm->format_date($salesinvoice['invoice_date']->sec) . '</td>
                         <td class="right_text">' . $this->opm->format_currency((float)$salesinvoice['unit_price']) . '</td>
                         <td class="right_text">' . $salesinvoice['quantity'] . '</td>
                         <td colspan="3" class="right_text">' . $this->opm->format_currency((float)$salesinvoice['sub_total']) . '</td>
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
                        <td class="bold_text right_none" width="70%">'.$total_num_of_salesinvoices.' record(s) listed</td>
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
        $arr_pdf['report_name'] = 'SI Report By Category Product (Detailed)';
        $arr_pdf['report_file_name'] = 'SI_'.md5(time());
        return $arr_pdf;
    }
	public function check_condition_SI()
	{
		$id = $this->get_id();
		$this->selectModel('Salesinvoice');
		$salesinvoice = $this->Salesinvoice->select_one(array('_id' => new MongoId($id)));
		if($salesinvoice!=''){
			if($salesinvoice['company_id']==''
					&&$salesinvoice['contact_id']=='')
				return array('err2');
			else if(empty($salesinvoice['products']))
				return array('err1');
			else if(isset($salesinvoice['shipping_id'])&&$salesinvoice['shipping_id']!='')
				return array('err3');
			else if($salesinvoice['invoice_status']=='Cancelled')
				return array('err4');
			return $salesinvoice;
		}
		return false;

	}
	public function create_shipping_from_invoice(){
		if(!$this->check_permission('shippings_@_entry_@_add')){
			echo 'You do not have permission on this action.';
			die;
		}
		$ids=$this->get_id();
		$total_invoice=0;
		$sum_amount_all=0;
		$arr_salesorder=array();
		$this->selectModel('Salesinvoice');
		if($ids!=''){
			$arr_salesinvoice = $this->opm->select_one(array('_id'=>new MongoId($ids)));

			if(is_object($arr_salesinvoice['company_id'])){
				$all_sales_invoice = $this->Salesinvoice->select_all(array(
					'arr_where'=>array('company_id'=>new MongoId($arr_salesinvoice['company_id']))
				));
				if(is_object($all_sales_invoice)){
					foreach($all_sales_invoice as $key=>$value){
						if(isset($value['sum_amount'])&&!$value['deleted'])
							$sum_amount_all+=(float)$value['sum_amount'];
					}
				}
			}


			$this->selectModel('Shipping');
			$arr_shipping=$this->Shipping->select_one(array('salesinvoice_id'=>new MongoId($ids)));
			if(is_array($arr_shipping)) {
				echo '/shippings/entry/'. $arr_shipping['_id'];
				die;
			}
		}





		if(!isset($arr_salesinvoice['company_id'])||!is_object($arr_salesinvoice['company_id'])){
			echo 'no_company';die;
		}

		$this->selectModel('Salesorder');
		$this->selectModel('Shipping');
		if(isset($arr_salesinvoice['salesorder_id'])&&is_object($arr_salesinvoice['salesorder_id'])){
			$arr_salesorder=$this->Salesorder->select_one(array('_id'=>new MongoId($arr_salesinvoice['salesorder_id'])));
		}


		$v_have_product=0;
		if(is_array($arr_salesinvoice)){

			if(is_array($arr_salesinvoice['products'])){
				foreach($arr_salesinvoice['products'] as $key1=>$value1){
					if(isset($value1['invoiced'])&&!$value1['deleted'])
						$v_have_product+=(int)$value1['invoiced'];
				}
			}
			if($v_have_product==0)
			{
				echo 'no_product';die;
			}

		}

		$this->selectModel('Company');
		$arr_company=array();
		if(is_object($arr_salesinvoice['company_id']))
			$arr_company=$this->Company->select_one(array('_id'=>new MongoId($arr_salesinvoice['company_id'])));

		if(isset($arr_company['account'])&&is_array($arr_company['account']))
		{


			if(is_object($arr_salesinvoice['salesorder_id'])){
				$query_salesinvoice = $this->opm->select_all(array(
					'arr_where' => array('salesorder_id' => new MongoId($arr_salesinvoice['salesorder_id']))
				));

				if(is_object($query_salesinvoice)){
					foreach($query_salesinvoice as $key=>$value){
						$total_invoice += isset($value['sum_amount'])?$value['sum_amount']:0;
					}
				}

			}


			if(isset($arr_company['account']['credit_limit'])&&isset($arr_salesinvoice['sum_amount'])&&$arr_company['account']['credit_limit']!=0)
			{
				if($arr_salesinvoice['sum_amount']>$arr_company['account']['credit_limit']){
					echo 'over';die;
				}

			}

			if(isset($arr_company['account']['credit_limit'])&&$arr_company['account']['credit_limit']!=0)
			{
				if(isset($arr_salesinvoice['salesorder_id'])&&is_object($arr_salesinvoice['salesorder_id'])){
					if($total_invoice>$arr_company['account']['credit_limit']){
						echo 'over1';die;
					}

				}

				if($sum_amount_all>$arr_company['account']['credit_limit']){
					echo 'over2';die;
				}

			}




		}



		$arr_save=array();

		if(isset($arr_salesorder['company_id']))
			$arr_save = $this->arr_associated_data('company_name',$arr_salesorder['company_name'], $arr_salesorder['company_id']);

		$arr_save['shipping_type']='Out';
		$arr_save['shipping_status']='Completed';
		$arr_save['shipping_date']=new MongoDate(time());

		$arr_save['salesorder_id']=isset($arr_salesorder['_id'])?$arr_salesorder['_id']:'';
		$arr_save['salesorder_number']=isset($arr_salesorder['code'])?$arr_salesorder['code']:'';
		$arr_save['salesorder_name']=isset($arr_salesorder['name'])?$arr_salesorder['name']:'';

		$arr_save['salesinvoice_id']=is_object($arr_salesinvoice['_id'])?$arr_salesinvoice['_id']:'';
		$arr_save['salesinvoice_number']=isset($arr_salesinvoice['code'])?$arr_salesinvoice['code']:'';
		$arr_save['salesinvoice_name']=isset($arr_salesinvoice['name'])?$arr_salesinvoice['name']:'';



		$arr_save['products']=isset($arr_salesinvoice['products'])?$arr_salesinvoice['products']:'';

		if(is_array($arr_save['products'])){
			foreach($arr_save['products'] as $key=>$value)
			{

				if(!$arr_save['products'][$key]['deleted']){

					$arr_save['products'][$key]['prev_shipped']=isset($arr_salesorder['products'][$key]['shipped'])?$arr_salesorder['products'][$key]['shipped']:0;

					if(isset($arr_salesorder['products'][$key]['shipped']))
						$arr_salesorder['products'][$key]['shipped'] += isset($arr_save['products'][$key]['invoiced'])?$arr_save['products'][$key]['invoiced']:0;
					$arr_save['products'][$key]['shipped']= isset($arr_save['products'][$key]['invoiced'])?$arr_save['products'][$key]['invoiced']:0;


					$v_quantity= isset($arr_salesorder['products'][$key]['quantity'])?$arr_salesorder['products'][$key]['quantity']:0;

					if(isset($arr_salesorder['products'][$key]['balance_shipped'])&&isset($arr_salesorder['products'][$key]['shipped']))
						$arr_salesorder['products'][$key]['balance_shipped']=$v_quantity-$arr_salesorder['products'][$key]['shipped'];

					if(isset($arr_salesorder['products'][$key]['shipped']))
						$arr_save['products'][$key]['balance_shipped']=$v_quantity-$arr_salesorder['products'][$key]['shipped'];



				}

			}

		}
		if(is_array($arr_salesorder)){
			if(is_array($arr_salesorder['products'])){
				foreach($arr_salesorder['products'] as $key1=>$value1){
					if(isset($value1['balance_shipped'])&&!$value1['deleted']&&$value1['balance_shipped']<0)
					{
						echo 'end_balance';die;
					}
				}
			}
		}
//		die;

		$arr_save['code'] =$this->Shipping->get_auto_code('code');

		if ($this->Shipping->save($arr_save)) {

			if(isset($arr_salesinvoice['salesorder_id'])&&is_object($arr_salesinvoice['salesorder_id'])){
				$this->Salesorder->save($arr_salesorder);
			}
			echo '/shippings/entry/'. $this->Shipping->mongo_id_after_save;
			die;
		}
		echo '/shippings/entry';
		die;



	}
	public function create_shipping(){
		if(!$this->check_permission('shippings_@_entry_@_add')){
			echo 'You do not have permission on this action.';
			die;
		}
		$id = $this->get_id();
		$this->selectModel('Salesinvoice');
		$salesinvoice = $this->Salesinvoice->select_one(array('_id' => new MongoId($id)));
		if($salesinvoice['company_id']==''
				&&$salesinvoice['contact_id']==''){
			echo json_encode(array('status'=>'error','message'=>'This function cannot be performed as there is no company or contact linked to this record.'));
			die;
		}
		else if(empty($salesinvoice['products'])){
			echo json_encode(array('status'=>'error','message'=>'There are no items on this sales invoice that are available for shipping because they were created from a job expense or resource.'));
			die;
		}
		else if(isset($salesinvoice['shipping_id'])&&$salesinvoice['shipping_id']!=''){
			echo json_encode(array('status'=>'error','message'=>'This invoice is already shipped.'));
			die;
		}
		else if($salesinvoice['invoice_status']=='Cancelled'){
			echo json_encode(array('status'=>'error','message'=>'This sales invoice has been cancelled.'));
			die;
		}
		$arr_save = $salesinvoice;
		$this->selectModel('Shipping');
		$arr_save['salesinvoice_id'] = $salesinvoice['_id'];
		$arr_save['salesinvoice_name'] = $salesinvoice['name'];
		$arr_save['salesinvoice_number'] = $salesinvoice['code'];
		$arr_save['code'] = $this->Shipping->get_auto_code('code');
		$arr_save['shipping_type'] =  "Out";
		$arr_save['shipping_status'] = 'Completed';
		$arr_save['shipping_date'] = new MongoDate();
		$arr_save['sum_amount']  = (isset($salesinvoice['sum_amount']) ? $salesinvoice['sum_amount'] : 0);
		$arr_save['sum_sub_total']  = (isset($salesinvoice['sum_sub_total']) ? $salesinvoice['sum_sub_total'] : 0);
		$arr_save['sum_tax'] = (isset($salesinvoice['sum_tax']) ? $salesinvoice['sum_tax'] : 0);
		$arr_save['tax']  = (isset($salesinvoice['tax']) ? $salesinvoice['tax'] : 0);
		$arr_save['taxval'] = (isset($salesinvoice['taxval']) ? $salesinvoice['taxval'] : 0);
		$arr_save['carrier_id'] = '';
		$arr_save['carrier_name'] = '';
		if($arr_save['shipping_address'] == '')
			$arr_save['shipping_address'] = $arr_save['invoice_address'];
		$arr_save['received_date'] = '';
		$arr_save['return_status'] = 0;
		$arr_save['tracking_no'] = '';
		$arr_save['traking'] = 0;
		unset($arr_save['_id']);
		unset($arr_save['date_modifide']);
		unset($arr_save['modified_by']);
		unset($arr_save['paid_date']);
		unset($arr_save['payment_due_date']);
		unset($arr_save['payment_terms']);
		unset($arr_save['invoice_date']);
		if($this->Shipping->save($arr_save)){
			$id = $this->Shipping->mongo_id_after_save;
			$salesinvoice['shipping_code'] = $arr_save['code'];
			$salesinvoice['shipping_id'] = $id;
			$this->selectModel('Salesinvoice');
			$this->Salesinvoice->save($salesinvoice);
			echo json_encode(array('status'=>'ok','url'=>URL.'/shippings/entry/'.$id));
		}
		die;
	}
	public function create_receipt($option='')
	{
		if(!$this->check_permission('salesinvoices_@_entry_@_add')
			&& !$this->check_permission($this->name.'_@_receipt_tab_@_add') ){
			echo 'You do not have permission on this action.';
			die;
		}
		$this->selectModel('Salesinvoice');
		$id = $this->get_id();
		$invoice = $this->Salesinvoice->select_one(array('_id'=>new MongoId($id)),array('company_id','sum_sub_total','sum_amount','taxval','invoice_status', 'invoice_type','company_name','code','job_id', 'shipping_cost'));
		$invoice['invoice_status_old'] = $invoice['invoice_status'];
		if($invoice['invoice_status']=='Paid'){
			if($option==''){
				echo 'Change status';
			}
			die;
		}
		// $invoice['invoice_status'] = 'Invoiced';
		$this->selectModel("Receipt");
		$arr_save = array();
		$arr_save['deleted'] = false;
		$arr_save['code'] = $this->Receipt->get_auto_code('code');
		$arr_save['amount_received'] = 0;
		$arr_save['description'] = '';
		$this->selectModel('Salesaccount');
		if(isset($invoice['company_id'])&&is_object($invoice['company_id'])){
			$salesaccount = $this->Salesaccount->select_one(array('company_id'=>$invoice['company_id']));
			if(!empty($salesaccount)){
				$arr_save['salesaccount_id'] = new MongoId($salesaccount['_id']);
				$arr_save['salesaccount_name'] = $invoice['company_name'];
			}
			$arr_save['company_name'] = $invoice['company_name'];
			$arr_save['company_id'] = new MongoId($invoice['company_id']);
		}
		$arr_save['receipt_date'] = new MongoDate();
		$arr_save['paid_by'] = '';
		$arr_save['our_bank_account'] = '';
		$arr_save['our_rep'] = (isset($invoice['our_rep']) ? $invoice['our_rep'] : '');
		$arr_save['our_rep_id'] = (isset($invoice['our_rep_id']) ? $invoice['our_rep_id'] : '');
		$arr_save['our_csr'] = (isset($invoice['our_csr']) ? $invoice['our_csr'] : '');
		$arr_save['our_csr_id'] = (isset($invoice['our_csr_id']) ? $invoice['our_csr_id'] : '');
		$arr_save['identity'] = (isset($invoice['indentity']) ? $invoice['indentity'] : '');
		$arr_save['use_own_letterhead'] = 0;
		$arr_save['ext_accounts_sync']  = 0 ;
		$arr_save['notes'] = '';
		$arr_save['allocation'][0] = array(
									'deleted'=>false,
									'salesinvoice_code'=>$invoice['code'],
									'salesinvoice_id'=> new MongoId($invoice['_id']),
									'note'=> '',
									'amount'=>0,
									'mod'=>'Part'
			);
		if($option=='Fully'){
			if($invoice['invoice_status'] == 'Credit') {
				if($invoice['sum_amount'] > 0)
					$invoice['sum_amount'] = -$invoice['sum_amount'];
			} else {
				$this->selectModel('Company');
				$original_minimum = Cache::read('minimum');
				$product = Cache::read('minimum_product');
				$arr_companies = Cache::read('arr_companies');
			    if(!$arr_companies)
			        $arr_companies = array();
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
				if(!isset($arr_companies[(string)$invoice['company_id']])){
	                $minimum = $original_minimum;
	                if(is_object($invoice['company_id'])){
	                    $company = $this->Company->select_one(array('_id'=>new MongoId($invoice['company_id'])),array('pricing'));
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
	                $arr_companies[(string)$invoice['company_id']] = $minimum;
	            } else {
	                $minimum = $arr_companies[(string)$invoice['company_id']];
	            }
	            if( $invoice['invoice_type'] != 'Credit' &&  $invoice['invoice_status'] != 'Credit' ) {
    	            if($invoice['sum_sub_total']<$minimum){
    					$invoice['taxval'] = (isset($invoice['taxval']) ? $invoice['taxval'] : 0);
    					$invoice['sum_amount'] = $minimum + ($minimum*$invoice['taxval']/100);
    				}
	            }
			}
			$sum_amount = $this->update_balace_credit_salesaccount($invoice);
			$arr_save['allocation'][0] = array(
									'deleted'=>false,
									'salesinvoice_code'=>$invoice['code'],
									'salesinvoice_id'=> new MongoId($invoice['_id']),
									'note'=> '',
									'amount'=>$invoice['sum_amount'],
									'mod'=>'Fully'
			);
			//Update lại status, ngày trả tiền cho SI
			$invoice['invoice_status'] = 'Paid';
			$invoice['paid_date'] = $arr_save['receipt_date'];
			$invoice['total_receipt'] = ($invoice['sum_amount'] < 0 ? -$invoice['sum_amount'] : $invoice['sum_amount']);
			//Trả full thì gán amount_received bằng tổng tiền cả SI luôn
			$arr_save['amount_received'] = 0;
		}
		$arr_save['comments'] = '';
		$arr_save['total_allocated'] = (isset($arr_save['allocation'][0]['amount']) ? $arr_save['allocation'][0]['amount'] : 0);
		$arr_save['unallocated'] = $arr_save['amount_received'] - $arr_save['total_allocated'];

		$this->Salesinvoice->save($invoice);
		if($invoice['invoice_status'] == 'Paid' && is_object($invoice['job_id'])){
			$this->selectModel('Job');
			$this->Job->save(array('_id' => $invoice['job_id'],'status' => 'Paid'));
		}
		if($this->Receipt->save($arr_save)){
			$id = $this->Receipt->mongo_id_after_save;
			if($option=='')
				$this->redirect('/receipts/entry/'.$id);
			else if($option=="Part")
				echo URL.'/receipts/entry/'.$id;
		}
		die;
	}


	public function delete_all_associate($idopt='',$key=''){
		$query=array();
		$ids = $this->get_id();
		if($ids!=0)
			$query=$this->opm->select_one(array('_id'=>new MongoId($ids)),array('company_id','sum_amount','products','taxval','sum_sub_total'));
		else{
			echo 'function SIController -> function delete_all_associate: ids is null'; die;
		}
		//Update lại balance và receipt cho SA
		$this->update_balace_credit_salesaccount($query,'minus');

		if($key=='products'){ // update cac line entry option cua products
			if($ids!=''){
				$arr_insert = $line_entry = array();
				//lay note products hien co
				$query = $this->opm->select_one(array('_id'=>new MongoId($ids)),array('products'));
				if(isset($query['products']) && !empty($query['products'])){
					$line_entry = $query['products'];
					$line_entry[$idopt] =  array('deleted'=>true);
					foreach($query['products'] as $keys=>$values){
						if(isset($values['option_for']) && $values['option_for']==$idopt){
                            $line_entry[$keys] = array('deleted'=>true);
						}
					}
				}
				$arr_insert['products'] = $line_entry;//pr($line_entry);die;
				$arr_insert['_id'] 		= new MongoId($ids);
				$arr_insert = array_merge($arr_insert,$this->new_cal_sum($line_entry));
				$this->opm->save($arr_insert);
			}
		} else if($key=='options'){
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
        }
	}
	function update_balace_credit_salesaccount($query,$action = 'plus'){
		if(isset($query['company_id']) && is_object($query['company_id'])){
			$minimum = 50;
	        $this->selectModel('Stuffs');
	        $product = $this->Stuffs->select_one(array('value'=>"Minimum Order Adjustment"),array('product_id'));
	        $product_id = $product['product_id'];
	        if(isset($product['product_id'])&&is_object($product['product_id'])){
	            $this->selectModel('Product');
	            $product = $this->Product->select_one(array('_id'=> new MongoId($product_id)),array('sell_price'));
	            $minimum = $product['sell_price'];
	            $product_id = $product['_id'];
	        }
	        $this->selectModel('Company');
	        $company = $this->Company->select_one(array('_id'=>$query['company_id']),array('pricing'));
			if(isset($company['pricing'])){
				foreach($company['pricing'] as $pricing){
					if(isset($pricing['deleted'])&&$pricing['deleted']) continue;
					if((string)$pricing['product_id']!=(string)$product_id) continue;
					if(!isset($pricing['price_break']) || empty($pricing['price_break'])) continue;
					$price_break = reset($pricing['price_break']);
					$minimum =  (float)$price_break['unit_price']; break;
				}
			}
			if( $query['invoice_status'] != 'Credit' && $query['invoice_type'] != 'Credit' ) {
				if($query['sum_sub_total']<$minimum){
					$query['taxval'] = (isset($query['taxval']) ? $query['taxval'] : 0);
					$query['sum_amount'] = $minimum + ($minimum*$query['taxval']/100);
				}
			}
			$sum_amount = ($action == 'plus' ? $query['sum_amount'] : (-$query['sum_amount']));
			$this->selectModel('Salesaccount');
			$this->Salesaccount->update_account($query['company_id'], array(
												'model' => 'Company',
												'balance' =>   $sum_amount,
												'invoices_credits' =>  $sum_amount,
												));
		}
		return $query['sum_amount'];
	}

	public function check_over_credit_limit($sum_amount=0){
		$ids=$this->get_id();
		$arr_salesinvoice=array();
		$arr_company=array();
		$arr_salesorder=array();
		$sum_amount_all=0;
		$total_invoice=0;
		if($ids!=0){
			$arr_salesinvoice=$this->opm->select_one(array('_id'=>new MongoId($ids)));
			$this->selectModel('Salesorder');
			if(is_object($arr_salesinvoice['salesorder_id'])){
				$query_salesinvoice = $this->opm->select_all(array(
					'arr_where' => array('salesorder_id' => new MongoId($arr_salesinvoice['salesorder_id']))
				));

				if(is_object($query_salesinvoice)){
					foreach($query_salesinvoice as $key=>$value){
						$total_invoice += isset($value['sum_amount'])?$value['sum_amount']:0;
					}
				}

				$arr_salesorder=$this->Salesorder->select_one(array('_id'=>new MongoId($arr_salesinvoice['salesorder_id'])));
			}



		}

		$this->selectModel('Company');
		$this->selectModel('Salesinvoice');
		if(is_object($arr_salesinvoice['company_id'])){
			$arr_company=$this->Company->select_one(array('_id'=>new MongoId($arr_salesinvoice['company_id'])));
			$all_sales_invoice = $this->Salesinvoice->select_all(array(
				'arr_where'=>array('company_id'=>new MongoId($arr_salesinvoice['company_id']))
			));
			if(is_object($all_sales_invoice)){
				foreach($all_sales_invoice as $key=>$value){
					if(isset($value['sum_amount'])&&!$value['deleted'])
						$sum_amount_all+=(float)$value['sum_amount'];
				}
			}


		}

		if(is_array($arr_company['account']))
		{

			if(isset($arr_company['account']['credit_limit'])&&isset($sum_amount)&&$sum_amount>$arr_company['account']['credit_limit']&&$arr_company['account']['credit_limit']!=0)
			{
				echo 'over';die;
			}

			if(isset($arr_company['account']['credit_limit'])&&$arr_company['account']['credit_limit']!=0)
			{
				if($total_invoice>$arr_company['account']['credit_limit']){
					echo 'over1';die;
				}
			}

			if($sum_amount_all>$arr_company['account']['credit_limit']){
				echo 'over2';die;
			}

		}
		die;
	}
	public function check_have_salesorder_id(){
		$ids=$this->get_id();
		$arr_salesinvoice=array();
		if($ids!=0)
			$arr_salesinvoice=$this->opm->select_one(array('_id'=>new MongoId($ids)));
		if(is_object($arr_salesinvoice['salesorder_id']))
		{
			echo 'have_link_to_sales_order';
			die;
		}
		else
		{
			echo 'no_have_link_to_sales_order';
			die;
		}
	}

	public function view_invoices_exclude_cancellations(){
		if(!isset($_GET['print_pdf'])){
			$arr_where = $this->arr_search_where();
			$arr_where['invoice_status'] = array('$ne'=>'Cancelled');
			$salesinvoices = $this->opm->select_all(array(
													'arr_where' => $arr_where,
													'arr_field' => array('code','company_name','invoice_date','our_rep','job_number'),
													'arr_order' => array('_id'=>1),
													'limit'     => 2000
													));

			$arr_data = array();
			if($salesinvoices->count() > 0){
				$group = array();
				$html= '';
				$i = 0;
				$arr_invoices = array();
				foreach($salesinvoices as $key => $salesinvoice){
					$arr_invoices[] = array(
					                        'code' => (isset($salesinvoice['code']) ? $salesinvoice['code'] : ''),
					                        'company_name' => (isset($salesinvoice['company_name']) ? $salesinvoice['company_name'] : ''),
					                        'invoice_date' => (isset($salesinvoice['invoice_date']) && is_object($salesinvoice['invoice_date']) ?date('d M, Y',$salesinvoice['invoice_date']->sec):''),
					                        'our_rep' => (isset($salesinvoice['our_rep']) ? $salesinvoice['our_rep'] : ''),
					                        'job_number' => (isset($salesinvoice['job_number']) ? $salesinvoice['job_number'] : ''),
					                        );
				}
				foreach($arr_invoices as $salesinvoice){
					$html .= '<tr class="'.($i%2==0 ? 'bg_2' : 'bg_1').'">';
					$html .= '<td>'.$salesinvoice['code'] .'</td>';
					$html .= '<td>'. $salesinvoice['company_name'] .'</td>';
					$html .= '<td class="center_text">'.$salesinvoice['invoice_date'] .'</td>';
					$html .= '<td>'. $salesinvoice['our_rep'] .'</td>';
					$html .= '<td>'. $salesinvoice['job_number'].'</td>';
					$html .= '</tr>';
	                $i++;
				}
				$html .='<tr class="last">
					<td colspan="8" class="bold_text right_none">'.$i.' record(s) listed.</td>
				</tr>';
				$arr_data['title'] = array('Ref No','Customer'=>'text-align: left',  'Date',  'Our Rep'=>'text-align: left', 'Job No'=>'text-align: left');
				$arr_data['content'] = $html;
				$arr_data['report_name'] = 'Invoices Mini Listing';
				$arr_data['report_file_name'] = 'SI_'.md5(time());
				$arr_data['report_orientation'] = 'landscape';
				$arr_data['excel_url'] = URL.'/salesinvoices/view_excel/exclude_cancellations';
				Cache::write('salesinvoices_exclude_cancellations', $arr_data);
				Cache::write('salesinvoices_exclude_cancellations_excel', $arr_invoices);
	        }
    	} else {
    		$arr_data = Cache::read('salesinvoices_exclude_cancellations');
    		Cache::delete('salesinvoices_exclude_cancellations');
    	}
		$this->render_pdf($arr_data);
	}
	public function view_minilist(){
		if(!isset($_GET['print_pdf'])){
			$arr_where = $this->arr_search_where();
			$salesinvoices = $this->opm->select_all(array(
													'arr_where' => $arr_where,
													'arr_field' => array('code','company_name','invoice_date','our_rep','job_number'),
													'arr_order' => array('_id'=>1),
													'limit'     => 2000
													));

			$arr_data = array();
			if($salesinvoices->count() > 0){
				$group = array();
				$html= '';
				$i = 0;
				$arr_invoices = array();
				foreach($salesinvoices as $key => $salesinvoice){
					$arr_invoices[] = array(
					                        'code' => (isset($salesinvoice['code']) ? $salesinvoice['code'] : ''),
					                        'company_name' => (isset($salesinvoice['company_name']) ? $salesinvoice['company_name'] : ''),
					                        'invoice_date' => (isset($salesinvoice['invoice_date']) && is_object($salesinvoice['invoice_date']) ?date('d M, Y',$salesinvoice['invoice_date']->sec):''),
					                        'our_rep' => (isset($salesinvoice['our_rep']) ? $salesinvoice['our_rep'] : ''),
					                        'job_number' => (isset($salesinvoice['job_number']) ? $salesinvoice['job_number'] : ''),
					                        );
				}
				foreach($arr_invoices as $salesinvoice){
					$html .= '<tr class="'.($i%2==0 ? 'bg_2' : 'bg_1').'">';
					$html .= '<td>'.$salesinvoice['code'] .'</td>';
					$html .= '<td>'. $salesinvoice['company_name'] .'</td>';
					$html .= '<td class="center_text">'.$salesinvoice['invoice_date'] .'</td>';
					$html .= '<td>'. $salesinvoice['our_rep'] .'</td>';
					$html .= '<td>'. $salesinvoice['job_number'].'</td>';
					$html .= '</tr>';
	                $i++;
				}
				$html .='<tr class="last">
					<td colspan="8" class="bold_text right_none">'.$i.' record(s) listed.</td>
				</tr>';
				$arr_data['title'] = array('Ref No','Customer'=>'text-align: left',  'Date',  'Our Rep'=>'text-align: left', 'Job No'=>'text-align: left');
				$arr_data['content'] = $html;
				$arr_data['report_name'] = 'Invoices Mini Listing';
				$arr_data['report_file_name'] = 'SI_'.md5(time());
				$arr_data['report_orientation'] = 'landscape';
				$arr_data['excel_url'] = URL.'/salesinvoices/view_excel';
				Cache::write('salesinvoices_minilist', $arr_data);
				Cache::write('salesinvoices_minilist_excel', $arr_invoices);
	        }
    	} else {
    		$arr_data = Cache::read('salesinvoices_minilist');
    		Cache::delete('salesinvoices_minilist');
    	}
		$this->render_pdf($arr_data);
	}
	function view_excel($option=""){
		if($option=="exclude_cancellations") {
        	$arr_invoices = Cache::read('salesinvoices_exclude_cancellations_excel');
	        Cache::delete('salesinvoices_exclude_cancellations_excel');
		} else {
        	$arr_invoices = Cache::read('salesinvoices_minilist_excel');
	        Cache::delete('salesinvoices_minilist_excel');
		}
	    if(!$arr_invoices){
            echo 'No data';die;
        }
        App::import('Vendor', 'phpexcel/PHPExcel');
        $objPHPExcel = new PHPExcel();
        // Set document properties
        $objPHPExcel->getProperties()->setCreator("")
                                     ->setLastModifiedBy("")
                                     ->setTitle("Salesinvoice - Minilist")
                                     ->setSubject("Salesinvoice - Minilist")
                                     ->setDescription("Salesinvoice - Minilist")
                                     ->setKeywords("Salesinvoice - Minilist")
                                     ->setCategory("Salesinvoice - Minilist");
        $worksheet = $objPHPExcel->getActiveSheet();
        $objPHPExcel->getActiveSheet()->setCellValue('A1',"Ref No")
                                        ->setCellValue('B1',"Customer")
                                        ->setCellValue('C1',"Date")
                                        ->setCellValue('D1',"Our Rep")
                                        ->setCellValue('E1',"Job No");
        $objPHPExcel->getActiveSheet()->freezePane('F2');
        $i = 2;
        foreach($arr_invoices as $invoice){
            $worksheet->setCellValue('A'.$i,$invoice['code'])
                        ->setCellValue('B'.$i,$invoice['company_name'])
                        ->setCellValue('C'.$i,$invoice['invoice_date'])
                        ->setCellValue('D'.$i,$invoice['our_rep'])
                        ->setCellValue('E'.$i,$invoice['job_number']);
            $i ++;
        }
        $worksheet->setCellValue('A'.$i,($i-2).' record(s) listed');
        $worksheet->mergeCells("A$i:C$i");
        $worksheet->getRowDimension(8)->setRowHeight(-1);
        $worksheet->getStyle('A1:E1')->getFont()->setBold(true);
        $worksheet->getStyle('A1:A'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
        $worksheet->getStyle('E1:E'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
        $worksheet->getStyle('C1:C'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
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
		$worksheet->getStyle('A1:E'.($i-1))->applyFromArray($styleArray);
        for($i = 'A'; $i !== 'E'; $i++){
        	$worksheet->getColumnDimension($i)
				        	->setAutoSize(true);
        }
        $objPHPExcel->setActiveSheetIndex(0);
        $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
        $objWriter->save(APP.DS.'webroot'.DS.'upload'.DS.'Salesinvoice_Minilist.xlsx');
        $this->redirect('/upload/Salesinvoice_Minilist.xlsx');
        die;
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
		$custom_option = $this->salesinvoice_options_data($idsub);
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
				else
					$data[$kks]['xlock']['choice'] = 1;
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
	 public function salesinvoice_options_data($parent_line_no=0){
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
	public function costing_list(){
		if(isset($_POST['submit'])){
            $this->option_cal_price($_POST);
        }
        $this->set('return_mod', true);
        $this->set('return_title', 'Making for this item');
        $this->set('return_link', URL . '/salesinvoices/entry');
        $opname = 'products';
        $arr_set = $this->opm->arr_settings;
        $subdatas = $arr_subsetting = $custom_option_group = array();
        $salesorder_code = $sumrfq = 0; $products_id = $idsub = $groupstr = '';
		//neu idopt khac rong
		if ($this->params->params['pass'][1] != '') {
            //DATA: salesorder line details
            $idsub = $this->params->params['pass'][1];
            $arr_ret = $this->opm->select_one(array('_id'=> new MongoId($this->get_id())),array('products','options','invoice_date'));
            if(!isset($arr_ret['options']))
            	$arr_ret['options'] = array();
            $subdatas['salesinvoice_line_details'] = array();
            $products_id = '';
            if(!empty($arr_ret[$opname])){
                foreach($arr_ret[$opname] as $key => $value){
                    if($key!=$idsub) continue;
                    if(isset($value['deleted'])&&$value['deleted']) continue;
                    $subdatas['salesinvoice_line_details'] = $value;
                    $subdatas['salesinvoice_line_details']['key'] = $key;
                    $products_id = $value['products_id'];
                    break;
                }
            }
            //DATA: option list
            $arr_ret[$opname][$idsub]['products_id'] = $products_id;
            $option_list_data = $this->new_option_data(array('key'=>$idsub,'products_id'=>$products_id,'options'=>$arr_ret['options'],'date'=>$arr_ret['invoice_date']),$arr_ret['products']);
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
        $this->set('salesinvoiceline', 'salesinvoice_line_details');
        $this->set('salesinvoice_code', $salesorder_code);
        $this->set('sumrfq', $sumrfq);
		$this->set('products_id', (string)$arr_ret[$opname][$idsub]['products_id']);
        $this->set('subitems', $idsub);
        $this->set('employee_id', $this->opm->user_id());
        $this->set('employee_name', $this->opm->user_name());
        if(!isset($arr_ret[$opname][$idsub]['is_saved']))
		  $this->set('custom_product2', '1');
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
            $arr_ret = $this->opm->select_one(array('_id'=> new MongoId($this->get_id())),array('products','options','invoice_date'));
            if(!isset($arr_ret['options']))
            	$arr_ret['options'] = array();
            $subdatas['salesinvoice_line_details'] = array();
            $products_id = '';
            if(!empty($arr_ret[$opname])){
                if(isset($arr_ret[$opname][$idsub])&&!$arr_ret[$opname][$idsub]['deleted']){
                    $products_note = '';
                    $subdatas['salesinvoice_line_details'] = $arr_ret[$opname][$idsub];
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
            $option_list_data = $this->new_option_data(array('key'=>$idsub,'products_id'=>$products_id,'options'=>$arr_ret['options'],'date'=>$arr_ret['_id']->getTimestamp()),$arr_ret['products']);
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
                            'title' => 'Hidden from PDFs',
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
        $this->set('salesinvoiceline', 'salesinvoice_line_details');
        $this->set('salesinvoice_code', $salesorder_code);
        $this->set('sumrfq', $sumrfq);
		$this->set('products_id', (string)$arr_ret[$opname][$idsub]['products_id']);
        $this->set('subitems', $idsub);
        $this->set('employee_id', $this->opm->user_id());
        $this->set('employee_name', $this->opm->user_name());
        if(!isset($arr_ret[$opname][$idsub]['is_saved']))
		  $this->set('custom_product2', '1');

    }
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

   	public function rebuild_invoice_option(){
        $invoice = $this->opm->select_all(array('limit'=>999999));
        $i =0;
        echo $invoice->count();
        foreach($invoice as $value){
        	$arr_data = array();
            $arr_data['_id']  = new MongoId($value['_id']);
            $arr_data['sum_tax']  = $value['sum_tax'];
            $arr_data['sum_amount']  = $value['sum_amount'];
            $arr_data['sum_sub_total']  = $value['sum_sub_total'];
            if(isset($value['products'])&&!empty($value['products'])){
                foreach($value['products'] as $product_k => $product){
                	$arr_data['products'][$product_k] = $product;
                	if(isset($product['deleted'])&&$product['deleted'])
                		$arr_data['products'][$product_k] = $value['products'][$product_k] = array('deleted'=>true);
                	if(isset($product['option_for'])&&$product['option_for']!=''){
                		$option_for = $product['option_for'];
                		if(!isset($value['products'][$option_for])
                		   || (isset($value['products'][$option_for]['deleted'])&&$value['products'][$option_for]['deleted'])
                		   )
                			$arr_data['products'][$product_k] = $value['products'][$product_k] = array('deleted'=>true);
                	}
                }
                $arr_sum = $this->new_cal_sum($value['products']);
                $arr_data = array_merge($arr_data,$arr_sum);
            }
            $this->opm->rebuild_collection($arr_data);
            $i++;
        }
        echo '<br />Xong - '.$i;
        die;
    }
    public function salesorders(){
    	$subdatas['salesorders'] = array();
    	$salesinvoice = $this->opm->select_one(array('_id'=> new MongoId($this->get_id())),array('salesorder_id','salesorders'));
    	$salesorders = array();
    	if(isset($salesinvoice['salesorder_id'])&&is_object($salesinvoice['salesorder_id']))
    		$salesorders[] = $salesinvoice['salesorder_id'];
    	if(isset($salesinvoice['salesorders'])){
    		foreach($salesinvoice['salesorders'] as $key=>$value)
				if((string)$salesinvoice['salesorder_id']==(string)$value)
					unset($salesinvoice['salesorders'][$key]);
    		$salesorders = array_merge($salesorders,$salesinvoice['salesorders']);
    	}
    	$this->selectModel('Salesorder');
    	$i = 0;
    	foreach($salesorders as $salesorder_id){
    		$salesorder = $this->Salesorder->select_one(array('_id'=> new MongoId($salesorder_id)),array('code','contact_name','salesorder_date','payment_due_date','status','our_rep','sum_sub_total','sum_tax','sum_amount','taxval', 'shipping_cost'));
    		$minimum = $this->get_minimum_order('Salesorder',$salesorder['_id']);
    		if($salesorder['sum_sub_total']<$minimum){
    			$more_sub_total = $minimum - (float)$salesorder['sum_sub_total'];
    			$sub_total = $more_sub_total;
                $tax = $sub_total*(float)$salesorder['taxval']/100;
                $amount = $sub_total+$tax;
    			$salesorder['sum_sub_total'] += $sub_total;
    			$salesorder['sum_amount'] += $amount;
    			$salesorder['sum_tax'] = $salesorder['sum_amount']-$salesorder['sum_sub_total'];
    		}
    		$subdatas['salesorders'][$i++] = array(
    		                                       'salesorder_id'=>$salesorder['_id'],
    		                                       'code'=>$salesorder['code'],
    		                                       'contact_name'=>$salesorder['contact_name'],
    		                                       'salesorder_date'=>$this->opm->format_date($salesorder['salesorder_date']->sec),
    		                                       'payment_due_date'=>$this->opm->format_date($salesorder['payment_due_date']->sec),
    		                                       'status'=>$salesorder['status'],
    		                                       'our_rep'=>$salesorder['our_rep'],
    		                                       'sum_sub_total'=> $this->opm->format_currency((float)$salesorder['sum_sub_total']),
    		                                       'sum_tax'=> $this->opm->format_currency((float)$salesorder['sum_tax']),
    		                                       'sum_amount'=> $this->opm->format_currency((float)$salesorder['sum_amount']),
    		                                       );
    	}
        $this->set('subdatas', $subdatas);
    }
    function add_salesorder(){
    	if(!$this->check_permission('salesinvoices_@_entry_@_edit')){
			echo 'You do not have permission on this action.';
			die;
		}
		if(!isset($_POST['ids']) || strlen(trim($_POST['ids']))!=24)
			die;
		$salesorder_id = $_POST['ids'];
		$arr_salesinvoice=array();
		$ids = $this->get_id();
		$this->selectModel('Salesinvoice');
		$this->selectModel('Salesorder');
		$arr_salesorder=$this->Salesorder->select_one(array('_id'=>new MongoId($salesorder_id)));
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
		if($this->append_build_salesorder($ids,$arr_salesorder))
			echo 'ok';
		die;
    }
    function batch_invoices_popup($key = ""){
		$this->set('key', $key);
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
        $arr_order = array('invoice_date'=>-1);
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
        }
		$cond['invoice_status'] = array('$nin'=>array('Invoiced','Paid','Cancelled'));
		if(IS_LOCAL && empty($key)) {
			$arr_order['invoice_status'] = 1;
			unset($cond['invoice_status']['$nin'][0]);
			$cond['invoice_status']['$nin'] = array_values($cond['invoice_status']['$nin']);
		}
        // search theo submit $_POST kèm điều kiện
        if (!empty($this->data) && !empty($_POST) && isset($this->data['Salesinvoice'])) {
            $arr_post = $this->Common->strip_search($this->data['Salesinvoice']);
            if (isset($arr_post['code']) && strlen($arr_post['code']) > 0) {
                $cond['code'] = new MongoRegex('/' . trim($arr_post['code']) . '/i');
            }

             if (isset($arr_post['invoice_status']) && strlen($arr_post['invoice_status']) > 0) {
             	$this->set('invoice_status',$arr_post['invoice_status']);
                $cond['invoice_status'] = new MongoRegex('/' . trim($arr_post['invoice_status']) . '/i');
            }

            if (isset($arr_post['company']) && strlen($arr_post['company']) > 0) {
                $cond['company_name'] = new MongoRegex('/' . trim($arr_post['company']) . '/i');
            }
        } else {
        	$company = $this->opm->select_one(array('_id'=> new MongoId($this->get_id())),array('company_name'));
        	if(isset($company['company_name'])&&$company['company_name']!=''){
        		$company['company_name'] = str_replace(array('(',')'), '*.*', $company['company_name']);
                $cond['company_name'] = new MongoRegex('/' . trim($company['company_name']) . '/i');
                $this->set('company_name',$company['company_name']);
        	}
        }
        $this->selectModel('Setting');
		$this->set('arr_invoice_status', $this->Setting->select_option(array('setting_value' => 'salesinvoices_status'), array('option')));
        $this->selectModel('Salesinvoice');
        $arr_salesinvoices = $this->Salesinvoice->select_all(array(
            'arr_where' => $cond,
            'arr_order' => $arr_order,
            'arr_field' => array( 'code', 'invoice_date', 'company_id', 'company_name', 'invoice_status'),
            'limit' => $limit,
            'skip' => $skip
        ));
        $this->set('arr_salesinvoices', $arr_salesinvoices);

        $total_page = $total_record = $total_current = 0;
        if (is_object($arr_salesinvoices)) {
            $total_current = $arr_salesinvoices->count(true);
            $total_record = $arr_salesinvoices->count();
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

    function get_batch_invoices(){
    	$arr_post = $this->data;
    	$arr_data = array();
    	if(isset($arr_post['batch_salesinvoices']) && !empty($arr_post['batch_salesinvoices'])){
    		$this->selectModel('Company');
    		$this->selectModel('Emailtemplate');
    		$this->selectModel('Communication');
    		$i = 0;
    		foreach($arr_post['batch_salesinvoices'] as $id=>$value){
    			if(strlen($id)!=24) continue;
    			$invoice = $this->opm->select_one(array('_id'=>new MongoId($id)),array('company_id','company_name','email','code','invoice_status'));
    			if(!isset($invoice['company_id']) || !is_object($invoice['company_id'])) continue;
    			$email = $this->Emailtemplate->select_one(array('name' => 'Batch Invoices'),array('template'));
    			$arr_data[(string)$invoice['company_id']]['name'] = $invoice['company_name'];
    			$arr_data[(string)$invoice['company_id']]['_id'] = $invoice['company_id'];
    			$arr_data[(string)$invoice['company_id']]['content'] = $email['template'];
    			$arr_data[(string)$invoice['company_id']]['invoices'][$i] = array(
    						'_id'				=> $id,
    						'code'				=> $invoice['code'],
    						'invoice_status' 	=> $invoice['invoice_status'],
    				);
    			if($invoice['invoice_status'] == 'Invoiced'){
    				$comm = $this->Communication->select_one(array('module_id' => new MongoId($id),'comms_status' => 'Sent'),array('date_modified'),array('_id' => -1));
    				if(isset($comm['_id'])){
    					$arr_data[(string)$invoice['company_id']]['invoices'][$i]['sent'] = true;
    					$arr_data[(string)$invoice['company_id']]['invoices'][$i]['sent_mail'] = $comm['_id'];
    					$arr_data[(string)$invoice['company_id']]['invoices'][$i]['sent_date'] = $this->opm->format_date($comm['date_modified'], true);
    				}
    			}
    			++$i;
    		}
    	}
    	$this->set('arr_data',$arr_data);
    }

    function batch_invoices(){
    	$arr_post = $this->data;
    	if(isset($arr_post['batch_salesinvoices']) && !empty($arr_post['batch_salesinvoices'])){
    		$this->selectModel('Company');
    		$this->selectModel('Contact');
    		$this->selectModel('Job');
    		$this->selectModel('Doc');
    		$this->selectModel('DocUse');
    		$company = $comms = array();
    		$user_id = $this->opm->user_id();
    		$user_name = $this->opm->user_name();
    		if(IS_LOCAL){
    			foreach($arr_post['batch_salesinvoices'] as $company_id=>$value){
    				if(strlen($company_id)!=24) continue;
    				foreach($value['invoices'] as $invoice){
    					if($invoice['invoice_status'] == 'Invoiced' && !isset($invoice['resend'])) continue;
		    			$query = $this->opm->select_one(array('_id'=>new MongoId($invoice['_id'])),array('company_id','company_name','email','code','contact_name','contact_id','job_id'));
		    			if(!isset($query['company_id']) || !is_object($query['company_id'])) continue;
		    			if(!isset($query['contact_id']) || !is_object($query['contact_id'])){
		    				if(isset($query['contact_name']) && $query['contact_name']){
		    					$contact_name = explode(' ', trim($query['contact_name']));
		    					$contact = $this->Contact->select_one(array('first_name' => $contact_name[0], 'last_name' => $contact_name[1], 'company_id' => $query['company_id']),array('_id'));
		    					if(isset($contact['_id']))
		    						$query['contact_id'] = $contact['_id'];
		    				}
		    			}
		    			$company[(string)$query['company_id']]['company_name'] = $query['company_name'];
		    			$company[(string)$query['company_id']]['content'] = $value['content'];
		    			$company[(string)$query['company_id']]['invoices'][] = array(
		    				'salesinvoice_id'	=>	$invoice['_id'],
		    				'email'				=>	(isset($query['email']) ? $query['email'] : ''),
		    				'code'				=>	$query['code'],
		    				'contact_id'		=>	isset($query['contact_id']) ? $query['contact_id'] : '',
		    				'job_id'			=>	isset($query['job_id']) ? $query['job_id'] : '',
		    				);
    				}
    			}
    		} else {
    			foreach($arr_post['batch_salesinvoices'] as $id=>$value){
	    			if(strlen($id)!=24) continue;
	    			$company_id = $this->opm->select_one(array('_id'=>new MongoId($id)),array('company_id','company_name','email','code','contact_name','contact_id','job_id'));
	    			if(!isset($company_id['company_id']) || !is_object($company_id['company_id'])) continue;
	    			if(!isset($company_id['contact_id']) || !is_object($company_id['contact_id'])){
	    				if(isset($company_id['contact_name']) && $company_id['contact_name']){
	    					$contact_name = explode(' ', trim($company_id['contact_name']));
	    					$contact = $this->Contact->select_one(array('first_name' => $contact_name[0], 'last_name' => $contact_name[1], 'company_id' => $company_id['company_id']),array('_id'));
	    					if(isset($contact['_id']))
	    						$company_id['contact_id'] = $contact['_id'];
	    				}
	    			}
	    			$company[(string)$company_id['company_id']]['company_name'] = $company_id['company_name'];
	    			$company[(string)$company_id['company_id']]['invoices'][] = array('salesinvoice_id'=>$id,'email'=>(isset($company_id['email']) ? $company_id['email'] : ''),'code'=>$company_id['code'],'contact_id'=>isset($company_id['contact_id']) ? $company_id['contact_id'] : '','job_id'=>isset($company_id['job_id']) ? $company_id['job_id'] : '');
	    		}
    		}
    		$check = false;
    		$arr_postal_company = array();
    		if(IS_LOCAL)
    			$check = true;
    		foreach($company as $company_id=>$value){
    			if($check){
    				$check_company = $this->Company->select_one(array('_id' => new MongoId($company_id), 'postal_mail_only' => 1), array('_id'));
    				if(isset($check_company['_id'])){
    					$arr_postal_company[] = array(
    									'company_name' => $value['company_name'],
    									'invoices'	=> $value['invoices']
    						);
    					continue;
    				}
    			}
    			$contact = $this->Contact->select_one(
    			                           array(
	    			                           'company_id'=>new MongoId($company_id),
	    			                           'position'=>'Account Payable',
	    			                           'email'=>array('$ne'=>'')
	    			                        ),
    										array('email','full_name','first_name','last_name')
    									);
    			if(!isset($contact['_id'])){
    				$co = $this->Company->select_one(array('_id'=>new MongoId($company_id)),array('contact_default_id'));
    				if(isset($co['contact_default_id']) && is_object($co['contact_default_id'])){
    					$contact = $this->Contact->select_one(
    			                           array(
	    			                           '_id'=>$co['contact_default_id'],
	    			                           'email'=>array('$ne'=>'')
	    			                        ),
    										array('email','full_name','first_name','last_name')
    									);
    				}
    			}
    			$arr_email = array();
    			foreach($value['invoices'] as $salesinvoice){
    				$salesinvoice_id = $salesinvoice['salesinvoice_id'];
    				$email = '';
    				/*if(filter_var($salesinvoice['email'], FILTER_VALIDATE_EMAIL))
    					$email = $salesinvoice['email'];
    				else */if(isset($contact['email']) && filter_var($contact['email'], FILTER_VALIDATE_EMAIL))
    					$email = $contact['email'];
    				else if(isset($salesinvoice['contact_id']) && is_object($salesinvoice['contact_id'])){
    					$contact = $this->Contact->select_one(
    			                           array(
	    			                           '_id'=>$salesinvoice['contact_id'],
	    			                           'email'=>array('$ne'=>'')
	    			                        ),
    										array('email','full_name','first_name','last_name')
    									);
    					if(isset($contact['email']))
    						$email = $contact['email'];
    				}
    				if( !filter_var($email, FILTER_VALIDATE_EMAIL)) continue;
    				$this->opm->save(array('_id'=>new MongoId($salesinvoice_id),'invoice_status'=>'Invoiced','other_comment'=>'Emailed '.date('d M, Y')));
    				if(isset($salesinvoice['job_id']) && is_object($salesinvoice['job_id'])){
    					$this->Job->save(array('_id'=>$salesinvoice['job_id'],'status'=>'Completed','status_id'=>'Completed'));
    				}
    				$file = $this->view_pdf(true,'group',$salesinvoice_id);
	    			if(!isset($arr_email[$email]['email_info'])){
	    				$cc_emails = array('jobtraq@anvydigital.com');
	    				$arr_data = array(
	    			                  'contact_name'=> $contact['first_name'].' '.$contact['last_name'],
	    			                  'payable_contact'=> $contact['first_name'].' '.$contact['last_name'],
	    			                  'contact_id'	=> $contact['_id'],
	    			                  'company_name'=> $value['company_name'],
	    			                  'company_id'	=> new MongoId($company_id),
	    			                  'to'			=> $email,
	    			                  'subject'		=> 'Anvy Digital - Batch Invoice',
	    			                  'template_name' 	=> 'Batch Invoices',
	    			                  'no_save_comms'	=> true
	    			                  );
	    				if(isset($value['content'])){
	    					$arr_data['template'] = $value['content'];
	    				} else
	    					$arr_data['template'] = $this->get_email_template($arr_data);
    					$arr_email[$email]['email_info'] = $arr_data;
    				}
    				if(isset($_POST['cc_to_contact'])){
    					if(isset($salesinvoice['contact_id']) && is_object($salesinvoice['contact_id'])){
    						$cc_contact = $this->Contact->select_one(array('_id'=>$salesinvoice['contact_id'],'email'=>array('$ne'=>$email)),array('email'));
    						if(isset($cc_contact['email'])
    						   && !in_array($cc_contact['email'], $cc_emails)
    						   && filter_var($cc_contact['email'], FILTER_VALIDATE_EMAIL))
    							$cc_emails[] = $cc_contact['email'];
    					}
    				}
    				if(!isset($cc_emails))
    					$cc_emails = array();
					$arr_email[$email]['email_info']['cc'] = $cc_emails;
	    			if(isset($file)){
	    				$arr_email[$email]['email_info']['attachments'][] = APP.WEBROOT_DIR.DS.'upload'.DS.$file;
	    			}
	    			$arr_email[$email]['salesinvoices'][] = array(
	    			                                              '_id'=>new MongoId($salesinvoice_id),
	    			                                              'file'=>(isset($file) ? $file : ''),
	    			                                              );
	    			$arr_email[$email]['salesinvoice_code'][] = $salesinvoice['code'];
    			}
    			$arr_email_cp = new ArrayIterator($arr_email);
    			unset($arr_email);
    			foreach($arr_email_cp as $email => $data){
    				if(isset($data['email_info']['attachments']) && count($data['email_info']['attachments']) > 5){
						$attachments = array_chunk($data['email_info']['attachments'], 5, true);
						$salesinvoices = array_chunk($data['salesinvoices'], 5, true);
						$salesinvoice_code = array_chunk($data['salesinvoice_code'], 5, true);
						$arr_email_cp[$email]['email_info']['attachments'] = $data['email_info']['attachments'] = $attachments[0];
						$arr_email_cp[$email]['salesinvoices'] = $data['salesinvoices'] = $salesinvoices[0];
						$arr_email_cp[$email]['salesinvoice_code'] = $data['salesinvoice_code'] = $salesinvoice_code[0];
						unset($attachments[0],$salesinvoices[0],$salesinvoice_code[0]);
						foreach($attachments as $key => $value){
							$info = $arr_email_cp[$email];
							$info['email_info']['attachments'] = $value;
							$info['salesinvoices'] = $salesinvoices[$key];
							$info['salesinvoice_code'] = $salesinvoice_code[$key];
							$arr_email_cp->append( $info);
							unset($info);
						}
						unset($attachments,$salesinvoices,$salesinvoice_code);
    				}
					$salesinvoice_code = rtrim(implode(', ', $data['salesinvoice_code']),', ');
    				$arr_data = $data['email_info'];
    				$arr_data['subject'].= ' '.$salesinvoice_code;
    				$arr_data['from'] = $user_name;
    				$arr_data['from_id'] = new MongoId($user_id);
    				$arr_data['prior'] = 1;
    				$this->selectModel('Mailqueue');
    				$this->Mailqueue->add($arr_data);
    				/*$this->auto_send_email($arr_data);*/
    				$arr_salesinvoices = $data['salesinvoices'];
    				$option = array(
									'not_redirect'	=>	true,
									'name'			=>	$arr_data['subject'],
									'content'		=>	$arr_data['template'],
									'comms_status'	=> 	'Sent',
									'no_use_signature' => true,
									'contact_name'	=>	$arr_data['contact_name'],
									'contact_id'	=>	$arr_data['contact_id'],
									'email'			=>	$arr_data['to'],
									'contact_from'	=> 	$this->opm->user_name(),
									'identity'		=> 	'Auto Send',
									'sign_off'		=> 	'',
									'email_cc'		=>	rtrim(implode('; ', $arr_data['cc']),'; ')
									);
    				foreach($arr_salesinvoices as $invoice){
    					$this->add_from_module($invoice['_id'],'Email',$option);
    					$comms[] = $comms_id = $this->Communication->mongo_id_after_save;
    					if($invoice['file']=='') continue;
    					$file = $invoice['file'];
						$arr_save = array(
								'deleted' 			=>	false,
								'no'				=>	$this->Doc->get_auto_code('no'),
								'create_by_module'	=>	'Sales invoice',
								'path'				=>	DS.'upload'.DS.$file,
								'name'				=>	$file,
								'ext'				=> 'pdf',
								'location'			=>	'Salesinvoice',
								'type'				=> 	'application/pdf',
								'description'		=>	'Created at: '.date("h:m a, M d, Y"),
								'create_by'			=>	new MongoId($this->opm->user_id()),
								);
						$this->Doc->save($arr_save);
						$doc_id = $this->Doc->mongo_id_after_save;
						$arr_save = array(
							'deleted'		=> false,
							'controller'	=> 'communications',
							'module'		=> 'Communication',
							'module_no'		=> '',
							'doc_id'		=> new MongoId($doc_id),
							'module_id'		=> new MongoId($comms_id),
							'created_by'	=> new MongoId($this->opm->user_id()),
							);
						$this->DocUse->save($arr_save);
    				}
    			}
    		}
			if(!empty($arr_postal_company)){
				$html = '';
				foreach($arr_postal_company as $company){
					$html .= "<span>{$company['company_name']} has requested to be send postal mail only, so invoices ";
					foreach($company['invoices'] as $invoice)
						$html .= $invoice['code'].', ';
					$html = rtrim($html,', ');
					$html .= ' will not be sent.<span><br />';
				}
				$this->Session->setFlash($html,'default',array('class'=>'flash_message'));
			}
    		if(!empty($comms)){
    			$this->background_process('sendmail','cake_console');
				echo json_encode($comms);
    		}
			else
				echo 'No payable contact found.';
    	} else
    		echo 'You must choose at least one Sales invoice.';
    	die;
    }
    function batch_invoices_pdf_only(){
    	$arr_data = $this->data;
    	if(isset($arr_data['batch_salesinvoices']) && !empty($arr_data['batch_salesinvoices'])){
    		$send = false;
    		foreach($arr_data['batch_salesinvoices'] as $id=>$value){
    			if(strlen($id)!=24) continue;
    			$query = $this->opm->select_one(array('_id'=>new MongoId($id)),array('_id'));
    			if(!isset($query['_id'])) continue;
    			$send = true;
    			$this->opm->save(array('_id'=>new MongoId($id),'invoice_status'=>'Invoiced','other_comment'=>'Mail out '.date('d M, Y')));
    		}
    		if($send)
				echo 'ok';
			else
				echo 'Something wrong! Please refresh and try again.';

    	} else
    		echo 'You must choose at least one Sales invoice.';
    	die;
    }
    function outstanding_statement(){
		$salesinvoice = $this->opm->select_one(array('_id'=> new MongoId($this->get_id())),array('company_id'));
    	if(!isset($salesinvoice['company_id']) || !is_object($salesinvoice['company_id'])){
    		echo 'Company was not existed! Please check.';
    		die;
    	}
    	$company_id = $salesinvoice['company_id'];
    	$this->selectModel('Company');
    	$this->selectModel('Salesaccount');
    	$this->selectModel('Receipt');
    	$company = $this->Company->select_one(array('_id'=>$company_id),array('name'));
    	$account = $this->Salesaccount->select_one(array('company_id'=>$company_id),array('payment_terms'));
    	if(!isset($account['payment_terms']))
    		$account['payment_terms'] = 0;
    	$arr_data = array('payment_terms' => $account['payment_terms'], 'company_name' => htmlentities($company['name']), 'invoices' => array() );
	    $current_time = strtotime(date('Y-m-d'));
    	$salesinvoices = $this->opm->select_all(array(
    	                       'arr_where' => array('company_id'=> $company_id,'invoice_status'=>array('$nin'=>array('Cancelled','In Progress','Paid'))),
    	                       'arr_field' => array('invoice_date','code','invoice_type','invoice_status','taxval','customer_po_no','sum_amount','sum_sub_total','total_receipt','payment_due_date', 'shipping_cost'),
    	                       'arr_order' => array('code'=>1)
    	                       ));
    	foreach($salesinvoices as $key=>$salesinvoice){
    		$receipts = $this->Receipt->collection->aggregate(
                array(
                    '$match'=>array(
                                    'company_id' => new MongoId($company_id),
                                    'deleted'=> false
                                    ),
                ),
                array(
                    '$unwind'=>'$allocation',
                ),
                 array(
                    '$match'=>array(
                                    'allocation.deleted'=> false,
                                    'allocation.salesinvoice_id' => $salesinvoice['_id']
                                    )
                ),
                array(
                    '$project'=>array('allocation'=>'$allocation')
                ),
                array(
                    '$group'=>array(
                                  '_id'=>array('_id'=>'$_id'),
                                  'allocation'=>array('$push'=>'$allocation')
                                )
                )
            );
            $total_receipt = 0;
            if(isset($receipts['ok']) && $receipts['ok']){
                foreach($receipts['result'] as $receipt){
                    foreach($receipt['allocation'] as $allocation)
                        $total_receipt += isset($allocation['amount']) ? (float)str_replace(',', '', $allocation['amount']) : 0;
                }
            }
            $minimum = $this->get_minimum_order( (new MongoId($company_id)) );
    		if($salesinvoice['invoice_type'] != 'Credit' && $salesinvoice['sum_sub_total'] < $minimum)
    			$salesinvoice['sum_amount'] = $minimum + $minimum * (isset($salesinvoice['taxval']) ? (float)$salesinvoice['taxval'] : 0)/100;
    		$salesinvoice['total_receipt'] = $total_receipt;
    		if( $salesinvoice['invoice_type'] == 'Credit' ) {
    			if($salesinvoice['total_receipt'] < 0) {
    				$salesinvoice['total_receipt'] *= -1;
    			}
    			$salesinvoice['balance'] = $salesinvoice['sum_amount'] + $salesinvoice['total_receipt'];
    		} else {
    			$salesinvoice['balance'] = $salesinvoice['sum_amount'] - $salesinvoice['total_receipt'];
    		}
    		if($salesinvoice['balance'] < 0.01 && $salesinvoice['balance'] > -0.5) {
    			continue;
            }
    		if($salesinvoice['invoice_type'] == 'Credit' && $salesinvoice['balance']>0 &&$salesinvoice['sum_amount']> 0){
    			$salesinvoice['balance'] *= -1;
    			$salesinvoice['sum_amount'] *= -1;
    		}
    		$debt = 0;
    		$invoice_date = strtotime(date('Y-m-d',$salesinvoice['invoice_date']->sec));
    		if($current_time - $invoice_date > 90*DAY){
    			$debt = 91;
    		} else if($current_time - $invoice_date > 60*DAY){
    			$debt = 61;
    		} else if($current_time - $invoice_date > 30*DAY ){
    			$debt = 31;
    		} else {
    			$debt = 0;
    		}
    		$sortcode = $salesinvoice['code'];
    		if(isset($salesinvoice['code']))
    			$arr_code = explode("-", $salesinvoice['code']);
    		if(count($arr_code)==3){
    			$sortcode = $arr_code[1].'-'.$arr_code[0].'-'.$arr_code[2];
    		}
    		$arr_data['invoices'][$key] = array(
    		                        'date' 	=> date('m/d/Y',$salesinvoice['invoice_date']->sec),
    		                        'code' 	=> $salesinvoice['code'],
    		                        'sortcode' => $sortcode,
    		                        'type' 	=> $salesinvoice['invoice_type'],
    		                        'status'=>$salesinvoice['invoice_status'],
    		                        'customer_po_no'=> (isset($salesinvoice['customer_po_no']) ? $salesinvoice['customer_po_no'] : ''),
    		                        'balance' 		=> $salesinvoice['balance'],
    		                        'debt' 	=> $debt
    		                        );

    	}
    	usort($arr_data['invoices'], function($a , $b){
    		if( $a['debt']  == $b['debt'])
    			return strcmp($a['sortcode'], $b['sortcode']);
    		return $a['debt'] < $b['debt'];
    	});
    	return $arr_data;
    }
    function outstanding_current_company(){
    	if(!isset($_GET['print_pdf'])){
    		//Use this instead
		    	$this->layout = 'ajax';
		    	$arr_data = $this->outstanding_statement();
		    	$arr_data['render_path'] = '../Salesinvoices/outstanding_statement';
		    	$arr_data['report_name'] = 'Statement Report';
		        $arr_data['report_file_name'] = 'SI_'.md5(time());
		    	$this->set('arr_data',$arr_data);
		    	$this->render('../Salesinvoices/outstanding_statement');
		        Cache::write('outstanding_current_company',$arr_data);
		    	return;
		    //
	    	$salesinvoice = $this->opm->select_one(array('_id'=> new MongoId($this->get_id())),array('company_id'));
	    	if(!isset($salesinvoice['company_id']) || !is_object($salesinvoice['company_id'])){
	    		echo 'Company was not existed! Please check.';
	    		die;
	    	}
	    	$company_id = $salesinvoice['company_id'];
	    	$this->selectModel('Company');
	    	$this->selectModel('Salesaccount');
	    	$company = $this->Company->select_one(array('_id'=>$company_id),array('addresses_default_key','addresses','name'));
	    	$account = $this->Salesaccount->select_one(array('company_id'=>$company_id),array('payment_terms'));
	    	if(!isset($account['payment_terms']))
	    		$account['payment_terms'] = 0;
	    	//Address
	    	$addresses_default_key = (isset($company['addresses_default_key']) ? $company['addresses_default_key'] : 0);
	    	$arr_address = $company['addresses'][$addresses_default_key];
			$address = '';
			if (isset($arr_address['address_1']) && $arr_address['address_1'] != '')
				$address .= $arr_address['address_1'] . ' ';
			if (isset($arr_address['address_2']) && $arr_address['address_2'] != '')
				$address .= $arr_address['address_2'] . ' ';
			if (isset($arr_address['address_3']) && $arr_address['address_3'] != '')
				$address .= $arr_address['address_3'] . '<br />';
			else
				$address .= '<br />';
			if (isset($arr_address['town_city']) && $arr_address['town_city'] != '')
				$address .= $arr_address['town_city'];
			if (isset($arr_address['province_state']))
				$address .= ' ' . $arr_address['province_state'] . ' ';
			else if (isset($arr_address['province_state_id']) && isset($arr_address['country_id'])) {
				$keyarr_address = $arr_address['province_state_id'];
				$provkey = $this->province($arr_address['country_id']);
				if (isset($provkey[$arr_address]))
					$address .= ' ' . $provkey[$arr_address] . ' ';
			}
			if (isset($arr_address['zip_postcode']) && $arr_address['zip_postcode'] != '')
				$address .= $arr_address['zip_postcode'];
			if (isset($arr_address['country']) && isset($arr_address['country_id']) && (int) $arr_address['country_id'] != "CA")
				$address .= ' ' . $arr_address['country'] . '<br />';
			else
				$address .= '<br />';
	    	$arr_pdf['left_info'] = array(
		    	                               'label'=> '',
		    	                               'name'=>(isset($company['name']) ? '<b>'.$company['name'].'</b>' : ''),
		    	                               'address' => $address,
		    	                               );
	    	//eND ADDRESS
	    	$origin_minimum = $this->get_minimum_order();
	    	$salesinvoices = $this->opm->select_all(array(
	    	                       'arr_where' => array('company_id'=> $company_id,'invoice_status'=>array('$nin'=>array('Cancelled','In Progress','Paid'))),
	    	                       'arr_field' => array('invoice_date','code','invoice_type','invoice_status','taxval','customer_po_no','sum_amount','sum_sub_total','total_receipt','payment_due_date', 'shipping_cost'),
	    	                       'arr_order' => array('invoice_date'=>1)
	    	                       ));
	    	$arr_data = array();
	    	$current_time = strtotime(date('Y-m-d'));
	    	$arr_data['debt']['amount_due'] = 0;
			$arr_data['debt']['90_plus'] = 0;
			$arr_data['debt']['60_plus'] = 0;
			$arr_data['debt']['30_plus'] = 0;
			$arr_data['debt']['30'] = 0;
	    	$in_payment_term = 0;
	    	foreach($salesinvoices as $key=>$salesinvoice){
            	$minimum = $origin_minimum;
	    		if($salesinvoice['invoice_type'] != 'Credit' && $salesinvoice['sum_sub_total'] < $minimum)
	    			$salesinvoice['sum_amount'] = $minimum + $minimum * (isset($salesinvoice['taxval']) ? (float)$salesinvoice['taxval'] : 0)/100;
	    		$salesinvoice['total_receipt'] = (isset($salesinvoice['total_receipt']) ? (float)$salesinvoice['total_receipt'] : 0 );
	    		if($salesinvoice['invoice_type'] == 'Credit' ) {
	    			if($salesinvoice['total_receipt'] < 0) {
	    				$salesinvoice['total_receipt'] *= -1;
	    			}
	    			$salesinvoice['balance'] = $salesinvoice['sum_amount'] + $salesinvoice['total_receipt'];
	    		} else {
	    			$salesinvoice['balance'] = $salesinvoice['sum_amount'] - $salesinvoice['total_receipt'];
	    		}
	    		if($salesinvoice['balance'] < 0.01 && $salesinvoice['balance'] > -0.5)
	    			continue;
	    		if($salesinvoice['invoice_type'] == 'Credit' && $salesinvoice['balance']>0 &&$salesinvoice['sum_amount']> 0){
	    			$salesinvoice['balance'] *= -1;
	    			$salesinvoice['sum_amount'] *= -1;
	    		}
	    		$arr_data[$key] = array(
	    		                        'date' => $this->opm->format_date($salesinvoice['invoice_date']->sec),
	    		                        'code' => $salesinvoice['code'],
	    		                        'type' => $salesinvoice['invoice_type'],
	    		                        'status'=>$salesinvoice['invoice_status'],
	    		                        'po_no' => (isset($salesinvoice['customer_po_no']) ? $salesinvoice['customer_po_no'] : ''),
	    		                        'amount' => $salesinvoice['sum_amount'],
	    		                        'paid' => $salesinvoice['total_receipt'],
	    		                        'balance' => $salesinvoice['balance'],
	    		                        );
	    		$invoice_date = strtotime(date('Y-m-d',$salesinvoice['invoice_date']->sec));
	    		if($current_time - $invoice_date > 90*DAY){
	    			$arr_data['debt']['90_plus'] += $salesinvoice['balance'];
	    		} else if($current_time - $invoice_date > 60*DAY){
	    			$arr_data['debt']['60_plus'] += $salesinvoice['balance'];
	    		} else if($current_time - $invoice_date > 30*DAY ){
	    			$arr_data['debt']['30_plus'] += $salesinvoice['balance'];
	    		} else {
	    			$arr_data['debt']['30'] += $salesinvoice['balance'];
	    		}

	    		if($current_time - $invoice_date <= $account['payment_terms']*DAY){
	    			$in_payment_term += $salesinvoice['balance'];
	    		}
	    	}
	    	// if($account['payment_terms'] > 90)
	    	// 	$arr_data['debt']['total'] = $arr_data['debt']['90_plus'];
	    	// else if($account['payment_terms'] > 60)
	    	// 	$arr_data['debt']['total'] = $arr_data['debt']['60_plus'];
	    	// else if($account['payment_terms'] > 30)
	    	// 	$arr_data['debt']['total'] = $arr_data['debt']['30_plus'];
	    	// else
	    	// 	$arr_data['debt']['total'] = $arr_data['debt']['30'];
	    	$arr_data['debt']['total'] = $arr_data['debt']['90_plus'] + $arr_data['debt']['60_plus'] +  $arr_data['debt']['30_plus'] + $arr_data['debt']['30'];
	    	$arr_data['debt']['amount_due'] = $arr_data['debt']['total'] - $in_payment_term;
	    	$html = '
	            <table class="table_content">
	               <tbody>
	                  <tr class="tr_right_none" style="background: #911b12;color: white;height: 29px;line-height: 29px;font-weight: bold;">
	                     <td width="15%" class="center_text">
	                        Date
	                     </td>
	                     <td width="10%">
	                        Ref no
	                     </td>
	                     <td width="10%" class="center_text">
	                        Type
	                     </td>
	                     <td >
	                        PO no
	                     </td>
	                     <td class="right_text">
	                        Amount CAD
	                     </td>
	                     <td class="right_text">
	                        Balance CAD
	                     </td>
	                  </tr>';
	        $arr_debt = $arr_data['debt'];
	        unset($arr_data['debt']);
	        $i = 0;
	        foreach($arr_data as $value){
	        	$html .= '
	                  <tr class="content_asset bg_' . ($i % 2 == 0 ? '1' : '2') . '">
	                     <td class="center_text">' . $value['date'] . '</td>
	                     <td>' . $value['code'] . '</td>
	                     <td class="center_text">' . $value['type'] . '</td>
	                     <td>' . $value['po_no'] . '</td>
	                     <td class="right_text">' . $this->opm->format_currency($value['amount']) . '</td>
	                     <td class="right_text">' . $this->opm->format_currency($value['balance']) . '</td>
	                  </tr>';
	        	$i++;
	        }
	        $html .=   '</tbody>
	            </table>
	            <br />';
	        $html .= '<table class="bordered" style="width: 100%;">
	        			<tr>
	        				<th style="color: white;height: 29px;line-height: 29px;font-weight: bold;" width="80%">
	        					<table class="table_color_white" style="width: 100%;">
	        						<tr>
	        							<td colspan="5" class="center_text">Aged Debt Analysis</td>
	        						</tr>
	        						<tr>
	        							<td class="right_text">0-30 days</td>
	        							<td class="right_text">30+ days</td>
	        							<td class="right_text">60+ days</td>
	        							<td class="right_text">90+ days</td>
	        							<td class="right_text">Total CAD</td>
	        						</tr>
	        						<tr style="background: #fff; color: #000; height: 35px;">
	        							<td class="right_text">'.(isset($arr_debt['30']) ? $this->opm->format_currency($arr_debt['30']) : '').'</td>
	        							<td class="right_text">'.(isset($arr_debt['30_plus']) ? $this->opm->format_currency($arr_debt['30_plus']) : '').'</td>
	        							<td class="right_text">'.(isset($arr_debt['60_plus']) ? $this->opm->format_currency($arr_debt['60_plus']) : '').'</td>
	        							<td class="right_text">'.(isset($arr_debt['90_plus']) ? $this->opm->format_currency($arr_debt['90_plus']) : '').'</td>
	        							<td class="right_text">'.(isset($arr_debt['total']) ? $this->opm->format_currency($arr_debt['total']) : '').'</td>
	        						</tr>
	        					</table>
	        				</th>
	        				<th>&nbsp;</th>
	        				<th style="background: #757575;color: white;height: 29px;line-height: 29px;font-weight: bold;">
	        					<table class="table_color_white" style="width: 100%;">
	        						<tr>
	        							<td class="right_text">CAD</td>
	        						</tr>
	        						<tr>
	        							<td class="right_text">Amount due</td>
	        						</tr>
	        						<tr style="background: #fff; color: #000; height: 35px;">
	        							<td class="right_text">'.(isset($arr_debt['amount_due']) ? $this->opm->format_currency($arr_debt['amount_due']) : '').'</td>
	        						</tr>
	        					</table>
	        				</th>
	        			</tr>
	        		</table>
	        		<br />
	        		<div class="bold_text">Outstanding balance reflect any payments received as of '.date('d M, Y').'. Please ignore this message if a rececent payment has been made for any outstanding invoices.</div>
	        		<br />';
	        //================================================================
	       	$arr_pdf['report_heading'] = 'Payment Term : '.$account['payment_terms'].' day(s)';
	        $arr_pdf['content'][]['html'] = $html;
	        $arr_pdf['is_custom'] = true;
	        $arr_pdf['image_logo'] = true;
	        $arr_pdf['report_name'] = 'Statement Report';
	        $arr_pdf['report_file_name'] = 'SI_'.md5(time());
	        Cache::write('outstanding_current_company',$arr_pdf);
	    } else {
	    	$arr_pdf = Cache::read('outstanding_current_company');
	    	Cache::delete('outstanding_current_company');
	    }
        $this->render_pdf($arr_pdf);
    }
    function check_different_from_salesorder(){
    	$query = $this->opm->select_one(array('_id'=>new MongoId($this->get_id())),array('salesorder_id','sum_sub_total','salesorders'));
    	if(isset($query['salesorders']) && !empty($query['salesorders'])){
    		echo 'ok';
    		die;
    	}
    	if(!isset($query['salesorder_id']) || !is_object($query['salesorder_id'])){
    		echo 'ok';
    		die;
    	}
    	if($query['sum_sub_total'] == 0){
    		echo 'ok';
    		die;
    	}
    	$this->selectModel('Salesorder');
    	$salesorder = $this->Salesorder->select_one(array('_id'=> new MongoId($query['salesorder_id'])),array('sum_sub_total'));
    	if(!isset($salesorder['_id'])){
    		echo 'ok';
    		die;
    	}
    	$salesorder['sum_sub_total'] = (float)$salesorder['sum_sub_total'];
    	$query['sum_sub_total'] = (float)$query['sum_sub_total'];
    	if(abs(($salesorder['sum_sub_total'] - $query['sum_sub_total']) / ($salesorder['sum_sub_total']==0? 1 :$salesorder['sum_sub_total']) ) > 0.001){
    		echo $salesorder['sum_sub_total'];
    		die;
    	}
    	echo 'ok';
    	die;
    }

    function combine_invoices_popup($key = ''){
    	$this->set('key',$key);
    	$id = $this->get_id();
    	$invoice = $this->opm->select_one(array('_id'=> new MongoId($id)),array('job_id'));
    	if(!isset($invoice['job_id']) || !is_object($invoice['job_id'])){
    		echo '<span style="padding: 36%; font-weight: bold;font-size: 20px;">This invoice has no Job</span>';
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
        	$arr_order = array('invoice_date' => -1);
        }
        // search theo submit $_POST kèm điều kiện
        $cond['job_id'] = $invoice['job_id'];
        $cond['_id'] = array('$ne' => $invoice['_id']);
        if (!empty($this->data) && !empty($_POST) && isset($this->data['Salesinvoice'])) {
            $arr_post = $this->Common->strip_search($this->data['Salesinvoice']);
            if (isset($arr_post['code']) && strlen($arr_post['code']) > 0) {
                $cond['code'] = new MongoRegex('/' . trim($arr_post['code']) . '/i');
            }

            if (isset($arr_post['invoice_status']) && strlen($arr_post['invoice_status']) > 0) {
             	$this->set('invoice_status',$arr_post['invoice_status']);
                $cond['invoice_status'] = new MongoRegex('/' . trim($arr_post['invoice_status']) . '/i');
            }
            if (isset($arr_post['company']) && strlen($arr_post['company']) > 0) {
                $cond['company_name'] = new MongoRegex('/' . trim($arr_post['company']) . '/i');
            }
        }
        $this->selectModel('Setting');
		$this->set('arr_invoice_status', $this->Setting->select_option(array('setting_value' => 'salesinvoices_status'), array('option')));
        $this->selectModel('Salesinvoice');
        $arr_salesinvoices = $this->Salesinvoice->select_all(array(
            'arr_where' => $cond,
            'arr_order' => $arr_order,
            'arr_field' => array('heading', 'code', 'job_number', 'job_name', 'invoice_date', 'company_id', 'company_name','invoice_status'),
            'limit' => $limit,
            'skip' => $skip,
        ));
        $this->set('arr_salesinvoices', $arr_salesinvoices);

        $total_page = $total_record = $total_current = 0;
        if (is_object($arr_salesinvoices)) {
            $total_current = $arr_salesinvoices->count(true);
            $total_record = $arr_salesinvoices->count();
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

    function combine_invoices(){
    	$arr_post = $this->data;
    	if(isset($arr_post['combine_salesinvoices']) && !empty($arr_post['combine_salesinvoices'])){
    		$id = $this->get_id();
    		$invoice = $this->opm->select_one(array('_id'=> new MongoId($id)),array('job_id','products','options'));
	    	if(!isset($invoice['job_id']) || !is_object($invoice['job_id'])){
	    		echo 'This invoice has no Job.';
	    		die;
	    	}
	    	$arr_invoices_id = array();
    		foreach($arr_post['combine_salesinvoices'] as $id=>$value){
    			if(strlen($id)!=24) continue;
    			$arr_invoices_id[] = new MongoId($id);
    		}
    		$arr_invoices = $this->opm->select_all(array(
    		                       'arr_where' => array(
    		                                            '_id' => array('$in'=>$arr_invoices_id)
    		                                            ),
    		                       'arr_order' => array('_id'=>1),
    		                       'arr_field' => array('options','products')
    		                       ));
    		if(!isset($invoice['products']))
    			$invoice['products'] = array();
    		if(!isset($invoice['options']))
    			$invoice['options'] = array();
			$products_num = count($invoice['products']);
			$options_num = count($invoice['options']);
    		foreach($arr_invoices as $value){
    			if(!isset($value['products']))
	    			$value['products'] = array();
	    		if(!isset($value['options']))
	    			$value['options'] = array();
    			$arr_data = $this->build_salesorder($value,$products_num,$options_num);
				$invoice['options'] = array_merge($invoice['options'],$arr_data['options']);
				$invoice['products'] = array_merge($invoice['products'],$arr_data['products']);
				$invoice['salesinvoices'][] = $value['_id'];
				$this->opm->save(array('_id'=>$value['_id'],'deleted'=>true));
    		}
    		$arr_sum = $this->new_cal_sum($invoice['products']);
			$invoice = array_merge($invoice,$arr_sum);
			$this->opm->save($invoice);
			echo 'ok';
    	} else
    		echo 'You must choose at least one Sales invoice.';
    	die;
    }

    public function accounts_receivable_all_outstanding(){
    	if(!isset($_GET['print_pdf'])){
	    	$this->selectModel('Salesinvoice');
	    	$this->selectModel('Receipt');
	        $arr_salesinvoices = $this->Salesinvoice->select_all(array(
	                                                             'arr_where' => array( 'invoice_status' => array('$nin' => array('Cancelled','Paid'))),
	                                                             'arr_field' => array('company_name','invoice_date','invoice_status','payment_due_date','payment_terms','sum_amount','company_id','sum_sub_total','invoice_type','taxval','total_receipt','code','customer_po_no', 'shipping_cost'),
	                                                             'arr_order' => array('invoice_date' => 1),
	                                                             'limit' => 9999999
	                                                             ));
	        $arr_data = array();
	        $current_time = strtotime(date('Y-m-d'));
	        $arr_company = array();

	        foreach($arr_salesinvoices as $key=> $salesinvoice){
	        	if(isset($salesinvoice['company_id'])){
                    if (!isset($salesinvoice['company_id'])
                        || empty($salesinvoice['company_id'])
                        || !is_object($salesinvoice['company_id'])
                        || strlen((string)$salesinvoice['company_id']) != 24) {
                        continue;
                    }
		        	$company_id = (string)$salesinvoice['company_id'];
		        	$arr_temp['code'] =  $salesinvoice['code'];
		        	$arr_temp['type'] =  isset($salesinvoice['invoice_type']) ? $salesinvoice['invoice_type'] : '';
		        	$arr_temp['customer_po_no'] =  isset($salesinvoice['customer_po_no']) ? $salesinvoice['customer_po_no'] : '';
		        	$arr_temp['date'] =  strtotime(date('Y-m-d',$salesinvoice['invoice_date']->sec));

		        	$arr_temp['30'] =  0;
		        	$arr_temp['30_plus'] =  0;
		        	$arr_temp['60_plus'] =  0;
		        	$arr_temp['90_plus'] =  0;

		        	if(!isset($salesinvoice['sum_sub_total'])) $salesinvoice['sum_sub_total'] = 0;
		        	$salesinvoice['balance'] = 0;
		        	//balance
		        	$minimum = $this->get_minimum_order( (new MongoId($company_id)) );
		        	if(isset($salesinvoice['invoice_type'])){
						if($salesinvoice['invoice_type'] != 'Credit' && $salesinvoice['sum_sub_total'] < $minimum)
			    			$salesinvoice['sum_amount'] = $minimum + $minimum * (isset($salesinvoice['taxval']) ? (float)$salesinvoice['taxval'] : 0)/100;
			    	}
			    	if(strlen($company_id) == 24) {
    			    	$receipts = $this->Receipt->collection->aggregate(
    		                array(
    		                    '$match'=>array(
    		                                    'company_id' => new MongoId($company_id),
    		                                    'deleted'=> false
    		                                    ),
    		                ),
    		                array(
    		                    '$unwind'=>'$allocation',
    		                ),
    		                 array(
    		                    '$match'=>array(
    		                                    'allocation.deleted'=> false,
    		                                    'allocation.salesinvoice_id' => $salesinvoice['_id']
    		                                    )
    		                ),
    		                array(
    		                    '$project'=>array('allocation'=>'$allocation')
    		                ),
    		                array(
    		                    '$group'=>array(
    		                                  '_id'=>array('_id'=>'$_id'),
    		                                  'allocation'=>array('$push'=>'$allocation')
    		                                )
    		                )
    		            );
			    	}
		            $total_receipt = 0;
		            if(isset($receipts['ok']) && $receipts['ok']){
		                foreach($receipts['result'] as $receipt){
		                    foreach($receipt['allocation'] as $allocation)
		                        $total_receipt += isset($allocation['amount']) ? (float)str_replace(',', '', $allocation['amount']) : 0;
		                }
		            }
		    		$salesinvoice['total_receipt'] = $total_receipt;//(isset($salesinvoice['total_receipt']) ? (float)$salesinvoice['total_receipt'] : 0 );
		    		if(isset($salesinvoice['sum_amount'])){
		    			if( $salesinvoice['invoice_status'] == 'Credit' ) {
		    				if($salesinvoice['total_receipt'] < 0) {
		    					$salesinvoice['total_receipt'] *= -1;
		    				}
		    				$salesinvoice['balance'] = $salesinvoice['sum_amount'] + $salesinvoice['total_receipt'];
		    			} else {
		    				$salesinvoice['balance'] = $salesinvoice['sum_amount'] - $salesinvoice['total_receipt'];
		    			}
		    		}
		    		if(isset($salesinvoice['invoice_type'])){
			    		if($salesinvoice['invoice_type'] == 'Credit' && $salesinvoice['balance']>0 &&$salesinvoice['sum_amount']> 0){
			    			$salesinvoice['balance'] *= -1;
			    			$salesinvoice['sum_amount'] *= -1;
			    		}
			    	}

                    if($salesinvoice['balance'] < 0.01 && $salesinvoice['balance'] > -0.5) {
                        continue;
                    }

		        	if($current_time - (int)$arr_temp['date'] > 90*DAY){
		    			$arr_temp['90_plus'] += $salesinvoice['balance'];
		    		} else if($current_time - (int)$arr_temp['date'] > 60*DAY){
		    			$arr_temp['60_plus'] += $salesinvoice['balance'];
		    		} else if($current_time - (int)$arr_temp['date'] > 30*DAY ){
		    			$arr_temp['30_plus'] += $salesinvoice['balance'];
		    		} else {
		    			$arr_temp['30'] += $salesinvoice['balance'];
		    		}

		        	$arr_temp['total'] =  $arr_temp['30']+$arr_temp['30_plus']+$arr_temp['60_plus']+$arr_temp['90_plus'];
		        	$payment_due_date = $current_time;
		        	if(isset($salesinvoice['payment_due_date']) && is_object($salesinvoice['payment_due_date'])) {
		        		$payment_due_date =  strtotime(date('Y-m-d',$salesinvoice['payment_due_date']->sec));
		        	}
		        	$invoice_date = $arr_temp['date'];
		        	$payment_terms = (isset($salesinvoice['payment_terms']) ? (float)$salesinvoice['payment_terms'] : 0 );

		        	// if($current_time - $payment_due_date> $payment_terms*DAY)
		        	if($current_time - $invoice_date> $payment_terms*DAY)
		        		$arr_temp['overdue'] =  'Yes';
		        	else
		        		$arr_temp['overdue']  =  '';


		        	$arr_company[$company_id]['invoice_info'][] = $arr_temp;
		        	if(!isset($arr_company[$company_id]['sum_total']))
		        		$arr_company[$company_id]['sum_total'] = 0;
		        	$arr_company[$company_id]['sum_total'] +=$arr_temp['total'];

		        	if(!isset($arr_company[$company_id]['sum_over']))
		        		$arr_company[$company_id]['sum_over'] = 0;
		        	if($arr_temp['overdue'] == 'Yes') {
		        		$arr_company[$company_id]['sum_over'] +=$arr_temp['total'];
		        	}

		        	if(!isset($arr_company[$company_id]['sum_30']))
		        		$arr_company[$company_id]['sum_30'] = 0;
		        	$arr_company[$company_id]['sum_30'] +=$arr_temp['30'];

		        	if(!isset($arr_company[$company_id]['sum_30_plus']))
		        		$arr_company[$company_id]['sum_30_plus'] = 0;
		        	$arr_company[$company_id]['sum_30_plus'] +=$arr_temp['30_plus'];

		        	if(!isset($arr_company[$company_id]['sum_60_plus']))
		        		$arr_company[$company_id]['sum_60_plus'] = 0;
		        	$arr_company[$company_id]['sum_60_plus'] +=$arr_temp['60_plus'];

		        	if(!isset($arr_company[$company_id]['sum_90_plus']))
		        		$arr_company[$company_id]['sum_90_plus'] = 0;
		        	$arr_company[$company_id]['sum_90_plus'] +=$arr_temp['90_plus'];

		        	$arr_company[$company_id]['company_name'] = $salesinvoice['company_name'];
		        }
	        }

	         //pr($arr_company);die;


			$html = '
	            <table class="table_content">
	               <tbody>
	                  <tr class="tr_right_none" style="background: #911b12;color: white;height: 29px;line-height: 29px;font-weight: bold;">
	                     <td class="center_text">
	                        Ref no
	                     </td>
	                     <td width="10%">
	                        Type
	                     </td>
	                     <td width="10%" class="center_text">
	                        Date
	                     </td>
	                     <td>
	                        PO#
	                     </td>
	                     <td class="right_text">
	                        0-30 days
	                     </td>
	                     <td class="right_text">
	                        30+ days
	                     </td>
	                     <td class="right_text">
	                        60+ days
	                     </td>
	                     <td class="right_text">
	                        90+ days
	                     </td>
	                     <td class="right_text">
	                        Total
	                     </td>
	                     <td class="right_text">
	                        Over due
	                     </td>
	                  </tr>';
	        $i = 0;
	        usort($arr_company, function($a , $b){
	        	return $a['company_name'] > $b['company_name'];
	        });
            $totalInvoice = 0;
            $sum30 = $sum30Plus = $sum60Plus = $sum90Plus = 0;
	        foreach($arr_company as $key => $value){
	        	$html .= '<tr style="border-style:solid;background-color:#A9A9A9" class="content_asset bg_' . ($i % 2 == 0 ? '1' : '2') . '">
		                     <td class="text" colspan="10">' . $value['company_name'] . '&nbsp;</td>
		                  </tr>';
		                  $i++;
	        	foreach($value['invoice_info'] as $k => $val){
                    $totalInvoice++;
        			if ($val['30']==0) $val['30']='';
		        	else $val['30'] = number_format($val['30'],2);
		        	if ($val['30_plus']==0) $val['30_plus']='';
		        	else $val['30_plus'] = number_format($val['30_plus'],2);
		        	if ($val['60_plus']==0) $val['60_plus']='';
		        	else $val['60_plus'] = number_format($val['60_plus'],2);
		        	if ($val['90_plus']==0) $val['90_plus']='';
		        	else $val['90_plus'] = number_format($val['90_plus'],2);
		        	$html .= '
		                  <tr class="content_asset bg_' . ($i % 2 == 0 ? '1' : '2') . '">
		                     <td class="center_text">' . $val['code'] . '</td>
		                     <td class="center_text">' . $val['type'] . '</td>
		                     <td class="center_text">' . $this->opm->format_date($val['date']) . '</td>
		                     <td>' . $val['customer_po_no'] . '</td>
		                     <td class="right_text">' . $val['30'] . '</td>
		                     <td class="right_text">' . $val['30_plus'] . '</td>
		                     <td class="right_text">' . $val['60_plus'] . '</td>
		                     <td class="right_text">' . $val['90_plus'] . '</td>
		                     <td class="right_text">' . number_format($val['total'],2) . '</td>
		                     <td class="right_text">' . $val['overdue'] . '</td>
		                  </tr>';
		        	$i++;
		        }

                $sum30 += $value['sum_30'];

		        if ($value['sum_30']==0) $value['sum_30']='';
	        	else $value['sum_30'] = number_format($value['sum_30'],2);

                $sum30Plus += $value['sum_30_plus'];

	        	if ($value['sum_30_plus']==0) $value['sum_30_plus']='';
	        	else $value['sum_30_plus'] = number_format($value['sum_30_plus'],2);

                $sum60Plus += $value['sum_60_plus'];

	        	if ($value['sum_60_plus']==0) $value['sum_60_plus']='';
	        	else $value['sum_60_plus'] = number_format($value['sum_60_plus'],2);

                $sum90Plus += $value['sum_90_plus'];

	        	if ($value['sum_90_plus']==0) $value['sum_90_plus']='';
	        	else $value['sum_90_plus'] = number_format($value['sum_90_plus'],2);


		        $html .= '<tr class="content_asset bg_' . ($i % 2 == 0 ? '1' : '2') . '">
		        			<td class="center_text" colspan="4"></td>
		                     <td class="right_text bold_text">' . $value['sum_30'] . '</td>
		                     <td class="right_text bold_text" >' . $value['sum_30_plus'] . '</td>
		                     <td class="right_text bold_text">' . $value['sum_60_plus'] . '</td>
		                     <td class="right_text bold_text">' . $value['sum_90_plus'] . '</td>
		                     <td class="right_text bold_text">' . number_format($value['sum_total'],2) . '</td>
		                     <td class="right_text bold_text">' . number_format($value['sum_over'],2) . '</td>
		                  </tr>';
		                  $i++;
		         $html .= '<tr class="content_asset bg_' . ($i % 2 == 0 ? '1' : '2') . '">
		        			<td class="center_text" colspan="10">&nbsp;</td>
		                  </tr>';
		                  $i++;
	        }

	        $html .=   '
                        <tr style="border-style:solid;background-color:#911b12; color: #fff;">
                            <td colspan="4" style="border: none;"></td>
                            <td class="right_text" style="border: none;">Current</td>
                            <td class="right_text" style="border: none;" >30+ days</td>
                            <td class="right_text" style="border: none;">60+ days</td>
                            <td class="right_text" style="border: none;">90+ days</td>
                            <td class="right_text" style="border: none;" colspan="2">Total</td>
                        </tr>
                        <tr class="content_asset bg_1">
                            <td colspan="4" style="border: none;">'. $totalInvoice .' record(s) listed.</td>
                            <td class="right_text">' . number_format($sum30, 2) . '</td>
                            <td class="right_text" >' . number_format($sum30Plus, 2) . '</td>
                            <td class="right_text">' . number_format($sum60Plus, 2) . '</td>
                            <td class="right_text">' . number_format($sum90Plus, 2) . '</td>
                            <td class="right_text" colspan="2">'. number_format($sum30 + $sum30Plus + $sum60Plus + $sum90Plus, 2) .'</td>
                        </tr>
                    </tbody>
	            </table>
	            <br />';
	        $arr_pdf['content'][]['html'] = $html;
	        $arr_pdf['is_custom'] = true;
	        $arr_pdf['image_logo'] = true;
	        $arr_pdf['report_name'] = 'Accounts Receivable All Outstanding';
	        $arr_pdf['report_file_name'] = 'SI_'.md5(time());
	        $arr_pdf['excel_url'] = URL.'/salesinvoices/accounts_receivable_excel';
	        Cache::write('accounts_receivable_all_outstanding',$arr_pdf);
	        Cache::write('accounts_receivable_all_outstanding_excel',$arr_company);
    	}else {
    		$arr_pdf = Cache::read('accounts_receivable_all_outstanding');
    		Cache::delete('accounts_receivable_all_outstanding');
    	}
        $this->render_pdf($arr_pdf);
    }

    function accounts_receivable_excel(){
    	$arr_data = Cache::read('accounts_receivable_all_outstanding_excel');
        Cache::delete('accounts_receivable_all_outstanding_excel');
        if(!$arr_data){
            echo 'No data';die;
        }
        App::import('Vendor', 'phpexcel/PHPExcel');
        $objPHPExcel = new PHPExcel();
        // Set document properties
        $objPHPExcel->getProperties()->setCreator("")
                                     ->setLastModifiedBy("")
                                     ->setTitle('Accounts Receivable All Outstanding')
                                     ->setSubject('Accounts Receivable All Outstanding')
                                     ->setDescription('Accounts Receivable All Outstanding')
                                     ->setKeywords('Accounts Receivable All Outstanding')
                                     ->setCategory('Accounts Receivable All Outstanding');
        $worksheet = $objPHPExcel->getActiveSheet();
        $i = 2;
        $worksheet->setCellValue("A$i",'Ref no')
                        ->setCellValue("B$i",'Type')
                        ->setCellValue("C$i",'Date')
                        ->setCellValue("D$i",'PO#')
                        ->setCellValue("E$i",'0-30 days')
                        ->setCellValue("F$i",'30+ days')
                        ->setCellValue("G$i",'60+ days')
                        ->setCellValue("H$i",'90+ days')
                        ->setCellValue("I$i",'Total')
                        ->setCellValue("J$i",'Over ');
   		$styleArray = array(
            'fill' => array(
	            'type' => PHPExcel_Style_Fill::FILL_SOLID,
	            'color' => array('rgb' => '538DD5')
	        ),
            'font'  => array(
                'size'  => 12,
                'name'  => 'Century Gothic',
                'bold'  => true,
                'color' => array('rgb' => 'FFFFFF'),
            )
        );
        $worksheet->getStyle("A$i:J$i")->applyFromArray($styleArray);
        $worksheet->freezePane('K3');
       	foreach($arr_data as $data){
       		$i++;
       		$worksheet->setCellValue("A$i", $data['company_name'])
       					->mergeCells("A$i:J$i");
       		$worksheet->getStyle("A$i:J$i")->applyFromArray(array(
											                'fill' => array(
													            'type' => PHPExcel_Style_Fill::FILL_SOLID,
													            'color' => array('rgb' => '4BACC6')
													        ),
													        'font'  => array(
												                'bold'  => true,
												                'color' => array('rgb' => 'FFFFFF'),
												            )
												        ));
       		$from = $i+1;
       		foreach($data['invoice_info'] as $invoice) {
       			$i++;
       			$worksheet->setCellValue("A$i", $invoice['code'])
       			                ->setCellValue("B$i", $invoice['type'])
       			                ->setCellValue("C$i", $this->opm->format_date($invoice['date']))
       			                ->setCellValue("D$i", $invoice['customer_po_no'])
       			                ->setCellValue("E$i", $invoice['30'])
       			                ->setCellValue("F$i", $invoice['30_plus'])
       			                ->setCellValue("G$i", $invoice['60_plus'])
       			                ->setCellValue("H$i", $invoice['90_plus'])
       			                ->setCellValue("I$i", $invoice['total'])
       			                ->setCellValue("J$i", $invoice['overdue']);
       		}
       		$i++;
       		$worksheet->setCellValue("E$i", $data['sum_30'])
       			                ->setCellValue("F$i", $data['sum_30_plus'])
       			                ->setCellValue("G$i", $data['sum_60_plus'])
       			                ->setCellValue("H$i", $data['sum_90_plus'])
       			                ->setCellValue("I$i", $data['sum_total'])
       			                ->setCellValue("J$i", $data['sum_over'])
       			                ->mergeCells("A$i:D$i");
       		$worksheet->getStyle("E$from:I$i")->getNumberFormat()->setFormatCode("#,##0.00");
       		$worksheet->getStyle("E$i:I$i")->applyFromArray(array(
													        'font'  => array(
												                'bold'  => true,
												            )
												        ));
       		$worksheet->getStyle("J$i")->getNumberFormat()->setFormatCode("#,##0.00");
       		$worksheet->getStyle("J$i")->applyFromArray(array(
													        'font'  => array(
												                'bold'  => true,
												            )
												        ));
       		$i++;
       		$worksheet->mergeCells("A$i:J$i");
       	}
	    $worksheet->getStyle("A3:J$i")->applyFromArray(array(
											                'borders' => array(
											                   'allborders' => array(
											                       'style' => PHPExcel_Style_Border::BORDER_THIN
											                    )
											                ),
											                'font'  => array(
											                    'size'  => 11,
											                    'name'  => 'Century Gothic',
											                )
												        ));
	    $worksheet->getStyle("C2:C$i")->applyFromArray(array(
													        'alignment'  => array(
												                'horizontal'  => PHPExcel_Style_Alignment::HORIZONTAL_LEFT,
												            )
												        ));
	    $worksheet->getStyle("J2:J$i")->applyFromArray(array(
													        'alignment'  => array(
												                'horizontal'  => PHPExcel_Style_Alignment::HORIZONTAL_RIGHT,
												            )
												        ));
       	for($i = 'A'; $i !== 'K'; $i++){
            $worksheet->getColumnDimension($i)
                            ->setAutoSize(true);
        }
        $objPHPExcel->setActiveSheetIndex(0);
        $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
        $objWriter->save(APP.DS.'webroot'.DS.'upload'.DS.'accounts_receivable_excel.xlsx');
        $this->redirect('/upload/accounts_receivable_excel.xlsx');
        die;

    }

    function get_sum_outstading(){
    	$current_time = strtotime(date('Y-m-d'));
    	$invoices = $this->opm->select_all(array(
    			'arr_where' => array(
								'invoice_status' 	=> array('$in' => array('In Progress','Invoiced')),
								'payment_due_date' 	=> array('$lt' => new MongoDate($current_time - 30*DAY))
						),
    			'arr_field' => array('company_id', 'sum_sub_total', 'invoice_status')
    		));
    	$amount = 0;
    	$arr_companies = Cache::read('arr_companies');
	    if(!$arr_companies)
	        $arr_companies = array();
	    $count = count($arr_companies);
	    $original_minimum = Cache::read('minimum');
	    $product = Cache::read('minimum_product');
	    $this->selectModel('Company');
	    $this->selectModel('Salesorder');
	    $this->selectModel('Job');
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
    	foreach($invoices as $invoice) {
    		if(!isset($invoice['company_id']))
    		    $invoice['company_id'] = '';
    		if($invoice['invoice_status'] == 'Cancelled')
    		    $invoice['sum_sub_total'] = 0;
    		else if(!isset($arr_companies[(string)$invoice['company_id']])){
    		    $minimum = $original_minimum;
    		    if(is_object($invoice['company_id'])){
    		        $company = $this->Company->select_one(array('_id'=>new MongoId($invoice['company_id'])),array('pricing'));
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
    		    $arr_companies[(string)$invoice['company_id']] = $minimum;
    		} else
    		    $minimum = $arr_companies[(string)$invoice['company_id']];
    		$invoice['sum_sub_total'] = $invoice['sum_sub_total'] * 1.05;
    		if(isset($minimum) && $invoice['sum_sub_total'] < $minimum)
    		    $invoice['sum_sub_total'] = $minimum;
    		$amount += $invoice['sum_sub_total'];
    	}
    	echo json_encode(array('number' => number_format($invoices->count()), 'amount' => $this->opm->format_currency($amount)));
    	die;
    }

    public function create_import_excel() {
    	App::import('Vendor', 'phpexcel/PHPExcel');
    	$objPHPExcel = new PHPExcel();
    	$objPHPExcel->getProperties()->setCreator('')
    	                             ->setLastModifiedBy('')
    	                             ->setTitle('Invoice Import Excel')
    	                             ->setSubject('Invoice Import Excel')
    	                             ->setDescription('Invoice Import Excel')
    	                             ->setKeywords('Invoice Import Excel')
    	                             ->setCategory('Invoice Import Excel');
    	$worksheet = $objPHPExcel->getActiveSheet();

    	$arr_where = $this->arr_search_where();
    	$arr_field = array('code', 'company_name', 'contact_name', 'invoice_date', 'our_rep', 'our_csr', 'job_number', 'job_name', 'heading', 'status', 'customer_po_no', 'sum_sub_total', 'sum_tax', 'sum_amount');
    	$invoices = $this->opm->select_all(array(
    											'arr_where' => $arr_where,
    											'arr_field' => $arr_field,
    											'arr_order' => array('invoice_date' => -1),
    											));
    	$worksheet->setCellValue('A1','Ref no')
                        ->setCellValue('B1','Company name')
                        ->setCellValue('C1','Contact name')
                        ->setCellValue('D1','Our Rep')
                        ->setCellValue('E1','Our CSR')
                        ->setCellValue('F1','Invoice Date')
                        ->setCellValue('G1','Job #')
                        ->setCellValue('H1','Job name')
                        ->setCellValue('I1','Heading')
                        ->setCellValue('I1','Customer PO no')
                        ->setCellValue('K1','Status')
                        ->setCellValue('L1','Sub total')
                        ->setCellValue('M1','Tax')
                        ->setCellValue('N1','Amount');
        $i = 2;
        foreach($invoices as $invoice) {
        	foreach($arr_field as $field) {
        		if( !isset($invoice[$field]) ) {
        			if( $field == 'invoice_date' ) {
        				$invoice[$field] = new MongoDate();
        			} else {
        				$invoice[$field] = '';
        			}
        		}
        	}
        	$worksheet->setCellValue('A'.$i, $invoice['code'])
                        ->setCellValue('B'.$i, $invoice['company_name'])
                        ->setCellValue('C'.$i, $invoice['contact_name'])
                        ->setCellValue('D'.$i, $invoice['our_rep'])
                        ->setCellValue('E'.$i, $invoice['our_csr'])
                        ->setCellValue('F'.$i, date('d/m/Y', $invoice['invoice_date']->sec))
                        ->setCellValue('G'.$i, $invoice['job_number'])
                        ->setCellValue('H'.$i, $invoice['job_name'])
                        ->setCellValue('I'.$i, $invoice['heading'])
                        ->setCellValue('J'.$i, $invoice['customer_po_no'])
                        ->setCellValue('K'.$i, $invoice['status'])
                        ->setCellValue('L'.$i, $invoice['sum_sub_total'])
                        ->setCellValue('M'.$i, $invoice['sum_tax'])
                        ->setCellValue('N'.$i, $invoice['sum_amount']);
        	$i++;
        }
        $worksheet->getStyle('L2:N'.$i)->getNumberFormat()->setFormatCode("#,##0.00");
	    for($i = 'A'; $i !== 'O'; $i++){
	    	$worksheet->getColumnDimension($i)
				        	->setAutoSize(true);
	    }
        $objPHPExcel->setActiveSheetIndex(0);
        $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
        $objWriter->save(APP.DS.'webroot'.DS.'upload'.DS.'InvoiceExcel.xlsx');
        $this->redirect('/upload/InvoiceExcel.xlsx');
        die;
    }

    function missing_tax()
    {
    	$invoices = $this->opm->select_all(array(
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
    	foreach($invoices as $invoice) {
    		$html .= 'Invoice <a href="'.URL.'/salesinvoices/entry/'.$invoice['_id'].'" target="_blank">#'.$invoice['code'].'</a><br/>';
    	}
    	echo $html;
    	die;
    }
    public function history($ids = '') {
		if($ids=='')
			$ids = $this->get_id();
		$query = $this->opm->select_one(array('_id'=> new MongoId($ids)),array('quotation_id','quotation_code','created_by'));
		$this->set('quotation_id', (isset($query['quotation_id'])&&is_object($query['quotation_id']))? (string)$query['quotation_id']:'');
		$this->set('quotation_code', (isset($query['quotation_code']))? $query['quotation_code']:'');
		$this->set('created_by', (isset($query['created_by']))? $this->get_name('Contact',$query['created_by']):'');
		parent::history($ids);
	}

}