<style type="text/css">
#confirms_window .jt_confirms_window_ok{
    width: 15%;
    height: 18%;
    margin-top: 14%;
    margin-left: 80%;
}
.changePadding ul li{
    padding-left: 4px !important;
}
.clear_percent_19 {
width: 20%;
}
.clear_percent_11 {
width: 77%;
}
</style>
<div class="clear_percent">
    <div class="clear_percent_19 float_left">
        <div class="tab_1 full_width changePadding">
            <span class="title_block bo_ra1">
                <span class="fl_dent"><h4><?php echo translate('List of modules'); ?></h4></span>
            </span>
            <ul class="ul_mag clear bg3">
                <li class="hg_padd center_text no_border"><?php echo translate('Modules'); ?></li>
            </ul>
            <div id="support_height" class="container_same_category" style="height: 449px;overflow-y: auto">
                <ul class="find_list setup_menu">
                <?php $i = 1;$count = 0;
                foreach ($arr_modules as $key => $value) { $count += 1; ?>
                    <li>
                        <a class="clickfirst" href="javascript:void(0)" onclick="settings_support(this, '<?php echo $value['name']; ?>')"><?php echo $value['name']; ?></a>
                    </li>
                <?php $i = 3 - $i;
                }
                echo '</ul>';

                if ($count < 20) {
                $count = 20 - $count;
                for ($j = 0; $j < $count; $j++) {
                    $i = 3 - $i;
                    ?>
                    <ul class="find_list setup_menu">
                    </ul>
                    <?php
                }
            }
            ?>

            </div>
            <span class="title_block bo_ra2"></span>
        </div><!--END Tab1 -->
    </div>
    <div class="clear_percent_9_arrow float_left">
        <div class="full_width box_arrow">
            <span class="icon_emp" style="cursor:default"></span>
        </div>
    </div>
    <div class="clear_percent_11 float_left" id="support_detail">
        <!-- Detail -->
    </div>
</div>

<style type="text/css">
#support_height ul:hover, #support_height ul:hover input{
    background-color: #B8B8B8;
}
</style>

<script type="text/javascript">
    $(window).load(function() {
        $('.container_same_category').mCustomScrollbar({
            scrollButtons:{
                enable:false
            }
        });
    });
</script>

<script type="text/javascript">

    $(function(){
        $('.container_same_category').mCustomScrollbar({
            scrollButtons:{
                enable:false
            }
        });
        $(".clickfirst:first", "#support_height").click(); // click menu li dau tien khi page load xong
    });

    // click cot 2
    function settings_support(object, name){

        $("#support_height a").removeClass("active").parents("li").removeClass("active");
        $(object).addClass("active").parents("li").addClass("active");
        settings_support_update(name);
    }
    function settings_support_update(name){
        $.ajax({
            url: '<?php echo URL; ?>/settings/support_detail/' + name,
            timeout: 15000,
            success: function(html){

                $("div#support_detail").html(html);
                $('.container_same_category', "#support_detail").mCustomScrollbar({
                    scrollButtons:{
                        enable:false
                    }
                });
            }
        });
    }
</script>