<span class="bt_block float_right no_bg" style=" width:90%; margin-right:3%">
    
    <div class="float_right" style=" width:5%; margin-left:1%;">
        <input class="input_7" type="text" style=" width:90%;text-align:right; padding:0 2% 0 0; margin:0 1% 0 0; float:right;" id="cost_per_unit" value="<?php if(isset($total)) echo $this->Common->format_currency($total,0); else echo '0';?>" readonly="readonly" />
    </div>
    <div class="float_right" style=" width:5%; text-align:right;">Totals</div>
    
    <div class="float_right" style=" width:5%; margin-left:1%;">
        <input class="input_7" type="text" style=" width:90%;text-align:right; padding:0 2% 0 0; margin:0 1% 0 0; float:right;" id="cost_per_unit" value="<?php if(isset($total_completed)) echo $this->Common->format_currency($total_completed,0); else echo '0';?>" readonly="readonly" />
    </div>
    <div class="float_right" style=" width:8%; text-align:right;">Totals Completed</div>

    <div class="float_right" style=" width:5%; margin-left:1%;">
        <input class="input_7" type="text" style=" width:90%;text-align:right; padding:0 2% 0 0; margin:0 1% 0 0; float:right;" id="cost_per_unit" value="<?php if(isset($total_not_completed)) echo $this->Common->format_currency($total_not_completed,0); else echo '0';?>" readonly="readonly" />
    </div>
    <div class="float_right" style=" width:8%; text-align:right;">Totals On SO's</div>

    <div class="float_right" style=" width:5%; margin-left:1%;">
        <input class="input_7" type="text" style=" width:90%;text-align:right; padding:0 2% 0 0; margin:0 1% 0 0; float:right;" id="cost_per_unit" value="<?php if(isset($total_cancel)) echo $this->Common->format_currency($total_cancel,0); else echo '0';?>" readonly="readonly" />
    </div>
    <div class="float_right" style=" width:8%; text-align:right;">Totals Cancel</div>
</span>
