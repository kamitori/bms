<?php
class TaxsController extends AppController {

	var $name = 'Taxs';
	var $modelName = 'Tax';
	public $helpers = array();
	public $opm; //Option Module
	public function beforeFilter(){
		parent::beforeFilter();
		$this->set_module_before_filter('Tax');
	}


	//Các điều kiện mở/khóa field trong entry
	public function check_lock(){
		return false;
	}


	public function entry(){
		$mod_lock = '0';
		// if($this->check_lock()){
		// 	$this->opm->set_lock(array('fed_tax',),'out');
		// 	$mod_lock = '1';
		// 	$this->set('address_lock','1');
		// }

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
						if(preg_match("/_date$/",$field) && is_object($arr_tmp[$field]))
							$arr_set['field'][$ks][$field]['default'] = date('m/d/Y',$arr_tmp[$field]->sec);
						if(in_array($field,$arr_set['title_field']))
							$item_title[$field] = $arr_tmp[$field];
					}
				}
			}
			//set id item
			$arr_set['field']['panel_1']['mongo_id']['default'] = $iditem;
			//set session items
			$this->Session->write($this->name.'ViewId',$iditem);
			//set header title
			$this->set('item_title',$item_title);
			//show footer info
			$this->show_footer_info($arr_tmp);
		}



		$this->set('arr_settings',$arr_set);
		parent::entry();
		$arr_prov['province'] = $this->province('CA');
		$this->set('arr_options_custom',$arr_prov);

	}

}