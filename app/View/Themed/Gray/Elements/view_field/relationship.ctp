<span class="width_blo" id="valuereturn_<?php if(isset($viewkeys)) echo $viewkeys;?>_<?php if(isset($arr_vls['_id'])) echo $arr_vls['_id'];?>" style=" <?php if(isset($arr_view_st['css'])) echo $arr_view_st['css'];?>">
	<?php echo $arr_vls[$viewkeys];?>
</span>
<span class="icon_down_new float_right no_top change_<?php if(isset($viewkeys)) echo $viewkeys;?>" id="click_open_window_change_<?php if(isset($viewkeys)) echo $viewkeys;?>_<?php echo $arr_vls['_id'];?>" title="<?php if(isset($arr_view_st['title'])) echo $arr_view_st['title'];?>"></span>

<?php if(isset($arr_view_st['cls'])){?>
	<script type="text/javascript">
        $(function(){
            window_popup('<?php echo $arr_view_st['cls'];?>', 'Specify <?php if(isset($arr_view_st['name'])) echo $arr_view_st['name'];?> for change','change_<?php echo $arr_vls['_id'];?>','click_open_window_change_<?php if(isset($viewkeys)) echo $viewkeys;?>_<?php if(isset($arr_vls['_id'])) echo $arr_vls['_id'];?>'<?php if(isset($arr_view_st['para'])) echo $arr_view_st['para'];?>);
        });
    </script>
<?php }?>