<?php
App::import('Vendor','xtcpdf');
$pdf = new XTCPDF('P','mm','USLETTER',true,'UTF-8',false,false);
$textfont = 'freesans'; // looks better, finer, and more condensed than 'dejavusans'

// set document information
$pdf->SetCreator(PDF_CREATOR);
$pdf->SetAuthor('Anvy Digital');
$pdf->SetTitle('Anvy Digital Sale Invoice');
$pdf->SetSubject('Sale Invoice');
$pdf->SetKeywords('Sale Invoice, PDF');

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
			<td width="52%" valign="top" style="color:#1f1f1f;">
				<img src="'.$logo_link.'" alt="" />
				<div style="margin-bottom:1px; margin-top:4px;border-bottom: 1px solid #cbcbcb;">
					'.$company_address.'
				</div>
				<div>'.$customer_address.'</div>
			</td>
			<td width="25%" valign="top">
				<div></div>
				<div></div>
				<div></div>
				<div></div>
				<br />
				<table style="width:100%; margin: 0px auto">
					<tr>
						<td><b>Shipping address:</b></td>
					</tr>
					<tr>
						<td>'.(isset($ship_to)&&$ship_to!='' ? $ship_to:$info_data->contact_name).'</td>
					</tr>
					<tr>
						<td>'.$shipping_address.'</td>
					</tr>
				</table>
			</td>
			<td width="23%" valign="top" align="left">
					<div style=" text-align:right; font-size:20px; font-weight:bold; color: #919295; border-bottom: 1px solid #cbcbcb;width:30%;">'.($credit ? '<span style="color:#b32017">C</span>redit Note' :'<span style="color:#b32017">S</span>ales Invoice').'</div>
					<div style="float:right;">
					<span style="font-weight:bold;">'.($credit ? 'Credit Note' : 'Sales Invoice').':</span> '.$info_data->no.' <br />
					<span style="font-weight:bold;">Job no:</span> '.$info_data->job_no.'<br />
					<span style="font-weight:bold;">Date:</span> '.$info_data->date.'<br />
					<span style="font-weight:bold;">CPO No:</span> '.$info_data->po_no.'<br />
					<span style="font-weight:bold;">A/c no:</span> '.$info_data->ac_no.'<br />
					<span style="font-weight:bold;">Terms:</span> '.$info_data->terms. (is_numeric($info_data->terms) ? 'days' : '').'<br />
					<span style="font-weight:bold;">Due Date:</span> '.$info_data->due_date.'<br />
				</div>
			</td>
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
		table.maintb{

		}
	</style>


	<div class="option">'.$heading.'</div><br />
	<table cellpadding="3" cellspacing="0" class="maintb">
		<thead>
			<tr>
				<td style="width:11%" class="first top">
					&nbsp;SKU
				</td>
				<td style="width: 31%" class="top">
					Description
				</td>
				<td style="text-align: right;width: 10%" class="top">
					Width
				</td>
				<td style="text-align: right;width: 10%" class="top">
					Height
				</td>
				<td style="text-align: right;width: 14%" class="top">
					Unit Price
				</td>
				<td style="text-align: right;width: 8%" class="top">
					Qty
				</td>';
if(IS_LOCAL){
$html2 .=		'<td style="text-align: right;width: 5%" class="top">
				</td>
				<td style="text-align: right;width: 12%" class="end top">
					Line total
				</td>';
} else {
$html2 .=		'<td style="text-align: right;width: 16%" class="end top">
					Line total
				</td>';
}
$html2 .=	'</tr>
		</thead>';
$html2 .= $html_cont;
//echo $html_cont; die;

$html2 .= '</table>';

$pdf->writeHTML($html2, true, false, true, false, '');



//PLEASE SIGN AND DATE TO INDICATE YOUR ACCEPTANCE
$html3 = '<style>

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
	';
$html3 .= '
<h3></h3>
<span style="font-size: 16px;font-weight: bold">Thank you for your business.</span><br />
Please make cheque payable to <span style="font-size: 16px; font-weight: bold">BanhMi SUB Ltd.</span>
<br />
<br />
<span style="font-size: 13px;font-weight: bold">GST / Business Reg.#: '.$tax.'</span>';

$pdf->writeHTML($html3, true, false, true, false, '');

// reset pointer to the last page
$pdf->lastPage();



// ---------------------------------------------------------
// Close and output PDF document
// This method has several options, check the source code documentation for more information.
//$pdf->Output('example_001.pdf', 'I');

$pdf->Output('upload/'.$filename.'.pdf', 'F');
echo '<script>window.location.assign("'.URL.'/upload/'.$filename.'.pdf");</script>';
