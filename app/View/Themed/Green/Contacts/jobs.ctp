<?php
	if(isset($arr_settings['relationship'][$sub_tab]['block']))
	foreach($arr_settings['relationship'][$sub_tab]['block'] as $key => $arr_val){
		echo $this->element('box',array('key'=>$key,'arr_val'=>$arr_val));
	}
?>
<p class="clear"></p>

<script type="text/javascript">
$(function(){
	$('#bt_add_jobs').click(function(){
		var ids = $("#mongo_id").val();
		$.ajax({
			url:"<?php echo URL;?>/contacts/jobs_add/" + ids,
			timeout: 15000,
			success: function(html){
				 location.replace(html);
			}
		});
	});
	$("#markup_rate,#rate_per_hour").change(function(){
		var id_mongo = $("#mongo_id").val();
		var name = $(this).attr('name'); // name = recruiment
		var value = $(this).val();
		save_field(name,value,id_mongo);
	});

})
</script>