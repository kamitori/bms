<?php echo $this->Form->create($model, array('id' => $controller.'_popup_form')); ?>

<div style="float: left;">

</div>
 <ul class="menu_control float_right">
    <li >
        <a href="javascript:void(0)" id="employee_submit">Submit</a>
    </li>
</ul>
<!-- END SEARCH POPUP -->

<div style="clear:both;height:6px"></div>

<div class="block_dent2 container_same_category" style="overflow: auto;overflow-x: hidden;max-width:1000px; margin: 0 auto; height:430px;" id="list_view_employee">
    <table class="jt_tb" id="employees_table" style="font-size:12px;">
        <thead style="position: fixed;" id="pagination_sort">
            <tr>
                <th style="width:20px"></th>
                <th style="width:775px"><?php echo translate('Employee'); ?><span id="sort_first_name" rel="first_name" class="desc"></span></th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <th style="width:20px"></th>
                <th style="width:775px ">&nspb;</th>
            </tr>
            <tr class="jt_line_light" id="tr_all" style="position: fixed; padding: 0;">
                <td align="center" style="width:20px"><input class="check_employees" name="data[employees][all]" type="checkbox" id="all" value="all" checked /></td>
                <td onclick="$('input[type=checkbox]',$(this).parent()).click()" style="font-size: 14px; color: blue; font-weight: bold;width:775px">All</td>
            </tr>
            <tr style="height: 33px;">
                <th style="width:20px"></th>
                <th style="width:775px ">&nspb;</th>
            </tr>
            <?php
            $i = 0; $STT = 0;
            foreach ($arr_employees as $value) {
                $i = 1 - $i; $STT += 1;
                ?>
                <tr class="jt_line_<?php if ($i == 1) { ?>black<?php } else { ?>light<?php } ?>" id="tr_<?php echo $value['_id']; ?>">
                    <td align="center"><input class="check_employees" name="data[employees][<?php echo $value['_id'] ?>]" type="checkbox" id="<?php echo $value['_id'] ?>" value="<?php echo $value['_id'] ?>" /></td>
                    <td onclick="$('input[type=checkbox]',$(this).parent()).click()">
                        <?php
                            echo (isset($value['first_name']) ? $value['first_name'] : '').' '.(isset($value['last_name']) ? $value['last_name'] : '');
                        ?>&nbsp;
                    </td>
                </tr>
            <?php } ?>

            <?php if( $STT > 0 && $STT < 10 ){ // chỉ khi nào số lượng nhỏ hơn 10 mới add thêm mà thôi
                $loop_for = $limit - $STT;
                for ($j=0; $j < $loop_for; $j++) {
                    $i = 1 - $i;
                  ?>
                <tr class="jt_line_<?php if ($i == 1) { ?>black<?php } else { ?>light<?php } ?>">
                    <td>&nbsp;</td>
                    <td></td>
                </tr>
            <?php
                }
            } ?>
        </tbody>
    </table>
</div>
<p>
<p>
<p>
<script type="text/javascript">
$(function(){
    $(".check_employees","#employees_table").change(function(){
        var id = $(this).attr("id");
        if( $(this).is(":checked") ) {
            if( id == "all") {
                $(".check_employees[id!=all]","#employees_table").prop("checked", false);
            } else {
                $("#all","#employees_table").prop("checked", false);
            }
        } else {
            if( !$(".check_employees:checked","#employees_table").length ) {
                $("#all","#employees_table").prop("checked", true);
            }
        }
    });
});
</script>
<?php echo $this->Form->end(); ?>