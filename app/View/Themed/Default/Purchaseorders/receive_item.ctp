<style type="text/css">
ul.ul_mag li.hg_padd {
  overflow: visible !important;
}
.bg4 {
  background: none repeat scroll 0 0 #949494;
  color: #fff;
}

.bg4 span h4 {
  margin-left: 1%;
  width: 100%;
}
</style>
<div class="tab_1 full_width" style="margin:2% 1%; width:98%;">
    <span class="title_block bo_ra1">
        <span class="fl_dent">
            <h4>
               <?php
                    if(isset($arr_purchaseorder['code']))
                        echo translate('Purchase Order').': '.$arr_purchaseorder['code'].' ('.translate('Receive').')';
                ?>
            </h4>
        </span>
    </span>
    <p class="clear"></p>
    <ul class="ul_mag clear bg3">
        <li class="hg_padd center_text" style="width:7%"><?php echo translate('Code'); ?></li>
        <li class="hg_padd" style="width:30%"><?php echo translate('Name / detail'); ?></li>
        <li class="hg_padd right_txt" style="width:6%"><?php echo translate('Original qty'); ?></li>
        <div class="float_left" style="width:15%;">
            <div class="tab_title_purchasing">
                <span class="block_purcharsing"><?php echo translate('Shipping') ; ?></span>
            </div>
            <div>
                <li class="hg_padd line_mg right_txt" style="width:50%">Shipped</li>
                <li class="hg_padd line_mg right_txt" style="width:47%">Balance</li>
            </div>
        </div>
        <div class="float_left" style="width:15%;">
            <div class="tab_title_purchasing">
                <span class="block_purcharsing"><?php echo translate('Receiving'); ?></span>
            </div>
            <div>
                <li class="hg_padd line_mg right_txt" style="width:50%">Received</li>
                <li class="hg_padd line_mg right_txt" style="width:47%">Balance</li>
            </div>
        </div>
        <li class="hg_padd center_text" style="width:6%"><?php echo translate('Return'); ?></li>
        <li class="hg_padd right_txt" style="width:6%"><?php echo translate('Receive now'); ?></li>
        <li class="hg_padd" style="width:8%"><?php echo translate('Location'); ?></li>
    </ul>
    <div class="container_same_category" style="height: 450px;">
        <form method="POST" id="form_receive">
            <?php
                $i = 0;
                if(isset($arr_purchaseorder['products']) && !empty($arr_purchaseorder['products'])){
                    foreach ($arr_purchaseorder['products'] as $key => $product){
                        if($product['deleted']) continue;
                        if(!isset($product['quantity']) || $product['quantity'] < 1) continue;
                        if(isset($product['balance_received']) && $product['balance_received'] == $product['quantity']) continue;
            ?>
                <ul class="ul_mag clear bg<?php echo ($i%2==0 ? 2 : 1); ?>">
                    <input type="hidden" id="balance_value_<?php echo $key; ?>" value="<?php echo (isset($product['balance_received']) ? $product['balance_received'] : $product['quantity']); ?>" disabled="disabled" />
                    <li class="hg_padd center_text" style="width:7%">
                        <?php echo isset($product['code'])?$product['code']:''; ?>
                    </li>
                    <li class="hg_padd" style="width:30%">
                        <?php echo $product['products_name']; ?>.
                    </li>
                    <li id="product_quantity" class="hg_padd right_txt" style="width:6%">
                        <?php echo $product['quantity']; ?>
                    </li>
                    <li class="hg_padd right_txt" style="width:6.7%">
                        <?php echo (isset($product['quantity_shipped']) ? $product['quantity_shipped'] : 0); ?>
                    </li>
                    <li class="hg_padd right_txt" style="width:6.4%">
                        <?php
                            echo (isset($product['balance_shipped']) ? $product['balance_shipped'] : $product['quantity']);
                        ?>
                    </li>
                    <li id="received_<?php echo $key; ?>" class="hg_padd right_txt" style="width:6.7%">
                        <?php echo (isset($product['quantity_received']) ? $product['quantity_received'] : 0); ?>

                    </li>
                    <li id="balance_<?php echo $key; ?>" class="hg_padd right_txt" style="width:6.2%">
                        <?php
                            echo (isset($product['balance_received']) ? $product['balance_received'] : $product['quantity']);
                        ?>

                    </li>
                    <li class="hg_padd right_txt" style="width:6%">
                        <?php echo (isset($product['quantity_returned']) ? $product['quantity_returned'] : 0 ); ?>
                    </li>
                    <li class="hg_padd right_text" style="width:6%">

                        <input type="text" rel="0" onkeypress="return isPrice(event);" id="receive_now_<?php echo $key; ?>" name="receive_now_<?php echo $key; ?>" value="0" class="receive_now input_inner jt_box_save right_txt"/>
                    </li>
                    <li class="hg_padd right_text" style="width:8%; overflow: visible;">
                        <?php echo $this->Form->input("location_".$key, array(
                                'class'=>'input_inner jt_box_save viewprice_products_name',
                                'name'=>'location_name_'.$key,
                                'value'=>(isset($product['receive_item']['location_name']) ? $product['receive_item']['location_name'] : reset($arr_location)),
                                'stype'=>'width:150px',
                        )); ?>
                        <input type="hidden" name='location_id_<?php echo $key; ?>' id="location_<?php echo $key; ?>Id" value="<?php echo (isset($product['receive_item']['location_id']) ? $product['receive_item']['location_id']: key($arr_location) ); ?>" />
                        <script type="text/javascript">
                            $(function () {
                                $("#location_<?php echo $key; ?>").combobox(<?php echo json_encode($arr_location); ?>);
                            });
                        </script>
                    </li>
                </ul>
            <?php
                        $i++;
                    }
                }
            ?>
            <input type="hidden" name="submit" value="submit" />
        </form>
        <?php
            if($i<20){
                for($i; $i < 20 ; $i++ )
                    echo ' <ul class="ul_mag clear bg'.($i%2==0 ? 2 : 1).'" style="overflow:none;"></ul>';
            }
        ?>
    </div>
    <span class="title_block bo_ra2">
        <span class="bt_block float_right no_bg">
        </span>
    </span>
</div>

<script>
    $(function() {
        $('.receive_now').change(function() {
            var ids = $(this).attr('id');
            var values = $(this).val();
            var old = $(this).attr('rel');
            var tmp = ids.split("_");
            tmp = tmp[2];
            var balance = parseInt($("#balance_value_" + tmp).val());
            if (values > balance) {
                alerts('Message', 'Product received is not greater than the remaining products', function() {
                    $("#" + ids).val(parseInt(old)).focus();
                });
            }
            else {
                 $("#" + ids).val(parseInt(values));
                $("#" + ids).attr('rel', parseInt(values));
            }
        });
    });
</script>