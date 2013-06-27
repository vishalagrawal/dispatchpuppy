<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Lanes extends CI_Model {

    function __construct()
    {
        // Call the Model constructor
        parent::__construct();
    }

    function get_all_primary_and_secondary_lanes()
    {
        $this->db->where('primary_account',0);
        $primary_lanes = $this->db->get('lanes');
        
        $all_lanes = array();

        if ($primary_lanes->num_rows() > 0)
        {
            foreach ($primary_lanes->result() as $row)
            {
                $lane = array(
                    'shipper_code'      => $row->shipper_code,
                    'shipper_name'      => $row->shipper_name,
                    'shipper_address'   => $row->shipper_address,
                    'shipper_city'      => $row->shipper_city,
                    'shipper_state'     => $row->shipper_state,
                    'shipper_zipcode'   => $row->shipper_zipcode,
                    'shipper_lat'       => $row->shipper_lat,
                    'shipper_lng'       => $row->shipper_lng,
                    'consignee_code'    => $row->consignee_code,
                    'consignee_name'    => $row->consignee_name,
                    'consignee_address' => $row->consignee_address,
                    'consignee_city'    => $row->consignee_city,
                    'consignee_state'   => $row->consignee_state,
                    'consignee_zipcode' => $row->consignee_zipcode,
                    'consignee_lat'     => $row->consignee_lat,
                    'consignee_lng'     => $row->consignee_lng,
                    'commodity'         => $row->commodity,
                    'commodity_code'    => $row->commodity_code,
                    'number_of_loads'   => $row->number_of_loads,
                    'miles'             => $row->miles,
                    'secondary_lanes'   => NULL
                );
  
                $this->db->where('primary_account',$lane['consignee_code']);
                $secondary_lanes = $this->db->get('lanes');
                
                if($secondary_lanes->num_rows() > 0)
                {
                    foreach($secondary_lanes->result() as $sub_row)
                    {
                        $sub_lane = array(
                            'shipper_code'      => $sub_row->shipper_code,
                            'shipper_name'      => $sub_row->shipper_name,
                            'shipper_address'   => $sub_row->shipper_address,
                            'shipper_city'      => $sub_row->shipper_city,
                            'shipper_state'     => $sub_row->shipper_state,
                            'shipper_zipcode'   => $sub_row->shipper_zipcode,
                            'shipper_lat'       => $sub_row->shipper_lat,
                            'shipper_lng'       => $sub_row->shipper_lng,
                            'consignee_code'    => $sub_row->consignee_code,
                            'consignee_name'    => $sub_row->consignee_name,
                            'consignee_address' => $sub_row->consignee_address,
                            'consignee_city'    => $sub_row->consignee_city,
                            'consignee_state'   => $sub_row->consignee_state,
                            'consignee_zipcode' => $sub_row->consignee_zipcode,
                            'consignee_lat'     => $sub_row->consignee_lat,
                            'consignee_lng'     => $sub_row->consignee_lng,
                            'commodity'         => $sub_row->commodity,
                            'commodity_code'    => $sub_row->commodity_code,
                            'number_of_loads'   => $sub_row->number_of_loads,
                            'miles'             => $sub_row->miles
                        );
                        
                        $lane['secondary_lanes'][] = $sub_lane;
                    }
                }

                $all_lanes[$row->consignee_code.'-'.$row->commodity_code] = $lane;
            }
        }
        //var_dump($all_lanes);
        return $all_lanes;
    }
}

/* End of file lanes.php */
/* Location: ./application/models/lanes.php */