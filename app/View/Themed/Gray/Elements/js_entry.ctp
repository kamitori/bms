<?php if( !isset($not_init_js_entry) ) echo $this->element('js/js');?>

<script type="text/javascript">
<?php if( !isset($not_init_js_entry) ){ ?>
	$(function(){
		element_js_entry_init();
	});
<?php } ?>

var max_height = 0;
$("#<?php echo $controller ?>_form_auto_save .jt_panel").each(function(){
    if($(this).height() > max_height)
        max_height = $(this).height();
});
$("#<?php echo $controller ?>_form_auto_save .jt_panel").each(function(){
    if($(this).height() < max_height){
        var height = max_height - $(this).height();
        $(".jt_box_label:last",this).css('height',height+24);
        $(".tab_1_inner",this).each(function(){
    		var height = max_height - $(this).height();
        	$(".jt_ppbot:last",this).css('height',height+24);
    	})
    } else {
    	$(".tab_1_inner",this).each(function(){
    		var height = max_height - $(this).height();
        	$(".jt_ppbot:last",this).css('height',height+24);
    	})
    }
});

<?php if( in_array($controller, array('quotations', 'salesinvoices', 'salesorders')) ) {  ?>
if( !$("#<?php echo $controller ?>_form_auto_save #show-summary-entry").length ) {
	$("#<?php echo $controller ?>_form_auto_save .jt_panel .fixbor:first").prepend('<button type="button" id="show-summary-entry" class="btn_pur" onclick="showSummary()">Summary</button>');
}
function showSummary(show)
{
	var controller = '<?php echo $controller ?>';
	var show;
	if( $('#'+ controller +'_form_auto_save').is(':visible') ) {
		$('#'+ controller +'_form_auto_save').slideUp({
			duration: 500,
			complete: function() {
				$('#'+ controller +'_entry_summary input').each(function() {
					var id = $(this).data('name');
					$(this).val( $('#'+ controller +'_form_auto_save #'+id).val() );
				});
				$('#'+ controller +'_entry_summary').slideDown(500);
			}
		});
		show = 1;
	} else {
		$('#'+ controller +'_entry_summary').slideUp({
			duration: 500,
			complete: function() {
				$('#'+ controller +'_form_auto_save').slideDown(500);
			}
		});
		show = 0;
	}
	$.ajax({
		url: '<?php echo URL.'/'.$controller.'/show_summary_entry/' ?>'+ show,
		success: function() {
		}
	});
}
<?php } ?>
function fixHiddenCombobox(container_id){
	if(container_id==undefined) {
		container_id = "container_products";
	}
	$.fn.reverse = [].reverse;
	var num = null;
	$('.ul_mag.line_box[id!=listbox_products_Extra_Row]', '#'+ container_id).reverse().each(function() {
		if(container_id == "container_products" && !$('.viewprice_quantity', this).length ) {
			return;
		}
		$('.combobox_selector', this).each(function() {
			$(this).prev().click();
			if( num == null || num < $('li', this).length ) {
				num = $('li', this).length;
			}
		});
		return false;
	});
	var ul_num = $("ul.line_box  ",".mCSB_container").length;
	if(num){
		html = '';
		for(var i = 0; i<num; i++){
			ul_num++;
			html += '<ul class="ul_mag clear '+(ul_num%2==0? 'bg1' : 'bg2')+' "></ul>';
		}
		$(".mCSB_container", "#"+container_id).append(html);
	}
	$(".combobox_selector:last","#load_subtab").hide();
}
function element_js_entry_init(){
	//Link Sub Tab
	$(".ul_tab li").click(function(e) {
		var val = $(this).attr("id");
		if(val=='' || val==undefined)
			return false;
		// window.location.hash = $(this).attr("id");
        e.preventDefault();
		$(".ul_tab li").removeClass("active");
		$("#"+val).addClass("active");
		$.ajax({
			url: '<?php echo URL.'/'.$controller;?>/sub_tab/'+val+'/<?php echo $iditem;?>',
			success: function(html){
				$("#load_subtab").html(html);
			}
		});

	});
	<?php if(!isset($no_alert_input)){ ?>
	$('form input[readonly!="readonly"],form select[readonly!="readonly"]').keydown(function(e) {
		if(e.which == 8)
			ajax_note_set('<?php msg('MD_PRESS_TAB');?>');
	});
	$('form input[readonly!="readonly"],form select[readonly!="readonly"]').keypress(function(e) {
		if (e.which == 13) {
			$(this).change();
		}else
			ajax_note_set('<?php msg('MD_PRESS_TAB');?>');
	});
	<?php } ?>
	$('form input[readonly="readonly"],form select[readonly="readonly"]').click(function(){
		ajax_note('<?php msg('MD_NOT_TYPING');?>');
	});
	$('form input[readonly="readonly"],form select[readonly="readonly"]').focusin(function(){
		ajax_note('<?php msg('MD_NOT_TYPING');?>');
	});


	//link to relationship field
	$( document ).delegate(".jt_box_line_span","click",function(){
		var keys = $(this).attr("class");

		keys = keys.split("link_to_");
		keys =  keys[1];

		keys = keys.replace("jt_link_on","");
		keys = $.trim(keys);

		var ids = keys.replace("_name","");
			ids = ids+'_id';

		var md_id = $("#"+ids).val();

		var controllers = $(this).parent().parent().find('.iconw_m').attr('id');
		//console.log(controllers);
		controllers = controllers.replace(keys,"");

		controllers = controllers.replace("click_open_window_","");

		if(md_id!='')
			window.location.assign("<?php echo URL;?>/"+controllers+"/entry/"+md_id);
		else
			confirms("Message",'<?php msg('QUOTATION_CREATE_LINK');?>'+UpCaseFirst(controllers)+'?',
			function(){
				window.location.assign("<?php echo URL;?>/"+controllers+"/add");
			},function(){ ajax_note('Cancel');});
	});




	$(document).delegate(".deleteopt_link","focus",function(){
		$('.ul_mag').css('border-right','none');
		$(this).parent().parent().parent().css('border-right','1px solid #000');
		ajax_note('Enter to delete this line.');
	});

	element_js_entry_init_icon_emaili();
}

function element_js_entry_init_icon_emaili(){
	$(".icon_emaili").click(function(){
		var emails = $(this).parent().find("input").val();
		if(emails=='' || emails==undefined)
			emails = $(this).parent().parent().find("input").val();
		if(emails=='' || emails==undefined){
			alerts("Message","Email empty.");
		}else if(check_format_email(emails)){
			create_email(emails);
		}else
			alerts("Message","Please check email format again.");
	});
}



// Hàm dùng chung:=========================================
/*
	check_format_email(email)
	create_email(email)
	FortmatPrice(values)
	CheckNegative(values,toop)
	ChangeFormatId(str_id)
	ajax_note_set(txt)
	ajax_note(txt)
	save_field(field,value,idmongo)
	add_new(fieldname,values)
	save_muti_field(arr_field,arr_value,idmongo)
	ajax_delete(ports,iditem)
	change_bg(index,boxname)
	cal_sum_in_display(itemkey,parentkey)
	cal_sum_by_request(opt,field)
	reload_subtab(subtabname)
	reload_box(boxname)
	save_option(opname,arr_value,opid,isreload,subtab,keys)
	save_default(opname,arr_value,opid,isreload,subtab,keys)
	update_default(opname,defaultfield,fields,values)
	get_data_form_module(module_name,ids,arr,handleData)
	save_data_form_to(module_name,ids,arr,handleData);
	reload_address(address_key)
	after_choose_addresses(ids,names,keys)
	get_para_employee()
	get_para_customer_company()
	Scrollbar(divname_scroll)
	convert_date_to_num(date_str)
	confirm_delete(link)

*/

//convert 16 Oct, 2013 to 256565689
function convert_date_to_num(date_str){
	var d=new Date(date_str+" 00:00:00");
	return parseInt(d.getTime());
}


function check_format_email(email){
	var r = new RegExp("[a-z0-9!#$%&'*+/=?^_`{|}~-]+(?:\.[a-z0-9!#$%&'*+/=?^_`{|}~-]+)*@(?:[a-z0-9](?:[a-z0-9-]*[a-z0-9])?\.)+[a-z0-9](?:[a-z0-9-]*[a-z0-9])?");

    return (email.match(r) == null) ? false : true;
}


function create_email(email){
	if(email!=undefined && email!='' ){
		ajax_note_set('Creating email ...');
		var ids = $("#mongo_id").val();
		var arr_field = {
					'f1':'comms_type',
					'f2':'email',
					'f3':'created_from',
					'f4':'created_from_id'
					};
		var arr_data = {
					'f1':'Email',
					'f2':email,
					'f3':'<?php echo $controller;?>',
					'f4':ids
					};
		var jsonfield = JSON.stringify(arr_field);
		var jsondata = JSON.stringify(arr_data);
		$.ajax({
			url: '<?php echo URL;?>/communications/save_muti_field',
			dataType: "json",
			type:"POST",
			data: {ids:'noneid',jsonfield:jsonfield,jsondata:jsondata},
			success: function(retn){
				ajax_note('Complete.');
				window.location.assign("<?php echo URL;?>/communications/entry/"+retn['_id']);
			}
		});
	}
}



function focusNextInputField(){
	console.log('next');
}


function FortmatPrice(values){
	values = parseFloat(values);
	values = values.formatMoney(2, '.', ',');
	return values;
}



function UnFortmatPrice(str){
	if(str!=undefined){
		str = str.replace(/,/g,"");
		str = parseFloat(str);
		return str;
	}else return 0;
}



function CheckNegative(values,toop){
	vs = parseFloat(values);
	if(vs<0)
		$(""+toop).css("color","red");
	else{
		$(""+toop).css("color","");
		$(""+toop).css("color","#000000");
	}
}



Number.prototype.formatMoney = function(c, d, t){
var n = this,
    c = isNaN(c = Math.abs(c)) ? 2 : c,
    d = d == undefined ? "." : d,
    t = t == undefined ? "," : t,
    s = n < 0 ? "-" : "",
    i = parseInt(n = Math.abs(+n || 0).toFixed(c)) + "",
    j = (j = i.length) > 3 ? j % 3 : 0;
   return s + (j ? i.substr(0, j) + t : "") + i.substr(j).replace(/(\d{3})(?=\d)/g, "$1" + t) + (c ? d + Math.abs(n - i).toFixed(c).slice(2) : "");
 };



String.prototype.UpperCaseFirst = function(){
	var strs = this;
	return strs.charAt(0).toUpperCase() + strs.slice(1);
}



function UpCaseFirst(strs){
	return strs.charAt(0).toUpperCase() + strs.slice(1);
}



function ChangeFormatId(str_id){
	var arr = str_id.split("_");
	var retxt='';
	for(var i in arr){
		retxt += UpCaseFirst(arr[i]);
	}
	return retxt;
}



//save main fields
function save_field(field,value,idmongo,handleData){
	if(field!='' && field!=undefined && value!=undefined){
		var func,ids;
		if(idmongo=='')
			ids = $("#mongo_id").val();
		else
			ids = idmongo;
		if(ids!='')
			func = 'update';
		else
			func = 'add';
		//alert(field+'='+value+'='+func+'='+ids);
		$(".k-window").fadeOut();
		$.ajax({
			url: '<?php echo URL.'/'.$controller;?>/ajax_save',
			type:"POST",
			data: {field:field,value:value,func:func,ids:ids},
			success: function(text_return){
				text_return = text_return.split("||");
				$("#mongo_id").val(text_return[0]);
				ajax_note("Saving...Saved !");
				if(handleData!=undefined)
					handleData(text_return);
				else
					return text_return[1];
			}
		});
	}
}


//hàm save mới cho phép xuất ra array dữ liệu các field đã được save kèm theo field chính
//và đổ dữ liệu ra có input trên màn hình. Hàm này đi kèm với controller thích hợp
function save_data(field,value,idmongo,valueid,handleData,controller){
	if(field!='' && field!=undefined && value!=undefined){
		var func,ids;
		if(idmongo==undefined || idmongo=='')
			ids = $("#mongo_id").val();
		else
			ids = idmongo;
		$(".k-window").fadeOut();
		if(controller ==undefined || controller =='')
			controller = '<?php echo $controller;?>';
		ajax_note_set("Saving... ");
		$.ajax({
			url: '<?php echo URL;?>/'+controller+'/save_data',
			dataType: "json",
			type:"POST",
			data: {field:field,value:value,ids:ids,valueid:valueid},
			success: function(arr_return){
				ajax_note("Saving...Saved !");
				for(var i in arr_return){
					i = i.replace("$");
					$("#"+i).val(arr_return[i]);
				}
				if(handleData!=undefined)
					handleData(arr_return);
			}
		});
	}
}


function add_new(fieldname,values){
	$.ajax({
		url: '<?php echo URL.'/'.$controller;?>/ajax_save',
		type:"POST",
		data: {field:fieldname,value:values,func:'add'},
		success: function(text_return){
			text_return = text_return.split("||");
			window.location.assign("<?php echo URL.'/'.$controller;?>/entry/"+text_return[0]);
		}
	});
}

//save muti main fields
function save_muti_field(arr_field,jsondata,idmongo,tempdata){
	if(arr_field!=undefined && jsondata!='' ){
		var jsonfield = JSON.stringify(arr_field);
		var func,ids;
		if(idmongo=='' || idmongo==undefined)
			ids = $("#mongo_id").val();
		else
			ids = idmongo;
		$.ajax({
			url: '<?php echo URL.'/'.$controller;?>/save_muti_field',
			dataType: "json",
			type:"POST",
			data: {ids:ids,jsonfield:jsonfield,jsondata:jsondata},
			success: function(returndata){
				tempdata(returndata);
			}
		});
	}
}



function ajax_delete(ports,iditem){
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
				url: '<?php echo URL.'/'.$controller;?>/'+ports+'/'+ids,
				type:"POST",
				success: function(txt){
					change_bg(ix,boxname);
					reload_subtab(subtabname);
					ajax_note(" Deleted !");
				}
			});
	},function(){ console.log('testing');});
}


function change_bg(index,boxname){
	var sum = $("#container_"+boxname+" .ul_mag").length;
	sum = parseInt(sum);
	var strs='';var lengs = 0; var newbg ='';
	var i=0; index = parseInt(index);
	for(i=index;i<=sum+1;i++){
		strs =$("#container_"+boxname+" .ul_mag:eq("+i+")").attr('class');
		if(strs){
			strs = strs.split("bg1");
			if(strs.length>1){
				$("#container_"+boxname+" .ul_mag:eq("+i+")").addClass('bg2');
				$("#container_"+boxname+" .ul_mag:eq("+i+")").removeClass('bg1');
				newbg = "bg1";

			}else{
				$("#container_"+boxname+" .ul_mag:eq("+i+")").addClass('bg1');
				$("#container_"+boxname+" .ul_mag:eq("+i+")").removeClass('bg2');
				newbg = "bg2";
			}
		}

	}
	$("#container_"+boxname+" .mCSB_container").append('<ul class="ul_mag clear '+newbg+' "></ul>');
}


//Lặp và tính sum cho cột có class là itemkey, nằm trong box parentkey
function cal_sum_in_display(itemkey,parentkey,notedit){ //ex. ('.viewprice_amount','#container_allocation')
	var sum = $(parentkey+" "+itemkey).length;
	var totals=0; var thisval;
	for(var i=0;i<sum;i++){
		if(notedit!=undefined)
			thisval = $(parentkey+" "+itemkey+":eq("+i+")").html();
		else
			thisval = $(parentkey+" "+itemkey+":eq("+i+")").val();
		thisval = thisval.replace(",","");
		thisval = parseFloat(thisval);
		totals = totals+thisval;
	}
	return totals;
}


//tính tổng bằng cách request lại
function cal_sum_by_request(opname,amount_field,handleData){
	$.ajax({
		url: '<?php echo URL.'/'.$controller;?>/cal_sum_by_request',
		type:"POST",
		data: {opname:opname,amount_field:amount_field},
		success: function(values){
			handleData(values);
		}
	});
}


//focus after add new
function focus_end(boxname,fname,setval){
	var lengs = $("#container_"+boxname+" .line_box").length;
	lengs--;console.log(lengs);
	var indes = $("#container_"+boxname+" .ul_mag:eq("+lengs+")").attr('id');//console.log(indes);
	if(indes!=undefined)
	{
		indes = indes.split("_");
		lengs = indes.length;
		indes = indes[lengs-1];
		if(setval!=undefined)
			$('#'+fname+'_'+indes).val(setval);
		$('#'+fname+'_'+indes).focus();
	}
}


function reload_subtab(subtabname){
	$(".ul_tab li").removeClass("active");
	$("#"+subtabname).addClass("active");
	$.ajax({
		url: '<?php echo URL.'/'.$controller;?>/sub_tab/'+subtabname+'/<?php echo $iditem;?>',
		success: function(html){
			$("#load_subtab").html(html);
			ajax_note("");
		}
	});
}


function reload_box(boxname,handleData){
	$.ajax({
		url: '<?php echo URL.'/'.$controller;?>/reload_box',
		type:"POST",
		data: {boxname:boxname},
		success: function(text_return){//console.log(text_return);
			if(text_return!='')
			$("#container_"+boxname).parent().html(text_return);
			if(handleData!=undefined)
				handleData(text_return);
		}
	});
}


function save_option(opname,arr_value,opid,isreload,subtab,keys,handleData,fieldchage,module_id){
	if(opname != undefined ){
		if(keys == undefined  || keys == '')
			keys  = 'update';
		var arr = {
				'keys' : keys,
				'opname' : opname,
				'value_object' : arr_value,
				'opid' : opid
			};
		var jsonString = JSON.stringify(arr);
		if(fieldchage == undefined )
			fieldchage = '';
		if(module_id == undefined )
			module_id = '';
		//ajax_note_set(keys+"=\n"+opname+"=\n"+value_str+"=\n"+opid);
		var url = '<?php echo URL.'/'.$controller;?>/save_option';
		var popup_id = '';
		if(isreload != undefined){
			isreload = String(isreload);
			if(isreload.indexOf("&&") != -1){
				isreload = isreload.split("&&");
				popup_id = isreload[0];
				isreload = isreload[isreload.length - 1];
			}
			isreload = parseInt(isreload);
		}
		if(popup_id != "no_close"){
			if(popup_id=='')
				$(".k-window").fadeOut();
			else
				$("#"+popup_id).data("kendoWindow").close();
		}
		$.ajax({
			url: '<?php echo URL.'/'.$controller;?>/save_option',
			type:"POST",
			data: {arr:jsonString,fieldchage:fieldchage,mongo_id:module_id},
			success: function(rtu){
				if( isreload != undefined && isreload==1 )
					reload_subtab(subtab);
				else if( isreload != undefined && isreload==2)
					reload_box(opname);
				ajax_note("Saving ... Saved.");
				if(handleData!=undefined)
					handleData(rtu);
			}
		});

	}else
		return '';
}

function save_default(opname,arr_value,opid,isreload,subtab,keys){
	if(opname != undefined ){
		if(keys == undefined )
			keys  = 'update';
		var arr = {
				'keys' : keys,
				'opname' : opname,
				'value_object' : arr_value,
				'opid' : opid
			};
		var jsonString = JSON.stringify(arr);
		$.ajax({
			url: '<?php echo URL.'/'.$controller;?>/save_default',
			type:"POST",
			data: {arr:jsonString},
			success: function(rtu){
				ajax_note_set(rtu);
				$(".k-window").fadeOut('slow', function() {

				 });

				 if( isreload != undefined && isreload==1 )
					reload_subtab(subtab);
				 else if( isreload != undefined && isreload==2)
					reload_box(opname);
			}
		});

	}else
		return '';
}


function update_default(opname,defaultfield,fields,values,handleData){
	if(opname!= undefined && fields!= undefined && values!= undefined){
		$.ajax({
			url: '<?php echo URL.'/'.$controller;?>/update_default',
			type:"POST",
			data: {opname:opname,defaultfield:defaultfield,fields:fields,values:values},
			success: function(ret){
				ajax_note('');
				if(handleData!=undefined)
					handleData(ret);
			}
		});
	}
}


function get_data_form_module(module_name,ids,arr,handleData){
	var jsonString = JSON.stringify(arr);
	$.ajax({
		url: '<?php echo URL.'/'.$controller;?>/get_data_form_module',
		dataType: "json",
		type:"POST",
		data: {module_name:module_name,ids:ids,arr:jsonString},
		success: function(jsonReturn){
			if(handleData!=undefined)
				handleData(jsonReturn);
		}
	});
}



function save_data_form_to(module_from,ids,arr,handleData){
	var jsonString = JSON.stringify(arr); var keys;
	$.ajax({
		url: '<?php echo URL.'/'.$controller;?>/save_data_form_to',
		dataType: "json",
		type:"POST",
		data: {module_from:module_from,ids:ids,arr:jsonString},
		success: function(data_return){
			if(handleData!=undefined)
				handleData(data_return);
			else{
				for(var i in arr){
					keys = arr[i];
					if(typeof(data_return[keys])!= 'undefined')
						$("#"+keys).val(data_return[keys]);
					else
						$("#"+keys).val('');

				}
			}

		}
	});
}



/**Lưu data vào module khi đứng ở module khác
*
*
*/
function save_to_other_module(to_module,arr,keylink,handleData){
	var jsonString = JSON.stringify(arr);
	if(to_module!=undefined){
		$.ajax({
			url: '<?php echo URL;?>/'+to_module+'/save_to_other_module',
			dataType: "json",
			type:"POST",
			data: {arr:jsonString,keylink:keylink},
			success: function(data_return){
				if(handleData!=undefined)
					handleData(data_return);

			}
		});
	}
}



//ten cua gia tri array la: address_key+'address'. vd: address_key = 'invoice_';
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
					if(data_return[address_key+arr_field[i]]!=undefined)
						$('#'+ChangeFormatId(address_key+arr_field[i])).val(data_return[address_key+arr_field[i]]);
					else
						$('#'+ChangeFormatId(address_key+arr_field[i])).val('');
				}
			}
		});
		//load lại nút chọn popup address
		var company_id = $("#company_id").val();
		var contact_id = $("#contact_id").val();

		/*var addressesinvoice_icon = $('#click_open_window_addressesinvoice').attr('class');
		var addressesshipping_icon = $('#click_open_window_addressesshipping').attr('class');

		if(address_key=='invoice_' && addressesinvoice_icon ==undefined)
			$("#map_invoice").before("<span class=\"iconw_m indent_dw_m\" title=\"Specify address\" id=\"click_open_window_addressesinvoice\"></span>");
		if(address_key=='shipping_' && addressesshipping_icon ==undefined)
			$("#map_invoice").before("<span class=\"iconw_m indent_dw_m\" title=\"Specify address\" id=\"click_open_window_addressesshipping\"></span>");*/

		window_popup('addresses', 'Specify company address','invoice','click_open_window_addressesinvoice','?company_id='+company_id+'&contact_id='+contact_id,'force_re_install');
		var controller = '<?php echo $controller ?>';
		if ($.inArray(controller, ['salesinvoices', 'salesorders', 'quotations']) != -1) {
			findShippingAddress(true);
		} else {
			window_popup('addresses', 'Specify company address','shipping','click_open_window_addressesshipping','?company_id='+company_id+'&contact_id='+contact_id,'force_re_install');
		}

	}
}


function unset_combobox(ids){
	if(ids!=undefined){
		var html = $("#"+ids).parent().html();
		if(html!=undefined)
		{
			html = html.split('<span>');
			html = html[0];
			html = html.replace("value","alt");
			//$("#"+ids).parent().parent().find('script').remove();
			$("#"+ids).parent().remove();
			$("#"+ids+'Id').before(html);
			//$("#"+ids+'Id').remove();
		}
	}else
		console.log('ids is undefined');
}
function change_combobox(ids,uls){
	if(ids!=undefined){
		$("#"+ids).parent().find('.combobox_selector ul').html('red');
		//alerts('',html);
	}else
		console.log('ids is undefined');
}

function lock_fields(arrfield){
	for(var i=0;i<arrfield.length;i++){
		if(typeof($('#'+arrfield[i])) != "undefined" && variable !== null) {
			$('#'+arrfield[i]).attr("readonly","readonly");
		}
	}
}


function update_all_option(opname,arr_value,handleData){
	var arr_value = JSON.stringify(arr_value);
	$.ajax({
		url: '<?php echo URL.'/'.$controller;?>/update_all_option',
		dataType: "json",
		type:"POST",
		data: {opname:opname,arr_value:arr_value},
		success: function(data_return){
			if(handleData!=undefined)
				handleData(data_return);
		}
	});
}


function get_para_employee(){
	var para = '?is_employee=1';
	return para;
}


function get_para_customer_company(){
	var para = '?is_supplier=1';
	return para;
}

function get_para_customer_shipper(){
	return '?is_shipper=1';
}

function get_para_is_customer(){
		var para = '?is_customer=1';
		return para;
	}

function get_company_is_shipper(){
	var para = '?is_shipper=1';
	return para;
}

// Scrollbar
function Scrollbar(divname_scroll){
	$("#" + divname_scroll).mCustomScrollbar({
		scrollButtons:{
			enable:false
		},
		advanced:{
        	updateOnContentResize: true,
        	autoScrollOnFocus: false,
    	}
	});
}
function comeback(){
	window.history.back();
}

</script>