<?php
// Attach lib cal_price
App::import('Vendor','cal_price/cal_price');

App::uses('AppController', 'Controller');
class ShippingsController extends AppController {

	var $name = 'Shippings';
	var $modelName = 'Shipping';
	public $helpers = array();
	public $opm; //Option Module
	public $cal_price; //Option cal_price

	public function beforeFilter(){
		parent::beforeFilter();
		$this->set_module_before_filter('Shipping');

	}

	//Các điều kiện mở/khóa field trong entry
	public function check_lock(){
		if($this->get_id()!=''){
			$arr_tmp = $this->opm->select_one(array('_id' => new MongoId($this->get_id())));
			if(isset($arr_tmp['shipping_status']) && $arr_tmp['shipping_status']!='In progress')
				return true;

		}else
			return false;
	}

	function create_shipping_salesorder($shipping, $shipping_cost){
		$this->selectModel('Salesorder');
		$order = $this->Salesorder->select_one(array('_id' => $shipping['salesorder_id']), array('products','taxval'));
		$product = array();
		if(isset($shipping['shipper_id']) && is_object($shipping['shipper_id'])){
			$this->selectModel('Product');
			$product = $this->Product->select_one(array('company_id' => $shipping['shipper_id'], 'sku' => new MongoRegex('/SHP/')), array('code','sku','name'));
		}
		$new_line = array();
		$new_line['products_id'] = $new_line['product_id'] = isset($product['_id']) ? $product['_id'] : '';
		$new_line['products_name'] = $new_line['product_name'] = isset($product['name']) ? $product['name'] : 'Shipping and Handling';
		$new_line['sku'] = isset($product['sku']) ? $product['sku'] : '';
		$new_line['code'] = isset($product['code']) ? $product['code'] : '';
		$new_line['deleted'] = false;
		$new_line['quantity'] = 1;
		$new_line['sizew'] =  0;
		$new_line['sizew_unit'] = 'in';
		$new_line['sizeh'] = 0;
		$new_line['sizeh_unit'] = 'in';
		$new_line['sell_by'] = 'unit';
		$new_line['oum'] = 'unit';
		$new_line['oum_depend'] = 'unit';
		$new_line['adj_qty'] = 0;
		$new_line['area'] = 0;
		$new_line['perimeter'] = 0;
		$new_line['perimeter'] = 0;
		$new_line['plus_sell_price'] = 0;
		$new_line['plus_unit_price'] = 0;
		$new_line['is_custom_size'] = $shipping_cost;
		$new_line['custom_unit_price'] = $shipping_cost;
		$new_line['sell_price'] = $shipping_cost;
		$new_line['unit_price'] = $shipping_cost;
		$new_line['sub_total'] = $shipping_cost;
		$new_line['taxper'] = $order['taxval'];
		$new_line['tax'] = $new_line['sub_total'] * ($new_line['taxper']/100);
		$new_line['amount'] = $new_line['tax'] + $new_line['sub_total'];
		if(!isset($order['products'] ))
			$order['products']  = array();
		foreach($order['products'] as $key => $value){
			if($value['deleted']) continue;
			if(!isset($value['products_name'])) continue;
			if(strpos($value['products_name'], 'Shipping') !== false){
				$order['products'][$key] = array('deleted' => true);
			}
		}
		$order['products'][] = $new_line;
		$order = array_merge($order, $this->new_cal_sum($order['products']));
		$this->Salesorder->save($order);
	}

	public function rebuild_setting($arr_setting=array()){
		parent::rebuild_setting($arr_setting=array());
		$arr_setting = $this->opm->arr_settings;
		$iditem = $this->get_id();
		if($iditem!=''){
			$query = $this->opm->select_one(array('_id' => new MongoId($iditem)));
			if( (isset($query['salesorder_id']) && strlen((string)$query['salesorder_id'])==24)
			||(isset($query['salesinvoice_id']) && strlen((string)$query['salesinvoice_id'])==24)
			){
				unset($arr_setting['relationship']['line_entry']['block']['products']['field']['quantity']['edit']);
				unset($arr_setting['relationship']['line_entry']['block']['products']['field']['shipped']['edit']);
				unset($arr_setting['relationship']['line_entry']['block']['products']['field']['balance_shipped']['edit']);
				unset($arr_setting['relationship']['line_entry']['block']['products']['add']);
				unset($arr_setting['relationship']['line_entry']['block']['products']['delete']);
				unset($arr_setting['relationship']['line_entry']['block']['products']['field']['code']['edit']);


				unset($arr_setting['relationship']['text_entry']['block']['products']['field']['quantity']['edit']);
				unset($arr_setting['relationship']['text_entry']['block']['products']['field']['shipped']['edit']);
				unset($arr_setting['relationship']['text_entry']['block']['products']['field']['balance_shipped']['edit']);
				unset($arr_setting['relationship']['text_entry']['block']['products']['add']);
				unset($arr_setting['relationship']['text_entry']['block']['products']['delete']);
			}
		}

		$this->opm->arr_settings = $arr_setting;
	}

	public function entry(){
		$mod_lock = '0';
		if($this->check_lock()){
			$this->opm->set_lock(array('shipping_status'),'out');
			$mod_lock = '1';
			$this->set('address_lock','1');
			$this->opm->set_lock_option('line_entry','products');
			$this->opm->set_lock_option('text_entry','products');

		}

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
						if(in_array($field,$arr_set['title_field']))
							$item_title[$field] = $arr_tmp[$field];

						if(preg_match("/_date$/",$field) && is_object($arr_tmp[$field]))
							$arr_set['field'][$ks][$field]['default'] = date('m/d/Y',$arr_tmp[$field]->sec);
						//chế độ lock, hiện name của các relationship custom
						else if(($field=='company_name' || $field=='contact_name') && $mod_lock=='1')
							$arr_set['field'][$ks][$field]['default'] = $arr_tmp[$field];

						else if($this->opm->check_field_link($ks,$field)){
							$field_id = $arr_set['field'][$ks][$field]['id'];
							if(!isset($arr_set['field'][$ks][$field]['syncname']))
								$arr_set['field'][$ks][$field]['syncname'] = 'name';
							$arr_set['field'][$ks][$field]['default'] = $this->get_name($this->ModuleName($arr_set['field'][$ks][$field]['cls']),$arr_tmp[$field_id],$arr_set['field'][$ks][$field]['syncname']);

						}else if($field=='company_name' && isset($arr_tmp['company_id']) && $arr_tmp['company_id']!=''){
							$arr_set['field'][$ks][$field]['default'] = $this->get_name('Company',$arr_tmp['company_id']);

						}else if($field=='contact_name' && isset($arr_tmp['contact_id']) && $arr_tmp['contact_id']!=''){
							$arr_set['field'][$ks][$field]['default'] = $this->get_name('Contact',$arr_tmp['contact_id']);
							$item_title[$field] = $this->get_name('Contact',$arr_tmp['contact_id']);
						}
						else if($field=='shipper' && isset($arr_tmp['shipper_id']) && $arr_tmp['shipper_id']!=''){
							$arr_set['field'][$ks][$field]['default'] = $this->get_name('Shipper',$arr_tmp['shipper_id']);
						}
					}
				}
			}
			$arr_set['field']['panel_1']['mongo_id']['default'] = $iditem;
			$this->Session->write($this->name.'ViewId',$iditem);

			//BEGIN custom
			if(isset($arr_set['field']['panel_1']['code']['default']))
				$item_title['code'] = $arr_set['field']['panel_1']['code']['default'];
			else
				$item_title['code'] = '1';
			if(isset($arr_tmp['shipping_type']))
				$arr_set['field']['panel_1']['code']['after'] = "<div class=\"jttype\">Type: <span class=\"bold\">".$arr_tmp['shipping_type']."</span></div>";
			if(isset($arr_tmp['shipping_type']) && $arr_tmp['shipping_type'] =='In')
				$item_title['shipping_type'] = 'Incoming <span class="inoutgoing"><-</span>';
			else if(isset($arr_tmp['shipping_type']) && $arr_tmp['shipping_type'] =='Out')
				$item_title['shipping_type'] = 'Outgoing <span class="inoutgoing">-></span>';
			else
				$item_title['shipping_type'] ='';
			$this->set('item_title',$item_title);

			//END custom

			//show footer info
			$this->show_footer_info($arr_tmp);


		//add, setup field tự tăng
		}else{
			$this->redirect(URL.'/shippings/add');
		}
		$this->set('arr_settings',$arr_set);
		$this->sub_tab_default = 'line_entry';
		$this->sub_tab('',$iditem);
		$this->set_entry_address($arr_tmp,$arr_set);

		parent::entry();
	}

	public function delete_all_associate($idopt='',$key=''){
		if($key=='products'){ // update cac line entry option cua products
			if( $this->check_permission($this->name.'_@_entry_@_delete') ){
				$ids = $this->get_id();
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
					$this->opm->save($arr_insert);
				}
			}
		}

	}
	public function arr_associated_data($field = '', $value = '', $valueid = '' , $fieldopt='') {

		$arr_return[$field]=$value;
		/**
		* Chọn Company
		*/
		if ($field == 'shipper' && $valueid != '') {
			$arr_return['shipper_id'] = new MongoId($valueid);
			$this->selectModel('Company');
			$arr_company = $this->Company->select_one(array('_id'=>new MongoId($valueid)));
			if(isset($arr_company['tracking_url'])&&$arr_company['tracking_url']!='')
				$arr_return['web_tracker']=$arr_company['tracking_url'];
		}
		if ($field == 'signed_by_detail' && $valueid != '') {
			$arr_return['signed_by_detail_id'] = new MongoId($valueid);
		}
		if ($field == 'tracking_no' && $valueid != '') {

			$arr_return['tracking_no'] =$value.' ';
		}
		if($field == 'company_name' && $valueid !=''){
			$arr_return = array(
				'company_name'	=>'',
				'company_id'	=>'',
				'contact_name'	=>'',
				'contact_id'	=>'',
				'our_csr'		=>'',
				'our_csr_id'	=>'',
				'our_rep'		=>'',
				'our_rep_id'	=>'',
				'phone'			=>'',
				'email'			=>'',
				'invoice_address' => array(),
				'shipping_address' => array(),
			);
			//change company
			$arr_return['company_name'] = $value;
			$arr_return['company_id'] = new MongoId($valueid);
			$query = $this->opm->select_one(array('_id'=>new MongoId($this->get_id())),array('code'));
			$arr_return['name'] = $query['code'].' - '. $value;
			//find contact and more from Company
			$this->selectModel('Company');
			$arr_company = $this->Company->select_one(array('_id'=>new MongoId($valueid)));

			$this->selectModel('Contact');
			$arr_contact = $arrtemp = array();
			// is set contact_default_id
			if(isset($arr_company['contact_default_id']) && is_object($arr_company['contact_default_id'])){
				$arr_contact = $this->Contact->select_one(array('_id'=>$arr_company['contact_default_id']));

			// not set contact_default_id
			}else{
				$arr_contact = $this->Contact->select_all(array(
					'arr_where' => array('company_id'=>new MongoId($valueid)),
					'arr_order' => array('_id'=>-1),
				));
				$arrtemp = iterator_to_array($arr_contact);
				if(count($arrtemp)>0){
					$arr_contact = current($arrtemp);
				}else
					$arr_contact = array();
			}
			//change contact
			if(isset($arr_contact['_id']))
			{
				$arr_return['contact_name']=$arr_contact['first_name'].' '.$arr_contact['last_name'];
				$arr_return['contact_id'] = $arr_contact['_id'];
			}
			else
			{
				$arr_return['contact_name']='';
				$arr_return['contact_id'] = '';
			}




			//change our_csr
			if(isset($arr_company['our_csr']) && isset($arr_company['our_csr_id']) && $arr_company['our_csr_id']!=''){
				$arr_return['our_csr_id'] = $arr_company['our_csr_id'];
				$arr_return['our_csr'] = $arr_company['our_csr'];
			}else{
				$arr_return['our_csr_id'] = $this->opm->user_id();
				$arr_return['our_csr'] = $this->opm->user_name();
			}


			//change our_rep
			if(isset($arr_company['our_responsible']) && isset($arr_company['our_responsible_id']) && $arr_company['our_responsible_id']!=''){
				$arr_return['our_rep_id'] = $arr_company['our_responsible_id'];
				$arr_return['our_rep'] = $arr_company['our_responsible'];
			}else{
				$arr_return['our_rep_id'] = $this->opm->user_id();
				$arr_return['our_rep'] = $this->opm->user_name();
			}

			//change our_rep
			if(isset($arr_company['our_rep']) && isset($arr_company['our_rep_id']) && $arr_company['our_rep_id']!=''){
				$arr_return['our_rep_id'] = $arr_company['our_rep_id'];
				$arr_return['our_rep'] = $arr_company['our_rep'];
			}else{
				$arr_return['our_rep_id'] = $this->opm->user_id();
				$arr_return['our_rep'] = $this->opm->user_name();
			}


			//change phone
			if(isset($arr_company['phone']))
				$arr_return['phone'] = $arr_company['phone'];
			else  // neu khong co phone thi lay phone cua contact mac dinh
			{
				if(isset($arr_contact['direct_dial']))
					$arr_return['phone']=$arr_contact['direct_dial'];
				elseif(!isset($arr_contact['direct_dial'])&&isset($arr_contact['mobile']))
					$arr_return['phone']=$arr_contact['mobile'];
				elseif(!isset($arr_contact['direct_dial'])&&!isset($arr_contact['mobile']))
					$arr_return['phone']='';  //bat buoc phai co dong nay khong thi no se lay du lieu cua cty truoc
			}


			if(isset($arr_company['email']))
				$arr_return['email'] = $arr_company['email'];
			elseif (isset($arr_contact['email']))
				$arr_return['email']=$arr_contact['email'];
			elseif  (!isset($arr_contact['email']))
				$arr_return['email']='';

			if(isset($arr_company['fax']))
				$arr_return['fax'] = $arr_company['fax'];
			elseif (isset($arr_contact['fax']))
				$arr_return['fax']=$arr_contact['fax'];
			elseif  (!isset($arr_contact['fax']))
				$arr_return['fax']='';


			//change address
			if(isset($arr_company['addresses_default_key']))
			{
				$add_default = $arr_company['addresses_default_key'];
				$arr_return['addresses_default_key']= $arr_company['addresses_default_key'];
			}

			if(isset($add_default) && isset($arr_company['addresses'][$add_default])){
				foreach($arr_company['addresses'][$add_default] as $ka=>$va){
					if($ka!='deleted')
						$arr_return['invoice_address'][0]['invoice_'.$ka] = $va;
					else
						$arr_return['invoice_address'][0][$ka] = $va;
				}
			}




		/**
		* Chọn Contact
		*/
		}else if($field == 'contact_name' && $valueid !=''){
			$arr_return = array(
				'contact_name'	=>'',
				'contact_id'	=>'',
				'phone'			=>'',
				'email'			=>'',
			);
			//change company
			$arr_return['contact_name'] = $value;
			$arr_return['contact_id'] = new MongoId($valueid);
			//find more from contact
			$this->selectModel('Contact');
			$arr_contact = $this->Contact->select_one(array('_id'=>new MongoId($valueid)));
			//change phone
			if(isset($arr_contact['direct_dial']) && $arr_contact['direct_dial']!='')
				$arr_return['phone'] = $arr_contact['direct_dial'];
			//change email
			if(isset($arr_contact['email']))
				$arr_return['email'] = $arr_contact['email'];
			//nếu company khác hiện có
			if(isset($arr_contact['company_id'])){
				echo '';
			}

		}else if($field == 'products'){

			if(isset($value[$valueid]) && isset($value[$valueid]['products_id']) && is_object($value[$valueid]['products_id']) && $fieldopt!='code' && $fieldopt!='deleted'){
				//change size other


			//giam gia cho product parrent neu la xoa item option
			}else if(isset($value[$valueid]) && isset($value[$valueid]['products_id']) && is_object($value[$valueid]['products_id']) && $fieldopt=='deleted'){
				$vv = $value[$valueid];

				if(isset($vv['option_for']) && $vv['option_for']!='' && isset($vv['same_parent']) && $vv['same_parent']==1 && isset($value[$vv['option_for']])){
					$query = $this->opm->select_one(array('_id' => new MongoId($this->get_id())),array('company_id'));
					$option_for = $vv['option_for'];
					if(!isset($query['company_id']))
						$query['company_id'] = '';

					$result = array();
					$arr_plus_temp = $value[$option_for];
					//tinh gia theo price list
					$arr_plus_temp['plus_sell_price'] = 0;
					$cal_price = new cal_price;
					$cal_price->arr_product_items = $arr_plus_temp;
					$result = $this->change_sell_price_company($query['company_id'],$vv['products_id']);
					$cal_price->price_break_from_to = $result;
					$cal_price->field_change = '';
					$arr_plus_temp = $cal_price->cal_price_items();

					//loai bo gia option
					$value[$option_for]['sell_price'] -= (float)$arr_plus_temp['sell_price'];
					$value[$option_for]['plus_sell_price'] -= (float)$arr_plus_temp['sell_price'];
					//tinh lai unit price
					$cal_price2 = new cal_price;
					$cal_price2->arr_product_items = $value[$option_for];
					$cal_price2->field_change = 'sell_price';
					$value[$option_for] = $cal_price2->cal_price_items();

				}
				$value[$valueid]['deleted'] = true;
				//pr($value);die;



			//truong hop thay Code
			}else if(isset($value[$valueid]) && isset($value[$valueid]['products_id']) && is_object($value[$valueid]['products_id']) && $fieldopt=='code'){
                $query = $this->opm->select_one(array('_id' => new MongoId($this->get_id())),array('products','options','company_id'));
				//remove cac option cu cua $valueid
				foreach($value as $kks=>$vvs){
					if(isset($vvs['option_for']) && $vvs['option_for']==$valueid)
					   $value[$kks] = array('deleted' => true);
				}
				if(isset($query['options']))
	                foreach($query['options'] as $options_key=>$options){
	                    if(isset($options['parent_line_no']) && $options['parent_line_no']==$valueid)
	                        $query['options'][$options_key] = array('deleted' => true);
	                }

				//tim data option cua product
				$this->selectModel('Product');
				$parent = $this->Product->select_one(array('_id'=>$value[$valueid]['products_id']));
				if(isset($parent['sku']))
					$value[$valueid]['sku'] = $parent['sku'];
				else
					$value[$valueid]['sku'] = '';

				//lay danh sach option va luu lai
				$products = $this->Product->options_data((string)$value[$valueid]['products_id']);
				if(isset($products['productoptions']) && is_array($products['productoptions']) && count($products['productoptions'])>0){
                    $total_sub_total = 0;
                    if(!isset($query['options']))
                        $query['options']= array();
                    $arr_return['options'] = $query['options'];
                    $options_num = count($arr_return['options']);
                    $line_num = count($value);
					foreach($products['productoptions'] as $kk=>$vv){
						//loop va tao moi items
						$new_array = array();
						$new_array['code'] 			= $vv['code'];
						$new_array['sku'] 			= $vv['sku'];
                        $new_array['products_name'] = $vv['product_name'];
						$new_array['product_name'] = $vv['product_name'];
                        $new_array['products_id']   = $vv['product_id'];
						$new_array['product_id'] 	= $vv['product_id'];
						$new_array['quantity'] 		= $vv['quantity'];
						$new_array['sub_total'] 	= $vv['sub_total'];
                        $new_array['option_group']  = (isset($vv['option_group']) ? $vv['option_group'] : '');
						if(isset($value[$valueid]['sizew']))
							$new_array['sizew'] 		= $value[$valueid]['sizew'];
						else
							$new_array['sizew'] 		= $vv['sizew'];

						if(isset($value[$valueid]['sizew_unit']))
							$new_array['sizew_unit'] 		= $value[$valueid]['sizew_unit'];
						else
							$new_array['sizew_unit'] 		= $vv['sizew_unit'];

						if(isset($value[$valueid]['sizeh']))
							$new_array['sizeh'] 		= $value[$valueid]['sizeh'];
						else
							$new_array['sizeh'] 		= $vv['sizeh'];

						if(isset($value[$valueid]['sizeh_unit']))
							$new_array['sizeh_unit'] 		= $value[$valueid]['sizeh_unit'];
						else
							$new_array['sizeh_unit'] 		= $vv['sizeh_unit'];


						$new_array['sell_by'] 		= $vv['sell_by'];
						$new_array['oum'] 		= $vv['oum'];

						if(isset($vv['same_parent']))
							$new_array['same_parent'] 	= (int)$vv['same_parent'];
						else
							$new_array['same_parent'] 	= 0;
						$more_discount 				= (float)$vv['unit_price']*((float)$vv['discount']/100);
						$new_array['sell_price'] 	= (float)$vv['unit_price'] - $more_discount;

						$new_array['taxper'] 		= (isset($value[$valueid]['taxper']) ? (float)$value[$valueid]['taxper'] : 0);
						$new_array['tax'] 			= $value[$valueid]['tax'];
						$new_array['option_for'] 	= $valueid;
						$new_array['deleted'] 		= false;
						$new_array['proids'] 		= $value[$valueid]['products_id'].'_'.$kk;

						$this->cal_price = new cal_price;
						//truyen data vao cal_price de tinh gia
						$this->cal_price->arr_product_items = $new_array;
						//lay thong tin khach hang de tinh chiec khau/giam gia
						$result = array();
						if(!isset($query['company_id']))
							$query['company_id'] = '';
						if(isset($new_array['products_id']))
							$result = $this->change_sell_price_company($query['company_id'],$new_array['products_id']);
						//truyen bang chiec khau va gia giam vao
						$this->cal_price->price_break_from_to = $result;
						//kiem tra field nao dang thay doi
						$this->cal_price->field_change = '';
						//chay tinh gia
						$arr_ret = $this->cal_price->cal_price_items();
                        //
                        if(isset($vv['line_no']))
                        	unset($vv['line_no']);
                        $arr_return['options'][$options_num] = $vv;
                        $arr_return['options'][$options_num]['this_line_no'] = $options_num;
                        $arr_return['options'][$options_num]['parent_line_no'] = $valueid;
                        $arr_return['options'][$options_num]['choice'] = 0;
                        if(isset($vv['require']) && (int)$vv['require']==1){
							$value[$line_num] = array_merge((array)$new_array,(array)$arr_ret);
                            $arr_return['options'][$options_num]['line_no'] = $line_num;
                            $arr_return['options'][$options_num]['choice'] = 1;
                            $line_num++;
                        }
                        $options_num++;
					}
				    $query['options'] = $arr_return['options'];
                    $query['products'] = $value;
                    //=============================================
                    $this->opm->save($query);
                    echo $valueid;
                    die;
                }
			}
			$arr_return[$field] = $value;
		}else if($field == 'salesorder'){
			if(strlen($valueid) == 24){
				$this->selectModel('Salesorder');
				$order = $this->Salesorder->select_one(array('_id' => new MongoId($valueid)),array('code','name'));

				$arr_return['salesorder_name'] = $order['name'];
				$arr_return['salesorder_number'] = $order['code'];
				$arr_return['salesorder_id'] = $order['_id'];
			}
		}


		return $arr_return;
    }


	public function entry_search(){
		//parent class
		$arr_set = $this->opm->arr_settings;
		$arr_set['field']['panel_1']['code']['lock'] = '';
        $arr_set['field']['panel_4']['shipping_status']['default'] = '';
		$arr_set['field']['panel_1']['quotation_type']['element_input'] = '';
		$arr_set['field']['panel_1']['our_rep']['not_custom'] = '0';
		$arr_set['field']['panel_1']['our_csr']['not_custom'] = '0';
		$arr_set['field']['panel_4']['job_name']['not_custom'] = '0';
		$arr_set['field']['panel_4']['job_number']['lock'] = '0';
		$arr_set['field']['panel_4']['salesorder_name']['not_custom'] = '0';
		$arr_set['field']['panel_4']['salesorder_number']['lock'] = '0';
		$arr_set['field']['panel_1']['quotation_type']['default'] = '';
		$arr_set['field']['panel_1']['quotation_date']['default'] = '';
		$arr_set['field']['panel_4']['quotation_status']['default'] = '';
		$arr_set['field']['panel_4']['payment_due_date']['default'] = '';
		$arr_set['field']['panel_4']['payment_terms']['default'] = '';
		$arr_set['field']['panel_4']['tax']['default'] = '';
		$arr_set['field']['panel_4']['taxval']['default'] = '';
		$arr_set['field']['panel_4']['shipping_cost']['default'] = '';

		$this->set('search_class','jt_input_search');
		$this->set('search_class2','jt_select_search');
		$this->set('search_flat','placeholder="1"');
		$where = array();
		if($this->Session->check($this->name.'_where'))
			$where = $this->Session->read($this->name.'_where');
		if(count($where)>0){
			foreach($arr_set['field'] as $ks => $vls){
				foreach($vls as $field => $values){
					if(isset($where[$field])){
						$arr_set['field'][$ks][$field]['default'] = $where[$field]['values'];
					}
				}
			}
		}
		//end parent class
		$this->set('arr_settings',$arr_set);
        $this->set('shipping_contact_name','');

		//set address
		$address_label = array('Shipping address','Invoice Address');
		$this->set('address_label',$address_label);
		$address_conner[0]['top'] 		= 'hgt fixbor';
		$address_conner[0]['bottom'] 	= 'fixbor2 jt_ppbot';
		$address_conner[1]['top'] 		= 'hgt';
		$address_conner[1]['bottom'] 	= 'fixbor3 jt_ppbot';
		$this->set('address_conner',$address_conner);
		$this->set('address_more_line',2);//set
		$address_hidden_field = array('shipping_address','invoice_address');
		$this->set('address_hidden_field',$address_hidden_field);//set
		$address_country = $this->country();
		$this->set('address_country',$address_country);//set
        $this->set('address_country_id', ''); //set
		$address_province['invoice'] = $address_province['shipping'] = $this->province("CA");
		$this->set('address_province',"");//set
		$this->set('address_province_id',"");//set
		$this->set('address_onchange',"save_address_pr('\"+keys+\"');");
		$address_hidden_value=array('','');
		$this->set('address_hidden_value',$address_hidden_value);
		$this->set('address_mode','search');
	}



	// Options list
	public function swith_options($keys) {
        parent::swith_options($keys);
	 	if ($keys == 'in_porgress')
	 	{
            $arr_where['shipping_status'] = array('values' => 'In progress', 'operator' => '=');
            $this->Session->write($this->name . '_where', $arr_where);
            echo URL . '/' . $this->params->params['controller'] . '/lists';
        }
        else if ($keys == 'complete')
	 	{
            $arr_where['shipping_status'] = array('values' => 'Completed', 'operator' => '=');
            $this->Session->write($this->name . '_where', $arr_where);
            echo URL . '/' . $this->params->params['controller'] . '/lists';
        }
        else if ($keys == 'cancelled')
	 	{
            $arr_where['shipping_status'] = array('values' => 'Cancelled', 'operator' => '=');
            $this->Session->write($this->name . '_where', $arr_where);
            echo URL . '/' . $this->params->params['controller'] . '/lists';
        }
        else if ($keys == 'outstanding') {
            $or_where = array(
                array("quotation_status" => 'Approved'),
                array("quotation_status" => 'Rejected'),
                array("quotation_status" => 'Cancelled')
            );
            $arr_where = array();
            $arr_where[] = array('values' => $or_where, 'operator' => 'or');
            $this->Session->write($this->name . '_where', $arr_where);
            echo URL . '/' . $this->params->params['controller'] . '/lists';
        }
        else if ($keys == 'existing')
            echo URL . '/' . $this->params->params['controller'] . '/entry';
        else if ($keys == 'under')
            echo URL . '/' . $this->params->params['controller'] . '/entry';
        else if ($keys == 'report_by_customer_summary')
            echo URL . '/' . $this->params->params['controller'] . '/option_summary_customer_find';
        else if ($keys == 'report_by_customer_detailed')
            echo URL . '/' . $this->params->params['controller'] . '/option_detailed_customer_find';
        else if ($keys == 'report_by_customer_sale_detailed')
            echo URL . '/' . $this->params->params['controller'] . '/option_detailed_customer_find/sale';
        else if ($keys == 'report_by_area_summary')
            echo URL . '/' . $this->params->params['controller'] . '/option_summary_customer_find/area';
        else if ($keys == 'report_by_area_detailed')
            echo URL . '/' . $this->params->params['controller'] . '/option_detailed_customer_find/area';
        else if ($keys == 'report_by_product_summary')
            echo URL . '/' . $this->params->params['controller'] . '/option_summary_product_find';
        else if ($keys == 'report_by_product_detailed')
            echo URL . '/' . $this->params->params['controller'] . '/option_detailed_product_find';
        else if ($keys == 'report_by_category_detailed')
            echo URL . '/' . $this->params->params['controller'] . '/option_detailed_product_find/category';
        else if ($keys == 'report_by_category_summary')
            echo URL . '/' . $this->params->params['controller'] . '/option_summary_product_find/category';
        else if ($keys == 'print_mini_list')
	        echo URL . '/' . $this->params->params['controller'] . '/view_minilist';
        else if ($keys == 'print_shipping_note')
	        echo URL . '/' . $this->params->params['controller'] . '/view_shipping_note';
        else if ($keys == 'email_shipping_note')
	        echo URL . '/' . $this->params->params['controller'] . '/email_shipping_note';
	    else if ($keys == 'create_email')
            echo URL . '/' . $this->params->params['controller'] . '/add_from_module/'.$this->get_id().'/Email';
        else if ($keys == 'create_letter')
            echo URL . '/' . $this->params->params['controller'] . '/add_from_module/'.$this->get_id().'/Letter';
        else if ($keys == 'create_fax')
            echo URL . '/' . $this->params->params['controller'] . '/add_from_module/'.$this->get_id().'/Fax';



        else
            echo '';
        die;
    }

	var $is_text = 0;

	public function email_shipping_note(){
		$this->layout = 'pdf';
		$ids = $this->get_id();
		if($ids!=''){
			$query = $this->opm->select_one(array('_id' =>new MongoId($ids)));
			$arrtemp = $query;
			//set header
			$this->set('logo_link','img/logo_anvy.jpg');
			$this->set('company_address','3145 - 5th Ave NE<br />Calgary  AB  T2A  6A3<br />');

			//customer address
			$customer = '';
			if(isset($arrtemp['company_id']) && strlen($arrtemp['company_id'])==24)
				$customer .= '<b>'.$this->get_name('Company',$arrtemp['company_id']).'</b><br />';
			else if(isset($arrtemp['company_name']))
				$customer .= '<b>'.$arrtemp['company_name'].'</b><br />';
			if(isset($arrtemp['contact_id']) && strlen($arrtemp['contact_id'])==24)
				$customer .= $this->get_name('Contact',$arrtemp['contact_id']).'<br />';
			else if(isset($arrtemp['contact_name']))
				$customer .= $arrtemp['contact_name'].'<br />';

			//loop 2 address
			$arradd = array('invoice','shipping');
			foreach($arradd as $vvs){
				$kk = $vvs; $customer_address = '';
				if(isset($arrtemp[$kk.'_address']) && isset($arrtemp[$kk.'_address'][0]) && count($arrtemp[$kk.'_address'])>0){
					$temp = $arrtemp[$kk.'_address'][0];
					if(isset($temp[$kk.'_address_1']) && $temp[$kk.'_address_1']!='')
						$customer_address .= $temp[$kk.'_address_1'].' ';
					if(isset($temp[$kk.'_address_2']) && $temp[$kk.'_address_2']!='')
						$customer_address .= $temp[$kk.'_address_2'].' ';
					if(isset($temp[$kk.'_address_3']) && $temp[$kk.'_address_3']!='')
						$customer_address .= $temp[$kk.'_address_3'].'<br />';
					else
						$customer_address .= '<br />';
					if(isset($temp[$kk.'_town_city']) && $temp[$kk.'_town_city']!='')
						$customer_address .= $temp[$kk.'_town_city'];

					if(isset($temp[$kk.'_province_state']))
						$customer_address .= ' '.$temp[$kk.'_province_state'].' ';
					else if(isset($temp[$kk.'_province_state_id']) && isset($temp[$kk.'_country_id'])){
						$keytemp = $temp[$kk.'_province_state_id'];
						$provkey = $this->province($temp[$kk.'_country_id']);
						if(isset($provkey[$temp]))
							$customer_address .= ' '.$provkey[$temp].' ';
					}


					if(isset($temp[$kk.'_zip_postcode']) && $temp[$kk.'_zip_postcode']!='')
						$customer_address .= $temp[$kk.'_zip_postcode'];

					if(isset($temp[$kk.'_country']) && isset($temp[$kk.'_country_id']) && (int)$temp[$kk.'_country_id']!="CA")
						$customer_address .= ' '.$temp[$kk.'_country'].'<br />';
					else
						$customer_address .= '<br />';
					$arr_address[$kk] = $customer_address;
				}
			}

			if(isset($arrtemp['name']) && $arrtemp['name']!='')
				$heading = $arrtemp['name'];
			else
				$heading = '';
			if(!isset($arr_address['invoice']))
				$arr_address['invoice'] = '';
			$this->set('customer_address',$customer.$arr_address['invoice']);
			if(!isset($arr_address['shipping']))
				$arr_address['shipping'] = '';
			$this->set('shipping_address',$arr_address['shipping']);

			// info data
			$info_data = (object) array();
			$info_data->contact_name = $arrtemp['contact_name'];
			$info_data->no = $arrtemp['code'];
			$info_data->job_no =  isset($arrtemp['job_number'])?$arrtemp['job_number']:'';
			$info_data->date = (isset($arrtemp['invoice_date'])&&$arrtemp['invoice_date']!='' ? $this->opm->format_date($arrtemp['invoice_date']) : '');
			$info_data->po_no = (isset($arrtemp['customer_po_no']) ? $arrtemp['customer_po_no'] : '');
			$info_data->ac_no = '';
			$info_data->terms = (isset($arrtemp['payment_terms']) ? $arrtemp['payment_terms'] : '');
			$info_data->required_date = (isset($arrtemp['payment_due_date'])&&$arrtemp['payment_due_date']!='' ? $this->opm->format_date($arrtemp['payment_due_date']) : '');
			$this->set('info_data', $info_data);


			//$this->set('quote_date',$this->opm->format_date($arrtemp['quotation_date']));
			/**Nội dung bảng giá */
			$date_now = date('Ymd');
			$time=time();
			$filename = 'SN'.$date_now.$time.'-'.$info_data->no;

			$thisfolder = 'upload'.DS.date("Y_m");
			$thisfolder_1='upload'.','.date("Y_m");

			$folder = ROOT.DS.APP_DIR.DS.WEBROOT_DIR.DS.$thisfolder;
			if (!file_exists($folder)) {
				mkdir($folder, 0777, true);
			}

			$this->set('filename', $filename);
			$this->set('heading',$heading);
			$html_cont = '';
			if(isset($arrtemp['products']) && is_array($arrtemp['products']) && count($arrtemp['products'])>0){
				$line = 0; $colum = 7;
				foreach($arrtemp['products'] as $keys=>$values){
					if(!$values['deleted']){
						if($line%2==0)
							$bgs = '#fdfcfa';
						else
							$bgs = '#eeeeee';
						//code
						$html_cont .= '<tr style="background-color:'.$bgs.';"><td class="first">';
						if(isset($values['code']))
							$html_cont .= '  '.$values['code'];
						else
							$html_cont .= '  #'.$keys;
						//desription
						$html_cont .= '</td><td>';
						if(isset($values['products_name']))
							$html_cont .= str_replace("\n","<br />",$values['products_name']);
						else
							$html_cont .= 'Empty';
						//width
						$html_cont .= '</td><td align="right">';
						if(isset($values['sizew']) && $values['sizew']!='' && isset($values['sizew_unit']) && $values['sizew_unit']!='')
							$html_cont .= $values['sizew'].' ('.$values['sizew_unit'].')';
						else if(isset($values['sizew'])&& $values['sizew']!='')
							$html_cont .= $values['sizew'].' (in.)';
						else
							$html_cont .= '';
						//height
						$html_cont .= '</td><td align="right">';
						if(isset($values['sizeh']) && $values['sizeh']!='' && isset($values['sizeh_unit']) && $values['sizeh_unit']!='')
							$html_cont .= $values['sizeh'].' ('.$values['sizeh_unit'].')';
						else if(isset($values['sizeh']) && $values['sizeh']!='' )
							$html_cont .= $values['sizeh'].' (in.)';
						else
							$html_cont .= '';
						//Unit price
						$html_cont .= '</td><td align="right">';
						if(isset($values['unit_price']))
							$html_cont .= $this->opm->format_currency($values['unit_price']);
						else
							$html_cont .= '0.00';
						//Qty
						$html_cont .= '</td><td align="right">';
						if(isset($values['quantity']))
							$html_cont .= $values['quantity'];
						else
							$html_cont .= '';
						//line total
						$html_cont .= '</td><td align="right" class="end">';
						if(isset($values['sub_total']))
							$html_cont .= $this->opm->format_currency($values['sub_total']);
						else
							$html_cont .= '';


						$html_cont .= '</td></tr>';
						$line++;
					}//end if deleted
				}//end for


				if($line%2==0){
					$bgs = '#fdfcfa';$bgs2 = '#eeeeee';
				}else{
					$bgs = '#eeeeee';$bgs2 = '#fdfcfa';
				}

				$sub_total = $total = $taxtotal = 0.00;
				if(isset($arrtemp['sum_sub_total']))
					$sub_total = $arrtemp['sum_sub_total'];
				if(isset($arrtemp['sum_tax']))
					$taxtotal = $arrtemp['sum_tax'];
				if(isset($arrtemp['sum_amount']))
					$total = $arrtemp['sum_amount'];
				//Sub Total
				$html_cont .= '<tr style="background-color:'.$bgs.';">
									<td colspan="'.($colum-1).'" align="right" style="font-weight:bold;border-top:2px solid #aaa;" class="first">Sub Total:</td>
									<td align="right" style="border-top:2px solid #aaa;" class="end">'.$this->opm->format_currency($sub_total).'</td>
							   </tr>';
				//GST
				$html_cont .= '<tr style="background-color:'.$bgs2.';">
									<td colspan="'.($colum-1).'" align="right" style="font-weight:bold;" class="first">HST/GST:</td>
									<td align="right" class="end">'.$this->opm->format_currency($taxtotal).'</td>
							   </tr>';
				//Total
				$html_cont .= '<tr style="background-color:'.$bgs.';">
									<td colspan="'.($colum-1).'" align="right" style="font-weight:bold;" class="first bottom">Total:</td>
									<td align="right" class="end bottom">'.$this->opm->format_currency($total).'</td>
							   </tr>';

			}//end if


			$this->set('html_cont',$html_cont);
			if(isset($arrtemp['our_csr'])){
				$this->set('user_name',' '.$arrtemp['our_csr']);
			}else
				$this->set('user_name',' '.$this->opm->user_name());
			//end set content

			//set footer

			$this->set('link_this_folder',$thisfolder);

			$this->render('email_shipping_note');

			$v_link_pdf= $thisfolder_1.','.$filename.'.pdf';
			$v_file_name=$filename.'.pdf';

			$this->redirect('/docs/add_from_option/'.$this->ModuleName().'/'.$this->get_id().'/'.$v_link_pdf.'/'.$v_file_name.'/'.$this->params->params['controller'].'');

		}
		die;
	}
	//subtab line entry
	public function view_shipping_note(){
		$this->layout = 'pdf';
		$ids = $this->get_id();
		if($ids!=''){
			$query = $this->opm->select_one(array('_id' =>new MongoId($ids)));
			$arrtemp = $query;
			//set header
			$this->set('logo_link','img/logo_anvy.jpg');
			$this->set('company_address','3145 - 5th Ave NE<br />Calgary  AB  T2A  6A3<br />');

			//customer address
			$customer = '';
			if(isset($arrtemp['company_id']) && strlen($arrtemp['company_id'])==24)
				$customer .= '<b>'.$this->get_name('Company',$arrtemp['company_id']).'</b><br />';
			else if(isset($arrtemp['company_name']))
				$customer .= '<b>'.$arrtemp['company_name'].'</b><br />';
			if(isset($arrtemp['contact_id']) && strlen($arrtemp['contact_id'])==24)
				$customer .= $this->get_name('Contact',$arrtemp['contact_id']).'<br />';
			else if(isset($arrtemp['contact_name']))
				$customer .= $arrtemp['contact_name'].'<br />';

			//loop 2 address
			$arradd = array('invoice','shipping');
			foreach($arradd as $vvs){
				$kk = $vvs; $customer_address = '';
				if(isset($arrtemp[$kk.'_address']) && isset($arrtemp[$kk.'_address'][0]) && count($arrtemp[$kk.'_address'])>0){
					$temp = $arrtemp[$kk.'_address'][0];
					if(isset($temp[$kk.'_address_1']) && $temp[$kk.'_address_1']!='')
						$customer_address .= $temp[$kk.'_address_1'].' ';
					if(isset($temp[$kk.'_address_2']) && $temp[$kk.'_address_2']!='')
						$customer_address .= $temp[$kk.'_address_2'].' ';
					if(isset($temp[$kk.'_address_3']) && $temp[$kk.'_address_3']!='')
						$customer_address .= $temp[$kk.'_address_3'].'<br />';
					else
						$customer_address .= '<br />';
					if(isset($temp[$kk.'_town_city']) && $temp[$kk.'_town_city']!='')
						$customer_address .= $temp[$kk.'_town_city'];

					if(isset($temp[$kk.'_province_state']))
						$customer_address .= ' '.$temp[$kk.'_province_state'].' ';
					else if(isset($temp[$kk.'_province_state_id']) && isset($temp[$kk.'_country_id'])){
						$keytemp = $temp[$kk.'_province_state_id'];
						$provkey = $this->province($temp[$kk.'_country_id']);
						if(isset($provkey[$temp]))
							$customer_address .= ' '.$provkey[$temp].' ';
					}


					if(isset($temp[$kk.'_zip_postcode']) && $temp[$kk.'_zip_postcode']!='')
						$customer_address .= $temp[$kk.'_zip_postcode'];

					if(isset($temp[$kk.'_country']) && isset($temp[$kk.'_country_id']) && (int)$temp[$kk.'_country_id']!="CA")
						$customer_address .= ' '.$temp[$kk.'_country'].'<br />';
					else
						$customer_address .= '<br />';
					$arr_address[$kk] = $customer_address;
				}
			}

			if(isset($arrtemp['name']) && $arrtemp['name']!='')
				$heading = $arrtemp['name'];
			else
				$heading = '';
			if(!isset($arr_address['invoice']))
				$arr_address['invoice'] = '';
			$this->set('customer_address',$customer.$arr_address['invoice']);
			if(!isset($arr_address['shipping']))
				$arr_address['shipping'] = '';
			$this->set('shipping_address',$arr_address['shipping']);

			// info data
			$info_data = (object) array();
			$info_data->contact_name = $arrtemp['contact_name'];
			$info_data->no = $arrtemp['code'];
			$info_data->job_no = isset($arrtemp['job_number'])?$arrtemp['job_number']:'';
			$info_data->date = (isset($arrtemp['invoice_date'])&&$arrtemp['invoice_date']!='' ? $this->opm->format_date($arrtemp['invoice_date']) : '');
			$info_data->po_no = (isset($arrtemp['customer_po_no']) ? $arrtemp['customer_po_no'] : '');
			$info_data->ac_no = '';
			$info_data->terms = (isset($arrtemp['payment_terms']) ? $arrtemp['payment_terms'] : '');
			$info_data->required_date = (isset($arrtemp['payment_due_date'])&&$arrtemp['payment_due_date']!='' ? $this->opm->format_date($arrtemp['payment_due_date']) : '');
			$this->set('info_data', $info_data);


			//$this->set('quote_date',$this->opm->format_date($arrtemp['quotation_date']));
			/**Nội dung bảng giá */
			$date_now = date('Ymd');
			$time=time();
			$filename = 'SN'.$date_now.$time.'-'.$info_data->no;

			$this->set('filename', $filename);
			$this->set('heading',$heading);
			$html_cont = '';
			if(isset($arrtemp['products']) && is_array($arrtemp['products']) && count($arrtemp['products'])>0){
				$line = 0; $colum = 7;
				foreach($arrtemp['products'] as $keys=>$values){
					if(!$values['deleted']){
						if($line%2==0)
							$bgs = '#fdfcfa';
						else
							$bgs = '#eeeeee';
						//code
						$html_cont .= '<tr style="background-color:'.$bgs.';"><td class="first">';
						if(isset($values['code']))
							$html_cont .= '  '.$values['code'];
						else
							$html_cont .= '  #'.$keys;
						//desription
						$html_cont .= '</td><td>';
						if(isset($values['products_name']))
							$html_cont .= str_replace("\n","<br />",$values['products_name']);
						else
							$html_cont .= 'Empty';
						//width
						$html_cont .= '</td><td align="right">';
						if(isset($values['sizew']) && $values['sizew']!='' && isset($values['sizew_unit']) && $values['sizew_unit']!='')
							$html_cont .= $values['sizew'].' ('.$values['sizew_unit'].')';
						else if(isset($values['sizew'])&& $values['sizew']!='')
							$html_cont .= $values['sizew'].' (in.)';
						else
							$html_cont .= '';
						//height
						$html_cont .= '</td><td align="right">';
						if(isset($values['sizeh']) && $values['sizeh']!='' && isset($values['sizeh_unit']) && $values['sizeh_unit']!='')
							$html_cont .= $values['sizeh'].' ('.$values['sizeh_unit'].')';
						else if(isset($values['sizeh']) && $values['sizeh']!='' )
							$html_cont .= $values['sizeh'].' (in.)';
						else
							$html_cont .= '';
						//Unit price
						$html_cont .= '</td><td align="right">';
						if(isset($values['unit_price']))
							$html_cont .= $this->opm->format_currency($values['unit_price']);
						else
							$html_cont .= '0.00';
						//Qty
						$html_cont .= '</td><td align="right">';
						if(isset($values['quantity']))
							$html_cont .= $values['quantity'];
						else
							$html_cont .= '';
						//line total
						$html_cont .= '</td><td align="right" class="end">';
						if(isset($values['sub_total']))
							$html_cont .= $this->opm->format_currency($values['sub_total']);
						else
							$html_cont .= '';


						$html_cont .= '</td></tr>';
						$line++;
					}//end if deleted
				}//end for


				if($line%2==0){
					$bgs = '#fdfcfa';$bgs2 = '#eeeeee';
				}else{
					$bgs = '#eeeeee';$bgs2 = '#fdfcfa';
				}

				$sub_total = $total = $taxtotal = 0.00;
				if(isset($arrtemp['sum_sub_total']))
					$sub_total = $arrtemp['sum_sub_total'];
				if(isset($arrtemp['sum_tax']))
					$taxtotal = $arrtemp['sum_tax'];
				if(isset($arrtemp['sum_amount']))
					$total = $arrtemp['sum_amount'];
				//Sub Total
				$html_cont .= '<tr style="background-color:'.$bgs.';">
									<td colspan="'.($colum-1).'" align="right" style="font-weight:bold;border-top:2px solid #aaa;" class="first">Sub Total:</td>
									<td align="right" style="border-top:2px solid #aaa;" class="end">'.$this->opm->format_currency($sub_total).'</td>
							   </tr>';
				//GST
				$html_cont .= '<tr style="background-color:'.$bgs2.';">
									<td colspan="'.($colum-1).'" align="right" style="font-weight:bold;" class="first">HST/GST:</td>
									<td align="right" class="end">'.$this->opm->format_currency($taxtotal).'</td>
							   </tr>';
				//Total
				$html_cont .= '<tr style="background-color:'.$bgs.';">
									<td colspan="'.($colum-1).'" align="right" style="font-weight:bold;" class="first bottom">Total:</td>
									<td align="right" class="end bottom">'.$this->opm->format_currency($total).'</td>
							   </tr>';

			}//end if


			$this->set('html_cont',$html_cont);
			if(isset($arrtemp['our_csr'])){
				$this->set('user_name',' '.$arrtemp['our_csr']);
			}else
				$this->set('user_name',' '.$this->opm->user_name());
			//end set content

			//set footer
			$this->render('view_shipping_note');
			$this->redirect('/upload/'.$filename.'.pdf');
		}
		die;
	}
	public function line_entry(){
		$is_text = $this->is_text;
		if($this->check_lock()){
			$this->opm->set_lock_option('line_entry','products');
			$this->opm->set_lock_option('text_entry','products');
		}

		$subdatas = $arr_ret = array(); $codeauto = 0; $opname = 'products';
		$sum_sub_total = $sum_tax = 0;
		$subdatas[$opname] = array();
		$ids = $this->get_id();
		if($ids!=''){
			//update sum
			$keyfield = array(
							"sub_total"		=> "sub_total",
							"tax"			=> "tax",
							"amount"		=> "amount",
							"sum_sub_total"	=> "sum_sub_total",
							"sum_tax"		=> "sum_tax",
							"sum_amount"	=> "sum_amount",
						);
			$arr_sum = $this->update_sum('products',$keyfield);
			//get entry data
			$arr_ret = $this->line_entry_data($opname,$is_text);
			if(isset($arr_ret[$opname]))
				$subdatas[$opname] = $arr_ret[$opname];
		}
		$this->set('subdatas', $subdatas);
		$codeauto = $this->opm->get_auto_code('code');
		$this->set('nextcode',$codeauto);
		$this->set('file_name','quotation_'.$this->get_id());
		$this->set('sum_sub_total',$arr_ret['sum_sub_total']);
		$this->set('sum_amount',$arr_ret['sum_amount']);
		$this->set('sum_tax',$arr_ret['sum_tax']);
		$this->set_select_data_list('relationship','line_entry');
	}

	//check and cal for Line Entry
	 public function line_entry_data($opname = 'products', $is_text = 0,$mod = '') {
        $arr_ret = array(); $option_for = '';
        $this->selectModel('Setting');
        if ($this->get_id() != '') {
            $newdata = $option_select_dynamic = array();
            $query = $this->opm->select_one(array('_id' => new MongoId($this->get_id())),array('options','products','sum_sub_total','sum_amount','sum_tax'));
            if(!isset($query['options']))
                $query['options'] = array();
            //set sum
            $arr_ret['sum_sub_total'] = $arr_ret['sum_amount'] = $arr_ret['sum_tax'] = '0.00';
            if (isset($query['sum_sub_total']) && $query['sum_sub_total'] != '')
                $arr_ret['sum_sub_total'] = $query['sum_sub_total'];
            if (isset($query['sum_amount']) && $query['sum_amount'] != '')
                $arr_ret['sum_amount'] = $query['sum_amount'];
            if (isset($query['sum_tax']) && $query['sum_tax'] != '')
                $arr_ret['sum_tax'] = $query['sum_tax'];
            $this->selectModel('Product');
            $arr_product_approved = $this->Product->select_all(array(
                                       'arr_where'=>array('approved'=>1),
                                       'arr_field'=>array('_id')
                                       ));
            $arr_product_approved = iterator_to_array($arr_product_approved);
            $option_for_sort = array();
            if (isset($query[$opname]) && is_array($query[$opname])) {
                $options = array();
                if(isset($query['options']) && !empty($query['options']) )
                    $options = $query['options'];
                foreach ($query[$opname] as $key => $arr) {
                    if (!$arr['deleted']) {
                        $newdata[$key] = $arr;
						//set default Unit price
						if(!isset($arr['custom_unit_price']) && isset($arr['unit_price']))
							$newdata[$key]['custom_unit_price'] = $arr['unit_price'];

                        if(!isset($arr['option_for'])){
                            $option = $this->new_option_data(array('key'=>$key,'products_id'=>$arr['products_id'],'options'=>$query['options'],'products'=>$query['products']),$query['products']);
                            //Khoa sell_by,oum neu nhu line nay co option
                            //Khoa tiep sell_price neu line nay co option same_parent
                            if(isset($option['option'])&&!empty($option['option'])){
                                foreach($option['option'] as $value){
                                    if($value['deleted']) continue;
                                    if(isset($value['choice'])&&$value['choice']==0&&isset($value['require']) && $value['require']!=1) continue;
                                    if($value['oum']!=$arr['oum'])
                                        $newdata[$key]['oum'] = 'Mixed';
                                    $newdata[$key]['xlock']['sell_by'] = '1';
                                    $newdata[$key]['xlock']['oum'] = '1';
                                    break;
                                    // if(!isset($value['same_parent']) || $value['same_parent']==0) continue;
                                    // $newdata[$key]['xlock']['sell_price'] = '1';
                                }
                            }
                            if(isset($arr['products_id'])&&!isset($arr_product_approved[(string)$arr['products_id']]))
                                $newdata[$key]['xcss_element']['sku']= 'approved_product';
                        } else {
                            $newdata[$key]['xlock']['sell_by'] = '1';
                            $newdata[$key]['xlock']['oum'] = '1';
                        }
						$newdata[$key]['option'] = 1;
                        $newdata[$key]['option_group'] = '';
						if (isset($newdata[$key]['products_id']) && $newdata[$key]['products_id']!='')
							$newdata[$key]['xlock']['unit_price']   = '1';
						else
							$newdata[$key]['xlock']['unit_price']   = '0';
                        if (isset($newdata[$key]['products_name'])) {
                            if($is_text != 1){
                                $arrtmp = explode("\n", $newdata[$key]['products_name']);
                                $newdata[$key]['products_name'] = $arrtmp[0];
                                if(isset($arr['same_parent']) && $arr['same_parent']==1)
                                    $get_name_only = true;
                            }
                            if(!empty($option)){
                                foreach($options as $k=>$val){
                                    if(isset($val['deleted']) && $val['deleted']) continue;
                                    if(!isset($val['line_no']) || $val['line_no']!=$key) continue;
                                    $newdata[$key]['option_group'] = (isset($val['option_group']) ? $val['option_group'] : '');
                                    if(isset($get_name_only)){
                                        if(!isset($val['quantity'])) continue;
                                        if($val['quantity']==1) continue;
                                        $newdata[$key]['products_name'] .= ' ('.$val['quantity'].')';
                                        unset($options[$k]);
                                    }
                                }
                            }
                        }
                        if(isset($newdata[$key]['products_name']) && $is_text == 1){
                            $newdata[$key]['products_costing_name'] = '';
                            if(isset($arr['details']))
                                $newdata[$key]['products_costing_name'] .= htmlentities('<p style="margin-left:15px;font-style:italic;">'.nl2br($arr['details']).'</p>');
                            if(isset($option['option'])&&!empty($option['option'])){
                                foreach($option['option'] as $value){
                                    if(isset($value['deleted']) && $value['deleted']) continue;
                                    if(!isset($value['product_name']) || $value['product_name']=='') continue;
                                    if(!isset($value['view_in_detail'])|| $value['view_in_detail']==0)continue;
                                    $newdata[$key]['products_costing_name'] .= htmlentities($value['product_name']).' ('.(isset($value['quantity']) ? $value['quantity'] : 0).')<br />';
                                }
                            }
                            $newdata[$key]['xlock']['products_name']= '1';
                            $newdata[$key]['xlock']['sell_by']  = '1';
                            $newdata[$key]['xlock']['sell_price']   = '1';
                            $newdata[$key]['xlock']['oum']      = '1';
                            $newdata[$key]['xlock']['quantity']     = '1';
                            $newdata[$key]['xlock']['sizew']        = '1';
                            $newdata[$key]['xlock']['sizew_unit']   = '1';
                            $newdata[$key]['xlock']['sizeh']        = '1';
                            $newdata[$key]['xlock']['sizeh_unit']   = '1';
                            $newdata[$key]['xlock']['unit_price']   = '1';
                            $newdata[$key]['xlock']['adj_qty']  = '1';
                            $newdata[$key]['xlock']['sub_total']    = '1';
                            $newdata[$key]['xlock']['tax']      = '1';
                            $newdata[$key]['xlock']['amount']       = '1';
                            $newdata[$key]['xlock']['option']       = '1';
                            $newdata[$key]['xlock']['receipts']     = '1';
                        }
                        //set all price in display
                        if (isset($arr['area']))
                            $newdata[$key]['area'] = (float) $arr['area'];
                        $newdata[$key]['custom_unit_price'] = $this->opm->format_currency((isset($arr['custom_unit_price']) ? (float)$arr['custom_unit_price'] : 0), 3);
                        if (isset($arr['unit_price'])){
                            $newdata[$key]['unit_price'] = $this->opm->format_currency((float) $arr['unit_price'], 3);
                            if(!isset($arr['custom_unit_price']))
                                $newdata[$key]['custom_unit_price'] = $newdata[$key]['unit_price'];
                        }
                        else
                            $newdata[$key]['unit_price'] = '0.000';
                        if (isset($arr['sub_total']))
                            $newdata[$key]['sub_total'] = $this->opm->format_currency((float) $arr['sub_total']);
                        else
                            $newdata[$key]['sub_total'] = '0.00';
                        if (isset($arr['tax']))
                            $newdata[$key]['tax'] = $this->opm->format_currency((float) $arr['tax'], 3);
                        else
                            $newdata[$key]['tax'] = '0.000';
                        if (isset($arr['amount']))
                            $newdata[$key]['amount'] = $this->opm->format_currency((float) $arr['amount']);
                        else
                            $newdata[$key]['amount'] = '0.00';
						unset($newdata[$key]['id']);
						$newdata[$key]['_id'] = $key;
						$newdata[$key]['sort_key'] = $this->opm->num_to_string($key).'-'.'0';

						$option_for = '';
						if(isset($arr['option_for']) && $arr['option_for']!=''){
                            $newdata[$key]['xempty']['option']      = '1';
                            $newdata[$key]['xempty']['view_costing']      = '1';
                            if(isset($arr['same_parent'])&&$arr['same_parent']==1)
                                $newdata[$key]['xempty']['custom_unit_price']   = '1';
                            $newdata[$key]['_id'] = $key;
							$newdata[$key]['sku_disable'] = '1';
							$newdata[$key]['sku'] = '';
							$newdata[$key]['remove_deleted'] = '1';
							$newdata[$key]['icon']['products_name'] = (is_object($arr['products_id']) ? URL.'/products/entry/'.$arr['products_id'] : '#');
							$newdata[$key]['sort_key'] = $this->opm->num_to_string($arr['option_for']).'-'.$key;
							if($mod!='options_list')
							     unset($newdata[$key]['products_id']);
						}


                        //data RFQ's
                        $receipts = 0;
                        if (isset($query['rfqs']) && is_array($query['rfqs']) && count($query['rfqs']) > 0) {
                            foreach ($query['rfqs'] as $rk => $rv) {
                                if (!$rv['deleted'] && isset($rv['rfq_code']) && (int) $rv['rfq_code'] == $key) {
                                    $receipts = 1;
                                }
                            }
                            $newdata[$key]['receipts'] = $receipts;
                        } else
                            $newdata[$key]['receipts'] = 0;

                        //chặn không cho custom size nếu is_custom_size = 1
                        if(is_object($arr['products_id'])){
                            $product = $this->Product->select_one(array('_id'=>new MongoId($arr['products_id'])),array('is_custom_size'));
                            if(isset($product['is_custom_size'])&&$product['is_custom_size']==1){
                                $newdata[$key]['xlock']['sizeh'] = '1';
                                $newdata[$key]['xlock']['sizew'] = '1';
                                $newdata[$key]['xlock']['sizeh_unit'] = '1';
                                $newdata[$key]['xlock']['sizew_unit'] = '1';
                                $newdata[$key]['xlock']['sell_by'] = '1';
                            }
                        }


						//empty neu same_parent = 1
						if(isset($arr['same_parent']) && $arr['same_parent']==1){
							$newdata[$key]['xlock']['products_name']= '1';
							$newdata[$key]['xempty']['sell_by'] 	= '1';
							$newdata[$key]['xempty']['sell_price'] 	= '1';
							$newdata[$key]['xempty']['oum'] 		= '1';
							$newdata[$key]['xempty']['quantity'] 	= '1';
							$newdata[$key]['xempty']['sizew'] 		= '1';
							$newdata[$key]['xempty']['sizew_unit'] 	= '1';
							$newdata[$key]['xempty']['sizeh'] 		= '1';
							$newdata[$key]['xempty']['sizeh_unit'] 	= '1';
							$newdata[$key]['xempty']['unit_price'] 	= '1';
							$newdata[$key]['xempty']['adj_qty'] 	= '1';
							$newdata[$key]['xempty']['sub_total'] 	= '1';
							$newdata[$key]['xempty']['tax'] 		= '1';
							$newdata[$key]['xempty']['amount'] 		= '1';
							$newdata[$key]['xempty']['option'] 		= '1';
							$newdata[$key]['xempty']['receipts'] 	= '1';
						}


						//empty neu sell_by parent = combination
						if($option_for!='' && isset($query[$opname][$option_for]['sell_by']) && $query[$opname][$option_for]['sell_by']=='combination'){
							//$newdata[$key]['xempty']['sub_total'] = '1';
							$newdata[$key]['xempty']['tax'] 		= '1';
							$newdata[$key]['xempty']['amount'] 		= '1';
						}

						//khoa Sold by neu la combination
						if (isset($newdata[$key]['sell_by']) && $newdata[$key]['sell_by']=='combination') {
							$newdata[$key]['xlock']['sell_by']= '1';
							$newdata[$key]['xlock']['sell_price']= '1';
							$newdata[$key]['xlock']['oum']= '1';
						}



                        //set lại select dựa vào loại sell_by
                        if (isset($newdata[$key]['sell_by'])) {
                            $option_select_dynamic['oum_' . $key] = $this->Setting->select_option_vl(array('setting_value' => 'product_oum_' . strtolower($arr['sell_by'])));
                        }
                        if(isset($arr['option_for']))
                            $option_for_sort['option'][$arr['option_for']][$key] = $newdata[$key];
                        else
                            $option_for_sort['parent'][$key] = $newdata[$key];

                    } //end if
                }
            }
            $arr_ret[$opname] =array();
            $this->selectModel('Product');
            if(isset($option_for_sort['parent'])){
                foreach($option_for_sort['parent'] as $p_key=>$parent){
                    $arr_ret[$opname][] = $parent;
                    if(!isset($option_for_sort['option'][$p_key])) continue;
                    if(is_object($parent['products_id'])){
                        $p_product = $this->Product->select_one(array('_id'=> new MongoId($parent['products_id'])),array('options'));
                        if(isset($p_product['options'])&&!empty($p_product['options'])){
                            foreach($option_for_sort['option'][$p_key] as $k_opt=>$opt){
                                if(!isset($opt['proids'])) continue;
                                $opt_key = str_replace((string)$parent['products_id'].'_', '', $opt['proids']);
                                $option_for_sort['option'][$p_key][$k_opt]['option_group'] = (isset($p_product['options'][$opt_key]['option_group']) ? $p_product['options'][$opt_key]['option_group'] : '');
                                $option_for_sort['option'][$p_key][$k_opt]['option_group_for_sort'] = $option_for_sort['option'][$p_key][$k_opt]['option_group'].'_'.$option_for_sort['option'][$p_key][$k_opt]['products_name'];
                            }
                        }
                    }
                    if(isset($option_for_sort['option'])){
                        if(isset($query['options'])&&!empty($query['options'])){
                            foreach($option_for_sort['option'][$p_key] as $k_opt=>$opt){
                                $line_no = $opt['_id'];
                                foreach($query['options'] as  $custom_opt_k=>$custom_opt_v){
                                    if(isset($custom_opt_v['deleted'])&&$custom_opt_v['deleted']){
                                        unset($query['options'][$custom_opt_k]);
                                        continue;
                                    }
                                    if(!isset($custom_opt_v['line_no']) || $custom_opt_v['line_no']!=$line_no) continue;
                                    if(!isset($custom_opt_v['option_group']))
                                        $custom_opt_v['option_group'] = '';
                                    $option_for_sort['option'][$p_key][$k_opt]['option_group'] = $custom_opt_v['option_group'];
                                    $option_for_sort['option'][$p_key][$k_opt]['option_group_for_sort'] = $option_for_sort['option'][$p_key][$k_opt]['option_group'].'_'.$option_for_sort['option'][$p_key][$k_opt]['products_name'];
                                    unset($query['options'][$custom_opt_k]); break;
                                }
                            }
                        }
                        $option_for_sort['option'][$p_key] = $this->opm->aasort($option_for_sort['option'][$p_key],'option_group_for_sort');
                        foreach($option_for_sort['option'][$p_key] as $value)
                            array_push($arr_ret[$opname], $value);
                    }
                }
            }
        }
        $this->set('option_select_dynamic', $option_select_dynamic);
        return $arr_ret;
    }


	//subtab Text entry
	public function view_product_option(){
		echo '';
		die;
	}

	//subtab Text entry
	public function text_entry(){
		$this->is_text = 1;
		$this->line_entry();
	}

	//Text pdf
	public function test_pdf(){
		$this->layout = 'pdf';
		//set footer
		$this->render('test_pdf');
	}
	public function email_pdf(){
		$this->layout = 'pdf';
		$ids = $this->get_id();
		if($ids!=''){
			$query = $this->opm->select_one(array('_id' =>new MongoId($ids)));
			$arrtemp = $query;
			//set header
			$this->set('logo_link','img/logo_anvy.jpg');
			$this->set('company_address','3145 - 5th Ave NE<br />Calgary  AB  T2A  6A3<br />');

			//customer address
			$customer = '';
			if(isset($arrtemp['company_id']) && strlen($arrtemp['company_id'])==24)
				$customer .= '<b>'.$this->get_name('Company',$arrtemp['company_id']).'</b><br />';
			else if(isset($arrtemp['company_name']))
				$customer .= '<b>'.$arrtemp['company_name'].'</b><br />';
			if(isset($arrtemp['contact_id']) && strlen($arrtemp['contact_id'])==24)
				$customer .= $this->get_name('Contact',$arrtemp['contact_id']).'<br />';
			else if(isset($arrtemp['contact_name']))
				$customer .= $arrtemp['contact_name'].'<br />';

			//loop 2 address
			$arradd = array('invoice','shipping');
			foreach($arradd as $vvs){
				$kk = $vvs; $customer_address = '';
				if(isset($arrtemp[$kk.'_address']) && isset($arrtemp[$kk.'_address'][0]) && count($arrtemp[$kk.'_address'])>0){
					$temp = $arrtemp[$kk.'_address'][0];
					if(isset($temp[$kk.'_address_1']) && $temp[$kk.'_address_1']!='')
						$customer_address .= $temp[$kk.'_address_1'].' ';
					if(isset($temp[$kk.'_address_2']) && $temp[$kk.'_address_2']!='')
						$customer_address .= $temp[$kk.'_address_2'].' ';
					if(isset($temp[$kk.'_address_3']) && $temp[$kk.'_address_3']!='')
						$customer_address .= $temp[$kk.'_address_3'].'<br />';
					else
						$customer_address .= '<br />';
					if(isset($temp[$kk.'_town_city']) && $temp[$kk.'_town_city']!='')
						$customer_address .= $temp[$kk.'_town_city'];

					if(isset($temp[$kk.'_province_state']))
						$customer_address .= ' '.$temp[$kk.'_province_state'].' ';
					else if(isset($temp[$kk.'_province_state_id']) && isset($temp[$kk.'_country_id'])){
						$keytemp = $temp[$kk.'_province_state_id'];
						$provkey = $this->province($temp[$kk.'_country_id']);
						if(isset($provkey[$temp]))
							$customer_address .= ' '.$provkey[$temp].' ';
					}


					if(isset($temp[$kk.'_zip_postcode']) && $temp[$kk.'_zip_postcode']!='')
						$customer_address .= $temp[$kk.'_zip_postcode'];

					if(isset($temp[$kk.'_country']) && isset($temp[$kk.'_country_id']) && (int)$temp[$kk.'_country_id']!="CA")
						$customer_address .= ' '.$temp[$kk.'_country'].'<br />';
					else
						$customer_address .= '<br />';
					$arr_address[$kk] = $customer_address;
				}
			}

			if(isset($arrtemp['name']) && $arrtemp['name']!='')
				$heading = $arrtemp['name'];
			else
				$heading = '';
			if(!isset($arr_address['invoice']))
				$arr_address['invoice'] = '';
			$this->set('customer_address',$customer.$arr_address['invoice']);
			if(!isset($arr_address['shipping']))
				$arr_address['shipping'] = '';
			$this->set('shipping_address',$arr_address['shipping']);

			// info data
			$info_data = (object) array();
			$info_data->contact_name = $arrtemp['contact_name'];
			$info_data->no = $arrtemp['code'];
			$info_data->job_no = (isset($arrtemp['job_number']) ? $arrtemp['job_number'] : '');
			$info_data->date = (isset($arrtemp['invoice_date'])&&$arrtemp['invoice_date']!='' ? $this->opm->format_date($arrtemp['invoice_date']) : '');
			$info_data->po_no = (isset($arrtemp['customer_po_no']) ? $arrtemp['customer_po_no'] : '');
			$info_data->ac_no = '';
			$info_data->terms = (isset($arrtemp['payment_terms']) ? $arrtemp['payment_terms'] : '');
			$info_data->required_date = (isset($arrtemp['payment_due_date'])&&$arrtemp['payment_due_date']!='' ? $this->opm->format_date($arrtemp['payment_due_date']) : '');
			$this->set('info_data', $info_data);


			//$this->set('quote_date',$this->opm->format_date($arrtemp['quotation_date']));
			/**Nội dung bảng giá */
			$date_now = date('Ymd');
			$time=time();
			$filename = 'SH'.$date_now.$time.'-'.$info_data->no;

			$thisfolder = 'upload'.DS.date("Y_m");
			$thisfolder_1='upload'.','.date("Y_m");

			$folder = ROOT.DS.APP_DIR.DS.WEBROOT_DIR.DS.$thisfolder;
			if (!file_exists($folder)) {
				mkdir($folder, 0777, true);
			}



			$this->set('filename', $filename);
			$this->set('heading',$heading);
			$html_cont = '';
			if(isset($arrtemp['products']) && is_array($arrtemp['products']) && count($arrtemp['products'])>0){
				$line = 0; $colum = 7;
				foreach($arrtemp['products'] as $keys=>$values){
					if(!$values['deleted']){
						if($line%2==0)
							$bgs = '#fdfcfa';
						else
							$bgs = '#eeeeee';
						//code
						$html_cont .= '<tr style="background-color:'.$bgs.';"><td class="first">';
						if(isset($values['code']))
							$html_cont .= '  '.$values['code'];
						else
							$html_cont .= '  #'.$keys;
						//desription
						$html_cont .= '</td><td>';
						if(isset($values['products_name']))
							$html_cont .= str_replace("\n","<br />",$values['products_name']);
						else
							$html_cont .= 'Empty';
						//quantity
						$html_cont .= '</td><td align="right">';
						$html_cont .= (isset($values['quantity']) ? $values['quantity'] : 0);
						//prev.
						$html_cont .= '</td><td align="right">';
						$html_cont .= (isset($values['prev_shipped']) ? $values['prev_shipped'] : '');
						//Now
						$html_cont .= '</td><td align="right">';
						$html_cont .= (isset($values['shipped']) ? $values['shipped'] : 0);
						//B/O
						$html_cont .= '</td><td align="right">';
						$html_cont .= (isset($values['balance_shipped']) ? $values['balance_shipped'] : 0);


						$html_cont .= '</td></tr>';
						$line++;
					}//end if deleted
				}//end for


				if($line%2==0){
					$bgs = '#fdfcfa';$bgs2 = '#eeeeee';
				}else{
					$bgs = '#eeeeee';$bgs2 = '#fdfcfa';
				}


			}//end if


			$this->set('html_cont',$html_cont);
			if(isset($arrtemp['our_csr'])){
				$this->set('user_name',' '.$arrtemp['our_csr']);
			}else
				$this->set('user_name',' '.$this->opm->user_name());
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
	public function view_pdf($getfile = false, $ids = '')
	{
        $this->layout = 'pdf';
        if (empty($ids)) {
       		$ids = $this->get_id();
        }
        if ($ids != '') {
            $shipping = $this->opm->select_one(array(
                '_id' => new MongoId($ids)
            ));
            if (!isset($_GET['print_pdf'])) {
                $filename = 'SH-' . $shipping['code'];
                if ($this->print_pdf(array(
                    'report_file_name' => $filename,
                    'report_url' => URL . '/shippings/view_pdf/' . $getfile . '/' . $ids,
                ))) {
                    if ($getfile) {
                        return $filename . '.pdf';
                    }

                    $this->redirect(URL . '/upload/' . $filename . '.pdf');
                }
                else {
                    if ($getfile) {
                        return false;
                    }

                    echo 'Please contact IT for this issue.';
                    die;
                }
            }
            $arr_address = array('invoice', 'shipping');
			foreach ($arr_address as $value) {
				$address = '';
				if (isset($shipping[$value . '_address']) && isset($shipping[$value . '_address'][0]) && count($shipping[$value . '_address']) > 0) {
					$temp = $shipping[$value . '_address'][0];
					if (isset($temp[$value . '_address_1']) && !empty($temp[$value . '_address_1']))
						$address .= $temp[$value . '_address_1'] . '<br />';
					if (isset($temp[$value . '_address_2']) && !empty($temp[$value . '_address_2']))
						$address .= $temp[$value . '_address_2'] . '<br />';
					if (isset($temp[$value . '_address_3']) && $temp[$value . '_address_3'] != '')
						$address .= $temp[$value . '_address_3'] . '<br />';
					if (isset($temp[$value . '_town_city']) && $temp[$value . '_town_city'] != '')
						$address .= $temp[$value . '_town_city'];

					if (isset($temp[$value . '_province_state']))
						$address .= ' ' . $temp[$value . '_province_state'] . ' ';
					else if (isset($temp[$value . '_province_state_id']) && isset($temp[$value . '_country_id'])) {
						$keytemp = $temp[$value . '_province_state_id'];
						$provkey = $this->province($temp[$value . '_country_id']);
						if (isset($provkey[$temp]))
							$address .= ' ' . $provkey[$temp] . ' ';
					}


					if (isset($temp[$value . '_zip_postcode']) && $temp[$value . '_zip_postcode'] != '')
						$address .= $temp[$value . '_zip_postcode'];

					if (isset($temp[$value . '_country']) && isset($temp[$value . '_country_id']) && (int) $temp[$value . '_country_id'] != "CA")
						$address .= ' ' . $temp[$value . '_country'] . '<br />';
					$arr_address[$value] = $address;
				}
			}
			if (empty($arr_address['shipping'])) {
				$arr_address['shipping'] = $arr_address['invoice'];
			}
			$shipping_contact_name = isset($shipping['shipping_address'][0]['shipping_contact_name']) ? $shipping['shipping_address'][0]['shipping_contact_name'] : '';
            $i = 0;
			$html = '
	    			<table class="table_content">
	    				<tr class="tr_right_none" style="background: #911b12;color: white;height: 29px;line-height: 29px;font-weight: bold;">
    						<td>SKU</td>
	    					<td>Description</td>
	    					<td class="right_text">Width</td>
	    					<td class="right_text">Height</td>
	    					<td class="right_text">Qty</td>
	    					<td class="right_text" width="5">Count</td>
	    				</tr>';
	    	$query = $this->line_entry_data();
	    	$products = $query['products'];
			foreach($products as $product) {
				//Xem có details không để gắn thêm vào name
				$product['products_name'] .= (isset($product['details']) ? '<br /><span style="font-style:italic;font-size: 12px;">'.nl2br($product['details']).'</span>' : '');
				if (!isset($product['option_for'])) {
					$html .= '
		    				<tr class="'.($i%2==0 ? 'bg_2' : 'bg_1').' content_asset">
		    					<td>'.$product['sku'].'</td>
		    					<td>'.$product['products_name'].'</td>
		    					<td class="right_text">'.($product['oum']!='unit' ? $product['sizew'].' '.$product['sizew_unit'] : '').'</td>
		    					<td class="right_text">'.($product['oum']!='unit' ? $product['sizeh'].' '.$product['sizeh_unit'] : '').'</td>
		    					<td class="right_text">'.$product['quantity'].'</td>
		    					<td>_________</td>
		    				</tr>';
    			} else {
    				$html .= '<tr class="'.($i%2==0 ? 'bg_2' : 'bg_1').' content_asset">
    							<td></td>
	    						<td>&nbsp;&nbsp;&nbsp;•'.$opt_value['products_name'];
    				if(!isset($product['same_parent']) || $product['same_parent']==0){
	    				$html .= '
	    					<td class="right_text">'.($product['oum']!='unit' ? $product['sizew'].' '.$product['sizew_unit'] : '').'</td>
	    					<td class="right_text">'.($product['oum']!='unit' ? $product['sizeh'].' '.$product['sizeh_unit'] : '').'</td>
	    					<td class="right_text">'.$product['quantity'].'</td>
	    					<td>_________</td>';
	    			} else {
	    				$html .= '
	    					<td></td>
	    					<td></td>
	    					<td></td>
	    					<td></td>';
	    			}
	    			$html .= '</tr>';
    			}
	    		$i++;
    		} ///sfafasd
    		$comment = isset($shipping['other_comment']) ? $shipping['other_comment'] : '';
		    $html .= '</table>
                	<div class="row" id="note">
                    	<strong>Note:</strong>
                    	'. $comment .'
                		<div></div>
                		<div></div>
                		<div></div>
                	</div>
		    		<div style="clear:right;padding-bottom:25px"></div>';

            $arr_tmp = array(
	    		'default_top_left_info' => true,
	    		'custom_left_info' => '<div style="padding: 10px 0 20px;">
                                       	<p style="font-weight:bold; text-decoration: underline;">Shipping to: </p>'.
                                       	(isset($shipping_contact_name) && !empty($shipping_contact_name) ? '<p class="bold_text">'.$shipping_contact_name.'</p>' : '<p class="bold_text">'.$shipping['company_name'].'</p>')
	    								. $arr_address['shipping'] .
                                    '</div>
                                    <table>
	    									<tr>
	    										<td style="border-bottom: 1px solid #cbcbcb;" colspan="2">&nbsp;</td>
    										</tr>
    										<tr>
    											<td style="padding-top: 15px;" class="bold_text">Docket #:</td><td>'. (isset($shipping['salesorder_number']) ? $shipping['salesorder_number'] : '') .'</td>
    										</tr>
    										<tr>
    											<td class="bold_text">Method of shipping:</td><td>'.(isset($shipping['delivery_method']) ? $shipping['delivery_method'] : '').'</td>
    										</tr>
	    									<tr>
	    										<td colspan="2">&nbsp;</td>
    										</tr>
	    									<tr>
    											<td style="padding-top: 15px;" height="35px">Package:</td><td>_________ of _________</td>
    										</tr>
    										<tr>
    											<td height="35px" style="">Packed by:</td><td>____________________</td>
    										</tr>
    								</table>',
	    		'custom_main_info' => '<table style="float: right; margin-top: 15px;">
    										<tr>
    											<td class="bold_text">Job no:</td><td>'.$shipping['job_number'].'</td>
    										</tr>
    										<tr>
    											<td class="bold_text">Date:</td><td>'.date('d M, Y',$shipping['shipping_date']->sec).'</td>
    										</tr>
    										<tr>
    											<td class="bold_text">Customer PO no:</td><td>'. (isset($shipping['customer_po_no']) ? $shipping['customer_po_no'] : '') .'</td>
    										</tr>
    									</table>
    									<p style="clear: right;"></p>
    									<div style="padding: 10px 0 20px; padding-right: 90px; float: right; text-align: left; border-bottom: 1px solid #cbcbcb;">
	                                       	<p style="font-weight:bold; text-decoration: underline;">Billing address: </p>' .
	                                       	(isset($shipping['company_name']) ? '<p class="bold_text">'.$shipping['company_name'].'</p>' : '')
		    								. $arr_address['invoice'] .
	                                    '</div>',
    			'report_name'	=>	'Packing Slip',
	    		'html' => $html
	    		);
	    	$arr_html[] = $arr_tmp;

            $arr_pdf = array();
            $arr_pdf['is_custom'] = true;
	    	$arr_pdf['content'] = $arr_html;
	        $arr_pdf['report_size'] = '8.5in*11in';
	        if(isset($shipping['heading']) && !empty($shipping['heading'])) {
	        	$arr_pdf['report_heading'] = $shipping['heading'];
	        }
	        $arr_pdf['custom_main_info'] = true;
	        $arr_pdf['report_file_name']='SH-'.$shipping['code'];
       	 	$this->render_pdf($arr_pdf);
       	}
	}
	//Export pdf
	public function __view_pdf($getfile=false) {
		$this->layout = 'pdf';
		$info_data = (object) array();
		$ids = $this->get_id();
		if ($ids != '') {
			$query = $this->opm->select_one(array('_id' => new MongoId($ids)));
			$other_info = '';
			if(isset($query['salesorder_id']) && is_object($query['salesorder_id'])){
				$this->selectModel('Salesorder');
				$salesorder = $this->Salesorder->select_one(array('_id'=>$query['salesorder_id']),array('delivery_method', 'code'));
				if(isset($salesorder['code'])){
					$other_info = "<b>Docket #:</b> {$salesorder['code']}<br />";
				}
				if(isset($salesorder['delivery_method'])){
					$other_info .= "<b>Method of shipping:</b> {$salesorder['delivery_method']}<br />";
				}
			}
			if(isset($query['tracking_no']))
				$other_info .= "<b>Tracking number:</b> {$query['tracking_no']}";
			$this->set('other_info',$other_info);
			$arrtemp = $query;
			//set header
			$this->set('logo_link', 'img/logo_anvy.jpg');
			$this->set('company_address', '3145 - 5th Ave NE<br />Calgary  AB  T2A  6A3<br />');

			//customer address
			$customer = '';
			if (isset($arrtemp['company_id']) && strlen($arrtemp['company_id']) == 24)
				$customer .= '<b>' . $this->get_name('Company', $arrtemp['company_id']) . '</b><br />';
			else if (isset($arrtemp['company_name']))
				$customer .= '<b>' . $arrtemp['company_name'] . '</b><br />';
			if (isset($arrtemp['contact_id']) && strlen($arrtemp['contact_id']) == 24)
				$customer .= $this->get_name('Contact', $arrtemp['contact_id']) . '<br />';
			else if (isset($arrtemp['contact_name']))
				$customer .= $arrtemp['contact_name'] . '<br />';

			//loop 2 address
			$arradd = array('invoice', 'shipping');
			foreach ($arradd as $vvs) {
				$kk = $vvs;
				$arr_address[$kk] = $customer_address = '';
				if (isset($arrtemp[$kk . '_address']) && isset($arrtemp[$kk . '_address'][0]) && count($arrtemp[$kk . '_address']) > 0) {
					$temp = $arrtemp[$kk . '_address'][0];
					if (isset($temp[$kk . '_address_1']) && $temp[$kk . '_address_1'] != '')
						$customer_address .= $temp[$kk . '_address_1'] . ' ';
					if (isset($temp[$kk . '_address_2']) && $temp[$kk . '_address_2'] != '')
						$customer_address .= $temp[$kk . '_address_2'] . ' ';
					if (isset($temp[$kk . '_address_3']) && $temp[$kk . '_address_3'] != '')
						$customer_address .= $temp[$kk . '_address_3'] . '<br />';
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
						$customer_address .= ' ' . $temp[$kk . '_country'] ;
					$arr_address[$kk] = trim($customer_address);
				}
			}
			if (!isset($arr_address['invoice'])) {
				$arr_address['invoice'] = '';
			}
			if (!isset($arr_address['shipping'])) {
				$arr_address['shipping'] = '';
			}
			if (empty($arr_address['shipping']) && !empty($arr_address['invoice'])) {
				$arr_address['shipping'] = $arr_address['invoice'];
			}

			if (isset($arrtemp['heading']) && $arrtemp['heading'] != '')
				$heading = $arrtemp['heading'];
			else
				$heading = '';
			$ship_to = '';
            if(isset($arrtemp['shipping_address'][0]['shipping_contact_name']))
                $ship_to = $arrtemp['shipping_address'][0]['shipping_contact_name'];
            else
            	$ship_to = $customer;
            $this->set('ship_to', $ship_to);
			$this->set('customer', $customer);
			$this->set('customer_address', $arr_address['invoice']);

			$this->set('shipping_address', $arr_address['shipping']);
			$this->set('ref_no', $arrtemp['code']);

			$info_data->contact_name = $arrtemp['contact_name'];
			$info_data->no = $arrtemp['code'];
			$info_data->job_no = isset($arrtemp['job_number']) ? $arrtemp['job_number'] : '';
			$info_data->date = $this->opm->format_date($arrtemp['shipping_date']);
			$info_data->po_no = isset($arrtemp['customer_po_no']) ? $arrtemp['customer_po_no'] : '';
			$info_data->ac_no = '';

			$this->set('info_data', $info_data);
			/*             * Nội dung bảng giá */
			$date_now = date('Ymd');
			$numkey = explode("-",$info_data->no);
			$filename = 'SH-'.$numkey[count($numkey)-1];
			$other_comment = '';
			if(isset($arrtemp['other_comment']))
				$other_comment = str_replace("\n","<br />",'<br />'.$arrtemp['other_comment']);
			$this->set('other_comment',$other_comment);
			$this->set('filename', $filename);
			$this->set('heading', $heading);
			$html_cont = '';
			$line_entry_data = $this->line_entry_data();
			if (isset($line_entry_data['products'])
			    && is_array($line_entry_data['products']) && count($line_entry_data['products']) > 0) {
				$line = 0;
				$colum = 7;
				$options = array();
				if(isset($arrtemp['options']) && !empty($arrtemp['options']) )
					$options = $arrtemp['options'];
				foreach ($line_entry_data['products'] as $values) {
						$keys = $values['_id'];
                        if (!$values['deleted']) {
                            if ($line % 2 == 0)
                                $bgs = '#fdfcfa';
                            else
                                $bgs = '#eeeeee';
                            //code

	                    	if( isset($values['option_for']) && isset($query['products'][$values['option_for']] ) ){
							    $pro = $query['products'][$values['option_for']];
							    if(isset($pro['sku']) && strpos(str_replace(' ', '', $pro['sku']), 'DCP-') !== false)
							        continue;
							}
                            $html_cont .= '<tr style="background-color:' . $bgs . ';"><td class="first">';
                            if(isset($values['option_for'])&&is_numeric($values['option_for'])){
                            	$values['sku'] = '';
                            	$values['products_name'] = '&nbsp;&nbsp;&nbsp;•'.$values['products_name'];
                            }
                             if (isset($values['sku']))
                                $html_cont .= '  ' . $values['sku'];
                            else
                                $html_cont .= '  #' . $keys;
                            //desription
                            $html_cont .= '</td><td>';
                            if (isset($values['products_name']))
                                $html_cont .= str_replace("\n", "<br />", $values['products_name']);
                            else
                                $html_cont .= 'Empty';

							//clear các dòng phía sau nếu là same product parent
							if(isset($values['same_parent']) && $values['same_parent']==1){
								$html_cont .= '</td><td colspan="4" class="end"></td></tr>';
								 $line++;
								continue;
							}


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
                            $html_cont .= '</td><td align="right">';
                            if (isset($values['oum']) )
                                $html_cont .= $values['oum'];
                            $html_cont .= '</td><td align="right" class="end">';
                            if (isset($values['quantity']))
                                $html_cont .= $values['quantity'];
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
			}
			$this->set('html_cont', $html_cont);
			if (isset($arrtemp['our_csr']))
				$this->set('user_name', ' ' . $arrtemp['our_csr']);
			else
				$this->set('user_name', ' ' . $this->opm->user_name());
			//end set content
			//set footer
			$this->render('view_pdf');
			if($getfile)
				return $filename.'.pdf';
			$this->redirect('/upload/' . $filename . '.pdf');
		}
		die;
	}



	/*================ RFQ's ==================*/

	public function rfqs(){
		$subdatas = array();
		$subdatas['rfqs'] = $arr_temp = array();
		if($this->get_id()!=''){
			$links = "onclick=\" window.location.assign('".URL."/quotations/rfqs_entry/".$this->get_id()."/";
			$query = $this->opm->select_one(array('_id' =>new MongoId($this->get_id())));
			if(isset($query['rfqs']) && is_array($query['rfqs']) && count($query['rfqs'])>0){
				foreach($query['rfqs'] as $kss=>$vss){
					if(!$vss['deleted']){
						$arr_temp[$kss] = $vss;
						$arr_temp[$kss]['_id'] = $arr_temp[$kss]['rfqs_id'] = $kss;
						if(isset($vss['rfq_date']) && is_object($vss['rfq_date']))
						$arr_temp[$kss]['rfq_date'] = $vss['rfq_date']->sec;
						if(isset($vss['deadline_date']) && is_object($vss['deadline_date']))
							$arr_temp[$kss]['deadline_date'] = $vss['deadline_date']->sec;
						else
							$arr_temp[$kss]['deadline_date'] = '';
						$arr_temp[$kss]['set_link'] = $links.$arr_temp[$kss]['_id']."');\"";
						if(isset($arr_temp[$kss]['rfq_code'])){
							$temp = $arr_temp[$kss]['rfq_code'];
							$arr_temp[$kss]['rfq_code'] = $query['products'][$temp]['code'];
							$arr_temp[$kss]['name_details'] = $query['products'][$temp]['products_name'];
						}
					}
				}
				$subdatas['rfqs'] = $arr_temp;
			}
		}
		$this->set('subdatas', $subdatas);
	}


	/*================ Doccument ==================*/

	//address
	public function set_entry_address($arr_tmp,$arr_set){
		$address_fset = array('address_1','address_2','address_3','town_city','country','province_state','zip_postcode');
		$address_value = $address_province_id = $address_country_id = $address_province = $address_country = array();
		$address_controller = array('shipping','invoice');
		$address_value['invoice'] = $address_value['shipping']= array('','','','',"CA",'','');
		$this->set('address_controller',$address_controller);//set
		$address_key 	= array('shipping','invoice');
		$this->set('address_key',$address_key);//set
		$address_country 	= $this->country();
		$this->set('shipping_contact_name', (isset($arr_tmp['shipping_address'][0]['shipping_contact_name']) ? $arr_tmp['shipping_address'][0]['shipping_contact_name'] : ''));
		foreach($address_key as $kss=>$vss){
			//neu ton tai address trong data base
			if(isset($arr_tmp[$vss.'_address'][0])){
				$arr_temp_op = $arr_tmp[$vss.'_address'][0];
				for($i=0;$i<count($address_fset);$i++){ //loop field and set value for display
					if(isset($arr_temp_op[$vss.'_'.$address_fset[$i]])){
						$address_value[$vss][$i] = $arr_temp_op[$vss.'_'.$address_fset[$i]];
					}else{
						$address_value[$vss][$i] = '';
					}
				}//pr($arr_temp_op);die;
				//get province list and country list

				if(isset($arr_temp_op[$vss.'_country_id']))
					$address_province[$vss] = $this->province($arr_temp_op[$vss.'_country_id']);
				else
					$address_province[$vss] = $this->province();
				//set province
				if(isset($arr_temp_op[$vss.'_province_state_id']) && $arr_temp_op[$vss.'_province_state_id']!='' && isset($address_province[$vss][$arr_temp_op[$vss.'_province_state_id']]) )
					$address_province_id[$kss] = $arr_temp_op[$vss.'_province_state_id'];
				else if(isset($arr_temp_op[$vss.'_province_state']))
					$address_province_id[$kss] = $arr_temp_op[$vss.'_province_state'];
				else
					$address_province_id[$kss] = '';

				//set country
				if(isset($arr_temp_op[$vss.'_country_id'])){
					$address_country_id[$kss] = $arr_temp_op[$vss.'_country_id'];
					$address_province[$vss] = $this->province($arr_temp_op[$vss.'_country_id']);
				}else{
					$address_country_id[$kss] = "CA";
					$address_province[$vss] = $this->province("CA");
				}

				$address_add[$vss] = '0';
			//chua co address trong data
			}else{
				$address_country_id[$kss] = "CA";
				$address_province[$vss] = $this->province("CA");
				$address_add[$vss] = '1';
			}
		}
		//pr($address_province);
		$this->set('address_value',$address_value);
		$address_hidden_field = array('shipping_address','invoice_address');
		$this->set('address_hidden_field',$address_hidden_field);//set
		$address_label[1] = $arr_set['field']['panel_2']['invoice_address']['name'];
		$address_label[0] = $arr_set['field']['panel_2']['shipping_address']['name'];
		$this->set('address_label',$address_label);//set
		$address_conner[0]['top'] 		= 'hgt fixbor';
		$address_conner[0]['bottom'] 	= 'fixbor2 jt_ppbot';
		$address_conner[1]['top'] 		= 'hgt';
		$address_conner[1]['bottom'] 	= 'fixbor3 jt_ppbot';
		$this->set('address_conner',$address_conner);//set
		$this->set('address_country',$address_country);//set
		$this->set('address_country_id',$address_country_id);//set
		$this->set('address_province',$address_province);//set
		$this->set('address_province_id',$address_province_id);//set
		$this->set('address_more_line',1);//set
		$this->set('address_onchange',"save_address_pr('\"+keys+\"');");
		if(isset($arr_tmp['company_id']) && strlen($arr_tmp['company_id'])==24)
		$this->set('address_company_id','company_id');
		if(isset($arr_tmp['contact_id']) && strlen($arr_tmp['contact_id'])==24)
		$this->set('address_contact_id','contact_id');
		$this->set('address_add',$address_add);
	}


	// Popup form orther module
    public function popup($key = '') {
        $this->set('key', $key);
        $limit = 100; $skip = 0; $cond = array();
        $this->identity($cond);
        // Nếu là search GET
        if (!empty($_GET)) {
            $tmp = $this->data;
            if (isset($_GET['company_id'])) {
                $cond['company_id'] = new MongoId($_GET['company_id']);
                $tmp['Quotation']['company'] = $_GET['company_name'];
            }
            if (isset($_GET['is_customer'])) {
                $cond['is_customer'] = 1;
                $tmp['Quotation']['is_customer'] = 1;
            }
            if (isset($_GET['is_employee'])) {
                $cond['is_employee'] = 1;
                $tmp['Quotation']['is_employee'] = 1;
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
        if (!empty($this->data) && !empty($_POST) && isset($this->data['Shipping']) ) {
            $arr_post = $this->data['Shipping'];

            if (isset($arr_post['name']) && strlen($arr_post['name']) > 0) {
                $cond['full_name'] = new MongoRegex('/' . trim($arr_post['name']) . '/i');
            }

            if (strlen($arr_post['company']) > 0) {
                $cond['company'] = new MongoRegex('/' . $arr_post['company'] . '/i');
            }

            if( $arr_post['inactive'] )
                $cond['inactive'] = 1;

            if (is_numeric($arr_post['is_customer']) && $arr_post['is_customer'])
                $cond['is_customer'] = 1;

            if (is_numeric($arr_post['is_employee']) && $arr_post['is_employee'])
                $cond['is_employee'] = 1;
        }

        $arr_shipping = $this->opm->select_all(array(
            'arr_where' => $cond,
            'arr_order' => $arr_order,
            'limit' => $limit,
            'skip' => $skip
                // 'arr_field' => array('name', 'is_customer', 'is_employee', 'company_id', 'company_name')
        ));
        $this->set('arr_shipping', $arr_shipping);

        $total_page = $total_record = $total_current = 0;
        if( is_object($arr_shipping) ){
            $total_current = $arr_shipping->count(true);
            $total_record = $arr_shipping->count();
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

	public function create_shipping_from_salesorder($salesorder_id){
		$this->selectModel('Salesorder');

		if($salesorder_id!=''){
			$arr_salesorder = $this->Salesorder->select_one(array('_id' => new MongoId($salesorder_id)));
			$arr_shipping = $this->opm->select_one(array('salesorder_id' => new MongoId($salesorder_id)));
		}

		$arr_save=array();

		if(isset($arr_salesorder['company_id']))
			$arr_save = $this->arr_associated_data('company_name',$arr_salesorder['company_name'], $arr_salesorder['company_id']);

		$arr_save['shipping_type']='Out';
		$arr_save['shipping_status']='In progress';
		$arr_save['shipping_date']=new MongoDate(time());
		$arr_save['salesorder_id']=isset($salesorder_id)?new MongoId($salesorder_id):'';
		$arr_save['salesorder_number']=isset($arr_salesorder['code'])?$arr_salesorder['code']:'';
		$arr_save['salesorder_name']=(isset($arr_salesorder['name'])?$arr_salesorder['name']:'');
		$arr_save['shipper'] = (isset($arr_salesorder['shipper']) ? $arr_salesorder['shipper'] : '');
		$arr_save['shipper_id'] = (isset($arr_salesorder['shipper_id']) ? $arr_salesorder['shipper_id'] : '');
		$arr_save['heading'] = (isset($arr_salesorder['heading']) ? $arr_salesorder['heading'] : '');

		if(isset($arr_salesorder['our_rep']) && isset($arr_salesorder['our_rep_id']) && $arr_salesorder['our_rep_id']!=''){
			$arr_save['our_rep_id'] = $arr_salesorder['our_rep_id'];
			$arr_save['our_rep'] = $arr_salesorder['our_rep'];
		}else{
			$arr_save['our_rep_id'] = $this->opm->user_id();
			$arr_save['our_rep'] = $this->opm->user_name();
		}

		if(isset($arr_salesorder['our_csr']) && isset($arr_salesorder['our_csr_id']) && $arr_salesorder['our_csr_id']!=''){
			$arr_save['our_csr_id'] = $arr_salesorder['our_csr_id'];
			$arr_save['our_csr'] = $arr_salesorder['our_csr'];
		}else{
			$arr_save['our_csr_id'] = $this->opm->user_id();
			$arr_save['our_csr'] = $this->opm->user_name();
		}

		if(isset($arr_salesorder['shipping_address'])&&is_array($arr_salesorder['shipping_address'])){



			if(isset($arr_save['addresses_default_key']))
				$v_default=$arr_save['addresses_default_key'];
			else
				$v_default=0;

			if((isset($arr_salesorder['shipping_address'][0]['shipping_address_1'])&&$arr_salesorder['shipping_address'][0]['shipping_address_1']!='')
			||(isset($arr_salesorder['shipping_address'][0]['shipping_address_2'])&&$arr_salesorder['shipping_address'][0]['shipping_address_2']!='')
			||(isset($arr_salesorder['shipping_address'][0]['shipping_address_3'])&&$arr_salesorder['shipping_address'][0]['shipping_address_3']!='')
			||(isset($arr_salesorder['shipping_address'][0]['shipping_town_city'])&&$arr_salesorder['shipping_address'][0]['shipping_town_city']!='')
			||(isset($arr_salesorder['shipping_address'][0]['shipping_province_state'])&&$arr_salesorder['shipping_address'][0]['shipping_province_state']!='')
			||(isset($arr_salesorder['shipping_address'][0]['shipping_province_state_id'])&&$arr_salesorder['shipping_address'][0]['shipping_province_state_id']!='')
			||(isset($arr_salesorder['shipping_address'][0]['shipping_zip_postcode'])&&$arr_salesorder['shipping_address'][0]['shipping_zip_postcode']!='')
			)
			{

				$arr_save['shipping_address']=$arr_salesorder['shipping_address'];
			}
			else
			{
				if(is_array($arr_save['invoice_address'])){

					foreach($arr_save['invoice_address'][$v_default] as $ka=>$va){
						if($ka!='deleted')
						{
							$ka1=substr($ka, 8);
							$arr_save['shipping_address'][0]['shipping_'.$ka1] = $va;
						}
						else
						{
							$arr_save['shipping_address'][0][$ka] = $va;
						}

					}
				}
			}



//			pr($arr_save['shipping_address']);
//			die;

		}





		//Products-------------------------------------------------------------
		$arr_save_temp=array();
		$arr_save['products']=is_array($arr_salesorder['products'])?$arr_salesorder['products']:array();
		if(is_array($arr_save['products'])){
			foreach($arr_save['products'] as $key=>$value)
			{
				$arr_save['products'][$key]['shipped']=isset($value['quantity'])?(int)$value['quantity']:0;
				$arr_save['products'][$key]['balance_shipped']=0;
			}
			foreach($arr_salesorder['products'] as $key=>$value)
			{
				$arr_salesorder['products'][$key]['shipped']=isset($value['quantity'])?(int)$value['quantity']:0;
				$arr_salesorder['products'][$key]['balance_shipped']=0;
			}
			$this->Salesorder->save($arr_salesorder);
		}
		//---------------------------------------------------------------------



		$arr_save['code'] =$this->opm->get_auto_code('code');

		if ($this->opm->save($arr_save)) {
			$this->redirect('/shippings/entry/'. $this->opm->mongo_id_after_save);
		}
		$this->redirect('/shippings/entry');
	}
    // $id = id quotation;
    public function create_sale_invoice($id = ''){
    	$id = new MongoId($id);
    	$this->selectModel('Salesorder');
    	// defind
    	$data = (object) array();
     	// get Salesorder by id
    	$sales_order = $this->Salesorder->select_one(array('_id' => $id));
    	// chuyen Salesorder sang object
    	$sales_order = (object) $sales_order;
    	// check Salesorder exiting
    	if($sales_order){
			$data->code              = $this->opm->get_auto_code('code');
			$data->invoice_type      = 'Invoice';
			$data->company_name      = $sales_order->company_name;
			$data->company_id        = $sales_order->company_id;
			$data->contact_name      = $sales_order->contact_name;
			$data->contact_id        = $sales_order->contact_id;
			$data->customer_po_no    = $sales_order->customer_po_no;
			$data->description       = $sales_order->description;
			$data->email             = $sales_order->email;
			$data->invoice_address   = $sales_order->invoice_address;
			$data->job_id            = $sales_order->job_id;
			$data->job_name          = $sales_order->job_name;
			$data->job_number        = $sales_order->job_number;
			$data->name              = $sales_order->name;
			$data->our_csr           = $sales_order->our_csr;
			$data->our_csr_id        = $sales_order->our_csr_id;
			$data->our_rep           = $sales_order->our_rep;
			$data->our_rep_id        = $sales_order->our_rep_id;
			$data->payment_due_date  = '&nbsp;';
			$data->payment_terms     = $sales_order->payment_terms;
			$data->phone             = $sales_order->phone;
			$data->products          = $sales_order->products;
			$data->salesorder_id     = $sales_order->_id;
			$data->salesorder_name   = $sales_order->name;
			$data->salesorder_number = $sales_order->code;
			$data->invoice_date      = new MongoDate();
			$data->shipping_address  = $sales_order->shipping_address;
			$data->invoice_status	 = 'Invoiced';
			$data->paid_date         = '&nbsp;';
			$data->sum_amount        = $sales_order->sum_amount;
			$data->sum_sub_total     = $sales_order->sum_sub_total;
			$data->sum_tax           = $sales_order->sum_tax;
			$data->tax               = $sales_order->tax;
			$data->taxval            = $sales_order->taxval;

    		// convert $data object to array
    		$data = (array) $data;
    		$this->selectModel('Salesinvoice');
    		// save sale invoice success
    		if($this->Salesinvoice->save($data)){
    			// return id sale invoice after save;
    			$return_id = $this->Salesinvoice->mongo_id_after_save;
    			$this->redirect('entry/'.$return_id);
    		}else{
    			echo 'Error: ' . $this->Salesinvoice->arr_errors_save[1];
    		}
    	}else{
    		die();
    	}
    }

	function general_auto_save($id) {
		$arr_save=array();
		if (!empty($_POST)) {
			$arr_save['_id'] = new MongoId($id);
			if(isset($_POST['data']))
				$arr_save['other_comment'] = $_POST['data'];
			if(isset($_POST['content']))
				$arr_save['web_tracker'] = $_POST['content'];
			$error = 0;
			$this->selectModel('Shipping');
			if (!$error) {
				if ($this->Shipping->save($arr_save)) {
					echo 'ok';
				} else {
					echo 'Error: ' . $this->Shipping->arr_errors_save[1];
				}
			}
		}
		die;
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
	public function ajax_cal_line() {
		$this->set_cal_price();
		$arr_ret = $arr_product_items = array();
		if (isset($_POST['arr'])) {
			$getdata = $_POST['arr'];
			$getdata = (array) $getdata;
			//truong hop co id
			if (isset($getdata['id'])) {
				$get_id = $getdata['id'];
				$qr = $this->opm->select_one(array('_id' => new MongoId($this->get_id())));
				$query = $qr;
				if (isset($query['products']))
					$arr_pro = $arr_insert['products'] = (array) $query['products'];
				if (is_array($arr_pro) && count($arr_pro) > 0 && isset($arr_pro[$get_id]) && !$arr_pro[$get_id]['deleted']) {
					$arr_pro = array_merge((array) $arr_pro[$get_id], (array) $getdata);
					$this->cal_price->arr_product_items = $arr_pro;
					$arr_ret = $this->cal_price->cal_price_items();
					//Save all data
					$arr_insert['_id'] = new MongoId($this->get_id());
					$arr_insert['products'][$get_id] = array_merge((array) $arr_pro, (array) $arr_ret);
					$qty = (isset($arr_insert['products'][$get_id]['quantity'])&&$arr_insert['products'][$get_id]['quantity']!='' ? $arr_insert['products'][$get_id]['quantity'] : 0);
					$now = (isset($arr_insert['products'][$get_id]['shipped'])&&$arr_insert['products'][$get_id]['shipped']!='' ? $arr_insert['products'][$get_id]['shipped'] : 0);
					$prev = (isset($arr_insert['products'][$get_id]['prev_shipped'])&&$arr_insert['products'][$get_id]['prev_shipped']!='' ? $arr_insert['products'][$get_id]['prev_shipped'] : 0);
					$arr_insert['products'][$get_id]['balance_shipped'] = ($qty!=0 ? $qty - $prev - $now : '');
					$this->opm->save($arr_insert);
					//update sum
					$keyfield = array(
						"sub_total" => "sub_total",
						"tax" => "tax",
						"amount" => "amount",
						"sum_sub_total" => "sum_sub_total",
						"sum_tax" => "sum_tax",
						"sum_amount" => "sum_amount"
					);
					$arr_sum = $this->update_sum('products', $keyfield);
					$arr_ret = array_merge((array) $arr_ret, (array) $arr_sum);
					//Return data for display
					echo json_encode($arr_ret);
				}

				//truong hop khong chon id nao
			} else {

				echo '';
			}
		}
		die;
	}
	public function tracking(){
		$subdatas = array();
		$subdatas['tracking_detail'] = array();
		$subdatas['web_tracker'] = array();


		$this->selectModel('Shipping');
		$arr_shipping = $this->Shipping->select_one(array('_id' => new MongoId($this->get_id())));

		$subdatas['tracking_detail']=$arr_shipping;
		$subdatas['web_tracker']=$arr_shipping;

		$this->selectModel('Setting');
		$arr_setting=$this->Setting->select_one(array('setting_value' => 'shipping_method'));


		$arr_options_custom = $this->set_select_data_list('relationship', 'tracking');


		$this->set('arr_options_custom', $arr_options_custom);

		$this->selectModel('Company');
		$arr_company_shipper=array();
		if(isset($arr_shipping['shipper_id'])&&$arr_shipping['shipper_id']!=null)
			$arr_company_shipper = $this->Company->select_one(array('_id' => new MongoId($arr_shipping['shipper_id'])));

		if(isset($arr_company_shipper['tracking_url'])&&$arr_company_shipper['tracking_url']!='')
			$this->set('tracking_url_iframe',$arr_company_shipper['tracking_url']);

		$this->set('_id', $this->get_id());
		$this->set('subdatas', $subdatas);
		//$this->set('arr_shipping', $arr_shipping);
	}

	//An other
	public function other(){
		$subdatas = array();
		$id=$this->get_id();
		$this->selectModel('Shipping');
		if(isset($id)){
			$arr_shipping = $this->Shipping->select_one(array('_id' => new MongoId($id)));
		}
        $subdatas['other_pricing'] = array($arr_shipping);
        $subdatas['other_comment']= array('other_comment'=>isset($arr_shipping['other_comment'])?$arr_shipping['other_comment']:'');
        $this->set('subdatas', $subdatas);
		$this->selectModel('Shipping');
		$arr_options_custom = $this->set_select_data_list('relationship', 'other');
		$this->set('arr_options_custom', $arr_options_custom);
		$this->communications($id, true);
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
        <br />
        <br />
        <div style="border-bottom: 1px dashed #9f9f9f; height:1px; clear:both"></div>
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
    public function option_summary_customer_find($type = ''){
        $arr_data['shippings_type'] = $this->Setting->select_option_vl(array('setting_value'=>'shipping_types'));
        $arr_data['shippings_status'] = $this->Setting->select_option_vl(array('setting_value'=>'shipping_statuses'));

        $this->set('arr_data',$arr_data);
        $report_type = 'summary';
        if($type=='area')
            $report_type = 'area_summary';
        $this->set('report_type',$report_type);
    }
    public function option_detailed_customer_find($type = ''){
        $arr_data['shippings_type'] = $this->Setting->select_option_vl(array('setting_value'=>'shipping_types'));
        $arr_data['shippings_status'] = $this->Setting->select_option_vl(array('setting_value'=>'shipping_statuses'));
        $this->set('arr_data',$arr_data);
        $report_type = 'detailed';
        if($type=='area')
            $report_type = 'area_detailed';
        else if($type == 'sale')
            $report_type = 'sale_detailed';
        $this->set('report_type',$report_type);
    }
    public function customer_report($type = ''){
    	$arr_data = array();
        if(isset($_GET['print_pdf'])){
            $arr_data = Cache::read('shippings_customer_report_'.$type);
            Cache::delete('shippings_customer_report_'.$type);
        } else {
			if (isset($_POST) && !empty($_POST)) {
				$arr_post = $_POST;
				$arr_where = array( 'company_id' => array('$nin'=>array('',null)));
				if(isset($arr_post['type']) && $arr_post['type']!='')
					$arr_where['shipping_type'] = $arr_post['type'];
				if(isset($arr_post['status']) && $arr_post['status'])
					$arr_where['shipping_status'] = $arr_post['status'];
				//Check loại trừ cancel thì bỏ các status bên dưới
				if(isset($arr_post['is_not_cancel']) && $arr_post['is_not_cancel']==1){
					$arr_where['shipping_status'] = array('$nin'=>array('Delivered','Cancelled'));
					//Tuy nhiên nếu ở ngoài combobox nếu có chọn, thì ưu tiên nó, set status lại
					if(isset($arr_post['status']) && $arr_post['status']!='')
						$arr_where['shipping_status'] = $arr_post['status'];
				}
				if(isset($arr_post['is_return']) && $arr_post['is_return']==1)
					$arr_where['return_status'] = 1;
				//Có hai trường hợp, hoặc là receiver (type là in) hoặc là sender (type là out)
				if(isset($arr_post['company_receiver']) && $arr_post['company_receiver']!=''){
					$arr_where['company_name'] = new MongoRegex('/'.trim($arr_post['company_receiver']).'/i');
					$arr_where['shipping_type'] = 'Out';
				}
				if(isset($arr_post['contact_receiver']) && $arr_post['contact_receiver']!=''){
					$arr_where['contact_name'] = new MongoRegex('/'.trim($arr_post['contact_receiver']).'/i');
					$arr_where['shipping_type'] = 'Out';
				}
				if(isset($arr_post['company_sender']) && $arr_post['company_sender']!=''){
					$arr_where['company_name'] = new MongoRegex('/'.trim($arr_post['company_sender']).'/i');
					$arr_where['shipping_type'] = 'In';
				}
				if(isset($arr_post['contact_sender']) && $arr_post['contact_sender']!=''){
					$arr_where['contact_name'] = new MongoRegex('/'.trim($arr_post['contact_sender']).'/i');
					$arr_where['shipping_type'] = 'In';
				}
				if(isset($arr_post['carrier']) && $arr_post['carrier']!='')
					$arr_where['carrier_name'] = new MongoRegex('/'.trim($arr_post['carrier']).'/i');
				if(isset($arr_post['job_no']) && $arr_post['job_no']!='')
					$arr_where['job_number'] = $arr_post['job_no'];
				//Tìm chính xác ngày
				//Vì để = chỉ tìm đc 01/01/1969 00:00:00 nên phải cộng cho 23:59:59 rồi tìm trong khoảng đó
				if(isset($arr_post['date_equals']) && $arr_post['date_equals']!=''){
					$arr_where['shipping_date']['$gte'] = new MongoDate($this->Common->strtotime($arr_post['date_equals'] . '00:00:00'));
					$arr_where['shipping_date']['$lt'] = new MongoDate($this->Common->strtotime($arr_post['date_equals'] . '23:59:59'));
				}
				else if(isset($arr_post['date_equals']) && $arr_post['date_equals'] == '') { //Ngày nằm trong khoảng
					//neu chi nhap date from
					if(isset($arr_post['date_from']) && $arr_post['date_from']!=''){
						$arr_where['shipping_date']['$gte'] = new MongoDate($this->Common->strtotime($arr_post['date_from'] . '00:00:00'));
					}
					//neu chi nhap date to
					if(isset($arr_post['date_to']) && $arr_post['date_to'] != '') {
						$arr_where['shipping_date']['$lte']  = new MongoDate($this->Common->strtotime($arr_post['date_to'] . '23:59:59'));
					}
				}
				if(isset($arr_post['our_rep']) && $arr_post['our_rep']!='')
					$arr_where['our_rep'] = new MongoRegex('/'.$arr_post['our_rep'].'/i');
				if(isset($arr_post['our_csr']) && $arr_post['our_csr']!='')
					$arr_where['our_csr'] = new MongoRegex('/'.$arr_post['our_csr'].'/i');
				$this->selectModel('Shipping');
				//lay het shipping, voi where nhu tren va lay sum_amount giam dan
				$shipping = $this->Shipping->select_all(array(
						'arr_where'=>$arr_where,
						'arr_field'=>array('_id','code','company_name','company_id','contact_name','contact_id','shipping_type','carrier_name','shipping_date','shipping_address','shipping_status','tracking_no','return_status','shipping_cost', 'salesorder_id','salesorder_number'),
						'arr_order' => array('code' => -1)
					));
				if($shipping->count()==0){
					echo 'empty';
					die;
				} else if(!$this->request->is('ajax')) {
					if($arr_post['report_type']=='summary')
						$arr_data = $this->summary_customer_report($shipping,$arr_post);
					else if($arr_post['report_type'] == 'detailed')
						$arr_data = $this->detailed_customer_report($shipping,$arr_post);
					else if($arr_post['report_type'] == 'sale_detailed') {
						$arr_data = $this->detailed_sale_customer_report($shipping,$arr_post);
						Cache::write('shippings_sales_excel', $arr_data['excel']);
						$arr_data = $arr_data['pdf'];
					}
					else if($arr_post['report_type']=='area_summary')
						$arr_data = $this->summary_area_customer_report($shipping,$arr_post);
					else if($arr_post['report_type'] == 'area_detailed')
						$arr_data = $this->detailed_area_customer_report($shipping,$arr_post);
					Cache::write('shippings_customer_report_'.$type, $arr_data);
				}
			}
		}
		if($this->request->is('ajax'))
            die;
        else
            $this->render_pdf($arr_data);
	}
	public function summary_customer_report($arr_shippings,$arr_post){

		//--------------------------------------
		$html = '';
		$i = 0;
		$group = array();
		$this->selectModel('Company');
		$this->selectModel('Contact');
		//Có 2 trường hợp là công ty hoặc cá nhân,
		//nên phải lọc theo _id tương ứng, ưu tiên company nếu có cả 2
		foreach($arr_shippings as $shipping){
			if(!is_object($shipping['company_id'])) continue;
			$company = $this->Company->select_one(array('_id'=> new MongoId($shipping['company_id'])
														),array('email','phone'));
			$company_id = (string)$shipping['company_id'];
			$group[$company_id]['company_name'] = $shipping['company_name'];
			$group[$company_id]['contact_name'] = $shipping['contact_name'];
			$group[$company_id]['phone'] = (isset($company['phone']) ? $company['phone'] : '');
			$group[$company_id]['email'] = (isset($company['email']) ? $company['email'] : '');
			if(!isset($group[$company_id]['number_of_shipment']))
				$group[$company_id]['number_of_shipment'] = 0;
			$group[$company_id]['number_of_shipment']++;

		}
		//==========================================================================================
		$total_number = 0;
		 foreach ($group as $value) {
		 	$total_number += $value['number_of_shipment'];
            $html .= '
                <tr class="'.($i%2==0 ? 'bg_1' : 'bg_2').'">
                     <td>' . $value['company_name'] . '</td>
                     <td>' . $value['contact_name'] . '</td>
                     <td>' . $value['phone'] . '</td>
                     <td>' . $value['email'] . '</td>
                     <td class="right_text">' . $value['number_of_shipment'] . '</td>
                </tr>
            ';
            $i++;
        }
        $html .= '
                    <tr class="'.($i%2==0 ? 'bg_1' : 'bg_2').'">
                         <td colspan="3" class="bold_text right_none">' . $i . ' record(s) listed</td>
                         <td class="bold_text right_text right_none">Totals</td>
                         <td class="bold_text right_text">' . $total_number  . ' </td>
                    </tr>
                </table>
                ';
        //========================================
        //set header
        if($arr_post['heading']!='')
            $arr_data['report_heading'] = $arr_post['heading'];
        $arr_data['date_from_to'] = '';
        if(isset($arr_post['date_from'])&&$arr_post['date_from']!='')
            $arr_data['date_from_to'] .= '<span class="color_red bold_text">From</span> '.$arr_post['date_from'].' ';
        if(isset($arr_post['date_to'])&&$arr_post['date_to']!='')
            $arr_data['date_from_to'] .= ' <span class="color_red bold_text">To</span> '.$arr_post['date_to'];
        if(isset($arr_post['date_equals'])&&$arr_post['date_equals']!='')
            $arr_data['date_from_to'] .= $data['date_equals'];
        $arr_data['title'] = array('Company','Contact','Phone','Email','No. of SH'=>'text-align: center');
        $arr_data['content'] = $html;
        $arr_data['report_name'] = 'SH Report By Customer (Summary)';
        $arr_data['report_file_name'] = 'SH_'.md5(time());
        return $arr_data;
	}
	public function summary_area_customer_report($arr_shippings,$arr_post){

		//--------------------------------------
		$html = '';
		$i = 0;
		$group = array();
		$this->selectModel('Company');
		$this->selectModel('Contact');
		//Có 2 trường hợp là công ty hoặc cá nhân,
		//nên phải lọc theo _id tương ứng, ưu tiên company nếu có cả 2
		foreach($arr_shippings as $shipping){
			if(!is_object($shipping['company_id'])) continue;
			$province = '(empty)';
			if(isset($shipping['shipping_address'][0]['shipping_province_state']))
				$province = $shipping['shipping_address'][0]['shipping_province_state'];
			if(!isset($group[$province]['number_of_shipment']))
				$group[$province]['number_of_shipment'] = 0;
			$group[$province]['number_of_shipment']++;

		}
		//==========================================================================================
		$total_number = 0;
		 foreach ($group as $province=>$value) {
		 	$total_number += $value['number_of_shipment'];
            $html .= '
                <tr class="'.($i%2==0 ? 'bg_1' : 'bg_2').'">
                     <td colspan="2">' . $province. '</td>
                     <td class="right_text">' . $value['number_of_shipment'] . '</td>
                </tr>
            ';
            $i++;
        }
        $html .= '
                    <tr class="'.($i%2==0 ? 'bg_1' : 'bg_2').'">
                         <td class="bold_text right_none">' . $i . ' record(s) listed</td>
                         <td class="bold_text right_text right_none">Totals</td>
                         <td class="bold_text right_text">' . $total_number  . ' </td>
                    </tr>
                </table>
                ';
        //========================================
        //set header
        if($arr_post['heading']!='')
            $arr_data['report_heading'] = $arr_post['heading'];
        $arr_data['date_from_to'] = '';
        if(isset($arr_post['date_from'])&&$arr_post['date_from']!='')
            $arr_data['date_from_to'] .= '<span class="color_red bold_text">From</span> '.$arr_post['date_from'].' ';
        if(isset($arr_post['date_to'])&&$arr_post['date_to']!='')
            $arr_data['date_from_to'] .= ' <span class="color_red bold_text">To</span> '.$arr_post['date_to'];
        if(isset($arr_post['date_equals'])&&$arr_post['date_equals']!='')
            $arr_data['date_from_to'] .= $data['date_equals'];
        $arr_data['title'] = array('Province','','No. of SH'=>'text-align: right');
        $arr_data['content'] = $html;
        $arr_data['report_name'] = 'SH Report By Area Customer (Summary)';
        $arr_data['report_file_name'] = 'SH_'.md5(time());
        return $arr_data;
	}
	public function detailed_customer_report($shipping, $data) {
        $html = '';
        $i = 0;
        $this->selectModel('Company');
        $this->selectModel('Contact');
        //Có 2 trường hợp là công ty hoặc cá nhân,
		//nên phải lọc theo _id tương ứng, ưu tiên company nếu có cả 2
		//vì shipping chỉ lưu first name, nên phải dò từ Contact
        foreach ($shipping as $value) {
        	if(!is_object($value['company_id'])) continue;
        		$company_id = (string)$value['company_id'];
                $company = $this->Company->select_one(array('_id'=> new MongoId($value['company_id'])
															),array('email','phone'));
                $group[$company_id]['contact_name']='';
                if(isset($value['contact_id'])&&$value['contact_id']!='') {
                	$contact = $this->Contact->select_one(array('_id'=> new MongoId($value['contact_id'])),
														array('first_name', 'last_name')
														);

                	$group[$company_id]['contact_name'] = (isset($contact['first_name']) ? $contact['first_name'] : '').' '.(isset($contact['last_name']) ? $contact['last_name'] : '');
                }
				$group[$company_id]['company_name'] = $value['company_name'];
				$group[$company_id]['phone'] = isset($company['phone']) ? $company['phone'] : '';
				$group[$company_id]['email'] = isset($company['email']) ? $company['email'] : '';
				if(!isset($group[$company_id]['number_of_shipment']))
					$group[$company_id]['number_of_shipment'] = 0;
				$group[$company_id]['number_of_shipment']++;
                //Tach theo shipping code
                $group[$company_id]['shipping'][$value['code']]['code'] = $value['code'];
                $group[$company_id]['shipping'][$value['code']]['shipping_type'] = $value['shipping_type'];
                $group[$company_id]['shipping'][$value['code']]['return_status'] = (isset($value['return_status']) && $value['return_status']==1 ? 'X' : '');
                $group[$company_id]['shipping'][$value['code']]['shipping_date'] = (isset($value['shipping_date']) && is_object($value['shipping_date']) ? date('d/M/Y',$value['shipping_date']->sec)  : '');
                $group[$company_id]['shipping'][$value['code']]['shipping_status'] = $value['shipping_status'];
                $group[$company_id]['shipping'][$value['code']]['carrier_name'] = (isset($value['carrier_name']) ? $value['carrier_name'] : '');
                $group[$company_id]['shipping'][$value['code']]['tracking_no'] = (isset($value['tracking_no']) ? $value['tracking_no'] : '');
        }
        foreach ($group as $value) {
            $html .= '
            <table class="table_content">
               <tbody>
                  <tr class="tr_right_none" style="background: #911b12;color: white;height: 29px;line-height: 29px;font-weight: bold;">
                     <td width="35%">
                        Company
                     </td>
                     <td width="25%">
                        Contact
                     </td>
                     <td width="15%">
                        Phone
                     </td>
                     <td width="15%">
                        Email
                     </td>
                     <td class="right_text" width="7%">
                        No. of SH
                     </td>
                  </tr>
                  <tr class="bg_2">
                     <td>' . $value['company_name'] . '</td>
                     <td>' . $value['contact_name'] . '</td>
                     <td>' . $value['phone'] . '</td>
                     <td>' . $value['email'] . '</td>
                     <td class="right_text">' . $value['number_of_shipment'] . '</td>
                  </tr>
               </tbody>
            </table>';
            $html .= '<table class="table_content" >
                        <tbody>
                          <tr class="tr_right_none" style="background: #911b12;color: white;height: 29px;line-height: 29px;font-weight: bold;">
                             <td width="7%">
                                SH#
                             </td>
                             <td class="center_text" width="15%">
                                Type
                             </td>
                              <td class="center_text" width="5%">
                                Return
                             </td>
                             <td class="center_text" width="15%">
                                Date
                             </td>
                             <td class="center_text" width="15%">
                                Status
                             </td>
                             <td width="20%">
                                Carrier
                             </td>
                             <td class="right_text" width="15%">
                                Tracking no
                             </td>
                          </tr>';
            $i = 0;
            $sum = 0;
            foreach ($value['shipping'] as $shipping) {
                $html .= '
                      <tr class="content_asset bg_' . ($i % 2 == 0 ? '1' : '2') . '">
                         <td>' . $shipping['code'] . '</td>
                         <td class="center_text">' . $shipping['shipping_type'] . '</td>
                         <td class="center_text bold_text">' . $shipping['return_status'] . '</td>
                         <td class="center_text">' . $shipping['shipping_date'] . '</td>
                         <td class="center_text">' . $shipping['shipping_status'] . '</td>
                         <td class="center_text">' . $shipping['carrier_name'] . '</td>
                         <td class="right_text">' . $shipping['tracking_no'] . '</td>
                      </tr>';
                $i++;
            }
            $html .= '
                            <tr class="bg_' . ($i % 2 == 0 ? '1' : '2') . '">
                             <td colspan="7" class="left_text bold_text right_none">' . $i . ' record(s) listed</td>
                          </tr>
                        </tbody>
                    </table>
                    <br />
                    <br />
           			<div class="line" style="margin-bottom: 10px;"></div>';
        }
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
        $arr_data['report_name'] = 'SH Report By Customer (Detailed)';
        $arr_data['report_file_name'] = 'SH_'.md5(time());
        return $arr_data;
    }
    public function detailed_sale_customer_report($shipping, $data) {
        $html = '';
        $i = 0;
        $this->selectModel('Company');
        $this->selectModel('Contact');
        $this->selectModel('Salesorder');
        //Có 2 trường hợp là công ty hoặc cá nhân,
		//nên phải lọc theo _id tương ứng, ưu tiên company nếu có cả 2
		//vì shipping chỉ lưu first name, nên phải dò từ Contact
        foreach ($shipping as $value) {
        	if(!is_object($value['company_id'])) continue;
	    	$company_id = (string)$value['company_id'];
        	if( !isset($group[$company_id]) ) {
	            $company = $this->Company->select_one(array('_id'=> new MongoId($value['company_id'])
															),array('email','phone'));
	            $group[$company_id]['contact_name']='';
	            if(isset($value['contact_id'])&&$value['contact_id']!='') {
	            	$contact = $this->Contact->select_one(array('_id'=> new MongoId($value['contact_id'])),
														array('first_name', 'last_name')
														);

	            	$group[$company_id]['contact_name'] = (isset($contact['first_name']) ? $contact['first_name'] : '').' '.(isset($contact['last_name']) ? $contact['last_name'] : '');
	            }
				$group[$company_id]['company_name'] = $value['company_name'];
				$group[$company_id]['phone'] = isset($company['phone']) ? $company['phone'] : '';
				$group[$company_id]['email'] = isset($company['email']) ? $company['email'] : '';
        	}
			if(!isset($group[$company_id]['number_of_shipment']))
				$group[$company_id]['number_of_shipment'] = 0;
			$group[$company_id]['number_of_shipment']++;
            //Tach theo shipping code
            $group[$company_id]['shipping'][$value['code']]['code'] = $value['code'];
            $group[$company_id]['shipping'][$value['code']]['shipping_type'] = $value['shipping_type'];
            $group[$company_id]['shipping'][$value['code']]['return_status'] = (isset($value['return_status']) && $value['return_status']==1 ? 'X' : '');
            $group[$company_id]['shipping'][$value['code']]['shipping_date'] = (isset($value['shipping_date']) && is_object($value['shipping_date']) ? date('d M, Y',$value['shipping_date']->sec)  : '');
            $group[$company_id]['shipping'][$value['code']]['shipping_status'] = $value['shipping_status'];
            $group[$company_id]['shipping'][$value['code']]['carrier_name'] = (isset($value['carrier_name']) ? $value['carrier_name'] : '');
            $group[$company_id]['shipping'][$value['code']]['tracking_no'] = (isset($value['tracking_no']) ? $value['tracking_no'] : '');
            $group[$company_id]['shipping'][$value['code']]['shipping_cost'] = (isset($value['shipping_cost']) ? (float)$value['shipping_cost'] : 0);
            $sale_price = 0;
            $order_code = isset($value['salesorder_number'])? $value['salesorder_number'] : '';
            if( isset($value['salesorder_id']) && $value['salesorder_id']) {
            	try {
        			$result = $this->Salesorder->collection->aggregate(array(
        					array(
        		                '$match' => array(
        		                	'_id' => $value['salesorder_id'],
        		                	'deleted'=>false,
        		                	'products.0' => array(
        		                		'$exists' => true,
        		                		)
        		                	),
        		            ), array(
        		                '$unwind'=>'$products',
        		            ), array(
        		                '$match' => array(
        		                		'products.deleted' => false,
        		                		'products.sku' => new MongoRegex('/SH/'),
        		                	)
        		            ), array(
        		                '$project'=>array('products'=>'$products', 'code'=>'$code')
        		            ), array(
        		                '$group'=>array(
        		                              '_id'=>array('_id'=>'$_id','code'=>'$code'),
        		                              'products'=>array('$push'=>'$products')
        		                            )
        		            )
        				));
					if( !empty($result['result']) ) {
						$order_code = $result['result'][0]['_id']['code'];
						$sale_price = isset($result['result'][0]['products'][0]['sub_total']) ? $result['result'][0]['products'][0]['sub_total'] : 0;
					}
				} catch (Exception $e) {
					$result = $this->Salesorder->select_one(array('_id' => $value['salesorder_id']), array('products','code'));
					if( isset($result['products']) && is_array($result['products']) ) {
						foreach($result['products'] as $product) {
							if( isset($product['deleted']) && $product['deleted'] ) continue;
							if( !isset($product['products_id']) || !isset($product['sku']) ) continue;
							if( !preg_match('/SH/', $product['sku']) ) continue;
							$order_code = $result['code'];
							$sale_price = isset($product['sub_total']) ? $product['sub_total'] : 0;
							break;
						}
					}
				}
				unset($result);
            }
            $group[$company_id]['shipping'][$value['code']]['order_code'] = $order_code;
            $group[$company_id]['shipping'][$value['code']]['sale_price'] = $sale_price;
            $group[$company_id]['shipping'][$value['code']]['profit'] = $sale_price - $group[$company_id]['shipping'][$value['code']]['shipping_cost'];
            if( $sale_price != 0 ) {
            	$group[$company_id]['shipping'][$value['code']]['percent'] = ($group[$company_id]['shipping'][$value['code']]['profit'] /  $sale_price ) *100;
            } else {
            	if( $group[$company_id]['shipping'][$value['code']]['profit']  !=  0 ) {
            		$group[$company_id]['shipping'][$value['code']]['percent'] = -100;
            	} else {
            		$group[$company_id]['shipping'][$value['code']]['percent'] = 0;
            	}
            }
        }
        $total_number = $grand_total_sale = $grand_total_ship = 0;
        foreach ($group as $value) {
            $html .= '
            <table class="table_content">
               <tbody>
                  <tr class="tr_right_none" style="background: #911b12;color: white;height: 29px;line-height: 29px;font-weight: bold;">
                     <td width="35%">
                        Company
                     </td>
                     <td width="25%">
                        Contact
                     </td>
                     <td width="15%">
                        Phone
                     </td>
                     <td width="15%">
                        Email
                     </td>
                     <td class="right_text" width="9%">
                        No. of Page
                     </td>
                  </tr>
                  <tr class="bg_2">
                     <td>' . $value['company_name'] . '</td>
                     <td>' . $value['contact_name'] . '</td>
                     <td>' . $value['phone'] . '</td>
                     <td>' . $value['email'] . '</td>
                     <td class="right_text">' . $value['number_of_shipment'] . '</td>
                  </tr>
               </tbody>
            </table>';
            $html .= '<table class="table_content" >
                        <tbody>
                          <tr class="tr_right_none" style="background: #911b12;color: white;height: 29px;line-height: 29px;font-weight: bold;">
                             <td width="7%">
                                SH#
                             </td>
                             <td class="center_text" width="15%">
                                Shipping Date
                             </td>
                             <td>
                                SO #
                             </td>
                             <td class="right_text" width="8%">
                                Sale price
                             </td>
                             <td class="right_text" width="8%">
                                Cost
                             </td>
                             <td class="right_text" width="10%">
                                Profit / Loss
                             </td>
                             <td class="right_text" width="8%">
                                %
                             </td>
                          </tr>';
            $i =  $sum = $total_sale = $total_ship = 0;
            foreach ($value['shipping'] as $shipping) {
            	$total_sale += $shipping['sale_price'];
            	$total_ship += $shipping['shipping_cost'];
                $html .= '
                      <tr class="content_asset bg_' . ($i % 2 == 0 ? '1' : '2') . '">
                         <td>' . $shipping['code'] . '</td>
                         <td class="center_text">' . $shipping['shipping_date'] . '</td>
                         <td>' . $shipping['order_code'] . '</td>
                         <td class="right_text">' . number_format($shipping['sale_price'], 2) . '</td>
                         <td class="right_text">' . number_format($shipping['shipping_cost'], 2)  . '</td>
                         <td class="right_text">' . number_format($shipping['profit'], 2)  . '</td>
                         <td class="right_text">' . number_format($shipping['percent'], 2)  . '%</td>
                      </tr>';
                $i++;
            }
            $total_profit = $total_sale - $total_ship;
            if( $total_sale != 0 ) {
            	$total_percent = ($total_profit /  $total_sale ) *100;
            } else {
            	if( $total_profit  !=  0 ) {
            		$total_percent = -100;
            	} else {
            		$total_percent = 0;
            	}
            }
            $total_number += $i;
            $grand_total_sale += $total_sale;
            $grand_total_ship += $total_ship;
            $html .= '
                            <tr class="bg_' . ($i % 2 == 0 ? '1' : '2') . '">
                             <td colspan="3" class="left_text bold_text">' . $i . ' record(s) listed</td>
                             <td class="bold_text right_text">'.number_format($total_sale, 2).'</td>
                             <td class="bold_text right_text">'.number_format($total_ship, 2).'</td>
                             <td class="bold_text right_text">'.number_format($total_profit, 2).'</td>
                             <td class="bold_text right_text">'.number_format($total_percent, 2).'%</td>
                          </tr>
                        </tbody>
                    </table>
                    <br />
                    <br />
           			<div class="line" style="margin-bottom: 10px;"></div>';
        }
        $grand_total_profit = $grand_total_sale - $grand_total_ship;
        if( $grand_total_sale != 0 ) {
        	$grand_total_percent = ($grand_total_profit /  $grand_total_sale ) *100;
        } else {
        	if( $grand_total_profit  !=  0 ) {
        		$grand_total_percent = -100;
        	} else {
        		$grand_total_percent = 0;
        	}
        }
        $html .= '
            <div class="line" style="margin-bottom: 5px;"></div>
            <table class="table_content">
            	<tr>
            		<td colspan="2"></td>
            		<td class="right_text ">Total sale</td>
            		<td class="right_text ">Total cost</td>
            		<td class="right_text ">Profit / Loss</td>
            		<td class="right_text ">%</td>
            	</tr>
                <tr style="background-color: #333; color: white">
                    <td class="bold_text" width="40%">'.$total_number.' record(s) listed</td>
                    <td class="right_text bold_text">'.number_format($grand_total_sale, 2).'</td>
                    <td class="right_text bold_text">'.number_format($grand_total_ship, 2).'</td>
                    <td class="right_text bold_text">'.number_format($grand_total_sale, 2).'</td>
                    <td class="right_text bold_text">'.number_format($grand_total_profit, 2).'</td>
                    <td class="right_text bold_text">'.number_format($grand_total_percent, 2).'</td>
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
        $arr_data['report_name'] = 'SH Report By Customer (Sale Detailed)';
        $arr_data['report_file_name'] = 'SH_'.md5(time());
        $arr_data['excel_url'] = URL.'/shippings/detailed_sale_customer_excel';
        $arrData['pdf'] = $arr_data;
        $arrData['excel'] = $group;
        return $arrData;
    }
    public function detailed_sale_customer_excel(){
    	$arr_shippings = Cache::read('shippings_sales_excel');
        Cache::delete('shippings_sales_excel');
        if(!$arr_shippings){
            echo 'No data';die;
        }
        App::import('Vendor', 'phpexcel/PHPExcel');
        $objPHPExcel = new PHPExcel();
        // Set document properties
        $objPHPExcel->getProperties()->setCreator("")
                                     ->setLastModifiedBy("")
                                     ->setTitle("Shipping Report By Customer")
                                     ->setSubject("Shipping Report By Customer")
                                     ->setDescription("Shipping Report By Customer")
                                     ->setKeywords("Shipping Report By Customer")
                                     ->setCategory("Shipping Report By Customer");
        $worksheet = $objPHPExcel->getActiveSheet();
        $i = 2;
        $arr_companies = array();
        $this->selectModel('Company');
        $total_count = $grand_total_sale = $grand_total_ship = 0;
        foreach($arr_shippings as $shipping) {
	        $worksheet->setCellValue("A$i",'Company')
	                        ->setCellValue("C$i",'Contact')
	                        ->setCellValue("E$i",'Phone')
	                        ->setCellValue("F$i",'Email')
	                        ->setCellValue("G$i",'No. of Page')
	                        ->mergeCells("A$i:B$i")
	                        ->mergeCells("C$i:D$i");
	        $from = $i;
	        ++$i;
	        $worksheet->setCellValue("A$i", $shipping['company_name'])
	                        ->setCellValue("C$i", $shipping['contact_name'])
	                        ->setCellValue("E$i", $shipping['phone'])
	                        ->setCellValue("F$i", $shipping['email'])
	                        ->setCellValue("G$i", $shipping['number_of_shipment'])
	                        ->mergeCells("A$i:B$i")
	                        ->mergeCells("C$i:D$i");
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
	        $worksheet->getStyle("A$from:G$i")->applyFromArray($styleArray);
	        $count = $total_sale = $total_ship = 0;
            ++$i;
            $worksheet->setCellValue("A$i",'SH#')
                        ->setCellValue("B$i",'Shipping Date')
                        ->setCellValue("C$i",'SO#')
                        ->setCellValue("D$i",'Sale price')
                        ->setCellValue("E$i",'Cost')
                        ->setCellValue("F$i",'Profit / Loss')
                        ->setCellValue("G$i",'%');
	        foreach ($shipping['shipping'] as $shipping) {
                ++$i;
            	$total_sale += $shipping['sale_price'];
            	$total_ship += $shipping['shipping_cost'];
	            $worksheet->setCellValue("A$i", $shipping['code'])
                        ->setCellValue("B$i", $shipping['shipping_date'])
                        ->setCellValue("C$i", $shipping['order_code'])
                        ->setCellValue("D$i", $shipping['sale_price'])
                        ->setCellValue("E$i", $shipping['shipping_cost'])
                        ->setCellValue("F$i", $shipping['profit'])
                        ->setCellValue("G$i", $shipping['percent'] / 100);
                ++$count;
            }
            $total_count += $count;
            $grand_total_sale += $total_sale;
            $grand_total_ship += $total_ship;
            ++$i;
            $total_profit = $total_sale - $total_ship;
            if( $total_sale != 0 ) {
            	$total_percent = ($total_profit /  $total_sale ) *100;
            } else {
            	if( $total_profit  !=  0 ) {
            		$total_percent = -100;
            	} else {
            		$total_percent = 0;
            	}
            }
            $worksheet->setCellValue("A$i","$count record(s) listed")
	                        ->setCellValue("D$i", $total_sale)
	                        ->setCellValue("E$i", $total_ship)
	                        ->setCellValue("F$i", $total_profit)
	                        ->setCellValue("G$i", $total_percent / 100)
	                        ->mergeCells("A$i:C$i");
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
	        $worksheet->getStyle("A$from:G$i")->applyFromArray($styleArray);
	        $i+=2;
	    	$worksheet->getStyle("G$from:G$i")->getNumberFormat()->setFormatCode("0.00%");
        }
        $styleArray = array(
                'font'  => array(
                    'size'  => 11,
                    'name'  => 'Century Gothic',
                )
	        );
        $grand_total_profit = $grand_total_sale - $grand_total_ship;
        if( $grand_total_sale != 0 ) {
        	$grand_total_percent = ($grand_total_profit /  $grand_total_sale ) *100;
        } else {
        	if( $grand_total_profit  !=  0 ) {
        		$grand_total_percent = -100;
        	} else {
        		$grand_total_percent = 0;
        	}
        }
        $worksheet->setCellValue("A$i","$total_count record(s) listed")
	                        ->setCellValue("D$i", $grand_total_sale)
	                        ->setCellValue("E$i", $grand_total_ship)
	                        ->setCellValue("F$i", $grand_total_profit)
	                        ->setCellValue("G$i", $grand_total_percent / 100)
	                        ->mergeCells("A$i:C$i");
	    $worksheet->getStyle("G$i")->getNumberFormat()->setFormatCode("0.00%");
	    $worksheet->getStyle("A$from:G$i")->applyFromArray($styleArray);
	    $worksheet->getStyle("D2:D$i")->getNumberFormat()->setFormatCode("#,##0.00");
	    $worksheet->getStyle("E2:E$i")->getNumberFormat()->setFormatCode("#,##0.00");
	    $worksheet->getStyle("F2:F$i")->getNumberFormat()->setFormatCode("#,##0.00");
        for($i = 'A'; $i !== 'H'; $i++){
            $worksheet->getColumnDimension($i)
                            ->setAutoSize(true);
        }
        $objPHPExcel->setActiveSheetIndex(0);
        $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
        $objWriter->save(APP.DS.'webroot'.DS.'upload'.DS.'shippings_sales_excel.xlsx');
        $this->redirect('/upload/shippings_sales_excel.xlsx');
        die;
    }
    public function detailed_area_customer_report($shipping, $data) {
        $html = '';
        $i = 0;
        //Có 2 trường hợp là công ty hoặc cá nhân,
		//nên phải lọc theo _id tương ứng, ưu tiên company nếu có cả 2
		//vì shipping chỉ lưu first name, nên phải dò từ Contact
        foreach ($shipping as $value) {
        	if(!is_object($value['company_id'])) continue;
        	$province = '(empty)';
			if(isset($value['shipping_address'][0]['shipping_province_state']))
			$province = $value['shipping_address'][0]['shipping_province_state'];
			if(!isset($group[$province]['number_of_shipment']))
				$group[$province]['number_of_shipment'] = 0;
			$group[$province]['number_of_shipment']++;
            //Tach theo shipping code
            $group[$province]['shipping'][$value['code']]['code'] = $value['code'];
            $group[$province]['shipping'][$value['code']]['shipping_type'] = $value['shipping_type'];
            $group[$province]['shipping'][$value['code']]['return_status'] = (isset($value['return_status']) && $value['return_status']==1 ? 'X' : '');
            $group[$province]['shipping'][$value['code']]['shipping_date'] = (isset($value['shipping_date']) && is_object($value['shipping_date']) ? $this->opm->format_date($value['shipping_date']->sec)  : '');
            $group[$province]['shipping'][$value['code']]['shipping_status'] = $value['shipping_status'];
            $group[$province]['shipping'][$value['code']]['carrier_name'] = (isset($value['carrier_name']) ? $value['carrier_name'] : '');
            $group[$province]['shipping'][$value['code']]['tracking_no'] = (isset($value['tracking_no']) ? $value['tracking_no'] : '');
        }
        foreach ($group as $province=>$value) {
            $html .= '
            <table class="table_content">
               <tbody>
                  <tr class="tr_right_none" style="background: #911b12;color: white;height: 29px;line-height: 29px;font-weight: bold;">
                     <td width="60%">
                        Province
                     </td>
                     <td class="right_text">
                        No. of SH
                     </td>
                  </tr>
                  <tr class="bg_2">
                     <td>' . $province . '</td>
                     <td class="right_text">' . $value['number_of_shipment'] . '</td>
                  </tr>
               </tbody>
            </table>';
            $html .= '<table class="table_content" >
                        <tbody>
                          <tr class="tr_right_none" style="background: #911b12;color: white;height: 29px;line-height: 29px;font-weight: bold;">
                             <td width="7%">
                                SH#
                             </td>
                             <td class="center_text" width="15%">
                                Type
                             </td>
                              <td class="center_text" width="5%">
                                Return
                             </td>
                             <td class="center_text" width="15%">
                                Date
                             </td>
                             <td class="center_text" width="15%">
                                Status
                             </td>
                             <td width="20%">
                                Carrier
                             </td>
                             <td class="right_text" width="15%">
                                Tracking no
                             </td>
                          </tr>';
            $i = 0;
            $sum = 0;
            foreach ($value['shipping'] as $shipping) {
                $html .= '
                      <tr class="content_asset bg_' . ($i % 2 == 0 ? '1' : '2') . '">
                         <td>' . $shipping['code'] . '</td>
                         <td class="center_text">' . $shipping['shipping_type'] . '</td>
                         <td class="center_text bold_text">' . $shipping['return_status'] . '</td>
                         <td class="center_text">' . $shipping['shipping_date'] . '</td>
                         <td class="center_text">' . $shipping['shipping_status'] . '</td>
                         <td class="center_text">' . $shipping['carrier_name'] . '</td>
                         <td class="right_text">' . $shipping['tracking_no'] . '</td>
                      </tr>';
                $i++;
            }
            $html .= '
                            <tr class="bg_' . ($i % 2 == 0 ? '1' : '2') . '">
                             <td colspan="7" class="left_text bold_text right_none">' . $i . ' record(s) listed</td>
                          </tr>
                        </tbody>
                    </table>
                    <br />
                    <br />
           			<div class="line" style="margin-bottom: 10px;"></div>';
        }
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
        $arr_data['report_name'] = 'Shipping Report By Area Customer (Detailed)';
        $arr_data['report_file_name'] = 'SH_'.md5(time());
        return $arr_data;
    }
    public function option_summary_product_find($type = ''){
    	$arr_data['shippings_type'] = $this->Setting->select_option_vl(array('setting_value'=>'shipping_types'));
        $arr_data['shippings_status'] = $this->Setting->select_option_vl(array('setting_value'=>'shipping_statuses'));
        $arr_data['product_category'] = $this->Setting->select_option_vl(array('setting_value' => 'product_category'));
        $this->set('arr_data',$arr_data);
        if($type=='category')
            $this->render('../Shippings/option_summary_category_find');
    }
    public function option_detailed_product_find($type = ''){
    	$arr_data['shippings_type'] = $this->Setting->select_option_vl(array('setting_value'=>'shipping_types'));
        $arr_data['shippings_status'] = $this->Setting->select_option_vl(array('setting_value'=>'shipping_statuses'));
        $arr_data['product_category'] = $this->Setting->select_option_vl(array('setting_value' => 'product_category'));
        $this->set('arr_data',$arr_data);
        if($type=='category')
            $this->render('../Shippings/option_detailed_category_find');
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
            $arr_data = Cache::read('shippings_product_report_'.$type);
            Cache::delete('shippings_product_report_'.$type);
        } else {
			if (isset($_POST) && !empty($_POST)) {
				$arr_post = $_POST;
				$arr_where = array( 'company_id' => array('$nin'=>array('',null)));
				if(isset($arr_post['type']) && $arr_post['type']!='')
					$arr_where['shipping_type'] = $arr_post['type'];
				if(isset($arr_post['status']) && $arr_post['status'])
					$arr_where['shipping_status'] = $arr_post['status'];
				//Check loại trừ cancel thì bỏ các status bên dưới
				if(isset($arr_post['is_not_cancel']) && $arr_post['is_not_cancel']==1){
					$arr_where['shipping_status'] = array('$nin'=>array('Delivered','Cancelled'));
					//Tuy nhiên nếu ở ngoài combobox nếu có chọn, thì ưu tiên nó, set status lại
					if(isset($arr_post['status']) && $arr_post['status']!='')
						$arr_where['shipping_status'] = $arr_post['status'];
				}
				if(isset($arr_post['is_return']) && $arr_post['is_return']==1)
					$arr_where['return_status'] = 1;
				//Có hai trường hợp, hoặc là receiver (type là in) hoặc là sender (type là out)
				if(isset($arr_post['company_receiver']) && $arr_post['company_receiver']!=''){
					$arr_where['company_name'] = new MongoRegex('/'.trim($arr_post['company_receiver']).'/i');
					$arr_where['shipping_type'] = 'In';
				}
				if(isset($arr_post['contact_receiver']) && $arr_post['contact_receiver']!=''){
					$arr_where['contact_name'] = new MongoRegex('/'.trim($arr_post['contact_receiver']).'/i');
					$arr_where['shipping_type'] = 'In';
				}
				if(isset($arr_post['company_sender']) && $arr_post['company_sender']!=''){
					$arr_where['company_name'] = new MongoRegex('/'.trim($arr_post['company_sender']).'/i');
					$arr_where['shipping_type'] = 'Out';
				}
				if(isset($arr_post['contact_sender']) && $arr_post['contact_sender']!=''){
					$arr_where['contact_name'] = new MongoRegex('/'.trim($arr_post['contact_sender']).'/i');
					$arr_where['shipping_type'] = 'Out';
				}
				if(isset($arr_post['carrier']) && $arr_post['carrier']!='')
					$arr_where['carrier_name'] = new MongoRegex('/'.trim($arr_post['carrier']).'/i');
				if(isset($arr_post['job_no']) && $arr_post['job_no']!='')
					$arr_where['job_number'] = $arr_post['job_no'];
				//Tìm chính xác ngày
				//Vì để = chỉ tìm đc 01/01/1969 00:00:00 nên phải cộng cho 23:59:59 rồi tìm trong khoảng đó
				if(isset($arr_data['date_equals']) && $arr_data['date_equals']!=''){
					$date_equals = $arr_post['date_equals'];
					$date_equals = new MongoDate(strtotime(date('Y-m-d',strtotime($date_equals))));
					$date_equals_to = new MongoDate($date_equals->sec + DAY - 1);
					$arr_where['shipping_date']['$gte'] = $date_equals;
					$arr_where['shipping_date']['$lt'] = $date_equals_to;
				}
				else if(isset($arr_post['date_equals']) && $arr_post['date_equals'] == '') { //Ngày nằm trong khoảng
					//neu chi nhap date from
					if(isset($arr_post['date_from']) && $arr_post['date_from']!=''){
						$date_from = new MongoDate(strtotime(date('Y-m-d',strtotime($arr_post['date_from']))));
						$arr_where['shipping_date']['$gte'] = $date_from;
					}
					//neu chi nhap date to
					if(isset($arr_post['date_to']) && $arr_post['date_to'] != '') {
						$date_to = new MongoDate(strtotime(date('Y-m-d',strtotime($arr_post['date_to']))));
						$date_to = new MongoDate($date_to->sec + DAY -1);
						$arr_where['shipping_date']['$lte'] = $date_to;
					}
				}
				if(isset($arr_post['our_rep']) && $arr_post['our_rep']!='')
					$arr_where['our_rep'] = new MongoRegex('/'.$arr_post['our_rep'].'/i');
				if(isset($arr_post['our_csr']) && $arr_post['our_csr']!='')
					$arr_where['our_csr'] = new MongoRegex('/'.$arr_post['our_csr'].'/i');
				//Kiểm tra nếu có thông tin liên quan đến product tồn tại
                $pro_where = array();
                if(isset($arr_post['product'])&&$arr_post['product']!='')
                    $pro_where['code'] = trim($arr_post['product']);
                if(isset($arr_post['product_name'])&&$arr_post['product_name']!='')
                    $pro_where['name'] = new MongoRegex('/' . trim($arr_post['product_name']) . '/i');
                if(isset($arr_post['product_category'])&&$arr_post['product_category']!='')
                    $pro_where['category'] = new MongoRegex('/'.$arr_post['product_category'].'/i');
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
                $shipping = $this->opm->collection->aggregate(
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
                            '$project'=>array('shipping_status'=>'$shipping_status','code'=>'$code','company_name'=>'$company_name','company_id'=>'$company_id','shipping_date'=>'$shipping_date','sum_sub_total'=>'$sum_sub_total','products'=>'$products')
                        ),
                        array(
                            '$group'=>array(
                                          '_id'=>array('_id'=>'$_id','shipping_status'=>'$shipping_status','code'=>'$code','company_name'=>'$company_name','company_id'=>'$company_id','shipping_date'=>'$shipping_date','sum_sub_total'=>'$sum_sub_total'),
                                          'products'=>array('$push'=>'$products')
                                        )
                        )
                    );
				if(empty($shipping['result'])) {
                    echo 'empty';
                    die;
				} else if(!$this->request->is('ajax')) {
					$shipping = $shipping['result'];
                    if ($arr_post['report_type'] == 'summary'){
                        $arr_data = $this->summary_product_report($shipping,$arr_post);
                        Cache::write('shippings_product_report_'.$type, $arr_data);
                    }
                    else if ($arr_post['report_type'] == 'detailed'){
                        $arr_data = $this->detailed_product_report($shipping,$arr_post);
                        Cache::write('shippings_product_report_'.$type, $arr_data);
                    }
                    else if($arr_post['report_type'] == 'category_summary'){
                        $arr_data = $this->summary_category_product_report($shipping,$arr_post);
                        Cache::write('shippings_product_report_'.$type, $arr_data);
                    }
                    else if($arr_post['report_type'] == 'category_detailed'){
                        $arr_data = $this->detailed_category_product_report($shipping,$arr_post);
                        Cache::write('shippings_product_report_'.$type, $arr_data);
                    }
				}
			}
		}
		if($this->request->is('ajax'))
            die;
        else
            $this->render_pdf($arr_data);
    }
    public function summary_product_report($arr_shipping,$data){
        $html = '';
        $i = $sum = 0;
        $this->selectModel('Product');
        $category = $this->Setting->select_option_vl(array('setting_value' => 'product_category'));
        $arr_data = array();
        foreach($arr_shipping as $shipping){
            foreach($shipping['products'] as $product){
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
        $arr_pdf['report_name'] = 'Shipping Report By Product (Summary)';
        $arr_pdf['report_file_name'] = 'SH_'.md5(time());
        return $arr_pdf;
    }
    public function summary_category_product_report($arr_shipping,$data){
        $html = '';
        $i = $sum = 0;
        $this->selectModel('Product');
        $category = $this->Setting->select_option_vl(array('setting_value' => 'product_category'));
        $arr_data = array();
        foreach($arr_shipping as $shipping){
            foreach($shipping['products'] as $product){
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
        $arr_pdf['report_name'] = 'SH Report By Category Product (Summary)';
        $arr_pdf['report_file_name'] = 'SH_'.md5(time());
        return $arr_pdf;
    }
    public function detailed_product_report($arr_shipping,$data){
        $i = $sum = 0;
        $html = '';
        $this->selectModel('Product');
        $category = $this->Setting->select_option_vl(array('setting_value' => 'product_category'));
        $total_num_of_shippings = $total_sum_sub_total = 0;
        $arr_data = $arr_pdf = array();
        foreach($arr_shipping as $shipping){
            foreach($shipping['products'] as $product){
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
                $arr_data[$product['code']]['shippings'][] = array_merge($shipping['_id'], array('unit_price'=>$product['unit_price'],'quantity'=>$product['quantity'],'sub_total'=>$product['sub_total']));
                if(!isset( $arr_data[$product['code']]['no_of_sh']))
                    $arr_data[$product['code']]['no_of_sh'] = array();
                $arr_data[$product['code']]['no_of_sh'][$shipping['_id']['code']] = 1;
            }
        }

        foreach ($arr_data as $value) {
            $total_num_of_shippings += count($value['shippings']);
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
                        No. of SH
                     </td>
                     <td class="right_text" colspan="3" width="20%">
                        Group total (ex. tax)
                     </td>
                  </tr>
                  <tr class="bg_2">
                     <td>' . $value['code'] . '</td>
                     <td>' . $value['products_name'] . '</td>
                     <td>' . (isset($category[$product['category']]) ? $category[$product['category']] : '') . '</td>
                     <td class="right_text">' . count($value['no_of_sh']) . '</td>
                     <td colspan="3" class="right_text">' . $this->opm->format_currency($value['sub_total']) . '</td>
                  </tr>
               </tbody>
            </table>';
            $html .= '<table class="table_content" >
                        <tbody>
                          <tr class="tr_right_none" style="background: #979797;color: white;height: 29px;line-height: 29px;font-weight: bold;">
                             <td width="10%">
                                SH#
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
            foreach ($value['shippings'] as $shipping) {
                $sum += $shipping['sub_total'];
                $total_quantity += $shipping['quantity'];
                $html .= '
                      <tr class="content_asset bg_' . ($i % 2 == 0 ? '1' : '2') . '">
                         <td>' . $shipping['code'] . '</td>
                         <td>' . $shipping['company_name'] . '</td>
                         <td class="center_text">' . $this->opm->format_date($shipping['shipping_date']->sec) . '</td>
                         <td class="right_text">' . $this->opm->format_currency((float)$shipping['unit_price']) . '</td>
                         <td class="right_text">' . $shipping['quantity'] . '</td>
                         <td colspan="3" class="right_text">' . $this->opm->format_currency((float)$shipping['sub_total']) . '</td>
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
                        <td class="bold_text right_none" width="70%">'.$total_num_of_shippings.' record(s) listed</td>
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
        $arr_pdf['report_name'] = 'Shipping Report By Product (Detailed)';
        $arr_pdf['report_file_name'] = 'SH_'.md5(time());
        return $arr_pdf;
    }
    public function detailed_category_product_report($arr_shipping,$data){
        $i = $sum = 0;
        $html = '';
        $this->selectModel('Product');
        $category = $this->Setting->select_option_vl(array('setting_value' => 'product_category'));
        $total_num_of_shippings = $total_sum_sub_total = 0;
        $arr_data = $arr_pdf = array();
        foreach($arr_shipping as $shipping){
            foreach($shipping['products'] as $product){
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
                $arr_data[$product_category]['shippings'][] = array_merge($shipping['_id'], array('unit_price'=>$product['unit_price'],'quantity'=>$product['quantity'],'sub_total'=>$product['sub_total']));
                if(!isset( $arr_data[$product_category]['no_of_sh']))
                    $arr_data[$product_category]['no_of_sh'] = array();
                $arr_data[$product_category]['no_of_sh'][$shipping['_id']['code']] = 1;
            }
        }

        foreach ($arr_data as $value) {
            $total_num_of_shippings += count($value['shippings']);
            $html .= '
            <table class="table_content">
               <tbody>
                  <tr class="tr_right_none" style="background: #911b12;color: white;height: 29px;line-height: 29px;font-weight: bold;">
                     <td width="25%">
                        Category Name
                     </td>
                     <td class="right_text" width="15%">
                        No. of SH
                     </td>
                     <td class="right_text" colspan="3" width="20%">
                        Group total (ex. tax)
                     </td>
                  </tr>
                  <tr class="bg_2">
                     <td>' . $value['category_name'] . '</td>
                     <td class="right_text">' . count($value['no_of_sh']) . '</td>
                     <td colspan="3" class="right_text">' . $this->opm->format_currency($value['sub_total']) . '</td>
                  </tr>
               </tbody>
            </table>';
            $html .= '<table class="table_content" >
                        <tbody>
                          <tr class="tr_right_none" style="background: #979797;color: white;height: 29px;line-height: 29px;font-weight: bold;">
                             <td width="10%">
                                SH#
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
            foreach ($value['shippings'] as $shipping) {
                $sum += $shipping['sub_total'];
                $total_quantity += $shipping['quantity'];
                $html .= '
                      <tr class="content_asset bg_' . ($i % 2 == 0 ? '1' : '2') . '">
                         <td>' . $shipping['code'] . '</td>
                         <td>' . $shipping['company_name'] . '</td>
                         <td class="center_text">' . $this->opm->format_date($shipping['shipping_date']->sec) . '</td>
                         <td class="right_text">' . $this->opm->format_currency((float)$shipping['unit_price']) . '</td>
                         <td class="right_text">' . $shipping['quantity'] . '</td>
                         <td colspan="3" class="right_text">' . $this->opm->format_currency((float)$shipping['sub_total']) . '</td>
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
                        <td class="bold_text right_none" width="70%">'.$total_num_of_shippings.' record(s) listed</td>
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
        $arr_pdf['report_name'] = 'SH Report By Category Product (Detailed)';
        $arr_pdf['report_file_name'] = 'SH_'.md5(time());
        return $arr_pdf;
    }
    /*
	End report
    */
    public function check_condition_shipping()
    {
    	$this->selectModel('Shipping');
    	$id = $this->get_id();
    	if($id=='')
    		$shipping = $this->Shipping->select_one(array(),array(),array('_id'=>-1));
    	$shipping = $this->Shipping->select_one(array('_id' => new MongoId($id)));
    	if($shipping!='')
    	{

    		if($shipping['shipping_type']=='In')
    			return array('err1');
    		else if($shipping['company_id']==''
    				&&$shipping['contact_id']=='')
    			return array('err3');
    		else if($shipping['products']=='')
    			return array('err2');

    		else if(isset($shipping['invoice_id'])&&$shipping['invoice_id']!='')
    			return array('err4',$shipping['invoice_id']);
    		return $shipping;
    	}
    	return false;

    }




    public function create_sales_invoice()
    {
    	$this->autoRender = FALSE;
    	$check = $this->check_condition_shipping();
    	if(@$check[0] == 'err1')
    	{
    		echo json_encode(array('status'=>'error','mess'=>'Sales invoices are not created from incoming shippings.'));
    	}
    	else if(@$check[0] == 'err2')
    	{
    		echo json_encode(array('status'=>'error','mess'=>'No items have been entered on this transaction yet.'));
    	}
    	else if(@$check[0] == 'err3')
    	{
    		echo json_encode(array('status'=>'error','mess'=>'This function cannot be performed as there is no company or contact linked to this record.'));
    	}
    	else if(@$check[0] == 'err4')
    	{
    		echo json_encode(array('status'=>'exist','mess'=>'This shipping record is already linked to a sales invoice. View sales invoice?','url'=>URL.'/salesinvoices/entry/'.$check[1]));
    	}
    	else if(is_array($check))
    	{
    		$arr_save = $check;
    		$this->selectModel('Salesinvoice');
			$arr_save['code'] = $this->Salesinvoice->get_auto_code('code');
    		$arr_save['invoice_type'] =  "Invoice";
    		$arr_save['invoice_status'] = 'Invoice';
    		$arr_save['invoice_date'] = new MongoDate();
    		$arr_save['shipping_id'] = new MongoId($check['_id']);
    		$arr_save['shipping_code'] = $check['code'];
    		$arr_save['job_id'] = '';
    		$arr_save['job_name'] = '';
    		$arr_save['job_number'] = '';
    		$arr_save['paid_date'] = '';
    		$arr_save['payment_due_date'] = '';
    		$arr_save['payment_term'] = '';
    		$arr_save['salesorder_id'] = '';
    		$arr_save['salesorder_name'] = '';
    		$arr_save['salesorder_number'] = '';
    		//Hiện tại product chưa thêm được nên ko có các giá trị tiền
    		//Và ko có cả các field bên dưới
    		// => sales invoice sẽ ko có giá trị tiền
    		$arr_save['sum_amount']  = (isset($check['sum_amount']) ? $check['sum_amount'] : 0);
			$arr_save['sum_sub_total']  = (isset($check['sum_sub_total']) ? $check['sum_sub_total'] : 0);
			$arr_save['sum_tax'] = (isset($check['sum_tax']) ? $check['sum_tax'] : 0);
			$arr_save['tax']  = (isset($check['tax']) ? $check['tax'] : 0);
			$arr_save['taxval'] = (isset($check['taxval']) ? $check['taxval'] : 0);
    		unset($arr_save['_id']);
    		unset($arr_save['carrier_id']);
    		unset($arr_save['carrier_name']);
    		unset($arr_save['date_modifide']);
    		unset($arr_save['invoice_id']);
    		unset($arr_save['invoice_name']);
    		unset($arr_save['modified_by']);
    		unset($arr_save['received_date']);
    		unset($arr_save['return_status']);
    		unset($arr_save['shipping_date']);
    		unset($arr_save['shipping_status']);
    		unset($arr_save['shipping_type']);
    		unset($arr_save['tracking_no']);
    		unset($arr_save['traking']);
    		if($this->Salesinvoice->save($arr_save))
    		{
    			$id = $this->Salesinvoice->mongo_id_after_save;
    			$check['invoice_code'] = $arr_save['code'];
    			$check['invoice_id'] = $id;
    			$this->selectModel('Shipping');
    			$this->Shipping->save($check);
    			echo json_encode(array('status'=>'ok','url'=>URL.'/salesinvoices/entry/'.$id));
    		}
    	}
    	die;
    }

	function view_minilist(){
		if(!isset($_GET['print_pdf'])){
			$arr_where = $this->arr_search_where();
			$shippings = $this->opm->select_all(array(
													'arr_where' => $arr_where,
													'arr_field' => array('code','shipping_type','return_status','phone','name','customer_po_no','shipping_date','carrier_name','shipping_status'),
													'arr_order' => array('_id'=>1),
													'limit'     => 2000
													));

			$arr_data = array();
			if($shippings->count() > 0){
				$group = array();
				$html= '';
				$i = 0;
				foreach($shippings as $shipping){
					$html .= '<tr class="'.($i%2==0 ? 'bg_2' : 'bg_1').'">';
					$html .= '<td>'.(isset($shipping['code']) ? $shipping['code'] : '') .'</td>';
					$html .= '<td>'.(isset($shipping['shipping_type']) ? $shipping['shipping_type'] : '') .'</td>';
					$html .= '<td class="center_text bold_text">'.(isset($shipping['return_status'])&&$shipping['return_status']==1 ? 'X' : '') .'</td>';
					$html .= '<td>'.(isset($shipping['name']) ? $shipping['name'] : '') .'</td>';
					$html .= '<td>'.(isset($shipping['customer_po_no']) ? $shipping['customer_po_no'] : '') .'</td>';
					$html .= '<td>'.(isset($shipping['phone']) ? $shipping['phone'] : '') .'</td>';
					$html .= '<td class="center_text">'.(isset($shipping['shipping_date'])&&is_object($shipping['shipping_date']) ? date('m/d/Y',$shipping['shipping_date']->sec):'') .'</td>';
					$html .= '<td>'.(isset($shipping['carrier_name']) ? $shipping['carrier_name'] : '') .'</td>';
					$html .= '<td>'.(isset($shipping['shipping_status']) ? $shipping['shipping_status'] : '') .'</td>';
					$html .= '</tr>';
	                $i++;
				}
				$html .='<tr class="last">
					<td colspan="9" class="bold_text right_none">'.$i.' record(s) listed.</td>
				</tr>';
				$arr_data['title'] = array('Ref No','Type'=>'text-align: left', 'Return'=>'text-align: center', 'Name', 'PO No'=>'text-align: left', 'Phone', 'Date'=>'text-align: center',  'Carrier'=>'text-align: left', 'Status'=>'text-align: left');
				$arr_data['content'] = $html;
				$arr_data['report_name'] = 'Shipping Mini  Listing';
				$arr_data['report_file_name'] = 'SH_'.md5(time());
				$arr_data['report_orientation'] = 'landscape';
				Cache::write('shippings_minilist', $arr_data);
	        }
    	} else {
    		$arr_data = Cache::read('shippings_minilist');
    		Cache::delete('shippings_minilist');
    	}
		$this->render_pdf($arr_data);
	}

	function rebuild_job(){
		$arr_shipping = $this->opm->select_all(array(
								'arr_where' => array('salesorder_id'=>array('$nin'=>array(null,''))),
								'arr_field'	=> array('salesorder_id'),
								'limit'		=> 9999
						));
		$this->selectModel('Salesorder');
		echo 'Total records: '.$arr_shipping->count();
		$i = 0;
		foreach($arr_shipping as $shipping){
			if(!isset($shipping['salesorder_id']) || !is_object($shipping['salesorder_id'])) continue;
			$salesorder = $this->Salesorder->select_one(array('_id'=>$shipping['salesorder_id']),array('job_number','job_name','job_id'));
			$arr_data = array('_id'=>$shipping['_id']);
			$arr_data['job_id'] = isset($salesorder['job_id']) ? $salesorder['job_id'] : '' ;
			$arr_data['job_name'] = isset($salesorder['job_name']) ? $salesorder['job_name'] : '' ;
			$arr_data['job_number'] = isset($salesorder['job_number']) ? $salesorder['job_number'] : '' ;
			$this->opm->rebuild_collection($arr_data);
			$i++;
		}
		echo '<br />'.$i.' was rebuild.';
		die;
	}

	function update_customer_po(){
		$this->selectModel('Shipping');
		$this->selectModel('Salesorder');
		$this->selectModel('Salesinvoice');
		$arr_ships = $this->Shipping->collection->distinct('salesorder_id');
		echo count($arr_ships).' salesorder_id found.<br />';
		$i = 0;
		foreach($arr_ships as $order_id){
			if(!is_object($order_id)) continue;
			$order = $this->Salesorder->select_one(array('_id'=>$order_id),array('customer_po_no'));
			if(!isset($order['customer_po_no']))
				$order['customer_po_no'] = '';
			$this->Shipping->collection->update(
											array('salesorder_id'=>$order_id),
											array('$set' => array('customer_po_no' => $order['customer_po_no'])),
											array('multiple' => true)
				);
			$i++;
		}
		echo $i.' records updated.<br />';
		$arr_ships = $this->Shipping->collection->distinct('salesinvoice_id');
		echo count($arr_ships).' salesinvoice_id found.<br />';
		$i = 0;
		foreach($arr_ships as $invoice_id){
			if(!is_object($invoice_id)) continue;
			$invoice = $this->Salesinvoice->select_one(array('_id'=>$invoice_id),array('customer_po_no'));
			if(!isset($invoice['customer_po_no']))
				$invoice['customer_po_no'] = '';
			$this->Shipping->collection->update(
											array('salesorder_id'=>$invoice_id),
											array('$set' => array('customer_po_no' => $invoice['customer_po_no'])),
											array('multiple' => true)
				);
			$i++;
		}
		echo $i.' records updated.<br />';
		die;
	}

	public function create_label($quantity = 1, $type = 'shipping', $id = '')
	{
		if( $type != 'shipping' ) {
			$type = 'invoice';
		}
		$quantity = (int)$quantity;
		if( $quantity <= 0 ) {
			$quantity = 4;
		}
		$filename = 'SH-Label-'.time();
        if( !isset($_GET['print_pdf']) ) {
	        if( $this->print_pdf(array('report_file_name' =>  $filename, 'report_url' => URL.'/shippings/create_label/'.$quantity.'/'.$type.'/'.$this->get_id(), 'custom_footer' => '', 'report_size' => '4.25in*5.5in'), true) ) {
	        	$url = URL.'/upload/'.$filename.'.pdf';
	            if( $this->request->is('ajax') ) {
	            	echo json_encode(array('status' => 'ok', 'url' => $url));
	            } else {
	            	$this->redirect($url);
	            }
	        } else {
	        	$message = 'Please contact IT for this issue.';
	            if( $this->request->is('ajax') ) {
	            	echo json_encode(array('status' => 'error', 'message' => $message));
	            }  else {
	            	echo $message;
	            }
	        }
	        die;
    	}
    	if( empty($id) ) {
    		$id = $this->get_id();
    	}
    	$arrData = array();
		$shipping = $this->opm->select_one(array('_id' => new MongoId($id)), array('invoice_address', 'shipping_address', 'salesorder_id', 'code', 'company_name'));
		$shipping['salesorder_code']  = '';
		if( isset($shipping['salesorder_id']) && is_object($shipping['salesorder_id']) ) {
			$this->selectModel('Salesorder');
			$order = $this->Salesorder->select_one(array('_id' => $shipping['salesorder_id']), array('code'));
			$shipping['salesorder_code']  = $order['code'];
		}
		$address = '';
		$arrAddress = isset( $shipping[$type.'_address'][0] ) ? $shipping[$type.'_address'][0] : array();
		if( isset($shipping['company_name']) && !empty($shipping['company_name']) ) {
			$address .= '<span>'.$shipping['company_name'].'</span>';
		}
		$address .= '<span>'. (isset( $arrAddress[$type.'_address_1'] ) && !empty($arrAddress[$type.'_address_1']) ? trim($arrAddress[$type.'_address_1']).', ' : '').(isset( $arrAddress[$type.'_address_2'] ) && $arrAddress[$type.'_address_2'] ? trim($arrAddress[$type.'_address_2']).', ' : '').(isset( $arrAddress[$type.'_address_3'] ) && $arrAddress[$type.'_address_3'] ? trim($arrAddress[$type.'_address_3']) : '') .'</span>';
		$province = '';
		if( isset( $arrAddress[$type.'_province_state'] )  ) {
			$province =$arrAddress[$type.'_province_state'];
		} else if(  isset( $arrAddress[$type.'_province_state_id'] ) && isset( $arrAddress[$type.'_country_id'] )  ) {
			$pkey = $arrAddress[$type . '_province_state_id'];
			$proArr = $this->province($arrAddress[$type . '_country_id']);
			if( isset($proArr[$pKey]) ) {
				$province = $proArr[$pKey];
			}
		}
		$address .= '<span>'. ( !empty($province) ? $province.', ' : '').(isset( $arrAddress[$type . '_zip_postcode']) ? trim($arrAddress[$type . '_zip_postcode']).', ' : '' ).(isset( $arrAddress[$type . '_country']) ? $arrAddress[$type . '_country'] : '' ) .'</span>';
		$html = '';
		for( $i = 1; $i <= $quantity; $i++) {
			$html .= '
					<div class="avoid">
						<table>
							<tr>
								<td class="info" colspan="2">
									<img src="'.URL.'/img/logo.png" class="logo">
									<span>Anvy Digital</span>
									<span>#103, 3016 - 10th Ave. NE,</span>
									<span>Calgary, Alberta, Canada T2A 6K4</span>
								</td>
								<td class="other" valign="top">
									<span>Shipping Date:</span>
									<span>Docket #: '. $shipping['salesorder_code'] .'</span>
									<span class="package">Package: '.$i.' of '.$quantity.'</span>
								</td>
							</tr>
							<tr>
								<td class="ship-to">
									<span>Ship to:</span>
								</td>
								<td class="address" colspan="2">'.$address.'</td>
							</tr>
						</table>
					</div>';
		}
		$arrData['html'] = $html;
		$arrData['render_path'] = 'create_label';
		$this->render_pdf($arrData);
	}

}