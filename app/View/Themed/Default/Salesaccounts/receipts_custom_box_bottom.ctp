<?php
	$total_amount_received = 0;
	$total_allocated = 0;
	$total_unllocated = 0;
	if(!empty($arr_receipt))
	foreach($arr_receipt as $key => $value){
		$total_amount_received += $this->Common->format_currency($value['amount_received']);
		$total_allocated  += $this->Common->format_currency($value['total_allocated']);
		$total_unllocated += $this->Common->format_currency($value['unallocated']);
	}
?>

<div style="margin:0 5% 0 0; width:67%;padding:0; float:right;">
    <input class="input_w2" type="text" style=" width:14.5%; text-align:right;padding:0 1% 0 0; margin:-2px 1.5% 0 0%;float:right;color:#333; " id="sum_amount"  readonly="readonly" value="<?php echo $this->Common->format_currency($total_unllocated); ?>"  />
    <input class="input_w2" type="text" style=" width:15.5%;text-align:right;padding:0 1% 0 0;margin:-2px 0.5% 0 0%;float:right;color:#333;" id="sum_tax" readonly="readonly" value="<?php echo $this->Common->format_currency($total_allocated); ?>"  />
    <input class="input_w2" type="text" style=" width:16%; text-align:right; padding:0 1% 0 0;margin:-2px 0.5% 0 0%;float:right;color:#333;" id="sum_sub_total"  readonly="readonly" value="<?php echo $this->Common->format_currency($total_amount_received);?>" />
     <div class="float_left" style=" width:20%;margin:0;padding:0; text-align:right;float:right;">
        Totals&nbsp;
    </div>
</div>