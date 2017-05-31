<?php
    if(isset($arr_settings['relationship'][$sub_tab]['block']))
    foreach($arr_settings['relationship'][$sub_tab]['block'] as $key => $arr_val){
        echo $this->element('box',array('key'=>$key,'arr_val'=>$arr_val));
    }
?>
<p class="clear"></p>
<script type="text/javascript">
$('textarea#shipping_comment').change(function() {
    var fieldName = 'shipping_comment',
        value = $(this).val(),
        func = 'update',
        ids = $('#mongo_id').val();
    $.ajax({
        url: '<?php echo URL.'/'.$controller;?>/ajax_save',
        type:"POST",
        data: {field:fieldName,value:value,func:func,ids:ids},
        success: function(text_return){
        }
    });
});
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
</script>