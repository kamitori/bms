<?php echo $this->element('js'); ?>
<script type="text/javascript">
	$(function() {
        $(".container").delegate("input,select","change",function(){
            var fieldname = $(this).attr("name");  // data[Receipt][name] vs name -- data[Receipt][addresses][address_1]
            var fieldtype = $(this).attr("type");  // text vs text
            var ids = $("#mongoid").val();  // undefined vs 542b8a09005fc3cc220000db --> fixed
            var values = $(this).val();  // Hv13
           
            fieldname =  fieldname.replace("data[Receipt][", "");
            fieldname =  fieldname.replace("]", "");  // name

            var func = ''; var titles = new Array();

            if(ids!='')
                func = 'update'; //add,update
            else
                func = 'add';

            if(fieldtype=='checkbox'){
                if($(this).is(':checked'))
                    values = 1;
                else
                    values = 0;
            }
            loading("Saving..."); // $(".jt_ajax_note").html("Saving...       ");
            $.ajax({
                url: '<?php echo URL.'/'.$controller;?>/ajax_save', 
                type:"POST",
                data: {field:fieldname,value:values,func:func,ids:ids},
                success: function(text_return){
                    //text_return = text_return.split("||");
                    $.mobile.loading( 'hide' ); //ajax_note("Saving...Saved !");
                }
            });

            //receipt_auto_save();
        });
    });

    function after_choose_contacts(valueid, values, key){
        var ids = $("#mongoid").val();  //  542b8a09005fc3cc220000db 
        $.ajax({
            url: '<?php echo URL.'/'.$controller;?>/save_data',   
            type:"POST",
            data: {field:key,value:values,ids:ids,valueid:valueid},
            success: function(text_return){
                //text_return = text_return.split("||");
                if (key == 'our_rep') {
                    $("#ReceiptOurRep").val(values);
                    $("#ReceiptOurRepId").val(valueid);
                }
                else if (key == 'our_csr') {
                    $("#ReceiptOurCsr").val(values);
                    $("#ReceiptOurCsrId").val(valueid);
                }
                $.mobile.loading( 'hide' ); //ajax_note("Saving...Saved !");
            }
        });
        backToMain();
    }
    function  after_choose_salesaccounts(valueid, values, key) {
        var ids = $("#mongoid").val();
        $.ajax({
            url: '<?php echo M_URL.'/'.$controller;?>/save_data',   
            type:"POST",
            data: {field:key,value:values,ids:ids,valueid:valueid},
            success: function(text_return){
                //text_return = text_return.split("||");
                    $("#ReceiptSalesaccountName").val(values);
                    $("#ReceiptSalesaccountId").val(valueid);
                $.mobile.loading( 'hide' ); //ajax_note("Saving...Saved !");
            }
        });
        backToMain();
    }

     /*function receipt_auto_save(){
    }*/
</script>