<?php
	if(isset($sum_sub_total))
		$sum_sub_total = $this->Common->format_currency($sum_sub_total);
	else
		$sum_sub_total = 0;

	if(isset($sum_tax))
		$sum_tax = $this->Common->format_currency($sum_tax);
	else
		$sum_tax = 0;

	if(isset($sum_amount))
		$sum_amount = $this->Common->format_currency($sum_amount);
	else
		$sum_amount = 0;
?>
<span class="bt_block float_right no_bg" style="margin:0; width:18%;padding:0;">
    <input class="input_7" type="text" style=" width:27%; text-align:right;padding:0 3% 0 0; margin:0 2% 0 2%; " id="sum_tax" value="<?php echo $sum_tax;?>" readonly="readonly" />
    <input class="input_7" type="text" style=" width:41%;text-align:right;padding:0 3% 0 0; margin:0%;" id="sum_amount" value="<?php echo $sum_amount;?>" readonly="readonly" />
</span>
<span class="bt_block float_right no_bg" style=" width:20%;">
    <div class="float_left" style=" width:30%; text-align:right;">
        Totals
    </div>
    <div class="float_right" style=" width:60%;">
	    <?php if(IS_LOCAL){ ?>
	    <div style="width:25%; float:left;" >
		    <input class="input_select" id="currency" combobox_blank="1" type="text" readonly="readonly" name="currency" value="<?php echo $option_currency[$currency] ?>">
		    <input id="currencyId" type="hidden" name="currency" value="<?php echo $currency ?>">
		    <script type="text/javascript">
		    $(function(){
		    	$("#currency").combobox(<?php echo json_encode($option_currency); ?>);
		    	$("#currency").change(function(){
		    		var value = $("#currencyId").val();
		    		$.ajax({
		    			url : "<?php echo URL.'/salesinvoices/change_total_currency' ?>",
		    			type: "POST",
		    			data: {currency : value},
		    			success: function(result){
		    				result = $.parseJSON(result);
		    				for(i in result) {
		    					$("#"+i).val(FortmatPrice(result[i]));
		    				}
		    				reload_subtab('line_entry');
		    			}
		    		})
		    	});
		    })
		    </script>
		</div>
	    <?php } ?>
        <input class="input_7" type="text" style=" width:64%; text-align:right; padding:0 3% 0 0; margin:0 0 0 0; float:right;" id="sum_sub_total" value="<?php echo $sum_sub_total;?>" readonly="readonly" />
    </div>
</span>
