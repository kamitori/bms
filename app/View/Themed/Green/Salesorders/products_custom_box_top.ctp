<style type="text/css">
    #button_group{
        float: right;
        padding-right: 10px;
    }
</style>
<div class="float_left hbox_form" style="width:auto;">
    <a href="<?php echo URL.'/'.$controller;?>/create_email_pdf/0/group">
    	<input class="btn_pur" id="emailexport_products" type="button" value="Email Order" style="width:99%;" />
    </a>
</div>
<div class="float_left hbox_form" style="width:auto; margin-left:5px;">
<!--     <a href="javascript:void(0)" id="view_pdf" target="_blank">-->
     <a id="view_pdf">
        <input class="btn_pur" id="printexport_products" type="button" value="Export PDF" style="width:99%;" />
     </a>
</div>
<script type="text/javascript">
    $("#view_pdf").click(function(){
        confirms3("Message","Which report do you want to create?",["Detailed","Simple",""]
                  ,function(){
                    window.open("<?php echo URL.'/'.$controller; ?>/view_pdf");
                  },function(){
                    window.open("<?php echo URL.'/'.$controller; ?>/view_pdf/0/group");
                  },function(){
                    return false;
                  });
    });
</script>
<div class="middle_check" style="float: right;padding-right: 3.5%;">
    <label class="m_check2">
        <input type="checkbox" id="docket_check_checkall">
        <span class="bx_check"></span>
    </label>
</div>
<div style="float:right; margin-right: 15px">
    <a href="javascript:void(0)" id="asset_tag_report">
        <input class="btn_pur" id="asset_tag_pdf" type="button" value="Generate Dockets" style="width:99%;">
    </a>
</div>
<script type="text/javascript">
    $("#asset_tag_report").click(function(){
        $.ajax({
            url: '<?php echo URL.'/salesorders/check_generate_docket'  ?>',
            success: function(result) {
                result = $.parseJSON(result);
                if (result.error === 0) {
                    callDocket();
                } else if (result.error === 1) {
                    if (result.message) {
                        alerts("Message", result.message);
                    } else if (result.confirm) {
                        confirms("Message", result.confirm
                          ,function(){
                            callDocket();
                          },function(){
                            return false;
                          });
                    }
                }
            }
        });
    });

    function callDocket()
    {
        $.ajax({
            url: '<?php echo URL; ?>/salesorders/get_uncompleted_docket/',
            success: function(result){
                result = $.parseJSON(result);
                if(result.length){
                    var content = '';
                    for(var i = 0; i < result.length; i++){
                        content += '<ul class="ul_mag clear bg'+(i%2==0? 1 : 2)+'""><li class="class="hg_padd" style="text-align:left;width:40%;">'+result[i].product_name+'</li><li class="class="hg_padd" style="text-align:right;width:5.5%;"><span style="margin-right: 10px" id="current_qty_'+result[i].key+'">'+result[i].sizew+'</span></li><li class="class="hg_padd" style="text-align:right;width:5.5%;"><span style="margin-right: 10px" id="current_qty_'+result[i].key+'">'+result[i].sizeh+'</span></li><li class="class="hg_padd" style="text-align:right;width:7%;"><span style="margin-right: 10px" id="current_qty_'+result[i].key+'">'+result[i].quantity+'</span></li><li class="hg_padd" style="text-align:right;width:36.5%;"><input type="text" name="repair_qty_'+result[i].key+'" id="repair_qty_'+result[i].key+'" rel="'+result[i].key+'" class="input_inner jt_box_save repair_qty_docket" style="text-align: right" onkeypress="return isPrice(event);" value="'+result[i].quantity+'" /></li></ul>';
                    }
                    appendPopup(content);
                } else
                    alerts("Message","All asset tags are completed.");
            }
        });
    }

    function appendPopup(content){
        var docket = [
            '<div id="docket_popup">',
                '<ul class="ul_mag clear bg3" style="margin-top: 30px;">',
                    '<li class="hg_padd" style="text-align:left;width:39.5%;">',
                        'Product Name',
                    '</li>',
                    '<li class="hg_padd" style="text-align:right;width:5%;">',
                        'Size-W',
                    '</li>',
                    '<li class="hg_padd" style="text-align:right;width:5%;">',
                        'Size-H',
                    '</li><li class="hg_padd" style="text-align:right;width:6%;">',
                       ' Current Quantity',
                   ' </li>',
                    '<li class="hg_padd" style="text-align:right;width:37%;">',
                        'Repair Quantity',
                    '</li>',
                '</ul>',
                '<form id="docket_form">',
                    '<div id="docket_content">',
                        content,
                    '</div>',
               ' </form>',
            '</div>'].join("");
        var docket_popup = $(docket);
        docket_popup.kendoWindow({
            width: "60%",
            height: "35%",
            title: 'Docket',
            visible: false,
            close: function(){
                $("#docket_popup").data("kendoWindow").destroy();
            }
        });
        //show popup
        docket_popup.data("kendoWindow").center();
        docket_popup.data("kendoWindow").open();
        if($("#button_group").attr("id")==undefined){
            var html = '<ul id="button_group" class="menu_control float_right" style="margin-right: 50px; margin-bottom: 5px;">';
            html +=     '<li style="position: absolute;margin-top: 48px;left: 88%;background-color: #003256;width: 10%;text-align: center;">';
            html +=          '<a style=" cursor:pointer;"  href="javascript:void(0)" id="docket_cancel" >Cancel</a>';
            html +=     '</li>';
            html +=     '<li style="position: absolute;margin-top: 48px;left: 77%;background-color: #003256;width: 10%;text-align: center;">';
            html +=          '<a style=" cursor:pointer;"  href="javascript:void(0)" id="docket_ok"  >Ok</a>';
            html +=     '</li>';
            html += '</ul>';
            $("#docket_popup_wnd_title").after(html);
        }
        var error = false;
        $(".repair_qty_docket").change(function(){
            error = false;
            $(this).removeClass("error_input");
            var id = $(this).attr("rel");
            var current_qty = parseInt($("#current_qty_"+id).html());
            var value = $(this).val();
            if(value>current_qty){
                error = true;
                alerts("Message","Please enter valid quantity.");
                $(this).addClass("error_input");
            }
        });
        $("#docket_ok").click(function(){
            if(error)
                return false;
            var data = $("input","#docket_form").serialize();
            $.ajax({
                url: "<?php echo URL.'/'.$controller.'/docket_repair_save' ?>",
                type: "POST",
                data: data,
                async: false,
                success: function(result){
                    if(result!="ok")
                        alerts("Message",result);
                    else{
                        docket_popup.data("kendoWindow").destroy();
                        window.open("<?php echo URL.'/salesorders/first_page_docket/'.$mongo_id; ?>");
                    }
                }
            });

        });
        $("#docket_cancel").click(function(){
            docket_popup.data("kendoWindow").close();
        });
    }
</script>
