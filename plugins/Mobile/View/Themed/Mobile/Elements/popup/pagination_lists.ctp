<div id="pagination">
    <ul>
        <li>
            <select id="page-row" class="pagination-page-list" name="pagination[page-list]">
                <?php
                $page = 10;
                $option = $page;
                $total = 100;
                while ($option <= $total) {

                    $selected = '';
                    if( isset($limit) && $option == $limit ){ $selected = 'selected="selected"';  }
                    echo '<option value="' . $option . '" '.$selected.'>' . $option . '</option>';
                    $option = $page + $option;
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
            <label>&nbsp; of&nbsp;</label>
            <span id="pagination-num-label" style="padding-right:6px;"><?php echo $total_page; ?></span>
        </li>
        <?php if( $page_num < $total_page ){ ?>
        <li>
            <span id="next" class="pagination-nav pagination-next" title="Next">&nbsp;</span>
        </li>
        <?php }else{ ?>
        <li><span class="popup-pagination">&nbsp;</span></li>
        <?php } ?>
        <!-- An,16.1.2014, dung cho salesaccounts-->
        <?php if($controller=='salesaccounts'){ ?>
        <script type="text/javascript">
            $(function(){
                $("#sum_30_view").val($("#sum_30").val());
                $("#sum_60_view").val($("#sum_60").val());
                $("#sum_90_view").val($("#sum_90").val());
                $("#sum_other_view").val($("#sum_other").val());
                $("#sum_balance_view").val($("#sum_balance").val());
            })
        </script>
         <li>
            <ul class="list_inner_sa ">
                <li><input id="sum_30_view" readonly="readonly" class="right_txt" type="text"></li>
                <li><input id="sum_60_view" readonly="readonly"class="right_txt"type="text"></li>
                <li><input id="sum_90_view" readonly="readonly"class="right_txt"type="text"></li>
                <li><input id="sum_other_view" readonly="readonly"class="right_txt"type="text"></li>
                <li><input id="sum_balance_view" readonly="readonly"class="right_txt"type="text"></li>
            </ul>
        </li>
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
    $id_submit_button = $controller.'_submit_button';
?>

<input id="pagination_sort_field" type="hidden" type="text" name="sort[field]" value="<?php if(isset($sort_field))echo $sort_field; ?>">
<input id="pagination_sort_type" type="hidden" type="text" name="sort[type]" value="<?php if(isset($sort_type))echo $sort_type; ?>">

<?php
    // ?n nút submit này
    echo $this->Js->submit('Search', array(
        'id' => $id_submit_button,
        'style' => 'height:1px; width:1px;opacity:0.1',
        'success' => '$("#lists_view_content").html(data);'
    ));
?>

<script type="text/javascript">
    $(function() {

        var contain = $("#content");

        <?php if( $total_page <= 1 && $limit > 10 ){ ?>
        $(".block_dent2", contain).css("overflow", "");
        $(".block_dent2", contain).css("height", "");
        <?php } ?>

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

    });

</script>

<style type="text/css">
.k-window {
    padding-bottom: 0 !important;
}
.desc, .asc {
    margin-top: 0;
}
</style>

<?php if( $action == 'lists' ){ ?>
<style type="text/css">
#pagination {
    position: fixed;
    bottom: 26px;
}
</style>
<?php } ?>