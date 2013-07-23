<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Map_inbound extends CI_Controller {

	public function index()
	{
		// get the data from the lanes table
		$this->load->model('Customers');
		$all_combinations = $this->Customers->get_all_consignees_and_inbound_shippers();

		// get the co-ordianted to center the google map from the locations table
		$this->load->model('Locations');
		$google_map_center_location = $this->Locations->get_google_map_center_location();

		// create array to send to view
		$data = array(
			'title' 		  	=> 'Map Inbound',
			'google_map_center_location'  => $google_map_center_location,
			'all_combinations' 	=> $all_combinations
		);
		
		// load the data in the view
		$this->load->view('map_inbound',$data); 
	}
}

/* End of file map_inbound.php */
/* Location: ./application/controllers/map_inbound.php */