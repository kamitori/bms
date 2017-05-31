<?php
App::uses('AppController', 'Controller');
class LocationsController extends AppController {

	var $name = 'Locations';
    var $modelName = 'Location';
	public $helpers = array();
	public $opm; //Option Module
	public function beforeFilter(){
		parent::beforeFilter();
		$this->set_module_before_filter('Location');
		$this->sub_tab_default = 'general';
	}


	public function rebuild_setting($arr_setting=array()){
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


	//Các điều kiện mở/khóa field trong entry
    public function check_lock() {

            return false;
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
            $this->redirect(URL.'/locations/add');
        }

        $this->set('arr_settings', $arr_set);
        $this->sub_tab_default = 'general';
        $this->sub_tab('', $iditem);

        $this->set_entry_address($arr_tmp, $arr_set);
    }


    //address
    public function set_entry_address($arr_tmp, $arr_set) {
        $address_fset = array('address_1', 'address_2', 'address_3', 'town_city', 'country', 'province_state', 'zip_postcode');
        $address_value = $address_province_id = $address_country_id = $address_province = $address_country = array();
        $address_controller = array('shipping');
        $this->set('address_controller', $address_controller); //set
        $address_key = array('shipping');
        $this->set('address_key', $address_key); //set
        $address_country = $this->country();
        foreach ($address_key as $kss => $vss) {
            //neu ton tai address trong data base
            if (isset($arr_tmp[$vss . '_address'][0])) {
                $arr_temp_op = $arr_tmp[$vss . '_address'][0];
                for ($i = 0; $i < count($address_fset); $i++) { //loop field and set value for display
                    if (isset($arr_temp_op[$vss . '_' . $address_fset[$i]])) {
                        $address_value[$vss][$i] = $arr_temp_op[$vss . '_' . $address_fset[$i]];
                    } else {
                        $address_value[$vss][$i] = '';
                    }
                }//pr($arr_temp_op);die;
                //get province list and country list

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
                //chua co address trong data
            } else {
                $address_country_id[$kss] = "CA";
                $address_province[$vss] = $this->province("CA");
                $address_add[$vss] = '1';
            }
        }
        //pr($address_province);
        $this->set('address_value', $address_value);
        $address_hidden_field = array('shipping_address');
        $this->set('address_hidden_field', $address_hidden_field); //set
        $address_label = array('Shipping address');
        $this->set('address_label', $address_label); //set
        $address_conner[0]['top'] = 'hgt';
        $address_conner[0]['bottom'] = 'fixbor3 jt_ppbot';

        $this->set('address_conner', $address_conner); //set
        $this->set('address_country', $address_country); //set
        $this->set('address_country_id', $address_country_id); //set
        $this->set('address_province', $address_province); //set
        $this->set('address_province_id', $address_province_id); //set

        $this->set('address_value', $address_value);
        $address_hidden_field = array('shipping_address');
        $this->set('address_hidden_field', $address_hidden_field); //set
        $address_label[0] = $arr_set['field']['panel_3']['shipping_address']['name'];
        $this->set('address_label', $address_label); //set
        $address_conner[0]['top'] = 'hgt';
        $address_conner[0]['bottom'] = 'fixbor3 jt_ppbot';
        $this->set('address_conner', $address_conner); //set
        $address_country = $this->country();
        $this->set('address_country', $address_country); //set
        $this->set('address_province', $address_province); //set
        $this->set('address_more_line', 1); //set
        $this->set('address_onchange', "save_address_pr('\"+keys+\"');");
        if (isset($arr_tmp['ship_to_company_id']) && strlen($arr_tmp['ship_to_company_id']) == 24)
            $this->set('address_company_id', 'ship_to_company_id');
    }




    public function arr_associated_data($field = '', $value = '', $valueid = '') {
        $arr_return = array();
		$arr_return[$field] = $value;
        /* ---------------------------------------Xử lý chọn combobox company------------------------------------------ */
        if ($field == 'company_name' && $valueid != '') {

            $arr_return = array(
                'company_name' => '',
                'company_id' => '',
                'contact_name' => '',
                'contact_id' => '',
                'our_rep_id' => '',
                'our_rep' => '',
                'phone' => '',
                'email' => '',
                'fax' => '',
            );
            $arr_return['company_name'] = $value;
            $arr_return['company_id'] = new MongoId($valueid);

            $this->selectModel('Company');
            $arr_company = $this->Company->select_one(array('_id' => new MongoId($valueid)));

            $this->selectModel('Contact');
            $arr_contact = $arrtemp = array();

            if (isset($arr_company['contact_default_id']) && is_object($arr_company['contact_default_id'])) {
                $arr_contact = $this->Contact->select_one(array('_id' => $arr_company['contact_default_id']));
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


            if (isset($arr_contact['_id'])) {
                $arr_return['contact_name'] = $arr_contact['first_name'] . ' ' . $arr_contact['last_name'];
                $arr_return['contact_id'] = $arr_contact['_id'];
            } else {
                $arr_return['contact_name'] = '';
                $arr_return['contact_id'] = '';
            }


            if (isset($arr_company['our_rep_id']) && $arr_company['our_rep_id'] != '') {
                $arr_return['our_rep_id'] = $arr_company['our_rep_id'];
                $arr_return['our_rep'] = $arr_company['our_rep'];
            } else {
                $arr_return['our_rep_id'] = $this->opm->user_id();
                $arr_return['our_rep'] = $this->opm->user_name();
            }

            $arr_return['phone'] = '';

            if (isset($arr_company['phone']))
                $arr_return['phone'] = $arr_company['phone'];
            if (isset($arr_contact['direct_dial']) && $arr_contact['direct_dial'] != '')
                $arr_return['phone'] = $arr_contact['direct_dial'];


            $arr_return['email'] = '';
            if (isset($arr_company['email']))
                $arr_return['email'] = $arr_company['email'];
            if (isset($arr_contact['email']) && $arr_contact['email'] != '')
                $arr_return['email'] = $arr_contact['email'];


            $arr_return['fax'] = '';
            if (isset($arr_company['fax']))
                $arr_return['fax'] = $arr_company['fax'];
            if (isset($arr_contact['fax']) && $arr_contact['fax'] != '')
                $arr_return['fax'] = $arr_contact['fax'];

            //change address
            if (isset($arr_company['addresses_default_key']))
                $add_default = $arr_company['addresses_default_key'];
            if (isset($add_default) && isset($arr_company['addresses'][$add_default])) {
                foreach ($arr_company['addresses'][$add_default] as $ka => $va) {
                    if ($ka != 'deleted')
                        $arr_return['invoice_address'][0]['invoice_' . $ka] = $va;
                    else
                        $arr_return['invoice_address'][0][$ka] = $va;
                }
            }
        }
        /* ------------------------------------------------------------------------------------------------------------ */
        /* ---------------------------------------Xử lý chọn combobox contact------------------------------------------ */
//		elseif(){
//
//		}
        /* ------------------------------------------------------------------------------------------------------------ */
        return $arr_return;
    }








	public function entry_search() {
        //parent
        $arr_set = $this->opm->arr_settings;
        $arr_set['field']['panel_1']['code']['lock'] = '';
        $arr_set['field']['panel_1']['our_rep']['not_custom'] = '0';
        $arr_set['field']['panel_1']['our_csr']['not_custom'] = '0';
        $arr_set['field']['panel_1']['stock_usage']['default'] = '';
        $arr_set['field']['panel_4']['purchase_orders_status']['default'] = '';
        $arr_set['field']['panel_4']['payment_terms']['default'] = '';
        $arr_set['field']['panel_4']['job_name']['not_custom'] = '0';
        $arr_set['field']['panel_4']['job_number']['lock'] = '';
        $arr_set['field']['panel_4']['salesorder_name']['not_custom'] = '0';
        $arr_set['field']['panel_4']['salesorder_number']['lock'] = '';
        $arr_set['field']['panel_4']['tax']['default'] = '';

        $this->set('search_class', 'jt_input_search');
        $this->set('search_class2', 'jt_select_search');
        $this->set('search_flat', 'placeholder="1"');
        $where = array();
        if ($this->Session->check($this->name . '_where'))
            $where = $this->Session->read($this->name . '_where');
        if (count($where) > 0) {
            foreach ($arr_set['field'] as $ks => $vls) {
                foreach ($vls as $field => $values) {
                    if (isset($where[$field])) {
                        $arr_set['field'][$ks][$field]['default'] = $where[$field]['values'];
                    }
                }
            }
        }
        //end parent
        $this->set('arr_settings', $arr_set);

        //address
        $address_label = array('Shipping address');
        $this->set('address_label', $address_label);
        $address_conner[0]['top'] = 'hgt';
        $address_conner[0]['bottom'] = 'fixbor3 jt_ppbot';
        $this->set('address_conner', $address_conner);
        $this->set('address_more_line', 1); //set
        $address_hidden_field = array('shipping_address');
        $this->set('address_hidden_field', $address_hidden_field); //set
        $address_country = $this->country();
        $this->set('address_country', $address_country); //set
        $this->set('address_country_id', 'Canada'); //set
        $address_province['invoice'] = $address_province['shipping'] = $this->province("CA");
        $this->set('address_province', ""); //set
        $this->set('address_province_id', ""); //set
        $this->set('address_onchange', "save_address_pr('\"+keys+\"');");
        $address_hidden_value = array('');
        $this->set('address_hidden_value', $address_hidden_value);
        $this->set('address_mode', 'search');
    }



/*	public function other() {
        $subdatas = array();
        $subdatas['other'] = array();
        $this->set('subdatas', $subdatas);
    }*/


    public function swith_options($option = '') {
        parent::swith_options($option);
        $date = new MongoDate();
        // ------------ Group Find purchar order ------------ //
        // find outstanding
        if ($option == 'outstanding') {
            $or_where = array(
                array('purchase_orders_status' => 'On order'),
                array('purchase_orders_status' => 'In progress'),
                array('purchase_orders_status' => 'Partly Received')
            );
            $arr_where = array();
            $arr_where[] = array('values' => $or_where, 'operator' => 'or');
            $this->Session->write($this->name . '_where', $arr_where);
            echo URL . '/' . $this->params->params['controller'] . '/lists';
        }
        // find on order
        elseif ($option == 'on_order') {
            $arr_where = array();
            $arr_where['purchase_orders_status'] = array('values' => 'On order', 'operator' => '=');
            $this->Session->write($this->name . '_where', $arr_where);
            echo URL . '/' . $this->params->params['controller'] . '/lists';
        }
        // find received
        if ($option == 'received') {
            $arr_where = array();
            $arr_where['purchase_orders_status'] = array('values' => 'Received', 'operator' => '=');
            $this->Session->write($this->name . '_where', $arr_where);
            echo URL . '/' . $this->params->params['controller'] . '/lists';
        }

        if ($option == 'cancelled') {
            $arr_where = array();
            $arr_where['purchase_orders_status'] = array('values' => 'Cancelled', 'operator' => '=');
            $this->Session->write($this->name . '_where', $arr_where);
            echo URL . '/' . $this->params->params['controller'] . '/lists';
        }
        //  find late
        if ($option == 'late') {
            $arr_where = array();
            $arr_where['required_date'] = array('values' => $date, 'operator' => '<');
            $this->Session->write($this->name . '_where', $arr_where);
            echo URL . '/' . $this->params->params['controller'] . '/lists';
        }
        // ------------ end group find purchar order ------------ //

        if ($option == 'print_purchase_order') {
            echo URL . '/' . $this->params->params['controller'] . '/view_pdf';
        }

        if ($option == 'print_mini_list') {
            echo URL . '/' . $this->params->params['controller'] . '/print_mini_list';
        }

        // pdf report supplier detailed
        if ($option == 'report_by_supplier_detailed') {
            echo URL . '/' . $this->params->params['controller'] . '/report_supplier_detail';
        }
        // ------------ End For the found set of ------------ //
        die();
    }


    public function general(){
        $tmp = 0;
        $cat_select = array();
        $stockcurrent = array();
        $total = 0;
        $total_cost = 0;
        $total_sell = 0;
        $total_profit = 0;
        $total_onso = 0;
        $module_id = $this->get_id();
       // $this->selectModel('Location');
        if (isset($module_id)) {
            $arr_set = $this->opm->arr_settings;
            $fieldlist = $arr_set['relationship']['general']['block']['stockcurrent']['field'];
            $prokey = array('total_stock');
            $this->selectModel('Product');
            $arr_query = $this->Product->select_all(array(
                'arr_where' => array(
                    'locations' => array(
                        '$elemMatch' => array(
                            'deleted' => false,
                            'location_id' => new MongoId($module_id),
                          )
                    ),
                    'check_stock_stracking' => 1
                ),
                'arr_field' => array('code', 'unit_price', 'sell_price', 'profit', 'stocktakes', 'locations','category','name','sku','oum','oum_depend','product_type','total_stock','min_stock','low'),
                'arr_order' => array('code'=>1),//'category' => 1,
                'limit' => 10000
            ));
            $newdata = $temp = array();
            $newdata = $arr_query;
            $arr_product = iterator_to_array($arr_query);
            //pr($arr_product);
            $arr_so = array();
            $begin_time = strtotime("01-01-2013 00:00:00");
            $arr_so = $this->check_stock($begin_time,'','all_so');
            // pr($arr_so);
            foreach ($newdata as $keys => $values) {
                if(isset($values['cost_price']) && isset($vv['deleted']) && !$vv['deleted'] )
                    $total_cost += $values['cost_price'];
                if(isset($values['sell_price']) && isset($vv['deleted']) && !$vv['deleted'])
                    $total_sell += $values['sell_price'];
                if(isset($values['profit']) && isset($vv['deleted']) && !$vv['deleted'])
                    $total_profit += $values['profit'];

                $stockcurrent[$keys]['status_color'] ='#111111';

                //bo so luong SO completed trong qty_in_stock
                $begin_time = strtotime("01-01-2013 00:00:00"); $f_key =0;
                if(isset($values['stocktakes']) && is_array($values['stocktakes']) && count($values['stocktakes'])>0){
                    $arr_stock = $values['stocktakes'];
                    $sum = count($values['stocktakes']);
                    for($m=$sum-1;$m>=0;$m--){
                        if($f_key == 0 && !$values['stocktakes'][$m]['deleted']){
                            $f_key = $m;
                        }
                    }
                    $begin_time = $values['stocktakes'][$f_key]['stocktakes_date']->sec;
                }

                foreach ($values['locations'] as $key => $vv) {
                    if(isset($vv['location_id']) && $vv['location_id'] == $module_id && isset($vv['deleted']) && !$vv['deleted'] && isset($vv['total_stock'])){
                        $vv['total_stock'] = (float)$vv['total_stock'] - $this->check_stock($begin_time,(string)$values['_id'],'so_completed');
                        $stockcurrent[$keys] = array_merge($values, $vv);
                        if(isset($vv['min_stock']) && (int)$vv['total_stock'] < (int)$vv['min_stock']&&isset($vv['min_stock']) &&isset($vv['total_stock'])){
                            $stockcurrent[$keys]['low'] =1;
                            $stockcurrent[$keys]['xcss'] ='color:red';
                        }
                        if(isset($arr_so[(string)$values['_id']]) && isset($arr_so[(string)$values['_id']]['value']['quantity']))
                            $tmp = $arr_so[(string)$values['_id']]['value']['quantity'];
                        else
                            $tmp = 0;
                        $stockcurrent[$keys]['on_so'] = $tmp;
                        $total_onso += $tmp;

                        
                        $stockcurrent[$keys]['avalible'] =0;
						$stockcurrent[$keys]['_id'] =  $stockcurrent[$keys]['product_id'] = (string)$values['_id'];
                        if(isset($stockcurrent[$keys]['in_use'])){
                            $stockcurrent[$keys]['avalible'] +=(float)$stockcurrent[$keys]['in_use'];
                        }
                        if(isset($stockcurrent[$keys]['in_assembly'])){
                            $stockcurrent[$keys]['avalible'] +=(float)$stockcurrent[$keys]['in_assembly'];
                        }
                        if(isset($stockcurrent[$keys]['on_so'])){
                            $stockcurrent[$keys]['avalible'] +=(float)$stockcurrent[$keys]['on_so'];
                        }
                        if(isset($vv['total_stock']))
                            $total += (float)$vv['total_stock'];

                         $stockcurrent[$keys]['avalible'] = $stockcurrent[$keys]['total_stock'] -  $stockcurrent[$keys]['avalible'];
                    }

                }

            }

            $subdatas['stockcurrent'] = $stockcurrent;
            $this->set('total_stock', $total);
            $this->set('sell_price', $total_sell);
            $this->set('cost_price', $total_cost);
            $this->set('profit', $total_profit);
            $this->set('on_so', $total_onso);
            $this->set('subdatas', $subdatas);
        }

		$this->set_select_data_list('relationship', 'general');

        //die;
    }


	public function bookings() {
        $subdatas = array();
        $subdatas['bookings1'] = array();
        $subdatas['bookings2'] = array();
        $this->set('subdatas', $subdatas);
    }

    public function other() {
        $module_id = $this->get_id();
        //goi ham dung chung cua a.Nam
        $this->communications($module_id, true);
        $sub_tab = array();
        $sub_tab['otherdetails'] = $this->get_option_data('otherdetails');
        //pr($sub_tab['otherdetails']);die;
        $this->set('subdatas', $sub_tab);
    }

	// Popup form orther module
    public function popup($key = '') {
        $this->set('key', $key);
        $limit = 100;
        $skip = 0;
        $cond = array();
        // Nếu là search GET
        if (!empty($_GET)) {

            $tmp = $this->data;

            if (isset($_GET['name'])) {
                $tmp['Location']['name'] = $_GET['name'];
            }

            $this->data = $tmp;
        }

        // Nếu là search theo phân trang
        $page_num = 1;
        if (isset($_POST['pagination']) && $_POST['pagination']['page-num'] > 0) {
            // $limit = $_POST['pagination']['page-list'];
            $page_num = $_POST['pagination']['page-num'];
            $limit = $_POST['pagination']['page-list'];
            $skip = $limit * ($page_num - 1);
        }
        $this->set('page_num', $page_num);
        $this->set('limit', $limit);
        $arr_order = array('first_name' => 1);
        if (isset($_POST['sort']) && strlen($_POST['sort']['field']) > 0) {
            $sort_type = 1;
            if ($_POST['sort']['type'] == 'desc') {
                $sort_type = -1;
            }
            $arr_order = array($_POST['sort']['field'] => $sort_type);

            $this->set('sort_field', $_POST['sort']['field']);
            $this->set('sort_type', ($sort_type === 1) ? 'asc' : 'desc');
            $this->set('sort_type_change', ($sort_type === 1) ? 'desc' : 'asc');
        }

        // search theo submit $_POST kèm điều kiện
        if (!empty($this->data) && !empty($_POST) && isset($this->data['Location'])) {
            $arr_post = $this->data['Location'];

            if (isset($arr_post['name']) && strlen($arr_post['name']) > 0) {
                $cond['name'] = new MongoRegex('/' . trim($arr_post['name']) . '/i');
            }

            if (strlen($arr_post['contact_name']) > 0) {
                $cond['contact_name'] = new MongoRegex('/' . $arr_post['contact_name'] . '/i');
            }
        }

        $this->identity($cond);
        $this->selectModel('Location');
        $arr_location = $this->Location->select_all(array(
            'arr_where' => $cond,
            'arr_order' => $arr_order,
            'limit' => $limit,
            'skip' => $skip
                // 'arr_field' => array('name', 'is_customer', 'is_employee', 'company_id', 'company_name')
        ));
        $this->set('arr_location', $arr_location);

        $total_page = $total_record = $total_current = 0;
        if (is_object($arr_location)) {
            $total_current = $arr_location->count(true);
            $total_record = $arr_location->count();
            if ($total_record % $limit != 0) {
                $total_page = floor($total_record / $limit) + 1;
            } else {
                $total_page = $total_record / $limit;
            }
        }
        $this->set('total_current', $total_current);
        $this->set('total_page', $total_page);
        $this->set('total_record', $total_record);
		$this->set('model', 'Location');

        $this->layout = 'ajax';
    }

//count on_so in sales order
        public function on_so($id_product=''){
        $sum = 0;
        $this->selectModel('Salesorder');
        $data = $this->Salesorder->select_all(array(
            'arr_where' => array('products.products_id' => new MongoId($id_product), 'status' => array('$in'=>array('Submitted','In production')) )
            ));

        $data = iterator_to_array($data);
        $product = array();
        foreach($data as $key => $value){
            foreach($value['products'] as $vv){
                if(!$vv['deleted']){
                    $product[] = $vv['products_id'];
                    $product[] = $vv['quantity'];
                    if(isset($vv['quantity']))
                        $sum += $vv['quantity'];
                }
            }
        }

       //pr($sum);die;
        return $sum;

    }



 /*   public function quantity_low_in_stock(){
        $stockcurrent = array();
        if (isset($module_id)) {
            $arr_set = $this->opm->arr_settings;
            $this->selectModel('Product');
            $arr_query = $this->Product->select_all(array(
                'arr_where' => array(
                    'locations.deleted' => false,
                ),
            ));
            $newdata = $temp = array();
            $newdata = $arr_query;
            foreach ($newdata as $keys => $values) {
                foreach ($values['locations'] as $key => $vv) {
                    if (isset($vv['deleted']) && !$vv['deleted'] && isset($vv['total_stock']) && isset($vv['min_stock']) && (int)$vv['total_stock'] < (int)$vv['min_stock']){
                        $stockcurrent[$keys] = $vv;
                        //$stockcurrent[$keys] = $values['products_id'];
                        //$stockcurrent[$key] = $values['products_name'];
                        $stockcurrent[$key]['low'] = 'LOW';
                }
            }
        }
    }

    die;
    return $stockcurrent;

}*/


    public function quantity_low_in_stock(){
        $stockcurrent = array();
        $module_id = $this->get_id();
        if (isset($module_id)) {
            $arr_set = $this->opm->arr_settings;
            $this->selectModel('Product');
            $arr_query = $this->Product->select_all(array(
                'arr_where' => array(
                    'locations'=> array(
						'$elemMatch'=> array(
							'deleted' => false,
							'min_stock' => array('$ne' => null),
						),
					),
                ),
            ));
            $newdata = $temp = array();
            $newdata = $arr_query;
            foreach ($newdata as $keys => $values) {
                //pr($values['locations']);die;
                foreach ($values['locations'] as $key => $vv) {
                    if(isset($vv['deleted']) && isset($vv['total_stock']) && isset($vv['min_stock']) && !$vv['deleted'] && (int)$vv['total_stock'] < (int)$vv['min_stock'] ){
                        //$stockcurrent[$keys] = array_merge($values, $vv);
                        $kkey = (string)$vv['location_id'].(string) $values['_id'];
                        $stockcurrent[$kkey]['location_name']      =    $vv['location_name'];
                        $stockcurrent[$kkey]['product_name']       =    $values['name'];
                        if($vv['total_stock'] == '')
                            $vv['total_stock'] = 0;
                        $stockcurrent[$kkey]['total_stock']        =      $vv['total_stock'];
                        $stockcurrent[$kkey]['min_stock']          =      $vv['min_stock'];
                }
            }
        }
    }
	//pr($stockcurrent);die;
    return $stockcurrent;
}


    //========================view pdf=============================
    function view_minilist() {
        $this->layout = 'pdf';

        $date_now = date('Ymd');
        $time=time();
        $filename = 'low'.$date_now.$time;
        $html='';

        $tmp = array();
        $tmp = $this->quantity_low_in_stock();
        $i=0;
        foreach($tmp as $key=>$value){

            if($i%2==0)
                $html .= ' <table cellpadding="4" cellspacing="0" class="tab_nd">';
            else
                $html .= '<table cellpadding="4" cellspacing="0" class="tab_nd2">';

            $html .= ' <tr class="border_2">
                <td width="20%" class="first top border_left border_btom">'.$value['location_name'].'</td>
                <td width="20%" class="top border_btom border_left" align="left">'.$value['product_name'].'</td>
                <td width="15%" class="top border_btom border_left " align="center">'.$value['total_stock'].'</td>
                <td align="center" width="10%" class="top border_btom border_left">'.$value['min_stock'].'</td>
                <td align="center" width="11%" class="end top border_btom border_left">Low</td>
                <td align="center" width="24%" class="end top border_btom border_left">       </td>
            </tr>
        </table>
        ';


            $i+=1;
        }
        $html_new = $html;

        // =================================================== tao file PDF ==============================================//
        include(APP.'Vendor'.DS.'nguyenpdf.php');

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
        $pdf->file3 = 'img'.DS.'bar_975x23.png';

        $pdf->file2_left=208;
        $pdf->file2='img'.DS.'LowStock_title.png';


        $pdf->bar_top_left=208;
        $pdf->bar_top_top=23;
        $pdf->bar_top_content='------------------------------------------------------------------';

        $pdf->hidden_left=251;
        $pdf->hidden_top=19;
        $pdf->hidden_content='';

        $pdf->bar_big_content='------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------';
        $pdf->bar_words_content='Location Name                               Product Name                                         Total Stock                  Min                       Low                                          Counted ';
        //      $pdf->bar_mid_content='                                          |                                                    |                            |                         |';
        $pdf->bar_mid_content='';

        $pdf->printedat_left=223;
        $pdf->printedat_top=28;
        $pdf->time_left=241;
        $pdf->time_top=28;

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
        $pdf->AddPage('L', 'A4');
        $pdf->SetMargins(10, 19, 10);

        $pdf->file1 = 'img'.DS.'null.png';
        $pdf->file2 = 'img'.DS.'null.png';
        $pdf->file3_top=10;
        $pdf->bar_words_top=11;
        $pdf->bar_mid_top=10.6;
        $pdf->hidden_content='';
        $pdf->bar_top_content='';
        $pdf->today='';
        $pdf->print='';
        $pdf->address_1='';
        $pdf->address_2='';
        $pdf->bar_big_content='';
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

        <table cellpadding="3" cellspacing="0" class="tab_nd2">
            <tr class="border_2">
                <td width="80%" class="first top border_btom size_font">
                    &nbsp;';

        $html .= $i;

        $html .=' records listed
                </td>
                <td width="20%" class="end top border_btom">
                    &nbsp;
                </td>
            </tr>
        </table>
        <div style=" clear:both; color: #c9c9c9;"><br />
    --------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------
        </div><br />
        ';
        $pdf->writeHTML($html, true, false, true, true, '');
        $pdf->Output(APP. 'webroot'.DS. 'upload'.DS .$filename.'.pdf', 'F');
        $this->redirect('/upload/'. $filename .'.pdf');
        die;
    }

    function clear_stock($location_id){
        $this->selectModel('Product');
        $this->selectModel('Location');
        $arr_product = $this->Product->select_all(array(
                                                  'arr_where' => array('locations.location_id' => new MongoId($location_id), 'deleted' => false),
                                                  'arr_field' => array('locations','qty_in_stock','stocktakes'),
                                                  ));
        foreach($arr_product as $key => $value){
            $arr_new_product = array();
            if(isset($value['locations'])){
                $qty = 0;
                foreach($value['locations'] as $k_locations => $v_locations){
                    if($v_locations['location_id'] == new MongoID($location_id)){
                        if(isset($v_locations['total_stock']))
                            $qty += $v_locations['total_stock'];
                        unset($value['locations'][$k_locations]);
                    }
                }
            }
            if(isset($value['stocktakes'])){
                foreach($value['stocktakes'] as $k_stocktakes => $v_stocktakes){
                    if($v_stocktakes['location_id'] == new MongoID($location_id)){
                        unset($value['stocktakes'][$k_locations]);
                    }
                }
                $arr_new_product['stocktakes'] = $value['stocktakes'];
            }
            $arr_new_product['_id'] = $value['_id'];
            $arr_new_product['locations'] = $value['locations'];
            $arr_new_product['qty_in_stock'] = isset($value['qty_in_stock'])?($value['qty_in_stock'] - $qty):0;
            $this->Product->save($arr_new_product);
        }
        echo 'xong!';
        die;
    }


}