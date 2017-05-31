<?php echo $this->element('js_entry');?>
<script type="text/javascript">
	$(function() {
		load_communication();
		$("#click_open_window_contactsour_rep").unbind("click").click(function(){
/*			if(confirm("use password")){
				var window_popup = $("#window_popup_contactsour_rep");
				window_popup.data("kendoWindow").center();
				window_popup.data("kendoWindow").open();
			}*/
			var fieldname = $("#our_rep").attr("name");
			var func = '';
			if(ids!='')
				func = 'update';
			else
				func = 'add';
			modulename = 'mongo_id';
			var ids = $("#"+modulename).val();
			if(fieldname == 'our_rep'){
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
					var values = {};
					values["password"] = $("#password").val();
					$.ajax({
						url: '<?php echo URL.'/'.$controller;?>/ajax_save',
						type:"POST",
						data: {field:fieldname,value:values,func:func,ids:ids},
						success: function(text_return){
							if(text_return=='wrong_pass'){
								ajax_note("Wrong password.");
							}else if(text_return=='success'){
								var window_popup = $("#window_popup_contactsour_rep");
								window_popup.data("kendoWindow").center();
								window_popup.data("kendoWindow").open();
							}
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
		});

		$("#companies_form_auto_save input,#companies_form_auto_save select").change(function() {
			var fixkendo = $(this).attr('class');
			var fieldname = $(this).attr("name");
			var fieldid = $(this).attr("id");
			var fieldtype = $(this).attr("type");
			var ids = $("#mongo_id").val();
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
					reload_subtab('addresses');
				});
				return '';
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
					success: function(text_return){
						$(".jt_ajax_email").addClass("hidden");
						text_return = text_return.split("||");
							 if (text_return == "email_not_valid"){
									$("#email").addClass('error_input');
									ajax_note('Email not valid, please check email field!');
							 }else{
								$("#email").removeClass('error_input');
								// change tittle, thay đổi tiêu đề của items
								<?php foreach($arr_settings['title_field'] as $ks=>$vls){?>
									titles[<?php echo $ks;?>] = '<?php echo $vls;?>';
								<?php } ?>
								if(titles.indexOf(fieldname)!=-1){
									$("#md_"+fieldname).html(values);
									$(".md_center").html("-");
								}
								ajax_note("Saving...Saved !");

							}
						if(fieldname=="is_customer" || fieldname=="is_supplier"){
							if($("#orders").attr('class') == 'active')
								reload_subtab('orders');
							else if($("#products").attr('class') == 'active')
								reload_subtab('products');
						}

					}
				});

		});
		$(".jt_ajax_note").html('');

		$("#companies_form_auto_save").on("change", "#CompanyProvinceState", function() {
			var fixkendo = $(this).attr('class');
			var fieldname = $(this).attr("name");
			var fieldid = $(this).attr("id");
			var fieldtype = $(this).attr("type");
				modulename = 'mongo_id';
			var ids = $("#mongo_id").val();
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
					reload_subtab('addresses');
				});
				return '';
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
					success: function(text_return){
						$(".jt_ajax_email").addClass("hidden");
						text_return = text_return.split("||");
							 if (text_return == "email_not_valid"){
									$("#email").addClass('error_input');
									ajax_note('Email not valid, please check email field!');
							 }else{
								$("#email").removeClass('error_input');
								// change tittle, thay đổi tiêu đề của items
								<?php foreach($arr_settings['title_field'] as $ks=>$vls){?>
									titles[<?php echo $ks;?>] = '<?php echo $vls;?>';
								<?php } ?>
								if(titles.indexOf(fieldname)!=-1){
									$("#md_"+fieldname).html(values);
									$(".md_center").html("-");
								}
								ajax_note("Saving...Saved !");

							}
						if(fieldname=="is_customer" || fieldname=="is_supplier"){
							if($("#orders").attr('class') == 'active')
								reload_subtab('orders');
							else if($("#products").attr('class') == 'active')
								reload_subtab('products');
						}

					}
				});
		});



	});

	function save_address(arr,values,fieldid,handleData){
		var	keys = arr[1].replace("]","");
		var keyups = keys.charAt(0).toUpperCase() + keys.slice(1);
		var address_field = arr[2].replace("]","");
		var fieldid = keyups + address_field.charAt(0).toUpperCase() + address_field.slice(1);
			fieldid = fieldid.replace("_","");
		var datas = new Object();
		if(address_field!='country'  && address_field!='province_state'){
			datas[address_field] = values;

		//luu province  hoac country
		} else if(address_field=='province_state') {
			var vtemp = $("#CompanyProvinceStateId").val();
			datas[address_field] = $("#CompanyProvinceState").val();//luu gia tri custom cua province
			datas[address_field+'_id'] = vtemp;
			//$("#"+fieldid).focus();
		}
		else{
			var vtemp = $("#"+fieldid+'Id').val();
			datas[address_field] = $("#"+fieldid).val();//luu gia tri custom cua province
			datas[address_field+'_id'] = vtemp;
			//$("#"+fieldid).focus();
		}

		var olds = $("#invoice_address").val();
		var idas = $("input[type=checkbox]:checked","#container_addresses").attr("id");
		if (idas == undefined ) {
			idas = $("#address_choose").val();
		} else {
			idas = idas.split("_");
			idas = idas[idas.length -1];
		}

		if(olds!=''){
			olds = 'update';
		}else{
			olds = 'add';
			$("#invoice_address").val(values+',');
		}

		save_option('addresses',datas,idas,0,'',olds,function(arr_return){
			if(handleData!=undefined)
				handleData(arr_return);
			//save tax
		});
		ajax_note("Saving...Saved !");
	}
	function save_address_pr(keys){
		var keyups = keys.charAt(0).toUpperCase() + keys.slice(1);  // = Company
		var fieldid = keyups+'ProvinceState';  // = CompanyProvinceState
		var values = $("#"+fieldid).val(); // New York
		var arr = new Array();
		arr[1] = keys+']';
		arr[2] = 'province_state]'; // arr = [undefined, "company]", "province_state]"]
		save_address(arr,values,fieldid);

		$("#"+keyups+'ProvinceState').css('border','none');
		$("#"+keyups+'ProvinceState').css('border-bottom','1px solid #dddddd');
	}


	function companies_update_entry_header() {
		$("#company_name_header").html($("#CompanyName").val());

		if ($.trim($("#CompanyPhone").val()).length > 0) {
			$("#company_phone_header").html($("#CompanyPhone").val());
			$("#company_phoneprefix_header").show();
		}
	}

	function companies_auto_save_entry(object) {
		if ($.trim($("#CompanyHeading").val()) == "") {
			$("#CompanyHeading").val("#" + $("#CompanyNo").val() + "-" + $("#CompanyCompanyName").val());
		}

		// checkbox customer or employee
		if (object != undefined) {
			if ($(object).attr("id") == "CompanyIsCustomer") {
				$("#contacts").click();
			} else if ($(object).attr("id") == "CompanyIsSupplier") {
				$("#contacts").click();
			}
		}

		companies_update_entry_header();
		$("form :input", "#companies_form_auto_save").removeClass('error_input').removeClass('error_color ');
		$(".jt_ajax_note").hide();

		companies_adddress_update_addresses_in_subtab();

		$.ajax({
			url: '<?php echo URL; ?>/companies/auto_save',
			timeout: 15000,
			type: "post",
			data: $("form", "#companies_form_auto_save").serialize(),
			success: function(html) {

				if ($.trim(html) == "ref_no_existed") {
					$("#CompanyNo").addClass('error_color');
					alerts('Error', 'This "no" existed');

				} else if (html == "email_not_valid") {
					$("#CompanyEmail").addClass('error_input');
					ajax_note_set('Email not valid, please check email field!');

				} else if (html == "company_exists") {
					$("#CompanyName").addClass('error_input');
					ajax_note_set('This company existed!');

				} else if (html == "company_no_exists") {
					$("#CompanyNo").addClass('error_input');
					ajax_note_set('This no existed!');

				} else if (html != "ok") {
					alerts('Error', html);
				}
				 // view log when debug
			}
		});
	}

	function companies_adddress_update_addresses_in_subtab(){

		var CompanyAddressesDefaultKey = $("#CompanyAddressesDefaultKey").val();

		// kiểm tra xem tab Addresses có active không
		if( $("#companies_addresses_" + CompanyAddressesDefaultKey).attr("id") != undefined ){

			var contain = $("#companies_addresses_" + CompanyAddressesDefaultKey);
			var contain_entry_panel = $("#companies_form_auto_save");
			// thay đổi địa chỉ default

			$("#AddressAddress1", contain).val($("#DefaultAddress1", contain_entry_panel).val());
			$("#AddressAddress2", contain).val($("#DefaultAddress2", contain_entry_panel).val());
			$("#AddressAddress3", contain).val($("#DefaultAddress3", contain_entry_panel).val());
			$("#AddressTownCity", contain).val($("#DefaultTownCity", contain_entry_panel).val());
			$("#AddressZipPostcode", contain).val($("#DefaultZipPostcode", contain_entry_panel).val());

			// kiểm tra xem có đổi country không để load lại danh sách tỉnh thành
			if( $("#DefaultCountryId", contain_entry_panel).val() != $("#Address" +CompanyAddressesDefaultKey+ "CountryId", contain).val() ){
				change_province(CompanyAddressesDefaultKey, $("#DefaultCountryId", contain_entry_panel).val());
				setTimeout("companies_adddress_update_addresses_in_subtab_country_province(" +CompanyAddressesDefaultKey+ ")", 1800);

			}else{
				companies_adddress_update_addresses_in_subtab_country_province(CompanyAddressesDefaultKey);
			}
		}
	}

	function companies_adddress_update_addresses_in_subtab_country_province(CompanyAddressesDefaultKey){
		var contain = $("#companies_addresses_" + CompanyAddressesDefaultKey);
		var contain_entry_panel = $("#companies_form_auto_save");
		$("#Address" +CompanyAddressesDefaultKey+ "Country", contain).val($("#DefaultCountry", contain_entry_panel).val());
		$("#Address" +CompanyAddressesDefaultKey+ "CountryId", contain).val($("#DefaultCountryId", contain_entry_panel).val());
		$("#Address" +CompanyAddressesDefaultKey+ "ProvinceState", contain).val($("#DefaultProvinceState", contain_entry_panel).val());
		$("#Address" +CompanyAddressesDefaultKey+ "ProvinceStateId", contain).val($("#DefaultProvinceStateId", contain_entry_panel).val());
	}

	function after_choose_contacts(ids,names,keys){
		var mongoid,func;
		mongoid = $("#mongo_id").val();
		if(mongoid!='')
			func = 'update';
		else
			func = 'add';

		if(keys=='our_rep'){
			$("#window_popup_contactsour_rep").data("kendoWindow").close();
			$(".link_to_our_rep").attr("onclick", "window.location.assign('<?php echo URL;?>/contacts/entry/"+ids+"')");
			$("#our_rep_id").val(ids);
			$("#our_rep").val(names);
			$("#md_our_rep").html(names);
			$(".link_to_our_rep").addClass('jt_link_on');
			save_data('our_rep',names,'',ids);

		}else if(keys=='our_csr'){
			$("#window_popup_contactsour_csr").data("kendoWindow").close();
			$(".link_to_our_csr").attr("onclick", "window.location.assign('<?php echo URL;?>/contacts/entry/"+ids+"')");
			$("#our_csr_id").val(ids);
			$("#our_csr").val(names);
			$("#md_our_csr").html(names);
			$(".link_to_our_csr").addClass('jt_link_on');
			save_data('our_csr',names,'',ids);
		}
	}

/*	function change_tax_entry(){

		$.ajax({
			url: '<?php echo URL.'/'.$controller;?>/change_tax_entry',
			dataType: "json",
			type:"POST",
			success: function(jsondata){
				console.log('tesing:');
				console.log(jsondata);
				$('#tax').val(jsondata['texttax']);
				$('#taxId').val(jsondata['keytax']);
				reload_subtab('line_entry');
			}
		});
	}*/

	function after_choose_companies(ids,names,keys){
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

				if(arr_ret['tax']!='' && arr_ret['taxtext']!=''){
					$("#tax").val(arr_ret['taxtext']);
				}

				window_popup('contacts', 'Specify Contact','contact_name','click_open_window_contactscontact_name',get_para_contact(),'force_re_install');
				reload_address('invoice_');
				reload_address('shipping_');

				// BaoNam
				reload_payment_term_tax_company(ids);
				reload_subtab('line_entry');
			});

		} else if(keys == 'search_prefer_customer'){
	        var data_json = JSON.parse($("#after_choose_companies"+ keys + ids).val());
			$("#products_prefer_customer").val(data_json.name);
			$('#products_submit_').click();
		}
		$("#window_popup_companies" + keys).data("kendoWindow").close();
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

	function load_communication(){
		$.ajax({
			url: '<?php echo URL; ?>/companies/sub_tab/comms/' + $("#mongo_id").val(),
			timeout: 15000,
			success:function(html){
				$("#entry_communication").html(html);
				var h = $("#address_box_company").height();
				$("#comms_box").height(h-72);
			}
		});
	}

</script>