<?php
require_once APP.'Model'.DS.'AppModel.php';
class Equipment extends AppModel {

	function __construct($db) {
		if(is_object($db)){
			$this->collection = $db->selectCollection('tb_equipment');
			$this->collection->ensureIndex(array('deleted'=>1), array('name'=>'deleted_id_key'));
			$this->db = $db;
		}
	}

	// Popup form orther module
	public function arr_asset($ids = ''){
		$arr_equipment = $this->select_all(array('arr_order' => array('name' => 1)));
		$arr = array();
		if($ids==''){
			foreach($arr_equipment as $keys=>$values){
				$arr[$keys] = $values;
			}
			return $arr;
		}else{
			foreach($arr_equipment as $keys=>$values){
				if((string)$keys==$ids)
					return $values;
			}
		}
		return 0;
	}


	public function speed_asset($ids = ''){
		$arr_equipment = $this->select_all(array('arr_order' => array('name' => 1),'arr_field' => array('speed_per_hour')));
		$arr = array();
		if($ids==''){
			foreach($arr_equipment as $keys=>$values){
				if(isset($values['speed_per_hour']))
				$arr[$keys] = (float)$values['speed_per_hour'];
			}
			return $arr;
		}else{
			foreach($arr_equipment as $keys=>$values){
				if((string)$keys==$ids && isset($values['speed_per_hour']))
					return (float)$values['speed_per_hour'];
			}
		}
		return 0;
	}


	public function speed_asset_old($ids = ''){
		$arr_equipment = $this->select_one(array('_id'=> new MongoId($ids)));
		if(!empty($arr_equipment)&&isset($arr_equipment['speed_per_hour']))
			return (float)$arr_equipment['speed_per_hour'];
		return 0;
	}


	public function select_combobox_asset(){
		$arr_equipment = $this->select_all(array('arr_order' => array('name' => 1),'arr_field' => array('name')));
		$arr = array();
		foreach($arr_equipment as $keys=>$values){
			if(isset($values['name']))
			$arr[(string)$values['_id']] = $values['name'];
		}
		return $arr;
	}



	public function select_combobox_asset_old(){
		$arr_equipment = $this->select_all(array('arr_order' => array('name' => 1),'arr_field' => array('name')));
		$arr = array();
		foreach($arr_equipment as $keys=>$values){
			if(isset($values['name']))
			$arr[(string)$values['_id']] = $values['name'];
		}
		return $arr;
	}

}


