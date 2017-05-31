<?php echo $this->element('js_search');?>
<script type="text/javascript">
$(function(){

});
function get_company_is_shipper()
{
    var para = '?t=1';
	var company_id = $("#ship_to_company_id").val();
	var company_name = $("#ship_to_company_name").val();
	if(company_id!='')
		para += '&company_id='+company_id;
	if(company_name!='')
		para += '&company_name='+company_name;
	return para;

}
function get_para_ship_contact()
{
    var para = '?is_employee=1';
	var company_id = $("#ship_to_company_id").val();
	var company_name = $("#ship_to_company_name").val();
	var contact_name = $("#ship_to_contact_name").val();
	var contact_id = $("#ship_to_contact_id").val();
	if(company_id!='')
		para += '&company_id='+company_id;
	if(company_name!='')
		para += '&company_name='+company_name;
	if(contact_id!='')
		para += '&contact_id='+contact_id;
	if(contact_name!='')
		para += '&contact_name'+contact_name;
	return para;

}
// xử lý sau khi chọn company
function after_choose_companies(ids,names,keys){
	var m,changes;
	var arr = new Array();
	arr[0] = keys;
	arr[1] = 'company_name';
	arr[2] = 'ship_to_company';
	arr[2] = names;
	arr[3] = ids;

	if(arr[0]=='company_name'){
		$("#"+arr[1]).val(names);
		$("#"+arr[1]+'_id').val(ids);

	}else if(arr[0]=='ship_to_company_name'){
		$("#ship_to_company_name").val(names);
		$("#ship_to_company_id").val(ids);
	}else if(arr[0]=='shipper_company_name'){
		$("#shipper_company_name").val(names);
		$("#shipper_company_id").val(ids);
	}
	$(".k-window").fadeOut();
}

function after_choose_contacts(ids,names,keys){
	if(keys=='contact_name'){
		$("#contact_id").val(ids);
		$("#contact_name").val(names);
	}
	if(keys=='ship_to_contact_name'){
		$("#ship_to_contact_id").val(ids);
		$("#ship_to_contact_name").val(names);
	}
	$(".k-window").fadeOut();
}

</script>