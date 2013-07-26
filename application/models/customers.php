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

    function get_all_consignees_and_inbound_shippers()
    {
        $this->db->select(array('consignee_code','name','city','state','lat','lng','COUNT(consignee_code) AS number_of_loads'));
        $this->db->from('detail_from_tmw');
        $this->db->join('customers', 'detail_from_tmw.consignee_code = customers.code','left');
        $this->db->group_by('consignee_code');
        $this->db->order_by('number_of_loads','desc');
        $this->db->limit(50);
        $all_consignees = $this->db->get();

        $all_combinations = array();
        if ($all_consignees->num_rows() > 0)
        {
            foreach($all_consignees->result() as $consignee)
            {
                $combination = array(
                    'consignee_name'  => $consignee->name,
                    'consignee_city'  => $consignee->city,
                    'consignee_state' => $consignee->state,
                    'consignee_lat'   => $consignee->lat,
                    'consignee_lng'   => $consignee->lng,
                    'number_of_loads' => $consignee->number_of_loads,
                    'shippers'        => null
                );
      
                $this->db->select(array('shipper_code','name','city','state','lat','lng','commodity_id','detail_from_tmw.commodity_code','commodity','distance','COUNT(lane_id) AS number_of_loads'));
                $this->db->from('detail_from_tmw');
                $this->db->where('consignee_code',$consignee->consignee_code);
                $this->db->join('customers', 'detail_from_tmw.shipper_code = customers.code','left');
                $this->db->join('commodity', 'detail_from_tmw.commodity_code = commodity.commodity_code','left');
                $this->db->group_by('lane_id');
                $this->db->order_by('number_of_loads','desc');
                $all_consignee_shippers = $this->db->get();

                if($all_consignee_shippers->num_rows() > 0)
                {
                    foreach($all_consignee_shippers->result() as $consignee_shipper)
                    {
                        //var_dump($consignee_shipper);
                        $shipper = array(
                            'shipper_name'      => $consignee_shipper->name,
                            'shipper_city'      => $consignee_shipper->city,
                            'shipper_state'     => $consignee_shipper->state,
                            'shipper_lat'       => $consignee_shipper->lat,
                            'shipper_lng'       => $consignee_shipper->lng,
                            'commodity_code'    => $consignee_shipper->commodity_code,
                            'commodity'         => $consignee_shipper->commodity,
                            'miles'             => $consignee_shipper->distance,
                            'number_of_loads'   => $consignee_shipper->number_of_loads
                        );

                        $combination['shippers'][$consignee_shipper->shipper_code.'-'.$consignee_shipper->commodity_id] = $shipper;
                    }
                }

                $all_combinations[$consignee->consignee_code] = $combination;
            }
        }
        //var_dump($all_combinations);
        return $all_combinations;
    }
}

/* End of file customers.php */
/* Location: ./application/models/customers.php */