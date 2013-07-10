<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Map_combinations extends CI_Controller {

	public function index()
	{
		
		// get the data from the lanes table
		$this->load->model('Lanes');
		$all_lanes = $this->Lanes->get_all_primary_and_secondary_lanes();

		
		// get the co-ordianted to center the google map from the locations table
		$this->load->model('Locations');
		$google_map_center_location = $this->Locations->get_google_map_center_location();

		//create array to send to view
		$data = array(
			'title' 					  => 'All Lanes',
			'google_map_center_location'  => $google_map_center_location,
			'all_lanes' 			 	  => $all_lanes
		);

		//var_dump($data);

		// load the data in the view
		$this->load->view('all_combinations',$data); 
	}
}

/* End of file map_combinations.php */
/* Location: ./application/controllers/map_combinations.php */