<?php
    $i = 2;
    $delete = $this->Common->check_permission($controller.'_@_entry_@_delete',$arr_permission);
    $arr_companies = Cache::read('arr_companies');
    if(!$arr_companies)
        $arr_companies = array();
    $count = count($arr_companies);
    $original_minimum = Cache::read('minimum');
    $product = Cache::read('minimum_product');
    $_controller->selectModel('Company');
    $_controller->selectModel('Quotation');
    $_controller->selectModel('Job');
    if(!$original_minimum){
        $_controller->selectModel('Product');
        $_controller->selectModel('Stuffs');
        $original_minimum = 50;
        $product = $_controller->Stuffs->select_one(array('value'=>"Minimum Order Adjustment"),array('product_id'));
        $p = $_controller->Product->select_one(array('_id'=> new MongoId($product['product_id'])),array('sell_price'));
        if(isset($p['sell_price']))
            $original_minimum = (float)$p['sell_price'];
        Cache::write('minimum',$original_minimum);
        Cache::write('minimum_product',$product);
    }
?><br>
<?php
    $current_date = strtotime(date('Y-m-d'));
?>
<?php foreach ($arr_orders as $order){
    if ($i == 2)
        $i = $i - 1;
    else
        $i = $i + 1;
    $late = '';
    if( !in_array($order['status'],array('Completed','Cancelled')) ){
        $payment_due_date = date('Y-m-d', $order['payment_due_date']->sec);
        if(round((strtotime($payment_due_date) - $current_date) / DAY) < 0)
            $late = 'style="background-color: rgb(247, 215, 134);"';
    }
?>
    <ul class="ul_mag clear bg<?php echo $i ?>" id="order_<?php echo (string) $order['_id']; ?>" <?php echo $late; ?>>
        <?php
            if(!isset($order['company_id']))
                $order['company_id'] = '';
            if($order['status'] == 'Cancelled')
                $order['sum_sub_total'] = 0;
            else if(!isset($arr_companies[(string)$order['company_id']])){
                $minimum = $original_minimum;
                if(is_object($order['company_id'])){
                    $company = $_controller->Company->select_one(array('_id'=>new MongoId($order['company_id'])),array('pricing'));
                    if(isset($company['pricing'])&&!empty($company['pricing'])){
                        foreach($company['pricing'] as $pricing){
                            if(isset($pricing['deleted'])&&$pricing['deleted']) continue;
                            if((string)$pricing['product_id']!=(string)$product['product_id']) continue;
                            if(!isset($pricing['price_break']) || empty($pricing['price_break'])) continue;
                            $price_break = reset($pricing['price_break']);
                            $minimum = (float)$price_break['unit_price']; break;
                        }
                    }
                } else {
                    $minimum = $original_minimum;
                }
                $arr_companies[(string)$order['company_id']] = $minimum;
            } else
                $minimum = $arr_companies[(string)$order['company_id']];
        ?>
        <li class="hg_padd" style="width:.5%">
            <a href="<?php echo URL; ?>/salesorders/entry/<?php echo $order['_id']; ?>"><span class="icon_emp"></span></a>
        </li>
        <li class="hg_padd center_txt" style="width:5%"><?php echo $order['code']; ?></li>
        <li class="hg_padd" style="width:15%">
            <?php if (isset($order['company_id']) && is_object($order['company_id']) ) { ?>
                <a style="text-decoration: none;" href="<?php echo URL; ?>/companies/entry/<?php echo $order['company_id']; ?>">
                    <?php echo $order['company_name']; ?>
                </a>
            <?php } ?>
        </li>
        <li class="hg_padd" style="width:7%">
            <?php if (isset($order['our_csr_id']) && is_object($order['our_csr_id']) ) { ?>
                <a style="text-decoration: none;" href="<?php echo URL; ?>/contacts/entry/<?php echo $order['our_csr_id']; ?>">
                    <?php echo $order['our_csr']; ?>
                </a>
            <?php } ?>
        </li>
        <li class="hg_padd center_txt" style="width:5%"><?php echo $this->Common->format_date($order['salesorder_date']->sec, false); ?></li>
        <li class="hg_padd center_txt" style="width:5%"><?php echo $this->Common->format_date($order['payment_due_date']->sec, false); ?></li>
        <li class="hg_padd" style="width:20%">
            <?php if(isset($order['heading'])) echo $order['heading']; ?>
        </li>
        <li class="hg_padd right_txt" style="width:8%;">
            <?php if (isset($order['job_id']) && is_object($order['job_id']) ) { ?>
                <a style="text-decoration: none;" href="<?php echo URL; ?>/jobs/entry/<?php echo $order['job_id']; ?>">
                    <?php echo $order['job_number']; ?>
                </a>
            <?php } ?>
        </li>
        <li class="hg_padd right_txt" style="width:5%;">
            <?php
                $minimum += isset($order['shipping_cost']) ? $order['shipping_cost'] : 0;
                if(isset($minimum) && $order['sum_sub_total'] < $minimum)
                    $order['sum_sub_total'] = $minimum;
                echo $this->Common->format_currency($order['sum_sub_total']);
            ?>
        </li>

        <li class="hg_padd" style="width:15%" title="<?php echo $order['asset_status']; ?>">
            <?php echo $order['asset_status']; ?>
        </li>
        <li class="hg_padd bor_mt" style="width:.5%">
            <?php if($delete){ ?>
            <div class="middle_check">
                <a href="javascript:void(0)" title="Delete link" onclick="salesorders_lists_delete('<?php echo $order['_id']; ?>')">
                    <span class="icon_remove2"></span>
                </a>
            </div>
        <?php } ?>
        </li>
    </ul>
<?php }
if(count($arr_companies) > $count)
    Cache::write('arr_companies',$arr_companies);
echo $this->element('popup/pagination_lists');
?>
<script type="text/javascript">
$(function(){
    $("ul","#pagination").append('<li style="float: right; text-align: right;margin-right: 2%; line-height: 30px; padding: 0px;width: 69%;">&nbsp;&nbsp;&nbsp;<input type="text" style="width: 18%; text-align: right;margin-right: 16%;" readonly="" id="total-quotation">&nbsp;&nbsp;&nbsp;<input type="text" style="width: 12%; text-align: right;" readonly="" id="total-sale">&nbsp;&nbsp; </li>');
    $.ajax({
        url: "<?php echo URL.'/salesorders/get_sum_list' ?>",
        success: function(result) {
            result = $.parseJSON(result);
            $("#total-quotation").val(result.quotation);
            $("#total-sale").val(result.sales);
        }
    });
})
</script>