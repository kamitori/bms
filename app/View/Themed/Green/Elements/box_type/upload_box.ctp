<?php
	if(!isset($block))
		$block =  $arr_settings['relationship'][$sub_tab]['block'][$blockname]['field'];
?>

<?php if(isset($img_path)){?>
    <div style="height:312px; margin:auto; line-height: 312px;text-align: center;display:list-item;list-style-type: none;">
        <a href="<?php echo URL.'/docs/entry/'.$doc_id;?>">
        	<img src="<?php echo URL.$img_path;?>" alt="Images" style=" max-height:100%; max-width:100%;vertical-align: middle;" />
        </a>
    </div>
<?php }else{?>

    <div style=" width:100%; height:100%; display:table; text-align:center; font-size:11px; vertical-align:middle; <?php if(isset($block['files']['css'])) echo $block['files']['css']; ?>">
        <?php if(isset($block['files']['name'])) echo ''.$block['files']['name']; ?>
    </div>

<?php }?>
