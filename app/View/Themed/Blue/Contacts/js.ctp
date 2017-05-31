<?php echo $this->element('js_entry');?>
<?php echo $this->element('js/permission_product_modules');?>
<style>
.jt_ajax_note{
z-index: 59;
}
</style>
<script type="text/javascript">
/*$(function(){*/
$(document).ready(function() {
//reload_subtab('line_entry');
	$("#is_customer").change(function(){
		location.reload();
	});
	$("#country").change(function(){
		if($(this).val()!=40)
			$("#province").html('');
	});

	//default focus
	$("#code").focus();

	// Xu ly save, update
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

		//check invoice address
		var check_address = fieldname.split("[");
		if(check_address[0]=='data'){
			var field = check_address[check_address.length -1];
			field = field.replace("]","");
			field = field.replace("invoice_","");
			var data = {};
			data[field] = values;
			if($(this).parent().hasClass("combobox"))
				data[field+"_id"] = $("#"+fieldid+"Id").val();
			var id = $("#addresses_default_key").val();
			save_option("addresses",data,id,0,'addresses','update',function(){
				reload_subtab('addresses');
				window_popup('addresses', 'Specify address','company','click_open_window_addressesinvoice','?contact_id='+$("#mongo_id").val(),'force_re_install');
			});
			return false;
		}


		if(fieldtype=='checkbox'){
			if($(this).is(':checked'))
				values = 1;
			else
				values = 0;
		}
		$(".jt_ajax_note").html("Saving...       ");

		$.ajax({
				url: '<?php echo URL.'/'.$controller;?>/ajax_save',
				type:"POST",
				data: {field:fieldname,value:values,func:func,ids:ids},
				success: function(text_return){ //alert(text_return);
					text_return = text_return.split("||");
						 if (text_return == "email_not_valid"){
								$("#email").addClass('error_input');
								ajax_note('Email not valid, please check email field!');
						 }else{
							$("#email").removeClass('error_input');
							$("#"+modulename).val(text_return[0]);
							// change tittle, thay đổi tiêu đề của items
							<?php foreach($arr_settings['title_field'] as $ks=>$vls){?>
								titles[<?php echo $ks;?>] = '<?php echo $vls;?>';
							<?php } ?>
							if(titles.indexOf(fieldname)!=-1){
								$("#md_"+fieldname).html(values);
								$(".md_center").html("-");
							}
							ajax_note("Saving...Saved !");

							// if status
							if(fieldname=='invoice_status')
								location.reload();

                            if(fieldname=='payment_terms')
                                location.reload();

                            if(fieldname=='name')
								$("form#other_record input#name").val(values);
							if(fieldname=='tax')
								save_field('taxval',taxval,'');
						}
				}
		});
	});



	$(".jt_ajax_note").html('');

	//View and cutom Option value
	$( document ).delegate(".view_option","click",function(){
		view_product_option($(this).attr('rel'));
	});

	//RFQ's List
	$('#bt_add_rfqs, .entry_menu_add_rfqs ').click(function(){
		var d = new Date();
		var itemid = $('#itemid').val();
		var subitems = $('#subitems').val();
		var employee_id = $('#employee_id').val();
		var employee_name = $('#employee_name').val();
		var quote_code = $('#quote_code').val();
		var sumrfq = parseInt($('#sumrfq').val()); sumrfq = sumrfq+1;
		var dates = parseInt(d.getTime());
			dates = Math.round(dates/1000);
		var datas = {
			'rfq_no' : quote_code+'/'+sumrfq,
			'rfq_code' : subitems,
			'rfq_date' : dates,
			'employee_id' : employee_id,
			'employee_name' : employee_name
		};
		save_option('rfqs',datas,'',0,'rfqs','add',function(sms){
			window.location.assign("<?php echo URL;?>/invoices/rfqs_entry/"+itemid+'/'+(sumrfq-1));
		});
	});
});


function after_choose_companies(ids,names,keys){
	if(keys=='company'){
		var arr_data_from = JSON.parse($("#after_choose_companies"+ keys + ids).val());
		var value = arr_data_from.name;
		add1 = arr_data_from.addresses['0'].address_1;
		add2 = arr_data_from.addresses['0'].address_2;
		add3 = arr_data_from.addresses['0'].address_3;
		phone = arr_data_from.phone;
		fax = arr_data_from.fax;
		email = arr_data_from.email;
		web = arr_data_from.web;

		$("#company").val(names);
		$("#company_id").val(ids);

		save_data('company',value,'',ids,function(txt){
			reload_address('invoice_');
		});
	}
}
function reload_address(address_key){
	var arr_field = ["address_1","address_2","address_3","town_city","province_state","zip_postcode","country"];
	if(address_key!='' && address_key!=undefined){
		$.ajax({
			url: '<?php echo URL.'/'.$controller;?>/reload_address',
			dataType: "json",
			type:"POST",
			data: {address_key:address_key},
			success: function(data_return){
				for (var i=0;i<arr_field.length;i++){
					if(data_return[arr_field[i]]!=undefined)
					{
						console.log(data_return[arr_field[i]]);
						$('#'+ChangeFormatId(arr_field[i])).val(data_return[arr_field[i]]);  // #Address1, #Address2,...
					}
					else
						$('#'+ChangeFormatId(arr_field[i])).val('');
				}
			}
		});
		//load lại nút chọn popup address
		var company_id = $("#company_id").val();
		var contact_id = $("#contact_id").val();
		var extra = '';
		if(contact_id!='')
			extra = '&contact_id='+contact_id;

		/*var addressesinvoice_icon = $('#click_open_window_addressesinvoice').attr('class');
		var addressesshipping_icon = $('#click_open_window_addressesshipping').attr('class');

		if(address_key=='invoice_' && addressesinvoice_icon ==undefined)
			$("#map_invoice").before("<span class=\"iconw_m indent_dw_m\" title=\"Specify address\" id=\"click_open_window_addressesinvoice\"></span>");
		if(address_key=='shipping_' && addressesshipping_icon ==undefined)
			$("#map_invoice").before("<span class=\"iconw_m indent_dw_m\" title=\"Specify address\" id=\"click_open_window_addressesshipping\"></span>");*/

		window_popup('addresses', 'Specify company address','invoice','click_open_window_addressesinvoice','?company_id='+company_id+extra,'force_re_install');
		window_popup('addresses', 'Specify company address','shipping','click_open_window_addressesshipping','?company_id='+company_id+extra,'force_re_install');

	}
}


function after_choose_contacts(ids,names,keys){
	if(keys=='our_rep'){
		var arr_data_from = JSON.parse($("#after_choose_contacts"+ keys + ids).val());
		var value = arr_data_from.first_name + " " + arr_data_from.last_name;
		save_data('our_rep',value,'',ids,function(txt){
			console.log(txt);
		});

	}
	if(keys=='contact_name'){
		var arr_data_from = JSON.parse($("#after_choose_contacts"+ keys + ids).val());
		var value = arr_data_from.first_name + " " + arr_data_from.last_name;
		save_data('contact_name',value,'',ids,function(txt){
			console.log(txt);
		});

	}
}
// xử lý sau khi chọn contact
//cách xử lý theo 2 hàm độc lập:
//Lấy data(get_data_form_module) + Save data (save_muti_field)



/*function save_address(arr,values,fieldid,handleData){ // giống ben Enquiries
	// arr = ["data", "invoice]", "invoice_address_1]"] ;
	// arr = ["data", "invoice]", "invoice_address_2]"]
	var	keys = arr[1].replace("]",""); //keys = 'invoice'
	var keyups = keys.charAt(0).toUpperCase() + keys.slice(1); // Invoice
	var opname = "addresses"; // opname = "invoice_address", luon luon vay voi moi field , sua lai thanh "addresses"
	var address_field = arr[2].replace("]","").replace("invoice_",""); // address_field chinh la cai field luu trong db:invoice_address_1,invoice_address_2,invoice_address_3, invoice_town_city, invoice_province_state,invoice_zip_postcode, invoice_country
	var datas = new Object();
	if(address_field!='invoice_country' && address_field=='invoice_province_state'){
		datas[address_field] = values;
	//luu province
	}else if(address_field=='invoice_province_state'){
		var vtemp = $("#"+fieldid+'Id').val();
			datas[address_field] = $("#"+fieldid).val();//luu gia tri custom cua province
			datas[address_field+'_id'] = vtemp;
		$("#"+keyups+'ProvinceState').css('border','none');
		$("#"+keyups+'ProvinceState').css('border-bottom','1px solid #dddddd');
		//$("#"+keyups+'ProvinceState').focus();

	//luu country
	}else{
		vtemp = $("#"+fieldid+'Id').val();
		datas[address_field] = $("#"+fieldid).val(); // obj[invoice_address_2] = "2a"
		datas[address_field+'_id'] = vtemp;
		if(vtemp=='CA' || vtemp=='US'){
			$("#"+keyups+'ProvinceState').css('border','1px solid #f00');
			$("#"+keyups+'ProvinceState').focus();
		}else{
			$("#"+keyups+'ProvinceState').css('border','none');
			$("#"+keyups+'ProvinceState').css('border-bottom','1px solid #dddddd');
		}
	}
	var olds = $("#"+opname).val();
	if(olds!=''){  // update
		olds = '';
		idas = '0';
	}else{  // add moi
		olds = 'add';
		idas = '';
		$("#"+opname).val(values+',');
	}
	olds = '';idas = '0';  																		// tam thoi fix cung, check lai dieu kien
	save_option(opname,datas,idas,0,'',olds,function(arr_return){
		if(handleData!=undefined)
			handleData(arr_return);
	});
	ajax_note("Saving...Saved !");
}*/

function save_address(arr,values,fieldid,handleData){
	// arr = [ "data" ,  "]" ,  "_address_3]" ]
	var	keys = arr[1].replace("]","");  //keys = ''
	var keyups = keys.charAt(0).toUpperCase() + keys.slice(1);
	var opname = "addresses";  // opname là key ngoài, moi field con, sua lai thanh "addresses"
	var address_field = arr[2].replace("]","").replace("_","");;   // address_field chinh la cai field luu trong db, moi lan change la address_field = field này
	var datas = new Object();

	// luu cac field ko phai droplist
	if(address_field!='country' && address_field!='province_state'){
		datas[address_field] = values;

	//luu province
	}else if(address_field=='province_state'){
		var vtemp = $("#"+fieldid+'Id').val();
		datas[address_field] = $("#"+fieldid).val();//luu gia tri custom cua province
		datas[address_field+'_id'] = vtemp;
		$("#"+keyups+'ProvinceState').css('border','none');
		$("#"+keyups+'ProvinceState').css('border-bottom','1px solid #dddddd');
		//$("#"+keyups+'ProvinceState').focus();

	//luu country
	}else{
		vtemp = $("#"+fieldid+'Id').val();
		datas[address_field] = $("#"+fieldid).val();
		datas[address_field+'_id'] = vtemp;
		if(vtemp=='CA' || vtemp=='US'){
			$("#"+keyups+'ProvinceState').css('border','1px solid #f00');
			$("#"+keyups+'ProvinceState').focus();
		}else{
			$("#"+keyups+'ProvinceState').css('border','none');
			$("#"+keyups+'ProvinceState').css('border-bottom','1px solid #dddddd');
		}
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
	olds = '';idas = '0';											// tam thoi fix cung, check lai dieu kien
	save_option(opname,datas,idas,0,'',olds,function(arr_return){
		if(handleData!=undefined)
			handleData(arr_return);
	});
	ajax_note("Saving...Saved !");
}

function save_address_function(){
	var data = {};
	data["province_state"] = $("#InvoiceProvinceState").val();
	data["province_state_id"] = $("#InvoiceProvinceState").val();
	$("li",$("#InvoiceProvinceState").parent()).each(function(){
		if($.trim($(this).text()) == data["province_state"]){
			data["province_state_id"] = $(this).attr("value");
		}
	});
	save_option("addresses",data,0,0,'','update',function(){
		$("#addresses",".ul_tab").click();
	});
}

function build_popup(html,title,h,w,wfocus){
	if( $("#build_popup_window").attr("id") == undefined ){
		$('<div id="build_popup_window"><div class="popup_window_cont"></div></div>').appendTo("body");
	}
	if(title==undefined)
		var title = 'Message';
	if(h==undefined)
		var h = '250px';
	if(w==undefined)
		var w = '400px';
	if(wfocus==undefined)
		var wfocus = 'build_popup_window';
	if(html==undefined)
		var html = '';
	var build_popup_window = $("#build_popup_window");
		build_popup_window.kendoWindow({
			width: w,
			height: h,
			title: title,
			visible: false,
			activate: function(){
			  $('#'+wfocus).focus();
			}
		});
	//setup html
	$(".popup_window_cont").html(html);
	//show popup
	build_popup_window.data("kendoWindow").center();
	build_popup_window.data("kendoWindow").open();
}


function after_choose_addresses(ids,names,keys){
		$('#cb_default_'+ids).click();
		var address = new Object();
		var directs = ['name','address_1','address_2','address_3','town_city','province_state','province_state_id','zip_postcode','country'];
		for(var n in directs){
			address[keys+'_'+directs[n]] = $("#window_popup_addresses_"+names+"_"+directs[n]+'_'+ids+keys).val();
		}
		address[keys+'_country_id'] = parseInt($("#window_popup_addresses_"+names+"_country_id_"+ids+keys).val());
		address[keys+'_default'] = true;
		address['deleted'] = false;

		var address_0={'0':address};
		var invoice_address={'addresses':address_0};
		var jsonString = JSON.stringify(invoice_address);
		var arr_field = {'addresses':keys+'_address'};
		$(".k-window").fadeOut();
		save_muti_field(arr_field,jsonString,'',function(arr_ret){
			ajax_note('Saved.');
			address = arr_ret[keys+'_address'];
			address = address[0];
			for(var i in address){
				$("#"+ChangeFormatId(i)).val(address[i]);
			}
			//save_field('addresses_default_key',ids,'');
			//save tax
			var ShippingAddId = $("#ShippingProvinceStateId").val()
			if(keys=='shipping' || (keys=='invoice' && ShippingAddId=='')){
				var taxid = address[keys+'_province_state_id'];
				var allListElements = $( 'li[value="'+taxid+'"]' );
				var html = $("#tax").parent().find(allListElements);
				//console.log(taxid);
				//console.log(html);
				var tax = html[0].innerHTML;
				var taxval = tax.split("%");
				taxval = taxval[0];
				$('#tax').val(tax);
				$('#taxId').val(taxid);
				$('#tax').change();
			}
		});
}

</script>