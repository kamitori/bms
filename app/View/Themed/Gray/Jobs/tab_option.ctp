<?php echo $this->element('entry_tab_option'); ?>
<input type="hidden" id="click_open_window_salesinvoice" />
<script type="text/javascript">
	$(function(){
		window_popup("salesinvoices","Combine to Sales invoice","salesinvoice_options","click_open_window_salesinvoice","?job_id=<?php echo $_SESSION['JobsViewId'] ?>");
        $("#closing_month").attr("onclick","closing_month()");
		$("#combine_salesinvoices").removeAttr("onclick");
        $("#combine_salesorders").removeAttr("onclick");
        $("#create_invoice_from_combine_salesorders").removeAttr("onclick");
        open_popup("Combine orders","combine_salesorders","combine_salesorders");
        open_popup("Combine invoices","combine_salesinvoices","combine_salesinvoices");
        open_popup("Create Invoice from Combine Sales orders","create_invoice_from_combine_salesorders","create_invoice_from_combine_salesorders");
	})
	function after_choose_salesinvoices(ids,names,keys){
		$(".k-window").fadeOut('slow');
		$.ajax({
			url: "<?php echo URL.'/jobs/combine_salesinvoices/' ?>",
			type: "POST",
			data: {ids:ids},
			success: function(result){
				result = $.parseJSON(result);
                window.location.replace(result.url);
			}
		})
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
            content: "<?php echo URL.'/jobs/closing_months' ?>",
            visible: false,
            resizable: false,
            draggable: false,
            width: "auto",
            title: 'Closing Month',
            pinned: true,
        }).data("kendoWindow").open();
        closing_month_window.data("kendoWindow").maximize();
    }
    function open_popup(title, key, key_click_open){
        if(key == "combine_salesorders" || key == "create_invoice_from_combine_salesorders")
            controller = "salesorders";
        else if(key == "combine_salesinvoices")
            controller = "salesinvoices";
        // ---- set default ----
        if( key == undefined ){
            key = "";
        }
        var div_popup_id = controller + key;
        console.log('div_popup_id:'+div_popup_id);
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
                    if(key == "combine_salesorders")
                        url = "<?php echo URL; ?>/jobs/combine_orders_popup/" + key;
                    else if(key == "combine_salesinvoices")
                        url = "<?php echo URL; ?>/jobs/combine_invoices_popup/" + key;
                    else if(key == "create_invoice_from_combine_salesorders")
                        url = "<?php echo URL; ?>/jobs/combine_orders_popup/" + key;

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

    function create_full_salesinvoice(saleorder_id){
        $.ajax({
            url: "<?php echo URL; ?>/salesorders/create_full_salesinvoice/"+saleorder_id,
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
</script>