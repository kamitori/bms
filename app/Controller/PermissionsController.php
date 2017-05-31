<?php
App::uses('AppController', 'Controller');
class PermissionsController extends AppController {

	var $name = 'Permissions';
	var $modelName = 'Permission';
	public $helpers = array();
	public $opm; //Option Module
	public function beforeFilter(){
		parent::beforeFilter();
		$this->set_module_before_filter('Permission');
	}

	public function entry(){
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
		$this->sub_tab_default = 'option_list';
		$this->sub_tab('',$iditem);
	}

	public function ajax_save(){
		if(isset($_POST['field']) && isset($_POST['value']) && isset($_POST['func']) && !in_array((string)$_POST['field'],$this->opm->arr_autocomplete()) ){

			$values = $_POST['value'];
			if($_POST['func']=='add'){
				$ids = $this->opm->add($_POST['field'],$values);
				$newid = explode("||",$ids);
				$this->Session->write($this->name.'ViewId',$newid[0]);
			}else if($_POST['func']=='update' && isset($_POST['ids'])){
				$ids = $this->opm->update($_POST['ids'],$_POST['field'],$values);
				$this->Session->write($this->name.'ViewId',$_POST['ids']);
			}
			echo $ids;
		}else
			echo 'error';
		die;
	}

	public function option_list(){
		$subdatas = array();
		$subdatas['optiondata'] = array();
		$this->set('subdatas', $subdatas);
	}


	public function premission_list(){
		$subdatas = array();
		$subdatas['premissiondata'] = array();
		$this->set('subdatas', $subdatas);
	}

}