<div class="clear_percent">
    <div class="clear_percent_19 float_left">
        <div class="tab_1 full_width">
            <span class="title_block bo_ra1">
                <span class="fl_dent"><h4><?php echo translate('Specify values for lists used in system'); ?></h4></span>
            </span>
            <div class="title_small_once">
                <ul class="ul_mag clear bg3">
                    <li class="hg_padd">
						<?php echo translate('List Province'); ?> <span class="normal">(<?php echo translate('click a name to view or edit on right'); ?>)</span>
                    </li>
                </ul>
            </div>
            <div id="permission_height" class="container_same_category" style="height: 449px;overflow-y: auto">
                <ul class="find_list setup_menu">
					<?php
					$i = 0;
					foreach ($permissions as $value) {
						?>
						<li onclick="permission_detail(this, '<?php echo $value['_id']; ?>')">
							<a href="javascript:void(0)" <?php if ($i == 0) { ?>class="active"<?php } ?> >
								<?php
								if (isset($value['controller'])) {
									echo $value['controller'];
								}
								?>
							</a>
						</li>
						<?php
						$i = 1;
					}
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
    <div class="clear_percent_11 float_left" id="list_permission_detail">
        <!-- Detail -->
    </div>
</div>

<script type="text/javascript">

	$(function() {
		$("li:first", "#permission_height").click(); // click menu li dau tien khi page load xong

	});

	function permission_detail(object, id) {
		$("#permission_height a").removeClass("active");
		$("a", object).addClass("active");
		$.ajax({
			url: '<?php echo URL; ?>/settings/permission_detail/' + id,
			timeout: 15000,
			success: function(html) {
				$("div#list_permission_detail").html(html);
			}
		});
	}
</script>