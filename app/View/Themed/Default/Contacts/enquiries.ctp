<?php
	if(isset($arr_settings['relationship'][$sub_tab]['block']))
	foreach($arr_settings['relationship'][$sub_tab]['block'] as $key => $arr_val){
		echo $this->element('box',array('key'=>$key,'arr_val'=>$arr_val));
	}
?>
<p class="clear"></p>
<script type="text/javascript">
$(function(){
	$('#bt_add_enquiries').click(function(){
		var ids = $("#mongo_id").val();
		$.ajax({
			url:"<?php echo URL;?>/contacts/enquiries_add/" + ids,
			timeout: 15000,
			success: function(html){
				location.replace(html);
				reload_subtab('enquiries');
			}
		});
	})

})
</script>