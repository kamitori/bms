<?php
class EquipmentsController extends AppController {
	var $name = 'Equipments';

	var $modelName = 'Equipment';

	function beforeFilter(){
		parent::beforeFilter();
	}

	public function add(){
		if(!empty($this->data)){
			$arr_post = $this->data['Equipment'];
			$arr_save['name'] = $arr_post['name'];
			$this->selectModel('Equipment');
			if( $this->Equipment->save($arr_save) ){
				$this->set('message', 'Save OK');
			}else{
				$this->set('message', 'Error: ' . $this->Equipment->arr_errors_save[1]);
			}
		}
	}

	// Popup form orther module
	public function popup( $key = ""){
		$this->set('key', $key);
		$this->selectModel('Equipment');
		$arr_equipment = $this->Equipment->select_all(array('arr_order' => array('name' => 1)));
		$this->set( 'arr_equipment', $arr_equipment );
		$this->layout = 'ajax';
	}
}