<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class annotate extends CI_Controller {
	public function __construct()
	{
		parent::__construct();
		$this->load->model('backend/image_model');
	}

	public function index()
	{
		redirect(base_url().'platform');
	}

	// Upload image for the user and save in database the record
	public function upload_image()
	{
		// redirect if image isn't present
		if(empty($_FILES['image']))
		{
			$this->session->set_flashdata('warning', 'Image isn\'t preset, try again');
			redirect(site_url().'platform');
		}
		
		// uploading image
		$config['upload_path'] = './uploads/images/';
		$config['allowed_types'] = 'gif|jpg|png';
		$config['max_size']	= '2048'; // 2MB

		$this->load->library('upload', $config);

		if (!$this->upload->do_upload('image'))
		{
		    $this->session->set_flashdata('error', $this->upload->display_errors());
		    redirect('platform');
		}

		// Saving in database
		$image_title = $this->input->post('title');
		$added_image_id = $this->image_model->add_image_for_user($this->session->userdata('user_id'), $this->upload->data(), $image_title);
		if(empty($added_image_id))
		{
			$this->session->set_flashdata('error', 'Problem adding image to database');
			redirect(base_url().'platform');
		}

		// Creating Layer for Uploaded Image
		$image_layer_id = $this->image_model->create_layer_for_image($added_image_id);

		redirect(base_url().'platform/annotate/map/'.$added_image_id.'/'.$image_layer_id);
	}

	/* Controller method that has OpenLayers Map to annotate image map */
	public function map($image_id, $layer_id=0)
	{
		// @TODO check if current user has permission to annotate the $image_id image
		
		$image_row = $this->image_model->get_image_row($image_id);
		if(empty($image_row))
		{
			$this->session->set_flashdata('warning', 'Image not found');
			redirect(base_url().'platform');
		}
		
		$data['image_id'] = $image_row->id;
		$data['first_layer_id'] = !empty($layer_id) ? $layer_id : $this->image_model->get_first_layer_id($image_row->id);
		$data['map_image_data'] = json_decode($image_row->attributes, TRUE);
		$data['main_content'] = "backend/annotation-map";

		$this->load->view('backend/templates/layout', $data);
	}

	/* Controller Method to act as REST API for OpenLayers GeoJSON HTTP Protocol Layer*/
	public function layer_protocol($image_id=0, $layer_id=0, $feature_id=0)
	{
		if(empty($image_id))
		{
			$this->output->set_status_header('401');
			die(" Image Id not provided ");
		}
		
		// Determining layer id
		if(empty($layer_id)){
			$layer_id = $this->image_model->get_first_layer_id($image_id);
		}

		$request_method = strtolower($this->input->server('REQUEST_METHOD'));
		$result = false;
		switch ($request_method) {
			case 'post':
				$inputJSONArray = json_decode(trim(file_get_contents('php://input')), true);
				$result = $this->image_model->add_layer_features($layer_id, $inputJSONArray);
				break;

			case 'put':
				$inputJSONObject = json_decode(trim(file_get_contents('php://input')), true);
				$result = $this->image_model->update_layer_feature($feature_id, $inputJSONObject);
				break;

			case 'delete':
				$inputJSONObject = json_decode(trim(file_get_contents('php://input')), true);
				$result = $this->image_model->delete_layer_feature($feature_id, $inputJSONObject);
				break;

			default:
			case 'get':
				// Expose the whole first layer as Feature Collection
				$result = $this->image_model->get_layer_feature_collection_array($image_id, $layer_id);
				break;
		}

		if($result === false)
			$this->output->set_status_header('400', 'Error occurred while performing operation.');
		
		die(json_encode($result));
	}
}

?>