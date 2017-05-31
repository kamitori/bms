<?php
class JobsController extends MobileAppController {
	var $modelName = 'Job';
	var $name = 'Jobs';
	function beforeFilter() {
		parent::beforeFilter();
		if(!$this->request->is('ajax'))
			$this->set('arr_tabs', array(
			           			'resources',
			           			'quotes',
			           			'tasks',
			           			'orders',
			           			'invoices',
			           			'purchases',
			           			'shippings',
			           			'docs',
			           			'other',
			           			'general' => array('requirements','keywords','communications'),
			           			));
	}

	public function entry($id = '0', $num_position = -1) {
		$arr_tmp = $this->entry_init($id, $num_position, 'Job', 'jobs');
		$arr_tmp['work_start'] = (is_object($arr_tmp['work_start'])) ? date('m/d/Y', $arr_tmp['work_start']->sec) : '';
		$arr_tmp['work_end'] = (is_object($arr_tmp['work_end'])) ? date('m/d/Y', $arr_tmp['work_end']->sec) : '';

		$this->selectModel('Setting');
		$arr_jobs_type = $this->Setting->select_option(array('setting_value' => 'jobs_type'), array('option'));
		$this->set('arr_jobs_type', $arr_jobs_type);
		if (isset($arr_tmp['type_id'])) {
			$arr_tmp['type'] = $arr_tmp['type_id'];
		}

		$arr_jobs_status = $this->Setting->select_option(array('setting_value' => 'jobs_status'), array('option'));
		$this->set('arr_jobs_status', $arr_jobs_status);
		if (isset($arr_tmp['status_id'])) {
			$arr_tmp['status'] = $arr_tmp['status_id'];
		}

		$this->set('arr_priority', $this->Setting->select_option(array('setting_value' => 'lists_priority'), array('option')));
		$this->set('arr_country', $this->Setting->select_option(array('setting_value' => 'lists_country'), array('option')));


		$arr_tmp1['Job'] = $arr_tmp;
		$this->data = $arr_tmp1;
		$arr_job_id = array();
		if (isset($arr_tmp['our_rep_id']))
			$arr_job_id[] = $arr_tmp['our_rep_id'];
	}

	function auto_save() {
		if (!empty($this->data)) {
			$id = $this->get_id();
			$arr_return = array();
			$arr_post_data = $this->data['Job'];
			$arr_save = $arr_post_data;
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

			$arr_tmp = $this->Job->select_one(array('_id' =>  new MongoId($id)),array('status','company_id','custom_po_no'));

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



			if($arr_save['status_id']=='Completed'){
				if($arr_tmp['status_id']!=$arr_save['status_id']){
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

					if(abs(($salesorder_sum - $salesinvoice_sum) / ($salesinvoice_sum==0? 1 :$salesinvoice_sum) ) > 0.001) {
						echo json_encode(array('status'=>'error','message'=>'sum_different'));
						die;
					}
				}
			}
			if($arr_save['status']!='Completed' && $arr_tmp['status']=='Completed'){
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


			if (isset($arr_save['status']))
				$arr_save['status_id'] = $arr_save['status'];
			if (isset($arr_save['type']))
				$arr_save['type_id'] = $arr_save['type'];

			$work_start_sec = $this->Common->strtotime($arr_save['work_start'] . ' 00:00:00');
			$work_end_sec = $this->Common->strtotime($arr_save['work_end'] . ' 00:00:00');
			if ($work_start_sec > $work_end_sec) {
				echo json_encode(array('status'=>'error','message'=>'date_work'));
				die;
			}
			$arr_save['work_start'] = new MongoDate($work_start_sec);
			$arr_save['work_end'] = new MongoDate($work_end_sec);


			if (strlen(trim($arr_save['contact_id'])) == 24)
				$arr_save['contact_id'] = new MongoId($arr_save['contact_id']);
			else
				$arr_save['contact_id'] = '';
			if(strlen($arr_save['company_id'])==24){
				$arr_save['company_id'] = new MongoId($arr_save['company_id']);
				if(isset($arr_tmp['company_id'])
				   		&& (string)$arr_tmp['company_id']!= (string)$arr_save['company_id']){
					$this->selectModel('Company');
					$company = $this->Company->select_one(array('_id'=>$arr_save['company_id']),array('contact_default_id'));
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
				}
			}
			else
				$arr_save['company_id'] = '';
			if(strlen($arr_save['our_rep_id'])==24)
				$arr_save['our_rep_id'] = new MongoId($arr_save['our_rep_id']);
			else
				$arr_save['our_rep_id'] = '';
			if( strlen($arr_save['name']) > 0 )
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

	function lists( $content_only = '' ) {
		$this->selectModel('Job');
		$limit = 20; $skip = 0;
		// dùng cho sort
		$sort_field = 'no';
		$sort_type = -1;
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

		// dùng cho phân trang
		if(isset($_POST['offset'])){
			$skip = $_POST['offset'];
		}
		// query
		$arr_jobs = $this->Job->select_all(array(
			'arr_where' => $cond,
			'arr_order' => $arr_order,
			'limit' => $limit,
			'skip' => $skip
		));
		$this->set('arr_jobs', $arr_jobs);
		$this->selectModel('Setting');
		$this->set('arr_jobs_type', $this->Setting->select_option(array('setting_value' => 'jobs_type'), array('option')));

		$this->selectModel('Equipment');
		$arr_equipment = $this->Equipment->select_list( array(
			'arr_field' => array('_id', 'name'),
			'arr_order' => array('name' => 1)
		));
		$this->set( 'arr_equipment', $arr_equipment );
		if($content_only!=''){
			$this->render("lists_ajax");
		}
	}

	function delete($id = 0) {
		$arr_save['_id'] = $id;
		$arr_save['deleted'] = true;
		$error = 0;
		if (!$error) {
			$this->selectModel('Job');
			if ($this->Job->save($arr_save)) {
				$this->Session->delete($this->name.'ViewId');
				if($this->request->is('ajax'))
					echo 'ok';
				else
					$this->redirect('/mobile/jobs/entry');
			} else {
				echo 'Error: ' . $this->Job->arr_errors_save[1];
			}
		}
		die;
	}

	public function add() {
		$this->selectModel('Job');
		$arr_save = array();

		if (isset($_GET['salesorder_id'])) {

			$this->selectModel('Salesorder');
			$arr_tmp = $this->Salesorder->select_one(array('_id' => new MongoId($_GET['salesorder_id'])), array('_id', 'heading', 'code', 'company_id', 'company_name', 'contact_id', 'contact_name', 'work_start', 'work_end', 'our_rep', 'our_rep_id'));
			if (isset($arr_tmp['heading'])) {

				$arr_save['salesorder_id'] = $arr_tmp['_id'];
				$arr_save['salesorder_no'] = $arr_tmp['code'];
				$arr_save['salesorder_name'] = $arr_tmp['heading'];
				$arr_save['company_id'] = $arr_tmp['company_id'];
				$arr_save['company_name'] = $arr_tmp['company_name'];
				$arr_save['contact_id'] = $arr_tmp['contact_id'];
				$arr_save['contact_name'] = $arr_tmp['contact_name'];
				$arr_save['our_rep_id_id'] = $arr_tmp['our_rep_id'];
				$arr_save['our_rep_id'] = $arr_tmp['our_rep'];

				$arr_save['work_start'] = $arr_tmp['work_start'];
				$arr_save['work_end'] = $arr_tmp['work_end'];
				if ($arr_save['work_end']->sec > (strtotime('now') + 3600)) {
					$tmp_time = strtotime(date('Y-m-d H') . ':00:00');
					$arr_save['work_start'] = new MongoDate($tmp_time);
					$arr_save['work_end'] = new MongoDate($tmp_time + 3600);
				}
			}
		}

		$this->Job->arr_default_before_save = $arr_save;
		if ($this->Job->add())
			$this->redirect('/mobile/jobs/entry/' . $this->Job->mongo_id_after_save);
		die;
	}

	public function tasks(){
		if(isset($_POST['add']))
			return $this->tasks_add();
		$offset = 0;
		if(isset($_POST['offset']))
			$offset = $_POST['offset'];
		$job_id = $this->get_id();
		$this->selectModel('Task');
		$arr_tasks = $this->Task->select_all(array(
			'arr_where' => array('job_id' => new MongoId($job_id)),
			'arr_order' => array('work_start' => 1),
			'arr_field' => array('no','name','type','our_rep','work_start','work_end','status'),
			'skip' 		=> $offset,
			'limit'		=> 10
		));
		$arr_data = array('data'=>array());
		foreach($arr_tasks as $task){
			$task['_id'] = (string)$task['_id'];
			$task['name'] = isset($task['name']) ? $task['name'] : '';
			$task['type'] = isset($task['type']) ? $task['type'] : '';
			$task['our_rep'] = isset($task['our_rep']) ? $task['our_rep'] : '';
			$task['work_start'] = date('d M, Y', $task['work_start']->sec);
			$task['work_end'] = date('d M, Y', $task['work_end']->sec);
			$arr_data['data'][] = $task;
		}
        if($this->request->is('ajax')){
        	if($arr_tasks->count(true) == 0){
        		echo json_encode(array('empty'=>true));
        		die;
        	}
			echo json_encode($arr_data);
			die;
        } else {
	        $this->set('arr_tasks',$arr_data['data']);
        	$arr_data['field'] = array(
	                           'no'=>array(
	                                       'label' => 'Job no',
	                                       'type' => '',),
	                           'name'=>array(
	                                       'label' => 'Task',
	                                       'type' => '',
	                                       ),
	                           'type'=>array(
	                                       'label' => 'Type',
	                                       'type' => ''),
	                           'our_rep'=>array(
	                                       'label' => 'Responsible',
	                                       'type' => ''),
	                           'work_start'=>array(
	                                       'label' => 'Work start',
	                                       'type' => ''),
	                           'work_end'=>array(
	                                       'label' => 'Work end',
	                                       'type' => '',
	                                       ),
	                           'status'=>array(
	                                       'label' => 'Status',
	                                       'type' => '',),
	                           );
			$arr_data['header'] = array(
			                        'link_to_entry' => M_URL.'/tasks/entry/',
			                        'link_to_entry_value' => 'no',
			                        'info' => 'name'
			                            );
			$this->set('arr_data',$arr_data);
			$this->set('id',$job_id);
        }
	}

	function tasks_add() {
    	$id = $this->get_id();
        $this->selectModel('Job');
        $arr_job = $this->Job->select_one(array('_id' => new MongoId($id)),array('our_rep_id','our_rep','company_id','company_name', 'name', 'contact_id','contact_name','no'));
        $arr_save = array();
        $arr_save['company_name'] = isset($arr_job['company_name']) ? $arr_job['company_name'] : '';
        $arr_save['company_id'] =  isset($arr_job['company_id']) ? $arr_job['company_id'] : '';
        $arr_save['our_rep_id'] = $arr_save['our_rep'] = '';
        $arr_save['job_id'] = $arr_job['_id'];
        $arr_save['job_number'] = $arr_job['no'];
        $arr_save['job_name'] = isset($arr_job['name']) ? $arr_job['name'] : '';
        if (isset($arr_job['our_rep_id']) && is_object($arr_job['our_rep_id']) ){
            $arr_save['our_rep_id'] = $arr_job['our_rep_id'];
            $arr_save['our_rep'] = isset($arr_job['our_rep']) ? $arr_job['our_rep'] : '';
        }
        $arr_save['contact_id'] = $arr_save['contact_name'] = '';
        if (isset($arr_job['contact_id']) && is_object($arr_job['contact_id'])) {
            $arr_save['contact_id'] = $arr_job['contact_id'];
            $arr_save['contact_name'] = isset($arr_job['contact_name']) ? $arr_job['contact_name'] : '';
        }
        $this->selectModel('Task');
        $this->Task->arr_default_before_save = $arr_save;
        if ($this->Task->add())
            echo M_URL.'/tasks/entry/' . $this->Task->mongo_id_after_save;
        else
            echo M_URL .'/tasks/entry';
        die;
    }

	function tasks_delete($task_id){
    	$this->selectModel('Task');
    	$this->Task->save(array('_id' => new MongoId($task_id),'deleted' => true));
    	echo 'ok';
    	die;
    }

	public function popup($key = '') {
        $this->set('key', $key);
        $limit = 100;
        $skip = 0;
        $cond = array();
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
        $limit = 10; $skip = 0;
        if (isset($_POST['pagination']) && $_POST['pagination']['page-num'] > 0) {
            // $limit = $_POST['pagination']['page-list'];
            $page_num = $_POST['pagination']['page-num'];
            //$limit = $_POST['pagination']['page-list'];
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

            if (isset($arr_post['no']) && strlen($arr_post['no']) > 0) {
                $cond['no'] = trim($arr_post['no']);
            }
        }
        $cond['status_id']['$ne'] = 'Completed';
        unset($_GET['_']);
        $this->selectModel('Job');

        $arr_job = $this->Job->select_all(array(
            'arr_where' => $cond,
            'arr_order' => $arr_order,
            'limit' => $limit,
            'skip' => $skip,
            'arr_field' => array('name', 'company_id', 'company_name', 'contact_id', 'contact_name','no')
        ));

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

    function purchases(){
    	if(isset($_POST['add']))
    	   return $this->purchases_add();
    	$offset = 0;
    	if(isset($_POST['offset']))
    		$offset = $_POST['offset'];
    	$id = $this->get_id();
        $this->selectModel('Purchaseorder');
        $arr_purchases = $this->Purchaseorder->select_all(array(
                                                'arr_where' => array('job_id'=>new MongoId($id)),
                                                'arr_order' => array('purchord_date' => -1),
                                                'arr_field' => array('code','purchord_date','purchase_orders_status','our_rep','our_rep_id','sum_sub_total','company_name'),
                                                'skip' => $offset,
                                                'limit'  => 10
                                                ));
        $arr_data = array('data'=>array());
		foreach($arr_purchases as $purchase){
			$purchase['_id'] = (string)$purchase['_id'];
			$purchase['purchord_date'] = date('d M, Y', $purchase['purchord_date']->sec);
			$purchase['purchase_orders_status'] = isset($purchase['purchase_orders_status']) ? $purchase['purchase_orders_status'] : '';
			$purchase['company_name'] = isset($purchase['company_name']) ? $purchase['company_name'] : '';
			$purchase['our_rep'] = isset($purchase['our_rep']) ? $purchase['our_rep'] : '';
			$purchase['sum_sub_total'] = isset($purchase['sum_sub_total'])?number_format((float)$purchase['sum_sub_total'],2):0;
			$arr_data['data'][] = $purchase;
		}
        if($this->request->is('ajax')){
        	if($arr_purchases->count(true) == 0){
        		echo json_encode(array('empty'=>true));
        		die;
        	}
			echo json_encode($arr_data);
			die;
        } else {
	        $this->set('arr_purchases',$arr_data['data']);
        	$arr_data['field'] = array(
	                           'code'=>array(
	                                       'label' => 'Ref no',
	                                       'type' => '',),
	                           'purchord_date'=>array(
	                                       'label' => 'Date',
	                                       'type' => ''),
	                           'purchase_orders_status'=>array(
	                                       'label' => 'Status',
	                                       'type' => ''),
	                           'company_name'=>array(
	                                       'label' => 'Company Name',
	                                       'type' => '',
	                                       ),
	                           'our_rep'=>array(
	                                       'label' => 'Our Rep',
	                                       'type' => '',
	                                       ),
	                           'sum_sub_total'=>array(
	                                       'label' => 'Total (bf.Tax)',
	                                       'type' => '',
	                                       ),
	                           );
			$arr_data['header'] = array(
			                        'link_to_entry' => M_URL.'/purchaseorders/entry/',
			                        'link_to_entry_value' => 'code',
			                        'info' => 'heading'
			                            );
			$this->set('arr_data',$arr_data);
			$this->set('id',$id);
			$sum_amount = $this->Purchaseorder->sum('sum_amount', 'tb_purchaseorder' , array(
			                'job_id' => new MongoId($id),
			                'status' => array('$ne' => 'Cancelled'),
			                'deleted' => false
			                ));
			$sum_tax = $this->Purchaseorder->sum('sum_tax', 'tb_purchaseorder' , array(
			                'job_id' => new MongoId($id),
			                'status' => array('$ne' => 'Cancelled'),
			                'deleted' => false
			                ));
			$sum_sub_total = $this->Purchaseorder->sum('_', 'tb_purchaseorder' , array(
			                'job_id' => new MongoId($id),
			                'status' => array('$ne' => 'Cancelled'),
			                'deleted' => false
			                ));
			$this->set('sum_amount',$sum_amount);
			$this->set('sum_tax',$sum_tax);
			$this->set('sum_sub_total',$sum_sub_total);
        }
    }
    function purchases_add() {
    	$job_id = $this->get_id();
        $this->selectModel('Job');
        $arr_job = $this->Job->select_one(array('_id' => new MongoId($job_id)),array('name','company_name','company_phone','our_rep_id','our_rep'));
        //$arr_save = $this->get_job_info($arr_job);
        $this->selectModel('Purchaseorder');
        $arr_save['job_id'] = $arr_job['_id'];
        $arr_save['code'] = $this->Purchaseorder->get_auto_code('code');
        $arr_save['purchord_date'] = new MongoDate(strtotime(date('Y-m-d')));
        $arr_save['purchase_orders_status'] = 'In progress';
        $arr_save['company_name'] = isset($arr_job['company_name'])?$arr_job['company_name']:'';
        $arr_save['our_rep'] = isset($arr_job['our_rep'])?$arr_job['our_rep']:'';
        //$arr_save['sum_sub_total'] = ;

        $this->Purchaseorder->arr_default_before_save = $arr_save;
        if ($this->Purchaseorder->add()) {
           echo M_URL.'/purchaseorders/entry/'. $this->Purchaseorder->mongo_id_after_save;
        }else
           echo M_URL .'/purchaseorders/entry';
        die;
    }
    function purchases_delete($id) {
        $this->selectModel('Purchaseorder');
    	$this->Purchaseorder->save(array('_id' => new MongoId($id),'deleted' => true));
    	echo 'ok';
    	die;
    }

    public function shippings(){
    	if(isset($_POST['add']))
    	   return $this->shippings_add();
    	$offset = 0;
    	if(isset($_POST['offset']))
    		$offset = $_POST['offset'];
    	$id = $this->get_id();
        $this->selectModel('Shipping');
        $arr_shippings = $this->Shipping->select_all(array(
                                                'arr_where' => array('job_id'=>new MongoId($id)),
                                                'arr_order' => array('shipping_date' => -1),
                                                'arr_field' => array('code','shipping_type','return_status','shipping_date','shipping_status','our_rep','shipper','tracking_no'),
                                                'skip' => $offset,
                                                'limit'  => 10
                                                ));
        $arr_data = array('data'=>array());
		foreach($arr_shippings as $shipping){
			$shipping['_id'] = (string)$shipping['_id'];
			$shipping['shipper'] = isset($shipping['shipper']) ? $shipping['shipper'] : '';
			$shipping['return_status'] = isset($shipping['return_status'])&&$shipping['return_status'] ? 'X' : '';
			$shipping['tracking_no'] = isset($shipping['tracking_no']) ? $shipping['tracking_no'] : '';
			$shipping['our_rep'] = isset($shipping['our_rep']) ? $shipping['our_rep'] : '';
			$shipping['shipping_date'] = isset($shipping['shipping_date']) && is_object($shipping['shipping_date']) ? date('d M, Y', $shipping['shipping_date']->sec) : '';
			$arr_data['data'][] = $shipping;
		}
        if($this->request->is('ajax')){
        	if($arr_shippings->count(true) == 0){
        		echo json_encode(array('empty'=>true));
        		die;
        	}
			echo json_encode($arr_data);
			die;
        } else {
	        $this->set('arr_shippings',$arr_data['data']);
        	$arr_data['field'] = array(
	                           'code'=>array(
	                                       'label' => 'Ref no',
	                                       'type' => '',),
	                           'shipping_type'=>array(
	                                       'label' => 'Type',
	                                       'type' => '',
	                                       ),
	                           'shipping_date'=>array(
	                                       'label' => 'Date Shipped',
	                                       'type' => ''),
	                           'return_status'=>array(
	                                       'label' => 'Return',
	                                       'type' => ''),
	                           'shipping_status'=>array(
	                                       'label' => 'Status',
	                                       'type' => ''),
	                           'our_rep'=>array(
	                                       'label' => 'Our rep',
	                                       'type' => '',
	                                       ),
	                           'shipper'=>array(
	                                       'label' => 'Shipper',
	                                       'type' => '',
	                                       ),
	                           'tracking_no'=>array(
	                                       'label' => 'Tracking no',
	                                       'type' => '',
	                                       ),
	                           );
			$arr_data['header'] = array(
			                        'link_to_entry' => M_URL.'/shippings/entry/',
			                        'link_to_entry_value' => 'code',
			                        'info' => 'tracking_no'
			                            );
			$this->set('arr_data',$arr_data);
			$this->set('id',$id);
        }
    }

    function shippings_add(){
    	$id = $this->get_id();
        $this->selectModel('Job');
        $arr_job = $this->Job->select_one(array('_id' => new MongoId($id)),array('company_id','name','no'));
        if(isset($arr_job['company_id']) && is_object($arr_job['company_id'])){
	        $this->selectModel('Company');
	        $arr_company = $this->Company->select_one(array('_id' => new MongoId($arr_job['company_id'])),array('name','email','phone','contact_default_id','addresses_default_key','addresses','our_csr_id','our_csr','our_rep_id','our_rep','is_customer','is_supplier'));
	    	$arr_save = $this->get_company_info($arr_company, 'shipping');
	    	$arr_save['shipping_type'] = 'Out';
	        if($arr_company['is_customer']==0&&$arr_company['is_supplier']==1)
	            $arr_save['shipping_type'] = 'In';
	        elseif($arr_company['is_customer']==1&&$arr_company['is_supplier']==0)
	            $arr_save['shipping_type'] = 'Out';
        }
    	$this->selectModel('Shipping');
        $arr_save['code'] = $this->Shipping->get_auto_code('code');
        $arr_save['name'] = $arr_save['code'].' - '.(isset($arr_save['company_name']) ? $arr_save['company_name'] : '');
        $arr_save['job_id'] = new MongoId($id);
        $arr_save['job_number'] = $arr_job['no'];
        $arr_save['job_name'] = $arr_job['name'];
        $arr_save['shipping_status'] = 'In Progress';
        $arr_save['shipping_date'] = new MongoDate(strtotime(date('Y-m-d')));
        unset($arr_save['payment_due_date']);
        $this->Shipping->arr_default_before_save = $arr_save;
        if ($this->Shipping->add()) {
            echo M_URL .'/shippings/entry/'. $this->Shipping->mongo_id_after_save;
        }else
            echo M_URL . '/shippings/entry';
        die;
    }

    function shippings_delete($id) {
        $this->selectModel('Shipping');
    	$this->Shipping->save(array('_id' => new MongoId($id),'deleted' => true));
    	echo 'ok';
    	die;
    }

    function get_tax_job($arr_data){
		$arr_return = array();
		$this->selectModel('Tax');
		$key_tax = '';
		if(isset($arr_data['invoice_address'][0]['invoice_province_state_id'])&&$arr_data['invoice_address'][0]['invoice_province_state_id'])
			$key_tax = $arr_data['invoice_address'][0]['invoice_province_state_id'];
		else if(isset($arr_data['shipping_address'][0]['shipping_province_state_id'])&&$arr_data['shipping_address'][0]['shipping_province_state_id'])
			$key_tax = $arr_data['shipping_address'][0]['shipping_province_state_id'];
		$arr_tax = $this->Tax->tax_select_list();
		if(isset($arr_tax[$key_tax])){
			$tax = explode("%",$arr_tax[$key_tax]);
			$arr_return['tax'] = $key_tax;
			$arr_return['taxval'] = (float)$tax[0];
		}
		return $arr_return;
	}

    function get_company_info($arr_company, $address_suffix= 'invoice'){
    	$arr_save = array();
    	$arr_save['company_name'] = isset($arr_company['name'])?$arr_company['name']:'';
        $arr_save['company_id'] = $arr_company['_id'];
        $arr_save['email'] = isset($arr_company['email'])?$arr_company['email']:'';
        $arr_save['phone'] = isset($arr_company['phone'])?$arr_company['phone']:'';
        $this->selectModel('Salesaccount');
        $salesaccount = $this->Salesaccount->select_one(array('company_id' => new MongoId($arr_save['company_id'])),array('payment_terms'));
        $arr_save['payment_terms'] = isset($salesaccount['payment_terms']) ? $salesaccount['payment_terms'] : 0;
        if (isset($arr_company['contact_default_id'])) {
            $this->selectModel('Contact');
            $arr_contact = $this->Contact->select_one(array('_id' => $arr_company['contact_default_id']), array('first_name','last_name','email','direct_dial'));
            $arr_save['contact_id'] = $arr_contact['_id'];
            $arr_save['contact_name'] = $arr_contact['first_name'] . ' ' . $arr_contact['last_name'];
            if(isset($arr_contact['email'])&&$arr_contact['email']!='')
                $arr_save['email'] = $arr_contact['email'];
            if(isset($arr_contact['direct_dial'])&&$arr_contact['direct_dial']!='')
                $arr_save['phone'] = $arr_contact['direct_dial'];
        }
        if(isset($arr_company['addresses_default_key'])){
            $key_default = $arr_company['addresses_default_key'];
            $arr_save[$address_suffix.'_address'][0] = array(
                'deleted' => false,
                $address_suffix.'_address_1' => $arr_company['addresses'][$key_default]['address_1'],
                $address_suffix.'_address_2' => $arr_company['addresses'][$key_default]['address_2'],
                $address_suffix.'_address_3' => $arr_company['addresses'][$key_default]['address_3'],
                $address_suffix.'_town_city' => $arr_company['addresses'][$key_default]['town_city'],
                $address_suffix.'_province_state' => $arr_company['addresses'][$key_default]['province_state'],
                $address_suffix.'_province_state_id' => $arr_company['addresses'][$key_default]['province_state_id'],
                $address_suffix.'_zip_postcode' => $arr_company['addresses'][$key_default]['zip_postcode'],
                $address_suffix.'_country' => $arr_company['addresses'][$key_default]['country'],
                $address_suffix.'_country_id' => $arr_company['addresses'][$key_default]['country_id']
            );
        }elseif(isset($arr_company['addresses'][0])){
            $arr_save[$address_suffix.'_address'][0] = array(
                'deleted' => false,
                $address_suffix.'_address_1' => $arr_company['addresses'][0]['address_1'],
                $address_suffix.'_address_2' => $arr_company['addresses'][0]['address_2'],
                $address_suffix.'_address_3' => $arr_company['addresses'][0]['address_3'],
                $address_suffix.'_town_city' => $arr_company['addresses'][0]['town_city'],
                $address_suffix.'_province_state' => $arr_company['addresses'][0]['province_state'],
                $address_suffix.'_province_state_id' => $arr_company['addresses'][0]['province_state_id'],
                $address_suffix.'_zip_postcode' => $arr_company['addresses'][0]['zip_postcode'],
                $address_suffix.'_country' => $arr_company['addresses'][0]['country'],
                $address_suffix.'_country_id' => $arr_company['addresses'][0]['country_id']
            );
        }
        if(isset($arr_company['our_csr_id']) && is_object($arr_company['our_csr_id'])){
            $arr_save['our_csr'] = $arr_company['our_csr'];
            $arr_save['our_csr_id'] = $arr_company['our_csr_id'];
        }
        if(isset($arr_company['our_rep_id']) && is_object($arr_company['our_rep_id'])){
            $arr_save['our_rep'] = $arr_company['our_rep'];
            $arr_save['our_rep_id'] = $arr_company['our_rep_id'];
        }
        $arr_save = array_merge($arr_save, $this->get_tax_job($arr_save));
        return $arr_save;
    }
    function quotes(){
    	if(isset($_POST['add']))
    		return $this->quotes_add();
    	$offset = 0;
    	if(isset($_POST['offset']))
    		$offset = $_POST['offset'];
    	$id = $this->get_id();
    	$this->selectModel('Quotation');
    	$arr_quotations = $this->Quotation->select_all(array(
    	                                              'arr_where' => array('job_id' => new MongoId($id)),
    	                                              'arr_order' => array('quotation_date' => -1),
    	                                              'arr_field' => array('code','quotation_type','quotation_date','payment_due_date','quotation_status','our_rep','tax','name'),
    	                                              'skip' => $offset,
    	                                              'limit' => 10
    	                                              ));
    	$arr_data = array('data' => array());
    	foreach($arr_quotations as $quotation){
    		$quotation['_id'] = (string)$quotation['_id'];
    		$quotation['code'] = isset($quotation['code']) ? $quotation['code'] : '';
    		$quotation['quotation_type'] = isset($quotation['quotation_type']) ? $quotation['quotation_type'] : '';
    		$quotation['quotation_date'] = isset($quotation['quotation_date']) ?  date('m/d/Y',$quotation['quotation_date']->sec) : '';
    		$quotation['payment_due_date'] = isset($quotation['payment_due_date']) ? date('m/d/Y',$quotation['payment_due_date']->sec) : '';
    		$quotation['quotation_status'] = isset($quotation['quotation_status']) ?$quotation['quotation_status'] : '';
    		$quotation['our_rep'] = isset($quotation['our_rep']) ? $quotation['our_rep'] : '';
    		$quotation['tax'] = isset($quotation['tax']) ? $quotation['tax'] : '';
    		$quotation['name'] = isset($quotation['name']) ? $quotation['name'] : '';
    		$arr_data['data'][] = $quotation;
    	}
    	if($this->request->is('ajax')){
    		if($arr_quotations->count(true)==0){
    			echo json_encode(array('empty'=>true));
    			die;
    		}
    		echo json_encode($arr_data);
    		die;
    	}else {
	        $this->set('arr_quotations',$arr_data['data']);
        	$arr_data['field'] = array(
	                           'code'=>array(
	                                       'label' => 'Ref no',
	                                       'type' => '',),
	                           'quotation_type'=>array(
	                                       'label' => 'Type',
	                                       'type' => ''),
	                           'quotation_date'=>array(
	                                       'label' => 'Date',
	                                       'type' => ''),
	                           'payment_due_date'=>array(
	                                       'label' => 'Due date',
	                                       'type' => ''),
	                           'quotation_status'=>array(
	                                       'label' => 'Status',
	                                       'type' => '',
	                                       ),
	                           'our_rep'=>array(
	                                       'label' => 'Our Rep',
	                                       'type' => '',
	                                       ),
	                           'tax'=>array(
	                                       'label' => 'Tax',
	                                       'type' => '',
	                                       ),
	                           'name'=>array(
	                                       'label' => 'Heading',
	                                       'type' => '',
	                                       ),
	                           );
			$arr_data['header'] = array(
			                        'link_to_entry' => M_URL.'/quotations/entry/',
			                        'link_to_entry_value' => 'code',
			                        'info' => 'heading'
			                            );
			$this->set('arr_data',$arr_data);
			$this->set('id',$id);
			$sum_amount = $this->Quotation->sum('sum_amount', 'tb_quotation' , array(
			                'job_id' => new MongoId($id),
			                'quotation_status' => array('$ne' => 'Cancelled'),
			                'deleted' => false
			                ));
			$sum_tax = $this->Quotation->sum('sum_tax','tb_quotation',array(
			                                 'job_id' => new MongoId($id),
			                                 'quotation_status' => array('$ne' => 'Cancelled'),
			                                 'deleted' => false
			                                 ));
			$sum_sub_total = $this->Quotation->sum('sum_sub_total','tb_quotation',array(
			                                 'job_id' => new MongoId($id),
			                                 'quotation_status' => array('$ne' => 'Cancelled'),
			                                 'deleted' => false
			                                 ));
			$this->set('sum_amount',number_format($sum_amount,2));
			$this->set('sum_tax',number_format($sum_tax, 2));
			$this->set('sum_sub_total',number_format($sum_sub_total, 2));
        }
    }

    function quotes_add(){
    	$job_id = $this->get_id();
    	$this->selectModel('Job');
    	$arr_job = $this->Job->select_one(array('_id' => new MongoId($job_id)),array('name','company_name','company_phone','our_rep_id','our_rep'));
    	$this->selectModel('Quotation');
    	$arr_save['job_id'] = $arr_job['_id'];
    	$arr_save['code'] = $this->Quotation->get_auto_code('code');
    	$arr_save['date_modified'] = new MongoDate(strtotime(date('Y-m-d')));
    	$arr_save['quotation_status'] = 'In progress';
    	$arr_save['company_name'] = isset($arr_job['company_name']) ? $arr_job['company_name']:'';
    	$arr_save['our_rep'] = isset($arr_job['our_rep']) ? $arr_job['our_rep']:'';

    	$this->Quotation->arr_default_before_save = $arr_save;
    	if($this->Quotation->add()){
    		echo M_URL.'/quotations/entry/'.$this->Quotation->mongo_id_after_save;
    	}else
    		echo M_URL.'/quotations/entry';
    	die;
    }

    function quotes_delete($id) {
        $this->selectModel('Quotation');
    	$this->Quotation->save(array('_id' => new MongoId($id),'deleted' => true));
    	echo 'ok';
    	die;
    }

    public function invoices(){
    	if(isset($_POST['add']))
    	   return $this->invoices_add();
    	$offset = 0;
    	if(isset($_POST['offset']))
    		$offset = $_POST['offset'];
    	$id = $this->get_id();
        $this->selectModel('Salesinvoice');
        $this->selectModel('Receipt');
        $arr_invoices = $this->Salesinvoice->select_all(array(
                                                'arr_where' => array('job_id'=>new MongoId($id)),
                                                'arr_order' => array('invoice_date' => -1),
                                                'arr_field' => array('code','invoice_type','invoice_date','invoice_status','our_rep','sum_sub_total','other_comment'),
                                                'skip' => $offset,
                                                'limit'  => 10
                                                ));
        $arr_data = array('data'=>array());
		foreach($arr_invoices as $invoice){
            if($invoice['invoice_status'] == 'Credit' && $invoice['sum_sub_total'] > 0)
                $invoice['sum_sub_total'] = (float)$invoice['sum_sub_total'] * -1;
			$invoice['_id'] = (string)$invoice['_id'];
			$invoice['invoice_type'] = isset($invoice['invoice_type']) ? $invoice['invoice_type'] : '';
			$invoice['invoice_date'] = date('d M, Y', $invoice['invoice_date']->sec);
			$invoice['our_rep'] = isset($invoice['our_rep']) ? $invoice['our_rep'] : '';
			$invoice['other_comment'] = isset($invoice['other_comment']) ? $invoice['other_comment'] : '';
			$invoice['sum_sub_total'] = number_format((float)$invoice['sum_sub_total'],2);
			$arr_data['data'][] = $invoice;
		}
        if($this->request->is('ajax')){
        	if($arr_invoices->count(true) == 0){
        		echo json_encode(array('empty'=>true));
        		die;
        	}
			echo json_encode($arr_data);
			die;
        } else {
	        $this->set('arr_invoices',$arr_data['data']);
        	$arr_data['field'] = array(
	                           'code'=>array(
	                                       'label' => 'Ref no',
	                                       'type' => '',),
	                           'invoice_type'=>array(
	                                       'label' => 'Type',
	                                       'type' => '',
	                                       ),
	                           'invoice_date'=>array(
	                                       'label' => 'Date',
	                                       'type' => ''),
	                           'invoice_status'=>array(
	                                       'label' => 'Status',
	                                       'type' => ''),
	                           'our_rep'=>array(
	                                       'label' => 'Our rep',
	                                       'type' => ''),
	                           'sum_sub_total'=>array(
	                                       'label' => 'Ex. Tax total',
	                                       'type' => '',
	                                       ),
	                           'other_comment'=>array(
	                                       'label' => 'Comments',
	                                       'type' => '',
	                                       ),
	                           );
			$arr_data['header'] = array(
			                        'link_to_entry' => M_URL.'/salesinvoices/entry/',
			                        'link_to_entry_value' => 'code',
			                        'info' => 'other_comment'
			                            );
			$this->set('arr_data',$arr_data);
			$this->set('id',$id);
			$sum_amount = $this->Salesinvoice->sum('sum_amount', 'tb_salesinvoice' , array(
			                'job_id' => new MongoId($id),
			                'invoice_status' => array('$ne' => 'Cancelled'),
			                'deleted' => false
			                ));
			$sum_tax = $this->Salesinvoice->sum('sum_tax', 'tb_salesinvoice' , array(
			                'job_id' => new MongoId($id),
			                'invoice_status' => array('$ne' => 'Cancelled'),
			                'deleted' => false
			                ));
			$sum_sub_total = $this->Salesinvoice->sum('_', 'tb_salesinvoice' , array(
			                'job_id' => new MongoId($id),
			                'invoice_status' => array('$ne' => 'Cancelled'),
			                'deleted' => false
			                ));
			$this->set('sum_amount',$sum_amount);
			$this->set('sum_tax',$sum_tax);
			$this->set('sum_sub_total',$sum_sub_total);
        }
    }

    function invoices_add(){
    	$id = $this->get_id();
    	$this->selectModel('Job');
    	$arr_job = $this->Job->select_one(array('_id' => new MongoId($id)),array('company_id','name','no'));
        if(isset($arr_job['company_id']) && is_object($arr_job['company_id'])){
	        $this->selectModel('Company');
	        $arr_company = $this->Company->select_one(array('_id' => new MongoId($arr_job['company_id'])),array('name','email','phone','contact_default_id','addresses_default_key','addresses','our_csr_id','our_csr','our_rep_id','our_rep','is_customer','is_supplier'));
	    	$arr_save = $this->get_company_info($arr_company);
        }
    	$this->selectModel('Salesinvoice');
        $arr_save['code'] = $this->Salesinvoice->get_auto_code('code');
        $arr_save['name'] = $arr_save['code'].' - '.(isset($arr_save['company_name']) ? $arr_save['company_name'] : '');
        $arr_save['job_id'] = new MongoId($id);
        $arr_save['job_number'] = $arr_job['no'];
        $arr_save['job_name'] = $arr_job['name'];
        $arr_save['status'] = 'In Progress';
        $arr_save['invoice_date'] = new MongoDate(strtotime(date('Y-m-d')));
        $arr_save['payment_due_date'] = new MongoDate($arr_save['invoice_date']->sec + $arr_save['payment_terms']*DAY);
        $this->Salesinvoice->arr_default_before_save = $arr_save;
        if ($this->Salesinvoice->add()) {
            echo M_URL .'/salesinvoices/entry/'. $this->Salesinvoice->mongo_id_after_save;
        }else
            echo M_URL . '/salesinvoices/entry';
        die;
    }

    function invoices_delete($id) {
        $this->selectModel('Salesinvoice');
    	$this->Salesinvoice->save(array('_id' => new MongoId($id),'deleted' => true));
    	echo 'ok';
    	die;
    }


 	public function orders(){
    	if(isset($_POST['add']))
    	   return $this->orders_add();
    	$offset = 0;
    	if(isset($_POST['offset']))
    		$offset = $_POST['offset'];
    	$id = $this->get_id();
        $this->selectModel('Salesorder');
        $this->selectModel('Receipt');
        $arr_orders = $this->Salesorder->select_all(array(
                                                'arr_where' => array('job_id'=>new MongoId($id)),
                                                'arr_order' => array('invoice_date' => -1),
                                                'arr_field' => array('sum_sub_total','sum_amount','sum_tax','status','our_rep','code','salesorder_date','payment_due_date','taxval','tax','heading','other_comment'),
                                                'skip' => $offset,
                                                'limit'  => 10
                                                ));
        $arr_data = array('data'=>array());
        $arr_orders = iterator_to_array($arr_orders);
		foreach($arr_orders as $key => $order){
			$minimum = $this->get_minimum_order('Salesorder',$order['_id']);
			if($order['sum_sub_total']<$minimum){
    			$order['sum_sub_total'] = $minimum;
    			$order['sum_tax'] = $order['sum_sub_total']*(float)$order['taxval']/100;
    			$order['sum_amount'] = $order['sum_sub_total'] + $order['sum_tax'];
    		}
    		if($order['status']=='Cancelled'){
    			$order['sum_sub_total'] = 0;
    			$order['sum_tax'] = 0;
    			$order['sum_amount'] = 0;
    		}
            if($order['status'] == 'Credit' && $order['sum_sub_total'] > 0)
                $order['sum_sub_total'] = (float)$order['sum_sub_total'] * -1;
			$order['_id'] = (string)$order['_id'];
			$order['salesorder_date'] = date('d M, Y', $order['salesorder_date']->sec);
			$order['payment_due_date'] = date('d M, Y', $order['payment_due_date']->sec);
			$order['status'] = isset($order['status']) ? $order['status'] : '';
			$order['our_rep'] = isset($order['our_rep']) ? $order['our_rep'] : '';
			$order['heading'] = isset($order['heading']) ? $order['heading'] : '';
			$order['other_comment'] = isset($order['other_comment']) ? $order['other_comment'] : '';
			$arr_data['data'][] = $order;
			$arr_orders[$key] = $order;
		}
        if($this->request->is('ajax')){
        	if($arr_orders->count(true) == 0){
        		echo json_encode(array('empty'=>true));
        		die;
        	}
			echo json_encode($arr_data);
			die;
        } else {
        	$arr_data['field'] = array(
	                           'code'=>array(
	                                       'label' => 'Ref no',
	                                       'type' => '',),
	                           'salesorder_date'=>array(
	                                       'label' => 'Date in',
	                                       'type' => ''),
	                           'payment_due_date'=>array(
	                                       'label' => 'Due Date',
	                                       'type' => ''),
	                           'status'=>array(
	                                       'label' => 'Status',
	                                       'type' => ''),
	                           'our_rep'=>array(
	                                       'label' => 'Our rep',
	                                       'type' => ''),
	                           'heading'=>array(
	                                       'label' => 'Heading',
	                                       'type' => '',
	                                       ),
	                           'other_comment'=>array(
	                                       'label' => 'Comments',
	                                       'type' => '',
	                                       ),
	                           );
			$arr_data['header'] = array(
			                        'link_to_entry' => M_URL.'/salesorders/entry/',
			                        'link_to_entry_value' => 'code',
			                        'info' => 'other_comment'
			                            );
			$this->set('arr_orders',$arr_data['data']);
			$this->set('arr_data',$arr_data);
			$this->set('id',$id);
			/*$sum_amount = $this->Salesorder->sum('sum_amount', 'tb_salesorder' , array(
			                'job_id' => new MongoId($id),
			                'status' => array('$ne' => 'Cancelled'),
			                'deleted' => false
			                ));
			$sum_tax = $this->Salesorder->sum('sum_tax', 'tb_salesorder' , array(
			                'job_id' => new MongoId($id),
			                'status' => array('$ne' => 'Cancelled'),
			                'deleted' => false
			                ));
			$sum_sub_total = $this->Salesorder->sum('sum_sub_total', 'tb_salesorder' , array(
			                'job_id' => new MongoId($id),
			                'status' => array('$ne' => 'Cancelled'),
			                'deleted' => false
			                ));*/
			$i = 1; $count = 0;
			$total_sub_total = $total_tax = $total_amount = 0;
			foreach ($arr_orders as $key => $value) {
				if($value['status']!='Cancelled'){
					$sub_total = (isset($value['sum_sub_total'])&&$value['sum_sub_total'] ? $value['sum_sub_total'] : 0);
					$tax = (isset($value['sum_tax'])&&$value['sum_tax'] ? $value['sum_tax'] : 0);
					$amount = (isset($value['sum_amount'])&&$value['sum_amount'] ? $value['sum_amount'] : 0);
				
	                $total_sub_total += $sub_total;
	                $total_tax += $tax;
	                $total_amount += $amount;
	            }
	        }
			$this->set('total_amount',$total_amount);
			$this->set('total_tax',$total_tax);
			$this->set('total_sub_total',$total_sub_total);
        }
    }

    function orders_add(){
    	$id = $this->get_id();
    	$this->selectModel('Job');
    	$arr_job = $this->Job->select_one(array('_id' => new MongoId($id)),array('company_id','name','no','work_end','work_start'));
        if(isset($arr_job['company_id']) && is_object($arr_job['company_id'])){
	        $this->selectModel('Company');
	        $arr_company = $this->Company->select_one(array('_id' => new MongoId($arr_job['company_id'])),array('name','email','phone','contact_default_id','addresses_default_key','addresses','our_csr_id','our_csr','our_rep_id','our_rep','is_customer','is_supplier'));
	    	$arr_save = $this->get_company_info($arr_company);
        }
    	$this->selectModel('Salesorder');
        $arr_save['code'] = $this->Salesorder->get_auto_code('code');
        $arr_save['name'] = $arr_save['code'].' - '.(isset($arr_save['company_name']) ? $arr_save['company_name'] : '');
        $arr_save['job_id'] = new MongoId($id);
        $arr_save['job_number'] = $arr_job['no'];
        $arr_save['job_name'] = $arr_job['name'];
        $arr_save['status'] = 'New';
        //$arr_save['salesorder_date'] = new MongoDate(strtotime(date('Y-m-d')));
        $arr_save['salesorder_date'] = (isset($arr_job['work_start']) ? $arr_job['work_start'] : '');

        if(isset($arr_save['company_id']) && is_object($arr_save['company_id'])){
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
		if(isset($arr_save['company_id']) && is_object($arr_save['company_id'])){
			$this->selectModel('Salesaccount');
			$salesaccount = $this->Salesaccount->select_one(array('company_id'=>new MongoId($arr_save['company_id'])),array('payment_terms','payment_terms_id'));
			if(!empty($salesaccount)){
				$arr_save['payment_terms'] = $salesaccount['payment_terms'];
				$arr_save['payment_terms_id'] = $salesaccount['payment_terms_id'];
			}
		}
        //$arr_save['payment_due_date'] = new MongoDate($arr_save['salesorder_date']->sec + $arr_save['payment_terms']*DAY);
        $arr_save['payment_due_date'] = (isset($arr_job['work_end']) ? $arr_job['work_end'] : '');
        $this->Salesorder->arr_default_before_save = $arr_save;
        if ($this->Salesorder->add()) {
            echo M_URL .'/salesorders/entry/'. $this->Salesorder->mongo_id_after_save;
        }else
            echo M_URL . '/salesorders/entry';
        die;
    }

    function orders_delete($id) {
        $this->selectModel('Salesorder');
    	$this->Salesorder->save(array('_id' => new MongoId($id),'deleted' => true));
    	echo 'ok';
    	die;
    }

	public function other() {
		die();
	}

}