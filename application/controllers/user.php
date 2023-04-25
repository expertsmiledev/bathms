<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class User extends MY_Controller {

	public function __construct(){
		parent::__construct();
		$this->load->model('User_model', 'user');
	}
	
	public function index(){
		//$this->load->view('view');
	}
	
	function update_user(){
		$res = $this->user->update_user($_POST);
		if($res === true){
			echo 1;
		}else{
			echo 0;	
		}
	}
}