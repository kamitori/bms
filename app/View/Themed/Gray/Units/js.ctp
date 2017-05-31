<?php echo $this->element('js_entry');?>
<?php echo $this->element('js/permission_product_modules');?>
<script type="text/javascript">
$(function(){

	//default focus
	$("#name").focus();
	// Xu ly save, update
	$(".jt_ajax_note").html('');


	$("form input,form select").change(function() {
		var fixkendo = $(this).attr('class');
		var fieldname = $(this).attr("name");
		var fieldid = $(this).attr("id");
		var fieldtype = $(this).attr("type");
			modulename = 'mongo_id';
		var ids = $("#"+modulename).val();
		var values = $(this).val();
		var func = ''; var titles = new Array();
		if(ids!='')
			func = 'update'; //add,update
		else
			func = 'add';

		/**
		* Xử lý dữ liệu
		*/
		var valueid='';

		//nếu là địa chỉ
		var check_address = fieldname.split("[");
		if(check_address[0]=='data'){
			save_address(check_address,values,fieldid,function(){
				change_tax_entry();
			});
			return '';
		}
		//nếu là check box
		if(fieldtype=='checkbox'){
			if($(this).is(':checked'))
				values = 1;
			else
				values = 0;
		}

		//nếu là select box
		if($('#'+fieldname).parent().attr('class')=='combobox'){
			values = $("#"+fieldname+"Id").val();
		}


		/**
		* lưu dữ liệu và hiển thị
		*/
		$(".jt_ajax_note").html("Saving...       ");
		save_data(fieldname,values,ids,valueid);


	});

	$(".jt_ajax_note").html('');

});



function save_address(arr,values,fieldid,handleData){
	var	keys = arr[1].replace("]","");
	var keyups = keys.charAt(0).toUpperCase() + keys.slice(1);
	var opname = keys + "_address";
	var address_field = arr[2].replace("]","");
	var datas = new Object();
	// if(address_field!='invoice_country' && address_field!='shipping_country'){
	// 	datas[address_field] = values;


	// }else
	if(address_field=='shipping_province_state'){
		var vtemp = $("#ShippingProvinceState").val();
		datas[address_field] = vtemp;

		values = $("#ShippingProvinceStateId").val();
		datas[address_field + "_id"] = values;

	}else{
		datas[address_field] = values;
		values = $("#"+fieldid+'Id').val();
		datas[address_field + "_id"] = values;
	}
	var olds = $("#"+opname).val();
	if(olds!=''){
		olds = '';
		idas = '0';
	}else{
		olds = 'add';
		idas = '';
		$("#"+opname).val(values+',');
	}
	save_option(opname,datas,idas,0,'',olds,function(arr_return){
		if(handleData!=undefined)
			handleData(arr_return);
	});
	ajax_note("Saving...Saved !");
}


function save_address_pr(keys){
	var keyups = keys.charAt(0).toUpperCase() + keys.slice(1);
	var fieldid = keyups+'ProvinceState';
	var values = $("#"+fieldid).val();
	var arr = new Array();
	arr[1] = keys+']';
	arr[2] = keys+'_province_state]';
	save_address(arr,values,fieldid);

	$("#"+keyups+'ProvinceState').css('border','none');
	$("#"+keyups+'ProvinceState').css('border-bottom','1px solid #dddddd');
}

function after_choose_contacts(ids,names,keys){
	var mongoid,func;
	mongoid = $("#mongo_id").val();
	if(mongoid!='')
		func = 'update';
	else
		func = 'add';
	$(".k-window").fadeOut('slow');

	if(keys=='contact_name'){
		var arrdata = JSON.parse($("#after_choose_contacts"+keys+ids).val());
		var fullname = arrdata.full_name;
		$("#contact_id").val(ids);
		$("#contact_name").val(fullname);
		$("#md_contact_name").html(fullname);
		$(".k-window").fadeOut('slow');
		$(".link_to_contact_name").addClass('jt_link_on');
		save_data('contact_name',names,'',ids,function(arr_ret){
		});
	}
}

function after_choose_locations(ids,names,keys){
	if(keys=='current_location_name'){
		var arrdata = JSON.parse($("#after_choose_locations"+keys+ids).val());
		var currentlocationname = arrdata.name;
		$("#current_location_name").val(currentlocationname);
		$("#current_location_id").val(ids);
		$(".current_location_name").addClass('jt_link_on');
		save_data('current_location_name',currentlocationname,'',ids,function(arr_ret){
		});
	}
	else if(keys=='standard_location_name'){
		var arrdata = JSON.parse($("#after_choose_locations"+keys+ids).val());
		var standardlocationname = arrdata.name;
		$("#standard_location_name").val(standardlocationname);
		$("#standard_location_id").val(ids);
		$(".standard_location_name").addClass('jt_link_on');
		save_data('standard_location_name',standardlocationname,'',ids,function(arr_ret){
		});
	}
}
function after_choose_products(ids,names,keys){
	if(keys=='product_name'){
		var arrdata = JSON.parse($("#after_choose_products"+keys+ids).val());
		var name = arrdata.name;
		var product_id = arrdata._id
		$("#product_name").val(name);
		$("#product_id").val(ids);
		$(".product_name").addClass('jt_link_on');
		save_data('product_name',name,'',ids,function(arr_ret){reload_subtab('general');
		});
		save_data('product_id',product_id,'',ids,function(arr_ret){reload_subtab('general');
		});
	}
}
function after_choose_batches(ids,names,keys){
 if(keys=='batch_name'){
  var arrdata = JSON.parse($("#after_choose_batches"+keys+ids).val());
  var batch =arrdata.batch_name;
  var batch_ref =arrdata.code;
  //console.log(batch_ref);
  $("#batch_ref").val(batch_ref);
  $("#batch_name").val(batch_name);
  $("#batch_id").val(ids);
  $(".batch_name").addClass('jt_link_on');
  save_data('batch_name',batch,'',ids,function(arr_ret){
  });
  save_data('batch_ref',batch_ref,'',ids,function(arr_ret){
  });
 }
}

<?php if(!$this->Common->check_permission('units_@_entry_@_edit',$arr_permission)){?>
	$(".container_same_category ").find("a").each(function(){$(this).remove();});
	$("#comms_create").remove();
	$("#comms_type").remove();
	$("#bt_add_bookings").remove();
<?php } ?>

</script>