<?php echo $this->element('menu_calendar'); ?>
<style type="text/css" media="screen">
	html, body {
		margin: 0px;
		padding: 0px;
		height: 100%;
	}
	.dhx_cal_event_clear {
		color: #FFFFFF;
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

	ul.list_day_rs li.active:after {
		color: rgba(0, 0, 0, 0);
		content: "x";
		cursor: pointer;
		right: 48px;
		position: absolute;
		z-index: 999;
	}
	ul.list_day_rs li:hover:after, ul.list_day_rs li:active:after, ul.list_day_rs li.active:after {
		color: #6A1515 !important;
	}
	#calendar-left ul li {
		position: relative;
	}
	.employee-legend{
        float: right;
        margin-right: 60px;
        width: 28px;
    }
    .k-colorpicker{
        float: right !important;
        margin-right: 60px !important;
        margin-top: 7px !important;
    }
    .k-colorpicker .k-selected-color {
        -moz-border-radius: 0 !important;
        -webkit-border-radius: 0 !important;
        width: 15px !important;
        height: 15px !important;
        border-radius: 0 !important;
    }
</style>

<div id="calendar-left">
    <ul id="have_data" class="list_day_rs">
        <?php
        foreach ($arr_responsible_task as $key => $value) { ?>
            <li id="li<?php echo $key; ?>" onclick="<?php echo $controller; ?>_calendar_onchange_rep(this, '<?php echo $key; ?>')">
                <?php
                    if($type=='contacts')
                        $name = (isset($value['first_name']) ? $value['first_name'].' ' : '').(isset($value['last_name']) ? $value['last_name'] :
                            '');
                    else
                        $name = $value;
                    echo $name;
                ?>
                <?php
                    if($type=='contacts'){
                        $arr_color[$key] = $value['color'];
                ?>
                <input class="employee-legend" data-id="<?php echo $key; ?>" style="background-color: <?php echo $value['color']; ?>" value="<?php echo $value['color']; ?>" readonly=readonly />
                <?php } ?>
            </li>
        <?php } ?>
        <li></li>
        <li></li>
    </ul>

    <!-- <ul id="no_data" class="list_day_rs">
        <li><?php //echo translate('There are no <?php echo $controller; ?> is used'); ?></li>
    </ul> -->
</div>
<script type="text/javascript">
$(function(){
    $(".employee-legend").kendoColorPicker({
        buttons: true,
    });
    $(".employee-legend").each(function(){
        var currentObj = $(this);
        var colorPicker = currentObj.data("kendoColorPicker");
        <?php if(!$this->Common->check_permission('calendar_@_entry_@_change_color',$arr_permission)){ ?>
        colorPicker.enable(false);
        <?php } else { ?>
        colorPicker.bind({
            change: function(e) {
                currentObj.val(e.value);
                save_data("color",e.value,currentObj.attr("data-id"),'contacts');
            },
        });
        <?php } ?>
    });
    $(".k-picker-wrap ").removeClass("k-picker-wrap ");
    $(".k-select",".k-colorpicker").remove();
})
</script>
<div class="myscheduler" id="calendar-right"></div>
<br><br><br>

<script type="text/javascript">

var filters_status = new Array();
<?php foreach ($arr_status as $key => $value) {
	echo 'filters_status["'.$key.'"] = true;';
}
?>

function <?php echo $controller; ?>_calendar_onchange_status() {
	var value = $("#<?php echo $model; ?>StatusFilter").val();
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

var filters_responsible_task = new Array();
<?php foreach ($arr_responsible_task as $key => $value) {
	echo 'filters_responsible_task["'.$key.'"] = true;';
}
?>

function <?php echo $controller; ?>_calendar_onchange_rep(object, rep_id) {

	if($(object).hasClass("active")){
		// $("li#", "#calendar-left").removeClass("active");
		$(object).removeClass("active");
		if( $("li.active", "#calendar-left").length < 1 ){
			for (var x in filters_responsible_task){
				filters_responsible_task[x] = true;
			}
		}else{
			for (var x in filters_responsible_task){
				if( $("#li" + x).hasClass("active") ){
					filters_responsible_task[x] = true;
				}else{
					filters_responsible_task[x] = false;
				}
			}
		}
	}else{// Nếu chọn thêm 1 active rep nữa

		// $("li", "#calendar-left").removeClass("active");
		$(object).addClass("active");
		if( rep_id == undefined ){
			for (var x in filters_responsible_task){
				filters_responsible_task[x + ""] = true;
			}
		}else{
			// lấy hết tất cả các rep active cho bằng true
			for (var x in filters_responsible_task){
				if( $("#li" + x).hasClass("active") ){
					filters_responsible_task[x] = true;
				}else{
					filters_responsible_task[x] = false;
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

	if( $("#<?php echo $controller; ?>_calendar_date_from").val() == "" || calendar_init == "init" ){
		$("#<?php echo $controller; ?>_calendar_date_from").val(date_from);
		$("#<?php echo $controller; ?>_calendar_date_to").val(date_to);

		arr_return[0] = date_from;
		arr_return[1] = date_to;
		return arr_return;
	}else{

		var a1 = new Date(  $("#<?php echo $controller; ?>_calendar_date_from").val() );
		var b1 = new Date( $("#<?php echo $controller; ?>_calendar_date_to").val() );

		// nếu thời gian người dùng click NHỎ hơn thời gian của data view thì request thêm data
		if( a.getTime() < a1.getTime() ){
			date_to = a1.getFullYear() + "-" + calendar_add_prefix_zero(a1.getMonth() + 1) + "-" + calendar_add_prefix_zero(a1.getDate());
			$("#<?php echo $controller; ?>_calendar_date_from").val(date_from);

			arr_return[0] = date_from;
			arr_return[1] = date_to;

			scheduler.load("<?php echo URL; ?>/<?php echo $controller; ?>/calendar_json/<?php echo $type; ?>/" + arr_return[0] + "/" + arr_return[1]  + "/yes");
			scheduler.updateView();

		}

		if( b.getTime() > b1.getTime() ){
			date_from = $("#<?php echo $controller; ?>_calendar_date_to").val();
			$("#<?php echo $controller; ?>_calendar_date_to").val(date_to_backup);

			arr_return[0] = date_from;
			arr_return[1] = date_to_backup;

			scheduler.load("<?php echo URL; ?>/<?php echo $controller; ?>/calendar_json/<?php echo $type; ?>/" + arr_return[0] + "/" + arr_return[1]  + "/yes");
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
		// var  b_date = new Date(b);
		// var  d_date = new Date(d);

		var str = "<b>Task name</b>: " + c.text.replace("_1_","&");
		str = str + "<br/><b>Type:</b> " + c.text_type;
		str = str + "<br><b>Responsible: </b>" + c.text_responsible;
		// str = str + "<br><b>Status: </b>" + c.text_status;
		if( c.can_move != "1" ){
			str = str + "<br>(can not move)";
		}

		return str;
	};
	// end Tooltip

	scheduler.templates.week_date_class=function(date,today){
		return "custom_color";
	}

	scheduler.xy.nav_height=0; // hide menu of calendar

	$(".myscheduler").dhx_scheduler({
		now_date: new Date(<?php echo date("Y").','. (date("m") -1).','. date("d").','. date("H").','. date("i").','. date("s"); ;?>,0),
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
	scheduler.load("<?php echo URL; ?>/<?php echo $controller; ?>/calendar_json/<?php echo $type; ?>/" + get_date[0] + "/" + get_date[1], function(){
		$( "div[event_id]" ).each(function() {
			$(this).addClass("rightclick");
		});
	    rightclick_run();
	});

	scheduler.attachEvent("onBeforeEventCreated", function (e){
		return false;
	});

	scheduler.templates.event_header = function(start,end,ev){
		// return ev.name;
		return scheduler.templates.event_date(start)+" - "+ scheduler.templates.event_date(end);

	};

	scheduler.templates.event_text = function(start,end,ev){
		// return "";
		return ev.text.replace("_1_","&");//salesorder_heading;
	};

	scheduler.templates.event_bar_text = function(a, b, c) {
		return c.text.replace("_1_","&"); //salesorder_heading; // for multi days
		// return c.text; // for multi days
	};

	// here we are using single function for all filters_status but we can have different logic for each view
	scheduler.filter_month = scheduler.filter_day = scheduler.filter_week = function(id, event) {
		// display event only if its type is set to true in filters_status obj
		// or it was not defined yet - for newly created event
		if ( (filters_status[event.status] || event.status==scheduler.undefined) && (filters_responsible_task[event.rep_id] || filters_responsible_task[event.contact_id] || event.rep_id==scheduler.undefined) )
		{
			return true;
		}
		return false;
	};

	// ------ end -------- Filter for status --------------------

	scheduler.attachEvent("onBeforeDrag", function (event_id, mode, native_event_object){
	   var ev = scheduler.getEvent(event_id);
	   if(ev != undefined && ev.can_move == 0)
		  return false; // blocked drag if can_move = 0
	   return true; // dnd is enabled for other events
	});

	scheduler.attachEvent("onDblClick", function (event_id, native_event_object){
		location.href = '<?php echo URL; ?>/<?php echo $controller; ?>/entry/' + event_id;
	});

	scheduler.attachEvent("onBeforeEventChanged", function(data, e, flag){
		var formatFunc = scheduler.date.date_to_str("%Y-%m-%d %H:%i:%s");
		var start_date = formatFunc(data.start_date);
		var end_date = formatFunc(data.end_date);
		$.ajax({
			url: '<?php echo URL; ?>/<?php echo $controller; ?>/calendar_change',
			timeout: 15000,
			type:"post",
			data: {work_start: start_date, work_end: end_date, id: data.id },
			success: function(html){
				if(html != "ok"){
					alerts("Error: ", html, function(){ location.reload(false) });
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

<?php if( $type == 'contacts' ){ ?>
$(function(){
	$("#li<?php echo $_SESSION['arr_user']['contact_id'];?>").click();
});
<?php } ?>

</script>

<!-- DUNG CHO CLICK CHUOT PHAI -->
<link rel="stylesheet" href="/theme/default/js/rightclick.css" type="text/css" />
<!-- tất cả các div có class .rightclick sẽ right click được -->
<script type="text/javascript" src="/theme/default/js/rightclick.js"></script>
<!-- DIV menu hiện lên khi right click, thuộc tính rel cho biết đã right click vào div có id nào -->
<ul id="mouse_menu_right" class="mouse_menu" style="visibility:hidden" rel="" >
	<li style="background-color: #6A1515;"><b style="font-weight: bold;color: #FFF;">Change status</b></li>
	<li class="break"><hr /></li>
	<?php foreach ($arr_status as $key => $value) { ?>
	<li class="cusorpointer" onclick="rightclick_update_calendar('<?php echo $key; ?>')"><?php echo $value; ?></li>
	<?php } ?>
	<li class="break"><hr /></li>
</ul>
<!-- DIV này dùng để xóa tooltip khi có rightclick -->
<div id="dhtmlXTooltip_tooltip_css"></div>
<script type="text/javascript">
function rightclick_update_calendar(type){
	$("#mouse_menu_right").css("visibility", "hidden");
	$.ajax({
		url: '<?php echo URL; ?>/<?php echo $controller; ?>/calendar_rightclick_change/' + $("#mouse_menu_right").attr("rel") + "/" + type,
		timeout: 15000,
		success: function(html){
			// dhx_header
			// dhx_title
			// dhx_body
			// dhx_footer

			// TH event 1 ngày
			$(".dhx_header,.dhx_title,.dhx_body,.dhx_footer", "#" + $("#mouse_menu_right").attr("rel")).css("background-color", html);

			// TH event nhiều ngày
			if( $("div", "#" + $("#mouse_menu_right").attr("rel") ).attr("class") == undefined ){
				$("#" + $("#mouse_menu_right").attr("rel") ).css("background-color", html);
			}

			$("#dhtmlXTooltip_tooltip_css").html("");
			console.log(html);
		}
	});
	return false;
}
</script>
<!-- END === DUNG CHO CLICK CHUOT PHAI -->