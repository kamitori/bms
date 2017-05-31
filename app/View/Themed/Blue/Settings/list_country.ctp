<style type="text/css">
table.CssTable {
	width:100%
}
table.CssTable td {
	border-bottom: 1px solid #D0D7E5;
	padding: 8px;
}
</style>
<div class="clear_percent">
	<div class="clear_percent_19 float_left">
		<div class="tab_1 full_width">
			<span class="title_block bo_ra1">
				<span class="fl_dent"><h4><?php echo translate('List of countries'); ?></h4></span>
				<a title="Add new country" href="javascript:void(0)" onclick="$('#list_province_add_country').show()">
					<span class="icon_down_tl top_f"></span>
				</a>
			</span>
			<div class="title_small_once">
				<ul class="ul_mag clear bg3">
					<li class="hg_padd">
						<?php echo translate('Name'); ?>
					</li>
				</ul>
			</div>
			<div id="list_province_height" class="container_same_category" style="height: 449px;overflow-y: auto">

				<?php echo $this->Form->create('Setting'); ?>
				<table class="CssTable" id="list_province_add_country" style="display:none">
					<tr>
						<td>Country code:</td>
						<td><?php echo $this->Form->input('Setting.country_code'); ?></td>
					</tr>
					<tr>
						<td>Country name:</td>
						<td><?php echo $this->Form->input('Setting.country_name'); ?></td>
					</tr>
					<tr>
						<td colspan="2" align="center">
							<?php
								echo $this->Js->submit('Save', array(
								    'url' => URL.'/settings/list_province_add_country',
									'success' => 'settings_list(this, "list_country");',
									'div' => false
								));
							?>
							<input type="button" value="Cancel" onclick="$('#list_province_add_country').hide()" />
						</td>
					</tr>
				</table>
				<?php echo $this->Form->end(); ?>

				<ul class="find_list setup_menu">
					<?php
					$i = 0;
					foreach ($arr_countrys as $value) {
						if (!isset($value['name']))
							continue;
						?>
						<li onclick="settings_list_menu_detail(this, '<?php echo $value['value']; ?>')">
							<a href="javascript:void(0)" <?php if ($i == 0) { ?>class="active"<?php } ?> >
								<?php
								if (isset($value['name'])) {
									echo $value['name'];
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
			<span class="icon_emp" style="cursor:default"></span>
		</div>
	</div>
	<div class="clear_percent_11 float_left" id="list_province_detail">
		<!-- Detail -->
	</div>
</div>

<script type="text/javascript">

	$(function() {

		$(".container_same_category").mCustomScrollbar({
			scrollButtons:{
				enable:false
			}
		});

		$("li:first", "#list_province_height").click(); // click menu li dau tien khi page load xong
	});

	function settings_add_new_country(){
		//
	}

	function settings_list_menu_detail(object, id) {

		$("#list_province_height a").removeClass("active");
		$("a", object).addClass("active");

		$.ajax({
			url: '<?php echo URL; ?>/settings/list_province_detail/' + id,
			timeout: 15000,
			success: function(html) {

				$("div#list_province_detail").html(html);

				$(function(){
					$('.container_same_category').mCustomScrollbar({
						scrollButtons:{
							enable:false
						}
					});
				});

				list_province_detail_input_change();
			}
		});
	}


	function settings_list_province_add(id) {
		$.ajax({
			url: '<?php echo URL; ?>/settings/list_province_add/' + id,
			timeout: 15000,
			success: function(html) {
				$("div#list_province_detail").html(html);
				list_province_detail_input_change();
			}
		});
	}


	function list_province_detail_input_change() {
		$("form :input", "#list_province_detail").change(function() {
			list_province_detail_input_change_update(this);

		});
	}
	;

	function list_province_detail_input_change_update(object) {
		$.ajax({
			url: '<?php echo URL; ?>/settings/list_province_detail_auto_save',
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

</script>