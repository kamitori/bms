<?php
class TasksController extends MobileAppController {
	var $modelName = 'Task';
	var $name = 'Tasks';
	function beforeFilter() {
		parent::beforeFilter();
		if(!$this->request->is('ajax'))
			$this->set('arr_tabs', array(
			           			'general' => array('note_activity','contacts_related'),
			           			'expensive' => array('expenses_tasks'),
			           			'document'  => array('docs'),
			           			//'resouce' => array('details'),
			           			));
	}
	public function entry($id = '0', $num_position = -1) {
		$arr_tmp = $this->entry_init($id, $num_position, 'Task', 'tasks');
		$arr_tmp['work_start_header'] = $arr_tmp['work_start'];
		$arr_tmp['work_start_hour'] = (is_object($arr_tmp['work_start'])) ? date('H:i', $arr_tmp['work_start']->sec) : '';
		$arr_tmp['work_start'] = (is_object($arr_tmp['work_start'])) ? date('m/d/Y', $arr_tmp['work_start']->sec) : '';
		$arr_tmp['work_end_hour'] = (is_object($arr_tmp['work_end'])) ? date('H:i', $arr_tmp['work_end']->sec) : '';
		$arr_tmp['work_end'] = (is_object($arr_tmp['work_end'])) ? date('m/d/Y', $arr_tmp['work_end']->sec) : '';

		$this->selectModel('Setting');
		$arr_tasks_type = $this->Setting->select_option(array('setting_value' => 'tasks_type'), array('option'));
		$this->set('arr_tasks_type', $arr_tasks_type);
		if (isset($arr_tmp['type_id'])) {
			$arr_tmp['type'] = $arr_tmp['type_id'];
		}

		$arr_tasks_status = $this->Setting->select_option(array('setting_value' => 'tasks_status'), array('option'));
		$this->set('arr_tasks_status', $arr_tasks_status);
		if (isset($arr_tmp['status_id'])) {
			$arr_tmp['status'] = $arr_tmp['status_id'];
		}

		$this->set('arr_priority', $this->Setting->select_option(array('setting_value' => 'lists_priority'), array('option')));
		$this->set('arr_country', $this->Setting->select_option(array('setting_value' => 'lists_country'), array('option')));

		if (isset($arr_tmp['enquiry_id']) && is_object($arr_tmp['enquiry_id']) ) {
			$this->selectModel('Enquiry');
			$enquiry = $this->Enquiry->select_one(array('_id' => $arr_tmp['enquiry_id']), array('_id', 'no', 'company', 'company_name'));
			if( isset($enquiry['_id']) ){
				$arr_tmp['enquiry_no'] = $enquiry['no'];
				$arr_tmp['enquiry_name'] = isset($enquiry['company_name'])?$enquiry['company_name']:'';
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
		/*$this->show_footer_info($arr_tmp, $arr_contact_id);*/

		// Get info for subtask
	}
	function lists( $content_only = '' ) {
		$this->selectModel('Task');

		$limit = 20; $skip = 0;

		// dùng cho sort
		$sort_field = 'no';
		$sort_type = -1;
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
		if(isset($_POST['offset'])){
			$skip = $_POST['offset'];
		}
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
	function auto_save( $field = '' ) {
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

			if (isset($arr_save['status']))
				$arr_save['status_id'] = $arr_save['status'];
			if (isset($arr_save['type']))
				$arr_save['type_id'] = $arr_save['type'];

			if (strlen(trim($arr_save['salesorder_id'])) > 0)
			     $arr_save['salesorder_id'] = new MongoId($arr_save['salesorder_id']);

			if (strlen(trim($arr_save['purchaseorder_id'])) > 0)
				$arr_save['purchaseorder_id'] = new MongoId($arr_save['purchaseorder_id']);

			if( strlen($arr_save['name']) > 0 )
				$arr_save['name']{0} = strtoupper($arr_save['name']{0});

			$this->selectModel('Task');
			if ($this->Task->save($arr_save)) {
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
		$arr_save['_id'] = $id;
		$arr_save['deleted'] = true;
		$error = 0;
		if (!$error) {
			$this->selectModel('Task');
			if ($this->Task->save($arr_save)) {
				$this->Session->delete($this->name.'ViewId');
				if($this->request->is('ajax'))
					echo 'ok';
				else
					$this->redirect('/mobile/tasks/entry');
			} else {
				echo 'Error: ' . $this->Task->arr_errors_save[1];
			}
		}
		die;
	}

	public function add() {
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
			$this->redirect('/mobile/tasks/entry/' . $this->Task->mongo_id_after_save);
		die;
	}

	public function note_activity() {
		$task_id = $this->get_id();
		//$this->noteactivity($task_id);

		$this->selectModel('Noteactivity');
		$arr_noteactivity = $this->Noteactivity->select_all(array(
			'arr_where' => array(
				'module' => $this->modelName,
				'module_id' => new MongoId($task_id)
			),
			'arr_order' => array('_id' => 1)
		));
		$this->set('arr_noteactivity', $arr_noteactivity);
		$this->set('module_id', $task_id);
		$this->set(strtolower($this->modelName).'_id', $task_id);

		$this->selectModel('Contact');
		$this->set('model_contact', $this->Contact);

		//pr(iterator_to_array($arr_noteactivity));die;
	}
	function noteactivity_update($id) {
		$arr_save = array();
		$arr_save['_id'] = $id;
		$arr_save['content'] = $_POST['content'];
		$this->selectModel('Noteactivity');
		if ($this->Noteactivity->save($arr_save)) {
			echo 'ok';
		} else {
			echo 'Error: ' . $this->Noteactivity->arr_errors_save[1];
		}
		die;
	}
	function noteactivity_add() {
		$task_id = $this->get_id();

		$arr_save = array();
		$arr_save['type'] = 'Note';
		$arr_save['content'] = '';
		$arr_save['module'] = $this->modelName;
		$arr_save['module_id'] = new MongoId($task_id);

		$this->selectModel('Noteactivity');
		if ($this->Noteactivity->save($arr_save)) {
			//$this->noteactivity($task_id);
		} else {
			echo 'Error: ' . $this->Noteactivity->arr_errors_save[1];
		}
		die;
	}
	function noteactivity_delete($id) {
		$this->selectModel('Noteactivity');
		$arr_save = array();
		$arr_save['_id'] = $id;
		$arr_save['deleted'] = true;
		if ($this->Noteactivity->save($arr_save)) {
			echo 'ok';
		} else {
			echo 'Error: ' . $this->Noteactivity->arr_errors_save[1];
		}
		die;
	}
	function noteactivity($module_id) {
		$this->selectModel('Noteactivity');
		$arr_noteactivity = $this->Noteactivity->select_all(array(
			'arr_where' => array(
				'module' => $this->modelName,
				'module_id' => new MongoId($module_id)
			),
			'arr_order' => array('_id' => 1)
		));
		$this->set('arr_noteactivity', $arr_noteactivity);
		$this->set('module_id', $module_id);
		$this->set(strtolower($this->modelName).'_id', $module_id);

		$this->selectModel('Contact');
		$this->set('model_contact', $this->Contact);

		if ($this->request->is('ajax')) {
			if( $this->params->params['action'] == 'noteactivity_add' ){
				echo $this->render('../Elements/noteactivity');die;
			}else{
				$this->set('noteactivity', $this->render('../Elements/noteactivity'));
			}
		}
	}
	function contacts_related() {
		$task_id = $this->get_id();
		$this->selectModel('Task');
		$arr_contacts = $this->Task->select_one(array('_id' => new MongoId($task_id)), array('contacts', 'contacts_default_key'));
		$arr_contacts_related = array();
		foreach ($arr_contacts['contacts'] as $key => $value) {
			if ($value['deleted'] != true || $value['deleted'] != 1 )
				$arr_contacts_related[$key] = $value;
		}
		//pr($arr_contacts_related);die;
		$this->set('arr_contacts_related', $arr_contacts_related);
		$this->set('contacts_default_key',$arr_contacts['contacts_default_key']);
		$this->set('task_id',$task_id);

	}
	public function expenses_tasks() {
		$task_id = $this->get_id();
		$this->selectModel('Task');
		$arr_expense = array();
		$arr_expense_task = $this->Task->select_one(array('_id' => new MongoId($task_id)), array('expense'));
		if (!isset($arr_expense_task['expense']) || !is_array($arr_expense_task['expense']))  $arr_expense_task['expense'] = array();
		foreach ($arr_expense_task['expense'] as $key => $value) {
			if ($value['deleted']!=true)
				$arr_expense[$key] = $value;
		}
		//pr($arr_expense);die;
		$this->set('arr_expense',$arr_expense);
		$this->set('task_id',$task_id);
	}
	function expense_auto_save() {
		$id = $this->get_id();
		$field = $_POST['field'];
		$value = $_POST['value'];
		$key = $_POST['key'];
		/*$arr_save = array();
		$arr_save['_id'] = $id;
		$arr_save['expense'][$key][$field] = $value;*/
		
		$this->selectModel('Task');
		$is_save = $this->Task->collection->update(
			array('_id' => new MongoId($id)), array('$set' => array(
				'expense.'.$key.'.'.$field => $value,
			))
		);
		if ($is_save) {
			echo 'ok';
		} else {
			echo 'Error: ' . $this->Task->arr_errors_save[1];
		}
		die;
	}
	function expense_delete($task_id,$key) {
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

		die;
	}

}