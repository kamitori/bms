<?php
App::uses('Controller', 'Controller');
class MobileAppController extends Controller {
	public $viewClass = 'Theme';
	public $theme = 'mobilev2';
	public $layout = 'mobile';
	public $opm = '';
	function beforeFilter() {
		if ($this->request->is('ajax') && !$this->Session->check('arr_user')) {
			echo 'Your session is time out, please re-login. Thank you.<script type="text/javascript">location.reload(true);</script>';
			exit;
		}
		if ($this->request->url != 'mobile/users/login' && !$this->Session->check('arr_user')) {
			$this->Session->write('REFERRER_LOGIN', '/' . $this->request->url);
			$this->redirect('/mobile/users/login');
		}
		$this->set('controller', $this->params->params['controller']);
		$this->set('model', $this->modelName);
		$this->set('action', $this->params->params['action']);
		if(!$this->request->is('ajax'))
			$this->set('arr_menu',$this->rebuild_header());
		$this->set('request',$this->request);
	}
	function rebuild_header(){
		$arr_menu = array(
			'apartment-icon' => array(
				'name'	=> 'CRM',
				'companies' => array(
									'name' => 'Company',
									'class' => 'company',
									'inactive' => '0',
								),
				'contacts' => array(
									'name' => 'Contact',
									'class' => 'contacts',
									'inactive'	=> '0',
								),
				'communications' => array(
									'name' => 'Comm',
									'class' => 'comms',
									'inactive'	=> '0',
								),
				'docs' => array(
									'name' => 'Doc',
									'class' => 'docs',
									'inactive'	=> '0',
								),
				'jobs' => array(
									'name' => 'Job',
									'class' => 'jobs',
									'inactive'	=> '0',
								),
				'tasks' => array(
									'name' => 'Task',
									'class' => 'tasks',
									'inactive'	=> '0',
								),
				'timelogs' => array(
				  					'name' => 'TimeLog',
				  					'class' => 'timelog',
				  					'inactive'	=> '1',
				  				),
				'stages' => array(
				 					'name' => 'Stages',
				 					'class' => 'stages',
				 					'inactive'	=> '1',
				 				),
			),
			'suitcase-icon' => array(
				'name'	=> 'Sales',
				'enquiries' => array(
									'name' => 'Enquiry',
									'class' => 'enquiries',
									'inactive'	=> '0',
								),
				'quotations' => array(
									'name' => 'Quote',
									'class' => 'quotes',
									'inactive'	=> '0',
								),
				'salesorders' => array(
									'name' => 'Sales Ord',
									'class' => 'sales_ord',
									'inactive'	=> '0',
								),
				'salesaccounts' => array(
									'name' => 'Sales Acc',
									'class' => 'sales_acc',
									'inactive'	=> '0',
								),
				'salesinvoices' => array(
									'name' => 'Sales Inv',
									'class' => 'sales_inv',
									'inactive'	=> '0',
								),
				'receipts' => array(
									'name' => 'Receipt',
									'class' => 'receipts',
									'inactive'	=> '0',
								)
			),
			'table-icon' => array(
				'name'	=> 'Inventory',

				'products' => array(
								'name' => 'Product',
								'class' => 'products',
								'inactive'	=> '0',
								),
				'locations' => array(
								'name' => 'Location',
								'class' => 'locations',
								'inactive'	=> '0',
								),
				'units' => array(
								'name' => 'Units',
								'class' => 'units',
								'inactive'	=> '1',
								),
				'batches' => array(
								'name' => 'Batches',
								'class' => 'batches',
								'inactive'	=> '1',
								),
				'purchaseorders' => array(
								'name' => 'Pur Order',
								'class' => 'purch_ord',
								'inactive'	=> '0',
								),
				'shippings' => array(
								'name' => 'Shipping',
								'class' => 'shipping',
								'inactive'	=> '0',
								),
			)
		);
		// foreach($arr_menu as $menu=>$sub_menu){
		// 	foreach($sub_menu as $controller=>$value){
		// 		if(is_array($value)&&$value['inactive']!=1){
		// 			$model = trim(ucfirst(Inflector::singularize($controller)));
		// 			$this->selectModel($model);
		// 			$count = $this->$model->count();
		// 			$arr_menu[$menu][$controller]['number'] = $count;
		// 		}
		// 	}
		// }
		return $arr_menu;
	}
	public function index() {
		if ($this->Session->check($this->name . 'ViewThemes') && $this->Session->read($this->name . 'ViewThemes') != '')
			$views = $this->Session->read($this->name . 'ViewThemes');
		else
			$views = 'entry';
		$this->redirect('/mobile/' . $this->params->params['controller'] . '/' . $views);
		die;
	}
	public function entry(){
		echo 'In Construct<br />';
		echo '<a href="'.M_URL.'/tasks/entry">Come back to Tasks</a>';
		die;
	}
	public function entry_search(){
		echo 'In Construct<br />';
		echo '<a href="'.M_URL.'/tasks/entry">Come back to Tasks</a>';
		die;
	}
	public function lists(){
		echo 'In Construct<br />';
		echo '<a href="'.M_URL.'/tasks/entry">Come back to Tasks</a>';
		die;
	}
	public function options($module_id='',$module_name=''){
		echo 'In Construct<br />';
		echo '<a href="'.M_URL.'/tasks/entry">Come back to Tasks</a>';
		die;
	}

	function entry_init($id, $num_position, $model, $controller) {
		$this->selectModel($model);
		$cond = array();
		if( $this->Session->check($controller.'_entry_search_cond') ){
			$cond = $this->Session->read($controller.'_entry_search_cond');
		}
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
			if(!$this->request->is("ajax"))
				$this->Session->write($this->name . 'ViewId',(string) $arr_tmp['_id']);
		} else {

			if ($this->Session->check($this->name . 'ViewId')) {
				$arr_tmp = $this->$model->select_one(array('_id' => new MongoId($this->Session->read($this->name . 'ViewId'))));
				if (!isset($arr_tmp['_id'])) {
					$this->Session->delete($this->name . 'ViewId');
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
		$this->set('mongoid',$arr_tmp['_id']);
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
	public function prevs($id='') {
		if($id!=''){
			$this->Session->write($this->name . 'ViewId',$id);
		}
		$modelName = $this->modelName;
		$this->selectModel($modelName);
		$this->opm = $this->$modelName;
		if ($this->get_id() != '') {
			$where_query = $this->arr_search_where();

			$where_query['_id'] = array('$lt' => new MongoId($this->get_id()));
			$arr_prev = $this->opm->select_one($where_query, array('_id'), array('_id' => -1));
		}
		if (isset($arr_prev['_id']))
			$prevs = $arr_prev['_id'];
		else
			$prevs = 'first';
		if($this->request->is('ajax')){
			if($prevs=='first')
				die;
			$this->entry($prevs);
			$this->set("ajax",true);
			$this->render('../'.$this->name.'/entry');
		} else
			$this->redirect('/mobile/' . $this->params->params['controller'] . '/entry/' . $prevs);
	}

	// link to next entry
	public function nexts($id='') {
		if($id!=''){
			$this->Session->write($this->name . 'ViewId',$id);
		}
		$modelName = $this->modelName;
		$this->selectModel($modelName);
		$this->opm = $this->$modelName;
		if ($this->get_id() != '') {
			$where_query = $this->arr_search_where();
			$where_query['_id'] = array('$gt' => new MongoId($this->get_id()));
			$arr_nexts = $this->opm->select_one($where_query, array('_id'), array('_id' => 1));
		}
		if (isset($arr_nexts['_id']))
			$nexts = $arr_nexts['_id'];
		else
			$nexts = 'last';
		if($this->request->is('ajax')){
			if($nexts=='last')
				die;
			$this->entry($nexts);
			$this->set("ajax",true);
			$this->render('../'.$this->name.'/entry');
		} else
			$this->redirect('/mobile/' . $this->params->params['controller'] . '/entry/' . $nexts);
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
				else if ($keys == 'code')
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

		if(isset($_SESSION[$this->name.'_where_permission'])){
			if(is_array($where_query))
				return array_merge($where_query,$_SESSION[$this->name.'_where_permission']);
			return $_SESSION[$this->name.'_where_permission'];
		}
		return $where_query;
	}
	public function get_id() {
		if($this->Session->check($this->name . 'ViewId')){
			$iditem = $this->Session->read($this->name . 'ViewId');
		}
		else {
			//find last id
			if ($this->opm)
				$arr_tmp = $this->opm->select_one(array(), array('_id'), array('_id' => -1));
			else {
				$module = $this->modelName;
				$this->selectModel($module);
				$arr_tmp = $this->$module->select_one(array(), array('_id'), array('_id' => -1));
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
	public function getSubTab($className){
		$arr_subtab = array();
		$arr_methods = get_class_methods($className);
		foreach($arr_methods as $method_name){
			if(strpos($method_name, '_sub_tab')){
				$arr_subtab[] = str_replace('_sub_tab', '', $method_name);
			}
		}
		return $arr_subtab;
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
			$this->connectionDB = new Mongo(IP_CONNECT_MONGODB . ':27017?connectTimeoutMS=300000');
			$this->db = $this->connectionDB->selectDB(DB_CONNECT_MONGODB);
		}
	}

	function mongo_disconnect() {
		// $this->db->command(array("logout" => 1));
		return $this->connectionDB->close();
	}

	function selectModel($model) {
		$this->mongo_connect();
		if (is_object($this->db)/* && is_null($this->$model)*/) {
			if (file_exists(APP . 'Model' . DS . $model . '.php')) {
				require_once APP . 'Model' . DS . $model . '.php';
				$this->$model = new $model($this->db);
			} else {
				//echo 'File ' . APP.'Model'.DS.$model.'.php' . ' does not exist';die;
			}
		}
	}

	function delete($id = '') {
		if(strlen($id) != 24)
			$id = $this->get_id();
		$arr_save['_id'] = new MongoId($id);
		$arr_save['deleted'] = true;
		$controller = $this->params->params['controller'];
		$model = ucfirst(Inflector::singularize($controller));
		$this->selectModel($model);
		if ($this->$model->save($arr_save)) {
			$this->Session->delete($this->name.'ViewId');
			if($this->request->is('ajax'))
				echo 'ok';
			else
				$this->redirect('/mobile/'.$controller.'/entry');
		} else {
			echo 'Error: ' . $this->$model->arr_errors_save[1];
		}
		die;
	}

	function before_save($field,$value,$id, $options = array()){
		$arr_return = array();
		return $arr_return;
	}

	function save_data(){
		if(!empty($_POST)){
			$controller = $this->params->params['controller'];
			$model = ucfirst(Inflector::singularize($controller));
			$this->selectModel($model);
			$arr_save = array(
			                    '_id' => new MongoId($_POST['ids']),
			                    $_POST['field'] => $_POST['value']
			                );
			$arr_save = array_merge($arr_save,$this->before_save($_POST['field'],$arr_save,$_POST['ids']));
			$this->$model->save(array(
			                    '_id' => new MongoId($_POST['ids']),
			                    $_POST['field'] => $_POST['value']
			                    ));
			echo 'ok';
		}
		die;
	}

	function save_option(){
		if(!empty($_POST)){
			$controller = $this->modelClass;
			$model = ucfirst(Inflector::singularize($controller));
			$this->selectModel($model);
			$id = $this->get_id();
			$arr_field = array($_POST['opname']);
			if(isset($_POST['extra_field'])){
				$arr_field = array_merge($arr_field,$_POST['extra_field']);
			}
			$arr_save = $this->$model->select_one(array('_id' => new MongoId($id)),$arr_field);
			if(!isset($arr_save[$_POST['opname']]))
				$arr_save[$_POST['opname']] = array();
			$arr_data = $_POST['data'];
			if(!is_numeric($_POST['key']))
				$key_save = count($arr_save[$_POST['opname']]);
			else
				$key_save = $_POST['key'];
			$arr_data = $this->handleData($arr_data);
			//pr($arr_data); // TH save address field 'default', chuyen all 'default' thanh 0
			if (isset($arr_data['default']) && isset($arr_save['addresses']))
				foreach ($arr_save['addresses'] as $key => $address) {
					$arr_save['addresses'][$key]['default'] = false;
				}	
			//pr($arr_save);
			foreach($arr_data as $dataKey => $dataValue){
				if(is_array($dataValue)){
					foreach($dataValue as $key => $value){
						if(is_array($value)){
							foreach($value as $k => $v)
								$arr_save[$_POST['opname']][$key_save][$dataKey][$key][$k] = $v;
						} else
							$arr_save[$_POST['opname']][$key_save][$dataKey][$key] = $value;
					}
				} else {
					$arr_save[$_POST['opname']][$key_save][$dataKey] = $dataValue;
				}
			}
			//pr($arr_save);die;
			$arr_save = array_merge($arr_save,$this->before_save($_POST['opname'],$arr_save,$id,array('key' => $key_save)));
			$this->$model->save($arr_save);
			$arr_return = array($_POST['opname'] => array($key_save => $arr_save[$_POST['opname']][$key_save]));
			unset($arr_save[$_POST['opname']]);
			if(!empty($arr_save))
				$arr_return = array_merge($arr_return,$arr_save);
			echo json_encode($arr_return);
		}
		die;
	}

	function handleData($arr){
		foreach($arr as $key => $value){
			if($value == 'true'){
				$value = true;
			} else if($value == 'false'){
				$value = false;
			} else if(strpos($key, '_id') !== false && strlen($value) == 24){
				$value = new MongoId($value);
			} else if(is_numeric($value))
				$value = (int)$value;
			else if(is_array($value)){
				$value = $this->handleData($value);
			}
			$arr[$key] = $value;
		}
		return $arr;
	}

	public function docs(){
		if(isset($_POST['add'])){
			return $this->docs_add();
		}
		$offset = 0;
		if(isset($_POST['offset']))
			$offset = $_POST['offset'];
		$id = $this->get_id();

		/*----------------------------------*/
		$this->selectModel('DocUse');
		$arr_docuse = $this->DocUse->select_all(array(
			'arr_where' => array(
				'module' => $this->modelName,
				'module_id' => new MongoId($id)
			)
		));
		$arr_tmp_id = array();
		foreach ($arr_docuse as $value) {
			$arr_tmp_id[] = $value['doc_id'];
		}
		/*----------------------------------*/

        $this->selectModel('Doc');
        $arr_doc = $this->Doc->select_all(array(
                                                  'arr_where' => array('_id' => array('$in' => $arr_tmp_id)),
                                                  'arr_field' => array('no','name','category','ext','type','description','create_by_module','modified_by','date_modifie','location'),
                                                  'arr_order' => array('_id' => 1),
                                                  'limit' => 10,
                                                  'skip' => $offset
                                                  ));
        if($this->request->is('ajax')){
        	if($arr_doc->count(true) == 0){
        		echo json_encode(array('empty'=>true));
        		die;
        	}
			foreach($arr_doc as $doc){
				$doc['_id'] = (string)$doc['_id'];
				$arr_data['data'][] = $doc;
			}
			echo json_encode($arr_data);
			die;
        } else {
	        $arr = array();
	        foreach($arr_doc as $key => $value){
	                $arr[] = $value;
	        }
	        $this->set('arr_docs',$arr);

	        $arr_data['field'] = array(
        	                           'no'=>array(
        	                                       'label' => 'Doc no',
        	                                       'type' => ''),
        	                           'name'=>array(
        	                                       'label' => 'Doc name',
        	                                       'type' => 'text'),
        	                           'type'=>array(
        	                                       'label' => 'Category',
        	                                       'type' => 'text'),
        	                           'ext'=>array(
        	                                       'label' => 'Ext',
        	                                       'type' => 'text'),
        	                           'type'=>array(
        	                                       'label' => 'Type',
        	                                       'type' => 'checkbox'),
        	                           'description'=>array(
        	                                       'label' => 'Description',
        	                                       'type' => 'text'),
        	                           );
			$arr_data['header'] = array(
			                        'link_to_entry' => M_URL.'/docs/entry/',
			                        'link_to_entry_value' => 'no',
			                        'info' => 'name'
			                            );
			$this->set('arr_data',$arr_data);
        }
        $this->render('../elements/docs');
    }
    function docs_add() {
        $id = $this->get_id();

        $this->selectModel('Doc');
        $arr_save['no'] = $this->Doc->get_auto_code('no');
        $arr_save['type'] = '';
        $arr_save['path'] = '';
        $arr_save['create_by_module'] = $this->modelName;

        $this->Doc->arr_default_before_save = $arr_save;
        if ($this->Doc->add()) {
	        $this->selectModel('DocUse');
	        $arrDocUse = array(
	                            'module' => $this->modelName,
	                            'doc_id' => $this->Doc->mongo_id_after_save,
	                            'create_by_module' => $this->modelName,
	                            'module_id' => new MongoId($id)
	                            );
	        $this->DocUse->save($arrDocUse);
           echo M_URL.'/docs/entry/'. $this->Doc->mongo_id_after_save;
        }else
           echo M_URL .'/docs/entry';
        die;
    }
    function docs_delete($doc_id){
    	$this->selectModel('Doc');
    	$this->Doc->save(array('_id' => new MongoId($doc_id),'deleted' => true));
    	echo 'ok';
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
            //$this->opm->aasort($data,'option_group');
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
            }
            if(is_object($vvs['product_id'])){
            	$data[$kks]['xlock']['product_name'] = 1;
            	$data[$kks]['xlock']['oum'] = 1;
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
	                if(isset($pro_data['sub_total']))
	                    $count_sub_total += round((float)$pro_data['sub_total'],2);
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

	function get_tax($tax_key=''){
		if($tax_key!=''){
			$this->selectModel('Tax');
			$tax_list = $this->Tax->tax_list();
			if(isset($tax_list[$tax_key]))
				return $tax_list[$tax_key];
		}
		return 0;
	}

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
		if(isset($sell_break['sell_price']) && $sell_break['sell_price']!='')
			$result['sell_price'] = $sell_break['sell_price'];

		//gia phu them cua option
		if(isset($sell_break['sell_price_plus']) && $sell_break['sell_price_plus']!='')
			$result['sell_price_plus'] = $sell_break['sell_price_plus'];

		//lay bang chiec khau product
		if(isset($sell_break['price_break']) && count($sell_break['price_break'])>0)
			$result['product_price_break'] = $sell_break['price_break'];

		//pr($result);die;
		return $result;

		/**Kết quả cần lấy :
		*  1.Bảng chiếc khấu trong Company 							=> $result['company_price_break']
		*  2.Chiếc khấu áp dụng cho Company 						=> $result['discount']
		*  3.Giá bán cho company dựa theo key category, 			=> $result['sell_price']
		*  4.Bảng chiếc khấu trong product dựa theo key category, 	=> $result['product_price_break']
		*/
	}

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


			if(isset($arr_company['sell_category_id']))
				$result['sell_category_key'] = $arr_company['sell_category_id'];
			if(isset($arr_company['discount']))
				$result['discount'] = (float)$arr_company['discount'];

		}

		return $result; //ket qua tra ve la array price_break va sell_category_key

	}

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

	function get_minimum_order($modelName='',$id=''){
    	$original_minimun = Cache::read('minimun');
    	$product = Cache::read('minimun_product');
    	if(!$original_minimun){
	    	$this->selectModel('Stuffs');
	    	$product = $this->Stuffs->select_one(array('value'=>"Minimun Order Adjustment"),array('product_id'));
	    	$this->selectModel('Product');
    		$p = $this->Product->select_one(array('_id'=> new MongoId($product['product_id'])),array('sell_price'));
    		if(isset($p['sell_price']))
    			$original_minimun = (float)$p['sell_price'];
	    	Cache::write('minimun',$original_minimun);
        	Cache::write('minimun_product',$product);
    	}
    	$controller = $this->params->params['controller'];
		$model = ucfirst(Inflector::singularize($controller));
		$this->selectModel($model);
		if($modelName=='')
			$query = $this->$model->select_one(array('_id'=> new MongoId($this->get_id())),array('company_id'));
		else{
			$this->selectModel($modelName);
			$query = $this->$modelName->select_one(array('_id'=> new MongoId($id)),array('company_id'));
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
						$original_minimun = (float)$price_break['unit_price'];
						$arr_companies[(string)$query['company_id']] = $original_minimun;
        				Cache::write('arr_companies',$arr_companies);
        				return $original_minimun;
					}
				}
			}
        } else
        	$original_minimun = $arr_companies[(string)$query['company_id']];
    	return $original_minimun;
    }

	function cal_price_line($arr_post = array(),$lock_ajx =0){
        if(isset($_POST['data']) && $lock_ajx ==0){
            $arr_post = $_POST;
            $arr_post['data'] = (array)json_decode($_POST['data']);
        }
        $controller = $this->params->params['controller'];
		$model = ucfirst(Inflector::singularize($controller));
		$this->selectModel($model);
        if(!empty($arr_post)){
            $data = $arr_post['data'];
            $fieldchange = $arr_post['fieldchange'];
            $id = $this->get_id();
        	$query = $this->$model->select_one(array('_id'=>new MongoId($id)),array('products','options','company_id','tax',));
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
            if(isset($query['tax']) && $query['tax']!='')
                $line_data['taxper'] = $this->get_tax($query['tax']);
            //==============================================
            //Nếu là thay đổi ở line entry, thì foreach option tính lại từ đầu
            //Nếu thay đổi sell price ở line entry thì bỏ qua
            //FIELDCHANGE
            if(($fieldchange!='sell_price')&&!$no_same_parent){
                $line_data['plus_sell_price'] = 0;
                if(isset($line_data['plus_unit_price']))
                    unset($line_data['plus_unit_price']);
                //Lấy tất cả option của line
                $options = $this->new_option_data(array('key'=>$line_no,'products_id'=>$line_data['products_id'],'options'=>$query['options'],'products'=>$query['products'],'date'=>$date),$query['products']);
                if(isset($options['option'])&&!empty($options['option'])){
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
                            if(isset($option['is_custom']) && $option['is_custom']==1){
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
            $cal_price = new cal_price;
            $cal_price->arr_product_items = $line_data;
            if($fieldchange != 'custom_unit_price'){
            	//=============Tính line chính==============
            	if($is_combination){
	                if(isset($line_data['plus_sell_price']))
	                    unset($line_data['plus_sell_price']);
	                $line_data = array_merge($line_data,$cal_price->combination_cal_price());
	                $line_data['sell_price'] = $line_data['unit_price'] += $total_sub_total;
	                if($total_sub_total>0){
	                    $line_data['sub_total'] = round((float)$line_data['unit_price']*(float)$line_data['quantity'],2);
	                    $line_data['tax'] = round(((float)(isset($line_data['taxper']) ? $line_data['taxper'] : 0)/100)*(float)$line_data['sub_total'],3);
	                    $line_data['amount'] = round((float)$line_data['sub_total']+(float)$line_data['tax'],2);
	                }
	            } else {
	                $result = array();
	                if($fieldchange!='sell_price')
	                    $result = $this->change_sell_price_company($query['company_id'],$line_data['products_id']);
	                $cal_price->price_break_from_to = $result;
	                $cal_price->field_change = $fieldchange;
	                $line_data = array_merge($line_data,$cal_price->cal_price_items($is_special));
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
	                        $line_data['tax'] = round(((float)(isset($line_data['taxper']) ? $line_data['taxper'] : 0)/100)*(float)$line_data['sub_total'],3);
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
            //=============Save=========================
            $this->$model->save($query);
            //==========================================
            $arr_data = array();
            $arr_data['sum'] = $arr_sum;
            //===Update Minium Order Adjustment=========
            if($this->name!='Purchaseorders'){
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
            if(!isset($parent_line_data))
                $arr_data['self'] = $line_data;
            else {
                $arr_data['parent'] = $line_data;
                $arr_data['self'] = $this_line_data;
            }
            if(isset($_POST['data']) && $lock_ajx ==0)
                echo json_encode($arr_data);
            else
                return $arr_data;
        }
        die;
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
        $controller = $this->params->params['controller'];
		$model = ucfirst(Inflector::singularize($controller));
		$this->selectModel($model);
        $query = $this->$model->select_one(array('_id'=>new MongoId($id)),array('options','products','company_id','taxval'));
        $line_no = $arr_tmp_data['product_key'];
        $line_data = $query['products'][$line_no];
        $parent_id = $query['products'][$line_no]['products_id'];
        if(isset($line_data['plus_unit_price']))
            unset($line_data['plus_unit_price']);
        if(!is_object($line_data['products_id']))
            $line_data['unit_price'] = $line_data['sell_price'] = 0;
        $line_data['plus_sell_price'] = 0;
        unset($arr_tmp_data['submit'],$arr_tmp_data['product_key']);
        //=====================================
        foreach($arr_tmp_data as $key=>$value){
            $position = strrpos($key, '_',-1);
            if($position===false) continue;
            $k =substr($key, $position+1);
            if($k=='id') continue;
            if($value == 'on')
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
        $num_of_products = count($query['products']);
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
                        $query['options'][$option_no]['line_no'] = $num_of_products;
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
        $arr_update = array('sizew','product_name','sell_by','oum','sizew_unit','sizeh','sizeh_unit','unit_price','quantity','sub_total','sell_price','amount','tax','same_parent');
        $is_special = false;
        $this->selectModel('Setting');
        //===================Tính lại thuế===========================
        $taxval = 0;
        if(isset($query['taxval'])&&$query['taxval']!='')
        	$taxval = $query['taxval'];
        //===============End Tính lại thuế===========================
        $total_sub_total = 0;
        //Lặp vòng và tính lại toàn bộ option
        if(isset($options['option'])&&!empty($options['option'])){
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
                    $option['quantity']     = (isset($option['quantity']) ? (float)$option['quantity'] : 1);
                    //========================
                    $option['plus_sell_price'] = 0;
                    if(!isset($option['sell_by'])||$option['sell_by']=='')
                        $option['sell_by'] = $uom_list[$option['oum']];
                    //Bắt đầu tính giá trên từng option SAMEPARENT
                    $cal_price = new cal_price;
                    $cal_price->arr_product_items   = $option;
                    $cal_price->field_change = 'sell_price';
                    $option = $cal_price->cal_price_items();
                    //=========== End calprice =============
                    if(isset($option['choice'])&&$option['choice']==1)
                    	$line_data['plus_sell_price'] += $option['sub_total'];

                } else if($option['same_parent']==0){
                    //Bắt đầu tính giá trên từng option KHÔNG SAMEPARENT
                    $cal_price = new cal_price;
                    $option['taxper'] = $taxval;
                    $cal_price->arr_product_items = $option;
                    $cal_price->field_change = 'sell_price';
                    $option = array_merge($option,$cal_price->cal_price_items());
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
        //===============End Xét custom unit price================
        if($line_data['sell_by']=='combination'){
            if(isset($line_data['plus_sell_price']))
                unset($line_data['plus_sell_price']);
            $line_data = array_merge($line_data,$cal_price->combination_cal_price());
            $line_data['sell_price'] = $line_data['unit_price'] += $total_sub_total;
            if($total_sub_total>0){
                $line_data['sub_total'] = round((float)$line_data['unit_price']*(float)$line_data['quantity'],2);
                $line_data['tax'] = round(((float)(isset($line_data['taxper']) ? $line_data['taxper'] : 0)/100)*(float)$line_data['sub_total'],3);
                $line_data['amount'] = round((float)$line_data['sub_total']+(float)$line_data['tax'],2);
            }
        } else {
            $cal_price = new cal_price;
            $cal_price->arr_product_items = $line_data;
            $cal_price->price_break_from_to = $this->change_sell_price_company($query['company_id'],$line_data['products_id']);
            $line_data = array_merge($line_data,$cal_price->option_cal_price());
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
        $arr_sum = $this->new_cal_sum($query['products']);
        $query = array_merge($query,$arr_sum);
        if($this->request->is('ajax')){
        	if($this->$model->save($query))
	            echo 'ok';
	        else
	            echo $this->arr_errors_save[1];
            die;
        } else
        	$this->opm->save($query);
    }
    function new_line_entry($option,&$parent_line,$company_id,&$current_options,$option_no){
        $option_for = $option['parent_line_no'];
        $option_id = $option['proline_no'];
        $new_line = array();
        $new_line['code']           = $option['code'];
        $new_line['sku']            = (isset($option['sku']) ? $option['sku'] : '');
        $new_line['products_name']  = $option['product_name'];
        $new_line['products_id']    = $option['product_id'];
        $new_line['quantity']       = $option['quantity'];
        $new_line['sub_total']      = $option['sub_total'];
        $new_line['sizew']          = isset($parent_line['sizew']) ? $parent_line['sizew'] : $option['sizew'];
        $new_line['sizew_unit']     = isset($parent_line['sizew_unit'])?$parent_line['sizew_unit']:$option['sizew_unit'];
        $new_line['sizeh']          = isset($parent_line['sizeh']) ? $parent_line['sizeh'] : $option['sizeh'];
        $new_line['sizeh_unit']     = isset($parent_line['sizeh_unit'])?$parent_line['sizeh_unit']:$option['sizeh_unit'];
        $new_line['sell_by']        = (isset($option['sell_by']) ? $option['sell_by'] : 'unit');
        $new_line['oum']            = $option['oum'];
        $new_line['same_parent']    = isset($option['same_parent']) ? (int)$option['same_parent'] : 0;
        $new_line['sell_price']     = (float)$option['unit_price'] - (float)$option['unit_price']*((float)$option['discount']/100);

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

    function get_minimum_order_adjustment($arr_data,$minimum){
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
        foreach(array('sku','products_id','details','option','sizew','sizew_unit','sizeh','sizeh_unit','receipts','view_costing','sell_by','sell_price','adj_qty','oum','custom_unit_price','unit_price') as $value)
            $last_insert['xempty'][$value] = '1';
        $last_insert['sku_disable'] = 1;
        $last_insert['_id'] = 'Extra_Row';
        $last_insert['remove_deleted'] = '1';
        $last_insert['quantity'] = '1';
        if(!isset($last_insert['taxper']))
        {
        	$modelName = $this->modelClass;
        	$query = $this->$modelName->select_one(array('_id'=> new MongoId($this->get_id())),array('taxval'));
        	if(isset($query['taxval']))
	        	$last_insert['taxper']= $query['taxval'];
	        else
	        	$last_insert['taxper']= 0;
        }
        $last_insert['sub_total'] = number_format($minimum - $arr_data['sum_sub_total'],2);
        $arr_data['sum_sub_total'] = $minimum;
        $tax = $minimum*$last_insert['taxper']/100;
        $last_insert['tax'] = number_format(($tax - $arr_data['sum_tax']),2);
        $arr_data['sum_tax'] = $tax;
        $last_insert['amount'] = number_format($minimum + $tax - $arr_data['sum_amount'],2);
        $arr_data['sum_amount'] = $minimum + $tax;
        array_push($arr_data['products'], $last_insert);
        return $arr_data;
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

	public function communications() {
    	if(isset($_POST['add']))
    	   return $this->coms_add();
    	$offset = 0;
    	if(isset($_POST['offset']))
    		$offset = $_POST['offset'];
    	$id = $this->get_id();
        $this->selectModel('Communication');
        $arr_coms = $this->Communication->select_all(array(
                                                'arr_where' => array('module_id'=>new MongoId($id)),
                                                'arr_order' => array('comms_date' => -1),
                                                'arr_field' => array('code','comms_type','comms_date','contact_from','module_id'),
                                                'skip' => $offset,
                                                'limit'  => 10
                                                ));
        $arr_data = array('data'=>array());
        $this->set("module_id",$id);
        //pr($arr_coms->count());die;
		foreach($arr_coms as $com){
			$com['_id'] = (string)$com['_id'];
			$com['name'] = isset($com['name']) ? $com['name'] : '';
			$com['comms_date'] = is_object($com['comms_date'])?date('d M, Y', $com['comms_date']->sec):$com['comms_date'];
			$arr_data['data'][] = $com;
		}
        if($this->request->is('ajax')){
        	if($arr_coms->count(true) == 0){
        		echo json_encode(array('empty'=>true));
        		die;
        	}
			echo json_encode($arr_data);
			die;
        } else {
	        $this->set('arr_coms',$arr_data['data']);
        	$arr_data['field'] = array(
	                           'code'=>array(
	                                       'label' => 'Ref no',
	                                       'type' => '',),
	                           'comms_type'=>array(
	                                       'label' => 'Type',
	                                       'type' => '',
	                                       ),
	                           'comms_date'=>array(
	                                       'label' => 'Date',
	                                       'type' => ''),
	                           'date_modified'=>array(
	                                       'label' => 'Due date',
	                                       'type' => ''),
	                           'contact_from'=>array(
	                                       'label' => 'From',
	                                       'type' => ''
	                                       ),
	                           'detail'=>array(
	                                       'label' => 'Detail',
	                                       'type' => '',
	                                       ),

	                           );
			$arr_data['header'] = array(
			                        'link_to_entry' => M_URL.'/communications/entry/',
			                        'link_to_entry_value' => 'code',
			                        'info' => 'comms_type'
			                            );
			$this->set('arr_data',$arr_data);
			$this->set('id',$id);
			$this->render('../elements/communications');
        }
    }
	public function add_from_module($module_id='', $comms_type='', $option = array()){
		if( $comms_type == 'Message' ){
			$this->redirect('/communications/entry_message/'.$_GET['contact_id'].'?module=' . $this->modelName . '&module_id=' . $module_id);
		}else{

			$arr_save['comms_type'] = ucfirst($comms_type);
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
					$this->redirect('/mobile/communications/entry/'.$this->Communication->mongo_id_after_save);
					die;
				} else
					return $this->Communication->mongo_id_after_save;
			}else{
				echo 'Error: ' . $this->Communication->arr_errors_save[1];die;
			}
		}
	}
    function coms_delete($id) {
        $this->selectModel('Communication');
        $this->Communication->save(array('_id' => new MongoId($id),'deleted' => true));
        echo 'ok';
        die;
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
			else
				$production_time = ceil($production_time*2)/2;
			return $production_time;

		}
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
	function error_auth(){
		$this->redirect('/');
		echo 'You have no right in this item, please come back. Thank you!'; die;
	}

}
