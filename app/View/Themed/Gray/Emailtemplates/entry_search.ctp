<?php echo $this->element('../emailtemplates/tab_option'); ?>
<div id="content" class="fix_magr">
    <div class="clear">
        <div class="jbcont">
            <div class="jt_module_title float_left jt_t_left">
            </div>
        </div>
        <form id="<?php echo $controller; ?>_entry_search" method="post">
            <div class="clear_percent">
                <!--Elememt Panel type 01-->
                <div class="clear_percent_1 float_left">
                    <div class="jt_panel" style=" width: 100%">
                        <div class="jt_box" style=" width:100%;">
                            <div class="jt_box_line">
                                <div class=" jt_box_label fixbor" style=" width:25%;">
                                    Template no
                                </div>
                                <div class="jt_box_field " style=" width:71%">
                                    <?php echo $this->Form->input('EmailTemplate.no', array(
                                            'class' => 'input_4 float_left',
                                            'placeholder' => 1
                                        )); ?>
                                </div>
                            </div>
                            <div class="jt_box_line">
                                <div class=" jt_box_label fixbor2" style=" width:25%;height:380px">
                                    Template name
                                </div>
                                <div class="jt_box_field " style=" width:70%;">
                                    <?php echo $this->Form->input('EmailTemplate.name', array(
                                            'class' => 'input_4 float_left',
                                            'placeholder' => 1
                                        )); ?>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <!--Elememt Panel type 02-->
            </div>
        </form>
    </div>
    <div class="clear"></div>
</div>

<?php echo $this->element('../Emailtemplates/js_search'); ?>