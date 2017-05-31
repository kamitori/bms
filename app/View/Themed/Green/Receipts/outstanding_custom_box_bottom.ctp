<?php
	if(isset($total))
		$total = $this->Common->format_currency($total);
	else
		$total = 0;

	if(isset($balance))
		$balance = $this->Common->format_currency($balance);
	else
		$balance = 0;

	if(isset($receipts))
		$receipts = $this->Common->format_currency($receipts);
	else
		$receipts = 0;
?>


<div class="bt_block float_right no_bg" style="width:70%;">
	<div class="float_left" style="width:20%; text-align:right;">Totals</div>
    <div class="float_left" style="width:80%; margin-left:0%;">
            <input class="input_w2" type="text" style="float:right;text-align:right;width:30%; margin:0 0.5% 0 0;color:#444;" value="<?php echo $balance?>" readonly="readonly" id="total_balance" />
            <input class="input_w2" type="text" style="float:right;text-align:right;width:30%; margin:0 0.5% 0 0;color:#444;" value="<?php echo $receipts?>" readonly="readonly" id="total_receipt" />
            <input class="input_w2" type="text" style=" float:right;text-align:right;width:28%; margin:0 1% 0 0;color:#444;" value="<?php echo $total ?>" readonly="readonly" id="total_total" />
    </div>
</div>
