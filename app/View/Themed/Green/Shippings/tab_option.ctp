<?php echo $this->element('tab_option'); ?>

<script type="text/javascript">
	$(function(){
		$('#create_sales_invoice').attr('onclick', 'check_condition_shipping()');
		$('#print_label_using_receiver_shipping_address').attr('onclick', 'print_label()');
	});
	function check_condition_shipping()
	{
		$.ajax({
			url: '<?php echo URL; ?>/shippings/create_sales_invoice',
			type: 'GET',
			success: function(result){
				//console.log(result);
				var result = $.parseJSON(result);
				if(result.status == 'error')
				{
					alerts('Message',result.mess);
				}
				else if(result.status == 'exist')
				{
					confirms('Message',result.mess,function(){window.location.replace(result.url);},function(){return false;});
				}
				else if(result.status='ok')
				{
					window.location.replace(result.url);
				}
			}
		});
		return false;
	}

	function print_label()
	{
		confirms('Message', 'Do you want to print <input id="label_number" min="1" style="width: 50px; text-align: right;" onkeypress="return isNumbers(event);" type="number" value="4"> label(s)?',
					function(){
						var quantity = $('#label_number').val();
						if( quantity == undefined ) {
							quantity = 4;
						}
						var url = undefined;
						$.ajax({
							async: false,
							url: '<?php echo URL; ?>/shippings/create_label/'+quantity,
							success: function(result) {
								var result = $.parseJSON(result);
								if( result.status == "ok" ) {
									url = result.url;
								} else {
									alerts('Message',result.message);
								}
							}
						});
						if( url ) {
							window.open(url);
						}
				}, function() {
					return false;
				});
	}

</script>