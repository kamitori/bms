<?php

    $i = 2;
    $sum_quotations = 0;
    $arr_companies = Cache::read('arr_companies');
    if(!$arr_companies)
        $arr_companies = array();
    $count = count($arr_companies);
    $_controller->selectModel('Company');
    $original_minimum = Cache::read('minimum');
    $product = Cache::read('minimum_product');
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
    $delete = $this->Common->check_permission($controller.'_@_entry_@_delete',$arr_permission);
?><br>
<?php foreach ($arr_jobs as $value): ?>
    <?php
    if ($i == 2)
        $i = $i - 1;
    else
        $i = $i + 1;
    ?>
    <ul class="ul_mag clear bg<?php echo $i ?>" id="jobs_<?php echo (string) $value['_id']; ?>">
        <li class="hg_padd" style="width:1%">
            <a style="color: blue" href="<?php echo URL; ?>/jobs/entry/<?php echo $value['_id']; ?>"><span class="icon_emp"></span></a>
        </li>
        <li class="hg_padd center_txt" style="width:4%"><?php echo $value['no']; ?></li>
        <li class="hg_padd" style="width:13%">
            <?php
                // if(isset($value['company_name']) && $value['company_name'] && (!isset($value['company_id'])||!is_object($value['company_id']))){
                //     $value['company_name'] = str_replace(array('(',')'), '.*', $value['company_name']);
                //     $co = $_controller->Company->select_one(array('name'=>new MongoRegex('/'.$value['company_name'].'/')),array('_id'));
                //     if(isset($co['_id']))
                //         $value['company_id'] = $co['_id'];
                // }
                if (isset($value['company_id']) && (is_object($value['company_id']) || strlen($value['company_id']) == 24) ) {
            ?>
                <a href="<?php echo URL; ?>/companies/entry/<?php echo $value['company_id']; ?>">
                    <?php echo $value['company_name']; ?>
                </a>
            <?php } //else if(isset($value['company_name']) && $value['company_name'] ){ ?>
            <?php //echo $value['company_name']; ?>
            <?php //} ?>
        </li>
        <li class="hg_padd" style="width:11%"><?php echo $value['name']; ?></li>
        <li class="hg_padd center_txt" style="width:8%"><?php if(is_object($value['work_end'])) echo $this->Common->format_date($value['work_end']->sec, false); ?></li>
        <li class="hg_padd" style="width:6%"><?php echo $value['status']; ?></li>
        <li class="hg_padd right_txt" style="width:10%">
        <?php
            $arr_quote = $quotation->select_all(array(
                'arr_where' => array(
                                     'job_id' => new MongoId($value['_id']),
                                     'quotation_status'=>array('$ne'=>'Cancelled'),
                                     ),
                'arr_field' => array('_id','sum_sub_total','quotation_status','company_id', 'shipping_cost')
            ));
            $sum = 0;
            foreach($arr_quote as $key=>$quote){
                if(!isset($quote['company_id']))
                    $quote['company_id'] = '';
                if(!isset($arr_companies[(string)$quote['company_id']])){
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
                $minimum += isset($quote['shipping_cost']) ? $quote['shipping_cost'] : 0;
                if($quote['sum_sub_total']<$minimum)
                    $sum += $minimum;
                else
                    $sum += $quote['sum_sub_total'];
            }
            $sum_quotations += $sum;
            echo $this->Common->format_currency((float)$sum);
        ?>
        </li>


		<?php //SALES TOTAL
            $arr_salesorder = $salesorder->select_all(array(
                'arr_where' => array(
                                     'job_id' => new MongoId($value['_id']),
                                     'status'=>array('$ne'=>'Cancelled')
                                     ),
                'arr_field' => array('_id','sum_sub_total','status','company_id', 'shipping_cost')
            ));
            $order_sum = 0; $m = $iscolor = 0;
            foreach($arr_salesorder as $key=>$order){
                if(!isset($order['company_id']))
                    $order['company_id'] = '';
                if(!isset($arr_companies[(string)$order['company_id']])){
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
                $minimum += isset($order['shipping_cost']) ? $order['shipping_cost'] : 0;
                if($order['sum_sub_total']<$minimum)
                    $order_sum += $minimum;
                else
                    $order_sum += $order['sum_sub_total'];

				if(isset($order['status']) && $order['status']!='Completed')
					$iscolor++;
				$m++;

            }
		?>

        <?php // INVOICE TOTAL
            $arr_salesinvoice = $salesinvoice->select_all(array(
                'arr_where' => array(
                                     'job_id' => new MongoId($value['_id']),
                                     'invoice_status'=>array('$ne'=>'Cancelled'),
                                     ),
                'arr_field' => array('_id','sum_sub_total','invoice_status','company_id', 'shipping_cost', 'invoice_status')
            ));
            $invoice_sum = 0;$m = $isred = $isblue = 0;
            foreach($arr_salesinvoice as $key=>$invoice){
                if(!isset($invoice['company_id']))
                    $invoice['company_id'] = '';
                if(!isset($arr_companies[(string)$invoice['company_id']])){
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
                $minimum += isset($invoice['shipping_cost']) ? $invoice['shipping_cost'] : 0;
                if( $invoice['invoice_status'] == 'Credit' && $invoice['sum_sub_total'] > 0 ) {
                    $invoice['sum_sub_total'] *= -1;
                }
                if($invoice['sum_sub_total']<$minimum && $invoice['invoice_status'] != 'Credit')
                    $invoice_sum += $minimum;
                else
                    $invoice_sum += $invoice['sum_sub_total'];

                if(isset($invoice['invoice_status']) && $invoice['invoice_status']!='Invoiced')
                    $isred++;
                if(isset($invoice['invoice_status']) && $invoice['invoice_status']!='Paid')
                    $isblue++;
                $m++;
            }
        ?>
        <?php
            $unbalance = '';
            if(abs(($order_sum - $invoice_sum) / ($invoice_sum==0? 1 :$invoice_sum) ) > 0.001)
                $unbalance = 'background-color: rgb(247, 215, 134);';
        ?>
        <li class="hg_padd right_txt" style="width:10%;<?php if($unbalance){ echo $unbalance; }  if($iscolor==0 && $m>0) echo 'color:red;';?>">
            <?php echo $this->Common->format_currency((float)$order_sum);?>
        </li>




        <li class="hg_padd right_txt" style="width:10%;<?php if($unbalance){ echo $unbalance; }  if($isred==0 && $m>0) { echo 'color:red;'; } else if($isblue==0 && $m>0) { echo 'color:blue;'; }?>">
        	<?php echo $this->Common->format_currency((float)$invoice_sum);?>
        </li>


        <li class="hg_padd" style="width:8%"><?php if (isset($value['type']) && isset($arr_jobs_type[$value['type']])) echo $arr_jobs_type[$value['type']]; ?></li>
        <li class="hg_padd center_txt" style="width:3%">
            <?php
            if ( in_array( $value['status_id'], array('New', 'Confirmed')) && isset($value['work_end']) && is_object($value['work_end'])) {
                if ($value['work_end']->sec < strtotime('now')) {
                    echo '<span class="Late">X</span>';
                }
            }?>
        </li>
        <li class="hg_padd bor_mt" style="width:3%">
            <?php if($delete): ?>
            <div class="middle_check">
                <a href="javascript:void(0)" title="Delete link" onclick="jobs_lists_delete('<?php echo $value['_id']; ?>')">
                    <span class="icon_remove2"></span>
                </a>
            </div>
        <?php endif; ?>
        </li>
    </ul>
<?php endforeach;
if(count($arr_companies) > $count)
    Cache::write('arr_companies',$arr_companies);
echo $this->element('popup/pagination_lists');
?>