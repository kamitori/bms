<?php 
	if(isset($arr_field['default']) && (int)$arr_field['default']<0)
		$cssn = 'color:red;';
	else
		$cssn = '';
	
	if(isset($arr_field['default']) && trim($arr_field['default'])!=''){
		$per = round((float)$arr_field['default']*100,2);
		$per = $per.' %';
	}else
		$per = '';
?>

<input type="text" class="jt_box_input rel_jtprice <?php if(isset($search_class)) echo $search_class;?>" <?php if(isset($search_flat)) echo $search_flat;?> id="rel_<?php echo $keys;?>" value="<?php echo $per;?>" style=" <?php echo $cssn;?> <?php if(isset($arr_field['css'])) echo $arr_field['css'];?>" />

<input name="<?php echo $keys;?>" id="<?php echo $keys;?>" class="jt_box_input jtprice" type="text" value="<?php if(isset($arr_field['default'])) echo $arr_field['default'];?>" <?php if(isset($arr_field['element_input'])) echo $arr_field['element_input'];?> style=" display:none;<?php echo $cssn;?> <?php if(isset($arr_field['css'])) echo $arr_field['css'];?>" />
   
<?php if(isset($arr_field['moreinline'])) echo $arr_field['moreinline'];?>