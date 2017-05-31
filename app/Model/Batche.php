<?php
require_once APP.'Model'.DS.'AppModel.php';
class Batche extends AppModel {
	public $arr_settings = array(
								'module_name' => 'Batche'
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
	public function add($field='',$values='',$arr_more = array()){
		$this->arr_temp = array();
		if($this->arrfield()){
			
			//BEGIN special
			$nextcode = $this->get_auto_code('code');
			if(isset($nextcode))
				$this->arr_temp['code'] = $nextcode;
			else
				$this->arr_temp['code'] = 1;
				
			$this->arr_temp[$field] = $values;
			
			//lấy company systerm
			if(isset($arr_more) && is_array($arr_more) && count($arr_more)>0){
				foreach($arr_more as $kk=>$vv){
					$this->arr_temp[$kk] = $vv;
				}
			}
			//END special
			
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


	//lay du lieu cho column notes
	/*public function get_data_general_notes($ids=''){
		$supplier = $same_category =  $comdata = $images = $pricing_method = array();
		$temps = ''; 
		$query = array();
		if($ids!=''){
			$query = $this->select_one(array('_id' => new MongoId($ids)));
			$arr_thisdata = $query;
			if(isset($arr_thisdata['category']) && $arr_thisdata['category']!='')
				$arr_where = array('category'=>$arr_thisdata['category']);
			else
				$arr_where = array('category'=>'');
			$query = $this->select_all(array(
				'arr_where' => $arr_where,
				'arr_order' => array('_id' => -1)
			));

			$same_category = $query;
				if(isset($arr_d['type']))
					$supplier[$kd]['type'] = $arr_d['type'];

				if(isset($arr_d['notes_date']))
					$supplier[$kd]['notes_date'] = (float)$arr_d['notes_date'];
				else
					$supplier[$kd]['notes_date'] = 0;
				if(isset($arr_d['from']))
					$supplier[$kd]['from'] = $arr_d['from'];
				else
					$supplier[$kd]['from'] = 0;
				if(isset($arr_d['details']))
					 $supplier[$kd]['details'] = $arr_d['details'];
				else
					$supplier[$kd]['details'] = 0;
			}
		}
		$subdatas['type'] 			= $type;
		$subdatas['notes_date'] = $notes_date;
		$subdatas['from'] = $from;
		$subdatas['cu']
		return $subdatas;
	}
*/
}