<?php
App::import('Vendor','xtcpdf');
$pdf = new XTCPDF('P','mm','USLETTER',true,'UTF-8',false,false);
// $pdf->xfootertext = "Quotes are valid for 30 days.<br/>All quotes are based on information provided by the customer at the time of quoting. Prices are subject to change when alterations to original quote occur. Prices are based on the customer providing camera-ready electronic files. When additional file management is required, the quote will be revised to reflect the extra charges.";
$pdf->xfooterfontsize = 8;
$pdf->isQuote = true;
$pdf->xfootertext = 'www.anvydigital.com        ';
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
$textfont = 'freesans';
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
				<img src="'.$logo_link.'" alt="" />
				<div style="margin-bottom:5px; margin-top:4px;border-bottom: 1px solid #cbcbcb;">
					'.$company_address.'
				</div>
			</td>
			<td width="48%" style="text-align:right">
			</td>
			<td width="20%" valign="top" align="right">
				<table>
					<tr>
						<td width="25%"></td>
						<td width="75%">
							<div style=" text-align:right; font-size:20px; font-weight:bold; color: #919295; border-bottom: 1px solid #cbcbcb;"><span style="color:#b32017">Q</span>uotation</div>
						</td>
					</tr>
					<tr>
						<td colspan="2">
							<div style="float:right;"><span style="font-weight:bold;">Quotation no:</span> '.$ref_no.' <br /><span style="font-weight:bold;">Date:</span> '.$quote_date.'<br /><span style="font-weight:bold;">Our rep:</span>'.$user_name.'<br /></div>
						</td>
					</tr>
				</table>
			</td>
		</tr>
		<tr>
			<td width="50%">
				<div>'.$customer_address.'</div>
			</td>
			<td>
				<b>Shipping address:</b>
				<div>'.$shipping_address.'</div>
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
			font-size:14px;
			text-align: center;
			width:100%;
		}
		table.maintb
		}
	</style>
	<div class="option">'.$heading.'</div><br />';
if($type=='exclude_qty_price')
	$html2 .= '
		<table cellpadding="3" cellspacing="0" class="maintb">
			<thead>
				<tr>
					<td style="width: 12%;" class="first top">
						&nbsp;SKU
					</td>
					<td style="width: 40%;" class="top">
						Description
					</td>
					<td style="width: 19%; text-align: right;" class="top">
						Width
					</td>
					<td style="width: 19%; text-align: right;" class="top">
						Height
					</td>
					<td style="width: 10%; text-align: right;" class="end top">
						&nbsp;
					</td>
				</tr>
			</thead>
';
else if($type=='category_heading_only')
	$html2 .= '
		<table cellpadding="3" cellspacing="0" class="maintb">
			<tr>
				<td style="width:15%;" class="first top">
					&nbsp;SKU
				</td>
				<td style="width:70%;" class="top">
					Description
				</td>
				<td style="width: 15%; text-align: right;" class="end top">
					Line total
				</td>
			</tr>
';
else
	$html2 .= '
		<table cellpadding="3" cellspacing="0" class="maintb">
			<tr>
				<td style="width: 12%;" class="first top">
					&nbsp;SKU
				</td>
				<td style="width: 28%;" class="top">
					Description
				</td>
				<td style="width: 14%; text-align: right;" class="top">
					Width
				</td>
				<td style="width: 14%; text-align: right;" class="top">
					Height
				</td>
				<td style="width: 12%; text-align: right;" class="top">
					Unit Price
				</td>
				<td style="width: 8%; text-align: right;" class="top">
					Qty
				</td>
				<td style="width: 12%; text-align: right;" class="end top">
					Line total
				</td>
			</tr>';


$html2 .= $html_cont;
//echo $html_cont; die;

$html2 .= '</table>';

$pdf->writeHTML($html2, true, false, true, false, '');



//PLEASE SIGN AND DATE TO INDICATE YOUR ACCEPTANCE
$html3 = '<style>
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
			height:100px;
		}
		.option{
			color: #3d3d3d;
			font-weight:bold;
			font-size:15px;
		}
		table.maintb{

		}
	</style>';
if($other_comment!='')
	$html3 .= "<div style=\"line-height:2px;\"><u><b>Note:</b></u>".$other_comment."</div>";
else
	$html3 .= '<div style="border-bottom: 1px dashed #9f9f9f; height:1px; clear:both"><u><b>Note:</b></u></div><div style="border-bottom: 1px dashed #9f9f9f; height:1px; clear:both"></div><div style="border-bottom: 1px dashed #9f9f9f; height:1px; clear:both"></div>';

$html3 .= '<h3>PLEASE SIGN AND DATE TO INDICATE YOUR ACCEPTANCE</h3>';
$html3 .= '<table cellpadding="2" cellspacing="0" class="maintb" width="100%">
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
			</tr>';

$html3 .= '<tr>
				<td class="bottom first">&nbsp;</td>
				<td class="bottom">&nbsp;</td>
				<td class="bottom">&nbsp;</td>
				<td class="bottom end">&nbsp;</td>
		  </tr>';

$html3 .= '</table>
			<br/>
			<div style="font-size: 10px">
				Quotes are based on infomation provided and valid for 30 days. Subsequent information or insufficient print ready files to additional charges. A minimum charge of $50.00 applies to all orders.
			</div>';
$pdf->writeHTML($html3, true, false, true, false, '');

// reset pointer to the last page
$pdf->lastPage();



// ---------------------------------------------------------
// Close and output PDF document
// This method has several options, check the source code documentation for more information.
//$pdf->Output('example_001.pdf', 'I');


$pdf->Output('upload/'.$filename.'.pdf', 'F');
echo '<script>window.location.assign("'.URL.'/upload/'.$filename.'.pdf");</script>';
?>