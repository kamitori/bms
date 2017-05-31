<style type="text/css">
	ul.ul_mag li.hg_padd {
	overflow: visible !important;
	}
	.bg4 {
	background: none repeat scroll 0 0 #949494;
	color: #fff;
	}
	.bg4 span h4 {
	margin-left: 1%;
	width: 100%;
	}

</style>
<div class="tab_1 full_width">
	<span class="title_block bo_ra1">
		<span class="fl_dent">
			<h4>Auto Process</h4>
		</span>
		<?php if($_SESSION['arr_user']['contact_name'] == 'System Admin'): ?>
		<a title="Add new Process" href="javascript:void(0)" onclick="settings_auto_process_add()">
			<span class="icon_down_tl top_f"></span>
		</a>
		<?php endif; ?>
	</span>
	<ul class="ul_mag clear bg3">
		<li class="hg_padd" style="width:10%"><?php echo translate('Module name'); ?></li>
		<?php if($_SESSION['arr_user']['contact_name'] == 'System Admin'): ?>
		<li class="hg_padd" style="width:10%"><?php echo translate('Controller'); ?></li>
		<li class="hg_padd" style="width:15%"><?php echo translate('Name'); ?></li>
		<li class="hg_padd" style="width:33%"><?php echo translate('Description'); ?></li>
		<li class="hg_padd" style="width:5%"><?php echo translate('Ex. Email'); ?></li>
		<li class="hg_padd" style="width:10%"><?php echo translate('E. Template'); ?></li>
		<li class="hg_padd line_mg center_txt" style="width:4%"><?php echo translate('Inactive'); ?></li>
		<li class="hg_padd line_mg center_txt" style="width:4%"><?php echo translate('Remove'); ?></li>
		<?php else: ?>
		<li class="hg_padd" style="width:15%"><?php echo translate('Name'); ?></li>
		<li class="hg_padd" style="width:38%"><?php echo translate('Description'); ?></li>
		<li class="hg_padd" style="width:10%"><?php echo translate('Extra Email'); ?></li>
		<li class="hg_padd" style="width:15%"><?php echo translate('Email Template'); ?></li>
		<li class="hg_padd line_mg center_txt" style="width:5%"><?php echo translate('Inactive'); ?></li>
		<?php endif; ?>
	</ul>
	<div class="container_same_category" style="height: 449px;overflow-y: auto">
		<?php if($_SESSION['arr_user']['contact_name'] == 'System Admin'): ?>
		<?php $i = 1; $j=0; $count = count($arr_process);
		foreach ($arr_process as $key => $value) {
			$i = 3 - $i;
		?>

		<?php echo $this->Form->create('Process', array('id' => 'ProcessForm_'.$key)); ?>
		<?php echo $this->Form->hidden('Process._id', array( 'value' => $value['_id'] )); ?>
		<ul class="ul_mag clear bg<?php echo $i; ?>" id="autoprocess_<?php echo $value['_id']; ?>">
			<li class="hg_padd line_mg" style="width:10%">
				<?php echo $this->Form->input('Process.module_name'.$j, array(
				        'rel'	=>	$j,
						'class' => 'module_name input_inner input_inner_w bg'.$i,
						'name'	=>	'data[Process][module_name]',
						'value' => (isset($value['module_name']))?$value['module_name']:''
				)); ?>
				<script type="text/javascript">
					$(function () {
						$("#ProcessModuleName<?php echo $j; ?>").combobox(<?php echo json_encode($arr_module); ?>);
					});
				</script>
			</li>
			<li class="hg_padd line_mg" style="width:10%">
				<?php echo $this->Form->input('Process.controller', array(
				        'rel'	=>	$j,
				        'id'	=> 'ProcessModuleName'.$j.'Id',
						'class' => 'controller input_inner input_inner_w bg'.$i,
						'value' => (isset($value['controller']))?$value['controller']:''
				)); ?>
			</li>
			<li class="hg_padd line_mg" style="width:15%">
				<?php echo $this->Form->input('Process.name'.$j, array(
						'class' => 'input_inner input_inner_w bg'.$i,
						'name'	=> 'data[Process][name]',
						'value' => (isset($value['name']))?$value['name']:''
				)); ?>
				<script type="text/javascript">
					$(function () {
						$("#ProcessName<?php echo $j; ?>").combobox(<?php echo json_encode($arr_name); ?>);
					});
				</script>
			</li>
			<li class="hg_padd line_mg" style="width:33%">
				<?php echo $this->Form->input('Process.description', array(
						'class' => 'input_inner input_inner_w process_description bg'.$i,
						'id'	=> 'ProcessName'.$j.'Id',
						'value' => (isset($value['description']))?$value['description']:''
				)); ?>
			</li>
			<li class="hg_padd line_mg" style="width:5%">
				<?php echo $this->Form->input('Process.extra_email', array(
						'class' => 'input_inner input_inner_w process_extra_email bg'.$i,
						'value' => (isset($value['extra_email']))?$value['extra_email']:''
				)); ?>
			</li>
			<li class="hg_padd line_mg" style="width:10%">
				<?php echo $this->Form->input('Process.email_template_'.$j, array(
						'class' => 'input_inner input_inner_w bg'.$i,
						'name'	=>	'data[Process][email_template]',
						'value' => (isset($value['email_template']))?$value['email_template']:''
				)); ?>
				<input type="hidden" name="data[Process][email_template_id]" id="ProcessEmailTemplate<?php echo $j; ?>Id" value="<?php echo (isset($value['email_template_id']))?$value['email_template_id']:'' ?>" />
				<script type="text/javascript">
					$(function () {
						$("#ProcessEmailTemplate<?php echo $j; ?>").combobox(<?php echo json_encode($arr_template); ?>);
					});
				</script>
			</li>
			<li class="hg_padd line_mg center_txt" style="width:4%">
				<input type="checkbox" name="data[Process][deleted]" id="ProcessInactive" <?php if(isset($value['deleted'])&&$value['deleted']==true) echo 'checked'; ?> />
			</li>
			<li class="hg_padd line_mg center_txt" style="width:4%;">
				<div style="margin: auto;width:4%">
				<a onclick="autoprocess_delete('<?php echo $value['_id']; ?>');" href="javascript:void(0);">
                    <span class="icon_remove2"></span>
                </a>
            	</div>
			</li>
		</ul>
		<?php $j++; echo $this->Form->end(); ?>

		<?php
		} ?>
		<?php else: ?>
		<?php $i = 1; $j=0; $count = count($arr_process);
		foreach ($arr_process as $key => $value) {
			$i = 3 - $i;
		?>

		<?php echo $this->Form->create('Process', array('id' => 'ProcessForm_'.$key)); ?>
		<?php echo $this->Form->hidden('Process._id', array( 'value' => $value['_id'] )); ?>
		<ul class="ul_mag clear bg<?php echo $i; ?>" style="height:30px;line-height: 30px">
			<li class="hg_padd line_mg" style="width:10%;line-height:19px;height:22px;">
				<span><?php echo (isset($value['module_name'])?$value['module_name']:''); ?></span>
			</li>
			<li class="hg_padd line_mg" style="width:15%;line-height:19px;height:22px;;">
				<span><?php echo (isset($value['name'])?$value['name']:''); ?></span>
			</li>
			<li class="hg_padd line_mg process_description" style="width:38%;height:22px;">
				<span><?php echo (isset($value['description'])?$value['description']:''); ?></span>
			</li>
			<li class="hg_padd line_mg" style="width:10%">
				<span>
					<?php echo $this->Form->input('Process.extra_email', array(
						'class' => 'input_inner input_inner_w process_extra_email bg'.$i,
						'value' => (isset($value['extra_email']))?$value['extra_email']:''
					)); ?>
				</span>
			</li>
			<li class="hg_padd line_mg" style="width:15%;line-height:19px;height:22px;">
				<span><?php echo (isset($value['email_template_id'])? '<a href="'.URL.'/emailtemplates/entry/'.$value['email_template_id'].'">'.$value['email_template'].'</a>':''); ?></span>
			</li>
			<li class="hg_padd line_mg center_txt" style="width:5%;line-height:19px;height:22px;">
				<input type="checkbox" name="data[Process][deleted]" value="1" id="ProcessInactive" <?php if(isset($value['deleted'])&&$value['deleted']==true) echo 'checked'; ?> />
			</li>
		</ul>
		<?php $j++; echo $this->Form->end(); ?>

		<?php
		} ?>
		<?php endif; ?>
		<?php if( $count < 12 ){
				$count = 12 - $count;
				for ($j=0; $j < $count; $j++) {
					$i = 3 - $i;
		?>
		<ul class="ul_mag clear bg<?php echo $i; ?>">
		</ul>
		<?php }
		} ?>

	</div>
	<span class="title_block bo_ra2">
	</span>
</div>
<script type="text/javascript">
$(function(){
	$('.module_name').keyup(function(){
		var rel = $(this).attr("rel");
		var value = $(this).val().replace(' ','').toLowerCase();
		$("input.controller[rel="+rel+"]").val(value);
	});
	$('.container_same_category').mCustomScrollbar({
	   	scrollButtons:{
	    	enable:false
	   	}
	});
	$("form :input", "#detail_for_main_menu").change(function() {
		$.ajax({
			url: '<?php echo URL; ?>/settings/auto_process_auto_save',
			timeout: 15000,
			type:"post",
			data: $(this).closest('form').serialize(),
			success: function(html){
				console.log(html);
				if( html != "ok" )alerts("Error: ", html);
			}
		});
	});
})
function autoprocess_delete(id){
	confirms('Message','Are you sure to delete?',function(){
		$.ajax({
			url: '<?php echo URL; ?>/<?php echo $controller; ?>/auto_process_remove',
			type: 'post',
			data: {id:id},
			success: function(html){
				if(html!='ok')
					alerts('Message',html);
				else
					$("#autoprocess_"+id).fadeOut();
			}
		});
	});
}
function settings_auto_process_add(){
	$.ajax({
		url: '<?php echo URL; ?>/<?php echo $controller; ?>/auto_process_add',
		success: function(html){
			$("#detail_for_main_menu").html(html);
		}
	});
}
</script>