<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Get_customer_location extends CI_Controller {

	public function index()
	{
		// get the customers from the customers table without Lat & Lng
		$this->load->model('Customers');
		$customers_without_lat_lng = $this->Customers->get_all_customers_without_lat_lng();

		
		// get the co-ordianted to center the google map from the locations table
		$this->load->model('Locations');
		$google_map_center_location = $this->Locations->get_google_map_center_location();

		//create array to send to view
		$data = array(
			'title' 					  => 'Get Location',
			'google_map_center_location'  => $google_map_center_location,
			'customers_without_lat_lng'	  => $customers_without_lat_lng
		);
		
		// load the data in the view
		$this->load->view('get_customer_location',$data); 
	}

	public function update_customer_lat_lng($customer_id, $lat, $lng)
	{
		$lat_lng = array(
			'lat'	=> $lat,
			'lng'	=> $lng
		);

		$this->load->model('Customers');
		$return_value = $this->Customers->update_customer_lat_lng($customer_id, $lat_lng);

		echo $return_value;
	}
}

/* End of file get_customer_location.php */
/* Location: ./application/controllers/get_customer_location.php */