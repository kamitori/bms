<?php 
	if(isset($arr_field['default']) && (int)$arr_field['default']<0)
		$cssn = 'color:red!important;';
	else
		$cssn = '';
?>

<input name="<?php echo $keys;?>" id="<?php echo $keys;?>" class="input_1 float_left jtprice jt_<?php echo $keys;?>" type="text" value="<?php if(isset($arr_field['default'])) echo $arr_field['default'];?>" <?php if(isset($arr_field['element_input'])) echo $arr_field['element_input'];?> style=" border-bottom:1px #ddd; background:#faf6f6; padding-right:0; font-weight:bold;<?php echo $cssn;?> <?php if(isset($arr_field['css'])) echo $arr_field['css'];?>" />
   
<?php if(isset($arr_field['moreinline'])) echo $arr_field['moreinline'];?>

<script>
	$(document).ready(function() {
		$("#<?php echo $keys;?>").kendoNumericTextBox({
			format: "<?php if(isset($arr_field['format'])) echo $arr_field['format'];?>",
			<?php if(isset($arr_field['format']) && $arr_field['format']=='p0'){?>
				min: 0
			<?php }else{?>
				decimals: 3
			<?php }?>
		});
	});
</script>