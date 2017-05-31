<?php
App::uses('AppController', 'Controller');
class TasksController extends AppController {

	var $modelName = 'Task';

	public function beforeFilter() {
		// goi den before filter cha
		parent::beforeFilter();
		$this->set('title_entry', 'Tasks');
	}

	// public $components = array('Mpdf');
	// function abc(){
	// 	// initializing mPDF
	// 	$this->Mpdf->init();
	// 	// setting filename of output pdf file
	// 	$this->Mpdf->setFilename('file.pdf');
	// 	// setting output to I, D, F, S
	// 	$this->Mpdf->setOutput('D');
	// 	// you can call any mPDF method via component, for example:
	// 	$this->Mpdf->SetWatermarkText("Draft");

	// 	$this->layout = 'ajax';
	// }

	public function swith_options($option = '') {
        parent::swith_options($option);
		if ($option == 'New') {
			$this->Session->write('tasks_entry_search_cond', array('status_id' => 'New'));
			echo URL . DS . $this->params->params['controller'] .DS.'lists';
		}elseif($option == 'Confirmed') {
			$this->Session->write('tasks_entry_search_cond', array('status_id' => 'Confirmed'));
			echo URL . DS . $this->params->params['controller'] .DS.'lists';
		}elseif($option == 'Completed') {
			$this->Session->write('tasks_entry_search_cond', array('status_id' => 'DONE'));
			echo URL . DS . $this->params->params['controller'] .DS.'lists';
		}elseif($option == 'On_Hold') {
			$this->Session->write('tasks_entry_search_cond', array('status_id' => 'On Hold'));
			echo URL . DS . $this->params->params['controller'] .DS.'lists';
		}elseif($option == 'Cancelled') {
			$this->Session->write('tasks_entry_search_cond', array('status_id' => 'Cancelled'));
			echo URL . DS . $this->params->params['controller'] .DS.'lists';

		}elseif($option == 'occuring_today') {
			$cond = array(
				'work_start' => array( '$gte' => new MongoDate(strtotime(date('Y-m-d'))) ),
				'work_end' => array( '$lte' => new MongoDate(strtotime(date('Y-m-d')) + 23*3600 + 1800) )
			);
			$cond['status'] = array( '$in' => array('New', 'Confirmed') ) ;
			$this->Session->write('tasks_entry_search_cond', $cond);
			echo URL . DS . $this->params->params['controller'] .DS.'lists';
		}elseif($option == 'starting_today') {
			$cond = array(
				'work_start' => array( '$gte' => new MongoDate(strtotime(date('Y-m-d'))), '$lte' => new MongoDate(strtotime(date('Y-m-d')) + 23*3600 + 1800) )
			);
			$cond['status'] = array( '$in' => array('New', 'Confirmed') ) ;
			$this->Session->write('tasks_entry_search_cond', $cond);
			echo URL . DS . $this->params->params['controller'] .DS.'lists';
		}elseif($option == 'finishing_today') {
			$cond = array(
				'work_end' => array( '$gte' => new MongoDate(strtotime(date('Y-m-d'))), '$lte' => new MongoDate(strtotime(date('Y-m-d')) + 23*3600 + 1800) )
			);
			$cond['status'] = array( '$in' => array('New', 'Confirmed') ) ;
			$this->Session->write('tasks_entry_search_cond', $cond);
			echo URL . DS . $this->params->params['controller'] .DS.'lists';
		}elseif($option == 'find_late_tasks') {
			$cond = array(
				'work_end' => array( '$lte' => new MongoDate(strtotime('now')) )
			);
			$cond['status'] = array( '$in' => array('New', 'Confirmed') ) ;
			$this->Session->write('tasks_entry_search_cond', $cond);
			echo URL . DS . $this->params->params['controller'] .DS.'lists';
		}elseif($option == 'duplicate_task_does_no_duplicate_expenses_or_documents') {
			echo URL . DS . $this->params->params['controller'] .DS.'duplicate_task_does_no_duplicate_expenses_or_documents';
		}else if($option=='Print_mini_list')
			echo URL . '/' . $this->params->params['controller'] . '/view_minilist';

		die();
	}
	function duplicate_task_does_no_duplicate_expenses_or_documents(){
		$ids=$this->get_id();
		$this->selectModel('Task');
		$arr_task=$this->Task->select_one(array('_id'=>new MongoId($ids)));
		$arr_save=array();

		foreach($arr_task as $key=>$value){

			if($key!='_id'&&$key!='expense'){
				$arr_save[$key]=$value;
			}

		}


		$arr_save['no']=$this->Task->get_auto_code('no');
//		var_dump($arr_save);die;

//		echo $a;die;

		if($this->Task->save($arr_save))
		{
			$this->redirect('/tasks/entry/'. $this->Task->mongo_id_after_save);
		}

		$this->redirect('/tasks/entry');

	}
	function auto_save( $field = '' ) {
		if(!$this->check_permission($this->name.'_@_entry_@_edit')){
			echo 'You do not have permission on this action.';
			die;
		}

		if (!empty($this->data)) {
			$arr_post_data = $this->data['Task'];
			$arr_save = $arr_post_data;

			$this->selectModel('Task');
			$arr_tmp = $this->Task->select_one(array('no' => (int) $arr_save['no'], '_id' => array('$ne' => new MongoId($arr_save['_id']))));
			if (isset($arr_tmp['no'])) {
				echo 'ref_no_existed';
				die;
			}

			$work_start = $this->Common->strtotime($arr_save['work_start'] . '' . $arr_save['work_start_hour'] . ':00');
			$work_end = $this->Common->strtotime($arr_save['work_end'] . '' . $arr_save['work_end_hour'] . ':00');
			$field = str_replace(array('data[Task][', ']'), '', $field);

			if (strlen(trim($arr_save['salesorder_id'])) > 0){
				$arr_save['salesorder_id'] = new MongoId($arr_save['salesorder_id']);

				$this->selectModel('Salesorder');
				$so = $this->Salesorder->select_one(array('_id' => new MongoId($arr_save['salesorder_id'])), array('_id', 'salesorder_date', 'payment_due_date'));

				// Kiểm tra xem work_start có thay đổi work_start không,
				if ($field != '' && ( $field == 'work_start' || $field == 'work_start_hour' )) { // $work_start != $arr_save['work_start_old'] ){
					// nếu có thì có thay đổi đúng không
					if ($work_start < strtotime('now')) {
						echo 'error_work_start';
						die;
					}

					// Kiểm tra salesorder_id
					if (is_object($so['salesorder_date']) && $work_start < $so['salesorder_date']->sec) {
						echo 'work_start_salesorder_date';
						die;
					}

					if (is_object($so['payment_due_date']) && $work_start > ( $so['payment_due_date']->sec + 23*3600 + 1800 ) ) {
						echo 'work_start_due_date';
						die;
					}

					if ($work_start > $work_end) {
						$work_end = $work_start + 3600;
					}

					$check_reload = true;
				}


				// Kiểm tra xem có thay đổi work_end không,
				if ($field != '' && ( $field == 'work_end' || $field == 'work_end_hour' )) { // if($work_end != $arr_save['work_end_old'] ){
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

					if (is_object($so['payment_due_date']) && $work_end > ($so['payment_due_date']->sec + 23*3600 + 1800)) {
						echo 'work_end_payment_due_date';
						die;
					}

					$check_reload = true;
				}

				if( date('h', $work_end) == 0 ){
					$work_end = $work_end - 1800;
				}
				if( $work_start == $work_end ){
					$work_start = $work_start - 1800;
				}

			}


			$arr_save['work_start'] = new MongoDate($work_start);
			$arr_save['work_end'] = new MongoDate($work_end);

			if (strlen(trim($arr_save['our_rep_id'])) > 0)
				$arr_save['our_rep_id'] = new MongoId($arr_save['our_rep_id']);

			if (strlen(trim($arr_save['company_id'])) > 0)
				$arr_save['company_id'] = new MongoId($arr_save['company_id']);

			if (strlen(trim($arr_save['contact_id'])) > 0)
				$arr_save['contact_id'] = new MongoId($arr_save['contact_id']);

			if (strlen(trim($arr_save['enquiry_id'])) > 0)
				$arr_save['enquiry_id'] = new MongoId($arr_save['enquiry_id']);

			if (strlen(trim($arr_save['quotation_id'])) > 0)
				$arr_save['quotation_id'] = new MongoId($arr_save['quotation_id']);

			if (strlen(trim($arr_save['job_id'])) > 0)
				$arr_save['job_id'] = new MongoId($arr_save['job_id']);

			// if (strlen(trim($arr_save['salesorder_id'])) > 0)
			//     $arr_save['salesorder_id'] = new MongoId($arr_save['salesorder_id']);

			if (strlen(trim($arr_save['purchaseorder_id'])) > 0)
				$arr_save['purchaseorder_id'] = new MongoId($arr_save['purchaseorder_id']);

			if( strlen($arr_save['name']) > 0 )
				$arr_save['name']{0} = strtoupper($arr_save['name']{0});

			$this->selectModel('Task');
			if ($this->Task->save($arr_save)) {
				if(isset($arr_save['salesorder_id']) && is_object($arr_save['salesorder_id'])){
					$tasks = $this->Task->select_all(array(
			                        'arr_where'=>array('salesorder_id'=>new MongoId($arr_save['salesorder_id'])),
			                        'arr_field'=>array('status_id','type')
			                        ));
					$id = $arr_save['salesorder_id'];
					if($tasks->count()){
						$count = $tasks->count();
						$i = 0;
						$add = true;
						foreach($tasks as $value){
							if($value['status_id']!='DONE') continue;
							if(isset($value['type'])&&$value['type']=='Accountant'){
								$add = false; break;
							}
							$i++;
						}
						if($add && $i == $count){
							$this->selectModel('Stuffs');
							$accountant = $this->Stuffs->select_one(array('value'=>'Accountant'));
							if(isset($accountant['accountant_id'])){
								$current_date = strtotime(date('Y-m-d H:30:00'));
								$arr_save = array();
								$arr_save['our_rep_type'] = 'contacts';
								$arr_save['salesorder_id'] = new MongoId($id);
								$arr_save['type_id'] = '';
								$arr_save['type'] = 'Accountant';
								$this->selectModel('Salesorder');
								$so = $this->Salesorder->select_one(array('_id'=>new MongoId($arr_save['salesorder_id'])),array('name'));
								$arr_save['name'] = (isset($so['name']) ? $so['name'] : '');
								$arr_save['our_rep'] = $accountant['accountant'];
								$arr_save['our_rep_id'] = $accountant['accountant_id'];
								$arr_save['work_start'] = new MongoDate($current_date);
								$arr_save['work_end'] = new MongoDate($current_date + DAY * 2);
								$this->Task->arr_default_before_save = $arr_save;
								$this->Task->add();
								$this->requestAction('/salesorders/send_accountant/'.$accountant['accountant_id']);
							}
						}
					}
				}
				if( isset($check_reload) ){
					$this->layout = 'ajax';
					$arr_post_data['work_start'] = date('m/d/Y', $arr_save['work_start']->sec);
					$arr_post_data['work_end'] = date('m/d/Y', $arr_save['work_end']->sec);
					$arr_post_data['work_start_hour'] = date('H:i', $arr_save['work_start']->sec);
					$arr_post_data['work_end_hour'] = date('H:i', $arr_save['work_end']->sec);
					$arr_post_data['Task'] = $arr_post_data;
					$this->data = $arr_post_data;
					$this->render('entry_udpate_date');
				}else{
					echo 'ok';die;
				}

			} else {
				echo 'Error: ' . $this->Task->arr_errors_save[1];
				die;
			}
		}
	}

	function delete($id = 0) {
		if(!$this->check_permission($this->name.'_@_entry_@_delete'))
			$this->error_auth();
		$arr_save['_id'] = $id;
		$arr_save['deleted'] = true;
		$error = 0;
		if (!$error) {
			$this->selectModel('Task');
			if ($this->Task->save($arr_save)) {
				$this->Session->delete('Task_entry_id');
				$this->redirect('/tasks/entry');
			} else {
				echo 'Error: ' . $this->Task->arr_errors_save[1];
			}
		}
		die;
	}

	public function add() {
		if(!$this->check_permission($this->name.'_@_entry_@_add'))
			$this->error_auth();
		$this->selectModel('Task');
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

		$this->Task->arr_default_before_save = $arr_save;
		if ($this->Task->add())
			$this->redirect('/tasks/entry/' . $this->Task->mongo_id_after_save);
		die;
	}

	public function entry($id = '0', $num_position = -1) {

		$arr_tmp = $this->entry_init($id, $num_position, 'Task', 'tasks');
		$arr_tmp['work_start_header'] = $arr_tmp['work_start'];
		$arr_tmp['work_start_hour'] = (is_object($arr_tmp['work_start'])) ? date('H:i', $arr_tmp['work_start']->sec) : '';
		$arr_tmp['work_start'] = (is_object($arr_tmp['work_start'])) ? date('m/d/Y', $arr_tmp['work_start']->sec) : '';
		$arr_tmp['work_end_hour'] = (is_object($arr_tmp['work_end'])) ? date('H:i', $arr_tmp['work_end']->sec) : '';
		$arr_tmp['work_end'] = (is_object($arr_tmp['work_end'])) ? date('m/d/Y', $arr_tmp['work_end']->sec) : '';

		
		$arr_tmp['pos_task'] = isset($arr_tmp['pos_task']) ? $arr_tmp['pos_task'] : 0;

		
		$this->selectModel('Setting');
		$arr_tasks_type = $this->Setting->select_option(array('setting_value' => 'tasks_type'), array('option'));
		$this->set('arr_tasks_type', $arr_tasks_type);
		if (isset($arr_tmp['type_id']) && isset($arr_tasks_type[$arr_tmp['type_id']])) {
			$arr_tmp['type'] = $arr_tasks_type[$arr_tmp['type_id']];
		}

		$arr_tasks_status = $this->Setting->select_option(array('setting_value' => 'tasks_status'), array('option'));
		$this->set('arr_tasks_status', $arr_tasks_status);
		if (isset($arr_tmp['status_id']) && isset($arr_tasks_status[$arr_tmp['status_id']])) {
			$arr_tmp['status'] = $arr_tasks_status[$arr_tmp['status_id']];
		}

		$this->set('arr_priority', $this->Setting->select_option(array('setting_value' => 'lists_priority'), array('option')));
		$this->set('arr_country', $this->Setting->select_option(array('setting_value' => 'lists_country'), array('option')));

		if (isset($arr_tmp['type_id']) && isset($arr_tasks_type[$arr_tmp['type_id']])) {
			$arr_tmp['type'] = $arr_tasks_type[$arr_tmp['type_id']];
		}

		if (isset($arr_tmp['enquiry_id']) && is_object($arr_tmp['enquiry_id']) ) {
			$this->selectModel('Enquiry');
			$enquiry = $this->Enquiry->select_one(array('_id' => $arr_tmp['enquiry_id']), array('_id', 'no', 'company'));
			if( isset($enquiry['_id']) ){
				$arr_tmp['enquiry_no'] = $enquiry['no'];
				$arr_tmp['enquiry_name'] = isset($enquiry['company'])?$enquiry['company']:'';
			}
		}

		if (isset($arr_tmp['quotation_id']) && is_object($arr_tmp['quotation_id']) ) {
			$this->selectModel('Quotation');
			$quotation = $this->Quotation->select_one(array('_id' => $arr_tmp['quotation_id']), array('_id', 'code', 'name'));
			if( isset($quotation['_id']) ){
				$arr_tmp['quotation_no'] = $quotation['code'];
				$arr_tmp['quotation_name'] = $quotation['name'];
			}
		}

		if (isset($arr_tmp['job_id']) && is_object($arr_tmp['job_id']) ) {
			$this->selectModel('Job');
			$job = $this->Job->select_one(array('_id' => $arr_tmp['job_id']), array('_id', 'no', 'name'));
			if( isset($job['_id']) ){
				$arr_tmp['job_no'] = $job['no'];
				$arr_tmp['job_name'] = $job['name'];
			}
		}

		if (isset($arr_tmp['salesorder_id']) && is_object($arr_tmp['salesorder_id']) ) {
			$this->selectModel('Salesorder');
			$salesorder = $this->Salesorder->select_one(array('_id' => $arr_tmp['salesorder_id']), array('_id', 'code', 'name'));
			if( isset($salesorder['_id']) ){
				$arr_tmp['salesorder_no'] = $salesorder['code'];
				$arr_tmp['salesorder_name'] = $salesorder['name'];
			}
		}

		if (isset($arr_tmp['purchaseorder_id']) && is_object($arr_tmp['purchaseorder_id']) ) {
			$this->selectModel('Purchaseorder');
			$purchaseorder = $this->Purchaseorder->select_one(array('_id' => $arr_tmp['purchaseorder_id']), array('_id', 'code', 'name'));
			if( isset($purchaseorder['_id']) ){
				$arr_tmp['purchaseorder_no'] = $purchaseorder['code'];
				$arr_tmp['purchaseorder_name'] = isset($purchaseorder['name'])?$purchaseorder['name']:'';
			}
		}

		$arr_tmp1['Task'] = $arr_tmp;
		$this->data = $arr_tmp1;
		$arr_contact_id = array();
		if (isset($arr_tmp['our_rep_id']))
			$arr_contact_id[] = $arr_tmp['our_rep_id'];
		if (isset($arr_tmp['contact_id']))
			$arr_contact_id[] = $arr_tmp['contact_id'];

		// hiển thị cho footer
		$this->show_footer_info($arr_tmp, $arr_contact_id);

		// Get info for subtask
		$this->sub_tab('', $arr_tmp['_id']);
	}

	public function entry_search_all(){
		$this->Session->delete('tasks_entry_search_cond');
		$this->redirect('/tasks/lists');
	}

	public function entry_search() {

		if (!empty($this->data) && $this->request->is('ajax')) {

			$post = $this->data['Task'];
			$cond = array();

			$post = $this->Common->strip_search($post);

			if( strlen($post['no']) > 0 )$cond['no'] = (int)$post['no'];
			if( strlen($post['name']) > 0 )$cond['name'] = new MongoRegex('/' . trim($post['name']) . '/i');
			if( strlen($post['type_id']) > 0 )$cond['type_id'] = $post['type_id'];
			if( strlen($post['our_rep_id']) > 0 )$cond['our_rep_id'] = new MongoId($post['our_rep_id']);
			if( strlen($post['status_id']) > 0 )$cond['status_id'] = $post['status_id'];
			if( strlen($post['priority_id']) > 0 )$cond['priority_id'] = $post['priority_id'];
			if( strlen($post['company_id']) > 0 )$cond['company_id'] = new MongoId($post['company_id']);
			if( strlen($post['contact_id']) > 0 )$cond['contact_id'] = new MongoId($post['contact_id']);
			if( strlen($post['job_id']) > 0 )$cond['job_id'] = new MongoId($post['job_id']);
			if( strlen($post['job_no']) > 0 )$cond['job_no'] = (int)$post['job_no'];
			if( strlen($post['salesorder_id']) > 0 )$cond['salesorder_id'] = new MongoId($post['salesorder_id']);
			if( strlen($post['salesorder_no']) > 0 )$cond['salesorder_no'] = (int)$post['salesorder_no'];
			if( strlen($post['enquiry_id']) > 0 )$cond['enquiry_id'] = new MongoId($post['enquiry_id']);
			if( strlen($post['enquiry_no']) > 0 )$cond['enquiry_no'] = (int)$post['enquiry_no'];

			if( strlen($post['work_start']) > 0 ){
				$sec = $this->Common->strtotime($arr_save['work_start'] . '00:00:00');
				$cond['work_start'] = array( '$gt' => new MongoDate($sec) );
			};

			if( strlen($post['work_end']) > 0 ){
				$sec = $this->Common->strtotime($arr_save['work_end'] . '00:00:00');
				$cond['work_end'] = array( '$lt' => new MongoDate($sec) );
			};

			$this->selectModel('Task');
            $this->identity($cond);
			$tmp = $this->Task->select_one($cond);
			if( $tmp ){
				$this->Session->write('tasks_entry_search_cond', $cond);

				$cond['_id'] = array('$ne' => $tmp['_id']);
				$tmp1 = $this->Task->select_one($cond);
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
		$this->set('arr_tasks_type', $this->Setting->select_option(array('setting_value' => 'tasks_type'), array('option')));
		$this->set('arr_tasks_status', $this->Setting->select_option(array('setting_value' => 'tasks_status'), array('option')));
		$this->set('arr_priority', $this->Setting->select_option(array('setting_value' => 'lists_priority'), array('option')));
		$this->set('arr_country', $this->Setting->select_option(array('setting_value' => 'lists_country'), array('option')));

		$this->set('set_footer', 'footer_search');
		$this->set('address_country', $this->country());
		$this->set('address_province', $this->province("CA"));

		// Get info for subtask
		// $this->sub_tab('', $arr_tmp['_id']);
	}
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
                $tmp['Task']['company'] = $_GET['company_name'];
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
        if (!empty($this->data) && !empty($_POST) && isset($this->data['Task'])) {
            $arr_post = $this->Common->strip_search($this->data['Task']);
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
        $cond['status_id']['$ne'] = 'DONE';
        $this->selectModel('Task');
        $arr_task = $this->Task->select_all(array(
            'arr_where' => $cond,
            'arr_order' => $arr_order,
            'limit' => $limit,
            'skip' => $skip,
            'arr_field' => array('name', 'company_id', 'company_name', 'contact_id', 'contact_name','our_rep_id','our_rep','no')
        ));
        $this->set('arr_task', $arr_task);

        $total_page = $total_record = $total_current = 0;
        if (is_object($arr_task)) {
            $total_current = $arr_task->count(true);
            $total_record = $arr_task->count();
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

	var $name = 'Tasks';
	var $sub_tab_default = 'general';

	function general($task_id) {
		$this->set('task_id', $task_id);

		$this->selectModel('Task');
		$arr_task = $this->Task->select_one(array('_id' => new MongoId($task_id)), array('contacts', 'detail', 'contacts_default_key'));
		$this->set('arr_task', $arr_task);

		$this->noteactivity($task_id);
	}

	function general_delete_contact($task_id, $key) {

		$this->selectModel('Task');
		$this->Task->collection->update(
				array('_id' => new MongoId($task_id)), array('$set' => array(
				'contacts.' . $key . '.deleted' => true
			)
				)
		);
		echo 'ok';
		die;
	}

	function general_window_contact_choose($task_id, $contact_id, $contact_name) {

		$this->selectModel('Task');
		$arr_task = $this->Task->select_one(array('_id' => new MongoId($task_id)), array('contacts'));
		$check_not_exist = true;
		$default = true;
		if (isset($arr_task['contacts'])) {
			foreach ($arr_task['contacts'] as $value) {
				if(isset($value['deleted']) && $value['deleted']) continue;
				if ((string) $value['contact_id'] == $contact_id) {
					$check_not_exist = false;
					break;
				}
			}
			$contact = array_filter($arr_task['contacts'], function($arr){
	    					return isset($arr['deleted'])&&!$arr['deleted']&&$arr['default'];
	    				});
			if(!empty($contact))
				$default = false;
		}
		if ($check_not_exist) {
			$this->Task->collection->update(
						array('_id' => new MongoId($task_id)), array('$push' => array(
						'contacts' => array(
							'contact_name' => $contact_name,
							'contact_id' => new MongoId($contact_id),
							'default' => $default,
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

	function general_choose_manager($task_id, $option_id) {

		// gán lại key deleted để không bị mất
		$this->selectModel('Task');
		$this->Task->collection->update(
				array('_id' => new MongoId($task_id)), array('$set' => array('contacts.' . $option_id . '.default' => true, 'contacts_default_key' => $option_id))
		);
		echo 'ok';
		die;
	}

	function resources($task_id) {
		// get all equipments are used for this task
		$this->selectModel('Resource');
		$arr_task = $this->Resource->select_all(array(
			'arr_where' => array('module_id' => new MongoId($task_id)),
			'arr_order' => array('_id' => 1)
		));
		$this->set('arr_task', $arr_task);
		$this->set('task_id', $task_id);
		$this->selectModel('Setting');
		$this->set('arr_equipments_status', $this->Setting->select_option(array('setting_value' => 'equipments_status'), array('option')));
	}

	function resources_auto_save( $field = '' ) {
		if (!empty($this->data)) {
			$this->selectModel('Task');
			$task = $this->Task->select_one(array('_id'=>new MongoId($this->get_id())),array('work_end','work_start'));
			foreach ($this->data['Resource'] as $value) {
				$arr_save = $value;
			}

			$work_start = $this->Common->strtotime($arr_save['work_start'] . '' . $arr_save['work_start_hour'] . ':00');
			if($work_start < $task['work_start']->sec){
				echo 'error_work_start'; die;
			}

			$work_end = $this->Common->strtotime($arr_save['work_end'] . '' . $arr_save['work_end_hour'] . ':00');
			if($work_end < $task['work_end']->sec){
				echo 'error_work_end'; die;
			}

			if($work_end < $work_start ){
				echo 'error_time'; die;
			}

			$arr_save['work_start'] = new MongoDate($work_start);
			$arr_save['work_end'] = new MongoDate($work_end);

			$this->selectModel('Resource');
			if ($this->Resource->save($arr_save)) {
				echo 'ok';
			} else {
				echo 'Error: ' . $this->Resource->arr_errors_save[1];
			}
		}
		die;
	}

	function resources_delete($task_id) {
		$arr_save['_id'] = $task_id;
		$arr_save['deleted'] = true;
		$this->selectModel('Resource');
		if ($this->Resource->save($arr_save)) {
			echo 'ok';
		} else {
			echo 'Error: ' . $this->Resource->arr_errors_save[1];
		}
		die;
	}

	function resources_window_choose($task_id, $type, $item_id, $name = '') {

		// thong tin cua contact hoac equipment
		$arr_save['type'] = $type;
		$arr_save['item_id'] = new MongoId($item_id);
		$arr_save['name'] = $name;

		// thong tin cua module duoc chen resource vao
		$this->selectModel('Task');
		$arr_task = $this->Task->select_one(array('_id' => new MongoId($task_id)));
		$arr_save['module'] = 'Task';
		$arr_save['module_id'] = $arr_task['_id'];
		$arr_save['text'] = $arr_task['name'];
		$arr_save['status'] = 'New';
		$arr_save['status_id'] = 'New';


		if( strtotime('now') > $arr_task['work_start']->sec ){

			if( date('i') <= 30 ){
				$work_start = strtotime(date('Y-m-d H') . ':30:00');
				$arr_save['work_start'] = new MongoDate( $work_start );
			}else{
				$work_start = strtotime(date('Y-m-d H') . ':00:00')  + 3600;
				$arr_save['work_start'] = new MongoDate( $work_start );
			}

		}else{
			$work_start = $arr_task['work_start']->sec;
			$arr_save['work_start'] = $arr_task['work_start'];
		}

		$arr_save['work_end'] = new MongoDate($work_start + 3600);

		$this->selectModel('Resource');
		if ($this->Resource->save($arr_save)) {

			// Lưu vào danh sách contact theo dõi
			if( $type == 'Contact' ){
				$this->selectModel('Task');
				$task_tmp = $this->Task->select_one(array('_id' => new MongoId($task_id)));
				$check_exist_contact = false;

				if( isset($task_tmp['contacts']) ){
					foreach ($task_tmp['contacts'] as $key => $value) {
						if( (string)$value['contact_id'] == $item_id ){
							$check_exist_contact = true;
							break;
						}
					}
				}

				if( !$check_exist_contact ){
					$task_tmp['contacts'][] = array(
						"contact_name" => $arr_save['name'],
						"contact_id" => $arr_save['item_id'],
						"deleted" => false
					);
					if( !$this->Task->save( $task_tmp ) ){
						echo 'Error: ' . $this->Resource->arr_errors_save[1]; die;
					}
				}
			}
			echo 'ok';

		} else {
			echo 'Error: ' . $this->Resource->arr_errors_save[1];
		}
		die;
	}

	function timelog() {

	}

	function expensive() {
		$ids=$this->get_id();
		$this->set('task_id',$ids);
		$this->selectModel('Task');
		$arr_task = $this->Task->select_one(array('_id' => new MongoId($ids)), array('expense'));
		$this->set('arr_task', $arr_task);


		$this->selectModel('Setting');
		$arr_company_category = $this->Setting->select_option_vl(array('setting_value'=>'company_category'));
		$this->set('arr_company_category', $arr_company_category);

		$arr_company_type = $this->Setting->select_option_vl(array('setting_value'=>'company_type'));
		$this->set('arr_company_type', $arr_company_type);

		$arr_company_rating = $this->Setting->select_option_vl(array('setting_value'=>'company_rating'));
		$this->set('arr_company_rating', $arr_company_rating);

		$arr_phone_type = $this->Setting->select_option_vl(array('setting_value'=>'phone_type'));
		$this->set('arr_phone_type', $arr_phone_type);

		$arr_custom_field = $this->Setting->select_option_vl(array('setting_value'=>'custom_field'));
		$this->set('arr_custom_field', $arr_custom_field);

		$arr_return = $this->Task->select_one(array('_id' => new MongoId($ids)));
		$this->set('arr_return', $arr_return);

	}
	function expensive_add($task_id){
		$this->selectModel('Task');
		$this->Task->collection->update(
			array('_id' => new MongoId($task_id)), array(
				'$push' => array(
					'expense' => array(
						'heading' => '',
						'details' => '',
						'deleted' => false
					)
				)
			)
		);
		$this->expensive();
		$this->render('expensive');
	}

	function expense_delete($key, $task_id) {
		$this->selectModel('Task');
		$this->Task->collection->update(
			array('_id' => new MongoId($task_id)), array('$set' => array(
				'expense.' . $key . '.deleted' => true
			)
			)
		);
		echo 'ok';
		die;
	}

	function expense_auto_save() {

		if (!empty($this->data)) {

			$default_key = $this->data['Expense']['key'];
			$arr_save = array();
			$arr_save['_id'] = $this->data['Expense']['_id'];

			$arr_save['expense.' . (int) $default_key . '.heading'] = $this->data['Expense']['heading'];
			$arr_save['expense.' . (int) $default_key . '.details'] = $this->data['Expense']['details'];

			$this->selectModel('Task');
			if ($this->Task->save($arr_save)) {
				echo 'ok';
			} else {
				echo 'Error: ' . $this->Task->arr_errors_save[1];
			}
		}
		die;
	}
	function other() {

	}

	function lists( $alerts = "" ) {

		if( $alerts == "alerts_late" ){
			$cond['work_end'] = array( '$lte' => new MongoDate(strtotime('now')) );
			$cond['status'] = array( '$in' => array('New', 'Confirmed') ) ;
			$cond['$or']=array(
				array('contacts' => array('$elemMatch' => array('contact_id' => $_SESSION['arr_user']['contact_id']) )),
				array('our_rep_id' => $_SESSION['arr_user']['contact_id'])
			);
			$this->Session->write('tasks_entry_search_cond', $cond);
		}

		$this->selectModel('Task');

		$limit = LIST_LIMIT; $skip = 0;

		// dùng cho sort
		$sort_field = '_id';
		$sort_type = 1;
		if( isset($_POST['sort']) && strlen($_POST['sort']['field']) > 0 ){
			if( $_POST['sort']['type'] == 'desc' ){
				$sort_type = -1;
			}
			$sort_field = $_POST['sort']['field'];
			$this->Session->write('tasks_lists_search_sort', array($sort_field, $sort_type));

		}elseif( $this->Session->check('tasks_lists_search_sort') ){
			$session_sort = $this->Session->read('tasks_lists_search_sort');
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
		if( $this->Session->check('tasks_entry_search_cond') ){
			$cond = $this->Session->read('tasks_entry_search_cond');
		}

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
		$arr_tasks = $this->Task->select_all(array(
			'arr_where' => $cond,
			'arr_order' => $arr_order,
			'limit' => $limit,
			'skip' => $skip
		));
		$this->set('arr_tasks', $arr_tasks);

		$this->selectModel('Setting');
		$this->set('arr_tasks_type', $this->Setting->select_option(array('setting_value' => 'tasks_type'), array('option')));
		$this->set('arr_tasks_status', $this->Setting->select_option(array('setting_value' => 'tasks_status'), array('option')));
		$this->set('arr_priority', $this->Setting->select_option(array('setting_value' => 'lists_priority'), array('option')));

		$total_page = $total_record = $total_current = 0;
		if( is_object($arr_tasks) ){
			$total_current = $arr_tasks->count(true);
			$total_record = $arr_tasks->count();
			if( $total_record%$limit != 0 ){
				$total_page = floor($total_record/$limit) + 1;
			}else{
				$total_page = $total_record/$limit;
			}
		}
		$this->set('total_current', $total_current);
		$this->set('total_page', $total_page);
		$this->set('total_record', $total_record);

		$this->selectModel('Equipment');
		$arr_equipment = $this->Equipment->select_list( array(
			'arr_field' => array('_id', 'name'),
			'arr_order' => array('name' => 1)
		));
		$this->set( 'arr_equipment', $arr_equipment );

		if ($this->request->is('ajax')) {
			$this->render('lists_ajax');
		}

		$this->set('sum', $total_record);
	}

	function lists_delete($id = 0) {
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

	function module_delete($id) {
		$arr_save['_id'] = $id;
		$arr_save['deleted'] = true;
		$this->selectModel('Task');
		if ($this->Task->save($arr_save)) {
			echo 'ok';
		} else {
			echo 'Error: ' . $this->Task->arr_errors_save[1];
		}
		die;
	}

	// ================================== CALENDAR ====================================
	public function calendar($type = 'contacts'){

		$this->set('set_footer', '../Tasks/calendar_footer');
		$this->Session->write('calendar_last_visit', '/' . $this->request->url);

		$arr_option = $this->get_option_status_color('tasks_status');
		$arr_status = $arr_option[0];
		unset($arr_status['Cancelled'], $arr_status['On Hold']);
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
		$date_from = strtotime($date_from);
		$date_to = strtotime($date_to);
		$arr_where = array(/*'our_rep_type' => $type*/);
		$arr_where['$or'] = array(
			array(
				'work_start' => array( '$lte' => new MongoDate($date_from) ),
				'work_end' => array( '$gte' => new MongoDate($date_from) )
			),
			array(
				'work_start' => array( '$lte' => new MongoDate($date_to) ),
				'work_end' => array( '$gte' => new MongoDate($date_to) )
			),
			array(
				'work_start' => array( '$gte' => new MongoDate($date_from) ),
				'work_end' => array( '$lte' => new MongoDate($date_to) )
			)
		);
		// $arr_where['work_start'] = array('$gte' =>new MongoDate($date_from) );
		// $arr_where['work_end'] = array('$lte' => new MongoDate($date_to) );
		// $arr_where['salesorder_id'] = '';
		$this->selectModel('Task');
		$arr_tasks = $this->Task->select_all(array(
			'arr_where' => $arr_where,
			// 'arr_order' => array(
			//                      'work_end' => 1,
			//                      )
		));
		// parse like a data jsonp to client
		$str = '<data>';
		$this->selectModel('Contact');
		$arr_employee_color = array();
		foreach ($arr_tasks as $value) {
			// if( isset($value['system_default']) )continue;
			$contact = isset($value['contacts']) ? $value['contacts'] : array();
			if(!empty($contact) ){
				$contact = array_filter($value['contacts'], function($arr){
					return isset($arr['deleted']) && !$arr['deleted'] && $arr['default'];
				});
				if(!empty($contact)){
					$contact = reset($contact);
					$contact = $contact['contact_id'];
				} else
					$contact = '';
			} else
				$contact = '';
			$value['contact_id'] = $contact;
			$str .= '<event';
			$str .= ' id="' . $value['_id'] . '"';
			$str .= ' rep_id="' . $value['our_rep_id'] . '"';
			$str .= ' contact_id="' . $contact . '"';
			$str .= ' status="' . $value['status_id'] . '"';

			// nếu là asset thì lấy color là màu của asset $type
			if( $type == 'assets' ){
				$this->selectModel('Equipment');
				$arr_tmp = $this->Equipment->select_one(array('_id' => $value['our_rep_id']));

				if ($value['work_end']->sec < strtotime('now') && $value['status_id'] != 'DONE'){
					$find = false;
					if(isset($value['contact_id']) && $this->Contact->user_id() == $value['contact_id']) {
						$find = true;
						if(!isset($arr_employee_color[(string)$value['contact_id']])) {
							$employee = $this->Contact->select_one(array('_id' => new MongoId($value['contact_id'])),array('color'));
							$arr_employee_color[(string)$value['contact_id']] = $employee['color'];
						}
						$str .= ' color="' . $arr_employee_color[(string)$value['contact_id']] . '"';
					}
					if(!$find)
						$str .= ' color="red"';
				}else{
					if( isset($arr_tmp['color']) && isset($arr_tmp['color'][$value['status_id']]) ){
						$str .= ' color="'.$arr_tmp['color'][$value['status_id']].'"';
					}else{
						$str .= ' color="green"';
					}
				}

			}else{
				// type == contacts
				// Nếu sales order bị trễ thì chuyển sang màu đỏ
				if ($value['status_id'] == 'DONE'){
					$str .= ' color="black"';
				}elseif ($value['work_end']->sec < strtotime('now') && $value['status_id'] != 'DONE'){
					$str .= ' color="red"';
				}elseif ($value['status_id'] == 'Confirmed'){
					$str .= ' color="blue"';
				}elseif ($value['status_id'] == 'On Hold'){
					$str .= ' color="#FFA500"';
				}elseif ($value['status_id'] == 'New'){
					$find = false;
					if(isset($value['contact_id']) && $this->Contact->user_id() == $value['contact_id']) {
						$find = true;
						if(!isset($arr_employee_color[(string)$value['contact_id']])) {
							$employee = $this->Contact->select_one(array('_id' => new MongoId($value['contact_id'])),array('color'));
							$arr_employee_color[(string)$value['contact_id']] = $employee['color'];
						}
						$str .= ' color="' . $arr_employee_color[(string)$value['contact_id']] . '"';
					}
					if(!$find) {
						if(!isset($arr_employee_color[(string)$value['our_rep_id']])) {
							$employee = $this->Contact->select_one(array('_id' => new MongoId($value['our_rep_id'])),array('color'));
							$arr_employee_color[(string)$value['our_rep_id']] = $employee['color'];
						}
						$str .= ' color="' . $arr_employee_color[(string)$value['our_rep_id']] . '"';
					}
				}else{
					if(!isset($arr_employee_color[(string)$value['our_rep_id']])) {
						$employee = $this->Contact->select_one(array('_id' => new MongoId($value['our_rep_id'])),array('color'));
						$arr_employee_color[(string)$value['our_rep_id']] = $employee['color'];
					}
					$str .= ' color="' . $arr_employee_color[(string)$value['our_rep_id']] . '"';
					// $str .= ' color="' . $arr_status_color[$value['status_id']] . '"';
				}
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
			// $text_tooltip_end .= '<br><b>:</b> ' . $value['our_rep'];
			foreach(array('salesorder_name','job_name','our_rep','type','status','contact_name','name') as $field){
				if(isset($value[$field]))
					$value[$field] = htmlentities($value[$field]);
				else
					$value[$field] = '';
			}

			$str .= ' text_so="' .  $value['salesorder_name']  . '"';
			$str .= ' text_job="' .  $value['job_name']. '"';
			$str .= ' text_responsible="' . $value['our_rep'] . '"';
			$str .= ' text_type="' . $value['type']. '"';
			$str .= ' text_status="' . $value['status'] . '"';
			$str .= ' text_contact_name="' . $value['contact_name'] . '"';



			if( date("H", $value['work_start']->sec) >= 7 && date("H", $value['work_end']->sec) >= 7 ){

				$str .= ' start_date="' . date('Y-m-d H:i', $value['work_start']->sec) . '"';
				$str .= ' end_date="' . date('Y-m-d H:i', $value['work_end']->sec) . '"';
			}else{
				$str .= ' start_date="' . date('Y-m-d', $value['work_end']->sec ).' 08:00' . '"';
				$str .= ' end_date="' . date('Y-m-d', $value['work_end']->sec).' 09:00' . '"';
			}

			$str .= ' text="' . $value['name'] . '">';
			$str .= '</event>';
		}
		$str .= '</data>';
		echo $str;
		// echo json_encode($arr_tmp);
		die;
	}

	public function calendar_change(){
		$arr_post_data = $this->data;
		$arr_save['work_start'] = new MongoDate(strtotime($arr_post_data['work_start']));
		$arr_save['work_end'] = new MongoDate(strtotime($arr_post_data['work_end']));

		$this->selectModel('Task');
		$this->selectModel('Salesorder');

		// Kiểm tra salesorder_id
		$task = $this->Task->select_one(array('_id' => new MongoId($arr_post_data['id'])), array('_id', 'salesorder_id', 'status_id', 'status'));
		$arr_option = $this->get_option_status_color('tasks_status');
		$arr_status_move = $arr_option[2];
		if (!isset($arr_status_move[$task['status_id']]) || !$arr_status_move[$task['status_id']]) {
			echo 'You can not move this task, because this status is '.$task['status'];
			die;
		}

		$so = $this->Salesorder->select_one(array('_id' => new MongoId($task['salesorder_id'])), array('_id', 'salesorder_date', 'payment_due_date'));

		if (is_object($so['salesorder_date']) && $arr_save['work_start']->sec < $so['salesorder_date']->sec) {
			echo 'Work start can not less than Order date of SO';
			die;
		}
		if (is_object($so['payment_due_date']) && $arr_save['work_end']->sec > ($so['payment_due_date']->sec + 23*3600 + 1800)) {
			echo 'Work End can not greater than Due date of this SO';
			die;
		}

		$arr_save['_id'] = $arr_post_data['id'];
		$this->selectModel('Task');
		if( $this->Task->save($arr_save) ){
			echo 'ok';
		}else{
			echo 'Error: ' . $this->Task->arr_errors_save[1];
		}
		die;
	}

	public function calendar_rightclick_change($id, $status){
		$arr_option = $this->get_option_status_color('tasks_status');
		$arr_status = $arr_option[0];
		$arr_status_color = $arr_option[1];

		$this->selectModel('Task');
		$arr_save = $this->Task->select_one(array('_id' => new MongoId($id)));
		$arr_save['status_id'] = $status;
		$arr_save['status'] = $arr_status[$arr_save['status_id']];

		if( $this->Task->save($arr_save) ){

			// --------------- lay mau -------------------------------------------
			// nếu là asset thì lấy color là màu của asset $type
			if( isset($arr_save['our_rep_type']) && $arr_save['our_rep_type'] == 'assets' ){
				$this->selectModel('Equipment');
				$arr_tmp = $this->Equipment->select_one(array('_id' => $arr_save['our_rep_id']));
				// Nếu  bị trễ thì chuyển sang màu đỏ
				if ($arr_save['work_end']->sec < strtotime('now') && $arr_save['status_id'] != 'DONE'){
					$color = 'red';
				}else{
					if( isset($arr_tmp['color']) && isset($arr_tmp['color'][$arr_save['status_id']]) ){
						$color = $arr_tmp['color'][$arr_save['status_id']];
					}else{
						$color = 'green';
					}
				}

			}else{
				// type == contacts
				// Nếu  bị trễ thì chuyển sang màu đỏ
				if ($arr_save['status_id'] == 'DONE'){
					$color = 'black';
				}elseif ($arr_save['work_end']->sec < strtotime('now') && $arr_save['status_id'] != 'DONE'){
					$color = 'red';
				}elseif ($arr_save['status_id'] == 'Confirmed'){
					$color = 'blue';
				}elseif ($arr_save['status_id'] == 'On Hold'){
					$color = '#FFA500';
				}elseif ($arr_save['status_id'] == 'New'){
					$color = 'green';
				}else{
					$color = 'green';
				}
			}
			echo $color;
			// ----------------------------------------------------------

		}else{
			echo 'Error: ' . $this->Task->arr_errors_save[1];
		}
		die;
	}

	// public function module_redirect($id){
	//     $this->selectModel('Task');
	//     $arr_tmp = $this->Task->select_one( array('_id' => new MongoId($id)), array('module', 'module_id') );
	//     $this->redirect('/' . strtolower($arr_tmp['module']) . 's/entry/' . (string)$arr_tmp['module_id'] );
	// }

	function change_so_entry_date( $salesorder_id ){
		// $this->selectModel('Salesorder');
		// $arr_salesorder = $this->Salesorder->select_one( array('_id' => new MongoId( $salesorder_id )) );

		$this->selectModel('Task');
		$arr_task = $this->Task->select_all(array(
			'arr_where' => array(
				 'salesorder_id' => new MongoId($salesorder_id),
			),
			'arr_order' => array('system_default' => -1)
		));

		$salesorder_date = $this->Common->strtotime( $_POST['salesorder_date'] . ' 00:00:00' );
		$payment_due_date = $this->Common->strtotime( $_POST['payment_due_date'] . ' 00:00:00' );

		$error_check_work_start = $error_check_work_end = true;
		foreach ($arr_task as $key => $task) {

			if( isset($task['system_default']) && $task['system_default'] ){
				$arr_save = $task;
				continue;
			}

			if($salesorder_date > $task['work_start']->sec ){
				echo 'error_check_work_start';
				break;
			}
			if( ($payment_due_date + DAY) < $task['work_end']->sec ){
				echo 'error_check_work_end';
				break;
			}
		}

		// cập nhật
		if( isset($arr_save) ){
			$arr_save['work_start'] = new MongoDate( $payment_due_date + date("H")*3600);
			$arr_save['work_end'] = new MongoDate( $payment_due_date + date("H")*3600 + 3600);
			if (!$this->Task->save($arr_save)) {
				echo 'Error: ' . $this->Task->arr_errors_save[1]; die;
			}
		}
		die;
		// $arr_salesorder
	}

	function change_so_entry_heading( $salesorder_id ){

		$this->selectModel('Task');
		$arr_task = $this->Task->select_all(array(
			'arr_where' => array(
				 'salesorder_id' => new MongoId($salesorder_id),
			),
			'arr_field'	=> array('our_rep','our_rep_type','system_default')
		));
		foreach ($arr_task as  $arr_save) {
			$arr_save['name'] = $_POST['name'];
			if(isset($arr_save['our_rep_type'])&&$arr_save['our_rep_type']=='assets')
				$arr_save['name'] = $_POST['name'].' - '.$arr_save['our_rep'];
			if (!$this->Task->save($arr_save)) {
				echo 'Error: ' . $this->Task->arr_errors_save[1]; die;
			}
		}
		echo 'ok';
		die;
	}

	function change_so_entry_status( $salesorder_id ){
		$this->selectModel('Task');
		$arr_task = $this->Task->select_all(array(
			'arr_where' => array(
				 'salesorder_id' => new MongoId($salesorder_id)
			),
			'arr_field' => array('_id', 'status_id')
		));
		// cập nhật
		foreach ($arr_task as $key => $task) {
			if( !in_array($task['status_id'], array('DONE','Cancelled')) ){
				echo 'dont_change_status'; die;
			}
		}
		echo 'ok';
		die;
	}

	public function redirect_so( $id ){
		$this->selectModel('Task');
		$arr_task = $this->Task->select_one(array(
			'_id' => new MongoId( $id )
		), array('_id', 'salesorder_id'));
		$this->redirect('/salesorders/entry/'.$arr_task['salesorder_id']);
		die;
	}

	public function quick_view() {
		// load model
		$this->selectModel('Task');
		$this->selectModel('Contact');
		// set default
		$cond = array();
		$order = array('_id' => -1);
		// load dong thu may trong csdl
		$skip = 0;
		$limit = LIST_LIMIT;
		// seach or sort
		if ($this->request->is('ajax')) {
			// seach
			if ($_REQUEST['our_rep'] != '') {
				$cond['name'] = new MongoRegex('/' . $_REQUEST['our_rep'] . '/i');
			}
			// end seach
			// set offset load_more
			if (isset($_REQUEST['offset'])) {
				$skip = (int) $_REQUEST['offset'];
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
			$order = array($sort_key => $sort);
			// end sort
		}
		// query
		$this->set('arr_tasks', $this->Task->select_all(array(
					'arr_where' => $cond,
					'arr_order' => $order,
					'limit' => $limit,
					'skip' => $skip
		)));
		// render ajax view
		if ($this->request->is('ajax')) {
			$this->render('quick_view_ajax');
		}
		// select contact
		$this->selectModel('Contact');
		$contact_list = $this->Contact->select_all(array(
			'arr_field' => array('first_name'),
			'arr_order' => array('first_name' => 1)
		));
		foreach ($contact_list as $value) {
			$contact_list_tmp[] = (string) $value['first_name'];
		}
		$this->set('arr_contact_list', $contact_list_tmp);
	}


	function view_minilist1() {
		$this->layout = 'pdf';

		$date_now = date('Ymd');
		$time=time();
		$filename = 'TA'.$date_now.$time;
		$this->selectModel('Task');
		$sort_field = '_id';
		$sort_type = 1;
		if( isset($_POST['sort']) && strlen($_POST['sort']['field']) > 0 ){
			if( $_POST['sort']['type'] == 'desc' ){
				$sort_type = -1;
			}
			$sort_field = $_POST['sort']['field'];
			$this->Session->write('tasks_lists_search_sort', array($sort_field, $sort_type));

		}elseif( $this->Session->check('tasks_lists_search_sort') ){
			$session_sort = $this->Session->read('tasks_lists_search_sort');
			$sort_field = $session_sort[0];
			$sort_type = $session_sort[1];
		}
		$arr_order = array($sort_field => $sort_type);
		$cond = array();
		if( $this->Session->check('tasks_entry_search_cond') ){
			$cond = $this->Session->read('tasks_entry_search_cond');
		}
		$arr_pur = $this->Task->select_all(array(
			'arr_where' => $cond,
			'arr_order' => $arr_order,
			'limit' => LIMIT_PRINT_PDF
		));
		$html='';
		$this->selectModel('Contact');
		$i=0;
		foreach($arr_pur as $key=>$value){

			if($i%2==0)
				$html .= ' <table cellpadding="4" cellspacing="0" class="tab_nd">';
			else
				$html .= '<table cellpadding="4" cellspacing="0" class="tab_nd2">';

			$html .= ' <tr class="border_2">
			<td width="24%" class="first top border_left border_btom">';

			if(isset($value['name']))
				$html .= $value['name'];

			$html .= '</td>
			<td width="10%" class="top border_btom border_left" align="center">';

			if(isset($value['job_no']))
				$html .= $value['job_no'];

			$html .='</td>
			<td width="10%" class="top border_btom border_left " align="left">';


			if(isset($value['our_rep']))
				$html .=$value['our_rep'];

			$html .='</td>
			<td align="left" width="10%" class="top border_btom border_left">
				';

			if(isset($value['type']))
				$html .=$value['type'];



			$html.='
			</td>
			<td align="center" width="10%" class="top border_btom border_left">
				';


			if(isset($value['priority']))
				$html .=$value['priority'];


			$html.='
			</td>
			<td width="10%" class="end top border_btom border_left" align="center">
				';

			if(isset($value['work_start'])&&is_object($value['work_start']))
				$html .= $this->Task->format_date($value['work_start']->sec,false);


			$html.='
			</td>
			<td width="10%" class="end top border_btom border_left" align="center">
				';

			if(isset($value['work_end'])&&is_object($value['work_end']))
				$html .= $this->Task->format_date($value['work_end']->sec,false);


			$html.='
			</td>
			<td width="8%" class="end top border_btom border_left">
				';

			if(isset($value['status']))
				$html .=$value['status'];


			$html.='
			</td>
			<td width="8%" class="end top border_btom border_left"  align="center">
				';


			if(isset($value['work_end'])){
				if( $value['work_end']->sec < strtotime('now')){
					$html .= '<span class="Late">X</span>';
				}
			}



			$html.='
			</td>
		</tr>
	</table>
';


			$i+=1;
		}
		$html_new = $html;

		// =================================================== tao file PDF ==============================================//
		include(APP.'Vendor'.DS.'nguyenpdf.php');

		$pdf = new XTCPDF();
		date_default_timezone_set('UTC');
		$pdf->today=date("g:i a, j F, Y");
		$textfont = 'freesans'; // looks better, finer, and more condensed than 'dejavusans'

		$pdf->SetCreator(PDF_CREATOR);
		$pdf->SetAuthor('Anvy Digital');
		$pdf->SetTitle('Anvy Digital Company');
		$pdf->SetSubject('Company');
		$pdf->SetKeywords('Company, PDF');

// set default header data
		$pdf->setPrintHeader(true);
		$pdf->setPrintFooter(true);

// set default monospaced font
		$pdf->SetDefaultMonospacedFont(2);

// set margins
		$pdf->SetMargins(10, 52, 10);
		$pdf->file3 = 'img'.DS.'bar_975x23.png';

		$pdf->file2_left=231;
		$pdf->file2='img'.DS.'task_minilist.png';


		$pdf->bar_top_left=199;
		$pdf->bar_top_top=23;
		$pdf->bar_top_content='--------------------------------------------------------------------------';

		$pdf->hidden_left=260;
		$pdf->hidden_top=19;
		$pdf->hidden_content='(with main task)';

		$pdf->bar_big_content='------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------';

		$pdf->bar_words_content='Task                                                                 Job no         Responsible       Type                          Priority              Start date          Finish date      Status                 Late';
//		$pdf->bar_mid_content='         |                                                    |                               |                         |                          |              |';
		$pdf->bar_mid_content='';

		$pdf->printedat_left=223;
		$pdf->printedat_top=28;
		$pdf->time_left=241;
		$pdf->time_top=28;

		$pdf->SetHeaderMargin(PDF_MARGIN_HEADER);
		$pdf->SetFooterMargin(PDF_MARGIN_FOOTER);

// set auto page breaks
		$pdf->SetAutoPageBreak(TRUE, 30);

// set image scale factor
		$pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);
		if (@file_exists(dirname(__FILE__).DS.'lang'.DS.'eng.php')) {
			require_once(dirname(__FILE__).DS.'lang'.DS.'eng.php');
			$pdf->setLanguageArray($l);
		}

// ---------------------------------------------------------
// set font
		$pdf->SetFont($textfont, '', 9);

// add a page
		$pdf->AddPage('L', 'A4');
		$pdf->SetMargins(10, 19, 10);

		$pdf->file1 = 'img'.DS.'null.png';
		$pdf->file2 = 'img'.DS.'null.png';
		$pdf->file4 = 'img'.DS.'null.png';
		$pdf->file5 = 'img'.DS.'null.png';
		$pdf->file6 = 'img'.DS.'null.png';
		$pdf->file7 = 'img'.DS.'null.png';

		$pdf->file3_top=10;
		$pdf->address_1='';
		$pdf->address_2='';
		$pdf->bar_words_top=11;
		$pdf->bar_mid_top=10.6;
		$pdf->hidden_content='';
		$pdf->bar_top_content='';
		$pdf->today='';
		$pdf->print='';
		$pdf->bar_big_content='';
		$html='
			<style>
				table{
					font-size: 12px;
					font-family: arial;
				}
				td.first{
					border-left:1px solid #e5e4e3;
				}
				td.end{
					border-right:1px solid #e5e4e3;
				}
				td.top{
					color:#fff;
					font-weight:bold;
					background-color:#911b12;
					border-top:1px solid #e5e4e3;
				}
				td.bottom{
					border-bottom:1px solid #e5e4e3;
				}
				.option{
					color: #3d3d3d;
					font-weight:bold;
					font-size:18px;
					text-align: center;
					width:100%;
				}
				.border_left{
					border-left:1px solid #A84C45;
				}
				.border_1{
					border-bottom:1px solid #911b12;
				}

			</style>
			<style>
					table.tab_nd{
						font-size: 12px;
						font-family: arial;
					}
					table.tab_nd td.first{
						border-left:1px solid #e5e4e3;
					}
					table.tab_nd td.end{
						border-right:1px solid #e5e4e3;
					}
					table.tab_nd td.top{
						background-color:#FDFBF9;
						border-top:1px solid #e5e4e3;
						font-weight: normal;
						color: #3E3D3D;
					}
					table.tab_nd .border_2{
						border-bottom:1px solid red;
					}
					table.tab_nd .border_left{
						border-left:1px solid #E5E4E3;
						border-bottom:1px solid #E5E4E3;
					}
					table.tab_nd .border_btom{
						border-bottom:1px solid #E5E4E3;
					}

				</style>
				<style>
						table.tab_nd2{
							font-size: 12px;
							font-family: arial;
						}
						table.tab_nd2 td.first{
							border-left:1px solid #e5e4e3;
						}
						table.tab_nd2 td.end{
							border-right:1px solid #e5e4e3;
						}
						table.tab_nd2 td.top{
							background-color:#EDEDED;
							border-top:1px solid #e5e4e3;
							font-weight: normal;
							color: #3E3D3D;
						}
						table.tab_nd2 .border_2{
							border-bottom:1px solid red;
						}
						table.tab_nd2 .border_left{
							border-left:1px solid #E5E4E3;
							border-bottom:1px solid #E5E4E3;
						}
						table.tab_nd2 .border_btom{
							border-bottom:1px solid #E5E4E3;
						}
						.size_font{
							font-size: 12px !important;
						}

					</style>
		';
		$html.=$html_new;
		$html .= '

	<table cellpadding="3" cellspacing="0" class="tab_nd2">
		<tr class="border_2">
			<td width="80%" class="first top border_btom size_font">
				&nbsp;';

		$html .= $i;

		$html .=' records listed
			</td>
			<td width="20%" class="end top border_btom">
				&nbsp;
			</td>
		</tr>
	</table>
	<div style=" clear:both; color: #c9c9c9;"><br />
--------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------
	</div><br />
	';
		$pdf->writeHTML($html, true, false, true, true, '');
		$pdf->Output(APP. 'webroot'.DS. 'upload'.DS .$filename.'.pdf', 'F');
		$this->redirect('/upload/'. $filename .'.pdf');
		die;
	}


	function report_by_responsible() {
		$this->layout = 'pdf';

		$date_now = date('Ymd');
		$time=time();
		$filename = 'TA'.$date_now.$time;
		$this->selectModel('Task');
		$sort_field = '_id';
		$sort_type = 1;
		if( isset($_POST['sort']) && strlen($_POST['sort']['field']) > 0 ){
			if( $_POST['sort']['type'] == 'desc' ){
				$sort_type = -1;
			}
			$sort_field = $_POST['sort']['field'];
			$this->Session->write('tasks_lists_search_sort', array($sort_field, $sort_type));

		}elseif( $this->Session->check('tasks_lists_search_sort') ){
			$session_sort = $this->Session->read('tasks_lists_search_sort');
			$sort_field = $session_sort[0];
			$sort_type = $session_sort[1];
		}
		$arr_order = array($sort_field => $sort_type);
		$cond = array();
		if( $this->Session->check('tasks_entry_search_cond') ){
			$cond = $this->Session->read('tasks_entry_search_cond');
		}
		$arr_pur = $this->Task->select_all(array(
			'arr_where' => $cond,
			'arr_order' => $arr_order,
			'limit' => LIMIT_PRINT_PDF
		));

		$html='';
		$this->selectModel('Contact');
		$i=0;

		$arr_rep=array();
		foreach($arr_pur as $key=>$value){
			if(isset($value['our_rep_id'])){
				$arr_rep[(string)$value['our_rep_id']][]=array(
					'our_rep'=>isset($value['our_rep'])?$value['our_rep']:'',
					'name'=>isset($value['name'])?$value['name']:'',
					'type'=>isset($value['type'])?$value['type']:'',
					'priority'=>isset($value['priority'])?$value['priority']:'',
					'work_start'=>isset($value['work_start'])?$value['work_start']:'',
					'work_end'=>isset($value['work_end'])?$value['work_end']:'',
					'status'=>isset($value['status'])?$value['status']:'',
					'job_no'=>isset($value['job_no'])?$value['job_no']:'',
				);
			}

		}

		foreach($arr_rep as $key=>$value){
			$html.='<table cellpadding="3" cellspacing="0" class="maintb">';
			$html .='<tr><td></td></tr>';
			$html .='<tr>';
			$html.='<td align="left" width="100%" class="top">';
			$html .=$value[0]['our_rep'];
			$html.='</td>';
			$html.='</tr>';
			$html.='</table>';
			$k=0;
			foreach($value as $key1=>$value1){
				if($k%2==0)
					$html .= ' <table cellpadding="4" cellspacing="0" class="tab_nd">';
				else
					$html .= '<table cellpadding="4" cellspacing="0" class="tab_nd2">';

				$html .= ' <tr class="border_2">
			<td width="10%" class="top border_btom border_left">';

				if(isset($value1['job_no']))
					$html .= $value1['job_no'];

				$html .='</td>
			<td align="left" width="30%" class="top border_btom border_left">
				';

				if(isset($value1['name']))
					$html .= $value1['name'];


				$html .='</td>
			<td align="left" width="10%" class="top border_btom border_left">
				';

				if(isset($value1['type']))
					$html .= $value1['type'];

				$html .='</td>
			<td align="left" width="10%" class="top border_btom border_left">
				';

				if(isset($value1['priority']))
					$html .= $value1['priority'];


				$html .='</td>
			<td align="center" width="10%" class="top border_btom border_left">
				';

				if(isset($value1['work_start'])&&is_object($value1['work_start']))
					$html .= $this->Task->format_date($value1['work_start']->sec,false);


				$html .='</td>
			<td align="center" width="10%" class="top border_btom border_left">
				';

				if(isset($value1['work_end'])&&is_object($value1['work_end']))
					$html .= $this->Task->format_date($value1['work_end']->sec,false);


				$html .='</td>
			<td align="left" width="10%" class="top border_btom border_left border_right">
				';

				if(isset($value1['status']))
					$html .= $value1['status'];


				$html .='</td>
			<td align="center" width="10%" class="top border_btom border_left border_right">
				';

				if(isset($value1['work_end'])){
					if( $value1['work_end']->sec < strtotime('now')){
						$html .= '<span class="Late">X</span>';
					}
				}


				$html.='
			</td>
		</tr>
	</table>
';

				$k++;
				$i+=1;
			}
		}

//pr($arr_rep);
//die;

		$html_new = $html;

		// =================================================== tao file PDF ==============================================//
		include(APP.'Vendor'.DS.'nguyenpdf.php');

		$pdf = new XTCPDF();
		date_default_timezone_set('UTC');
		$pdf->today=date("g:i a, j F, Y");
		$textfont = 'freesans'; // looks better, finer, and more condensed than 'dejavusans'

		$pdf->SetCreator(PDF_CREATOR);
		$pdf->SetAuthor('Anvy Digital');
		$pdf->SetTitle('Anvy Digital Company');
		$pdf->SetSubject('Company');
		$pdf->SetKeywords('Company, PDF');

// set default header data
		$pdf->setPrintHeader(true);
		$pdf->setPrintFooter(true);

// set default monospaced font
		$pdf->SetDefaultMonospacedFont(2);

// set margins
		$pdf->SetMargins(10, 10, 10);
		$pdf->file1 = 'img'.DS.'null.png';
		$pdf->file3 = 'img'.DS.'bar_975x23.png';

		$pdf->file2_left=198;
		$pdf->file2='img'.DS.'null.png';


		$pdf->bar_top_left=198;
		$pdf->bar_top_top=23;
		$pdf->bar_top_content='--------------------------------------------------------------------------';

		$pdf->hidden_left=258;
		$pdf->hidden_top=19;
		$pdf->hidden_content='(with main task)';

		$pdf->bar_big_content='------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------';

		$pdf->bar_words_content='Job no              Task                                                                             Type                          Priority              Start date         Finish date         Status                       Late';
//		$pdf->bar_mid_content='         |                                                    |                               |                         |                          |              |';
		$pdf->bar_mid_content='';

		$pdf->printedat_left=222;
		$pdf->printedat_top=28;
		$pdf->time_left=240;
		$pdf->time_top=28;

		$pdf->SetHeaderMargin(PDF_MARGIN_HEADER);
		$pdf->SetFooterMargin(PDF_MARGIN_FOOTER);

// set auto page breaks
		$pdf->SetAutoPageBreak(TRUE, 30);

// set image scale factor
		$pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);
		if (@file_exists(dirname(__FILE__).DS.'lang'.DS.'eng.php')) {
			require_once(dirname(__FILE__).DS.'lang'.DS.'eng.php');
			$pdf->setLanguageArray($l);
		}

// ---------------------------------------------------------
// set font
		$pdf->SetFont($textfont, '', 9);
		$html = '
			<table cellpadding="2" cellspacing="0" style="width:100%; margin: 0px auto">
				<tr>
					<td width="40%" valign="top" style="color:#1f1f1f;">
						<img src="img/logo_anvy.png" alt="" />
						<div style="margin-bottom:1px; margin-top:4px;">
							<br>
							<br>
						</div>
						<div></div>
					</td>
					<td width="10%">&nbsp;</td>

					<td width="50%" valign="top" align="right">
						<div style=" text-align:right; font-size:20px; font-weight:bold; color: #919295;width:30%;"><span style="color:#b32017">T</span>ask <span style="color:#b32017">R</span>eport - <span style="color:#b32017">B</span>y <span style="color:#b32017">R</span>esponsible<br><br></div>
						<br>
					</td>
				</tr>
			</table>

				';
// add a page
		$pdf->AddPage('L', 'A4');
		$pdf->SetMargins(10, 12, 10);

		$pdf->file1 = 'img'.DS.'null.png';
		$pdf->file2 = 'img'.DS.'null.png';
		$pdf->file3 = 'img'.DS.'null.png';
		$pdf->file4 = 'img'.DS.'null.png';
		$pdf->file5 = 'img'.DS.'null.png';
		$pdf->file6 = 'img'.DS.'null.png';
		$pdf->file7 = 'img'.DS.'null.png';

		$pdf->file3_top=10;
		$pdf->address_1='';
		$pdf->address_2='';
		$pdf->bar_words_top=11;
		$pdf->bar_mid_top=10.6;
		$pdf->hidden_content='';
		$pdf->bar_top_content='';
		$pdf->today='';
		$pdf->print='';
		$pdf->bar_big_content='';
		$html.='
			<style>
				table{
					font-size: 12px;
					font-family: arial;
				}
				td.first{
					border-left:1px solid #e5e4e3;
				}
				td.end{
					border-right:1px solid #e5e4e3;
				}
				td.top{
					color:#fff;
					font-weight:bold;
					background-color:#911b12;
					border-top:1px solid #e5e4e3;
				}
				td.bottom{
					border-bottom:1px solid #e5e4e3;
				}
				.option{
					color: #3d3d3d;
					font-weight:bold;
					font-size:18px;
					text-align: center;
					width:100%;
				}
				.border_left{
					border-left:1px solid #A84C45;
				}
				.border_1{
					border-bottom:1px solid #911b12;
				}

			</style>
			<style>
					table.tab_nd{
						font-size: 12px;
						font-family: arial;
					}
					table.tab_nd td.first{
						border-left:1px solid #e5e4e3;
					}
					table.tab_nd td.end{
						border-right:1px solid #e5e4e3;
					}
					table.tab_nd td.top{
						background-color:#FDFBF9;
						border-top:1px solid #e5e4e3;
						font-weight: normal;
						color: #3E3D3D;
					}
					table.tab_nd .border_2{
						border-bottom:1px solid red;
					}
					table.tab_nd .border_left{
						border-left:1px solid #E5E4E3;
						border-bottom:1px solid #E5E4E3;
					}
					table.tab_nd .border_right{
						border-right:1px solid #E5E4E3;
						border-bottom:1px solid #E5E4E3;
					}
					table.tab_nd .border_btom{
						border-bottom:1px solid #E5E4E3;
					}

				</style>
				<style>
						table.tab_nd2{
							font-size: 12px;
							font-family: arial;
						}
						table.tab_nd2 td.first{
							border-left:1px solid #e5e4e3;
						}
						table.tab_nd2 td.end{
							border-right:1px solid #e5e4e3;
						}
						table.tab_nd2 td.top{
							background-color:#EDEDED;
							border-top:1px solid #e5e4e3;
							font-weight: normal;
							color: #3E3D3D;
						}
						table.tab_nd2 .border_2{
							border-bottom:1px solid red;
						}
						table.tab_nd2 .border_left{
							border-left:1px solid #E5E4E3;
							border-bottom:1px solid #E5E4E3;
						}
						table.tab_nd2 .border_btom{
							border-bottom:1px solid #E5E4E3;
						}
						.size_font{
							font-size: 12px !important;
						}

					</style>
		';
		$html.=$html_new;
		$html .= '

	<table cellpadding="3" cellspacing="0" class="tab_nd2">
		<tr class="border_2">
			<td width="80%" class="first top border_btom size_font">
				&nbsp;';

		$html .= $i;

		$html .=' records listed
			</td>
			<td width="20%" class="end top border_btom">
				&nbsp;
			</td>
		</tr>
	</table>
	<div style=" clear:both; color: #c9c9c9;"><br />
--------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------
	</div><br />
	';
		$pdf->writeHTML($html, true, false, true, true, '');
		$pdf->Output(APP. 'webroot'.DS. 'upload'.DS .$filename.'.pdf', 'F');
		$this->redirect('/upload/'. $filename .'.pdf');
		die;
	}


	function report_by_responsible_detail() {
		$this->layout = 'pdf';

		$date_now = date('Ymd');
		$time=time();
		$filename = 'TA'.$date_now.$time;
		$this->selectModel('Task');
		$sort_field = '_id';
		$sort_type = 1;
		if( isset($_POST['sort']) && strlen($_POST['sort']['field']) > 0 ){
			if( $_POST['sort']['type'] == 'desc' ){
				$sort_type = -1;
			}
			$sort_field = $_POST['sort']['field'];
			$this->Session->write('tasks_lists_search_sort', array($sort_field, $sort_type));

		}elseif( $this->Session->check('tasks_lists_search_sort') ){
			$session_sort = $this->Session->read('tasks_lists_search_sort');
			$sort_field = $session_sort[0];
			$sort_type = $session_sort[1];
		}
		$arr_order = array($sort_field => $sort_type);
		$cond = array();
		if( $this->Session->check('tasks_entry_search_cond') ){
			$cond = $this->Session->read('tasks_entry_search_cond');
		}
		$arr_pur = $this->Task->select_all(array(
			'arr_where' => $cond,
			'arr_order' => $arr_order,
			'limit' => LIMIT_PRINT_PDF
		));

		$html='';
		$this->selectModel('Contact');
		$i=0;

		$arr_rep=array();
		$this->selectModel('Noteactivity');
		foreach($arr_pur as $key=>$value){



			$obj_noteactivity = $this->Noteactivity->select_all(array(
				'arr_where'=>array('module_id'=>new MongoId($value['_id'])),

			));
			$arr_noteactivity= iterator_to_array($obj_noteactivity,true);

			if(isset($value['our_rep_id'])){
				$arr_rep[(string)$value['our_rep_id']][]=array(
					'our_rep'=>isset($value['our_rep'])?$value['our_rep']:'',
					'name'=>isset($value['name'])?$value['name']:'',
					'type'=>isset($value['type'])?$value['type']:'',
					'priority'=>isset($value['priority'])?$value['priority']:'',
					'work_start'=>isset($value['work_start'])?$value['work_start']:'',
					'work_end'=>isset($value['work_end'])?$value['work_end']:'',
					'status'=>isset($value['status'])?$value['status']:'',
					'job_no'=>isset($value['job_no'])?$value['job_no']:'',
					'noteactivity'=>$arr_noteactivity
				);
			}

		}
//		pr($arr_rep);die;

		foreach($arr_rep as $key=>$value){
			$html.='<table cellpadding="3" cellspacing="0" class="maintb">';
			$html .='<tr><td></td></tr>';
			$html .='<tr>';
			$html.='<td align="left" width="100%" class="top">';
			$html .=$value[0]['our_rep'];
			$html.='</td>';
			$html.='</tr>';
			$html.='</table>';
			$k=0;
			foreach($value as $key1=>$value1){
				if($k%2==0)
					$html .= ' <table cellpadding="4" cellspacing="0" class="tab_nd">';
				else
					$html .= '<table cellpadding="4" cellspacing="0" class="tab_nd2">';

				$html .= ' <tr class="border_2">
			<td width="10%" class="top border_btom border_left">';

				if(isset($value1['job_no']))
					$html .= $value1['job_no'];

				$html .='</td>
			<td align="left" width="30%" class="top border_btom border_left">
				';

				if(isset($value1['name']))
					$html .= $value1['name'];


				$html .='</td>
			<td align="left" width="10%" class="top border_btom border_left">
				';

				if(isset($value1['type']))
					$html .= $value1['type'];

				$html .='</td>
			<td align="left" width="10%" class="top border_btom border_left">
				';

				if(isset($value1['priority']))
					$html .= $value1['priority'];


				$html .='</td>
			<td align="center" width="10%" class="top border_btom border_left">
				';

				if(isset($value1['work_start'])&&is_object($value1['work_start']))
					$html .= $this->Task->format_date($value1['work_start']->sec,false);


				$html .='</td>
			<td align="center" width="10%" class="top border_btom border_left">
				';

				if(isset($value1['work_end'])&&is_object($value1['work_end']))
					$html .= $this->Task->format_date($value1['work_end']->sec,false);


				$html .='</td>
			<td align="left" width="10%" class="top border_btom border_left border_right">
				';

				if(isset($value1['status']))
					$html .= $value1['status'];


				$html .='</td>
			<td align="center" width="10%" class="top border_btom border_left border_right">
				';

				if(isset($value1['work_end'])){
					if( $value1['work_end']->sec < strtotime('now')){
						$html .= '<span class="Late">X</span>';
					}
				}


				$html.='
			</td>
		</tr>
	</table>
';


				if(isset($value1['noteactivity'])&&count($value1['noteactivity'])>0){

				$html .= ' <table cellpadding="4" cellspacing="0" class="tab_nd">';

				$html .= ' <tr class="border_2"><td width="10%" class="">';



				$html .='</td>
			<td align="center" width="10%" class="top border_btom border_left border_right">
				';

				$html.='Detail';

				$html.='
			</td>';



				$html.='</tr> </table>';



					$html.='<table cellpadding="4" cellspacing="0" class="maintb">';

					$html.='<tr class="border_2"><td align="center" width="10%" class=""></td>

					<td align="center" width="10%" class="top">Type</td>
					<td align="center" width="10%" class="top">Date</td>
					<td align="center" width="10%" class="top">By</td>
					<td align="center" width="60%" class="top">Details</td>';

					$html.='</tr> </table>';


				$html.='<table cellpadding="4" cellspacing="0" class="tab_nd">';

				foreach($value1['noteactivity'] as $key2=>$value2){
					$html.='<tr class="border_2"><td align="center" width="10%" ></td>

							<td align="center" width="10%" class="top border_btom border_left border_right" >';

					if(isset($value2['type']))
						$html.=$value2['type'];

					$html.='
							</td>
							<td align="center" width="10%" class="top border_btom border_left border_right">';

					if(isset($value2['date_modified'])&&is_object($value2['date_modified']))
						$html .= $this->Task->format_date($value2['date_modified']->sec,false);

					$html.='</td>
							<td align="center" width="10%" class="top border_btom border_left border_right">';

					if(isset($value[0]['our_rep']))
						$html.=$value[0]['our_rep'];

					$html.='</td>
							<td align="left" width="60%" class="top border_btom border_left border_right">';

					if(isset($value2['content']))
						$html.=$value2['content'];

					$html.='</td>

							</tr>';

				}


				$html.='<tr class="border_2"><td align="center" width="10%" class=""></td></tr>';


				$html.='</table>';

				}
				else
				{

					$html.='<table cellpadding="4" cellspacing="0" class=""><tr class="border_2"><td align="center" width="10%" class=""></td></tr></table>';

				}


				$k++;
				$i+=1;
			}
		}

//pr($arr_rep);
//die;

		$html_new = $html;

		// =================================================== tao file PDF ==============================================//
		include(APP.'Vendor'.DS.'nguyenpdf.php');

		$pdf = new XTCPDF();
		date_default_timezone_set('UTC');
		$pdf->today=date("g:i a, j F, Y");
		$textfont = 'freesans'; // looks better, finer, and more condensed than 'dejavusans'

		$pdf->SetCreator(PDF_CREATOR);
		$pdf->SetAuthor('Anvy Digital');
		$pdf->SetTitle('Anvy Digital Company');
		$pdf->SetSubject('Company');
		$pdf->SetKeywords('Company, PDF');

// set default header data
		$pdf->setPrintHeader(true);
		$pdf->setPrintFooter(true);

// set default monospaced font
		$pdf->SetDefaultMonospacedFont(2);

// set margins
		$pdf->SetMargins(10, 10, 10);
		$pdf->file1 = 'img'.DS.'null.png';
		$pdf->file3 = 'img'.DS.'bar_975x23.png';

		$pdf->file2_left=198;
		$pdf->file2='img'.DS.'null.png';


		$pdf->bar_top_left=198;
		$pdf->bar_top_top=23;
		$pdf->bar_top_content='--------------------------------------------------------------------------';

		$pdf->hidden_left=258;
		$pdf->hidden_top=19;
		$pdf->hidden_content='(with main task)';

		$pdf->bar_big_content='------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------';

		$pdf->bar_words_content='Job no              Task                                                                             Type                          Priority              Start date         Finish date         Status                       Late';
//		$pdf->bar_mid_content='         |                                                    |                               |                         |                          |              |';
		$pdf->bar_mid_content='';

		$pdf->printedat_left=222;
		$pdf->printedat_top=28;
		$pdf->time_left=240;
		$pdf->time_top=28;

		$pdf->SetHeaderMargin(PDF_MARGIN_HEADER);
		$pdf->SetFooterMargin(PDF_MARGIN_FOOTER);

// set auto page breaks
		$pdf->SetAutoPageBreak(TRUE, 30);

// set image scale factor
		$pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);
		if (@file_exists(dirname(__FILE__).DS.'lang'.DS.'eng.php')) {
			require_once(dirname(__FILE__).DS.'lang'.DS.'eng.php');
			$pdf->setLanguageArray($l);
		}

// ---------------------------------------------------------
// set font
		$pdf->SetFont($textfont, '', 9);
		$html = '
			<table cellpadding="2" cellspacing="0" style="width:100%; margin: 0px auto">
				<tr>
					<td width="40%" valign="top" style="color:#1f1f1f;">
						<img src="img/logo_anvy.png" alt="" />
						<div style="margin-bottom:1px; margin-top:4px;">
							<br>
							<br>
						</div>
						<div></div>
					</td>
					<td width="10%">&nbsp;</td>

					<td width="50%" valign="top" align="right">
						<div style=" text-align:right; font-size:20px; font-weight:bold; color: #919295;width:30%;"><span style="color:#b32017">T</span>ask <span style="color:#b32017">R</span>eport - <span style="color:#b32017">B</span>y <span style="color:#b32017">R</span>esponsible<br><br></div>
						<br>
					</td>
				</tr>
			</table>

				';
// add a page
		$pdf->AddPage('L', 'A4');
		$pdf->SetMargins(10, 12, 10);

		$pdf->file1 = 'img'.DS.'null.png';
		$pdf->file2 = 'img'.DS.'null.png';
		$pdf->file3 = 'img'.DS.'null.png';
		$pdf->file4 = 'img'.DS.'null.png';
		$pdf->file5 = 'img'.DS.'null.png';
		$pdf->file6 = 'img'.DS.'null.png';
		$pdf->file7 = 'img'.DS.'null.png';

		$pdf->file3_top=10;
		$pdf->address_1='';
		$pdf->address_2='';
		$pdf->bar_words_top=11;
		$pdf->bar_mid_top=10.6;
		$pdf->hidden_content='';
		$pdf->bar_top_content='';
		$pdf->today='';
		$pdf->print='';
		$pdf->bar_big_content='';
		$html.='
			<style>
				table{
					font-size: 12px;
					font-family: arial;
				}
				td.first{
					border-left:1px solid #e5e4e3;
				}
				td.end{
					border-right:1px solid #e5e4e3;
				}
				td.top{
					color:#fff;
					font-weight:bold;
					background-color:#911b12;
					border-top:1px solid #e5e4e3;
				}
				td.bottom{
					border-bottom:1px solid #e5e4e3;
				}
				.option{
					color: #3d3d3d;
					font-weight:bold;
					font-size:18px;
					text-align: center;
					width:100%;
				}
				.border_left{
					border-left:1px solid #A84C45;
				}
				.border_1{
					border-bottom:1px solid #911b12;
				}

			</style>
			<style>
					table.tab_nd{
						font-size: 12px;
						font-family: arial;
					}
					table.tab_nd td.first{
						border-left:1px solid #e5e4e3;
					}
					table.tab_nd td.end{
						border-right:1px solid #e5e4e3;
					}
					table.tab_nd td.top{
						background-color:#FDFBF9;
						border-top:1px solid #e5e4e3;
						font-weight: normal;
						color: #3E3D3D;
					}
					table.tab_nd .border_2{
						border-bottom:1px solid red;
					}
					table.tab_nd .border_left{
						border-left:1px solid #E5E4E3;
						border-bottom:1px solid #E5E4E3;
					}
					table.tab_nd .border_right{
						border-right:1px solid #E5E4E3;
						border-bottom:1px solid #E5E4E3;
					}
					table.tab_nd .border_btom{
						border-bottom:1px solid #E5E4E3;
					}

				</style>
				<style>
						table.tab_nd2{
							font-size: 12px;
							font-family: arial;
						}
						table.tab_nd2 td.first{
							border-left:1px solid #e5e4e3;
						}
						table.tab_nd2 td.end{
							border-right:1px solid #e5e4e3;
						}
						table.tab_nd2 td.top{
							background-color:#EDEDED;
							border-top:1px solid #e5e4e3;
							font-weight: normal;
							color: #3E3D3D;
						}
						table.tab_nd2 .border_2{
							border-bottom:1px solid red;
						}
						table.tab_nd2 .border_left{
							border-left:1px solid #E5E4E3;
							border-bottom:1px solid #E5E4E3;
						}
						table.tab_nd2 .border_btom{
							border-bottom:1px solid #E5E4E3;
						}
						.size_font{
							font-size: 12px !important;
						}

					</style>
		';
		$html.=$html_new;
		$html .= '

	<table cellpadding="3" cellspacing="0" class="tab_nd2">
		<tr class="border_2">
			<td width="80%" class="first top border_btom size_font">
				&nbsp;';

		$html .= $i;

		$html .=' records listed
			</td>
			<td width="20%" class="end top border_btom">
				&nbsp;
			</td>
		</tr>
	</table>
	<div style=" clear:both; color: #c9c9c9;"><br />
--------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------
	</div><br />
	';
		$pdf->writeHTML($html, true, false, true, true, '');
		$pdf->Output(APP. 'webroot'.DS. 'upload'.DS .$filename.'.pdf', 'F');
		$this->redirect('/upload/'. $filename .'.pdf');
		die;
	}

	function report_by_job() {
		$this->layout = 'pdf';

		$date_now = date('Ymd');
		$time=time();
		$filename = 'TA'.$date_now.$time;
		$this->selectModel('Task');
		$sort_field = '_id';
		$sort_type = 1;
		if( isset($_POST['sort']) && strlen($_POST['sort']['field']) > 0 ){
			if( $_POST['sort']['type'] == 'desc' ){
				$sort_type = -1;
			}
			$sort_field = $_POST['sort']['field'];
			$this->Session->write('tasks_lists_search_sort', array($sort_field, $sort_type));

		}elseif( $this->Session->check('tasks_lists_search_sort') ){
			$session_sort = $this->Session->read('tasks_lists_search_sort');
			$sort_field = $session_sort[0];
			$sort_type = $session_sort[1];
		}
		$arr_order = array($sort_field => $sort_type);
		$cond = array();
		if( $this->Session->check('tasks_entry_search_cond') ){
			$cond = $this->Session->read('tasks_entry_search_cond');
		}
		$arr_pur = $this->Task->select_all(array(
			'arr_where' => $cond,
			'arr_order' => $arr_order,
			'limit' => LIMIT_PRINT_PDF
		));

		$html='';
		$this->selectModel('Contact');
		$i=0;

		$arr_rep=array();
		foreach($arr_pur as $key=>$value){
			if(isset($value['job_no'])&&$value['job_no']!=0){
				$arr_rep[(string)$value['job_no']][]=array(
					'job_name'=>isset($value['job_name'])?$value['job_name']:'',

					'name'=>isset($value['name'])?$value['name']:'',
					'our_rep'=>isset($value['our_rep'])?$value['our_rep']:'',
					'priority'=>isset($value['priority'])?$value['priority']:'',

					'work_start'=>isset($value['work_start'])?$value['work_start']:'',
					'work_end'=>isset($value['work_end'])?$value['work_end']:'',
					'status'=>isset($value['status'])?$value['status']:'',
				);
			}

		}

		foreach($arr_rep as $key=>$value){
			$html.='<table cellpadding="3" cellspacing="0" class="maintb">';
			$html .='<tr><td></td></tr>';
			$html .='<tr>';
			$html.='<td align="left" width="100%" class="top">';
			$html .='Job'.' : '.$key.' - '.$value[0]['job_name'];
			$html.='</td>';
			$html.='</tr>';
			$html.='</table>';
			$k=0;
			foreach($value as $key1=>$value1){
				if($k%2==0)
					$html .= ' <table cellpadding="4" cellspacing="0" class="tab_nd">';
				else
					$html .= '<table cellpadding="4" cellspacing="0" class="tab_nd2">';

				$html .= ' <tr class="border_2">
			<td width="40%" class="top border_btom border_left">';

				if(isset($value1['name']))
					$html .= $value1['name'];


				$html .='</td>
			<td align="left" width="10%" class="top border_btom border_left">
				';

				if(isset($value1['our_rep']))
					$html .= $value1['our_rep'];

				$html .='</td>
			<td align="left" width="10%" class="top border_btom border_left">
				';

				if(isset($value1['priority']))
					$html .= $value1['priority'];


				$html .='</td>
			<td align="center" width="10%" class="top border_btom border_left">
				';

				if(isset($value1['work_start'])&&is_object($value1['work_start']))
					$html .= $this->Task->format_date($value1['work_start']->sec,false);


				$html .='</td>
			<td align="center" width="10%" class="top border_btom border_left">
				';

				if(isset($value1['work_end'])&&is_object($value1['work_end']))
					$html .= $this->Task->format_date($value1['work_end']->sec,false);


				$html .='</td>
			<td align="left" width="10%" class="top border_btom border_left border_right">
				';

				if(isset($value1['status']))
					$html .= $value1['status'];


				$html .='</td>
			<td align="center" width="10%" class="top border_btom border_left border_right">
				';

				if(isset($value1['work_end'])){
					if( $value1['work_end']->sec < strtotime('now')){
						$html .= '<span class="Late">X</span>';
					}
				}


				$html.='
			</td>
		</tr>
	</table>
';

				$k++;
				$i+=1;
			}
		}

//pr($arr_rep);
//die;

		$html_new = $html;

		// =================================================== tao file PDF ==============================================//
		include(APP.'Vendor'.DS.'nguyenpdf.php');

		$pdf = new XTCPDF();
		date_default_timezone_set('UTC');
		$pdf->today=date("g:i a, j F, Y");
		$textfont = 'freesans'; // looks better, finer, and more condensed than 'dejavusans'

		$pdf->SetCreator(PDF_CREATOR);
		$pdf->SetAuthor('Anvy Digital');
		$pdf->SetTitle('Anvy Digital Company');
		$pdf->SetSubject('Company');
		$pdf->SetKeywords('Company, PDF');

// set default header data
		$pdf->setPrintHeader(true);
		$pdf->setPrintFooter(true);

// set default monospaced font
		$pdf->SetDefaultMonospacedFont(2);

// set margins
		$pdf->SetMargins(10, 10, 10);
		$pdf->file1 = 'img'.DS.'null.png';
		$pdf->file3 = 'img'.DS.'bar_975x23.png';

		$pdf->file2_left=198;
		$pdf->file2='img'.DS.'null.png';


		$pdf->bar_top_left=198;
		$pdf->bar_top_top=23;
		$pdf->bar_top_content='--------------------------------------------------------------------------';

		$pdf->hidden_left=258;
		$pdf->hidden_top=19;
		$pdf->hidden_content='(with main task)';

		$pdf->bar_big_content='------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------';

		$pdf->bar_words_content='Task                                                                                                       Responsible       Priority                   Start date         Finish date        Status                         Late';
//		$pdf->bar_mid_content='         |                                                    |                               |                         |                          |              |';
		$pdf->bar_mid_content='';

		$pdf->printedat_left=222;
		$pdf->printedat_top=28;
		$pdf->time_left=240;
		$pdf->time_top=28;

		$pdf->SetHeaderMargin(PDF_MARGIN_HEADER);
		$pdf->SetFooterMargin(PDF_MARGIN_FOOTER);

// set auto page breaks
		$pdf->SetAutoPageBreak(TRUE, 30);

// set image scale factor
		$pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);
		if (@file_exists(dirname(__FILE__).DS.'lang'.DS.'eng.php')) {
			require_once(dirname(__FILE__).DS.'lang'.DS.'eng.php');
			$pdf->setLanguageArray($l);
		}

// ---------------------------------------------------------
// set font
		$pdf->SetFont($textfont, '', 9);
		$html = '
			<table cellpadding="2" cellspacing="0" style="width:100%; margin: 0px auto">
				<tr>
					<td width="40%" valign="top" style="color:#1f1f1f;">
						<img src="img/logo_anvy.png" alt="" />
						<div style="margin-bottom:1px; margin-top:4px;">
							<br>
							<br>
						</div>
						<div></div>
					</td>
					<td width="10%">&nbsp;</td>

					<td width="50%" valign="top" align="right">
						<div style=" text-align:right; font-size:20px; font-weight:bold; color: #919295;width:30%;"><span style="color:#b32017">T</span>ask <span style="color:#b32017">R</span>eport - <span style="color:#b32017">B</span>y <span style="color:#b32017">J</span>ob<br><br></div>
						<br>
					</td>
				</tr>
			</table>

				';
// add a page
		$pdf->AddPage('L', 'A4');
		$pdf->SetMargins(10, 12, 10);

		$pdf->file1 = 'img'.DS.'null.png';
		$pdf->file2 = 'img'.DS.'null.png';
		$pdf->file3 = 'img'.DS.'null.png';
		$pdf->file4 = 'img'.DS.'null.png';
		$pdf->file5 = 'img'.DS.'null.png';
		$pdf->file6 = 'img'.DS.'null.png';
		$pdf->file7 = 'img'.DS.'null.png';

		$pdf->file3_top=10;
		$pdf->address_1='';
		$pdf->address_2='';
		$pdf->bar_words_top=11;
		$pdf->bar_mid_top=10.6;
		$pdf->hidden_content='';
		$pdf->bar_top_content='';
		$pdf->today='';
		$pdf->print='';
		$pdf->bar_big_content='';
		$html.='
			<style>
				table{
					font-size: 12px;
					font-family: arial;
				}
				td.first{
					border-left:1px solid #e5e4e3;
				}
				td.end{
					border-right:1px solid #e5e4e3;
				}
				td.top{
					color:#fff;
					font-weight:bold;
					background-color:#911b12;
					border-top:1px solid #e5e4e3;
				}
				td.bottom{
					border-bottom:1px solid #e5e4e3;
				}
				.option{
					color: #3d3d3d;
					font-weight:bold;
					font-size:18px;
					text-align: center;
					width:100%;
				}
				.border_left{
					border-left:1px solid #A84C45;
				}
				.border_1{
					border-bottom:1px solid #911b12;
				}

			</style>
			<style>
					table.tab_nd{
						font-size: 12px;
						font-family: arial;
					}
					table.tab_nd td.first{
						border-left:1px solid #e5e4e3;
					}
					table.tab_nd td.end{
						border-right:1px solid #e5e4e3;
					}
					table.tab_nd td.top{
						background-color:#FDFBF9;
						border-top:1px solid #e5e4e3;
						font-weight: normal;
						color: #3E3D3D;
					}
					table.tab_nd .border_2{
						border-bottom:1px solid red;
					}
					table.tab_nd .border_left{
						border-left:1px solid #E5E4E3;
						border-bottom:1px solid #E5E4E3;
					}
					table.tab_nd .border_right{
						border-right:1px solid #E5E4E3;
						border-bottom:1px solid #E5E4E3;
					}
					table.tab_nd .border_btom{
						border-bottom:1px solid #E5E4E3;
					}

				</style>
				<style>
						table.tab_nd2{
							font-size: 12px;
							font-family: arial;
						}
						table.tab_nd2 td.first{
							border-left:1px solid #e5e4e3;
						}
						table.tab_nd2 td.end{
							border-right:1px solid #e5e4e3;
						}
						table.tab_nd2 td.top{
							background-color:#EDEDED;
							border-top:1px solid #e5e4e3;
							font-weight: normal;
							color: #3E3D3D;
						}
						table.tab_nd2 .border_2{
							border-bottom:1px solid red;
						}
						table.tab_nd2 .border_left{
							border-left:1px solid #E5E4E3;
							border-bottom:1px solid #E5E4E3;
						}
						table.tab_nd2 .border_btom{
							border-bottom:1px solid #E5E4E3;
						}
						.size_font{
							font-size: 12px !important;
						}

					</style>
		';
		$html.=$html_new;
		$html .= '

	<table cellpadding="3" cellspacing="0" class="tab_nd2">
		<tr class="border_2">
			<td width="80%" class="first top border_btom size_font">
				&nbsp;';

		$html .= $i;

		$html .=' records listed
			</td>
			<td width="20%" class="end top border_btom">
				&nbsp;
			</td>
		</tr>
	</table>
	<div style=" clear:both; color: #c9c9c9;"><br />
--------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------
	</div><br />
	';
		$pdf->writeHTML($html, true, false, true, true, '');
		$pdf->Output(APP. 'webroot'.DS. 'upload'.DS .$filename.'.pdf', 'F');
		$this->redirect('/upload/'. $filename .'.pdf');
		die;
	}

	function report_by_job_detail() {
		$this->layout = 'pdf';

		$date_now = date('Ymd');
		$time=time();
		$filename = 'TA'.$date_now.$time;
		$this->selectModel('Task');
		$sort_field = '_id';
		$sort_type = 1;
		if( isset($_POST['sort']) && strlen($_POST['sort']['field']) > 0 ){
			if( $_POST['sort']['type'] == 'desc' ){
				$sort_type = -1;
			}
			$sort_field = $_POST['sort']['field'];
			$this->Session->write('tasks_lists_search_sort', array($sort_field, $sort_type));

		}elseif( $this->Session->check('tasks_lists_search_sort') ){
			$session_sort = $this->Session->read('tasks_lists_search_sort');
			$sort_field = $session_sort[0];
			$sort_type = $session_sort[1];
		}
		$arr_order = array($sort_field => $sort_type);
		$cond = array();
		if( $this->Session->check('tasks_entry_search_cond') ){
			$cond = $this->Session->read('tasks_entry_search_cond');
		}
		$arr_pur = $this->Task->select_all(array(
			'arr_where' => $cond,
			'arr_order' => $arr_order,
			'limit' => LIMIT_PRINT_PDF
		));

		$html='';
		$this->selectModel('Contact');
		$i=0;

		$arr_rep=array();
		$this->selectModel('Noteactivity');
		foreach($arr_pur as $key=>$value){

			$obj_noteactivity = $this->Noteactivity->select_all(array(
				'arr_where'=>array('module_id'=>new MongoId($value['_id'])),

			));
			$arr_noteactivity= iterator_to_array($obj_noteactivity,true);

			if(isset($value['job_no'])&&$value['job_no']!=0){
				$arr_rep[(string)$value['job_no']][]=array(
					'job_name'=>isset($value['job_name'])?$value['job_name']:'',

					'name'=>isset($value['name'])?$value['name']:'',
					'our_rep'=>isset($value['our_rep'])?$value['our_rep']:'',
					'priority'=>isset($value['priority'])?$value['priority']:'',

					'work_start'=>isset($value['work_start'])?$value['work_start']:'',
					'work_end'=>isset($value['work_end'])?$value['work_end']:'',
					'status'=>isset($value['status'])?$value['status']:'',
					'noteactivity'=>$arr_noteactivity
				);
			}

		}

		foreach($arr_rep as $key=>$value){
			$html.='<table cellpadding="3" cellspacing="0" class="maintb">';
			$html .='<tr><td></td></tr>';
			$html .='<tr>';
			$html.='<td align="left" width="100%" class="top">';
			$html .='Job'.' : '.$key.' - '.$value[0]['job_name'];
			$html.='</td>';
			$html.='</tr>';
			$html.='</table>';
			$k=0;
			foreach($value as $key1=>$value1){
				if($k%2==0)
					$html .= ' <table cellpadding="4" cellspacing="0" class="tab_nd">';
				else
					$html .= '<table cellpadding="4" cellspacing="0" class="tab_nd2">';

				$html .= ' <tr class="border_2">
			<td width="40%" class="top border_btom border_left">';

				if(isset($value1['name']))
					$html .= $value1['name'];


				$html .='</td>
			<td align="left" width="10%" class="top border_btom border_left">
				';

				if(isset($value1['our_rep']))
					$html .= $value1['our_rep'];

				$html .='</td>
			<td align="left" width="10%" class="top border_btom border_left">
				';

				if(isset($value1['priority']))
					$html .= $value1['priority'];


				$html .='</td>
			<td align="center" width="10%" class="top border_btom border_left">
				';

				if(isset($value1['work_start'])&&is_object($value1['work_start']))
					$html .= $this->Task->format_date($value1['work_start']->sec,false);


				$html .='</td>
			<td align="center" width="10%" class="top border_btom border_left">
				';

				if(isset($value1['work_end'])&&is_object($value1['work_end']))
					$html .= $this->Task->format_date($value1['work_end']->sec,false);


				$html .='</td>
			<td align="left" width="10%" class="top border_btom border_left border_right">
				';

				if(isset($value1['status']))
					$html .= $value1['status'];


				$html .='</td>
			<td align="center" width="10%" class="top border_btom border_left border_right">
				';

				if(isset($value1['work_end'])){
					if( $value1['work_end']->sec < strtotime('now')){
						$html .= '<span class="Late">X</span>';
					}
				}


				$html.='
			</td>
		</tr>
	</table>
';



				if(isset($value1['noteactivity'])&&count($value1['noteactivity'])>0){

					$html .= ' <table cellpadding="4" cellspacing="0" class="tab_nd">';

					$html .= ' <tr class="border_2"><td width="10%" class="">';



					$html .='</td>
			<td align="center" width="10%" class="top border_btom border_left border_right">
				';

					$html.='Detail';

					$html.='
			</td>';



					$html.='</tr> </table>';



					$html.='<table cellpadding="4" cellspacing="0" class="maintb">';

					$html.='<tr class="border_2"><td align="center" width="10%" class=""></td>

					<td align="center" width="10%" class="top">Type</td>
					<td align="center" width="10%" class="top">Date</td>
					<td align="center" width="10%" class="top">By</td>
					<td align="center" width="60%" class="top">Details</td>';

					$html.='</tr> </table>';


					$html.='<table cellpadding="4" cellspacing="0" class="tab_nd">';

					foreach($value1['noteactivity'] as $key2=>$value2){
						$html.='<tr class="border_2"><td align="center" width="10%" ></td>

							<td align="center" width="10%" class="top border_btom border_left border_right" >';

						if(isset($value2['type']))
							$html.=$value2['type'];

						$html.='
							</td>
							<td align="center" width="10%" class="top border_btom border_left border_right">';

						if(isset($value2['date_modified'])&&is_object($value2['date_modified']))
							$html .= $this->Task->format_date($value2['date_modified']->sec,false);

						$html.='</td>
							<td align="center" width="10%" class="top border_btom border_left border_right">';

						if(isset($value[0]['our_rep']))
							$html.=$value[0]['our_rep'];

						$html.='</td>
							<td align="left" width="60%" class="top border_btom border_left border_right">';

						if(isset($value2['content']))
							$html.=$value2['content'];

						$html.='</td>

							</tr>';

					}


					$html.='<tr class="border_2"><td align="center" width="10%" class=""></td></tr>';


					$html.='</table>';
				}
				else
				{

					$html.='<table cellpadding="4" cellspacing="0" class=""><tr class="border_2"><td align="center" width="10%" class=""></td></tr></table>';

				}
				$k++;
				$i+=1;
			}
		}

//pr($arr_rep);
//die;

		$html_new = $html;

		// =================================================== tao file PDF ==============================================//
		include(APP.'Vendor'.DS.'nguyenpdf.php');

		$pdf = new XTCPDF();
		date_default_timezone_set('UTC');
		$pdf->today=date("g:i a, j F, Y");
		$textfont = 'freesans'; // looks better, finer, and more condensed than 'dejavusans'

		$pdf->SetCreator(PDF_CREATOR);
		$pdf->SetAuthor('Anvy Digital');
		$pdf->SetTitle('Anvy Digital Company');
		$pdf->SetSubject('Company');
		$pdf->SetKeywords('Company, PDF');

// set default header data
		$pdf->setPrintHeader(true);
		$pdf->setPrintFooter(true);

// set default monospaced font
		$pdf->SetDefaultMonospacedFont(2);

// set margins
		$pdf->SetMargins(10, 10, 10);
		$pdf->file1 = 'img'.DS.'null.png';
		$pdf->file3 = 'img'.DS.'bar_975x23.png';

		$pdf->file2_left=198;
		$pdf->file2='img'.DS.'null.png';


		$pdf->bar_top_left=198;
		$pdf->bar_top_top=23;
		$pdf->bar_top_content='--------------------------------------------------------------------------';

		$pdf->hidden_left=258;
		$pdf->hidden_top=19;
		$pdf->hidden_content='(with main task)';

		$pdf->bar_big_content='------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------';

		$pdf->bar_words_content='Task                                                                                                       Responsible       Priority                   Start date         Finish date        Status                         Late';
//		$pdf->bar_mid_content='         |                                                    |                               |                         |                          |              |';
		$pdf->bar_mid_content='';

		$pdf->printedat_left=222;
		$pdf->printedat_top=28;
		$pdf->time_left=240;
		$pdf->time_top=28;

		$pdf->SetHeaderMargin(PDF_MARGIN_HEADER);
		$pdf->SetFooterMargin(PDF_MARGIN_FOOTER);

// set auto page breaks
		$pdf->SetAutoPageBreak(TRUE, 30);

// set image scale factor
		$pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);
		if (@file_exists(dirname(__FILE__).DS.'lang'.DS.'eng.php')) {
			require_once(dirname(__FILE__).DS.'lang'.DS.'eng.php');
			$pdf->setLanguageArray($l);
		}

// ---------------------------------------------------------
// set font
		$pdf->SetFont($textfont, '', 9);
		$html = '
			<table cellpadding="2" cellspacing="0" style="width:100%; margin: 0px auto">
				<tr>
					<td width="40%" valign="top" style="color:#1f1f1f;">
						<img src="img/logo_anvy.png" alt="" />
						<div style="margin-bottom:1px; margin-top:4px;">
							<br>
							<br>
						</div>
						<div></div>
					</td>
					<td width="10%">&nbsp;</td>

					<td width="50%" valign="top" align="right">
						<div style=" text-align:right; font-size:20px; font-weight:bold; color: #919295;width:30%;"><span style="color:#b32017">T</span>ask <span style="color:#b32017">R</span>eport - <span style="color:#b32017">B</span>y <span style="color:#b32017">J</span>ob<br><br></div>
						<br>
					</td>
				</tr>
			</table>

				';
// add a page
		$pdf->AddPage('L', 'A4');
		$pdf->SetMargins(10, 12, 10);

		$pdf->file1 = 'img'.DS.'null.png';
		$pdf->file2 = 'img'.DS.'null.png';
		$pdf->file3 = 'img'.DS.'null.png';
		$pdf->file4 = 'img'.DS.'null.png';
		$pdf->file5 = 'img'.DS.'null.png';
		$pdf->file6 = 'img'.DS.'null.png';
		$pdf->file7 = 'img'.DS.'null.png';

		$pdf->file3_top=10;
		$pdf->address_1='';
		$pdf->address_2='';
		$pdf->bar_words_top=11;
		$pdf->bar_mid_top=10.6;
		$pdf->hidden_content='';
		$pdf->bar_top_content='';
		$pdf->today='';
		$pdf->print='';
		$pdf->bar_big_content='';
		$html.='
			<style>
				table{
					font-size: 12px;
					font-family: arial;
				}
				td.first{
					border-left:1px solid #e5e4e3;
				}
				td.end{
					border-right:1px solid #e5e4e3;
				}
				td.top{
					color:#fff;
					font-weight:bold;
					background-color:#911b12;
					border-top:1px solid #e5e4e3;
				}
				td.bottom{
					border-bottom:1px solid #e5e4e3;
				}
				.option{
					color: #3d3d3d;
					font-weight:bold;
					font-size:18px;
					text-align: center;
					width:100%;
				}
				.border_left{
					border-left:1px solid #A84C45;
				}
				.border_1{
					border-bottom:1px solid #911b12;
				}

			</style>
			<style>
					table.tab_nd{
						font-size: 12px;
						font-family: arial;
					}
					table.tab_nd td.first{
						border-left:1px solid #e5e4e3;
					}
					table.tab_nd td.end{
						border-right:1px solid #e5e4e3;
					}
					table.tab_nd td.top{
						background-color:#FDFBF9;
						border-top:1px solid #e5e4e3;
						font-weight: normal;
						color: #3E3D3D;
					}
					table.tab_nd .border_2{
						border-bottom:1px solid red;
					}
					table.tab_nd .border_left{
						border-left:1px solid #E5E4E3;
						border-bottom:1px solid #E5E4E3;
					}
					table.tab_nd .border_right{
						border-right:1px solid #E5E4E3;
						border-bottom:1px solid #E5E4E3;
					}
					table.tab_nd .border_btom{
						border-bottom:1px solid #E5E4E3;
					}

				</style>
				<style>
						table.tab_nd2{
							font-size: 12px;
							font-family: arial;
						}
						table.tab_nd2 td.first{
							border-left:1px solid #e5e4e3;
						}
						table.tab_nd2 td.end{
							border-right:1px solid #e5e4e3;
						}
						table.tab_nd2 td.top{
							background-color:#EDEDED;
							border-top:1px solid #e5e4e3;
							font-weight: normal;
							color: #3E3D3D;
						}
						table.tab_nd2 .border_2{
							border-bottom:1px solid red;
						}
						table.tab_nd2 .border_left{
							border-left:1px solid #E5E4E3;
							border-bottom:1px solid #E5E4E3;
						}
						table.tab_nd2 .border_btom{
							border-bottom:1px solid #E5E4E3;
						}
						.size_font{
							font-size: 12px !important;
						}

					</style>
		';
		$html.=$html_new;
		$html .= '

	<table cellpadding="3" cellspacing="0" class="tab_nd2">
		<tr class="border_2">
			<td width="80%" class="first top border_btom size_font">
				&nbsp;';

		$html .= $i;

		$html .=' records listed
			</td>
			<td width="20%" class="end top border_btom">
				&nbsp;
			</td>
		</tr>
	</table>
	<div style=" clear:both; color: #c9c9c9;"><br />
--------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------
	</div><br />
	';
		$pdf->writeHTML($html, true, false, true, true, '');
		$pdf->Output(APP. 'webroot'.DS. 'upload'.DS .$filename.'.pdf', 'F');
		$this->redirect('/upload/'. $filename .'.pdf');
		die;
	}
	function report_by_job_responsible_detail() {
		$this->layout = 'pdf';

		$date_now = date('Ymd');
		$time=time();
		$filename = 'TA'.$date_now.$time;
		$this->selectModel('Task');
		$sort_field = '_id';
		$sort_type = 1;
		if( isset($_POST['sort']) && strlen($_POST['sort']['field']) > 0 ){
			if( $_POST['sort']['type'] == 'desc' ){
				$sort_type = -1;
			}
			$sort_field = $_POST['sort']['field'];
			$this->Session->write('tasks_lists_search_sort', array($sort_field, $sort_type));

		}elseif( $this->Session->check('tasks_lists_search_sort') ){
			$session_sort = $this->Session->read('tasks_lists_search_sort');
			$sort_field = $session_sort[0];
			$sort_type = $session_sort[1];
		}
		$arr_order = array($sort_field => $sort_type);
		$cond = array();
		if( $this->Session->check('tasks_entry_search_cond') ){
			$cond = $this->Session->read('tasks_entry_search_cond');
		}
		$arr_pur = $this->Task->select_all(array(
			'arr_where' => $cond,
			'arr_order' => $arr_order,
			'limit' => LIMIT_PRINT_PDF
		));

		$html='';
		$this->selectModel('Contact');
		$this->selectModel('Job');
		$i=0;

		$arr_rep=array();
		$this->selectModel('Noteactivity');
		foreach($arr_pur as $key=>$value){

			$obj_noteactivity = $this->Noteactivity->select_all(array(
				'arr_where'=>array('module_id'=>new MongoId($value['_id'])),

			));
			$arr_noteactivity= iterator_to_array($obj_noteactivity,true);

			if(isset($value['job_no'])&&$value['job_no']!=0){
				$arr_rep[(string)$value['job_no']][(string)$value['our_rep_id']][]=array(
					'our_rep'=>isset($value['our_rep'])?$value['our_rep']:'',
					'name'=>isset($value['name'])?$value['name']:'',
					'type'=>isset($value['type'])?$value['type']:'',
					'priority'=>isset($value['priority'])?$value['priority']:'',

					'work_start'=>isset($value['work_start'])?$value['work_start']:'',
					'work_end'=>isset($value['work_end'])?$value['work_end']:'',
					'status'=>isset($value['status'])?$value['status']:'',
					'noteactivity'=>$arr_noteactivity
				);
			}

		}

		foreach($arr_rep as $key=>$value){

			$arr_job=$this->Job->select_one(array('no'=>$key));
			$v_job_name=isset($arr_job['name'])?$arr_job['name']:'';

			$html.='<table cellpadding="3" cellspacing="0" class="maintb">';
			$html .='<tr><td></td></tr>';
			$html .='<tr>';
			$html.='<td align="left" width="100%" class="top" >';
			$html .='Job'.' : '.$key.' - '.$v_job_name;
			$html.='</td>';
			$html.='</tr>';
			$html.='</table>';
			foreach($value as $key1=>$value1){


				$arr_contact=$this->Contact->select_one(array('_id'=>new MongoId($key1)));


				$html .= ' <table cellpadding="4" cellspacing="0" class="tab_nd">';



				$html .= ' <tr class="border_2">
				<td width="10%" class="">';





				$html .= '</td>
			<td width="10%" class="top border_btom border_left border_right" align="center">';


				if(isset($arr_contact['first_name']))
					$html .= $arr_contact['first_name'];
				if(isset($arr_contact['last_name']))
					$html .= " ".$arr_contact['last_name'];


				$html.='</td></tr>

				</table>';
				$k=0;
				foreach($value1 as $key2=>$value2){
					if($k%2==0)
						$html .= ' <table cellpadding="4" cellspacing="0" class="tab_nd">';
					else
						$html .= '<table cellpadding="4" cellspacing="0" class="tab_nd2">';

					$html .= ' <tr class="border_2"><td width="10%" class="">';


					$html .= '</td><td width="10%" class="" align="center">';

					$html .= '</td><td width="20%" class="top border_btom border_left" align="left">';


					if(isset($value2['name']))
						$html .= $value2['name'];


					$html .= '</td><td width="10%" class="top border_btom border_left" align="left">';


					if(isset($value2['type']))
						$html .= $value2['type'];


					$html .= '</td><td width="10%" class="top border_btom border_left" align="left">';


					if(isset($value2['priority']))
						$html .= $value2['priority'];


					$html .= '</td><td width="10%" class="top border_btom border_left" align="center">';


					if(isset($value2['work_start'])&&is_object($value2['work_start']))
						$html .= $this->Task->format_date($value2['work_start']->sec,false);


					$html .= '</td><td width="10%" class="top border_btom border_left" align="center">';


					if(isset($value2['work_end'])&&is_object($value2['work_end']))
						$html .= $this->Task->format_date($value2['work_end']->sec,false);


					$html .= '</td><td width="10%" class="top border_btom border_left" align="left">';


					if(isset($value2['status']))
						$html .= $value2['status'];


					$html .= '</td><td width="10%" class="top border_btom border_left border_right" align="center">';

					if(isset($value2['work_end'])){
						if( $value2['work_end']->sec < strtotime('now')){
							$html .= '<span class="Late">X</span>';
						}
					}

					$html.='
								</td>
							</tr>

						</table>
					';


					if(count($value2['noteactivity'])>0){
						$html .= ' <table cellpadding="4" cellspacing="0" class="tab_nd">';

						$html .= ' <tr class="border_2"><td width="10%" class="">';



						$html .='</td>
			<td align="center" width="10%" class="">
				';

						$html .='</td>
			<td align="center" width="10%" class="top border_btom border_left border_right">
				';

						$html.='Detail';

						$html.='
			</td>';



						$html.='</tr> </table>';



						$html.='<table cellpadding="4" cellspacing="0" class="maintb">';

						$html.='<tr class="border_2"><td align="center" width="10%" class=""></td>

					<td align="center" width="10%" class=""></td>
					<td align="center" width="10%" class="top">Type</td>
					<td align="center" width="10%" class="top">Date</td>
					<td align="center" width="10%" class="top">By</td>
					<td align="center" width="50%" class="top">Details</td>';

						$html.='</tr> </table>';

						$html.='<table cellpadding="4" cellspacing="0" class="tab_nd">';

						foreach($value2['noteactivity'] as $key3=>$value3){
							$html.='<tr class="border_2"><td align="center" width="10%" ></td>

							<td align="center" width="10%" class="" >';



							$html.='
							</td>
							<td align="center" width="10%" class="top border_btom border_left border_right">';

							if(isset($value3['type']))
								$html.=$value3['type'];

							$html.='
							</td>
							<td align="center" width="10%" class="top border_btom border_left border_right">';

							if(isset($value3['date_modified'])&&is_object($value3['date_modified']))
								$html .= $this->Task->format_date($value3['date_modified']->sec,false);

							$html.='</td>
							<td align="center" width="10%" class="top border_btom border_left border_right">';

							if(isset($arr_contact['first_name']))
								$html .= $arr_contact['first_name'];
							if(isset($arr_contact['last_name']))
								$html .= " ".$arr_contact['last_name'];

							$html.='</td>
							<td align="left" width="50%" class="top border_btom border_left border_right">';

							if(isset($value3['content']))
								$html.=$value3['content'];

							$html.='</td>

							</tr>';

						}


						$html.='<tr class="border_2"><td align="center" width="10%" class=""></td></tr>';


						$html.='</table>';
					}
					else
					{

						$html.='<table cellpadding="4" cellspacing="0" class=""><tr class="border_2"><td align="center" width="10%" class=""></td></tr></table>';

					}

					$k++;
					$i++;
				}
				$html.='<table cellpadding="4" cellspacing="0" class="tab_nd"><tr class="border_2"><td width="10%" class="" align="center"></td></tr></table>';


			}

		}

//pr($arr_rep);
//die;

		$html_new = $html;

		// =================================================== tao file PDF ==============================================//
		include(APP.'Vendor'.DS.'nguyenpdf.php');

		$pdf = new XTCPDF();
		date_default_timezone_set('UTC');
		$pdf->today=date("g:i a, j F, Y");
		$textfont = 'freesans'; // looks better, finer, and more condensed than 'dejavusans'

		$pdf->SetCreator(PDF_CREATOR);
		$pdf->SetAuthor('Anvy Digital');
		$pdf->SetTitle('Anvy Digital Company');
		$pdf->SetSubject('Company');
		$pdf->SetKeywords('Company, PDF');

// set default header data
		$pdf->setPrintHeader(true);
		$pdf->setPrintFooter(true);

// set default monospaced font
		$pdf->SetDefaultMonospacedFont(2);

// set margins
		$pdf->SetMargins(10, 10, 10);
		$pdf->file1 = 'img'.DS.'null.png';
		$pdf->file3 = 'img'.DS.'bar_975x23.png';

		$pdf->file2_left=198;
		$pdf->file2='img'.DS.'null.png';


		$pdf->bar_top_left=198;
		$pdf->bar_top_top=23;
		$pdf->bar_top_content='--------------------------------------------------------------------------';

		$pdf->hidden_left=258;
		$pdf->hidden_top=19;
		$pdf->hidden_content='(with main task)';

		$pdf->bar_big_content='------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------';

		$pdf->bar_words_content='                            Responsible       Task                                               Type                   Priority                   Start date         Finish date        Status                         Late';
//		$pdf->bar_mid_content='         |                                                    |                               |                         |                          |              |';
		$pdf->bar_mid_content='';

		$pdf->printedat_left=222;
		$pdf->printedat_top=28;
		$pdf->time_left=240;
		$pdf->time_top=28;

		$pdf->SetHeaderMargin(PDF_MARGIN_HEADER);
		$pdf->SetFooterMargin(PDF_MARGIN_FOOTER);

// set auto page breaks
		$pdf->SetAutoPageBreak(TRUE, 30);

// set image scale factor
		$pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);
		if (@file_exists(dirname(__FILE__).DS.'lang'.DS.'eng.php')) {
			require_once(dirname(__FILE__).DS.'lang'.DS.'eng.php');
			$pdf->setLanguageArray($l);
		}

// ---------------------------------------------------------
// set font
		$pdf->SetFont($textfont, '', 9);
		$html = '
			<table cellpadding="2" cellspacing="0" style="width:100%; margin: 0px auto">
				<tr>
					<td width="40%" valign="top" style="color:#1f1f1f;">
						<img src="img/logo_anvy.png" alt="" />
						<div style="margin-bottom:1px; margin-top:4px;">
							<br>
							<br>
						</div>
						<div></div>
					</td>
					<td width="10%">&nbsp;</td>

					<td width="50%" valign="top" align="right">
						<div style=" text-align:right; font-size:20px; font-weight:bold; color: #919295;width:30%;"><span style="color:#b32017">T</span>ask <span style="color:#b32017">R</span>eport - <span style="color:#b32017">B</span>y <span style="color:#b32017">J</span>ob / <span style="color:#b32017">R</span>esponsible<br><br></div>
						<br>
					</td>
				</tr>
			</table>

				';
// add a page
		$pdf->AddPage('L', 'A4');
		$pdf->SetMargins(10, 12, 10);

		$pdf->file1 = 'img'.DS.'null.png';
		$pdf->file2 = 'img'.DS.'null.png';
		$pdf->file3 = 'img'.DS.'null.png';
		$pdf->file4 = 'img'.DS.'null.png';
		$pdf->file5 = 'img'.DS.'null.png';
		$pdf->file6 = 'img'.DS.'null.png';
		$pdf->file7 = 'img'.DS.'null.png';

		$pdf->file3_top=10;
		$pdf->address_1='';
		$pdf->address_2='';
		$pdf->bar_words_top=11;
		$pdf->bar_mid_top=10.6;
		$pdf->hidden_content='';
		$pdf->bar_top_content='';
		$pdf->today='';
		$pdf->print='';
		$pdf->bar_big_content='';
		$html.='
			<style>
				table{
					font-size: 12px;
					font-family: arial;
				}
				td.first{
					border-left:1px solid #e5e4e3;
				}
				td.end{
					border-right:1px solid #e5e4e3;
				}
				td.top{
					color:#fff;
					font-weight:bold;
					background-color:#911b12;
					border-top:1px solid #e5e4e3;
				}
				td.bottom{
					border-bottom:1px solid #e5e4e3;
				}
				.option{
					color: #3d3d3d;
					font-weight:bold;
					font-size:18px;
					text-align: center;
					width:100%;
				}
				.border_left{
					border-left:1px solid #A84C45;
				}
				.border_1{
					border-bottom:1px solid #911b12;
				}

			</style>
			<style>
					table.tab_nd{
						font-size: 12px;
						font-family: arial;
					}
					table.tab_nd td.first{
						border-left:1px solid #e5e4e3;
					}
					table.tab_nd td.end{
						border-right:1px solid #e5e4e3;
					}
					table.tab_nd td.top{
						background-color:#FDFBF9;
						border-top:1px solid #e5e4e3;
						font-weight: normal;
						color: #3E3D3D;
					}
					table.tab_nd .border_2{
						border-bottom:1px solid red;
					}
					table.tab_nd .border_left{
						border-left:1px solid #E5E4E3;
						border-bottom:1px solid #E5E4E3;
					}
					table.tab_nd .border_right{
						border-right:1px solid #E5E4E3;
						border-bottom:1px solid #E5E4E3;
					}
					table.tab_nd .border_btom{
						border-bottom:1px solid #E5E4E3;
					}

				</style>
				<style>
						table.tab_nd2{
							font-size: 12px;
							font-family: arial;
						}
						table.tab_nd2 td.first{
							border-left:1px solid #e5e4e3;
						}
						table.tab_nd2 td.end{
							border-right:1px solid #e5e4e3;
						}
						table.tab_nd2 td.top{
							background-color:#EDEDED;
							border-top:1px solid #e5e4e3;
							font-weight: normal;
							color: #3E3D3D;
						}
						table.tab_nd2 .border_2{
							border-bottom:1px solid red;
						}
						table.tab_nd2 .border_left{
							border-left:1px solid #E5E4E3;
							border-bottom:1px solid #E5E4E3;
						}
						table.tab_nd2 .border_right{
						border-right:1px solid #E5E4E3;
						border-bottom:1px solid #E5E4E3;
						}
						table.tab_nd2 .border_btom{
							border-bottom:1px solid #E5E4E3;
						}
						.size_font{
							font-size: 12px !important;
						}

					</style>
		';
		$html.=$html_new;
		$html .= '

	<table cellpadding="3" cellspacing="0" class="tab_nd2">
		<tr class="border_2">
			<td width="80%" class="first top border_btom size_font">
				&nbsp;';

		$html .= $i;

		$html .=' records listed
			</td>
			<td width="20%" class="end top border_btom">
				&nbsp;
			</td>
		</tr>
	</table>
	<div style=" clear:both; color: #c9c9c9;"><br />
--------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------
	</div><br />
	';
		$pdf->writeHTML($html, true, false, true, true, '');
		$pdf->Output(APP. 'webroot'.DS. 'upload'.DS .$filename.'.pdf', 'F');
		$this->redirect('/upload/'. $filename .'.pdf');
		die;
	}
	function report_by_job_responsible() {
		$this->layout = 'pdf';

		$date_now = date('Ymd');
		$time=time();
		$filename = 'TA'.$date_now.$time;
		$this->selectModel('Task');
		$sort_field = '_id';
		$sort_type = 1;
		if( isset($_POST['sort']) && strlen($_POST['sort']['field']) > 0 ){
			if( $_POST['sort']['type'] == 'desc' ){
				$sort_type = -1;
			}
			$sort_field = $_POST['sort']['field'];
			$this->Session->write('tasks_lists_search_sort', array($sort_field, $sort_type));

		}elseif( $this->Session->check('tasks_lists_search_sort') ){
			$session_sort = $this->Session->read('tasks_lists_search_sort');
			$sort_field = $session_sort[0];
			$sort_type = $session_sort[1];
		}
		$arr_order = array($sort_field => $sort_type);
		$cond = array();
		if( $this->Session->check('tasks_entry_search_cond') ){
			$cond = $this->Session->read('tasks_entry_search_cond');
		}
		$arr_pur = $this->Task->select_all(array(
			'arr_where' => $cond,
			'arr_order' => $arr_order,
			'limit' => LIMIT_PRINT_PDF
		));

		$html='';
		$this->selectModel('Contact');
		$this->selectModel('Job');
		$i=0;

		$arr_rep=array();
		foreach($arr_pur as $key=>$value){
			if(isset($value['job_no'])&&$value['job_no']!=0){
				$arr_rep[(string)$value['job_no']][(string)$value['our_rep_id']][]=array(
//					'job_name'=>isset($value['job_name'])?$value['job_name']:'',
//					'our_rep'=>isset($value['our_rep'])?$value['our_rep']:'',

					'name'=>isset($value['name'])?$value['name']:'',
					'type'=>isset($value['type'])?$value['type']:'',
					'priority'=>isset($value['priority'])?$value['priority']:'',

					'work_start'=>isset($value['work_start'])?$value['work_start']:'',
					'work_end'=>isset($value['work_end'])?$value['work_end']:'',
					'status'=>isset($value['status'])?$value['status']:'',
				);
			}

		}

		foreach($arr_rep as $key=>$value){

			$arr_job=$this->Job->select_one(array('no'=>$key));
			$v_job_name=isset($arr_job['name'])?$arr_job['name']:'';

			$html.='<table cellpadding="3" cellspacing="0" class="maintb">';
			$html .='<tr><td></td></tr>';
			$html .='<tr>';
			$html.='<td align="left" width="100%" class="top" >';
			$html .='Job'.' : '.$key.' - '.$v_job_name;
			$html.='</td>';
			$html.='</tr>';
			$html.='</table>';
			foreach($value as $key1=>$value1){


				$arr_contact=$this->Contact->select_one(array('_id'=>new MongoId($key1)));


				$html .= ' <table cellpadding="4" cellspacing="0" class="tab_nd">';



				$html .= ' <tr class="border_2">
				<td width="10%" class="">';





				$html .= '</td>
			<td width="10%" class="top border_btom border_left border_right" align="center">';


				if(isset($arr_contact['first_name']))
					$html .= $arr_contact['first_name'];
				if(isset($arr_contact['last_name']))
					$html .= " ".$arr_contact['last_name'];


				$html.='</td></tr>

				</table>';
				$k=0;
				foreach($value1 as $key2=>$value2){
					if($k%2==0)
						$html .= ' <table cellpadding="4" cellspacing="0" class="tab_nd">';
					else
						$html .= '<table cellpadding="4" cellspacing="0" class="tab_nd2">';

					$html .= ' <tr class="border_2"><td width="10%" class="">';


					$html .= '</td><td width="10%" class="" align="center">';

					$html .= '</td><td width="20%" class="top border_btom border_left" align="left">';


					if(isset($value2['name']))
						$html .= $value2['name'];


					$html .= '</td><td width="10%" class="top border_btom border_left" align="left">';


					if(isset($value2['type']))
						$html .= $value2['type'];


					$html .= '</td><td width="10%" class="top border_btom border_left" align="left">';


					if(isset($value2['priority']))
						$html .= $value2['priority'];


					$html .= '</td><td width="10%" class="top border_btom border_left" align="center">';


					if(isset($value2['work_start'])&&is_object($value2['work_start']))
						$html .= $this->Task->format_date($value2['work_start']->sec,false);


					$html .= '</td><td width="10%" class="top border_btom border_left" align="center">';


					if(isset($value2['work_end'])&&is_object($value2['work_end']))
						$html .= $this->Task->format_date($value2['work_end']->sec,false);


					$html .= '</td><td width="10%" class="top border_btom border_left" align="left">';


					if(isset($value2['status']))
						$html .= $value2['status'];


					$html .= '</td><td width="10%" class="top border_btom border_left border_right" align="center">';

					if(isset($value2['work_end'])){
						if( $value2['work_end']->sec < strtotime('now')){
							$html .= '<span class="Late">X</span>';
						}
					}

					$html.='
								</td>
							</tr>

						</table>
					';

					$k++;
					$i++;
				}
				$html.='<table cellpadding="4" cellspacing="0" class="tab_nd"><tr class="border_2"><td width="10%" class="" align="center"></td></tr></table>';


			}

		}

//pr($arr_rep);
//die;

		$html_new = $html;

		// =================================================== tao file PDF ==============================================//
		include(APP.'Vendor'.DS.'nguyenpdf.php');

		$pdf = new XTCPDF();
		date_default_timezone_set('UTC');
		$pdf->today=date("g:i a, j F, Y");
		$textfont = 'freesans'; // looks better, finer, and more condensed than 'dejavusans'

		$pdf->SetCreator(PDF_CREATOR);
		$pdf->SetAuthor('Anvy Digital');
		$pdf->SetTitle('Anvy Digital Company');
		$pdf->SetSubject('Company');
		$pdf->SetKeywords('Company, PDF');

// set default header data
		$pdf->setPrintHeader(true);
		$pdf->setPrintFooter(true);

// set default monospaced font
		$pdf->SetDefaultMonospacedFont(2);

// set margins
		$pdf->SetMargins(10, 10, 10);
		$pdf->file1 = 'img'.DS.'null.png';
		$pdf->file3 = 'img'.DS.'bar_975x23.png';

		$pdf->file2_left=198;
		$pdf->file2='img'.DS.'null.png';


		$pdf->bar_top_left=198;
		$pdf->bar_top_top=23;
		$pdf->bar_top_content='--------------------------------------------------------------------------';

		$pdf->hidden_left=258;
		$pdf->hidden_top=19;
		$pdf->hidden_content='(with main task)';

		$pdf->bar_big_content='------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------';

		$pdf->bar_words_content='                            Responsible       Task                                               Type                   Priority                   Start date         Finish date        Status                         Late';
//		$pdf->bar_mid_content='         |                                                    |                               |                         |                          |              |';
		$pdf->bar_mid_content='';

		$pdf->printedat_left=222;
		$pdf->printedat_top=28;
		$pdf->time_left=240;
		$pdf->time_top=28;

		$pdf->SetHeaderMargin(PDF_MARGIN_HEADER);
		$pdf->SetFooterMargin(PDF_MARGIN_FOOTER);

// set auto page breaks
		$pdf->SetAutoPageBreak(TRUE, 30);

// set image scale factor
		$pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);
		if (@file_exists(dirname(__FILE__).DS.'lang'.DS.'eng.php')) {
			require_once(dirname(__FILE__).DS.'lang'.DS.'eng.php');
			$pdf->setLanguageArray($l);
		}

// ---------------------------------------------------------
// set font
		$pdf->SetFont($textfont, '', 9);
		$html = '
			<table cellpadding="2" cellspacing="0" style="width:100%; margin: 0px auto">
				<tr>
					<td width="40%" valign="top" style="color:#1f1f1f;">
						<img src="img/logo_anvy.png" alt="" />
						<div style="margin-bottom:1px; margin-top:4px;">
							<br>
							<br>
						</div>
						<div></div>
					</td>
					<td width="10%">&nbsp;</td>

					<td width="50%" valign="top" align="right">
						<div style=" text-align:right; font-size:20px; font-weight:bold; color: #919295;width:30%;"><span style="color:#b32017">T</span>ask <span style="color:#b32017">R</span>eport - <span style="color:#b32017">B</span>y <span style="color:#b32017">J</span>ob / <span style="color:#b32017">R</span>esponsible<br><br></div>
						<br>
					</td>
				</tr>
			</table>

				';
// add a page
		$pdf->AddPage('L', 'A4');
		$pdf->SetMargins(10, 12, 10);

		$pdf->file1 = 'img'.DS.'null.png';
		$pdf->file2 = 'img'.DS.'null.png';
		$pdf->file3 = 'img'.DS.'null.png';
		$pdf->file4 = 'img'.DS.'null.png';
		$pdf->file5 = 'img'.DS.'null.png';
		$pdf->file6 = 'img'.DS.'null.png';
		$pdf->file7 = 'img'.DS.'null.png';

		$pdf->file3_top=10;
		$pdf->address_1='';
		$pdf->address_2='';
		$pdf->bar_words_top=11;
		$pdf->bar_mid_top=10.6;
		$pdf->hidden_content='';
		$pdf->bar_top_content='';
		$pdf->today='';
		$pdf->print='';
		$pdf->bar_big_content='';
		$html.='
			<style>
				table{
					font-size: 12px;
					font-family: arial;
				}
				td.first{
					border-left:1px solid #e5e4e3;
				}
				td.end{
					border-right:1px solid #e5e4e3;
				}
				td.top{
					color:#fff;
					font-weight:bold;
					background-color:#911b12;
					border-top:1px solid #e5e4e3;
				}
				td.bottom{
					border-bottom:1px solid #e5e4e3;
				}
				.option{
					color: #3d3d3d;
					font-weight:bold;
					font-size:18px;
					text-align: center;
					width:100%;
				}
				.border_left{
					border-left:1px solid #A84C45;
				}
				.border_1{
					border-bottom:1px solid #911b12;
				}

			</style>
			<style>
					table.tab_nd{
						font-size: 12px;
						font-family: arial;
					}
					table.tab_nd td.first{
						border-left:1px solid #e5e4e3;
					}
					table.tab_nd td.end{
						border-right:1px solid #e5e4e3;
					}
					table.tab_nd td.top{
						background-color:#FDFBF9;
						border-top:1px solid #e5e4e3;
						font-weight: normal;
						color: #3E3D3D;
					}
					table.tab_nd .border_2{
						border-bottom:1px solid red;
					}
					table.tab_nd .border_left{
						border-left:1px solid #E5E4E3;
						border-bottom:1px solid #E5E4E3;
					}
					table.tab_nd .border_right{
						border-right:1px solid #E5E4E3;
						border-bottom:1px solid #E5E4E3;
					}
					table.tab_nd .border_btom{
						border-bottom:1px solid #E5E4E3;
					}

				</style>
				<style>
						table.tab_nd2{
							font-size: 12px;
							font-family: arial;
						}
						table.tab_nd2 td.first{
							border-left:1px solid #e5e4e3;
						}
						table.tab_nd2 td.end{
							border-right:1px solid #e5e4e3;
						}
						table.tab_nd2 td.top{
							background-color:#EDEDED;
							border-top:1px solid #e5e4e3;
							font-weight: normal;
							color: #3E3D3D;
						}
						table.tab_nd2 .border_2{
							border-bottom:1px solid red;
						}
						table.tab_nd2 .border_left{
							border-left:1px solid #E5E4E3;
							border-bottom:1px solid #E5E4E3;
						}
						table.tab_nd2 .border_right{
						border-right:1px solid #E5E4E3;
						border-bottom:1px solid #E5E4E3;
						}
						table.tab_nd2 .border_btom{
							border-bottom:1px solid #E5E4E3;
						}
						.size_font{
							font-size: 12px !important;
						}

					</style>
		';
		$html.=$html_new;
		$html .= '

	<table cellpadding="3" cellspacing="0" class="tab_nd2">
		<tr class="border_2">
			<td width="80%" class="first top border_btom size_font">
				&nbsp;';

		$html .= $i;

		$html .=' records listed
			</td>
			<td width="20%" class="end top border_btom">
				&nbsp;
			</td>
		</tr>
	</table>
	<div style=" clear:both; color: #c9c9c9;"><br />
--------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------
	</div><br />
	';
		$pdf->writeHTML($html, true, false, true, true, '');
		$pdf->Output(APP. 'webroot'.DS. 'upload'.DS .$filename.'.pdf', 'F');
		$this->redirect('/upload/'. $filename .'.pdf');
		die;
	}
	function save_data_for_non_model() {
		if (isset($_POST['fieldname']))
			$field = $_POST['fieldname'];
		if (isset($_POST['values']))
			$value = $_POST['values'];
		if (isset($_POST['ids']))
			$ids = $_POST['ids'];

//		echo $field."_".$value."_".$ids;die;
		$arr_save = array();
		$this->selectModel('Task');
		if ($field != '' && $value != ''){
			$arr_save['_id'] = new MongoId($ids);

			$arr_save[$field]=$value;



			if ($this->Task->save($arr_save)) {

				echo 'ok';die;

			}
			else
			{
				echo 'Can not save.';die;
			}

		}
		die;
	}

	public function view_minilist(){
		if(!isset($_GET['print_pdf'])){
			$arr_where = array();
			$this->selectModel('Task');
			$tasks = $this->Task->select_all(array(
											'arr_where' => $arr_where,
											'arr_field' => array('name','job_no','our_rep','type','priority','work_start','work_end','status'),
											'arr_order' => array('_id'=> 1),
											'limit'		=> 2000
											));
			$arr_data= array();
			if($tasks->count() > 0){
				$i=0;
				$html='';
				$current_date = strtotime(date("m/d/Y"));
				foreach($tasks as $key => $task){
					$html .= '<tr class="'.($i%2==0 ? 'bg_2' : 'bg_1').'">';
					$html .= '<td>'.(isset($task['name']) ? $task['name'] : '') .'</td>';
					$html .= '<td>'.(isset($task['job_no']) ? $task['job_no'] : '') .'</td>';
					$html .= '<td>'.(isset($task['our_rep']) ? $task['our_rep'] : '') .'</td>';
					$html .= '<td class="center_text">'.(isset($task['type']) ? $task['type'] : '') .'</td>';
					$html .= '<td>'.(isset($task['priority']) ? $task['priority'] : '') .'</td>';
					$html .= '<td>'.(isset($task['work_start']) ? $this->Task->format_date($task['work_start']->sec):'').'</td>';
					$html .= '<td>'.(isset($task['work_end']) ? $this->Task->format_date($task['work_start']->sec):'') .'</td>';
					$html .= '<td>'.(isset($task['status']) ? $task['status'] : '') .'</td>';
					$html .= '<td class="center_text bold_text">'.($current_date > (isset($task['work_end'])? $task['work_end']->sec: $current_date)?'X':'') .'</td>';
					$html .= '</tr>';
	                $i++;
				}
				$html .='<tr class="last">
	                        <td colspan="2" class="bold_text right_none">'.$i.' record(s) listed.</td>
	                        </tr>';
	            $arr_data['title'] = array('Task'=>'text-align:left','Job no','Responsible'=>'text-align:left','Type','Priority','Start date','Finish date','Status','Late');
	            $arr_data['content'] = $html;
	            $arr_data['report_name'] = 'Task Mini Listing ';
	            $arr_data['report_file_name']='TA_'.md5(time());
	            $arr_data['report_orientation'] = 'landscape';
			}
			Cache::write('task_minilist', $arr_data);
		}else {

    		$arr_data = Cache::read('task_minilist');
    		Cache::delete('task_minilist');
		}
		$this->render_pdf($arr_data);
	}

	function rebuild_tasks(){
		$this->selectModel('Task');
		$this->selectModel('Salesorder');
		$arr_tasks = $this->Task->select_all(array(
		                                     'arr_where' => array(
		                                                        'salesorder_id' => array(
		                                                                                '$exists' => true,
		                                                                                '$nin' => array(null,'')
		                                                                                ),
		                                                        'our_rep_type' => 'assets'
		                                                          ),
		                                     'arr_field' => array('salesorder_id','our_rep'),
		                                     'limit' => 99999
		                                     ));
		echo $arr_tasks->count().' record(s) found.<br />';
		$i = 0;
		foreach($arr_tasks as $task){
			if(!isset($task['salesorder_id']) || !is_object($task['salesorder_id'])) continue;
			$order = $this->Salesorder->select_one(array('_id' => $task['salesorder_id']),array('code','company_name'));
			$task['name'] = $order['code'].' - '.$order['company_name'].' - '.$task['our_rep'];
			$this->Task->rebuild_collection($task);
			$i++;
		}
		echo $i. ' record(s) fixed.';
		die;
	}

	function delete_empty()
	{
		$this->selectModel('Task');
		$this->selectModel('Salesorder');
		$tasks = $this->Task->select_all(array(
				'arr_where' => array(
						'_id' => array( '$gt' => new MongoId( str_pad(dechex(strtotime('2015-06-01 00:00:00')), 8, '0', STR_PAD_LEFT).'0000000000000000'  ) ),
					),
				'arr_field' => array('salesorder_id', 'no')
			));
		$i = 0;
		foreach($tasks as $task) {
			if( !is_object($task['salesorder_id']) ) continue;
			$count = $this->Salesorder->count(array('_id' => $task['salesorder_id'], 'deleted' => false));
			if( !$count ) {
				$i++;
				$this->Task->collection->remove(array('_id' => $task['_id']));
			}
		}
		echo $i.' task(s) removed.';
		die;
	}
}