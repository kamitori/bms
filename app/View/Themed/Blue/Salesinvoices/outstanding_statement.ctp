<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<link href="<?php echo URL; ?>/favicon.ico" type="image/x-icon" rel="icon">
<link href="<?php echo URL; ?>/favicon.ico" type="image/x-icon" rel="shortcut icon">
<title>Customer Open Balance - All Transactions</title>
<?php
    echo $this->Html->css('report');
?>
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
.hidden{
    display: none;
}
.left-text{
    text-align: left !important;
}
.right-text{
    text-align: right;
}
.center-text{
    text-align: center;
}
.bold-text{
    font-weight: 900;
}
.border-bottom{
    border-bottom: solid 2px;
}
table{
    width: 96%;
    margin: 30px;
}
.main-table > thead > tr > td{
    text-align: center;
    font-weight: 900;
}
.main-table > thead > tr > td > div{
    width: 90%;
    border-bottom: 4px solid;

}
.main-table > tbody > tr > td{
    text-align: center;

}
.main-table > tbody > tr:last-child > td{
    border-top: 1px solid;
    border-bottom: 1px solid;

}
.main-table > tbody > tr:last-child > td > div{
    clear: both;
    width: 100%;
    border-bottom: 1px solid;
    margin-bottom: 2px;

}
.main-table  tfoot td{
    font-weight: 900;
    font-size: 14px;
}
.print_button{
    top : 80px !important;
}
.sign{
    float: left;
}
.amount{
    float: right;
    margin-right: 15px;
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
</style>
</head>
<body>
    <input id="is_completed" type="hidden" value="" />
    <div class="background_process hidden">&nbsp;</div>
    <div class="process-wrapper html5-progress-bar hidden">
        <div class="progress-bar-wrapper">
            <progress id="progressbar" value="0" max="100"></progress>
            <span class="progress-value">0%</span>
        </div>
    </div>
    <a href="javascript:void(0)" id="print_button" class="print_button">Export PDF</a>
    <table class="center-text">
        <tr>
            <td class="left-text" style="width: 15%;"><?php echo date('h:i a') ?><br /><?php echo date('m/d/Y') ?></td>
            <td class="center-text bold-text" style="font-size: 16px;">BanhMi SUB Ltd.<br /><span style="font-size: 20px; line-height:  30px;">Customer Open Balance</span><br />All Transactions</td>
            <td style="width: 15%;">&nbsp;</td>
        </tr>
        <tr>
            <td></td>
        </tr>
    </table>

    <table class="main-table" cellspacing="15">
        <thead>
            <tr>
                <td>Type<div></div></td>
                <td>Date<div></div></td>
                <td>Inv #<div></div></td>
                <td>PO #<div></div></td>
                <td>Current<div></div></td>
                <td>30+ days<div></div></td>
                <td>60+ days<div></div></td>
                <td>90+ days<div></div></td>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td colspan="8" class="bold-text left-text" style="line-height: 50px"><?php echo $arr_data['company_name'] ?></td>
            </tr>
            <?php
                $total_0 = 0;
                $total_31 = 0;
                $total_61 = 0;
                $total_91 = 0;
            ?>
            <?php foreach($arr_data['invoices'] as $invoice){ ?>
            <?php
                $tmp = 'total_'.$invoice['debt'];
                $$tmp += $invoice['balance'];
            ?>
            <tr>
                <td><?php echo $invoice['type']; ?></td>
                <td><?php echo $invoice['date']; ?></td>
                <td><?php echo $invoice['code']; ?></td>
                <td><?php echo $invoice['customer_po_no']; ?></td>
                <td><?php echo $invoice['debt'] == 0 ? '<span class="sign">$</span><span class="amount">'.$this->Common->format_currency($invoice['balance']).'</span>' : ''; ?></td>
                <td><?php echo $invoice['debt'] == 31 ? '<span class="sign">$</span><span class="amount">'.$this->Common->format_currency($invoice['balance']).'</span>' : ''; ?></td>
                <td><?php echo $invoice['debt'] == 61 ? '<span class="sign">$</span><span class="amount">'.$this->Common->format_currency($invoice['balance']).'</span>' : ''; ?></td>
                <td><?php echo $invoice['debt'] == 91 ? '<span class="sign">$</span><span class="amount">'.$this->Common->format_currency($invoice['balance']).'</span>' : ''; ?></td>
            </tr>
            <?php } ?>
            <tr>
                <td colspan="4"><div></div></td>
                <td><?php echo $total_0!=0 ?  '<span class="sign">$</span><span class="amount">'.$this->Common->format_currency($total_0).'</span>' : ''; ?><div></div></td>
                <td><?php echo $total_31!=0 ?  '<span class="sign">$</span><span class="amount">'.$this->Common->format_currency($total_31).'</span>' : ''; ?><div></div></td>
                <td><?php echo $total_61!=0 ?  '<span class="sign">$</span><span class="amount">'.$this->Common->format_currency($total_61).'</span>' : ''; ?><div></div></td>
                <td><?php echo $total_91!=0 ?  '<span class="sign">$</span><span class="amount">'.$this->Common->format_currency($total_91).'</span>' : ''; ?><div></div></td>
            </tr>
        </tbody>
        <tfoot>
            <tr>
                <td colspan="8">&nbsp;</td>
            </tr>
            <tr>
                <td>Total:</td>
                <td class="center-text">$</td>
                <td class="center-text"><?php echo $this->Common->format_currency($total_0 + $total_31 + $total_61 + $total_91); ?></td>
                <td colspan="5"></td>
            </tr>
            <tr>
                <td colspan="8">Payment Terms: Net <?php echo $arr_data['payment_terms'] ?> day<?php echo $arr_data['payment_terms']>1 ? 's' : ''  ?></td>
            </tr>
        </tfoot>
    </table>
<?php
    $arr_data['report_url'] = "http://".$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'];
?>
<?php
    echo $this->Html->script('jquery-1.10.2.min');
?>
<script type="text/javascript">
$(function() {
    $("#print_button").click(function(){
        print_click();
        $.ajax({
            url:'<?php echo URL; ?>/<?php echo $controller; ?>/print_pdf',
            type:'POST',
            data: <?php echo json_encode($arr_data); ?>,
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