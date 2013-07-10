<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Add_lat_lng extends CI_Controller {

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
		$this->load->view('get_location',$data); 
	}

	public function update_lat_lng($lane_id, $customer_type, $lat, $lng)
	{
		
		$info = explode('-',$lane_id);

		$data = array(
			'customer_type'	 => $customer_type,
			'shipper_code'   => $info[0],
			'consignee_code' => $info[1],
			'commodity_code' => str_replace('%20', ' ', $info[2]),
			'lat' 			 => $lat,
			'lng'			 => $lng
		);

		$this->load->model('Lanes');
		$return_value = $this->Lanes->update_lat_lng($data);

		echo $return_value;
	}
}

/* End of file add_lat_lng.php */
/* Location: ./application/controllers/add_lat_lng.php */