<div class="logo float_left">
    <div class="back">
        <a <?php if(isset($return_id)) echo 'id="'.$return_id.'"'; ?> href="<?php if(isset($return_link)) echo $return_link; else echo 'javascript:void(0);';?>">
            <span class="back_icon"></span>
            <p>Return</p>
        </a>
    </div>
    <div class="top_opt float_left" style="max-width: 316px;">
        <h2><?php if(isset($return_title)) echo $return_title;?></h2>
    </div>
</div>