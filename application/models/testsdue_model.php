<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Testsdue_model extends MY_Model{
	
	function __construct(){ 	
		parent::__construct();
	}
	
	function get_tests_due($m){
		$qry = "SELECT cus_id, cus_name	
				FROM cus_customer
				WHERE cus_retestdate = $m
				AND cus_hmsretest = 1";
		$res = $this->db->query($qry);
		return $res->result();
	}
}