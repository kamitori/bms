<?php echo $this->element('entry_tab_option');?>
<div id="content" class="fix_magr">
    <div class="clear">

        <div class="clear_percent">
            <div class="block_dent_a">
                <div class="title_1 float_left"><h1><span id="communication_name_header"></span></h1></div>
                <div class="title_1 right_txt float_right"><h1><span id="communication_phoneprefix_header" style="display:none">: </span><span id="communication_phone_header"></span></h1></div>
            </div>
        </div>

        <div id="<?php echo $controller; ?>_entry_search">
            <form>
                <div class="clear_percent">
                    <div class="clear_percent_1 float_left">
                        <div class="tab_1 block_dent_a">

                            <p class="clear">
                                <span class="label_1 float_left fixbor"><?php echo translate('Ref code'); ?></span>
                            </p>
                            <div class="indent_new width_in float_left">
                                <?php echo $this->Form->input('Communication.code', array(
                                        'class' => 'input_4 float_left width_ina22',
                                        'style' => 'margin-top: 1.6%;width: 13% !important;',
                                        'placeholder' => 1
                                )); ?>
                            </div>


                            <p class="clear">
                                <span class="label_1 float_left"><?php echo translate('Type'); ?></span>
                            </p>
                            <div class="width_in float_left indent_input_tp">
                                <?php echo $this->Form->input('Communication.comms_type', array(
                                        'class' => 'input_4 input_select',
                                        'readonly' => true,
                                        'placeholder' => 1
                                )); ?>
                                <script type="text/javascript">
                                    $(function () {
                                        $("#CommunicationCommsType").combobox(<?php echo json_encode($arr_communication_type); ?>);
                                    });
                                </script>
                            </div>


                            <p class="clear">
                                <span class="label_1 float_left"><?php echo translate('Contact: Title'); ?></span>
                            </p>
                            <div class="indent_new width_in float_left">
                                <?php echo $this->Form->input('Communication.contact_title', array(
                                        'class' => 'input_4 float_left',
                                        'placeholder' => 1
                                )); ?>
                                <span class="icon_phonec"></span>
                            </div>


                            <p class="clear">
                                <span class="label_1 float_left"><?php echo translate('Contact name'); ?></span>
                            </p>
                            <div class="width_in float_left indent_input_tp">
                                <?php echo $this->Form->input('Communication.contact_name', array(
                                        'class' => 'input_4 float_left',
                                        'placeholder' => 1
                                )); ?>
                            </div>

                            <p class="clear">
                                <span class="label_1 float_left"><?php echo translate('Last name'); ?></span>
                            </p>
                            <div class="width_in float_left indent_input_tp">
                                <?php echo $this->Form->input('Communication.last_name', array(
                                        'class' => 'input_4 float_left',
                                        'placeholder' => 1
                                )); ?>
                            </div>


                            <p class="clear">
                                <span class="label_1 float_left"><?php echo translate('Email'); ?></span>
                            </p>
                            <div class="indent_new width_in float_left">
                                <?php echo $this->Form->input('Communication.email', array(
                                        'class' => 'input_4 float_left',
                                        'type' => 'email',
                                        'placeholder' => 1
                                )); ?>
                                <span class="icon_emaili"></span>
                            </div>

                            <p class="clear">
                                <span class="label_1 float_left fixbor2">
                                    <span style="display:inline;"><?php echo translate('Comms date'); ?></span>
                                </span>
                            </p>
                            <div class="indent_new width_in float_left">
                                <?php echo $this->Form->input('Communication.comms_date', array(
                                        'class' => 'input_4 float_left',
                                        'placeholder' => 1
                                )); ?>
                            </div>

                            <p class="clear"></p>
                        </div><!--END Tab1 -->
                    </div>

                    <div class="float_right" style="width: 72%;">
                        <div class="tab_1 float_left block_dent8">

                            <div class="tab_1_inner float_left">

                                <!-- Business Type -->
                                <p class="clear">
                                    <span class="label_1 float_left  minw_lab "><?php echo translate('Sign Off'); ?></span>
                                </p>
                                <div class="width_in3 float_left indent_input_tp">
                                    <?php echo $this->Form->input('Communication.sign_off', array(
                                            'class' => 'input_4 input_select',
                                            'placeholder' => 1
                                    )); ?>
                                </div>
                                <script type="text/javascript">
                                    $(function () {
                                        $("#CommunicationBusinessType").combobox(<?php echo json_encode($arr_sign_off); ?>);
                                    });
                                </script>


                                <!-- Industry -->
                                <p class="clear">
                                    <span class="label_1 float_left  minw_lab "><?php echo translate('From'); ?></span>
                                </p>
                                <div class="width_in3 float_left indent_input_tp">
                                    <?php echo $this->Form->input('Communication.contact_from', array(
                                            'class' => 'input_4 input_select',
                                            'readonly' => true,
                                            'placeholder' => 1
                                    )); ?>
                                </div>
                                <script type="text/javascript">
                                    $(function () {
                                        $("#CommunicationIndustry").combobox(<?php echo json_encode($arr_industry); ?>);
                                    });
                                </script>



                                <!-- Industry -->
                                <p class="clear">
                                    <span class="label_1 float_left  minw_lab "><?php echo translate('Position'); ?></span>
                                </p>
                                <div class="width_in3 float_left indent_input_tp">
                                    <?php echo $this->Form->input('Communication.position', array(
                                            'class' => 'input_4 input_select',
                                            'placeholder' => 1
                                    )); ?>
                                </div>
                                <script type="text/javascript">
                                    $(function () {
                                        $("#CommunicationSize").combobox(<?php echo json_encode($arr_position); ?>);
                                    });
                                </script>

                                <p class="clear">
                                    <span class="label_1 float_left  minw_lab "><?php echo translate('Salutation'); ?></span>
                                </p>
                                <div class="width_in3 float_left indent_input_tp">
                                    <?php echo $this->Form->input('Communication.salutation', array(
                                            'class' => 'input_4 input_select',
                                            'placeholder' => 1
                                    )); ?>
                                </div>


                                <p class="clear">
                                    <span class="label_1 float_left  minw_lab "><?php echo translate('Subject'); ?></span>
                                </p>
                                <div class="width_in3 float_left indent_input_tp">
                                    <?php echo $this->Form->input('Communication.name', array(
                                            'class' => 'input_4 input_select',
                                            'placeholder' => 1
                                    )); ?>
                                </div>


                                <p class="clear">
                                    <span class="label_1 float_left minw_lab fixbor3"><?php echo translate('Include Signature'); ?></span>
                                </p>
                                <div class="width_in3 float_left indent_input_tp" style="margin-top: 9px;">
                                    <input type="hidden" name="data[Communication][include_signature]" id="Communicationinclude_signature_" value="0">
                                    <label class="m_check2">
                                        <?php echo $this->Form->input('Communication.include_signature', array(
                                                'type' => 'checkbox'
                                        )); ?>
                                        <span class="bx_check dent_chk"></span>
                                    </label>
                                    <span class="include_signature dent_check"></span>
                                </div>
              

                                <p class="clear">
                                    <span class="label_1 float_left  minw_lab "><?php echo translate('Status'); ?></span>
                                </p>
                                <div class="width_in3 float_left indent_input_tp">
                                    <?php echo $this->Form->input('Communication.comms_status', array(
                                            'class' => 'input_4 input_select',
                                            'placeholder' => 1
                                    )); ?>
                                </div>


                            </div>

                            <!-- TAB 3 -->
                            <div class="tab_1_inner float_left">

                                <p class="clear">
                                    <span class="label_1 float_left minw_lab"></span>
                                </p>
                                <div class="width_in3 float_left indent_input_tp"></div>

                                <p class="clear">
                                    <span class="label_1 float_left minw_lab"></span>
                                </p>
                                <div class="width_in3 float_left indent_input_tp"></div>

                                <p class="clear">
                                    <span class="label_1 float_left minw_lab"></span>
                                </p>
                                <div class="width_in3 float_left indent_input_tp"></div>

                                <p class="clear">
                                    <span class="label_1 float_left minw_lab"></span>
                                </p>
                                <div class="width_in3 float_left indent_input_tp"></div>

                                <p class="clear">
                                    <span class="label_1 float_left minw_lab"></span>
                                </p>
                                <div class="width_in3 float_left indent_input_tp"></div>

                                <p class="clear">
                                    <span class="label_1 float_left minw_lab"></span>
                                </p>
                                <div class="width_in3 float_left indent_input_tp"></div>

                                <p class="clear">
                                    <span class="label_1 float_left minw_lab fixbor3"></span>
                                </p>
                                <div class="width_in3 float_left indent_input_tp"></div>

                            </div>
                        </div>
                    </div>
                </div>
            </form>
            <div class="clear"></div>
        </div><!--  END DIV communications_form_auto_save -->

        <!-- DIV NOI DUNG CUA CAC SUBTAB -->
        <div id="communications_sub_content" class="jt_sub_content">
        </div>

    </div>
    <div class="clear"></div>
</div>

<?php echo $this->element('../Communications/js_search'); ?>