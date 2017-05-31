<?php
	if(!isset($arr_view_st['edit']) || isset($arr_vls[$viewkeys.'_disable'])){
		if(isset($arr_vls[$viewkeys])){?>

			<a id="txt_<?php echo  $viewkeys.'_'.$arr_vls['_id'];?>" style="cursor:pointer; text-decoration:none;" <?php if(isset($arr_view_st['url'])) echo "href=\"".$arr_view_st['url']."\"";?>><?php echo $arr_vls[$viewkeys];?></a>
         <?php }?>

<?php }else if(isset($arr_vls) && is_array($arr_vls) && isset($viewkeys) && isset($arr_vls['_id'])){
?>
    <div class="jt_choice choice_codes" id="choices_<?php echo $viewkeys.'_'.$arr_vls['_id'];?>" title="Choice <?php echo $viewkeys;?>" rel="<?php if(isset($arr_vls['_id'])) echo $arr_vls['_id'];?>">
        <?php if(isset($arr_vls[$viewkeys])) echo $arr_vls[$viewkeys]; else echo " ";?>
        <?php if(isset($arr_vls[$viewkeys.'_disable'])) echo $arr_vls[$viewkeys.'_disable'];?>
    </div>
<?php }?>