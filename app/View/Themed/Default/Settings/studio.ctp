<style type="text/css">
	button{
		width: 10%;
		font-weight: bold;
		padding: 3px 0px;
		margin: -3px 0px 18px;
		background-color: #d6d3d3;
		color: #515151;
		border: 1px solid #aaa9a9;
	}
	button:hover{
		color: white;
		background-color: #6a1515;
		border-color: #5f2121;
	}
	.field_list{
		width: 100%;
		min-height: 100px;
		float: left;
		display: table;
		background: #ccc;
		padding-bottom: 10px;
		list-style:none;
	}
	.field_item,.group_item,.panel_item{
		cursor: pointer;
		width:12%;
		margin: 1% 1% 0.5% 2%;
		padding: 0.5%;
		float: left;
		background: #eee;
		height:18px;
		overflow:hidden;
	}
	.field_item_text{
		width:100%;
		line-height: 16px;
		height:16px;
		float:left;
		position:relative;
		overflow:hidden;
		z-index:1;
	}
</style>
<div id="header">
	<div class="logo logo_border">
		<a href="<?php echo URL; ?>/"><?php echo $this->Html->image('logo.png', array('alt' => 'Jobtraq')); ?></a>
	</div>
	<div class="setup_title">
		<div>
			<a href="<?php echo URL; ?>/settings"><button class="btn_return">Return</button></a>
		</div>
		<h1>Setup: General</h1>
	</div>
</div><!-- End Header -->
<p class="clear_fix">clearfix</p>
<div class="menu">
	<div class="bg_menu bg_menu_res"></div>
</div><!--END Menu -->

<div id="content">
	<div class="percent content_indent">
		<div class="clear_percent_3 float_left bg_nav_setup">
			<ul class="nav_setup" id="settings_ul_nav_setup">
				<?php foreach($list_module as $value){ ?>
					<li><a id="<?php echo $value;?>" href="javascript:void(0)" onclick="studio_detail('<?php echo $value; ?>')" class="<?php if ($value == "equipments") { ?>active<?php } ?>"><?php echo $value ?></a></li>
				<?php } ?>
			</ul>
		</div>
		<div class="percent_content block_dent_a float_left">
			<div id="list_detail">
		        <!-- Detail -->
		    </div>
			<p class="clear"></p>
		</div>
	</div>
	<p class="clear"></p>
</div><!--END Content -->

<div id="footer">
	<div class="bg_footer footer_res3">

		<div class="clear_percent_3 float_left center_txt">
			<span class="label_footer"><?php echo translate('Click menu above to view details on right'); ?>.</span>
		</div>
		<div class="percent_content float_left center_txt">
			<div class="center_block">
				<span class="dent_bl_txt">Jobtraq</span>
				<span class="dent_bl_txt">&copy<?php echo translate(' 2013 Anvy Digital.Inc - All Rights Reserved'); ?>.</span>
				<span class="dent_bl_txt"><?php echo translate('Current user'); ?>:
					<a style="color:black" href="<?php echo URL.DS.'contacts'.DS.'entry'.DS.(isset($_SESSION['arr_user']['_id']) ? $_SESSION['arr_user']['_id'] : ''); ?>"><?php echo (isset($_SESSION['arr_user']['full_name']) ? $_SESSION['arr_user']['full_name'] : ''); ?>
					</a>
				</span>
			</div>
		</div>
	</div>
</div><!--END Footer -->

<script type="text/javascript">
$(function(){
	$("a:first",".nav_setup").click();
})
	function studio_detail(id){
		$("a",".nav_setup").removeClass("active");
		$("#"+id).addClass("active");
		$.ajax({
			url:'<?php echo URL; ?>/settings/studio_detail/' + id,
			timeout: 15000,
			success: function(html){
				$("div#list_detail").html(html);
			}
		});
	}
</script>