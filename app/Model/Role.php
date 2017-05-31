<?php
require_once APP.'Model'.DS.'AppModel.php';
class Role extends AppModel {
	public $has_field_deleted = false;
	function __construct($db) {
		if(is_object($db)){
			$this->collection = $db->selectCollection('tb_role');
			$this->collection->ensureIndex(array('deleted'=>1), array('name'=>'deleted_id_key'));
			$this->db = $db;
		}
	}

	public function save(array $arr = array()) {
		$array = array('@arr_menu_','@arr_permission_','arr_inactive_permission');
		foreach($array as $value){
			if(strpos($value, '@') !== false){
	    		$value = ltrim($value, '@');
	    		$arr_caches = $this->get_cache_keys_diff('',$value);
	    		foreach($arr_caches as $cache){
	    			Cache::delete($cache);
	    		}
	    	} else
	    		Cache::delete($value);
		}
		return parent::save($arr);
	}
}