<?php
class HomesController extends MobileAppController {
	function beforeFilter() {
		parent::beforeFilter();

	}
	public function index(){
		$arr_data['alert'] = $this->alerts_get_data();
		$this->set('arr_data',$arr_data);
	}
	function alerts_get_data(){
		// ============================ check mesage receive ================================================
		$cond = array();
		$cond['contact_to_id'] = $_SESSION['arr_user']['contact_id'];
		$cond['comms_type'] = 'Message';
		$cond['viewed'] = false;

		$this->selectModel('Communication');
		$arr_communication = $this->Communication->select_all(array(
			'arr_where' => $cond
		));
		$arr_data['communication'] = $arr_communication;

		// ============================ check Task late ========================================================
		$cond = array();
		$cond['work_end'] = array( '$lte' => new MongoDate(strtotime('now')) );
		$cond['status'] = array( '$in' => array('New', 'Confirmed') ) ;
		$cond['$or']=array(
			array('contacts' => array('$elemMatch' => array('contact_id' => $_SESSION['arr_user']['contact_id']) )),
			array('our_rep_id' => $_SESSION['arr_user']['contact_id'])
		);
		$this->selectModel('Task');
		$arr_task = $this->Task->select_all(array(
			'arr_where' => $cond
		));
		$arr_data['task'] = $arr_task;

		// return
		return $arr_data;
	}

}