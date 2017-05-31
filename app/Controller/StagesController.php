<?php
App::uses('AppController', 'Controller');
class StagesController extends AppController {

	var $modelName = 'Stage';
	var $name = 'Stages';
	var $sub_tab_default = 'general';

	public function beforeFilter( ){
		// goi den before filter cha
		parent::beforeFilter();

		$this->set('title_entry', 'Stages');
	}

	function auto_save(){
		if(!empty($this->data)){
			$arr_post_data = $this->data['Stage'];
			$arr_save = $arr_post_data;

			$work_start_sec = $this->Common->strtotime($arr_save['work_start'] . ' 00:00:00');
			$work_end_sec = $this->Common->strtotime($arr_save['work_end'] . ' 00:00:00');
			if( $work_start_sec > $work_end_sec ){
				echo 'date_work'; die;
			}
			$arr_save['work_start'] = new MongoDate($work_start_sec);
			$arr_save['work_end'] = new MongoDate($work_end_sec);

			if(strlen(trim($arr_save['our_rep_id'])) > 0)
			$arr_save['our_rep_id'] = new MongoId($arr_save['our_rep_id']);

			if(strlen(trim($arr_save['job_id'])) > 0)
			$arr_save['job_id'] = new MongoId($arr_save['job_id']);

			$error = 0;
			if( !$error ){
				$this->selectModel('Stage');
				if( $this->Stage->save($arr_save) ){
					echo 'ok';
				}else{
					echo 'Error: ' . $this->Stage->arr_errors_save[1];
				}
			}
		}
		die;
	}

	function delete($id=0){
		if(!$this->check_permission($this->name.'_@_entry_@_delete'))
			$this->error_auth();
		$arr_save['_id'] = $id;
		$arr_save['deleted'] = true;
		$error = 0;
		if( !$error ){
			$this->selectModel('Stage');
			if( $this->Stage->save($arr_save) ){
				$this->redirect('/stages/entry');
			}else{
				echo 'Error: ' . $this->Stage->arr_errors_save[1];
			}
		}
		die;
	}

	function _add_get_info_save($work_start_sec = null){
		$this->selectModel('Stage');
		$arr_tmp = $this->Stage->select_one(array(), array(), array('no' => -1));
		$arr_save = array();
		$arr_save['no'] = 1;
		if(isset($arr_tmp['no'])){
			$arr_save['no'] = $arr_tmp['no'] + 1;
		}

		$arr_save['work_start'] = new MongoDate( strtotime( date('Y-m-d' . ' 08:00:00') ) );
		$arr_save['work_end'] = new MongoDate( strtotime( date('Y-m-d') . ' 09:00:00') );

		if(isset($work_start_sec) && $work_start_sec > 0){
			$arr_save['work_end'] = $arr_save['work_start'] = new MongoDate($work_start_sec);
		}else{

			$arr_save['work_end'] = $arr_save['work_start'] = new MongoDate(strtotime(date('Y-m-d H:00:00')) + 3600);
		}
		return $arr_save;
	}

	public function add($work_start_sec = 0){
		$arr_save = $this->_add_get_info_save($work_start_sec );

		if( $this->Stage->save($arr_save) ){
			$this->redirect('/stages/entry/' . $this->Stage->mongo_id_after_save);
		}else{
			echo 'Error: ' . $this->Stage->arr_errors_save[1];
		}
		die;
	}

	public function entry( $id = '0', $num_position = -1 ){// echo date('d/m/Y', strtotime('2 Jul, 2013'));die;

		$arr_tmp = $this->entry_init( $id, $num_position, 'Stage', 'stages');

		$arr_tmp['work_start'] = (is_object($arr_tmp['work_start']))?date('m/d/Y', $arr_tmp['work_start']->sec):'';
		$arr_tmp['work_end'] = (is_object($arr_tmp['work_end']))?date('m/d/Y', $arr_tmp['work_end']->sec):'';

		$arr_tmp1['Stage'] = $arr_tmp;
		$this->data = $arr_tmp1;

		$this->selectModel('Setting');
		$this->set( 'arr_stage_stage', $this->Setting->select_option(array('setting_value' => 'stages_stage'), array('option')) );
		$this->set( 'arr_stages_status', $this->Setting->select_option(array('setting_value' => 'stages_status'), array('option')) );

		$arr_contact_id = array();
		if(isset($arr_tmp['our_rep_id']))
			$arr_contact_id[] = $arr_tmp['our_rep_id'];

		// hiển thị cho footer
		$this->show_footer_info($arr_tmp, $arr_contact_id);

		// Get info for substage
		$this->sub_tab('', $arr_tmp['_id']);
	}

	function resources_auto_save(){
		foreach ($this->data['Stage'] as $value) {
			$arr_save = $value;
		}
		$arr_save['work_start'] = new MongoDate($this->Common->strtotime($arr_save['work_start'] . '' . $arr_save['work_start_hour'] . ':00'));
		$arr_save['work_end'] = new MongoDate($this->Common->strtotime($arr_save['work_end'] . '' . $arr_save['work_end_hour'] . ':00'));
		$this->selectModel('Stage');
		if( $this->Stage->save($arr_save) ){
			echo 'ok';
		}else{
			echo 'Error: ' . $this->Stage->arr_errors_save[1];
		}
		die;
	}

	function general($stage_id){
		$this->set('stage_id', $stage_id);

		$this->selectModel('Stage');
		$arr_stage = $this->Stage->select_one(array('_id' => new MongoId($stage_id)), array('contacts'));
		$this->set('arr_stage', $arr_stage);

	}

	function general_window_contact_choose($stage_id, $contact_id, $contact_name){

		$this->selectModel('Stage');
		$arr_stage = $this->Stage->select_one(array('_id' => new MongoId($stage_id)), array('contacts'));
		$check_not_exist = true;
		if( isset($arr_stage['contacts']) ){
			foreach ($arr_stage['contacts'] as $value) {
				if( (string)$value['contact_id'] == $contact_id ){
					$check_not_exist = false;
				}
			}
		}

		if($check_not_exist){

			$this->Stage->collection->update(
				array('_id' => new MongoId($stage_id)),
				array('$push' => array(
						'contacts' => array (
							'contact_name' => $contact_name,
							'contact_id' => new MongoId($contact_id),
							'default' => false,
							'deleted' => false
						)
					)
				)
			);
			echo 'ok';

		}else{
			echo 'Error this contact is selected before';
		}
		die;
		// $this->addresses($company_id);
		// $this->render('addresses');
	}

	function general_choose_manager( $stage_id, $option_id, $sum ){

		// gán lại key deleted để không bị mất
		$this->selectModel('Stage');

		$id = new MongoId($stage_id);
		for ($i=0; $i < $sum; $i++) {
			$this->Stage->collection->update(
				array('_id' => $id),
				array('$set' => array( 'contacts.'.$i.'.default' => false ) )
			);
		}
		$this->Stage->collection->update(
			array('_id' => $id),
			array('$set' => array( 'contacts.'.$option_id.'.default' => true, 'contacts_default_key' => $option_id ) )
		);

		echo 'ok';
		die;
	}

	function general_delete_contact( $stage_id, $key ){

		$this->selectModel('Stage');
		$this->Stage->collection->update(
			array('_id' => new MongoId($stage_id)),
			array('$set' => array(
					'contacts.'.$key.'.deleted' => true
				)
			)
		);
		echo 'ok';
		die;
	}

	function resources($stage_id){

		// get all equipments are used for this stage
		$this->selectModel('Stage');
		$arr_stage = $this->Stage->select_all(array(
			'arr_where' => array('stage_id' => new MongoId($stage_id)),
			'arr_order' => array('work_start' => 1)
		));
		$this->set( 'arr_stage', $arr_stage );
		$this->set( 'stage_id', $stage_id );
		$this->selectModel('Setting');
		$this->set( 'arr_stages_status', $this->Setting->select_option(array('setting_value' => 'stages_status'), array('option')) );
	}

	function resources_delete($stage_id){
		$arr_save['_id'] = $stage_id;
		$arr_save['deleted'] = true;
		$this->selectModel('Stage');
		if( $this->Stage->save($arr_save) ){
			echo 'ok';
		}else{
			echo 'Error: ' . $this->Stage->arr_errors_save[1];
		}
		die;
	}

	function resources_window_list_contact($stage_id){
		$this->selectModel('User');
		$arr_user = $this->User->select_all();
		$this->set( 'arr_user', $arr_user );
		$this->set( 'stage_id', $stage_id );
	}

	function resources_window_choose($stage_id, $type, $name = '' ){
		$arr_save['name'] = $name;
		$arr_save['type'] = $type;
		$arr_save['status'] = 0;
		$arr_save['stage_id'] = new MongoId($stage_id);

		$this->selectModel('Stage');
		$arr_stage = $this->Stage->select_one(array('_id' => new MongoId($stage_id)));

		$arr_save['work_start'] = $arr_stage['work_start'];
		$arr_save['work_end'] = $arr_stage['work_end'];
		$this->selectModel('Stage');
		if( $this->Stage->save($arr_save) ){
			echo 'ok';
		}else{
			echo 'Error: ' . $this->Stage->arr_errors_save[1];
		}
		die;
	}

	function resources_window_list_asset($stage_id){
		$this->selectModel('Equipment');
		$arr_equipment = $this->Equipment->select_all();
		$this->set( 'arr_equipment', $arr_equipment );
		$this->set( 'stage_id', $stage_id );
	}

	function timelog(){
	}
	function expensive(){
	}

	function other(){
	}

	function lists(){
		$this->selectModel('Stage');
		$arr_stages = $this->Stage->select_all(array(
			'arr_order' => array('_id' => -1)
		));
		$this->set( 'arr_stages', $arr_stages );

		$this->selectModel('Setting');
		$this->set( 'arr_stage_stage', $this->Setting->select_option(array('setting_value' => 'stages_stage'), array('option')) );
		$this->set( 'arr_stages_status', $this->Setting->select_option(array('setting_value' => 'stages_status'), array('option')) );
	}

	function lists_delete($id=0){
		$arr_save['_id'] = $id;
		$arr_save['deleted'] = true;
		$error = 0;
		if( !$error ){
			$this->selectModel('Stage');
			if( $this->Stage->save($arr_save) ){
				echo 'ok';
			}else{
				echo 'Error: ' . $this->Stage->arr_errors_save[1];
			}
		}
		die;
	}

	// ================================== CALENDAR ====================================
	public function calendar( $date_from_sec = '', $date_to_sec = '' ){ // calendar week

		$this->set('set_footer', '../Stages/calendar_footer');
		$this->Session->write('calendar_last_visit', '/' . $this->request->url);

		$this->selectModel('Setting');
		$arr_option = $this->Setting->select_one(array('setting_value' => 'stages_status'), array('option'));
		$arr_status = $arr_status_color = array();
		foreach ($arr_option['option'] as $key => $value) {
			if($value['deleted'])continue;
			$arr_status[$key] = $value['name'];
			$arr_status_color[$key] = $value['color'];
		}
		$this->set( 'arr_stages_status', $arr_status );
		$this->set( 'arr_status_color', $arr_status_color );

		// get all stage
		if( $date_from_sec == '' ){
			if( date('N') == 1 ){
				$date_from_sec = strtotime(date('Y-m-d'));
				$date_to_sec = strtotime('next Sunday');

			}elseif( date('N') == 7 ){
				$date_from_sec = strtotime('last Monday');
				$date_to_sec = strtotime(date('Y-m-d'));

			}else{
				$date_from_sec = strtotime('last Monday');
				$date_to_sec = strtotime('next Sunday');
			}
		}
		$this->set( 'date_from_sec', $date_from_sec );
		$this->set( 'date_to_sec', $date_to_sec );

		$arr_time_sec['prev'] = array( strtotime('last Monday', $date_from_sec), strtotime('last Sunday', $date_from_sec) );
		$arr_time_sec['next'] = array( strtotime('next Monday', $date_to_sec), strtotime('next Sunday', $date_to_sec) );
		$this->set( 'arr_time_sec', $arr_time_sec );

		$arr_where['$or'] = array(
			array(
				'work_start' => array( '$lte' => new MongoDate($date_from_sec) ),
				'work_end' => array( '$gte' => new MongoDate($date_from_sec))
			),
			array(
				'work_start' => array( '$lte' => new MongoDate($date_to_sec) ),
				'work_end' => array( '$gte' => new MongoDate($date_to_sec))
			),
			array(
				'work_start' => array( '$gte' => new MongoDate($date_from_sec) ),
				'work_end' => array( '$lte' => new MongoDate($date_to_sec))
			)
		);

		$this->selectModel('Stage');
		$arr_stages_tmp = $this->Stage->select_all(array(
			'arr_where' => $arr_where,
			'limit' => 1000,
			'maxLimit' => 1000
		));
		$arr_stages = array();
		foreach ($arr_stages_tmp as $value) {
			$arr_stages[] = $value;
		}
		$this->set( 'arr_stages', $arr_stages );

		$this->selectModel('Contact');
		$arr_contact = $this->Contact->select_list(array(
			'arr_where' => array('is_employee' => 1),
			'arr_field' => array('_id', 'first_name', 'last_name'),
			'arr_order' => array('first_name' => 1),
		));
		$this->set( 'arr_contact', $arr_contact );

		$this->layout = 'calendar';
	}

	public function _get_beginning_month_datetime( $current_view_date ){
		$time_sec = strtotime(date($current_view_date));

		// tìm ngày đầu tiên của tuần chứa ngày 01
		if( date('N', $time_sec) == 1 ){
			$date_from_sec = strtotime(date('Y-m-d', $time_sec));

		}else{
			$date_from_sec = strtotime('last Monday', $time_sec);
		}
		return $date_from_sec;
	}

	public function calendar_month( $current_view_date = 'Y-m-01' ){

		$this->set('set_footer', '../Stages/calendar_footer');
		$this->Session->write('calendar_last_visit', '/' . $this->request->url);

		$this->selectModel('Setting');
		$arr_option = $this->Setting->select_one(array('setting_value' => 'stages_status'), array('option'));
		$arr_status = $arr_status_color = array();
		foreach ($arr_option['option'] as $key => $value) {
			if($value['deleted'])continue;
			$arr_status[$key] = $value['name'];
			$arr_status_color[$key] = $value['color'];
		}
		$this->set( 'arr_stages_status', $arr_status );
		$this->set( 'arr_status_color', $arr_status_color );

		// get all stage
		// echo ;
		$date_from_sec = $this->_get_beginning_month_datetime($current_view_date);
		$date_to_sec = $date_from_sec + 41*DAY;
		$this->set( 'date_from_sec', $date_from_sec );
		$this->set( 'date_to_sec', $date_to_sec );

		$this->set( 'current_view_date', $current_view_date );

		$arr_time_sec['prev'] = array( date('Y-m-01', strtotime('first day of previous month', strtotime(date($current_view_date)))), '' );
		$arr_time_sec['next'] = array( date('Y-m-01', strtotime('first day of next month', strtotime(date($current_view_date)))), '' );
		$this->set( 'arr_time_sec', $arr_time_sec );

// echo date('d/m/Y',$date_from_sec ); die;
// echo date('d/m/Y',$date_to_sec ); die;

		$arr_where['$or'] = array(
			array(
				'work_start' => array( '$lte' => new MongoDate($date_from_sec) ),
				'work_end' => array( '$gte' => new MongoDate($date_from_sec))
			),
			array(
				'work_start' => array( '$lte' => new MongoDate($date_to_sec) ),
				'work_end' => array( '$gte' => new MongoDate($date_to_sec))
			),
			array(
				'work_start' => array( '$gte' => new MongoDate($date_from_sec) ),
				'work_end' => array( '$lte' => new MongoDate($date_to_sec))
			)
		);

		$this->selectModel('Stage');
		$arr_stages_tmp = $this->Stage->select_all(array(
			'arr_where' => $arr_where,
			'limit' => 1000,
			'maxLimit' => 1000
		));

		$arr_stages = $arr_contact_id = array();
		foreach ($arr_stages_tmp as $value) {
			$arr_stages[] = $value;
			// $arr_contact_id[] = $value['contacts'][$value['contacts_default_key']]['contact_id'];
		}
		$this->set( 'arr_stages', $arr_stages );

		$this->layout = 'calendar';
	}

	public function calendar_day( $date_from_sec = '' ){

		$this->set('set_footer', '../Stages/calendar_footer');
		$this->Session->write('calendar_last_visit', '/' . $this->request->url);

		$this->selectModel('Setting');
		$arr_option = $this->Setting->select_one(array('setting_value' => 'stages_status'), array('option'));
		$arr_status = $arr_status_color = array();
		foreach ($arr_option['option'] as $key => $value) {
			if($value['deleted'])continue;
			$arr_status[$key] = $value['name'];
			$arr_status_color[$key] = $value['color'];
		}
		$this->set( 'arr_stages_status', $arr_status );
		$this->set( 'arr_status_color', $arr_status_color );

		// get all stage
		if( $date_from_sec == '' ){
			$date_from_sec = strtotime(date('Y-m-d'));
		}

		$date_to_sec = $date_from_sec + DAY - 1;

		$arr_time_sec['prev'] = array( $date_from_sec - DAY, '' );
		$arr_time_sec['next'] = array( $date_from_sec + DAY, '' );
		$this->set( 'arr_time_sec', $arr_time_sec );

		$this->set( 'date_from_sec', $date_from_sec );
		$this->set( 'date_to_sec', $date_to_sec );

		$arr_where['$or'] = array(
			array(
				'work_start' => array( '$lte' => new MongoDate($date_from_sec) ),
				'work_end' => array( '$gte' => new MongoDate($date_from_sec))
			),
			array(
				'work_start' => array( '$lte' => new MongoDate($date_to_sec) ),
				'work_end' => array( '$gte' => new MongoDate($date_to_sec))
			),
			array(
				'work_start' => array( '$gte' => new MongoDate($date_from_sec) ),
				'work_end' => array( '$lte' => new MongoDate($date_to_sec))
			)
		);

		$this->selectModel('Stage');
		$arr_stages_tmp = $this->Stage->select_all(array(
			'arr_where' => $arr_where,
			'limit' => 1000,
			'maxLimit' => 1000
		));
		$this->set( 'arr_stages', $arr_stages_tmp );

		$this->layout = 'calendar';
	}

	// Popup form orther module
	public function popup( $key = ""){

		$this->set('key', $key);

		$cond = array();
		// if(!empty($this->data)){
		// 	$arr_post = $this->data['Stage'];
		// 	$cond['name'] = new MongoRegex('/'.$arr_post['name'].'/i');
		// 	$cond['inactive'] = $arr_post['inactive'];
		// 	if( is_numeric($arr_post['is_customer']) )
		// 		$cond['is_customer'] = $arr_post['is_customer'];
		// }

		$this->selectModel('Stage');
		$arr_stages = $this->Stage->select_all(array(
			'arr_where' => $cond,
			'arr_order' => array('_id' => -1),
			// 'arr_field' => array('name', 'is_customer', 'is_employee', 'default_address_1', 'default_address_2', 'default_address_3', 'default_town_city', 'default_country_name', 'default_province_state_name', 'default_zip_postcode', 'phone')
		));
		$this->set( 'arr_stages', $arr_stages );

		$this->layout = 'ajax';
	}
}