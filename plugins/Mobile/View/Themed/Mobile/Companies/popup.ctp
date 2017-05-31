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
                    <input name="data[Company][name]" class="window_popup_input_<?php echo $controller ?>_<?php echo $key; ?>" data-type="search" placeholder="Filter by: Name" value="" />
                 </section>
            </div>
        </div>
        <div data-role="main" class="ui-content">
            <table data-role="table" data-mode="columntoggle" class="ui_table_customes popup ui-responsive ui-shadow ui-table ui-table-columntoggle table-stroke" id="table_<?php echo $key?>" data-filter="true" data-input=".window_popup_input_<?php echo $controller ?>_<?php echo $key; ?>">
                <thead id="pagination_sort">
                    <tr>
                        <th  data-priority="1"><?php echo __('Company name'); ?><span id="sort_name" rel="name" class="desc"></span></th>
                        <th  data-priority="2"><?php echo __('Customer'); ?><span id="sort_is_customer" rel="is_customer" class="desc"></span></th>
                        <th  data-priority="2"><?php echo __('Supplier'); ?><span id="sort_is_supplier" rel="is_supplier" class="desc"></span></th>
                        <th  data-priority="4"><?php echo __('Company default address'); ?></th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $i = 0; $STT = 0;
                    foreach ($arr_company as $value) {

                        $i = 1 - $i; $STT += 1;
                        ?>
                        <tr onclick="after_choose_companies<?php if (substr($key, 0, 1) == '_') echo $key; ?>('<?php echo $value['_id']; ?>', '<?php echo addslashes($value['name']); ?>', '<?php echo $key; ?>');" >
                            <td align="left" style="width:35%"><?php echo $value['name']; ?></td>
                            <td style="width:5%;text-align:center;">
                                <?php if (isset($value['is_customer']) && $value['is_customer']) echo 'X'; ?>
                            </td>
                            <td style="width:5%;text-align:center;">
                                <?php if (isset($value['is_supplier']) && $value['is_supplier']) echo 'X'; ?>
                            </td>
                            <td style="width:35%">

                                <?php echo $value['addresses'][$value['addresses_default_key']]['address_1'] . ' ' . $value['addresses'][$value['addresses_default_key']]['address_2'] . ' ' . $value['addresses'][$value['addresses_default_key']]['address_3'] . (isset($value['addresses'][$value['addresses_default_key']]['town_city']) ? ', ' . $value['addresses'][$value['addresses_default_key']]['town_city'] : '') . (isset($value['addresses'][$value['addresses_default_key']]['province_state_name']) ? ', ' . $value['addresses'][$value['addresses_default_key']]['province_state_name'] : '') . (isset($value['addresses'][$value['addresses_default_key']]['country_name']) ? ', ' . $value['addresses'][$value['addresses_default_key']]['country_name'] : '' . (isset($value['addresses'][$value['addresses_default_key']]['zip_postcode']) ? ', ' . $value['addresses'][$value['addresses_default_key']]['zip_postcode'] : '')); ?>

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
        <input id="<?php echo $controller; ?>_popup_submit_<?php echo $key; ?>" style="display:none" data-role="none" type="submit" value="Search">

        <!-- Minh dang test -->
        <?php echo $this->element('popup/pagination'); ?>

    <?php echo $this->Form->end(); ?>
</div>
