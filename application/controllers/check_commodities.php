<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Check_commodities extends CI_Controller {

	public function index()
	{
		
		// get the data from the lanes table
		$this->load->model('Detail_from_tmw');
		$all_commodities = $this->Detail_from_tmw->get_all_commodities_and_top_ten_weights_with_trailer();

		// create array to send to view
		$data = array(
			'title' 		  	=> 'Check Commodities',
			'all_commodities' 	=> $all_commodities
		);
		
		// load the data in the view
		$this->load->view('display_commodities',$data);
	}	
}

/* End of file check_rates.php */
/* Location: ./application/controllers/check_rates.php */