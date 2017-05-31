<style type="text/css">
.hook_menu li:hover, .hook_menu li:hover input{
	background-color: #B8B8B8;
}
.active{
	background-color: #B8B8B8;
}
</style>
<div class="clear_percent">
	<div class="clear_percent_19 float_left">
		<div class="tab_1 full_width">
			<span class="title_block bo_ra1">
				<span class="fl_dent"><h4><?php echo translate('Specify values for lists used in system'); ?></h4></span>
			</span>
			<div class="container_same_category" style="height: 474px;overflow-y: auto">
				<ul class="find_list hook_menu">
					<?php foreach($hooks as $hook){ ?>
					<li onclick="hook_detail('<?php echo $hook['_id']; ?>')">
						<a href="javascript:void(0)">
							<?php echo $hook['name'];?>
						</a>
					</li>
					<?php } ?>
				</ul>
			</div>
			<span class="title_block bo_ra2"></span>
		</div><!--END Tab1 -->
	</div>
	<div class="clear_percent_9_arrow float_left">
		<div class="full_width box_arrow">
			<span class="icon_emp" style="cursor:default"></span>
		</div>
	</div>
	<div class="clear_percent_11 float_left" id="hook_detail">
		<!-- Detail -->
	</div>
</div>
<script type="text/javascript">
	$("li",".hook_menu").click(function(){
		$("li",".hook_menu").removeClass("active");
		$(this).addClass("active");
	});
	$("li:first",".hook_menu").click();
	function hook_detail(hook_id){
		$.ajax({
			url:'<?php echo URL; ?>/settings/hook_detail/'+hook_id,
			success: function(html){
				if(html!='')
					$("#hook_detail").html(html);
			}
		});
	}
</script>