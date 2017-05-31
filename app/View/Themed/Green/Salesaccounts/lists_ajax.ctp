<?php $i = 1;
	$sum_30 = 0;
	$sum_60 = 0;
	$sum_90 = 0;
	$sum_other = 0;
	$sum_balance = 0;
?><br>
<?php foreach ($arr_salesaccount as $value):
	$i = 3 - $i;
	$sum = 0;
	$total_sum = 0;
?>
	<ul class="ul_mag clear bg<?php echo $i ?>" id="salesaccounts_<?php if(isset($value['_id']))echo (string) $value['_id']; ?>">

		<li class="hg_padd" style="width:1%">
			<a style="color: blue" href="<?php echo URL; ?>/salesaccounts/entry/<?php if(isset($value['_id']))echo $value['_id']; ?>"><span class="icon_emp"></span></a>
		</li>
		<li class="hg_padd" style="width:6%">
			<?php
			if (isset($value['company_id'])&&is_object($value['company_id']))
				echo 'Company';
			?>
		</li>
		<li class="hg_padd center_txt" style="width:4%"><?php if(isset($value['no']))echo $value['no']; ?></li>
		<li class="hg_padd" style="width:24%">
			<?php
			// nếu SA là company
			if (isset($value['company_id']) && is_object($value['company_id'])){
				if(!isset($arr_company_tmp))$arr_company_tmp = array();
				if( !isset($arr_company_tmp[(string)$value['company_id']]) ){
					$arr_company = $model_company->select_one(array('_id' => $value['company_id']), array('_id', 'name','addresses','our_rep'));
					if(isset($arr_company['name'])){
						$arr_company_tmp[(string)$value['company_id']] = $arr_company['name'];
						echo $arr_company['name'];
					}
				}else{
					echo $arr_company_tmp[(string)$value['company_id']];
				}
			}
			?>
		</li>
		<li class="hg_padd right_txt" style="width:6% ">
			<?php
				$sum = 0;
				$current_date = strtotime(date("d-m-Y"));
				// nếu SA là company
				if (isset($value['company_id']) && is_object($value['company_id'])){
					$sum = $model_salesinvoice->sum('sum_amount','tb_salesinvoice',array(
					                                								'deleted'=>false,
					                                								'company_id'=>$value['company_id'],
					                                								'invoice_status' => array('$ne' => 'Cancel'),
					                                								'payment_due_date'=>array(
					                                								                           '$lte'=>new MongoDate($current_date - 30*DAY))
					                                								)
													);
					$total_sum += $sum;
					$sum_30 += $sum;
					$sum_balance += $total_sum;
					if($sum!=0)
						echo $this->Common->format_currency($sum);
				}
			?>
		</li>
		<li class="hg_padd right_txt" style="width:6%">
			<?php
				$sum = 0;
				$current_date = strtotime(date("d-m-Y"));
				// nếu SA là company
				if (isset($value['company_id']) && is_object($value['company_id'])){
					$sum = $model_salesinvoice->sum('sum_amount','tb_salesinvoice',array(
					                                							'deleted'=>false,
					                                							'company_id'=>$value['company_id'],
					                                							'invoice_status' => array('$ne' => 'Cancel'),
					                                							'payment_due_date'=>array('$gte'=>new MongoDate($current_date - 30*DAY)),
					                                							'payment_due_date'=>array('$lte'=> new MongoDate(60*DAY - $current_date))
					                                							)
												);
					$sum_60 += $sum;
					$total_sum += $sum;
					$sum_balance += $total_sum;
					if($sum!=0)
						echo $this->Common->format_currency($sum);
				}
			?>
		</li>
		<li class="hg_padd right_txt" style="width:6%">
			<?php
				$sum = 0;
				$current_date = strtotime(date("d-m-Y"));
				// nếu SA là company
				if (isset($value['company_id']) && is_object($value['company_id'])){
					$sum = $model_salesinvoice->sum('sum_amount','tb_salesinvoice',array(
					                                									'deleted'=>false,
					                                									'company_id'=>$value['company_id'],
					                                									'invoice_status' => array('$ne' => 'Cancel'),
					                                									'payment_due_date'=>array('$gte'=>new MongoDate($current_date - 60*DAY)),
					                                									'payment_due_date'=>array('$lte'=> new MongoDate(90*DAY - $current_date))
					                                									)
													);
					$total_sum += $sum;
					$sum_90 += $sum;
					$sum_balance += $total_sum;
					if($sum!=0)
						echo $this->Common->format_currency($sum);
				}
			?>
		</li>
		<li class="hg_padd right_txt" style="width:6%">
			<?php
				$sum = 0;
				$current_date = strtotime(date("d-m-Y"));
				// nếu SA là company
				if (isset($value['company_id']) && is_object($value['company_id'])){
					$sum = $model_salesinvoice->sum('sum_amount','tb_salesinvoice',array(
					                                								'deleted'=>false,
					                                								'company_id'=>$value['company_id'],
					                                								'invoice_status' => array('$ne' => 'Cancel'),
					                                								'payment_due_date'=>array('$gte'=>new MongoDate($current_date - 90*DAY))
					                                								)
													);
					$total_sum += $sum;
					$sum_other += $sum;
					$sum_balance += $total_sum;
					if($sum!=0)
						echo $this->Common->format_currency((float)$sum);
				}
			?>
		</li>
		<li class="hg_padd right_txt" style="width:6%">
			<?php
				if($total_sum>0)
					echo $this->Common->format_currency($total_sum);
			?>
		</li>
		<li class="hg_padd" style="width:6%">
			<?php
				if (isset($value['company_id']) && is_object($value['company_id'])){
					if(isset($arr_company['addresses'])){
						echo $arr_company['addresses']['0']['zip_postcode'];
					}
				}
			?>
		</li>
		<li class="hg_padd" style="width:8%">
			<?php
				if (isset($value['company_id']) && is_object($value['company_id'])){
					if(isset($arr_company['our_rep']))
					echo $arr_company['our_rep'];
				}
			?>
		</li>
		<li class="hg_padd" style="width:6%">
			<?php
			if(isset($value['status']))
				echo $value['status'];
			?>
		</li>
		<li class="hg_padd" style="width:1%">
			<div class="middle_check">
	            <a href="javascript:void(0)" title="Delete this" onclick="salesaccounts_lists_delete('<?php if(isset($value['_id']))echo $value['_id']; ?>')">
	                <span class="icon_remove2"></span>
	            </a>
             </div>
		</li>
	</ul>
<?php endforeach; ?>
<input type="hidden" id="sum_30" value="<?php echo $this->Common->format_currency($sum_30) ?>" />
<input type="hidden" id="sum_60" value="<?php echo $this->Common->format_currency($sum_60) ?>" />
<input type="hidden" id="sum_90" value="<?php echo $this->Common->format_currency($sum_90) ?>" />
<input type="hidden" id="sum_other" value="<?php echo $this->Common->format_currency($sum_other) ?>" />
<input type="hidden" id="sum_balance" value="<?php echo $this->Common->format_currency($sum_balance) ?>" />
<?php echo $this->element('popup/pagination_lists'); ?>