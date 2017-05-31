<?php echo $this->element('js_entry');?>
<?php echo $this->element('js/permission_product_modules');?>
<style>
.jt_ajax_note{
z-index: 59;
}
</style>
<script type="text/javascript">
$(function(){
	if(!$("#clear-cache").length) {
		$(".bg_menu > .menu_control").append('<li><a id="clear-cache" href="javascript:void(0)" onclick="clear_cache()" title="Click to clear current Cache.">Clear cache</a></li>');
	}
	<?php $closing_month_id = isset($closing_month_id) ? $closing_month_id : false; ?>
	<?php if(isset($closing_month)){ ?>
	$(".bg_menu > .menu_control").append('<li><a id="open-module" style="background: #6A1515; color: #fff" href="javascript:void(0)" onclick="callPasswordPopup(function(){location.reload()},{open:1})" title="Click to open current Invoice.">Open Invoice</a></li>');
	<?php if(IS_LOCAL) {?>
	$(":input", "#<?php echo $controller; ?>_form_auto_save").click(function(){
		$("#open-module").click();
	});
	$("span[id^=click_open_window_]", "#<?php echo $controller; ?>_form_auto_save").removeAttr("onclick").click(function(){
		$("#open-module").click();
	});
	<?php } ?>
	<?php } else if(isset($_SESSION['JobsOpen_'.$closing_month_id])){ ?>
	if(!$("#close-module").length)
		$(".bg_menu > .menu_control").append('<li><a id="close-module" style="background: #6A1515; color: #fff" href="javascript:void(0)" onclick="closeAll()" title="Click to close.">Close Invoice</a></li>');
	<?php } ?>
	<?php if(IS_LOCAL) {?>
		$("#open-module,#close-module").hide();
	<?php } ?>
	callPassword();
	changePaymentLabel($("#payment_terms").val());
	$("#code").focus(function(){
		$(this).attr("rel",$(this).val());
	});
	$("#payment_due_date").next("a").click(function(){
		$("#payment_terms").trigger("change");
	});
//reload_subtab('line_entry');
	$("#pst_tax").change(function(){
		var ids = $(this).attr("id");
		var val = $(this).val();
		ids = "mx_"+ids;
		$("#"+ids).html(val);
	});

	$("#country").change(function(){
		if($(this).val()!=40)
			$("#province").html('');
	});

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

		//check address
		var check_address = fieldname.split("[");
		if(check_address[0]=='data'){
			jobtraq_loading();
			save_address(check_address,values,fieldid,function(){
				change_tax_entry();
			});
			return '';
		}
		if(fieldname=='code'){
			$(".jt_ajax_note").html("");
			if( $("#change_code_window" ).attr("id") == undefined ){
				var html = '<div id="password_confirm" >';
					html +=	   '<div class="jt_box" style=" width:100%;">';
					html +=		  '<div class="jt_box_line"><div class=" jt_box_label " style=" width:25%;"></div><div id="alert_message"></div></div>';
					html +=	      '<div class="jt_box_line">';
					html +=	         '<div class=" jt_box_label " style=" width:25%;height: 61px">Password</div>';
					html +=	         '<div class="jt_box_field " style=" width:71%"><input name="password" id="password" class="input_1 float_left" type="password" value=""></div><input style="margin-top:2%" type="button" class="jt_confirms_window_cancel" id="confirms_cancel" value=" Cancel " /><input style="margin-top:2%" type="button" class="jt_confirms_window_ok" value=" Ok " id="confirms_ok" />';
					html +=	      '</div>';
					html +=	      '</div>';
					html +=	   '</div>';
					html +=	'</div>';
				$('<div id="change_code_window" style="width: 99%; padding: 0px; overflow: auto;">'+html+'</div>').appendTo("body");
			}
			var confirms_window = $("#change_code_window");
			confirms_window.kendoWindow({
				width: "355px",
				height: "100px",
				title: "Enter password",
				visible: false,
				activate: function(){
				  $('#password').focus();
				}
			});
			$("#password").keypress(function(evt){
			var keyCode = (evt.which) ? evt.which : event.keyCode
			    if(keyCode==13)
			    	$("#confirms_ok").click();
			});
			$("#password").val("");
			confirms_window.data("kendoWindow").center();
			confirms_window.data("kendoWindow").open();
			$("#confirms_ok").unbind("click");
			$("#confirms_ok").click(function() {
				$("#alert_message").html("");
				if( $("#password").val().trim()==''  ){
					$("#alert_message").html('<span style="margin-left: 5px; color:red; font-weight: 700">Password must not be empty.</span');
					$("#password").focus();
					return false;
				}
				var values = {};
				values["password"] = $("#password").val();
				values["value"] = $("#code").val();
				$.ajax({
					url: '<?php echo URL.'/'.$controller;?>/ajax_save',
					type:"POST",
					data: {field:fieldname,value:values,func:func,ids:ids},
					success: function(text_return){
						if(text_return=='wrong_pass'){
							ajax_note("Wrong password.");
							$("#code").val($("#code").attr("rel"));
						} else if(text_return=='code_existed'){
							ajax_note("Code existed.");
							$("#code").val($("#code").attr("rel"));
						}else
							ajax_note("Saving...Saved !");
					}
				});
		       	confirms_window.data("kendoWindow").destroy();
			});
			$('#confirms_cancel').click(function() {
				$("#alert_message").html("");
				$("#code").val($("#code").attr("rel"));
		       	confirms_window.data("kendoWindow").destroy();
		    });
		    return false;
		}

		//check invoice_date < payment_due_date
		if(fieldname=='invoice_date' || fieldname=='payment_due_date'){
			var invoice_date = convert_date_to_num($('#invoice_date').val());
			var payment_due_date = convert_date_to_num($('#payment_due_date').val());
			if(invoice_date>payment_due_date){
				alerts('Message','<?php msg('DUE_DATE_AND_QUOTE_DATE');?>');
				$(this).css('color','#f00');
				return '';
			}else{
				$(this).css('color','#545353');
			}
		}

		var taxval;
		if(fieldname=='tax'){
			arrva = values.split('%');
			taxval = arrva[0];
			var arrvalue =  {"taxper":taxval};
			update_all_option('products',arrvalue,function(){
				reload_subtab('line_entry');
			});
			values = $('#'+fieldid+'Id').val();
		}

		if(fieldtype=='checkbox'){
			if($(this).is(':checked'))
				values = 1;
			else
				values = 0;
		}
		$(".jt_ajax_note").html("Saving...       ");
		jobtraq_loading();
		$.ajax({
			url: '<?php echo URL.'/'.$controller;?>/ajax_save',
			type:"POST",
			data: {field:fieldname,value:values,func:func,ids:ids},
			success: function(text_return){ //alert(text_return);
				jobtraq_loading('off');
				text_return = text_return.split("||");
					 if (text_return == "email_not_valid"){
							$("#email").addClass('error_input');
							ajax_note('Email not valid, please check email field!');
					 } else if( text_return == 'refresh' ) {
                       	location.reload();
					 } else{
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

						if( $.inArray(fieldname, ['invoice_status', 'payment_terms', 'invoice_date', 'invoice_type']) != -1 ) {
                            location.reload();
						}

                        if(fieldname=='name')
							$("form#other_record input#name").val(values);
						if(fieldname=='tax')
							save_field('taxval',taxval,'');

						if(fieldname=='company_name')
							change_tax_entry();
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
function closeAll(){
	$.ajax({
		url : "<?php echo URL.'/'.$controller.'/close_module' ?>",
		success : function(){
			location.reload();
		}
	});
}
function callPassword(){
	if($.trim($("#our_rep").val()) != "<?php echo $_SESSION['arr_user']['full_name'] ?>")
		$("#click_open_window_contactsour_rep").unbind("click").click(function(){
			var fieldname = $("#our_rep").attr("name");
			if(fieldname == 'our_rep'){
				var callBack = function(){
					$("#window_popup_contactsour_rep").data("kendoWindow").center();
					$("#window_popup_contactsour_rep").data("kendoWindow").open();
				};
				callPasswordPopup(callBack);
			}
		});
}
function callPasswordPopup(callBack,extraData){
	if( $("#confirms_window" ).attr("id") == undefined ){
		var html = '<div id="password_confirm" >';
			html +=	   '<div class="jt_box" style=" width:100%;">';
			html +=		  '<div class="jt_box_line"><div class=" jt_box_label " style=" width:25%;"></div><div id="alert_message"></div></div>';
			html +=	      '<div class="jt_box_line">';
			html +=	         '<div class=" jt_box_label " style=" width:25%;height: 75px">Password</div>';
			html +=	         '<div class="jt_box_field " style=" width:71%"><input name="password" id="password" class="input_1 float_left" type="password" value=""></div><input style="margin-top:2%" type="button" class="jt_confirms_window_cancel" id="confirms_cancel" value=" Cancel " /><input style="margin-top:2%" type="button" class="jt_confirms_window_ok" value=" Ok " id="confirms_ok" />';
			html +=	      '</div>';
			html +=	      '</div>';
			html +=	   '</div>';
			html +=	'</div>';
		$('<div id="confirms_window" style="width: 99%; padding: 0px; overflow: auto;">'+html+'</div>').appendTo("body");
	}
	var confirms_window = $("#confirms_window");
	confirms_window.kendoWindow({
		width: "355px",
		height: "100px",
		title: "Enter password",
		visible: false,
		activate: function(){
		  $('#password').focus();
		}
	});

	confirms_window.data("kendoWindow").center();
	confirms_window.data("kendoWindow").open();
	$("#confirms_ok").unbind("click");
	$("#confirms_ok").click(function() {
		$("#alert_message").html("");
		if( $("#password").val().trim()==''  ){
			$("#alert_message").html('<span style="margin-left: 5px; color:red; font-weight: 700">Password must not be empty.</span');
			$("#password").focus();
			return false;
		}
		$("#password").keypress(function(evt){
		var keyCode = (evt.which) ? evt.which : event.keyCode
		    if(keyCode==13)
		    	$("#confirms_ok").click();
		});
		var data = {};
		data['password'] = $("#password").val();
		if(typeof extraData == "object"){
			$.extend(data,extraData);
		}
		$.ajax({
			url: '<?php echo URL.'/'.$controller;?>/check_password',
			type:"POST",
			data: data,
			success: function(text_return){
				if(text_return=='wrong_pass'){
					ajax_note("Wrong password.");
				}else if(text_return=='success'){
					callBack();
				}
			}
		});
       	confirms_window.data("kendoWindow").destroy();
	});
	$('#confirms_cancel').click(function() {
		$("#alert_message").html("");
		if($("#code").attr("rel")!="" && $("#code").attr("rel")!= undefined)
			$("#code").val($("#code").attr("rel"));
       	confirms_window.data("kendoWindow").destroy();
    });
    return false;
}
function changePaymentLabel(value){
	if(value == 0){
		$("#payment_terms").val("DUE ON RECEIPT").css({"color":"red","font-weight":900});
	}
}
// Hàm dùng cho module Quotation :=========================================
/*
	after_choose_companies(ids,names,keys)
	after_choose_contacts(ids,names,keys)
	after_choose_jobs(ids,names,keys)
	save_address(arr,values,fieldid)
	save_address_pr(keys)
*/



/**
* Thay đổi tax entry khi province thay đổi
*/
function change_tax_entry(){

	$.ajax({
		url: '<?php echo URL.'/'.$controller;?>/change_tax_entry',
		type:"POST",
		success: function(jsondata){
			jobtraq_loading("off");
			$('#tax').val(jsondata['texttax']);
			$('#taxId').val(jsondata['keytax']);
			reload_subtab('line_entry');
			/*arrva = values.split('%');
			var arrvalue =  {"taxper":arrva[0]};
			update_all_option('products',arrvalue,function(){
				reload_subtab('line_entry');
			});*/
		}
	});
}




//xử lý sau khi chọn company
//cách xử lý mới
//dùng hàm save_data
function after_choose_companies(ids,names,keys){
	if(keys=='company_name'){
		$("#company_id").val(ids);
		$("#company_name").val(names);
		$("#md_company_name").html(names);
		$(".link_to_company_name").addClass('jt_link_on');
		jobtraq_loading();
		save_data('company_name',names,'',ids,function(arr_ret){
			jobtraq_loading("off");
			$(".link_to_contact_name").removeClass('jt_link_on');
			if(arr_ret['contact_id']!='')
				$(".link_to_contact_name").addClass('jt_link_on');
			$("#md_contact_name").html('');
			if(arr_ret['contact_name']!='')
				$("#md_contact_name").html(arr_ret['contact_name']);
			if(arr_ret['tax']!='' && arr_ret['taxtext']!=''){
				$("#tax").val(arr_ret['taxtext']);
			}
			window_popup('contacts', 'Specify Contact','contact_name','click_open_window_contactscontact_name',get_para_contact(),'force_re_install');
			reload_address('invoice_');
			reload_address('shipping_');
			reload_subtab('line_entry');
		});

	} else if(keys == 'search_company_name'){
        var data_json = JSON.parse($("#after_choose_companies"+ keys + ids).val());
		$(".products_company_name").val(data_json.name);
		$('#products_submit_change').click();
	} else if(keys == 'search_prefer_customer'){
        var data_json = JSON.parse($("#after_choose_companies"+ keys + ids).val());
		$("#products_prefer_customer").val(data_json.name);
		$('#products_submit_change').click();
	}
	$("#window_popup_companies" + keys).data("kendoWindow").close();
}

// xử lý sau khi chọn contact
//cách xử lý theo 2 hàm độc lập:
//Lấy data(get_data_form_module) + Save data (save_muti_field)

function after_choose_contacts(ids,names,keys){
	var mongoid,func;
	mongoid = $("#mongo_id").val();
	if(mongoid!='')
		func = 'update';
	else
		func = 'add';
	$(".k-window").fadeOut('slow');

	if(keys=='contact_name'){
		$("#contact_id").val(ids);
		$("#contact_name").val(names);
		$("#md_contact_name").html(names);
		$(".k-window").fadeOut('slow');
		$(".link_to_contact_name").addClass('jt_link_on');
		save_data('contact_name',names,'',ids,function(arr_ret){

			/*$(".link_to_contact_name").removeClass('jt_link_on');
			if(arr_ret['contact_id']!='')
				$(".link_to_contact_name").addClass('jt_link_on');
			$("#md_contact_name").html('');
			if(arr_ret['contact_name']!='')
				$("#md_contact_name").html(arr_ret['contact_name']);

			window_popup('contacts', 'Specify Contact','contact_name','click_open_window_contactscontact_name',get_para_contact(),'force_re_install');*/
			//reload_address('invoice_');
			//reload_address('shipping_');
		});

	}
	else if(keys=='our_rep'){
		$(".link_to_our_rep").attr("onclick", "window.location.assign('/jobtraq/contacts/entry/"+ids+"')");
		$("#our_rep_id").val(ids);
		$("#our_rep").val(names);
		var id_invoice = $("#mongo_id").val();
		$("#md_our_rep").html(names);
		$(".link_to_our_rep").addClass('jt_link_on');
		save_data('our_rep',names,'',ids);
		callPassword();

	}else if(keys=='our_csr'){
		$(".link_to_our_csr").attr("onclick", "window.location.assign('/jobtraq/contacts/entry/"+ids+"')");
		$("#our_csr_id").val(ids);
		$("#our_csr").val(names);
		$("#md_our_csr").html(names);
		$(".link_to_our_csr").addClass('jt_link_on');
		save_data('our_csr',names,'',ids);
	}
}

// xử lý sau khi chọn job,
function after_choose_jobs(ids,names,keys){
	if(keys=='job_name'){
		var module_from = 'Job';
		var arr = {
					"_id"	:"job_id",
					"name"	:"job_name",
					"no"	:"job_number"
				 }; //danh sách các field cần nhận về từ module jobs, và fields cần lưu
		$(".k-window").fadeOut('slow');
		$(".link_to_job_name").addClass('jt_link_on');
		save_data_form_to(module_from,ids,arr);
	}
}

// xử lý sau khi chọn job,
function after_choose_salesorders(ids,names,keys){
	if(keys=='salesorder_name'){
		var module_from = 'Salesorder';
		var arr = {
					"_id"	:"salesorder_id",
					"name":"salesorder_name",
					"code"	:"salesorder_number"
				 }; //danh sách các field cần nhận về từ module jobs, và fields cần lưu
		$(".k-window").fadeOut('slow');
		$(".link_to_salesorder_name").addClass('jt_link_on');
		save_data_form_to(module_from,ids,arr);
		reload_subtab('line_entry');
	}
}


function save_address(arr,values,fieldid,handleData){
	var	keys = arr[1].replace("]","");
	var keyups = keys.charAt(0).toUpperCase() + keys.slice(1);
	var opname = keys + "_address";
	var address_field = arr[2].replace("]","");
	var datas = new Object();
	if(address_field!='invoice_country' && address_field!='shipping_country' && address_field=='invoice_province_state' && address_field=='shipping_province_state'){
		datas[address_field] = values;

	//luu province
	}else if(address_field=='invoice_province_state' || address_field=='shipping_province_state'){
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

function view_product_option(proid){
	$.ajax({
		url: '<?php echo URL.'/'.$controller;?>/view_product_option',
		type:"POST",
		data: {proid:proid},
		success: function(text_return){
			build_popup(text_return,'Option of product');
		}
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
function clear_cache() {
	$.ajax({
		url : "<?php echo URL.'/'.$controller.'/clear_line_cache/' ?>"+$("#mongo_id").val(),
		success : function(result){
			if(result == "ok")
				location.reload();
			else
				alerts("Message",result);
		}
	})
}
</script>