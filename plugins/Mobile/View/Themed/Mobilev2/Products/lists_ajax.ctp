<?php foreach ($arr_products as $value): ?>
    <li id="list-<?php echo $value['_id'] ?>" class="ui-li-static ui-body-inherit"  data-role="collapsible" data-role="collapsible" data-collapsed-icon="arrow-d" data-expanded-icon="arrow-u" data-iconpos="right" data-inset="false">
        <h2>
            <div class="ui-block-a" style="width:40%">
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
                    <?php echo isset($value['name']) ? $value['name'] : ''?>
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
                <div class="ui-block-a" style="width:40%"><b>In stock</b></div>
                <div class="ui-block-b" style="width:60%">
                    <?php echo isset($value['qty_in_stock']) ? $value['qty_in_stock'] : ''; ?>
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
                <div class="ui-block-a" style="width:40%"><b>Cost price</b> </div>
                <div class="ui-block-b" style="width:60%">
                    <?php echo isset($value['cost_price']) ? $value['cost_price'] : ''; ?>
                </div>
            </li>
            <li>
                <div class="ui-block-a" style="width:40%"><b>Special Order</b> </div>
                <div class="ui-block-b" style="width:60%">
                    <?php echo isset($value['special_order']) ? $value['special_order'] : ''; ?>
                </div>
            </li>

            <li>
            	<a href="#popupDialog" class="callDelete" data-id="<?php echo $value['_id']; ?>" data-rel="popup" data-position-to="window" data-transition="pop" class="ui-btn ui-shadow ui-btn-inline ui-icon-delete ui-btn-icon-left ui-btn-b">Delete</a>
            </li>
        </ul>
    </li>
<?php endforeach ?>
<script type="text/javascript">
    $(".link-to-entry").click(function(){
        window.location.assign($(this).attr("href"));
        event.preventDefault();
    });
</script>