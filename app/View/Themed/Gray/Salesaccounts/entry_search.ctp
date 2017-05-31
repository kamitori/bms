<?php echo $this->element('entry_tab_option'); ?>

<div id="content" class="fix_magr">
    <div class="clear">

        <div class="clear_percent">
            <div class="block_dent_a">
                <div class="title_1 float_left">
                    <h1 id="h1_salesaccount_company_header">
                        <span id="salesaccount_company_header"></span>
                    </h1>
                </div>
                <div class="title_1 right_txt float_right">
                    <h1>
                    </h1>
                </div>
            </div>
        </div>

        <div id="<?php echo $controller; ?>_entry_search">
            <div class="clear_percent">
                <div class="clear_percent_1 float_left">
                    <div class="tab_1 block_dent_a">
                        <p class="clear">
                            <span class="label_1 float_left fixbor"><?php echo translate('Account no'); ?></span>
                            </p>
                            <div class="width_in float_left indent_input_tp">
                                <div class="three_colum top_se">
                                    <?php echo $this->Form->input('Salesaccount.no', array(
                                        'class' => 'input_4 float_left',
                                        'placeholder' => 1
                                    )); ?>
                                </div>
                                <div class="three_colum top_se">
                                    <span class="three_text center_txt b_none">Type</span>
                                </div>
                                <div class="two_colum no_border">
                                    <span style="position:relative; display:-moz-inline-box; display:inline-block;" class="combobox">
                                        <input type="text" value="Company" class="input_select input_se" style="margin: 0px 16px 0px 0px;" readonly="true">
                                    </span>
                                </div>
                            </div>

                        <p class="clear">
                            <span class="label_1 float_left">
                                <?php echo translate('Account name'); ?>
                            </span>
                        </p>
                        <div class="width_in float_left indent_input_tp">
                            <?php echo $this->Form->input('Salesaccount.name', array(
                                    'class' => 'input_4 float_left ',
                                    'placeholder' => 1
                            )); ?>
                            <?php echo $this->Form->hidden('Salesaccount._id'); ?>
                            <span class="iconw_m indent_dw_m" id="click_open_window_companies"></span>
                            <script type="text/javascript">
                                $(function(){
                                    window_popup('companies', 'Specify company');
                                });
                            </script>
                        </div>

                        <p class="clear">
                            <span class="label_1 float_left"><?php echo translate('Account status'); ?></span>
                            </p><div class="width_in float_left indent_input_tp">
                                <?php echo $this->Form->input('Salesaccount.status', array(
                                        'class' => 'input_select jt_input_search',
                                        'placeholder' => 1,
                                        'readonly' => true
                                )); ?>
                                <?php echo $this->Form->hidden('Salesaccount.status_id'); ?>
                                <script type="text/javascript">
                                    $(function () {
                                        $("#SalesaccountStatus").combobox(<?php echo json_encode($arr_status); ?>);
                                    });
                                </script>
                            </div>

                        <p class="clear">
                            <span class="label_1 float_left"><?php echo translate('Phone'); ?></span>
                            </p><div class="indent_new width_in float_left">
                                <?php echo $this->Form->input('Salesaccount.phone', array(
                                        'class' => 'input_4 float_left',
                                        'placeholder' => 1
                                )); ?>
                                <span class="icon_phonec"></span>
                            </div>

                        <p class="clear">
                            <span class="label_1 float_left"><?php echo translate('Fax'); ?></span>
                            </p><div class="indent_new width_in float_left">
                                <?php echo $this->Form->input('Salesaccount.fax', array(
                                        'class' => 'input_4 float_left',
                                        'placeholder' => 1
                                )); ?>
                                <span class="icon_down_pl"></span>
                            </div>

                        <p class="clear">
                            <span class="label_1 float_left "><?php echo translate('Email'); ?></span>
                            </p><div class="indent_new width_in float_left">
                                <?php echo $this->Form->input('Salesaccount.email', array(
                                        'class' => 'input_4 float_left',
                                        'placeholder' => 1
                                )); ?>
                                <span class="icon_emaili"></span>
                            </div>

                        <p class="clear">
                            <span class="label_1 float_left ">
                                <?php echo translate('Account contact'); ?>
                            </span>
                        </p>
                        <div class="width_in float_left indent_input_tp">
                            <?php echo $this->Form->hidden('Salesaccount.contact_id'); ?>
                            <?php echo $this->Form->input('Salesaccount.contact_name', array(
                                    'class' => 'input_4 float_left ',
                                    'placeholder' => 1,
                                    'readonly' => true
                            )); ?>
                            <span class="icon_down_new float_right" id="click_open_window_contacts"></span>
                            <script type="text/javascript">
                                $(function(){
                                    // kiểm tra xem đã chọn company chưa
                                    salesaccounts_init_popup_contacts();
                                });

                                function salesaccounts_init_popup_contacts( force_re_install ){

                                    var parameter_get = "";
                                    if( $("#SalesaccountName").val() != "" )
                                        parameter_get = "?company_id="+$("#SalesaccountId").val()+"&company_name="+$("#SalesaccountName").val();

                                    if( force_re_install == "force_re_install" )
                                        window_popup("contacts", "Specify contact", "", "", parameter_get, "force_re_install");
                                    else
                                        window_popup("contacts", "Specify contact", "", "", parameter_get);
                                }

                                function after_choose_contacts(contact_id, contact_name){
                                    $("#link_to_contacts").attr("href", "<?php echo URL; ?>/contacts/entry/" + contact_id);
                                    $("#SalesaccountContactId").val(contact_id);
                                    $("#SalesaccountContactName").val(contact_name);
                                    $("#window_popup_contacts").data("kendoWindow").close();

                                    return false;
                                }
                            </script>
                        </div>

                            <p class="clear">
                                <span class="label_1 float_left fixbor2"><?php echo translate('Direct detail'); ?></span>
                                </p><div class="width_in float_left indent_input_tp">
                                    <?php echo $this->Form->input('Salesaccount.direct_dial', array(
                                            'class' => 'input_4 float_left',
                                            'placeholder' => 1
                                    )); ?>
                                    <!-- <span class="icon_down_new float_right"></span> -->
                                </div>

                            <p class="clear"></p>

                        <p class="clear"></p>
                    </div><!--END Tab1 -->
                </div>
                <div class="clear_percent_2 float_right">
                    <div class="tab_1 float_left block_dent8">

                        <?php

                                echo $this->element('box_type/address', array(
                                    'address_mode' => 'search',
                                    'address_label' => array('Default address'),
                                    'address_more_line' => 1,
                                    'address_controller' => array('Salesaccount'),
                                    'address_country_id' => array(), //array($this->data['Salesaccount']['addresses'][$key]['country_id']),
                                    'address_value' => array(
                                        'default' => array(
                                            '', //$this->data['Salesaccount']['addresses'][$key]['address_1'],
                                            '', //'', //$this->data['Salesaccount']['addresses'][$key]['address_2'],
                                            '', //$this->data['Salesaccount']['addresses'][$key]['address_3'],
                                            '', //$this->data['Salesaccount']['addresses'][$key]['town_city'],
                                            '', //$this->data['Salesaccount']['addresses'][$key]['country_id'],
                                            '', //
                                            '', //$this->data['Salesaccount']['addresses'][$key]['province_state_id'],
                                            ''  //$this->data['Salesaccount']['addresses'][$key]['zip_postcode']
                                        )
                                    ),
                                    'address_key' => array('default'),
                                    'address_conner' => array(
                                        array(
                                            'top' => 'hgt fixbor',
                                            'bottom' => 'fixbor3 fix_bottom_address fix_bot_bor'
                                        )
                                    )
                            )); ?>



                        <div class="tab_1_inner float_left" id="address_box_shipping">
                            <p class="clear">
                                <span class="label_1 float_left minw_lab">
                                    <span class="link_to_contact_address">Invoices / credits</span>
                                </span>
                                </p>
                                <div class="width_in3 float_left indent_input_tp">
                                    <?php echo $this->Form->input('Salesaccount.invoices_credits', array(
                                            'class' => 'input_4 float_left',
                                            'placeholder' => 1,
                                    )); ?>
                                </div>

                            <p class="clear">
                                <span class="label_1 float_left minw_lab">
                                    <span class="link_to_contact_address">Receipts</span>
                                </span>
                                </p>
                                <div class="width_in3 float_left indent_input_tp">
                                    <?php echo $this->Form->input('Salesaccount.receipts', array(
                                            'class' => 'input_4 float_left',
                                            'placeholder' => 1,
                                    )); ?>
                                </div>

                            <p class="clear">
                                <span class="label_1 float_left minw_lab">
                                    <span class="link_to_contact_address">Account balance</span>
                                </span>
                                </p>
                                <div class="width_in3 float_left indent_input_tp">
                                    <?php echo $this->Form->input('Salesaccount.balance', array(
                                            'class' => 'input_4 float_left',
                                            'placeholder' => 1,
                                    )); ?>
                                </div>

                            <p class="clear">
                                <span class="label_1 float_left minw_lab ">Credit limit</span>
                                </p>
                                <div class="width_in3 float_left indent_input_tp">
                                    <?php echo $this->Form->input('Salesaccount.credit_limit', array(
                                        'class' => 'input_4 float_left',
                                        'placeholder' => 1,
                                    )); ?>
                                </div>

                            <p class="clear">
                                <span class="label_1 float_left minw_lab ">Difference</span>
                                </p>
                                <div class="width_in3 float_left indent_input_tp">
                                    <?php echo $this->Form->input('Salesaccount.difference', array(
                                        'class' => 'input_4 float_left',
                                        'placeholder' => 1,
                                    )); ?>
                                </div>

                            <p class="clear">
                                <span class="label_1 float_left minw_lab ">Payment terms</span>
                                </p>
                                <div class="width_in3 float_left indent_input_tp">
                                    <?php echo $this->Form->input('Salesaccount.payment_terms', array(
                                        'class' => 'input_select jt_input_search',
                                        'placeholder' => 1,
                                        'readonly' => true
                                    )); ?>
                                    <?php echo $this->Form->hidden('Salesaccount.payment_terms_id'); ?>
                                    <script type="text/javascript">
                                        $(function () {
                                            $("#SalesaccountPaymentTerms").combobox(<?php echo json_encode($arr_payment_terms); ?>);
                                        });
                                    </script>
                                </div>

                            <p class="clear">
                                <span class="label_1 float_left minw_lab ">Tax code</span>
                                </p>
                                <div class="width_in3 float_left indent_input_tp">
                                    <?php echo $this->Form->input('Salesaccount.tax_code', array(
                                        'class' => 'input_select jt_input_search',
                                        'placeholder' => 1,
                                        'readonly' => true
                                    )); ?>
                                    <?php echo $this->Form->hidden('Salesaccount.tax_code_id'); ?>
                                    <script type="text/javascript">
                                        $(function () {
                                            $("#SalesaccountTaxCode").combobox(<?php echo json_encode($arr_tax_code); ?>);
                                        });
                                    </script>
                                </div>



                            <p class="clear">
                                <span class="label_1 float_left minw_lab fixbor3">Nominal code</span>
                                </p>
                                <div class="width_in3 float_left indent_input_tp">
                                    <?php echo $this->Form->input('Salesaccount.nominal_code', array(
                                        'class' => 'input_select jt_input_search',
                                        'placeholder' => 1,
                                        'readonly' => true
                                    )); ?>
                                    <?php echo $this->Form->hidden('Salesaccount.nominal_code_id'); ?>
                                    <script type="text/javascript">
                                        $(function () {
                                            $("#SalesaccountNominalCode").combobox(<?php echo json_encode($arr_nominal_code); ?>);
                                        });
                                    </script>
                                </div>

                        </div>

                        <div class="tab_1_inner float_left" id="address_box_shipping">
                            <p class="clear">
                                <span class="label_1 float_left minw_lab">
                                    <span class="link_to_contact_address">Usually Pay By</span>
                                </span>
                                </p>
                                <div class="width_in3 float_left indent_input_tp">
                                    <?php echo $this->Form->input('Salesaccount.usually_pay_by', array(
                                        'class' => 'input_select jt_input_search',
                                        'placeholder' => 1,
                                        'readonly' => true
                                    )); ?>
                                    <?php echo $this->Form->hidden('Salesaccount.usually_pay_by_id'); ?>
                                    <script type="text/javascript">
                                        $(function () {
                                            $("#SalesaccountUsuallyPayBy").combobox(<?php echo json_encode($arr_usually_pay_by); ?>);
                                        });
                                    </script>
                                </div>

                            <p class="clear">
                                <span class="label_1 float_left minw_lab">
                                    <span class="link_to_contact_address">Card type</span>
                                </span>
                                </p>
                                <div class="width_in3 float_left indent_input_tp">
                                    <?php echo $this->Form->input('Salesaccount.card_type', array(
                                        'class' => 'input_select jt_input_search',
                                        'placeholder' => 1,
                                        'readonly' => true
                                    )); ?>
                                    <?php echo $this->Form->hidden('Salesaccount.card_type_id'); ?>
                                    <script type="text/javascript">
                                        $(function () {
                                            $("#SalesaccountCardType").combobox(<?php echo json_encode($arr_card_type); ?>);
                                        });
                                    </script>
                                </div>

                            <p class="clear">
                                <span class="label_1 float_left minw_lab">
                                    <span class="link_to_contact_address">Card number</span>
                                </span>
                                </p>
                                <div class="width_in3 float_left indent_input_tp">
                                    <?php echo $this->Form->input('Salesaccount.card_number', array(
                                            'class' => 'input_4 float_left',
                                            'placeholder' => 1,
                                    )); ?>
                                </div>

                            <p class="clear">
                                <span class="label_1 float_left minw_lab ">Expires: Month</span>
                                </p>
                                <div class="width_in3 float_left indent_input_tp">
                                    <?php echo $this->Form->input('Salesaccount.expires_month', array(
                                        'class' => 'input_4 float_left',
                                        'placeholder' => 1,
                                    )); ?>
                                </div>

                            <p class="clear">
                                <span class="label_1 float_left minw_lab ">Security ID</span>
                                </p>
                                <div class="width_in3 float_left indent_input_tp">
                                    <?php echo $this->Form->input('Salesaccount.security_id', array(
                                        'class' => 'input_4 float_left',
                                        'placeholder' => 1,
                                        'type' => 'text'
                                    )); ?>
                                </div>

                            <p class="clear">
                                <span class="label_1 float_left minw_lab ">Card holder</span>
                                </p>
                                <div class="width_in3 float_left indent_input_tp">
                                    <?php echo $this->Form->input('Salesaccount.card_holder', array(
                                        'class' => 'input_select jt_input_search',
                                        'placeholder' => 1,
                                        'readonly' => true
                                    )); ?>
                                    <?php echo $this->Form->hidden('Salesaccount.card_holder_id'); ?>
                                    <script type="text/javascript">
                                        $(function () {
                                            $("#SalesaccountCardHolder").combobox(<?php echo json_encode($arr_card_holder); ?>);
                                        });
                                    </script>
                                </div>

                            <p class="clear">
                                <span class="label_1 float_left minw_lab ">Address</span>
                                </p>
                                <div class="width_in3 float_left indent_input_tp">
                                    <?php echo $this->Form->input('Salesaccount.address', array(
                                    'class' => 'input_4 float_left',
                                    'placeholder' => 1,
                                )); ?>
                                </div>

                            <p class="clear">
                                <span class="label_1 float_left minw_lab fixbor3">Ext accounts ID</span>
                                </p>
                                <div class="width_in3 float_left indent_input_tp">
                                    <?php echo $this->Form->input('Salesaccount.ext_accounts_id', array(
                                        'class' => 'input_4 float_left',
                                        'placeholder' => 1,
                                        'type' => 'text'
                                    )); ?>
                                </div>
                        </div>
                    </div><!--END Tab1 -->
                </div>
            </div>
            <div class="clear"></div>
        </div><!--  END DIV salesaccounts_form_auto_save -->

    </div>
    <div class="clear"></div>
</div>
<?php echo $this->element('../Salesaccounts/js_search'); ?>