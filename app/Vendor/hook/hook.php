<?php
//Class hook là class được sử dụng để móc nối các phần kèm theo 1 cách tự động
//Trong hệ thống này, class này sẽ đọc file hoặc hàm dựa theo tên hook

class hook{
	var $hook_list = array();
	var $arr_data = array();
	protected function loadClass($classname){
		App::import('Vendor', "hook/$classname/$classname");
		if(class_exists($classname)){
			$object = new $classname($this->arr_data);
		}
	}

	public function hook($arr_data = array()){
		if(isset($arr_data['hook_list'])){
			$this->arr_data = $arr_data;
			$this->scan_hook();
			return true;
		}
		$this->arr_data = $arr_data;
		if(isset($arr_data['event'])){
			$this->loadClass($arr_data['event']);
		}else{
			$this->scan_hook();
			//loop and process
			if(!empty($this->hook_list)){
				foreach($this->hook_list as $events){
					$this->loadClass($events);
				}
			}

		}
		if($this->arr_data == $arr_data)
			$this->sum_process = false;
		else
			$this->sum_process = true;
	}
	public function scan_hook(){
		$hook_list = scandir(getcwd().'/../Vendor/hook/');
			//get hook_list
		foreach($hook_list as $keys=>$values){
			if(strpos($values,".")!==false)
				continue;
			$this->hook_list[] = $values;
		}
	}

}