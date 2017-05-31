<?php echo $this->element('js'); ?>
<script type="text/javascript">
	$(function() {
        $(".container").delegate("input,select","change",function(){
            shipping_auto_save();
        });
    });

    function shipping_auto_save(){
    	loading("Saving...");
        $.ajax({
            url:"<?php echo M_URL.'/'.$controller; ?>/auto_save",
            type:"POST",
            data:$(".<?php echo $controller; ?>_form_auto_save","#main-page").serialize(),
            success: function(result){
                $.mobile.loading( 'hide' );
                /*if(result!='ok')
                    alert(result);*/
            }
        });
    }

    function after_choose_companies(id, name, key){
        if (key=='company_name') {
            $("#ShippingCompanyName").val(name);
            $("#ShippingCompanyId").val(id);
        } else if (key=='shipper') {
            $("#ShippingShipper").val(name);
            $("#ShippingShipperId").val(id);
        }
        backToMain();
        shipping_auto_save();
    }

    function after_choose_jobs(id, name, key, code){
        $("#ShippingJobNumber").val(code);
        $("#ShippingJobName").val(name);
        $("#ShippingJobId").val(id);
        backToMain();
        shipping_auto_save();
    }
    function after_choose_salesorders(id, name, key, code){
        $("#ShippingSalesorderNumber").val(code);
        $("#ShippingSalesorderName").val(name);
        $("#ShippingSalesorderId").val(id);
        backToMain();
        shipping_auto_save();
    }
    function after_choose_salesinvoices(id, name, key, code){
        $("#ShippingSalesinvoiceNumber").val(code);
        $("#ShippingSalesinvoiceName").val(name);
        $("#ShippingSalesinvoiceId").val(id);
        backToMain();
        shipping_auto_save();
    }
    function after_choose_contacts(id, name, key){
        if (key=='contact_name') {
            $("#ShippingContactName").val(name);
            $("#ShippingContactId").val(id);
        } else if (key=='our_rep') {
            $("#ShippingOurRep").val(name);
            $("#ShippingOurRepId").val(id);
        } else if (key=='our_csr') {
            $("#ShippingOurCsr").val(name);
            $("#ShippingOurCsrId").val(id);     
        } 
        backToMain();
        shipping_auto_save();
    }
   
</script>