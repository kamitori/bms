<form id="part_shipping_form" >
<?php
	foreach($arr_settings['relationship']['part_shipping']['block'] as $key => $arr_val){
		echo $this->element('box',array('key'=>$key,'arr_val'=>$arr_val));
	}
?>
</form>
<script type="text/javascript">
$(".viewprice_ship_qty").change(function(){
	var id = $(this).attr("id");
	id = id.split("_");
	id = id[id.length - 1];
	if( $("#txt_balance_shipped_"+id).length ) {
		maxQty = $("#txt_balance_shipped_"+id).text();
	} else {
		maxQty = $("#txt_quantity_"+id).text();
	}
	maxQty = parseFloat(maxQty.replace(",", ""));
	qty = parseFloat($(this).val());
	if( qty < 0 || qty > maxQty ) {
		$(this).val(maxQty);
	}
});
</script>