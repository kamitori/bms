<style type="text/css">
.error_input{
  border: 1px solid red !important;
}
</style>
<?php if(!$request->is('ajax')) echo $this->element('js');?>
<script type="text/javascript">
	$(function() {
        $(".container").delegate("input,select","change",function(){
            var fieldname = $(this).attr("name");  // data[Salesorder][name] vs name -- data[Salesorder][addresses][address_1]
            var fieldtype = $(this).attr("type");  // text vs text
            var ids = $("#mongoid").val();  // undefined vs 542b8a09005fc3cc220000db --> fixed
            var values = $(this).val();  // Hv13
            if (fieldname.indexOf("address")==-1) {
                fieldname =  fieldname.replace("data[Salesorder][", "");
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
                    url: '<?php echo URL.'/'.$controller;?>/ajax_save',   
                    type:"POST",
                    data: {field:fieldname,value:values,func:func,ids:ids},
                    success: function(text_return){
                     
                        text_return = text_return.split("||");
                            if (text_return == "email_not_valid"){
                                    $("#SalesorderEmail").addClass('error_input');
                                    $.mobile.loading( 'hide' );//ajax_note('Email not valid, please check email field!');
                            }else{
                                    $("#SalesorderEmail").removeClass('error_input');
                                    $.mobile.loading( 'hide' ); //ajax_note("Saving...Saved !");
                            }
                    }
                });
            } else { // neu field la addresses
               
                if (fieldname.indexOf("invoice_address")!=-1) {
                    fieldname =  fieldname.replace("data[Salesorder][invoice_address][0][", "");
                    fieldname =  fieldname.replace("]", "");  // address_1
                    // alert(fieldname);
                    loading("Saving..."); // $(".jt_ajax_note").html("Saving...       ");
                    var text = $("#" + $(this).attr('id') + " option:selected").text(); //alert(values + ' ' +text);
                    save_address_invoice(fieldname,values,'','',text);
                }
                else if (fieldname.indexOf("shipping_address")!=-1) {
                    fieldname =  fieldname.replace("data[Salesorder][shipping_address][0][", "");
                    fieldname =  fieldname.replace("]", "");  // address_1
                    // alert(fieldname);
                    loading("Saving..."); // $(".jt_ajax_note").html("Saving...       ");
                    var text = $("#" + $(this).attr('id') + " option:selected").text(); //alert(values + ' ' +text);
                    save_address_shipping(fieldname,values,'','',text);
                }
            }
            //salesorder_auto_save();
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
    function change_pro(){
        location.reload();
    }
    /*function salesorder_auto_save(){
    }*/

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
                    $("#SalesorderCompanyName").val(values);
                    $("#SalesorderCompanyId").val(valueid);
                    $.mobile.loading( 'hide' ); //ajax_note("Saving...Saved !");
                    change_pro();
                }
            });
            backToMain();
        }
        else if(key=='shipper_name'){
            $.ajax({
                url: '<?php echo URL.'/'.$controller;?>/save_data',   
                type:"POST",
                data: {field:"shipper",value:values,ids:ids,valueid:valueid},
                success: function(text_return){
                    $("#SalesorderShipper").val(values);
                    $("#SalesorderShipperId").val(valueid);
                    $.mobile.loading( 'hide' ); //ajax_note("Saving...Saved !");
                }
            });
            backToMain();
        }
        //salesorder_auto_save();
    }
    function after_choose_contacts(valueid, values, key){
        var ids = $("#mongoid").val();  //  542b8a09005fc3cc220000db 
        $.ajax({
            url: '<?php echo URL.'/'.$controller;?>/save_data',   
            type:"POST",
            data: {field:key,value:values,ids:ids,valueid:valueid},
            success: function(text_return){
                //text_return = text_return.split("||");
                if (key == 'our_rep') {
                    $("#SalesorderOurRep").val(values);
                    $("#SalesorderOurRepId").val(valueid);
                }
                else if (key == 'our_csr') {
                    $("#SalesorderOurCsr").val(values);
                    $("#SalesorderOurCsrId").val(valueid);
                }
                else if(key == 'contact_name') {
                    $("#SalesorderContactName").val(values);
                    $("#SalesorderContactId").val(valueid);
                }
                $.mobile.loading( 'hide' ); //ajax_note("Saving...Saved !");
            }
        });
        backToMain();
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
            save_data_form_to_job(module_from,id,arr);
            backToMain();
            //salesorder_auto_save();
        }
    }
    function save_data_form_to_job(module_from,ids,arr){
        var jsonString = JSON.stringify(arr);
        $.ajax({
            url: '<?php echo URL.'/'.$controller;?>/save_data_form_to',
            dataType: "json",
            type:"POST",
            data: {module_from:module_from,ids:ids,arr:jsonString},
            success: function(data_return){
                //$("#SalesorderJobNo").val(code);
                $("#SalesorderJobNumber").val(data_return.job_number);
                $("#SalesorderJobName").val(data_return.job_name);
                $("#SalesorderJobId").val(data_return.job_id);    
            }
        });
    }

    function after_choose_quotations(ids,names,keys){
        if(keys=='quotation_name'){
            var module_from = 'Quotation';
            var arr = {
                        "_id"   :"quotation_id",
                        "name":"quotation_name",
                        "code"  :"quotation_number"
                     }; //danh sách các field cần nhận về từ module jobs, và fields cần lưu
            save_data_form_to_quotation(module_from,ids,arr);
            backToMain();
        }
    }
    function save_data_form_to_quotation(module_from,ids,arr){
        var jsonString = JSON.stringify(arr);
        $.ajax({
            url: '<?php echo URL.'/'.$controller;?>/save_data_form_to',
            dataType: "json",
            type:"POST",
            data: {module_from:module_from,ids:ids,arr:jsonString},
            success: function(data_return){
                //$("#SalesorderQuotationNo").val(code);
                $("#SalesorderQuotationNumber").val(data_return.quotation_number);
                $("#SalesorderQuotationName").val(data_return.quotation_name);
                $("#SalesorderQuotationId").val(data_return.quotation_id);  
            }
        });
    }
</script>