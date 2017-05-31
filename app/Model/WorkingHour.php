<?php
require_once APP.'Model'.DS.'AppModel.php';
class WorkingHour extends AppModel {
	function __construct($db) {
		if(is_object($db)){
			$this->collection = $db->selectCollection('tb_working_hour');
			$this->collection->ensureIndex(array('deleted'=>1), array('name'=>'deleted_id_key'));
			$this->db = $db;
		}
	}

	public $arr_default_before_save = array();
	public function add() {
		$arr_save = array();
		$arr_save['no'] = $this->get_auto_code('no');

		//Minh, For services without session
		if(isset($_SESSION['arr_user']) && isset($_SESSION['arr_user']['our_rep_id']) && isset($_SESSION['arr_user']['our_rep']))
		{
			$our_rep_id = $_SESSION['arr_user']['our_rep_id'];
			$our_rep = $_SESSION['arr_user']['our_rep'];
		}
		else
		{
			$our_rep_id = isset($_GET['our_rep_id']) ? $_GET['our_rep_id'] : null;
			$our_rep = isset($_GET['our_rep']) ? $_GET['our_rep'] : null;
		}
		$arr_save['our_rep'] = $our_rep;
		$arr_save['our_rep_id'] = new MongoId($our_rep_id);
		$arr_save['date'] = new MongoDate(strtotime(date('Y-m-d')));

		$arr_save = array_merge($arr_save, $this->arr_default_before_save);
		if ($this->save($arr_save)) {
			return $this->mongo_id_after_save;
		} else {
			echo 'Error: ' . $this->arr_errors_save[1];die;
		}

	}
}