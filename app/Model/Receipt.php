<?php
require_once APP.'Model'.DS.'AppModel.php';
class Receipt extends AppModel {
	public $arr_settings = array(
								'module_name' => "Receipt"
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
			$this->collection->ensureIndex(array('company_id'=>1), array('name'=>'company_id'));
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
			$this->arr_temp['code'] = $this->get_auto_code('code');
			$this->arr_temp['our_rep_id'] = $this->user_id();
			$this->arr_temp['our_rep'] = $this->user_name();
			$this->arr_temp['receipt_date'] = new MongoDate(time());
			if($field=='amount_received')
				$this->arr_temp[$field] = (float)$values;
			$this->arr_temp['allocation'] = array();
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
			$arr_temp = $cal_price = array();
			$arr_temp['_id'] = $ids;

			//BEGIN special
			if($field=='amount_received')
				$arr_temp[$field] = (float)$values;
			//END special
			else if(preg_match("/_date$/",$field))
				$arr_temp[$field] = new MongoDate($values);
			else if(preg_match("/_id$/",$field) && !is_object($values))
				$arr_temp[$field] = new MongoId($values);
			else
				$arr_temp[$field] = $values;

			if($this->save($arr_temp))
				return $ids.'||'.$arr_temp[$field];
	}


	//List ra các field cho phép hiện trong ListView
	public function list_view_field(){
		$this->arr_temp = $newdata = array();
		foreach($this->arr_settings['field']['panel_1'] as $k =>$arr_v){
			if(isset($this->arr_settings['field']['panel_1'][$k]['listview'])){
				if(!isset($arr_v['listview']['order']))
					$arr_v['listview']['order'] = 0;
				$sort = (int)$arr_v['listview']['order'];
				$this->arr_temp[$sort][$k] = $arr_v['listview'];
			}
		}

		for($m=0;$m<=count($this->arr_temp);$m++){
			if(isset($this->arr_temp[$m]))
			foreach($this->arr_temp[$m] as $kss=>$vss){
				$newdata[$kss] = $vss;
			}
		}
		$this->arr_temp = array();
		return $newdata;
	}


}