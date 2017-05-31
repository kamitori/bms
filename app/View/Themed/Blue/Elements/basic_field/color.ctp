
<?php
	$color = array();
	if(isset($arr_field['default'])){
		$color = $arr_field['default'];
	}
	if(!isset($arr_field['color_number']))
		$arr_field['color_number'] = 1;
	$random_id = md5(time());
?>
<?php for($i = 0; $i < $arr_field['color_number']; $i++){ ?>
<input type="text" name="<?php echo  $keys.'_'.$i?>" id="<?php echo  $keys.'_'.$i?>" class="<?php echo  $keys?> " value="<?php echo isset($color[$i]) ? $color[$i] : ''; ?>" <?php if(isset($arr_field['lock']) && $arr_field['lock']=='1'){ ?> disabled <?php } ?> />
<?php } ?>
<script type="text/javascript">
$(function(){
	$(".<?php echo  $keys?>").each(function(){
		$(this).kendoColorPicker({
			// buttons: false
		});
		var input = $(this);
		var colorPicker = $(this).data("kendoColorPicker");
		colorPicker.bind({
			change: function(e) {
				after_choose_color(input);
				// console.log(input);
			}
		});
	});
})
</script>
