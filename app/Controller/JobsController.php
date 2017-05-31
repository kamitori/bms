<?php

App::uses('AppController', 'Controller');

class JobsController extends AppController {

	var $modelName = 'Job';
	var $name = 'Jobs';
	var $sub_tab_default = 'general';

	public function beforeFilter() {
		// goi den before filter cha
		parent::beforeFilter();
		$this->set('title_entry', 'Jobs');
		$this->set('name',$this->name);
	}

	function auto_save() {
		if (!empty($this->data)) {
			$id = $this->get_id();
			$arr_return = array();
			$arr_post_data = $this->data['Job'];
			$arr_save = $arr_post_data;
			if(isset($arr_save['work_start'])){
				$work_start_sec = $this->Common->strtotime($arr_save['work_start'] . ' 00:00:00');
				$arr_save['work_start'] = new MongoDate($work_start_sec);
			}
			if(isset($arr_save['work_end'])){
				$work_end_sec = $this->Common->strtotime($arr_save['work_end'] . ' 00:00:00');
				$arr_save['work_end'] = new MongoDate($work_end_sec);
			}
			$arr_save['_id'] = new MongoId($id);
			$this->selectModel('Job');
			if(isset($arr_save['no'])){
				if(!is_numeric($arr_save['no'])){
					echo json_encode(array('status'=>'error','message'=>'must_be_numberic'));
					die;
				}
				$arr_tmp = $this->Job->select_one(array('no' => (int) $arr_save['no'], '_id' => array('$ne' => new MongoId($id))));
				if (isset($arr_tmp['no'])) {
					echo json_encode(array('status'=>'error','message'=>'ref_no_existed'));
					die;
				}
			}
			//if(IS_LOCAL){
		        if( isset($_POST['field_change']) && $_POST['field_change'] == 'JobCompanyName' ){
		        	if(empty($_POST['password_jobs'])){
			        	$company_id = $arr_save['company_id'];
			        	$this->selectModel('Receipt');
			            $this->selectModel('Salesinvoice');
			            $current_time = strtotime(date('Y-m-d'));
                		$this->selectModel('Salesaccount');
			            $salesaccount =$this->Salesaccount->select_one(array('company_id' => new MongoId($company_id)),array('payment_terms'));
                		$payment_terms_salesaccount = isset($salesaccount['payment_terms']) ? $salesaccount['payment_terms'] : 0;
			            $obj_salesinvoices = $this->Salesinvoice->select_all(array(
			                                'arr_where'=>array(
			                                                    'company_id' => new MongoID($company_id),
	                                                    		//'invoice_status' => array('$nin' => array('Cancelled','Paid','In Progress')),
			                                                    'payment_due_date' => array('$lt' => new MongoDate($current_time -$payment_terms_salesaccount*DAY))
			                                                   ),
			                                'arr_field'=>array('_id'),
			                        ));
			            if($obj_salesinvoices->count()){
			                $obj_salesinvoices = $this->Salesinvoice->select_all(array(
			                            'arr_where'=>array(
			                                                'company_id' => new MongoID($company_id),
			                                                //'invoice_status' => array('$nin' => array('Cancelled','Paid','In Progress')),
			                                               ),
			                            'arr_field'=>array('payment_due_date','invoice_date','sum_amount','total_receipt','invoice_status'),
			                            'arr_order'=>array('invoice_date'=>-1)
			                    ));
			                $total_balance = 0;
			                foreach($obj_salesinvoices as $key => $value){
			                    $payment_due_date = strtotime(date('Y-m-d',$value['payment_due_date']->sec));

			                    $receipts = $this->Receipt->collection->aggregate(
			                        array(
			                            '$match'=>array(
			                                            'company_id' => new MongoID($company_id),
			                                            'deleted'=> false
			                                            ),
			                        ),
			                        array(
			                            '$unwind'=>'$allocation',
			                        ),
			                         array(
			                            '$match'=>array(
			                                            'allocation.deleted'=> false,
			                                            'allocation.salesinvoice_id' => $value['_id']
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
			                    if($value['invoice_status'] == 'Credit' && $value['sum_amount'] > 0)
			                        $value['sum_amount'] = (float)$value['sum_amount'] * -1;
			                    $value['total_receipt']= $total_receipt;
			                    $total_balance += $value['sum_amount'] - (float)$value['total_receipt'];
			                }
                                        // if($total_balance)
			                if (($total_balance) && ($company_id != "5271dab4222aad6819000ed0")) { // Khong kiem tra Cong ty Anvy - Le Quan Ha
			                   	echo json_encode(array('message'=>'not_add||'.$payment_terms_salesaccount));
			                    die;
			                }
			            }
		        	}else{
		        		 $this->selectModel('Stuffs');
		                 $change = $this->Stuffs->select_one(array('value' => 'Changing Code'));
		                 if(md5($_POST['password_jobs']) != $change['password']){
		                    echo json_encode(array('message'=>'wrong_pass_company'));
		                    die;
		                }
		        	}
		        }
		    //}
		   	$_id = $this->checkClosingMonth($arr_save );
			$arr_tmp = $this->Job->select_one(array('_id' =>  new MongoId($id)),array('status','company_id','custom_po_no'));
			if($arr_save['status']!='Paid' && $arr_tmp['status']=='Paid'){
				if(!$this->Session->check('JobsOpen_'.$_id)){
					if(isset($_POST['password'])){
						$this->selectModel('Stuffs');
						$change = $this->Stuffs->select_one(array('value'=>'Changing Code'));
						if(md5($_POST['password'])!=$change['password']){
							echo json_encode(array('status'=>'error','message'=>'wrong_pass'));
							die;
						}
					} else {
						echo json_encode(array('status'=>'error','message'=>'need_pass'));
						die;
					}
				}
				if ($this->Job->save($arr_save))
					$arr_return['status'] = 'ok';
				else
					$arr_return = array('status'=>'error','message'=>'Error: ' . $this->Job->arr_errors_save[1]);
				echo json_encode($arr_return);
				die;
			}
			if(isset($arr_save['custom_po_no']) && isset($arr_tmp['custom_po_no']) && $arr_save['custom_po_no']!= $arr_tmp['custom_po_no']){
				foreach(array('Salesinvoice','Salesorder','Quotation') as $model){
					$this->selectModel($model);
					$this->$model->collection->update(
		                                           array('job_id'=>new MongoId($arr_save['_id'])),
		                                           array('$set'=>array(
		                                                 'customer_po_no'=>$arr_save['custom_po_no']
		                                                 )
		                                           ),
		                                           array('multiple'=>true)
	                                           );
				}
			}
			if($arr_save['status']=='Completed'){
				if($arr_tmp['status']!=$arr_save['status']){
					$this->selectModel('Salesorder');
					$salesorder = $this->Salesorder->count(array('job_id'=> new MongoId($arr_save['_id'])));
					$salesorder_completed = $this->Salesorder->count(array('job_id'=> new MongoId($arr_save['_id']),'status'=>'Completed'));

					if($salesorder > $salesorder_completed){
						echo json_encode(array('status'=>'error','message'=>'so_completed'));
						die;
					}
					$this->selectModel('Salesinvoice');
					$salesinvoice = $this->Salesinvoice->count(array('job_id'=> new MongoId($arr_save['_id'])));
					$salesinvoice_invoiced = $this->Salesinvoice->count(array('job_id'=> new MongoId($arr_save['_id']),'invoice_status'=>'Invoiced'));
					if($salesinvoice > $salesinvoice_invoiced){
						echo json_encode(array('status'=>'error','message'=>'si_invoiced'));
						die;
					}
					$salesorder_sum = $this->Salesorder->sum('sum_amount','tb_salesorder',array('job_id'=>new MongoId($arr_save['_id']),'deleted'=>false));
					$salesinvoice_sum = $this->Salesinvoice->sum('sum_amount','tb_salesinvoice',array('job_id'=>new MongoId($arr_save['_id']),'deleted'=>false));
					// $salesorder_sum = $salesinvoice_sum = 0;
					// $so = $this->Salesorder->select_all(array('arr_where'=>array('job_id'=>new MongoId($arr_save['_id'])),'arr_field'=>array('sum_amount')));
					// foreach($so as $k=>$v){
					// 	$salesorder_sum += (float)$v['sum_amount'];
					// }
					// $si = $this->Salesinvoice->select_all(array('arr_where'=>array('job_id'=>new MongoId($arr_save['_id'])),'arr_field'=>array('sum_amount')));
					// foreach($si as $k2=>$v2){
					// 	$salesinvoice_sum += (float)$v2['sum_amount'];
					// }
					if(abs(($salesorder_sum - $salesinvoice_sum) / ($salesinvoice_sum==0? 1 :$salesinvoice_sum) ) > 0.001) {
						echo json_encode(array('status'=>'error','message'=>'sum_different'));
						die;
					}
				}
			}
			if(!$this->Session->check('JobsOpen_'.$_id) && $arr_save['status']!='Completed' && $arr_tmp['status']=='Completed' ){
				if(isset($_POST['password'])){
					$this->selectModel('Stuffs');
					$change = $this->Stuffs->select_one(array('value'=>'Changing Code'));
					if(md5($_POST['password'])!=$change['password']){
						echo json_encode(array('status'=>'error','message'=>'wrong_pass'));
						die;
					}
				} else {
					echo json_encode(array('status'=>'error','message'=>'need_pass'));
					die;
				}
			}

			if (isset($work_start_sec) && $work_start_sec > $work_end_sec) {
				echo json_encode(array('status'=>'error','message'=>'date_work'));
				die;
			}
			if(isset($arr_save['contact_id'])) {
				if (strlen(trim($arr_save['contact_id'])) == 24)
					$arr_save['contact_id'] = new MongoId($arr_save['contact_id']);
				else
					$arr_save['contact_id'] = '';
			}
			if(isset($arr_save['company_id'])) {
				if(strlen($arr_save['company_id'])==24){
					$arr_save['company_id'] = new MongoId($arr_save['company_id']);
					if(isset($arr_tmp['company_id'])
					   		&& (string)$arr_tmp['company_id']!= (string)$arr_save['company_id']){
						$this->selectModel('Company');
						$company = $this->Company->select_one(array('_id'=>$arr_save['company_id']),array('contact_default_id','email','phone'));
						if(isset($company['contact_default_id'])&&is_object($company['contact_default_id'])){
							$this->selectModel('Contact');
							$contact = $this->Contact->select_one(array('_id'=>new MongoId($company['contact_default_id'])),array('full_name'));
							$arr_save['contact_name'] = '';
							$arr_save['contact_id'] = $company['contact_default_id'];
							if(isset($contact['full_name']))
								$arr_save['contact_name'] = $contact['full_name'];
							$arr_return['contact_id'] = (string)$arr_save['contact_id'];
							$arr_return['contact_name'] = $arr_save['contact_name'];
						}
						$arr_return['email'] = isset($company['email']) ? $company['email'] : '';
						$arr_return['company_phone'] = isset($company['phone']) ? $company['phone'] : '';
					}
				}
				else
					$arr_save['company_id'] = '';
			}
			if(isset($arr_save['our_rep_id'])) {
				if(strlen($arr_save['our_rep_id'])==24)
					$arr_save['our_rep_id'] = new MongoId($arr_save['our_rep_id']);
				else
					$arr_save['our_rep_id'] = '';
			}
			if( isset($arr_save['name']) && strlen($arr_save['name']) > 0 )
				$arr_save['name']{0} = strtoupper($arr_save['name']{0});
			$this->selectModel('Job');
			if ($this->Job->save($arr_save))
				$arr_return['status'] = 'ok';
			else
				$arr_return = array('status'=>'error','message'=>'Error: ' . $this->Job->arr_errors_save[1]);
			echo json_encode($arr_return);
		}
		die;
	}

	function delete($id = 0) {
		$arr_save['_id'] = $id;
		$arr_save['deleted'] = true;
		$error = 0;
		if (!$error) {
			$this->selectModel('Job');
			if ($this->Job->save($arr_save)) {
				$this->Session->delete('Job_entry_id');
				$this->redirect('/jobs/entry');
			} else {
				echo 'Error: ' . $this->Job->arr_errors_save[1];
			}
		}
		die;
	}

	public function add() {
		$this->selectModel('Job');
		if ($this->Job->add()) {
			$this->redirect('/jobs/entry/' . $this->Job->mongo_id_after_save);
		} else {
			echo 'Error: ' . $this->Job->arr_errors_save[1];
		}
		die;
	}

	public function entry($id = '0', $num_position = -1) {// echo date('d/m/Y', strtotime('2 Jul, 2013'));die;
		$arr_tmp = $this->entry_init($id, $num_position, 'Job', 'jobs');


		// $arr_tmp['work_end'] = new MongoDate(strtotime($arr_tmp['work_end']));
        $_id = $this->checkClosingMonth($arr_tmp);
        $this->set('closing_month_id',$_id);
        if($_id && !$this->Session->check('JobsOpen_'.$_id)){
            $this->set('closing_month', 1);
        }
		$arr_tmp['work_start'] = (is_object($arr_tmp['work_start'])) ? date('m/d/Y', $arr_tmp['work_start']->sec) : '';
		$arr_tmp['work_end'] = (is_object($arr_tmp['work_end'])) ? date('m/d/Y', $arr_tmp['work_end']->sec) : '';

		$arr_tmp1['Job'] = $arr_tmp;
		$this->data = $arr_tmp1;

		$this->selectModel('Setting');
		$this->set('arr_jobs_type', $this->Setting->select_option(array('setting_value' => 'jobs_type'), array('option')));
		$this->set('arr_jobs_status', $this->Setting->select_option(array('setting_value' => 'jobs_status'), array('option')));

		$arr_contact_id = array();
		if (isset($arr_tmp['our_rep_id']))
			$arr_contact_id[] = $arr_tmp['our_rep_id'];
		if (isset($arr_tmp['contact_id']))
			$arr_contact_id[] = $arr_tmp['contact_id'];

		// hiển thị cho footer
		$this->show_footer_info($arr_tmp, $arr_contact_id);

		// Get info for subjob
		$this->sub_tab('', $arr_tmp['_id']);
	}

	public function entry_search_all(){
		$this->Session->delete('jobs_entry_search_cond');
		$this->redirect('/jobs/lists');
	}

	public function entry_search() {
		if (!empty($this->data) && $this->request->is('ajax')) {
			$post = $this->data['Job'];
			$cond = array();
			$post = $this->Common->strip_search($post);
			if( isset($post['no'])&&strlen($post['no']) > 0 )$cond['no'] = (int)$post['no'];
			if( isset($post['name'])&&strlen($post['name']) > 0 )$cond['name'] = new MongoRegex('/' . trim($post['name']).'/i');
			if( isset($post['company_id'])&&strlen($post['company_id']) > 0 )$cond['company_id'] = new MongoId($post['company_id']);
			if( isset($post['contact_id'])&&strlen($post['contact_id']) > 0 )$cond['company_id'] = new MongoId($post['contact_id']);
			if( isset($post['work_start'])&&strlen($post['work_start']) > 0 ){
				$sec = $this->Common->strtotime($post['work_start'] . '00:00:00');
				$cond['work_start'] = array( '$gt' => new MongoDate($sec) );
			};
			if( isset($post['work_end'])&&strlen($post['work_end']) > 0 ){
				$sec = $this->Common->strtotime($post['work_end'] . '00:00:00');
				$cond['work_end'] = array( '$lt' => new MongoDate($sec) );
			};
			if( isset($post['type_id'])&&strlen($post['type_id']) > 0 )$cond['type_id'] = $post['type_id'];
			if( isset($post['status_id'])&&strlen($post['status_id']) > 0 )$cond['status_id'] = $post['status_id'];
			if( isset($post['email'])&&strlen($post['email']) > 0 )$cond['email'] = new MongoRegex('/' . trim($post['email']).'/i');
			if( isset($post['company_phone'])&&strlen($post['company_phone']) > 0 )$cond['company_phone'] = new MongoRegex('/' . trim($post['company_phone']).'/i');
			if( isset($post['direct_phone'])&&strlen($post['direct_phone']) > 0 )$cond['direct_phone'] = new MongoRegex('/' . trim($post['direct_phone']).'/i');
			if( isset($post['mobile'])&&strlen($post['mobile']) > 0 )$cond['mobile'] = new MongoRegex('/' . trim($post['mobile']).'/i');
			if( isset($post['fax'])&&strlen($post['fax']) > 0 )$cond['fax'] = new MongoRegex('/' . trim($post['fax']).'/i');
			if( isset($post['custom_po_no'])&&strlen($post['custom_po_no']) > 0 )$cond['custom_po_no'] = new MongoRegex('/' . trim($post['custom_po_no']).'/i');
			if( isset($post['work_end_from'])&&strlen($post['work_end_from']) > 0 ){
				$sec = $this->Common->strtotime($post['work_end_from'] . '00:00:00');
				$cond['work_end']['$gte'] =new MongoDate($sec);
			}
			if( isset($post['work_end_to'])&&strlen($post['work_end_to']) > 0 ){
				$sec = $this->Common->strtotime($post['work_end_to'] . '23:00:00');
				$cond['work_end']['$lte'] = new MongoDate($sec);
			}
			$this->selectModel('Job');
            $this->identity($cond);
			$tmp = $this->Job->select_one($cond);
			if( $tmp ){
				$this->Session->write('jobs_entry_search_cond', $cond);

				$cond['_id'] = array('$ne' => $tmp['_id']);
				$tmp1 = $this->Job->select_one($cond);
				if( $tmp1 ){
					echo 'yes'; die;
				}
				echo 'yes_1_'.$tmp['_id']; die; // chỉ có 1 kết quả thì chuyển qua trang entry luôn
			}else{
				echo 'no'; die;
			}
			echo 'ok';
			die;
		}

		$this->selectModel('Setting');
		$this->set('arr_jobs_type', $this->Setting->select_option(array('setting_value' => 'jobs_type'), array('option')));
		$this->set('arr_jobs_status', $this->Setting->select_option(array('setting_value' => 'jobs_status'), array('option')));

		$this->set('set_footer', 'footer_search');
		$this->set('address_country', $this->country());
		$this->set('address_province', $this->province("CA"));

		// Get info for subtask
		// $this->sub_tab('', $arr_tmp['_id']);
	}

	function general($job_id) {
		$this->set('job_id', $job_id);

		$this->selectModel('Job');
		$arr_job = $this->Job->select_one(array('_id' => new MongoId($job_id)));
		$this->set('arr_job', $arr_job);
		$this->set('address_country', $this->country());

		$invoice_country_id = "CA";
		if (isset($arr_job['invoice_country_id']))
			$invoice_country_id = $arr_job['invoice_country_id'];
		$this->set('address_province', $this->province($invoice_country_id));

		$shipping_country_id = "CA";
		if (isset($arr_job['shipping_country_id']))
			$shipping_country_id = $arr_job['shipping_country_id'];
		$this->set('address_province_shipping', $this->province($shipping_country_id));

		$this->set('address_onchange', "job_general_auto_save()");
		//Goi ham dung chung
		$this->communications($job_id, true);
	}

	function general_delete_contact($job_id, $key) {
		$this->selectModel('Job');
		$this->Job->collection->update(
				array('_id' => new MongoId($job_id)), array('$set' => array(
				'contacts.' . $key . '.deleted' => true
			)
				)
		);
		echo 'ok';
		die;
	}

	function general_auto_save() {
		if (!empty($this->data)) {
			$arr_post_data = $this->data['Job'];
			$arr_save = $arr_post_data;

			$error = 0;
			if (!$error) {
				$this->selectModel('Job');
				if ($this->Job->save($arr_save)) {
					echo 'ok';
				} else {
					echo 'Error: ' . $this->Job->arr_errors_save[1];
				}
			}
		}
		die;
	}

	function general_window_contact_choose($job_id, $contact_id, $contact_name) {
		$this->selectModel('Job');
		$arr_job = $this->Job->select_one(array('_id' => new MongoId($job_id)), array('contacts'));
		$check_not_exist = true;
		if (isset($arr_job['contacts'])) {
			foreach ($arr_job['contacts'] as $value) {
				if ((string) $value['contact_id'] == $contact_id) {
					$check_not_exist = false;
				}
			}
		}

		if ($check_not_exist) {

			$this->Job->collection->update(
					array('_id' => new MongoId($job_id)), array('$push' => array(
					'contacts' => array(
						'contact_name' => $contact_name,
						'contact_id' => new MongoId($contact_id),
						'default' => false,
						'deleted' => false
					)
				)
					)
			);
			echo 'ok';
		} else {
			echo 'Error this contact is selected before';
		}
		die;
		// $this->addresses($company_id);
		// $this->render('addresses');
	}

	function general_choose_manager($job_id, $option_id) {
		// gán lại key deleted để không bị mất
		$this->selectModel('Job');
		$this->Job->collection->update(
				array('_id' => new MongoId($job_id)), array('$set' => array('contacts.' . $option_id . '.default' => true, 'contacts_default_key' => $option_id))
		);

		echo 'ok';
		die;
	}

	function tasks($job_id) {
		$this->selectModel('Task');
		$arr_task = $this->Task->select_all(array(
			'arr_where' => array('job_id' => new MongoId($job_id)),
			'arr_order' => array('work_start' => 1)
		));
		$this->set('arr_task', $arr_task);
		$this->set('job_id', $job_id);
	}

	function tasks_add($job_id,$option=false) {
		$this->selectModel('Company');
		$arr_job = $this->Company->select_one(array('_id' => new MongoId($job_id)));
		$this->selectModel('Task');
		$arr_tmp = $this->Task->select_one(array(), array(), array('no' => -1));
		$arr_save = array();
		$this->selectModel('Job');
		$arr_job = $this->Job->select_one(array('_id' => new MongoId($job_id)));
		$arr_save['job_no'] = (isset($arr_job['no']) ? $arr_job['no'] : '');
		$arr_save['job_name'] = (isset($arr_job['name']) ? $arr_job['name'] : '');
		$arr_save['job_id'] = $arr_job['_id'];
		$arr_save['company_name'] = (isset($arr_job['company_name']) ? $arr_job['company_name'] : '');
		$arr_save['company_id'] = (isset($arr_job['company_id']) ? $arr_job['company_id'] : '');
		$arr_save['contact_name'] = (isset($arr_job['contact_name']) ? $arr_job['contact_name'] : '');
		$arr_save['contact_id'] = (isset($arr_job['contact_id']) ? $arr_job['contact_id'] : '');

		if (isset($arr_job['contact_default_id'])) {
			$this->selectModel('Contact');
			$arr_contact = $this->Contact->select_one(array('_id' => $arr_job['contact_default_id']));
			$arr_save['contact_id'] = $arr_contact['_id'];
			$arr_save['contact_name'] = $arr_contact['first_name'] . ' ' . $arr_contact['last_name'];
		}

		$this->Task->arr_default_before_save = $arr_save;
		if ($this->Task->add()){
			$task_id =  $this->Task->mongo_id_after_save;
			if($option){
				echo URL.'/tasks/entry/'.$task_id;
				die;
			}
			$this->redirect('/tasks/entry/' . $task_id);
		}
		if($option){
			echo URL.'/jobs/entry/' . $job_id;
			die;
		}
		$this->redirect('/jobs/entry/' . $job_id);
	}

	function tasks_delete($id) {
		$arr_save['_id'] = $id;
		$arr_save['deleted'] = true;
		$error = 0;
		if (!$error) {
			$this->selectModel('Task');
			if ($this->Task->save($arr_save)) {
				echo 'ok';
			} else {
				echo 'Error: ' . $this->Task->arr_errors_save[1];
			}
		}
		die;
	}

	function stages($job_id) {

	}

	function quotes($job_id) {
		$this->selectModel('Quotation');
		$arr_quote = $this->Quotation->select_all(array(
			'arr_where' => array('job_id' => new MongoId($job_id)),
			'arr_field' => array('sum_sub_total','sum_amount','sum_tax','quotation_type','quotation_status','our_rep','code','quotation_date','payment_due_date','taxval','tax','name', 'shipping_cost')
		));
		$arr_quote = iterator_to_array($arr_quote);
		foreach($arr_quote as $key=>$quote){
			$minimum = $this->get_minimum_order('Quotation',$quote['_id']);
			if(!isset($quote['sum_sub_total']))
				$arr_quote[$key]['sum_sub_total'] = 0;
			if(!isset($quote['sum_amount']))
				$arr_quote[$key]['sum_amount'] = 0;
			if(!isset($quote['sum_tax']))
				$arr_quote[$key]['sum_tax'] = 0;
    		if($arr_quote[$key]['sum_sub_total']<$minimum){
    			$more_sub_total = $minimum - (float)$arr_quote[$key]['sum_sub_total'];
    			$sub_total = $more_sub_total;
                $tax = $sub_total*(float)$quote['taxval']/100;
                $amount = $sub_total+$tax;
    			$arr_quote[$key]['sum_sub_total'] += $sub_total;
    			$arr_quote[$key]['sum_amount'] += $amount;
    			$arr_quote[$key]['sum_tax'] = $arr_quote[$key]['sum_amount']-$arr_quote[$key]['sum_sub_total'];
    		}
		}
		$this->selectModel('Tax');
		$arr_tax = $this->Tax->tax_select_list();
		$this->set('arr_tax',$arr_tax);
		$this->set('arr_quotation', $arr_quote);
		$this->set('job_id', $job_id);
	}

	function quotes_add($job_id,$option=false) {
		$this->selectModel('Quotation');
		$arr_save = array();

		$this->selectModel('Job');
		$arr_job = $this->Job->select_one(array('_id' => new MongoId($job_id)));
		$arr_save['company_name'] = (isset($arr_job['company_name']) ? $arr_job['company_name'] : '');
		$arr_save['company_id'] = (isset($arr_job['company_id']) ? $arr_job['company_id'] : '');
		if(is_object($arr_save['company_id'])){
			$this->selectModel('Salesaccount');
			$salesaccount = $this->Salesaccount->select_one(array('company_id'=>new MongoId($arr_save['company_id'])),array('payment_terms','payment_terms_id'));
			if(!empty($salesaccount)){
				$arr_save['payment_terms'] = $salesaccount['payment_terms'];
				$arr_save['payment_terms_id'] = $salesaccount['payment_terms_id'];
			}
			$this->selectModel('Company');
			$company = $this->Company->select_one(array('_id'=>$arr_save['company_id']),array('our_rep','our_rep_id','our_csr','our_csr_id'));
			if(isset($company['our_rep_id']) && is_object($company['our_rep_id'])){
				$arr_save['our_rep'] = $company['our_rep'];
				$arr_save['our_rep_id'] = $company['our_rep_id'];
			}
			if(isset($company['our_csr_id']) && is_object($company['our_csr_id'])){
				$arr_save['our_csr'] = $company['our_csr'];
				$arr_save['our_csr_id'] = $company['our_csr_id'];
			}
		}
		$arr_save['code'] = $this->Quotation->get_auto_code('code');
		$arr_save['heading'] = (isset($arr_job['name']) ? $arr_job['name'] : '');
		$arr_save['name'] = $arr_save['code'].' - '.$arr_save['company_name'];
		$arr_save['contact_name'] = (isset($arr_job['contact_name']) ? $arr_job['contact_name'] : '');
		$arr_save['contact_id'] = (isset($arr_job['contact_id']) ? $arr_job['contact_id'] : '');
		$arr_save['job_name'] = $arr_job['name'];
		$arr_save['job_number'] = $arr_job['no'];
		$arr_save['job_id'] = $arr_job['_id'];
		$arr_save = array_merge($arr_save,$this->get_tax($arr_job));
		$arr_save = array_merge($arr_save,$this->rebuild_job_address($arr_job));
		$this->Quotation->arr_default_before_save = $arr_save;
		if ($this->Quotation->add()){
			$quotation_id = $this->Quotation->mongo_id_after_save;
			if($option){
				echo URL.'/quotations/entry/' . $quotation_id;
				die;
			}
			$this->redirect('/quotations/entry/' . $quotation_id);
		}
		if($option){
			echo URL.'/jobs/entry/' . $job_id;
			die;
		}
		$this->redirect('/jobs/entry/' . $job_id);
	}

	function quotes_delete($id) {
		$arr_save['_id'] = new MongoId($id);
		$arr_save['job_id'] = $arr_save['job_number'] = $arr_save['job_name'] = '';
		$error = 0;
		if (!$error) {
			$this->selectModel('Quotation');
			if ($this->Quotation->save($arr_save)) {
				echo 'ok';
			} else {
				echo 'Error: ' . $this->Quotation->arr_errors_save[1];
			}
		}
		die;
	}

	function budgets($job_id) {

	}

	function costs($job_id) {

	}

	function orders($job_id) {
		$this->set('job_id', $job_id);
		$this->selectModel('Salesorder');
		$arr_salesorder = $this->Salesorder->select_all(array(
			'arr_where' => array('job_id' => new MongoId($job_id)),
			'arr_field' => array('sum_sub_total','sum_amount','sum_tax','status','our_rep','code','salesorder_date','payment_due_date','taxval','tax','heading','other_comment', 'shipping_cost')
		));
		$arr_salesorder = iterator_to_array($arr_salesorder);
		foreach($arr_salesorder as $key=>$order){
			$minimum = $this->get_minimum_order('Salesorder',$order['_id']);
    		if($order['sum_sub_total']<$minimum){
    			$arr_salesorder[$key]['sum_sub_total'] = $minimum;
    			$arr_salesorder[$key]['sum_tax'] = $arr_salesorder[$key]['sum_sub_total']*(float)$order['taxval']/100;
    			$arr_salesorder[$key]['sum_amount'] = $arr_salesorder[$key]['sum_sub_total'] + $arr_salesorder[$key]['sum_tax'];
    		}
		}
		$this->set('arr_salesorder', $arr_salesorder);
	}

	function orders_add_salesorder($job_id,$option=false) {
		$this->selectModel('Salesorder');
		$arr_save = array();

		$this->selectModel('Job');
		$arr_job = $this->Job->select_one(array('_id' => new MongoId($job_id)));
		$arr_save['company_name'] = (isset($arr_job['company_name']) ? $arr_job['company_name'] : '');
		$arr_save['company_id'] = (isset($arr_job['company_id']) ? $arr_job['company_id'] : '');
		if(is_object($arr_save['company_id'])){
			$this->selectModel('Company');
			$company = $this->Company->select_one(array('_id'=>$arr_save['company_id']),array('our_rep','our_rep_id','our_csr','our_csr_id'));
			if(isset($company['our_rep_id']) && is_object($company['our_rep_id'])){
				$arr_save['our_rep'] = $company['our_rep'];
				$arr_save['our_rep_id'] = $company['our_rep_id'];
			}
			if(isset($company['our_csr_id']) && is_object($company['our_csr_id'])){
				$arr_save['our_csr'] = $company['our_csr'];
				$arr_save['our_csr_id'] = $company['our_csr_id'];
			}
		}
		if(is_object($arr_save['company_id'])){
			$this->selectModel('Salesaccount');
			$salesaccount = $this->Salesaccount->select_one(array('company_id'=>new MongoId($arr_save['company_id'])),array('payment_terms','payment_terms_id'));
			if(!empty($salesaccount)){
				$arr_save['payment_terms'] = $salesaccount['payment_terms'];
				$arr_save['payment_terms_id'] = $salesaccount['payment_terms_id'];
			}
		}
		$arr_save['contact_name'] = (isset($arr_job['contact_name']) ? $arr_job['contact_name'] : '');
		$arr_save['contact_id'] = (isset($arr_job['contact_id']) ? $arr_job['contact_id'] : '');
		$arr_save['job_name'] = $arr_job['name'];
		$arr_save['job_number'] = $arr_job['no'];
		$arr_save['job_id'] = $arr_job['_id'];
		$arr_save['name'] = $arr_job['name'];
		$arr_save['customer_po_no'] = (isset($arr_job['custom_po_no']) ? $arr_job['custom_po_no'] : '');
		$arr_save['email'] = (isset($arr_job['email']) ? $arr_job['email'] : '');
		$arr_save['phone'] = (isset($arr_job['company_phone']) ? $arr_job['company_phone'] : '');
		$arr_save['salesorder_date'] = (isset($arr_job['work_start']) ? $arr_job['work_start'] : '');
		$arr_save['payment_due_date'] = (isset($arr_job['work_end']) ? $arr_job['work_end'] : '');
		$arr_save['code'] = $this->Salesorder->get_auto_code('code');
		$arr_save['heading'] = (isset($arr_job['name']) ? $arr_job['name'] : '');
		$arr_save['name'] = $arr_save['code'].' - '.$arr_save['company_name'];
		$arr_save = array_merge($arr_save,$this->get_tax($arr_job));
		$arr_save = array_merge($arr_save,$this->rebuild_job_address($arr_job));
		$this->Salesorder->arr_default_before_save = $arr_save;
		if ($this->Salesorder->add())
			$salesorder_id = $this->Salesorder->mongo_id_after_save;
			if($option){
				echo URL.'/salesorders/entry/' . $salesorder_id;
				die;
			}
			$this->redirect('/salesorders/entry/' . $salesorder_id);
		if($option){
			echo URL.'/jobs/entry/' . $job_id;
			die;
		}
		$this->redirect('/jobs/entry/' . $job_id);
	}

	function orders_delete($id) {
		$arr_save['_id'] = new MongoId($id);
		$arr_save['job_id'] = $arr_save['job_number'] = $arr_save['job_name'] = '';
		$error = 0;
		if (!$error) {
			$this->selectModel('Salesorder');
			if ($this->Salesorder->save($arr_save)) {
				echo 'ok';
			} else {
				echo 'Error: ' . $this->Salesorder->arr_errors_save[1];
			}
		}
		die;
	}

	function shipping($job_id) {

		$this->selectModel('Shipping');
		$arr_shipping = $this->Shipping->select_all(array(
			'arr_where' => array('job_id' => new MongoId($job_id)),
		));
		$this->set('arr_shipping', $arr_shipping);
		$this->set('job_id', $job_id);
	}

	function shipping_add($job_id,$option=false) {
		$arr_save = array();
		$this->selectModel('Job');
		$arr_job = $this->Job->select_one(array('_id' => new MongoId($job_id)));
		$arr_save['company_name'] = (isset($arr_job['company_name']) ? $arr_job['company_name'] : '');
		$arr_save['company_id'] = (isset($arr_job['company_id']) ? $arr_job['company_id'] : '');
		$arr_save['contact_name'] = (isset($arr_job['contact_name']) ? $arr_job['contact_name'] : '');
		$arr_save['contact_id'] = (isset($arr_job['contact_id']) ? $arr_job['contact_id'] : '');
		$arr_save['job_name'] = $arr_job['name'];
		$arr_save['job_id'] = $arr_job['_id'];
		$arr_save['job_number'] = $arr_job['no'];
		$arr_save['carrier_name'] = '';
		$arr_save['carrier_id'] = '';
		$arr_save['shipping_type'] = 'Out';
		$arr_save['customer_po_no'] = (isset($arr_job['custom_po_no']) ? $arr_job['custom_po_no'] : '');
		$arr_save = array_merge($arr_save,$this->rebuild_job_address($arr_job,'shipping'));
		$this->selectModel('Shipping');
		$this->Shipping->arr_default_before_save = $arr_save;
		if ($this->Shipping->add()){
			$shipping_id = $this->Shipping->mongo_id_after_save;
			if($option){
				echo URL.'/shippings/entry/' . $shipping_id;
				die;
			}
			$this->redirect('/shippings/entry/' . $shipping_id);
		}
		if($option){
			echo URL.'/jobs/entry/' . $job_id;
			die;
		}
		$this->redirect('/jobs/entry/' . $job_id);
	}

	function shipping_delete($id) {
		$arr_save['_id'] = $id;
		$arr_save['deleted'] = true;
		$error = 0;
		if (!$error) {
			$this->selectModel('Shipping');
			if ($this->Shipping->save($arr_save)) {
				echo 'ok';
			} else {
				echo 'Error: ' . $this->Shipping->arr_errors_save[1];
			}
		}
		die;
	}

	function invoices($job_id) {
		$this->selectModel('Salesinvoice');
		$arr_invoices = $this->Salesinvoice->select_all(array(
			'arr_where' => array('job_id' => new MongoId($job_id)),
			'arr_field' => array('sum_sub_total','sum_amount','sum_tax','invoice_type','code','invoice_date','invoice_status','our_rep','our_rep_id','other_comment','taxval', 'shipping_cost', 'company_id')
		));
		$arr_tmp['overall_job_totals']['commission'] = 0;
		$arr_our_rep = array();
		$arr_invoices = iterator_to_array($arr_invoices);

		foreach($arr_invoices as $key=>$invoice){
			if($invoice['invoice_status']=='Cancelled'){
				$arr_invoices[$key]['sum_sub_total'] = $arr_invoices[$key]['sum_tax'] = $arr_invoices[$key]['sum_amount'] = 0;
				continue;
			}
			if($invoice['invoice_status'] != 'Credit'){
				$minimum = $this->get_minimum_order($invoice['company_id']);
	    		if($invoice['sum_sub_total']<$minimum){
	    			$more_sub_total = $minimum - (float)$invoice['sum_sub_total'];
	    			$sub_total = $more_sub_total;
	                $tax = $sub_total*(float)$invoice['taxval']/100;
	                $amount = $sub_total+$tax;
	    			$arr_invoices[$key]['sum_sub_total'] += $sub_total;
	    			$arr_invoices[$key]['sum_amount'] += $amount;
	    			$arr_invoices[$key]['sum_tax'] = $arr_invoices[$key]['sum_amount']-$arr_invoices[$key]['sum_sub_total'];
	    		}
    		}
			$commission = 0;
    		if(isset($invoice['our_rep_id']) && is_object($invoice['our_rep_id'])){
				if(isset($arr_our_rep[(string)$invoice['our_rep_id']]))
					$commission = $arr_our_rep[(string)$invoice['our_rep_id']];
				else{
					$this->selectModel('Contact');
					$our_rep = $this->Contact->select_one(array('_id'=>$invoice['our_rep_id']),array('commission'));
					if(isset($our_rep['commission'])){
						$commission = (float)$our_rep['commission'];
						$arr_our_rep[(string)$invoice['our_rep_id']] = $commission;
					}
				}
			}
			$arr_tmp['overall_job_totals']['commission'] += ($arr_invoices[$key]['sum_sub_total'] * $commission)/100;
		}
		$this->set('arr_invoices', $arr_invoices);
		$this->set('job_id', $job_id);
		//======================================================
		$arr_tmp['overall_job_totals']['purchaseorder'] = 0;
		$arr_tmp['overall_job_totals']['expenses'] = 0;
		$arr_tmp['overall_job_totals']['employee_time_costs'] = 0;
		$arr_tmp['overall_job_totals']['salesorder'] = 0;
		$this->selectModel('Purchaseorder');
		$purchaseorders = $this->Purchaseorder->select_all(array(
		                                                  'arr_where' => array(
		                                                            'job_id'=>new MongoId($job_id),
		                                                            'purchase_orders_status'=>array('$nin'=>array('Cancelled'))
		                                                                       ),
		                                                  'arr_field' => array('sum_sub_total'),
		                                                  ));
		foreach($purchaseorders as $purchaseorder){
			if(!isset($purchaseorder['sum_sub_total']))
				$purchaseorder['sum_sub_total'] = 0;
			$arr_tmp['overall_job_totals']['purchaseorder'] += $purchaseorder['sum_sub_total'];

		}

		$this->selectModel('Salesorder');
		$this->selectModel('Company');
		$this->selectModel('Contact');
		$this->selectModel('Product');
		$this->selectModel('Stuffs');
		$salesorders = $this->Salesorder->select_all(array(
		                                                  'arr_where' => array(
		                                                            'job_id'=>new MongoId($job_id),
		                                                            'status'=>array('$nin'=>array('Cancelled'))
		                                                                       ),
		                                                  'arr_field' => array('sum_sub_total','company_id', 'shipping_cost'),
		                                                  ));
		foreach($salesorders as $salesorder){
	        $company_id = $salesorder['company_id'];
			if(!is_object($company_id)) continue;
            $minimum = $this->get_minimum_order($salesorder['company_id']);
			if($salesorder['sum_sub_total']<$minimum)
				$salesorder['sum_sub_total'] = $minimum;

			$arr_tmp['overall_job_totals']['salesorder'] += $salesorder['sum_sub_total'];

		}
		//===========================================================================
		$this->selectModel('Shipping');
		$shippings = $this->Shipping->select_all(array(
		                            'arr_where' => array(
                                                            'job_id'=>new MongoId($job_id),
                                                            'status'=>array('$nin'=>array('Cancelled'))
                                                                       ),
		                            'arr_field' => array('shipping_cost'),
		                            ));
		foreach($shippings as $shipping){
			if(!isset($shipping['shipping_cost']))
				$shipping['shipping_cost'] = 0;
			$arr_tmp['overall_job_totals']['expenses'] += $shipping['shipping_cost'];

		}
		$arr_tmp['overall_job_totals']['total_costs'] = $arr_tmp['overall_job_totals']['purchaseorder'] + $arr_tmp['overall_job_totals']['expenses'] + $arr_tmp['overall_job_totals']['employee_time_costs'] + $arr_tmp['overall_job_totals']['commission'];
		$arr_tmp['overall_job_totals']['profit'] = $arr_tmp['overall_job_totals']['salesorder'] - $arr_tmp['overall_job_totals']['total_costs'];

		$arr_tmp['overall_job_totals']['margin'] = $arr_tmp['overall_job_totals']['profit']/($arr_tmp['overall_job_totals']['salesorder']==0 ? 1 : $arr_tmp['overall_job_totals']['salesorder'])*100;
		$this->set('data', $arr_tmp);
	}

	function overall_job_totals_save($id){
		if($_POST){
			$this->selectModel('Job');
			$arr_save = $this->Job->select_one(array('_id'=>new MongoId($id)));

			$arr_save['overall_job_totals'][$_POST['field']] = $_POST['value'];
			if($this->Job->save($arr_save)){
				echo 'ok';
			}else{
				echo 'Error: '. $this->Job->arr_errors_save[1];
			}
		}
		die;
	}

	function invoices_add($job_id,$option=false) {
		$arr_save = array();
		$this->selectModel('Job');
		$arr_job = $this->Job->select_one(array('_id' => new MongoId($job_id)));
		$arr_save['company_name'] = (isset($arr_job['company_name']) ? $arr_job['company_name'] : '');
		$arr_save['company_id'] = (isset($arr_job['company_id']) ? $arr_job['company_id'] : '');
		if(is_object($arr_save['company_id'])){
			$this->selectModel('Company');
			$company = $this->Company->select_one(array('_id'=>$arr_save['company_id']),array('our_rep','our_rep_id','our_csr','our_csr_id'));
			if(isset($company['our_rep_id']) && is_object($company['our_rep_id'])){
				$arr_save['our_rep'] = $company['our_rep'];
				$arr_save['our_rep_id'] = $company['our_rep_id'];
			}
			if(isset($company['our_csr_id']) && is_object($company['our_csr_id'])){
				$arr_save['our_csr'] = $company['our_csr'];
				$arr_save['our_csr_id'] = $company['our_csr_id'];
			}
		}
		$arr_save['contact_name'] = (isset($arr_job['contact_name']) ? $arr_job['contact_name'] : '');
		$arr_save['contact_id'] = (isset($arr_job['contact_id']) ? $arr_job['contact_id'] : '');
		$arr_save['job_name'] = $arr_job['name'];
		$arr_save['job_number'] = $arr_job['no'];
		$arr_save['job_id'] = $arr_job['_id'];
		$arr_save = array_merge($arr_save,$this->get_tax($arr_job));
		$arr_save = array_merge($arr_save,$this->rebuild_job_address($arr_job));
		$this->selectModel('Salesinvoice');
		$arr_save['code'] = $this->Salesinvoice->get_auto_code('code');
		$arr_save['heading'] = (isset($arr_job['name']) ? $arr_job['name'] : '');
		$arr_save['name'] = $arr_save['code'].' - '.$arr_save['company_name'];
		$this->Salesinvoice->arr_default_before_save = $arr_save;
		if ($this->Salesinvoice->add()){
			$salesinvoice_id = $this->Salesinvoice->mongo_id_after_save;
			if($option){
				echo URL.'/salesinvoices/entry/' . $salesinvoice_id;
				die;
			}
			$this->redirect('/salesinvoices/entry/' . $salesinvoice_id);
		}
		if($option){
			echo URL.'/jobs/entry/' . $job_id;
			die;
		}
		$this->redirect('/jobs/entry/' . $job_id);
	}

	function invoices_delete($id) {
		$arr_save['_id'] = new MongoID($id);
		$arr_save['job_id'] = $arr_save['job_number'] = $arr_save['job_name'] = '';
		$error = 0;
		if (!$error) {
			$this->selectModel('Salesinvoice');
			if ($this->Salesinvoice->save($arr_save)) {
				echo 'ok';
			} else {
				echo 'Error: ' . $this->Salesinvoice->arr_errors_save[1];
			}
		}
		die;
	}
	function purchaseorder($job_id) {
		$this->selectModel('Purchaseorder');
		$arr_purchaseorders= $this->Purchaseorder->select_all(array(
			'arr_where' => array('job_id' => new MongoId($job_id)),
			'arr_field' => array('sum_sub_total','purchord_date','purchase_orders_status','our_rep','code','company_name')
		));
		$this->set('arr_purchaseorders', $arr_purchaseorders);
		$this->set('job_id', $job_id);
	}
	function purchaseorder_delete($id) {
		$arr_save['_id'] = new MongoID($id);
		$arr_save['job_id'] = $arr_save['job_number'] = $arr_save['job_name'] = '';
		$error = 0;
		if (!$error) {
			$this->selectModel('Purchaseorder');
			if ($this->Purchaseorder->save($arr_save)) {
				echo 'ok';
			} else {
				echo 'Error: ' . $this->Purchaseorder->arr_errors_save[1];
			}
		}
		die;
	}
	function purchasesorder_add($job_id,$option=false) {
		$arr_save = array();
		$this->selectModel('Job');
		$arr_job = $this->Job->select_one(array('_id' => new MongoId($job_id)));
		$arr_save['company_name'] = (isset($arr_job['company_name']) ? $arr_job['company_name'] : '');
		$arr_save['company_id'] = (isset($arr_job['company_id']) ? $arr_job['company_id'] : '');
		$arr_save['contact_name'] = (isset($arr_job['contact_name']) ? $arr_job['contact_name'] : '');
		$arr_save['contact_id'] = (isset($arr_job['contact_id']) ? $arr_job['contact_id'] : '');
		$arr_save['job_name'] = $arr_job['name'];
		$arr_save['job_number'] = $arr_job['no'];
		$arr_save['job_id'] = $arr_job['_id'];
		$arr_save = array_merge($arr_save,$this->get_tax($arr_job));
		$arr_save = array_merge($arr_save,$this->rebuild_job_address($arr_job));
		$this->selectModel('Purchaseorder');
		$this->Purchaseorder->arr_default_before_save = $arr_save;
		if ($this->Purchaseorder->add()){
			$purchaseorder_id = $this->Purchaseorder->mongo_id_after_save;
			if($option){
				echo URL.'/purchaseorders/entry/' . $purchaseorder_id;
				die;
			}
			$this->redirect('/purchaseorders/entry/' . $purchaseorder_id);
		}
		if($option){
			echo URL.'/jobs/entry/' . $job_id;
			die;
		}
		$this->redirect('/jobs/entry/' . $job_id);
	}

	function assembly() {

	}

	function resources($job_id) {
		// get all equipments are used for this job
		$this->selectModel('Resource');
		$arr_job = $this->Resource->select_all(array(
			'arr_where' => array('module_id' => new MongoId($job_id)),
		));
		$this->set('arr_job', $arr_job);
		$this->set('job_id', $job_id);
		$this->selectModel('Setting');
		$this->set('arr_equipments_status', $this->Setting->select_option(array('setting_value' => 'equipments_status'), array('option')));
	}

	function resources_auto_save() {
		if (!empty($this->data)) {
			foreach ($this->data['Resource'] as $value) {
				$arr_save = $value;
			}

			$work_start_sec = $this->Common->strtotime($arr_save['work_start'] . '' . $arr_save['work_start_hour'] . ':00');
			$work_end_sec = $this->Common->strtotime($arr_save['work_end'] . '' . $arr_save['work_end_hour'] . ':00');
			if ($work_start_sec > $work_end_sec) {
				echo 'date_work';
				die;
			}
			$arr_save['work_start'] = new MongoDate($work_start_sec);
			$arr_save['work_end'] = new MongoDate($work_end_sec);

			$this->selectModel('Resource');
			if ($this->Resource->save($arr_save)) {
				echo 'ok';
			} else {
				echo 'Error: ' . $this->Resource->arr_errors_save[1];
			}
		}
		die;
	}

	function resources_delete($job_id) {
		$arr_save['_id'] = $job_id;
		$arr_save['deleted'] = true;
		$this->selectModel('Resource');
		if ($this->Resource->save($arr_save)) {
			echo 'ok';
		} else {
			echo 'Error: ' . $this->Resource->arr_errors_save[1];
		}
		die;
	}

	function resources_window_choose($job_id, $type, $item_id, $name = '') {
		$arr_save['status'] = 0;

		// thong tin cua contact hoac equipment
		$arr_save['type'] = $type;
		$arr_save['item_id'] = new MongoId($item_id);
		$arr_save['name'] = $name;

		// thong tin cua module duoc chen resource vao
		$this->selectModel('Job');
		$arr_job = $this->Job->select_one(array('_id' => new MongoId($job_id)));
		$arr_save['module'] = 'Job';
		$arr_save['module_id'] = $arr_job['_id'];
		$arr_save['text'] = $arr_job['name'];

		$arr_save['work_start'] = $arr_job['work_start'];
		$arr_save['work_end'] = $arr_job['work_end'];

		if ($arr_save['work_end']->sec > (strtotime('now') + 3600)) {
			$tmp_time = strtotime(date('Y-m-d H') . ':00:00');
			$arr_save['work_start'] = new MongoDate($tmp_time);
			$arr_save['work_end'] = new MongoDate($tmp_time + 3600);
		}

		$this->selectModel('Resource');
		if ($this->Resource->save($arr_save)) {
			echo 'ok';
		} else {
			echo 'Error: ' . $this->Resource->arr_errors_save[1];
		}
		die;
	}

	function other() {

	}

	function lists() {
		$this->set('_controller',$this);
		$this->selectModel('Salesorder');
		$this->set('salesorder',$this->Salesorder);
		$this->selectModel('Salesinvoice');
		$this->set('salesinvoice',$this->Salesinvoice);
		$this->selectModel('Quotation');
		$this->set('quotation',$this->Quotation);
		$this->selectModel('Job');
		$limit = LIST_LIMIT;
		$skip = 0;
		$sort_field = 'work_start';
		$sort_type = 1;
		if( isset($_POST['sort']) && strlen($_POST['sort']['field']) > 0 ){
			if( $_POST['sort']['type'] == 'desc' ){
				$sort_type = -1;
			}
			$sort_field = $_POST['sort']['field'];
			$this->Session->write('jobs_lists_search_sort', array($sort_field, $sort_type));

		}elseif( $this->Session->check('jobs_lists_search_sort') ){
			$session_sort = $this->Session->read('jobs_lists_search_sort');
			$sort_field = $session_sort[0];
			$sort_type = $session_sort[1];
		}
		$arr_order = array($sort_field => $sort_type);
		$this->set('sort_field', $sort_field);
		$this->set('sort_type', ($sort_type === 1)?'asc':'desc');

		$this->selectModel('Contact');
		$this->set('model_contact', $this->Contact);

		// dùng cho điều kiện
		$cond = array();
		if( $this->Session->check('jobs_entry_search_cond') ){
			$cond = $this->Session->read('jobs_entry_search_cond');
		}
		$cond = array_merge($cond, $this->arr_search_where());
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
		$arr_jobs = $this->Job->select_all(array(
			'arr_where' => $cond,
			'arr_order' => $arr_order,
			'limit' => $limit,
			'skip' => $skip,
			'arr_field' => array('no','company_id','company_name','contact_id','contact_name','name','work_end','status','status_id','type')
		));
		$this->set('arr_jobs', $arr_jobs);

		$this->selectModel('Setting');
		$this->set('arr_jobs_type', $this->Setting->select_option(array('setting_value' => 'jobs_type'), array('option')));
		$this->set('arr_jobs_status', $this->Setting->select_option(array('setting_value' => 'jobs_status'), array('option')));

		$total_page = $total_record = $total_current = 0;
		if( is_object($arr_jobs) ){
			$total_current = $arr_jobs->count(true);
			$total_record = $arr_jobs->count();
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
		if($this->Session->check('jobs_entry_search_cond')){
			$cond = $this->Session->read('jobs_entry_search_cond');
			if(isset($cond['work_end']['$gte']))
				$this->set('date_from',$this->Job->format_date($cond['work_end']['$gte']->sec));
			if(isset($cond['work_end']['$lte']))
				$this->set('date_to',$this->Job->format_date($cond['work_end']['$lte']->sec));
		}
		$this->set('sum', $total_record);
	}

	function lists_delete($id = 0) {
		$arr_save['_id'] = $id;
		$arr_save['deleted'] = true;
		$error = 0;
		if (!$error) {
			$this->selectModel('Job');
			if ($this->Job->save($arr_save)) {
				echo 'ok';
			} else {
				echo 'Error: ' . $this->Job->arr_errors_save[1];
			}
		}
		die;
	}

	// ================================== CALENDAR ====================================
	public function calendar($date_from_sec = '', $date_to_sec = '') { // calendar week
		$this->set('set_footer', '../Jobs/calendar_footer');
		$this->Session->write('calendar_last_visit', '/' . $this->request->url);

		$this->selectModel('Setting');
		$arr_option = $this->Setting->select_one(array('setting_value' => 'jobs_status'), array('option'));
		$arr_status = $arr_status_color = array();
		foreach ($arr_option['option'] as $key => $value) {
			if ($value['deleted'])
				continue;
			$arr_status[$key] = $value['name'];
			$arr_status_color[$key] = $value['color'];
		}
		$this->set('arr_jobs_status', $arr_status);
		$this->set('arr_status_color', $arr_status_color);
		$this->set('arr_jobs_type', $this->Setting->select_option(array('setting_value' => 'jobs_type'), array('option')));

		// get all job
		if ($date_from_sec == '') {
			if (date('N') == 1) {
				$date_from_sec = strtotime(date('Y-m-d'));
				$date_to_sec = strtotime('next Sunday');
			} elseif (date('N') == 7) {
				$date_from_sec = strtotime('last Monday');
				$date_to_sec = strtotime(date('Y-m-d'));
			} else {
				$date_from_sec = strtotime('last Monday');
				$date_to_sec = strtotime('next Sunday');
			}
		}
		$this->set('date_from_sec', $date_from_sec);
		$this->set('date_to_sec', $date_to_sec);

		$arr_time_sec['prev'] = array(strtotime('last Monday', $date_from_sec), strtotime('last Sunday', $date_from_sec));
		$arr_time_sec['next'] = array(strtotime('next Monday', $date_to_sec), strtotime('next Sunday', $date_to_sec));
		$this->set('arr_time_sec', $arr_time_sec);

		$arr_where['$or'] = array(
			array(
				'work_start' => array('$lte' => new MongoDate($date_from_sec)),
				'work_end' => array('$gte' => new MongoDate($date_from_sec))
			),
			array(
				'work_start' => array('$lte' => new MongoDate($date_to_sec)),
				'work_end' => array('$gte' => new MongoDate($date_to_sec))
			),
			array(
				'work_start' => array('$gte' => new MongoDate($date_from_sec)),
				'work_end' => array('$lte' => new MongoDate($date_to_sec))
			)
		);

		$this->selectModel('Job');
		$arr_jobs_tmp = $this->Job->select_all(array(
			'arr_where' => $arr_where,
			'limit' => 1000,
			'maxLimit' => 1000
		));
		$arr_jobs = $arr_contact_id = array();
		foreach ($arr_jobs_tmp as $value) {
			$arr_jobs[] = $value;
			$arr_contact_id[] = $value['contacts'][$value['contacts_default_key']]['contact_id'];
		}
		$this->set('arr_jobs', $arr_jobs);

		$this->selectModel('Contact');
		$arr_contact = $this->Contact->select_list(array(
			//'arr_where' => array('_id' => array('$in' => $arr_contact_id)),
			'arr_where' => array('is_employee' => 1),
			'arr_field' => array('_id', 'first_name', 'last_name'),
			'arr_order' => array('first_name' => 1),
		));
		$this->set('arr_contact', $arr_contact);

		$this->layout = 'calendar';
	}

	public function _get_beginning_month_datetime($current_view_date) {
		$time_sec = strtotime(date($current_view_date));

		// tìm ngày đầu tiên của tuần chứa ngày 01
		if (date('N', $time_sec) == 1) {
			$date_from_sec = strtotime(date('Y-m-d', $time_sec));
		} else {
			$date_from_sec = strtotime('last Monday', $time_sec);
		}
		return $date_from_sec;
	}

	public function calendar_month($current_view_date = 'Y-m-01') {

		$this->set('set_footer', '../Jobs/calendar_footer');
		$this->Session->write('calendar_last_visit', '/' . $this->request->url);

		$this->selectModel('Setting');
		$arr_option = $this->Setting->select_one(array('setting_value' => 'jobs_status'), array('option'));
		$arr_status = $arr_status_color = array();
		foreach ($arr_option['option'] as $key => $value) {
			if ($value['deleted'])
				continue;
			$arr_status[$key] = $value['name'];
			$arr_status_color[$key] = $value['color'];
		}
		$this->set('arr_jobs_status', $arr_status);
		$this->set('arr_status_color', $arr_status_color);
		// $this->set( 'arr_jobs_type', $this->Setting->select_option(array('setting_value' => 'jobs_type'), array('option')) );
		// get all job
		// echo ;
		$date_from_sec = $this->_get_beginning_month_datetime($current_view_date);
		$date_to_sec = $date_from_sec + 41 * DAY;
		$this->set('date_from_sec', $date_from_sec);
		$this->set('date_to_sec', $date_to_sec);

		$this->set('current_view_date', $current_view_date);

		$arr_time_sec['prev'] = array(date('Y-m-01', strtotime('first day of previous month', strtotime(date($current_view_date)))), '');
		$arr_time_sec['next'] = array(date('Y-m-01', strtotime('first day of next month', strtotime(date($current_view_date)))), '');
		$this->set('arr_time_sec', $arr_time_sec);

// echo date('d/m/Y',$date_from_sec ); die;
// echo date('d/m/Y',$date_to_sec ); die;

		$arr_where['$or'] = array(
			array(
				'work_start' => array('$lte' => new MongoDate($date_from_sec)),
				'work_end' => array('$gte' => new MongoDate($date_from_sec))
			),
			array(
				'work_start' => array('$lte' => new MongoDate($date_to_sec)),
				'work_end' => array('$gte' => new MongoDate($date_to_sec))
			),
			array(
				'work_start' => array('$gte' => new MongoDate($date_from_sec)),
				'work_end' => array('$lte' => new MongoDate($date_to_sec))
			)
		);

		$this->selectModel('Job');
		$arr_jobs_tmp = $this->Job->select_all(array(
			'arr_where' => $arr_where,
			'limit' => 1000,
			'maxLimit' => 1000
		));

		$arr_jobs = $arr_contact_id = array();
		foreach ($arr_jobs_tmp as $value) {
			$arr_jobs[] = $value;
		}
		$this->set('arr_jobs', $arr_jobs);

		$this->layout = 'calendar';
	}

	public function calendar_day($date_from_sec = '') {

		$this->set('set_footer', '../Jobs/calendar_footer');
		$this->Session->write('calendar_last_visit', '/' . $this->request->url);

		$this->selectModel('Setting');
		$arr_option = $this->Setting->select_one(array('setting_value' => 'jobs_status'), array('option'));
		$arr_status = $arr_status_color = array();
		foreach ($arr_option['option'] as $key => $value) {
			if ($value['deleted'])
				continue;
			$arr_status[$key] = $value['name'];
			$arr_status_color[$key] = $value['color'];
		}
		$this->set('arr_jobs_status', $arr_status);
		$this->set('arr_status_color', $arr_status_color);
		$this->set('arr_jobs_type', $this->Setting->select_option(array('setting_value' => 'jobs_type'), array('option')));

		// get all job
		if ($date_from_sec == '') {
			$date_from_sec = strtotime(date('Y-m-d'));
		}

		$date_to_sec = $date_from_sec + DAY - 1;

		$arr_time_sec['prev'] = array($date_from_sec - DAY, '');
		$arr_time_sec['next'] = array($date_from_sec + DAY, '');
		$this->set('arr_time_sec', $arr_time_sec);

		$this->set('date_from_sec', $date_from_sec);
		$this->set('date_to_sec', $date_to_sec);

		$arr_where['$or'] = array(
			array(
				'work_start' => array('$lte' => new MongoDate($date_from_sec)),
				'work_end' => array('$gte' => new MongoDate($date_from_sec))
			),
			array(
				'work_start' => array('$lte' => new MongoDate($date_to_sec)),
				'work_end' => array('$gte' => new MongoDate($date_to_sec))
			),
			array(
				'work_start' => array('$gte' => new MongoDate($date_from_sec)),
				'work_end' => array('$lte' => new MongoDate($date_to_sec))
			)
		);

		$this->selectModel('Job');
		$arr_jobs_tmp = $this->Job->select_all(array(
			'arr_where' => $arr_where,
			'limit' => 1000,
			'maxLimit' => 1000
		));
		$this->set('arr_jobs', $arr_jobs_tmp);

		$this->layout = 'calendar';
	}

	// Popup form orther module
	// public function popup($key = "") {

	// 	$this->set('key', $key);

	// 	$cond = array('status_id'=>array('$ne'=>'Completed'));
	// 	// if(!empty($this->data)){
	// 	// 	$arr_post = $this->data['Job'];
	// 	// 	$cond['name'] = new MongoRegex('/'.$arr_post['name'].'/i');
	// 	// 	$cond['inactive'] = $arr_post['inactive'];
	// 	// 	if( is_numeric($arr_post['is_customer']) )
	// 	// 		$cond['is_customer'] = $arr_post['is_customer'];
	// 	// }

	// 	$this->selectModel('Job');
	// 	$arr_jobs = $this->Job->select_all(array(
	// 		'arr_where' => $cond,
	// 		'arr_order' => array('_id' => -1),
	// 			// 'arr_field' => array('name', 'is_customer', 'is_employee', 'default_address_1', 'default_address_2', 'default_address_3', 'default_town_city', 'default_country_name', 'default_province_state_name', 'default_zip_postcode', 'phone')
	// 	));
	// 	$this->set('arr_jobs', $arr_jobs);

	// 	$this->layout = 'ajax';
	// }

	public function popup($key = '') {
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
                $tmp['Job']['company'] = $_GET['company_name'];
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
        $arr_order = array('_id' => -1);
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
        if (!empty($this->data) && !empty($_POST) && isset($this->data['Job'])) {
            $arr_post = $this->Common->strip_search($this->data['Job']);
            if (isset($arr_post['contact_name']) && strlen($arr_post['contact_name']) > 0) {
                $cond['contact_name'] = new MongoRegex('/' . trim($arr_post['contact_name']) . '/i');
            }

            if (isset($arr_post['company']) && strlen($arr_post['company']) > 0) {
                $cond['company_name'] = new MongoRegex('/' . trim($arr_post['company']) . '/i');
            }

            if (isset($arr_post['no']) && strlen($arr_post['no']) > 0) {
                $cond['no'] = trim($arr_post['no']);
            }
        }
        $cond['status_id']['$ne'] = 'Completed';
        unset($_GET['_']);
        $cache_key = md5(serialize($_GET));
        $no_cache = true;
        $this->selectModel('Job');
        if(empty($_POST) ){
            $arr_job = Cache::read('popup_job_'.$cache_key);
            if($arr_job)
                $no_cache = false;
        }
        if($no_cache){
	        $arr_job = $this->Job->select_all(array(
	            'arr_where' => $cond,
	            'arr_order' => $arr_order,
	            'limit' => $limit,
	            'skip' => $skip,
	            'arr_field' => array('name', 'company_id', 'company_name', 'contact_id', 'contact_name','our_rep_id','our_rep','custom_po_no','no')
	        ));
	        if(empty($_POST))
                Cache::write('popup_job_'.$cache_key,iterator_to_array($arr_job));
        }
        $this->set('arr_job', $arr_job);

        $total_page = $total_record = $total_current = 0;
        if (is_object($arr_job)) {
            $total_current = $arr_job->count(true);
            $total_record = $arr_job->count();
            if ($total_record % $limit != 0) {
                $total_page = floor($total_record / $limit) + 1;
            } else {
                $total_page = $total_record / $limit;
            }
        } else if(is_array($arr_job)){
            $total_current = count($arr_job);
            $total_record = $this->Job->count($cond);
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

	public function quick_view() {

		$this->selectModel('Job');
		$this->selectModel('Contact');
		$this->selectModel('Setting');
		// set default
		$cond = array();
		$order = array('_id' => -1);
		// load dong thu may trong csdl
		$skip = 0;
		$limit = LIST_LIMIT;
		// check ajax
		if ($this->request->is('ajax')) {
			// seach
			if ($_REQUEST['identity']) {
				$cond['type'] = new MongoRegex('/' . $_REQUEST['identity'] . '/i');
			}
			if ($_REQUEST['filter_list_below']) {
				$cond['contact_name'] = new MongoRegex('/' . $_REQUEST['filter_list_below'] . '/i');
			}
			if ($_REQUEST['our_rep']) {
				$cond['contacts'] = array(
					'$elemMatch' => array(
						'contact_name' => new MongoRegex('/' . $_REQUEST['our_rep'] . '/i'),
						'default' => true
					)
				);
			}
			// end seach
			// set offset load_more
			if (isset($_REQUEST['offset'])) {
				$skip = $_REQUEST['offset'];
			}
			// sort
			$sort_key = $_REQUEST['sort_key'];
			$sort_type = $_REQUEST['sort_type'];
			// kiem tran sort type roi gan gia tri "asc = 1;  desc = -1 "
			if ($sort_type == 'desc') {
				$sort = -1;
			}
			if ($sort_type == 'asc') {
				$sort = 1;
			}
			// order sub
			if ($sort_key == 'contact_name') {
				$order = array('contacts.contact_name' => $sort);
			} else {
				$order = array($sort_key => $sort);
			}

			$this->Session->write('jobs_quick_view_search', array($cond, $order));
		} elseif ($this->Session->check('jobs_quick_view_search')) {
			$seach_tmp = $this->Session->read('jobs_quick_view_search');
			$cond = $seach_tmp[0];
			$order = $seach_tmp[1];
		}
		// end ajax
		// jobs list
		$this->set('arr_jobs', $this->Job->select_all(array(
					'arr_where' => $cond,
					'arr_order' => $order,
					'limit' => $limit,
					'skip' => $skip
		)));
		if ($this->request->is('ajax')) {
			$this->render('quick_view_ajax');
		}
		// jobs type list
		$this->set('arr_jobs_type', $this->Setting->select_option(array('setting_value' => 'jobs_type'), array('option')));
		// contact list
		$contact_list = $this->Contact->select_all(array(
			'arr_where' => array(
				'is_employee' => 1
			),
			'arr_field' => array('first_name', '_id')
		));
		foreach ($contact_list as $value) {
			$contact_list_tmp[] = (string) $value['first_name'];
		}
		$this->set('arr_contact_list', $contact_list_tmp);
	}
	//Tung
	function swith_options($keys=''){
        parent::swith_options($keys);
		$current_date = strtotime(date('Y-m-d'));
		if($keys=='not_started_'){
			$arr_where['status_id'] = array('values' => 'Not Started', 'operator' => '=');
			$this->Session->write($this->name . '_where', $arr_where);
			echo URL . '/' . $this->params->params['controller'] . '/lists';
		} else if ($keys == 'in_progess'){
			$arr_where['status_id'] = array('values' => 'In Progress', 'operator' => '=');
			$this->Session->write($this->name . '_where', $arr_where);
			echo URL . '/' . $this->params->params['controller'] . '/lists';
		} else if( $keys == 'ongoing'){
			$arr_where['status_id'] = array('values' => 'On Going', 'operator' => '=');
			$this->Session->write($this->name . '_where', $arr_where);
			echo URL . '/' . $this->params->params['controller'] . '/lists';
		} else if ($keys == 'not_complete'){
			$arr_where['work_end'] = array('values' => new MongoDate($current_date), 'operator' => '>');
			$this->Session->write($this->name . '_where', $arr_where);
			echo URL . '/' . $this->params->params['controller'] . '/lists';
		} else if( $keys == 'complete'){
			$arr_where['status_id'] = array('values' => 'Completed', 'operator' => '=');
			$this->Session->write($this->name . '_where', $arr_where);
			echo URL . '/' . $this->params->params['controller'] . '/lists';
		} else if ( $keys == 'on_hold' ){
			$arr_where['status_id'] = array('values' => 'On Hold', 'operator' => '=');
			$this->Session->write($this->name . '_where', $arr_where);
			echo URL . '/' . $this->params->params['controller'] . '/lists';
		} else if( $keys == 'cancelled' ){
			$arr_where['status_id'] = array('values' => 'Cancelled', 'operator' => '=');
			$this->Session->write($this->name . '_where', $arr_where);
			echo URL . '/' . $this->params->params['controller'] . '/lists';
		} else if ( $keys == 'late' ){
			$arr_where['work_end'] = array('values' => new MongoDate($current_date), 'operator' => '>');
			$this->Session->write($this->name . '_where', $arr_where);
			echo URL . '/' . $this->params->params['controller'] . '/lists';
		} else if ( $keys == 'create_quotation' ){
			$this->quotes_add($this->get_id(),true);
		} else if ( $keys == 'create_sales_order' ){
			$this->orders_add_salesorder($this->get_id(),true);
		} else if ( $keys == 'create_purchases_order' ) {
			$this->purchasesorder_add($this->get_id(),true);
		} else if ( $keys == 'create_sales_invoice'){
			$this->invoices_add($this->get_id(),true);
		} else if ( $keys == 'create_shipping' ){
			$this->shipping_add($this->get_id(),true);
		} else if ( $keys == 'duplicate_job')
			$this->duplicate_job();
		else if($keys == 'create_email')
			echo URL .'/'.$this->params->params['controller']. '/create_email';
		else if($keys == 'create_fax')
			echo URL .'/'.$this->params->params['controller']. '/create_fax';
		else if($keys == 'create_letter')
			echo URL .'/'.$this->params->params['controller']. '/create_letter';
		else if($keys == 'detailed_area_report')
			echo URL .'/'.$this->params->params['controller']. '/detailed_area_report';
		else if($keys == 'summary_area_report')
			echo URL .'/'.$this->params->params['controller']. '/summary_area_report';

		die;
	}

	function create_email(){
		$this->selectModel('Job');
		$arr_job = $this->Job->select_one(array('_id' => new MongoId($this->get_id())));

		$arr_save = array();
		$this->selectModel('Communication');
		$arr_save['code'] = $this->Communication->get_auto_code('code');

		$arr_save['comms_type'] = 'Email';

		$arr_save['comms_date'] = new MongoDate();
		$arr_save['job_name'] = isset($arr_job['name'])?$arr_job['name']:'';
		$arr_save['job_number'] = isset($arr_job['no'])?$arr_job['no']:'';
		$arr_save['sign_off'] = 'Regards';
		$arr_save['company_id'] = isset($arr_job['company_id'])?$arr_job['company_id']:'';
		$arr_save['company_name'] = isset($arr_job['company_name'])?$arr_job['company_name']:'';
		$arr_save['email'] = isset($arr_job['email'])?$arr_job['email']:'';
		$arr_save['module']=isset($this->params->params['controller'])?$this->params->params['controller']:'';

		$this->selectModel('Contact');
		$arr_contact = $arr_temp = array();
		if(isset($arr_job['contact_id']) && is_object($arr_job['contact_id'])){
			$arr_contact = $this->Contact->select_one(array('_id' => new MongoId($arr_job['contact_id'])));
		}
		else{
			$arr_contact = $this->Contact->select_all(array(
				'arr_where' => array('company_id'=> new MongoId($arr_job['_id'])),
				'arr_order' => array('_id'=> -1),
			));
			$arrtemp = iterator_to_array($arr_contact);
			if(count($arr_temp) > 0){
				$arr_contact = current($arrtemp);
			}else
				$arr_contact = array();
		}
		if(isset($arr_contact['_id'])){
			$arr_save['contact_name'] = isset($arr_contact['first_name'])?$arr_contact['first_name']:'';
			$arr_save['last_name']= isset($arr_contact['last_name'])?$arr_contact['last_name']:'';
			$arr_save['contact_id'] = $arr_contact['_id'];
		}
		else{
			$arr_save['contact_name']='';
			$arr_save['contact_id']='';
		}
		$arr_save['contact_from_id']=$this->Contact->user_id();
		$arr_save['contact_from']=$this->Contact->user_name();

		if ($this->Communication->save($arr_save)) {
			$this->redirect('/communications/entry/'. $this->Communication->mongo_id_after_save);
		}
		$this->redirect('/communications/entry');
	}

	function create_fax(){
		$this->selectModel('Job');
		$arr_job = $this->Job->select_one(array('_id' => new MongoId($this->get_id())));

		$arr_save = array();
		$this->selectModel('Communication');
		$arr_save['code'] = $this->Communication->get_auto_code('code');

		$arr_save['comms_type'] = 'Fax';

		$arr_save['phone'] = isset($arr_job['company_phone'])?$arr_job['company_phone']:'';
		$arr_save['fax'] = isset($arr_job['fax'])?$arr_job['fax']:'';
		$arr_save['comms_date'] = new MongoDate();
		$arr_save['job_name'] = isset($arr_job['name'])?$arr_job['name']:'';
		$arr_save['job_number'] = isset($arr_job['no'])?$arr_job['no']:'';
		$arr_save['sign_off'] = 'Regards';
		$arr_save['company_id'] = isset($arr_job['company_id'])?$arr_job['company_id']:'';
		$arr_save['company_name'] = isset($arr_job['company_name'])?$arr_job['company_name']:'';
		$arr_save['email'] = isset($arr_job['email'])?$arr_job['email']:'';
		$arr_save['module']=isset($this->params->params['controller'])?$this->params->params['controller']:'';

		$this->selectModel('Contact');
		$arr_contact = $arr_temp = array();
		if(isset($arr_job['contact_id']) && is_object($arr_job['contact_id'])){
			$arr_contact = $this->Contact->select_one(array('_id' => new MongoId($arr_job['contact_id'])));
		}
		else{
			$arr_contact = $this->Contact->select_all(array(
				'arr_where' => array('company_id'=> new MongoId($arr_job['_id'])),
				'arr_order' => array('_id'=> -1),
			));
			$arrtemp = iterator_to_array($arr_contact);
			if(count($arr_temp) > 0){
				$arr_contact = current($arrtemp);
			}else
				$arr_contact = array();
		}
		if(isset($arr_contact['_id'])){
			$arr_save['contact_name'] = isset($arr_contact['first_name'])?$arr_contact['first_name']:'';
			$arr_save['last_name']= isset($arr_contact['last_name'])?$arr_contact['last_name']:'';
			$arr_save['contact_id'] = $arr_contact['_id'];
		}
		else{
			$arr_save['contact_name']='';
			$arr_save['contact_id']='';
		}
		$arr_save['contact_from_id']=$this->Contact->user_id();
		$arr_save['contact_from']=$this->Contact->user_name();

		if ($this->Communication->save($arr_save)) {
			$this->redirect('/communications/entry/'. $this->Communication->mongo_id_after_save);
		}
		$this->redirect('/communications/entry');
	}

	function create_letter(){
		$this->selectModel('Job');
		$arr_job = $this->Job->select_one(array('_id' => new MongoId($this->get_id())));

		$arr_save = array();
		$this->selectModel('Communication');
		$arr_save['code'] = $this->Communication->get_auto_code('code');

		$arr_save['comms_type'] = 'Letter';

		$arr_save['phone'] = isset($arr_job['company_phone'])?$arr_job['company_phone']:'';
		$arr_save['fax'] = isset($arr_job['fax'])?$arr_job['fax']:'';
		$arr_save['comms_date'] = new MongoDate();
		$arr_save['job_name'] = isset($arr_job['name'])?$arr_job['name']:'';
		$arr_save['job_number'] = isset($arr_job['no'])?$arr_job['no']:'';
		$arr_save['sign_off'] = 'Regards';
		$arr_save['company_id'] = isset($arr_job['company_id'])?$arr_job['company_id']:'';
		$arr_save['company_name'] = isset($arr_job['company_name'])?$arr_job['company_name']:'';
		$arr_save['email'] = isset($arr_job['email'])?$arr_job['email']:'';
		$arr_save['module']=isset($this->params->params['controller'])?$this->params->params['controller']:'';

		$this->selectModel('Contact');
		$arr_contact = $arr_temp = array();
		if(isset($arr_job['contact_id']) && is_object($arr_job['contact_id'])){
			$arr_contact = $this->Contact->select_one(array('_id' => new MongoId($arr_job['contact_id'])));
		}
		else{
			$arr_contact = $this->Contact->select_all(array(
				'arr_where' => array('company_id'=> new MongoId($arr_job['_id'])),
				'arr_order' => array('_id'=> -1),
			));
			$arrtemp = iterator_to_array($arr_contact);
			if(count($arr_temp) > 0){
				$arr_contact = current($arrtemp);
			}else
				$arr_contact = array();
		}
		if(isset($arr_contact['_id'])){
			$arr_save['contact_name'] = isset($arr_contact['first_name'])?$arr_contact['first_name']:'';
			$arr_save['last_name']= isset($arr_contact['last_name'])?$arr_contact['last_name']:'';
			$arr_save['contact_id'] = $arr_contact['_id'];
		}
		else{
			$arr_save['contact_name']='';
			$arr_save['contact_id']='';
		}
		$arr_save['contact_from_id']=$this->Contact->user_id();
		$arr_save['contact_from']=$this->Contact->user_name();

		if ($this->Communication->save($arr_save)) {
			$this->redirect('/communications/entry/'. $this->Communication->mongo_id_after_save);
		}
		$this->redirect('/communications/entry');
	}

	public function duplicate_job()
	{
		$this->selectModel('Job');
		$current_job = $this->Job->select_one(array('_id'=>new MongoId($this->get_id())));
		//kei, giữ id để tìm resource và tạo mới cho new job
		$current_id = $current_job['_id'];
		unset($current_job['_id']);
		//reset work_start work_end
		$current_job['work_end'] = $current_job['work_start'] = new MongoDate(strtotime(date('Y-m-d H:00:00')) + 3600);
		unset($current_job['modified_by']);
		unset($current_job['date_modified']);
		$new_job = $current_job;
		$new_job['no'] = $this->Job->get_auto_code('no');
		//new job mặc định status sẽ là not started, giống như khi new
		$new_job['status_id'] = 'Not Started';
		$new_job['status'] = 'Not Started';
		$new_job['created_by'] = new MongoId($this->Job->user_id());
		$new_id = '';
		if($this->Job->save($new_job)){
			$new_id = $this->Job->mongo_id_after_save;
			$this->selectModel('Resource');
			$current_resources = $this->Resource->select_all(array('arr_where'=>array('module_id'=>new MongoId($current_id))));
			if($current_resources->count()>0){
				foreach($current_resources as $value){
					//Moi record resource tìm thấy từ tạo mới 1 resource tương tự nhưng với module id là new_job
					if($value['deleted']) continue;
					$tmp_time = strtotime(date('Y-m-d H') . ':00:00');
					unset($value['_id']);
					$value['created_by'] = new MongoId($this->Job->user_id());
					$value['module_id'] = new MongoId($new_id);
					$value['status'] = 'New';
					$arr_save['work_start'] = new MongoDate($tmp_time);
					$arr_save['work_end'] = new MongoDate($tmp_time + 3600);
					$this->Resource->save($value);
				}
			}
		}
		echo URL.'/jobs/entry/'.$new_id;
		die;
	}
	function get_tax($arr_job=array()){
		$arr_return = array();
		if(!empty($arr_job)){
			$this->selectModel('Tax');
			$key_tax = '';
			if(isset($arr_job['invoice_province_state_id'])&&$arr_job['invoice_province_state_id']!='')
				$key_tax = $arr_job['invoice_province_state_id'];
			else if(isset($arr_job['shipping_province_state_id'])&&$arr_job['shipping_province_state_id']!='')
				$key_tax = $arr_job['shipping_province_state_id'];
			$arr_tax = $this->Tax->tax_select_list();
			if(isset($arr_tax[$key_tax])){
				$tax = explode("%",$arr_tax[$key_tax]);
				$arr_return['tax'] = $key_tax;
				$arr_return['taxval'] = (float)$tax[0];
			}
		}
		return $arr_return;
	}
	function save_company_address()
	{
		$this->selectModel('Job');
		$job = $this->Job->select_one(array('_id'=>new MongoId($this->get_id())),array('company_id'));
		if(isset($job['company_id'])&&is_object($job['company_id'])){
			$this->selectModel('Company');
			$company = $this->Company->select_one(array('_id'=> new MongoId($job['company_id'])),array('addresses_default_key','addresses'));
			$key = 0;
			if(isset($company['addresses_default_key']))
				$key = $company['addresses_default_key'];
			foreach($company['addresses'][$key] as $k=>$value)
				$job['invoice_'.$k] = $value;
			if($this->Job->save($job))
				echo 'ok';
			else
				echo 'Error: ' . $this->Job->arr_errors_save[1];
		}
		die;
	}
	function rebuild_job_address($arr_job=array(),$type='')
	{
		$arr_return = array();
		if(!empty($arr_job)){
			//được 1 mảng với value là key của arr_job với preg match tương ứng
			//$match_invoice = aray(
			//		16=>'invoice_address_1',
			//		17=>'invoice_address_2'
			//	);
			// do nó lấy luôn khóa của mảng $arr_job
			$match_invoice = preg_grep('/^invoice_/',array_keys($arr_job));
			$match_shipping = preg_grep('/^shipping_/',array_keys($arr_job));
			if(!empty($match_invoice)){
				//Neu la shipping thi doi ngc invoice_address la shipping_address
				if($type=='shipping'){
					foreach($match_invoice as $k=>$value){
						$key = str_replace('invoice_', 'shipping_', $value);
						$match_invoice[$key] =  $value;
						unset($match_invoice[$k]);
					}
					foreach($match_invoice as $k=>$value)
						$arr_return['shipping_address'][0][$k] = $arr_job[$value];
				}
				else
					foreach($match_invoice as $value)
						$arr_return['invoice_address'][0][$value] = $arr_job[$value];
			}
			if(!empty($match_shipping)){
				//Neu la shipping thi doi ngc shipping_address la invoice_address
				if($type=='shipping'){
					foreach($match_shipping as $k=>$value){
						$key = str_replace('shipping', 'invoice', $value);
						$match_shipping[$key] =  $value;
						unset($match_shipping[$k]);
					}
					foreach($match_shipping as $k=>$value)
						$arr_return['invoice_address'][0][$k] = $arr_job[$value];
				}
				else
					foreach($match_shipping as $value)
						$arr_return['shipping_address'][0][$value] = $arr_job[$value];
			}
		}
		return $arr_return;
	}

	public function view_minilist(){
		if(!isset($_GET['print_pdf'])){
			$arr_where = array();
			$this->selectModel('Job');
			$jobs = $this->Job->select_all(array(
											'arr_where' => $arr_where,
											'arr_field' => array('no','name','company_name','contact_name','company_phone','work_start','work_end','status'),
											'arr_order' => array('_id'=> 1),
											'limit' => 2000
											));
			$arr_data = array();
			if($jobs->count() > 0){
				$html='';
				$i=0;
				$current_date = strtotime(date("m/d/Y"));
				foreach($jobs as $key => $job){
					$html .= '<tr class="'.($i%2==0 ? 'bg_2' : 'bg_1').'">';
					$html .= '<td>'.$job['no'].'</td>';
					$html .= '<td>'.(isset($job['name']) ? $job['name'] : '') .'</td>';
					$html .= '<td>'.(isset($job['company_name']) ? $job['company_name'] : '') .'</td>';
					$html .= '<td>'.(isset($job['company_phone']) ? $job['company_phone'] : '' ) .'</td>';
					$html .= '<td>'.(isset($job['work_start']) && is_object($job['work_start']) ? date('m/d/Y',$job['work_start']->sec): '') .'</td>';
					$html .= '<td>'.(isset($job['work_end']) && is_object($job['work_end']) ? date('m/d/Y',$job['work_end']->sec):'') .'</td>';
					$html .= '<td>'.(isset($job['status']) ? $job['status'] : '') .'</td>';
					$html .= '<td class="center_text bold_text">'.($current_date>(isset($job['work_end']) && is_object($job['work_end']) && $job['status']  == 'New' ? $job['work_end']->sec : $current_date) ? 'X':'') .'</td>';
					$html .= '</tr>';
	                $i++;
				}
				$html .='<tr class="last">
	                        <td colspan="8" class="bold_text right_none">'.$i.' record(s) listed.</td>
	                        </tr>';
	            $arr_data['title'] = array('Job No','Job Name', 'Customer','Phone','Start','Finish','Status','Late');
	            $arr_data['content'] = $html;
	            $arr_data['report_name'] = 'Job Mini  Listing';
	            $arr_data['report_file_name']='Jo_'.md5(time());
	            $arr_data['report_orientation'] = 'landscape';
			}
			Cache::write('jobs_minilist', $arr_data);
		}else {
    		$arr_data = Cache::read('jobs_minilist');
    		Cache::delete('jobs_minilist');
		}
		$this->render_pdf($arr_data);
	}
	function get_sum_jobs(){
		$this->selectModel('Job');
		$this->selectModel('Quotation');
		$this->selectModel('Salesorder');
		$this->selectModel('Salesinvoice');
		$this->selectModel('Product');
		$this->selectModel('Stuffs');
		$this->selectModel('Company');
		//
		$cond = array();
		if( $this->Session->check('jobs_entry_search_cond') ){
			$cond = $this->Session->read('jobs_entry_search_cond');
		}
		$cond = array_merge($cond, $this->arr_search_where());
		$obj_jobs = $this->Job->select_all(array(
			'arr_where' => $cond,
			'arr_field' => array('_id','company_id'),
			'limit' => 9999999
		));
		$orgirin_minimum = Cache::read('minimum');
    	$product = Cache::read('minimum_product');
    	if(!$orgirin_minimum){
    		$product = $this->Stuffs->select_one(array('value'=>"Minimum Order Adjustment"),array('product_id'));
			$p = $this->Product->select_one(array('_id'=> new MongoId($product['product_id'])),array('sell_price'));
			$orgirin_minimum = $p['sell_price'];
			Cache::write('minimum',$orgirin_minimum);
        	Cache::write('minimum_product',$product);
    	}
    	$arr_companies = Cache::read('arr_companies');
	    if(!$arr_companies)
	        $arr_companies = array();
		$product_id = $product['product_id'];
		$Quotation = $Salesorder = $Salesinvoice = 0;
		$arr_models = array(
                'Quotation' => array(
                                     'arr_status' => array('quotation_status'=>array('$ne'=>'Cancelled'))
                                     ),
                'Salesinvoice' => array(
                                     'arr_status' => array('invoice_status'=>array('$ne'=>'Cancelled'))
                                     ),
                'Salesorder' => array(
                                     'arr_status' => array('status'=>array('$ne'=>'Cancelled'))
                                     ),
                );
	    $Quotation = $Salesorder = $Salesinvoice =  0;
		foreach($obj_jobs as $job){
			$invoice_code = $sales_code = '';
	        foreach($arr_models as $model=>$condition){
	        	$arr_where = array('job_id'=> new MongoId($job['_id']));
	        	$arr_where = array_merge($arr_where,$condition['arr_status']);
	        	$query = $this->$model->select_all(array(
			                             'arr_where'=>$arr_where,
			                             'arr_field'=>array('company_id','sum_sub_total','code', 'shipping_cost', 'invoice_status'),
			                             'limit' => 9999999
			                             ));
	        	foreach($query as $value){
	        		$company_id = $value['company_id'];
					$minimum = $orgirin_minimum;
					if(isset($arr_companies[(string)$company_id])){
						$minimum = $arr_companies[(string)$company_id];
					}else if(is_object($company_id)){
						$company = $this->Company->select_one(array('_id'=>$company_id),array('pricing'));
						if(isset($company['pricing'])){
							foreach($company['pricing'] as $pricing){
								if(isset($pricing['deleted'])&&$pricing['deleted']) continue;
								if((string)$pricing['product_id']!=(string)$product_id) continue;
								if(!isset($pricing['price_break']) || empty($pricing['price_break'])) continue;
								$price_break = reset($pricing['price_break']);
								$minimum =  (float)$price_break['unit_price'];
                				$arr_companies[(string)$company_id] = $minimum;
								break;
							}
						}
					}
					if( isset($value['invoice_status']) && $value['invoice_status'] == 'Credit' && $value['sum_sub_total'] > 0 ) {
					    $value['sum_sub_total'] *= -1;
					}
					if($value['sum_sub_total']<$minimum && ( $model != 'Salesinvoice' || $value['invoice_status'] != 'Credit' )) {
						$value['sum_sub_total'] = $minimum;
					}
					$value['sum_sub_total'] = round($value['sum_sub_total'],2);
					$$model += $value['sum_sub_total'];
	        	}
	        }

		}
		echo json_encode(array('quotations_sum'=>$this->Job->format_currency($Quotation),'salesorders_sum'=>$this->Job->format_currency($Salesorder),'salesinvoices_sum'=>$this->Job->format_currency($Salesinvoice)));
		die;
	}
	function get_sum($product_id,$origin_minimum,$arr_jobs,$model,$collection, $arr_where_and = array()){
		$sum = 0;
		$arr_where = array(
							'deleted' => false,
                            'company_id'=>array('$ne'=>''),
         					'job_id'=>array('$in'=>$arr_jobs),
                            );
		$arr_where = array_merge($arr_where,$arr_where_and);
        $arr_companies = $this->$model->collection->group(array('company_id' => true), array('companies' => array()), 'function (obj, prev) { prev.companies.push({_id : obj.company_id}); }',array('condition' => $arr_where));
		foreach($arr_companies as $company){
            $_id = $company['company_id'];
			$minimum = $origin_minimum;
			$company = $this->Company->select_one(array('_id'=>$_id,'pricing.product_id'=>new MongoId($product_id)),array('pricing'));
			if(isset($company['pricing'])){
				foreach($company['pricing'] as $pricing){
					if(isset($pricing['deleted'])&&$pricing['deleted']) continue;
					if((string)$pricing['product_id']!=(string)$product_id) continue;
					if(!isset($pricing['price_break']) || empty($pricing['price_break'])) continue;
					$price_break = reset($pricing['price_break']);
					$minimum =  (float)$price_break['unit_price']; break;
				}
			}
			switch ($model) {
				case 'Quotation':
					$collection = 'tb_quotation';
					break;
				case 'Salesinvoice':
					$collection = 'tb_salesinvoice';
					break;
				case 'Salesorder':
					$collection = 'tb_salesorder';
					break;
				default:
					$collection = 'tb_quotation';
					break;
			}
			$data = $this->db->command(array(
			    'mapreduce'     => $collection,
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
	           							'company_id'=>$_id,
			                            'deleted'    => false,
	           							'job_id'=>array('$in'=>$arr_jobs),
			                        ),
			    'out'           => array('merge' => 'tb_result')
			));
			if( isset($data['ok']) ) {
			    $result = $this->db->selectCollection('tb_result')->findOne();
			    $this->db->tb_sum_result->remove(array('_id' => 'total'));
			    $sum = isset($result['value']) ? $result['value'] : 0;

			}
		}
		return $sum;
	}
	public function detailed_area_report(){
		$this->selectModel('Setting');
		$this->selectModel('Tax');
        $arr_data['jobs_status'] = $this->Setting->select_option_vl(array('setting_value' => 'jobs_status'));
        $arr_data['jobs_type'] = $this->Setting->select_option_vl(array('setting_value' => 'jobs_type'));
		$arr_data['jobs_tax'] = $this->Tax->tax_select_list();

        $this->set('arr_data', $arr_data);
	}
	public function summary_area_report(){
		$this->selectModel('Setting');
		$this->selectModel('Tax');
        $arr_data['jobs_status'] = $this->Setting->select_option_vl(array('setting_value' => 'jobs_status'));
        $arr_data['jobs_type'] = $this->Setting->select_option_vl(array('setting_value' => 'jobs_type'));
		$arr_data['jobs_tax'] = $this->Tax->tax_select_list();

        $this->set('arr_data', $arr_data);
	}
	function area_report($type=''){
		$arr_data = array();
        if(isset($_GET['print_pdf'])){
            $arr_data = Cache::read('jobs_area_report_'.$type);
            Cache::delete('jobs_area_report_'.$type);
        } else {
            if (isset($_POST) && !empty($_POST)) {
                $arr_post = $_POST;
                $heading = $arr_post['heading'];
                $arr_post = $this->Common->strip_search($arr_post);
                $arr_post['heading'] =  $heading;
                $arr_where = array();
                if($arr_post['status'] && $arr_post['status'] != '')
                    $arr_where['status'] = $arr_post['status'];
                if ($arr_post['type'] && $arr_post['type'] != '')
                    $arr_where['type'] = $arr_post['type'];
                if (isset($arr_post['company']) &&$arr_post['company'] != '')
                    $arr_where['company_name'] = new MongoRegex('/' . trim($arr_post['company']) . '/i');
                // if (isset($arr_post['our_rep'])&&$arr_post['our_rep'] != '')
                //     $arr_where['our_rep'] = new MongoRegex('/' . trim($arr_post['our_rep']) . '/i');
                if (isset($arr_post['job_no'])&&$arr_post['job_no'] != '')
                    $arr_where['no'] = (int)trim($arr_post['job_no']);
                //tim chinh xac ngay
                if (isset($arr_post['date_equals'])&&$arr_post['date_equals'] != '') {
                    $arr_where['work_end']['$gte'] = new MongoDate($this->Common->strtotime($arr_post['date_equals'] . '00:00:00'));
                    $arr_where['work_end']['$lt'] = new MongoDate($this->Common->strtotime($arr_post['date_equals'] . '23:59:59'));
                } else { //ngay nam trong khoang
                    //neu chi nhap date from
                    if (isset($arr_post['date_from'])&&$arr_post['date_from']!='') {
                        $arr_where['work_end']['$gte'] = new MongoDate($this->Common->strtotime($arr_post['date_from'] . '00:00:00'));
                    }
                    //neu chi nhap date to
                    if (isset($arr_post['date_to'])&&$arr_post['date_to']!='') {
                        $arr_where['work_end']['$lte']  = new MongoDate($this->Common->strtotime($arr_post['date_to'] . '23:59:59'));
                    }
                }
                $this->selectModel('Job');
                $jobs = $this->Job->select_all(array(
                    'arr_where' => $arr_where,
                    'arr_field' => array('status','no','company_name','work_end','company_id','name','company_id','custom_po_no'),
                    'arr_order'	=> array('work_end'=>1),
                    'limit' => 99999
                ));
                if(isset($arr_post['product_code']) && $arr_post['product_code'] != ''){
                	$arr_post['product_code'] = trim($arr_post['product_code']);
                	if(strpos($arr_post['product_code'], ', ') !== false)
                		$arr_where['product']['code'] = explode(', ', $arr_post['product_code']);
                	else
                		$arr_where['product']['code'] = array($arr_post['product_code']);
                }
                if(isset($arr_post['product_name']) && $arr_post['product_name'] != ''){
                	$arr_post['product_name'] = trim($arr_post['product_name']);
                	if(strpos($arr_post['product_name'], ', ') !== false)
                		$arr_where['product']['products_name'] = explode(', ', $arr_post['product_name']);
                	else
                		$arr_where['product']['products_name'] = array($arr_post['product_name']);
                }
                if ($jobs->count() == 0) {
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
                    if ($arr_post['report_type'] == 'summary'){
                        $arr_data = $this->summary_jobs_area_report($jobs, $arr_post, $arr_where,$minimum,$product['_id']);
                        Cache::write('jobs_area_report_'.$type, $arr_data);
                    }
                    else if ($arr_post['report_type'] == 'detailed'){
                        $arr_data = $this->detailed_jobs_area_report($jobs, $arr_post, $arr_where,$minimum,$product['_id']);
                        Cache::write('jobs_area_report_'.$type, $arr_data['pdf']);
                        $arr_excel = array('data'=>$arr_data['data']);
                        if(isset($arr_post['our_csr']) && !empty($arr_post['our_csr']))
                        	$arr_excel = array_merge($arr_excel, array('our_csr' => $arr_post['our_csr']));
                        Cache::write('jobs_area_excel_'.$type, $arr_excel);
                    	$arr_data = $arr_data['pdf'];
                    } else{
                        $arr_data = $this->summary_jobs_area_report($jobs, $arr_post, $arr_where,$minimum,$product['_id']);
                        Cache::write('jobs_area_report_'.$type, $arr_data);

                    }
                }
            }
        }
        if($this->request->is('ajax'))
            die;
        else
            $this->render_pdf($arr_data);
	}
	function summary_jobs_area_report($obj_jobs, $data, $arr_where,$orgirin_minimum,$product_id){
		$this->selectModel('Company');
		$this->selectModel('Salesinvoice');
		$this->selectModel('Salesorder');
		$this->selectModel('Quotation');


		$this->selectModel('Setting');
		//
		$arr_jobs = array();
		$html = '';
		//
        $arr_models = array(
                'Quotation' => array(
                                     'arr_status' => array('quotation_status'=>array('$ne'=>'Cancelled'))
                                     ),
                'Salesinvoice' => array(
                                     'arr_status' => array('invoice_status'=>array('$ne'=>'Cancelled'))
                                     ),
                'Salesorder' => array(
                                     'arr_status' => array('status'=>array('$ne'=>'Cancelled'))
                                     ),
                );
        $arr_companies = array();
	    $Quotation = $Salesorder = $Salesinvoice = $i =  0;
		foreach($obj_jobs as $job){
	        foreach($arr_models as $model=>$condition){
	        	$arr_where = array('job_id'=> new MongoId($job['_id']));
	        	$arr_where = array_merge($arr_where,$condition['arr_status']);
	        	$query = $this->$model->select_all(array(
			                             'arr_where'=>$arr_where,
			                             'arr_field'=>array('company_id','sum_sub_total', 'shipping_cost', 'invoice_status'),
			                             'limit' => 99999
			                             ));
	        	foreach($query as $value){
	        		$company_id = $value['company_id'];
	        		$minimum = $orgirin_minimum;
	        		if(is_object($company_id)){
	        			if(isset($arr_companies[(string)$company_id])) {
	        				$minimum = $arr_companies[(string)$company_id];
	        			} else {
	        				$company = $this->Company->select_one(array('_id'=>$company_id),array('pricing'));
	        				if(isset($company['pricing'])){
	        					foreach($company['pricing'] as $pricing){
	        						if(isset($pricing['deleted'])&&$pricing['deleted']) continue;
	        						if((string)$pricing['product_id']!=(string)$product_id) continue;
	        						if(!isset($pricing['price_break']) || empty($pricing['price_break'])) continue;
	        						$price_break = reset($pricing['price_break']);
	        						$minimum =  (float)$price_break['unit_price'];
	        						$arr_companies[(string)$company_id] = $minimum;
	        						break;
	        					}
	        				}
	        			}
	        		}
	        		if( isset($value['invoice_status']) && $value['invoice_status'] == 'Credit' && $value['sum_sub_total'] > 0 ) {
	        		    $value['sum_sub_total'] *= -1;
	        		}
	        		if($value['sum_sub_total']<$minimum && ( $model != 'Salesinvoice' || $value['invoice_status'] != 'Credit' )) {
	        			$value['sum_sub_total'] = $minimum;
	        		}
					$$model += $value['sum_sub_total'];
	        	}
	        }
	        $i++;
		}
		$html .= '
                <tr style="background-color:#eeeeee";">
                	 <td class="center_text"> '.$i.' job(s) </td>
                     <td class="right_text">' . $this->Job->format_currency($Quotation) . '</td>
                     <td class="right_text">' . $this->Job->format_currency($Salesorder) . '</td>
                     <td class="right_text">' . $this->Job->format_currency($Salesinvoice) . '</td>
                     <td class="center_text">' . (isset($data['our_rep'])&&$data['our_rep']!='' ? $data['our_rep'] : 'All') . '</td>
                </tr>
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
        $arr_pdf['content'] = $html;
        $arr_pdf['title'] = array('Total Jobs','QT total'=>'text-align: right;width: 20%;','SO Total'=>'text-align: right;width: 20%;','INV Total'=>'text-align: right;width: 20%;','Sale Rep Name');
        $arr_pdf['report_name'] = 'Job Report By Area (Summary)';
        $arr_pdf['report_file_name'] = 'JOB_'.md5(time());
        return $arr_pdf;
	}
	function detailed_jobs_area_report($obj_jobs, $data, $arr_where,$orgirin_minimum,$product_id){
		$this->selectModel('Company');
		$this->selectModel('Contact');
		$this->selectModel('Salesinvoice');
		$this->selectModel('Salesorder');
		$this->selectModel('Quotation');


		$this->selectModel('Setting');
		//
		$arr_commission = array();
		$arr_jobs = array();
		$html = '';
		//
        $arr_models = array(
                'Quotation' => array(
                                     'arr_status' => array('quotation_status'=>array('$ne'=>'Cancelled'))
                                     ),
                'Salesinvoice' => array(
                                     'arr_status' => array('invoice_status'=>array('$ne'=>'Cancelled'))
                                     ),
                'Salesorder' => array(
                                     'arr_status' => array('status'=>array('$ne'=>'Cancelled'))
                                     ),
                );
        $arr_companies = $arr_commission =  $arr_product_where =  $arr_where_product = array();
        if(isset($arr_where['product'])){
        	$arr_product_where = $arr_where['product'];
        	$arr_where_product['products'] = array(
        				'$elemMatch' => array('deleted' => false),
        		);
        	if( isset($arr_product_where['code']) ) {
        		foreach($arr_product_where['code'] as $code) {
	        		$arr_where_product['products']['$elemMatch']['code']['$in'][] = (int)$code;
	        	}
        	}
        	if( isset($arr_product_where['products_name']) ) {
        		foreach($arr_product_where['products_name'] as $name) {
        			$name = str_replace(['(', ')'], '.*', $name);
        			$arr_where_product['products']['$elemMatch']['code']['$in'][] = new MongoRegex('/'. $name .'/i');
        		}
        	}
        	unset($arr_where['product']);
        }


        $date_from = $date_to = '';
        if( isset($data['order_date_from']) && !empty($data['order_date_from']) ) {
        	$date_from = $this->Common->strtotime($data['order_date_from'] . '00:00:00');
        }
        if( isset($data['order_date_to']) && !empty($data['order_date_to']) ) {
        	$date_to = $this->Common->strtotime($data['order_date_to'] . '23:59:59');
        }
        $arr_field = array('company_id','sum_sub_total','code','our_rep','our_rep_id','salesorder_date', 'shipping_cost', 'invoice_status');
        if ( isset($data['our_csr_id']) && !empty($data['our_csr_id']) ){
    		$arr_field[] = 'our_csr';
    		$arr_field[] = 'our_csr_id';
		}
    	if(!empty($arr_product_where)) {
    		$arr_field[] = 'products';
    	}
		foreach($obj_jobs as $job){
	        $Quotation = $Salesorder = $Salesinvoice = $count = 0;
			$invoice_code = $sales_code = '';
			$arr_our_rep = $arr_our_csr = array();
        	$i = 0;
	        foreach($arr_models as $model=>$condition){
	        	$arr_where = array('job_id'=> new MongoId($job['_id']));
	        	$arr_where = array_merge($arr_where, $condition['arr_status'], $arr_where_product);
        		if ( isset($data['our_csr_id']) && !empty($data['our_csr_id']) ){
            		$arr_where['our_csr_id'] = new MongoId($data['our_csr_id']);
        		}
        		if ( isset($data['our_rep_id']) && !empty($data['our_rep_id']) ){
            		$arr_where['our_rep_id'] = new MongoId($data['our_rep_id']);
        		}
	        	$query = $this->$model->select_all(array(
			                             'arr_where'=>	$arr_where,
			                             'arr_field'=>	$arr_field,
			                             'limit' => 99999
			                             ));

	        	foreach($query as $value){
	        		if(empty($arr_product_where)){
		        		$company_id = $value['company_id'];
		        		$minimum = $orgirin_minimum;
		        		if(isset($arr_companies[(string)$company_id]))
		        			$minimum = $arr_companies[(string)$company_id];
		        		else if(is_object($company_id)){
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
							$arr_companies[(string)$company_id] = $minimum;
		        		}
		        		if( isset($value['invoice_status']) && $value['invoice_status'] == 'Credit' && $value['sum_sub_total'] > 0 ) {
		        		    $value['sum_sub_total'] *= -1;
		        		}
		        		if($value['sum_sub_total']<$minimum && ( $model != 'Salesinvoice' || $value['invoice_status'] != 'Credit' )) {
		        			$value['sum_sub_total'] = $minimum;
		        		}
	        		} else {
	        			if(!isset($value['products']))
	        				$value['products'] = array();
	        			$value['sum_sub_total'] = $this->find_product_total($value['products'],$arr_product_where);
	        			/*if($value['sum_sub_total'] == 0)
	        				continue;*/
	        		}
        			if($model=='Salesinvoice'){
						$arr_our_rep['salesinvoice_code'] = $value['code'];
						$arr_our_rep['our_rep'] = $value['our_rep'];
						$arr_our_rep['our_rep_id'] = $value['our_rep_id'];
						$arr_our_rep['commission'] = 0;
						if(isset($arr_commission[(string)$value['our_rep_id']]))
							$arr_our_rep['commission'] = $arr_commission[(string)$value['our_rep_id']];
						else if(is_object($value['our_rep_id'])){
							$our_rep = $this->Contact->select_one(array('_id'=>$value['our_rep_id']),array('commission'));
							if(isset($our_rep['commission'])){
								$arr_commission[(string)$value['our_rep_id']] = (float)$our_rep['commission'];
								$arr_our_rep['commission'] = (float)$our_rep['commission'];
							}

						}
						if( !isset($arr_our_rep['commission_price']) ){
							$arr_our_rep['commission_price'] = 0;
						}
						$arr_our_rep['commission_price'] += $arr_our_rep['commission'] * $value['sum_sub_total'] / 100;
						if( isset($data['our_csr']) && !empty($data['our_csr']) ){
							$arr_our_csr['commission'] = 0;
							if(isset($arr_commission[(string)$value['our_csr_id']]))
								$arr_our_csr['commission'] = $arr_commission[(string)$value['our_csr_id']];
							else if(is_object($value['our_csr_id'])){
								$our_csr = $this->Contact->select_one(array('_id'=>$value['our_csr_id']),array('commission'));
								if(isset($our_csr['commission'])){
									$arr_commission[(string)$value['our_csr_id']] = (float)$our_csr['commission'];
									$arr_our_csr['commission'] = (float)$our_csr['commission'];
								}

							}
							if( !isset($arr_our_csr['commission_price']) ){
								$arr_our_csr['commission_price'] = 0;
							}
							$arr_our_csr['commission_price'] += $arr_our_csr['commission'] * $value['sum_sub_total'] / 100;
						}
						$invoice_code .= $value['code'].' , ';
					} else if($model=='Salesorder'){
						if( ( !empty($date_from) && $value['salesorder_date']->sec < $date_from )
							 || (!empty($date_to) && $value['salesorder_date']->sec > $date_to) ) {
							continue 2;
						}
						$sales_code .= $value['code'].' , ';
					}
					$$model += $value['sum_sub_total'];
	        	}
	        }
			$invoice_code = rtrim($invoice_code,', ');
			$sales_code = rtrim($sales_code,', ');
			$commission = 0;
			/*if((isset($arr_where['our_rep']) || isset($arr_where['our_csr']) || !empty($arr_product_where)) && $Quotation == 0 && $Salesorder == 0 && $Salesinvoice == 0)
				continue;*/
			if( empty($invoice_code) && empty($sales_code) ) continue;
			$extra_array = array(
								'sum_quotation'		=>$Quotation,
								'sum_salesinvoice'	=>$Salesinvoice,
								'sum_salesorder'	=>$Salesorder,
								'invoice_code'		=>$invoice_code,
								'sales_code'		=>$sales_code,
								'po_number'			=>(isset($job['custom_po_no']) ? $job['custom_po_no'] : ''),
								'arr_our_rep'		=>$arr_our_rep
								);
			if( isset($data['our_csr']) && !empty($data['our_csr']) ){
				unset($extra_array['arr_our_rep']);
				$extra_array = array_merge($extra_array, array('arr_our_csr'=>$arr_our_csr));
			}
			$arr_jobs[] = array_merge(
									$job,
									$extra_array
									);

		}
		$sum_qt = $sum_sales = $sum_inv = $sum_commission = $sum_csr_commission = $i = 0;
		foreach ($arr_jobs as $job) {
            $html .= '
                <tr style="background-color:' . ( $i%2==0 ? '#eeeeee' : '#fdfcfa'). '; font-size: 12px;">
                     <td>' . $job['no'] . '</td>
                     <td>' . $job['company_name'] . '</td>
                     <td>' . $job['name'] . '</td>
                     <td>' . $job['po_number'] . '</td>
                     <td class="center_text">' .(is_object($job['work_end']) ? $this->Job->format_date($job['work_end']->sec) : $job['work_end']) . '</td>
                     <td class="center_text">' . $job['status'] . '</td>
                     <td class="right_text">' . $this->Job->format_currency($job['sum_quotation']) . '</td>
                     <td>' . $job['sales_code'] . '</td>
                     <td class="right_text">' . $this->Job->format_currency($job['sum_salesorder']) . '</td>
                     <td>' . $job['invoice_code'] . '</td>
                     <td class="right_text">' . $this->Job->format_currency($job['sum_salesinvoice']) . '</td>';
		    if( isset($job['arr_our_csr']) ){
		    	$html .= '<td class="right_text">' .( isset($job['arr_our_csr']['commission']) ? ($job['arr_our_csr']['commission'] ? $job['arr_our_csr']['commission'].'%' : '') : ''). '</td>
		             <td class="right_text">' .( isset($job['arr_our_csr']['commission_price'])  ? ($job['arr_our_csr']['commission_price'] ? $this->Job->format_currency($job['arr_our_csr']['commission_price']) : '') : ''). '</td>';
		        $sum_csr_commission += isset($job['arr_our_csr']['commission_price']) ? $job['arr_our_csr']['commission_price'] : 0;
		    } else {
		    	$html .= '<td>'.(isset($job['arr_our_rep']['our_rep']) ? $job['arr_our_rep']['our_rep'] : '').'</td>
			              <td class="right_text">' .( isset($job['arr_our_rep']['commission']) ? ($job['arr_our_rep']['commission'] ? $job['arr_our_rep']['commission'].'%' : '') : ''). '</td>
			              <td class="right_text">' .( isset($job['arr_our_rep']['commission_price'])  ? ($job['arr_our_rep']['commission_price'] ? $this->Job->format_currency($job['arr_our_rep']['commission_price']) : '') : ''). '</td>';
           		$sum_commission += isset($job['arr_our_rep']['commission_price']) ? $job['arr_our_rep']['commission_price'] : 0;
		    }
            $html .= '</tr>';
            $sum_qt += $job['sum_quotation'];
            $sum_sales += $job['sum_salesorder'];
            $sum_inv += $job['sum_salesinvoice'];
            $i++;
        }
        $html .= '
                    <tr style="background-color:' . ( $i%2==0 ? '#eeeeee' : '#fdfcfa') . ';">
                         <td colspan="2" class="bold_text right_none">' . $i . ' record(s) listed</td>
                         <td colspan="3" class="bold_text right_none right_text" >Totals</td>
                         <td></td>
                         <td class="bold_text right_text">' . $this->Job->format_currency($sum_qt) . '</td>
                         <td></td>
                         <td class="bold_text right_text">' . $this->Job->format_currency($sum_sales) . '</td>
                         <td></td>
                         <td class="bold_text right_text">' . $this->Job->format_currency($sum_inv) . '</td>';
        if( isset($data['our_csr']) && !empty($data['our_csr']) ){
        	$html .= 	 '<td></td>
        				  <td class="bold_text right_text">' . $this->Job->format_currency($sum_csr_commission) . '</td>';
        } else {
        	$html .= 	'<td></td>
                         <td></td>
                         <td class="bold_text right_text">' . $this->Job->format_currency($sum_commission) . '</td>';
        }
        $html .= '  </tr>
                </table>
                ';
		//========================================
        //set header
        if($data['heading']!='')
            $arr_pdf['report_heading'] = $data['heading'];
        if(!empty($arr_product_where)){
        	$heading = '';
        	foreach($arr_product_where['code'] as $key =>  $code){
        		$heading .= $code . (isset($arr_product_where['products_name'][$key]) ?  ' - '.$arr_product_where['products_name'][$key] : '').'<br /><br />';
        	}
        	if(!isset($arr_pdf['report_heading']))
        		$arr_pdf['report_heading'] = $heading;
        	else
        		$arr_pdf['report_heading'] .= '<br /><br />'.$heading;
        }
        $arr_pdf['date_from_to'] = '';
        if(isset($data['date_from'])&&$data['date_from']!='')
            $arr_pdf['date_from_to'] .= '<span class="color_red bold_text">From</span> '.$data['date_from'].' ';
        if(isset($data['date_to'])&&$data['date_to']!='')
            $arr_pdf['date_from_to'] .= ' <span class="color_red bold_text">To</span> '.$data['date_to'];
        if(isset($data['date_equals'])&&$data['date_equals']!='')
            $arr_pdf['date_from_to'] .= $data['date_equals'];
        $arr_pdf['content'] = $html;
        $arr_pdf['title'] = array('No'=>'text-align: left','Company'=>'text-align: left;width: 15%','Job name'=>'text-align: left; width: 13%','PO#','Date'=>'text-align: center;width: 7%','Status'=>'text-align: center','QT total'=>'text-align: right;width: 8%;','SO #'=>'min-width: 72px;','SO Total'=>'text-align: right;width: 8%;','INV #','INV Total'=>'text-align: right;width: 8%;',);
        if(isset($data['our_csr']) && !empty($data['our_csr']) ){
        	$arr_pdf['left_info'] = array(
                                       'name'=>'<span class="bold_text">Our CSR</span>:     '.$data['our_csr'].'<br />',
                                       'address' => '&nbsp;',
                                       );
        	$arr_pdf['title'] = array_merge($arr_pdf['title'], array('CSR%','CSR Comm.'));
        } else {
        	$arr_pdf['title'] = array_merge($arr_pdf['title'], array('Saleman','%','Commission'));
        }
        $arr_pdf['font-size'] = 12;
        $arr_pdf['report_name'] = 'Job Report By Area (Detailed)';
        $arr_pdf['report_orientation'] = 'landscape';
        $arr_pdf['excel_url'] = URL.'/jobs/jobs_area_excel/detail';
        $arr_pdf['report_file_name'] = 'JOB_'.md5(time());
        $arr_data['pdf'] = $arr_pdf;
        $arr_data['data'] = $arr_jobs;
        return $arr_data;
	}

	function find_product_total($products, $arr_where){
		$code = isset($arr_where['code']) ? $arr_where['code'] : array();
		// $products_name = isset($arr_where['products_name']) ? $arr_where['products_name'] : '';
		$sub_total = 0;
		if(empty($products))
			return 0;
		foreach($products as $product){
			if(isset($product['deleted']) && $product['deleted']) continue;
			if(!isset($product['code'])) continue;
			if( !empty($code) ){
				if(isset($product['option_for']) && isset($products[$product['option_for']]['code'])
				   	&& in_array($products[$product['option_for']]['code'],$code)){
					if(isset($product['same_parent']) && $product['same_parent'] == 1) continue;
					$sub_total += (float)$product['sub_total']; continue;
				}
				else if(!in_array($product['code'],$code)) continue;
			}
			/*if($products_name != '' && $product['products_name'] != $products_name){
				if(isset($product['option_for']) && isset($products[$product['option_for']])
				   	&&$products[$product['option_for']]['products_name'] == $products_name){
					$sub_total += (float)$product['sub_total']; continue;
				}
				else if($product['products_name'] != $products_name) continue;
			}*/
			if(isset($product['same_parent']) && $product['same_parent'] == 1) continue;
			$sub_total += (float)$product['sub_total'];
		}
		return $sub_total;
	}

	function jobs_area_excel($type){
		$this->selectModel('Job');
		$arr_data = Cache::read('jobs_area_excel_'.$type);
		Cache::delete('jobs_area_excel_'.$type);
		if(!$arr_data){
			echo 'No data';die;
		}
		App::import('Vendor', 'phpexcel/PHPExcel');
		$objPHPExcel = new PHPExcel();

		// Set document properties
		$objPHPExcel->getProperties()->setCreator("")
									 ->setLastModifiedBy("")
									 ->setTitle("Job Report By Area")
									 ->setSubject("Job Report By Area")
									 ->setDescription("Job Report By Area")
									 ->setKeywords("Job Report By Area")
									 ->setCategory("Job Report");
        $worksheet = $objPHPExcel->getActiveSheet();
		$worksheet->setCellValue('A1',"No")
										->setCellValue('B1',"Company")
										->setCellValue('C1',"Job name")
										->setCellValue('D1',"PO#")
										->setCellValue('E1',"Date")
										->setCellValue('F1',"Status")
										->setCellValue('G1',"QT total")
										->setCellValue('H1',"SO #")
										->setCellValue('I1',"SO Total")
										->setCellValue('J1',"INV #")
										->setCellValue('K1',"INV Total");
		if(isset($arr_data['our_csr'])){
			$worksheet->setCellValue('L1',"CSR%")
											->setCellValue('M1',"CSR Comm.")
											->setCellValue('N1',"Our CSR: {$arr_data['our_csr']}");
			$worksheet->mergeCells("N1:P1");
			$worksheet->getStyle('P1')->applyFromArray(
																array(
															        'font'  => array(
																        'bold'  => true,
																        'color' => array('rgb' => 'FF0000'),
																    ),
																    'alignment' => array(
																        'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
															    	)
															    )
			                                                );
			// $worksheet->freezePane('Q2');
		} else {
			$worksheet->setCellValue('L1',"Salesman")
										->setCellValue('M1',"%")
										->setCellValue('N1',"Commission");
			// $worksheet->freezePane('O2');
		}
		$i = 2;
		$sum_qt = $sum_sales = $sum_inv  = 0;
		// usort($arr_data['data'], function($a , $b){
		// 	return $a['work_end']->sec > $b['work_end']->sec ;
		// });
		foreach($arr_data['data'] as $job){
			$sum_qt += $job['sum_quotation'];
            $sum_sales += $job['sum_salesorder'];
            $sum_inv += $job['sum_salesinvoice'];
            $commission_percent = $commission = 0;
            $saleman = '';
            if(isset($job['arr_our_rep']['our_rep']))
            	$saleman = $job['arr_our_rep']['our_rep'];
            if(isset($job['arr_our_rep']['commission']))
            	$commission_percent = $job['arr_our_rep']['commission'] / 100;
            if(isset($job['arr_our_rep']['commission_price']))
            	$commission = $job['arr_our_rep']['commission_price'];
			$worksheet->setCellValue('A'.$i,$job['no'])
						->setCellValue('B'.$i,$job['company_name'])
						->setCellValue('C'.$i,$job['name'])
						->setCellValue('D'.$i,$job['po_number'])
						->setCellValue('E'.$i,$this->Job->format_date($job['work_end']->sec))
						->setCellValue('F'.$i,$job['status'])
						->setCellValue('G'.$i,$job['sum_quotation'])
						->setCellValue('H'.$i,$job['sales_code'])
						->setCellValue('I'.$i,$job['sum_salesorder'])
						->setCellValue('J'.$i,$job['invoice_code'])
						->setCellValue('K'.$i,$job['sum_salesinvoice']);

			if(isset($arr_data['our_csr'])){
				$csr_commission_percent = $csr_commission = 0;
				 if(isset($job['arr_our_csr']['commission']))
	            	$csr_commission_percent = $job['arr_our_csr']['commission'] / 100;
	            if(isset($job['arr_our_csr']['commission_price']))
	            	$csr_commission = $job['arr_our_csr']['commission_price'];
				$worksheet->setCellValue('L'.$i,$csr_commission_percent)
												->setCellValue('M'.$i,$csr_commission);
				$worksheet->getStyle('L'.$i)->getNumberFormat()->setFormatCode("#,0%");
				$worksheet->getStyle('M'.$i)->getNumberFormat()->setFormatCode("#,##0.00");
			} else {
				$worksheet->setCellValue('L'.$i,$saleman)
						->setCellValue('M'.$i,$commission_percent)
						->setCellValue('N'.$i,$commission);
				$worksheet->getStyle('M'.$i)->getNumberFormat()->setFormatCode("#,0%");
				$worksheet->getStyle('N'.$i)->getNumberFormat()->setFormatCode("#,##0.00");
			}
			$worksheet->getStyle('G'.$i)->getNumberFormat()->setFormatCode("#,##0.00");
			$worksheet->getStyle('I'.$i)->getNumberFormat()->setFormatCode("#,##0.00");
			$worksheet->getStyle('K'.$i)->getNumberFormat()->setFormatCode("#,##0.00");
			$i++;
		}
		$worksheet->mergeCells("A$i:E$i")
						->setCellValue('A'.$i,($i-2).' record(s) listed')
						->setCellValue('G'.$i,"=SUM(G2:G".($i-1).")")
						->setCellValue('I'.$i,"=SUM(I2:I".($i-1).")")
						->setCellValue('K'.$i,"=SUM(K2:K".($i-1).")");
		$worksheet->getStyle('G'.$i)->getNumberFormat()->setFormatCode("#,##0.00");
		$worksheet->getStyle('I'.$i)->getNumberFormat()->setFormatCode("#,##0.00");
		$worksheet->getStyle('K'.$i)->getNumberFormat()->setFormatCode("#,##0.00");
		if(isset($arr_data['our_csr'])){
			$worksheet->setCellValue('M'.$i,"=SUM(M2:M".($i-1).")");
			$worksheet->getStyle('M'.$i)->getNumberFormat()->setFormatCode("#,##0.00");
		} else {
			$worksheet->setCellValue('N'.$i,"=SUM(N2:N".($i-1).")")
											->getStyle('N'.$i)->getNumberFormat()->setFormatCode("#,##0.00");
		}
		$worksheet->getRowDimension(8)->setRowHeight(-1);
        $worksheet->getStyle('A1:N1')->getFont()->setBold(true);
        $worksheet->getStyle('A1:A'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
        $worksheet->getStyle('E1:E'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        $worksheet->getStyle('M1:M'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
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
		$worksheet->getStyle('A1:N'.($i))->applyFromArray($styleArray);
        for($i = 'A'; $i !== 'O'; $i++){
        	$worksheet->getColumnDimension($i)
				        	->setAutoSize(true);
        }
        $worksheet->getStyle('A'.$i.':N'.$i)->getFont()->setBold(true);

		$objPHPExcel->setActiveSheetIndex(0);
		$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
		$objWriter->save(APP.DS.'webroot'.DS.'upload'.DS.'Job_Report_By_Area.xlsx');
		Cache::delete('jobs_area_excel_'.$type);
		$this->redirect('/upload/Job_Report_By_Area.xlsx');
		die;
	}
	function rebuild_our_rep(){
		$this->selectModel('Company');
		$this->selectModel('Job');
		$arr_company = $this->Job->collection->distinct('company_id');
		foreach($arr_company as $company_id){
			$company = $this->Company->select_one(array('_id'=>$company_id),array('our_rep','our_rep_id'));
			$arr_jobs = $this->Job->collection->update(
				                                           array('company_id'=>$company_id),
				                                           array('$set'=>array(
				                                                 'our_rep'=>(isset($company['our_rep']) ? $company['our_rep'] : ''),
				                                                 'our_rep_id'=>(isset($company['our_rep_id'])&&is_object($company['our_rep_id']) ? $company['our_rep_id'] : '')
				                                                 )
				                                           ),
				                                           array('multiple'=>true)
			                                           );
		}
		echo 'Xong';
		die;
	}

	function combine_salesinvoices(){
		$salesinvoice_id = '';
		if(isset($_POST['ids']) && strlen($_POST['ids']) == 24)
			$salesinvoice_id = $_POST['ids'];
		$id = $this->get_id();
		$this->selectModel('Salesorder');
		$this->selectModel('Salesinvoice');
		$this->selectModel('Job');
		$this->selectModel('Task');
		$arr_salesorders = $this->Salesorder->select_all(array(
		                              'arr_where' => array(
		                                                   'job_id' => new MongoId($id)
		                                                   ),
		                              'arr_order' => array('_id'=>1)
		                              ));
		$so_ids = array();
		$count = $arr_salesorders->count();
		if($count){
			$arr_save = array();
			$arr_job = $this->Job->select_one(array('_id'=>new MongoId($id)));
			$arr_save = $this->get_default_salesinvoice($arr_job,$salesinvoice_id);
			$arr_save['options'] = $arr_save['products'] = array();
			foreach($arr_salesorders as $salesorder){
				if(!isset($products_num))
					$products_num = count($salesorder['products']);
				if(!isset($options_num))
					$options_num = count($salesorder['options']);
				if(empty($arr_save['options']) && empty($arr_save['products'])){
					$arr_save['options'] = (isset($salesorder['options']) ? $salesorder['options'] : array() );
					$arr_save['products'] = (isset($salesorder['products']) ? $salesorder['products'] : array() );
				} else {
					$arr_data = $this->build_salesorder($salesorder,$products_num,$options_num);
					$arr_save['options'] = array_merge($arr_save['options'],$arr_data['options']);
					$arr_save['products'] = array_merge($arr_save['products'],$arr_data['products']);
				}
				if(!isset($arr_save['salesorder_id'])){
					$arr_save['salesorder_id'] = $salesorder['_id'];
			        $arr_save['salesorder_number'] = $salesorder['code'];
			        $arr_save['salesorder_name'] = $salesorder['name'];
				}
				$arr_save['salesorders'][] = $salesorder['_id'];
			}
			$arr_sum = $this->new_cal_sum($arr_save['products']);
			$arr_save = array_merge($arr_save,$arr_sum);
			if(!$arr_save['code'])
				$arr_save['code'] = $this->Salesinvoice->get_auto_code('code');
			$arr_save['job_id'] = new MongoId($id);
			$arr_save['job_name'] = $arr_job['name'];
			$arr_save['job_number'] = $arr_job['no'];
			$this->Salesinvoice->save($arr_save);
			if($salesinvoice_id){
				$arr_salesinvoices = $this->Salesinvoice->select_all(array(
				                                'arr_where' => array(
				                                                     'job_id' => new MongoId($id),
				                                                     '_id' => array('$ne' => new MongoId($salesinvoice_id))
				                                                     ),
				                                'arr_field' => array('_id')
				                                ));
				foreach($arr_salesinvoices as $invoice){
					$invoice['deleted'] = true;
					$this->Salesinvoice->save($invoice);
				}
			}
			$arr_return = array('status'=>'ok','url'=>URL.'/salesinvoices/entry/'.$this->Salesinvoice->mongo_id_after_save);

		}else
			$arr_return = array('status'=>'error','message'=>'There is no sales order belongs to this job.');
		echo json_encode($arr_return);
		die;
	}

	function get_default_salesinvoice($arr_job,$salesinvoice_id){
		$arr_invoice = array();
		if($salesinvoice_id){
			$this->selectModel('Salesinvoice');
			$arr_invoice = $this->Salesinvoice->select_one(array('_id'=> new MongoId($salesinvoice_id)));
		}
		if(empty($arr_invoice) && isset($arr_job['company_id']) && is_object($arr_job['company_id'])){
			$this->selectModel('Company');
			$this->selectModel('Salesaccount');
			$this->selectModel('Contact');
			$company = $this->Company->select_one(array('_id'=> $arr_job['company_id']));
			$salesaccount = $this->Salesaccount->select_one(array('company_id'=>$arr_job['company_id']),array('payment_terms'));
			$arr_invoice['company_id'] = $arr_job['company_id'];
			$arr_invoice['company_name'] = (isset($company['name']) ? $company['name'] : '');
			$arr_invoice['our_rep'] = (isset($company['our_rep']) ? $company['our_rep'] : '');
			$arr_invoice['our_rep_id'] = (isset($company['our_rep_id']) ? $company['our_rep_id'] : '');
			$arr_invoice['our_csr'] = (isset($company['our_csr']) ? $company['our_csr'] : '');
			$arr_invoice['our_csr_id'] = (isset($company['our_csr_id']) ? $company['our_csr_id'] : '');
			if($arr_invoice['our_csr'] == '' && $arr_invoice['our_csr_id'] == ''){
				$arr_invoice['our_csr'] = $this->Company->user_name();
				$arr_invoice['our_csr_id']  = $this->Company->user_id();
			}
			$arr_invoice['phone'] = (isset($company['phone']) ? $company['phone'] : '');
			$arr_invoice['email'] = (isset($company['email']) ? $company['email'] : '');
			$arr_invoice['payment_terms'] = (isset($salesaccount['payment_terms']) ? $salesaccount['payment_terms'] : 0);
			$arr_invoice['invoice_date'] = new MongoDate();
			$arr_invoice['payment_due_date'] = new MongoDate($arr_invoice['invoice_date']->sec + $arr_invoice['payment_terms']*DAY);
			if(isset($company['contact_default_id']) && is_object($company['contact_default_id'])){
				$contact = $this->Contact->select_one(array('_id'=>$company['contact_default_id']),array('first_name','last_name','email'));
			} else {
				$contact = $this->Contact->select_one(array('company_id'=>$arr_job['company_id']),array('first_name','last_name','email'));
			}
			$arr_invoice['contact_name'] = (isset($contact['first_name']) ? $contact['first_name'] : '').' '.(isset($contact['last_name']) ? $contact['last_name'] : '');
			$arr_invoice['contact_id'] = (isset($contact['_id']) ? $contact['_id'] : '');
			if(isset($contact['email']))
				$arr_invoice['email'] = $contact['email'];
			$arr_invoice['invoice_type'] = 'Invoice';
			$arr_invoice['invoice_status'] = 'In Progress';
			foreach(array('invoice','shipping') as $address){
				$arr_invoice[$address.'_address'] = array(array(
				                                       'deleted'=>false,
				                                       	$address.'_name' => '',
				                                       	$address.'_country' => 'Canada',
				                                       	$address.'_country_id' => 'CA',
				                                       	$address.'_province_state' => '',
				                                       	$address.'_province_state_id' => '',
				                                       	$address.'_address_1' => '',
				                                       	$address.'_address_2' => '',
				                                       	$address.'_address_3' => '',
				                                       	$address.'_town_city' => '',
				                                       	$address.'_zip_postcode' => '',
				                                       ));
			}
			$addresses_default_key = (isset($company['addresses_default_key']) ? $company['addresses_default_key'] : 0);
			if(isset($company['addresses'][$addresses_default_key])){
				foreach($company['addresses'][$addresses_default_key] as $address_key => $address){
					if($address_key == 'deleted') continue;
					$arr_invoice['invoice_address'][0]['invoice_'.$address_key] = $address;
				}
			}
			$this->selectModel('Tax');
			$key_tax = '';
			if(isset($arr_invoice['invoice_address'][0]['invoice_province_state_id'])&&$arr_invoice['invoice_address'][0]['invoice_province_state_id']!='')
				$key_tax = $arr_invoice['invoice_address'][0]['invoice_province_state_id'];
			else if(isset($arr_invoice['shipping_address'][0]['shipping_province_state_id'])&&$arr_invoice['shipping_address'][0]['shipping_province_state_id']!='')
				$key_tax = $arr_invoice['shipping_address'][0]['shipping_province_state_id'];
			$arr_tax = $this->Tax->tax_select_list();
			if(isset($arr_tax[$key_tax])){
				$tax = explode("%",$arr_tax[$key_tax]);
				$arr_invoice['tax'] = $key_tax;
				$arr_invoice['taxval'] = (float)$tax[0];
			}
			$arr_invoice['heading'] = '';
		}
		return $arr_invoice;
	}

	function rebuild_companies(){
		$this->selectModel('Job');
		$this->selectModel('Company');
		$arr_jobs = $this->Job->collection->distinct('company_name',array(
		                                             'status'=>array('$ne' => 'Cancelled'),
		                                             'company_name' => array('$nin' => array('',null)),
		                                             '$or' => array(
		                                                            array(
		                                                                  'company_id' => array('$in'=>array('',null))
		                                                                  ),
		                                                            array(
		                                                                  'company_id' => array('$exists' => false)
		                                                                  )
		                                                            )
		                                             ));
		$i = 0;
		echo count($arr_jobs).' records found from CompanyName<br />';
		foreach ($arr_jobs as $company_name) {
			$name = str_replace(array('(',')'), '.*', $company_name);
			if($name == 'Carbon Copy Digital')
				$name = 'Carboncopy Digital';
			$company = $this->Company->collection->findOne(array(
			                           	'name' => new MongoRegex('/'.$name.'/i'),
			                           	'our_rep_id' => array('$nin' => array('', null)),
			                           	'our_csr_id' => array('$nin' => array('', null)),
			                           ),array('_id'));
			$this->Job->collection->update(
			                               array('company_name' => $company_name),
			                               array('$set' => array('company_id' => $company['_id'])),
			                               array('multiple' => true)
			                               );
			$i++;
		}
		echo $i.' records rebuild<br />';
		$arr_jobs = $this->Job->collection->distinct('company_id',array(
		                                             		'status'=>array('$ne' => 'Cancelled'),
		                                             		'company_id' => array(
		                                             		                      '$nin'=>array('',null),
		                                             		                      '$exists' => true
		                                             		                    )
		                                             		));
		echo count($arr_jobs).' records found from CompanyID<br />';
		$i = 0;
		foreach($arr_jobs as $company_id){
			$company = $this->Company->collection->findOne(array('_id' =>  new MongoId($company_id)),array('name'));
			$this->Job->collection->update(
			                               array('company_id' => $company_id),
			                               array('$set' => array(
			                                     					'company_name' => $company['name'],
			                                     					'company_id' => new MongoId($company_id)),
			                                     				),
			                               array('multiple'=>true)
			                               );
			$i++;
		}
		echo $i.' records rebuild<br />';
		die;
	}

	function closing_months(){
    	$this->selectModel('Closingmonth');
    	$arr_months = $this->Closingmonth->select_all(array(
    	                                'arr_where' => array('module'=>'Jobs'),
    	                                'arr_order' => array('date_from' => 1)
    	                                ));
    	$this->set('arr_months',$arr_months);
    	$this->selectModel('Contact');
    	$this->set('_contact',$this->Contact);
    	$this->render('../Elements/closing_months');
    }
    function closing_months_add(){
    	$this->selectModel('Closingmonth');
    	$this->Closingmonth->arr_save['module'] = 'Jobs';
    	$this->Closingmonth->add();
    	$this->selectModel('Contact');
    	$contact = $this->Contact->select_one(array('_id' => $this->Closingmonth->arr_save['modified_by']),array('full_name'));
    	$arr_return = array(
    	                    '_id' => (string)$this->Closingmonth->mongo_id_after_save,
    	                   	'date_from' => date('m/d/Y',$this->Closingmonth->arr_save['date_from']->sec),
    	                   	'date_to' => date('m/d/Y',$this->Closingmonth->arr_save['date_to']->sec),
    	                   	'description' => '',
    	                   	'inactive' => 0,
    	                   	'created_by' => isset($contact['full_name']) ? $contact['full_name'] : '',
    	                   	'modified_by' => isset($contact['full_name']) ? $contact['full_name'] : '',
    	                   	'date_modified' => date('d M, Y H:i:s',$this->Closingmonth->arr_save['date_modified']->sec),
    	                    );
    	echo json_encode($arr_return);
    	die;
    }
    function closing_months_save(){
    	if(!empty($_POST)){
    		$arr_save['_id'] = new MongoId($_POST['_id']);
			$arr_save['date_from'] = new MongoDate(strtotime(date('Y-m-d',strtotime($_POST['date_from']))));
			$arr_save['date_to'] = new MongoDate(strtotime(date('Y-m-d',strtotime($_POST['date_to']))));
			if($arr_save['date_to'] < $arr_save['date_from']){
				echo '"Date To" must be greater than "Date From".';
				die;
			}
    		$this->selectModel('Closingmonth');
			/*$arr_where = array(
			                   'module' => 'Jobs',
			                   '$or' => array(
			                                array(
			                                    'date_from' => $arr_save['date_from'],
			                                    'date_to' => $arr_save['date_to'],
			                                ),
			                                array(
			                                     'date_from' => array('$gte' => $arr_save['date_from']),
			                                     'date_to' => array('$lte' => $arr_save['date_from']),
			                               	),
			                           )
			                   );
			$result = $this->Closingmonth->select_one($arr_where,array('_id'),array('date_from' => 1));
			if(isset($result['_id'])){
				echo 'Date in range of existed record.';
				die;
			}*/
    		$arr_save['description'] = $_POST['description'];
    		$arr_save['inactive'] = 0;
    		if(isset($_POST['inactive'])){
    			$arr_save['inactive'] = 1;
    		}
    		$this->Closingmonth->save($arr_save);
    		echo 'ok';
    	}
    	die;
    }

    function rebuild_date(){
    	$this->selectModel('Job');
    	$jobs = $this->Job->select_all(array(
    						'arr_where' => array(
    								'$or' => array(
    										array(
    											'work_start' => array(
	    											'$in' => array( new MongoRegex('/2013/'), new MongoRegex('/2014/'))
	    											),
    										),
    										array(
	    										'work_end' => array(
	    											'$in' => array( new MongoRegex('/2013/'), new MongoRegex('/2014/'))
	    											),
    										),
    									)
    							),
    						'arr_field' => array('no','work_start','work_end'),
    						'arr_order' => array('no' => 1)
    		));
    	echo $jobs->count().' records found.<br />';
    	$i = 0;
    	foreach($jobs as $job){
    		$find = false;
    		if(strpos($job['work_start'], ', 2013') !== false
    			|| strpos($job['work_start'], ', 2014') !== false){
    			$find = true;
    			$work_start_sec = $this->Common->strtotime($job['work_start'] . ' 00:00:00');
				$job['work_start'] = new MongoDate($work_start_sec);
    		}
    		if(strpos($job['work_end'], ', 2013') !== false
    			|| strpos($job['work_end'], ', 2014') !== false){
    			$find = true;
    			$work_end_sec = $this->Common->strtotime($job['work_end'] . ' 00:00:00');
				$job['work_end'] = new MongoDate($work_end_sec);
    		}
    		if($find){
    			$this->Job->rebuild_collection($job);
    			$i++;
    		}
    	}
    	echo $i.' jobs are rebuild';
    	die;
    }

    function combine_orders_popup($key = ''){
    	$this->set('key',$key);
    	$id = $this->get_id();
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
        }
        // search theo submit $_POST kèm điều kiện
        $cond['job_id'] = new MongoId($this->get_id());
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
        $arr_salesorders = $this->Salesorder->select_all(array(
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
	    	$arr_orders_id = array();
    		foreach($arr_post['combine_salesorders'] as $id=>$value){
    			if(strlen($id)!=24) continue;
    			$arr_orders_id[] = new MongoId($id);
    		}
    		$this->selectModel('Salesinvoice');
    		$arr_invoices = $this->Salesinvoice->select_all(array(
    		                       'arr_where' => array(
    		                                            'salesorder_id' => array('$in'=>$arr_orders_id)
    		                                            ),
    		                       'arr_order' => array('_id'=>-1),
    		                       'arr_field' => array('options','products')
    		                       ));
    		$last_invoice = array();
    		if($arr_invoices->count() != 0){
    			$i = 0;
	    		foreach($arr_invoices as $value){
	    			if($i == 0){
	    				$last_invoice = $this->Salesinvoice->select_one(array('_id' => $value['_id']));
			    		if(!isset($value['products']))
			    			$value['products'] = array();
			    		if(!isset($value['options']))
			    			$value['options'] = array();
	    				$last_invoice = array_merge($last_invoice,$value);
						$products_num = count($value['products']);
						$options_num = count($value['options']);
						$i++;
						continue;
	    			}
	    			if(!isset($value['products']))
		    			$value['products'] = array();
		    		if(!isset($value['options']))
		    			$value['options'] = array();
	    			$arr_data = $this->build_salesorder($value,$products_num,$options_num);
					$last_invoice['options'] = array_merge($last_invoice['options'],$arr_data['options']);
					$last_invoice['products'] = array_merge($last_invoice['products'],$arr_data['products']);
					$last_invoice['salesinvoices'][] = $value['_id'];
					$this->Salesinvoice->save(array('_id'=>$value['_id'],'deleted'=>true));
	    		}
	    		$arr_sum = $this->new_cal_sum($last_invoice['products']);
	    		$last_invoice = array_merge($last_invoice,$arr_sum);
    		}
    		//=====================================================
    		$this->selectModel('Salesorder');
    		$arr_orders = $this->Salesorder->select_all(array(
    		                       'arr_where' => array(
    		                                            '_id' => array('$in'=>$arr_orders_id)
    		                                            ),
    		                       'arr_order' => array('_id'=>-1),
    		                       'arr_field' => array('options','products')
    		                       ));
			$i = 0;
			$last_order = array();

    		foreach($arr_orders as $value){
    			if($i == 0){
    				$last_order = $this->Salesorder->select_one(array('_id' => $value['_id']));
		    		if(!isset($value['products']))
		    			$value['products'] = array();
		    		if(!isset($value['options']))
		    			$value['options'] = array();
    				$last_order = array_merge($last_order,$value);
					$products_num = count($value['products']);
					$options_num = count($value['options']);
					$i++;
					continue;
    			}
    			if(!isset($value['products']))
	    			$value['products'] = array();
	    		if(!isset($value['options']))
	    			$value['options'] = array();
    			$arr_data = $this->build_salesorder($value,$products_num,$options_num);
				$last_order['options'] = array_merge($last_order['options'],$arr_data['options']);
				$last_order['products'] = array_merge($last_order['products'],$arr_data['products']);
				$last_order['salesorders'][] = $value['_id'];
				$this->Salesorder->save(array('_id'=>$value['_id'],'deleted'=>true));
    		}
    		$arr_sum = $this->new_cal_sum($last_order['products']);
			$last_order = array_merge($last_order,$arr_sum);
			if(!empty($last_invoice)){
				$last_order['salesinvoice_id'] = $last_invoice['_id'];
				$last_order['salesinvoice_code'] = $last_invoice['code'];
				$last_order['salesinvoice_name'] = $last_invoice['name'];
				//=====================================================
				$last_invoice['salesorder_id'] = $last_order['_id'];
				$last_invoice['salesorder_code'] = $last_order['code'];
				$last_invoice['salesorder_name'] = $last_order['name'];
				$this->Salesinvoice->save($last_invoice);
			}
			$this->Salesorder->save($last_order);
			$this->Session->write('SalesordersViewId',(string)$last_order['_id']);
			echo json_encode(['status'=>'ok', 'saleorder_id'=>(string)$last_order['_id']]);
    	} else
    		echo 'You must choose at least one Sales order.';
    	die;
    }

    function combine_invoices_popup($key = ''){
    	$this->set('key',$key);
    	$id = $this->get_id();
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
        }
        // search theo submit $_POST kèm điều kiện
        $cond['job_id'] = new MongoId($id);
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
	    	$arr_invoices_id = array();
    		foreach($arr_post['combine_salesinvoices'] as $id=>$value){
    			if(strlen($id)!=24) continue;
    			$arr_invoices_id[] = new MongoId($id);
    		}
    		$this->selectModel('Salesinvoice');
    		$arr_invoices = $this->Salesinvoice->select_all(array(
    		                       'arr_where' => array(
    		                                            '_id' => array('$in'=>$arr_invoices_id)
    		                                            ),
    		                       'arr_order' => array('_id'=>-1),
    		                       'arr_field' => array('options','products')
    		                       ));
    		$last_invoice = array();
    		$i = 0;
    		foreach($arr_invoices as $value){
    			if($i == 0){
    				$last_invoice = $this->Salesinvoice->select_one(array('_id' => $value['_id']));
		    		if(!isset($value['products']))
		    			$value['products'] = array();
		    		if(!isset($value['options']))
		    			$value['options'] = array();
    				$last_invoice = array_merge($last_invoice,$value);
					$products_num = count($value['products']);
					$options_num = count($value['options']);
					$i++;
					continue;
    			}
    			if(!isset($value['products']))
	    			$value['products'] = array();
	    		if(!isset($value['options']))
	    			$value['options'] = array();
    			$arr_data = $this->build_salesorder($value,$products_num,$options_num);
				$last_invoice['options'] = array_merge($last_invoice['options'],$arr_data['options']);
				$last_invoice['products'] = array_merge($last_invoice['products'],$arr_data['products']);
				$last_invoice['salesinvoices'][] = $value['_id'];
				$this->Salesinvoice->save(array('_id'=>$value['_id'],'deleted'=>true));
    		}
    		$arr_sum = $this->new_cal_sum($last_invoice['products']);
    		$last_invoice = array_merge($last_invoice,$arr_sum);
    		//=====================================================
			$this->Salesinvoice->save($last_invoice);
			$this->Session->write( 'SalesinvoicesViewId',(string)$last_invoice['_id']);
			echo 'ok';
    	} else
    		echo 'You must choose at least one Sales order.';
    	die;
    }

    public function update_job_number()
    {
    	$this->selectModel('Job');
    	$this->selectModel('Quotation');
    	$this->selectModel('Salesorder');
    	$this->selectModel('Salesinvoice');
    	$jobs = $this->Job->select_all(array(
    			'arr_where' => array(
	    						'deleted' 	=> false,
	    						'no'		=> array(
	    								'$nin' => array(
	    										new MongoRegex('/-/')
	    									)
	    							)
	    					),
    			'arr_field'	=> array('_id'),
    			'arr_order' => array('_id' => 1)
    		));

    	echo $jobs->count().' jobs found<br />';
    	$jobNumber = 0;
    	foreach ($jobs as $job) {
    		$jobNumber++;
    		$this->Job->collection->update(
    				array('_id' => $job['_id']),
    				array('$set' => array('no' => $jobNumber))
    			);
    		foreach (array('Quotation', 'Salesorder', 'Salesinvoice') as $model) {
    			$this->$model->collection->update(
    										array('job_id' => $job['_id']),
    										array('$set' => array('job_number' => $jobNumber)),
    										array('multiple' => true)
    									);
    		}
    	}
    	die;
    }
}