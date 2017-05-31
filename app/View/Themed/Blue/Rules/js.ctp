<script type="text/javascript">
$(function(){
	//default focus
	$("#formula").focus();
	
	//Link Sub Tab
	$(".ul_tab li").click(function() {
		var val = $(this).attr("id");
		$(".ul_tab li").removeClass("active");
		$("#"+val).addClass("active");
		$.ajax({
			url: '<?php echo URL.'/'.$controller;?>/sub_tab/'+val,
			success: function(html){
				$("#load_subtab").html(html);
				//location.reload();
			}
		});
		
	});
	
	// Xu ly save, update
	$("form input,#form_<?php echo $controller;?>").change(function() {
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
		if(fieldtype=='checkbox'){
			if($(this).is(':checked'))
				values = 1;
			else
				values = 0;
		}
			
	if(fieldname=='tools_num' || fieldname=='tools_edit' || fieldname=='tools_clear' || fieldname=='tools_clear_all' )
		$(".jt_ajax_note").html("");
		
	else{
		//test:	
		//alert('Saving...\nfield='+fieldname+'\nvalue='+values+'\nids='+ids+'<?php echo URL.'/'.$controller;?>\nfunc='+func+'\ntype='+fieldtype);
		$(".jt_ajax_note").html("Saving...       ");
		
		$.ajax({
			url: '<?php echo URL.'/'.$controller;?>/ajax_save',
			type:"POST",
			data: {field:fieldname,value:values,func:func,ids:ids},
			success: function(text_return){
				//$(".jt_ajax_note").html(text_return);
				text_return = text_return.split("||");
				$("#"+modulename).val(text_return[0]);
				
				// change tittle, thay đổi tiêu đề của items
				<?php foreach($arr_settings['title_field'] as $ks=>$vls){?>
					titles[<?php echo $ks;?>] = '<?php echo $vls;?>';
				<?php }?>
				if(titles.indexOf(fieldname)!=-1){
					$("#md_"+fieldname).html(values);
				}
				ajax_note("Saving...Saved !");
			}
		});
	}
	
		
	});
	
	// xu ly cong thuc
	$("#tools_num").keypress(function(e){
		var code = (e.keyCode ? e.keyCode : e.which);
		 if(code == 13) { //Enter keycode
		   StringPlus($("#tools_num").val(),'n');
		 }
	});
	$("#tools_numbt").click(function(){
		$(".jt_ajax_note").html('Add Number');
		var newnum = $("#tools_num").val();
		StringPlus(newnum,'n');
	});
	
	$("#tools_save").click(function(){
		$(".jt_ajax_note").html('Saving...');
		SaveFormuler('formula','');
		var str = $("#map_formula").html();
		SaveFormuler('map_formula',str);
	});
	
	$("#tools_addition").click(function(){
		$(".jt_ajax_note").html('+');
		StringPlus('+','s');
	});
	$("#tools_subtraction").click(function(){
		$(".jt_ajax_note").html('-');
		StringPlus('-','s');
	});
	$("#tools_multiplication").click(function(){
		$(".jt_ajax_note").html('*');
		StringPlus('*','s');
	});
	$("#tools_division").click(function(){
		$(".jt_ajax_note").html('/');
		StringPlus('/','s');
	});
	$("#tools_and").click(function(){
		$(".jt_ajax_note").html('AND');
		StringPlus(' (AND)','b');
	});
	$("#tools_or").click(function(){
		$(".jt_ajax_note").html('OR');
		StringPlus(' (OR)','b');
	});
	$("#tools_function").click(function(){
		$(".jt_ajax_note").html('Function();');
		StringPlus(' (F:())','f');
	});
	
	$("#tools_clear_all").click(function(){
		alert('Do you want Clear this Formula ?');
		$(".jt_ajax_note").html('Clear all');
	});
	$("#tools_clear").click(function(){
		alert('Click item in Formula for Remove ');
		$(".jt_ajax_note").html('Click item in Formula for Remove');
	});
	$("#tools_edit").click(function(){
		alert('Click item in Formula for Edit ');
		$(".jt_ajax_note").html('Click item in Formula for Edit');
	});
	
	var window_popup = $("#list_field_<?php echo $controller;?>_popup");
	var undo = $("#tools_fields");

	undo.bind("click", function() {
		$(".k-window").fadeOut('slow');
		window_popup.data("kendoWindow").open();
		var leftpos = ($(window).width() - $(".k-window").width())/2;
		var toppos = ($(window).height() - $(".k-window").height())/5;
		$(".k-window").css('left',leftpos);
		$(".k-window").css('top',toppos);
	});
	
	window_popup.kendoWindow({
		width: "auto",
		title: "Products Field List",
		content: ""
	});
	
	$(".jt_ajax_note").html('');
	
});

function StringPlus(values,types){
	var str = $("#map_formula").html();
	var formula = $("#formula").val();
	var newhtml = str + '<input type="button" value="'+values+'" name="formulaitem_1" id="formulaitem_1" class="formula_item" />';
	$("#map_formula").html(newhtml);
	
	if(types!='')
		values = "'"+types+":"+values+"'";
	else
		values = "'"+values+"'";
		
	if(formula!='')
		values = ','+values;
	$("#formula").val(formula+values);
	return true;
}

function SaveFormuler(fieldname,values){
		var fieldid = $("#"+fieldname).attr("id");
		var fieldtype = 'text';
		var	modulename = 'mongo_id';
		var ids = $("#"+modulename).val();
		if(values=='')
			var values = $("#"+fieldname).val();
		var func = '';
		
		if(ids!='')
			func = 'update'; //add,update
		else
			func = 'add';
	$.ajax({
		url: '<?php echo URL.'/'.$controller;?>/ajax_save',
		type:"POST",
		data: {field:fieldname,value:values,func:func,ids:ids},
		success: function(text_return){ //alert(text_return);
			$(".jt_ajax_note").html(text_return);
			text_return = text_return.split("||");
			$("#"+modulename).val(text_return[0]);
			//ajax_note("Saving...Saved !");
		}
	});
}

function FortmatPrice(values){
	values = parseFloat(values);
	values = values.formatMoney(2, '.', ',');
	return values;
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

//Hien thong bao truoc khi ajax
function ajax_note_set(txt){
	$(".jt_ajax_note").stop().fadeIn(1);
	$(".jt_ajax_note").css("color","red");
	$(".jt_ajax_note").html(txt);
}
//Hien thong bao sau khi ajax thanh cong
function ajax_note(txt){
	$(".jt_ajax_note").stop().html(txt);
	$(".jt_ajax_note").fadeOut(1500, function() {
		$(".jt_ajax_note").html("");
		$(".jt_ajax_note").fadeIn(100);
	});
}

// Scrollbar
function Scrollbar(divname_scroll){
	$("#" + divname_scroll).mCustomScrollbar({
		scrollButtons:{
			enable:false
		}
	});	
	
}

</script>