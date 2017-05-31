<?php
require_once APP.'Model'.DS.'AppModel.php';
class Purchaseorder extends AppModel {
	public $arr_settings = array(
		'module_name' => 'Purchaseorder'
	);
	public $arr_temp = array();
	public $temp = '';
	public $arr_key_nosave = array('setup','none','none2','mongo_id');
	public $arr_type_nosave = array('autocomplete','id');
	public $company='';

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
	public $arr_default_before_save = array();
	public function add($field='',$values=''){
		$this->arr_temp = array();
		if($this->arrfield()){

			if( $field != '' )
				$this->arr_temp[$field] = $values;

			/**BEGIN custom*/
			$this->arr_temp['code'] = $this->get_auto_code('code');
			//set our_rep default
			$this->arr_temp['our_rep_id'] = $this->user_id();
			$this->arr_temp['our_rep'] = $this->user_name();
			//set shipping contact default
			$this->arr_temp['ship_to_contact_id'] = $this->user_id();
			$this->arr_temp['ship_to_contact_name'] = $this->user_name();
			//set shipping company default
			require_once APP . 'Controller' . DS  . 'AppController.php';
			$app = new AppController;
			$app->selectModel('Company');
			$collection_company=$app->Company->select_one(array('system'=>true));
			$this->arr_temp['ship_to_company_id']=$collection_company['_id'];
			$this->arr_temp['ship_to_company_name'] = $collection_company['name'];
			//set shipping addresses default
			$arr_temp=array();
			foreach ($collection_company['addresses'][$collection_company['addresses_default_key']] as $key=>$value){
				if($key=='deleted')
					$arr_temp[$key] = $value;
				else
					$arr_temp['shipping_'.$key] = $value;
			}
			$object_child[0] =(object)$arr_temp;
			$object_parent=(object)$object_child;
			$this->arr_temp['shipping_address']=$object_parent;
			$this->arr_temp['shipping_address'] = array('deleted'=>false);
			//set shipping date default
			$this->arr_temp['purchord_date'] =  new MongoDate(time());
			$this->arr_temp['required_date'] =  new MongoDate(3*24*60*60 + (int)time()); // đổi lại chỉ 3 ngày thôi
			$this->arr_temp['delivery_date'] =  '';
			$this->arr_temp['products'] =  array();
			/**END custom */

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
			if(preg_match("/_date$/",$field))
				$arr_temp[$field] = new MongoDate($values);
			else
				$arr_temp[$field] = $values;

			if($this->save($arr_temp))
				return $ids.'||'.$arr_temp[$field];
	}
}