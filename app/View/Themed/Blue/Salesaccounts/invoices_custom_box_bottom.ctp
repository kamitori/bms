<div style="margin:0 6% 0 0;padding:0; float:right;">
    <input class="input_w2" type="text" style=" width:17%; text-align:right;padding:0 1% 0 0; margin:-2px 1.5% 0 0%;float:right;color:#333; " id="sum_amount"  readonly="readonly" value="<?php echo $this->Common->format_currency($total['balance_invoiced']); ?>"  />
    <input class="input_w2" type="text" style=" width:17%;text-align:right;padding:0 1% 0 0;margin:-2px 0.5% 0 0%;float:right;color:#333;" id="sum_tax" readonly="readonly" value="<?php echo $this->Common->format_currency($total['total_receipt']); ?>"  />
    <input class="input_w2" type="text" style=" width:20%; text-align:right; padding:0 1% 0 0;margin:-2px 0.5% 0 0%;float:right;color:#333;" id="sum_sub_total"  readonly="readonly" value="<?php echo $this->Common->format_currency($total['sum_amount']); ?>" />
    <input class="input_w2" type="text" style=" width:28%; text-align:right;padding:0 1% 0 0; margin:-2px 0.5% 0 0%;float:right;color:#333; " id="sum_amount"  readonly="readonly" value="<?php echo $this->Common->format_currency($total['total_tax']); ?>"  />
     <div class="float_left" style="margin:0;padding:0; text-align:right;float:right;">
        Totals&nbsp;
    </div>
</div>

 <div style="margin:0 2% 0 0; width:50%;padding:0; float:left;">
 	<div class="icon_vwie indent_down_vwie2" style="float:left; display:block">
		<a href="">
			View
		</a>
	</div>
    <div style=" width:20%; float:left; margin-left: 10%">
    	<input class="input_w2" type="text" style=" width:40%; text-align:right;padding:0 1% 0 0; margin:-2px 1.5% 0 0%;float:right;color:#333; " id="sum_amount"  readonly="readonly" value="<?php echo $this->Common->format_currency($total['total_30']); ?>"  />
	     <div class="float_left" style=" width:76px; margin:0;padding:0; text-align:right;float:right; font-size: 12px;">
	        0 - 30 days &nbsp;
	    </div>
    </div>
    <div style=" width:20%; float:left;">
    	<input class="input_w2" type="text" style=" width:40%; text-align:right;padding:0 1% 0 0; margin:-2px 1.5% 0 0%;float:right;color:#333; " id="sum_amount"  readonly="readonly" value="<?php echo $this->Common->format_currency($total['total_60']); ?>"  />
	     <div class="float_left" style=" width:76px;margin:0;padding:0; text-align:right;float:right; font-size: 12px;">
	        31 - 60 days &nbsp;
	    </div>
    </div>
    <div style=" width:20%; float:left;">
    	<input class="input_w2" type="text" style=" width:40%; text-align:right;padding:0 1% 0 0; margin:-2px 1.5% 0 0%;float:right;color:#333; " id="sum_amount"  readonly="readonly" value="<?php echo $this->Common->format_currency($total['total_90']); ?>"  />
	     <div class="float_left" style=" width:76px;margin:0;padding:0; text-align:right;float:right; font-size: 12px;">
	        61 - 90 days &nbsp;
	    </div>
    </div>
    <div style=" width:20%; float:left;">
    	<input class="input_w2" type="text" style=" width:40%; text-align:right;padding:0 1% 0 0; margin:-2px 1.5% 0 0%;float:right;color:#333; " id="sum_amount"  readonly="readonly" value="<?php echo $this->Common->format_currency($total['total_90+']); ?>"  />
	     <div class="float_left" style=" width:76px;margin:0;padding:0; text-align:right;float:right; font-size: 12px;">
	        90+ days &nbsp;
	    </div>
    </div>
</div>