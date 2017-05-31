<?php
	if(isset($arr_field['default']))
		$thisval_key =  $arr_field['default'];
	else
		$thisval_key = '';
	if(isset($arr_options_custom[$keys][$thisval_key]))
		$thisval = $arr_options_custom[$keys][$thisval_key];
	else if(isset($arr_options[$keys][$thisval_key]))
		$thisval = $arr_options[$keys][$thisval_key];
	else
		$thisval = $thisval_key;
?>

<input class="input_select <?php if(isset($arr_field['more_in_class'])) echo $arr_field['more_in_class'];?> <?php if(isset($search_class2)) echo $search_class2;?>" <?php if(isset($search_flat)) echo $search_flat;?> name="<?php echo $keys;?>" id="<?php echo $keys;?>" style=" <?php if(isset($arr_field['css'])) echo $arr_field['css'];?>" type="text" value="<?php echo $thisval;?>" <?php if(isset($arr_field['element_input'])) echo $arr_field['element_input'];?> <?php if((isset($arr_field['not_custom']) && $arr_field['not_custom']=='1')|| (isset($arr_field['lock']) && $arr_field['lock']=='1')){?>readonly="readonly"<?php }?> />

<input <?php if(isset($search_flat)) echo $search_flat;?> name="<?php echo $keys;?>_id" id="<?php echo $keys;?>Id" type="hidden" value="<?php if(isset($arr_field['default_id'])) echo $arr_field['default_id']; elseif(isset($arr_field['default'])) echo $arr_field['default'];?>" />

<?php if(isset($arr_field['lock']) && $arr_field['lock']=='1')
		$str = '';
	  else if(isset($arr_options_custom[$keys]))
		$str = json_encode($arr_options_custom[$keys]);
	  else if(isset($arr_options[$keys]))
		$str = json_encode($arr_options[$keys]);
	  else
	  	$str = '';

?>
<script type="text/javascript">
	$(function () {
		$("#<?php echo $keys;?>").combobox(<?php echo $str;?>);
	});
</script>
<?php if(isset($arr_field['moreinline'])) echo $arr_field['moreinline'];?>

