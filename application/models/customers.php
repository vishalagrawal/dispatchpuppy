<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Customers extends CI_Model {

    function __construct()
    {
        // Call the Model constructor
        parent::__construct();
    }

    function get_all_customers_without_lat_lng()
    {
        // get all the primary_lanes from the database and the relevant commodities
        $this->db->select('*');
        $this->db->from('customers');
        $this->db->where('lat',0);
        $this->db->or_where('lng',0);
        $this->db->order_by('name','asc');
        $selected_customers = $this->db->get();
        
        $customers_without_lat_lng = array();

        if ($selected_customers->num_rows() > 0)
        {
            foreach ($selected_customers->result() as $row)
            {
                // create lane_id
                $customer_id = $row->code;

                // create lane
                $customer = array(
                    'code'      => $row->code,
                    'name'      => $row->name,
                    'address'   => $row->address,
                    'city'      => $row->city,
                    'state'     => $row->state,
                    'zipcode'   => $row->zipcode,
                    'lat'       => $row->lat,
                    'lng'       => $row->lng    
                );

                $customers_without_lat_lng[$customer_id] = $customer;
            }
        }
        return $customers_without_lat_lng;
    }

    function update_customer_lat_lng($customer_id, $lat_lng)
    {
        $this->db->where('code',$customer_id);
        $this->db->where('lat', 0);
        $this->db->where('lng', 0);
        
        if($this->db->update('customers',$lat_lng) > 0)
        {
            return 'updated';
        }
        else
        {
            return 'failed';
        }
    }
}

/* End of file customers.php */
/* Location: ./application/models/customers.php */