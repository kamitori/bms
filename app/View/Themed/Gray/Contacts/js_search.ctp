<?php echo $this->element('js_search');?>
<script type="text/javascript">
$(function() {
	$(window).keypress(function(e) {
		if( e.keyCode == 13 ){
			mainjs_entry_search_ajax('contacts');
		}
	});
});

function after_choose_companies_from_search(company_id, company_name){
    $("#link_to_companies").attr("href", "<?php echo URL; ?>/companies/entry/" + company_id);

    var json = $("#after_choose_companies" + company_id).val();
    $("#ContactCompanyId").val(company_id);
    $("#ContactCompany").val(JSON.parse(json).name);
    $("#ContactCompanyPhone").val(JSON.parse(json).phone);
    $("#ContactFax").val(JSON.parse(json).fax);

    $("#window_popup_companies").data("kendoWindow").close();
}

// xử lý sau khi chọn company
function after_choose_companies(ids,names,keys){
	var m,changes;
	var arr = new Array();
	arr[0] = keys;
	arr[1] = 'company';
	arr[2] = names;
	arr[3] = ids;

	changes = keys.split("_");

	if(arr[0]=='company'){
		$("#"+arr[1]).val(names);
		$("#md_company").html(names);
		$("#"+arr[1]+'_id').val(ids);
		var supplier_default = $("#supplier_current").val();
		if($("#cb_current_"+supplier_default).length>0)
		$("#valuereturn_"+supplier_default).html(names);
		$(".k-window").fadeOut();


	}else if(arr[0]=='supplier'){
		$(".k-window").fadeOut();

	}else if(changes[0]=='change'){
		arr[0] =changes[0];
		arr[1] = changes[1];
		$("#valuereturn_"+arr[1]).html(names);

		// kiem tra neu la default thi set
		if($("#cb_current_"+arr[1]).attr("checked") == "checked"){
			$("#company").val(names);
			$("#md_company").html(names);
		}

		$(".k-window").fadeOut('slow', function() {
			$("#valuereturn_"+arr[1]).html(names);
		});
	};

	after_choose_companies_from_search(ids,names);
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