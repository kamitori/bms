<div class="tab_1 full_width">
    <span class="title_block bo_ra1">
        <span class="fl_dent"><h4>Related messages</h4></span>
    </span>
    <p class="clear"></p>
    <ul class="ul_mag clear bg3">
        <li class="hg_padd" style="width:1.5%"></li>
        <li class="hg_padd " style="width:45%">Message</li>
        <li class="hg_padd " style="width:5%">Date</li>
        <li class="hg_padd" style="width:5%">Time</li>
        <li class="hg_padd" style="width:15%">Message from</li>
        <li class="hg_padd" style="width:15%">Message to</li>
    </ul>
    <div style="overflow-y:scroll;<?php if(isset($option)&&$option) echo 'height:212px'; else echo 'height: 150px;'?>">
        <?php
            $i = 0;
            if(isset($data)&&$data->count() > 0)
            {
                foreach($data as $value)
                {
                    $bg = $i%2==0 ? 'bg1' : 'bg2';
        ?>
        <ul class="ul_mag clear <?php echo $bg; ?>">
            <li class="hg_padd" style="width:1.5%"><a href="<?php echo URL.'/'.$controller.'/entry/'.$value['_id'];?>"><span class="icon_emp"></span></a></li>
            <li class="hg_padd " style="width:45%;"><?php echo isset($value['content'])? $value['content'] : '' ?></li>
            <li class="hg_padd " style="width:5%"><?php echo date('M d, Y',$value['comms_date']->sec); ?></li>
            <li class="hg_padd" style="width:5%;text-align: center"><?php echo date('H:m',$value['comms_date']->sec); ?></li>
            <li class="hg_padd" style="width:15%"><?php echo $value['contact_from']; ?></li>
            <li class="hg_padd" style="width:15%"><?php echo $value['contact_to'] ?></li>
        </ul>
        <?php
                    $i++;
                }
            }
            $num = 8;
            if(isset($option)&&$option)
                $num = 11;
            if($i<$num)
            {
                for($j = $i; $j < $num-$i ; $j++)
                {
                    $bg = $j%2==0 ? 'bg1' : 'bg2';
                    echo '<ul class="ul_mag clear '.$bg.'"></ul>';
                }
            }
        ?>
    </div>
    <span class="title_block bo_ra2">
    </span>
</div>