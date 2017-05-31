<?php
App::import('Vendor','tcpdf/tcpdf');
class XTCPDF  extends TCPDF
{

    var $xheadercolor = array(200,200,200);
    var $xfootertext  = "103, 3016 - 10th Ave. NE, Calgary, AB, Canada T2A 6K4    * Tel: 403.291.2244  * Fax: 403.291.2246  * Web: www.anvydigital.com       ";
    var $xfooterfont  = PDF_FONT_NAME_MAIN ;
    var $xfooterfontsize = 8 ;


	/**
    * Overwrites the default header
    * set the text in the view using
    *    $fpdf->xheadertext = 'YOUR ORGANIZATION';
    * set the fill color in the view using
    *    $fpdf->xheadercolor = array(0,0,100); (r, g, b)
    * set the font in the view using
    *    $fpdf->setHeaderFont(array('YourFont','',fontsize));
    */

    public function Header()
    {

    }

    /**
    * Overwrites the default footer
    * set the text in the view using
    * $fpdf->xfootertext = 'Copyright Â© %d YOUR ORGANIZATION. All rights reserved.';
    */
    public function Footer()
    {
        $this->SetTextColor(10, 10, 10);
        $this->SetFont($this->xfooterfont,'',$this->xfooterfontsize);
        $footertext = sprintf($this->xfootertext.'Page '.$this->getAliasNumPage().'/'.$this->getAliasNbPages());
        $this->SetY(-15);
        if(isset($this->isQuote)){
            $style= '<style>
                    td{
                        border-top:1px solid #000;
                    }
                </style>';
            $this->writeHTML($style.'<table cellpadding="3"><tr><td></td><td style="text-align: center;">'.$this->xfootertext.'</td><td style="text-align: right;">Page '.$this->getAliasNumPage().'/'.$this->getAliasNbPages().'</td></tr></table>');
        }
        else
            $this->Cell(0,8, $footertext,'T',1,'C');
    }
}
?>