<div class="block_dent2" style="max-width:1000px; margin: 0 auto; height:400px;" id="list_view_company">
	<table class="jt_tb" id="Form_add" style="width:100%; font-size:12px;">
		<thead>
			<tr>
				<th><?php echo translate('Company'); ?></th>
				<th><?php echo translate('Contact'); ?></th>
				<th><?php echo translate('Stage No'); ?></th>
				<th><?php echo translate('Stage Name'); ?></th>
			</tr>
		</thead>

		<?php
		$i=0;
		foreach ($arr_stages as $value) {
			$i = 1 - $i;
		?>
		<tr class="jt_line_<?php if($i == 1){ ?>black<?php }else{ ?>light<?php } ?>" onclick="after_choose_stages<?php if(substr($key,0,1) == '_')echo $key; ?>('<?php echo $value['_id']; ?>','<?php echo $value['name']; ?>', '<?php echo $key; ?>')">
			<td align="center">
				<?php if(isset($value['company_id']) && isset($arr_companies[(string)$value['company_id']])){
							echo $arr_companies[(string)$value['company_id']];

					}elseif(isset($value['company_name'])){
							echo $value['company_name'];
					} ?>
			</td>
			<td align="left">
				<?php if(isset($value['contact_id']) && isset($arr_contact[(string)$value['contact_id']])){
						echo $arr_contact[(string)$value['contact_id']];

					}elseif(isset($value['contact_name'])){
							echo $value['contact_name'];
					} ?>
			</td>
			<td align="center"><?php if(isset($value['no']))echo $value['no'];?></td>
			<td align=""><?php if(isset($value['name']))echo $value['name'];?>
				<!-- INPUT HIDDEN THEM VAO TUY Y DE SU DUNG, cau truc thong nhat: id="<id cua window>_<fieldname>" -->
				<input type="hidden" id="window_popup_stage_no_<?php echo $value['_id']; ?>" value="<?php if(isset($value['no']))echo $value['no']; ?>">
			</td>
		</tr>
		<?php } ?>
	</table>
</div>