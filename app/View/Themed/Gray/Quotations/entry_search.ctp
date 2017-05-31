<?php echo $this->element('../'.$name.'/tab_option'); ?>
<link href="<?php echo URL ?>/plugins/jquery-datepick/jquery.datepick.css" rel="stylesheet">
<style type="text/css" media="screen">
#block_full_products .jt_subtab_box_cont {
    min-height: 100px !important;
}
</style>
<div id="content">
	<div class="jt_ajax_note">Loading...</div>
    <!-- Title -->
    <div class="jbcont">
        <div class="jt_module_title float_left jt_t_left">
            <h2>&nbsp;</h2>
        </div>
        <div class="jt_module_title float_right jt_t_right">
            <h2>&nbsp;</h2>
        </div>
    </div>

    <!-- Add form -->
    <form id="search_form" class="form_<?php echo $controller;?>" action="" method="post" class="float_left">
        <div class="clear_percent">
            <!--Elememt Panel type 01-->
            <?php echo $this->element('panel_group',array('datas'=>array('panel_1'),'css'=>'panel_1')); ?>

            <!--Elememt Panel type 02-->
            <?php echo $this->element('panel_group',array('datas'=>array('panel_2','panel_4'),'css'=>'panel_2')); ?>
        </div>
        <div class="clear"></div>
        <div class="clear block_dent3">
            <div class="box_inner">
                <ul class="ul_tab">
                </ul>
            </div>
        </div>
        <!--Load cont of sub tab -->
        <div class="clear_percent" id="load_subtab">
            <div class="float_left " style=" width:100%;margin-top:0;">
                <div class="tab_1 full_width" id="block_full_products">
                    <!-- Header-->
                    <span class="title_block bo_ra1">
                <span class="fl_dent">
                    <h4>Details</h4>
                </span>
                    </span>
                    <!--CONTENTS-->
                    <div class="jt_subtab_box_cont" style=" height:0; height: auto !important; min-height: 250px;px;">
                    <?php
                        echo $this->element('box_type/listview_box', array('arr_subsetting'=>$arr_settings['relationship']['line_entry']['block'],'blockname' => 'products', 'subdatas' => $subdatas, 'no_js' => true));
                    ?>
                    </div>
                    <!--<span class="hit"></span>-->
                    <!--Footer-->
                    <span class="title_block bo_ra2">
                                                             </span>
                </div>
            </div>
        </div>
    </form>
	<?php echo $this->element('../'.$name.'/js_search'); ?>
</div>
<script src="<?php echo URL ?>/plugins/jquery-datepick/jquery.plugin.min.js"></script>
<script src="<?php echo URL ?>/plugins/jquery-datepick/jquery.datepick.min.js"></script>
<script type="text/javascript">
$('#block_full_products input').attr('placeholder', 1).addClass('jt_input_search');
$(function() {
    $( ".hasDatepicker" ).datepicker('destroy')
        .each(function() {
            $(this).datepick({
                rangeSelect: true,
                dateFormat: 'dd M, yyyy',
                autoSize: true,
            });
        });
    var html = '<div id="sum-input" title="You can use >, <, >=, <=, fromNumber-toNumber" style="margin:0 2% 0 0; width:35%;padding:0; float:right;">' +
                    '<input class="input_w2" type="text" style=" width:14.5%; text-align:right;padding:0 1% 0 0; margin:-2px 1.5% 0 0%;float:right;color:#333; " name="sum_amount" value="">' +
                    '<input class="input_w2" type="text" style=" width:15.5%;text-align:right;padding:0 1% 0 0;margin:-2px 0.5% 0 0%;float:right;color:#333;" name="sum_tax" value="">' +
                    '<input class="input_w2" type="text" style=" width:16%; text-align:right; padding:0 1% 0 0;margin:-2px 0.5% 0 0%;float:right;color:#333;" name="sum_sub_total" value="">' +
                     '<div class="float_left" style=" width:20%;margin:0;padding:0; text-align:right;float:right;">' +
                        'Totals&nbsp;' +
                    '</div>' +
                '</div>';
    if( !$('#block_full_products #sum-input').length ) {
        $('#block_full_products .title_block.bo_ra2').html(html);
    }
});
</script>