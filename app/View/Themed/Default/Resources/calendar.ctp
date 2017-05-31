<?php echo $this->element('menu_calendar'); ?>
<style type="text/css" media="screen">
	html, body {
		margin: 0px;
		padding: 0px;
		height: 100%;
	}
	.dhx_cal_navline{
		display: none !important;
	}
	.dhx_cal_select_menu{
		display: none;
	}
	.dhx_scale_holder_now {
		background-image: url(<?php echo URL; ?>/theme/default/images/databg_now.png);
		border-right: 1px solid #CECECE;
		background-position: 0 0;
	}

	.myscheduler a{
		color: #00f !important;
	}
	#calendar-left{
		float:left;
		width: 18%;
		height:82%;
		overflow-y: auto;
		overflow-x: hidden;
		border-bottom: 1px solid #E3E3E3;
		border-right: 1px solid #E3E3E3;
	}
	#calendar-left ul li{
		padding-left: 21px;
	}
/*    #calendar-left ul li, #calendar-left ul{
		background: none;
	}*/
	#calendar-left ul.list_day_rs li:hover, #calendar-left ul.list_day_rs li:active, #calendar-left ul.list_day_rs li.active {
		background: #C28080;
		color: #FFF;
	}

	#calendar-left ul li:hover{
		cursor: pointer;
	}
	/*#calendar-left ul, #calendar-left ul li{
		display: none;
	}*/
	#calendar-right{
		float:right;
		width:80%;
		position: relative;
		height:82%;
	}
	body {
		overflow:hidden;
	}
	.dhx_expand_icon{
		display: none;
	}
	.clear_fix {
		margin-top: 86px;
	}

	.dhtmlXTooltip b{
		font-weight: bold;
	}

	.dhtmlXTooltip a{
		color: #B9331A !important;
	}

</style>
<div id="calendar-left">
	<ul id="have_data" class="list_day_rs">
		<?php
		foreach ($arr_type_resource as $key => $value) { ?>
			<li id="<?php echo $key; ?>" onclick="resources_calendar_onchange_type(this, '<?php echo $key; ?>')">
				<?php echo $value; ?>
			</li>
		<?php } ?>
	</ul>

	<!-- <ul id="no_data" class="list_day_rs">
		<li><?php //echo translate('There are no resources is used'); ?></li>
	</ul> -->
</div>
<div class="myscheduler" id="calendar-right"></div>
<br><br><br>

<script type="text/javascript">

var filters_status = new Array();
<?php foreach ($arr_status as $key => $value) {
	echo 'filters_status["'.$key.'"] = true;';
}
?>

function resources_calendar_onchange_status() {
	var value = $("#ResourceStatusFilter").val();
	if( value == "" ){
		for (var x in filters_status){
			filters_status[x] = true;
		}
	}else{
		for (var x in filters_status){
			if( value == x ){
				filters_status[x] = true;
			}else{
				filters_status[x] = false;
			}
		}
	}
	scheduler.updateView();
}

var filters_type_resource = new Array();
<?php foreach ($arr_type_resource as $key => $value) {
	echo 'filters_type_resource["'.$key.'"] = true;';
}
?>

function resources_calendar_onchange_type(object, item_id) {

	if($(object).hasClass("active")){

		$("li", "#calendar-left").removeClass("active");

		for (var x in filters_type_resource){
			filters_type_resource[x] = true;
		}
	}else{
		$("li", "#calendar-left").removeClass("active");

		$(object).addClass("active");

		if( item_id == undefined ){
			for (var x in filters_type_resource){
				filters_type_resource[x + ""] = true;
			}
		}else{
			for (var x in filters_type_resource){
				if( item_id == x ){
					filters_type_resource[x] = true;
				}else{
					filters_type_resource[x] = false;
				}

			}
		}
	}

	scheduler.updateView();
}

// dùng để click chọn ngày trên menu
function show_minical(){

	if (scheduler.isCalendarVisible())
		scheduler.destroyCalendar();
	else
		scheduler.renderCalendar({
			position:"dhx_minical_icon",
			date:scheduler._date,
			navigation:true,
			handler:function(date,calendar){
				scheduler.setCurrentView(date);
				calendar_get_date(); // cập nhật lại data trên view
				scheduler.destroyCalendar();
			}
		});
}

function calendar_add_prefix_zero(str){
	if( $.trim(str).length == 1 )str = "0" + str;
	return str;
}

// get data cho calendar
function calendar_get_date( calendar_init ){

	var a = new Date(scheduler.getState().min_date);
	var date_from = a.getFullYear() + "-" + calendar_add_prefix_zero(a.getMonth() + 1) + "-" + calendar_add_prefix_zero(a.getDate());
	var b = new Date(scheduler.getState().max_date);
	var date_to_backup = date_to = b.getFullYear() + "-" + calendar_add_prefix_zero(b.getMonth() + 1) + "-" + calendar_add_prefix_zero(b.getDate());

	var arr_return = new Array();

	if( $("#resources_calendar_date_from").val() == "" || calendar_init == "init" ){
		$("#resources_calendar_date_from").val(date_from);
		$("#resources_calendar_date_to").val(date_to);

		arr_return[0] = date_from;
		arr_return[1] = date_to;
		return arr_return;
	}else{

		var a1 = new Date(  $("#resources_calendar_date_from").val() );
		var b1 = new Date( $("#resources_calendar_date_to").val() );

		// nếu thời gian người dùng click NHỎ hơn thời gian của data view thì request thêm data
		if( a.getTime() < a1.getTime() ){
			date_to = a1.getFullYear() + "-" + calendar_add_prefix_zero(a1.getMonth() + 1) + "-" + calendar_add_prefix_zero(a1.getDate());
			$("#resources_calendar_date_from").val(date_from);

			arr_return[0] = date_from;
			arr_return[1] = date_to;

			scheduler.load("<?php echo URL; ?>/resources/calendar_json/<?php echo $type; ?>/" + arr_return[0] + "/" + arr_return[1]  + "/yes");
			scheduler.updateView();

		}

		if( b.getTime() > b1.getTime() ){
			date_from = $("#resources_calendar_date_to").val();
			$("#resources_calendar_date_to").val(date_to_backup);

			arr_return[0] = date_from;
			arr_return[1] = date_to_backup;

			scheduler.load("<?php echo URL; ?>/resources/calendar_json/<?php echo $type; ?>/" + arr_return[0] + "/" + arr_return[1]  + "/yes");
			scheduler.updateView();
		}

		// thay đổi list bên trái khi click change ngày
		// scheduler.get_visible_events();
		// var visible_event = scheduler.get_visible_events();
		// $("li", "#calendar-left").hide();
		// if( visible_event.length > 0 ){
		//     $("#" + visible_event[0]['id']).show();
		// }
		return true;

	}

	return arr_return;
}

$(function(){

	//===============
	// Tooltip related code
	//===============
	scheduler.templates.tooltip_text = function(b, d, c) {
		// scheduler.templates.tooltip_date_format(b)
		// scheduler.templates.tooltip_date_format(d)
		var  b_date = new Date(b);
		var  d_date = new Date(d);

		var str = "";

		str = str + "<b>" + c.tltp_res_type + ":</b> " + c.tltp_res_name + "<br/><br/>";

		str = str + "<b>(" + c.tltp_module + ")</b>";
		str = str + "<br/><b>Name</b>: <a href='<?php echo URL; ?>/" + c.tltp_module.toLowerCase() + "s/entry/" + c.tltp_id + "'>" + c.tltp_name + "</a>";
		str = str + "<br/><b>Type:</b> " + c.tltp_type; //b_date.toString('dd-MMM-yyyy').substring(0, 16);
		str = str + "<br/><b>Contact:</b> " + c.tltp_contact_name; //d_date.toString('dd-MMM-yyyy').substring(0, 16);
		str = str + "<br/><b>Responsible:</b> " + c.tltp_responsible;



		if( c.salesorder_id != undefined ){
			str = str + "<br/>";
			str = str + "<br/><b>(Sales Order)</b>";
			str = str + "<br/><b>Heading</b>: <a href='<?php echo URL; ?>/salesorders/entry/" + c.salesorder_id + "'>" + c.salesorder_heading + "</a>";
			str = str + "<br/><b>Assign to:</b> " + c.salesorder_assign_to;

		}

		return str;
	};
	// end Tooltip

	scheduler.templates.week_date_class=function(date,today){
		return "custom_color";
	}

	scheduler.xy.nav_height=0; // hide menu of calendar

	$(".myscheduler").dhx_scheduler({
		xml_date:"%Y-%m-%d %H:%i",
		date:new Date(),
		mode:"week",
		dblclick_create: false,
		mark_now: true,
		first_hour: 7,
		// last_hour: 20,
		time_step: 30,
		icons_select: []
	});

	// scheduler.config.dblclick_create = false;
	var get_date = calendar_get_date("init");
	scheduler.load("<?php echo URL; ?>/resources/calendar_json/<?php echo $type; ?>/" + get_date[0] + "/" + get_date[1]);

	scheduler.attachEvent("onBeforeEventCreated", function (e){
		return false;
	});

	scheduler.templates.event_header = function(start,end,ev){
		// return ev.name;
		return scheduler.templates.event_date(start)+" - "+ scheduler.templates.event_date(end);

	};

	scheduler.templates.event_text = function(start,end,ev){
		// return "";
		return ev.tltp_name;//salesorder_heading;
	};

	scheduler.templates.event_bar_text = function(a, b, c) {
		return c.tltp_name; //salesorder_heading; // for multi days
		// return c.text; // for multi days
	};

	// here we are using single function for all filters_status but we can have different logic for each view
	scheduler.filter_month = scheduler.filter_day = scheduler.filter_week = function(id, event) {
		// display event only if its type is set to true in filters_status obj
		// or it was not defined yet - for newly created event
		if ( (filters_status[event.status] || event.status==scheduler.undefined) && (filters_type_resource[event.item_id] || event.item_id==scheduler.undefined) )
		{
			// chỉ hiển thị những resource có calendar mà thôi
			// $("ul#no_data").hide();
			// $("ul#have_data").show();

			// if($.trim(event.item_id).length > 0)
			//     $("ul#have_data li#" + event.item_id).show();
			// check_show_ul = false;

			return true;
		}

		// default, do not display event
		return false;
	};
	// if( check_show_ul ){
	//     $("ul#no_data").show();
	//     $("ul#no_data li").show();
	// }
	// ------ end -------- Filter for status --------------------

	scheduler.attachEvent("onBeforeDrag", function (event_id, mode, native_event_object){
	   var ev = scheduler.getEvent(event_id);
	   if(ev != undefined && ev.can_move == 0)
		  return false; // blocked drag if can_move = 0
	   return true; // dnd is enabled for other events
	});

	scheduler.attachEvent("onDblClick", function (event_id, native_event_object){
		location.href = '<?php echo URL; ?>/resources/module_redirect/' + event_id;
	});

	scheduler.attachEvent("onBeforeEventChanged", function(data, e, flag){
		var formatFunc = scheduler.date.date_to_str("%Y-%m-%d %H:%i:%s");
		var start_date = formatFunc(data.start_date);
		var end_date = formatFunc(data.end_date);
		$.ajax({
			url: '<?php echo URL; ?>/resources/calendar_change',
			timeout: 15000,
			type:"post",
			data: {work_start: start_date, work_end: end_date, id: data.id },
			success: function(html){
				if(html != "ok"){
					alerts("Error: ", html);
				}
			}
		});
		// return false;
		return true;
	});

	// scheduler._click.dhx_cal_tab();
	// scheduler._click.dhx_cal_today_button();
	// scheduler._click.dhx_cal_prev_button();
	// scheduler._click.dhx_cal_next_button();
	// $('.dhx_cal_data').animate({"scrollTop": 310}, "slow");

});

<?php if( $type == 'Contact' ){ ?>
$(function(){
	$("#<?php echo $_SESSION['arr_user']['contact_id'];?>").click();
});
<?php } ?>
</script>