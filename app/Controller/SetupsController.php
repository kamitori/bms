<?php
App::uses('AppController', 'Controller');
class SetupsController extends AppController {

	var $name = 'Setups';
    var $modelName='Setup';
	public $helpers = array();
	public $opm; //Option Module
	public function beforeFilter(){
		parent::beforeFilter();
		$this->set_module_before_filter('Setup');
		$this->sub_tab_default = 'general';
	}


	public function rebuild_setting($arr_setting=array()){
		// parent::rebuild_setting($arr_setting);
         $arr_setting = $this->opm->arr_settings;
        if(!$this->check_permission($this->name.'_@_entry_@_edit')){
            $arr_setting = $this->opm->set_lock(array(),'out');
            $this->set('address_lock', '1');
        }
        $this->opm->arr_settings = $arr_setting;
        $arr_tmp = $this->opm->arr_field_key('cls');
        $arr_link = array();
        if(!empty($arr_tmp))
            foreach($arr_tmp as $key=>$value)
                $arr_link[$value][] = $key;
        $this->set('arr_link',$arr_link);
	}

	//Associated data function
	public function arr_associated_data($field = '', $value = '', $valueid = '') {
		$arr_return = array();
		$arr_return[$field] = $value;
		// ..........more code
		return $arr_return;
	}



	//Entry - trang chi tiet
 	public function entry() {
        $arr_set = $this->opm->arr_settings;
        $this->set('arr_settings', $arr_set);

		$active = 'general';
		if($this->Session->check('setup_remember_page'))
			$active = $this->Session->read('setup_remember_page');
		else
			$this->Session->write('setup_remember_page', $active);
		$this->sub_tab($active,0);
		$this->set('active',$active);
    }


	function save_product(){
		if(isset($_POST)){
			$this->selectModel('Stuffs');
			$arr_save = $_POST;
			$arr_save['value'] = 'Minimum Order Adjustment';
			$arr_save['product_id'] = new MongoId($arr_save['product_id']);
			if(strlen($arr_save['_id'])==24 )
				$arr_save['_id'] = new MongoId($arr_save['_id']);
			else
				unset($arr_save['_id']);
			if($this->Stuffs->save($arr_save))
				echo 'ok';
			else
				echo $this->Stuffs->arr_errors_save[1];
		}
		die;
	}
	function save_changing_code(){
		if(isset($_POST)){
			$this->selectModel('Stuffs');
			$arr_save = $_POST;
			$arr_save['value'] = 'Changing Code';
			$arr_save['password'] = md5($arr_save['password']);
			if(strlen($arr_save['_id'])==24 )
				$arr_save['_id'] = new MongoId($arr_save['_id']);
			else
				unset($arr_save['_id']);
			if($this->Stuffs->save($arr_save))
				echo 'ok';
			else
				echo $this->Stuffs->arr_errors_save[1];
		}
		die;
	}

	//General
	public function general(){
		$this->selectModel('Stuffs');
		$accountant = $this->Stuffs->select_one(array('value'=>"Accountant"));
		$product = $this->Stuffs->select_one(array('value'=>"Minimum Order Adjustment"));
		$current_default= $this->Stuffs->select_one(array('key'=>"currency_list"));
		$changing_code = $this->Stuffs->select_one(array('value'=>"Changing Code"));
		$logo = $this->Stuffs->select_one(array('value'=>"Logo"));
		$format_currency = $this->Stuffs->select_one(array('value'=>"Format Currency"));
		$format_date = $this->Stuffs->select_one(array('value'=>"Format Date"));
		$this->set('accountant', $accountant);
		$this->set('product', $product);
		$this->set('changing_code', $changing_code);
		$this->set('logo', $logo);
		$this->set('format_currency', $format_currency);
		$this->set('format_date', $format_date);
		$format_date_list = array(
		                          'd M, Y' => date('d M, Y'),
		                          'd/m/Y' => date('d/m/y'),
		                          'd-m-Y' => date('d-m-y'),
		                          );
		$this->selectModel('Setting');
        $current_list = $this->Setting->select_option_vl(array('setting_value'=> 'currency_type'));
		$this->set('current_list',$current_list);
		$this->set('current_default',$current_default);
		$this->set('format_date_list',json_encode($format_date_list));
		$this->Session->write('setup_remember_page', 'general');
	}


	//Lists & Menus
	public function list_and_menu() {
		$this->selectModel('Setting');
		$arr_settings = $this->Setting->select_all(
				array(
					'arr_where'=>array(
						'setting_value'=>array('$nin'=>array('email_setting')),
						'name'=>array('$ne'=>'Pricing Rules')
					),
					'arr_order' => array('setting_name' => 1)
				));

		$this->set('setting_data', $arr_settings);
		$this->Session->write('setup_remember_page', 'list_and_menu');
	}




}