<?php $i = 1; ?><br>
<?php foreach ($arr_tasks as $value): ?>
	<?php
		$i = 3 - $i;
	?>
	<ul class="ul_mag clear bg<?php echo $i ?>" id="tasks_<?php echo (string) $value['_id']; ?>">
		<li class="hg_padd" style="width:1%">
			<a style="color: blue" href="<?php echo URL; ?>/tasks/entry/<?php echo $value['_id']; ?>"><span class="icon_emp"></span></a>
		</li>
		<li class="hg_padd center_txt" style="width:4%"><?php echo $value['no']; ?></li>
		<li class="hg_padd" style="width:33%"><?php echo $value['name']; ?></li>
		<li class="hg_padd" style="width:4%">
			<?php if (isset($value['job_id']) && is_object($value['job_id']) ) { ?>
				<a href="<?php echo URL; ?>/jobs/entry/<?php echo $value['job_id']; ?>">
					<?php echo $value['job_name']; ?>
				</a>
			<?php } ?>
		</li>
		<li class="hg_padd" style="width:9%">
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
							<a href="<?php echo URL; ?>/contacts/entry/<?php echo $value['our_rep_id']; ?>">
								<?php echo $arr_contact['first_name'].' '.$arr_contact['last_name']; ?>
							</a>
						<?php
							}
						}else{
						 ?>
							<a href="<?php echo URL; ?>/contacts/entry/<?php echo $value['our_rep_id']; ?>">
								<?php echo $arr_contact_tmp[(string)$value['our_rep_id']]; ?>
							</a>
						<?php
						}
					}

				}
			}else{
				if( isset($value['our_rep_id']) && isset($arr_equipment[(string)$value['our_rep_id']]) )echo $arr_equipment[(string)$value['our_rep_id']];
			} ?>
		</li>
		<li class="hg_padd" style="width:8%"><?php if (isset($value['type']) && isset($arr_tasks_type[$value['type']])) echo $arr_tasks_type[$value['type']]; ?></li>
		<li class="hg_padd center_txt" style="width:8%"><?php echo $this->Common->format_date($value['work_start']->sec); ?></li>
		<li class="hg_padd center_txt" style="width:8%"><?php echo $this->Common->format_date($value['work_end']->sec); ?></li>
		<li class="hg_padd" style="width:6%"><?php echo $value['status']; ?></li>
		<li class="hg_padd center_txt" style="width:3%">
			<?php
			if ( in_array( $value['status_id'], array('New', 'Confirmed')) && isset($value['work_end']) && is_object($value['work_end'])) {
				if ($value['work_end']->sec < strtotime('now')) {
					echo '<span class="Late">X</span>';
				}
			}?>
		</li>
		<li class="hg_padd bor_mt" style="width:3%">
			<div class="middle_check">
				<a href="javascript:void(0)" title="Delete link" onclick="tasks_lists_delete('<?php echo $value['_id']; ?>')">
					<span class="icon_remove2"></span>
				</a>
			</div>
		</li>
	</ul>
<?php endforeach; ?>

<?php echo $this->element('popup/pagination_lists'); ?>
