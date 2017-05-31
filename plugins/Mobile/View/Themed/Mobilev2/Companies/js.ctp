<?php echo $this->element('js'); ?>
<?php 
$default_address_key = isset($this->data['Company']['addresses_default_key']) ? $this->data['Company']['addresses_default_key'] : 0;
?>
<script type="text/javascript">
    $(function() {
        $(".container").delegate("input,select","change",function(){
            var fieldname = $(this).attr("name");  // data[Company][name] vs name -- data[Company][addresses][address_1]
            var fieldid = $(this).attr("id");   // CompanyName vs name
            fieldid = fieldname.replace("Company", "");  // name ?

            var fieldtype = $(this).attr("type");  // text vs text
            modulename = 'mongoid';
            var ids = $("#"+modulename).val();  // undefined vs 542b8a09005fc3cc220000db --> fixed
            var values = $(this).val();  // Hv13

            if (fieldname.indexOf("addresses")==-1) {
                fieldname =  fieldname.replace("data[Company][", "");
                fieldname =  fieldname.replace("]", "");  // name
                if (fieldname == 'is_customer' || fieldname == 'is_supplier') change_pro();
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
                    url: '<?php echo URL.'/'.$controller;?>/ajax_save',   // http://jobtraq.local.com/companies
                    type:"POST",
                    data: {field:fieldname,value:values,func:func,ids:ids},
                    success: function(text_return){
                     
                        text_return = text_return.split("||");
                            if (text_return == "email_not_valid"){
                                    $("#CompanyEmail").addClass('error_input');
                                    $.mobile.loading( 'hide' );//ajax_note('Email not valid, please check email field!');
                            }else{
                                    $("#CompanyEmail").removeClass('error_input');
                                    $.mobile.loading( 'hide' ); //ajax_note("Saving...Saved !");
                            }
                    }
                });
                //company_auto_save();
            } else { // neu field la addresses
                fieldname =  fieldname.replace("data[Company][addresses][", "");
                fieldname =  fieldname.replace("]", "");  // address_1
                loading("Saving..."); // $(".jt_ajax_note").html("Saving...       ");
                var text = $("#" + $(this).attr('id') + " option:selected").text(); //alert(values + ' ' +text);
                save_address(fieldname,values,fieldid,'',text);
            }
        });
    });
    function save_address(address_field,values,fieldid,handleData,text){ // '',values la gia tri luu,CompanyAddress1,''
        var datas = new Object();
        if(address_field!='country'  && address_field!='province_state' && address_field!='province_state_id' && address_field!='country_id'){
            datas[address_field] = values;
        } else if(address_field == 'province_state_id') { //luu province  
            datas['province_state'] = text;
            datas['province_state_id'] = values;
        } else if(address_field == 'country_id') { // luu country
            datas['country'] = text;
            datas['country_id'] = values;
        }

        var idas = "<?php echo $default_address_key; ?>";
        olds = 'update';
        save_option('addresses',datas,idas,0,'',olds,function(arr_return){
           /* if(handleData!=undefined)
                handleData(arr_return);*/
        });
        $.mobile.loading( 'hide' );//ajax_note("Saving...Saved !");
    }
    function companies_auto_save_entry(object) {
        if ($.trim($("#CompanyHeading").val()) == "") {
            $("#CompanyHeading").val("#" + $("#CompanyNo").val() + "-" + $("#CompanyCompanyName").val());
        }
        $("form :input", "#companies_form_auto_save").removeClass('error_input');
        $.ajax({
            url: '<?php echo M_URL; ?>/companies/auto_save/' + $(object).attr("name"),
            timeout: 15000,
            type: "post",
            data: $("#companies_form_auto_save","#main_page").serialize(),
            success: function(html) {
                if ($.trim(html) == "ref_no_existed") {
                    $("#CompanyNo").addClass('error_input');
                    alerts('Error', 'This "no" existed');

                } else if (html == "date_work") {
                    $("#CompanyWorkEnd").addClass('error_input');
                    $("#CompanyWorkEndHour").addClass('error_input');
                    alerts("Error: ", '"Work start date" can not greater than "Work end date"');

                } else if (html != "ok") {

                    if( $(object).hasClass("force_reload") ){// khi thay đổi giờ ...
                        $("#entry_udpate_date").html(html);
                        $(":input", "#entry_udpate_date").change(function() {
                            companies_auto_save_entry(this);
                        });
                        input_show_select_calendar(".JtSelectDate", "#entry_udpate_date");
                    }else{
                        alerts("Error: ", html);
                    }
                }
            }
        });
    }

    function after_choose_contacts(id, name, key){
        if (key == 'our_rep') {
            $("#CompanyOurRep").val(name);
            $("#CompanyOurRepId").val(id);
            var fieldname = 'our_rep';
            backToMain();
        }
        if (key == 'our_csr') {
            $("#CompanyOurCsr").val(name);
            $("#CompanyOurCsrId").val(id);
            var fieldname = 'our_csr';
            backToMain();
        }

        var values = name; 
        var valueid = id;
        var ids = $("#mongoid").val();  //  542b8a09005fc3cc220000db 
        $.ajax({
            url: '<?php echo URL.'/'.$controller;?>/save_data',   // http://jobtraq.local.com/companies
            type:"POST",
            data: {field:fieldname,value:values,ids:ids,valueid:valueid},
            success: function(text_return){
                text_return = text_return.split("||");
                //alert(text_return);
                $.mobile.loading( 'hide' ); //ajax_note("Saving...Saved !");
            }
        });

    }
    function change_pro(){
        location.reload();
    }
    function changeIDValue(){
        $("select","#main_page").each(function(){
            var id = $(this).attr("id");
            var value = $(this).find('option:selected').text();
            var hidden_value = $(this).find('option:selected').val();
            $(this).find('option:selected').val(value);
            $("#"+id+"Id","#main_page").val(hidden_value);
        });
    }
    /*function company_auto_save(){
    }*/

</script>