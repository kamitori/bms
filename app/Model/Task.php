<?php
require_once APP.'Model'.DS.'AppModel.php';
class Task extends AppModel {
	public function __construct($db) {
		if(is_object($db)){
			$this->collection = $db->selectCollection('tb_task');
			$this->collection->ensureIndex(array('deleted'=>1), array('name'=>'deleted_id_key'));
			$this->db = $db;
		}
	}

	public $arr_default_before_save = array();
	public function add() {
		$arr_save = array();
		$arr_save['no'] = $this->get_auto_code('no');
		$arr_save['status_id'] = $arr_save['status'] = 'New';
		$arr_save['type_id'] = $arr_save['type'] = '';
		$arr_save['our_rep_type'] = 'contacts';
		$arr_save['name'] = '';
		$arr_save['company_name'] = '';
		$arr_save['contacts_default_key'] = 0;
		$arr_save['contacts'] = array(); // contacts_default_key
		$arr_save['our_rep'] = $_SESSION['arr_user']['contact_name'];
		$arr_save['our_rep_id'] = new MongoId($_SESSION['arr_user']['contact_id']);
		$arr_save['work_start'] = new MongoDate(strtotime(date('Y-m-d' . ' 08:00:00')));
		$arr_save['work_end'] = new MongoDate(strtotime(date('Y-m-d') . ' 09:00:00'));

		$arr_save = array_merge($arr_save, $this->arr_default_before_save);
		if ($this->save($arr_save)) {
			return $this->mongo_id_after_save;
		} else {
			echo 'Error: ' . $this->arr_errors_save[1];die;
		}
	}

	public function save(array $arr = array())
	{
		if (isset($arr['_id'])) {
			$task = $this->select_one(array('_id' => new MongoId($arr['_id'])), array('salesorder_id'));
			if (isset($task['salesorder_id']) && is_object($task['salesorder_id'])) {
				if (isset($arr['our_rep_type']) && $arr['our_rep_type'] == 'assets') {
					require_once APP.'Model'.DS.'Salesorder.php';
					$OrderModel = new Salesorder($this->db);
					$OrderModel->updateAssetStatus($task['salesorder_id'], $task['_id'], $arr['our_rep'], $arr['status']);
				}
			}
		}
		return parent::save($arr);
	}
}