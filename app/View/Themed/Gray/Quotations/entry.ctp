<?php echo $this->element('../'.$name.'/tab_option'); ?>
<div id="content">
	<div class="jt_ajax_note">Loading...</div>
    <div class="jt_ajax_email hidden" style="top:140px;">Sending emails, please waiting for a moment...</div>
    <!-- Title -->
    <div class="jbcont">
        <div class="jt_module_title float_left jt_t_left" style="display: none;">
            <h1>
                <span id="md_<?php if(isset($arr_settings['title_field'][0])) $tit_0 = $arr_settings['title_field'][0]; echo $tit_0;?>">
                    <?php if(isset($item_title[$tit_0])) echo $item_title[$tit_0];?>
                </span>
                <span class="md_center">
					<?php if(isset($item_title[$tit_0]) && $item_title[$tit_0]!='' && $item_title[$arr_settings['title_field'][1]]!='') echo '-';?>
                </span>
                <span id="md_<?php if(isset($arr_settings['title_field'][1])) $tit_1 = $arr_settings['title_field'][1]; echo $tit_1;?>">
                    <?php if(isset($item_title[$tit_1])) echo $item_title[$tit_1];?>
                </span>
             </h1>
        </div>
        <div class="jt_module_title float_right jt_t_right" style="display: none;">
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
    <?php
        $show = true;
        if(!isset($query['company_id']) || !is_object($query['company_id']) ) {
            $show = false;
        } else if( !isset($_SESSION[$controller.'_summary_entry']) || !$_SESSION[$controller.'_summary_entry'] ) {
            $show = false;
        }
    ?>
    <div id="<?php echo $controller ?>_entry_summary" <?php if( !$show ) { ?> style="display: none;" <?php } ?>>
        <div class="clear_percent">
            <div class="jt_panel" style=" width:30%;float:left; margin-bottom: 15px;">
                <div class="jt_box" style=" width:100%;">
                    <div class="jt_box_line">
                        <div class=" jt_box_label fixbor" style=" width:25%;">
                            <button type="button" class="btn_pur" onclick="showSummary()">Detail</button>
                            Ref no
                        </div>
                        <div class="jt_box_field " style=" width:30%;text-align:right;">
                            <input class="input_1 float_left  " type="text" data-name="code" value="<?php echo isset($query['code']) ? $query['code'] : '' ?>" readonly="readonly">
                        </div>
                    </div>
                    <div class="jt_box_line">
                        <div class=" jt_box_label fixbor2" style=" width:25%; height: 35px;">
                            <span class="jt_box_line_span ">Company</span>

                        </div>
                        <div class="jt_box_field " style=" width:71%;">
                            <input class="input_1 float_left" data-name="company_name" type="text" value="<?php echo isset($query['company_name']) ? $query['company_name'] : '' ?>" style=" padding-left:2%;" readonly="readonly">
                        </div>
                    </div>
                </div>
            </div>
            <div class="jt_panel" style=" width:69%;float:right;">
                <div class="tab_1_inner float_left float_left" >
                    <p class="clear">
                        <span class="label_1 float_left minw_lab fixbor"> Contact</span>
                    </p>
                    <div class="width_in3 float_left indent_input_tp">
                        <input class="input_1 float_left  "  data-name="contact_name" type="text" value="<?php echo isset($query['contact_name']) ? $query['contact_name'] : '' ?>" style=" padding-left:2%;" readonly="readonly">
                    </div>
                    <p class="clear">
                        <span class="label_1 float_left minw_lab fixbor2"> Our Rep</span>
                    </p>
                    <div class="width_in3 float_left indent_input_tp">
                        <input class="input_1 float_left  "  data-name="our_rep" type="text" value="<?php echo isset($query['our_rep']) ? $query['our_rep'] : '' ?>" style=" padding-left:2%;" readonly="readonly">
                    </div>
                </div>
                <div class="tab_1_inner float_left float_left" >
                    <p class="clear">
                        <span class="label_1 float_left minw_lab"> Date</span>
                    </p>
                    <div class="width_in3 float_left indent_input_tp">
                        <input type="text" class="input_1 float_left  " data-name="quotation_date" value="<?php echo isset($query['quotation_date']) && is_object($query['quotation_date']) ? date('d M, Y', $query['quotation_date']->sec) : '' ?>" readonly="readonly">
                    </div>
                    <p class="clear">
                        <span class="label_1 float_left minw_lab " style=" height: 34px;"> Due Date</span>
                    </p>
                    <div class="width_in3 float_left indent_input_tp" >
                        <input type="text" class="input_1 float_left  " data-name="payment_due_date"  value="<?php echo isset($query['payment_due_date']) && is_object($query['payment_due_date']) ? date('d M, Y', $query['payment_due_date']->sec) : '' ?>" readonly="readonly">
                    </div>
                </div>
                <div class="tab_1_inner float_left float_left" >
                    <p class="clear">
                        <span class="label_1 float_left minw_lab " style="height: 58px;"> Status</span>
                    </p>
                    <div class="width_in3 float_left indent_input_tp" >
                        <input type="text" class="input_1 float_left  " value="<?php echo isset($query['quotation_status']) ? $query['quotation_status'] : '' ?>" data-name="quotation_status"  readonly="readonly">
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div id="<?php echo $controller;?>_form_auto_save" <?php if( $show ) { ?> style="display: none;" <?php } ?> >
        <!-- Add form -->
        <form class="form_<?php echo $controller;?>" action="" method="post" class="float_left">
            <div class="clear_percent">
                <!--Elememt Panel type 01-->
                <?php echo $this->element('panel_group',array('datas'=>array('panel_1'),'css'=>'panel_1')); ?>

                <!--Elememt Panel type 02-->
                <?php echo $this->element('panel_group',array('datas'=>array('panel_2','panel_4'),'css'=>'panel_2')); ?>
            </div>
            <div class="clear"></div>

            <!--Elememt Sub Tab -->
        </form>
	</div>
    <?php echo $this->element('sub_tab');?>
    <!--Load cont of sub tab -->
    <div class="clear_percent" id="load_subtab">
        <span style="padding: 50%;"><img src="<?php echo URL.'/theme/'.$theme.'/images/ajax-loader.gif'; ?>" title="Loading..." /></span>
		<?php
    		/*if($sub_tab!='' && $sub_tab !='documents'){
    			// if($this->elementExists('../Themed/Default/'.$name.'/'.$sub_tab))
                if(file_exists(APP.'View'.DS.'Themed'.DS.'Default'.DS.$name.DS.$sub_tab.'.ctp' ))
    				echo $this->element('..'.DS.$name.DS.$sub_tab);
    			else
    				echo $this->element('../Elements/box_type/subtab_box_default');
    		}else{
                echo $this->element('..'.DS.$name.DS.$sub_tab);
            }*/
		?>
    </div>
	<?php echo $this->element('../'.$name.'/js'); ?>
</div>
<input type="hidden" id="position_store" value="" />

<style>
	.k-select{
		display:none!important;
	}
	.k-numeric-wrap{
		border-radius:0!important;
		margin:0!important;
		padding:0!important;
		border:none!important;
		border-bottom: 1px solid #dddddd!important;
	}
</style>
<script type="text/javascript">
    $(function(){
        if(window.location.href.indexOf("#") != -1) {
            var id = window.location.href.split("#");
            id = id[1];
            $("#"+id,".ul_tab").click();
        } else
            $("#<?php echo $sub_tab ?>",".ul_tab").click();
        notifyTop('<?php echo $this->Session->flash(); ?>');
    })
</script>