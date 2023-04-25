<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Options_model extends MY_Model{
	
	function __construct(){ 	
		parent::__construct();
	}
	
	function get_attributes($table, $prefix){
		$qry = "SELECT * FROM $table ORDER BY ".$prefix."sort";
		$res = $this->db->query($qry);
		return $res->result();	
	}
	
	function save_attributes($data, $table, $prefix){
		$qry = "INSERT INTO $table (".$prefix."id, ".$prefix."name, ".$prefix."sort) VALUES";
		foreach($data as $d){
			$qry .= " (".$d->id.", '".$d->name."', ".$d->sort."),";
		}
		$qry = substr($qry,0,-1);
		$qry .= " ON DUPLICATE KEY UPDATE
					".$prefix."id = VALUES(".$prefix."id),
					".$prefix."name = VALUES(".$prefix."name),
					".$prefix."sort = VALUES(".$prefix."sort)";
		if(!$this->db->query($qry)){
			return false;
		}else{
			return true;
		}
	}
	
	function delete_attributes($data, $table, $prefix){
		$qry = "DELETE FROM $table WHERE ";
		foreach($data as $d){
			$qry .= $prefix."id = ".$d->id." OR ";	
		}
		$qry = substr($qry,0,-3);
		if(!$this->db->query($qry)){
			return false;
		}else{
			return true;
		}
	}
	
	function get_questions(){
		$qry = "SELECT * FROM que_question ORDER BY que_sort";
		$res = $this->db->query($qry);
		return $res->result();	
	}
	
	function save_questions($data){
		$qry = "INSERT INTO que_question (que_id, que_question, que_sort) VALUES";
		foreach($data as $d){
			$qry .= " (".$d->id.", '".$d->question."', ".$d->sort."),";
		}
		$qry = substr($qry,0,-1);
		$qry .= " ON DUPLICATE KEY UPDATE
					que_id = VALUES(que_id),
					que_question = VALUES(que_question),
					que_sort = VALUES(que_sort)";
		if(!$this->db->query($qry)){
			return false;
		}else{
			return true;
		}
	}
	
	function delete_questions($data){
		$qry = "DELETE FROM que_question WHERE ";
		foreach($data as $d){
			$qry .= "que_id = ".$d->id." OR ";	
		}
		$qry = substr($qry,0,-3);
		if(!$this->db->query($qry)){
			return false;
		}else{
			return true;
		}
	}
	
	/**
	* gets all users in role 1 (admin)
	* @param int $role role id - admin = 1, assembly = 2
	* @return array of user objects
	*/
	function get_users($role){
		$qry = "SELECT * FROM usr_user
				WHERE rol_id = $role
				AND usr_status = 1";
		$res = $this->db->query($qry);
		$u = array();
		foreach($res->result() as $r){
			$u[] = $r;	
		}
		return $u;
	}
	
	/**
	* adds new row to user table, adds customer as BAT and role as 1(admin)
	* @param int $role role id - admin = 1, assembly = 2
	* @return int new user id on success, false on failure
	*/
	function add_user($role){
		$qry = "INSERT INTO usr_user SET cus_id = ".DEFAULT_USER_CUSTOMER_ID.", rol_id = $role";
		
		return($this->db->query($qry));	
	}
	
	/**
	* Updates single value in usr_user table
	* @param string $col - table field name
	* @param string $value - value to be inserted
	* @param string $email - user email address
	* @return string 'exists' if user email exists in the usr_user table
	* @return bool update success
	*/
	function update_user($col, $value, $email){
		if($col == "usr_email"){
			if($this->check_user_email($value)){
				return 'exists';
			}else{
				$qry = "UPDATE usr_user SET $col = '$value' WHERE usr_email = ''";	
			}
		}else{
			$qry = "UPDATE usr_user SET $col = '$value' WHERE usr_email = '$email'";
		}
		return $this->db->query($qry);
	}
	
	/**
	* Checks for existing email address in the user table
	* @param string $email - user email address
	* @return bool true if email exists
	*/
	function check_user_email($email){
		$qry = "SELECT * FROM usr_user WHERE usr_email = '$email'";
		$res = $this->db->query($qry);
		return($res->num_rows > 0);
	}
	
	/**
	* deletes existing user
	* @return bool true on success, false on failure
	*/
	function delete_user($email){
		$qry = "DELETE FROM usr_user WHERE usr_email = '$email'";
		
		if(!$this->db->query($qry)){
			return false;	
		}else{
			return true;	
		}
	}
	
	function save_user($id, $field, $value){
		$qry = "UPDATE usr_user
					SET $field = '$value'
					WHERE usr_id = $id";
		if(!$this->db->query($qry)){
			return false;	
		}else{
			return true;	
		}
	}


	
}