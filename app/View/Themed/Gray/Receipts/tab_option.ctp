<?php echo $this->element('tab_option'); ?>
<script type="text/javascript">
	$(function(){
		$('#print_receipt').attr('onclick', 'check_condition()');
		$('#print_receipts').attr('onclick','confirm_print()');
	});
	function check_condition()
	{
		$.ajax({
			url: '<?php echo URL; ?>/receipts/check_condition_receipt',
			success: function(result)
			{
				result = jQuery.parseJSON(result);
				if(result.status=='error')
					alerts('Message',result.message);
				else if(result.status == 'ok')
					window.location.replace(result.url);
				console.log(result);
			}
		});
		return false;
	}
	function confirm_print()
	{
		confirms('Message','There are receipts in the found set that have no allocations entered on them. Prints those that do have allocations?',function(){
				window.location.replace('<?php echo URL; ?>/receipts/loading');
			},function(){
				return false;
			},['Cancel','Print']);
	}
</script>