
<?php
$total_amount=isset($sum_amount)?(float)$sum_amount:0;
$amount=$total_amount;
$total_in = isset($total_invoice)?(float)$total_invoice:0;
$amount_in = $total_in;


$difference=$amount-$amount_in;

?>

<span class="bt_block float_right no_bg" style=" width:27%; padding-left:0px;">
    <div class="float_left" style=" width:45%; text-align:right;">
        Difference
    </div>
    <div class="float_right" style=" width:55%;">
        <input class="input_7" type="text" style=" width:76%; text-align:right; padding:0 3% 0 0; margin:0 0 0 0; float:right;"  value="<?php if(isset($difference)) echo $this->Common->format_currency($difference); else echo 0;?>" readonly="readonly" />
    </div>
</span>


<span class="bt_block float_right no_bg" style=" width:27%;padding-left:0px;">
    <div class="float_left" style=" width:45%; text-align:right;">
        Total order
    </div>
    <div class="float_right" style=" width:55%;">
        <input class="input_7" type="text" style=" width:76%; text-align:right; padding:0 3% 0 0; margin:0 0 0 0; float:right;"  value="<?php if(isset($amount)) echo $this->Common->format_currency($amount); else echo 0;?>" readonly="readonly" />
    </div>
</span>

<span class="bt_block float_right no_bg" style=" width:27%;padding-left:0px;">
    <div class="float_left" style=" width:45%; text-align:right;">
        Total invoices
    </div>
    <div class="float_right" style=" width:55%;">
        <input class="input_7" type="text" style=" width:76%; text-align:right; padding:0 3% 0 0; margin:0 0 0 0; float:right;"  value="<?php if(isset($amount_in)) echo $this->Common->format_currency($amount_in); else echo 0;?>" readonly="readonly" />
    </div>
</span>