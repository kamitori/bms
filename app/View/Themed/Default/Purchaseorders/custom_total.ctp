<?php
	if(isset($sum_sub_total))
		$sum_sub_total = $this->Common->format_currency($sum_sub_total);
	else
		$sum_sub_total = 0;

	if(isset($sum_tax))
		$sum_tax = $this->Common->format_currency($sum_tax);
	else
		$sum_tax = 0;

	if(isset($sum_amount))
		$sum_amount = $this->Common->format_currency($sum_amount);
	else
		$sum_amount = 0;
?>
<span class="bt_block float_right no_bg" style="margin-right:1%; width:17%">
    <input class="input_7" type="text" style=" width:45%; text-align:right" id="sum_tax" value="<?php echo $sum_tax;?>" />
    <input class="input_7" type="text" style=" width:45%;text-align:right" id="sum_amount" value="<?php echo $sum_amount;?>" />
</span>
<span class="bt_block float_right no_bg" style=" width:18%;">
    <div class="dent_input float_left" style=" width:7%;">
        Totals
    </div>
    <div class="dent_input float_right" style=" width:60%;">
        <input class="input_7" type="text" style=" width:75%;text-align:right" id="sum_sub_total" value="<?php echo $sum_sub_total;?>" />
    </div>
</span>
