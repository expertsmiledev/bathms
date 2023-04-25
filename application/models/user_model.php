<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class User_model extends MY_Model{
	
	function __construct(){ 	
		parent::__construct();
	}
	
	function get_landing_page($role){
		$qry = "SELECT rol_landingpage FROM rol_role
				WHERE rol_id = $role";
		if($res = $this->db->query($qry)){	
			$row = $res->row();
			$page = $row->rol_landingpage;
			return $page;
		}else{
			return false;
		}
	}
	
	function validate($email, $password){
		$qry = "SELECT 	r.rol_id,
						rol_name,
						rol_landingpage,
						usr_firstname,
						usr_lastname,
						usr_email,
						cus_id
				FROM usr_user u, rol_role r
				WHERE u.rol_id = r.rol_id
				AND u.usr_status = 1
				AND usr_email = '$email'
				AND usr_password = '".$password."'";
		$res = $this->db->query($qry);
		if($res->num_rows() > 0){
			return $res->row();
		}else{
			return false;	
		}
	}
	
	public function update_user($data){
		extract($data);
		if(is_int($value)){	
			$qry = "UPDATE usr_user SET $field = $value WHERE usr_id = $usr_id";
		}else{
			$qry = "UPDATE usr_user SET $field = '$value' WHERE usr_id = $usr_id";
		}
		if(!$this->db->query($qry)){
			return false;
		}else{
			return true;
		}
	}
}