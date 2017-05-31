<?php
class EnquiriesController extends MobileAppController {
	var $modelName = 'Enquiry';
	var $name = 'Enquiries';
	function beforeFilter() {
		parent::beforeFilter();
		if(!$this->request->is('ajax'))
			$this->set('arr_tabs', array(
			           			'general' => array('requirements','keywords','communications'),
			           			'quotes' => array('quotes'),
			           			'task' => array('tasks'),
			           			'document'  => array('docs'),
			           			'other'  => array('other'),
			           			));
	}

	public function entry($id = '0', $num_position = -1) {
		$arr_tmp = $this->entry_init($id, $num_position, 'Enquiry', 'enquiries');
		$arr_tmp['date'] = (is_object($arr_tmp['date'])) ? date('m/d/Y', $arr_tmp['date']->sec) : '';
		//pr($arr_tmp);die;

		$this->selectModel('Setting');
		$arr_enquiries_type = $this->Setting->select_option(array('setting_value' => 'enquiries_type'), array('option'));
		$this->set('arr_enquiries_type', $arr_enquiries_type);


		$arr_enquiries_title = $this->Setting->select_option(array('setting_value' => 'contacts_title'), array('option'));
		$this->set('arr_enquiries_title', $arr_enquiries_title);

		$arr_enquiries_status = $this->Setting->select_option(array('setting_value' => 'enquiry_status'), array('option'));
		$this->set('arr_enquiries_status', $arr_enquiries_status);

		$arr_enquiries_rating = $this->Setting->select_option(array('setting_value' => 'enquiry_rating'), array('option'));
		$this->set('arr_enquiries_rating', $arr_enquiries_rating);

		$arr_enquiries_position = $this->Setting->select_option(array('setting_value' => 'z_enquiry_position'), array('option'));
		$this->set('arr_enquiries_position', $arr_enquiries_position);

		$arr_enquiries_department = $this->Setting->select_option(array('setting_value' => 'z_enquiry_department'), array('option'));
		$this->set('arr_enquiries_department', $arr_enquiries_department);

		$arr_enquiries_category = $this->Setting->select_option(array('setting_value' => 'enquiry_category'), array('option'));
		$this->set('arr_enquiries_category', $arr_enquiries_category);

		$arr_enquiries_referred = $this->Setting->select_option(array('setting_value' => 'enquiry_referred_by'), array('option'));
		$this->set('arr_enquiries_referred', $arr_enquiries_referred);

		$this->selectModel('Country');
		$options = array();
		$options['countries'] = $this->Country->get_countries();
		$this->selectModel('Province');
		$options['provinces'] = $this->Province->get_all_provinces();
		$this->set('options',$options);



		if (isset($arr_tmp['type_id'])) {
			$arr_tmp['type'] = $arr_tmp['type_id'];
		}


		$this->set('arr_priority', $this->Setting->select_option(array('setting_value' => 'lists_priority'), array('option')));
		$this->set('arr_country', $this->Setting->select_option(array('setting_value' => 'lists_country'), array('option')));


		$arr_tmp1['Enquiry'] = $arr_tmp;
		$this->data = $arr_tmp1;
		$arr_enquiry_id = array();
		if (isset($arr_tmp['our_rep_id']))
			$arr_enquiry_id[] = $arr_tmp['our_rep_id'];
	}

	function auto_save() {
		if (!empty($this->data)) {
			$id = $this->get_id();
			$arr_return = array();
			$arr_post_data = $this->data['Enquiry'];
			$arr_save = $arr_post_data;
			$arr_save['_id'] = new MongoId($id);
			$this->selectModel('Enquiry');

			if(isset($arr_save['no'])){
				if(!is_numeric($arr_save['no'])){
					echo json_encode(array('status'=>'error','message'=>'must_be_numberic'));
					die;
				}
				$arr_tmp = $this->Enquiry->select_one(array('no' => (int) $arr_save['no'], '_id' => array('$ne' => new MongoId($id))));
				if (isset($arr_tmp['no'])) {
					echo json_encode(array('status'=>'error','message'=>'ref_no_existed'));
					die;
				}
			}

			$arr_tmp = $this->Enquiry->select_one(array('_id' =>  new MongoId($id)),array('status','company_id','custom_po_no'));

			if(isset($arr_save['custom_po_no']) && isset($arr_tmp['custom_po_no']) && $arr_save['custom_po_no']!= $arr_tmp['custom_po_no']){
				foreach(array('Salesinvoice','Salesorder','Quotation') as $model){
					$this->selectModel($model);
					$this->$model->collection->update(
		                                           array('enquiry_id'=>new MongoId($arr_save['_id'])),
		                                           array('$set'=>array(
		                                                 'customer_po_no'=>$arr_save['custom_po_no']
		                                                 )
		                                           ),
		                                           array('multiple'=>true)
	                                           );
				}
			}

			if (isset($arr_save['status']))
				$arr_save['status_id'] = $arr_save['status'];
			if (isset($arr_save['type']))
				$arr_save['type_id'] = $arr_save['type'];


			$date = $this->Common->strtotime($arr_save['date'] . ' 00:00:00');
			$arr_save['date'] = new MongoDate($date);


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

			$this->selectModel('Enquiry');
			if ($this->Enquiry->save($arr_save))
				$arr_return['status'] = 'ok';
			else
				$arr_return = array('status'=>'error','message'=>'Error: ' . $this->Enquiry->arr_errors_save[1]);
			echo json_encode($arr_return);
		}
		die;
	}

	function lists( $content_only = '' ) {
		$this->selectModel('Enquiry');
		$limit = 20; $skip = 0;
		// dùng cho sort
		$sort_field = 'no';
		$sort_type = -1;
		if( isset($_POST['sort']) && strlen($_POST['sort']['field']) > 0 ){
			if( $_POST['sort']['type'] == 'desc' ){
				$sort_type = -1;
			}
			$sort_field = $_POST['sort']['field'];
			$this->Session->write('enquiries_lists_search_sort', array($sort_field, $sort_type));

		}elseif( $this->Session->check('enquiries_lists_search_sort') ){
			$session_sort = $this->Session->read('enquiries_lists_search_sort');
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
		if( $this->Session->check('enquiries_entry_search_cond') ){
			$cond = $this->Session->read('enquiries_entry_search_cond');
		}

		// dùng cho phân trang
		if(isset($_POST['offset'])){
			$skip = $_POST['offset'];
		}
		// query
		$arr_enquiries = $this->Enquiry->select_all(array(
			'arr_where' => $cond,
			'arr_order' => $arr_order,
			'limit' => $limit,
			'skip' => $skip
		));
		$this->set('arr_enquiries', $arr_enquiries);
		$this->selectModel('Setting');
		$this->set('arr_enquiries_type', $this->Setting->select_option(array('setting_value' => 'enquiries_type'), array('option')));

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
			$this->selectModel('Enquiry');
			if ($this->Enquiry->save($arr_save)) {
				$this->Session->delete($this->name.'ViewId');
				if($this->request->is('ajax'))
					echo 'ok';
				else
					$this->redirect('/mobile/enquiries/entry');
			} else {
				echo 'Error: ' . $this->Enquiry->arr_errors_save[1];
			}
		}
		die;
	}

	public function add() {
		$this->selectModel('Enquiry');
		$arr_save = array();

		$this->Enquiry->arr_default_before_save = $arr_save;
		if ($this->Enquiry->add())
			$this->redirect('/mobile/enquiries/entry/' . $this->Enquiry->mongo_id_after_save);
		die;
	}

    public function tasks(){
    	if(isset($_POST['add']))
    		return $this->tasks_add();
    	$offset = 0;
    	if(isset($_POST['offset']))
    		$offset = $_POST['offset'];
    	$id = $this->get_id();
    	$this->selectModel('Task');
    	$arr_tasks = $this->Task->select_all(array(
    	                        'arr_where' => array('enquiry_id' => new MongoId($id),),
    	                        'arr_field' => array('no','name','type','our_rep','work_start','work_end','status'),
    	                        'arr_order' => array('work_end' => -1),
    	                        'skip' => $offset,
    	                        'limit' => 10
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
			$this->set('id',$id);
        }
    }

    function tasks_add() {
    	$id = $this->get_id();
        $this->selectModel('Enquiry');
        $arr_enquiry = $this->Enquiry->select_one(array('_id' => new MongoId($id)),array('our_rep_id', 'name', 'contact_default_id'));
        $arr_save = array();
        $arr_save['enquiry_name'] = $arr_enquiry['name'];
        $arr_save['enquiry_id'] = $arr_enquiry['_id'];
        $arr_save['our_rep_id'] = $arr_save['our_rep'] = '';
        if (isset($arr_enquiry['our_rep_id']) && is_object($arr_enquiry['our_rep_id']) ){
            $arr_save['our_rep_id'] = $arr_enquiry['our_rep_id'];
            $arr_save['our_rep'] = isset($arr_enquiry['our_rep']) ? $arr_enquiry['our_rep'] : '';
        }
        $arr_save['contact_id'] = $arr_save['contact_name'] = '';
        if (isset($arr_enquiry['contact_default_id']) && is_object($arr_enquiry['contact_default_id'])) {
            $this->selectModel('Contact');
            $arr_contact = $this->Contact->select_one(array('_id' => $arr_enquiry['contact_default_id']),array('first_name','first_name'));
            $arr_save['contact_id'] = $arr_contact['_id'];
            $arr_save['contact_name'] = $arr_contact['first_name'] . ' ' . $arr_contact['first_name'];
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

    function quotes(){
    	if(isset($_POST['add']))
    	   return $this->quotes_add();
    	$offset = 0;
    	if(isset($_POST['offset']))
    		$offset = $_POST['offset'];
    	$id = $this->get_id();
        $this->selectModel('Quotation');
        $arr_quotes = $this->Quotation->select_all(array(
                                                'arr_where' => array('enquiry_id'=>new MongoId($id)),
                                                'arr_order' => array('quotation_date' => -1),
                                                'arr_field' => array('code','quotation_type','quotation_date','payment_due_date','quotation_status','our_rep','our_csr','sum_sub_total','heading'),
                                                'skip' => $offset,
                                                'limit'  => 10
                                                ));
        $arr_data = array('data'=>array());
		foreach($arr_quotes as $quote){
			$quote['_id'] = (string)$quote['_id'];
			$quote['name'] = isset($quote['name']) ? $quote['name'] : '';
			$quote['our_rep'] = isset($quote['our_rep']) ? $quote['our_rep'] : '';
			$quote['our_csr'] = isset($quote['our_csr']) ? $quote['our_csr'] : '';
			$quote['quotation_date'] = date('d M, Y', $quote['quotation_date']->sec);
			$quote['payment_due_date'] = date('d M, Y', $quote['payment_due_date']->sec);
			$quote['heading'] = isset($quote['heading']) ? $quote['heading'] : '';
			$quote['sum_sub_total'] = number_format((float)$quote['sum_sub_total'],2);
			$arr_data['data'][] = $quote;
		}
        if($this->request->is('ajax')){
        	if($arr_quotes->count(true) == 0){
        		echo json_encode(array('empty'=>true));
        		die;
        	}
			echo json_encode($arr_data);
			die;
        } else {
	        $this->set('arr_quotes',$arr_data['data']);
        	$arr_data['field'] = array(
	                           'code'=>array(
	                                       'label' => 'Ref no',
	                                       'type' => '',),
	                           'quotation_type'=>array(
	                                       'label' => 'Type',
	                                       'type' => '',
	                                       ),
	                           'quotation_date'=>array(
	                                       'label' => 'Date',
	                                       'type' => ''),
	                           'payment_due_date'=>array(
	                                       'label' => 'Due date',
	                                       'type' => ''),
	                           'quotation_status'=>array(
	                                       'label' => 'Status',
	                                       'type' => ''),
	                           'our_rep'=>array(
	                                       'label' => 'Our rep',
	                                       'type' => '',
	                                       ),
	                           'status'=>array(
	                                       'label' => 'Our CRS',
	                                       'type' => '',
	                                       ),
	                           'sum_sub_total'=>array(
	                                       'label' => 'Total (bf.Tax)',
	                                       'type' => '',
	                                       ),
	                           'heading'=>array(
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
        }
    }
    function quotes_add() {
    	$enquiry_id = $this->get_id();
        $this->selectModel('Enquiry');
        $arr_enquiry = $this->Enquiry->select_one(array('_id' => new MongoId($enquiry_id)),array('name','email','phone','contact_default_id','addresses_default_key','addresses','our_csr_id','our_csr','our_rep_id','our_rep'));
        $arr_save = $this->get_enquiry_info($arr_enquiry);
        $this->selectModel('Quotation');
        $arr_save['code'] = $this->Quotation->get_auto_code('code');
        $arr_save['quotation_status'] = 'In progress';
        $arr_save['quotation_date'] = new MongoDate(strtotime(date('Y-m-d')));
        $arr_save['payment_due_date'] = $arr_save['quotation_date'];
        $this->Quotation->arr_default_before_save = $arr_save;
        if ($this->Quotation->add()) {
           echo M_URL.'/quotations/entry/'. $this->Quotation->mongo_id_after_save;
        }else
           echo M_URL .'/quotations/entry';
        die;
    }
    function quotes_delete($id) {
        $this->selectModel('Quotation');
    	$this->Quotation->save(array('_id' => new MongoId($id),'deleted' => true));
    	echo 'ok';
    	die;
    }
    function get_enquiry_info($arr_enquiry){
        $arr_save = array();
        $arr_save['enquiry_id'] = $arr_enquiry['_id'];

        if(isset($arr_enquiry['our_rep_id']) && is_object($arr_enquiry['our_rep_id'])){
            $arr_save['our_rep'] = $arr_enquiry['our_rep'];
            $arr_save['our_rep_id'] = $arr_enquiry['our_rep_id'];
        }
        return $arr_save;
    }


    public function popup($key = '') {
		$this->set('key', $key);

		$limit = 100; $skip = 0; $cond = array();

		// Nếu là search GET
		if (!empty($_GET)) {

			$tmp = $this->data;

			if (isset($_GET['company_id'])) {
				$cond['company_id'] = new MongoId($_GET['company_id']);
				$tmp['Enquiry']['company'] = $_GET['company_name'];
			}

			if (isset($_GET['is_customer'])) {
				$cond['is_customer'] = 1;
				$tmp['Enquiry']['is_customer'] = 1;
			}

			if (isset($_GET['is_employee'])) {
				$cond['is_employee'] = 1;
				$tmp['Enquiry']['is_employee'] = 1;
			}

			$this->data = $tmp;
		}

		// Nếu là search theo phân trang
		$page_num = 1;
		$limit = 10; $skip = 0;
		if( isset($_POST['pagination']) && $_POST['pagination']['page-num'] > 0 ){

			// $limit = $_POST['pagination']['page-list'];
			$page_num = $_POST['pagination']['page-num'];
			//$limit = $_POST['pagination']['page-list'];
			$skip = $limit*($page_num - 1);
		}
		$this->set('page_num', $page_num);
		$this->set('limit', $limit);

		$arr_order = array('date' => -1);
		if( isset($_POST['sort']) && strlen($_POST['sort']['field']) > 0 ){
			$sort_type = 1;
			if( $_POST['sort']['type'] == 'desc' ){
				$sort_type = -1;
			}
			$arr_order = array($_POST['sort']['field'] => $sort_type);

			$this->set('sort_field', $_POST['sort']['field']);
			$this->set('sort_type', ($sort_type === 1)?'asc':'desc');
			$this->set('sort_type_change', ($sort_type === 1)?'desc':'asc');
		}

		// search theo submit $_POST kèm điều kiện
		if (!empty($this->data) && !empty($_POST) && isset($this->data['Enquiry']) ) {
			$arr_post = $this->data['Enquiry'];

			if (isset($arr_post['contact_name']) && strlen($arr_post['contact_name']) > 0) {
				$cond['contact_name'] = new MongoRegex('/' . trim($arr_post['contact_name']) . '/i');
			}

			if (isset($arr_post['company']) && strlen($arr_post['company']) > 0) {
				$cond['company'] = new MongoRegex('/' . $arr_post['company'] . '/i');
			}

		}

		$this->selectModel('Enquiry');
		$arr_enquiry = $this->Enquiry->select_all(array(
			'arr_where' => $cond,
			'arr_order' => $arr_order,
			'limit' => $limit,
			'skip' => $skip,
			'arr_field' => array('no', 'company_name', 'contact_name','date')
		));
		$this->set('arr_enquiry', $arr_enquiry);

		$total_page = $total_record = $total_current = 0;
		if( is_object($arr_enquiry) ){
			$total_current = $arr_enquiry->count(true);
			$total_record = $arr_enquiry->count();
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

	public function requirements($id = '0', $num_position = -1) {
		$arr_tmp = $this->entry_init($id, $num_position, 'Enquiry', 'enquirys');
		if (!isset($arr_tmp['detail'])) $arr_tmp['detail'] = $arr_tmp['detail'] = '';
		$this->set('detail',$arr_tmp['detail']);
	
		$arr_tmp1['Enquiry'] = $arr_tmp;
		$this->data = $arr_tmp1;
		$arr_enquiry_id = array();
		//pr($arr_tmp);die;
	}
	public function keywords($id = '0', $num_position = -1) {
		$arr_tmp = $this->entry_init($id, $num_position, 'Enquiry', 'enquirys');
		$this->selectModel('Setting');
		
		$arr_enquiry_keywords = array(""=>"");
		$arr_enquiry_keywords = $arr_enquiry_keywords + $this->Setting->select_option(array('setting_value' => 'enquiry_keywords'), array('option'));

		if (!isset($arr_tmp['keywords'])) $arr_tmp['keywords'] = array();
		$this->set('keywords',$arr_tmp['keywords']);
		$this->set('arr_enquiry_keywords',$arr_enquiry_keywords);

		$arr_tmp1['Enquiry'] = $arr_tmp;
		$this->data = $arr_tmp1;
		$arr_enquiry_id = array();
	}

}