<style type="text/css">
.error_input{
  border: 1px solid red !important;
}
</style>
<?php echo $this->element('js'); ?>
<script type="text/javascript">
	$(function() {
        $(".container").delegate("input,select","change",function(){

            var fieldname = $(this).attr("name");  // data[Company][name] vs name -- data[Company][addresses][address_1]
            var fieldtype = $(this).attr("type");  // text vs text
            modulename = 'mongoid';
            var ids = $("#mongoid").val();  // undefined vs 542b8a09005fc3cc220000db --> fixed
            var values = $(this).val();  // Hv13
            if (fieldname.indexOf("address")==-1) {
                fieldname =  fieldname.replace("data[Quotation][", "");
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
                if(fieldname=='tax'){
                    var text = $("#" + $(this).attr('id') + " option:selected").text();//"15% (Nova Scotia) HST" alert(values + ' ' +text);
                    arrva = text.split('%');
                    taxval = arrva[0];
                    $.ajax({
                        url: '<?php echo URL.'/'.$controller;?>/ajax_save',   
                        type:"POST",
                        data: {field:'taxval',value:taxval,func:func,ids:ids},
                        success: function(text_return){
                            console.log('Thanh cong' + taxval);
                        }
                    });

                    var arrvalue =  {"taxper":taxval};
                    update_all_option('products',arrvalue,function(){});
                }

                loading("Saving..."); // $(".jt_ajax_note").html("Saving...       ");
                $.ajax({
                    url: '<?php echo URL.'/'.$controller;?>/ajax_save',   // http://jobtraq.local.com/companies
                    type:"POST",
                    data: {field:fieldname,value:values,func:func,ids:ids},
                    success: function(text_return){
                     
                        text_return = text_return.split("||");
                            if (text_return == "email_not_valid"){
                                    $("#QuotationEmail").addClass('error_input');
                                    $.mobile.loading( 'hide' );//ajax_note('Email not valid, please check email field!');
                            }else{
                                    $("#QuotationEmail").removeClass('error_input');
                                    $.mobile.loading( 'hide' ); //ajax_note("Saving...Saved !");
                            }
                    }
                });
            } else { // neu field la addresses
               
                if (fieldname.indexOf("invoice_address")!=-1) {
                    fieldname =  fieldname.replace("data[Quotation][invoice_address][0][", "");
                    fieldname =  fieldname.replace("]", "");  // address_1
                    // alert(fieldname);
                    loading("Saving..."); // $(".jt_ajax_note").html("Saving...       ");
                    var text = $("#" + $(this).attr('id') + " option:selected").text(); //alert(values + ' ' +text);
                    save_address_invoice(fieldname,values,'','',text);
                }
                else if (fieldname.indexOf("shipping_address")!=-1) {
                    fieldname =  fieldname.replace("data[Quotation][shipping_address][0][", "");
                    fieldname =  fieldname.replace("]", "");  // address_1
                    // alert(fieldname);
                    loading("Saving..."); // $(".jt_ajax_note").html("Saving...       ");
                    var text = $("#" + $(this).attr('id') + " option:selected").text(); //alert(values + ' ' +text);
                    save_address_shipping(fieldname,values,'','',text);
                }
            }
            //quotation_auto_save();
        });
    });
    function save_address_invoice(address_field,values,fieldid,handleData,text){ // '',values la gia tri luu,ContactAddress1,''
        var datas = new Object();
        if(address_field!='invoice_country'  && address_field!='invoice_province_state' && address_field!='invoice_province_state_id' && address_field!='invoice_country_id'){
            datas[address_field] = values;
        } else if(address_field == 'invoice_province_state_id') { //luu province  
            datas['invoice_province_state'] = text;
            datas['invoice_province_state_id'] = values;
        } else if(address_field == 'invoice_country_id') { // luu country
            datas['invoice_country'] = text;
            datas['invoice_country_id'] = values;
        }
        olds = 'update';
        save_option('invoice_address',datas,"0",0,'',olds,function(arr_return){});
    }
    function save_address_shipping(address_field,values,fieldid,handleData,text){ // '',values la gia tri luu,ContactAddress1,''
        var datas = new Object();
        if(address_field!='shipping_country'  && address_field!='shipping_province_state' && address_field!='shipping_province_state_id' && address_field!='shipping_country_id'){
            datas[address_field] = values;
        } else if(address_field == 'shipping_province_state_id') { //luu province  
            datas['shipping_province_state'] = text;
            datas['shipping_province_state_id'] = values;
        } else if(address_field == 'shipping_country_id') { // luu country
            datas['shipping_country'] = text;
            datas['shipping_country_id'] = values;
        }
        olds = 'update';
        save_option('shipping_address',datas,"0",0,'',olds,function(arr_return){});
    }
    function after_choose_companies(id, name, key){
        if(key=='company_name'){
            $("#QuotationCompanyName").val(name);
            $("#QuotationCompanyId").val(id);
            var fieldname = "company_name"
            var values = name; 
            var valueid = id;
            var ids = $("#mongoid").val();  //  542b8a09005fc3cc220000db 
            $.ajax({
                url: '<?php echo URL.'/'.$controller;?>/save_data',   
                type:"POST",
                data: {field:fieldname,value:values,ids:ids,valueid:valueid},
                success: function(text_return){
                    text_return = text_return.split("||");
                    //alert(text_return);
                    $.mobile.loading( 'hide' ); //ajax_note("Saving...Saved !");
                }
            });
            backToMain();
        }
    }
    function after_choose_contacts(id, name, key){
        if (key == 'our_rep') {
            $("#QuotationOurRep").val(name);
            $("#QuotationOurRepId").val(id);
            var fieldname = "our_rep";  
        }
        if (key == 'our_csr') {
            $("#QuotationOurCsr").val(name);
            $("#QuotationOurCsrId").val(id);
            var fieldname = "our_csr";
        }
        var values = name; 
        var valueid = id;
        var ids = $("#mongoid").val();  //  542b8a09005fc3cc220000db 
        $.ajax({
            url: '<?php echo URL.'/'.$controller;?>/save_data',   
            type:"POST",
            data: {field:fieldname,value:values,ids:ids,valueid:valueid},
            success: function(text_return){
                text_return = text_return.split("||");
                $.mobile.loading( 'hide' ); //ajax_note("Saving...Saved !");
            }
        });
        backToMain();
    }
    function change_pro(){
        location.reload();
    }
    function after_choose_jobs(id, name, key, code){
        //alert(id + '_' + name + '_' +  key + '_' + code);
        if(key=='job_name'){
          

            var module_from = 'Job';
            var arr = {
                        "_id"   :"job_id",
                        "name"  :"job_name",
                        "no"    :"job_number"
                     }; //danh sách các field cần nhận về từ module jobs, và fields cần lưu 
            save_data_form_to(module_from,id,arr);
            backToMain();
            //quotation_auto_save();
        }
    }
    function save_data_form_to(module_from,ids,arr){
        var jsonString = JSON.stringify(arr); var keys;
        $.ajax({
            url: '<?php echo URL.'/'.$controller;?>/save_data_form_to',
            dataType: "json",
            type:"POST",
            data: {module_from:module_from,ids:ids,arr:jsonString},
            success: function(data_return){
                //$("#QuotationJobNo").val(code);
                $("#QuotationJobNumber").val(data_return.job_number);
                $("#QuotationJobName").val(data_return.job_name);
                $("#QuotationJobId").val(data_return.job_id);
                   
            }
        });
    }
    function after_choose_salesorders(ids,names,keys){
        //alert(ids +'__' + names + '__' + keys);
        if(keys=='salesorder_name'){
            var module_from = 'Salesorder';
            var arr = {
                        "_id"   :"salesorder_id",
                        "name":"salesorder_name",
                        "code"  :"salesorder_number"
                     }; //danh sách các field cần nhận về từ module jobs, và fields cần lưu
            save_data_form_salesorder(module_from,ids,arr);
            backToMain();
        }
    }
    function save_data_form_salesorder(module_from,ids,arr){
        var jsonString = JSON.stringify(arr); var keys;
        $.ajax({
            url: '<?php echo URL.'/'.$controller;?>/save_data_form_to',
            dataType: "json",
            type:"POST",
            data: {module_from:module_from,ids:ids,arr:jsonString},
            success: function(data_return){
                $("#QuotationSalesorderNumber").val(data_return.salesorder_number);
                $("#QuotationSalesorderName").val(data_return.salesorder_name);
                $("#QuotationSalesorderId").val(data_return.salesorder_id);
                   
            }
        });
    }
    function update_all_option(opname,arr_value,handleData){
        var arr_value = JSON.stringify(arr_value);
        $.ajax({
            url: '<?php echo URL.'/'.$controller;?>/update_all_option',
            dataType: "json",
            type:"POST",
            data: {opname:opname,arr_value:arr_value},
            success: function(data_return){
                if(handleData!=undefined)
                    handleData(data_return);
            }
        });
    }
    /*function quotation_auto_save(){
    }*/
</script>