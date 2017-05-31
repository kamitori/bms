<div class="bg_menu">
    <ul class="menu_control float_right">
        <li style="margin-right: 5px;">
            <a href="javascript:void(0)" class="submit_form_<?php echo $controller;?>_option">
                Submit for Line entry
            </a>
        </li>
        <li>
            <a style=" cursor:pointer;" class="entry_menu_save_custom_option" onclick="save_custom_product();" >
                &nbspSave Custom Product&nbsp;
            </a>
        </li>
    </ul>
</div>
<script type="text/javascript">
$(".submit_form_<?php echo $controller;?>_option").click(function(){
    $("#submit",".form_<?php echo $controller;?>_option").click();
})
</script>