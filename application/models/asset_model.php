<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Asset_model extends MY_Model{
	
	function __construct(){ 	
		parent::__construct();
	}
	
	function update_asset($data){
		extract($data);
		if($field == 'ast_id'){
			//check for duplicates
			$chk = "SELECT * FROM ast_asset WHERE ast_id = $value";
			$res = 	$this->db->query($chk);
			if($res->num_rows() > 0){
				return 'duplicate';	
			}
		}
		
		if(is_int($value)){	
			$qry = "UPDATE ast_asset SET $field = $value WHERE ast_id = $ast_id";
		}else{
			$qry = "UPDATE ast_asset SET $field = '$value' WHERE ast_id = $ast_id";
		}
		
		if(!$this->db->query($qry)){
			return $this->db->_error_message();
		}else{
			return true;
		}
	}
}