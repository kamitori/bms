<input name="<?php echo $keys;?>" id="<?php echo $keys;?>" class="jt_box_input jtprice jt_<?php echo $keys;?>" type="text" value="<?php if(isset($arr_field['default'])) echo $arr_field['default'];?>" <?php if(isset($arr_field['element_input'])) echo $arr_field['element_input'];?> style="border: none;margin-top: 5px;text-align: right; padding:0; margin:0; width:100%;border-radius:0;<?php if(isset($arr_field['css'])) echo $arr_field['css'];?>" />
   
<?php if(isset($arr_field['moreinline'])) echo $arr_field['moreinline'];?>

<script>
	$(document).ready(function() {
		$("#<?php echo $keys;?>").kendoNumericTextBox({
			format: "<?php if(isset($arr_field['format'])) echo $arr_field['format'];?>",
			<?php if(isset($arr_field['decimals'])) 
					echo 'decimals:'.$arr_field['decimals'];
				  else{ 
				  	echo 'decimals:3';
				  }
			?>
		});
	});
</script>