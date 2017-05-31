<script type="text/javascript">
    $(function() {
        $(".contain").delegate("input,select","change",function(){
            contact_auto_save();
        });
    });
    function contact_auto_save(){
        loading("Saving...");
        changeIDValue();
        contacts_update_entry_header("<?php echo $this->data['Contact']['_id']; ?>");
        $.ajax({
            url:"<?php echo M_URL.'/'.$controller; ?>/auto_save",
            type:"POST",
            data:$(".<?php echo $controller; ?>_form_auto_save","#main_page").serialize(),
            success: function(result){
                $.mobile.loading( 'hide' );
                if(result!='ok')
                    alerts("Error",result);
            }
        });
    }
    function after_choose_equipments(equipment_id, equipment_name) {
        $("#ContactOurRepId","#main_page").val(equipment_id);
        $("#ContactOurRep","#main_page").val(equipment_name);
        $("#ContactOurRepType").val("assets");
        $.mobile.changePage("#main_page",{
            transition: "flow",
        });
        contact_auto_save();
        return false;
    }
    function after_choose_contacts(contact_id, contact_name){
        var inputHolder = $("#inputHolder","#contacts_popup").val();
        if(inputHolder == "ContactContactName"){
            $("#ContactContactId","#main_page").val(contact_id);
            $("#ContactContactName","#main_page").val(contact_name);
        } else if(inputHolder == "ContactOurRep"){
            $("#ContactOurRepId","#main_page").val(contact_id);
            $("#ContactOurRep","#main_page").val(contact_name);
            $("#ContactOurRepType").val("contacts");
        }
        $.mobile.changePage("#main_page",{
            transition: "flow",
        });
        contact_auto_save();
        return false;
    }
    function after_choose_companies(company_id, company_name){

        $("#ContactCompanyId","#main_page").val(company_id);
        $("#ContactCompanyName","#main_page").val(company_name);
        $.mobile.changePage("#main_page",{
            transition: "flow",
        });
        contact_auto_save();
        // khởi tạo lại kendo window của sales order tương ứng với company mới
        // contacts_init_popup_contacts("force_re_install");

        // contacts_init_popup_enquiries("force_re_install");

        // contacts_init_popup_quotations("force_re_install");

        // contacts_init_popup_jobs("force_re_install");

        // contacts_init_popup_salesorders("force_re_install");

        // contacts_init_popup_purchaseorders("force_re_install");

    }
    function contacts_update_entry_header(id) {
        var oneDay = 24 * 60 * 60 * 1000; // hours*minutes*seconds*milliseconds
        var f_firstDate = new Date($("#work_end_"+id).val());
        var firstDate = new Date(f_firstDate.getFullYear(), f_firstDate.getMonth() + 1, f_firstDate.getDate());
        var f_secondDate = new Date();
        var secondDate = new Date(f_secondDate.getFullYear(), f_secondDate.getMonth() + 1, f_secondDate.getDate());

        // Math.abs(
        var diffDays = Math.round((firstDate.getTime() - secondDate.getTime()) / (oneDay));

        // var diffDays = Math.round(Math.abs((firstDate.getTime() - secondDate.getTime())/(oneDay)));
        if (parseInt(diffDays) < 0) {

            $("#contacts_days_left_"+id).attr("style", "color: red");

        } else {
            $("#contacts_days_left_"+id).removeAttr("style");

        }
        $("#contacts_days_left_"+id).val(diffDays);
    }

    function contacts_auto_save_entry(object) {
        if ($.trim($("#ContactHeading").val()) == "") {
            $("#ContactHeading").val("#" + $("#ContactNo").val() + "-" + $("#ContactCompanyName").val());
        }

        contacts_update_entry_header();

        $("form :input", "#contacts_form_auto_save").removeClass('error_input');

        $.ajax({
            url: '<?php echo M_URL; ?>/contacts/auto_save/' + $(object).attr("name"),
            timeout: 15000,
            type: "post",
            data: $("#contacts_form_auto_save","#main_page").serialize(),
            success: function(html) {
                if ($.trim(html) == "ref_no_existed") {
                    $("#ContactNo").addClass('error_input');
                    alerts('Error', 'This "no" existed');

                } else if (html == "date_work") {
                    $("#ContactWorkEnd").addClass('error_input');
                    $("#ContactWorkEndHour").addClass('error_input');
                    alerts("Error: ", '"Work start date" can not greater than "Work end date"');

                } else if (html != "ok") {

                    if( $(object).hasClass("force_reload") ){// khi thay đổi giờ ...
                        $("#entry_udpate_date").html(html);
                        $(":input", "#entry_udpate_date").change(function() {
                            contacts_auto_save_entry(this);
                        });
                        input_show_select_calendar(".JtSelectDate", "#entry_udpate_date");
                    }else{
                        alerts("Error: ", html);
                    }
                }
            }
        });
    }
</script>