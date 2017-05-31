<div class="block_dent2 container_same_category" style="overflow: auto;overflow-x: hidden;width:270px; margin: 0 auto; height:430px;">
    <table class="jt_tb" id="belong_to_module_window_popup" style="font-size:12px;">
        <thead style="position: fixed;" id="pagination_sort">
            <tr>
            	<th style="width:20px"></th>
                <th style="width:270px"><?php echo translate('Permission'); ?></th>
            </tr>
        </thead>
        <tbody>
            <tr>
            	<th style="width:20px"></th>
                <th style="width:270px">&nspb;</th>
            </tr>
            <?php
            $i = 0; $STT = 0;
            foreach ($all_permissions as $permission) {
                foreach($permission['permission'] as $key => $value){
                    foreach($value as $k => $v){
                        if($k != 'entry') continue;
                        foreach($v as $sub_v){
                            $i = 1 - $i; $STT += 1;
                            ?>
                            <tr class="jt_line_<?php if ($i == 1) { ?>black<?php } else { ?>light<?php } ?>">
                                <?php
                                    $permission_string = $permission['controller'].'_@_entry_@_'.$sub_v['codekey'];
                                    $checked = '';
                                    if(in_array($permission_string, $belong_to))
                                        $checked = 'checked';
                                ?>
                                <td align="center"><input class="check_permission" type="checkbox" data-permission="<?php echo $permission_string; ?>" <?php echo $checked; ?> /></td>
                                <td align="left" onclick="$('input[type=checkbox]',$(this).parent()).click()"><?php echo $permission['name'].' - Entry - '.$sub_v['name']; ?></td>
                            </tr>
            <?php 
                        }
                        break;
                    }
                }
            } 
            ?>
        </tbody>
    </table>

    <?php if( $STT == 0 ){ ?>
    <center style="margin-top:30px">(No data)</center>
    <?php } ?>
</div>
<script type="text/javascript">
    $(".container_same_category","#belong_to_module_window").mCustomScrollbar({
        scrollButtons:{
            enable:false
        },
        advanced:{
            updateOnContentResize: true,
            autoScrollOnFocus: false,
        }
    });
    $(".check_permission").change(function() {
        var check = 0;
        if($(this).is(":checked"))
            check = 1;
        var permission = $(this).attr("data-permission");
        $.ajax({
            url: "<?php echo URL.'/settings/privilege_detail_belong_to_module/'.$current_id.'/'.$current_key ?>",
            type: "POST",
            data: {permission: permission, check : check},
            success: function(result) {
                if(result != "ok")
                    alerts("Message",result);
            }
        })
    });
</script>
