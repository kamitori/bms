<?php
    $original_minimun = Cache::read('minimun');
    if(!$original_minimun){
        $original_minimun = 50;
        if(isset($value['sell_price'])) $original_minimun = (float)$value['sell_price'];
        Cache::write('minimun',$original_minimun);$minimun = 1;
    }
    $minimun = $original_minimun;
?>
<?php foreach ($arr_purchaseorders as $value): ?>
    <li id="list-<?php echo $value['_id'] ?>" class="ui-li-static ui-body-inherit"  data-role="collapsible" data-role="collapsible" data-collapsed-icon="arrow-d" data-expanded-icon="arrow-u" data-iconpos="right" data-inset="false">
        <h2>
            <div class="ui-block-a" style="width:40%">
                <a class="link-to-entry" href="<?php echo URL; ?>/mobile/purchaseorders/entry/<?php echo $value['_id']; ?>"><?php echo $value['code']; ?></a>
            </div>
            <div class="ui-block-b" style="width:60%"><?php echo isset($value['company_name'])?$value['company_name']:'' ;?></div>
        </h2>
        <ul data-role="listview" data-theme="b">
            <li>
                <div class="ui-block-a" style="width: 40%"><b>Ref no</b></div>
                <div class="ui-block-b" style="width:60%">
                    <?php echo $value['code']; ?>
                </div>
            </li>

            <li>
                <div class="ui-block-a" style="width: 40%"><b>Company</b></div>
                <div class="ui-block-b" style="width:60%">
                        <?php if (isset($value['company_id']) && is_object($value['company_id']) ) { ?>
                        <a data-ajax="false" href="<?php echo URL; ?>/mobile/companies/entry/<?php echo $value['company_id']; ?>">
                        <?php echo $value['company_name']; ?>
                        </a>
                    <?php } ?>
                </div>
            </li>

            <li>
                <div class="ui-block-a" style="width: 40%"><b>Contact</b></div>
                <div class="ui-block-b" style="width:60%">
                    <?php echo isset($value['contact_name'])?$value['contact_name']:'';?>
                </div>
            </li>

            <li>
                <div class="ui-block-a" style="width:40%"><b>Order date</b></div>
                <div class="ui-block-b" style="width:60%"><?php echo $this->Common->format_date($value['purchord_date']->sec); ?></div>
            </li>

            <li>
                <div class="ui-block-a" style="width: 40%"><b>Our rep</b></div>
                <div class="ui-block-b" style="width:60%">
                    <?php echo isset($value['our_rep'])?$value['our_rep']:'';?>
                </div>
            </li>

            <li>
                <div class="ui-block-a" style="width: 40%"><b>Status</b></div>
                <div class="ui-block-b" style="width:60%">
                    <?php echo isset($value['purchase_orders_status'])?$value['purchase_orders_status']:'';?>
                </div>
            </li>

             <li>
                <div class="ui-block-a" style="width: 40%"><b>Sales order</b></div>
                <div class="ui-block-b" style="width:60%">
                    <?php echo isset($value['salesorder_name'])?$value['salesorder_name']:'';?>
                </div>
            </li>


            <li>
                <div class="ui-block-a" style="width: 40%"><b>Total cost</b></div>
                <div class="ui-block-b" style="width:60%">
                    <?php
                        if(isset($minimun) && $value['sum_amount'] < $minimun && $value['sum_amount']!='')
                            $value['sum_amount'] = $minimun;
                    ?>
                    <?php 
                        if (isset($value['sum_amount']) && $value['sum_amount']!='')
                            echo $this->Common->format_currency($value['sum_amount']);
                        else
                            echo '';
                    ?>
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