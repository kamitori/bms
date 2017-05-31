<script type="text/javascript">
$(function(){
	<?php $this->Common->check_lock_sub_tab($controller,$arr_permission); ?>
	<?php $this->Common->unlink_modules($arr_link,$arr_permission); ?>
	<?php if(!$this->Common->check_permission('products_@_entry_@_view',$arr_permission)): ?>
	$(".choice_sku ,.choice_code","#container_products").each(function(){
		$(this).replaceWith(function(){
			return $("<span>"+$(this).html()+"</span>");
		});
	});
	// $("#container_products").find('[onclick]').each(function(){
	// 	$(this).removeAttr('onclick');
	// 	$(this).removeAttr('title');
	// 	$(this).html('');
	// });
	<?php endif; ?>
})
</script>