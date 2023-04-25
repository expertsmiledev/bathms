<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Customer extends MY_Controller {
	private $role;
	
	public function __construct(){
		parent::__construct();
		$this->load->model('Customer_model', 'cust');
		$this->role = $this->session->userdata['role'];
		ini_set('memory_limit', '256M');//CPK Added 2015-03-20 after memory limit exceeded
	}
	
	function view($cus_id, $cust_name, $details){
		$data = new stdClass();
		if($this->role == 1 || $this->role == 2){
			$data->js_files = array('/js/customer.js', '/js/jquery-ui-1.10.3.custom.min.js');
		}else{
			$data->js_files = array('/js/dashboard.js', '/js/jquery-ui-1.10.3.custom.min.js');
		}
		$data->css_files = array('/css/jquery-ui-1.10.3.custom.min.css');
		$data->nav = $this->build_nav();
		$data->cust_id = $cus_id;
		$data->cust_name = $cust_name;
		$data->customer = $details;
		$data->assets = $this->build_customer_hose_table($cus_id);
		$data->locations = $this->build_customer_location_table($cus_id);
		$data->users = $this->build_customer_user_table($cus_id);
		$data->user_role = $this->role;
		$data->content = $this->load->view('customer_view', $data, true);
		$this->load->view('main_template', $data);
	}
	
//*************CUSTOMER  DETAILS************//	

	function create_customer_form($c){
		$f = form_open('', array('class'=>'customerForm', 'id'=>$c->cus_id));
		$f .= form_fieldset();
		$f .= form_label('Customer Code', 'cus_code');
		$f .= form_input('cus_code', $c->cus_code);
		$f .= form_label('Name', 'cus_name');
		$f .= form_input('cus_name', $c->cus_name);
		$f .= form_label('Address 1', 'cus_address1');
		$f .= form_input('cus_address1', $c->cus_address1);
		$f .= form_label('Address 2', 'cus_address2');
		$f .= form_input('cus_address2', $c->cus_address2);
		$f .= form_label('City', 'cus_city');
		$f .= form_input('cus_city', $c->cus_city);
		$f .= form_label('State', 'cus_state');
		$f .= form_input('cus_state', $c->cus_state);
		$f .= form_label('Country', 'cus_country');
		$f .= form_input('cus_country', $c->cus_countrycode);
		if($this->session->userdata['role'] == 1 || $this->session->userdata['role'] == 2){
			$f .= form_label('HMS Retest', 'cus_hmsretest');
			$f .= form_checkbox('cus_hmsretest', $c->cus_hmsretest, $c->cus_hmsretest, "class='cat_check'");
			$month = array('--select month--','January','February','March','April','May','June','July','August','September','October','November','December');
			$f .= form_dropdown('cus_retestdate', $month, $c->cus_retestdate, "class='retestDate'");
		}
		$f .= form_fieldset_close();
		$f .= form_fieldset();
		$f .= form_label('Phone', 'cus_phone');
		$f .= form_input('cus_phoneprefix', $c->cus_phoneprefix, "class='preTextBox'");
		$f .= form_input('cus_phone', $c->cus_phone, "class='sufTextBox'");
		$f .= form_label('Fax', 'cus_fax');
		$f .= form_input('cus_faxprefix', $c->cus_faxprefix, "class='preTextBox'");
		$f .= form_input('cus_fax', $c->cus_fax, "class='sufTextBox'");
		$f .= form_label('Mobile', 'cus_mobile');
		$f .= form_input('cus_mobileprefix', $c->cus_mobileprefix, "class='preTextBox'");
		$f .= form_input('cus_mobile', $c->cus_mobile, "class='sufTextBox'");
		$f .= form_label('Email', 'cus_email');
		$f .= form_input('cus_email', $c->cus_email);
		$f .= form_label('Notes', 'cus_notes');
		$f .= form_textarea('cus_notes', $c->cus_notes);
		$f .= form_fieldset_close();
		$f .= form_close();
		return $f;
	}

	/**
	* Adds new customer
	* 
	*
	*/
	function add_new(){
		$id = $this->cust->add_new();
		$this->get_customer_details($id);
	}
	
	/**
	* Deletes customer from database
	* @param $id = Customer ID
	*/
	function delete_customer($id){
		if($this->cust->delete_customer($id)){
			redirect("/siteadmin");
		}else{
			redirect("/customer/$id?err=delete_fail");
		}
	}
	
	/**
	* this function called from url. See Routes.
	* @param int $id customer id
	*/
	function get_customer_details($id){
		$cust_details = $this->cust->get_customer($id);
		$form_html = $this->create_customer_form($cust_details);
		$cust_name = $cust_details->cus_name;
		if($cust_details->cus_code != ""){
			$cust_name .= " (".$cust_details->cus_code.")";
		}
		$this->view($id, $cust_name, $form_html);
	}
	
	/**
	* Ajax function called when Customer Details have been changed
	* @return string Field Value. "_failed_" if query fails
	*/
	function update_customer_details(){
		extract($_POST);
		$det = $this->cust->save_customer_details($cus_id, $name, $value);
		if($det === false){
			echo "_failed_";	
		}else{
			echo $det;
		}
	}
	

//************CUSTOMER HOSE TABLE *******************//
	/**
	* @param int $id customer id
	* @return string asset table html. Between <tbody></tbody> tags
	*/
	function build_customer_hose_table($id){
		$data = new stdClass();
		$role = $this->role;
		$res = $this->cust->get_hoses($id);
		$rows = "";
		foreach($res as $h){
			$rows .= $this->build_asset_table_row($h, $role);
		}
		$data->hose_table_rows = $rows;
		$data->cus_id = $id;
		$data->user_role = $role;
		$html = $this->load->view('asset_table_view', $data, true);
		return $html;		
	}
	
	
	/**
	* Ajax function called when copy asset button is clicked
	* @return string whole hose table with copied row added. FALSE if query fails
	*/
	function copy_asset(){
		extract($_POST);
		//extract($_GET);
		if($this->cust->copy_asset($ast_id)){
			$html = $this->build_customer_hose_table($cus_id);
			echo $html;
		}else{
			echo false;	
		}
	}
	
	/**
	* Ajax function called when delete asset button is clicked
	* @return string whole hose table minus deleted row. FALSE if query fails
	*/
	function delete_asset(){
		extract($_POST);
		if($this->cust->delete_asset($ast_id)){
			$html = $this->build_customer_hose_table($cus_id);
			echo $html;
		}else{
			echo false;	
		}
	}

	/**
	* Ajax function called when add asset button is clicked
	* @return string whole hose table with blank row added (cus_id added to new row). FALSE if query fails
	*/
	function add_asset(){
		extract($_POST);
		if($this->cust->add_asset($cus_id)){
			$html = $this->build_customer_hose_table($cus_id);
			echo $html;
		}else{
			echo false;	
		}
	}
	
	/**
	* @param obj $ast - asset object
	* @return string table row html code. Includes <tr></tr> tags
	*/
	function build_asset_table_row($ast){
		$data = new stdClass();
		$cid = $ast->cus_id;
		// determine row colour
		$oneyear = strtotime('-1 year');
		$exp = strtotime($this->get_last_inspection_date($ast->ast_id)) < $oneyear;
		$data->rowclass = $exp ? 'exp' : 'cur';
		//get field values
		$data->ast_id = $ast->ast_id;
		$data->loc_dropdown = $this->create_cust_dropdown($cid, 'loc_location', 'loc_id', 'loc_name', $ast->loc_id);
		$data->prd_dropdown = $this->create_product_dropdown($ast->prd_id);
		$data->ast_length = $ast->ast_length;
		$data->ast_serial = $ast->ast_serial;
		$data->cpl_id_a_dropdown = $this->create_dropdown('cpl_coupling', 'cpl_id', 'cpl_name', $ast->cpl_id_a);
		$data->cpa_id_a_dropdown = $this->create_dropdown('cpa_couplingaddon', 'cpa_id', 'cpa_name', $ast->cpa_id_a);
		$data->atm_id_a_dropdown = $this->create_dropdown('atm_attachmethod', 'atm_id', 'atm_name', $ast->atm_id_a);
		$data->cpm_id_a_dropdown = $this->create_dropdown('cpm_couplingmaterial', 'cpm_id', 'cpm_name', $ast->cpm_id_a);
		$data->cpl_id_b_dropdown = $this->create_dropdown('cpl_coupling', 'cpl_id', 'cpl_name', $ast->cpl_id_b);
		$data->cpa_id_b_dropdown = $this->create_dropdown('cpa_couplingaddon', 'cpa_id', 'cpa_name', $ast->cpa_id_b);
		$data->atm_id_b_dropdown = $this->create_dropdown('atm_attachmethod', 'atm_id', 'atm_name', $ast->atm_id_b);
		$data->cpm_id_b_dropdown = $this->create_dropdown('cpm_couplingmaterial', 'cpm_id', 'cpm_name', $ast->cpm_id_b);
		$data->nmb_dropdown = $this->create_dropdown('nmb_nominalbore', 'nmb_id', 'nmb_name', $ast->nmb_id);
		$data->ast_manufacturedate = $ast->ast_manufacturedate;
		$data->ast_gravedate = $ast->ast_gravedate;
		$data->ast_lastcert = $ast->ast_lastcert;
		$data->ast_notes = $ast->ast_notes;
		$data->user_role = $this->role;
		
		return $this->load->view('asset_table_row_view', $data, true);
				
	}

	function get_last_inspection_date($ast_id){
		return $this->cust->get_last_inspection($ast_id);
	}
	
	/**
	* Creates option values for HTML select element
	* @param string $table = table name
	* @param string $value_field = table field name to use as option value
	* @param string $text_field = table field name to use as option text
	* @param int $selected = pk value to be default selected when dropdown loads
	* @return string dropdown (select) html code. Option tags only
	*/
	function create_dropdown($table, $value_field, $text_field, $selected=false){
		$res = $this->cust->get_all_records_as_array($table);
//print_r($res);
		$h = "<option> - </option>\n";
		
		foreach($res as $r){
			if($r->$value_field == $selected){
				$h .= "<option value='".$r->$value_field."' selected='selected'>".$r->$text_field."</option>\n";	
			}else{
				$h .= "<option value='".$r->$value_field."'>".$r->$text_field."</option>\n";	
			}
		}
		return $h;
	}
	/**
	* Creates option values for HTML select element specific to a customer
	* @param int $cus_id = Customer ID
	* @param string $table = table name
	* @param string $value_field = table field name to use as option value
	* @param string $text_field = table field name to use as option text
	* @param int $selected = pk value to be default selected when dropdown loads
	* @return string dropdown (select) html code. Option tags only
	*/
	function create_cust_dropdown($cus_id, $table, $value_field, $text_field, $selected=false){
		$res = $this->cust->get_all_cust_records_as_array($cus_id, $table);
//print_r($res);
		$h = "<option> - </option>\n";
		
		foreach($res as $r){
			if($r->$value_field == $selected){
				$h .= "<option value='".$r->$value_field."' selected='selected'>".$r->$text_field."</option>\n";	
			}else{
				$h .= "<option value='".$r->$value_field."'>".$r->$text_field."</option>\n";	
			}
		}
		return $h;
	}
	/**
	* Creates option values for product dropdown
	* @param int $selected = prd_id value to be default selected when dropdown loads
	* @return string dropdown (select) html code. Option tags only
	*/

	function create_product_dropdown($selected=false){
		$res = $this->cust->get_products();
//print_r($res);
		$h = "<option> - </option>\n";
		
		foreach($res as $r){
			if($r->prd_id == $selected){
				$h .= "<option value='".$r->prd_id."' selected='selected'>".$r->prd_name." (".$r->prd_code.")</option>\n";	
			}else{
				$h .= "<option value='".$r->prd_id."'>".$r->prd_name." (".$r->prd_code.")</option>\n";	
			}
		}
		return $h;
	}
	
	/**
	* Creates option values for HTML select element through an ajax request
	* @param json_obj $json_args
	* @print string option tag html code
	*/
	function ajax_create_cust_dropdown(){
		extract($_POST);
		$html = $this->create_cust_dropdown($cus_id, $table, $value, $text);
		echo $html;
	}

//************ LOCATION TABLE ***********************//
	
	/**
	* @param int $id customer id
	* @return string location table html. Goes between <tbody></tbody> tags
	*/
	function build_customer_location_table($id){
		$res = $this->cust->get_locations($id);
		$rows = "";
		$data = new stdClass;
		foreach($res as $l){
			$rows .= $this->build_location_table_row($l);
		}
		$data->location_table_rows = $rows;
		$data->cus_id = $id;
		$html = $this->load->view('location_table_view', $data, true);
		return $html;		
	}
	
	/**
	* @param obj $loc - location object
	* @return string table row html code. Includes <tr></tr> tags
	*/
	function build_location_table_row($loc){
		$data = new stdClass;
		$data->loc_id = $loc->loc_id;
		$data->loc_name = $loc->loc_name;
		
		return $this->load->view('location_table_row_view', $data, true);
	}
	

	/**
	* Ajax function called when add location button is clicked
	* @return string whole location table with blank row added (cus_id added to new row). FALSE if query fails
	*/
	function add_location(){
		extract($_POST);
		if($this->cust->add_location($cus_id)){
			$html = $this->build_customer_location_table($cus_id);
			echo $html;
		}else{
			echo false;	
		}
	}
	
	/**
	* Ajax function called when location details are changed
	* @return bool true(1) on success, false(0) on fail.
	*/
	function update_location(){
		extract($_POST);
		if($this->cust->update_location($loc_id, $field, $value)){
			echo 1;
		}else{
			echo 0;	
		}
	}
	
	/**
	* Ajax function called when delete location button is clicked
	* @return string whole location table minus deleted row. FALSE if query fails
	*/
	function delete_location(){
		extract($_POST);
		if(!$this->cust->check_assets_at_location($loc_id)){
			if($this->cust->delete_location($loc_id)){
				$html = $this->build_customer_location_table($cus_id);
				echo $html;
			}else{
				echo false;	
			}
		}else{
			echo 'exists';	
		}
	}

//************************* CUSTOMER USERS ***************************//
	
	/**
	* @param int $id customer id
	* @return string user table html. Goes between <tbody></tbody> tags
	*/
	function build_customer_user_table($id){
		$res = $this->cust->get_users($id);
		$rows = "";
		$data = new stdClass;
		foreach($res as $u){
			$rows .= $this->build_user_table_row($u);
		}
		$data->user_table_rows = $rows;
		$data->cus_id = $id;
		$html = $this->load->view('user_table_view', $data, true);
		return $html;		
	}
	
	/**
	* @param obj $loc - location object
	* @return string table row html code. Includes <tr></tr> tags
	*/
	function build_user_table_row($usr){
		$data = new stdClass;
		$data->usr_firstname = $usr->usr_firstname;
		$data->usr_lastname = $usr->usr_lastname;
		$data->usr_password = $usr->usr_password;
		$data->usr_email = $usr->usr_email;
		$data->role_dropdown = $this->create_dropdown('rol_role', 'rol_id', 'rol_name', $usr->rol_id);
		
		return $this->load->view('user_table_row_view', $data, true);
	}

	/**
	* Ajax function called when add user button is clicked
	* @return string whole user table with blank row added (usr_id added to new row). FALSE if query fails
	*/
	function add_user(){
		extract($_POST);
		if($this->cust->add_user($cus_id)){
			$html = $this->build_customer_user_table($cus_id);
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
		echo $this->cust->update_user($field,$value,$email);
	}

	/**
	* Ajax function called when delete user button is clicked
	* @return string whole user table minus deleted row. FALSE if query fails
	*/
	function delete_user(){
		extract($_POST);
		if($this->cust->delete_user($usr_email)){
			$html = $this->build_customer_user_table($cus_id);
			echo $html;
		}else{
			echo false;	
		}
	}

	
}