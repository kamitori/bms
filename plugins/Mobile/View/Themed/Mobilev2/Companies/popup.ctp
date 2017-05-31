<form action="<?php echo URL; ?>/mobile/<?php echo $controller ?>/popup/<?php echo $key; ?>" id="<?php echo $controller ?>_popup_form<?php echo $key; ?>" method="post" accept-charset="utf-8">
    <div>
        <div style="width: 100%" class="filter-box-header" data-role="header" data-position="fixed">
            <div style="width: 22%; float: left">
                 <a id="prev-pagination" data-role="button" class="ui-btn ui-shadow ui-corner-all" title="Back" onclick='$.mobile.changePage($("#main-page").parent());'  href="javascript:void(0)">BACK</a>
            </div>
            <input name="data[Company][name]" class="window_popup_input_<?php echo $controller ?>_<?php echo $key; ?>" data-type="search" placeholder="Filter by: Name" value="" />
        </div>

        <ul data-role="listview" >
            <?php
            $i = 0; $STT = 0;
            foreach ($arr_company as $value) {
                $i = 1 - $i; $STT += 1;
                ?>
                <li id="list-<?php echo $value['_id'] ?>"  data-role="collapsible" data-role="collapsible" data-collapsed-icon="arrow-d" data-expanded-icon="arrow-u" data-iconpos="right" data-inset="false">

                    <h2>
                        <div onclick="after_choose_companies<?php if (substr($key, 0, 1) == '_') echo $key; ?>('<?php echo $value['_id']; ?>', '<?php echo addslashes($value['name']); ?>', '<?php echo $key; ?>');" class="ui-block-a" style="width:40%">
                            <a class="link-to-entry" data-ajax="false" href="<?php echo URL; ?>/mobile/companies/entry/<?php echo $value['_id']; ?>"><?php echo $value['no']; ?></a>
                        </div>
                        <div class="ui-block-b" style="width:40%"><?php echo $value['name']; ?></div>
                    </h2>

                    <ul data-role="listview" data-theme="b">
                        <li>
                            <div class="ui-block-a" style="width:40%"><b>Company name</b></div>
                            <div class="ui-block-b" style="width:60%">
                                <?php if (isset($value['name']) ) { ?>
                                <?php echo $value['name']; ?>
                                <?php } ?>
                            </div>
                        </li>

                        <li>
                            <div class="ui-block-a" style="width:40%"><b>Customer</b></div>
                            <div class="ui-block-b" style="width:60%">
                                <?php if (isset($value['is_customer']) && $value['is_customer']) { ?>
                                <?php echo 'X'; ?>
                                <?php } ?>
                            </div>
                        </li>

                        <li>
                            <div class="ui-block-a" style="width:40%"><b>Supplier</b></div>
                            <div class="ui-block-b" style="width:60%">
                                <?php if (isset($value['is_supplier']) && $value['is_supplier']) { ?>
                                <?php echo 'X'; ?>
                                <?php } ?>
                            </div>
                        </li>

                        <li>
                            <div class="ui-block-a" style="width:40%"><b>Company default address</b></div>
                            <div class="ui-block-b" style="width:60%">
                                <?php echo $value['addresses'][$value['addresses_default_key']]['address_1'] . ' ' . $value['addresses'][$value['addresses_default_key']]['address_2'] . ' ' . $value['addresses'][$value['addresses_default_key']]['address_3'] . (isset($value['addresses'][$value['addresses_default_key']]['town_city']) ? ', ' . $value['addresses'][$value['addresses_default_key']]['town_city'] : '') . (isset($value['addresses'][$value['addresses_default_key']]['province_state_name']) ? ', ' . $value['addresses'][$value['addresses_default_key']]['province_state_name'] : '') . (isset($value['addresses'][$value['addresses_default_key']]['country_name']) ? ', ' . $value['addresses'][$value['addresses_default_key']]['country_name'] : '' . (isset($value['addresses'][$value['addresses_default_key']]['zip_postcode']) ? ', ' . $value['addresses'][$value['addresses_default_key']]['zip_postcode'] : '')); ?>
                                <input type="hidden" id="after_choose_companies<?php echo $key; ?><?php echo $value['_id']; ?>" value="<?php echo htmlentities(json_encode($value)); ?>">

                            </div>
                        </li>

                    </ul>

                </li>
            <?php } ?>
        </ul>

  
    <?php if( $STT == 0 ){ ?>
    <center style="margin-top:30px">(No data)</center>
    <?php } ?>
<?php echo $this->element('pagination'); ?>
<?php echo $this->Form->end(); ?>
