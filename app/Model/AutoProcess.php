<?php
require_once APP.'Model'.DS.'AppModel.php';
class AutoProcess extends AppModel {
	function __construct($db) {
		if(is_object($db)){
			$this->collection = $db->selectCollection('tb_autoprocess');
			$this->collection->ensureIndex(array('deleted'=>1), array('name'=>'deleted_id_key'));
			$this->db = $db;
		}
	}
	public function add() {
		$arr_tmp = $this->select_one(array(), array(), array('no' => -1));
		$arr_save = array();
		$arr_save['no'] = 1;
		if (isset($arr_tmp['no']))
			$arr_save['no'] = $arr_tmp['no'] + 1;
		$arr_save['name'] = 'undefined';
		$arr_save['module_name']= '';
		$arr_save['controller']= '';
		$arr_save['email_template']= '';
		$arr_save['description']= '';
		$arr_save['deleted'] = false;
		if ($this->save($arr_save)) {
			return $this->mongo_id_after_save;
		} else {
			echo 'Error: ' . $this->arr_errors_save[1];die;
		}

	}
	public function remove($id){
		try{
			$this->collection->remove(array('_id'=>new MongoId($id)),array('justOne' => true));
			return true;
		}catch(MongoCursorException $e){
			$this->arr_errors = array( $e->getCode(), $e->getMessage() );
			return false;
		}
	}
}