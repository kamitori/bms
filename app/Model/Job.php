<?php
require_once APP.'Model'.DS.'AppModel.php';
class Job extends AppModel {
	function __construct($db) {
		if(is_object($db)){
			$this->collection = $db->selectCollection('tb_job');
			$this->collection->ensureIndex(array('deleted'=>1), array('name'=>'deleted_id_key'));
			$this->db = $db;
		}
	}

	public $arr_default_before_save = array();
	public function add() {
        $lastJob = $this->select_one(
                                array(
                                    'no' => array(
                                        '$nin' => array(new MongoRegex('/^'.date('y').'-/'))
                                    )
                                ),
                                array('no'),
                                array('no' => -1));
		$arr_save = array();
        $arr_save['no'] = 1;
        if (isset($lastJob['no'])) {
            $arr_save['no'] = $lastJob['no'] + 1;
        }
        $arr_save['status'] = $arr_save['status_id'] = 'Not Started';
        $arr_save['type'] = $arr_save['type_id'] = '';
        $arr_save['name'] = '';
        $arr_save['company_name'] = '';
        $arr_save['contacts_default_key'] = 0;
        $arr_save['contacts'][] = array(
            "contact_name" => $_SESSION['arr_user']['contact_name'],
            "contact_id" => $_SESSION['arr_user']['contact_id'],
            "default" => true,
            "deleted" => false
        );
        $arr_save['work_end'] = $arr_save['work_start'] = new MongoDate(strtotime(date('Y-m-d H:00:00')) + 3600);
		$arr_save = array_merge($arr_save, $this->arr_default_before_save);
		if ($this->save($arr_save)) {
			return $this->mongo_id_after_save;
		} else {
			echo 'Error: ' . $this->arr_errors_save[1];die;
		}

	}

	public function tao_data_demo_job($arr_company_id){
		$arr_company_id[] = '';
		$where = array();
		$where['company_id']['$nin'] = $arr_company_id;
		$arr_set = array('deleted'=>true);
		$this->collection->update($where, array( '$set' => $arr_set ), array('multiple' => true));
	}
}