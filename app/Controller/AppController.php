<?php
App::uses('Controller', 'Controller');
class AppController extends Controller {
	var $components = array('Session', 'Common' , 'Cookie');
	var $helpers = array('Html', 'Form', 'Common','Minify.Minify');
	public $viewClass = 'Theme';
	public $theme = 'default';
	var $uses = null;
	var $modelName = '';
	var $arr_permission = null;
	var $arr_inactive_permission = null;
	function isAuthorized($user) {
		return true;
	}
	var $system_admin = false;

	function beforeFilter() {
		ignore_user_abort(true);
		//Tung, auto redirect and use plugin mobile
		/*if (!$this->Session->check('desktop_site') && $this->request->is('mobile'))
			$this->redirect(URL.'/mobile/');*/
		//$this->Session->write('default_lang','vi');
		//An, 21/2/2014 Use for Mobile
		//Minh, bypass session user for controller services
		if(!$this->check_report_pdf() && $this->params->params['controller']!='services'){ //Dùng để bypass SESSION cho phantomjs
			if ($this->request->is('ajax') && !$this->Session->check('arr_user')) {
				echo 'Your session is time out, please re-login. Thank you.<script type="text/javascript">location.reload(true);</script>';
				exit;
			}
			if ($this->request->url != 'users/login' && !$this->Session->check('arr_user')) {
				$this->Session->write('REFERRER_LOGIN', '/' . $this->request->url);
				$this->redirect('/users/login');
				die;
			}
		}

		// $this->theme = 'AnotherExample';
		$this->set('controller', $this->params->params['controller']);
		$this->set('model', $this->modelName);
		$this->set('action', $this->params->params['action']);

		// định nghĩa các thông báo từ hệ thống
		$this->set('arr_msg', $this->define_system_message());
		$arr_permission = array('current_permission'=>array(),'inactive_permission'=>array());
		$arr_inactive_permission = array();

		// BaoNam: kiem tra system admin
		if( isset($_SESSION['arr_user']) && $_SESSION['arr_user']['contact_name'] == 'System Admin' ){
			$this->system_admin = true;
			$this->set('system_admin', true);
		}

		if ($this->request->url != 'users/login' && $this->Session->check('arr_user') && CHECK_PERMISSION && !$this->system_admin) {
			// dùng cho view hoặc function của controller
			$arr_permission['current_permission'] = $this->get_all_permission();
			$arr_permission['inactive_permission'] = $this->get_all_inactive_permission();
			if(!$this->request->is('ajax'))
				$this->set('arr_menu',$this->rebuild_header_menu($arr_permission));
			/*	Tạm thời ko dùng khái niệm owner
				//Nếu như không có quyền trên toàn controller, và ko có quyền xem tất cả record entry trên controller
				if(!isset($this->arr_permission['all']) &&
				   isset($this->arr_permission[strtolower($this->name).'_@_entry_@_view']) && $this->arr_permission[strtolower($this->name).'_@_entry_@_view']!='all'){
					$arr_where = array('created_by'=>new MongoId($_SESSION['arr_user']['contact_id']));
					$this->Session->write($this->name.'_where_permission',$arr_where);
				}
			*/
		}
		$this->arr_permission = $arr_permission['current_permission'];
		$this->arr_inactive_permission = $arr_permission['inactive_permission'];
		$this->set('arr_permission',$arr_permission);
		$this->get_language();
		if(isset($_SESSION['theme']))
			$this->theme = $_SESSION['theme'];
		$this->set('theme',$this->theme);
		if(!$this->Session->check('format_currency')){
			$this->selectModel('Stuffs');
			$format_currency = $this->Stuffs->select_one(array('value'=>'Format Currency'));
			$format_date = $this->Stuffs->select_one(array('value'=>'Format Date'));
			$this->Session->write('format_currency', (isset($format_currency['format_currency']) ? $format_currency['format_currency'] : 2));
			$this->Session->write('format_date', (isset($format_date['format_date']) ? $format_date['format_date'] : 'd M, Y'));
		}
		$this->selectModel('Setting');
		if(IS_LOCAL) {
			$this->arr_currency = $this->get_arr_currency();
		}

		//Minh, bypass user session for controller services.
		if($this->params->params['controller']!='services')
		{
			if(!in_array($this->params->params['controller'], array('homes','users','settings','calendars','salesorderdds', 'charts')) && !$this->check_permission($this->params->params['controller'].'_@_entry_@_view'))
				$this->error_auth();

		}
	}

	function clean_directory($dir){
		$di = new RecursiveDirectoryIterator($dir, FilesystemIterator::SKIP_DOTS);
		$ri = new RecursiveIteratorIterator($di, RecursiveIteratorIterator::CHILD_FIRST);
		foreach ( $ri as $file ) {
		    $file->isDir() ?  rmdir($file) : unlink($file);
		}
		return true;
	}

	function sync_data_to_server(){

		define('MONGODB_SERVER', 'jt');
		
		if(!IS_LOCAL){
			echo 'Please sync from local.';
			die;
		}

		$path = WWW_ROOT.'mongodump';

        if( !file_exists($path) ){
            // File::makeDirectory($path, 0777, true);
            mkdir($path, 0777, true);
        }

        $this->clean_directory($path);

        $path .= DS.'mongodump-'.(date('y.m.d'));

        if( !file_exists($path) ) {
            // File::makeDirectory($path, 0777, true);
            mkdir($path, 0777, true);
        }

        $this->selectModel('Stuffs');
        $lastSyncs = $this->Stuffs->select_all(array('arr_where' => array('name' => 'Last_sync_date'),
        											'arr_field' => array('_id', 'sync_time', 'name', 'date_modified'),
        											'limit' => 1
        							));

        $lastSync = array();
        // echo $lastSyncs->count();exit;
        if( !$lastSyncs->count() ) {
            $lastSync = [
                    'name' => 'Last_sync_date',
                    'sync_time' => 0,
                    'date_modified' => new MongoDate()
            ];
        } else {
        	foreach ($lastSyncs as $lasts) {
            	$lastSync = $lasts;
            }
        }

        $lastSync['sync_time'] = isset($lastSync['sync_time']) ? $lastSync['sync_time'] : 0;
        $lastSync['date_modified'] = isset($lastSync['date_modified']) ? $lastSync['date_modified'] : new MongoDate();


        // foreach(['tb_contact', 'tb_company', 'tb_country', 'tb_province', 'tb_tax', 'tb_product', 'tb_settings'] as $collection) {
        $arr_collections = array('Company' => 'tb_company');
        foreach($arr_collections as $key => $collection) {
            $this->selectModel($key);
            $lastRecords = $this->$key->select_all(
            	array('arr_field' => array('_id'),
						'arr_where' => array('date_modified' => [ '$lt' => $lastSync['date_modified'] ]),
						'arr_order'=>array('_id'=>-1),
						'limit'=>1
        							));
            foreach ($lastRecords as $rec) {
            	$lastRecord = $rec;
            }
            // pr($lastRecord);exit;
            $query = '{ \'_id\' : { \'$gt\' : ObjectId(\''.$lastRecord['_id'].'\') } }';
            if( DS == '/' ) {
                $query = '{ \'_id\' : { \$gt : ObjectId(\''.$lastRecord['_id'].'\') } }';
            }
            //dump db in current machine
            $command = 'mongodump -u sysadmin -p serCurity!2017 --port 27017 -d '.DB_CONNECT_MONGODB.' -q "'.$query.'" -c '.$collection.' -o '.$path;
            exec($command);
            echo 'command:'.$command.'<br/>';
            if( is_file($path.DS.DB_CONNECT_MONGODB.DS.$collection.'.bson') ){
            	//restore into db in current machine
            	$command = "mongorestore -h 167.114.209.179 -u sysadmin -p serCurity!2017 -d ".MONGODB_SERVER." -c $collection {$path}".DS.DB_CONNECT_MONGODB.DS."$collection.bson";
                exec($command);
                echo 'command:'.$command.'<br/>';
            }

        }
        
        $lastSync['sync_time']++;
        $lastSync['date_modified'] = new MongoDate();
        $this->Stuffs->save($lastSync);
        
        $arrData = [
                    'event'     => 'DBSync',
                    'message'   => 'Database has been synchronized to the lastest version.',
                    'status'    => 'success',
                    'updated_at'    => date('d M, y H:i', $lastSync['date_modified']->sec),
                    'updated_time'  => number_format($lastSync['sync_time'])
                ];
        pr($arrData);
        die;
	}

	function sync_data_from_server(){

		define('MONGODB_SERVER', 'jt');

		if(!IS_LOCAL){
			echo 'Please sync from local.';
			die;
		}

		$path = WWW_ROOT.'mongodump';

        if( !file_exists($path) ){
            // File::makeDirectory($path, 0777, true);
            mkdir($path, 0777, true);
        }

        $this->clean_directory($path);

        $path .= DS.'mongodump-'.(date('y.m.d'));

        if( !file_exists($path) ) {
            // File::makeDirectory($path, 0777, true);
            mkdir($path, 0777, true);
        }

        $this->selectModel('Stuffs');
        $lastSyncs = $this->Stuffs->select_all(array('arr_where' => array('name' => 'Last_sync_date'),
        											'arr_field' => array('_id', 'sync_time', 'name', 'date_modified'),
        											'limit' => 1
        							));

        $lastSync = array();
        if( !$lastSyncs ) {
            $lastSync = [
                    'name' => 'Last_sync_date',
                    'sync_time' => 0,
                    'date_modified' => new MongoDate()
            ];
        } else {
        	foreach ($lastSyncs as $lasts) {
            	$lastSync = $lasts;
            }
        }

        $lastSync['sync_time'] = isset($lastSync['sync_time']) ? $lastSync['sync_time'] : 0;
        $lastSync['date_modified'] = isset($lastSync['date_modified']) ? $lastSync['date_modified'] : new MongoDate();


        // foreach(['tb_contact', 'tb_company', 'tb_country', 'tb_province', 'tb_tax', 'tb_product', 'tb_settings'] as $collection) {
        $arr_collections = array('Company' => 'tb_company');
        foreach($arr_collections as $key => $collection) {
            $this->selectModel($key);
            $lastRecords = $this->$key->select_all(array('arr_field' => array('_id'),
            												'arr_order'=>array('_id'=>-1),
            												'limit'=>1
        							));
            foreach ($lastRecords as $rec) {
            	$lastRecord = $rec;
            }
            // pr($lastRecord);exit;
            $query = '{ \'_id\' : { \'$gte\' : ObjectId(\''.$lastRecord['_id'].'\') } }';
            if( DS == '/' ) {
                $query = '{ \'_id\' : { \$gte : ObjectId(\''.$lastRecord['_id'].'\') } }';
            }
            //dump jobtraq db on the SERVER
            $command = 'mongodump -h 167.114.209.179 -u sysadmin -p serCurity!2017 --port 27017 -d '.MONGODB_SERVER.' -q "'.$query.'" -c '.$collection.' -o '.$path;
            exec($command);
            echo 'command:'.$command.'<br/>';
            if( is_file($path.DS.MONGODB_SERVER.DS.$collection.'.bson') ){
            	//restore into db in current machine
                exec("mongorestore -u sysadmin -p serCurity!2017 -d ".DB_CONNECT_MONGODB." -c $collection {$path}".DS.MONGODB_SERVER.DS."$collection.bson");
            }

            $newRecords = $this->$key->select_all(
            		array('arr_where' => array('date_modified' => [ '$gte' => $lastSync['date_modified'] ],
            									'_id' => [ '$lte' => $lastRecord['_id'] ])
        							));
            if( $newRecords ){
                foreach($newRecords as $record) {
                    foreach($record as $key1 => $value) {
                        if( $key1 == 'updated_at' && is_string($value) ) {
                            $record[$key1] = new MongoDate(strtotime($value));
                        }
                    }
                    $this->$key->save($record);
                }
            }

        }
        
        $lastSync['sync_time']++;
        $lastSync['date_modified'] = new MongoDate();
        $this->Stuffs->save($lastSync);
        
        $arrData = [
                    'event'     => 'DBSync',
                    'message'   => 'Database has been synchronized to the lastest version.',
                    'status'    => 'success',
                    'updated_at'    => date('d M, y H:i', $lastSync['date_modified']->sec),
                    'updated_time'  => number_format($lastSync['sync_time'])
                ];
        pr($arrData);
        die;
	}

	/*function sync_local_data() {
		// $this->selectModel('Stuffs');
        // $sync_date = $this->Stuffs->select_one(array('value'=>"Latest Sync Date"),array('sync_date'));
		$sync_date = "2017-03-03 00:00:00";
		$this->selectModel('Salesinvoice');
		$arr_where = array('deleted'=>false);
		$arr_where['date_modified']['$gte'] = new MongoDate($this->Common->strtotime($sync_date));
		$arr_data = $this->Salesinvoice->select_all(array(
			'arr_where' => $arr_where
		));
		if($arr_data) {
			foreach ($arr_data as $key => $value) {
				$id_existed = $this->Salesinvoice->select_one(array('_id'=>$value['_id']),array('_id'));
				if($id_existed) {
					//update
					$this->Salesinvoice->save($value);
				} else {
					//create
					$this->Salesinvoice->save($value);
				}
			}
		}
	}*/

	function check_report_pdf(){
		if(isset($_GET['print_pdf'])&&trim($_GET['print_pdf'])!=''){
			$token = trim($_GET['print_pdf']);
			$this->selectModel('Stuffs');
			$report_pdf_token = $this->Stuffs->select_one(array('name'=>new MongoRegex('/report_pdf_token/i')));
			if(isset($report_pdf_token['value'])&&!empty($report_pdf_token['value'])){
				foreach($report_pdf_token['value'] as $key=>$value){
					if($token!=$value) continue;
					unset($report_pdf_token['value'][$key]);
					$arr_contact['contact_id'] = new MongoId('100000000000000000000000');
					$arr_contact['contact_name'] = $arr_contact['full_name'] = 'System Admin';
					$arr_contact['first_name'] = 'System';
					$arr_contact['last_name'] = 'Admin';
					$this->Session->write('arr_user', $arr_contact);
					$this->Stuffs->save($report_pdf_token);
					return true;
				}
			}
		}
		return false;
	}
	function rebuild_header_menu($arr_permission){
		$arr_menu = Cache::read('arr_menu_'.(string)$_SESSION['arr_user']['contact_id']);
		if(!$arr_menu){
			$arr_menu = array();
			//inactive: 0-hoạt động, 1-ẩn, 2-hiện nhưng không hoạt động
			$arr_crm = array(
							'stages' 		=> array(
											'name' => __('Stages'),
											'class' => 'stages',
											'inactive'	=> 1,
									),
							'timelogs'		=> array(
											'name' => __('TimeLog'),
											'class' => 'timelog',
											'inactive'	=> 1,
										),
							'tasks'			=> array(
											'name' => __('Task'),
											'class' => 'tasks',
											'inactive'	=> 1,
										),
							'jobs'			=> array(
											'name' => __('Job'),
											'class' => 'jobs',
											'inactive'	=> 1,
										),
							'docs'			=> array(
											'name' => __('Doc'),
											'class' => 'docs',
											'inactive'	=> 1,
										),
							'communications'=> array(
											'name' => __('Comm'),
											'class' => 'comms',
											'inactive'	=> 1,
										),
							'contacts'		=> array(
											'name' => __('Contact'),
											'class' => 'contacts',
											'inactive'	=> 1,
										),
							'companies'	=>	array(
											'name' => __('Company'),
											'class' => 'company',
											'inactive' =>  1,
										),
							);
			$arr_sales = array(
							'receipts' => array(
												'name' => __('Receipt'),
												'class' => 'receipts',
												'inactive'	=> 1,
											),
							'salesinvoices' => array(
												'name' => __('Sales Inv'),
												'class' => 'sales_inv',
												'inactive'	=> 1,
											),
							'salesaccounts' => array(
												'name' => __('Sales Acc'),
												'class' => 'sales_acc',
												'inactive'	=> 1,
											),
							'salesorders' => array(
												'name' => __('Sales Ord'),
												'class' => 'sales_ord',
												'inactive'	=> 1,
											),
							'quotations' => array(
												'name' => __('Quote'),
												'class' => 'quotes',
												'inactive'	=> 1,
											),
							'enquiries' => array(
												'name' => __('Enquiry'),
												'class' => 'enquiries',
												'inactive'	=> 1,
											),

							   );
			$arr_inventory = array(
							'shippings' => array(
											'name' => __('Shipping'),
											'class' => 'shipping',
											'inactive'	=> 1,
											),
							'purchaseorders' => array(
											'name' => __('Pur Order'),
											'class' => 'purch_ord',
											'inactive'	=> 1,
											),
							'batches' => array(
											'name' => __('Batches'),
											'class' => 'batches',
											'inactive'	=> 1,
											),
							'units' => array(
											'name' => __('Units'),
											'class' => 'units',
											'inactive'	=> 1,
											),
							'locations' => array(
											'name' => __('Location'),
											'class' => 'locations',
											'inactive'	=> 1,
											),
							'products' => array(
											'name' => __('Product'),
											'class' => 'products',
											'inactive'	=> 1,
											),
								   );
			$arr_hrm = array(
			                'name' => __('HRM'),
			                'employees' => array(
											'name' => __('Employee'),
											'class' => 'employees',
											'inactive'	=> 1,
											),
								   );
			$current_permission = $arr_permission['current_permission'];
			$inactive_permission = $arr_permission['inactive_permission'];
			//Neu co quyen all, mo het tru` timelogs, stages
			if(isset($current_permission['all'])){
				foreach($arr_crm as $key=>$value){
					if($key!='stages'
					   && (isset($current_permission[$key.'_@_entry_@_view']) && !in_array('', $current_permission[$key.'_@_entry_@_view']))) {
						$arr_crm[$key]['inactive'] = 0;
					}
				}
				$arr_crm['name'] = __('CRM');
				foreach($arr_sales as $key=>$value) {
					if(isset($current_permission[$key.'_@_entry_@_view']) && !in_array('', $current_permission[$key.'_@_entry_@_view'])) {
						$arr_sales[$key]['inactive'] = 0;
					}
				}
				$arr_sales['name'] = __('Sales');
				foreach($arr_inventory as $key=>$value) {
					if(isset($current_permission[$key.'_@_entry_@_view']) && !in_array('', $current_permission[$key.'_@_entry_@_view'])) {
						$arr_inventory[$key]['inactive'] = 0;
					}
				}
				$arr_inventory['name'] = __('Inventory');
				$arr_menu['crm'] = array_reverse($arr_crm);
				$arr_menu['sales'] = array_reverse($arr_sales);
				$arr_menu['inventory'] = array_reverse($arr_inventory);
				// $arr_menu['hrm'] = $arr_hrm;
				return $arr_menu;
			}
			//Tên gán ngược vì name sẽ đc gán sau cùng, nếu để name vào luôn thì trong foreach liên tục kiểm tra if ! liên tục (name với sub_menu cùng cấp)
			foreach($arr_crm as $key=>$value){
				if (isset($current_permission[$key.'_@_entry_@_view']) && !in_array('', $current_permission[$key.'_@_entry_@_view'])) {
					$arr_crm[$key]['inactive'] = 0;
					$crm = true;
				}
			}
			foreach($arr_sales as $key=>$value){
				if (isset($current_permission[$key.'_@_entry_@_view']) && !in_array('', $current_permission[$key.'_@_entry_@_view'])) {
					$arr_sales[$key]['inactive'] = 0;
					$sales = true;
				}
			}
			foreach($arr_inventory as $key=>$value){
				if (isset($current_permission[$key.'_@_entry_@_view']) && !in_array('', $current_permission[$key.'_@_entry_@_view'])) {
					$arr_inventory[$key]['inactive'] = 0;
					$inventory = true;
				}
			}
			//Mục đích gán $sales,$crm,$inventory vì nếu menu con của nó ko cái nào active hết thì nó (CRM , Sales, Inventory) sẽ ko có trong arr_menu
			//reverse lại vì mình đã sắp theo thứ tự ngược
			//Nếu ở trên sắp thứ tự đúng, và gán name sau cùng vẫn chạy, tuy nhiên chữ CRM lại nằm sau Company,Contact,...
			if(isset($crm)){
				$arr_crm['name'] = __('CRM');
				$arr_menu['crm'] = array_reverse($arr_crm);
			}
			if(isset($sales)){
				$arr_sales['name'] = __('Sales');
				$arr_menu['sales'] = array_reverse($arr_sales);
			}
			if(isset($inventory)){
				$arr_inventory['name'] = __('Inventory');
				$arr_menu['inventory'] = array_reverse($arr_inventory);
			}
			// $arr_menu['hrm'] = $arr_hrm;
			Cache::write('arr_menu_'.(string)$_SESSION['arr_user']['contact_id'],$arr_menu);
		}
		return $arr_menu;
	}
	function beforeRender() {
		if ($this->request->is('ajax')) {
			$this->set('ajax', true);
			$this->layout = 'ajax';
		}
		if (is_object($this->db)) {
			// tạm thời không ngắt kết nối để tối ưu các request đỡ phải mất thời gian kết nối nhiều lần, vd: các ajax trên page chẳng hạn
			// if (!$this->mongo_disconnect()) {
			//     die('Can not disconnect mongodb!');
			// }
		}
	}

	protected $db = null;
	private $connectionDB = null;

	function mongo_connect() {
		if (!is_object($this->connectionDB)) {
			// http://docs.mongodb.org/manual/reference/connection-string/#connections-standard-connection-string-format
			// set Timeout và không cần close nữa
			$this->connectionDB = new MongoClient('mongodb://sysadmin:serCurity!2017@localhost:27017?connectTimeoutMS=300000');
			// $this->connectionDB = new Mongo('mongodb://sadmin:2016Anvy!@'.IP_CONNECT_MONGODB.':27017?connectTimeoutMS=300000');
			$this->db = $this->connectionDB->selectDB(DB_CONNECT_MONGODB);
		}
	}

	function mongo_disconnect() {
		// $this->db->command(array("logout" => 1));
		return $this->connectionDB->close();
	}

	function selectModel($model) {
		$this->mongo_connect();
		if (is_object($this->db) && !is_object($this->$model)) {
			if (file_exists(APP . 'Model' . DS . $model . '.php')) {
				require_once APP . 'Model' . DS . $model . '.php';
				$this->$model = new $model($this->db);
			} else {
				//echo 'File ' . APP.'Model'.DS.$model.'.php' . ' does not exist';die;
			}
		}
	}

	function get_language(){
		if(!isset($_SESSION['default_lang']) || $_SESSION['default_lang'] == DEFAULT_LANG)
			return array();
		$key = $_SESSION['default_lang'];
		$arr_tmp = array();
		// if(!isset($_SESSION['arr_language_'.$key])){
			$this->selectModel('Languagedetail');
			$arr_temp = array();
			$arr_language = $this->Languagedetail->select_all(array(
				'arr_where' => array(
					'content.'.$key => array(
						'$exists'=>true,
						'$ne' => ''
					),
					'key' => array(
					   '$ne'=>''
					),
				),
				'arr_order' => array('key' => 1),
			));
			foreach($arr_language as $keys => $value){
				// $arr_tmp[$value['key']] = isset($value['content'][$key])?$value['content'][$key]:'';
				$arr_tmp[$value['content']['en']] = isset($value['content'][$key])?$value['content'][$key]:'';
			}
			$_SESSION['arr_language_'.$key] = $arr_tmp;
		// }
	}

	function define_system_message() {
		$arr_messages = Cache::read('arr_messages');
		if(!$arr_messages){
			$arr_messages = array();
			$this->selectModel('SettingMessage');
			$arr_messages = $this->SettingMessage->select_list(array(
				'arr_field' => array('key', 'content'),
			));
			Cache::write('arr_messages',$arr_messages);
		}
		$this->Session->write('arr_messages', $arr_messages);
		return $arr_messages;
	}

	//nhận tên dựa vào id
	function get_name($module = '', $module_id = '', $fieldname = 'name') {
		//for ajax
		if (isset($_POST['module']))
			$module = $_POST['module'];
		if (isset($_POST['module_id']))
			$module_id = $_POST['module_id'];
		if (isset($_POST['fieldname']))
			$fieldname = $_POST['fieldname'];

		$names = '';
		if ($module_id == '') {
			$module_id = $this->get_id();
		}

		if($module == '')
			$module = $this->modelName;

		//process
		$names = '';

		if (!is_object($module_id))
			$module_id = new MongoId($module_id);
		$this->selectModel($module);
		$datalist = array($fieldname);
		if ($module == 'Contact'){
			$datalist = array('first_name', 'last_name');
			$query = $this->$module->collection->findOne(array('deleted' => false, '_id' => $module_id), $datalist);
			$names = $query['first_name'] . ' ' . $query['last_name'];
		}
		else
		{
			if( $module == 'Shipper' ){
				$module = 'Company';
				$this->selectModel('Company');
			}
			$query = $this->$module->collection->findOne(array('deleted' => false, '_id' => $module_id), $datalist);
			if (isset($query[$fieldname]))
				$names = $query[$fieldname];
			else
				$names='';
		}

		//output
		if (isset($_POST['module'])) {
			echo $names;
			die;
		}
		else
			return $names;
	}

	//chuyển text controller thành text module
	function ModuleName($controller = '') {
		if ($controller == '')
			$controller = $this->params->params['controller'];
		$modulename = preg_replace("/s$/i", "", $controller);
		$modulename = preg_replace("/ie$/i", "y", $modulename);
		return ucfirst($modulename);
	}

	/* ======  BASIC MODULE FUNCTION ======

	  The Basic module include these functions:

	  set_module_before_filter()
	  index()					: action index
	  add()					: action add
	  ajax_save()				: ajax save
	  delete(id)				: action delete
	  get_id()				: find this id
	  get_pages_order()		: get page for this id by oder
	  get_pages()				: get page for this id
	  first() 				: action first
	  prevs()					: action prevs
	  nexts()					: action nexts
	  lasts()					: action lasts
	  entry_search()			: Entry search
	  popup()					: popup listview
	  printpdf()				: print pdf frame
	  sub_tab($sub_tab)		: Subtab
	  general_select(array)	: general options of selects in arr_setting
	  show_footer_info(array)	: show footer info in entry
	  save_option(array)		: add/update option(value array) of modules
	  get_default_rel($opname,$flat): nhận giá trị default của field trong arr setting box
	  get_fields_rel($field_name): field chính có rel_name link đến 1 box trong subtab,dựa vào nhận danh sách field
	  deleteopt()				: delete option (fields have type is array)
	  search_list()			:
	  update_default()		:
	  province()				:
	  general_province()		:
	  country()				:
	  general_country()		:
	  ajax_general_province()	:
	  get_data_other_model($from,$setup_ass)
	  get_data_form_module()	:
	  save_muti_field()		:
	  save_data_form_to()		:
	  reload_address()		:
	  reload_cc($company_id='')
	  get_email_of_company($company_id)
	  cal_sum_by_request($opname,$amount_field)

	 */

	public $name;
	public $opm; //Option Module
	public $sub_tab_default;
	public $lists_mod;
	public $is_popup = false;

	//set_module_before_filter()
	public function set_module_before_filter($module) {

		$this->selectModel($module);
		$this->opm = $this->$module;
		$this->set('name', $this->name);
		//kiểm tra và khóa dựa vào seeting
		if ($this->get_id()!='' || $this->get_last_id()!='')
			$this->rebuild_setting();

		//kiem tra session sub tab với setting subtab de tranh bao loi k oton tai trong arr setting
		$this->check_subtab();
		// Get setting from Module
		$arr_set = $this->opm->arr_settings;
		if ($this->params->params['action'] != 'entry')
			$this->set('arr_settings', $arr_set);
		// Render Option Field into arr_options. Struction: arr_options['fieldname']= 'htmlstring';
		$datas = $this->general_select($arr_set['field']);
		if (isset($datas['category']))
			asort($datas['category']);
		$this->set('arr_options', $datas);
		//Save session of sub tab
		if (isset($this->params->params['pass'][0]) && strlen($this->params->params['pass'][0]) == 24)
			$this->set('iditem', $this->params->params['pass'][0]);
		else if ($this->Session->read($this->name . 'ViewId') != '')
			$this->set('iditem', $this->Session->read($this->name . 'ViewId'));
		else {
			//find last id
			$where_query = $this->arr_search_where();
			$arr_tmp = $this->opm->select_one($where_query, array('_id'), array('_id' => -1));
			if (isset($arr_tmp['_id'])) {
				$direct = (array) $arr_tmp['_id'];
				$this->set('iditem', $direct['$id']);
			}
			else
				$this->set('iditem', '');
		}
		if(!$this->request->is('ajax')){
			$entry_menu['page'] = $this->get_pages();
			$where_query = $this->arr_search_where();
			$entry_menu['search'] = $this->opm->count($where_query);
			$entry_menu['total'] = $this->opm->count($where_query);
			$this->set('entry_menu', $entry_menu);
		}
	}



	//kiểm tra và khóa dựa vào setting. custom trong từng module
	public function rebuild_setting($arr_setting=array()){
		$model_name = $this->modelName;
		$this->selectModel($model_name);
		if(in_array($this->params->params['action'], array('entry','line_entry','text_entry'))){
			$arr_setting = $this->$model_name->arr_settings;
			if(!$this->check_permission($this->name.'_@_entry_@_edit')){
				$arr_setting = $this->$model_name->set_lock(array(),'out');
				$this->set('address_lock', '1');
			}
			if(!$this->check_permission('products_@_entry_@_view')){
				//Cac module khac
				unset($arr_setting['relationship']['line_entry']['block']['products']['link'],$arr_setting['relationship']['text_entry']['block']['products']['link']);
				//Dung cho QT
				unset($arr_setting['relationship']['line_entry']['block']['products']['field']['sku']['link_field']);
			}
			$this->$model_name->arr_settings = $arr_setting;
			if($model_name!='Tax')
				$this->rebuild_line_text_entry();
			$arr_link = Cache::read('arr_link_'.$model_name);
			if(!$arr_link){
				$arr_tmp = $this->$model_name->arr_field_key('cls');
				$arr_link = array();
				if(!empty($arr_tmp))
					foreach($arr_tmp as $key=>$value)
						$arr_link[$value][] = $key;
				Cache::write('arr_link_'.$model_name,$arr_link);
			}
			$this->set('arr_link',$arr_link);
		}
	}


	//check subtab off. Dựa vào arr_setting
	public function check_subtab(){
		if ($this->Session->check($this->name . '_sub_tab')) {
			$sub_tab = $this->Session->read($this->name . '_sub_tab');
			$arr_setting = $this->opm->arr_settings;
			if(isset($arr_setting['relationship']))
				if(!isset($arr_setting['relationship'][$sub_tab]) && count($arr_setting['relationship'])>0){
					foreach($arr_setting['relationship'] as $kk=>$vv){
						$this->Session->write($this->name . '_sub_tab',$kk);
						break;
					}
			}
		}
	}


	//Index action
	public function index() {
		if ($this->Session->check($this->name . 'ViewThemes') && $this->Session->read($this->name . 'ViewThemes') != '')
			$views = $this->Session->read($this->name . 'ViewThemes');
		else
			$views = 'entry';
		$this->redirect('/' . $this->params->params['controller'] . '/' . $views);
		die;
	}


	//Entry action
	public function entry() {
		$this->Session->write($this->name . 'ViewThemes', 'entry');
		$this->SetModelClass();
		if (!$this->element_exit('entry'))
			$this->render('../Elements/entry');
	}



	/**
	* List view action
	* @author vu.nguyen
	* @since v1.0
	*/
	public function lists() {
		$this->selectModel('Setting');
		// --- BaoNam sort phan trang ---
		$limit = LIST_LIMIT; $skip = 0;
		// dùng cho sort
		$sort_field = '_id';
		$sort_type = -1;
		if( isset($_POST['sort']) && strlen($_POST['sort']['field']) > 0 ){
			if( $_POST['sort']['type'] == 'desc' ){
				$sort_type = -1;
			}else{
				$sort_type = 1;
			}
			$sort_field = $_POST['sort']['field'];
			$this->Session->write($this->name.'_lists_search_sort', array($sort_field, $sort_type));

		}elseif( $this->Session->check($this->name.'_lists_search_sort') ){
			$session_sort = $this->Session->read($this->name.'_lists_search_sort');
			$sort_field = $session_sort[0];
			$sort_type = $session_sort[1];
		}
		$arr_order = array($sort_field => $sort_type);
		$this->set('sort_field', $sort_field);
		$this->set('sort_type', ($sort_type === 1)?'asc':'desc');
		// BaoNam sort phan trang

		// dùng cho phân trang
		$page_num = 1;
		if( isset($_POST['pagination']) && $_POST['pagination']['page-num'] > 0){
			$page_num = $_POST['pagination']['page-num'];
			$limit = $_POST['pagination']['page-list'];
			$skip = $limit*($page_num - 1);
		}

		$this->set('page_num', $page_num);
		$this->set('limit', $limit);
		// --- end --- BaoNam phan trang

		$where_query = $this->arr_search_where();
		//An, 25.4.2014: merge array search
		$extra_where_query = array();
		if($this->Session->check(strtolower($this->name).'_entry_search_cond'))
			$extra_where_query = $this->Session->read(strtolower($this->name).'_entry_search_cond');
		$where_query = array_merge($where_query,$extra_where_query);
//unset($where_query['company_id'], $where_query['product_type'], $where_query['status']);
		//neu khong phai la POPUP
		if(!$this->is_popup){
			$arr_query = $this->opm->select_all(array(
				'arr_where' => $where_query,
				'arr_order' => $arr_order,
				'limit' => $limit,
				'skip' => $skip
			));
		//neu la POPUP
		}else{
			// BaoNam, dùng cho popup
			$arr_order = array('code' => 1);
			if( isset($_POST['sort']) && strlen($_POST['sort']['field']) > 0 ){
				$sort_type = 1;
				if( $_POST['sort']['type'] == 'desc' ){
					$sort_type = -1;
				}
				$arr_order = array('code'=>1,$_POST['sort']['field'] => $sort_type);

				$this->set('sort_field', $_POST['sort']['field']);
				$this->set('sort_type', ($sort_type === 1)?'asc':'desc');
				$this->set('sort_type_change', ($sort_type === 1)?'desc':'asc');
			}

			// BaoNam ============== PRODUCT ================================
			$limit = 100; $skip = 0; $arr_product_name = array();
			if( $this->name == 'Products'){
				$limit = 100; $skip = 0;
				$page_num = 1;
				if( isset($_POST['pagination']) && $_POST['pagination']['page-num'] > 0 ){
					$page_num = $_POST['pagination']['page-num'];
					$limit = $_POST['pagination']['page-list'];
					$skip = $limit*($page_num - 1);
				}
				$this->set('page_num', $page_num);
				$this->set('limit', $limit);
				// Kiem tra search popup
				if( isset($where_query['name']) && isset($_POST['products_name']) && strlen($_POST['products_name']) > 0 ){
					$cond_tmp = $where_query['name'];
					unset($where_query['name']);
					if(is_numeric($_POST['products_name'])){
						$where_query['code'] = (int)$_POST['products_name'];
					} else if(is_string($_POST['products_name'])){
						$where_query['$or'] = array(
							array('sku' => new MongoRegex('/'.$cond_tmp.'/i') ),
							array('name' => new MongoRegex('/'.$cond_tmp.'/i') )
						);
						$arr_product_name = explode(" ",$_POST['products_name']);
					}
				}
				// end
				if (!empty($_GET)) {
					if (isset($_GET['company_id'])) { // $_GET['company_name']
						$arr_where['company_name']['values'] = $_GET['company_name'];
						$this->set('arr_where', $arr_where);
						$this->set('popup_company_id', $_GET['company_id']);
					}
				}
			}
			// pr($where_query);die;
			//============== END PRODUCT ================================

			// a.Vu code
			$no_cache = true;
			unset($_GET['_']);
		    $cache_key = md5(serialize($_GET));
			if(empty($_POST)){
		        if( empty($_POST) && !isset($_GET['ispo']) ){
		            $arr_query = Cache::read('popup_'.strtolower($this->name).'_'.$cache_key);
		            if($arr_query)
		                $no_cache = false;
		        }
			}
			if($no_cache){
				$arr_query = $this->opm->select_all(array(
					'arr_where' => $where_query,
					'arr_order' => $arr_order,// BaoNam
					'arr_field' => array('code','sku','name','product_type','category','company_id','company_name','cost_price','group_type','is_custom_size','sell_by','sell_price','sizeh','sizeh_unit','sizew','sizew_unit','special_order','unit_price','oum','oum_depend','gst_tax','pst_tax'),
					'limit' => $limit, // BaoNam
					'skip' => $skip // BaoNam
				));
				//neu tim cum tu khong co ket qua thi tim cac tu roi xa nhau
				if(count($arr_product_name)>0 && $arr_query->count()==0){
					unset($where_query['$or']);
					$where_query['name'] = new MongoRegex("/".str_replace(" ", ".*",$_POST['products_name'])."/i");
					$arr_query = $this->opm->select_all(array(
						'arr_where' => $where_query,
						'arr_order' => $arr_order,
						'arr_field' => array('code','sku','name','product_type','category','company_id','company_name','cost_price','group_type','is_custom_size','sell_by','sell_price','sizeh','sizeh_unit','sizew','sizew_unit','special_order','unit_price','oum','oum_depend','gst_tax','pst_tax'),
						'limit' => $limit,
						'skip' => $skip
					));
				}
				if( empty($_POST)  && !isset($_GET['ispo']) )
                	Cache::write('popup_'.strtolower($this->name).'_'.$cache_key,iterator_to_array($arr_query));
			}
			if($this->name == 'Products' && (isset($where_query['$or']) || isset($where_query['code']) )
			   	&& (!isset($where_query['product_type']) || isset($_POST['products_product_type']) && $_POST['products_product_type']!='Product')
			 ){
				$select_data['category'] = $this->Setting->select_option_vl(array('setting_value' => 'product_category'));
		        asort( $select_data['category']);
				$tmp_query = $where_query;
				unset($tmp_query['status']);
				unset($tmp_query['product_type']);
				unset($tmp_query['product_category']);
				$product_type = $this->Product->collection->distinct('product_type',$tmp_query);
				if(empty($product_type))
		        	$select_data['product_type'] = $this->Setting->select_option_vl(array('setting_value' => 'product_type'));
		        else{
		        	foreach($product_type as $type){
		        		$select_data['product_type'][$type] = $type;
		        	}
		        }
		        $this->set('select_data', $select_data);
			}
		}

		// --- BaoNam ---
		$total_page = $total_record = $total_current = 0;
		if( is_object($arr_query) ){
			$total_current = $arr_query->count(true);
			$total_record = $arr_query->count();
			if( $total_record%$limit != 0 ){
				$total_page = floor($total_record/$limit) + 1;
			}else{
				$total_page = $total_record/$limit;
			}
		} else if(is_array($arr_query)){
            $total_current = count($arr_query);
            $total_record = $this->opm->count($where_query);
            if( $total_record%$limit != 0 ){
                $total_page = floor($total_record/$limit) + 1;
            }else{
                $total_page = $total_record/$limit;
            }
        }

		$this->set('total_current', $total_current);
		$this->set('total_page', $total_page);
		$this->set('total_record', $total_record);
		// --- BaoNam dùng cho popup và lists ---
		$this->set('_controller',$this);
		$this->set('arr_list', $arr_query);
		$list_field = $this->opm->list_view_field();
		$arr_set = $this->opm->arr_field_multi_key(array('name', 'type', 'cls', 'id', 'droplist'));
		$this->set('list_field', $list_field);
		$this->set('arr_set', $arr_set);

		foreach ($arr_set['type'] as $keys => $values) {
			if ($values == 'select')
				$opt_select[$keys] = $this->Setting->select_option_vl(array('setting_value' => $arr_set['droplist'][$keys]));
		}
		$this->set('opt_select', $opt_select);
		$this->SetModelClass();

		$this->Session->write($this->name . 'ViewThemes', 'lists');

		//Truong hop sort hoac load ajax
		if ($this->request->is('ajax') && !$this->is_popup) {
			$this->render('../Elements/lists_ajax');

		// Trường hợp không custom view
		}else if (!$this->is_popup && (!$this->element_exit('lists') || ($this->element_exit('lists') && $this->lists_mod != ''))){
			$this->render('../Elements/lists');

		 // Trường hợp custom view trong module
		}
	}


	// Set module class dùng cho view
	public function SetModelClass() {
		$arr_set = $this->opm->arr_field_multi_key(array('type', 'cls', 'syncname','list_syncname'));
		$syncname = $list_syncname = array();
		if(isset($arr_set['cls']))
		foreach ($arr_set['cls'] as $kk => $vv) {
			if (isset($vv)) {
				$arr_list = array();
				$modulename = $this->ModuleName($vv);
				if (!in_array($modulename, $arr_list)) {
					$arr_list = array_push($arr_list, $modulename);
					$moduleclass = $vv . '_class';
					$this->selectModel($modulename);
					$this->set($moduleclass, $this->$modulename);
					if (isset($arr_set['syncname'][$kk]))
						$syncname[$kk] = $arr_set['syncname'][$kk];
					if (isset($arr_set['list_syncname'][$kk]))
						$list_syncname[$kk] = $arr_set['list_syncname'][$kk];
					else
						$syncname[$kk] = 'name';
				}
			}
		}
		$this->set('syncname', $syncname);
		$this->set('list_syncname', $list_syncname);
	}

	// Add action
	public function add() {
		if(!$this->check_permission($this->name.'_@_entry_@_add'))
			$this->error_auth();
		$ids = $this->opm->add('name', '');
		$newid = explode("||", $ids);
		$this->Session->write($this->name . 'ViewId', $newid[0]);
		$this->redirect('/' . $this->params->params['controller'] . '/entry/'.$newid[0]);
		die;
	}

	//Add or update field when this field change, use in js.ctp
	public function ajax_save() {
		if (isset($_POST['field']) && isset($_POST['value']) && isset($_POST['func']) && !in_array((string) $_POST['field'], $this->opm->arr_autocomplete())) {
			if($_POST['func']=='update'&&!$this->check_permission($this->name.'_@_entry_@_edit')){
				echo 'You do not have permission on this action.';
				die;
			}
			if($_POST['func']=='add'&&!$this->check_permission($this->name.'_@_entry_@_add')){
				echo 'You do not have permission on this action.';
				die;
			}
			$values = $_POST['value'];
			if (preg_match("/_date$/", $_POST['field']))
				$values = $this->Common->strtotime($values . '00:00:00');
//echo $_POST['func'];die;
			if ($_POST['func'] == 'add') {
				$ids = $this->opm->add($_POST['field'], $values);
				$newid = explode("||", $ids);
				$this->Session->write($this->name . 'ViewId', $newid[0]);
			} else if ($_POST['func'] == 'update' && isset($_POST['ids'])) {
				if($_POST['field']=='email') {
					if (strlen($values) > 0 && !filter_var($values, FILTER_VALIDATE_EMAIL)) {
						echo 'email_not_valid';
						die;
					}
				} else if(preg_match("/status$/i", $_POST['field'])
						  && ($this->name == 'Salesorders' || $this->name == 'Quotations' ) ){
					$this->auto_action($_POST['value']);
				} else if(strpos($_POST['field'], '_cost')!==false || strpos($_POST['field'], 'sum_')!==false)
					 $values = (float)$values;
				$ids = $this->opm->update($_POST['ids'], $_POST['field'], $values);
				$this->Session->write($this->name . 'ViewId', $_POST['ids']);
			}
			echo $ids;
		}
		else
			echo 'error';
		die;
	}


	/**
	* Ajax save data
	* This is new ajax save function, return array data, more sercury
	* The first, you need setup $this->arr_associated_data()
	* @use: call ajax from js file
	* @author vu.nguyen
	* @since v1.0
	*/
	public function save_data($field = '', $value = '', $ids = '', $valueid = '') {
		if (isset($_POST['field']))
			$field = $_POST['field'];
		if (isset($_POST['value']))
			$value = $_POST['value'];
		if (isset($_POST['ids']))
			$ids = $_POST['ids'];
		if (isset($_POST['valueid']))
			$valueid = $_POST['valueid'];
		$arr_save = $arr_temp = array();

		if ($field != '' && !in_array((string) $field, $this->opm->arr_nonsave())) {

			$arr_save = $this->set_mongo_data($field, $value); //check & format data
			if ($ids != '')
				$arr_save['_id'] = new MongoId($ids);
			$arr_temp = $this->arr_associated_data($field, $value, $valueid); //set more associated fields : các dữ liệu liên quan
			$arr_save = array_merge((array) $arr_save, (array) $arr_temp);
			if ($this->opm->save($arr_save)) {
				echo json_encode($this->arr_list_changed($arr_save));
			}
			else
				echo 'Can not save.';
		}
		die;
	}

	//kiem tra loai data dua vao field và value
	public function set_mongo_data($field = '', $value = '') {
		$arr_temp = array();
		if (preg_match("/_date$/", $field) && (int)$value>0)
			$arr_temp[$field] = new MongoDate($value);
		else if(preg_match("/_date$/", $field)){
			$values = $this->Common->strtotime($values . '00:00:00');
			$arr_temp[$field] = new MongoDate($value);
		}else if (preg_match("/_id$/", $field))
			$arr_temp[$field] = new MongoId($value);
		else
			$arr_temp[$field] = $value;
		return $arr_temp;
	}


	//kiem tra loai data dua vao field và value
	public function set_mongo_array($arr = array()) {
	   $arr_temp = array();
	   if(count($arr)>0){
		   foreach($arr as $field=>$value){
			   if (preg_match("/_date$/", $field))
					$arr_temp[$field] = new MongoDate($value);
			   else if (preg_match("/_id$/", $field))
					$arr_temp[$field] = new MongoId($value);
			   else
					$arr_temp[$field] = $value;

		   }
	   }
	   return $arr_temp;
	}


	//custom in ModuleModel
	public function arr_associated_data($field = '', $value = '', $valueid = '') {
		$arr_return = array();
		$arr_return[$field] = $value;
		return $arr_return;
	}

	//custom in ModuleController
	public function check_lock() {
		return true;
	}

	//liet ke cac field da check
	public function arr_list_changed($arr_save = array()) {
		$arr_ret = array();
		foreach ($arr_save as $k1 => $arr1) {
			if (preg_match("/_date$/", $k1) && is_object($arr1)){
				$arr_ret[$k1] = date("d M, Y",$arr1->sec);

			}else if (preg_match("/_id$/", $k1) && is_object($arr1)){
				$arr1 = (array)$arr1;
				if(isset($arr1['$id']))
				$arr_ret[$k1] = $arr1['$id'];

			}else if (is_array($arr1) || is_object($arr1)) {
				foreach ($arr1 as $k2 => $arr2) {
					if (is_array($arr2) || is_object($arr2)) {
						foreach ($arr2 as $k3 => $arr3) {
							if (is_array($arr3) || is_object($arr3)) {
								foreach ($arr3 as $k4 => $arr4) {
									$arr_ret[$k1 . '.' . $k3 . '.' . $k4] = $arr4;
								}
							}
							else
								$arr_ret[$k1 . '.' . $k3] = $arr3;
						}
					}
					else
						$arr_ret[$k2] = $arr2;
				}
			}
			else
				$arr_ret[$k1] = $arr1;
		}
		return $arr_ret;
	}

	// Delete 1 record
	public function delete($ids = 0) {
		if(!$this->check_permission($this->name.'_@_entry_@_delete'))
			$this->error_auth();
		if(!$ids)
			$ids = $this->get_id();
		if ($ids != '') {
			$this->delete_all_associate($ids);
			$str_return = $this->opm->update($ids, 'deleted', true);
			$actions = $this->Session->read($this->name . 'ViewThemes');
			$this->Session->delete($this->name . 'ViewId');

			if($this->request->is('ajax')){
				echo 'ok';
				die;
			}
			$this->redirect('/' . $this->params->params['controller'] . '/lists');
			//$this->redirect('/' . $this->params->params['controller'] . '/' . $actions);
		} else {
			$this->Session->delete($this->name . 'ViewId');
			$this->redirect('/' . $this->params->params['controller'] . '/lists');
		}
		die;
	}

	public function delete_all_associate($ids='',$opname=''){
		//
	}

	// Check url or session to get this id
	public function get_id() {
		if (isset($this->params->params['pass'][0]) && strlen($this->params->params['pass'][0]) == 24 && strpos($this->params->params['pass'][0], '_')===false)
			$iditem = $this->params->params['pass'][0];
		else if ($this->Session->check($this->name . 'ViewId'))
			$iditem = $this->Session->read($this->name . 'ViewId');
		else {
			//find last id
			$where_query = array();
			$this->identity($where_query);
			if($this->name == 'Salesorders' && !$this->check_permission('salesorders_@_view_worktraq_@_view')){
	        	$where_query['code'] = array('$not' => new MongoRegex('/WT-/'));
	        }
			if ( isset($this->opm) ) {
				$arr_tmp = $this->opm->select_one($where_query, array('_id'), array('_id' => -1));
			} else {
				$module = $this->modelName;
				$this->selectModel($module);
				$arr_tmp = $this->$module->select_one($where_query, array('_id'), array('_id' => -1));
			}
			if (isset($arr_tmp['_id']) && is_object($arr_tmp['_id'])) {
				$iditem = (array) $arr_tmp['_id'];
				$iditem = $iditem['$id'];
			} else if (isset($arr_tmp['_id']) && strlen($arr_tmp['_id']) == 24) {
				$iditem = $arr_tmp['_id'];
			}
			else
				$iditem = '';
		}
		return $iditem;
	}

	public function identity_company()
	{
		$arr_contact = $this->Session->read('arr_user');
		return $arr_contact['company_id'];
	}

	public function identity(&$arr_where)
	{
		/*if ( IS_LOCAL ) {
			$arr_contact = $this->Session->read('arr_user');
			if( !isset($arr_contact['system_admin']) && isset($arr_contact['company_id'])  ) {
				$company_id = $arr_contact['company_id'];
				$arr_where['identity']['$in'][] = $company_id;
				$arr_where['identity']['$in'][] = 0;
			}
		}*/
		return $arr_where;
	}

	//session search where chage to array
	public function arr_search_where() {
		$where_query = $where = array();
		if ($this->Session->check($this->name . '_where'))
			$where = $this->Session->read($this->name . '_where');

		//ghép điều kiện truyền vào từ hàm popup
		if ($this->is_popup && $this->Session->check($this->name . '_where_popup'))
			$where = $this->Session->read($this->name . '_where_popup');
		//set operater
		if (count($where) > 0) {
			foreach ($where as $keys => $arr) {
				if (isset($arr['operator']) && $arr['operator'] == 'LIKE')
					$where_query[$keys] = new MongoRegex("/" . $arr['values'] . "/i");
				else if (isset($arr['operator']) && $arr['operator'] == 'in')
					$where_query[$keys]['$in'] = $arr['values'];
				else if (isset($arr['operator']) && $arr['operator'] == 'nin')
					$where_query[$keys]['$nin'] = $arr['values'];
				else if (isset($arr['operator']) && $arr['operator'] == 'or')
					$where_query['$or']= $arr['values'];

				else if (isset($arr['operator']) && $arr['operator'] == '>')
					$where_query[$keys]['$gt'] = $arr['values'];

				else if (isset($arr['operator']) && $arr['operator'] == '<')
					$where_query[$keys]['$lt'] = $arr['values'];

				else if (isset($arr['operator']) && $arr['operator'] == '>=')
					$where_query[$keys]['$gte'] = $arr['values'];

				else if (isset($arr['operator']) && $arr['operator'] == '<=')
					$where_query[$keys]['$lte'] = $arr['values'];
				else if(isset($arr['operator']) && $arr['operator'] == '!=')
					$where_query[$keys] = array('$ne' => $arr['values']);
				else if (isset($arr['operator']) && $arr['operator'] == '=int')
					$where_query[$keys] = (int) $arr['values'];
				else if (isset($arr['operator']) && $arr['operator'] == 'exists@!=')
					$where_query[$keys] = array('$exists'=>true,'$ne'=>array($arr['values']));
				else if ($keys == 'code' && !in_array($this->name, array('Salesorders','Salesinvoices')))
					$where_query[$keys] = (int) $arr['values'];
				else if(isset($arr['>='])||isset($arr['<=']))
				{
					if (isset($arr['>=']['operator']) && strpos($arr['>=']['operator'], '>=')!== false)
						$where_query[$keys]['$gte'] = $arr['>=']['values'];
					if (isset($arr['<=']['operator']) && strpos($arr['<=']['operator'], '<=')!== false)
						$where_query[$keys]['$lte'] = $arr['<=']['values'];

				}
				else if(isset($arr['operator']) && $arr['operator'] == 'elemMatch')
					$where_query[$keys]['$elemMatch'] =  $arr['values'];
				else if(isset($arr['operator']) && $arr['operator'] == 'other'){
					$where_query = array_merge($where_query,$arr['values']);
				}
				else
					$where_query[$keys] = isset($arr['values'])?$arr['values']:'';
			}
		}
		$this->identity($where_query);
		if(isset($_SESSION[$this->name.'_where_permission'])){
			if(is_array($where_query))
				return array_merge($where_query,$_SESSION[$this->name.'_where_permission']);
			return $_SESSION[$this->name.'_where_permission'];
		}
		return $where_query;
	}

	public function get_pages() {
		$ids = $this->get_id();
		$where_query = $this->arr_search_where();
		//set operater
		if (isset($where_query) && count($where_query) > 0) {
			$query = $this->opm->select_all(array(
				'arr_field' => array('_id'),
				'arr_where' => $where_query,
				'arr_order' => array('_id' => 1)//,
			));
			$n = 0;
			foreach ($query as $keys => $values) {
				$n++;
				if ($this->get_id() == $keys)
					return $n;
			}
			if ($n == 0)
				return '0';
			else
				return '1';
		}

		$sum = 0;
		if ($ids != '') {
			$arr_where = array('_id' => array('$lte' => new MongoId($ids)));
			$sum = $this->opm->count($arr_where);
		}
		if ($ids == '')
			return $this->opm->count();
		else if ($sum != -1)
			return $sum;
		else
			return 0;
	}

	// link to first entry
	public function first() {
		$where_query = $this->arr_search_where();
		if($this->name == 'Salesorders' && !$this->check_permission('salesorders_@_view_worktraq_@_view')){
        	$where_query['code'] = array('$not' => new MongoRegex('/WT-/'));
        }
		$arr_tmp = $this->opm->select_one($where_query, array('_id'), array('_id' => 1));
		if (isset($arr_tmp['_id']))
			$direct = $arr_tmp['_id'];
		else
			$direct = '';
		$this->redirect('/' . $this->params->params['controller'] . '/entry/' . $direct);
		die;
	}

	// link to preview entry
	public function prevs() {
		if ($this->get_id() != '') {
			$where_query = $this->arr_search_where();
			$where_query['_id'] = array('$lt' => new MongoId($this->get_id()));
			if($this->name == 'Salesorders' && !$this->check_permission('salesorders_@_view_worktraq_@_view')){
        		$where_query['code'] = array('$not' => new MongoRegex('/WT-/'));
	        }
			$arr_prev = $this->opm->select_one($where_query, array('_id'), array('_id' => -1));
		}
		if (isset($arr_prev['_id']))
			$prevs = $arr_prev['_id'];
		else
			$prevs = '';
		$this->redirect('/' . $this->params->params['controller'] . '/entry/' . $prevs);
		die;
	}

	// link to next entry
	public function nexts() {
		if ($this->get_id() != '') {
			$where_query = $this->arr_search_where();
			$where_query['_id'] = array('$gt' => new MongoId($this->get_id()));
			if($this->name == 'Salesorders' && !$this->check_permission('salesorders_@_view_worktraq_@_view')){
	        	$where_query['code'] = array('$not' => new MongoRegex('/WT-/'));
	        }
			$arr_nexts = $this->opm->select_one($where_query, array('_id'), array('_id' => 1));
		}
		if (isset($arr_nexts['_id']))
			$nexts = $arr_nexts['_id'];
		else
			$nexts = '';
		$this->redirect('/' . $this->params->params['controller'] . '/entry/' . $nexts);
		die;
	}

	// link to lasts entry
	public function lasts() {
		$where_query = $this->arr_search_where();
		if($this->name == 'Salesorders' && !$this->check_permission('salesorders_@_view_worktraq_@_view')){
        	$where_query['code'] = array('$not' => new MongoRegex('/WT-/'));
        }
		$arr_tmp = $this->opm->select_one($where_query, array('_id'), array('_id' => -1));
		if (isset($arr_tmp['_id']))
			$direct = $arr_tmp['_id'];
		else
			$direct = '';
		$this->redirect('/' . $this->params->params['controller'] . '/entry/' . $direct);
		die;
	}

	// link to lasts entry
	public function get_last_id() {
		$where_query = array();
		if($this->name == 'Salesorders' && !$this->check_permission('salesorders_@_view_worktraq_@_view')){
        	$where_query['code'] = array('$not' => new MongoRegex('/WT-'));
        }
        $this->identity($where_query);
		$arr_tmp = $this->opm->select_one($where_query, array('_id'), array('_id' => -1));
		if (isset($arr_tmp['_id']))
			return $arr_tmp['_id'];
		else
			return '';
		die;
	}

	// Action Options
	public function options($module_id = '', $module_name = '') {
		//Kiểm tra quyền view option
		if(!$this->check_permission($this->name.'_@_options_@_',true))
			$this->error_auth();
		//$this->set('set_sumline',25); //set in custom of ModuleController
		$ids = $this->get_id();
		if ($module_name == '' && isset($this->opm->arr_settings))
			$module_name = $this->opm->arr_settings['module_name'];

		$this->selectModel('Permission');
		$permission = $this->Permission;
		$controller = '';
		if(isset($this->params->params['controller']))
			$controller = $this->params->params['controller'];
		$query = $permission->select_one(array('controller' => $controller));
		$arr_opt = $query;

		if (isset($arr_opt['option_list'])){
			//Tung, dùng để filter option list dựa trên permission, 6/1/2013
			/*
				-Lấy tất cả permission của user login (1)
				-Tạo 1 chuỗi controller_@_options_@_ (2)
				-So sánh chuỗi (2) với (1) để lấy ra 1 mảng chứa codekey của option mà user có quyền (3)
				-Duyệt mảng option_list của permission, nếu code_key của mảng option tồn tại trong (3), đẩy vào mảng $arr_option
				-Cuối cùng set vào view
				-Rườm ra như vậy, mục đích để option hiển thị chính xác những codekey tồn tại trong permission, và view cũng hiển thị hàng cột chính xác như chỉ có duy nhất những codekey trong quyền tồn tại.
			*/
			$data = $this->get_all_permission();
			if(!isset($data['all'])){

				$arr_option = array();
				$search = strtolower($this->name).'_@_options_@_';
				$option = array();
				foreach($data as $key=>$value){
					if(strpos($key, $search)!==false){
						$key = explode("_@_", $key);
						$option[] = $key[2];
					}
				}
				foreach($arr_opt['option_list'] as $key_options=>$options){
					foreach($options as $key=>$value){
						foreach($value as $k=>$v){
							if(isset($v['deleted'])&&$v['deleted']) continue;
							if(in_array($v['codekey'], $option) || $this->system_admin){
								if(isset($v['permission']) ){
									$is_array = false;
									if(strpos($v['permission'],'||')!==false){
										$is_array = true;
										$type="or";
										$delimiter = '||';
									} else if (strpos($v['permission'],'&&')!==false){
										$is_array = true;
										$type="and";
										$delimiter = '&&';
									}
									if($is_array){
										$permission = explode($delimiter, $v['permission']);
										if($this->check_permission_array($permission,$arr_permission,$type)){
											$arr_option['option_list'][$key_options][$key][$k] = $v;
										}
									} else if( $this->check_permission($v['permission']) )
										$arr_option['option_list'][$key_options][$key][$k] = $v;
								}
								else if(!isset($v['permission']))
									$arr_option['option_list'][$key_options][$key][$k] = $v;
							}
							if( (isset($v['belong_to']) || !empty($v['belong_to'])) && !$this->system_admin ) {
								$count = count($v['belong_to']);
								$i = 0;
								foreach($v['belong_to'] as $belong_to){
									if(!$this->check_permission($belong_to)) {
										$i++;
									} else {
										break;
									}
								}
								if($i == $count && isset($arr_option['option_list'][$key_options][$key][$k]))
									unset($arr_option['option_list'][$key_options][$key][$k] );
							}
						}
					}
				}
				//End filter
			} else {
				$arr_option = $arr_opt;
			}
			//pr($arr_option['option_list']);die;
			$this->set('option_list', $arr_option['option_list']);
		}
		if (1 == 1) {
			$this->render('../Elements/options');
		}
		$this->set('module_id', $module_id);
	}

	// Action Options
	public function swith_options($keys) {
		if(!$this->check_permission($this->name.'_@_options_@_'.$keys))
			die;
	}

	// Action cancel
	public function cancel() {
		$this->redirect('/' . $this->params->params['controller'] . '/entry');
		die;
	}

	// Action Options
	public function continues() {
		$this->redirect('/' . $this->params->params['controller'] . '/entry');
		die;
	}

	// Action find_all
	public function find_all() {
		$this->Session->write($this->name . '_where', array());
		$this->redirect('/' . $this->params->params['controller'] . '/lists');
		die;
	}

	// Action omit
	public function omit() {
		$this->redirect('/' . $this->params->params['controller'] . '/lists');
		die;
	}

	// Action sorts
	public function sorts() {
		$this->redirect('/' . $this->params->params['controller'] . '/lists');
		die;
	}

	// Action prints
	public function prints() {
		$this->redirect('/' . $this->params->params['controller'] . '/lists');
		die;
	}

	// Entry search
	public function entry_search() {
		if(!$this->check_permission($this->name.'_@_entry_@_view'))
			$this->error_auth();
		$arr_set = $this->opm->arr_settings;
		$this->set('search_class', 'jt_input_search');
		$this->set('search_class2', 'jt_select_search');
		$this->set('search_flat', 'placeholder="1"');
		$where = array();

		if ($this->Session->check($this->name . '_where'))
			$where = $this->Session->read($this->name . '_where');
		if (count($where) > 0) {
			foreach ($arr_set['field'] as $ks => $vls) {
				foreach ($vls as $field => $values) {
					if (isset($where[$field])) {
						$arr_set['field'][$ks][$field]['default'] = '';//$where[$field]['values'];//tạm thời không đưa các giá trị đã tìm kiếm ra giao diện tìm kiếm
					}
				}
			}
		}
		$this->set('arr_settings', $arr_set);
		//$this->sub_tab();
	}

	//Popup action
	public function popup($keys='') {
		if($keys!='')
			$this->set('ppkey',$keys);
		$this->is_popup = true;
		$this->lists();
		$this->layout = 'ajax';
		// if (isset($this->params->params['pass'][0]))
		//     $this->set('keys', $this->params->params['pass'][0]);
		// else
		//     $this->set('keys', '');
		// BaoNam
		$this->set('keys', $keys);
		$this->set('key', $keys);
		if (!$this->element_exit('popup')) // dung theme chung
			$this->render('../Elements/popup');
	}

	public function printpdf() {
		if (isset($this->params->params['pass'][0])) {
			$this->layout = 'ajax';
			$this->set('file_name', $this->params->params['pass'][0]);
		}
		else
			$this->redirect('/' . $this->params->params['controller'] . '/lists');
	}

	//kiem tra element trong Module
	public function element_exit($sub_tab) {
		if (isset($this->name) && is_file(APP . 'View' . DS . 'Themed' . DS . ucfirst($this->theme) . DS . $this->name . DS . $sub_tab . '.ctp')){
			return true;
		}else{
			return false;
		}
	}

	// Subtab
	function sub_tab($sub_tab = '', $item_id = null) {
		// Kiểm tra $_GET để khi bấm vào thẻ a href thì sẽ vào trực tiếp tab liên quan luôn mà không cần click chọn tab nữa
		if( isset($_GET['sub_tab']) ){
			$sub_tab = $_GET['sub_tab'];
		}
		if ($sub_tab == '') {
			if (!$this->Session->check($this->name . '_sub_tab')) {
				$sub_tab = $this->sub_tab_default;
				$this->Session->write($this->name . '_sub_tab', $sub_tab);
			} else {
				$sub_tab = $this->Session->read($this->name . '_sub_tab');
			}
		} else {
			$this->Session->write($this->name . '_sub_tab', $sub_tab);
		}

		$this->set('sub_tab', $sub_tab);
		if ($item_id)
			$this->$sub_tab($item_id);
		if ($this->request->is('ajax')) {
			if ($this->element_exit($sub_tab))
				$this->render($sub_tab);
			else
				$this->render('../Elements/box_type/subtab_box_default');
		}
	}

	// general options of selects in arr_setting
	function general_select($arr_field = array()) {
		$class = get_class($this);
		$datas = Cache::read($class.'_all_select');
		if(!$datas){
			$datas = array();
			if (isset($arr_field) && is_array($arr_field)) {
				$this->selectModel('Setting');
				foreach ($arr_field as $keys => $arr_value) {
					foreach ($arr_value as $k => $arr_v) {
						if (isset($arr_v['droplist']) && isset($arr_v['type']) && $arr_v['type'] == 'select'){
							$datas[$k] = $this->Setting->select_option_vl(array('setting_value' => $arr_v['droplist']));
						}
					}
				}
			}
			Cache::write($class.'_all_select',$datas);
		}
		return $datas;
	}

	function get_option_status_color( $option_name ){

		$this->selectModel('Setting');
		$arr_option = $this->Setting->select_one(array('setting_value' => $option_name), array('option'));
		$arr_status = $arr_status_color = array();
		foreach ($arr_option['option'] as $key => $value) {
			if ($value['deleted'])
				continue;
			$arr_status[$value['value']] = $value['name'];
			$arr_status_color[$value['value']] = $value['color'];
			$arr_status_move[$value['value']] = $value['cal_enabled_move'];

		}
		return array( $arr_status, $arr_status_color, $arr_status_move );
	}

	function entry_init($id, $num_position, $model, $controller) {

		$this->selectModel($model);

		$cond = array();
		if( $this->Session->check($controller.'_entry_search_cond') ){
			$cond = $this->Session->read($controller.'_entry_search_cond');
		}

		$this->identity($cond);

		// TH vào khi click entry bình thường
		if ($id != '0') {
			if ($id == 'first') { // called from menu_entry.ctp
				$num_position = 1;
				$arr_tmp = $this->$model->select_one($cond, array(), array('_id' => 1));
			} elseif ($id == 'last') { // called from menu_entry.ctp
				$arr_tmp = $this->$model->select_one($cond, array(), array('_id' => -1));
			} else {
				$arr_tmp = $this->$model->select_one(array('_id' => new MongoId($id)));
				if (!isset($arr_tmp['_id'])) {
					echo 'This record is deleted or not exist, please come back or click to go to <a href="/">JobTraq - Home page</a>';die;
				}
			}
			$this->Session->write($model . '_entry_id', (string) $arr_tmp['_id']);
		} else {

			if ($this->Session->check($model . '_entry_id')) {
				$arr_tmp = $this->$model->select_one(array('_id' => new MongoId($this->Session->read($model . '_entry_id'))));
				if (!isset($arr_tmp['_id'])) {
					$this->Session->delete($model . '_entry_id');
					$this->redirect('/' . $controller . '/entry');
					die;
				}
				$id = (string) $arr_tmp['_id'];
			}

			if (!isset($arr_tmp['_id'])) {
				$arr_tmp = $this->$model->select_one($cond, array(), array('_id' => -1));
			}
		}

		if (!isset($arr_tmp['_id'])) {
			$this->redirect('/' . $controller . '/add');
			die;
		}
		$this->Session->write($this->name . 'ViewId',(string) $arr_tmp['_id']);

		// -------------------- kiểm tra phân quyền -------------------------
		if( !$this->check_permission($controller.'_@_entry_@_view') ){
			$this->error_auth();
		}

		// this valriable array use in menu_entry.ctp
		$arr_prev = $this->$model->select_one(array_merge($cond, array('_id' => array('$lt' => $arr_tmp['_id']))), array('_id'), array('_id' => -1));
		$this->set('arr_prev', $arr_prev);
		$arr_next = $this->$model->select_one(array_merge($cond, array('_id' => array('$gt' => $arr_tmp['_id']))), array('_id'), array('_id' => 1));
		$this->set('arr_next', $arr_next);
		$sum = $this->$model->count($cond);
		$this->set('sum', $sum);
		if ($id == '0') {
			$num_position = $sum;
		} else {
			$num_position = $sum - $this->$model->count(array_merge($cond, array('_id' => array('$gt' => $arr_tmp['_id']))));
		}
		$this->set('num_position', $num_position); // đếm entry thứ mấy trong tất cả entry
		return $arr_tmp;
	}

	// show footer info in entry footer
	public function show_footer_info($arr_tmp = array(), $arr_contact_id = array()) {

		// BaoNam đổi 04/09/2013 09:25
		// $arr_contact_id = array();
		$arr_contact_id[] = $arr_tmp['created_by'];
		$arr_contact_id[] = $arr_tmp['modified_by'];
		$arr_contact_id = array_values($arr_contact_id);
		$this->selectModel('Contact');
		$arr_contact = $this->Contact->select_list(array(
			'arr_where' => array(
				'_id' => array('$in' => $arr_contact_id)
			),
			'arr_field' => array('_id', 'first_name', 'last_name'),
		));
		$this->set('arr_contact', $arr_contact);

		// show info user in the footer
		$arr_info_footer = array();
		if (isset($arr_tmp['_id'])) {
			$arr_info_footer['date_created'] = $this->Contact->format_date($arr_tmp['_id']->getTimestamp());
			$arr_info_footer['date_created_hour'] = date('H:i', $arr_tmp['_id']->getTimestamp());
		}
		if (isset($arr_tmp['date_modified'])) {
			$arr_info_footer['date_modified'] = $this->Contact->format_date($arr_tmp['date_modified']->sec);
			$arr_info_footer['date_modified_hour'] = date('H:i', $arr_tmp['date_modified']->sec);
		}
		if (isset($arr_contact[(string) $arr_tmp['created_by']]))
			$arr_info_footer['created_by'] = $arr_contact[(string) $arr_tmp['created_by']];
		else
			$arr_info_footer['created_by'] = 'System Admin';
		if (isset($arr_contact[(string) $arr_tmp['modified_by']]))
			$arr_info_footer['modified_by'] = $arr_contact[(string) $arr_tmp['modified_by']];
		else
			$arr_info_footer['modified_by'] = 'System Admin';

		if((string)$arr_tmp['modified_by'] == '200000000000000000000000'){
			$arr_info_footer['modified_by'] = $arr_tmp['modified_by_name'];
		}
		if((string)$arr_tmp['created_by'] == '200000000000000000000000'){
			$arr_info_footer['created_by'] = $arr_tmp['created_by_name'];
		}

		if (isset($arr_tmp['_id']))
			$this->set('arr_info_footer', $arr_info_footer);
	}




	//========== OPTION ===========
	public function save_default(array $arr_input = array()) {
		if (isset($_POST['arr'])) {
			$arr_input = (array) json_decode(stripslashes($_POST['arr']));
			$arr_input2 = (array) json_decode(stripslashes($_POST['arr']));
			reset($arr_input['value_object']);
			$first_key = key($arr_input['value_object']);
			if ($this->reset_default($arr_input['opname'], $first_key, 0))
				;
			$this->save_option($arr_input);
		}
		die;
	}

	public function reset_default($opname = '', $defaultkey = '', $vls = 0) {
		$ids = $this->get_id();
		if ($ids != '') {
			$arr_tmp = $this->opm->select_one(array('_id' => new MongoId($ids)));
			$data_insert = $options = array();
			if (isset($arr_tmp[$opname])) {
				$options = (array) $arr_tmp[$opname];
				foreach ($options as $keys => $values) {
					$options[$keys][$defaultkey] = $vls;
				}
			}
			$data_insert[$opname] = $options;
			$data_insert['_id'] = $ids;
			if ($this->opm->save($data_insert))
				return true;
			else
				return false;
		}
		else
			echo false;
		die;
	}

	// input array(1=opname,2=value_str,3=opid,0=keys)
	public function save_option(array $arr_input = array()) {
		$ids = $this->get_id();
		if(isset($_POST['mongo_id']) && $_POST['mongo_id']!='')
			$ids = $_POST['mongo_id'];
		if(isset($_POST['fieldchage']))
			$fieldchage = $_POST['fieldchage'];
		else
			$fieldchage = '';
		if ($ids != '') {
			if (isset($_POST['arr']))
				$arr_input = (array)json_decode($_POST['arr']);
				// $arr_input = (array) json_decode(stripslashes($_POST['arr'])); // BaoNam: stripslashes sẽ gây lỗi nếu có dấu hai nháy "

			//nhận giá trị record
			$arr_tmp = $this->opm->select_one(array('_id' => new MongoId($ids)));
			$options = array();

			if (isset($arr_tmp[$arr_input['opname']]))
				$options = $arr_tmp[$arr_input['opname']];

			//tạo giá trị mới
			if (!is_numeric($arr_input['opid']) || $arr_input['keys'] == 'add') {
				$newopt = array();
				//nhận giá trị default
				$newopt = $this->get_default_rel($arr_input['opname']);

				//nhận giá trị input
				if (isset($arr_input['value_object'])) {
					$temps = array();
					$temps = (array) $arr_input['value_object'];
					foreach ($temps as $keys => $values) {

						if (preg_match("/_id$/", $keys) && strlen($values) == 24)
							$newopt[$keys] = new MongoId($values); //neu la id
						else if (preg_match("/_date$/", $keys)){
							 //neu la ngay
								$newopt[$keys] = new MongoDate($values);
						}
						else if (preg_match("/_price$/", $keys))
							$newopt[$keys] = (float) $values; //neu la gia
						else
							$newopt[$keys] = $values;
					}
				}
				$newopt = (array) $newopt;
				$options[] = $newopt;

				$data_insert = $arrass = $this->arr_associated_data($arr_input['opname'],$options,count($options)-1,$fieldchage);//set các field liên quan
				unset($arrass[$arr_input['opname']]);
				$arrass = $this->set_mongo_array($arrass);//lọc object
				$data_insert['_id'] = $ids;
				if (count($options) > 0)
					if ($this->opm->save($data_insert)){
						if(count($arrass)>0)
							echo json_encode($arrass);
						else
							echo (count($options)-1);
					}
			}else {//update giá trị cũ
				if (isset($arr_input['value_object'])) {
					$temps = array();
					$temps = (array) $arr_input['value_object'];

					foreach ($temps as $keys => $values) {
						if (preg_match("/_id$/", $keys) && strlen($values) == 24)
							$options[$arr_input['opid']][$keys] = new MongoId($values); //neu la id
						else if (preg_match("/_date$/", $keys)){
							if($values != '')
								$options[$arr_input['opid']][$keys] = new MongoDate($values); //neu la ngay
							else
								$options[$arr_input['opid']][$keys] = '';
						}
						else if (is_numeric($values) && is_float((float) $values))
							$options[$arr_input['opid']][$keys] = (float) $values; //neu la gia
						else
							$options[$arr_input['opid']][$keys] = $values;
					}

				}
				$arrass = $data_insert = array();
				$data_insert = $arrass = $this->arr_associated_data($arr_input['opname'],$options,$arr_input['opid'],$fieldchage);
				unset($arrass[$arr_input['opname']]);
				$arrass = $this->set_mongo_array($arrass);//lọc object
				$data_insert['_id'] = $ids;

				if ($this->opm->save($data_insert)){
					if(count($arrass)>0)
						echo json_encode($arrass);
					else
						echo $arr_input['opid'];
				}
			}
		}
		else
			echo '';
		die;
	}




	//nhận giá trị default của field trong box
	public function get_default_rel($opname = '', $entryfield = 0) {
		$new_opt = array();
		$new_opt['deleted'] = false;
		if ($opname != '') {
			if ($entryfield != 0)
				$field_list = $this->opm->get_fields_rel($opname);
			else
				$field_list = $this->opm->arr_field_rel($opname);
			if (count($field_list) > 0)
				foreach ($field_list as $ks => $vs) { //lập vòng field của option
					if (isset($vs['indata']) && $vs['indata'] == '0') //ko lưu field indata=0
						$m = 0;
					else if (isset($vs['default']) && isset($vs['type']) && $vs['type'] == 'price')
						$new_opt[$ks] = (float) $vs['default'];
					else if (isset($vs['default']))
						$new_opt[$ks] = $vs['default'];
					else
						$new_opt[$ks] = '';
				}
		}
		return $new_opt;
	}

	//field chính có rel_name link đến 1 box trong subtab,dựa vào nhận danh sách field
	public function get_fields_rel($field_name = '') {
		if ($field_name != '') {
			$name_field = array();
			$keys = '';
			$name_field = $this->opm->arr_field_key('rel_name');
			$keys = $name_field[$field_name]; //nhận tên box
			return $this->opm->arr_field_rel($keys);
		}
		else
			return '';
	}

	//give data of fields in field_list
	public function reload_field($field_list = '', $module_id = '') {
		if (isset($_POST['field_list']))
			$field_list = $_POST['field_list'];
		if (isset($_POST['module_id']))
			$module_id = $_POST['module_id'];

		if (strlen($module_id) != 24)
			$module_id = $this->get_id();

		$field_list = explode(",", $field_list);
		$arr_tmp = $this->opm->select_one(array('_id' => new MongoId($module_id)), $field_list);
		$new_str = '';
		foreach ($field_list as $vals) {
			if (isset($arr_tmp[$vals]))
				$new_str .= $arr_tmp[$vals] . '@';
		}

		echo $new_str;
		die;
	}

	// delete option
	public function deleteopt() {
		// neu ton tai session id
		if ($this->Session->check($this->name . 'ViewId') && isset($this->params->params['pass'][0])) {
			$idsession = $this->Session->read($this->name . 'ViewId');
			$arr_tmp = $this->opm->select_one(array('_id' => new MongoId($idsession)));
			$idopt = $this->params->params['pass'][0];
			$idopt = explode('@', $idopt);
			$opname = $idopt[1];
			$idopt = $idopt[0];
			$options = array();
			if (isset($arr_tmp[$opname]) && isset($opname))
				$options = $arr_tmp[$opname];
			if($opname == 'products' && in_array($this->modelName, array('Quotation', 'Salesorder', 'Salesinvoice'))) {
				if( isset($options[$idopt]['sku']) && preg_match('/^SHP/', $options[$idopt]['sku']) ) {
					$arr_insert['shipping_cost'] = $arr_tmp['shipping_cost'] - $options[$idopt]['sub_total'];
				}
			}
			$options[$idopt]['deleted'] = true;
			$arr_insert[$opname] = $options;
			$arr_insert['_id'] = $idsession;
			//thong tin lien quan


			if ($this->opm->save($arr_insert)){
				$this->delete_all_associate($idopt,$opname);
				echo $this->opm->mongo_id_after_save;
			}
			if(!$this->request->is('ajax'))
				$this->redirect('/' . $this->params->params['controller'] . '/entry/' . $idsession);
		}
		else
			$this->redirect('/' . $this->params->params['controller'] . '/entry');

		die;
	}

	//search list
	public function search_list($arr_where = array()) {
		$arr_fields = $this->opm->arr_field_key('type');
		//BaoNam
		//Nhận danh sách giá trị các field từ giao diện
		$post = $_POST; //pr($post);
		//Lọc ký hiệu đặc biệt
		$post = $this->Common->strip_search($post); // replace some special characters to search as LIKE in mysql
		if(isset($post['data']))
			$data = $post['data'];
		if(!empty($data))
		{
			$where_invoice_address = array();
			$where_shipping_address = array();
			$tmp_array = array('contact_address_1','contact_address_2','contact_address_3','contact_town_city','contact_province_state','contact_province_state_id','contact_zip_postcode','contact_country','contact_country_id');
			foreach($tmp_array as $value){
				if(isset($data['invoice'][$value]) &&strlen($data['invoice'][$value])>0){
					$where_invoice_address[$tmp_value] = new MongoRegex( '/'. $data['invoice'][$value].'/i');
				}
			}
			$tmp_array = array('shipping_contact_name','shipping_address_1','shipping_address_2','shipping_address_3','shipping_town_city','shipping_province_state','shipping_province_state_id','shipping_zip_postcode','shipping_country','shipping_country_id');
			foreach($tmp_array as $value){
				if(isset($data['shipping'][$value]) && strlen($data['shipping'][$value])>0)
					$where_shipping_address[$value] = new MongoRegex('/'. $data['shipping'][$value].'/i');
			}
			if(!empty($where_invoice_address))
				// $where_invoice_address['deleted'] = false;
				$arr_where['invoice_address'] = array('values' => $where_invoice_address, 'operator' => 'elemMatch');
			if(!empty($where_shipping_address))
				// $where_shipping_address['deleted'] = false;
				$arr_where['shipping_address'] = array('values' => $where_shipping_address, 'operator' => 'elemMatch');
		}
		foreach ($arr_fields as $kss => $vss) {
			if (isset($post[$kss]) && trim($post[$kss]) != '') {
				$arr_where[$kss]['values'] = trim($post[$kss]);
				//nếu là loại select
				if($vss=='select'){
					$arr_where[$kss]['operator'] = 'other';
					$arr_where[$kss]['values'] = array(
						                                $kss => array('$in' => array($post[$kss], $post[$kss.'_id']))
						                            );

				//nếu là loại checkbox
				}else if($vss=='checkbox'){
					$arr_where[$kss]['operator'] = '=';
					if($post[$kss]=='on')
						$arr_where[$kss]['values'] = 1;
					else
						$arr_where[$kss]['values'] = 0;

				//nếu là loại id
				}else if (preg_match("/_id$/", $kss) && strlen($arr_where[$kss]['values']) == 24) {
					$arr_where[$kss]['operator'] = '=';
					$arr_where[$kss]['values'] = new MongoId($arr_where[$kss]['values']);
					if($kss=='our_rep_id' || $kss=='our_csr_id')
						$kss2 = str_replace("_id","",$kss);
					else
						$kss2 = str_replace("_id","_name",$kss);
					if(isset($post[$kss2]))
						$arr_where[$kss2]['values'] = trim($post[$kss2]);
				//nếu là loại ngày
				}else if (preg_match("/_date$/", $kss)) {
                    if( strpos($arr_where[$kss]['values'], ' - ') !== false ) {
                        list($begin, $end) = explode(' - ', $arr_where[$kss]['values']);
                        $begin = $this->Common->strtotime(trim($begin) . '00:00:00');
                        $end = $this->Common->strtotime(trim($end) . '23:59:59');
                    } else {
                        $begin = $this->Common->strtotime($arr_where[$kss]['values'] . '00:00:00');
                        $end = $this->Common->strtotime($arr_where[$kss]['values'] . '23:59:59');
                    }
					$arr_where[$kss]['>=']['operator'] = 'day>=';
					$arr_where[$kss]['>=']['values'] = new MongoDate($begin);
					$arr_where[$kss]['<=']['operator'] = 'day<=';
					$arr_where[$kss]['<=']['values'] = new MongoDate($end);

				//các loại khác
				} else {
                    $arr_where[$kss]['operator'] = 'LIKE';
                    if( strpos($arr_where[$kss]['values'], '; ') !== false ) {
                        $values = $arr_where[$kss]['values'];
                        $values = explode('; ', $values);
                        foreach($values as $k => $v) {
                            $values[$k] = new MongoRegex('/'. trim($v) .'/i');
                        }
                        $arr_where[$kss] = array(
                                            'operator' => 'other',
                                            'values' => array(
                                                    $kss => array( '$in' => $values )
                                                )

                                        );
                    }
                }


				//nếu giá trị là loại số
				if (is_numeric($arr_where[$kss]['values'])){
					if ($vss=='price'){
						unset($arr_where[$kss]['operator']);
						$arr_where[$kss]['>=']['operator'] = '>=';
						$arr_where[$kss]['>=']['values'] = (float)$arr_where[$kss]['values'] - 0.05;
						$arr_where[$kss]['<=']['operator'] = '<=';
						$arr_where[$kss]['<=']['values'] = (float)$arr_where[$kss]['values'] + 0.05;
					}
					else{
						$arr_where[$kss]['operator'] = '=';
						$arr_where[$kss]['values'] = (int)$arr_where[$kss]['values'];
					}
				}

			}
		}
		if($this->name == 'Products'){
			if(isset($arr_where['name'])){
				$result = $this->db->command(
					    array(
					        'text' => 'tb_product', //this is the name of the collection where we are searching
					        'search' => $post['name'], //the string to search
					        'limit' => 1000,
					        'project' => Array( //the fields to retrieve from db
					            'title' => 'name'
					        )
					    )
					);
				if($result['ok'] && !empty($result['results'])){
					$arr_results = array();
					foreach($result['results'] as $value){
						if($value['score'] < 0.4) continue;
						if(!isset($value['obj']['_id']) || !is_object($value['obj']['_id'])) continue;
						$arr_results[] = $value['obj']['_id'];
					}
					if(!empty($arr_results)){
						unset($arr_where['name']);
						$arr_where['_id']['operator'] = 'other';
						$arr_where['_id']['values'] = array(
						                                '_id' => array('$in' => $arr_results)
						                                    );
					}
				}
			}
		}
		$arrWhere['products']['$elemMatch'] = [];
		if( isset($post['sku_0']) && !empty($post['sku_0']) ) {
			$arrWhere['products']['$elemMatch']['sku'] = new MongoRegex('/'. $post['sku_0'] .'/i');
		}
		if( isset($post['products_name_0']) && !empty($post['products_name_0']) ) {
			$arrWhere['products']['$elemMatch']['products_name'] = new MongoRegex('/'. $post['products_name_0'] .'/i');
		}
		if( isset($post['sell_by_0']) && !empty($post['sell_by_0']) ) {
			$arrWhere['products']['$elemMatch']['sell_by'] = new MongoRegex('/'. $post['sell_by_0'] .'/i');
		}
		if( isset($post['sizew_0']) && !empty($post['sizew_0']) ) {
			$arrWhere['products']['$elemMatch']['sizew'] = (float) $post['sizew_0'];
		}
		if( isset($post['sizeh_0']) && !empty($post['sizeh_0']) ) {
			$arrWhere['products']['$elemMatch']['sizeh'] = (float) $post['sizeh_0'];
		}
		if( isset($post['sell_price_0']) && !empty($post['sell_price_0']) ) {
			$arrWhere['products']['$elemMatch']['sell_price'] = (float) $post['sell_price_0'];
		}
		if( isset($post['unit_price_0']) && !empty($post['unit_price_0']) ) {
			$arrWhere['products']['$elemMatch']['unit_price'] = (float) $post['unit_price_0'];
		}
		if( isset($post['custom_unit_price_0']) && !empty($post['custom_unit_price_0']) ) {
			$arrWhere['products']['$elemMatch']['custom_unit_price'] = (float) $post['custom_unit_price_0'];
		}
		if( isset($post['quantity_0']) && !empty($post['quantity_0']) ) {
			$arrWhere['products']['$elemMatch']['quantity'] = (float) $post['quantity_0'];
		}
		if( !empty($arrWhere['products']['$elemMatch']) ){
			$arrWhere['products']['$elemMatch']['deleted'] = false;
			$arr_where['products'] = array(
				'values' => $arrWhere
			);
        	$arr_where['products']['operator'] = 'other';
		}
		$arrOperator = array('<' => '$lt', '>' => '$gt', '<=' => '$lte', '>=' => '$gte');
		foreach (['sum_sub_total', 'sum_tax', 'sum_amount'] as $sum) {
			if( !isset($post[$sum]) ) continue;
			if(  strpos($post[$sum], '-') !== false ) {
				$number = explode('-', $post[$sum]);
				if( !isset($number[0]) || !isset($number[1]) ) continue;
				$from = $number[0];
				$to = $number[1];
        		$arr_where[$sum]['operator'] = 'other';
        		$arr_where[$sum]['values'] = array(
        										$sum => array(
        												'$gte' => (float)$from,
        												'$lte' => (float)$to
        											)
        									);
        		continue;
			}
			foreach($arrOperator as $operator => $mongoOperator) {
				if( strpos($post[$sum], $operator) !== false ) {
					$arr_where[$sum]['operator'] = 'other';
					$arr_where[$sum]['values'] = array(
													$sum => array(
															$mongoOperator => (float)$post[$sum],
														)
												);
					break;
				}
			}
		}
		if(isset($post['products_name']) && !empty($post['products_name']) ){
			if(is_numeric($post['products_name'])){
				$number_from = (float)$post['products_name'] - 0.35;
				$number_to = (float)$post['products_name'] + 0.35;
				$arr_where['products']['operator'] = 'or';
				$arr_where['products']['values'] = array(
				                                         array(
				                                            'products' => array(
				                                                                '$elemMatch' => array(
				                                                                                    'deleted' => false,
       	                                          													'sizew' => array(
       	                                          													                 '$lte' => $number_to,
       	                                          													                 '$gte' => $number_from
       	                                          													                 ),
				                                                                                      )
				                                                                )
				                                               ),
				                                        array(
				                                            'products' => array(
				                                                                '$elemMatch' => array(
				                                                                                    'deleted' => false,
       	                                          													'sizeh' => array(
       	                                          													                 '$lte' => $number_to,
       	                                          													                 '$gte' => $number_from
       	                                          													                 ),
				                                                                                      )
				                                                                )
				                                               ),
				                                        array(
				                                            'products' => array(
				                                                                '$elemMatch' => array(
				                                                                                    'deleted' => false,
       	                                          													'quantity' => array(
       	                                          													                 '$lte' => $number_to,
       	                                          													                 '$gte' => $number_from
       	                                          													                 ),
				                                                                                      )
				                                                                )
				                                               ),
				                                        array(
				                                            'products' => array(
				                                                                '$elemMatch' => array(
				                                                                                    'deleted' => false,
       	                                          													'sell_price' => array(
       	                                          													                 '$lte' => $number_to,
       	                                          													                 '$gte' => $number_from
       	                                          													                 ),
				                                                                                      )
				                                                                )
				                                               ),
				                                         array(
				                                            'products' => array(
				                                                                '$elemMatch' => array(
				                                                                                    'deleted' => false,
       	                                          													'custom_unit_price' => array(
       	                                          													                 '$lte' => $number_to,
       	                                          													                 '$gte' => $number_from
       	                                          													                 ),
				                                                                                      )
				                                                                )
				                                               ),
       	                                          );
			} else {
				$arr_where['products']['operator'] = 'elemMatch';
				$arr_where['products']['values'] = array(
       	                                          'deleted' => false,
       	                                          'products_name' => new MongoRegex('/'.$post['products_name'].'/i'),
       	                                          );
			}
		}
		if($this->name == 'Salesorders' && !$this->check_permission('salesorders_@_view_worktraq_@_view')){
			if(isset($arr_where['code'])) {
				if( strpos($arr_where['code']['values'], 'WT-') !== false )
					$arr_where['code']['values'] = -1;
				$arr_where['code'] = array(
					'values' => array(
						'code' => new MongoRegex('/'.$arr_where['code']['values'].'/'))
					);
			} else {
				$arr_where['code'] = array(
					'values' => array(
						'code' => array( '$not' => new MongoRegex('/'.$arr_where['code']['values'].'/')) )
					);
			}
        	$arr_where['code']['operator'] = 'other';
        }
		if (is_array($arr_where) && count($arr_where) > 0) {
			$this->Session->write($this->name . '_where', $arr_where);
		}
		//check

		$where_query = $this->arr_search_where();
		$amount = $this->opm->count($where_query);
		if ($amount > 1) {
			$arr_query = $this->opm->select_one($where_query, array('_id'), array('_id' => -1));
			if (isset($arr_query['_id'])) {
				$arr_query['_id'] = (array) $arr_query['_id'];
				$this->Session->write($this->name . 'ViewId', $arr_query['_id']['$id']);
			}
			echo 'lists';
		} else if ($amount == 1) {
			$arr_query = $this->opm->select_one($where_query, array('_id'));
			if (isset($arr_query['_id']))
				echo 'entry/' . $arr_query['_id'];
			else
				echo 'entry';
		}else {
			$this->Session->write($this->name . '_where', array());
			echo '0';
		}
		die;
	}

	// update các giá trị array được set là default
	public function update_default($opname = '', $default = 'default', $fields = '', $values = '') {
		$ids = $this->get_id();
		if (isset($_POST['opname']))
			$opname = $_POST['opname'];
		if (isset($_POST['defaultfield']))
			$default = $_POST['defaultfield'];
		if (isset($_POST['fields']))
			$fields = $_POST['fields'];
		if (isset($_POST['values']))
			$values = $_POST['values'];

		if ($opname != '' && $fields != '' && $ids != '') {
			$arr_tmp = $this->opm->select_one(array('_id' => new MongoId($ids)), array($opname));
			$flat = 0;
			if (isset($arr_tmp[$opname])) {
				$arr_insert = $arr_tmp[$opname];
				foreach ($arr_tmp[$opname] as $kss => $vss) {
					$arr_insert[$kss] = $vss;
					if (!($vss['deleted']) && (int) $vss[$default] == 1) {
						$arr_insert[$kss][$fields] = $values;
						$flat = 1;
					}
				}
			}
			if ($flat == 0) {
				$arr_new['deleted'] = false;
				$arr_new[$default] = 1;
				$arr_new[$fields] = $values;
				$arr_insert[] = $arr_new;
			}

			$data_insert[$opname] = $arr_insert;
			$data_insert['_id'] = $ids;
			if ($this->opm->save($data_insert))
				echo 'Saved';
		}
		die;
	}

	public function province($country_id = '') {
		$original_country_id = $country_id;
		$datas = Cache::read('arr_province_'.$original_country_id);
		if(!$datas){
			$this->selectModel('Province');
			$where = array();
			if ($country_id != '')
				$where['country_id'] = $country_id;
				$query = $this->Province->select_all(
					array(
						'arr_where' => $where,
						'arr_field' => array('key', 'name')
					)
			);

			$datas = array();
			foreach ($query as $kss => $arr) {
				$datas[$arr['key']] = $arr['name'];
			}
			asort($datas);
			Cache::write('arr_province_'.$original_country_id,$datas);
		}
		return $datas;
	}


	public function province_reverse($country_id = '') {
		$this->selectModel('Province');
		$where = array();
		if ($country_id != '')
			$where['country_id'] = $country_id;
			$query = $this->Province->select_all(
				array(
					'arr_where' => $where,
					'arr_field' => array('key', 'name')
				)
		);

		$datas = array();
		foreach ($query as $kss => $arr) {
			$datas[$arr['name']] = $arr['key'];
		}
		asort($datas);
		return $datas;
	}


	public function country() { // BaoNam 08/11/2013
		$arr_country = Cache::read('arr_country');
		if(!$arr_country){
			$this->selectModel('Country');
			$arr_temp = $this->Country->select_all(array('arr_order' => array('name' => 1)));
			$arr_country = array();
			foreach ($arr_temp as $key => $value) {
				$arr_country[$value['value']] = $value['name'];
			}
			// $this->selectModel('Setting');
			// $arr_temp = $this->Setting->select_option(
			//         array('setting_value' => $dropname)
			// );
			Cache::write('arr_country',$arr_country);
		}
		return $arr_country;
	}

	public function ajax_general_province($country_id = "CA") {
		if (isset($_POST['country_id']))
			$country_id = $_POST['country_id'];
		echo json_encode($this->province($country_id));
		die;
	}

	public function get_data_other_model($module_name, $field_setup, $ids) {
		$form_field = $to_field = $arr_ret = $arr_tmp = array();
		foreach ($field_setup as $kss => $vss) {
			$form_field[] = $kss;
		}
		$this->selectModel($module_name);
		$modules = $this->$module_name;
		$arr_tmp = $modules->select_one(array('_id' => new MongoId($ids)), $form_field);
		foreach ($field_setup as $kk => $vv) {
			if (isset($arr_tmp[$kk]))
				$arr_ret[$vv] = $arr_tmp[$kk];
			else
				$arr_ret[$vv] = '';
		}
		return $arr_ret;
	}

	// ajax nhận data từ 1 record của module bất kỳ
	public function get_data_form_module($module_name = 'Company', $ids = '', $arr = array()) {
		if (isset($_POST['module_name']))
			$module_name = $_POST['module_name'];
		if (isset($_POST['ids']))
			$ids = $_POST['ids'];
		if (isset($_POST['arr']))
			$arr = json_decode($_POST['arr']);
		$arr = (array)$arr;
		$form_field = array();
		$x=0;
		foreach ($arr as $keys => $values) {
			$form_field[$x] = $keys;
			$x++;
		}
		$this->selectModel($module_name);
		$modules = $this->$module_name;
		$arr_tmp = $modules->select_one(array('_id' => new MongoId($ids)), $form_field);
		$arr_return = array();
		if (isset($arr_tmp)) {
			foreach ($arr_tmp as $kk => $vv) {
				$arr_return[$kk] = $vv;
				if (preg_match("/_id$/", $kk) && is_object($vv)) {
					$arr_return[$kk] = (array) $arr_return[$kk];
					$arr_return[$kk] = $arr_return[$kk]['$id'];
				}
			}
		}
		echo json_encode($arr_return);
		die;
	}

	public function save_muti_field($ids = '', $arr_field = array(), $arr_data = array()) {
		$arr_data = $arr_insert = $arr_return = array();
		if (isset($_POST['ids']) && $_POST['ids'] != '')
			$ids = $_POST['ids'];
		else
			$ids = $this->get_id();
		if (isset($_POST['jsondata']))
			$arr_data = json_decode($_POST['jsondata']);
		if (isset($_POST['jsonfield']))
			$arr_field = json_decode($_POST['jsonfield']);
		$arr_field = (array) $arr_field;
		$arr_data = (array) $arr_data;
		foreach ($arr_field as $keys => $values) {
			if (isset($arr_data[$keys])) {
				$arr_return[$values] = $arr_insert[$values] = $arr_data[$keys];
				//set id
				if (preg_match("/_id$/", $values) && strlen($arr_data[$keys]) == 24)
					$arr_insert[$values] = new MongoId($arr_data[$keys]);
			}
		}
		if (strlen($ids) == 24) {
			$arr_insert['_id'] = new MongoId($ids);
			$arr_return['_id'] = $ids;
		} else {
			$temps = $this->opm->add('default', false);
			$temps = explode("||", $temps);
			$arr_insert['_id'] = $temps[0];
			$this->Session->write($this->name . 'ViewId', $arr_insert['_id']);
			$arr_return['_id'] = $temps[0];
		}

		if ($this->opm->save($arr_insert))
			echo json_encode($arr_return);
		else
			echo 'error';
		die;
	}




	// ajax nhận data từ 1 record của module bất kỳ và lưu vào module đang thao tác
	public function save_data_form_to($module_from = 'Company', $ids = '', $arr = array()) {
		if (isset($_POST['module_from']))
			$module_from = $_POST['module_from'];
		if (isset($_POST['ids']))
			$ids = $_POST['ids'];
		if (isset($_POST['arr']))
			$arr = json_decode(str_replace('\"', '"', $_POST['arr']));
		$form_field = array();
		$arr = (array) $arr;
		foreach ($arr as $keys => $values) {
			$form_field[] = $keys;
		}
		//get data
		$this->selectModel($module_from);
		$modules = $this->$module_from;
		$arr_tmp = $modules->select_one(array('_id' => new MongoId($ids)), $form_field);

		//send data
		$arr_return = $arr_insert = array();
		foreach ($arr_tmp as $kss => $vals) {
			if (isset($arr[$kss])) {
				$arr_return[$arr[$kss]] = $arr_insert[$arr[$kss]] = $vals;
				if (preg_match("/_id$/", $arr[$kss])) {
					if (is_object($arr_return[$arr[$kss]])) {
						$arr_return[$arr[$kss]] = (array) $arr_return[$arr[$kss]];
						$arr_return[$arr[$kss]] = $arr_return[$arr[$kss]]['$id'];
					}
					if (is_string($arr_return[$arr[$kss]]) && isset($arr_return[$arr[$kss]]) && strlen($arr_return[$arr[$kss]]) == 24)
						$arr_insert[$arr[$kss]] = new MongoId($arr_return[$arr[$kss]]);
					else
						$arr_insert[$arr[$kss]] = '';
				}
			}
		}

		//save data
		$this_ids = $this->get_id();
		if ($this_ids != '') {
			$arr_insert['_id'] = new MongoId($this_ids);
			if ($this->opm->save($arr_insert)) {
				echo json_encode($arr_return);
			}
		}
		else
			echo 'empty id';
		die;
	}

	public function reload_address($address_key = '') {
		if (isset($_POST['address_key']))
			$address_key = $_POST['address_key'];
		$ids = $this->get_id();
		$arr_tmp = $this->opm->select_one(
			array('_id' => new MongoId($ids)),
			array($address_key . 'address')
		);

		$arr_temp = array();
		if (isset($arr_tmp[$address_key . 'address'][0]))
			foreach ($arr_tmp[$address_key . 'address'][0] as $kk => $vv) {
				$arr_temp[$kk] = $vv;
				if ($kk == $address_key . 'province_state') {
					$arr_province = $this->province();
					if (isset($arr_province[$vv]))
						$arr_temp[$kk] = $arr_province[$vv];
				}else if ($kk == $address_key . 'country') {
					$arr_country = $this->country();
					if (isset($arr_country[$vv]))
						$arr_temp[$kk] = $arr_country[$vv];
				}
			}

		echo json_encode($arr_temp);
		die;
	}

	//general html cc,bcc
	public function reload_cc($company_id = '') {
		if (isset($_POST['company_id']))
			$company_id = $_POST['company_id'];
		$cc = array('' => '(blank)');
		if (isset($company_id) && strlen($company_id) == 24)
			$cc = $this->get_email_of_company($company_id);
		if (isset($_POST['company_id'])) {
			echo json_encode($cc);
			die;
		}
		else
			return $cc;
	}

	public function get_email_of_company($company_id) {
		if (isset($company_id)) {
			//set id
			if (is_object($company_id))
				$idmongo = $company_id;
			else if (strlen($company_id) == 24)
				$idmongo = new MongoId($company_id);

			//find email list
			if (isset($idmongo)) {
				$this->selectModel('Contact');
				$query = $this->Contact->select_all(array(
					'arr_where' => array('company_id' => $idmongo),
					'arr_order' => array('_id' => 1),
					'arr_field' => array('email', 'first_name', 'last_name')
				));
				$arr_contact = $query;
				$arr = array('' => '(blank)');
				$m = 0;
				foreach ($arr_contact as $kss => $vss) {
					$x = $m;
					if (isset($vss['email'])) {
						if(!filter_var($vss['email'], FILTER_VALIDATE_EMAIL)) continue;
						$m = $vss['email'];
						$arr[$m] = $vss['email'];
					}
					if (isset($vss['first_name']))
						$arr[$m] .= ' ' . $vss['first_name'];
					if (isset($vss['last_name']))
						$arr[$m] .= ' ' . $vss['last_name'];
					$m = $x + 1;
				}
				return $arr;
			}
		}
		return array();
	}

	// display Log or history
	public function history($ids = '') {
		/*echo 'Under Contruct';
		die;*/
		$arr_data = array();
		if ($ids == '')
			$ids = $this->get_id();
		if ($ids != '') {
			$where_query = array();
			$where_query['item_id'] = new MongoId($ids);
			$where_query['module'] = $this->ModuleName();
			$this->selectModel('Log');
			$this->selectModel('Contact');
			$query = $this->Log->select_all(array(
				'arr_order' => array('_id' => -1),
				'arr_where' => $where_query
			));
			$arr = $query;
			foreach ($arr as $k => $v) {
				if (isset($v['change_to']) && count($v['change_to']) > 0) {
					$created_by = $this->get_name('Contact', $v['created_by']);
					$created_date = date("d M,Y H:i:s", $v['date_modified']->sec);
					foreach($v['change_to'] as $keys=>$values){
						if(!is_array($values))
						$arr_data[] = array(
										'fieldname' => $keys,
										'created_by' => $created_by,
										'created_date' => $created_date,
										'change_from' => (isset($v['change_from'][$keys]) ? $v['change_from'][$keys] : array()),
										'change_to' => $values,
									 );
						else{
							foreach($values as $morekey=>$morevalue){
								if(!is_array($morevalue)){
									if(is_array($v['change_from'][$keys][$morekey]))
										$change_from = '';
									else
										$change_from = (isset($v['change_from'][$keys][$morekey][$kks]) ? $v['change_from'][$keys][$morekey][$kks] : '');
									if($morevalue==$change_from)
										continue;
									$arr_data[] = array(
										'fieldname' => $morekey,
										'created_by' => $created_by,
										'created_date' => $created_date,
										'change_from' => $change_from,
										'change_to' => $morevalue,
									 );


								}else
									foreach($morevalue as $kks=>$vvs){
										//echo $keys.'.'.$morekey.'.'.$kks.'<br />';
										if(is_array($vvs)){
											$vvs = (count($vvs)>0)?'ARRAY':'';
											$change_from = $vvs;
										}elseif(is_object($vvs)){
											$vvs = (string)$vvs;
											if(!isset($v['change_from'][$keys][$morekey][$kks])||!is_array($v['change_from'][$keys][$morekey][$kks]))
												$change_from = (isset($v['change_from'][$keys][$morekey][$kks]) ? (string)$v['change_from'][$keys][$morekey][$kks] : '');
											else
												$change_from = '';
										}else{
											if(!isset($v['change_from'][$keys][$morekey][$kks])||!is_array($v['change_from'][$keys][$morekey][$kks]))
												$change_from = (isset($v['change_from'][$keys][$morekey][$kks]) ? $v['change_from'][$keys][$morekey][$kks] : '');
											else
												$change_from = '';
										}
										if($vvs==$change_from)
											continue;
										$arr_data[] = array(
												'fieldname' => $keys.'.'.$morekey.'.'.$kks,
												'created_by' => $created_by,
												'created_date' => $created_date,
												'change_from' => $change_from,
												'change_to' => $vvs,
											 );
									}
							}
						}
					}

				}
			}
		}
		//pr($this->Log->arr_settings);
		//pr($arr_data);die;
		$subdatas = array();
		$subdatas['history_detail'] = $arr_data;
		$lock_settings = $this->Log->arr_settings;
		$this->set('subdatas',$subdatas);
		$this->set('name',$this->name);
		$this->set('iditem',$this->get_id());
		$this->set('lock_settings',$lock_settings);
		if (!$this->element_exit('history'))
			$this->render('../Elements/history');
		//return $arr_data;
	}

	//cal sum again after delete
	public function cal_sum_by_request($opname = '', $amount_field = '') {
		if (isset($_POST['opname']))
			$opname = $_POST['opname'];
		if (isset($_POST['amount_field']))
			$amount_field = $_POST['amount_field'];
		$sum = 0;
		if ($this->get_id() != '') {
			$query = $this->opm->select_one(
					array('_id' => new MongoId($this->get_id())), array($opname)
			);
			$arr_tmp = $query;
			if (isset($arr_tmp[$opname]) && is_array($arr_tmp[$opname]) && count($arr_tmp[$opname]) > 0) {
				foreach ($arr_tmp[$opname] as $kk => $vv) {
					if (isset($vv[$amount_field]) && !$vv['deleted'])
						$sum += (float) $vv[$amount_field];
				}
			}
		}
		echo $sum;
		die;
	}


	//scan setting and get data for type Select
	public function set_select_data_list($boxtype='panel',$subtab=''){
		$listdata = array();
		$arr_set = $this->opm->arr_settings;
		$this->selectModel('Setting');
		if($boxtype=='panel' && $subtab!=''){
			return $listdata;
		}else if($boxtype=='relationship' && $subtab!=''){
			foreach($arr_set['relationship'][$subtab]['block'] as $bkey => $arr_box){
				if(isset($arr_box['field'])){
					foreach($arr_box['field'] as $fk=>$fv){
						if(isset($fv['type']) && $fv['type']=='select'){
							$listdata[$fk] = $this->Setting->select_option_vl(array('setting_value'=>$fv['droplist']));
							if(isset($fv['combobox_blank'])&&$fv['combobox_blank']==1)
								$listdata[$fk] = array_merge(array(''=>' '),$listdata[$fk]);
						}
					}
				}
			}
			$this->set('option_select', $listdata);
			return $listdata;
		}else return false;
	}


	//update sub doc cua module
	public function update_all_option($opname='',$arr_value=array(),$nosave=false){
		if (isset($_POST['opname']))
			$opname = $_POST['opname'];
		if (isset($_POST['arr_value'])){
		   $arr_value = json_decode($_POST['arr_value']);
		   $arr_value = (array)$arr_value;
		}

		$new_arr = array();

		if ($this->get_id() != '') {
			$query = $this->opm->select_one(
					array('_id' => new MongoId($this->get_id())), array($opname)
			);
			$arr_tmp = $query;
			if(isset($arr_tmp[$opname]))
				$new_arr = $arr_tmp[$opname];
			else
				$new_arr = array();

			if($opname=='products')
				$this->set_cal_price();

			if(isset($new_arr) && is_array($new_arr) && count($new_arr) > 0) {
				foreach ($new_arr as $kk => $vv) {
					if(!$vv['deleted'] && $opname=='products'){
						if(isset($arr_value['taxper']) && $arr_value['taxper']!='' && (!isset($vv['gst_tax']) || $vv['gst_tax']==''))
							$vv['taxper'] = (float)$arr_value['taxper'];
						else
							$vv['taxper'] = 0;
						$vv['tax'] = round(((float)(isset($vv['taxper']) ? $vv['taxper'] : 0)/100)*(float)$vv['sub_total'],3);
						$vv['amount'] = round((float)$vv['sub_total']+(float)$vv['tax'],2);
						$new_arr[$kk] = $vv;

					}else if(!$vv['deleted']){
						foreach($arr_value as $keys=>$vls){
							$new_arr[$kk][$keys] = $vls;
						}

					}
				}
			}
			$arr_insert[$opname] = $new_arr;
			//update sum
			$arr_sum = $this->new_cal_sum($new_arr);
			$arr_insert = array_merge($arr_insert,$arr_sum);
			$arr_insert['_id'] = new MongoId($this->get_id());
			if($nosave)
				return $new_arr;
			if ($this->opm->save($arr_insert)) {
				if (isset($_POST['opname']))
					echo json_encode($new_arr);
				else
					return true;
			}
		}
		if (isset($_POST['opname']))
			die;
		else
			return false;
	}




	/**
	* Thay đổi tax entry khi province thay đổi
	*/
	public function change_tax_entry($not_js=false){
		if ($this->get_id() != '') {
			$this->selectModel('Tax');
			$arr_tax = $this->Tax->tax_select_list();
			$query = $this->opm->select_one(
					array('_id' => new MongoId($this->get_id())), array('invoice_address','shipping_address','tax')
			);

			$shipping = $invoice = array(); $key_tax = '';
			if(isset($query['shipping_address'][0]))
				$shipping = $query['shipping_address'][0];
			if(isset($query['invoice_address'][0]))
				$invoice = $query['invoice_address'][0];
			if(isset($shipping['shipping_province_state_id']) && $shipping['shipping_province_state_id']!='')
				$key_tax = $shipping['shipping_province_state_id'];
			else if(isset($invoice['invoice_province_state_id']) && $invoice['invoice_province_state_id']!='')
				$key_tax = $invoice['invoice_province_state_id'];
			if(isset($arr_tax[$key_tax])){
				$prov_key = explode("%",$arr_tax[$key_tax]);
				$this->update_all_option('products',array('taxper'=>$prov_key[0]));
				$arr_insert['tax'] = $key_tax;
				$arr_insert['taxval'] = $prov_key[0];
				$arr_insert['_id'] = new MongoId($this->get_id());
				if($this->opm->save($arr_insert)){
					$html_ret['keytax'] = $key_tax;
					$html_ret['texttax'] = $arr_tax[$key_tax];

					if(!$not_js)
						echo json_encode($html_ret);
					else
						return true;
				}
			}
		}
		die;
	}


	//Lấy thông tin các company của hệ thống (anvy)
	public function company_system(){
		$this->selectModel('Company');
		$arr_tmp = $this->Company->select_one(array('system' => true));
		if(count($arr_tmp)>0){
			$arr_tmp = iterator_to_array($arr_tmp);
			return $this->set_mongo_array($arr_tmp);
		}else
			return array();
	}


	/*
	* Tính total các line entry
	* Use: Dùng cho chế độ hàm tính và ajax. Trước hết định nghĩa lại các tên field từ ajax.
	* Ex: Xem cách truyền ajax từ file line_entry.ctp của Quotation
	* Construct: $keyfield = array(
		"sub_total"		: "sub_total",
		"tax"			: "tax",
		"amount"		: "amount",
		"sum_sub_total"	: "sum_sub_total",
		"sum_tax"		: "sum_tax",
		"sum_amount"	: "sum_amount"
	)
	*/
	public function update_sum($subdoc='',$keyfield=array()){
		$arr_return = array();
		//get data
		if(isset($_POST['keyfield'])){
			$keyfield = $_POST['keyfield'];
			$keyfield = json_decode($keyfield);
			$keyfield = (array)$keyfield;
		}
		if(isset($_POST['subdoc']))
			$subdoc = $_POST['subdoc'];

		$sub_total		=	$keyfield['sub_total'];
		$tax			=	$keyfield['tax'];
		$amount			=	$keyfield['amount'];
		$key_sub_total	=	$keyfield['sum_sub_total'];
		$key_tax		=	$keyfield['sum_tax'];
		$key_amount		=	$keyfield['sum_amount'];
		$count_sub_total = $count_tax = $count_amount = 0;
		//process
		$ids = $this->get_id();
		if($ids!=''){
			$qr = $this->opm->select_one(array('_id'=> new MongoId($ids)));
			$query = $qr;
			if(isset($query[$subdoc]) && is_array($query[$subdoc]) && count($query[$subdoc])>0){
				$prolist = (array)$query[$subdoc];
				foreach($prolist as $opid=>$arr_item){
					if(isset($arr_item['deleted'])&&!$arr_item['deleted']){
						if(isset($arr_item['option_for'])&&is_array($prolist[$arr_item['option_for']])
							&& (isset($prolist[$arr_item['option_for']]['sell_by'])&&$prolist[$arr_item['option_for']]['sell_by']=='combination'
								|| (isset($arr_item['same_parent'])&&$arr_item['same_parent']>0)
								)
							)
							continue;
						//cộng dồn sub_total
						if(isset($arr_item[$sub_total]))
							$count_sub_total += (float)$arr_item[$sub_total];
						//cộng dồn amount
						if(isset($arr_item[$sub_total]))
							$count_amount += (float)$arr_item[$amount];
					}
				}
				//tính lại sum tax
				$count_tax = $count_amount - $count_sub_total;

				//save sum
				$arr_insert[$key_amount] 	= $count_amount;
				$arr_insert[$key_sub_total] = $count_sub_total;
				$arr_insert[$key_tax] 		= $count_tax;
				$arr_insert['_id'] = new MongoId($ids);
				if($this->opm->save($arr_insert)){
					$arr_insert['_id'] = $ids;
					$arr_return = $arr_insert;
				}

			}
		}

		//output
		if(isset($_POST['keyfield'])){
			echo json_encode($arr_return);
			die;
		}else
			return $arr_return;
	}




	/**
	* Lấy data sub doccumment của 1 item
	* Use:
	* Ex: ví dụ tìm sub doccumment <product> của quotation
	*/
	public function get_option_data($opname='',$module=''){
		$ids = $this->get_id();
		if($ids!='' && $opname!=''){
			if($module!=''){
				$this->selectModel($module);
				$qr = $this->$module->select_one(array('_id'=> new MongoId($ids)));
			}else
				$qr = $this->opm->select_one(array('_id'=> new MongoId($ids)));
			if(isset($qr[$opname]) && is_array($qr[$opname]) && count($qr[$opname])>0)
				return $qr[$opname];
		}
		return array();
	}



	/**
	* Lưu data vào module khi đứng ở module khác
	* Use:
	* Ex: ví dụ tìm sub doccumment <product> của quotation
	*/
	public function save_to_other_module($arr=array(),$keylink=''){
		$arr_insert = array();
		if(isset($_POST['arr']))
			$arr = json_decode($_POST['arr']);
		$arr_insert = $this->set_mongo_array($arr);
		if($keylink!='')
			$arr_insert[$keylink] = $this->get_id();
		$this->opm->save($arr_insert);
		if(isset($_POST['arr']))
			echo json_encode($this->arr_list_changed($arr_insert));
		else
			return $arr_insert;
		die;
	}




	function documents($module_id) {
		$this->selectModel('DocUse');
		$arr_docuse = $this->DocUse->select_all(array(
			'arr_where' => array(
				'module' => $this->modelName,
				'module_id' => new MongoId($module_id)
			)
		));
		$arr_tmp_id = array();
		foreach ($arr_docuse as $value) {
			$arr_tmp_id[] = $value['doc_id'];
		}

		$arr_doc = array();
		if (!empty($arr_tmp_id)) {
			$this->selectModel('Doc');
			$arr_doc = $this->Doc->select_all(array(
				'arr_where' => array(
					'_id' => array('$in' => $arr_tmp_id)
				)
			));
		}
		$this->set('arr_doc', $arr_doc);
		$this->set('module_id', $module_id);
		$this->set(strtolower($this->modelName).'_id', $module_id);

		// lấy ra category
		$this->selectModel('Setting');
		$this->set('arr_docs_category', $this->Setting->select_option(array('setting_value' => 'docs_category'), array('option')));

		if ($this->request->is('ajax')) {
			echo $this->render('../Elements/documents');die;
		}
	}

	function documents_save($module_id, $doc_id, $module_no = '', $module_detail = '') {
		if(!$this->check_permission($this->name.'_@_documents_tab_@_add')
		   &&!$this->check_permission('docs_@_entry_@_add')){
			echo 'You do not have this permission on this action.';
			die;
		}
		$this->selectModel('DocUse');
		$arr_docuse = $this->DocUse->select_one(array('module' => $this->modelName, 'doc_id' => new MongoId($doc_id), 'module_id' => new MongoId($module_id)));
		if (!isset($arr_docuse['_id'])) {
			$arr_save = array();
			$arr_save['doc_id'] = new MongoId($doc_id);
			// $arr_save['module_controller'] = 'companies';
			$arr_save['module_id'] = new MongoId($module_id);
			$arr_save['controller'] = $this->params->params['controller'];
			$arr_save['module'] = $this->modelName;
			$arr_save['module_no'] = $module_no;
			$arr_save['module_detail'] = $module_detail;
			if ($this->DocUse->save($arr_save)) {
				echo 'ok';
			} else {
				echo 'Error: ' . $this->DocUse->arr_errors_save[1];
			}
		} else {
			echo 'Error: this document is selected before';
		}

		die;
	}

	function documents_delete($module_id, $doc_id, $type= '') {
		$this->selectModel('DocUse');
		$arr_docuse = $this->DocUse->select_one(array('module' => new MongoRegex('/'.$this->modelName.'/i'), 'doc_id' => new MongoId($doc_id), 'module_id' => new MongoId($module_id)));
		if (isset($arr_docuse['_id'])) {
			$arr_save = array();
			$arr_save['_id'] = $arr_docuse['_id'];
			$arr_save['deleted'] = true;
			if ($this->DocUse->save($arr_save)) {
				echo 'ok';
			} else {
				echo 'Error: ' . $this->DocUse->arr_errors_save[1];
			}
		} else {
			echo 'Error: not exist record';
		}
		die;
	}

	// BaoNam:
	function communications($module_id) {

		$arr_communication=array();
		$this->selectModel('Communication');
		$arr_communication = $this->Communication->select_all(array(
			'arr_where' => array(
			   'module' => $this->modelName,
			   'module_id' => new MongoId($module_id)
			),
			'arr_order' => array('_id' => -1)
		));
		$this->set('arr_communication',$arr_communication);

		if( $this->modelName == "" ){ echo "Vui long ghi bien modelName vao moi controller. Vi du: var \$modelName = 'Company';"; die; }
		$this->set('model_name', $this->modelName);
		$this->selectModel('Setting');
		$this->set('com_type', $this->Setting->select_option_vl(array('setting_value'=>'com_type')));
		$this->set('module_id', $module_id);
		if ($this->request->is('ajax') && $this->params->params['pass'][0] == 'communications') {
			echo $this->render('../Elements/communications');die;
		}
	}

	// BaoNam:
	public function add_from_module($module_id='', $comms_type='', $option = array()){
		if( $comms_type == 'Message' ){
			$this->redirect('/communications/entry_message/'.$_GET['contact_id'].'?module=' . $this->modelName . '&module_id=' . $module_id);
		}else{

			$arr_save['comms_type'] = $comms_type;
			$arr_save['module'] = $this->modelName;
			$arr_save['module_id'] = new MongoId($module_id);
			if( $arr_save['module'] == 'Company' ){
				$this->selectModel('Company');
				$tmp = $this->Company->select_one(array('_id' => $arr_save['module_id']), array('_id', 'name', 'contact_default_id'));
				$arr_save['company_name'] = $tmp['name'];
				$arr_save['company_id'] = $tmp['_id'];

				if (isset($tmp['contact_default_id']) && is_object($tmp['contact_default_id'])) {
					$this->selectModel('Contact');
					$contact_default = $this->Contact->select_one(array('_id' => $tmp['contact_default_id']), array('_id', 'first_name', 'last_name'));
					$arr_save['contact_name'] = $contact_default['first_name'].' '.$contact_default['last_name'];
					$arr_save['contact_id'] = $contact_default['_id'];
				}
			}
			$not_redirect = false;
			if(isset($option['not_redirect'])){
				unset($option['not_redirect']);
				$not_redirect = true;
			}
			$this->selectModel('Communication');
			$arr_save = array_merge($arr_save, $option);
			$this->Communication->arr_default_before_save = $arr_save;
			if( $this->Communication->add() ){
				if(!$not_redirect){
					$this->redirect('/communications/entry/'.$this->Communication->mongo_id_after_save);
					die;
				} else
					return $this->Communication->mongo_id_after_save;
			}else{
				echo 'Error: ' . $this->Communication->arr_errors_save[1];die;
			}
		}

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

	function noteactivity_add($module_id) {
		$arr_save = array();
		$arr_save['type'] = 'Note';
		$arr_save['content'] = '';
		$arr_save['module'] = $this->modelName;
		$arr_save['module_id'] = new MongoId($module_id);

		$this->selectModel('Noteactivity');
		if ($this->Noteactivity->save($arr_save)) {
			$this->noteactivity($module_id);
		} else {
			echo 'Error: ' . $this->Noteactivity->arr_errors_save[1];
		}
		die;
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



	//change sellprice: nhận sell price moi va bang chiec khau
	function change_sell_price_company($idcompany='',$product_id=''){
		$result = array();
		//Dò thông tin trong bảng Company
		$sell_category = $this->company_pricing($idcompany,$product_id);


		//Nếu trong Company có Price Break thì lấy thông tin
		if(isset($sell_category['price_break']) && count($sell_category['price_break'])>0)
			$result['company_price_break'] = $sell_category['price_break'];

		//Lấy thông tin Discount cho Company
		if(isset($sell_category['discount']) && $sell_category['discount']!='')
			$result['discount'] = $sell_category['discount'];


		//neu company co sell_category_key
		if(!isset($sell_category['sell_category_key']))
			$sell_category['sell_category_key'] = '';

		$sell_break = $this->product_price_break($sell_category['sell_category_key'],$product_id);
		//lay gia moi
		if(isset($sell_break['sell_price']))
			$result['sell_price'] = $sell_break['sell_price'];
		else
			$result['sell_price'] = 0;

		//gia phu them cua option
		if(isset($sell_break['sell_price_plus']) && $sell_break['sell_price_plus']!='')
			$result['sell_price_plus'] = $sell_break['sell_price_plus'];

		//lay bang chiec khau product
		if(isset($sell_break['price_break']) && count($sell_break['price_break'])>0)
			$result['product_price_break'] = $sell_break['price_break'];
		return $result;

		/**Kết quả cần lấy :
		*  1.Bảng chiếc khấu trong Company 							=> $result['company_price_break']
		*  2.Chiếc khấu áp dụng cho Company 						=> $result['discount']
		*  3.Giá bán cho company dựa theo key category, 			=> $result['sell_price']
		*  4.Bảng chiếc khấu trong product dựa theo key category, 	=> $result['product_price_break']
		*/
	}



	//lay data pricing_category tu Product dua vao Company
	function company_pricing($idcompany='',$product_id=''){
		$price_break = $result = array();

		if($idcompany!=''){
			if(!is_object($idcompany))
				$idcompany = new MongoId($idcompany);

			$this->selectModel('Company');

			$arr_company = $this->Company->select_one(
				array('_id'=>$idcompany),
				array('sell_category','sell_category_id','pricing','discount')
			);

			//neu co bang  pricing
			if(isset($arr_company['pricing']) && is_array($arr_company['pricing']) && count($arr_company['pricing'])>0 && $product_id!=''){
				if(is_object($product_id))
					$product_id = (string)$product_id;

				//lap va tim $price_break cho 1 san pham dang can tim
				foreach($arr_company['pricing'] as $keys => $values){
					if(isset($values['deleted']) && !($values['deleted']) && isset($values['product_id']) && (string)$values['product_id'] == $product_id ){
						if(isset($values['price_break']) && is_array($values['price_break']) && count($values['price_break'])>0 ){
							foreach($values['price_break'] as $kk=>$vv){
								if(isset($vv['deleted']) && !$vv['deleted'])
									$price_break[$kk] = $vv;
							}
						}
					}
				}

				$result['price_break'] = $price_break;
			}
			if( !isset($arr_company['sell_category_id']) || empty($arr_company['sell_category_id']) ) {
				$arr_company['sell_category_id'] = 'Retail';
			}
			if(isset($arr_company['sell_category_id'])) {
				$result['sell_category_key'] = $arr_company['sell_category_id'];
			}
			if(isset($arr_company['discount']))
				$result['discount'] = (float)$arr_company['discount'];

		}

		return $result; //ket qua tra ve la array price_break va sell_category_key

	}




	//lay data price_break tu Product dua vao Company hoac default pricing_category
	function product_price_break($sell_category_key='',$product_id=''){
		$result = array();
		$sell_price_default = '';
		$sell_category_key_df = '';

		if($product_id!=''){
			if(!is_object($product_id) && strlen($product_id)!=24)
				return '';
			else if(!is_object($product_id))
				$product_id = new MongoId($product_id);


			$this->selectModel('Product');
			$arr_product = $this->Product->select_one(
				array('_id'=>$product_id),
				array('pricebreaks','sellprices')
			);
			//tim sell_category id
			if(isset($arr_product['sellprices']) && is_array($arr_product['sellprices']) && count($arr_product['sellprices'])>0){
				$result['sell_price'] = '';
				foreach($arr_product['sellprices'] as $keys=>$values){
					if(isset($values['deleted']) && !$values['deleted'] && isset($values['sell_category'])){
						if($sell_category_key!='' && $values['sell_category'] == $sell_category_key)
							$result['sell_price'] = $values['sell_unit_price'];

						if(isset($values['sell_default']) && (int)$values['sell_default']==1){
							$sell_price_default = $values['sell_unit_price'];
							$sell_category_key_df = $values['sell_category'];
						}
					}
				}

				if($result['sell_price'] == '' && $sell_price_default!='' ){
					$result['sell_price'] = $sell_price_default;
					$sell_category_key = $sell_category_key_df;
				}

			}else if(isset($arr_product['sell_price'])){
				$result['sell_price'] = $arr_product['sell_price'];
			}

			//Cong them option
			if(isset($result['sell_price'])){
				$this->selectModel('Product');
				$result['sell_price_plus'] = $this->Product->sell_price_plus_option((string)$product_id,$result['sell_price']) - $result['sell_price'];
			}

			//tim Price breaks
			if(isset($arr_product['pricebreaks']) && is_array($arr_product['pricebreaks']) && count($arr_product['pricebreaks'])>0){

				foreach($arr_product['pricebreaks'] as $keys=>$values){
					if(isset($values['deleted']) && !$values['deleted'] && isset($values['sell_category']) && $values['sell_category'] == $sell_category_key){
						$result['price_break'][$keys] = $values;
					}
				}
			}


		}

		return $result;
	}



	//nhan gia tri tax dua vao key cua province
	function get_tax($tax_key=''){
		if($tax_key!=''){
			$this->selectModel('Tax');
			$tax_list = $this->Tax->tax_list();
			if(isset($tax_list[$tax_key]))
				return $tax_list[$tax_key];
		}
		return 0;
	}



	function get_setting_option($type){
		$this->selectModel('Setting');
		return $this->Setting->select_option(array('setting_value' => $type), array('option'));
	}
	//gửi email dựa vào template định sẵn
	function get_email_template($email){
		if(!isset($email['template_name']) || $email['template_name'] =='')
			$email['template_name'] = 'default';
		$this->selectModel("Emailtemplate");
		if(isset($email['template_id']))
			$arr_where = array('_id'=>new MongoId($email['template_id']));
		else
			$arr_where = array('name'=>new MongoRegex('/'.$email['template_name'].'/i'));
		$email_template = $this->Emailtemplate->select_one($arr_where);
		$template = $email_template['template'];
		//Lay tat ca span
		preg_match_all("!<span[^>]+>(.*?)</span>!", $template, $matches);
		$all_span = $matches[0];
		foreach($all_span as $span){
			//Lay noi dung trong rel
			preg_match_all("/<span [^>]+ rel=\"{{(.+?)}}\" [^>]+>[^>]+<\/span>/",$span,$content_matches);
			foreach($content_matches[1] as $val){
				$val = strtolower($val);
				if(strpos($val, '_date')!==false && isset($email[$val]))
				   $email[$val] = date('M d, Y',$email[$val]->sec);
				else if(strpos($val, '_address')!==false){
					$name = str_replace('_address', '', $val);
					$tmp_address = $email[$val][0];
					$email[$val] = '';
					if (isset($tmp_address[$name . '_address_1']) && $tmp_address[$name . '_address_1'] != '')
					$email[$val] .= $tmp_address[$name . '_address_1'] . ' ';
					if (isset($tmp_address[$name . '_address_2']) && $tmp_address[$name . '_address_2'] != '')
						$email[$val] .= $tmp_address[$name . '_address_2'] . ' ';
					if (isset($tmp_address[$name . '_address_3']) && $tmp_address[$name . '_address_3'] != '')
						$email[$val] .= $tmp_address[$name . '_address_3'] . '<br />';
					else
						$email[$val] .= '<br />';
					if (isset($tmp_address[$name . '_town_city']) && $tmp_address[$name . '_town_city'] != '')
						$email[$val] .= $tmp_address[$name . '_town_city'];
					if (isset($tmp_address[$name . '_province_state']))
						$email[$val] .= ' ' . $tmp_address[$name . '_province_state'] . ' ';
					else if (isset($tmp_address[$name . '_province_state_id']) && isset($tmp_address[$name . '_country_id'])) {
						$keytmp_address = $tmp_address[$name . '_province_state_id'];
						$provkey = $this->province($tmp_address[$name . '_country_id']);
						if (isset($provkey[$tmp_address]))
							$email[$val] .= ' ' . $provkey[$tmp_address] . ' ';
					}
					if (isset($tmp_address[$name . '_zip_postcode']) && $tmp_address[$name . '_zip_postcode'] != '')
						$email[$val] .= $tmp_address[$name . '_zip_postcode'];
					if (isset($tmp_address[$name . '_country']) && isset($tmp_address[$name . '_country_id']) && (int) $tmp_address[$name . '_country_id'] != "CA")
						$email[$val] .= ' ' . $tmp_address[$name . '_country'] . '<br />';
					else
						$email[$val] .= '<br />';

				}
				$template = str_replace($span, (isset($email[$val]) ? $email[$val] : ''), $template);
			}
		}
		return $template;
	}
	//Gửi email tự động
	function auto_send_email($data)
	{
		if(!empty($data)){
			App::uses('CakeEmail', 'Network/Email');
			$email = new CakeEmail();
			$this->selectModel('Stuffs');
			$system_email = $this->Stuffs->select_one(array('value'=>"system_email"));
			if(!empty($system_email)){
				$config_set = array(
					'from'      => array($system_email['username']=>(trim($system_email['email_name'])!='' ? $system_email['email_name'] : 'BanhMiSub - JobTraq')) ,
					'username'  => $system_email['username'],
					'password'  => $system_email['password'],
					'host'		=> $system_email['host'],
					'port'		=> $system_email['port'],
				);
				$email->config('smtp',$config_set);
			}
			else{
				$email->config('gmail',array('jobtraq.mail@gmail.com'=>$this->opm->user_name()));
				$system_email['username'] = 'jobtraq.mail@gmail.com';
			}
			$email->to($data['to']);
			if(isset($data['cc'])&&!empty($data['cc']))
				$email->cc($data['cc']);
			if(isset($data['bcc'])&&!empty($data['bcc']))
				$email->bcc($data['bcc']);
			$email->subject($data['subject']);
			//Kiem tra attachment, va dua vao mail se gui
			$this->email_image_filter($data['template'], $data['attachments']);
			if(!empty($data['attachments']))
				$email->attachments($data['attachments']);
			$email->emailFormat('both');
			try{
				$email->send($data['template']);
				if(!isset($data['no_save_comms'])){
					//Save vao comms
					$option = array(
									'not_redirect'	=>	true,
									'name'			=>	$data['subject'],
									'content'		=>	$data['template'],
									'comms_status'	=> 	'Sent',
									'contact_name'	=>	$data['contact_name'],
									'contact_id'	=>	$data['contact_id'],
									'email'			=>	$data['to'],
									'contact_from'	=> 	$this->opm->user_name(),
									'identity'		=> 	'Auto Send',
									'sign_off'		=> 	'',
									);
					if(isset($data['module_id']))
						$module_id = $data['module_id'];
					else
						$module_id = $this->get_id();
					$this->add_from_module($module_id,'Email',$option);
					$comms_id = $this->Communication->mongo_id_after_save;
					$this->Session->setFlash('<span>An email has just been sent to '.$data['to'].' by '.$system_email['username'].'.</span><a id="notifyTopsub" style="right: 35px;position: absolute;" href="'.URL.'/communications/entry/'.$comms_id.'">View</a>','default',array('class'=>'flash_message'));
					return $comms_id;
				} return true;
			} catch(SocketException  $e){
	            $message = $e->getMessage();
	            if( strpos($message, 'authentication failed') !== false ) {
	                $message .= '<br />Wrong username or password.';
	            }
	            echo $message;
	            die;
	        } catch(Exception $e){
				echo $e;
				die;
			}
		}
	}

	function auto_action($status){
		$id = $this->get_id();
		$controller_name = $this->name;
		$model_name = $this->modelName;
		//Lấy thông tin record dựa vào id
		$data = $this->opm->select_one(array('_id'=>new MongoId($id)));
		$this->selectModel('AutoProcess');
		//Lấy id của template
		$process= $this->AutoProcess->select_all(array('arr_where'=>array('controller'=> new MongoRegex('/'.$controller_name.'/i'))));
		if($process->count()){
			$content = '';//'<p>The <a href="'.URL.'/'.strtolower($controller_name).'/entry/'.$data['_id'].'">'.$model_name.' code: '.$data['code'].'</a> has been changed status to '.$status.'. Please check.</p>';
			$subject = 'Jobtraq - '.$model_name.' code : '.$data['code'];
			$this->selectModel('Contact');
			foreach($process as $value){
				switch ($value['name']) {
					case 'Email CSR, Rep (Completed)':
						//GỬI MAIL CHO OUR REP VÀ OUR CSR KHI STATUS VỀ COMPLETE
						if($status!='Completed') continue 2;
						if(!is_object($data['our_rep_id']) || !is_object($data['our_csr_id'])){
							echo 'This record must have our rep and csr to send email.';
							die;
						}
						//Gửi OUR REP
						$our_rep = $this->Contact->select_one(array('_id'=>new MongoId($data['our_rep_id'])));
						if(!isset($our_rep['email']) || !filter_var($our_rep['email'], FILTER_VALIDATE_EMAIL)){
							echo 'The email of our responsible is not valid.';
							die;
						}
						$email = array(
								   'contact_name' 	=> 	$our_rep['full_name'],
								   'contact_id'		=>	new MongoId($our_rep['_id']),
								   'template_id'	=>	$value['email_template_id'],
								   'content'		=> 	$content,
								   );
						$email = array_merge($data,$email);
						if(isset($value['extra_email'])&&filter_var($value['extra_email'], FILTER_VALIDATE_EMAIL)){
							$email['cc'] = $value['extra_email'];
						}
						$email['to'] = $our_rep['email'];
						$email['template'] = $this->get_email_template($email);
						$email['subject'] = $subject;
						//Gửi mail cho our_rep
						$this->auto_send_email($email);
						//==================================================
						//Gửi OUR CSR
						$our_csr = $this->Contact->select_one(array('_id'=>new MongoId($data['our_csr_id'])));
						if(!isset($our_csr['email']) || !filter_var($our_csr['email'], FILTER_VALIDATE_EMAIL)){
							echo 'The email of our crs is not valid.';
							die;
						}
						if($data['our_csr_id']==$data['our_rep_id']) continue 2;
						$email = array(
								   'contact_name' 	=> 	$our_csr['full_name'],
								   'contact_id'		=>	new MongoId($our_csr['_id']),
								   'template_id'	=>	$value['email_template_id'],
								   'content'		=> 	$content,
								   );
						$email = array_merge($data,$email);
						$email['to'] = $our_csr['email'];
						$email['template'] = $this->get_email_template($email);
						$email['subject'] = $subject;
						//Gửi mail cho our_csr
						$this->auto_send_email($email);
						//=======================End=======================
						break;
					case 'Email Customer (Completed)':
						//GỬI MAIL CHO CUSTOMER KHI STATUS VỀ COMPLETE
						if($status!='Completed') continue 2;
						if(!is_object($data['company_id'])){
						echo 'This record must have customer to send email.';
							die;
						}
						$send_email = $this->get_name('Company',$data['company_id'],'email_so_completed');
						if($send_email!=1) continue 2;
						$customer = $this->Contact->select_one(array('_id'=>new MongoId($data['contact_id'])));
						if(!isset($customer['email']) || !filter_var($customer['email'], FILTER_VALIDATE_EMAIL)){
							echo 'The email of customer is not valid.';
							die;
						}
						$email = array(
										'contact_name' 	=> 	$customer['full_name'],
										'contact_id'	=>	new MongoId($customer['_id']),
										'template_id'	=>	$value['email_template_id'],
										'content'		=> 	$content,
									   );
						$email = array_merge($data,$email);
						if(isset($value['extra_email'])&&filter_var($value['extra_email'], FILTER_VALIDATE_EMAIL)){
							$email['cc'] = $value['extra_email'];
						}
						$email['to'] = $customer['email'];
						$email['template'] = $this->get_email_template($email);
						$email['subject'] = $subject;
						//Gửi mail cho our_rep
						$this->auto_send_email($email);
						//=======================End=======================
						break;
					case 'Email CSR (Submitted)':
						//GỬI MAIL CHO OUR CSR KHI STATUS VỀ SUBMIT
						if(strtolower($controller_name)!='quotations') continue 2;
						if($status!='Submitted') continue 2;
						if(!$this->check_limit()) continue 2;
						if(!isset($data['our_csr_id']) || !is_object($data['our_csr_id'])){
							echo 'This record must have our csr to send email.';
							die;
						}
						$our_csr = $this->Contact->select_one(array('_id'=>new MongoId($data['our_csr_id'])));
						if(!isset($our_csr['email']) || !filter_var($our_csr['email'], FILTER_VALIDATE_EMAIL)){
							echo 'The email of our crs is not valid.';
							die;
						}
						$email = array(
										'contact_name' 	=> 	$our_csr['full_name'],
										'contact_id'	=>	new MongoId($our_csr['_id']),
										'template_id'	=>	$value['email_template_id'],
										'content'		=> 	$content,
									   );
						$email = array_merge($data,$email);
						if(isset($value['extra_email'])&&filter_var($value['extra_email'], FILTER_VALIDATE_EMAIL)){
							$email['cc'] = $value['extra_email'];
						}
						$email['to'] = $our_csr['email'];
						$email['template'] = $this->get_email_template($email);
						$email['subject'] = $subject;
						//Gửi mail cho our_csr
						$this->auto_send_email($email);
						//=======================End=======================
						break;
					case 'Email Rep (Approved)':
						//GỬI MAIL CHO OUR REP KHI STATUS VỀ APPROVED
						if($status!='Approved') continue 2;
						$our_rep = $this->Contact->select_one(array('_id'=>new MongoId($data['our_rep_id'])));
						if(!isset($our_rep['email']) || !filter_var($our_rep['email'], FILTER_VALIDATE_EMAIL)){
							echo 'The email of our crs is not valid.';
							die;
						}
						$email = array(
									   'contact_name' 	=> 	$our_rep['full_name'],
									   'contact_id' 	=> 	new MongoId($our_rep['_id']),
									   'template_id'	=>	$value['email_template_id'],
									   'content'		=> 	$content,
									   );
						$email = array_merge($data,$email);
						if(isset($value['extra_email'])&&filter_var($value['extra_email'], FILTER_VALIDATE_EMAIL)){
							$email['cc'] = $value['extra_email'];
						}
						$email['to'] = $our_rep['email'];
						$email['template'] = $this->get_email_template($email);
						$email['subject'] = $subject;
						//Gửi mail cho our_rep
						$this->auto_send_email($email);
						//=======================End=======================
						break;
					default:
						continue 2;
						break;
				}
			}
		}
	}
	public function create_email_pdf($ajax=false,$type = ''){
		//Lay len ten file cua report vua dc tao ra
		$file = $this->view_pdf(true,$type);
		if($file!=''){
			$cc_email = '';
			$model_name = $this->modelName;
			$this->selectModel($model_name);
			//Lay du lieu record cua controller hien tai, de dua vao comms
			$data = $this->$model_name->select_one(array('_id'=> new MongoId($this->get_id())),array('no','code','company_id','company_name','contact_id','contact_name','position','email','phone','fax','job_number','job_name','job_id'));
			if($model_name == 'Salesinvoice'){
				if(isset($data['company_id']) && is_object($data['company_id'])){
						$this->selectModel('Company');
					if(IS_LOCAL){
						$company = $this->Company->select_one(array('_id' => $data['company_id'],'postal_mail_only' => 1),array('_id'));
						if(isset($company['_id'])){
							$this->Session->setFlash('<span>This company has requested to be send postal mail only.</span>','default',array('class'=>'flash_message'));
							$this->redirect(URL.'/salesinvoices/entry');
							die;
						}
					} else {
						$company = $this->Company->select_one(array('_id' => $data['company_id']),array('contact_default_id'));
						$this->selectModel('Contact');
						$contact_default = array();
						if(isset($company['contact_default_id']) && is_object($company['contact_default_id'])) {
							$contact_default = $this->Contact->select_one(array('_id' => $company['contact_default_id']), array('first_name','last_name','email'));
						}
						$account_payable = $this->Contact->select_one(array('company_id' => $data['company_id'],'position' => 'Account Payable'), array('first_name','last_name','email'));
						if(isset($account_payable['email'])){
							$data['contact_id'] = $account_payable['_id'];
							$data['contact_name'] = (isset($account_payable['first_name']) ? $account_payable['first_name'].' ' : '').(isset($account_payable['last_name']) ? $account_payable['last_name'] : '');
							$data['email'] = $account_payable['email'];
							if(isset($contact_default['email'])){
								$cc_email = $contact_default['email'];
							}

						} else if(isset($contact_default['email'])){
							$data['contact_id'] = $contact_default['_id'];
							$data['contact_name'] = (isset($contact_default['first_name']) ? $contact_default['first_name'].' ' : '').(isset($contact_default['last_name']) ? $contact_default['last_name'] : '');
							$data['email'] = $contact_default['email'];
						}
						$cc_email = '';
					}
				}
			}
			$arr_save = array();
			//Add Doc truoc
			$this->selectModel('Doc');
			$arr_save = array(
				'deleted' 			=>	false,
				'no'				=>	$this->Doc->get_auto_code('no'),
				'create_by_module'	=>	$model_name,
				'path'				=>	DS.'upload'.DS.$file,
				'name'				=>	$file,
				'ext'				=> 'pdf',
				'location'			=>	$model_name,
				'type'				=> 	'application/pdf',
				'description'		=>	'Created at: '.date("h:m a, M d, Y"),
				'create_by'			=>	new MongoId($this->opm->user_id()),
				);
			$this->Doc->save($arr_save);
			$doc_id = $this->Doc->mongo_id_after_save;
			//Tiep theo add Comms
			$this->selectModel('Communication');
			$arr_save = array(
				'deleted'			=> false,
				'code'				=> $this->Communication->get_auto_code('code'),
				'comms_type'		=> 'Email',
				'comms_date'		=> new MongoDate(),
				'comms_status'		=> 'Draft',
				'sign_off'			=> 0,
				'include_signature'	=> 0,
				'email_cc'			=> $cc_email,
				'email_bcc'			=> 'jobtraq@anvydigital.com',
				'identity'			=> '',
				'internal_notes'	=> '',
				'company_id'		=> (isset($data['company_id'])&&$data['company_id']!='' ? new MongoId($data['company_id']) : ''),
				'company_name'		=> (isset($data['company_name']) ? $data['company_name'] : ''),
				'module'			=> $model_name,
				'module_id'			=> new MongoId($data['_id']),
				'content'			=> '',
				'contact_id'		=> (isset($data['contact_id'])&&$data['contact_id']!='' ? new MongoId($data['contact_id']) : ''),
				'contact_name'		=> (isset($data['contact_name']) ?$data['contact_name'] : ''),
				'last_name'			=> '',
				'position'			=> (isset($data['position'])?$data['position']:''),
				'salutation'		=> '',
				'name'				=> '',
				'toother'			=> '',
				'email'				=> (isset($data['email']) ? $data['email'] : ''),
				'phone'				=> (isset($data['phone']) ? $data['phone'] : ''),
				'fax'				=> (isset($data['fax']) ? $data['fax'] : ''),
				'job_number'		=> (isset($data['job_number']) ? $data['job_number'] : ''),
				'job_name'			=> (isset($data['job_name']) ? $data['job_name'] : ''),
				'job_id'			=> (isset($data['job_id'])&&$data['job_id']!='' ? new MongoId($data['job_id']) : ''),
				);
			$model_name = str_replace('Sales', '', $model_name);
			$model_name = ucfirst($model_name);
			if($model_name == 'Purchaseorder')
				$model_name = 'Purchase order';
			$arr_save['name'] = "System Admin - JobTraq - {$model_name} #{$data['code']}";
			$arr_save['content'] = '';
			$this->Communication->save($arr_save);
			$comms_code = $arr_save['code'];
			$comms_id = $this->Communication->mongo_id_after_save;
			//Add docuse, gan lien ket giua comms va doc, muc dich de email tu dong get dc attachment
			$this->selectModel('DocUse');
			$arr_save = array(
				'deleted'		=> false,
				'controller'	=> 'communications',
				'module'		=> 'Communication',
				'module_no'		=> 	$comms_code,
				'doc_id'		=> new MongoId($doc_id),
				'module_id'		=> new MongoId($comms_id),
				'created_by'	=> new MongoId($this->opm->user_id()),
				);
			$this->DocUse->save($arr_save);
			if($ajax){
				echo URL.'/communications/entry/'.$comms_id;
				die;
			}
			$this->redirect('/communications/entry/'.$comms_id);
		}
	}
	function get_all_permission()
	{
		$arr_permission = Cache::read('arr_permission_'.(string)$_SESSION['arr_user']['contact_id']);
		if(!$arr_permission) {
			$arr_permission = array();
			if( CHECK_DB_PRIVILEGE ){
				$this->selectModel('Contact');
				$current_contact = $this->Contact->select_one(array('_id' => $_SESSION['arr_user']['contact_id']));
			}else
				$current_contact = $_SESSION['arr_user'];
			if( !isset($current_contact['roles']) )
				$current_contact['roles'] = array();
			if( !isset($current_contact['roles']['roles']) )
				$current_contact['roles']['roles'] = array();

			if(isset($current_contact['roles']) && !empty($current_contact['roles'])){
				$roles= $current_contact['roles']['roles'];
				unset($current_contact['roles']['roles']);
				$arr_permission = $current_contact['roles'];
				if(!empty($roles)){
					$roles = array_values($roles);
					$this->selectModel('Role');
					$group_roles = $this->Role->select_all(array(
											'arr_where'	=>	array('_id'	=>	array('$in'	=> $roles)),
												  ));
					if($group_roles->count()>0){
						foreach($group_roles as $value){
							// BaoNam: kiểm tra đây có phải là System Admin không
							if($value['name'] == 'System Admin'){
								$this->set('system_admin', true);
							}
							//==================================================
							if(isset($value['value'])&&!empty($value['value'])){
								foreach($value['value'] as $key=>$val){
									$arr_permission[$key] = $val;
								}
							}
						}
					}
				}
			}
			Cache::write('arr_permission_'.(string)$_SESSION['arr_user']['contact_id'],$arr_permission);
		}
		return $arr_permission;
	}
	function get_all_inactive_permission()
	{
		$arr_inactive_permission = Cache::read('arr_inactive_permission');
		if(!$arr_inactive_permission){
			$arr_inactive_permission = array();
			$this->selectModel('Permission');
			$permissions = $this->Permission->select_all(array('arr_where'=>array('inactive_permission'=>array('$exists'=>true)),'arr_field'=>array('inactive_permission')));
			if($permissions->count()==0)
				return array();
			else {
				foreach($permissions as $permission){
					foreach($permission['inactive_permission'] as $key=>$value)
						$arr_inactive_permission[$key] = $value;
				}
			}
			Cache::write('arr_inactive_permission',$arr_inactive_permission);
		}
		return $arr_inactive_permission;
	}
	function check_permission($permission,$options=false){
		$permission = strtolower($permission);
		if(!CHECK_PERMISSION || $this->system_admin)
			return true;
		if(isset($this->arr_inactive_permission[$permission]))
			return false;
		if(isset($this->arr_permission['all']))
			return true;
		if($options){
			foreach($this->arr_permission as $key=>$value)
				if(strpos($key, $permission)!==false)
					return true;
		} else {
			if(isset($this->arr_permission[$permission]))
				return true;
		}
		return false;
	}
	function check_permission_array(array $permission,$type="and",$options=false){
		$i = 0;
		if($options){
			$controller = strtolower($this->name);
			foreach($permission as $key=>$value)
				$permission[$key] = $controller.'_@_options_@_'.$value;
		} else {
			foreach($permission as $key=>$value)
				$permission[$key] = strtolower($value);
		}
		if(!CHECK_PERMISSION || $this->system_admin)
			return true;
		if($type=="and"){
			//AND:
			//Nếu 1 giá trị trong mảng permission tồn tại trong inactive_permission, return false
			foreach($permission as $value){
				if(isset($this->arr_inactive_permission[$value]))
					return false;
			}
		} else if($type=="or"){
			//OR:
			//unset nhứng giá trị trong mảng permission tồn tại trong inactive_permission
			foreach($permission as $key=>$value){
				if(isset($this->arr_inactive_permission[$value]))
					unset($permission[$key]);
			}
			//Nếu đã unset tất cả, return false
			if(empty($permission))
				return false;
		}
		if(isset($this->arr_permission['all']))
			return true;
		if($type=="and"){
			//AND sai 1 => sai het
			foreach($permission as $value){
				if(!isset($this->arr_permission[$value]))
					return false;
			}
			return true;
		}
		else if($type=="or"){
			//OR dung 1 => dung het
			foreach($permission as $value){
				if(isset($this->arr_permission[$value]))
					return true;
			}
		}
		return false;
	}



	function error_auth(){
		if(!isset($this->params->params['action']) || !in_array($this->params->params['action'], array('popup','costings_data','outstanding','send_accountant') ) ) {
			if($this->request->is('ajax')) {
				echo 'You have no right in this item, please come back. Thank you!';
				die;
			}
			$this->redirect('/');
        }
	}




	function cal_production_time($arr_data=array()){
		//pr($arr_data);
		//$arr_data['sell_by'] = strtolower($arr_data['sell_by']); // BaoNam fix bug viet hoa chu Unit
		if($arr_data['sell_by']!=''){
			//tinh tong do may
			$speed_asset = 1; $production_time = 0;
			$this->selectModel('Equipment');
			if(isset($arr_data['tag_key'])){
				$speed_asset = $this->Equipment->speed_asset_old($arr_data['tag_key']);
			}
			if($speed_asset==0)
				$speed_asset = 1;
			//echo $speed_asset;

			$cal_price = new cal_price;
			$cal_price->arr_product_items['sizew'] = (float)$arr_data['sizew'];
			$cal_price->arr_product_items['sizeh'] = (float)$arr_data['sizeh'];
			if(isset($arr_data['sizew_unit']))
				$cal_price->arr_product_items['sizew_unit'] = $arr_data['sizew_unit'];
			if(isset($arr_data['sizeh_unit']))
				$cal_price->arr_product_items['sizeh_unit'] = $arr_data['sizeh_unit'];
			//neu la dien tich
			if($arr_data['sell_by']=='area'){
				$cal_price->cal_area();
				$production_time = $cal_price->arr_product_items['area']*$arr_data['quantity']*$arr_data['factor']/$speed_asset;
				//echo '<br />'.$arr_data['factor'];
			//neu la chieu dai
			}else if($arr_data['sell_by']=='lengths'){
				$cal_price->cal_perimeter();
				$production_time = $cal_price->arr_product_items['perimeter']*$arr_data['quantity']*$arr_data['factor']/$speed_asset;

			//neu la unit
			}else if($arr_data['sell_by']=='unit'){

				$production_time = (float)$arr_data['min_of_uom']*$arr_data['quantity']/60;
			}
			if($production_time<0.5&&$production_time>0)
				$production_time = 0.5;
			else {
				$production_time = ceil($production_time*2)/2;
			}
			return $production_time;

		}
	}


	function rebuild_line_text_entry(){
		$model_name = $this->modelName;
		$this->selectModel($model_name);
		$line_action = array();
		$text_action = array();
		//LINE_ENTRY
		if(!$this->check_permission(strtolower($this->name).'_@_entry_@_edit')){ // Baonam fix: strtolower
			$line_action['edit'] = 1;
			$line_action['delete'] = 1;
			$line_action['add'] = 1;
			$text_action['edit'] = 1;
			$text_action['delete'] = 1;
			$text_action['add'] = 1;
		}
		if(!empty($line_action))
			$this->$model_name->set_lock_option('line_entry','products',$line_action);
		if(!empty($text_action))
			$this->$model_name->set_lock_option('text_entry','products',$text_action);
	}

    public function other() {
    	$id = $this->get_id();
        $data = $this->opm->select_one(array('_id' => new MongoId($id)),array('sum_sub_total','commission','other_comment','record_type','fax','name','include_signature','sign_off_section','own_letterhead','include_images','invoice_status','status','quotation_status', 'shipping_cost'));
        if(!isset($data['commission']['sales_amount'])){
	        $minimum = $this->get_minimum_order();
	        if($data['sum_sub_total']<$minimum)
	        	$data['sum_sub_total'] = $minimum;
        }
        $arr_save['_id'] = $data['_id'];
        if(!isset($data['commission']))
        	$data['commission'] = array();
        $arr_save['commission'] = $data['commission'];
        $save = false;
        if(isset($data['quotation_status']) && $data['quotation_status'] != 'Approved'
			   	||isset($data['status']) && $data['status'] == 'Cancelled'
			   	||isset($data['invoice_status']) && $data['invoice_status'] == 'Cancelled'){
        	$data['commission']['commission_amount'] = $arr_save['commission']['commission_amount'] = 0;
        	$save = true;
		} else {
			$save = true;
			$sales_amount = isset($data['commission']['sales_amount']) ? $data['commission']['sales_amount'] : (float)$data['sum_sub_total'];
			$data['commission']['profit'] = $arr_save['commission']['profit'] = $profit = isset($data['commission']['profit']) ? $data['commission']['profit'] : 0;
			$data['commission']['rate'] = $arr_save['commission']['rate'] = $rate = isset($data['commission']['rate']) ? $data['commission']['rate'] : 0;
			if(isset($data['commission']['base_on']) && $data['commission']['base_on'] == 'sale_amt')
				$arr_save['commission']['commission_amount'] = $sales_amount*$rate/100;
			else
				$arr_save['commission']['commission_amount'] = $profit*$rate/100;
			$data['commission']['commission_amount'] = $arr_save['commission']['commission_amount'];
		}
		if($save)
        	$this->opm->save($arr_save);
		$this->set('data', $data);
		//Gọi view ctp communications dùng chung
		$this->communications($id, true);
	}
	function commission_auto_save(){
		if(isset($_POST)){
			$query = $this->opm->select_one(array('_id' => new MongoId($this->get_id())),array('commission','invoice_status','status','quotation_status'));
			$_POST['sales_amount'] = isset($_POST['sales_amount']) ? (float)str_replace(',', '', $_POST['sales_amount']) : 0;
			$_POST['sales_cost'] = isset($_POST['sales_cost']) ? (float)str_replace(',', '', $_POST['sales_cost']) : 0;
			$_POST['rate'] = isset($_POST['rate']) ? (float)str_replace('%', '', $_POST['rate']) : 0;
			$_POST['paid'] = isset($_POST['paid']) && $_POST['paid'] == 1 ? 1 : 0;

			$arr_save['commission'] = $_POST;
			$arr_save['commission']['profit'] = $_POST['sales_amount'] - $_POST['sales_cost'];
			if(isset($query['quotation_status']) && $query['quotation_status'] == 'Approved'
			   	||isset($query['status']) && $query['status'] != 'Cancelled'
			   	||isset($query['invoice_status']) && $query['invoice_status'] != 'Cancelled'){
				if(isset($_POST['base_on']) && $_POST['base_on'] == 'sale_amt')
					$arr_save['commission']['commission_amount'] = $arr_save['commission']['sales_amount']*$_POST['rate']/100;
				else
					$arr_save['commission']['commission_amount'] = $arr_save['commission']['profit']*$_POST['rate']/100;
			}
			else
				$arr_save['commission']['commission_amount'] = 0;
			unset($arr_save['commission']['sales_amount']);
			if($_POST['fieldchange'] == 'sales_amount' || isset($query['commission']['sales_amount']))
				$arr_save['commission']['sales_amount'] = $_POST['sales_amount'];
			unset($arr_save['commission']['fieldchange']);
			if(isset($arr_save['commission']['contact_id']) && strlen($arr_save['commission']['contact_id']) == 24 )
				$arr_save['commission']['contact_id'] = new MongoId($arr_save['commission']['contact_id']);
			$arr_save['_id'] = new MongoId($this->get_id());
			$this->opm->save($arr_save);
			echo json_encode(array_merge($arr_save['commission'],array('sales_amount'=>$_POST['sales_amount'])));
		}
		die;
	}
	function other_tab_auto_save(){
		if(!$this->check_permission($this->name.'_@_entry_@_edit')){
			echo 'You do not have permission on this action.';
			die;
		}
		if (!empty($_POST)) {
			parse_str($_POST['content'],$data);
			$arr_save['_id'] = new MongoId($_POST['id']);
			foreach($data as $key=>$value)
				$arr_save[$key] = $value;
			if ($this->opm->save($arr_save)) {
				echo 'ok';
			} else {
				echo 'Error: ' . $this->opm->arr_errors_save[1];
			}
		}
		die;
	}


	function theme(){
	}
	function render_pdf($arr_data = array()){
		if(!isset($arr_data['footer']) || $arr_data['footer'] == ''){
			$arr_data['footer'] = '
								<ul>
									<li>103, 3016 - 10th Ave. NE, Calgary, Alberta, Canada T2A 6K4 </li>
									<li><span style="font-weight:bold;">Tel:</span> 403.614.6113</li>
									<li class="left"><span class="bold_text">Web:</span> <a href="banhmisub.com">banhmisub.com</a></li>
								</ul>';
		}
		$this->layout = 'ajax';
		$this->set('arr_data',$arr_data);
		if(isset($arr_data['render_path']))
			$this->render($arr_data['render_path']);
		else
			$this->render('../Elements/render_pdf');
	}
	function print_pdf($arr_post = array(), $return = false){
		if( empty($arr_post) )
			$arr_post = $_POST;
		$this->selectModel('Stuffs');
		$token = $this->Stuffs->select_one(array('name'=>new MongoRegex('/report_pdf_token/i')));
		$report_token = $token['value'][] = md5(time().rand(0,123654));
		$this->Stuffs->save($token);
		$report_size = (isset($arr_post['report_size'])&&$arr_post['report_size']!='' ? $arr_post['report_size'] : 'A4');
		$report_file_name = (isset($arr_post['report_file_name'])&&$arr_post['report_file_name']!='' ? $arr_post['report_file_name'] : md5(time().rand(0,1000)));
		$report_orientation = (isset($arr_post['report_orientation'])&&$arr_post['report_orientation']!='' ? $arr_post['report_orientation'] : 'portrait');
		$report_url = (isset($arr_post['report_url'])&&$arr_post['report_url']!='' ? $arr_post['report_url'] : URL);
		if(strpos($report_url, '?') === false) {
			$report_url .= '?print_pdf='.$report_token;
		} else {
			$report_url = explode('?', $report_url);
			$report_url = $report_url[0].'?print_pdf='.$report_token;
		}
		if (isset($arr_post['pages'])) {
			foreach($arr_post['pages'] as $page) {
				$report_url .= '&pages[]='.$page;
			}
		}
		$custom_footer = '';
		if( isset($arr_post['custom_footer']) ) {
			$custom_footer = ' "'.$arr_post['custom_footer'].'"';
		}
		$command = PHANTOMJS_PATH."phantomjs ".PHANTOMJS_PATH."kei.js \"".$report_url."\" ".PHANTOMJS_PATH.DS."..".DS."upload".DS.$report_file_name.".pdf \"".$report_size."\" \"".$report_orientation."\"".$custom_footer;
		if(exec($command)=='ok'){
			if($this->request->is('ajax') && !$return){
				echo URL.'/upload/'.$report_file_name.'.pdf';
				die;
			}
			return true;
		}
		if($this->request->is('ajax') && !$return){
			echo 'failed';
			die;
		}
		return false;
	}
	public function support(){
		$this->selectModel('Support');
		$supports = $this->Support->select_all(array(
		                                       	'arr_where'=>array('module'=>$this->modelName)
		                                       ));
		$this->set('supports',$supports);
		$this->set('name',$this->name);
		$this->set('iditem',$this->get_id());
		$this->render('../Elements/support');
	}
	function autocomplete(){
		if(isset($_POST['data'])){
			header("Content-type: application/json");
			$arr_data = array();
			$_POST['data'] = (array)json_decode($_POST['data']);
			if($this->name=='Products'){
				if(is_numeric($_POST['data']['name'])){
					$where['code'] = (int)$_POST['data']['name'];
				} else {
					$where['$or'] = array(
					                      array('sku' => new MongoRegex('/'.$_POST['data']['name'].'/i')),
					                      array('name' => new MongoRegex('/'.$_POST['data']['name'].'/i')),
					                );
				}
				if($_POST['data']['type']!='')
					$where['product_type'] = $_POST['data']['type'];
				if($_POST['data']['category']!='')
					$where['category'] = $_POST['data']['category'];
				$products = $this->opm->select_all(array(
				                       'arr_where'=> $where,
				                       'arr_order'=>array('name'=>1),
				                       'arr_field'=>array('name'),
				                       'limit'=>10
				                       ));
				if($products->count()){
					$arr_data = array();
					foreach($products as $value)
						$arr_data[]['name'] = $value['name'];
				}
			} else if($this->name=='Contacts'){
				$where['full_name'] = new MongoRegex('/'.$_POST['data']['full_name'].'/i');
				unset($_POST['data']['full_name']);
				if(!empty($_POST['data'])){
					foreach($_POST['data'] as $key=>$value){
						$key = str_replace(array('data[Contact][',']'),'',$key);
						$where[$key] = $value;
					}
				}
				$this->selectModel('Contact');
				$contacts = $this->Contact->select_all(array(
				                       'arr_where'=> $where,
				                       'arr_order'=>array('full_name'=>1),
				                       'arr_field'=>array('full_name'),
				                       'limit'=>10
				                       ));
				if($contacts->count()){
					$arr_data = array();
					foreach($contacts as $value)
						$arr_data[]['full_name'] = $value['full_name'];
				}
			} else if($this->name=='Companies'){
				$where['name'] = new MongoRegex('/'.$_POST['data']['name'].'/i');
				unset($_POST['data']['name']);
				if(!empty($_POST['data'])){
					foreach($_POST['data'] as $key=>$value){
						$key = str_replace(array('data[Company][',']'),'',$key);
						$where[$key] = $value;
					}
				}
				$this->selectModel('Company');
				$companies = $this->Company->select_all(array(
				                       'arr_where'=> $where,
				                       'arr_order'=>array('name'=>1),
				                       'arr_field'=>array('name'),
				                       'limit'=>10
				                       ));
				if($companies->count()){
					$arr_data = array();
					foreach($companies as $value)
						$arr_data[]['name'] = $value['name'];
				}
			}
			echo "{\"data\":" .json_encode($arr_data)."}";
		}
		die;
	}
	function rebuild_code(){
		$arr_field = array('salesorder_date','invoice_date','quotation_date','code');
		switch (strtolower($this->modelName)) {
			case 'salesorder':
				$where = 'salesorder_date';
				break;
			case 'salesinvoice':
				$where = 'invoice_date';
				break;
			case 'quotation':
				$where = 'quotation_date';
				break;
			default:
				$where = 'quotation_date';
				break;
		}
        $i =0;
        for($y = 2013; $y <= 2014; $y++){
	        for($m = 1; $m <= 12; $m++){
	        	$day_start = date("1-$m-$y");
	        	$mongo_day_start = new MongoDate(strtotime("1-$m-$y"));
	        	$num_day_of_month = date('t',strtotime("1-$m-$y"));
	        	$day_end = date("$num_day_of_month-$m-$y");
	        	$mongo_day_end = new MongoDate(strtotime("$num_day_of_month-$m-$y") + DAY - 1);
	        	echo "$day_start to $day_end";
	        	$records = $this->opm->select_all(array('arr_where'=>array($where=>array('$gte'=>$mongo_day_start,'$lte'=>$mongo_day_end)),'arr_field'=>$arr_field,'arr_order'=>array($where=>1)),array('limit'=>999999));
        		pr($records->count().' record(s) found.');
        		$year = substr($y, 2);
        		$i = 1;
	        	foreach($records as $value){
	        		$arr_data = array();
	        		$arr_data['_id'] = new MongoId($value['_id']);
	        		$arr_data['code'] = "$year-".str_pad($m, 2, "0", STR_PAD_LEFT)."-".str_pad($i, 3, "0", STR_PAD_LEFT);
	        		pr($arr_data['code']);
	        		$this->opm->rebuild_collection($arr_data);
	        		$i++;
	        	}
	        	echo ($i-1).' record(s) updated.';
	        	echo '<hr />';

	        }
    	}
        die;
	}
	//CAL PRICE AT QT,SO,SI
	function cal_price_line($arr_post = array(),$noAjax = false, $isReturn = false, $query = array()){
        if(isset($_POST['data']) && !$noAjax){
            $arr_post = $_POST;
            $arr_post['data'] = (array)json_decode($_POST['data']);
        }
        if(!empty($arr_post)){
            $data = $arr_post['data'];
            $fieldchange = $arr_post['fieldchange'];
            $id = $this->get_id();
            if( empty($query) ) {
        		$query = $this->opm->select_one(array('_id'=>new MongoId($id)),array('products','options','company_id','tax','currency','invoice_status', 'invoice_type', 'shipping_cost'));
            }
        	if(!isset($query['currency']))
        		$query['currency'] = 'cad';
        	if(!isset($query['shipping_cost']))
        		$query['shipping_cost'] = 0;
        	$date = $query['_id']->getTimestamp();
            if(!isset($query['options']))
                $query['options'] = array();
            $this->selectModel('Setting');
            $uom_list = $this->Setting->uom_option_list();
            //=================
            $is_custom  = false;
            $is_special = false;
            $is_combination = false;
            $is_custom_unit_price = false;
            $no_same_parent = false;
            //=================
            $total_sub_total = 0;
            $this_line_data = array();
            //Set các thông tin cần thiết
            //Xét riêng line entry
            //Nếu line cha thay đổi thông số liên quan đến size thì dùng size này gán hết vào option để tìm giá
            if(strpos($fieldchange, 'size')!==false){
                $size_key = $fieldchange;
                $size_value = $data[$size_key];
            }
            $line_no    = $this_line_no     = $data['id'];
            unset($data['id']);
            if( $query['shipping_cost'] && isset($query['products'][$this_line_no]['sku']) && preg_match('/^SHP/', $query['products'][$this_line_no]['sku'])  ) {
            	$query['shipping_cost'] -= $query['products'][$this_line_no]['sub_total'];
            }
            $query['products'][$this_line_no] = array_merge($query['products'][$this_line_no],$data);
            $line_data  = $this_line_data   = $query['products'][$this_line_no];
            if(isset($this_line_data['option_for'])&&$this_line_data['option_for']!=''){
                $no_same_parent = true;
                if(isset($this_line_data['same_parent'])&&$this_line_data['same_parent']==1){
                    $line_no    = $parent_line_no   = $this_line_data['option_for'];
                    $line_data  = $parent_line_data = $query['products'][$parent_line_no];
                    $no_same_parent = false;
                }
            }
            if($line_data['sell_by']=='combination')
                $is_combination = true;
            //Nếu product custom thì set lại unitprice và sellprice
            if(!is_object($line_data['products_id']))
                $line_data['unit_price'] = $line_data['sell_price'] = 0;
            // if($no_same_parent)
            //     $line_data = array_merge($line_data,$data);
            if(isset($line_data['gst_tax']) && $line_data['gst_tax']!='')
                $line_data['taxper'] = $this->get_tax($line_data['gst_tax']);
            else if(isset($query['tax']) && $query['tax']!='')
                $line_data['taxper'] = $this->get_tax($query['tax']);
            //==============================================
            //Nếu là thay đổi ở line entry, thì foreach option tính lại từ đầu
            //Nếu thay đổi sell price ở line entry thì bỏ qua
            //FIELDCHANGE
            if(($fieldchange!='sell_price')&&!$no_same_parent){
                $line_data['plus_sell_price'] = 0;
                if(isset($line_data['plus_unit_price']))
                    unset($line_data['plus_unit_price']);
                //Cal Bleed==============================================
                $cal_price = new cal_price;
            	$lineBleed = $cal_price->cal_bleed($line_data, true);
            	if( !empty($lineBleed) ){
                	$line_data['bleed_sizew'] = $lineBleed['bleed_sizew'];
                	$line_data['bleed_sizeh'] = $lineBleed['bleed_sizeh'];
            	} else {
            		$line_data['bleed_sizew'] = $line_data['bleed_sizeh'] = 0;
            	}
                //End Cal Bleed==============================================
                //Lấy tất cả option của line
                $options = $this->new_option_data(array('key'=>$line_no,'products_id'=>$line_data['products_id'],'options'=>$query['options'],'products'=>$query['products'],'date'=>$date),$query['products']);
                if(isset($options['option'])&&!empty($options['option'])){
                	foreach($options['option'] as $option_key=>$option){
                        if(isset($option['same_parent'])&&$option['same_parent']==1
                        	&& isset($option['choice'])&&$option['choice']==1){
                        	$option['sizew']        = $line_data['sizew'];
                            $option['sizew_unit']   = $line_data['sizew_unit'];
                            $option['sizeh']        = $line_data['sizeh'];
                            $option['sizeh_unit']   = $line_data['sizeh_unit'];
                            if(isset($size_key))
                                $option[$size_key]  = $size_value;
                        	$cal_price = new cal_price;
            				$optionBleed = $cal_price->cal_bleed($option, true);
            				if( !empty($optionBleed) ) {
                            	$line_data['bleed_sizew'] += $optionBleed['bleed_sizew'];
                            	$line_data['bleed_sizeh'] += $optionBleed['bleed_sizeh'];
            				}
                        }
                    }
                    //=============Xét bleed=============
		            if( isset($line_data['bleed_sizew']) && !$line_data['bleed_sizew'] ) unset($line_data['bleed_sizew']);
		            if( isset($line_data['bleed_sizeh']) && !$line_data['bleed_sizeh'] ) unset($line_data['bleed_sizeh']);
		            //=============End Xét bleed=========
                    foreach($options['option'] as $option_key=>$option){
                        if(isset($option['same_parent'])&&$option['same_parent']==1){
                            //Set kiểu đặt biệt sẽ tính line cha trước rồi mới + thêm vào plus sell price
                            $is_special = true;
                            if(isset($option['is_custom']) && $option['is_custom']==1)
                                $option['sell_price'] = $option['unit_price'];
                            //Same parent thì lấy size của line cha gán hết vào
                            //=======================
                            $option['sizew']        = $line_data['sizew'];
                            $option['sizew_unit']   = $line_data['sizew_unit'];
                            $option['sizeh']        = $line_data['sizeh'];
                            $option['sizeh_unit']   = $line_data['sizeh_unit'];
                            if(isset($size_key))
                                $option[$size_key]  = $size_value;
                            if( isset($line_data['bleed_sizew']) ) {
                            	$option['bleed_sizew'] = $line_data['bleed_sizew'];
                            }
                            if( isset($line_data['bleed_sizeh']) ) {
                            	$option['bleed_sizeh'] = $line_data['bleed_sizeh'];
                            }
                            $option['quantity']     = (isset($option['quantity']) ? (float)$option['quantity'] : 1);
                            //========================
                            $option['plus_sell_price'] = 0;
                            if(!isset($option['sell_by'])||$option['sell_by']==''){
                            	if(isset($uom_list[$option['oum']]))
                                	$option['sell_by'] = $uom_list[$option['oum']];
                                else {
                                	$option['sell_by'] = 'area';
                                	$option['oum'] = 'Sq.ft.';
                                }
                            }
                            //Bắt đầu tính giá trên từng option
                            //========================
                            $cal_price = new cal_price;
                            $cal_price->arr_product_items   = $option;
                            $cal_price->price_break_from_to = $this->change_sell_price_company($query['company_id'],$option['product_id']);
                            if(!isset($option['user_custom']) || $option['user_custom']){
                                //Nếu là loại custom thì không cần tìm lại unitprice mà cứ nhân size, số lượng
                                $cal_price->field_change = 'sell_price';
                            }else{
                                $cal_price->field_change = $fieldchange;
                                //Nhân cho số lượng line cha để tìm price break chính xác
                                $cal_price->arr_product_items['quantity'] *= $line_data['quantity'];
                                $cal_price->cal_price_items();
                                //Tính xong chỉ cần gán lại sellprice, dùng sell_price này gán tiếp tính lại giá
                                $option['sell_price']           = $cal_price->arr_product_items['sell_price'];
                                $cal_price->arr_product_items   = $option;
                                $cal_price->field_change        = 'sell_price';
                            }
                            $option = $cal_price->cal_price_items();
            				if(IS_LOCAL && $this->name == 'Salesinvoices' && $query['currency'] != 'cad') {
            					$option = $cal_price->cal_price_currency('cad',$query['currency'],$this->arr_currency);
            				}

                            //=========== End calprice =============
                        	if(isset($option['choice'])&&$option['choice']==1)
                            	$line_data['plus_sell_price'] += $option['sub_total'];
                            //=========== Lưu ngược lại vào option nếu có =============
                            if(isset($option['this_line_no'])&&isset($query['options'][$option['this_line_no']])){
                                $this_option_data = $query['options'][$option['this_line_no']];
                                $this_option_data['sell_price'] = $option['sell_price'];
                                $this_option_data['quantity'] = $option['quantity'];
                                $this_option_data['sub_total'] = $option['sub_total'];
                                $this_option_data['sizew']      = $option['sizew'];
                                $this_option_data['sizew_unit'] = $option['sizew_unit'];
                                $this_option_data['sizeh']      = $option['sizeh'];
                                $this_option_data['sizeh_unit'] = $option['sizeh_unit'];
                                $query['options'][$option['this_line_no']] = $this_option_data;
                            }
                            // $fieldchange = '';
                            //==========End tính giá option============
                        } // End if SAMEPARENT
                        //Cộng đồn sub_total để sử dụng cho sellby là combination
                        $total_sub_total += (isset($option['sub_total']) ? (float)$option['sub_total'] : 0);
                    } // End foreach
                }// End If OPTION
            } //END FIELDCHANGE
            //=============Xét custom_unit_price=============
            if(!isset($line_data['custom_unit_price']))
                $line_data['custom_unit_price'] = $line_data['unit_price'];
            else if(isset($line_data['custom_unit_price'])){
                if(!is_object($line_data['products_id']) && $line_data['custom_unit_price']!=0){
                    $line_data['sell_price'] = $line_data['unit_price'] = $line_data['custom_unit_price'];
                    if($fieldchange=='custom_unit_price')
                        $line_data['plus_sell_price'] = 0;
                    $fieldchange = 'custom';
                }
            }
            //=============End Xét custom_unit_price=========
            //=============Xét bleed=============
            if( isset($line_data['bleed_sizew']) && !$line_data['bleed_sizew'] ) unset($line_data['bleed_sizew']);
            if( isset($line_data['bleed_sizeh']) && !$line_data['bleed_sizeh'] ) unset($line_data['bleed_sizeh']);
            //=============End Xét bleed=========
            $cal_price = new cal_price;
            if(isset($line_data['products_id']) && is_object($line_data['products_id']) && ( !isset($line_data['option_for']) || (isset($line_data['user_custom']) && !$line_data['user_custom']) ) ){
            	$this->selectModel('Product');
            	$this_product = $this->Product->select_one(array('_id' => $line_data['products_id']),array('sell_price','product_type'));
            	$line_data['sell_price'] = isset($this_product['sell_price']) ? (float)$this_product['sell_price'] : 0;
            	$is_combination = (isset($this_product['product_type']) && $this_product['product_type'] == 'Combination' ? true : false);
            }
            $cal_price->arr_product_items = $line_data;
	        $tmp_sell_price = 0;
            if($fieldchange != 'custom_unit_price'){
            	//=============Tính line chính==============
            	if($is_combination){
           			$cal_price->price_break_from_to = $this->change_sell_price_company($query['company_id'],$line_data['products_id']);
	                $line_data['plus_sell_price'] = $line_data['plus_unit_price'] = 0;
	                $line_data = array_merge($line_data,$cal_price->combination_cal_price());
	                $line_data['custom_unit_price'] = $line_data['unit_price'];
            		if(IS_LOCAL && $this->name == 'Salesinvoices' && $query['currency'] != 'cad') {
            			$line_data = $cal_price->cal_price_currency('cad',$query['currency'],$this->arr_currency);
            		}
	                // $line_data['sell_price'] = $line_data['unit_price'] += $total_sub_total;
	                // if($total_sub_total>0){
	                //     $line_data['sub_total'] = round((float)$line_data['unit_price']*(float)$line_data['quantity'],2);
	                //     $line_data['tax'] = round(((float)(isset($line_data['taxper']) ? $line_data['taxper'] : 0)/100)*(float)$line_data['sub_total'],3);
	                //     $line_data['amount'] = round((float)$line_data['sub_total']+(float)$line_data['tax'],2);
	                // }
	            } else {
	                $result = array();
	                if($fieldchange!='sell_price' && (!isset($line_data['option_for']) || !isset($line_data['user_custom']) || !$line_data['user_custom'] ) ){
	                    $result = $this->change_sell_price_company($query['company_id'],$line_data['products_id']);
                    	$tmp_sell_price = isset($result['sell_price']) ? $result['sell_price'] : 0;
	                	$cal_price->price_break_from_to = $result;
	                    if(/*IS_LOCAL &&*/ !isset($line_data['option_for']) && isset($result['company_price_break']) ){
	                    	// if(!isset($line_data['vip']))
	                    	// 	$cal_price->auto_check = 1;
	                    	$cal_price->vip = isset($line_data['vip']) ? $line_data['vip'] : 0;
	                    }
	                }
	                if($fieldchange == 'sell_price' && isset($line_data['option_for'])) {
	                	$line_data['user_custom'] = 1;
	                }
	                $cal_price->field_change = $fieldchange;
	                if(isset($line_data['user_custom']) && $line_data['user_custom']) {
	                	$cal_price->field_change = 'sell_price';
	                }
	                $line_data = array_merge($line_data,$cal_price->cal_price_items($is_special));
	                if(IS_LOCAL && $this->name == 'Salesinvoices' && $query['currency'] != 'cad' && $fieldchange != 'custom') {
            			$line_data = $cal_price->cal_price_currency('cad',$query['currency'],$this->arr_currency);
            		}
	                $line_data['custom_unit_price'] = $line_data['unit_price'];
	            }
            	//=============End tính line chính==========
            } else {
    	       	//=============Xét custom_unit_price=============
            	if(isset($line_data['custom_unit_price'])){
	                if(!is_object($line_data['products_id'])){
	                    $line_data['custom_unit_price'] = $line_data['unit_price'] = $line_data['sell_price'];

	                } else {
	                    $unit_price = $line_data['unit_price'];
	                    if($line_data['custom_unit_price']<$line_data['unit_price']
	                            &&!$this->check_permission($this->name.'_@_custom_unit_price_@_add'))
	                       	$line_data['custom_unit_price'] = $line_data['unit_price'];
	                    $line_data['sell_price'] = $line_data['unit_price'] = $line_data['custom_unit_price'];
	                    //if($is_combination){
							$line_data['sub_total'] = round((float)$line_data['unit_price']*(float)$line_data['quantity'],2);
	                    	
	                        if(isset($line_data['gst_tax']) && $line_data['gst_tax']!=''){
	                        	$tmp_taxper = $this->get_tax($line_data['gst_tax']);
	                        	$line_data['tax'] = round(((float)$tmp_taxper/100)*(float)$line_data['sub_total'],3);
	                        }
	                        else if(isset($line_data['taxper']))
	                        	$line_data['tax'] = round(((float)$line_data['taxper']/100)*(float)$line_data['sub_total'],3);
	                       	else
	                       		$line_data['tax'] = 0;

	                        $line_data['amount'] = round((float)$line_data['sub_total']+(float)$line_data['tax'],2);

						/*} else { // Vi sao edit custom unit lai phai tinh lai tu dau ?
	                        $cal_price = new cal_price;
	                        $cal_price->arr_product_items = $line_data;
	                        $cal_price->field_change = 'sell_price';
	                        $line_data = $cal_price->cal_price_items($is_special);
	                    }*/
	                    $line_data['unit_price'] = $unit_price;

	                }
	            }
     	        //=============End Xét custom_unit_price=========
            }

			/*
            //25-03-2015 - https://app.asana.com/0/search/10511086818339/29866794531173
            if((!isset($line_data['vip']) || !$line_data['vip']) && (isset($line_data['company_price_break']) && $line_data['company_price_break']) && $fieldchange != 'custom_unit_price' && !$is_combination){
        		$line_data['sell_price'] = $line_data['unit_price'] = $tmp_sell_price;
            }
            */


            $query['products'][$line_no] = $line_data;
            $this_line_data = $query['products'][$this_line_no];
            //Nếu line entry đang sửa là option và không phải same parent thì update ngược lại option
            if($no_same_parent){
                if(isset($query['options'])&&!empty($query['options'])){
                    foreach($query['options'] as $option_key=>$option){
                        if(isset($option['deleted'])&&$option['deleted']) continue;
                        if(!isset($option['line_no']) || $option['line_no']!=$line_no) continue;
                        $query['options'][$option_key] = array_merge($option,$line_data); break;
                    }
                }
            }
            //Tính lại sum
            $arr_sum = $this->new_cal_sum($query['products']);
            $query = array_merge($query,$arr_sum);
            if( isset($query['products'][$this_line_no]['sku']) && preg_match('/^SHP/', $query['products'][$this_line_no]['sku'])  ) {
            	$query['shipping_cost'] += $query['products'][$this_line_no]['sub_total'];
            }
            //=============Save or Return===============
            if( $isReturn ) {
            	return $query;
            }
            $this->opm->save($query);
            //==========================================
            $arr_data = array();
            $arr_data['sum'] = $arr_sum;
            //===Update Minium Order Adjustment=========
            if($this->name!='Purchaseorders' && (!isset($query['invoice_status']) || $query['invoice_status']!= 'Credit')
            									&& (!isset($query['invoice_type']) || $query['invoice_type']!= 'Credit')
            	){
	            $minimum = $this->get_minimum_order();
	            if($arr_sum['sum_sub_total'] < $minimum){
	            	$extra_price = $minimum - $arr_sum['sum_sub_total'];
	            	$arr_data['last_insert']['sub_total'] = $extra_price;
	            	$arr_data['last_insert']['tax'] = $extra_price*$line_data['taxper']/100;
	            	$arr_data['last_insert']['amount'] = $extra_price+$arr_data['last_insert']['tax'];
	            	$arr_data['sum']['sum_amount'] += $arr_data['last_insert']['amount'];
	            	$arr_data['sum']['sum_sub_total'] += $arr_data['last_insert']['sub_total'];
	            	$arr_data['sum']['sum_tax'] += $arr_data['last_insert']['tax'];
	            }
            }
            //===End Update Minium Order Adjustment=====
            // $line_data['unit_price'] = $line_data['custom_unit_price'];
            if(!isset($parent_line_data))
                $arr_data['self'] = $line_data;
            else {
                $arr_data['parent'] = $line_data;
                $arr_data['self'] = $this_line_data;
            }
            if( isset($_POST['data']) && !$noAjax )
                echo json_encode($arr_data);
            else
                return $arr_data;
        }
        die;
    }
    function new_option_data($arr_data,$products){
        $data = $option_group = array(); $groupstr = '';
        $idsub = $arr_data['key'];
        $products_id = $arr_data['products_id'];
        $custom_option = $arr_data['options'];
        if(isset($arr_data['date'])){
        	if(is_object($arr_data['date']))
        		$date = $arr_data['date']->sec;
        	else if(is_numeric($arr_data['date']))
        		$date = $arr_data['date'];
        }
        else
        	$date = time();
        $change_date = strtotime(date('2014-03-28'));
        $arr_option_choice = array();
        foreach($custom_option as $value){
            if(!isset($value['choice']))
                $value['choice'] = 0;
            if(isset($value['this_line_no']))
                $arr_option_choice[$value['this_line_no']]['choice'] = $value['choice'];
        }
        if($idsub<0)
            return $data;
        foreach($custom_option as $k_custom=>$v_custom){
            if(!isset($v_custom['parent_line_no'])
               || $v_custom['parent_line_no']!=$idsub ){
                unset($custom_option[$k_custom]);
                continue;
            }
        }
        if(is_object($products_id))
            $products_id = (string)$products_id;

        if($products_id!='' && strlen($products_id)==24){
            $this->selectModel('Product');
            $products_option = $this->Product->options_data($products_id);
        }
        if($date<$change_date){
	        if(isset($products_option['productoptions']) && !empty($products_option['productoptions'])){
	            $data = $products_option['productoptions'];
	            foreach($data as $kk=>$vv){
	                foreach($custom_option as $k_custom => $v_custom){
	                    if( !isset($v_custom['proline_no']) || $v_custom['proline_no'] == ''){
	                        unset($custom_option[$k_custom]);
	                        continue;
	                    }
	                    if($kk == $v_custom['proline_no']){
	                        $option = $v_custom;
	                        $data[$kk] = array_merge($vv,array_merge($v_custom,array('is_custom'=>true)));
	                        unset($custom_option[$k_custom]);
	                        break;
	                    }
	                }
	            }
	        }else{
	            foreach($custom_option as $key=>$value)
	                $custom_option[$key]['is_custom'] = true;
	            $data = $custom_option;
	        }
    	} else{
    		foreach($custom_option as $key=>$value)
                $custom_option[$key]['is_custom'] = true;
            $data = $custom_option;
    	}
        if(!empty($data)){
            foreach($data as $k=>$v){
                $data[$k]['_id'] = $k;
                if(!isset($data[$k]['option_group']))
                	$data[$k]['option_group'] = '';
            }
            $this->opm->aasort($data,'option_group');
        }
        foreach($data as $kks=>$vvs){
            if(!isset($vvs['product_id']))
                continue;
            if(!isset($vvs['proline_no']))
                $data[$kks]['proline_no'] = $vvs['_id'];
            if(!isset($vvs['parent_line_no']))
                $data[$kks]['parent_line_no'] = $idsub;
            if($data[$kks]['proline_no']!=''&&(!isset($vvs['line_no']) || $vvs['line_no']=='')){
                foreach($products as $k_p => $v_p){
                     if($v_p['deleted'] || !isset($v_p['option_for']) || $v_p['option_for']!=$idsub){
                        unset($products[$k_p]);
                        continue;
                    }
                    if($v_p['proids']==$products_id.'_'.$data[$kks]['proline_no']){
                        $data[$kks]['line_no'] = $k_p;
                        unset($products[$k_p]);
                        break;
                    }
                }
            }
            if(isset($vvs['require'])&&$vvs['require']==1){
                $data[$kks]['choice'] = 1;
                if(isset($arr_option_choice[$kks]['choice']))
                    $data[$kks]['choice'] = $arr_option_choice[$kks]['choice'];
                if(isset($vvs['group_type'])&&$vvs['group_type']!='Exc')
                    $data[$kks]['xlock']['choice'] = 1;
                if(!isset($vvs['same_parent']) || !$vvs['same_parent'])
                	$data[$kks]['xlock']['choice'] = 1;
            }
            if(is_object($vvs['product_id'])){
            	$data[$kks]['xlock']['product_name'] = 1;
            	$data[$kks]['xlock']['oum'] = 1;
            }
            $data[$kks]['user_custom'] = 0;
            if(!isset($vvs['user_custom']) || $vvs['user_custom']) {
            	$data[$kks]['user_custom'] = 1;
            	if(!isset($vvs['user_custom']) && isset($vvs['same_parent']) && !$vvs['same_parent'])
            		$data[$kks]['user_custom'] = 0;
            }
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
    function option_cal_price($arr_tmp_data){
        if(!isset($arr_tmp_data['product_key']))
            return false;
        //Xử lý mảng $_POST tách ra theo stt
        $arr_data = array();
        $arr_options = array();
        //=====================================
        $this->selectModel('Setting');
        $uom_list = $this->Setting->uom_option_list();
        //=====================================
        $id = $this->get_id();
        $query = $this->opm->select_one(array('_id'=>new MongoId($id)),array('options','products','company_id','taxval','currency'));
        $line_no = $arr_tmp_data['product_key'];
        $line_data = $query['products'][$line_no];
        $parent_id = $query['products'][$line_no]['products_id'];
        if(isset($line_data['plus_unit_price']))
            unset($line_data['plus_unit_price']);
        if(!is_object($line_data['products_id']))
            $line_data['unit_price'] = $line_data['sell_price'] = 0;
        $line_data['plus_sell_price'] = 0;
        unset($arr_tmp_data['submit'],$arr_tmp_data['product_key']);

        if(IS_LOCAL && $this->name == 'Salesinvoices'){
        	if(!isset($query['currency']))
        		$query['currency'] = 'cad';
        }
        //=====================================
        foreach($arr_tmp_data as $key=>$value){
            $position = strrpos($key, '_',-1);
            if($position===false) continue;
            $k =substr($key, $position+1);
            if($k=='id') continue;
            if(strpos($key, 'cb_')!==false
               &&$value == 'on')
                $value = 1;
            $key = str_replace(array('_'.$k,'cb_'), '', $key);
            if($key=='product_id'&&strlen($value)==24)
                $value = new MongoId($value);
            if($key=='deleted'){
            	$value = false;
            }
            $arr_data[$k][$key] = $value;
        }
        //Debug - $_POST after group
        // pr($arr_data);die;
        //End xử lý $_POST
        //=====================================
        if(!isset($query['options'])){
            $query['options'] = array();
        }
        $i = count($query['options']);
        $arr_option = array();
        $option_group = array();
        foreach($arr_data as $key=>$value){
        	if((!isset($value['product_id']) || $value['product_id']=='')
        		&&isset($value['oum']))
        		$arr_data[$key]['sell_by'] = $value['sell_by'] = (isset($uom_list[$value['oum']]) ? $uom_list[$value['oum']] : 'unit');
            if(!isset($value['choice']))
                $arr_data[$key]['choice'] = $value['choice'] = 0;
            if(!isset($value['user_custom']))
                $arr_data[$key]['user_custom'] = $value['user_custom'] = 0;
            if(!isset($value['hidden']))
                $arr_data[$key]['hidden'] = $value['hidden'] = 0;
            if(isset($value['option_group'])&&$value['option_group']!=''
               	&&isset($value['group_type'])&&$value['group_type']=='Exc'
               	&&isset($value['choice'])&&$value['choice']==1){
            	if(!isset($option_group[$value['option_group'].'_'.$value['group_type']]))
        			$option_group[$value['option_group'].'_'.$value['group_type']] = 1;
        		else
        			$arr_data[$key]['choice'] = $value['choice'] = 0;
            }
            if(!isset($value['same_parent']))
                $arr_data[$key]['same_parent'] = $value['same_parent'] =  0;
            if(isset($value['thisline_no'])&&$value['thisline_no']!=''
               || isset($value['this_line_no'])&&$value['this_line_no']!=''){
                if(isset($value['thisline_no'])){
                    $value['this_line_no'] = $value['thisline_no'];
                    unset($value['thisline_no']);
                }
                $option_no = $value['this_line_no'];
                $query['options'][$option_no] = array_merge($query['options'][$option_no],$value);
                $query['options'][$option_no]['parent_line_no'] = $line_no;
                $arr_option[$key] = $query['options'][$option_no];
            } else {
                $value['this_line_no'] = $query['options'][$i]['this_line_no'] = $i;
                $query['options'][$i] = $value;
                $query['options'][$i]['parent_line_no'] = $line_no;
                $arr_option[$i] = $query['options'][$i];
                $i++;
            }
        }
        //==========================================================
        //Lấy option
        $options = $this->new_option_data(array('key'=>$line_no,'products_id'=>$parent_id,'options'=>$arr_option,'date'=>$query['_id']->getTimestamp()),$query['products']);
        ksort($query['products']);
        end($query['products']);
        $num_of_products = key($query['products']);
        $num_of_products++;
        foreach($options['option'] as $key=>$value){
            $option_no = $value['this_line_no'];
            if(!isset($value['choice']))
               $value['choice'] = 0;
            if(isset($value['choice'])){
                if($value['choice']==0
                   &&isset($value['line_no'])&&$value['line_no']!=''){
                    if(isset($query['products'][$value['line_no']])){
                        unset($query['options'][$option_no]['line_no'],$options['option'][$key]['line_no'],$arr_option[$option_no]);
                        $query['products'][$value['line_no']] = array('deleted'=>true);
                    }
                } else if ($value['choice']==1){
                    if(!isset($value['line_no']) || $value['line_no']==''){
                        $query['products'][$num_of_products] = $this->new_line_entry($value,$query['products'][$value['parent_line_no']],$query['company_id'],$query['options'],$option_no);
                        $options['option'][$key]['line_no'] = $query['options'][$option_no]['line_no'] = $num_of_products;
                        $num_of_products++;
                    } else if( isset($query['products'][$value['line_no']]) /*&& count($query['products'][$value['line_no']]) == 1*/){
                    	$value['products_id'] = $value['product_id'];
                    	$value['products_name'] = $value['product_name'];
                    	$value['_id'] = $value['line_no'];
                    	$value['option_for'] = $value['parent_line_no'];
                    	$value['proids'] = $value['products_id'].'_'.$value['this_line_no'];
                    	$opt_line_no = $value['line_no'];
                    	foreach(array('product_id','product_name','line_no','xlock','parent_line_no','require','this_line_no') as $field)
                    		unset($value[$field]);
                    	$query['products'][$opt_line_no] = $value;
                    	unset($query['products'][$opt_line_no]['choice']);
                    }
                }
            }
            $query['options'][$option_no]['choice'] = $value['choice'];
        }
        //==========================================================
        $arr_update = array('sizew','product_name','sell_by','oum','sizew_unit','sizeh','sizeh_unit','unit_price','quantity','sub_total','sell_price','amount','tax','same_parent','user_custom','hidden');
        $is_special = false;
        $this->selectModel('Setting');
        //===================Tính lại thuế===========================
        $taxval = 0;
        if(isset($query['taxval'])&&$query['taxval']!='')
        	$taxval = $query['taxval'];
        //===============End Tính lại thuế===========================
        //Cal Bleed==============================================
        $cal_price = new cal_price;
    	$lineBleed = $cal_price->cal_bleed($line_data, true);
    	if( !empty($lineBleed) ){
        	$line_data['bleed_sizew'] = $lineBleed['bleed_sizew'];
        	$line_data['bleed_sizeh'] = $lineBleed['bleed_sizeh'];
    	} else {
    		$line_data['bleed_sizew'] = $line_data['bleed_sizeh'] = 0;
    	}
        //End Cal Bleed==============================================
        $total_sub_total = 0;
        //Lặp vòng và tính lại toàn bộ option
        if(isset($options['option'])&&!empty($options['option'])){
	        foreach($options['option'] as $option_key=>$option){
	            if(isset($option['same_parent']) && $option['same_parent']==1
	            	&& isset($option['choice'])&&$option['choice']==1){
	            	$option['sizew']        = $line_data['sizew'];
	                $option['sizew_unit']   = $line_data['sizew_unit'];
	                $option['sizeh']        = $line_data['sizeh'];
	                $option['sizeh_unit']   = $line_data['sizeh_unit'];
	                if(isset($size_key))
	                    $option[$size_key]  = $size_value;
	                $cal_price = new cal_price;
					$optionBleed = $cal_price->cal_bleed($option, true);
					if( !empty($optionBleed) ) {
	                	$line_data['bleed_sizew'] += $optionBleed['bleed_sizew'];
	                	$line_data['bleed_sizeh'] += $optionBleed['bleed_sizeh'];
					}
	            }
	        }
	        //=============Xét bleed=============
            if( isset($line_data['bleed_sizew']) && !$line_data['bleed_sizew'] ) unset($line_data['bleed_sizew']);
            if( isset($line_data['bleed_sizeh']) && !$line_data['bleed_sizeh'] ) unset($line_data['bleed_sizeh']);
            //=============End Xét bleed=========
            foreach($options['option'] as $option_key=>$option){
                if(!isset($option['same_parent']))
                    $option['same_parent'] = 0;
                $option['sell_price'] = $option['unit_price'];
                if($option['same_parent']==1){
                    //Set kiểu đặt biệt sẽ tính line cha trước rồi mới + thêm vào plus sell price
                    $is_special = true;
                    //Same parent thì lấy size của line cha gán hết vào
                    //=======================
                    $option['sizew']        = $line_data['sizew'];
                    $option['sizew_unit']   = $line_data['sizew_unit'];
                    $option['sizeh']        = $line_data['sizeh'];
                    $option['sizeh_unit']   = $line_data['sizeh_unit'];
                    if(isset($size_key))
                        $option[$size_key]  = $size_value;
                    if( isset($line_data['bleed_sizew']) ) {
                    	$option['bleed_sizew'] = $line_data['bleed_sizew'];
                    }
                    if( isset($line_data['bleed_sizeh']) ) {
                    	$option['bleed_sizeh'] = $line_data['bleed_sizeh'];
                    }
                    $option['quantity']     = (isset($option['quantity']) ? (float)$option['quantity'] : 1);
                    //========================
                    $option['plus_sell_price'] = 0;
                    if(!isset($option['sell_by'])||$option['sell_by']=='')
                        $option['sell_by'] = $uom_list[$option['oum']];
                    //Bắt đầu tính giá trên từng option SAMEPARENT
                    $cal_price = new cal_price;
                    $cal_price->arr_product_items   = $option;
                    if(isset($option['user_custom']) && !$option['user_custom']) {
                    	$cal_price->field_change = '';
                    	$cal_price->price_break_from_to = $this->change_sell_price_company($query['company_id'],$option['product_id']);
                        //Nhân cho số lượng line cha để tìm price break chính xác
                        $cal_price->arr_product_items['quantity'] *= $line_data['quantity'];
                        $cal_price->cal_price_items();
                        //Tính xong chỉ cần gán lại sellprice, dùng sell_price này gán tiếp tính lại giá
                        $option['sell_price']           = $cal_price->arr_product_items['sell_price'];
                        $cal_price->arr_product_items   = $option;
                        $cal_price->field_change        = 'sell_price';
                    } else {
                    	$cal_price->field_change = 'sell_price';
                    }
                    $option = $cal_price->cal_price_items();
                    if(IS_LOCAL && $this->name == 'Salesinvoices' && $query['currency'] != 'cad'){
                    	$option = $cal_price->cal_price_currency('cad',$query['currency'], $this->arr_currency);
                    }
                    //=========== End calprice =============
                    if(isset($option['choice'])&&$option['choice']==1){
                    	$line_data['plus_sell_price'] += $option['sub_total'];
                    }
		           	$option['sizew']	= $line_data['sizew'];
		            $option['sizeh']	= $line_data['sizeh'];

                } else if($option['same_parent']==0){
                    //Bắt đầu tính giá trên từng option KHÔNG SAMEPARENT
                    $cal_price = new cal_price;
                    $option['taxper'] = $taxval;
                    $cal_price->arr_product_items = $option;
                    $tmp_sell_price = 0;
                    if(isset($option['user_custom']) && !$option['user_custom']) {
                    	$price_break = $this->change_sell_price_company($query['company_id'],$option['product_id']);
                    	$tmp_sell_price = $price_break['sell_price'];
                    	$cal_price->price_break_from_to = $price_break;
                    } else {
                    	$cal_price->field_change = 'sell_price';
                    }
                    $option = array_merge($option,$cal_price->cal_price_items(true));
                	if( isset($option['bleed']) && $option['bleed'] ) {
                		$tmp_sell_price = $option['sell_price'];
                	}
                    // if(IS_LOCAL && $this->name == 'Salesinvoices' && $query['currency'] != 'cad'){
                    // 	$option = $cal_price->cal_price_currency('cad',$query['currency'], $this->arr_currency);
                    // }
                    //=========== End calprice =============
                }
                $option['unit_price'] = $option['sell_price'];
                //=========== Lưu ngược lại vào option=============
                $query['options'][$option['this_line_no']] = array_merge($query['options'][$option['this_line_no']],$option);
                if(isset($option['line_no'])
                   &&isset($query['products'][$option['line_no']])
                   /*&&!$query['products'][$option['line_no']]['deleted']*/){
                    foreach($arr_update as $update_field){
                    	if(isset($option[$update_field])){
                    		$p_update_field = $update_field;
	                    	if($update_field=='product_name')
	                    		$p_update_field = 'products_name';
	                    	else if($update_field == 'unit_price')
	                    		$query['products'][$option['line_no']]['custom_unit_price'] = $option[$update_field];
                        	$query['products'][$option['line_no']][$p_update_field] = $option[$update_field];
                        }
                    }
                }
                if(isset($option['company_price_break'])){
                	if($option['same_parent'] == 0){
                		$option['sell_price'] = $option['unit_price'] = $tmp_sell_price;
                		if(isset($option['line_no'])&&isset($query['products'][$option['line_no']])) {
               				$query['products'][$option['line_no']]['unit_price'] = $query['products'][$option['line_no']]['sell_price'] = $option['sell_price'];
               			}
                	}
                }
                $total_sub_total += (isset($option['sub_total']) ? (float)$option['sub_total'] : 0);
                //==========End tính giá option====================
            } // End foreach OPTION
        }// End if OPTION

        //==================Xét custom unit price=================
        if(!isset($line_data['custom_unit_price']))
            $line_data['custom_unit_price'] = $line_data['unit_price'];
        else if(isset($line_data['custom_unit_price'])){
            if(!is_object($line_data['products_id']))
                $line_data['sell_price'] = 0;
        }
        $is_combination = false;
        //===============End Xét custom unit price================
        //=============Xét bleed=============
        if( isset($line_data['bleed_sizew']) && !$line_data['bleed_sizew'] ) unset($line_data['bleed_sizew']);
        if( isset($line_data['bleed_sizeh']) && !$line_data['bleed_sizeh'] ) unset($line_data['bleed_sizeh']);
        //=============End Xét bleed=========
        if(isset($line_data['products_id']) && is_object($line_data['products_id'])){
        	$this->selectModel('Product');
        	$this_product = $this->Product->select_one(array('_id' => $line_data['products_id']), array('product_type'));
            $is_combination = (isset($this_product['product_type']) && $this_product['product_type'] == 'Combination' ? true : false);
        }
        if($is_combination || $line_data['sell_by']=='combination'){
            $line_data['plus_sell_price']= $line_data['plus_unit_price'] = 0;
            $cal_price = new cal_price;
            $cal_price->arr_product_items = $line_data;
            $cal_price->price_break_from_to = $this->change_sell_price_company($query['company_id'],$line_data['products_id']);
            $line_data = array_merge($line_data,$cal_price->combination_cal_price());
            $line_data['custom_unit_price'] = $line_data['unit_price'];
            // $line_data['sell_price'] = $line_data['unit_price'] += $total_sub_total;
            // if($total_sub_total>0){
            //     $line_data['sub_total'] = round((float)$line_data['unit_price']*(float)$line_data['quantity'],2);
            //     $line_data['tax'] = round(((float)(isset($line_data['taxper']) ? $line_data['taxper'] : 0)/100)*(float)$line_data['sub_total'],3);
            //     $line_data['amount'] = round((float)$line_data['sub_total']+(float)$line_data['tax'],2);
            // }
        } else {
            $cal_price = new cal_price;
            if(isset($line_data['products_id']) && is_object($line_data['products_id'])){
            	$this->selectModel('Product');
            	$this_product = $this->Product->select_one(array('_id' => $line_data['products_id']),array('sell_price'));
            	$line_data['sell_price'] = isset($this_product['sell_price']) ? (float)$this_product['sell_price'] : 0;
            }
            $cal_price->arr_product_items = $line_data;
            $cal_price->price_break_from_to = $this->change_sell_price_company($query['company_id'],$line_data['products_id']);
            if(/*IS_LOCAL &&*/ !isset($line_data['option_for']) && isset( $cal_price->price_break_from_to['company_price_break']) ){
            	// if(!isset($line_data['vip']))
            	// 	$cal_price->auto_check = 1;
            	$cal_price->vip = isset($line_data['vip']) ? $line_data['vip'] : 0;
            }
            $tmp_sell_price = 0;
            if(isset($cal_price->price_break_from_to ['sell_price'])) {
            	$tmp_sell_price = $cal_price->price_break_from_to ['sell_price'];
            }
            $line_data = array_merge($line_data,$cal_price->option_cal_price());
            if((!isset($line_data['vip']) || !$line_data['vip']) && (isset($line_data['company_price_break']) && $line_data['company_price_break'])){
        		$line_data['sell_price'] = $line_data['unit_price'] = $tmp_sell_price;
            }
        }
        if(IS_LOCAL && $this->name == 'Salesinvoices' && $query['currency'] != 'cad'){
            $cal_price = new cal_price;
        	$cal_price->arr_product_items = $line_data;
        	$line_data = $cal_price->cal_price_currency('cad',$query['currency'], $this->arr_currency);
        }
        //==================Xét custom unit price=================
        // if(isset($line_data['custom_unit_price'])){
        //     if(!is_object($line_data['products_id'])){
        //         $line_data['custom_unit_price'] = $line_data['unit_price'] = $line_data['sell_price'];
        //     } else {
        //         $unit_price = $line_data['unit_price'];
        //         $line_data['custom_unit_price'] = $line_data['unit_price'];
        //         $line_data['sell_price'] = $line_data['unit_price'] = $line_data['custom_unit_price'];
        //         if($line_data['sell_by']=='combination'){
        //             $line_data['sub_total'] = round((float)$line_data['unit_price']*(float)$line_data['quantity'],2);
        //             $line_data['tax'] = round(((float)(isset($line_data['taxper']) ? $line_data['taxper'] : 0)/100)*(float)$line_data['sub_total'],3);
        //             $line_data['amount'] = round((float)$line_data['sub_total']+(float)$line_data['tax'],2);
        //         } else {
        //             $cal_price = new cal_price;
        //             $cal_price->arr_product_items = $line_data;
        //             $cal_price->field_change = 'sell_price';
        //             $line_data = $cal_price->cal_price_items($is_special);
        //         }
        //         $line_data['sell_price'] = $line_data['unit_price'] = $unit_price;
        //     }
        // }
        //===============End Xét custom unit price================
        $query['products'][$line_no] = $line_data;
        ksort($query['products']);
        $arr_sum = $this->new_cal_sum($query['products']);
        $query = array_merge($query,$arr_sum);
        if($this->request->is('ajax')){
        	if($this->opm->save($query))
	            echo 'ok';
	        else
	            echo $this->arr_errors_save[1];
            die;
        } else
        	$this->opm->save($query);
    }
    function create_temporary_product(){
    	$this->layout = 'ajax';
        if(isset($_POST['product_id'])&&isset($_POST['num_of_options'])){
            $product_id = $_POST['product_id'];
            $num_of_options = $_POST['num_of_options'];
            if($product_id != ''){
                $this->selectModel('Product');
                $product = $this->Product->select_one(array('_id'=> new MongoId($product_id)),array('code','name','oum','sell_price','quantity','sku','sizew','sizew_unit','sizeh','sizeh_unit','sell_by'));
                $product['this_line_no'] = $product['_id'].time();
                $product['product_id'] = $product['_id'];
                $product['product_name'] = $product['name'];
                $product['sizew_unit'] = (isset($product['sizew_unit']) ? $product['sizew_unit'] : 'in');
                $product['sizeh_unit'] = (isset($product['sizeh_unit']) ? $product['sizeh_unit'] : 'in');
                $product['sell_by'] = (isset($product['sell_by']) ? $product['sell_by'] : 'unit');
                $product['unit_price'] = (isset($product['sell_price']) ? (float)$product['sell_price'] : 0);
            } else {
                $product = array(
                                'this_line_no' => md5(time()),
                                'code' => '',
                                'product_id' => '',
                                'product_name' => 'This is new record. Click for edit',
                                'sizew_unit' => 'in',
                                'sizeh_unit' => 'in',
                                'sell_by' => 'area',
                                'oum' => 'Sq.ft.',
                                'unit_price' => 0,
                                'quantity' => 1,
                                'sku' => '',
                                'sizew' => 12,
                                'sizeh' => 12,
                                 );
                $this->set('custom_product',true);
            }
            $this->set('option',$product);
            $this->set('bg',($num_of_options%2==0 ? 'bg2' : 'bg1'));
            $this->render('../Elements/render_temporary_product');
        } else
            die;
    }
    function new_line_entry($option,&$parent_line,$company_id,&$current_options,$option_no){
        $option_for = $option['parent_line_no'];
        $option_id = $option['proline_no'];
        $new_line = array();
        $new_line['code']           = $option['code'];
        $new_line['sku']            = (isset($option['sku']) ? $option['sku'] : '');
        $new_line['products_name']  = isset($option['product_name'])?$option['product_name']:'';
        $new_line['products_id']    = $option['product_id'];
        $new_line['quantity']       = $option['quantity'];
        $new_line['sub_total']      = isset($option['sub_total'])?$option['sub_total']:'';
        $new_line['sizew']          = isset($parent_line['sizew']) ? $parent_line['sizew'] : $option['sizew'];
        $new_line['sizew_unit']     = isset($parent_line['sizew_unit'])?$parent_line['sizew_unit']:$option['sizew_unit'];
        $new_line['sizeh']          = isset($parent_line['sizeh']) ? $parent_line['sizeh'] : $option['sizeh'];
        $new_line['sizeh_unit']     = isset($parent_line['sizeh_unit'])?$parent_line['sizeh_unit']:$option['sizeh_unit'];
        $new_line['sell_by']        = (isset($option['sell_by']) ? $option['sell_by'] : 'unit');
        $new_line['oum']            = isset($option['oum'])?$option['oum']:'';
        $new_line['same_parent']    = isset($option['same_parent']) ? (int)$option['same_parent'] : 0;
        $new_line['sell_price']     = (float)$option['unit_price'] - (float)$option['unit_price']*((float)$option['discount']/100);
        $new_line['currency'] 		= isset($parent_line['currency']) ? $parent_line['currency'] : 'cad';
        if(isset($parent_line['taxper']))
            $new_line['taxper']     = $parent_line['taxper'];
        if(isset($parent_line['tax']))
            $new_line['tax']        = $parent_line['tax'];
        $new_line['option_for']     = $option_for;
        $new_line['deleted']        = false;
        $new_line['proids']         = $parent_line['products_id'].'_'.$option_id;

        if(!isset($company_id))
            $company_id='';

        $cal_price = new cal_price;
        $cal_price->arr_product_items = $new_line;
        $cal_price->price_break_from_to = $this->change_sell_price_company($company_id,$new_line['products_id']);
        $cal_price->field_change = '';
        if(isset($option['original_unit_price'])&&$option['original_unit_price']!=$option['unit_price'])
        	$cal_price->field_change = 'sell_price';
        $cal_price->cal_price_items();
        $new_line = array_merge((array)$new_line,(array)$cal_price->arr_product_items);

        //neu la same_parent thi thay gia cua parent va tinh lai gia
        if($new_line['same_parent']==1){
            $cal_price = new cal_price;
            $cal_price->arr_product_items = $new_line;
            $cal_price->price_break_from_to = $this->change_sell_price_company($company_id,$new_line['products_id']);
            $cal_price->field_change = '';
            $cal_price->cal_price_items();
            $new_line['sell_price'] = $cal_price->arr_product_items['sell_price'];
            if(!isset($parent_line['plus_sell_price']))
                $parent_line['plus_sell_price'] = 0;
            $parent_line['sell_price'] += (float)$new_line['sell_price'];
            $parent_line['plus_sell_price'] += (float)$new_line['sell_price'];
            $cal_price2 = new cal_price;
            $cal_price2->arr_product_items = $parent_line;
            $cal_price2->field_change = 'sell_price';
            $cal_price2->cal_price_items();
            $parent_line = $cal_price2->arr_product_items;
            $new_line['sell_price'] = '';
        }
        return $new_line;
    }
    function new_cal_sum($products){
        $count_sub_total = 0;
        $count_amount = 0;
        $count_tax = 0;
        if(!empty($products)){
            foreach($products as $pro_key=>$pro_data){
                if(!$pro_data['deleted']){
                    if(isset($pro_data['option_for'])&&is_array($products[$pro_data['option_for']])
                        && (isset($products[$pro_data['option_for']]['sell_by'])&&$products[$pro_data['option_for']]['sell_by']=='combination'
                            || (isset($pro_data['same_parent'])&&$pro_data['same_parent']>0)
                            )
                        )
                        continue;
                    if(isset($pro_data['option_for'])
                       && isset($products[$pro_data['option_for']])
                       && ($products[$pro_data['option_for']]['deleted'] || isset($products[$pro_data['option_for']]['option_for']) )
                       )
                    	continue;
                    //cộng dồn sub_total
                    if(isset($pro_data['sub_total'])) {
                        $count_sub_total += round((float)$pro_data['sub_total'],2);
                    }
                    //cộng dồn amount
                    if(isset($pro_data['amount']))
                        $count_amount += round((float)$pro_data['amount'],2);
                }
            }
            //tính lại sum tax
            $count_tax = $count_amount - $count_sub_total;
        }
        return array('sum_amount'=>$count_amount,'sum_sub_total'=>$count_sub_total,'sum_tax'=>$count_tax);
    }
    function save_new_line($product_id='',$option_id='',$option_for=''){
        if(isset($_POST['product_id']))
            $product_id = $_POST['product_id'];
        if(isset($_POST['option_id']))
            $option_id = $_POST['option_id'];
        if(isset($_POST['option_for']))
            $option_for = $_POST['option_for'];

        $ids = $this->get_id();
        if($ids!=''){
            $arr_insert = $line_entry = $parent_line = array();
            //lay products hien co
            $query = $this->opm->select_one(array('_id'=>new MongoId($ids)),array('products','company_id','options'));
            if(isset($query['products']) && is_array($query['products']) && !empty($query['products'])){
                $line_entry = $query['products'];
                $key = count($line_entry);
            }

            //khởi tạo line entry mới
            $option_line_data = $this->option_list_data($product_id,$option_for);

            $options_data = $option_line_data['option'];
            if(isset($options_data[$option_id])){
                $vv = $options_data[$option_id];
                $product = array();
                if(isset($vv['product_id'])&&is_object($vv['product_id'])){
                    $this->selectModel('Product');
                    $product = $this->Product->select_one(array('_id'=> new MongoId($vv['product_id'])),array('sizew','sizew_unit','sizeh','sizeh_unit'));
                }
                if(isset($line_entry[$option_for]))
                    $parent_line = $line_entry[$option_for];
                $vv['unit_price'] = (isset($vv['unit_price']) ? (float)$vv['unit_price'] : 0);
                $new_line = array();
                $new_line['code']           = $vv['code'];
                $new_line['sku']            = (isset($vv['sku']) ? $vv['sku'] : '');
                $new_line['products_name']  = $vv['product_name'];
                $new_line['products_id']    = $vv['product_id'];
                $new_line['quantity']       = $vv['quantity'];
                $new_line['sub_total']      = isset($vv['sub_total']) ? $vv['sub_total'] : 0;
                $new_line['sizew']          = isset($product['sizew']) ? $product['sizew'] : 12;
                $new_line['sizew_unit']     = isset($product['sizew_unit'])?$parent_line['sizew_unit']:'unit';
                $new_line['sizeh']          = isset($product['sizeh']) ? $product['sizeh'] : 12;
                $new_line['sizeh_unit']     = isset($product['sizeh_unit'])?$product['sizeh_unit']:'unit';
                $new_line['sell_by']        = (isset($vv['sell_by']) ? $vv['sell_by'] : 'unit');
                $new_line['oum']            = $vv['oum'];
                $new_line['same_parent']    = isset($vv['same_parent']) ? (int)$vv['same_parent'] : 0;
                $new_line['sell_price']     = (float)$vv['unit_price'] - (float)$vv['unit_price']*((float)$vv['discount']/100);

                if(isset($query['products'][$option_for]['taxper']))
                    $new_line['taxper']     = $query['products'][$option_for]['taxper'];
                if(isset($query['products'][$option_for]['tax']))
                    $new_line['tax']        = $query['products'][$option_for]['tax'];
                $new_line['option_for']     = $option_for;
                $new_line['deleted']        = false;
                $new_line['proids']         = $product_id.'_'.$option_id;
                if(!isset($query['company_id']))
                    $query['company_id']='';

                $cal_price = new cal_price;
                $cal_price->arr_product_items = $new_line;
                $cal_price->price_break_from_to = $this->change_sell_price_company($query['company_id'],$new_line['products_id']);
                $cal_price->field_change = 'sell_price';
                $cal_price->cal_price_items();
                $new_line = array_merge((array)$new_line,(array)$cal_price->arr_product_items);


                $line_entry[] = $new_line;
                //Update lại option
                $arr_insert['options'] = $query['options'];
                $arr_insert['options'][$option_id]['unit_price'] = $new_line['unit_price'];
                $arr_insert['options'][$option_id]['sell_by'] = $new_line['sell_by'];
                $arr_insert['options'][$option_id]['oum'] = $new_line['oum'];
                $arr_insert['options'][$option_id]['quantity'] = $new_line['quantity'];
                $arr_insert['options'][$option_id]['sell_price'] = $new_line['sell_price'];
                $arr_insert['options'][$option_id]['amount'] = $new_line['amount'];
                $arr_insert['options'][$option_id]['sub_total'] = $new_line['sub_total'];
                $arr_insert['options'][$option_id]['line_no'] = $key;
                $arr_insert['options'][$option_id]['this_line_no'] = $option_id;

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
                //save lai
                $arr_insert['products'] = $line_entry;
                $arr_insert['_id'] = new MongoId($ids);

                if($this->opm->save($arr_insert)){
                    $this->cal_price_line(array('data'=>array('id'=>$key),'fieldchange'=>'products_name'));
                    echo 'ok';
                }else{
                    echo $this->opm->arr_errors_save[1];
                }
            }
        }die;
    }
    function get_minimum_order($modelNameOrCompanyId='',$id=''){
    	$original_minimum = Cache::read('minimum');
    	$product = Cache::read('minimum_product');
    	if(!$original_minimum){
	    	$this->selectModel('Stuffs');
	    	$product = $this->Stuffs->select_one(array('value'=>"Minimum Order Adjustment"),array('product_id'));
	    	$this->selectModel('Product');
    		$p = $this->Product->select_one(array('_id'=> new MongoId($product['product_id'])),array('sell_price'));
    		if(isset($p['sell_price']))
    			$original_minimum = (float)$p['sell_price'];
	    	Cache::write('minimum',$original_minimum);
        	Cache::write('minimum_product',$product);
    	}
    	if( is_object($modelNameOrCompanyId) ) {
    		$query['company_id'] = $modelNameOrCompanyId;
    	} else {
    		if($modelNameOrCompanyId=='')
    			$query = $this->opm->select_one(array('_id'=> new MongoId($this->get_id())),array('company_id'));
    		else{
    			$this->selectModel($modelNameOrCompanyId);
    			$query = $this->$modelNameOrCompanyId->select_one(array('_id'=> new MongoId($id)),array('company_id'));
    		}
    	}
		$arr_companies = Cache::read('arr_companies');
    	if(!$arr_companies)
        	$arr_companies = array();
        if(!isset($arr_companies[(string)$query['company_id']])){
        	if(isset($query['company_id'])&&is_object($query['company_id'])){
				$this->selectModel('Company');
				$company = $this->Company->select_one(array('_id'=>new MongoId($query['company_id'])),array('pricing'));
				if(isset($company['pricing'])&&!empty($company['pricing'])){
					foreach($company['pricing'] as $pricing){
						if(isset($pricing['deleted'])&&$pricing['deleted']) continue;
						if((string)$pricing['product_id']!=(string)$product['product_id']) continue;
						if(!isset($pricing['price_break']) || empty($pricing['price_break'])) continue;
						$price_break = reset($pricing['price_break']);
						$original_minimum = (float)$price_break['unit_price'];
						$arr_companies[(string)$query['company_id']] = $original_minimum;
        				Cache::write('arr_companies',$arr_companies);
        				return $original_minimum;
					}
				}
			}
        } else
        	$original_minimum = $arr_companies[(string)$query['company_id']];
    	return $original_minimum;
    }
    function append_build_salesorder($salesinvoice_id,$salesorder){
		$this->selectModel('Salesinvoice');
		$this->selectModel('Salesorder');
		$shipping_cost = 0;
		$salesinvoice = $this->Salesinvoice->select_one(array('_id'=> new MongoId($salesinvoice_id)));
		if(isset($salesinvoice['salesorder_id']) && is_object($salesinvoice['salesorder_id'])){
			$first_salesorder = $this->Salesorder->select_one(array('_id'=> new MongoId($salesinvoice['salesorder_id'])),array('options','products','sum_tax','sum_sub_total','sum_amount','taxval', 'shipping_cost'));
			if(!isset($first_salesorder['options']))
				$first_salesorder['options'] = array();
			$last_insert = array('sub_total'=>0,'amount'=>0,'tax'=>0);
			$salesinvoice['options'] = $first_salesorder['options'];
			$salesinvoice['products'] = $first_salesorder['products'];
			$minimum = $this->get_minimum_order('Salesorder',$first_salesorder['_id']);
			$shipping_cost += isset($first_salesorder['shipping_cost']) ? $first_salesorder['shipping_cost'] : 0;
			$sub_total = $tax = $amount = 0;
    		if($first_salesorder['sum_sub_total']<$minimum){
    			$more_sub_total = $minimum - (float)$first_salesorder['sum_sub_total'];
    			$sub_total = $more_sub_total;
                $tax = $sub_total*(float)$first_salesorder['taxval']/100;
                $amount = $sub_total+$tax;
    		}
    		$last_insert['sub_total'] += $sub_total;
    		$last_insert['amount'] += $amount;
    		$last_insert['tax'] += $tax;
			$options_num = count($salesinvoice['options']);
			$products_num = count($salesinvoice['products']);
			$existed = false;
			if(isset($salesinvoice['salesorders'])&&!empty($salesinvoice['salesorders'])){
				foreach($salesinvoice['salesorders'] as $key=>$so_id){
					if($key==0) continue;
					if(!is_object($so_id)) continue;
					if((string)$so_id==(string)$salesorder['_id'])
						$existed = true;
					$so = $this->Salesorder->select_one(array('_id'=> new MongoId($so_id)),array('options','products','sum_tax','sum_sub_total','sum_amount','taxval', 'shipping_cost'));
					$arr_data = $this->build_salesorder($so,$products_num,$options_num);
					$salesinvoice['options'] = array_merge($salesinvoice['options'],$arr_data['options']);
					$salesinvoice['products'] = array_merge($salesinvoice['products'],$arr_data['products']);
					$minimum = $this->get_minimum_order('Salesorder',$so['_id']);
					$sub_total = $tax = $amount = 0;
					$shipping_cost += isset($so['shipping_cost']) ? $so['shipping_cost'] : 0;
		    		if($so['sum_sub_total']<$minimum){
		    			$more_sub_total = $minimum - (float)$so['sum_sub_total'];
		    			$sub_total = $more_sub_total;
		                $tax = $sub_total*(float)$so['taxval']/100;
		                $amount = $sub_total+$tax;
		    		}
		    		$last_insert['sub_total'] += $sub_total;
		    		$last_insert['amount'] += $amount;
		    		$last_insert['tax'] += $tax;
				}
			}
			if(!$existed){
				$arr_data = $this->build_salesorder($salesorder,$products_num,$options_num);
				$salesinvoice['options'] = array_merge($salesinvoice['options'],$arr_data['options']);
				$salesinvoice['products'] = array_merge($salesinvoice['products'],$arr_data['products']);
				$minimum = $this->get_minimum_order('Salesorder',$salesinvoice['_id']);
				$sub_total = $tax = $amount = 0;
	    		if($salesorder['sum_sub_total']<$minimum){
	    			$more_sub_total = $minimum - (float)$salesorder['sum_sub_total'];
	    			$sub_total = $more_sub_total;
	                $tax = $sub_total*(float)$salesorder['taxval']/100;
	                $amount = $sub_total+$tax;
	    		}
	    		$last_insert['sub_total'] += $sub_total;
	    		$last_insert['amount'] += $amount;
	    		$last_insert['tax'] += $tax;
			}
			//Last insert=======================================================
            if($last_insert['sub_total']>0){
            	$last_insert = array_merge(end($salesinvoice['products']),$last_insert);
				if(isset($last_insert['option_for']))
					unset($last_insert['option_for']);
				foreach($last_insert as $key=>$value){
	                if($key=='deleted' || $key=='sub_total' || $key=='amount' || $key=='tax') continue;
	                $last_insert[$key] = '';
	                $last_insert['xlock'][$key] = 1;
	            }
	            $last_insert['products_name'] = 'Minimum Order Adjustment';
	            $last_insert['xlock']['products_name'] = '1';
	            $last_insert['xlock']['unit_price'] = '1';
	            $last_insert['xlock']['quantity'] = '1';
	            foreach(array('sku','products_id','details','option','sizew','sizew_unit','sizeh','sizeh_unit','receipts','view_costing','sell_by','sell_price','adj_qty','oum','custom_unit_price','unit_price') as $value)
	                $last_insert['xempty'][$value] = '1';
	            $last_insert['sku_disable'] = 1;
	            $last_insert['_id'] = 'Extra_Row';
	            $last_insert['quantity'] = '1';
				$salesinvoice['products'][] = $last_insert;
			}
            //==================================================================
			$arr_sum = $this->new_cal_sum($salesinvoice['products']);
			$salesinvoice = array_merge($salesinvoice,$arr_sum);
			if(!$existed)
				$salesinvoice['salesorders'][] = new MongoId($salesorder['_id']);
			$this->Salesinvoice->save($salesinvoice);
			return true;
		}
		return false;
    }
    function build_salesorder($salesorder,&$products_num,&$options_num){
		$arr_data = array('products'=>array(),'options'=>array());
		if(!isset($salesorder['options']))
			$salesorder['options'] = array();
		if(!isset($salesorder['products']))
			$salesorder['products'] = array();
		foreach($salesorder['products'] as $product_key => $product_value){
			if(isset($product_value['deleted'])&&$product_value['deleted']) continue;
			$product_value['salesorder_id'] = new MongoId($salesorder['_id']);
			if(!isset($product_value['option_for']) || $product_value['option_for']==''){
				$arr_data['products'][$products_num] = $salesorder['products'][$product_key];
				foreach($salesorder['options'] as $option_key => $option_value){
					if(isset($option_value['deleted'])&&$option_value['deleted']) continue;
					$salesorder['options'][$option_key]['salesorder_id'] = new MongoId($salesorder['_id']);
					if(isset($option_value['line_no'])&&$option_value['line_no']==$product_key
					   &&!isset($option_value['line_no_changed'])){
						$salesorder['options'][$option_key]['line_no'] = $products_num;
						$salesorder['options'][$option_key]['line_no_changed'] = true;
					}
					if(isset($option_value['parent_line_no'])&&$option_value['parent_line_no']==$product_key
					   &&!isset($option_value['parent_line_no_changed'])){
						$salesorder['options'][$option_key]['parent_line_no'] = $products_num;
						$salesorder['options'][$option_key]['parent_line_no_changed'] = true;
					}
				}
				foreach($salesorder['products'] as $child_key => $child_value){
					if(isset($child_value['deleted'])&&$child_value['deleted']) continue;
					if(isset($child_value['option_for'])&&$child_value['option_for']==$product_key
					   &&!isset($child_value['option_for_changed'])){
						$salesorder['products'][$child_key]['option_for'] = $products_num;
						$salesorder['products'][$child_key]['option_for_changed'] = true;
					}

				}
			} else {
				$arr_data['products'][$products_num] = $salesorder['products'][$product_key];
				foreach($salesorder['options'] as $option_key => $option_value){
					if(isset($option_value['deleted'])&&$option_value['deleted']) continue;
					$salesorder['options'][$option_key]['salesorder_id'] = new MongoId($salesorder['_id']);
					if(isset($option_value['line_no'])&&$option_value['line_no']==$product_key
					   &&!isset($option_value['line_no_changed'])){
						$salesorder['options'][$option_key]['line_no'] = $products_num;
						$salesorder['options'][$option_key]['line_no_changed'] = true;
					}
				}
			}
			$products_num++;
		}
		foreach($salesorder['options'] as $option_key => $option_value){
			if(isset($option_value['deleted'])&&$option_value['deleted']) continue;
			$option_value['this_line_no'] = $options_num;
			$arr_data['options'][$options_num] = $option_value;
			$options_num++;
		}
		return $arr_data;
	}


	//Back code to data
	public function revert_so_inv(){
		$md = $this->modelName.'bk';

		$this->selectModel($md);
		$qry = $this->$md->select_all(array(
			'arr_where' => array(),
			'arr_field' => array(),
			'arr_order' => array()
		));
		$m=0;$arr_query = array();
		foreach($qry as $key=>$value){
			$arr_query[] = $value['_id'];
			$this->opm->revert_so_inv($value['_id'],$value['code']);
			echo $value['code']."--".$key."</br>";
			$m++;
		}
		echo $m;
		die;
	}
	function check_company_contact(){
		$this->selectModel('Contact');
		$this->selectModel('Company');
		$query = $this->opm->select_all(array('arr_field'=>array('contact_id','company_id'),'limit'=>9999));
		echo $query->count().'<br />';
		$i = $m = 0;
		foreach($query as $value){
			$i++;
			$company_id = $value['company_id'];
			$arr_data = array('_id'=>new MongoId($value['_id']));
			if(!isset($value['contact_id']) || !is_object($value['contact_id']))
				$value['contact_id'] = '';
			if(!isset($value['company_id']) || !is_object($value['company_id']))
				$value['company_id'] = '';
			if(is_object($value['contact_id'])){
				$contact = $this->Contact->select_one(array('_id'=>new MongoId($value['contact_id'])),array('_id'));
				if(!isset($contact['_id']))
					$value['contact_id'] = '';
			}
			if(is_object($value['company_id'])){
				$company = $this->Company->select_one(array('_id'=>new MongoId($value['company_id'])),array('_id'));
				if(!isset($company['_id']))
					$value['company_id'] = '';
			}
			if($company_id != $value['company_id'])
				$m++;
			$arr_data['contact_id'] = $value['contact_id'];
			$arr_data['company_id'] = $value['company_id'];
			//$this->opm->rebuild_collection($arr_data);
		}
		echo 'Xong - '.$m.'/'.$i;
		die;
	}


	//Rebuild heading : QT,SO,INV
	public function rebuild_heading(){
		$this->selectModel('Company');
		$this->selectModel('Task');
		$m=0;

		//find result
		$query = $this->opm->select_all(array(
 			'arr_where'=>array(
							'name'=>array('$exists'=>true,'$ne'=>''),
							'heading'=>array('$in'=>array(null,''))
						)
		));

		foreach($query as $id=>$arr){
			$arr_set = array();
			//Rebuil name = code + company_name
			$name = $arr['code'];
			if(isset($arr['company_id']) && (string)$arr['company_id']!=''){
				$company = $this->Company->collection->findOne(array('_id'=>$arr['company_id']), array('name'));
				$name .= '-'.$company['name'];
			}
			//Set lai heading neu name custom
			if($arr['name']!=$name){
				$arr_set['heading'] = $arr['name'];
			}
			//name = new name
			$arr_set['name'] = $name;
			//Run change: update
			$this->opm->collection->update(
					array('_id'=>$arr['_id']),
					array( '$set' => $arr_set)
			);
			//Run change: update task
			/*if($this->name =='Salesorder'){
				$company = $this->Company->collection->findOne(array('_id'=>$arr['company_id']), array('name'));
				$this->Task->collection->update(
					array('_id'=>$arr['_id']),
					array( '$set' => $arr_set)
				);
			}*/

			echo $id.'---'.$arr['name'].'---'.$name.'<br />';
			$m++;
		}
		echo $m;die;
	}


	//Rebuild heading : QT,SO,INV,Task
	public function rebuild_heading_task(){
		$this->selectModel('Task');
		$this->selectModel('Salesorder');
		$this->selectModel('Company');
		$m=$a=0;
		//find result
		$query = $this->Task->select_all(array(
 			'arr_where'=>array(
							'salesorder_id'=>array('$exists'=>true,'$ne'=>'')
						)
		));

		foreach($query as $id=>$arr){
			$arr_set = array();
			//Rebuil name = code + company_name
			if(isset($arr['salesorder_id']) && is_object($arr['salesorder_id'])){
				$salesorder = $this->Salesorder->collection->findOne(array('_id'=>$arr['salesorder_id']), array('code','company_id'));
				$name = $salesorder['code'];

				if(isset($salesorder['company_id'])){
					$company = $this->Company->collection->findOne(array('_id'=>$salesorder['company_id']), array('name'));
					$name .= '-'.$company['name'];
				}
				if(isset($arr['our_rep_type']) && $arr['our_rep_type']=='assets')
					$name .= '-'.$arr['our_rep'];

				$arr_set['name_custom'] = $arr['name'];
				$arr_set['name'] = $name;



				//Run change: update
				$this->Task->collection->update(
						array('_id'=>$arr['_id']),
						array( '$set' => $arr_set)
				);
				echo $name.'<br />'; $a++;
			}

			$m++;
		}
		echo $a.'/'.$m;die;
	}

	//Rebuild heading : QT,SO,INV,Task
	public function revert_rebuild_heading_task(){
		$this->selectModel('Task');
		//find result
		$query = $this->Task->select_all(array(
 			'arr_where'=>array(
							'salesorder_id'=>array('$exists'=>true,'$ne'=>'')
						)
		));
		$m=$a=0;
		foreach($query as $id=>$arr){
			$arr_set = array();
			if(isset($arr['name_custom'])){
				$arr_set['name'] = $arr['name_custom'];
				//Run change: update
				$this->Task->collection->update(
						array('_id'=>$arr['_id']),
						array( '$set' => $arr_set)
				);
				echo $arr_set['name'].'<br />';
				$m++;
			}
		}
		echo $m;die;
	}


	public function change_theme($theme='blue'){
		$_SESSION['theme'] = $theme;
		$this->redirect(URL);
	}

	function test_cal_sum(){
		$query = $this->opm->select_one(array('_id'=>new MongoId($this->get_id())),array('products'));
		$products = $query['products'];
		$count_sub_total = 0;
        $count_amount = 0;
        $count_tax = 0;
        if(!empty($products)){
            foreach($products as $pro_key=>$pro_data){
                if(!$pro_data['deleted']){
                    if(isset($pro_data['option_for'])&&is_array($products[$pro_data['option_for']])
                        && (isset($products[$pro_data['option_for']]['sell_by'])&&$products[$pro_data['option_for']]['sell_by']=='combination'
                            || (isset($pro_data['same_parent'])&&$pro_data['same_parent']>0)
                            )
                        )
                        continue;
                    if(isset($pro_data['option_for'])
                       && isset($products[$pro_data['option_for']])
                       && ($products[$pro_data['option_for']]['deleted'] || isset($products[$pro_data['option_for']]['option_for']))
                       )
                    	continue;
                    //cộng dồn sub_total
                    if(isset($pro_data['sub_total']))
                        $count_sub_total += round((float)$pro_data['sub_total'],2);
                    //cộng dồn amount
                    if(isset($pro_data['amount']))
                        $count_amount += round((float)$pro_data['amount'],2);
                    echo "{$pro_data['sub_total']} -  $pro_key <br/ >";
                }
            }
            //tính lại sum tax
            $count_tax = $count_amount - $count_sub_total;
        }
        echo '<pre>';
        print_r(array('sum_amount'=>$count_amount,'sum_sub_total'=>$count_sub_total,'sum_tax'=>$count_tax));
        echo '</pre>';
        die;
        return array('sum_amount'=>$count_amount,'sum_sub_total'=>$count_sub_total,'sum_tax'=>$count_tax);
	}


	//Rebuild heading : QT,SO,INV,Task
	public function rebuild_payment_due_date(){
		$this->selectModel('Salesinvoice');
		//find result
		$query = $this->Salesinvoice->select_all(array(
 			'arr_where'=>array(),
			'arr_field'=>array('code','invoice_date','payment_terms')
		));
		$m=$a=0;
		foreach($query as $id=>$arr){
			$arr_set = array();
			if(isset($arr['invoice_date'])){
				if(empty($arr['payment_terms']))
					$arr['payment_terms'] = 0;

				$arr_set['payment_due_date'] = (int)$arr['payment_terms']*86400 + (int)$arr['invoice_date']->sec;
				$arr_set['payment_due_date'] = new MongoDate($arr_set['payment_due_date']);
				$arr_set['payment_terms'] = $arr_set['payment_terms_id'] = $arr['payment_terms'];
				//Run change: update
				$this->Salesinvoice->collection->update(
						array('_id'=>$arr['_id']),
						array( '$set' => $arr_set)
				);
				echo $arr['code'].'====='.$arr['payment_terms'].'<br />';
				$m++;
			}
		}
		echo $m;die;
	}
	function rebuild_index(){
		$arr_models = array('Quotation','Salesinvoice','Salesorder');
		foreach($arr_models as $model)
			$this->selectModel($model);
		$Quotation = array('job_id','company_id','salesorder_id');
		$Salesinvoice = array('job_id','company_id','salesorder_id');
		$Salesorder = array('job_id','company_id','quotation_id');
		foreach($arr_models as $model){
			$arr_where = array();
			foreach($$model as $condition)
				$arr_where['$or'][][$condition] = array('$exists' => false);
			$query = $this->$model->select_all(array(
			                                   'arr_where' => $arr_where,
			                                   'arr_field' => $$model,
			                                   'limit'		=> 9999
			                                   ));
			$count = $query->count();
			if($count){
				echo '<br />'.$model.': '.$count.'<br />';
				$i = 0;
				foreach($query as $value){
					$arr_data = array('_id' => $value['_id']);
					foreach($$model as $index){
						if(!isset($value[$index]))
							$arr_data[$index]  = '';
					}
					if(count($arr_data)>1){
						$this->opm->rebuild_collection($arr_data);
						$i++;
					}

				}
				echo 'Done: '.$i;
			}
		}
		die;
	}
	function build_province(){
		$this->selectModel('Company');
		$arr_companies = $this->Company->select_all(array(
		                           'arr_field' => array('addresses'),
		                           'limit' => 99999999,
		                           ));
		echo $arr_companies->count().'<br />';
		$i = 0;
		$arr_province = $this->province('CA');
		$arr_r_province = array_flip($arr_province);
		foreach($arr_companies as $company){
			if(!isset($company['addresses']) || empty($company['addresses'])) continue;
			$arr_data = array( '_id'=>new MongoId($company['_id']) );
			foreach($company['addresses'] as $addresses_key=>$addresses){
				if(isset($addresses['deleted']) && $addresses['deleted']){
					$company['addresses'][$addresses_key] = array('deleted'=>true);
					continue;
				}

				if( isset($addresses['province_state']) ) {
					if( in_array($addresses['province_state'], array_keys($arr_province)) ){
						$addresses['province_state_id'] = $addresses['province_state'];
						$addresses['province_state'] = $arr_province[$addresses['province_state_id']];
					} else if( in_array($addresses['province_state'], $arr_province) )
						$addresses['province_state_id'] = $arr_r_province[$addresses['province_state']];
				} else if( isset($addresses['province_state_id']) && in_array($addresses['province_state_id'], array_keys($arr_province)) )
					$addresses['province_state'] = $arr_province[$addresses['province_state_id']];
				$company['addresses'][$addresses_key] = $addresses;
			}
			$arr_data['addresses'] = $company['addresses'];
			$this->Company->rebuild_collection($arr_data);
			$i ++;
		}
		echo $i;
		die;
	}

    public function commission_summary(){
    	if(!in_array($this->modelName, array('Quotation','Salesorder','Salesinvoice')))
    		$this->redirect(URL);
	    if(!isset($_GET['print_pdf'])){
	    	if($this->modelName=='Quotation')
    			$arr_order = array('quotation_date'=>1);
    		else if($this->modelName=='Salesorder')
    			$arr_order = array('salesorder_date'=>1);
    		else if($this->modelName=='Salesinvoice')
    			$arr_order = array('invoice_date'=>1);
	    	$query = $this->opm->select_all(array(
	    	                               	'arr_order' => $arr_order,
	    	                               	'arr_where' => array(
	    	                               		key($arr_order) => array('$exists' => true, '$nin' => array(null,''))
	    	                               		),
	    	                               	'arr_field'	=> array('commission','company_id','sum_sub_total','quotation_status','status','invoice_status','payment_due_date','invoice_date','quotation_date','salesorder_date'),
	    	                               	));
	    	$arr_commission = array();

	    	$origin_minimum = 50;
	    	$this->selectModel('Stuffs');
	    	$product = $this->Stuffs->select_one(array('value'=>"Minimum Order Adjustment"),array('product_id'));
	    	$this->selectModel('Product');
			$product = $this->Product->select_one(array('_id'=> new MongoId($product['product_id'])),array('sell_price'));
			if(isset($product['sell_price']))
				$origin_minimum = (float)$product['sell_price'];
	    	$this->selectModel('Company');
	    	$date_from = false;
	    	//==========

	    	foreach ($query as $commission) {
	    		if(!$date_from && is_object($commission[key($arr_order)]))
	    			$date_from = $this->opm->format_date($commission[key($arr_order)]->sec);
	    		$date_to = $commission[key($arr_order)]->sec;
	    		if(!isset($commission['commission']))
	    			$commission['commission'] = array();
	    		$data = $commission;
	    		$sum_sub_total = isset($commission['sum_sub_total']) ? $commission['sum_sub_total'] : 0;
	    		$commission = isset($commission['commission']) ? $commission['commission'] : 0;
	    		$contact_id = isset($commission['contact_id']) ? (string)$commission['contact_id'] : '';
	    		$contact_name = isset($commission['contact_name']) ? $commission['contact_name'] : 'Name not spectified';
	    		$profit = isset($commission['profit']) ? $commission['profit'] : 0;
	    		$rate = isset($commission['rate']) ? $commission['rate'] : 0;
	    		$commission_amount = isset($commission['commission_amount']) ? $commission['commission_amount'] : 0;
	    		$base_on = isset($commission['base_on'])&&$commission['base_on']=='sale_amt' ? 'sale_amt' : 'profit';
	    		$sales_cost = isset($commission['sales_cost']) ? $commission['sales_cost'] : 0;
	    		$paid = isset($commission['paid']) ? $commission['paid'] : '';
	    		$base_on = isset($commission['base_on']) ? $commission['base_on'] : '';
	    		$minimum = $origin_minimum;
	    		if(!isset($commission['sales_amount'])){
		    		if(isset($commission['company_id']) && is_object($commission['company_id'])){
		    			$company = $this->Company->select_one(array('_id'=>new MongoId($query['company_id'])),array('pricing'));
		    			if(isset($company['pricing'])&&!empty($company['pricing'])){
		    				foreach($company['pricing'] as $pricing){
		    					if(isset($pricing['deleted'])&&$pricing['deleted']) continue;
		    					if((string)$pricing['product_id']!=(string)$product['_id']) continue;
		    					if(!isset($pricing['price_break']) || empty($pricing['price_break'])) continue;
		    					$price_break = reset($pricing['price_break']);
		    					$minimum = (float)$price_break['unit_price'];
		    					break;
		    				}
		    			}
		    		}
	    		}
	   			if($sum_sub_total < $minimum)
	    			$sum_sub_total = $minimum;
	    		$sales_amount = (isset($commission['sales_amount']) ? $commission['sales_amount'] : $sum_sub_total);
	    		$arr_commission[$contact_id]['contact_name'] = $contact_name;
	    		if(!isset($arr_commission[$contact_id]['sales_amount']))
	    			$arr_commission[$contact_id]['sales_amount'] = 0;
	    		$arr_commission[$contact_id]['sales_amount'] += $sales_amount;
	    		if(!isset($arr_commission[$contact_id]['sales_cost']))
	    			$arr_commission[$contact_id]['sales_cost'] = 0;
	    		$arr_commission[$contact_id]['sales_cost'] += $sales_cost;
	    		if(!isset($arr_commission[$contact_id]['profit']))
	    			$arr_commission[$contact_id]['profit'] = 0;
	    		$arr_commission[$contact_id]['profit'] += $profit;
	    		if(!isset($arr_commission[$contact_id]['commission_amount']))
	    			$arr_commission[$contact_id]['commission_amount'] = 0;
	    		$arr_commission[$contact_id]['commission_amount'] += $commission_amount;
	    		if(!isset($arr_commission[$contact_id]['outstanding']))
	    			$arr_commission[$contact_id]['outstanding'] = 0;
	    		if($base_on == 'profit')
	    			$outstanding = ($sales_amount - $sales_cost) * $rate / 100;
	    		else
	    			$outstanding = $sales_amount * $rate / 100;
	    		if($this->modelName=='Quotation'){
	    			if(in_array($data['quotation_status'], array('In progress','Submitted','Amended')))
	    				$arr_commission[$contact_id]['outstanding'] += $outstanding;
	    		} else if($this->modelName=='Salesorder'){
	    			if($data['status']!='Completed')
	    				$arr_commission[$contact_id]['outstanding'] += $outstanding;
	    		} else if($this->modelName=='Salesinvoice' && is_object($data['payment_due_date'])){
	    			if($data['payment_due_date']->sec >  strtotime(date('Y-m-d')))
	    				$arr_commission[$contact_id]['outstanding'] += $outstanding;
	    		}
	    	}
	    	$date_to = $this->opm->format_date($date_to);
	    	$html = '
		            <table class="table_content">
		               <tbody>
		                  <tr class="tr_right_none" style="background: #911b12;color: white;height: 29px;line-height: 29px;font-weight: bold;">
		                     <td width="25%" colspan="2" >
		                        Contact name
		                     </td>
		                     <td width="13%" class="right_text">
		                        Sales amount
		                     </td>
		                     <td width="13%" class="right_text">
		                        Cost of sales
		                     </td>
		                     <td width="13%" class="right_text">
		                        Commissionable
		                     </td>
		                     <td width="13%" class="right_text">
		                        Commission
		                     </td>
		                     <td width="13%" class="right_text">
		                        outstanding
		                     </td>
		                  </tr>';
		    $i = $total_sales_amount = $total_sales_cost = $total_profit = $total_commission_amount = $total_outstanding = 0;
		    foreach($arr_commission as $commission){
		    	$total_sales_amount += $commission['sales_amount'];
		    	$total_sales_cost += $commission['sales_cost'];
		    	$total_profit += $commission['profit'];
		    	$total_commission_amount += $commission['commission_amount'];
		    	$total_outstanding += $commission['outstanding'];
		    	$html .= '<tr class="content_asset bg_' . ($i % 2 == 0 ? '1' : '2') . '">
		    				<td colspan="2">'.$commission['contact_name'].'</td>
		    				<td class="right_text">'.$this->opm->format_currency($commission['sales_amount']).'</td>
		    				<td class="right_text">'.$this->opm->format_currency($commission['sales_cost']).'</td>
		    				<td class="right_text">'.$this->opm->format_currency($commission['profit']).'</td>
		    				<td class="right_text">'.$this->opm->format_currency($commission['commission_amount']).'</td>
		    				<td class="right_text">'.$this->opm->format_currency($commission['outstanding']).'</td>
		    			</tr>';
		    	$i++;
		    }
		    $html .= '<tr class="content_asset bg_' . ($i % 2 == 0 ? '1' : '2') . '">
		    				<td class="bold_text" style="border-right: none;">'.$i.' record(s) listed.</td>
		    				<td class="bold_text right_text">Total:</td>
		    				<td class="right_text">'.$this->opm->format_currency($total_sales_amount).'</td>
		    				<td class="right_text">'.$this->opm->format_currency($total_sales_cost).'</td>
		    				<td class="right_text">'.$this->opm->format_currency($total_profit).'</td>
		    				<td class="right_text">'.$this->opm->format_currency($total_commission_amount).'</td>
		    				<td class="right_text">'.$this->opm->format_currency($total_outstanding).'</td>
		    			</tr>';
		    //================================================================
	        $arr_pdf['date_from_to'] = '<span class="color_red bold_text">From</span> '.$date_from.' <span class="color_red bold_text">To</span> '.$date_to;
	        $arr_pdf['content'][]['html'] = $html;
	        $arr_pdf['is_custom'] = true;
	        $arr_pdf['image_logo'] = true;
	        $modelName = $this->modelName;
	        if($this->modelName ==  'Salesinvoice'){
	        	$modelName = $short_name = 'SI';
	        } else if($this->modelName == 'Salesorder'){
	        	$modelName = $short_name = 'SO';
	        } else
	        	$short_name = 'QT';
	        $arr_pdf['report_name'] = $modelName.' Commission (summary)';
	        $arr_pdf['report_file_name'] = $short_name.'_'.md5(time());
	        Cache::write('commission_summary_'.strtolower($this->modelName),$arr_pdf);
        } else
	    	$arr_pdf = Cache::read('commission_summary_'.strtolower($this->modelName));
        $this->render_pdf($arr_pdf);
    }
    public function commission_detailed(){
    	if(!in_array($this->modelName, array('Quotation','Salesorder','Salesinvoice')))
    		$this->redirect(URL);
	    if(!isset($_GET['print_pdf'])){
	    	if($this->modelName=='Quotation')
    			$arr_order = array('quotation_date'=>1);
    		else if($this->modelName=='Salesorder')
    			$arr_order = array('salesorder_date'=>1);
    		else if($this->modelName=='Salesinvoice')
    			$arr_order = array('invoice_date'=>1);
	    	$query = $this->opm->select_all(array(
	    	                               	'arr_where' => array(
	    	                               			key($arr_order) => array(
	    	                               					'$exists' => true
	    	                               				)
	    	                               		),
	    	                               	'arr_order' => $arr_order,
	    	                               	'arr_field'	=> array('commission','company_id','sum_sub_total','quotation_status','status','invoice_status','payment_due_date','invoice_date','salesorder_date','quotation_date','code','company_name'),
	    	                               	));
	    	$arr_commission = array();
	    	$arr_commission = array();

	    	$origin_minimum = 50;
	    	$this->selectModel('Stuffs');
	    	$product = $this->Stuffs->select_one(array('value'=>"Minimum Order Adjustment"),array('product_id'));
	    	$this->selectModel('Product');
			$product = $this->Product->select_one(array('_id'=> new MongoId($product['product_id'])),array('sell_price'));
			if(isset($product['sell_price']))
				$origin_minimum = (float)$product['sell_price'];
	    	$this->selectModel('Company');
	    	//First date
	    	$query->next();
	    	$first_record = $query->current();
	    	$date_from = $this->opm->format_date($first_record[key($arr_order)]);
	    	//==========
	    	foreach ($query as $commission) {
	    		$date_to = $commission[key($arr_order)]->sec;
	    		if(!isset($commission['commission']))
	    			$commission['commission'] = array();
	    		$data = $commission;
	    		$sum_sub_total = isset($commission['sum_sub_total']) ? $commission['sum_sub_total'] : 0;
	    		$commission = isset($commission['commission']) ? $commission['commission'] : 0;
	    		$contact_id = isset($commission['contact_id']) ? (string)$commission['contact_id'] : '';
	    		$contact_name = isset($commission['contact_name']) ? $commission['contact_name'] : 'Name not spectified';
	    		$profit = isset($commission['profit']) ? $commission['profit'] : 0;
	    		$rate = isset($commission['rate']) ? $commission['rate'] : 0;
	    		$paid = isset($commission['paid'])&&$commission['paid'] == 1 ? 'X' : '';
	    		$commission_amount = isset($commission['commission_amount']) ? $commission['commission_amount'] : 0;
	    		$base_on = isset($commission['base_on'])&&$commission['base_on']=='sale_amt' ? 'sale_amt' : 'profit';
	    		$sales_cost = isset($commission['sales_cost']) ? $commission['sales_cost'] : 0;
	    		$base_on = isset($commission['base_on']) ? $commission['base_on'] : '';
	    		$minimum = $origin_minimum;
	    		if(!isset($commission['sales_amount'])){
		    		if(isset($commission['company_id']) && is_object($commission['company_id'])){
		    			$company = $this->Company->select_one(array('_id'=>new MongoId($query['company_id'])),array('pricing'));
		    			if(isset($company['pricing'])&&!empty($company['pricing'])){
		    				foreach($company['pricing'] as $pricing){
		    					if(isset($pricing['deleted'])&&$pricing['deleted']) continue;
		    					if((string)$pricing['product_id']!=(string)$product['_id']) continue;
		    					if(!isset($pricing['price_break']) || empty($pricing['price_break'])) continue;
		    					$price_break = reset($pricing['price_break']);
		    					$minimum = (float)$price_break['unit_price'];
		    					break;
		    				}
		    			}
		    		}
	    		}
	   			if($sum_sub_total < $minimum)
	    			$sum_sub_total = $minimum;
	    		$sales_amount = (isset($commission['sales_amount']) ? $commission['sales_amount'] : $sum_sub_total);
	    		if($base_on == 'profit')
	    			$outstanding = ($sales_amount - $sales_cost) * $rate / 100;
	    		else
	    			$outstanding = $sales_amount * $rate / 100;
	    		if($this->modelName=='Quotation'){
	    			if(!in_array($data['quotation_status'], array('In progress','Submitted','Amended')))
	    				$outstanding = 0;
	    			$date = $this->opm->format_date($data['quotation_date']->sec);
	    		} else if($this->modelName=='Salesorder'){
	    			if($data['status']=='Completed')
	    				$outstanding = 0;
	    			$date = $this->opm->format_date($data['salesorder_date']->sec);
	    		} else if($this->modelName=='Salesinvoice' && is_object($data['payment_due_date'])){
	    			if($data['payment_due_date']->sec < strtotime(date('Y-m-d')))
	    				$outstanding = 0;
	    			$date = $this->opm->format_date($data['invoice_date']->sec);
	    		}
	    		$arr_commission[$contact_id]['contact_name'] = $contact_name;
	    		$arr_commission[$contact_id]['commissions'][] = array(
	    		                                                      'code' 		=> $data['code'],
	    		                                                      'date' 		=> $date,
	    		                                                      'company_name'=> isset($data['company_name']) ? $data['company_name'] : '',
	    		                                                      'sales_amount' => $sales_amount,
	    		                                                      'rate' => $rate.'%',
	    		                                                      'commission_amount' => $commission_amount,
	    		                                                      'paid' => $paid,
	    		                                                      'sales_cost' => $sales_cost,
	    		                                                      'outstanding' => $outstanding,
	    		                                                      );
	    		if(!isset($arr_commission[$contact_id]['total']['sales_amount']))
	    			$arr_commission[$contact_id]['total']['sales_amount'] = 0;
	    		$arr_commission[$contact_id]['total']['sales_amount'] += $sales_amount;
	    		if(!isset($arr_commission[$contact_id]['total']['commission_amount']))
	    			$arr_commission[$contact_id]['total']['commission_amount'] = 0;
	    		$arr_commission[$contact_id]['total']['commission_amount'] += $commission_amount;
	    		if(!isset($arr_commission[$contact_id]['total']['outstanding']))
	    			$arr_commission[$contact_id]['total']['outstanding'] = 0;
	    		$arr_commission[$contact_id]['total']['outstanding'] += $outstanding;


	    	}
	    	$date_to = $this->opm->format_date($date_to);
	    	$html = '';
	    	foreach($arr_commission as $commission){
	    		$html .= '
		            <table class="table_content">
		               <tbody>
		                  <tr class="tr_right_none" style="background: #911b12;color: white;height: 29px;line-height: 29px;font-weight: bold;">
		                     <td width="30%" >
		                        Contact name
		                     </td>
		                     <td  class="right_text">
		                        Sales amount
		                     </td>
		                     <td class="right_text">
		                        Commission
		                     </td>
		                     <td class="right_text">
		                        Outstanding
		                     </td>
		                  </tr>
		                  <tr class="bg_2">
		                  	<td>'.$commission['contact_name'].'</td>
		                  	<td class="right_text">'.$this->opm->format_currency($commission['total']['sales_amount']).'</td>
		                  	<td class="right_text">'.$this->opm->format_currency($commission['total']['commission_amount']).'</td>
		                  	<td class="right_text">'.$this->opm->format_currency($commission['total']['outstanding']).'</td>
		                  </tr>
		            </tbody>';

		         $html .= '<table class="table_content" >
                        <tbody>
                          <tr class="tr_right_none" style="background: #979797;color: white;height: 29px;line-height: 29px;font-weight: bold;">
                             <td width="10%">
                                Ref no
                             </td>
                             <td width="15%" class="center_text">
                                Date
                             </td>
                             <td width="30%">
                                Company
                             </td>
                             <td width="15%" class="right_text">
                                Sales amount
                             </td>
                             <td width="5%" class="right_text">
                                Rate
                             </td>
                             <td width="15%" class="right_text">
                                Commission
                             </td>
                             <td width="3%" class="center_text">
                                Paid
                             </td>
                             <td width="15%" class="right_text">
                                Outstanding
                             </td>
                          </tr>';
                $i = 0;
                foreach($commission['commissions'] as $value){
                	$html .= '
                      <tr class="content_asset bg_' . ($i % 2 == 0 ? '1' : '2') . '">
                         <td>' . $value['code'] . '</td>
                         <td class="center_text">' . $value['date'] . '</td>
                         <td >' . $value['company_name'] . '</td>
                         <td class="right_text">' . $this->opm->format_currency($value['sales_amount']) . '</td>
                         <td class="right_text">' . $value['rate'] . '</td>
                         <td class="right_text">' . $this->opm->format_currency($value['commission_amount']) . '</td>
                         <td class="center_text bold_text">' . $value['paid'] . '</td>
                         <td class="right_text">' . $this->opm->format_currency($value['outstanding']) . '</td>
                      </tr>';
                    $i++;
                }
                $html .= '
                        <tr class="bg_' . ($i % 2 == 0 ? '1' : '2') . '">
                         <td colspan="2" class="left_text bold_text right_none">' . $i . ' record(s) listed</td>
                         <td class="bold_text right_text right_none">Totals</td>
                         <td class="bold_text right_text right_none">'.$this->opm->format_currency($commission['total']['sales_amount']).'</td>
                         <td class="right_none"></td>
		                 <td class="bold_text right_text right_none">'.$this->opm->format_currency($commission['total']['commission_amount']).'</td>
                         <td class="right_none"></td>
		                 <td class="bold_text right_text">'.$this->opm->format_currency($commission['total']['outstanding']).'</td>
                      </tr>
                    </tbody>
                </table>
                <br />
                <br />';
	    	}
			$arr_pdf['date_from_to'] = '<span class="color_red bold_text">From</span> '.$date_from.' <span class="color_red bold_text">To</span> '.$date_to;
	        $arr_pdf['content'][]['html'] = $html;
	        $arr_pdf['is_custom'] = true;
	        $arr_pdf['image_logo'] = true;
	        $modelName = $this->modelName;
	        if($this->modelName ==  'Salesinvoice'){
	        	$modelName = $short_name = 'SI';
	        } else if($this->modelName == 'Salesorder'){
	        	$modelName = $short_name = 'SO';
	        } else
	        	$short_name = 'QT';
	        $arr_pdf['report_name'] = $modelName.' Commission (detailed)';
	        $arr_pdf['report_file_name'] = $short_name.'_'.md5(time());
	    	Cache::write('commission_detailed_'.strtolower($this->modelName),$arr_pdf);
	    } else {
	    	$arr_pdf = Cache::read('commission_detailed_'.strtolower($this->modelName));
	    	Cache::delete('commission_detailed_'.strtolower($this->modelName));
	    }
        $this->render_pdf($arr_pdf);
    }
    function build_our_rep_our_csr(){
    	$this->selectModel('Contact');
    	$arr_models = array('Salesorder','Quotation','Salesinvoice','Shipping','Purchaseorder');
    	foreach($arr_models as $model){
    		$this->selectModel($model);
    		$query = $this->$model->select_all(array('arr_field'=>array('our_rep_id','our_csr_id'),'limit'=>9999));
			echo $model.' - '.$query->count().' record(s) found.<br />';
			$i = 0;
			foreach($query as $value){
				$arr_data = array('_id'=>$value['_id']);
				if(isset($value['our_rep_id']) && is_object($value['our_rep_id'])){
		    		$our_rep = $this->Contact->select_one(array('_id'=> new MongoId($value['our_rep_id'])),array('first_name','last_name'));
		    		$arr_data['our_rep'] = (isset($our_rep['first_name']) ? $our_rep['first_name'] : '').' '.(isset($our_rep['last_name']) ? $our_rep['last_name'] : '');

		    	}
		    	if(isset($value['our_csr_id']) && is_object($value['our_csr_id'])){
		    		$our_csr = $this->Contact->select_one(array('_id'=> new MongoId($value['our_csr_id'])),array('first_name','last_name'));
		    		$arr_data['our_csr'] = (isset($our_csr['first_name']) ? $our_csr['first_name'] : '').' '.(isset($our_csr['last_name']) ? $our_csr['last_name'] : '');

		    	}
		    	if(count($arr_data)>1){
		    		$i++;
		    		$this->$model->rebuild_collection($arr_data);
		    	}
			}
			echo $i.' Done.<br />';
    	}
    	die;
    }
    function build_payment_term(){
    	$this->selectModel('Salesorder');
    	$this->selectModel('Salesaccount');
    	$arr_company = array();
    	$salesorders = $this->Salesorder->select_all(array(
    	                                                 'arr_where' => array(
    	                                                                      'company_id'=>array('$nin'=>array('',null)),
    	                                                                      'payment_terms'=>0
    	                                                                      ),
    	                                                 'arr_field' => array('company_id'),
    	                                                 'limit'	=> 9999
    	                                                 ));
    	$i =0;
    	foreach($salesorders as $order){
    		if(!is_object($order['company_id'])) continue;
    		$salesaccount = $this->Salesaccount->select_one(array('company_id'=>$order['company_id']),array('payment_terms'));
    		if(!isset($salesaccount['_id'])) continue;
    		$i++;
    		$arr_data = array('_id'=> $order['_id']);
    		$arr_data['payment_terms'] = isset($salesaccount['payment_terms']) ? $salesaccount['payment_terms'] : 0;
    		$this->Salesorder->rebuild_collection($arr_data);
    	}
    	echo '<br />Records need to repair: '.$i;
    	echo '<br />Records repaired: '.$i;
    	die;
    }

    function get_cache_keys_diff($name, $prefix_name) {
    	$settings = Cache::settings();
		$list = array();
    	if(class_exists('Memcache') && $settings['engine'] == 'Memcache'){
    		$memcache = new Memcache;
		    $memcache->connect('127.0.0.1', 11211)
		       or die ("Could not connect to memcache server");
		    $allSlabs = $memcache->getExtendedStats('slabs');
		    $items = $memcache->getExtendedStats('items');
		    foreach($allSlabs as $server => $slabs) {
		        foreach($slabs AS $slabId => $slabMeta) {
		        	if (!is_int($slabId)) continue;
		            $cdump = $memcache->getExtendedStats('cachedump',(int)$slabId,  100000000);
		            foreach($cdump AS $keys => $arrVal) {
		                if (!is_array($arrVal)) continue;
		                foreach($arrVal AS $k => $v){
		                	if(strpos($k, $prefix_name) !== false
		                	   	&& strpos($k, $name) === false)
		                	   	$k = str_replace('app_', '', $k);
		                		$list[] = $k;
		                }
		            }
		        }
		    }
    	} else {
    		$cache_dir = APP.'tmp'.DS.'cache'.DS;
            $old_cache = glob($cache_dir.'cake_'.$prefix_name.'*');
            foreach($old_cache as $cache){
            	$cache = str_replace($cache_dir.'cake_', '', $cache);
                if($cache == $name) continue;
                $list[] = $cache;
            }
    	}
	    return $list;
	}

	function clear_line_cache($id = ''){
		if($id == '')
			$id = $this->get->id();
		$model = strtolower($this->modelName);
		$cache = $this->get_cache_keys_diff('',"line_{$model}_{$id}");
		foreach($cache as $c){
			Cache::delete($c);
		}
		Cache::delete('arr_product_special');
		Cache::delete('arr_product_approved');
		Cache::delete('arr_product_rfq');
		echo 'ok';
		die;
	}

	// Calculator Printing
	public function sheet_yield_calculator($w=0,$h=0,$wr=1,$hr=1,$output=0){
		if(isset($_POST['w']))
			$w=$_POST['w'];
		if(isset($_POST['h']))
			$h=$_POST['h'];
		if(isset($_POST['wr']))
			$wr=$_POST['wr'];
		if(isset($_POST['hr']))
			$hr=$_POST['hr'];
		$w = (float)$w+0.125;
		$h = (float)$h+0.125;
		$wr = (float)$wr+0.125;
		$hr = (float)$hr+0.125;

		//option A : horizontal
		$na['type'] = 'A-horizontal';
		$na['total_yield'] = floor($w/$wr)*floor($h/$hr);
		$na['sheet_u'] = ($wr*$hr*$na['total_yield'])*100/(($w-0.125)*($h-0.125)); // (%)
		$na['offcut_yield_w'] = $w - $wr*floor($w/$wr);
		$na['offcut_yield_h'] = $h - $hr*floor($h/$hr);
		if($na['offcut_yield_w']>$hr && $wr<($h-0.125) && $hr!=0)
			$na['oy_w'] = $na['offcut_yield_w']/$hr;
		else
			$na['oy_w'] = 0;

		if(($h-0.125)>$wr && $wr!=0)
			$na['oy_h'] = ($h-0.125)/$wr;
		else
			$na['oy_h'] = 0;
		$na['offcut_yield'] = floor($na['oy_w'])*floor($na['oy_h']);
		$na['total_yield'] +=$na['offcut_yield'];
		$na['row_col'] = (floor($w/$wr)).'x'.(floor($h/$hr));
		$na['cutting_amount'] =floor($w/$wr)+floor($h/$hr)+2;

		//option B : vertical
		$nb['type'] = 'B-vertical';
		$nb['total_yield'] = floor($w/$hr)*floor($h/$wr);
		$nb['sheet_u'] = ($wr*$hr*$nb['total_yield'])*100/(($w-0.125)*($h-0.125)); // (%)
		$nb['offcut_yield_w'] = $w - $hr*floor($w/$hr);
		$nb['offcut_yield_h'] = $h - $wr*floor($h/$wr);
		if($nb['offcut_yield_h']>$hr && ($w-0.125)>$wr && $wr!=0)
			$nb['oy_w'] = ($w-0.125)/$wr;
		else
			$nb['oy_w'] = 0;

		if($nb['offcut_yield_h']>$hr && ($w-0.125)>$wr && $hr!=0)
			$nb['oy_h'] = $nb['offcut_yield_h']/$hr;
		else
			$nb['oy_h'] = 0;
		$nb['offcut_yield'] = floor($nb['oy_w'])*floor($nb['oy_h']);
		$nb['total_yield'] +=$nb['offcut_yield'];
		$nb['row_col'] = (floor($w/$hr)).'x'.(floor($h/$wr));
		$nb['cutting_amount'] =floor($w/$hr)+floor($h/$wr)+2;

		//choice one
		if($na['total_yield']>$nb['total_yield'])
			$n = $na;
		else
			$n = $nb;

		if(isset($_POST['w']) && $output==0){
			echo json_encode($n); die;
		}else
			return $n;
    }

	public function cal_cutting_line($w=0,$h=0){
		return (($w+1)+($h+1));
	}

	function open_closed_module(){
		if(!empty($_POST)){
			$this->selectModel('Stuffs');
			$change = $this->Stuffs->select_one(array('value'=>'Changing Code'));
			if(md5($_POST['password'])!=$change['password']){
				echo 'wrong_pass';
			}
			else {
				$_id = $this->checkClosingMonth();
                if(!$_id) {
					$this->Session->setFlash('The closing month has not been setup yet.','default',array('class'=>'flash_message'));
                	die;
                }
				$this->Session->write('JobsOpen_'.$_id,1);
			}
		}
		die;
	}

	function check_password(){
        $this->selectModel('Stuffs');
        $change = $this->Stuffs->select_one(array('value' => 'Changing Code'));
        if(md5($_POST['password']) != $change['password']){
            echo 'wrong_pass';
        }else{
            echo 'success';
            if(isset($_POST['open'])){
            	$_id = $this->checkClosingMonth();
                if(!$_id) {
					$this->Session->setFlash('The closing month has not been setup yet.','default',array('class'=>'flash_message'));
                	die;
                }
				$this->Session->write('JobsOpen_'.$_id,1);
				$this->Session->setFlash('This '.$this->name.' has been opened.','default',array('class'=>'flash_message'));
            }
            $arr_save = array();
            foreach($_POST as $key => $post){
            	if(strpos($key, '_save') !== false){
            		$arr_save[str_replace('_save', '', $key)] = $post;
            	}
            }
            if(!empty($arr_save)){
            	$arr_save['_id'] = new MongoId($this->get_id());
            	$this->opm->save($arr_save);
            }
        }
       	die;
	}

	function close_module(){
        $_id = $this->checkClosingMonth();
        if(!$_id) {
			$this->Session->setFlash('The closing month has not been setup yet.','default',array('class'=>'flash_message'));
        	die;
        }
		$this->Session->delete('JobsOpen_'.$_id);
		$this->Session->setFlash('This '.$this->name.' has been closed.','default',array('class'=>'flash_message'));
		echo 'ok';
		die;
	}

	function checkClosingMonth($arr = array()) {
		if(empty($arr)) {
			if(isset($this->opm)){
				$arr = $this->opm->select_one(array('_id' => new MongoId($this->get_id())),array('job_id'));
			}
		}
		if(isset($arr['work_end'])) {
			$work_end = $arr['work_end'];
			$job_id = $arr['_id'];
		} else {
			if(isset($arr['job_id']) && is_object($arr['job_id'])) {
				$job_id = $arr['job_id'];
			} else {
				$job_id = new MongoId($this->get_id());
			}
			$this->selectModel('Job');
	    	$work_end = $this->Job->select_one(array('_id'=>$job_id),array('work_end'));
	    	$work_end = $work_end['work_end'];
		}
    	$this->selectModel('Closingmonth');
        $arr_where = array(
                    'inactive' => 0,
                    'module'    => 'Jobs',
                    'date_from' => array('$lte' => $work_end),
                    'date_to' => array('$gte' => $work_end),
                    );
        $closing_month = $this->Closingmonth->select_one($arr_where,array('_id'),array('date_from' => 1));
        return isset($closing_month['_id']) ? (string)$job_id : false;
	}

	function get_minimum_order_adjustment($arr_data,$minimum, $ids = ''){
        if(isset($arr_data['products']) && !empty($arr_data['products'])){
            $last_insert = end($arr_data['products']);
            foreach($last_insert as $key=>$value){
                if($key=='deleted' || $key=='taxper' || $key=='tax') continue;
                $last_insert[$key] = '';
                $last_insert['xlock'][$key] = 1;
            }
        }
        $last_insert['products_name'] = 'Minimum Order Adjustment';
        $last_insert['xlock']['products_name'] = '1';
        $last_insert['xlock']['unit_price'] = '1';
        $last_insert['xlock']['quantity'] = '1';
        foreach(array('sku','products_id','details','option','sizew','sizew_unit','sizeh','sizeh_unit','receipts','view_costing','sell_by','sell_price','adj_qty','oum','custom_unit_price','unit_price','currency','vip') as $value)
            $last_insert['xempty'][$value] = '1';
        $last_insert['sku_disable'] = 1;
        $last_insert['_id'] = 'Extra_Row';
        $last_insert['remove_deleted'] = '1';
        $last_insert['quantity'] = '1';
        if($ids == '')
        	$ids = $this->get_id();
    	$query = $this->opm->select_one(array('_id'=> new MongoId($ids)),array('taxval'));
    	if(isset($query['taxval']))
        	$last_insert['taxper']= $query['taxval'];
        else
        	$last_insert['taxper']= 0;
        $last_insert['sub_total'] = $this->opm->format_currency($minimum - $arr_data['sum_sub_total']);
        $arr_data['sum_sub_total'] = $minimum;
        $tax = $minimum*$last_insert['taxper']/100;
        $last_insert['tax'] = $this->opm->format_currency(($tax - $arr_data['sum_tax']),3);
        $arr_data['sum_tax'] = $tax;
        $last_insert['amount'] = $this->opm->format_currency($minimum + $tax - $arr_data['sum_amount']);
        $arr_data['sum_amount'] = $minimum + $tax;
        array_push($arr_data['products'], $last_insert);
        return $arr_data;
	}

	function rebuild_roles(){
		$this->selectModel('Role');
		$arr_roles = $this->Role->select_all(array('arr_filed'=>array('value')));
		echo $arr_roles->count().' records found.<br />';
		$i = 0;
		foreach($arr_roles as $role){
			if(!isset($role['value'])) continue;
			foreach($role['value'] as $key => $value)
				$role['value'][$key] = array('all','owner');
			$this->Role->rebuild_collection($role);
			$i++;
		}
		die;
	}

	public function duplicate_option_product(){
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
            $this->Product->arrfield();
            $default_field = $this->Product->arr_temp;
            //Neu product co ID
            if( isset($query['products'][$product_line]['products_id'])&&is_object($query['products'][$product_line]['products_id']) ){
                $product_id = $query['products'][$product_line]['products_id'];
                $product = $this->Product->select_one(array('_id'=>new MongoId($product_id)));
            } else { //Custom product
                //Lay default
                $product = $default_field;
                $product['sell_price'] = $product['unit_price'] = $product['cost_price'] =  (float)$this_product['unit_price'];
                $product['product_type'] = 'Custom Product';
            }
            $current_user_id = $this->opm->user_id();
            // $option = $this->option_list_data($product_id,$product_line);
            $option = $this->new_option_data(array('key'=>$product_line,'products_id'=>$query['products'][$product_line]['products_id'],'options'=>$query['options'],'date'=>$query['_id']->getTimestamp()),$query['products']);
            foreach($option['option'] as $key=>$value){
                if(isset($value['choice'])&&$value['choice']==1)
                    $option['option'][$key]['require'] = 1;
                unset($option['option'][$key]['original_unit_price']);
                unset($option['option'][$key]['is_tempory_product']);
                unset($option['option'][$key]['sub_total']);
                unset($option['option'][$key]['this_line_no']);
                unset($option['option'][$key]['parent_line_no']);
                unset($option['option'][$key]['line_no']);
                unset($option['option'][$key]['proline_no']);
                unset($option['option'][$key]['adj_qty']);
                unset($option['option'][$key]['plus_unit_price']);
                unset($option['option'][$key]['amount']);
                unset($option['option'][$key]['thisline_no']);
                unset($option['option'][$key]['xlock']);
                if(isset($value['products_id'])&&is_object($value['products_id'])) continue;
                if(isset($value['product_id'])&&is_object($value['product_id'])) continue;
                $option_product = $default_field;
                $option_product['sell_price'] = $option_product['unit_price'] = $option_product['cost_price'] =  $value['sell_price'];
                $option_product['product_type'] = 'Custom Product';
                $option_product['code'] = $this->Product->get_auto_code('code');
                $option_product['name'] = $value['product_name'];
                $option_product['sizeh'] = $value['sizeh'];
                $option_product['sizeh_unit'] = $value['sizeh_unit'];
                $option_product['sizew'] = $value['sizew'];
                $option_product['sizew_unit'] = $value['sizew_unit'];
                $option_product['sku'] = $value['sku'];
                $option_product['sell_by'] = $value['sell_by'];
                $option_product['oum'] = $value['oum'];
                $option_product['quantity'] = isset($value['quantity']) ? $value['quantity'] : 1;
                $option_product['options'] = array();
                $option_product['created_by'] = new MongoId($current_user_id);
                unset($option_product['_id']);
                $this->Product->save($option_product);
                $new_option_product_id = $this->Product->mongo_id_after_save;
                $option['option'][$key]['name'] = $value['product_name'];
                $option['option'][$key]['code'] = $option_product['code'];
                $option['option'][$key]['product_id'] = new MongoId($new_option_product_id);
                if(isset($value['this_line_no']) && $value['this_line_no']!=''){
                    $query['options'][$value['this_line_no']]['product_id'] = $query['options'][$value['this_line_no']]['products_id'] = new MongoId($new_option_product_id);
                    $query['options'][$value['this_line_no']]['code'] = $option_product['code'];
                }
                if(isset($value['line_no']) && $value['line_no']!=''){
                    $query['products'][$value['line_no']]['products_id']  = new MongoId($new_option_product_id);
                    $query['products'][$value['line_no']]['code']  = $option_product['code'];
                }
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
            $product['options'] = $option['option'];
            $product['created_by'] = new MongoId($current_user_id);
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

    function replace_option_product(){
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
            // $option = $this->option_list_data($product_id,$product_line);
            $option = $this->new_option_data(array('key'=>$product_line,'products_id'=>$query['products'][$product_line]['products_id'],'options'=>$query['options'],'date'=>$query['_id']->getTimestamp()),$query['products']);
            foreach($option['option'] as $key=>$value){
                if(isset($value['choice'])&&$value['choice']==1)
                    $option['option'][$key]['require'] = 1;
                unset($option['option'][$key]['original_unit_price']);
                unset($option['option'][$key]['is_tempory_product']);
                unset($option['option'][$key]['sub_total']);
                unset($option['option'][$key]['this_line_no']);
                unset($option['option'][$key]['parent_line_no']);
                unset($option['option'][$key]['line_no']);
                unset($option['option'][$key]['proline_no']);
                unset($option['option'][$key]['adj_qty']);
                unset($option['option'][$key]['plus_unit_price']);
                unset($option['option'][$key]['amount']);
                unset($option['option'][$key]['thisline_no']);
                unset($option['option'][$key]['xlock']);
            }
            $product['name'] = $this_product['products_name'];
            $product['sizeh'] = $this_product['sizeh'];
            $product['sizeh_unit'] = $this_product['sizeh_unit'];
            $product['sizew'] = $this_product['sizew'];
            $product['sizew_unit'] = $this_product['sizew_unit'];
            $product['sku'] = $this_product['sku'];
            $product['sell_by'] = $this_product['sell_by'];
            $product['oum'] = $this_product['oum'];
            $product['options'] = (isset($option['option'])&&!empty($option['option']) ? $option['option'] : array());
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
                $this->Session->write('ProductsViewId',(string)$this->Product->mongo_id_after_save);
                die;
            } else {
                echo $this->opm->arr_errors_save[1];
                die;
            }
        }
        die;
    }


    public function get_arr_currency() {
    	$arr_currency = Cache::read('arr_currency');
    	if(!$arr_currency) {
	    	$this->selectModel('Setting');
			$arr_currency  = $this->Setting->select_one(array('setting_value'=>'currency_type'),array('option'));
			if(!isset($arr_currency['option']))
				$arr_currency['option'] = array();
			$arr_currency = array_filter($arr_currency['option'],function($arr){
				return isset($arr['deleted']) && !$arr['deleted'];
			});
			Cache::write('arr_currency',$arr_currency);
    	}
		return $arr_currency;
    }

    function change_total_currency() {
    	if(!empty($_POST)) {
    		$id = new MongoId($this->get_id());
    		$query = $this->opm->select_one(array('_id' => $id), array('products','options','taxval','currency'));
    		if(isset($query['products']) && !empty($query['products'])) {
    			foreach($query['products'] as $key => $product) {
    				$cal_price = new cal_price;
    				$cal_price->arr_product_items = $product;
    				$query['products'][$key] = $cal_price->cal_price_currency($query['currency'], $_POST['currency'], $this->arr_currency);
    			}
    		}
    		if(isset($query['options']) && !empty($query['options'])) {
    			foreach($query['options'] as $key => $product) {
    				$cal_price = new cal_price;
    				$cal_price->arr_product_items = $product;
    				$query['options'][$key] = $cal_price->cal_price_currency($query['currency'], $_POST['currency'], $this->arr_currency);
    			}
    		}
    		$query['currency'] = $_POST['currency'];

    		$arr_sum = $this->new_cal_sum($query['products']);
    		$query = array_merge($query,$arr_sum);
    		$this->opm->save($query);
    		if($this->name!='Purchaseorders'){
	            $minimum = $this->get_minimum_order();
	            if($arr_sum['sum_sub_total'] < $minimum){
	            	$extra_price = $minimum - $arr_sum['sum_sub_total'];
	            	$arr_data['last_insert']['sub_total'] = $extra_price;
	            	$arr_data['last_insert']['tax'] = $extra_price*$query['taxval']/100;
	            	$arr_data['last_insert']['amount'] = $extra_price+$arr_data['last_insert']['tax'];
	            	$arr_sum['sum_amount'] += $arr_data['last_insert']['amount'];
	            	$arr_sum['sum_sub_total'] += $arr_data['last_insert']['sub_total'];
	            	$arr_sum['sum_tax'] += $arr_data['last_insert']['tax'];
	            }
            }
    		echo json_encode($arr_sum);
    	}
    	die;
    }

    function email_image_filter(&$content, &$files){
    	$arr_downloaded_files = array();
        preg_match_all("!<img [^>]+ />!", $content, $matches);
        if(!empty($matches)) {
        	foreach($matches as $match) {
				foreach($match as $urls){
					preg_match('!http://[^?#]+\.(?:jpe?g|png|gif)!Ui' , $urls , $url_match);
					if(empty($url_match)) continue;
					$url_match = reset($url_match);
					$original_url = $url_match;
					$url_match = urldecode($url_match);
					$file_name = pathinfo($url_match, PATHINFO_BASENAME );
					if(strpos($url_match, URL) === false) {
						$path = WWW_ROOT.'upload'.DS.$file_name;
						file_put_contents($path, file_get_contents($url_match));
					} else {
						$path = str_replace(URL.'/', WWW_ROOT, $url_match);
						$path = str_replace('/', DS, $path);
					}
					$contentId = md5($file_name);
	        		$files[$contentId] = array(
	                            'file' => $path,
	                            'mimetype' => image_type_to_mime_type (exif_imagetype($path)) ,
	                            'contentId' => $contentId
	                        );
	        		$content = str_replace($original_url, 'cid:'.$contentId, $content);
				}
        	}
        }
    }

    function set_default_identity()
    {
    	foreach(['Company', 'Contact','Communication', 'Doc', 'Job', 'Task', 'Salesorder', 'Salesinvoice', 'Salesaccount', 'Quotation', 'Product', 'Receipt', 'Shipping', 'Purchaseorder', 'Batche', 'Unit', 'Location', 'Emailtemplate'] as $model) {
    		$this->selectModel($model);
    		echo '<h3>'.$model.'</h3><br />';
    		$count = $this->$model->count(array('identity' => array('$exists' => false)));
    		echo $count.' records found.<br />';
    		$this->$model->collection->update(array('identity' => array('$exists' => false)), array('$set' => array('identity' => new MongoId('5271dab4222aad6819000ed0'))), array('multiple' => true));
    		echo $count.' were updated.<br/>_________________________<br/>';
    	}
    	die;
    }

    function background_process($cmd, $type = '') {
    	if( $type == 'cake_console' ) {
    		$cmd = 'php '.ROOT.DS.'lib'.DS.'Cake'.DS.'Console'.DS.'cake.php -app '.ROOT.DS.'app'.' '.$cmd;
    	}
    	if( DS == '\\') {
            pclose(popen("start /B ". $cmd, "r"));
        } else {
            return exec($cmd.' > /dev/null &');
        }
    }

    function check_stock($begin_time=0, $product_id,$type='so'){ // so, so_completed, po, all_so, all_so_completed, all_po
        $query_where = array(
            'deleted' => false,
        );
        if(in_array($type,array('po','all_po'))){
            $table = 'tb_purchaseorder';
            $query_where['purchase_orders_status'] = 'On order';
        }else if(in_array($type,array('so','all_so'))){
            $table = 'tb_salesorder';
            $query_where['status'] = array('$nin' => array('Cancelled','Completed'));
        }else{
            $table = 'tb_salesorder';
            $query_where['status'] = 'Completed';
            if((int)$begin_time>1420059772)
            	$query_where['date_modified'] = array('$gt'=> new MongoDate($begin_time));
        }
        if(in_array($type,array('all_so','all_so_completed','all_po'))){ //all
            $query_where['products'] = array('$exists' => true);
             $output = 2;
        }else{ //one
            $query_where['products'] = array('$elemMatch'=>array(
									            				'products_id'=>new MongoId($product_id),
									            				'deleted'=> false
            													)
            								);
            $output = 1;
        }

        $data = $this->db->command(array(
            'mapreduce'     => $table,
            'map'           => new MongoCode('
                                            function() {
                                                for( var i in this.products ) {
                                                    if( this.products[i].deleted == true ) continue;
                                                    if( typeof this.products[i].products_id != "object" ) continue;
                                                    id = this.products[i].products_id;
                                                    var value = { quantity: this.products[i].quantity  };
                                                    emit( id, value );
                                                }
                                            }
                                            '),
            'reduce'        => new MongoCode('
                                            function(k, v) {
                                                var data = {quantity : 0};
                                                for( var i in v) {
                                                    data.quantity += v[i].quantity;
                                                }
                                                return data;
                                            }
                                        '),
            'query'         => $query_where,
            'out'           => array('merge' => 'tb_result')
        ));
        if( isset($data['ok']) ){
            $data = $this->db->selectCollection('tb_result')->find();
            $return_one = $return_all = array();
            foreach($data as $value) {
                if( isset($value['_id']) && is_object($value['_id']) && $product_id!='' && $value['_id']==new MongoId($product_id))
                    $return_one = $value;
                if(isset($value['_id']) && is_object($value['_id']))
                    $return_all[(string)$value['_id']] = $value;
            }
        }
        $this->db->selectCollection('tb_result')->drop();
        if( $output == 1)
            return isset($return_one['value']['quantity'])?(float)$return_one['value']['quantity']:0;
        else
            return $return_all;
    }

    public function rebuild_shipping_cost() {
    	$returnString = '';
    	foreach(array('Quotation', 'Salesinvoice', 'Salesorder') as $model) {
    		$returnString .= '<br />'.$model;
    		$i = 0;
    		$this->selectModel($model);
    		$data = $this->$model->select_all(array(
    					'arr_field' => array('products')
    			));
    		$returnString .= '<br />'.$data->count().' records found.';
    		foreach($data as $value) {
    			$value['shipping_cost'] = 0;
    			if( isset($value['products']) && !empty($value['products']) ) {
    				foreach($value['products'] as $product) {
    					if( isset($product['deleted']) && $product['deleted'] ) continue;
    					if( !isset($product['sku']) || empty($product['sku']) ) continue;
    					if( !preg_match('/^SHP/', $product['sku']) ) continue;
    					$value['shipping_cost'] += isset($product['sub_total']) ? $product['sub_total'] : 0;
    				}
    				unset($value['products']);
    			}
    			if( $value['shipping_cost'] ) {
    				$i++;
    			}
    			$this->$model->rebuild_collection($value);
    		}
    		$returnString .= '<br />'.$i.' records rebuild.<br />-------------------------------------';
    	}
    	echo $returnString;
    	die;
    }

    public function show_summary_entry($show)
    {
    	if( $this->request->is('ajax') ) {
    		$this->Session->write($this->params->params['controller'].'_summary_entry', $show ? true : false);
    		echo 'ok';
    	}
    	die;
    }

    public function clear_deleted_record(){
    	$condition = array('deleted'=>true);
    	$this->opm->remove($condition);
    	die;
    }

}