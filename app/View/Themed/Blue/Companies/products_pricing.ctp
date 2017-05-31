<style type="text/css">
.label_1 {

width: 7.5%;
}
.minw_lab2 {
min-width: 21%;
}
.width_in3a {
width: 75%;
}
</style>

<div style="padding:0px 0 10px 15px; margin-top:10px;">
	<h2><?php echo translate('Product'); ?>: <?php echo $arr_product['name']; ?></h2>
</div>

<?php echo $this->element('entry_tab_option');?>
<div class="clear_percent">
	<div class="float_left " style=" width:27%; float: left;">
		<div class="tab_1 full_width" style="">
			<span class="title_block bo_ra1">
				<span class="float_left">
					<span class="fl_dent"><h4></h4></span>
				</span>
			</span>
			<div class="tab_2_inner" style="height:198px;" id="company_product_left">

				<p class="clear">
					<span class="label_1 float_left minw_lab2"><?php echo translate('SKU'); ?></span>
				</p>
				<div class="width_in3a float_left indent_input_tp">
					<?php echo $this->Form->input('sku', array(
							'class' => 'input_1 float_left',
							'readonly' => true,
							'value' => $arr_product['sku']
					)); ?>
				</div>
				<p></p>
				<p class="clear">
					<span class="label_1 float_left minw_lab2"><?php echo translate('Name'); ?></span>
				</p>
				<div class="width_in3a float_left indent_input_tp">
					<?php echo $this->Form->input('name', array(
							'class' => 'input_1 float_left',
							'readonly' => true,
							'value' => $arr_product['name']
					)); ?>
				</div>

				<p class="clear">
					<span class="label_1 float_left minw_lab2 "><?php echo translate('Customer'); ?></span>
				</p><div class="width_in3a float_left indent_input_tp">
					<?php echo $this->Form->input('customer', array(
							'class' => 'input_1 float_left',
							'readonly' => true,
							'value' => $company['name']
					)); ?>
				</div>
				<p class="clear">
					<span class="label_1 float_left minw_lab2 "></span>
				</p><div class="width_in3a float_left indent_input_tp">
				</div>
				<p class="clear">
					<span class="label_1 float_left minw_lab2 "></span>
				</p><div class="width_in3a float_left indent_input_tp">
				</div>
				<p class="clear">
					<span class="label_1 float_left minw_lab2 "></span>
				</p><div class="width_in3a float_left indent_input_tp">
				</div>
				<p class="clear">
					<span class="label_1 float_left minw_lab2 fixbor4" style="height: 37px;"></span>
				</p><div class="width_in3a float_left indent_input_tp">
				</div>

				<p></p>
				<p class="clear"></p>

			</div>
			<span class="title_block bo_ra2"></span>
		</div>
	</div>

	<div class="float_left" style=" width:27%;margin-left:1%;float: left;" id="company_product_price_break">
		<?php echo $this->element('..'.DS.'Companies'.DS.'products_price_break',array('key',$key)); ?>
	</div>
	<script type="text/javascript">

	function company_product_pricebreak_save(object){
		$.ajax({
			url: "<?php echo URL; ?>/companies/product_pricebreak_save/<?php echo $key; ?>/" + $(object).attr("rel"),
			timeout: 15000,
			type: "POST",
			data: { field: $(object).attr("name"), value: $(object).val() },
			success: function(html){
				if(html != "ok"){
					alerts("Error: ", html, function(){ location.reload(); });
				}
			}
		});
		return false;
	}

	function company_pricebreak_delete(subkey){
		confirms( "Message", "Are you sure you want to delete?",
			function(){
				$.ajax({
					 url: '<?php echo URL; ?>/companies/products_pricebreak_delete/<?php echo $key; ?>/' + subkey,
					 timeout: 15000,
					 success: function(html){
						 if(html == "ok"){
							$("#company_product_price_break_" + subkey).remove();
						 }else{
							alerts("Error: ", html);
						 }
					 }
				 });
			},function(){
				//else do somthing
		});
	}
	</script>

	<div class="float_left " style=" width:44%;margin-left:1%; float: left;">
		<div class="tab_1 full_width">
			<span class="title_block bo_ra1">
				<span class="float_left">
					<span class="fl_dent"><h4><?php echo translate('Notes'); ?></h4></span>
				</span>
			</span>
			<p class="clear"></p>
			<div id="company_products_pricing_note" style="height: 483px;">
				<textarea class="area_t3" onchange="company_product_save_notes(this)" style="height:98%;"><?php echo $arr_product['notes']; ?></textarea>
				<script type="text/javascript">

				function company_product_save_notes(object){
					$.ajax({
						url: "<?php echo URL; ?>/companies/products_save_notes/<?php echo $company_id; ?>/<?php echo $key; ?>",
						timeout: 15000,
						type: "POST",
						data: { notes: $(object).val() },
						success: function(html){
							if(html != "ok"){
								alerts("Error: ", html);

							}
						}
					});
					return false;
				}
				</script>
			</div>
			<span class="title_block bo_ra2">
				<span class="float_left bt_block">
				</span>
			</span>
		</div>
	</div>
    <p class="clear"></p>
</div>
<p class="clear"></p><p class="clear"></p>