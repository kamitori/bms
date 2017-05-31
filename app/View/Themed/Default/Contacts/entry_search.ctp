<?php echo $this->element('entry_tab_option'); ?>
<div id="content" class="fix_magr">
    <div class="clear">

        <div class="clear_percent">
            <div class="block_dent_a">
                <div class="title_1 float_left"><h1><span id="contact_name_header"></span> <span id="contact_account_header"></span></h1></div>
                <div class="title_1 right_txt float_right"><h1><span id="contact_company_header"></span></h1></div>
            </div>
        </div>

        <div id="<?php echo $controller; ?>_entry_search">
            <form>
                <div class="clear_percent">
                    <div class="clear_percent_1 float_left">
                        <div class="tab_1 block_dent_a">
                            <p class="clear">
                                <span class="label_1 float_left fixbor"><?php echo translate('Contact no'); ?></span>
                                <div class="indent_new width_in float_left">
                                    <?php echo $this->Form->input('Contact.no', array(
                                            'class' => 'input_3 float_left width_ina22',
                                            'placeholder' => 1,
                                            'style' => 'margin-top: 1.6%;width: 11% !important;'
                                    )); ?>
                                    <div class="in_active width_active_in2">
                                        <input type="hidden" name="data[Contact][is_employee]" id="ContactIsEmployee_" value="0">
                                        <span class="inactive">Employee</span>
                                        <label class="m_check2">
                                            <?php echo $this->Form->input('Contact.is_employee', array(
                                                    'type' => 'checkbox',
                                                    'class' => 'customer-employee'
                                            )); ?>
                                            <span></span>
                                        </label>
                                    </div>
                                    <div class="in_active width_active_in2">
                                    <input type="hidden" name="data[Contact][is_customer]" id="ContactIsCustomer_" value="0">
                                        <span class="inactive">Customer</span>
                                        <label class="m_check2">
                                            <?php echo $this->Form->input('Contact.is_customer', array(
                                                    'type' => 'checkbox',
                                                    'class' => 'customer-employee'
                                            )); ?>
                                            <span></span>
                                        </label>
                                    </div>
                                </div>
                            </p>
                            <p class="clear">
                                <span class="label_1 float_left"><?php echo translate('Title'); ?></span>
                                <div class="width_in float_left indent_input_tp">
                                    <?php echo $this->Form->input('Contact.title', array(
                                            'class' => 'input_select input_3',
                                            'placeholder' => 1
                                    )); ?>
                                    <?php echo $this->Form->hidden('Contact.title_id'); ?>
                                    <script type="text/javascript">
                                        $(function () {
                                            $("#ContactTitle").combobox(<?php echo json_encode($arr_contacts_title); ?>);
                                        });
                                    </script>
                                </div>
                                <p class="clear"></p>
                            </p>

                            <p class="clear">
                                <span class="label_1 float_left"><?php echo translate('First name'); ?></span>
                            </p>
                            <div class="width_in float_left indent_input_tp">
                                <?php echo $this->Form->input('Contact.first_name', array(
                                        'class' => 'input_4 float_left',
                                        'placeholder' => 1
                                        // 'readonly' => true,
                                        // 'onclick' => 'open_window_select_company()'
                                )); ?>
                            </div>

                            <p class="clear">
                                <span class="label_1 float_left"><?php echo translate('Last name'); ?></span>
                            </p>
                            <div class="width_in float_left indent_input_tp">
                                <?php echo $this->Form->input('Contact.last_name', array(
                                        'class' => 'input_4 float_left',
                                        'placeholder' => 1
                                )); ?>
                            </div>

                            <p class="clear">
                                <span class="label_1 float_left"><?php echo translate('Type'); ?></span>
                                <div class="width_in float_left indent_input_tp">
                                    <?php echo $this->Form->input('Contact.type', array(
                                            'class' => 'input_select input_4',
                                            'placeholder' => 1
                                    )); ?>
                                    <?php echo $this->Form->hidden('Contact.type_id'); ?>
                                    <script type="text/javascript">
                                        $(function () {
                                            $("#ContactType").combobox(<?php echo json_encode($arr_contacts_type); ?>);
                                        });
                                    </script>

                                </div>
                                <p class="clear"></p>
                            </p>

                            <p class="clear">
                                <span class="label_1 float_left"><?php echo translate('Direct dial'); ?></span>
                            </p>
                            <div class="indent_new width_in float_left">
                                <?php echo $this->Form->input('Contact.direct_dial', array(
                                        'class' => 'input_4 float_left',
                                        'placeholder' => 1
                                )); ?>
                                <span class="icon_phonec" title="Not yet implemented"></span>
                            </div>

                            <p class="clear">
                                <span class="label_1 float_left"><?php echo translate('Mobile'); ?></span>
                            </p>
                            <div class="width_in float_left indent_input_tp">
                                <?php echo $this->Form->input('Contact.mobile', array(
                                        'class' => 'input_4 float_left',
                                        'placeholder' => 1
                                )); ?>
                                <span class="icon_phonec" title="Not yet implemented"></span>
                            </div>

                            <p class="clear">
                                <span class="label_1 float_left fixbor2"><?php echo translate('Email'); ?></span>
                            </p>
                            <div class="width_in float_left indent_input_tp">
                                <?php echo $this->Form->input('Contact.email', array(
                                        'class' => 'input_4 float_left',
                                        'placeholder' => 1
                                )); ?>
                                <span class="icon_emaili" title="Not yet implemented"></span>
                            </div>
                            <p class="clear"></p>
                        </div><!--END Tab1 -->
                    </div>
                    <div class="clear_percent_2 float_right">
                        <div class="tab_1 float_left block_dent8">
                            <div class="tab_1_inner float_left">
                                <p class="clear">
                                    <span class="label_1 float_left minw_lab fixbor"><?php echo translate('Fax'); ?></span>
                                </p>
                                <div class="width_in3 float_left indent_input_tp">
                                    <?php echo $this->Form->input('Contact.fax', array(
                                            'class' => 'input_4 float_left ',
                                            'placeholder' => 1
                                    )); ?>
                                    <span class="icon_down_pl" title="Not yet implemented"></span>
                                </div>
                                <p class="clear">
                                    <span class="label_1 float_left minw_lab"><?php echo translate('Home phone'); ?></span>
                                </p>
                                <div class="width_in3 float_left indent_input_tp">
                                    <?php echo $this->Form->input('Contact.home_phone', array(
                                            'class' => 'input_4 float_left ',
                                            'placeholder' => 1
                                    )); ?>
                                    <span class="icon_phonec" title="Not yet implemented"></span>
                                </div>
                                <p class="clear">
                                    <span class="label_1 float_left minw_lab">
                                        <?php
                                            $link = 'javascript:void(0)';
                                            if(isset($this->data['Contact']['company_id']) && is_object($this->data['Contact']['company_id'])){
                                                $link = URL . '/companies/entry/' . $this->data['Contact']['company_id'];
                                            }
                                        ?>
                                        <span style="display:inline;" onclick="jt_link_module(this, '<?php echo msg('QUOTATION_CREATE_LINK'); ?> Companies', '<?php echo URL; ?>/companies/add')" href="<?php echo $link; ?>" class="jt_box_line_span" id="link_to_companies" ><?php echo translate('Company'); ?></span>
                                    </span>
                                </p>
                                <div class="width_in3 float_left indent_input_tp">
                                    <?php echo $this->Form->hidden('Contact.company_id'); ?>
                                    <?php echo $this->Form->input('Contact.company', array(
                                            'class' => 'input_4 float_left ',
                                            'readonly' => true,
                                            'placeholder' => 1
                                    )); ?>

                                    <?php
                                        $style = "";
                                        if( isset($this->data['Contact']['is_employee']) && $this->data['Contact']['is_employee'] ){
                                            $style = "display:none";
                                    } ?>
                                    <span class="icon_down_new float_right" id="click_open_window_companies" style="<?php echo $style; ?>"></span>
                                    <script type="text/javascript">
                                        $(function(){
                                            window_popup('companies', 'Specify company');
                                        });

                                    </script>
                                </div>
                                <p class="clear">
                                    <span class="label_1 float_left minw_lab"><?php echo translate('Company phone'); ?></span>
                                </p>
                                <div class="width_in3 float_left indent_input_tp">
                                    <?php echo $this->Form->input('Contact.company_phone', array(
                                            'class' => 'input_4 float_left ',
                                            'placeholder' => 1
                                    )); ?>
                                    <span class="icon_phonec" title="Not yet implemented"></span>
                                </div>

                                <p class="clear">
                                    <span class="label_1 float_left minw_lab"><?php echo translate('Position'); ?></span>
                                </p>
                                <div class="width_in3 float_left indent_input_tp">
                                    <?php echo $this->Form->input('Contact.position', array(
                                            'class' => 'input_4 input_select',
                                            'placeholder' => 1
                                    )); ?>
                                    <?php echo $this->Form->hidden('Contact.position_id'); ?>
                                    <script type="text/javascript">
                                        $(function () {
                                            $("#ContactPosition").combobox(<?php echo json_encode($arr_contacts_position); ?>);
                                        });
                                    </script>

                                </div>

                                <p class="clear">
                                    <span class="label_1 float_left minw_lab"><?php echo translate('Department'); ?></span>
                                </p>
                                <div class="width_in3 float_left indent_input_tp">
                                    <?php echo $this->Form->input('Contact.department', array(
                                            'class' => 'input_4 input_select',
                                            'placeholder' => 1
                                    )); ?>
                                    <?php echo $this->Form->hidden('Contact.department_id'); ?>
                                    <script type="text/javascript">
                                        $(function () {
                                            $("#ContactDepartment").combobox(<?php echo json_encode($arr_contacts_department); ?>);
                                        });
                                    </script>

                                </div>

                                <p class="clear">
                                    <span class="label_1 float_left minw_lab"><?php echo translate('Extension no'); ?></span>
                                </p>
                                <div class="width_in3 float_left indent_input_tp">
                                    <?php echo $this->Form->input('Contact.extension_no', array(
                                            'class' => 'input_4 float_left ',
                                            'placeholder' => 1
                                    )); ?>
                                    <span class="icon_phonec" title="Not yet implemented"></span>
                                </div>

                                <p class="clear">
                                    <span class="label_1 float_left fixbor2 minw_lab " style="height:25px"></span>
                                </p>
                                <div class="width_in3 float_left indent_input_tp">
                                </div>

                            </div>

                            <?php

                            echo $this->element('box_type/address', array(
                                    'address_label' => array('Default address'),
                                    'address_mode' => 'search',
                                    'address_more_line' => 0,
                                    'address_conner' => array(
                                        array('top' => 'hgt', 'bottom' => 'fixbor3 hgt2'),
                                    ),
                                    'address_controller' => array('Contact'),
                                    'address_country_id' => array(),
                                    'address_value' => array(
                                        'default' => array(
                                            '',
                                            '',
                                            '',
                                            '',
                                            '',
                                            '',
                                            '',
                                            ''
                                        )
                                    ),
                                    'address_key' => array('default'),
                                    'address_conner' => array(
                                        array(
                                            'top' => 'hgt',
                                            'bottom' => 'fixbor3 fix_bottom_address  fix_bottom_address_28'
                                        )
                                    ),
                                    'address_more_line' => 1
                            )); ?>

                            <div class="tab_1_inner float_left">

                                <?php for ($i=0; $i < 24; $i++) {
                                    $j = $i;
                                    if($j < 10)$j = '0'.$j;
                                    if($i > 7 && $i < 18){
                                        $arr_hour[$j.':00'] = array('name'=> $j.':00', 'value'=> $j.':00', 'class'=>'BgOptionHour');
                                        $arr_hour[$j.':30'] = array('name'=> $j.':30', 'value'=> $j.':30', 'class'=>'BgOptionHour');
                                    }else{
                                        $arr_hour[$j.':00'] = $j.':00';
                                        $arr_hour[$j.':30'] = $j.':30';
                                    }
                                }
                                ?>

                                <p class="clear">
                                    <span class="label_1 float_left minw_lab"><?php echo translate('Date of birth'); ?></span>
                                </p>
                                <div class="width_in3 float_left indent_input_tp">
                                    <?php echo $this->Form->input('Contact.date_of_birth', array(
                                            'class' => 'JtSelectDate input_4 float_left',
                                            'readonly' => true,
                                            'placeholder' => 1,
                                            'style' => 'width: 70px'
                                    )); ?>
                                </div>

                                <p class="clear">
                                    <span class="label_1 float_left minw_lab"><?php echo translate('Inactive'); ?></span>
                                </p>
                                <div class="width_in3 float_left indent_input_tp">
                                    <input type="hidden" name="data[Contact][inactive]" id="ContactInactive_" value="0">
                                    <div class="in_active2">
                                        <label class="m_check2">
                                            <?php echo $this->Form->input('Contact.inactive', array(
                                                    'type' => 'checkbox'
                                            )); ?>
                                            <span class="bx_check dent_chk"></span>
                                        </label>
                                        <span class="inactive dent_check"></span>

                                        <p class="clear"></p>
                                    </div>
                                </div>

                                <!-- TIME TO WORK -->
                                <p class="clear">
                                    <span class="label_1 float_left minw_lab"></span>
                                </p>
                                <div class="width_in3 float_left indent_input_tp">
                                </div>

                                <p class="clear">
                                    <span class="label_1 float_left minw_lab"></span>
                                </p>
                                <div class="width_in3 float_left indent_input_tp">


                                </div>

                                <p class="clear">
                                    <span class="label_1 float_left minw_lab"></span>
                                </p>
                                <div class="width_in3 float_left indent_input_tp">
                                </div>

                                <p class="clear">
                                    <span class="label_1 float_left fixbor3 minw_lab hgt3"><!-- Identity --></span>
                                </p>
                                <div class="width_in3 float_left indent_input_tp">
                                </div>
                                <div class="width_in3 float_left indent_input_tp">
                                </div>
                                <div class="width_in3 float_left indent_input_tp">
                                </div>

                            </div>
                        </div><!--END Tab1 -->
                    </div>
                </div>
            </form>
            <div class="clear"></div>
        </div><!--  END DIV contacts_form_auto_save -->

        <!-- DIV MENU NGANG -->

        <!-- DIV NOI DUNG CUA CAC SUBTAB -->
        <div id="contacts_sub_content" class="jt_sub_content">
        </div>

    </div>
    <div class="clear"></div>
</div>

<?php echo $this->element('../Contacts/js_search'); ?>