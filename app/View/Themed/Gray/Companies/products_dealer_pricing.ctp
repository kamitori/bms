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
					<span class="label_1 float_left minw_lab2"><?php echo translate('Code'); ?></span>
				</p>
				<div class="width_in3a float_left indent_input_tp">
					<?php echo $this->Form->input('code', array(
							'class' => 'input_1 float_left',
							'readonly' => true,
							'value' => $arr_product['code']
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

	<script type="text/javascript">

	</script>

    <p class="clear"></p>
</div>
<p class="clear"></p><p class="clear"></p>