<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Login extends MY_Controller {

	public function __construct(){
		parent::__construct();
		$this->load->model('User_model', 'user');
	}
	
	function index(){
		$role = $this->session->userdata('role');
		if($this->landing_page($role) != ""){
			redirect(base_url($this->landing_page($role)));	
		}
		$this->session->keep_flashdata('entry_uri');
		$this->view();
	}
	
	function view($valid=true){
		$data = new stdClass;
		$data->header = $valid?LOG_IN:INVALID_LOGIN;
		$data->nav = $this->build_nav();
		$data->content = $this->load->view('login_view', $data, true);
		$this->load->view('main_template', $data);
	}
	
	function log_out(){
		$this->session->unset_userdata('is_logged_in');
		$this->session->unset_userdata('role');
		redirect(base_url("login"));	
	}
	
	function landing_page($role){
		if($role == NULL || $role == ""){
			return '';
		}else{
			if($page = $this->user->get_landing_page($role)){
				return $page;
			}
		}
	}
	
	function validate(){
		$email = $_POST['email'];
		$password = $_POST['password'];
		$re = $this->validate_login($email,$password);
		if(!$re){
			$this->view(false);
		}else{
			redirect($re);	
		}
	}
	
	function validate_login($email,$password){
		
		if($result = $this->user->validate($email, $password)){
			//set session data
			$user_name = $result->usr_firstname." ".$result->usr_lastname;
			$ses_data = array(
				"is_logged_in"	=> true,
				"role"			=> $result->rol_id,
				"username"		=> $user_name,
				"user_email"	=> $result->usr_email,
				"cus"			=> $result->cus_id
			);
			$this->session->set_userdata($ses_data);
			if($this->session->flashdata('entry_uri') != ""){
				return(base_url($this->session->flashdata('entry_uri')));
			}else{
				return(base_url($result->rol_landingpage));
			}
		}else{
			return false;	
		}
	}
}
