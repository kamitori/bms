<?php
class CompaniesController extends MobileAppController {
	var $modelName = 'Company';
	var $name = 'Companies';
	function beforeFilter() {
		parent::beforeFilter();
		$this->selectModel('Company');$id = $this->get_id();
        $this_company = $this->Company->select_one(array('_id' => new MongoId($id)), array('is_customer', 'is_supplier'));
        
		if(!$this->request->is('ajax'))
			if ($this_company['is_customer'] == 1 && $this_company['is_supplier'] == 1)
				$this->set('arr_tabs', array(
				           			'contacts',
				           			'addresses',
				           			'enquiries',
				           			'jobs',
				           			'tasks',
				           			'docs',
				           			'quotes',
				           			'shippings',
				           			//'rfq',
				           			'products',
				           			'communications',
				           			//'other',
				           			'orders' => array('salesorders','purchases'),
				           			'account' => array('invoices','account'),
				           			));
			elseif ($this_company['is_customer'] == 0 && $this_company['is_supplier'] == 1)
				$this->set('arr_tabs', array(
				           			'contacts',
				           			'addresses',
				           			'enquiries',
				           			'jobs',
				           			'tasks',
				           			'docs',
				           			'quotes',
				           			'shippings',
				           			'products',
				           			'communications',
				           			//'other',
				           			'orders' => array('purchases'),
				           			'account' => array('invoices','account'),
				           			));
			else 
				$this->set('arr_tabs', array(
				           			'contacts',
				           			'addresses',
				           			'enquiries',
				           			'jobs',
				           			'tasks',
				           			'docs',
				           			'quotes',
				           			'shippings',
				           			'products',
				           			'communications',
				           			//'other',
				           			'orders' => array('salesorders'),
				           			'account' => array('invoices','account'),
				           			));
	}

	public function add() {
		$this->selectModel('Company');
		$arr_save = array();

		$this->Company->arr_default_before_save = $arr_save;
		if ($this->Company->add())
			$this->redirect('/mobile/companies/entry/' . $this->Company->mongo_id_after_save);
		die;
	}

	public function popup($key = "") {
		$this->set('key', $key);// shipper_name

		$limit = 10; $skip = 0;
		$page_num = 1;
		if( isset($_POST['pagination']) && $_POST['pagination']['page-num'] > 0){
			$page_num = $_POST['pagination']['page-num'];
			$skip = $limit*($page_num - 1);
		}
		$this->set('page_num', $page_num);

		$arr_order = array('no' => 1);
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

		$cond = array();
		$cond['inactive'] = 0;
		if (!empty($this->data)) {
			$arr_post = $this->data['Company'];

			if (strlen($arr_post['name']) > 0)
				$cond['name'] = new MongoRegex('/'. (string)$arr_post['name'] .'/i');

			if( isset($arr_post['inactive']) && $arr_post['inactive'] )
				$cond['inactive'] = 1;

			if ( isset($arr_post['is_customer']) &&is_numeric($arr_post['is_customer']) && $arr_post['is_customer']) {
				$cond['is_customer'] = 1;
			}

			if ( isset($arr_post['is_supplier']) &&is_numeric($arr_post['is_supplier']) && $arr_post['is_supplier']) {
				$cond['is_supplier'] = 1;
			}

			if (isset($arr_post['is_shipper'])) {
				$cond['is_shipper'] = 1;
				$this->set( 'is_shipper', 1 );
			}
		}

		if (!empty($_GET)) {

			$tmp = $this->data;

			if (isset($_GET['is_customer'])) {
				$cond['is_customer'] = 1;
				$tmp['Company']['is_customer'] = 1;
			}

			if (isset($_GET['is_supplier'])) {
				$cond['is_supplier'] = 1;
				$tmp['Company']['is_supplier'] = 1;
			}

			if (isset($_GET['name'])) {

				$cond['name'] = new MongoRegex('/'. $_GET['name'] .'/i');
				$tmp['Company']['name'] = $_GET['name'];
			}
		
			if (isset($_GET['is_shipper']) && is_numeric($_GET['is_shipper']) && $_GET['is_shipper']) {
				$cond['is_shipper'] = 1;
				$this->set( 'is_shipper', 1 );
			}

			$this->data = $tmp;
		}

		$this->selectModel('Company');
		$arr_companies = $this->Company->select_all(array(
			'arr_where' => $cond,
			'arr_order' => $arr_order,
			'arr_field' => array('no','name', 'addresses', 'addresses_default_key', 'is_customer', 'is_supplier'),
			'limit' => 10,
			'skip' => $skip
		));
		/*$i = 0;
		foreach ($arr_companies as $key => $value) {
			pr($value);
			$i++;
		}
		echo $i;
		die;*/
		$this->set('arr_company', $arr_companies);

		$this->set('limit', 10);
		$total_page = $total_record = $total_current = 0;
		if( is_object($arr_companies) ){
			$total_current = $arr_companies->count(true);
			$total_record = $arr_companies->count();
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

	public function entry($id = '0', $num_position = -1) {
		$arr_tmp = $this->entry_init($id, $num_position, 'Company', 'companies');
		//pr($arr_tmp);die;

		$this->selectModel('Setting');

		$this->set('salesaccounts_status', $this->Setting->select_option(array('setting_value' => 'salesaccounts_status'), array('option')));
		$this->set('arr_priority', $this->Setting->select_option(array('setting_value' => 'lists_priority'), array('option')));
		$this->set('arr_country', $this->Setting->select_option(array('setting_value' => 'lists_country'), array('option')));

		$arr_tmp1['Company'] = $arr_tmp;
		$this->data = $arr_tmp1;
		$this->selectModel('Country');
		$options = array();
		$options['countries'] = $this->Country->get_countries();
		$this->selectModel('Province');
		$options['provinces'] = $this->Province->get_all_provinces();
		$this->set('options',$options);
	}

	function auto_save( $field = '' ) {
		if (!empty($this->data)) {
			$arr_post_data = $this->data['Company'];
			$arr_save = $arr_post_data;

			$this->selectModel('Company');
			$arr_tmp = $this->Company->select_one(array('no' => (int) $arr_save['no'], '_id' => array('$ne' => new MongoId($arr_save['_id']))));
			if (isset($arr_tmp['no'])) {
				echo 'ref_no_existed';
				die;
			}

			$field = str_replace(array('data[Company][', ']'), '', $field);

			if (!isset($arr_save['inactive']))
				$arr_save['inactive'] = 0;
			if (!isset($arr_save['is_supplier']))
				$arr_save['is_supplier'] = 0;
			if (!isset($arr_save['is_customer']))
				$arr_save['is_customer'] = 0;


			if ( isset($arr_save['our_rep_id']) && strlen(trim($arr_save['our_rep_id'])) > 0)
				$arr_save['our_rep_id'] = new MongoId($arr_save['our_rep_id']);

			$this->selectModel('Company');
			if ($this->Company->save($arr_save)) {
				if( isset($check_reload) ){
					$this->layout = 'ajax';
					$arr_post_data['Company'] = $arr_post_data;
					$this->data = $arr_post_data;
					$this->render('entry_udpate_date');
				}else{
					echo 'ok';die;
				}

			} else {
				echo 'Error: ' . $this->Company->arr_errors_save[1];
				die;
			}
		}
	}

	function lists( $content_only = '' ) {
		$this->selectModel('Company');
		$limit = 20; $skip = 0;
		// dùng cho sort
		$sort_field = 'no';
		$sort_type = -1;
		if( isset($_POST['sort']) && strlen($_POST['sort']['field']) > 0 ){
			if( $_POST['sort']['type'] == 'desc' ){
				$sort_type = -1;
			}
			$sort_field = $_POST['sort']['field'];
			$this->Session->write('companies_lists_search_sort', array($sort_field, $sort_type));

		}elseif( $this->Session->check('companies_lists_search_sort') ){
			$session_sort = $this->Session->read('companies_lists_search_sort');
			$sort_field = $session_sort[0];
			$sort_type = $session_sort[1];
		}
		$arr_order = array($sort_field => $sort_type);
		$this->set('sort_field', $sort_field);
		$this->set('sort_type', ($sort_type === 1)?'asc':'desc');

		$this->selectModel('Company');
		$this->set('model_company', $this->Company);

		$this->selectModel('Contact');
		$this->set('model_contact', $this->Contact);

		// dùng cho điều kiện
		$cond = array();
		if( $this->Session->check('companies_entry_search_cond') ){
			$cond = $this->Session->read('companies_entry_search_cond');
		}

		// dùng cho phân trang
		if(isset($_POST['offset'])){
			$skip = $_POST['offset'];
		}
		// query
		$arr_companies = $this->Company->select_all(array(
			'arr_where' => $cond,
			'arr_order' => $arr_order,
			'limit' => $limit,
			'skip' => $skip
		));
		$this->set('arr_companies', $arr_companies);
		$this->selectModel('Setting');
		$this->set('arr_companies_type', $this->Setting->select_option(array('setting_value' => 'companies_type'), array('option')));
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

	public function contacts(){
		if(isset($_POST['add'])){
			return $this->contacts_add();
		}
		$offset = 0;
		if(isset($_POST['offset']))
			$offset = $_POST['offset'];
		$id = $this->get_id();
        $this->selectModel('Contact');
        $arr_contact = $this->Contact->select_all(array(
                                                  'arr_where' => array('company_id'=>new MongoId($id)),
                                                  'arr_field' => array('title','first_name','last_name','contact_default','position','direct_dial','extension_no','email','inactive','mobile','no'),
                                                  'arr_order' => array('_id' => 1),
                                                  'limit' => 10,
                                                  'skip' => $offset
                                                  ));
        if($this->request->is('ajax')){
        	if($arr_contact->count(true) == 0){
        		echo json_encode(array('empty'=>true));
        		die;
        	}
			foreach($arr_contact as $contact){
				$contact['_id'] = (string)$contact['_id'];
				$arr_data['data'][] = $contact;
			}
			echo json_encode($arr_data);
			die;
        } else {
        	$this->selectModel('Company');
	        $arr_company = $this->Company->select_one(array('_id' => new MongoId($id)), array('_id', 'contact_default_id', 'name'));
	        $arr = array(0=>array());
	        foreach($arr_contact as $key => $value){
	            if($value['_id']==$arr_company['contact_default_id']){
	                $value['contact_default'] = 1;
	                $arr[0] = $value;
	            }
	            else
	                $arr[] = $value;
	        }
	        if(empty($arr[0]))
	            unset($arr[0]);
	        $this->set('arr_contacts',$arr);

	        $arr_data['field'] = array(
        	                           'no'=>array(
        	                                       'label' => 'No',
        	                                       'type' => ''),
        	                           'title'=>array(
        	                                       'label' => 'Title',
        	                                       'type' => 'select',
        	                                       'options' => 'contacts_title'
        	                                       ),
        	                           'first_name'=>array(
        	                                       'label' => 'First Name',
        	                                       'type' => 'text'),
        	                           'last_name'=>array(
        	                                       'label' => 'Last Name',
        	                                       'type' => 'text'),
        	                           'contact_default'=>array(
        	                                       'label' => 'Default',
        	                                       'type' => 'checkbox'),
        	                           'position'=>array(
        	                                       'label' => 'Position',
        	                                       'type' => 'select',
        	                                       'options' => 'contacts_position'
        	                                       ),
        	                           'direct_dial'=>array(
        	                                       'label' => 'Direct dial',
        	                                       'type' => 'text'),
        	                           'extension_no'=>array(
        	                                       'label' => 'Ext no',
        	                                       'type' => 'text'),
        	                           'mobile'=>array(
        	                                       'label' => 'Mobile',
        	                                       'type' => 'text'),
        	                           'email'=>array(
        	                                       'label' => 'Email address',
        	                                       'type' => 'text'),
        	                           'inactive'=>array(
        	                                       'label' => 'Inactive',
        	                                       'type' => 'checkbox'),
        	                           );
			$arr_data['header'] = array(
			                        'link_to_entry' => M_URL.'/contacts/entry/',
			                        'link_to_entry_value' => 'no',
			                        'info' => 'first_name last_name'
			                            );
			$this->set('arr_data',$arr_data);
			$this->set('id',$id);
        }
        $this->selectModel('Setting');
		$options['contacts_title'] = $this->Setting->select_option(array('setting_value' => 'contacts_title'), array('option'));
		$options['contacts_position'] = $this->Setting->select_option(array('setting_value' => 'contacts_position'), array('option'));
		$this->set('options', $options);
    }

	function contacts_delete($contact_id) {
        $arr_save['_id'] = new MongoId($contact_id);
        $arr_save['deleted'] = true;
        $this->selectModel('Contact');
        if ($this->Contact->save($arr_save)) {
            echo 'ok';
        } else {
            echo 'Error: ' . $this->Contact->arr_errors_save[1];
        }
        die;
    }

    function contacts_add(){
    	$id  = $this->get_id();
    	$this->selectModel('Company');
    	$company = $this->Company->select_one(array('_id' => new MongoId($id)),array('name','addresses_default_key','addresses', 'system', 'fax', 'phone','is_customer'));
    	$arr_save = array(
    	                'company_id' 	=> new MongoId($id),
    	                'Company'		=> $company['name']
    	                  );
    	if( isset($company['system']) && $company['system'] ){
            $arr_save['is_customer'] = 0;
            $arr_save['is_employee'] = 1;
        } else if(isset($company['is_customer']) && $company['is_customer']){
            $arr_save['is_customer'] = 1;
            $arr_save['is_employee'] = 0;
        }
        $addresses_default_key = 0;
        if(isset($company['addresses_default_key']))
        	$addresses_default_key = $company['addresses_default_key'];
        $arr_save['fax'] = $company['fax'];
        $arr_save['company_phone'] = $company['phone'];
        $arr_save['addresses'] = array(
            array(
                'deleted' => false,
                'default' => true,
                'country' => $company['addresses'][$addresses_default_key]['country'],
                'country_id' => $company['addresses'][$addresses_default_key]['country_id'],
                'province_state' => $company['addresses'][$addresses_default_key]['province_state'],
                'province_state_id' => $company['addresses'][$addresses_default_key]['province_state_id'],
                'address_1' => $company['addresses'][$addresses_default_key]['address_1'],
                'address_2' => $company['addresses'][$addresses_default_key]['address_2'],
                'address_3' => $company['addresses'][$addresses_default_key]['address_3'],
                'town_city' => $company['addresses'][$addresses_default_key]['town_city'],
                'zip_postcode' => $company['addresses'][$addresses_default_key]['zip_postcode']
            )
        );
        $arr_save['addresses_default_key'] = 0;

    	$this->selectModel('Contact');
    	$this->Contact->arr_default_before_save = $arr_save;
        $this->Contact->add();

        $count = $this->Contact->count(array('company_id' => new MongoId($id),'deleted'=>false));
        if($count == 1){
        	$this->Company->save(array(
        	                     	'_id' 					=> new MongoId($id),
        	                     	'contact_default_id' 	=> $this->Contact->mongo_id_after_save
        	                     ));
        }
        echo json_encode(array(
                         	array(
                         	      '_id' => (string)$this->Contact->mongo_id_after_save,
                         	      'no'	=> $this->Contact->arr_temp['no']
                         	      )
                         ));
        die;
    }

    public function addresses(){
    	if(isset($_POST['add'])){
    		return $this->addresses_add();
    	}
    	$this->selectModel('Company');
    	$company = $this->Company->select_one(array('_id' => new MongoId($this->get_id())),array('addresses','addresses_default_key'));
    	if(!isset($company['addresses_default_key']))
    		$company['addresses_default_key'] = 0;
        $this->set('arr_addresses',$company['addresses']);
        $this->set('addresses_default_key',$company['addresses_default_key']);

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
    	$this->selectModel('Company');
    	$company = $this->Company->select_one(array('_id' => new MongoId($this->get_id())),array('addresses'));
    	if(!isset($company['addresses']))
    		$company['addresses'] = array();
    	$i = 0;
    	foreach($company['addresses'] as $address){
    		if(isset($address['deleted']) && $address['deleted']) continue;
    		$i++;
    	}
    	$company['addresses'][] = array(
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
    	$this->Company->save($company);
    	echo json_encode(array(
    	                 	array(
    	                 	      '_id' => $i,
    	                          'country_id' => 'CA',
    	                 	      )
    	                 ));
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
        $arr_jobs = $this->Job->select_all(array(
                                          'arr_where'=>array('company_id'=>new MongoId($id)),
                                          'arr_field' => array('no','name','type','work_start','work_end','status','contact_name','contact_id'),
                                          'arr_order' => array('work_start' => -1),
                                          'limit' => 10,
                                          'skip' => $offset
                                          ));
        $arr_data = array('data'=>array());
		foreach($arr_jobs as $job){
			$job['_id'] = (string)$job['_id'];
			$job['contact_name'] = isset($job['contact_name']) ? $job['contact_name'] : '';
			$job['work_start'] = date('d M, Y', $job['work_start']->sec);
			$job['work_end'] = date('d M, Y', $job['work_end']->sec);
			$arr_data['data'][] = $job;
		}
        if($this->request->is('ajax')){
        	if($arr_jobs->count(true) == 0){
        		echo json_encode(array('empty'=>true));
        		die;
        	}
			echo json_encode($arr_data);
			die;
        } else {
	        $this->set('arr_contacts',$arr_data['data']);
        	$arr_data['field'] = array(
	                           'no'=>array(
	                                       'label' => 'Job no',
	                                       'type' => '',),
	                           'name'=>array(
	                                       'label' => 'Job heading',
	                                       'type' => '',
	                                       ),
	                           'type'=>array(
	                                       'label' => 'Job type',
	                                       'type' => ''),
	                           'work_start'=>array(
	                                       'label' => 'Start',
	                                       'type' => ''),
	                           'work_end'=>array(
	                                       'label' => 'Finish',
	                                       'type' => ''),
	                           'status'=>array(
	                                       'label' => 'Status',
	                                       'type' => '',
	                                       ),
	                           'contact_name'=>array(
	                                       'label' => 'Job manager',
	                                       'type' => '',),
	                           );
			$arr_data['header'] = array(
			                        'link_to_entry' => M_URL.'/jobs/entry/',
			                        'link_to_entry_value' => 'no',
			                        'info' => 'name'
			                            );
			$this->set('arr_data',$arr_data);
			$this->set('id',$id);
        }
    }

    function jobs_add() {
    	$id  = $this->get_id();
        $this->selectModel('Company');
        $arr_company = $this->Company->select_one(array('_id' => new MongoId($id)),array('name','phone','fax','email','contact_default_id'));
        $this->selectModel('Job');
        $arr_tmp = $this->Job->select_one(array(), array(), array('no' => -1));
        $arr_save = array();
        $arr_save['no'] = 1;
        if (isset($arr_tmp['no'])) {
            $arr_save['no'] = $arr_tmp['no'] + 1;
        }

        $arr_save['company_id'] = $arr_company['_id'];
        $arr_save['name'] = '';
        $arr_save['contacts_default_key'] = 0;
        $arr_save['contacts'][] = array(
            "contact_name" => $_SESSION['arr_user']['contact_name'],
            "contact_id" => $_SESSION['arr_user']['contact_id'],
            "default" => true,
            "deleted" => false
        );
        $arr_save['type'] = '';
        $arr_save['status'] = 'New';


        $arr_save['company_name'] = isset($arr_company['name'])?$arr_company['name']:'';
        $arr_save['company_id'] = $arr_company['_id'];
        $arr_save['company_phone'] = isset($arr_company['phone'])?$arr_company['phone']:'';
        $arr_save['fax'] = isset($arr_company['fax'])?$arr_company['fax']:'';
        $arr_save['email']=isset($arr_company['email'])?$arr_company['email']:'';

        $this->selectModel('Contact');
        if (isset($arr_company['contact_default_id'])) {
            $arr_contact = $this->Contact->select_one(array('_id' => $arr_company['contact_default_id']),array('first_name','last_name','email','direct_dial','mobile'));
        }
        else{
            $arr_contact = $this->Contact->select_one(array('company_id' => new MongoId($id)),array('first_name','last_name','email','direct_dial','mobile'),array('_id' => -1));

        }
        if(isset($arr_contact['_id'])){
            $arr_save['contact_id'] = $arr_contact['_id'];
            $arr_save['contact_name'] = $arr_contact['first_name'] . ' ' . $arr_contact['last_name'];
        }
        if(isset($arr_contact['email'])&&$arr_contact['email']!='')
            $arr_save['email'] = $arr_contact['email'];
        if(isset($arr_contact['direct_dial'])&&$arr_contact['direct_dial']!='')
            $arr_save['direct_phone'] = $arr_contact['direct_dial'];

        if(isset($arr_contact['mobile'])&&$arr_contact['mobile']!='')
            $arr_save['mobile'] = $arr_contact['mobile'];
        if (isset($work_start_sec) && $work_start_sec > 0) {
            $arr_save['work_end'] = $arr_save['work_start'] = new MongoDate($work_start_sec);
        } else {
            $arr_save['work_end'] = $arr_save['work_start'] = new MongoDate(strtotime(date('Y-m-d H:00:00')) + 3600);
        }
        if(isset($arr_company['addresses_default_key'])){
            $key = $arr_company['addresses_default_key'];
            foreach($arr_company['addresses'][$key] as $k=>$value)
                $arr_save['invoice_'.$k] = $value;
        }

        $this->Job->arr_default_before_save = $arr_save;
        if ($this->Job->add()) {
          echo M_URL .'/jobs/entry/'. $this->Job->mongo_id_after_save;
        }
        else
          echo M_URL . '/jobs/entry';
        die;
    }

    function jobs_delete($job_id){
    	$this->selectModel('Job');
    	$this->Job->save(array('_id' => new MongoId($job_id),'deleted' => true));
    	echo 'ok';
    	die;
    }

    public function tasks(){
    	$id = $this->get_id();
    	if(isset($_POST['add']))
    		return $this->tasks_add($id);
    	$offset = 0;
    	if(isset($_POST['offset']))
    		$offset = $_POST['offset'];
    	$this->selectModel('Task');
    	$arr_tasks = $this->Task->select_all(array(
    	                        'arr_where' => array('company_id' => new MongoId($id),),
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

    function tasks_add($company_id) {
    	$id = $company_id;
        $this->selectModel('Company');
        $arr_company = $this->Company->select_one(array('_id' => new MongoId($id)),array('our_rep_id','our_rep','name','contact_default_id'));
        $arr_save = array();
        $arr_save['company_name'] = $arr_company['name'];
        $arr_save['company_id'] = $arr_company['_id'];
        if (isset($arr_company['our_rep_id']) && $arr_company['our_rep_id'] != '' && $arr_company['our_rep_id']!=null) {
            $arr_save['our_rep_id'] = $arr_company['our_rep_id'];
            $arr_save['our_rep'] = $arr_company['our_rep'];
        }
        $arr_save['contact_id'] = $arr_save['contact_name'] = '';
        if (isset($arr_company['contact_default_id']) && is_object($arr_company['contact_default_id'])) {
            $this->selectModel('Contact');
            $arr_contact = $this->Contact->select_one(array('_id' => $arr_company['contact_default_id']),array('first_name','first_name'));
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
                                                'arr_where' => array('company_id'=>new MongoId($id)),
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
    	$company_id = $this->get_id();
        $this->selectModel('Company');
        $arr_company = $this->Company->select_one(array('_id' => new MongoId($company_id)),array('name','email','phone','contact_default_id','addresses_default_key','addresses','our_csr_id','our_csr','our_rep_id','our_rep'));
        $arr_save = $this->get_company_info($arr_company);
        $this->selectModel('Quotation');
        $arr_save['code'] = $this->Quotation->get_auto_code('code');
        $arr_save['name'] = $arr_save['code'].' - '.$arr_save['company_name'];
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

    public function salesorders(){
    	if(isset($_POST['add']))
    	   return $this->salesorders_add();
    	$offset = 0;
    	if(isset($_POST['offset']))
    		$offset = $_POST['offset'];
    	$id = $this->get_id();
        $this->selectModel('Salesorder');
        $arr_orders = $this->Salesorder->select_all(array(
                                                'arr_where' => array('company_id'=>new MongoId($id)),
                                                'arr_order' => array('salesorder_date' => -1),
                                                'arr_field' => array('code','sales_order_type','salesorder_date','payment_due_date','status','our_rep','our_csr','sum_sub_total','heading'),
                                                'skip' => $offset,
                                                'limit'  => 10
                                                ));
        $sum_total = 0;
        $total_order = 0;
        foreach ($arr_orders as $key => $value) {
        	if ($value['status']=="Cancelled") $value['sum_sub_total'] = 0;
        	$sum_total = $sum_total + $value['sum_sub_total'];
        	$total_order++;
        }
        /*pr($sum_total);
        die;*/
        $arr_data = array('data'=>array());
		foreach($arr_orders as $order){
			if ($order['status']=="Cancelled") $order['sum_sub_total'] = 0;
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
			$this->set('arr_orders',$arr_data['data']);
			$this->set('arr_data',$arr_data);
			$this->set('id',$id);
			$this->set('sum_total',$sum_total);
			$this->set('total_order',$total_order);
        }
    }

    function salesorders_add(){
    	$company_id = $this->get_id();
        $this->selectModel('Company');
        $arr_company = $this->Company->select_one(array('_id' => new MongoId($company_id)),array('name','email','phone','contact_default_id','addresses_default_key','addresses','our_csr_id','our_csr','our_rep_id','our_rep'));
    	$arr_save = $this->get_company_info($arr_company);
    	$this->selectModel('Salesorder');
        $arr_save['code'] = $this->Salesorder->get_auto_code('code');
        $arr_save['name'] = $arr_save['code'].' - '.$arr_save['company_name'];
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

    public function shippings(){
    	if(isset($_POST['add']))
    	   return $this->shippings_add();
    	$offset = 0;
    	if(isset($_POST['offset']))
    		$offset = $_POST['offset'];
    	$id = $this->get_id();
        $this->selectModel('Shipping');
        $arr_shippings = $this->Shipping->select_all(array(
                                                'arr_where' => array('company_id'=>new MongoId($id)),
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
    	$company_id = $this->get_id();
        $this->selectModel('Company');
        $arr_company = $this->Company->select_one(array('_id' => new MongoId($company_id)),array('name','email','phone','contact_default_id','addresses_default_key','addresses','our_csr_id','our_csr','our_rep_id','our_rep','is_customer','is_supplier'));
    	$arr_save = $this->get_company_info($arr_company);
    	$this->selectModel('Shipping');
        $arr_save['code'] = $this->Shipping->get_auto_code('code');
        $arr_save['name'] = $arr_save['code'].' - '.$arr_save['company_name'];
        if($arr_company['is_customer']==0&&$arr_company['is_supplier']==1)
            $arr_save['shipping_type'] = 'In';
        elseif($arr_company['is_customer']==1&&$arr_company['is_supplier']==0)
            $arr_save['shipping_type'] = 'Out';
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

    function get_tax_company($arr_data){
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

    function get_company_info($arr_company){
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
            $arr_save['invoice_address'][0] = array(
                'deleted' => false,
                'invoice_address_1' => $arr_company['addresses'][$key_default]['address_1'],
                'invoice_address_2' => $arr_company['addresses'][$key_default]['address_2'],
                'invoice_address_3' => $arr_company['addresses'][$key_default]['address_3'],
                'invoice_town_city' => $arr_company['addresses'][$key_default]['town_city'],
                'invoice_province_state' => $arr_company['addresses'][$key_default]['province_state'],
                'invoice_province_state_id' => $arr_company['addresses'][$key_default]['province_state_id'],
                'invoice_zip_postcode' => $arr_company['addresses'][$key_default]['zip_postcode'],
                'invoice_country' => $arr_company['addresses'][$key_default]['country'],
                'invoice_country_id' => $arr_company['addresses'][$key_default]['country_id']
            );
        }elseif(isset($arr_company['addresses'][0])){
            $arr_save['invoice_address'][0] = array(
                'deleted' => false,
                'invoice_address_1' => $arr_company['addresses'][0]['address_1'],
                'invoice_address_2' => $arr_company['addresses'][0]['address_2'],
                'invoice_address_3' => $arr_company['addresses'][0]['address_3'],
                'invoice_town_city' => $arr_company['addresses'][0]['town_city'],
                'invoice_province_state' => $arr_company['addresses'][0]['province_state'],
                'invoice_province_state_id' => $arr_company['addresses'][0]['province_state_id'],
                'invoice_zip_postcode' => $arr_company['addresses'][0]['zip_postcode'],
                'invoice_country' => $arr_company['addresses'][0]['country'],
                'invoice_country_id' => $arr_company['addresses'][0]['country_id']
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
        $arr_save = array_merge($arr_save, $this->get_tax_company($arr_save));
        return $arr_save;
    }

    public function before_save($field,$value,$id, $options = array()){
    	$arr_return = array();
    	if($field == 'addresses'){
    		$arr_data = $value['addresses'][$options['key']];
    		if($arr_data['default'] == 1){
    			foreach($value['addresses'] as $k => $v){
    				if($k == $options['key']) continue;
    				if($v['deleted']) continue;
    				$value['addresses'][$k]['default'] = 0;
    			}
    			$value['addresses'][$options['key']]['default'] = 1;
    			$arr_return['addresses'] = $value['addresses'];
    			$arr_return['addresses_default_key'] = $options['key'];
    		}
    	}
    	return $arr_return;
    }
    function entry_search(){
    	$this->selectModel('Setting');

		$this->set('salesaccounts_status', $this->Setting->select_option(array('setting_value' => 'salesaccounts_status'), array('option')));
		$this->set('arr_priority', $this->Setting->select_option(array('setting_value' => 'lists_priority'), array('option')));
		$this->set('arr_country', $this->Setting->select_option(array('setting_value' => 'lists_country'), array('option')));
    }

    function enquiries_add(){
    	$id  = $this->get_id();
    	$this->selectModel('Enquiry');
    	$this->selectModel('Company');
    	$company = $this->Company->select_one(array('_id' => new MongoId($id)),array('name', 'phone'));
    	$arr_save = array(
    	                'company_id' 	=> new MongoId($id),
    	                'company'	=>  $company['name']
    	                  );

        $arr_save['company_phone'] = $company['phone'];
		$arr_save['no'] = $this->Enquiry->get_auto_code('no');
        $this->Enquiry->arr_default_before_save = $arr_save;
        if ($this->Enquiry->add()) {
           echo M_URL.'/enquiries/entry/'. $this->Enquiry->mongo_id_after_save;
        }else
           echo M_URL .'/enquiries/entry';
        die;
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
                                                  'arr_where' => array('company_id'=>new MongoId($id)),
                                                  'arr_field' => array('no','date','status','company_id','company_name','our_rep','referred','referred_id','enquiry_value','mobile','no'),
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
        	$this->selectModel('Company');
	        $arr_company = $this->Company->select_one(array('_id' => new MongoId($id)), array('_id', 'first_name', 'last_name','enquiry_id'));
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
        	                                       'label' => 'Referred',
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
                                                'arr_where' => array('company_id'=>new MongoId($id)),
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
                                    'company_id' => new MongoID($id),
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
    	$company_id = $this->get_id();
        $this->selectModel('Company');
        $arr_company = $this->Company->select_one(array('_id' => new MongoId($company_id)),array('name','email','phone','contact_default_id','addresses_default_key','addresses','our_csr_id','our_csr','our_rep_id','our_rep'));
    	$arr_save = $this->get_company_info($arr_company);
    	$this->selectModel('Salesinvoice');
        $arr_save['code'] = $this->Salesinvoice->get_auto_code('code');
        $arr_save['name'] = $arr_save['code'].' - '.$arr_save['company_name'];
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

    function account_auto_save($company_id){
        if (!empty($this->data)) {
            $this->selectModel('Salesaccount');
            $arr_save = $this->Salesaccount->select_one(array('company_id' => new MongoId($company_id)));
            foreach ($this->data['Salesaccount'] as $key => $value) {
                if( $key == 'balance' )continue;
                $arr_save[$key] = $value;
                if( $key == 'credit_limit' || $key == 'quotation_limit' ){
                    $ext = '';
                    if( substr($value, -3, 1) == '.' ){
                        $ext = substr($value, -3);
                        $value = substr($value, 0, -3);
                    }
                    $arr_save[$key] = (float)(str_replace(array(',', '.'), '', $value).$ext);
                }
            }
            if ($this->Salesaccount->save($arr_save)) {
                // cập nhật lại toàn bộ khung html------------Account Related-----------
                $arr_tmp = $arr_save;
                $arr_tmp['Salesaccount'] = $arr_save;
                if( strlen($arr_tmp['Salesaccount']['credit_limit']) > 0 ){
                    $arr_tmp['Salesaccount']['difference'] = $this->opm->format_currency($arr_tmp['Salesaccount']['credit_limit'] - $arr_tmp['Salesaccount']['balance']);
                }
                $arr_tmp['Salesaccount']['balance'] = (is_numeric($arr_tmp['Salesaccount']['balance']))?$this->opm->format_currency($arr_tmp['Salesaccount']['balance']):'';
                $arr_tmp['Salesaccount']['credit_limit'] = (is_numeric($arr_tmp['Salesaccount']['credit_limit']))?$this->opm->format_currency($arr_tmp['Salesaccount']['credit_limit']):'';
                //An them truong quotation limit
                $arr_tmp['Salesaccount']['quotation_limit'] = (is_numeric($arr_tmp['Salesaccount']['quotation_limit']))?$this->opm->format_currency($arr_tmp['Salesaccount']['quotation_limit']):'';
                $this->data = $arr_tmp;
                $this->selectModel('Setting');
                //$this->set( 'arr_status', $this->Setting->select_option(array('setting_value' => 'salesaccounts_status'), array('option')) );
                $this->set( 'arr_payment_terms', $this->Setting->select_option(array('setting_value' => 'salesaccounts_payment_terms'), array('option')) );
                $this->selectModel('Tax');
                $this->set( 'arr_tax_code', $this->Tax->tax_select_list());
                $this->set( 'arr_nominal_code', $this->Setting->select_option(array('setting_value' => 'salesaccounts_nominal_code'), array('option')) );
                echo $this->render('account_related'); die;
                // end cập nhật ---------------------------------------------------

            } else {
                echo 'Error: ' . $this->Salesaccount->arr_errors_save[1];
            }
        }
        die;
    }

    function account(){
    	$company_id = $this->get_id();
        $this->set('company_id',$company_id);
    	$this->selectModel('Setting');
		$this->set('salesaccounts_status', $this->Setting->select_option(array('setting_value' => 'salesaccounts_status'), array('option')));
		$this->set('salesaccounts_payment_terms', $this->Setting->select_option(array('setting_value' => 'salesaccounts_payment_terms'), array('option')));
		$this->set('arr_country', $this->Setting->select_option(array('setting_value' => 'lists_country'), array('option')));

		$this->selectModel('Tax');
		$this->set('arr_tax_code',$this->Tax->tax_select_list());
		$this->set( 'arr_nominal_code', $this->Setting->select_option(array('setting_value' => 'salesaccounts_nominal_code'), array('option')) );

		$this->selectModel('Salesinvoice');
		$this->selectModel('Receipt');
		$obj_salesinvoices = $this->Salesinvoice->select_all(array(
                                     'arr_where' => array('company_id' => new MongoId($company_id)),
                                     'arr_field' => array('code','customer_po_no','invoice_date','invoice_status','our_rep','other_comment','sum_amount','total_receipt'),
                                     'arr_order' => array('invoice_date' => -1)
                                     ));
		$arr_salesinvoices = array();
		$total_balance = 0;
		foreach($obj_salesinvoices as $key => $value){
			$receipts = $this->Receipt->collection->aggregate(
			                                                  array(
			                                                        '$match' => array(
			                                                                          'company_id' => new MongoId($company_id),
			                                                                          'deleted' => false
			                                                                          ),
			                                                        ),
			                                                  array(
			                                                        '$match' => array(
			                                                                          '$unwind' => '$allocation',
			                                                                          ),
			                                                        ),
			                                                  array(
			                                                        '$match' => array(
			                                                                          'allocation.deleted' => false,
			                                                                          'allocation.salesinvoice_id' => $value['_id']
			                                                                          ),
			                                                        ),
			                                                  array(
			                                                        '$project' => array('allocation'=>'$allocation')
			                                                        ),
			                                                  array(
			                                                        '$group' => array(
			                                                                          '_id' => array('_id'=>'$_id'),
			                                                                          'allocation' => array('$push' => '$allocation')
			                                                                          ),
			                                                        )
			                                                  );
			$total_receipt = 0;
			if(isset($receipts['ok']) && $receipts['ok']){
				foreach($receipts['result'] as $receipt){
					foreach($receipt['allocation'] as $allocation){
						$total_receipt += isset($allocation['amount']) ? (float)str_replace(',','', $allocation['amount']) : 0;
					}
				}
			}
			if($value['invoice_status'] == 'Credit' && $value['sum_amount'] > 0)
				$value['sum_amount'] = (float)$value['sum_amount'] * -1;
			$value['total_receipt'] = $total_receipt;
			$value['balance'] = $value['sum_amount'] - (float)$value['total_receipt'];
			$total_balance += $value['balance'];
			$arr_salesinvoices[$key] = $value;
		}

        $this->selectModel('Salesaccount');
        $salesaccount = $this->Salesaccount->select_one(array('company_id' => new MongoId($company_id)));

        if( isset($salesaccount['_id']) ){
            $arr_tmp['Salesaccount'] = $salesaccount;
            $arr_tmp['Salesaccount']['difference'] = 0;
            $arr_tmp['Salesaccount']['balance'] = $total_balance;
            if( strlen($arr_tmp['Salesaccount']['credit_limit']) > 0 ){
                if($arr_tmp['Salesaccount']['balance'] < 0){
                    $tmp_balance = number_format($arr_tmp['Salesaccount']['balance'],2)*(-1);
                    $arr_tmp['Salesaccount']['difference'] = number_format(($arr_tmp['Salesaccount']['credit_limit'] - $tmp_balance),2);
                }else
                    $arr_tmp['Salesaccount']['difference'] = number_format(($arr_tmp['Salesaccount']['credit_limit'] - $arr_tmp['Salesaccount']['balance']),2);
            }
            if( strlen($arr_tmp['Salesaccount']['quotation_limit']) > 0 ){
                $arr_tmp['Salesaccount']['quotation_limit'] = number_format($arr_tmp['Salesaccount']['quotation_limit'],2);
            }

            $arr_tmp['Salesaccount']['credit_limit'] = (is_numeric($arr_tmp['Salesaccount']['credit_limit']))?number_format($arr_tmp['Salesaccount']['credit_limit'],2):'';
            $this->data = $arr_tmp;
        }
    }

    function account_create(){
    	$company_id = $this->get_id();
        $arr_tmp['company_id'] = new MongoId($company_id);
    	$this->selectModel('Salesaccount');
        $this->Salesaccount->arr_default_before_save = $arr_tmp;
        $salesaccount = $this->Salesaccount->add();
        echo $this->Salesaccount->mongo_id_after_save;die;
    }

    public function products(){
    	$this->selectModel('Company');
    	$company = $this->Company->select_one(array('_id' => new MongoId($this->get_id())),array('pricing'));
    	$arr_pricing = array();
    	if(isset($company['pricing']) && is_array($company['pricing'])){
    		foreach($company['pricing'] as $key => $value){
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
    	$this->selectModel('Company');
    	$company = $this->Company->select_one(array('_id' => new MongoId($this->get_id())),array('name','pricing'));
    	if(!isset($company['pricing']) || !isset($company['pricing'][$pricing_key]) || $company['pricing'][$pricing_key]['deleted']){
    		if(isset($_POST['add'])){
    			echo M_URL.'/companies/products';
	    		die;
	    	} else {
    			$this->redirect('/companies/products');
	    	}
    	}
    	$pricing = $company['pricing'][$pricing_key];
    	if(isset($_POST['add'])){
    		$arr_save = array(
                              'deleted' => false,
                              'range_from' => 1,
                              'range_to'	=> 1,
                              'unit_price'	=> (float)0
                            );
    		$pricing['price_break'][] = $arr_save;
    		$company['pricing'][$pricing_key] = $pricing;
    		$this->Company->save($company);
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
    	$arr_data['company_name'] = $company['name'];
    	$arr_data['notes'] = $pricing['notes'];
    	$arr_data['name'] = $pricing['name'];
    	$arr_data['code'] = $pricing['code'];
    	$this->set('arr_data',$arr_data);
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
                                                'arr_where' => array('company_id'=>new MongoId($id)),
                                                'arr_order' => array('purchord_date' => -1),
                                                'arr_field' => array('code','purchord_date','purchase_orders_status','our_rep','our_rep_id','sum_sub_total','company_name','required_date','delivery_date','ship_to_contact_name','name'),
                                                'skip' => $offset,
                                                'limit'  => 10
                                                ));
        $arr_data = array('data'=>array());
        foreach($arr_purchases as $purchase){
            $purchase['_id'] = (string)$purchase['_id'];
            $purchase['required_date'] = date('d M, Y', $purchase['required_date']->sec);
            if (is_object($purchase['delivery_date'])) 
            	$purchase['delivery_date'] = date('d M, Y', $purchase['delivery_date']->sec);
            $purchase['purchase_orders_status'] = isset($purchase['purchase_orders_status']) ? $purchase['purchase_orders_status'] : '';
            $purchase['company_name'] = isset($purchase['company_name']) ? $purchase['company_name'] : '';
            $purchase['our_rep'] = isset($purchase['our_rep']) ? $purchase['our_rep'] : '';
            $purchase['ship_to_contact_name'] = isset($purchase['ship_to_contact_name']) ? $purchase['ship_to_contact_name'] : '';
            $purchase['sum_sub_total'] = isset($purchase['sum_sub_total'])?number_format((float)$purchase['sum_sub_total'],2):0;
            $purchase['name'] = isset($purchase['name']) ? $purchase['name'] : '';
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
        }
    }
    function purchases_add() {
        $company_id = $this->get_id();
        $this->selectModel('Company');
        $arr_company = $this->Company->select_one(array('_id' => new MongoId($company_id)),array('name','company_name','company_phone','our_rep_id','our_rep'));
        //$arr_save = $this->get_company_info($arr_company);
        $this->selectModel('Purchaseorder');
        $arr_save['company_id'] = $arr_company['_id'];
        $arr_save['code'] = $this->Purchaseorder->get_auto_code('code');
        $arr_save['purchord_date'] = new MongoDate(strtotime(date('Y-m-d')));
        $arr_save['purchase_orders_status'] = 'In progress';
        $arr_save['company_name'] = isset($arr_company['company_name'])?$arr_company['company_name']:'';
        if (isset($arr_company['our_rep']) && $arr_company['our_rep']!='')
        	$arr_save['our_rep'] = $arr_company['our_rep'];
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

}