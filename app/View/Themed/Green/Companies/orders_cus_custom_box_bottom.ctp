<div style="margin:0 2% 0 0; width:35%;padding:0; float:right;">
    <input class="input_w2" type="text" style=" width:10%; text-align:right;padding:0 1% 0 0; margin:-2px 1.5% 0 0%;float:right;color:#333; " id="total_salesorders" value="" readonly="readonly" />

    <div class="float_left" style=" width:30%;margin:0;padding:0; text-align:right;float:right;">
         Total sales order&nbsp;
    </div>
    <input class="input_w2" type="text" style=" width:20%; text-align:right; padding:0 1% 0 0;margin:-2px 0.5% 0 0%;float:right;color:#333;" id="sum_sub_total" value="" readonly="readonly" />
    <div class="float_left" style=" width:20%;margin:0;padding:0; text-align:right;float:right;">
        Sub Total&nbsp;
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
	var sum_sub_total = i = 0;
	$(".price_sum_sub_total_lv","#block_full_orders_cus").each(function(){
		value = $(this).html();
		value = parseFloat(value.replace(/[,]/g,''));
		sum_sub_total += value;

		i++;
	});
	$("#sum_sub_total","#block_full_orders_cus").val(sum_sub_total.formatMoney());
	$("#total_salesorders","#block_full_orders_cus").val(i);
</script>