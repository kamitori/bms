<style type="text/css">
	html, body {
		margin: 0px;
		padding: 0px;
		height: 100%;
	}
	#wrapper, #content{
		height: 100%;
	}
	.cene {
		position: absolute;
		margin: 0;
		left: 50%;
	}
	.position-relative{
		position: relative;
	}
	#confirms_ok{
		float: right;
		margin-top: 0 !important;
		margin-left: 0 !important;
	}
</style>

<div id="header">
	<div class="logo logo_border">
		<a href="<?php echo URL; ?>/"><?php echo $this->Html->image('logo.png', array('alt' => 'Jobtraq')); ?></a>
	</div>
	<div class="setup_title">
		<div>
			<a href="<?php echo URL; ?>/"><button class="btn_return">Return</button></a>
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
				<li>
					<a id="general" href="javascript:void(0)" onclick="settings_list(this, 'general')" class="<?php if(isset($arr_settings)) { ?>active<?php } ?>"><?php echo translate('General'); ?></a>
				</li>
				<li><a id="list_and_menu" href="javascript:void(0)" onclick="settings_list(this, 'list_and_menu')" class="<?php if(isset($arr_settings)) { ?>active<?php } ?>"><?php echo translate('Lists & Menus'); ?></a></li>

				<li><a id="list_message" href="javascript:void(0)" onclick="settings_list(this, 'list_message')" class="<?php if ($action == "system_message") { ?>active<?php } ?>"><?php echo translate('System Message'); ?></a></li>

				<li><a id="auto_process" href="javascript:void(0)" onclick="settings_list(this, 'auto_process')" class="<?php if ($action == "auto_process") { ?>active<?php } ?>"><?php echo translate('Auto Process'); ?></a></li>

				<li><a id="equipments" href="javascript:void(0)" onclick="settings_list(this, 'equipments')" class="<?php if ($action == "equipments") { ?>active<?php } ?>"><?php echo translate('Assets'); ?></a></li>

				<!-- <li><a id="equipments" href="javascript:void(0)" onclick="settings_list(this, 'pricing_rules')" class="<?php if ($action == "equipments") { ?>active<?php } ?>"><?php echo translate('Pricing Rules'); ?></a></li> -->

				<li><a id="list_country" href="javascript:void(0)" onclick="settings_list(this, 'list_country')" class="<?php if ($action == "provinces") { ?>active<?php } ?>"><?php echo translate('Province'); ?></a></li>


				<?php if(1==1 || isset( $system_admin )){ ?>
				<li><a id="privileges" href="javascript:void(0)" onclick="settings_list(this, 'privileges')" class="<?php if ($action == "privileges") { ?>active<?php } ?>"><?php echo translate('Permissions'); ?></a></li>
				<?php } ?>

				<li><a id="roles" href="javascript:void(0)" onclick="settings_list(this, 'roles')" class="<?php if ($action == "roles") { ?>active<?php } ?>"><?php echo translate('Roles'); ?></a></li>

				<li><a id="user_roles" href="javascript:void(0)" onclick="settings_list(this, 'user_roles')" class="<?php if ($action == "roles") { ?>active<?php } ?>"><?php echo translate('User Roles'); ?></a></li>

				<li><a id="system_email" href="javascript:void(0)" onclick="settings_list(this, 'system_email')" class="<?php if ($action == "system_email") { ?>active<?php } ?>"><?php echo translate('System Email'); ?></a></li>

				<?php if($this->Common->check_permission('emailtemplates_@_entry_@_view',$arr_permission)): ?>
				<li><a onclick="iframe(this, '<?php echo URL.'/emailtemplates/entry' ?>')" href="javascript:void(0)" class="<?php if ($action == "email_template") { ?>active<?php } ?>" id="email_template"><?php echo translate('Email template'); ?></a></li>
				<?php endif; ?>

				<li><a id="hook_setting" href="javascript:void(0)" onclick="settings_list(this, 'hook_setting')" class="<?php if ($action == "hook_setting") { ?>active<?php } ?>"><?php echo translate('Hook Setting'); ?></a></li>

				<li><a id="language" href="javascript:void(0)" onclick="settings_list(this, 'language')" class="<?php if ($action == "language") { ?>active<?php } ?>"><?php echo translate('Language'); ?></a></li>
				<li><a id="support" href="javascript:void(0)" onclick="settings_list(this, 'support')" class="<?php if ($action == "support") { ?>active<?php } ?>"><?php echo translate('Support'); ?></a></li>
				<li>
					<a href="<?php echo URL.'/settings/studio'; ?>" >Studio</a>
				</li>
				<li>
					<a id="administrator" href="javascript:void(0)" onclick="settings_list(this, 'administrator')" class="<?php if(isset($arr_settings)) { ?>active<?php } ?>">Administrator</a>
				</li>
				<li>
					<a id="cache" href="javascript:void(0)"  onclick="settings_list(this, 'cache')" class="<?php if ($action == "cache") { ?>active<?php } ?>">Cache</a>
				</li>
				<li>
					<a id="sale_ruler" href="javascript:void(0)"  onclick="settings_list(this, 'sale_ruler')" class="<?php if ($action == "sale_ruler") { ?>active<?php } ?>">Sales ruler</a>
				</li>
				<li><a href="javascript:void(0)" style="background-color: #797979;;cursor:default">&nbsp;</a></li>

			</ul>
		</div>
		<div class="percent_content block_dent_a float_left">
			<div id="detail_for_main_menu">
				<?php if(isset($arr_settings))echo $this->element('../Settings/list_and_menu'); ?>
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
	<?php
		$setup_remember_page = 'general';
		if(isset($_GET['page']))
			$setup_remember_page = $_GET['page'];
		else if( $this->Session->check('setup_remember_page') )
			$setup_remember_page =  $this->Session->read('setup_remember_page');
	?>
	$(function(){
		 $("#<?php echo $setup_remember_page; ?>").click();
	});
	function settings_list(object, setting_function_name) {

		$("#settings_ul_nav_setup a").removeClass("active");
		$(object).addClass("active");

		$.ajax({
			url: "<?php echo URL; ?>/settings/" + setting_function_name,
			timeout: 15000,
			success: function(html) {

				$("div#detail_for_main_menu").html(html);
				//alert(html);
			}
		});
	}
	function iframe(object, url)
	{
		$("#settings_ul_nav_setup a").removeClass("active");
		$(object).addClass("active");
		$("div#detail_for_main_menu").html('<iframe style="display: none;" src="'+ url +'" ></iframe>');
		$('div#detail_for_main_menu iframe').load(function() {
			if (url.indexOf('<?php echo URL; ?>') != -1) {
				var iframe = $(this).contents();
				iframe.find('#header .top_opt').parent().remove();
				iframe.find('#header ul#header_nav').remove();
				iframe.find('#footer').remove();
			}
			$(this).show(function() {
				$(this).css({
					'width': '100%',
					'height': '700px'
				})
			});
		});
	}
</script>