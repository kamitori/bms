<div class="new_select_black" style="width:30%; float:right; margin-right:8px;">
    <div class="new_select_black_left">&nbsp;</div>
    <div class="new_select_black_center" style="width:97%;">
        <!--<a href=""><span class="new_select_black_icon_search"></span></a>-->
        <input type="text" id="line_entry_list" combobox_blank="1" value="<?php if(isset($line_entry_value)) echo $line_entry_value;?>" />
        <input type="hidden" name="line_entry_list" id="line_entry_listId" value="<?php if(isset($line_entry_id)) echo $line_entry_id;?>" />
        <!--<a href=""><div class="new_select_black_icon_plus"></div></a>-->
    </div>
    <div class="new_select_black_right">&nbsp;</div>
</div>

<script type="text/javascript">
    $(function () {
        $("#line_entry_list").combobox(<?php if(isset($list_line_entry)) echo $list_line_entry;?>);
        $("#line_entry_list").change(function(){
            $.ajax({
                url: '<?php echo URL.'/'.$controller;?>/sub_tab/asset_tags/<?php echo $iditem;?>',
                type: 'post',
                data: {data:$("#line_entry_listId").val()},
                success: function(html){
                    $("#load_subtab").stop().html(html);
                    ajax_note("");
                }
            });
        });
    });
</script>