<?php echo $this->Form->create($model, array('id' => 'company_address_popup_form')); ?>
<div style="float: left;">
    <div class="float_left">
        <h6 class="float_left" style="margin-top:2px"><?php echo translate('Company'); ?> &nbsp;</h6>
        <span class="float_left" style=" margin-right:20px">
            <span class="block_sear  block1" style="width: 182px;">
                <span class="bg_search_1"></span>
                <span class="bg_search_2"></span>
                <div class="box_inner_search float_left" id="box_products_company_name">
                    <a href="javascript:void(0)" onclick="$('#products_submit_choice_code_option').click();">
                        <span class="icon_search"></span>
                    </a>
                    <div class="styled_select2 float_left" style="width: 145px; background: none;">
                        <?php
                        echo $this->Form->input($model.'.company', array(
                            'id' => 'window_popup_'.$controller.'_Company_Address',
                            'value'=> (isset($company_name) ? $company_name : ''),
                            'style' => 'background: #636363;color: #fff;margin-left: -2px;margin-top:-2px; width: 150px;',
                            'name'  => 'company_name'
                        ));
                        ?>
                    </div>
                </div>
            </span>
        </span>
    </div>
    </div>
    <?php
// Ẩn nút submit này
    echo $this->Js->submit('Search', array(
        'id' => $controller.'_popup_submit_button_address',
        'style' => 'height:1px; width:1px;opacity:0.1',
        'success' => '$("#window_popup_shipping_address_popup").html(data);'
    ));
    ?>

</div>
<!-- END SEARCH POPUP -->

<div style="clear:both;height:6px"></div>

<div class="block_dent2 container_same_category" style="overflow: auto;overflow-x: hidden;max-width:1000px; margin: 0 auto; height:430px;" >
    <table id="address_popup" class="jt_tb" style="font-size:12px; width: 98%">
        <thead  >
            <tr>
                <th style="width: 64px;"><?php echo translate('Name'); ?></th>
                <th ><?php echo translate('Address line 1'); ?></th>
                <th ><?php echo translate('Address line 2'); ?></th>
                <th ><?php echo translate('Address line 3'); ?></th>
                <th ><?php echo translate('Town / City'); ?></th>
                <th ><?php echo translate('Province / State'); ?></th>
                <th style="width: 80px;"><?php echo translate('Zip Postcode'); ?></th>
                <th style="width: 44px;"><?php echo translate('Country'); ?></th>
            </tr>
        </thead>
        <tbody>
            <?php
            $i = 0; $STT = 0;

            foreach($addresses as $address) {
            ?>
            <tr class="address-line" data-address="<?php echo htmlentities(json_encode($address)); ?>" class="jt_line_<?php echo $STT % 2 == 0 ? 'light' : 'black'; ?>">
                <td style="width: 64px;"><?php echo $address['name']; ?></td>
                <td ><?php echo $address['address_1']; ?></td>
                <td ><?php echo $address['address_2']; ?></td>
                <td ><?php echo $address['address_3']; ?></td>
                <td ><?php echo $address['town_city']; ?></td>
                <td ><?php echo $address['province_state']; ?></td>
                <td style="width: 80px;"><?php echo $address['zip_postcode']; ?></td>
                <td style="width: 44px;"><?php echo $address['country']; ?></td>
            </tr>
            <?php
                $STT++;
            }

            ?>
            <?php if( $STT > 0 && $STT < 5 ){ // chỉ khi nào số lượng nhỏ hơn 5 mới add thêm mà thôi
                $loop_for = 5 - $STT;
                for ($j=0; $j < $loop_for; $j++) {
                    $i = 1 - $i;
                  ?>
                <tr class="jt_line_<?php if ($i == 1) { ?>black<?php } else { ?>light<?php } ?>"><td>&nbsp;</td><td></td><td></td><td></td><td></td><td></td><td></td><td></td></tr>
            <?php
                }
            } ?>
        </tbody>
    </table>

    <?php if( $STT == 0 ){ ?>
    <center style="margin-top:30px">(No data)</center>
    <?php } ?>
</div>

<?php echo $this->Form->end(); ?>

<script type="text/javascript">
$(function(){
    $("#window_popup_companies_Company_Address").kendoAutoComplete({
        minLength: 3,
        dataTextField: "name",
        dataSource: new kendo.data.DataSource({
            transport: {
                read:{
                    dataType: "json",
                    url: "<?php echo URL.'/companies/autocomplete/'; ?>",
                    type:"POST",
                    data: {
                       data: function(){
                            return JSON.stringify({name:$("#window_popup_companies_Company_Address").val()});
                       },
                    },
                    parameterMap: function(options, operation) {
                        return {
                            StartsWith: options.filter.filters[0].value
                        }
                    }
                }
            },
            schema: {
               data: "data"
            },
            serverFiltering: true
        }),
    });
    $('table#address_popup tr.address-line').click(function() {
        var object = $(this).data('address');
        var address = new Object();
        var field = ['name','address_1','address_2','address_3','town_city','province_state','province_state_id','zip_postcode','country', 'country_id'];
        for(var i in field){
            address['shipping_'+field[i]] = object[field[i]];
        }
        address['shipping_contact_name'] = '<?php echo htmlentities(addslashes($company_name)); ?>';
        address['deleted'] = false;

        var address_0={'0':address};
        var invoice_address={'addresses':address_0};
        var jsonString = JSON.stringify(invoice_address);
        var arr_field = {'addresses': 'shipping_address'};
        $("#window_popup_shipping_address_popup").data("kendoWindow").close();
        save_muti_field(arr_field,jsonString,'',function(arr_ret){
            ajax_note('Saved.');
            address = arr_ret[ 'shipping_address'];
            address = address[0];
            for(var i in address){
                $("#"+ChangeFormatId(i)).val(address[i]);
            }
            //save tax
            var taxid = address[ 'shipping_province_state_id'];
            $('#tax').trigger('click');
            var tax = $("li[value="+taxid+"]", $('#tax').parent()).html();
            console.log(taxid);
            var taxval = tax.split("%");
            taxval = taxval[0];
            $('#tax').val(tax);
            $('#taxId').val(taxid);
            $('#tax').change();
        });
    });
});
</script>