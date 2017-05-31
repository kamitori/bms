<?php echo $this->element('js_entry');?>
<script type="text/javascript">
	
	<?php
		$setup_remember_page = 'list_and_menu';
		if(isset($_GET['page']))
			$setup_remember_page = $_GET['page'];
		else if( $this->Session->check('setup_remember_page') )
			$setup_remember_page =  $this->Session->read('setup_remember_page');
	?>
	$(function(){
		 $("#<?php echo $setup_remember_page;?>").click();
	});
	
	function settings_list(object, setting_function_name) {
		$("#settings_ul_nav_setup a").removeClass("active");
		$(object).addClass("active");

		$.ajax({
			url: "<?php echo URL; ?>/setups/" + setting_function_name,
			timeout: 15000,
			success: function(html) {
				$("div#detail_for_main_menu").html(html);
				//alert(html);
			}
		});
	}
</script>