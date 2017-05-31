<?php // echo $this->element('js_search');?>
<script type="text/javascript">
$(function(){
	$(".menu_control li:first a").attr('href','javascript:choice_type_comms();');
	$(window).keypress(function(e) {
		if( e.keyCode == 13 ){
			mainjs_entry_search_ajax('<?php echo $controller; ?>');
		}
	});

});

function choice_type_comms(){
	//cutom Option value
	if( $("#confirms_window").attr("id") == undefined ){
		var buttons = '';
		buttons += '<input id="choose_email" class="btn_pur" type="button" value="Email">';
		buttons += '<input id="choose_letter" class="btn_pur" type="button" value="Letter">';
		buttons += '<input id="choose_fax" class="btn_pur" type="button" value="Fax">';

		$('<div id="confirms_window" style="display:none;"><div class="choice_type_comms">'+buttons+'<input type="button" id="choose_cancel" value=" Cancel " /></div>').appendTo("body");
	}
	var confirms_window = $("#confirms_window");
		confirms_window.kendoWindow({
			width: "400px",
			height: "150px",
			title: "Choose type to create"
		});

	//show popup
	confirms_window.data("kendoWindow").center();
	confirms_window.data("kendoWindow").open();
	$('#choose_email').click(function() {
       confirms_window.data("kendoWindow").close();
	   add_new('comms_type','Email');
    });
	$('#choose_letter').click(function() {
       confirms_window.data("kendoWindow").close();
	   add_new('comms_type','Letter');
    });
	$('#choose_fax').click(function() {
       confirms_window.data("kendoWindow").close();
	   add_new('comms_type','Fax');
    });
	$('#choose_cancel').click(function() {
       confirms_window.data("kendoWindow").close();
    });
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