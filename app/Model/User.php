<?php
require_once APP.'Model'.DS.'AppModel.php';
class User extends AppModel {

	public $arr_settings = array(
								'module_name' => 'User'
							);

	public function __construct($db) {
		if(is_object($db)){
			$this->_setting();
			$this->collection = $db->selectCollection($this->arr_settings['colection']);
			$this->collection->ensureIndex(array('deleted'=>1), array('name'=>'deleted_id_key'));
			// $this->collection->ensureIndex(array('deleted'=>1), array('name'=>'deleted_id_key', "unique"=>1, "dropDups" => 1));
			$this->db = $db;
			if(!isset($_SESSION['default_lang']))
				$_SESSION['default_lang'] = DEFAULT_LANG;
			$this->change_language($_SESSION['default_lang']);
		}
	}
}