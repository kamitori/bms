<script type="text/javascript">
$(document).ready(function() {
	$(".hidden_oum").each(function(){
		var value = $(this).val();
		var ids = $(this).attr("id");
		ids = ids.replace("Id","");
		ids = ids.split("_");
		ids = ids[ids.length - 1];
		$("#oum_"+ids).val(value);
	})
	var request = '';
	if($("#products_id").val()!=''){
		request = '&products_product_id='+$("#products_id").val();
	}
	window_popup("products", "Specify Products","save_custom_product", "save_custom_product","?products_product_type=Product"+request);
	$(".jt_ajax_note").css("top","28px");
});
$("input","#container_option").change(function(){
	submit_alert("costing");
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
$("#bt_add_option").click(function(){
	confirms3('Message','Create a blank line to manually in a cost or select from a product code?',['Product','Blank','']
	      ,function(){//Product
			$('#click_open_window_products_option_popup').click();
	      },function(){//Blank
	  //     	var datas = new Object();
	  //     	datas['product_id'] 	= '';
			// datas['code'] 			= '';
			// datas['product_name'] 	= '';
			// datas['parent_line_no'] = $("#subitems").val();
			// datas['line_no'] 		= '';
			// datas['proline_no'] 	= '';
			// datas['unit_price'] 	= '';
			// datas['custom_unit_price'] 	= '';
			// datas['discount'] 		= 0;
			// datas['oum'] 			= '';
			// datas['quantity'] 		= 1;
			// datas['option_group'] 	= '';
			// datas['require'] 		= 0;
			// datas['choice'] 		= 1;
			// datas['same_parent']	= 0;
			// datas['sell_by']		= '';
			// save_option('options',datas,'',"window_popup_productschoice_option&&0",'options','add',function(option_key){
			// 	create_new_line(option_key);
			// });
			create_temporary_product();
	      },function(){
	      	return false;
	      },function(){
	      	return false;
	      });
})
// function create_new_line(option_key){
// 	var keysave = 'add';
// 	var product_id = $("#products_id").val();
// 	var subitems = $("#subitems").val();
// 	var num_of_options = $(".line_box","#container_option").length;
// 	$.ajax({
// 		url: '<?php echo URL;?>/<?php echo $controller;?>/save_new_line',
// 		type:"POST",
// 		data: {product_id:product_id,option_id:option_key,option_for:subitems, num_of_options: num_of_options},
// 		success: function(result){
// 			if($(".line_box","#container_option").length){
// 				$(".line_box:last","#container_option").next().remove();
// 				$(".line_box:last","#container_option").after(result);
// 			} else {
// 				$(".ul_mag:first","#container_option").remove();
// 				$(".ul_mag:first","#container_option").before(result);
// 			}
// 		}
// 	});
// }
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
      			if(result=='ok')
      				ajax_note('Saving...Saved!');
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
</script>