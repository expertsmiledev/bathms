<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Customer_model extends MY_Model{
	
	function __construct(){ 	
		parent::__construct();
	}
	/**
	* Adds new empty customer record
	* @return int new record id
	*/
	function add_new(){
		$qry = "INSERT INTO cus_customer() VALUES ()";
		if($this->db->query($qry)){
			return $this->db->insert_id();
		}else{
			return false;	
		}
	}
	
	/**
	* Deletes customer record from database
	* @param int $id = Customer ID
	* @return bool confirm deletion
	*/
	function delete_customer($id){
		$qry = "DELETE FROM cus_customer WHERE cus_id = $id";
		if($this->db->query($qry)){
			return true;
		}else{
			return false;	
		}
	}

	
	/**
	* retrieves single customer record from cus_customer table
	* @param int $id customer id
	* @return obj customer row object
	*/
	function get_customer($id){
		$qry = "SELECT * FROM cus_customer	
				WHERE cus_id = $id
				AND cus_status = 1";
		$res = $this->db->query($qry);
		return $res->row();
	}
	
	/**
	* Retrieves all records from a table belonging to a particular customer
	* @param int $cus_id customer id
	* @param string $table_name name of database table
	* @return array - array of row value arrays
	*/
	function get_all_cust_records_as_array($cus_id, $table_name){
		$qry = "SELECT *
				FROM $table_name
				WHERE cus_id = $cus_id";
		$res = $this->db->query($qry);
		$p = array();
		foreach($res->result() as $r){
			$p[] = $r;	
		}
		return $p;
	}
	
	/**
	* gets all hoses belonging to a particular customer
	* @param int $id customer id
	* @return array of hose objects
	*/
	function get_hoses($id){
		$qry = "SELECT * FROM ast_asset
				WHERE cus_id = $id
				AND ast_status = 1
				ORDER BY ast_id DESC";
// new qry with last cert date added
				// SELECT a.*,(SELECT MAX(ins_date) FROM ins_inspection ins WHERE ins.ast_id = a.ast_id) ins_date 
				// FROM ast_asset a
				// WHERE a.cus_id = $id
				// AND ast_status = 1
				// ORDER BY a.ast_id DESC				
		$res = $this->db->query($qry);
		$h = array();
		foreach($res->result() as $r){
			$h[] = $r;	
		}
		return $h;
	}


	/**
	* gets all product details
	* @param int $id product id
	* @return array of hose objects
	*/
	function get_products(){
		$qry = "SELECT * FROM prd_product";
		$res = $this->db->query($qry);
		$p = array();
		foreach($res->result() as $r){
			$p[] = $r;	
		}
		return $p;
	}

	function get_last_inspection($ast_id){
		$qry = "SELECT MAX(ins_timestamp) as latest
					  FROM ins_inspection
					 WHERE ast_id = $ast_id";

		$res = $this->db->query($qry);
		$date = $res->row();
		return $date->latest;
	}
	
	/**
	* Saves Customer Details data
	* $param int $ast_id existing asset id
	* @return bool true on success, false on failure
	*/
	function save_customer_details($cus_id, $field, $value){
		if($field == "cus_hmsretest"){
			$qry = "UPDATE cus_customer SET $field = $value WHERE cus_id = $cus_id";
		}else{
			$qry = "UPDATE cus_customer SET $field = '$value' WHERE cus_id = $cus_id";
		}
		if(!$this->db->query($qry)){
			return false;	
		}else{
			return $value;	
		}
	}

	
	/**
	* copies existing asset to a new row
	* $param int $ast_id existing asset id
	* @return bool true on success, false on failure
	*/
	function copy_asset($ast_id){
		$qry = "INSERT INTO ast_asset 
				(cus_id, loc_id, prd_id, ast_length, cpl_id_a, cpa_id_a, cpm_id_a, atm_id_a, cpl_id_b, cpa_id_b, cpm_id_b, atm_id_b, nmb_id, ast_manufacturedate, ast_gravedate) 
				SELECT cus_id, loc_id, prd_id, ast_length, cpl_id_a, cpa_id_a, cpm_id_a, atm_id_a, cpl_id_b, cpa_id_b, cpm_id_b, atm_id_b, nmb_id, NOW(), DATE_ADD(NOW(), INTERVAL 5 YEAR) FROM ast_asset WHERE ast_id = $ast_id";
		if(!$this->db->query($qry)){
			return false;	
		}else{
			return true;	
		}
	}
	
	/**
	* adds new row to asset table using $cus_id
	* $param int $cus_id customer id
	* @return bool true on success, false on failure
	*/
	function add_asset($cus_id){
		$qry = "INSERT INTO ast_asset SET cus_id = $cus_id, ast_manufacturedate = NOW(), ast_gravedate = DATE_ADD(NOW(), INTERVAL 5 YEAR)";
		if(!$this->db->query($qry)){
			return false;	
		}else{
			return $this->db->insert_id();	
		}
	}
	
	/**
	* deletes existing asset
	* $param int $ast_id existing asset id
	* @return bool true on success, false on failure
	*/
	function delete_asset($ast_id){
		//$qry = "DELETE FROM ast_asset WHERE ast_id = $ast_id";
		$qry = "UPDATE ast_asset SET ast_status = 0 WHERE ast_id = $ast_id";
		
		if(!$this->db->query($qry)){
			return false;	
		}else{
			return true;	
		}
	}
	
	/**
	* gets all locations belonging to a particular customer
	* @param int $id customer id
	* @return array of location objects
	*/
	function get_locations($id){
		$qry = "SELECT * FROM loc_location
				WHERE cus_id = $id
				ORDER BY loc_id DESC";
		$res = $this->db->query($qry);
		$l = array();
		foreach($res->result() as $r){
			$l[] = $r;	
		}
		return $l;
	}
	
	/**
	* adds new row to location table using $cus_id
	* @param int $cus_id customer id
	* @return bool true on success, false on failure
	*/
	function add_location($cus_id){
		$qry = "INSERT INTO loc_location SET cus_id = $cus_id";
		
		if(!$this->db->query($qry)){
			return false;	
		}else{
			return $this->db->insert_id();	
		}
	}
	
	/**
	* Updates location table.
	* @param int $loc_id Location ID
	* @param string $field Field Name to update
	* @param mixed $value Value to insert
	* @return bool true on success, false on failure
	*/
	function update_location($loc_id, $field, $value){
		if(is_int($value)){
			$qry = "UPDATE loc_location SET $field = $value WHERE loc_id = $loc_id";
		}else{
			$qry = "UPDATE loc_location SET $field = '$value' WHERE loc_id = $loc_id";
		}
		
		if(!$this->db->query($qry)){
			return false;	
		}else{
			return true;	
		}
	}

	/**
	* Checks for any existing assets for location
	* $param int $loc_id location id
	* @return bool true(1) if true, false(0) if none.
	*/
	function check_assets_at_location($loc_id){
		$qry = "SELECT * FROM ast_asset WHERE loc_id = $loc_id";
		$res = $this->db->query($qry);
		if($res->num_rows() < 1){
			return false;	
		}else{
			return true;	
		}
	}
	
	/**
	* deletes existing location
	* $param int $loc_id existing location id
	* @return bool true on success, false on failure
	*/
	function delete_location($loc_id){
		$qry = "DELETE FROM loc_location WHERE loc_id = $loc_id";
		
		if(!$this->db->query($qry)){
			return false;	
		}else{
			return true;	
		}
	}
	
	/**
	* gets all users assigned to a particular customer
	* @param int $id customer id
	* @return array of user objects
	*/
	function get_users($id){
		$qry = "SELECT * FROM usr_user
				WHERE cus_id = $id
				AND usr_status = 1";
		$res = $this->db->query($qry);
		$u = array();
		foreach($res->result() as $r){
			$u[] = $r;	
		}
		return $u;
	}
	
	/**
	* adds new row to user table using $usr_id
	* $param int $cus_id customer id
	* @return int new user id on success, false on failure
	*/
	function add_user($cus_id, $rol_id = 3){
		//delete users where email is blank
		$qry_del = "DELETE FROM usr_user WHERE usr_email = ''";
		$this->db->query($qry_del);
		//add new user
		$qry = "INSERT INTO usr_user SET usr_email = '', cus_id = $cus_id, rol_id = $rol_id";
		if(!$this->db->query($qry)){
			return false;	
		}else{
			return true;	
		}
	}
	
	/**
	* Updates single value in usr_user table
	* @param string $col - table field name
	* @param string $value - value to be inserted
	* @param string $email - user email address
	* @return string 'exists' if usre email exists in the usr_user table
	* @return bool update success
	*/
	function update_user($col, $value, $email){
		if($col == "usr_email"){
			if($this->check_user_email($value)){
				return 'exists';
			}
		}
		$qry = "UPDATE usr_user SET $col = '$value' WHERE usr_email = '$email'";
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
	* $param int $ast_id existing asset id
	* @return bool true on success, false on failure
	*/
	function delete_user($usr_email){
		$qry = "DELETE FROM usr_user WHERE usr_email = '$usr_email'";
		
		if(!$this->db->query($qry)){
			return false;	
		}else{
			return true;	
		}
	}
	
}