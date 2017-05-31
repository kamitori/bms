<?php
	//echo $after_field;
	//echo $iditem;
	//echo $panel;
	//pr($arr_settings['field'][$panel][$after_field]);
	$arr_s = $arr_settings['field'][$panel][$after_field];
?>
<div class="jt_box_field" style=" <?php if(isset($arr_s['width']))echo 'width:'.$arr_s['width'].';';?>">
	<?php echo $this->element('basic_field/'.$arr_s['type'],array('keys'=>$after_field,'arr_field'=>$arr_s)); ?>
</div>