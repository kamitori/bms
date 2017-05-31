<div class="tab_1 full_width">
	<span class="title_block bo_ra1">
		<span class="fl_dent"><h4><?php echo translate('Sales orders for this job'); ?></h4></span>
		<?php if($this->Common->check_permission('salesorders_@_entry_@_add',$arr_permission)): ?>
		<a href="<?php echo URL; ?>/jobs/orders_add_salesorder/<?php echo $job_id; ?>" title="Add new sales order">
			<span class="icon_down_tl top_f"></span>
		</a>
		<?php endif; ?>
	</span>
	<p class="clear"></p>
	<ul class="ul_mag clear bg3">
		<li class="hg_padd" style="width:1%;"></li>
		<li class="hg_padd" style="width:7%;"><?php echo translate('Ref no'); ?></li>
		<li class="hg_padd center_txt" style="width:10%;"><?php echo translate('Date in'); ?></li>
		<li class="hg_padd center_txt" style="width:10%;"><?php echo translate('Due date'); ?></li>
		<li class="hg_padd" style="width:8%;"><?php echo translate('Status'); ?></li>
		<!-- <li class="hg_padd" style="width:8%;"><?php echo translate('Assign to'); ?></li> -->
		<li class="hg_padd" style="width:8%;"><?php echo translate('Total before Tax'); ?></li>
		<li class="hg_padd" style="width:13%;"><?php echo translate('Heading'); ?></li>
		<li class="hg_padd" style="width:29%;"><?php echo translate('Comments'); ?></li>
		<li class="hg_padd bor_mt" style="width:1%;"></li>
	</ul>
	<div class="container_same_category" style="height: 200px;overflow-y: auto;">
		<?php
		$i = 1; $count = 0;
		$total_sub_total = $total_tax = $total_amount = 0;
		foreach ($arr_salesorder as $key => $value) {
			$sub_total = (isset($value['sum_sub_total'])&&$value['sum_sub_total'] ? $value['sum_sub_total'] : 0);
			$tax = (isset($value['sum_tax'])&&$value['sum_tax'] ? $value['sum_tax'] : 0);
			$amount = (isset($value['sum_amount'])&&$value['sum_amount'] ? $value['sum_amount'] : 0);
			if($value['status']!='Cancelled'){
                $total_sub_total += $sub_total;
                $total_tax += $tax;
                $total_amount += $amount;
            }
		?>
		<ul class="ul_mag clear bg<?php echo $i; ?>" id="Job_salesorder_<?php echo $value['_id']; ?>">
			<li class="hg_padd" style="width:1%;">
				<a href="<?php echo URL; ?>/salesorders/entry/<?php echo $value['_id']; ?>">
					<span class="icon_emp"></span>
				</a>
			</li>
			<li class="hg_padd" style="width:7%;"><?php echo $value['code']; ?></li>
			<li class="hg_padd center_txt" style="width:10%;"><?php echo $this->Common->format_date($value['salesorder_date']->sec,false); ?></li>
			<li class="hg_padd center_txt" style="width:10%;"><?php echo $this->Common->format_date($value['payment_due_date']->sec,false); ?></li>
			<li class="hg_padd" style="width:8%;"><?php echo $value['status']; ?></li>
			<!-- <li class="hg_padd" style="width:8%;"><?php echo $value['our_rep']; ?></li> -->
			<li class="hg_padd" style="width:8%;"><?php echo $this->Common->format_currency($sub_total); ?></li>
			<li class="hg_padd" style="width:13%;"><?php echo $value['heading']; ?></li>
			<li class="hg_padd" style="width:29%;"><?php if(isset($value['other_comment']))echo $value['other_comment']; ?></li>
			<?php if($this->Common->check_permission('salesorders_@_entry_@_delete',$arr_permission)): ?>
			<li class="hg_padd bor_mt" style="width:1%;">
				<div class="middle_check">
					<a title="Delete link" href="javascript:void(0)" onclick="salesorder_remove_salesorder_job('<?php echo $value['_id']; ?>')">
						<span class="icon_remove2"></span>
					</a>
				</div>
			</li>
			<?php endif; ?>
		</ul>

		<?php $i = 3 - $i; $count += 1;
			}

			$count = 8 - $count;
			if( $count > 0 ){
				for ($j=0; $j < $count; $j++) { ?>
				<ul class="ul_mag clear bg<?php echo $i; ?>">
					<li class="hg_padd" style="width:1%;"></li>
					<li class="hg_padd" style="width:7%;"></li>
					<li class="hg_padd center_txt" style="width:10%;"></li>
					<li class="hg_padd center_txt" style="width:10%;"></li>
					<li class="hg_padd" style="width:8%;"></li>
					<li class="hg_padd" style="width:8%;"></li>
					<li class="hg_padd" style="width:13%;"></li>
					<li class="hg_padd" style="width:29%;"></li>
					<li class="hg_padd bor_mt" style="width:1%;"></li>
				</ul>
		  <?php $i = 3 - $i;
				}
			}
		?>
	</div>
	<span class="title_block bo_ra2">
		<span class="float_left bt_block">Click to view full details</span>
		<span class="bt_block float_left no_bg" style="float: right;">
			<span class="float_left">Total Amount</span>
			<input class="input_7 right_txt" style="width:90px" value="<?php echo $this->Common->format_currency($total_amount); ?>" readonly="readonly" type="text">
		</span>
		<span class="bt_block float_left no_bg" style="float: right;">
			<span class="float_left">Total Tax</span>
			<input class="input_7 right_txt" style="width:90px" value="<?php echo $this->Common->format_currency($total_tax); ?>" readonly="readonly" type="text">
		</span>
		<span class="bt_block float_left no_bg" style="float: right;">
			<span class="float_left">Total Sub total</span>
			<input class="input_7 right_txt" style="width:90px" value="<?php echo $this->Common->format_currency($total_sub_total); ?>" readonly="readonly" type="text">
		</span>
	</span>
</div>
<?php if($this->Common->check_permission('salesorders_@_entry_@_edit',$arr_permission)): ?>
<script type="text/javascript">
function salesorder_remove_salesorder_job(salesorder_id){
	confirms( "Message", "Are you sure you want to delete?",
		function(){

			$.ajax({
				 url: '<?php echo URL; ?>/jobs/orders_delete/' + salesorder_id,
				 timeout: 15000,
				 success: function(html){
					 if(html == "ok"){
						 $("#Job_salesorder_" + salesorder_id).fadeOut();
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
<?php endif; ?>