<?php echo $this->Form->create('Quotation', array('id' => 'quotations_popup_form' . $key)); ?>

<div style="float: left;width: 92%;">
    <?php
    // Ẩn nút submit này
    /*echo $this->Js->submit('Search', array(
        'id' => $controller.'_popup_submit_subtton_' . $key,
        'style' => 'height:1px; width:1px;opacity:0.1',
        'success' => '$("#window_popup_' . $controller . $key . '").html(data);'
    ));*/
    ?>
    <input id="<?php echo $controller.'_popup_submit_subtton_' . $key; ?>" style="height:1px; width:1px;opacity:0.1" type="submit" value="Search">
    <script type="text/javascript">
    //<![CDATA[
    $("#<?php echo $controller.'_popup_submit_subtton_' . $key; ?>").bind("click", function (event) {
        $.ajax({
            data:$("#<?php echo $controller.'_popup_submit_subtton_' . $key; ?>").closest("form").serialize(),
            success:function (data, textStatus) {
                $("#window_popup_<?php echo $controller.$key; ?>").html(data);
            },
            type:"post",
            url:"<?php echo URL; ?>/enquiries/popup"
        });
        return false;
    });
    //]]>
    </script>
</div>
<!-- END SEARCH POPUP -->

<div class="block_dent2 container_same_category" style="overflow: auto;overflow-x: hidden;max-width:1000px; margin: 0 auto; height:430px;" id="list_view_quotations">
    <table class="jt_tb" id="Form_add" style="font-size:12px;">
        <thead style="position: fixed;" id="pagination_sort">
            <tr>
                <th style="width:60px"><?php echo translate('Ref no'); ?><span id="sort_no" rel="no" class="desc"></span></th>
                <th style="width:348px"><?php echo translate('Company name'); ?><span id="sort_company" rel="company" class="desc"></span></th>
                <th style="width:272px"><?php echo translate('Contact name'); ?><span id="sort_contact_name" rel="contact_name" class="desc"></span></th>
                <th style="width: 118px"><?php echo translate('Date'); ?><span id="sort_date" rel="date" class="desc"></span></th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <th style="width:60px ">&nspb;</th>
                <th style="width:348px"></th>
                <th style="width:272px"></th>
                <th style="width:118px"></th>
            </tr>
            <?php
            $i = 0; $STT = 0;
            foreach ($arr_quotation as $value) {
                $i = 1 - $i; $STT += 1;
                ?>
                <tr class="jt_line_<?php if ($i == 1) { ?>black<?php } else { ?>light<?php } ?>" onclick="after_choose_quotations<?php if (substr($key, 0, 1) == '_') echo $key; ?>('<?php echo $value['_id']; ?>', '', '<?php echo $key; ?>')">

                    <td align="center"><?php if(isset($value['no']))echo $value['no']; ?></td>
                    <td align="left"><?php if(isset($value['company']))echo $value['company']; ?></td>
                    <td align="left"><?php if(isset($value['contact_name']))echo $value['contact_name']; ?></td>
                    <td align="center"><?php if(isset($value['date']))echo $this->Common->format_date( $value['date']->sec, false); ?>
                        <input type="hidden" id="after_choose_quotations<?php echo $key; ?><?php echo $value['_id']; ?>" value="<?php echo htmlentities(json_encode($value)); ?>">
                    </td>
                </tr>
            <?php } ?>

            <?php if( $STT > 0 && $STT < 10 ){ // chỉ khi nào số lượng nhỏ hơn 10 mới add thêm mà thôi
                $loop_for = $limit - $STT;
                for ($j=0; $j < $loop_for; $j++) {
                    $i = 1 - $i;
                  ?>
                <tr class="jt_line_<?php if ($i == 1) { ?>black<?php } else { ?>light<?php } ?>"><td>&nbsp;</td><td></td><td></td><td></td></tr>
            <?php
                }
            } ?>
        </tbody>
    </table>

    <?php if( $STT == 0 ){ ?>
    <center style="margin-top:30px">(No data)</center>
    <?php } ?>
</div>

<?php echo $this->element('popup/pagination'); ?>

<?php echo $this->Form->end(); ?>