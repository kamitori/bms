<input name="<?php echo $keys;?>" id="<?php echo $keys;?>" class="jt_box_input <?php if(isset($arr_field['more_in_class'])) echo $arr_field['more_in_class'];?>" type="text" value="10/10/2011" <?php if(isset($arr_field['element_input'])) echo $arr_field['element_input'];?> style=" <?php if(isset($arr_field['css'])) echo $arr_field['css'];?>" />

<?php if(isset($arr_field['moreinline'])) echo $arr_field['moreinline'];?>
<script>
	$(function() {
		$("#<?php echo $keys;?>").kendoDatePicker();
	});
</script>