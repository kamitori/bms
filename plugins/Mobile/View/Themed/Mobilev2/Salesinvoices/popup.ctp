<form action="<?php echo URL; ?>/mobile/<?php echo $controller ?>/popup/<?php echo $key; ?>" id="<?php echo $controller ?>_popup_form<?php echo $key; ?>" method="post" accept-charset="utf-8">
    <div class="filter-box-header" data-role="header" data-position="fixed">
        <div style="width: 20%; float: left">
            <a id="prev-pagination" data-role="button" class="ui-btn ui-shadow ui-corner-all" title="Back" onclick='$.mobile.changePage($("#main-page").parent());'  href="javascript:void(0)">BACK</a>
        </div>
        <input name="data[Salesinvoice][name]" class="window_popup_input_<?php echo $controller ?>_<?php echo $key; ?>" data-type="search" placeholder="Filter by: Name" value="" />
    </div>

    <?php
    $i = 0; $STT = 0;
    foreach ($arr_salesinvoices as $value) {
        $i = 1 - $i; $STT += 1;
        ?>
        <li id="list-<?php echo $value['_id'] ?>" class="ui-li-static ui-body-inherit"  data-role="collapsible" data-role="collapsible" data-collapsed-icon="arrow-d" data-expanded-icon="arrow-u" data-iconpos="right" data-inset="false">
            <h2>
                <div onclick="after_choose_salesinvoices<?php if (substr($key, 0, 1) == '_') echo $key; ?>('<?php echo $value['_id']; ?>', '<?php echo isset($value['name'])?addslashes($value['name']):''; ?>', '<?php echo $key; ?>', '<?php echo isset($value['code'])?$value['code']:''; ?>'  );" class="ui-block-a" style="width:40%">
                    <a class="link-to-entry" href="<?php echo URL; ?>/mobile/salesinvoices/entry/<?php echo $value['_id']; ?>"><?php echo $value['code']; ?></a>
                </div>
                <div class="ui-block-b" style="width:60%"><?php echo isset($value['name'])?$value['name']:'' ;?></div>
            </h2>

            <ul data-role="listview" data-theme="b">
                 <li>
                    <div class="ui-block-a" style="width: 40%"><b>Company name</b></div>
                    <div class="ui-block-b" style="width:60%">
                            <?php if (isset($value['company_id']) && is_object($value['company_id']) ) { ?>
                            <a data-ajax="false" href="<?php echo URL; ?>/mobile/companies/entry/<?php echo $value['company_id']; ?>">
                            <?php echo $value['company_name']; ?>
                            </a>
                        <?php } ?>
                    </div>
                </li>
                <li>
                    <div class="ui-block-a" style="width: 40%"><b>Contact name</b></div>
                    <div class="ui-block-b" style="width:60%">
                        <?php echo isset($value['contact_name'])?$value['contact_name']:'';?>
                    </div>
                </li>
                <li>
                    <div class="ui-block-a" style="width:40%"><b>Date</b></div>
                    <div class="ui-block-b" style="width:60%"><?php echo $this->Common->format_date($value['invoice_date']->sec); ?></div>
                </li>
            </ul>

        </li>
    <?php } ?>

    <?php if( $STT == 0 ){ ?>
    <center style="margin-top:30px">(No data)</center>
    <?php } ?>
<?php echo $this->element('pagination'); ?>
<?php echo $this->Form->end(); ?>
