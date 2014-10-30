<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Login extends CI_Controller {

	public function __construct()
	{
		parent::__construct();
		
		$this->load->model('backend/admin_model');
	}
	
	public function index()
	{
		$data['main_content'] = 'backend/login';
		$this->load->view('backend/templates/layout', $data);
	}
	
	public function login_process()
	{
		$login_result = $this->admin_model->do_login();
		
		if(!empty($login_result))
		{
			$userLogindata = array(
				'user_id'  => $login_result->id,
				'username'  => $login_result->username,
				'logged_in' => TRUE
			);
			
			$this->session->set_userdata($userLogindata);
			redirect(base_url().'platform');
			exit();			
		}
		else
		{
			$this->session->set_flashdata('error', '<strong>Please!</strong> provide correct username/password!');
			redirect(base_url().'platform/login');
			exit();
		}
	}

	// If user not logged in, provides a form and after successfull login, redirects from where the user came
	public function force_login()
	{

	}

	public function logout()
	{
		$userLogOut = array(
				'user_id'  => '',
				'username'  => '',
				'logged_in' => FALSE
				);
		$this->session->unset_userdata($userLogOut);
		$this->session->set_flashdata('message', 'You are <strong>logged out</strong> successfully!');
		redirect(base_url().'platform');
		exit();
	}
	
}