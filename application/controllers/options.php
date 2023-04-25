<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
class Options extends MY_Controller {

	public function __construct(){
		parent::__construct();
		$this->load->model('Options_model', 'options');
	}
	
	public function index(){
		$this->view();
	}
	
	public function view(){
		$data = new stdClass;
		$data->js_files = array('/js/options.js', '/js/jquery-ui-1.10.3.custom.min.js', '/js/jquery.ui.touch-punch.min.js');
		$data->css_files = array('/css/jquery-ui-1.10.3.custom.min.css');
		$data->nav = $this->build_nav();
		$data->user = '';
		$data->asset_attributes = $this->build_options_attributes();
		$data->questions = $this->build_options_questions();
		$data->admin_users = $this->build_user_table(1);
		$data->assembly_users = $this->build_user_table(2);
		
		$data->content = $this->load->view('options_view', $data, true);
		$this->load->view('main_template', $data);
	}
	
	
/*Asset Attributes section methods*/
	
	function build_options_attributes(){
		$data = new stdClass;
		$tables = array(
			'couplings'=>'cpl_coupling',
			'coupling_addons'=>'cpa_couplingaddon',
			'attach_methods'=>'atm_attachmethod',
			'materials'=>'cpm_couplingmaterial'
		);	
		
		foreach($tables as $var=>$t){
			$prefix = substr($t,0,4);
			$d = $this->options->get_attributes($t, $prefix);
			$items = '';
			foreach($d as $item){
				$varname = $prefix.'name';
				$varid = $prefix.'id';
				$items .= "<li class='ui-state-default' id='".$prefix.$item->$varid."' >".$item->$varname."</li>\n";	
			}
			$data->$var = $items;
		}
		$html = $this->load->view('options_attributes_view',$data,true);
		return $html;
	}
	
	public function save_attributes(){
		$savedata = json_decode($_POST['save']);
		$deldata = json_decode($_POST['del']);
		$table = $_POST['table'];
		$prefix = substr($table,0,4);
		if(count((array)$savedata) > 0){
			if(!$this->options->save_attributes($savedata, $table, $prefix)){
				echo 0;
				exit;
			}
		}
		if(count((array)$deldata) > 0){
			if(!$this->options->delete_attributes($deldata, $table, $prefix)){
				echo 0;
				exit;
			}
		}
		echo $this->build_options_attributes();	
	}
	
/*Inspection Questions section methods*/
	
	function build_options_questions(){
		$data = new stdClass;
		$q = $this->options->get_questions();
		$items = '';
		foreach($q as $item){
			$items .= "<li class='ui-state-default' id='que_".$item->que_id."' >".$item->que_question."</li>\n";	
		}
		$data->questions = $items;
		$html = $this->load->view('options_questions_view',$data,true);
		return $html;
	}

	public function save_questions(){
		$savedata = json_decode($_POST['save']);
		$deldata = json_decode($_POST['del']);
		if(count((array)$savedata) > 0){
			if(!$this->options->save_questions($savedata)){
				echo 0;
				exit;
			}
		}
		if(count((array)$deldata) > 0){
			if(!$this->options->delete_questions($deldata)){
				echo 0;
				exit;
			}
		}
		echo $this->build_options_questions();	
	}

/*Admin Users/Assembly Users Sections methods*/
	/**
	* @param int $role role id - admin = 1, assembly = 2
	* @return string user table html. Goes between <tbody></tbody> tags
	*/
	function build_user_table($role){
		$data = new stdClass;
		$res = $this->options->get_users($role);
		$rows = "";
		foreach($res as $u){
			$rows .= $this->build_user_table_row($u);
		}
		$data->user_table_rows = $rows;
		$html = $this->load->view('options_user_table_view', $data, true);
		return $html;		
	}
	
	/**
	* @param obj $user - user object
	* @return string table row html code. Includes <tr></tr> tags
	*/
	function build_user_table_row($user){
		$data = new stdClass;
		$data->usr_firstname = $user->usr_firstname;
		$data->usr_lastname = $user->usr_lastname;
		$data->usr_password = $user->usr_password;
		$data->usr_email = $user->usr_email;
		
		return $this->load->view('options_user_table_row_view', $data, true);
	}

	/**
	* Ajax function called when add user button is clicked
	* @return string whole user table with blank row added (usr_id added to new row). FALSE if query fails
	*/
	function add_user(){
		extract($_POST);
		if($this->options->add_user($role)){
			$html = $this->build_user_table($role);
			echo $html;
		}else{
			echo false;	
		}
	}
	
	/**
	* Ajax function called when a text box in the customer user table is changed
	* @return string whole user table. FALSE if query fails
	*/
	function update_user(){
		extract($_POST);
		echo $this->options->update_user($field,$value,$email);
	}

	/**
	* Ajax function called when delete user button is clicked
	* @return string whole location table minus deleted row. FALSE if query fails
	*/
	function delete_user(){
		extract($_POST);
		if($this->options->delete_user($email)){
			$html = $this->build_user_table($role);
			echo $html;
		}else{
			echo false;	
		}
	}
	
	/**
	* Ajax function called when user table edit box is changed
	* @return string whole location table minus deleted row. FALSE if query fails
	*/
	function save_user(){
		extract($_POST);
		echo $this->options->save_user($id,$field,$value);
//		if($this->options->save_user($id,$field,$value)){
//			echo 1;
//		}else{
//			echo 0;	
//		}
	}


}