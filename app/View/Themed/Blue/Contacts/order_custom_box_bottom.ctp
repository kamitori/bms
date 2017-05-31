<?php
	$sum_sub_total = 0;
	$sum_tax = 0;
	$sum_amount = 0;
	//pr($arr_salesorder);die();
	foreach($arr_salesorder as $key=>$salesorder){
        $sum_sub_total      +=   $arr_salesorder[$key]['sum_sub_total'] ;
        $sum_amount     +=   $arr_salesorder[$key]['sum_amount'];
        $sum_tax        +=   $arr_salesorder[$key]['sum_tax'] ;
	}
	if($sum_sub_total>0){ $sum_sub_total = $this->Common->format_currency($sum_sub_total); }
	if($sum_tax>0){ $sum_tax = $this->Common->format_currency($sum_tax,3); }
	if($sum_amount>0){ $sum_amount = $this->Common->format_currency($sum_amount); }
?>



<div style="margin:0; width:40%;padding:0; float:right;">
    <input class="input_w2" type="text" style=" width:14%; text-align:right;padding:0 1% 0 0; margin:-2px 1.5% 0 0%;float:right;color:#333; " id="sum_amount" value="<?php echo $sum_amount;?>" readonly="readonly" />
    <div class="float_left" style=" width:17%;margin:0;padding:0; text-align:right;float:right;">
         Total Amount&nbsp;
    </div>

    <input class="input_w2" type="text" style=" width:14%;text-align:right;padding:0 1% 0 0;margin:-2px 0.5% 0 0%;float:right;color:#333;" id="sum_tax" value="<?php echo $sum_tax;?>" readonly="readonly" />
     <div class="float_left" style=" width:14%;margin:0;padding:0; text-align:right;float:right;">
          Total Tax&nbsp;
    </div>

    <input class="input_w2" type="text" style=" width:14%; text-align:right; padding:0 1% 0 0;margin:-2px 0.5% 0 0%;float:right;color:#333;" id="sum_sub_total" value="<?php echo $sum_sub_total;?>" readonly="readonly" />
     <div class="float_left" style=" width:20%;margin:0;padding:0; text-align:right;float:right;">
        &nbsp;
    </div>
</div>


