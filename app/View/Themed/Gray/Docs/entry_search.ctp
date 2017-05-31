<?php echo $this->element('entry_tab_option');?>
<div id="content" class="fix_magr">
    <div class="clear">

        <div class="clear_percent">
            <div class="block_dent_a">
                <div class="title_1 float_left"><h1><span id="doc_name_header"></span></h1></div>
                <div class="title_1 right_txt float_right"><h1><span id="doc_phoneprefix_header" style="display:none">: </span><span id="doc_phone_header"></span></h1></div>
            </div>
        </div>

        <div id="<?php echo $controller; ?>_entry_search">
            <form>
                <div class="clear_percent">
                    <div class="clear_percent_1 float_left">
                        <div class="tab_1 block_dent_a">

                            <p class="clear">
                                <span class="label_1 float_left fixbor"><?php echo translate('Ref no'); ?></span>
                            </p>
                            <div class="indent_new width_in float_left">
                                <?php echo $this->Form->input('Doc.no', array(
                                        'class' => 'input_4 float_left',
                                        'placeholder' => 1
                                )); ?>
                            </div>

                            <p class="clear">
                                <span class="label_1 float_left"><?php echo translate('Document name'); ?></span>
                            </p>
                            <div class="indent_new width_in float_left">
                                <?php echo $this->Form->input('Doc.name', array(
                                        'class' => 'input_4 float_left',
                                        'placeholder' => 1
                                )); ?>
                                <span class="icon_phonec"></span>
                            </div>

                            <p class="clear">
                                <span class="label_1 float_left"><?php echo translate('Location'); ?></span>
                            </p>
                            <div class="width_in float_left indent_input_tp">
                                <?php echo $this->Form->input('Doc.location', array(
                                        'class' => 'input_4 float_left',
                                        'placeholder' => 1
                                )); ?>
                            </div>

                            <p class="clear">
                                <span class="label_1 float_left"><?php echo translate('Description'); ?></span>
                            </p>
                            <div class="width_in float_left indent_input_tp">
                                <?php echo $this->Form->input('Doc.description', array(
                                        'class' => 'input_4 float_left',
                                        'placeholder' => 1
                                )); ?>
                            </div>

                            <p class="clear">
                                <span class="label_1 float_left"><?php echo translate('Category'); ?></span>
                            </p>
                            <div class="indent_new width_in float_left">
                                <?php echo $this->Form->input('Doc.category', array(
                                        'class' => 'input_4 input_select',
                                        'readonly' => true,
                                        'placeholder' => 1
                                )); ?>
                                <script type="text/javascript">
                                $(function(){
                                    $("#DocCategory").combobox(<?php echo json_encode($arr_docs_category) ?>);
                                })
                                </script>
                            </div>


                            <p class="clear">
                                <span class="label_1 float_left"><?php echo translate('Type'); ?></span>
                            </p>
                            <div class="width_in float_left indent_input_tp">
                                <?php echo $this->Form->input('Doc.type', array(
                                        'class' => 'input_4 float_left',
                                        'placeholder' => 1
                                )); ?>
                            </div>

                            <p class="clear">
                                <span class="label_1 float_left"><?php echo translate('Extension'); ?></span>
                            </p>
                            <div class="indent_new width_in float_left">
                                <?php echo $this->Form->input('Doc.ext', array(
                                        'class' => 'input_4 float_left',
                                        'placeholder' => 1
                                )); ?>
                            </div>

                            <p class="clear">
                                <span class="label_1 float_left fixbor2"><?php echo translate('Created by module'); ?></span>
                            </p>
                            <div class="indent_new width_in float_left">
                                <?php echo $this->Form->input('Doc.create_by_module', array(
                                        'class' => 'input_4 float_left',
                                        'placeholder' => 1
                                )); ?>
                            </div>

                            <p class="clear"></p>
                        </div><!--END Tab1 -->
                    </div>

                    <div class="float_right" style="width: 72%;">
                        <div class="tab_1 float_left block_dent8">

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
        </div><!--  END DIV docs_form_auto_save -->

        <!-- DIV NOI DUNG CUA CAC SUBTAB -->
        <div id="docs_sub_content" class="jt_sub_content">
        </div>

    </div>
    <div class="clear"></div>
</div>

<?php echo $this->element('../Docs/js_search'); ?>