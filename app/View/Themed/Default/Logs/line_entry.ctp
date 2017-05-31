
<?php 
		foreach($arr_settings['relationship'][$sub_tab]['block'] as $key => $arr_val){
			
			echo $this->element('box',array('key'=>$key,'arr_val'=>$arr_val));
			
		}
?>
<p class="clear"></p>
<script>
$(document).ready(function() {
	//tạo thêm 1 products line mới
	$("#bt_add_products").click(function() {
		var datas = {
			'products_id' : '', 
			'products_name' : 'This is new record. Click for edit'
		};
		save_option('products',datas,'',1,'line_entry','add');
	});
		
	$(".del_products").focusin(function(){
		ajax_note_set("");
		var ids = $(this).attr("id");
		ids  = ids.split("_");
		var ind = ids.length;
		var idfield =  parseInt(ids[ind-1]);
		ajax_note_set(" Press ENTER to delete the line:"+(idfield+1));
	});
	
	$(".del_products").focusout(function(){
		ajax_note("");
		var ids = $(this).attr("id");
			ids = ids.split("_");
		var index = ids.length;
		ids  = parseInt(ids[index-1])+1;
		$(".jt_line_over").removeClass('jt_line_over');
		$("#listbox_products_"+ids).addClass('jt_line_over');
	});
	
	$(".choice_code").click(function(){
		var ids = $(this).attr("id");
		var key_click_open = ids;
		ids  = ids.split("_");
		var ind = ids.length;
		var ids = ids[ind-1];
		ajax_note(" Change one product ");
	});
	
	$(".rowedit input,.viewcheck_omit").change(function(){
		//nhan id
		var isreload=0;
		var names = $(this).attr("name");
		var intext = 'box_test_'+names;
		var inval = $(this).val();
		var ids  = names.split("_");
		var index = ids.length;
		var ids = ids[index-1];
		
		//khoi tao gia tri luu
		names = names.replace("_"+ids,"");
		names = names.replace("cb_","");
		if(names=='omit'){
			if ($(this).attr("checked") == "checked") 
				inval = 0;
			else
				inval = 1;
			isreload=1;
		}
		var values = new Object();
			values[names]=inval;
		
		//luu lai
		save_option("products",values,ids,isreload,'line_entry');
		var sum,tax,amounts,taxds;
		if(names=='sell_price' || names=='quantity'){
			if(names=='sell_price')
				sum = $('#quantity_'+ids).val();
			else if(names=='quantity')
				sum = $('#sell_price_'+ids).val();
				
			sum = parseFloat(sum)*parseFloat(inval);
			taxper = $('#txt_taxper_'+ids).html();
			tax = parseFloat(taxper)*0.01*sum;
			amounts = sum+parseFloat(tax);
			inval = FortmatPrice(inval);
			sum = FortmatPrice(sum);
			taxds = FortmatPrice(tax);
			amounts = FortmatPrice(amounts);
			$(this).val(inval);
			$('#txt_tax_'+ids).html(taxds);
			$('#txt_sub_total_'+ids).html(sum);
			$('#txt_amount_'+ids).html(amounts);
		
		}
	});
});


function after_choose_products(ids,names,keys){
	$(".k-window").fadeOut();
	var opid  = keys.split("_");
	var	index = opid.length;
		opid = opid[index-1];
		keys = keys.replace("_"+opid,"");
	if(keys=='change'){
		
		var values = new Object();
		var arr = { "_id"		:"products_id",
					"code"		:"code",
					"option"	:"option",
					"oum"		:"oum",
					"sell_price":"sell_price",
					"markup"	:"markup",
					"profit"	:"profit",
					"gst_tax"	:"gst_tax",
					"pst_tax"	:"pst_tax",
				 };
			get_data_form_module('Product',ids,arr,function(arr_data_from){
				values = arr_data_from;
				for(var m in arr){
					if(m!='gst_tax' && m!='_id'){
						keyss = arr[m]; 
						values[keyss] = arr_data_from[m];
					}
				}
				values['products_id'] = ids;
				values['products_name'] = names;
				values['taxper'] = parseFloat(arr_data_from['gst_tax']);
				save_option("products",values,opid,1,'line_entry');
			});
		
	}
}




	
</script>