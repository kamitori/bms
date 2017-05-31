<?php 
	$arr_val = $arr_settings['relationship'][$sub_tab]['block'];
	echo $this->element('box',array('key'=>'protype','arr_val'=>$arr_val['protype']));
	
	echo $this->element('box',array('key'=>'proitem','arr_val'=>$arr_val['proitem']));
            
?>