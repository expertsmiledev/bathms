<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

require_once(APPPATH . '/controllers/test/toast.php');
require_once(APPPATH . "/controllers/siteadmin.php");

class Siteadmin_controller_tests extends Toast {

	function __construct(){
		parent::__construct(__FILE__); // Remember this
		$this->load->model('Admin_model', 'admin');
	}
	
	function test_CustomerSearchResultEqualToDesiredHtml(){
		$search_term = "BAT";
		$res = Siteadmin::generate_customer_search($search_term);
		$desired_result = "<ul><li><a href='/customer/238' >BAT Hose () </a></li>
<li><a href='/customer/39771' >bat01 (bat01) </a></li>
</ul>";
		$this->_assert_equals($res, $desired_result);
	}
	
}