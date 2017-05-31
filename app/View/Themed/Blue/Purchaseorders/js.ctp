<?php echo $this->element('js_entry');?>
<?php echo $this->element('js/permission_product_modules'); ?>
<script type="text/javascript">
$(function(){

	//default focus
	$("#name").focus();


	/*
	* Xu ly save, update
	*/
	$("input,select",".form_purchaseorders").change(function(){
		var fieldname 	= $(this).attr("name");
		var fieldid 	= $(this).attr("id");
		var fieldtype 	= $(this).attr("type");
		var values 		= $(this).val();
		var valueid;
		var titles = new Array();

		if(fieldname=='tax'){
			arrva = values.split('%');
			var arrvalue =  {"taxper":arrva[0]};
			update_all_option('products',arrvalue,function(){
				reload_subtab('line_entry');
			});
			values = $('#'+fieldid+'Id').val();
		}
		// Check address
		var check_address = fieldname.split("[");
		if(check_address[0]=='data'){
			fieldname = '';values = '';
			var ship_value = {
				"shipping_address_1"	:$("#ShippingAddress1").val(),
				"shipping_address_2"	:$("#ShippingAddress2").val(),
				"shipping_address_3"	:$("#ShippingAddress3").val(),
				"shipping_country"		:$("#ShippingCountry").val(),
				"shipping_country_id"	:$("#ShippingCountryId").val(),
				"shipping_default"		:true,
				"shipping_name"				:'Shipping Purchase Orders',
				"shipping_province_state"	: $("#ShippingProvinceState").val(),
				"shipping_province_state_id": $("#ShippingProvinceStateId").val(),
				"shipping_town_city"		: $("#ShippingTownCity").val(),
				"shipping_zip_postcode"		: $("#ShippingZipPostcode").val()
			};
			//save address
			save_option('shipping_address',ship_value,0,0,'line_entry','update',function(newarr){
				newarr = JSON.parse(newarr);
				if(newarr.tax!=undefined)
					$("#tax").val(newarr.tax);
				if(newarr.tax_key!=undefined)
					$("#taxId").val(newarr.tax_key);
				reload_subtab('line_entry');
			});

		//nếu là select box
		}else if($('#'+fieldname).parent().attr('class')=='combobox'){
			values = $("#"+fieldname+"Id").val();
			valuesid = $("#"+fieldname).val();
		}

		// Kiểm tra tax
		//pur_order_get_tax( fieldname ); /// (***)



		//nếu là date
		if(fieldname=='purchord_date' || fieldname=='required_date' || fieldname=='delivery_date' || fieldname=='email'){
			var idss = $("#mongo_id").val();
			$.ajax({
				url: '<?php echo URL.'/'.$controller;?>/ajax_save',
				type:"POST",
				data: {field:fieldname,value:values,func:'update',ids:idss},
				success: function(text_return){
					if(text_return == "email_not_valid"){
                        $("#email").addClass('error_input');
                        ajax_note('Email not valid, please check email field!');

                    }else{
						$("#email").removeClass('error_input');
					}
					return true;
				}
			});
			return true;
		}

		/**
		* lưu dữ liệu và hiển thị
		*/

		if(fieldname!=''){
			$(".jt_ajax_note").html("Saving...       ");
			save_data(fieldname,values,'',valueid,function(ret){
				// change tittle, thay đổi tiêu đề của items
				<?php foreach($arr_settings['title_field'] as $ks=>$vls){?>
					titles[<?php echo $ks;?>] = '<?php echo $vls;?>';
				<?php }?>
				if(titles.indexOf(fieldname)!=-1){
					$("#md_"+fieldname).html(values);
				}

				if(fieldname=='purchase_orders_status')
						location.reload();

				if(fieldname=='company_name')
						change_tax_entry();

				if($('#'+fieldname).parent().attr('class')=='combobox'){
					$("#"+fieldname+"Id").val(values);
					$("#"+fieldname).val(valuesid);
				}

			});
		}


	});


	$(".jt_ajax_note").html('');

});



/**
* Thay đổi tax entry khi province thay đổi
*/
function change_tax_entry(){
	$.ajax({
		url: '<?php echo URL.'/'.$controller;?>/change_tax_entry',
		dataType: "json",
		type:"POST",
		success: function(jsondata){
			$('#tax').val(jsondata['texttax']);
			$('#taxId').val(jsondata['keytax']);
			reload_subtab('line_entry');
		}
	});
}




function pur_order_get_tax(fieldname){
	var ShippingProvinceStateId = $("#ShippingProvinceStateId").val();
	var tax = $("#taxId").val();
	if( ShippingProvinceStateId != tax ){
		var div_contain = $("#tax").parent("div");
		var ul_combobox = $("ul", div_contain);
		$("ul li", div_contain).each(function() {
		});
		// nếu bằng thì chỉ cần thay đổi tax, hệ thống sẽ chạy tiếp mà ko cần auto, do (***) ở trên
		if(fieldname != 'ShippingProvinceState'){
			// goi ajax o day
		}
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


function after_choose_companies(ids,names,keys){
	var jsondata = $("#after_choose_companies"+keys+ids).val();
		jsondata = JSON.parse(jsondata);
		names = jsondata.name;

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

			// BaoNam
			// reload_payment_term_tax_company(ids);
			reload_subtab('line_entry');
		});


	}else if(keys=='ship_to_company_name'){
		$("#window_popup_companiesship_to_company_name").data("kendoWindow").close();
		$("#ship_to_company_id").val(ids);
		$("#ship_to_company_name").val(names);
		$(".link_to_ship_to_company_name").addClass('jt_link_on');
		save_data('ship_to_company_name',names,'',ids,function(arr_ret){
			reload_address('shipping_');
			//thay link Company va contact
			//$(".link_to_ship_to_contact_name").removeClass('jt_link_on');
			//if(arr_ret['ship_to_contact_id']!='')
				//$(".link_to_ship_to_contact_name").addClass('jt_link_on');
			//rebuild popup contacts
			window_popup('contacts', 'Specify Contact','ship_to_contact_name','click_open_window_contactsship_to_contact_name',get_para_ship_contact(),'force_re_install');
		});


	}else if(keys=='shipper_company_name'){
		$("#window_popup_companiesshipper_company_name").data("kendoWindow").close();
		$("#shipper_company_id").val(ids);
		$("#shipper_company_name").val(names);
		$(".link_to_shipper_company_name").addClass('jt_link_on');
		save_data('shipper_company_name',names,'',ids);
	}
	$("#window_popup_companies" + keys).data("kendoWindow").close();
}


// xử lý sau khi chọn company
//cách xử lý theo 2 hàm độc lập:
//Lấy data(get_data_form_module) + Save data (save_muti_field)
function after_choose_companies111(ids,names,keys){
if(keys=='company_name'){
	$("#company_id").val(ids);
	$("#company_name").val(names);
	$("#md_company_name").html(names);
	$(".k-window").fadeOut('slow');
	$("#email").val('');
	var module_from = 'Company';
	var address_key = 'invoice_';
	var arr = {
		"_id"	:"company_id",
		"name"	:"company_name",
		"phone"	: "phone",
		"email"	: "email",
		"fax"	: "fax",
		"contact_default_id"	:"contact_id",
		"addresses":"invoice_address"
	};

	// BaoNam: refesh chọn product cho company khác nếu đang đứng ở line-entry
	if( $("#line_entry").hasClass("active") ){ // nếu line-entry là tab đang active
		$("div.choice_code", "#load_subtab").each(function(){
			var div = $(this);
			var parameter_get = "";
	        if( $("#company_id").val() != "" ){
	            parameter_get += "?company_id=" + $("#company_id").val() + "&company_name=" + $("#company_name").val();
	            window_popup("products", "Specify Products",div.attr("rel"),div.attr("id"), parameter_get, "force_re_install");
	        }
		});
	}

	//$("#contact_name").val("");
	//$("#contact_id").val("");

	get_data_form_module(module_from,ids,arr,function(arr_data_from){ //lay data

		if(arr_data_from['contact_default_id']!=undefined){

			var contact_field = {"_id"	:"contact_id"
				,"first_name":"contact_name"
				,"last_name":"contact_last_name"};
			get_data_form_module('Contact',arr_data_from['contact_default_id'],contact_field,function(data_from_contact){
				arr_data_from['first_name'] = data_from_contact['first_name'];
				arr_data_from['last_name'] = data_from_contact['last_name'];
				arr['first_name'] = 'contact_name';
				arr['last_name'] = 'contact_last_name';
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


				var jsonString = JSON.stringify(arr_data_from);
				save_muti_field(arr,jsonString,'',function(arr_return){

					for(var a in arr_return){
						if(a=='contact_name'){
							$("#"+a).val(arr_return[a]+' '+arr_return['contact_last_name']);
							$("#md_"+a).html(arr_return[a]+' '+arr_return['contact_last_name']);
						}else if(a!='invoice_address'){
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
						$("#"+a).val(arr_return[a]+' '+arr_return['contact_last_name']);
						$("#md_"+a).html(arr_return[a]+' '+arr_return['contact_last_name']);
					}else if(a!='invoice_address'){
						$("#"+a).val(arr_return[a]);
					}
				}
				reload_address(address_key,arr_return['_id']);
			});





		}
	});

	window_popup('contacts', 'Specify Contact','contact_name','click_open_window_contactscontact_name',get_para_contact(),'force_re_install');



		}else if(keys=='ship_to_company_name'){

		var module_from = 'Company';
		var mongo_id=$("#mongo_id").val();
		var arr = {
					"_id"	:"ship_to_company_id",
					"name"	:"ship_to_company_name"

				 }; //danh sách các field cần nhận về từ module jobs, và fields cần lưu
		$(".k-window").fadeOut('slow');

		save_data_form_to(module_from,ids,arr,function(data_return){
            for(var i in arr){
                keys = arr[i];
                if(typeof(data_return[keys])!= undefined){
                    $("#"+keys).val(data_return[keys]);
                }else
                    $("#"+keys).val('');

            }

            window_popup('contacts', 'Specify Contact','ship_to_contact_name','click_open_window_contactsship_to_contact_name',get_para_ship_contact(),'force_re_install');
            window_popup('contacts', 'Specify Contact','received_by_contact_name','click_open_window_contactsreceived_by_contact_name',get_para_ship_contact(),'force_re_install');

		     $("#ship_to_contact_name").val('');
		     $("#received_by_contact_name").val('');
             save_field('ship_to_contact_name','',mongo_id);
             save_field('received_by_contact_name','',mongo_id);
		});



                var address_key = 'shipping_';
        		var arr1 = {
                					"addresses":address_key+"address"
                				 };
        		get_data_form_module(module_from,ids,arr1,function(arr_data_from){ //lay data
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
                			save_muti_field(arr1,jsonString,'',function(arr_return){
                				for(var a in arr_return){
                					if(a=='contact_name'){
                						$("#"+a).val(arr_return[a]+' '+arr_return['contact_last_name']);
                						$("#md_"+a).html(arr_return[a]+' '+arr_return['contact_last_name']);
                					}else if(a!=address_key+'address'){
                						$("#"+a).val(arr_return[a]);
                					}
                				}
                				reload_address(address_key,arr_return['_id']);
                			});
                		});



	}else if(keys=='shipper_company_name'){
	    var module_from = 'Company';
		var mongo_id=$("#mongo_id").val();
		var arr = {
        					"_id"	:"shipper_company_id",
        					"name"	:"shipper_company_name",
        				 };
        $(".k-window").fadeOut('slow');
	    save_data_form_to(module_from,ids,arr,function(data_return){
            for(var i in arr){
                keys = arr[i];
                if(typeof(data_return[keys])!= 'undefined')
                    $("#"+keys).val(data_return[keys]);
                else
                    $("#"+keys).val('');

            }

		});
		$(".link_to_shipper_company_name").attr("onclick", "window.location.assign('/companies/entry/"+ids+"')");

	}
}




function after_choose_contacts(ids,names,keys){

	var jsondata = $("#after_choose_contacts"+keys+ids).val();
	jsondata = JSON.parse(jsondata);
	names = jsondata.full_name;
	if(keys=='contact_name'){
		$("#window_popup_contactscontact_name").data("kendoWindow").close();
		$("#contact_id").val(ids);
		$("#contact_name").val(names);
		$("#md_contact_name").html(names);
		$(".link_to_contact_name").addClass('jt_link_on');
		save_data('contact_name',names,'',ids);

	}else if(keys=='our_rep'){
		$("#window_popup_contactsour_rep").data("kendoWindow").close();
		$("#our_rep_id").val(ids);
		$("#our_rep").val(names);
		$("#md_our_rep").html(names);
		$(".link_to_our_rep").addClass('jt_link_on');
		save_data('our_rep',names,'',ids);

	}else if(keys=='ship_to_contact_name'){
		$("#window_popup_contactsship_to_contact_name").data("kendoWindow").close();
		$("#ship_to_contact_id").val(ids);
		$("#ship_to_contact_name").val(names);
		$(".link_to_ship_to_contact_name").addClass('jt_link_on');
		save_data('ship_to_contact_name',names,'',ids);

	}else if(keys=='received_by_contact_name'){
		$("#window_popup_contactsreceived_by_contact_name").data("kendoWindow").close();
		$("#received_by_contact_id").val(ids);
		$("#received_by_contact_name").val(names);
		$(".link_to_received_by_contact_name").addClass('jt_link_on');
		save_data('received_by_contact_name',names,'',ids);
	}
}



// xử lý sau khi chọn contact
//cách xử lý theo 2 hàm độc lập:
//Lấy data(get_data_form_module) + Save data (save_muti_field)

function after_choose_contacts_backup(ids,names,keys){
	var mongoid,func;
	mongoid = $("#mongo_id").val();
	if(mongoid!='')
		func = 'update';
	else
		func = 'add';
	if(keys=='contact_name'){

		$("#contact_id").val(ids);
		$("#contact_name").val(names);
		$("#md_contact_name").html(names);
		module_from = 'Contact';
		var address_key = 'shipping_';
		var arr = {
					"_id"	:"contact_id",
					"first_name":"contact_name",
					"last_name":"contact_last_name",
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
						$("#"+a).val(arr_return[a]+' '+arr_return['contact_last_name']);
						$("#md_"+a).html(arr_return[a]+' '+arr_return['contact_last_name']);
					}else if(a!=address_key+'address'){
						$("#"+a).val(arr_return[a]);
					}
				}
				//reload_address(address_key,arr_return['_id']);
			});
		});


	}else if(keys=='our_rep'){
		$("#our_rep_id").val(ids);
		$("#our_rep").val(names);
		$("#md_our_rep").html(names);
		$.ajax({
			url: '<?php echo URL.'/'.$controller;?>/ajax_save',
			type:"POST",
			data: {field:'our_rep_id',value:ids,func:func,ids:mongoid},
			success: function(text_return){
				text_return = text_return.split("||");
				save_field('our_rep',names,text_return[0]);
			}
		});
		$(".link_to_our_rep").attr("onclick", "window.location.assign('/contacts/entry/"+ids+"')");

	}else if(keys=='our_csr'){
		$(".link_to_our_csr").attr("onclick", "window.location.assign('/jobtraq/contacts/entry/"+ids+"')");
		$("#our_csr_id").val(ids);
		$("#our_csr").val(names);
		$("#md_our_csr").html(names);
		$.ajax({
			url: '<?php echo URL.'/'.$controller;?>/ajax_save',
			type:"POST",
			data: {field:'our_csr_id',value:ids,func:func,ids:mongoid},
			success: function(text_return){
				text_return = text_return.split("||");
				save_field('our_csr',names,text_return[0]);
			}
		});

	}else if(keys=='ship_to_contact_name'){

		$("#ship_to_contact_id").val(ids);
		$("#ship_to_contact_name").val(names);
        module_from = 'Contact';
		var address_key = 'shipping_';
		var arr = {
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
        						$("#"+a).val(arr_return[a]+' '+arr_return['contact_last_name']);
        						$("#md_"+a).html(arr_return[a]+' '+arr_return['contact_last_name']);
        					}else if(a!=address_key+'address'){
        						$("#"+a).val(arr_return[a]);
        					}
        				}
        				reload_address(address_key,arr_return['_id']);
        			});
        		});
		$.ajax({
			url: '<?php echo URL.'/'.$controller;?>/ajax_save',
			type:"POST",
			data: {field:'ship_to_contact_id',value:ids,func:func,ids:mongoid},
			success: function(text_return){
				text_return = text_return.split("||");
				save_field('ship_to_contact_name',names,text_return[0]);
			}
		});
		$(".link_to_ship_to_contact_name").attr("onclick", "window.location.assign('/contacts/entry/"+ids+"')");

	}else if(keys=='received_by_contact_name'){

		 $("#received_by_contact_id").val(ids);
         		$("#received_by_contact_name").val(names);

         		$.ajax({
         			url: '<?php echo URL.'/'.$controller;?>/ajax_save',
         			type:"POST",
         			data: {field:'received_by_contact_id',value:ids,func:func,ids:mongoid},
         			success: function(text_return){
         				text_return = text_return.split("||");
         				save_field('received_by_contact_name',names,text_return[0]);
         			}
         		});
        		$(".link_to_received_by_contact_name").attr("onclick", "window.location.assign('/contacts/entry/"+ids+"')");

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
		save_data_form_to(module_from,ids,arr);
	}
}


// xử lý sau khi chọn job,
function after_choose_salesorders(ids,names,keys){
	if(keys=='salesorder_name'){
		var jsondata = $("#after_choose_salesorders"+keys+ids).val();
		jsondata = JSON.parse(jsondata);
		names = jsondata.name;
		names += '_@_'+jsondata.code;
		$(".k-window").fadeOut('slow');
		save_data('salesorder_name',names,'',ids,function(arr_ret){
			$("#salesorder_number").val(arr_ret['salesorder_number']);
			$("#salesorder_name").val(arr_ret['salesorder_name']);
			$("#salesorder_id").val(arr_ret['salesorder_id']);
			$(".link_to_salesorder_name").addClass('jt_link_on');
		});
	}
}


function after_choose_addresses(ids,names,keys){
	var ship_value = {
			"shipping_address_1"	:$("#window_popup_addresses_"+names+"_address_1_"+ids+keys).val(),
			"shipping_address_2"	:$("#window_popup_addresses_"+names+"_address_2_"+ids+keys).val(),
			"shipping_address_3"	:$("#window_popup_addresses_"+names+"_address_3_"+ids+keys).val(),
			"shipping_country"		:$("#window_popup_addresses_"+names+"_country_"+ids+keys).val(),
			"shipping_country_id"	:$("#window_popup_addresses_"+names+"_country_id_"+ids+keys).val(),
			"shipping_default"		:true,
			"shipping_name"				:'Shipping Purchase Orders',
			"shipping_province_state"	: $("#window_popup_addresses_"+names+"_province_state_"+ids+keys).val(),
			"shipping_province_state_id": $("#window_popup_addresses_"+names+"_province_state_id_"+ids+keys).val(),
			"shipping_town_city"		: $("#window_popup_addresses_"+names+"_town_city_"+ids+keys).val(),
			"shipping_zip_postcode"		: $("#window_popup_addresses_"+names+"_zip_postcode_"+ids+keys).val()
		};
		//save address
		save_option('shipping_address',ship_value,0,0,'line_entry','update',function(newarr){
			newarr = JSON.parse(newarr);
			if(newarr.tax!=undefined)
				$("#tax").val(newarr.tax);
			if(newarr.tax_key!=undefined)
				$("#taxId").val(newarr.tax_key);
			reload_subtab('line_entry');
			$("#ShippingAddress1").val(ship_value['shipping_address_1']);
			$("#ShippingAddress2").val(ship_value['shipping_address_2']);
			$("#ShippingAddress3").val(ship_value['shipping_address_3']);
			$("#ShippingCountry").val(ship_value['shipping_country']);
			$("#ShippingCountryId").val(ship_value['shipping_country_id']);
			$("#ShippingProvinceState").val(ship_value['shipping_province_state']);
			$("#ShippingProvinceStateId").val(ship_value['shipping_province_state_id']);
			$("#ShippingTownCity").val(ship_value['shipping_town_city']);
			$("#ShippingZipPostcode").val(ship_value['shipping_zip_postcode']);

		});
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

function get_para_ship_contact()
{
    var para = '?t=1';
	var company_id = $("#ship_to_company_id").val();
	var company_name = $("#ship_to_company_name").val();
	if(company_id!='')
		para += '&company_id='+company_id;
	if(company_name!='')
		para += '&company_name='+company_name;
	return para;

}

function get_where_com_po(){
	var parameter_get = "";
	var company_id = $("#company_id").val();
	var company_name = $("#company_name").val();
	parameter_get += "?ispo=1&products_company_id=" + company_id + "&products_company_name=" + company_name+ "&products_product_type=Vendor Stock";
	if(company_id==''){
		alerts('Message','<?php echo "Specified Company is not a Supplier System.";//msg('PO_CHOICE_SUPPLIER');?>',function(){
			return '';
		},function(){
			return '';
		});
	}else
		return parameter_get;
}


</script>