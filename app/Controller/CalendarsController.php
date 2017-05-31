<?php
App::uses('AppController', 'Controller');
class CalendarsController extends AppController {

	var $name = 'Calendars';
	public $helpers = array();
	
	public function beforeFilter( ){
		parent::beforeFilter();
	}

	public function index(){
		// check last type of time and last module a user come, then redirect that info
		if(!$this->Session->check('calendar_last_visit')){
			$calendar_last_visit = '/salesorders/calendar';
			$this->Session->write('calendar_last_visit', $calendar_last_visit);
		}else{
			$calendar_last_visit = $this->Session->read('calendar_last_visit');
		}
		$this->redirect($calendar_last_visit);
	}
}