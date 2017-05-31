<?php
require_once APP.'Model'.DS.'AppModel.php';
class Enquiry extends AppModel {
	public function __construct($db) {
		if(is_object($db)){
			$this->collection = $db->selectCollection('tb_enquiry');
			$this->collection->ensureIndex(array('deleted'=>1), array('name'=>'deleted_id_key'));
			$this->db = $db;
		}
	}

	public $arr_default_before_save = array();
	public function add() {
		$arr_save = array();
		$arr_save['no'] = $this->get_auto_code('no');
		$arr_save['web'] = '';
		$arr_save['name'] = '';
		$arr_save['default_country'] = '';
		$arr_save['default_country_id'] = "CA";
		$arr_save['default_province_state_id'] = 0;
		$arr_save['default_address_1'] = '';
		$arr_save['default_address_2'] = '';
		$arr_save['default_address_3'] = '';
		$arr_save['default_town_city'] = '';
		$arr_save['default_province_state'] = '';
		$arr_save['default_zip_postcode'] = '';
		$arr_save['our_rep'] = $_SESSION['arr_user']['contact_name'];
		$arr_save['our_rep_id'] = new MongoId($_SESSION['arr_user']['contact_id']);
		$arr_save['date'] = new MongoDate(strtotime(date('Y-m-d')));

		$arr_save = array_merge($arr_save, $this->arr_default_before_save);
		if ($this->save($arr_save)) {
			return $this->mongo_id_after_save;
		} else {
			echo 'Error: ' . $this->arr_errors_save[1];die;
		}

	}
}