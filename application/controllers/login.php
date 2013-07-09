<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Login extends CI_Controller {

	public function index()
	{
		$this->load->view('login');
	}

	public function verify_login()
	{
		$this->load->model('users','get_user_details');
		$this->users->my_constructor->('hi');
	}
}

/* End of file login.php */
/* Location: ./application/controllers/login.php */