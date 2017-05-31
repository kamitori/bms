<?php
foreach ($arr_settings['relationship'][$sub_tab]['block'] as $key => $arr_val) {

    echo $this->element('box', array('key' => $key, 'arr_val' => $arr_val));
}
?>
<p class="clear"></p>


<script type="text/javascript">
    $(function() {
        <?php if($this->Common->check_permission('products_@_entry_@_edit',$arr_permission)): ?>
        $('#bt_add_received').click(function() {
            var arr = new Object();
            arr['invoice_day'] = '<?php echo date('d M, Y'); ?>';
            save_option('received', arr, '', 1, 'shipping_received', 'add');
        });
        <?php endif; ?>
        <?php if(!$this->Common->check_permission('shippings_@_entry_@_view',$arr_permission)): ?>
        $("#container_shipping").find('[onclick]').each(function(){
            $(this).removeAttr('title');
            $(this).removeAttr('onclick');
            $(this).html('');
        });
        <?php endif; ?>
    });
    function checkCondition(Object,type)
    {
    	$.ajax({
    		url: '<?php echo URL ?>/purchaseorders/checkCondition',
    		type: 'POST',
    		data: {type:type},
    		success: function(result){
    			result = jQuery.parseJSON(result);
    			if(result.status=='error')
    				alerts('Message',result.message);
    			else
    				window.location.replace(Object.attr('rel'));
    			// console.log(result);
    			return false;
    		}
    	});
    }
</script>