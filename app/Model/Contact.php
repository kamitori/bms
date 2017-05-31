<?php
require_once APP.'Model'.DS.'AppModel.php';
class Contact extends AppModel {
	public $arr_settings = array(
								'module_name' => 'Contact'
							);
	public $arr_temp = array();
	public $temp = '';
	public $arr_key_nosave = array('setup','none','none2','none3','none4','none5','none6','none7','none8','field03','field06','mongo_id');
	public $arr_type_nosave = array('autocomplete','id');

	function __construct($db) {
		$this->_setting();
		if(is_object($db)){
			$this->_setting(); // di chuyển vào trong if để không bị lỗi ->input()
			$this->collection = $db->selectCollection($this->arr_settings['colection']);
			$this->collection->ensureIndex(array('deleted'=>1), array('name'=>'deleted_id_key'));
			$this->db = $db;
			$this->change_language();
		}
	}
	// Thêm record, gán các giá trị mặc định và gán field $field = $values,
	public $arr_default_before_save = array();

	public function add($field='',$values=''){
		$this->arr_temp = array();
		if($this->arrfield()){

			if( $field != '' )
			$this->arr_temp[$field] = $values;

			//BEGIN custom
			$this->arr_temp['no'] = $this->get_auto_code('no');
			$this->arr_temp['addresses_default_key'] = 0;
			$this->arr_temp['our_rep_id'] = $this->user_id();
			$this->arr_temp['our_rep'] = $this->user_name();
			$this->arr_temp['payment_due_date'] = $this->arr_temp['Contact_date'] = new MongoDate(strtotime(date('Y-m-d')));
			$this->arr_temp['addresses'][0]['country'] = 'Canada';
			$this->arr_temp['addresses'][0]['country_id'] = 'CA';
			$this->arr_temp['addresses'][0]['deleted'] = false;
			$this->arr_temp['addresses'][0]['province_state'] = '';
			$this->arr_temp['addresses'][0]['province_state_id'] = '';
			$this->arr_temp['addresses'][0]['address_1'] = '';
			$this->arr_temp['addresses'][0]['address_2'] = '';
			$this->arr_temp['addresses'][0]['address_3'] = '';
			$this->arr_temp['addresses'][0]['town_city'] = '';
			$this->arr_temp['addresses'][0]['zip_postcode'] = '';
			$this->arr_temp['addresses'][0]['default'] = true;
			$this->arr_temp['commisstion'] = 1;

			//$this->arr_temp['is_employee']= 1;
			//$this->arr_temp['shipping_address'][0]['shipping_country'] = 'CA';
			//$this->arr_temp['shipping_address'][0]['delete'] = false;
			//END custom

			// BaoNam: arr_default_before_save dùng để gán trước các giá trị mặc định từ controller, ví dụ trong các tạo từ các options
			// mục đích là dùng chung 1 hàm add thôi để sau này chỉ sửa 1 chỗ
			$this->arr_temp = array_merge($this->arr_temp, $this->arr_default_before_save);

			$this->save($this->arr_temp);
			return $this->mongo_id_after_save.'||'.$values; //xuất ra chuỗi để js hiển thị ra html
		}else
			return '';
	}



	//update field $field  = $values của record có _id là $ids
	public function update($ids='',$field='',$values=''){
			$arr_temp = $cal_price = array();
			$arr_temp['_id'] = $ids;
			if(preg_match("/_date$/",$field))
				$arr_temp[$field] = new MongoDate($values);
			else
				$arr_temp[$field] = $values;
			if($this->save($arr_temp))
				return $ids.'||'.$arr_temp[$field];
	}

	public function save(array $arr = array()) {
		if(isset($arr['roles'])) {
			$array = array('@arr_menu_','@arr_permission_','arr_inactive_permission');
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
		}
		return parent::save($arr);
	}

	public function clear_anvy_support(){
		$arr_cond = $arr = array();
		$arr_cond['anvy_support'] = 1;
		$arr['anvy_support'] = 0;
		$this->collection->update($arr_cond, array( '$set' => $arr ), array('multiple' => true));
	}

	public function tao_data_demo_contact($arr_company_id){
		$where = array();
		$where['company_id']['$nin'] = $arr_company_id;
		$arr_set = array('deleted'=>true);
		$this->collection->update($where, array( '$set' => $arr_set ), array('multiple' => true));
	}

}