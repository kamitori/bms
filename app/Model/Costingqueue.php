<?php
require_once APP.'Model'.DS.'AppModel.php';
class Costingqueue extends AppModel {
	function __construct($db) {
		if(is_object($db)){
			$this->collection = $db->selectCollection('tb_costingqueue');
			$this->collection->ensureIndex(array('deleted'=>1), array('name'=>'deleted_id_key'));
			$this->db = $db;
		}
	}
	public function add($arr_data) {
		$arr_save = array();
		$arr_save['no'] = $this->get_auto_code('no');
		if (isset($arr_tmp['no']))
			$arr_save['no'] = $arr_tmp['no'] + 1;
		$arr_save['product_id'] = $arr_data['product_id'];
		$arr_save['costings'] = isset($arr_data['costings']) ? $arr_data['costings'] : array();
		$arr_save['deleted'] = false;
		$arr_save['status'] = 'New';
		if ($this->save($arr_save)) {
			return $this->mongo_id_after_save;
		} else {
			echo 'Error: ' . $this->arr_errors_save[1];die;
		}

	}
}