<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Inspection_model extends MY_Model{
	
	function __construct(){ 	
		parent::__construct();
	}
	
	function get_customer($id){
		$qry = "SELECT c.cus_id, cus_name, cus_code
				FROM (cus_customer c JOIN ast_asset a ON  a.cus_id = c.cus_id)
				JOIN ins_inspection i ON i.ast_id = a.ast_id
				WHERE i.ins_id = $id";
		$res = $this->db->query($qry);
		return $res->row();	
				
	}
	
	function generate_inspection($id, $type){
		$email = $this->session->userdata('user_email');
		$prs = (object)$this->get_pressures($id);
		$working = is_null($prs->working) ? 'NULL' : $prs->working;
		$test = is_null($prs->test) ? 'NULL' : $prs->test;
		$qry = "INSERT INTO ins_inspection
				SET usr_email = '$email',
					ast_id = $id,
					ins_workingpressure = ".$working.",
					ins_testpressure = ".$test.",
					ins_type = '$type'";	
		if($this->db->query($qry)){
			return $this->db->insert_id();
		}else{
			return false;
		}
	}
	
	function get_inspection($id){
		$qry = "SELECT i.*, p.prd_name 
				FROM (ins_inspection i JOIN ast_asset a 
						ON a.ast_id = i.ast_id) 
				JOIN prd_product p 
				ON a.prd_id = p.prd_id
				WHERE i.ins_id = $id";
		$res = $this->db->query($qry);
		return $res->row();
				
	}
	
	/**
	* Gets Test & Working pressures of Asset from Asset & Nominal Bore
	* @param int $id = Asset ID
	* @return array Working (w) Pressure & Test (t) Pressure as working->xx & test->xx
	*/
	function get_pressures($id){
		$qnmb = "SELECT nmb_name 
				FROM nmb_nominalbore n
				JOIN ast_asset a
				ON a.nmb_id =  n.nmb_id
				WHERE ast_id = $id";
		$res = $this->db->query($qnmb)->row();
		$nmb = $res->nmb_name;
		
		$qry = "SELECT prd_wp_".strtolower($nmb)." as working, prd_tp_".strtolower($nmb)." as test
				FROM prd_product
				WHERE prd_id = (SELECT prd_id FROM ast_asset WHERE ast_id = $id)";
		$res = $this->db->query($qry);
		return $res->row();
	}
	
	/**
	* Gets the currently valid service questions
	* @return array array of question objects
	*/
	function get_service_questions(){
		$qry = "SELECT que_id, que_question
				FROM que_question 
				WHERE que_show = 1";
		$res = $this->db->query($qry);
		return $res->result();	
	}
	
	/**
	* Saves Inspection Details data
	* @param int $ins_id existing inspection id
	* @return string field value on success, false on failure
	*/
	function save_inspection_details($ins_id, $field, $value){
		if($field == "ins_alert"){
			$qry = "UPDATE ins_inspection SET $field = $value WHERE ins_id = $ins_id";
		}else{
			$qry = "UPDATE ins_inspection SET $field = '$value' WHERE ins_id = $ins_id";
		}
		if(!$this->db->query($qry)){
			return false;	
		}else{
			return $value;	
		}
	}
	
	/**
	* Saves Inspection Questions data
	* @param int $ins_id existing inspection id
	* @return string field value on success, false on failure
	*/
	function save_question_details($ins_id, $que_id, $inq_answer){
		$qry = "INSERT INTO inq_inspectionquestion 
				(ins_id, que_id, inq_answer)
				VALUES ($ins_id, $que_id, '$inq_answer')
				ON DUPLICATE KEY UPDATE
				ins_id = VALUES(ins_id),
				que_id = VALUES(que_id),
				inq_answer = VALUES(inq_answer)";
//echo $qry;
		if(!$this->db->query($qry)){
			return false;	
		}else{
			return $inq_answer;	
		}
	}


}