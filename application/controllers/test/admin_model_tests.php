<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

require_once(APPPATH . '/controllers/test/toast.php');

class Admin_model_tests extends Toast {

	function __construct(){
		parent::__construct(__FILE__); // Remember this
		$this->load->model('Admin_model', 'admin');
	}
	
	function test_search_customer_doesnt_exist(){
		$search_term = "xxxxzzzz";
		$result = $this->admin->customer_search($search_term);
		$this->_assert_empty($result);
	}
	
	function test_search_for_known_name(){
		$search_term = "BAT Hose";
		$result = $this->admin->customer_search($search_term);
		$good_result = false;
		foreach($result as $sr){
			if($sr->cus_name == $search_term){
				$good_result = true;
			}
		}
		$this->_assert_true($good_result);
	}
	
	function test_return_multiple_results(){
		$search_term = "Ltd";
		$result = $this->admin->customer_search($search_term);
		$this->_assert_not_equals(count($result), 1 );
	}
	
}