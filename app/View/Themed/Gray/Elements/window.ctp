<style type="text/css">
	.k-window-content{
		overflow-x: hidden !important;
	}
</style>
<script type="text/javascript">

// http://demos.kendoui.com/web/window/api.html
// parameter_get : ex: "?company_id=48943546&contact_id=8597340345"
//custom by vu.nguyen: do nhu cau popup long nhau nen se dung force_re_install de khong cho tu dong popup
// neu force_re_install = 'no_auto_close' thi se khong tu dong


function window_popup(controller, title, key, key_click_open, parameter_get, force_re_install){

	// ---- set default ----


	if( key == undefined ){
		key = "";
	}

	if( parameter_get == undefined ){
		parameter_get = "";
	}

	if( force_re_install == undefined ){
		force_re_install = "";
	}

	var div_popup_id = controller + key;

	// -------------- bắt đầu code -------------------------------

	// Kiểm tra tồn tại trước khi tạo lại
	if( $("#window_popup_" + div_popup_id).attr("id") == undefined ){
		$('<div id="window_popup_' + div_popup_id + '" style="display:none; min-width:300px;"></div>').appendTo("body");

	}else if(force_re_install != "force_re_install") {

		// không cần tạo lại khung window popup mà chỉ cần bind click lại như cũ là được
		var window_popup = $("#window_popup_" + div_popup_id);
		if( key_click_open != undefined && key_click_open != "" ){
			var undo = $("#" + key_click_open);
		}else{
			var undo = $("#click_open_window_" + div_popup_id);
		}

		undo.bind("click", function() {
			/*if( force_re_install != "no_auto_close")
				$(".k-window").fadeOut('slow');*/
			window_popup.data("kendoWindow").center();
			window_popup.data("kendoWindow").open();
			$(".container_same_category", window_popup).mCustomScrollbar({
				scrollButtons:{
					enable:false
				},
				advanced:{
			        updateOnContentResize: true,
			        autoScrollOnFocus: false,
			    }
			});
		});
		return false;
		// end
	}

	if( controller == undefined ){
		alert("You must set a controller for function: window_popup(controller, title, key, key_click_open) . Thanks.");
	}

	if( title == undefined ){
		alert("You must set a title for function: window_popup(controller, title, key, key_click_open) . Thanks.");
	}

	var window_popup = $("#window_popup_" + div_popup_id);

	if( key_click_open != undefined && key_click_open != "" ){
		var undo = $("#" + key_click_open);
	}else{
		var undo = $("#click_open_window_" + div_popup_id);
	}

	// refesh lại kendo window để hiển thị các giá trị chọn mới
	if( force_re_install == "force_re_install" ){

		if( window_popup.attr("id") == undefined || $.trim(window_popup.html()) == "" ){
			console.log("Bạn chưa khai báo window popup '" + "#window_popup_" + div_popup_id + "', vui lòng kiểm tra lại code, thanks.");
		}else{
			window_popup.data("kendoWindow").refresh({
		        url: "<?php echo URL; ?>/"+ controller +"/popup/" + key + parameter_get
		    });
		    // return true;
		}

	}

	undo.bind("click", function() {
		/*if( force_re_install != "no_auto_close")
			$(".k-window").fadeOut('slow');*/
		window_popup.data("kendoWindow").center();
		window_popup.data("kendoWindow").open();
		$(".container_same_category", window_popup).mCustomScrollbar({
			scrollButtons:{
				enable:false
			},
			advanced:{
		        updateOnContentResize: true,
		        autoScrollOnFocus: false,
		    }
		});
	});
	if(controller == "products"){
		window_popup.kendoWindow({
	    	iframe: false,
	        actions: ["Maximize", "Close"],
	        content: "<?php echo URL; ?>/"+ controller +"/popup/" + key + parameter_get,
	        visible: false,
			title: title,
	        open: function() {
	            this.wrapper.css({ top: 50 });
	        }
	    }).data("kendoWindow").center();
	} else {
		window_popup.kendoWindow({
	    	iframe: false,
	        actions: ["Maximize", "Close"],
	        visible: false,
	        width: "845px",
	        height: "510px",
			title: title,
	        open: function() {
	            this.wrapper.css({ top: 50 });
	        },
	        activate: function(){
	        	if($.trim(window_popup.html()) == ""){
	        		var html = '<span style="padding: 50%;"><img src="<?php echo URL ?>/theme/<?php echo $theme ?>/images/ajax-loader.gif" title="Loading..." /></span>';
	        		window_popup.html(html);
	        		$.ajax({
		        		url: "<?php echo URL; ?>/"+ controller +"/popup/" + key + parameter_get,
		        		success: function(html){
                        	window_popup.parent().css({'height':'auto'});
		        			window_popup.html(html);
		        		}
		        	})
	        	}
	        },
	    }).data("kendoWindow").center();
	}

}

var refreshIntervalId;
function onOpen(e) {
 	console.log("event :: open");
 	var id = e.sender.element[0].id;
 	if( $.trim($("#" + id).html()) == "" ){
 		clearInterval(refreshIntervalId);
 		refreshIntervalId = setInterval('console.log("setInterval kiểm tra popup load xong thì dừng");if( $.trim($("#' + id + '").html()) != "" ){ $("#' + id + '").data("kendoWindow").center(); $(".container_same_category", "#' + id + '").mCustomScrollbar({scrollButtons:{enable:false},advanced:{updateOnContentResize: true, autoScrollOnFocus: false}}); clearInterval(refreshIntervalId); }', 1000);
 	} else
 		checkFunctionFocus();
}
function checkFunctionFocus(){
	if(typeof focusFirstInput == 'function')
		focusFirstInput();
}

// function window_check_popup_loaded(id){
// 	if( $.trim($("#" + ' + id + ').html()) != "" ){ $("#" + ' + id + ').data("kendoWindow").center(); clearInterval(refreshIntervalId); }
// }

// function onActivate(e) {
//     console.log("event :: open");
//     // kendoConsole.log("event :: activate");
// }

function update_ajax_province_input(object, input_updated_object, contain){
	$.ajax({
		url: '<?php echo URL; ?>/settings/get_province_from_country/' + $(object).val(),
		timeout: 15000,
		type:"post",
		success: function(html){
			if( contain == "contain" ){
				var updated_contain = $(object).closest("form");
				$(input_updated_object, updated_contain).html(html);

			}else{
				$(input_updated_object).html(html);

			}
		}
	});
}

function notifyTop(html){
	if($.trim(html) != "" )
		$("#notifyTop").html(html).fadeIn(600).append('<div id="notifyTopsub"><a href="javascript:void(0)" onclick="$(\'#notifyTop\').fadeOut()">Hide</a></div>');
}
</script>

<div id="notifyTop"></div>
<style type="text/css">
#notifyTop {
	display: none;
	position: fixed;
	top: 101px;
	left: 30%;
	z-index: 9999;
	background-color: #852020;
	color: #FFF;
	border-radius: 3px;
	box-shadow: 0 2px 4px rgba(0, 0, 0, 0.2);
	padding: 6px 28px 6px 10px;
	font-weight: bold;
	width: 40%;
	text-align: center;
	overflow: hidden;
	line-height: 1.3;
}
#notifyTop .flash_message{
	margin-right: 40px;
}
#notifyTop #notifyTopsub{
	position: absolute;
	right: 4px;
	top: 6px;
	text-decoration: underline;
}#notifyTop #notifyTopsub a:hover{
	color: #D1B9B9;
}
</style>