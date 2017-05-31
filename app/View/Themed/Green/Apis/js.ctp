<?php echo $this->element('js_entry');?>
<script type="text/javascript">
$(function(){
	//default focus
	$("#description").focus();
	// Xu ly save, update
	$("form input,form select").change(function() {
		var fixkendo = $(this).attr('class');
		var fieldname = $(this).attr("name");
		fieldname = fieldname.replace("_cb","");
		var fieldid = $(this).attr("id");
		var fieldtype = $(this).attr("type");
			modulename = 'mongo_id';
		var ids = $("#"+modulename).val();
		var values = $(this).val();


		$(".jt_ajax_note").html("Saving...");
		if(fieldname=='province'){
			var values2 = $("#provinceId").val();
			save_data('province_key',values2,ids);
			save_data('province',values,ids);
		} else
			save_data(fieldname,values,ids);
	});

	$(".jt_ajax_note").html('');

});

function after_choose_companies(ids,names,keys){
	if(keys=='company'){
		var arr_data_from = JSON.parse($("#after_choose_companies"+ keys + ids).val());
		var value = arr_data_from.name;
		add1 = arr_data_from.addresses['0'].address_1;
		add2 = arr_data_from.addresses['0'].address_2;
		add3 = arr_data_from.addresses['0'].address_3;
		phone = arr_data_from.phone;
		fax = arr_data_from.fax;
		email = arr_data_from.email;
		web = arr_data_from.web;
		$("#company").val(names);
		$("#company_id").val(ids);
		save_data('company',value,'',ids,function(){
		});
	}
}
</script>