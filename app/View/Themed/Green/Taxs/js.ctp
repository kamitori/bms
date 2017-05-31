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
		var callBack = undefined;
		if( fieldname == 'hst_tax' && values != 'Not use HST tax' ) {
			var oldValue = values;
			callBack = function(){
				$('#'+fieldname).val(oldValue);
			};
			values = $('#'+fieldname+'Id').val();
		}


		$(".jt_ajax_note").html("Saving...");
		if(fieldname=='province'){
			var values2 = $("#provinceId").val();
			save_data('province_key',values2,ids);
			save_data('province',values,ids);
		} else {
			save_data(fieldname,values,ids, 0,callBack);
		}
	});
	$(".jt_ajax_note").html('');

});
</script>