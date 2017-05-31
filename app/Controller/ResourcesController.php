<?php
App::uses('AppController', 'Controller');
class ResourcesController extends AppController {

	var $modelName = 'Resource';

	public function beforeFilter( ){
		// goi den before filter cha
		parent::beforeFilter();
	}

	// ================================== CALENDAR ====================================
	public function calendar($type = 'Contact'){

		$this->set('set_footer', '../Resources/calendar_footer');
		$this->Session->write('calendar_last_visit', '/' . $this->request->url);

		$arr_option = $this->get_option_status_color('equipments_status');
        $arr_status = $arr_option[0];
        $arr_status_color = $arr_option[1];

		$this->set( 'arr_status', $arr_status );
		$this->set( 'arr_status_color', $arr_status_color );

		// Nếu resource đang xem là Contact
		if( $type == 'Contact' ){

			$this->selectModel('Contact');
			$arr_employees = $this->Contact->select_list(array(
				'arr_where' => array('is_employee' => 1),
				'arr_field' => array('_id', 'first_name', 'last_name'),
				'arr_order' => array('first_name' => 1),
			));
			// $this->set( 'arr_employees', $arr_employees );
			$this->set( 'arr_type_resource', $arr_employees );
		}

		// Nếu resource đang xem là Equipment
		if( $type == 'Equipment' ){

			$this->selectModel('Equipment');
			$arr_equipment = $this->Equipment->select_all( array(
				'arr_field' => array('_id', 'name'),
				'arr_order' => array('name' => 1)
			));


			$arr_tmp = array();
			foreach ($arr_equipment as $value) {
				$arr_tmp[(string)$value['_id']] = $value['name'];
			}
			// $this->set('arr_asset', $arr_tmp);
			$this->set( 'arr_type_resource', $arr_tmp );
		}

		$this->set('type', $type);
		$this->layout = 'calendar';
	}

	public function calendar_json($type = 'Contact', $date_from = '', $date_to = '', $get_more = 'no'){

		$arr_option = $this->get_option_status_color('equipments_status');
        $arr_status = $arr_option[0];
        $arr_status_color = $arr_option[1];
        $arr_status_move = $arr_option[2];

		// kiem tra xem co phai la load them data vao Calendar View khong
		$arr_where = array('type' => $type);
		$arr_where['$or'] = array(
			array(
				'work_start' => array( '$lte' => new MongoDate(strtotime($date_from)) ),
				'work_end' => array( '$gte' => new MongoDate(strtotime($date_from)))
			),
			array(
				'work_start' => array( '$lte' => new MongoDate(strtotime($date_to)) ),
				'work_end' => array( '$gte' => new MongoDate(strtotime($date_to)))
			),
			array(
				'work_start' => array( '$gte' => new MongoDate(strtotime($date_from)) ),
				'work_end' => array( '$lte' => new MongoDate(strtotime($date_to)))
			)
		);

		$this->selectModel('Resource');
		$arr_resources = $this->Resource->select_all(array(
			'arr_where' => $arr_where,
			'limit' => 1000,
			'maxLimit' => 1000
		));

		// parse like a data jsonp to client
		$str = '<data>';
		foreach ($arr_resources as $value) {

			// THÔNG TIN CỦA RESOURCES
			$str .= '<event';
			$str .= ' id="'.$value['_id'].'" item_id="' . $value['item_id'] . '"';
			$str .= ' status="'.$value['status_id'].'"';

			if( $value['work_end']->sec > strtotime('now') )
				$str .= ' color="red"';
			else
				$str .= ' color="'.$arr_status_color[$value['status_id']] . '"';

			if( $arr_status_move[$value['status_id']] ){
				$str .= ' can_move="1"';
			}else{
				$str .= ' can_move="0"';
			}

			// insert them du lieu vao data view cua calendar
			if( $get_more == 'yes' ){
				$str .= ' type="inserted"';
			}

			// $str .= ' type="'.$value['type']. '"';
			$str .= ' start_date="'.date('Y-m-d H:i', $value['work_start']->sec). '"';
			$str .= ' end_date="'.date('Y-m-d H:i', $value['work_end']->sec). '"';
			$str .= ' text="'.$value['name'].'"';

			// =================== THÔNG TIN CỦA MODULE LIEN QUAN ==========================
			$module = $value['module'];
			$this->selectModel($module);
			$arr_module = $this->$module->select_one(array('_id' => $value['module_id']), array('_id', 'name', 'type', 'contact_name', 'salesorder_id', 'our_rep', 'our_rep_id'));
			$str .= ' tltp_module="'.$module.'"';
			$str .= ' tltp_id="'.$arr_module['_id'].'"';
			$str .= ' tltp_name="'.$arr_module['name'].'"';
			$str .= ' tltp_type="'.( isset($arr_module['type'])?$arr_module['type']:'' ).'"';
			$str .= ' tltp_contact_name="'.$arr_module['contact_name'].'"';
			$str .= ' tltp_responsible="'.$arr_module['our_rep'].'"';

			$tltp_res_type = 'Contact';
			if( $type != 'Contact' ){
				$tltp_res_type = 'Asset';
			}
			$str .= ' tltp_res_type="'.$tltp_res_type.'"';
			$str .= ' tltp_res_name="'.$value['name'].'"';

			// --- thêm vào thông tin sales order nếu đây là TASK ---
			if( $module == 'Task' ){
				if(isset($arr_module['salesorder_id']) && is_object($arr_module['salesorder_id'])){
					$this->selectModel('Salesorder');
					$arr_salesorder = $this->Salesorder->select_one(array('_id' => $arr_module['salesorder_id']), array('_id', 'name', 'work_start', 'work_end', 'our_rep', 'our_rep_id'));
					if( isset($arr_salesorder['name']) ){
						$str .= ' salesorder_id="'.$arr_salesorder['_id']. '"';
						$str .= ' salesorder_heading="'.(isset($arr_salesorder['name'])?$arr_salesorder['name']:'').'"';
						$str .= ' salesorder_assign_to="'.(isset($arr_salesorder['our_rep'])?$arr_salesorder['our_rep']:'').'"';
					}
				}
			}
			// ======================= END =================================================

			// ĐÓNG THẺ EVENT
			$str .= '></event>';
		}
		$str .= '</data>';
		echo $str;
		// echo json_encode($arr_tmp);
		die;
	}

	public function calendar_change(){
		$arr_post_data = $this->data;
		$arr_save['work_start'] = new MongoDate(strtotime($arr_post_data['work_start']));
		$arr_save['work_end'] = new MongoDate(strtotime($arr_post_data['work_end']));
		$arr_save['_id'] = $arr_post_data['id'];
		$this->selectModel('Resource');
		if( $this->Resource->save($arr_save) ){
			echo 'ok';
		}else{
			echo 'Error: ' . $this->Resource->arr_errors_save[1];
		}
		die;
	}

	public function module_redirect($id){
		$this->selectModel('Resource');
		$arr_tmp = $this->Resource->select_one( array('_id' => new MongoId($id)), array('module', 'module_id') );
		$this->redirect('/' . strtolower($arr_tmp['module']) . 's/entry/' . (string)$arr_tmp['module_id'] );
	}
}