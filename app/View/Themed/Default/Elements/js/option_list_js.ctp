<script type="text/javascript">
$(document).ready(function() {
	var request = '';
	if($("#products_id").val()!=''){
		request = '&products_product_id='+$("#products_id").val();
	}
	window_popup("products", "Specify Products","save_custom_product", "save_custom_product","?products_product_type=Product"+request);
	$(".jt_ajax_note").css("top","28px");
	<?php if($controller == 'salesorder'){ ?>
	var key = "<?php echo $controller ?>_"+$("#mongo_id").val()+"_"+$("#costing_product_key").val();
	if(localStorage[key]!=undefined){
		var html = localStorage[key];
		html = JSON.parse(html);
		if(html.html=="")
			return;
		if($(".line_box","#container_option").length)
			$(".line_box:last","#container_option").after(html.html);
		else
			$(".ul_mag:first","#container_option").before(html.html);
		$("ul.ul_mag:gt(-"+html.lengths+")","#container_option").remove();
		reset_bg('option');
	}
	<?php } ?>
});
$("input","#container_option").change(function(){
	if($(this).attr("id").indexOf("hidden_")!==-1)
		return false;
	submit_alert();
});
$("#bt_add_option").click(function(){
	confirms3('Message','Create a blank line to manually in a cost or select from a product code?',['Product','Blank','']
	      ,function(){//Product
			$('#click_open_window_products_option_popup').click();
	      },function(){//Blank
			create_temporary_product();
	      },function(){
	      	return false;
	      },function(){
	      	return false;
	      });
})
$(".viewcheck_hidden").change(function(){
	var id = $(this).attr("id");
	id = id.split("_");
	id = id[id.length - 1];
	var value = 0;
	if($(this).is(":checked"))
		value = 1;
	save_option('options',{hidden : value},id, "no_close&&0");
	var line_no = $("#line_no_"+id).val();
	if(line_no!=undefined){
		save_option('products',{hidden : value},line_no,"no_close&&0");
	}
	localStorage.setItem("<?php echo $controller; ?>_costing_submit",1);
});
$(".viewprice_unit_price").change(function(){
	var id = $(this).attr("id");
	id = id.split("_");
	id = id[id.length - 1];
	$("#cb_user_custom_"+id).prop("checked",true);
});
function create_temporary_product(product_id){
	$("#window_popup_productschoice_option").data("kendoWindow").close();
	if(product_id==undefined)
		product_id = '';
	var num_of_options = $(".line_box","#container_option").length;
	$.ajax({
		url: '<?php echo URL;?>/<?php echo $controller;?>/create_temporary_product',
		type:"POST",
		data: {product_id:product_id, num_of_options: num_of_options},
		success: function(result){
			if($(".line_box","#container_option").length){
				$(".line_box:last","#container_option").next().remove();
				$(".line_box:last","#container_option").after(result);
			} else {
				$(".ul_mag:first","#container_option").remove();
				$(".ul_mag:first","#container_option").before(result);
			}
			$("input","#container_option").unbind("change");
			$("input","#container_option").change(function(){
				submit_alert("costing");
			});
			$("#entry_menu_save_custom_costing").hide();
		}
	});
}
function after_choose_products(ids,names,keys){
	if(keys=='choice_option'){
		create_temporary_product(ids);
	} else if(keys=='save_custom_product'){
		$("#window_popup_productssave_custom_product").data("kendoWindow").close();
		var product_line = $("#subitems").val();
      	$.ajax({
      		url: "<?php echo URL; ?>/<?php echo $controller; ?>/replace_option_product",
      		type: "POST",
      		data: {replace_id:ids,product_line:product_line},
      		success: function(result){
      			if(result=='ok') {
      				location.href = "<?php echo URL.'/products/entry'; ?>"
      			}
      			else
      				confirms('Message',result);
      		}
      	});
	}
}
function save_custom_product(){
	confirms3('Message','Do you want to Replace or Create new product?',['Create new','Replace','']
	          ,function(){//create new
	          	var product_line = $("#subitems").val();
	          	$.ajax({
	          		url: "<?php echo URL; ?>/<?php echo $controller; ?>/duplicate_option_product",
	          		type: "POST",
	          		data: {product_line:product_line},
	          		success: function(result){
	          			if(result=='ok')
	          				location.href = "<?php echo URL.'/products/lasts'; ?>"
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
function removeOnScreen(obj){
	confirms('Confirm delete','Are you sure you want to delete this record?',
         function(){
         	var id = $(obj).attr("id");
			id = id.replace("del_option_","");
			var class_name = $("#listbox_option_"+id).attr("class");
			class_name = class_name.replace("line_box","");
			$("#listbox_option_"+id).animate({
			  opacity:'0.1',
			  height:'1px'
			},500,function(){$(this).remove();});
			var ix = $("#container_option .ul_mag").index($(obj).parent().parent().parent());
			change_bg(parseInt(ix),'option');
         },function(){
         	return false;
	});

}
function option_delete(iditem){
	confirms('Confirm delete','Are you sure you want to delete this record?',
		function(){
			//remove line
			var boxname = $("#"+iditem).attr("rev");
			var ids = $("#"+iditem).attr("rel");
			var ix = $("#container_"+boxname+" .ul_mag").index($("#"+iditem).parent().parent().parent());
			$("#"+iditem).parent().parent().parent().animate({
			  opacity:'0.1',
			  height:'1px'
			},500,function(){$(this).remove();});

			//get subtab active
			var subtabname = $(".ul_tab .active").attr("id");

			ix = parseInt(ix);
			$.ajax({
				url: '<?php echo URL.'/'.$controller;?>/deleteopt/'+ids,
				type:"POST",
				success: function(txt){
					change_bg(ix,boxname);
					ajax_note(" Deleted !");
				}
			});
	},function(){ return false; });
}
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
		if(name=="choice"&&!$(this).is(":checked")){
			if(this_group_type=='Exc'){
				var require = false;
				for(var ids in group){
					if($("input[name=cb_require_"+ids+"]").is(":checked")){
						require = true; break;
					}
				}
				if(require){
					if(Object.keys(group).length>1){
						for(var ids in group){
							if(ids==this_ids) continue;
								$("#cb_choice_"+ids).prop("checked",true);break;
							}
					} else
						$(this).prop("checked",true);
				}
			}
			return false;
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
function create_new_line(option_key){
	var keysave = 'add';
	var product_id = $("#products_id").val();
	var subitems = $("#subitems").val();
	$.ajax({
		url: '<?php echo URL;?>/<?php echo $controller;?>/save_new_line',
		type:"POST",
		data: {product_id:product_id,option_id:option_key,option_for:subitems},
		success: function(result){
			if(result!='ok')
				alerts('Message',result);
			else
				location.reload();
		}
	});
}
/*function after_choose_products(ids,names,keys){
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
		save_option('options',datas,'',"window_popup_productschoice_option&&0",'options','add',function(option_key){
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
      			}
      			else
      				alerts('Message',result);
      		}
      	});
	}
}*/
</script>