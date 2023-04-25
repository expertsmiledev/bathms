<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Certificate extends MY_Controller {

	public function __construct(){
		parent::__construct();
		$this->load->model('Certificate_model', 'cert');
		$this->load->library('Pdf', 'pdf');
		$this->load->helper('date');
	}
	
	public function index(){
		
	}
	
	function inspection($ins_id, $group){
		$ins = $this->cert->get_inspection_data($ins_id);
		$que = $this->cert->get_question_data($ins_id);
		$f = $this->create_certificate($ins, $que, $group);
		$this->cert->save_certificate_link($ins->ast_id, "/".$f);
	}

	/**
	* Creates certificate pdf object
	* @param object $ins - Inspection Data object
	* @return object - Certificate tcpdf object
	*/
	function create_certificate($ins, $que, $group){
        
		$cert = new $this->pdf($group);
		$cert->SetAuthor($ins->username);
		$cert->SetTitle('Certificate of Compliance');
		$c = $this->generate_certificate_page($ins, $cert, $que);
		//Close and output PDF document
		$filename = 'certificates/Certificate_'.$ins->ins_id.'.pdf';
		$c->Output("$filename", 'FI');
		
		return $filename;
	}

	/**
	* Function to generate all latest certificates for a customer
	* Opens pdf in browser & saves file in /certificates/
	* @param int $id - Customer ID to print certs for
	*/
	function generate_bulk_certificates($id){
		ini_set('memory_limit', '2048M');
		$cert_data = $this->cert->get_inspection_data_for_customer($id);
		$cert = new $this->pdf();
		$cert->SetAuthor('BAT Industries');
		$cert->SetTitle('Certificate of Compliance');
		$que = NULL;
		foreach($cert_data as $c){
			if($c->ins_type == "SERVICE"){
				$que = $this->cert->get_question_data($c->ins_id);
			}
			$cert = $this->generate_certificate_page($c, $cert, $que);
		}
		//Close and output PDF document
		$filename = 'certificates/bulk_cert_cus_'.$id.'_'.mdate('%Y-%m-%d').'.pdf';
		$cert->Output("$filename", 'FI');
	}
	
	function generate_certificate_page($ins,$cert,$que){
		$cert->AddPage();
		//Page Heading
		$cert->CreateTextBox('Certificate of Compliance', 0, 67, 160, 10, 24, 'B', 'C', "0,142,166");
		//Data
		//Left Box
		$left_box_data = array(
			"Test Date"=> $this->mysql_date_to_readable($ins->ins_date),
			"Asset Desc" => $ins->prd_name,
			"Asset No" => $ins->ast_id,
			"Manufacture Date" => $this->mysql_date_to_readable($ins->ast_manufacturedate),
			"Grave Date" => $this->mysql_date_to_readable($ins->ast_gravedate),
			"Owner" => $ins->cus_name,
			"City" => $ins->cus_city
			);
		$yval = 84;
		foreach($left_box_data as $name=>$val){
			$cert->CreateTextBox($name.":", 0, $yval, 20, 5, 10);
			$cert->CreateTextBox($val, 29, $yval, 20, 5, 10);
			$yval += 5;
		}
		
		//Right Box
		$right_box_data = array(
			"Location" => $ins->loc_name,
			"Test No" => $ins->ins_id,
			"Tested By" => $ins->username,
			"Test type" => $ins->ins_type,
			"Our Ref" => $ins->ins_jobnumber,
			"Cust PO" => $ins->ins_custpo,
			"Cust Serial No" => $ins->ast_serial
		);
		
		$yval = 84;
		foreach($right_box_data as $name=>$val){
			$cert->CreateTextBox($name.":", 85, $yval, 20, 5, 10);
			$cert->CreateTextBox($val, 110, $yval, 20, 5, 10);
			$yval += 5;
		}
		//Left Table
		//Heading
		$cert->CreateTextBox('Asset Attributes', 0, 125, 88, 6, 12, 'B','L','255,255,255','0,142,166', 'T,L,R');
		
		//Data
		$left_table_data = array(
			'Australian Standard' => $ins->prd_standard,
			'Nominal Bore' => $ins->nmb_name,
			'Overall Length' => $ins->ast_length,
			'Coupling A' => $ins->coupling_a,
			'Coupling A Add-on' => $ins->coupling_addon_a,
			'Attach Method A' => $ins->attach_method_a,
			'Coupling A Material' => $ins->coupling_material_a,
			'Coupling B' => $ins->coupling_b,
			'Coupling B Add-on' => $ins->coupling_addon_b,
			'Attach Method B' => $ins->attach_method_b,
			'Coupling B Material' => $ins->coupling_material_b
		);
		
		$yval = 131;
		$i = 1;
		foreach($left_table_data as $name=>$val){
			if($i < count($left_table_data)){
				$cert->CreateTextBox($name, 0, $yval, 40, 6, 10, '', '', '0,0,0', '', 'T,L');
				$cert->CreateTextBox($val, 40, $yval, 48, 6, 10, '', '', '0,0,0', '', 'T,L,R');
			}else{
				$cert->CreateTextBox($name, 0, $yval, 40, 6, 10, '', '', '0,0,0', '', 'T,L,B');
				$cert->CreateTextBox($val, 40, $yval, 48, 6, 10, '', '', '0,0,0', '', 'T,L,R,B');
			}
			$yval += 6;
			$i++;
		}


		//Right Table
		//Heading
		$cert->CreateTextBox('Test Results', 93, 125, 78, 6, 12, 'B','L','255,255,255','0,142,166', 'T,L,R');
		
		//Data
		$right_table_data = array(
			'OHMS Coupling A' => $ins->ins_ohms_a,
			'OHMS Coupling B' => $ins->ins_ohms_b,
			'OHMS Overall' => $ins->ins_ohms_overall,
			'Test Pressure' => $ins->ins_testpressure . " kPa",
			'Test Time (min)' => $ins->ins_testtime,
			'Working Pressure' => $ins->ins_workingpressure . " kPa",
			'Certification Result' => $ins->ins_certresult
		);
		
		$yval = 131;
		$i = 1;
		foreach($right_table_data as $name=>$val){
			if($i < count($right_table_data)){
				$cert->CreateTextBox($name, 93, $yval, 40, 6, 10, '', '', '0,0,0', '', 'T,L');
				$cert->CreateTextBox($val, 133, $yval, 38, 6, 10, '', '', '0,0,0', '', 'T,L,R');
			}else{
				$cert->CreateTextBox($name, 93, $yval, 40, 6, 10, '', '', '0,0,0', '', 'T,L,B');
				$cert->CreateTextBox($val, 133, $yval, 38, 6, 10, '', '', '0,0,0', '', 'T,L,R,B');
			}
			$yval += 6;
			$i++;
		}
		//Comments
		$cert->CreateTextBox('Comments:', 93, $yval + 1, 78, 4, 10, '', '', '0,0,0', '', '', 'T');
		//$cert->CreateTextBox($ins->ins_comments, 93, $yval + 6, 78, 18, 10, '', '', '0,0,0', '', 'T,L,R,B', 'T');
		$cert->MultiCell(78, 18, $ins->ins_comments, 'T,L,R,B', 'L', '', false, 113, $yval+6, true, 0, false, true, 18, 'TOP', true);
		
		//Questions
		//Heading
		if($ins->ins_type == "SERVICE" && $que != NULL){
			$cert->CreateTextBox('Visual Inspection', 0, 201, 171, 6, 12, 'B','L','255,255,255','0,142,166', 'T,L,R');
			$y = 207;
			foreach($que as $q){
				$cert->CreateTextBox($q->que_question, 0, $y, 133, 6, 10, '', '', '0,0,0', '', 'T,L,B');
				$cert->CreateTextBox($q->inq_answer, 133, $y, 38, 6, 10, '', '', '0,0,0', '', 'T,L,R,B');
				$y += 6;
			}
		}
		
		return $cert; 
	}

}
