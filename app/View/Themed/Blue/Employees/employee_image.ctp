<div class="float_left " style=" width:100%;margin-top:1%;margin-left:0;float:left;">
    <div class="tab_1 full_width" id="block_full_image">
        <!-- Header-->
        <span class="title_block bo_ra1">
            <span class="fl_dent">
                <h4>Image</h4>
            </span>
            <span class="icon_down_tl top_f" style="cursor:pointer;">
                <form action="<?php echo URL; ?>/employee/employee_image" id="employee_image_upload_form" method="post" enctype="multipart/form-data">
                    <input type="file" name="employee_image_upload" id="employee_image_upload" class="jt_upload" title="Import image" style="cursor:pointer;">
                </form>
            </span>
            <?php if($employee_image){ ?>
            <div class="middle_check" style="margin-left: 275px;">
                <a title="Delete image" style=" text-decoration: none" href="javascript:void(0)" onclick="product_image_delete()">
                <span style="width: 14px;height: 14px;border: 1px solid #fff; text-align: center; line-height: 14px;font-weight: 900;">X</span>
                </a>
            </div>
            <?php } ?>
        </span>
        <!--CONTENTS-->
        <div class="jt_subtab_box_cont" style=" ">
            <div class="box_image" style=" padding:0; height:147px; vertical-align:middle;overflow-y: hidden;">
                <?php if($employee_image){ ?>
                <img src="<?php echo URL.'/'.$employee_image; ?>" alt="Images" style="max-height: 140px; margin-top: 0px !important;">
                <?php } ?>
            </div>
        </div>
        <span class="title_block bo_ra2">
        </span>
    </div>
</div>
<script type="text/javascript">
    function product_image_delete(){
        confirms( "Message", "Are you sure you want to delete?",
            function(){
                $.ajax({
                    url : "<?php echo URL.'/employees/employee_image/delete' ?>",
                    success: function(){
                        $(".middle_check","#block_full_image").remove();
                        $("img",".box_image").remove();
                    }
                })
            },function(){
                //else do somthing
        });
        return false;
    }
$(function(){
    $("#employee_image_upload").change(function(){
        var form = $("#employee_image_upload_form").submit();
    });
    $("#employee_image_upload_form").submit(function(){
         $.ajax({
            url : "<?php echo URL.'/employees/employee_image' ?>",
            type: "POST",
            data: new FormData(this),
            processData: false,
            contentType: false,
            success: function(result){
                var html = '<img src="'+result+'" alt="Images" style="max-height: 140px; margin-top: 0px !important;">';
                $(".box_image","#block_full_image").html(html);
            }
        });
         return false;
    });
})
</script>