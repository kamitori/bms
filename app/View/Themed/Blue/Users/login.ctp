<div id="wrapper">
	<div class="header">
		<div class="logo logo_fix"><a href="home.html"><?php echo $this->Html->image('logo.png', array('alt' => 'logo JobTraq')); ?></a></div>
	</div>
	<div class="menu">
		<div class="bg_menu"></div>
		<div class="logo_inner">
			<div class="box_logo_inner">
				<span class="bg_left"></span>
				<span class="bgmain_logoinner"><?php echo $this->Html->image('logo_inner.png', array('alt' => 'logo JobTraq')); ?></span>
				<span class="bg_right"></span>
			</div>
		</div>
	</div><!--END Header -->
	<div id="content">
	   <?php echo $this->Form->create('User', array('class' => 'login')); ?>

			<?php if( $this->Session->check('REFERRER_LOGIN') ){ ?>
			<input type="hidden" value="<?php echo $this->Session->read('REFERRER_LOGIN'); ?>" name="referrer">
			<?php } ?>

			<div class="title_login">
				<span class="linelg_left"></span>
				<h2>Login</h2>
				<span class="linelg_right"></span>
			</div>
			<?php echo $this->element('message'); ?>
			<p class="put">
				<span class="height_in"><input autofocus="autofocus" id="username" type="text" required="" placeholder="Username" name="txt_user_name" class="input_user"></span>
				<span class="height_in"><input id="password" type="password" required="" placeholder="Password" name="txt_user_pass" class="input_pass"></span>
			</p>
			<div class="btn_div">
				<input type="submit" class="btn" value="Submit">
				<input type="reset" class="btn" value="Cancel">

			</div>
		<?php echo $this->Form->end(); ?>
	</div><!--END Content -->
	<div id="footer">
		<div class="bg_footer"></div>
	</div><!--END Footer -->
</div>
