<?php
	$total = 0;
	if(!empty($arr_allocation))
		foreach($arr_allocation as $key => $value){
			$total += $value['amount'];
		}
?>

<div style="margin:0 1% 0 0; width:98%;padding:0; float:right;">
    <input class="input_w2" type="text" style=" width:16%; text-align:right; padding:0 1% 0 0;margin:-2px 0.5% 0 0%;float:right;color:#333;" id="sum_sub_total"  readonly="readonly" value="<?php echo $this->Common->format_currency($total);?>" />
     <div class="float_left" style=" width:20%;margin:0;padding:0; text-align:right;float:right;">
        Totals&nbsp;
    </div>
</div>