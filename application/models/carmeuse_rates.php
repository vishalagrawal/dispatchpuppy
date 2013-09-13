<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Carmeuse_rates extends CI_Model {

    function __construct()
    {
        // Call the Model constructor
        parent::__construct();
    }

    /*
     *
     *  get all the lanes from the details_from_tmw database
     *
     */
    function get_lanes()
    {
        // get all the distinct lane_ids from the database
        $this->db->select('*');
        $this->db->from('carmeuse_rates');
        $this->db->order_by('shipper_zipcode','asc');
        $unique_lanes = $this->db->get();

        $all_lanes = array();

        foreach($unique_lanes->result() as $particular_lane)
        {
            $lane = array(
                'lane_id'           => $particular_lane->lane_id,
                'shipper_address'   => $particular_lane->shipper_address,
                'shipper_city'      => $particular_lane->shipper_city,
                'shipper_state'     => $particular_lane->shipper_state,
                'shipper_zipcode'   => $particular_lane->shipper_zipcode,
                'consignee_name'    => $particular_lane->consignee_name,
                'consignee_city'    => $particular_lane->consignee_city,
                'consignee_state'   => $particular_lane->consignee_state,
                'consignee_zipcode' => $particular_lane->consignee_zipcode,
                'rate'              => $particular_lane->rate,
                'miles'              => $particular_lane->miles,
            );
            $all_lanes[$particular_lane->lane_id] = $lane;
        }

        return $all_lanes;
    }
}

/* End of file carmeuse_rates.php */
/* Location: ./application/models/carmeuse_rates.php */