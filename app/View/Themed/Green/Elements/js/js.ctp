<?php //phần dùng chung cho cả js_entry và js_list của các module ?>
<script language="javascript" type="application/javascript">
	(function($){
	})(jQuery);
	
	/**
	* Confirm delete for menu Delete top right
	* @author vu.nguyen
	* @since v1.0
	*/
	function confirm_delete(links){
		confirms('Message','<?php msg('CONFIRM_DELETE');?>',
			function(){
				window.location.assign(links);
			},
			function(){ console.log('You had cancel.')}
		);
	}


</script>