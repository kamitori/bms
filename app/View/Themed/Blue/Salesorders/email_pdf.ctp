<?php 
App::import('Vendor','xtcpdf');  
$pdf = new XTCPDF(); 
$textfont = 'freesans'; // looks better, finer, and more condensed than 'dejavusans' 

// set document information
$pdf->SetCreator(PDF_CREATOR);
$pdf->SetAuthor('Anvy Digital');
$pdf->SetTitle('Anvy Digital - Sale Order');
$pdf->SetSubject('Sale Order');
$pdf->SetKeywords('Sale Order, PDF');

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
			<td width="40%" valign="top" style="color:#1f1f1f;">
				<img src="'.$logo_link.'" alt="" />
				<div style="margin-bottom:1px; margin-top:4px;border-bottom: 1px solid #cbcbcb;">
					'.$company_address.'
				</div>
				<div>'.$customer_address.'</div>
			</td>
			<td width="10%">&nbsp;</td>
			<td width="25%" valign="top">
				<div></div>
				<div></div>
				<div></div>
				<div></div>
				<br>
				<b>Shipping address:</b>
				<div>'.$info_data->contact_name.'<br>'.$shipping_address.'</div>
			</td>
			<td width="25%" valign="top" align="right">
					<div style=" text-align:right; font-size:20px; font-weight:bold; color: #919295; border-bottom: 1px solid #cbcbcb;width:30%;"><span style="color:#b32017">S</span>ales Order</div>
					<div style="float:right;">
					<span style="font-weight:bold;">Sales Order:</span> '.$info_data->no.' <br />
					<span style="font-weight:bold;">Job no:</span> '.$info_data->job_no.'<br />
					<span style="font-weight:bold;">Date:</span> '.$info_data->date.'<br />
					<span style="font-weight:bold;">Customer PO no:</span> '.$info_data->po_no.'<br />
					<span style="font-weight:bold;">A/c no:</span> '.$info_data->ac_no.'<br />
					<span style="font-weight:bold;">Terms:</span> '.$info_data->terms.' days<br />
					<span style="font-weight:bold;">Required date:</span> '.$info_data->required_date.'<br />
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
		<tr>
			<td width="12%" class="first top">
				&nbsp;Code
			</td>
			<td width="30%" class="top">
				Description
			</td>
			<td align="right" width="9%" class="top">
				Width
			</td>
			<td align="right" width="9%" class="top">
				Height
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
	<div style="border-bottom: 1px dashed #9f9f9f; height:1px; clear:both">Note:</div>
	<div style="border-bottom: 1px dashed #9f9f9f; height:1px; clear:both"></div>
	<div style="border-bottom: 1px dashed #9f9f9f; height:1px; clear:both"></div>	
	';
$html3 .= '<h3>PLEASE SIGN AND DATE TO INDICATE YOUR ACCEPTANCE</h3>';
$html3 .= '<table cellpadding="3" cellspacing="0" class="maintb" width="100%">
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
					Your comment
				</td>
			</tr>';

$html3 .= '<tr>
				<td class="bottom first">&nbsp;</td>
				<td class="bottom">&nbsp;</td>
				<td class="bottom">&nbsp;</td>
				<td class="bottom end">&nbsp;</td>
		  </tr>';

$html3 .= '</table>';
$pdf->writeHTML($html3, true, false, true, false, '');

// reset pointer to the last page
$pdf->lastPage();



// ---------------------------------------------------------
// Close and output PDF document
// This method has several options, check the source code documentation for more information.
//$pdf->Output('example_001.pdf', 'I');

$pdf->Output($link_this_folder.DS.$filename.'.pdf', 'F');
//echo '<script>window.location.assign("'.URL.'/upload/'.$filename.'.pdf");</script>';
?>