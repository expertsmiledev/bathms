<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Asset extends MY_Controller {

	public function __construct(){
		parent::__construct();
		$this->load->model('Asset_model', 'asset');
	}
	
	public function index(){
		$this->load->view('view');
	}
	
	public function update_asset(){
		echo $this->asset->update_asset($_POST);
	}
}