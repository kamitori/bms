<?php echo $this->element('../'.$name.'/tab_option'); ?>
<div id="content">
	<div class="jt_ajax_note">Loading...</div>
    <!-- Title -->
    <div class="jbcont">
        <div class="jt_module_title float_left jt_t_left">
            <h1>
                <span>History</span>
                <span class="md_center"></span>
                <span></span>
             </h1>
        </div>
        <div class="jt_module_title float_right jt_t_right"></div>
    </div>

    <div id="<?php echo $controller;?>_history" class="clear_percent">
        <?php
			$arr_val = $lock_settings['relationship']['history']['block']['history_detail'];
			$panel = $lock_settings['relationship']['history']['block'];
			//pr($arr_val);
			echo $this->element('box',array('key'=>'history_detail','arr_val'=>$arr_val,'panel'=>$panel));
			//pr($arr_history);
		?>
	</div>
	<?php echo $this->element('../'.$name.'/js'); ?>

</div>