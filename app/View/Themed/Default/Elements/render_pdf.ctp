<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<link href="<?php echo URL; ?>/favicon.ico" type="image/x-icon" rel="icon">
<link href="<?php echo URL; ?>/favicon.ico" type="image/x-icon" rel="shortcut icon">
<title><?php echo (isset($arr_data['report_name']) ? $arr_data['report_name']: ''); ?></title>
<?php
    echo $this->Html->css('report');
?>
<?php if(isset($arr_data['pages'])): ?>
<link rel="stylesheet" type="text/css" media="screen" href="<?php echo URL ?>/css/jquery.modal.css">
<style type="text/css" media="screen">
#page-form.modal {
  border-radius: 0;
  line-height: 18px;
  padding: 0;
  font-family: "Lucida Grande", Verdana, sans-serif;
}

#page-form h3 {
  margin: 0;
  padding: 10px;
  color: #fff;
  font-size: 14px;
  background: #911b12;
}

#page-form.modal p { padding: 20px 30px; border-bottom: 1px solid #ddd; margin: 0;
  background: -webkit-gradient(linear,left bottom,left top,color-stop(0, #eee),color-stop(1, #fff));
  overflow: hidden;
}
#page-form.modal p:last-child { border: none; }
#page-form.modal p label { margin-left: 25px; font-weight: bold; color: #333; font-size: 13px; width: 110px; line-height: 22px; }
#page-form.modal p input {
  font: normal 12px/18px "Lucida Grande", Verdana;
  padding: 3px;
  border: 1px solid #ddd;
  width: 200px;
}
</style>
<?php endif; ?>
<?php if(!isset($arr_data['is_custom'])): ?>
<style type="text/css" media="print,screen">
table {
   page-break-inside:always;
 }
tr{
  page-break-inside:avoid;
  page-break-after:auto;
 }
@media print {
    thead {display: table-header-group;}
    tfoot { display:table-footer-group; }
}
</style>
<?php endif; ?>
<style type="text/css" media="print,screen">
@media print {
    .no_print, .process-wrapper, .background_process, .print_button{display: none;}
    .wrapper{
        width:100%!important;
    }
}
@media screen {
    .table_content tbody tr td {
        font-size: 1.1vmax;
    }
}
<?php if(isset($arr_data['font-size']) && is_numeric($arr_data['font-size'])){ ?>
.table_content tbody tr td {
    font-size: <?php echo $arr_data['font-size']; ?>px !important;
}
<?php } ?>
</style>
<style type="text/css">
.no_break {
    page-break-inside: avoid !important;
    page-break-after: avoid !important;
}
.hidden{
    display: none;
}
.asset_table td{
    border-right: 1px solid #d3d3d3 !important;
    height: 50px;
    background-color: #911b12;
    color: #fff;
    font-size: 18px;
    font-weight: 900;
}
.asset_product td{
    border-right: none !important;
}
.content_asset td{
    border-right: 1px solid #E5E4E3 !important;
}
.parent_product td{
    font-weight: 900;
    color:  #911b12;
}
.asset_product{
    margin-bottom: 5px;
}
.asset_product a{
    text-decoration: none;
}
.footer {
    display: table; width: 100%; margin: 100px auto 0; border-top: 1px solid #000;
}
<?php if( DS == '\\' ) { ?>
.footer_ul {
    font-family: "Times New Roman", Times, serif;
    font-size: 10.5px;
}
<?php } else { ?>
.footer_ul {
    font-family: "Times New Roman", Times, serif;
    font-size: 10px;
}
<?php } ?>
.footer_li {
    list-style: none; float: left; margin-right: 20px;
}
#note{
    margin-top: 20px;
    page-break-inside:avoid;
    page-break-after:auto;
}
#note strong {
    text-decoration: underline;
    font-weight: 900;
}
#note div {
    border-bottom: 1pt dashed #9f9f9f;
    margin-bottom: 30px;
}
</style>
<?php if(isset($arr_data['custom_css'])){ ?>
<style type="text/css">
<?php echo $arr_data['custom_css']; ?>
</style>
<?php } ?>
</head>
<?php
    //Set trước biến
    $left_info = array();
    if(isset($arr_data['left_info'])){
        $left_info['label'] = (isset($arr_data['left_info']['label']) ? $arr_data['left_info']['label'] : '');
        $left_info['name'] = $arr_data['left_info']['name'];;
        $left_info['address'] = $arr_data['left_info']['address'];
    }
    $right_info = array();
    if(isset($arr_data['right_info'])){
        $right_info['label'] = (isset($arr_data['right_info']['label']) ? $arr_data['right_info']['label'] : '');
        $right_info['name'] = $arr_data['right_info']['name'];;
        $right_info['address'] = $arr_data['right_info']['address'];
    }
    $custom_info = false;
    $main_info = '';
    if(isset($arr_data['custom_main_info'])){
        $custom_info = true;
        $main_info = $arr_data['custom_main_info'];
    }
    else if(isset($arr_data['main_info'])){
         foreach($arr_data['main_info'] as $key=>$value)
            $main_info .= '<p><span class="bold_text">'.$key.'</span>'.$value.'</p>';
    }
    $report_name = '';
    if(isset($arr_data['report_name'])){
        $report_name = $arr_data['report_name'];
        $report_name = $report_name;
        $tmp_string = explode('(',$report_name,2);
        $first_string = ucwords($tmp_string[0]);
        $first_string=trim($first_string);
        $first_string = explode(" ",$first_string);
        $report_name = '';
        $i = 0;
        $count = count($first_string);
        foreach($first_string as $value){
            $space= (++$i<$count ? '&nbsp;' : '');
            $first_letter = substr($value, 0, 1);
            $report_name .= str_replace($first_letter, '<span class="color_red">'.$first_letter.'</span>', $value).$space;
        }
        if(isset($tmp_string[1])){
            $report_name .= '<div class="bold_txt" style="margin-top:5px;">( '.ucfirst($tmp_string[1]).'</div>';
        }
    }
    $print_time = $this->Common->format_date();
?>
<body>
    <input id="is_completed" type="hidden" value="" />
    <?php if(!isset($arr_data['is_custom'])): ?>
    <div class="wrapper">
        <div class="header">
            <div class="logo">
                <img src="<?php echo URL; ?>/img/logo.png" alt="" />
                <div style=" padding: 10px 0; width:35.5%;">
                    <p>3145 - 5th Ave NE</p>
                    <p>Calgary  AB  T2A  6A3 </p>
                </div>
                 <?php if(!empty($left_info)){ ?>
                <div style="padding: 10px 0 20px;float:left">
                    <p style="font-weight:bold;"><?php echo $left_info['label']; ?></p>
                    <p><?php echo $left_info['name']; ?></p>
                    <?php echo $left_info['address']; ?>
                </div>
                <?php } ?>
                <?php if(!empty($right_info)){ ?>
                <div style="padding: 10px 0 20px;float:right">
                    <p style="font-weight:bold"><?php echo $right_info['label']; ?></p>
                    <p><?php echo $right_info['name']; ?></p>
                    <?php echo $right_info['address']; ?>
                </div>
                <?php } ?>
                <div style="padding: 10px 0 20px;float:right">
                    <p>Phone number:<b>403-454-8644</b></p>
                </div>
                
            </div>
            <div class="title_report">
                <div class="box">
                    <?php echo $report_name; ?>
                    <?php if(isset($arr_data['date_from_to'])) echo '<div class="bold_txt" style="margin-top:5px;">'.$arr_data['date_from_to'].'</div>'; ?>
                </div>
                <div class="printed">
                    <p><span style="font-weight:bold">Printed at:</span> <?php echo $print_time; ?></p>
                </div>
            </div>
            <p class="clear"></p>
        </div>
        <div class="p_height"></div>
    </div>
    <?php endif; ?>
    <div class="background_process hidden">&nbsp;</div>
    <div class="process-wrapper html5-progress-bar hidden">
        <div class="progress-bar-wrapper">
            <progress id="progressbar" value="0" max="100"></progress>
            <span class="progress-value">0%</span>
        </div>
    </div>
    <a href="javascript:void(0)" id="print_button" class="print_button">Export PDF</a>
    <?php if(isset($arr_data['excel_url'])){ ?>
    <a href="<?php echo $arr_data['excel_url']; ?>" target="_blank" style="top: 165px" class="print_button">Export Excel</a>
    <?php } ?>
    <?php if(!isset($arr_data['is_custom'])){  ?>
    <?php if(isset($arr_data['report_heading'])){ ?>
    <div class="report_heading"><?php echo $arr_data['report_heading']; ?></div>
    <?php } ?>
    <div class="line"></div>
    <?php } ?>
    <div class="wrapper">
        <?php if(!isset($arr_data['is_custom'])){ ?>
        <table class="table_content" border="1" >
            <thead>
                <tr>
                    <?php
                        if(isset($arr_data['title'])){
                            $count = count($arr_data['title']);
                            $html = '';
                            foreach($arr_data['title'] as $key=>$value){
                                if(is_numeric($key))
                                    $html .= '<th>'.$value.'</th>';
                                else
                                    $html .= '<th style="'.$value.'">'.$key.'</th>';
                            }
                            echo $html;
                        }
                    ?>
                </tr>
            </thead>
            <!-- <tfoot>
                <tr>
                    <td <?php if(isset($count)&&$count>1) echo 'colspan="'.$count.'"'; ?>>
                        <div class="line_1"></div>
                        <div class="footer">
                            <?php echo $arr_data['footer']; ?>
                        </div>
                    </td>
                </tr>
            </tfoot> -->
             <tbody>
                <?php
                    if(isset($arr_data['content']))
                        echo $arr_data['content'];
                ?>
            </tbody>
        </table>
        <?php } else{ ?>
            <?php foreach($arr_data['content'] as $value){ ?>
                    <div class="wrapper" style=" width:100%;">
                        <div class="header">
                            <div class="logo" style="width:40%;">
                                <?php if( isset($arr_data['image_logo']) || isset($value['default_top_left_info']) ){ ?>
                                <img src="<?php echo URL; ?>/img/logo.png" alt="" />
                                <?php } ?>
                                <div style=" padding: 7px 0 10px; width:100%;">
                                    <?php if(!isset($arr_data['custom_top_left_info']) || isset($value['default_top_left_info'])){ ?>
                                    <p>3145 - 5th Ave NE</p>
                                    <p>Calgary  AB  T2A  6A3 </p>
                                    <?php } else { echo $arr_data['custom_top_left_info'];} ?>
                                </div>
                                <?php if(isset($value['custom_left_info'])): ?>
                                <?php echo $value['custom_left_info']; ?>
                                <?php else: ?>
                                <?php if(!empty($left_info)){ ?>
                                <div style="padding: 10px 0 20px;float:left">
                                    <?php if($left_info['label']!=''){ ?>
                                    <p style="font-weight:bold;"><?php echo $left_info['label']; ?></p>
                                    <?php } ?>
                                    <p><?php echo $left_info['name']; ?></p>
                                    <?php echo $left_info['address']; ?>
                                </div>
                                <?php } ?>
                                <?php endif; ?>
                            </div>
                            <?php if(isset($value['qr_url'])){ ?>
                            <div style="float:left; margin-top: -17px;">
                                <img src="<?php echo $value['qr_url']; ?>" />
                                <?php if(!empty($right_info)){ ?>
                                    <div style="margin-left: 24px;">
                                        <?php if($right_info['label']!=''){ ?>
                                        <p style="font-weight:bold;"><?php echo $right_info['label']; ?></p>
                                        <?php } ?>
                                        <p><?php echo $right_info['name']; ?></p>
                                        <?php echo $right_info['address']; ?>
                                    </div>
                                <?php } ?>
                            </div>
                            <?php } ?>
                            <div class="title_report" <?php if( !isset($value['qr_url'])){ ?>style="width: 60%;"<?php } ?> >
                                <div class="box">
                                    <?php
                                        if(isset($value['report_name']))
                                            echo $value['report_name'];
                                       else
                                            echo $report_name;
                                    ?>
                                    <?php
                                        if(isset($arr_data['date_from_to']))
                                            echo '<div class="bold_txt" style="margin-top:5px;">'.$arr_data['date_from_to'].'</div>';
                                    ?>
                                </div>
                                <?php if(!$custom_info){ ?>
                                <div class="printed">
                                    <p><span style="font-weight:bold">Printed at:</span> <?php echo $print_time; ?></p>
                                    <?php echo $main_info; ?>
                                </div>
                                <?php } else{
                                    if(isset($value['custom_main_info']))
                                        echo $value['custom_main_info'];
                                    else
                                        echo $main_info;
                                }?>
                            </div>
                        <p class="clear"></p>
                    </div>
                    <?php if(isset($arr_data['report_heading'])){ ?>
                    <div class="report_heading"><?php echo $arr_data['report_heading']; ?></div>
                    <?php } ?>
                    <div class="p_height"></div>
                    <!-- <div class="line" style="margin-bottom: 5px;"></div> -->
                </div>
               <?php echo $value['html']; ?>
               <div class="no_print" style="padding-bottom: 100px;border-top: 1px solid; clear: both;"></div>
            <?php }//end foreach ?>
        <?php } //end if ?>
    </div>
    </div>

<?php
    unset($arr_data['content'],$arr_data['footer'],$arr_data['title'],$arr_data['report_heading'],$arr_data['date_from_to']);
    $arr_data['report_url'] = "http://".$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'];
    echo $this->Html->script('jquery-1.10.2.min');
?>
<?php if(isset($arr_data['pages'])): ?>
<form action="javascript:void(0)" id="page-form" class="modal" style="position: fixed; top: 50%; left: 50%; margin-top: -117.5px; margin-left: -200px; z-index: 2; display: none;">
    <h3>Please select page to print</h3>
    <?php foreach($arr_data['pages'] as $pageIndex => $page): ?>
    <p>
        <input id="page-<?php echo $pageIndex ?>" style="width: 15px;" type="checkbox" name="pages[]" value="<?php echo $pageIndex; ?>" checked>
        <label for="page-<?php echo $pageIndex ?>"><?php echo $page; ?></label>
    </p>
    <?php endforeach; ?>
    <p class="center_text">
        <input type="submit" value="Submit">
    </p>
</form>
<script src="<?php echo URL ?>/js/jquery.modal.min.js" type="text/javascript" charset="utf-8"></script>
<?php endif; ?>
<script type="text/javascript">
$(function() {
    <?php if(isset($arr_data['pages'])): ?>
    $("#print_button").click(function(){
        $('#page-form').modal();
    });
    $('#page-form').submit(function() {
        var data = <?php echo json_encode($arr_data); ?>;
        data.pages = [];
        $('input:checked', this).each(function() {
            data.pages.push($(this).val());
        });
        $.modal.close();
        if (!data.pages.length) {
            alert('Please choose at least 1 page to print.');
            return false;
        }
        print_click();
        $.ajax({
            url:'<?php echo URL; ?>/<?php echo $controller; ?>/print_pdf',
            type:'POST',
            data: data,
            success: function(result){
                if(result!=''){
                    $("#is_completed").val(1);
                    endProcessBar();
                    setTimeout(function() {
                        window.location.href = result;
                    }, 1200);
                }
            }
        });
    });
    <?php else: ?>
    $("#print_button").click(function(){
        var data = <?php echo json_encode($arr_data); ?>;
        print_click();
        $.ajax({
            url:'<?php echo URL; ?>/<?php echo $controller; ?>/print_pdf',
            type:'POST',
            data: data,
            success: function(result){
                if(result!=''){
                    $("#is_completed").val(1);
                    endProcessBar();
                    setTimeout(function() {
                        window.location.href = result;
                    }, 1200);
                }
            }
        });
    });
    <?php endif; ?>
});
function print_click(){
    $(".print_button").addClass("hidden");
    $(".process-wrapper").removeClass("hidden");
    $(".background_process").removeClass("hidden");
    $("#is_completed").val('');
    startProcessBar();
}
function startProcessBar(){
    var progressbar = $('#progressbar'),
        max = 100,
        time = (10000/max)*5,
        value = progressbar.val();

    var loading = function() {
        if($("#is_completed").val()!='')
            clearInterval(animate);
        value += 1;
        addValue = progressbar.val(value);

        $('.progress-value').html(value + '%');

        if (value >= 80) {
            clearInterval(animate);
            return false;
        }
    };

    var animate = setInterval(function() {
        loading();
    }, time);
}
function endProcessBar(){
    var progressbar = $('#progressbar'),
        max = 100,
        time = (100/max)*5,
        value = progressbar.val();

    var loading = function() {
        value += 1;
        addValue = progressbar.val(value);

        $('.progress-value').html(value + '%');

        if (value == 100) {
            $('.progress-value').html('100%');
            clearInterval(animate);
            return false;
        }
    };

    var animate = setInterval(function() {
        loading();
    }, time);
}
</script>
</body>
</html>
