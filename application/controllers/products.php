<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Products extends MY_Controller {

	public function __construct(){
		parent::__construct();
		$this->load->model('Products_model', 'prd');
	}
	
	public function index(){
		$this->view();
	}
	
	public function view(){
		$data = new stdClass;
		$data->js_files = array('/js/products.js', '/js/jquery-ui-1.10.3.custom.min.js', '/js/jquery.ui.touch-punch.min.js');
		$data->css_files = array('/css/jquery-ui-1.10.3.custom.min.css');
		$data->nav = $this->build_nav();
		$data->user = '';
		$top_cats = $this->prd->get_top_cats();
		$data->sections = "";
		foreach($top_cats as $c){
			$data->sections .= $this->build_section($c);	
		}
		
		
		$data->content = $this->load->view('products_view', $data, true);
		$this->load->view('main_template', $data);
	}
	
	/**
	* Builds section of product table for category
	* @param int - Category ID
	* @return string - html for category section
	*/
	function build_section($cat){
		$html = "<h2>".$cat->cat_name."</h2>\n";
		$html .= "<div class='productList'>\n";
		$html .= "<button class='addNewProduct' data-cat-id='".$cat->cat_id."'>Add Product</button>";
		$html .= "<div class='productTableWrapper'>\n";
		$html .= $this->build_product_table($cat->cat_id);
		$html .= "</div>\n</div>\n";
		return $html;
	}
	/**
	* Builds product page product table
	* @return string - html for Options page product table
	*/
	function build_product_table($cat){
		$data = new stdClass;
		$res = $this->prd->get_products($cat);
		$rows = "";
		foreach($res as $p){
			$rows .= $this->build_product_table_row($p);	
		}
		$data->product_table_rows = $rows;
		
		$html = $this->load->view('product_table_view',$data,true);
		return $html;
	}
	
	/**
	* @param obj $prd - product object
	* @return string table row html code. Includes <tr></tr> tags
	*/
	function build_product_table_row($prd){
		$data = new stdClass;
		$data->prd_enabled = $prd->prd_enabled;
		$data->prd_id = $prd->prd_id;
		$data->sub_cat_id = $this->create_category_dropdown($prd->cat_id, $prd->sub_cat_id);
		$data->prd_name = $prd->prd_name;
		$data->prd_code = $prd->prd_code;
		$data->prd_type = $prd->prd_type;
		$data->prd_grade = $prd->prd_grade;
		$data->prd_kind = $prd->prd_kind;
		$data->prd_testcode = $prd->prd_testcode;
		$data->prd_standard = $prd->prd_standard;
		$data->prd_wp_12nb = $prd->prd_wp_12nb;
		$data->prd_wp_20nb = $prd->prd_wp_20nb;
		$data->prd_wp_25nb = $prd->prd_wp_25nb;
		$data->prd_wp_32nb = $prd->prd_wp_32nb;
		$data->prd_wp_38nb = $prd->prd_wp_38nb;
		$data->prd_wp_50nb = $prd->prd_wp_50nb;
		$data->prd_wp_63nb = $prd->prd_wp_63nb;
		$data->prd_wp_75nb = $prd->prd_wp_75nb;
		$data->prd_wp_100nb = $prd->prd_wp_100nb;
		$data->prd_wp_150nb = $prd->prd_wp_150nb;
		$data->prd_wp_200nb = $prd->prd_wp_200nb;
		$data->prd_tp_12nb = $prd->prd_tp_12nb;
		$data->prd_tp_20nb = $prd->prd_tp_20nb;
		$data->prd_tp_25nb = $prd->prd_tp_25nb;
		$data->prd_tp_32nb = $prd->prd_tp_32nb;
		$data->prd_tp_38nb = $prd->prd_tp_38nb;
		$data->prd_tp_50nb = $prd->prd_tp_50nb;
		$data->prd_tp_63nb = $prd->prd_tp_63nb;
		$data->prd_tp_75nb = $prd->prd_tp_75nb;
		$data->prd_tp_100nb = $prd->prd_tp_100nb;
		$data->prd_tp_150nb = $prd->prd_tp_150nb;
		$data->prd_tp_200nb = $prd->prd_tp_200nb;
		return $this->load->view('product_table_row_view', $data, true);
	}
	
	/**
	* Creates option values for HTML select element
	* @param string $table = table name
	* @param string $value_field = table field name to use as option value
	* @param string $text_field = table field name to use as option text
	* @param int $selected = pk value to be default selected when dropdown loads
	* @return string dropdown (select) html code. Option tags only
	*/
	function create_category_dropdown($parent_cat_id, $selected=false){
		$res = $this->prd->get_subcategories($parent_cat_id);
		$h = "<option> - </option>\n";
		
		foreach($res as $c){
			if($c->cat_id == $selected){
				$h .= "<option value='".$c->cat_id."' selected='selected'>".$c->cat_name."</option>\n";	
			}else{
				$h .= "<option value='".$c->cat_id."'>".$c->cat_name."</option>\n";	
			}
		}
		return $h;
	}
	/**
	* Ajax function - updates product field when changed
	* Receives data from $_POST
	* @return string 1 on update success/0 on fail in echoed string
	*/
	public function update_product(){
		$res = $this->prd->update_product($_POST);
		if($res === true){
			echo 1;
		}else{
			echo 0;	
		}
	}
	
	/**
	* Ajax function - deletes product
	* Receives product id from $_POST
	* @return string product table html/0 on fail in echoed string
	*/
	public function delete_product(){
		$id = $_POST['prd_id'];
		$res = $this->prd->delete_product($id);
		if($res === true){
			echo $this->build_product_table($_POST['cat_id']);
		}else{
			echo 0;	
		}
	}
	
	/**
	* Ajax function - add product
	* @return string product table html with new row/0 on fail in echoed string
	*/
	public function add_product(){
		$res = $this->prd->add_product($_POST['cat_id']);
		if($res === true){
			echo $this->build_product_table($_POST['cat_id']);
		}else{
			echo 0;	
		}
	}
}