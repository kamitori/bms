<?php
App::uses('AppController', 'Controller');
class LogsController extends AppController {
	var $name = 'Logs';
	public $helpers = array();
	public $opm; //Option Module

	public function beforeFilter(){
		parent::beforeFilter();
		$this->set_module_before_filter('Log');
	}

	//Các điều kiện mở/khóa field trong entry
	public function check_lock(){
		if($this->get_id()!=''){
			$arr_tmp = $this->opm->select_one(array('_id' => new MongoId($this->get_id())));
			if(0==0)
				return true;
		}else
			return false;
	}

	public function entry(){
		$mod_lock = '0';
		if($this->check_lock()){
			$this->opm->set_lock(array('invoice_status'),'out');
			$mod_lock = '1';
			$this->set('address_lock','1');
		}
		$arr_set = $this->opm->arr_settings;
		// Get value id
		$iditem = $this->get_id();
		if($iditem=='')
			$iditem = $this->get_last_id();

		$this->set('iditem',$iditem);
		//Load record by id
		if($iditem!=''){
			$arr_tmp = $this->opm->select_one(array('_id' => new MongoId($iditem)));
			foreach($arr_set['field'] as $ks => $vls){
				foreach($vls as $field => $values){
					if(isset($arr_tmp[$field])){
						$arr_set['field'][$ks][$field]['default'] = $arr_tmp[$field];
						if($field=='payment_due_date')
							$arr_set['field'][$ks][$field]['default'] = $this->opm->format_date($arr_tmp[$field]->sec);
						else if(preg_match("/_date$/",$field) && is_object($arr_tmp[$field]))
							$arr_set['field'][$ks][$field]['default'] = date('m/d/Y',$arr_tmp[$field]->sec);
						//chế độ lock, hiện name của các relationship custom
						else if(($field=='company_name' || $field=='contact_name') && $mod_lock=='1')
							$arr_set['field'][$ks][$field]['default'] = $arr_tmp[$field];

						else if($this->opm->check_field_link($ks,$field)){
							$field_id = $arr_set['field'][$ks][$field]['id'];
							if(!isset($arr_set['field'][$ks][$field]['syncname']))
								$arr_set['field'][$ks][$field]['syncname'] = 'name';
							$arr_set['field'][$ks][$field]['default'] = $this->get_name($this->ModuleName($arr_set['field'][$ks][$field]['cls']),$arr_tmp[$field_id],$arr_set['field'][$ks][$field]['syncname']);

						}else if($field=='company_name' && isset($arr_tmp['company_id']) && $arr_tmp['company_id']!=''){
							$arr_set['field'][$ks][$field]['default'] = $this->get_name('Company',$arr_tmp['company_id']);

						}else if($field=='contact_name' && isset($arr_tmp['contact_id']) && $arr_tmp['contact_id']!=''){
							$arr_set['field'][$ks][$field]['default'] = $this->get_name('Contact',$arr_tmp['contact_id']);
						}

						if(in_array($field,$arr_set['title_field']))
							$item_title[$field] = $arr_tmp[$field];
					}
				}
			}
			$arr_set['field']['panel_1']['mongo_id']['default'] = $iditem;
			$this->Session->write($this->name.'ViewId',$iditem);
			//show footer info
			$this->show_footer_info($arr_tmp);


		//add, setup field tự tăng
		}else{
			$nextcode = $this->opm->get_auto_code('code');
			$arr_set['field']['panel_1']['code']['default'] = $nextcode;
			$this->set('item_title',array('code'=>$nextcode));
		}
		$this->set('arr_settings',$arr_set);
		parent::entry();
	}


}