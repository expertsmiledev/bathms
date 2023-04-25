<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Admin_model extends MY_Model{
	
	function __construct(){ 	
		parent::__construct();
	}
	
	function customer_search($search){
		$qry = "SELECT cus_id, cus_name, cus_code
				FROM cus_customer
				WHERE cus_name LIKE '%$search%'
				OR cus_code LIKE '%$search%'
				AND cus_status = 1";
				
		$res = $this->db->query($qry);
		return $res->result();
	}

}