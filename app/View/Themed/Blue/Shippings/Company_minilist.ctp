<?php 
App::import('Vendor','xtcpdf');  
$pdf = new XTCPDF(); 
$textfont = 'freesans'; // looks better, finer, and more condensed than 'dejavusans' 

// set document information
$pdf->SetCreator(PDF_CREATOR);
$pdf->SetAuthor('Anvy Digital');
$pdf->SetTitle('Anvy Digital Quotation');
$pdf->SetSubject('Quotation');
$pdf->SetKeywords('Quotation, PDF');

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
if (@file_exists(dirname(__FILE__).'/lang/eng.php')) {
    require_once(dirname(__FILE__).'/lang/eng.php');
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
				<img src="img/logo_anvy.png" alt="" />
				<div style="margin-bottom:5px; margin-top:4px; border-bottom: 1px solid #cbcbcb;">
					3145 - 5th Ave NE<br />Calgary  AB  T2A  6A3<br />
				</div>
				<div style="margin-top:10px;">
					<b>Customer:</b><br />abc
					Photo Studio<br />
					0546 - Barrie Zehrs (Loblaw)<br />
					472 Bayfield St,<br />
					Barrie L4M 5A2Canada<br />
				</div>
			</td>
			<td width="30%">&nbsp;</td>
			<td width="38%" valign="top" align="right">
				<div style=" text-align:right; font-size:20px; font-weight:bold; color: #919295; width:10%;"><span style="color:#b32017;">C</span><span style="border-bottom: 1px solid #cbcbcb;">ompany </span><span style="color:#b32017;">M</span><span style="border-bottom: 1px solid #cbcbcb;">ini </span><span style="color:#b32017;">L</span><span style="border-bottom: 1px solid #cbcbcb;">isting</span></div>
				<div style ="font-size: 12px; font-weight: normal;">( with main company )</div>
				<div style="float:right;"><span style="font-weight:bold;">Quotation no:</span> Q568<br /><span style="font-weight:bold;">Date:</span> 07 Nov, 2013</div>
				<br />
				
				
			</td>
		</tr>
	</table>
	<div style="border-bottom: 1px dashed #9f9f9f; height:1px; clear:both"></div><br />
		
		
		
		
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
		td.top2{
			background-color: #EAE9E8;
		    color: #616161;
		    font-weight:bold;
		    font-size: 16px;
		    padding: 20px 0 10px 5px;
		    text-decoration: underline;
		    text-align: left !important;
		}
		td.bottom{
			border-bottom:1px solid #e5e4e3;
		}
		.option{
			color: #3d3d3d;
			font-weight:bold;
			font-size:18px;
			text-align: center;
			width:100%;
		}
		table.maintb{
			
		}


	</style>
	
	
	<div class="option">Quotation heading</div><br />
	<table cellpadding="3" cellspacing="0" class="maintb">
		<tr>
			<td width="12%" class="first top">
				&nbsp;Code
			</td>
			<td width="30%" class="top">
				Description
			</td>
			<td align="right" width="9%" class="top">
				W
			</td>
			<td align="right" width="9%" class="top">
				H
			</td>
			<td align="right" width="15%" class="top">
				Unit Price
			</td>
			<td align="right" width="10%" class="top">
				Qty
			</td>
			<td align="right" width="15%" class="end top">
				Line total
			</td>
		</tr>
	</table>
	
	
	
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
			background-color:#565656;
			border-top:1px solid #e5e4e3;
		}
		td.bottom{
			border-bottom:1px solid #e5e4e3;
			border-right:1px solid #e5e4e3;
			height:120px;
		}
		.option{
			color: #3d3d3d;
			font-weight:bold;
			font-size:18px;
		}
		table.maintb{
			
		}
	</style>
	<div style="border-bottom: 1px dashed #9f9f9f; height:1px; clear:both"></div><br />
	
	<h3>PLEASE SIGN AND DATE TO INDICATE YOUR ACCEPTANCE</h3>
	<table cellpadding="3" cellspacing="0" class="maintb" width="100%">
			<tr>
				<td align="center" width="30%" class="first top">
					Print name and sign
				</td>
				<td align="center" width="20%" class="top">
					Position
				</td>
				<td align="center" width="19%" class="top">
					Date
				</td>
				<td align="center" width="31%" class="top end">
					Selected Option
				</td>
			</tr>

			<tr>
				<td class="bottom first">&nbsp;</td>
				<td class="bottom">&nbsp;</td>
				<td class="bottom">&nbsp;</td>
				<td class="bottom end">&nbsp;</td>
		  </tr>
		  </table>

	<style>
		.maintb2{
			font-size: 14px;
			line-height: 14px;
			border: 1px solid #9F9F9F;
		}
		.maintb2 tr{
			height: 2px;
		}
		td.text_title{
			border-left-width: 0px;
			border-left-color: #ffffff;
		}
		td.border_tyle{
			border-bottom: 1px dotted #9F9F9F;
			border-left-color: #ffffff;
			border-right-width: 0px;
		}
		td.no_line{
			text-decoration: none;
		}
	</style>
	
	<h3></h3>
	<table cellpadding="3" cellspacing="0" class="maintb2" width="100%">
			<tr>
				<td width="100%" align="left" class="first top2 no_line">
					&nbsp; <span style="text-decoration: underline">Payment</span>
				</td>
			</tr>
			<tr>
				<td align="left" width="10%" class="text_title">
					&nbsp; Terms:
				</td>
				<td align="left" width="90%" align="left" class="border_tyle">312312312</td>
			</tr>
			<tr>
				<td align="left" width="10%" class="first text_title">
					&nbsp; Pay by:
				</td>
				<td align="left" width="90%" align="left" class="first border_tyle">321312</td>
			</tr>
			<tr>
				<td align="left" width="10%" class="first text_title">
					&nbsp; C/c #:
				</td>
				<td align="left" width="41%" align="left" class="first border_tyle">321312</td>
				<td align="left" width="13%" class="first text_title">
					Expiry date:
				</td>
				<td align="left" width="12%" align="left" class="first border_tyle">321312</td>
				<td align="left" width="16%" class="first text_title">
					Vertification No:
				</td>
				<td align="left" width="8%" align="left" class="first border_tyle">1</td>
			</tr>
			<tr>
				<td align="left" width="23%" class="first text_title">
					&nbsp; Card holders name:
				</td>
				<td align="left" width="77%" align="left" class="first border_tyle">321312</td>
			</tr>
			<tr>
				<td align="left" width="23%" class="first text_title">
					&nbsp; Authorized Signature:
				</td>
				<td align="left" width="33%" align="left" class="first border_tyle">321312</td>
				<td align="left" width="8%" class="first text_title">
					&nbsp; Date:
				</td>
				<td align="left" width="36%" align="left" class="first border_tyle">321312</td>
			</tr>
			<tr>
				<td align="left" width="23%" class="first text_title">
					&nbsp;
				</td>
				<td align="left" width="77%" align="left" class="first border_tyle">&nbsp;</td>
			</tr>
		  </table>
	
		  ';







$pdf->writeHTML($html, true, false, true, false, '');
$pdf->lastPage();
// ---------------------------------------------------------
// Close and output PDF document
// This method has several options, check the source code documentation for more information.
//$pdf->Output('example_001.pdf', 'I');

$pdf->Output('upload/test.pdf', 'F'); 
echo '<script>window.location.assign("'.URL.'/upload/test.pdf");</script>';
?>