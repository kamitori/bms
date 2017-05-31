<div style="margin:0 8% 0 0; width:35%;padding:0; float:right;">
    <input class="input_w2" type="text" style=" width:20%; text-align:right;padding:0 1% 0 0; margin:-2px 1.5% 0 0%;float:right;color:#333; " id="total_balances" value="" readonly="readonly" />
    <input class="input_w2" type="text" style=" width:20%; text-align:right;padding:0 1% 0 0; margin:-2px 1.5% 0 0%;float:right;color:#333; " id="total_receipts" value="" readonly="readonly" />
    <input class="input_w2" type="text" style=" width:20%; text-align:right; padding:0 1% 0 0;margin:-2px 0.5% 0 0%;float:right;color:#333;" id="total_sum_amount" value="" readonly="readonly" />
    <div class="float_left" style=" width:20%;margin:0;padding:0; text-align:right;float:right;">
        Totals&nbsp;
    </div>
</div>
<script type="text/javascript">
	Number.prototype.formatMoney = function(c, d, t){
		var n = this,
	    c = isNaN(c = Math.abs(c)) ? 2 : c,
	    d = d == undefined ? "." : d,
	    t = t == undefined ? "," : t,
	    s = n < 0 ? "-" : "",
	    i = parseInt(n = Math.abs(+n || 0).toFixed(c)) + "",
	    j = (j = i.length) > 3 ? j % 3 : 0;
	   return s + (j ? i.substr(0, j) + t : "") + i.substr(j).replace(/(\d{3})(?=\d)/g, "$1" + t) + (c ? d + Math.abs(n - i).toFixed(c).slice(2) : "");
	 };
	var total_sum_amount = total_receipts = total_balances = 0;
	$(".price_sum_amount_lv,.price_total_receipt_lv,.price_balance_lv","#block_full_sales_invoice_this_company").each(function(){
		value = $(this).html();
		value = parseFloat(value.replace(/[,]/g,''));
		if($(this).hasClass("price_sum_amount_lv"))
			total_sum_amount += value;
		else if($(this).hasClass("price_total_receipt_lv"))
			total_receipts += value;
		else
			total_balances += value;
	});
	$("#total_sum_amount","#block_full_sales_invoice_this_company").val(total_sum_amount.formatMoney());
	$("#total_receipts","#block_full_sales_invoice_this_company").val(total_receipts.formatMoney());
	$("#total_balances","#block_full_sales_invoice_this_company").val(total_balances.formatMoney());
</script>