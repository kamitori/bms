<?php 

?>
<span class="bt_block float_right no_bg">
    <div style="float:right; margin-right: 10%">
        <div class="dent_input float_right" style="width:100%; margin-right: 0% !important">
            <input class="input_w2" type="text" style="height: 100%; margin:-2px 0 0 0;color:#444;text-align:center" value="<?php if(isset($total)){echo $total;} else echo $total = 0; ?>" readonly="readonly" />
            <span class="float_left" style="width:13%; margin-right: 20px;">Totals</span>
        </div>
    </div>
</span>