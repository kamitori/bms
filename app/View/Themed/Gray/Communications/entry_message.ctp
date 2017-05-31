<div class="bg_menu">
    <ul class="menu_control float_left">
        <li>
            <a style=" cursor:pointer;" class="entry_menu_send ">
                <?php echo translate('Send'); ?>&nbsp;&nbsp;
            </a>
        </li>
    </ul>
</div>
<div id="content">
    <div class="clear_percent">
        <div class="block_dent_a">
            <div class="title_1 float_left">
                <h1><?php echo translate('To'); ?>: <?php echo $contact_name; ?></h1>
            </div>
        </div>
    </div>
    <div class="width98 mar_gin float_left" style="margin-bottom:15px">
        <div class="tab_1 full_width">
            <span class="title_block bo_ra1">
                <span class="fl_dent"><h4>Message</h4></span>
                    <div class="float_left hbox_form dent_left_form width37">
                        <!-- <input class="btn_pur size_width2" type="button" value="Spell check" disabled style="color:red"> -->
                    </div>
            </span>
            <p class="clear"></p>
            <textarea id="message_content" class="w_text_area" style="height:75px;min-height: 30px;"></textarea>
            <span class="title_block bo_ra2">
                <span class="bt_block float_right no_bg"></span>
            </span>
        </div><!--END Tab1 -->
    </div>
    <div class="float_left width98 mar_gin" style="margin-top:0; margin-bottom:30px">
        <?php echo $this->element('../Communications/related_messages'); ?>
    </div>
</div>
<script type="text/javascript">
    $(function(){
        $("#cancel_message").click(function(event) {
            confirms('Message','Cancel message?',
                function(){
                    window.location.replace("<?php echo URL.'/'.$controller.'/entry/'.$message_id;?>");
                },
                function(){
                    return false;
                });
        });
        $(".entry_menu_send").click(function(){
            if($("#message_content").val().trim()!='')
                $.ajax({
                    url:   "<?php echo URL.'/'.$controller.'/create_message/'; ?>",
                    type: 'POST',
                    data: {<?php if(isset($reply)&&$reply) echo "reply: true, message_id:'".$message_id."', "; ?>contact: '<?php echo $contact_name?>',contact_id: '<?php echo $contact_id; ?>',content: $("#message_content").val()},
                    success: function(result){
                        result = jQuery.parseJSON(result);
                        if(result.status!='ok')
                            alerts('Message',result);
                        else
                            window.location.replace(result.url);
                        console.log(result);

                    }
                });
            else
                alerts('Message','Please enter a message first.');
        });
    })
</script>