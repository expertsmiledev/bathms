<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

require_once(APPPATH . '/controllers/test/toast.php');
require_once(APPPATH . "/controllers/login.php");

class Login_controller_tests extends Toast {

	function __construct(){
		parent::__construct(__FILE__); // Remember this
		$this->load->model('User_model', 'user');
	}
	
	function test_LandingPageAdminIsSiteAdmin(){
		$role = 1;
		$des = 'siteadmin';
		$res = Login::landing_page($role);
		$this->_assert_equals($res,$des);
	}
	function test_LandingPageAssemblyIsAssembly(){
		$role = 2;
		$des = 'assembly';
		$res = Login::landing_page($role);
		$this->_assert_equals($res,$des);
	}
	function test_LandingPageCustomerIsDashboard(){
		$role = 3;
		$des = 'dashboard';
		$res = Login::landing_page($role);
		$this->_assert_equals($res,$des);
	}
	
	function test_ValidateLoginInvalidEmail(){
		$email = "xxxxxx";
		$password = '';
		$res = Login::validate_login($email,$password);
		$this->_assert_false($res);
	}
	function test_ValidateLoginInvalidPassword(){
		$email = "peter@bda.co.nz";
		$password = '';
		$res = Login::validate_login($email,$password);
		$this->_assert_false($res);
	}
	function test_ValidateLoginValidEmailPassword(){
		$this->message = "Test Separately";
	}

}