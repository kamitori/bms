<?php echo $this->element('js_entry');?>
<?php echo $this->element('js/permission_product_modules');?>
<script type="text/javascript">
$(function(){
	$("#country").change(function(){
		if($(this).val()!=40)
			$("#province").html('');
	});

	//default focus
	$("#code").focus();
	$(".menu_control li:first a").attr('href','javascript:check_add();');

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
			save_address(check_address,values,fieldid,function(){
				change_tax_entry();
			});
			return '';
		}
		fieldname = fieldname.replace("_cb","");
		if(fieldname == "shipping_cost")
			values = UnFortmatPrice(values);
		//check quotation_date < payment_due_date
		if(fieldname=='quotation_date' || fieldname=='payment_due_date'){
			var quotation_date = convert_date_to_num($('#quotation_date').val());
			var payment_due_date = convert_date_to_num($('#payment_due_date').val());
			if(quotation_date>payment_due_date){
				alerts('Message','<?php msg('DUE_DATE_AND_QUOTE_DATE');?>');
				$(this).css('color','#f00');
				return '';
			}else{
				$(this).css('color','#545353');
			}
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
							if(fieldname=='shipping_status')
								location.reload();

							if(fieldname=='tax')
								save_field('taxval',taxval,'');

							if(fieldname=='company_name')
								change_tax_entry();
						}
				}
			});

	});

	$(".jt_ajax_note").html('');
    var id_shipper = $("input#shipper_id").val();
     if(id_shipper!='')
     {
        $(".link_to_shipper").addClass('jt_link_on');
     }
     else
     {
        $(".link_to_shipper").removeClass('jt_link_on');
     }

     var id_signed_by_detail = $("input#signed_by_detail_id").val();
     if(id_signed_by_detail!='')
     {
        $(".link_to_signed_by_detail").addClass('jt_link_on');
     }
     else
     {
        $(".link_to_signed_by_detail").removeClass('jt_link_on');
     }


});

// Hàm dùng cho module Quotation :=========================================
/*
	after_choose_companies(ids,names,keys)
	after_choose_contacts(ids,names,keys)
	after_choose_jobs(ids,names,keys)
	save_address(arr,values,fieldid)
	save_address_pr(keys)
	get_para_contact()

*/


function check_add(){
	var arr = new Array();
	arr = ['Outgoing','Incoming',''];
	confirms3('Message',"Create an '<span class=\"bold\">Outgoing</span>' or '<span class=\"bold\">Incoming</span>' shipping/delivery?",arr,function(){
			add_new('shipping_type','Out'); //Outgoing
	},function(){
			add_new('shipping_type','In'); //Incoming
	},function(){
			//
	},function(){
			//
	});
}



//xử lý sau khi chọn company
//cách xử lý mới
//dùng hàm save_data
function after_choose_companies(ids,names,keys){
	mongoid = $("#mongo_id").val();
	if(keys=='company_name'){
		$("#company_id").val(ids);
		$("#company_name").val(names);
		$("#md_company_name").html(names);
		$(".link_to_company_name").addClass('jt_link_on');
		save_data('company_name',names,'',ids,function(arr_ret){

			$(".link_to_contact_name").removeClass('jt_link_on');
			if(arr_ret['contact_id']!='')
				$(".link_to_contact_name").addClass('jt_link_on');
			$("#md_contact_name").html('');
			if(arr_ret['contact_name']!='')
				$("#md_contact_name").html(arr_ret['contact_name']);

			window_popup('contacts', 'Specify Contact','contact_name','click_open_window_contactscontact_name',get_para_contact(),'force_re_install');
			reload_address('invoice_');
			reload_address('shipping_');
		});

	}
	else if(keys=='shipper' || keys=='shipper_tracking')
	{
		$("#shipper_id_tracking").val(ids);
		$("#shipper_tracking").val(names);

		$("#shipper_id").val(ids);
		$("#shipper").val(names);
		$("#md_shipping_name").html(names);
		$(".k-window").fadeOut('slow');
		$(".link_to_shipper").addClass('jt_link_on');
		 	save_data('shipper',names,mongoid,ids,function(arr_ret){
         	if(ids!='')
         		$(".link_to_shipper").addClass('jt_link_on');
         	else
            	$(".link_to_shipper").removeClass('jt_link_on');
         	ajax_note("Saving...Saved !");
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
		});

	}
	else if(keys=='our_rep'){
		$(".link_to_our_rep").attr("onclick", "window.location.assign('/jobtraq/contacts/entry/"+ids+"')");
		$("#our_rep_id").val(ids);
		$("#our_rep").val(names);
		$("#md_our_rep").html(names);
		$(".link_to_our_rep").addClass('jt_link_on');
		$.ajax({
			url: '<?php echo URL.'/'.$controller;?>/ajax_save',
			type:"POST",
			data: {field:'our_rep_id',value:ids,func:func,ids:mongoid},
			success: function(text_return){
				text_return = text_return.split("||");
				save_field('our_rep',names,text_return[0]);
			}
		});

	}else if(keys=='our_csr'){
		$(".link_to_our_csr").attr("onclick", "window.location.assign('/jobtraq/contacts/entry/"+ids+"')");
		$("#our_csr_id").val(ids);
		$("#our_csr").val(names);
		$("#md_our_csr").html(names);
		$(".link_to_our_csr").addClass('jt_link_on');
		$.ajax({
			url: '<?php echo URL.'/'.$controller;?>/ajax_save',
			type:"POST",
			data: {field:'our_csr_id',value:ids,func:func,ids:mongoid},
			success: function(text_return){
				text_return = text_return.split("||");
				save_field('our_csr',names,text_return[0]);
			}
		});

	}else if(keys=='signed_by_detail'){
            $("#signed_by_detail_id").val(ids);
    		$("#signed_by_detail").val(names);
    		 save_data('signed_by_detail',names,'',ids,function(arr_ret){
                 if(ids!='')
                 {
                    $(".link_to_signed_by_detail").addClass('jt_link_on');
                 }
                 else
                 {
                    $(".link_to_signed_by_detail").removeClass('jt_link_on');
                 }
                 ajax_note("Saving...Saved !");

             });
    }
    $(".k-window").fadeOut('slow');
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
		$(".k-window").fadeOut('slow');
		$(".link_to_salesorder_name").addClass('jt_link_on');
		save_data('salesorder',names,'',ids);
	}
}

// xử lý sau khi chọn job,
function after_choose_salesinvoices(ids,names,keys){
	if(keys=='salesinvoice_name'){
		var module_from = 'Salesinvoice';
		var arr = {
					"_id"	:"salesinvoice_id",
					"name":"salesinvoice_name",
					"code"	:"salesinvoice_number"
				 }; //danh sách các field cần nhận về từ module jobs, và fields cần lưu
		$(".k-window").fadeOut('slow');
		$(".link_to_salesinvoice_name").addClass('jt_link_on');
		save_data_form_to(module_from,ids,arr);
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

function get_para_contact(){
	var para = '?is_customer=1';
	var company_id = $("#company_id").val();
	var company_name = $("#company_name").val();
	if(company_id!='')
		para += '&company_id='+company_id;
	if(company_name!='')
		para += '&company_name='+company_name;
	return para;
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

</script>