<?php
App::import('Vendor','tcpdf/tcpdf');
class XTCPDF  extends TCPDF
{

	var $xheadertext  = 'Anvy Digital Quotations';
	var $xheadercolor = array(200,200,200);
	var $xfootertext  = "103, 3016 - 10th Ave. NE, Calgary, AB, Canada T2A 6K4    * Tel: 403.291.2244  * Fax: 403.291.2246  * Web: www.anvydigital.com       ";
	var $xfooterfont  = PDF_FONT_NAME_MAIN ;
	var $xfooterfontsize = 8 ;
	var $today = '';
	var $print='Printed at ';

	var $file1='img/logo_anvy.png';
	var $file1_left=12;
	var $file1_top=10;


	var $file2='img/company_title.png';
	var $file2_left=222;
	var $file2_top=10;

	var $file3='img/bar_975x23.png';
	var $file3_left=11;
	var $file3_top=45;


	var $address_1='Unit 103 , 3016 - 10th Ave NE';
	var $address_2='Calgary AB T2A 6K4';


	//Thanh bar chữ
	var $bar_words_content='Type                   Ref no            Company                                                     Contact                                   Phone                             Mobile              Email';
	var $bar_words_left=13;
	var $bar_words_top=46;
	//----------------------

	//Thanh bar ngăn
	var $bar_mid_content=  '                          |                     |                                                                     |                                 |                                      |                                      |';
	var $bar_mid_left=13;
	var $bar_mid_top=45.6;
	//----------------------


	/**
	 * Overwrites the default header
	 * set the text in the view using
	 *    $fpdf->xheadertext = 'YOUR ORGANIZATION';
	 * set the fill color in the view using
	 *    $fpdf->xheadercolor = array(0,0,100); (r, g, b)
	 * set the font in the view using
	 *    $fpdf->setHeaderFont(array('YourFont','',fontsize));
	 */

	//Thanh ngăn trên
	var $bar_top_content = '-----------------------------------------------------';
	var $bar_top_left=223;
	var $bar_top_top=22;
	//------------------------

	//Chữ mờ dưới logo2
	var $hidden_content='(with main company)';
	var $hidden_left=250;
	var $hidden_top=18;
	//-------------------------


	//Chữ printed at
	var $printedat_left=221;
	var $printedat_top=27;
	//--------------------------


	//Time
	var $time_left=239;
	var $time_top=27;
	//--------------------------


	//font - time + printed at

	var $time_printedat_font=10;
	//--------------------------


	//Bar big
	var $bar_big_content='------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------';
	var $bar_big_left=11;
	var $bar_big_top=40;
	//--------------------------

	function Header()
	{
		list($r, $b, $g) = $this->xheadercolor;
		$this->setY(20); // shouldn't be needed due to page margin, but helas, otherwise it's at the page top
		$this->SetFillColor($r, $b, $g);
		$this->SetTextColor(0 , 0, 0);

//	    $file = 'img/logo_anvy.png';
		$this->Image(APP.DS.'webroot'.DS.$this->file1, $this->file1_left, $this->file1_top, '', '', 'PNG', '', 'T', false, 600, '', false, false, 0, false, false, false);
//
//	    $file2 = 'img/company_title.png';
		$this->Image(APP.DS.'webroot'.DS.$this->file2, $this->file2_left, $this->file2_top, '', '', 'PNG', '', 'R', false, 600, '', false, false, 0, false, false, false);
//
//	    $file3 = 'img/Company_bar.png';
		$this->Image(APP.DS.'webroot'.DS.$this->file3, $this->file3_left , $this->file3_top, '', '', 'PNG', '', 'R', false, 500, '', false, false, 0, false, false, false);

//Địa chỉ
		$this->SetFont($this->xfooterfont,'',$this->time_printedat_font);
		$this->Text(12, 26, $this->address_1 );
		$this->Text(12, 31, $this->address_2 );

		$this->SetFont('helvetica', 'B', 10, '', 'false');
		$this->SetTextColor(255,255,255);
		$this->Text($this->bar_words_left, $this->bar_words_top,$this->bar_words_content);


//Thanh bar ngăn
		$this->SetTextColor(153 , 45, 37);
		$this->Text($this->bar_mid_left, $this->bar_mid_top,$this->bar_mid_content);

// Thanh ngăn trên
		$this->SetTextColor(203,203,203);
		$this->Text($this->bar_top_left, $this->bar_top_top, $this->bar_top_content);


//Chữ mờ dưới logo2
		$this->SetTextColor(180,180,180);
		$this->Text($this->hidden_left, $this->hidden_top, $this->hidden_content);

//Chữ printed at
		$this->SetTextColor(0,0,0);
		$this->SetFont('helvetica', 'B', $this->time_printedat_font, '', 'false');
		$this->Text($this->printedat_left, $this->printedat_top , $this->print );

//Time
		$this->SetFont('helvetica', '', $this->time_printedat_font, '', 'false');
		$this->SetTextColor(0,0,0);
		$this->Text($this->time_left, $this->time_top , $this->today );

//Thanh ngăn lớn
		$this->SetTextColor(203,203,203);
		$this->Text($this->bar_big_left, $this->bar_big_top, $this->bar_big_content );



	}

	/**
	 * Overwrites the default footer
	 * set the text in the view using
	 * $fpdf->xfootertext = 'Copyright Â© %d YOUR ORGANIZATION. All rights reserved.';
	 */
	function Footer()
	{
//	    $this->SetTextColor(203,203,203);
//	    $this->Text(11, 175, '------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------' );

		$footertext = sprintf($this->xfootertext.'Page '.$this->getAliasNumPage().'/'.$this->getAliasNbPages());
		$this->SetY(-15);
		$this->SetTextColor(10, 10, 10);
		$this->SetFont($this->xfooterfont,'',$this->xfooterfontsize);
		$this->Cell(0,8, $footertext,'T',1,'C');
	}
}
?>