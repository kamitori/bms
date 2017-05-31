<?php
require_once APP.'Model'.DS.'AppModel.php';
class Company extends AppModel {

	public $arr_settings = array(
								'module_name' => 'Company'
							);
	public $arr_temp = array();
	public $temp = '';
	public $arr_key_nosave = array('setup','none','none2','mongo_id','none11','none3','none4','none5','none6','none7');
	public $arr_type_nosave = array('autocomplete','id');

	public function __construct($db) {
		if(is_object($db)){
			$this->_setting(); // di chuyển vào trong if để không bị lỗi ->input()
			$this->collection = $db->selectCollection($this->arr_settings['colection']);
			$this->collection->ensureIndex(array('deleted'=>1), array('name'=>'deleted_id_key'));
			$this->collection->ensureIndex(array("lower('name')" =>1, 'caseInsensitive' => true), array('name'=>'name_id_key'));
			$this->db = $db;
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
			$this->arr_temp['our_rep_id'] = $this->user_id();
			$this->arr_temp['our_rep'] = $this->user_name();
			$this->arr_temp['our_csr_id'] = $this->arr_temp['our_rep_id'];
			$this->arr_temp['our_csr'] = $this->arr_temp['our_rep'];
			$this->arr_temp['addresses_default_key'] = 0;
			$address[0]['name'] = '';
			$address[0]['deleted'] = false;
			$address[0]['country'] = "Canada";
			$address[0]['country_id'] = 'CA';
			$address[0]['province_state'] = '';
			$address[0]['province_state_id'] = '';
			$address[0]['address_1'] = '';
			$address[0]['address_2'] = '';
			$address[0]['address_3'] = '';
			$address[0]['town_city'] = '';
			$address[0]['zip_postcode'] = '';
			$address[0]['default'] = true;
			$this->arr_temp['addresses'] = $address;

			//END custom
			$this->arr_temp = array_merge($this->arr_temp, $this->arr_default_before_save);
			if( isset($this->arr_temp['system']) && $this->arr_temp['system'] ){
				$this->arr_temp['no'] = 1;
			}
			$this->save($this->arr_temp);
			if( isset($this->arr_temp['system']) && $this->arr_temp['system'] ) {
				$this->arr_temp['identity'] = new MongoId($this->mongo_id_after_save);
				$this->arr_temp['_id'] = new MongoId($this->mongo_id_after_save);
				$this->save($this->arr_temp);
			}
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
			else if(preg_match("/_id$/",$field) && !is_object($values)){
				if(strlen($values) == 24 && $field != '_id')
					$arr_temp[$field] = new MongoId($values);
				else
					$arr_temp[$field] = '';
			} else
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


	public function tao_data_demo_company(){
		$where = array();
		$where['name'] = new MongoRegex("/Van Houtte/i");
		$van_houtte = $this->collection->find($where, array('name'))->limit(99999);
		$where = array();
		$where['_id']['$nin'] = array(
										new MongoId('541b4eadb0e6df4c080001ac'),
										new MongoId('5271dab4222aad6819000ed0'),
									);
		foreach ($van_houtte as $key => $value) {
			$where['_id']['$nin'][] = new MongoId($key);
		}
		$arr_set = array('deleted'=>true);
		$this->collection->update($where, array( '$set' => $arr_set ), array('multiple' => true));
		return $where['_id']['$nin'];
	}


}