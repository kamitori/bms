<?php echo $this->element('js'); ?>
<script type="text/javascript">
	$(function() {
        $(".container").delegate("input,select","change",function(){
            var fieldname = $(this).attr("name");  // data[Product][name] vs name -- data[Product][addresses][address_1]
            var fieldtype = $(this).attr("type");  // text vs text
            var ids = $("#mongoid").val();  // undefined vs 542b8a09005fc3cc220000db --> fixed
            var values = $(this).val();  // Hv13
        
            fieldname =  fieldname.replace("data[Product][", "");
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
                    if (fieldname == 'product_type' || fieldname == 'sell_by') location.reload();
                    $.mobile.loading( 'hide' );//ajax_note('Email not valid, please check email field!');
                }
            });
            //product_auto_save();
        });
    });
    
    function after_choose_companies(valueid, values, key){
        var ids = $("#mongoid").val();  //  542b8a09005fc3cc220000db 
        if(key=='company_name'){
            $.ajax({
                url: '<?php echo URL.'/'.$controller;?>/save_data',   
                type:"POST",
                data: {field:"company_name",value:values,ids:ids,valueid:valueid},
                success: function(text_return){
                    text_return = text_return.split("||");
                    //alert(text_return);
                    $("#ProductCompanyName").val(values);
                    $("#ProductCompanyId").val(valueid);
                    $.mobile.loading( 'hide' ); //ajax_note("Saving...Saved !");
                }
            });
            backToMain();
        }
    }

    jQuery.fn.ForceNumericOnly =
    function()
    {
        return this.each(function()
        {
            $(this).keydown(function(e)
            {
                var key = e.charCode || e.keyCode || 0;
                // allow backspace, tab, delete, enter, arrows, numbers and keypad numbers ONLY
                // home, end, period, and numpad decimal
                return (
                    key == 8 || 
                    key == 9 ||
                    key == 13 ||
                    key == 46 ||
                    key == 110 ||
                    key == 190 ||
                    (key >= 35 && key <= 40) ||
                    (key >= 48 && key <= 57) ||
                    (key >= 96 && key <= 105));
            });
        });
    };
    $("#ProductThickness, #ProductSizew, #ProductSizeh, #ProductSellPrice, #ProductCostPrice, #ProductUnitPrice").ForceNumericOnly();

    /*function product_auto_save(){
    }*/
</script>