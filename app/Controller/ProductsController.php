<?php
App::import('Vendor', 'cal_price/cal_price');
App::import('Vendor', '2dcutting/2dCutting');
App::import('Vendor', '2dcutting/2dDrawing');
App::uses('AppController', 'Controller');

class ProductsController extends AppController {

    var $name = 'Products';
    public $helpers = array();
    public $opm; //Option Module
    public $modelName = 'Product';
    public function beforeFilter() {
        parent::beforeFilter();
        //$this->set: name, arr_settings, arr_options, iditem, entry_menu
        $this->set_module_before_filter('Product');
        $this->sub_tab_default = 'general';
    }

	/*
	* Hàm dùng để điều chỉnh setting(giao diện)/phân quyền
	*/
	public function rebuild_setting($arr_setting=array()){
		$arr_setting = $this->opm->arr_settings;
		$iditem = $this->get_id();
		if($iditem!=''){
			 $query = $this->opm->select_one(array('_id' => new MongoId($iditem)));

			//khóa subtab
			if (isset($query['group_type']) && $query['group_type']=='BUY') {
				$m=0;
			}else if(isset($query['group_type']) && $query['group_type']=='SELL'){
				if(isset($query['product_type']) && $query['product_type']=='Finished Goods')
					$m=0;//none doing
				else
					$m=0;//unset($arr_setting['relationship']['costings']['block']['madeup']);
			}
			$action = $this->params->params['action'];
			if($action=='entry_search'){
				unset($arr_setting['field']['panel_3']['oum_depend']['lock']);
				$arr_setting['field']['panel_3']['sell_price']['name'] = 'Price';
				$arr_setting['field']['panel_3']['unit_price']['type'] = 'price';
				$arr_setting['field']['panel_3']['oum_depend']['type'] = 'select';
				unset($arr_setting['field']['panel_3']['none21']);
				unset($arr_setting['field']['panel_3']['none22']);
				$arr_setting['field']['panel_3']['pst_tax']['moreclass'] = 'fixbor3';
			}

			if(isset($query['product_type']) && $query['product_type']=='Vendor Stock'){
				$arr_setting['field']['panel_3']['sell_price']['name'] = 'Cost price';
				$arr_setting['field']['panel_3']['unit_price']['type'] = 'price';
				$arr_setting['field']['panel_3']['oum_depend']['type'] = 'select';
				unset($arr_setting['field']['panel_3']['none21']);
				unset($arr_setting['field']['panel_3']['none22']);
				$arr_setting['field']['panel_3']['oum']['name'] = '<span title="Unit of measurement for Purchase">Purchase OUM</span>';
				$arr_setting['field']['panel_3']['pst_tax']['moreclass'] = 'fixbor3';
				unset($arr_setting['relationship']['purchasing']['block']['po_supplier']);

			}else{
				unset($arr_setting['relationship']['purchasing']['block']['products_useon']);

				if($action!='entry_search')
					$arr_setting['field']['panel_3']['cost_price']['type'] = 'price';

				unset($arr_setting['field']['panel_3']['none21']);
			}

			if($this->params->params['action']=='lists' || $this->params->params['action']=='popup'){
				$arr_setting['field']['panel_3']['oum']['name'] = '<span title="Unit of measurement for Purchase">OUM</span>';
			}


			//empty all tracking
			if(!isset($query['check_stock_stracking']) || ( isset($query['check_stock_stracking']) && $query['check_stock_stracking']==0)){
				$arr_val = $this->opm->arr_settings['relationship']['general']['block']['stocktracking'];
				$field_list = $this->opm->arr_settings['relationship']['general']['block']['stocktracking']['field'];
				foreach($field_list as $key => $value){
					if($key=='check_stock_stracking')
						break;

					//set name
					if(isset($arr_val['field'][$key]['name']))
						$arr_val['field'][$key]['name'] = '&nbsp;';
					//set default
					if(isset($arr_val['field'][$key]['default']))
						$arr_val['field'][$key]['default'] = '&nbsp;';

					if($value['type']=='header')
						continue;
					//set type
					if(isset($arr_val['field'][$key]['type']))
						$arr_val['field'][$key]['type'] = 'display';

				}
				$arr_setting['relationship']['general']['block']['stocktracking'] = $arr_val;
			}
		}

		$this->opm->arr_settings = $arr_setting;
	}




    public function change_sold_by_len() {
        $this->opm->update_sold_by_len();
		die;
    }

    public function change_sold_by_sq() {
        $this->opm->update_sold_by_sq();
        die;
    }

	public function change_type() {
        $this->opm->change_group_type();
        die;
    }

	public function backup_type() {
        $this->opm->backup_type();
        die;
    }


	public function change_oum_from_parent_product() {
        $this->opm->change_oum_from_parent_product();
        die;
    }


	public function update_unitprice_from_parent_product() {
        $this->opm->update_unitprice_from_parent_product();
        die;
    }


	public function update_type_for_blank() {
        $this->opm->update_type_for_blank();
        die;
    }


	/*public function change_field_supplier() {
        $this->opm->change_field_supplier();
        die;
    }

	public function repair_type() {
        $this->opm->repair_type();
        die;
    }*/

    // public function up_supplier(){

    //     $this->selectModel("Product");
    //     $arr_alls = $this->Product->select_all();
    //     foreach ($arr_alls as $value) {
    //         $arr_save = $value;
    //         if( isset($arr_save['supplier']['0']) ){
    //             foreach ($arr_save['supplier'] as $supplier_key => $supplier_value) {
    //                 $arr_tmp = array(
    //                     'sname' => $arr_save['name'],
    //                     'sizew' => (isset($arr_save['sizew'])?$arr_save['sizew']:''),
    //                     'sizew_unit' => (isset($arr_save['sizew_unit'])?$arr_save['sizew_unit']:''),
    //                     'sizeh' => (isset($arr_save['sizeh'])?$arr_save['sizeh']:''),
    //                     'sizeh_unit' => (isset($arr_save['sizeh_unit'])?$arr_save['sizeh_unit']:''),
    //                     'sold_by' => (isset($arr_save['sell_by'])?$arr_save['sell_by']:''),
    //                     'cost_price' => (isset($arr_save['sell_price'])?$arr_save['sell_price']:''),
    //                     'current' => ''
    //                 );
    //                 $arr_save['supplier'][$supplier_key] = array_merge($arr_tmp, $arr_save['supplier'][$supplier_key]);
    //             }

    //             if( !isset($arr_save['supplier'][1]) ){
    //                 $arr_save['supplier'][0]['current'] = 'on';
    //             }

    //             if (!$this->Product->save($arr_save)) {
    //                 echo 'Error: User ' . $this->Product->arr_errors_save[1]; die;
    //             }
    //         }

    //     }
    //     echo 'xong'; die;
    // }

    public function import__() {
        $conn = mysql_connect('127.0.0.1', 'root', '') or die("Database error");
        mysql_select_db('test', $conn);
        $query = "SELECT * FROM contact LIMIT 10";
        $result = mysql_query($query);
        echo '<pre>';
        while ($row = mysql_fetch_object($result)) {
            var_dump($row);
            echo '<br>';
        }
        echo '</pre>';
        die();
    }

    public function update_supplier_rmsupplier() {

		$this->selectModel('Product');
		$this->Product->collection->update(array('supplier.company_code' => ''), array( '$set' => array('supplier' =>  array()) ), array('multiple' => true));
		echo 'xong';
		die();
	}

    public function import() {
        //ini_set('memory_limit', '128M');
        $conn = mysql_connect('127.0.0.1', 'root', '') or die("Database error");
        mysql_select_db('test', $conn);
        $query = "SELECT * FROM product LIMIT 9000";
        $result = mysql_query($query);
        $arr_save = array();
        $this->selectModel('Product');
//		$arr_tmp = $this->opm->arrfield();
//		pr($this->opm->arr_temp);
        //$STT = 0;


        $STT = 1;
        while ($row = mysql_fetch_object($result)) {

//            $STT += 1;
//
//			if( in_array( $STT, array(20, 45, 46, 47,48, 249, 270, 271)) ){
//				continue;
//			}

            $arr_save = array();
            $supplier = array();
            $arr_save['code'] = (int) $row->code;
            $arr_save['serial'] = (int) ($row->_Serial);
            $arr_save['serial'] = (int) $row->_Serial;
            $arr_save['name'] = trim($row->StockDescription);
            $arr_save['description'] = trim($row->StockDescription);
            $arr_save['status'] = 1;
            $arr_save['product_type'] = trim($row->StockType);
            $arr_save['color'] = '';
            $arr_save['thickness'] = '';
            $arr_save['thickness_unit'] = '';
            $arr_save['sizew'] = trim($row->anvy_width);
            $arr_save['sizew_unit'] = '';
            $arr_save['sizeh'] = '';
            $arr_save['sizeh_unit'] = '';
            $arr_save['is_custom_size'] = 0;
            $arr_save['cost_price'] = trim($row->anvy_cost);
            $arr_save['markup'] = '';
            $arr_save['profit'] = '';
            $arr_save['sell_by'] = trim($row->anvy_sellby);
            $arr_save['sell_price'] = trim($row->anvy_sellprice);
            $arr_save['oum'] = '';
            $arr_save['gst_tax'] = '';
            $arr_save['pst_tax'] = '';

            $supplier[0]['company_id'] = '';
            $supplier[0]['company_code'] = trim($row->AccNoForSupplier);
            $supplier[0]['supplier'] = trim($row->Supplier);
            $supplier[0]['cost_price'] = trim($row->anvy_cost);
            $supplier[0]['current'] = 1;
            $supplier[0]['deleted'] = false;
            $supplier[0]['supplier_code_0'] = trim($row->Supply_StockCode);

            $arr_save['company'] = $supplier;
            $arr_save['category'] = trim($row->Category);
            if ($row->anvy_specialorder_flag == 'Yes') {
                $arr_save['special_order'] = 1;
            } elseif ($row->anvy_specialorder_flag == 'No') {
                $arr_save['special_order'] = 0;
            } else {
                $arr_save['special_order'] = NULL;
            }
            $arr_save['madeup'] = '';
            $arr_save['on_po'] = '';
            $arr_save['on_so'] = '';
            $arr_save['in_stock'] = '';
            $arr_save['under_over'] = '';

            //new field
            $arr_save['buy_by'] = trim($row->anvy_costper);
            $arr_save['length'] = trim($row->anvy_length);
            $arr_save['priority'] = trim($row->anvy_priority);
            $arr_save['stock_by'] = trim($row->anvy_stockby);
            $arr_save['tech_notes'] = trim($row->anvy_tech_notes);
            $arr_save['anvy_tw'] = trim($row->anvy_tw);
            $arr_save['barcode'] = trim($row->BarcodeNumber);
            $arr_save['identity'] = trim($row->CompanyNameOnStockList);
            $arr_save['sell_price_intax'] = trim($row->SellPriceIncTax);
            $arr_save['stock_code'] = trim($row->StockCode);
            $arr_save['stock_des_current'] = trim($row->StockDescriptionWhenCurrent);

            $STT += 1;

            if (in_array($STT, array(20, 45, 46, 47, 48, 249, 270, 271))) {
                pr($arr_save);
            }

//            if ($this->Product->save($arr_save)) {
//                //echo 'save thanh cong - ' . $row->code;
//            } else {
//                echo 'Error: ' . $this->Product->arr_errors_save[1];
//                die;
//            }
            /* if($STT==5)
              sleep(2); */
        }


//
//			// BusinessType
//			/*$arr_save['business_type'] = trim($row->BusinessType);
//			// $arr_save['business_type_id'] = // BaoNam nhớ chỉnh lại id cho cột này
//
//			$arr_save['our_csr'] = trim($row->anvy_CSR);
//			// $arr_save['our_csr_id'] = // BaoNam nhớ chỉnh lại id cho cột này
//
//			$arr_save['our_rep'] = trim($row->OurRep);
//			// $arr_save['our_rep_id'] = // BaoNam nhớ chỉnh lại id cho cột này
//
//			$arr_save['phone'] = trim($row->Phone);
//			$arr_save['fax'] = trim($row->Fax);
//			$arr_save['web'] = trim($row->WebSiteAddress);*/
//
//			// ADDRESS
//			/*$country = 'Canada';
//			$country_id = "CA";
//			if( strlen(trim($row->Address_Country)) > 0 ){
//				$country = trim($row->Address_Country);
//				if( $country == 'USA' ||  $country == 'United States' || substr($country, 0, 3) == 'U.S' ){
//					$country_id = 249;
//				}
//				if( substr($country, 0, 3) == 'kor' ){
//					$country_id = 216;
//				}
//			}*/
//
//			// search province/state
//			/*$this->selectModel('Province');
//			$arr_tmp = $this->Province->select_one(array('name' => trim($row->Address_CountyState)));
//			$province_state_id = '';
//			if( isset($arr_tmp['name']) ){
//				$province_state_id = $arr_tmp['_id'];
//			}
//			$arr_save['addresses'] = array(
//				array(
//					'name' => '',
//					'deleted' => false,
//					'default' => true,
//					'country' => $country,
//					'country_id' => $country_id,
//					'province_state' => trim($row->Address_CountyState),
//					'province_state_id' => '',
//					'address_1' => trim($row->Address_Line1),
//					'address_2' => trim($row->Address_Line2),
//					'address_3' => trim($row->Address_Line3),
//					'town_city' => trim($row->Address_TownCity),
//					'zip_postcode' => trim($row->Address_ZipPostCode),
//				)
//			);
//			$arr_save['addresses_default_key'] = 0;
//
//			// giữ lại dữ liệu cũ khi cần
//			$arr_save['old_data'] = array(
//				'CompanyRegNo' => trim($row->CompanyRegNo),
//				'Category' => trim($row->Category),
//				'BusinessType' => trim($row->BusinessType),
//				'anvy_CSR' => trim($row->anvy_CSR),
//				'OurRep' => trim($row->OurRep),
//			);
//
//			$this->selectModel('Company');
//			if ($this->Company->save($arr_save)) {
//				// echo 'ok';
//			} else {
//				echo 'Error: ' . $this->Company->arr_errors_save[1];die;
//			}*/
//		}
        echo 'xong';
        die;
    }



	public function entry() {

        $arr_set = $this->opm->arr_settings;

		//set color RED
		/*$not_yet['units_serials'] = '1';
		$not_yet['batches'] = '1';
		$this->set('not_yet', $not_yet);*/

        // Check url to get value id
        $iditem = $this->get_id();
        if ($iditem == '')
            $iditem = $this->get_last_id();
        //Load record by id and set default for fields
        if ($iditem != '') {
            $arr_tmp = $this->opm->select_one(array('_id' => new MongoId($iditem)));
            foreach ($arr_set['field'] as $ks => $vls) {
                foreach ($vls as $field => $values) {
                    if (isset($arr_tmp[$field])) {
                        $arr_set['field'][$ks][$field]['default'] = $arr_tmp[$field];
                        if ($arr_set['field'][$ks][$field]['type'] == 'select')
                            $arr_set['field'][$ks][$field]['default_id'] = $arr_tmp[$field];
                        if (in_array($field, $arr_set['title_field']))
                            $item_title[$field] = $arr_tmp[$field];


                    }
                }
            }
            $arr_set['field']['panel_1']['mongo_id']['default'] = $iditem;
            $this->Session->write($this->name . 'ViewId', $iditem);
            //BEGIN special
            if (isset($arr_tmp['company']) && is_array($arr_tmp['company'])) {
                $arr_temp = (array) $arr_tmp['company'];
                $com_name = $com_id = '';
                foreach ($arr_temp as $ok => $ov) {
                    if (isset($ov['current']) && (int) $ov['current'] == 1 && !($ov['deleted'])) {
                        if (isset($ov['company_id'])) {
                            $com_id = $ov['company_id'];
                            $com_name = $this->get_name('Company', $com_id);
                        }
                    }
                }
                $arr_set['field']['panel_3']['company']['default'] = $item_title['company'] = $com_name;
                $arr_set['field']['panel_3']['company_id']['default'] = $com_id;
            }

            if (isset($arr_set['field']['panel_1']['code']['default']))
                $item_title['code'] = $arr_set['field']['panel_1']['code']['default'];
            else
                $item_title['code'] = '1';

            $this->set('item_title', $item_title);

            //custom select data
            $datas = $this->general_select($arr_set['field']);
            $this->selectModel('Setting');
            if (isset($arr_set['field']['panel_3']['sell_by']['default']))
                $datas['oum'] = $this->Setting->select_option_vl(array('setting_value' => 'product_oum_' . strtolower($arr_set['field']['panel_3']['sell_by']['default'])));
            asort($datas['category']);//pr($datas['category']);die;
            $this->set('arr_options', $datas);

			//END custom
            //show footer info
            if (count($arr_tmp) > 0)
                $this->show_footer_info($arr_tmp);


            //add, setup field tự tăng
        }else {
            $this->redirect(URL.'/products/add');
        }

		//custom list tax
        $this->set('arr_settings', $arr_set);
        $this->sub_tab('', $iditem);
        parent::entry();
		$arr_options_custom = array();
		$arr_options_custom['gst_tax'] = $arr_options_custom['pst_tax'] = '';
		$this->selectModel('Tax');
		$arr_options_custom['gst_tax'] = $arr_options_custom['pst_tax'] = $this->Tax->tax_select_list();
		$arr_options_custom['oum_depend'] = array('unit'=>'Unit','Sq.ft.'=>'Sq.ft.', 'Lr. ft.'=>'Lr. ft.');
		$this->set('arr_options_custom',$arr_options_custom);

        $this->selectModel('Setting');

        $product_option_type = $this->Setting->select_option_vl(array('setting_value' => 'product_option_type'));
        if (empty($product_option_type)) {
            $product_option_type = array();
        }
        $this->set('product_option_type', $product_option_type);
        if (!isset($arr_tmp['option_type'])) {
            $arr_tmp['option_type'] = '';
        }
        $this->set('option_type', $arr_tmp['option_type']);

        if (!isset($arr_tmp['cate_more'])) {
            $arr_tmp['cate_more'] = array();
        }        
        $this->set('cate_more', $arr_tmp['cate_more']);
        $categories = $this->Setting->select_option_vl(array('setting_value'=>'product_category'));
        $this->set('categories', $categories);
    }

    public function entry_search() {
        //parent
        $arr_set = $this->opm->arr_settings;
        $arr_set['field']['panel_1']['code']['lock'] = '';
        $arr_set['field']['panel_4']['status']['not_custom'] = '0';
        $arr_set['field']['panel_4']['status']['default'] = '';
        $arr_set['field']['panel_1']['product_type']['default'] = '';
		$arr_set['field']['panel_1']['group_type']['default'] = '';
		$arr_set['field']['panel_1']['group_type']['lock'] = '';
        $arr_set['field']['panel_2']['thickness_unit']['default'] = '';
        $arr_set['field']['panel_2']['sizew_unit']['default'] = '';
        $arr_set['field']['panel_2']['sizeh_unit']['default'] = '';
        $arr_set['field']['panel_3']['sell_by']['default'] = '';
        $arr_set['field']['panel_3']['oum']['default'] = '';

		$arr_setting['field']['panel_3']['unit_price']['type'] = 'price';
		$arr_setting['field']['panel_3']['cost_price']['type'] = 'hidden';
		$arr_setting['field']['panel_3']['oum_depend']['type'] = 'select';
		unset($arr_setting['field']['panel_3']['none21']);
		unset($arr_setting['field']['panel_3']['none22']);
		$arr_setting['field']['panel_3']['pst_tax']['moreclass'] = 'fixbor3';

        $this->set('search_class', 'jt_input_search');
        $this->set('search_class2', 'jt_select_search');
        $this->set('search_flat', 'placeholder="1"');

		//set default
        $where = array();
        if ($this->Session->check($this->name . '_where')){
            //$where = $this->Session->read($this->name . '_where');
			$this->Session->write($this->name . '_where',array());
		}
        if (count($where) > 0) {
            foreach ($arr_set['field'] as $ks => $vls) {
                foreach ($vls as $field => $values) {
                    if (isset($where[$field])) {
                        $arr_set['field'][$ks][$field]['default'] = '';//$where[$field]['values'];
                    }
                }
            }
        }
        //end parent
        $this->set('arr_settings', $arr_set);
    }

    public function lists() {
        $this->lists_mod = 'custom';
        $this->selectModel('Company');
        $this->set('companies_class', $this->Company);
		$this->Session->write($this->name.'_lists_search_sort', array('code', 1));
        parent::lists();
    }

    public function popup($keys = '') {
        $keysearch = array(
            'name' => array(
                'name' => 'Filter list below: Name/Code/SKU',
                'search_type' => 'text',
            ),
            'company_name' => array(
                'name' => 'Supplier',
                'search_type' => 'text',
            ),
			'company_id' => array(
                'name' => '',
                'search_type' => 'hidden',
            ),
            'product_type' => array(
                'name' => 'Type',
                'search_type' => 'select',
                'width' => '50',
            ),
            'category' => array(
                'name' => 'Category',
                'search_type' => 'select',
                'width' => '50',
            ),
            /*'status' => array(
                'name' => 'Active',
                'search_type' => 'select',
                'width' => '50',
            ),*/
			/*'approved' => array(
                'name' => 'Approved',
                'search_type' => 'checkbox',
                'width' => '50',
				'css'=>'margin-left: 872px;padding:0px 18px 1px;border-radius:13px;color:#FFF;margin-top:1px;',
            ),*/
			'assemply_item' => array(
                'name' => 'Assembly',
                'search_type' => 'hidden',
            ),
			'group_type' => array(
                'name' => 'Type',
                'search_type' => 'hidden',
            ),
            'is_costing'=>array(
                'name' => 'Is costing',
                'search_type' => 'hidden',
                                ),
            'prefer_customer_id' => array(
                'name' => 'Prefer Products',
                'search_type' => 'checkbox',
                'css' => 'float: right; position: inherit; background: #fff; margin-right: 70px;'
            ),
        );

		//pr($_REQUEST);die;
		//khóa input supplier
		$arr_off = array();
		if(isset($_REQUEST['no_supplier']) || isset($_REQUEST['arr_off']['company_name'])){
			unset($keysearch['company_name']);
			$arr_off['company_name'] = 'off';
		}

		//bật tính năng cho PO
		if(isset($_REQUEST['ispo'])){
			$this->set('lockproduct','1');//$this->set('ispo','1');
            unset($keysearch['prefer_customer'], $keysearch['prefer_customer_id']);
		}
		if(isset($_REQUEST['lockproduct'])){
			$this->set('lockproduct','1');
		}
        if(isset($_REQUEST['products_is_costing']))
            $this->set('is_costing','1');

		if(!isset($_REQUEST['products_assemply_item'])){
			unset($keysearch['assemply_item']); // khoa input neu ko co trong parameter
		}else{
			$keysearch['product_type']['search_type'] = 'hidden'; //neu la co assemply_item thi ko dung product_type
		}

		if(!isset($_REQUEST['products_group_type'])){
			unset($keysearch['group_type']); // khoa input neu ko co trong parameter
		}

		if(!isset($_REQUEST['products_group_type'])){
			unset($keysearch['group_type']); // khoa input neu ko co trong parameter
		}

		if(isset($_REQUEST['arr_off'])){
			$arr_off = $_REQUEST['arr_off'];
			foreach($arr_off as $kk=>$vv){
				unset($keysearch[$kk]);
			}
		}

        $this->set('keysearch', $keysearch);

        $this->selectModel('Setting');
        $select_data = array();
        $select_data['status'] = array(
            '1' => 'Yes',
            '2' => 'No',
        );

        $select_data['category'] = $this->Setting->select_option_vl(array('setting_value' => 'product_category'));
        asort( $select_data['category']);
        $select_data['product_type'] = $this->Setting->select_option_vl(array('setting_value' => 'product_type'));
        $this->set('select_data', $select_data);

        $ctrl = 'products';
        $arr_where = array();
        if(isset($_REQUEST['products_is_costing']) && $_REQUEST['products_is_costing']!=''){
            $group_id = array();
            $id = new MongoId($this->get_id());
            $group_id[] = $id;
            $data = $this->opm->get_data_costing((string)$id);

            if(!empty($data['useon'])){
                foreach($data['useon'] as $value)
                    $group_id[] = new MongoId($value['_id']);
            }
            $arr_where = array(
                               '_id'    => array(
                                        'values' => $group_id,
                                        'operator'=>'nin'
                                                 )
                               );
        }
        //set default
        if (!isset($_REQUEST[$ctrl . '_status'])) {
            $arr_where['status']['values'] = 1;
            $arr_where['status']['operator'] = '=';
        }
        if(isset($_REQUEST['products_product_id'])&&strlen($_REQUEST['products_product_id'])==24){
            $arr_where['_id']['values'] = new MongoId($_REQUEST['products_product_id']);
            $arr_where['_id']['operator'] = '=';
        }
		//search po
		if(isset($_REQUEST['company_id'])){
			$this->set('po_supplier',$_REQUEST['company_id']);
		}else
			$this->set('po_supplier','');
        //set where search
        foreach ($keysearch as $kss => $vss) {
			if (isset($_REQUEST[$ctrl . '_' . $kss]) && $_REQUEST[$ctrl . '_' . $kss] != '') {
                if ($vss['search_type'] == 'select' || $kss == 'assemply_item' || $kss == 'product_type' || $kss=='company_id')
                    $arr_where[$kss]['operator'] = '=';
                else
                    $arr_where[$kss]['operator'] = 'LIKE';
                $arr_where[$kss]['values'] = str_replace("(",".*.",$_REQUEST[$ctrl . '_' . $kss]);
				$arr_where[$kss]['values'] = str_replace(")",".*.",$arr_where[$kss]['values']);
            }
        }
        if( isset($_GET['prefer_customer_id']) && strlen($_GET['prefer_customer_id']) == 24
            || isset($_REQUEST['products_prefer_customer_id']) && strlen($_REQUEST['products_prefer_customer_id']) == 24 ) {
            $arr_where['prefer_customer_id']['values'] = new MongoId(isset($_REQUEST['prefer_customer_id']) ? $_REQUEST['prefer_customer_id'] : $_REQUEST['products_prefer_customer_id']);
            $arr_where['prefer_customer_id']['operator'] = '=';
            $this->set('prefer_check', true);
        }
        if( isset($_GET['prefer_customer_id']) && isset($arr_where['product_type']) ) {
            unset($arr_where['product_type']);
        }
		$this->set('arr_off', $arr_off);// khóa ô search
        $this->set('arr_where_popro', $arr_where); //giá trị các ô search

        unset($arr_where['is_costing']);

		if(isset($arr_where['company_id']['values']) && strlen($arr_where['company_id']['values'])==24){
			$arr_where['company_id']['values'] = new MongoId($arr_where['company_id']['values']);
			unset($arr_where['company_name']);
		}
		//$this->Session->write($this->name . '_where',array());
        $this->Session->write($this->name . '_where_popup', $arr_where); //đổi lại session dành riêng cho popup phân biệt với list: _where => _where_popup
        // BaoNam
        if(isset($_POST['key_return'])) {
            $keys = $_POST['key_return'];
        }

		//pr($arr_where);die;
        parent::popup($keys);
		// pr($this->Session->read($this->name . '_where_popup'));
		//pr($this->Session->read($this->name . '_where'));
    }



	// Action Upload
    public function upload() {
        $post_file = array();
        $post_file = $_FILES['products_upload'];
        $file = $this->Common->move_file($post_file);
        $arr_save = array();
        $tempdata = $this->opm->select_one(array('_id' => new MongoId($this->get_id())));
        $arr_save['name'] = $post_file['name'];
        $arr_save['path'] = $file;
        $arr_save['type'] = $post_file['type'];
        if ($arr_save['type'] == 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet') {
            $arr_save['type'] = 'excel';
        }
        $arr_save['ext'] = strtolower(substr(strrchr($post_file['name'], '.'), 1));
        $arr_save['create_by_module'] = 'Product';
        $arr_save['category'] = '';
        $arr_save['location'] = 'Inventory-Products';
        $arr_save['description'] = 'No.' . $tempdata['code'] . ':' . $tempdata['name'];

        $this->selectModel('Doc');
        $arr_save['no'] = $this->Doc->get_auto_code('no');
        if ($this->Doc->save($arr_save)) {
            $arr_use = array();
            $arr_use['doc_id'] = $this->Doc->mongo_id_after_save;
            $arr_use['module'] = 'Product';
            $arr_use['module_controller'] = 'products';
            $arr_use['create_by_module'] = 'Product';
            $arr_use['module_detail'] = $tempdata['name'];
            $arr_use['module_id'] = new MongoId($this->get_id());
            $arr_use['module_no'] = $tempdata['code'];
            $arr_use['created_by'] = new MongoId($this->opm->user_id());
            $file = str_replace('\\', '/', $file);
            $tempdata['products_upload'][] = array(
                                                'deleted'=>false,
                                                'doc_id'=>$arr_use['doc_id'],
                                                'path'  =>   $file
                                                );

            $this->opm->save($tempdata);
            $this->selectModel('DocUse');
            if ($this->DocUse->save($arr_use))
                $this->redirect('/' . $this->params->params['controller'] . '/entry');
        }else {
            $this->redirect('/' . $this->params->params['controller'] . '/entry');
        }
    }


	public function pricebreaks_range_comparasion($range=array(),$the_one=0,$key=0,$field='range_from'){
        if(!empty($range)){
            foreach($range as $k=>$value){
                if( isset($value['deleted'])&&$value['deleted']) continue;
                if(!isset($value['range_from']) && !isset($value['range_to'])) continue;
                if( $key==$k )  continue;
                if($the_one>$value['range_from']&&$the_one<$value['range_to'] && $range[$key]['sell_category']==$value['sell_category']){
                    return false;
                }
            }
        }
        return true;
    }

	public function arr_associated_data($field = '', $value = '', $valueid = '',$field_name = '') {
		$arr_return[$field] = $value;
		$query = $this->opm->select_one(array('_id' => new MongoId($this->get_id())));

		if($field=='thickness' || $field=='cost_price'){
			$arr_return[$field] = (float)$value;
		} else if($field == 'prefer_customer') {
            if (strlen($valueid) == 24)
                $arr_return['prefer_customer_id'] = new MongoId($valueid);
            else
                $arr_return['prefer_customer_id'] = '';
            return $arr_return;
        } else if($field=='sku'){
			$arr_return[$field] = (string)$value.' ';
		} else if($field=='production_step'){
            $data = (array)json_decode($_POST['arr']);
            if($data['keys']=='add'){
                $this->selectModel('Equipment');
                $arr_tag = $this->Equipment->select_combobox_asset_old();
                foreach($value as $k=>$v){
                    if(isset($v['deleted'])&&$v['deleted']) continue;
                    if(isset($arr_tag[(string)$v['tag_key']])) unset($arr_tag[(string)$v['tag_key']]);
                }
                if(empty($arr_tag)){
                    echo 'max_tag';
                    die;
                }
                $value[$valueid]['tag'] = reset($arr_tag);
                $value[$valueid]['tag_key'] = new MongoId(key($arr_tag));
                $arr_return[$field] = $value;
                return $arr_return;
            }
            else if($data['keys']=='update'){
                $value[$valueid]['tag_key'] = new MongoId($value[$valueid]['tag_key']);
                $arr_return[$field] = $value;
                return $arr_return;
            }
        } else if($field=='sellprices'){
            $this->selectModel('Setting');
            $sell_category = $this->Setting->select_option_vl(array('setting_value'=>'products_sell_category'));
            $i = 1; $same = 0;
			$arr_list_cate = array();
            if(isset($query['sellprices'])&&!empty($query['sellprices'])){
                foreach($query['sellprices'] as $key=>$val)
                    if(isset($val['deleted']) && !$val['deleted']){
                        $i++;
                        if(trim($value[$valueid]['sell_category'])!='' && $key!=$valueid //Tru no ra, so sanh tat ca
                            &&$value[$valueid]['sell_category']==$val['sell_category'])
                            $same++;
						if(isset($val['sell_category']) && $val['sell_category']!='')
						$arr_list_cate[] = $val['sell_category'];
                    }
            }
            //$_POST dang json_encode cua object
            $data = (array)json_decode($_POST['arr']);
            if($same>0){
                echo 'existed';
                die;
            }//kei, Add moi can dem vuot gioi han, update ko can
            else if($data['keys']!='update'&&$i>3){
                echo 'reach_limit';
                die;
            }


			//neu la default
			if(isset($value[$valueid]['deleted']) && !$value[$valueid]['deleted'] && isset($value[$valueid]['sell_default']) && $value[$valueid]['sell_default']==1){
				$arr_return['sell_price'] = (float)$value[$valueid]['sell_unit_price'];
			}


			$actionjson = json_decode($_POST['arr']);
			$actionjson = (array)$actionjson;
			//neu la add moi sell_category =''
			if(isset($query['sellprices']) && is_array($query['sellprices']) && count($query['sellprices'])>0 && isset($actionjson['keys']) && $actionjson['keys']=='add' && isset($value[$valueid]['sell_category']) && $value[$valueid]['sell_category']==''){

                foreach($sell_category as $kk=>$vv){
					if(!in_array($kk,$arr_list_cate)){
                    	$value[$valueid]['sell_category'] = $kk;
						break;
					}
                }
				//pr($value[$valueid]);die;
			}
			$arr_return[$field] = $value;

        } else if($field=='price_breaks_type'){
            $this->recal_table_price('','',$value);
        } else if($field=='cost_price'){
            $this->recal_table_price('',$value);
        } else if($field=='pricebreaks'){
            $data = (array)json_decode($_POST['arr']);
            $max = 0;
            if($data['keys']=='add'){
                //Tim gia tri lon nhat
                if(isset($query['pricebreaks'])&&!empty($query['pricebreaks'])){
                    $value[$valueid]['sell_category'] = (isset($_SESSION['Products_current_category'])? $_SESSION['Products_current_category'] : '');
                    foreach($query['pricebreaks'] as $val){
                        if($val['deleted']) continue;
                        if($val['sell_category']!=$value[$valueid]['sell_category']) continue;
                        if(isset($val['range_to'])&&$val['range_to']!=''){
                            if($max<$val['range_to'])
                                $max = $val['range_to'];
                        }
                    }
                }
                $range_from = 1;
                $range_to = $range_from + 4;
                if($max>0){
                    $range_from = $max;
                    $range_to = $range_from + 5;
                }
                $value[$valueid]['range_from'] = $range_from;
                $value[$valueid]['range_to'] = $range_to;
                $arr_return['pricebreaks'] = array();
                $arr_return['pricebreaks'] = $value;
                return $arr_return;
            }else if($data['keys']=='update'){
                $range_from_in_range = 0;
                $range_to_in_range = 0;
                if($value[$valueid]['range_from']>$value[$valueid]['range_to']){
                    echo 'range_from_greater_than_range_to';
                    die;
                }
                if(isset($query['pricebreaks'])&&!empty($query['pricebreaks'])&&isset($_POST['fieldchage'])){
                    if($_POST['fieldchage']=='range_from'){
                        if(!$this->pricebreaks_range_comparasion($query['pricebreaks'],$value[$valueid]['range_from'],$valueid)){
                            echo 'range_from_in_range';
                            die;
                        }
                        $arr_tmp = $this->opm->aasort($query['pricebreaks'],'range_from',1);
                        foreach($arr_tmp as $key=>$val){
                            if($val['deleted'] || $key==$valueid) continue;
                            if(isset($_SESSION['Products_current_category'])
                                &&$val['sell_category']!=$_SESSION['Products_current_category']) continue;
                            if($val['range_from']>=$value[$valueid]['range_from'] ){
                                $value[$valueid]['range_to'] = $val['range_from'];
                                break;
                            }
                        }
                    }
                    else if($_POST['fieldchage']=='range_to'){
                        if(!$this->pricebreaks_range_comparasion($query['pricebreaks'],$value[$valueid]['range_to'],$valueid)){
                            echo 'range_to_in_range';
                            die;
                        }
                        $arr_tmp = $this->opm->aasort($query['pricebreaks'],'range_to',-1);
                        foreach($arr_tmp as $key=>$val){
                            if($val['deleted'] || $key==$valueid) continue;
                            if(isset($_SESSION['Products_current_category'])
                                &&$val['sell_category']!=$_SESSION['Products_current_category']) continue;
                            if($val['range_to']<=$value[$valueid]['range_to']){
                                $value[$valueid]['range_from'] = $val['range_to'];
                                break;
                            }
                        }
                    }
                    $arr_tmp = $this->opm->aasort($value,'range_from',1);
                    $arr_return['pricebreaks'] = array();
                    if($value[$valueid]['range_from']!=$value[$valueid]['range_to']){
                        foreach($arr_tmp as $value){
                            if($value['deleted']) continue;
                            $arr_return['pricebreaks'][] = $value;
                        }
                    } else if($value[$valueid]['range_from']==$value[$valueid]['range_to']){
                        echo 'range_from_equals_range_to';
                        die;
                    }
                     return $arr_return;
                }
            }
        }


		/* Main field anh huong den note supplier */
		$main_field = array(
							'sku','name','company_name',
							'sizew','sizeh','sizew_unit',
							'sizeh_unit','sell_by','oum','sell_price','oum_depend','unit_price'
							);
		$arr_num = array('sizew','sizeh','sell_price','unit_price');

		$arr_change_unitprice = array('sizew','sizeh','sizew_unit','sizeh_unit','oum','oum_depend','sell_by','sell_price','unit_price');

		if(in_array($field,(array)$main_field)){
            $this->selectModel('Costingqueue');
            $costings = $this->opm->get_data_costing($query['_id']);
            if(!empty($costings['useon'])){
                $arr_save['product_id'] = $query['_id'];
                foreach($costings['useon'] as $costing){
                    $arr_save['costings'][] = $costing['_id'];
                }
                $this->Costingqueue->add($arr_save);
                $this->background_process('updatecostings '.$query['_id'],'cake_console');
            }
			$idm = $this->get_id();
			if($field=='sell_by' && $value=='unit'){
				$arr_return['oum'] = $query['oum'] = 'unit';
			}
			if($field=='sell_by' && $value=='area'){
				$arr_return['oum'] = $query['oum'] = 'Sq.ft.';
			}

			//tính lại unit price
			if(in_array($field,(array)$arr_change_unitprice)){
				$items = $query;
				if(isset($arr_return['oum']))
				$items['oum'] = $arr_return['oum'];
				$items[$field] = $value;
				$cal_price = new cal_price();
				$cal_price->arr_product_items = $items;
				$cal_price->cal_unit_price_for_product();
				foreach($arr_change_unitprice as $keys){
					if(isset($cal_price->arr_product_items[$keys]))
						$arr_return[$keys] = $cal_price->arr_product_items[$keys];
				}
			}

			//update supplier trong product cha neu la vendor
			if($idm !='' && isset($query['parent_product_id']) && strlen((string)$query['parent_product_id'])==24){
				$parent_pro = $this->opm->select_one(array('_id' => $query['parent_product_id']));
				if(isset($parent_pro['supplier']) && is_array($parent_pro['supplier']) && count($parent_pro['supplier'])>0){
				foreach($parent_pro['supplier'] as $kk=>$vv){
					$arr_tmp = array();
					$arr_tmp['supplier'] = $parent_pro['supplier'];
					$arr_tmp['_id'] 	 = $parent_pro['_id'];
						if(!$vv['deleted'] && isset($vv['product_id']) && (string)$vv['product_id'] ==$idm){
							//neu co vendor trong parent product thi update lai
							if($field =='company_name'){
								$arr_tmp['supplier'][$kk][$field] = $value;
								$arr_tmp['supplier'][$kk]['company_id'] = new MongoId($valueid);
							}else if(in_array($field,(array)$arr_num)){
								$arr_tmp['supplier'][$kk][$field] = (float)$value;
							}else
								$arr_tmp['supplier'][$kk][$field] = (string)$value;

							foreach($arr_change_unitprice as $keys){
								if(isset($arr_return[$keys])){
									if(in_array($keys,(array)$arr_num))
										$arr_tmp['supplier'][$kk][$keys] = (float)$arr_return[$keys];
									else
										$arr_tmp['supplier'][$kk][$keys] = (string)$arr_return[$keys];
								}
							}
							$this->opm->save($arr_tmp);
						}

						//kiem tra va set lai current
					}

				}

			}


			/* Current Supplier */
			if($field =='company_name'){
                if (strlen($valueid) == 24)
				    $arr_return['company_id'] = new MongoId($valueid);
                else
                    $arr_return['company_id'] = '';

				//set default cho not supplier neu la product


				if(isset($query['supplier']) && is_array($query['supplier']) && count($query['supplier'])>0){
					$supplier_po = $query['supplier'];
					$arrcost = array();
					//$cal_price=new cal_price();
					//loop va xac dinh current = on
					foreach($supplier_po as $kk=>$vv){
						if(!$vv['deleted'] && $vv['current']=='on'){
							$reset_key = $kk;
						}
						if(isset($vv['company_id']) && (string)$vv['company_id'] == $valueid){
							$comid = $vv['company_id'];
							//$cal_price->arr_product_items = $vv;
							//$cal_price->cal_area();
							//$aftercal = $cal_price->arr_product_items;
						}else
							continue;

						if((!isset($newsell_price)) || $newsell_price > (float)$vv['unit_price'] && isset($newsell_price)){							$newkeyset = $kk;
							$newsell_price = (float)$vv['unit_price'];
						}


					}
					//kiem tra va set lai current
					if(isset($newkeyset)  && isset($reset_key)){
						$supplier_po[$reset_key]['current'] = '';
						$supplier_po[$newkeyset]['current'] = 'on';
						$arr_return['supplier'] = $supplier_po;
					}
					//else : tao supplier moi
				}

			}


			// Tao moi hoac update Pricing
			if($field=='sell_price'){
				//kiem va lap vong sellprices
				if(isset($query['sellprices']) && is_array($query['sellprices']) && count($query['sellprices'])>0){
					$is_null = 1;
					foreach($query['sellprices'] as $keys=>$values){
						if(isset($values['sell_default']) && $values['sell_default']==1 && isset($values['deleted']) && !$values['deleted']){
							$is_null = 0;
							$arr_return['sellprices'] = $query['sellprices'];
							$arr_return['sellprices'][$keys]['sell_category'] = 'Retail';
							$arr_return['sellprices'][$keys]['sell_unit_price'] = $value;
							break;
						}
					}

					if($is_null==1){
						$arr_return['sellprices'][0]['sell_category'] = 'Retail';
						$arr_return['sellprices'][0]['sell_unit_price'] = $value;
						$arr_return['sellprices'][0]['sell_default'] = 1;
						$arr_return['sellprices'][0]['deleted'] = false;
					}

				}else{
					$arr_return['sellprices'][0]['sell_category'] = 'Retail';
					$arr_return['sellprices'][0]['sell_unit_price'] = $value;
					$arr_return['sellprices'][0]['sell_default'] = 1;
					$arr_return['sellprices'][0]['deleted'] = false;
				}
			}


		}





		/* Supplier po của main product */
		if($field =='supplier'){
			$arr_temp = $arr_vendor = array();
			//save product vendor
			if(isset($value[$valueid])){
				$compo = $value[$valueid];

				if(isset($compo['product_id']))
					$arr_temp['_id'] = $compo['product_id'];
				if(isset($compo['company_name']))
					$arr_temp['company_name'] = $compo['company_name'];
				if(isset($compo['company_id']))
					$arr_temp['company_id'] = $compo['company_id'];
				if(isset($compo['sku']))
					$arr_temp['sku'] = $compo['sku'];
				if(isset($compo['name']))
					$arr_temp['name'] = $compo['name'];
				$cal_price = new cal_price();
				$cal_price->arr_product_items = $compo;
				$cal_price->cal_unit_price_for_product();
				$arrnew = $cal_price->arr_product_items;
				foreach($arr_change_unitprice as $keys){
					if(isset($arrnew[$keys])){
						$arr_temp[$keys] = $value[$valueid][$keys] = $arrnew[$keys];
					}
				}
				$arr_temp['parent_product_name'] = $query['name'];
				$arr_temp['parent_product_id'] = $query['_id'];
				$arr_temp['parent_product_code'] = $query['code'];
				$arr_temp['group_type'] = 'BUY';
				//$arr_temp['code'] = $this->opm->get_auto_code('code');
				//$arr_temp['status'] = 1;
				$arr_temp['product_type'] = 'Vendor Stock';

				if(isset($compo['product_id'])) // chi cho update
					$this->opm->save($arr_temp);
				//$value[$count]['product_id'] = $this->opm->mongo_id_after_save;

				//Set Current supplier cho product
				if(isset($compo['current']) && $compo['current']=='on' && isset($compo['company_name']) && isset($compo['company_id'])){
					$arr_return['company_name'] = $compo['company_name'];
					$arr_return['company_id'] = $compo['company_id'];

					//them neu chua co item nay
					$more_madeup = 1; $idmore = -1;
					if(isset($query['madeup']) && is_array($query['madeup']) && count($query['madeup'])>0){
						$arr_return['madeup'] = $query['madeup'];
						foreach($query['madeup'] as $keys => $values){
							if(isset($values['deleted']) && !$values['deleted'] && $values['product_id'] == $compo['product_id']){
								$more_madeup = 0; //ko cho tao moi

							}
							$idmore = $keys;

						}
					}
					if($more_madeup == 1){
						$idmore = 0;
						if(isset($compo['product_id']))
							$arr_return['madeup'][$idmore]['product_id'] = $compo['product_id'];
						if(isset($compo['name']))
							$arr_return['madeup'][$idmore]['product_name'] = $compo['name'];
						if(isset($compo['oum']))
							$arr_return['madeup'][$idmore]['oum'] = $compo['oum'];
						if(isset($compo['unit_price']))
							$arr_return['madeup'][$idmore]['unit_price'] = $compo['unit_price'];
						$arr_return['madeup'][$idmore]['product_type'] = '';
						$arr_return['madeup'][$idmore]['markup'] = 0;
						$arr_return['madeup'][$idmore]['margin'] = 0;
						$arr_return['madeup'][$idmore]['quantity'] = 1;
						$arr_return['madeup'][$idmore]['deleted'] = false;
					}

				}

			}
			$arr_return[$field] = $value;
		}

		/* Locations */
		$unset = 0;
		if($field =='locations'){
			if(isset($query['locations']) && is_array($query['locations']) && count($query['locations'])>0){
                $arr_return[$field] = $query['locations'];
				$arr_return[$field][$valueid] = array_merge((array)$arr_return[$field][$valueid],(array)$value[$valueid]);
			}
		} else if($field =='stocktakes'){
			$arr_return['stocktakes'] = $value;
			if(isset($value[$valueid])){
				$arr_tmp = $value[$valueid];

				$add_local = 1;
				$last_id = -1;

				//lap vong location de gan gia tri cu vao qty_in_stock (stocktakes) và check tao moi, dem chi muc cuoi
				if(isset($query['locations']) && is_array($query['locations']) && count($query['locations'])>0){
					$arr_return['locations'] = $query['locations'];
					foreach($query['locations'] as $kk=>$vv){
						//truong hop ton tai kho can tim và không có qty_in_stock (stocktakes)
						if(!$vv['deleted'] && isset($arr_tmp['location_id']) && $vv['location_id']==$arr_tmp['location_id'] && (!isset($arr_tmp['qty_in_stock']) || (isset($arr_tmp['qty_in_stock']) && $arr_tmp['qty_in_stock']=='')) ){
							if(isset($vv['total_stock']) && isset($vv['total_stock'])!='')
								$arr_tmp['qty_in_stock'] = (int)$vv['total_stock'];
							else
								$arr_tmp['qty_in_stock'] = 0;
							$add_local = 0; //bật cờ ko cho add

						//truong hop co tim thay id location, ko thay đổi $arr_tmp['qty_in_stock']
						}else if(!$vv['deleted'] && isset($arr_tmp['location_id']) && $vv['location_id']==$arr_tmp['location_id']){
							$add_local = 0; //bật cờ ko cho add
						}

						$last_id = $kk; //đếm id cuối cùng
					}

				}

				//thay doi qty_amended
				if(isset($arr_tmp['qty_counted']) && $arr_tmp['qty_counted']!=''){
					$arr_tmp['qty_amended'] = (int)$arr_tmp['qty_counted'];
					if(isset($arr_tmp['qty_in_stock']))
						 $arr_tmp['qty_amended'] = $arr_tmp['qty_amended'] - (int)$arr_tmp['qty_in_stock']; //tính amend
					$arr_return['qty_in_stock'] = (int)$arr_tmp['qty_counted'];//gan so moi vao total bên general

					//UPDATE LOCATION
					if(isset($query['locations']) && is_array($query['locations']) && count($query['locations'])>0){
						$arr_return['qty_in_stock'] = 0;
						foreach($query['locations'] as $kk=>$vv){ // lặp vòng location để tính tổng và gán total mới
							if(!$vv['deleted']){
								if(isset($arr_tmp['location_id']) && $vv['location_id']==$arr_tmp['location_id']){
									$arr_return['locations'][$kk]['total_stock'] = (int)$arr_tmp['qty_counted'];
								}
								$arr_return['qty_in_stock'] += (int)$arr_return['locations'][$kk]['total_stock'];
							}
						}
					}
				}

				// Add location
				if($add_local ==1){
					$new_local = array();
					if(isset($arr_tmp['location_id']))
						$new_local['location_id'] = $arr_tmp['location_id'];
					if(isset($arr_tmp['location_name']))
						$new_local['location_name'] = $arr_tmp['location_name'];
					if(isset($arr_tmp['location_type']))
						$new_local['location_type'] = $arr_tmp['location_type'];
					if(isset($arr_tmp['stock_usage']))
						$new_local['stock_usage'] = $arr_tmp['stock_usage'];
					$new_local['low'] = '';
					$new_local['deleted'] = false;

					$new_local['qty_in_stock'] = (int)$arr_tmp['qty_counted'];

					$arr_return['locations'][$last_id+1] = $new_local;
				}

				//set user
				$arr_tmp['stocktakes_by'] = $this->opm->user_name();
				$arr_tmp['stocktakes_by_id'] = $this->opm->user_id();
				$value[$valueid] = $arr_tmp;
			}
			$arr_return['stocktakes'] = $value;
		} else if($field =='options'){
			/*if(isset($value[$valueid])){
				$cal_price = new cal_price();
				$cal_price->arr_product_items = $value[$valueid];
				$cal_price->cal_price_in_markup_margin();
				$value[$valueid] = array_merge($value[$valueid],(array)$cal_price->arr_product_items);
			}*/

			//tim group_type
			if(isset($value[$valueid]) && isset($value[$valueid]['option_group']) && $value[$valueid]['option_group']!=''){
				foreach($value as $kk=>$vv){
					if($kk!=$valueid && isset($vv['group_type']) && $vv['group_type']!='' && $vv['option_group'] == $value[$valueid]['option_group'])
						$value[$valueid]['group_type'] = $vv['group_type'];

				}
			}


			//kiem tra va update neu co check Require
			if(isset($value[$valueid]) && isset($value[$valueid]['require']) && $value[$valueid]['require']==1 && isset($value[$valueid]['group_type']) && $value[$valueid]['group_type']=='Exc'){
				foreach($value as $kk=>$vv){
					if($kk!=$valueid && isset($vv['option_group']) && $vv['option_group'] == $value[$valueid]['option_group'])
						$value[$kk]['require'] = 0;
				}
			}

			$arr_return[$field] = $value;
		}

		//pr($arr_return);die;
		return $arr_return;
	}


    public function delete_all_associate($ids='',$opname=''){
        //delete Amendments/ stocktakes for this item
        if($opname=='stocktakes'){
            $query = $this->opm->select_one(array('_id' => new MongoId($this->get_id())), array('stocktakes','locations','qty_in_stock'));
            $reset = 0;
            if(isset($query['stocktakes']) && is_array($query['stocktakes']) && isset($query['stocktakes'][$ids]['location_id'])){
                $location_id = $query['stocktakes'][$ids]['location_id'];
                foreach ($query['stocktakes'] as $key => $value) {
                    if(!$value['deleted'] && $value['location_id']==$location_id){
                        $reset++;
                    }
                }
                if($reset<=1){
                    foreach ($query['locations'] as $key => $value) {
                        if(!$value['deleted'] && $value['location_id']==$location_id){
                            $query['qty_in_stock'] = 0;
                            $query['locations'][$key]['total_stock'] = 0;
                            $query['locations'][$key]['qty_in_stock'] = 0;
                        }
                    }
                    $this->opm->save($query);
                }
            }
        }

    }

    //custom save_data function
    function save_data($field = '', $value = '', $ids = '', $valueid = ''){
        if(isset($_POST['field'])){

            //require enough products for combo
            if($_POST['field'] == 'category' && $_POST['value'] == 'Sub Combo'){
                $ids = new MongoId($this->get_id());
                $query = $this->opm->select_one(array('_id'=> $ids),array('options'));
                if(count($query['options']) >= 3){
                    $arr_category_combo = ['Appetizers', 'Banh Mi SUBS', 'Drinks'];
                    $error_message = 'Please add enough products to this combo, include '.implode(",", $arr_category_combo).'.';
                    $count_in_combo = 0;
                    foreach ($query['options'] as $key => $value) {
                        if($value['deleted'] == false)
                        {
                            $product_item = $this->opm->select_one(array('_id'=> new MongoId($value['product_id'])),array('_id', 'category'));
                            if($product_item)
                            {
                                if(isset($product_item['category']) && in_array($product_item['category'], $arr_category_combo))
                                {
                                    $count_in_combo++;
                                    array_diff($arr_category_combo, [$product_item['category']]);
                                }
                            }
                        }
                    }
                    if($count_in_combo >= 3)
                    {
                        parent::save_data();
                        return;
                    }
                }
                //$this->Session->setFlash($error_message,'default',array('class'=>'flash_message'));
                //$this->redirect(URL.'/products/entry/'.$this->get_id());
                echo json_encode(['status'=>'error', 'message'=>$error_message]);
                die;
            }
            else
            {
                parent::save_data();
            }
        }        
    }

    //Add or update field when this field change, use in js.ctp
    public function ajax_save() {
        if (isset($_POST['field']) && isset($_POST['value']) && isset($_POST['func']) && !in_array((string) $_POST['field'], $this->opm->arr_autocomplete())) {

            if ($_POST['func'] == 'add') {
                $ids = $this->opm->add($_POST['field'], $_POST['value']);
                $newid = explode("||", $ids);
                $this->Session->write($this->name . 'ViewId', $newid[0]);
            } else if ($_POST['func'] == 'update' && isset($_POST['ids'])) {
                $ids = $this->opm->update($_POST['ids'], $_POST['field'], $_POST['value']);

                $this->Session->write($this->name . 'ViewId', $_POST['ids']);
            }
            echo $ids;
        } else
            echo 'error';
        die;
    }

    //Add or update field when this field change, use in js.ctp
    public function ajax_box_update() {
        $ids = $this->get_id();
        if ($ids != '' && isset($_POST['moduleop']) && isset($_POST['field']) && isset($_POST['value'])) {
            $arr_vl = array();
            $arr_vl['moduleids'] = $ids;
            $arr_vl['moduleop'] = $_POST['moduleop']; //tên option của module
            $arr_vl['field'] = $_POST['field'];   // key của option
            $arr_vl['value'] = $_POST['value'];   // giá trị của key
            $idm = $_POST['ids'];   // giá trị id của option

            $arr_vl['opwhere'] = array('id' => $idm);
            if ($_POST['field'] == 'current') { // update current for company
                $arr_vl['ids'] = $_POST['ids'];
                $bbl = $this->opm->update_current_default($arr_vl);
            } else //update cost_pricee
                $bbl = $this->opm->update_value_option_of_module($arr_vl);

            //echo $bbl;
            if ($bbl)
                echo $bbl;
            else
                echo '';
        }
        die;
    }

    //load lại nội dung box
    public function ajax_contents_box($boxname = '', $new_values = '') {
        if (isset($_POST['boxname']))
            $boxname = $_POST['boxname'];
        if (isset($_POST['new_values']))
            $new_values = $_POST['new_values'];

        if (!$this->Session->check($this->name . '_sub_tab'))
            $sub_tab = 'general';
        else
            $sub_tab = $this->Session->read($this->name . '_sub_tab');
        $arr_set = $this->opm->arr_settings;
        //xác định giá trị box
        $subdatas[$boxname] = array();
        if ($boxname == 'same_category') {
            $query = $this->opm->select_all(array(
                'arr_where' => array('category' => $new_values),
                'arr_order' => array('_id' => -1)
            ));
            $subdatas[$boxname] = $query;
        }
        if ($boxname == 'pricingsummary') {
            $subdatas = $this->opm->get_data_pricing($this->get_id());
        }
        $this->set('blockname', $boxname);
        $this->set('arr_subsetting', $arr_set['relationship'][$sub_tab]['block']);
        $this->set('subdatas', $subdatas);
        $this->set('box_type', $arr_set['relationship'][$sub_tab]['block'][$boxname]['type']);
    }

    //load lại nội dung box
    public function reload_box($boxname = '') {
        if (isset($_POST['boxname']))
            $boxname = $_POST['boxname'];

        if (!$this->Session->check($this->name . '_sub_tab'))
            $sub_tab = $this->sub_tab_default;
        else
            $sub_tab = $this->Session->read($this->name . '_sub_tab');
        $arr_set = $this->opm->arr_settings;
        $subdatas[$boxname] = array();

        //custom
        if ($boxname == 'same_category') {
            $query = $this->opm->select_all(array(
                'arr_where' => array('category' => $new_values),
                'arr_order' => array('_id' => -1)
            ));
            $subdatas[$boxname] = $query;
        } else if ($boxname == 'madeup') {
            $subdatas = $this->opm->get_data_costing($this->get_id());
            //end custom
        } else if($boxname=='production_step'){
            $subdatas['production_step'] = $this->opm->get_product_asset($this->get_id());
        } else if($boxname=='pricebreaks'){
            $subdatas['pricebreaks'] = $this->opm->get_pricebreaks($this->get_id());
            //echo $_SESSION['Products_current_category'];die;
        }else {
            $arr_where = array('module_id' => $this->get_id(), 'option_name' => $boxname);
            $subdatas[$boxname] = $this->opm->get_optmodule($arr_where);
        }
        $this->set('blockname', $boxname);
        $this->set('arr_subsetting', $arr_set['relationship'][$sub_tab]['block']);
        $this->set('subdatas', $subdatas);
        $this->set('box_type', $arr_set['relationship'][$sub_tab]['block'][$boxname]['type']);
    }

    // Save option, giá trị post tu ajax: keys,option,name,id
    public function ajax_update_supplier() {
        // neu ton tai session id
        if ($this->Session->check($this->name . 'ViewId') && isset($_POST['arr'])) {
            $idsession = $this->Session->read($this->name . 'ViewId');
            $arr_tmp = $this->opm->select_one(array('_id' => new MongoId($idsession)));
            $ndt = $_POST['arr'];
            $opname = $ndt[1]; // nhan gia tri option=company
            $options = $arr_insert = array();

            //Trường hợp add thêm supplier
            if ($ndt[0] == 'supplier') {
                $new_opt = array();
                $new_opt['company_name'] = $ndt[2];
                $new_opt['company_id'] = $ndt[3];
                if (isset($arr_tmp['cost_price']))
                    $new_opt['cost_price'] = $arr_tmp['cost_price'];
                else
                    $new_opt['cost_price'] = 0;
                if (isset($arr_tmp[$opname]) && is_array($arr_tmp[$opname])) {
                    $options = (array) $arr_tmp[$opname];
                    $new_opt['current'] = '0';
                    $new_opt['cost_price'] = '';
                    array_push($options, $new_opt);
                } else {
                    $new_opt['current'] = '1';
                    $options = $new_opt;
                }

                $test = implode(",", $new_opt);
            } else if ($ndt[0] == 'change') {
                $opname = 'company';
                $options = $arr_tmp[$opname];
                foreach ($options as $kop => $arr_op) {//lap va thay doi gia tri
                    if ($kop == (int) $ndt[1]) {
                        $options[$kop]['company_name'] = $ndt[2];
                        $options[$kop]['company_id'] = $ndt[3];
                    }
                }

                //Kiểm tra tồn tại array option company, nếu có thì update
            } else if (isset($arr_tmp[$opname]) && is_array($arr_tmp[$opname])) {
                $options = $arr_tmp[$opname];
                foreach ($arr_tmp[$opname] as $kop => $arr_op)
                    if (isset($arr_op['current']) && (int) $arr_op['current'] == 1) {
                        $options[$kop]['company_name'] = $ndt[2];
                        $options[$kop]['company_id'] = $ndt[3];
                    }
            } else { // ngược lại tạo option company mới
                $options[0]['company_name'] = $ndt[2];
                $options[0]['company_id'] = $ndt[3];
                if (isset($arr_tmp['cost_price']))
                    $options[0]['cost_price'] = $arr_tmp['cost_price'];
                else
                    $options[0]['cost_price'] = 0;
                $options[0]['current'] = '1';
            }
            $arr_insert[$opname] = $options;
            $arr_insert['_id'] = $idsession;
            if ($this->opm->save($arr_insert))
                echo $this->opm->mongo_id_after_save;
        } else
            echo 'error';
        die;
    }

    public function swith_options($keys) {
        if ($keys == 'existing')
            echo URL . '/' . $this->params->params['controller'] . '/entry';
        else if ($keys == 'under')
            echo URL . '/' . $this->params->params['controller'] . '/entry';
        else if ($keys == 'over')
            echo URL . '/' . $this->params->params['controller'] . '/entry';

        else if ($keys == 'find_out_sync')
            echo URL . '/' . $this->params->params['controller'] . '/entry';
        else if ($keys == 'sync_stock_current')
            echo URL . '/' . $this->params->params['controller'] . '/entry';
        else if ($keys == 'sync_stock_found')
            echo URL . '/' . $this->params->params['controller'] . '/entry';

        else if ($keys == 'flagged_as_old'){
            $arr_where = array();
            $arr_where['status'] = array('values' => 2, 'operator' => '=');
            $this->Session->write($this->name . '_where', $arr_where);
            echo URL . '/' . $this->params->params['controller'] . '/lists';

		}else if ($keys == 'on_sales_orders') {
            $data = $this->db->command(array(
                'mapreduce'     => 'tb_salesorder',
                'map'           => new MongoCode('
                                                function() {
                                                    for( var i in this.products ) {
                                                        if( this.products[i].deleted ) continue;
                                                        if( typeof this.products[i].products_id != "object" ) continue;
                                                        id = this.products[i].products_id;
                                                        var value = { quantity: this.products[i].quantity  };
                                                        emit( id, value );
                                                    }
                                                }
                                                '),
                'reduce'        => new MongoCode('
                                                function(k, v) {
                                                    var data = {quantity : 0};
                                                    for( var i in v) {
                                                        data.quantity += v[i].quantity;
                                                    }
                                                    return data;
                                                }
                                            '),
                'query'         => array(
                                        'deleted' => false,
                                        'products' => array(
                                            '$exists' => true,
                                        )
                                    ),
                'out'           => array('merge' => 'tb_result')
            ));
            if( isset($data['ok']) ){
                $data = $this->db->selectCollection('tb_result')->find();
                $arrId = array();
                foreach($data as $value) {
                    if( !isset($value['_id']) || !is_object($value['_id']) ) continue;
                    $arrId[] = $value['_id'];
                }
                $arr_where = array();
                $arr_where['_id'] = array('values' => array( '_id' => array('$in' => $arrId) ), 'operator' => 'other');
                $this->Session->write($this->name . '_where', $arr_where);
            }
            $this->db->selectCollection('tb_result')->drop();
           echo URL . '/' . $this->params->params['controller'] . '/lists';
		}else if ($keys == 'low_stock' || $keys == 'print_low_stock') {
           echo URL . '/' . $this->params->params['controller'] . '/low_stock';

		}else if ($keys == 'print_list_stock') {
           echo URL . '/' . $this->params->params['controller'] . '/low_stock/1';

		}else if ($keys == 'print_list_stock_tracking') {
           echo URL . '/' . $this->params->params['controller'] . '/low_stock/2';

		}else if ($keys == 'print_low_stock_tracked') {
           echo URL . '/' . $this->params->params['controller'] . '/low_stock/3';


		}else if ($keys == 'create_purchase_order')
            echo URL . '/purchaseorders/create_pur_from_product/' . $this->get_id();

        else if ($keys == 'print_price_list')
            echo URL . '/' . $this->params->params['controller'] . '/entry';

	    else if ($keys == 'print_mini_list')
            echo URL . '/' . $this->params->params['controller'] . '/entry';

		else if ($keys == 'print_mini_list_for_internal_use')
	        echo URL . '/' . $this->params->params['controller'] . '/view_minilist';

		else if ($keys == 'finished_products'){
	        $arr_where['product_type'] = array('values' => 'Finished Goods', 'operator' => '=');
	        $this->Session->write($this->name . '_where', $arr_where);
	        echo URL . '/' . $this->params->params['controller'] . '/lists';

		}else if ($keys == 'print_customer_price_list'){
			echo URL . '/' . $this->params->params['controller'] . '/print_customer_price_list';

		}else if ($keys == 'sub_assembly_items'){
			$arr_products=array();
	        $this->selectModel('Product');
	        $v_product_id=$this->get_id();
	        if(isset($v_product_id)){

				$arr_products = $this->Product->select_all(array(
			        'arr_where'=>array('madeup.deleted'=>false)
		        ));
		        $arr_products1=$arr_products;
		        $v_find=array();
				$arr_temp=array();
		        foreach($arr_products as $key=>$value){
			        foreach($value['madeup'] as $key1=>$value1){
				         if(isset($value1['product_id'])&&is_object($value1['product_id']))
				            $arr_temp[] =$value1['product_id'];
			        }

		        }

		        $where_query['_id']['values'] = $arr_temp;
				$where_query['_id']['operator'] = 'in';
		        $this->Session->write($this->name . '_where', $where_query);

	        }
	        echo URL . '/' . $this->params->params['controller'] . '/lists';
        }else if ($keys == 'tracking_stock'){
            echo URL . '/' . $this->params->params['controller'] . '/tracking_stock';

		}else if ($keys == 'small_area_list'){
            echo URL . '/' . $this->params->params['controller'] . '/small_area_list';

        }else
            echo '';
        die;
    }
	public function print_customer_price_list(){
		$arr_data['product_category'] = $this->Setting->select_option_vl(array('setting_value'=>'products_sell_category'));
		$this->set('arr_data',$arr_data);

	}
	public function check_exist_price_list(){
		$data = $_POST;
		parse_str($data['data'],$data);
		if(!empty($data))
		{
			if($data['product_category'])
				$product_category = $data['product_category'];


			if(isset($data['group_by_category']))
				$group_by_category = '1';
			else
				$group_by_category = '0';

			if(isset($data['include_tax']))
				$include_tax = '1';
			else
				$include_tax = '0';


			$arr_product = $this->opm->select_all(array(
				'arr_where'=>array('sellprices.deleted'=>false)
			));

			$arr_result=array();

			$arr_category=array();

			$arr_category=$this->Setting->select_option_vl(array('setting_value'=>'product_category'));
//echo $arr_category['VH'];die;
			$this->selectModel('Tax');
			foreach($arr_product as $key=>$value){
					if(isset($value['category']))
					{
						$v_category=isset($arr_category[$value['category']])?$arr_category[$value['category']]:'No Category';
					}
					else
					{
						$v_category='No Category';
					}
					foreach($value['sellprices'] as $key1=>$value1){
						if(isset($value1['sell_category'])&&$value1['sell_category']==$product_category&&isset($value1['deleted'])&&$value1['deleted']==false)
						{

								if(isset($value1['sell_unit_price']))
								{
									$v_sell_unit_price=$this->opm->format_currency((float)$value1['sell_unit_price']);
								}
								else
								{
									$v_sell_unit_price=0;
								}


								$v_name=isset($value['name'])?$value['name']:'';

								$v_code=isset($value['code'])?$value['code']:'';

								$v_gst_tax=isset($value['gst_tax'])?$value['gst_tax']:'';
								if($v_gst_tax!='')
								{
									$arr_gst_tax_value=$this->Tax->select_one(array('province_key'=>$v_gst_tax));
									$v_gst_tax_value=isset($arr_gst_tax_value['fed_tax'])?$arr_gst_tax_value['fed_tax']:0;
								}
								else
								{
									$v_gst_tax_value=0;
								}
								if($v_gst_tax_value!=0)
								{
									$v_gst_tax_value_price=($v_sell_unit_price/100)*$v_gst_tax_value;
									$v_total=$v_sell_unit_price+$v_gst_tax_value_price;
								}
								else
								{
									$v_gst_tax_value_price=0;
									$v_total=$v_sell_unit_price;
								}


								$arr_result[$v_category][]=array('sell_unit_price'=>$v_sell_unit_price
									,'name'=>$v_name
									,'category'=>$v_category
									,'code'=>$v_code
									,'tax_percent'=>$v_gst_tax_value
									,'tax_value'=>$v_gst_tax_value_price
									,'total'=>$v_total
								);



						}

					}


			}

//			var_dump($arr_result);die;


			$date_now = date('Ymd');
			$time=time();
			$filename = 'CPL'.$date_now.$time;
			include(APP.'Vendor'.DS.'nguyenpdf.php');
			$pdf = new XTCPDF();
			date_default_timezone_set('UTC');
			$pdf->today=date("g:i a, j F, Y");
			$textfont = 'freesans';
			$pdf->SetCreator(PDF_CREATOR);
			$pdf->SetAuthor('Anvy Digital');
			$pdf->SetTitle('Anvy Digital Company');
			$pdf->SetSubject('Company');
			$pdf->SetKeywords('Company, PDF');
			$pdf->setPrintHeader(true);
			$pdf->setPrintFooter(true);
			$pdf->SetDefaultMonospacedFont(2);
			$pdf->SetMargins(10, 10, 10);


			$pdf->file1 = 'img'.DS.'null.png';
			$pdf->file2 = 'img'.DS.'null.png';
			$pdf->file3 = 'img'.DS.'null.png';
//			$pdf->address_1='';
//			$pdf->address_2='';
			$pdf->bar_words_content='';
			$pdf->bar_mid_content='';
			$pdf->bar_top_content='';
			$pdf->hidden_content='';
			$pdf->bar_big_content='';
			$pdf->printedat_left=139;
			$pdf->printedat_top=20;
			$pdf->time_left=156;
			$pdf->time_top=20;
			$pdf->time_printedat_font=9;


			$pdf->SetHeaderMargin(PDF_MARGIN_HEADER);
			$pdf->SetFooterMargin(PDF_MARGIN_FOOTER);
			$pdf->SetAutoPageBreak(TRUE, 30);

			$pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);
			if (@file_exists(dirname(__FILE__).DS.'lang'.DS.'eng.php')) {
				require_once(dirname(__FILE__).DS.'lang'.DS.'eng.php');
				$pdf->setLanguageArray($l);
			}
			$pdf->SetFont($textfont, '', 9);

			$html = '
			<table cellpadding="2" cellspacing="0" style="width:100%; margin: 0px auto">
				<tr>
					<td width="40%" valign="top" style="color:#1f1f1f;">
						<img src="img/logo_anvy.png" alt="" />
						<div style="margin-bottom:1px; margin-top:4px;border-bottom: 1px solid #cbcbcb;">
							<br>
							<br>
						</div>
						<div></div>
					</td>
					<td width="10%">&nbsp;</td>

					<td width="50%" valign="top" align="right">
						<div style=" text-align:right; font-size:20px; font-weight:bold; color: #919295;width:30%;"><span style="color:#b32017">P</span>roduct <span style="color:#b32017">C</span>ustomer <span style="color:#b32017">P</span>rice <span style="color:#b32017">L</span>ist<br><br></div>
						<br>
					</td>
				</tr>
			</table>

				';



			$pdf->AddPage();
			$pdf->address_1='';
			$pdf->address_2='';
			$pdf->print='';
			$pdf->today='';

			$html.='
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
	             	table.tab_nd .border_right{
	                    border-right:1px solid #E5E4E3;
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
		                table.tab_nd2 .border_right{
		                    border-right:1px solid #E5E4E3;
		                }
		                table.tab_nd2 .border_btom{
		                    border-bottom:1px solid #E5E4E3;
		                }
		                .size_font{
		                    font-size: 12px !important;
		                }

		            </style>
			';


			if($include_tax=='0'){
				$html.='<table cellpadding="3" cellspacing="0" class="maintb">
				<tr>
					<td width="15%" class="first top">
						&nbsp;Code
					</td>
					<td width="45%" class="top" align="left">
						&nbsp;Details
					</td>
					<td align="left" width="20%" class="top">
						&nbsp;Category
					</td>
					<td align="left" width="20%" class="top">
						&nbsp;Unit Price USD
					</td>

				</tr></table>';
			}
			else
			{
				$html.='<table cellpadding="3" cellspacing="0" class="maintb">
				<tr>
					<td width="10%" class="first top">
						&nbsp;Code
					</td>
					<td width="30%" class="top" align="left">
						&nbsp;Details
					</td>
					<td align="left" width="15%" class="top">
						&nbsp;Unit Price
					</td>
					<td align="left" width="15%" class="top">
						&nbsp;Tax rate
					</td>
					<td align="left" width="15%" class="top">
						&nbsp;Tax
					</td>
					<td align="left" width="15%" class="top">
						&nbsp;Total USD
					</td>

				</tr></table>';
			}



			$i=0;

			foreach($arr_result as $key=>$value)
			{

				if($group_by_category=='1')
				{
					$k=0;
					$html.='<table cellpadding="3" cellspacing="0" class="maintb">';
					$html .='<tr><td></td></tr>';
					$html .='<tr>';
					$html.='<td align="left" width="100%" class="top">';
					$html .=$key;
					$html.='</td>';
					$html.='</tr>';
					$html.='</table>';

				}
				else
				{
					$k=0;
				}
				foreach($value as $key1=>$value1)
				{

					if($k%2==0)
						$html .= '<table cellpadding="4" cellspacing="0" class="tab_nd">';
					else
						$html .= '<table cellpadding="4" cellspacing="0" class="tab_nd2">';

					$k++;

					if($include_tax=='0'){
						$html .= ' <tr class="border_2">
						<td width="15%"  align="left" class="first top border_left border_btom">';
					}else{
						$html .= ' <tr class="border_2">
						<td width="10%"  align="left" class="first top border_left border_btom">';
					}

						$html.=isset($value1['code'])?$value1['code']:'';


					if($include_tax=='0'){
						$html .='</td>
						<td align="left" width="45%" class="top border_btom border_left">';
					}else{
						$html .='</td>
						<td align="left" width="30%" class="top border_btom border_left">';
					}


						$html.=isset($value1['name'])?$value1['name']:'';



					if($include_tax=='0'){
						$html .='</td>
						<td align="left" width="20%" class="top border_btom border_left">';

							$html.=isset($value1['category'])?$value1['category']:'';
					}



					if($include_tax=='0'){
						$html .='</td>
						<td align="left" width="20%" class="top border_btom border_left border_right">';
					}
					else
					{
						$html .='</td>
						<td align="left" width="15%" class="top border_btom border_left border_right">';
					}





						$html.=isset($value1['sell_unit_price'])?$value1['sell_unit_price']:'';

					if($include_tax=='1'){
						$html .='</td>
						<td align="left" width="15%" class="top border_btom border_left border_right">';
							$html.=$value1['tax_percent']!=0?$value1['tax_percent'].'%':'0%';


						$html .='</td>
						<td align="left" width="15%" class="top border_btom border_left border_right">';
							$html.=$value1['tax_value']!=0?$value1['tax_value']:'0.00';


						$html .='</td>
						<td align="left" width="15%" class="top border_btom border_left border_right">';
							$html.=$value1['total']!=0?$value1['total']:'0.00';

					}


					$html.='</td></tr></table>';
					$i++;
				}
			}
			$html .= '
				<table cellpadding="3" cellspacing="0" class="tab_nd2">
					<tr class="border_2">
						<td width="80.6%" class="first top border_btom size_font">
							&nbsp;';

						$html .= $i;

						$html .=' records listed
						</td>
						<td width="19.4%" class="end top border_btom">
							&nbsp;
						</td>
					</tr>
				</table>
				<div style=" clear:both; color: #c9c9c9;"><br />
			---------------------------------------------------------------------------------------------------------------------------------------------------------------------------------
				</div><br />
				';










			$pdf->writeHTML($html, true, false, true, true, '');
			$pdf->Output(APP. 'webroot'.DS. 'upload'.DS .$filename.'.pdf', 'F');
			echo URL.'/upload/'. $filename .'.pdf';
			die;


		}

	}
    public function dropopt() {
        // neu ton tai session id
        if ($this->Session->check($this->name . 'ViewId') && isset($this->params->params['pass'][0])) {
            $idsession = $this->Session->read($this->name . 'ViewId');
            $arr_tmp = $this->opm->select_one(array('_id' => new MongoId($idsession)));
            $idopt = $this->params->params['pass'][0];
            $idopt = explode('@', $idopt);
            $opname = $idopt[1];
            $idopt = $idopt[0];

            $options = array();
            foreach ($arr_tmp[$opname] as $kop => $arr_op) {//lap va bo bot gia tri
                if (isset($arr_op['id']) && (int) $arr_op['id'] != (int) $idopt) {
                    $options[] = $arr_op;
                }
            }

            $arr_insert[$opname] = $options;
            $arr_insert['_id'] = $idsession;
            if ($this->opm->save($arr_insert))
                echo $this->opm->mongo_id_after_save;

            $this->redirect('/' . $this->params->params['controller'] . '/entry/' . $idsession);
        } else
            $this->redirect('/' . $this->params->params['controller'] . '/entry');

        die;
    }

    public function general() {
        $ids = $this->get_id();
        $subdatas = $this->opm->get_data_general($ids);
        $pricing_method = $subdatas['pricing_method'];
        // if (isset($pricing_method[0]))
        //     $pricing_method[0]['pricing_method_id'] = @$pricing_method[0]['id'];
        // if (isset($pricing_method[0]['rule_id']) && $pricing_method[0]['rule_id'] != '') {
        //     $this->selectModel('Rule');
        //     $ruledata = array();
        //     $ruledata = $this->Rule->select_one(array('_id' => new MongoId($pricing_method[0]['rule_id'])));
        //     $pricing_method[0]['rule_name'] = $ruledata['name'];
        //     $pricing_method[0]['rule_formula'] = $ruledata['map_formula'];
        //     $pricing_method[0]['rule_description'] = $ruledata['description'];
        // }
        $subdatas['pricing_method'] = $pricing_method;
        if (isset($subdatas['supplier']) && count($subdatas['supplier']) > 0)
            foreach ($subdatas['supplier'] as $kk => $vv) {
                if (isset($vv['company_id'])) {
                    $subdatas['supplier'][$kk]['company_name'] = $this->get_name('Company', $vv['company_id']);
                }
            }
        if(is_object($subdatas['same_category'])){
            $arr_temp = $subdatas['same_category'];
            $subdatas['same_category'] = iterator_to_array($arr_temp);
        }

        if ($ids != '' && isset($subdatas['same_category']) && isset($subdatas['same_category'][$ids]))
            unset($subdatas['same_category'][$ids]);


		//stock tracking
		$subdatas['stocktracking'] = array();
		//product options
		$subdatas['productoptions'] = array();
		$stocktracking = $productoptions = $product = $option_select_custom= array();
		$groupstr = '';
		$arr_set = $this->opm->arr_settings;
		$field_list = $arr_set['relationship']['general']['block']['stocktracking']['field'];

		if($ids!=''){
			$query = $this->opm->select_one(array('_id'=>new MongoId($ids)));
			foreach($field_list as $key => $value){
				if(isset($query[$key])){
					$stocktracking[$key] = $query[$key];
				}
			}
            $this->opm->sendToView = true;
			$options_data = $this->opm->options_data($ids,true);
            $this->set('option_select_dynamic', $options_data['option_select_dynamic']);
			if(isset($options_data['productoptions']))
			$subdatas['productoptions'] = $options_data['productoptions'];
			$option_select_custom['option_group'] = $options_data['custom_option_group'];
			$groupstr = $options_data['groupstr'];
		}

        //bo so luong SO completed trong qty_in_stock
        $begin_time = strtotime("01-01-2013 00:00:00"); $f_key =0;
        if(isset($query['stocktakes']) && is_array($query['stocktakes']) && count($query['stocktakes'])>0){
            $arr_stock = $query['stocktakes'];
            $sum = count($query['stocktakes']);
            for($m=$sum-1;$m>=0;$m--){
                if($f_key == 0 && !$query['stocktakes'][$m]['deleted']){
                    $f_key = $m;
                }
            }
            $begin_time = $query['stocktakes'][$f_key]['stocktakes_date']->sec;
        }
        if(isset($stocktracking['qty_in_stock']))
            $stocktracking['qty_in_stock'] = (float)$stocktracking['qty_in_stock'] - $this->check_stock($begin_time,$ids,'so_completed');
        else
            $stocktracking['qty_in_stock'] = - $this->check_stock($begin_time,$ids,'so_completed');

		//set empty
		if(!isset($query['check_stock_stracking']) || ( isset($query['check_stock_stracking']) && $query['check_stock_stracking']==0))
			$subdatas['stocktracking'] = array();
		else
			$subdatas['stocktracking'] = $stocktracking;
        $this->set('subdatas', $subdatas);

		$arr_options_custom = $this->set_select_data_list('relationship', 'general');
		$this->set('arr_options_custom', $arr_options_custom);

		$option_select_custom['group_type'] = array('Inc'=>'Inc','Exc'=>'Exc');
		$this->set('option_select_custom', $option_select_custom);
		$total = 0;
        if(isset($query['products_upload'])&&!empty($query['products_upload']))
        {
            end($query['products_upload']);
            $key = key($query['products_upload']);
            while(!empty($query['products_upload'])&&$query['products_upload'][$key]['deleted']){
                unset($query['products_upload'][$key]);
                if(!empty($query['products_upload'])){
                    end($query['products_upload']);
                    $key = key($query['products_upload']);
                }
            }
            if(isset($query['products_upload'][$key])&&!$query['products_upload'][$key]['deleted']){
                $this->set('img_path',$query['products_upload'][$key]['path']);
                $this->set('doc_id',$query['products_upload'][$key]['doc_id']);
                $this->set('key',$key);
            }
        }
		$this->set('groupstr', $groupstr);
		$this->set('is_choice','1');
    }



	public function product_total_cost($product_id = ''){
		if($product_id!=''){
			$costings_data = $this->costings_data((string)$product_id);
			if(isset($costings_data['pricingsummary']['cost_price']));
				return round((float)$costings_data['pricingsummary']['cost_price'],2);
		}else
			return 0;
	}

	// lay thong tin costing va tong cost
	public function costings_data($ids='') {
		if($ids=='')
			$ids = $this->get_id();

		$subdatas = array();
		$total = 0;
        //$codeauto = 0;
        $subdatas['madeup'] = array();
		if($ids!=''){
			$query = $this->opm->select_one(array('_id'=>new MongoId($ids)));
			if(isset($query['madeup']) && is_array($query['madeup']) && count($query['madeup'])>0){
				foreach($query['madeup'] as $key=>$value){
					if(isset($value['deleted']) && !$value['deleted']){
						//set data co trong produc
						if(isset($value['product_id']) && is_object($value['product_id'])){
							$prodata = $this->opm->select_one(array('_id'=>$value['product_id']));
                            if(isset($prodata['code']))
								$subdatas['madeup'][$key]['code'] = $prodata['code'];
							if(isset($prodata['sku']))
								$subdatas['madeup'][$key]['sku'] = $prodata['sku'];
							if(isset($prodata['name']))
								$subdatas['madeup'][$key]['product_name'] = $prodata['name'];
							if(isset($prodata['product_type']))
								$subdatas['madeup'][$key]['product_type'] = $prodata['product_type'];
							if(isset($prodata['company_name']))
								$subdatas['madeup'][$key]['company_name'] = $prodata['company_name'];
							if(isset($prodata['company_id']))
								$subdatas['madeup'][$key]['company_id'] = $prodata['company_id'];
							if(isset($prodata['oum_depend']))
								$subdatas['madeup'][$key]['oum'] = $prodata['oum_depend'];
                            $cal_price = new cal_price;
                            $cal_price->arr_product_items = array(
                                                                  'sell_by' => ($prodata['sell_by']!='' ? $prodata['sell_by'] : 'unit'),
                                                                  'sizeh'   => (float)$prodata['sizeh'],
                                                                  'sizeh_unit'  =>  ($prodata['sizeh_unit']!='' ? $prodata['sizeh_unit'] : 'in'),
                                                                  'sizew'   => (float)$prodata['sizew'],
                                                                  'sizew_unit'  =>  ($prodata['sizew_unit']!='' ? $prodata['sizew_unit'] : 'in'),
                                                                  'oum'     =>  ($prodata['oum']!='' ? $prodata['oum'] : 'unit'),
                                                                  'quantity'    => ($value['quantity']!='' ? $value['quantity'] : 0),
                                                                  );
                            $cal_price->check_product_items();
                            $cal_price->price_break_from_to = $this->change_sell_price_company('',$prodata['_id']);
                            $arr_result = $cal_price->cal_price_items();

                            $subdatas['madeup'][$key]['unit_price'] = $arr_result['sell_price'];
							if(isset($prodata['unit_price']) && isset($prodata['product_type']) && $prodata['product_type']=='Vendor Stock')
								$subdatas['madeup'][$key]['unit_price'] = $prodata['unit_price'];

							if(isset($prodata['product_type']) && $prodata['product_type']!='Vendor Stock' && $prodata['product_type']!='Service' && isset($prodata['sell_price'])){
								if(isset($prodata['oum']))
									$subdatas['madeup'][$key]['oum'] = $prodata['oum'];

							}


						}
						//set data khong co trong product
						$subdatas['madeup'][$key]['id'] = $key;
						if(isset($value['product_id'])&&$value['product_id']!='')
							$subdatas['madeup'][$key]['product_id'] = $value['product_id'];
						if(isset($value['markup'])&&$value['markup']!='')
							$subdatas['madeup'][$key]['markup'] = (float)$value['markup'];
						if(isset($value['margin'])&&$value['margin']!='')
							$subdatas['madeup'][$key]['margin'] = (float)$value['margin'];
						if(isset($value['quantity'])&&$value['quantity']!='')
							$subdatas['madeup'][$key]['quantity'] = (float)$value['quantity'];
                        // if(isset($value['unit_price'])&&$value['unit_price']!='')
                        //      $subdatas['madeup'][$key]['unit_price'] = (float)$value['unit_price'];
                         if(isset($value['product_name'])&&$value['product_name']!='')
                            $subdatas['madeup'][$key]['product_name'] = $value['product_name'];
                        if(isset($value['product_type'])&&$value['product_type']!='')
                            $subdatas['madeup'][$key]['product_type'] = $value['product_type'];
                        if(isset($value['oum'])&&$value['oum']!='')
                            $subdatas['madeup'][$key]['oum'] = $value['oum'];
                        if(isset($value['view_in_detail']))
                            $subdatas['madeup'][$key]['view_in_detail'] = $value['view_in_detail'];
						//tinh total
						$cal_price = new cal_price();
						$cal_price->arr_product_items = $subdatas['madeup'][$key];
						$cal_price->cal_price_in_markup_margin();
						$subdatas['madeup'][$key]['sub_total'] = $cal_price->arr_product_items['sub_total'];
						$total += $subdatas['madeup'][$key]['sub_total'];
					}
				}
			}

			//pricingsummary
			$pricingsummary = array();
			if($total>0){
				$pricingsummary['cost_price'] 	= $total;
				$pricingsummary['total_cost'] 	= $total;
			}else if(isset($query['cost_price']))
				$pricingsummary['cost_price'] 	= (float)$query['cost_price'];
			if(isset($query['sell_price']))
				$pricingsummary['sell_price'] 	= (float)$query['sell_price'];
			else
				$pricingsummary['sell_price'] 	= 0;

			if(isset($pricingsummary['cost_price']))
				$pricingsummary['profit'] 	= $pricingsummary['sell_price'] - $pricingsummary['cost_price'];

			if(isset($pricingsummary['cost_price']) && $pricingsummary['cost_price']!=0)
				$pricingsummary['markup'] 	= 100*($pricingsummary['profit']/$pricingsummary['cost_price']);
			else
				$pricingsummary['markup'] 	= 0;

			if(isset($pricingsummary['sell_price']) && isset($pricingsummary['profit']) && $pricingsummary['sell_price']!=0)
				$pricingsummary['margin'] 	= 100*($pricingsummary['profit']/$pricingsummary['sell_price']);
			else
				$pricingsummary['margin'] 	= 0;
			$subdatas['pricingsummary'] = $pricingsummary;

		}
		//output
		return $subdatas;

	}


    public function costings($id = '') {
        $this->recal_table_price();
		$total = 0;
        if($id == '')
            $id = $this->get_id();
		$subdatas = $this->opm->get_data_costing($id);
		$costings_data = $this->costings_data($id);
		$subdatas = array_merge((array)$subdatas,(array)$costings_data);
        if(isset($subdatas['pricingsummary']['cost_price'])){
            $this->opm->save(array('_id' => new MongoId($id),'cost_price'=>$subdatas['pricingsummary']['cost_price']));
            if(IS_LOCAL)
                $this->recal_table_price($id,$subdatas['pricingsummary']['cost_price']);
        }
		if(isset($costings_data['pricingsummary']['total_cost']))
			$total = $costings_data['pricingsummary']['total_cost'];
        $this->set('total', $total);
        $this->set('subdatas', $subdatas);
		//$codeauto = $this->opm->get_auto_code('code');
        //$this->set('nextcode', $codeauto);
		//bật js chọn popup
		 $this->set('is_choice','1');
		 $this->set_select_data_list('relationship','costings');
    }



    public function pricing() {
        $subdatas = array();
        $query = $this->opm->select_one(array('_id'=>new MongoId($this->get_id())));
        $module_id = $this->get_id();
        $subdatas = $this->opm->get_data_pricing($module_id);
		$costings_data = $this->costings_data();
		$subdatas['pricingsummary'] = $costings_data['pricingsummary'];
        $hook = new hook(array('hook_list'=>true));
        $hook_list_tmp = $hook->hook_list;
        $pricing_method_list= array();
        foreach($hook_list_tmp as $value){
            $pricing_method_list[$value] = ucfirst(str_replace('_',' ',$value));
        }
		$subdatas['otherpricing']['pricing_method_list'] =  $pricing_method_list;
        $subdatas['otherpricing']['pricing_method'] = (isset($query['pricing_method']) ? (isset($pricing_method_list[$query['pricing_method']]) ? $pricing_method_list[$query['pricing_method']] : '') : '');
        //arr_price_breaks_type
        $this->selectModel('Stuffs');
        $sale_price_list = $this->Stuffs->select_all(array(
            'arr_where' => array(
                    'type' => 'price_break'
                ),
            'arr_field' => array('option')
        ));
        foreach ($sale_price_list as $key => $value) {
            $subdatas['otherpricing']['arr_price_breaks_type'][$key] = $value['option']['name']['value'];
        }
        if(isset($query['price_breaks_type']))
            $subdatas['otherpricing']['price_breaks_type'] = $query['price_breaks_type'];
        else
            $subdatas['otherpricing']['price_breaks_type'] = '';
        //end arr_price_breaks_type
        //bleed
        $bleeds = $this->Stuffs->select_one(array('value' => 'bleed_type'), array('option'));
        $subdatas['otherpricing']['pricing_bleed_list'] = array();
        if( isset($bleeds['option']) ){
            foreach($bleeds['option'] as $bleed) {
                $subdatas['otherpricing']['pricing_bleed_list'][$bleed['key']] = "{$bleed['name']} : {$bleed['sizew']}{$bleed['sizew_unit']} x {$bleed['sizeh']}{$bleed['sizeh_unit']}";
            }
        }
        $subdatas['otherpricing']['pricing_bleed'] = (isset($query['pricing_bleed']) ? $query['pricing_bleed']  : '');
        //end bleed
        $this->set('subdatas', $subdatas);
        $select_list = $this->set_select_data_list('relationship', 'pricing');
        if (isset($subdatas['sell_by']))
            $select_list['range_unit'] = $this->Setting->select_option_vl(array('setting_value' => 'product_oum_' . $subdatas['sell_by']));
        $this->set('option_select', $select_list);
        $this->set('sell_sum', count($subdatas['sellprices']));
        if (isset($subdatas['otherpricing']['update_price_by_id'])){
            $user_id = $subdatas['otherpricing']['update_price_by_id'];
            $user_name = $this->get_name('Contact', $user_id);
        } else {
            $user_id = $user_name = '';

        }
        $this->set('user_name', $user_name);
        $this->set('user_id', $user_id);
    }



	// Stock
	public function stock() {
		$ids = $this->get_id();
		if($ids!=''){
			$query = $this->opm->select_one(array('_id'=> new MongoId($ids)));
		}

        //stock takes
        $subdatas['stocktakes']     = array();
        $total = 0;$f_key = 0; $begin_time = strtotime('01-01-2013 00:00:00');
        //them phan SO cho qty_in_stock

        if(isset($query['stocktakes']) && is_array($query['stocktakes']) && count($query['stocktakes'])>0){
            $stocktakes = array();
            $arr_stock = $query['stocktakes'];
            $sum = count($arr_stock);
            for($m=$sum-1;$m>=0;$m--){
                if($f_key == 0 && !$arr_stock[$m]['deleted']){
                    $f_key = $m;
                }
                $stocktakes[$m] = $arr_stock[$m];
                if(isset($arr_stock[$m]['qty_amended']) && !$arr_stock[$m]['deleted'])
                    $total += (float)$arr_stock[$m]['qty_amended'];
            }
            $begin_time = $stocktakes[$f_key]['stocktakes_date']->sec;
            $subdatas['stocktakes'] = $stocktakes;
        }
        $qty_so_completed = $this->check_stock($begin_time,$ids,'so_completed');

		$subdatas['stock_summary'] 	= array();
		if(isset($query['qty_in_stock']))
			$subdatas['stock_summary']['in_stock_total'] = (float)$query['qty_in_stock']-$qty_so_completed;
		else
			$subdatas['stock_summary']['in_stock_total'] = 0;

		$subdatas['stock_summary']['in_stock_av'] = $subdatas['stock_summary']['in_stock_total'];
		$subdatas['stock_summary']['loan_total'] = '';
		$subdatas['stock_summary']['loan_av'] = $subdatas['stock_summary']['loan_total'];
		$subdatas['stock_summary']['internal_assets'] = '';
		$subdatas['stock_summary']['total'] = (float)$subdatas['stock_summary']['in_stock_total'] + (float)$subdatas['stock_summary']['loan_total'] + (float)$subdatas['stock_summary']['internal_assets'];

		$subdatas['stock_summary']['purchases_total'] = 0;
		$subdatas['stock_summary']['purchases_current'] = 0;
		$subdatas['stock_summary']['used_on_jobs'] = '';
		$subdatas['stock_summary']['used_on_stages'] = '';
		$subdatas['stock_summary']['used_on_tasks'] = '';
		$subdatas['stock_summary']['used_on_timelogs'] = '';
		$subdatas['stock_summary']['staff_expenses'] = '';
		$subdatas['stock_summary']['assembly_add_total'] = '';
		$subdatas['stock_summary']['assembly_add_current'] = '';
		$subdatas['stock_summary']['assembly_use_total'] = '';
		$subdatas['stock_summary']['assembly_use_current'] = '';
		$subdatas['stock_summary']['sales_total'] = '';
		$subdatas['stock_summary']['sales_current'] = '';
		$subdatas['stock_summary']['resource_total'] = '';
		$subdatas['stock_summary']['resource_current'] = '';
		$subdatas['stock_summary']['min_stock'] = '';
		$subdatas['stock_summary']['low'] = 'Low';

		//locations
		$subdatas['locations'] 	= array();
		$location_total = array();
		$location_total['onpo'] = $this->check_stock($begin_time,$ids,'po');
		$location_total['minstock'] = '0';
		$location_total['avalible'] = '0';
		$location_total['assembly'] = '0';
		$location_total['inuse'] = '0';
		$location_total['onso'] = $this->check_stock($begin_time,$ids,'so');

		$location_total['total'] = '0';
		if(isset($query['locations']) && is_array($query['locations']) && count($query['locations'])>0){
			$locations = array();
			foreach($query['locations'] as $keys=>$value){
                if(isset($value['deleted']) && $value['deleted'])
                    continue;

				$locations[$keys] = $value;
				//set default and avalible
				$locations[$keys]['total_stock'] = isset($locations[$keys]['total_stock'])?$locations[$keys]['total_stock']-$qty_so_completed:0;
				$locations[$keys]['in_use'] = isset($locations[$keys]['in_use'])&&$locations[$keys]['in_use']!=''?$locations[$keys]['in_use']:0;
				$locations[$keys]['on_so'] = $location_total['onso'];
				$locations[$keys]['in_assembly'] = isset($locations[$keys]['in_assembly'])&&$locations[$keys]['in_assembly']!=''?$locations[$keys]['in_assembly']:0;

				$locations[$keys]['avalible'] = $locations[$keys]['total_stock']-$locations[$keys]['in_use']-$locations[$keys]['on_so']-$locations[$keys]['in_assembly'];
                $locations[$keys]['on_po'] = $location_total['onpo'];
				//total
				if(isset($locations[$keys]['min_stock']))
					$location_total['minstock'] += (int)$locations[$keys]['min_stock'];
				if(isset($locations[$keys]['in_assembly']))
					$location_total['assembly'] += (int)$locations[$keys]['in_assembly'];
				if(isset($locations[$keys]['in_use']))
					$location_total['inuse'] += (int)$locations[$keys]['in_use'];
				if(isset($locations[$keys]['total_stock']))
					$location_total['total'] += (int)$locations[$keys]['total_stock'];

				$location_total['avalible'] = $location_total['avalible'] + ( $location_total['total'] - $location_total['inuse'] - $location_total['onso'] - $location_total['assembly']);

				if(isset($locations[$keys]['total_stock']) && isset($locations[$keys]['min_stock']) && $locations[$keys]['total_stock'] < $locations[$keys]['min_stock'])
					$locations[$keys]['low'] = 1;
				else
					$locations[$keys]['low'] = 0;
			}
			$subdatas['locations'] = $locations;
		}
		$this->set('location_total', $location_total);

		$this->set('total', $total);
		$this->set('first_amended', $f_key);
        $this->set('subdatas', $subdatas);
		$this->set_select_data_list('relationship', 'stock');
    }

    public function orders() {
        $datareturn = $cat_select = array();
        $total = 0; $total_completed = 0; $total_not_completed = $total_cancel = 0;
        $module_id = $this->get_id();
        if (isset($module_id) && strlen($module_id) == 24) {
            $prokey = array('quantity');
            $this->selectModel('Salesorder');
            $orders = $this->Salesorder->select_all(array(
                'arr_where' => array(
                    'deleted' => false,
                    'products' => array(
                                    '$elemMatch' => array(
                                                        'deleted' => false,
                                                        'products_id' => new MongoId($module_id),
                                                        )
                                    )
                ),
                'arr_field' => array('code', 'salesorder_date', 'company_name', 'company_id', 'contact_name', 'contact_id', 'our_rep', 'our_rep_id', 'job_number', 'job_name', 'job_id', 'status', 'products.$'),
                'arr_order' => array('salesorder_date' => -1)
            ));
            $thisOrder = $this->opm->select_one(array('_id' => new MongoId($module_id)),array('salesorders'));
            if( !isset($thisOrder['salesorders']) ) {
                $thisOrder = array();
            } else {
                $thisOrder = $thisOrder['salesorders'];
            }
            foreach ($orders as $order) {
                if( isset($thisOrder[(string)$order['_id']]) ) continue;
                $order['quantity'] = 0;
                foreach($order['products'] as $product){
                    if(isset($product['products_id']) && (string)$product['products_id']==$module_id){
                        $order['quantity'] += $product['quantity'];

                    }
                }
                if($order['status']=== 'Completed'){
                    $total_completed+=$order['quantity'];
                }
                else if($order['status']=== 'Cancelled'){
                    $total_cancel+=$order['quantity'];
                }
                else{
                    $total_not_completed+=$order['quantity'];
                }
                $total +=$order['quantity'];
                $datareturn[] = $order;
            }

            $this->selectModel('Setting');
            $cat_select['status'] = $this->Setting->select_option(array('setting_value' => 'salesorders_status'));
        }

        $this->set('total', $total);
        $subdatas['salesorders'] = $datareturn;
        $this->set('subdatas', $subdatas);
        $this->set('option_select', $cat_select);
        $this->set('total_completed', $total_completed);
        $this->set('total_not_completed', $total_not_completed);
        $this->set('total_cancel', $total_cancel);
    }

    public function order_report()
    {
        if( isset($_GET['print_pdf']) ) {
            $arr_data = Cache::read('order_report');
            Cache::delete('order_report');
        } else {
            $product_id = new MongoId($this->get_id());
            $product = $this->opm->select_one(array('_id' => $product_id), array('code', 'name'));
            $this->selectModel('Salesorder');
            $orders = $this->Salesorder->select_all(array(
                'arr_where' => array(
                    'products' => array(
                        '$elemMatch' => array(
                            'products_id' => $product_id,
                            'deleted' => false
                        )
                    )
                ),
                'arr_field' => array('code', 'salesorder_date', 'company_name','contact_name', 'our_rep', 'job_number', 'job_name', 'status', 'products.$'),
                'arr_order' => array('salesorder_date' => -1)
            ));
            $arr_data = array();
            $html = '';
            $i = $total = $total_canceled = $total_completed = $total_other = 0;
            $arr_excel = [];
            foreach ($orders as $order) {
                $order['quantity'] = 0;
                foreach($order['products'] as $p) {
                    $order['quantity'] += $p['quantity'];
                }
                unset($order['products']);
                if($order['status']=== 'Completed'){
                    $total_completed += $order['quantity'];
                } else if($order['status']=== 'Cancelled'){
                    $total_canceled += $order['quantity'];
                } else{
                    $total_other += $order['quantity'];
                }
                $total += $order['quantity'];
                $order['company_name'] = (isset($order['company_name']) ? $order['company_name'] : '');
                $order['contact_name'] = (isset($order['contact_name']) ? $order['contact_name'] : '');
                $order['our_rep'] = (isset($order['our_rep']) ? $order['our_rep'] : '');
                $order['job_number'] = (isset($order['job_number']) ? $order['job_number'] : '');
                $order['job_name'] = (isset($order['job_name']) ? $order['job_name'] : '');
                $arr_excel['orders'][] = $order;
                $html .= '
                        <tr style="background-color:' . ( $i%2==0 ? '#eeeeee' : '#fdfcfa'). ';">
                            <td>'. $order['code'] .'</td>
                            <td class="center_text">'. $this->opm->format_date($order['salesorder_date']->sec) .'</td>
                            <td>'. $order['company_name'] .'</td>
                            <td>'. $order['contact_name'] .'</td>
                            <td>'. $order['our_rep'] .'</td>
                            <td>'. $order['job_number'] .'</td>
                            <td>'. $order['job_name'] .'</td>
                            <td>'. $order['status'] .'</td>
                            <td class="right_text">'. $order['quantity'] .'</td>
                        </tr>
                ';
                $i++;
            }
            $html .= '
                        <tr style="background-color:' . ( $i%2==0 ? '#eeeeee' : '#fdfcfa'). ';">
                            <td class="right_none"></td>
                            <td class="bold_text right_text">Totals Canceled</td>
                            <td class="bold_text right_text">'. number_format($total_canceled) .'</td>
                            <td class="bold_text right_text">Totals On SO\'s</td>
                            <td class="bold_text right_text">'. number_format($total_other) .'</td>
                            <td class="bold_text right_text">Totals Completed</td>
                            <td class="bold_text right_text">'. number_format($total_completed) .'</td>
                            <td class="bold_text right_text">Totals</td>
                            <td class="bold_text right_text">'. number_format($total) .'</td>
                        </tr>
                ';
            $arr_data['content'] = $html;
            $arr_data['report_heading'] = $product['code'].' - ' .$product['name'];
            $arr_excel['name'] = $arr_data['report_heading'];
            $arr_data['title'] = array('SO #' => 'text-align: left; width: 7%','Order Date'=>'width: 12%;','Company'=> 'text-align: left;','Contact'=> 'text-align: left;','Our Rep'=> 'text-align: left;','Job #'=> 'text-align: left;','Job Name'=> 'text-align: left;','Status'=> 'text-align: left;','Quantity'=> 'text-align: right;');
            $arr_data['report_name'] = 'Product Orders';
            $arr_data['report_file_name'] = 'ProductOrders';
            $arr_data['excel_url'] = URL.'/products/order_excel';
            Cache::write('order_report', $arr_data);
            Cache::write('order_excel', $arr_excel);
        }
        $this->render_pdf($arr_data);
    }

    public function order_excel()
    {
        $arr_order = Cache::read('order_excel');
        Cache::delete('order_excel');
        if(!$arr_order){
            echo 'No data';die;
        }
        App::import('Vendor', 'phpexcel/PHPExcel');
        $objPHPExcel = new PHPExcel();
        // Set document properties
        $objPHPExcel->getProperties()->setCreator('')
                                     ->setLastModifiedBy('')
                                     ->setTitle($arr_order['name'])
                                     ->setSubject($arr_order['name'])
                                     ->setDescription($arr_order['name'])
                                     ->setKeywords($arr_order['name'])
                                     ->setCategory($arr_order['name']);
        $worksheet = $objPHPExcel->getActiveSheet();
        $worksheet->setCellValue('A1', 'SO #')
                    ->setCellValue('B1', 'Order Date')
                    ->setCellValue('C1', 'Company')
                    ->setCellValue('D1', 'Contact')
                    ->setCellValue('E1', 'Our Rep')
                    ->setCellValue('F1', 'Job #')
                    ->setCellValue('G1', 'Job Name')
                    ->setCellValue('H1', 'Status')
                    ->setCellValue('I1', 'Quantity');
        $i = 2;
        foreach($arr_order['orders'] as $order){
            $worksheet->setCellValue('A'.$i, $order['code'])
                        ->setCellValue('B'.$i, $this->opm->format_date($order['salesorder_date']->sec))
                        ->setCellValue('C'.$i, $order['company_name'])
                        ->setCellValue('D'.$i, $order['contact_name'])
                        ->setCellValue('E'.$i, $order['our_rep'])
                        ->setCellValue('F'.$i, $order['job_number'])
                        ->setCellValue('G'.$i, $order['job_name'])
                        ->setCellValue('H'.$i, $order['status'])
                        ->setCellValue('I'.$i, $order['quantity']);
            $i++;
        }
        $worksheet->getStyle("B1:B".$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        $worksheet->getRowDimension(8)->setRowHeight(-1);
        $worksheet->getStyle('A1:I1')->getFont()->setBold(true);
        $worksheet->setCellValue('B'.$i, 'Totals Canceled');
        $worksheet->setCellValue('C'.$i, '=SUMIF(H2:H'.($i-1).', "Cancelled", I2:I'.($i-1).')');
        $worksheet->setCellValue('D'.$i, 'Totals On SO\'s');
        $worksheet->setCellValue('E'.$i, '=I'.$i.' - (C'.$i.' + G'.$i.')');
        $worksheet->setCellValue('F'.$i, 'Totals Completed');
        $worksheet->setCellValue('G'.$i, '=SUMIF(H2:H'.($i-1).', "Completed", I2:I'.($i-1).')');
        $worksheet->setCellValue('H'.$i, 'Totals');
        $worksheet->setCellValue('I'.$i, '=SUM(I2:I'.($i-1).')');
        $worksheet->getStyle('A'.$i.':I'.$i)->getFont()->setBold(true);
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
        $worksheet->getStyle('A1:I'.$i)->applyFromArray($styleArray);
        for($i = 'A'; $i !== 'J'; $i++){
            $worksheet->getColumnDimension($i)
                            ->setAutoSize(true);
        }
        $objPHPExcel->setActiveSheetIndex(0);
        $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
        $name = Inflector::slug($arr_order['name'], '-');
        $objWriter->save(APP.DS.'webroot'.DS.'upload'.DS.$name.'.xlsx');
        $this->redirect('/upload/'.$name.'.xlsx');
        die;
    }

	public function shipping() {
        $module_id = $this->get_id();
        if($module_id!='') {
			$ship = array();
			$this->selectModel('Shipping');
            $shippings = $this->Shipping->select_all(array(
                'arr_where' => array(
                    'products' => array(
                                    '$elemMatch' => array(
                                                        'deleted' => false,
                                                        'products_id' => new MongoId($module_id),
                                                        )
                                    )
                ),
                'arr_field' => array('code', 'shipping_type', 'return_status', 'shipping_date', 'company_name', 'company_id', 'contact_name', 'contact_id', 'our_rep', 'our_rep_id', 'job_number', 'job_name', 'job_id', 'shipping_status', 'products.$'),
                'arr_order' => array('salesorder_date' => -1)
            ));
            $thisShipping = $this->opm->select_one(array('_id' => new MongoId($module_id)),array('shippings'));
            if( !isset($thisShipping['shippings']) ) {
                $thisShipping = array();
            } else {
                $thisShipping = $thisShipping['shippings'];
            }
			$total_all = array();
			$total_all['quantity_in'] = $total_all['quantity_out'] = 0;

			foreach ($shippings as $shipping) {
                if( isset($thisShipping[(string)$shipping['_id']]) ) continue;
                $shipping['quantity_in'] = $shipping['quantity_out'] = '';
                $qty = 0;
                foreach($shipping['products'] as $product) {
                    $qty += $product['quantity'];
                }
                if( $shipping['return_status'] == 'Out' ) {
                    $shipping['quantity_out'] = $qty;
                } else {
                    $shipping['quantity_in'] = $qty;
                }
				$ship[] = $shipping;

				$total_all['quantity_in']  += (int)$shipping['quantity_in'];
				$total_all['quantity_out'] += (int)$shipping['quantity_out'];
            }
			$subdatas['ship'] = $ship;
			$this->set('subdatas', $subdatas);
			$this->set('total_all', $total_all);
        }
    }



    public function invoices() {
        $datareturn = $cat_select = array();
        $total = 0;
        $module_id = $this->get_id();
        if (isset($module_id) && strlen($module_id) == 24) {
            $this->selectModel('Salesinvoice');
            $invoices = $this->Salesinvoice->select_all(array(
                'arr_where' => array(
                    'products' => array(
                                    '$elemMatch' => array(
                                                        'deleted' => false,
                                                        'products_id' => new MongoId($module_id),
                                                        )
                                    )
                ),
                'arr_field' => array('code', 'invoice_type', 'invoice_date', 'company_name', 'company_id', 'contact_name', 'contact_id', 'our_rep', 'our_rep_id', 'job_number', 'job_name', 'job_id', 'invoice_status', 'products.$'),
                'arr_order' => array('shipping_date' => -1)
            ));
            $thisInvoice = $this->opm->select_one(array('_id' => new MongoId($module_id)),array('salesinvoices'));
            if( !isset($thisInvoice['salesinvoices']) ) {
                $thisInvoice = array();
            } else {
                $thisInvoice = $thisInvoice['salesinvoices'];
            }
            foreach ($invoices as $invoice) {
                if( isset($thisInvoice[(string)$invoice['_id']]) ) continue;
                $invoice['quantity'] = 0;
                foreach($invoice['products'] as $product){
                    $invoice['quantity'] += $product['quantity'];
                }
                $datareturn[] = $invoice;
            }
            $this->selectModel('Setting');
            $cat_select['invoice_status'] = $this->Setting->select_option_vl(array('setting_value' => 'salesinvoices_status'));
        }//end if

        $subdatas['salesinvoices'] = $datareturn;

		$subdatas['vendorinvoice'] = array();

        $this->set('subdatas', $subdatas);
        $this->set('total', $total);
        $this->set('option_select', $cat_select);
    }


    public function select_company_name($ids) {
        if (isset($_POST['ids']))
            $ids = $_POST['ids'];
        if ($ids != '')
            echo $this->get_name('Company', $ids);
        die;
    }

    public function select_render($sell_by = '') {
        if (isset($_POST['sell_by']))
            $sell_by = $_POST['sell_by'];
        $this->selectModel('Setting');
        echo json_encode($this->Setting->select_option_vl(array('setting_value' => 'product_oum_' . $sell_by)));
        die;
    }




    public function purchasing_report() {
        if( isset($_GET['print_pdf']) ) {
            $arr_data = Cache::read('purchasing_report');
            Cache::delete('purchasing_report');
        } else {
            $product_id = new MongoId($this->get_id());
            $product = $this->opm->select_one(array('_id' => $product_id), array('code', 'name'));
            $this->selectModel('Purchaseorder');
            $orders = $this->Purchaseorder->select_all(array(
                'arr_where' => array(
                    'products' => array(
                        '$elemMatch' => array(
                            'products_id' => $product_id,
                            'deleted' => false
                        )
                    )
                ),
                'arr_field' => array('products.$', 'code', 'purchord_date', 'company_id', 'company_name', 'purchase_orders_status'),
                'arr_order' => array('purchord_date' => -1)
            ));
            $arr_data = array();
            $html = '';
            $i = $total = 0;
            foreach ($orders as $order) {
                $order['quantity'] = 0;
                foreach($order['products'] as $p) {
                    $order['quantity'] += $p['quantity'];
                }
                $total += $order['quantity'];
                $html .= '
                        <tr style="background-color:' . ( $i%2==0 ? '#eeeeee' : '#fdfcfa'). ';">
                            <td>'. $order['code'] .'</td>
                            <td class="center_text">'. $this->opm->format_date($order['purchord_date']->sec) .'</td>
                            <td>'. $order['company_name'] .'</td>
                            <td>'. $order['purchase_orders_status'] .'</td>
                            <td class="right_text">'. $order['quantity'] .'</td>
                        </tr>
                ';
                $i++;
            }
            $html .= '
                        <tr style="background-color:' . ( $i%2==0 ? '#eeeeee' : '#fdfcfa'). ';">
                            <td class="right_none" colspan="3"></td>
                            <td class="bold_text right_text">Total</td>
                            <td class="bold_text right_text">'. $total .'</td>
                        </tr>
                ';
            $arr_data['content'] = $html;
            $arr_data['report_heading'] = $product['code'].' - ' .$product['name'];
            $arr_data['title'] = array('PO #' => 'text-align: left;','PO Date'=>'width: 20%;','Supplier'=> 'text-align: left;','Status'=> 'text-align: left;','Quantity'=> 'text-align: right;');
            $arr_data['report_name'] = 'Product Purchasing';
            $arr_data['report_file_name'] = 'ProductPurchasing';
            Cache::write('purchasing_report', $arr_data);
        }
        $this->render_pdf($arr_data);
    }


    public function purchasing() {

		$arr_set = $this->opm->arr_settings;

		/*
		* Purchase orders for this item
		*/
        $po_of_item = $query = array();
        $fieldlist = $arr_set['relationship']['purchasing']['block']['po_of_item']['field'];
        $this->selectModel('Purchaseorder');
        $product_id = $this->get_id();
        $query = $this->opm->select_one(array('_id' => new MongoId($product_id)), array('company_id'));
        if( !isset($query['company_id']) ) {
            $query['company_id'] = '';
        }
	    $this->set('product_id',$product_id);
        $query_puchase = $this->Purchaseorder->select_all(array(
            'arr_where' => array(
                'products' => array(
                    '$elemMatch' => array(
                        'products_id' => new MongoId($product_id),
                        'deleted' => FALSE
                    )
                )
            ),
            'arr_order' => array('purchord_date' => -1)
        ));

		$query_puchase = iterator_to_array($query_puchase);
        $m = 0; $sum = 0;
        $data_puchase = array();
        foreach ($query_puchase as $k => $v) {
            if (isset($v['products'])) {
                $prolist = (array) $v['products'];
                foreach ($prolist as $key => $value) {
                    if (!$value['deleted'] && $value['products_id'] == $product_id) {
                        $data_puchase[$m] = array();
                        if (isset($v['code']))
                            $data_puchase[$m]['code'] = $v['code'];
                        if (isset($v['purchord_date']))
                            $data_puchase[$m]['purchord_date'] = $v['purchord_date'];
                        if (isset($v['company_id']))
                            $data_puchase[$m]['company_id'] = $v['company_id'];
                        else
                            $data_puchase[$m]['company_id'] = '';
                        if (isset($v['company_name']))
                            $data_puchase[$m]['company_name'] = $v['company_name'];
                        if (isset($v['purchase_orders_status']))
                            $data_puchase[$m]['purchase_orders_status'] = $v['purchase_orders_status'];
                        if (isset($value['quantity'])) {
                            $data_puchase[$m]['quantity'] = $value['quantity'];
	                        $sum += $value['quantity'];
                        }
                        $data_puchase[$m]['id'] = $m;
                        $data_puchase[$m]['_id'] = $v['_id'];
                        $data_puchase[$m] = $this->arr_list_changed($data_puchase[$m]);
                        $m++;
                    }
                }
            }
        }
        $data = array();
        $company_id = (string)$query['company_id'];
        foreach( $data_puchase as $key => $order ) {
            if( $company_id == (string)$order['company_id'] ) {
                $data[$key] = $order;
                unset($data_puchase[$key]);
            }
        }

        $data_puchase = array_merge($data, $data_puchase);

        $sub_tab['po_of_item'] = $data_puchase;

		//tim products_useon
		$sub_tab['products_useon'] = array();
		$query = $this->opm->select_all( array(
			'arr_where' => array(
				'supplier' => array(
					'$elemMatch' =>array(
						'deleted' => false,
						'product_id'=> new MongoId($product_id)
					)
				)
			),
			'arr_field' => array('code','sku','name','product_type','product_category'),
		));

		$sub_tab['products_useon'] = $query;


        $this->set('total', $sum);
        // end po_of_item




		/*
		* Purchase orders for this item
		*/
        $query_product = $this->opm->select_one(array('_id' => new MongoId($product_id)));

		$arr_sup1=array();
	    $arr_sup2=array();
	    $v_check_unit='';
	    $v_total_average=(float)0;
	    $v_average_plus=(float)0;
		$v_count_supplier=0;
		$sub_tab['po_supplier'] = array();

		if(isset($query_product['oum']))
			$v_check_unit = $query_product['oum'];

	    if(isset($query_product['supplier']) && is_array($query_product['supplier']) && count($query_product['supplier'])>0){
			/*
			* Đem dòng curent default lên trên cùng
			*/
			foreach($query_product['supplier'] as $keys=>$values){
				if(isset($values['deleted']) && !$values['deleted']){

					$values['_id'] = $keys;
					//set link cho product
					if(isset($values['product_id'])){
						$values['product_link'] = '<a href="'.URL.'/products/entry/'.(string)$values['product_id'].'"><span class="icon_linkleft" title="Link to this Vendor stock" onclick=""></span></a>';
						$values['name'] = '<a style="text-decoration:none;" href="'.URL.'/products/entry/'.(string)$values['product_id'].'">'.$values['name'].'</a>';
					}

					//neu la current supplier
					if(isset($values['company_name']) && $values['current']=='on'){
						//$values['remove_deleted'] = '1';//bật cờ ẩn icon deleted
						$arr_sup1[]=$values;//array current

					//neu la # current
					}else
						$arr_sup2[]=$values;

					//tinh trung binh unit_price
					if(isset($values['unit_price'])){
						$v_total_average += (float)$values['unit_price'];

					}

					$v_count_supplier++;

				}//end if delete
		    }

			$sub_tab['po_supplier'] = array_merge((array)$arr_sup1, (array)$arr_sup2);
			if($v_count_supplier!=0)
				$v_average_plus = $v_total_average/$v_count_supplier;

			/* Tính trung bình unit cost trên mỗi UOM
			*/
		    /*if($flag_cost_per_unit!=null || strtolower($query_product['sell_by'])=='area'){
			    $v_check_unit='area';
			    for($i=0;$i<count($query_product['supplier']);$i++){
				    if($query_product['supplier'][$i]['deleted']==false && $query_product['supplier'][$i]['sell_price']!=0){
					    $v_count_supplier+=1;
					    $arr_pro=array();
					    $quotes_cal = new cal_price();
					    $arr_pro['sizew']=$query_product['supplier'][$i]['sizew'];
					    $arr_pro['sizew_unit']=$query_product['supplier'][$i]['sizew_unit'];
					    $arr_pro['sizeh']=$query_product['supplier'][$i]['sizeh'];
					    $arr_pro['sizeh_unit']=$query_product['supplier'][$i]['sizeh_unit'];

					    $quotes_cal->arr_product_items = $arr_pro;
					    $arr_pro_return=array();


					    $quotes_cal->cal_area();
					    $arr_pro_return = $quotes_cal->arr_product_items;
					    $v_area=$arr_pro_return['area'];
					    $v_average=$query_product['supplier'][$i]['sell_price']/$v_area;
						if(isset($arr_sup1[$i])){
							$arr_sup1[$i]['unit_price'] = $v_average;
							$arr_sup1[$i]['oum_depend'] = 'Sq.ft.';

						}
						if(isset($arr_sup2[$i])){
							$arr_sup2[$i]['unit_price'] = $v_average;
							$arr_sup2[$i]['oum_depend'] = 'Sq.ft.';
						}
					    $v_total_average+=$v_average;
				    }
			    }

				if($v_count_supplier!=0)
			        $v_average_plus=(float)$v_total_average/(float)$v_count_supplier;


			}else{
			    $v_check_unit='unit';
			    foreach($query_product['supplier'] as $keys=>$values){
				    if(isset($values['deleted']) && isset($values['sell_price']) && $values['deleted']==false && $values['sell_price']!=0){
					    $v_count_supplier += 1;
					    $v_total_average += $query_product['supplier'][$i]['sell_price'];
				    }
			    }
			    if($v_count_supplier!=0)
				    $v_average_plus=(float)$v_total_average/(float)$v_count_supplier;
		    }*/

	    }


		//Locations
		$sub_tab['locations'] 	= array();
		$location_total = array();
		$location_total['onpo'] = '0';
		$location_total['minstock'] = '0';
		$location_total['avalible'] = '0';
		$location_total['assembly'] = '0';
		$location_total['inuse'] = '0';
		$location_total['onso'] = '0';
		$location_total['total'] = '0';
		if(isset($query_product['locations']) && is_array($query_product['locations']) && count($query_product['locations'])>0){
			$locations = array();
			foreach($query_product['locations'] as $keys=>$value){
				$locations[$keys] = $value;
				if(isset($locations[$keys]['on_po']))
					$location_total['onpo'] += (int)$locations[$keys]['on_po'];
				if(isset($locations[$keys]['min_stock']))
					$location_total['minstock'] += (int)$locations[$keys]['min_stock'];
				if(isset($locations[$keys]['avalible']))
					$location_total['avalible'] += (int)$locations[$keys]['avalible'];
				if(isset($locations[$keys]['in_assembly']))
					$location_total['assembly'] += (int)$locations[$keys]['in_assembly'];
				if(isset($locations[$keys]['in_use']))
					$location_total['inuse'] += (int)$locations[$keys]['in_use'];
				if(isset($locations[$keys]['on_so']))
					$location_total['onso'] += (int)$locations[$keys]['on_so'];
				if(isset($locations[$keys]['total_stock']))
					$location_total['total'] += (int)$locations[$keys]['total_stock'];
				if(isset($locations[$keys]['total_stock']) && isset($locations[$keys]['min_stock']) && $locations[$keys]['total_stock'] < $locations[$keys]['min_stock'])
					$locations[$keys]['low'] = 1;
				else
					$locations[$keys]['low'] = 0;
			}
			$sub_tab['locations'] = $locations;
		}

		$this->set('location_total', $location_total);

	    $this->set('cost_per_unit', $v_check_unit);
	    $this->set('v_average_plus',$v_average_plus);
        $this->set('subdatas', $sub_tab);

		$this->set_select_data_list('relationship', 'purchasing');

		$listdata1 = $this->Setting->select_option_vl(array('setting_value'=>'product_oum_area'));
		$listdata2 = $this->Setting->select_option_vl(array('setting_value'=>'product_oum_unit'));

		$option_select_custom['oum'] = array_merge((array)$listdata1,(array)$listdata2);
		$this->set('option_select_custom', $option_select_custom);

    }





	public function other() {
		$sub_tab = array();
		$sub_tab['otherdetails'] = $this->get_option_data('otherdetails');
		$sub_tab['noteactive'] = $this->get_option_data('noteactive');
		// $sub_tab['production_step'] = $this->get_option_data('production_step');
        $sub_tab['production_step'] = $this->opm->get_product_asset($this->get_id());
		$this->set('subdatas', $sub_tab);
		$this->set('your_user_name', $this->opm->user_name());
		$this->set('your_user_id', $this->opm->user_id());
		$this->set_select_data_list('relationship', 'other');
		$option_select_custom['oum'] = array_merge(
		     $this->Setting->select_option_vl(array('setting_value'=>'product_oum_area'))
		     ,$this->Setting->select_option_vl(array('setting_value'=>'product_oum_lengths'))
		     ,$this->Setting->select_option_vl(array('setting_value'=>'product_oum_unit'))
		);
		$this->selectModel('Equipment');
		$option_select_custom['tag_key'] = $this->Equipment->select_combobox_asset();
		$this->set('option_select_custom', $option_select_custom);
    }




	public function get_product_by_po() {
        $this->selectModel('Purchaseorder');
        $po = $this->Purchaseorder->select_all(array(
            'arr_field' => array('products.products_id'),
        ));
        // get all product with po
        $product = array();
        foreach ($po as $value) {
            foreach ($value['products'] as $vv) {
                if( !isset($vv['deleted']) || !$vv['deleted'] )
                $product[] = $vv['products_id'];
            }
        }
        $product =  array_unique($product);
        $arr_where['_id'] = array('values' => $product, 'operator' => 'in');
        $this->Session->write($this->name . '_where', $arr_where);
        $this->redirect('lists');
    }




	public function dem($madeup_id=''){
        $sum = 0;
        $this->selectModel('Product');
        $data = $this->Product->select_all(array(
            'arr_where' => array('deleted' => false)
            ));
        $md = array();
        foreach($data as $key => $value){
            //pr($value['madeup']);
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

    public function units_serials() {
        $tmp = array();
        $total = 0;
        $subdatas = array();
        $module_id = $this->get_id();
        if(isset($module_id)){
             $this->selectModel('Unit');
             $arr_set = $this->opm->arr_settings;
             $data = $this->Unit->select_all(array(
                'arr_where' => array(
                    'deleted' => false,
                 ),
            ));
             $newdata = array();
             $newdata = $data;
             $newdata = iterator_to_array($data);
            foreach($newdata as $keys => $values){
                if(isset($values['serial_no']) && isset($values['code']) && isset($values['standard_location_name']) && isset($values['usage'])&&isset($values['current_location_name'])&&isset($values['status'])&&isset($values['batch_name'])&&isset($values['date_modified'])){
                    $tmp[$keys]['serial_no'] = $values['serial_no'];
                    $tmp[$keys]['code'] = $values['code'];
                    $tmp[$keys]['standard_location_name'] = $values['standard_location_name'];
                    $tmp[$keys]['usage'] = $values['usage'];
                    $tmp[$keys]['current_location_name'] = $values['current_location_name'];
                    $tmp[$keys]['batch_name'] = $values['batch_name'];
                    $tmp[$keys]['status'] = $values['status'];
                    $tmp[$keys]['date_modified'] = $values['date_modified'];
                    //pr($values);
                    $total += count($values['_id']);
                }
            }
        }
        //pr($total);
        $subdatas['unit_serial'] = $tmp;
        $this->set('subdatas', $subdatas);
        $this->set('total', $total);
        $this->set_select_data_list('relationship', 'units_serials');
        //die;
    }

    public function batches() {
        $total_quantity = 0;
        $total_used = 0;
        $total_balance = 0;
        $tmp = array();
        $subdatas = array();
        $modele_id = $this->get_id();
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




	//$type_serach: 1(not low), 2(with check tracking)
	public function quantity_low_in_stock($type_serach=''){
        $stockcurrent = array();
        $module_id = $this->get_id();
        if (isset($module_id)) {
            $arr_set = $this->opm->arr_settings;
            $this->selectModel('Product');
			$where_query = array(
                'arr_where' => array(
                    'locations'=> array(
						'$elemMatch'=> array(
							'deleted' => false,
							'min_stock' => array('$ne' => null),
						),
					),
                ),
            );
			if($type_serach=='2' || $type_serach=='3')
				$where_query['arr_where']['check_stock_stracking'] = 1;

            $arr_query = $this->Product->select_all($where_query);

            $newdata = $temp = array();
            $newdata = $arr_query;
			$not_low_true =  false;
            foreach ($newdata as $keys => $values) {
                //pr($values['locations']);die;
                foreach ($values['locations'] as $key => $vv) {
					if(isset($vv['total_stock']) && isset($vv['min_stock']) && (int)$vv['total_stock'] < (int)$vv['min_stock'])
						$not_low_true =  true;
					else
						$not_low_true =  false;
					//bo dieu kien low
					if(($type_serach=='1' || $type_serach=='2') && isset($vv['total_stock']) && isset($vv['min_stock']))
						$not_low_true =  true;

                    if(isset($vv['deleted']) && !$vv['deleted'] && $not_low_true){
                        //$stockcurrent[$keys] = array_merge($values, $vv);
                        $kkey = (string)$vv['location_id'].(string) $values['_id'];
                        $stockcurrent[$kkey]['location_name']      =    $vv['location_name'];
                        $stockcurrent[$kkey]['product_name']       =    $values['name'];
                        if($vv['total_stock'] == '')
                            $vv['total_stock'] = 0;
						if($vv['min_stock'] == '')
                            $vv['min_stock'] = 0;
                        $stockcurrent[$kkey]['total_stock']        =      $vv['total_stock'];
                        $stockcurrent[$kkey]['min_stock']          =      $vv['min_stock'];
						if((int)$vv['total_stock']<(int)$vv['min_stock'])
							$stockcurrent[$kkey]['low']            =      'Low';
						else
							$stockcurrent[$kkey]['low']       	   =    	'';
                }
            }
        }
    }
	//pr($stockcurrent);die;
    return $stockcurrent;
}


    //========================view pdf=============================
function low_stock($type_serach='') {
    $this->layout = 'pdf';
    $date_now = date('Ymd');
    $time=time();
    $filename = 'low'.$date_now.$time;
	if($type_serach=='1')
		$filename = 'StockList'.$date_now.$time;
	if($type_serach=='2')
		$filename = 'StockListWithTracking'.$date_now.$time;
	if($type_serach=='3')
		$filename = 'LowWithTracking'.$date_now.$time;

    $html='';

    $tmp = array();
    $tmp = $this->quantity_low_in_stock($type_serach);
    $i=0;
    foreach($tmp as $key=>$value){

        if($i%2==0)
            $html .= ' <table cellpadding="4" cellspacing="0" class="tab_nd">';
        else
            $html .= '<table cellpadding="4" cellspacing="0" class="tab_nd2">';

        $html .= ' <tr class="border_2">
                <td width="20%" class="first top border_left border_btom">'.$value['location_name'].'</td>
                <td width="47%" class="top border_btom border_left" align="left">'.$value['product_name'].'</td>
                <td width="10%" class="top border_btom border_left " align="right">'.$value['total_stock'].'</td>
                <td align="right" width="8%" class="top border_btom border_left">'.$value['min_stock'].'</td>
                <td align="center" width="6%" class="end top border_btom border_left">'.$value['low'].'</td>
                <td align="left" width="9%" class="end top border_btom border_left">       </td>
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

    $pdf->file2_left=226;
    $pdf->file2='img'.DS.'LowStock_title.png';


    $pdf->bar_top_left=226;
    $pdf->bar_top_top=23;
    $pdf->bar_top_content='---------------------------------------------------';

    $pdf->hidden_left=251;
    $pdf->hidden_top=19;
    $pdf->hidden_content='';

    $pdf->bar_big_content='------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------';
    $pdf->bar_words_content='Location Name                             Product Name                                                                                                                  Total Stock                 Min     Low         Counted ';
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
                &nbsp; <b>';

    $html .= $i;

    $html .=' records listed
            </b></td>
            <td width="20%" class="end top border_btom">
                &nbsp;
            </td>
        </tr>
    </table>
    <div style=" clear:both; color: #c9c9c9;"><br />
    --------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------
    </div><br />
    ';
    $pdf->writeHTML($html, true, false, true, true, '');
    $pdf->Output(APP. 'webroot'.DS. 'upload'.DS .$filename.'.pdf', 'F');
    $this->redirect('/upload/'. $filename .'.pdf');
    die;
}

    function writeCate(){
        if(isset($_POST['category'])){
            $_SESSION['Products_current_category'] = $_POST['category'];
            echo 'ok';
        }
        die;
    }

    function duplicate_product(){
        $id = $this->get_id();
        $product = $this->opm->select_one(array('_id'=>new MongoId($id)));
        $product['code'] = $this->opm->get_auto_code('code');
        $product['created_by'] = new MongoId($this->opm->user_id());
        unset($product['_id']);
        unset($product['modified_by']);
        $this->opm->save($product);
        $new_id = $this->opm->mongo_id_after_save;
        echo URL.'/products/entry/'.$new_id;
        die;
    }
    function replace_product(){
        if(isset($_POST['id'])){
            $old_product = $this->opm->select_one(array('_id'=>new MongoId($_POST['id'])));
            $arr_data['_id'] = $old_product['_id'];
            $arr_data['code'] = $old_product['code'];
            unset($old_product['_id']);
            $current_product = $this->opm->select_one(array('_id'=>new MongoId($this->get_id())));
            $current_product['productoptions'] = $current_product['madeup'] = array();
            $replace_product = array_merge($current_product, $arr_data);
            $this->opm->save($replace_product);
            $replace_id = $replace_product['_id'];
            $old_product['deleted']= true;
            $old_product['replace_by_id'] = $arr_data['_id'];
            $old_product['code'] = $this->opm->get_auto_code('code');
            $this->opm->save($old_product);
            echo URL.'/products/entry/'.$replace_id;
        }
        die;
    }
    public function change_sku_to_string(){
        $products = $this->opm->select_all(array('limit'=>999999));
        $i =0;
        echo $products->count();
        foreach($products as $value){
            //An, 06.2.2014 chuyen so thanh string
            if(isset($value['sku'])){
                 if(!is_string($value['sku']))
                    $i++;
                $value['sku'] = (string)$value['sku'];

            }
            else
                $value['sku'] = '';
            $this->opm->update_sku($value);

        }
        echo '<br />Xong - '.$i;
        die;
    }
    public function rebuild_product(){
        $products = $this->opm->select_all(array('limit'=>999999));
        $i =0;
        echo $products->count();
        foreach($products as $value){
            $arr_data = array('_id'=>new MongoId($value['_id']));

			if(isset($value['oum']) && trim($value['oum'])!= ''){
                if($value['oum']=='Sq. ft.')
                    $arr_data['oum'] = $value['oum'] =  'Sq.ft.';
                else if($value['oum']=='Unit')
                    $arr_data['oum'] = 'unit';
                if($value['oum'] ==  'Sq.ft.' && (!isset($value['sell_by']) || $value['sell_by']!='area') )
                    $arr_data['sell_by']  = 'area';
				else if($value['oum'] ==  'unit' && (!isset($value['sell_by']) || $value['sell_by']!='unit') )
                    $arr_data['sell_by']  = 'unit';


			}else if( !isset($value['oum']) || trim($value['oum'])=='' ){
                if(isset($value['sell_by']) && $value['sell_by']!=''){
					if($value['sell_by']=='Area')
                    	$arr_data['sell_by'] = $value['sell_by'] =  'area';
                	else if($value['sell_by']=='Unit')
                    	$arr_data['sell_by'] = $value['sell_by'] = 'unit';

					else if($value['sell_by']=='Sq. ft.' || $value['sell_by']=='Sq.ft.'){
                    	$arr_data['sell_by'] = $value['sell_by'] = 'area';
						$arr_data['oum'] = $value['oum'] = 'Sq.ft.';

					}else if($value['sell_by']=='roll' || $value['sell_by']=='Roll'){
                    	$arr_data['sell_by'] = $value['sell_by'] = 'unit';
						$arr_data['oum'] = $value['oum'] = 'Roll';

					}else if($value['sell_by']=='Lr. ft.' || $value['sell_by']=='Lr.ft.'){
                    	$arr_data['sell_by'] = $value['sell_by'] = 'lengths';
						$arr_data['oum'] = $value['oum'] = 'Lr.ft.';
					}

                    if($value['sell_by']=='area')
                        $arr_data['oum'] =  'Sq.ft.';
                    else if($value['sell_by']=='unit')
                        $arr_data['oum'] =  'unit';
                } else{
                    $arr_data['oum'] =  'unit';
                    $arr_data['sell_by'] =  'unit';
                }
            }

            if(!isset($value['sizew_unit']) || trim($value['sizew_unit'])=='')
                $arr_data['sizew_unit'] = 'in';
            if(!isset($value['sizeh_unit']) || trim($value['sizeh_unit'])=='')
                $arr_data['sizeh_unit'] = 'in';
            if(!isset($value['thickness_unit']) || trim($value['thickness_unit'])=='')
                $arr_data['thickness_unit'] = 'in';

            if(isset($value['product_type']) && $value['product_type']=='Vendor Stock'){
                if((!isset($value['oum_depend']) || trim($value['oum_depend'])=='')
                    && isset($value['unit_price']) && $value['unit_price'] != '')
                    $arr_data['oum_depend'] = 'Sq.ft.';
            }
            if(count($arr_data)>1){
                $this->opm->rebuild_product($arr_data);
                $i++;
            }
			//echo (string)$value['_id'].'</br>';
        }
        echo '<br />Xong - '.$i;
        die;
    }
    public function set_default_caterogy(){
        $products = $this->opm->select_all(array('limit'=>999999));
        $i =0;
        echo $products->count();
        foreach($products as $value){
            $arr_data['_id']  = new MongoId($value['_id']);
            if(!isset($value['sell_price']) || trim($value['sell_price']=='')){
                    $arr_data['sellprices'] = $value['sellprices'] = array();
                    $this->opm->rebuild_product($arr_data);
                    $i++;
            } else {
                $arr_data['sellprices'] = array();
                $this->opm->rebuild_product($arr_data);
                $arr_data['sellprices'][] = array(
                                            'deleted'       => false,
                                            'sell_category' => 'Retail',
                                            'sell_unit_price'=> (float)$value['sell_price'],
                                            'sell_default'  => 1,
                                            'cate_key'      => 1,
                                            'category_text' => 1,
                                             );
                $this->opm->rebuild_product($arr_data);
                $i++;
            }
        }
        echo '<br />Xong - '.$i;
        die;
    }
    public function revert_deleted_product(){
        $this->opm->has_field_deleted = false;
        $products = $this->opm->select_all(array('arr_where'=>array('deleted'=>true,'code'=>array('$lte'=>3000)),'limit'=>999999));
        $i =0;
        echo $products->count();
        foreach($products as $value){
            $arr_data['_id']  = new MongoId($value['_id']);
            $arr_data['code'] = $value['code'];
            $arr_data['name'] = 'Blank';
            $arr_data['deleted'] = false;
            foreach($value as $k=>$v){
                $value[$k] = '';
            }
            $arr_data = array_merge($value,$arr_data);
            $this->opm->rebuild_product($arr_data);
            $i++;
        }
        echo '<br />Xong - '.$i.'<br />';
        $this->insert_lost_product();
        die;
    }
    public function insert_lost_product(){
        $this->opm->has_field_deleted = true;
        $products = $this->opm->select_all(
                                           array(
                                                'arr_where'=>array('code'=>array('$lte'=>5000)),
                                                'limit'=>999999,
                                                'arr_order'=>array('code'=>1)
                                                )
                                        );
        $this->opm->arrfield();
        $arr_save = $this->opm->arr_temp;
        $arr_save['name'] = 'Blank';
        $str = '';
        foreach($products as $value){
            if(isset($previous_code)){
                $count = (int)$value['code'] - $previous_code;
                if($count>1){
                    for($i = $previous_code + 1 ; $i < $value['code']; $i++){
                        $arr_save['code'] = $i;
                        $this->opm->save($arr_save);
                    }
                    $str .= '------Inserted '.$count.' lost record(s) from code: '.$previous_code.' to code: '.$value['code'].'<br />';
                }
            }
            $previous_code = (int)$value['code'];
        }
        echo $str;
        die;
    }
    public function unset_empty_tag(){
        $products = $this->opm->select_all(array('limit'=>999999));
        $i =0;
        echo $products->count();
        foreach($products as $value){
            $arr_data['_id']  = new MongoId($value['_id']);
            if(isset($value['production_step'])&&!empty($value['production_step'])){
                $old_num = count($value['production_step']);
                foreach($value['production_step'] as $key=>$val){
                    if(trim($val['tag'])==''){
                        unset($value['production_step'][$key]);
                    }
                }
                if(count($value['production_step'])!=$old_num)
                    $arr_data['production_step'] = $value['production_step'];
            }
            if(count($arr_data)>1){
                $this->opm->rebuild_product($arr_data);
                $i++;
            }
        }
        echo '<br />Xong - '.$i;
        die;
    }
    function view_minilist(){
        if(!isset($_GET['print_pdf'])){
            $arr_where = $this->arr_search_where();
            $products = $this->opm->select_all(array(
                                               'arr_where'  =>  $arr_where,
                                               'arr_field'  =>  array('code','name','company_name','status','cost_price','sell_price','qty_in_stock'),
                                               'arr_order'  =>  array('_id'=>1),
                                               'limit'      =>  99999
                                               ));
            if($products->count()>0){
                $group = array();
                $html = '';
                $i = 0;
                $total_qty = 0;
                $total_margin = 0;
                $total_cost = 0;
                $total_sell = 0;
                $arr_data = array();
                foreach($products as $key=>$product){
                    $costings_data = $this->costings_data($product['_id']);
                    $pricingsummary = $costings_data['pricingsummary'];
                    $cost_price = (isset($pricingsummary['cost_price']) ? (float)$pricingsummary['cost_price']: 0);
                    $sell_price = (isset($pricingsummary['sell_price']) ? (float)$pricingsummary['sell_price'] : 0);
                    $margin = (isset($pricingsummary['margin']) ? (float)$pricingsummary['margin'] : 0);
                    $qty_in_stock = (isset($product['qty_in_stock']) ? (int)$product['qty_in_stock'] : 0);
                    $total_cost += $cost_price;
                    $total_sell += $sell_price;
                    $total_margin += $margin;
                    $total_qty += $qty_in_stock;
                    $html .= '<tr class="'.($i%2==0 ? 'bg_2' : 'bg_1').'">';
                    $html .= '<td>'.$product['code'].'</td>';
                    $html .= '<td>'. (isset($product['name']) ? $product['name'] : '') .'</td>';
                    $html .= '<td>'.(isset($product['company_name']) ? $product['company_name'] : '') .'</td>';
                    $html .= '<td class="bold_text center_text">'.(isset($product['status'])&&$product['status']==2 ? '<strong>X</strong>' : '').'</td>';
                    $html .= '<td class="right_text">'.$this->opm->format_currency($cost_price).'</td>';
                    $html .= '<td class="right_text">'.$this->opm->format_currency($sell_price).'</td>';
                    $html .= '<td class="right_text">'.($margin<0 ? '<span class="red_text">'.$this->opm->format_currency($margin,1).'%</span>' : $this->opm->format_currency($margin,1).'%' ).'</td>';
                    $html .= '<td class="right_text">'.$qty_in_stock.'</td>';
                    $html .= '<td class="right_text">'.$this->opm->format_currency($cost_price*$qty_in_stock).'</td>';
                    $html .= '<td class="right_text">'.$this->opm->format_currency($sell_price*$qty_in_stock).'</td>';
                    $html .= '</tr>';
                    $i++;
                }
                $html .='<tr class="last">
                            <td colspan="2" class="bold_text right_none">'.$i.' record(s) listed.</td>
                            <td colspan="4" class="right_text bold_text right_none">Avarage / Total:</td>
                            <td class="right_text right_none">'.($total_margin<0 ? '<span class="red_text">'.$this->opm->format_currency($total_margin/$i,1).'%</span>' : $this->opm->format_currency($total_margin/$i,1).'%' ).'</td>
                            <td class="right_text right_none">'.$total_qty.'</td>
                            <td class="right_text right_none">'.$this->opm->format_currency($total_cost).'</td>
                            <td class="right_text">'.$this->opm->format_currency($total_sell).'</td>
                         </tr>';
                $arr_data['title'] = array('Code','Name','Supllier','Inactive','Cost price'=>'width: 8%;text-align: right;','Sell price'=>'width: 8%;text-align: right;','Margin','In stock'=>'width: 6%;text-align: right;','Sum cost'=>'width: 8%;text-align: right;','Sum'=>'width: 8%;text-align: right;');
                $arr_data['content'] = $html;
                $arr_data['report_name'] = 'Product Mini Listing (Financial)';
                $arr_data['report_file_name']='PRO_'.md5(time());
                $arr_data['report_orientation'] = 'landscape';
                Cache::write('products_minilist', $arr_data);
            }
        } else{
            $arr_data = Cache::read('products_minilist');
            Cache::delete('products_minilist');
        }
        $this->render_pdf($arr_data);
    }
    function export_list(){
        if(!isset($_GET['print_pdf'])){
            $arr_where = $this->arr_search_where();
            $products = $this->opm->select_all(array(
                                               'arr_where'  =>  $arr_where,
                                               'arr_field'  =>  array('code','name', 'sku','company_id','product_type','category','sell_price','qty_in_stock','sell_by','oum','approved','special_order'),
                                               'arr_order'  =>  array('_id'=>1),
                                               'limit'      =>  999999
                                               ));
            if($products->count()>0){
                $html = '';
                $i = 0;
                $arr_companies = array();
                $this->selectModel('Company');
                foreach($products as $product){
                    if( isset($product['company_id']) && is_object($product['company_id']) ) {
                        if( !isset($arr_companies[(string)$product['company_id']]) ) {
                            $company = $this->Company->select_one(array('_id' => $product['company_id']),array('name'));
                            if( isset($company['name']) ) {
                                $company = $company['name'];
                            } else {
                                $company = '';
                            }
                            $arr_companies[(string)$product['company_id']] = $company;
                        }
                         $company = $arr_companies[(string)$product['company_id']];
                    } else {
                        $company = '';
                    }
                    $qty_in_stock = isset($product['qty_in_stock']) ? $product['qty_in_stock'] : 0;
                    $sell_price = isset($product['sell_price']) ? $product['sell_price'] : 0;
                    $html .= '<tr class="'.($i%2==0 ? 'bg_2' : 'bg_1').'">';
                    $html .= '<td>'.(isset($product['code']) ? $product['code'] : '').'</td>';
                    $html .= '<td>'.(isset($product['sku']) ? $product['sku'] : '').'</td>';
                    $html .= '<td>'.(isset($product['name']) ? $product['name'] : '').'</td>';
                    $html .= '<td>'.(isset($product['product_type']) ? $product['product_type'] : '') .'</td>';
                    $html .= '<td>'.$company.'</td>';
                    $html .= '<td>'.(isset($product['category']) ? $product['category'] : '') .'</td>';
                    $html .= '<td class="right_text">'.$qty_in_stock.'</td>';
                    $html .= '<td>'.(isset($product['sell_by']) ? ucfirst($product['sell_by']) : '') .'</td>';
                    $html .= '<td>'.(isset($product['oum']) ? ucfirst($product['oum']) : '') .'</td>';
                    $html .= '<td class="right_text">'.$this->opm->format_currency($sell_price).'</td>';
                    $html .= '<td class="center_text">'.(isset($product['approved']) && $product['approved'] ? '<span class="bold_text">X<span>' : '') .'</td>';
                    $html .= '<td class="center_text">'.(isset($product['special_order']) && $product['special_order'] ? '<span class="bold_text">X</span>' : '') .'</td>';
                    $html .= '</tr>';
                    $i++;
                }
                $html .='<tr class="last">
                            <td colspan="12" class="bold_text right_none">'.$i.' record(s) listed.</td>
                         </tr>';
                $arr_data['title'] = array('Code','SKU' => 'width: 13%;', 'Name','Type','Supplier','Category','In stock'=>'width: 8%;text-align: right;', 'Sold by', 'OUM', 'Sell price'=>'width: 8%;text-align: right;','Approved'=>'text-align: center;','Special Order' => 'text-align: center;');
                $arr_data['content'] = $html;
                $arr_data['report_name'] = 'Product Listing';
                $arr_data['report_file_name']='PRO_'.md5(time());
                $arr_data['report_orientation'] = 'landscape';
                Cache::write('products_list', $arr_data);
            }
        } else{
            $arr_data = Cache::read('products_list');
            Cache::delete('products_list');
        }
        $this->render_pdf($arr_data);
    }
    public function description(){
        $query = $this->opm->select_one(array('_id'=>new MongoId($this->get_id())));
        $this->set('product_desciption',(isset($query['product_desciption']) ? $query['product_desciption'] : ''));
    }

	//Printing
	public function printing(){
		$query = $this->opm->select_one(array('_id'=>new MongoId($this->get_id())));
		$subdatas = $arr_options_custom = array();
        $subdatas['printer_setup'] = array();
		$subdatas['printer_setup']['is_printer'] = isset($query['is_printer'])?$query['is_printer']:0;
		$subdatas['printer_setup']['paper_size'] = isset($query['paper_size'])?$query['paper_size']:'0x0';
		if(isset($query['arr_paper_size']) && is_array($query['arr_paper_size']) && count($query['arr_paper_size'])>0)
			$arr_options_custom['paper_size']= $query['arr_paper_size'];

		$subdatas['sheetimage'] =  array();

		$subdatas['price_calculator'] = array();
		$price_setup = $this->printing_get_setup();
		$subdatas['price_calculator'] = $price_setup['setup'];
		if(isset($price_setup['arr_options_custom']))
		$arr_options_custom = array_merge((array)$arr_options_custom,(array)$price_setup['arr_options_custom']);
		$subdatas['price_calculator']['option_unit_price'] = 1;

        $this->set('subdatas', $subdatas);
		$this->set('arr_options_custom', $arr_options_custom);

    }
	public function add_paper_size($w=0,$h=0){
		$product = $query = array();
		$product['_id'] = new MongoId($this->get_id());
		if(isset($_POST['w']))
			$w=(float)$_POST['w'];
		if(isset($_POST['h']))
			$h=(float)$_POST['h'];
		$query = $this->opm->select_one(array('_id'=>$product['_id']));
		$arr_paper_size = (isset($query['arr_paper_size']) && is_array($query['arr_paper_size'])) ? $query['arr_paper_size'] : array();
		$key = $w.'x'.$h;
		$key = str_replace(".","_",$key);
		$arr_paper_size[$key] = $w.'x'.$h;
		$product['arr_paper_size'] = $arr_paper_size;
		$this->opm->save($product);
		if(isset($_POST['w'])) die;
		else return true;
    }

	public function printing_get_setup($select_key=array('inkcolor','packing'),$input_key = array('rip','cutting')){
		$options_data = $price_option = $price  = array();
		$options_data = $this->opm->options_data($this->get_id(),true);
		$options_data = isset($options_data['productoptions'])?$options_data['productoptions']:array();
		if(is_array($options_data) && count($options_data)>0){
			foreach($options_data as $key=>$value){
				if(!$value['deleted']){
					if(isset($value['group_type']) && $value['group_type']=='Exc'){
						$price_option[$value['option_group']][(string)$value['sub_total']] = $value['product_name'].'('.$value['sub_total'].')';
					}else
						$price_option[$value['option_group']] = $value['sub_total'];
				}
			}
		}

		foreach($select_key as $key){
			if(isset($price_option[$key])){
				end($price_option[$key]);
				$price['setup'][$key] = key($price_option[$key]);
				$price['arr_options_custom'][$key] = $price_option[$key];
			}else
				$price['setup'][$key] = '';
		}
		foreach($input_key as $key){
			$price['setup'][$key] = isset($price_option[$key])?$price_option[$key]:0;
		}
		return $price;
	}
	public function printer_pricing($price_setup=array(),$qt=1,$w=0,$h=0,$wr=1,$hr=1,$cutting_amount=1,$cut_ra=1){
		if(isset($_POST['price_setup']))
			$price_setup=$_POST['price_setup'];
		if(isset($_POST['qt']))
			$qt=$_POST['qt'];
		if(isset($_POST['w']))
			$w=$_POST['w'];
		if(isset($_POST['h']))
			$h=$_POST['h'];
		if(isset($_POST['wr']))
			$wr=$_POST['wr'];
		if(isset($_POST['hr']))
			$hr=$_POST['hr'];
		if(isset($_POST['cutting_amount']))
			$cutting_amount=(float)$_POST['cutting_amount'];
		if(isset($_POST['cut_ra']))
			$cut_ra=(float)$_POST['cut_ra'];
		$yield = $this->sheet_yield_calculator($w,$h,$wr,$hr,1);

		$paper_amount = (float)$qt/(float)$yield['total_yield'];

		$printer_pricing = $qt*(float)$price_setup['inkcolor'] + (float)$price_setup['rip'] + (float)$price_setup['packing'] + $cutting_amount*$cut_ra*$paper_amount*(float)$price_setup['cutting'];

		/*$test[] = $qt*(float)$price_setup['inkcolor'];
		$test[] = (float)$price_setup['rip'];
		$test[] = (float)$price_setup['packing'];
		$test[] = $cutting_amount*$cut_ra*$paper_amount*(float)$price_setup['cutting'];*/

		if(isset($_POST['w'])){
			echo $printer_pricing; die;
		}else
			return $printer_pricing;
	}


    //2D Cutting
    public function cutting(){
        // $query = $this->opm->select_one(array('_id'=>new MongoId($this->get_id())));
        $subdatas = $arr_options_custom = array();
        $subdatas['setup_size'] = array();
        $subdatas['setup_size']['paper_size'] = isset($query['paper_size'])?$query['paper_size']:'0x0';
        if(isset($query['arr_paper_size']) && is_array($query['arr_paper_size']) && count($query['arr_paper_size'])>0)
            $arr_options_custom['paper_size'] = $query['arr_paper_size'];

        $subdatas['sheetimage'] =  array();
        $subdatas['finished_size_list'] = array();

        $this->set('subdatas', $subdatas);
    }

    public function cutting_process(){
        $arr_return = array('status' => 'error' ,'message' => 'Please enter valid infomation.');
        if(!empty($_POST)){
            $arr_post = $_POST;
            $W = $arr_post['poster_size_w'];
            $H = $arr_post['poster_size_h'];

            $osizes = $arr_post['materials'];

            cutting($W, $H, $osizes, $skyArray, $bestPolicy, $leastSheets, $sheets, $gaps);

            // output to JSON file
            $sheetStop = $leastSheets;

            // output to screen

            $image = array();
            $imageI = 0;
            $image = new Imagick();    // Create a new instance an $image class
            $width =  3500;// Some necessary dimensions
            $height = 1800;

            // $image class now inherits some attributes. i.e. Dimensions, bkgcolor...
            $image->newImage( $width, $height, new ImagickPixel( 'lightgray' ) );
            $draw = new ImagickDraw();    //Create a new drawing class

            $sheetStop = $leastSheets;
            $sky = $skyArray[$bestPolicy]["sky"];
            $skyLeft = $skyLen = count($sky);
            for($sheetStop = 0; $sheetStop < $leastSheets; $sheetStop++) {
                $startY = (300*$sheetStop)%1800;
                $startX = intval($sheetStop/6);
                $startX = $startX*720;

                $gap = $gaps[$bestPolicy][$sheetStop];

                drawEmptySheet($startX, $startY, $W, $H, $draw);
                //draw Skyline
                drawSkyLine($startX, $startY, $sheetStop+1, $sky, $draw, $skyLeft);
                // drawing gaps
                drawGaps($startX, $startY, $gap, $draw);
                drawRemaining($startX, $startY, $H, $sheetStop+1, $skyLeft, $skyLen, $draw);
            }

            drawProblem($osizes, $draw);
            drawPolicy("Chosen Policy: ".$skyArray[$bestPolicy]["policy"]."\n\nwith ".$leastSheets." material sheets", $draw);

            $image->drawImage( $draw );
            $image->setImageFormat('jpg');    // Give the image a format
            $filename = md5(time()).'.jpg';
            $image->writeImage(WWW_ROOT.'upload'.DS.$filename);
            $arr_return = array('status' => 'ok', 'image' => URL.'/upload/'.$filename, 'type' => $skyArray[$bestPolicy]["policy"], 'sheet' => $leastSheets);

        }
        echo json_encode($arr_return);
        die;
    }



    public function rebuild_asset_tag(){
        $products = $this->opm->select_all(array('limit'=>999999));
        $i =0;
        echo $products->count();
        $this->selectModel('Equipment');
        $combo_asset = $this->Equipment->select_combobox_asset();
        $combo_asset = array_flip ($combo_asset);
        foreach($products as $value){
            $change = false;
            $arr_data = array();
            $arr_data['_id']  = new MongoId($value['_id']);
            if(isset($value['production_step'])&&!empty($value['production_step'])){
                foreach($value['production_step'] as $key=>$val){
                    if(isset($val['deleted'])&&$val['deleted']) continue;
                    if(!isset($val['tag']) || $val['tag']=='') {
                        $value['production_step'][$key]['deleted'] = true;
                        $change = true;
                        continue;
                    }
                    if(isset($val['tag_key'])&&is_object($val['tag_key'])) continue;
                    if(isset($combo_asset[$val['tag']])){
                        $value['production_step'][$key]['tag_key'] = new MongoId($combo_asset[$val['tag']]);
                    }
                    else
                        $value['production_step'][$key]['deleted'] = true;
                    $change = true;
                }
                if($change){
                    $arr_data['production_step'] = $value['production_step'];
                    $this->opm->rebuild_product($arr_data);
                    $i++;
                }
            }
        }
        echo '<br />Xong - '.$i;
        die;
    }
    public function tracking_stock(){
        $arr_data = array();
        if(isset($_GET['print_pdf'])){
            $arr_data = Cache::read('products_tracking_stock');
            Cache::delete('products_tracking_stock');
        } else{
            $this->selectModel('Salesorder');
            $location = $this->opm->select_one(array('_id'=> new MongoId($this->get_id())),array('locations'));
            if(!isset($location['locations']))
                $location['locations'] = array();
            $arr_stocks = array();
            foreach($location['locations'] as $value){
                if(isset($value['deleted']) && $value['deleted']) continue;
                if(!isset($value['location_id']) || !is_object($value['location_id'])) continue;
                if(isset($arr_stocks[(string)$value['location_id']])) continue;
                $arr_query = $this->opm->select_all(array(
                    'arr_where' => array(
                            'locations'=>array(
                                               '$elemMatch'=>array(
                                                'location_id' => new MongoId($value['location_id']),
                                                'deleted'   => false
                                                )
                                )
                    ),
                    'arr_order'=>array('code'=>-1),
                    'arr_field'=>array('code','sku','name','category','unit_price','oum_depend','cost_price','oum','sell_price','profit','locations')
                ));
                $total_cost = 0;
                $total = 0;
                $total_sell = 0;
                $total_available = 0;
                $total_min_stock = 0;
                $total_profit = 0;
                $total_onso = 0;
                $total_stock = 0;
                foreach($arr_query as $query){
                    if(isset($query['cost_price']))
                        $total_cost += $query['cost_price'];
                    if(isset($query['sell_price']))
                        $total_sell += $query['sell_price'];
                    if(isset($query['profit']))
                        $total_profit += $query['profit'];
                    foreach ($query['locations'] as $location) {
                        if(isset($location['deleted']) && $location['deleted']) continue;
                        if(!isset($location['location_id']) || !is_object($location['location_id'])) continue;
                        if($location['location_id'] != $value['location_id']) continue;
                        $stockcurrent = array_merge($query, $location);
                        if(!isset($stockcurrent['unit_price']))
                            $stockcurrent['unit_price'] = 0;
                        if(!isset($stockcurrent['total_stock']))
                            $stockcurrent['total_stock'] = 0;
                        if(!isset($stockcurrent['min_stock']))
                            $stockcurrent['min_stock'] = 0;
                        unset($stockcurrent['locations']);
                        $stockcurrent['low'] = '';
                        if(isset($location['min_stock']) && (int)$location['total_stock'] < (int)$location['min_stock']&&isset($location['min_stock']) &&isset($location['total_stock'])){
                            $stockcurrent['low'] = 'X';
                        }
                        $arr_salesorders = $this->Salesorder->collection->aggregate(
                            array(
                                '$match'=>array(
                                                'status' => array('$in'=>array('Submitted','In production')),
                                                'products'=>array(
                                                                  '$elemMatch'=>array(
                                                                                      'products_id'=>$query['_id'],
                                                                                      'deleted'=>false
                                                                                      )
                                                                  )
                                                ),
                            ),
                            array(
                                '$unwind'=>'$products',
                            ),
                             array(
                                '$match'=>array(
                                                'products.deleted'=>false,
                                                'products.products_id'=>$query['_id'],
                                                )
                            ),
                            array(
                                '$project'=>array('products'=>'$products.quantity')
                            ),
                            array(
                                '$group'=>array(
                                              '_id'=>array('_id'=>'$_id'),
                                              'quantity'=>array('$push'=>'$products')
                                            )
                            )
                        );
                        $stockcurrent['on_so']  = 0;
                        if(isset($arr_salesorders['ok'])){
                            foreach($arr_salesorders['result'] as $result)
                                foreach($result['quantity'] as $quantity)
                                    $stockcurrent['on_so']  +=$quantity;
                        }
                        $total_onso += $stockcurrent['on_so'];
                        $stockcurrent['available'] =0;
                        if(isset($stockcurrent['in_use']))
                            $stockcurrent['available'] +=(float)$stockcurrent['in_use'];
                        if(isset($stockcurrent['in_assembly']))
                            $stockcurrent['available'] +=(float)$stockcurrent['in_assembly'];
                        if(isset($stockcurrent['on_so']))
                            $stockcurrent['available'] +=(float)$stockcurrent['on_so'];
                        if(isset($location['total_stock']))
                            $total += (float)$location['total_stock'];
                        $stockcurrent['available'] = $stockcurrent['total_stock'] -  $stockcurrent['available'];
                        $total_stock += $stockcurrent['total_stock'];
                        $total_available += $stockcurrent['available'];
                        $total_min_stock += $stockcurrent['min_stock'];
                        $arr_stocks[(string)$value['location_id']]['products'][] = $stockcurrent;

                    }
                }
                if($value['location_name'] == '')
                    $value['location_name'] = '(empty)';
                $arr_stocks[(string)$value['location_id']]['location_name'] = $value['location_name'];
                $arr_stocks[(string)$value['location_id']]['total_cost'] = $total_cost;
                $arr_stocks[(string)$value['location_id']]['total_sell'] = $total_sell;
                $arr_stocks[(string)$value['location_id']]['total_onso'] = $total_onso;
                $arr_stocks[(string)$value['location_id']]['total_stock'] = $total_stock;
                $arr_stocks[(string)$value['location_id']]['total_profit'] = $total_profit;
                $arr_stocks[(string)$value['location_id']]['total_available'] = $total_available;
                $arr_stocks[(string)$value['location_id']]['total_min_stock'] = $total_min_stock;
            }
            //=============================================================================
            $html = '';
            foreach($arr_stocks as $stock){
                $html .= '
                <table class="table_content">
                   <tbody>
                      <tr class="tr_right_none" style="background: #911b12;color: white;height: 29px;line-height: 29px;font-weight: bold;">
                         <td width="50%">
                            Stock Name
                         </td>
                         <td class="right_text" width="25%">
                            Total Stock
                         </td>
                      </tr>
                      <tr class="bg_2">
                         <td>' . $stock['location_name'] . '</td>
                         <td class="right_text">' . $stock['total_stock'] . '</td>
                      </tr>
                   </tbody>
                </table>';
                 $html .= '<table class="table_content" >
                        <tbody>
                          <tr class="tr_right_none" style="background: #911b12;color: white;height: 29px;line-height: 29px;font-weight: bold;">
                             <td width="3%">
                                Code
                             </td>
                             <td width="5%">
                                SKU
                             </td>
                             <td width="15%">
                                Name
                             </td>
                             <td>
                                Category
                             </td>
                             <td class="right_text">
                                Cost Price
                             </td>
                             <td>
                                OUM
                             </td>
                             <td class="right_text">
                                Unit Price
                             </td>
                             <td>
                                OUM
                             </td>
                             <td class="right_text">
                                Total stock
                             </td>
                             <td class="right_text">
                                On SO\'s
                             </td>
                             <td>
                                Use
                             </td>
                             <td>
                                Assembly
                             </td>
                             <td class="right_text">
                                Available
                             </td>
                             <td>
                                Min stock
                             </td>
                             <td>
                                Low
                             </td>
                          </tr>';
                $i = 0;
                foreach($stock['products'] as $product){
                    $html .= '
                        <tr class="content_asset bg_' . ($i % 2 == 0 ? '1' : '2') . '">
                            <td>'.$product['code'].'</td>
                            <td>'.$product['sku'].'</td>
                            <td>'.$product['name'].'</td>
                            <td>'.$product['category'].'</td>
                            <td class="right_text">'.$this->opm->format_currency((float)$product['sell_price']).'</td>
                            <td>'.ucfirst($product['oum']).'</td>
                            <td class="right_text">'.$this->opm->format_currency((float)$product['unit_price']).'</td>
                            <td>'.ucfirst($product['oum_depend']).'</td>
                            <td class="right_text">'.$product['total_stock'].'</td>
                            <td class="right_text">'.$product['on_so'].'</td>
                            <td></td>
                            <td></td>
                            <td class="right_text">'.$product['available'].'</td>
                            <td class="right_text">'.$product['min_stock'].'</td>
                            <td class="bold_text center_text">'.$product['low'].'</td>
                        </tr>
                    ';
                    $i++;
                }
                $html .= '
                        <tr class="content_asset bg_' . ($i % 2 == 0 ? '1' : '2') . '">
                            <td class="bold_text" colspan="6">'.$i.' record(s) listed.</td>
                            <td class="right_text bold_text" colspan="2">Totals</td>
                            <td class="right_text bold_text">' . $stock['total_stock'] . '</td>
                            <td class="right_text bold_text">' . $stock['total_onso'] . '</td>
                            <td class="right_text bold_text"></td>
                            <td class="right_text bold_text"></td>
                            <td class="right_text bold_text">' . $stock['total_available'] . '</td>
                            <td class="right_text bold_text">' . $stock['total_min_stock'] . '</td>
                            <td class="right_text bold_text"></td>
                        </tr>
                    ';
                $html.='</table><br /><br /><br />';
            }
            //=============================================================================
            $arr_data['content'][]['html'] = $html;
            $arr_data['is_custom'] = true;
            $arr_data['image_logo'] = true;
            $arr_data['report_name'] = 'Tracking stock';
            $arr_data['report_file_name'] = 'PR_'.md5(time());
            Cache::write('products_tracking_stock',$arr_data);
        }
        $this->render_pdf($arr_data);
    }

    function small_area_list(){
        $products = $this->opm->select_all(array(
                                        'arr_where' => array(
                                            'pricing_method' => 'small_area'
                                            ),
                                        'arr_order' => array('code' => 1),
                                        'arr_field' => array('sku','code','name','cost_price','pricebreaks','sell_price')
            ));
        $arr_products = array();
        foreach($products as $product){
            if(!isset($product['pricebreaks']))
                $product['pricebreaks'] = array();
            $retail_price = array_filter($product['pricebreaks'], function($arr){
                if(isset($arr['deleted']) && $arr['deleted'])
                    return false;
                if(isset($arr['sell_category']) && $arr['sell_category'] != 'Retail')
                    return false;
                return true;
            });
            $trade_price = array_filter($product['pricebreaks'], function($arr){
                if(isset($arr['deleted']) && $arr['deleted'])
                    return false;
                if(isset($arr['sell_category']) && $arr['sell_category'] != 'Trade')
                    return false;
                return true;
            });
            if(empty($trade_price))
                $trade_price = $retail_price;
            $arr_products[] = array(
                    'code'  => $product['code'],
                    'sku'   => isset($product['sku']) ? $product['sku'] : '',
                    'name'  => isset($product['name']) ? $product['name'] : '',
                    'cost_price'  => isset($product['cost_price']) ? (float)$product['cost_price'] : 0,
                    'sell_price'  => isset($product['sell_price']) ? (float)$product['sell_price'] : 0,
                    'retail_price'  => $retail_price,
                    'trade_price'   => $trade_price,
                );
        }
        if(empty($arr_products)){
            echo 'No data.';die;
        }
        $this->selectModel('Hooksetting');
        $small_area = $this->Hooksetting->select_one(array('name' => 'Small area'),array('options'));
        usort($small_area['options'], function($a, $b){
            return $a['up_price'] > $b['up_price'];
        });
        $small_area = $small_area['options'];

        App::import('Vendor', 'phpexcel/PHPExcel');
        $objPHPExcel = new PHPExcel();

        // Set document properties
        $objPHPExcel->getProperties()->setCreator('')
                                     ->setLastModifiedBy('')
                                     ->setTitle('Small Area Products')
                                     ->setSubject('Small Area Products')
                                     ->setDescription('Small Area Products')
                                     ->setKeywords('Small Area Products')
                                     ->setCategory('Small Area Products');
        $worksheet = $objPHPExcel->getActiveSheet();
        $worksheet->setCellValue('A1','#')
                                        ->setCellValue('B1','Code')
                                        ->setCellValue('C1','SKU')
                                        ->setCellValue('D1','Name')
                                        ->setCellValue('E1','Calculating')
                                        ->setCellValue('G1','Cost Price')
                                        ->setCellValue('H1','Sell Price')
                                        ->setCellValue('I1','Retail Price')
                                        ->setCellValue('K1','Trade Price');
        $worksheet->mergeCells('I1:J1');
        $worksheet->mergeCells('K1:L1');
        $worksheet->mergeCells('E1:F2');
        //
        for($i = 'A'; $i !== 'E'; $i++){
            $worksheet->mergeCells("{$i}1:{$i}2");
        }
        $worksheet->mergeCells('G1:G2');
        $worksheet->mergeCells('H1:H2');
        $worksheet->setCellValue('I2','Qty')
                    ->setCellValue('J2','Sell price')
                    ->setCellValue('K2','Qty')
                    ->setCellValue('L2','Sell price');
        $worksheet->getRowDimension(8)->setRowHeight(-1);
        $worksheet->getStyle('A1:L1')->getFont()->setBold(true);
        $worksheet->getStyle('A1:F1')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
        $worksheet->getStyle('G1:H1')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
        $worksheet->getStyle('I1:L1')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        $worksheet->getStyle('I2')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        $worksheet->getStyle('J2')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
        $worksheet->getStyle('K2')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        $worksheet->getStyle('L2')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
        $i = 3;
        $j = 1;
        foreach($arr_products as $product){
            $sell_price = $this->opm->format_currency($product['sell_price']);
            $worksheet->setCellValue('A'.$i, $j)
                        ->setCellValue('B'.$i, $product['code'])
                        ->setCellValue('C'.$i, $product['sku'])
                        ->setCellValue('D'.$i, $product['name'])
                        ->setCellValue('G'.$i,$this->opm->format_currency($product['cost_price']))
                        ->setCellValue('H'.$i,$sell_price);
            $i_area = $i_cp = $i;
            foreach($small_area as $area){
                $worksheet->setCellValue('E'.$i_area, 'Adj. Area < '.$area['area_limit']);
                $worksheet->setCellValue('F'.$i_area, "( $sell_price + ($sell_price * {$area['up_price']}%) ) * Area");
                $i_area++;
            }
            foreach($product['retail_price'] as $retail_price){
                $worksheet->setCellValue('I'.$i, $retail_price['range_from'].' - '.$retail_price['range_to']);
                $worksheet->setCellValue('J'.$i, $this->opm->format_currency($retail_price['unit_price']));
                $i++;
            }
            foreach($product['trade_price'] as $retail_price){
                $worksheet->setCellValue('K'.$i_cp, $retail_price['range_from'].' - '.$retail_price['range_to']);
                $worksheet->setCellValue('L'.$i_cp, $this->opm->format_currency($retail_price['unit_price']));
                $i_cp++;
            }
            $retail_count = count($product['retail_price']);
            $trade_count = count($product['trade_price']);
            $max = $retail_count > $trade_count ? $retail_count : $trade_count;
            $i_max = $i > $i_cp ? $i : $i_cp;
            if($max < 3){
                $i_max = $i += (3 - $max);
                $max = 3;
            }
            for($k = 'A'; $k !== 'E'; $k++){
                $worksheet->mergeCells($k.($i_max - $max).':'.$k.($i_max  - 1));
            }
            for($k = 'G'; $k !== 'I'; $k++){
                $worksheet->mergeCells($k.($i_max - $max).':'.$k.($i_max  - 1));
            }
            if($max > 3)
                $worksheet->mergeCells('E'.($i_area).':F'.($i_max  - 1));
            $j++;
        }
        $worksheet->getStyle('I3:I'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        $worksheet->getStyle('K2:K'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        $worksheet->getStyle('A1:D'.$i)->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
        $worksheet->getStyle('E1:F'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER)->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
        $worksheet->getStyle('G1:H'.$i)->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);

        $worksheet->getStyle('G3:H'.$i)->getNumberFormat()->setFormatCode("#,##0.00");
        $worksheet->getStyle('J3:J'.$i)->getNumberFormat()->setFormatCode("#,##0.00");
        $worksheet->getStyle('L3:L'.$i)->getNumberFormat()->setFormatCode("#,##0.00");
        $styleArray = array(
                'borders' => array(
                   'allborders' => array(
                       'style' => PHPExcel_Style_Border::BORDER_THIN
                    )
                ),
                'font'  => array(
                    'size'  => 12,
                    'name'  => 'Century Gothic'
                )
        );
        $worksheet->getStyle('A1:L'.($i -1) )->applyFromArray($styleArray);
        $objPHPExcel->setActiveSheetIndex(0);
        $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
        $objWriter->save(APP.DS.'webroot'.DS.'upload'.DS.'Small_Area_Products.xlsx');
        $this->redirect('/upload/Small_Area_Products.xlsx');
        die;
    }

    function id_to_time($id){
        echo date('d M, Y h:i:s',(new MongoId($id))->getTimestamp());
        die;
    }

    function recal_table_price($id='',$cost='',$price_breaks_type=''){
        $arr_save = array();
        if($id=='')
            $id = $this->get_id();
        $pro = $this->opm->select_one(array('_id'=>new MongoId($id)));
        $this->selectModel('Stuffs');
        if($price_breaks_type=='' && isset($pro['price_breaks_type']))
            $price_breaks_type =$pro['price_breaks_type'];
        if($price_breaks_type!=''){
            $cf_pb = $this->Stuffs->select_one(array('_id'=>new MongoId($price_breaks_type)));
            if($cost==''){
                $cost = (float)$pro['cost_price'];
                $arr_save['cost_price'] = $cost;
            }
            $sell_price = $cost*(float)$cf_pb['option']['price_rate']['value'];

            $arr_save['_id'] = new MongoId($id);
            $arr_save['sell_price'] = $sell_price;
            $pricebreaks = array();
            // if(isset($pro['pricebreaks']))
            //     $pricebreaks = $pro['pricebreaks'];
            if(isset($cf_pb['table_ranges']))
                $pricebreaks = $cf_pb['table_ranges'];
            $price_step = ($sell_price-$cost)/(count($pricebreaks)-1);
            //use price_step in config
            if((float)$cf_pb['option']['price_step']['value']>0)
                $price_step = (float)$cf_pb['option']['price_step']['value'];

            $arr_save['pricebreaks'] = array();
            if(count($pricebreaks)>0){
                foreach ($pricebreaks as $key => $value) {
                    $pricebreaks[$key]['unit_price'] = $sell_price;
                    $sell_price = $sell_price - $price_step;
                }
            }
            //update
            $arr_save['pricebreaks'] = $pricebreaks;
            $this->opm->save($arr_save);
        }
    }

    function rebuild_images() {
        $products = $this->opm->select_all(array(
                'arr_where' => array(
                        'products_upload' => array(
                            '$exists' => true,
                            '$nin' => array(null,'')
                            )
                    ),
                'arr_field' => array('products_upload')
            ));
        echo $products->count().' products found.<br />';
        $i = 0;
        foreach($products as $product) {
            if(!isset($product['products_upload']) || empty($product['products_upload'])) continue;
            $found = false;
            foreach($product['products_upload'] as $key => $value){
                if(!isset($value['path']) || empty($value['path'])) continue;
                if(strpos($value['path'], '\\') !== false){
                    $i++;
                    $found = true;
                    echo $product['products_upload'][$key]['path'].'<br />';
                    $product['products_upload'][$key]['path'] = str_replace('\\', '/', $product['products_upload'][$key]['path']);
                    echo $product['products_upload'][$key]['path'].'<br />';
                }
            }
            if($found){
                $this->opm->rebuild_collection($product);
            }
        }
        echo $i.' rebuild';
        die;
    }

    function check_exists()
    {
         $arrDuplicate = $this->opm->collection->aggregate(
                array(
                    '$group'=>array(
                                    '_id'=>array(
                                        'code'=> '$code',
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
        $i = 0;
        foreach($arrDuplicate['result'] as $key => $products) {
            foreach($products['uniqueIds'] as $k => $productId) {
                if( $this->opm->count(array('_id' => $productId, 'product_type' => 'not used', 'deleted' => false)) ) {
                    $this->opm->collection->remove(array('_id' => $productId));
                    $i++;
                }
            }
        }
        echo $i;
        die;

    }

    public function recover_missing_products()
    {
        $lastProduct = $this->opm->select_one(array(),array('code'), array('code' => -1));
        $lastCode = (int)$lastProduct['code'];
        $this->Product->arrfield();
        $default_field = $this->Product->arr_temp;
        for($i = 1; $i < $lastCode; $i++) {
            if( !$this->opm->count(array('deleted' => false, 'code' => $i)) ) {
                $product = $default_field;
                $product['code'] = $i;
                $product['product_type'] = 'not used';
                $this->opm->save($product);
                pr($i);
            }
        }
        die;
    }






}
