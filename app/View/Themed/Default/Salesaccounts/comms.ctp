<?php
    if(isset($arr_settings['relationship'][$sub_tab]['block']))
    foreach($arr_settings['relationship'][$sub_tab]['block'] as $key => $arr_val){
        echo $this->element('box',array('key'=>$key,'arr_val'=>$arr_val));
    }
?>

<div class="clear_percent_11 float_left no_right" style="width: 49%;margin-left:1%">
	<?php echo $this->element('communications'); ?>
</div>

<p class="clear"></p>