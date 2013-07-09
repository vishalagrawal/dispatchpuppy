<?php header('Access-Control-Allow-Origin: *'); ?>
<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Get_location extends CI_Controller {

	public function index()
	{
		
		// get the data from the lanes table
		$this->load->model('Lanes');
		$lanes_without_lat_lng = $this->Lanes->get_all_lanes_without_lat_lng();

		
		// get the co-ordianted to center the google map from the locations table
		$this->load->model('Locations');
		$google_map_center_location = $this->Locations->get_google_map_center_location();

		//create array to send to view
		$data = array(
			'title' 					  => 'Get Location',
			'google_map_center_location'  => $google_map_center_location,
			'lanes_without_lat_lng' 	  => $lanes_without_lat_lng
		);

		// load the data in the view
		$this->load->view('getLocationCoordinates',$data); 
	}
}

/* End of file lanes.php */
/* Location: ./application/controllers/lanes.php */