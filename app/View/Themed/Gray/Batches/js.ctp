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

function after_choose_products(ids,names,keys){
	if(keys=='product_name'){
		var arrdata = JSON.parse($("#after_choose_products"+keys+ids).val());
		var productname = arrdata.name;
		var product = arrdata.product_id;
		$("#product_name").val(productname);
		$("#product").val(product);
		$("#product").val(ids);
		$(".product_name").addClass('jt_link_on');
		save_data('product_name',productname,'',ids,function(arr_ret){
		});
		save_data('product',product,'',ids,function(arr_ret){
		});
	}
}
//An, tam thoi de nhu vay, khi nao co yeu cau thi chinh sau.
<?php if(!$this->Common->check_permission('batches_@_entry_@_edit',$arr_permission)){?>
	$('.icon_emp').remove();
	$("input,select,checkbox", "#editview_box_bookings1,#block_full_otherdetails").each(function() {
		$(this).attr("disabled", true).css("background-color", "transparent");
	});
	$('#comms_create').remove();
	$('#comms_type').remove();
	$('#bt_add_otherdetails').remove();
	$('#bt_add_bookings2').remove();
	$("#block_full_otherdetails ").find("a").each(function(){$(this).remove();});
<?php } ?>

</script>