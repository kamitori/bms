<?php
require_once APP.'Model'.DS.'AppModel.php';
class Hooksetting extends AppModel {
	public $has_field_deleted = false;
	function __construct($db) {
		if(is_object($db)){
			$this->collection = $db->selectCollection('tb_hook');
			$this->collection->ensureIndex(array('deleted'=>1), array('name'=>'deleted_id_key'));
			$this->db = $db;
		}
	}
}