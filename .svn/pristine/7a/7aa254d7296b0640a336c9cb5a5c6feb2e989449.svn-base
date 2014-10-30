<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class backend_start extends CI_Controller {
	public function __construct()
	{
		parent::__construct();
		if(!$this->session->userdata('logged_in'))
			redirect('platform/login');
	}

	public function index()
	{
		$this->load->model('backend/image_model');
		$data['user_images'] = $this->image_model->get_user_images($this->session->userdata('user_id'));
		$data['main_content'] = "backend/start";
		$this->load->view('backend/templates/layout', $data);
	}

	/* Controller method that has OpenLayers Map to annotate image map */
	public function annotate()
	{
		$data['main_content'] = "backend/annotate";
		$this->load->view('backend/templates/layout', $data);
	}
}

?>