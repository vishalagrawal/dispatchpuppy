<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Lanes extends CI_Model {

    function __construct()
    {
        // Call the Model constructor
        parent::__construct();
    }

    function get_lane()
    {
        $query = $this->db->get('lanes');
        
        $all_lanes = array();

        if ($query->num_rows() > 0)
        {
            foreach ($query->result() as $row)
            {
                $lane = array(
                    'bill_to_code'      => $row->bill_to_code,
                    'bill_to_name'      => $row->bill_to_name,
                    'bill_to_address'   => $row->bill_to_address,
                    'bill_to_city'      => $row->bill_to_city,
                    'bill_to_state'     => $row->bill_to_state,
                    'bill_to_zipcode'   => $row->bill_to_zipcode,
                    'shipper_code'      => $row->shipper_code,
                    'shipper_name'      => $row->shipper_name,
                    'shipper_address'   => $row->shipper_address,
                    'shipper_city'      => $row->shipper_city,
                    'shipper_state'     => $row->shipper_state,
                    'shipper_zipcode'   => $row->shipper_zipcode,
                    'consignee_code'    => $row->consignee_code,
                    'consignee_name'    => $row->consignee_name,
                    'consignee_address' => $row->consignee_address,
                    'consignee_city'    => $row->consignee_city,
                    'consignee_state'   => $row->consignee_state,
                    'consignee_zipcode' => $row->consignee_zipcode,
                    'commodity'         => $row->commodity,
                    'commodity_code'    => $row->commodity_code,
                    'number_of_loads'   => $row->number_of_loads,
                    'miles'             => $row->miles
                );
                
                $all_lanes[] = $lane;
            }
        }
        return $all_lanes;
    }
}

/* End of file lanes.php */
/* Location: ./application/models/lanes.php */