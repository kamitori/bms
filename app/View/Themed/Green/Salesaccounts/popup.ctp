<?php echo $this->Form->create('Salesaccount', array('id' => 'salesaccount_popup_form' . $key)); ?>
<div style="margin-right: 3%;width: 60%;position:relative">
    <div class="float_left">
        <h6 class="float_left" style="margin-top:2px"><?php echo translate('Filter by') . ': ' . translate('Name'); ?> &nbsp;</h6>
        <span class="float_left" style="margin-right:45px">
            <span class="block_sear  block1">
                <span class="bg_search_1"></span>
                <span class="bg_search_2"></span>
                <div class="box_inner_search float_left">
                    <a href="javascript:void(0)" onclick="$('#<?php echo $controller; ?>_num_<?php echo $key; ?>').val(1);$('#salesaccounts_popup_submit_subtton_<?php echo $key; ?>').click();"><span class="icon_search"></span></a>
                    <div class="styled_select2 float_left">
                        <?php
                        echo $this->Form->input('Salesaccount.name', array(
                            'id' => 'window_popup_salesaccounts_SalesaccountName_' . $key,
                            'style' => 'background: #636363;color: #fff;margin-left: -2px;margin-top:-2px;',
                            'onkeypress' => 'pagination_remove_num_'.$controller.$key.'();',
                        ));
                        ?>
                    </div>
                    <a href="javascript:void(0)" onclick="$('#<?php echo $controller; ?>_num_<?php echo $key; ?>').val(1);$('#window_popup_salesaccounts_SalesaccountName_<?php echo $key; ?>').val('');
                            $('#salesaccounts_popup_submit_subtton_<?php echo $key; ?>').click();"><span class="icon_closef" style="margin-left:0"></span></a>

                </div>
            </span>
        </span>
    </div>
    <?php
        // Ẩn nút submit này
        echo $this->Js->submit('Search', array(
            'id' => 'salesaccounts_popup_submit_subtton_' . $key,
            'style' => 'height:1px; width:1px;opacity:0.1',
            'success' => '$("#window_popup_salesaccounts' . $key . '").html(data);window_popup_extra("#window_popup_salesaccounts' . $key . '");'
        ));
    ?>
</div>

<div style="clear:both;height:6px"></div>

<div class="block_dent2 container_same_category" style="overflow: auto;overflow-x: hidden;max-width:1000px; margin: 0 auto; height:430px;" id="list_view_salesaccount">
    <table class="jt_tb" id="Form_add" style="font-size:12px; ">
        <thead style="position: fixed;" id="pagination_sort">
            <tr>
                <th style="width:299px"><?php echo translate('Name'); ?></th>
                <th style="width:500px"><?php echo translate('Address'); ?></th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <th>&nspb;</th>
                <th></th>
            </tr>
            <?php
            $i = 0; $STT = 0;
            foreach ($arr_salesaccount as $value) {

                $i = 1 - $i; $STT += 1;
                ?>

                <?php
                    $name = '';
                    // nếu SA là company
                    if (isset($value['company_id']) && is_object($value['company_id'])){
                        $arr_company = $model_company->select_one(array('_id' => $value['company_id']), array('_id', 'name', 'addresses_default_key', 'addresses'));
                        if(!isset($arr_company['addresses_default_key']))
                            $arr_company['addresses_default_key'] = 0;
                        if(isset($arr_company['name'])){
                            $arr_company_tmp[(string)$value['company_id']] = $arr_company['name'];
                            $name = $arr_company['name'];
                            $address = $arr_company['addresses'][$arr_company['addresses_default_key']];
                        }

                    // nếu SA là contact
                    }
                ?>

                <tr class="jt_line_<?php if ($i == 1) { ?>black<?php } else { ?>light<?php } ?>" onclick="after_choose_salesaccounts<?php if (substr($key, 0, 1) == '_') echo $key; ?>('company', '<?php echo $value['_id']; ?>', '<?php echo addslashes($name); ?>', '<?php echo $key; ?>');">
                    <td align="left" style="width:300px">
                        <?php echo $name; ?>
                        <input type="hidden" id="after_choose_salesaccounts<?php echo $key; ?><?php echo $value['_id']; ?>" value="<?php echo htmlentities(json_encode($value)); ?>">
                    </td>
                    <td style="width:500px;">&nbsp;
                        <?php echo $address['address_1'] . ' ' . $address['address_2'] . ' ' . $address['address_3'] . (isset($address['town_city']) ? ', ' . $address['town_city'] : '') . (isset($address['province_state_name']) ? ', ' . $address['province_state_name'] : '') . (isset($address['country_name']) ? ', ' . $address['country_name'] : '' . (isset($address['zip_postcode']) ? ', ' . $address['zip_postcode'] : '')); ?>

                        <input type="hidden" id="after_choose_companies<?php echo $key; ?><?php echo $value['_id']; ?>" value="<?php echo htmlentities(json_encode($value)); ?>">
                    </td>
                </tr>
            <?php } ?>

            <?php if( $STT > 0 && $STT < 10 ){ // chỉ khi nào số lượng nhỏ hơn 10 mới add thêm mà thôi
                $loop_for = $limit - $STT;
                for ($j=0; $j < $loop_for; $j++) {
                    $i = 1 - $i;
                  ?>
                <tr class="jt_line_<?php if ($i == 1) { ?>black<?php } else { ?>light<?php } ?>"><td>&nbsp;</td><td></td><td>&nbsp;</td><td></td></tr>
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