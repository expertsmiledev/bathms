<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Siteadmin extends MY_Controller {

	public function __construct(){
		parent::__construct();
		$this->load->model('Admin_model', 'admin');
	}
	
	public function index(){
		$this->view();
	}
	
	function view(){
		$data = new stdClass;
		$data->js_files = array("/js/search.js");
		$data->nav = $this->build_nav();
		$data->user = '';
		$data->content = $this->load->view('admin_view', $data, true);
		$this->load->view('main_template', $data);
	}
	
	//ajax function for Customer Search
	function customer_search(){
		$s = $_POST['srch'];
		echo $this->generate_customer_search($s);	
	}
	
	function generate_customer_search($search_term){
		$result = $this->admin->customer_search($search_term);

		$cus_list = "<ul>";
		foreach($result as $cus){
			$cus_list .= "<li><a href='/customer/".$cus->cus_id."' >".$cus->cus_name." (".$cus->cus_code.") </a></li>\n";	
		}
		$cus_list .= "</ul>";
		
		return $cus_list;
	}
}