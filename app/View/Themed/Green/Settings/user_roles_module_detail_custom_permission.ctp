<div class="block_dent2 container_same_category" style="overflow: auto;overflow-x: hidden;width:350px; margin: 0 auto; height:430px;">
    <table class="jt_tb" id="<?php echo $custom_permission; ?>_popup" style="font-size:12px;">
        <thead style="position: fixed;" id="pagination_sort">
            <tr>
            	<th style="width:20px"></th>
                <th style="width:90px"><?php echo translate('Ref no'); ?></th>
                <th style="width:180px"><?php echo translate('Employee'); ?></th>
            </tr>
        </thead>
        <tbody>
            <tr>
            	<th style="width:20px"></th>
                <th style="width:90px ">&nspb;</th>
                <th style="width:180px ">&nspb;</th>
            </tr>
            <?php
            $i = 0; $STT = 0;
            foreach ($arr_employees as $value) {
                $i = 1 - $i; $STT += 1;
                ?>
                <tr class="jt_line_<?php if ($i == 1) { ?>black<?php } else { ?>light<?php } ?>" id="tr_<?php echo $value['_id']; ?>" >
                    <td align="center"><input class="check_employees" name="<?php echo $value['_id']; ?>" type="checkbox" id="<?php echo $value['_id'] ?>" <?php if(isset($current_permission[$custom_permission]) && in_array($value['_id'], $current_permission[$custom_permission])) { ?> checked="checked" <?php } ?> /></td>
                    <td align="center" onclick="$('input[type=checkbox]',$(this).parent()).click()"><?php if(isset($value['no']))echo $value['no']; ?>&nbsp;</td>
                    <td align="left" onclick="$('input[type=checkbox]',$(this).parent()).click()"><?php echo $value['first_name'].' '.$value['last_name']; ?></td>
                </tr>
            <?php } ?>
        </tbody>
    </table>

    <?php if( $STT == 0 ){ ?>
    <center style="margin-top:30px">(No data)</center>
    <?php } ?>
</div>
<script type="text/javascript">
    $(".container_same_category","#custom_permission_window").mCustomScrollbar({
        scrollButtons:{
            enable:false
        },
        advanced:{
            updateOnContentResize: true,
            autoScrollOnFocus: false,
        }
    });
    $(".check_employees").change(function() {
        var check = 'false';
        if($(this).is(":checked"))
            check = 'true';
        var id = $(this).attr("id");
        $.ajax({
            url: "<?php echo URL.'/settings/custom_permission/' ?>"+check,
            type: "POST",
            data: {custom_permission: "<?php echo $custom_permission; ?>", id : id},
            success: function(result) {
                if(result != "ok")
                    alerts("Message",result);
            }
        })
    });
</script>
