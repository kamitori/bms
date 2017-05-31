<!----------------- -->
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


$html2 = '
<style>
        .option{
            color: #3d3d3d;
            font-weight:bold;
            font-size:14px;
            text-align: center;
            width:100%;
        }
    </style>
    
    

<table cellpadding="2" cellspacing="0" style="width:100%; margin: 0px auto" class="tab_first">
      <tr>
         <td width="32%" valign="top" style="color:#1f1f1f;">
            <img src="img/logo_anvy.png" alt="" />
            <div style="margin-bottom:5px; margin-top:4px;">
               3145 - 5th Ave NE<br />Calgary  AB  T2A  6A3<br />
            </div>
         </td>
         <td width="23%">&nbsp;</td>
         <td width="45%" style="text-align:right;">
            <div style=" text-align:right; font-size:20px; font-weight:bold; color: #919295; border-bottom: 1px solid #cbcbcb;" class="div_header">
               <span style="color:#b32017;">S</span><span style="border-bottom: 1px solid #cbcbcb;">hipping </span><span style="color:#b32017;">R</span><span style="border-bottom: 1px solid #cbcbcb;">eport </span><span style="color:#b32017;">B</span><span style="border-bottom: 1px solid #cbcbcb;">y </span><span style="color:#b32017;">C</span><span style="border-bottom: 1px solid #cbcbcb;">ustomer</span>
               <div style ="font-size: 12px; font-weight: normal;">( 19-Nov-13 - 20-Nov-13 )</div>
            </div><br/>
            <span><span style="font-weight:bold;">Printed at</span>  8: 12 am, Nov 19, 2013</span>
         </td>
      </tr>
   </table>
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
   font-size:14px;
   text-align: center;
   width:100%;
   }
   table.maintb{
   }
</style>

';

$html2 .='
<div class="option">Heading</div>
</br>
<div class="option" style="text-align:left">Group</div>
</br>
<table cellpadding="3" cellspacing="0" class="maintb">
   <tbody>
      <tr>
         <td width="30%" class="first top">
            Company
         </td>
         <td width="25%" class="top">
            Contact
         </td>
         <td width="20%" class="top">
            Our Rep
         </td>
         <td colspan="2" width="25%" class="top">
            Group total
         </td>        
      </tr>
      <tr style="background-color:#eeeeee;">
         <td class="first content">Anvy Digital</td>
         <td class="content">Minh Hoang</td>
         <td class="content">+84 1645 941 900</td>
         <td colspan="2" class="content">minh.hoang@anvyinc.com</td>
         
      </tr>  
   </tbody>
</table>
<div class="option"></div><br />
<table cellpadding="3" cellspacing="0" class="maintb">
      <tr>
         <td width="10%" class="first top">
            Ref no
         </td>
         <td width="15%" class="top">
            Type
         </td>
         <td width="10%" class="top">
            Date
         </td>
         <td width="10%" class="top">
            Job no
         </td>
         <td width="20%" class="top">
            Job name
         </td>
         <td width="15%" class="top">
            Our Rep
         </td>
         <td colspan="2" width="20%" class="end top">
            Ex. Tax total
         </td>
      </tr>
      <tr style="background-color:#fdfcfa;">
         <td class="first content">1</td>
         <td class="content">Customer</td>
         <td class="content">22-Nov-13</td>
         <td class="content">1</td>
         <td class="content">Design</td>
         <td class="content">Minh Hoang</td>
         <td colspan="2" class="content"  align="right" class="end">30.00</td>
      </tr>
      <tr style="background-color:#eeeeee;">
         <td class="first content">1</td>
         <td class="content">Customer</td>
         <td class="content">22-Nov-13</td>
         <td class="content">1</td>
         <td class="content">Design</td>
         <td class="content">Minh Hoang</td>
         <td colspan="2" class="content"  align="right" class="end">30.00</td>
      </tr>      
      <tr style="background-color:#fdfcfa;">
         <td colspan="4" align="left" class="first bottom">2 records listed</td>
         <td class="bottom">&nbsp;</td>
         <td class="bottom">&nbsp;</td>
         <td align="left" class="bottom"><span style="font-weight:bold; padding-left:20px">Total:</span></td>
         <td align="right" class="content bottom">2,547.58</td>
      </tr>
   </tbody>
</table>
</br>
<div style="border-bottom: 1px dashed #9f9f9f; height:1px; clear:both"></div>
';
$html2 .='
<div class="option">Heading</div>
</br>
<table cellpadding="3" cellspacing="0" class="maintb">
   <tbody>
      <tr>
         <td width="30%" class="first top">
            Company
         </td>
         <td width="25%" class="top">
            Contact
         </td>
         <td width="20%" class="top">
            Our Rep
         </td>
         <td colspan="2" width="25%" class="top">
            Group total
         </td>        
      </tr>
      <tr style="background-color:#eeeeee;">
         <td class="first content">Anvy Digital</td>
         <td class="content">Le Nguyen</td>
         <td class="content">+84 966811009</td>
         <td colspan="2" class="content">abc@abc.com</td>
         
      </tr>  
   </tbody>
</table>
<div class="option"></div><br />
<table cellpadding="3" cellspacing="0" class="maintb">
      <tr>
         <td width="10%" class="first top">
            Ref no
         </td>
         <td width="15%" class="top">
            Type
         </td>
         <td width="10%" class="top">
            Date
         </td>
         <td width="10%" class="top">
            Job no
         </td>
         <td width="20%" class="top">
            Job name
         </td>
         <td width="15%" class="top">
            Our Rep
         </td>
         <td colspan="2" width="20%" class="end top">
            Ex. Tax total
         </td>
      </tr>
      <tr style="background-color:#fdfcfa;">
         <td class="first content">1</td>
         <td class="content">Customer</td>
         <td class="content">22/11/2013</td>
         <td class="content">1</td>
         <td class="content">Design</td>
         <td class="content">Le Nguyen</td>
         <td colspan="2" class="content"  align="right" class="end">30.00</td>
      </tr>
      <tr style="background-color:#eeeeee;">
         <td class="first content">1</td>
         <td class="content">Customer</td>
         <td class="content">22/11/2013</td>
         <td class="content">1</td>
         <td class="content">Design</td>
         <td class="content">Le Nguyen</td>
         <td colspan="2" class="content"  align="right" class="end">30.00</td>
      </tr>      
      <tr style="background-color:#fdfcfa;">
         <td colspan="4" align="left" class="first bottom">2 records listed</td>
         <td class="bottom">&nbsp;</td>
         <td class="bottom">&nbsp;</td>
         <td align="left" class="bottom"><span style="font-weight:bold; padding-left:20px">Total:</span></td>
         <td align="right" class="content bottom">2,547.58</td>
      </tr>
   </tbody>
</table>
</br>
<div style="border-bottom: 1px dashed #9f9f9f; height:1px; clear:both"></div>
';
//die();

$pdf->writeHTML($html2, true, false, true, false, '');



// //PLEASE SIGN AND DATE TO INDICATE YOUR ACCEPTANCE
 $html3 = '';
 $pdf->writeHTML($html3, true, false, true, false, '');

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
echo '<script>window.location.assign("'.URL.'/upload/'.$filename.'.pdf");</script>';
?>