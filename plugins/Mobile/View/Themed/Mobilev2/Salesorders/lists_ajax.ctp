<?php
    $original_minimun = Cache::read('minimun');
    if(!$original_minimun){
        $original_minimun = 50;
        if(isset($value['sell_price'])) $original_minimun = (float)$value['sell_price'];
        Cache::write('minimun',$original_minimun);$minimun = 1;
    }
    $minimun = $original_minimun;
?>
<?php foreach ($arr_salesorders as $value): ?>
    <li id="list-<?php echo $value['_id'] ?>" class="ui-li-static ui-body-inherit"  data-role="collapsible" data-role="collapsible" data-collapsed-icon="arrow-d" data-expanded-icon="arrow-u" data-iconpos="right" data-inset="false">
        <h2>
            <div class="ui-block-a" style="width:40%">
                <a class="link-to-entry" href="<?php echo URL; ?>/mobile/salesorders/entry/<?php echo $value['_id']; ?>"><?php echo $value['code']; ?></a>
            </div>
            <div class="ui-block-b" style="width:60%"><?php echo isset($value['company_name'])?$value['company_name']:'' ;?></div>
        </h2>
        <ul data-role="listview" data-theme="b">
            <li>
                <div class="ui-block-a" style="width: 40%"><b>Salesorder code</b></div>
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
                <div class="ui-block-b" style="width:60%"><?php echo $this->Common->format_date($value['salesorder_date']->sec); ?></div>
            </li>

            <li>
                <div class="ui-block-a" style="width: 40%"><b>Heading</b></div>
                <div class="ui-block-b" style="width:60%">
                    <?php echo isset($value['heading'])?$value['heading']:'';?>
                </div>
            </li>


            <li>
                <div class="ui-block-a" style="width: 40%"><b>Total Quotation</b></div>
                <div class="ui-block-b" style="width:60%">
                    <?php
                    $_controller->selectModel('Quotation');
                    if(isset($value['quotation_id']) && is_object($value['quotation_id'])){
                        $quote = $_controller->Quotation->select_one(array('_id'=> $value['quotation_id']),array('sum_sub_total','quotation_status'));
                        if($quote['quotation_status'] == 'Cancelled')
                                $quote['sum_sub_total'] = 0;
                        else if(isset($minimun) && $quote['sum_sub_total'] < $minimun)
                                $quote['sum_sub_total'] = $minimun;

                        echo '<a style="text-decoration: none;" href="'.URL.'/quotations/entry/'.$value['quotation_id'].'">'.$this->Common->format_currency($quote['sum_sub_total']).'</a>';
                    } else
                        echo '';
                    ?>
                </div>
            </li>


            <li>
                <div class="ui-block-a" style="width: 40%"><b>Job</b></div>
                <div class="ui-block-b" style="width:60%">
                    <?php echo isset($value['job_number'])?$value['job_number']:'';?>
                </div>
            </li>


            <li>
                <div class="ui-block-a" style="width: 40%"><b>Total sales</b></div>
                <div class="ui-block-b" style="width:60%">
                    <?php
                        if(isset($minimun) && $value['sum_sub_total'] < $minimun)
                            $value['sum_sub_total'] = $minimun;
                    ?>
                    <?php echo isset($value['sum_sub_total'])?$this->Common->format_currency($value['sum_sub_total']):'';?>
                </div>
            </li>

            <li>
                <div class="ui-block-a" style="width: 40%"><b>Status</b></div>
                <div class="ui-block-b" style="width:60%">
                    <?php echo isset($value['status'])?$value['status']:'';?>
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