<?php

class AppModel {
	public $primaryKey = '_id';
	public $hasAndBelongsToMany = array();
	public $hasOne = array();
	public $belongsTo =array();
	public $hasMany = array();
	public $has_field_deleted = true;
	public $collection = null;
	protected $db = null;
	public $old_data = null;
	public $mongo_id_after_save = null;
	public $arr_errors_save = null;

	//Schema
	public function schema($field = false) {
		return null;
	}

	// Lưu các field vừa mới thay đổi.
	public function save_log($arr=array()){
		// return true;//Mở khóa lưu log
		if(isset($this->db) && is_object($this->db) && count($arr)>0 && isset($arr['_id'])){
			require_once APP.'Model'.DS.'Log.php';
			$ModelLog = new Log($this->db);
			$arr_temp = array();
			$arr_old = array();
			foreach ($arr as $kk=>$vv){
				if( $kk == 'deleted' && $vv==false) continue;
				if( $kk == 'date_modified' ) continue;
				if( $kk == 'modified_by' ) continue;
				if( $kk == '_id' ) continue;
				if(is_array($vv) && !empty($vv) ){
					foreach($vv as $k=>$v){
						if(is_array($v) && !empty($v)){
							foreach($v as $subkey=>$subvalue){
								if(!isset($this->old_data[$kk][$k][$subkey])){
									$arr_old[$kk][$k][$subkey] = array();
									$arr_temp[$kk][$k][$subkey] = $subvalue;
								}
								else if(isset($this->old_data[$kk][$k][$subkey])&&$this->old_data[$kk][$k][$subkey]!=$subvalue){
									$arr_temp[$kk][$k][$subkey] = $subvalue;
									$arr_old[$kk][$k][$subkey] = (!isset($this->old_data[$kk][$k][$subkey])||$this->old_data[$kk][$k][$subkey]==null ? '' : $this->old_data[$kk][$k][$subkey]);
								}
							}
						}
					}
				}
				else if( ((isset($this->old_data[$kk]) && $vv!=$this->old_data[$kk]) || !isset($this->old_data[$kk])) ){
					if(preg_match("/./",$kk))
						$kk = str_replace(".","@",$kk);
					$arr_temp[$kk] = $vv;
					$arr_old[$kk] = (!isset($this->old_data[$kk])||$this->old_data[$kk]==null ? '' : $this->old_data[$kk]);
				}
			}
			if(!empty($arr_temp)){
				$ModelLog->save(
					array(
						'module'	=> get_class($this),
						'change_from'=>$arr_old,
						'change_to'	=> $arr_temp,
						'item_id'	=> $arr['_id']
					)
				);
				return true;
			}else return false;
		}else
			return false;
	}


	//Select one item
	public function select_one(array $arr_where = array(), array $arr_field = array(), array $arr_sort = array()){
		if($this->has_field_deleted)
			$arr_where = array_merge(array('deleted' => false), $arr_where);
		if( isset($arr_where['is_employee']) && !isset($arr_where['inactive']) ){
			$arr_where['inactive'] = 0;
		}
		if(isset($arr_where['deleted']) && $arr_where['deleted'] == 'no_search')unset($arr_where['deleted']); // dùng cho trường hợp không muốn tìm điều kiện deleted

		if(empty($arr_sort)){
			return $this->collection->findOne($arr_where, $arr_field);
		}else{
			$arr_return = $this->collection->find($arr_where, $arr_field)->sort($arr_sort)->limit(1);
			foreach ($arr_return as $key => $value) {
				return $value;
			}
			return array();
		}
	}

	// Count item in database
	public function count(array $arr_where = array()){
		if($this->has_field_deleted)
			$arr_where = array_merge(array('deleted' => false), $arr_where);
		return $this->collection->count($arr_where);
	}

	//Sum total for lists view
	public function sum($p_column, $collection , array $arr_where = array()){
		$v_function_map = new MongoCode("function() { emit('total', this.".$p_column."); }");
		$v_function_reduce = new MongoCode("function(k, vals) {
												var sum = 0;
												for(i in vals) {
													sum += vals[i];
												}
												return sum;
											}
		");

		$arr_sumtotal = $this->db->command(array(
			'mapreduce' => $collection,
			'map' => $v_function_map,
			'reduce' => $v_function_reduce,
			'query'=> $arr_where,
			'out' => array('merge' => 'tb_sum_result')
		));
		$arr_result = $this->db->selectCollection($arr_sumtotal['result'])->findOne();
		$this->db->tb_sum_result->remove(array("_id" => "total"));
		return isset($arr_result['value'])?$arr_result['value']:0;
	}


	//Get max value of on field
	public function max_field($field='',array $arrWhere = array()){
		$arr_where = $arrWhere;
		$arr_where[$field] = array('$gt' => 0);
		if(IS_NEW_CODE)
			$arr_where = $arrWhere;
		$arr_where['api_key'] = array('$exists' => false);
		$this->identity($arr_where);
		$arr_return = $this->collection->find($arr_where,array($field => 1))->sort(array('_id' => -1))->limit(1);
		foreach($arr_return as $key => $value) {
			return $value;
		}
		return '';
	}


	//Select all item which where
	public function select_all(array $arr_options = array()){
		$arr_default = array(
			'arr_where' => ($this->has_field_deleted)?array('deleted' => false):array(),
			'arr_field' => array(),
			'arr_order' => array(), // array('_id' => -1);//last insert show first
			'limit' => 99999,
			'skip' => 0
		);
		$arr_options = array_merge($arr_default, $arr_options);
		if($this->has_field_deleted && !isset($arr_options['arr_where']['deleted']))$arr_options['arr_where']['deleted'] = false;
		if(isset($arr_options['arr_where']['deleted']) && $arr_options['arr_where']['deleted'] == 'no_search')unset($arr_options['arr_where']['deleted']); // dùng cho trường hợp không muốn tìm điều kiện deleted

		foreach ($arr_options['arr_where'] as $key => $value) {
			if(is_numeric($value))$arr_options['arr_where'][$key] = (int)$value;
		}

		if( isset($arr_options['arr_where']['is_employee']) && !isset($arr_options['arr_where']['inactive']) ){
			$arr_options['arr_where']['inactive'] = 0;
		}
		if(get_class($this)=='Salesinvoice' && isset($arr_options['arr_order']) && isset($arr_options['arr_order']['code'])){
			$arr_options['arr_order']['_id'] = $arr_options['arr_order']['code'];
			unset($arr_options['arr_order']['code']);
		}

		return $this->collection->find($arr_options['arr_where'], $arr_options['arr_field'])->sort($arr_options['arr_order'])->skip($arr_options['skip'])->limit($arr_options['limit']);
	}


	// this function returns array( key => value, key1 => value1, key2 => value2, key3 => value3, ... ) to use in input select box or sth else belongs to you
	public function select_list(array $arr_options = array()){
		$arr_default = array(
			'arr_where' => ($this->has_field_deleted)?array('deleted' => false):array(),
			'arr_field' => array('_id', '_id'), // array[0] is key, array[1] is value
			'arr_order' => array(), // array('_id' => -1);//last insert show first
			'limit' => 1000
		);
		$arr_options = array_merge($arr_default, $arr_options);
		foreach ($arr_options['arr_where'] as $key => $value) {
			if(is_numeric($value))$arr_options['arr_where'][$key] = (int)$value;
		}

		if( isset($arr_options['arr_where']['is_employee']) && !isset($arr_options['arr_where']['inactive']) ){
			$arr_options['arr_where']['inactive'] = 0;
		}

		$arr_data = $this->collection->find($arr_options['arr_where'], $arr_options['arr_field'])->sort($arr_options['arr_order'])->limit($arr_options['limit']);
		$arr_tmp = array();
		if(!isset($arr_options['arr_field'][1])){
			$arr_options['arr_field'][1] = $arr_options['arr_field'][0];
			$arr_options['arr_field'][0] = '_id';
		}
		foreach ($arr_data as $value) {
			if(isset($arr_options['arr_field'][2])){
				$arr_tmp[(string)$value[$arr_options['arr_field'][0]]] = $value[$arr_options['arr_field'][1]] .' '.$value[$arr_options['arr_field'][2]];
			}else if(isset($value[$arr_options['arr_field'][1]])){
				$arr_tmp[(string)$value[$arr_options['arr_field'][0]]] = $value[$arr_options['arr_field'][1]];
			}

		}
		return $arr_tmp;
	}




	/*	Save function: main function
		- if isset _id => update else insert
		- auto save create_by, modify_by, date_modified
		- if you want to access _id after you save, use $this->ModelName->mongo_id_after_save in controller to get value of _id
	*/
	public function save(array $arr = array()){
		try{
			foreach ($arr as $key => $value) {
				if(is_string($value))$arr[$key] = trim($arr[$key]);
				if( in_array($key, array('tracking_no', 'mobile', 'mobile_login','fax', 'phone')) || strpos($key, '_comment') !== false) {
					$arr[$key] = (string)$value;
					continue;
				}
				if(is_string($value) && substr($value, 0, 1) != "+" && is_numeric($value) && !is_float($value) && ( substr($value, 0, 1) != "0" || $value == "0" ) )$arr[$key] = (int)$value;

				if( $key == 'name' && is_string($arr[$key]) && strlen($arr[$key]) > 0 )
					$arr[$key]{0} = strtoupper($arr[$key]{0});

				// if(!is_array($value))
					// if(trim($value) == "")$arr[$key] = " "; // blank to save to db, we have fields, if not mongo will remove field
			}
			if(!isset($arr['_id'])){// add new
				$this->identity($arr);
				$arr['_id'] = new MongoId();

				//Minh, For services without session
				if(isset($_SESSION['arr_user']))
				{
					$contact_id = $_SESSION['arr_user']['contact_id'];
				}
				else
				{
					$contact_id = isset($_GET['contact_id']) ? $_GET['contact_id'] : null;
				}

				$arr['created_by'] = $arr['modified_by'] = $contact_id;
				$arr['date_modified'] = new MongoDate(strtotime('now'));
				if($this->has_field_deleted)
					$arr = array_merge(array('deleted' => false), $arr);
				$this->collection->insert($arr, array('safe'=>true));
				// Save log
				// $this->save_log($arr);

			}else{ //is update
				if(!is_object($arr['_id']))
					$arr['_id'] = new MongoId($arr['_id']);
				$class_name = get_class($this);
				$this->old_data = $this->select_one(array( '_id' => new MongoId($arr['_id'])));//old data
				if( !isset($this->old_data['identity']) ) {
					$this->identity($arr);
				}

				$arr['modified_by'] = isset($_SESSION['arr_user']['contact_id']) ? $_SESSION['arr_user']['contact_id'] : null;
				$arr['date_modified'] = new MongoDate(strtotime('now'));
				if($this->has_field_deleted)
					$arr = array_merge(array('deleted' => false), $arr);
				$arr_tmp_save = $arr;
				unset($arr_tmp_save['_id']);
				$this->collection->update(array( '_id' => new MongoId($arr['_id'])), array( '$set' => $arr_tmp_save ));
				$this->save_log($arr);
			}
			$this->mongo_id_after_save = $arr['_id'];

			return true;
		}catch(MongoCursorException $e){
			$this->arr_errors_save = array( $e->getCode(), $e->getMessage() );
			return false;
		}
	}


	//Update all record
	public function update_all(array $arr_cond = array(), array $arr = array()){
		try{
			foreach ($arr as $key => $value) {
				if(is_numeric($value) && !is_float($value))$arr[$key] = (int)$value;
				if(!is_array($value))
					if(trim($value) == "")$arr[$key] = ""; // blank to save to db, we have fields, if not mongo will remove field
			}

			$arr['modified_by'] = $_SESSION['arr_user']['contact_id'];
			$arr['date_modified'] = new MongoDate(strtotime('now'));
			if($this->has_field_deleted)
				$arr = array_merge(array('deleted' => false), $arr);
			$this->collection->update($arr_cond, array( '$set' => $arr ), array('multiple' => true));
			return true;
		}catch(MongoCursorException $e){
			$this->arr_errors_save = array( $e->getCode(), $e->getMessage() );
			return false;
		}
	}






	/*===========================================================
							MODULE FUNCTION
	===========================================================*/
	/*
		arrfield():
		arr_field_key($keyfind)
		arr_field_rel($fields)
		list_view_field()
		main_field($field)
		arr_nonsave()

	===========================================================*/
	public $arr_settings = array();
	public $arr_key_nosave = array();
	public $arr_type_nosave=array();
	public $arr_temp = array();
	public $temp = '';


	// nhận giá trị cho $arr_settings từ $ModuleNameField (ModuleNameField.php)
	function _setting(){
		$module_field = $this->arr_settings['module_name'].'Field';
		//An, 01-3-2014 Module Studio(Neu co sua layout thi lay tu thu muc Custom ra)
		$custom_dir = ROOT.DS.APP_DIR.DS.'Model'.DS.'Custom';
		$file_name = ROOT.DS.APP_DIR.DS.'Model'.DS.'Custom'.DS.$module_field.'.php';
		if(is_dir($custom_dir) && is_file($file_name)){
			require APP.'Model'.DS.'Custom'.DS.$module_field.'.php';
		}else{
			require APP.'Model'.DS.$module_field.'.php';
		}
		$this->arr_settings = $$module_field;
	}

	// Function get_language($key){
	public function get_language(){
		if(!isset($_SESSION['default_lang']))
			$key = DEFAULT_LANG;
		else
			$key = $_SESSION['default_lang'];
		$arr_tmp = array();
		if(!isset($_SESSION['arr_lang_'.$key])){
			if(isset($this->db) && is_object($this->db)){
				require_once APP.'Model'.DS.'Languagedetail.php';
				$Languagedetail = new Languagedetail($this->db);
				$arr_temp = array();
				$arr_language = $Languagedetail->select_all(array(
					'arr_where' => array('content.'.$key => array('$exists'=>true,'$ne' => ''),
										'key' => array('$ne'=>''),
						),
					'arr_order' => array('key' => 1),
					));
				if(empty($arr_language)){return false;}
				foreach($arr_language as $keys => $value){
					$arr_tmp[$value['key']] = isset($value['content'][$key])?$value['content'][$key]:'';
				}

				$_SESSION['arr_lang_'.$key] = $arr_tmp;
			}
		}else{
			$arr_tmp = $_SESSION['arr_lang_'.$key];
		}
		return $arr_tmp;
	}


	//Change language in all Setup file
	public function change_language(){
		$arr_settings = Cache::read(get_class($this).'_change_language_'.DEFAULT_LANG);
		if(!$arr_settings){
			$arr_language = $this->get_language();
			$name_module = strtoupper($this->arr_settings['module_name']);
			$key_lang =  KEY_LANG.$name_module;
			if(isset($arr_language[$key_lang]) && $arr_language[$key_lang]!='')
				$this->arr_settings['module_label'] = $arr_language[$key_lang];
			//dich ngon ngu cho array field
			foreach($this->arr_settings['field'] as $keys => $value){
				foreach($value as $k => $v){
					if(isset($v['name'])){
						$key_name = $key_lang.'_'.strtoupper($k);
						if(isset($arr_language[$key_name]) && $arr_language[$key_name]!='')
							$this->arr_settings['field'][$keys][$k]['name'] = $arr_language[$key_name];
					}
				}
			}
			//dich ngon ngu cho array relationship
			$rel = array();
			if(isset($this->arr_settings['relationship']))
				$rel = $this->arr_settings['relationship'];

			$key_lang.='_REL';
			if(!empty($rel))
			foreach($rel as $k => $v){
				$key_rel = $key_lang.'_'.strtoupper($k);
				if(isset($arr_language[$key_rel]) && $arr_language[$key_rel] != ''){
					$rel[$k]['name'] = $arr_language[$key_rel];
				}
				if(isset($rel[$k]['block']))
				foreach($rel[$k]['block'] as $k_block => $v_block){
					$key_block = $key_lang.'_'.strtoupper($k).'_'.strtoupper($k_block);
					if(isset($arr_language[$key_block]) && $arr_language[$key_block] != ''){
						$rel[$k]['block'][$k_block]['title'] = $arr_language[$key_block];
					}
					if(isset($v_block['field']))
					foreach($v_block['field'] as $k_field => $v_field){
						if(isset($v_field['name'])){
							$key_name = $key_block.'_'.strtoupper($k_field);
							if(isset($arr_language[$key_name]) && $arr_language[$key_name]!='')
								$rel[$k]['block'][$k_block]['field'][$k_field]['name'] = $arr_language[$key_name];
						}
					}
				}
			}
			$this->arr_settings['relationship'] = $rel;
			Cache::write(get_class($this).'_change_language_'.DEFAULT_LANG,$this->arr_settings);
		} else
			$this->arr_settings = $arr_settings;

	}


	//Bật khóa cho các field trong $this->arr_settings. $mod: gồm in(within), hoặc out(without)
	public function set_lock($arr_lock=array(),$mod='in'){
		foreach($this->arr_settings['field'] as $keys =>$arr_field){
			foreach($arr_field as $k =>$arr_v){
				if((count($arr_lock)>0 && in_array($k,$arr_lock) && $mod=='in') || (count($arr_lock)>0 && !in_array($k,$arr_lock) && $mod=='out') || count($arr_lock)==0){
						$this->arr_settings['field'][$keys][$k]['lock'] = '1';
				}
			}
		}
		return $this->arr_settings;
	}


	//Khóa edit trong arr_setting
	public function set_lock_option($tab='line_entry',$option='products',$action=array()){
		if(empty($action))
			foreach($this->arr_settings['relationship'][$tab]['block'][$option]['field'] as $keys =>$arr_field){
				unset($this->arr_settings['relationship'][$tab]['block'][$option]['field'][$keys]['edit']);
				unset($this->arr_settings['relationship'][$tab]['block'][$option]['add']);
				unset($this->arr_settings['relationship'][$tab]['block'][$option]['delete']);
			}
		else
			foreach($this->arr_settings['relationship'][$tab]['block'][$option]['field'] as $keys =>$arr_field){
				if(isset($action['add']))
					unset($this->arr_settings['relationship'][$tab]['block'][$option]['add']);
				if(isset($action['edit']))
				unset($this->arr_settings['relationship'][$tab]['block'][$option]['field'][$keys]['edit']);
				if(isset($action['delete']))
					unset($this->arr_settings['relationship'][$tab]['block'][$option]['delete']);
			}
	}

	//Đếm số lượng field trong array setting, gán giá trị default, xuất ra tổng số field
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


	//Từ array setting chung, tạo ra 1 array có chứa key là $keyfind
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

	//xuất ra array các field có các key được chỉ định $arr_keyfind
	public function arr_field_multi_key($arr_keyfind=array()){
		$this->arr_temp = array();
		foreach($this->arr_settings['field'] as $keys =>$arr_field){
			foreach($arr_field as $k =>$arr_v){
				foreach($arr_keyfind as $kss){
					if(!in_array($k,$this->arr_key_nosave) && isset($arr_v[$kss]) )
						$this->arr_temp[$kss][$k] = $arr_v[$kss];
				}
			}

		}
		return $this->arr_temp;
	}


	//lấy danh sách tất cả field trong tất cả subtab mục relationship
	public function arr_field_rel($fields=''){
		$this->arr_temp = $arr_temp = array();
		if(isset($this->arr_settings['relationship'])&&$this->arr_settings['relationship']!='')
			foreach($this->arr_settings['relationship'] as $keys =>$arr_field){
				foreach($arr_field as $ks =>$arr_s){
					if($ks=='block')
					foreach($arr_s as $kb =>$arr_b){
						if($fields=='' || ($fields!='' && $fields == $kb))
						$arr_temp[$kb] = $arr_b;
					}
				}
			}
		if(count($arr_temp)>0){
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
		}else return array();
	}


	//List ra các field cho phép hiện trong ListView
	public function list_view_field(){
		$this->arr_temp = $newdata = array();
		foreach($this->arr_settings['field'] as $keys =>$arr_field){
			foreach($arr_field as $k =>$arr_v){
				if(isset($this->arr_settings['field'][$keys][$k]['listview'])){
					if(!isset($arr_v['listview']['order']))
						$arr_v['listview']['order'] = 0;
					$sort = (int)$arr_v['listview']['order'];
					$this->arr_temp[$sort][$k] = $arr_v['listview'];
				}
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


	// List ra các field của module. array('field'=> giá trị default của field)
	public function main_field($field='field'){
		foreach($this->arr_settings[$field] as $keys =>$arr_field){
			foreach($arr_field as $k =>$arr_v){
				if($k=='setup')
					$arr_temp['setup_'.$keys] = $arr_v;
				else
					$arr_temp[$k] = $arr_v;
			}
		}
		return $arr_temp;
	}


	// Danh sách các field không cần save, dựa vào type
	public function arr_nonsave(){
		$arr_nonsave = array();
		$temp = $this->arr_field_key('type');
		foreach($temp as $sk => $sv){ //type none save
			if(in_array($sk,$this->arr_type_nosave)){
				$arr_nonsave[$sk] = $sk;
			}
		}
		return $arr_nonsave;
	}

	// Get fiel autocomplete in setup
	public function arr_autocomplete(){
		$temp = $this->arr_field_key('type');
		$autocomplete_key =array();
		foreach($temp as $sk => $sv){ //type autocomplete
			if($sv=='autocomplete'){
				if(isset($autocomplete_key[$sk]))
					$autocomplete_key[$sk] = $sk;
			}
		}
		return $autocomplete_key;
	}


	// lấy array các field name không lưu
	public function arr_rel_set($field='field'){
		$arr_temp = array();
		foreach($this->arr_settings[$field] as $keys =>$arr_field){
			foreach($arr_field as $k =>$arr_v){
				if(isset($arr_v['type']) && $arr_v['type']=='relationship' && (isset($arr_v['lock']) && $arr_v['lock']=='1' || isset($arr_v['not_custom']) && $arr_v['not_custom']=='1'))
					$arr_temp[] = $k;
			}
		}
		return $arr_temp;
	}






	/*=== OPTION CỦA MODULE (các giá trị dạng array của module) ========*/

	//array('module_id'=>'','option_name'=>'',)
	public function get_optmodule($arr_input=array()){ //nhận toàn bộ array option
		if(isset($arr_input) && is_array($arr_input) && isset($arr_input['module_id']) && isset($arr_input['option_name']) && $arr_input['module_id']!='' && $arr_input['option_name']!=''){
			$arr_tmp = $this->select_one(array('_id' => new MongoId($arr_input['module_id'])),array($arr_input['option_name']));
			if(isset($arr_tmp[$arr_input['option_name']])){
				//lọc các giá trị đã xóa
				$newarr = array();
				foreach($arr_tmp[$arr_input['option_name']] as $kys=>$values){
					$values['_id']=$kys;
					if(!$values['deleted'])
						$newarr[] = $values;

				}
				return $newarr;
			}else
				return array();
		}else
			return array();
	}


	// nhận số thứ tự kế tiếp
	public function get_auto_code($fields){
		$autocode = $this->max_field($fields);
                if(IS_NEW_CODE){
			$class_name = get_class($this);
			if(in_array($class_name, array('Salesorder','Salesinvoice'))){
				$y = date('y');
				$m = str_pad(date('m'), 2, "0", STR_PAD_LEFT);
				$prefix = "$y-$m-";
				if($class_name == 'Salesinvoice'){
					$autocode = $this->max_field($fields,array('code' => new MongoRegex('/-'.$y.'-/'),));
					$prefix = "$m-$y-";
				}
				if(isset($autocode[$fields])){
					$prefix = "$y-$m-";
					if($class_name == 'Salesinvoice')
						$prefix = "$m-$y-";
					if(strpos($autocode[$fields], $prefix)!==false){
						$code = str_replace($prefix, '', $autocode[$fields]);
						$autocode = 1+(int)$code;
					} else {
						$autocode = 1;
					}
					$autocode = $prefix.str_pad($autocode, 4, "0", STR_PAD_LEFT);
				} else {
					$autocode = $prefix.str_pad(1, 4, "0", STR_PAD_LEFT);
				}
				return $autocode;
			}
		}
		if(isset($autocode[$fields])){
			$autocode = 1+(int)$autocode[$fields];
		}
		else
			$autocode = 1;
		return $autocode;
	}
	public function get_product_minimum(){
		$sell_price = 50;
		require_once APP.'Model'.DS.'Stuffs.php';
		$StuffsModel = new Stuffs($this->db);
    	$product = $StuffsModel->select_one(array('value'=>"Minimun Order Adjustment"),array('product_id'));
		require_once APP.'Model'.DS.'Product.php';
		$ProductModel = new Product($this->db);
		$product = $ProductModel->select_one(array('_id'=> new MongoId($product['product_id'])),array('sell_price'));
		if(isset($product['sell_price']))
			$sell_price = (float)$product['sell_price'];
    	return array('minimum'=>$sell_price,'product_id'=>$product['_id']);
	}
	public function get_minimum_order($company_id,$product_id,$minimum,&$arr_companies){
		if(is_object($company_id)){
			require_once APP.'Model'.DS.'Company.php';
			$CompanyModel = new Company($this->db);
			$pricing = $CompanyModel->select_one(array('_id'=>new MongoId($company_id)),array('pricing'));
			if(isset($pricing['pricing'])&&!empty($pricing['pricing'])){
				foreach($pricing['pricing'] as $price){
					if(isset($price['deleted'])&&$price['deleted']) continue;
					if((string)$price['product_id']!=(string)$product_id) continue;
					if(!isset($price['price_break']) || empty($price['price_break'])) continue;
					$price_break = reset($price['price_break']);
					$arr_companies[(string)$company_id] = (float)$price_break['unit_price'];
					return (float)$price_break['unit_price'];
				}
			}
		}
    	return $minimum;
	}
	// Find value of field(name) by _id
	public function find_name($ids,$fieldname='name'){
		if(strlen((string)$ids)!=24)
			return '';
		if(!is_object($ids))
			$ids = new MongoId($ids);
		$datalist = array($fieldname);
		if(isset($this->arr_settings['module_name']) && $this->arr_settings['module_name']=='Contact')
			$datalist = array('first_name','last_name');

		$arr = $this->collection->findOne(array('_id'=>$ids,'deleted'=>false),$datalist);

		if(isset($this->arr_settings['module_name']) && $this->arr_settings['module_name']=='Contact')
			return $arr['first_name'].' '.$arr['last_name'];
		else if(isset($arr[$fieldname]))
			return $arr[$fieldname];
		else
			return '';
	}

	//Get minimum order


	//kiem tra xem co thuoc loai relationship va khong cho chinh sua ko
	public function check_field_link($panel,$field){
		$arr_set = $this->arr_settings;
		if(isset($arr_set['field'][$panel][$field]['cls']) && ((isset($arr_set['field'][$panel][$field]['not_custom']) && $arr_set['field'][$panel][$field]['not_custom']=='1') || (isset($arr_set['field'][$panel][$field]['lock']) && $arr_set['field'][$panel][$field]['lock']=='1')))
			return true;
		else
			return false;
	}


	//nếu là field loại relationship mà có
	public function set_null_id($field=''){
		if($field!=''){
			$arr = array();
			foreach($this->arr_settings['field'] as $keys =>$arr_field){
				foreach($arr_field as $k =>$arr_v){
					if($k==$field && isset($this->arr_settings['field'][$keys][$k]['id']) && !($this->check_field_link($keys,$k))){
							$idkey = $this->arr_settings['field'][$keys][$k]['id'];
							$arr[$idkey]='';
							return $arr;
					}
				}
			}
		}else
			return array();
	}


	//lọc lấy giá trị dạng array, bỏ qua các giá trị đã xóa
	public function select_field_arr($ids,$opname=''){
		$newdata = array();
		if($ids!='' && $opname!=''){
			$query = $this->select_one(array('_id' =>new MongoId($ids)));
			if(isset($query[$opname])){
				foreach($query[$opname] as $key=>$arr){
					if(isset($arr['deleted']) && !($arr['deleted'])){
						$newdata[$key] = $arr;
						if(!isset($newdata[$key]['_id']) && isset($newdata[$key][$opname.'_id']))
							$newdata[$key]['_id'] = $newdata[$key][$opname.'_id'];
						else if(!isset($newdata[$key]['_id']))
							$newdata[$key]['_id'] = $key;
					}

				}
			}
		}

		return $newdata;
	}



	/*=== DÙNG CHO MẢNG ===============================================*/

	// Sort mảng theo giá trị key, hàm đơn giản
	public function aasort(&$array=array(), $key='',$order=1,$isResetKey = false) {
		$sorter=array();
		$ret=array();
		if(is_array($array) && count($array)>0){
			reset($array);
			foreach ($array as $ii => $va) {
				if(!isset($va[$key])) continue;
				$sorter[$ii]=$va[$key];
			}
		}
		if($order==1)
			asort($sorter);
		else
			arsort($sorter);
		if(!$isResetKey)
			foreach ($sorter as $ii => $va) {
				$ret[$ii]=$array[$ii];
			}
		else
			foreach ($sorter as $ii => $va) {
				$ret[]=$array[$ii];
			}
		$array=$ret;
		return $array;
	}

	// Sort mảng theo giá trị key, cho phép theo nhiều cách sort_flags
	public function msort($array, $key,$order=1,$sort_flags = SORT_REGULAR) {
		if (is_array($array) && count($array) > 0) {
			if (!empty($key)) {
				$mapping = array();
				foreach ($array as $k => $v) {
					$sort_key = '';
					if (!is_array($key)) {
						$sort_key = $v[$key];
					} else {
						// @TODO This should be fixed, now it will be sorted as string
						foreach ($key as $key_key) {
							$sort_key .= $v[$key_key];
						}
						$sort_flags = SORT_STRING;
					}
					$mapping[$k] = $sort_key;
				}
				if($order==1)
					asort($mapping, $sort_flags);
				else
					arsort($mapping, $sort_flags);
				$sorted = array();
				foreach ($mapping as $k => $v) {
					$sorted[] = $array[$k];
				}
				return $sorted;
			}
		}
		return $array;
	}


	//Array flatten
	public function array_flatten($array) {
	  if (!is_array($array)) {
		return FALSE;
	  }
	  $result = array();
	  foreach ($array as $key => $value) {
		if (is_array($value)) {
		  $result = array_merge((array)$result, (array)$this->array_flatten($value));
		}
		else {
		  $result[$key] = $value;
		}
	  }
	  return $result;
	}

	//user_id login
	public function user_id(){
		if(isset($_SESSION['arr_user']['contact_id']) && strlen($_SESSION['arr_user']['contact_id'])==24)
			return $_SESSION['arr_user']['contact_id'];
		else if($_SESSION['arr_user']['_id']){
			$temp = $_SESSION['arr_user']['_id'];
			$temp = (array)$temp;
			return $temp['$id'];
		}else
			return '';
	}

	//user_name login
	public function user_name(){
		if(isset($_SESSION['arr_user']['contact_name']) && $_SESSION['arr_user']['contact_name']!='')
			return $_SESSION['arr_user']['contact_name'];
		else if(isset($_SESSION['arr_user']['user_name']) && $_SESSION['arr_user']['user_name']!='')
			return $_SESSION['arr_user']['user_name'];
		else
			return '';
	}

	//Display currency
	public function format_currency($num, $afterComma = -1){
		if(is_string($num))
			$num = str_replace(',', '', $num);
		$num = (float)$num;
		if($afterComma == -1)
			$afterComma = $_SESSION['format_currency'];
		$num = round($num,$afterComma);
		return number_format($num,$afterComma);
	}

	//Undisplay currency
	public function unformat_currency($str){
		$str = explode(".",$str);
		$dec = str_replace(",","",$str[0]);
		$dec = (int)$dec;
		if(isset($str[1]))
			$per = (int)$str[1];
		else
			$per = 0;
		$per = $per/1000;
		return $dec+$per;
	}


	//Sau này phát triển cho chọn định dạng ngày
	public function format_date($dateobj, $time = false){
		$format_date = $_SESSION['format_date'];
		if($time)
			$format_date .= ' h:i:s';
		if(is_object($dateobj))
			return date($format_date,$dateobj->sec);
		else
			return date($format_date,$dateobj);
	}

	//Convert number to string: 3 => 003
	public function num_to_string($num=0,$format_num =3){
		$num = (int)$num;
		$newstr = ''.$num;
		if($num<99)
			$newstr = '0'.$newstr;
		if($num<9)
			$newstr = '0'.$newstr;
		return $newstr;

	}//

	//Rebuild collection
	public function rebuild_collection($arr_save){
		if( !isset($arr_save['_id'])){
			$this->collection->insert($arr_save, array('safe'=>true));
		} else {
			$where['_id']	= $arr_save['_id'];
			unset($arr_save['_id']);
			$this->collection->update($where, array('$set'=>$arr_save));
		}
	}


	//Back ref no form backup data to current data
	public function revert_so_inv($ids,$code){
		$this->collection->update(array('_id'=>$ids), array('$set' => array('code'=>$code)));
	}

	//Remove data
	public function remove($condition){
		try{
			$this->collection->remove($condition);
			return true;
		}catch(MongoCursorException $e){
			$this->arr_errors = array( $e->getCode(), $e->getMessage() );
			return false;
		}
	}

	public function get_cache_keys_diff($name, $prefix_name) {
    	$settings = Cache::settings();
		$list = array();
    	if(class_exists('Memcache') && $settings['engine'] == 'Memcache'){
    		$memcache = new Memcache;
		    $memcache->connect('127.0.0.1', 11211)
		       or die ("Could not connect to memcache server");
		    $allSlabs = $memcache->getExtendedStats('slabs');
		    $items = $memcache->getExtendedStats('items');
		    foreach($allSlabs as $server => $slabs) {
		        foreach($slabs AS $slabId => $slabMeta) {
		        	if (!is_int($slabId)) continue;
		            $cdump = $memcache->getExtendedStats('cachedump',(int)$slabId,  100000000);
		            foreach($cdump AS $keys => $arrVal) {
		                if (!is_array($arrVal)) continue;
		                foreach($arrVal AS $k => $v){
		                	if(strpos($k, $prefix_name) !== false
		                	   	&& ( empty($name) || strpos($k, $name) === false) )
		                	   	$k = str_replace('app_', '', $k);
		                		$list[] = $k;
		                }
		            }
		        }
		    }
    	} else {
    		$cache_dir = APP.'tmp'.DS.'cache'.DS;
            $old_cache = glob($cache_dir.'cake_'.$prefix_name.'*');
            foreach($old_cache as $cache){
            	$cache = str_replace($cache_dir.'cake_', '', $cache);
                if($cache == $name) continue;
                $list[] = $cache;
            }
    	}
	    return $list;
	}

	public function deleteAll(){
		$this->collection->update(array('deleted'=>false), array('$set' => array('deleted'=>true)),array('multiple' => true));
	}

	public function identity(&$arr)
	{
		/*if ( IS_LOCAL && !isset($arr['identity']) ) {
			$arr_contact = $_SESSION['arr_user'];
			if(  isset($arr_contact['company_id']) ) {
				$arr['identity'] = $arr_contact['company_id'];
			}
		}*/
		return $arr;
	}

}