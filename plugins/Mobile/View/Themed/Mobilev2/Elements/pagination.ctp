<div class="pagination" data-role="footer" data-position="fixed">
    <input id="num" class="pagination-num" type="hidden" name="pagination[page-num]" value="<?php echo $page_num; ?>">
    <div class="ui-block-a" style="width: 50%;">
        <a class="prev-pagination" data-role="button" class="ui-btn ui-shadow ui-corner-all" <?php if( $page_num <= 1 ) echo 'disabled'; ?> href="javascript:void(0)" style="width: 97%;" title="Prev">PREV</a>
    </div>
    <div class="ui-block-b" style="width: 50%;">
        <a class="next-pagination" data-role="button" class="ui-btn ui-shadow ui-corner-all" <?php if( $page_num >= $total_page )  echo 'disabled' ?> href="javascript:void(0)" style="width: 97%;" title="Next">NEXT</a>
    </div>
</div>
<script type="text/javascript">
    $(function() {
        //USE FOR POPUP
        $(".window_popup_input_<?php echo $controller ?>_<?php echo $key; ?>").change(function(e){
               $("#<?php echo $controller ?>_popup_form<?php echo $key ?>").submit();
               return false;
        });
        $("#<?php echo $controller ?>_popup_form<?php echo $key; ?>").submit(function(){
            $.ajax({
                type:"post",
                url:"<?php echo M_URL.'/'.$controller.'/popup/'.$key; if (isset($_GET['is_supplier'])) echo '?is_supplier=1'; if (isset($_GET['is_shipper'])) echo '?is_shipper=1' ?>",
                data:$("#<?php echo $controller ?>_popup_form<?php echo $key; ?>").serialize(),
                success:function (html) {
                    $("#<?php echo $controller ?>_popup_form<?php echo $key; ?>").parent().html(html).trigger('create');
                },
            });
            return false;//skip normal submit
        });
        $("a", ".pagination").click(function() {
            var class_nav = $(this).attr("class");
            var num = $("#num", ".pagination").val();
            if( class_nav.indexOf("prev-pagination") >= 0 ){
                $("#num", ".pagination").val(parseInt(num) - 1);
            }else if( class_nav.indexOf("next-pagination") >= 0 ){
                $("#num", ".pagination").val(parseInt(num) + 1);
            }
            $("#<?php echo $controller; ?>_popup_form<?php echo $key ?>").submit();
        });
        //END
    });
</script>