<?php echo $this->element('js_search');?>
<script type="text/javascript">
$(function(){
	//Link Sub Tab
});
// xử lý sau khi chọn company
function after_choose_companies(ids,names,keys){
	$(".k-window").fadeOut();
	if(keys=='company_name'){
		$('#company_name').val(names);
		//$('#company_id').val(ids);
	} else if( keys == 'prefer_customer' ) {
		$('#prefer_customer').val(names);
	}
}
</script>