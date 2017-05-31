<script type="text/javascript">
$(document).ready(function() {
	window_popup("products", "Specify Products","choice_option", "option_popup","?products_product_type=Product");
	var request = '';
	if($("#products_id").val()!=''){
		request = '&products_product_id='+$("#products_id").val();
	}
	window_popup("products", "Specify Products","save_custom_product", "save_custom_product","?products_product_type=Product"+request);
	$(".jt_ajax_note").css("top","28px");
});
function cal_line_entry(data,fieldchange,callBack){
	$.ajax({
		url : "<?php echo URL.'/'.$controller;?>/cal_price_line",
		type:"POST",
		data: {data:JSON.stringify(data),fieldchange:fieldchange,isOption:true},
		success: function(result){
			if(typeof callBack == "function")
				callBack(result);
		}
	});
}
$("input",".form_<?php echo $controller;?>_option").change(function(){
	ajax_note('<span class="bold">Please save change before exit!</span>');
	var name = $(this).attr("name");
	var this_ids = name.split("_");
	this_ids = this_ids[this_ids.length-1];
	name = name.replace("_"+this_ids,"");
	name = name.replace("cb_","");
	//Xử lý EXC Group Type
	if(name == "group_type" || name == "option_group" || name == "choice"){
		var this_group_name = $("#option_group_"+this_ids).val();
		var this_group_type = $("#group_type_"+this_ids).val();
		var is_span = false;
		if(this_group_name==undefined){
			this_group_name = $.trim($("#txt_option_group_"+this_ids).html());
			is_span = true;
		}
		if(this_group_type==undefined)
			this_group_type = $.trim($("#txt_group_type_"+this_ids).html());
		if(this_group_name=="" || this_group_type=="Inc")
			return false;
		if(name=="choice"&&!$(this).is(":checked"))
			return false;
		if(!is_span)
			var option_group = $(".form_<?php echo $controller;?>_option").find(".viewprice_option_group");
		else
			var option_group = $(".form_<?php echo $controller;?>_option").find(".option_group");
		var group = {};
		for(var i = 0; i < option_group.length; i++){
			if(!is_span)
				var group_name = option_group[i].value;
			else
				var group_name = option_group[i].innerText;
			var ids = option_group[i].id;
			var ids = ids.split("_");
			ids = ids[ids.length-1];
			if(group_name == this_group_name){
				group[ids] = ids;
			}
		}
		var i = 0;
		var found = false;
		for(var ids in group){
			var type_group = $("#group_type_"+ids).val();
			if(type_group==undefined)
				type_group = $.trim($("#txt_group_type_"+this_ids).html());
			if(type_group!="Exc")
				delete group[ids];
			else{
				if(ids==this_ids)
					found = true;
				i++;
			}
		}
		if(i>1){
			for(var ids in group)
				$("#cb_choice_"+ids).attr("checked",false);
			if(found)
				$("#cb_choice_"+this_ids).prop("checked",true);
			else
				for(var ids in group){
					$("#cb_choice_"+ids).prop("checked",true);break;
				}
		}
	}

});

$("#bt_add_option").click(function(){
	$("#option_popup").click();
})


function create_new_line(option_key){
	var keysave = 'add';
	var product_id = $("#products_id").val();
	var subitems = $("#subitems").val();
	$.ajax({
		url: '<?php echo URL;?>/<?php echo $controller;?>/save_new_line',
		type:"POST",
		data: {product_id:product_id,option_id:option_key,option_for:subitems},
		success: function(result){
			if(result!='ok'){
				alerts('Message',result);
				return false;
			}else{
				location.reload();
			}
		}
	});
}
function after_choose_products(ids,names,keys){
	if(keys=='choice_option'){
		var datas = new Object();
		var data_json = JSON.parse($("#after_choose_products"+ keys + ids).val());

		var unitprice = data_json.sell_price;
		if(data_json.product_type=='Vendor Stock')
			unitprice = data_json.unit_price;

		datas['product_id'] 	= ids;
		datas['code'] 			= data_json.code;
		datas['product_name'] 	= data_json.name;
		datas['parent_line_no'] = $("#subitems").val();
		datas['line_no'] 		= '';
		datas['proline_no'] 	= '';
		datas['unit_price'] 	= unitprice;
		datas['custom_unit_price'] 	= unitprice;
		datas['discount'] 		= 0;
		datas['oum'] 			= data_json.oum;
		datas['quantity'] 		= 1;
		datas['option_group'] 	= '';
		datas['require'] 		= 0;
		datas['choice'] 		= 1;
		datas['same_parent']	= 0;
		datas['sell_by']		= data_json.sell_by;
		save_option('options',datas,'',0,'options','add',function(option_key){
			create_new_line(option_key);
		});
	} else if(keys=='save_custom_product'){
		$("#window_popup_productssave_custom_product").data("kendoWindow").close();
		var product_line = $("#subitems").val();
      	$.ajax({
      		url: "<?php echo URL; ?>/<?php echo $controller; ?>/replace_option_product",
      		type: "POST",
      		data: {replace_id:ids,product_line:product_line},
      		success: function(result){
      			if(result=='ok'){
      				ajax_note('Saving...Saved!');
      				// location.reload();
      			}
      			else
      				confirms('Message',result);
      		}
      	});
	}
}
function save_custom_product(){
	confirms3('Message','Do you want to Replace or Create new product?',['Create new','Replace','']
	          ,function(){//Duplicate
	          	var product_line = $("#subitems").val();
	          	$.ajax({
	          		url: "<?php echo URL; ?>/<?php echo $controller; ?>/duplicate_option_product",
	          		type: "POST",
	          		data: {product_line:product_line},
	          		success: function(result){
	          			if(result=='ok'){
	          				ajax_note('Saving...Saved!');
	          				location.reload();
	          			}
	          			else
	          				confirms('Message',result);
	          		}
	          	});
	          },function(){//Replace
	          	$("#save_custom_product").click();
	          },function(){
	          	return false;
	          },function(){
	          	return false;
	          });
}
</script>