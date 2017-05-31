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
				<span class="fl_dent"><h4><?php echo translate('List of language'); ?></h4></span>
				<a title="Add new language" href="javascript:void(0)" onclick="$('#list_language').show()">
					<span class="icon_down_tl top_f"></span>
				</a>
			</span>
			<div class="title_small_once">
				<ul class="ul_mag clear bg3">
					<li class="hg_padd" style="width: 98%">
						<?php echo translate('Name'); ?>
						<span style="margin-left: 78%;margin-top: -15px;">Select</span>
					</li>
				</ul>
			</div>
			<div id="list_province_height" class="container_same_category" style="height: 449px;overflow-y: auto">

				<?php echo $this->Form->create('Setting'); ?>
				<table class="CssTable" id="list_language" style="display:none">
					<tr>
						<td>Language code:</td>
						<td><?php echo $this->Form->input('Setting.language_code'); ?></td>
					</tr>
					<tr>
						<td>Language name:</td>
						<td><?php echo $this->Form->input('Setting.language_name'); ?></td>
					</tr>
					<tr>
						<td colspan="2" align="center">
							<?php
								echo $this->Js->submit('Save', array(
								    'url' => URL.'/settings/list_laguage',
									'success' => "if(data=='All input must is not empty' || data=='This language is exited'){ alerts('Message',data); $('#SettingLanguageCode').focus(); return true; }else settings_list(this, 'language')",
									'div' => false
								));
							?>
							<input type="button" value="Cancel" onclick="$('#list_language').hide()" />
						</td>
					</tr>
				</table>
				<?php echo $this->Form->end(); ?>

					<?php
					$i = 0;
					foreach ($arr_language as $value) {
						if (!isset($value['lang']))
							continue;
						?>
						<ul class="find_list setup_menu">
							<li  onclick="settings_list_menu_detail(this, '<?php echo $value['value']; ?>')">

								<a href="javascript:void(0)" <?php if ($i == 0) { ?>class="active"<?php } ?> >
									<?php
									if (isset($value['lang'])) {
										echo $value['lang'];
									}
									?>
								</a>
								<span style="margin-left: 82%;">
									<input type="checkbox" style="margin-top: -16px;">
								</span>
							</li>
						</ul>
						<?php
						$i = 1;
					}
					?>

			</div>
			<span class="title_block bo_ra2"></span>
		</div><!--END Tab1 -->
	</div>
	<div class="clear_percent_9_arrow float_left">
		<div class="full_width box_arrow">
			<span class="icon_emp" style="cursor:default"></span>
		</div>
	</div>
	<div class="clear_percent_11 float_left" id="language_detail">
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

		$("li:first", "#list_province_height").click();
	});
	function settings_list_menu_detail(object, id) {
		$("#list_province_height a").removeClass("active");
		$("a", object).addClass("active");

		$.ajax({
			url: '<?php echo URL; ?>/settings/language_detail/' + id,
			timeout: 15000,
			success: function(html) {
				$("div#language_detail").html(html);
				$(function(){
					$('.container_same_category').mCustomScrollbar({
						scrollButtons:{
							enable:false
						}
					});
				})
				language_input_change();
			}
		});
	}

	function settings_language_add(id) {
		$.ajax({
			url: '<?php echo URL; ?>/settings/language_add/' + id,
			timeout: 15000,
			success: function(html) {
				$("div#language_detail").html(html);
				language_input_change();
			}
		});
	}

	function language_input_change() {
		$("form :input", "#language_detail").change(function() {
			language_input_change_update(this);

		});
	}

	function language_input_change_update(object) {
		console.log(object);
		var old = $(object).attr('rel');
		var name_change = $(object).attr('name');
		if(name_change=='Setting[key]')
			var data = $(object).closest('form').serialize()+"&name_change=1";
		else
			var data = $(object).closest('form').serialize();

		console.log(data);
		$.ajax({
			url: '<?php echo URL; ?>/settings/language_auto_save',
			timeout: 15000,
			type: "post",
			data: data,
			success: function(html) {

				console.log(html);
				if (html != "ok"){
					alerts("Error: ", html);
					$(object).val(old);
				}
			}
		});
	}

</script>


