<?php
require_once APP.'Model'.DS.'AppModel.php';
class Studio extends AppModel {
	function __construct($db) {
		if(is_object($db)){
			$this->collection = $db->selectCollection('tb_studio');
			$this->collection->ensureIndex(array('deleted'=>1), array('name'=>'deleted_id_key'));
			$this->collection->ensureIndex(array('country_id'=>1), array('name'=>'country_id_key'));
			$this->db = $db;
		}
		$this->has_field_deleted = false;
	}
}
