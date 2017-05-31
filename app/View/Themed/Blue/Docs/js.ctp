<script type="text/javascript">
$(function(){

	$("#DocFile").change(function(){
		$("#DocfileEntryForm").submit();
	});

	$("form :input", "#docs_form_auto_save").change(function() {
		docs_auto_save_entry(this);

	});
});


function docs_auto_save_entry(object){
	$.ajax({
		url: '<?php echo URL; ?>/docs/auto_save',
		timeout: 15000,
		type:"post",
		data: $("form", "#docs_form_auto_save").serialize(),
		success: function(html){
			console.log(html); // view log when debug
		}
	});
}

function docs_entry_delete(id){
	confirms( "Message", "Are you sure you want to delete?",
	    function(){
	       $.ajax({
				url: '<?php echo URL; ?>/docs/entry_docuse_delete/' + id,
				timeout: 15000,
				success: function(html){
					if(html != "ok"){
						alerts("Error: ", html);
					}else{
						$("#DocUse_" + id).fadeOut();
					}
					console.log(html); // view log when debug
				}
			});
	    },function(){
	        //else do somthing
	});
}
</script>