<?php
	if(isset($total_stock)){
		$total_stock = $this->Common->format_currency($total_stock, 0);
	}
	else{
		$total_stock = 0;
	}
	if(isset($cost_price)){
		$cost_price = $this->Common->format_currency($cost_price);
	}
	else{
		$cost_price = 0;
	}
	if(isset($sell_price)){
		$sell_price = $this->Common->format_currency($sell_price);
	}
	else{
		$sell_price = 0;
	}
	if(isset($profit)){
		$profit = $this->Common->format_currency($profit);
	}
	else{
		$profit = 0;
	}
	if(isset($on_so)){
		$on_so = $this->Common->format_currency($on_so, 0);
	}
	else{
		$on_so = 0;
	}

?>
<!--<span class="bt_block float_right no_bg" style="width:100%;">
    <div class="float_left" style="width:75%; margin-left:22.5%;">
        <div class="dent_input float_right" style="width:100%;">
            <input class="input_w2" type="text" style="width:7.5%;color:#444;text-align:right" value="<?php echo $total_stock ?>" readonly="readonly"/>
            <input class="input_w2" type="text" style="width:7.5%; margin:0 0 0 0;color:#444;text-align:right" value="<?php echo $cost_price ?>" readonly="readonly" />
            <input class="input_w2" type="text" style="width:7.5%; margin:0 0 0 0;color:#444;text-align:right" value="<?php echo $sell_price ?>" readonly="readonly" />
            <input class="input_w2" type="text" style="width:7.5%; margin:0 0 0 0;color:#444;text-align:right" value="<?php echo $profit ?>" readonly="readonly" />
            <input class="input_w2" type="text" style="width:7.5%; margin:0 0 0 0;color:#444;text-align:right" value="<?php echo $on_so ?>" readonly="readonly" />
            <span class="float_left" style="width:12%;">Totals</span>
        </div>
    </div>
</span>-->
<span class="bt_block float_right no_bg" style=" width:80%; margin-right:0%">
    <div class="float_left" style=" width:53%; text-align:right;">
        Totals&nbsp;
    </div>
    <div class="float_left" style=" width:46%; margin-left:1%;">
        <input class="input_7" type="text" style=" width:15%;text-align:right; padding:0 1% 0 0; margin:0 79% 0 0; float:right;" id="cost_per_unit" value="<?php echo $total_stock ?>" readonly="readonly" />
    </div>
</span>
