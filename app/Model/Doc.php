<?php
require_once APP.'Model'.DS.'AppModel.php';
class Doc extends AppModel {
	function __construct($db) {
		if(is_object($db)){
			$this->collection = $db->selectCollection('tb_document');
			$this->collection->ensureIndex(array('deleted'=>1), array('name'=>'deleted_id_key'));
			$this->db = $db;
		}
	}

	public function add() {
        $arr_save = array();
        $arr_save['no'] = $this->get_auto_code('no');
        $arr_save['name'] = '';
        $arr_save['category'] = '';
        $arr_save['ext'] = '';
        $arr_save['description'] = '';
        $arr_save = array_merge($arr_save, $this->arr_default_before_save);
        if ($this->save($arr_save)) {
            return $this->mongo_id_after_save;
        } else {
            echo 'Error: ' . $this->arr_errors_save[1];die;
        }

    }
}