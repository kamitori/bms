	<div class="clear_percent_10 float_left right_pc" style="margin-right: 1%;">
		<div class="tab_1 full_width">
			<span class="title_block bo_ra1">
				<span class="fl_dent"><h4>Overall job totals</h4></span>
			</span>
			<div>

			<p class="clear">
				<span class="label_1 float_left minw_lab2"><a href=""></a>Purchase orders</span>
				</p><div class="width_in3a float_left indent_input_tp">
					<?php echo $this->Form->input('Job.purchaseorder', array(
                        'class' => 'input_1 float_left',
                        'value' => isset($data['overall_job_totals']['purchaseorder'])?$this->Common->format_currency($data['overall_job_totals']['purchaseorder']):0,
                        'readonly' => 'readonly'
                    ));?>
				</div>

				<p class="clear">
					<span class="label_1 float_left minw_lab2"><a href=""></a>Expenses</span>
					</p><div class="width_in3a float_left indent_input_tp">
						<?php echo $this->Form->input('Job.expenses', array(
                        'class' => 'input_1 float_left',
                        'value' => isset($data['overall_job_totals']['expenses'])?$this->Common->format_currency($data['overall_job_totals']['expenses']):0,
                        'readonly' => 'readonly'
                    ));?>
					</div>

				<p class="clear">
					<span class="label_1 float_left minw_lab2"><a href=""></a>Employee time costs</span>
					</p><div class="width_in3a float_left indent_input_tp">
						<?php echo $this->Form->input('Job.employee_time_costs', array(
                        'class' => 'input_1 float_left',
                        'value' => isset($data['overall_job_totals']['employee_time_costs'])?$this->Common->format_currency($data['overall_job_totals']['employee_time_costs']):0,
                        'readonly' => 'readonly'
                    ));?>
					</div>

				<p class="clear">
					<span class="label_1 float_left minw_lab2">Commission</span>
					</p><div class="width_in3a float_left indent_input_tp">
						<?php echo $this->Form->input('Job.commission', array(
                        'class' => 'input_1 float_left',
                        'value' => isset($data['overall_job_totals']['commission'])?$this->Common->format_currency($data['overall_job_totals']['commission']):0,
                        'readonly' => 'readonly'
                    ));?>
					</div>
				<p class="clear">
					<span class="label_1 float_left minw_lab2">______________</span>
					</p><div class="width_in3a float_left indent_input_tp">
						<?php echo $this->Form->input('Job.null', array(
                        'class' => 'input_1 float_left',
                        'readonly' => true,
                        'disbled' => true,
                    ));?>
					</div>
				<p class="clear">
					<span class="label_1 float_left minw_lab2">Total costs</span>
					</p><div class="width_in3a float_left indent_input_tp">
						<?php echo $this->Form->input('Job.total_costs', array(
                        'class' => 'input_1 float_left',
                        'value' => isset($data['overall_job_totals']['total_costs'])?$this->Common->format_currency($data['overall_job_totals']['total_costs']):0,
                        'readonly' => 'readonly'
                    ));?>
					</div>

				<p class="clear">
					<span class="label_1 float_left minw_lab2"><a href="" class="hidd"></a>Sales order costs</span>
					</p><div class="width_in3a float_left indent_input_tp">
						<?php echo $this->Form->input('Job.salesorder', array(
                        'class' => 'input_1 float_left',
                        'value' => isset($data['overall_job_totals']['salesorder'])?$this->Common->format_currency($data['overall_job_totals']['salesorder']):0,
                        'readonly' => 'readonly'
                    ));?>
					</div>
				<p class="clear">
					<span class="label_1 float_left minw_lab2">Profit</span>
					</p><div class="width_in3a float_left indent_input_tp">
						<?php echo $this->Form->input('Job.profit', array(
                        'class' => 'input_1 float_left',
                        'value' => isset($data['overall_job_totals']['profit'])?$this->Common->format_currency($data['overall_job_totals']['profit']):0,
                        'readonly' => 'readonly'
                    ));?>
					</div>
				<p class="clear">
					<span class="label_1 float_left minw_lab2">Margin</span>
					</p><div class="width_in3a float_left indent_input_tp">
						<?php echo $this->Form->input('Job.margin', array(
                        'class' => 'input_1 float_left',
                        'value' => (isset($data['overall_job_totals']['margin'])?$this->Common->format_currency($data['overall_job_totals']['margin'],1):0).'%',
                        'readonly' => 'readonly'
                    ));?>
					</div>

				<div class="block_warning warning_height" style="height:6px">
					<span class="label_bg float_left minw_lab2 fixbor3"></span><!--
					<div class="width_in3a float_left indent_input_tp">
							<div class="warning"><span class="color_hidden"></span></div>
					</div> -->
				</div>
				<p class="clear"></p>
			</div>
			<span class="title_block bo_ra2">
				<!-- <p class="cent">
					<input class="btn_pur" type="button" value="Summary job costing report">
				</p> -->
			</span>
		</div><!--END Tab1 -->
	</div>


    <div class="clear_percent_11 float_left no_right" style="width: 68.5%;">
		<div class="tab_1 full_width">
			<span class="title_block bo_ra1">
				<span class="fl_dent"><h4>Sales invoices for this job</h4></span>
				<?php if($this->Common->check_permission('salesinvoices_@_entry_@_add',$arr_permission)): ?>
				<a href="<?php echo URL; ?>/jobs/invoices_add/<?php echo $job_id; ?>" title="Add new sales invoice">
					<span class="icon_down_tl top_f"></span>
				</a>
				<?php endif; ?>
			</span>
			<ul class="ul_mag clear bg3">
				<li class="hg_padd" style="width:1%"></li>
				<li class="hg_padd" style="width:7%">Ref no</li>
				<li class="hg_padd" style="width:9%">Type</li>
				<li class="hg_padd center_txt" style="width:6%">Date</li>
				<li class="hg_padd" style="width:9%">Status</li>
				<li class="hg_padd" style="width:15%">Our rep</li>
				<li class="hg_padd right_txt" style="width:9%">Ex. Tax total</li>
				<li class="hg_padd" style="width:32%">Comments</li>
				<li class="hg_padd no_border" style="width:1%"></li>
			</ul>
			<div class="container_same_category" style="height: 200px;overflow-y: auto;">

			<?php
				$i = 1; $count = 0;
				$total_sub_total = $total_tax = $total_amount = 0;
				foreach ($arr_invoices as $key => $value) {
					$sub_total = (isset($value['sum_sub_total'])&&$value['sum_sub_total'] ? $value['sum_sub_total'] : 0);
					$tax = (isset($value['sum_tax'])&&$value['sum_tax'] ? $value['sum_tax'] : 0);
					$amount = (isset($value['sum_amount'])&&$value['sum_amount'] ? $value['sum_amount'] : 0);
					if($value['invoice_status']!='Cancelled'){
						$total_sub_total += $sub_total;
						$total_tax += $tax;
						$total_amount += $amount;
					}
				?>
				<ul class="ul_mag clear bg<?php echo $i; ?>" id="Job_salesinvoice_<?php echo $value['_id']; ?>">
					<li class="hg_padd" style="width:1%;">
						<a href="<?php echo URL; ?>/salesinvoices/entry/<?php echo $value['_id']; ?>">
							<span class="icon_emp"></span>
						</a>
					</li>
					<li class="hg_padd" style="width:7%"><?php echo $value['code']; ?></li>
					<li class="hg_padd" style="width:9%"><?php echo $value['invoice_type']; ?></li>
					<li class="hg_padd center_txt" style="width:6%"><?php if(is_object($value['invoice_date']))echo $this->Common->format_date($value['invoice_date']->sec,false); ?></li>
					<li class="hg_padd" style="width:9%"><?php echo $value['invoice_status']; ?></li>
					<li class="hg_padd" style="width:15%"><?php echo $value['our_rep']; ?></li>
					<li class="hg_padd right_txt" style="width:9%"><?php echo $this->Common->format_currency($sub_total); ?></li>
					<li class="hg_padd" style="width:32%"><?php echo (isset($value['other_comment']) ? $value['other_comment'] : ''); ?></li>
					<?php if($this->Common->check_permission('salesinvoices_@_entry_@_delete',$arr_permission)): ?>
					<li class="hg_padd bor_mt" style="width:1%;">
						<div class="middle_check">
							<a title="Delete link" href="javascript:void(0)" onclick="salesinvoice_remove_salesinvoice_job('<?php echo $value['_id']; ?>')">
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
						<li class="hg_padd" style="width:1%"></li>
						<li class="hg_padd" style="width:7%"></li>
						<li class="hg_padd" style="width:9%"></li>
						<li class="hg_padd center_txt" style="width:6%"></li>
						<li class="hg_padd" style="width:9%"></li>
						<li class="hg_padd" style="width:15%"> </li>
						<li class="hg_padd right_txt" style="width:9%"></li>
						<li class="hg_padd" style="width:32%"></li>
						<li class="hg_padd no_border" style="width:1%"></li>
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
					<span class="float_left">Total (not inc. cancelled transactions)</span>
					<input class="input_7 right_txt" style="width:90px" value="<?php echo $this->Common->format_currency($total_sub_total); ?>" readonly="readonly" type="text">
				</span>
			</span>
		</div><!--END Tab1 -->
	</div>

	<p class="clear"></p>

<?php if($this->Common->check_permission('salesinvoices_@_entry_@_edit',$arr_permission)): ?>
<script type="text/javascript">
function salesinvoice_remove_salesinvoice_job(salesinvoice_id){
	confirms( "Message", "Are you sure you want to delete?",
		function(){

			$.ajax({
				 url: '<?php echo URL; ?>/jobs/invoices_delete/' + salesinvoice_id,
				 timeout: 15000,
				 success: function(html){
					 if(html == "ok"){
						 $("#Job_salesinvoice_" + salesinvoice_id).fadeOut();
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

<script type="text/javascript">
function save_overall_job(object, field){
	$.ajax({
		url:'<?php echo URL; ?>/jobs/overall_job_totals_save/' + $("#JobId").val(),
		timeout:1500,
		type:"POST",
		data:{"field":field, value: $(object).val()},
		success: function(html){
			if(html != "ok"){
				alerts("Error: ", html);
			}
			console.log(html);
		}
	});
}
</script>