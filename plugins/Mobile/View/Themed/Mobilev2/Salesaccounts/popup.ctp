<form action="<?php echo URL; ?>/mobile/<?php echo $controller ?>/popup/<?php echo $key; ?>" id="<?php echo $controller ?>_popup_form<?php echo $key; ?>" method="post" accept-charset="utf-8">
    <div class="filter-box-header" data-role="header" data-position="fixed">
        <div style="width: 20%; float: left">
            <a id="prev-pagination" data-role="button" class="ui-btn ui-shadow ui-corner-all" title="Back" onclick='$.mobile.changePage($("#main-page").parent());'  href="javascript:void(0)">BACK</a>
        </div>
        <input name="data[salesaccount][name]" class="window_popup_input_<?php echo $controller ?>_<?php echo $key; ?>" data-type="search" placeholder="Filter by: Name" value="" />
    </div>
    
    <?php
    $i = 0; $STT = 0;
    foreach ($arr_salesaccount as $value) {
        $i = 1 - $i; $STT += 1;
        ?>
        <?php
            $name = '';
            $address = '';
            // nếu SA là company
            if (isset($value['company_id']) && is_object($value['company_id'])){
                $arr_company = $model_company->select_one(array('_id' => $value['company_id']), array('_id', 'name', 'addresses_default_key', 'addresses'));
                if(!isset($arr_company['addresses_default_key']))
                    $arr_company['addresses_default_key'] = 0;
                if(isset($arr_company['name'])){
                    $arr_company_tmp[(string)$value['company_id']] = $arr_company['name'];
                    $name = $arr_company['name'];
                    $address = $arr_company['addresses'][$arr_company['addresses_default_key']];
                    $address = $address['address_1'].' '.$address['address_2'].' '.$address['address_3'].', '.$address['town_city'].', '.$address['zip_postcode'];
                }
            // nếu SA là contact
            }
        ?>
        <li id="list-<?php echo $value['_id'] ?>" class="ui-li-static ui-body-inherit"  data-role="collapsible" data-role="collapsible" data-collapsed-icon="arrow-d" data-expanded-icon="arrow-u" data-iconpos="right" data-inset="false">
            <h2>
                <div onclick="after_choose_salesaccounts<?php if (substr($key, 0, 1) == '_') echo $key; ?>('<?php echo $value['_id']; ?>', '<?php echo addslashes($name); ?>', '<?php echo $key; ?>', '<?php echo isset($value['code'])?$value['code']:''; ?>'  );" class="ui-block-a" style="width:40%">
                    <a class="link-to-entry" href="<?php echo URL; ?>/mobile/salesaccounts/entry/<?php echo $value['_id']; ?>"><?php echo $value['no']; ?></a>
                </div>
                <div class="ui-block-b" style="width:60%"><?php echo isset($name)?$name:'' ;?></div>
            </h2>


            <ul data-role="listview" data-theme="b">
                <li>
                    <div class="ui-block-a" style="width: 40%"><b>Name</b></div>
                    <div class="ui-block-b" style="width:60%">
                        <?php echo isset($name)?$name:''; ?>
                    </div>
                </li>
                <li>
                    <div class="ui-block-a" style="width: 40%"><b>Address</b></div>
                    <div class="ui-block-b" style="width:60%">
                        <?php echo isset($address)?$address:'';?>
                    </div>
                </li>
            </ul>

        </li>
    <?php } ?>

    <?php if( $STT == 0 ){ ?>
    <center style="margin-top:30px">(No data)</center>
    <?php } ?>
<?php echo $this->element('pagination'); ?>
<?php echo $this->Form->end(); ?>
