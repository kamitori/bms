<div class="float_left hbox_form" style="width:auto;">
    <a href="<?php echo URL . '/' . $controller; ?>/create_email_pdf/">
        <input class="btn_pur" id="emailexport_products" type="button" value="Email Order" style="width:99%;" />
    </a>
</div>
<div class="float_left hbox_form" style="width:auto; margin-left:5px;">
    <a href="<?php echo URL . '/' . $controller; ?>/view_pdf/" target="_blank">
        <input class="btn_pur" id="printexport_products" type="button" value="Export PDF" style="width:99%;" />
    </a>

</div>
<?php if($this->Common->check_permission('shippings_@_entry_@_add',$arr_permission)): ?>
<div class="float_right hbox_form" style="margin-right: 20.6%;position: relative;width: 5%;">
     <a href="javascript:void(0)"><input class="btn_pur" id="return_button" type="button" value="Return" style="width:99%;" /></a>
</div>
<?php endif; ?>
<div class="float_right hbox_form" style="margin-right: 0%;position: relative;width: 10.2%;">
     <a href="javascript:void(0)"><input class="btn_pur" id="receive_button" type="button" value="Receive" style="width:99%;" /></a>
</div>
<script type="text/javascript">
	$("#return_button").click(function(){
		var url = "<?php echo URL . '/' . $controller; ?>/return_item/";
		if( $("#return_window" ).attr("id") == undefined )
			$('<div id="return_window" style="display:none; min-width:300px;"></div>').appendTo("body");
		var loading = '<span style="padding: 19% 0 0 50%;float: left;font-size: xx-large;" >Loading...</span>';
		$("#return_window").html(loading);
		return_window = $("#return_window");
        return_window.kendoWindow({
	    	iframe: false,
	        actions: ["Close"],
	        content: url,
	        visible: false,
	        width: "auto",
			title: 'Return items',
			pinned: true,
			close: function() {
                reload_subtab("line_entry");
				$(".jt_ajax_note").css("top","120px");
            }
	    }).data("kendoWindow").open();
	    return_window.data("kendoWindow").maximize();
	    if($("#return_window_buttons").attr("id")==undefined){
            var html = '<ul id="return_window_buttons" class="menu_control float_right" style="margin-right: 50px; margin-bottom: 5px;">';
            html +=     '<li style="position: absolute;margin-top: 26px;left: 91%;width: 90px;text-align: center;">';
            html +=          '<a href="javascript:void(0)" id="return_window_ok" >OK</a>';
            html +=     '</li>';
            html += '</ul>';
            $("#return_window_wnd_title").after(html);
        }
        $("#return_window_ok").unbind("click");
        $("#return_window_ok").click(function(){
            $.ajax({
                url: url,
                type: "POST",
                data: $("#form_return").serialize(),
                success: function(result){
                	if(result=="ok"){
                		reload_subtab('line_entry');
                		$("#return_window").data("kendoWindow").destroy();
                	}
                	else
                		alerts("Message",result);
                }
            });
        });
	});
	$("#receive_button").click(function(){
		var url = "<?php echo URL . '/' . $controller; ?>/receive_item/";
		if( $("#receive_window" ).attr("id") == undefined )
			$('<div id="receive_window" style="display:none; min-width:300px;"></div>').appendTo("body");
		var loading = '<span style="padding: 19% 0 0 50%;float: left;font-size: xx-large;" >Loading...</span>';
		$("#receive_window").html(loading);
		receive_window = $("#receive_window");
        receive_window.kendoWindow({
	    	iframe: false,
	        actions: ["Close"],
	        content: url,
	        visible: false,
	        width: "auto",
			title: 'Receive items',
			pinned: true,
			close: function() {
                reload_subtab("line_entry");
				$(".jt_ajax_note").css("top","120px");
            }
	    }).data("kendoWindow").open();
	    receive_window.data("kendoWindow").maximize();
	    if($("#receive_window_buttons").attr("id")==undefined){
            var html = '<ul id="receive_window_buttons" class="menu_control float_right" style="margin-right: 50px; margin-bottom: 5px;">';
            html +=     '<li style="position: absolute;margin-top: 26px;left: 91%;width: 90px;text-align: center;">';
            html +=          '<a href="javascript:void(0)" id="receive_window_ok" >OK</a>';
            html +=     '</li>';
            html += '</ul>';
            $("#receive_window_wnd_title").after(html);
        }
        $("#receive_window_ok").unbind("click");
        $("#receive_window_ok").click(function(){
            $.ajax({
                url: url,
                type: "POST",
                data: $("#form_receive").serialize(),
                success: function(result){
                	if(result=="ok"){
                		reload_subtab('line_entry');
                		$("#receive_window").data("kendoWindow").destroy();
                	}
                	else
                		alerts("Message",result);
                }
            });
        });
	});
</script>