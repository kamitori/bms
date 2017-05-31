<?php
require_once APP.'Model'.DS.'AppModel.php';
class Tax extends AppModel {
	public $arr_settings = array(
								'module_name' => 'Tax'
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
	public function add($field='',$values=''){
		$this->arr_temp = array();
		if($this->arrfield()){
			$this->arr_temp[$field] = $values;
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
			if(preg_match("/_date$/",$field))
				$arr_temp[$field] = new MongoDate($values);
			else
				$arr_temp[$field] = $values;
			if($this->save($arr_temp))
				return $ids.'||'.$arr_temp[$field];
	}


	//return array tax
	public function tax_select_list(){
		$arr_ret = Cache::read('tax_select_list');
		if(!$arr_ret){
				$arr_tmp = $this->select_all(array(
				'arr_order' => array('_id'=>-1),
			));
			$arr_ret = array();
			$arr_ret[''] = 'Not used';
			foreach($arr_tmp as $kk=>$vv){
				if(isset($vv['hst_tax']) && $vv['hst_tax']=='H')
					$typetax = 'HST';
				else if($vv['province_key']=='NO')
					$typetax = '';
				else
					$typetax = 'GST';
				if(isset($vv['province_key']) && isset($vv['fed_tax']) && isset($vv['province']))
				$arr_ret[$vv['province_key']] = $vv['fed_tax'].'% ('.$vv['province'].') '.$typetax;
			}
			Cache::write('tax_select_list',$arr_ret);
		}
		return $arr_ret;
	}

	//return array tax
	public function tax_select_list_keyislabel(){
		$arr_ret = Cache::read('tax_select_list_keyislabel');
		if(!$arr_ret){
			$arr_tmp = $this->select_all(array(
				'arr_order' => array('_id'=>-1),
			));
			$arr_ret = array();
			$arr_ret[''] = '0% (No tax)';
			foreach($arr_tmp as $kk=>$vv){
				if(isset($vv['hst_tax']) && $vv['hst_tax']=='H')
					$typetax = 'HST';
				else
					$typetax = 'GST';
				if(isset($vv['province']) && isset($vv['fed_tax']) && isset($vv['province']))
				$arr_ret[$vv['province']] = $vv['fed_tax'].'% ('.$vv['province'].') '.$typetax;
			}
			Cache::write('tax_select_list_keyislabel',$arr_ret);
		}
		return $arr_ret;
	}



	//return array tax with value tax
	public function tax_list(){
		$arr_ret = Cache::read('tax_list');
		if(!$arr_ret){
			$arr_tmp = $this->select_all(array(
				'arr_order' => array('_id'=>-1),
			));
			$arr_ret = array();
			$arr_ret[''] = 0;
			foreach($arr_tmp as $kk=>$vv){
				if(isset($vv['hst_tax']) && $vv['hst_tax']=='H')
					$typetax = 'HST';
				else
					$typetax = 'GST';
				if(isset($vv['province_key']) && isset($vv['fed_tax']) && isset($vv['province']))
				$arr_ret[$vv['province_key']] = (float)$vv['fed_tax'];
			}
			Cache::write('tax_list',$arr_ret);
		}
		return $arr_ret;
	}


}