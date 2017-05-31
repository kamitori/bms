<?php

require_once APP . 'Model' . DS . 'AppModel.php';

class Country extends AppModel {

	public function __construct($db) {
		if (is_object($db)) {
			$this->collection = $db->selectCollection('tb_country');
			$this->collection->ensureIndex(array('deleted' => 1), array('name' => 'deleted_id_key'));
			$this->db = $db;
		}
	}

	public function get_countries(){
		$countries = $this->collection->find(array('deleted'=>false));
		$arr_return = array();
		foreach ($countries as  $country) {
			$arr_return[$country['value']] = $country['name'];
		}
		return $arr_return;
	}
}
