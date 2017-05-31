<?php
App::uses('AppController', 'Controller');
class BatchesController extends AppController {

	var $name = 'Batches';
    var $modelName = 'Batch';
	public $helpers = array();
	public $opm; //Option Module
	public function beforeFilter(){
		parent::beforeFilter();
		$this->set_module_before_filter('Batche');
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


	//Các điều kiện mở/khóa field trong entry
    public function check_lock() {
       /* if ($this->get_id() != '') {
            $arr_tmp = $this->opm->select_one(array('_id' => new MongoId($this->get_id())));
            if ($arr_tmp['inactive'] == 1)
                return true;
        } else*/
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
                //pr($vls);
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
                $item_title['code]'] = '1';
            $this->set('item_title', $item_title);

            //END custom
			$this->set('address_lock', '1');
            //END custom
            //show footer info
            $this->show_footer_info($arr_tmp);


            //add, setup field tự tăng
        }else {
            $this->redirect(URL.'/batches/add');
        }

        $this->set('arr_settings', $arr_set);
        $this->sub_tab_default = 'general';
        $this->sub_tab('', $iditem);
        parent::entry();

        //$this->set_entry_address($arr_tmp, $arr_set);
    }


    //address
   /* public function set_entry_address($arr_tmp, $arr_set) {
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
*/



    public function arr_associated_data($field = '', $value = '', $valueid = '') {
        $arr_return = array();
        $arr_return[$field] = $value;
        if ($field == 'product_name' && $valueid != '') {
            $arr_return['product_id'] = new MongoId($valueid);
         }
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



	public function other() {
        $subdatas = array();
        $subdatas['other'] = array();
        $this->set('subdatas', $subdatas);
    }


    public function swith_options($option = '') {
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




	public function general() {
        $subdatas = array();
        $subdatas['stockcurrent1'] = array();
        $subdatas['stockcurrent2'] = array();
        $this->set('subdatas', $subdatas);
        //goi ham dung chung cua a.Nam
        $module_id = $this->get_id();
        $this->communications($module_id, true);
    }

  public function batches() {
        $total_quantity = 0;
        $total_used = 0;
        $total_balance = 0;
        $tmp = array();
        $subdatas = array();
        $module_id = $this->get_id();
        if(isset($modele_id)){
            $this->selectModel('Batche');
            $arr_set = $this->opm->arr_settings;
            $data = $this->Batche->select_all(array(
                'arr_where' => array(
                    'deleted' => false,
                    ),
            ));
            $newdata = array();
            $newdata = $data;
            $newdata = iterator_to_array($data);
            foreach($newdata as $keys => $values){
                if(isset($values['batch_no']) && isset($values['batch_name']) && isset($values['original_quantity']) && isset($values['qty_used_sold'])&&isset($values['balance'])){
                    $tmp[$keys]['batch_no'] = $values['batch_no'];
                    $tmp[$keys]['batch_name'] = $values['batch_name'];
                    $tmp[$keys]['original_quantity'] = $values['original_quantity'];
                    $tmp[$keys]['qty_used_sold'] = $values['qty_used_sold'];
                    $tmp[$keys]['balance'] = $values['balance'];
                    $total_quantity += $tmp[$keys]['original_quantity'];
                    $total_used += $tmp[$keys]['qty_used_sold'];
                    $total_balance += $tmp[$keys]['balance'];
                }
            }

        }
        //pr($total_quantity);
        $subdatas['batch'] = $tmp;
        $this->set('subdatas', $subdatas);
        $this->set('total_quantity',$total_quantity);
        $this->set('total_used',$total_used);
        $this->set('total_balance',$total_balance);
        $this->set_select_data_list('relationship', 'batches');
        //die;
    }

	public function bookings() {
        $subdatas = array();
        $tmp = array();
        $module_id = $this->get_id();
        //pr($module_id);
        if(isset($module_id)){
            $this->selectModel('Unit');
            $arr_set = $this->Unit->arr_settings;
            $data = $this->Unit->select_all(array(
                'arr_where' => array(
                    'batch_id' => new MongoId($module_id)
                    ),
            ));
            $newdata = array();
            $newdata = $data;
            $newdata = iterator_to_array($data);
            //pr($newdata);
            foreach($newdata as $keys => $values){
                if($module_id == $values['batch_id']&&isset($values['code']) && isset( $values['standard_location_name']) && isset($values['serial_no'])&& isset($values['current_location_name'])&& isset($values['usage'])&& isset($values['status'])){
                    $tmp[$keys]['code'] = $values['code'];
                    $tmp[$keys]['standard_location_name'] = $values['standard_location_name'];
                    $tmp[$keys]['serial_no'] = $values['serial_no'];
                    $tmp[$keys]['current_location_name'] = $values['current_location_name'];
                    $tmp[$keys]['usage'] = $values['usage'];
                    $tmp[$keys]['status'] = $values['status'];
                }
            }
        }
        //pr($tmp);
        $subdatas['bookings'] = $tmp;
        $this->set('subdatas', $subdatas);
        $this->set_select_data_list('relationship', 'bookings');
        //die;
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
                $tmp['Batche']['name'] = $_GET['name'];
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
        if (!empty($this->data) && !empty($_POST) && isset($this->data['Batche'])) {
            $arr_post = $this->data['Batche'];

            if (isset($arr_post['name']) && strlen($arr_post['name']) > 0) {
                $cond['name'] = new MongoRegex('/' . trim($arr_post['name']) . '/i');
            }

            if (strlen($arr_post['contact_name']) > 0) {
                $cond['contact_name'] = new MongoRegex('/' . $arr_post['contact_name'] . '/i');
            }
        }

        $this->selectModel('Batche');
        $arr_location = $this->Batche->select_all(array(
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
		$this->set('model', 'Batche');

        $this->layout = 'ajax';
    }

        public function dem($madeup_id=''){
        $sum = 0;
        $this->selectModel('Product');
        $data = $this->Product->select_all(array(
            'arr_where' => array('deleted' => false)
            ));
        $data = iterator_to_array($data);
        $md = array();
        foreach($data as $key => $value){
            pr($value['madeup']);
            if(isset($value['madeup']) && $value['madeup']!='')
            foreach($value['madeup'] as $vv){
                if(!$vv['deleted']){
                    $md[] = $vv['madeup_id'];
                    $md[] = $vv['quantity'];
                    $sum += $vv['quantity'];
                }
            }
        }
        pr($md);
        pr($sum);
        die;
    }



}