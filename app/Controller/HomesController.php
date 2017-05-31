<?php
App::uses('AppController', 'Controller');
class HomesController extends AppController {

	public $helpers = array();

	// public $helpers = array('Cache');
	// public $cacheAction = "1 hour";

	public function beforeFilter( ){
		// goi den before filter cha
		$this->selectModel('Version');
		parent::beforeFilter();
	}

	public function iframe_reload_session(){
		echo '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd"><html xmlns="http://www.w3.org/1999/xhtml" lang="en"><head><meta http-equiv="refresh" content="180"><title>Iframe</title></head><body></body></html>'; die;
	}

	public function index(){
		$this->selectModel('Stuffs');
		$logo = $this->Stuffs->select_one(array('value'=>'Logo'));
		$this->set('logo',$logo);
	}

	public function resset_data(){

		$this->selectModel('Contact');
		$arr_tmp = $this->Contact->select_all();
		foreach ($arr_tmp as $key => $value) {

			$arr_tmp = $value;
			$arr_tmp['company'] = 'Anvy';
			$arr_tmp['company_id'] = new MongoId('524d224167b96d6a3c00000e');

			// pr($arr_tmp);die;
			if( $this->Contact->save($arr_tmp) ){
				echo 'ok '.$key . '<br>';
			}else{
				echo 'Error: ' . $this->Contact->arr_errors_save[1];
				die;
			}
		}
		echo 'xong';
		die;
	}

	function remove($model){
		$this->selectModel($model);
		$this->$model->collection->remove(array('company' => 'Anvy Digital Imaging'));
		echo 'ok';
		die;
	}

	function db($model, $id){
		$this->selectModel($model);
		$arr_tmp = $this->$model->select_one(array('_id' => new MongoId($id)));
		unset($arr_tmp['_id']);
		for ($i=0; $i < 1000; $i++) {
			$arr_tmp['name'] = $arr_tmp['name'] . ($i + 1);
			if( $this->$model->save($arr_tmp) ){
				echo 'ok '.$i . '<br>';
			}else{
				echo 'Error: ' . $this->$model->arr_errors_save[1];
				die;
			}
		}
		echo 'ok';
		die;
	}

	public function arr_menu_show_hide( $active, $name ){
		$arr_menu_show_hide = array();
		if( $this->Session->check('arr_menu_show_hide') ){
			$arr_menu_show_hide = $this->Session->read('arr_menu_show_hide');
		}

		if( $active ){
			$arr_menu_show_hide[] = $name;
		}else{

			if(($key = array_search($name, $arr_menu_show_hide)) !== false) {
			    unset($arr_menu_show_hide[$key]);
			}
		}

		$this->Session->write('arr_menu_show_hide', array_unique($arr_menu_show_hide));
		echo 'ok';
		die;
	}

	function get_employee_condition($field = '_id'){
		$this->selectModel('Contact');
		$arr_where = array();
		if(!$this->system_admin){
			$id = $this->Contact->user_id();
			$arr_permission = $this->Contact->select_one(array('_id'=>$id ),array('roles','custom_permission'));
			if(!isset($arr_permission['custom_permission']))
				$arr_permission['custom_permission'] = array();
			$custom_permission = $arr_permission['custom_permission'];
			$arr_roles = isset($arr_permission['roles']['roles']) ? $arr_permission['roles']['roles'] : array();
			if(isset($arr_permission['roles']['roles']))
				unset($arr_permission['roles']['roles']);
			$arr_permission = $arr_permission['roles'];
			$this->selectModel('Role');
			$full = false;
			if(isset($arr_permission['dashboard_@_entry_@_view']) && in_array('all', $arr_permission['dashboard_@_entry_@_view']))
				$full = true;
			if(!$full){
				foreach($arr_roles as $key => $role_id){
					$arr_permissions = $this->Role->select_one(array('_id' => $role_id),array('value'));
					if(!isset($arr_permissions['value']['dashboard_@_entry_@_view'])) {
						unset($arr_roles[$key]);
						continue;
					}
					if( in_array('all',$arr_permissions['value']['dashboard_@_entry_@_view']) ){
						$full = true; break;
					}
					if(in_array('owner',$arr_permissions['value']['dashboard_@_entry_@_view'])){
						unset($arr_roles[$key]);
					}
				}
				if(isset($custom_permission['dashboard_@_entry_@_view']) && !empty($custom_permission['dashboard_@_entry_@_view'])){
					foreach($custom_permission['dashboard_@_entry_@_view'] as $more_id)
						$arr_where['$or'][]['_id'] = $more_id;
				}
				$arr_where['$or'][]['_id'] = $id;
				if(!empty($arr_roles)) {
					$arr_roles = array_values($arr_roles);
					$arr_where['$or'][]['roles.roles'] = array('$in' => $arr_roles);
				}
				$arr_where['$or'] = array_values($arr_where['$or']);
			}
		}
		$arr_where = array_merge($arr_where, array('is_employee' => 1, 'deleted' => false));
		if($field != '_id'){
			$arr_contact = $this->Contact->select_all(array(
								'arr_where' => $arr_where,
								'arr_field' => array('_id'),
								'arr_order' => array('_id' => 1),
				));
			$arr_where = array();
			if($arr_contact->count()){
				foreach($arr_contact as $contact){
					$arr_where[$field]['$in'][] = $contact['_id'];
				}
			}
			if(empty($arr_where))
				$arr_where = array($field => array('$in' => array($id)));
		}
		return $arr_where;
	}

	function get_employee($return = false) {
		$this->selectModel('Contact');
		$arr_where = $this->get_employee_condition();
		return $this->Contact->select_list(array(
			'arr_where' => $arr_where,
			'arr_field' => array('_id', 'first_name', 'last_name'),
			'arr_order' => array('first_name' => 1),
		));

	}

	function dashboard( $type_auth = '' ){
		if( $type_auth == '' ){
			if( $this->Session->check('homes_dashboard_type_auth') ){
				$type_auth = $this->Session->read('homes_dashboard_type_auth');
			}else{
				$type_auth = 'administration';
			}
        }else{
        	$this->Session->write('homes_dashboard_type_auth', $type_auth);
        }

		$this->selectModel('Contact');
		$arr_employee = $this->get_employee();
		$this->set( 'arr_employee', $arr_employee );

		$this->{'_dashboard_type_auth_'.$type_auth}();
		$this->set( 'type_auth', $type_auth );
		$this->render('dashboard_auth_'.$type_auth);
	}

	function _dashboard_type_auth_administration(){
		$this->dashboard_enquiries();
		$this->dashboard_jobs();
		$this->dashboard_tasks();
		$this->dashboard_salesorders();
		$this->dashboard_quotations();
		$this->dashboard_purchaseorders();
		$this->dashboard_shippings();
		$this->dashboard_salesinvoices();
	}

	function _dashboard_type_auth_production(){
		$this->selectModel('Equipment');
		$arr_equipment = $this->Equipment->select_list(array('arr_field' => array('_id', 'name'), 'arr_order' => array('name' => 1)));
		$this->set( 'arr_equipment', $arr_equipment );
		foreach ($arr_equipment as $key => $value) {
			$this->dashboard_assets($key);
		}

		$this->dashboard_tasks();
		$this->dashboard_salesorders();
	}

	function _dashboard_type_auth_chart(){
	}

	function dashboard_enquiries( $our_rep_id = '' ){
		$this->selectModel('Enquiry');

		$arr_order = array('date' => 1);
		if( isset($_POST['field']) && strlen($_POST['field']) > 0 && isset($_POST['type']) && strlen($_POST['type']) > 0 ){
			$arr_order = array( $_POST['field'] => (($_POST['type'] == 'asc')?1:-1 ));
		}

		$cond = array();
		if( $our_rep_id != '' ){
			$cond['our_rep_id'] = new MongoId($our_rep_id);
		}elseif(  $our_rep_id == '' ){
			if(!$this->request->is('ajax'))
				$cond['our_rep_id'] = $_SESSION['arr_user']['contact_id'];
			else {
				$cond = $this->get_employee_condition('our_rep_id');
			}

		}

		$cond['status']=array( '$in' => array('Hot', 'Cold', 'Deal', 'Lost') );
		$arr_enquiry = $this->Enquiry->select_all(array(
			'arr_where' => $cond,
			'arr_field' => array('_id', 'date', 'company', 'enquiry_value'),
			'arr_order' => $arr_order
		));
		$this->set( 'arr_enquiry', $arr_enquiry );
	}

	function dashboard_jobs( $contact_id = '' ){
		$this->selectModel('Job');

		$arr_order = array('work_end' => 1);
		if( isset($_POST['field']) && strlen($_POST['field']) > 0 && isset($_POST['type']) && strlen($_POST['type']) > 0 ){
			$arr_order = array( $_POST['field'] => (($_POST['type'] == 'asc')?1:-1 ));
		}

		$cond = array();
		if( $contact_id != '' ){
			$cond['contacts.contact_id'] = new MongoId($contact_id);
		}elseif( $contact_id == '' ){
			if( !$this->request->is('ajax'))
				$cond['contacts.contact_id'] = $_SESSION['arr_user']['contact_id'];
			else {
				$cond = $this->get_employee_condition('contacts.contact_id');
			}
		}
		$cond['$or']=array(
			array("status" => 'New')
			,array("status" => 'Confirmed')
		);
		$arr_job = $this->Job->select_all(array(
			'arr_where' => $cond,
			'arr_field' => array('_id', 'work_start', 'work_end', 'no', 'company_name'),
			'arr_order' => $arr_order
		));
		$this->set( 'arr_job', $arr_job );
	}

	function _get_color_status(){
		$this->selectModel('Setting');
		$arr_status = $this->Setting->select_one(array('setting_value' => 'tasks_status'));
		foreach ($arr_status['option'] as $status) {
			if( isset($status['color']) ){
				$arr_tmp[(string)$status['value']] = $status['color'];
			}
		}

		$this->selectModel('Equipment');
		$arr_equipment = $this->Equipment->select_all();
		foreach ($arr_equipment as $equipment) {
			if( isset($equipment['color']) ){
				foreach ($equipment['color'] as $key => $value) {
					$arr_tmp[(string)$equipment['_id'].'_'.$key] = $value;
				}
			}

		}
		return $arr_tmp;
	}

	function dashboard_tasks( $our_rep_id = '' ){
		$this->set( 'color_status', $this->_get_color_status() );

		$this->selectModel('Task');
		$arr_order = array('work_end' => 1);
		if( isset($_POST['field']) && strlen($_POST['field']) > 0 && isset($_POST['type']) && strlen($_POST['type']) > 0 ){
			$arr_order = array( $_POST['field'] => (($_POST['type'] == 'asc')?1:-1 ));
		}
		$cond = array();
		if( $our_rep_id != '' ){
			$cond['$or']=array(
				array('contacts' => array('$elemMatch' => array('contact_id' => new MongoId($our_rep_id)) )),
				array('our_rep_id' => new MongoId($our_rep_id))
			);
		}elseif($our_rep_id == '' ){
			if( !$this->request->is('ajax') )
				$cond['$or']=array(
					array('contacts' => array('$elemMatch' => array('contact_id' => $_SESSION['arr_user']['contact_id']) )),
					array('our_rep_id' => $_SESSION['arr_user']['contact_id'])
				);
			else {
				$our_rep_condition = $this->get_employee_condition('our_rep_id');
				$cond['$or']=array(
					array('contacts' => array('$elemMatch' => array('contact_id' => $our_rep_condition['our_rep_id']) )),
					$our_rep_condition
				);
			}
		}
		$cond['status'] = array( '$in' => array('New', 'Confirmed') );
		$arr_task = $this->Task->select_all(array(
			'arr_where' => $cond,
			'arr_field' => array('_id', 'work_start', 'work_end', 'name', 'our_rep_type', 'our_rep_id', 'status_id'),
			'arr_order' => $arr_order
		));
		$this->set( 'arr_task', $arr_task );
	}

	function dashboard_assets( $our_rep_id = '' ){
		$this->set( 'color_status', $this->_get_color_status() );

		$this->selectModel('Task');
		$arr_order = array('work_end' => 1);
		if( isset($_POST['field']) && strlen($_POST['field']) > 0 && isset($_POST['type']) && strlen($_POST['type']) > 0 ){
			$arr_order = array( $_POST['field'] => (($_POST['type'] == 'asc')?1:-1 ));
		}

		$cond = array();
		if( $our_rep_id != '' ){
			$cond['our_rep_id'] = new MongoId($our_rep_id);
		}
		$cond['status'] = array( '$in' => array('New', 'Confirmed') ) ;
		$cond['our_rep_type'] = 'assets';

		$arr_task = $this->Task->select_all(array(
			'arr_where' => $cond,
			'arr_field' => array('_id', 'work_start', 'work_end', 'our_rep', 'name', 'our_rep_type', 'our_rep_id', 'status_id','company_name','salesorder_no'),
			'arr_order' => $arr_order
		));
		// pr($cond);
		// foreach ($arr_task as $key => $value) {
		// 	pr($value);
		// }echo 123;die;
		$this->set( 'asset_key', $our_rep_id );
		$this->set( 'arr_asset_'.$our_rep_id, $arr_task );
	}

	function dashboard_salesorders( $our_rep_id = '' ){
		$this->selectModel('Salesorder');
		$arr_order = array('payment_due_date' => 1);
		if( isset($_POST['field']) && strlen($_POST['field']) > 0 && isset($_POST['type']) && strlen($_POST['type']) > 0 ){
			$arr_order = array( $_POST['field'] => (($_POST['type'] == 'asc')?1:-1 ));
		}
		$cond = array();
		if( $our_rep_id != '' ){
			// $cond['our_rep_id'] = new MongoId($our_rep_id);
			$cond['$or']=array(
				array("our_rep_id" => new MongoId($our_rep_id)),
				array("our_csr_id" => new MongoId($our_rep_id))
			);
		}elseif(  $our_rep_id == '' ){
			if(!$this->request->is('ajax'))
				$cond['$or']=array(
					array("our_rep_id" => $_SESSION['arr_user']['contact_id']),
					array("our_csr_id" => $_SESSION['arr_user']['contact_id'])
				);
			else {
				$our_rep_condition = $this->get_employee_condition('our_rep_id');
				$cond['$or']=array(
					$our_rep_condition,
					array("our_csr_id" => $our_rep_condition['our_rep_id'])
				);
			}
		}

		$cond['status'] = array( '$in' => array('New','Submitted','In production','Partly shipped') );

		$arr_salesorder = $this->Salesorder->select_all(array(
			'arr_where' => $cond,
			'arr_field' => array('_id','due_date','payment_due_date', 'date_in', 'company_name','sum_sub_total','code'),
			'arr_order' => $arr_order
		));
		$this->set( 'arr_salesorder', $arr_salesorder );
	}

	function dashboard_quotations( $our_rep_id = '' ){
		$this->selectModel('Quotation');
		$arr_order = array('payment_due_date' => 1);
		if( isset($_POST['field']) && strlen($_POST['field']) > 0 && isset($_POST['type']) && strlen($_POST['type']) > 0 ){
			$arr_order = array( $_POST['field'] => (($_POST['type'] == 'asc')?1:-1 ));
		}
		$cond = array();
		if( $our_rep_id != '' ){
			$cond['our_rep_id'] = new MongoId($our_rep_id);
		}elseif($our_rep_id == '' ){
			if( !$this->request->is('ajax') )
				$cond['our_rep_id'] = $_SESSION['arr_user']['contact_id'];
			else {
				$cond = $this->get_employee_condition('our_rep_id');
			}
		}
		$cond['$or']=array(
			array("quotation_status" => 'In progress')
		   ,array("quotation_status" => 'Submitted')
		   // ,array("quotation_status" => 'Amended')

		);
		$arr_quotation = $this->Quotation->select_all(array(
			'arr_where' => $cond,
			'arr_field' => array('_id', 'quotation_date', 'payment_due_date','sum_sub_total', 'company_name','code','salesorder_number'),
			'arr_order' => $arr_order
		));
		$this->set( 'arr_quotation', $arr_quotation );
	}

	function dashboard_purchaseorders( $our_rep_id = '' ){
		$this->selectModel('Purchaseorder');
		$arr_order = array('required_date' => 1);
		if( isset($_POST['field']) && strlen($_POST['field']) > 0 && isset($_POST['type']) && strlen($_POST['type']) > 0 ){
			$arr_order = array( $_POST['field'] => (($_POST['type'] == 'asc')?1:-1 ));
		}
		$cond = array();
		if( $our_rep_id != '' ){
			$cond['our_rep_id'] = new MongoId($our_rep_id);
		}elseif($our_rep_id == '' ){
			if( !$this->request->is('ajax') )
				$cond['our_rep_id'] = $_SESSION['arr_user']['contact_id'];
			else {
				$cond = $this->get_employee_condition('our_rep_id');
			}
		}

		$cond['$or']=array(
			array('purchase_orders_status' => 'On order'),
			array('purchase_orders_status' => 'In progress'),
			array('purchase_orders_status' => 'Partly Received')
		);

		$arr_purchaseorder = $this->Purchaseorder->select_all(array(
			'arr_where' => $cond,
			'arr_field' => array('_id', 'purchord_date','required_date','sum_sub_total', 'company_name','code'),
			'arr_order' => $arr_order
		));
		$this->set( 'arr_purchaseorder', $arr_purchaseorder );
	}

	function dashboard_shippings( $our_rep_id = '' ){
		$this->selectModel('Shipping');
		$arr_order = array('shipping_date' => 1);
		if( isset($_POST['field']) && strlen($_POST['field']) > 0 && isset($_POST['type']) && strlen($_POST['type']) > 0 ){
			$arr_order = array( $_POST['field'] => (($_POST['type'] == 'asc')?1:-1 ));
		}
		$cond = array();
		if( $our_rep_id != '' ){
			$cond['our_rep_id'] = new MongoId($our_rep_id);
		}elseif( $our_rep_id == '' ){
			if( !$this->request->is('ajax') )
				$cond['our_rep_id'] = $_SESSION['arr_user']['contact_id'];
			else {
				$cond = $this->get_employee_condition('our_rep_id');
			}
		}
		$cond['$or']=array(
			array("shipping_status" => 'In progress')
		   ,array("shipping_status" => 'Complete')
		);
		$arr_shipping = $this->Shipping->select_all(array(
			'arr_where' => $cond,
			'arr_field' => array('_id','shipping_date', 'company_name','code','shipping_status'),
			'arr_order' => $arr_order
		));
		$this->set( 'arr_shipping', $arr_shipping );
	}

	function dashboard_salesinvoices( $our_rep_id = '' ){
		$this->selectModel('Salesinvoice');
		$arr_order = array('payment_due_date' => 1);
		if( isset($_POST['field']) && strlen($_POST['field']) > 0 && isset($_POST['type']) && strlen($_POST['type']) > 0 ){
			$arr_order = array( $_POST['field'] => (($_POST['type'] == 'asc')?1:-1 ));
		}
		$cond = array();
		if( $our_rep_id != '' ){
			$cond['our_rep_id'] = new MongoId($our_rep_id);
		}elseif(  $our_rep_id == '' ){
			if(!$this->request->is('ajax') )
				$cond['our_rep_id'] = $_SESSION['arr_user']['contact_id'];
			else {
				$cond = $this->get_employee_condition('our_rep_id');
			}
		}
		$cond['$or']=array(
			array("invoice_status" => 'Invoiced')

		);
		$arr_salesinvoice = $this->Salesinvoice->select_all(array(
			'arr_where' => $cond,
			'arr_field' => array('_id', 'payment_due_date','invoice_date','sum_sub_total', 'company_name','code'),
			'arr_order' => $arr_order
		));
		$this->set( 'arr_salesinvoice', $arr_salesinvoice );
	}

	function alerts_open(){
		$arr_data = $this->alerts_get_data(); // dùng hàm get data chung lấy hết
		$this->set('arr_data', $arr_data);
	}

	function alerts_check(){

		$arr_check['has_alert'] = 0;
		$arr_data = $this->alerts_get_data(); // dùng hàm get data chung lấy hết

		$arr_check['communication'] = $arr_data['communication']->count();
		if( $arr_check['communication'] > 0 )
			$arr_check['has_alert'] = 1;
		if(!isset($arr_data['leave'])) $arr_data['leave'] = array();
		$arr_check['leave'] = count($arr_data['leave']);
		//$arr_check['leave'] = $arr_data['leave']->count();
		if( $arr_check['leave'] > 0 )
			$arr_check['has_alert'] = 1;

		$arr_check['task'] = $arr_data['task']->count();
		if( $arr_check['task'] > 0 )
			$arr_check['has_alert'] = 1;

		echo json_encode($arr_check);
		die;
	}

	function alerts_get_data(){
		$cond = array();
		$cond['_id'] = $_SESSION['arr_user']['contact_id'];
		$cond['leave']['$elemMatch']['deleted'] = false;
		$cond['leave']['$elemMatch']['approved'] = '1';
		$cond['leave']['$elemMatch']['viewed'] = false;

		$this->selectModel('Contact');
		$arr_leave = $this->Contact->select_one($cond,array('leave'));
		$arr_leave = isset($arr_leave['leave']) ? $arr_leave['leave'] : array();
		foreach($arr_leave as $key => $value){
			if( $value['deleted']) continue;
			if( $value['viewed']) continue;
			if( !$value['approved']) continue;
			$arr_data['leave'][$key] = $value;
		}
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