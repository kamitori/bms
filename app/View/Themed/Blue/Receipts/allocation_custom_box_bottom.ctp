<?php
	if(isset($total_allocated))
		$total_allocated = $this->Common->format_currency($total_allocated);
	else
		$total_allocated = '0.00';

	if(isset($amount_allocated)){
		$unallocated = (float)str_replace(",","",$amount_allocated) - (float)str_replace(",","",$total_allocated);
		$unallocated = $this->Common->format_currency($unallocated);
	}else
		$unallocated = '0.00';

?>
<span class="bt_block float_right no_bg" style=" width:98%;">
    <div class="dent_input float_right" style=" width:23%;margin-left:1px;">
        <input class="input_7" type="text" style=" width:98%; height: 17px;text-align:right; padding-right:2%;" id="total_allocated" value="<?php echo $total_allocated;?>" readonly="readonly" />
    </div>
    <div class="dent_input float_right" style=" width:19%; margin-right:0px;text-align:right;">
        Total allocated
    </div>

    <div class="dent_input float_right" style=" width:20%;margin-left:1px;">
        <input class="input_7" type="text" style=" width:98%;text-align:right; height:17px; padding-right:2%;" id="unallocated" value="<?php echo $unallocated;?>" readonly="readonly" />
    </div>
    <div class="dent_input float_right" style=" width:15%;margin-right:0px; text-align:right;">
        Unallocated
    </div>
</span>
