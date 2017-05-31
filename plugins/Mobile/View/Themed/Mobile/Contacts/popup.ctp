<style type="text/css">
    .popup thead tr{
        width: 100%;
        color: #fff;
        background-color: #d82f2f;
    }
    .popup td{
        height: 25px;
    }
    .filter_box input{
        background-image:none !important;
        position: relative;
        z-index: 10;
        font-size:1em;
        margin:0;
    }
    .filter_item{
        width:100%; /*Edit*/
    }
    .check_cus_item{
        width:20%;
        float:left;
    }
</style>
<div class="ui-content content_popup">
    <form action="<?php echo URL; ?>/mobile/<?php echo $controller ?>/popup/<?php echo $key; ?>" id="<?php echo $controller ?>_popup_form<?php echo $key; ?>" method="post" accept-charset="utf-8" data-ajax="false">
        <div class="filter_box_sea" data-role="header">
            <div class="filter_box">
                <section class="filter_item">
                    <input name="data[Contact][name]" class="window_popup_input_<?php echo $controller ?>_<?php echo $key; ?>" data-type="search" placeholder="Filter by: Name" value="" />
                </section>
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
                <?php
                echo $this->Form->input('Contact.is_employee', array(
                    'type' => 'hidden',
                ));
                ?>
                <?php
                echo $this->Form->input('Contact.company', array(
                    'class' => 'window_popup_input_'.$controller.'_'. $key,
                    'type'  => 'hidden'
                ));
                ?>
            </div>
        </div>
        <div data-role="main" class="ui-content">
            <table data-role="table" data-mode="columntoggle" class="ui_table_customes popup ui-responsive ui-shadow ui-table ui-table-columntoggle table-stroke" id="table_<?php echo $key?>" data-filter="true" data-input=".window_popup_input_<?php echo $controller ?>_<?php echo $key; ?>">
                <thead id="pagination_sort">
                    <tr>
                        <th  data-priority="1"><?php echo __('Contact'); ?><span id="sort_first_name" rel="first_name" class="desc"></span></th>
                        <th  data-priority="2" style="text-align:center;"><?php echo __('Customer'); ?><span id="sort_is_customer" rel="is_customer" class="desc"></span></th>
                        <th  data-priority="2" style="text-align:center;"><?php echo __('Employee'); ?><span id="sort_is_supplier" rel="is_supplier" class="desc"><span id="sort_is_employee" rel="is_employee" class="desc"></th>
                        <th  data-priority="5"><?php echo __('Default address'); ?></th>
                        <th  data-priority="4"><?php echo __('Linked to company'); ?><span id="sort_company" rel="company" class="desc"></span></th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $i = 0; $STT = 0;
                    foreach ($arr_contact as $value) {

                        $i = 1 - $i; $STT += 1;
                        ?>
                        <tr onclick="after_choose_contacts<?php if (substr($key, 0, 1) == '_') echo $key; ?>('<?php echo $value['_id']; ?>', '<?php echo $value['first_name'] . ' ' . $value['last_name']; ?>', '<?php echo $key; ?>')">
                            <td align="left"><?php echo $value['first_name'] . ' ' . $value['last_name']; ?></td>
                            <td style="text-align:center;">
                                <?php if (isset($value['is_customer']) && $value['is_customer']) { ?>x<?php } ?>
                            </td>
                            <td style="text-align:center;">
                                <?php if (isset($value['is_employee']) && $value['is_employee']) { ?>x<?php } ?>
                            </td>
                            <td></td>
                            <td>

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
                            </td>
                        </tr>
                    <?php } ?>

                    <?php if( $STT > 0 && $STT < 10 ){ // chỉ khi nào số lượng nhỏ hơn 10 mới add thêm mà thôi
                        $loop_for = $limit - $STT;
                        for ($j=0; $j < $loop_for; $j++) {
                            $i = 1 - $i;
                          ?>
                        <tr><td></td><td></td><td></td><td></td><td></td></tr>
                    <?php
                        }
                    } ?>
                </tbody>
            </table>
            <?php if( $STT == 0 ){ ?>
            <center style="margin-top:30px">(No data)</center>
            <?php } ?>
        </div>
        <input id="<?php echo $controller; ?>_popup_submit_<?php echo $key; ?>" style="display:none" data-role="none" type="submit" value="Search">

        <!-- Minh dang test -->
        <?php echo $this->element('popup/pagination'); ?>

    <?php echo $this->Form->end(); ?>
</div>
