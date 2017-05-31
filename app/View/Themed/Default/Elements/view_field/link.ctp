<?php
	if(!isset($arr_view_st['edit']) || isset($arr_vls[$viewkeys.'_disable'])){
		if(isset($arr_vls[$viewkeys])){?>

			<a id="txt_<?php echo  $viewkeys.'_'.$arr_vls['_id'];?>" style="cursor:pointer; text-decoration:none;" <?php if(isset($arr_view_st['url'])) echo "href=\"".$arr_view_st['url']."\"";?>><?php echo $arr_vls[$viewkeys];?></a>
         <?php }?>

<?php }else if(isset($arr_vls) && is_array($arr_vls) && isset($viewkeys) && isset($arr_vls['_id'])){
?>
    <div class="jt_choice choice_<?php echo $viewkeys;?>" id="choice_<?php echo $viewkeys.'_'.$arr_vls['_id'];?>" title="Choice <?php echo $viewkeys;?>" rel="<?php if(isset($arr_view_st['popup_key'])) echo $arr_view_st['popup_key'].'_'.$arr_vls['_id'];?>">
        <?php if(isset($arr_vls[$viewkeys])) echo $arr_vls[$viewkeys]; else echo " "?>
    </div>
    <script type="text/javascript">
        $(function(){
            window_popup('<?php if(isset($arr_view_st['module_rel'])) echo $arr_view_st['module_rel'];?>', '<?php if(isset($arr_view_st['popup_title'])) echo $arr_view_st['popup_title'];?>','<?php if(isset($arr_view_st['popup_key'])) echo $arr_view_st['popup_key'].'_'.$arr_vls['_id'];?>', 'choice_<?php echo $viewkeys.'_'.$arr_vls['_id'];?>', <?php if(isset($arr_view_st['para'])) echo $arr_view_st['para']; else echo "''";?>);
        });
    </script>

<?php }?>