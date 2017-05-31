<?php echo $this->element('tab_option'); ?>
<span style="display: none" id="click_open_window_products" style=""></span>
<script type="text/javascript">
	$(function(){
		$("#copy_and_create_new_record").attr("onclick","confirms_duplicate();");
		window_popup('products', 'Specify Product','product_name','click_open_window_products');
	})
	function choose_product(){
		$("#click_open_window_products").click();
	}
	function confirms_duplicate(){
		confirms3('Message','Do you want "duplicate" or "replace" a product?',['Duplicate','Replace','']
		   	,function(){
				$.ajax({
					url: '<?php echo URL . '/' . $controller; ?>/duplicate_product' ,
					success: function(links) {
						if (links != '')
							window.location.assign(links);
					}
				});
			},function(){
				choose_product();
			},function(){
				return false;
			});
	}
	function after_choose_products(ids){
		$("#window_popup_productsproduct_name").data("kendoWindow").close();
		$.ajax({
			url: '<?php echo URL . '/' . $controller; ?>/replace_product' ,
			type: 'post',
			data: {id:ids},
			success: function(links) {
				if (links != '')
					window.location.assign(links);
			}
		});
	}
</script>