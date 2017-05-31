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
		var taxper = $('#tax').val();
		taxper = taxper.split("%");
		taxper = taxper[0];
		var datas = {
			'products_id' : '',
			'products_name' : 'This is new record. Click for edit',
			'sizew_unit' : 'in',
			'sizeh_unit' : 'in',
			'sell_by' : 'area',
			'oum' : 'Sq.ft.',
			'taxper' : taxper,
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
		//window_popup('products', 'Specify Purchase items','change_'+ids, 'choice_code_'+ids, get_where_com_po(),'force_re_install');
	});

	$('.rowedit input').change(function(){
		//nhan id
		var isreload=0;
		var names = $(this).attr("name");
		var intext = 'box_test_'+names;
		var inval = $(this).val();
		var ids  = names.split("_");
		var index = ids.length;
		var ids = ids[index-1];
		var price_key = new Array("sizew","sizeh","sell_price","area","unit_price","sub_total","taxper","tax","amount","custom_unit_price");
		var pricetext_key = new Array("unit_price","sub_total","tax","amount","adj_qty");
		//khoi tao gia tri luu
		names = names.replace("_"+ids,"");
		names = names.replace("cb_","");
		if(names=='sizew' || names=='sizeh' || names=='sell_price' || names=='area' || names=='sub_total' || names=='taxper' || names=='tax' || names=='amount' || names=='unit_price' || names=='adj_qty' || names=='custom_unit_price')
			inval = UnFortmatPrice(inval);
		var values = new Object();
			values[names]=inval;
		if(names == "products_name"){
			save_option('products',values,ids);
			return false;
		}
		//format price
		if(price_key.indexOf(names) != -1){
			$('#'+names+'_'+ids).val(FortmatPrice(inval));
		}else
			$('#'+names+'_'+ids).val(inval);
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
		purchaseorder_cal_price(values,function(result){

			//=====================================================
			var sum = result.sum;
			//EXTRA ROW============================================
			//SUM==================================================
			$('#sum_sub_total').val(FortmatPrice(sum.sum_sub_total));
			$('#sum_tax').val(sum.sum_tax.formatMoney(3, '.', ','));
			$('#sum_amount').val(FortmatPrice(sum.sum_amount));
			//SELF==================================================
			var self = result.self;
			var textval = '';
			for(var i in self){
				if(i=='sizeh' && names!='sizeh'){
					continue;
				}else if(i=='sell_price' && names=='sell_by'){
					continue;
				}else if($('#'+i+'_'+ids).parent().attr('class')=='combobox'){
					$('#'+i+'_'+ids+'Id').val(self[i]);
				}else if(i=='tax'){
					txtval = parseFloat(self[i]);
					txtval = txtval.formatMoney(3, '.', ',');
					$('#txt_'+i+'_'+ids).html(txtval);
				}else if(i=='sub_total' || i=='amount' || i=='adj_qty'){
					txtval = parseFloat(self[i]);
					txtval = txtval.formatMoney(2, '.', ',');
					$('#txt_'+i+'_'+ids).html(txtval);
				}else if(price_key.indexOf(i) != -1){
					$('#'+i+'_'+ids).val(FortmatPrice(self[i]));
					if($('#txt_'+i+'_'+ids).prop("tagName")=='SPAN')
						$('#txt_'+i+'_'+ids).html(FortmatPrice(self[i]));
				}else
					$('#'+i+'_'+ids).val(self[i]);
			}
			if (result.confirm != undefined && self.sell_price) {
				confirms('Message', result.confirm,
					function() {
						$.ajax({
							url: '<?php echo URL.'/products/save_data' ?>',
							type: 'POST',
							data: {
								'ids': self.products_id['$id'],
								'field': 'sell_price',
								'value': self.sell_price
							},
							success: function() {

							}
						});
					}, function() {
						return false;
					});
			}
		});

	});

	$(".viewprice_quantity_received, .viewprice_quantity_returned").focus(function(){
		var value = $(this).val();
		if(value == undefined || value == "")
			value = 0;
		$(this).attr("data-old-value",value);
	});

	$(".viewprice_quantity_received, .viewprice_quantity_returned").unbind("change").change(function(){
		var value = $(this).val();
		var name = $(this).attr("name");
		var id = name.split("_");
		id = id[id.length - 1];
		name = name.replace("_"+id,"");
		$.ajax({
			url : "<?php echo URL.'/purchaseorders/receive_return/' ?>",
			type : "POST",
			data : {id : id, name : name, value : value},
			success : function(result){
				result = $.parseJSON(result);
				for(var i in result){
					if(i.indexOf('sum_')!= -1){
						$("#"+i).val(result[i]);
						continue;
					}
					if($('#txt_'+i+"_"+id).prop("tagName")=='SPAN')
						$('#txt_'+i+"_"+id).text(result[i]);
					else
						$("#"+i+"_"+id).val(result[i]);
				}
			}
		})
	});

});

/**
/* Tính lại tổng trong database
/*
**/
function update_sum(handleData){
	//định nghĩa lại field trong database để tính
	var keyfield = {
			"sub_total"		: "sub_total",
			"tax"			: "tax",
			"amount"		: "amount",
			"sum_sub_total"	: "sum_sub_total",
			"sum_tax"		: "sum_tax",
			"sum_amount"	: "sum_amount"
		};
	var keyfield = JSON.stringify(keyfield);
	$.ajax({
		url: '<?php echo URL.'/'.$controller;?>/update_sum',
		type:"POST",
		dataType: "json",
		data: {subdoc:'products',keyfield:keyfield},
		success: function(ret){
			console.log(ret);
			$('#sum_sub_total').val(FortmatPrice(sum_ret['sum_sub_total']));
			$('#sum_tax').val(FortmatPrice(sum_ret['sum_tax']));
			$('#sum_amount').val(FortmatPrice(sum_ret['sum_amount']));
			if(handleData!=undefined)
				handleData(ret);
		}
	});
}



/**
/* Tính lại giá cho 1 line
/* Use: values là mảng data cần thay đổi
**/
function purchaseorder_cal_price(values,handleData){
	var jsonString = JSON.stringify(values);
	$.ajax({
		url: '<?php echo URL.'/'.$controller;?>/purchaseorder_cal_price',
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



function after_choose_products(ids,names,keys, stt){ // stt dùng cho trường họp riêng của popup sẽ thấy Số Thứ Tự
	var datas = JSON.parse($("#after_choose_products"+ keys + ids).val()); //(1)
	$("#window_popup_products"+ keys).data("kendoWindow").close();
	var opid  = keys.split("_");
	var	index = opid.length;
		opid = opid[index-1];
		keys = keys.replace("_"+opid,"");
	if(keys=='change'){
		var values = {
					"products_id":ids,
					"code"		:datas.code,
					"products_name"	:datas.name,
					"sku"		:datas.sku,
					"sell_by"	:datas.sell_by, // BaoNam: ẩn đi vì đã gán vào chỗ (1) ở for bên dưới
					"sell_price":datas.sell_price,
					"sizeh"		:datas.sizeh,
					"sizeh_unit":datas.sizeh_unit,
					"sizew"		:datas.sizew,
					"sizew_unit":datas.sizew_unit,
					//"unit_price":datas.unit_price,
					"oum_depend":datas.oum_depend,
					"oum"		:datas.oum,
					//"markup"	:datas.markup",
					//"profit"	:datas.profit",
					"is_custom_size":datas.is_custom_size,
					"balance_received" : 0
				 };
		var taxx = $('#tax').val();
			taxx = taxx.split('%');
		values['products_id'] = ids;
		values['tax'] = taxx[0];

		save_option("products",values,opid,1,'line_entry','',function(opid){
			//$("#products_name_" + opid).trigger("change"); // //////////////////////////////// (2) /////////////////
			//$("#listbox_products_" + opid + " li:first").html('<span class="icon_emp"></span>');
			//$("#listbox_products_" + opid + " li:first").attr("onclick", " window.location.assign('<?php echo URL; ?>/products/entry/" + ids + "')");
			//reload_subtab('line_entry');
			//setTimeout("reload_subtab('line_entry')", 1800);
		});


	}
}

</script>