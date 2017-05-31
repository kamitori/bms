<?php
require_once APP.'Model'.DS.'AppModel.php';
class Province extends AppModel {
	function __construct($db) {
		if(is_object($db)){
			$this->collection = $db->selectCollection('tb_province');
			$this->collection->ensureIndex(array('deleted'=>1), array('name'=>'deleted_id_key'));
			$this->collection->ensureIndex(array('country_id'=>1), array('name'=>'country_id_key'));
			$this->db = $db;
		}
		$this->has_field_deleted = false;
	}

	public function get_provinces($country_id){
		$Provinces = $this->collection->find(array('deleted'=>false,'country_id'=>$country_id));
		$arr_return = array();
		foreach ($Provinces as  $province) {
			$arr_return[$province['key']] = $province['name'];
		}
		return $arr_return;
	}

	public function get_all_provinces(){
		$provinces = $this->collection->find(array('deleted'=>false));
		$arr_return = array();
		foreach ($provinces as  $province) {
			$arr_return[$province['country_id']][$province['key']] = $province['name'];
		}
		return $arr_return;
	}
}