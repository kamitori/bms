<?php echo $this->element('tab_option'); ?>
<script type="text/javascript">
$(function(){
    $('#print_active_clients').attr({'href': '<?php  echo URL.'/contacts/clients' ?>', 'target': '_blank' });
	$("#hour_worked_last, #hour_worked_current, #print_active_clients").removeAttr("onclick");
    open_popup("Specify employee","hour_worked_last");
    open_popup("Specify employee","hour_worked_current");
    $("body").on("click", "#employee_submit", function(){
    	if( !$(".check_employees:checked","#employees_table").length ) {
    		alerts("Message", "Please choose at least one employee.");
    	} else {
    		var url = "<?php echo URL ?>/contacts/" + currentPDF;
    		var html = '<form id="hour_worked_form" action="'+url+'" method="POST">';
    		$(".check_employees:checked","#employees_table").each(function(){
    			html += '<input type="hidden" name="employees[]" value="'+$(this).val()+'" />';
    		});
    		html += '<input type="submit" class="hidden" />';
    		html += "</form>";
    		$("body").append(html);
    		$("[type=submit]", "#hour_worked_form").click();
    	}
    });
    var currentPDF;
    function open_popup(title,  key_click_open){
        controller = "contacts";
        // ---- set default ----



        var div_popup_id = controller

        // -------------- bắt đầu code -------------------------------

        // Kiểm tra tồn tại trước khi tạo lại
        if( $("#window_popup_" + div_popup_id).attr("id") == undefined )
            $('<div id="window_popup_' + div_popup_id + '" style="display:none; min-width:300px;"></div>').appendTo("body");

        var window_popup = $("#window_popup_" + div_popup_id);

        if( key_click_open != undefined && key_click_open != "" ){
            var click = $("#" + key_click_open);
        }else{
            var click = $("#click_open_window_" + div_popup_id);
        }

        // refesh lại kendo window để hiển thị các giá trị chọn mới

        click.bind("click", function() {
    		currentPDF = key_click_open;
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
                    $.ajax({
                        url: "<?php echo URL; ?>/contacts/working_hours_employee/",
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

});
</script>