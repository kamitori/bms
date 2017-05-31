<?php
require_once APP.'Model'.DS.'AppModel.php';
class Basic extends AppModel {
	public $arr_settings = array(
								'module_name' => 'Basic'
							);
	private $arr_temp = array();
	private $temp = '';
	public $arr_key_nosave = array('setup','none','none2','mongo_id');
	public $arr_type_nosave = array('autocomplete','id');

	function __construct($db) {
		if(is_object($db)){
			$this->_setting();
			$this->collection = $db->selectCollection($this->arr_settings['colection']);
			$this->collection->ensureIndex(array('deleted'=>1), array('name'=>'deleted_id_key'));
			//$this->db = $db;
			//$this->change_language();
		}
	}

	// nhận giá trị cho $arr_settings từ $ModuleNameField (ModuleNameField.php)
	function _setting(){
		$module_field = $this->arr_settings['module_name'].'Field';
		require_once APP . 'Model'.DS.$module_field.'.php';
		$this->arr_settings = $$module_field;
	}

	//List ra các field của module. array('field'=> giá trị default của field)
	public function arrfield(){
		foreach($this->arr_settings['field'] as $keys =>$arr_field){
			foreach($arr_field as $k =>$arr_v){
				// bỏ các key setup,none,mongo_id,..
				if(!in_array($k,$this->arr_key_nosave)){
					if(isset($arr_v['default']))
						$this->arr_temp[$k] = $arr_v['default'];
					else
						$this->arr_temp[$k] = '';
				}
			}

		}
		if(isset($this->arr_temp) && count($this->arr_temp)>0)
			return count($this->arr_temp);
		else
			return 0;
	}


	//List ra fields của module. Vd: $this->arr_temp['name'] = $arr_v['type']
	public function arr_field_key($keyfind=''){
		$this->arr_temp = array();
		foreach($this->arr_settings['field'] as $keys =>$arr_field){
			foreach($arr_field as $k =>$arr_v){
				if(!in_array($k,$this->arr_key_nosave) && isset($arr_v[$keyfind]) )
					$this->arr_temp[$k] = $arr_v[$keyfind];
			}

		}
		return $this->arr_temp;
	}

	//List ra các field của relationship module.Vd: [supplier] = array(..)
	public function arr_field_rel($fields=''){
		$this->arr_temp = array();
		foreach($this->arr_settings['relationship'] as $keys =>$arr_field){
			foreach($arr_field as $ks =>$arr_s){
				if($ks=='block')
				foreach($arr_s as $kb =>$arr_b){
					if($fields=='' || ($fields!='' && $fields == $kb))
					$arr_temp[$kb] = $arr_b;
				}
			}
		}
		foreach($arr_temp as $kv =>$arr_v){
			foreach($arr_v as $kf =>$arr_f){
				if($kf=='field')
				$this->arr_temp[$kv] = $arr_f;
			}
		}
		if($fields!='')
			return $this->arr_temp[$fields];
		else
			return $this->arr_temp;
	}


	//List ra các field cho phép hiện trong ListView
	public function list_view_field(){
		$this->arr_temp = array();
		foreach($this->arr_settings['field'] as $keys =>$arr_field){
			foreach($arr_field as $k =>$arr_v){
				if(isset($this->arr_settings['field'][$keys][$k]['listview']))
					$this->arr_temp[$k] = $arr_v['listview'];
			}
		}
		return $this->arr_temp;
	}



	// Thêm record, gán các giá trị mặc định và gán field $field = $values,
	public function add($field='',$values=''){
		if($this->arrfield()){
			//BEGIN special
			$cauto = $this->max_field('code');
			$this->arr_temp['code'] = 1+(int)$cauto['code'];
			$this->arr_temp[$field] = $values;
			//END special

			$this->save($this->arr_temp);
			return $this->mongo_id_after_save.'||'.$values; //xuất ra chuỗi để js hiển thị ra html
		}else
			return '';
	}


	//update field $field  = $values của record có _id là $ids
	public function update($ids='',$field='',$values=''){
			$this->arr_temp = array(); $this->temp ='';
			$this->arr_temp['_id'] = $ids;
			$this->arr_temp[$field] = $values;
			if($this->save($this->arr_temp))
				return $ids.'||'.$this->arr_temp[$field];
	}

	public function arr_autocomplete(){
		$temp = $this->arr_field_key('type');
		foreach($temp as $sk => $sv){ //type autocomplete
			if($sv=='autocomplete'){
				$autocomplete_key[$sk] = $sk;
			}
		}
		return $autocomplete_key;
	}

	public function arr_nonsave(){
		$temp = $this->arr_field_key('type');
		foreach($temp as $sk => $sv){ //type none save
			if(in_array($sk,$this->arr_type_nosave)){
				$arr_nonsave[$sk] = $sk;
			}
		}
		return $arr_nonsave;
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




}