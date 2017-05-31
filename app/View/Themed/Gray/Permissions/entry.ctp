<?php echo $this->element('../'.$name.'/tab_option'); ?>
<div id="content">
	<div class="jt_ajax_note">Loading...</div>
     <!-- Title -->
    <div class="jbcont">
        <div class="jt_module_title float_left jt_t_left">
            <h1>
                <span id="md_1">
                    Permissions 
                </span>
                <span class="md_center">
					:
                </span>
                <span id="md_2">
                    &nbsp;
                </span>
             </h1>
        </div>
        <div class="jt_module_title float_right jt_t_right">
            <h1>
                <span id="md_3">
                   	&nbsp;
                </span> 
                <span class="md_center">
					&nbsp;
                </span> 
                <span id="md_4">
                    &nbsp;
                </span>
            </h1>
        </div>
    </div>
    
    <div id="<?php echo $controller;?>_form_auto_save">
        <!-- Add form -->
        <form class="form_<?php echo $controller;?>" action="" method="post" class="float_left">
            <div class="clear_percent">
                <!--Elememt Panel type 01-->
                <?php echo $this->element('panel_group',array('datas'=>array('panel_1'),'css'=>'panel_1')); ?>
            </div>
            <div class="clear"></div>
             <!--Elememt Sub Tab -->
        	<?php echo $this->element('sub_tab');?>
        </form>
	</div>
    <!--Load cont of sub tab -->
    <div class="clear_percent" id="load_subtab">
		<?php if($sub_tab!='') echo $this->element('../'.$name.'/'.$sub_tab);?>
    </div>
	<?php echo $this->element('../'.$name.'/js'); ?>
</div>