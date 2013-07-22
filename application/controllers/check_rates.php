<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Check_rates extends CI_Controller {

	public function index()
	{
		
		// get the data from the lanes table
		$this->load->model('Detail_from_tmw');
		$lanes_detail = $this->Detail_from_tmw->get_lanes_detail();

		// create array to send to view
		$data = array(
			'title' 		  => 'Check Rates',
			'lanes_detail' 	  => $lanes_detail
		);
		
		// load the data in the view
		$this->load->view('display_rates',$data); 
	}

	public function display_summary()
	{
		// get the data from the lanes table
		$this->load->model('Detail_from_tmw');
		$all_lanes = $this->Detail_from_tmw->get_lanes_summary();

		// create array to send to view
		$data = array(
			'title' 		  => 'Lanes Summary',
			'all_lanes' 	  => $all_lanes
		);
		
		// load the data in the view
		$this->load->view('display_summary',$data); 
	}

	public function display_all_customers()
	{
		// get the data from the lanes table
		$this->load->model('Detail_from_tmw');
		$all_customers = $this->Detail_from_tmw->get_all_active_customers();

		// create array to send to view
		$data = array(
			'title' 		  => 'All Customers',
			'all_customers'   => $all_customers
		);
		
		// load the data in the view
		$this->load->view('display_all_customers',$data); 
	}

}

/* End of file check_rates.php */
/* Location: ./application/controllers/check_rates.php */