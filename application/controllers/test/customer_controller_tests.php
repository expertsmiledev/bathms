<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

require_once(APPPATH . '/controllers/test/toast.php');
require_once(APPPATH . "/controllers/customer.php");

class Customer_controller_tests extends Toast {

	function __construct(){
		parent::__construct(__FILE__); // Remember this
		$this->load->model('Customer_model', 'cust');
	}
	
	
}