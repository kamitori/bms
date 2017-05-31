<div class="tab_1 full_width">
	<span class="title_block bo_ra1">
		<span class="fl_dent">
			<h4><?php echo translate('Tasks relating to this job'); ?></h4>
		</span>
		<?php if($this->Common->check_permission('tasks_@_entry_@_add',$arr_permission)){ ?>
		<a href="<?php echo URL; ?>/jobs/tasks_add/<?php echo $job_id; ?>" title="Add new task">
			<span class="icon_down_tl top_f"></span>
		</a>
		<?php } ?>
	</span>
	<p class="clear"></p>
	<ul class="ul_mag clear bg3">
		<li class="hg_padd" style="width:1.5%"></li>
		<li class="hg_padd center_txt" style="width:4%">
			<?php echo translate('No'); ?></li>
		<li class="hg_padd line_mg center_txt" style="width:29%">
			<?php echo translate('Task'); ?></li>
		<li class="hg_padd center_txt" style="width:14%">
			<?php echo translate('Type'); ?></li>
		<li class="hg_padd line_mg center_txt" style="width:10%">
			<?php echo translate('Responsible'); ?></li>
		<li class="hg_padd line_mg center_txt" style="width:10%">
			<?php echo translate('Work start'); ?></li>
		<li class="hg_padd line_mg center_txt" style="width:10%">
			<?php echo translate('Work end'); ?></li>
		<li class="hg_padd center_txt" style="width:10%;">
			<?php echo translate('Status'); ?></li>
		<li class="hg_padd bor_mt" style="width:1.5%"></li>
	</ul>
	<div class="container_same_category" style="height:200px;overflow-y:auto;">
		<?php
		$i = 1;$count = 0;
		foreach ($arr_task as $key => $value) {
		?>
		<ul class="ul_mag clear bg<?php echo $i; ?>" id="Job_Task_<?php echo $value['_id']; ?>">
			<li class="hg_padd center_txt" style="width:1.5%">
				<a href="<?php echo URL; ?>/tasks/entry/<?php echo $value['_id']; ?>">
					<span class="icon_emp"></span>
				</a>
			</li>
			<li class="hg_padd center_txt" style="width:4%"><?php echo $value['no']; ?></li>
			<li class="hg_padd line_mg" style="width:29%"><?php echo $value['name']; ?></li>
			<li class="hg_padd" style="width:14%"><?php echo @$value['type']; ?></li>
			<li class="hg_padd line_mg" style="width:10%"><?php echo $value['our_rep']; ?></li>
			<li class="hg_padd line_mg center_txt" style="width:10%"><?php if(isset($value['work_start'])) echo $this->Common->format_date($value['work_start']->sec); ?></li>
			<li class="hg_padd line_mg center_txt" style="width:10%"><?php if(isset($value['work_end'])) echo $this->Common->format_date($value['work_end']->sec); ?></li>
			<li class="hg_padd" style="width:10%;"><?php echo @$value['status']; ?></li>
			<?php if($this->Common->check_permission('tasks_@_entry_@_delete',$arr_permission)){ ?>
			<li class="hg_padd bor_mt" style="width:1.5%">
				<div class="middle_check">
					<a title="Delete link" href="javascript:void(0)" onclick="resource_remove_task_job('<?php echo $value['_id']; ?>')">
						<span class="icon_remove2"></span>
					</a>
				</div>
			</li>
			<?php } ?>
		</ul>

		<?php $i = 3 - $i; $count += 1;
			}

			$count = 8 - $count;
			if( $count > 0 ){
				for ($j=0; $j < $count; $j++) { ?>
				<ul class="ul_mag clear bg<?php echo $i; ?>">
					<li class="hg_padd" style="width:1.5%"></li>
					<li class="hg_padd center_txt" style="width:4%"></li>
					<li class="hg_padd line_mg center_txt" style="width:29%"></li>
					<li class="hg_padd center_txt" style="width:14%"></li>
					<li class="hg_padd line_mg center_txt" style="width:10%"></li>
					<li class="hg_padd line_mg center_txt" style="width:10%"></li>
					<li class="hg_padd line_mg center_txt" style="width:10%"></li>
					<li class="hg_padd center_txt" style="width:10%;"></li>
					<li class="hg_padd bor_mt" style="width:1.5%"></li>
				</ul>
		  <?php $i = 3 - $i;
				}
			}
		?>
	</div>

	<span class="title_block bo_ra2">
		<span class="float_left bt_block">
			<?php echo translate('Click to view full details'); ?>
		</span>
	</span>
</div>

<?php if($this->Common->check_permission('tasks_@_entry_@_delete',$arr_permission)){ ?>
<script type="text/javascript">
function resource_remove_task_job(task_id){
	confirms( "Message", "Are you sure you want to delete?",
		function(){
			$.ajax({
				 url: '<?php echo URL; ?>/jobs/tasks_delete/' + task_id,
				 timeout: 15000,
				 success: function(html){
					 if(html == "ok"){
						 $("#Job_Task_" + task_id).fadeOut();
					 }else{
						 alerts("Error: ", html);
					 }
				 }
			 });
		},function(){
			//else do somthing
	});
}
</script>
<?php } ?>