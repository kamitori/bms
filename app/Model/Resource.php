<?php
require_once APP.'Model'.DS.'AppModel.php';
class Resource extends AppModel {
	function __construct($db) {
		if(is_object($db)){
			$this->collection = $db->selectCollection('tb_resource');
			$this->collection->ensureIndex(array('deleted'=>1), array('name'=>'deleted_id_key'));
			$this->collection->ensureIndex(array('module_id'=>1), array('name'=>'module_id_key'));
		}
	}
}