<style type="text/css">
ul.ul_mag li.hg_padd {
  overflow: visible !improductrtant;
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
                    echo translate('Purchase Order').': '.$arr_purchaseorder['code'].' ('.translate('Return').')';
                ?>
            </h4>
        </span>
    </span>
    <p class="clear"></p>
    <ul class="ul_mag clear bg3">
        <li class="hg_padd center_text" style="width:10%"><?php echo translate('Code'); ?></li>
        <li class="hg_padd" style="width:30%"><?php echo translate('Name / detail'); ?></li>
        <li class="hg_padd right_txt" style="width:6%"><?php echo translate('Original qty'); ?></li>
        <div class="float_left" style="width:15%;">
            <div class="tab_title_purchasing">
                <span class="block_purcharsing"><?php echo translate('Received') ; ?></span>
            </div>
            <div>
                <li class="hg_padd line_mg right_txt" style="width:50%"><?php echo translate('Received') ; ?></li>
                <li class="hg_padd line_mg right_txt" style="width:47%"><?php echo translate('Balance'); ?></li>
            </div>
        </div>
        <div class="float_left" style="width:15%;">
            <div class="tab_title_purchasing">
                <span class="block_purcharsing"><?php echo translate('Returned'); ?></span>
            </div>
            <div>
                <li class="hg_padd line_mg right_txt" style="width:50%"><?php echo translate('Returned'); ?></li>
                <li class="hg_padd line_mg right_txt" style="width:47%"><?php echo translate('Balance'); ?></li>
            </div>
        </div>
        <li class="hg_padd right_txt" style="width:6%"><?php echo translate('Returned now'); ?></li>
    </ul>
    <div class="container_same_category" style="height: 450px;">
        <form method="POST" id="form_return">
            <?php
                $i = 1;
                if(isset($arr_purchaseorder['products']) && !empty($arr_purchaseorder['products'])){
                    foreach ($arr_purchaseorder['products'] as $key => $product){
                        if($product['deleted']) continue;
                        if(!isset($product['quantity']) || $product['quantity'] < 1) continue;
                        if(isset($product['balance_returned']) && $product['balance_returned'] == 0) continue;
            ?>
                <ul class="ul_mag clear bg<?php echo ($i%2==0 ? 2 : 1); ?>" style="overflow:none;">
                    <input type="hidden" disable="disabled" id="balance_value_<?php echo $key; ?>" value="<?php echo (isset($product['balance_returned'])?$product['balance_returned']:0); ?>" />
                    <li class="hg_padd center_text" style="width:10%">
                        <?php echo $product['code']; ?>
                    </li>
                    <li class="hg_padd" style="width:30%">
                        <?php echo $product['products_name']; ?>
                    </li>
                    <li id="product_quantity" class="hg_padd right_txt" style="width:6%">
                        <?php echo $product['quantity']; ?>
                    </li>
                    <li id="received_quantity" class="hg_padd right_txt" style="width:6.7%">
                        <?php echo (isset($product['quantity_received']) ? $product['quantity_received'] : 0); ?>
                    </li>
                    <li id="received_balance" class="hg_padd right_txt" style="width:6.4%">
                        <?php
                            echo (isset($product['balance_received']) ? $product['balance_received'] : 0);
                        ?>
                    </li>
                    <li id="returned_<?php echo $key; ?>" class="hg_padd right_txt" style="width:6.7%">
                        <?php echo (isset($product['quantity_returned']) ? $product['quantity_returned'] : 0); ?>

                    </li>
                    <li id="balance_<?php echo $key; ?>" class="hg_padd right_txt" style="width:6.2%">
                        <?php
                            echo (isset($product['balance_returned'])?$product['balance_returned']:0);
                        ?>
                    </li>
                    <li class="hg_padd right_text" style="width:6%">

                        <input type="text" rel="0" onkeypress="return isPrice(event);" id="return_now_<?php echo $key; ?>" name="return_now_<?php echo $key; ?>" value="0" class="return_now input_inner jt_box_save right_txt"/>
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
            <div class="dent_input float_right">
            </div>
        </span>
    </span>
</div>

<script>
    $(function() {
        $('.return_now').change(function() {
            var ids = $(this).attr('id');
            var values = $(this).val();
            var old = $(this).attr('rel');
            var tmp = ids.split("_");
            tmp = tmp[2];
            var balance = parseInt($("#balance_value_" + tmp).val());
            if (values > balance) {
                alerts('Message', 'Product returned is not greater than the received products', function() {
                    $("#" + ids).val(old).focus();
                });
            }
            else {
                 $("#" + ids).val(parseInt(values));
                $("#" + ids).attr('rel', parseInt(values));
            }
        });
    });
</script>