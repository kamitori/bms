<?php echo $this->element('js'); ?>
<script type="text/javascript">
    $(function() {
        $(".container").delegate("input,select","change",function(){
            task_auto_save();
        });
    });
	function after_choose_companies(id, name, key){
		$("#TaskCompanyName").val(name);
		$("#TaskCompanyId").val(id);
		backToMain();
		task_auto_save();
	}
    function after_choose_salesorders(id, name, key, code){
        $("#TaskSalesorderName").val(name);
        $("#TaskSalesorderId").val(id);
        $("#TaskSalesorderNo").val(code);
        backToMain();
        task_auto_save();
    }

    function after_choose_jobs(id, name, key, code){
        $("#TaskJobNo").val(code);
        $("#TaskJobName").val(name);
        $("#TaskJobId").val(id);
        backToMain();
        task_auto_save();
    }


    function after_choose_quotations(id, name, key, code){
        $("#TaskQuotationNo").val(code);
        $("#TaskQuotationName").val(name);
        $("#TaskQuotationId").val(id);
        backToMain();
        task_auto_save();
    }

    function after_choose_enquiries(id, name, key, code){
        $("#TaskEnquiryNo").val(code);
        $("#TaskEnquiryName").val(name);
        $("#TaskEnquiryId").val(id);
        backToMain();
        task_auto_save();
    }

    function after_choose_contacts(id, name, key){
        $("#TaskContactName").val(name);
        $("#TaskContactId").val(id);
        backToMain();
        task_auto_save();
    }

    function after_choose_purchaseorders(id, name, key, code){
        $("#TaskPurchaseorderName").val(name);
        $("#TaskPurchaseorderId").val(id);
        $("#TaskPurchaseorderNo").val(code);
        backToMain();
        task_auto_save();
    }

	function task_auto_save(){
        loading("Saving...");
        tasks_update_entry_header("<?php echo $this->data['Task']['_id']; ?>");
        $.ajax({
            url:"<?php echo M_URL.'/'.$controller; ?>/auto_save",
            type:"POST",
            data:$(".<?php echo $controller; ?>_form_auto_save","#main-page").serialize(),
            success: function(result){
                $.mobile.loading( 'hide' );
                if(result!='ok')
                    alerts("Error",result);
            }
        });
    }
    function tasks_update_entry_header(id) {
        var oneDay = 24 * 60 * 60 * 1000; // hours*minutes*seconds*milliseconds
        var f_firstDate = new Date($("#work_end_"+id).val());
        var firstDate = new Date(f_firstDate.getFullYear(), f_firstDate.getMonth() + 1, f_firstDate.getDate());
        var f_secondDate = new Date();
        var secondDate = new Date(f_secondDate.getFullYear(), f_secondDate.getMonth() + 1, f_secondDate.getDate());
        var diffDays = Math.round((firstDate.getTime() - secondDate.getTime()) / (oneDay));
        if (parseInt(diffDays) < 0) {
            $("#tasks_days_left_"+id).attr("style", "color: red");
        } else {
            $("#tasks_days_left_"+id).removeAttr("style");
        }
        $("#tasks_days_left_"+id).val(diffDays);
    }
</script>