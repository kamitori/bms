<?php foreach ($arr_tasks as $value): ?>
	<?php
	?>
	<tr id="tasks_<?php echo (string) $value['_id']; ?>">
		<td class="hg_padd"><a data-ajax="false" href="<?php echo URL; ?>/mobile/tasks/entry/<?php echo $value['_id']; ?>"><?php echo $value['no']; ?></a></td>
		<td><?php echo $value['name']; ?></td>
		<td>
			<?php if (isset($value['job_id']) && is_object($value['job_id']) ) { ?>
				<a data-ajax="false" href="<?php echo URL; ?>/mobile/jobs/entry/<?php echo $value['job_id']; ?>">
					<?php echo $value['job_name']; ?>
				</a>
			<?php } ?>
		</td>
		<td>
			<?php
			if( $value['our_rep_type'] == 'contacts' ){
				if (isset($value['our_rep_id'])){
					if (is_object($value['our_rep_id'])) {
						if(!isset($arr_contact_tmp))$arr_contact_tmp = array();
						if( !isset($arr_contact_tmp[(string)$value['our_rep_id']]) ){
							$arr_contact = $model_contact->select_one(array('_id' => $value['our_rep_id']), array('_id', 'first_name', 'last_name'));
							if(isset($arr_contact['first_name'])){
								$arr_contact_tmp[(string)$value['our_rep_id']] = $arr_contact['first_name'].' '.$arr_contact['last_name'];
							?>
							<a data-ajax="false" href="<?php echo URL; ?>/mobile/contacts/entry/<?php echo $value['our_rep_id']; ?>">
								<?php echo $arr_contact['first_name'].' '.$arr_contact['last_name']; ?>
							</a>
						<?php
							}
						}else{
						 ?>
							<a data-ajax="false" href="<?php echo URL; ?>/mobile/contacts/entry/<?php echo $value['our_rep_id']; ?>">
								<?php echo $arr_contact_tmp[(string)$value['our_rep_id']]; ?>
							</a>
						<?php
						}
					}

				}
			}else{
				if( isset($value['our_rep_id']) && isset($arr_equipment[(string)$value['our_rep_id']]) )echo $arr_equipment[(string)$value['our_rep_id']];
			} ?>
		</td>
		<td><?php if (isset($value['type']) && isset($arr_tasks_type[$value['type']])) echo $arr_tasks_type[$value['type']]; ?></td>
		<td style="text-align:center;"><?php echo $this->Common->format_date($value['work_start']->sec); ?></td>
		<td style="text-align:center;"><?php echo $this->Common->format_date($value['work_end']->sec); ?></td>
		<td><?php echo $value['status']; ?></td>
		<td style="text-align:center;">
			<?php
			if ( in_array( $value['status'], array('New', 'Confirmed')) && isset($value['work_end']) && is_object($value['work_end'])) {
				if ($value['work_end']->sec < strtotime('now')) {
					echo '<span class="late">X</span>';
				}
			}?>
		</td>
		<td>
			<a href="#" class="delete delete ui-btn ui-btn-icon-notext ui-icon-delete" onclick="confirmAndDelete(this)">X</a>
		</td>
	</tr>
<?php endforeach; ?>

