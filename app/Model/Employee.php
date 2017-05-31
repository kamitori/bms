<?php
require_once APP.'Model'.DS.'AppModel.php';
class Employee extends AppModel {
	public $arr_settings = array(
								'module_name' => 'Employee'
							);
	public $arr_temp = array();
	public $temp = '';
	public $arr_key_nosave = array('setup','none','none2','none3','none4','none5','none6','none7','field03','field06','mongo_id');
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

	public function add($field='',$values=''){
		$this->arr_temp = array();
		if($this->arrfield()){

			if( $field != '' )
			$this->arr_temp[$field] = $values;

			//BEGIN custom
			$this->arr_temp['code'] = $this->get_auto_code('code');
			$this->arr_temp['addresses_default_key'] = 0;
			$this->arr_temp['employee_name'] = $this->arr_temp['employee_id'] = '';
			$this->arr_temp['addresses'][0]['country'] = 'CA';
			$this->arr_temp['addresses'][0]['deleted'] = false;
			$this->arr_temp['employee_image'] = '';

			// BaoNam: arr_default_before_save dùng để gán trước các giá trị mặc định từ controller, ví dụ trong các tạo từ các options
			// mục đích là dùng chung 1 hàm add thôi để sau này chỉ sửa 1 chỗ
			$this->arr_temp = array_merge($this->arr_temp, $this->arr_default_before_save);

			$this->save($this->arr_temp);
			$this->arr_temp = array();
			return $this->mongo_id_after_save.'||'.$values; //xuất ra chuỗi để js hiển thị ra html
		}else
			return '';
	}
	// Thêm record, gán các giá trị mặc định và gán field $field = $values,
	public $arr_default_before_save = array();


}