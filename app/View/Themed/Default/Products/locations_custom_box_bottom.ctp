<?php
?>
<span class="bt_block float_right no_bg" style="width:70%;">
    <input class="input_w2 float_right" type="text" style=" margin:-2px 3% 0 0; width:12%; color:#333; text-align:right;" readonly="readonly" value="<?php if(isset($location_total['onpo'])) echo $location_total['onpo'];?>" />
    <div class="float_left" style="width:84%; margin:0;">
        <div class="dent_input float_right" style="width:86%; margin:0;">
            <input class="input_w2" type="text" style=" float:left;width:15%; text-align:right;" readonly="readonly" value="<?php if(isset($location_total['total'])) echo $location_total['total'];?>" />
            <input class="input_w2" type="text" style=" float:left;width:14%; text-align:right;" readonly="readonly" value="<?php if(isset($location_total['onso'])) echo $location_total['onso'];?>" />
            <input class="input_w2" type="text" style=" float:left;width:13%; text-align:right;" readonly="readonly" value="<?php if(isset($location_total['inuse'])) echo $location_total['inuse'];?>" />
            <input class="input_w2" type="text" style=" float:left;width:19%; text-align:right;" readonly="readonly" value="<?php if(isset($location_total['assembly'])) echo $location_total['assembly'];?>" />
            <input class="input_w2" type="text" style=" float:left;width:14%; text-align:right;" readonly="readonly" value="<?php if(isset($location_total['avalible'])) echo $location_total['avalible'];?>" />
            <input class="input_w2" type="text" style=" float:left;width:16%; text-align:right;" readonly="readonly" value="<?php if(isset($location_total['minstock'])) echo $location_total['minstock'];?>" />
        </div>
        <span class="float_left" style="width:14%; margin:0;">Totals</span>
    </div>
</span>
<span class="float_left bt_block">View</span>
<span class="float_left left_text">Note: Locations are created by movements.</span>