<style type="text/css">
#company_product_price_break .hg_padd {
	padding: 4px 1% !important;
}
</style>
<div class="tab_1 full_width">
	<span class="title_block bo_ra1">
		<span class="fl_dent">
			<h4><?php echo translate('Price break'); ?></h4>
		</span>

 		<?php echo $this->Js->link( '<span class="icon_down_tl top_f"></span>', '/companies/products_price_break_add/'.$key,
			array(
				'update' => '#company_product_price_break',
				'title' => 'Add new price break',
				'escape' => false
			) );
		?>
	</span>


	<p class="clear"></p>
	<ul class="ul_mag clear bg3">
		<li class="hg_padd" style="text-align:center;width:24%">Range from</li>
		<li class="hg_padd" style="text-align:center;width:24%">Range to</li>
		<li class="hg_padd" style="width:36%">Unit price</li>
		<li class="hg_padd" style="width:6%"></li>
	</ul>
	<div class="">
		<?php
			$i = 1; $count = 0;
			?>
			<?php foreach ($arr_product['price_break'] as $price_break_key => $value) {

					if( $value['deleted'] )continue;
			 ?>
			<ul class="ul_mag clear bg<?php echo $i; ?>" id="company_product_price_break_<?php echo $price_break_key; ?>">
				<li class="hg_padd" style="width:24%">
					<?php echo $this->Form->input('range_from', array(
							'class' => 'input_inner input_inner_w bg'.$i,
							'rel' => $price_break_key,
							'placeholder' => 'new value',
							'style' => 'text-align:center;',
							'onchange' => 'company_product_pricebreak_save(this)',
							'value' => (is_numeric($value['range_from']))?(int)$value['range_from']:''
					)); ?>
				</li>
				<li class="hg_padd" style="width:24%">
					<?php echo $this->Form->input('range_to', array(
							'class' => 'input_inner input_inner_w bg'.$i,
							'rel' => $price_break_key,
							'placeholder' => 'new value',
							'style' => 'text-align:center;',
							'onchange' => 'company_product_pricebreak_save(this)',
							'value' => (is_numeric($value['range_to']))?(int)$value['range_to']:''
					)); ?>
				</li>
				<li class="hg_padd " style="width:36%;">
					<?php echo $this->Form->input('unit_price', array(
							'class' => 'input_inner input_inner_w bg'.$i,
							'rel' => $price_break_key,
							'placeholder' => 'new value',
							'style' => 'text-align:right;',
							'onchange' => 'company_product_pricebreak_save(this)',
							'value' => (is_numeric($value['unit_price']))?$this->Common->format_currency($value['unit_price']):''
					)); ?>
				</li>
				<li class="hg_padd" style="width:6%">
					<div class="middle_check">
						<a title="Delete link" href="javascript:void(0)" onclick="company_pricebreak_delete(<?php echo $price_break_key; ?>)">
							<span class="icon_remove2"></span>
						</a>
					</div>
				</li>
			</ul>
			<?php $i = 3 - $i;
				$count += 1;
			}

			$count = 21 - $count;
			if( $count > 0 ){
				for ($j=0; $j < $count; $j++) { ?>
					<ul class="ul_mag clear bg<?php echo $i; ?>">
					</ul>
					<?php $i = 3 - $i;
				}
			}
		?>
	</div>
	<span class="title_block bo_ra2">
		<span class="float_left bt_block">
			<?php echo translate('Click to view full details'); ?>
		</span>
	</span>
</div>