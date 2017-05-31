<?php echo $this->Form->create('Enquiry', array('id' => 'enquirys_popup_form' . $key)); ?>
<div style="float: left;">

    <div class="float_left">
        <h6 class="float_left" style="margin-top:2px"><?php echo translate('Filter by') . ': ' . translate('Company'); ?> &nbsp;</h6>
        <span class="float_left">
            <span class="block_sear  block1">
                <span class="bg_search_1"></span>
                <span class="bg_search_2"></span>
                <div class="box_inner_search float_left">
                    <a href="javascript:void(0)" onclick="$('#<?php echo $controller; ?>_popup_submit_subtton_<?php echo $key; ?>').click();"><span class="icon_search"></span></a>
                    <div class="styled_select2 float_left">
                        <?php
                        echo $this->Form->input('Enquiry.company', array(
                            'id' => 'window_popup_'.$controller.'_Company_' . $key,
                            'style' => 'background: #636363;color: #fff;margin-left: -2px;margin-top:-2px;'
                        ));
                        ?>
                    </div>
                    <a href="javascript:void(0)" onclick="$('#window_popup_<?php echo $controller; ?>_Company_<?php echo $key; ?>').val('');
                            $('#<?php echo $controller; ?>_popup_submit_subtton_<?php echo $key; ?>').click();"><span class="icon_closef" style="margin-left:0"></span></a>

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
                        echo $this->Form->input('Enquiry.contact_name', array(
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
<div class="block_dent2 container_same_category" style="overflow: auto;overflow-x: hidden;max-width:1000px; margin: 0 auto; height:430px;" id="list_view_enquirys">
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
            foreach ($arr_enquiry as $value) {
                $i = 1 - $i; $STT += 1;
                ?>
                <tr class="jt_line_<?php if ($i == 1) { ?>black<?php } else { ?>light<?php } ?>" onclick="after_choose_enquiries<?php if (substr($key, 0, 1) == '_') echo $key; ?>('<?php echo $value['_id']; ?>', '', '<?php echo $key; ?>')">

                	<td align="center"><?php if(isset($value['no']))echo $value['no']; ?></td>
					<td align="left"><?php if(isset($value['company']))echo $value['company']; ?></td>
					<td align="left"><?php if(isset($value['contact_name']))echo $value['contact_name']; ?></td>
					<td align="center"><?php if(isset($value['date']))echo $this->Common->format_date( $value['date']->sec, false); ?>
                        <input type="hidden" id="after_choose_enquiries<?php echo $key; ?><?php echo $value['_id']; ?>" value="<?php echo htmlentities(json_encode($value)); ?>">
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