
<?php 
		foreach($arr_settings['relationship'][$sub_tab]['block'] as $key => $arr_val){
			
			echo $this->element('box',array('key'=>$key,'arr_val'=>$arr_val));
			
		}
?>