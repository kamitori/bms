<div class="tab_1 full_width">
    <span class="title_block bo_ra1">
        <span class="fl_dent">
            <h4 id="setting_name"><?php
				if (isset($arr_language['lang'])) {
					echo $arr_language['lang'];
				}
				?>
            </h4>
        </span>

        <!--
		<div class="search">
			<form id="search_product" action="" onsubmit="search_product(); return false;">
				<input id="search_product_input" type="text" placeholder="Search barcode">
				<input type="submit">
			</form>
		</div>
 		-->
		<!-- Minh code -->
			<span class="float_right">
				<span class="block_sear bl_t">
					<span class="bg_search_1"></span>
					<span class="bg_search_2"></span>
					<div class="box_inner_search float_left" style="width: 95px;">
						<a><span class="icon_search"></span></a>
							<span class="combobox" style="position:relative; display:-moz-inline-box; display:inline-block; height: 17px;">
								<input class="input_select input-search-listbox" type="text" id="lang_search_input"  style=" height:79%; margin: 0px 14px 0px 0px; width: 109%; line-height: 16px;" placeholder="Search" onchange="lang_search();" />
							</span>
					</div>
				</span>
			</span>
		<!-- End Minh Code -->

        <a title="Add new content"href="javascript:void(0)" onclick="settings_language_add('<?php echo $arr_language['value']; ?>')">
            <span class="icon_down_tl top_f"></span>
        </a>

    </span>
    <ul class="ul_mag clear bg3">
		<li class="hg_padd center_text" style="width:5%">ID</li>
		<li class="hg_padd " style="width:20%"> Key Language</li>
		<li class="hg_padd " style="width:32%">English </li>
		<li class="hg_padd " style="width:36%"><?php echo $arr_language['lang']; ?></li>
    </ul>
    <div id="lang_search_div" class="container_same_category" style="height: 449px;overflow-y: auto" id="language_detail">
		<?php
		$stt = 1;
		$i = 1;
		$count = 0;
		$keylang = $arr_language['value'];

		foreach ($arr_language_detail as $value) {
			$i = 3 - $i; $count += 1;
			?>
			<?php echo $this->Form->create('Setting'); ?>
			<input type="hidden" name="Setting[_id]" value="<?php echo $value['_id']; ?>" />
			<ul class="ul_mag clear bg<?php echo $i; ?>">
				<li class="hg_padd center_text" style="width:5%"><?php echo $stt++; ?></li>
				<li class="hg_padd center_text" style="width:20%">
				<input type="text" name="Setting[key]" value="<?php echo isset($value['key'])?$value['key']:''; ?>"  class="input_inner bg<?php echo $i; ?>" rel="<?php echo isset($value['key'])?$value['key']:''; ?>" />
				</li>
				<li class="hg_padd center_text" style="width:32%">
					<input type="text" name="Setting[content][en]" value="<?php echo $value['content']['en']; ?>"  class="input_inner bg<?php echo $i; ?>"/>
				</li>
				<li class="hg_padd center_text" style="width:36%">
				<input type="text" name="Setting[content][<?php echo $keylang;?>]" value="<?php echo isset($value['content'][$keylang])?$value['content'][$keylang]:''; ?>"  class="input_inner bg<?php echo $i; ?>"/>
				</li>
			</ul>
			<?php echo $this->Form->end(); ?>
		<?php }
		?>

		<?php
		if ($count < 20) {
			$count = 20 - $count;
			for ($j = 0; $j < $count; $j++) {
				$i = 3 - $i;
				?>
				<ul class="ul_mag clear bg<?php echo $i; ?>">
				</ul>
				<?php
			}
		}
		?>
    </div>
    <span class="title_block bo_ra2">
        <span class="float_left bt_block"><?php echo translate('Edit or create values for list'); ?>.</span>
    </span>
</div>

<script type="text/javascript">
	$(function(){
		$('.container_same_category').mCustomScrollbar({
			scrollButtons:{
				enable:false
			}
		});
	});
function lang_search(){
	$.ajax({
		url: '<?php echo URL; ?>/settings/language_detail/<?php echo $keylang;?>/' + $("#lang_search_input").val(),
		timeout: 15000,
		success: function(html) {
			$("div#language_detail").html(html);
			$(function(){
				$('.container_same_category').mCustomScrollbar({
					scrollButtons:{
						enable:false
					}
				});
			});
		}
	});
}

</script>