<?php 
if ( ! defined('BASEPATH')) exit('No direct script access allowed');
require_once APPPATH.'/libraries/tcpdf/tcpdf.php';

class Pdf extends TCPDF{
    
    
    public function __construct($group='')
    {
        parent::__construct();
        if($group == 'BAT')
        	$this->setHeaderData('./images/certificate/BAT-Certificate-Header.png');
       	else
       		$this->setHeaderData('./images/certificate/MAXIMUS-Certificate-Header.png');
    }
 
	public function Header() {
		$this->setJPEGQuality(90);
		$logo_link = $this->getHeaderData()['logo'];
		$this->Image($logo_link, 20, 20, 170, 0, 'PNG');
	}

	public function Footer() {
		$this->setJPEGQuality(90);
		$this->Image('./images/certificate/BAT-Certificate-Footer.png', 20, 250, 170, 0, 'PNG');
	}
	/**
	* Creates a Text Box using the TCPDF 'Cell' method
	* @param string $textval - Text to display in the cell
	* @param int $x - x position in mm
	* @param int $y - y position in mm
	* @param int $width - cell width in mm
	* @param int $height - cell height in mm
	* @param int $fontsize - Font size in points
	* @param string $fontstyle - B,I,U,D,O - Bold,Italic,Underline,Line Through,Overline
	* @param string $align - L,C,R,J
	* @param string $color - Sets font color in R,G,B - 0-255 Comma Separated No Spaces
	* @param string $fill - Enables & sets background color in R,G,B - 0-255 Comma Separated No Spaces
	* @param int or string $border - draws border around cell 0 - No Border (default), 1 - Frame, T,R,B,L
	*/
	public function CreateTextBox($textval, $x = 0, $y, $width = 0, $height = 10, $fontsize = 10, $fontstyle = '', $align = 'L', $color='0,0,0', $fill='', $border=0, $valign='C') {
		$this->SetXY($x+20, $y); // 20 = margin left
		$col = explode(',', $color);
		$this->SetTextColor($col[0],$col[1],$col[2]);
		$f = false;
		if($fill != ''){
			$f = true;
			$fc = explode(',',$fill);
			$this->SetFillColor($fc[0],$fc[1],$fc[2]);
		}
		$this->SetFont('Helvetica', $fontstyle, $fontsize);
		$this->Cell($width, $height, $textval, $border, false, $align, $f, '', '', '', '', $valign);
	}
	public function CreateHTMLTextBox($htmlval, $x = 0, $y, $width = 0, $height = 10, $fontsize = 10, $fontstyle = '', $align = '') {
		$this->SetXY($x+20, $y); // 20 = margin left
		$this->SetFont('Helvetica', $fontstyle, $fontsize);
		$this->writeHTMLCell($width, $height, $x+20, $y, $htmlval, 0, 1, false, true, $align);
		return $this->getY();
	}
}
?>