<?php
	if(isset($arr_settings['relationship'][$sub_tab]['block']))
	foreach($arr_settings['relationship'][$sub_tab]['block'] as $key => $arr_val){
		echo $this->element('box',array('key'=>$key,'arr_val'=>$arr_val));
	}
?>
<p class="clear"></p>

<input type="hidden" id="id_choicing_for_popup" value="" />
<script>
$(document).ready(function() {
	window_popup("contacts", "Specify Employee","choice_contact", "id_choicing_for_popup","?is_employee=1");

	$(".choice_contact").click(function(){
		var ids = $(this).attr("id");
		var key_click_open = ids;
		ids  = $("#"+key_click_open).attr("rel");
		var html = $('#'+key_click_open).html();
		ajax_note(" Change Code "+html);
		$("#id_choicing_for_popup").val(ids);
		$("#id_choicing_for_popup").click();
	});
	
	//tạo thêm 1 cost / item mới
	$("#bt_add_commission").click(function() {
		$("#id_choicing_for_popup").val('add');
		$("#id_choicing_for_popup").click();
	});
	
});
</script>