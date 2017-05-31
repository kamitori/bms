<?php
if (isset($name))
	echo $this->element('../' . $name . '/tab_option');
else
	echo $this->element('entry_tab_option');
?>
<?php
$sumcol = 3;
if (!isset($option_list))
	$option_list = array(1 => array(array()), 2 => array(array()), 3 => array(array()));
$sumcol = count($option_list);
if($sumcol == 0)
	$sumcol = 10;
$width = round(100 / $sumcol, 1) - 1;

if (!isset($set_sumline))
	$set_sumline = 25;
?>
<div id="content">
    <div class="clear_percent block_dent_a">
		<?php
		foreach ($option_list as $kss => $vss) { //loop Column
			$max_line = $null_line = $set_sumline;
			$linenum = 1;
			$linenum++;
			$groupnum = 0;
			$sumgroup = count($vss);
			$countgroup = 0;
			?>
			<div class="block_dent_a float_left" style="width:<?php echo $width; ?>%;margin:1% 0.5%;">
				<div class="tab_1 full_width">
					<?php
					foreach ($vss as $kk => $vv) {
						$countgroup++; // loop Groups
						?>
						<div>
							<span class="title_block <?php if ($groupnum == 0) echo 'bo_ra1'; ?>">
								<span class="fl_dent">
									<?php $msg = str_replace('_', ' ', $kk); ?>
									<h4><?php echo ucfirst($msg); ?></h4>
								</span>
							</span>
							<ul class="find_list">
								<?php foreach ($vv as $kmm => $vmm) { // loop Items    ?>
									<li style="height:23px;">
										<div class="icon_find" style="margin-top:20px;">
											<a title="<?php if (isset($vmm['description'])) echo $vmm['description']; ?>" <?php if (isset($vmm['url']) && $vmm['url'] != '') echo 'href="' . URL . '/' . $vmm['url'] . '"'; else { ?> onclick="swith_options('<?php if (isset($vmm['codekey'])) echo $vmm['codekey']; ?>');"<?php } ?>>
												<?php if (isset($vmm['type']) && $vmm['type'] == 'search') { ?>
													<span class="icon_search_ip"></span>
												<?php } elseif (isset($vmm['type']) && $vmm['type'] == 'mail') { ?>
													<span class="icon_mail_opt"></span>
												<?php } elseif (isset($vmm['type']) && $vmm['type'] == 'printer') { ?>
													<span class="icon_printer_opt"></span>
												<?php } elseif (isset($vmm['type']) && $vmm['type'] == 'add') { ?>
													<span class="icon_plus_opt"></span>
												<?php } else { ?>
													<span class="icon_report"></span>
												<?php } ?>
											</a>
										</div>
									<a class="swith_options" id="<?php if (isset($vmm['codekey'])) echo $vmm['codekey']; ?>" style="cursor:pointer;line-height:24px;<?php if(isset($vmm['not_finished'])&&$vmm['not_finished']==1) echo 'color:red;'; ?>" <?php if (isset($vmm['url']) && $vmm['url'] != '') echo 'href="' . URL . '/' . $vmm['url'] . '"'; else { ?> onclick="swith_options('<?php if (isset($vmm['codekey'])) echo $vmm['codekey']; ?>');"<?php } ?>>
                                    	<?php if (isset($vmm['name'])) echo $vmm['name']; ?>
                                    </a>
										<?php if (isset($vmm['flag'])) { ?>
											<div class="icon_find2">
												<a title="Dial phone">
													<span class="flag_<?php echo $vmm['flag']; ?>"></span>
												</a>
											</div>
										<?php } ?>
									</li>
									<?php
									$linenum++;
									if ($linenum > $max_line)
										$max_line = $linenum;
								}
								?>

								<?php
								$null_line = $max_line - $linenum;
								if ($null_line > 0 && $countgroup == $sumgroup) {
									for ($n = 0; $n < $null_line; $n++) { // loop null line
										?>
										<li style="height:23px;"><a>&nbsp;</a></li>
										<?php
									}
								} else
									$linenum++;
								?>
							</ul>
						</div>
						<?php
						$groupnum++;
					}
					?>

					<span class="title_block bo_ra2"></span>
				</div><!--END colum 01 -->
			</div>
		<?php } ?>
        <p class="clear"></p>
    </div>
</div>
<!--END Content -->
<script>
$(function(){
	$("a:first",$("a.swith_options").parent()).attr("onclick","javascript:void(0)");
	$("a:first",$("a.swith_options").parent()).click(function(){
		$("a.swith_options",$(this).parent().parent()).click();
		return false;
	})
})
	function swith_options(ids) {
		$.ajax({
			url: '<?php echo URL . '/' . $controller; ?>/swith_options/' + ids,
			success: function(links) {
				//console.log(links);
				if (links != '')
					window.location.assign(links);
			}
		});
	}
	;
</script>
