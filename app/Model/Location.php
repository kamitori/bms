<?php
require_once APP.'Model'.DS.'AppModel.php';
class Location extends AppModel {
	public $arr_settings = array(
								'module_name' => 'Location'
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
	
	//count so
	public function on_so($id_product=''){
        $sum = 0;
        $this->selectModel('Salesorder');
        $data = $this->Salesorder->select_all(array(
            'arr_where' => array('products.products_id' => new MongoId($id_product), )
            ));

        $data = iterator_to_array($data);
        $product = array();
        foreach($data as $key => $value){
            foreach($value['products'] as $vv){
                if(!$vv[deleted]){
                    $product[] = $vv['products_id'];
                    $product[] = $vv['quantity'];
                    if(isset($vv['quantity']))
                        $sum += $vv['quantity'];
                }
            }
        }
       //die;
        return $sum;

    }
    //count po
    public function on_po($id_product='') {
        $sum = 0;
        $this->selectModel('Purchaseorder');
        $data = $this->Purchaseorder->select_all(array(    
            'arr_where' => array('deleted' => false, 'purchase_orders_status' => 'On order')
        ));

        $data = iterator_to_array($data);
        $product = array();
        foreach ($data as $key => $value  ) {
           //pr($data);
            if(isset($value['products'])  && is_array($value['products'])  && count($value['products']) > 0){
                foreach ($value['products'] as $vv) {
                    if(isset($vv['deleted']) && $vv['deleted'] == false){
                        $product[] = $vv['products_id'];
                        $product[] = $vv['quantity'];
                        if(isset($vv['quantity']))
                        $sum += (Integer)$vv['quantity'];
                    }
                }
            }
         }
         return $sum;
    }

}