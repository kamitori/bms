<div id="popup_contacts<?php echo $key; ?>">
    <?php echo $this->Form->create('Contact', array('id' => 'contacts_popup_form' . $key)); ?>
    <div style="float: left;width: 99%;">
        <?php if(!isset($manager_only)){ ?>
        <div class="box_inner_search float_left" style="padding:0px 24px 1px;border-radius:13px;color:#FFF;margin-top:1px;margin-left: 0;">
            <h6 class="float_left fix_label_popup_1">Inactive &nbsp;</h6>
            <input type="hidden" name="data[Contact][inactive]" id="ContactInactive_" value="0">
            <label class="m_check2">
                <?php
                echo $this->Form->input('Contact.inactive', array(
                    'type' => 'checkbox',
                    'onchange' => 'pagination_remove_num_'.$controller.$key.'();$("#contacts_popup_submit_subtton_' . $key . '").click();'
                ));
                ?>
                <span style="margin: 4px 0 0 0 "></span>
            </label>

            <h6 class="float_left fix_label_popup">Customer &nbsp;</h6>
            <input type="hidden" name="data[Contact][is_customer]" id="ContactIsCustomer_" value="0">
            <label class="m_check2">
                <?php
                echo $this->Form->input('Contact.is_customer', array(
                    'type' => 'checkbox',
                    'onchange' => 'pagination_remove_num_'.$controller.$key.'();$("#contacts_popup_submit_subtton_' . $key . '").click();'
                ));
                ?>
                <span style="margin: 4px 0 0 0 "></span>
            </label>

            <h6 class="float_left fix_label_popup">Employee &nbsp;</h6>
            <input type="hidden" name="data[Contact][is_employee]" id="ContactIsEmployee_" value="0">
            <label class="m_check2">
                <?php
                echo $this->Form->input('Contact.is_employee', array(
                    'type' => 'checkbox',
                    'onchange' => 'pagination_remove_num_'.$controller.$key.'();$("#contacts_popup_submit_subtton_' . $key . '").click();'
                ));
                ?>
                <span style="margin: 4px 0 0 0 "></span>
            </label>
        </div>
        <?php } else { ?>
            <input type="hidden" name="manager_only" value="manager_only" />
            <?php
                echo $this->Form->input('Contact.is_employee', array(
                    'type' => 'hidden',
                ));
            ?>
            <?php
                echo $this->Form->input('Contact.company', array(
                    'type' => 'hidden',
                ));
            ?>
            <?php
                echo $this->Form->input('Contact.inactive', array(
                    'type' => 'hidden',
                ));
            ?>
            <?php
                echo $this->Form->input('Contact.is_customer', array(
                    'type' => 'hidden',
                ));
            ?>
        <?php } ?>
        <div class="float_left" style="margin-left: 270px;">
            <h6 class="float_left" style="margin-top:2px"><?php echo translate('Filter by') . ': ' . translate('Contact name'); ?> &nbsp;</h6>
            <span class="float_left" style="margin-right: 8px;">
                <span class="block_sear  block1">
                    <span class="bg_search_1"></span>
                    <span class="bg_search_2"></span>
                    <div class="box_inner_search float_left">
                        <a href="javascript:void(0)" onclick="$('#contacts_popup_submit_subtton_<?php echo $key; ?>').click();"><span class="icon_search"></span></a>
                        <div class="styled_select2 float_left">
                            <?php
                            echo $this->Form->input('Contact.name', array(
                                'id' => 'window_popup_contacts_ContactName_' . $key,
                                'onkeypress' => 'pagination_remove_num_'.$controller.$key.'();',
                                'style' => 'background: #636363;color: #fff;margin-left: -2px;margin-top:-2px;'
                            ));
                            ?>
                        </div>
                        <a href="javascript:void(0)" onclick="$('#window_popup_contacts_ContactName_<?php echo $key; ?>').val('');
                                $('#contacts_popup_submit_subtton_<?php echo $key; ?>').click();"><span class="icon_closef" style="margin-left:0"></span></a>

                    </div>
                </span>
            </span>
        </div>

        <?php if(!isset($manager_only)){ ?>
        <div class="float_left">
            <h6 class="float_left" style="margin-top:2px"><?php echo translate('Company'); ?> &nbsp;</h6>
            <span class="float_left">
                <span class="block_sear  block1">
                    <span class="bg_search_1"></span>
                    <span class="bg_search_2"></span>
                    <div class="box_inner_search float_left">
                        <a href="javascript:void(0)" onclick="$('#contacts_popup_submit_subtton_<?php echo $key; ?>').click();"><span class="icon_search"></span></a>
                        <div class="styled_select2 float_left">
                            <?php
                            echo $this->Form->input('Contact.company', array(
                                'id' => 'window_popup_contacts_ContactCompany_' . $key,
                                'style' => 'background: #636363;color: #fff;margin-left: -2px;margin-top:-2px;'
                            ));
                            ?>
                        </div>
                        <a href="javascript:void(0)" onclick="$('#window_popup_contacts_ContactCompany_<?php echo $key; ?>').val('');
                                $('#contacts_popup_submit_subtton_<?php echo $key; ?>').click();"><span class="icon_closef" style="margin-left:0"></span></a>

                    </div>
                </span>
            </span>
        </div>

        <?php } ?>
        <?php if( $key == '_tab_tasks' ){ ?>
        <div class="float_right">
            <input title="Switch to choose asset" onclick="$('#window_popup_equipments_tab_tasks').data('kendoWindow').open().center();" type="button" class="input_switch_popup btn_pur" value="Assets">
        </div>
        <?php } ?>

        <?php
    // Ẩn nút submit này
        echo $this->Js->submit('Search', array(
            'id' => 'contacts_popup_submit_subtton_' . $key,
            'style' => 'height:1px; width:1px;opacity:0.1',
            'success' => '$("#window_popup_contacts' . $key . '").html(data);'
        ));
        ?>

    </div>
    <!-- END SEARCH POPUP -->

    <div style="clear:both;height:6px"></div>

    <div class="block_dent2" style="overflow: auto;overflow-x: hidden;max-width:1000px; margin: 0 auto; height:430px; display:none" id="list_view_contacts">

    </div>
    <div class="block_dent2 container_same_category" style="overflow: auto;overflow-x: hidden;max-width:1000px; margin: 0 auto; height:430px;" id="list_view_contacts">
        <table class="jt_tb" id="Form_add" style="font-size:12px;">
            <thead style="position: fixed;" id="pagination_sort">
                <tr>
                    <th style="width: 181px"><?php echo translate('Contact'); ?><span id="sort_first_name" rel="first_name" class="desc"></span></th>
                    <th style="width: 86px"><?php echo translate('Customer'); ?><span id="sort_is_customer" rel="is_customer" class="desc"></span></th>
                    <th style="width: 86px"><?php echo translate('Employee'); ?><span id="sort_is_employee" rel="is_employee" class="desc"></span></th>
                    <th style="width: 135px"><?php echo translate('Default address'); ?></th>
                    <th style="width: 292px"><?php echo translate('Linked to company'); ?><span id="sort_company" rel="company" class="desc"></span></th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <th style="width: 181px">&nspb;</th>
                    <th style="width: 86px"></th>
                    <th style="width: 86px"></th>
                    <th style="width: 135px"></th>
                    <th style="width: 292px"></th>
                </tr>
                <?php
                $i = 0; $STT = 0;
                foreach ($arr_contact as $value) {
                    $i = 1 - $i; $STT += 1;
                    ?>
                    <tr class="jt_line_<?php if ($i == 1) { ?>black<?php } else { ?>light<?php } ?>" onclick='after_choose_contacts<?php if (substr($key, 0, 1) == '_') echo $key; ?>("<?php echo $value['_id']; ?>", "<?php echo $value['first_name'] . ' ' . htmlentities($value['last_name'], ENT_QUOTES); ?>", "<?php echo $key; ?>")'>
                        <td align="left" style="width: 181px"><?php echo $value['first_name'] . ' ' . $value['last_name']; ?></td>
                        <td align="center" style="width: 86px">
                            <?php if (isset($value['is_customer']) && $value['is_customer']) { ?>x<?php } ?>
                        </td>
                        <td align="center" style="width: 86px"><?php if (isset($value['is_employee']) && $value['is_employee']) { ?>x<?php } ?></td>
                        <td style="width: 135px"></td>
                        <td style="width: 292px">
                            <?php
                                if (is_object($value['company_id'])) {
                                    if(!isset($arr_company_tmp))$arr_company_tmp = array();
                                    if( !isset($arr_company_tmp[(string)$value['company_id']]) ){
                                        $arr_company = $model_company->select_one(array('_id' => new MongoId($value['company_id'])), array('_id', 'name'));
                                        if(isset($arr_company['name'])){
                                            $arr_company_tmp[(string)$value['company_id']] = $arr_company['name'];
                                            echo $arr_company['name'];
                                        }
                                    }else{
                                        echo $arr_company_tmp[(string)$value['company_id']];

                                    }
                                }
                            ?>

                            <input type="hidden" id="after_choose_contacts<?php echo $key; ?><?php echo $value['_id']; ?>" value="<?php echo htmlentities(json_encode($value)); ?>">

                            <!-- INPUT HIDDEN THEM VAO TUY Y DE SU DUNG, cau truc thong nhat: id="<id cua window>_<fieldname>" -->
                            <?php if(false){ ?>
                            <input type="hidden" id="window_popup_contact_email_<?php echo $value['_id']; ?>" value="<?php if (isset($value['email'])) echo $value['email']; ?>">
                            <input type="hidden" id="window_popup_contact_direct_dial_<?php echo $value['_id']; ?>" value="<?php if (isset($value['direct_dial'])) echo $value['direct_dial']; ?>">
                            <input type="hidden" id="window_popup_contact_mobile_<?php echo $value['_id']; ?>" value="<?php if (isset($value['mobile'])) echo $value['mobile']; ?>">
                            <input type="hidden" id="window_popup_contact_home_phone_<?php echo $value['_id']; ?>" value="<?php if (isset($value['home_phone'])) echo $value['home_phone']; ?>">
                            <input type="hidden" id="window_popup_contact_position_<?php echo $value['_id']; ?>" value="<?php if (isset($value['position'])) echo $value['position']; ?>">
                            <input type="hidden" id="window_popup_contact_position_id_<?php echo $value['_id']; ?>" value="<?php if (isset($value['position_id'])) echo $value['position_id']; ?>">
                            <input type="hidden" id="window_popup_contact_department_<?php echo $value['_id']; ?>" value="<?php if (isset($value['department'])) echo $value['department']; ?>">
                            <input type="hidden" id="window_popup_contact_department_id_<?php echo $value['_id']; ?>" value="<?php if (isset($value['department_id'])) echo $value['department_id']; ?>">

                            <!-- =========================== ADDRESS =========================== -->
                            <input type="hidden" id="window_popup_contact_address_1_<?php echo $key; ?><?php echo $value['_id']; ?>" value="<?php echo $value['addresses'][$value['addresses_default_key']]['address_1']; ?>">

                            <input type="hidden" id="window_popup_contact_address_2_<?php echo $key; ?><?php echo $value['_id']; ?>" value="<?php if (isset($value['addresses'][$value['addresses_default_key']]['address_2'])) echo $value['addresses'][$value['addresses_default_key']]['address_2']; ?>">

                            <input type="hidden" id="window_popup_contact_address_3_<?php echo $key; ?><?php echo $value['_id']; ?>" value="<?php if (isset($value['addresses'][$value['addresses_default_key']]['address_3'])) echo $value['addresses'][$value['addresses_default_key']]['address_3']; ?>">

                            <input type="hidden" id="window_popup_contact_town_city_<?php echo $key; ?><?php echo $value['_id']; ?>" value="<?php if (isset($value['addresses'][$value['addresses_default_key']]['town_city'])) echo $value['addresses'][$value['addresses_default_key']]['town_city']; ?>">

                            <input type="hidden" id="window_popup_contact_province_state_<?php echo $key; ?><?php echo $value['_id']; ?>" value="<?php if (isset($value['addresses'][$value['addresses_default_key']]['province_state'])) echo $value['addresses'][$value['addresses_default_key']]['province_state']; ?>">

                            <input type="hidden" id="window_popup_contact_province_state_id_<?php echo $key; ?><?php echo $value['_id']; ?>" value="<?php if (isset($value['addresses'][$value['addresses_default_key']]['province_state_id'])) echo $value['addresses'][$value['addresses_default_key']]['province_state_id']; ?>">

                            <input type="hidden" id="window_popup_contact_zip_postcode_<?php echo $key; ?><?php echo $value['_id']; ?>" value="<?php if (isset($value['addresses'][$value['addresses_default_key']]['zip_postcode'])) echo $value['addresses'][$value['addresses_default_key']]['zip_postcode']; ?>">

                            <input type="hidden" id="window_popup_contact_country_<?php echo $key; ?><?php echo $value['_id']; ?>" value="<?php if (isset($value['addresses'][$value['addresses_default_key']]['country'])) echo $value['addresses'][$value['addresses_default_key']]['country']; ?>">

                            <input type="hidden" id="window_popup_contact_country_id_<?php echo $key; ?><?php echo $value['_id']; ?>" value="<?php if (isset($value['addresses'][$value['addresses_default_key']]['country_id'])) echo $value['addresses'][$value['addresses_default_key']]['country_id']; ?>">
                            <?php } ?>
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

</div>
<script type="text/javascript">
function getCheckedInput(){
    var obj = Object();
    $("input[type=checkbox]:checked","#contacts_popup_form<?php echo $key ?>").each(function(){
        var name = $(this).attr("name");
        obj[name] = 1;
    });
    return obj;
}
$(function(){
        $("#window_popup_contacts_ContactName_<?php echo $key ?>").kendoAutoComplete({
            minLength: 3,
            dataTextField: "full_name",
            dataSource: new kendo.data.DataSource({
                transport: {
                    read:{
                        dataType: "json",
                        url: "<?php echo URL.'/'.$controller.'/autocomplete/'; ?>",
                        type:"POST",
                        data: {
                           data: function(){
                                var obj = getCheckedInput();
                                obj.full_name = $("#window_popup_contacts_ContactName_<?php echo $key ?>").val();
                                return JSON.stringify(obj);
                           },
                        },
                        parameterMap: function(options, operation) {
                            return {
                                StartsWith: options.filter.filters[0].value
                            }
                        }
                    }
                },
                schema: {
                   data: "data"
                },
                serverFiltering: true
            }),
        });
    });
</script>