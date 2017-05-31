<div class="clear_percent">
    <div class="clear_percent_19 float_left">
        <div class="tab_1 full_width">
            <span class="title_block bo_ra1">
                <span class="fl_dent"><h4><?php echo translate('Specify values for lists used in system'); ?></h4></span>
            </span>
            <div class="title_small_once">
                <ul class="ul_mag clear bg3">
                    <li class="hg_padd">
						<?php echo translate('List name'); ?> <span class="normal">(<?php echo translate('click a name to view or edit on right'); ?>)</span>
                    </li>
                </ul>
            </div>
            <div id="system_heigh" class="container_same_category" style="height: 449px;overflow-y: auto">
                <ul class="find_list setup_menu">
					<?php
						$i = 0;
						foreach($arr_system as $val):
					?>
					<li onclick="sys_list_ajax(this,'<?php echo $val['_id'] ?>')">
						<a href="javascript:void(0)" <?php if ($i == 0) { ?>class="active"<?php } ?> >
							<?php
								echo ucwords(str_replace('_', ' ', $val['setting_name']));
							?>
						</a>
					</li>
					<?php
						$i = 1;
						endforeach;
					?>
                </ul>
            </div>
            <span class="title_block bo_ra2"></span>
        </div><!--END Tab1 -->
    </div>
    <div class="clear_percent_9_arrow float_left">
        <div class="full_width box_arrow">
            <span class="icon_emp"></span>
        </div>
    </div>
    <div class="clear_percent_11 float_left" id="system_detail">
        <!-- Detail -->
    </div>
</div>

<script type="text/javascript">

	$(function() {
		$("li:first", "#system_heigh").click(); // click menu li dau tien khi page load xong
	});

	function sys_list_ajax(object,id) {

		$("#system_heigh a").removeClass("active");
		$("a",object).addClass("active");

		$.ajax({
			url: '<?php echo URL; ?>/settings/system_detail/' + id,
			timeout: 15000,
			success: function(html) {

				$("div#system_detail").html(html);

				// ----------- kendo color picker --------
				settings_kendo_enable_colorpicker();
				// ----------- end --- kendo color picker --------

				system_detail_input_change();
			}
		});
		return false;
	}

	function system_detail_input_change() {
		$("form :input", "#system_detail").change(function() {
			system_detail_input_change_update(this);

		});
	}
	;

	function system_detail_input_change_update(object) {
		$.ajax({
			url: '<?php echo URL; ?>/settings/system_detail_auto_save',
			timeout: 15000,
			type: "post",
			data: $(object).closest('form').serialize(),
			success: function(html) {
				console.log(html);
				if (html != "ok")
					alerts("Error: ", html);
			}
		});
	}

	function system_detail_add(id) {
		$.ajax({
			url: '<?php echo URL; ?>/settings/system_detail_add/' + id,
			timeout: 15000,
			success: function(html) {
				$("div#system_detail").html(html);
				settings_kendo_enable_colorpicker();
				system_detail_input_change();
			}
		});
	}



</script>