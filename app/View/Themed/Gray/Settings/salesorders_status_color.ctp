<?php echo $this->element('../Settings/index', array('active' => 0)); ?>
<div id="content" class="fix_magr">
	<div class="clear" style="margin-top: 10px;" id="salesorders_form_auto_save">
		<?php echo $this->Form->create('Setup'); ?>
		<div class="block_dent2" style="margin: 0 auto;">
			<div id="jt_setup_salesorders_status" style="float:left">
				<table class="jt_tb" id="Form_add" style="width:100%; font-size:12px;">
					<thead>
						<tr>
							<th><?php echo translate('STT'); ?></th>
							<th><?php echo translate('Name'); ?></th>
							<th></th>
						</tr>
					</thead>
					<tbody>
				<?php
					$i=0; $STT = 1;
					foreach ($arr_salesorders_status_color['option'] as $key => $value) {
						$i = 1 - $i;
				?>
						<tr class="jt_line_<?php if($i == 1){ ?>black<?php }else{ ?>light<?php } ?>">
							<td><?php echo $STT++; ?></td>
							<td><?php echo $value['name']; ?></td>
							<td><input id="pickcolor_<?php echo $key; ?>" rel_idx="<?php echo $key; ?>" rel="<?php echo $arr_salesorders_status_color['_id']; ?>" class="ColorPicker" type="color" value="<?php echo $value['color']; ?>" data-role="colorpicker" />
							</td>
						</tr>
				<?php } ?>
					</tbody>
				</table>
			</div>
		</div><!--END Tab1 -->
		<?php echo $this->Form->end(); ?>
	</div>
	<div class="clear"></div>
</div>
<?php echo $this->element('../Settings/js'); ?>