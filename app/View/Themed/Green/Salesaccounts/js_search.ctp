<?php echo $this->element('js_search');?>
<script type="text/javascript">
$(function() {
	$(window).keypress(function(e) {
		if( e.keyCode == 13 ){
			mainjs_entry_search_ajax('salesaccounts');
		}
	});
});
// xử lý sau khi chọn company
function after_choose_companies(ids,names,keys){
	$("#SalesaccountName").val(names);
	$("#SalesaccountId").val(ids);
	$(".k-window").fadeOut();
}

function after_choose_contacts(ids,names,keys){
	if(keys=='assign'){
		$("#assign_id").val(ids);
		$("#assign").val(names);
	}
	if(keys=='update_price_by'){
		$("#otherpricing_update_price_by_id").val(ids);
		$("#otherpricing_update_price_by").val(names);
	}
	$(".k-window").fadeOut();
}

</script>