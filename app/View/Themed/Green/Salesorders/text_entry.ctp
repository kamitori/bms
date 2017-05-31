<?php
	foreach($arr_settings['relationship'][$sub_tab]['block'] as $key => $arr_val){
		echo $this->element('box',array('key'=>$key,'arr_val'=>$arr_val));
	}
?>
<p class="clear"></p>
<script>
$(document).ready(function() {
	//tạo thêm 1 products text entry mới
	$("#bt_add_products").click(function() {
		var taxper = $('#tax').val();
		taxper = taxper.split("%");
		taxper = taxper[0];
		var datas = {
			'products_id' : '',
			'products_name' : 'This is new record. Click for edit',
			'quantity' : 0,
			'adj_qty' : 0,
			'sizew_unit' : 'in',
			'sizeh_unit' : 'in',
			'sell_by' : 'area',
			'oum' : 'Sq.ft.',
			'taxper' : taxper,
		};
		save_option('products',datas,'',1,'text_entry','add');
	});

	$(".choice_code").click(function(){
		var ids = $(this).attr("id");
		var key_click_open = ids;
		ids  = ids.split("_");
		var ind = ids.length;
		var ids = ids[ind-1];
	});

	//$( document ).delegate("textarea","change",function(){
	$("textarea, .rowedit input").change(function(){
		//nhan id
		var isreload=0;
		var names = $(this).attr("name");
		var intext = 'box_test_'+names;
		var inval = $(this).val();
		var ids  = names.split("_");
		var index = ids.length;
		var ids = ids[index-1];
		var price_key = new Array("sizew","sizeh","sell_price","area","unit_price","sub_total","taxper","tax","amount");
		var pricetext_key = new Array("unit_price","sub_total","tax","amount");
		//khoi tao gia tri luu
		names = names.replace("_"+ids,"");
		names = names.replace("cb_","");
		if(names=='sizew' || names=='sizeh' || names=='sell_price' || names=='area' || names=='sub_total' || names=='taxper' || names=='tax' || names=='amount' || names=='unit_price')
			inval = UnFortmatPrice(inval);
		var values = new Object();
			values[names]=inval;

		//format price
		if(price_key.indexOf(names) != -1){
			$('#'+names+'_'+ids).val(FortmatPrice(inval));
		}else
			$('#'+names+'_'+ids).val(inval);

		//xử lý ký hiệu enter /n
		/*var tmprr;
		if(names=='products_name'){
			$('#'+names+'_'+ids).val(inval);
			tmprr = inval.split("\n");
			var newtrs = '';
			for(var m=0;m<tmprr.length;m++){
				newtrs +=tmprr[m]+'<br>';
			}
			values[names]=newtrs;
		}*/

		//if is select box
		if($('#'+names+'_'+ids).parent().attr('class')=='combobox'){
			values[names]=$('#'+names+'_'+ids+'Id').val();
		}
		//set default oum
		if(names=='sell_by'){
			var newval = $('#sell_by_'+ids+'Id').val();
			if(newval=='unit')
				values['oum'] = 'unit';//set default
			else
				values['oum'] = 'Sq.ft.';//set default
			change_uom_item(newval,ids);
		}

		values['id']=ids;
		cal_line_entry(values,function(ret){
			var i,tem,txtval;
			for(i in ret){
				if(i=='products_name' && names=='products_name'){
					$('#'+i+'_'+ids).val(inval);
				}else if($('#'+i+'_'+ids).parent().attr('class')=='combobox'){
					$('#'+i+'_'+ids+'Id').val(ret[i]);
				}else if(i=='unit_price' || i=='tax'){
					txtval = parseFloat(ret[i]);
					txtval = txtval.formatMoney(3, '.', ',');
					$('#txt_'+i+'_'+ids).html(txtval);
				}else if(i=='sub_total' || i=='amount'){
					txtval = parseFloat(ret[i]);
					txtval = txtval.formatMoney(2, '.', ',');
					$('#txt_'+i+'_'+ids).html(txtval);
				}else if(price_key.indexOf(i) != -1){
					$('#'+i+'_'+ids).val(FortmatPrice(ret[i]));
				}else
					$('#'+i+'_'+ids).val(ret[i]);
			}
			//hiển thị lại tổng
			$('#sum_sub_total').val(FortmatPrice(ret['sum_sub_total']));
			$('#sum_tax').val(FortmatPrice(ret['sum_tax']));
			$('#sum_amount').val(FortmatPrice(ret['sum_amount']));

		});

	});

});


/**
/* Tính lại giá cho 1 line
/* Use: values là mảng data cần thay đổi
**/
function cal_line_entry(values,handleData){
	var jsonString = JSON.stringify(values);
	$.ajax({
		url: '<?php echo URL.'/'.$controller;?>/ajax_cal_line',
		type:"POST",
		dataType: "json",
		data: {arr:values},
		success: function(ret){
			if(handleData!=undefined)
				handleData(ret);
		}
	});
}


/**
/* Change list box OUM (after Sell price)
/*
**/
function change_uom_item(val,ids){
	var units = '';
	if(val=='unit')
		units = 'unit';
	else
		units = 'Sq.ft.';
	var old_html = $("#box_edit_oum"+"_"+ids).html();
		old_html = old_html.split("<span");
		old_html = old_html[1].split(">");
		old_html = old_html[1];
		old_html = old_html.replace('value','value="'+units+'" title');
		$("#box_edit_oum"+"_"+ids+" .combobox").remove();
		$("#box_edit_oum"+"_"+ids).prepend(old_html+"  />");

	$.ajax({
		url: '<?php echo URL.'/';?>products/select_render',
		dataType: "json", //luu y neu dung json
		type:"POST",
		data: {sell_by:val},
		success: function(jsondata){
			$("#oum"+"_"+ids).combobox(jsondata);
		}
	});
}



function after_choose_products(ids,names,keys){
	var origin_keys = keys; // BaoNam, dùng cho (1)
	$(".k-window").fadeOut();
	var keys = $("#product_choice_sku").val();
	var opid  = keys.split("_");
	var	index = opid.length;
		opid = opid[index-1];
		keys = keys.replace("_"+opid,"");
	if(keys=='change'){
		var values = new Object();
		var arr = { "_id"		:"products_id",
					"code"		:"code",
					"option"	:"option",
					"sell_by"	:"sell_by",
					"oum"		:"oum",
					"sell_price":"sell_price",
					"markup"	:"markup",
					"profit"	:"profit",
					"sizeh"		:"sizeh",
					"sizeh_unit":"sizeh_unit",
					"sizew"		:"sizew",
					"sizew_unit":"sizew_unit",
					"is_custom_size":"is_custom_size"
				 };
		// BaoNam 13/11/2013
		// thay vì request về server thì lấy mảng json từ popup luôn cho nhanh hơn
		var arr_data_from = JSON.parse($("#after_choose_products"+ origin_keys + ids).val()); //(1)
		values = arr;
		for(var m in arr){
			if(m!='_id'){
				keyss = arr[m];
				values[keyss] = arr_data_from[m];
			}
		}
		//tax
		var taxx = $('#tax').val();
			taxx = taxx.split('%');
		values['products_id'] = ids;
		values['tax'] = taxx[0];
		values['products_name'] = arr_data_from.name; // names : BaoNam: không dùng name nữa vì bị lỗi charset
		save_option("products",values,opid,0,'line_entry','',function(opid){
			var newval = new Object();
			//newval = values;
			newval['id'] = parseInt(opid);
			newval['sell_price'] = arr_data_from.sell_price;

			cal_line_entry(newval,function(txt){
				reload_subtab('line_entry');
			});
		});


	}
}
</script>