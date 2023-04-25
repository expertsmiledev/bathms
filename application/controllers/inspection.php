<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Inspection extends MY_Controller {

	public function __construct(){
		parent::__construct();
		$this->load->model('Inspection_model', 'insp');
	}
	
	public function index(){
	}
	
	function new_asset($id){
		$ins_id = $this->insp->generate_inspection($id, 'NEW');
		$this->view($ins_id, 'NEW');
	}
	
	function service($id){
		$ins_id = $this->insp->generate_inspection($id, 'SERVICE');
		$this->view($ins_id, 'SERVICE');
	}
	
	function view($id, $type='NEW'){
		$ins = $this->insp->get_inspection($id);
		
		$data = new stdClass;
		$data->js_files = array('/js/inspection.js', '/js/jquery-ui-1.10.3.custom.min.js');
		$data->css_files = array('/css/jquery-ui-1.10.3.custom.min.css');
		$data->nav = $this->build_nav();
		$data->customer = $this->insp->get_customer($id);
		$data->asset_id = $ins->ast_id." - ".$ins->prd_name;
		$data->type = $type;
		$data->inspection_form = $this->generate_inspection_form($ins, $type);
		$data->ins_id = $id;
		
		$data->content = $this->load->view('inspection_view', $data, true);
		$this->load->view('main_template', $data);
	}
	
	/**
	* Ajax function called when Inspection Details have been updated
	* @return string Field Value. '_failed_' if query fails
	*/
	function update_inspection_details(){
		extract($_POST);
		$det = $this->insp->save_inspection_details($ins_id, $name, $value);
		if($det === false){
			echo "_failed_";	
		}else{
			echo $det;
		}
	}
	
	/**
	* Ajax function called when Inspection Questions have been answered
	* @return string Field Value. '_failed_' if query fails
	*/
	function update_question_details(){
		extract($_POST);
		$det = $this->insp->save_question_details($ins_id, $que_id, $que_answer);
		if($det === false){
			echo "_failed_";	
		}else{
			echo $det;
		}
	}
	
	
	/**
	* Generates input form for current asset inspection
	* @param obj $ins Inspection Object
	* @param bool TRUE if Service Inspection, FALSE if New Asset
	* @return string Form HTML
	*/
	function generate_inspection_form($ins, $type){
		$f = form_open('', array('class'=>'inspectionForm', 'id'=>$ins->ins_id));
		$f .= form_fieldset();

		$f .= form_label("Group Select", "ins_group");
		$f .= form_dropdown("ins_group", array(""=>"--please select--", "BAT"=>"BAT", "MAXIMUS"=>"MAXIMUS"));
		
		$f .= form_label("Inspection Date", "ins_date");
		$f .= form_input("date", $ins->ins_date == 0?"":date("d/m/Y", $ins->ins_date));
		$f .= form_hidden("ins_date", $ins->ins_date);
		$f .= form_label("BAT Job Number", "ins_jobnumber");
		$f .= form_input("ins_jobnumber", $ins->ins_jobnumber);
		$f .= form_label("Cust PO Number", "ins_custpo");
		$f .= form_input("ins_custpo", $ins->ins_custpo);
		$f .= form_label("Test Pressure", "ins_testpressure");
		$f .= form_input("ins_testpressure", $ins->ins_testpressure);
		$f .= form_label("Working Pressure", "ins_workingpressure");
		$f .= form_input("ins_workingpressure", $ins->ins_workingpressure);
		$f .= form_label("Test Time (min)", "ins_testtime");
		//$f .= form_input("ins_testtime", $ins->ins_testtime);
		$f .= form_dropdown("ins_testtime", array(""=>"--please select--", 5=>"5 Min", 10=>"10 min", 15=>"15 min", 20=>"20 min", 30=>"30 min", 60=>"60 min"));
		$f .= form_label("Ohms End A", "ins_ohms_a");
		$f .= form_input("ins_ohms_a", $ins->ins_ohms_a);
		$f .= form_label("Ohms End B", "ins_ohms_b");
		$f .= form_input("ins_ohms_b", $ins->ins_ohms_b);
		$f .= form_label("Ohms Total", "ins_ohms_overall");
		$f .= form_input("ins_ohms_overall", $ins->ins_ohms_overall);
		if($type == "SERVICE"){
			$f .= form_label("Instruction", "ins_instruction");
			$f .= form_dropdown("ins_instruction", array(""=>"--please select--", "PASS"=>"PASS", "REPAIR"=>"REPAIR", "SCRAP"=>"SCRAP"));
		}
		$f .= form_label("Certificate Result", "ins_certresult");
		$f .= form_dropdown("ins_certresult", array(""=>"--please select--", "PASS"=>"PASS", "FAIL"=>"FAIL"));
		$f .= form_label("Notes", "ins_comments");
		$f .= form_textarea('ins_comments', $ins->ins_comments);
		$f .= form_fieldset_close();
		//generate questions from que_question table
		if($type == "SERVICE"){
			$f .= form_fieldset('',array('class'=>'questions'));
			$f .= form_button(array('id'=>'passAllButton', 'content'=>'Pass All'));
			$que = $this->insp->get_service_questions();
			$ans = array(""=>"--please select--", "PASS"=>"PASS", "FAIL"=>"FAIL", "N/A"=>"N/A", "DNI"=>"DNI");
			foreach($que as $q){
				$f .= form_label($q->que_question, "que_".$q->que_id);
				$f .= form_dropdown("que_".$q->que_id, $ans, "", "class='inspQuestion'");	
			}
			$f .= form_fieldset_close();
		}
		$f .= form_close();
		return $f;
		
	}
	
}