<?php echo $this->element('../'.$name.'/tab_option'); ?>
<div id="content">
	<div class="jt_ajax_note">Loading...</div>
    <!-- Title -->
    <div class="jbcont">
        <div class="jt_module_title float_left jt_t_left">
            <h1>
                <span id="md_<?php if(isset($arr_settings['title_field'][0])) $tit_0 = $arr_settings['title_field'][0]; echo $tit_0;?>">
                    <?php if(isset($item_title[$tit_0])) echo $item_title[$tit_0];?>
                </span>
                <span class="md_center">
					<?php if(isset($item_title[$tit_0]) && $item_title[$tit_0]!='' && isset($item_title[$arr_settings['title_field'][1]]) && $item_title[$arr_settings['title_field'][1]]!='') echo '-';?>
                </span>
                <span id="md_<?php if(isset($arr_settings['title_field'][1])) $tit_1 = $arr_settings['title_field'][1]; echo $tit_1;?>">
                    <?php if(isset($item_title[$tit_1])) echo $item_title[$tit_1];?>
                </span>
             </h1>
        </div>
        <div class="jt_module_title float_right jt_t_right">
            <h1>
                <span id="md_<?php if(isset($arr_settings['title_field'][2])) $tit_2 = $arr_settings['title_field'][2]; echo $tit_2;?>">
                    <?php if(isset($item_title[$tit_2])) echo $item_title[$tit_2];?>
                </span>
                <span class="md_center">
					<?php if(isset($item_title[$tit_2])) echo '-';else echo '&nbsp;';?>
                </span>
                <span id="md_<?php if(isset($arr_settings['title_field'][3])) $tit_3 = $arr_settings['title_field'][3]; echo $tit_3;?>">
                    <?php if(isset($item_title[$tit_3])) echo $item_title[$tit_3];?>
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

                <!--Elememt Panel type 02 -3 04-->
                <?php echo $this->element('panel_group',array('datas'=>array('panel_2','panel_3','panel_4'),'css'=>'panel_3')); ?>

            </div>
            <div class="clear"></div>

            <!--Elememt Sub Tab -->
            <?php echo $this->element('sub_tab');?>
        </form>
	</div>

    <!--Load cont of sub tab -->
    <div class="clear_percent" id="load_subtab">
        <span style="padding: 50%;"><img src="<?php echo URL.'/theme/'.$theme.'/images/ajax-loader.gif'; ?>" title="Loading..." /></span>
		<?php
			/*if($sub_tab!=''){
				// if($this->elementExists('../Themed/Default/'.$name.'/'.$sub_tab))
                if(file_exists(APP.'View'.DS.'Themed'.DS.'Default'.DS.$name.DS.$sub_tab.'.ctp' ))
					echo $this->element('..'.DS.$name.DS.$sub_tab);
				else
					echo $this->element('../Elements/box_type/subtab_box_default');
			}
            echo $this->element('..'.DS.$name.DS.$sub_tab);*/
		?>
    </div>
	<?php echo $this->element('../'.$name.'/js'); ?>
</div>
<script type="text/javascript">
    $(function(){
        if(window.location.href.indexOf("#") != -1) {
            var id = window.location.href.split("#");
            id = id[1];
            $("#"+id,".ul_tab").click();
        } else {
            $("#<?php echo $sub_tab ?>",".ul_tab").click();
        }
    });
</script>