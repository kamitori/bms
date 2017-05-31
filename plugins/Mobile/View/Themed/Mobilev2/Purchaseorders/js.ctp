<?php echo $this->element('js'); ?>
<script type="text/javascript">
	$(function() {
        $(".container").delegate("input,select","change",function(){
            var fieldname = $(this).attr("name");  // data[Purchaseorder][name] vs name -- data[Purchaseorder][addresses][address_1]
            var fieldtype = $(this).attr("type");  // text vs text
            var ids = $("#mongoid").val();  // undefined vs 542b8a09005fc3cc220000db --> fixed
            var values = $(this).val();  // Hv13
            if (fieldname.indexOf("address")==-1) {
                fieldname =  fieldname.replace("data[Purchaseorder][", "");
                fieldname =  fieldname.replace("]", "");  // name

                var func = ''; var titles = new Array();

                if(ids!='')
                    func = 'update'; //add,update
                else
                    func = 'add';

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
                                    $("#PurchaseorderEmail").addClass('error_input');
                                    $.mobile.loading( 'hide' );//ajax_note('Email not valid, please check email field!');
                            }else{
                                    $("#PurchaseorderEmail").removeClass('error_input');
                                    $.mobile.loading( 'hide' ); //ajax_note("Saving...Saved !");
                            }
                    }
                });
            } else { // neu field la addresses
                if (fieldname.indexOf("shipping_address")!=-1) {
                    fieldname =  fieldname.replace("data[Purchaseorder][shipping_address][0][", "");
                    fieldname =  fieldname.replace("]", "");  // address_1
                    // alert(fieldname);
                    loading("Saving..."); // $(".jt_ajax_note").html("Saving...       ");
                    var text = $("#" + $(this).attr('id') + " option:selected").text(); //alert(values + ' ' +text);
                    save_address_shipping(fieldname,values,'','',text);
                }
            }
            //purchaseorder_auto_save();
        });
    });

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
 
    function after_choose_companies(valueid, values, key){
        var ids = $("#mongoid").val();  //  542b8a09005fc3cc220000db 
        $.ajax({
            url: '<?php echo URL.'/'.$controller;?>/save_data',   
            type:"POST",
            data: {field:key,value:values,ids:ids,valueid:valueid},
            success: function(text_return){
                text_return = text_return.split("||");
                if(key=='company_name'){
                    $("#PurchaseorderCompanyName").val(values);
                    $("#PurchaseorderCompanyId").val(valueid);
                    $.mobile.loading( 'hide' ); //ajax_note("Saving...Saved !");
                    change_pro();
                } else if (key=='ship_to_company_name') {
                    $("#PurchaseorderShipToCompanyName").val(values);
                    $("#PurchaseorderShipToCompanyId").val(valueid);
                    $.mobile.loading( 'hide' ); //ajax_note("Saving...Saved !");
                } else if (key=='shipper_company_name') {
                    $("#PurchaseorderShipperCompanyName").val(values);
                    $("#PurchaseorderShipperCompanyId").val(valueid);
                    $.mobile.loading( 'hide' ); //ajax_note("Saving...Saved !");
                }
            }
        });
        backToMain();
    }
    function change_pro(){
        location.reload();
    }
    
    function after_choose_jobs(id, name, key, code){
        if(key=='job_name'){
            var module_from = 'Job';
            var arr = {
                        "_id"   :"job_id",
                        "name"  :"job_name",
                        "no"    :"job_number"
                     }; //danh sách các field cần nhận về từ module jobs, và fields cần lưu 
            save_data_form_to_job(module_from,id,arr);
            backToMain();
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
                //$("#PurchaseorderJobNo").val(code);
                $("#PurchaseorderJobNumber").val(data_return.job_number);
                $("#PurchaseorderJobName").val(data_return.job_name);
                $("#PurchaseorderJobId").val(data_return.job_id);    
            }
        });
    }

    function after_choose_salesorders(ids,names,keys){
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
                $("#PurchaseorderSalesorderNumber").val(data_return.salesorder_number);
                $("#PurchaseorderSalesorderName").val(data_return.salesorder_name);
                $("#PurchaseorderSalesorderId").val(data_return.salesorder_id);       
            }
        });
    }

    function after_choose_contacts(valueid, values, key){
        var ids = $("#mongoid").val();  //  542b8a09005fc3cc220000db 
        $.ajax({
            url: '<?php echo URL.'/'.$controller;?>/save_data',   
            type:"POST",
            data: {field:key,value:values,ids:ids,valueid:valueid},
            success: function(text_return){
                //text_return = text_return.split("||");
                if (key=='contact_name') {
                    $("#PurchaseorderContactName").val(values);
                    $("#PurchaseorderContactId").val(valueid);
                    location.reload();
                } else if (key=='our_rep') {
                    $("#PurchaseorderOurRep").val(values);
                    $("#PurchaseorderOurRepId").val(valueid);
                } else if (key=='ship_to_contact_name') {
                    $("#PurchaseorderShipToContactName").val(values);
                    $("#PurchaseorderShipToContactId").val(valueid);     
                } else if (key=='received_by_contact_name') {
                    $("#PurchaseorderReceivedByContactName").val(values);
                    $("#PurchaseorderReceivedByContactId").val(valueid);
                }
                $.mobile.loading( 'hide' ); //ajax_note("Saving...Saved !"); 
            }
        });
        backToMain();
    }
</script>