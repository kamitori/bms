<?php
require_once APP.'Model'.DS.'AppModel.php';
class Timelog extends AppModel {
	public function __construct($db) {
		if(is_object($db)){
			$this->collection = $db->selectCollection('tb_timelog');
			$this->collection->ensureIndex(array('deleted'=>1), array('name'=>'deleted_id_key'));
			$this->db = $db;
		}
	}
}