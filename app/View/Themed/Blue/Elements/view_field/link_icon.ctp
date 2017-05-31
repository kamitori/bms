<?php
    $css_class = '';
    if(isset($arr_vls['xcss_element'][$viewkeys]))
        $css_class=$arr_vls['xcss_element'][$viewkeys];
	if(isset($arr_view_st['link_field']))
		$link_field = $arr_view_st['link_field'];
	else
		$link_field = 'id';

	if(!isset($arr_view_st['edit']) || isset($arr_vls[$viewkeys.'_disable'])){

		if(isset($arr_vls[$viewkeys])){?>
			<a id="txt_<?php echo  $viewkeys.'_'.$arr_vls['_id'];?>" style="cursor:pointer; text-decoration:none;" <?php if(isset($arr_view_st['url'])) echo "href=\"".$arr_view_st['url']."\"";?> <?php if($css_class!='') echo 'class="'.$css_class.'"'; ?>><?php echo $arr_vls[$viewkeys];?></a>
         <?php }?>

		 <?php if(isset($arr_vls[$link_field])){?>
         	<a href="<?php echo URL.'/'.$arr_view_st['module_rel'];?>/entry/<?php echo $arr_vls[$link_field];?>">
            	<span class="icon_linkleft" title="View"></span>
           	</a>
         <?php }?>

<?php }else if(isset($arr_vls) && is_array($arr_vls) && isset($viewkeys) && isset($arr_vls['_id'])){?>

    <div class="jt_choice_2 choice_<?php echo $viewkeys;?> <?php echo $css_class ?>" <?php if(isset($arr_vls['xstyle_element'][$viewkeys])) echo 'style="'.$arr_vls['xstyle_element'][$viewkeys].'"'; ?> id="choice_<?php echo $viewkeys.'_'.$arr_vls['_id'];?> " title="Choice <?php echo $viewkeys;?>" rel="<?php if(isset($arr_view_st['popup_key'])) echo $arr_view_st['popup_key'].'_'.$arr_vls['_id'];?>" onclick="$('#product_choice_sku').click();$('#product_choice_sku').val('<?php if(isset($arr_view_st['popup_key'])) echo $arr_view_st['popup_key'].'_'.$arr_vls['_id'];?>');">
        <?php if(isset($arr_vls[$viewkeys])) echo $arr_vls[$viewkeys]; else echo " ";?>

    </div>

    <?php if(isset($arr_vls[$link_field]) && strlen((string)$arr_vls[$link_field])==24){?>
        <div class="icon_links_more">
            <a href="<?php echo URL.'/'.$arr_view_st['module_rel'];?>/entry/<?php echo $arr_vls[$link_field];?>">
                <span class="icon_linkleft" title="View" onclick=""></span>
            </a>
        </div>
    <?php }?>

<?php }?>