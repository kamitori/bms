<?php
	$arr_setup = $arr_settings['relationship'][$sub_tab]['block'];
	echo $this->element('box',array('key'=>'other_pricing','arr_val'=>$arr_setup['other_pricing']));
	echo $this->element('box',array('key'=>'other_comment','arr_val'=>$arr_setup['other_comment']));
?>
<div class="clear_percent_11 float_left no_right" style="width: 53%; margin-left:1%">
	<?php echo $this->element('communications'); ?>
</div>

<p class="clear"></p>
<script type="text/javascript">
$(function(){
	$("#other_comment").change(function(){
		var id_mongo = $("#mongo_id").val();
		var name = $(this).attr('name');
		var value = $(this).val();
		save_field(name,value,id_mongo);
	});
	$("input","#block_full_other_pricing").change(function(){
		var id_mongo = $("#mongo_id").val();
		var name = $(this).attr('name');
		var value = $(this).val();
		save_field(name,value,id_mongo);
	});
})

</script>