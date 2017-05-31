<?php
App::uses('AppController', 'Controller');
class BasicsController extends AppController {

	var $name = 'Basics';
	public $helpers = array();
	public $opm; //Option Module
	public function beforeFilter(){
		parent::beforeFilter();
		$this->set_module_before_filter('Basic');
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

	// Add action
    public function add() {
		$this->selectModel('Company');
        $query = $this->Company->select_one(array('system' => true));
		$arr_more = $query;
		if(isset($arr_more) && is_array($arr_more) && count($arr_more)>0){
			//lưu các giá trị của company system vào loation
			if(isset($arr_more['name']))
				$arr_tmp['company_name'] = $arr_more['name'];
			if(isset($arr_more['_id']))
				$arr_tmp['company_id'] = $arr_more['_id'];
			if(isset($arr_more['phone']))
				$arr_tmp['phone'] = $arr_more['phone'];
			if(isset($arr_more['fax']))
				$arr_tmp['fax'] = $arr_more['fax'];
			if(isset($arr_more['email']))
				$arr_tmp['email'] = $arr_more['email'];
			if(isset($arr_more['addresses'][0])){
				foreach($arr_more['addresses'][0] as $kk=>$vv){
					$arr_tmp['shipping_address'][0]['shipping_'.$kk] = $vv;
				}
			}
			//contact
			if(isset($arr_more['contact_default_id'])){
				$this->selectModel('Contact');
        		$query = $this->Contact->select_one(array('_id' => new MongoId($arr_more['contact_default_id'])));
				$contact = $query;
				if(isset($contact['first_name']))
					$arr_tmp['contact_name'] = $contact['first_name'].' ';
				if(isset($contact['last_name']))
					$arr_tmp['contact_name'] .= $contact['last_name'];
				if(isset($contact['_id']))
					$arr_tmp['contact_id'] = $contact['_id'];
			}
		}

        $ids = $this->opm->add('name', '',$arr_tmp);
        $newid = explode("||", $ids);
        $this->Session->write($this->name . 'ViewId', $newid[0]);
        $this->redirect('/' . $this->params->params['controller'] . '/entry');
        die;
    }



	//Entry - trang chi tiet
 	public function entry() {
        $arr_set = $this->opm->arr_settings;
        $arr_tmp = array();
        // Get value id
        $iditem = $this->get_id();
        if ($iditem == '')
            $iditem = $this->get_last_id();

        $this->set('iditem', $iditem);
        //Load record by id
        if ($iditem != '') {
            $arr_tmp = $this->opm->select_one(array('_id' => new MongoId($iditem)));
            foreach ($arr_set['field'] as $ks => $vls) {
                foreach ($vls as $field => $values) {
                    if (isset($arr_tmp[$field])) {
                        $arr_set['field'][$ks][$field]['default'] = $arr_tmp[$field];
                        if (preg_match("/_date$/", $field) && is_object($arr_tmp[$field]))
                            $arr_set['field'][$ks][$field]['default'] = date('m/d/Y', $arr_tmp[$field]->sec);
                        if (in_array($field, $arr_set['title_field']))
                            $item_title[$field] = $arr_tmp[$field];
                        if ($field == 'contact_name' && isset($arr_tmp['contact_last_name'])) {
                            $arr_set['field'][$ks][$field]['default'] = $arr_tmp[$field] . ' ' . $arr_tmp['contact_last_name'];
                            $item_title['contact_name'] = $arr_tmp[$field] . ' ' . $arr_tmp['contact_last_name'];
                        }
                    }
                }
            }

            $arr_set['field']['panel_1']['mongo_id']['default'] = $iditem;
            $this->Session->write($this->name . 'ViewId', $iditem);

            //BEGIN custom
            if (isset($arr_set['field']['panel_1']['code']['default']))
                $item_title['code'] = $arr_set['field']['panel_1']['code']['default'];
            else
                $item_title['code'] = '1';
            $this->set('item_title', $item_title);

            //END custom
            $this->set('address_lock', '1');
            //END custom
            //show footer info
            $this->show_footer_info($arr_tmp);


            //add, setup field tự tăng
        }else {
            $nextcode = $this->opm->get_auto_code('code');
            $arr_set['field']['panel_1']['code']['default'] = $nextcode;
            $this->set('item_title', array('code' => $nextcode));
        }

        $this->set('arr_settings', $arr_set);
        $this->sub_tab_default = 'general';
        $this->sub_tab('', $iditem);
        parent::entry();
    }

    
	
	//Associated data function
	public function arr_associated_data($field = '', $value = '', $valueid = '') {
        $arr_return = array();
		$arr_return[$field] = $value;
		// ..........more code
        return $arr_return;
    }


	//Search function
	public function entry_search() {
        
    }

	//Swith options function
    public function swith_options($option = ''){
        parent::swith_options($option);
    }
	
	//Subtab function
	public function general() {
        $subdatas = array();
        $subdatas['subtab01'] = array();
        $this->set('subdatas', $subdatas);
    }
	
	//Subtab function
	public function subtab02() {
        $subdatas = array();
        $subdatas['subtab02'] = array();
        $this->set('subdatas', $subdatas);
    }

	// Popup form orther module
    public function popup($key = '') {
		parent::popup($key);
    }
	
	
	public function test($module=''){
		if($module=='')
			$module='Quotation';
		require_once APP.'Model'.DS.$module.'Field.php';
		$arr_tmp = 	${$module.'Field'};
		$newfile = APP.'Model'.DS.'Custom'.DS.$module.'Field.php';
		$fopen = fopen($newfile,"w+");
		
		$data = "<?php \n";
		$data .= "\t\$ModuleField = array();\n";
		$data .= $this->convert_array_to_string_code($arr_tmp,'',1);
		$data .= "\t\$QuotationField = \$ModuleField;\n?>";
		
		fwrite($fopen,$data);
		fclose($fopen);
		pr($data);die;
    }
	
	public function convert_array_to_string_code($array='',$string='',$lv=0){
		$tab_num = "";
		for($m=0; $m<$lv ;$m++){
			$tab_num .="\t";
		}
		
		if(count($array)>0)
		foreach($array as $key=>$value){
			if(is_array($value)){
				if($lv==1)
					$string .= $tab_num."\$ModuleField['$key'] = array(\n";
				else{
					$string .= $tab_num." '$key' => array(\n";
				}
					
				$string .= $this->convert_array_to_string_code($value,'',$lv+1);
				
				if($lv==1)
					$string .= $tab_num.");\n";
				else
					$string .= $tab_num." ),\n";
			}else{
				$value = str_replace("'",'"',$value);
				if($lv==1)
					$string .= $tab_num."\$ModuleField['$key'] = '$value';\n";
				else{
					$string .= $tab_num." '$key' => '$value',\n";
				}					
				
			}
		}
		return $string;
	}

}