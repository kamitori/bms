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