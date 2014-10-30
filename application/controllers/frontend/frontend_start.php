<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class frontend_start extends CI_Controller {
	var $templates_locator = 'frontend/templates/layout'; 

	public function index()
	{
		$data['main_content'] = "frontend/start";
		$this->load->view($this->templates_locator,$data);
	}

	public function login()
	{
		echo "test";
	}
}

?>