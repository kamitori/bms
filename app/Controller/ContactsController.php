<?php
App::import('Vendor', 'cal_price/cal_price');
App::uses('AppController', 'Controller');
class ContactsController extends AppController {

	var $name = 'Contacts';
	public $helpers = array();
	public $opm; //Option Module
    var $modelName = 'Contact';
	public function beforeFilter(){
		parent::beforeFilter();
		$this->set_module_before_filter('Contact');
		$this->sub_tab_default = 'addresses ';
	}

    public function ajax_save()
    {
        if($_POST['field'] == 'mobile') {
            $contact['mobile'] = $_POST['value'];
            $contact['mobile_login'] = preg_replace( '/[^0-9]/', '', $_POST['value']);
            $contact['_id'] = new MongoId($_POST['ids']);
            $this->opm->save($contact);
            echo $_POST['ids'].'||'.$_POST['value'];
            die;
        }
        parent::ajax_save();
    }


	public function rebuild_setting($arr_setting=array()){
		// parent::rebuild_setting($arr_setting);
         $arr_setting = $this->opm->arr_settings;
        if(!$this->check_permission($this->name.'_@_entry_@_edit')){
            $arr_setting = $this->opm->set_lock(array(),'out');
            $this->set('address_lock', '1');
        }
        if(!$this->check_permission($this->name.'_@_other_tab_@_change_support')){
            unset($arr_setting['relationship']['other']['block']['3']['field']['anvy_support']);
        }
        $this->selectModel('Contact');
        $arr_tmp = $this->Contact->select_one(array('_id'=>new MongoId($this->get_id())),array('is_employee','is_customer'));

        if(isset($arr_tmp['is_customer']) && $arr_tmp['is_customer'] == 1 && $arr_tmp['is_employee'] == 0){
            unset($arr_setting['relationship']['personal']);
            unset($arr_setting['relationship']['rate']);
            unset($arr_setting['relationship']['expense']);
            unset($arr_setting['relationship']['leave']);
            unset($arr_setting['relationship']['workings_holidays']);
            unset($arr_setting['relationship']['user_refs']);
        }

        if(isset($arr_tmp['is_employee']) && $arr_tmp['is_employee'] == 1 && $arr_tmp['is_customer'] == 0){
            unset($arr_setting['relationship']['product']);
            unset($arr_setting['relationship']['quote']);
            unset($arr_setting['relationship']['order']);
            unset($arr_setting['relationship']['shipping']);
            unset($arr_setting['relationship']['invoice']);
        }

        if(isset($arr_tmp['is_employee']) && isset($arr_tmp['is_customer']) && $arr_tmp['is_employee'] == 0 && $arr_tmp['is_customer'] == 0){
            unset($arr_setting['relationship']['product']);
            unset($arr_setting['relationship']['quote']);
            unset($arr_setting['relationship']['order']);
            unset($arr_setting['relationship']['shipping']);
            unset($arr_setting['relationship']['invoice']);

            unset($arr_setting['relationship']['personal']);
            unset($arr_setting['relationship']['rate']);
            unset($arr_setting['relationship']['expense']);
            unset($arr_setting['relationship']['leave']);
            unset($arr_setting['relationship']['workings_holidays']);
            unset($arr_setting['relationship']['user_refs']);
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



	//Entry - trang chi tiet
 	public function entry() {

        $arr_set = $this->opm->arr_settings;
        $arr_tmp = array();
        // Get value id
        $iditem = $this->get_id();  // lay  "_id": ObjectId("53315b02005fc3e003001226"),
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
                        if ($field=='is_employee' && $arr_tmp[$field] == 1){
                            $arr_set['field']['panel_1']['code']['after_field'] = 'is_employee';
                            $arr_set['field']['panel_1']['is_employee']['type'] = 'checkbox';
                            $arr_set['field']['panel_1']['is_customer']['type'] = 'hidden';
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

            $this->show_footer_info($arr_tmp);
        }else {
            $nextcode = $this->opm->get_auto_code('code');
            $arr_set['field']['panel_1']['code']['default'] = $nextcode;
            $this->set('item_title', array('code' => $nextcode));
        }

        $this->set('arr_settings', $arr_set);
        $this->sub_tab_default = 'addresses';
        $this->sub_tab('', $iditem);

        $this->set_entry_address($arr_tmp, $arr_set);
        parent::entry();
    }

    public function reload_address($address_key = '') {
        if (isset($_POST['address_key']))
            $address_key = $_POST['address_key'];
        $ids = $this->get_id();
        $arr_tmp = $this->opm->select_one(
            array('_id' => new MongoId($ids)),
            array('addresses')
        );

        $arr_temp = array();
        if (isset($arr_tmp['addresses'][0]))
            foreach ($arr_tmp['addresses'][0] as $kk => $vv) {
                $arr_temp[$kk] = $vv;
                if ($kk == 'province_state') {
                    $arr_province = $this->province();
                    if (isset($arr_province[$vv]))
                        $arr_temp[$kk] = $arr_province[$vv];
                }else if ($kk == 'country') {
                    $arr_country = $this->country();
                    if (isset($arr_country[$vv]))
                        $arr_temp[$kk] = $arr_country[$vv];
                }
            }
        echo json_encode($arr_temp);
        die;
    }

    public function set_entry_address($arr_tmp, $arr_set) {
        $address_fset = array('address_1', 'address_2', 'address_3', 'town_city', 'country', 'province_state', 'zip_postcode');
        $address_value = $address_province_id = $address_country_id = $address_province = $address_country = array();
        $address_controller = array('invoice');
        $address_value['invoice'] = array('', '', '', '', "CA", '', '');
        $this->set('address_controller', $address_controller); //set
        $address_key = array('invoice'); // ******chu y ********

        $this->set('address_key', $address_key); //set
        $address_country = $this->country();

        $arr_address_tmp = array();
        if(!isset($arr_tmp['addresses_default_key']))
            $arr_tmp['addresses_default_key'] = 0;
        if(isset($arr_tmp['addresses'][$arr_tmp['addresses_default_key']])){
            foreach($arr_tmp['addresses'][$arr_tmp['addresses_default_key']] as $key=>$value){
                if($key=='deleted') continue;
                $arr_address_tmp['invoice_'.$key] = $value;
            }
        }

        // $arr_set luu nguyen mang db toan bo trang rat lon
        // $arr_tmp la 1 mang lon gom nhiu [addresses], [invoice_address] , đổ dữ liệu từ db ra là nhờ biến nay   ****************
        $arr_tmp['invoice_address'][0] = array();
        foreach ($address_key as $kss => $vss) {  // vss = invoice
            //neu ton tai address trong data base
            if (isset($arr_tmp['invoice_address'][0])) {
                if(!empty($arr_address_tmp))
                    $arr_tmp['invoice_address'][0] = $arr_address_tmp;
                $arr_temp_op = $arr_tmp['invoice_address'][0];

                for ($i = 0; $i < count($address_fset); $i++) { //loop field and set value for display
                    if (isset($arr_temp_op[$vss . '_' . $address_fset[$i]])) {
                        $address_value[$vss][$i] = $arr_temp_op[$vss . '_' . $address_fset[$i]];
                    } else {
                        $address_value[$vss][$i] = '';
                    }
                }
                if (isset($arr_temp_op[$vss . '_country_id']))
                    $address_province[$vss] = $this->province($arr_temp_op[$vss . '_country_id']);
                else
                    $address_province[$vss] = $this->province();
                //set province
                if (isset($arr_temp_op[$vss . '_province_state_id']) && $arr_temp_op[$vss . '_province_state_id'] != '' && isset($address_province[$vss][$arr_temp_op[$vss . '_province_state_id']]))
                    $address_province_id[$kss] = $arr_temp_op[$vss . '_province_state_id'];
                else if (isset($arr_temp_op[$vss . '_province_state']))
                    $address_province_id[$kss] = $arr_temp_op[$vss . '_province_state'];
                else
                    $address_province_id[$kss] = '';

                //set country
                if (isset($arr_temp_op[$vss . '_country_id'])) {
                    $address_country_id[$kss] = $arr_temp_op[$vss . '_country_id'];
                    $address_province[$vss] = $this->province($arr_temp_op[$vss . '_country_id']);
                } else {
                    $address_country_id[$kss] = "CA";
                    $address_province[$vss] = $this->province("CA");
                }
                $address_add[$vss] = '0';
            }
           /* if (isset($arr_tmp['addresses'][0])) {  // Neu ton tai $arr_tmp['invoice_address'][0] sua lai $arr_tmp['addresses'][0]
                $arr_temp_op = $arr_tmp['addresses'][0];  //  $arr_temp_op là mãng quan trọng nhát : array( country=>Viet Nam, address_1=>'1 oki',....)
                for ($i = 0; $i < count($address_fset); $i++) { //loop field and set value for display
                    if (isset($arr_temp_op[$address_fset[$i]])) {
                        $address_value[$vss][$i] = $arr_temp_op[$address_fset[$i]];  // $address_value chính là giá trị trực tiếp show ra giao diện
                    } else {
                        $address_value[$vss][$i] = '';
                    }
                }

                if (isset($arr_temp_op[$vss . 'country_id']))
                    $address_province[$vss] = $this->province($arr_temp_op['country_id']); // array(California=>'California',New York =>'New York',.)
                else
                    $address_province[$vss] = $this->province();

                //set province
                if (isset($arr_temp_op[$vss . 'province_state_id']) && $arr_temp_op[$vss . 'province_state_id'] != '' && isset($address_province[$vss][$arr_temp_op[$vss . 'province_state_id']]))
                    $address_province_id[$kss] = $arr_temp_op[$vss . 'province_state_id'];
                else if (isset($arr_temp_op['province_state']))
                    $address_province_id[$kss] = $arr_temp_op['province_state'];
                else
                    $address_province_id[$kss] = '';

                //set country
                if (isset($arr_temp_op['country_id'])) {
                    $address_country_id[$kss] = $arr_temp_op['country_id'];
                    $address_province[$vss] = $this->province($arr_temp_op['country_id']);
                } else {
                    $address_country_id[$kss] = "CA";
                    $address_province[$vss] = $this->province("CA");
                }

                $address_add[$vss] = '0';
                //chua co address trong data
            } */else {
                $address_country_id[$kss] = "CA";
                $address_province[$vss] = $this->province("CA");
                $address_add[$vss] = '1';
            }
        }

        $this->set('address_value', $address_value);  // $address_value chính là giá trị trực tiếp show ra giao diện
        $address_hidden_field = array('invoice_address');
        $this->set('address_hidden_field', $address_hidden_field); //set
        $address_label[0] = $arr_set['field']['panel_3']['invoice_address']['name'];
        $this->set('address_label', $address_label); //set
        $address_conner[0]['top'] = 'hgt';// fixbor';
        $address_conner[0]['bottom'] = 'jt_ppbot'; //fixbor2
        $address_conner[1]['top'] = 'hgt';
        $address_conner[1]['bottom'] = 'fixbor3 jt_ppbot';
        $this->set('address_conner', $address_conner); //set
        $this->set('address_country', $address_country); //set
        $this->set('address_country_id', $address_country_id); //set
        $this->set('address_province', $address_province); //set
        $this->set('address_province_id', $address_province_id); //set
        $this->set('address_more_line', 2); //set
        $this->set('address_onchange', "save_address_pr('\"+keys+\"');");
        /*if (isset($arr_tmp['company_id']) && strlen($arr_tmp['company_id']) == 24)
            $this->set('address_company_id', 'company_id');*/
        $this->set('address_contact_id', 'mongo_id');
        if (isset($arr_tmp['contact_id']) && strlen($arr_tmp['contact_id']) == 24)
            $this->set('address_contact_id', 'contact_id');
        $this->set('address_add', $address_add);
    }


    public function arr_associated_data($field = '', $value = '', $valueid = '',$fieldopt='') {
        $arr_return = array();
        $arr_return[$field] = $value;
        $tmp_data = array();
        if(isset($_POST['arr']) && is_string($_POST['arr']) && $_POST['arr']!='')
            $tmp_data = (array)json_decode($_POST['arr']);
        if(isset($tmp_data['keys'])){
            if( ($tmp_data['keys']=='update' || $tmp_data['keys']=='add')
                &&!$this->check_permission($this->name.'_@_entry_@_edit')){
                echo 'You do not have permission on this action.';
                die;
            }
        }
        /**
         * Chọn Company  ***********************************************
         */
        if ($field == 'company' && $valueid != '') {
            $arr_return = array(
                'company_name' => '',
                'company_id' => '',
                'contact_name' => '',
                'contact_id' => '',
                'our_rep' => '',
                'our_rep_id' => '',
                'phone' => '',
                'email' => '',
                'addresses' => array(),
            );
            //change company
            $arr_return['company_name'] = $value;
            $arr_return['company_id'] = new MongoId($valueid);

            //find contact and more from Company
            $this->selectModel('Company');
            $arr_company = $this->Company->select_one(array('_id' => new MongoId($valueid)));

            $this->selectModel('Contact');
            $arr_contact = $arrtemp = array();
            // is set contact_default_id
            if (isset($arr_company['contact_default_id']) && is_object($arr_company['contact_default_id'])) {
                $arr_contact = $this->Contact->select_one(array('_id' => $arr_company['contact_default_id']));

                // not set contact_default_id
            } else {
                $arr_contact = $this->Contact->select_all(array(
                    'arr_where' => array('company_id' => new MongoId($valueid)),
                    'arr_order' => array('_id' => -1),
                ));
                $arrtemp = iterator_to_array($arr_contact);
                if (count($arrtemp) > 0) {
                    $arr_contact = current($arrtemp);
                } else
                    $arr_contact = array();
            }


            //change our_rep
            if (isset($arr_company['our_rep']) && isset($arr_company['our_rep_id']) && $arr_company['our_rep_id'] != '') {
                $arr_return['our_rep_id'] = $arr_company['our_rep_id'];
                $arr_return['our_rep'] = $arr_company['our_rep'];
            }else{
                $arr_return['our_rep_id'] = $this->opm->user_id();
                $arr_return['our_rep'] = $this->opm->user_name();
            }


            //change phone
            if (isset($arr_company['phone']))
                $arr_return['company_phone'] = $arr_company['phone'];
            if (isset($arr_contact['direct_dial']))
                    $arr_return['direct_phone'] = $arr_contact['direct_dial'];
            if (isset($arr_contact['home_phone']))
                    $arr_return['home_phone'] = $arr_contact['home_phone'];
            if (isset($arr_contact['mobile']))
                    $arr_return['mobile'] = $arr_contact['mobile'];


            if (!isset($arr_contact['direct_dial']) && !isset($arr_contact['mobile']))
                    $arr_return['phone'] = '';  //bat buoc phai co dong nay khong thi no se lay du lieu cua cty truoc

            if (isset($arr_company['email']) && $arr_company['email']!='')
                $arr_return['email'] = $arr_company['email'];
            elseif (isset($arr_contact['email']))
                $arr_return['email'] = $arr_contact['email'];
            elseif (!isset($arr_contact['email']))
                $arr_return['email'] = '';

            if (isset($arr_company['fax']))
                $arr_return['fax'] = $arr_company['fax'];
            elseif (isset($arr_contact['fax']))
                $arr_return['fax'] = $arr_contact['fax'];
            elseif (!isset($arr_contact['fax']))
                $arr_return['fax'] = '';

            //change address
            if (isset($arr_company['addresses_default_key']))
                $add_default = $arr_company['addresses_default_key'];
            if (isset($add_default) && isset($arr_company['addresses'][$add_default])) {
                foreach ($arr_company['addresses'][$add_default] as $ka => $va) {
                    if ($ka != 'deleted')
                        $arr_return['addresses'][0][$ka] = $va;
                    else
                        $arr_return['addresses'][0][$ka] = $va;
                }
            }

            //change tax
            if((!isset($arr_return['invoice_address'][0]['invoice_province_state_id']) || $arr_return['invoice_address'][0]['invoice_province_state_id']=='') && isset($arr_return['invoice_address'][0]['invoice_province_state'])){
                $province_name = $arr_return['invoice_address'][0]['invoice_province_state'];
                $arr_province = $this->province_reverse('CA');
                if(isset($arr_province[$province_name]))
                    $arr_return['invoice_address'][0]['invoice_province_state_id'] = $arr_province[$province_name];
            }
        } else if( $field == 'addresses'){
            if($fieldopt == 'default'){
                foreach($value as $key => $val){
                    if($val['deleted']) continue;
                    if($key != $valueid)
                        $value[$key]['default'] = false;
                }
                $arr_return['addresses_default_key'] = $valueid;
                $arr_return[$field] = $value;
                return $arr_return;
            }
        } else if( $field == 'anvy_support'){
            if($value==1)
                $this->opm->clear_anvy_support();
            else{
                $arr_save = array();
                $arr_save['_id'] = new MongoId('5271f4fa67b96d7f11000022');
                $arr_save['anvy_support'] = 1;
                $this->opm->save($arr_save);
            }
        } else if( $field == 'full_name' ) {
            $id = $this->get_id();
            $arr_return[$field] = trim( $arr_return[$field] );
            while( $this->opm->count(array('full_name' => $arr_return[$field], '_id' => array('$ne' => new MongoId($id)))) ){
                $arr_return[$field] .= rand(1,9);
            }
        } else if ($field == 'mobile') {
            $arr_return['mobile'] = $value;
            $arr_return['mobile_login'] = preg_replace( '/[^0-9]/', '', $value);
        }

        return $arr_return;
    }

	//Search function
	   public function entry_search() {

        if (!empty($this->data) && $this->request->is('ajax')) {

            $post = $this->data['Contact'];
            $cond = array();

            if( strlen($post['no']) > 0 )$cond['no'] = (int)$post['no'];
            if( $post['is_customer'] )$cond['is_customer'] = 1;
            if( $post['is_employee'] )$cond['is_employee'] = 1;

            if( strlen($post['first_name']) > 0 )$cond['first_name'] = new MongoRegex('/' . trim($post['first_name']) . '/i');
            if( strlen($post['last_name']) > 0 )$cond['last_name'] = new MongoRegex('/' . trim($post['last_name']) . '/i');

            if( strlen($post['title']) > 0 )$cond['title'] = $post['title'];
            if( strlen($post['type']) > 0 )$cond['type'] = $post['type'];
            if( strlen($post['position']) > 0 )$cond['position'] = $post['position'];
            if( strlen($post['department']) > 0 )$cond['department'] = $post['department'];

            if( strlen($post['direct_dial']) > 0 )$cond['direct_dial'] = new MongoRegex('/' . trim($post['direct_dial']) . '/i');
            if( strlen($post['mobile']) > 0 )$cond['mobile'] = new MongoRegex('/' . trim($post['mobile']) . '/i');
            if( strlen($post['email']) > 0 )$cond['email'] = new MongoRegex('/' . trim($post['email']) . '/i');
            if( strlen($post['fax']) > 0 )$cond['fax'] = new MongoRegex('/' . trim($post['fax']) . '/i');
            if( strlen($post['home_phone']) > 0 )$cond['home_phone'] = new MongoRegex('/' . trim($post['home_phone']) . '/i');
            if( strlen($post['company_phone']) > 0 )$cond['company_phone'] = new MongoRegex('/' . trim($post['company_phone']) . '/i');
            if( strlen($post['extension_no']) > 0 )$cond['extension_no'] = new MongoRegex('/' . trim($post['extension_no']) . '/i');

            if( strlen($post['company_id']) > 0 )$cond['company_id'] = new MongoId($post['company_id']);

            if( strlen($post['default_address_1']) > 0 )$cond['addresses'] = array('$elemMatch' => array('address_1' => new MongoRegex('/' . trim($post['default_address_1']) . '/i'), 'default' => true) );
            if( strlen($post['default_address_2']) > 0 )$cond['addresses'] = array('$elemMatch' => array('address_2' => new MongoRegex('/' . trim($post['default_address_2']) . '/i'), 'default' => true) );
            if( strlen($post['default_address_3']) > 0 )$cond['addresses'] = array('$elemMatch' => array('address_3' => new MongoRegex('/' . trim($post['default_address_3']) . '/i'), 'default' => true) );
            if( strlen($post['default_town_city']) > 0 )$cond['addresses'] = array('$elemMatch' => array('town_city' => new MongoRegex('/' . trim($post['default_town_city']) . '/i'), 'default' => true) );
            if( strlen($post['default_province_state']) > 0 )$cond['addresses'] = array('$elemMatch' => array('province_state' => new MongoRegex('/' . trim($post['default_province_state']) . '/i'), 'default' => true) );
            if( strlen($post['default_country']) > 0 )$cond['addresses'] = array('$elemMatch' => array('country' => new MongoRegex('/' . trim($post['default_country']) . '/i'), 'default' => true) );

            if( $post['inactive'] )$cond['inactive'] = 1;

            $this->selectModel('Contact');
            $this->identity($cond);
            $tmp = $this->Contact->select_one($cond);
            if( $tmp ){
                $this->Session->write('contacts_entry_search_cond', $cond);

                $cond['_id'] = array('$ne' => $tmp['_id']);
                $tmp1 = $this->Contact->select_one($cond);
                if( $tmp1 ){
                    echo 'yes'; die;
                }
                echo 'yes_1_'.$tmp['_id']; die; // chỉ có 1 kết quả thì chuyển qua trang entry luôn
            }else{
                echo 'no'; die;
            }

            echo 'ok';
            die;
        }

        $this->selectModel('Setting');
        $this->set('arr_contacts_title', $this->Setting->select_option(array('setting_value' => 'contacts_title'), array('option')));
        $this->set('arr_contacts_department', $this->Setting->select_option(array('setting_value' => 'contacts_department'), array('option')));
        $this->set('arr_contacts_position', $this->Setting->select_option(array('setting_value' => 'contacts_position'), array('option')));
        $this->set('arr_contacts_type', $this->Setting->select_option(array('setting_value' => 'contacts_type'), array('option')));
        $this->set('set_footer', 'footer_search');
        $this->set('address_country', $this->country());
        $this->set('address_province', $this->province("CA"));

        // Get info for subtask
        // $this->sub_tab('', $arr_tmp['_id']);
    }

    public function find_all(){
        $this->Session->delete('contacts_entry_search_cond');
        $this->redirect('/contacts/lists');
    }

	//Swith options function
    public function swith_options($option = '') {
        parent::swith_options($option);
        $this->selectModel('Contact');
        $v_id_current =  $this->Contact->select_one(array(), array(), array('no' => -1));
        if( !$this->Session->check('Contact_entry_id') ){
            $this->Session->write('Contact_entry_id', $v_id_current['_id']);
        }

        if ($option == 'active_contacts') {
            $or_where = array(
                'inactive' => 0
            );
            $this->Session->write('contacts_entry_search_cond',$or_where);
            echo URL . '/' . $this->params->params['controller'] .'/lists';
        }
        elseif ($option == 'inactive_contacts') {
            $or_where = array(
                'inactive' => 1
            );
            $this->Session->write('contacts_entry_search_cond',$or_where);
            echo URL . '/' . $this->params->params['controller'] .'/lists';
        }
        elseif ($option == 'active_customers_(who_are_individuals)') {
            $or_where = array(
                'inactive' => 0,
                'is_customer'=>1
            );
            $this->Session->write('contacts_entry_search_cond',$or_where);
            echo URL . '/' . $this->params->params['controller'] .'/lists';
        }
        elseif ($option == 'inactive_customers_(who_are_individuals)') {
            $or_where = array(
                'inactive' => 1,
                'is_customer'=>1
            );
            $this->Session->write('contacts_entry_search_cond',$or_where);
            echo URL . '/' . $this->params->params['controller'] .'/lists';
        }
        elseif ($option == 'active_contacts_that_are_not_customers_or_employees') {
            $or_where = array(
                'inactive' => 0,
                'is_customer'=>0,
                'is_employee'=>0
            );
            $this->Session->write('contacts_entry_search_cond',$or_where);
            echo URL . '/' . $this->params->params['controller'] .'/lists';
        }
        else if($option == 'create_enquiry')
        {
            if($this->Session->check('Contact_entry_id')!='')
                echo URL . '/' . $this->params->params['controller'] .'/enquiries_add_option/'.$this->Session->read('Contact_entry_id');

        }
        else if($option == 'create_quotation')
        {
            if($this->Session->check('Contact_entry_id')!='')
                echo URL . '/' . $this->params->params['controller']  .'/quotes_add/'.$this->Session->read('Contact_entry_id');
        }
        else if($option == 'create_job'){
            if($this->Session->check('Contact_entry_id')!='')
                echo URL . '/' . $this->params->params['controller']  .'/jobs_add_option/'.$this->Session->read('Contact_entry_id');

        }
        else if($option == 'create_task'){
            if($this->Session->check('Contact_entry_id')!='')
                echo URL . '/' . $this->params->params['controller']  .'/task_add/'.$this->Session->read('Contact_entry_id');

        }
        else if($option == 'create_sales_order'){
            if($this->Session->check('Contact_entry_id')!='')
                echo URL . '/' . $this->params->params['controller']  .'/orders_add_salesorder/'.$this->Session->read('Contact_entry_id');

        }
//      else if($option == 'create_purchase_order')
//      {
//          if($this->Session->check('Contact_entry_id')!='')
//              echo URL . '/' . $this->params->params['controller']  .'/orders_add_purchasesorder/'.$this->Session->read('Contact_entry_id');
//
//      }
        else if($option == 'create_shipping'){
            if($this->Session->check('Contact_entry_id')!='')
                echo URL . '/' . $this->params->params['controller']  .'/shipping_add/'.$this->Session->read('Contact_entry_id');
        }
        else if($option == 'create_email'){
            echo URL .'/'.$this->params->params['controller']. '/create_email';
        }
        else if($option == 'create_fax'){
            echo URL .'/'.$this->params->params['controller']. '/create_fax';
        }
        else if($option == 'create_letter'){
            echo URL .'/'.$this->params->params['controller'].'/create_letter';
        }
        else if($option == 'create_sales_invoice'){
            $this->salesinvoice_add($this->get_id());
        }
        else if($option == 'hour_worked_last'){
            echo URL.'/'.$this->params->params['controller'].'/hour_worked_last';
        }
        else if($option == 'hour_worked_current'){
            echo URL.'/'.$this->params->params['controller'].'/hour_worked_current';
        }
        else if($option == 'working_hours_report'){
            echo URL.'/'.$this->params->params['controller'].'/working_hours_report';
        }
        die();
    }

    function get_data_print_requirement($contact_id=''){
        $contact_id = $this->get_id();
        $this->selectModel('contact');
        $cond['_id'] = new MongoId($contact_id);
        $arr_query = $this->Contact->select_all(array(
            'arr_where' => $cond,
        ));
        $arr_tmp = array();
        $arr_tmp =$arr_query;
        $tmp = array();
        foreach($arr_tmp as $value){
            $tmp['company'] = isset($value['company'])?$value['company']:'';
            $tmp['no'] = isset($value['code'])?$value['code']:'';
            $tmp['contact_name'] = isset($value['contact_name'])?$value['contact_name']:'';
            $tmp['company_phone'] = isset($value['company_phone'])?$value['company_phone']:'';
            $tmp['date'] = isset($value['date'])?$value['date']:'';
            $tmp['status'] = isset($value['status'])?$value['status']:'';
            $tmp['rating'] = isset($value['rating'])?$value['rating']:'';
            $tmp['default_address_1'] = isset($value['default_address_1'])?$value['default_address_1']:'';
            $tmp['detail'] = isset($value['detail'])?$value['detail']:'';
        }
        return $tmp;
    }

    function print_requirements_pdf($contact_id){
        $this->layout = 'pdf';
        $date_now = date('Ymd');
        $time=time();
        $filename = 'REQ'.$date_now.$time;
        $tmp = array();
        $tmp = $this->get_data_print_requirement();
        //pr($tmp);die();
            $html='';
            $html .= ' <table cellpadding="4" cellspacing="0" class="tab_nd">';

                //$html .= '<table cellpadding="4" cellspacing="0" class="tab_nd2">';

            $html .= ' <tr class="border_2">
                <td width="9%" class="first top border_left border_btom" text-align="left">';

            if(isset($tmp['no']))
            $html .= $tmp['no'];


            $html .= '</td>
                <td width="25%" class="top border_btom border_left">';

            if(isset($tmp['company']))
            $html .= $tmp['company'];

            $html .='</td>
                <td width="23%" class="top border_btom border_left">';

            if(isset($tmp['contact_name']))
                $html .= $tmp['contact_name'];


            $html .='</td>
                <td align="left" width="13.3%" class="top border_btom border_left">
                    ';


            if(isset($tmp['company_phone']))
            $html .= $tmp['company_phone'];

            $html.='
                </td>
                <td align="left" width="12%" class="top border_btom border_left">
                    ';

            if(isset($tmp['date']))
                //pr($tmp['date']);die();
            $html .= $tmp['date'];

            $html.='
                </td>
                <td width="10%" class="end top border_btom border_left">
                    ';
            if(isset($tmp['status']))
            $html .= $tmp['status'];


            $html.='
                </td>
                <td width="7%" class="end top border_btom border_left">
                    ';


            if(isset($tmp['rating']))
            $html .= $tmp['rating'];


            $html.='
                </td>
            </tr>
        </table>
    ';


            $html .= ' <table cellpadding="4" cellspacing="0" class="tab_nd">';

                //$html .= '<table cellpadding="4" cellspacing="0" class="tab_nd2">';

            $html .= ' <tr class="border_2">
                <td width="99.5%" class="first top border_left border_btom" text-align="left"> Address: ';

            if(isset($tmp['default_address_1']))
            $html .= $tmp['default_address_1'];


            $html .= '</td> ';


            $html.='</tr></table>';


            $html .= ' <table cellpadding="4" cellspacing="0" class="tab_nd">';

                //$html .= '<table cellpadding="4" cellspacing="0" class="tab_nd2">';

            $html .= ' <tr class="border_2">
                <td width="99.5%" class="first top border_left border_btom" text-align="left"> <b>Requirements 1</b>:<br/> ';

            if(isset($tmp['detail']))
            $html .= str_replace("\n", "<br>", $tmp['detail']);


            $html .= '</td> ';


            $html.='</tr></table>';


        $html_new = $html;

        // =================================================== tao file PDF ==============================================//
        include(APP.'Vendor'.DS.'xtcpdf.php');

        $pdf = new XTCPDF();
        date_default_timezone_set('UTC');
        $pdf->today=date("g:i a, j F, Y");
        $textfont = 'freesans'; // looks better, finer, and more condensed than 'dejavusans'

        $pdf->SetCreator(PDF_CREATOR);
        $pdf->SetAuthor('Anvy Digital');
        $pdf->SetTitle('Anvy Digital Company');
        $pdf->SetSubject('Company');
        $pdf->SetKeywords('Company, PDF');

    // set default header data
        $pdf->setPrintHeader(true);
        $pdf->setPrintFooter(true);

    // set default monospaced font
        $pdf->SetDefaultMonospacedFont(2);

    // set margins
        $pdf->SetMargins(10, 52, 10);
        $pdf->file3 = 'img'.DS.'bar_662x23.png';

        $pdf->file2_left=115;
        $pdf->file2='img'.DS.'contact_Requirements_title.png';
        $pdf->file4 = 'img'.DS.'null.png';
        $pdf->file5 = 'img'.DS.'null.png';
        $pdf->file6 = 'img'.DS.'null.png';
        $pdf->file7 = 'img'.DS.'null.png';
        $pdf->print1 = '';


        $pdf->bar_top_left=136;


        $pdf->hidden_left=153;
        $pdf->hidden_content='';

        $pdf->bar_big_content='---------------------------------------------------------------------------------------------------------------------------------------------------------------';

        $pdf->bar_words_content='Ref no    Company                               Contact                                 Phone              Date              Status         Rating ';
        $pdf->bar_mid_content='         |                                                    |                               |                         |                          |              |';


        $pdf->printedat_left=134;
        $pdf->time_left=152;

        $pdf->SetHeaderMargin(PDF_MARGIN_HEADER);
        $pdf->SetFooterMargin(PDF_MARGIN_FOOTER);

    // set auto page breaks
        $pdf->SetAutoPageBreak(TRUE, 30);

    // set image scale factor
        $pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);
        if (@file_exists(dirname(__FILE__).DS.'lang'.DS.'eng.php')) {
            require_once(dirname(__FILE__).DS.'lang'.DS.'eng.php');
            $pdf->setLanguageArray($l);
        }

    // ---------------------------------------------------------
    // set font
        $pdf->SetFont($textfont, '', 9);

    // add a page
        $pdf->AddPage();
        $pdf->SetMargins(10, 19, 10);

        $pdf->file1 = 'img'.DS.'null.png';
        $pdf->file2 = 'img'.DS.'null.png';
        $pdf->file4 = 'img'.DS.'null.png';
        $pdf->file5 = 'img'.DS.'null.png';
        $pdf->file6 = 'img'.DS.'null.png';
        $pdf->file7 = 'img'.DS.'null.png';
        $pdf->file3_top=10;
        $pdf->bar_words_top=11;
        $pdf->bar_mid_top=10.6;
        $pdf->hidden_content='';
        $pdf->bar_top_content='';
        $pdf->today='';
        $pdf->print='';

        $html='
                <style>
                    table{
                        font-size: 12px;
                        font-family: arial;
                    }
                    td.first{
                        border-left:1px solid #e5e4e3;
                    }
                    td.end{
                        border-right:1px solid #e5e4e3;
                    }
                    td.top{
                        color:#fff;
                        font-weight:bold;
                        background-color:#911b12;
                        border-top:1px solid #e5e4e3;
                    }
                    td.bottom{
                        border-bottom:1px solid #e5e4e3;
                    }
                    .option{
                        color: #3d3d3d;
                        font-weight:bold;
                        font-size:18px;
                        text-align: center;
                        width:100%;
                    }
                    .border_left{
                        border-left:1px solid #A84C45;
                    }
                    .border_1{
                        border-bottom:1px solid #911b12;
                    }

                </style>
                <style>
                        table.tab_nd{
                            font-size: 12px;
                            font-family: arial;
                        }
                        table.tab_nd td.first{
                            border-left:1px solid #e5e4e3;
                        }
                        table.tab_nd td.end{
                            border-right:1px solid #e5e4e3;
                        }
                        table.tab_nd td.top{
                            background-color:#FDFBF9;
                            border-top:1px solid #e5e4e3;
                            font-weight: normal;
                            color: #3E3D3D;
                        }
                        table.tab_nd .border_2{
                            border-bottom:1px solid red;
                        }
                        table.tab_nd .border_left{
                            border-left:1px solid #E5E4E3;
                            border-bottom:1px solid #E5E4E3;
                        }
                        table.tab_nd .border_btom{
                            border-bottom:1px solid #E5E4E3;
                        }

                    </style>
                    <style>
                            table.tab_nd2{
                                font-size: 12px;
                                font-family: arial;
                            }
                            table.tab_nd2 td.first{
                                border-left:1px solid #e5e4e3;
                            }
                            table.tab_nd2 td.end{
                                border-right:1px solid #e5e4e3;
                            }
                            table.tab_nd2 td.top{
                                background-color:#EDEDED;
                                border-top:1px solid #e5e4e3;
                                font-weight: normal;
                                color: #3E3D3D;
                            }
                            table.tab_nd2 .border_2{
                                border-bottom:1px solid red;
                            }
                            table.tab_nd2 .border_left{
                                border-left:1px solid #E5E4E3;
                                border-bottom:1px solid #E5E4E3;
                            }
                            table.tab_nd2 .border_btom{
                                border-bottom:1px solid #E5E4E3;
                            }
                            .size_font{
                                font-size: 12px !important;
                            }

                        </style>
            ';
        $html.=$html_new;
        $html .= '


        <div style=" clear:both; color: #c9c9c9;"><br />
    ---------------------------------------------------------------------------------------------------------------------------------------------------------------------------------
        </div><br />
        ';
        $pdf->writeHTML($html, true, false, true, true, '');
        $pdf->Output(APP. 'webroot'.DS. 'upload'.DS .$filename.'.pdf', 'F');
        $this->redirect('/upload/'. $filename .'.pdf');
        die;
    }





    //////////////////////////////////////////////14-4-2014////////////////////////////////////////////////////////////
    function addresses(){
        $this->selectModel('Contact');
        $option_select_dynamic = array();
        $contact = $this->Contact->select_one(array('_id'=> new MongoId($this->get_id())),array('addresses'));
        $this->selectModel('Province');
        $provinces = $this->Province->get_all_provinces();
        $arr_all_province = array();
        $subdatas = array();
        if(!isset($contact['addresses']))
            $contact['addresses'] = array();
        $this->selectModel('Country');
        $countries = $this->Country->get_countries();
        $this->selectModel('Province');
        foreach($contact['addresses'] as $key => $value){
            $option_select_dynamic['country_id_'.$key] = $countries;
            $option_select_dynamic['province_state_id_'.$key] = $this->Province->get_provinces($value['country_id']);
        }
        $subdatas['addresses'] = $contact['addresses'];
        $this->set('provinces', $provinces);
        $this->set_select_data_list('relationship','addresses');
        $this->set('subdatas', $subdatas);
        $this->set('option_select_dynamic', $option_select_dynamic);

    }

    function addresses_add($contact_id) {
        $this->selectModel('Contact');
        $this->Contact->collection->update(
                array('_id' => new MongoId($contact_id)), array('$push' => array(
                'addresses' => array(
                    'name' => '',
                    'default' => false,
                    'address_1' => '',
                    'address_2' => '',
                    'address_3' => '',
                    'town_city' => '',
                    'zip_postcode' => '',
                    'province_state' => '',
                    'province_state_id' => '',
                    'country' => 'Canada',
                    'country_id' => "CA",
                    'deleted' => false
                )))
        );
        $this->addresses($contact_id);
        $this->render('addresses');
    }

    function enquiries($contact_id){
        $this->selectModel('Enquiry');
        $arr_enquiries = $this->Enquiry->select_all(array(
                                                    'arr_where' => array('contact_id'=>new MongoId($contact_id))
                                                    ));
        $arr_tmp = array();
        foreach($arr_enquiries as $key => $value){
            $arr_tmp[$key] = $value;
        }
        //pr($arr_tmp);die;
        $subdatas = array();
        $subdatas['enquiries'] = $arr_tmp;
        $this->set('subdatas', $subdatas);
    }

    function enquiries_add($contact_id) {
        $this->selectModel('Contact');
        $arr_contact = $this->Contact->select_one(array('_id' => new MongoId($contact_id)));
        $this->selectModel('Enquiry');
        $arr_tmp = $this->Enquiry->select_one(array(), array(), array('no' => -1));
        $arr_save = array();
        $arr_save = $this->arr_associated_data('contact_name',$arr_contact['first_name']. ' ' .$arr_contact['last_name'], $contact_id);
        $arr_save['no'] = 1;
        if (isset($arr_tmp['no'])) {
            $arr_save['no'] = $arr_tmp['no'] + 1;
        }

        $key = 0;
        if(isset($this->data['Contact']['addresses_default_key']))
            $key = $this->data['Contact']['addresses_default_key'];
        $address = '';
        if(isset($this->data['Contact']['addresses'][$key]['country_id']))
            $address = $this->data['Contact']['addresses'][$key]['country_id'];

        $key = isset($arr_contact['addresses_default_key'])?$arr_contact['addresses_default_key']:0;
        $arr_save['date']= new MongoDate(time());
        $arr_save['status']='Hot';
        $arr_save['default_country'] = isset($arr_contact['addresses'][$key]['country'])?$arr_contact['addresses'][$key]['country']:'';
        $arr_save['default_country_id'] = isset($arr_contact['addresses'][$key]['country_id'])?$arr_contact['addresses'][$key]['country_id']:0;
        $arr_save['default_province_state'] = isset($arr_contact['addresses'][$key]['province_state'])?$arr_contact['addresses'][$key]['province_state']:'';
        $arr_save['default_province_state_id'] = isset($arr_contact['addresses'][$key]['province_state_id'])?$arr_contact['addresses'][$key]['province_state_id']:'';
        $arr_save['default_address_1'] = isset($arr_contact['addresses'][$key]['address_1'])?$arr_contact['addresses'][$key]['address_1']:'';
        $arr_save['default_address_2'] = isset($arr_contact['addresses'][$key]['address_2'])?$arr_contact['addresses'][$key]['address_2']:'';
        $arr_save['default_address_3'] = isset($arr_contact['addresses'][$key]['address_3'])?$arr_contact['addresses'][$key]['address_3']:'';
        $arr_save['default_town_city'] = isset($arr_contact['addresses'][$key]['town_city'])?$arr_contact['addresses'][$key]['town_city']:'';
        $arr_save['default_zip_postcode'] = isset($arr_contact['addresses'][$key]['zip_postcode'])?$arr_contact['addresses'][$key]['zip_postcode']:'';
        $arr_save['contact_phone'] = isset($arr_contact['phone'])?$arr_contact['phone']:'';
        $arr_save['contact_fax'] = isset($arr_contact['fax'])?$arr_contact['fax']:'';
        $arr_save['contact_email'] = isset($arr_contact['email'])?$arr_contact['email']:'';
        $arr_save['web'] = isset($arr_contact['web'])?$arr_contact['web']:'';
        $arr_save['contact'] = isset($arr_contact['name'])?$arr_contact['name']:'';

        //   14/4/2014
        $arr_save['contact_name'] = isset($arr_contact['name'])?$arr_contact['name']:'';
        $arr_save['contact_id'] = $arr_contact['_id'];
        $arr_save['contact_phone'] = isset($arr_contact['phone'])?$arr_contact['phone']:'';
        //   14/4/2014

        $this->Enquiry->arr_default_before_save = $arr_save;
        if ($this->Enquiry->add())
           echo URL.'/enquiries/entry/' . $this->Enquiry->mongo_id_after_save;
        else
            echo URL.'/enquiries/entry';
        die;
    }

    function enquiries_add_option($contact_id) {

        $this->selectModel('Contact');
        $arr_contact = $this->Contact->select_one(array('_id' => new MongoId($contact_id)));

        $this->selectModel('Enquiry');
        $arr_tmp = $this->Enquiry->select_one(array(), array(), array('no' => -1));

        $arr_save = array();
        $arr_save['no'] = 1;
        if (isset($arr_tmp['no'])) {
            $arr_save['no'] = $arr_tmp['no'] + 1;
        }
        $arr_save['web'] = '';
        $arr_save['name'] = '';

        $key = isset($arr_contact['addresses_default_key'])?$arr_contact['addresses_default_key']:0;



        $arr_save['default_country'] = isset($arr_contact['addresses'][$key]['country'])?$arr_contact['addresses'][$key]['country']:'';
        $arr_save['default_country_id'] =isset( $arr_contact['addresses'][$key]['country_id'])?$arr_contact['addresses'][$key]['country_id']:'';
        $arr_save['default_province_state'] =isset( $arr_contact['addresses'][$key]['province_state'])?$arr_contact['addresses'][$key]['province_state']:'';
        $arr_save['default_province_state_id'] = isset($arr_contact['addresses'][$key]['province_state_id'])?$arr_contact['addresses'][$key]['province_state_id']:'';
        $arr_save['default_address_1'] =isset( $arr_contact['addresses'][$key]['address_1'])?$arr_contact['addresses'][$key]['address_1']:'';
        $arr_save['default_address_2'] = isset($arr_contact['addresses'][$key]['address_2'])?$arr_contact['addresses'][$key]['address_2']:'';
        $arr_save['default_address_3'] = isset($arr_contact['addresses'][$key]['address_3'])?$arr_contact['addresses'][$key]['address_3']:'';
        $arr_save['default_town_city'] =isset( $arr_contact['addresses'][$key]['town_city'])?$arr_contact['addresses'][$key]['town_city']:'';
        $arr_save['default_zip_postcode'] =isset( $arr_contact['addresses'][$key]['zip_postcode'])?$arr_contact['addresses'][$key]['zip_postcode']:'';




        $arr_save['contact_id'] = isset($arr_contact['_id'])?$arr_contact['_id']:'';
        $v_first_name=isset($arr_contact['first_name'])?$arr_contact['first_name']:'';
        $v_last_name=isset($arr_contact['last_name'])?$arr_contact['last_name']:'';
        $arr_save['contact_name'] = $v_first_name.' '.$v_last_name;
        $arr_save['direct_phone'] = isset($arr_contact['direct_dial'])?$arr_contact['direct_dial']:'';
        $arr_save['home_phone']=isset($arr_contact['home_phone'])?$arr_contact['home_phone']:'';
        $arr_save['mobile'] = isset($arr_contact['mobile'])?$arr_contact['mobile']:'';
        $arr_save['contact_email'] =isset($arr_contact['email'])?$arr_contact['email']:'';

        $arr_save['web'] = isset($arr_contact['web']) ? $arr_contact['web'] : '';
        $this->selectModel('Company');
        $arr_save['our_rep'] = $this->Company->user_name();
        $arr_save['our_rep_id'] = $this->Company->user_id();
        $arr_save['direct_fax']= isset($arr_contact['fax'])?$arr_contact['fax']:'';
        if (isset($arr_contact['company_id'])) {
            $this->selectModel('Company');
            $arr_company = $this->Company->select_one(array('_id' => $arr_contact['company_id']));
            $arr_save['company_phone'] =isset( $arr_company['phone'])?$arr_company['phone']:'';
            $arr_save['company_fax'] =isset( $arr_company['fax'])?$arr_company['fax']:'';
            $arr_save['company_email'] =isset( $arr_company['email'])?$arr_company['email']:'';


            $arr_save['company_id'] =isset( $arr_company['_id'])?$arr_company['_id']:'';
            $arr_save['company'] =isset( $arr_company['name'])?$arr_company['name']:'';


            $this->selectModel('Company');
            if (isset($arr_company['our_rep_id']) && $arr_company['our_rep_id'] != '' && $arr_company['our_rep_id']!=null) {
                $arr_return['our_rep_id'] = $arr_company['our_rep_id'];
                $arr_return['our_rep'] = $arr_company['our_rep'];
            } else {
                $arr_return['our_rep_id'] = $this->Company->user_id();
                $arr_return['our_rep'] = $this->Company->user_name();
            }

            $arr_save['web'] = isset($arr_company['web']) ? $arr_company['web'] : '';

        }

        $arr_save['date'] = new MongoDate(time());





        if ($this->Enquiry->save($arr_save)) {
            $this->redirect('/enquiries/entry/' . $this->Enquiry->mongo_id_after_save);
        }
        $this->redirect('/enquiries/entry/');
    }

    function jobs($contact_id){
        $this->selectModel('Job');
        $arr_job = $this->Job->select_all(array(
                                          'arr_where'=>array('contact_id'=>new MongoId($contact_id))
                                          ));
        $arr_tmp = array();
        foreach($arr_job as $key => $value){
            $arr_tmp[$key] = $value;
        }

        $this->selectModel('Contact');
        $arr_contact = $this->Contact->select_one(array('_id'=> new MongoId($contact_id)),array('markup_rate','rate_per_hour'));
        $subdatas = array();
        $subdatas['jobs'] = $arr_tmp;
        $subdatas['default'] = $arr_contact;
        $this->set('subdatas', $subdatas);
    }

    function jobs_add($contact_id) {
        $this->selectModel('Contact');
        $arr_contact = $this->Contact->select_one(array('_id' => new MongoId($contact_id)));
        $this->selectModel('Job');
        $arr_tmp = $this->Job->select_one(
                                array(
                                    'no' => array(
                                        '$nin' => array(new MongoRegex('/-/'))
                                    )
                                ),
                                array(),
                                array('no' => -1));
        $arr_save = array();
        $arr_save['no'] = 1;
        if (isset($arr_tmp['no'])) {
            $arr_save['no'] = $arr_tmp['no'] + 1;
        }
        $arr_save['status_id'] = 0;
        $arr_save['name'] = '';
        $arr_save['contacts_default_key'] = 0;
        $arr_save['contacts'][] = array(
            "contact_name" => $_SESSION['arr_user']['contact_name'],
            "contact_id" => $_SESSION['arr_user']['contact_id'],
            "default" => true,
            "deleted" => false
        );
        $arr_save['type'] = '';
        $arr_save['status'] = 'New';

        $arr_save['contact_name'] = isset($arr_contact['name'])?$arr_contact['name']:'';
        $arr_save['contact_id'] = $arr_contact['_id'];
        $arr_save['contact_phone'] = isset($arr_contact['phone'])?$arr_contact['phone']:'';
        $arr_save['fax'] = isset($arr_contact['fax'])?$arr_contact['fax']:'';
        $arr_save['email']=isset($arr_contact['email'])?$arr_contact['email']:'';

        if (isset($arr_contact['contact_default_id'])) {
            $this->selectModel('Contact');
            $arr_contact = $this->Contact->select_one(array('_id' => $arr_contact['contact_default_id']));
            $arr_save['contact_id'] = $arr_contact['_id'];
            $arr_save['contact_name'] = $arr_contact['first_name'] . ' ' . $arr_contact['last_name'];

            if(isset($arr_contact['email'])&&$arr_contact['email']!='')
                $arr_save['email'] = $arr_contact['email'];

            if(isset($arr_contact['direct_dial'])&&$arr_contact['direct_dial']!='')
                $arr_save['direct_phone'] = $arr_contact['direct_dial'];

            if(isset($arr_contact['mobile'])&&$arr_contact['mobile']!='')
                $arr_save['mobile'] = $arr_contact['mobile'];
        }
        else{
            $this->selectModel('Contact');
            $arr_contact = $this->Contact->select_all(array(
                'arr_where' => array('contact_id'=>new MongoId($contact_id)),
                'arr_order' => array('_id'=>-1),
            ));
            $arrtemp = iterator_to_array($arr_contact);
            if(count($arrtemp)>0){
                $arr_contact = current($arrtemp);
            }else
                $arr_contact = array();
            if(isset($arr_contact['_id'])){
                $arr_save['contact_id'] = $arr_contact['_id'];
                $arr_save['contact_name'] = $arr_contact['first_name'] . ' ' . $arr_contact['last_name'];
            }

            if(isset($arr_contact['email'])&&$arr_contact['email']!='')
                $arr_save['email'] = $arr_contact['email'];


            if(isset($arr_contact['direct_dial'])&&$arr_contact['direct_dial']!='')
                $arr_save['direct_phone'] = $arr_contact['direct_dial'];

            if(isset($arr_contact['mobile'])&&$arr_contact['mobile']!='')
                $arr_save['mobile'] = $arr_contact['mobile'];
        }
        if (isset($work_start_sec) && $work_start_sec > 0) {
            $arr_save['work_end'] = $arr_save['work_start'] = new MongoDate($work_start_sec);
        } else {
            $arr_save['work_end'] = $arr_save['work_start'] = new MongoDate(strtotime(date('Y-m-d H:00:00')) + 3600);
        }

        $this->Job->arr_default_before_save = $arr_save;
        if ($this->Job->add()) {
          echo URL .'/jobs/entry/'. $this->Job->mongo_id_after_save;
        }
        else
          echo URL . '/jobs/entry';
        die;
    }
    ////////////////////////////////////////////14-4-2014////////////////////////////////////////////////////////////////////////

     function jobs_add_option($contact_id) {
        $this->selectModel('Contact');
        $arr_contact = $this->Contact->select_one(array('_id' => new MongoId($contact_id)));
        $this->selectModel('Job');
        $arr_tmp = $this->Job->select_one(
                                array(
                                    'no' => array(
                                        '$nin' => array(new MongoRegex('/-/'))
                                    )
                                ),
                                array('no'),
                                array('no' => -1));
        $arr_save = array();
        if(isset($arr_contact['company_id'])) {
            $arr_save = $this->arr_associated_data('company_name',$arr_contact['company'], $arr_contact['company_id'],$contact_id);
        }
        $arr_save['no'] = 1;
        if (isset($arr_tmp['no'])) {
            $arr_save['no'] = $arr_tmp['no'] + 1;
        }
        $arr_save['contacts_default_key'] = 0;
        $arr_save['contact_id'] = $arr_contact['_id'];
        $arr_save['contact_name'] = $arr_contact['first_name'].' - '.$arr_contact['last_name'];
        $arr_save['contacts'][] = array(
            "contact_name" =>$this->Contact->user_name(),
            "contact_id" => $this->Contact->user_id(),
            "default" => true,
            "deleted" => false
        );
        if (isset($work_start_sec) && $work_start_sec > 0) {
            $arr_save['work_end'] = $arr_save['work_start'] = new MongoDate($work_start_sec);
        } else {
            $arr_save['work_end'] = $arr_save['work_start'] = new MongoDate(strtotime(date('Y-m-d H:00:00')) + 3600);
        }

        $this->Job->arr_default_before_save = $arr_save;
        if ($this->Job->add()) {
            $this->redirect('/jobs/entry/' . $this->Job->mongo_id_after_save);
        }
        $this->redirect('/jobs/entry/');
    }

    function task_add($contact_id) {

        //$this->selectModel('Company');
        //$arr_contact = $this->Company->select_one(array('_id' => new MongoId($contact_id)));

        $this->selectModel('Contact');
        $arr_contact = $this->Contact->select_one(array('_id' => new MongoId($contact_id))); // oki

        $this->selectModel('Task');
        $arr_tmp = $this->Task->select_one(array(), array(), array('no' => -1));
        $arr_save = array();
        $arr_save['no'] = 1;
        if (isset($arr_tmp['no'])) {
            $arr_save['no'] = $arr_tmp['no'] + 1;
        }
        $arr_save['status_id'] = 0;
        $arr_save['name'] = '';
        $arr_save['contact_name'] = '';
        $arr_save['our_rep'] = $_SESSION['arr_user']['contact_name'];
        $arr_save['our_rep_id'] = new MongoId($_SESSION['arr_user']['contact_id']);
        $arr_save['work_start'] = new MongoDate(strtotime(date('Y-m-d' . ' 08:00:00')));
        $arr_save['work_end'] = new MongoDate(strtotime(date('Y-m-d') . ' 09:00:00'));
        $arr_save['contact_no'] = $arr_contact['no'];
        $arr_save['contact_id'] = $arr_contact['_id'];   // oki


        if(isset( $arr_contact['company']) && isset($arr_contact['company_id']) ) {

            $arr_save['company_id'] = $arr_contact['company_id'];

            $arr_save['contact_name'] = $arr_contact['company'];
            $arr_save['company_name'] = $arr_contact['company'];
        }


        //pr($arr_contact);die();
        // pr($arr_save);die();

        $this->Task->arr_default_before_save = $arr_save;
        if ($this->Task->add()) {
            $this->redirect('/tasks/entry/' . $this->Task->mongo_id_after_save);
        }
        $this->redirect('/companies/entry/' . $contact_id);
    }
    public function task() {
        $subdatas['task'] = array();
        $iditem = $this->get_id();
        $this->selectModel('Task');
        $arr_task = $this->Task->select_all(array(
            'arr_where' => array('contact_id' => new MongoId($iditem)),
            'arr_order' => array('_id' => -1)
        ));
        $arr = array();
        foreach ($arr_task as $key => $value) {
            $arr[$key] = $value;
        }
        $subdatas['task'] =  $arr;
        $this->set('subdatas', $subdatas);
    }

    public function personal() {
        $contact_id = $this->get_id();

        $subdatas['emergency'] = $this->get_option_data('emergency');

        $arr_options_custom = $this->set_select_data_list('relationship', 'personal');
        $this->set('arr_options_custom', $arr_options_custom);

        $arr_exp_profile = array();
        $arr_exp_profile = $this->Contact->select_one(array('_id'=>new MongoID($contact_id)),array('status','ssn_no','start_date','finish_date','weeks_worked','date_birth'));
        $subdatas['profile'] = $arr_exp_profile;


        $this->set('subdatas', $subdatas);
        //hoang vu 3/4/2014
    }

    public function rate() {
        $contact_id = $this->get_id();

        $arr_options_custom = $this->set_select_data_list('relationship', 'rate');
        $this->set('arr_options_custom', $arr_options_custom);

        $arr_exp_profile = array();
        $arr_exp_profile = $this->Contact->select_one(array('_id'=>new MongoID($contact_id)),array('employee_type','employment_type','work_type','paid_by','overtime_starts_at','overtime_ends_at','commission'));
        $subdatas['profile'] = $arr_exp_profile;

        $subdatas['review'] = $this->get_option_data('review');


        $this->set('subdatas', $subdatas);
        //hoang vu 3/4/2014
    }

    public function expense() {
        $contact_id = $this->get_id();

        $arr_options_custom = $this->set_select_data_list('relationship', 'expense');
        $this->set('arr_options_custom', $arr_options_custom);


        $subdatas['expenses'] = $this->get_option_data('expenses');

        $this->set('subdatas', $subdatas);
        //hoang vu 3/4/2014
    }

/*    public function leave() {
        $contact_id = $this->get_id();

        $arr_options_custom = $this->set_select_data_list('relationship', 'leave');
        $this->set('arr_options_custom', $arr_options_custom);  // show danh sach Select
        //pr($arr_options_custom);die;


        $subdatas['leave'] = $this->get_option_data('leave');
        $subdatas['accummulated'] = $this->get_option_data('accummulated');

        $this->set('subdatas', $subdatas);
        //hoang vu 3/4/2014
    }*/

    function leave($contact_id, $return = false) {
        $arr_leave = $arr_accummulated = array();
        if( !isset($this->data['Contact']) ){ // kiểm tra xem đã có load ở entry chưa
            $this->selectModel('Contact');
            $arr_tmp = $this->Contact->select_one(array('_id' => new MongoId($contact_id)), array('leave', 'accummulated'));

            if( isset($arr_tmp['leave']) )
                $arr_leave = $arr_tmp['leave'];
            if( isset($arr_tmp['accummulated']) )
                $arr_accummulated = $arr_tmp['accummulated'];
        }else{
            if( isset($this->data['Contact']['leave']) )
                $arr_leave = $this->data['Contact']['leave'];
            if( isset($this->data['Contact']['accummulated']) )
                $arr_accummulated = $this->data['Contact']['accummulated'];

        }
        $this->selectModel('Setting');
        $arr_leave_purpose = $this->Setting->select_option(array('setting_value' => 'employee_leave_purpose'), array('option'));
        $arr_leave_status = $this->Setting->select_option(array('setting_value' => 'employee_leave_status'), array('option'));

        if($return){
            return array(
                         'arr_leave' => $arr_leave,
                         'arr_accummulated' =>$arr_accummulated,
                         'arr_leave_purpose' => $arr_leave_purpose,
                         'arr_leave_status' => $arr_leave_status
                         );
        }
        $this->set('arr_leave', $arr_leave);
        $this->set('arr_accummulated', $arr_accummulated);

        $this->set('arr_leave_purpose', $arr_leave_purpose);
        $this->set('arr_leave_status', $arr_leave_status);
        $this->set('contact_id', $contact_id);
    }

    function leave_total($contact_id) {
        $this->selectModel('Contact');
        $arr_tmp = $this->Contact->select_one(array('_id' => new MongoId($contact_id)), array('leave', 'accummulated'));

        $arr_total = array('total_left' => 0, 'total_right' => 0, 'days_leave_due_to' => 0 );
        if( isset($arr_tmp['leave']) ){
            foreach ($arr_tmp['leave'] as $key => $value) {
                if( $value['dontdeduct'] )continue;
                $arr_total['total_left'] += $value['used'];
            }
        }

        if( isset($arr_tmp['accummulated']) ){
            foreach ($arr_tmp['accummulated'] as $key => $value) {
                $arr_total['total_right'] += $value['total'];
            }
        }

        $arr_total['days_leave_due_to'] = $arr_total['total_right'] - $arr_total['total_left'];
        echo json_encode($arr_total);die;
    }

    function leave_auto_save($contact_id = '', $key){
        $this->selectModel('Contact');
        $arr_save = $this->Contact->select_one(array('_id' => new MongoId($contact_id)));

        $post = $this->data['Leave'];
        $arr_save['leave'][$key]['purpose'] = $post['purpose'];
        $arr_save['leave'][$key]['purpose_id'] = $post['purpose_id'];
        $arr_save['leave'][$key]['used'] = $post['used'];
        $arr_save['leave'][$key]['date_from'] = new MongoDate($this->Common->strtotime($post['date_from'] . ' 00:00:00'));
        $arr_save['leave'][$key]['date_to'] = new MongoDate($this->Common->strtotime($post['date_to'] . ' 00:00:00'));
        $arr_save['leave'][$key]['status'] = $post['status'];
        $arr_save['leave'][$key]['status_id'] = $post['status_id'];
        $arr_save['leave'][$key]['details'] = $post['details'];
        $arr_save['leave'][$key]['dontdeduct'] = (int)$post['dontdeduct'];

        if ($this->Contact->save($arr_save)) {
            echo 'ok';
        } else {
            echo 'Error: ' . $this->Contact->arr_errors_save[1];
        }
        die;
    }

    function leave_add($contact_id) {
        $this->selectModel('Contact');
        $arr_contact = $this->Contact->select_one(array('_id' => new MongoId($contact_id)));
        $arr_save = $arr_contact;
        $arr_save['leave'][] = array(
            'purpose' => '',
            'purpose_id' => '',
            'used' => '',
            'date_from' => new MongoDate(),
            'date_to' => new MongoDate(),
            'status' => 'Scheduled',
            'status_id' => 'Scheduled',
            'details' => '',
            'dontdeduct' => '',
            'approved' => 0,
            'comment' => '',
            'viewed' => false,
            'deleted' => false
        );
        if ($this->Contact->save($arr_save)) {
            $this->leave($contact_id);
            echo $this->render('leave');
            die;
        }
        //$this->redirect('/contacts/entry/' . $contact_id);
    }

    function leave_delete($key, $id) {
        $this->selectModel('Contact');
        $arr_save = $this->Contact->select_one(array('_id' => new MongoId($id)));
        $arr_save['_id'] = $id;
        $arr_save['leave'][$key]['deleted'] = true;
        if ($this->Contact->save($arr_save)) {
            echo 'ok';
        } else {
            echo 'Error: ' . $this->Contact->arr_errors_save[1];
        }
        die;
    }

    function leave_accummulated_auto_save($contact_id = '', $key){
        $this->selectModel('Contact');
        $arr_save = $this->Contact->select_one(array('_id' => new MongoId($contact_id)));

        $post = $this->data['Accummulated'];
        $arr_save['accummulated'][$key]['per_month'] = $post['per_month'];
        $arr_save['accummulated'][$key]['start'] = new MongoDate($this->Common->strtotime($post['start'] . ' 00:00:00'));
        $arr_save['accummulated'][$key]['end'] = new MongoDate($this->Common->strtotime($post['end'] . ' 00:00:00'));
        $arr_save['accummulated'][$key]['total'] = $post['total'];

        if ($this->Contact->save($arr_save)) {
            echo 'ok';
        } else {
            echo 'Error: ' . $this->Contact->arr_errors_save[1];
        }
        die;
    }

    function leave_accummulated_add($contact_id) {
        $this->selectModel('Contact');
        $arr_contact = $this->Contact->select_one(array('_id' => new MongoId($contact_id)));
        $arr_save = $arr_contact;
        $arr_save['accummulated'][] = array(
            'per_month' => '',
            'start' => new MongoDate(),
            'end' => new MongoDate(),
            'total' => '',
            'deleted' => false
        );
        if ($this->Contact->save($arr_save)) {
            $this->leave($contact_id);
            echo $this->render('leave'); die;
        }
        $this->redirect('/contacts/entry/' . $contact_id);
    }

    function Leave_accummulated_delete($key, $id) {
        $this->selectModel('Contact');
        $arr_save = $this->Contact->select_one(array('_id' => new MongoId($id)));
        $arr_save['_id'] = $id;
        $arr_save['accummulated'][$key]['deleted'] = true;
        if ($this->Contact->save($arr_save)) {
            echo 'ok';
        } else {
            echo 'Error: ' . $this->Contact->arr_errors_save[1];
        }
        die;
    }

    function leave_view($contact_id,$key,$viewed = false){
        $contact_id = $this->get_id();
        $this->selectModel('Contact');
        $arr_contact = $this->Contact->select_one(array('_id' => new MongoId($contact_id)),array('leave','full_name'));
        if(isset($arr_contact['leave']) && !empty($arr_contact['leave'])){
            /*foreach($arr_contact['leave'] as $k => $v_leave){
                if($v_leave['deleted'] == false){
                    $leave = $v_leave;
                }
            }*/
        }
        $return_link = URL.'/contacts/entry/'.$key;
        if(isset($_GET['redirect']))
             $return_link = URL.'/'.$_GET['redirect'];
        if($viewed){
            $arr_contact['leave'][$key]['viewed'] = true;
            $this->opm->save($arr_contact);
        }
        $this->set('key',$key);
        $this->set('contact_id',$contact_id);
        $this->set('leave',$arr_contact['leave'][$key]);
        $this->set('full_name', $arr_contact['full_name']);
        $this->set('return_mod', 1);
        $this->set('return_link',  $return_link);
        $this->set('return_title', 'Leave ');
        $this->selectModel('Setting');
        $this->set('arr_leave_purpose', $this->Setting->select_option(array('setting_value' => 'employee_leave_purpose'), array('option')));
        $this->set('arr_leave_status', $this->Setting->select_option(array('setting_value' => 'employee_leave_status'), array('option')));
    }

    function leave_save_details($contact_id, $key){
        if( !empty( $_POST ) ){
            $this->selectModel('Contact');
            $arr_save = $this->Contact->select_one(array('_id' => new MongoId($contact_id)));
            $arr_save['leave'][$key]['details'] = $_POST['details'];
            $this->selectModel('Contact');
            if ($this->Contact->save($arr_save)) {
                echo 'ok';
            } else {
                echo 'Error: ' . $this->Contact->arr_errors_save[1];
            }
        }
        die;
    }

    function leave_save_approved($contact_id, $key){
        if(!empty($_POST)){
            $field = $_POST['fieldname'];
            if(in_array($field, array('comment','approved'))){
                $check = $this->check_permission('contacts_@_workings_holidays_approve_@_edit');
                if(!$check){
                    echo 'Dont permission';die;
                }
            }
            $value = $_POST['values'];
            $this->selectModel('Contact');
            $arr_contact = $this->Contact->select_one(array('_id' => new MongoId($contact_id)));
            $arr_save = array();
            $arr_save['_id']=$arr_contact['_id'];
            $arr_save['leave'] = $arr_contact['leave'];
            $arr_save['leave'][$key][$field] = $value;
            if($field=='approved'){
                if($value=='1')
                    $arr_save['leave'][$key]['status_id'] = $arr_save['leave'][$key]['status'] = 'Approved';
                else
                    $arr_save['leave'][$key]['status_id'] = $arr_save['leave'][$key]['status'] = 'Scheduled';
            }
            if($this->Contact->save($arr_save)){
                echo 'ok';
            }else{
                echo 'Error:' . $this->Contact->$arr_errors_save[1];
            }
        }
        die;
    }

    function get_email_template_leave($contact_id, $key){
        $this->selectModel('Contact');
        $arr_contact = $this->Contact->select_one(array('_id' =>  new MongoID($contact_id)),array('leave','full_name'));
        $content = $arr_contact['leave'][$key];
        $content['full_name'] = $arr_contact['full_name'];
        $this->selectModel('Emailtemplate');
        $email_template = $this->Emailtemplate->select_one(array('name'=>'Contact Leave'));
        $template = $email_template['template'];
        preg_match_all("!<span[^>]+>(.*?)</span>!", $template, $matches);
        $all_span = $matches[0];
        foreach($all_span as $span){
            //Lay noi dung trong rel
            preg_match_all("/<span [^>]+ rel=\"{{(.+?)}}\" [^>]+>[^>]+<\/span>/",$span,$content_matches);
            foreach($content_matches[1] as $val){
                $val = strtolower($val);
                if(strpos($val, 'date_')!==false && isset($content[$val]))
                   $content[$val] = date('M d, Y',$content[$val]->sec);
                $template = str_replace($span, (isset($content[$val]) ? $content[$val] : ''), $template);
            }
        }
        return $template;
    }

    function send_email_leave($contact_id, $key){
        $content = $this->get_email_template_leave($contact_id,$key);
        $model_name = $this->modelName;
        $this->selectModel($model_name);
        $data = $this->$model_name->select_one(array('_id'=> new MongoId($this->get_id())));
        $arr_save = array();

        $this->selectModel('Communication');
            $arr_save = array(
                'deleted'           => false,
                'code'              => $this->Communication->get_auto_code('code'),
                'comms_type'        => 'Email',
                'comms_date'        => new MongoDate(),
                'comms_status'      => 'Draft',
                'sign_off'          => 0,
                'include_signature' => 0,
                'email_cc'          => '',
                'email_bcc'         => '',
                'identity'          => '',
                'internal_notes'    => '',
                'company_id'        => (isset($data['company_id'])&&$data['company_id']!='' ? new MongoId($data['company_id']) : ''),
                'company_name'      => (isset($data['company']) ? $data['company'] : ''),
                'module'            => $model_name,
                'module_id'         => new MongoId($data['_id']),
                'content'           => $content,
                'contact_id'        => (isset($data['contact_id'])&&$data['contact_id']!='' ? new MongoId($data['contact_id']) : ''),
                'contact_name'      => (isset($data['contact_name']) ?$data['contact_name'] : ''),
                'last_name'         => '',
                'position'          => (isset($data['position'])?$data['position']:''),
                'salutation'        => '',
                'name'              => '',
                'toother'           => '',
                'email'             => '',
                'phone'             => (isset($data['phone']) ? $data['phone'] : ''),
                'fax'               => (isset($data['fax']) ? $data['fax'] : ''),
                'job_number'        => (isset($data['job_number']) ? $data['job_number'] : ''),
                'job_name'          => (isset($data['job_name']) ? $data['job_name'] : ''),
                'job_id'            => (isset($data['job_id'])&&$data['job_id']!='' ? new MongoId($data['job_id']) : ''),
                'contact_from'      => (isset($data['full_name'])?$data['full_name']:''),
                );
            $this->Communication->save($arr_save);
            $comms_code = $arr_save['code'];
            $comms_id = $this->Communication->mongo_id_after_save;
            $this->redirect('/communications/entry/'.$comms_id);
    }

/*    public function working() {
        $contact_id = $this->get_id();

        $subdatas['working_hour'] = $this->get_option_data('working_hour');

        $subdatas['working_hour']['0']['day'] = 'Monday';
        $subdatas['working_hour']['1']['day'] = 'Tuesday';
        $subdatas['working_hour']['2']['day'] = 'Wednesday';
        $subdatas['working_hour']['3']['day'] = 'Thursday';
        $subdatas['working_hour']['4']['day'] = 'Friday';
        $subdatas['working_hour']['5']['day'] = 'Saturday';
        $subdatas['working_hour']['6']['day'] = 'Sunday';

         $subdatas['working_hour']['0']['time1'] = '';




        $this->set('subdatas', $subdatas);
        //hoang vu 3/4/2014
    }*/


    public function product() {
        $subdatas['pricing_category'] = array();
        $subdatas['product'] = array();
        $subdatas['units'] = array();
        $contact_id = $this->get_id();
        $this->selectModel('Contact');
        $arr_contact = $this->Contact->select_one(array('_id'=>new MongoId($contact_id)));
        if (isset($arr_contact['pricing'])) {
              $arr_pro_contact = $arr_contact['pricing'];
        } else {
             $arr_pro_contact = array();
        }
        foreach ($arr_pro_contact as $key => $value) {
            foreach ($value['price_break'] as $key2 => $value2) {
                 $arr_pro_contact[$key]['range'] = $value2['range_from'].' - '.$value2['range_to'];
                 $arr_pro_contact[$key]['unit_price'] = $value2['unit_price'];
             }
        }
        //pr($arr_contact['pricing']);die;
        //pr($arr_pro_contact);die;

        $arr = array();
        $subdatas['product'] = $arr_pro_contact ;
        $this->set('subdatas', $subdatas);
    }
    function products_pricing($contact_id, $key = 0, $product_id=''){
        $this->selectModel('Contact');
        $arr_save = $this->Contact->select_one(array('_id'=> new MongoId($contact_id)));
        //pr($arr_save);die;

        if($product_id !=''){
            $this->selectModel('Product');
            $arr_product = $this->Product->select_one(array('_id' => new MongoId($product_id)), array('_id','name','code'));
            $arr_product['product_id'] = $arr_product['_id'];
            unset($arr_product['_id']);
            $arr_product['notes']='';
            $arr_product['deleted'] = false;
            $arr_product['price_break'] = array();

            $check_not_exist = true;
            if(isset($arr_save['pricing'])){
                foreach($arr_save['pricing'] as $pricing_key => $value){
                    if((string)$value['product_id']==$product_id){
                        $check_not_exist = false;
                        $key = $pricing_key;
                        break;
                    }
                }
            }
            //neu chua ton tai thi add vao contact
            if($check_not_exist){
                $arr_save['pricing'][] = $arr_product;
                if(!$this->Contact->save($arr_save)){
                    echo 'Error: ' .$this->Contact->arr_errors_save[1];die;
                }
            }else{
                $key = count($arr_save['pricing']) - 1;
                $arr_product = $arr_save['pricing'][$key];
            }
            $key = count($arr_save['pricing']) - 1;
        }
        else{
            $arr_product = $arr_save['pricing'][$key];
        }
        //pr($arr_product);die;
        $this->set('company_pricing', $arr_save);
        $this->set('arr_product', $arr_product);
        $this->set('contact_id', $contact_id);
        $this->set('key', $key);

        $this->set('return_mod', 1);
        $this->set('return_link', URL.'/contacts/entry/'.$contact_id);
        $this->set('return_title', 'Pricing: ');
    }
    function products_price_break_add($contact_id, $key){
        $this->set('contact_id', $contact_id);
        $this->set('key', $key);

        $this->selectModel('Contact');
        $arr_save = $this->Contact->select_one(array('_id'=> new MongoId($contact_id)));
        $arr_save['pricing'][$key]['price_break'][] = array(
            'deleted' => false,
            'range_from' => '',
            'range_to' => '',
            'unit_price' =>''
        );
        if($this->Contact->save($arr_save)){
        $this->set('arr_product', $arr_save['pricing'][$key]);
            $this->render('products_price_break');
        } else {
            echo 'Error: ' . $this->Company->arr_errors_save[1]; die;
        }
    }
    function product_pricebreak_save($contact_id, $key, $price_break_key){
        if( !empty( $_POST ) ){
            $this->selectModel('Contact');
            $arr_save = $this->Contact->select_one(array('_id' => new MongoId($contact_id)));

            $field = str_replace(array('data[', ']'), '', $_POST['field']);

            $ext = '';
            $value = $_POST['value'];

            if( $field == 'unit_price' ){
                if( substr($value, -3, 1) == '.' ){
                    $ext = substr($value, -3);
                    $value = substr($value, 0, -3);
                }
                $value = (float)(str_replace(array(',', '.'), '', $value).$ext);
            }

            $arr_save['pricing'][$key]['price_break'][$price_break_key][$field] = $value;
            if ($this->Contact->save($arr_save)) {
                echo 'ok';
            } else {
                echo 'Error: ' . $this->Contact->arr_errors_save[1];
            }
        }
        die;
    }
    function products_pricebreak_delete($contact_id, $key, $price_break_key){
        $this->selectModel('Contact');
        $arr_save = $this->Contact->select_one(array('_id' => new MongoId($contact_id)));
        $arr_save['pricing'][$key]['price_break'][$price_break_key]['deleted'] = true;
        if ($this->Contact->save($arr_save)) {
            echo 'ok';
        } else {
            echo 'Error: ' . $this->Contact->arr_errors_save[1];
        }
        die;
    }
    function products_save_notes($contact_id, $key){
        if( !empty( $_POST ) ){
            $this->selectModel('Contact');
            $arr_save = $this->Contact->select_one(array('_id' => new MongoId($contact_id)));
            $arr_save['pricing'][$key]['notes'] = $_POST['notes'];
            if ($this->Contact->save($arr_save)) {
                echo 'ok';
            } else {
                echo 'Error: ' . $this->Contact->arr_errors_save[1];
            }
        }
        die;
    }


    public function quote() {
        $subdatas['quote'] = array();
        $iditem = $this->get_id();
        $this->selectModel('Quotation');
        $arr_quote = $this->Quotation->select_all(array(
            'arr_where' => array('contact_id' => new MongoId($iditem)),
            'arr_order' => array('_id' => -1),
            'arr_field' => array('sum_sub_total','sum_amount','sum_tax','quotation_type','quotation_status','our_rep','code','quotation_date','payment_due_date','taxval','tax','name')
        ));


        // Hoang Vu 3/4/2013
        $arr_quote = iterator_to_array($arr_quote);
        foreach($arr_quote as $key=>$quote){
            $minimum = $this->get_minimum_order('Quotation',$quote['_id']);
            if(isset($sum_sub_total) && $quote['sum_sub_total']<$minimum){
                $more_sub_total = $minimum - (float)$quote['sum_sub_total'];
                $sub_total = $more_sub_total;
                $tax = $sub_total*(float)$quote['taxval']/100;
                $amount = $sub_total+$tax;
                $arr_quote[$key]['sum_sub_total'] += $sub_total;
                $arr_quote[$key]['sum_amount'] += $amount;
                $arr_quote[$key]['sum_tax'] = $arr_quote[$key]['sum_amount']-$arr_quote[$key]['sum_sub_total'];
            }
        }
        $this->selectModel('Tax');
        $arr_tax = $this->Tax->tax_select_list();
        $this->set('arr_tax',$arr_tax);
        $this->set('arr_quotation', $arr_quote);
       /* foreach($arr_quote as $key=>$quote){
            if ($arr_quote[$key]['sum_tax'] == 0)
                $arr_quote[$key]['sum_tax'] = $arr_quote[$key]['sum_tax'].'% (No tax)';
        }*/
        //pr($arr_quote);die();
        $this->set('contact_id', $iditem);
        // Hoang Vu 3/4/2013
        $subdatas['quote'] = $arr_quote;
        $this->set('subdatas', $subdatas);
    }
    function quotes_add($contact_id) {
        $this->selectModel('Quotation');
        $arr_tmp=array();
        $arr_tmp = $this->Quotation->select_one(array(), array(), array('code' => -1));
        $arr_save = array();
        $arr_save['code'] = 1;
        if (isset($arr_tmp['code'])) {
            $arr_save['code'] = $arr_tmp['code'] + 1;
        }

        $this->selectModel('contact');
        $arr_contact = $this->Contact->select_one(array('_id' => new MongoId($contact_id)));
        //pr($arr_contact);die();

        $arr_save['contact_id'] = $arr_contact['_id'];
        $arr_save['company_name'] = isset($arr_contact['company'])?$arr_contact['company']:'';
        $arr_save['company_id'] = isset($arr_contact['company_id'])?$arr_contact['company_id']:'';
        $arr_save['email'] = isset($arr_contact['email'])?$arr_contact['email']:'';
        $arr_save['phone'] = isset($arr_contact['company_phone'])?$arr_contact['company_phone']:'';
        $arr_invoice_addrress['invoice_name'] = '';
        $arr_invoice_addrress['deleted'] = false;

        $arr_save['invoice_address'][0] = (object)$arr_invoice_addrress;
        $arr_save['our_csr'] = $_SESSION['arr_user']['contact_name'];
        $arr_save['our_csr_id'] = new MongoId($_SESSION['arr_user']['contact_id']);

        if(isset($arr_contact['our_rep_id'])){
            $arr_save['our_rep'] = isset($arr_contact['our_rep'])?$arr_contact['our_rep']:'';
            $arr_save['our_rep_id'] = new MongoId($arr_contact['our_rep_id']);
        }else
        {
            $arr_save['our_rep'] = $_SESSION['arr_user']['contact_name'];
            $arr_save['our_rep_id'] = new MongoId($_SESSION['arr_user']['contact_id']);
        }
        $arr_save['quotation_date'] = new MongoDate(strtotime(date('Y-m-d')));
        $arr_save['quotation_type'] = 'Quotation';
        $arr_save['tax'] = 'AB';
        //pr($arr_save);die();
        $this->Quotation->arr_default_before_save = $arr_save;
        if ($this->Quotation->add()) {
            $this->redirect('/quotations/entry/'. $this->Quotation->mongo_id_after_save);
        }
        $this->redirect('/quotations/entry');
    }


    function orders_add($contact_id) {
        $this->selectModel('Salesorder');
        $arr_tmp=array();
        $arr_tmp = $this->Salesorder->select_one(array(), array(), array('code' => -1));
        $arr_save = array();
        $arr_save['code'] = 1;
        if (isset($arr_tmp['code'])) {
            $arr_save['code'] = $arr_tmp['code'] + 1;
        }

        $this->selectModel('contact');
        $arr_contact = $this->Contact->select_one(array('_id' => new MongoId($contact_id)));
        //pr($arr_contact);die();

        $arr_save['contact_id'] = $arr_contact['_id'];
       /* $arr_save['our_csr'] = $_SESSION['arr_user']['contact_name'];
        $arr_save['our_csr_id'] = new MongoId($_SESSION['arr_user']['contact_id']);*/

        if(isset($arr_contact['assign_to'])){
            $arr_save['our_rep'] = isset($arr_contact['assign_to'])?$arr_contact['assign_to']:'';
            $arr_save['our_rep_id'] = new MongoId($arr_contact['our_rep_id']);
        }else
        {
            $arr_save['our_rep'] = $_SESSION['arr_user']['contact_name'];
            $arr_save['our_rep_id'] = new MongoId($_SESSION['arr_user']['contact_id']);
        }
        $arr_save['salesorder_date'] = new MongoDate(strtotime(date('Y-m-d')));
        //$arr_save['payment_due_date'] = new MongoDate(strtotime(date('Y-m-d')));
        //pr($arr_save);die();
        $this->Salesorder->arr_default_before_save = $arr_save;
        if ($this->Salesorder->add()) {
            $this->redirect('/salesorders/entry/'. $this->Salesorder->mongo_id_after_save);
        }
        $this->redirect('/salesorders/entry');
    }
    public function order() {
        $subdatas['order'] = array();
        $contact_id = $this->get_id();
        $this->selectModel('Salesorder');
        $arr_orders = $this->Salesorder->select_all(array(
            'arr_where' => array('contact_id' => new MongoId($contact_id)),
            'arr_order' => array('_id' => -1)
        ));
        $arr = array();
        foreach ($arr_orders as $key => $value) {
            $arr[$key] = $value;
        }

        //hoang vu 3/4/2014
        $arr_salesorder = $this->Salesorder->select_all(array(
            'arr_where' => array('contact_id' => new MongoId($contact_id)),
            'arr_field' => array('sum_sub_total','sum_amount','sum_tax','status','our_rep','code','salesorder_date','payment_due_date','taxval','tax','name','other_comment')
        ));
        $arr_salesorder = iterator_to_array($arr_salesorder);
        foreach($arr_salesorder as $key=>$order){
            $minimum = $this->get_minimum_order('Salesorder',$order['_id']);
            if($order['sum_sub_total']<$minimum){
                $more_sub_total = $minimum - (float)$order['sum_sub_total'];
                $sub_total = $more_sub_total;
                $tax = $sub_total*(float)$order['taxval']/100;
                $amount = $sub_total+$tax;
                $arr_salesorder[$key]['sum_sub_total'] += $sub_total;
                $arr_salesorder[$key]['sum_amount'] += $amount;
                $arr_salesorder[$key]['sum_tax'] = $arr_salesorder[$key]['sum_amount']-$arr_salesorder[$key]['sum_sub_total'];
            }
        }
        $this->set('arr_salesorder', $arr_salesorder);
        //pr($arr_salesorder);die();
        //hoang vu 3/4/2014


        $subdatas['order'] = $arr;
        $this->set('subdatas', $subdatas);
    }



    public function shipping() {
        $subdatas['ship'] = array();
        $iditem = $this->get_id();
        $this->selectModel('Shipping');
        $arr_shipping = $this->Shipping->select_all(array(
            'arr_where' => array('contact_id' => new MongoId($iditem)),
            'arr_order' => array('_id' => -1)
        ));
        $arr = array();
        foreach ($arr_shipping as $key => $value) {
            $arr[$key] = $value;
        }

        $subdatas['ship'] = $arr;
        $this->set('subdatas', $subdatas);
    }
    function shippings_add($contact_id) {
        $this->selectModel('Shipping');
        $arr_tmp=array();
        $arr_tmp = $this->Shipping->select_one(array(), array(), array('code' => -1));
        $arr_save = array();
        $arr_save['code'] = 1;
        if (isset($arr_tmp['code'])) {
            $arr_save['code'] = $arr_tmp['code'] + 1;
        }

        $this->selectModel('contact');
        $arr_contact = $this->Contact->select_one(array('_id' => new MongoId($contact_id)));
        $arr_save['contact_id'] = $arr_contact['_id'];


        if(isset($arr_contact['assign_to'])){
            $arr_save['our_rep'] = isset($arr_contact['assign_to'])?$arr_contact['assign_to']:'';
            $arr_save['our_rep_id'] = new MongoId($arr_contact['our_rep_id']);
        }else
        {
            $arr_save['our_rep'] = $_SESSION['arr_user']['contact_name'];
            $arr_save['our_rep_id'] = new MongoId($_SESSION['arr_user']['contact_id']);
        }
        $arr_save['salesorder_date'] = new MongoDate(strtotime(date('Y-m-d')));

        $this->Shipping->arr_default_before_save = $arr_save;
        if ($this->Shipping->add()) {
            $this->redirect('/shippings/entry/'. $this->Shipping->mongo_id_after_save);
        }
        $this->redirect('/shippings/entry');
    }



    public function invoice() {
        $subdatas['overall'] = array();
        $subdatas['sale_invoice'] = array();
        $contact_id = $this->get_id();
        $this->selectModel('Salesinvoice');


        //hoang vu 3/4/2014
        $arr_invoices = $this->Salesinvoice->select_all(array(
            'arr_where' => array('contact_id' => new MongoId($contact_id)),
            'arr_field' => array('sum_sub_total','sum_amount','sum_tax','invoice_type','code','invoice_date','invoice_status','our_rep','other_comment','taxval')
        ));
        $arr_invoices = iterator_to_array($arr_invoices);
        foreach($arr_invoices as $key=>$invoice){
            $minimum = $this->get_minimum_order('Salesinvoice',$invoice['_id']);
            if($invoice['sum_sub_total']<$minimum){
                $more_sub_total = $minimum - (float)$invoice['sum_sub_total'];
                $sub_total = $more_sub_total;
                $tax = $sub_total*(float)$invoice['taxval']/100;
                $amount = $sub_total+$tax;
                $arr_invoices[$key]['sum_sub_total'] += $sub_total;
                $arr_invoices[$key]['sum_amount'] += $amount;
                $arr_invoices[$key]['sum_tax'] = $arr_invoices[$key]['sum_amount']-$arr_invoices[$key]['sum_sub_total'];
            }
        }
        //pr($arr_invoices);die();
        $this->set('arr_invoices', $arr_invoices);
        $subdatas['sale_invoice'] = $arr_invoices;
        $this->set('subdatas', $subdatas);
        //hoang vu 3/4/2014
    }
    function invoices_add($contact_id) {
        $this->selectModel('Salesinvoice');
        $arr_tmp=array();
        $arr_tmp = $this->Salesinvoice->select_one(array(), array(), array('code' => -1));
        $arr_save = array();
        $arr_save['code'] = 1;
        if (isset($arr_tmp['code'])) {
            $arr_save['code'] = $this->Salesinvoice->get_auto_code('code');
        }

        $this->selectModel('contact');
        $arr_contact = $this->Contact->select_one(array('_id' => new MongoId($contact_id)));
        $arr_save['contact_id'] = $arr_contact['_id'];


        if(isset($arr_contact['assign_to'])){
            $arr_save['our_rep'] = isset($arr_contact['assign_to'])?$arr_contact['assign_to']:'';
            $arr_save['our_rep_id'] = new MongoId($arr_contact['our_rep_id']);
        }else
        {
            $arr_save['our_rep'] = $_SESSION['arr_user']['contact_name'];
            $arr_save['our_rep_id'] = new MongoId($_SESSION['arr_user']['contact_id']);
        }
        $arr_save['salesorder_date'] = new MongoDate(strtotime(date('Y-m-d')));

        $this->Salesinvoice->arr_default_before_save = $arr_save;
        if ($this->Salesinvoice->add()) {
            $this->redirect('/salesinvoices/entry/'. $this->Salesinvoice->mongo_id_after_save);
        }
        $this->redirect('/salesinvoices/entry');
    }


    function other(){
        $contact_id = $this->get_id();
        $this->selectModel('Contact');
        $arr_contact = array();
        $arr_contact = $this->Contact->select_one(array('_id'=>new MongoID($contact_id)),array('other','off_report_working_hour','anvy_support'));
        $this->set_select_data_list('relationship','other');
        $subdatas = array();

        if ( isset($arr_contact['other']) )
            $subdatas['other_detail'] = $arr_contact['other'];
        else
            $subdatas['other_detail'] = array();

        $subdatas['2'] = array();
        $subdatas['3'] = array(); //Profile
        $subdatas['3']['off_report_working_hour'] = isset($arr_contact['off_report_working_hour'])?$arr_contact['off_report_working_hour']:0;
        $subdatas['3']['anvy_support'] = isset($arr_contact['anvy_support'])?$arr_contact['anvy_support']:0;

        $this->set('subdatas', $subdatas);
    }

    function other_add($contact_id) {
        $this->selectModel('Contact');
        $this->Contact->collection->update(
                array('_id' => new MongoId($contact_id)), array(
                    '$push' => array(
                        'other' => array(
                            'heading' => '',
                            'details' => '',
                            'deleted' => false
                        )
                    )
                )
        );
        $this->other($contact_id);
        $this->render('other');
    }



	// Popup form orther module
    public function popup($key = '') {

        $this->set('key', $key);

        if( $key == '_assets' ){
            $this->selectModel('Equipment');
            $arr_equipment = $this->Equipment->select_all(array('arr_order' => array('name' => 1)));
            $this->set( 'arr_equipment', $arr_equipment );
        }

        $limit = 100; $skip = 0; $cond = array();
        $this->identity($cond);
        // Nếu là search GET
        if (!empty($_GET)) {

            $tmp = $this->data;

            if (isset($_GET['company_id'])) {
                $cond['company_id'] = new MongoId($_GET['company_id']);
            }
            if( isset($_GET['company_name']) ) {
                $tmp['Contact']['company'] = $_GET['company_name'];
            }

            if (isset($_GET['is_customer'])) {
                $cond['is_customer'] = 1;
                $tmp['Contact']['is_customer'] = 1;
            }

            if (isset($_GET['is_employee'])) {
                $cond['is_employee'] = 1;
                $tmp['Contact']['is_employee'] = 1;
            }

            if( isset($_GET['true_employee']) ) {
                $cond['is_employee'] = 1;
                $cond['off_report_working_hour'] = array('$exists' =>false);
                $tmp['Contact']['is_employee'] = 1;
            }

            $this->data = $tmp;
        }

        // Nếu là search theo phân trang
        $page_num = 1;
        if( isset($_POST['pagination']) && $_POST['pagination']['page-num'] > 0 ){

            // $limit = $_POST['pagination']['page-list'];
            $page_num = $_POST['pagination']['page-num'];
            $limit = $_POST['pagination']['page-list'];
            $skip = $limit*($page_num - 1);
        }
        $this->set('page_num', $page_num);
        $this->set('limit', $limit);

        $arr_order = array('first_name' => 1);
        if( isset($_POST['sort']) && strlen($_POST['sort']['field']) > 0 ){
            $sort_type = 1;
            if( $_POST['sort']['type'] == 'desc' ){
                $sort_type = -1;
            }
            $arr_order = array($_POST['sort']['field'] => $sort_type);

            $this->set('sort_field', $_POST['sort']['field']);
            $this->set('sort_type', ($sort_type === 1)?'asc':'desc');
            $this->set('sort_type_change', ($sort_type === 1)?'desc':'asc');
        }

        // search theo submit $_POST kèm điều kiện
        $cond['inactive'] = 0;
        if (!empty($this->data) && !empty($_POST)) {
            $arr_post = $this->data['Contact'];

            if (isset($arr_post['name']) && strlen($arr_post['name']) > 0) {
                $cond['full_name'] = new MongoRegex('/' . trim($arr_post['name']) . '/i');
            }

            if (strlen($arr_post['company']) > 0) {
                $arr_post['company'] = str_replace(array('(',')'), '.*',  $arr_post['company']);
                $cond['company'] = new MongoRegex('/' . $arr_post['company'] . '/i');
            }

            if( $arr_post['inactive'] )
                $cond['inactive'] = 1;

            if (is_numeric($arr_post['is_customer']) && $arr_post['is_customer'])
                $cond['is_customer'] = 1;

            if (is_numeric($arr_post['is_employee']) && $arr_post['is_employee'])
                $cond['is_employee'] = 1;
        }
        unset($_GET['_']);
        if(isset($_GET['manager_only']) || isset($_POST['manager_only'])){
            $arr_where = array();
            if(isset($cond['full_name']))
                $arr_where['full_name'] = $cond['full_name'];
            $arr_where['is_employee'] = 1;
            $arr_where['inactive'] = 0;
            $this->selectModel('Role');
            $roles = $this->Role->select_all(array(
                'arr_where' => array(
                    'name' => array(
                            '$in' => array(
                                new MongoRegex('/Administrator/i'),
                                new MongoRegex('/Manager/i'),
                            )
                        )
                    ),
                'arr_field' =>array('_id')
                ));
            $in_role = array();
            if($roles->count()){
                foreach($roles as $value)
                    $in_role[] = $value['_id'];
                $arr_where['roles.roles'] = array('$in' => $in_role);

            }
            $this->identity($arr_where);
            $arr_where['_id'] = array(
                    '$nin' => array(
                            new MongoId('528c36f667b96d314e000018'), //kei
                            new MongoId('5271f68167b96d1813000008'), //Joyce Wong
                            new MongoId('536c47bbe6f2b204527e8934'), //An Vu
                            new MongoId('5271f6d267b96d6013000011'), //Vu Nguyen
                            new MongoId('54261251b0e6dfd40b00006e'), //Hoang Vu
                            new MongoId('5498a832b0e6dfb40b00007c'), //Eddie Relucio
                        )
                );
            $arr_contact = $this->Contact->select_all(array(
                'arr_where' => $arr_where,
                'arr_order' => $arr_order,
                'arr_field' => array('first_name', 'last_name','full_name','company_id','company_name', 'is_employee', 'is_customer'),
                'limit' => $limit,
                'skip' => $skip
            ));
            $this->set('manager_only' , true);
        } else {
            $cache_key = md5(serialize($_GET));
            $no_cache = true;
            if(empty($_POST) && !isset($_GET['manager_only']) ){
                $arr_contact = Cache::read('popup_contact_'.$cache_key);
                if($arr_contact)
                    $no_cache = false;
            }
            $this->selectModel('Contact');
            if($no_cache){
                $arr_contact = $this->Contact->select_all(array(
                    'arr_where' => $cond,
                    'arr_order' => $arr_order,
                    'arr_field' => array('first_name', 'last_name','full_name','company_id','company_name', 'is_employee', 'is_customer'),
                    'limit' => $limit,
                    'skip' => $skip
                ));
                if(empty($_POST))
                    Cache::write('popup_contact_'.$cache_key,iterator_to_array($arr_contact));
            }
        }
        $this->set('arr_contact', $arr_contact);

        $total_page = $total_record = $total_current = 0;
        if( is_object($arr_contact) ){
            $total_current = $arr_contact->count(true);
            $total_record = $arr_contact->count();
            if( $total_record%$limit != 0 ){
                $total_page = floor($total_record/$limit) + 1;
            }else{
                $total_page = $total_record/$limit;
            }
        } else if(is_array($arr_contact)){
            $total_current = count($arr_contact);
            $total_record = $this->Contact->count($cond);
            if( $total_record%$limit != 0 ){
                $total_page = floor($total_record/$limit) + 1;
            }else{
                $total_page = $total_record/$limit;
            }
        }
        $this->set('total_current', $total_current);
        $this->set('total_page', $total_page);
        $this->set('total_record', $total_record);

        $this->selectModel('Company');
        $this->set('model_company', $this->Company);

        $this->layout = 'ajax';
    }

    public function save_option(array $arr_input = array()) {
        $ids = $this->get_id();
        if(isset($_POST['mongo_id']) && $_POST['mongo_id']!='')
            $ids = $_POST['mongo_id'];
        if(isset($_POST['fieldchage']))
            $fieldchage = $_POST['fieldchage'];
        else
            $fieldchage = '';
        if ($ids != '') {
            if (isset($_POST['arr']))
                $arr_input = (array)json_decode($_POST['arr']);
                // $arr_input = (array) json_decode(stripslashes($_POST['arr'])); // BaoNam: stripslashes sẽ gây lỗi nếu có dấu hai nháy "

            //nhận giá trị record
            $arr_tmp = $this->opm->select_one(array('_id' => new MongoId($ids)));
            $options = array();

            if (isset($arr_tmp[$arr_input['opname']]))
                $options = $arr_tmp[$arr_input['opname']];

            //tạo giá trị mới
            if (!is_numeric($arr_input['opid'])  || $arr_input['keys'] == 'add') {
                $newopt = array();
                //nhận giá trị default
                $newopt = $this->get_default_rel($arr_input['opname']);

                //nhận giá trị input
                if (isset($arr_input['value_object'])) {
                    $temps = array();
                    $temps = (array) $arr_input['value_object'];
                    foreach ($temps as $keys => $values) {

                        if (preg_match("/_id$/", $keys) && strlen($values) == 24)
                            $newopt[$keys] = new MongoId($values); //neu la id
                        else if (preg_match("/_date$/", $keys))
                            $newopt[$keys] = new MongoDate(strtotime($values)); //neu la ngay
                        else if (preg_match("/_price$/", $keys))
                            $newopt[$keys] = (float) $values; //neu la gia
                        else
                            $newopt[$keys] = $values;
                    }
                }
                $newopt = (array) $newopt;
                $options[] = $newopt;

                $data_insert = $arrass = $this->arr_associated_data($arr_input['opname'],$options,count($options)-1,$fieldchage);//set các field liên quan
                unset($arrass[$arr_input['opname']]);
                $arrass = $this->set_mongo_array($arrass);//lọc object
                $data_insert['_id'] = $ids;
                if (count($options) > 0)
                    if ($this->opm->save($data_insert)){
                        if(count($arrass)>0)
                            echo json_encode($arrass);
                        else
                            echo (count($options)-1);
                    }
            }else {//update giá trị cũ
                if (isset($arr_input['value_object'])) {
                    $temps = array();
                    $temps = (array) $arr_input['value_object'];

                    foreach ($temps as $keys => $values) {
                        if (preg_match("/_id$/", $keys) && strlen($values) == 24)
                            $options[$arr_input['opid']][$keys] = new MongoId($values); //neu la id
                        else if (preg_match("/_date$/", $keys))
                            $options[$arr_input['opid']][$keys] = new MongoDate(strtotime($values)); //neu la ngay
                        else if (is_numeric($values) && is_float((float) $values))
                            $options[$arr_input['opid']][$keys] = (float) $values; //neu la gia
                        else
                            $options[$arr_input['opid']][$keys] = $values;
                        if ($keys == 'country_id') {
                            $options[$arr_input['opid']]['province_state'] = '';
                            $options[$arr_input['opid']]['province_state_id'] = '';
                        }
                    }

                }

                $arrass = $data_insert = array();
                $data_insert = $arrass = $this->arr_associated_data($arr_input['opname'],$options,$arr_input['opid'],$fieldchage);
                unset($arrass[$arr_input['opname']]);
                $arrass = $this->set_mongo_array($arrass);//lọc object
                $data_insert['_id'] = $ids;

                if ($this->opm->save($data_insert)){
                    if(count($arrass)>0)
                        echo json_encode($arrass);
                    else
                        echo $arr_input['opid'];
                }
            }
        }
        else
            echo '';
        die;
    }

    function user_refs($contact_id) {

        $this->selectModel('Contact');
        $arr_contact = $this->Contact->select_one(array('_id' => new MongoId($contact_id)));

        $arr_tmp = $this->data; // nếu không gán như vầy sẽ bị lỗi khi ghi nhớ tab và load bị mất $this->data của entry
        $arr_tmp['Contact']['_id'] = $contact_id;
        $arr_tmp['Contact']['user_name_contact'] = $arr_contact['full_name'];

        $arr_tmp['Contact']['password_contact'] = 'P@ssword';
        $this->data = $arr_tmp;

        $this->selectModel('Setting');
        $arr_on_login_set_quick_view = $this->Setting->select_option_vl(array('setting_value'=>'on_login_set_quick_view'));
        $this->set('arr_on_login_set_quick_view', $arr_on_login_set_quick_view);

        $arr_language_printing_external_docs = $this->Setting->select_option_vl(array('setting_value'=>'language_printing_external_docs'));
        $this->set('arr_language_printing_external_docs', $arr_language_printing_external_docs);

        $arr_on_login_set_window_sizes = $this->Setting->select_option_vl(array('setting_value'=>'on_login_set_window_sizes'));
        $this->set('arr_on_login_set_window_sizes', $arr_on_login_set_window_sizes);

        $this->selectModel('Language');
        $arr_tmp = array();
        $arr_language = $this->Language->select_all(array('arr_order' => array('name' => 1)));
        foreach($arr_language as $key => $value){
            $arr_tmp[$value['value']] = isset($value['lang'])?$value['lang']:'';
        }
        $this->set('arr_language', $arr_tmp);
        $arr_return = $arr_contact;
        if(isset($arr_return['language']) && isset($arr_tmp['lang']) && in_array($arr_return['language'], $arr_tmp)){
            $arr_return['language'] = $arr_tmp['lang'];
        }
        $this->set('arr_return', $arr_return);
    }

    function workings_holidays_bk($contact_id){
        $day = '2013-12-06 ';
        $this->set('contact_id', $contact_id);
        $this->selectModel('Contact');
        $arr_contact = $this->Contact->select_one(array('_id' => new MongoId($contact_id)));
        if( !isset($arr_contact['working_hour']) ){
            for ($k=0; $k < 6; $k++) {
                $arr_contact['working_hour'][$k]['time1'] = '08:00';
                $arr_contact['working_hour'][$k]['time2'] = '18:00';
                $arr_contact['working_hour'][$k]['time3'] = '08:00';
                $arr_contact['working_hour'][$k]['time4'] = '18:00';
                $arr_contact['working_hour'][$k]['time5'] = '08:00';
                $arr_contact['working_hour'][$k]['time6'] = '18:00';
                $arr_contact['working_hour'][$k]['time7'] = '08:00';
                $arr_contact['working_hour'][$k]['time8'] = '18:00';
                $arr_contact['working_hour'][$k]['lunch1'] = '';
                $arr_contact['working_hour'][$k]['lunch2'] = '';
                $arr_contact['working_hour'][$k]['lunch3'] = '';
                $arr_contact['working_hour'][$k]['lunch4'] = '';

            }
            if( !$this->Contact->save($arr_contact) ){
                echo 'Error: ' . $this->Task->arr_errors_save[1];die;
            }
        }
        $total_hour_in_month = $total_week1 = $total_week2 = $total_week3 = $total_week4 =  0;
        if(!empty($arr_contact['working_hour'])){
            foreach($arr_contact['working_hour'] as $key => $contact){
                if($contact['time1'] != "" || $contact['time2'] != "" || $contact['time3'] != "" || $contact['time4'] != "" || $contact['time5'] != "" || $contact['time6'] != "" || $contact['time7'] != "" || $contact['time8'] != "" || $contact['lunch1']!= "" || $contact['lunch2'] != "" || $contact['lunch3'] != "" || $contact['lunch4'] != ""){
                    $time1 = strtotime($day.$contact['time1'].':00');
                    $time2 = strtotime($day.$contact['time2'].':00');
                    $time3 = strtotime($day.$contact['time3'].':00');
                    $time4 = strtotime($day.$contact['time4'].':00');
                    $time5 = strtotime($day.$contact['time5'].':00');
                    $time6 = strtotime($day.$contact['time6'].':00');
                    $time7 = strtotime($day.$contact['time7'].':00');
                    $time8 = strtotime($day.$contact['time8'].':00');
                    $lunch_hour = $contact['lunch1'] + $contact['lunch2'] + $contact['lunch3'] + $contact['lunch4'];

                    $total_week1 += ((($time2 - $time1)/60)/60)-$contact['lunch1'];
                    $total_week2 += ((($time4 - $time3)/60)/60)-$contact['lunch2'];
                    $total_week3 += ((($time6 - $time5)/60)/60)-$contact['lunch3'];
                    $total_week4 += ((($time8 - $time7)/60)/60)-$contact['lunch4'];
                }
            }
        }
        $total_hour_in_month = $total_week1 + $total_week2 + $total_week3 + $total_week4;
        $this->set('total_week1',$total_week1);
        $this->set('total_week2',$total_week2);
        $this->set('total_week3',$total_week3);
        $this->set('total_week4',$total_week4);
        $this->set('total_hour_in_month',$total_hour_in_month);
        $this->set('workings_hours', $arr_contact['working_hour']);
    }

    public function workings_holidays($contact_id,$mod='',$arr_ft=array()){
        $fix4week = 1;

        //from_date
        if($this->Session->check('from_date_'.$contact_id)){
            $from_date = $this->Session->read('from_date_'.$contact_id);
        }else{
            //$from_date = date("01 M Y");//fromdate ko phai la ngay dau tien cua thang nua
            $begin_week3 = ((int) date("w"))-1;
            if($begin_week3<0) //sunday
                $begin_week3 = 6;
            $begin_date_time3 = ((int) time()) - $begin_week3*24*60*60 - 14*24*60*60;
            $from_date = date("d M Y", $begin_date_time3);
            $this->Session->write('from_date_'.$contact_id,$from_date);
        }
        if(isset($arr_ft['form'])){
            $from_date = $arr_ft['form'];
        }

        // monday of begin of month
        $begin_week = ((int) date("w", strtotime($from_date)))-1;
        if($begin_week<0) //sunday
            $begin_week = 6;
        $begin_date_time = ((int) strtotime($from_date)) - $begin_week*24*60*60;
        $begin_date = date("d M Y", $begin_date_time);

        //to_date
        if($fix4week==1){
            $to_date = date("d M Y",$begin_date_time + 4*7*24*60*60 -1);
            $this->Session->write('to_date_'.$contact_id,$to_date);
        }else if($this->Session->check('to_date_'.$contact_id)){
            $to_date = $this->Session->read('to_date_'.$contact_id);
        }else{
            $to_date = date("t M Y");
            $this->Session->write('to_date_'.$contact_id,$to_date);
        }


        // monday of end of month
        $end_week = (int) date("w", strtotime($to_date))-1;
        if($end_week<0) //sunday
            $end_week = 6;
        $end_date_time = ((int) strtotime($to_date)) - $end_week*24*60*60;
        $end_date = date("d M Y H:i:s", $end_date_time);

        // sunday of end of month
        $end_week_date_time = $end_date_time + 7*24*60*60 -1;
        $end_week_date = date("d M Y H:i:s", $end_week_date_time);

        // echo $from_date.'</br />';
        // echo $begin_date.'</br />';
        // echo $to_date.'</br />';
        // echo $end_week.'</br />';
        // echo $end_date.'</br />';
        // echo $end_week_date.'</br />';

        //number week for view
        $num_week = $end_week_date_time - $begin_date_time;
        $num_week = round($num_week/(7*24*60*60));

        //arr_month - cac thang can xem
        $arr_month = array();
        $f_month = 100*(int) date("Y", strtotime($begin_date));
        $f_month += (int) date("m", strtotime($begin_date)); //nam+ngay
        $t_month = 100*(int) date("Y", strtotime($end_week_date));
        $t_month += (int) date("m", strtotime($end_week_date)); //nam+ngay
        $year = (int) date("Y",strtotime($begin_date));
        for($m=$f_month;$m<=$t_month;$m++){
                if($m%100<13 && $m%100!=0){
                    $arr_month[] = $m;
                }
        }

        // echo $num_week.'<br />';
        // echo $t_month;
        // pr($arr_month);die;

        $arr_month_data = array();
        $this->selectModel('WorkingHour');
        $arr_working_hour = $this->WorkingHour->select_all(
            array(
                'arr_where' => array(
                    'contact_id' => new MongoId($contact_id),
                    'year_month' => array('$in'=>$arr_month)
                )
            )
        );
        $arr_month_old = $arr_month;
        if(!empty($arr_working_hour))
        foreach ($arr_working_hour as $key => $value) {
            $arr_month_data[$value['year_month']] =$key;
            if(($key = array_search($value['year_month'], $arr_month)) !== false) {
                unset($arr_month[$key]);
            }
        }
        // pr($arr_month);
        //tao cac thang chua co data
        if(!empty($arr_month)){
            foreach ($arr_month as $year_month) {
               $this->buildWorkingHourNew($contact_id,$year_month);
            }
            $arr_working_hour = $this->WorkingHour->select_all(
                array(
                    'arr_where' => array(
                        'contact_id' => new MongoId($contact_id),
                        'year_month' => array('$in'=>$arr_month_old)
                    )
                )
            );
        }
        if(!empty($arr_working_hour))
        foreach ($arr_working_hour as $key => $value){
            $arr_month_data[$value['year_month']] = $value;
        }
        //===================================get data leave
        $this->selectModel('Contact');
        $arr_leave = $this->Contact->select_one(array('_id' => new MongoId($contact_id)), array('leave'));
        if(isset($arr_leave['leave']))
        foreach ($arr_leave['leave'] as $key => $value) {
            $leave_date_from = $value['date_from']->sec;
            $leave_date_to = $value['date_to']->sec+24*60*60-1;
            if(isset($value['approved']) && $value['approved']==1)
            for($d=$leave_date_from;$d<=$leave_date_to;$d=$d+(24*60*60)) {
                $ym = (int) date("Y",$d)*100+(int) date("m",$d);
                $dd = (int) date("d",$d);
                $leave_date[$ym][$dd] = $value['purpose'];
            }
        }
        //===================================general for view
        $data = array();
        $day = 1; $time1 = 1; $time2 = 2;
        $column = $num_week*2;
        $form_to_of_week = $sumweek = array();
        $sum_all_week = $sum_2_week_1 = $sum_2_week_2 = $tmp_holiday_num = 0;

        $day = $n = 1; $data = array();
        for($d=$begin_date_time;$d<=$end_week_date_time;$d=$d+(24*60*60)) {
            // fix truong hop dac biet 1 ngay ko du 24h
            if(isset($dd) && $dd == (int) date("d",$d)){
                $more = 24*60*60;
                $more = $more -  3600*(int)date("H",$d) - 60*(int)date("i",$d) - (int)date("s",$d);
                $d = $d+$more;
            }
            $ym = (int) date("Y",$d)*100+(int) date("m",$d);
            $dd = (int) date("d",$d);
            $data[$day][$n]['work_start'] = $arr_month_data[$ym]['day_'.$dd]['work_start'];
            $data[$day][$n]['work_end'] = $arr_month_data[$ym]['day_'.$dd]['work_end'];
            $data[$day][$n]['purpose'] = $arr_month_data[$ym]['day_'.$dd]['purpose'];
            $data[$day][$n]['lunch'] = $arr_month_data[$ym]['day_'.$dd]['lunch'];
            $data[$day][$n]['yearmonth'] = $ym;
            $data[$day][$n]['date'] =$dd;
            //if leave
            $tmp_holiday_num = 0;
            if(isset($leave_date[$ym][$dd])){
                $data[$day][$n]['work_start'] = '';
                $data[$day][$n]['work_end'] = '';
                $data[$day][$n]['lunch'] = '';
                $data[$day][$n]['leave_by'] = $leave_date[$ym][$dd];
                $tmp_holiday_num = 1;
            }

            //Re-format the time
            $data[$day][$n] = $this->reformatTime($data[$day][$n]);

            //tong thoi gian cua tuan
            //WorkEnd
            $workEnd = $data[$day][$n]['work_end'];
            /*if( !empty($data[$day][$n]['work_end']) ) {
                list($hour, $minute) = explode(':', $data[$day][$n]['work_end']);
                $hour = (int)$hour;
                $minute = (int)$minute;
                if( $minute > 0 && $minute < 30 ) {
                    $minute = 0;
                } else if( $minute > 30 ) {
                    $minute = 30;
                }
                $workEnd = str_pad($hour, 2, '0', STR_PAD_LEFT).':'.str_pad($minute, 2, '0', STR_PAD_LEFT);
            }*/
            //WorkStart
            $workStart = $data[$day][$n]['work_start'];
            /*if( !empty($data[$day][$n]['work_start']) ) {
                list($hour, $minute) = explode(':', $data[$day][$n]['work_start']);
                $hour = (int)$hour;
                $minute = (int)$minute;
                if( $minute > 0 && $minute < 30 ) {
                    $minute = 30;
                } else if( $minute > 30 ) {
                    $minute = 0;
                    $hour++;
                }
                $workStart = str_pad($hour, 2, '0', STR_PAD_LEFT).':'.str_pad($minute, 2, '0', STR_PAD_LEFT);
            }*/
            $tmp_times_of_day = strtotime('10-10-2014 '.$workEnd.':00') - strtotime('10-10-2014 '.$workStart.':00');
            $tmp_times_of_day -= (float)$data[$day][$n]['lunch']*3600;
            $tmp_times_of_day = $tmp_times_of_day/3600;
            $data[$day][$n]['hours_work'] = $tmp_times_of_day;
            if(isset($sumweek[$n]))
                $sumweek[$n] += $tmp_times_of_day;
            else
                $sumweek[$n] = $tmp_times_of_day;

            if(isset($holiday[$n]))
                $holiday[$n] += $tmp_holiday_num;
            else
                $holiday[$n] = $tmp_holiday_num;

            if($n>2)
                $sum_2_week_2 += $tmp_times_of_day;
            else
                $sum_2_week_1 += $tmp_times_of_day;

            $sum_all_week += $tmp_times_of_day;



            if($day==1)
                $form_to_of_week[$n] = '('.date("d M",$d);
            if($day==7)
                $form_to_of_week[$n] .= ' - '.date("d M",$d).')';
            $day++;
            if($day>7){
                $day = 1;
                $n++;
                if($n>$column+1)
                    $n=1;
            }
        }
        // pr($data);exit;
        if($mod=='report')
            return array('sumweek'=>$sumweek,'holiday'=>$holiday);
        $login = $logout = true;
        $query = $this->WorkingHour->select_one(array(
                        'contact_id' => new MongoId($contact_id),
                        'year_month' => (int)date('Ym')
                    ));
        if( !empty($query) ) {
            $daynum = (int)date('d');
            if( isset($query['day_'.$daynum]['work_start']) && !empty($query['day_'.$daynum]['work_start']) ) {
                $login = false;
            }
            if( isset($query['day_'.$daynum]['work_end']) && !empty($query['day_'.$daynum]['work_end']) ) {
                $logout = false;
            }
        }
        $this->set('login',$login);
        $this->set('logout',$logout);
        $this->set('data',$data);
        $this->set('contact_id',$contact_id);
        $this->set('from_date', $from_date);
        $this->set('to_date', $to_date);
        $this->set('num_week',$num_week);
        $this->set('form_to_of_week',$form_to_of_week);
        $this->set('begin_date_time', $begin_date_time);
        $this->set('end_week_date_time', $end_week_date_time);
        $this->set('sumweek', $sumweek);
        $this->set('sum_all_week', $sum_all_week);
        $this->set('sum_2_week_1', $sum_2_week_1);
        $this->set('sum_2_week_2', $sum_2_week_2);
        $this->set('lockInput', $this->check_permission('contacts_@_working_hours_@_edit'));

    }

    function reformatTime($data) {
        if($data['work_start'] != '') {
            $data['work_start'] = $this->formatTimeByQuarter($data['work_start']);
        }
        if($data['work_end'] != '') {
            $data['work_end'] = $this->formatTimeByQuarter($data['work_end']);
        }
        return $data;
    }
    function formatTimeByQuarter($time) {
        $hm = explode(":", $time);
        $h = intval($hm[0]);
        $m = intval(isset($hm[1]) ? $hm[1] : 0);
        if($m >= 6 && $m <= 18) $m = '15';
        elseif($m >= 19 && $m <= 35) $m = '30';
        elseif($m >= 36 && $m <= 48) $m = '45';
        else {
            if($m >= 49 && $m <= 59) $h += 1;
            $m = '00';
        }
        $h = str_pad($h, 2, '0', STR_PAD_LEFT);
        $time = $h.':'.$m;
        return $time;
    }

    function buildWorkingHourNew($contact_id,$year_month){
        $this->selectModel('WorkingHour');
        $default_work_star = '';
        $default_work_end = '';
        $month = $year_month%100;
        $year = floor($year_month/100);
        $endmonth = (int) (date("t",strtotime('01-'.$month.'-'.$year)));
        $working_hour = array();
        for($i=1; $i<=$endmonth; $i++){
            $working_hour['day_'.$i]['work_start'] = $default_work_star;
            $working_hour['day_'.$i]['work_end'] = $default_work_end;
            $working_hour['day_'.$i]['lunch'] = 0;
            $working_hour['day_'.$i]['off'] = 0;
            $working_hour['day_'.$i]['purpose'] = '';
            $check_sunday = date("w",strtotime($i.'-'.$month.'-'.$year));
            if($check_sunday==0){
                $working_hour['day_'.$i]['work_start'] = '';
                $working_hour['day_'.$i]['work_end'] = '';
            }

        }

        $working_hour['contact_id'] = new MongoId($contact_id);
        $working_hour['year_month'] = (int)$year_month;

        $this->WorkingHour->arr_default_before_save = $working_hour;
        if($this->WorkingHour->add())
            return true;
    }

    public function working_hours_start($login = '')
    {
        $user = $this->Session->read('arr_user');
        $this->selectModel('WorkingHour');
        $arr_where = array();
        $arr_where['contact_id'] = new MongoId($user['_id']);
        $arr_where['year_month'] = (int)date('Ym');
        $wh = $this->WorkingHour->select_one($arr_where);

        if(!isset($wh['_id'])){
            $this->buildWorkingHourNew($arr_where['contact_id'],$arr_where['year_month']);
            $wh = $this->WorkingHour->select_one($arr_where);
        }
        $work_start = (string)date('H:i');

        $daynum = (int)date('d');
        if( !isset($wh['day_'.$daynum]) ){
            $wh['day_'.$daynum]['work_start'] = $work_start;
            $wh['day_'.$daynum]['work_end'] = '';
            $wh['day_'.$daynum]['lunch'] = '';
            $wh['day_'.$daynum]['off'] = '';
        } else if( empty($login) ) {
            $wh['day_'.$daynum]['work_start'] = $work_start;
        }
        if( isset($wh['day_'.$daynum]) && empty($wh['day_'.$daynum]['work_start']) && $login == 'call-from-login' ) {
            $wh['day_'.$daynum]['work_start'] = $work_start;
        }
        $this->WorkingHour->save($wh);
        $this->autoRender = false;
    }

    public function working_hours_end()
    {
        $user = $this->Session->read('arr_user');
        $this->selectModel('WorkingHour');
        $arr_where = array();
        $arr_where['contact_id'] = new MongoId($user['_id']);
        $arr_where['year_month'] = (int)date('Ym');
        $wh = $this->WorkingHour->select_one($arr_where);

        if(!isset($wh['_id'])){
            $this->buildWorkingHourNew($arr_where['contact_id'],$arr_where['year_month']);
            $wh = $this->WorkingHour->select_one($arr_where);
        }
        $work_end = (string)date('H:i');

        $daynum = (int)date('d');
        if(!isset($wh['day_'.$daynum])){
            $wh['day_'.$daynum]['work_start'] = $work_end;
            $wh['day_'.$daynum]['work_end'] = $work_end;
            $wh['day_'.$daynum]['lunch'] = '';
            $wh['day_'.$daynum]['off'] = '';
        } else {
            $wh['day_'.$daynum]['work_end'] = $work_end;
        }
        $this->WorkingHour->save($wh);
        $this->autoRender = false;
    }

    public function working_hour_set_session($begin_time=0,$fc='',$contact_id=''){
        if(isset($_POST['begin_time']))
            $begin_time = $_POST['begin_time'];
        if(isset($_POST['fc']))
            $fc = $_POST['fc'];
        if(isset($_POST['contact_id']))
            $contact_id = $_POST['contact_id'];
        if($contact_id=='')
            $contact_id = $this->get_id();
        $from_date = $this->Session->read('from_date_'.$contact_id);
        $to_date = $this->Session->read('to_date_'.$contact_id);

        if($fc=='next'){
            $from_date_time = (int)$begin_time + 1;
            $from_date = date("d M Y",$from_date_time);
            $to_date = date("d M Y",$from_date_time + 4*7*24*60*60);
            echo 'ok';
        }
        if($fc=='pre'){
            $from_date_time = (int)$begin_time - 4*7*24*60*60;
            $from_date = date("d M Y",$from_date_time);
            $to_date = date("d M Y",$from_date_time + 4*7*24*60*60);
            echo 'ok';
        }

        if($fc=='date_from'){
            $begin_time = str_replace(",", "", $begin_time);
            $from_date_time = strtotime($begin_time);
            $from_date = date("d M Y",$from_date_time);
            $to_date = date("d M Y",$from_date_time + 4*7*24*60*60);
            echo 'ok';
        }

        if($fc=='date_to'){
            $begin_time = str_replace(",", "", $begin_time);
            $to_date_time = strtotime($begin_time);
            $to_date = date("d M Y H:i:s",$to_date_time);

            $num_day = (int) date("w", strtotime($to_date))-1;
            if($num_day<0) //sunday
                $num_day = 6;
            $monday_time = ((int) strtotime($to_date)) - $num_day*24*60*60;
            $monday = date("d M Y H:i:s", $monday_time);

            // sunday of end of month
            $sunday_time = $monday_time + 7*24*60*60 -1;
            $sunday = date("d M Y H:i:s", $sunday_time);
            $to_date = date("d M Y", $sunday_time);

            $from_date = date("d M Y",$sunday_time - 4*7*24*60*60);
            echo 'ok';
        }
        // echo $from_date.'--'.$to_date;die;

       $this->Session->write('from_date_'.$contact_id,$from_date);
       //$this->Session->write('to_date_'.$contact_id,$to_date);

        die;

    }

    public function print_hrs_worked_for_employee_w1(){
        if(!isset($_GET['print_pdf'])){
            $contact_id = $this->get_id();
            $this->selectModel('Contact');
            $day = '2013-12-06 ';
            $total = $total_week1 = $total_week2 = $total_day_w1 = $total_day_w2 = 0;
            $arr_contact = $this->Contact->select_one(array('_id' => new MongoId($contact_id)), array('full_name','company_phone','working_hour'));

            foreach($arr_contact['working_hour'] as $key => $hour){
                if($hour['time1'] != "" || $hour['time2'] != "" || $hour['time3'] != "" || $hour['time4'] != "" || $hour['lunch1']!= "" || $hour['lunch2'] != ""){
                    $time1 = strtotime($day.$hour['time1'].':00');
                    $time2 = strtotime($day.$hour['time2'].':00');
                    $time3 = strtotime($day.$hour['time3'].':00');
                    $time4 = strtotime($day.$hour['time4'].':00');
                    $total_week1 += ((($time2 - $time1)/60)/60)-$hour['lunch1'];
                    $total_week2 += ((($time4 - $time3)/60)/60)-$hour['lunch2'];
                }
            }
            $total = $total_week1 + $total_week2;

            $contact = array();
            $contact = $arr_contact['working_hour'];
            $arr_data = array();
            if(!empty($arr_contact)){
                $html='';
                $i=1;
                $arr_data = array();
                $arr_day = array( 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday' );

                    for ($k=0; $k < 7; $k++){
                        $html .= '<tr class="'.($i%2==0 ? 'bg_2' : 'bg_1').'">';
                        if($contact[$k]['time1'] != "" || $contact[$k]['time2']!= ""){
                            $time_in = strtotime($day.$contact[$k]['time1']);
                            $time_out = strtotime($day.$contact[$k]['time2']);
                            $total_day_w1 = ((($time_out - $time_in)/60)/60)-$contact[$k]['lunch1'];
                        }else{
                            $total_day_w1 = 0;
                        }
                        if($k == 0)
                            $html .= '<td rowspan="7" class="center_text" style="background-color:#eeeeee">Week 1</td>';
                        $html .= '<td class="center_text">'.$arr_day[$k].'</td>';

                        $html .= '<td class="center_text">'.$contact[$k]['time1'].'</td>';
                        $html .= '<td class="center_text">'.$contact[$k]['time2'].'</td>';
                        $html .= '<td class="right_text">'.(isset($contact[$k]['lunch1']) ? $this->opm->format_currency($contact[$k]['lunch1']) : '').'</td>';
                        $html .= '<td class="right_text">'.$this->opm->format_currency($total_day_w1).'</td>';
                        $html .= '</tr>';
                        $i++;
                    }
                        $html .= '<tr>
                                    <td colspan="4"></td>';
                        $html .= '<td class="right_text" style="font-size:11">Sub Total: </td>';
                        $html .= '<td class="right_text">'.$this->opm->format_currency($total_week1).'</td>';
                        $html .= '</tr>';

                        $html .= '<tr class="'.($i+1%2==0 ? 'bg_2' : 'bg_1').'"><td colspan="5" class="center_text">&nbsp;</td></tr>';


                    for ($k=0; $k < 7; $k++){
                        $html .= '<tr class="'.($i%2==0 ? 'bg_2' : 'bg_1').'">';
                        if($contact[$k]['time3'] != "" || $contact[$k]['time4']!= ""){
                            $time_in = strtotime($day.$contact[$k]['time3']);
                            $time_out = strtotime($day.$contact[$k]['time4']);
                            $total_day_w2 = ((($time_out - $time_in)/60)/60)-$contact[$k]['lunch2'];
                        }else{
                            $total_day_w2 = 0;
                        }
                        if($k == 0)
                            $html .= '<td rowspan="7" class="center_text">Week 2</td>';
                        $html .= '<td class="center_text">'.$arr_day[$k].'</td>';
                        $html .= '<td class="center_text">'.$contact[$k]['time3'].'</td>';
                        $html .= '<td class="center_text">'.$contact[$k]['time4'].'</td>';
                        $html .= '<td class="right_text">'.(isset($contact[$k]['lunch2']) ? $this->opm->format_currency($contact[$k]['lunch2']) : '').'</td>';
                        $html .= '<td class="right_text">'. $this->opm->format_currency($total_day_w2).'</td>';
                        $html .= '</tr>';
                        $i++;
                    }
                        $html .= '<tr class="'.($i+1%2==0 ? 'bg_2' : 'bg_1').'">
                                    <td colspan="4"></td>';
                        $html .= '<td class="right_text" style="font-size:11">Sub Total: </td>';
                        $html .= '<td class="right_text">'.$this->opm->format_currency($total_week2).'</td>';
                        $html .= '</tr>';

                        $html .= '<tr style="background-color:#eeeeee">
                                    <td colspan="4"></td>';
                        $html .= '<td class="right_text" style="font-weight:bold">Total: (hrs) </td>';
                        $html .= '<td class="right_text" style="color:red; font-size:20;font-weight:bold">'.$total.'</td>';
                        $html .= '</tr>';

                $arr_data['left_info'] = array(
                                           'name'=>'<span class="bold_text">Name</span>:     '.$arr_contact['full_name'].'<br /><span class="bold_text">Phone</span>:       '.$arr_contact['company_phone'].'<br />',
                                           'address' => '',
                                           );
                $arr_data['title'] = array('Week','', 'In', 'Out', 'Lunch', 'Hrs. Woked');
                $arr_data['content'] = $html;
                $arr_data['report_name'] = 'Hrs.Worked Employee';
                $arr_data['report_file_name'] = 'Hrs_'.md5(time());
                $arr_data['report_orientation'] = 'portait';
                //$arr_data['report_size'] = '8.5in*11in';
                Cache::write('hrs_worked_employee',$arr_data);
            }
        }else{
            $arr_data = Cache::read('hrs_worked_employee');
            Cache::delete('hrs_worked_employee');
        }
        $this->render_pdf($arr_data);
    }

    public function print_hrs_worked_for_employee_w2(){
        if(!isset($_GET['print_pdf'])){
            $contact_id = $this->get_id();
            $this->selectModel('Contact');
            $day = '2013-12-06 ';
            $total = $total_week3 = $total_week4 = $total_day_w3 = $total_day_w4 = 0;
            $arr_contact = $this->Contact->select_one(array('_id' => new MongoId($contact_id)), array('full_name','company_phone','working_hour'));

            foreach($arr_contact['working_hour'] as $key => $hour){
                if($hour['time5'] != "" || $hour['time6'] != "" || $hour['time7'] != "" || $hour['time8'] != "" || $hour['lunch1']!= "" || $hour['lunch2'] != "" || $hour['lunch3'] != "" || $hour['lunch4'] != ""){
                    $time5 = strtotime($day.$hour['time5'].':00');
                    $time6 = strtotime($day.$hour['time6'].':00');
                    $time7 = strtotime($day.$hour['time7'].':00');
                    $time8 = strtotime($day.$hour['time8'].':00');
                    $total_week3 += ((($time6 - $time5)/60)/60)-$hour['lunch3'];
                    $total_week4 += ((($time8 - $time7)/60)/60)-$hour['lunch4'];
                }
            }
            $total = $total_week3 + $total_week4;

            $contact = array();
            $contact = $arr_contact['working_hour'];
            $arr_data = array();
            if(!empty($arr_contact)){
                $html='';
                $i=1;
                $arr_data = array();
                $arr_day = array( 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday' );

                    for ($k=0; $k < 7; $k++){
                        $html .= '<tr class="'.($i%2==0 ? 'bg_2' : 'bg_1').'">';
                        if($contact[$k]['time5'] != "" || $contact[$k]['time6']!= ""){
                            $time_in = strtotime($day.$contact[$k]['time5']);
                            $time_out = strtotime($day.$contact[$k]['time6']);
                            $total_day_w3 = ((($time_out - $time_in)/60)/60)-$contact[$k]['lunch3'];
                        }else{
                            $total_day_w3 = 0;
                        }
                        if($k == 0)
                            $html .= '<td rowspan="7" class="center_text" style="background-color:#eeeeee">Week 3</td>';
                        $html .= '<td class="center_text">'.$arr_day[$k].'</td>';

                        $html .= '<td class="center_text">'.$contact[$k]['time5'].'</td>';
                        $html .= '<td class="center_text">'.$contact[$k]['time6'].'</td>';
                        $html .= '<td class="right_text">'.(isset($contact[$k]['lunch3']) ? $contact[$k]['lunch3'] : 0).'</td>';
                        $html .= '<td class="right_text">'.$this->opm->format_currency($total_day_w3).'</td>';
                        $html .= '</tr>';
                        $i++;
                    }
                        $html .= '<tr>
                                    <td colspan="4"></td>';
                        $html .= '<td class="right_text" style="font-size:11">Sub Total: </td>';
                        $html .= '<td class="right_text">'.$this->opm->format_currency($total_week3).'</td>';
                        $html .= '</tr>';

                        $html .= '<tr class="'.($i+1%2==0 ? 'bg_2' : 'bg_1').'"><td colspan="5" class="center_text">&nbsp;</td></tr>';


                    for ($k=0; $k < 7; $k++){
                        $html .= '<tr class="'.($i%2==0 ? 'bg_2' : 'bg_1').'">';
                        if($contact[$k]['time7'] != "" || $contact[$k]['time8']!= ""){
                            $time_in = strtotime($day.$contact[$k]['time7']);
                            $time_out = strtotime($day.$contact[$k]['time8']);
                            $total_day_w4 = ((($time_out - $time_in)/60)/60)-$hour['lunch4'];
                        }else{
                             $total_day_w4 = 0;
                        }
                        if($k == 0)
                            $html .= '<td rowspan="7" class="center_text">Week 4</td>';
                        $html .= '<td class="center_text">'.$arr_day[$k].'</td>';
                        $html .= '<td class="center_text">'.$contact[$k]['time7'].'</td>';
                        $html .= '<td class="center_text">'.$contact[$k]['time8'].'</td>';
                        $html .= '<td class="right_text">'.(isset($contact[$k]['lunch4']) ? $contact[$k]['lunch4'] : 0).'</td>';
                        $html .= '<td class="right_text">'. $this->opm->format_currency($total_day_w4).'</td>';
                        $html .= '</tr>';
                        $i++;
                    }
                        $html .= '<tr class="'.($i+1%2==0 ? 'bg_2' : 'bg_1').'">
                                    <td colspan="4"></td>';
                        $html .= '<td class="right_text" style="font-size:11">Sub Total: </td>';
                        $html .= '<td class="right_text">'.$this->opm->format_currency($total_week4).'</td>';
                        $html .= '</tr>';

                        $html .= '<tr style="background-color:#eeeeee">
                                    <td colspan="4"></td>';
                        $html .= '<td class="right_text" style="font-weight:bold">Total: (hrs) </td>';
                        $html .= '<td class="right_text" style="color:red; font-size:20;font-weight:bold">'.$total.'</td>';
                        $html .= '</tr>';

                $arr_data['left_info'] = array(
                                           'name'=>'<span class="bold_text">Name</span>:     '.$arr_contact['full_name'].'<br /><span class="bold_text">Phone</span>:       '.$arr_contact['company_phone'].'<br />',
                                           'address' => '',
                                           );
                $arr_data['title'] = array('Week','', 'In', 'Out', 'Lunch', 'Hrs. Woked');
                $arr_data['content'] = $html;
                $arr_data['report_name'] = 'Hrs.Worked Employee';
                $arr_data['report_file_name'] = 'Hrs_'.md5(time());
                $arr_data['report_orientation'] = 'portait';
                //$arr_data['report_size'] = '8.5in*11in';
                Cache::write('hrs_worked_employee',$arr_data);
            }
        }else{
             $arr_data = Cache::read('hrs_worked_employee');
            Cache::delete('hrs_worked_employee');
        }
        $this->render_pdf($arr_data);
    }

    function hour_worked_last(){
        if(!isset($_GET['print_pdf'])){
            if( !isset($_POST['employees']) || empty($_POST['employees']) ) {
                $this->redirect(URL.'/contacts/options');
                die;
            }
            $arr_where = array('is_employee' => 1, 'off_report_working_hour' => array('$exists' =>false));
            foreach($_POST['employees'] as $employee) {
                if( strlen($employee) != 24 ) continue;
                $arr_where['_id']['$in'][] = new MongoId($employee);
            }
            $this->selectModel('Contact');
            // $day = '2013-12-06 ';
            $total = $total_week1 = $total_week2 = 0;
            $this->selectModel('WorkingHour');
            $arr_from_to = array();
            $arr_contacts = $this->Contact->select_all(array(
                                                  'arr_where' => $arr_where,
                                                  'arr_field' => array('is_employee','full_name','working_hour','company_phone','off_report_working_hour'),
                                                  ));
            $arr_employee = array();
            $worked_hr = 8;
            foreach($arr_contacts as $key => $contact){
                $workings_holidays = $this->workings_holidays($key,'report',$arr_from_to);
                $arr_week_time = $workings_holidays['sumweek'];
                $arr_holiday = $workings_holidays['holiday'];
                $arr_employee[$key] = array(
                                            'company_phone'=>$contact['company_phone'],
                                            'full_name'=>$contact['full_name'],
                                            'total_week1' => (float)$arr_week_time[1],
                                            'total_week2' => (float)$arr_week_time[2],
                                            'holiday1' => (float)$arr_holiday[1]*$worked_hr,
                                            'holiday2' => (float)$arr_holiday[2]*$worked_hr,
                                            'total' => (float)$arr_week_time[1]+(float)$arr_week_time[2],
                                            );
            }
            Cache::write('hrs_worked_all_employee_excel',$arr_employee);
            if(count($arr_employee) > 0){
                $html='';
                $i=0;
                $arr_data = array();
                $total = 0;
                foreach($arr_employee as $k_employee => $v_employee){
                    $html .= '<tr class="'.($i%2==0 ? 'bg_2' : 'bg_1').'">';
                    $html .= '<td ></td>';
                    $html .= '<td class="left_text">'.$v_employee['full_name'].'</td>';
                    $html .= '<td class="right_text">'.$this->opm->format_currency($v_employee['total_week1']).'</td>';
                    $html .= '<td class="right_text">'.$this->opm->format_currency($v_employee['total_week2']).'</td>';
                    $html .= '<td >'.($v_employee['holiday1']+$v_employee['holiday2']).'</td>';
                    $html .= '<td class="right_text">'.$this->opm->format_currency($v_employee['total']).'</td>';
                    $html .= '</tr>';
                    $total += $v_employee['total'];
                    $i++;
                }
                 $html .= '<tr class="'.($i%2==0 ? 'bg_2' : 'bg_1').'">';
                    $html .= '<td colspan="5" class="bold_text right_text">Production hours worked and holiday total (hrs): </td>';
                    $html .= '<td class="bold_text right_text" style="color:red">'.$this->opm->format_currency($total).'</td>';
                    $html .= '</tr>';
                $arr_data['title'] = array('EID','Name', 'Week1', 'Week2', 'Holiday', 'Total');
                $arr_data['content'] = $html;
                $arr_data['report_name'] = 'Hrs. Worked Last';
                $arr_data['report_file_name'] = 'Hrs_'.md5(time());
                $arr_data['report_orientation'] = 'portrait';
                $arr_data['excel_url'] = URL.'/contacts/hour_worked_last_excel';
                Cache::write('hrs_worked_all_employee',$arr_data);
            }
        }else{
            $arr_data = Cache::read('hrs_worked_all_employee');
            Cache::delete('hrs_worked_all_employee');
        }
        $this->render_pdf($arr_data);
    }

    function hour_worked_last_excel()
    {
        $arr_employee = Cache::read('hrs_worked_all_employee_excel');
        Cache::delete('hrs_worked_all_employee_excel');
        if(!$arr_employee){
            echo 'No data';die;
        }

        App::import('Vendor', 'phpexcel/PHPExcel');
        $objPHPExcel = new PHPExcel();
        // Set document properties
        $objPHPExcel->getProperties()->setCreator('')
                                     ->setLastModifiedBy('')
                                     ->setTitle('Contact - Hours Worked LAST')
                                     ->setSubject('Contact - Hours Worked LAST')
                                     ->setDescription('Contact - Hours Worked LAST')
                                     ->setKeywords('Contact - Hours Worked LAST')
                                     ->setCategory('Contact - Hours Worked LAST');
        $worksheet = $objPHPExcel->getActiveSheet();
        $worksheet->setCellValue('A1', '#')
                    ->setCellValue('B1', 'EID')
                    ->setCellValue('C1', 'Name')
                    ->setCellValue('D1', 'Week1')
                    ->setCellValue('E1', 'Week2')
                    ->setCellValue('F1', 'Holyday')
                    ->setCellValue('G1', 'Total');
        $worksheet->freezePane('H2');
        $i = 2;
        foreach($arr_employee as $employee){
            $worksheet->setCellValue('A'.$i, $i-1)
                        ->setCellValue('B'.$i, '')
                        ->setCellValue('C'.$i, $employee['full_name'])
                        ->setCellValue('D'.$i, $employee['total_week1'])
                        ->setCellValue('E'.$i, $employee['total_week2'])
                        ->setCellValue('F'.$i, $employee['holiday1']+$employee['holiday2'])
                        ->setCellValue('G'.$i, $employee['total']);
            $i ++;
        }
        $worksheet->setCellValue('A'.$i,($i-1).' record(s) listed');
        $worksheet->setCellValue('D'.$i, 'Production hours worked and holiday total (hrs):');
        $worksheet->setCellValue('G'.$i, '=SUM(G2:G'.($i-1).')');
        $worksheet->mergeCells("A$i:C$i");
        $worksheet->mergeCells("D$i:F$i");
        $worksheet->getStyle("D$i")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
        $worksheet->getRowDimension(8)->setRowHeight(-1);
        $worksheet->getStyle('A1:G1')->getFont()->setBold(true);
        $worksheet->getStyle('A'.$i.':G'.$i)->getFont()->setBold(true);
        $worksheet->getStyle('B1:B'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
        $styleArray = array(
                'borders' => array(
                   'allborders' => array(
                       'style' => PHPExcel_Style_Border::BORDER_THIN
                    )
                ),
                'font'  => array(
                    'size'  => 11,
                    'name'  => 'Century Gothic'
                )
        );
        $worksheet->getStyle('A1:G'.$i)->applyFromArray($styleArray);
        for($i = 'A'; $i !== 'H'; $i++){
            $worksheet->getColumnDimension($i)
                            ->setAutoSize(true);
        }
        $objPHPExcel->setActiveSheetIndex(0);
        $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
        $objWriter->save(APP.DS.'webroot'.DS.'upload'.DS.'Contact_HoursWorksLast.xlsx');
        $this->redirect('/upload/Contact_HoursWorksLast.xlsx');
        die;
    }

    function hour_worked_current(){
        if(!isset($_GET['print_pdf'])){
            if( !isset($_POST['employees']) || empty($_POST['employees']) ) {
                $this->redirect(URL.'/contacts/options');
                die;
            }
            $arr_where = array('is_employee' => 1, 'off_report_working_hour' => array('$exists' =>false));
            foreach($_POST['employees'] as $employee) {
                if( strlen($employee) != 24 ) continue;
                $arr_where['_id']['$in'][] = new MongoId($employee);
            }
            $this->selectModel('Contact');
            // $day = '2013-12-06 ';
            $total = $total_week3 = $total_week4 = 0;
            $this->selectModel('WorkingHour');
            $arr_from_to = array();
            $arr_contacts = $this->Contact->select_all(array(
                                                  'arr_where' => $arr_where,
                                                  'arr_field' => array('is_employee','full_name','working_hour','company_phone','off_report_working_hour'),
                                                  ));
            $arr_employee = array();
            $worked_hr = 8;
            foreach($arr_contacts as $key => $contact){
                $workings_holidays = $this->workings_holidays($key,'report',$arr_from_to);
                $arr_week_time = $workings_holidays['sumweek'];
                $arr_holiday = $workings_holidays['holiday'];
                $arr_employee[$key] = array(
                                            'company_phone'=>$contact['company_phone'],
                                            'full_name'=>$contact['full_name'],
                                            'total_week3' => (float)$arr_week_time[3],
                                            'total_week4' => (float)$arr_week_time[4],
                                            'holiday3' => (float)$arr_holiday[3]*$worked_hr,
                                            'holiday4' => (float)$arr_holiday[4]*$worked_hr,
                                            'total' => (float)$arr_week_time[3]+(float)$arr_week_time[4],
                                            );
            }
            if(count($arr_employee) > 0){
                $html='';
                $i=0;
                $arr_data = array();
                $total = 0;
                foreach($arr_employee as $k_employee => $v_employee){
                    $html .= '<tr class="'.($i%2==0 ? 'bg_2' : 'bg_1').'">';
                    $html .= '<td ></td>';
                    $html .= '<td class="left_text">'.$v_employee['full_name'].'</td>';
                    $html .= '<td class="right_text">'.$this->opm->format_currency($v_employee['total_week3']).'</td>';
                    $html .= '<td class="right_text">'.$this->opm->format_currency($v_employee['total_week4']).'</td>';
                    $html .= '<td class="right_text">'.$this->opm->format_currency($v_employee['holiday3']+$v_employee['holiday4']).'</td>';
                    $html .= '<td class="right_text">'.$this->opm->format_currency($v_employee['total']).'</td>';
                    $html .= '</tr>';
                    $total += $v_employee['total'];
                    $i++;
                }
                 $html .= '<tr class="'.($i%2==0 ? 'bg_2' : 'bg_1').'">';
                    $html .= '<td colspan="5" class="bold_text right_text">Production hours worked and holiday total (hrs): </td>';
                    $html .= '<td class="bold_text right_text" style="color:red">'.$this->opm->format_currency($total).'</td>';
                    $html .= '</tr>';
                $arr_data['title'] = array('EID','Name', 'Week3', 'Week4', 'Holiday', 'Total');
                $arr_data['content'] = $html;
                $arr_data['report_name'] = 'Hrs. Worked Current';
                $arr_data['report_file_name'] = 'Hrs_'.md5(time());
                $arr_data['report_orientation'] = 'portait';
                Cache::write('hrs_worked_all_employee',$arr_data);
            }
        }else{
             $arr_data = Cache::read('hrs_worked_all_employee');
             Cache::delete('hrs_worked_all_employee');
        }
        $this->render_pdf($arr_data);
    }


    function work_hour_auto_save($contact_id){
        $this->selectModel('WorkingHour');
        if(isset($_POST['yearmonth'])){
            $arr_working_hour = $this->WorkingHour->select_one(
                        array(
                                'contact_id' => new MongoId($contact_id),
                                'year_month'=> (int)$_POST['yearmonth'],
                            )
                        );
            if(!empty($arr_working_hour) && isset($arr_working_hour['_id'])){
                $arr_save = array();
                $arr_save['_id'] = $arr_working_hour['_id'];
                $day = $_POST['day'];
                $keyval = $_POST['keyval'];
                $rightPass = $this->Session->read('contact_pemission_change_hours');
                if( in_array($keyval, array('work_end', 'work_start'))
                    && !$this->check_permission('contacts_@_working_hour_@_edit') ) {
                    if( !$rightPass ) {
                        echo 'need_manager';
                        die;
                    } else {
                        $this->Session->delete('contact_pemission_change_hours');
                    }
                }
                $arr_save['day_'.$day] = $arr_working_hour['day_'.$day];
                if(isset($arr_save['day_'.$day][$keyval]))
                    $old_val = (int)strtotime($arr_save['day_'.$day][$keyval]);
                else
                    $old_val = 0;
                $arr_save['day_'.$day][$keyval] = $_POST['value'];

                $bool = false; $message = 'ok';
                $int_date = (int)$_POST['yearmonth']*100+(int)$_POST['day'];

                if($_POST['yearmonth']==date('Ym') && $_POST['day']==date('d')){ // dung ngay thi cho phep chinh
                    //if((int)strtotime($_POST['value']) > $old_val)//neu gia tri chinh lon hon gia tri cu thi cho phep
                        $bool = true;
                    //else
                        //$message = 'min_value';
                }else if($int_date > (int)date('Ymd')) // future
                    $message = 'no_edit_future_date';
                else // last date
                    $message = 'no_edit_last_date';

                if($this->check_permission('contacts_@_workings_holidays_tab_@_change_all_date') || $this->Session->check('contact_pemission_change_hours')){
                    if($int_date <= (int)date('Ymd')) // future
                        $bool = true;
                }

                if($bool){
                    if($this->WorkingHour->save($arr_save)){
                         echo 'ok';
                    }else
                         echo 'Error: ' . $this->Task->arr_errors_save[1];
                }else
                    echo $message;

            }

            die;
        }
    }


    function work_hour_auto_save_bk($contact_id){
        $reload = true;
        $this->selectModel('WorkingHour');
        $arr_contact = $this->WorkingHour->select_one(array('_id' => new MongoId($contact_id)));
        if( !empty($_POST) ){
            $arr_contact['working_hour'][$_POST['day']][$_POST['time']] = $_POST['value'];

            if( $_POST['time'] == 'time3' && !isset($arr_contact['working_hour'][$_POST['day']]['time4']) ){
                $arr_contact['working_hour'][$_POST['day']]['time4'] = date('H:i', strtotime('2013-12-06 '.$_POST['value'].':00') + 3600);
                $reload = true;
            }

            if( $_POST['time'] == 'time5' && !isset($arr_contact['working_hour'][$_POST['day']]['time6']) ){
                $arr_contact['working_hour'][$_POST['day']]['time6'] = date('H:i', strtotime('2013-12-06 '.$_POST['value'].':00') + 3600);
                $reload = true;
            }

            // Kiểm tra dữ liệu
            $this->_work_hour_check_time($arr_contact['working_hour'][$_POST['day']]);

            if( $this->Contact->save($arr_contact) ){
                if( isset($reload) )
                    echo 'reload';
                else
                    echo 'ok';
            }else{
                echo 'Error: ' . $this->Task->arr_errors_save[1];die;
            }
        }
        die;
    }



    function _work_hour_check_time( $arr_time ){
        $day = '2013-12-06 ';

        if( $arr_time['time1'] != "" ){
            $time1 = strtotime($day.$arr_time['time1'].':00');
            $time2 = strtotime($day.$arr_time['time2'].':00');
            if( $time1 > $time2 ){
                echo 'Time 1: first time can not greater than end time'; die;
            }
        }

        if(isset($arr_time['time3']) && isset($arr_time['time4'])){
            $time3 = strtotime($day.$arr_time['time3'].':00');
            $time4 = strtotime($day.$arr_time['time4'].':00');
            if($time3 > $time4){
                echo 'Time 2: first time can not greater than end time';die;
            }
        }

        if(isset($arr_time['time5']) && isset($arr_time['time6'])){
            $time5 = strtotime($day.$arr_time['time5'].':00');
            $time6 = strtotime($day.$arr_time['time6'].':00');
            if($time5 > $time6){
                echo 'Time 3: first time can not greater than end time';die;
            }
        }

        if(isset($arr_time['time7']) && isset($arr_time['time8'])){
            $time7 = strtotime($day.$arr_time['time7'].':00');
            $time8 = strtotime($day.$arr_time['time8'].':00');
            if($time7 > $time8){
                echo 'Time 4: first time can not greater than end time';die;
            }
        }


       /* if( isset($arr_time['time3']) ){
            $time3 = strtotime($day.$arr_time['time3'].':00');
            if( $time3 <= $time2 ){
                echo 'First time of Time 2 can not less than end time of Time 1'; die;
            }
        }

        if( isset($arr_time['time3']) && isset($arr_time['time4']) ){
            $time3 = strtotime($day.$arr_time['time3'].':00');
            $time4 = strtotime($day.$arr_time['time4'].':00');
            if( $time3 >= $time4 ){
                echo 'Time 2: first time can not greater than end time'; die;
            }
        }

        if( isset($arr_time['time5']) ){
            $time4 = strtotime($day.$arr_time['time4'].':00');
            $time5 = strtotime($day.$arr_time['time5'].':00');
            if( $time5 <= $time4 ){
                echo 'First time of Time 3 can not less than end time of Time 2'; die;
            }
        }

        if( isset($arr_time['time5']) && isset($arr_time['time6']) ){
            $time5 = strtotime($day.$arr_time['time5'].':00');
            $time6 = strtotime($day.$arr_time['time6'].':00');
            if( $time5 >= $time6 ){
                echo 'Time 3: first time can not greater than end time'; die;
            }
        }*/
    }

    function save_data_for_non_model() {
        if (isset($_POST['fieldname']))
            $field = $_POST['fieldname'];
        if (isset($_POST['values']))
            $value = $_POST['values'];
        if (isset($_POST['ids']))
            $ids = $_POST['ids'];
        $arr_save = array();
        $this->selectModel('Contact');
        if (isset($field) && isset($value) ){
            $arr_save['_id'] = new MongoId($ids);
            $arr_save[$field]=$value;
            if ($this->Contact->save($arr_save)) {
                if($field == 'theme' && $value != '')
                    $_SESSION['theme'] = strtolower($value);
                echo 'ok';die;
            }
            else{
                echo 'Can not save.';die;
            }
        }
        die;
    }

    function user_refs_auto_save() {
        $arr_post = $this->data['Contact'];
        $arr_save['_id'] =  new MongoId($arr_post['_id']);
        $arr_save['password'] = md5(md5(trim($arr_post['password_contact'])).(string)$arr_save['_id']);
        $arr_save['full_name'] = $arr_post['user_name_contact'];
        while( $this->Contact->count(array(
                                'deleted' => false,
                                'full_name' => $arr_save['full_name'],
                                '_id' => array('$ne' => $arr_save['_id']),
                                'is_employee' => 1  )) ){
            $arr_save['full_name'] .= rand(1, 9);
        }

/*        if( !isset($arr_save['roles']) ){
            $this->selectModel('Role');
            $arr_role = $this->Role->select_one(array('name' => 'Administrator'));
            $arr_save['roles']= array( 'roles' => array($arr_role['_id']) );
        }*/

        $this->selectModel('Contact');
        if ($this->Contact->save($arr_save)) {
            echo json_encode($arr_save);
        } else {
            echo 'Error: ' . $this->Contact->arr_errors_save[1];
        }
        die;
    }

   /*  $arr_contact = $this->Contact->select_all(array(
                    'arr_where' => array('company_id' => new MongoId($valueid)),
                    'arr_order' => array('_id' => -1),
                ));*/

    function convert_contact_no(){
        $this->selectModel('Contact');
        $arr_contact = $this->Contact->select_all(array(
                                                  'arr_where' => array('_id' => array('$gt' => new MongoId("5356ed36e6f2b2b17d437570")),'no'=>1),
                                                  'arr_field' => array('no'),
                                                  'arr_order' => array('_id'=>1)
                                                  ));
        $no = 5476;
        $arr_save= array();
        $i = 0;
        foreach($arr_contact as $contact){
            $i++;
            $arr_save['_id'] = $contact['_id'];
            $arr_save['no'] = ++$no;
            $this->Contact->rebuild_collection($arr_save);
        }
        echo 'Found: '.$arr_contact->count();
        echo '<br />Done '.$i;
        die;
    }

    function create_email(){
        $this->selectModel('Contact');
        $arr_contact = $this->Contact->select_one(array('_id'  => new MongoId($this->get_id())));

        $arr_save = array();
        $this->selectModel('Communication');
        $arr_save['code'] = $this->Communication->get_auto_code('code');

        $arr_save['comms_type'] = 'Email';
        $arr_save['sign_off'] = 'Regards';
        $arr_save['company_id'] = isset($arr_contact['company_id'])?$arr_contact['company_id']:'';
        $arr_save['company_name'] = isset($arr_contact['company'])?$arr_contact['company']:'';
        $arr_save['email'] = isset($arr_contact['email'])?$arr_contact['email']:'';
        $arr_save['module'] = isset($this->params->params['controller'])?$this->params->params['controller']:'';
        $arr_save['contact_name'] = isset($arr_contact['first_name'])?$arr_contact['first_name']:'';
        $arr_save['last_name'] = isset($arr_contact['last_name'])?$arr_contact['last_name']:'';
        $arr_save['position'] = isset($arr_contact['position'])?$arr_contact['position']:'';
        $arr_save['comms_date'] = new MongoDate();


        if ($this->Communication->save($arr_save)) {
            $this->redirect('/communications/entry/'. $this->Communication->mongo_id_after_save);
        }
        $this->redirect('/communications/entry');

    }
    function create_fax($contact_id = ''){
        if($contact_id == '')$contact_id = $this->get_id();
        $this->selectModel('Contact');
        $arr_contact = $this->Contact->select_one(array('_id'=>new MongoId($contact_id)));
        $arr_save = array();
        $this->selectModel('Communication');
        $arr_save['code'] = $this->Communication->get_auto_code('code');
        $arr_save['comms_type'] = 'Fax';
        $arr_save['sign_off'] = 'Regards';
        $arr_save['company_id'] = isset($arr_contact['company_id'])?$arr_contact['company_id']:'';
        $arr_save['company_name'] = isset($arr_contact['company'])?$arr_contact['company']:'';
        $arr_save['email'] = isset($arr_contact['email'])?$arr_contact['email']:'';
        $arr_save['module'] = isset($this->params->params['controller'])?$this->params->params['controller']:'';
        $arr_save['contact_name'] = isset($arr_contact['first_name'])?$arr_contact['first_name']:'';
        $arr_save['last_name'] = isset($arr_contact['last_name'])?$arr_contact['last_name']:'';
        $arr_save['position'] = isset($arr_contact['position'])?$arr_contact['position']:'';
        $arr_save['comms_date'] = new MongoDate();
        $arr_save['fax'] = isset($arr_contact['fax'])?$arr_contact['fax']:'';
        $arr_save['phone'] = isset($arr_contact['company_phone'])?$arr_contact['company_phone']:'';
        $arr_save['contact_address']['0']['contact_address_1'] = isset($arr_contact['addresses']['0']['address_1'])?$arr_contact['addresses']['0']['address_1']:'';
        $arr_save['contact_address']['0']['contact_address_2'] = isset($arr_contact['addresses']['0']['address_2'])?$arr_contact['addresses']['0']['address_2']:'';
        $arr_save['contact_address']['0']['contact_address_3'] = isset($arr_contact['addresses']['0']['address_3'])?$arr_contact['addresses']['0']['address_3']:'';
        $arr_save['contact_address']['0']['contact_town_city'] = isset($arr_contact['addresses']['0']['town_city'])?$arr_contact['addresses']['0']['town_city']:'';
        $arr_save['contact_address']['0']['contact_province_state'] = isset($arr_contact['addresses']['0']['province_state'])?$arr_contact['addresses']['0']['province_state']:'';
        $arr_save['contact_address']['0']['contact_zip_postcode'] = isset($arr_contact['addresses']['0']['zip_postcode'])?$arr_contact['addresses']['0']['zip_postcode']:'';
        if($this->Communication->save($arr_save)){
            $this->redirect('/communications/entry/'.$this->Communication->mongo_id_after_save);
        }
        $this->redirect('communications/entry');
    }
    function create_letter($contact_id = ''){
        if($contact_id == '')$contact_id = $this->get_id();
        $this->selectModel('Contact');
        $arr_contact = $this->Contact->select_one(array('_id'=>new MongoId($contact_id)));
        $arr_save = array();
        $this->selectModel('Communication');
        $arr_save['code'] = $this->Communication->get_auto_code('code');
        $arr_save['comms_type'] = 'Letter';
        $arr_save['sign_off'] = 'Regards';
        $arr_save['company_id'] = isset($arr_contact['company_id'])?$arr_contact['company_id']:'';
        $arr_save['company_name'] = isset($arr_contact['company'])?$arr_contact['company']:'';
        $arr_save['email'] = isset($arr_contact['email'])?$arr_contact['email']:'';
        $arr_save['module'] = isset($this->params->params['controller'])?$this->params->params['controller']:'';
        $arr_save['contact_name'] = isset($arr_contact['first_name'])?$arr_contact['first_name']:'';
        $arr_save['last_name'] = isset($arr_contact['last_name'])?$arr_contact['last_name']:'';
        $arr_save['position'] = isset($arr_contact['position'])?$arr_contact['position']:'';
        $arr_save['comms_date'] = new MongoDate();
        $arr_save['fax'] = isset($arr_contact['fax'])?$arr_contact['fax']:'';
        $arr_save['phone'] = isset($arr_contact['company_phone'])?$arr_contact['company_phone']:'';
        $arr_save['contact_address']['0']['contact_address_1'] = isset($arr_contact['addresses']['0']['address_1'])?$arr_contact['addresses']['0']['address_1']:'';
        $arr_save['contact_address']['0']['contact_address_2'] = isset($arr_contact['addresses']['0']['address_2'])?$arr_contact['addresses']['0']['address_2']:'';
        $arr_save['contact_address']['0']['contact_address_3'] = isset($arr_contact['addresses']['0']['address_3'])?$arr_contact['addresses']['0']['address_3']:'';
        $arr_save['contact_address']['0']['contact_town_city'] = isset($arr_contact['addresses']['0']['town_city'])?$arr_contact['addresses']['0']['town_city']:'';
        $arr_save['contact_address']['0']['contact_province_state'] = isset($arr_contact['addresses']['0']['province_state'])?$arr_contact['addresses']['0']['province_state']:'';
        $arr_save['contact_address']['0']['contact_zip_postcode'] = isset($arr_contact['addresses']['0']['zip_postcode'])?$arr_contact['addresses']['0']['zip_postcode']:'';
        if($this->Communication->save($arr_save)){
            $this->redirect('/communications/entry/'.$this->Communication->mongo_id_after_save);
        }
        $this->redirect('communications/entry');
    }

    function salesinvoice_add($contact_id){
        $this->selectModel('Contact');
        $data = $this->Contact->select_one(array('_id'=>new MongoId($contact_id)));
        $arr_save['contact_name'] =  $data['full_name'];
        $arr_save['contact_id'] = $data['_id'];
        $arr_save['phone'] = isset($data['company_phone'])?$data['company_phone']:'';
        $arr_save['email'] = isset($data['email'])?$data['email']:'';

        $this->selectModel('Salesaccount');
        $salesaccount = $this->Salesaccount->select_one(array('contact_id' => new MongoId($contact_id)));
        if( isset($salesaccount['_id']) ){
            $arr_save['payment_terms'] = $salesaccount['payment_terms'];
            $arr_save['tax'] = $salesaccount['tax_code'];
        }

        $this->selectModel('Salesinvoice');
        $this->Salesinvoice->arr_default_before_save = $arr_save;
        if($id = $this->Salesinvoice->add()){
            if($this->request->is('ajax')){
                echo URL.'/salesinvoices/entry/' .$id;
                die;
            }else{
                $this->redirect('/salesinvoices/entry/'.$id);
                die;
            }
        }
    }

    function buildWorkingHour(){
        $this->selectModel('Contact');
        $arr_contacts = $this->Contact->select_all(array(
                                                  'arr_where' => array('is_employee' => 1),
                                                  'arr_field' => array('is_employee','working_hour','last_name')
                                                  ));
        $arr_contact = array();
        for ($k=0; $k < 7; $k++) {
            $arr_contact['working_hour'][$k]['time1'] = '08:00';
            $arr_contact['working_hour'][$k]['time2'] = '18:00';
            $arr_contact['working_hour'][$k]['time3'] = '08:00';
            $arr_contact['working_hour'][$k]['time4'] = '18:00';
            $arr_contact['working_hour'][$k]['time5'] = '08:00';
            $arr_contact['working_hour'][$k]['time6'] = '18:00';
            $arr_contact['working_hour'][$k]['time7'] = '08:00';
            $arr_contact['working_hour'][$k]['time8'] = '18:00';
            $arr_contact['working_hour'][$k]['lunch1'] = '';
            $arr_contact['working_hour'][$k]['lunch2'] = '';
            $arr_contact['working_hour'][$k]['lunch3'] = '';
            $arr_contact['working_hour'][$k]['lunch4'] = '';

        }
        foreach($arr_contacts as $key => $contact){
            $arr_data = array('_id' => new MongoId($contact['_id']));
            $arr_data['working_hour'] = $arr_contact['working_hour'];
            $this->opm->rebuild_collection($arr_data);

        }
        die;
    }





    function build_employee_commission(){
        $this->selectModel('Contact');
        $contacts = $this->Contact->select_all(array(
                                   'arr_where' => array('is_employee' =>1),
                                   'arr_field' => array('commission')
                                   ));
        echo $contacts->count().' records found(s)<br />';
        $i = 0;
        foreach($contacts as $contact){
            if(isset($contact['commission']) && $contact['commission']) continue;
            $contact['commission'] = 1;
            $this->Contact->rebuild_collection($contact);
            $i++;
        }
        echo $i.' record fixed.';
        die;
    }

    public function lists(){
        $this->selectModel('Contact');
        $limit = LIST_LIMIT;
        $skip = 0;
        $sort_field = '_id';
        $sort_type = 1;
        if( isset($_POST['sort']) && strlen($_POST['sort']['field']) > 0 ){
            if( $_POST['sort']['type'] == 'desc' ){
                $sort_type = -1;
            }
            $sort_field = $_POST['sort']['field'];
            $this->Session->write('Contacts_lists_search_sort', array($sort_field, $sort_type));

        }elseif( $this->Session->check('Contacts_lists_search_sort') ){
            $session_sort = $this->Session->read('Contacts_lists_search_sort');
            $sort_field = $session_sort[0];
            $sort_type = $session_sort[1];
        }
        $arr_order = array($sort_field => $sort_type);
        $this->set('sort_field', $sort_field);
        $this->set('sort_type', ($sort_type === 1)?'asc':'desc');

        // dùng cho điều kiện
        $cond = $where_query = $this->arr_search_where();
        if($this->Session->check('contacts_entry_search_cond')){
            $extra_cond = $this->Session->read('contacts_entry_search_cond');
            if(is_array($extra_cond))
                $cond = array_merge($cond,$extra_cond);
        }
        // dùng cho phân trang
        $page_num = 1;
        if( isset($_POST['pagination']) && $_POST['pagination']['page-num'] > 0){
            $page_num = $_POST['pagination']['page-num'];
            $limit = $_POST['pagination']['page-list'];
            $skip = $limit*($page_num - 1);
        }
        $this->set('page_num', $page_num);
        $this->set('limit', $limit);

        // query
        $arr_contacts = $this->Contact->select_all(array(
            'arr_where' => $cond,
            'arr_order' => $arr_order,
            'arr_field' => array('title','first_name','last_name','type','direct_dial','mobile','home_phone','company','company_id','email','no'),
            'limit' => $limit,
            'skip' => $skip
        ));
        $this->set('arr_contacts', $arr_contacts);


        $total_page = $total_record = $total_current = 0;
        if( is_object($arr_contacts) ){
            $total_current = $arr_contacts->count(true);
            $total_record = $arr_contacts->count();
            if( $total_record%$limit != 0 ){
                $total_page = floor($total_record/$limit) + 1;
            }else{
                $total_page = $total_record/$limit;
            }
        }
        $this->set('total_current', $total_current);
        $this->set('total_page', $total_page);
        $this->set('total_record', $total_record);

        if ($this->request->is('ajax')) {
            $this->render('lists_ajax');
        }
        $this->set('sum', $total_record);
    }

    function encrypt_password(){
        $contacts = $this->opm->select_all(array(
                'arr_where' => array(
                        'is_employee' => 1
                    ),
                'arr_field' => array('password')
            ));
        echo $contacts->count().' employees found.<br />';
        $i = 0;
        foreach($contacts as $contact){
            $contact['password'] = md5($contact['password'].(string)$contact['_id']);
            $this->opm->rebuild_collection($contact);
            $i++;
        }
        echo $i.' employees done.';
        die;
    }

    function set_color() {
        $this->selectModel('Contact');
        $arr_employees = $this->Contact->select_all(array(
            'arr_where' => array('is_employee' => 1),
            'arr_field' => array('_id'),
            'arr_order' => array('first_name' => 1),
        ));
        foreach($arr_employees as $employee){
            $employee['color'] = $this->rand_color();
            $this->Contact->rebuild_collection($employee);
        }
        die;
    }

    function rand_color() {
        return '#' . str_pad(dechex(mt_rand(0, 0xFFFFFF)), 6, '0', STR_PAD_LEFT);
    }

    function rebuild_permission() {
        $this->selectModel('Permission');
        $permissions = $this->Permission->select_all(array(
                        'arr_field' => array('controller','option_list'),
                        ));
        echo $permissions->count().'<br />';
        foreach($permissions as $permission) {
            if(!isset($permission['option_list']) || empty($permission['option_list'])) continue;
            foreach($permission['option_list'] as $key_options=>$options){
                foreach($options as $key=>$value){
                    foreach($value as $k=>$v){
                        $permission['option_list'][$key_options][$key][$k]['belong_to'] = array(
                                $permission['controller'].'_@_entry_@_view',
                            );
                    }
                }
            }
            $this->Permission->save( $permission );
        }
        die;
    }

    function rebuild_user_role(){
        $this->selectModel('Contact');
        $contacts = $this->Contact->select_all(array(
            'arr_where' => array('is_employee' => 1, 'roles.roles' => array('$exists' => true)),
            'arr_field' => array('roles')
            ));
        echo $contacts->count().' found<br />';
        $i = 0;
        foreach($contacts as $contact){
            if(!isset($contact['roles']['roles'])) continue;
            $contact['roles']['roles'] = array_values($contact['roles']['roles']);
            $this->Contact->rebuild_collection($contact);
            $i++;
        }
        echo $i;die;
    }


    function rebuild_working_hours_future(){
        $this->selectModel('WorkingHour');
        $now_ym = (int)date('Ym');
        $now_day = (int)date('d');
        $working_hour = $this->WorkingHour->select_all(array(
            'arr_where' => array('year_month' => array('$gte'=>$now_ym))
            ));
        echo $working_hour->count().' found<br />';
        $m = 0;
        foreach($working_hour as $wh){
            echo '==============================='.$wh['year_month'].'<br />';
            $nowmonth = false;
            if($wh['year_month']==$now_ym)
                $nowmonth = true;
            for($i=1;$i<32;$i++) {
                if(isset($wh['day_'.$i]) && (($nowmonth && $i>$now_day) || !$nowmonth)){
                    echo 'day_'.$i.'---------------<br />';
                    pr($wh['day_'.$i]);
                    //reset
                    $wh['day_'.$i]['work_start'] = '';
                    $wh['day_'.$i]['work_end'] = '';
                    $wh['day_'.$i]['lunch'] = 0;
                    $wh['day_'.$i]['off'] = 0;
                    $wh['day_'.$i]['purpose'] = '';
                    $m++;
                }
            }
            $this->WorkingHour->save($wh);
        }
        echo '<br />'.$m; die;
    }


    function check_password(){
        $this->selectModel('Stuffs');
        $change = $this->Stuffs->select_one(array('value' => 'Changing Code'));
        $wrong = true;
        if(md5($_POST['password']) == $change['password']){
            $wrong = false;
        }
        if(!$wrong){
            echo 'success';
            $this->Session->write('contact_pemission_change_hours',1);
        } else {
            echo 'wrong_pass';
        }
        die;
    }

    function check_exists()
    {
         $arrDuplicate = $this->opm->collection->aggregate(
                array(
                    '$group'=>array(
                                    '_id'=>array(
                                        'email'=> '$email',
                                        'deleted' => '$deleted',
                                    ),
                                    'uniqueIds' => array( '$addToSet' => '$_id' ),
                                    'count' => array( '$sum' =>  1 )
                                )
                ),
                array(
                    '$match'=> array(
                            'count' => array('$gte' => 2),
                        )
                )
            );
        $arrReturn = [];
        $i = 0;
        $arrCompanies = [];
        $this->selectModel('Company');
        foreach($arrDuplicate['result'] as $result){
            if( empty($result['_id']['email']) || $result['_id']['deleted'] ) continue;
            foreach($result['uniqueIds'] as $id){
                $contact = $this->opm->select_one(array('_id' => new MongoId($id)),array('company_id','deleted'));
                $i++;
                $remove = false;
                if(!isset($contact['company_id']) || !is_object($contact['company_id']) ) {
                    $remove = true;
                } else if( !isset($arrCompanies[(string)$contact['company_id']]) ) {
                    $company = $this->Company->select_one(array('_id' => new MongoId($contact['company_id'])),array('_id'));
                    if( !empty($company) ) {
                        $arrCompanies[(string)$company['_id']] = $company['_id'];
                    } else {
                        $remove = true;
                    }
                }
                if( $remove ) {
                    $contact['anvy_login'] = false;
                } else {
                    $contact['anvy_login'] = true;
                }
                $this->opm->save($contact);
            }
        }
        echo $i;
        die;

    }

    function working_hours_employee()
    {
        $this->set('key', 1);
        // Nếu là search theo phân trang
        $arr_order = array('first_name' => 1);
        $this->selectModel('Contact');
        $arr_employees = $this->Contact->select_all(array(
            'arr_where' => array('is_employee' =>1, 'off_report_working_hour' => array('$exists' =>false)),
            'arr_order' => $arr_order,
            'arr_field' => array('first_name', 'last_name','no'),
        ));
        $this->set('arr_employees', $arr_employees);

        $this->layout = 'ajax';
    }

    public function working_hours_report()
    {
        if( !isset($_GET['print_pdf']) ) {
            if( $this->request->is('post') ) {
                $this->selectModel('WorkingHour');
                $arr_where = array();
                if( !empty($_POST['contact_id']) && strlen($_POST['contact_id']) == 24 ) {
                    $arr_where['contact_id'] = new MongoId($_POST['contact_id']);
                }
                if( !empty($_POST['date_equals']) ) {
                    $date = $this->Common->strtotime($_POST['date_equals'] . '00:00:00');
                    $yearFrom = $yearTo = date('Y', $date);
                    $monthFrom = $monthTo =  date('m', $date);
                    $dayFrom = $dayTo =  date('d', $date);
                    $arr_where['year_month'] = (int)($yearFrom.$monthFrom);
                } else {
                    if( !empty($_POST['date_from']) ) {
                        $date = $this->Common->strtotime($_POST['date_from'] . '00:00:00');
                        $yearFrom = date('Y', $date);
                        $monthFrom = date('m', $date);
                        $dayFrom = date('d', $date);
                        $arr_where['year_month']['$gte'] =(int)($yearFrom.$monthFrom);
                    }
                    if( !empty($_POST['date_to']) ) {
                        $date = $this->Common->strtotime($_POST['date_to'] . '00:00:00');
                        $yearTo = date('Y', $date);
                        $monthTo = date('m', $date);
                        $dayTo = date('d', $date);
                        $arr_where['year_month']['$lte'] =  (int)($yearTo.$monthTo);
                    }
                }
                $results = $this->WorkingHour->select_all(array(
                        'arr_where' => $arr_where,
                        'arr_order' => array('year_month' => 1)
                    ));
                if( $this->request->is('ajax') ) {
                    if( !$results->count() ) {
                        echo 'empty';
                    } else{
                        echo 'ok';
                    }
                    die;
                } else {
                    $arr_employees = array();
                    foreach($results as $result) {
                        $arr_employees[(string)$result['contact_id']][] = $result;
                    }
                    $this->selectModel('Contact');
                    $html = '';
                    $count = count($arr_employees);
                    $i = 0;
                    foreach($arr_employees as $employee_id => $data) {
                        $contact = $this->Contact->select_one(array('_id' => new MongoId($employee_id)),array('first_name', 'last_name', 'phone', 'email'));
                        $html .= '
                            <table class="table_header">
                               <tbody>
                                  <tr class="tr_right_none" style="background: #911b12;color: white;height: 29px;line-height: 29px;font-weight: bold;">
                                     <td>
                                        ' . ( (isset($contact['first_name']) ? $contact['first_name'] : '').' '.(isset($contact['last_name']) ? $contact['last_name'] : '') ). '
                                     </td>
                                     <td width="15%">
                                        ' . (isset($contact['phone']) ? $contact['phone'] : '') . '
                                     </td>
                                     <td width="20%">
                                        ' . (isset($contact['email']) ? $contact['email'] : '') . '
                                     </td>
                                  </tr>
                               </tbody>
                            </table>';
                        foreach($data as $value) {
                            $m = substr($value['year_month'], 4);
                            $y = substr($value['year_month'], 0, 4);
                            $running_day = date('w',mktime(0,0,0,$m,1,$y));
                            $days_in_month = date('t',mktime(0,0,0,$m,1,$y));
                            $days_in_this_week = 1;
                            $day_counter = 0;
                            $calendar = '<tr class="calendar-row">';
                            if( $running_day ) {
                                $last_month_date = date('d', strtotime('last day of previous month'));
                                $ld = $last_month_date - $running_day + 1;
                                for($x = 0; $x < $running_day; $x++) {
                                    $calendar.= '<td class="calendar-day">
                                                    <div class="day-number blank_date">
                                                        '.$ld.'
                                                        <div></div>
                                                    </div>
                                                </td>';
                                    $days_in_this_week++;
                                    $ld++;
                                }
                            }
                            for($list_day = 1; $list_day <= $days_in_month; $list_day++){
                                $day_info = '';
                                if( !in_array($days_in_this_week, array( 1, 7)) && isset($value['day_'.$list_day]) ) {
                                    $day = $value['day_'.$list_day];
                                    //WorkEnd
                                    $workEnd = $day['work_end'];
                                    if (!empty($day['work_end'])) {
                                        list($hour, $minute) = explode(':', $day['work_end']);
                                        $hour = (int) $hour;
                                        $minute = (int) $minute;
                                        if ($minute > 0 && $minute < 30) {
                                            $minute = 0;
                                        } else if ($minute > 31) {
                                            $minute = 30;
                                        }
                                        $workEnd = str_pad($hour, 2, '0', STR_PAD_LEFT).':'.str_pad($minute, 2, '0', STR_PAD_LEFT);
                                    }
                                    //WorkStart
                                    $workStart = $day['work_start'];
                                    if (!empty($day['work_start'])) {
                                        list($hour, $minute) = explode(':', $day['work_start']);
                                        $hour = (int) $hour;
                                        $minute = (int) $minute;
                                        if ($minute > 0 && $minute < 30) {
                                            $minute = 30;
                                        } else if ($minute > 31) {
                                            $minute = 0;
                                            $hour++;
                                        }
                                        $workStart = str_pad($hour, 2, '0', STR_PAD_LEFT).':'.str_pad($minute, 2, '0', STR_PAD_LEFT);
                                    }
                                    $work_end = strtotime('10-10-2014 '.$workEnd.':00');
                                    $work_start = strtotime('10-10-2014 '.$workStart.':00');
                                    $work_hour = $this->sec2hour((float)$work_end - (float)$work_start - (float)$day['lunch'] * 3600);
                                    $day_info = "<p>{$work_hour}</p>";
                                }
                                $calendar.= '<td class="calendar-day">';
                                    /* add in the day number */
                                    $class = '';
                                    if( isset($dayTo) &&
                                        ($y > $yearTo ||
                                            ($y == $yearTo
                                                && ( $m > $monthTo || ($m == $monthTo && $list_day > $dayTo) )
                                                )
                                            )
                                        ){
                                        $class = 'blank_date';
                                        $day_info = '';
                                    } else if( isset($dayFrom) &&
                                        ($y < $yearFrom ||
                                            ($y == $yearFrom
                                                && ( $m < $monthFrom || ($m == $monthFrom && $list_day < $dayFrom) )
                                                )
                                            )
                                        ){
                                        $class = 'blank_date';
                                        $day_info = '';
                                    }
                                    $calendar.= '<div class="day-number '.$class.'">
                                                    '.$list_day.'
                                                    <div>
                                                        '.$day_info.'
                                                    </div>
                                                </div>';

                                $calendar.= '</td>';
                                if($running_day == 6) {
                                    $calendar.= '</tr>';
                                    if(($day_counter+1) != $days_in_month) {
                                        $calendar.= '<tr class="calendar-row">';
                                    }
                                    $running_day = -1;
                                    $days_in_this_week = 0;
                                }
                                $days_in_this_week++; $running_day++; $day_counter++;
                            }
                            if($days_in_this_week < 8){
                                for($x = 1; $x <= (8 - $days_in_this_week); $x++) {
                                    $calendar.= '<td class="calendar-day-np"> </td>';
                                }
                            }
                            $calendar.= '</tr>';
                            $html .= '<table class="table_calendar_content">
                                           <tbody>
                                             <tr class="tr_right_none text_center" style=" background-color: #949494;color: white;height: 29px;line-height: 29px;font-weight: bold;">
                                                <td  colspan="7">
                                                   '.$m.'-'.$y.'
                                                </td>
                                             </tr>
                                             <tr style=" background-color:rgb(170, 218, 224);color: white;">
                                                <td>Su</td>
                                                <td>Mo</td>
                                                <td>Tu</td>
                                                <td>We</td>
                                                <td>Th</td>
                                                <td>Fr</td>
                                                <td>Sa</td>
                                             </tr>
                                             '.$calendar.'
                                            </tbody>
                                    </table>';
                        }

                        $html .= '<br />
                                <br />';
                        if( $count != $i + 1 ) {
                            $html .= '<div class="no_print" style="padding-bottom: 100px;border-top: 1px solid;clear: both;"></div>
                                        <div style="margin-bottom: 10px; page-break-after:always;"></div>';
                        }
                        $i++;
                    }

                    if( !empty($_POST['heading']) )
                        $arr_data['report_heading'] = $_POST['heading'];
                    $arr_data['date_from_to'] = '';
                    if(isset($_POST['date_from'])&&$_POST['date_from']!='')
                        $arr_data['date_from_to'] .= '<span class="color_red bold_text">From</span> '.$_POST['date_from'].' ';
                    if(isset($_POST['date_to'])&&$_POST['date_to']!='')
                        $arr_data['date_from_to'] .= ' <span class="color_red bold_text">To</span> '.$_POST['date_to'];
                    if(isset($_POST['date_equals'])&&$_POST['date_equals']!='')
                        $arr_data['date_from_to'] .= $_POST['date_equals'];
                    $arr_data['content'][]['html'] = $html;
                    $arr_data['is_custom'] = true;
                    $arr_data['image_logo'] = true;
                    $arr_data['report_name'] = 'Working Hour (Detailed)';
                    $arr_data['report_file_name'] = 'WorkingHour';
                    $arr_data['custom_css'] = '
                                .day-number > div {
                                    float: right; font-size: 10px !important;
                                }
                                .day-number > div > p {
                                    color: #66f; margin-top:-5px;
                                }
                                .blank_date, .blank_date > div > p {
                                    color: rgb(213, 208, 208) !important;
                                }
                                .calendar-row td{
                                    border-top: 1px solid #c2c2c2;
                                }
                                .day-number{
                                    font-size: 12px;
                                    margin:-top:5px;
                                }
                                .table_calendar_content{
                                    width: 48%;
                                    margin:1%;
                                    float:left;
                                    height: 235px;
                                    border: 1px solid #c2c2c2;
                                    page-break-inside:avoid;
                                    page-break-after:auto;
                                }
                                .table_calendar_content tr:nth-child(n+3) td:first-child,
                                .table_calendar_content tr:nth-child(n+3) td:last-child
                                {
                                    color: rgb(213, 208, 208);
                                }

                                .table_calendar_content tbody tr td {
                                  vertical-align: middle;
                                  border-right: 1px solid #E5E4E3;
                                  padding: 1px 0.6%; }

                                .table_header{
                                    width:100%;
                                }
                                .table_header tr td {
                                    padding:5px;
                                    text-align: right;
                                }
                                .table_header tr td:first-child {
                                    text-align: left;
                                }';
                    Cache::write('working_hour_report', $arr_data);
                    $this->render_pdf($arr_data);
                }
            }
        } else {
            $arr_data = Cache::read('working_hour_report');
            Cache::delete('working_hour_report');
            $this->render_pdf($arr_data);
        }
    }


    function sec2hour($time){
        $hours = floor($time / 3600);
        $mins = floor(($time - ($hours*3600)) / 60);
        $hours = str_pad($hours, 2, '0', STR_PAD_LEFT);
        $mins = str_pad($mins, 2, '0', STR_PAD_LEFT);
        return "$hours:$mins";
    }


    public function clients()
    {
        $arrClients = array();
        $this->selectModel('Salesorder');
        $contacts = $this->Salesorder->collection->group(
                                                array('contact_id' => 1),
                                                array('orders' => array()),
                                                'function (obj, prev) { prev.orders.push({  "salesorder_date": obj.salesorder_date, "salesorder_code": obj.code, "salesorder_id": obj._id}); }',
                                                array(
                                                    'condition' => array(
                                                            'deleted' => false,
                                                            'contact_id' => array('$nin' => array(null, '')),
                                                            'salesorder_date' => array(
                                                                    '$gte' => new MongoDate( strtotime( (new DateTime('-3 years'))->format('Y-m-d 00:00:00') ) )
                                                                )
                                                        )
                                                    ));
        if( isset($contacts['ok']) && $contacts['count'] ) {
            $this->selectModel('Company');
            $arrCompanies = array();
            foreach($contacts['retval'] as $contact) {
                if( empty($contact['orders']) || !is_object($contact['contact_id']) ) {
                    continue;
                }
                $client = $this->opm->select_one(array('_id' => $contact['contact_id'], 'inactive' => 0, 'is_employee' => 0), array('first_name', 'last_name', 'position', 'title', 'company_id', 'email'));
                if( empty($client) ) {
                    continue;
                }
                $client = array_merge(array('company_id' => '', 'first_name' => '', 'last_name' => '', 'position' => '', 'title' => '', 'email' => ''),$client);
                if( isset($client['company_id']) && is_object($client['company_id']) ) {
                    if( !isset($arrCompanies[ (string)$client['company_id'] ]) ) {
                        $company = $this->Company->select_one(array('_id' => $client['company_id']), array('name'));
                        $client['company_name'] = $arrCompanies[ (string)$client['company_id'] ] =  isset($company['name']) ? $company['name'] : '';
                    } else {
                        $client['company_name'] = $arrCompanies[ (string)$client['company_id'] ];
                    }
                }
                usort($contact['orders'], function($a, $b) {
                    return $a['salesorder_date']->sec < $b['salesorder_date']->sec;
                });
                $client['order_code'] = $contact['orders'][0]['salesorder_code'];
                $client['order_date'] = $contact['orders'][0]['salesorder_date'];
                $client['order_id'] = $contact['orders'][0]['salesorder_id'];
                $arrClients[] = $client;
            }
        }
        if( empty($arrClients) ) {
            echo 'No record';
            die;
        }
        usort($arrClients, function($a, $b) {
            return $a['order_date']->sec < $b['order_date']->sec;
        });
        App::import('Vendor', 'phpexcel/PHPExcel');
        $objPHPExcel = new PHPExcel();
        // Set document properties
        $objPHPExcel->getProperties()->setCreator('')
                                     ->setLastModifiedBy('')
                                     ->setTitle('Contact - Active Clients')
                                     ->setSubject('Contact - Active Clients')
                                     ->setDescription('Contact - Active Clients')
                                     ->setKeywords('Contact - Active Clients')
                                     ->setCategory('Contact - Active Clients');
        $worksheet = $objPHPExcel->getActiveSheet();
        $worksheet->setCellValue('A1', '#')
                    ->setCellValue('B1', 'Title')
                    ->setCellValue('C1', 'First Name')
                    ->setCellValue('D1', 'Last Name')
                    ->setCellValue('E1', 'Company')
                    ->setCellValue('F1', 'Position')
                    ->setCellValue('G1', 'Email')
                    ->setCellValue('H1', 'Order')
                    ->setCellValue('H2', 'Code')
                    ->setCellValue('I2', 'Date');
        foreach (range('A', 'G') as $char) {
            $worksheet->mergeCells("{$char}1:{$char}2");
        }
        $worksheet->mergeCells('H1:I1');
        $i = 3;
        foreach($arrClients as $client){
            $worksheet->setCellValue('A'.$i, ($i-2))
                        ->setCellValue('B'.$i, $client['title'])
                        ->setCellValue('C'.$i, $client['first_name'])
                        ->setCellValue('D'.$i, $client['last_name'])
                        ->setCellValue('E'.$i, $client['company_name'])
                        ->setCellValue('F'.$i, $client['position'])
                        ->setCellValue('G'.$i, $client['email'])
                        ->setCellValue('H'.$i, $client['order_code'])
                        ->setCellValue('I'.$i, date('d M, Y', $client['order_date']->sec));
            $worksheet->getCell('H'.$i)
                        ->setDataType(PHPExcel_Cell_DataType::TYPE_STRING2)
                        ->getHyperlink()
                        ->setUrl(URL.'/salesorders/entry/'.$client['order_id']);
            $i++;
        }
        $worksheet->setCellValue('A'.$i, ($i-3).' record(s) listed');
        $worksheet->mergeCells("A$i:C$i");
        $worksheet->getRowDimension(8)->setRowHeight(-1);
        $worksheet->getStyle('A1:I2')->getFont()->setBold(true);
        $worksheet->getStyle('H1')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        $worksheet->getStyle('A1:I2')->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
        $styleArray = array(
                'borders' => array(
                   'allborders' => array(
                       'style' => PHPExcel_Style_Border::BORDER_THIN
                    )
                ),
                'font'  => array(
                    'size'  => 11,
                    'name'  => 'Century Gothic'
                )
        );
        $worksheet->getStyle('A1:J'.($i-1))->applyFromArray($styleArray);
        for($i = 'A'; $i !== 'J'; $i++){
            $worksheet->getColumnDimension($i)
                            ->setAutoSize(true);
        }
        $objPHPExcel->setActiveSheetIndex(0);
        $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
        $objWriter->save(APP.DS.'webroot'.DS.'upload'.DS.'Contact_Clients.xlsx');
        $this->redirect('/upload/Contact_Clients.xlsx');
        die;
    }

    public function mobile_rebuild()
    {
        $contacts = $this->opm->select_all(array(
                'arr_fields' => array('mobile')
            ));
        $i = 0;
        foreach ($contacts as $contact) {
            $contact = array_merge(array('mobile' => ''), $contact);
            $this->opm->collection->update(
                                        array( '_id' => $contact['_id']),
                                        array(
                                            '$set' => array (
                                                    'mobile_login' => preg_replace( '/[^0-9]/', '', $contact['mobile'] )
                                                )
                                            )
                                    );
            $i++;
        }
        echo $i;die;
    }



}