<form action="<?php echo URL; ?>/mobile/<?php echo $controller ?>/popup/<?php echo $key; ?>" id="<?php echo $controller ?>_popup_form<?php echo $key; ?>" method="post" accept-charset="utf-8">
    <div class="filter-box-header" data-role="header" data-position="fixed">
        <div style="width: 20%; float: left">
            <a id="prev-pagination" data-role="button" class="ui-btn ui-shadow ui-corner-all" title="Back" onclick='$.mobile.changePage($("#main-page").parent());'  href="javascript:void(0)">BACK</a>
        </div>
        <input name="data[Product][name]" class="window_popup_input_<?php echo $controller ?>_<?php echo $key; ?>" data-type="search" placeholder="Filter by: Name" value="" />
    </div>

    <?php
    $i = 0; $STT = 0;
    foreach ($arr_products as $value) {
        $i = 1 - $i; $STT += 1;
        ?>
        <li id="list-<?php echo $value['_id'] ?>" class="ui-li-static ui-body-inherit"  data-role="collapsible" data-role="collapsible" data-collapsed-icon="arrow-d" data-expanded-icon="arrow-u" data-iconpos="right" data-inset="false">
            <h2>
                <div onclick="after_choose_products<?php if (substr($key, 0, 1) == '_') echo $key; ?>('<?php echo $value['_id']; ?>', '<?php echo htmlentities(addslashes($value['name'])); ?>', '<?php echo $key; ?>');" class="ui-block-a" style="width:40%">
                    <a class="link-to-entry" href="<?php echo URL; ?>/mobile/products/entry/<?php echo $value['_id']; ?>"><?php echo $value['code'].'-'. $value['sku']; ?></a>
                </div>
                <div class="ui-block-b" style="width:60%"><?php echo $value['name'];?></div>
            </h2>
            <ul data-role="listview" data-theme="b">
                <li>
                    <div class="ui-block-a" style="width: 40%"><b>Code</b></div>
                    <div class="ui-block-b" style="width:60%">
                        <?php echo $value['code']; ?>
                    </div>
                </li>
                <li>
                    <div class="ui-block-a" style="width: 40%"><b>SKU</b></div>
                    <div class="ui-block-b" style="width:60%">
                        <?php echo isset($value['sku']) ? $value['sku'] : ''?>
                    </div>
                </li>
                <li>
                    <div class="ui-block-a" style="width: 40%"><b>Name</b></div>
                    <div class="ui-block-b" style="width:60%">
                        <?php echo isset($value['name']) ? $value['name'] : '';?>
                    </div>
                </li>
                <li>
                    <div class="ui-block-a" style="width: 40%"><b>Type</b></div>
                    <div class="ui-block-b" style="width:60%">
                        <?php echo isset($value['product_type']) ? $value['product_type'] : '';?>
                    </div>
                </li>
                <li>
                    <div class="ui-block-a" style="width: 40%"><b>Current supplier</b></div>
                    <div class="ui-block-b" style="width:60%">
                        <?php echo isset($value['company_name']) ? $value['company_name'] : '';?>
                    </div>
                </li>
                <li>
                    <div class="ui-block-a" style="width:40%"><b>Category</b></div>
                    <div class="ui-block-b" style="width:60%">
                        <?php echo isset($value['category']) ? $value['category'] : '';?>
                    </div>
                </li>
                <li>
                    <div class="ui-block-a" style="width:40%"><b>Sold by</b> </div>
                    <div class="ui-block-b" style="width:60%">
                        <?php echo isset($value['sell_by']) ? $value['sell_by'] : ''; ?>
                    </div>
                </li>
                <li>
                    <div class="ui-block-a" style="width:40%"><b>OUM</b> </div>
                    <div class="ui-block-b" style="width:60%">
                        <?php echo isset($value['oum']) ? $value['oum'] : ''; ?>
                    </div>
                </li>
                <li>
                    <div class="ui-block-a" style="width:40%"><b>Sell price</b> </div>
                    <div class="ui-block-b" style="width:60%">
                        <?php echo $this->Common->format_currency(isset($value['sell_price']) ? (float)$value['sell_price'] : 0); ?>
                    </div>
                </li>
                <input type="hidden" id="after_choose_products_<?php echo $value['_id']; ?>" value='<?php echo json_encode($value) ?>' />
            </ul>
        </li>
    <?php } ?>

    <?php if( $STT == 0 ){ ?>
    <center style="margin-top:30px">(No data)</center>
    <?php } ?>
<?php echo $this->element('pagination'); ?>
</form>
