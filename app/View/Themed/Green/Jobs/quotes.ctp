    <div class="tab_1 full_width">
        <span class="title_block bo_ra1">
            <span class="fl_dent"><h4><?php echo translate('Quotations for this job'); ?></h4></span>
             <?php if($this->Common->check_permission('quotations_@_entry_@_add',$arr_permission)): ?>
            <a href="<?php echo URL; ?>/jobs/quotes_add/<?php echo $job_id; ?>" title="Add new quotation">
                <span class="icon_down_tl top_f"></span>
            </a>
            <?php endif; ?>
        </span>
        <p class="clear"></p>
        <ul class="ul_mag clear bg3">
            <li class="hg_padd" style="width:6%;"></li>
            <li class="hg_padd" style="width:6%;"><?php echo translate('Ref no'); ?></li>
            <li class="hg_padd" style="width:8%;"><?php echo translate('Type'); ?></li>
            <li class="hg_padd center_txt" style="width:6%;"><?php echo translate('Date'); ?></li>
            <li class="hg_padd" style="width:6%;"><?php echo translate('Due date'); ?></li>
            <li class="hg_padd" style="width:8%;"><?php echo translate('Status'); ?></li>
            <li class="hg_padd" style="width:10%;"><?php echo translate('Our rep'); ?></li>
            <li class="hg_padd" style="width:10%;"><?php echo translate('Tax'); ?></li>
            <li class="hg_padd" style="width:28%;"><?php echo translate('Heading'); ?></li>
            <li class="hg_padd bor_mt" style="width:1%;"></li>
        </ul>
        <div class="container_same_category" style="height: 200px;overflow-y: auto;">
            <?php
            $i = 1; $count = 0;
            $total_sub_total = $total_tax = $total_amount = 0;
            foreach ($arr_quotation as $key => $value) {
                $sub_total = (isset($value['sum_sub_total'])&&$value['sum_sub_total'] ? $value['sum_sub_total'] : 0);
                $tax = (isset($value['sum_tax'])&&$value['sum_tax'] ? $value['sum_tax'] : 0);
                $amount = (isset($value['sum_amount'])&&$value['sum_amount'] ? $value['sum_amount'] : 0);
                if($value['quotation_status']!='Cancelled'){
                    $total_sub_total += $sub_total;
                    $total_tax += $tax;
                    $total_amount += $amount;
                }
            ?>
            <ul class="ul_mag clear bg<?php echo $i; ?>" id="Job_Quotation_<?php echo $value['_id']; ?>">

                <li class="hg_padd" style="width:6%;">
                    <a href="<?php echo URL; ?>/quotations/entry/<?php echo $value['_id']; ?>">
                        <span class="icon_emp"></span>
                    </a>
                </li>
                <li class="hg_padd" style="width:6%;"><?php echo $value['code']; ?></li>
                <li class="hg_padd" style="width:8%;"><?php echo $value['quotation_type']; ?></li>
                <li class="hg_padd center_txt" style="width:6%;"><?php echo $this->Common->format_date($value['quotation_date']->sec,false); ?></li>
                <li class="hg_padd" style="width:6%;"><?php echo $this->Common->format_date($value['payment_due_date']->sec,false); ?></li>
                <li class="hg_padd" style="width:8%;"><?php echo $value['quotation_status']; ?></li>
                <li class="hg_padd" style="width:10%;"><?php echo $value['our_rep']; ?></li>
                <li class="hg_padd right_txt" style="width:10%;"><?php echo (isset($arr_tax[$value['tax']]) ? $arr_tax[$value['tax']] : ''); ?></li>
                <li class="hg_padd" style="width:28%;"><?php echo $value['name']; ?></li>
                <?php if($this->Common->check_permission('quotations_@_entry_@_delete',$arr_permission)): ?>
                <li class="hg_padd bor_mt" style="width:1%;">
                    <div class="middle_check">
                        <a title="Delete link" href="javascript:void(0)" onclick="resource_remove_quotation_job('<?php echo $value['_id']; ?>')">
                            <span class="icon_remove2"></span>
                        </a>
                    </div>
                </li>
                <?php endif; ?>
            </ul>

            <?php $i = 3 - $i; $count += 1;
                }

                $count = 8 - $count;
                if( $count > 0 ){
                    for ($j=0; $j < $count; $j++) { ?>
                    <ul class="ul_mag clear bg<?php echo $i; ?>">
                        <li class="hg_padd" style="width:6%;"></li>
                        <li class="hg_padd" style="width:6%;"></li>
                        <li class="hg_padd" style="width:8%;"></li>
                        <li class="hg_padd center_txt" style="width:6%;"></li>
                        <li class="hg_padd" style="width:6%;"></li>
                        <li class="hg_padd" style="width:8%;"></li>
                        <li class="hg_padd" style="width:10%;"></li>
                        <li class="hg_padd" style="width:10%;"></li>
                        <li class="hg_padd" style="width:28%;"></li>
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
                <span class="float_left">Total Amount</span>
                <input class="input_7 right_txt" style="width:90px" value="<?php echo $this->Common->format_currency($total_amount); ?>" readonly="readonly" type="text">
            </span>
            <span class="bt_block float_left no_bg" style="float: right;">
                <span class="float_left">Total Tax</span>
                <input class="input_7 right_txt" style="width:90px" value="<?php echo $this->Common->format_currency($total_tax); ?>" readonly="readonly" type="text">
            </span>
            <span class="bt_block float_left no_bg" style="float: right;">
                <span class="float_left">Total Sub total</span>
                <input class="input_7 right_txt" style="width:90px" value="<?php echo $this->Common->format_currency($total_sub_total); ?>" readonly="readonly" type="text">
            </span>
        </span>
    </div>

<?php if($this->Common->check_permission('quotations_@_entry_@_edit',$arr_permission)): ?>
<script type="text/javascript">
function resource_remove_quotation_job(quotation_id){
    confirms( "Message", "Are you sure you want to delete?",
        function(){
            $.ajax({
                 url: '<?php echo URL; ?>/jobs/quotes_delete/' + quotation_id,
                 timeout: 15000,
                 success: function(html){
                     if(html == "ok"){
                         $("#Job_Quotation_" + quotation_id).fadeOut();
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
<?php endif; ?>