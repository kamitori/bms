<input id="inputHolder" name="inputHolder" type="hidden" value= "<?php echo (isset($inputHolder) ? $inputHolder : '') ?>" />
<style type="text/css">
    #pagination li{
        display: inline;
    }
</style>
<div id="pagination" data-role="footer" class="bttom_pnex">
    <ul>
        <input type="hidden" id="page" name="pagination[page-list]" value="10" />
        <li>
            <a <?php if( $page_num > 1 ){ echo 'id="prev" class="pagination-nav pagination-prev"'; } else echo 'class="pagination-prev pagination-no-click"'; ?> href="javascript:void(0)"  title="Prev">PREV</a>
        </li>
        <li><span class="indent_links"></span></li>
        <li>
            <input id="num" class="pagination-num" type="hidden" name="pagination[page-num]" value="<?php echo $page_num; ?>" size="2">
        </li>
        <li>
            <a <?php if( $page_num < $total_page ){  echo 'id="next" class="pagination-nav pagination-next"'; } else echo 'class="pagination-next pagination-no-click"'; ?> href="javascript:void(0)" title="Next">NEXT</a>
        </li>
    </ul>
</div>

<?php

    if(isset($popup_id_submit_button)){
        $id_submit_button = $popup_id_submit_button;
    }else{
        $id_submit_button = $controller.'_popup_submit_'.$key;
    }
?>

<input id="pagination_sort_field" type="hidden" type="text" name="sort[field]" value="<?php if(isset($sort_field))echo $sort_field; ?>">
<input id="pagination_sort_type" type="hidden" type="text" name="sort[type]" value="<?php if(isset($sort_type))echo $sort_type; ?>">

<script type="text/javascript">
    $(function() {
        //USE FOR POPUP
         $(".window_popup_input_<?php echo $controller ?>_<?php echo $key; ?>").on("keypress",function(e){
            var code = (e.keyCode ? e.keyCode : e.which);
             if(code == 13) { //Enter keycode
               $("#<?php echo $controller ?>_popup_form<?php echo $key ?>").submit();
               return false;
            }
        });
        $("#<?php echo $controller ?>_popup_form<?php echo $key; ?>").on("submit",function(){
            loading();
            $.ajax({
                type:"post",
                url:"<?php echo M_URL.'/'.$controller.'/popup/'.$key ?>",
                data:$("#<?php echo $controller ?>_popup_form<?php echo $key; ?>").serialize(),
                success:function (data) {
                    $.mobile.loading( 'hide' );
                    $("#<?php echo $key; ?>").html(data).trigger('create');
                },
            });
            return false;//skip normal submit
        });
        //END

        var contain = $("#<?php echo $controller; ?>_popup_form<?php echo $key ?>");

        $(".pagination-nav", contain).click(function() {
            var id_nav = $(this).attr("id");
            var num = $("#num", contain).val();
            if( id_nav == "prev" ){
                $("#num", contain).val(parseInt(num) - 1);
            }else if( id_nav == "next" ){
                $("#num", contain).val(parseInt(num) + 1);
            }
            $("#<?php echo $controller; ?>_popup_form<?php echo $key ?>").submit();
        });

        $("#num", contain).change(function() {
            $("#<?php echo $controller; ?>_popup_form<?php echo $key ?>").submit();
        });

        $("#page-row", contain).change(function() {
            $("#num", contain).val(1);
            $("#<?php echo $controller; ?>_popup_form<?php echo $key ?>").submit();
        });

        $("th", contain).click(function() {console.log(this);
            var span = $("span", this);
            if( span.attr('rel') != undefined ){ // tab nào có span thì mới sort
                $("#pagination_sort_field", contain).val(span.attr('rel'));
                $("#pagination_sort_type", contain).val(span.attr('class'));
                $("#<?php echo $controller; ?>_popup_form<?php echo $key ?>").submit();
            }

        });

        <?php if(isset($sort_field) && isset($sort_type_change) ){ ?>
        $("#sort_<?php echo $sort_field; ?>", contain).attr("class", "<?php echo $sort_type_change; ?>");
        <?php } ?>

    });
    function pagination_remove_num_<?php echo $controller.$key; ?>(){
        var contain = $("#<?php echo $controller; ?>_popup_form<?php echo $key ?>");
        $("#num", contain).val(1);
    }
</script>