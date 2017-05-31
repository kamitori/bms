<div class="tab_1 full_width">
    <span class="title_block bo_ra1">
        <span class="fl_dent">
            <h4>
                <?php echo $hook['name'];?>
            </h4>
        </span>
        <a title="Add new hook option" href="javascript:void(0)" id="add_new_hook_option">
            <span class="icon_down_tl top_f"></span>
        </a>
    </span>
    <ul class="ul_mag clear bg3">
    	<?php
            $sample_hook = reset($hook['options']);
    		$width = 94/count( $sample_hook);
    		foreach( $sample_hook as $key => $value)
    			echo '<li class="hg_padd" style="width:'.$width.'%">'.ucfirst(str_replace('_', ' ', $key)).'</li>';
    	?>
        <li class="hg_padd" style="width:2%"></li>
    </ul>
    <input type="hidden" id="all_field_of_option" >
    <div class="hook_content" style="height:449px">
        <?php foreach($hook['options'] as $key=>$option){ ?>
        <ul class="ul_mag clear bg1">
        	<?php foreach($option as $option_name => $option_value){ ?>
            <li class="hg_padd line_mg" style="width:<?php echo $width ?>%; position: relative">
                <?php
                echo $this->Form->input($option_name.'_'.$key, array(
                    'class' => 'input_inner input_inner_w bg1',
                    'value' => $option_value,
                    'name'  => $option_name.'_'.$key
                ));
                ?>
            </li>
            <?php } ?>
            <li class="hg_padd center_text delete_hook_option" style="width:2%" id="hook_option_<?php echo $key; ?>">
                <div class="middle_check">
                    <span class="icon_remove2"></span>
                </div>
            </li>
        </ul>
        <?php } ?>
    </div>

    <span class="title_block bo_ra2">
        <span class="float_left bt_block"><?php echo translate('Edit values for list'); ?>.</span>
    </span>
</div>
<script type="text/javascript">
$(function(){
    $("#add_new_hook_option").click(function(){
        $.ajax({
            url:"<?php echo URL; ?>/settings/hook_option_add/",
            type: 'POST',
            data: {hook_id : "<?php echo $hook['_id']; ?>"},
            success: function(result){
                if(result!='ok')
                    alerts("Message",result);
                else
                    $(".active",".hook_menu").click();
            }
        });
    });
    $(".delete_hook_option").click(function(){
        var id = $(this).attr("id");
        id = id.split("_");
        id = id[id.length - 1];
        $.ajax({
            url:"<?php echo URL; ?>/settings/hook_option_delete/",
            type: 'POST',
            data: {hook_id : "<?php echo $hook['_id']; ?>", key : id},
            success: function(result){
                if(result!='ok')
                    alerts("Message",result);
                else
                    $("#hook_option_"+id).parent().fadeOut();
            }
        });
    });
	$("input",".hook_content").change(function(){
		$.ajax({
			url:"<?php echo URL; ?>/settings/hook_option_save/",
			type: 'POST',
			data: {hook_id : "<?php echo $hook['_id']; ?>", key : $(this).attr("name"), value : $(this).val()},
			success: function(result){
				if(result!='ok')
					alerts("Message",result);
			}
		});
	});
});
</script>