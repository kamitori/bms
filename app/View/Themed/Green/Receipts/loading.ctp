
<div class="bg_menu"></div>
<div id="ajax_content" style="width: 100%;margin-top: 300px;text-align: center">
	<h1>Loading<span id="waiting"></span></h1>
</div>
<script type="text/javascript">
	$(function(){
		$.ajax({
			url: '<?php echo URL; ?>/receipts/print_receipts',
			success: function(result){
				if(result!='')
				{
					result = jQuery.parseJSON(result);					
					$("#ajax_content").html('');
					console.log(result);
					for(var i in result)
					{
						$("#ajax_content").append(result[i]);
					}
				}
			}
		});
		
		window.setInterval(function (){
			if($("#waiting").html()=='.....')
				$("#waiting").html('');
			else
				$("#waiting").append('.');
		},500);
		
	});
</script>