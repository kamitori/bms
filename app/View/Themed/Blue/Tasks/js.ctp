<?php echo $this->element('js/permission_entry'); ?>
<script type="text/javascript">
    $(function() {
        tasks_update_entry_header();
    });

    function tasks_update_entry_header() {

        $("#task_name_header").html($("#TaskName").val());
        setTimeout('$("#task_work_start_header").html($("#TaskWorkStart").val());', 1800);
        $("#task_status_header").html($("#TaskStatus").val());
        $("#task_assign_to_header").html($("#TaskOurRep").val());

        var oneDay = 24 * 60 * 60 * 1000; // hours*minutes*seconds*milliseconds
        var f_firstDate = new Date($("#TaskWorkEnd").val());
        var firstDate = new Date(f_firstDate.getFullYear(), f_firstDate.getMonth() + 1, f_firstDate.getDate());
        var f_secondDate = new Date();
        var secondDate = new Date(f_secondDate.getFullYear(), f_secondDate.getMonth() + 1, f_secondDate.getDate());

        // Math.abs(
        var diffDays = Math.round((firstDate.getTime() - secondDate.getTime()) / (oneDay));

        // var diffDays = Math.round(Math.abs((firstDate.getTime() - secondDate.getTime())/(oneDay)));
        if (parseInt(diffDays) < 0) {

            $("#tasks_days_left").attr("style", "color: red");

        } else {
            $("#tasks_days_left").removeAttr("style");

        }
        $("#tasks_days_left").val(diffDays);
    }

    function tasks_auto_save_entry(object) {
        if ($.trim($("#TaskHeading").val()) == "") {
            $("#TaskHeading").val("#" + $("#TaskNo").val() + "-" + $("#TaskCompanyName").val());
        }

        tasks_update_entry_header();

        $("form :input", "#tasks_form_auto_save").removeClass('error_input');

        $.ajax({
            url: '<?php echo URL; ?>/tasks/auto_save/' + $(object).attr("name"),
            timeout: 15000,
            type: "post",
            data: $("form", "#tasks_form_auto_save").serialize(),
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
                    alerts('Message', 'This "no" existed');

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
                console.log(html); // view log when debug
            }
        });
    }
</script>