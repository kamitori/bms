<?php
	if(isset($sum_sub_total)){
		$sum_sub_total = $this->Common->format_currency($sum_sub_total);
	}
	else
		$sum_sub_total = 0;

	if(isset($sum_tax))
		$sum_tax = $this->Common->format_currency($sum_tax,3);
	else
		$sum_tax = 0;

	if(isset($sum_amount))
		$sum_amount = $this->Common->format_currency($sum_amount);
	else
		$sum_amount = 0;
?>


<div style="margin:0 2% 0 0; width:35%;padding:0; float:right;">
    <input class="input_w2" type="text" style=" width:14.5%; text-align:right;padding:0 1% 0 0; margin:-2px 1.5% 0 0%;float:right;color:#333; " id="sum_amount" value="<?php echo $sum_amount;?>" readonly="readonly" />
    <input class="input_w2" type="text" style=" width:15.5%;text-align:right;padding:0 1% 0 0;margin:-2px 0.5% 0 0%;float:right;color:#333;" id="sum_tax" value="<?php echo $sum_tax;?>" readonly="readonly" />
    <input class="input_w2" type="text" style=" width:16%; text-align:right; padding:0 1% 0 0;margin:-2px 0.5% 0 0%;float:right;color:#333;" id="sum_sub_total" value="<?php echo $sum_sub_total;?>" readonly="readonly" />
     <div class="float_left" style=" width:20%;margin:0;padding:0; text-align:right;float:right;">
        Totals&nbsp;
    </div>
</div>


