<?php
	if(isset($arr_settings['relationship'][$sub_tab]['block']))
	foreach($arr_settings['relationship'][$sub_tab]['block'] as $key => $arr_val){
		echo $this->element('box',array('key'=>$key,'arr_val'=>$arr_val));
	}

?>
<p class="clear"></p>

<script type="text/javascript">
// $("#ContactId").val() là _id của tb_contact

//		 after_choose_products('530c539d0e790e6c0b00016e','','_product')
function after_choose_products(ids,names,keys){
	if(keys=='_product'){
		var json_data = JSON.parse($("#after_choose_products"+ keys + ids).val());
		// #id = after_choose_products_product530c539d0e790e6c0b00016e
		//console.log(json_data._id);
		window.location = "<?php echo URL; ?>/contacts/products_pricing/" + $("#mongo_id").val() + "/0/" + json_data._id;
	}
}
$(document).ready(function(){
	window_popup("products", "Specify Product", "_product", "bt_add_product", "");

});

	
</script>

