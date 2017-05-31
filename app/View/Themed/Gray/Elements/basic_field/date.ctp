<input name="<?php echo $keys;?>" id="<?php echo $keys;?>" class="input_1 float_left <?php if(isset($arr_field['more_in_class'])) echo $arr_field['more_in_class'];?> <?php if(isset($search_class)) echo $search_class;?>" <?php if(isset($search_flat)) echo $search_flat;?> type="text" value="<?php if(isset($arr_field['default']) && strtotime($arr_field['default'])>0) echo $this->Common->format_date(strtotime($arr_field['default'])); else if(isset($search_flat)) echo ''; else if(isset($arr_field['default']) && $arr_field['default']!='') echo ''; else echo '';//$this->Common->format_date(); ?>" <?php if(isset($arr_field['element_input'])) echo $arr_field['element_input'];?> style=" <?php if(isset($arr_field['css'])) echo $arr_field['css'];?>" <?php if(isset($arr_field['lock']) && $arr_field['lock']=='0') echo ''; else{?>readonly="readonly"<?php }?> />

<?php if(isset($arr_field['moreinline'])) echo $arr_field['moreinline'];?>

<?php if(isset($arr_field['lock']) && $arr_field['lock']=='1') echo ''; else{?>
<script>
	$(function() {
		$( "#<?php echo $keys;?>" ).datepicker({dateFormat: 'dd M, yy', changeMonth: true, changeYear: true, yearRange: "c-70:c+3" });
		// $( this ).datepicker({ changeMonth: true, changeYear: true, yearRange: "c-70:c+3"});
	});
</script>
<?php }?>