<?php
	//echo $after_field;
	//echo $iditem;
	//echo $panel;
	//pr($arr_settings['field'][$panel][$after_field]);
	$arr_s = $arr_settings['field'][$panel][$before_field];
	
?>
<div style=" float:left;;margin-left:1%;border-right:1px solid #ddd;display:block; <?php if(isset($arr_s['width']))echo 'width:'.$arr_s['width'].';';?>">
    <?php if(isset($arr_s['name'])) echo $arr_s['name'];?><?php echo $this->element('basic_field/'.$arr_s['type'],array('keys'=>$before_field,'arr_field'=>$arr_s)); ?>
</div>