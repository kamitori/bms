<?php
require_once APP.'Model'.DS.'AppModel.php';
class Support extends AppModel {
	function __construct($db) {
		if(is_object($db)){
			$this->collection = $db->selectCollection('tb_support');
			$this->collection->ensureIndex(array('deleted'=>1), array('name'=>'deleted_id_key'));
			$this->db = $db;
		}
	}
	public function add($arr_data = array()) {
		$arr_save = array();
		$arr_save['no'] = $this->get_auto_code('no');
		$arr_save['name'] = 'This is new record.';
		$arr_save['content'] = 'This is what showed in support page.';
		$arr_save['module'] = '';
		$arr_save['deleted'] = false;
		$arr_save = array_merge($arr_save,$arr_data);
		if ($this->save($arr_save)) {
			return $this->mongo_id_after_save;
		} else {
			echo 'Error: ' . $this->arr_errors_save[1];die;
		}

	}
}