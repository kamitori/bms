<?php 

?>
<span class="bt_block float_right no_bg" style="width:100%;">
    <div class="float_left" style="width:50%; margin-left:27%;">
        <div class="dent_input float_right" style="width:100%;">
            <input class="input_w2" type="text" style="width:11%; height: 100%;  margin:0 1% 0 0;color:#444; text-align:right" value="<?php if(isset($total_quantity)){echo $total_quantity;} else echo $total_quantity = 0; ?>" readonly="readonly" />
            <input class="input_w2" type="text" style="width:11%; height: 100%; margin:0 1% 0 0;color:#444; text-align:right" value="<?php if(isset($total_used)){echo $total_used;} else echo $total_used = 0; ?>" readonly="readonly" />
            <input class="input_w2" type="text" style="width:11%; margin:0 0 0 0;color:#444;text-align:right" value="<?php if(isset($total_balance)){echo $total_balance;} else echo $total_balance = 0; ?>" readonly="readonly" />
            <span class="float_left" style="width:12%;">Totals</span>
        </div>
    </div>
</span>
