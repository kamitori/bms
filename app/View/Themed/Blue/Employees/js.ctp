<?php echo $this->element('js_entry');?>
<?php echo $this->element('js/permission_product_modules');?>
<style>
.jt_ajax_note{
z-index: 59;
}
</style>
<script type="text/javascript">
$(document).ready(function() {
	load_image_box();
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
			save_address(check_address,values,fieldid,function(){
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
});
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
						$('#'+ChangeFormatId(arr_field[i])).val(data_return[arr_field[i]]);  // #Address1, #Address2,...
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


	}
}


function after_choose_contacts(ids,names,keys){
	if(keys=='employee_name'){
		var arr_data_from = JSON.parse($("#after_choose_contacts"+ keys + ids).val());
		var value = arr_data_from.first_name + " " + arr_data_from.last_name;
		save_data('employee_name',value,'',ids,function(txt){
		});

	}
}

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
		});
}
function load_image_box(){
	$.ajax({
		url: '<?php echo URL; ?>/employees/employee_image/',
		timeout: 15000,
		success:function(html){
			$("#employee_image").html(html);
			var h = $("#address_box_").height();
			$(".jt_subtab_box_cont","#block_full_image").height(h - 56);
		}
	});
}
</script>
