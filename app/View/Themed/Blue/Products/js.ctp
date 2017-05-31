<?php echo $this->element('js_entry');?>
<?php echo $this->element('js/permission_product_modules');?>
<script type="text/javascript">
$(function(){

	if ($('#product_type').val() == 'OPTIONS' && !$('#product-option-type').length) {
		var optionType = <?php echo json_encode($option_type); ?>;
		var productOptionTypes = <?php echo json_encode($product_option_type); ?>;
		var optionSelect = '<select name="option_type" id="option_type" >'
							 + '<option value=""></option>';
		for(option in productOptionTypes) {
			var selected = '';
			if (optionType == option) {
				selected = 'selected';
			}
			optionSelect += '<option value="'+ option +'" '+ selected +'>'+ productOptionTypes[option] +'</option>';
		}
		optionSelect += '</select>';
		var html = [
					'<div id="product-option-type">',
						'<div class=" jt_box_label " style=" width:38%;">',
							'Type of options',
					    '</div>',
					    '<div class="jt_box_field " style=" width:58%;">',
					    	optionSelect,
						'</div>',
					'</div>',
				].join('\n');
		$('.jt_panel:last .jt_box:first .jt_box_line:eq(5)').html(html);

	}

	// load other categories
	var cate_more = <?php echo json_encode($cate_more); ?>;
	var categories = <?php echo json_encode($categories); ?>;
	// console.log(cate_more);
	var optionCategories = '<select name="cate_more" id="cate_more" multiple="multiple" style="width: 100%; position: relative; z-index:10">'
						 + '<option value=""></option>';
	for(option in categories) {
		selected = '';
		if ($.inArray(option, cate_more) != "-1") {
			selected = 'selected';
		}
		optionCategories += '<option value="'+ option +'" '+ selected +'>'+ categories[option] +'</option>';
	}
	optionCategories += '</select>';
	html = [
				'<div id="product-categories">',
					'<div class=" jt_box_label " style=" width:38%;">',
						'Other Categories',
				    '</div>',
				    '<div class="jt_box_field " style=" width:58%;">',
				    	optionCategories,
					'</div>',
				'</div>',
			].join('\n');
	$('.jt_panel:last .jt_box:first .jt_box_line:eq(6)').html(html);
	$('#cate_more').select2();
	//end load other categories	

	$("#sell_by").change(function(){
		var val;
		var fieldid = $(this).attr("id");
		var temp = $("#"+fieldid+'Id').val();
		if(temp!='')
			val = temp;
		else
			val = $(this).val();
		change_uom(val);
	});
	$("#discount").change(function(){
		var value = $(this).val();
		value = FortmatPrice(value);
		$("#rel_discount").val(value);
	})

	$("form select").focusin(function(){
		ajax_note(" (Alt + &darr;) to expand this select");
	});

	//cac filed price
	$(".jtprice").focusout(function(){
		if($(this).attr("id")=="rel_discount")
			return false;
		var ids = $(this).attr('id');
		ids = '#rel_'+ids;
		$(this).css("display","none");
		$(ids).css("display","block");
	});


	$(".rel_jtprice").focusin(function(){
		if($(this).attr("id")=="rel_discount")
			return false;
		var ids = $(this).attr('id');
		ids = ids.split("rel_");
		ids = ids[1];
		if(ids!='unit_price'){
			$(this).css("display","none");
			$("#"+ids).css("display","block");
			$("#"+ids).focus();
		}
	});
	$("#rel_sell_price,#rel_unit_price").css({'font-weight':'bold','color':'#000','font-size':'14px'});

	/*
	* Xu ly save, update
	*/
	$("form ").on('change', 'input, select', function() {

		if($(this).attr("id")=="products_upload")
			return false;
		var fieldname 	= $(this).attr("name");
		var fieldid 	= $(this).attr("id");
		var fieldtype 	= $(this).attr("type");
		var values 		= $(this).val();
		var valueid;
		var titles = new Array();

		//set value
		if(fieldtype=='checkbox'){
			if($(this).is(':checked'))
				values = 1;
			else
				values = 0;
		}

		//nếu là select box
		if($('#'+fieldname).parent().attr('class')=='combobox'){
			values = $("#"+fieldname+"Id").val();
			valuesid = $("#"+fieldname).val();
		}

		//set title
		if(fieldname=='product_type'){
			$('.title_top_ctrl h2').html(values);
		}

		/**
		* lưu dữ liệu và hiển thị
		*/
		$(".jt_ajax_note").html("Saving...       ");

		save_data(fieldname,values,'',valueid,function(ret){
			console.log(ret);
			// change tittle, thay đổi tiêu đề của items
			<?php foreach($arr_settings['title_field'] as $ks=>$vls){?>
				titles[<?php echo $ks;?>] = '<?php echo $vls;?>';
			<?php }?>
			if(titles.indexOf(fieldname)!=-1){
				$("#md_"+fieldname).html(values);
			}

			//neu la input loai gia
			if(fieldname=='sell_price' || fieldname=='cost_price' || fieldname=='thickness' || fieldname=='sizew' || fieldname=='sizeh' || fieldname=='unit_price'){
				var valuef = parseFloat(ret[fieldname]);
				valuef = FortmatPrice(values);
				$("#rel_"+fieldname).val(valuef);
				$("#"+fieldname).val(values);
			}


			if(fieldname=='product_type'){
				location.reload();
			}


			if(ret['unit_price']!=undefined && ret['unit_price']!=''){
				valuef = parseFloat(ret['unit_price']);
				$("#unit_price").val(valuef);
				valuef = valuef.formatMoney(3, '.', ',');
				$("#rel_unit_price").val(valuef);

			}
			//sell_price
			if(fieldname=='sell_price'){
				reload_subtab('pricing');
				/*checkid = $(".viewcheck_sell_default:checked").attr("id");
				if(checkid){
					checkid =checkid.split("_");
					var lengs = checkid.length;
					checkid = checkid[lengs-1];
					$("#sell_unit_price_"+checkid).val(values);
					var valuesf = FortmatPrice(values);
					$("#box_test_sell_unit_price_"+checkid).html(valuesf);

					var sell = new Object();
					sell['sell_unit_price'] = values;
					save_option('sellprices',sell,checkid,1,'pricing');
				}else{
					update_default('sellprices','sell_default','sell_unit_price',values,function(){
						reload_subtab('pricing');
					});

				}

				var pricingclass = $("#pricing").attr("class");
				if(pricingclass=='active'){
					$.ajax({
						url: '<?php//echo URL.'/'.$controller;?>/ajax_contents_box',
						type:"POST",
						data: {boxname:'pricingsummary',new_values:values},
						success: function(text_return){
							$("#editview_box_pricingsummary").parent().html(text_return);
						}
					});
				}*/


			//cost_price
			}else if(fieldname=='cost_price'){
				reload_subtab('pricing');
				// location.reload();


			//category
			}else if(fieldname=='category'){
				if(ret['status'] == 'error')
				{
					notifyTop(ret['message']);
					setTimeout(function(){
						location.reload(true);
					}, 1000);
				}
				else
				{
					$.ajax({
						url: '<?php echo URL.'/'.$controller;?>/ajax_contents_box',
						type:"POST",
						data: {boxname:'same_category',new_values:values},
						success: function(text_return){
							$("#container_same_category").parent().html(text_return);
						}
					});

				}
			}//end if


			if($('#'+fieldname).parent().attr('class')=='combobox'){
				$("#"+fieldname).val(valuesid);
			}

		});//end save_data



	});

	$(".jt_ajax_note").html('');


});





/*	========================================================================
	Các hàm của module Product:
		after_choose_companies(ids,names,keys);
		after_choose_contacts(ids,names,keys)
		reload_price()
		update_option(optionname,arr_value,opid,isreload)
		change_uom(val)
		save_uom()
*/





// xử lý sau khi chọn company
function after_choose_companies(ids,names,keys){

	if(keys=='company_name'){
		var arr_data_from = JSON.parse($("#after_choose_companies"+ keys + ids).val());
		var value = arr_data_from.name;
		save_data('company_name',value,'',ids,function(txt){
			//consoloe.log(txt);
			reload_subtab('purchasing');
		});

	}else if(keys=='change_company_name'){ //option company: change company name
		$("#window_popup_companies"  + keys).data("kendoWindow").close();
        var data_json = JSON.parse($("#after_choose_companies"+ keys + ids).val());
		var opid = $("#choicing_key").val();
        var data = {
            'company_id': ids,
            'company_name' : data_json.name,
        };

        save_option('supplier', data, opid, 1, 'purchasing', 'update',function(){
			if($("#cb_current_"+opid).is(':checked')){
				$("#company_name").val(data_json.name);
				$("#company_id").val(ids);
			}
			window_popup("products", "Specify vendor stock by seleted supplier", "changesupplier_"+opid, "click_open_window_change_sku_"+opid, "?lockproduct=1&products_product_type=Vendor Stock&products_company_id=" + ids + "&products_company_name=" + data_json.name,'force_re_install');
		});

	}else if(keys == 'supplier'){
		$("#window_popup_companies"+keys).data("kendoWindow").close();
        var data_json = JSON.parse($("#after_choose_companies"+ keys + ids).val());
        var data = {
            'company_id': ids,
            'company_name' : data_json.name,
			'sku': '',
            'name': '',
            'current': '',
			'sizew' : $("#sizew").val(),
            'sizew_unit': $("#sizew_unit").val(),
            'sizeh' : $("#sizeh").val(),
            'sizeh_unit': $("#sizeh_unit").val(),
            'sell_by': $("#sell_by").val(),
            'sell_price' : $("#sell_price").val()
        };
        save_option('supplier', data, '', 1, 'purchasing', 'add');



	}else if(keys == 'search_company_name'){
		$("#window_popup_companies"+keys).data("kendoWindow").close();
        var data_json = JSON.parse($("#after_choose_companies"+ keys + ids).val());
		$(".products_company_name").val(data_json.name);
		$("#products_submit_choice_code_option").click();
		$("#products_submit_choice_code").click();

	} else if(keys == 'search_prefer_customer'){
		$("#window_popup_companies"+keys).data("kendoWindow").close();
        var data_json = JSON.parse($("#after_choose_companies"+ keys + ids).val());
		$("[name=products_prefer_customer]").val(data_json.name);
		$("#products_submit_choice_code_option").click();
		$("#products_submit_choice_code").click();
	} else if(keys == 'prefer_customer') {
		var arr_data_from = JSON.parse($("#after_choose_companies"+ keys + ids).val());
		var value = arr_data_from.name;
		save_data('prefer_customer',value,'',ids);
	}


}

function check_supplier_empty(){
	var id = $('.supplier_com_id').val();
	if(id==undefined)
		return true;
	else
		return false;
}

function after_choose_contacts(ids,names,keys){
	if(keys=='assign'){
		var rs = save_field('assign',names,'');
		save_field('assign_id',ids,'');
		$("#assign_id").val(ids);
		$("#assign").val(names);
	}
	if(keys=='update_price_by'){
		//$("#link_to_contacts").attr("href", "/jobtraq/contacts/entry/" + contact_id);
		$("#otherpricing_update_price_by_id").val(ids);
		$("#otherpricing_update_price_by").val(names);
		save_field('update_price_by_id',ids,'');
		save_field('update_price_by',names,'');
	}
}


function reload_price(){
	var module_id = $("#mongo_id").val();
	var field_list = 'markup,profit';
	$.ajax({
		url: '<?php echo URL.'/'.$controller;?>/reload_field',
		type:"POST",
		data: {field_list:field_list,module_id:module_id},
		success: function(returnvl){
			if(returnvl!=''){
				returnvl = returnvl.split("@");
				temp = parseFloat(returnvl[0])*100;
				temp = temp.formatMoney(2, '.', ',');
				$("#rel_markup").val(temp+' %');
				$("#markup").val(returnvl[0]);
				CheckNegative(returnvl[0],'#rel_markup');

				temp = FortmatPrice(returnvl[1]);
				$(".jt_profit").val('$'+temp);
				$("#profit").val(returnvl[1]);
				CheckNegative(returnvl[1],'.jt_profit');
			}
		}
	});
};


function update_option(optionname,arr_value,opid,isreload){
	if(optionname != undefined ){
		var arr = new Array(); //keys,option,arr_value(key1@value1,key2@value2),idcode
		arr[0] = 'update';
		arr[1] = optionname;
		arr[2] = arr_value;
		arr[3] = opid;
		$(".k-window").fadeOut();
		$.ajax({
			url: '<?php echo URL.'/'.$controller;?>/mutisave_option',
			type:"POST",
			data: {arr:arr},
			success: function(rtu){
				ajax_note(" Saved !");
				if( isreload != undefined && isreload==1 )
					location.reload();
			}
		});

	}else
		return '';
}

function change_uom(val){
	var old_html = $("#field_oum").html();
		old_html = old_html.split("<span");
		old_html = old_html[1].split(">");
		old_html = old_html[1];
		old_html = old_html.replace('value','value="" title');
		$("#field_oum .combobox").remove();
		$("#field_oum").prepend(old_html+" onchange=\"save_uom();\" />");


	$.ajax({
		url: '<?php echo URL.'/'.$controller;?>/select_render',
		dataType: "json", //luu y neu dung json
		type:"POST",
		data: {sell_by:val},
		success: function(jsondata){
			$("#oum").combobox(jsondata);
			reload_subtab('purchasing');
		}
	});
}

function save_uom(){
	var values = $("#oumId").val();
	save_field('oum',values,'');
}

function after_choose_products(ids,names,keys){
	var data_json = JSON.parse($("#after_choose_products"+ keys + ids).val());
	var old_key = keys;
	keys = keys.split("_");
	var opid = keys[1];
	keys = keys[0];


	if(keys=='changesupplier'){
		var datacheck = '';
		if($("#cb_current_"+opid).is(':checked')){
			datacheck = 'on';
		}

		var data = {
			'sku': data_json.sku,
            'name': data_json.name,
			'product_id': ids,
            'current'	: datacheck,
			'sizew' 	: data_json.sizew,
            'sizew_unit': data_json.sizew_unit,
            'sizeh' 	: data_json.sizeh,
            'sizeh_unit': data_json.sizeh_unit,
            'sell_by'	: data_json.sell_by,
            'sell_price': data_json.sell_price
       };
       save_option('supplier', data, opid, 1, 'purchasing', 'update');


	}else if(old_key=='choice_code'){
		opid = $("#id_choicing_for_popup").val();
		var datas = new Object();

		if(opid=='add'){//add
			datas['product_id'] 	= ids;
			datas['markup'] 		= 0;
			datas['margin'] 		= 0;
			datas['quantity'] 		= 1;
			save_option('madeup',datas,'',1,'costings','add');

		}else{ //update
			datas['product_id'] 	= ids;
			save_option('madeup',datas,opid,1,'costings','update');
		}

<?php $choice_key = 'option'; ?>

	}else if(old_key=='choice_code_<?=$choice_key;?>'){
		//$("#window_popup_products"+old_key).data("kendoWindow").close();
		opid = $("#id_choicing_for_popup").val();
		var datas = new Object();

		if(opid=='add'){//add
			var unitprice = data_json.sell_price;
			if(data_json.product_type=='Vendor Stock')
				unitprice = data_json.unit_price;

			datas['product_id'] 	= ids;
			datas['markup'] 		= 0;
			datas['margin'] 		= 0;
			datas['quantity'] 		= 1;
			datas['option_group'] 	= '';
			datas['require'] 		= 1;
			save_option('options',datas,'',1,'general','add');

		}else{ //update
			datas['product_id'] 	= ids;
			save_option('options',datas,opid,1,'general','update');
		}
	}

}



	function new_popup_supplier(){
		$("#click_open_window_companiessearch_company_name").click();
	}


</script>