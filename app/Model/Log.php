<?php
require_once APP.'Model'.DS.'AppModel.php';
class Log extends AppModel {
	public $arr_settings = array(
								'module_name' => 'Log'
							);
	public $arr_temp = array();
	public $temp = '';
	public $arr_key_nosave = array('setup','none','none2','mongo_id');
	public $arr_type_nosave = array('autocomplete','id');

	function __construct($db) {
		if(is_object($db)){
			$this->_setting();
			$this->collection = $db->selectCollection($this->arr_settings['colection']);
			$this->collection->ensureIndex(array('deleted'=>1), array('name'=>'deleted_id_key'));
			$this->db = $db;
			$this->change_language();
		}
	}

	// Thêm record, gán các giá trị mặc định và gán field $field = $values,
	public function add($field='',$values=''){
		$this->arr_temp = array();
		if($this->arrfield()){
			$this->arr_temp[$field] = $values;
			$this->save($this->arr_temp);
			$this->arr_temp = array();
			return $this->mongo_id_after_save.'||'.$values; //xuất ra chuỗi để js hiển thị ra html
		}else
			return '';
	}


	//update field $field  = $values của record có _id là $ids
	public function update($ids='',$field='',$values=''){
			$arr_temp = $cal_price = array();
			$arr_temp['_id'] = $ids;
			$arr_temp[$field] = $values;
			if($this->save($arr_temp))
				return $ids.'||'.$arr_temp[$field];
	}

	 public function save(array $arr = array()){
		try{
			foreach ($arr as $key => $value) {
				if(is_string($value))$arr[$key] = trim($arr[$key]);
				if(is_string($value) && substr($value, 0, 1) != "+" && is_numeric($value) && !is_float($value) && ( substr($value, 0, 1) != "0" || $value == "0" ) )$arr[$key] = (int)$value;
				// if(!is_array($value))
					// if(trim($value) == "")$arr[$key] = " "; // blank to save to db, we have fields, if not mongo will remove field
			}
			if(!isset($arr['_id'])){// add new
				$arr['_id'] = new MongoId();

				//Minh, For services without session
				if(isset($_SESSION['arr_user']))
				{
					$contact_id = $_SESSION['arr_user']['contact_id'];
				}
				else
				{
					$contact_id = isset($_GET['contact_id']) ? $_GET['contact_id'] : null;
				}				
				$arr['created_by'] = $arr['modified_by'] = $contact_id;
				
				$arr['date_modified'] = new MongoDate(strtotime('now'));
				if($this->has_field_deleted)
					$arr = array_merge(array('deleted' => false), $arr);
				$this->collection->insert($arr, array('safe'=>true));

			}else{ //is update
				if(!is_object($arr['_id']))
					$arr['_id'] = new MongoId($arr['_id']);

				$this->old_data = $this->select_one(array( '_id' => new MongoId($arr['_id'])));//old data

				$arr['modified_by'] = $_SESSION['arr_user']['contact_id'];
				$arr['date_modified'] = new MongoDate(strtotime('now'));
				if($this->has_field_deleted)
					$arr = array_merge(array('deleted' => false), $arr);
				$arr_tmp_save = $arr;
				unset($arr_tmp_save['_id']);
				$this->collection->update(array( '_id' => new MongoId($arr['_id'])), array( '$set' => $arr_tmp_save ));
			}
			$this->mongo_id_after_save = $arr['_id'];

			return true;
		}catch(MongoCursorException $e){
			$this->arr_errors_save = array( $e->getCode(), $e->getMessage() );
			return false;
		}
	}


}