<?php
	if(isset($arr_settings['relationship'][$sub_tab]['block']))
	foreach($arr_settings['relationship'][$sub_tab]['block'] as $key => $arr_val){
		echo $this->element('box',array('key'=>$key,'arr_val'=>$arr_val));
	}
?>
<p class="clear"></p>
<input type="hidden" id="click_open_window_salesinvoice" />
<script type="text/javascript">
window_popup("salesinvoices","Specify Sales invoice","salesinvoice_options","click_open_window_salesinvoice", "?new=true");
open_popup("Combine orders","combine_orders","combine_salesorder");
$("#create_part_shipment").click(function(){
    if( $("#part_shipping_window" ).attr("id") == undefined )
        $('<div id="part_shipping_window" style="display:none; min-width:300px;"></div>').appendTo("body");
    var loading = '<span style="padding: 19% 0 0 50%;float: left;font-size: xx-large;" >Loading...</span>';
    $("#part_shipping_window").html(loading);
    costing_window = $("#part_shipping_window");
    costing_window.kendoWindow({
        iframe: false,
        actions: ["Close"],
        content: "<?php echo URL.'/salesorders/part_shipping' ?>",
        visible: false,
        resizable: false,
        draggable: false,
        width: "auto",
        title: 'Part Shipping',
        pinned: false,
        close: function(){
            costing_window.data("kendoWindow").destroy();
        }
    }).data("kendoWindow").open();
    if($("#part_shipping_window_buttons").attr("id")==undefined){
        var html = '<ul id="part_shipping_window_buttons" class="menu_control float_right" style="margin-right: 50px; margin-bottom: 5px;">';
        html +=     '<li style="position: absolute;margin-top: 45px;left: 83%;background-color: #003256;">';
        html +=          '<a style=" cursor:pointer;" onclick="create_part_shipping();" >&nbspCreate Part Shipping&nbsp;</a>';
        html +=     '</li>';
        html += '</ul>';
        $("#part_shipping_window_wnd_title").after(html);
    }
    costing_window.data("kendoWindow").maximize();
});
function create_part_shipping(){
    $.ajax({
        url: "<?php echo URL; ?>/<?php echo $controller;?>/create_part_shipping",
        type: "POST",
        data: $("input", "#part_shipping_form").serialize(),
        success: function(result){
            $("#part_shipping_window").data("kendoWindow").close();
            $("body").css("overflow","auto");
            if(result != "ok") {
                alerts("Message",result);
            } else {
                reload_subtab('ship_invoice');
            }
        }
    });
}
function create_full_shipping(){
    $.ajax({
        url: "<?php echo URL; ?>/<?php echo $controller;?>/create_shipping",
        timeout: 15000,
        type: "POST",
        success: function(result){
            result = $.parseJSON(result);
            if(result.status == "error") {
                alerts("Message",result.mess);
            } else if(result.status == "need_pass") {
                callPasswordPopup();
            } else {
                location.replace(result.url);
            }
        }
    });
}


function callPasswordPopup(){
    if( $("#confirms_window" ).attr("id") == undefined ){
        var html = '<div id="password_confirm" >';
            html +=    '<div class="jt_box" style=" width:100%;">';
            html +=       '<div class="jt_box_line"><div class=" jt_box_label " style=" width:25%;"></div><div id="alert_message"></div></div>';
            html +=       '<div class="jt_box_line">';
            html +=          '<div class=" jt_box_label " style=" width:25%;height: 75px">Password</div>';
            html +=          '<div class="jt_box_field " style=" width:71%"><input name="password" id="password" class="input_1 float_left" type="password" value=""></div><input style="margin-top:2%" type="button" class="jt_confirms_window_cancel" id="confirms_cancel" value=" Cancel " /><input style="margin-top:2%" type="button" class="jt_confirms_window_ok" value=" Ok " id="confirms_ok" />';
            html +=       '</div>';
            html +=       '</div>';
            html +=    '</div>';
            html += '</div>';
        $('<div id="confirms_window" style="width: 99%; padding: 0px; overflow: auto;">'+html+'</div>').appendTo("body");
    }
    var confirms_window = $("#confirms_window");
    confirms_window.kendoWindow({
        width: "355px",
        height: "100px",
        title: "See front desk",
        visible: false,
        activate: function(){
          $('#password').focus();
        }
    });
    $("#password").keypress(function(evt){
        var keyCode = (evt.which) ? evt.which : event.keyCode
        if(keyCode==13)
            $("#confirms_ok").click();
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
        var data = {};
        data['password'] = $("#password").val();
        if(typeof extraData == "object"){
            $.extend(data,extraData);
        }
        $.ajax({
            url: '<?php echo URL.'/'.$controller;?>/create_shipping',
            type:"POST",
            data: data,
            success: function(result){
                result = JSON.parse(result);
                if(result.status == "error") {
                    alerts("Message",result.message);
                } else {
                    location.replace(result.url);
                }
            }
        });
        confirms_window.data("kendoWindow").destroy();
    });
    $('#confirms_cancel').click(function() {
        $("#alert_message").html("");
        if($("#code").attr("rel")!="" && $("#code").attr("rel")!= undefined)
            $("#code").val($("#code").attr("rel"));
        confirms_window.data("kendoWindow").destroy();
    });
    return false;
}
function create_full_invoice(){
    $.ajax({
        url: "<?php echo URL; ?>/<?php echo $controller;?>/create_full_salesinvoice",
        timeout: 15000,
        type: "POST",
        success: function(html){
            if(html=='full_invoiced'){
                confirms("Message","This Job already have an invoice link to it.<br />Do you want to append to this?",
                         function(){
                            window.location.assign("<?php echo URL.'/salesorders/replace_salesinvoice' ?>");
                         },function(){
                            return false;
                         });
            }
            else if(html=='no_company')
                alerts('Message','This function cannot be performed as there is no company or contact linked to this record.');
            else if(html=='no_product')
                alerts('Message','No items have been entered on this transaction yet.');
            else{
                confirms3("Message","Do you want to create new Sales invoice or append to an existed one?",['New','Append','']
                         ,function(){//New
                            window.location.assign(html);
                         },function(){//Apend
                            $("#click_open_window_salesinvoice").click();
                         },function(){
                            return false;
                         });

            }
        }
    });

    return false;
}
function open_popup(title, key, key_click_open){
    controller = "salesorders";
    // ---- set default ----
    if( key == undefined ){
        key = "";
    }
    var div_popup_id = controller + key;
    // -------------- bắt đầu code -------------------------------
    // Kiểm tra tồn tại trước khi tạo lại
    if( $("#window_popup_" + div_popup_id).attr("id") == undefined )
        $('<div id="window_popup_' + div_popup_id + '" style="display:none; min-width:300px;"></div>').appendTo("body");

    if( title == undefined ){
        alert("You must set a title for function: window_popup(controller, title, key, key_click_open) . Thanks.");
    }

    var window_popup = $("#window_popup_" + div_popup_id);

    if( key_click_open != undefined && key_click_open != "" ){
        var click = $("#" + key_click_open);
    }else{
        var click = $("#click_open_window_" + div_popup_id);
    }
    // refesh lại kendo window để hiển thị các giá trị chọn mới
    click.bind("click", function() {
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
    window_popup.kendoWindow({
        iframe: false,
        actions: ["Maximize", "Close"],
        width: "845px",
        height: "510px",
        activate: function(e){
            if($.trim(window_popup.html()) == ""){
                var html = '<span style="padding: 50%;"><img src="<?php echo URL ?>/theme/<?php echo $theme ?>/images/ajax-loader.gif" title="Loading..." /></span>';
                window_popup.html(html);
                url = "<?php echo URL; ?>/salesorders/combine_orders_popup/" + key;
                $.ajax({
                    url: url,
                    success: function(html){
                        window_popup.parent().css({'height':'auto'});
                        window_popup.html(html);
                    }
                })
            }
        },
        visible: false,
        title: title,
        open: onOpen,
        // activate: onActivate,
    }).data("kendoWindow").center();
}
</script>