<?php echo $this->element('../Emailtemplates/tab_option'); ?>
<?php echo $this->Html->script('ckeditor/ckeditor'); ?>
<style type="text/css">

</style>
<div id="content">
    <div class="jt_ajax_note"></div>
    <!-- Title -->
    <div class="jbcont">
        <div class="jt_module_title float_left jt_t_left">
            <h1>
                <?php echo translate('Template: '); ?>
                <span id="email_template_header"><?php echo (isset($arr_template['name']) ? $arr_template['name'] : ''); ?></span>
            </h1>
        </div>
    </div>
    <!-- Add form -->
    <form id="form_email_template" method="post">
        <div class="clear_percent">
            <!--Elememt Panel type 01-->
            <div class="clear_percent_1 float_left">
                <div class="jt_panel" style=" width: 100%">
                    <div class="jt_box" style=" width:100%;">
                        <div class="jt_box_line">
                            <div class=" jt_box_label fixbor" style=" width:25%;">
                                No
                            </div>
                            <div class="jt_box_field " style=" width:71%">
                                <span class="input_1 float_left" style="cursor: default"><?php echo $arr_template['no']; ?></span>
                            </div>
                        </div>
                        <div class="jt_box_line">
                            <div class=" jt_box_label" style=" width:25%;">
                                Name
                            </div>
                            <div class="jt_box_field " style=" width:70%;">
                                <?php echo $this->Form->input('EmailTemplate.name', array(
										'class' => 'input_1 float_left',
										'value'	=> (isset($arr_template['name']) ? $arr_template['name'] : ''),
									)); ?>
                            </div>
                        </div>
                        <div class="jt_box_line">
                            <div class=" jt_box_label" style=" width:25%;">
                                Type
                            </div>
                            <div class="jt_box_field " style=" width:70%;">
                                <?php echo $this->Form->input('EmailTemplate.type', array(
                                        'class' => 'input_select',
                                        'value' => (isset($arr_template['type']) ? $arr_template['type'] : ''),
                                    )); ?>
                            </div>
                            <input type="hidden" id="EmailTemplateTypeId" name="data[EmailTemplate][type_id]" value="<?php echo (isset($arr_template['type_id']) ? $arr_template['type_id'] : ''); ?>" />
                            <script type="text/javascript">
                                $(function(){
                                    $("#EmailTemplateType").combobox(<?php echo json_encode($template_type); ?>);
                                })
                            </script>
                        </div>
                        <div class="jt_box_line">
                            <div class=" jt_box_label" style=" width:25%;">
                                Folder
                            </div>
                            <div class="jt_box_field " style=" width:70%;">
                                <?php echo $this->Form->input('EmailTemplate.folder', array(
                                        'class' => 'input_select',
                                        'value' => (isset($arr_template['folder']) ? $arr_template['folder'] : ''),
                                    )); ?>
                            </div>
                            <input type="hidden" id="EmailTemplateFolderId" name="data[EmailTemplate][folder_id]" value="<?php echo (isset($arr_template['folder_id']) ? $arr_template['folder_id'] : ''); ?>" />
                            <script type="text/javascript">
                                $(function(){
                                    $("#EmailTemplateFolder").combobox(<?php echo (isset($template_folder)?json_encode($template_folder): ''); ?>);
                                })
                            </script>
                        </div>
                        <div class="jt_box_line" style="-moz-user-select: none;-webkit-user-select: none;-ms-user-select: none;">
                            <div class=" jt_box_label fixbor2" style=" width:25%;height:330px">
                                Field
                            </div>
                            <div class="jt_box_field " style=" width:70%;" >
                                <span class="field_button" style="padding-top: 15px">
                                    <?php if(isset($field_button))echo $field_button;  ?>
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <!--Elememt Panel type 02-->
        </div>
        <div class="width69 mar_gin float_left" style="margin-bottom:10px;margin-top: 0%">
            <div class="full_width">
                <span class="title_block bo_ra1" style="display: block">
                    <span class="fl_dent" style="display: block">
                        <h4>Email template</h4>
                    </span>
                </span>
                <p class="clear"></p>
                <textarea class="ckeditor" id="email_template" name="data[EmailTemplate][template]">
                	<?php echo (isset($arr_template['template']) ? $arr_template['template'] : ''); ?>
                </textarea>
                <span class="title_block bo_ra2" style="display: block">
                <span class="bt_block float_right no_bg" style="display: block"></span>
                </span>
            </div>
            <!--END Tab1 -->
        </div>
    </form>
</div>

<?php echo $this->element('../Emailtemplates/js'); ?>