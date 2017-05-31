<?php

// Attach lib cal_price
App::import('Vendor', 'cal_price/cal_price');
App::uses('AppController', 'Controller');

class PurchaseordersController extends AppController {

	var $name = 'Purchaseorders';
	public $helpers = array();
	public $opm; //Option Module
	public $cal_price; //Option cal_price
	var $is_text = 0;
	public $modelName = 'Purchaseorder';

	public function beforeFilter() {
		parent::beforeFilter();
		$this->set_module_before_filter('Purchaseorder');
	}

	//Các điều kiện mở/khóa field trong entry
	public function check_lock() {
		if ($this->get_id() != '') {
			$arr_tmp = $this->opm->select_one(array('_id' => new MongoId($this->get_id())),array('purchase_orders_status'));
			if (isset($arr_tmp['purchase_orders_status'])) {
				if ($arr_tmp['purchase_orders_status'] == 'On order' || $arr_tmp['purchase_orders_status'] == 'Received'
					|| $arr_tmp['purchase_orders_status'] == 'Partly Received')
					return true;
			}
		} else
			return false;
	}

	public function rebuild_setting($arr_setting=array()){
		parent::rebuild_setting($arr_setting=array());
		$iditem = $this->get_id();
		if($iditem!='')
			$query = $this->opm->select_one(array('_id' => new MongoId($iditem)),array('job_id','purchase_orders_status'));
        $params = isset($this->params->params['pass'][0]) ? $this->params->params['pass'][0] : null;
        $valid = false;
		if($this->params->params['action'] == 'entry' || $valid = in_array($params,array('line_entry','text_entry'))){
			if(!$this->check_permission('purchaseorders_@_entry_@_edit')){
                if($valid){
                    $this->opm->set_lock_option('line_entry', 'products');
                    $this->opm->set_lock_option('text_entry', 'products');
                } else {
                    $this->opm->set_lock(array('name'), 'out');
                    $this->set('address_lock', '1');
                }
                return;
            }
			if($query['purchase_orders_status']=='Cancelled'){
				if($valid){
                    unset($this->opm->arr_settings['relationship']['line_entry']['block']['products']['custom_box_top']);
                    $this->opm->set_lock_option('line_entry', 'products');
                    $this->opm->set_lock_option('text_entry', 'products');
                } else {
                    $this->opm->set_lock(array('purchase_orders_status'), 'out');
                    $this->set('address_lock', '1');
                }
			}else if ($query['purchase_orders_status']!='In progress' && $valid){
				unset($this->opm->arr_settings['relationship']['line_entry']['block']['products']['field']['quantity']['edit']);
				unset($this->opm->arr_settings['relationship']['text_entry']['block']['products']['field']['quantity']['edit']);
			}
		}
        /*if($this->params->params['action']!='entry_search'){
			$query = $this->opm->select_one(array('_id'=> new MongoId($this->get_id())),array('job_id'));
			// if(isset($query['job_id'])&&is_object($query['job_id'])){
			// 	$this->selectModel('Job');
			// 	$job = $this->Job->select_one(array('_id'=> new MongoId($query['job_id'])),array('status'));
			// 	if(isset($job['status']) && $job['status'] == 'Completed'){
			// 		$this->opm->set_lock(array('name'), 'out');
			// 		$this->set('address_lock', '1');
			// 		$this->opm->set_lock_option('line_entry', 'products');
			// 		$this->opm->set_lock_option('text_entry', 'products');
			// 	}
			// }
		}*/
	}
	public function entry() {
		if ($this->check_lock()) {
			$this->set('address_lock', '1');
			$this->opm->set_lock(array('purchase_orders_status'), 'out');
			$this->opm->set_lock_option('line_entry', 'products');
			$this->opm->set_lock_option('text_entry', 'products');
		}
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

			if(isset($arr_tmp['tax_key']))
				$arr_set['field']['panel_4']['tax']['default'] = $arr_tmp['tax_key'];

			$this->set('item_title', $item_title);

			//END custom
			//show footer info
			$this->show_footer_info($arr_tmp);


			//add, setup field tự tăng
		}else {
			$this->redirect(URL.'/purchaseorders/add');
		}

		$this->set('arr_settings', $arr_set);
		$this->sub_tab_default = 'line_entry';
		$this->sub_tab('', $iditem);

		$this->set_entry_address($arr_tmp, $arr_set);

		//custom list tax
		$arr_options_custom['tax'] = '';
		$this->selectModel('Tax');
		$arr_options_custom['tax'] = $this->Tax->tax_select_list();
		$this->set('arr_options_custom', $arr_options_custom);
		//END custom
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
		$this->set('address_more_line', 2); //set
		$this->set('address_onchange', "save_address_pr('\"+keys+\"');");
		if (isset($arr_tmp['ship_to_company_id']) && strlen($arr_tmp['ship_to_company_id']) == 24)
			$this->set('address_company_id', 'ship_to_company_id');
	}





	/*public function lists() {
		// --- BaoNam sort phan trang ---
		$limit = LIST_LIMIT;
		$skip = 0;
		// dùng cho sort
		$sort_field = '_id';
		$sort_type = -1;
		if (isset($_POST['sort']) && strlen($_POST['sort']['field']) > 0) {
			if ($_POST['sort']['type'] == 'desc') {
				$sort_type = -1;
			} else {
				$sort_type = 1;
			}
			$sort_field = $_POST['sort']['field'];
			$this->Session->write($this->name . '_lists_search_sort', array($sort_field, $sort_type));
		} elseif ($this->Session->check($this->name . '_lists_search_sort')) {
			$session_sort = $this->Session->read($this->name . '_lists_search_sort');
			$sort_field = $session_sort[0];
			$sort_type = $session_sort[1];
		}
		$arr_order = array($sort_field => $sort_type);
		$this->set('sort_field', $sort_field);
		$this->set('sort_type', ($sort_type === 1) ? 'asc' : 'desc');
		// BaoNam sort phan trang
		// dùng cho phân trang
		$page_num = 1;
		if (isset($_POST['pagination']) && $_POST['pagination']['page-num'] > 0) {
			$page_num = $_POST['pagination']['page-num'];
			$limit =isset($_POST['pagination']['page-lists'])?$_POST['pagination']['page-lists']:0;
			$skip = $limit * ($page_num - 1);
		}

		$this->set('page_num', $page_num);
		$this->set('limit', $limit);
		// --- end --- BaoNam phan trang

		$where_query = $this->arr_search_where();

		if (!$this->is_popup) {
			$arr_query = $this->opm->select_all(array(
				'arr_where' => $where_query,
				'arr_order' => $arr_order,
				'limit' => $limit,
				'skip' => $skip
			));
		} else {

			// BaoNam, dùng cho popup
			$arr_order = array('id' => -1);
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

			// BaoNam
			$limit = 100;
			$skip = 0;
			if ($this->name == 'Products') {
				$limit = 100;
				$skip = 0;
				$page_num = 1;
				if (isset($_POST['pagination']) && $_POST['pagination']['page-num'] > 0) {

					// $limit = $_POST['pagination']['page-list'];
					$page_num = $_POST['pagination']['page-num'];
					$limit = $_POST['pagination']['page-list'];
					$skip = $limit * ($page_num - 1);
				}
				$this->set('page_num', $page_num);
				$this->set('limit', $limit);

				// BaoNam -- kiem tra search popup
				if (isset($where_query['name']) && isset($_POST['products_name']) && strlen($_POST['products_name']) > 0 && is_numeric($_POST['products_name'])) {
					$cond_tmp = $where_query['name'];
					unset($where_query['name']);

					$where_query['$or'] = array(
						array('code' => (int) $_POST['products_name']),
						array('name' => $cond_tmp)
					);
				}
				// end

				if (!empty($_GET)) {
					if (isset($_GET['company_id'])) { // $_GET['company_name']
						$where_query['supplier'] = array('$elemMatch' => array('company_id' => new MongoId($_GET['company_id'])));
						$arr_where['company_name']['values'] = $_GET['company_name'];
						$this->set('arr_where', $arr_where);
						$this->set('popup_company_id', $_GET['company_id']);
					}
				}

				// Fix search company_name popup là supplier
				if (isset($where_query['company_name'])) {
					$where_query['supplier'] = array('$elemMatch' => array('supplier' => $where_query['company_name']));
					unset($where_query['company_name']);
				}
			}

			// a.Vu code
			$arr_query = $this->opm->select_all(array(
				'arr_where' => $where_query,
				'arr_order' => $arr_order, // BaoNam
				'limit' => $limit, // BaoNam
				'skip' => $skip // BaoNam
			));
		}

		// --- BaoNam ---
		$total_page = $total_record = $total_current = 0;
		if (is_object($arr_query)) {
			$total_current = $arr_query->count(true);
			$total_record = $arr_query->count();
			if ($total_record % $limit != 0 &&$limit!=0) {
				if($limit!=0)
					$total_page = floor($total_record / $limit) + 1;
			} else {
				if($limit!=0)
					$total_page = $total_record / $limit;
			}
		}

		$this->set('total_current', $total_current);
		$this->set('total_page', $total_page);
		$this->set('total_record', $total_record);
		// --- BaoNam dùng cho popup và lists ---

		$this->set('arr_list', $arr_query);
		$list_field = $this->opm->list_view_field();
		$arr_set = $this->opm->arr_field_multi_key(array('name', 'type', 'cls', 'id', 'droplist'));
		$this->set('list_field', $list_field);
		$this->set('arr_set', $arr_set);

		foreach ($arr_set['type'] as $keys => $values) {
			if ($values == 'select')
				$opt_select[$keys] = $this->Setting->select_option_vl(array('setting_value' => $arr_set['droplist'][$keys]));
		}
		$this->set('opt_select', $opt_select);
		$this->SetModelClass();

		$this->Session->write($this->name . 'ViewThemes', 'lists');

		//Truong hop sort hoac load ajax
		if ($this->request->is('ajax') && !$this->is_popup) {
			$this->render('../Elements/lists_ajax');

			// Trường hợp không custom view
		} else if (!$this->is_popup && (!$this->element_exit('lists') || ($this->element_exit('lists') && $this->lists_mod != ''))) {
			$this->render('../Elements/lists');

			// Trường hợp custom view trong module
		}
	}*/




	public function arr_associated_data($field = '', $value = '', $valueid = '') {
		if(isset($_POST['arr']) && is_string($_POST['arr']) && $_POST['arr']!='')
			$tmp_data = (array)json_decode($_POST['arr']);
        if(isset($tmp_data['keys'])){
            if( ($tmp_data['keys']=='update' || $tmp_data['keys']=='add')
                &&!$this->check_permission($this->name.'_@_entry_@_edit')){
                echo 'You do not have permission on this action.';
                die;
            }
        }
		$arr_return = array();
		$arr_return[$field] = $value;

		/*
		* Xử lý chọn combobox company
		*/
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
			//change company
			$arr_return['company_name'] = $value;
			$arr_return['company_id'] = new MongoId($valueid);

			//find contact and more from Company
			$this->selectModel('Company');
			$arr_company = $this->Company->select_one(array('_id' => new MongoId($valueid)));
			$this->selectModel('Contact');
			$arr_contact = $arrtemp = array();

			$this->selectModel('Salesaccount');
			$salesaccount = $this->Salesaccount->select_one(array('company_id' => $arr_return['company_id']));
			$arr_return['payment_terms'] = (isset($salesaccount['payment_terms']) ? $salesaccount['payment_terms'] : 0);
			$arr_return['payment_terms_id'] = (isset($salesaccount['payment_terms_id']) ? $salesaccount['payment_terms_id'] : 0);

			// is set contact_default_id
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

			//change contact
			if (isset($arr_contact['_id'])) {
				$arr_return['contact_name'] = $arr_contact['first_name'] . ' ' . $arr_contact['last_name'];
				$arr_return['contact_id'] = $arr_contact['_id'];
			} else {
				$arr_return['contact_name'] = '';
				$arr_return['contact_id'] = '';
			}


			//change our_csr
			/*if (isset($arr_company['our_csr']) && isset($arr_company['our_csr_id']) && $arr_company['our_csr_id'] != '') {
				$arr_return['our_csr_id'] = $arr_company['our_csr_id'];
				$arr_return['our_csr'] = $arr_company['our_csr'];
			}else{
				$arr_return['our_csr_id'] = $this->opm->user_id();
				$arr_return['our_csr'] = $this->opm->user_name();
			}*/

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
				$arr_return['phone'] = $arr_company['phone'];
			else {  // neu khong co phone thi lay phone cua contact mac dinh
				if (isset($arr_contact['direct_dial']))
					$arr_return['phone'] = $arr_contact['direct_dial'];
				elseif (!isset($arr_contact['direct_dial']) && isset($arr_contact['mobile']))
					$arr_return['phone'] = $arr_contact['mobile'];
				elseif (!isset($arr_contact['direct_dial']) && !isset($arr_contact['mobile']))
					$arr_return['phone'] = '';  //bat buoc phai co dong nay khong thi no se lay du lieu cua cty truoc
			}

			//change email
			if (isset($arr_contact['email']))
				$arr_return['email'] = $arr_contact['email'];
			elseif (isset($arr_company['email']) && $arr_company['email']!='')
				$arr_return['email'] = $arr_company['email'];
			elseif (!isset($arr_contact['email']))
				$arr_return['email'] = '';

			//change fax
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
						$arr_return['invoice_address'][0]['invoice_' . $ka] = $va;
					else
						$arr_return['invoice_address'][0][$ka] = $va;
				}
			}

			if(isset($salesaccount['tax_code_id'])&& $salesaccount['tax_code_id']!=''){
                $keytax = $salesaccount['tax_code_id'];
                $this->selectModel('Tax');
                $arr_tax = $this->Tax->tax_list();
                $arr_tax_text = $this->Tax->tax_select_list();
                $arr_return['tax'] = $keytax;
                $arr_return['taxval'] = (float)$arr_tax[$keytax];
                $arr_return['taxtext'] = $arr_tax_text[$keytax];

            }else if(isset($arr_return['invoice_address'][0]['invoice_province_state_id'])){
				$keytax = $arr_return['invoice_address'][0]['invoice_province_state_id'];
				$this->selectModel('Tax');
				$arr_tax = $this->Tax->tax_list();
				$arr_tax_text = $this->Tax->tax_select_list();
				$arr_return['tax'] = $keytax;
				$arr_return['taxval'] = (float)$arr_tax[$keytax];
				$arr_return['taxtext'] = $arr_tax_text[$keytax];
			}
            $arr_return['products'] = $this->update_all_option('products',array('taxper'=>$arr_return['taxval']),true);
           	$arr_return = array_merge(	$arr_return, $this->new_cal_sum($arr_return['products']));
		}



		/*
		* Xử lý chọn ship_to_company_name
		*/
		if ($field == 'ship_to_company_name' && $valueid != ''){

			$arr_return = array(
				'ship_to_company_name' => '',
				'ship_to_company_id' => '',
				'ship_to_contact_name' => '',
				'ship_to_contact_id' => '',
			);

			$arr_return['ship_to_company_name'] = $value;
			$arr_return['ship_to_company_id'] = new MongoId($valueid);

			//find contact and more from Company
			$this->selectModel('Company');
			$arr_company = $this->Company->select_one(array('_id' => new MongoId($valueid)));
			$this->selectModel('Contact');
			$arr_contact = $arrtemp = array();

			// is set contact_default_id
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

			//change contact
			if (isset($arr_contact['_id'])) {
				$arr_return['ship_to_contact_name'] = $arr_contact['first_name'] . ' ' . $arr_contact['last_name'];
				$arr_return['ship_to_contact_id'] = $arr_contact['_id'];
			}else {
				$arr_return['ship_to_contact_name'] = '';
				$arr_return['ship_to_contact_id'] = '';
			}


			//change address
			if(isset($arr_company['addresses_default_key']))
				$add_default = $arr_company['addresses_default_key'];
			if(isset($add_default) && isset($arr_company['addresses'][$add_default])) {
				foreach ($arr_company['addresses'][$add_default] as $ka => $va) {
					if ($ka != 'deleted')
						$arr_return['shipping_address'][0]['shipping_'.$ka] = $va;
					else
						$arr_return['shipping_address'][0][$ka] = $va;
				}

				//set lai province_state_id neu Company ko luu
				if(isset($arr_return['shipping_address'][0]['shipping_province_state_id']) && $arr_return['shipping_address'][0]['shipping_province_state_id']==''){
					$reprovice = $this->province_reverse('CA');
					if(isset($reprovice[$arr_return['shipping_address'][0]['shipping_province_state']]))
						$arr_return['shipping_address'][0]['shipping_province_state_id'] = $reprovice[$arr_return['shipping_address'][0]['shipping_province_state']];
				}

				//change tax
				if(isset($arr_return['shipping_address'][0]['shipping_province_state_id']) && $arr_return['shipping_address'][0]['shipping_province_state_id']!=''){
					$arr_return['tax_key'] = $arr_return['shipping_address'][0]['shipping_province_state_id'];
					$this->selectModel('Tax');
					$arr_tax = $this->Tax->tax_select_list();
					if(isset($arr_tax[$arr_return['tax_key']]))
						$arr_return['tax'] = $arr_tax[$arr_return['tax_key']];
				// neu company ko lưu shipping_province_state_id
				}
			}

		}


		/*
		* Xử lý chọn shipper_company_name
		*/
		if ($field == 'shipper_company_name' && $valueid != ''){
			$arr_return['shipper_company_id'] = new MongoId($valueid);
		}


		/*
		* Xử lý chọn combobox contact
		*/
		if ($field == 'contact_name' && $valueid != '') {
			$arr_return = array(
				'contact_name' => '',
				'contact_id' => '',
				'phone' => '',
				'email' => '',
			);
			//change contacts
			$arr_return['contact_name'] = $value;
			$arr_return['contact_id'] = new MongoId($valueid);
			//find more from contact
			$this->selectModel('Contact');
			$arr_contact = $this->Contact->select_one(array('_id' => new MongoId($valueid)));

			//change phone
			if (isset($arr_contact['direct_dial']) && $arr_contact['direct_dial'] != '')
				$arr_return['phone'] = $arr_contact['direct_dial'];
			//change email
			if (isset($arr_contact['email']))
				$arr_return['email'] = $arr_contact['email'];
			//nếu company khác hiện có
			if (isset($arr_contact['company_id'])) {
				echo '';
			}
		}


		/*
		* Xử lý chọn our_rep
		*/
		if ($field == 'our_rep' && $valueid != ''){
			$arr_return['our_rep_id'] = new MongoId($valueid);
		}

		/*
		* Xử lý chọn ship_to_contact_name
		*/
		if ($field == 'ship_to_contact_name' && $valueid != ''){
			$arr_return['ship_to_contact_id'] = new MongoId($valueid);
		}

		/*
		* Xử lý chọn combobox received_by_contact_name
		*/
		if ($field == 'received_by_contact_name' && $valueid != ''){
			$arr_return['received_by_contact_id'] = new MongoId($valueid);
		}

		/*
		* Xử lý thay đổi TAX
		*/
		if ($field == 'shipping_address'){
			$value = end($value);
			if( isset($value['shipping_province_state_id']) )
				$arr_return['tax_key'] = $value['shipping_province_state_id'];
			$this->selectModel('Tax');
			$arr_tax = $this->Tax->tax_select_list();
			if(isset($arr_tax[$arr_return['tax_key']]))
				$arr_return['tax'] = $arr_tax[$arr_return['tax_key']];
			$arr_return['shipping_address'] = array();
			$arr_return['shipping_address'][0] = $value; //ep address ve array vi address chep tu company qua co the khong phai la array thu 0

			//tinh lai thue cho line entry
			if(isset($arr_return['tax'])){
				$taxper = explode("%",$arr_return['tax']);
				$taxper = (float)trim($taxper[0]);
				$this->update_all_option('products',array('taxper'=>$taxper));
			}
			return $arr_return;
		}

		/*
		* Xử lý status
		*/
		if($field=='purchase_orders_status'){
			if ($value == 'Received') {
				$arr_return['delivery_date'] = new MongoDate(time());
				$arr_return['received_by_contact_id']= $this->opm->user_id();
				$arr_return['received_by_contact_name']= $this->opm->user_name();
			} else if ($value != 'Approved for Payment') {
				$arr_return['delivery_date'] = '';
				$arr_return['received_by_contact_id']= '';
				$arr_return['received_by_contact_name']= '';
			}
		}else if($field=='salesorder_name'){
			$arr_value = explode('_@_', $value);
			$arr_return['salesorder_name'] =$arr_value[0];
			$arr_return['salesorder_number'] =$arr_value[1];
			$arr_return['salesorder_id'] = new MongoId($valueid);
		}

		return $arr_return;


	}







	public function entry_search() {
		//parent
		$arr_set = $this->opm->arr_settings;
		$arr_set['field']['panel_1']['code']['lock'] = '';
		$arr_set['field']['panel_1']['our_rep']['not_custom'] = '0';
		$arr_set['field']['panel_1']['our_csr']['not_custom'] = '0';
		$arr_set['field']['panel_1']['purchaseorder_date']['default'] = '';
		$arr_set['field']['panel_4']['purchase_orders_status']['default'] = '';
		$arr_set['field']['panel_4']['payment_terms']['default'] = '';
		$arr_set['field']['panel_4']['job_name']['not_custom'] = '0';
		$arr_set['field']['panel_4']['job_number']['lock'] = '';
		$arr_set['field']['panel_4']['salesorder_name']['not_custom'] = '0';
		$arr_set['field']['panel_4']['salesorder_number']['lock'] = '';
		$arr_set['field']['panel_4']['tax']['default'] = '';
		$arr_set['field']['panel_4']['taxval']['default'] = '';
		$arr_set['field']['panel_1']['sum_amount'] = array(
											        'name' => __('Total Cost'),
											        'type' => 'text',
											        'moreclass' => 'fixbor2',
											    );
		unset($arr_set['field']['panel_1']['none']);

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
						$arr_set['field'][$ks][$field]['default'] = ''; //$where[$field]['values'];
					}
				}
			}
		}
		//end parent
		$this->set('arr_settings', $arr_set);

		//address
		$this->set('address_key',array('shipping'));
		$address_label = array('Shipping address');
		$this->set('address_label', $address_label);
		$address_conner[0]['top'] = 'hgt';
		$address_conner[0]['bottom'] = 'fixbor3 jt_ppbot';
		$this->set('address_conner', $address_conner);
		$this->set('address_more_line', 2); //set
		$address_hidden_field = array('shipping_address');
		$this->set('address_hidden_field', $address_hidden_field); //set
		$address_country = $this->country();
		$this->set('address_country', $address_country); //set
		$this->set('address_country_id', ''); //set
		$address_province['invoice'] = $address_province['shipping'] = $this->province("CA");
		$this->set('address_province', ""); //set
        $this->set('address_province_id', ""); //set
		$this->set('address_onchange', "save_address_pr('\"+keys+\"');");
		$address_hidden_value = array('');
		$this->set('address_hidden_value', $address_hidden_value);
		$this->set('address_mode', 'search');
	}

	public function set_cal_price() {
		$this->cal_price = new cal_price; //Option cal_price
		//set arr_price_break default
		$this->cal_price->arr_price_break = array();
		//set arr_product default
		$this->cal_price->arr_product = array();
		//set arr_product item default
		$this->cal_price->arr_product_items = array();
	}

	//Sử dụng thư viện cal_price để tính
	function purchaseorder_cal_price(){
		if(isset($_POST['arr']['id'])){
			$query = $this->opm->select_one(array('_id'=>new MongoId($this->get_id())),array('products','tax'));
			$key = $_POST['arr']['id'];
			unset($_POST['arr']['id']);
			if(isset($query['products'][$key])){
				$arr_return = array();
				$query['products'][$key] = array_merge($query['products'][$key],$_POST['arr']);
				if (is_object($query['products'][$key]['products_id']) && isset($_POST['arr']['sell_price']) && $_POST['arr']['sell_price']) {
					$this->selectModel('Product');
					if ($this->Product->collection->count(array('_id' => $query['products'][$key]['products_id'], 'product_type' => 'Vendor Stock'))) {
						$arr_return['confirm'] = 'Do you want update cost price '. $_POST['arr']['sell_price'] .' to <span style="font-weight: bold;">Product #'.$query['products'][$key]['code'].'</span> ?';
					}
				}
				$receive_qty = isset($query['products'][$key]['quantity_received']) ? $query['products'][$key]['quantity_received'] : 0;
				$return_qty = isset($query['products'][$key]['quantity_returned']) ? $query['products'][$key]['quantity_returned'] : 0;
				$balance_received = $receive_qty - $return_qty;
				$original_quantity = $query['products'][$key]['quantity'];
				if($balance_received == 0){
					if(isset($query['products'][$key]['balance_received']) && !$query['products'][$key]['balance_received'] && isset($query['products'][$key]['quantity_received']) && $query['products'][$key]['quantity_received'])
						$balance_received = 0;
					else
						$balance_received = $original_quantity;
				}
				$query['products'][$key]['quantity'] = $balance_received;
				$cal_price = new cal_price;
            	$cal_price->arr_product_items = $query['products'][$key];
            	$query['products'][$key] = $cal_price->purchaseorder_cal_price();
            	$query['products'][$key]['quantity'] = $original_quantity;
            	$sum_sub_total = $sum_amount = 0;
            	foreach($query['products'] as $product){
            		if(isset($product['deleted']) && $product['deleted']) continue;
            		$sum_sub_total += (float)round($product['sub_total'],2);
            		$sum_amount += (float)round($product['amount'],2);
            	}
            	$sum_tax = round($sum_amount - $sum_sub_total,3);
            	$query['sum_sub_total'] = $sum_sub_total;
            	$query['sum_tax'] = $sum_tax;
            	$query['sum_amount'] = $sum_amount;
            	$this->opm->save($query);
            	echo json_encode(array_merge($arr_return, array('self'=>$query['products'][$key],'sum'=>array('sum_sub_total'=>$sum_sub_total,'sum_tax'=>$sum_tax,'sum_amount'=>$sum_amount))));
			}
		}
		die;
	}




	public function create_pur_from_product($product_id = '') {
		if(strlen($product_id)!=24){
			$this->redirect('/products/entry');
		}

		//lay data dua vao product
		$this->selectModel('Product');
		$arr_product = $this->Product->select_one(array('_id' => new MongoId($product_id)));

		//Tạo data PO để lưu
		$new_arr = array();
		if(isset($arr_product['company_name']) && isset($arr_product['company_id'])){
			$new_arr['company_name'] = $arr_product['company_name'];
			$new_arr['company_id'] = $arr_product['company_id'];
			//lấy thông tin liên quan với company
			$new_arr = $this->arr_associated_data('company_name', $arr_product['company_name'], $arr_product['company_id']);
		}

		// thông tin lien quan den product - line entry
		$arr_fields = array('code','sku','sizew','sizew_unit','sizeh','sizeh_unit','oum','sell_price','sell_by','oum_depend','unit_price');
		$arr_float = array('sizew','sizeh','sell_price','unit_price');

		if(isset($arr_product['_id']))
			$new_arr['products'][0]['products_id'] = $arr_product['_id'];
		if(isset($arr_product['name']))
			$new_arr['products'][0]['products_name'] = $arr_product['name'];

		$new_arr['products'][0]['deleted'] = false;
		$new_arr['products'][0]['quantity'] = 0;

		foreach($arr_fields as $kk){
			if(isset($arr_product[$kk])){
				if(in_array($kk,$arr_float))
					$new_arr['products'][0][$kk] = (float)$arr_product[$kk];
				else if($kk=='code')
					$new_arr['products'][0]['code'] = (int)$arr_product[$kk];
				else
					$new_arr['products'][0][$kk] = $arr_product[$kk];
			}
		}

		$new_arr['products'][0]['taxper'] = 5;

		//tính gia cho line
		$this->set_cal_price();
		$this->cal_price->arr_product_items = $new_arr['products'][0];
		$new_arr['products'][0] = $this->cal_price->cal_price_items();
		$this->opm->arr_default_before_save = $new_arr;
		$this->opm->add('name','');
		$this->redirect('/purchaseorders/entry/'.$this->opm->mongo_id_after_save);
	}



	// Delete 1 record
	public function delete($ids = 0) {
		$ids = $this->get_id();
		if ($ids != '') {
			$str_return = $this->opm->update($ids, 'deleted', true);
			$actions = $this->Session->read($this->name . 'ViewThemes');
			$this->Session->write($this->name . 'ViewId', '');
			$this->redirect('/' . $this->params->params['controller'] . '/' . $actions);
		} else {
			$this->redirect('/' . $this->params->params['controller'] . '/lists');
			$this->Session->write($this->name . 'ViewId', '');
		}
		echo 'ok'; // BaoNam: ẩn redirect vì không dùng để reload lại
		die;
	}

	//subtab line entry
	public function line_entry() {
		$is_text = $this->is_text;
		if ($this->check_lock()) {
			$this->opm->set_lock_option('line_entry', 'products');
			$this->opm->set_lock_option('text_entry', 'products');
		}

		$subdatas = $arr_ret = array();
		$codeauto = 0;
		$opname = 'products';
		$sum_sub_total = $sum_tax = 0;
		$subdatas[$opname] = array();
		$ids = $this->get_id();
		if ($ids != '') {
			//update sum
			// $keyfield = array(
			// 	"sub_total" => "sub_total",
			// 	"tax" => "tax",
			// 	"amount" => "amount",
			// 	"sum_sub_total" => "sum_sub_total",
			// 	"sum_tax" => "sum_tax",
			// 	"sum_amount" => "sum_amount"
			// );

			// if($this->request->is('ajax'))
			// 	$arr_sum = $this->update_sum('products', $keyfield);

			//get entry data
			$arr_ret = $this->line_entry_data($opname, $is_text);
			if (isset($arr_ret[$opname]))
				$subdatas[$opname] = $arr_ret[$opname];
		}
		$this->set('subdatas', $subdatas);
		$codeauto = $this->opm->get_auto_code('code');
		$this->set('nextcode', $codeauto);
		$this->set('file_name', 'quotation_' . $this->get_id());
		$this->set('sum_sub_total', $arr_ret['sum_sub_total']);
		$this->set('sum_amount', $arr_ret['sum_amount']);
		$this->set('sum_tax', $arr_ret['sum_tax']);
		$this->set('link_add_atction', 'rfqs_list');
		$this->set_select_data_list('relationship', 'line_entry');
	}

	//check and cal for Line Entry
	public function line_entry_data($opname = '', $is_text = 0) {
		$arr_ret = array();
		$this->selectModel('Setting');
		if ($this->get_id() != '') {
			$newdata = $option_select_dynamic = array();
			$query = $this->opm->select_one(array('_id' => new MongoId($this->get_id())));

			//set sum
			$arr_ret['sum_sub_total'] = $arr_ret['sum_amount'] = $arr_ret['sum_tax'] = '0.00';
			if (isset($query['sum_sub_total']) && $query['sum_sub_total'] != '')
				$arr_ret['sum_sub_total'] = $query['sum_sub_total'];
			if (isset($query['sum_amount']) && $query['sum_amount'] != '')
				$arr_ret['sum_amount'] = $query['sum_amount'];
			if (isset($query['sum_tax']) && $query['sum_tax'] != '')
				$arr_ret['sum_tax'] = $query['sum_tax'];
			if($query['purchase_orders_status']=='Cancelled'){
				$arr_ret['sum_tax'] = $arr_ret['sum_amount'] = $arr_ret['sum_sub_total'] = 0;
			}

			if (isset($query[$opname]) && is_array($query[$opname])) {
				foreach ($query[$opname] as $key => $arr) {
					if (!isset($arr['deleted']) || isset($arr['deleted'])&&!$arr['deleted']) {
						$newdata[$key] = $arr;
						//chuyển html products_name <br /> thành \n
						//$newdata[$key]['products_name'] = str_replace("<br>","\n",$arr['products_name']);
						//nếu là line entry thì hiện products_name 1 dòng
						if (isset($newdata[$key]['products_name']) && $is_text != 1) {
							$arrtmp = explode("\n", $newdata[$key]['products_name']);
							$newdata[$key]['products_name'] = $arrtmp[0];
						}
						//set all price in display
						if (isset($arr['area']))
							$newdata[$key]['area'] = (float) $arr['area'];
						if (isset($arr['unit_price']))
							$newdata[$key]['unit_price'] = $this->opm->format_currency( $arr['unit_price'], 4);
						else
							$newdata[$key]['unit_price'] = '0.0000';
						if (isset($arr['sub_total']))
							$newdata[$key]['sub_total'] = $this->opm->format_currency( $arr['sub_total']);
						else
							$newdata[$key]['sub_total'] = '0.00';
						if (isset($arr['tax']))
							$newdata[$key]['tax'] = $this->opm->format_currency( $arr['tax'], 3);
						else
							$newdata[$key]['tax'] = '0.000';
						if (isset($arr['amount']))
							$newdata[$key]['amount'] = $this->opm->format_currency( $arr['amount']);
						else
							$newdata[$key]['amount'] = '0.00';
						// $newdata[$key]['icon']['code'] = (is_object($arr['products_id']) ? URL.'/products/entry/'.$arr['products_id'] : '#');
						$newdata[$key]['_id'] = $key;

						//tính tổng khi lặp vòng
						/* if(isset($arr['sub_total']))
						  $arr_ret['sum_sub_total'] += (float)$arr['sub_total'];
						  if(isset($arr['tax']))
						  $arr_ret['sum_tax'] += (float)$arr['tax']; */


						//chặn không cho custom size nếu is_custom_size = 1
						if (isset($arr['is_custom_size']) && (int) $arr['is_custom_size'] == 1) {
							$newdata[$key]['attr']['sizeh'] = 'readonly="readonly"';
							$newdata[$key]['attr']['sizew'] = 'readonly="readonly"';
							$newdata[$key]['attr']['sizeh_unit'] = 'readonly="readonly"';
							$newdata[$key]['attr']['sizew_unit'] = 'readonly="readonly"';
							$newdata[$key]['attr']['sell_by'] = 'readonly="readonly"';
						}
						//set lại select dựa vào loại sell_by
						if (isset($newdata[$key]['sell_by'])) {
							$option_select_dynamic['oum_' . $key] = $this->Setting->select_option_vl(array('setting_value' => 'product_oum_' . strtolower($arr['sell_by'])));
						}
					} //end if
				}
			}
			$arr_ret[$opname] = $newdata;
			//pr($arr_ret);die;
		}
		$this->set('option_select_dynamic', $option_select_dynamic);
		return $arr_ret;
	}

	//subtab Text entry
	public function view_product_option() {
		echo '';
		die;
	}

	//subtab Text entry
	public function text_entry() {
		$this->is_text = 1;
		$this->line_entry();
	}

	//	end trieu


	public function email_pdf() {
		$this->layout = 'pdf';
		$ids = $this->get_id();
		if ($ids != '') {
			$query = $this->opm->select_one(array('_id' => new MongoId($ids)));
			$arrtemp = $query;
			//set header
			$this->set('logo_link', 'img/logo_anvy.jpg');
			$this->set('company_address', '3145 - 5th Ave NE<br />Calgary  AB  T2A  6A3<br />');

			//customer address
			$customer = '';
			if (isset($arrtemp['company_id']) && strlen($arrtemp['company_id']) == 24)
				$customer .= '<b>' . $this->get_name('Company', $arrtemp['company_id']) . '</b><br />';
			else if (isset($arrtemp['company_name']))
				$customer .= '<b>' . $arrtemp['company_name'] . '</b>.<br />';
			if (isset($arrtemp['contact_id']) && strlen($arrtemp['contact_id']) == 24)
				$customer .= $this->get_name('Contact', $arrtemp['contact_id']) . '<br />';
			else if (isset($arrtemp['contact_name']) && $arrtemp['contact_name'] != '')
				$customer .= $arrtemp['contact_name'] . '<br />';

			$arradd = array('invoice', 'shipping');
			foreach ($arradd as $vvs) {
				$kk = $vvs;
				$customer_address = '';
				if (isset($arrtemp[$kk . '_address']) && isset($arrtemp[$kk . '_address'][0]) && count($arrtemp[$kk . '_address']) > 0) {
					$temp = $arrtemp[$kk . '_address'][0];
					if (isset($temp[$kk . '_address_1']) && $temp[$kk . '_address_1'] != '')
						$customer_address .= $temp[$kk . '_address_1'] . ', ';
					if (isset($temp[$kk . '_address_2']) && $temp[$kk . '_address_2'] != '')
						$customer_address .= $temp[$kk . '_address_2'] . ' ';
					if (isset($temp[$kk . '_address_3']) && $temp[$kk . '_address_3'] != '')
						$customer_address .= $temp[$kk . '_address_3'] . '<br />';
					else
						$customer_address .= '<br />';
					if (isset($temp[$kk . '_town_city']) && $temp[$kk . '_town_city'] != '')
						$customer_address .= $temp[$kk . '_town_city'];

					if (isset($temp[$kk . '_province_state']))
						$customer_address .= ' ' . $temp[$kk . '_province_state'] . ' ';
					else if (isset($temp[$kk . '_province_state_id']) && isset($temp[$kk . '_country_id'])) {
						$keytemp = $temp[$kk . '_province_state_id'];
						$provkey = $this->province($temp[$kk . '_country_id']);
						if (isset($provkey[$temp]))
							$customer_address .= ' ' . $provkey[$temp] . ' ';
					}

					if (isset($temp[$kk . '_zip_postcode']) && $temp[$kk . '_zip_postcode'] != '')
						$customer_address .= $temp[$kk . '_zip_postcode'];

					if (isset($temp[$kk . '_country']) && isset($temp[$kk . '_country_id']) && (int) $temp[$kk . '_country_id'] != "CA")
						$customer_address .= ' ' . $temp[$kk . '_country'] . '<br />';
					else
						$customer_address .= '<br />';
					$arr_address[$kk] = $customer_address;
				}
			}


			if (isset($arrtemp['name']) && $arrtemp['name'] != '') {
				$heading = $arrtemp['name'];
			} else {
				$heading = '';
			}

			if ($arrtemp['ship_to_contact_name']) {
				$ship_to_contact_name = $arrtemp['ship_to_contact_name'] . '<br>';
			} else {
				$ship_to_contact_name = '';
			}
			if (isset($arr_address['invoice']))
				$this->set('customer_address', $customer . $arr_address['invoice']);
			$this->set('ship_to_contact_name', $ship_to_contact_name);
			$this->set('shipping_address', $arr_address['shipping']);
			$this->set('ref_no', $arrtemp['code']);
			if (isset($arrtemp['purchord_date']) && is_object($arrtemp['purchord_date']))
				$this->set('purchord_date', $this->opm->format_date($arrtemp['purchord_date']));
			if (isset($arrtemp['required_date']) && is_object($arrtemp['required_date']))
				$this->set('required_date', $this->opm->format_date($arrtemp['required_date']));

			//set content
			$date_now = date('Ymd');
			$time=time();
			$filename = 'PUR' . $date_now .$time. '-' . $arrtemp['code'];
			$this->set('filename', $filename);


			$thisfolder = 'upload'.DS.date("Y_m");
			$thisfolder_1='upload'.','.date("Y_m");

			$folder = ROOT.DS.APP_DIR.DS.WEBROOT_DIR.DS.$thisfolder;
			if (!file_exists($folder)) {
				mkdir($folder, 0777, true);
			}



			$this->set('heading', $heading);
			$html_cont = '';
			if (isset($arrtemp['products']) && is_array($arrtemp['products']) && count($arrtemp['products']) > 0) {
				$line = $qty_sum = $total = 0;
				$colum = 7;
				foreach ($arrtemp['products'] as $keys => $values) {
					if (!$values['deleted']) {
						if ($line % 2 == 0)
							$bgs = '#fdfcfa';
						else
							$bgs = '#eeeeee';
						//code
						$html_cont .= '<tr style="background-color:' . $bgs . ';"><td class="first">';
						if (isset($values['sku']))
							$html_cont .= '  ' . $values['sku'];
						else
							$html_cont .= '  #' . $keys;
						//desription
						$html_cont .= '</td><td>';
						if (isset($values['products_name']))
							$html_cont .= $values['products_name'];
						else
							$html_cont .= '#';
						//width
						$html_cont .= '</td><td align="right">';
						if (isset($values['sizew']) && $values['sizew'] != '' && isset($values['sizew_unit']) && $values['sizew_unit'] != '')
							$html_cont .= $values['sizew'] . ' (' . $values['sizew_unit'] . ')';
						else if (isset($values['sizew']) && $values['sizew'] != '')
							$html_cont .= $values['sizew'] . ' (in.)';
						else
							$html_cont .= '';
						//height
						$html_cont .= '</td><td align="right">';
						if (isset($values['sizeh']) && $values['sizeh'] != '' && isset($values['sizeh_unit']) && $values['sizeh_unit'] != '')
							$html_cont .= $values['sizeh'] . ' (' . $values['sizeh_unit'] . ')';
						else if (isset($values['sizeh']) && $values['sizeh'] != '')
							$html_cont .= $values['sizeh'] . ' (in.)';
						else
							$html_cont .= '';
						//Unit price
						$html_cont .= '</td><td align="right">';
						if (isset($values['unit_price']))
							$html_cont .= $this->opm->format_currency($values['unit_price']);
						else
							$html_cont .= '0.00';
						//Qty
						$html_cont .= '</td><td align="right">';
						if (isset($values['quantity']))
							$html_cont .= $values['quantity'];
						else
							$html_cont .= '';
						//line total
						$html_cont .= '</td><td align="right" class="end">';
						if (isset($values['sub_total']))
							$html_cont .= $this->opm->format_currency((float) $values['sub_total']);
						else
							$html_cont .= '';


						$html_cont .= '</td></tr>';
						$line++;
					}//end if deleted
				}//end for


				if ($line % 2 == 0) {
					$bgs = '#fdfcfa';
					$bgs2 = '#eeeeee';
				} else {
					$bgs = '#eeeeee';
					$bgs2 = '#fdfcfa';
				}

				$sub_total = $total = $taxtotal = 0.00;
				if (isset($arrtemp['sum_sub_total']))
					$sub_total = (float) $arrtemp['sum_sub_total'];
				if (isset($arrtemp['sum_tax']))
					$taxtotal = (float) $arrtemp['sum_tax'];
				if (isset($arrtemp['sum_amount']))
					$total = (float) $arrtemp['sum_amount'];
				//Sub Total
				$html_cont .= '<tr style="background-color:' . $bgs . ';">
									<td colspan="' . ($colum - 1) . '" align="right" style="font-weight:bold;border-top:2px solid #aaa;" class="first">Sub Total:</td>
									<td align="right" style="border-top:2px solid #aaa;" class="end">' . $this->opm->format_currency($sub_total) . '</td>
							   </tr>';
				//GST
				$html_cont .= '<tr style="background-color:' . $bgs2 . ';">
									<td colspan="' . ($colum - 1) . '" align="right" style="font-weight:bold;" class="first">HST/GST:</td>
									<td align="right" class="end">' . $this->opm->format_currency($taxtotal) . '</td>
							   </tr>';
				//Total
				$html_cont .= '<tr style="background-color:' . $bgs . ';">
									<td colspan="' . ($colum - 1) . '" align="right" style="font-weight:bold;" class="first bottom">Total:</td>
									<td align="right" class="end bottom">' . $this->opm->format_currency($total) . '</td>
							   </tr>';
			}//end if


			$this->set('html_cont', $html_cont);
			if (isset($arrtemp['our_rep'])) {
				$this->set('user_name', ' ' . $arrtemp['our_rep']);
			} else
				$this->set('user_name', ' ' . $this->opm->user_name());
			//end set content
			//set footer
			$this->set('link_this_folder',$thisfolder);
			$this->render('email_pdf');
			$v_link_pdf= $thisfolder_1.','.$filename.'.pdf';
			$v_file_name=$filename.'.pdf';

			$this->redirect('/docs/add_from_option/'.$this->ModuleName().'/'.$this->get_id().'/'.$v_link_pdf.'/'.$v_file_name.'/'.$this->params->params['controller'].'');

		}
		die;
	}

	//Export pdf
	public function view_pdf($getfile=false) {
		$this->layout = 'pdf';
		$ids = $this->get_id();
		if ($ids != '') {
			$query = $this->opm->select_one(array('_id' => new MongoId($ids)));
			$arrtemp = $query;
			//set header
			$this->set('logo_link', 'img/logo_anvy.jpg');
			$this->set('company_address', '3145 - 5th Ave NE<br />Calgary  AB  T2A  6A3<br />');

			//customer address
			$customer = '';
			if (isset($arrtemp['company_id']) && strlen($arrtemp['company_id']) == 24)
				$customer .= '<b>' . $this->get_name('Company', $arrtemp['company_id']) . '</b><br />';
			else if (isset($arrtemp['company_name']))
				$customer .= '<b>' . $arrtemp['company_name'] . '</b>.<br />';
			if (isset($arrtemp['contact_id']) && strlen($arrtemp['contact_id']) == 24)
				$customer .= $this->get_name('Contact', $arrtemp['contact_id']) . '<br />';
			else if (isset($arrtemp['contact_name']) && $arrtemp['contact_name'] != '')
				$customer .= $arrtemp['contact_name'] . '<br />';

			$arradd = array('invoice', 'shipping');
			foreach ($arradd as $vvs) {
				$kk = $vvs;
				$customer_address = '';
				if (isset($arrtemp[$kk . '_address']) && isset($arrtemp[$kk . '_address'][0]) && count($arrtemp[$kk . '_address']) > 0) {
					$temp = $arrtemp[$kk . '_address'][0];
					if (isset($temp[$kk . '_address_1']) && $temp[$kk . '_address_1'] != '')
						$customer_address .= $temp[$kk . '_address_1'] . ', ';
					if (isset($temp[$kk . '_address_2']) && $temp[$kk . '_address_2'] != '')
						$customer_address .= $temp[$kk . '_address_2'] . ' ';
					if (isset($temp[$kk . '_address_3']) && $temp[$kk . '_address_3'] != '')
						$customer_address .= $temp[$kk . '_address_3'] . '<br />';
					else
						$customer_address .= '<br />';
					if (isset($temp[$kk . '_town_city']) && $temp[$kk . '_town_city'] != '')
						$customer_address .= $temp[$kk . '_town_city'];

					if (isset($temp[$kk . '_province_state']))
						$customer_address .= ' ' . $temp[$kk . '_province_state'] . ' ';
					else if (isset($temp[$kk . '_province_state_id']) && isset($temp[$kk . '_country_id'])) {
						$keytemp = $temp[$kk . '_province_state_id'];
						$provkey = $this->province($temp[$kk . '_country_id']);
						if (isset($provkey[$temp]))
							$customer_address .= ' ' . $provkey[$temp] . ' ';
					}

					if (isset($temp[$kk . '_zip_postcode']) && $temp[$kk . '_zip_postcode'] != '')
						$customer_address .= $temp[$kk . '_zip_postcode'];

					if (isset($temp[$kk . '_country']) && isset($temp[$kk . '_country_id']) && $temp[$kk . '_country_id'] != "CA")
						$customer_address .= ' ' . $temp[$kk . '_country'] . '<br />';
					else
						$customer_address .= '<br />';
					$arr_address[$kk] = $customer_address;
				}
			}

			if (isset($arrtemp['name']) && $arrtemp['name'] != '') {
				$heading = $arrtemp['name'];
			} else {
				$heading = '';
			}

			if ($arrtemp['ship_to_contact_name']) {
				$ship_to_contact_name = $arrtemp['ship_to_contact_name'] . '<br>';
			} else {
				$ship_to_contact_name = '';
			}
			$this->set('customer_address', $customer . (isset($arr_address['invoice']) ? $arr_address['invoice'] : ''));
			$this->set('ship_to_contact_name', $ship_to_contact_name);


			if(!isset($arr_address['shipping']))
				$arr_address['shipping'] = '';
			$this->set('shipping_address', $arr_address['shipping']);

			$this->set('ref_no', $arrtemp['code']);
			if (isset($arrtemp['purchord_date']) && is_object($arrtemp['purchord_date']))
				$this->set('purchord_date', $this->opm->format_date($arrtemp['purchord_date']));
			if (isset($arrtemp['required_date']) && is_object($arrtemp['required_date']))
				$this->set('required_date', $this->opm->format_date($arrtemp['required_date']));

			//set content
			$date_now = date('Ymd');
			$filename = 'PUR' . $date_now . '-' . $arrtemp['code'];
			$this->set('filename', $filename);

			$this->set('heading', $heading);
			$html_cont = '';
			if (isset($arrtemp['products']) && is_array($arrtemp['products']) && count($arrtemp['products']) > 0) {
				$line = $qty_sum = $total = 0;
				$colum = 7;
				foreach ($arrtemp['products'] as $keys => $values) {
					if (!isset($values['deleted']) || !$values['deleted']) {
						if ($line % 2 == 0)
							$bgs = '#fdfcfa';
						else
							$bgs = '#eeeeee';
						//code
						$html_cont .= '<tr style="background-color:' . $bgs . ';"><td class="first">';
						if (isset($values['sku']))
							$html_cont .= '  ' . $values['sku'];
						else
							$html_cont .= '  #' . $keys;
						//desription
						$html_cont .= '</td><td>';
						if (isset($values['products_name']))
							$html_cont .= $values['products_name'];
						else
							$html_cont .= '#';
						//width
						$html_cont .= '</td><td align="right">';
						if (isset($values['sizew']) && $values['sizew'] != '' && isset($values['sizew_unit']) && $values['sizew_unit'] != '')
							$html_cont .= $values['sizew'] . ' (' . $values['sizew_unit'] . ')';
						else if (isset($values['sizew']) && $values['sizew'] != '')
							$html_cont .= $values['sizew'] . ' (in.)';
						else
							$html_cont .= '';
						//height
						$html_cont .= '</td><td align="right">';
						if (isset($values['sizeh']) && $values['sizeh'] != '' && isset($values['sizeh_unit']) && $values['sizeh_unit'] != '')
							$html_cont .= $values['sizeh'] . ' (' . $values['sizeh_unit'] . ')';
						else if (isset($values['sizeh']) && $values['sizeh'] != '')
							$html_cont .= $values['sizeh'] . ' (in.)';
						else
							$html_cont .= '';
						//Unit price
						$html_cont .= '</td><td align="right">';
						if (isset($values['unit_price']))
							$html_cont .= $this->opm->format_currency($values['unit_price']);
						else
							$html_cont .= '0.00';
						//Qty
						$html_cont .= '</td><td align="right">';
						if (isset($values['quantity']))
							$html_cont .= $values['quantity'];
						else
							$html_cont .= '';
						//line total
						$html_cont .= '</td><td align="right" class="end">';
						if (isset($values['sub_total']))
							$html_cont .= $this->opm->format_currency((float) $values['sub_total']);
						else
							$html_cont .= '';


						$html_cont .= '</td></tr>';
						$line++;
					}//end if deleted
				}//end for


				if ($line % 2 == 0) {
					$bgs = '#fdfcfa';
					$bgs2 = '#eeeeee';
				} else {
					$bgs = '#eeeeee';
					$bgs2 = '#fdfcfa';
				}

				$sub_total = $total = $taxtotal = 0.00;
				if (isset($arrtemp['sum_sub_total']))
					$sub_total = (float) $arrtemp['sum_sub_total'];
				if (isset($arrtemp['sum_tax']))
					$taxtotal = (float) $arrtemp['sum_tax'];
				if (isset($arrtemp['sum_amount']))
					$total = (float) $arrtemp['sum_amount'];
				//Sub Total
				$html_cont .= '<tr style="background-color:' . $bgs . ';">
									<td colspan="' . ($colum - 1) . '" align="right" style="font-weight:bold;border-top:2px solid #aaa;" class="first">Sub Total:</td>
									<td align="right" style="border-top:2px solid #aaa;" class="end">' . $this->opm->format_currency($sub_total) . '</td>
							   </tr>';
				//GST
				$html_cont .= '<tr style="background-color:' . $bgs2 . ';">
									<td colspan="' . ($colum - 1) . '" align="right" style="font-weight:bold;" class="first">HST/GST:</td>
									<td align="right" class="end">' . $this->opm->format_currency($taxtotal) . '</td>
							   </tr>';
				//Total
				$html_cont .= '<tr style="background-color:' . $bgs . ';">
									<td colspan="' . ($colum - 1) . '" align="right" style="font-weight:bold;" class="first bottom">Total:</td>
									<td align="right" class="end bottom">' . $this->opm->format_currency($total) . '</td>
							   </tr>';
			}//end if


			$this->set('html_cont', $html_cont);
			if (isset($arrtemp['our_rep'])) {
				$this->set('user_name', ' ' . $arrtemp['our_rep']);
			} else
				$this->set('user_name', ' ' . $this->opm->user_name());

			if (isset($arrtemp['job_number'])) {
				$this->set('job_number', ' ' . $arrtemp['job_number']);
			} else
				$this->set('job_number', ' ');
			//end set content
			//set footer
			$this->render('view_pdf');
			if($getfile)
				return $filename.'.pdf';
			$this->redirect('/upload/' . $filename . '.pdf');
		}
		die;
	}




	public function error_message($type='',$data)
	{
		if($type == 'company')
			echo json_encode(array('status'=>'error','message'=>'The function cannot be performed as there is no supplier linked to the purchase order.'));
		else if($type=='product')
		{
			if($data=='track_shipping')
				echo json_encode(array('status'=>'error','message'=>'There are no items on this purchase order with quantities to ship.'));
			else if($data=='receive_item')
				echo json_encode(array('status'=>'error','message'=>'There are no items on this purchase order with quantities to receive.'));
			else if($data=='return_item')
				echo json_encode(array('status'=>'error','message'=>'There are no items on this purchase order with quantities to be returned.'));
		}
		else
			echo json_encode(array('status'=>'ok'));
	}


	public function checkCondition()
	{
		$id = $this->get_id();
		$this->selectModel('Purchaseorder');
		$po = $this->Purchaseorder->select_one(array(
												'_id'=> new MongoId($id),
												// 'products'=> array('$elemMatch'=>array(
									// 									'deleted'=>false,
												),array('_id','company_id','products'));
		if(!is_object($po['company_id']))
			$this->error_message('company',$_POST['type']);
		else if(empty($po['products']))
		{
			$this->error_message('product',$_POST['type']);
		}
		else if(!empty($po['products']))
		{
			$flag = false;
			foreach($po['products'] as $products)
			{
				if($products['deleted']==false&&isset($products['quantity'])&&$products['quantity']>0)
				{
					$flag = true;
					break;

				}
			}
			if($flag)
				echo json_encode(array('status'=>'ok'));
			else
			{
				$this->error_message('product',$_POST['type']);
			}
		}
		die;
	}


	public function getShippingReceiveQuantity()
	{
		$id = $this->get_id();
		$po = $this->opm->select_one(array('_id' => new MongoId($id)));
		$html = '';
		if(isset($po['products'])&&!empty($po['products']))
		foreach($po['products'] as $key=>$product)
		{
			if(isset($product['receive_item'])&&!empty($product['receive_item']))
			{
				$html .= '<span id="receive_item_'.$key.'" style="background-color: white;font-weight: bold">';
				foreach($product['receive_item'] as $received)
					$html .= '<p>'.(isset($received['receive_date'])&&is_object($received['receive_date']) ? $this->opm->format_date($received['receive_date']->sec) : '').' | '.(isset($received['receive_by']) ? $received['receive_by'] : '').' | '.(isset($received['quantity']) ? $received['quantity'] : '').'</p>';
				$html .= '</span>';
			}
		}
		echo $html;
		die;
	}

	public function shipping_received() {
		$subdatas = array();

		$arr_set = $this->opm->arr_settings;
		//pr($arr_set);
		$fieldlist = $arr_set['relationship']['shipping_received']['block']['shipping']['field'];
		$this->selectModel('Shipping');
		$query = $this->Shipping->select_all(array(
													'arr_where'=>array(
																	'purchaseorder_id'=> new MongoId($this->get_id()),
																	'deleted'=>false,
														),
													'arr_order'=>array('code'=>1),
													'arr_field'=>array('code','shipping_type','return_status','shipping_date','our_rep','our_rep_id','carrier_name','carrier_id','tracking_no','name','shipping_status','products')
					));
		$shipping = array();

		if (isset($query) && $query->count() > 0) {
			foreach($query as $key=>$value)
			{
				$shipping[$key]['_id'] = $key;
				$shipping[$key]['shipping_id'] = $key;
				$shipping[$key]['no'] = $value['code'];
				$shipping[$key]['type'] = $value['shipping_type'];
				$shipping[$key]['return'] = $value['return_status'];
				$shipping[$key]['shipping_date'] = (isset($value['shipping_date'])&&$value['shipping_date']!='' ? $this->opm->format_date($value['shipping_date']->sec) : '');
				$shipping[$key]['our_rep'] = (isset($value['our_rep']) ? $value['our_rep'] : '');
				$shipping[$key]['carrier'] = 	(isset($value['carrier_name']) ? $value['carrier_name'] : '');
				$shipping[$key]['tracking_no'] = (isset($value['tracking_no']) ? $value['tracking_no']: '');
				$shipping[$key]['heading'] = (isset($value['name']) ? $value['name'] : '');
				$shipping[$key]['status'] = $value['shipping_status'];
			}
		}
		$subdatas['shipping'] = $shipping;
		$this->set('subdatas', $subdatas);
	}

	public function receive_item()
	{
		if(!$this->check_permission('products_@_entry_@_edit'))
			$this->error_auth();
		$id = $this->get_id();
		$update_products = array();
		$arr_purchaseorder = $this->opm->select_one(array('_id' => new MongoId($id)),array('products','code'));
		$this->selectModel('Location');
		$location = $this->Location->select_all(array(
			'arr_field' => array('_id','name','location_type','stock_usage'),
			'arr_order'	=> array('_id'=>1)
		));
		$arr_location = $all_location = array();
		foreach ($location as $key => $value){
			$arr_location[$key] = $value['name'];
			$all_location[$key] = $value;
		}
		if(isset($_POST['submit'])){
			$arr_products = $arr_purchaseorder['products'];
			$arr_post = $_POST;
			$arr_data = array();
			$current_user = $this->opm->user_name();
			$current_user_id = new MongoId($this->opm->user_id());
			$received_all = true;
			$parly_receive = false;
			foreach($arr_post as $key=>$value){
				$position = strrpos($key, '_',-1);
	            if($position===false) continue;
	            $k =substr($key, $position+1);
	            $key = str_replace('_'.$k, '', $key);
	            $arr_data[$k][$key] = $value;
			}
			foreach ($arr_data as $key => $value){
				if( !isset($value['receive_now']) || (int)$value['receive_now'] <1)continue;
				$value['receive_now'] = (int)$value['receive_now'];
				$arr_products[$key]['receive_item'][] = array(
					'deleted' => false,
					'receive_by' => $current_user,
					'receive_by_id' => $current_user_id,
					'quantity' => $value['receive_now'],
					'location_name' => $value['location_name'],
					'location_id' => new MongoId($value['location_id']),
					'receive_date' => new MongoDate(),
				);
				//for update po
				if (isset($arr_products[$key]['quantity_received']))
					$arr_products[$key]['quantity_received'] += $value['receive_now'];
				else
					$arr_products[$key]['quantity_received'] = $value['receive_now'];

				//Kiem tra da nhan du chua
				if(!isset($arr_products[$key]['quantity_returned']))
					$arr_products[$key]['quantity_returned'] = 0;
				$arr_products[$key]['balance_received'] = $arr_products[$key]['quantity_received'] - $arr_products[$key]['quantity_returned'];
				$arr_products[$key]['balance_shipped'] = $arr_products[$key]['balance_received'];
				$arr_products[$key]['balance_returned'] = $arr_products[$key]['quantity'] - $arr_products[$key]['balance_received'];
				$arr_products[$key]['quantity_shipped'] = (isset($arr_products[$key]['quantity_shipped']) ? $arr_products[$key]['quantity_shipped'] : 0);
				$arr_products[$key]['quantity_shipped'] += $value['receive_now'];
				if($arr_products[$key]['balance_received'])
					$received_all = false;
				//for update stock product
				if(isset($arr_products[$key]['products_id']) && is_object($arr_products[$key]['products_id'])){
					$update_products[$key]['_id'] = $arr_products[$key]['products_id'];
					$update_products[$key]['quantity'] = $value['receive_now'];

					if(isset($value['location_id']) && strlen($value['location_id']) == 24) {
						$update_products[$key]['location']['_id'] = new MongoId($value['location_id']);
						$update_products[$key]['location']['name'] = $value['location_name'];
						$update_products[$key]['location']['location_type'] = (isset($all_location[$value['location_name']]['location_type']) ? $all_location[$value['location_name']]['location_type'] : '');
						$update_products[$key]['location']['stock_usage'] = (isset($all_location[$value['location_name']]['stock_usage']) ? $all_location[$value['location_name']]['stock_usage'] : '');

						$first_location = reset($all_location);
						$update_products[$key]['location']['_id'] = $first_location['_id'];
						$update_products[$key]['location']['name'] = $first_location['name'];
						$update_products[$key]['location']['location_type'] = $first_location['location_type'];
						$update_products[$key]['location']['stock_usage'] = $first_location['stock_usage'];
					}
				}
				$parly_receive = true;
			}
			$arr_purchaseorder['products'] = $arr_products;
			if($received_all){
				$arr_purchaseorder['purchase_orders_status'] = 'Received';
				$arr_purchaseorder['delivery_date'] = new MongoDate();
				$arr_purchaseorder['received_by_contact_name'] = $current_user;
				$arr_purchaseorder['received_by_contact_id'] = $current_user_id;
			} else if($parly_receive)
				$arr_purchaseorder['purchase_orders_status'] = 'Partly Received';
			if($this->opm->save($arr_purchaseorder)){
				//update stock
				$this->selectModel('Product');
				$this->Product->update_stock($update_products);
				echo 'ok';
			}
			die;
		}
		$this->set('arr_location', $arr_location);
		$this->set('arr_purchaseorder', $arr_purchaseorder);

	}
	public function return_item(){
		if(!$this->check_permission('shippings_@_entry_@_add'))
			$this->error_auth();
		$id = $this->get_id();
		$arr_purchaseorder = $this->opm->select_one(array('_id' => new MongoId($id)));
		if(isset($_POST['submit'])){
			$this->selectModel('Location');
			$location = $this->Location->select_all(array(
				'arr_field' => array('_id','name','location_type','stock_usage'),
				'arr_order'	=> array('_id'=>1)
			));
			foreach ($location as $key => $value)
				$all_location[$key] = $value;
			//=================================================
			$arr_products = $arr_purchaseorder['products'];
			$arr_post = $_POST;
			$arr_data = $update_products = array();
			$current_user = $this->opm->user_name();
			$current_user_id = new MongoId($this->opm->user_id());
			$parly_receive = false;
			$shipping_products = array();
			foreach($arr_post as $key=>$value){
				$position = strrpos($key, '_',-1);
	            if($position===false) continue;
	            $k =substr($key, $position+1);
	            $key = str_replace('_'.$k, '', $key);
	            $arr_data[$k][$key] = $value;
			}
			foreach ($arr_data as $key => $value){
				if( !isset($value['return_now']) || (int)$value['return_now'] <1)continue;
				$value['return_now'] = (int)$value['return_now'];

				if(isset($arr_products[$key]['balance_returned'])
				   &&$value['return_now'] > (int)$arr_products[$key]['balance_returned'])
					$$value['return_now'] = (int)$arr_products[$key]['balance_returned'];
				$arr_products[$key]['return_item'][] = array(
					'deleted' => false,
					'return_date' 	=> 		new MongoDate(),
					'return_by' 	=> 		$current_user,
					'return_by_id'	=> 		$current_user_id,
					'quantity'		=> 		$value['return_now'],
				);
				//for update po
				if (isset($arr_products[$key]['quantity_returned']))
					$arr_products[$key]['quantity_returned'] += $value['return_now'];
				else
					$arr_products[$key]['quantity_returned'] = $value['return_now'];
				if(!isset($arr_products[$key]['quantity_received']))
					$arr_products[$key]['quantity_received'] = 0;
				$arr_products[$key]['balance_received'] = $arr_products[$key]['quantity_received'] - $arr_products[$key]['quantity_returned'];
				$arr_products[$key]['balance_shipped'] = $arr_products[$key]['balance_received'];
				$arr_products[$key]['balance_returned'] = $arr_products[$key]['quantity'] - $arr_products[$key]['balance_received'];

				//Product for create out shipping (return)
				$return_product = $arr_products[$key];
				$return_product['quantity'] = $value['return_now']['return_now'];
				$shipping_products[] = $return_product;
				$quantity_returned = 0;
				//for update stock product
				if(isset($arr_products[$key]['products_id'])&&is_object($arr_products[$key]['products_id'])){
					$update_products[$key]['_id'] = new MongoId($arr_products[$key]['products_id']);
					//Lay so luong cuoi cung de dua vao Product
					$update_products[$key]['quantity']  = $value['return_now'];
					$update_products[$key]['location']['_id'] = new MongoId('5297e68367b96d0e7200000c');
					$update_products[$key]['location']['name'] = "Main stock location";
					$update_products[$key]['location']['location_type'] = $all_location['5297e68367b96d0e7200000c']['location_type'];
					$update_products[$key]['location']['stock_usage'] = $all_location['5297e68367b96d0e7200000c']['stock_usage'];

				}
				$parly_receive = true;
			}
			if($parly_receive)
				$arr_purchaseorder['purchase_orders_status'] = 'Partly Received';
			$arr_purchaseorder['products'] = $arr_products;
			// check validate
		    if($this->opm->save($arr_purchaseorder)){
				//update stock
				$this->selectModel('Product');
				$this->Product->update_stock($update_products,'minus');
					//create shipping
				$this->selectModel('Shipping');
				$arr_shipping = $arr_purchaseorder;
				$arr_shipping['code'] = $this->Shipping->get_auto_code('code');
				$arr_shipping['carrier_id'] = '';
				$arr_shipping['carrier_name'] = '';
				$arr_shipping['received_date'] = '';
				$arr_shipping['return_status'] = 1;
				$arr_shipping['shipping_date'] = new MongoDate();
				$arr_shipping['shipping_status'] = 'Completed';
				$arr_shipping['shipping_type'] = 'Out';
				$this->selectModel('Company');
				$company = array();
				$shipping_address = array(
				                 'address_1'=>'',
				                 'address_2'=>'',
				                 'address_3'=>'',
				                 'town_city'=>'',
				                 'province_state'=>'',
				                 'province_state_id'=>'',
				                 'zip_postcode'=>'',
				                 'country'=>'',
				                 'country'=>'',
				                 'country_id'=>''
				                 );
				if(is_object($arr_purchaseorder['company_id'])){
					$company = $this->Company->select_one(array('_id'=> new MongoId($arr_purchaseorder['company_id'])),array('addresses'));
					$company['addresses_default_key'] = (isset($company['addresses_default_key']) ? $company['addresses_default_key'] : 0);
					$shipping_address = $company['addresses'][$company['addresses_default_key']];
					$arr_shipping['shipping_address'][0]['deleted'] = false;
					foreach($shipping_address as $key=>$value)
						$arr_shipping['shipping_address'][0]['shipping_'.$key] = $value;
				}
				$arr_shipping['invoice_address'] = array(
				                                         'deleted' => false,
				                                         'shipping_country' => 'CA'
				                                         );
				$arr_shipping['purchaseorder_id'] = new MongoId($id);
				$arr_shipping['purchaseorder_code'] = $arr_purchaseorder['code'];
				$arr_shipping['purchaseorder_name'] = $arr_purchaseorder['name'];
				$arr_shipping['products'] = $shipping_products;
				unset($arr_shipping['_id']);
				unset($arr_shipping['invoice_address']);
				unset($arr_shipping['delivery_date']);
				unset($arr_shipping['purchase_orders_status']);
				unset($arr_shipping['purchord_date']);
				unset($arr_shipping['required_date']);
				unset($arr_shipping['salesorder_id']);
				unset($arr_shipping['salesorder_name']);
				unset($arr_shipping['salesorder_number']);
				unset($arr_shipping['ship_to_company_id']);
				unset($arr_shipping['ship_to_company_name']);
				unset($arr_shipping['ship_to_contact_id']);
				unset($arr_shipping['ship_to_contact_name']);
				unset($arr_shipping['shipper_company_id']);
				unset($arr_shipping['shipper_company_name']);
				unset($arr_shipping['sum_amount']);
				unset($arr_shipping['sum_sub_total']);
				unset($arr_shipping['sum_tax']);
				unset($arr_shipping['tax']);
				$this->Shipping->save($arr_shipping);
				echo 'ok';
		   	}
		   	die;
		}
		$this->set('arr_purchaseorder', $arr_purchaseorder);
	}
	public function supplier_invoice(){

        $this->selectModel('Purchaseorder');
        $po = $this->Purchaseorder->select_one(array('_id'=> new MongoId($this->get_id())));
        $arr_data['current_user'] = $this->opm->user_name();
    	$arr_data['current_user_id'] = $this->opm->user_id();
    	$this->selectModel('Setting');
        $arr_data['salesinvoices_type'] = $this->Setting->select_option_vl(array('setting_value'=>'salesinvoices_type'));
        $arr_data['salesinvoices_status'] = $this->Setting->select_option_vl(array('setting_value'=>'salesinvoices_status'));
        unset($arr_data['salesinvoices_status']['In Progress'],$arr_data['salesinvoices_status']['No Charge ( Redo)']);
        $this->set('arr_data',$arr_data);
        $this->set('po',$po);

    }
    public function check_fully_supplier_invoice()
    {
        $this->selectModel('Purchaseorder');
        $po = $this->Purchaseorder->select_one(array('_id'=> new MongoId($this->get_id())));
        if(!isset($po['supplier_invoice']))
        {
            echo 'false';
            die;
        }
        else if(!empty($po['supplier_invoice']))
        {
            foreach($po['supplier_invoice'] as $supplier_invoice)
                if(isset($supplier_invoice['mod'])&&$supplier_invoice['mod']=='fully')
                {
                    echo 'true';
                    die;
                }
            echo 'false';
            die;
        }
        else
            echo 'false';
        die;

    }
    public function supplier_invoice_ajax()
    {
    	if(!$this->check_permission($this->name.'_@_entry_@_edit')){
    		echo 'You do not have permission on this action.';
    		die;
    	}
        if(isset($_POST['act']))
        {
        	$date = new MongoDate();
            $this->selectModel('Purchaseorder');
            $po = $this->Purchaseorder->select_one(array('_id'=> new MongoId($this->get_id())));
            if($_POST['act']=='add')
            {
            	$payment_terms = (isset($po['payment_terms'])&&$po['payment_terms']!='' ? $po['payment_terms'] : 0);
				$day_left = ($date->sec + $payment_terms*DAY - $date->sec)/DAY;
                if($_POST['type']=='part')
                {
                    $po['supplier_invoice'][] = array(
                                                'no'=>'',
                                                'date'=> $date,
                                                'type'=>'Invoice',
                                                'status'=>'Invoiced',
                                                //term lấy mặc định từ PO term
                                                'term'=>$payment_terms,
                                                //due date = date + term
                                                'due_date'=>new MongoDate($date->sec + $payment_terms * DAY),
                                                'day_left'=>$day_left,
                                                'due'=>($day_left <= 0 ? 1 : 0),
                                                'notes'=>'',
                                                'nc'=>'',
                                                'amount'=>'',
                                                'tax'=>'',
                                                'total_with_tax'=>0,
                                                'approved'=>0,
                                                'approved_by_name'=>'',
                                                'approved_by_id'=> '',
                                                'paid'=>0,
                                                'mod'=>'partly'
                        );
                }
                else if($_POST['type']=='full')
                {
                    $po['supplier_invoice'][] = array(
                                                'no'=>'',
                                                'date'=> new MongoDate(),
                                                'type'=>'Invoice',
                                                'status'=>'Invoiced',
                                                //term lấy mặc định từ PO term
                                                'term'=>$payment_terms,
                                                //due date = date + term
                                                'due_date'=>new MongoDate($date->sec + $payment_terms * DAY),
                                                'day_left'=>$day_left,
                                                'due'=>($day_left <= 0 ? 1 : 0),
                                                'notes'=>'',
                                                'nc'=>'',
                                                'amount'=>(isset($po['sum_sub_total']) ? (float)$po['sum_sub_total'] : 0),
                                                'tax'=>(isset($po['sum_tax']) ? (float)$po['sum_tax'] : 0),
                                                'total_with_tax'=>(isset($po['sum_amount']) ? (float)$po['sum_amount'] : 0),
                                                'approved'=>0,
                                                'approved_by_name'=>'',
                                                'approved_by_id'=> '',
                                                'paid'=>0,
                                                'mod'=>'fully'
                        );
                }
                $this->Purchaseorder->save($po);
                $this->supplier_invoice();
                $this->render('supplier_invoice');
            }
            else if($_POST['act']=='delete')
            {
                if(isset($_POST['key']))
                {
                    unset($po['supplier_invoice'][$_POST['key']]);
                    $this->Purchaseorder->save($po);
                	$this->supplier_invoice();
                	$this->render('supplier_invoice');
                }
            }
            else if($_POST['act']=='update')
            {
                if(isset($_POST['key']))
                {
                    $key = $_POST['key'];
                    parse_str($_POST['data'],$data);

                    $supplier_invoice = $po['supplier_invoice'][$key];
                    $supplier_invoice['no'] = (isset($data['no']) ? $data['no'] : '');
                    //Tạo mới luôn có date, nhập mới thì update, ko thì giữ lại gtri cũ
                    $supplier_invoice['date'] = (isset($data['date'])&&$data['date']!='' ? new MongoDate($this->Common->strtotime($data['date'].' 00:00:00')) : $supplier_invoice['date']);
                    //Mặc định là Invoice
                    $supplier_invoice['type'] = (isset($data['type'])&&$data['type']!='' ? $data['type'] : 'Invoice');
                    $supplier_invoice['term'] = (isset($data['term']) ? $data['term']   : (isset($supplier_invoice['term']) ? $supplier_invoice['term'] : ''));
                    //due_date = date + term
                    $supplier_invoice['due_date'] = ($supplier_invoice['term']!= '' ? new MongoDate($supplier_invoice['date']->sec + $supplier_invoice['term']* DAY)  : '');
                    $due_date = (is_object($supplier_invoice['due_date']) ? $supplier_invoice['due_date']->sec :'');
                    //day_left = current_date - due_date, ko có due_date thì rỗng
                    $supplier_invoice['day_left'] = '';
                    if($due_date!='')
                    	$supplier_invoice['day_left'] =  floor(($due_date - $date->sec)/DAY);
                    $supplier_invoice['notes'] = (isset($data['notes']) ? $data['notes'] : '');
                    $supplier_invoice['amount'] = (isset($data['amount'])&&$data['amount']!='' ? (float)$data['amount'] : 0);
                    //Nếu ko có tax thì total_with_tax = amount
                    $supplier_invoice['total_with_tax'] = $supplier_invoice['amount'];
                    //Nếu có total_with_tax = amount + tax
                    $supplier_invoice['tax'] = (isset($data['tax'])&&$data['tax']!='' ? (float)$data['tax'] : 0);
                    if($supplier_invoice['tax']!='')
                        $supplier_invoice['total_with_tax'] = (float)$supplier_invoice['amount'] + (float)$supplier_invoice['tax'];
                    if($supplier_invoice['type']=='Credit')
                    	$supplier_invoice['paid'] = $supplier_invoice['total_with_tax'];
                    $supplier_invoice['approved'] = (isset($data['approved'])&&$data['approved']==1 ? 1: 0);
                    if(isset($data['approved_by_name'])&&$data['approved_by_name']!='')
                        $supplier_invoice['approved_by_name'] = $data['approved_by_name'];
                    if(isset($data['approved_by_id'])&&$data['approved_by_id']!='')
                        $supplier_invoice['approved_by_id'] = $data['approved_by_id'];
                    if(isset($data['status'])&&$data['status']!='')
                    {
                    	if($supplier_invoice['status']!=$data['status']&&$data['status']=='Paid')
                    	{
                    		$supplier_invoice['day_left'] = 0;
                    		$supplier_invoice['paid'] = $supplier_invoice['total_with_tax'];
                    	}
                    	$supplier_invoice['status'] = $data['status'];
                    }
                    if(isset($data['paid'])&&$data['paid']!='')
                    {
                    	$supplier_invoice['paid'] = (float)$data['paid'];
						// if($supplier_invoice['paid']==0)
						// 	$supplier_invoice['paid'] = (float)$data['paid'];
						// else
						// 	$supplier_invoice['paid'] += (float)$data['paid'];
						if($supplier_invoice['type']!='Credit'&&$supplier_invoice['paid']>=$supplier_invoice['total_with_tax']&&$supplier_invoice['total_with_tax']!=0)
							$supplier_invoice['status'] = 'Paid';
                    }
                    //Day left >0 thì chưa due, và ngược lại
                    $supplier_invoice['due'] = ($supplier_invoice['day_left']<=0? 1: 0);
                    //Gan lai
					$po['supplier_invoice'][$key] = $supplier_invoice;
                    $this->Purchaseorder->save($po);
                    $this->supplier_invoice();
                	$this->render('supplier_invoice');
                }
            }

        }

    }
    public function supplier_invoice_report()
    {
    	$this->selectModel('Purchaseorder');
        $po = $this->Purchaseorder->select_one(array('_id'=> new MongoId($this->get_id())));
        $sum_amount = (isset($po['sum_amount'])&&$po['sum_amount']!= '' ? $po['sum_amount'] : 0);

        $html_loop = '';
        $html_loop .= '<table cellpadding="3" cellspacing="0" class="maintb">
						  <tr>
							 <td width="17%" class="first top">
								Supplier invoice #
							 </td>
							 <td width="7%" class="top">
								Type
							 </td>
							 <td width="15%" class="top">
								Date
							 </td>
							 <td width="7%" class="top">
								Term
							 </td>
							 <td width="15%" class="top">
								Due date
							 </td>
							 <td width="9%" class="top">
								Approved
							 </td>
							 <td width="15%" class="top">
							    Approved by
							 </td>
							 <td width="15%" class="end top" colspan="3">
							 	Amount
							 </td>
						  </tr>';
		$i = 0;
		$total_receipt = 0;
		$balance = 0;
        foreach($po['supplier_invoice'] as $supplier_invoice)
        {
        	$total_receipt += $supplier_invoice['amount'];
        	$color = ($i%2==0 ? '#eeeeee' : '#fdfcfa');
        	$html_loop .= '<tr style="background-color:'.$color.';">
        						<td class"first content" style="border-right: 1px solid #E5E4E3;border-left: 1px solid #E5E4E3;">'.$supplier_invoice['no'].'</td>
        						<td class="content">'.$supplier_invoice['type'].'</td>
        						<td class="content">'.$this->opm->format_date($supplier_invoice['date']->sec).'</td>
        						<td class="content">'.$supplier_invoice['term'].'</td>
        						<td class="content">'.(is_object($supplier_invoice['due_date']) ? $this->opm->format_date($supplier_invoice['due_date']) : '').'</td>
        						<td class="content">'.($supplier_invoice['approved']==1? '<strong>X</strong>' : '').'</td>
        						<td class="content">'.$supplier_invoice['approved_by_name'].'</td>
        						<td class="end content" align="right" colspan="3">'.($supplier_invoice['amount']!=0 ? $this->opm->format_currency($supplier_invoice['amount']) : '0.00').'</td>
        					</tr>';
        	$i++;
        }
        $balance = (float)$sum_amount - (float)$total_receipt;
        $color = ($i%2==0 ? '#eeeeee' : '#fdfcfa');
        $html_loop .= '	<tr style="background-color:'.$color.';">
        					<td class="first content" colspan="7" align="right"><strong>Total receipt:</strong></td>
        					<td class="end content" colspan="3"  align="right">'.$this->opm->format_currency($total_receipt).'</td>
        				</tr>
        				<tr style="background-color:'.$color.';">
        					<td class="first content" colspan="7" align="right"><strong>Total purchase order:</strong></td>
        					<td class="end content" colspan="3"  align="right">'.($this->opm->format_currency($sum_amount)).'</td>
        				</tr>
        				<tr style="background-color:'.$color.';">
        					<td class="first bottom content" colspan="7" align="right"><strong>Balance:</strong></td>
        					<td class="end bottom" colspan="3"  align="right">'.$this->opm->format_currency($balance).'</td>
        				</tr>';
        //
       	$html_loop .= '</table>';
       	$data['heading'] = '<table cellpadding="4" style="border: 1px solid grey; font-size: 12px; font-weight: bold; text-align: left">
        						<tr>
        							<td width="10%" align="right">PO no:</td>
        							<td width="20%" align="left">'.$po['code'].'</td>
        							<td width="10%" align="right">Supplier:</td>
        							<td width="20%" align="left">'.(isset($po['company_name']) ? $po['company_name'] : '').'</td>
        							<td width="10%" align="right"></td>
        							<td width="30%" align="left"></td>
        						</tr>
        						<tr>
        							<td width="10%" align="right">Total PO:</td>
        							<td width="20%" align="left">'.($this->opm->format_currency($sum_amount)).'</td>
        							<td width="10%" align="right">Invoices:</td>
        							<td width="20%" align="left">'.$this->opm->format_currency($total_receipt).'</td>
        							<td width="10%" align="right">Balance:</td>
        							<td width="30%" align="left">'.$this->opm->format_currency($balance).'</td>
        						</tr>
        					</table>';
        $pdf['current_time'] = date('h:i a m/d/Y');
		$pdf['title'] = '<span style="color:#b32017">I</span>nvoice receive for <span style="color:#b32017">PO</span> #'.$po['code'];
		$this->layout = 'pdf';
			//set header
		$pdf['logo_link'] = 'img/logo_anvy.jpg';
		$pdf['company_address'] = '3145 - 5th Ave NE<br />Calgary  AB  T2A  6A3<br />';
		$pdf['heading'] = $data['heading'];
		$pdf['html_loop'] = $html_loop;
		$pdf['filename'] = 'PO_'.md5($pdf['current_time']);

		$this->report_pdf($pdf);
		$this->redirect('/upload/'.$pdf['filename'].'.pdf');
		die;
    }
	function general_auto_save($id) {
		$arr_save=array();
		if (!empty($_POST)) {
			$arr_save['_id'] = new MongoId($id);
			$arr_save['other_comment'] = $_POST['content'];
			$error = 0;

			if (!$error) {
				if ($this->opm->save($arr_save)) {
					echo 'ok';
				} else {
					echo 'Error: ' . $this->opm->arr_errors_save[1];
				}
			}
		}
		die;
	}

	public function other() {
		$purchaseorder_id = $this->get_id();
		$this->set('arr_return', $this->opm->select_one(array('_id' => new MongoId($purchaseorder_id))));

		$this->selectModel('Setting');
		$arr_option_vl = $this->Setting->select_option_vl(array('setting_value'=>'com_type'));
		$this->set('arr_option_vl',$arr_option_vl);
		// BaoNam: gọi view ctp communications dùng chung
		$this->communications($purchaseorder_id, true);
	}

	public function add_from_product() {
		$this->redirect('/' . $this->params->params['controller'] . '/entry');
	}

	// Popup form orther module
	public function popup($key = '') {
		$this->set('key', $key);

		$limit = 100;
		$skip = 0;
		$cond = array();
		$this->identity($cond);
		// Nếu là search GET
		if (!empty($_GET)) {

			$tmp = $this->data;

			if (isset($_GET['company_id'])) {
				$cond['company_id'] = new MongoId($_GET['company_id']);
				$tmp['Purchaseorder']['company'] = $_GET['company_name'];
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

		$arr_order = array('date' => -1);
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
		if (!empty($this->data) && !empty($_POST) && isset($this->data['Purchaseorder'])) {
			$arr_post = $this->data['Purchaseorder'];

			if (isset($arr_post['contact_name']) && strlen($arr_post['contact_name']) > 0) {
				$cond['contact_name'] = new MongoRegex('/' . trim($arr_post['contact_name']) . '/i');
			}

			if (strlen($arr_post['company']) > 0) {
				$cond['company'] = new MongoRegex('/' . $arr_post['company'] . '/i');
			}
		}

		$this->selectModel('Purchaseorder');
		$arr_purchaseorder = $this->Purchaseorder->select_all(array(
			'arr_where' => $cond,
			'arr_order' => $arr_order,
			'limit' => $limit,
			'skip' => $skip
				// 'arr_field' => array('name', 'is_customer', 'is_employee', 'company_id', 'company_name')
		));
		$this->set('arr_purchaseorder', $arr_purchaseorder);

		$total_page = $total_record = $total_current = 0;
		if (is_object($arr_purchaseorder)) {
			$total_current = $arr_purchaseorder->count(true);
			$total_record = $arr_purchaseorder->count();
			if ($total_record % $limit != 0) {
				$total_page = floor($total_record / $limit) + 1;
			} else {
				$total_page = $total_record / $limit;
			}
		}
		$this->set('total_current', $total_current);
		$this->set('total_page', $total_page);
		$this->set('total_record', $total_record);

		$this->layout = 'ajax';
	}

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
		else if ($option == 'on_order') {
			$arr_where = array();
			$arr_where['purchase_orders_status'] = array('values' => 'On order', 'operator' => '=');
			$this->Session->write($this->name . '_where', $arr_where);
			echo URL . '/' . $this->params->params['controller'] . '/lists';
		}
		// find received
		else if ($option == 'received') {
			$arr_where = array();
			$arr_where['purchase_orders_status'] = array('values' => 'Received', 'operator' => '=');
			$this->Session->write($this->name . '_where', $arr_where);
			echo URL . '/' . $this->params->params['controller'] . '/lists';
		}

		else if ($option == 'cancelled') {
			$arr_where = array();
			$arr_where['purchase_orders_status'] = array('values' => 'Cancelled', 'operator' => '=');
			$this->Session->write($this->name . '_where', $arr_where);
			echo URL . '/' . $this->params->params['controller'] . '/lists';
		}
		//  find late
		else if ($option == 'late') {
			$arr_where = array();
			$arr_where['required_date'] = array('values' => $date, 'operator' => '<');
			$arr_where['purchase_orders_status'] = array(
													'values' => array('purchase_orders_status' => array('$nin' => array('Cancelled', 'Received', 'Returned', 'Approved for Payment'))),
													'operator' => 'other'
													);
			$this->Session->write($this->name . '_where', $arr_where);
			echo URL . '/' . $this->params->params['controller'] . '/lists';
		}
		else if ($option == 'shipped') {
			$arr_where = array();
			$arr_where['purchase_orders_status'] = array('values' => 'Shipped', 'operator' => '=');
			$this->Session->write($this->name . '_where', $arr_where);
			echo URL . '/' . $this->params->params['controller'] . '/lists';
		}
		else if ($option == 'duplicate_purchase_order') {
			$this->duplicate_purchase_order();
		}
		// ------------ end group find purchar order ------------ //

		else if ($option == 'print_purchase_order') {
			echo URL . '/' . $this->params->params['controller'] . '/view_pdf';
		}
		else if($option == 'create_shipping_return')
			echo URL . '/' . $this->params->params['controller'] . '/return_item';
		// pdf report supplier detailed
		else if ($option == 'report_by_supplier_summary') {
			echo URL . '/' . $this->params->params['controller'] . '/option_summary_supplier_find';
		}
		else if ($option == 'report_by_supplier_detailed') {
			echo URL . '/' . $this->params->params['controller'] . '/option_detailed_supplier_find';
		}
		else if ($option == 'report_by_product_summary') {
			echo URL . '/' . $this->params->params['controller'] . '/option_summary_product_find';
		}
		else if ($option == 'report_by_product_detailed') {
			echo URL . '/' . $this->params->params['controller'] . '/option_detailed_product_find';
		}
		else if ($option == 'report_by_category_detailed')
            echo URL . '/' . $this->params->params['controller'] . '/option_detailed_product_find/category';
        else if ($option == 'report_by_category_summary')
            echo URL . '/' . $this->params->params['controller'] . '/option_summary_product_find/category';
		else if ($option == 'print_mini_list') {
			echo URL . '/' . $this->params->params['controller'] . '/view_minilist';
		}
		else if ($option == 'receive_items') {
			echo URL . '/' . $this->params->params['controller'] . '/receive_item';
		}
		else if ($option == 'purchaseorder_invoice_receive_report'){
			$this->purchaseorder_invoice_receive_report();
		}
		else if ($option == 'email_purchase_order'){
			echo URL . '/' . $this->params->params['controller'] . '/create_email_pdf';
		}
		else if ($option == 'duplicate_current_purchase_order'){
			echo URL . '/' . $this->params->params['controller'] . '/duplicate_purchase_order';
		}
		else if ($keys == 'create_email')
            echo URL . '/' . $this->params->params['controller'] . '/add_from_module/'.$this->get_id().'/Email';
        else if ($keys == 'create_letter')
            echo URL . '/' . $this->params->params['controller'] . '/add_from_module/'.$this->get_id().'/Letter';
        else if ($keys == 'create_fax')
            echo URL . '/' . $this->params->params['controller'] . '/add_from_module/'.$this->get_id().'/Fax';

		// ------------ End For the found set of ------------ //
		die();
	}

	// get purchaseorder by id
	private function get_purcharse() {
		$id = $this->get_id();
		if ($id) {
			$query = $this->opm->select_one(array('_id' => new MongoId($id)));
			if ($query)
				return $query;
			else
				return null;
		}
		echo 'Error Purchase order not found';
		die();
	}

	public function find_option_supplier() {
		if ($this->request->is('ajax')) {
			$data = $_REQUEST['data'];
			pr($data);
			die();
		}
		$this->selectModel('Setting');
		$this->set('status', $this->Setting->select_option(array('setting_value' => 'purchase_orders_status'), array('option')));
	}

	public function report_supplier_summary() {
		$date_now = date('Y-m-d');
		$filename = 'report-supplier-summary-' . $date_now . '-' . $query->code;
		$this->set('filename', $filename);

		$this->render('report_supplier_summary');
		$this->redirect('/upload/' . $filename . '.pdf');
	}







	/*
	  Tung Report
	 */

	public function report_pdf($data) {

		App::import('Vendor', 'xtcpdf');
		$pdf = new XTCPDF();
		$textfont = 'freesans'; // looks better, finer, and more condensed than 'dejavusans'
		// set document information
		$pdf->SetCreator(PDF_CREATOR);
		$pdf->SetAuthor('Anvy Digital');
		$pdf->SetTitle('Anvy Digital Quotation');
		$pdf->SetSubject('Quotation');
		$pdf->SetKeywords('Quotation, PDF');

		// set default header data
		$pdf->setPrintHeader(false);
		$pdf->setPrintFooter(true);

		// set default monospaced font
		$pdf->SetDefaultMonospacedFont(2);

		// set margins
		$pdf->SetMargins(10, 3, 10);
		$pdf->SetHeaderMargin(PDF_MARGIN_HEADER);
		$pdf->SetFooterMargin(PDF_MARGIN_FOOTER);

		// set auto page breaks
		$pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);

		// set image scale factor
		$pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);

		// set some language-dependent strings (optional)
		if (@file_exists(dirname(__FILE__) . '/lang/eng.php')) {
			require_once(dirname(__FILE__) . '/lang/eng.php');
			$pdf->setLanguageArray($l);
		}

		// ---------------------------------------------------------
		// set font
		$pdf->SetFont($textfont, '', 9);

		// add a page
		$pdf->AddPage();


		// writeHTML($html, $ln=true, $fill=false, $reseth=false, $cell=false, $align='')
		// writeHTMLCell($w, $h, $x, $y, $html='', $border=0, $ln=0, $fill=0, $reseth=true, $align='', $autopadding=true)
		// create some HTML content


		$html = '
		<table cellpadding="2" cellspacing="0" style="width:100%; margin: 0px auto">
		   <tbody>
			  <tr>
				 <td width="32%" valign="top" style="color:#1f1f1f;">
					<img src="img/logo_anvy.jpg" alt="" margin-bottom:0px>
					<p style="margin-bottom:5px; margin-top:0px;">3145 - 5th Ave NE<br/ >Calgary  AB  T2A  6A3</p>
				 </td>
				 <td width="68%" valign="top" align="right">
					<table>
					   <tbody>
						  <tr>
							 <td width="10%">&nbsp;</td>
							 <td width="90%">
								<span style="text-align:right; font-size:21px; font-weight:bold; color: #919295;">
									' . $data['title'] . '<br />';
		if (isset($data['date_equals']))
			$date = '<span style="font-size:12px; font-weight:normal">' . $data['date_equals'] . '</span>';
		else {
			if (isset($data['date_from']) && isset($data['date_to']))
				$date = '<span style="font-size:12px; font-weight:normal">( ' . $data['date_from'] . ' - ' . $data['date_to'] . ' )</span>';
			else if (isset($data['date_from']))
				$date = '<span style="font-size:12px; font-weight:normal">From ' . $data['date_from'] . '</span>';
			else if (isset($data['date_to']))
				$date = '<span style="font-size:12px; font-weight:normal">To ' . $data['date_to'] . '</span>';
			else
				$date = '';
		}
		$html .= $date;
		$html .= '
								</span>
								<div style=" border-bottom: 1px solid #cbcbcb;height:5px">&nbsp;</div>
							 </td>
						  </tr>
						  <tr>
							 <td colspan="2">
									<span style="font-weight:bold;">Printed at: </span>' . $data['current_time'] . '
							 </td>
						  </tr>
					   </tbody>
					</table>
				 </td>
			  </tr>
		   </tbody>
		</table>
		<div class="option">' . @$data['heading'] . '</div>
		<div style="border-bottom: 1px dashed #9f9f9f; height:1px;"></div>
		<br />
		<style>
		   td{
		   line-height:2px;
		   }
		   td.first{
			text-align: center;
		   	border-left:1px solid #e5e4e3;
		   }
		   td.end{
		   border-right:1px solid #e5e4e3;
		   }
		   td.top{
		   color:#fff;
		   text-align: center;
		   font-weight:bold;
		   background-color:#911b12;
		   border-top:1px solid #e5e4e3;
		   }
		   td.bottom{
		   border-bottom:1px solid #e5e4e3;
		   }
		   td.content{
			border-right: 1px solid #E5E4E3;
			text-align: center;
		   }
		   .option{
		   color: #3d3d3d;
		   font-weight:bold;
		   font-size:20px;
		   text-align: center;
		   width:100%;
		   }
		   table.maintb{
		   }
		</style>
		<br />
		';
		$html .= $data['html_loop'];

		$pdf->writeHTML($html, true, false, true, false, '');



		// reset pointer to the last page
		$pdf->lastPage();



		// ---------------------------------------------------------
		// Close and output PDF document
		// This method has several options, check the source code documentation for more information.
		//$pdf->Output('example_001.pdf', 'I');




		$pdf->Output('upload/' . $data['filename'] . '.pdf', 'F');
	}

	public function option_summary_supplier_find() {
		$arr_data['purchaseorders_status'] = $this->Setting->select_option_vl(array('setting_value' => 'purchase_orders_status'));

		$this->set('arr_data', $arr_data);
	}

	public function option_detailed_supplier_find() {
		$arr_data['purchaseorders_status'] = $this->Setting->select_option_vl(array('setting_value' => 'purchase_orders_status'));

		$this->set('arr_data', $arr_data);
	}
	public function customer_report($type = ''){
		$arr_data = array();
        if(isset($_GET['print_pdf'])){
            $arr_data = Cache::read('purchaseorders_customer_report_'.$type);
            Cache::delete('purchaseorders_customer_report_'.$type);
        } else {
			if(isset($_POST) && !empty($_POST)){
				$arr_post = $_POST;
	            $arr_post = $this->Common->strip_search($arr_post);
	            $arr_where = array('company_id'=>array('$nin'=>array('',null)), 'deleted' => false);
				if(isset($arr_post['status'])&&$arr_post['status']!='')
					$arr_where['purchase_orders_status'] = $arr_post['status'];
				//Check loại trừ cancel thì bỏ các status bên dưới
				if(isset($arr_post['is_not_cancel']) && $arr_post['is_not_cancel']==1){
					$arr_where['purchase_orders_status'] = array('$nin'=> array('Cancelled'));
					//Tuy nhiên nếu ở ngoài combobox nếu có chọn, thì ưu tiên nó, set status lại
					if(isset($arr_post['status'])&&$arr_post['status']!='')
						$arr_where['purchase_orders_status'] = $arr_post['status'];

				}
				if(isset($arr_post['company']) && $arr_post['company']!='')
					$arr_where['company_name'] = new MongoRegex('/'.trim($arr_post['company']).'/i');
				if(isset($arr_post['contact']) && $arr_post['contact']!='')
					$arr_where['contact_name'] = new MongoRegex('/'.trim($arr_post['contact']).'/i');
				if(isset($arr_post['job_no']) && $arr_post['job_no']!='')
					$arr_where['job_number'] = new MongoRegex('/'.trim($arr_post['job_no']).'/i');
				//tim chinh xac ngay
				if(isset($arr_post['date_equals']) && $arr_post['date_equals']!=''){
					$arr_where['purchord_date']['$gte'] = new MongoDate($this->Common->strtotime($arr_post['date_equals'] . '00:00:00'));
					$arr_where['purchord_date']['$lt'] = new MongoDate($this->Common->strtotime($arr_post['date_equals'] . '23:59:59'));
				} else { //ngay nam trong khoang
					//neu chi nhap date from`
					if(isset($arr_post['date_from']) && $arr_post['date_from']){
						$arr_where['purchord_date']['$gte'] = new MongoDate($this->Common->strtotime($arr_post['date_from'] . '00:00:00'));
					}
					//neu chi nhap date to
					if( isset($arr_post['date_to']) && $arr_post['date_to'] ){
						$arr_where['purchord_date']['$lte'] = new MongoDate($this->Common->strtotime($arr_post['date_to'] . '23:59:59'));
					}
				}
				if(isset($arr_post['our_rep']) && $arr_post['our_rep']!='')
					$arr_where['our_rep'] = new MongoRegex('/'.$arr_post['our_rep'].'/i');
				if(isset($arr_post['our_csr']) && $arr_post['our_csr']!='')
					$arr_where['our_csr'] = new MongoRegex('/'.$arr_post['our_csr'].'/i');
				//lay het salesorder, voi where nhu tren va lay sum_amount giam dan
				$purchaseorders = $this->opm->select_all(array(
						'arr_where'=>$arr_where,
						'arr_order'=>array(
										'purchord_date'=>1
										),
						'arr_field'=>array('code','purchord_date','heading','purchase_orders_status','our_rep','sum_amount','company_id','our_rep','supplier_invoice'),
						'limit'	=> 999999
					));
				if($purchaseorders->count() == 0){
					echo 'empty';
					die;
				}
				else {
	                if ($arr_post['report_type'] == 'summary'){
                        $arr_data = $this->summary_customer_report($purchaseorders,$arr_post);
                        Cache::write('purchaseorders_customer_report_'.$type, $arr_data);
	                } else if ($arr_post['report_type'] == 'detailed'){
	                    $arr_data = $this->detailed_customer_report($purchaseorders,$arr_post);
	                    Cache::write('purchaseorders_customer_report_'.$type, $arr_data['pdf']);
	                    Cache::write('purchaseorders_customer_excel', $arr_data['excel']);
	                    $arr_data = $arr_data['pdf'];
	                } else{
	                    $arr_data = $this->summary_customer_report($purchaseorders,$arr_post);
	                    Cache::write('purchaseorders_customer_report_'.$type, $arr_data);
	                }

				}
			}
		}
		if($this->request->is('ajax'))
            die;
        else
            $this->render_pdf($arr_data);
	}
	function summary_customer_report($purchaseorders, $data) {

		//--------------------------------------
		$html = '';
        $i = $sum = 0;
        $arr_company = array();
        foreach($purchaseorders as $po){
        	if(!isset($po['company_id']))
        		$po['company_id'] = '';
        	$company_id = (string)$po['company_id'];
        	$arr_company[$company_id]['company_id'] = $po['company_id'];
        	if(!isset($arr_company[$company_id]['sum_amount']))
        		$arr_company[$company_id]['sum_amount'] = 0;
        	if( $po['purchase_orders_status'] == 'Cancelled')
        		$po['sum_amount'] = 0;
        	$arr_company[$company_id]['sum_amount'] += (isset($po['sum_amount']) ? (float)$po['sum_amount'] : 0);
        	if(!isset($arr_company[$company_id]['number_of_purchaseorders']))
        		$arr_company[$company_id]['number_of_purchaseorders'] = 0;
        	$arr_company[$company_id]['number_of_purchaseorders']++;
        }
        $this->selectModel('Company');
        foreach ($arr_company as $value) {
        	if(is_object($value['company_id']))
        		$company = $this->Company->select_one(array('_id'=>$value['company_id']),array('our_rep','name'));
            $html .= '
                <tr class="bg_' . ( $i%2==0 ? '1' : '2'). '">
                     <td>' . (isset($company['name']) ? $company['name'] : '') . '</td>
                     <td>' . (isset($company['our_rep']) ? $company['our_rep'] : '') . '</td>
                     <td class="right_text">' . $value['number_of_purchaseorders'] . '</td>
                     <td colspan="3" class="right_text">' . $this->opm->format_currency($value['sum_amount']) . '</td>
                </tr>
            ';
            $sum += ($value['sum_amount'] ? $value['sum_amount'] : 0);
            $i++;
        }
        $html .= '
                    <tr class="bg_' . ( $i%2==0 ? '1' : '2'). '">
                         <td colspan="2" class="bold_text right_none">' . $i . ' record(s) listed</td>
                         <td class="bold_text right_none right_text" >Total</td>
                         <td class="bold_text right_text">' . $this->opm->format_currency($sum) . '</td>
                    </tr>
                </table>
                ';
        //========================================
        //set header
        if($data['heading']!='')
            $arr_data['report_heading'] = $data['heading'];
        $arr_data['date_from_to'] = '';
        if(isset($data['date_from'])&&$data['date_from']!='')
            $arr_data['date_from_to'] .= '<span class="color_red bold_text">From</span> '.$data['date_from'].' ';
        if(isset($data['date_to'])&&$data['date_to']!='')
            $arr_data['date_from_to'] .= ' <span class="color_red bold_text">To</span> '.$data['date_to'];
        if(isset($data['date_equals'])&&$data['date_equals']!='')
            $arr_data['date_from_to'] .= $data['date_equals'];
        $arr_data['title'] = array('Company'=>'text-align: left','Our Rep'=>'text-align: left','No. of PO'=>'text-align: right;','Ex. Tax total'=>'text-align: right');
        $arr_data['content'] = $html;
        $arr_data['report_name'] = 'PO Report By Supplier (Summary)';
        $arr_data['report_file_name'] = 'PO_'.md5(time());
        return $arr_data;
	}
	function detailed_customer_report($purchaseorders, $data) {
        $i = $sum = 0;
        $html = '';
        //Loc ra nhung salesorder group theo company id
        $this->selectModel('Company');
        $number_of_purchaseorders = $total_sum_sub_total = 0;
        foreach($purchaseorders as $po){
        	if(!isset($po['company_id']))
        		$po['company_id'] = '';
        	$po['sum_amount'] = (isset($po['sum_amount']) ? (float)$po['sum_amount'] : 0);
        	if( $po['purchase_orders_status'] == 'Cancelled')
        		$po['sum_amount'] = 0;
        	$company_id = (string)$po['company_id'];
        	$arr_company[$company_id]['company_id'] = $po['company_id'];
        	if(!isset($arr_company[$company_id]['sum_amount']))
        		$arr_company[$company_id]['sum_amount'] = 0;
        	$arr_company[$company_id]['sum_amount'] += $po['sum_amount'];
        	if(!isset($arr_company[$company_id]['number_of_purchaseorders']))
        		$arr_company[$company_id]['number_of_purchaseorders'] = 0;
        	$arr_company[$company_id]['number_of_purchaseorders']++;
        	$supplier_invoice = '';
        	if(isset($po['supplier_invoice']) && !empty($po['supplier_invoice'])){
        		foreach($po['supplier_invoice'] as $value){
        			$supplier_invoice .= isset($value['no']) ? $value['no'].', ' : '';
        		}
        		$supplier_invoice = rtrim($supplier_invoice,', ');
        	}
        	$arr_company[$company_id]['purchaseorders'][$po['code']] = array(
        	                                                         'code' => $po['code'],
        	                                                         'purchord_date' => (isset($po['purchord_date'])&&is_object($po['purchord_date']) ? $this->opm->format_date($po['purchord_date']->sec) : ''),
        	                                                         'purchase_orders_status' => $po['purchase_orders_status'],
        	                                                         'sum_amount' => $po['sum_amount'],
        	                                                         'heading' => (isset($po['heading']) ? $po['heading'] : ''),
        	                                                         'supplier_invoice' => $supplier_invoice
        	                                                      );
        }
        $arr_companies = array();
        foreach ($arr_company as $key => $value) {
        	if(!isset($arr_companies[(string)$value['company_id']]) && is_object($value['company_id']))
        		 $arr_companies[(string)$value['company_id']] = $company = $this->Company->select_one(array('_id'=>$value['company_id']),array('name','our_rep'));
        	else
        		$company = $arr_companies[(string)$value['company_id']];
            $html .= '
            <table class="table_content">
               <tbody>
                  <tr class="tr_right_none" style="background: #911b12;color: white;height: 29px;line-height: 29px;font-weight: bold;">
                     <td width="35%">
                        Company
                     </td>
                     <td width="25%">
                        Our Rep
                     </td>
                     <td class="right_text" width="15%">
                        No. of PO
                     </td>
                     <td class="right_text" colspan="3">
                        Group total
                     </td>
                  </tr>
                  <tr class="bg_2">
                     <td>' . (isset($company['name']) ? $company['name'] : '') . '</td>
                     <td>' . (isset($company['our_rep']) ? $company['our_rep'] : '') . '</td>
                     <td class="right_text">' . $value['number_of_purchaseorders'] . '</td>
                     <td colspan="3" class="right_text">' . $this->opm->format_currency($value['sum_amount']) . '</td>
                  </tr>
               </tbody>
            </table>';
            $html .= '<table class="table_content" >
                        <tbody>
                          <tr class="tr_right_none" style="background: #979797;color: white;height: 29px;line-height: 29px;font-weight: bold;">
                             <td width="15%">
                                Date
                             </td>
                             <td>
                             	Supplier Invoice
                             </td>
                             <td width="7%">
                                PO#
                             </td>
                             <td width="15%">
                                Status
                             </td>
                             <td class="right_text" colspan="3" width="18%">
                                Total
                             </td>
                          </tr>';
            $i = 0;
            $sum = 0;
            foreach ($value['purchaseorders'] as $key => $purchaseorder) {
                $sum += $purchaseorder['sum_amount'];
                $html .= '
                      <tr class="content_asset bg_' . ($i % 2 == 0 ? '1' : '2') . '">
                         <td>' . $purchaseorder['purchord_date'] . '</td>
                         <td>' . $purchaseorder['supplier_invoice'] . '</td>
                         <td>' . $purchaseorder['code'] . '</td>
                         <td>' . $purchaseorder['purchase_orders_status'] . '</td>
                         <td colspan="3" class="right_text">' . $this->opm->format_currency($purchaseorder['sum_amount']) . '</td>
                      </tr>';
                $i++;
            }
            $number_of_purchaseorders += $i;
            $total_sum_sub_total += $sum;
            $html .= '
                            <tr class="bg_' . ($i % 2 == 0 ? '1' : '2') . '">
                             <td colspan="4" class="left_text bold_text right_none">' . $i . ' record(s) listed</td>
                             <td class="bold_text right_text right_none">Total</td>
                             <td colspan="3" class="bold_text right_text">' . $this->opm->format_currency($sum) . '</td>
                          </tr>
                        </tbody>
                    </table>
                    <br />
                    <br />';
        }
        $html .= '
            	<div class="line" style="margin-bottom: 5px;"></div>
        		<table class="table_content">
        			<tr style="background-color: #333; color: white">
        				<td class="bold_text right_none" width="70%">'.$number_of_purchaseorders.' record(s) listed</td>
        				<td class="right_text bold_text right_none" >Totals</td>
        				<td class="right_text bold_text" width="15%">'.$this->opm->format_currency($total_sum_sub_total).'</td>
        			</tr>
        		</table>';
        //========================================
        //set header
        if($data['heading']!='')
            $arr_data['report_heading'] = $data['heading'];
        $arr_data['date_from_to'] = '';
        if(isset($data['date_from'])&&$data['date_from']!='')
            $arr_data['date_from_to'] .= '<span class="color_red bold_text">From</span> '.$data['date_from'].' ';
        if(isset($data['date_to'])&&$data['date_to']!='')
            $arr_data['date_from_to'] .= ' <span class="color_red bold_text">To</span> '.$data['date_to'];
        if(isset($data['date_equals'])&&$data['date_equals']!='')
            $arr_data['date_from_to'] .= $data['date_equals'];
        $arr_data['content'][]['html'] = $html;
        $arr_data['is_custom'] = true;
        $arr_data['image_logo'] = true;
        $arr_data['report_name'] = 'PO Report By Supplier (Detailed)';
        $arr_data['report_file_name'] = 'PO_'.md5(time());
        $arr_data['excel_url'] = URL.'/purchaseorders/detailed_customer_excel';;
        $arrData['pdf'] = $arr_data;
        $arrData['excel'] = $arr_company;
        return $arrData;
    }
    function detailed_customer_excel()
    {
    	$arr_orders = Cache::read('purchaseorders_customer_excel');
        Cache::delete('purchaseorders_customer_excel');
        if(!$arr_orders){
            echo 'No data';die;
        }
        App::import('Vendor', 'phpexcel/PHPExcel');
        $objPHPExcel = new PHPExcel();
        // Set document properties
        $objPHPExcel->getProperties()->setCreator("")
                                     ->setLastModifiedBy("")
                                     ->setTitle("Purchase Order Report By Supplier")
                                     ->setSubject("Purchase Order Report By Supplier")
                                     ->setDescription("Purchase Order Report By Supplier")
                                     ->setKeywords("Purchase Order Report By Supplier")
                                     ->setCategory("Purchase Order Report By Supplier");
        $worksheet = $objPHPExcel->getActiveSheet();
        $i = 2;
        $arr_companies = array();
        $this->selectModel('Company');
        $total_sum = $total_count = 0;
        foreach($arr_orders as $order) {
        	if(!isset($arr_companies[(string)$order['company_id']]) && is_object($order['company_id']))
        		 $arr_companies[(string)$order['company_id']] = $company = $this->Company->select_one(array('_id'=>$order['company_id']),array('name','our_rep'));
        	else
        		$company = $arr_companies[(string)$order['company_id']];

	        $worksheet->setCellValue("A$i",'Company')
	                        ->setCellValue("C$i",'Our Ref')
	                        ->setCellValue("D$i",'No. of PO')
	                        ->setCellValue("E$i",'Group total')
	                        ->mergeCells("A$i:B$i");
	        $from = $i;
	        ++$i;
	        $worksheet->setCellValue("A$i",(isset($company['name']) ? $company['name'] : ''))
	                        ->setCellValue("C$i",(isset($company['our_rep']) ? $company['our_rep'] : ''))
	                        ->setCellValue("D$i",$order['number_of_purchaseorders'])
	                        ->setCellValue("E$i",$order['sum_amount'])
	                        ->mergeCells("A$i:B$i");
	        $styleArray = array(
                'fill' => array(
		            'type' => PHPExcel_Style_Fill::FILL_SOLID,
		            'color' => array('rgb' => '538DD5')
		        ),
                'font'  => array(
                    'size'  => 12,
                    'name'  => 'Century Gothic',
                    'bold'  => true,
                    'color' => array('rgb' => 'FFFFFF'),
                )
	        );
	        $worksheet->getStyle("A$from:E$i")->applyFromArray($styleArray);
	        $sum = 0;
	        $count = 0;
            ++$i;
            $worksheet->setCellValue("A$i",'Date')
                        ->setCellValue("B$i",'Supplier Invoice')
                        ->setCellValue("C$i",'PO#')
                        ->setCellValue("D$i",'Status')
                        ->setCellValue("E$i",'Total');
	        foreach ($order['purchaseorders'] as $key => $purchaseorder) {
                ++$i;
            	$sum += $purchaseorder['sum_amount'];
	            $worksheet->setCellValue("A$i", $purchaseorder['purchord_date'])
	                        ->setCellValue("B$i", $purchaseorder['supplier_invoice'])
	                        ->setCellValue("C$i", $purchaseorder['code'])
	                        ->setCellValue("D$i", $purchaseorder['purchase_orders_status'])
	                        ->setCellValue("E$i", $purchaseorder['sum_amount']);
                ++$count;
            }
            $total_count += $count;
            $total_sum += $sum;
            ++$i;
            $worksheet->setCellValue("A$i","$count record(s) listed")
	                        ->setCellValue("E$i",$sum)
	                        ->mergeCells("A$i:D$i");
	        $styleArray = array(
                'borders' => array(
                   'allborders' => array(
                       'style' => PHPExcel_Style_Border::BORDER_THIN
                    )
                ),
                'font'  => array(
                    'size'  => 11,
                    'name'  => 'Century Gothic',
                )
	        );
	        $from+=2;
	        $worksheet->getStyle("A$from:E$i")->applyFromArray($styleArray);
	        $i+=2;
        }
        $styleArray = array(
                'font'  => array(
                    'size'  => 11,
                    'name'  => 'Century Gothic',
                )
	        );
        $worksheet->setCellValue("A$i", "$total_count record(s) listed")
                        ->setCellValue("E$i", $total_sum)
                        ->mergeCells("A$i:D$i");
	    $worksheet->getStyle("A$from:E$i")->applyFromArray($styleArray);
	    $worksheet->getStyle("E2:E$i")->getNumberFormat()->setFormatCode("#,##0.00");
        for($i = 'A'; $i !== 'F'; $i++){
            $worksheet->getColumnDimension($i)
                            ->setAutoSize(true);
        }
        $objPHPExcel->setActiveSheetIndex(0);
        $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
        $objWriter->save(APP.DS.'webroot'.DS.'upload'.DS.'po_report_by_supplier.xlsx');
        $this->redirect('/upload/po_report_by_supplier.xlsx');
        die;

    }
	public function option_summary_product_find($type = ''){
		$arr_data['purchaseorders_status'] = $this->Setting->select_option_vl(array('setting_value' => 'purchase_orders_status'));
		$arr_data['product_category'] = $this->Setting->select_option_vl(array('setting_value'=>'product_category'));
		$this->set('arr_data',$arr_data);
		if($type=='category')
            $this->render('../Purchaseorders/option_summary_category_find');
	}
	public function option_detailed_product_find($type = ''){
		$arr_data['purchaseorders_status'] = $this->Setting->select_option_vl(array('setting_value' => 'purchase_orders_status'));
		$arr_data['product_category'] = $this->Setting->select_option_vl(array('setting_value'=>'product_category'));
		$this->set('arr_data',$arr_data);
		if($type=='category')
            $this->render('../Purchaseorders/option_detailed_category_find');
	}
	public function get_cate_product($value) {
		$cate = $this->Setting->select_option_vl(array('setting_value' => 'product_category'));
		if(isset($cate[$value]))
			echo $cate[$value];
		else
			echo '';
		die();
	}
	public function product_report($type = ''){
        $arr_data = array();
        if(isset($_GET['print_pdf'])){
            $arr_data = Cache::read('purchaseorders_product_report_'.$type);
        } else {
            if(isset($_POST)){
                $data['product_category'] = $this->Setting->select_option_vl(array('setting_value'=>'product_category'));
                $arr_post = $_POST;
                $arr_post = $this->Common->strip_search($arr_post);
                $arr_where = array();
                $arr_where['products']['$ne'] = '';
                if(isset($arr_post['status']) && $arr_post['status'] != '')
                    $arr_where['purchase_orders_status'] = $arr_post['status'];
                //Check loại trừ cancel thì bỏ các status bên dưới
                if(isset($arr_post['is_not_cancel'])&&$arr_post['is_not_cancel']==1){
                    $arr_where['purchase_orders_status'] = array('$nin'=>array('Cancelled'));
                    //Tuy nhiên nếu ở ngoài combobox nếu có chọn, thì ưu tiên nó, set status lại
                    if(isset($arr_post['status'])&&$arr_post['status']!='')
                        $arr_where['purchase_orders_status'] = $arr_post['status'];
                }
                if(isset($arr_post['company']) && $arr_post['company']!='')
                    $arr_where['company_name'] = new MongoRegex('/'.trim($arr_post['company']).'/i');
                if(isset($arr_post['contact']) &&$arr_post['contact']!='')
                    $arr_where['contact_name'] = new MongoRegex('/'.trim($arr_post['contact']).'/i');
                if(isset($arr_post['job_no']) && $arr_post['job_no']!='')
                    $arr_where['job_number'] = trim($arr_post['job_no']);
                if(isset($arr_post['employee']) && trim($arr_post['employee'])!=''){
                    $arr_where['$or'][]['our_rep'] = new MongoRegex('/'.trim($arr_post['employee']).'/i');
                    $arr_where['$or'][]['our_csr'] = new MongoRegex('/'.trim($arr_post['employee']).'/i');
                }
                //Tìm chính xác ngày
                //Vì để = chỉ tìm đc 01/01/1969 00:00:00 nên phải cộng cho 23:59:59 rồi tìm trong khoảng đó
                if(isset($arr_post['date_equals'])&&$arr_post['date_equals']!=''){
                    $date_equals = new MongoDate(strtotime(date('Y-m-d',strtotime($arr_post['date_equals']))));
                    $date_equals_to = new MongoDate($date_equals->sec + DAY);
                    $arr_where['purchord_date']['$gte'] = $date_equals;
                    $arr_where['purchord_date']['$lt'] = $date_equals_to;
                } else{  //Ngày nằm trong khoảng
                    //neu chi nhap date from
                    if(isset($arr_post['date_from']) && $arr_post['date_from'] != ''){
                        $date_from = new MongoDate(strtotime(date('Y-m-d',strtotime($arr_post['date_from']))));
                        $arr_where['purchord_date']['$gte'] = $date_from;
                    }
                    //neu chi nhap date to
                    if(isset($arr_post['date_to']) && $arr_post['date_to'] != ''){
                        $date_to = new MongoDate(strtotime(date('Y-m-d',strtotime($arr_post['date_to']))));
                        $date_to = new MongoDate($date_to->sec + DAY -1);
                        $arr_where['purchord_date']['$lte'] = $date_to;
                    }
                }
                //Kiểm tra nếu có thông tin liên quan đến product tồn tại
                $pro_where = array();
                if(isset($arr_post['product'])&&$arr_post['product']!='')
                    $pro_where['code'] = trim($arr_post['product']);
                if(isset($arr_post['name'])&&$arr_post['name']!='')
                    $pro_where['name'] = new MongoRegex('/' . trim($arr_post['name']) . '/i');
                if(isset($arr_post['category_id'])&&$arr_post['category_id']!='')
                    $pro_where['category'] = new MongoRegex('/'.$arr_post['category_id'].'/i');
                $pro_list = array();
                $arr_products_where = array();
                $arr_products_where['products.deleted'] = $arr_where['deleted'] = false;
                if(isset($arr_post['sell_price_from'])&&$arr_post['sell_price_from']!=''){
                    $arr_where['products']['$elemMatch']['sell_price']['$gte'] = (float)$arr_post['sell_price_from'];
                    $arr_products_where['products.unit_price']['$gte'] = (float)$arr_post['sell_price_from'];
                }
                if(isset($arr_post['sell_price_to'])&&$arr_post['sell_price_to']!=''){
                    $arr_where['products']['$elemMatch']['sell_price']['$lte'] = (float)$arr_post['sell_price_to'];
                    $arr_products_where['products.unit_price']['$lte'] = (float)$arr_post['sell_price_to'];
                }
                if(!empty($pro_where)){
                    //Lấy ra _id của Product phù hợp với điều kiện trên
                    $this->selectModel('Product');
                    $pro_list = $this->Product->select_all(array(
                                            'arr_where'=>$pro_where,
                                            'arr_field'=>array('_id')
                        ));
                    foreach($pro_list as $p_id){
                       $arr_where['products']['$elemMatch']['products_id']['$in'][] = new MongoId($p_id['_id']);
                       $arr_products_where['products.products_id']['$in'][] = new MongoId($p_id['_id']);
                    }
                }
                $arr_where['products']['$elemMatch']['deleted'] = false;
                $arr_purchaseorders = $this->opm->collection->aggregate(
                        array(
                            '$match'=>$arr_where,
                        ),
                        array(
                            '$unwind'=>'$products',
                        ),
                         array(
                            '$match'=>$arr_products_where
                        ),
                        array(
                            '$project'=>array('status'=>'$status','code'=>'$code','company_name'=>'$company_name','company_id'=>'$company_id','purchord_date'=>'$purchord_date','sum_sub_total'=>'$sum_sub_total','products'=>'$products')
                        ),
                        array(
                            '$group'=>array(
                                          '_id'=>array('_id'=>'$_id','status'=>'$status','code'=>'$code','company_name'=>'$company_name','company_id'=>'$company_id','purchord_date'=>'$purchord_date','sum_sub_total'=>'$sum_sub_total'),
                                          'products'=>array('$push'=>'$products')
                                        )
                        )
                    );
                if(empty($arr_purchaseorders['result'])) {
                    echo 'empty';
                    die;
                } else {
                    $arr_purchaseorders = $arr_purchaseorders['result'];
                    if ($arr_post['report_type'] == 'summary'){
                        $arr_data = $this->summary_product_report($arr_purchaseorders,$arr_post);
                        Cache::write('purchaseorders_product_report_'.$type, $arr_data);
                    }
                    else if ($arr_post['report_type'] == 'detailed'){
                        $arr_data = $this->detailed_product_report($arr_purchaseorders,$arr_post);
                        Cache::write('purchaseorders_product_report_'.$type, $arr_data);
                    }
                    else if($arr_post['report_type'] == 'category_summary'){
                        $arr_data = $this->summary_category_product_report($arr_purchaseorders,$arr_post);
                        Cache::write('purchaseorders_product_report_'.$type, $arr_data);
                    }
                    else if($arr_post['report_type'] == 'category_detailed'){
                        $arr_data = $this->detailed_category_product_report($arr_purchaseorders,$arr_post);
                        Cache::write('purchaseorders_product_report_'.$type, $arr_data);
                    }

                }

            }
        }
        if($this->request->is('ajax'))
            die;
        else
            $this->render_pdf($arr_data);
    }
	public function summary_product_report($arr_purchaseorders,$data){
        $html = '';
        $i = $sum = 0;
        $this->selectModel('Product');
        $category = $this->Setting->select_option_vl(array('setting_value' => 'product_category'));
        $arr_data = array();
        foreach($arr_purchaseorders as $purchaseorder){
            foreach($purchaseorder['products'] as $product){
                $product['code'] = (isset($product['code']) ? $product['code'] : 'empty');
                $arr_data[$product['code']]['products_name'] = $product['products_name'];
                $arr_data[$product['code']]['code'] = $product['code'];
                $arr_data[$product['code']]['products_id'] = $product['products_id'];
                if(!isset($arr_data[$product['code']]['quantity']))
                    $arr_data[$product['code']]['quantity'] = 0;
                $arr_data[$product['code']]['quantity'] += $product['quantity'];
                if(!isset($arr_data[$product['code']]['sub_total']))
                    $arr_data[$product['code']]['sub_total'] = 0;
                $arr_data[$product['code']]['sub_total'] += (isset($product['sub_total']) ? (float)$product['sub_total'] : 0);
            }
        }
        foreach ($arr_data as $value) {
            if(is_object($value['products_id']))
                $product = $this->Product->select_one(array('_id'=>new MongoId($value['products_id'])),array('category'));
            if (!isset($product['category']))
                $product['category'] = '';
            $html .= '
                <tr style="background-color:' . ( $i%2==0 ? '#eeeeee' : '#fdfcfa'). ';">
                     <td>' . $value['code'] . '</td>
                     <td>' . $value['products_name'] . '</td>
                     <td>' . (isset($category[$product['category']]) ? $category[$product['category']] : '') . '</td>
                     <td class="right_text">' . $value['quantity'] . '</td>
                     <td colspan="3" class="right_text">' . $this->opm->format_currency($value['sub_total']) . '</td>
                </tr>
            ';
            $sum += ($value['sub_total'] ? $value['sub_total'] : 0);
            $i++;
        }
        $html .= '
                    <tr style="background-color:' . ( $i%2==0 ? '#eeeeee' : '#fdfcfa') . ';">
                         <td colspan="3" class="bold_text right_none">' . $i . ' record(s) listed</td>
                         <td class="bold_text right_none right_text" >Total</td>
                         <td class="bold_text right_text">' . $this->opm->format_currency($sum) . '</td>
                    </tr>
                </table>
                ';
        //========================================
        //set header
        if($data['heading']!='')
            $arr_pdf['report_heading'] = $data['heading'];
        $arr_pdf['date_from_to'] = '';
        if(isset($data['date_from'])&&$data['date_from']!='')
            $arr_pdf['date_from_to'] .= '<span class="color_red bold_text">From</span> '.$data['date_from'].' ';
        if(isset($data['date_to'])&&$data['date_to']!='')
            $arr_pdf['date_from_to'] .= ' <span class="color_red bold_text">To</span> '.$data['date_to'];
        if(isset($data['date_equals'])&&$data['date_equals']!='')
            $arr_pdf['date_from_to'] .= $data['date_equals'];
        $arr_pdf['title'] = array('P. Code'=>'text-align: left','Product Name'=>'text-align: left','Category'=>'text-align: left','Qty'=>'text-align: right;','Ex. Tax total'=>'text-align: right');
        $arr_pdf['content'] = $html;
        $arr_pdf['report_name'] = 'PO Report By Product (Summary)';
        $arr_pdf['report_file_name'] = 'PO_'.md5(time());
        return $arr_pdf;
    }
    public function summary_category_product_report($arr_purchaseorders,$data){
        $html = '';
        $i = $sum = 0;
        $this->selectModel('Product');
        $category = $this->Setting->select_option_vl(array('setting_value' => 'product_category'));
        $arr_data = array();
        foreach($arr_purchaseorders as $purchaseorder){
            foreach($purchaseorder['products'] as $product){
                $product_category = '(empty)';
                if(is_object($product['products_id'])){
                    $_product = $this->Product->select_one(array('_id'=> new MongoId($product['products_id'])),array('category'));
                    if (isset($_product['category']))
                        $product_category = (isset($category[$_product['category']]) ? $category[$_product['category']] : '(empty)');
                }
                $arr_data[$product_category]['category_name'] = $product_category;
                if(!isset($arr_data[$product_category]['quantity']))
                    $arr_data[$product_category]['quantity'] = 0;
                $arr_data[$product_category]['quantity'] += $product['quantity'];
                if(!isset($arr_data[$product_category]['sub_total']))
                    $arr_data[$product_category]['sub_total'] = 0;
                $arr_data[$product_category]['sub_total'] += (isset($product['sub_total']) ? (float)$product['sub_total'] : 0);
            }
        }
        foreach ($arr_data as $value) {
            $html .= '
                <tr style="background-color:' . ( $i%2==0 ? '#eeeeee' : '#fdfcfa'). ';">
                     <td>' . $value['category_name'] . '</td>
                     <td class="right_text">' . $value['quantity'] . '</td>
                     <td colspan="2" class="right_text">' . $this->opm->format_currency($value['sub_total']) . '</td>
                </tr>
            ';
            $sum += ($value['sub_total'] ? $value['sub_total'] : 0);
            $i++;
        }
        $html .= '
                    <tr style="background-color:' . ( $i%2==0 ? '#eeeeee' : '#fdfcfa') . ';">
                         <td class="bold_text right_none">' . $i . ' record(s) listed</td>
                         <td class="bold_text right_none right_text" >Total</td>
                         <td class="bold_text right_text">' . $this->opm->format_currency($sum) . '</td>
                    </tr>
                </table>
                ';
        //========================================
        //set header
        if($data['heading']!='')
            $arr_pdf['report_heading'] = $data['heading'];
        $arr_pdf['date_from_to'] = '';
        if(isset($data['date_from'])&&$data['date_from']!='')
            $arr_pdf['date_from_to'] .= '<span class="color_red bold_text">From</span> '.$data['date_from'].' ';
        if(isset($data['date_to'])&&$data['date_to']!='')
            $arr_pdf['date_from_to'] .= ' <span class="color_red bold_text">To</span> '.$data['date_to'];
        if(isset($data['date_equals'])&&$data['date_equals']!='')
            $arr_pdf['date_from_to'] .= $data['date_equals'];
        $arr_pdf['title'] = array('Category Name'=>'text-align: left','Qty'=>'text-align: right;','Ex. Tax total'=>'text-align: right');
        $arr_pdf['content'] = $html;
        $arr_pdf['report_name'] = 'PO Report By Category Product (Summary)';
        $arr_pdf['report_file_name'] = 'PO_'.md5(time());
        return $arr_pdf;
    }
	public function detailed_product_report($arr_purchaseorders,$data){
        $i = $sum = 0;
        $html = '';
        $this->selectModel('Product');
        $category = $this->Setting->select_option_vl(array('setting_value' => 'product_category'));
        $total_num_of_purchaseorders = $total_sum_sub_total = 0;
        $arr_data = $arr_pdf = array();
        foreach($arr_purchaseorders as $purchaseorder){
            foreach($purchaseorder['products'] as $product){
                $product['code'] = (isset($product['code']) ? $product['code'] : '(empty)');
                $arr_data[$product['code']]['products_name'] = $product['products_name'];
                $arr_data[$product['code']]['code'] = $product['code'];
                $arr_data[$product['code']]['products_id'] = $product['products_id'];
                if(!isset($arr_data[$product['code']]['quantity']))
                    $arr_data[$product['code']]['quantity'] = 0;
                $arr_data[$product['code']]['quantity'] += $product['quantity'];
                if(!isset($arr_data[$product['code']]['sub_total']))
                    $arr_data[$product['code']]['sub_total'] = 0;
                $arr_data[$product['code']]['sub_total'] += (isset($product['sub_total']) ? (float)$product['sub_total'] : 0);
                if(!isset($arr_data[$product['code']]['quantity']))
                    $arr_data[$product['code']]['quantity'] = 0;
                $arr_data[$product['code']]['quantity'] += (isset($product['quantity']) ? (float)$product['quantity'] : 0);
                $arr_data[$product['code']]['purchaseorders'][] = array_merge($purchaseorder['_id'], array('unit_price'=>$product['unit_price'],'quantity'=>$product['quantity'],'sub_total'=>$product['sub_total']));
                if(!isset( $arr_data[$product['code']]['no_of_po']))
                    $arr_data[$product['code']]['no_of_po'] = array();
                $arr_data[$product['code']]['no_of_po'][$purchaseorder['_id']['code']] = 1;
            }
        }

        foreach ($arr_data as $value) {
            $total_num_of_purchaseorders += count($value['purchaseorders']);
            if(is_object($value['products_id']))
                $product = $this->Product->select_one(array('_id'=>new MongoId($value['products_id'])),array('category'));
            if (!isset($product['category']))
                $product['category'] = '';
            $html .= '
            <table class="table_content">
               <tbody>
                  <tr class="tr_right_none" style="background: #911b12;color: white;height: 29px;line-height: 29px;font-weight: bold;">
                     <td width="10%">
                        P. Code
                     </td>
                     <td>
                        Product Name
                     </td>
                     <td width="15%">
                        Category
                     </td>
                     <td class="right_text" width="15%">
                        No. of PO
                     </td>
                     <td class="right_text" colspan="3" width="20%">
                        Group total (ex. tax)
                     </td>
                  </tr>
                  <tr class="bg_2">
                     <td>' . $value['code'] . '</td>
                     <td>' . $value['products_name'] . '</td>
                     <td>' . (isset($category[$product['category']]) ? $category[$product['category']] : '') . '</td>
                     <td class="right_text">' . count($value['no_of_po']) . '</td>
                     <td colspan="3" class="right_text">' . $this->opm->format_currency($value['sub_total']) . '</td>
                  </tr>
               </tbody>
            </table>';
            $html .= '<table class="table_content" >
                        <tbody>
                          <tr class="tr_right_none" style="background: #979797;color: white;height: 29px;line-height: 29px;font-weight: bold;">
                             <td width="10%">
                                PO#
                             </td>
                             <td width="30%">
                                Company
                             </td>
                             <td width="15%" class="center_text">
                                Date
                             </td>
                             <td width="15%" class="right_text">
                                Unit Price
                             </td>
                             <td width="15%" class="right_text">
                                Quantity
                             </td>
                             <td class="right_text" colspan="3" width="18%">
                                Ex. Tax total
                             </td>
                          </tr>';
            $i = 0;
            $sum = $total_quantity = 0;
            foreach ($value['purchaseorders'] as $purchaseorder) {
                $sum += $purchaseorder['sub_total'];
                $total_quantity += $purchaseorder['quantity'];
                $html .= '
                      <tr class="content_asset bg_' . ($i % 2 == 0 ? '1' : '2') . '">
                         <td>' . $purchaseorder['code'] . '</td>
                         <td>' . $purchaseorder['company_name'] . '</td>
                         <td class="center_text">' . $this->opm->format_date($purchaseorder['purchord_date']->sec) . '</td>
                         <td class="right_text">' . $this->opm->format_currency((float)$purchaseorder['unit_price']) . '</td>
                         <td class="right_text">' . $purchaseorder['quantity'] . '</td>
                         <td colspan="3" class="right_text">' . $this->opm->format_currency((float)$purchaseorder['sub_total']) . '</td>
                      </tr>';
                $i++;
            }
            $html .= '
                            <tr class="bg_' . ($i % 2 == 0 ? '1' : '2') . '">
                             <td colspan="3" class="left_text bold_text right_none">' . $i . ' record(s) listed</td>
                             <td class="bold_text right_text right_none">Totals</td>
                             <td class="bold_text right_text right_none">' . $total_quantity . '</td>
                             <td colspan="4" class="bold_text right_text">' . $this->opm->format_currency($sum) . '</td>
                          </tr>
                        </tbody>
                    </table>
                    <br />
                    <br />';
            $total_sum_sub_total += $sum;
        }
        $html .= '
                <div class="line" style="margin-bottom: 5px;"></div>
                <table class="table_content">
                    <tr style="background-color: #333; color: white">
                        <td class="bold_text right_none" width="70%">'.$total_num_of_purchaseorders.' record(s) listed</td>
                        <td class="right_text bold_text right_none" >Totals</td>
                        <td class="right_text bold_text" width="15%">'.$this->opm->format_currency($total_sum_sub_total).'</td>
                    </tr>
                </table>';

        //========================================
        //set header
        if($data['heading']!='')
            $arr_pdf['report_heading'] = $data['heading'];
        $arr_pdf['date_from_to'] = '';
        if(isset($data['date_from'])&&$data['date_from']!='')
            $arr_pdf['date_from_to'] .= '<span class="color_red bold_text">From</span> '.$data['date_from'].' ';
        if(isset($data['date_to'])&&$data['date_to']!='')
            $arr_pdf['date_from_to'] .= ' <span class="color_red bold_text">To</span> '.$data['date_to'];
        if(isset($data['date_equals'])&&$data['date_equals']!='')
            $arr_pdf['date_from_to'] .= $data['date_equals'];
        $arr_pdf['content'][]['html'] = $html;
        $arr_pdf['is_custom'] = true;
        $arr_pdf['image_logo'] = true;
        $arr_pdf['report_name'] = 'PO Report By Product (Detailed)';
        $arr_pdf['report_file_name'] = 'PO_'.md5(time());
        return $arr_pdf;
    }
    public function detailed_category_product_report($arr_purchaseorders,$data){
        $i = $sum = 0;
        $html = '';
        $this->selectModel('Product');
        $category = $this->Setting->select_option_vl(array('setting_value' => 'product_category'));
        $total_num_of_purchaseorders = $total_sum_sub_total = 0;
        $arr_data = $arr_pdf = array();
        foreach($arr_purchaseorders as $purchaseorder){
            foreach($purchaseorder['products'] as $product){
                $product_category = '(empty)';
                if(is_object($product['products_id'])){
                    $_product = $this->Product->select_one(array('_id'=> new MongoId($product['products_id'])),array('category'));
                    if (isset($_product['category']))
                        $product_category = (isset($category[$_product['category']]) ? $category[$_product['category']] : '(empty)');
                }
                 $arr_data[$product_category]['category_name'] = $product_category;
                if(!isset($arr_data[$product_category]['quantity']))
                    $arr_data[$product_category]['quantity'] = 0;
                $arr_data[$product_category]['quantity'] += $product['quantity'];
                if(!isset($arr_data[$product_category]['sub_total']))
                    $arr_data[$product_category]['sub_total'] = 0;
                $arr_data[$product_category]['sub_total'] += (isset($product['sub_total']) ? (float)$product['sub_total'] : 0);
                $arr_data[$product_category]['quantity'] += (isset($product['quantity']) ? (float)$product['quantity'] : 0);
                $arr_data[$product_category]['purchaseorders'][] = array_merge($purchaseorder['_id'], array('unit_price'=>$product['unit_price'],'quantity'=>$product['quantity'],'sub_total'=>$product['sub_total']));
                if(!isset( $arr_data[$product_category]['no_of_qt']))
                    $arr_data[$product_category]['no_of_qt'] = array();
                $arr_data[$product_category]['no_of_qt'][$purchaseorder['_id']['code']] = 1;
            }
        }

        foreach ($arr_data as $value) {
            $total_num_of_purchaseorders += count($value['purchaseorders']);
            $html .= '
            <table class="table_content">
               <tbody>
                  <tr class="tr_right_none" style="background: #911b12;color: white;height: 29px;line-height: 29px;font-weight: bold;">
                     <td width="25%">
                        Category Name
                     </td>
                     <td class="right_text" width="15%">
                        No. of PO
                     </td>
                     <td class="right_text" colspan="3" width="20%">
                        Group total (ex. tax)
                     </td>
                  </tr>
                  <tr class="bg_2">
                     <td>' . $value['category_name'] . '</td>
                     <td class="right_text">' . count($value['no_of_qt']) . '</td>
                     <td colspan="3" class="right_text">' . $this->opm->format_currency($value['sub_total']) . '</td>
                  </tr>
               </tbody>
            </table>';
            $html .= '<table class="table_content" >
                        <tbody>
                          <tr class="tr_right_none" style="background: #979797;color: white;height: 29px;line-height: 29px;font-weight: bold;">
                             <td width="10%">
                                PO#
                             </td>
                             <td width="30%">
                                Company
                             </td>
                             <td width="15%" class="center_text">
                                Date
                             </td>
                             <td width="15%" class="right_text">
                                Unit Price
                             </td>
                             <td width="15%" class="right_text">
                                Quantity
                             </td>
                             <td class="right_text" colspan="3" width="18%">
                                Ex. Tax total
                             </td>
                          </tr>';
            $i = 0;
            $sum = $total_quantity = 0;
            foreach ($value['purchaseorders'] as $purchaseorder) {
                $sum += $purchaseorder['sub_total'];
                $total_quantity += $purchaseorder['quantity'];
                $html .= '
                      <tr class="content_asset bg_' . ($i % 2 == 0 ? '1' : '2') . '">
                         <td>' . $purchaseorder['code'] . '</td>
                         <td>' . $purchaseorder['company_name'] . '</td>
                         <td class="center_text">' . $this->opm->format_date($purchaseorder['purchord_date']->sec) . '</td>
                         <td class="right_text">' . $this->opm->format_currency((float)$purchaseorder['unit_price']) . '</td>
                         <td class="right_text">' . $purchaseorder['quantity'] . '</td>
                         <td colspan="3" class="right_text">' . $this->opm->format_currency((float)$purchaseorder['sub_total']) . '</td>
                      </tr>';
                $i++;
            }
            $html .= '
                            <tr class="bg_' . ($i % 2 == 0 ? '1' : '2') . '">
                             <td colspan="3" class="left_text bold_text right_none">' . $i . ' record(s) listed</td>
                             <td class="bold_text right_text right_none">Totals</td>
                             <td class="bold_text right_text right_none">' . $total_quantity . '</td>
                             <td colspan="4" class="bold_text right_text">' . $this->opm->format_currency($sum) . '</td>
                          </tr>
                        </tbody>
                    </table>
                    <br />
                    <br />';
            $total_sum_sub_total += $sum;
        }
        $html .= '
                <div class="line" style="margin-bottom: 5px;"></div>
                <table class="table_content">
                    <tr style="background-color: #333; color: white">
                        <td class="bold_text right_none" width="70%">'.$total_num_of_purchaseorders.' record(s) listed</td>
                        <td class="right_text bold_text right_none" >Totals</td>
                        <td class="right_text bold_text" width="15%">'.$this->opm->format_currency($total_sum_sub_total).'</td>
                    </tr>
                </table>';

        //========================================
        //set header
        if($data['heading']!='')
            $arr_pdf['report_heading'] = $data['heading'];
        $arr_pdf['date_from_to'] = '';
        if(isset($data['date_from'])&&$data['date_from']!='')
            $arr_pdf['date_from_to'] .= '<span class="color_red bold_text">From</span> '.$data['date_from'].' ';
        if(isset($data['date_to'])&&$data['date_to']!='')
            $arr_pdf['date_from_to'] .= ' <span class="color_red bold_text">To</span> '.$data['date_to'];
        if(isset($data['date_equals'])&&$data['date_equals']!='')
            $arr_pdf['date_from_to'] .= $data['date_equals'];
        $arr_pdf['content'][]['html'] = $html;
        $arr_pdf['is_custom'] = true;
        $arr_pdf['image_logo'] = true;
        $arr_pdf['report_name'] = 'PO Report By Category Product (Detailed)';
        $arr_pdf['report_file_name'] = 'PO_'.md5(time());
        return $arr_pdf;
    }
	/*
	  End report
	 */



	 function view_minilist(){
    	if(!isset($_GET['print_pdf'])){
	    	$arr_where = $this->arr_search_where();
	    	$purchaseorders = $this->opm->select_all(array(
	    											'arr_where' => $arr_where,
	    											'arr_field' => array('code','company_name','contact_name','purchord_date','our_rep','job_number','purchase_orders_status','sum_amount'),
	    											'arr_order' => array('_id'=>1),
	    											'limit' => 2000
	    											));
	    	if($purchaseorders->count()>0){
	    		$group = array();
	    		$html = '';
	    		$i = 0;
	    		$arr_data = array();
	    		$total_amount = 0;
	    		foreach($purchaseorders as $purchaseorder){
		    		$total_amount += $sum_amount = (isset($purchaseorder['sum_amount']) ? (float)$purchaseorder['sum_amount'] : 0);
	    			$html .= '<tr class="'.($i%2==0 ? 'bg_2' : 'bg_1').'">';
	    			$html .= '<td>'.(isset($purchaseorder['code']) ? $purchaseorder['code'] : '') .'</td>';
	    			$html .= '<td>'.(isset($purchaseorder['company_name']) ? $purchaseorder['company_name'] : '') .'</td>';
	    			$html .= '<td>'.(isset($purchaseorder['contact_name']) ? $purchaseorder['contact_name'] : '') .'</td>';
	    			$html .= '<td>'.(isset($purchaseorder['purchord_date']) ? date('m/d/Y',$purchaseorder['purchord_date']->sec) : '') .'</td>';
	    			$html .= '<td>'.(isset($purchaseorder['our_rep']) ? $purchaseorder['our_rep'] : '') .'</td>';
	    			$html .= '<td>'.(isset($purchaseorder['job_number']) ? $purchaseorder['job_number'] : '') .'</td>';
	    			$html .= '<td>'.(isset($purchaseorder['purchase_orders_status']) ? $purchaseorder['purchase_orders_status'] : '') .'</td>';
	    			$html .= '<td class="right_text">'. $this->opm->format_currency($sum_amount) .'</td>';
	                $html .= '</tr>';
	                $i++;
	    		}
		    	$html .='<tr class="last">
				            <td colspan="6" class="bold_text right_none">'.$i.' record(s) listed.</td>
				            <td class="bold_text right_none right_text">Totals:</td>
				            <td class="bold_text right_none right_text">'.$this->opm->format_currency($total_amount).'</td>
				        </tr>';
		        $arr_data['title'] = array('Ref No'=>'text-align: left','Supplier Company'=>'text-align: left','Supplier Contact'=>'text-align: left','Date'=>'text-align: left','Our Rep'=>'text-align: left','Job No'=>'text-align: left','Status'=>'text-align: left','Total amount'=>'text-align: right;');
		    	$arr_data['content'] = $html;
		    	$arr_data['report_name'] = 'Purchase order Mini  Listing';
		    	$arr_data['report_file_name'] = 'PO_'.md5(time());
		    	$arr_data['report_orientation'] = 'landscape';
		    	Cache::write('purchaseorders_minilist', $arr_data);
	    	}
    	} else
    		$arr_data = Cache::read('purchaseorders_minilist');
    	$this->render_pdf($arr_data);
    }


	function tasks($purchaseorder_id){
		$this->selectModel('Task');
		$arr_task = $this->Task->select_all(array(
			'arr_where' => array('purchaseorder_id' => new MongoId($purchaseorder_id)),
			'arr_order' => array('work_start' => 1)
		));
		$this->set('arr_task', $arr_task);
		$this->set('purchaseorder_id', $purchaseorder_id);

		$this->selectModel('Noteactivity');
		$this->set('model_noteactivity', $this->Noteactivity);
	}

	function tasks_add($purchaseorder_id) {
		if(!$this->check_permission('tasks_@_entry_@_add'))
			$this->error_auth();
		$this->selectModel('Purchaseorder');
		$arr_purchaseorder = $this->Purchaseorder->select_one(array('_id' => new MongoId($purchaseorder_id)));
		if (isset($arr_purchaseorder['our_rep_id']) && is_object($arr_purchaseorder['our_rep_id'])) {
			$arr_save['our_rep_id'] = $arr_purchaseorder['our_rep_id'];
			$arr_save['our_rep'] = $arr_purchaseorder['our_rep'];
		}
		$arr_save['company_id'] = $arr_purchaseorder['company_id'];
		$arr_save['company_name'] = $arr_purchaseorder['company_name'];
		$arr_save['contact_id'] = $arr_purchaseorder['contact_id'];
		$arr_save['contact_name'] = $arr_purchaseorder['contact_name'];
		$arr_save['purchaseorder_id'] = $arr_purchaseorder['_id'];
		$arr_save['purchaseorder_name'] = $arr_save['name'] = $arr_purchaseorder['name'];

		$arr_save['work_start'] = new MongoDate(strtotime(date('Y-m-d', $arr_purchaseorder['required_date']->sec)) - DAY + 8*3600 );
		$arr_save['work_end'] = new MongoDate($arr_save['work_start']->sec + 3600);

		$this->selectModel('Task');
		$this->Task->arr_default_before_save = $arr_save;
		if ($this->Task->add())
			$this->redirect('/tasks/entry/' . $this->Task->mongo_id_after_save);
		$this->redirect('/purchaseorders/entry');
	}

	function tasks_delete($id) {
		if(!$this->check_permission('tasks_@_entry_@_delete')){
			echo 'You do not have permission on this action.';
			die;
		}
		$arr_save['_id'] = $id;
		$arr_save['deleted'] = true;
		$error = 0;
		if (!$error) {
			$this->selectModel('Task');
			if ($this->Task->save($arr_save)) {
				echo 'ok';
			} else {
				echo 'Error: ' . $this->Task->arr_errors_save[1];
			}
		}
		die;
	}

	function delete_all_associate($idopt='',$key=''){
		if(!$this->check_permission($this->name.'_@_entry_@_delete')){
			echo 'You do not have permission on this action.';
			die;
		}
		$ids = $this->get_id();
		if($key=='products'){ // update cac line entry option cua products
			if($ids!=''){
				$arr_insert = $line_entry = array();
				//lay note products hien co
				$query = $this->opm->select_one(array('_id'=>new MongoId($ids)),array('products'));
				if(isset($query['products']) && !empty($query['products'])){
					$line_entry = $query['products'];
					$line_entry[$idopt] =  array('deleted'=>true);
					foreach($query['products'] as $keys=>$values){
						if(isset($values['option_for']) && $values['option_for']==$idopt){
                            $line_entry[$keys] = array('deleted'=>true);
						}
					}
				}
				$arr_insert['products'] = $line_entry;//pr($line_entry);die;
				$arr_insert['_id'] 		= new MongoId($ids);
				$arr_insert = array_merge($arr_insert,$this->new_cal_sum($line_entry));
				$this->opm->save($arr_insert);
			}
		}
		return true;
	}

	function duplicate_purchase_order(){
		$arr_save = $this->opm->select_one(array('_id'=> new MongoId($this->get_id())));
		$arr_save['purchaseorder_code'] = $arr_save['code'];
		$arr_save['purchaseorder_id'] = $arr_save['_id'];
		$arr_save['code'] = $this->opm->get_auto_code('code');
		$arr_save['purchord_date'] = new MongoDate();
		unset($arr_save['_id']);

		if($this->opm->save($arr_save)){
			$new_id = $this->opm->mongo_id_after_save;
			$this->redirect('/purchaseorders/entry/'.$new_id);
		} else
			$this->redirect('/purchaseorders/entry/');
		die;
	}
	public function purchaseorder_invoice_receive_report()
	{
		$po = $this->opm->select_all(array('arr_field'=>array('company_id','company_name','supplier_invoice','sum_amount','code','purchord_date','purchase_orders_status')));
		if($po->count()>0)
		{
			$total_po = 0;
			$total_invoice = 0;
			$total_balance = 0;
			$html_loop = '
			<table cellpadding="3" cellspacing="0" class="maintb">
			   <tbody>
				  <tr>
					<td width="7%" class="first top">
						Order#
					 </td>
					 <td width="23%" class="top">
						Supplier
					 </td>
					 <td width="15%" class="top">
						Date
					 </td>
					 <td width="10%" class="top">
						Status
					 </td>
					 <td align="right" width="15%" class="top">
						Total PO
					 </td>
					 <td align="right" width="15%" class="top">
						Total inv recd
					 </td>
					 <td align="right" width="15%" class="top">
						Balance
					 </td>
				  </tr>';

				$i = 0;
				foreach($po as $value)
				{
					$color = '#fdfcfa';
					if($i%2==0)
						$color = '#eeeeee';
					$sum_amount = (isset($value['sum_amount'])&&$value['sum_amount']!='' ? (float)$value['sum_amount'] : 0);
					$invoice = 0;
					if(isset($value['supplier_invoice'])&&!empty($value['supplier_invoice']))
					{
						foreach($value['supplier_invoice'] as $val)
							$invoice += ($val['paid']!='' ? (float)$val['paid'] : 0);
					}
					$balance = $sum_amount - $invoice;
					$html_loop .= '
						  <tr style="background-color:'.$color.';">
							 <td class="first content">'.$value['code'].'</td>
							 <td class="content">'.$value['company_name'].'</td>
							 <td class="content">'.(date('M d, Y',$value['purchord_date']->sec)).'</td>
							 <td class="content">'.$value['purchase_orders_status'].'</td>
							 <td class="content" align="right">'.$this->opm->format_currency($sum_amount).'</td>
							 <td class="content" align="right">'.$this->opm->format_currency($invoice).'</td>
							 <td class="content end" align="right">'.$this->opm->format_currency($balance).'</td>
						  </tr>';
					$i++;
					$total_po += $sum_amount;
					$total_invoice += $invoice;
					$total_balance += $balance;
				}

			$color = '#fdfcfa';
			if(!isset($i))
				$i =0;
			if($i%2==0)
				$color = '#eeeeee';
			$html_loop .= '
							<tr style="background-color:'.$color.'">
							 <td colspan="3" align="left" class="first bottom">'.$i.' record(s) listed</td>
							 <td align="right" class="bottom"><span style="font-weight:bold; padding-left:20px">Total:</span></td>
							 <td align="right" class="bottom">'.$this->opm->format_currency($total_po).'</td>
							 <td align="right" class="bottom">'.$this->opm->format_currency($total_invoice).'</td>
							 <td align="right" class="content bottom">'.$this->opm->format_currency($total_balance).'</td>
						  </tr>
						</tbody>
					</table>
					<br />
					<div style="border-bottom: 1px dashed #9f9f9f; height:1px; clear:both"></div><br />';


			//==============================
			$pdf['current_time'] = date('h:i a m/d/Y');
			$pdf['title'] = '<span style="color:#b32017">P</span>urchase <span style="color:#b32017">I</span>nvoice <span style="color:#b32017">R</span>eceive <span style="color:#b32017">T</span>otal <br />';
			$this->layout = 'pdf';
				//set header
			$pdf['logo_link'] = 'img/logo_anvy.jpg';
			$pdf['company_address'] = '3145 - 5th Ave NE<br />Calgary  AB  T2A  6A3<br />';
			$pdf['html_loop'] = $html_loop;
			$pdf['filename'] = 'PO_'.md5($pdf['current_time']);

			$this->report_pdf($pdf);
			echo URL.'/upload/'.$pdf['filename'].'.pdf';
		}
		die;

	}

	function rebuild_products(){
		$this->selectModel('Purchaseorder');
		$arr_order = $this->Purchaseorder->select_all(array(
		                                              'arr_where' => array(
		                                                                '$or' => array(
		                                                                               array(
		                                                                                    'products' => array('$exists' => false)
		                                                                                     ),
		                                                                               array(
		                                                                                     'products' => array('$in'=>array('',null))
		                                                                                     )
		                                                                               )
		                                                                   ),
		                                              'arr_field' => array('products')
		                                              ));
		echo $arr_order->count().' records found.<br />';
		$i = 0;
		foreach($arr_order as $order){
			$order['products'] = array();
			$this->Purchaseorder->rebuild_collection($order);
			$i++;
		}
		echo $i.' records rebuild - Done.';
		die;
	}


	function recalculation_pricing($query,$key){
		$original_quantity = $query['products'][$key]['quantity'];
		$balance_received = $query['products'][$key]['balance_received'];;
		if($balance_received == 0){
			if(isset($query['products'][$key]['balance_received']) && !$query['products'][$key]['balance_received'] && isset($query['products'][$key]['quantity_received']) && $query['products'][$key]['quantity_received'])
				$balance_received = 0;
			else
				$balance_received = $original_quantity;
		}
		$query['products'][$key]['quantity'] = $balance_received;
		$cal_price = new cal_price;
    	$cal_price->arr_product_items = $query['products'][$key];
    	$query['products'][$key] = $cal_price->purchaseorder_cal_price();
    	$query['products'][$key]['quantity'] = $original_quantity;
    	$sum_sub_total = $sum_amount = 0;
    	foreach($query['products'] as $product){
    		if(isset($product['deleted']) && $product['deleted']) continue;
    		if($product['sub_total'] < 0) continue;
    		$sum_sub_total += (float)round($product['sub_total'],2);
    		$sum_amount += (float)round($product['amount'],2);
    	}
    	$sum_tax = round($sum_amount - $sum_sub_total,3);
    	$query['sum_sub_total'] = $sum_sub_total;
    	$query['sum_tax'] = $sum_tax;
    	$query['sum_amount'] = $sum_amount;
    	return $query;
	}

	function receive_return(){
		if(!empty($_POST)){
			$id = $this->get_id();
			$po = $this->opm->select_one(array('_id'=> new MongoId($id)));
			if(isset($po['products'][$_POST['id']])){
				$_POST['value'] = (int)$_POST['value'];
				$current_user = $this->opm->user_name();
				$current_user_id = new MongoId($this->opm->user_id());
				$product = $po['products'][$_POST['id']];
				$quantity = $product['quantity'];
				$this->selectModel('Location');
				$location = $this->Location->select_one(array(),array('_id','name','location_type','stock_usage'),array('_id'=>1));
				if($_POST['name'] == 'quantity_received'){
					if(!isset($product['quantity_received']))
						$product['quantity_received'] = 0;
					if(!isset($product['quantity_returned']))
						$product['quantity_returned'] = 0;
					if($_POST['value'] > $quantity)
						$_POST['value'] = $quantity;
					$quantity_received = $_POST['value'] - $product['quantity_received'];
					$balance_received = $_POST['value'] - $product['quantity_returned'];
					$product['quantity_received'] = $product['quantity_shipped'] = $_POST['value'];
					$product['balance_received'] = $product['balance_shipped'] = $balance_received;
					$product['balance_returned'] = $_POST['value'];
					$product['receive_item'][] = array(
					                                   	'deleted' => false,
														'receive_by' => $current_user,
														'receive_by_id' => $current_user_id,
														'quantity' => $quantity_received,
														'location_name' => $location['name'],
														'location_id' => $location['_id'],
														'receive_date' => new MongoDate(),
					                                   );
					$update_product[$_POST['id']] = array(
					                                    '_id' => $product['products_id'],
					                                    'quantity' => $quantity_received,
					                                    'location' => array(
					                                                        '_id' => $location['_id'],
					                                                        'name' => $location['name'],
					                                                        'location_type' => isset($location['location_type']) ? $location['location_type'] : '',
					                                                        'stock_usage' => isset($location['stock_usage']) ? $location['stock_usage'] : '',
					                                                        )
					                                      );
					$po['products'][$_POST['id']] = $product;
					$arr_return = array();
					$po = array_merge($po, $this->recalculation_pricing($po, $_POST['id']));
					$arr_return = array(
							'sub_total' => $this->opm->format_currency($po['products'][$_POST['id']]['sub_total']),
							'tax' => $this->opm->format_currency($po['products'][$_POST['id']]['tax'],3),
							'amount' => $this->opm->format_currency($po['products'][$_POST['id']]['amount']),
							'sum_sub_total' => $this->opm->format_currency($po['sum_sub_total']),
							'sum_tax' => $this->opm->format_currency($po['sum_tax'], 3),
							'sum_amount' => $this->opm->format_currency($po['sum_amount']),
						);
					$this->opm->save($po);
					$this->selectModel('Product');
					$this->Product->update_stock($update_product);

				} else if($_POST['name'] == 'quantity_returned'){
					if(!isset($product['quantity_received']))
						$product['quantity_received'] = 0;
					if(!isset($product['quantity_returned']))
						$product['quantity_returned'] = 0;
					if($_POST['value'] > $quantity)
						$_POST['value'] = $quantity;
					$quantity_returned = $_POST['value'] - $product['quantity_returned'];
					if(!isset($product['balance_received']))
						$product['balance_received'] = 0;
					$balance = $product['quantity_received'] - $_POST['value'];
					if($balance > $quantity)
						$balance = $quantity;
					$product['balance_received'] = $product['balance_shipped'] = $balance;
					$product['balance_returned'] = $quantity - $_POST['value'];
					$product['quantity_returned'] = $_POST['value'];
					$product['return_item'][] = array(
				                                   	'deleted' => false,
													'return_date' => new MongoDate(),
													'return_by' => $current_user,
													'return_by_id' => $current_user_id,
													'quantity' => $quantity_returned,
				                                   );
					$update_product[$_POST['id']] = array(
				                                    '_id' => $product['products_id'],
				                                    'quantity' => $quantity_returned,
				                                    'location' => array(
				                                                        '_id' => $location['_id'],
				                                                        'name' => $location['name'],
				                                                        'location_type' => isset($location['location_type']) ? $location['location_type'] : '',
				                                                        'stock_usage' => isset($location['stock_usage']) ? $location['stock_usage'] : '',
				                                                        )
				                                      );
					$po['products'][$_POST['id']] = $product;
					$arr_return = array();
					$po = array_merge($po, $this->recalculation_pricing($po, $_POST['id']));
					$arr_return = array(
							'sub_total' => $this->opm->format_currency($po['products'][$_POST['id']]['sub_total']),
							'tax' => $this->opm->format_currency($po['products'][$_POST['id']]['tax'],3),
							'amount' => $this->opm->format_currency($po['products'][$_POST['id']]['amount']),
							'sum_sub_total' => $this->opm->format_currency($po['sum_sub_total']),
							'sum_tax' => $this->opm->format_currency($po['sum_tax'], 3),
							'sum_amount' => $this->opm->format_currency($po['sum_amount']),
						);
					$this->opm->save($po);
					$this->selectModel('Product');
					$this->Product->update_stock($update_product,'minus');
					if($quantity_returned > 0){
						$this->selectModel('Shipping');
						$arr_shipping = $po;
						$arr_shipping['code'] = $this->Shipping->get_auto_code('code');
						$arr_shipping['carrier_id'] = '';
						$arr_shipping['carrier_name'] = '';
						$arr_shipping['received_date'] = '';
						$arr_shipping['return_status'] = 1;
						$arr_shipping['shipping_date'] = new MongoDate();
						$arr_shipping['shipping_status'] = 'Completed';
						$arr_shipping['shipping_type'] = 'Out';
						$this->selectModel('Company');
						$company = array();
						$shipping_address = array(
						                 'address_1'=>'',
						                 'address_2'=>'',
						                 'address_3'=>'',
						                 'town_city'=>'',
						                 'province_state'=>'',
						                 'province_state_id'=>'',
						                 'zip_postcode'=>'',
						                 'country'=>'',
						                 'country'=>'',
						                 'country_id'=>''
						                 );
						if(is_object($po['company_id'])){
							$company = $this->Company->select_one(array('_id'=> new MongoId($po['company_id'])),array('addresses'));
							$company['addresses_default_key'] = (isset($company['addresses_default_key']) ? $company['addresses_default_key'] : 0);
							$shipping_address = $company['addresses'][$company['addresses_default_key']];
							$arr_shipping['shipping_address'][0]['deleted'] = false;
							foreach($shipping_address as $key=>$value)
								$arr_shipping['shipping_address'][0]['shipping_'.$key] = $value;
						}
						$arr_shipping['invoice_address'] = array(
						                                         'deleted' => false,
						                                         'shipping_country' => 'CA'
						                                         );
						$arr_shipping['purchaseorder_id'] = new MongoId($id);
						$arr_shipping['purchaseorder_code'] = $po['code'];
						$arr_shipping['purchaseorder_name'] = $po['name'];
						$product['quantity'] = $quantity_returned;
						$arr_shipping['products'][0] = $product;
						unset($arr_shipping['_id']);
						unset($arr_shipping['invoice_address']);
						unset($arr_shipping['delivery_date']);
						unset($arr_shipping['purchase_orders_status']);
						unset($arr_shipping['purchord_date']);
						unset($arr_shipping['required_date']);
						unset($arr_shipping['salesorder_id']);
						unset($arr_shipping['salesorder_name']);
						unset($arr_shipping['salesorder_number']);
						unset($arr_shipping['ship_to_company_id']);
						unset($arr_shipping['ship_to_company_name']);
						unset($arr_shipping['ship_to_contact_id']);
						unset($arr_shipping['ship_to_contact_name']);
						unset($arr_shipping['shipper_company_id']);
						unset($arr_shipping['shipper_company_name']);
						unset($arr_shipping['sum_amount']);
						unset($arr_shipping['sum_sub_total']);
						unset($arr_shipping['sum_tax']);
						unset($arr_shipping['tax']);
						$this->Shipping->save($arr_shipping);
					}
				}

				$arr_return = array_merge($arr_return, array(
				                 	'quantity_received' => $product['quantity_received'],
				                 	'balance_received' => $product['balance_received'],
				                 	'quantity_shipped' => $product['quantity_shipped'],
				                 	'balance_shipped' => $product['balance_shipped'],
				                 	'balance_returned' => $product['balance_returned'],
				                 ));
				echo json_encode($arr_return);
			}
		}
		die;
	}

	function build_balance(){
		$orders = $this->opm->select_all(array(
		                       	$po = 'arr_where' => array(
		                       	                     'products' => array(
		                       	                                         '$exists' => true,
		                       	                                         '$nin' => array('',null)
		                       	                                         )
		                       	                     ),
		                       	'arr_field' => array('products')

		                       ));
		$arr = array('quantity','quantity_received','quantity_returned');
		foreach($orders as $order){
			foreach($order['products'] as $key => $value){
				if(isset($value['deleted']) && $value['deleted']){
					$order['products'][$key] = array('deleted' => true);
					continue;
				}
				foreach($arr as $v){
					if(!isset($value[$v]))
						$value[$v] = 0;
				}
				$order['products'][$key]['balance_received'] = $order['products'][$key]['balance_shipped'] = $value['quantity_received'] - $value['quantity_returned'];
				$this->opm->rebuild_collection($order);
			}
		}
		die;
	}

}