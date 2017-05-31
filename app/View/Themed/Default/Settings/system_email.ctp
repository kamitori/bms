<style type="text/css">
.label{
    font-weight: bold;
    font-size: 12px;
    cursor: default;
}
</style>
<div class="tab_1 full_width">
    <span class="title_block bo_ra1">
        <span class="fl_dent">
            <h4>System email</h4>
        </span>
    </span>
    <div style="height: 473px;overflow-y: auto">
        <div class="jt_box" style=" width:100%;">
            <!- -!>
            <div id="system_email_content">
            <div class="jt_box_line">
                <div class=" jt_box_label " style=" width:25%;"></div>
                <div id="alert_message"></div>
                <input type="hidden" name="data[_id]" value="<?php echo $email['_id'] ?>" />
            </div>
            <div class="jt_box_line">
                <div class=" jt_box_label " style=" width:25%;"><span class="label">Email name</span></div>
                <div class="jt_box_field " style=" width:71%"><input name="data[email_name]" placeholder="Anvy Digital - JobTraq"  class="input_1 float_left  " type="text" value="<?php echo $email['email_name']; ?>"></div>
            </div>
            <div class="jt_box_line">
                <div class=" jt_box_label " style=" width:25%;"><span class="label">Email</span></div>
                <div class="jt_box_field " style=" width:71%"><input name="data[username]" placeholder="examle@example.com"  class="input_1 float_left  " type="text" value="<?php echo $email['username']; ?>"></div>
            </div>
            <div class="jt_box_line">
                <div class=" jt_box_label " style=" width:25%;"><span class="label" id="password_label">Password</span></div>
                <div class="jt_box_field " style=" width:71%"><input name="data[password]" id="password_content" title="Press Password label to show password" class="input_1 float_left" type="password" value="<?php echo $email['password']; ?>"></div>
            </div>
            <div class="jt_box_line">
                <div class=" jt_box_label " style=" width:25%;"><span class="label">Host</span></div>
                <div class="jt_box_field " style=" width:71%"><input name="data[host]" class="input_1 float_left" type="text" value="<?php echo $email['host']; ?>"></div>
            </div>
            <div class="jt_box_line">
                <div class=" jt_box_label " style=" width:25%;"><span class="label">Port</span></div>
                <div class="jt_box_field " style=" width:71%"><input name="data[port]" onkeypress="return isPrice(this);" class="input_1 float_left" type="text" value="<?php echo $email['port']; ?>"></div>
            </div>
            </div>
            <!- -!>
            <div class="jt_box_line">
                <div class=" jt_box_label " style=" width:25%;height:150px"></div>
                <div class="jt_box_field " style=" width:71%"></div>
            </div>
            <div class="jt_box_line">
                <div class=" jt_box_label " style=" width:25%;height:150px"></div>
                <div class="jt_box_field " style=" width:71%"></div>
            </div>
        </div>
    </div>
    <span class="title_block bo_ra2">
    <span class="float_left bt_block"></span>
    </span>
</div>
<script type="text/javascript">
$(function(){
    $("input").change(function(){
        $.ajax({
            url: '<?php echo URL ?>/settings/system_email',
            type: 'POST',
            data: $("input","#system_email_content").serialize(),
            success: function(result){
                if(result!='ok')
                    alerts('Message',result);
            }
        });
    });
    $("#password_label")
        .mouseup(function(){
            $("#password_content").attr('type','password');
        })
        .mousedown(function(){
            $("#password_content").attr('type','text');
        });
})
</script>
