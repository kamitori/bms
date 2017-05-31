<!-- POPUP ASSETS -->
<style type="text/css">
.input_switch_popup{
width:99%;padding: 3px 10px;border-radius: 9px;
}
.input_switch_popup:hover{
color:#942525;
}
</style>

<!-- POPUP CONTACTS -->
<div style="clear:both;height:6px"></div>

<div class="block_dent2" style="overflow: auto;overflow-x: hidden;max-width:1000px; margin: 0 auto; height:430px;" id="list_view_contacts">
	<table class="jt_tb" id="Form_add" style="font-size:12px;">
		<thead>
			<tr>
				<th style="width: 81px"><?php echo translate('Type'); ?><span id="sort_first_name" rel="first_name" class="desc"></span></th>
				<th style="width: 86px"><?php echo translate('Date'); ?><span id="sort_is_customer" rel="is_customer" class="desc"></span></th>
				<th style="width: 86px"><?php echo translate('Time'); ?><span id="sort_is_employee" rel="is_employee" class="desc"></span></th>
				<th style="width: 635px"><?php echo translate('Detail'); ?></th>
			</tr>
		</thead>
		<tbody>
			<!-- <tr>
				<th style="width: 81px">&nspb;</th>
				<th style="width: 86px"></th>
				<th style="width: 86px"></th>
				<th style="width: 635px"></th>
			</tr> -->
			<?php
			$i = 0; $STT = 0;
			if(isset($arr_data['leave']) && !empty($arr_data['leave'])){
				foreach ($arr_data['leave'] as $key=> $value) {
					$i = 1 - $i; $STT += 1;
			?>
				<tr class="jt_line_<?php if ($i == 1) { ?>black<?php } else { ?>light<?php } ?>" onclick="window.location='<?php echo URL; ?>/contacts/leave_view/<?php echo $_SESSION['arr_user']['contact_id'].'/'.$key.'/view'; ?>'" title="view detail">

					<td align="left" style="width: 81px"><?php echo translate('Appoved '.$value['purpose']); ?></td>
					<td align="center" style="width: 86px">
						<?php echo $this->Common->format_date($value['date_from']->sec, false); ?>
					</td>
					<td align="center" style="width: 86px"><?php echo $value['used'] ? 'Used day(s): '.$value['used'] : ''; ?></td>
					<td style="width: 635px"><?php echo isset($value['comment']) ? $value['comment'] : ''; ?></td>
				</tr>
			<?php
					}
				}
			?>
			<?php
			foreach ($arr_data['task'] as $value) {
				$i = 1 - $i; $STT += 1;
				?>
				<tr class="jt_line_<?php if ($i == 1) { ?>black<?php } else { ?>light<?php } ?>" onclick="window.location='<?php echo URL; ?>/tasks/entry/<?php echo $value['_id']; ?>'" title="view detail">

					<td align="left" style="width: 81px"><?php echo translate('Task'); ?></td>
					<td align="center" style="width: 86px">
						<?php echo $this->Common->format_date($value['work_end']->sec, false); ?>
					</td>
					<td align="center" style="width: 86px"><?php echo date( "H:i", $value['work_end']->sec); ?></td>
					<td style="width: 635px"><?php echo $value['name']; ?></td>
				</tr>
			<?php } ?>

			<?php
			foreach ($arr_data['communication'] as $value) {
				$i = 1 - $i; $STT += 1;
				?>
				<tr class="jt_line_<?php if ($i == 1) { ?>black<?php } else { ?>light<?php } ?>" onclick="window.location='<?php echo URL; ?>/communications/entry/<?php echo $value['_id']; ?>'" title="view detail">

					<td align="left" style="width: 81px"><?php echo translate('Message'); ?></td>
					<td align="center" style="width: 86px">
						<?php echo $this->Common->format_date($value['date_modified']->sec, false); ?>
					</td>
					<td align="center" style="width: 86px"><?php echo date( "H:i", $value['date_modified']->sec); ?></td>
					<td style="width: 635px"><?php echo $value['message_content']; ?></td>
				</tr>
			<?php } ?>

		</tbody>
	</table>

	<?php if( $STT == 0 ){ ?>
	<center style="margin-top:30px">(No data)</center>
	<?php } ?>
</div>