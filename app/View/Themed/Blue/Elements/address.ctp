    <script type="text/javascript">
        var strtemp = '?';
        var company_id = $("#company_id").val();
        var contact_id = $("#contact_id").val();
        if(company_id != undefined && company_id.length == 24)
        strtemp += 'company_id='+company_id+'&';
        if(contact_id != undefined && contact_id.length == 24)
        strtemp += 'contact_id='+contact_id;
    </script>
    <div class="tab_1_inner float_left float_left" id="address_box_invoice">
        <p class="clear">
            <span class="label_1 float_left minw_lab hgt hgt fixbor">
            <span class="link_to_invoice_address">Invoice address</span>
            </span>
        </p>
        <div class="width_in3 float_left indent_input_tp">
            <input class="input_1 float_left " type="text" value="<?php if(isset($query['invoice_address'][0]['invoice_address_2'])) echo $query['invoice_address'][0]['invoice_address_2']; ?>" name="data[invoice][invoice_address_1]" id="InvoiceAddress1" style=" padding: 0 20% 0 3%; width:78%;">
            <span class="iconw_m indent_dw_m" title="Specify address" id="click_open_window_addressesinvoice"></span>
            <script type="text/javascript">
                $(function(){
                    window_popup('addresses', 'Specify address','invoice','click_open_window_addressesinvoice',strtemp);
                });
            </script>
            <a title="View map" id="map_invoice" href="javascript:void()" onclick="addresses_run_map_invoice()">
            <span class="icosp_addr indent_sp_add"></span>
            </a>
            <script type="text/javascript">
                function addresses_run_map_invoice(){
                    var address_1 = $("#InvoiceAddress1").val();
                    var address_2 = $("#InvoiceAddress2").val();
                    var address_3 = $("#InvoiceAddress3").val();
                    var town_city = $("#InvoiceTownCity").val();
                    var province_state = $("#InvoiceProvinceState").val();
                    var zip_postcode = $("#InvoiceZipPostcode").val();
                    var country = $("#InvoiceCountry").val();
                    window.open("https://maps.google.com/maps?q=" + address_1 + " " + address_2 + " " + address_3 + " " + town_city + " " + province_state + " " + zip_postcode + " " + country,"_blank");
                }
            </script>
        </div>
        <div class="width_in3 float_left indent_input_tp">
            <input class="input_1 float_left " type="text" value="<?php if(isset($query['invoice_address'][0]['invoice_address_2'])) echo $query['invoice_address'][0]['invoice_address_2']; ?>" name="data[invoice][invoice_address_2]" id="InvoiceAddress2">
        </div>
        <div class="width_in3 float_left indent_input_tp">
            <input class="input_1 float_left " type="text" value="<?php if(isset($query['invoice_address'][0]['invoice_address_3'])) echo $query['invoice_address'][0]['invoice_address_3']; ?>" name="data[invoice][invoice_address_3]" id="InvoiceAddress3">
        </div>
        <p></p>
        <p class="clear">
            <span class="label_1 float_left minw_lab "> Town / City</span>
        </p>
        <div class="width_in3 float_left indent_input_tp">
            <input class="input_1 float_left " type="text" value="<?php if(isset($query['invoice_address'][0]['invoice_town_city'])) echo $query['invoice_address'][0]['invoice_town_city']; ?>" name="data[invoice][invoice_town_city]" id="InvoiceTownCity">
        </div>
        <p></p>
        <p class="clear">
            <span class="label_1 float_left minw_lab "> Province / State</span>
        </p>
        <div class="width_in3 float_left indent_input_tp" id="invoice_province">
            <input name="data[invoice][invoice_province_state]" combobox_blank="1" id="InvoiceProvinceState" class="input_select " type="text" value="<?php if(isset($query['invoice_address'][0]['invoice_province_state'])) echo $query['invoice_address'][0]['invoice_province_state']; ?>" readonly="" style="margin: 0px 17px 0px 0px;" />
            <input type="hidden" name="data[invoice][invoice_province_state_id]" id="InvoiceProvinceStateId" value="<?php if(isset($query['invoice_address'][0]['invoice_province_state_id'])) echo $query['invoice_address'][0]['invoice_province_state_id']; ?>" />
            <script type="text/javascript">
                $(function () {
                    $("#InvoiceProvinceState").combobox(<?php echo json_encode($arr_combobox['invoice_province_state']) ?>);
                });
            </script>
        </div>
        <p></p>
        <p class="clear">
            <span class="label_1 float_left minw_lab ">Zip / Post code</span>
        </p>
        <div class="width_in3 float_left indent_input_tp">
            <input class="input_1 float_left " type="text" value="<?php if(isset($query['invoice_address'][0]['invoice_zip_postcode'])) echo $query['invoice_address'][0]['invoice_zip_postcode']; ?>" name="data[invoice][invoice_zip_postcode]" id="InvoiceZipPostcode">
        </div>
        <p></p>
        <p class="clear">
            <span class="label_1 float_left minw_lab ">Country</span>
        </p>
        <div class="width_in3 float_left indent_input_tp" id="invoice_country">
            <input combobox_blank="1" name="data[invoice][invoice_country]" id="InvoiceCountry" class="input_select " type="text" value="<?php if(isset($query['invoice_address'][0]['invoice_country'])) echo $query['invoice_address'][0]['invoice_country']; ?>" onchange="change_pro('invoice');" readonly="readonly" style="margin: 0px 17px 0px 0px;">
            <input type="hidden" name="data[invoice][invoice_country_id]" id="InvoiceCountryId" value="<?php if(isset($query['invoice_address'][0]['invoice_country_id'])) echo $query['invoice_address'][0]['invoice_country_id']; ?>">
            <script type="text/javascript">
                $(function () {
                    $("#InvoiceCountry").combobox(<?php echo json_encode($arr_combobox['country']) ?>);
                });
            </script>
        </div>
        <p></p>
        <p class="clear">
            <span class="label_1 float_left minw_lab fixbor2 jt_ppbot">&nbsp;</span>
        </p>
        <div class="width_in3 float_left">&nbsp;</div>
        <p></p>
        <input name="invoice_address" id="invoice_address" type="hidden" value="1, 3200 14th Ave N.E.,Calgary,Alberta,Canada,">
    </div>
    <div class="tab_1_inner float_left float_left" id="address_box_shipping">
        <p class="clear">
            <span class="label_1 float_left minw_lab">
            <span class="link_to_contact_address fixbor">Ship to</span>
            </span>
        </p>
        <div class="width_in3 float_left indent_input_tp">
            <input class="input_1 float_left " type="text" value="<?php if(isset($query['shipping_address'][0]['shipping_contact_name'])) echo $query['shipping_address'][0]['shipping_contact_name']; ?>" name="data[shipping][shipping_contact_name]" id="ShippingContactName" style=" padding: 0 20% 0 3%; width:78%;">
        </div>
        <p></p>
        <p class="clear">
            <span class="label_1 float_left minw_lab hgt hgt">
            <span class="link_to_shipping_address">Shipping address</span>
            <span class="difr">(if different)</span>            </span>
        </p>
        <div class="width_in3 float_left indent_input_tp">
            <input class="input_1 float_left " type="text" value="<?php if(isset($query['shipping_address'][0]['shipping_address_1'])) echo $query['shipping_address'][0]['shipping_address_1']; ?>" name="data[shipping][shipping_address_1]" id="ShippingAddress1" style=" padding: 0 20% 0 3%; width:78%;">
            <span class="iconw_m indent_dw_m" title="Specify address" id="click_open_window_addressesshipping"></span>
            <script type="text/javascript">
                $(function(){
                    window_popup('addresses', 'Specify address','shipping','click_open_window_addressesshipping',strtemp);
                });
            </script>
            <a title="View map" id="map_shipping" href="javascript:void()" onclick="addresses_run_map_shipping()">
            <span class="icosp_addr indent_sp_add"></span>
            </a>
            <script type="text/javascript">
                function addresses_run_map_shipping(){
                    var address_1 = $("#ShippingAddress1").val();
                    var address_2 = $("#ShippingAddress2").val();
                    var address_3 = $("#ShippingAddress3").val();
                    var town_city = $("#ShippingTownCity").val();
                    var province_state = $("#ShippingProvinceState").val();
                    var zip_postcode = $("#ShippingZipPostcode").val();
                    var country = $("#ShippingCountry").val();
                    window.open("https://maps.google.com/maps?q=" + address_1 + " " + address_2 + " " + address_3 + " " + town_city + " " + province_state + " " + zip_postcode + " " + country,"_blank");
                }
            </script>
        </div>
        <div class="width_in3 float_left indent_input_tp">
            <input class="input_1 float_left " type="text" value="<?php if(isset($query['shipping_address'][0]['shipping_address_2'])) echo $query['shipping_address'][0]['shipping_address_2']; ?>" name="data[shipping][shipping_address_2]" id="ShippingAddress2">
        </div>
        <div class="width_in3 float_left indent_input_tp">
            <input class="input_1 float_left " type="text" value="<?php if(isset($query['shipping_address'][0]['shipping_address_3'])) echo $query['shipping_address'][0]['shipping_address_3']; ?>" name="data[shipping][shipping_address_3]" id="ShippingAddress3">
        </div>
        <p></p>
        <p class="clear">
            <span class="label_1 float_left minw_lab "> Town / City</span>
        </p>
        <div class="width_in3 float_left indent_input_tp">
            <input class="input_1 float_left " type="text" value="<?php if(isset($query['shipping_address'][0]['shipping_town_city'])) echo $query['shipping_address'][0]['shipping_town_city']; ?>" name="data[shipping][shipping_town_city]" id="ShippingTownCity">
        </div>
        <p></p>
        <p class="clear">
            <span class="label_1 float_left minw_lab "> Province / State</span>
        </p>
        <div class="width_in3 float_left indent_input_tp" id="shipping_province">
            <input name="data[shipping][shipping_province_state]" combobox_blank="1" id="ShippingProvinceState" class="input_select " type="text" value="<?php if(isset($query['shipping_address'][0]['shipping_province_state'])) echo $query['shipping_address'][0]['shipping_province_state']; ?>" readonly="" style="margin: 0px 17px 0px 0px;">
            <input type="hidden" name="data[shipping][shipping_province_state_id]" id="ShippingProvinceStateId" value="<?php if(isset($query['shipping_address'][0]['shipping_province_state_id'])) echo $query['shipping_address'][0]['shipping_province_state_id']; ?>">
            <script type="text/javascript">
                $(function () {
                    $("#ShippingProvinceState").combobox(<?php echo json_encode($arr_combobox['shipping_province_state']) ?>);
                });
            </script>
        </div>
        <p></p>
        <p class="clear">
            <span class="label_1 float_left minw_lab ">Zip / Post code</span>
        </p>
        <div class="width_in3 float_left indent_input_tp">
            <input class="input_1 float_left " type="text" value="<?php if(isset($query['shipping_address'][0]['shipping_zip_postcode'])) echo $query['shipping_address'][0]['shipping_zip_postcode']; ?>" name="data[shipping][shipping_zip_postcode]" id="ShippingZipPostcode">
        </div>
        <p></p>
        <p class="clear">
            <span class="label_1 float_left minw_lab   ">Country</span>
        </p>
        <div class="width_in3 float_left indent_input_tp" id="shipping_country">
            <input combobox_blank="1" name="data[shipping][shipping_country]" id="ShippingCountry" class="input_select " type="text" value="<?php if(isset($query['shipping_address'][0]['shipping_country'])) echo $query['shipping_address'][0]['shipping_country']; ?>" onchange="change_pro('shipping');" readonly="readonly" style="margin: 0px 17px 0px 0px;">
            <input type="hidden" name="data[shipping][shipping_country_id]" id="ShippingCountryId" value="<?php if(isset($query['shipping_address'][0]['shipping_country_id'])) echo $query['shipping_address'][0]['shipping_country_id']; ?>">
            <script type="text/javascript">
                $(function () {
                    $("#ShippingCountry").combobox(<?php echo json_encode($arr_combobox['country']) ?>);
                });
            </script>
        </div>
        <p></p>
        <p class="clear">
            <span class="label_1 float_left minw_lab fixbor3 jt_ppbot" style="height: 24px;">&nbsp;</span>
        </p>
        <div class="width_in3 float_left">&nbsp;</div>
        <p></p>
        <input name="shipping_address" id="shipping_address" type="hidden" value=",Canada,">
    </div>
    <script type="text/javascript">
        function change_pro(keys){
            var ids = keys.charAt(0).toUpperCase() + keys.slice(1);//Capitalize the first letter
            var country_id = $("#"+ids+"CountryId").val();
            var old_html = $("#"+keys+"_province").html();
            old_html = old_html.split("<span");
            old_html = old_html[1].split(">");
            old_html = old_html[1];
            old_html = old_html.replace('value','value="" title');
            $("#"+keys+"_province .combobox").remove();

            var readonly = "";
            if( country_id == "CA" || country_id == "US" ){
                readonly = 'readonly';
            }else{
                old_html = old_html.replace('readonly','');
            }
            $("#"+keys+"_province").prepend(old_html+" onchange=\"save_address_pr('"+keys+"');\" " + readonly + " / >");

            $.ajax({
                url: 'http://jt.com/quotations/ajax_general_province',
                dataType: "json",
                type:"POST",
                data: {country_id:country_id},
                success: function(jsondata){
                    $("#"+ids+"ProvinceState").combobox(jsondata);
                }
            });
        }
    </script>
