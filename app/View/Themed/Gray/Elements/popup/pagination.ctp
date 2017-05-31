<?php if($total_page > 0){ ?>

<div id="pagination">
    <ul>
        <li>
            <select id="page-row" class="pagination-page-list" name="pagination[page-list]">
                <?php
                $page_break = array(10,20,50,100,150,200,250);
                foreach ($page_break as $value) {
                    $selected = '';
                    if( isset($limit) && $limit == $value ){ $selected = 'selected="selected"';  }
                    echo '<option value="' . $value . '" '.$selected.'>' . $value . '</option>';
                }
                ?>
            </select>
        </li>
        <!-- <li>
            <span id="first" class="pagination-nav pagination-first" title="First">&nbsp;</span>
        </li> -->
        <?php if( $page_num > 1 ){ ?>
        <li>
            <span id="prev" class="pagination-nav pagination-prev" title="Prev">&nbsp;</span>
        </li>
        <?php }else{ ?>
        <li><span class="popup-pagination">&nbsp;</span></li>
        <?php } ?>

        <li>
            <label style="padding-left:27px;">Page</label>
        </li>
        <li>
            <input id="num" class="pagination-num" type="text" name="pagination[page-num]" value="<?php echo $page_num; ?>" size="2">
        </li>
        <li>
            <label>&nbsp; of  &nbsp;</label>
            <span id="pagination-num-label" style="padding-right:6px;"><?php echo $total_page; ?></span>
        </li>
        <?php if( $page_num < $total_page ){ ?>
        <li>
            <span id="next" class="pagination-nav pagination-next" title="Next">&nbsp;</span>
        </li>
        <?php }else{ ?>
        <li><span class="popup-pagination">&nbsp;</span></li>
        <?php } ?>
        <!-- <li>
            <span id="last" class="pagination-nav pagination-last" title="Last">&nbsp;</span>
        </li> -->
        <li>
            <span class="pagination-info">
                <span id="pagination-info-of"><?php echo $from_total = $limit*($page_num - 1) + 1; ?>
                    <?php
                        $end_record = $from_total + $total_current - 1;
                        if( $end_record > $total_record ){
                            echo ' - ' . $total_record;
                        }else{
                            echo ' - ' . $end_record;
                        }
                    ?>
                </span>
                <label>&nbsp; of &nbsp;</label>
                <span id="pagination-info-of"><?php echo $total_record; ?></span>
            </span>
        </li>
    </ul>
</div>

<?php

    if(isset($popup_id_submit_button)){
        $id_submit_button = $popup_id_submit_button;
    }else{
        $id_submit_button = $controller.'_popup_submit_subtton_'.$key;
    }
?>

<input id="pagination_sort_field" type="hidden" type="text" name="sort[field]" value="<?php if(isset($sort_field))echo $sort_field; ?>">
<input id="pagination_sort_type" type="hidden" type="text" name="sort[type]" value="<?php if(isset($sort_type))echo $sort_type; ?>">

<script type="text/javascript">
    $(function() {

        var contain = $("#window_popup_<?php echo $controller.$key; ?>");

        <?php //if( $total_page <= 1 && $limit > 10 ){ ?>
        // $(".block_dent2", contain).css("overflow", "");
        // $(".block_dent2", contain).css("height", "");
        <?php //} ?>

        $(".pagination-nav", contain).click(function() {
            var id_nav = $(this).attr("id");
            var num = $("#num", contain).val();
            if( id_nav == "prev" ){
                $("#num", contain).val(parseInt(num) - 1);
            }else if( id_nav == "next" ){
                $("#num", contain).val(parseInt(num) + 1);
            }
            $("#<?php echo $id_submit_button; ?>", contain).click();
        });

        $("#num", contain).change(function() {
            $("#<?php echo $id_submit_button; ?>", contain).click();
        });

        $("#page-row", contain).change(function() {
            $("#num", contain).val(1);
            $("#<?php echo $id_submit_button; ?>", contain).click();
        });

        $("th", contain).click(function() {console.log(this);
            var span = $("span", this);
            if( span.attr('rel') != undefined ){ // tab nào có span thì mới sort
                $("#pagination_sort_field", contain).val(span.attr('rel'));
                $("#pagination_sort_type", contain).val(span.attr('class'));
                $("#<?php echo $id_submit_button; ?>", contain).click();
            }

        });

        <?php if(isset($sort_field) && isset($sort_type_change) ){ ?>
        $("#sort_<?php echo $sort_field; ?>", contain).attr("class", "<?php echo $sort_type_change; ?>");
        <?php } ?>

    });



    // $(".k-window").fadeOut();
    // $("#window_popup_products" + keys).data("kendoWindow").close();
</script>

<style type="text/css">
.k-window {
    padding-bottom: 0 !important;
}
.desc, .asc {
    margin-top: 0;
}
</style>

<?php } ?>

<?php if( $action == 'lists' ){ ?>
<style type="text/css">
#pagination {
    position: fixed;
    bottom: 26px;
}
</style>
<?php } ?>

<script type="text/javascript">

function pagination_remove_num_<?php echo $controller.$key; ?>(){
    var contain = $("#window_popup_<?php echo $controller.$key; ?>");
    $("#num", contain).val(1);
}
function window_popup_extra(contain) {
    if('mCustomScrollbar' in $(".container_same_category", contain)){
        $(".container_same_category", contain).mCustomScrollbar({
            scrollButtons: {
                enable: false
            }
        });
    }
}
</script>