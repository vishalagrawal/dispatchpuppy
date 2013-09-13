<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Get_carmeuse_location_miles extends CI_Controller {

	public function index()
	{
		// get the data from the lanes table
		$this->load->model('Carmeuse_rates');
		$all_lanes = $this->Carmeuse_rates->get_lanes();

		// create array to send to view
		$data = array(
			'title' 		=>	'Carmeuse Rates',
			'all_lanes' 	=>	$all_lanes
		);
		
		// load the data in the view
		$this->load->view('display_carmeuse_rates',$data);
	}	
}

/* End of file get_carmeuse_location_miles.php */
/* Location: ./application/controllers/get_carmeuse_location_miles.php */