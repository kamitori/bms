<?php echo $this->element('js'); ?>
<?php 
$default_address_key = isset($this->data['Contact']['addresses_default_key']) ? $this->data['Contact']['addresses_default_key'] : 0;
?>
<script type="text/javascript">
    $(function() {
        $(".container").delegate("input,select","change",function(){
            var fieldname = $(this).attr("name");  // data[Company][name] vs name -- data[Company][addresses][address_1]
            //var fieldid = $(this).attr("id");   // CompanyName vs name
            //fieldid = fieldname.replace("Contact", "");  // name ?

            var fieldtype = $(this).attr("type");  // text vs text
            modulename = 'mongoid';
            var ids = $("#"+modulename).val();  // undefined vs 542b8a09005fc3cc220000db --> fixed
            var values = $(this).val();  // Hv13

            if (fieldname.indexOf("addresses")==-1) {
                fieldname =  fieldname.replace("data[Contact][", "");
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
                    url: '<?php echo URL.'/'.$controller;?>/ajax_save',   // http://jobtraq.local.com/companies
                    type:"POST",
                    data: {field:fieldname,value:values,func:func,ids:ids},
                    success: function(text_return){
                     
                        text_return = text_return.split("||");
                            if (text_return == "email_not_valid"){
                                    $("#ContactEmail").addClass('error_input');
                                    $.mobile.loading( 'hide' );//ajax_note('Email not valid, please check email field!');
                            }else{
                                    $("#ContactEmail").removeClass('error_input');
                                    $.mobile.loading( 'hide' ); //ajax_note("Saving...Saved !");
                            }
                    }
                });
            } else { // neu field la addresses
                fieldname =  fieldname.replace("data[Contact][addresses][", "");
                fieldname =  fieldname.replace("]", "");  // address_1
                loading("Saving..."); // $(".jt_ajax_note").html("Saving...       ");
                var text = $("#" + $(this).attr('id') + " option:selected").text(); //alert(values + ' ' +text);
                save_address(fieldname,values,'','',text);
            }
            //contact_auto_save();
        });
    })
    function save_address(address_field,values,fieldid,handleData,text){ // '',values la gia tri luu,ContactAddress1,''
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
    }

    function after_choose_companies(company_id, company_name){
        $("#ContactCompanyId").val(company_id);
        $("#ContactCompanyName").val(company_name);

        var fieldname = "company"
        var values = company_name; 
        var valueid = company_id;
        var ids = $("#mongoid").val();  //  542b8a09005fc3cc220000db 
        $.ajax({
            url: '<?php echo URL.'/'.$controller;?>/save_data',   // http://jobtraq.local.com/companies
            type:"POST",
            data: {field:fieldname,value:values,ids:ids,valueid:valueid},
            success: function(text_return){
                //text_return = text_return.split("||");
                //alert(text_return);
                change_pro();
                $.mobile.loading( 'hide' ); //ajax_note("Saving...Saved !");
            }
        });
        backToMain();
        //contact_auto_save();
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

    function contacts_update_entry_header(id) {
        var oneDay = 24 * 60 * 60 * 1000; // hours*minutes*seconds*milliseconds
        var f_firstDate = new Date($("#work_end_"+id).val());
        var firstDate = new Date(f_firstDate.getFullYear(), f_firstDate.getMonth() + 1, f_firstDate.getDate());
        var f_secondDate = new Date();
        var secondDate = new Date(f_secondDate.getFullYear(), f_secondDate.getMonth() + 1, f_secondDate.getDate());

        // Math.abs(
        var diffDays = Math.round((firstDate.getTime() - secondDate.getTime()) / (oneDay));

        // var diffDays = Math.round(Math.abs((firstDate.getTime() - secondDate.getTime())/(oneDay)));
        if (parseInt(diffDays) < 0) {

            $("#contacts_days_left_"+id).attr("style", "color: red");

        } else {
            $("#contacts_days_left_"+id).removeAttr("style");

        }
        $("#contacts_days_left_"+id).val(diffDays);
    }



    function contacts_auto_save_entry(object) {
        if ($.trim($("#ContactHeading").val()) == "") {
            $("#ContactHeading").val("#" + $("#ContactNo").val() + "-" + $("#ContactCompanyName").val());
        }

        contacts_update_entry_header();

        $("form :input", "#contacts_form_auto_save").removeClass('error_input');

        $.ajax({
            url: '<?php echo M_URL; ?>/contacts/auto_save/' + $(object).attr("name"),
            timeout: 15000,
            type: "post",
            data: $("#contacts_form_auto_save","#main_page").serialize(),
            success: function(html) {
                if ($.trim(html) == "ref_no_existed") {
                    $("#ContactNo").addClass('error_input');
                    alerts('Error', 'This "no" existed');

                } else if (html == "date_work") {
                    $("#ContactWorkEnd").addClass('error_input');
                    $("#ContactWorkEndHour").addClass('error_input');
                    alerts("Error: ", '"Work start date" can not greater than "Work end date"');

                } else if (html != "ok") {

                    if( $(object).hasClass("force_reload") ){// khi thay đổi giờ ...
                        $("#entry_udpate_date").html(html);
                        $(":input", "#entry_udpate_date").change(function() {
                            contacts_auto_save_entry(this);
                        });
                        input_show_select_calendar(".JtSelectDate", "#entry_udpate_date");
                    }else{
                        alerts("Error: ", html);
                    }
                }
            }
        });
    }
    function change_pro(){
        location.reload();
    }

</script>