<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<link href="<?php echo URL; ?>/favicon.ico" type="image/x-icon" rel="icon">
<link href="<?php echo URL; ?>/favicon.ico" type="image/x-icon" rel="shortcut icon">
<title>JobTraq</title>
<style type="text/css" media="print,screen">
#wrap{
    width:100%;
    margin: auto;
    float:left;
    font-size: 9.5pt;
    /*font-family: Arial, Helvetica, sans-serif;*/
    font-family: Ubuntu, sans-serif;
}

#wrap p {
    line-height: 4px;
}

#content{
    width: 100%;
	margin: 0px auto;
}

#header{
    width: 100%;
    border-bottom: 1pt dashed #ccc;
}

#header > tbody > tr > td:nth-child(1) {
    width: 65%;
    vertical-align: top;
}

#header > tbody > tr > td:nth-child(2) {
    text-align: right;
    vertical-align: top;
}

#header #right_info {
    border-top:1pt solid #ccc; padding-top: 15px; text-align: left;float: right;
}
#address{
    padding-bottom: 10px;
    border-top: 1pt solid #ccc;
    width: 100%;
}
#address td{
    vertical-align: top;
    width: 49%;
}
#address b {
    line-height: 18px;
}
#bottom_header {
    display: inline-block;
    width: 100%;
}
#billing_address{
    padding-bottom: 10px;
    display: inline-block;
    float:left;
    width: 50%;
}
#shipping_address{
    display: inline-block;
    float:left;
}
#title_pdf{
    font-size: 200%;
    font-weight: bolder;
    text-transform: capitalize;
    color:#919295;
}
#title_pdf span{
    color:#b32017;
}
#title_pdf #border {
    border-bottom:1pt solid #ccc;
    width: 70%;
	margin-left: 30%;
}

.row{
    width:100%;
    margin-bottom: 10px;
}

#pdf_content{
    width: 100%;
}
#pdf_content #heading{
    padding: 15px 0;
    text-align: center;
    font-weight: bold;
    text-transform: capitalize;
    font-size: 10pt;
}

#product_list{
    width: 100%;
    border: none;
    border-spacing: 0;
}

#product_list tr th{
    text-align: left;
    background: #921;
    color: #fff;
    padding: 5px;
    font-size: 9.5pt;
}
#product_list tr td{
    font-size: 9.5pt;
    padding: 8px;
}
#product_list tr td p{
    margin-bottom: 10px;
}
#product_list tr:nth-child(even) td{
    background: #eeeeee;

}
#product_list tr:nth-child(odd) td{
    background: #fdfcfa;
}

#product_list tr.sum_title{
    text-align: right !important;
    font-size: 10.5pt;
    font-weight: 600;
}

#product_list tr.sum_title td:nth-child(2){
    font-weight: normal !important;
}

#product_list tr td.option_product{
    padding-left: 15px;
}
span.bullet::before{
    content:"\2022";
    padding-right: 5px;
    font-weight: bold;
}

#right_info {
	float: right;
}

#right_info td:first-child {
	font-weight: bold !important;
}

#right_info td:last-child {
	padding-left: 20px !important;
}
.right_text {
	text-align: right;
}
.center_text {
	text-align: center;
}
.bold_text {
	font-weight: bold;
}
#note{
    margin-top: 20px;
    page-break-inside:avoid;
    page-break-after:auto;
}
#note strong {
    text-decoration: underline;
}
#note div {
    border-bottom: 1pt dashed #9f9f9f;
    margin-bottom: 30px;
}
#quotation-note {
    page-break-inside:avoid;
    page-break-after:auto;
    page-break-before:auto;
}
#product_list tfoot td {
    border: none !important;
    background: none !important;
}
#product_list .note td {
    border: none !important;
    background: none !important;
}
.quotation tr:first-child td {
    color:#fff;
    font-weight:bold;
    background-color:#565656;
    height: 25px;
}
.quotation tr:last-child td {
    border:1pt solid #e5e4e3;
    height: 150px;
}
table#product_list {
   page-break-inside:always;
 }
#product_list tr{
  page-break-inside:avoid;
  page-break-after:auto;
 }
@media print {
    thead {display: table-header-group;}
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
.cancelled {
    font-size: 26pt;
    width: 100px;
    height: 100px;
    top: 10px;
    left: 50%;
    margin-left: -50px;
    color: red;
    position: absolute;
    -webkit-transform: rotate(-20deg) skew(-20deg, 0);
       -moz-transform: rotate(-20deg) skew(-20deg, 0);
        -ms-transform: rotate(-20deg) skew(-20deg, 0);
         -o-transform: rotate(-20deg) skew(-20deg, 0);
            transform: rotate(-20deg) skew(-20deg, 0);
}
.paid {
    font-family: times;
    font-size: 30pt;
    font-weight: bold;
    word-spacing: 2pt;
    width: 100px;
    height: 100px;
    top: 10px;
    left: 50%;
    margin-left: -50px;
    color: blue;
    position: absolute;
    -webkit-transform: rotate(-18deg) skew(-18deg, 0);
       -moz-transform: rotate(-18deg) skew(-18deg, 0);
        -ms-transform: rotate(-18deg) skew(-18deg, 0);
         -o-transform: rotate(-18deg) skew(-18deg, 0);
            transform: rotate(-18deg) skew(-18deg, 0);
}
</style>
</head>
<body>
    <div class="div_details" id="wrap" style="margin-left: 4px;">
        <div id="content">
            <table id="header">
                <tr>
                    <td>
                        <?php if(isset($arr_data['logo'])): ?>
                        <img src="<?php echo $arr_data['logo']; ?>" id="logo">
                        <?php else: ?>
                        <img src="/img/logo.svg"  style="margin: -50px -18px; height: 150px; width: 275px;">
                        <?php endif; ?>
                    </td>
                    <td>
                        <?php if(isset($arr_data['pdf_name'])): ?>
                            <span id="title_pdf">
                            <?php
                                $name = trim($arr_data['pdf_name']);
                                $first_letter = substr($name, 0, 1);
                                $name = '<span>'.$first_letter.'</span>'.substr($name, 1);
                                echo $name;
                            ?>
                            </span>
                            <?php if(!isset($arr_data['right_info'])){ ?>
                            <div id="border"></div>
                            <?php } ?>
                        <?php endif; ?>

                    </td>
                </tr>
                <tr>
                    <td>
                        <?php if( !isset($arr_data['company_address']) ): ?>
                        <p>Unit 3145 - 5th Ave NE</p>
                        <p>Calgary AB T2A 6K4</p>
                        <!-- <p>Tel: 403.291.2244</p>
                        <p>Fax: 403.291.2246</p> -->
                        <?php else: ?>
                        <?php echo $arr_data['company_address']; ?>
                        <?php endif; ?>
                        <div style="float:right;position:relative;margin-top:-20px;">
                            Telephone:<b>403-454-8644</b>
                        </div>
                    </td>
                    <td rowspan="2">
                        <?php if(isset($arr_data['right_info'])): ?>
                        <table id="right_info">
                            <?php foreach($arr_data['right_info'] as $name => $value): ?>
                            <tr>
                                <td><?php echo $name ?>:</td>
                                <td><?php echo $value; ?></td>
                            </tr>
                            <?php endforeach; ?>
                        </table>
                        <?php endif; ?>
                    </td>
                </tr>
                <tr>
                    <td>
                        <table id="address">
                            <tr>
                                <td>
                                    <?php if(isset($arr_data['customer_address'])):  ?>
                                    <?php echo $arr_data['customer_address']; ?>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <?php if(isset($arr_data['shipping_address'])):  ?>
                                        <p><b>Shipping address:</b></p>
                                        <?php echo $arr_data['shipping_address']; ?>
                                    <?php endif; ?>
                                </td>

                            </tr>
                        </table>
                    </td>
                </tr>
            </table>
            <?php if(isset($arr_data['is_paid'])){ ?>
                <div class="paid">PAID</div>
                <?php } ?>
            <div id="pdf_content">
                <div class="row" id="heading">
                    <?php if(isset($arr_data['heading'])): ?>
                    <?php echo $arr_data['heading'];  ?>
                    <?php endif; ?>
                </div>
                <table id="product_list">
                    <thead>
                        <tr>
                            <?php
                                $count = 1;
                                if(isset($arr_data['title'])){
                                    $count = count($arr_data['title']);
                                    $title = '';
                                    foreach($arr_data['title'] as $key=>$value){
                                        if(is_numeric($key))
                                            $title .= '<th>'.$value.'</th>';
                                        else
                                            $title .= '<th style="'.$value.'">'.$key.'</th>';
                                    }
                                    echo $title;
                                }
                            ?>
                        </tr>
                    </thead>
                    <tbody>
	                    <?php if(isset($arr_data['content'])) echo $arr_data['content']; ?>
                        <?php if (isset($arr_data['custom_footer'])): ?>
                        <?php echo $arr_data['custom_footer']; ?>
                        <?php else: ?>
                        <tr class="sum_title">
                            <td colspan="<?php echo $count-1; ?>" style="border-top: 1pt solid #ABABAB;">
                                Sub Total:
                            </td>
                            <td style="border-top: 1pt solid #ABABAB;">
                            	<?php if(isset($arr_data['sum_sub_total'])) echo $arr_data['sum_sub_total']; ?>
                            </td>
                        </tr>
                        <tr class="sum_title">
                            <td colspan="<?php echo $count-1; ?>">
                                HST/GST:
                            </td>
                            <td>
                            	<?php if(isset($arr_data['sum_tax'])) echo $arr_data['sum_tax']; ?>
                            </td>
                        </tr>
                        <tr  class="sum_title">
                            <td colspan="<?php echo $count-1; ?>">
                                Total:
                            </td>
                            <td>
                            	<?php if(isset($arr_data['sum_amount'])) echo $arr_data['sum_amount']; ?>
                            </td>
                        </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
                <?php if(isset($arr_data['is_cancelled'])){ ?>
                <div class="cancelled">CANCELLED</div>
                <?php } ?>
                <?php if(!isset($arr_data['no_note'])): ?>
                <div class="row" id="note">
                    <strong>Note:</strong>
                    <?php
                        $note = '';
                        if(isset($arr_data['note'])) $note = $arr_data['note'];
                        $arrNote =  preg_split('/<br[^>]*>/i', $note);
                        $countNote = count($arrNote);
                        if( $countNote ) {
                            $note = '';
                            foreach($arrNote as $string) {
                                $note .= '<div>'.$string.'</div>';
                            }
                        }
                    ?>
                    <?php if(empty($note)): ?>
                    <div></div>
                    <div></div>
                    <div></div>
                    <?php else: ?>
                    <?php echo $note; ?>
                    <?php if( $countNote < 3): ?>
                    <?php for($i = $countNote; $i < 3; $i ++):  ?>
                    <div>&nbsp;</div>
                    <?php endfor; ?>
                    <?php endif; ?>
                    <?php endif; ?>
                </div>
                <?php endif; ?>
                <?php if($controller == 'quotations'): ?>
                <div id="quotation-note">
                    <h3>PLEASE SIGN AND DATE TO INDICATE YOUR ACCEPTANCE</h3>
                    <table cellpadding="2" cellspacing="0" class="quotation" width="100%">
                        <tr>
                            <td style="width: 30%; text-align: center;" class="first top">
                                Print name and sign
                            </td>
                            <td style="width: 20%; text-align: center;" class="top">
                                Position
                            </td>
                            <td style="width: 19%; text-align: center;" class="top">
                                Date
                            </td>
                            <td style="width: 31%; text-align: center;" class="top end">
                                Your comment
                            </td>
                        </tr>
                        <tr>
                            <td>&nbsp;</td>
                            <td>&nbsp;</td>
                            <td>&nbsp;</td>
                            <td>&nbsp;</td>
                        </tr>
                    </table>
                </div>
                <?php endif; ?>
                <?php if(isset($arr_data['extra_note'])): ?>
                <p></p>
                <div style="font-size: 11pt; marigin-top: 5px;">
                    <?php echo $arr_data['extra_note']; ?>
                </div>
                <?php endif; ?>
                <!-- <div class="footer" >
                <?php echo $arr_data['footer']; ?>
                </div> -->
            </div>
        </div>
    </div>
    <?php echo $this->Html->script('jquery-1.10.2.min'); ?>
    <script type="text/javascript">
        var height = Math.round($("#product_list").height());
        var width = Math.round($("#product_list").width());
        var degree = Math.round((height * 57.29) / width);
        var t = $("#product_list").offset().top + (height / 2) - ($(".cancelled").height() / 2) + 15;
        $(".cancelled").css({
            "top" : t + "px",
            /*"-webkit-transform": "rotate(-"+degree+"deg) skew(-"+degree+"deg, 0)",
            "-moz-transform": "rotate(-"+degree+"deg) skew(-"+degree+"deg, 0)",
            "-ms-transform": "rotate(-"+degree+"deg) skew(-"+degree+"deg, 0)",
            "-o-transform": "rotate(-"+degree+"deg) skew(-"+degree+"deg, 0)",
            "transform": "rotate(-"+degree+"deg) skew(-"+degree+"deg, 0)",*/
        });
    </script>
</body>
</html>