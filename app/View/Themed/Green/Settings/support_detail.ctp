<style type="text/css">
.slide_out_div strong{
    font-weight: 900;
}
</style>
<div class="tab_1 full_width">
    <span class="title_block bo_ra1">
        <span class="fl_dent">
            <h4 id="setting_name">New article support</h4>
        </span>
        <a title="Add new article" href="javascript:void(0)" onclick="support_add('<?php echo $name; ?>')">
            <span class="icon_down_tl top_f"></span>
        </a>
    </span>
    <ul class="ul_mag clear bg3">
        <li style="width:82%" class="hg_padd">Name</li>
        <li style="width:15%" class="hg_padd center_text no_border">Action</li>
    </ul>
    <div style="height: 449px;overflow-y: auto" class="container_same_category">
        <?php
            $i = 0;
            if($supports->count()){
                foreach($supports as $value){
        ?>
        <form>
            <ul class="ul_mag clear bg<?php echo ($i%2 ? 1:2); ?>">
                <input type="hidden" name="_id" value="<?php echo $value['_id'] ?>" />
                <li style="width:82%" class="hg_padd center_text">
                    <input type="text" name="name" class="input_inner bg<?php echo ($i%2 ? 1:2); ?>" value="<?php echo $value['name']; ?>" >
                </li>
                <li style="width:15%" class="hg_padd center_text no_border">
                    <div class="center_element">
                        <input name="deleted" type="checkbox" <?php if($value['deleted']) echo 'checked'; ?> value="1" class="left_icon" id="delete@<?php echo $value['_id']; ?>">
                        <a id="<?php echo $value['_id'] ?>" href="javascript:void(0)" class="combobox_arrow left_icon iconedit clickshowcontent"></a>
                    </div>
                </li>
            </ul>
        </form>
        <div class="slide_out_div" id="content_<?php echo $value['_id'] ?>" style="display: none; padding:10px;border:1px dashed" contenteditable="true"></div>
        <?php
                    $i++;
                }
            }
            if($i<20){
                for($i;$i<20;$i++)
                    echo '<ul class="ul_mag clear bg'.($i%2 ? 1:2).'"></ul>';
            }

        ?>
    </div>
    <span class="title_block bo_ra2">
        <span class="float_left bt_block">Edit or create values for list.</span>
    </span>
</div>
<script type="text/javascript">
$(function(){
    $(".container_same_category").mCustomScrollbar({
        scrollButtons:{
            enable:false
        },
        advanced:{
            updateOnContentResize: true,
            autoScrollOnFocus: false,
        }
    });
    $(".clickshowcontent").click(function(){
        var id = $(this).attr("id");
        if(!$("#content_"+id).is(":hidden")){
            $("#content_"+id).clearQueue().slideUp("slow");
            return false;
        }
        if(!$("#content_"+id).hasClass("cke_editable")){
            $.ajax({
                url: '<?php echo URL; ?>/settings/get_content_support/',
                type: 'POST',
                data: {'_id':id},
                success: function(html){
                    $("#content_"+id).html(html);
                    CKEDITOR.config.toolbar = 'InlineEditor';
                    CKEDITOR.inline( "content_"+id ,{
                        filebrowserImageUploadUrl : '<?php echo URL; ?>/js/kcfinder/upload.php?type=images',
                        filebrowserImageBrowseUrl : '<?php echo URL; ?>/js/kcfinder/browse.php?type=images',
                        on:{
                            blur: function(event){
                                if (event.editor.checkDirty()){
                                    $.ajax({
                                        url: '<?php echo URL; ?>/settings/support_auto_save/',
                                        type: 'POST',
                                        data: {'_id':id,'content':event.editor.getData()},
                                        success: function(result){
                                            if(result!='ok')
                                                alerts('Message',result);
                                        }
                                    });
                                }
                            }
                        }
                    });
                }
            });
        }
        if ($("#content_"+id).is(":hidden")) {
            $(".slide_out_div").clearQueue().slideUp("slow");
            $("#content_"+id).clearQueue().slideDown("slow");
        } else {
            $("#content_"+id).clearQueue().slideUp("slow");
        }
    });
    $("input",".container_same_category").change(function(){
        if($(this).attr("type")=="checkbox"){
            $(this).val(0);
            if($(this).is(":checked"))
                $(this).val(1);
        }
        var ul = $(this).parents("ul");
        $.ajax({
            url: '<?php echo URL; ?>/settings/support_auto_save/',
            type: 'POST',
            data: $(":input", ul).serialize(),
            success: function(result){
                if(result!='ok')
                    alerts('Message',result);
            }
        });
    });
})
function support_add(name){
     $.ajax({
            url: '<?php echo URL; ?>/settings/support_add/' + name,
            timeout: 15000,
            success: function(html){

                $("div#support_detail").html(html);
                $('.container_same_category', "#support_detail").mCustomScrollbar({
                    scrollButtons:{
                        enable:false
                    }
                });
            }
        });
}
</script>