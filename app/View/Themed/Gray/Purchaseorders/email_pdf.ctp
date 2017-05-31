<?php

App::import('Vendor', 'xtcpdf');
$pdf = new XTCPDF();
$textfont = 'freesans'; // looks better, finer, and more condensed than 'dejavusans' 
// set document information
$pdf->SetCreator(PDF_CREATOR);
$pdf->SetAuthor('Anvy Digital');
$pdf->SetTitle('Anvy Digital Purchase Orders ');
$pdf->SetSubject('Purchase Orders');
$pdf->SetKeywords('Purchase Orders, PDF');

// set default header data
$pdf->setPrintHeader(false);
$pdf->setPrintFooter(true);

// set default monospaced font
$pdf->SetDefaultMonospacedFont(2);

// set margins
$pdf->SetMargins(10, 3, 10);
$pdf->SetHeaderMargin(PDF_MARGIN_HEADER);
$pdf->SetFooterMargin(PDF_MARGIN_FOOTER);

// set auto page breaks
$pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);

// set image scale factor
$pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);

// set some language-dependent strings (optional)
if (@file_exists(dirname(__FILE__) . '/lang/eng.php')) {
	require_once(dirname(__FILE__) . '/lang/eng.php');
	$pdf->setLanguageArray($l);
}

// ---------------------------------------------------------
// set font
$pdf->SetFont($textfont, '', 9);

// add a page
$pdf->AddPage();

// writeHTML($html, $ln=true, $fill=false, $reseth=false, $cell=false, $align='')
// writeHTMLCell($w, $h, $x, $y, $html='', $border=0, $ln=0, $fill=0, $reseth=true, $align='', $autopadding=true)
// create some HTML content
$html = '
	<table cellpadding="2" cellspacing="0" style="width:100%; margin: 0px auto">
		<tr>
			<td width="32%" valign="top" style="color:#1f1f1f;">
				<img src="' . $logo_link . '" alt="" />
				<div style="margin-bottom:5px; margin-top:4px;border-bottom: 1px solid #cbcbcb;">
					' . $company_address . '
				</div>
			</td>
			<td width="15%"></td>
			<td width="28%">
				&nbsp;
			</td>
			<td width="25%" valign="top" align="right">
				<div style=" text-align:right; font-size:20px; font-weight:bold; color: #919295; border-bottom: 1px solid #cbcbcb;width:30%;"><span style="color:#b32017">P</span>urchase Orders</div>
				<div style="float:right;"><span style="font-weight:bold;">Purchase no:</span> ' . $ref_no . ' <br />
					<span style="font-weight:bold;">Order date:</span> ' . $purchord_date . '<br />
					<span style="font-weight:bold;">Required by:</span> ' . $required_date . '<br />
					<span style="font-weight:bold;">Order by:</span>' . $user_name . '<br />
				</div>
			</td>
		</tr>
		<tr>
			<td>
				<div>' . $customer_address . '</div>
			</td>
			<td>&nbsp;</td>
			<td>
				<b>Shipping address:</b>
				<div>' . $ship_to_contact_name . $shipping_address . '</div>
			</td>
			<td>&nbsp;</td>
		</tr>
	</table>
	<div style="border-bottom: 1px dashed #9f9f9f; height:1px; clear:both"></div><br />
	';

// output the HTML content
$pdf->writeHTML($html, true, false, true, false, '');

//<div class="option">Option 01</div>

$html2 = '
	<style>
		td{
			line-height:2px;
		}
		td.first{
			border-left:1px solid #e5e4e3;
		}
		td.end{
			border-right:1px solid #e5e4e3;
		}
		td.top{
			color:#fff;
			font-weight:bold;
			background-color:#911b12;
			border-top:1px solid #e5e4e3;
		}
		td.bottom{
			border-bottom:1px solid #e5e4e3;
		}
		.option{
			color: #3d3d3d;
			font-weight:bold;
			font-size:13px;
			text-align: center;
			width:100%;
		}
		table.maintb{
			
		}
	</style>
	<div class="option">' . $heading . '</div><br />
	<table cellpadding="3" cellspacing="0" class="maintb">
		<tr>
			<td width="12%" class="first top">&nbsp;SKU</td>
			<td width="30%" class="top">Description</td>
			<td align="right" width="9%" class="top">W</td>
			<td align="right" width="9%" class="top">H</td>
			<td align="right" width="15%" class="top">Unit Price</td>
			<td align="right" width="10%" class="top">Qty</td>
			<td align="right" width="15%" class="end top">Line total</td>
		</tr>';
$html2 .= $html_cont;
$html2 .= '</table>';

$pdf->writeHTML($html2, true, false, true, false, '');



// Note:
$note = '<style>
		.line{
			width: 100%;	
			line-height : 1px;
			border-bottom: 1px dashed #9f9f9f; height:1px; clear:both
		}
	</style>';
$note .= '<h4>Note:</h4>';
$note .= '
		<p class="line">&nbsp;</p>
		<p class="line">&nbsp;</p>
		<p class="line">&nbsp;</p><br>
		';
$pdf->writeHTML($note, true, false, true, false, '');
// Payment:
$payment = '<style>
		.row{
			width: 100%;
		}
	</style>';
$payment .= '
	<style>

		td{
			line-height:2px;
			font-size: 12px;
		}
		td.top2{
			background-color: #EAE9E8;
		    color: #616161;
		    font-weight:bold;
		    font-size: 16px;
		    padding: 20px 0 10px 5px;
		    text-decoration: underline;
		    text-align: left !important;
		}
		.option{
			color: #3d3d3d;
			font-weight:bold;
			font-size:18px;
			text-align: center;
			width:100%;
		}

		.maintb2{
			font-size: 14px;
			line-height: 18px;
			border: 1px solid #EAE9E8;
		}
		.maintb2 tr{
			height: 5px;
		}
		td.text_title{
		}
		td.border_tyle{
			border-bottom: 1px dashed #9F9F9F;
		}
		td.no_line{
			text-decoration: none !important;
		}

	</style>
	<table cellpadding="3" cellspacing="0" class="maintb2" width="100%">
<tr>
	<td width="100%" align="left" class="first top2 no_line">
		<span style="text-decoration: underline">Payment</span>
	</td>
</tr>
<tr>
	<td align="left" width="10%" class="text_title">
		&nbsp; Terms:
	</td>
	<td align="left" width="90%" align="left" class="border_tyle"></td>
</tr>
<tr>
	<td align="left" width="10%" class="first text_title">
		&nbsp; Pay by:
	</td>
	<td align="left" width="90%" align="left" class="first border_tyle"></td>
</tr>
<tr>
	<td align="left" width="10%" class="first text_title">
		&nbsp; C/c #:
	</td>
	<td align="left" width="41%" align="left" class="first border_tyle"></td>
	<td align="left" width="13%" class="first text_title">
		Expiry date:
	</td>
	<td align="left" width="12%" align="left" class="first border_tyle"></td>
	<td align="left" width="16%" class="first text_title">
		Vertification No:
	</td>
	<td align="left" width="8%" align="left" class="first border_tyle"></td>
</tr>
<tr>
	<td align="left" width="23%" class="first text_title">
		&nbsp; Card holders name:
	</td>
	<td align="left" width="77%" align="left" class="first border_tyle"></td>
</tr>
<tr>
	<td align="left" width="23%" class="first text_title">
		&nbsp; Authorized Signature:
	</td>
	<td align="left" width="33%" align="left" class="first border_tyle"></td>
	<td align="left" width="8%" class="first text_title">
		&nbsp; Date:
	</td>
	<td align="left" width="36%" align="left" class="first border_tyle"></td>
</tr>
<tr>
	<td align="left" width="23%" class="first text_title">
		&nbsp;
	</td>
	<td align="left" width="77%" align="left" class="first">&nbsp;</td>
</tr>
</table>';
$pdf->writeHTML($payment, true, false, true, false, '');




// reset pointer to the last page
$pdf->lastPage();



// ---------------------------------------------------------
// Close and output PDF document
// This method has several options, check the source code documentation for more information.
//$pdf->Output('example_001.pdf', 'I');

$pdf->Output($link_this_folder.DS.$filename.'.pdf', 'F');
//echo '<script>window.location.assign("' . URL . '/upload/' . $filename . '.pdf");</script>';
?>