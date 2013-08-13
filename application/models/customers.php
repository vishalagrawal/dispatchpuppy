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

    function get_all_consignees_and_inbound_shippers($type)
    {
        $code = $type.'_code';

        if($type === 'consignee')
        {
            $secondary_code = 'shipper_code';
        }
        else if($type === 'shipper')
        {
            $secondary_code = 'consignee_code';
        }

        // get all primary
        $this->db->select(array($code,'name','city','state','lat','lng','COUNT('.$code.') AS number_of_loads','commodity_id','detail_from_tmw.commodity_code','commodity'));
        $this->db->from('detail_from_tmw');
        $this->db->where('state','MD');
        $this->db->join('customers', 'detail_from_tmw.'.$code.' = customers.code','left');
        $this->db->join('commodity', 'detail_from_tmw.commodity_code = commodity.commodity_code','left');
        $this->db->group_by(array($code,'detail_from_tmw.commodity_code'));
        $this->db->order_by('number_of_loads','desc');
        $all_primary = $this->db->get();

        $all_combinations = array();
        if ($all_primary->num_rows() > 0)
        {
            foreach($all_primary->result() as $primary)
            {
                $primary_info = array(
                    'primary_name'            => $primary->name,
                    'primary_city'            => $primary->city,
                    'primary_state'           => $primary->state,
                    'primary_lat'             => $primary->lat,
                    'primary_lng'             => $primary->lng,
                    'primary_commodity_code'  => $primary->commodity_code,
                    'primary_commodity'       => $primary->commodity,
                    'number_of_loads'         => $primary->number_of_loads,
                    'secondary'               => null
                );
      
                $this->db->select(array($secondary_code,'name','city','state','lat','lng','commodity_id','detail_from_tmw.commodity_code','commodity','distance','COUNT(lane_id) AS number_of_loads'));
                $this->db->from('detail_from_tmw');
                $this->db->where($code,$primary->$code);
                $this->db->where('detail_from_tmw.commodity_code',$primary->commodity_code);
                $this->db->join('customers', 'detail_from_tmw.'.$secondary_code.' = customers.code','left');
                $this->db->join('commodity', 'detail_from_tmw.commodity_code = commodity.commodity_code','left');
                $this->db->group_by('lane_id');
                $this->db->order_by('number_of_loads','desc');
                $all_secondary = $this->db->get();

                if($all_secondary->num_rows() > 0)
                {
                    foreach($all_secondary->result() as $secondary)
                    {
                        $secondary_info = array(
                            'secondary_name'            => $secondary->name,
                            'secondary_city'            => $secondary->city,
                            'secondary_state'           => $secondary->state,
                            'secondary_lat'             => $secondary->lat,
                            'secondary_lng'             => $secondary->lng,
                            'secondary_commodity_code'  => $secondary->commodity_code,
                            'secondary_commodity'       => $secondary->commodity,
                            'miles'                     => $secondary->distance,
                            'number_of_loads'           => $secondary->number_of_loads
                        );

                        $primary_info['secondary'][$secondary->$secondary_code.'-'.$secondary->commodity_id] = $secondary_info;
                    }
                }

                $all_combinations[$primary->$code.'-'.$primary->commodity_id] = $primary_info;
            }
        }
        return $all_combinations;
    }
}

/* End of file customers.php */
/* Location: ./application/models/customers.php */