<?php echo $this->element('js_search');?>
<script type="text/javascript">
$(function(){


});

// xử lý sau khi chọn company
function after_choose_companies(ids,names,keys){
	$(".k-window").fadeOut();
	if(keys=='company_name'){
		$('#company_name').val(names);
		//$('#company_id').val(ids);
	}
}

function after_choose_contacts(ids,names,keys){
	$(".k-window").fadeOut();
	if(keys=='contact_name'){
		$('#contact_name').val(names);
		//$('#contact_id').val(ids);
	}else if(keys=='our_rep'){
		$('#our_rep').val(names);
		//$('#our_rep_id').val(ids);
	}else if(keys=='our_csr'){
		$('#our_csr').val(names);
		//$('#our_csr_id').val(ids);
	}
}

function after_choose_jobs(ids,names,keys){
	$(".k-window").fadeOut();
	if(keys=='job_name'){
		var jno = $('#window_popup_job_no_'+ids).val();
		$('#job_name').val(names);
		//$('#job_id').val(ids);
		$('#job_number').val(jno);
	}
}

function after_choose_salesorders(ids,names,keys){
	$(".k-window").fadeOut();
	if(keys=='salesorder_name'){
		var sno = $('#window_popup_salesorder_no_'+ids).val();
		$('#salesorder_name').val(names);
		//$('#salesorder_id').val(ids);
		$('#salesorder_number').val(sno);
	}
}

</script>