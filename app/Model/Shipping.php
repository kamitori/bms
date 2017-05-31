<?php
require_once APP.'Model'.DS.'AppModel.php';
class Shipping extends AppModel {
	public $arr_settings = array(
								'module_name' => 'Shipping'
							);
	public $arr_temp = array();
	public $temp = '';
	public $arr_key_nosave = array('setup','none','none2','mongo_id');
	public $arr_type_nosave = array('autocomplete','id');

	function __construct($db) {
		if(is_object($db)){
			$this->_setting();
			$this->collection = $db->selectCollection($this->arr_settings['colection']);
			$this->collection->ensureIndex(array('deleted'=>1), array('name'=>'deleted_id_key'));
			$this->db = $db;
			$this->change_language();
		}
	}

	// Thêm record, gán các giá trị mặc định và gán field $field = $values,
	var $arr_default_before_save = array();
	public function add($field='',$values=''){
		$this->arr_temp = array();
		if($this->arrfield()){

			// BaoNam:fix
			if( $field != '' )
				$this->arr_temp[$field] = $values;

			//BEGIN custom
			$this->arr_temp['code'] = $this->get_auto_code('code');
			$this->arr_temp['our_rep_id'] = $this->user_id();
			$this->arr_temp['our_rep'] = $this->user_name();
			$this->arr_temp['shipping_date'] = new MongoDate(time());
			$this->arr_temp['invoice_address'][0]['invoice_country'] = 'CA';
			$this->arr_temp['invoice_address'][0]['deleted'] = false;
			$this->arr_temp['shipping_address'][0]['shipping_country'] = 'CA';
			$this->arr_temp['shipping_address'][0]['deleted'] = false;
			//END custom


			// BaoNam: arr_default_before_save dùng để gán trước các giá trị mặc định từ controller, ví dụ trong các tạo từ các options
			// mục đích là dùng chung 1 hàm add thôi để sau này chỉ sửa 1 chỗ
			$this->arr_temp = array_merge($this->arr_temp, $this->arr_default_before_save);

			$this->save($this->arr_temp);
			$this->arr_temp = array();
			return $this->mongo_id_after_save.'||'.$values; //xuất ra chuỗi để js hiển thị ra html
		}else
			return '';
	}

	//update field $field  = $values của record có _id là $ids
	public function update($ids='',$field='',$values=''){
		if(!in_array($field,$this->arr_key_nosave) && !in_array($field,$this->arr_rel_set())){
			$arr_temp = $cal_price = array();
			$arr_temp['_id'] = $ids;

			//tim cac field name cho phep custom va set id = ''
			$arr_temp = array_merge((array)$arr_temp,(array)$this->set_null_id($field));

			//END special
			if(preg_match("/_date$/",$field))
				$arr_temp[$field] = new MongoDate($values);
			else if(preg_match("/_id$/",$field) && !is_object($values))
				$arr_temp[$field] = new MongoId($values);
			else if($field == 'tracking_no')
				$arr_temp[$field] = (string)$values;
			else
				$arr_temp[$field] = $values;
			if($this->save($arr_temp))
				return $ids.'||'.$arr_temp[$field];
		}else if($ids!='')
			return $ids;
	}



	//Sắp xếp lại thứ tự arr_setting và bỏ bớt các giá trị mảng ko dùng
	public function re_array_fields($arr_new=array(),$arr_fields=array()){
		$ret = array();
		foreach($arr_new as $vss){
			$ret[$vss] = $arr_fields[$vss];
		}
		return $ret;
	}




}