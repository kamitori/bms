
<div class="tab_1 full_width">
    <span class="title_block bo_ra1">
        <span class="fl_dent">
            <h4 id="setting_name">Cache Detail</h4>
        </span>
        <a title="Delete <?php echo $name ?>" href="javascript:void(0)" onclick="delete_cache('<?php echo $name; ?>')" style="text-decoration: none; font-weight: bold;"><span style="margin-top: 1px;">X</span></a>
    </span>
    <ul class="ul_mag clear bg3">
		<li class="hg_padd center_text" style="width:98%"><?php echo $name ?></li>
    </ul>
    <div class="container_same_category" style="height: 442px;overflow-y: auto;margin: 15px">
    	<textare style="word-wrap: break-word;" readonly><?php echo json_encode($cache_detail); ?></textare>
    </div>
</div>

