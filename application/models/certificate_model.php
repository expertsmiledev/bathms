<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Certificate_model extends MY_Model{
	
	function __construct(){ 	
		parent::__construct();
	}
	
	function get_inspection_data($ins_id){
		$qry = "SELECT ins_date,
						ins_type,
						prd_name,
						a.ast_id,
						cus_name,
						cus_city,
						i.ins_id,
						loc_name,
						CONCAT(usr_firstname, ' ',usr_lastname) AS username,
						prd_standard,
						nmb_name,
						ast_serial,
						ast_length,
						ast_manufacturedate,
						ast_gravedate,
						(SELECT cpl_name FROM cpl_coupling WHERE cpl_id = a.cpl_id_a) AS coupling_a,
						(SELECT cpa_name FROM cpa_couplingaddon WHERE cpa_id = a.cpa_id_a) AS coupling_addon_a,
						(SELECT atm_name FROM atm_attachmethod WHERE atm_id = a.atm_id_a) AS attach_method_a,
						(SELECT cpm_name FROM cpm_couplingmaterial WHERE cpm_id = a.cpm_id_a) AS coupling_material_a,
						(SELECT cpl_name FROM cpl_coupling WHERE cpl_id = a.cpl_id_b) AS coupling_b,
						(SELECT cpa_name FROM cpa_couplingaddon WHERE cpa_id = a.cpa_id_b) AS coupling_addon_b,
						(SELECT atm_name FROM atm_attachmethod WHERE atm_id = a.atm_id_b) AS attach_method_b,
						(SELECT cpm_name FROM cpm_couplingmaterial WHERE cpm_id = a.cpm_id_b) AS coupling_material_b,
						ins_jobnumber,
						ins_custpo,
						ins_ohms_a,
						ins_ohms_b,
						ins_ohms_overall,
						ins_testpressure,
						ins_testtime,
						ins_workingpressure,
						ins_electricalcontinuity,
						ins_instruction,
						ins_certresult,
						ins_alert,
						ins_comments,
						ins_timestamp
				FROM ((((((ins_inspection i JOIN ast_asset a ON i.ast_id = a.ast_id)
				JOIN prd_product p ON p.prd_id = a.prd_id)
				JOIN cus_customer c ON c.cus_id = a.cus_id)
				JOIN loc_location l ON l.`loc_id` = a.`loc_id`)
				JOIN usr_user u ON u.usr_email = i.`usr_email`)
				JOIN nmb_nominalbore n ON n.nmb_id = a.nmb_id)

				WHERE i.ins_id = $ins_id";
				
		$res = $this->db->query($qry);
		return $res->row();
	}
	
	function save_certificate_link($id, $link){
		$qry = "UPDATE ast_asset SET ast_lastcert = '$link'
				WHERE ast_id = $id";
		return $this->db->query($qry);	
	}
	
	/**
	* Retrieves latest inspection data for specified customer
	* @param int $id - Customer ID
	* @return array of data objects for inspection and asset 
	*/
	function get_inspection_data_for_customer($id){
		$qry = "SELECT ins_date,
						ins_type,
						prd_name,
						a.ast_id,
						cus_name,
						cus_city,
						i.ins_id,
						loc_name,
						CONCAT(usr_firstname, ' ',usr_lastname) AS username,
						prd_standard,
						nmb_name,
						ast_serial,
						ast_length,
						ast_manufacturedate,
						ast_gravedate,
						(SELECT cpl_name FROM cpl_coupling WHERE cpl_id = a.cpl_id_a) AS coupling_a,
						(SELECT cpa_name FROM cpa_couplingaddon WHERE cpa_id = a.cpa_id_a) AS coupling_addon_a,
						(SELECT atm_name FROM atm_attachmethod WHERE atm_id = a.atm_id_a) AS attach_method_a,
						(SELECT cpm_name FROM cpm_couplingmaterial WHERE cpm_id = a.cpm_id_a) AS coupling_material_a,
						(SELECT cpl_name FROM cpl_coupling WHERE cpl_id = a.cpl_id_b) AS coupling_b,
						(SELECT cpa_name FROM cpa_couplingaddon WHERE cpa_id = a.cpa_id_b) AS coupling_addon_b,
						(SELECT atm_name FROM atm_attachmethod WHERE atm_id = a.atm_id_b) AS attach_method_b,
						(SELECT cpm_name FROM cpm_couplingmaterial WHERE cpm_id = a.cpm_id_b) AS coupling_material_b,
						ins_jobnumber,
						ins_custpo,
						ins_ohms_a,
						ins_ohms_b,
						ins_ohms_overall,
						ins_testpressure,
						ins_testtime,
						ins_workingpressure,
						ins_electricalcontinuity,
						ins_instruction,
						ins_certresult,
						ins_alert,
						ins_comments,
						ins_timestamp
				FROM ((((((ins_inspection i JOIN ast_asset a ON i.ast_id = a.ast_id)
				JOIN prd_product p ON p.prd_id = a.prd_id)
				JOIN cus_customer c ON c.cus_id = a.cus_id)
				JOIN loc_location l ON l.`loc_id` = a.`loc_id`)
				JOIN usr_user u ON u.usr_email = i.`usr_email`)
				JOIN nmb_nominalbore n ON n.nmb_id = a.nmb_id) 
				WHERE a.cus_id = $id
				AND ins_timestamp = (SELECT MAX(i2.ins_timestamp)
										FROM ins_inspection i2
										WHERE i2.ast_id = i.ast_id)
				ORDER BY i.ast_id";
		$res = $this->db->query($qry);
		return $res->result();
	}
	
	function get_question_data($ins_id){
		$qry = "SELECT que_question, inq_answer
				FROM que_question q JOIN inq_inspectionquestion i
				ON i.que_id = q.que_id
				WHERE ins_id = $ins_id";
		$res = $this->db->query($qry);
		return $res->result();
	}
}