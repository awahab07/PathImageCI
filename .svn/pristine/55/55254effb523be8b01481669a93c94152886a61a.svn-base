<?php
class Admin_model extends CI_Model
{
	public function __construct()
	{
		parent::__construct();
	}
	
	public function do_login()
	{
		$password = $_POST['password'];
		$array = array(
						'username' => mysql_real_escape_string($this->input->post('username', TRUE)),
						'password' => md5 ( $password )
					);
		$this->db->select('id, username');
		$this->db->where($array); 
		$query = $this->db->get('users');
		if($query->num_rows())
			return $query->row();
		else
			return false;
	}
}

?>