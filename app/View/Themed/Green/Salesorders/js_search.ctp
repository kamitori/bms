<?php echo $this->element('js_search');?>
<script type="text/javascript">
$(function(){


});
function get_company_is_shipper()
{
    var para = '?is_shipper=1';
	var company_id = $("#shipper_id").val();
	var company_name = $("#shipper").val();
	if(company_id!='')
		para += '&company_id='+company_id;
	if(company_name!='')
		para += '&company_name='+company_name;
	return para;

}
// xử lý sau khi chọn company
function after_choose_companies(ids,names,keys){
	$(".k-window").fadeOut();
	var value = $('#'+ keys).val();
    if( value.length ) {
        value += '; ' + names;
    } else {
        value = names;
    }
    $('#'+ keys).val(value);
}

function after_choose_contacts(ids,names,keys){
	$(".k-window").fadeOut();
    var value = $('#'+ keys).val();
	if( value.length ) {
        value += '; ' + names;
    } else {
        value = names;
    }
    $('#'+ keys).val(value);
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