<?php
	foreach($arr_settings['relationship'][$sub_tab]['block'] as $key => $arr_val){

		echo $this->element('box',array('key'=>$key,'arr_val'=>$arr_val));

	}
?>
<p class="clear"></p>
<input type="hidden" id="is_add" value="0" />
<?php echo $this->element('js/line'); ?>
<script>
$(document).ready(function() {
	$(".viewprice_custom_unit_price").focus(function(){
		$(this).attr("rel",$(this).val());
	});
	var storeExtraRow = $("#listbox_products_Extra_Row").html();
	//fix combobox hide by div
	fixHiddenCombobox();
	//end fix
	var is_add = $("#is_add").val();
	if(is_add){
		var ids = $(".choice_sku:last","#container_products").attr("id");
		if(ids!=undefined){
			ids  = ids.split("_");
			var ind = ids.length;
			var ids = ids[ind-1];
			if($("#products_name_"+ids).offset()!=undefined){
				$("#container_products").mCustomScrollbar("scrollTo",$("#products_name_"+ids).offset().top);
				$("#products_name_"+ids).focus();
			}
		}
		$("#is_add").val(0);
	}
	//tạo thêm 1 products line mới
	$("#bt_add_products").click(function() {
		var taxper = $('#tax').val();
		taxper = taxper.split("%");
		taxper = taxper[0];
		var datas = {
			'products_id' : '',
			'products_name' : 'This is new record. Click for edit',
			'quantity' : 1,
			'sku':'',
			'adj_qty' : 0,
			'sizew_unit' : 'in',
			'sizeh_unit' : 'in',
			'sell_by' : 'area',
			'oum' : 'Sq.ft.',
			'taxper' : taxper,
		};
		$("#is_add").val(1);
		save_option('products',datas,'',1,'line_entry','add');
	});
	$(".details").kendoTooltip({
		autoHide: false,
    	showOn: "click",
    	position: "right",
    	width: 600,
      	height: 250,
    	content: function(e) {
          	var Object = e.target;
			var id = Object.attr('id');
			var value = $("#div_"+id).html();
			var html = '<textarea class="details_store" id="textarea_'+id+'">'+value+'</textarea>';
			return html;
        }
	});
	$("body").delegate(".details_store","change",function(){
		var ids  = $(this).attr("id").split("_");
		var index = ids.length;
		var ids = ids[index-1];
		var value = $(this).val();
		var data = {"details":value};

		$("#div_"+$(this).attr("id")).html(value);
		save_option('products',data,ids,0,'line_entry','update');
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
	$(".icon_emp4").click(function(){
		var ids = $('#mongo_id').val();
		window.location.assign("<?php echo URL;?>/quotations/rfqs_list/"+ids);
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
		if(names=='unit_price'){
			names='custom_unit_price';
			values = new Object();
			values[names]=inval;
		}
		//custom unit price
		if(names=='custom_unit_price'){
			<?php if(!$this->Common->check_permission($controller.'_@_custom_unit_price_@_add',$arr_permission)): ?>
			var unit_price = parseFloat($("#txt_unit_price_"+ids).html());
			if(inval<unit_price){
				alerts("Messsage","You do not have permission to change this value.");
				var old_value = $(this).attr('rel');
				if(old_value==undefined)
					old_value = "";
				$(this).val(old_value);
				return false;
			}
			values['is_custom_unit_price'] = 1;
			<?php endif; ?>
		}
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
		cal_line_entry(values,names,function(result){
			result = $.parseJSON(result);
			//=====================================================
			var sum = result.sum;
			//EXTRA ROW============================================
			if(result.last_insert!=undefined){
				$("#listbox_products_Extra_Row").html(storeExtraRow);
				$("#txt_sub_total_Extra_Row").html(FortmatPrice(result.last_insert['sub_total']));
				$("#txt_tax_Extra_Row").html(result.last_insert['tax'].formatMoney(3, '.', ','));
				$("#txt_amount_Extra_Row").html(FortmatPrice(result.last_insert['amount']));
			} else if(result.last_insert==undefined){
				$("#listbox_products_Extra_Row").html("");
			}
			//SUM==================================================
			$('#sum_sub_total').val(FortmatPrice(sum.sum_sub_total));
			$('#sum_tax').val(FortmatPrice(sum.sum_tax));
			$('#sum_amount').val(FortmatPrice(sum.sum_amount));
			//SELF==================================================
			var self = result.self;
			var textval = '';
			for(var i in self){
				if(i=='sizeh' && names!='sizeh'){
					continue;
				}else if(i=='sell_price' && names=='sell_by'){
					continue;
				}else if(i=='products_name'){
					txtval = self[i];
					if(self[i]!=''){
						txtval = txtval.split("\n");
						txtval = txtval[0];
					}
					$('#'+i+'_'+ids).val(txtval);
				}else if($('#'+i+'_'+ids).parent().attr('class')=='combobox'){
					$('#'+i+'_'+ids+'Id').val(self[i]);
				}else if(i=='unit_price'){
					txtval = parseFloat(self[i]);
					txtval = txtval.formatMoney(3, '.', ',');
					$('#'+i+'_'+ids).val(txtval);
					$('#txt_'+i+'_'+ids).html(txtval);
				}else if(i=='custom_unit_price'){
					txtval = parseFloat(self[i]);
					txtval = txtval.formatMoney(3, '.', ',');
					$('#'+i+'_'+ids).val(txtval);
					$('#txt_'+i+'_'+ids).html(txtval);
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
			//PARENT=================================================
			// if(result.parent!=undefined){
			// 	var parent = result.parent;
			// 	console.log(parent_ret);
			// 	p_ids = parent['ids'];
			// 	for(i in parent){
			// 		names = 'sizeh';
			// 		if(i=='unit_price' || i=='tax'){
			// 			txtval = parseFloat(parent[i]);
			// 			txtval = txtval.formatMoney(3, '.', ',');
			// 			$('#txt_'+i+'_'+p_ids).html(txtval);
			// 		}else if(i=='sub_total' || i=='amount' || i=='adj_qty'){
			// 			txtval = parseFloat(parent[i]);
			// 			txtval = txtval.formatMoney(2, '.', ',');
			// 			$('#txt_'+i+'_'+p_ids).html(txtval);
			// 		}else if(price_key.indexOf(i) != -1){
			// 			$('#'+i+'_'+p_ids).val(FortmatPrice(parent[i]));
			// 			if($('#txt_'+i+'_'+p_ids).prop("tagName")=='SPAN')
			// 				$('#txt_'+i+'_'+p_ids).html(FortmatPrice(parent[i]));
			// 		}else
			// 			$('#'+i+'_'+p_ids).val(parent[i]);
			// 	}
			// }
		});

	});
});
function after_choose_products(ids,names,keys){
	var origin_keys = keys; // BaoNam, dùng cho (1)
	$("#window_popup_products"+ origin_keys).data("kendoWindow").close();
	var keys = $("#product_choice_sku").val();
	var opid  = keys.split("_");
	var	index = opid.length;
		opid = opid[index-1];
		keys = keys.replace("_"+opid,"");
	if(keys=='change'){
		var values = new Object();
		var arr = {
					"code"		:"code",
					"sku"		:"sku",
					"option"	:"option",
					"sell_by"	:"sell_by",
					"oum"		:"oum",
					"sell_price":"sell_price",
					"unit_price":"unit_price",
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
		values['custom_unit_price'] = arr_data_from['unit_price'];
		//tax
		var taxx = $('#tax').val();
			taxx = taxx.split('%');
		values['products_id'] = ids;
		values['tax'] = taxx[0];
		values['products_name'] = arr_data_from.name; // names : BaoNam: không dùng name nữa vì bị lỗi charset
		console.log(values);
		save_option("products",values,opid,0,'line_entry','',function(opid){
			var newval = new Object();
			newval['id'] = parseInt(opid);
			console.log(newval);
			if(arr_data_from.product_type!='Custom Product')
				cal_line_entry(newval,'products_name',function(txt){
					reload_subtab('line_entry');
				});
			else{
				cal_line_entry(newval,'custom',function(txt){
					reload_subtab('line_entry');
				});
			}
		},'code');


	}
}
function cal_line_entry(data,fieldchange,callBack){
	$.ajax({
		url : "<?php echo URL.'/'.$controller;?>/cal_price_line",
		type:"POST",
		data: {data:JSON.stringify(data),fieldchange:fieldchange},
		success: function(result){
			if(typeof callBack == "function")
				callBack(result);
		}
	});
}
function change_uom_item(val,ids){
	var units = '';
	if(val=='unit')
		units = 'Unit';
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

</script>