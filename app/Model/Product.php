<?php
App::import('Vendor', 'cal_price/cal_price');

require_once APP.'Model'.DS.'AppModel.php';
class Product extends AppModel {
	public $arr_settings = array(
								'module_name' => 'Product'
							);
	public $arr_temp = array();
	public $temp = '';
	public $arr_key_nosave = array('setup','mongo_id','status_id','none02','none11','none12','none22','none31','none32');
	public $arr_type_nosave = array('autocomplete','id','display');

	function __construct($db) {
		$this->_setting();
		if(is_object($db)){
			$this->collection = $db->selectCollection($this->arr_settings['colection']);
			$this->collection->ensureIndex(array('deleted'=>1), array('name'=>'deleted_id_key'));
			$this->db = $db;
			$this->change_language();
		}
	}

	public function save(array $arr = array()) {
		foreach(array('rfq'=>'is_rfq','special' => 'special_order','approved' => 'approved') as $key => $value){
			if(isset($arr[$value])) {
			    Cache::delete('arr_product_'.$key);
			}
		}
		if( isset($value['is_custom_size']) ) {
			Cache::delete('product_custom_size');
		}
		$array = $this->get_cache_keys_diff('', 'popup_products_');
		foreach($array as $value) {
			Cache::delete( $value );
		}
		return parent::save($arr);
	}

	public function update_sku($arr_save){
		$where['_id']	= new MongoId($arr_save['_id']);
		$this->collection->update($where, array('$set'=>array('sku'=>$arr_save['sku'])));
	}
	public function rebuild_product($arr_save){
		$where['_id']	= $arr_save['_id'];
		unset($arr_save['_id']);
		$this->collection->update($where, array('$set'=>$arr_save));
	}
	public function update_sold_by_len(){
		$from = 'Lr. ft.';
		$to = 'lengths';
		$where['sell_by'] = new MongoRegex("/^".$from."$/i");
		$arr_set = array('sell_by'=>$to,'oum'=>$from);
		try{
			$this->collection->update($where, array( '$set' => $arr_set ), array('multiple' => true));
			return true;
		}catch(MongoCursorException $e){
			$this->arr_errors_save = array( $e->getCode(), $e->getMessage() );
			return false;
		}
	}
	public function update_sold_by_sq(){
		$from = 'Sq. ft.';
		$to = 'area';
		$where['sell_by'] = new MongoRegex("/^".$from."$/i");
		$arr_set = array('sell_by'=>$to,'oum'=>$from);
		try{
			$this->collection->update($where, array( '$set' => $arr_set ), array('multiple' => true));
			return true;
		}catch(MongoCursorException $e){
			$this->arr_errors_save = array( $e->getCode(), $e->getMessage() );
			return false;
		}
	}


	public function change_group_type(){
		//Set tat ca la loai BUY
		$this->collection->update(array(), array( '$set' => array('group_type'=>'BUY') ), array('multiple' => true));
		//cac loai ko thuoc nhom SELL se chuyen het ve Vendor Stock
		$this->collection->update(array('product_type'=> array('$nin'=>array('Product','Service','Finished Goods'))), array( '$set' => array('product_type'=>'Vendor Stock') ), array('multiple' => true));
		$where['product_type']['$in'] = array('Product','Service');
		$arr_set = array('group_type'=>'SELL');
		try{
			$this->collection->update($where, array( '$set' => $arr_set ), array('multiple' => true));
			return true;
		}catch(MongoCursorException $e){
			$this->arr_errors_save = array( $e->getCode(), $e->getMessage() );
			return false;
		}
	}


	public function change_oum_from_parent_product(){
		$qr = $this->select_all(array(
			'arr_where' => array('parent_product_id'=>array('$ne' => '')),
			'arr_field' => array('parent_product_id')
		));
		$query = iterator_to_array($qr,true);
		$m=0; $find=0; $temp = array();
		foreach($query as $keys=>$values){
			if(isset($values['parent_product_id']) && strlen((string)$values['parent_product_id'])==24)
			$newqry = $this->select_one(array('_id'=>$values['parent_product_id']), array('name','oum','supplier'));
			//update oum_depend cua product parent
			if(isset($newqry['oum']) && $newqry['oum']!=''){
				$temp = array();
				$temp['_id'] = $values['_id'];
				$temp['oum_depend'] = $newqry['oum'];
				$temp['parent_product_name'] = $newqry['name'];
				$find++;
				if($this->save($temp)){
					$m++;
				}
			}
			//update oum_depend in supplier note
			if(isset($newqry['supplier']) && is_array($newqry['supplier']) && count($newqry['supplier'])>0 && isset($newqry['oum']) && $newqry['oum']!=''){
				$temp = array();
				$temp['supplier'] = $newqry['supplier'];
				$temp['_id'] = $newqry['_id'];
				foreach($newqry['supplier'] as $sk=>$sv){
					$temp['supplier'][$sk]['oum_depend'] = $newqry['oum'];
				}
				$this->save($temp);
			}
		}

		echo 'Tim duoc '.count($query).' product co supplier<br />';
		echo 'Tim duoc '.$find.' product co supplier dung la loai id<br />';
		echo 'Save lai OUM cho '.$m.' product<br />';
	}



	public function update_unitprice_from_parent_product(){
		$cal_price = new cal_price();

		$qr = $this->select_all(array(
			'arr_where' => array(
					'oum_depend' =>array('$ne' => ''),
					'oum' =>array('$ne' => ''),
					'sell_by' =>array('$ne' => ''),
					'sell_price' =>array('$ne' => ''),
					),
			'arr_field' => array()
		));
		$query = iterator_to_array($qr,true);

		$m=0; $sm=0; $find=0;
		foreach($query as $keys=>$values){
			if(!isset($values['oum_depend']))
				$values['oum_depend'] = 'unit';
			if(isset($values['sizeh']) && $values['sizeh']!='' && isset($values['sizew']) && $values['sizew']!=''  && $values['oum_depend']!=''){
				$temp = array();
				$cal_price->arr_product_items = $values;
				$cal_price->cal_unit_price_for_product();
				//update vao tung product
				$temp['_id'] = $values['_id'];
				$temp['unit_price'] = $cal_price->arr_product_items['unit_price'];
				if($this->save($temp)){
					$m++;
					echo $values['_id'].'<br />';
				}

				//tim parent va update supplier
				if(isset($values['parent_product_id']) && strlen((string)$values['parent_product_id'])==24){
					$newqry = $this->select_one(array('_id'=>$values['parent_product_id']), array('name','oum','supplier'));
					if(isset($newqry['supplier']) && is_array($newqry['supplier']) && count($newqry['supplier'])>0 && isset($newqry['oum']) && $newqry['oum']!=''){
						$temp = array();
						$temp['supplier'] = $newqry['supplier'];
						$temp['_id'] = $newqry['_id'];
						foreach($newqry['supplier'] as $sk=>$sv){
							if(isset($values['oum']))
								$temp['supplier'][$sk]['oum'] = $values['oum'];
							if(isset($sv['product_id']) && $sv['product_id'] == $values['_id'])
								$temp['supplier'][$sk]['unit_price'] = $cal_price->arr_product_items['unit_price'];
						}
						if($this->save($temp)){
							$sm++;
						}
					}
				}
				$find++;
			}else
				continue;

			/*if(isset($values['parent_product_id']) && strlen((string)$values['parent_product_id'])==24)
			$newqry = $this->select_one(array('_id'=>$values['parent_product_id']), array('name','oum','supplier'));
			//update oum_depend cua product parent
			if(isset($newqry['oum']) && $newqry['oum']!=''){
				$temp = array();
				$temp['_id'] = $values['_id'];
				$temp['oum_depend'] = $newqry['oum'];
				$temp['parent_product_name'] = $newqry['name'];
				$find++;
				if($this->save($temp)){
					$m++;
				}
			}
			//update oum_depend in supplier note
			if(isset($newqry['supplier']) && is_array($newqry['supplier']) && count($newqry['supplier'])>0 && isset($newqry['oum']) && $newqry['oum']!=''){
				$temp = array();
				$temp['supplier'] = $newqry['supplier'];
				foreach($newqry['supplier'] as $sk=>$sv){
					$temp['_id'] = $newqry['_id'];
					$temp['supplier'][$sk]['oum_depend'] = $newqry['oum'];
					$this->save($temp);
				}
			}*/
		}

		echo 'Tim duoc '.count($query).' product co supplier<br />';
		echo 'Tim duoc '.$find.' product thoa dieu kien tinh unit price<br />';
		echo 'Save lai Unit price cho '.$m.' product<br />';
		echo 'Save lai supplier cho '.$sm.' parent product<br />';
	}



	public function update_type_for_blank(){
		$arr_where 	= array('name'=>'Blank');
		$arr_set 	= array('product_type'=>'not used');
		try{
			$this->collection->update($arr_where, array( '$set' => $arr_set ), array('multiple' => true));
			return true;
		}catch(MongoCursorException $e){
			$this->arr_errors_save = array( $e->getCode(), $e->getMessage() );
			return false;
		}
		echo 'Xong';
	}


	/*public function repair_type(){
		try{
			$this->collection->update(array('product_type'=> "BUY"), array( '$set' => array('product_type'=>'Vendor Stock','group_type'=>'BUY') ), array('multiple' => true));
			return true;
		}catch(MongoCursorException $e){
			$this->arr_errors_save = array( $e->getCode(), $e->getMessage() );
			return false;
		}
	}


	public function backup_type(){
		$qr = $this->select_all(array('arr_field' => array('product_type')));
		$query = iterator_to_array($qr,true);
		foreach($query as $kk=>$vv){
			if(isset($vv['product_type'])){
				$this->collection->update(array(
					'_id' => $query[$kk]['_id']),
					array( '$set' => array('sub_type'=>$vv['product_type'])
				));
			}
		}
	}



	public function change_field_supplier(){
		$qr = $this->select_all(array(
			'arr_where' => array('supplier.deleted'=>false),
			'arr_field' => array('supplier')
		));
		$query = iterator_to_array($qr,true);
		$temp_pro = array(); $count= $saveagain = 0;
		$nextcode = $this->get_auto_code('code');
		foreach($query as $keys=>$values){
			$savethis = 0;
			if(isset($values['supplier']) && is_array($values['supplier']) && count($values['supplier'])>0){
				foreach($values['supplier'] as $kk=>$vv){
					if(!$vv['deleted']){
						if(isset($vv['supplier'])){
							$query[$keys]['supplier'][$kk]['company_name'] = $vv['supplier'];
							unset($query[$keys]['supplier'][$kk]['supplier']);
							$savethis=1;
						}
						if(isset($vv['sname'])){
							$query[$keys]['supplier'][$kk]['name'] = $vv['sname'];
							unset($query[$keys]['supplier'][$kk]['sname']);
							$savethis=1;
						}
						if(isset($vv['company_code'])){
							$query[$keys]['supplier'][$kk]['sku'] = $vv['company_code'];
							unset($query[$keys]['supplier'][$kk]['company_code']);
							$savethis=1;
						}
						if(isset($vv['sold_by'])){
							$query[$keys]['supplier'][$kk]['sell_by'] = $vv['sold_by'];
							unset($query[$keys]['supplier'][$kk]['sold_by']);
							$savethis=1;
						}
						if(isset($vv['cost_price'])){
							$query[$keys]['supplier'][$kk]['sell_price'] = $vv['cost_price'];
							unset($query[$keys]['supplier'][$kk]['cost_price']);
							$savethis=1;
						}

						//tao product moi
						if(!isset($vv['product_id']) || (isset($vv['product_id']) && strlen((string)$vv['product_id']))!=24){
							$temp_pro = $query[$keys]['supplier'][$kk];
							unset($temp_pro['current']);
							unset($temp_pro['deleted']);
							$temp_pro['code'] = $nextcode;
							$temp_pro['product_type'] = "Vendor Stock";
							$temp_pro['group_type'] = "BUY";
							$temp_pro['status'] = 1;
							$temp_pro['thickness_unit'] = 'in';
							$temp_pro['parent_product_code'] = $query[$keys]['code'];;
							$temp_pro['parent_product_id'] = $query[$keys]['_id'];
							$temp_pro['parent_product_name'] = $query[$keys]['name'];
							if($this->save($temp_pro)){
								$nextcode++; $count++;
								$query[$keys]['supplier'][$kk]['product_id'] = new MongoId($this->mongo_id_after_save);
							}
							$savethis=1;
						}

					}//end if
				}//end for supplier

				//update supplier
				if($savethis==1){
					$temp_pro = array();
					$temp_pro['_id'] = $query[$keys]['_id'];
					$temp_pro['supplier'] = $query[$keys]['supplier'];
					if($this->save($temp_pro))
						$saveagain++;
				}
			}
		}

		echo 'Tim được '.count($query).' product co supplier<br />';
		echo 'Save lai '.$saveagain.' product co supplier<br />';
		echo 'Tao moi '.$count.' product';


	}*/


	// Thêm record, gán các giá trị mặc định và gán field $field = $values,
	public function add($field='',$values=''){
		$this->arr_temp = array();
		if($this->arrfield()){
			//BEGIN special
			$nextcode = $this->get_auto_code('code');
			if(isset($nextcode))
				$this->arr_temp['code'] = $nextcode;

			if($field=='cost_price' || $field=='sell_price' || $field=='markup' || $field=='sizeh' || $field=='sizew')
				$this->arr_temp[$field] = round((float)$values);

			else
				$this->arr_temp[$field] = $values;

			//END special

			$this->save($this->arr_temp);
			$this->arr_temp = array();
			return $this->mongo_id_after_save.'||'.$values; //xuất ra chuỗi để js hiển thị ra html
		}else
			return '';
	}



	//update field $field  = $values của record có _id là $ids
	public function update($ids='',$field='',$values=''){
			$arr_temp = $cal_price = array(); $this->temp = '';
			$arr_temp['_id'] = $ids;

			//BEGIN special
			if($field=='cost_price' || $field=='sell_price' || $field=='markup'){
				$cal_price = $this->cal_price($ids,$field,$values);
				$arr_temp = $cal_price;
				//save log change price
				$arr_temp['update_price_by_id'] = $this->user_id();
				$arr_temp['update_price_by'] = $this->user_name();
				$arr_temp['update_price_date'] = new MongoDate(time());

				if($this->save($arr_temp))
					return $ids.'||'.$this->temp;

			}else if($field=='sizeh' || $field=='sizew'){
				$arr_temp[$field] = round((float)$values,2);

			}else if($field=='product_type'){
				$arr_temp[$field] = $values;
				if($values == 'Product' || $values == 'Service' || $values == 'Finished Goods')
					$arr_temp['group_type'] = 'SELL';
				else
					$arr_temp['group_type'] = 'BUY';

			}else
			//END special
				$arr_temp[$field] = $values;

			if($this->save($arr_temp))
				return $ids.'||'.$arr_temp[$field];
	}




	//dùng kết hợp với function update()
	public function cal_price($ids='',$field='',$values=''){
		$arr_temp = array();
		$arr_temp = $this->select_one(array('_id' => new MongoId($ids)),array('cost_price','sell_price','markup','profit'));
		$arr_temp[$field] = (float)$values;
		$arr_temp['_id'] 			=  $ids;
		if($this->total_cost($ids)>0)
			$arr_temp['cost_price'] = $this->total_cost($ids);
		$this->temp = $arr_temp['cost_price'];

		if((float)$arr_temp['cost_price']>0 && ((isset($arr_temp['sell_price']) && $arr_temp['sell_price']!='') || (isset($arr_temp['sell_price']) && $arr_temp['markup']!=''))){
			if($field!='markup'){
				$arr_temp['profit'] = (float)$arr_temp['sell_price'] - (float)$arr_temp['cost_price'];
				$arr_temp['markup'] = ((float)$arr_temp['profit'])/(float)$arr_temp['cost_price'];
			}else{
				$arr_temp['profit'] = (float)$arr_temp['markup']*(float)$arr_temp['cost_price'];
				$arr_temp['sell_price'] = (float)$arr_temp['profit'] + (float)$arr_temp['cost_price'];
			}
			$this->temp = $arr_temp['cost_price'].'@'.$arr_temp['sell_price'].'@'.$arr_temp['markup'].'@'.$arr_temp['profit'];

			$arr_temp['cost_price'] 	= (float)$arr_temp['cost_price'];
			$arr_temp['sell_price'] 	= (float)$arr_temp['sell_price'];
			$arr_temp['markup'] 		= (float)$arr_temp['markup'];
			$arr_temp['profit'] 		= (float)$arr_temp['profit'];
			$arr_temp['_id'] 			=  $ids;
		}
		return $arr_temp;

	}

	//custom
	public function get_data_general($ids=''){
		$supplier = $same_category =  $comdata = $images = $pricing_method = array();
		$temps = ''; $query = array();
		if($ids!=''){
			$query = $this->select_one(array('_id' => new MongoId($ids)));
			$arr_thisdata = $query;
			if(isset($arr_thisdata['category']) && $arr_thisdata['category']!='')
				$arr_where = array('category'=>$arr_thisdata['category']);
			else
				$arr_where = array('category'=>'');
			$cache_key = md5(serialize($arr_where));
			$same_category = Cache::read('same_category_'.$cache_key);
			if(!$same_category){
				$query = $this->select_all(array(
					'arr_where' => $arr_where,
					'arr_field' => array('code','name','product_type','sell_price'),
					'arr_order' => array('_id' => -1)
				));
				$same_category = $query;
				$same_category = iterator_to_array($same_category);
				Cache::write('same_category_'.$cache_key,$same_category);
			}


			if(isset($arr_thisdata['company']) && is_array($arr_thisdata['company']))
				$comdata = (array)$arr_thisdata['company'];

			if(isset($arr_thisdata['image']))
				$images = (array)$arr_thisdata['image'];

			if(isset($arr_thisdata['pricing_method']))
				$pricing_method = (array)$arr_thisdata['pricing_method'];

			if(count($comdata)>0)
			foreach($comdata as $kd =>$arr_d){
				if(isset($arr_d['deleted']) && $arr_d['deleted'])
					continue;
				$supplier[$kd]['_id'] = $kd;
				if(isset($arr_d['company_id'])){
					$supplier[$kd]['company_id'] = $arr_d['company_id'];
					$supplier[$kd]['company_name'] = $arr_d['company_id'];
				}else{
					$supplier[$kd]['company_id'] = '';
					$supplier[$kd]['company_name'] = '';
				}

				if(isset($arr_d['supplier_code']))
					$supplier[$kd]['supplier_code'] = $arr_d['supplier_code'];

				if(isset($arr_d['cost_price']))
					$supplier[$kd]['cost_price'] = (float)$arr_d['cost_price'];
				else
					$supplier[$kd]['cost_price'] = 0;
				if(isset($arr_d['current']))
					$supplier[$kd]['current'] = $arr_d['current'];
				else
					$supplier[$kd]['current'] = 0;

				if(isset($arr_d['current']) && (int)$arr_d['current']==1)
					$temps = $kd;
			}
		}
		$subdatas['same_category'] 	= $same_category;
		$subdatas['supplier'] 		= $this->aasort($supplier,'current',0);
		$subdatas['supplier_current'] = $temps;
		$subdatas['image'] 			= $images;
		$subdatas['pricing_method'] = $pricing_method;
		$subdatas['avaliable_size'] = array();
		return $subdatas;
	}


	public function get_data_costing($ids=''){
		$subdatas = $madeup = $materiallist = $useon = $arr_temp = array();
		$sums = 0;
	 	$arr_product = $this->collection->aggregate(
                        array(
                            '$match'=>array(
                            				'deleted' => false,
                                            'madeup' => array(
													'$elemMatch' => array(
															'$or' => array(
																	array('deleted' => false,),
																	array('deleted' => array('$exists' => false),)
																),
															'product_id' => new MongoId($ids)
														)
												)
                                            ),
                        ),
                        array(
                            '$unwind'=>'$madeup',
                        ),
                         array(
                            '$match'=>array(
                                            'madeup.deleted'=>false,
                                            'madeup.product_id'=>new MongoId($ids),
                                            )
                        ),
                        array(
                            '$project'=>array('madeup'=>'$madeup.quantity','code'=>'$code','product_type' => '$product_type', 'name' => '$name','category' => '$category')
                        ),
                        array(
                            '$group'=>array(
                                          '_id'=>array('_id'=>'$_id','code' => '$code','product_type' => '$product_type', 'name' => '$name', 'category' => '$category'),
                                          'quantity'=>array('$push'=>'$madeup')
                                        )
                        )
                    );
		if(isset($arr_product['ok'])){
			foreach($arr_product['result'] as $k => $product){
				$k = (string)$product['_id']['_id'];
				$useon[$k] = $product['_id'];
				$useon[$k]['quantity'] = 0;
				foreach($product['quantity'] as $qty)
					$useon[$k]['quantity'] += $qty;
			}
		}
		$subdatas['useon'] 		= $useon;
		return $subdatas;
	}

	public function get_pricebreaks($id)
	{
		$query = $this->select_one(array('_id' => new MongoId($id)));
		$arr_temp = $query;
		$pricebreaks = array();
		if(isset($arr_temp['pricebreaks']) && is_array($arr_temp['pricebreaks'])){
			$pricebreaks = array();
			foreach($arr_temp['pricebreaks'] as $km => $vm){
				if(isset($vm['deleted']) && $vm['deleted'])
					continue;
				if(isset($_SESSION['Products_current_category'])&&$vm['sell_category']==$_SESSION['Products_current_category']){
					$pricebreaks[$km] = $vm;
					$pricebreaks[$km]['_id'] = $km;
					if(isset($vm['range_amount']))
						$pricebreaks[$km]['range_amount'] = $vm['range_amount'];
					else
						$pricebreaks[$km]['range_amount'] = 0;

					if(isset($vm['range_amount']) && isset($vm['breaks_unit_price']))
						$pricebreaks[$km]['breaks_sum_price'] = (float)$vm['range_amount']*(float)$vm['breaks_unit_price'];
					else
						$pricebreaks[$km]['breaks_sum_price'] = '';

					if(isset($vm['breaks_unit_price']))
						$pricebreaks[$km]['breaks_unit_price'] = $vm['breaks_unit_price'];
					else
						$pricebreaks[$km]['breaks_unit_price'] = 0;
				}
			}
		}
		return $pricebreaks;
	}


	public function get_data_pricing($ids=''){
		$subdatas = $sellprices = $materiallist = $pricebreaks = $otherpricing = $arr_temp = $arr_product = array();
		if(isset($ids) && $ids!=''){
			$query = $this->select_one(array('_id' => new MongoId($ids)));
			$arr_temp = $query;

			//sellprices
			if(isset($arr_temp['sellprices']) && is_array($arr_temp['sellprices'])){
				$sellprices = array(); $have_default = 0; $isdefault_session ='';
				foreach($arr_temp['sellprices'] as $km => $vm){
					if(isset($vm['deleted']) && $vm['deleted'])
						continue;
					$sellprices[$km] = $vm;
					$sellprices[$km]['_id'] = $km;
					if(isset($vm['sell_category']))
						$sellprices[$km]['sell_category'] = $vm['sell_category'];
					else
						$sellprices[$km]['sell_category'] = '';

					if(isset($vm['sell_unit_price']))
						$sellprices[$km]['sell_unit_price'] = $vm['sell_unit_price'];
					else
						$sellprices[$km]['sell_unit_price'] = 0;

					if(isset($vm['sell_default'])){
						if($vm['sell_default']==1 && !isset($_SESSION['Products_current_category']) && isset($vm['sell_category']))
							$_SESSION['Products_current_category'] = $vm['sell_category'];
						if($vm['sell_default']==1){
							$sellprices[$km]['remove_deleted'] = '1';
							$sellprices[$km]['xlock']['sell_category'] = '1';
							$have_default = 1;
						}
						$sellprices[$km]['sell_default'] = $vm['sell_default'];
					}else
						$sellprices[$km]['sell_default'] = '';

					if(isset($_SESSION['Products_current_category']) && isset($vm['sell_category']) && $_SESSION['Products_current_category'] == $vm['sell_category'])
						$isdefault_session = $vm['sell_category'];

					if(!isset($vm['sell_category']))
						$vm['sell_category'] = 0;
					$sellprices[$km]['cate_key'] = $km.'" class="sell_category_'.$vm['sell_category'];
					$sellprices[$km]['category_text'] = $vm['sell_category'];
				}
				//neu ko co default
				if($have_default==0)
					$_SESSION['Products_current_category'] = 'Retail';

				//neu co session nhung ko co category nao thoa
				if(isset($_SESSION['Products_current_category']) && $isdefault_session=='')
					$_SESSION['Products_current_category'] = 'Retail';
			}

			//pricebreaks
			if(isset($arr_temp['pricebreaks']) && is_array($arr_temp['pricebreaks'])){
				$pricebreaks = array();
				foreach($arr_temp['pricebreaks'] as $km => $vm){
					if(isset($vm['deleted']) && $vm['deleted'])
						continue;
					if(isset($_SESSION['Products_current_category'])&&$vm['sell_category']==$_SESSION['Products_current_category']){
						$pricebreaks[$km] = $vm;
						$pricebreaks[$km]['_id'] = $km;
						if(isset($vm['range_amount']))
							$pricebreaks[$km]['range_amount'] = $vm['range_amount'];
						else
							$pricebreaks[$km]['range_amount'] = 0;

						if(isset($vm['range_amount']) && isset($vm['breaks_unit_price']))
							$pricebreaks[$km]['breaks_sum_price'] = (float)$vm['range_amount']*(float)$vm['breaks_unit_price'];
						else
							$pricebreaks[$km]['breaks_sum_price'] = '';

						if(isset($vm['breaks_unit_price']))
							$pricebreaks[$km]['breaks_unit_price'] = $vm['breaks_unit_price'];
						else
							$pricebreaks[$km]['breaks_unit_price'] = 0;
					}
				}
			}

			if(isset($arr_temp['sell_by']) && $arr_temp['sell_by']!='')
				$subdatas['sell_by'] =  $arr_temp['sell_by'];

			//otherpricing
			if(isset($arr_temp['update_price_by']))
				$otherpricing['update_price_by'] 	= $arr_temp['update_price_by'];

			if(isset($arr_temp['update_price_by_id']))
				$otherpricing['update_price_by_id'] = $arr_temp['update_price_by_id'];

			if(isset($arr_temp['update_price_date'])  && is_object($arr_temp['update_price_date']))
				$otherpricing['update_price_date'] 	= $this->format_date($arr_temp['update_price_date']->sec);

			if(isset($arr_temp['price_note']))
				$otherpricing['price_note'] = $arr_temp['price_note'];

		}

		$subdatas['sellprices'] 	= $sellprices;
		$subdatas['pricebreaks'] 	= $pricebreaks;
		$subdatas['otherpricing'] 	= $otherpricing;
		return $subdatas;
	}


	//update costing default của company (option của product). Ket hop voi cal_price()
	public function update_cost_default($ids='',$cost=''){
		if($ids!=''){
			$arr_temp = $new_cost = $arr_comp = array();
			$arr_temp = $this->select_one(array('_id' => new MongoId($ids)),array('company'));
			if(isset($arr_temp['company'])){
				$arr_comp = (array)$arr_temp['company'];
				foreach($arr_comp as $kr => $vr){
					$new_cost = $vr;
					$new_cost['cost_price'] = 0;
					if((isset($vr['current']) && (int)$vr['current']== 1) || (isset($cost) && $cost!='')){
						$new_cost['cost_price'] = (float)$cost;
						$ii = $kr;
					}
				}
				$arr_comp[$ii] = $new_cost;

			}else{
				$new_cost['id'] = 0;
				$new_cost['company_name'] = ' ';
				$new_cost['company_id'] = ' ';
				$new_cost['current'] = '1';
				$new_cost['cost_price'] = (float)$cost;
				$arr_comp[0] = $new_cost;
			}
			return $arr_comp;
		}
	}


	//add company của module. $arr_vl = Array(moduleids,namevalue,idvalue)
	public function add_company_of_product($arr_vl=array()){
		if($arr_vl['moduleids']!=''){
			// khởi tạo giá trị
			$this->arr_temp = $arr_temp = array();
			$this->arr_temp = $this->select_one(array('_id' => new MongoId($arr_vl['moduleids'])),array('company'));
			//nhận array company
			$arr_temp = (array)$this->arr_temp['company'];
			if(isset($arr_temp) && is_array($arr_temp)){
				$newdata = array();
				$newdata['id'] = count($arr_temp)+1;
				$newdata['company_name'] = $arr_vl['namevalue'];
				$newdata['company_id'] = $arr_vl['idvalue'];
				array_push($arr_temp,$newdata);
				$this->arr_temp['company'] = $arr_temp;
				if($this->save($this->arr_temp))
					return true;
				else
					return false;
			}
		}
	}


	//update option của module. $arr_vl = Array(moduleids,moduleop,field,value,opwhere)
	public function update_value_option_of_module($arr_vl=array(),$add=0){
		if($arr_vl['moduleids']!=''){
			// khởi tạo biến
			$arr_temp = $arr_option = $cal_price =  array(); $result = '';
			$booleans = true;

			$arr_temp = $this->select_one(array('_id' => new MongoId($arr_vl['moduleids'])),array($arr_vl['moduleop']));
			//nhận array option
			$arr_option = (array)$arr_temp[$arr_vl['moduleop']];

			if($add!=0){ //nếu là add option
				$newdata = array();
				$newdata['id'] = count($arr_option)+1;
				$newdata[$arr_vl['field']] = $arr_vl['value'];
				array_push($arr_option,$newdata);
			}else{
				//lặp array option để xử lý
				foreach($arr_option as $kr => $vr){
				//Kiểm tra tất cả điều kiện đưa vào (AND), mặc định kiểm $arr_vl['opwhere']['id']
					$booleans = true;
					foreach($arr_vl['opwhere'] as $ok => $ov){
						if($booleans && $vr[$ok]==$ov)
							$booleans = true;
						else
							$booleans = false;
					}

					//Custom: danh cho current cua product-company, neu current = 1
					if($arr_vl['field'] == 'cost_price' && isset($vr['current']) && (int)$vr['current']==1 && $booleans){
						$arr_option[$kr]['cost_price'] = (float)$arr_vl['value'];
						$cal_price = $this->cal_price($arr_vl['moduleids'],'cost_price',(float)$arr_vl['value']);
						$arr_temp = array_merge($arr_temp,$cal_price);
					//gan gia tri neu thoa opwhere
					}else if($booleans)
						$arr_option[$kr][$arr_vl['field']] = $arr_vl['value'];
				}
			}//end if

			if(count($cal_price)>0)
				$result .= $cal_price['markup'].'@'.$cal_price['profit'];

			//gan gia tri cho option va luu record
			$arr_temp[$arr_vl['moduleop']] = $arr_option;
			if($this->save($arr_temp))
				return $result;
			else
				return '';
		}
	}


	//update option của module. $arr_vl = Array(moduleids,moduleop,field,value,opwhere)
	public function update_current_default($arr_vl=array()){
		if($arr_vl['moduleids']!=''){

			//khoi tao bien
			$arr_temp = $arr_option = $cal_price = array();
			$booleans = true; $str = '';$fl = 0;

			$arr_temp = $this->select_one(array('_id' => new MongoId($arr_vl['moduleids'])),array($arr_vl['moduleop']));
			//nhận array company
			$arr_option = (array)$arr_temp[$arr_vl['moduleop']];

			//lặp array company để xử lý
			$n=0; $result=''; $cal_price = $suplier = array();
			foreach($arr_option as $kr => $vr){
				if((int)$arr_vl['value'] == 1 && $kr == $arr_vl['opwhere']['id']){
					$arr_option[$kr][$arr_vl['field']] = 1;
					$cal_price = $this->cal_price($arr_vl['moduleids'],'cost_price',$vr['cost_price']);
					$suplier['company_name']=$vr['company_name'];
					$suplier['company_id']=$vr['company_id'];
					$arr_temp = array_merge($arr_temp,$cal_price);

				//TH cần set là default và không phải id đang xử lý cần update
				}else if((int)$arr_vl['value'] == 1){
					$arr_option[$kr][$arr_vl['field']] = 0;

				//TH cần set là non default và đang xử lý id cần update
				}else if((int)$arr_vl['value'] == 0 && $kr == $arr_vl['opwhere']['id']){
					$arr_option[$kr][$arr_vl['field']] = 0;

				//TH cần set là non default và không phải id đang xử lý cần update
				}else if($fl == 0){
					$arr_option[$kr][$arr_vl['field']] = 1;
					$cal_price = $this->cal_price($arr_vl['moduleids'],'cost_price',$vr['cost_price']);
					$suplier['company_name']=$vr['company_name'];
					$suplier['company_id']=$vr['company_id'];
					$arr_temp = array_merge($arr_temp,$cal_price);
					$fl = 1; //bật cờ cho lần xử lý đầu, để chỉ set 1 record là default

				//TH cần set là non default và không phải id đang xử lý cần update, và cờ đã bật không cho phép set default nữa
				}else{
					$arr_option[$kr][$arr_vl['field']] = 0;
				}
				$n++;//đếm tổng
			}
			$result .= $n.'@';
			if(count($cal_price)>0)
				$result .= $cal_price['cost_price'].'@'.$cal_price['markup'].'@'.$cal_price['profit'];
			if(count($suplier)>0)
				$result .= '@'.$suplier['company_name'].'@'.$suplier['company_id'];
			//return serialize($arr_option);
			//gan gia tri cho option va luu record
			$arr_temp[$arr_vl['moduleop']] = $arr_option;
			if($this->save($arr_temp))
				//return tổng số supplier,Cost price,Markup,Profit
				return $result;
			else
				return "";
		}
	}

	// list ra cac option image
	public function get_option($ids='',$opt='image'){
		if($ids!=''){
			$this->arr_temp = $this->select_one(array('_id' => new MongoId($ids)),array($opt));
			if(isset($this->arr_temp[$opt]))
				pr((array)$this->arr_temp[$opt]);die;
			// 	return (array)$this->arr_temp[$opt];
			// else
			// 	return array();
		}else
			return array();
	}

	// tính tổng Costing trong bảng madeup
	public function total_cost($ids='',$opname='madeup',$unit='unit_cost',$q='quantity'){
		$sum = 0;
		if($ids!=''){
			$arr_temp = $this->select_one(array('_id' => new MongoId($ids)),array($opname));
			if(isset($arr_temp[$opname]) && is_array($arr_temp[$opname]) && count($arr_temp[$opname])>0){
				foreach($arr_temp[$opname] as $keys => $values){
					if(isset($values[$unit]) && isset($values[$q]) && !($values['deleted']) )
						$sum += (float)$values[$unit]*(float)$values[$q];
				}
			}
		}
		return $sum;//float
	}


	// ham test, khi nao chay on se remove
	public function listfield(){
		$str='';
		foreach($this->arr_settings['field'] as $keys =>$arr_field){
			foreach($arr_field as $k =>$arr_v){
				if(!in_array($k,$this->arr_key_nosave) && !in_array((string)$arr_v['type'],$this->arr_type_nosave)){
					$str .= $k.'<br />';
				}
			}
		}
		return $str;
	}





	public function update_stock($prolist=array(),$option='plus') { //minus (-)
		//Nguyên tắc:
		//$prolist là một array chứa các giá trị product_id, quantity, location_id, location_name, và option là operator (plus hoặc minus) mặc
		//	định là plus
		// Ex: PO/receive_item,PO/return_item

		if(count($prolist)>0){
			$arr = array();
			$default_location = array(
				'_id' => new MongoId('5297e68367b96d0e7200000c'),
				'name' => "Main stock location",
				'location_type' =>'"Warehouse',
				'stock_usage' =>'Sell',
			);
			foreach($prolist as $product_item){

				if(isset($product_item['_id']) && isset($product_item['quantity']) && (int)$product_item['quantity']>0){

					//get data product
					$query = $this->select_one(array('_id' => $product_item['_id']));
					$new_loca = 0; $maxid = 0;

					//set data location, dam bao location luon co
					if(isset($product_item['location']) && is_array($product_item['location']) && count($product_item['location'])){

						if(!isset($product_item['location']['_id']) || (isset($product_item['location']['_id']) && !is_object($product_item['location']['_id'])) ){
							$product_item['location']['_id'] = $default_location['_id'];
						}
					}else
						$product_item['location'] = $default_location;


					//check location in product and push
					if(isset($query['locations']) && is_array($query['locations']) && count($query['locations'])>0){
						foreach($query['locations'] as $key => $location){
							//neu co location trong product
							if(isset($location['deleted']) && !$location['deleted'] && isset($location['location_id']) && $location['location_id']==$product_item['location']['_id']){
								if($option=='plus'){
									$query['locations'][$key]['total_stock'] += (int)$product_item['quantity'];
									$query['qty_in_stock'] += (int)$product_item['quantity'];

								}else if($option=='minus'){
									$query['locations'][$key]['total_stock'] -= (int)$product_item['quantity'];
									$query['qty_in_stock'] -= (int)$product_item['quantity'];
								}
								$new_loca = 1;

							}
							$maxid = $key; // lay id lon nhat
						}

						//set location moi neu chua co location trong product
						if($new_loca==0){
							$maxid++;//id moi
							if($option=='plus'){
								$query['qty_in_stock'] = (int)$product_item['quantity'];
								$query['locations'][$maxid]['total_stock'] = (int)$product_item['quantity'];
								$query['locations'][$maxid]['location_id'] = $product_item['location']['_id'];
								$query['locations'][$maxid]['location_name'] = $product_item['location']['name'];
								$query['locations'][$maxid]['location_type'] = (isset($product_item['location']['location_type']) ? $product_item['location']['location_type'] : '');
								$query['locations'][$maxid]['stock_usage'] = (isset($product_item['location']['stock_usage']) ? $product_item['location']['stock_usage'] : '');
								$query['locations'][$maxid]['deleted'] = false;

							}else if($option=='minus'){
								$query['qty_in_stock'] = 0;
								$query['locations'][$maxid]['total_stock'] = 0;
								$query['locations'][$maxid]['location_id'] = $product_item['location']['_id'];
								$query['locations'][$maxid]['location_name'] = $product_item['location']['name'];
								$query['locations'][$maxid]['location_type'] = (isset($product_item['location']['location_type']) ? $product_item['location']['location_type'] : '');
								$query['locations'][$maxid]['stock_usage'] = (isset($product_item['location']['stock_usage']) ? $product_item['location']['stock_usage'] : '');
								$query['locations'][$maxid]['deleted'] = false;
							}
						}

					//neu trong product chua co dong location nao
					}else{
						if($option=='plus'){
							$query['qty_in_stock'] = (int)$product_item['quantity'];
							$query['locations'][0]['total_stock'] = (int)$product_item['quantity'];
							$query['locations'][0]['location_id'] = $product_item['location']['_id'];
							$query['locations'][0]['location_name'] = $product_item['location']['name'];
							$query['locations'][0]['location_type'] = (isset($product_item['location']['location_type']) ? $product_item['location']['location_type'] : '');
							$query['locations'][0]['stock_usage'] = (isset($product_item['location']['stock_usage']) ? $product_item['location']['stock_usage'] : '');
							$query['locations'][0]['deleted'] = false;

						}else if($option=='minus'){
							$query['qty_in_stock'] =  0;
							$query['locations'][0]['total_stock'] = 0;
							$query['locations'][0]['location_id'] = $product_item['location']['_id'];
							$query['locations'][0]['location_name'] = $product_item['location']['name'];
							$query['locations'][0]['location_type'] = (isset($product_item['location']['location_type']) ? $product_item['location']['location_type'] : '');
							$query['locations'][0]['stock_usage'] = (isset($product_item['location']['stock_usage']) ? $product_item['location']['stock_usage'] : '');
							$query['locations'][0]['deleted'] = false;
						}
					}


					$arr['_id'] = $product_item['_id']; //$product_item['_id'] đã là MongoId
					$arr['locations'] = $query['locations'];
					$arr['qty_in_stock'] = $query['qty_in_stock'];
					if(isset($product_item['quantity']) && (int)$product_item['quantity']>0)
					{
						$this->save($arr);
					}
				}
			}

		}
	}




	// lay thong tin options
	public function options_data($ids='',$is_sort = false) {
		$arr_return = $product = array();
		$arr_return['custom_option_group'] = array();
		$arr_return['groupstr'] = '';
		if($ids!=''){
			$query = $this->select_one(array('_id'=>new MongoId($ids)),array('options'));
			if(isset($query['options']) && is_array($query['options']) && count($query['options'])>0){
					foreach($query['options'] as $key => $value){
						if(isset($value['deleted']) && !$value['deleted']){
							if(isset($value['product_id']) && is_object($value['product_id']))
							$product = $this->select_one(array('_id'=>$value['product_id']));
							$cal_price = new cal_price;
							$cal_price->arr_product_items = array(
							                                      'sell_by'	=> ($product['sell_by']!='' ? $product['sell_by'] : 'unit'),
							                                      'sizeh'	=> (float)$product['sizeh'],
																  'sizeh_unit'	=>	($product['sizeh_unit']!='' ? $product['sizeh_unit'] : 'in'),
																  'sizew'	=> (float)$product['sizew'],
																  'sizew_unit'	=>	($product['sizew_unit']!='' ? $product['sizew_unit'] : 'in'),
																  'oum'		=>	($product['oum']!='' ? $product['oum'] : 'unit'),
																  'quantity'	=> (isset($value['quantity']) && $value['quantity']!='' ? $value['quantity'] : 0),
							                                      );
							$cal_price->check_product_items();
							$cal_price->price_break_from_to = $this->change_sell_price_company('',$product['_id']);
							$arr_result = $cal_price->cal_price_items();
							$value['_id'] = $key;
							if(!isset($value['unit_price'])){
								$value['unit_price'] = $arr_result['sell_price'];
							}else{
								$value['is_custom'] = true;
							}
							if(isset($product['special_order'])&&$product['special_order']==1)
								$value['special_order'] = 1;
							else
								$value['special_order'] = 0;

							if(isset($product['oum']))
								$value['oum'] = $product['oum'];
							else
								$value['oum'] = '';

							if(isset($product['name']))
								$value['product_name'] = $product['name'];
							else
								$value['product_name'] = '';

							if(isset($product['sku']))
								$value['sku'] = $product['sku'];
							else
								$value['sku'] = '';

							if(isset($product['code']))
								$value['code'] = $product['code'];
							else
								$value['code'] = '';
							if(isset($product['option_type']))
								$value['option_type'] = $product['option_type'];
							else
								$value['option_type'] = '';

							$value['sizew'] = '';
							$value['sizew_unit'] = '';
							$value['sizeh'] = '';
							$value['sizeh_unit'] = '';
							$value['sell_by'] = '';
							$value['oum'] = '';
							if(isset($product['sizew']))
								$value['sizew'] = $product['sizew'];
							if(isset($product['sizew_unit']))
								$value['sizew_unit'] = $product['sizew_unit'];
							if(isset($product['sizeh']))
								$value['sizeh'] = $product['sizeh'];
							if(isset($product['sizeh_unit']))
								$value['sizeh_unit'] = $product['sizeh_unit'];
							if(isset($product['sell_by']))
								$value['sell_by'] = $product['sell_by'];
							if(isset($product['oum']))
								$value['oum'] = $product['oum'];


							if(!isset($value['discount']))
								$value['discount'] = 0;

							if(!isset($value['quantity']))
								$value['quantity'] = 1;

							$more_discount = (float)$value['unit_price']*((float)$value['discount']/100);
							$adjustment = isset($value['adjustment'])?$value['adjustment']:0;
							$value['sub_total'] = (float)$value['quantity']*((float)$value['unit_price']-$more_discount+$adjustment);
							$value['hidden'] = isset($value['hidden'])&&$value['hidden'] ? 1 : 0;
							$arr_return['productoptions'][$key] = $value;
							if(isset($value['option_group'])){
								$arr_return['custom_option_group'][$value['option_group']] = (string)$value['option_group'];
								$arr_return['groupstr'] .= (string)$value['option_group'].',';
							}else {
								$arr_return['productoptions'][$key]['option_group'] = '';
							}
						}
					}
				}
		}
		if (isset($this->sendToView)) {
			$arr_return['option_select_dynamic'] = array();
			require_once APP.'Model'.DS.'Setting.php';
			$setting = new Setting($this->db);
			$arrSettings = array();
		}
		if(!empty($arr_return['productoptions'])){
			if($is_sort) {
				uasort($arr_return['productoptions'], function($a, $b){
					$a['option_group'] = isset($a['option_group']) ? $a['option_group'] : '';
					$b['option_group'] = isset($b['option_group']) ? $b['option_group'] : '';
					return strcmp($a['option_group'],$b['option_group']);
				});
			}
			$optionGroups = array();
			foreach ($arr_return['productoptions'] as $key => $option) {
				if (!isset($option['level'])) {
					$option['level'] = 'Level 01';
				}
				if (!isset($option['group_order'])) {
					$option['group_order'] = 1;
				}
				$optionGroups[ $option['level'] ][$key] = $option;
			}
			$arr_return['productoptions'] = array();
			ksort($optionGroups);
			foreach ($optionGroups as $options) {
				uasort($options, function ($a, $b) {
					return $a['group_order'] > $b['group_order'];
				});
				foreach($options as $key => $option) {
					if (isset($this->sendToView)) {
						$option['_id'] = $key;
						if (!isset($arrSettings[$option['option_type']])) {
							$arrSettings[$option['option_type']] = (array)$setting->select_option_vl(array('setting_value' => $option['option_type']));
						}
						$arr_return['option_select_dynamic']['finish_'. $key] = $arrSettings[$option['option_type']];
					}
					$arr_return['productoptions'][$key] = $option;
				}
			}
		}
		return $arr_return;


	}





	// Lay Sell price va cac option co S.P.
	public function sell_price_plus_option($ids='',$custom_price='') {
		$sell_price = 0;
		if($ids!=''){
			$query = $this->select_one(array('_id'=>new MongoId($ids)));
			//Cộng dồn Option
			if(isset($query['options']) && is_array($query['options']) && count($query['options'])>0){
				//loop options
				foreach($query['options'] as $key => $value){
					if(isset($value['deleted']) && !$value['deleted'] && isset($value['require']) && (int)$value['require']==1 && isset($value['same_parent']) && (int)$value['same_parent']==1){
						//lay data tu product con
						if(isset($value['product_id']) && is_object($value['product_id']))
						$product = $this->select_one(array('_id'=>$value['product_id']));


						if(isset($product['product_type']) && $product['product_type']=='Vendor Stock' && isset($value['unit_price']))
							$value['unit_price'] = (isset($product['unit_price']) ? (float)$product['unit_price'] : 0);
						else if(isset($product['product_type']) && isset($product['sell_price']))
							$value['unit_price'] = (float)$product['sell_price'];
						else
							$value['unit_price'] = 0;

						if(!isset($value['discount']))
							$value['discount'] = 0;

						if(!isset($value['quantity']))
							$value['quantity'] = 1;

						$more_discount = (float)$value['unit_price']*((float)$value['discount']/100);
						$value['sub_total'] = (float)$value['quantity']*((float)$value['unit_price']-$more_discount);

						$sell_price += $value['sub_total'];
					}
				}
			}

			//Cộng thêm sell_price
			if($custom_price!='')
				$sell_price += (float)$custom_price;
			else if(isset($query['sell_price']))
				$sell_price += (float)$query['sell_price'];


		}
		return $sell_price;
	}

	// BaoNam (!): hàm get_product_asset này có dùng ở SalesordersController.php -> _get_production() nên có sửa gì ở trong hàm này thì test lại bên tab Task của SO
    function get_product_asset($cond,$extra_info=array(),$level=5){
    	$this->level = $level;
    	$this->arr_product_key = array();
    	//$cond có thể là id hoặc là mảng options
		$group = array();
		$find_key = array('production_step','madeup','code','sku','name','product_type','sell_by','oum','sizew','sizeh','sizew_unit','sizeh_unit');
		$key_in_product = array('product_type','sell_by','oum','sizew','sizeh','sizew_unit','sizeh_unit');
		$option = array('find_key'=>$find_key,'key_in_product'=>$key_in_product);
		if(isset($extra_info['line_no']))
			$option['line_no']=$extra_info['line_no'];
		if(isset($extra_info['for_line']))
			$option['for_line']=$extra_info['for_line'];
    	if(is_string($cond)&&strlen($cond)==24 || is_object($cond)){ // BaoNam: fix bug khi bỏ product id là 1 object từ foreach
    		$product = $this->select_one(array('_id'=>new MongoId($cond)),$find_key);
    		if(!empty($product)){
    			$group = $this->get_one_product_asset_tag($product,$group,$option,true);
    			$group = $this->get_all_asset_tag($group,$option,$level);
    			if(isset($group['madeup_id']))
    				unset($group['madeup_id']);
    		}
    	} else if(is_array($cond)){
    		$options = $cond[0];
    		$key = $cond[1];
    		$arr_product_id = array();
    		if(empty($options)) return array();
    		foreach($options as $value){
    			if(!isset($value['product_id']) || !is_object($value['product_id'])) continue;
    			if(!isset($value['for_line']) || $value['for_line']!= $key) continue;
    			$arr_product_id[] = new MongoId($value['product_id']);
    		}
    		if(!empty($arr_product_id)){
    			$group['madeup_id'] = $arr_product_id;
    			$group = $this->get_all_asset_tag($group,$option,$level);
    			if(isset($group['madeup_id']))
    				unset($group['madeup_id']);
    		}
    	}
    	return $group;
    }



    function get_one_product_asset_tag($product,$group,$option,$is_parent=false){
    	if(isset($product['production_step'])&&!empty($product['production_step'])){
	    	$this->aasort($product['production_step'],'tag');
	        foreach($product['production_step'] as $key=>$value){
	            if(isset($value['deleted']) && $value['deleted']) continue;
	            $this->arr_product_key[] = (string)$product['_id'];
	            $group_key = (string)$product['_id'].'_'.(string)$value['tag_key'];
	            $group[$group_key] = $value;
	            $group[$group_key]['_id'] = (string)$product['_id'].'_'.$key;
	            if(isset($option['line_no'])&&$is_parent)
	            	$group[$group_key]['line_no'] = $option['line_no'];
	            else if(isset($option['line_no'])&&!$is_parent)
	            	$group[$group_key]['for_line_no'] = $option['line_no'];
	            if(isset($option['for_line']))
	            	$group[$group_key]['for_line'] = $option['for_line'];
				if(isset($value['name']) && $value['name']!='')
	            	$group[$group_key]['name'] = $value['name'];
				else
					$group[$group_key]['name'] = $product['name'];

				if(isset($value['tag']))
					$group[$group_key]['tag'] = $value['tag'];
				if(isset($value['tag_key']))
					$group[$group_key]['tag_key'] = $value['tag_key'];
				if(!isset($value['min_of_uom']))
					$group[$group_key]['min_of_uom'] = 0;

				$group[$group_key]['product_name'] = $product['name'];
				$group[$group_key]['product_id'] = (string)$product['_id'];
				foreach($option['key_in_product'] as $prokey){
					if(isset($product[$prokey]))
						$group[$group_key][$prokey] = $product[$prokey];
					else
						$group[$group_key][$prokey] = '';
				}
	            $group[$group_key]['from'] = (isset($product['code'])?'<a href="'.URL.'/products/entry/'.(string)$product['_id'].'">'.$product['code'].'</a>':'');
				$group[$group_key]['key'] = $key;
				if(!$is_parent)
					$group[$group_key]['remove_deleted'] = '1';
				$group[$group_key]['id_delete'] = $key;
	        }
    	}
    	if(isset($product['madeup'])&&!empty($product['madeup'])){
        	foreach($product['madeup'] as $madeup){
        		if(isset($madeup['deleted'])&&$madeup['deleted']) continue;
        		if(!isset($madeup['product_id']) || !is_object($madeup['product_id']) ) continue;
        		$madeup_id[] = new MongoId($madeup['product_id']);
        	}
        	if(!empty($madeup_id))
        		$group['madeup_id'] = $madeup_id;
        }
        return $group;
    }




    function get_all_asset_tag($group,$option,&$level){
    	$level--;
    	if($level==0)
    		return $group;
    	if(isset($group['madeup_id'])&&$level!=0){
    		$madeup_id = $group['madeup_id'];
    		unset($group['madeup_id']);
    		$products = $this->select_all(array(
                	                              'arr_where' => array(
                	                                                   '_id'=>array('$in'=>$madeup_id)
                	                                                   ),
                	                              'arr_field' => $option['find_key'],
                	                              'arr_order' => array('_id'=>-1)
                	                              ));
    		foreach($products as $product_key=>$product){
    			if(in_array((string)$product['_id'], $this->arr_product_key)) continue;
				$group = $this->get_one_product_asset_tag($product,$group,$option);
				if($level==0)
					$level = $this->level;
				$group = $this->get_all_asset_tag($group,$option,$level);
        	}
    	}
    	return $group;
    }

    public function costings_data($ids) {
		$subdatas = array();
		$total = 0;
        //$codeauto = 0;
        $subdatas['madeup'] = array();
		if($ids!=''){
			$query = $this->select_one(array('_id'=>new MongoId($ids)),array('cost_price','sell_price','madeup'));
			if(isset($query['madeup']) && is_array($query['madeup']) && count($query['madeup'])>0){
				foreach($query['madeup'] as $key=>$value){
					if(isset($value['deleted']) && !$value['deleted']){
						//set data co trong produc
						if(isset($value['product_id']) && is_object($value['product_id'])){
							$prodata = $this->select_one(array('_id'=>$value['product_id']));
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
                            $cal_price->product_model = true;
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

	function change_sell_price_company($idcompany='',$product_id=''){
		$result = array();
		//Dò thông tin trong bảng Company
		$sell_category = $this->company_pricing($idcompany,$product_id);

		//Nếu trong Company có Price Break thì lấy thông tin
		if(isset($sell_category['price_break']) && count($sell_category['price_break'])>0)
			$result['company_price_break'] = $sell_category['price_break'];

		//Lấy thông tin Discount cho Company
		if(isset($sell_category['discount']) && $sell_category['discount']!='')
			$result['discount'] = $sell_category['discount'];


		//neu company co sell_category_key
		if(!isset($sell_category['sell_category_key']))
			$sell_category['sell_category_key'] = '';

		$sell_break = $this->product_price_break($sell_category['sell_category_key'],$product_id);
		//lay gia moi
		if(isset($sell_break['sell_price']) && $sell_break['sell_price']!='')
			$result['sell_price'] = $sell_break['sell_price'];

		//gia phu them cua option
		if(isset($sell_break['sell_price_plus']) && $sell_break['sell_price_plus']!='')
			$result['sell_price_plus'] = $sell_break['sell_price_plus'];

		//lay bang chiec khau product
		if(isset($sell_break['price_break']) && count($sell_break['price_break'])>0)
			$result['product_price_break'] = $sell_break['price_break'];

		//pr($result);die;
		return $result;

		/**Kết quả cần lấy :
		*  1.Bảng chiếc khấu trong Company 							=> $result['company_price_break']
		*  2.Chiếc khấu áp dụng cho Company 						=> $result['discount']
		*  3.Giá bán cho company dựa theo key category, 			=> $result['sell_price']
		*  4.Bảng chiếc khấu trong product dựa theo key category, 	=> $result['product_price_break']
		*/
	}

	function company_pricing($idcompany='',$product_id=''){
		$price_break = $result = array();

		if($idcompany!=''){
			if(!is_object($idcompany))
				$idcompany = new MongoId($idcompany);

			$this->selectModel('Company');

			$arr_company = $this->Company->select_one(
				array('_id'=>$idcompany),
				array('sell_category','sell_category_id','pricing','discount')
			);

			//neu co bang  pricing
			if(isset($arr_company['pricing']) && is_array($arr_company['pricing']) && count($arr_company['pricing'])>0 && $product_id!=''){
				if(is_object($product_id))
					$product_id = (string)$product_id;

				//lap va tim $price_break cho 1 san pham dang can tim
				foreach($arr_company['pricing'] as $keys => $values){
					if(isset($values['deleted']) && !($values['deleted']) && isset($values['product_id']) && (string)$values['product_id'] == $product_id ){
						if(isset($values['price_break']) && is_array($values['price_break']) && count($values['price_break'])>0 ){
							foreach($values['price_break'] as $kk=>$vv){
								if(isset($vv['deleted']) && !$vv['deleted'])
									$price_break[$kk] = $vv;
							}
						}
					}
				}

				$result['price_break'] = $price_break;
			}


			if(isset($arr_company['sell_category_id']))
				$result['sell_category_key'] = $arr_company['sell_category_id'];
			if(isset($arr_company['discount']))
				$result['discount'] = (float)$arr_company['discount'];

		}

		return $result; //ket qua tra ve la array price_break va sell_category_key

	}




	//lay data price_break tu Product dua vao Company hoac default pricing_category
	function product_price_break($sell_category_key='',$product_id=''){
		$result = array();
		$sell_price_default = '';
		$sell_category_key_df = '';

		if($product_id!=''){
			if(!is_object($product_id) && strlen($product_id)!=24)
				return '';
			else if(!is_object($product_id))
				$product_id = new MongoId($product_id);


			$arr_product = $this->select_one(
				array('_id'=>$product_id),
				array('pricebreaks','sellprices')
			);
			//tim sell_category id
			if(isset($arr_product['sellprices']) && is_array($arr_product['sellprices']) && count($arr_product['sellprices'])>0){
				$result['sell_price'] = '';
				foreach($arr_product['sellprices'] as $keys=>$values){
					if(isset($values['deleted']) && !$values['deleted'] && isset($values['sell_category'])){
						if($sell_category_key!='' && $values['sell_category'] == $sell_category_key)
							$result['sell_price'] = $values['sell_unit_price'];

						if(isset($values['sell_default']) && (int)$values['sell_default']==1){
							$sell_price_default = $values['sell_unit_price'];
							$sell_category_key_df = $values['sell_category'];
						}
					}
				}

				if($result['sell_price'] == '' && $sell_price_default!='' ){
					$result['sell_price'] = $sell_price_default;
					$sell_category_key = $sell_category_key_df;
				}

			}else if(isset($arr_product['sell_price'])){
				$result['sell_price'] = $arr_product['sell_price'];
			}

			//Cong them option
			if(isset($result['sell_price'])){
				$result['sell_price_plus'] = $this->sell_price_plus_option((string)$product_id,$result['sell_price']) - $result['sell_price'];
			}

			//tim Price breaks
			if(isset($arr_product['pricebreaks']) && is_array($arr_product['pricebreaks']) && count($arr_product['pricebreaks'])>0){

				foreach($arr_product['pricebreaks'] as $keys=>$values){
					if(isset($values['deleted']) && !$values['deleted'] && isset($values['sell_category']) && $values['sell_category'] == $sell_category_key){
						$result['price_break'][$keys] = $values;
					}
				}
			}


		}

		return $result;
	}

	function findVendorCosting($id, &$arr_return = array())
	{
		$product = $this->select_one(array('_id' => new MongoId($id)), array('madeup','name','code','sku','sell_price', 'oum', 'product_type', 'company_id'));
		if(isset($product['product_type']) && $product['product_type'] == 'Vendor Stock') {
			$tmp = $product;
			unset($tmp['madeup']);
			$arr_return[(string)$tmp['company_id']][(string)$tmp['_id']] = $tmp;
			unset($tmp);
		}
		if( !isset($product['madeup']) || empty($product['madeup']) )
			return array();
		foreach($product['madeup'] as $key => $value) {
			if(isset($value['deleted']) && $value['deleted']) continue;
			if(!isset($value['product_id']) || !is_object($value['product_id'])) continue;
			$this->findVendorCosting($value['product_id'], $arr_return);
		}
		return $arr_return;
	}

}