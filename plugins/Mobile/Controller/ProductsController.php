<?php
class ProductsController extends MobileAppController {
	var $modelName = 'Product';
	var $name = 'Products';
	function beforeFilter() {
		parent::beforeFilter();
		if(!$this->request->is('ajax'))
			$this->set('arr_tabs', array(
								'docs',
								'general'  => array('options_for_this_item'),
			           			'pricing'  => array('pricing_break'),
			           			'purchasing'  => array('purchases','supplier'),
			           			'salesOrder' => array('orders'),
			           			'shipping' =>array('shipping'),
			           			'invoices' => array('invoices'),
			           			));
	}

	public function entry($id = '0', $num_position = -1) {
		$arr_tmp = $this->entry_init($id, $num_position, 'Product', 'products');

		$this->selectModel('Setting');
		
		$product_type = $this->Setting->select_option(array('setting_value' => 'product_type'), array('option'));
		$this->set('product_type', $product_type);

		$product_color = array(''=>'') + $this->Setting->select_option(array('setting_value' => 'product_color'), array('option'));
		$this->set('product_color', $product_color);

		$product_oum_size = array(''=>'') + $this->Setting->select_option(array('setting_value' => 'product_oum_size'), array('option'));
		$this->set('product_oum_size', $product_oum_size);

		$product_sell_by = $this->Setting->select_option(array('setting_value' => 'product_sell_by'), array('option'));
		$this->set('product_sell_by', $product_sell_by);

		if ($arr_tmp['sell_by'] == 'unit')
			$arr_product_oum = $this->Setting->select_option(array('setting_value' => 'product_oum_unit'), array('option'));
		elseif ($arr_tmp['sell_by'] == 'area')
			$arr_product_oum = $this->Setting->select_option(array('setting_value' => 'product_oum_area'), array('option'));
		elseif ($arr_tmp['sell_by'] == 'lengths')
			$arr_product_oum = $this->Setting->select_option(array('setting_value' => 'product_oum_lengths'), array('option'));


		$this->set('arr_product_oum', $arr_product_oum);

        $arr_oum_depend = array('unit'=>'Unit','Sq.ft.'=>'Sq.ft.');
		$this->set('arr_oum_depend', $arr_oum_depend);

		$this->selectModel('Tax');
		$arr_taxtext = $this->Tax->tax_select_list();
		$this->set('arr_taxtext', $arr_taxtext);	

		$product_statuses = $this->Setting->select_option(array('setting_value' => 'product_statuses'), array('option'));
		$this->set('product_statuses', $product_statuses);

		$product_category =  array(''=>'') + $this->Setting->select_option(array('setting_value' => 'product_category'), array('option'));
		asort($product_category);
		$this->set('product_category', $product_category);

		$product_yesno = array(''=>'') + $this->Setting->select_option(array('setting_value' => 'product_yesno'), array('option'));
		$this->set('product_yesno', $product_yesno);

		$this->set('arr_priority', $this->Setting->select_option(array('setting_value' => 'lists_priority'), array('option')));
		$this->set('arr_country', $this->Setting->select_option(array('setting_value' => 'lists_country'), array('option')));

		$arr_tmp1['Product'] = $arr_tmp;
		$this->data = $arr_tmp1;
	}
	public function add() {
		/*if(!$this->check_permission($this->name.'_@_entry_@_add'))
			$this->error_auth();*/
        $this->selectModel('Product');
        $ids = $this->Product->add('name', '');
        $newid = explode("||", $ids);
        $this->Session->write($this->name . 'ViewId', $newid[0]);
        $this->redirect('/mobile/products/entry/' . $this->Product->mongo_id_after_save);
        die;
    }
	public function popup($key = "") {
		$this->set('key', $key);

		$limit = 10; $skip = 0;
		$page_num = 1;
		if( isset($_POST['pagination']) && $_POST['pagination']['page-num'] > 0){
			$page_num = $_POST['pagination']['page-num'];
			$skip = $limit*($page_num - 1);
		}
		$this->set('page_num', $page_num);

		$arr_order = array('no' => 1);
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

		$cond = array();
		if (!empty($this->data)) {
			$arr_post = $this->data['Product'];

			if (strlen($arr_post['name']) > 0)
				$cond['name'] = new MongoRegex('/'. (string)$arr_post['name'] .'/i');
		}

		if (!empty($_GET)) {

			$tmp = $this->data;
			if (isset($_GET['product_type'])) {
				$str = str_replace('"', '', $_GET['product_type']);
				$cond['product_type'] = $str;
				$tmp['Product']['product_type'] = $_GET['product_type'];
			}

			$this->data = $tmp;
		}

		$this->selectModel('Product');
		$arr_products = $this->Product->select_all(array(
			'arr_where' => $cond,
			'arr_order' => $arr_order,
			'arr_field' => array('code', 'sku', 'name', 'product_type', 'company_name', 'category', 'sell_by', 'oum', 'sell_price'),
			'limit' => 10,
			'skip' => $skip
		));
		
		$this->set('arr_products', $arr_products);

		$this->set('limit', 10);
		$total_page = $total_record = $total_current = 0;
		if( is_object($arr_products) ){
			$total_current = $arr_products->count(true);
			$total_record = $arr_products->count();
			if( $total_record%$limit != 0 ){
				$total_page = floor($total_record/$limit) + 1;
			}else{
				$total_page = $total_record/$limit;
			}
		}
		$this->set('total_current', $total_current);
		$this->set('total_page', $total_page);
		$this->set('total_record', $total_record);

		$this->layout = 'ajax';
	}

	function lists( $content_only = '' ) {
		$this->selectModel('Product');
		$limit = 20; $skip = 0;
		// dùng cho sort
		$sort_field = 'code';
		$sort_type = -1;
		if( isset($_POST['sort']) && strlen($_POST['sort']['field']) > 0 ){
			if( $_POST['sort']['type'] == 'desc' ){
				$sort_type = -1;
			}
			$sort_field = $_POST['sort']['field'];
			$this->Session->write('products_lists_search_sort', array($sort_field, $sort_type));

		}elseif( $this->Session->check('products_lists_search_sort') ){
			$session_sort = $this->Session->read('products_lists_search_sort');
			$sort_field = $session_sort[0];
			$sort_type = $session_sort[1];
		}
		$arr_order = array($sort_field => $sort_type);
		$this->set('sort_field', $sort_field);
		$this->set('sort_type', ($sort_type === 1)?'asc':'desc');

		$this->selectModel('Contact');
		$this->set('model_contact', $this->Contact);

		// dùng cho điều kiện
		$cond = array();
		if( $this->Session->check('products_entry_search_cond') ){
			$cond = $this->Session->read('products_entry_search_cond');
		}

		// dùng cho phân trang
		if(isset($_POST['offset'])){
			$skip = $_POST['offset'];
		}
		// query
		$arr_products = $this->Product->select_all(array(
			'arr_where' => $cond,
			'arr_order' => $arr_order,
			'limit' => $limit,
			'skip' => $skip
		));
		$this->set('arr_products', $arr_products);
		if($content_only!=''){
			$this->render("lists_ajax");
		}
	}



	function options_for_this_item(){
		if(isset($_POST['add']))
    		return $this->options_add();
		$ids = $this->get_id();
		$this->selectModel('Product');
		$subdatas = $this->Product->get_data_general($ids);
		$subdatas['productoptions'] = array();
		if($ids!=''){
			$options_data = $this->Product->options_data($ids,true);
			if(isset($options_data['productoptions']))
			$subdatas['productoptions'] = $options_data['productoptions'];
			$option_select_custom['option_group'] = $options_data['custom_option_group'];
			$groupstr = $options_data['groupstr'];
		}
		$this->set('productoptions',$subdatas['productoptions']);

		$arr_data['field'] = array(
           'sku'=>array(
                       'label' => 'SKU',
                       ),
           'name'=>array(
                       'type' => 'text',
                       'label' => 'Name',
                       ),
           'options'=>array(
                       'label' => '',
                       'type' => 'button'),
           'costings'=>array(
                       'label' => '',
                       'type' => 'button'),

           'sell_by'=>array(
                       'label' => 'Sold by',
                       'type' => 'select',
                       'options'=>'product_sell_by'
                       ),
           'oum'=>array(
                       'label' => 'OUM',
                       'type' => 'select',
                       'options' => 'product_oum_area',
                       ),
           'custom_unit_price'=>array(
                       'label' => 'Est. Unit price',
                       'type' => 'text'),
           'unit_price'=>array(
                       'label' => 'Unit price',
                       'type' => 'text'),
           'adj_qty'=>array(
                       'label' => 'Adj Qty'),
           'sub_total'=>array(
                       'label' => 'Sub total'),
           'tax'=>array(
                       'label' => 'Tax'),
           'amount'=>array(
                       'label' => 'Amount'),
           );
		$arr_data['header'] = array(
            'link_to_entry' => M_URL.'/products/entry/',
            'link_to_entry_value' => 'no',
            'info' => 'name'
    	);
    	$this->set('arr_data',$arr_data);
    	$options = array();
    	$this->set('options',$options);
	}

	function options_add(){
		echo 123;die;
	}

	function pricing_break(){
		if(isset($_POST['add']))
    		return $this->add_level();
 		$subdatas = array();
 		$this->selectModel('Product');
        $price_breaks = $this->Product->select_one(array('_id'=>new MongoId($this->get_id())),array('pricebreaks','sell_price'));
        if ($price_breaks['sell_price']=='') $price_breaks['sell_price'] = 0;
        $this->set('sell_price',$price_breaks['sell_price']);
        $this->set('price_breaks',$price_breaks);
	}
	function add_level(){
		echo 123;die;
	}

	function auto_save() {
		if (!empty($this->data)) {
			$id = $this->get_id();
			$arr_return = array();
			$arr_post_data = $this->data['Product'];
			$arr_save = $arr_post_data;
			$arr_save['_id'] = new MongoId($id);
			$this->selectModel('Product');

			if(isset($arr_save['code'])){
				if(!is_numeric($arr_save['code'])){
					echo json_encode(array('status'=>'error','message'=>'must_be_numberic'));
					die;
				}
				$arr_tmp = $this->Product->select_one(array('code' => (int) $arr_save['code'], '_id' => array('$ne' => new MongoId($id))));
				if (isset($arr_tmp['code'])) {
					echo json_encode(array('status'=>'error','message'=>'ref_no_existed'));
					die;
				}
			}

			$date = $this->Common->strtotime($arr_save['date'] . ' 00:00:00');
			$arr_save['date'] = new MongoDate($date);

			
			if (!isset($arr_save['is_custom_size']))
				$arr_save['is_custom_size'] = 0;
			if (!isset($arr_save['assemply_item']))
				$arr_save['assemply_item'] = 0;
			if (!isset($arr_save['approved']))
				$arr_save['approved'] = 0;
			

			if( strlen($arr_save['name']) > 0 )
				$arr_save['name']{0} = strtoupper($arr_save['name']{0});

			$this->selectModel('Product');
			if ($this->Product->save($arr_save))
				$arr_return['status'] = 'ok';
			else
				$arr_return = array('status'=>'error','message'=>'Error: ' . $this->Product->arr_errors_save[1]);
			echo json_encode($arr_return);
		}
		die;
	}

    function purchases(){
    	$id = $this->get_id();
    	$product_id = $this->get_id();

    	if(isset($_POST['add'])) {
    	    echo M_URL.'/purchaseorders/create_pur_from_product/'.$id;//return $this->purchases_add();
    	    die();
    	}
    	$offset = 0;
    	if(isset($_POST['offset']))
    		$offset = $_POST['offset'];
    	
        $this->selectModel('Purchaseorder');
        $arr_purchases = $this->Purchaseorder->select_all(array(
                                                'arr_where' => array(
                                                	'products' => array(
										                    '$elemMatch' => array(
										                        'products_id' => new MongoId($id),
										                        'deleted' => FALSE
										                    )
										                )
                                                ),
                                                'arr_order' => array('purchord_date' => -1),
                                                'arr_field' => array('code','purchord_date','purchase_orders_status','company_name','products'),
                                                'skip' => $offset,
                                                'limit'  => 10
                                                ));
        /*$arr_purchases = iterator_to_array($arr_purchases);
        pr($arr_purchases);die;*/
        $arr_data = array('data'=>array());
        $qty = 0;
		foreach($arr_purchases as $purchase){
			$purchase['_id'] = (string)$purchase['_id'];
			$purchase['purchord_date'] = date('d M, Y', $purchase['purchord_date']->sec);
			$purchase['purchase_orders_status'] = isset($purchase['purchase_orders_status']) ? $purchase['purchase_orders_status'] : '';
			$purchase['company_name'] = isset($purchase['company_name']) ? $purchase['company_name'] : '';
			$purchase['our_rep'] = isset($purchase['our_rep']) ? $purchase['our_rep'] : '';
			$arr_data['data'][] = $purchase;
			$qty = $qty + $purchase['products'][0]['quantity'];
		}
		$this->set('qty',$qty);
        if($this->request->is('ajax')){
        	if($arr_purchases->count(true) == 0){
        		echo json_encode(array('empty'=>true));
        		die;
        	}
			echo json_encode($arr_data);
			die;
        } else {
	        $this->set('arr_purchases',$arr_data['data']);
        	$arr_data['field'] = array(
	                           'code'=>array(
	                                       'label' => 'Ref no',
	                                       'type' => '',),
	                           'purchord_date'=>array(
	                                       'label' => 'Date',
	                                       'type' => ''),
	                           'purchase_orders_status'=>array(
	                                       'label' => 'Status',
	                                       'type' => ''),
	                           'company_name'=>array(
	                                       'label' => 'Company Name',
	                                       'type' => '',
	                                       ),
	                           'our_rep'=>array(
	                                       'label' => 'Our Rep',
	                                       'type' => '',
	                                       ),
	                           );
			$arr_data['header'] = array(
			                        'link_to_entry' => M_URL.'/purchaseorders/entry/',
			                        'link_to_entry_value' => 'code',
			                        'info' => 'heading'
			                            );
			$this->set('arr_data',$arr_data);
			$this->set('id',$id);
			$sum_amount = $this->Purchaseorder->sum('sum_amount', 'tb_purchaseorder' , array(
			                'product_id' => new MongoId($id),
			                'status' => array('$ne' => 'Cancelled'),
			                'deleted' => false
			                ));
			$sum_tax = $this->Purchaseorder->sum('sum_tax', 'tb_purchaseorder' , array(
			                'product_id' => new MongoId($id),
			                'status' => array('$ne' => 'Cancelled'),
			                'deleted' => false
			                ));
			$sum_sub_total = $this->Purchaseorder->sum('_', 'tb_purchaseorder' , array(
			                'product_id' => new MongoId($id),
			                'status' => array('$ne' => 'Cancelled'),
			                'deleted' => false
			                ));
			$this->set('sum_amount',$sum_amount);
			$this->set('sum_tax',$sum_tax);
			$this->set('sum_sub_total',$sum_sub_total);
        }
    }
    function supplier(){
    	$product_id = $this->get_id();
    	$this->selectModel('Product');
    	$query_product = $this->Product->select_one(array('_id' => new MongoId($product_id)));
    	$v_total_average=(float)0;
    	$v_average_plus=(float)0;
    	$v_count_supplier=0;
    	$arr_sup1=array();
    	$arr_sup2=array();
    	//pr($query_product);die;
    	if(isset($query_product['supplier']) && is_array($query_product['supplier']) && count($query_product['supplier'])>0){
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
    	}
    	$arr_supplier = array_merge((array)$arr_sup1, (array)$arr_sup2);
    	if($v_count_supplier!=0)
				$v_average_plus = $v_total_average/$v_count_supplier;
    	//pr($v_total_average);die;
    	$this->set('arr_supplier',$arr_supplier);
    	$this->set('v_average_plus',$v_average_plus);


    	$arr_data['field'] = array(
	                           'code'=>array(
	                                       'label' => 'Ref no',
	                                       'type' => '',),
	                           'purchord_date'=>array(
	                                       'label' => 'Date',
	                                       'type' => ''),
	                           'purchase_orders_status'=>array(
	                                       'label' => 'Status',
	                                       'type' => ''),
	                           );
		$arr_data['header'] = array(
		                        'link_to_entry' => M_URL.'/purchaseorders/entry/',
		                        'link_to_entry_value' => 'code',
		                        'info' => 'heading'
		                            );
		$this->set('arr_data',$arr_data);
	}

    public function orders() {
        $datareturn = $cat_select = array();
        $total = 0;
        $module_id = $this->get_id();
        if (isset($module_id) && strlen($module_id) == 24) {
        	$this->selectModel('Product');
            $arr_set = $this->Product->arr_settings;
            $fieldlist = $arr_set['relationship']['orders']['block']['salesorders']['field'];
            $prokey = array('quantity');
            $this->selectModel('Salesorder');
            $arr_query = $this->Salesorder->select_all(array(
                'arr_where' => array(
                    'products.products_id' => new MongoId($module_id)
                ),
                'arr_order' => array('_id' => -1)
            ));
            $newdata = $temp = array();
            $newdata = iterator_to_array($arr_query);
            foreach ($newdata as $keys => $values) {
                $datareturn[$keys]['_id'] = $keys;
                $datareturn[$keys]['products_id'] = $keys;
                $datareturn[$keys]['code'] = $newdata[$keys]['code'];
                $datareturn[$keys]['our_rep'] = $newdata[$keys]['our_rep'];
                foreach ($fieldlist as $kf => $vf) {
                    if (in_array($kf, $prokey)) {
                        foreach ($values['products'] as $kp => $vp) {
                            $temp = (array) $vp['products_id'];
                            if ($temp['$id'] == $module_id) {
                                $datareturn[$keys][$kf] = $vp[$kf];
                                if ($kf == 'quantity')
                                    $total += (int) $vp[$kf];
                            }
                        }
                    }else if ($kf == 'date_modified') {
                        $temp = (array) $values[$kf];
                        $datareturn[$keys][$kf] = $temp['sec'];
                    } else if (isset($values[$kf]))
                        $datareturn[$keys][$kf] = $values[$kf];
                    else
                        $datareturn[$keys][$kf] = '';
                }
                $datareturn[$keys]['date_modified'] = date('d M, Y',$newdata[$keys]['date_modified']->sec);
            }

            $this->selectModel('Setting');
            $cat_select['status'] = $this->Setting->select_option(array('setting_value' => 'salesorders_status'));
        }

        $this->set('total', $total);
        //pr($datareturn);die;
        $this->set('arr_orders', $datareturn);
        $this->set('option_select', $cat_select);
    }
	public function shipping() {
        $module_id = $this->get_id();
        if($module_id!='') {
			$ship = array();
			$this->selectModel('Shipping');
            $arr_query = $this->Shipping->select_all(array(
                'arr_where' => array(
                    'products.products_id' => new MongoId($module_id)
                ),
				'arr_field' => array('code', 'company_name', 'shipping_type', 'shipping_date', 'contact_name', 'our_rep', 'shipping_status', 'shipper', 'quantity_in', 'quantity_out','products'),

            ));
            $newdata = iterator_to_array($arr_query);
			$total =0; $total_all = array();
			$total_all['quantity_in'] = $total_all['quantity_out'] = 0;

			//loop and caculator
			foreach ($newdata as $keys => $values) {
				$ship[$keys] = $values;
				$ship[$keys]['_id'] = $keys;
				$ship[$keys]['products_id'] = $keys;
				foreach($values['products'] as $kp => $vp){
					 if(isset($vp['deleted']) && !$vp['deleted'] && (string)$vp['products_id'] == $module_id) {
						 if($kp == 'quantity')
						 $total += (float)$vp;
					 }
				}
				$ship[$keys]['shipping_date'] = date('d M, Y',$ship[$keys]['shipping_date']->sec);
				$ship[$keys]['quantity_in'] = $total;
				$ship[$keys]['quantity_out'] = $total;

				$total_all['quantity_in'] += $total;
				$total_all['quantity_out'] += $total;
            }
            //pr($ship);die;
            $this->set('arr_shippings',$ship);
			$this->set('total_all', $total_all);
        }
    }

    public function invoices() {
        $datareturn = $cat_select = array();
        $total = 0;
        $module_id = $this->get_id();
        if (isset($module_id) && strlen($module_id) == 24) {
        	$this->selectModel('Product');
            $arr_set = $this->Product->arr_settings;
            $fieldlist = $arr_set['relationship']['invoices']['block']['salesinvoices']['field'];
            $prokey = array('quantity');
            $this->selectModel('Salesinvoice');
            $arr_query = $this->Salesinvoice->select_all(array(
                'arr_where' => array(
                    'products.products_id' => new MongoId($module_id)
                ),
                'arr_order' => array('_id' => -1)
            ));
            $newdata = $temp = array();
            $newdata = iterator_to_array($arr_query);
            foreach ($newdata as $keys => $values) {
                $datareturn[$keys]['_id'] = $keys;
                $datareturn[$keys]['products_id'] = $keys;
                foreach ($fieldlist as $kf => $vf) {
                    if (in_array($kf, $prokey)) {
                        foreach ($values['products'] as $kp => $vp) {
                            if(!isset($vp['products_id'])) continue;
                            $temp = (array) $vp['products_id'];
                            if ($temp['$id'] == $module_id) {
                                $datareturn[$keys][$kf] = $vp[$kf];
                                if ($kf == 'quantity')
                                    $total += (float) $vp[$kf];
                            }
                        }
                    }else if ($kf == 'date_modified') {
                        $temp = (array) $values[$kf];
                        $datareturn[$keys][$kf] = $temp['sec'];
                    } else if (isset($values[$kf]))
                        $datareturn[$keys][$kf] = $values[$kf];
                    else
                        $datareturn[$keys][$kf] = '';
                }
            }
            $this->selectModel('Setting');
            $cat_select['invoice_status'] = $this->Setting->select_option_vl(array('setting_value' => 'salesinvoices_status'));
        }//end if
        //pr($datareturn);die;
        $this->set('arr_invoices', $datareturn);

        $this->set('total', $total);
        $this->set('option_select', $cat_select);
    }

}