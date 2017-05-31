<?php echo $this->Form->create('Company', array('id' => 'companies_popup_form' . $key)); ?>

<div style="float: left; margin-right: 3%;width: 60%;position:relative">
    <div class="float_left">
        <h6 class="float_left" style="margin-top:2px"><?php echo translate('Filter by') . ': ' . translate('Name'); ?> &nbsp;</h6>
        <span class="float_left" style="margin-right:45px">
            <span class="block_sear  block1">
                <span class="bg_search_1"></span>
                <span class="bg_search_2"></span>
                <div class="box_inner_search float_left">
                    <a href="javascript:void(0)" onclick="$('#<?php echo $controller; ?>_num_<?php echo $key; ?>').val(1);$('#companies_popup_submit_subtton_<?php echo $key; ?>').click();"><span class="icon_search"></span></a>
                    <div class="styled_select2 float_left">
                        <?php
                        echo $this->Form->input('Company.name', array(
                            'id' => 'window_popup_companies_CompanyName_' . $key,
                            'style' => 'background: #636363;color: #fff;margin-left: -2px;margin-top:-2px;',
                            'onkeypress' => 'pagination_remove_num_'.$controller.$key.'();',
                        ));
                        ?>
                    </div>
                    <a href="javascript:void(0)" onclick="$('#<?php echo $controller; ?>_num_<?php echo $key; ?>').val(1);$('#window_popup_companies_CompanyName_<?php echo $key; ?>').val('');
                            $('#companies_popup_submit_subtton_<?php echo $key; ?>').click();"><span class="icon_closef" style="margin-left:0"></span></a>

                </div>
            </span>
        </span>
    </div>

    <?php if( !isset($is_shipper) ){ ?>
    <div class="box_inner_search float_left" style="margin-left: 227px;padding:0px 24px 1px;border-radius:13px;color:#FFF;margin-top:1px;">

        <h6 class="float_left" style="margin-top:2px">Inactive &nbsp;</h6>
        <input type="hidden" name="data[Company][inactive]" id="CompanyInactive_" value="0">
        <label class="m_check2">
            <?php
            echo $this->Form->input('Company.inactive', array(
                'type' => 'checkbox',
                'onchange' => 'pagination_remove_num_'.$controller.$key.'();'.'$("#companies_popup_submit_subtton_' . $key . '").click();'
            ));
            ?>
            <span style="margin: 4px 0 0 0 "></span>
        </label>

        <h6 class="float_left" style="margin-top:2px;margin-left: 16px;">Customer &nbsp;</h6>
        <input type="hidden" name="data[Company][is_customer]" id="CompanyIsCustomer_" value="0">
        <label class="m_check2">
            <?php
            echo $this->Form->input('Company.is_customer', array(
                'type' => 'checkbox',
                'onchange' => 'pagination_remove_num_'.$controller.$key.'();'.'$("#companies_popup_submit_subtton_' . $key . '").click();'
            ));
            ?>
            <span style="margin: 4px 0 0 0 "></span>
        </label>

        <h6 class="float_left" style="margin-top:2px;margin-left: 16px;">Supplier &nbsp;</h6>
        <input type="hidden" name="data[Company][is_supplier]" id="CompanyIsSupplier_" value="0">
        <label class="m_check2">
            <?php
            echo $this->Form->input('Company.is_supplier', array(
                'type' => 'checkbox',
                'onchange' => 'pagination_remove_num_'.$controller.$key.'();'.'$("#companies_popup_submit_subtton_' . $key . '").click();'
            ));
            ?>
            <span style="margin: 4px 0 0 0 "></span>
        </label>
    </div>
    <?php }else{ ?>
    <input type="hidden" name="data[Company][is_shipper]" value="1">
    <?php } ?>

    <?php
    // Ẩn nút submit này
    echo $this->Js->submit('Search', array(
        'id' => 'companies_popup_submit_subtton_' . $key,
        'style' => 'height:1px; width:1px;opacity:0.1',
        'success' => '$("#window_popup_companies' . $key . '").html(data);window_popup_extra("#window_popup_companies' . $key . '");'
    ));
    ?>
</div>

<div style="clear:both;height:6px"></div>

<div class="block_dent2 container_same_category" style="overflow: auto;overflow-x: hidden;max-width:1000px; margin: 0 auto; height:430px;" id="list_view_company">
    <table class="jt_tb" id="Form_add" style="font-size:12px; ">
        <thead style="position: fixed;" id="pagination_sort">
            <tr>
                <th style="width:299px"><?php echo translate('Company name'); ?><span id="sort_name" rel="name" class="desc"></span></th>
                <th style="width:77px; "><?php echo translate('Customer'); ?><span id="sort_is_customer" rel="is_customer" class="desc"></span></th>
                <th style="width:77px;"><?php echo translate('Supplier'); ?><span id="sort_is_supplier" rel="is_supplier" class="desc"></span></th>
                <th style="width:360px;"><?php echo translate('Company default address'); ?></th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <th style="width:299px">&nspb;</th>
                <th style="width:77px; "></th>
                <th style="width:77px;"></th>
                <th style="width:360px;"></th>
            </tr>
            <?php
            $i = 0; $STT = 0;
            $address = array('address_1','address_2','address_3','town_city','province_state','country','zip_postcode');
            foreach ($arr_company as $value) {

                $i = 1 - $i; $STT += 1;
                ?>
                <tr class="jt_line_<?php if ($i == 1) { ?>black<?php } else { ?>light<?php } ?>" onclick="after_choose_companies<?php if (substr($key, 0, 1) == '_') echo $key; ?>('<?php echo $value['_id']; ?>', '<?php echo addslashes($value['name']); ?>', '<?php echo $key; ?>');">
                    <td align="left" style="width:300px"><?php echo $value['name']; ?></td>
                    <td align="center" style="width:77px;">
                        <?php if (isset($value['is_customer']) && $value['is_customer']) echo 'X'; ?>
                    </td>
                    <td align="center" style="width:77px;">
                        <?php if (isset($value['is_supplier']) && $value['is_supplier']) echo 'X'; ?>
                    </td>
                    <td style="width:360px;">
                        <?php if(!isset($value['addresses_default_key'])) $value['addresses_default_key'] = 0; ?>
                        <?php echo (isset($value['addresses'][$value['addresses_default_key']]['address_1']) ? $value['addresses'][$value['addresses_default_key']]['address_1'] : '') . ' ' . (isset($value['addresses'][$value['addresses_default_key']]['address_2']) ? $value['addresses'][$value['addresses_default_key']]['address_2'] : '') . ' ' . (isset($value['addresses'][$value['addresses_default_key']]['address_3']) ? $value['addresses'][$value['addresses_default_key']]['address_3'] : '') . (isset($value['addresses'][$value['addresses_default_key']]['town_city']) ? ', ' . $value['addresses'][$value['addresses_default_key']]['town_city'] : '') . (isset($value['addresses'][$value['addresses_default_key']]['province_state_name']) ? ', ' . $value['addresses'][$value['addresses_default_key']]['province_state_name'] : '') . (isset($value['addresses'][$value['addresses_default_key']]['country_name']) ? ', ' . $value['addresses'][$value['addresses_default_key']]['country_name'] : '' . (isset($value['addresses'][$value['addresses_default_key']]['zip_postcode']) ? ', ' . $value['addresses'][$value['addresses_default_key']]['zip_postcode'] : '')); ?>

                        <input type="hidden" id="after_choose_companies<?php echo $key; ?><?php echo $value['_id']; ?>" value="<?php echo htmlentities(json_encode($value)); ?>">
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