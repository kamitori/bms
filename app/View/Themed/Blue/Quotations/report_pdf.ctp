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
   <tbody>
      <tr>
         <td width="32%" valign="top" style="color:#1f1f1f;">
            <img src="img/logo_anvy.png" alt="" margin-bottom:0px>
            <p style="margin-bottom:5px; margin-top:0px;">3145 - 5th Ave NE<br/ >Calgary  AB  T2A  6A3</p>
         </td>
         <td width="68%" valign="top" align="right">
            <table>
               <tbody>
                  <tr>
                     <td width="25%">&nbsp;</td>
                     <td width="75%">
                        <span style="text-align:right; font-size:21px; font-weight:bold; color: #919295;">
                            '.$title.'<br />';
if(isset($date_equals))
  $date = '<span style="font-size:12px; font-weight:normal">'.$date_equals.'</span>';
else
{
    if(isset($date_from)&&isset($date_to))
      $date = '<span style="font-size:12px; font-weight:normal">( '.$date_from.' - '.$date_to.' )</span>'; 
    else if(isset($date_from))
      $date = '<span style="font-size:12px; font-weight:normal">From '.$date_from.'</span>';
    else if(isset($date_to))
      $date = '<span style="font-size:12px; font-weight:normal">To '.$date_to.'</span>';  
    else
      $date = '';
}                
$html .= $date;           
$html .=                    '
                        </span>
                        <div style=" border-bottom: 1px solid #cbcbcb;height:5px">&nbsp;</div>
                     </td>
                  </tr>
                  <tr>
                     <td colspan="2">
                            <span style="font-weight:bold;">Printed at: </span>'.$current_time.'
                     </td>
                  </tr>
               </tbody>
            </table>
         </td>
      </tr>
   </tbody>
</table>
<div class="option">'.@$heading.'</div>
<div style="border-bottom: 1px dashed #9f9f9f; height:1px; clear:both"></div>
<br />
<style>
   td{
   line-height:2px;
   }
   td.first{
    text-align: center;
   border-left:1px solid #e5e4e3;
   }
   td.end{
   border-right:1px solid #e5e4e3;
   }
   td.top{
   color:#fff;
   text-align: center;
   font-weight:bold;
   background-color:#911b12;
   border-top:1px solid #e5e4e3;
   }
   td.bottom{
   border-bottom:1px solid #e5e4e3;
   }
   td.content{    
    border-right: 1px solid #E5E4E3;
    text-align: center;
   }
   .option{
   color: #3d3d3d;
   font-weight:bold;
   font-size:20px;
   text-align: center;
   width:100%;
   }
   table.maintb{
   }
</style>
<br />
';
$html .= $html_loop;

$pdf->writeHTML($html, true, false, true, false, '');




// reset pointer to the last page
$pdf->lastPage();



// ---------------------------------------------------------
// Close and output PDF document
// This method has several options, check the source code documentation for more information.
//$pdf->Output('example_001.pdf', 'I');


// write some JavaScript code
$js = <<<EOD
var cResponse = app.response({
    cQuestion: 'Are you print now?',
    cTitle: 'Print action',
    cDefault: 'Yes',
    cLabel: 'Response:'
});
if (cResponse == null) {
    app.alert('Thanks for trying anyway.', 3, 0, 'Result');
} else {
    print(true);
}
EOD;

// set javascript
$pdf->IncludeJS($js);



$pdf->Output('upload/'.$filename.'.pdf', 'F'); 
?>
