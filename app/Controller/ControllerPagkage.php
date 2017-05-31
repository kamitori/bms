<?php

/* Muốn viết lại hàm thì copy từ file BasicsController.php qua rồi chỉnh lại */

App::uses('BasicsController', 'Controller');
class RulesController extends BasicsController {

	function __construct() {
		$this->name = 'Rules';
		$this->modulekey = 'Rule';
	}

	public function beforeFilter(){
		parent::beforeFilter();
	}

	public function entry(){
		parent::entry();
	}


	//List view
	public function lists(){
		parent::lists();
	}


	// Action Add
	public function add(){
		parent::add();
		die;
	}

	// Delete 1 record
	public function delete($ids=0){
		parent::delete();
		die;
	}

	// Action Upload
	public function upload(){
		parent::delete();
		die;
	}

	// Action Options
	public function options(){
		parent::options();
		die;
	}


}