<?php if($this->Common->check_permission('tasks_@_entry_@_view',$arr_permission)): ?>
	<div class="tab_1 full_width">
		<span class="title_block bo_ra1">
			<span class="fl_dent">
				<h4><?php echo translate('Tasks relating to this purchase order'); ?></h4>
			</span>
			<?php if($this->Common->check_permission('tasks_@_entry_@_add',$arr_permission) ): ?>
			<a href="<?php echo URL; ?>/purchaseorders/tasks_add/<?php echo $purchaseorder_id; ?>" title="Add new task">
				<span class="icon_down_tl top_f"></span>
			</a>
			<?php endif; ?>
		</span>
		<p class="clear"></p>
		<ul class="ul_mag clear bg3">
			<li class="hg_padd" style="width:1.5%"></li>
			<li class="hg_padd center_txt" style="width:4%">
				<?php echo translate('No'); ?></li>
			<li class="hg_padd line_mg center_txt" style="width:21%">
				<?php echo translate('Task'); ?></li>
			<li class="hg_padd center_txt" style="width:8%">
				<?php echo translate('Type'); ?></li>
			<li class="hg_padd line_mg center_txt" style="width:8%">
				<?php echo translate('Responsible'); ?></li>
			<li class="hg_padd line_mg center_txt" style="width:8%">
				<?php echo translate('Work start'); ?></li>
			<li class="hg_padd line_mg center_txt" style="width:8%">
				<?php echo translate('Work end'); ?></li>
			<li class="hg_padd center_txt" style="width:6%;">
				<?php echo translate('Status'); ?></li>
			<li class="hg_padd center_txt" style="width:23%;">
				<?php echo translate('Note'); ?></li>
			<li class="hg_padd bor_mt" style="width:1.5%"></li>
		</ul>
		<div class="container_same_category" style="height: auto;overflow: visible;">
			<?php
			$i = 1; $count = 0;
			$view = $this->Common->check_permission('tasks_@_entry_@_view',$arr_permission);
			$delete = false;
			if($this->Common->check_permission('tasks_@_entry_@_delete',$arr_permission)==true )
				$delete = true;
			foreach ($arr_task as $key => $value) {
			?>
			<ul class="ul_mag clear bg<?php echo $i; ?>" id="Purchaseorder_Task_<?php echo $value['_id']; ?>">
				<li class="hg_padd center_txt" style="width:1.5%">
					<?php if($view): ?>
					<a href="<?php echo URL; ?>/tasks/entry/<?php echo $value['_id']; ?>">
						<span class="icon_emp"></span>
					</a>
				<?php endif; ?>
				</li>
				<li class="hg_padd center_txt" style="width:4%"><?php echo $value['no']; ?></li>
				<li class="hg_padd line_mg" style="width:21%"><?php echo $value['name']; ?></li>
				<li class="hg_padd" style="width:8%"><?php if(isset($value['type']))echo $value['type']; ?></li>
				<li class="hg_padd line_mg" style="width:8%"><?php echo $value['our_rep']; ?></li>
				<li class="hg_padd line_mg center_txt" style="width:8%"><?php echo $this->Common->format_date($value['work_start']->sec); ?></li>
				<li class="hg_padd line_mg center_txt" style="width:8%"><?php echo $this->Common->format_date($value['work_end']->sec); ?></li>
				<li class="hg_padd" style="width:6%;"><?php echo $value['status']; ?></li>
				<li class="hg_padd" style="width:23%;">
					<?php
						$arr_noteactivity = $model_noteactivity->select_one(array('module' => 'Task', 'module_id' => new MongoId($value['_id'])), array('_id', 'content'), array('_id' => -1));
						$content_task = '';
						$content_noteactivity_id = '';
						if(isset($arr_noteactivity['content'])){
							$content_task = $arr_noteactivity['content'];
							$content_noteactivity_id = $arr_noteactivity['_id'];
						}
						echo $content_task;
					?>
				</li>
				<li class="hg_padd bor_mt" style="width:1.5%">
					<?php if($delete): ?>
					<div class="middle_check">
						<a title="Delete link" href="javascript:void(0)" onclick="po_remove_task('<?php echo $value['_id']; ?>')">
							<span class="icon_remove2"></span>
						</a>
					</div>
					<?php endif; ?>
				</li>
			</ul>

			<?php $i = 3 - $i; $count += 1;
				}

				$count = 8 - $count;
				if( $count > 0 ){
					for ($j=0; $j < $count; $j++) { ?>
					<ul class="ul_mag clear bg<?php echo $i; ?>">
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

<script type="text/javascript">
<?php if($this->Common->check_permission('tasks_@_entry_@_delete',$arr_permission)): ?>
function po_remove_task(task_id){
	confirms( "Message", "Are you sure you want to delete?",
		function(){
			$.ajax({
				 url: '<?php echo URL; ?>/purchaseorders/tasks_delete/' + task_id,
				 timeout: 15000,
				 success: function(html){
					 if(html == "ok"){
						 $("#Purchaseorder_Task_" + task_id).fadeOut();
					 }else{
						 alerts("Error: ", html);
					 }
				 }
			 });
		},function(){
			//else do somthing
	});
}
<?php endif; ?>
</script>
<?php endif; ?>