<div class="clear_percent_3a float_left">
    <form method="POST" id="profile_form">
        <div class="tab_1 full_width" id="block_full_otherpricing">
            <!-- Header-->
            <span class="title_block bo_ra1">
                <span class="fl_dent">
                    <h4>User preferences</h4>
                </span>
            </span>
            <!--CONTENTS-->
            <div class="jt_subtab_box_cont" style=" height:209px;">
                <div class="tab_2_inner">
                    <p class="clear">
                        <span class="label_1 float_left minw_lab2" style="width: 55%;">Default language for printing external docs</span>
                    </p>
                    <div class="width_in3 float_left indent_input_tp" id="shipping_province" style="width:40.5%">
                        <input name="default_language_for_printing" value="<?php if(isset($arr_return['default_language_for_printing'])) echo $arr_return['default_language_for_printing'];?>" class="input_select" readonly="readonly" type="text" id="default_language_for_printing">
                        <script type="text/javascript">
                            $(function () {
                                $("#default_language_for_printing").combobox(<?php echo json_encode($arr_language_printing_external_docs); ?>);
                            });
                        </script>
                    </div>
                    <p></p>
                    <p class="clear">
                        <span onclick="clearOnlogin();" class="label_1 float_left minw_lab2" id="on_login_set" style="cursor:pointer;width: 55%;text-decoration: underline;">On login set QuickView to</span>
                    </p>
                    <div class="width_in3 float_left indent_input_tp" id="shipping_province" style="width:40.5%">
                        <input name="on_login_set_quick_view" value="<?php if(isset($arr_return['on_login_set_quick_view'])) echo $arr_return['on_login_set_quick_view'];?>" id="on_login_set_quick_view" class="input_select" readonly="readonly" type="text">
                        <script type="text/javascript">
                            $(function () {
                                $("#on_login_set_quick_view").combobox(<?php echo json_encode($arr_on_login_set_quick_view); ?>);
                            });
                        </script>
                    </div>
                    <p></p>
                    <p class="clear">
                        <span class="label_1 float_left minw_lab2" style="width: 55%;">On login set window sizes to</span>
                    </p>
                    <div class="width_in3 float_left indent_input_tp" id="shipping_province" style="width:40.5%">
                        <input name="on_login_set_window_sizes" value="<?php if(isset($arr_return['on_login_set_window_sizes'])) echo $arr_return['on_login_set_window_sizes'];?>"  id="on_login_set_window_sizes" class="input_select" readonly="readonly" type="text">
                        <script type="text/javascript">
                            $(function () {
                                $("#on_login_set_window_sizes").combobox(<?php echo json_encode($arr_on_login_set_window_sizes); ?>);
                            });
                        </script>
                    </div>
                    <p></p>
                    <p class="clear">
                        <span class="label_1 float_left minw_lab2" style="width: 55%;">Display tool tips over non text buttons</span>
                    </p>
                    <div class="width_in3 float_left indent_input_tp" style=" width: 40.5%;border-bottom: 1px solid #DDDDDD;padding: 0%;margin: 0 1%;margin-top:6px;">
                        &nbsp;
                        <input type="radio" name="display_tool_tips" value="1" id="display_yes" <?php if(isset($arr_return['display_tool_tips'])&&$arr_return['display_tool_tips']==1) {?> checked <?php } ?>><label for="display_yes">  Yes</label>
                        &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                        <input type="radio" name="display_tool_tips" value="0" id="display_no" <?php if(isset($arr_return['display_tool_tips'])&&$arr_return['display_tool_tips']==0) {?> checked <?php } ?>><label for="display_no">  No</label>
                    </div>
                    <p></p>
                    <p class="clear">
                        <span class="label_1 float_left minw_lab2" style="width: 55%;">Extension ( used by phone system module )</span>
                    </p>
                    <div class="width_in3 float_left indent_input_tp" style=" width: 40.5%; ">
                        <input class="input_1 float_left" id="extension" name="extension" type="text" value="<?php if(isset($arr_return['extension'])) echo $arr_return['extension'];?>">
                    </div>
                    <p></p>
                    <p class="clear"></p>
                    <p></p>
                </div>
                <div class="tab_2_inner">
                </div>
                <!--END Tab2  inner-->
                <div class="tab_2_inner">
                    <span class="title_block">
                        <span class="fl_dent">
                            <h4>Alerts / reminders related (requires re-login)</h4>
                        </span>
                    </span>
                    <p class="clear">
                        <span class="label_1 float_left minw_lab2" style="width: 55%;">New messages
                        <span style="
                            background: #AAAAAA;
                            position: absolute;
                            margin-top: -1.2%;
                            width: 1%;
                            margin-left: 9.3%;
                            height: 1px;
                            ">            </span>
                        </span>
                    </p>
                    <div class="width_in3 float_left indent_input_tp" id="shipping_province" style="width:40.5%">
                        <div class="in_active2">
                            <label class="m_check2">
                            <input type="checkbox" id="new_messages" name="new_messages" value="<?php if(isset($arr_return['new_messages']))echo $arr_return['new_messages'];?>"  <?php if(isset($arr_return['new_messages'])&&$arr_return['new_messages']==1){?> checked <?php }?>>
                            <span class="bx_check dent_chk"></span>
                            </label>
                            <span class="inactive dent_check color_hidden">(internal and SMS)</span>
                            <p class="clear"></p>
                        </div>
                    </div>
                    <p></p>
                    <p class="clear">
                        <span class="label_1 float_left minw_lab2" style="width: 55%;">Tasks due
                        <span style="
                            background: #AAAAAA;
                            position: absolute;
                            margin-top: -1.2%;
                            width: 3%;
                            margin-left: 9.3%;
                            height: 1px;
                            ">            </span>
                        </span>
                        <span style="
                            background: #AAAAAA;
                            position: absolute;
                            margin-top: -0.62%;
                            width: 1px;
                            margin-left: 9.3%;
                            height: 3.8%;
                            ">            </span>
                        <span style="
                            background: #AAAAAA;
                            position: absolute;
                            margin-top: 0.2%;
                            width: 2%;
                            margin-left: 7.3%;
                            height: 1px;
                            ">            </span>
                        <span style="
                            color: #666666;
                            position: absolute;
                            margin-top: -0.2%;
                            width: 5%;
                            margin-left: 3.3%;
                            height: 1px;
                            ">   Check for         </span>
                        </span>
                        </span>
                    </p>
                    <div class="width_in3 float_left indent_input_tp" id="shipping_province" style="width:40.5%">
                        <div class="in_active2">
                            <label class="m_check2">
                            <input type="checkbox" id="tasks_due" name="tasks_due" value="<?php if(isset($arr_return['tasks_due']))echo $arr_return['tasks_due'];?>"  <?php if(isset($arr_return['tasks_due'])&&$arr_return['tasks_due']==1){?> checked <?php }?>>
                            <span class="bx_check dent_chk"></span>
                            </label>
                            <span class="inactive dent_check color_hidden"></span>
                            <p class="clear"></p>
                        </div>
                    </div>
                    <p></p>
                    <p class="clear">
                        <span class="label_1 float_left minw_lab2" style="width: 55%;">Check interval (seconds)</span>
                    </p>
                    <div class="width_in3 float_left indent_input_tp" style=" width: 40.5%; ">
                        <input class="input_1 float_left" id="check_interval" name="check_interval" type="text" value="<?php if(isset($arr_return['check_interval'])) echo $arr_return['check_interval'];?>">
                    </div>
                    <p></p>
                    <p class="clear">
                        <span class="label_1 float_left minw_lab2" style="width: 55%;">Display alert dialog </span>
                    </p>
                    <div class="width_in3 float_left indent_input_tp" id="shipping_province" style="width:40.5%">
                        <div class="in_active2">
                            <label class="m_check2">
                            <input type="checkbox" id="display_alert_dialog" name="display_alert_dialog" value="<?php if(isset($arr_return['display_alert_dialog']))echo $arr_return['display_alert_dialog'];?>"  <?php if(isset($arr_return['display_alert_dialog'])&&$arr_return['display_alert_dialog']==1){?> checked <?php }?>>
                            <span class="bx_check dent_chk"></span>
                            </label>
                            <span class="inactive dent_check color_hidden"></span>
                            <p class="clear"></p>
                        </div>
                    </div>
                    <p></p>
                    <p class="clear">
                        <span class="label_1 float_left minw_lab2" style="width: 55%;">Bring 'Alerts' window to front </span>
                    </p>
                    <div class="width_in3 float_left indent_input_tp" id="shipping_province" style="width:40.5%">
                        <div class="in_active2">
                            <label class="m_check2">
                            <input type="checkbox" id="bring_alerts" name="bring_alerts" value="<?php if(isset($arr_return['bring_alerts']))echo $arr_return['bring_alerts'];?>"  <?php if(isset($arr_return['bring_alerts'])&&$arr_return['bring_alerts']==1){?> checked <?php }?>>
                            <span class="bx_check dent_chk"></span>
                            </label>
                            <span class="inactive dent_check color_hidden"></span>
                            <p class="clear"></p>
                        </div>
                    </div>
                    <p></p>
                    <p class="clear">
                        <span class="label_1 float_left minw_lab2" style="width: 55%;height:20px;"></span>
                    </p>
                    <div class="width_in3 float_left indent_input_tp" id="shipping_province" style="width:40.5%">
                    </div>
                    <p></p>
                </div>
                <!--END Tab2  inner-->
            </div>
            <!--<span class="hit"></span>-->
            <!--Footer-->
            <span class="title_block bo_ra2">
            </span>
        </div>
        <!--END Tab1 -->
    </form>
</div>
<div class="clear_percent_4a float_left">
    <div class="tab_1 full_width">
        <span class="title_block bo_ra1">
            <span class="float_left">
                <span class="fl_dent">
                    <h4><?php echo translate('System login details'); ?></h4>
                </span>
            </span>
        </span>
        <!-- baonam -->
        <?php echo $this->Form->create('Contact', array('id' => 'ContactRefsForm')); ?>
        <?php echo $this->Form->hidden('Contact._id'); ?>
        <div class="tab_2_inner">
            <p class="clear">
                <span class="label_1 float_left minw_lab2"><?php echo translate('Username'); ?></span>
            </p>
            <div class="width_in3a float_left indent_input_tp">
                <?php echo $this->Form->input('Contact.user_name_contact', array(
                    'class' => 'input_1 float_left',
                    'value' => $this->data['Contact']['user_name_contact'],
                    'onchange' => 'change_password(this)'
                    )); ?>
            </div>
            <p class="clear">
                <span class="label_1 float_left minw_lab2"><?php echo translate('Password'); ?></span>
            </p>
            <div class="width_in3a float_left indent_input_tp">
                <?php echo $this->Form->input('Contact.password_contact', array(
                    'class' => 'input_1 float_left',
                    'type' => 'password',
                    'onchange' => 'change_password(this)'
                    )); ?>
            </div>
            <p class="clear">
                <span class="label_1 float_left minw_lab2 fixbor3"><?php echo translate('Set user as default as login'); ?></span>
            </p>
            <div class="width_in3a float_left indent_input_tp">
                <div class="in_active2">
                    <label class="m_check2">
                    <input type="checkbox">
                    <span class="bx_check dent_chk"></span>
                    </label>
                    <span class="inactive dent_check color_hidden">(<?php echo translate('if multi user will default all users'); ?>)</span>
                    <p class="clear"></p>
                </div>
            </div>
            <p class="clear"></p>
            <div>
                <div class="title_block">
                    <span class="fl_dent">
                        <h4>Language & Theme</h4>
                    </span>
                </div>
                <div class="tab_2_inner">
                    <p class="clear">
                        <span class="label_1 float_left minw_lab2" style=""><?php echo translate('Choice language') ?></span>
                    </p>
                    <div class="width_in3 float_left indent_input_tp" id="shipping_province" style="width:60.5%">
                        <?php if(!empty($arr_language)){?>
                        <input name="language" value="<?php if(isset($arr_return['language']) && isset($arr_language[$arr_return['language']])) echo $arr_language[$arr_return['language']];?>"  id="language_id" class="input_select" readonly="readonly" type="text">
                        <input name "languageid" type="hidden" value="<?php if(isset($arr_language['value'])) echo $arr_language['value'];?>" id="language_idId"/>
                        <script type="text/javascript">
                            $(function(){
                              $("#language_id").combobox(<?php echo json_encode($arr_language); ?>);
                            });
                        </script>
                        <?php }?>
                    </div>
                    <style type="text/css">
                        .contain_role{
                        border-bottom: 1px solid #DDD;
                        height: 10px;
                        padding: 6px 6px 2px;
                        }
                        .contain_role a{
                        color: blue;
                        }
                    </style>
                    <p class="clear">
                        <span class="label_1 float_left minw_lab2 fixbor3" style="height: 145px"><?php echo translate('Theme') ?></span>
                    </p>
                    <div class="width_in3 float_left indent_input_tp"  style="width:60.5%">
                        <input name="theme" value="<?php if(isset($arr_return['theme']) ) echo $arr_return['theme']; else echo 'Default';?>"  id="theme" class="input_select" readonly="readonly" type="text">
                        <input type="hidden" id="themeId"/>
                        <script type="text/javascript">
                            $(function(){
                              $("#theme").combobox({"default":"Default","blue":"Blue","green":"Green","gray":"Gray"});
                            });
                        </script>
                    </div>
                    <p class="clear"></p>
                </div>
            </div>
            <p class="clear"></p>
        </div>
        <?php echo $this->Form->end(); ?>
        <span class="title_block bo_ra2"></span>
    </div>
    <!--END Tab1 -->
</div>
<div class="clear_percent_5 float_right">
    <div class="tab_1 full_width">
        <span class="title_block bo_ra1">
            <span class="float_left">
                <span class="fl_dent">
                    <h4>Jobtraq related</h4>
                </span>
            </span>
        </span>
        <form method="POST" id="jobtraq_related">
            <div class="tab_2_inner">
                <p class="clear">
                    <span class="label_1 float_left minw_lab2 fixbor3">Enabled for this user</span>
                </p>
                <div class="width_in3a float_left indent_input_tp">
                    <div class="in_active2">
                        <label class="m_check2">
                        <input type="checkbox" id="enable_for_this_user" name="enable_for_this_user" value="<?php if(isset($arr_return['enable_for_this_user']))echo $arr_return['enable_for_this_user'];?>"  <?php if(isset($arr_return['enable_for_this_user'])&&$arr_return['enable_for_this_user']==1){?> checked <?php }?>>
                        <span class="bx_check dent_chk"></span>
                        </label>
                        <span class="inactive dent_check color_hidden"></span>
                        <p class="clear"></p>
                    </div>
                </div>
                <div class="block_warning height_block2">
                    <span class="label_bg float_left minw_lab2 fixbor3"></span>
                    <div class="width_in3a float_left indent_input_tp">
                    </div>
                </div>
                <p class="clear"></p>
            </div>
        </form>
        <span class="title_block bo_ra2"></span>
    </div>
    <!--END Tab1 -->
</div>
  <p class="clear"></p>
    <p class="clear"></p>
<script type="text/javascript">
    function clearOnlogin(){
    $("input#on_login_set_quick_view").val("");
        var fieldname = $("input#on_login_set_quick_view").attr("name");
            var values = $("input#on_login_set_quick_view").val();
            var ids = $("#ContactId").val();
        $.ajax({
           url: '<?php echo URL; ?>/<?php echo $controller;?>/save_data_for_non_model',
           timeout: 15000,
           type: "POST",
           data: { fieldname : fieldname,values:values,ids:ids },
           success: function(html){
               console.log(html);
           }
       });
       return false;
    }
     $("form#profile_form input,select,radio").change(function() {
            var fieldname =$(this).attr("name");
            var values = $(this).val();
            var ids = $("#ContactId").val();
          var fieldtype = $(this).attr("type");

            if(fieldtype=='checkbox'){
                if($(this).is(':checked'))
                    values = 1;
                else
                    values = 0;
            }

            $.ajax({
                url: '<?php echo URL; ?>/<?php echo $controller;?>/save_data_for_non_model',
                timeout: 15000,
                type: "POST",
                data: { fieldname : fieldname,values:values,ids:ids},
                success: function(html){
                    console.log(html);
                }
            });

            return false;
        });

    /* $("form#ContactRefsForm input,select").change(function(){
        var fieldname = $(this).attr("name");
        var values = $(this).val();
        var ids = $("ContactId").val();
        //alert(ids);
        $.ajax({
          url:'<?php echo URL; ?>/<?php echo $controller;?>/save_data_for_non_model',
          timeout: 15000,
          type: "POST",
          data: { fieldname : fieldname,values:values,ids:ids},
          success: function(html){
              console.log(html);
            }
        });
        return false;
     });*/

        $("#jobtraq_related input, #jobtraq_related select, #jobtraq_related radio,#language_id,#theme").change(function() {
                var fieldname =$(this).attr("name");
                var values = $(this).val();
            if(fieldname=='language')
                values = $("#language_idId").val();
                var ids = $("#ContactId").val();
            //alert(ids);
                var fieldtype = $(this).attr("type");

                if(fieldtype=='checkbox'){
                    if($(this).is(':checked'))
                        values = 1;
                    else
                        values = 0;
                }
                $.ajax({
                    url: '<?php echo URL; ?>/<?php echo $controller;?>/save_data_for_non_model',
                    timeout: 15000,
                    type: "POST",
                    data: { fieldname : fieldname,values:values,ids:ids },
                    success: function(html){
                        if(html == 'ok'){
                            if(fieldname=='theme')
                                location.reload();
                        }
                    }
                });

                return false;
            });

    function change_password(object){
        $.ajax({
            url: '<?php echo URL; ?>/contacts/user_refs_auto_save',
            timeout: 15000,
            type:"post",
            data: $(object).closest('form').serialize(),
            success: function(result){
                ajax_note('Saved!');
                result = $.parseJSON(result);
                $("#ContactUserNameContact").val(result.full_name);
            }
        });
    }
</script>