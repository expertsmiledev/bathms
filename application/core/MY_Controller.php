<?php
class MY_Controller extends CI_Controller{
	function __construct(){
		parent::__construct();
		$this->load->library('session');
		$this->load->helper('url');
		$this->load->helper('form');
		$this->load_constants();
		$uri = $this->uri->segment(1);
		if($uri != "login" & !$this->session->userdata('is_logged_in')){
			$this->session->set_flashdata('entry_uri',$uri);
			redirect(base_url("login"));
		}
}
	
	function load_constants(){
		//Get Config constants
		$qry = "SELECT con_name, con_datatype, con_value FROM con_configuration";
		$res = $this->db->query($qry);
		foreach($res->result() as $c){
			switch($c->con_datatype){
					case 'number':
						$cval = (float)$c->con_value;
						break;
					case 'int':
						$cval = (int)$c->con_value;
						break;
					case 'bool':
						$cval = (bool)$c->con_value;
						break;
					default:
						$cval = (string)$c->con_value;
			}
			$cname = $c->con_name;
			define($cname, $cval);	
		}
	}
	
	/**
	* Builds appropriate navigation depending on user role
	* @return string - html unordered list
	*/
	function build_nav(){
		$nav_array = array(1 => array("Home"=>"/siteadmin", "Options"=>"/options", "Products"=>"/products", "Tests Due"=>"/testsdue"),//admin users
							2 => array("Home"=>"/siteadmin", "Tests Due"=>"/testsdue"),//assembly users
							3 => array("Home"=>"/dashboard"));//customers
		$nav = "<ul>";
		if(!$this->session->userdata('role')){
			$nav .= "</ul>";
		}else{
			$role = $this->session->userdata('role');
			foreach($nav_array[$role] as $page=>$url){
				$nav .= "<li><a href='$url'>$page</a></li>\n";
			}
			$nav .= "</ul>";
		}
		return $nav;
	}
	
	function lock($args){
		$allow = array();
	    for ($i = 0; $i < func_num_args(); $i++) {
        	$allow[] = func_get_arg($i);
		}
		return in_array($this->session->userdata('role'), $allow);
    }
	
	function mysql_date_to_readable($mysql_date){
		if($mysql_date != NULL){
			$d = strtotime($mysql_date);
			$new_date = date("d/m/Y", $d);
			return $new_date;
		}else{
			return "";
		}
	}
	function english_date_to_mysql($english_date){
		$d = str_replace("/", "-" , $english_date);
		return date("Y-m-d", strtotime($d));
	}
}
?>