<?php echo $this->element('js_search');?>
<script type="text/javascript">
$(function(){


});

// xử lý sau khi chọn company
function after_choose_companies(ids,names,keys){
	var m,changes;
	var arr = new Array();
	arr[0] = keys;
	arr[1] = 'company';
	arr[2] = names;
	arr[3] = ids;

	changes = keys.split("_");

	if(arr[0]=='company_name')
	{
		$("#"+arr[1]+"_name").val(names);
		$("#company_name").html(names);
		$("#"+arr[1]+'_id').val(ids);
		$(".k-window").fadeOut();


	}


}

function after_choose_contacts(ids,names,keys){
	if(keys=='our_csr'){
		$("#our_csr_id").val(ids);
		$("#our_csr").val(names);
	}
	if(keys=='our_rep'){
		$("#our_rep_id").val(ids);
		$("#our_rep").val(names);
	}
	$(".k-window").fadeOut();
}
function after_choose_salesaccounts(type,id,name,key){
	$("#salesaccount_name").val(name);
	$("#salesaccount_id").val(id);
	$(".k-window").fadeOut();
}


</script>