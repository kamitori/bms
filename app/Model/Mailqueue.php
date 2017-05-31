<?php
require_once APP.'Model'.DS.'AppModel.php';
class Mailqueue extends AppModel {
	function __construct($db) {
		if(is_object($db)){
			$this->collection = $db->selectCollection('tb_mailqueue');
			$this->collection->ensureIndex(array('deleted'=>1), array('name'=>'deleted_id_key'));
			$this->db = $db;
		}
	}
	public function add($arr_data) {
		$arr_save = array();
		$arr_save['no'] = $this->get_auto_code('no');
		$arr_save['to'] = $arr_data['to'];
		$arr_save['cc'] = isset($arr_data['cc']) ? $arr_data['cc'] : '';
		$arr_save['from'] = $arr_data['from'];
		$arr_save['from_id'] = $arr_data['from_id'];
		$arr_save['status']= 'New';
		$arr_save['prior']= isset($arr_data['prior']) ? $arr_data['prior'] : 5;
		$arr_save['attachments'] = isset($arr_data['attachments']) ? $arr_data['attachments'] : '';
		$arr_save['subject'] = $arr_data['subject'];
		$arr_save['template'] = $arr_data['template'];
		$arr_save['deleted'] = false;
		$arr_save['try'] = 0;
		$arr_save['failed_reason'] = array();
		if ($this->save($arr_save)) {
			return $this->mongo_id_after_save;
		} else {
			echo 'Error: ' . $this->arr_errors_save[1];die;
		}

	}
}