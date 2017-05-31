<?php echo $this->Form->create($model, array('id' => $controller.'_popup_form' . $key)); ?>

<div style="float: left;">

    <div class="float_left">
        <h6 class="float_left" style="margin-top:2px"><?php echo translate('Filter by') . ': ' . translate('Batche'); ?> &nbsp;</h6>
        <span class="float_left">
            <span class="block_sear  block1">
                <span class="bg_search_1"></span>
                <span class="bg_search_2"></span>
                <div class="box_inner_search float_left">
                    <a href="javascript:void(0)" onclick="$('#<?php echo $controller; ?>_popup_submit_subtton_<?php echo $key; ?>').click();"><span class="icon_search"></span></a>
                    <div class="styled_select2 float_left">
                        <?php
                        echo $this->Form->input($model.'.name', array(
                            'style' => 'background: #636363;color: #fff;margin-left: -2px;margin-top:-2px;'
                        ));
                        ?>
                    </div>
                    <a href="javascript:void(0)" onclick="$('#<?php echo $model;?>Name').val(''); $('#<?php echo $controller; ?>_popup_submit_subtton_<?php echo $key; ?>').click();"><span class="icon_closef" style="margin-left:0"></span></a>

                </div>
            </span>
        </span>
    </div>

    <div class="float_left" style="margin-left: 10px">
        <h6 class="float_left" style="margin-top:2px"><?php echo translate('Contact name'); ?> &nbsp;</h6>
        <span class="float_left" style="margin-right: 8px;">
            <span class="block_sear  block1">
                <span class="bg_search_1"></span>
                <span class="bg_search_2"></span>
                <div class="box_inner_search float_left">
                    <a href="javascript:void(0)" onclick="$('#<?php echo $controller; ?>_popup_submit_subtton_<?php echo $key; ?>').click();"><span class="icon_search"></span></a>
                    <div class="styled_select2 float_left">
                        <?php
                        echo $this->Form->input($model.'.contact_name', array(
                            'id' => 'window_popup_'.$controller.'_ContactName_' . $key,
                            'onkeypress' => 'pagination_remove_num_'.$controller.$key.'();',
                            'style' => 'background: #636363;color: #fff;margin-left: -2px;margin-top:-2px;'
                        ));
                        ?>
                    </div>
                    <a href="javascript:void(0)" onclick="$('#window_popup_<?php echo $controller; ?>_ContactName_<?php echo $key; ?>').val('');
                            $('#<?php echo $controller; ?>_popup_submit_subtton_<?php echo $key; ?>').click();"><span class="icon_closef" style="margin-left:0"></span></a>

                </div>
            </span>
        </span>
    </div>

    <?php
// Ẩn nút submit này
    echo $this->Js->submit('Search', array(
        'id' => $controller.'_popup_submit_subtton_' . $key,
        'style' => 'height:1px; width:1px;opacity:0.1',
        'success' => '$("#window_popup_'.$controller . $key . '").html(data);'
    ));
    ?>

</div>
<!-- END SEARCH POPUP -->

<div style="clear:both;height:6px"></div>

<div class="block_dent2 container_same_category" style="overflow: auto;overflow-x: hidden;max-width:1000px; margin: 0 auto; height:430px;" id="list_view_quotations">
    <table class="jt_tb" id="Form_add" style="font-size:12px;">
        <thead style="position: fixed;" id="pagination_sort">
            <tr>
                <th style="width:65px"><?php echo translate('Code'); ?><span id="sort_no" rel="batch_no" class="desc"></span></th>
                 <th style="width:237px"><?php echo translate('Batch name'); ?><span id="sort_name" rel="batch_name" class="desc"></span></th>
                 <th style="width:262px"><?php echo translate('Created from'); ?><span id="sort_company" rel="created_from" class="desc"></span></th>
                <th style="width:98px"><?php echo translate('Batch created'); ?><span id="sort_contact_name" rel="batch_created" class="desc"></span></th>
                <th style="width: 113px"><?php echo translate('Date'); ?><span id="sort_date" rel="date" class="desc"></span></th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <th style="width:65px ">&nspb;</th>
                <th style="width:243px"></th>
                <th style="width:267px"></th>
                <th style="width:100px"></th>
                <th style="width:118px"></th>
            </tr>
            <?php
            $i = 0; $STT = 0;
            foreach ($arr_location as $value) {
                $i = 1 - $i; $STT += 1;
                ?>
                <tr class="jt_line_<?php if ($i == 1) { ?>black<?php } else { ?>light<?php } ?>" onclick="after_choose_batches<?php if (substr($key, 0, 1) == '_') echo $key; ?>('<?php echo $value['_id']; ?>', '', '<?php echo $key; ?>')">

                    <td align="center"><?php if(isset($value['code']))echo $value['code']; ?>&nbsp;</td>
                    <td align="left"><?php if(isset($value['batch_name']))echo $value['batch_name']; ?>&nbsp;</td>
                    <td align="left"><?php if(isset($value['created_from']))echo $value['created_from']; ?>&nbsp;</td>
                    <td align="left"><?php if(isset($value['batch_created']))echo $value['batch_created']; ?>&nbsp;</td>
                    <td align="center"><?php if(isset($value['date']))echo $this->Common->format_date( $value['date']->sec, false); ?>
                        <input type="hidden" id="after_choose_batches<?php echo $key; ?><?php echo $value['_id']; ?>" value="<?php echo htmlentities(json_encode($value)); ?>">
                    </td>
                </tr>
            <?php } ?>

            <?php if( $STT > 0 && $STT < 10 ){ // chỉ khi nào số lượng nhỏ hơn 10 mới add thêm mà thôi
                $loop_for = $limit - $STT;
                for ($j=0; $j < $loop_for; $j++) {
                    $i = 1 - $i;
                  ?>
                <tr class="jt_line_<?php if ($i == 1) { ?>black<?php } else { ?>light<?php } ?>"><td>&nbsp;</td><td></td><td></td><td></td><td></td></tr>
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