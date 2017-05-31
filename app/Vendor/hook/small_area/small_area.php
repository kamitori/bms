<?php
App::import('Controller','AppController');
class small_area{
	public $area_limit = 1;
	public $up_price = 50;
	public $cake;
	public function small_area(&$arr_data){
		$this->arr_data = $arr_data;

		$this->cake = new AppController;
		$this->cake->selectModel('Hooksetting');
		if(isset($arr_data['setting'])){
			return $this->setting($arr_data);
		}

		$option_data = $this->cake->Hooksetting->select_one(array('name' => 'Small area'),array('options'));
		if(!empty($option_data['options'])){
			usort($option_data['options'],function($a, $b){
				return $a['area_limit'] > $b['area_limit'] ;
			});
			$option_data = $option_data['options'];
		} else
			$option_data = array();
		$this->option_data = $option_data;

		$this->process_data();
		//Tung: 08/02/2014. Gán ngược lại để thay đổi dữ liệu sau khi đã xử lý
		$arr_data = $this->arr_data;
	}


	public function process_data(){
		if(isset($this->arr_data['sell_price']) && $this->arr_data['sell_by']=='area'){
			$sell_price = $this->arr_data['sell_price'];
			/*if(!IS_LOCAL && isset($this->arr_data['products_id']) && is_object($this->arr_data['products_id'])){
				$this->cake->selectModel('Product');
				$product = $this->cake->Product->select_one(array('_id'=> new MongoId($this->arr_data['products_id'])),array('sell_price'));
				$this->arr_data['sell_price'] = (isset($product['sell_price']) ? (float)$product['sell_price'] : 0);
				foreach($this->option_data as $option_data){
					if($this->arr_data['area'] <= (float)$option_data['area_limit']){
						$this->arr_data['unit_price'] = $this->arr_data['sell_price'] += (float)$this->arr_data['sell_price']*$option_data['up_price']/100;
						return true;
					}
				}
			} else {*/
				foreach($this->option_data as $option_data){
					if($this->arr_data['area'] <= (float)$option_data['area_limit']){
						$this->arr_data['sell_price'] = $this->arr_data['unit_price'] += (float)$this->arr_data['unit_price']*$option_data['up_price']/100;
						return true;
					}
				}
			/*}*/
			$this->arr_data['sell_price'] = $sell_price;
		}
		return false;
	}
}