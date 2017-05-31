<?php
if(!$is_html){
	App::import('Vendor','xtcpdf');
	$pdf = new XTCPDF('P','mm','USLETTER',true,'UTF-8',false,false);
	$pdf->xfootertext = 'This is an estimate based on the supplied information. Quotation is valid for 30 days.    ';
	$pdf->xfooterfontsize = 10;
	$pdf->SetCreator(PDF_CREATOR);
	$pdf->SetAuthor('Anvy Digital');
	$pdf->SetTitle('Anvy Digital - Sale Order');
	$pdf->SetSubject('Sale Order');
	$pdf->SetKeywords('Sale Order, PDF');
	$pdf->setPrintHeader(false);
	$pdf->setPrintFooter(true);
	$pdf->SetDefaultMonospacedFont(2);
	$pdf->SetMargins(10, 3, 10);
	$pdf->SetHeaderMargin(PDF_MARGIN_HEADER);
	$pdf->SetFooterMargin(PDF_MARGIN_FOOTER);
	$pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);
	$pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);
	if (@file_exists(dirname(__FILE__).'/lang/eng.php')) {
	    require_once(dirname(__FILE__).'/lang/eng.php');
	    $pdf->setLanguageArray($l);
	}
	$textfont = 'freesans';
	$pdf->SetFont($textfont, '', 9);
	$pdf->AddPage();
}
$html = '<table cellpadding="2" cellspacing="0" style="width:100%; margin: 0px auto">
		<tr>
			<td width="55%" valign="top" style="color:#1f1f1f;">
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
			<td width="20%" valign="top" align="right">
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
	<div style="border-bottom: 1px dashed #9f9f9f; height:1px; clear:both"></div><br />';
if(!$is_html)
	$pdf->writeHTML($html, true, false, true, false, '');
if(!$is_html)
$html2 = '<style>
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
	</style>';
else 
	$html2 = '';
$html2 .= '<div class="option">'.$heading.'</div><br />
	<table cellpadding="3" cellspacing="0" class="maintb">
		<thead>
			<tr>
				<td style="width: 12%;" class="first top">
					&nbsp;SKU
				</td>
				<td style="width: 30%;" class="top">
					Description
				</td>
				<td style="width: 9%; text-align: right;" class="top">
					Width
				</td>
				<td style="width: 9%; text-align: right;" class="top">
					Height
				</td>
				<td style="width: 15%; text-align: right;" class="top">
					Unit Price
				</td>
				<td style="width: 10%; text-align: right;" class="top">
					Qty
				</td>
				<td style="width: 15%; text-align: right;" class="end top">
					Line total
				</td>
			</tr>
		</thead>';
$html2 .= $html_cont;
$html2 .= '</table>';
if(!$is_html)
	$pdf->writeHTML($html2, true, false, true, false, '');
//PLEASE SIGN AND DATE TO INDICATE YOUR ACCEPTANCE
if(!$is_html)
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
		';
else
	$html3 = '';
if($other_comment!='')
	$html3 .= "<div style=\"line-height:2px;\"><u><b>Note:</b></u>".$other_comment."</div>";
else
	$html3 .= '<div style="border-bottom: 1px dashed #9f9f9f; height:1px; clear:both"><u><b>Note:</b></u></div><div style="border-bottom: 1px dashed #9f9f9f; height:1px; clear:both"></div><div style="border-bottom: 1px dashed #9f9f9f; height:1px; clear:both"></div>';
if(!$is_html){
	$pdf->writeHTML($html3, true, false, true, false, '');
	// reset pointer to the last page
	$pdf->lastPage();
	// ---------------------------------------------------------
	// Close and output PDF document
	// This method has several options, check the source code documentation for more information.
	//$pdf->Output('example_001.pdf', 'I');
	$pdf->Output('upload/'.$filename.'.pdf', 'F');
	if(!$get_file)
		echo '<script>window.location.assign("'.URL.'/upload/'.$filename.'.pdf");</script>';
	else
	echo $filename.'.pdf';
} else {
	echo $html.$html2.$html3;
}
?>