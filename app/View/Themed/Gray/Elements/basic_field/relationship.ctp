<input name="<?php echo $keys;?>" id="<?php echo $keys;?>" class="input_1 float_left <?php if(isset($arr_field['more_in_class'])) echo $arr_field['more_in_class'];?> <?php if(isset($search_class)) echo $search_class;?>" <?php if(isset($search_flat)) echo $search_flat;?> type="text" value="<?php if(isset($arr_field['default'])) echo $arr_field['default'];?>" <?php if(isset($arr_field['element_input'])) echo $arr_field['element_input'];?> style=" <?php if(isset($arr_field['css'])) echo $arr_field['css'];?>" <?php if(isset($arr_field['not_custom']) && $arr_field['not_custom']=='1'){?> readonly="readonly" <?php }?> <?php if(isset($arr_field['lock']) && $arr_field['lock']=='1' || isset($arr_field['readonly']) && $arr_field['readonly']=='readonly'){?>readonly="readonly"<?php }?> />
<?php if(!isset($arr_field['hide_button'])) { ?>
<span class="iconw_m indent_dw_m" id="click_open_window_<?php if(isset($arr_field['cls'])) echo $arr_field['cls'];?><?php echo $keys;?>" title="Specify <?php echo $arr_field['name'];?>" <?php if(isset($arr_field['lock']) && $arr_field['lock']=='1'){?> onclick="alerts('Message','<?php msg('STATUS_LOG');?>');"<?php }?>></span>
<?php } ?>
<?php if(isset($arr_field['null'])) { ?>
<span class="icon_remove2" style="margin: -18px 20px 0; float: right;" id="set_empty_<?php echo $keys;?>" title="Empty" <?php if(isset($arr_field['lock']) && $arr_field['lock']=='1'){?> onclick="alerts('Message','<?php msg('STATUS_LOG');?>');"<?php }?>></span>
<script type="text/javascript">
	$(function(){
		$("#set_empty_<?php echo $keys;?>").click(function(){
			$(".link_to_<?php echo $keys;?>").removeClass("jt_link_on");
			$("#<?php echo $keys;?>_id").val("");
			$("#<?php echo $keys;?>").val("").trigger("change");
		});
	});
</script>
<?php } ?>
<?php if(isset($arr_field['moreinline'])) echo $arr_field['moreinline'];?>

<?php if(isset($arr_field['cls']) && !(isset($arr_field['lock']) && $arr_field['lock']=='1')){?>
	<script type="text/javascript">
        $(function(){
            window_popup('<?php echo $arr_field['cls'];?>', 'Specify <?php if(isset($arr_field['name'])) echo $arr_field['name'];?>','<?php echo $keys;?>','click_open_window_<?php if(isset($arr_field['cls'])) echo $arr_field['cls'];?><?php echo $keys;?>'<?php if(isset($arr_field['para'])) echo $arr_field['para'];?>);
        });
    </script>
<?php }?>