<?php
require_once APP.'Model'.DS.'AppModel.php';
class SettingMessage extends AppModel {
	public function __construct($db) {
		if(is_object($db)){
			$this->collection = $db->selectCollection('tb_settings_message');
			$this->collection->ensureIndex(array("deleted"=>1), array('name'=>"deleted_id_key"));
		}
	}
}