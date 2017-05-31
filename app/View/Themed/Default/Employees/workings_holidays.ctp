<style type="text/css">
.k-window {
 	padding-bottom: 0 !important;
}
div.k-window-content {
 padding:  0 !important;
}
.ui-datepicker {
	z-index: 12000 !important;
}
</style>
<div class="tab_1 full_width">
	<span class="title_block bo_ra1">
		<span class="fl_dent"><h4><?php echo translate('Working hours for this contact'); ?></h4></span>
		<span class="fl_dent">
			<a class="add-working-hours" href="javascript:void(0)" title="Add new working hours"><span class="icon_down_tl top_f"></span></a>
		</span>
		<span class="fl_dent">
			<select id="workings_months" style="margin-top: -2px;">
				<?php foreach($workings_months as $month){ ?>
				<option value="<?php echo $month; ?>"><?php echo str_replace('_', ' / ', $month); ?></option>
				<?php } ?>
			</select>
			<script type="text/javascript">
				$("select#workings_months option:last").attr("selected","selected");
			</script>
		</span>
		<span class="fl_dent">
			<a href="javascript:void(0)" id="print_hrs_worked">
				<input class="btn_pur" type="button" value="Export PDF" style="width: 72px;">
			</a>
		</span>
	</span>
	<p class="clear"></p>
	<ul class="ul_mag clear bg3">
		<li class="hg_padd" style="width:1.5%"></li>
		<li class="hg_padd" style="width:7%"><?php echo translate('DAY'); ?></li>
		<li class="hg_padd" style="width:11.1%"><?php echo translate('Week 1'); ?></li><!-- center_txt -->
		<li class="hg_padd" style="width:4%"><?php echo translate('Lunch 1'); ?></li>
		<li class="hg_padd" style="width:2%"></li>
		<li class="hg_padd" style="width:11.1%"><?php echo translate('Week 2'); ?></li>
		<li class="hg_padd" style="width:4%"><?php echo translate('Lunch 2'); ?></li>
		<li class="hg_padd" style="width:2%"></li>
		<li class="hg_padd line_mg" style="width:11.1%"><?php echo translate('Week 3'); ?></li>
		<li class="hg_padd" style="width:4%"><?php echo translate('Lunch 3'); ?></li>
		<li class="hg_padd" style="width:2%"></li>
		<li class="hg_padd line_mg" style="width:11.1%"><?php echo translate('Week 4'); ?></li>
		<li class="hg_padd" style="width:4%"><?php echo translate('Lunch 4'); ?></li>
	</ul>
	<div id="contacts_work_hour">
	</div>
	<span class="hit"></span>
	<span class="title_block bo_ra2">
		<span class="bt_block float_right no_bg" style="float:right; font-weight:bold">Total :
			<input style="text-align: right; color:red; padding-right: 5px;" class="input_w2" type="text" readonly="true" id="total_month" value="">(hours)
		</span>
		<span class="bt_block float_right no_bg" style="float:left; font-weight:bold; margin-left:5%">Total W1:
			<input style="text-align: right; color:red; padding-right: 5px;" class="input_w2" type="text" readonly="true" id="total_week_1" value="">(hours)
		</span>
		<span class="bt_block float_right no_bg" style="float:left; font-weight:bold; margin-left:5.5%">Total W2:
			<input style="text-align: right; color:red; padding-right: 5px;" class="input_w2" type="text" readonly="true" id="total_week_2" value="">(hours)
		</span>
		<span class="bt_block float_right no_bg" style="float:left; font-weight:bold; margin-left:6%">Total W3:
			<input style="text-align: right; color:red; padding-right: 5px;" class="input_w2" type="text" readonly="true" id="total_week_3" value="">(hours)
		</span>
		<span class="bt_block float_right no_bg" style="float:left; font-weight:bold; margin-left:6%">Total W4:
			<input style="text-align: right; color:red; padding-right: 5px;" class="input_w2" type="text" readonly="true" id="total_week_4" value="">(hours)
		</span>
	</span>
</div>

<script type="text/javascript">
$(function(){
	$("#contacts_work_hour").load("<?php echo URL.'/employees/workings_holidays_ajax' ?>");
	$(".add-working-hours").click(function(){
		var div_id = "add-working-hours";
		var div_title = "Add working hours";
	    var html =	      '<div class="jt_box_line">';
			html +=	         '<div class=" jt_box_label " style=" width:25%;height: 25px;">Month</div>';
			html +=	         '<div class="jt_box_field " style=" width:71%">';
			html +=				'<input id="month" readonly name="month" class="input_1 float_left" type="text" value="">';
			html += 		 '</div>';
			html +=	      '</div>';
		var ok_callBack = function(){
			var month = $("#month","#"+div_id).val();
			$.ajax({
				url : "<?php echo URL.'/employees/workings_holidays_add'; ?>",
				type: "POST",
				data: {month: month},
				success: function(result){
					$("select#workings_months").append('<option value="'+month.replace('/','_')+'">'+month.replace('/',' / ')+'</div>');
					$("select#workings_months option:last").attr("selected","selected").trigger("change");
				}
			});
		}
		createPopup(div_id,div_title,html,ok_callBack);
		return false;
	});
	$("select#workings_months").change(function(){
		$.ajax({
				url : "<?php echo URL.'/employees/workings_holidays_ajax'; ?>",
				type: "POST",
				data: {month: $(this).val()},
				success: function(result){
					$("#contacts_work_hour").html(result);
				}
			});
	});
	$("#print_hrs_worked").click(function(){
		var div_id = "export-workings-pdf";
		var workings_months = $("select#workings_months").val();
		var div_title = "Export working hours - "+workings_months.replace('_','/');
		var html = "";
		for(var i = 1; i <= 4; i ++){
	    	html +=	      '<div class="jt_box_line">';
			html +=	         '<div class=" jt_box_label " style=" width:25%;height: 25px;">Week '+i+'</div>';
			html +=	         '<div class="jt_box_field " style=" width:71%">';
			html +=				'<input id="week_'+i+'" '+(i < 3 ? 'checked': '')+' name="week_'+i+'" class="input_1 float_left" type="checkbox" value="1">';
			html += 		 '</div>';
			html +=	      '</div>';
		}
		var ok_callBack = function(){
			window.location = "<?php echo URL.'/employees/workings_holidays_pdf?' ?>"+$("input","#"+div_id).serialize()+"&month="+$("select#workings_months").val();
		}
		createPopup(div_id,div_title,html,ok_callBack);
		return false;
	});
});
function createPopup(div_id,div_title,html_content,ok_callBack)
{
	if( $("#"+div_id ).attr("id") == undefined ){
		var html = '<div id="'+div_id+'" >';
			html +=	   '<div class="jt_box" style=" width:100%;">';
			html +=	      '<div class="jt_box_line">';
			html +=	         '<div class=" jt_box_label " style=" width:25%;height: 25px"></div>';
			html +=	         '<div class="jt_box_field" id="message" style=" width:71%">';
			html += 		 '</div>';
			html +=	      '</div>';
			html += html_content;
			html +=	      '<div class="jt_box_line">';
			html +=	         '<div class=" jt_box_label " style=" width:25%;height: 65px"></div>';
			html +=	         '<div class="jt_box_field " style=" width:71%">';
			html += '<input style="margin-top:2%" type="button" class="jt_confirms_window_cancel" id="cancel" value=" Cancel " /><input style="margin-top:2%" type="button" class="jt_confirms_window_ok" value=" Ok " id="ok" />';
			html += 		 '</div>';
			html +=	      '</div>';
			html +=	   '</div>';
			html +=	'</div>';
	}
	$('<div id="'+div_id+'" style="width: 99%; padding: 0px; overflow: auto;">'+html+'</div>').appendTo("body");
	if($("#month","#"+div_id).attr("id") != undefined)
    	$( "#month" ).datepicker({dateFormat: 'mm/yy', changeMonth: true, changeYear: true, yearRange: "c-70:c+3" });
	var popup = $("#"+div_id);
	popup.kendoWindow({
		width: "355px",
		title: div_title,
		visible: false,
		modal: true,
	 	activate: function(){
            $(".k-window-actions").remove();
        }
	});
	popup.data("kendoWindow").center();
	popup.data("kendoWindow").open();
	$("#ok").unbind("click");
	$("#ok").click(function(){
		ok_callBack(div_id);
       	popup.data("kendoWindow").destroy();
	});
	$('#cancel').click(function() {
       	popup.data("kendoWindow").destroy();
    });
}
</script>