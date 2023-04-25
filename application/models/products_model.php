<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Products_model extends MY_Model{
	
	function __construct(){ 	
		parent::__construct();
	}
	
	/**
	* Gets array of Product Main Categories
	* @return array - top level categories (parent = 0)
	*/
	function get_top_cats(){
		$qry = "SELECT * FROM cat_category WHERE cat_parent_id = 0";
		$res = $this->db->query($qry);
		return $res->result();	
	}
	
	/**
	* Gets products in a parent category
	* @param int $cat_id = Parent Category ID
	* @return object database result
	*/
	function get_products($cat_id){
		$qry = "SELECT * FROM prd_product WHERE cat_id = $cat_id";
		$res = $this->db->query($qry);
		return $res->result();	
	}
	
	/**
	* Gets subcategories for a particular Top Category
	* @param int $cat_id - Parent Category ID
	* @return string - Dropdown html
	*/
	function get_subcategories($cat_id){
		$qry = "SELECT * FROM cat_category WHERE cat_parent_id = $cat_id";
		$res = $this->db->query($qry);
		return $res->result();	
	}
	
	function update_product($data){
		extract($data);
		if(is_int($value)){	
			$qry = "UPDATE prd_product SET $field = $value WHERE prd_id = $prd_id";
		}else{
			$qry = "UPDATE prd_product SET $field = '$value' WHERE prd_id = $prd_id";
		}
		if(!$this->db->query($qry)){
			return false;
		}else{
			return true;
		}
	}
	
	function delete_product($id){
		$qry = "DELETE FROM prd_product WHERE prd_id = $id";
		if(!$this->db->query($qry)){
			return false;
		}else{
			return true;
		}
	}
	
	function add_product($cat){
		$qry = "INSERT INTO prd_product SET prd_name = '', cat_id = $cat";	
		if(!$this->db->query($qry)){
			return false;
		}else{
			return true;
		}
	}
}