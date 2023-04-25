<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Testsdue extends MY_Controller {

	public function __construct(){
		parent::__construct();
		$this->load->model('Testsdue_model', 'test');
	}
	
	public function index(){
		$this->view();
	}
	
	public function view(){
		$data = new stdClass;
		$data->js_files = array('/js/testsdue.js', '/js/jquery-ui-1.10.3.custom.min.js');
		$data->css_files = array('/css/jquery-ui-1.10.3.custom.min.css');
		$data->nav = $this->build_nav();
		$data->user = '';
		
		$m = $this->get_months();
		$data->this_month = $this->get_tests_due($m[0][0]);
		$data->next_month = $this->get_tests_due($m[0][1]);
		$data->this_month_name = "(".$m[1][0].")";
		$data->next_month_name = "(".$m[1][1].")";
		$data->content = $this->load->view('testsdue_view', $data, true);
		$this->load->view('main_template', $data);
	}
	
	/**
	* Finds the current month and the following month. Returns them in an array of numbers & names
	* @return array - [[this month number, next month number],[this month name, next month name]]
	*/
	function get_months(){
		$this_m = date("n");
		$next_m = $this_m + 1;
		$this_m_name = date("F");
		$next_m_name = date("F", mktime(0,0,0,$next_m));
		return array(array($this_m,$next_m), array($this_m_name,$next_m_name));
	}
	
	/**
	* Finds customers with tests due in the month supplied
	* @param int $m - integer corresponding to a month
	* @return string - html code for list of customers with tests due
	*/
	function get_tests_due($m){
		$td = $this->test->get_tests_due($m);
		$h = "<ul class='testsDueList'>\n";
		foreach($td as $c){
			$h .= "<li class='ui-state-default'><a href='/customer/".$c->cus_id."'>".$c->cus_name."</a></li>\n";
		}
		$h .= "</ul>";
		
		return $h;
	}
}