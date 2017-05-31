<?php
require_once APP.'Model'.DS.'AppModel.php';
class Chart extends AppModel {
	public $arr_settings = array(
								'module_name' => 'Chart'
							);
	public $arr_temp = array();
	public $temp = '';
	public $arr_key_nosave = array('setup','none','none2','none3','none4','none5','mongo_id');
	public $arr_type_nosave = array('autocomplete','id');
	public $subdb;
	public $subcolection;
	function __construct($db) {
		if(is_object($db)){
			$this->_setting();
			$this->collection = $db->selectCollection($this->arr_settings['colection']);
			$this->collection->ensureIndex(array('deleted'=>1), array('name'=>'deleted_id_key'));
			$this->db = $db;
			
			//sub colection
			if(isset($this->arr_settings['subcolection'])){
				$this->subdb = $db;
				$this->subcolection = $this->subdb->selectCollection($this->arr_settings['subcolection']);
			}
			$this->change_language();
		}
	}


	// Thêm record, gán các giá trị mặc định và gán field $field = $values,
	public function add($field='',$values=''){
		$this->arr_temp = array();
		if($this->arrfield()){
			$this->arr_temp[$field] = $values;
			$this->arr_temp['code'] = $this->get_auto_code('code');
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
			if(preg_match("/_date$/",$field))
				$arr_temp[$field] = new MongoDate($values);
			else
				$arr_temp[$field] = $values;
			if($this->save($arr_temp))
				return $ids.'||'.$arr_temp[$field];
	}
	
}