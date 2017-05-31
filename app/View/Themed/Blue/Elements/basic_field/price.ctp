<?php
	if(isset($arr_field['default']) && (int)$arr_field['default']<0)
		$cssn = 'color:red;';
	else
		$cssn = '';

	if(isset($arr_field['default']) && trim($arr_field['default'])){
		if(is_string($arr_field['default']))
			$arr_field['default'] = str_replace(',', '', $arr_field['default']);
		$currient = (float)$arr_field['default'];
		$num = 2;
		if(isset($arr_field['noformat']))
			$num = 0;
		if(isset($arr_field['numformat']))
			$num = $arr_field['numformat'];
		$currient = $this->Common->format_currency($currient,$num);
		//$currient = '$'.$currient;
	}else
		$currient = '';
?>

<input type="text" name="<?php echo $keys;?>_cb" class="jt_box_input rel_jtprice <?php if(isset($arr_field['more_in_class'])) echo $arr_field['more_in_class'];?> <?php if(isset($search_class)) echo $search_class;?>" <?php if(isset($search_flat)) echo $search_flat;?> id="rel_<?php echo $keys;?>" value="<?php echo $currient;?>" style=" <?php echo $cssn;?> <?php if(isset($arr_field['css'])) echo $arr_field['css'];?>" onkeypress="return isPrice(event);" <?php if(isset($arr_field['element_input'])) echo $arr_field['element_input'];?> <?php if(isset($arr_field['lock']) && $arr_field['lock']=='1'){?>readonly="readonly"<?php }?> />

<input name="<?php echo $keys;?>" id="<?php echo $keys;?>" class="jt_box_input jtprice" type="text" value="<?php if(isset($arr_field['default'])) echo $arr_field['default'];?>" <?php if(isset($arr_field['element_input'])) echo $arr_field['element_input'];?> onkeypress="return isPrice(event);" style=" display:none;<?php echo $cssn;?> <?php if(isset($arr_field['css'])) echo $arr_field['css'];?>" <?php if(isset($arr_field['lock']) && $arr_field['lock']=='1'){?>readonly="readonly"<?php }?> />

<?php if(isset($arr_field['moreinline'])) echo $arr_field['moreinline'];?>