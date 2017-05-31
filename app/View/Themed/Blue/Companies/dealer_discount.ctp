<?php
	if(isset($arr_settings['relationship'][$sub_tab]['block']))
	foreach($arr_settings['relationship'][$sub_tab]['block'] as $key => $arr_val){
		echo $this->element('box',array('key'=>$key,'arr_val'=>$arr_val));
	}
?>
<script type="text/javascript">
	window_popup('products', 'Specify Product', '', 'bt_add_dealer_pricing', "?no_supplier=1");
	$("#load_subtab").on("change","#rel_dealer_discount",function(){
        var ids = $("#mongo_id").val();
        var names = $(this).attr("name");
        names = names.replace("_cb", "");
        var inval = $(this).val();
        save_data(names,inval,'',function(){})
    })
    function after_choose_products(product_id){
		var company_id = $("#mongo_id").val();
		$.ajax({
			url : "<?php echo URL.'/companies/dealer_pricing_add' ?>",
			type: "POST",
			data: {product_id : product_id},
			success: function(result){
				$("div.k-widget:has(div#window_popup_products)").css("display","none");
				$("#dealer_discount").click();
			}
		})
	}
	$("input[name^='discount']").change(function(){
		var ids = $("#mongo_id").val();
        var code = $(this).attr("name");
        code = code.replace("discount_", "");
        var inval = $(this).val();
		//alert(ids + ' | ' + code + ' | ' + inval);
		$.ajax({
			url : "<?php echo URL.'/companies/edit_discount' ?>",
			type: "POST",
			data: {ids : ids, code:code, inval:inval},
			success: function(result){
				ajax_note("Saving...Saved !");
			}
		})
	})
</script>