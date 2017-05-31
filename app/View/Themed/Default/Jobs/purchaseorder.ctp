    <div class="tab_1 full_width">
        <span class="title_block bo_ra1">
            <span class="fl_dent"><h4><?php echo translate('Purchase orders for this job'); ?></h4></span>
             <?php if($this->Common->check_permission('purchaseorders_@_entry_@_add',$arr_permission)): ?>
            <a href="<?php echo URL; ?>/jobs/purchasesorder_add/<?php echo $job_id; ?>" title="Add new purchase order">
                <span class="icon_down_tl top_f"></span>
            </a>
            <?php endif; ?>
        </span>
        <p class="clear"></p>
        <ul class="ul_mag clear bg3">
            <li class="hg_padd" style="width:1%;"></li>
            <li class="hg_padd" style="width:8%;"><?php echo translate('Order no'); ?></li>
            <li class="hg_padd center_txt" style="width:10%;"><?php echo translate('Date'); ?></li>
            <li class="hg_padd" style="width:15%;"><?php echo translate('Status'); ?></li>
            <li class="hg_padd" style="width:25%;"><?php echo translate('Supplier'); ?></li>
            <li class="hg_padd" style="width:17%;"><?php echo translate('Our rep'); ?></li>
            <li class="hg_padd" style="width:12%;text-align: right;"><?php echo translate('Sub total'); ?></li>
            <li class="hg_padd bor_mt" style="width:1%;"></li>
        </ul>
        <div class="container_same_category" style="height: 200px;overflow-y: auto;">
            <?php
            $i = 1; $count = 0;
            $total_sub_total = 0;
            foreach ($arr_purchaseorders as $key => $value) {
                $sub_total = (isset($value['sum_sub_total'])&&$value['sum_sub_total'] ? $value['sum_sub_total'] : 0);
                if($value['purchase_orders_status']!='Cancelled')
                    $total_sub_total += $sub_total;
            ?>
            <ul class="ul_mag clear bg<?php echo $i; ?>" id="Job_PO_<?php echo $value['_id']; ?>">

                <li class="hg_padd" style="width:1%;">
                    <a href="<?php echo URL; ?>/purchaseorders/entry/<?php echo $value['_id']; ?>">
                        <span class="icon_emp"></span>
                    </a>
                </li>
                <li class="hg_padd" style="width:8%;"><?php echo $value['code']; ?></li>
                <li class="hg_padd center_txt" style="width:10%;"><?php echo $this->Common->format_date($value['purchord_date']->sec,false); ?></li>
                <li class="hg_padd" style="width:15%;"><?php echo $value['purchase_orders_status']; ?></li>
                <li class="hg_padd" style="width:25%;"><?php echo $value['company_name']; ?></li>
                <li class="hg_padd" style="width:17%;"><?php echo $value['our_rep']; ?></li>
                <li class="hg_padd" style="width:12%;text-align: right;"><?php echo $this->Common->format_currency($sub_total); ?></li>
                <li class="hg_padd bor_mt" style="width:1%;">
                    <div class="middle_check">
                        <a title="Delete link" href="javascript:void(0)" onclick="remove_purchaseorder_job('<?php echo $value['_id']; ?>')">
                            <span class="icon_remove2"></span>
                        </a>
                    </div>
                </li>
            </ul>

            <?php $i = 3 - $i; $count += 1;
                }

                $count = 8 - $count;
                if( $count > 0 ){
                    for ($j=0; $j < $count; $j++) { ?>
                    <ul class="ul_mag clear bg<?php echo $i; ?>">
                        <li class="hg_padd" style="width:1%;"></li>
                        <li class="hg_padd" style="width:8%;"></li>
                        <li class="hg_padd center_txt" style="width:10%;"></li>
                        <li class="hg_padd" style="width:15%;"></li>
                        <li class="hg_padd" style="width:25%;"></li>
                        <li class="hg_padd" style="width:17%;"></li>
                        <li class="hg_padd" style="width:12%;text-align: right;"></li>
                        <li class="hg_padd bor_mt" style="width:1%;"></li>
                    </ul>
              <?php $i = 3 - $i;
                    }
                }
            ?>
        </div>

        <span class="title_block bo_ra2">
            <span class="float_left bt_block">Click to view full details</span>
            <span class="bt_block float_left no_bg" style="float: right;">
                <span class="float_left">Total Sub total</span>
                <input class="input_7 right_txt" style="width:90px" value="<?php echo $this->Common->format_currency($total_sub_total); ?>" readonly="readonly" type="text">
            </span>
        </span>
    </div>

<script type="text/javascript">
function remove_purchaseorder_job(purchaseorder_id){
    confirms( "Message", "Are you sure you want to delete?",
        function(){
            $.ajax({
                 url: '<?php echo URL; ?>/jobs/purchaseorder_delete/' + purchaseorder_id,
                 timeout: 15000,
                 success: function(html){
                     if(html == "ok"){
                         $("#Job_PO_" + purchaseorder_id).fadeOut();
                     }else{
                         alerts("Error: ", html);
                     }
                 }
             });
        },function(){
            //else do somthing
    });
}
</script>
