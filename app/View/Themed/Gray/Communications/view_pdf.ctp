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
$pdf->setPrintFooter(false);

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
					3145 - 5th Ave NE<br />
					Calgary  AB  T2A  6A3<br />
				</div>
				<div style="margin-top:10px;">
					Mr Steve Bicknell<br />
					Mosaic Studios<br />
					Suite 350, 323 - 10th Ave SW<br />
					Calgary AB T2R 0A5<br />
				</div>
			</td>
			<td width="50%">&nbsp;</td>
			<td width="18%" valign="top" align="right">
				<div style=" text-align:right; font-size:20px; color: #919295; border-bottom: 1px solid #cbcbcb;width:30%;"><span style="color:#b32017">Q</span>uotation</div>
				<div>
					<span style="font-weight:bold">Quotation no:</span> 8662<br />
					<span style="font-weight:bold">Date:</span> 23 - July - 2013
				</div>
			</td>
		</tr>
	</table>
	<div style="border-bottom: 1px dashed #9f9f9f; height:1px; clear:both"></div><br />
		';

// output the HTML content
$pdf->writeHTML($html, true, false, true, false, '');


$html2 = '
	<style>
		td{
			line-height:2px;
		}
	</style>
	<table cellpadding="3" cellspacing="0">
		<tr style=" color:#fff;font-weight:bold;background-color:#911b12;">
			<td width="12%">
				Code
			</td>
			<td width="30%">
				Description
			</td>
			<td align="right" width="9%">
				W(in.)
			</td>
			<td align="right" width="9%">
				H(in.)
			</td>
			<td align="right" width="15%">
				Unit Price
			</td>
			<td align="right" width="10%">
				Qty
			</td>
			<td align="right" width="15%">
				Line total
			</td>
		</tr>';
for($m=0;$m<3;$m++){
$html2 .= '
		<tr style=" color:#444;font-weight:bold;background-color:#eeeeee;">
			<td>
				0-A2-AV-IT
			</td>
			<td>
				Image-Tex, Front entrance wall
			</td>
			<td align="right">
				141.5
			</td>
			<td align="right">
				122
			</td>
			<td align="right">
				767.25
			</td>
			<td align="right">
				1
			</td>
			<td align="right">
				767.24
			</td>
		</tr>
		<tr style=" color:#444;font-weight:bold;background-color:#fdfcfa;">
			<td>
				0-A2-AV-IT
			</td>
			<td>
				Image-Tex, Front entrance wall
			</td>
			<td align="right">
				141.5
			</td>
			<td align="right">
				122
			</td>
			<td align="right">
				767.25
			</td>
			<td align="right">
				1
			</td>
			<td align="right">
				767.24
			</td>
		</tr>';
}

		
$html2 .= '</table>';

$pdf->writeHTML($html2, true, false, true, false, '');

$pdf->writeHTML($html2, true, false, true, false, '');

// reset pointer to the last page
$pdf->lastPage();



// ---------------------------------------------------------
// Close and output PDF document
// This method has several options, check the source code documentation for more information.
//$pdf->Output('example_001.pdf', 'I');

$pdf->Output('upload/filename.pdf', 'F'); 

?>