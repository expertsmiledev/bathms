<?php
class MY_Model extends CI_Model{
	
	function __construct(){
		parent::__construct();
	}
	
	function get_all_records_as_array($table_name){
		$pref = substr($table_name,0,3);
		if($this->db->field_exists($pref."_sort", $table_name)){
			$qry = "SELECT *
				FROM $table_name ORDER BY ".$pref."_sort";
		}else{
			$qry = "SELECT *
					FROM $table_name";
		}
		$res = $this->db->query($qry);
		$p = array();
		foreach($res->result() as $r){
			$p[] = $r;	
		}
		return $p;
	}

	
}
?>