<?php echo $this->element('tab_option'); ?>
<script type="text/javascript">
	$(function(){
        $("#batch_invoices_via_email").attr("onclick","");
        $("#batch_invoices_via_mail").attr("onclick","");
        $("#combine_invoices").attr("onclick","");
        // $("#closing_month").attr("onclick","closing_month()");
		$('#create_shipping').attr('onclick', 'create_shipping()');
        open_popup("Specify Sales invoice","pdf_only","batch_invoices_via_mail");
        open_popup("Specify Sales invoice","","batch_invoices_via_email");
        open_popup("Combine invoices","combine_invoices","combine_invoices");
	});

	function create_shipping(){
            $.ajax({
                url: "<?php echo URL; ?>/<?php echo $controller;?>/create_shipping",
                type: "POST",
                success: function(result){
                    result = JSON.parse(result);
                    if(result.status != "ok") {
                        alerts("Message",result.message);
                    } else {
                        location.replace(result.url);
                    }
                }
            });
    }
    function open_popup(title, key, key_click_open){
        controller = "salesinvoices";
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
                    if(window_popup.attr("id")=="window_popup_salesinvoicescombine_invoices")
                        url = "<?php echo URL; ?>/salesinvoices/combine_invoices_popup/" + key;
                    else
                        url = "<?php echo URL; ?>/salesinvoices/batch_invoices_popup/" + key;
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
    function closing_month(){
        if( $("#closing_month_window" ).attr("id") == undefined )
            $('<div id="closing_month_window" style="display:none; min-width:300px;"></div>').appendTo("body");
        var loading = '<span style="padding: 19% 0 0 50%;float: left;font-size: xx-large;" >Loading...</span>';
        $("#closing_month_window").html(loading);
        closing_month_window = $("#closing_month_window");
        closing_month_window.kendoWindow({
            iframe: false,
            actions: ["Close"],
            content: "<?php echo URL.'/salesinvoices/closing_months' ?>",
            visible: false,
            resizable: false,
            draggable: false,
            width: "auto",
            title: 'Closing Month',
            pinned: true,
        }).data("kendoWindow").open();
        closing_month_window.data("kendoWindow").maximize();
    }
</script>