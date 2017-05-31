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
    $_controller->selectModel('Salesorder');
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
<?php foreach ($arr_invoices as $invoice){
    if ($i == 2)
        $i = $i - 1;
    else
        $i = $i + 1;
    ?>
    <ul class="ul_mag clear bg<?php echo $i ?>" id="invoice_<?php echo (string) $invoice['_id']; ?>">
        <?php
            if(!isset($invoice['company_id']))
                $invoice['company_id'] = '';
            if($invoice['invoice_status'] == 'Cancelled')
                $invoice['sum_sub_total'] = 0;
            else if(!isset($arr_companies[(string)$invoice['company_id']])){
                $minimum = $original_minimum;
                if(is_object($invoice['company_id'])){
                    $company = $_controller->Company->select_one(array('_id'=>new MongoId($invoice['company_id'])),array('pricing'));
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
                $arr_companies[(string)$invoice['company_id']] = $minimum;
            } else
                $minimum = $arr_companies[(string)$invoice['company_id']];
        ?>
        <li class="hg_padd" style="width:.5%">
            <a href="<?php echo URL; ?>/salesinvoices/entry/<?php echo $invoice['_id']; ?>"><span class="icon_emp"></span></a>
        </li>
        <li class="hg_padd center_txt" style="width:5%"><?php echo $invoice['code']; ?></li>
        <li class="hg_padd" style="width:15%">
            <?php if (isset($invoice['company_id']) && is_object($invoice['company_id']) ) { ?>
                <a style="text-decoration: none;" href="<?php echo URL; ?>/companies/entry/<?php echo $invoice['company_id']; ?>">
                    <?php echo $invoice['company_name']; ?>
                </a>
            <?php } ?>
        </li>
        <li class="hg_padd" style="width:7%">
            <?php if (isset($invoice['contact_id']) && is_object($invoice['contact_id']) ) { ?>
                <a style="text-decoration: none;" href="<?php echo URL; ?>/contacts/entry/<?php echo $invoice['contact_id']; ?>">
                    <?php echo $invoice['contact_name']; ?>
                </a>
            <?php } ?>
        </li>
        <li class="hg_padd center_txt" style="width:5%"><?php echo $this->Common->format_date($invoice['invoice_date']->sec, false); ?></li>
        <li class="hg_padd" style="width:15%">
            <?php if(isset($invoice['heading'])) echo $invoice['heading']; ?>
        </li>
        <li class="hg_padd right_txt" style="width:15%">
            <?php
                if(isset($invoice['salesorder_id']) && is_object($invoice['salesorder_id'])){
                    $order = $_controller->Salesorder->select_one(array('_id'=> $invoice['salesorder_id']),array('sum_sub_total','status'));
                    if($order['status'] == 'Cancelled') {
                        $order['sum_sub_total'] = 0;
                    } else if(isset($minimum) && $order['sum_sub_total'] < $minimum) {
                        $order['sum_sub_total'] = $minimum;
                    }
                    echo '<a style="text-decoration: none;" href="'.URL.'/salesorders/entry/'.$invoice['salesorder_id'].'">'.$this->Common->format_currency($order['sum_sub_total']).'</a>';
                }
            ?>
        </li>
        <li class="hg_padd right_txt" style="width:10%;">
            <?php if (isset($invoice['job_id']) && is_object($invoice['job_id']) ) { ?>
                <a style="text-decoration: none;" href="<?php echo URL; ?>/jobs/entry/<?php echo $invoice['job_id']; ?>">
                    <?php echo $invoice['job_number']; ?>
                </a>
            <?php } ?>
        </li>
        <li class="hg_padd right_txt" style="width:8%;">
            <?php
                if ($invoice['invoice_status'] == 'Cancelled') {
                    $invoice['sum_sub_total'] = 0;
                } else {
                    $invoice['sum_sub_total'] = $invoice['sum_sub_total'] * 1.05;
                    if(isset($minimum) && $invoice['sum_sub_total'] < $minimum)
                        $invoice['sum_sub_total'] = $minimum;
                }
                echo $this->Common->format_currency($invoice['sum_sub_total']);
            ?>
        </li>

        <li class="hg_padd" style="width:5%">
            <?php echo $invoice['invoice_status']; ?>
        </li>
        <li class="hg_padd bor_mt" style="width:.5%">
            <?php if($delete){ ?>
            <div class="middle_check">
                <a href="javascript:void(0)" title="Delete link" onclick="salesinvoices_lists_delete('<?php echo $invoice['_id']; ?>')">
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
    $("ul","#pagination").append('<li style="float: right; margin-right: 10px; line-height: 30px; padding: 0px"><span>Outstanding Invoices</span>&nbsp;&nbsp;&nbsp;<input type="text" style="width: 50px; text-align: right;" readonly id="outstanding_number" />&nbsp;&nbsp;&nbsp;<input type="text" style="width: 100px; text-align: right;" readonly id="outstanding_amount" />&nbsp;&nbsp;<span style="font-size: 14px; font-weight: bold">|</span> </li>');
    $.ajax({
        url: "<?php echo URL.'/salesinvoices/get_sum_outstading' ?>",
        success: function(result) {
            result = $.parseJSON(result);
            $("#outstanding_number").val(result.number);
            $("#outstanding_amount").val(result.amount);
        }
    });
})
</script>