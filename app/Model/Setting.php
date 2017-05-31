<?php
require_once APP.'Model'.DS.'AppModel.php';
class Setting extends AppModel {
	public function __construct($db) {
		if(is_object($db)){
			$this->collection = $db->selectCollection('tb_settings');
			$this->collection->ensureIndex(array("setting_name"=>1), array('name'=>"setting_name_key", "unique"=>1, "dropDups" => 1));
		}
		$this->has_field_deleted = false; // because tb_setting has no field "deleted"
	}
	public function select_option(array $arr_where = array(), $order_abc = true, $html_more = false){
		$cache_key = md5(serialize($arr_where));
		$arr_tmp = Cache::read('select_option_'.$cache_key);
		if(!$arr_tmp){
			if(isset($_SESSION['default_lang']) && $_SESSION['default_lang']!=DEFAULT_LANG){
				$names = 'name_'.$_SESSION['default_lang'];
			}else
				$names = 'name';
			$arr_option = $this->collection->findOne($arr_where, array('_id', 'sort_abc', 'option'));
			$arr_tmp = array();
			if(!empty($arr_option)&&isset($arr_option['option'])){
				foreach ($arr_option['option'] as $key => $value) {
					if(isset($value['deleted']) && $value['deleted'])continue;
					if(isset($value['value'])){
						if( $html_more ){
							$arr_tmp[COMBOBOX_SEPARATE.$value['value']] = (isset($value[$names])?$value[$names]:$value['name']); // thêm _jt@_ mục đích là để khi chuyển qua mảng json không bị mất thứ tự (jquery combobox plugin)
						}else{
							$arr_tmp[$value['value']] = (isset($value[$names])?$value[$names]:$value['name']);
						}
					}
				}
				if( isset($arr_option['sort_abc']) && $arr_option['sort_abc'] ){
					asort($arr_tmp);
				}
			}
			Cache::write('select_option_'.$cache_key,$arr_tmp);
		}
		return $arr_tmp;
	}

	public function update_field_option($mongo_id, $idx, $field_name, $value){
		try{
			$this->collection->update(
				array(
					'_id' => new MongoId($mongo_id)
				),
				array(
					'$set' => array( 'option.'.$idx.'.'.$field_name => $value )
				)
			);
			return true;
		}catch(MongoCursorException $e){
			$this->arr_errors_save = array( $e->getCode(), $e->getMessage() );
			return false;
		}
	}

	public function select_option_vl(array $arr_where = array()) {
		$cache_key = md5(serialize($arr_where));
		$arr_tmp = Cache::read('select_option_vl_'.$cache_key);
                if(!$arr_tmp){
			if(isset($_SESSION['default_lang']) && $_SESSION['default_lang']!=DEFAULT_LANG){
				$names = 'name_'.$_SESSION['default_lang'];
			}else
				$names = 'name';
			$arr_option = $this->collection->findOne($arr_where, array('option','sort_abc'));
			$arr_tmp = array();
			if(isset($arr_option['option'])){
				foreach ($arr_option['option'] as $key => $value) {
					if(isset($value['value']))
						$vals = $value['value'];
					else if(isset($value['id']))
						$vals = $value['id'];
					else
						$vals = $key;
					if(isset($value['deleted']) && $value['deleted'])continue;
					if(isset($value[$names]))
						$arr_tmp[$vals] = $value[$names];
					else
						$arr_tmp[$vals] = $value['name'];
				}
				if( isset($arr_option['sort_abc']) && $arr_option['sort_abc'] ){
					asort($arr_tmp);
				}
			}
			Cache::write('select_option_vl_'.$cache_key,$arr_tmp);
		}
		return $arr_tmp;
	}

	public function option_build($settingname='',$sellect_id='') {
		$option_select = '<option value="" label=""></option>';
		$arr_list = $this->select_one(array('setting_value'=>$settingname),array('option'));

		foreach($arr_list['option'] as $keys => $arr_value){
			if(isset($arr_value['deleted']))
				$del = $arr_value['deleted'];
			else
				$del = false;
			if(isset($arr_value['value']))
				$vals = $arr_value['value'];

			else if(isset($arr_value['id']))
				$vals = $arr_value['id'];

			else if(isset($arr_value['name']))
				$vals = $arr_value['name'];

			else
				$vals = '';

			if(!$del){
				$option_select .= '<option value="'.$vals.'" label="'.$arr_value['name'].'"';
				if($vals==$sellect_id)
					$option_select .= 'select="selected" >'.$arr_value['name'].'</option>';
				else
					$option_select .= '>'.$arr_value['name'].'</option>';
			}
		}
		return $option_select;
	}



	public function uom_option_list($is_reverse=false) {
		$new_arr = array();
		$arr_uom_type = array('product_oum_unit','product_oum_area','product_oum_lengths');
		$uom_option_list = $this->select_all(array(
			'arr_where' => array(
				'setting_value' => array('$in'=>$arr_uom_type),
			),
			'arr_field' => array('option','setting_value')
		));
		foreach($uom_option_list as $keys=>$values){
			foreach($values['option'] as $kk=>$vv){
				if(!$is_reverse)
					$new_arr[$vv['value']] = str_replace("product_oum_","",$values['setting_value']);
				else{
					if(isset($new_arr[str_replace("product_oum_","",$values['setting_value'])])) continue;
					$new_arr[str_replace("product_oum_","",$values['setting_value'])] = $vv['value'];
				}
			}
		}
		return $new_arr;
	}



	public function save(array $arr = array()){
		$array = array('arr_companies','arr_messages','arr_countries','arr_currency','@arr_province_','arr_permission','minimun','minimun_product','@select_option_', '@salesorder','@salesorders','@salesinvoice','@salesinvoices','@quotation','@quotations','@shipping','@shippings','@product','@products','@contact','@contacts','@companies','@shipping','@shippings');
		foreach($array as $value){
			if(strpos($value, '@') !== false){
	    		$value = ltrim($value, '@');
	    		$arr_caches = $this->get_cache_keys_diff('',$value);
	    		foreach($arr_caches as $cache){
	    			Cache::delete($cache);
	    		}
	    	} else
	    		Cache::delete($value);
		}
		return parent::save($arr);
	}
}