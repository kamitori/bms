
    <div data-role="header" class="header_lg">
        <section class="logo"><img src="<?php echo URL ?>/theme/mobile/img/logo.png"></section>
    </div>
    <div class="content">
        <div data-role="popup" id="alert" class="ui-content" data-theme="a">
        </div>
        <section class="login-pgs">
			<?php echo $this->element('message'); ?>
            <form class="login" action="" method="POST" data-ajax="false" data-transition="slide">
                <input type="text" name="txt_user_name" autofocus="autofocus" id="username" data-clear-btn="true" placeholder="Username">
                <input type="password" name="txt_user_pass" id="password" class="input_pass">
                <section class="remember_pss">
                    <label for="red" class="un_hover">Do you want to remember password?</label>
                    <input type="checkbox" name="rememberMe" id="red" value="Do you want to remember password?">
                </section>
                <div class="btn_flt marg btn_chane"><input type="submit" id="submitButton" value="Sign in"></div>
                <p class="clear"></p>
            </form>
        </section>
        <!-- <div id="deviceready" class="blink">
            <p class="event listening">Connecting to Device</p>
            <p class="event received">Device is Ready</p>
            <a href="pages/pages_2.html">pages_2.html</a>
        </div> -->
    </div>
