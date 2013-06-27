<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Locations extends CI_Model {

    function __construct()
    {
        // Call the Model constructor
        parent::__construct();
    }

    function get_google_map_center_location()
    {
        $query = $this->db->get('locations');

        if ($query->num_rows() > 0)
        {
            foreach ($query->result() as $row)
            {
                $google_map_center_location = array(
                    'location_name'  => $row->location_name,
                    'location_lat'   => $row->location_lat,
                    'location_lng'   => $row->location_lng,
                );
            }
        }
        return $google_map_center_location;
    }
}

/* End of file lanes.php */
/* Location: ./application/models/lanes.php */