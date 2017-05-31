<script type="text/javascript">
    $(function() {
        $(".contain").delegate("input,select","change",function(){
            task_auto_save();
        });
    });
    function task_auto_save(){
        loading("Saving...");
        changeIDValue();
        tasks_update_entry_header("<?php echo $this->data['Task']['_id']; ?>");
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
        $("#TaskOurRepId","#main_page").val(equipment_id);
        $("#TaskOurRep","#main_page").val(equipment_name);
        $("#TaskOurRepType").val("assets");
        $.mobile.changePage("#main_page",{
            transition: "flow",
        });
        task_auto_save();
        return false;
    }
    function after_choose_contacts(contact_id, contact_name){
        var inputHolder = $("#inputHolder","#contacts_popup").val();
        if(inputHolder == "TaskContactName"){
            $("#TaskContactId","#main_page").val(contact_id);
            $("#TaskContactName","#main_page").val(contact_name);
        } else if(inputHolder == "TaskOurRep"){
            $("#TaskOurRepId","#main_page").val(contact_id);
            $("#TaskOurRep","#main_page").val(contact_name);
            $("#TaskOurRepType").val("contacts");
        }
        $.mobile.changePage("#main_page",{
            transition: "flow",
        });
        task_auto_save();
        return false;
    }
    function after_choose_companies(company_id, company_name){

        $("#TaskCompanyId","#main_page").val(company_id);
        $("#TaskCompanyName","#main_page").val(company_name);
        $.mobile.changePage("#main_page",{
            transition: "flow",
        });
        task_auto_save();
        // khởi tạo lại kendo window của sales order tương ứng với company mới
        // tasks_init_popup_contacts("force_re_install");

        // tasks_init_popup_enquiries("force_re_install");

        // tasks_init_popup_quotations("force_re_install");

        // tasks_init_popup_jobs("force_re_install");

        // tasks_init_popup_salesorders("force_re_install");

        // tasks_init_popup_purchaseorders("force_re_install");

    }
    function tasks_update_entry_header(id) {
        var oneDay = 24 * 60 * 60 * 1000; // hours*minutes*seconds*milliseconds
        var f_firstDate = new Date($("#work_end_"+id).val());
        var firstDate = new Date(f_firstDate.getFullYear(), f_firstDate.getMonth() + 1, f_firstDate.getDate());
        var f_secondDate = new Date();
        var secondDate = new Date(f_secondDate.getFullYear(), f_secondDate.getMonth() + 1, f_secondDate.getDate());

        // Math.abs(
        var diffDays = Math.round((firstDate.getTime() - secondDate.getTime()) / (oneDay));

        // var diffDays = Math.round(Math.abs((firstDate.getTime() - secondDate.getTime())/(oneDay)));
        if (parseInt(diffDays) < 0) {

            $("#tasks_days_left_"+id).attr("style", "color: red");

        } else {
            $("#tasks_days_left_"+id).removeAttr("style");

        }
        $("#tasks_days_left_"+id).val(diffDays);
    }

    function tasks_auto_save_entry(object) {
        if ($.trim($("#TaskHeading").val()) == "") {
            $("#TaskHeading").val("#" + $("#TaskNo").val() + "-" + $("#TaskCompanyName").val());
        }

        tasks_update_entry_header();

        $("form :input", "#tasks_form_auto_save").removeClass('error_input');

        $.ajax({
            url: '<?php echo M_URL; ?>/tasks/auto_save/' + $(object).attr("name"),
            timeout: 15000,
            type: "post",
            data: $("#tasks_form_auto_save","#main_page").serialize(),
            success: function(html) {
                if(html == "work_start_salesorder_date"){
                    $(object).addClass('error_input');
                    alerts("Error: ", '"Work start" can not less than "Order date" of this SO');

                }else if(html == "work_end_payment_due_date"){
                    $(object).addClass('error_input');
                    alerts("Error: ", '"Work End" can not greater than "Due date" of this SO');

                }else if(html == "work_start_due_date"){
                    $(object).addClass('error_input');
                    alerts("Error: ", '"Work start" can not greater than "Due date" of this SO');

                }else if(html == "error_work_start"){
                    $(object).addClass('error_input');
                    alerts("Error: ", '"Work start" can not set into the past');

                }else if(html == "error_work_end"){
                    $(object).addClass('error_input');
                    alerts("Error: ", '"Work End" can not set into the past');

                }else if ($.trim(html) == "ref_no_existed") {
                    $("#TaskNo").addClass('error_input');
                    alerts('Error', 'This "no" existed');

                } else if (html == "date_work") {
                    $("#TaskWorkEnd").addClass('error_input');
                    $("#TaskWorkEndHour").addClass('error_input');
                    alerts("Error: ", '"Work start date" can not greater than "Work end date"');

                } else if (html != "ok") {

                    if( $(object).hasClass("force_reload") ){// khi thay đổi giờ ...
                        $("#entry_udpate_date").html(html);
                        $(":input", "#entry_udpate_date").change(function() {
                            tasks_auto_save_entry(this);
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