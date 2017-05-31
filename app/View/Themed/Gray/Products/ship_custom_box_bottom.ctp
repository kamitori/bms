<?php

?>

<span class="bt_block float_right no_bg" style=" width:50%; margin-right:0%">
    <div class="float_left" style=" width:71%; text-align:right;">
        Totals (not inc. canelled) <?php if(isset($cost_per_unit))echo $cost_per_unit;?>&nbsp;
    </div>
    <div class="float_left" style=" width:28%; margin-left:1%;">
        <input class="input_7" type="text" style=" width:39%;text-align:right; padding:0 3% 0 0; margin:0 5% 0 0; float:right;" id="cost_per_unit" value="<?php if(isset($total_all['quantity_out'])) echo $this->Common->format_currency($total_all['quantity_out'],0)?>" readonly="readonly" />
        <input class="input_7" type="text" style=" width:39%;text-align:right; padding:0 3% 0 0; margin:0 1% 0 0; float:right;" id="cost_per_unit" value="<?php if(isset($total_all['quantity_in'])) echo $this->Common->format_currency($total_all['quantity_in'],0)?>" readonly="readonly" />
    </div>
</span>
