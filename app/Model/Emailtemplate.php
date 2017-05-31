<?php
require_once APP.'Model'.DS.'AppModel.php';
class Emailtemplate extends AppModel {
	function __construct($db) {
		if(is_object($db)){
			$this->collection = $db->selectCollection('tb_emailtemplate');
			$this->collection->ensureIndex(array('deleted'=>1), array('name'=>'deleted_id_key'));
			$this->db = $db;
		}
	}
	public function add() {
		$arr_save = array();
		$arr_save['no'] = $this->get_auto_code('no');
		$arr_save['name'] = 'This is new record.';
		$arr_save['type'] = 'Auto Send';
		$arr_save['type_id'] = 'auto_send';
		$arr_save['folder'] = '';
		$arr_save['folder_id'] = '';
		$arr_save['name'] = 'This is new record.';
		$arr_save['template']= '';//'<span class="field_span" contenteditable="false" rel="{{CONTENT}}" unselectable="on">Content</span>';
		$arr_save['deleted'] = false;
		if ($this->save($arr_save)) {
			return $this->mongo_id_after_save;
		} else {
			echo 'Error: ' . $this->arr_errors_save[1];die;
		}

	}
}