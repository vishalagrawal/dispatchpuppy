<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Map extends CI_Controller {

	public function index()
	{
		
		// get the data from the model

		$this->load->model('Lanes');
		$query = $this->Lanes->get_lane();

		//create array to send to view
		$data = array(
			'title' => 'All Lanes',
			'all_lanes' => $query
		);

		// load the data in the view
		$this->load->view('all_lanes',$data); 
	}
}

/* End of file lanes.php */
/* Location: ./application/controllers/lanes.php */