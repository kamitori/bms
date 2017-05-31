<?php
?>
<span class="bt_block float_right no_bg" style=" width:50%; margin-right:0%">
    <div class="float_left" style=" width:42%; text-align:right;">
        <!--Totals -->&nbsp;
    </div>
    <div class="float_left" style=" width:57%; margin-left:1%;">

        <input class="input_7" type="text" style=" width:18%;text-align:right; padding:0 1.5% 0 0; margin:0 8.5% 0 0; float:right;" id="sum_amount" value="<?php if(isset($sum_amount)) echo $this->Common->format_currency($sum_amount);?>" readonly="readonly" />

        <input class="input_7" type="text" style=" width:12%;text-align:right; padding:0 1.5% 0 0; margin:0 0.5% 0 0; float:right;" id="sum_tax" value="<?php if(isset($sum_tax)) echo $this->Common->format_currency($sum_tax,3);?>" readonly="readonly" />
        <input class="input_7" type="text" style=" width:15%;text-align:right; padding:0 1.5% 0 0; margin:0 0.5% 0 0; float:right;" id="sum_sub_total" value="<?php if(isset($sum_sub_total)) echo $this->Common->format_currency($sum_sub_total);?>" readonly="readonly" />
        <!--<input class="input_7" type="text" style=" width:14.5%;text-align:right; padding:0 1.5% 0 0; margin:0 0.5% 0 0; float:right;" id="cost_per_unit" value="<?php if(isset($sum_quantity_received)) echo $this->Common->format_currency($sum_quantity_received,0);?>" readonly="readonly" />
        <input class="input_7" type="text" style=" width:15.5%;text-align:right; padding:0 1.5% 0 0; margin:0 0.5% 0 0; float:right;" id="cost_per_unit" value="<?php if(isset($sum_quantity)) echo $this->Common->format_currency($sum_quantity,0);?>" readonly="readonly" />-->
        <div style=" width:30%; text-align:right; float:right;">
            Totals &nbsp;
        </div>

    </div>
</span>
<div id="shipping_receive_quantity" style="display:none">

</div>