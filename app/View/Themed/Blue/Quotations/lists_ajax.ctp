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
<?php foreach ($arr_quotes as $quote){
    if ($i == 2)
        $i = $i - 1;
    else
        $i = $i + 1;
    ?>
    <ul class="ul_mag clear bg<?php echo $i ?>" id="quote_<?php echo (string) $quote['_id']; ?>">
        <?php
            if(!isset($quote['company_id']))
                $quote['company_id'] = '';
            if($quote['quotation_status'] == 'Cancelled')
                $quote['sum_sub_total'] = 0;
            else if(!isset($arr_companies[(string)$quote['company_id']])){
                $minimum = $original_minimum;
                if(is_object($quote['company_id'])){
                    $company = $_controller->Company->select_one(array('_id'=>new MongoId($quote['company_id'])),array('pricing'));
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
                $arr_companies[(string)$quote['company_id']] = $minimum;
            } else
                $minimum = $arr_companies[(string)$quote['company_id']];
        ?>
        <li class="hg_padd" style="width:.5%">
            <a href="<?php echo URL; ?>/quotations/entry/<?php echo $quote['_id']; ?>"><span class="icon_emp"></span></a>
        </li>
        <li class="hg_padd center_txt" style="width:5%"><?php echo $quote['code']; ?></li>
        <li class="hg_padd center_txt" style="width:5%"><?php echo $quote['quotation_type']; ?></li>
        <li class="hg_padd" style="width:15%">
            <?php if (isset($quote['company_id']) && is_object($quote['company_id']) ) { ?>
                <a style="text-decoration: none;" href="<?php echo URL; ?>/companies/entry/<?php echo $quote['company_id']; ?>">
                    <?php echo $quote['company_name']; ?>
                </a>
            <?php } ?>
        </li>
        <li class="hg_padd" style="width:10%">
            <?php if (isset($quote['contact_id']) && is_object($quote['contact_id']) ) { ?>
                <a style="text-decoration: none;" href="<?php echo URL; ?>/contacts/entry/<?php echo $quote['contact_id']; ?>">
                    <?php echo $quote['contact_name']; ?>
                </a>
            <?php } ?>
        </li>
        <li class="hg_padd center_txt" style="width:8%"><?php echo $quote['phone']; ?></li>

        <li class="hg_padd center_txt" style="width:5%"><?php echo $this->Common->format_date($quote['quotation_date']->sec, false); ?></li>
        <li class="hg_padd" style="width:10%">
            <?php if (isset($quote['our_rep_id']) && is_object($quote['our_rep_id']) ) { ?>
                <a style="text-decoration: none;" href="<?php echo URL; ?>/contacts/entry/<?php echo $quote['our_rep_id']; ?>">
                    <?php echo $quote['our_rep']; ?>
                </a>
            <?php } ?>
        </li>
        <li class="hg_padd" style="width:5%">
            <?php echo $quote['quotation_status']; ?>
        </li>
        <li class="hg_padd right_txt" style="width:15%;">
            <?php if (isset($quote['salesorder_id']) && is_object($quote['salesorder_id']) ) { ?>
                <a style="text-decoration: none;" href="<?php echo URL; ?>/salesorders/entry/<?php echo $quote['salesorder_id']; ?>">
                    <?php echo $quote['salesorder_name']; ?>
                </a>
            <?php } ?>
        </li>
        <li class="hg_padd right_txt" style="width:8%;">
            <?php
                $minimum += isset($quote['shipping_cost']) ? $quote['shipping_cost'] : 0;
                if(isset($minimum) && $quote['sum_sub_total'] < $minimum)
                    $quote['sum_sub_total'] = $minimum;
                echo $this->Common->format_currency($quote['sum_sub_total']);
            ?>
        </li>
        <li class="hg_padd bor_mt" style="width:.5%">
            <?php if($delete){ ?>
            <div class="middle_check">
                <a href="javascript:void(0)" title="Delete link" onclick="salesquotes_lists_delete('<?php echo $quote['_id']; ?>')">
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