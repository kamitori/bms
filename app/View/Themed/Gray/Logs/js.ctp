<?php echo $this->element('js_entry');?>
<script type="text/javascript">
$(function(){
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

		//check address
		var check_address = fieldname.split("[");
		if(check_address[0]=='data'){
			save_address(check_address,values,fieldid);
			return '';
		}

		//check invoice_date < paid_date
		if(fieldname=='invoice_date' || fieldname=='paid_date'){
			var invoice_date = convert_date_to_num($('#invoice_date').val());
			var paid_date = convert_date_to_num($('#paid_date').val());
			if(invoice_date>paid_date){
				alerts('Message','<?php msg('DUE_DATE_AND_QUOTE_DATE');?>');
				$(this).css('color','#f00');
				return '';
			}else{
				$(this).css('color','#545353');
			}
		}

		//cal again Payment due date
		if(fieldname=='invoice_date' || fieldname=='payment_terms'){
			var invoice_date = parseInt(convert_date_to_num($('#invoice_date').val()));
			var payment_terms = parseInt($('#payment_terms').val())*86400000;
				payment_terms = invoice_date + payment_terms;
			var payment_due_date = new Date(payment_terms);
				payment_due_date = payment_due_date.toDateString();
				payment_due_date = payment_due_date.split(" ");
				payment_due_date = payment_due_date[2]+" "+payment_due_date[1]+", "+payment_due_date[3];
			$('#payment_due_date').html(payment_due_date);
			save_field('payment_due_date',payment_due_date,ids);
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
					$("#"+modulename).val(text_return[0]);

					// change tittle, thay đổi tiêu đề của items
					<?php foreach($arr_settings['title_field'] as $ks=>$vls){?>
						titles[<?php echo $ks;?>] = '<?php echo $vls;?>';
					<?php }?>
					if(titles.indexOf(fieldname)!=-1){
						$("#md_"+fieldname).html(values);
						$(".md_center").html("-");
					}
					ajax_note("Saving...Saved !");

					// if status
					if(fieldname=='quotation_status')
						location.reload();
				}
			});

	});

	$(".jt_ajax_note").html('');

});

// Hàm dùng cho module Quotation :=========================================
/*
	after_choose_companies(ids,names,keys)
	after_choose_contacts(ids,names,keys)
	after_choose_jobs(ids,names,keys)
	save_address(arr,values,fieldid)
	save_address_pr(keys)
*/

// xử lý sau khi chọn company
//cách xử lý theo 2 hàm độc lập:
//Lấy data(get_data_form_module) + Save data (save_muti_field)

function after_choose_companies(ids,names,keys){
	if(keys=='company_name'){
		$("#company_id").val(ids);
		$("#company_name").val(names);
		$("#md_company_name").html(names);
		$(".k-window").fadeOut('slow');
		$(".link_to_company_name").addClass('jt_link_on');
		var module_from = 'Company';
		var address_key = 'invoice_';
		var arr = {
					"_id"	:"company_id",
					"name"	:"company_name",
					"phone"	: "phone",
					"email"	: "email",
					"our_rep":"our_rep",
					"our_rep_id":"our_rep_id",
					"our_csr":"our_csr",
					"our_csr_id":"our_csr_id",
					"contact_default_id"	:"contact_id",
					"addresses":"invoice_address"
				 };
		get_data_form_module(module_from,ids,arr,function(arr_data_from){ //lay data
			if(arr_data_from['contact_default_id']!=undefined){
				var contact_field = {"_id"	:"contact_id","first_name":"contact_name"};
				get_data_form_module('Contact',arr_data_from['contact_default_id'],contact_field,function(data_from_contact){
					arr_data_from['first_name'] = data_from_contact['first_name'];
					arr['first_name'] = 'contact_name';

					//set data address
					var newdata = new Object();
					if(arr_data_from['addresses'].length>0){
						for(var k in arr_data_from['addresses']){
							if(!arr_data_from['addresses'][k]['deleted'] && (arr_data_from['addresses'][k]['default']=='1' || arr_data_from['addresses'][k]['default']===true)){
								for(var m in arr_data_from['addresses'][k]){
									if(m=='deleted')
										newdata[m] = arr_data_from['addresses'][k][m];
									else
										newdata[address_key+m] = arr_data_from['addresses'][k][m];
								}
								break;
							}
						}
						var indata = new Object();
						indata[0]=newdata;
						arr_data_from['addresses'] = indata;
					}
					//save data from
					var jsonString = JSON.stringify(arr_data_from);
					save_muti_field(arr,jsonString,'',function(arr_return){
						for(var a in arr_return){
							if(a=='contact_name'){
								$("#"+a).val(arr_return[a]);
								$("#md_"+a).html(arr_return[a]);
							}else if(a!='invoice_address'){
								if($("#"+a).val()!=undefined)
									$("#"+a).val(arr_return[a]);
							}
						}
						reload_address(address_key,arr_return['_id']);
					});


				});

			}else{
					//set data address
					var newdata = new Object();
					if(arr_data_from['addresses'][0]!=undefined){
						for(var k in arr_data_from['addresses']){
							if(!arr_data_from['addresses'][k]['deleted'] && arr_data_from['addresses'][k]['default']=='1'){
								for(var m in arr_data_from['addresses'][k]){
									if(m=='deleted')
										newdata[m] = arr_data_from['addresses'][k][m];
									else
										newdata[address_key+m] = arr_data_from['addresses'][k][m];
								}
								break;
							}
						}
						var indata = new Object();
						indata[0]=newdata;
						arr_data_from['addresses'] = indata;
					}else{
						var arr_field = ["address_1","address_2","address_3","town_city","province_state","zip_postcode","country"];
						for (var i=0;i<arr_field.length;i++){
							$('#'+ChangeFormatId(address_key+arr_field[i])).val('');
						}
						if(arr_data_from['phone']==undefined)
							arr_data_from['phone'] = '';
						$("#phone").val(arr_data_from['phone']);
					}
					//save data from
					var jsonString = JSON.stringify(arr_data_from);
					save_muti_field(arr,jsonString,'',function(arr_return){
						for(var a in arr_return){
							if(a=='contact_name'){
								$("#"+a).val(arr_return[a]);
								$("#md_"+a).html(arr_return[a]);
							}else if(a!='invoice_address'){
								if($("#"+a).val()!=undefined)
									$("#"+a).val(arr_return[a]);
							}
						}
						reload_address(address_key);
					});
			}
		});
	 window_popup('contacts', 'Specify Contact','contact_name','click_open_window_contactscontact_name',get_para_contact(),'force_re_install');
	}
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
	if(keys=='contact_name'){

		$("#contact_id").val(ids);
		//$("#contact_name").val(names);
		$("#md_contact_name").html(names);
		$(".link_to_contact_name").addClass('jt_link_on');
		module_from = 'Contact';
		var address_key = 'shipping_';
		var arr = {
					"_id"	:"contact_id",
					"first_name"	:"contact_name",
					"mobile":"phone",
					"email":"email",
					"addresses":address_key+"address"
				 };
		get_data_form_module(module_from,ids,arr,function(arr_data_from){ //lay data
			if(arr_data_from['addresses'][0]!=undefined){
				var arrtemp = new Object();
				for(var i in arr_data_from['addresses'][0]){
					if(i!='deleted')
						arrtemp[address_key+i] = arr_data_from['addresses'][0][i];
					else
						arrtemp[i] = arr_data_from['addresses'][0][i];
				}
				var tempd = new Object();
				 tempd[0] = arrtemp;
				arr_data_from['addresses'] = tempd;
			}
			var jsonString = JSON.stringify(arr_data_from);
			save_muti_field(arr,jsonString,'',function(arr_return){
				for(var a in arr_return){
					if(a=='contact_name'){
						$("#"+a).val(arr_return[a]);
						$("#md_"+a).html(arr_return[a]);
					}else if(a!=address_key+'address'){
						$("#"+a).val(arr_return[a]);
					}
				}
				reload_address(address_key,arr_return['_id']);
			});
		});


	}else if(keys=='our_rep'){
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
		var module_from = 'Salesorder';
		var arr = {
					"_id"	:"salesorder_id",
					"heading":"salesorder_name",
					"no"	:"salesorder_number"
				 }; //danh sách các field cần nhận về từ module jobs, và fields cần lưu
		$(".k-window").fadeOut('slow');
		$(".link_to_salesorder_name").addClass('jt_link_on');
		save_data_form_to(module_from,ids,arr);
	}
}


function save_address(arr,values,fieldid){
	var	keys = arr[1].replace("]","");
	var keyups = keys.charAt(0).toUpperCase() + keys.slice(1);
	var opname = keys + "_address";
	var address_field = arr[2].replace("]","");
	var datas = new Object();
	if(address_field!='invoice_country' && address_field!='shipping_country' && address_field=='invoice_province_state' && address_field=='shipping_province_state'){
		datas[address_field] = values;

	}else if(address_field=='invoice_province_state' || address_field=='shipping_province_state'){
		var vtemp = $("#"+fieldid+'Id').val();
			datas[address_field] = $("#"+fieldid).val();//luu gia tri custom cua province
			datas[address_field+'_id'] = vtemp;
		$("#"+keyups+'ProvinceState').css('border','none');
		$("#"+keyups+'ProvinceState').css('border-bottom','1px solid #dddddd');
		//$("#"+keyups+'ProvinceState').focus();

	}else{
		vtemp = $("#"+fieldid+'Id').val();
		datas[address_field] = $("#"+fieldid).val();
		datas[address_field+'_id'] = vtemp;
		if(vtemp==39 || vtemp==249){
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
	save_option(opname,datas,idas,0,'',olds);
	ajax_note("Saving...Saved !");
	change_address_link(keys);
}


function save_address_pr(keys){
	var keyups = keys.charAt(0).toUpperCase() + keys.slice(1);
	var fieldid = keyups+'ProvinceState';
	var values = $("#"+fieldid).val();
	var arr = new Array();
	arr[1] = keys+']';
	arr[2] = keys+'_province_state]';

	$("#"+keyups+'ProvinceState').css('border','none');
	$("#"+keyups+'ProvinceState').css('border-bottom','1px solid #dddddd');
}

</script>