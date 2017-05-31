<?php
require_once APP.'Model'.DS.'AppModel.php';
class Communication extends AppModel {
	public $arr_settings = array(
								'module_name' => 'Communication'
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
		$arr_temp = array();
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
	public $arr_default_before_save = array();
	public function add($field='',$values=''){
		$this->arr_temp = array();
		if($field=='comms_type')
			$this->arr_settings['field'] = $this->custom_entry_layout(strtolower($values));
		if($this->arrfield()){

			if( $field != '' )
				$this->arr_temp[$field] = $values;
			//BEGIN custom
			$this->arr_temp['code'] = $this->get_auto_code('code');
			$this->arr_temp['contact_from'] = $this->user_name();
			$this->arr_temp['comms_date'] = new MongoDate();
			//END custom

			$this->arr_temp = array_merge($this->arr_temp, $this->arr_default_before_save);
			$this->save($this->arr_temp);
			$this->arr_temp = array();
			return $this->mongo_id_after_save.'||'.$values; //xuất ra chuỗi để js hiển thị ra html
		}else
			return '';
	}

	public function save(array $arr = array()){
		if(isset($arr['comms_type']) && $arr['comms_type'] == 'Email' && !isset($arr['no_use_signature'])) {
			require_once APP.'Model'.DS.'Emailtemplate.php';
			$EmailtemplateModel = new Emailtemplate($this->db);
			$template = $EmailtemplateModel->select_one(array('name' => 'Signature'),array('template'));
			if(!isset($arr['content']))
				$arr['content'] = '';
			$arr['content'] .= isset($template['template']) ? $template['template'] : '';
		}
		return parent::save($arr);
	}



	//update field $field  = $values của record có _id là $ids
	public function update($ids='',$field='',$values=''){
			$arr_temp = $cal_price = array();
			$arr_temp['_id'] = $ids;
			$arr_temp[$field] = $values;

			if($this->save($arr_temp))
				return $ids.'||'.$arr_temp[$field];
	}

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

	//custom


	//update option của module. $arr_vl = Array(moduleids,moduleop,field,value,opwhere)
	public function update_value_option_of_module($arr_vl=array(),$add=0){
		if($arr_vl['moduleids']!=''){
			// khởi tạo biến
			$arr_temp = $arr_option = $cal_price = array();
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

					if($booleans)
						$arr_option[$kr][$arr_vl['field']] = $arr_vl['value'];
				}
			}//end if

			//gan gia tri cho option va luu record
			$arr_temp[$arr_vl['moduleop']] = $arr_option;
			if($this->save($arr_temp))
				return true;
			else
				return false;
		}
	}


	// list ra cac option image
	public function get_option($ids='',$opt='image'){
		if($ids!=''){
			$this->arr_temp = $this->select_one(array('_id' => new MongoId($ids)),array($opt));
			if(isset($this->arr_temp[$opt]))
				return (array)$this->arr_temp[$opt];
			else
				return array();
		}else
			return array();
	}


	// custom layout entry
	public function custom_entry_layout($type='letter'){
		if($type=='email'){
			return $this->arr_settings['field'];
		}else if(isset($this->arr_settings['custom']) && $this->arr_settings['custom']){
			$allfield = $this->main_field();
			if(isset($this->arr_settings['field_custom'][$type])){
				$panel = 'panel_1';
				$arr_field = array();
				foreach($this->arr_settings['field_custom'][$type] as $kss=>$vss){
					if($vss!='')
					{
						if($vss=='split'){
							$panel = $kss;
							$arr_field[$panel]['setup'] = $allfield['setup_'.$panel];
							if(isset($this->arr_settings['field_custom'][$type]['setup_'.$panel]))
							$arr_field[$panel]['setup'] = array_merge($arr_field[$panel]['setup'],$this->arr_settings['field_custom'][$type]['setup_'.$panel]);
						}
						if($kss=='setup_'.$panel)
							continue;
						else if(isset($allfield[$kss])){
							if(is_array($vss) && count($vss)>0)
								$arr_field[$panel][$kss] = array_merge($allfield[$kss],$vss);
							else
								$arr_field[$panel][$kss] = $allfield[$kss];
						}else if(is_array($vss) && count($vss)>0)
							$arr_field[$panel][$kss] = $vss;
					}
				}
				return $arr_field;
			}else
				return 'No have field_custom';
		}else
			return 'arr_settings[custom] is false';
	}


	public function tao_data_demo_comms($arr_company_id){
		$arr_company_id[] = '';
		$where = array();
		$where['company_id']['$nin'] = $arr_company_id;
		$arr_set = array('deleted'=>true);
		$this->collection->update($where, array( '$set' => $arr_set ), array('multiple' => true));
	}


}