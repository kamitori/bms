<?php
class ContactsController extends MobileAppController {
	var $modelName = 'Contact';
	var $name = 'Contacts';
	function beforeFilter() {
		parent::beforeFilter();
		if(!$this->request->is('ajax'))
			$this->set('arr_tabs', array(
			           			'addresses' => array('addresses'),
			           			'enquiries' => array('enquiries'),
			           			'jobs' => array('jobs','defaul_new_job'),
			           			'tasks' => array('tasks'),
			           			'personal' => array('employee','emergency'),
			           			'rates/Wages' => array('employee','employment'),
			           			'expenses' => array('expenses'),
			           			'leave' => array('leave'),
			           			'working & Holiday'  => array('working'),
			           			'user prefs'=> array('user_prefs','system_login','jobtraq_related'),
			           			'product' => array('products'),
			           			'quotes' => array('quotes'),
			           			'orders' => array('orders'),
			           			'shippings' => array('shippings'),
			           			'account' => array('invoices'),
			           			'document'  => array('docs'),
			           			'other'  => array('other'),
			           			));
	}
	public function popup($key = '') {
		$this->set('key', $key);

		if( $key == '_assets' ){
			$this->selectModel('Equipment');
			$arr_equipment = $this->Equipment->select_all(array('arr_order' => array('name' => 1)));
			$this->set( 'arr_equipment', $arr_equipment );
		}

		$limit = 15; $skip = 0; $cond = array();

		// Nếu là search GET
		if (!empty($_GET)) {

			$tmp = $this->data;

			if (isset($_GET['company_id'])) {
				$cond['company_id'] = new MongoId($_GET['company_id']);
				$tmp['Contact']['company'] = $_GET['company_name'];
			}

			if (isset($_GET['is_customer'])) {
				$cond['is_customer'] = 1;
				$tmp['Contact']['is_customer'] = 1;
			}

			if (isset($_GET['is_employee'])) {
				$cond['is_employee'] = 1;
				$tmp['Contact']['is_employee'] = 1;
			}

			$this->data = $tmp;
		}

		// Nếu là search theo phân trang
		$page_num = 1;
		if( isset($_POST['pagination']) && $_POST['pagination']['page-num'] > 0 ){

			// $limit = $_POST['pagination']['page-list'];
			$page_num = $_POST['pagination']['page-num'];//2
			//$limit = $_POST['pagination']['page-list'];
			$skip = $limit*($page_num - 1); // 0->10->20
		}
		$this->set('page_num', $page_num);
		$this->set('limit', $limit);

		$arr_order = array('first_name' => 1);
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
		$cond['inactive'] = 0;
		if (!empty($this->data) && !empty($_POST)) {
			$arr_post = $this->data['Contact'];

			if (isset($arr_post['name']) && strlen($arr_post['name']) > 0) {
				$cond['full_name'] = new MongoRegex('/' . trim($arr_post['name']) . '/i');
			}

			if( $arr_post['inactive'] )
				$cond['inactive'] = 1;

			if (is_numeric($arr_post['is_customer']) && $arr_post['is_customer'])
				$cond['is_customer'] = 1;

			if (is_numeric($arr_post['is_employee']) && $arr_post['is_employee'])
				$cond['is_employee'] = 1;
			$inputHolder = (isset($this->data['inputHolder']) ? $this->data['inputHolder'] : '');
			$this->set('inputHolder',$inputHolder);
		}

		if(empty($_POST) && ( isset($cond['is_employee']) && $cond['is_employee'] ) || ( isset($_GET['company_id']) && $_GET['company_id'] == '5271dab4222aad6819000ed0' ) ){
			$limit = 15;
			$this->set('limit', $limit);
		}
		$this->selectModel('Contact');
		$arr_contact = $this->Contact->select_all(array(
			'arr_where' => $cond,
			'arr_order' => $arr_order,
			'limit' => $limit,
			'skip' => $skip,
			'arr_field' => array('first_name', 'last_name','company_id', 'is_employee', 'is_customer','no'),
		));

		$this->set('arr_contact', $arr_contact);

		$total_page = $total_record = $total_current = 0;
		if( is_object($arr_contact) ){
			$total_current = $arr_contact->count(true);
			$total_record = $arr_contact->count();
			if( $total_record%$limit != 0 ){
				$total_page = floor($total_record/$limit) + 1;
			}else{
				$total_page = $total_record/$limit;
			}
		}
		$this->set('total_current', $total_current);
		$this->set('total_page', $total_page);
		$this->set('total_record', $total_record);

		$this->selectModel('Company');
		$this->set('model_company', $this->Company);

		$this->layout = 'ajax';
	}

	public function entry($id = '0', $num_position = -1) {
		$arr_tmp = $this->entry_init($id, $num_position, 'Contact', 'contacts');

		$this->selectModel('Setting');
		$arr_contacts_type = $this->Setting->select_option(array('setting_value' => 'contacts_type'), array('option'));
		$this->set('arr_contacts_type', $arr_contacts_type);
		$this->set('first_name',$arr_tmp['first_name']);
		$this->set('last_name',$arr_tmp['last_name']);
		$this->set('direct_dial',$arr_tmp['direct_dial']);
		$this->set('mobile',$arr_tmp['mobile']);
		$this->set('email',$arr_tmp['email']);


		$arr_contacts_title = $this->Setting->select_option(array('setting_value' => 'contacts_title'), array('option'));
		$this->set('arr_contacts_title', $arr_contacts_title);

		$arr_position = $this->Setting->select_option(array('setting_value' => 'contacts_position'), array('option'));
		$this->set('arr_position', $arr_position);

		$arr_department = $this->Setting->select_option(array('setting_value' => 'contacts_department'), array('option'));
		$this->set('arr_department', $arr_department);

		$this->set('arr_country', $this->Setting->select_option(array('setting_value' => 'lists_country'), array('option')));

		if (isset($arr_tmp['enquiry_id']) && is_object($arr_tmp['enquiry_id']) ) {
			$this->selectModel('Enquiry');
			$enquiry = $this->Enquiry->select_one(array('_id' => $arr_tmp['enquiry_id']), array('_id', 'no', 'company'));
			if( isset($enquiry['_id']) ){
				$arr_tmp['enquiry_no'] = $enquiry['no'];
				$arr_tmp['enquiry_name'] = $enquiry['company'];
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
				$arr_tmp['purchaseorder_name'] = $purchaseorder['name'];
			}
		}

		$arr_tmp1['Contact'] = $arr_tmp;
		$this->data = $arr_tmp1;
		$arr_contact_id = array();
		if (isset($arr_tmp['our_rep_id']))
			$arr_contact_id[] = $arr_tmp['our_rep_id'];

		$this->selectModel('Country');
		$options = array();
		$options['countries'] = $this->Country->get_countries();
		$this->selectModel('Province');
		$options['provinces'] = $this->Province->get_all_provinces();
		$this->set('options',$options);

	}

	function auto_save( $field = '' ) {
		if (!empty($this->data)) {
			$arr_post_data = $this->data['Contact'];
			$arr_save = $arr_post_data;

			$this->selectModel('Contact');
			$arr_tmp = $this->Contact->select_one(array('no' => (int) $arr_save['no'], '_id' => array('$ne' => new MongoId($arr_save['_id']))));
			if (isset($arr_tmp['no'])) {
				echo 'ref_no_existed';
				die;
			}

			$field = str_replace(array('data[Contact][', ']'), '', $field);

			if (strlen(trim($arr_save['salesorder_id'])) > 0){
				$arr_save['salesorder_id'] = new MongoId($arr_save['salesorder_id']);

				$this->selectModel('Salesorder');
				$so = $this->Salesorder->select_one(array('_id' => new MongoId($arr_save['salesorder_id'])), array('_id', 'salesorder_date', 'payment_due_date'));

				// Kiểm tra xem có thay đổi work_end không,
				if ($field != '' && ( $field == 'work_end' || $field == 'work_end_hour' )) { // if($work_end != $arr_save['work_end_old'] ){
					// nếu có thì có thay đổi đúng không
					if ($work_end < strtotime('now')) {
						echo 'error_work_end';
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

			}

			if (!isset($arr_save['inactive']))
				$arr_save['inactive'] = 0;
			if (!isset($arr_save['is_employee']))
				$arr_save['is_employee'] = 0;
			if (!isset($arr_save['is_customer']))
				$arr_save['is_customer'] = 0;


			if (strlen(trim($arr_save['our_rep_id'])) > 0)
				$arr_save['our_rep_id'] = new MongoId($arr_save['our_rep_id']);

			if (strlen(trim($arr_save['company_id'])) > 0)
				$arr_save['company_id'] = new MongoId($arr_save['company_id']);

			if (strlen(trim($arr_save['company_name'])) > 0)
				$arr_save['company'] = $arr_save['company_name'];

			if (strlen(trim($arr_save['enquiry_id'])) > 0)
				$arr_save['enquiry_id'] = new MongoId($arr_save['enquiry_id']);

			if (strlen(trim($arr_save['quotation_id'])) > 0)
				$arr_save['quotation_id'] = new MongoId($arr_save['quotation_id']);

			if (strlen(trim($arr_save['job_id'])) > 0)
				$arr_save['job_id'] = new MongoId($arr_save['job_id']);

			if (strlen(trim($arr_save['purchaseorder_id'])) > 0)
				$arr_save['purchaseorder_id'] = new MongoId($arr_save['purchaseorder_id']);


			$this->selectModel('Contact');
			if ($this->Contact->save($arr_save)) {
				if( isset($check_reload) ){
					$this->layout = 'ajax';
					$arr_post_data['Contact'] = $arr_post_data;
					$this->data = $arr_post_data;
					$this->render('entry_udpate_date');
				}else{
					echo 'ok';die;
				}

			} else {
				echo 'Error: ' . $this->Contact->arr_errors_save[1];
				die;
			}
		}
	}

	function addresses_default_key() {
		/*if( $this->request->is('ajax') ) {
			pr( $this->request->data );die;
			//pr( $this->request->data('value_to_send'));die;
		}*/

		$arr_save = $this->data['Contact'];
		$arr_save['_id']= $this->get_id();
		$this->selectModel('Contact');
		if ($this->Contact->save($arr_save)) {
			echo 'ok';die;
		}

	}

	function lists( $content_only = '' ) {
		$this->selectModel('Contact');
		$limit = 20; $skip = 0;
		// dùng cho sort
		$sort_field = 'no';
		$sort_type = -1;
		if( isset($_POST['sort']) && strlen($_POST['sort']['field']) > 0 ){
			if( $_POST['sort']['type'] == 'desc' ){
				$sort_type = -1;
			}
			$sort_field = $_POST['sort']['field'];
			$this->Session->write('contacts_lists_search_sort', array($sort_field, $sort_type));

		}elseif( $this->Session->check('contacts_lists_search_sort') ){
			$session_sort = $this->Session->read('contacts_lists_search_sort');
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
		if( $this->Session->check('contacts_entry_search_cond') ){
			$cond = $this->Session->read('contacts_entry_search_cond');
		}

		// dùng cho phân trang
		if(isset($_POST['offset'])){
			$skip = $_POST['offset'];
		}
		// query
		$arr_contacts = $this->Contact->select_all(array(
			'arr_where' => $cond,
			'arr_order' => $arr_order,
			'limit' => $limit,
			'skip' => $skip
		));
		$this->set('arr_contacts', $arr_contacts);
		$this->selectModel('Setting');
		$this->set('arr_contacts_type', $this->Setting->select_option(array('setting_value' => 'contacts_type'), array('option')));
		//$this->set('arr_contacts_status', $this->Setting->select_option(array('setting_value' => 'contacts_status'), array('option')));
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

	function delete($id = 0) {
		$arr_save['_id'] = $id;
		$arr_save['deleted'] = true;
		$error = 0;
		if (!$error) {
			$this->selectModel('Contact');
			if ($this->Contact->save($arr_save)) {
				$this->Session->delete($this->name.'ViewId');
				if($this->request->is('ajax'))
					echo 'ok';
				else
					$this->redirect('/mobile/contacts/entry');
			} else {
				echo 'Error: ' . $this->Contact->arr_errors_save[1];
			}
		}
		die;
	}

	public function add() {
		$this->selectModel('Contact');
		$arr_save = array();

		$this->Contact->arr_default_before_save = $arr_save;
		if ($this->Contact->add())
			$this->redirect('/mobile/contacts/entry/' . $this->Contact->mongo_id_after_save);
		die;
	}

	public function before_save($field,$value,$id, $options = array()){
		$arr_return = array();
		if($field == 'contact_default'){
			$this->selectModel('Contact');
			$contact = $this->Contact->select_one(array('_id' => new MongoId($id)),array('company_id'));
			if(isset($contact['company_id']) && is_object($contact['company_id'])){
				$this->Contact->collection->update(array(
				                                   	'deleted' => false,
				                                   	'company_id' => $contact['company_id'],
				                                   	'contact_default' => 1
			                                   ),array(
			                                   		'$set' => array('contact_default' => 0)
			                                   ),array('multiple' => true)
			                                   );
				$this->selectModel('Company');
				$this->Company->save(array('_id' => $contact['company_id'], 'contact_default_id' => new MongoId($id)));
			}
		}
		return $arr_return;
	}

	public function enquiries(){
		if(isset($_POST['add'])){
			return $this->enquiries_add();
		}
		$offset = 0;
		if(isset($_POST['offset']))
			$offset = $_POST['offset'];
		$id = $this->get_id();
        $this->selectModel('Enquiry');
        $arr_enquiry = $this->Enquiry->select_all(array(
                                                  'arr_where' => array('contact_id'=>new MongoId($id)),
                                                  'arr_field' => array('no','date','status','contact_id','contact_name','our_rep','referred','referred_id','enquiry_value','mobile','no'),
                                                  'arr_order' => array('_id' => 1),
                                                  'limit' => 10,
                                                  'skip' => $offset
                                                  ));
        if($this->request->is('ajax')){
        	if($arr_enquiry->count(true) == 0){
        		echo json_encode(array('empty'=>true));
        		die;
        	}
			foreach($arr_enquiry as $enquiry){
				$enquiry['_id'] = (string)$enquiry['_id'];
				$arr_data['data'][] = $enquiry;
			}
			echo json_encode($arr_data);
			die;
        } else {
        	$this->selectModel('Contact');
	        $arr_contact = $this->Contact->select_one(array('_id' => new MongoId($id)), array('_id', 'first_name', 'last_name','enquiry_id'));
	        $arr = array();
	        foreach($arr_enquiry as $key => $value){
	                $arr[] = $value;
	        }
	        $this->set('arr_enquiries',$arr);

	        $arr_data['field'] = array(
        	                           'no'=>array(
        	                                       'label' => 'Ref No',
        	                                       'type' => ''),
        	                           'date'=>array(
        	                                       'label' => 'Date',
        	                                       'type' => 'text'),
        	                           'status'=>array(
        	                                       'label' => 'Status',
        	                                       'type' => 'text'),
        	                           'contact'=>array(
        	                                       'label' => 'Contact',
        	                                       'type' => 'text'),
        	                           'our_rep'=>array(
        	                                       'label' => 'Our rep',
        	                                       'type' => 'checkbox'),
        	                           'referred'=>array(
        	                                       'label' => 'referred',
        	                                       'type' => 'text'),
        	                           'enquiry_value'=>array(
        	                                       'label' => 'Enquiry value',
        	                                       'type' => 'text'),
        	                           'requirements'=>array(
        	                                       'label' => 'Requirements',
        	                                       'type' => 'text'),
        	                           );
			$arr_data['header'] = array(
			                        'link_to_entry' => M_URL.'/enquiries/entry/',
			                        'link_to_entry_value' => 'no',
			                        'info' => 'first_name last_name'
			                            );
			$this->set('arr_data',$arr_data);
        }
    }

	function enquiries_delete($enquiry_id) {
        $arr_save['_id'] = new MongoId($enquiry_id);
        $arr_save['deleted'] = true;
        $this->selectModel('Enquiry');
        if ($this->Enquiry->save($arr_save)) {
            echo 'ok';
        } else {
            echo 'Error: ' . $this->Enquiry->arr_errors_save[1];
        }
        die;
    }

    function enquiries_add(){
    	$id  = $this->get_id();
    	$this->selectModel('Enquiry');
    	$this->selectModel('Contact');
    	$contact = $this->Contact->select_one(array('_id' => new MongoId($id)),array('first_name', 'last_name',  'company_phone'));
    	$arr_save = array(
    	                'contact_id' 	=> new MongoId($id),
    	                'contact_name'		=> $contact['last_name']
    	                  );

        $arr_save['contact_phone'] = $contact['company_phone'];
		$arr_save['no'] = $this->Enquiry->get_auto_code('no');
        $this->Enquiry->arr_default_before_save = $arr_save;
        if ($this->Enquiry->add()) {
           echo M_URL.'/enquiries/entry/'. $this->Enquiry->mongo_id_after_save;
        }else
           echo M_URL .'/enquiries/entry';
        die;
    }

    public function jobs(){
		if(isset($_POST['add'])){
			return $this->jobs_add();
		}
		$offset = 0;
		if(isset($_POST['offset']))
			$offset = $_POST['offset'];
		$id = $this->get_id();
        $this->selectModel('Job');
        $arr_job = $this->Job->select_all(array(
                                                  'arr_where' => array('contact_id'=>new MongoId($id)),
                                                  'arr_field' => array('no','name','type','type_id','work_start','work_end','status','status_id'),
                                                  'arr_order' => array('_id' => 1),
                                                  'limit' => 10,
                                                  'skip' => $offset
                                                  ));
        if($this->request->is('ajax')){
        	if($arr_job->count(true) == 0){
        		echo json_encode(array('empty'=>true));
        		die;
        	}
			foreach($arr_job as $job){
				$job['_id'] = (string)$job['_id'];
				$arr_data['data'][] = $job;
			}
			echo json_encode($arr_data);
			die;
        } else {
        	$this->selectModel('Contact');
	        $arr = array();
	        foreach($arr_job as $key => $value){
	                $arr[] = $value;
	        }
	        $this->set('arr_jobs',$arr);

	        $arr_data['field'] = array(
        	                           'no'=>array(
        	                                       'label' => 'Job no',
        	                                       'type' => ''),
        	                           'name'=>array(
        	                                       'label' => 'Job name',
        	                                       'type' => 'text'),
        	                           'type'=>array(
        	                                       'label' => 'Job type',
        	                                       'type' => 'text'),
        	                           'work_start'=>array(
        	                                       'label' => 'Start',
        	                                       'type' => 'text'),
        	                           'work_end'=>array(
        	                                       'label' => 'Finish',
        	                                       'type' => 'checkbox'),
        	                           'status'=>array(
        	                                       'label' => 'Status',
        	                                       'type' => 'text'),
        	                           'job_value'=>array(
        	                                       'label' => 'Job manager',
        	                                       'type' => 'text'),
        	                           );
			$arr_data['header'] = array(
			                        'link_to_entry' => M_URL.'/jobs/entry/',
			                        'link_to_entry_value' => 'no',
			                        'info' => 'name'
			                            );
			$this->set('arr_data',$arr_data);
        }
    }

    function jobs_delete($job_id) {
        $arr_save['_id'] = new MongoId($job_id);
        $arr_save['deleted'] = true;
        $this->selectModel('Job');
        if ($this->Job->save($arr_save)) {
            echo 'ok';
        } else {
            echo 'Error: ' . $this->Job->arr_errors_save[1];
        }
        die;
    }

    function jobs_add(){
    	$id  = $this->get_id();
    	$this->selectModel('Contact');
    	$contact = $this->Contact->select_one(array('_id' => new MongoId($id)),array('first_name', 'last_name',  'company_phone'));
    	$arr_save = array(
    	                'contact_id' 	=> new MongoId($id),
    	                'contact_name'		=> $contact['last_name']
    	                  );
        $arr_save['contact_phone'] = $contact['company_phone'];
    	$this->selectModel('Job');
		$arr_tmp = $this->Job->select_one(array(), array(), array('_id' => -1));
        $arr_save['no'] = $this->Job->get_auto_code('no');
        $this->Job->arr_default_before_save = $arr_save;
        if ($this->Job->add()) {
           echo M_URL.'/jobs/entry/'. $this->Job->mongo_id_after_save;
        }else
           echo M_URL .'/jobs/entry';
        die;
    }

    public function defaul_new_job($id = '0', $num_position = -1) {
		$arr_tmp = $this->entry_init($id, $num_position, 'Contact', 'contacts');

		$this->set('markup_rate',$arr_tmp['markup_rate']);
		$this->set('rate_per_hour',$arr_tmp['rate_per_hour']);

		$arr_tmp1['Contact'] = $arr_tmp;
		$this->data = $arr_tmp1;
		$arr_contact_id = array();
		if (isset($arr_tmp['our_rep_id']))
			$arr_contact_id[] = $arr_tmp['our_rep_id'];
		//pr($arr_tmp);die;
	}

    public function tasks(){
        if(isset($_POST['add'])){
            return $this->tasks_add();
        }
        $offset = 0;
        if(isset($_POST['offset']))
            $offset = $_POST['offset'];
        $id = $this->get_id();
        $this->selectModel('Task');
        $arr_task = $this->Task->select_all(array(
                                                  'arr_where' => array('contact_id'=>new MongoId($id)),
                                                  'arr_field' => array('no','name','type','type_id','our_rep','work_start','work_end','status','status_id'),
                                                  'arr_order' => array('_id' => 1),
                                                  'limit' => 10,
                                                  'skip' => $offset
                                                  ));
        if($this->request->is('ajax')){
            if($arr_task->count(true) == 0){
                echo json_encode(array('empty'=>true));
                die;
            }
            foreach($arr_task as $task){
                $task['_id'] = (string)$task['_id'];
                $arr_data['data'][] = $task;
            }
            echo json_encode($arr_data);
            die;
        } else {
            $this->selectModel('Contact');
            $arr = array(0=>array());
            foreach($arr_task as $key => $value){
                    $arr[] = $value;
            }
            if(empty($arr[0]))
                unset($arr[0]);
            $this->set('arr_tasks',$arr);

            $arr_data['field'] = array(
                                       'no'=>array(
                                                   'label' => 'No',
                                                   'type' => ''),
                                       'name'=>array(
                                                   'label' => 'Task name',
                                                   'type' => 'text'),
                                       'type'=>array(
                                                   'label' => 'Task type',
                                                   'type' => 'text'),
                                        'our_rep'=>array(
                                                   'label' => 'Responsible',
                                                   'type' => 'text'),
                                       'work_start'=>array(
                                                   'label' => 'Work start',
                                                   'type' => 'text'),
                                       'work_end'=>array(
                                                   'label' => 'Work end',
                                                   'type' => 'checkbox'),
                                       'status'=>array(
                                                   'label' => 'Status',
                                                   'type' => 'text'),

                                       );
            $arr_data['header'] = array(
                                    'link_to_entry' => M_URL.'/tasks/entry/',
                                    'link_to_entry_value' => 'no',
                                    'info' => 'name'
                                        );
            $this->set('arr_data',$arr_data);
        }
    }

 	function tasks_delete($task_id) {
        $arr_save['_id'] = new MongoId($task_id);
        $arr_save['deleted'] = true;
        $this->selectModel('Task');
        if ($this->Task->save($arr_save)) {
            echo 'ok';
        } else {
            echo 'Error: ' . $this->Task->arr_errors_save[1];
        }
        die;
    }

    function tasks_add(){
    	$id  = $this->get_id();
    	$this->selectModel('Contact');
    	$contact = $this->Contact->select_one(array('_id' => new MongoId($id)),array('first_name', 'last_name',  'our_rep'));
    	$arr_save = array(
    	                'contact_id' 	=> new MongoId($id),
    	                'contact_name'		=> $contact['last_name']
    	                  );

        $arr_save['our_rep'] = $contact['our_rep'];
        $arr_save['status'] = 'New';

    	$this->selectModel('Task');
        $arr_save['no'] = $this->Task->get_auto_code('no');
        $this->Task->arr_default_before_save = $arr_save;
        if ($this->Task->add()) {
           echo M_URL.'/tasks/entry/'. $this->Task->mongo_id_after_save;
        }else
           echo M_URL .'/tasks/entry';
        die;
    }

    public function addresses(){
    	if(isset($_POST['add'])){
    		return $this->addresses_add();
    	}
    	$this->selectModel('Contact');
    	$contact = $this->Contact->select_one(array('_id' => new MongoId($this->get_id())),array('addresses','addresses_default_key'));
    	if(!isset($contact['addresses_default_key']))
    		$contact['addresses_default_key'] = 0;
        $this->set('arr_addresses',$contact['addresses']);
        $this->set('addresses_default_key',$contact['addresses_default_key']);

        $arr_data['field'] = array(
	                           'name'=>array(
	                                       'label' => 'Name',
	                                       'type' => 'select',
	                                       'options'=>'contacts_addresses_name'),
	                           'default'=>array(
	                                       'label' => 'Default',
	                                       'type' => 'checkbox',
	                                       ),
	                           'address_1'=>array(
	                                       'label' => 'Address 1',
	                                       'type' => 'text'),
	                           'address_2'=>array(
	                                       'label' => 'Address 2',
	                                       'type' => 'text'),
	                           'address_3'=>array(
	                                       'label' => 'Address 3',
	                                       'type' => 'text'),
	                           'town_city'=>array(
	                                       'label' => 'Town / City',
	                                       'type' => 'text',
	                                       ),
	                           'province_state_id'=>array(
	                                       'label' => 'Province / State',
	                                       'type' => 'select',
	                                       'options'=>'provinces@CA'),
	                           'zip_postcode'=>array(
	                                       'label' => 'Zip / Post code',
	                                       'type' => 'text'),
	                           'country_id'=>array(
	                                       'label' => 'Country',
	                                       'type' => 'select',
	                                       'options'=>'countries'),
	                           );
		$arr_data['header'] = array(
		                        'info' => 'name'
		                            );
		$this->set('arr_data',$arr_data);
		$this->selectModel('Setting');
		$options['contacts_addresses_name'] = $this->Setting->select_option(array('setting_value' => 'contacts_addresses_name'), array('option'));
		$this->selectModel('Country');
		$options['countries'] = $this->Country->get_countries();
		$this->selectModel('Province');
		$options['provinces'] = $this->Province->get_all_provinces();
		$this->set('options',$options);
    }

    function addresses_add(){
    	$this->selectModel('Contact');
    	$contact = $this->Contact->select_one(array('_id' => new MongoId($this->get_id())),array('addresses'));
    	if(!isset($contact['addresses']))
    		$contact['addresses'] = array();
    	$i = 0;
    	foreach($contact['addresses'] as $address){
    		if(isset($address['deleted']) && $address['deleted']) continue;
    		$i++;
    	}
    	$contact['addresses'][] = array(
    	                              'deleted' => false,
    	                              'name' => '',
    	                              'default' => $i > 1 ? 0 : 1,
    	                              'address_1' => '',
    	                              'address_2' => '',
    	                              'address_3' => '',
    	                              'town_city' => '',
    	                              'province_state' => '',
    	                              'province_state_id' => '',
    	                              'zip_postcode' => '',
    	                              'country' => 'Canada',
    	                              'country_id' => 'CA',
    	                              );
    	$this->Contact->save($contact);
    	echo json_encode(array(
    	                 	array(
    	                 	      '_id' => $i,
    	                          'country_id' => 'CA',
    	                 	      )
    	                 ));
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
                                                'arr_where' => array('contact_id'=>new MongoId($id)),
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
    function quotes_add() {
    	$contact_id = $this->get_id();
        $this->selectModel('Contact');
        $arr_contact = $this->Contact->select_one(array('_id' => new MongoId($contact_id)),array('first_name','last_name','email','phone','addresses_default_key','addresses','our_rep_id','our_rep','contact_name','no'));
        $arr_save = $this->get_contact_info($arr_contact);
        $this->selectModel('Quotation');
        $arr_save['code'] = $this->Quotation->get_auto_code('code');
        $arr_save['name'] = $arr_save['code'].' - '.$arr_save['contact_name'];
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
    function get_contact_info($arr_contact){
    	$arr_save = array();
    	$arr_save['contact_name'] = isset($arr_contact['first_name'])?$arr_contact['last_name']:'';
        $arr_save['contact_id'] = $arr_contact['_id'];
        $arr_save['email'] = isset($arr_contact['email'])?$arr_contact['email']:'';
        $arr_save['phone'] = isset($arr_contact['phone'])?$arr_contact['phone']:'';


        if(isset($arr_contact['addresses_default_key'])){
            $key_default = $arr_contact['addresses_default_key'];
            $arr_save['invoice_address'][0] = array(
                'deleted' => false,
                'invoice_address_1' => isset($arr_contact['addresses'][$key_default]['address_1'])?$arr_contact['addresses'][$key_default]['address_1']:'',
                'invoice_address_2' => $arr_contact['addresses'][$key_default]['address_2'],
                'invoice_address_3' => $arr_contact['addresses'][$key_default]['address_3'],
                'invoice_town_city' => $arr_contact['addresses'][$key_default]['town_city'],
                'invoice_province_state' => $arr_contact['addresses'][$key_default]['province_state'],
                'invoice_province_state_id' => $arr_contact['addresses'][$key_default]['province_state_id'],
                'invoice_zip_postcode' => $arr_contact['addresses'][$key_default]['zip_postcode'],
                'invoice_country' => $arr_contact['addresses'][$key_default]['country'],
                'invoice_country_id' => $arr_contact['addresses'][$key_default]['country_id']
            );
        }elseif(isset($arr_contact['addresses'][0])){
            $arr_save['invoice_address'][0] = array(
                'deleted' => false,
                'invoice_address_1' => $arr_contact['addresses'][0]['address_1'],
                'invoice_address_2' => $arr_contact['addresses'][0]['address_2'],
                'invoice_address_3' => $arr_contact['addresses'][0]['address_3'],
                'invoice_town_city' => $arr_contact['addresses'][0]['town_city'],
                'invoice_province_state' => $arr_contact['addresses'][0]['province_state'],
                'invoice_province_state_id' => $arr_contact['addresses'][0]['province_state_id'],
                'invoice_zip_postcode' => $arr_contact['addresses'][0]['zip_postcode'],
                'invoice_country' => $arr_contact['addresses'][0]['country'],
                'invoice_country_id' => $arr_contact['addresses'][0]['country_id']
            );
        }
        if(isset($arr_contact['our_rep_id']) && is_object($arr_contact['our_rep_id'])){
            $arr_save['our_rep'] = $arr_contact['our_rep'];
            $arr_save['our_rep_id'] = $arr_contact['our_rep_id'];
        }
        return $arr_save;
    }

    public function products(){
    	$this->selectModel('Contact');
    	$contact = $this->Contact->select_one(array('_id' => new MongoId($this->get_id())),array('pricing'));
    	$arr_pricing = array();
    	if(isset($contact['pricing']) && is_array($contact['pricing'])){
    		foreach($contact['pricing'] as $key => $value){
    			if($value['deleted']) continue;
    			$price_break = array();
    			if(isset($value['price_break']) && is_array($value['price_break'])){
    				$price_break = array();
    				foreach($value['price_break'] as $pb){
    					if($pb['deleted']) continue;
    					$price_break = $pb; break;
    				}
    			}
				$arr_pricing[$key] = array(
				                           '_id' => $key,
				                           'code' => $value['code'],
				                           'name' => $value['name'],
				                           'range' => (isset($price_break['range_from']) ? $price_break['range_from'].'-' : '').(isset($price_break['range_to']) ? $price_break['range_to'] : ''),
				                           'unit_price' => isset($price_break['unit_price']) ? number_format((float)$price_break['unit_price'],2) : '',
				                           );
    		}
    	}
    	$this->set('arr_products',$arr_pricing);
    }
    public function products_pricing($pricing_key){
    	$this->selectModel('Contact');
    	$contact = $this->Contact->select_one(array('_id' => new MongoId($this->get_id())),array('company','pricing'));
    	if(!isset($contact['pricing']) || !isset($contact['pricing'][$pricing_key]) || $contact['pricing'][$pricing_key]['deleted']){
    		if(isset($_POST['add'])){
    			echo M_URL.'/contacts/products';
	    		die;
	    	} else {
    			$this->redirect('/contacts/products');
	    	}
    	}
    	$pricing = $contact['pricing'][$pricing_key];
    	if(isset($_POST['add'])){
    		$arr_save = array(
                              'deleted' => false,
                              'range_from' => 1,
                              'range_to'	=> 1,
                              'unit_price'	=> (float)0
                            );
    		$pricing['price_break'][] = $arr_save;
    		$contact['pricing'][$pricing_key] = $pricing;
    		$this->Contact->save($contact);
    		$arr_save['_id'] = count($pricing['price_break']) - 1;
    		echo json_encode(array(0=>$arr_save));
    		die;
    	}
    	$this->set('pricing_key', $pricing_key);
    	$arr_pricebreaks = array();
    	if(isset($pricing['price_break']) && is_array($pricing['price_break'])){
    		foreach($pricing['price_break'] as $key => $value){
    			if($value['deleted']) continue;
    			$arr_pricebreaks[$key] = array(
    			                               '_id' => $key,
    			                               'range_from' => isset($value['range_from']) ? $value['range_from'] : '',
    			                               'range_to' => isset($value['range_to']) ? $value['range_to'] : '',
    			                               'unit_price' => isset($value['unit_price']) ? number_format($value['unit_price'],2) : '',
    			                               );
    		}
    	}
    	$this->set('arr_pricebreaks',$arr_pricebreaks);
    	if(!$this->request->is('ajax')){
    		$arr_data['field'] = array(
	                           'range_from'=>array(
	                                       'label' => 'Range From',
	                                       'type' => 'text',),
	                           'range_to'=>array(
	                                       'label' => 'Range To',
	                                       'type' => 'text',
	                                       ),
	                           'unit_price'=>array(
	                                       'label' => 'Unit Price',
	                                       'type' => 'text'),
	                           );
			$arr_data['header'] = array(
			                        'link_to_entry' => '',
			                        'link_to_entry_value' => 'range_from - range_to',
			                        'info' => 'unit_price'
			                            );
    	}
    	$arr_data['contact_name'] = $contact['company'];
    	$arr_data['notes'] = $pricing['notes'];
    	$arr_data['name'] = $pricing['name'];
    	$arr_data['code'] = $pricing['code'];
    	$this->set('arr_data',$arr_data);
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
                                                'arr_where' => array('contact_id'=>new MongoId($id)),
                                                'arr_order' => array('shipping_date' => -1),
                                                'arr_field' => array('code','shipping_type','return_status','received_date','shipping_status','our_rep','shipper','tracking_no'),
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
			$shipping['received_date'] = isset($shipping['received_date']) && is_object($shipping['received_date']) ? date('d M, Y', $shipping['received_date']->sec) : '';
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
	                           'return_status'=>array(
	                                       'label' => 'Return',
	                                       'type' => ''),
	                           'received_date'=>array(
	                                       'label' => 'Date received',
	                                       'type' => ''),
	                           'shipping_status'=>array(
	                                       'label' => 'Status',
	                                       'type' => ''),
	                           'our_rep'=>array(
	                                       'label' => 'Our rep',
	                                       'type' => '',
	                                       ),
	                           'shipper'=>array(
	                                       'label' => 'Carrier',
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
    	$contact_id = $this->get_id();
        $this->selectModel('Contact');
        $arr_contact = $this->Contact->select_one(array('_id' => new MongoId($contact_id)),array('name','email','phone','contact_default_id','addresses_default_key','addresses','our_csr_id','our_csr','our_rep_id','our_rep','is_customer'));
    	$arr_save = $this->get_contact_info($arr_contact);
    	$this->selectModel('Shipping');
        $arr_save['code'] = $this->Shipping->get_auto_code('code');
        $arr_save['name'] = $arr_save['code'].' - '.$arr_save['contact_name'];
        if($arr_contact['is_customer']==0)
            $arr_save['shipping_type'] = 'In';
        elseif($arr_contact['is_customer']==1)
            $arr_save['shipping_type'] = 'Out';
        $arr_save['shipping_status'] = 'In progress';
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

    public function orders(){
    	if(isset($_POST['add']))
    	   return $this->salesorders_add();
    	$offset = 0;
    	if(isset($_POST['offset']))
    		$offset = $_POST['offset'];
    	$id = $this->get_id();
        $this->selectModel('Salesorder');
        $arr_orders = $this->Salesorder->select_all(array(
                                                'arr_where' => array('contact_id'=>new MongoId($id)),
                                                'arr_order' => array('salesorder_date' => -1),
                                                'arr_field' => array('code','sales_order_type','salesorder_date','payment_due_date','status','our_rep','our_csr','sum_sub_total','heading','sum_amount','sum_tax'),
                                                'skip' => $offset,
                                                'limit'  => 10
                                                ));
        $arr_data = array('data'=>array());
		foreach($arr_orders as $order){
			$order['_id'] = (string)$order['_id'];
			$order['heading'] = isset($order['heading']) ? $order['heading'] : '';
			$order['our_rep'] = isset($order['our_rep']) ? $order['our_rep'] : '';
			$order['our_csr'] = isset($order['our_csr']) ? $order['our_csr'] : '';
			$order['salesorder_date'] = date('d M, Y', $order['salesorder_date']->sec);
			$order['payment_due_date'] = date('d M, Y', $order['payment_due_date']->sec);
			$order['sum_sub_total'] = number_format((float)$order['sum_sub_total'],2);
			$arr_data['data'][] = $order;
		}
        if($this->request->is('ajax')){
        	if($arr_orders->count(true) == 0){
        		echo json_encode(array('empty'=>true));
        		die;
        	}
			echo json_encode($arr_data);
			die;
        } else {
	        $this->set('arr_orders',$arr_data['data']);
        	$arr_data['field'] = array(
	                           'code'=>array(
	                                       'label' => 'Ref no',
	                                       'type' => '',),
	                           'sales_order_type'=>array(
	                                       'label' => 'Type',
	                                       'type' => '',
	                                       ),
	                           'salesorder_date'=>array(
	                                       'label' => 'Date in',
	                                       'type' => ''),
	                           'payment_due_date'=>array(
	                                       'label' => 'Due date',
	                                       'type' => ''),
	                           'status'=>array(
	                                       'label' => 'Status',
	                                       'type' => ''),
	                           'our_rep'=>array(
	                                       'label' => 'Our rep',
	                                       'type' => '',
	                                       ),
	                           'our_csr'=>array(
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
			$sum_amount = $this->Salesorder->sum('sum_amount', 'tb_salesorder' , array(
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
			                ));
			$this->set('sum_amount',$sum_amount);
			$this->set('sum_tax',$sum_tax);
			$this->set('sum_sub_total',$sum_sub_total);
        }
    }

    function salesorders_add(){
    	$contact_id = $this->get_id();
        $this->selectModel('Contact');
        $arr_contact = $this->Contact->select_one(array('_id' => new MongoId($contact_id)),array('name','email','phone','contact_default_id','addresses_default_key','addresses','our_csr_id','our_csr','our_rep_id','our_rep'));
    	$arr_save = $this->get_contact_info($arr_contact);
    	$this->selectModel('Salesorder');
        $arr_save['code'] = $this->Salesorder->get_auto_code('code');
        $arr_save['name'] = $arr_save['code'].' - '.$arr_save['contact_name'];
        $arr_save['status'] = 'New';
        $arr_save['salesorder_date'] = new MongoDate(strtotime(date('Y-m-d')));
        $arr_save['payment_due_date'] = $arr_save['salesorder_date'];
        $this->Salesorder->arr_default_before_save = $arr_save;
        if ($this->Salesorder->add()) {
            echo M_URL .'/salesorders/entry/'. $this->Salesorder->mongo_id_after_save;
        }else
            echo M_URL . '/salesorders/entry';
        die;
    }

    function salesorders_delete($id) {
        $this->selectModel('Salesorder');
    	$this->Salesorder->save(array('_id' => new MongoId($id),'deleted' => true));
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
                                                'arr_where' => array('contact_id'=>new MongoId($id)),
                                                'arr_order' => array('invoice_date' => -1),
                                                'arr_field' => array('code','customer_po_no','invoice_date','invoice_status','our_rep','other_comment','sum_amount'),
                                                'skip' => $offset,
                                                'limit'  => 10
                                                ));
        $arr_data = array('data'=>array());
		foreach($arr_invoices as $invoice){
			$receipts = $this->Receipt->collection->aggregate(
                array(
                    '$match'=>array(
                                    'contact_id' => new MongoID($id),
                                    'deleted'=> false
                                    ),
                ),
                array(
                    '$unwind'=>'$allocation',
                ),
                 array(
                    '$match'=>array(
                                    'allocation.deleted'=> false,
                                    'allocation.salesinvoice_id' => $invoice['_id']
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
            if($invoice['invoice_status'] == 'Credit' && $invoice['sum_amount'] > 0)
                $invoice['sum_amount'] = (float)$invoice['sum_amount'] * -1;
            $invoice['total_receipt']= $total_receipt;
            $invoice['balance'] = $invoice['sum_amount'] - (float)$invoice['total_receipt'];
			$invoice['_id'] = (string)$invoice['_id'];
			$invoice['customer_po_no'] = isset($invoice['customer_po_no']) ? $invoice['customer_po_no'] : '';
			$invoice['our_rep'] = isset($invoice['our_rep']) ? $invoice['our_rep'] : '';
			$invoice['other_comment'] = isset($invoice['other_comment']) ? $invoice['other_comment'] : '';
			$invoice['invoice_date'] = date('d M, Y', $invoice['invoice_date']->sec);
			$invoice['sum_amount'] = number_format((float)$invoice['sum_amount'],2);
			$invoice['total_receipt'] = number_format((float)$invoice['total_receipt'],2);
			$invoice['balance'] = number_format((float)$invoice['balance'],2);
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
	                           'customer_po_no'=>array(
	                                       'label' => 'PO#',
	                                       'type' => '',
	                                       ),
	                           'invoice_date'=>array(
	                                       'label' => 'Date',
	                                       'type' => ''),
	                           'invoice_status'=>array(
	                                       'label' => 'Status',
	                                       'type' => ''),
	                           'status'=>array(
	                                       'label' => 'Status',
	                                       'type' => ''),
	                           'our_rep'=>array(
	                                       'label' => 'Our rep',
	                                       'type' => '',
	                                       ),
	                           'other_comment'=>array(
	                                       'label' => 'Comments',
	                                       'type' => '',
	                                       ),
	                           'sum_amount'=>array(
	                                       'label' => 'Total',
	                                       'type' => '',
	                                       ),
	                           'total_receipt'=>array(
	                                       'label' => 'Receipts',
	                                       'type' => '',
	                                       ),
	                           'balance'=>array(
	                                       'label' => 'Balance',
	                                       'type' => '',
	                                       ),
	                           );
			$arr_data['header'] = array(
			                        'link_to_entry' => M_URL.'/salesinvoices/entry/',
			                        'link_to_entry_value' => 'code',
			                        'info' => 'customer_po_no'
			                            );
			$this->set('arr_data',$arr_data);
			$this->set('id',$id);
        }
    }

    function invoices_add(){
    	$contact_id = $this->get_id();
        $this->selectModel('Contact');
        $arr_contact = $this->Contact->select_one(array('_id' => new MongoId($contact_id)),array('name','email','phone','contact_default_id','addresses_default_key','addresses','our_csr_id','our_csr','our_rep_id','our_rep'));
    	$arr_save = $this->get_contact_info($arr_contact);

    	$this->selectModel('Salesaccount');
        $salesaccount = $this->Salesaccount->select_one(array('contact_id' => new MongoId($contact_id)));
        if( isset($salesaccount['_id']) ){
            $arr_save['payment_terms'] = $salesaccount['payment_terms'];
            $arr_save['tax'] = $salesaccount['tax_code'];
        }

    	$this->selectModel('Salesinvoice');
        $arr_save['code'] = $this->Salesinvoice->get_auto_code('code');
        $arr_save['name'] = $arr_save['code'].' - '.$arr_save['contact_name'];
        $arr_save['status'] = 'In Progress';
        $arr_save['salesorder_date'] = new MongoDate(strtotime(date('Y-m-d')));
        $this->Salesinvoice->arr_default_before_save = $arr_save;//die('123');
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

}