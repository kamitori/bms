<?php echo $this->element('../'.$name.'/tab_option'); ?>
<div id="content">
	<div class="jt_ajax_note">Loading...</div>
    <!-- Title -->
    <div class="jbcont">
        <div class="jt_module_title float_left jt_t_left">
            <h2>&nbsp;</h2>
        </div>
        <div class="jt_module_title float_right jt_t_right">
            <h2></h2>
        </div>
    </div>

    <!-- Add form -->
    <form id="search_form" class="form_<?php echo $controller;?>" action="" method="post" class="float_left">
        <div class="clear_percent">
            <!--Elememt Panel type 01-->
            <?php echo $this->element('panel_group',array('datas'=>array('panel_1'),'css'=>'panel_1')); ?>
            
            <!--Elememt Panel type 02-->
            <?php echo $this->element('panel_group',array('datas'=>array('panel_2','panel_3','panel_4'),'css'=>'panel_2')); ?>
        </div>
        <div class="clear"></div>
        
        <!--Elememt Sub Tab -->
        <?php //echo $this->element('sub_tab');?>
    </form>


    <!--Load cont of sub tab -->
    <div class="clear_percent" id="load_subtab">
		<?php //if($sub_tab!='') echo $this->element('../'.$name.'/'.$sub_tab);?>
    </div>
	<?php echo $this->element('../'.$name.'/js_search'); ?>
    
</div>
