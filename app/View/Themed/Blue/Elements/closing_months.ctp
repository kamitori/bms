<style type="text/css">
    #ui-datepicker-div{
        z-index: 99999 !important;
    }
</style>
<div id="content" style="padding-top: 25px;">
    <!-- Title -->
    <div id="closing_month_form_auto_save">
        <div class="tab_1 full_width">
            <span class="title_block bo_ra1">
                <span class="fl_dent">
                    <h4>
                        <?php echo translate('Closing Months'); ?></h4>
                </span>
                <div class="float_left hbox_form dent_left_form">
                    <a title="Add Month" id="bt_add_month">
                        <span class="icon_down_tl top_f"></span>
                    </a>
                </div>
            </span>
            <p class="clear"></p>
            <ul class="ul_mag clear bg3">
                <li class="hg_padd" style="width:1%"></li>
                <li class="hg_padd line_mg center_txt" style="width:10%">
                    <?php echo translate('Date From'); ?>
                </li>

                <li class="hg_padd line_mg center_txt" style="width:10%">
                    <?php echo translate('Date To'); ?>
                </li>

                <li class="hg_padd line_mg " style="width:35%;">
                    <?php echo translate('Description'); ?>
                </li>
                <li class="hg_padd line_mg center_txt" style="width:5%;">
                    <?php echo translate('Inactive'); ?>
                </li>
                <li class="hg_padd line_mg " style="width:10%;">
                    <?php echo translate('Created By'); ?>
                </li>
                <li class="hg_padd line_mg " style="width:10%;">
                    <?php echo translate('Modified By'); ?>
                </li>
                <li class="hg_padd line_mg center_txt" style="width:10%;">
                    <?php echo translate('Modified date'); ?>
                </li>
            </ul>

            <div id="closing_months_content" style=" overflow-y:auto;height:261px;">
                <?php
                    $i = $count = 0;
                    if($arr_months->count()){
                        foreach($arr_months as $month){
                ?>
                <ul class="ul_mag hasValue clear bg<?php echo $i; ?>" id="month_<?php echo $month['_id']; ?>">
                    <li class="hg_padd center_txt" style="width:1%"><input type="hidden" name="_id" value="<?php echo $month['_id']; ?>" /></li>
                    <li class="hg_padd center_txt" style="width:10%">
                        <input type="text" name="date_from" class="input_inner JtSelectDate input_inner_w bg<?php echo $i ?>" style="text-align:center;" value="<?php echo is_object($month['date_from']) ? date('m/d/Y', $month['date_from']->sec) : '' ?>" readonly />
                    </li>
                    <li class="hg_padd center_txt" style="width:10%">
                        <input type="text" name="date_to" class="input_inner JtSelectDate input_inner_w bg<?php echo $i ?>" style="text-align:center;" value="<?php echo is_object($month['date_to']) ?  date('m/d/Y', $month['date_to']->sec) : '' ?>" readonly />
                    </li>
                    <li class="hg_padd"  style="width:35%">
                        <input type="text" name="description" class="input_inner input_inner_w bg<?php echo $i ?>" value="<?php echo $month['description'] ?>" />
                    </li>
                    <li class="hg_padd center_txt" style="width:5%">
                        <input type="checkbox" name="inactive" <?php if($month['inactive']) echo 'checked'; ?> style="text-align:center;" />
                    </li>
                    <li class="hg_padd " style="width:10%">
                        <?php
                            $contact = $_contact->select_one(array('_id' => $month['created_by']),array('full_name'));
                        ?>
                        <span><?php echo isset($contact['full_name']) ? $contact['full_name'] : '' ?></span>
                    </li>
                    <li class="hg_padd " style="width:10%">
                        <?php
                            $contact = $_contact->select_one(array('_id' => $month['modified_by']),array('full_name'));
                        ?>
                        <span><?php echo isset($contact['full_name']) ? $contact['full_name'] : '' ?></span>
                    </li>
                    <li class="hg_padd center_txt" style="width:10%">
                        <span><?php echo date('d M, Y H:i:s',$month['date_modified']->sec); ?></span>
                    </li>
                </ul>
                <?php
                            $i = 3 - $i; $count += 1;
                        }
                    }
                    $count = 12 - $count;
                    if( $count > 0 ){
                        for ($j=0; $j < $count; $j++) {
                ?>
                        <ul class="ul_mag clear bg<?php echo $i; ?>"></ul>
                <?php $i = 3 - $i;
                        }
                    }
                ?>
            </div>
            <span class="title_block bo_ra2">
                <span class="float_left bt_block">
                </span>
            </span>
        </div>
    </div>
</div>
<script type="text/javascript">
$(function(){
    $("#closing_months_content").mCustomScrollbar({
        scrollButtons:{
            enable:false
        },
        advanced:{
            updateOnContentResize: true,
            autoScrollOnFocus: false,
        }
    });
    $("#bt_add_month").click(function(){
        $.ajax({
            url : "<?php echo URL.'/'.$controller.'/closing_months_add' ?>",
            success: function(result){
                result = $.parseJSON(result);
                var count  = $("ul.hasValue","#closing_months_content").length;
                if(count){
                    $("ul.hasValue:last","#closing_months_content").next().replaceWith(appendHTML(result));
                } else {
                    $("ul:first","#closing_months_content").remove();
                    $(".mCSB_container","#closing_months_content").prepend(appendHTML(result));
                }
                select_calendar(".JtSelectDate","#month_"+result._id);
            }
        })
    });
    $("#closing_months_content").on("change","input",function(){
        $.ajax({
            url : "<?php echo URL.'/'.$controller.'/closing_months_save' ?>",
            type: "POST",
            data: $("input",$(this).parent().parent()).serialize(),
            success: function(result){
                if(result!="ok"){
                    alerts("Message",result);
                }
            }
        })
    });
    select_calendar(".JtSelectDate");
})
function appendHTML(data)
{
    var count = $("ul.hasValue","#closing_months_content").length;
    var i = count % 2 == 0 ? 0 : 3;
    var html = '<ul class="ul_mag hasValue clear bg'+i+'" id="month_'+data._id+'">';
        html += '<li class="hg_padd center_txt" style="width:1%"><input type="hidden" name="_id" value="'+data._id+'" /></li>';
        html +=     '<li class="hg_padd center_txt" style="width:10%">';
        html +=         '<input type="text" name="date_from" class="input_inner JtSelectDate input_inner_w bg'+i+'" style="text-align:center;" value="'+data.date_from+'" readonly />';
        html +=     '</li><li class="hg_padd center_txt" style="width:10%">';
        html +=         '<input type="text" name="date_to" class="input_inner JtSelectDate input_inner_w bg'+i+'" style="text-align:center;" value="'+data.date_to+'" readonly />';
        html +=     '</li><li class="hg_padd"  style="width:35%">';
        html +=         '<input type="text" name="description" class="input_inner input_inner_w bg'+i+'" value="" />';
        html +=     '</li><li class="hg_padd center_txt" style="width:5%">';
        html +=         '<input type="checkbox" name="inactive"  style="text-align:center;" />';
        html +=     '</li><li class="hg_padd " style="width:10%">';
        html +=         '<span>'+data.created_by+'</span>';
        html +=     '</li><li class="hg_padd " style="width:10%">';
        html +=         '<span>'+data.modified_by+'</span>';
        html +=     '</li><li class="hg_padd center_txt" style="width:10%">';
        html +=         '<span>'+data.date_modified+'</span>';
        html +=     '</li></ul>';
    return html;
}
function select_calendar(class_selector,contain) {
    switch(localStorage.getItem('format_date')){
        case "d M, Y":
            date_format = "d M, yy";
            break;
        case "d-m-Y":
            date_format = "dd-mm-yy";
            break;
        case "d/m/Y":
            date_format = "dd/mm/yy";
            break;
        default:
            date_format = "d M, yy";
            break;
    }
    if (contain != undefined) {
        $(class_selector, contain).each(function() {
            var date_default = $(this).val();

            if ($.trim(date_default).length == 10 || $.trim(date_default).length == 0) {
                // $( this ).datepicker();
                $(this).datepicker({
                    changeMonth: true,
                    changeYear: true,
                    yearRange: "c-10:c",
                    // maxDate : "D"
                });
                if ($.trim(date_default) != "") {
                    $(this).datepicker("setDate", date_default);
                }
                $(this).datepicker("option", "showAnim", "slideDown");
                $(this).datepicker("option", "dateFormat", date_format);
            }

        });
    } else {
        $(class_selector).each(function() {
            var date_default = $(this).val();
            if ($.trim(date_default).length == 10 || $.trim(date_default).length == 0) {
                // $( this ).datepicker();
                $(this).datepicker({
                    changeMonth: true,
                    changeYear: true,
                    yearRange: "c-10:c",
                    // maxDate : "D"
                });
                if ($.trim(date_default) != "") {
                    $(this).datepicker("setDate", date_default);
                }
                $(this).datepicker("option", "showAnim", "slideDown");
                $(this).datepicker("option", "dateFormat", date_format);

            }

        });
    }
}
</script>
