
<span class="bt_block float_right no_bg" style=" width:50%; margin-right:12%">
    <div class="float_left" style=" width:87%; text-align:right;">
        Average cost per <?php if(isset($cost_per_unit))echo $cost_per_unit;?>&nbsp;&nbsp;
    </div>
    <div class="float_left" style=" width:12%; margin-left:1%;">
        <input class="input_7" type="text" style=" width:95%;text-align:right; padding:0 3% 0 0; margin:0 2% 0 0; float:right;" id="cost_per_unit" value="<?php if(isset($v_average_plus)) echo $this->Common->format_currency($v_average_plus,3)?>" readonly="readonly" />
    </div>
</span>