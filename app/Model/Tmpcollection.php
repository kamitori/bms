<?php
require_once APP.'Model'.DS.'AppModel.php';
class Tmpcollection extends AppModel {
	public $arr_settings = array(
								'module_name' => 'Tmpcollection'
							);
	public $arr_temp = array();
	function __construct($db) {
		if(is_object($db)){
			$this->_setting();
			$this->collection = $db->selectCollection('tb_tmp_collection');
			$this->collection->ensureIndex(array('deleted'=>1), array('name'=>'deleted_id_key'));
			$this->db = $db;
		}
	}

	function _setting(){

	}
}