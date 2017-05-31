<script type="text/javascript">
    $(function() {
        $("#TimelogEntryForm input, textarea").change(function() {
            timelog_auto_save_entry();
        });
        $("#time_change").change(function() {
            caldate();
        });
    });
    function reset_bg(boxname){
        var sum = $("#container_"+boxname+" .ul_mag").length;
        sum = parseInt(sum);
        var lengs = 0; var newbg ='';
        for(var i=0;i<=sum+1;i++){
            $("#container_"+boxname+" .ul_mag:eq("+i+")").removeClass('bg1').removeClass('bg2');
            $("#container_"+boxname+" .ul_mag:eq("+i+")").addClass(i%2==0 ? 'bg2' : 'bg1');
        }
    }
    Number.prototype.formatMoney = function(c, d, t){
        var n = this,
            c = isNaN(c = Math.abs(c)) ? 2 : c,
            d = d == undefined ? "." : d,
            t = t == undefined ? "," : t,
            s = n < 0 ? "-" : "",
            i = parseInt(n = Math.abs(+n || 0).toFixed(c)) + "",
            j = (j = i.length) > 3 ? j % 3 : 0;
           return s + (j ? i.substr(0, j) + t : "") + i.substr(j).replace(/(\d{3})(?=\d)/g, "$1" + t) + (c ? d + Math.abs(n - i).toFixed(c).slice(2) : "");
         };

    function FormatPrice(values){
        values = parseFloat(values);
        values = values.formatMoney(2, '.', ',');
        return values;
    }
    function save_option(opname,arr_value,opid,isreload,subtab,keys,handleData,fieldchage,module_id){
        if(opname != undefined ){
            if(keys == undefined  || keys == '')
                keys  = 'update';
            var arr = {
                    'keys' : keys,
                    'opname' : opname,
                    'value_object' : arr_value,
                    'opid' : opid
                };
            var jsonString = JSON.stringify(arr);
            if(fieldchage == undefined )
                fieldchage = '';
            if(module_id == undefined )
                module_id = '';
            //ajax_note_set(keys+"=\n"+opname+"=\n"+value_str+"=\n"+opid);
            var url = '<?php echo URL.'/'.$controller;?>/save_option';
            var popup_id = '';
            if(isreload != undefined){
                isreload = String(isreload);
                if(isreload.indexOf("&&") != -1){
                    isreload = isreload.split("&&");
                    popup_id = isreload[0];
                    isreload = isreload[isreload.length - 1];
                }
                isreload = parseInt(isreload);
            }
            if(popup_id=='')
                $(".k-window").fadeOut();
            else
                $("#"+popup_id).data("kendoWindow").close();
            $.ajax({
                url: '<?php echo URL.'/'.$controller;?>/save_option',
                type:"POST",
                data: {arr:jsonString,fieldchage:fieldchage,mongo_id:module_id},
                success: function(rtu){
                    if( isreload != undefined && isreload==1 )
                        reload_subtab(subtab);
                    else if( isreload != undefined && isreload==2)
                        reload_box(opname);
                    ajax_note("Saving ... Saved.");
                    if(handleData!=undefined)
                        handleData(rtu);
                }
            });

        }else
            return '';
    }
    function caldate() {
        $.ajax({
            url: '<?php echo URL; ?>/timelogs/entry_caltime',
            timeout: 15000,
            type: "post",
            data: $("input", "#TimelogEntryForm").serialize(),
            success: function(html) {
                $('#TimelogTotalTime').val(html);
                $(".actual").html(html);
                timelog_auto_save_entry();
            }
        });
    }

    function timelog_auto_save_entry() {
        $.ajax({
            url: '<?php echo URL; ?>/timelogs/auto_save',
            timeout: 15000,
            type: "post",
            data: $("#TimelogEntryForm input, textarea").serialize(),
            success: function(html) {
                console.log(html); // view log when debug
            }
        });
    }



    function timelog_entry_delete(id) {
        confirms("Message", "Are you sure you want to delete?",
                function() {
                    $.ajax({
                        url: '<?php echo URL; ?>/timelog/delete/' + id,
                        timeout: 15000,
                        success: function(html) {
                            if (html != "ok") {
                                alerts("Error: ", html);
                            } else {
                                $("#DocUse_" + id).fadeOut();
                            }
                            console.log(html); // view log when debug
                        }
                    });
                }, function() {
            //else do somthing
        });
    }


    function tasks_update_entry_header() {
        $("#task_name_header").html($("#TaskName").val());
        $("#task_work_start_header").html($("#TaskWorkStart").val());
        $("#task_status_header").html($("#TaskStatus option[value='" + $("#TaskStatus").val() + "']").text());
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

    function after_choose_jobs(job_id, job_name) {
        var json_data = $("#after_choose_jobs"+job_id).val();
        json_data = $.parseJSON(json_data);
        $("#TimelogJobNo").val(json_data.no);
        $("#TimelogJobId").val(job_id);
        $("#TimelogJobName").val(json_data.name);
        $(".link_to_job_name").addClass("jt_link_on").html("<a href=\"<?php echo URL.'/jobs/entry'; ?>/"+job_id+"\">Job</a>");
        $("#window_popup_jobs").data("kendoWindow").close();
        timelog_auto_save_entry();
        return false;
    }
    function after_choose_tasks(task_id, task_name) {
        var json_data = $("#after_choose_tasks"+task_id).val();
        json_data = $.parseJSON(json_data);
        $("#TimelogTaskNo").val(json_data.no);
        $("#TimelogTaskId").val(task_id);
        $("#TimelogTaskName").val(json_data.name);
        $(".link_to_task_name").addClass("jt_link_on").html("<a href=\"<?php echo URL.'/tasks/entry'; ?>/"+task_id+"\">Job</a>");
        $("#window_popup_tasks").data("kendoWindow").close();
        timelog_auto_save_entry();
        return false;
    }
</script>

