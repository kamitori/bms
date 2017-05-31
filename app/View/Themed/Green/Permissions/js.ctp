<?php echo $this->element('js_entry');?>
<script type="text/javascript">
$(function(){
	//default focus
	$("#module_name").focus();
	// Xu ly save, update
	$("form input,form select").change(function() {
		var fixkendo = $(this).attr('class');
		var fieldname = $(this).attr("name");
		var fieldid = $(this).attr("id");
		var fieldtype = $(this).attr("type");
			modulename = 'mongo_id';
		var ids = $("#"+modulename).val();
		var values = $(this).val();
		if(fieldtype=='checkbox'){
			if($(this).is(':checked'))
				values = 1;
			else
				values = 0;
		}
		
		$(".jt_ajax_note").html("Saving...");
		save_data(fieldname,values,ids);
		ajax_note("Saving...Saved !");
		
	});
	
	$(".jt_ajax_note").html('');
	
});
</script>