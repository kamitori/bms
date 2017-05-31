<?php
require_once APP.'Model'.DS.'AppModel.php';
class Closingmonth extends AppModel {
	public $has_field_deleted = false;
	function __construct($db) {
		if(is_object($db)){
			$this->collection = $db->selectCollection('tb_closingmonth');
			$this->collection->ensureIndex(array('deleted'=>1), array('name'=>'deleted_id_key'));
			$this->db = $db;
		}
	}

	 function add(){
		$this->arr_save['deleted'] = false;
		$this->arr_save['date_modified'] = new MongoDate();
		$this->arr_save['modified_by'] =new MongoId($this->user_id());
		$this->arr_save['created_by'] = $this->arr_save['modified_by'];
		$this->arr_save['date_from'] = new MongoDate();
		$this->arr_save['date_to'] = new MongoDate();
		$this->arr_save['description'] = '';
		$this->arr_save['inactive'] = 0;
		$this->save($this->arr_save);
		return $this->mongo_id_after_save; //xuất ra chuỗi để js hiển thị ra html
	}
}