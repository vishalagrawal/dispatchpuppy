<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Detail_from_tmw extends CI_Model {

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
    function get_lanes_detail()
    {
        // get all the distinct lane_ids from the database
        $this->db->select(array('lane_id','COUNT(lane_id) as number_of_lanes'));
        $this->db->from('detail_from_tmw');
        $this->db->where('bill_date <', '2013-01-01');
        $this->db->like('bill_to_name','Carmeuse');
        $this->db->group_by('lane_id'); 
        $this->db->order_by('number_of_lanes','asc'); //total = 628
        //$this->db->limit(300); //set1
        //$this->db->limit(200,300); //set2
        //$this->db->limit(20,500); //set3
        //$this->db->limit(22,520); //set4
        $unique_lane_ids = $this->db->get();
        $all_lanes = array();

        //echo $unique_lane_ids->num_rows();

        if ($unique_lane_ids->num_rows() > 0)
        {
            foreach ($unique_lane_ids->result() as $ids)
            {  
                // get all the lanes with the same lane_id
                $this->db->select(array(
                    'bill_date',
                    'trip_number',
                    'bill_to_code',
                    'bill_to_name',
                    'bill_to_city',
                    'bill_to_state',
                    'shipper_name',
                    'shipper_city',
                    'shipper_state',
                    //'shipper_zipcode',
                    'consignee_name',  
                    'consignee_city',  
                    'consignee_state',
                    //'consignee_zipcode',  
                    'commodity_code', 
                    'weight',
                    'rate',         
                    'driver_pay',       
                    'distance'));

                $this->db->from('detail_from_tmw');
                $this->db->where('lane_id',$ids->lane_id);
                $this->db->order_by('bill_date','asc');
                $all_particular_lanes = $this->db->get();

                $particular_lane = array();

                if($all_particular_lanes->num_rows() > 0)
                {
                    foreach ($all_particular_lanes->result() as $row)
                    {
                        // create lane
                        $lane = array(
                            'bill_date'         => $row->bill_date,
                            'trip_number'       => $row->trip_number,
                            'bill_to_code'      => $row->bill_to_code,
                            'bill_to_name'      => $row->bill_to_name,
                            'bill_to_city'      => $row->bill_to_city,
                            'bill_to_state'     => $row->bill_to_state,
                            'shipper_name'      => $row->shipper_name,
                            'shipper_city'      => $row->shipper_city,
                            'shipper_state'     => $row->shipper_state,
                            //'shipper_zipcode'   => $row->shipper_zipcode,
                            'consignee_name'    => $row->consignee_name,
                            'consignee_city'    => $row->consignee_city,
                            'consignee_state'   => $row->consignee_state,
                            //'consignee_zipcode' => $row->consignee_zipcode,
                            'commodity_code'    => $row->commodity_code,
                            'weight'            => $row->weight,
                            'rate'              => $row->rate,
                            'driver_pay'        => $row->driver_pay,
                            'distance'          => $row->distance,
                        );
                        
                        $particular_lane[] = $lane;

                    }
                }

                $all_lanes[$ids->lane_id] = $particular_lane;
            }
            
        }
        //var_dump($all_lanes);
        return $all_lanes;
    }

    /*
     *
     * get all the suammry of lanes from the details_from_tmw database
     *
     */
    function get_lanes_summary()
    {
        // get all the distinct lane_ids from the database
        $this->db->select(array('lane_id','COUNT(lane_id) AS number_of_loads'));
        $this->db->from('detail_from_tmw');
        $this->db->group_by('lane_id'); 
        $this->db->order_by('number_of_loads','desc');
        $unique_lane_ids = $this->db->get();
        
        $all_lanes = array();

        if ($unique_lane_ids->num_rows() > 0)
        {
            foreach ($unique_lane_ids->result() as $ids)
            {  
                // get all the lanes with the same lane_id
                $this->db->select('*');
                $this->db->from('detail_from_tmw');
                $this->db->where('lane_id',$ids->lane_id);
                $this->db->limit(1);
                $lane_info = $this->db->get();

                if($lane_info->num_rows() > 0)
                {
                    foreach ($lane_info->result() as $row)
                    {
                        // create lane
                        $lane = array(
                            'trip_number'       => $row->trip_number,
                            'bill_to_code'      => $row->bill_to_code,
                            'bill_to_name'      => $row->bill_to_name,
                            'bill_to_city'      => $row->bill_to_city,
                            'bill_to_state'     => $row->bill_to_state,
                            'shipper_code'      => $row->shipper_code,
                            'shipper_name'      => $row->shipper_name,
                            'shipper_city'      => $row->shipper_city,
                            'shipper_state'     => $row->shipper_state,
                            'consignee_code'    => $row->consignee_code,
                            'consignee_name'    => $row->consignee_name,
                            'consignee_city'    => $row->consignee_city,
                            'consignee_state'   => $row->consignee_state,
                            'commodity_code'    => $row->commodity_code,
                            'rate'              => $row->rate,
                            'rate_unit'         => $row->rate_unit,
                            'driver_pay'        => $row->driver_pay,
                            'distance'          => $row->distance,
                            'distance_unit'     => $row->distance_unit,
                            'number_of_loads'   => $ids->number_of_loads
                        );

                        $all_lanes[$ids->lane_id] = $lane;
                    }
                }       
            }
            
        }
        //var_dump($all_lanes);
        return $all_lanes;
    }

    /*
     *
     * get a list of all the active customers from details_from_tmw database
     *
     */
    function get_all_active_customers()
    {
        // get all active bill_to_customers from the database
        $this->db->select(array('bill_to_code','bill_to_name','bill_to_address','bill_to_city','bill_to_state','bill_to_zipcode','SUM(charges) AS sum','SUM(accessorial_charges) AS accessorial_sum'));
        $this->db->from('detail_from_tmw');
        $this->db->group_by('bill_to_code'); 
        $this->db->order_by('bill_to_name','asc');
        $unique_bill_to_customers = $this->db->get();
        
        $all_customers = array();
        //$all_bill_to_customers = array();

        if ($unique_bill_to_customers->num_rows() > 0)
        {
            foreach ($unique_bill_to_customers->result() as $row)
            {  
                
                if(array_key_exists($row->bill_to_code,$all_customers))
                {
                    $all_customers[$row->bill_to_code]['bill_to'] = TRUE;
                }
                else
                {
                    // create lane
                    $bill_to_customer = array(
                        'code'      => $row->bill_to_code,
                        'name'      => $row->bill_to_name,
                        'address'   => $row->bill_to_address,
                        'city'      => $row->bill_to_city,
                        'state'     => $row->bill_to_state,
                        'zipcode'   => $row->bill_to_zipcode,
                        'bill_to'   => TRUE,
                        'bill_to_revenue' => $row->sum+$row->accessorial_sum
                    );

                    $all_customers[$row->bill_to_code] = $bill_to_customer;
                }
            }
        }
        
        // get all active shippers from the database
        $this->db->select(array('shipper_code','shipper_name','shipper_address','shipper_city','shipper_state','shipper_zipcode','SUM(charges) AS sum','SUM(accessorial_charges) AS accessorial_sum'));
        $this->db->from('detail_from_tmw');
        $this->db->group_by('shipper_code'); 
        $this->db->order_by('shipper_name','asc');
        $unique_shipper_customers = $this->db->get();

        if ($unique_shipper_customers->num_rows() > 0)
        {
            foreach ($unique_shipper_customers->result() as $row)
            {  
                if(array_key_exists($row->shipper_code,$all_customers))
                {
                    $all_customers[$row->shipper_code]['shipper'] = TRUE;
                    $all_customers[$row->shipper_code]['shipper_revenue'] = $row->sum+$row->accessorial_sum;
                }
                else
                {
                    // create lane
                    $shipper_customer = array(
                        'code'      => $row->shipper_code,
                        'name'      => $row->shipper_name,
                        'address'   => $row->shipper_address,
                        'city'      => $row->shipper_city,
                        'state'     => $row->shipper_state,
                        'zipcode'   => $row->shipper_zipcode,
                        'shipper'   => TRUE,
                        'shipper_revenue' => $row->sum+$row->accessorial_sum
                    );

                    $all_customers[$row->shipper_code] = $shipper_customer;
                }
            }
        }

        // get all active consignee from the database
        $this->db->select(array('consignee_code','consignee_name','consignee_address','consignee_city','consignee_state','consignee_zipcode','SUM(charges) AS sum','SUM(accessorial_charges) AS accessorial_sum'));
        $this->db->from('detail_from_tmw');
        $this->db->group_by('consignee_code'); 
        $this->db->order_by('consignee_name','asc');
        $unique_consignee_customers = $this->db->get();

        if ($unique_consignee_customers->num_rows() > 0)
        {
            foreach ($unique_consignee_customers->result() as $row)
            {  
                if(array_key_exists($row->consignee_code,$all_customers))
                {
                    $all_customers[$row->consignee_code]['consignee'] = TRUE;
                    $all_customers[$row->consignee_code]['consignee_revenue'] = $row->sum+$row->accessorial_sum;
                }
                else
                {
                    // create lane
                    $consignee_customer = array(
                        'code'      => $row->consignee_code,
                        'name'      => $row->consignee_name,
                        'address'   => $row->consignee_address,
                        'city'      => $row->consignee_city,
                        'state'     => $row->consignee_state,
                        'zipcode'       => $row->consignee_zipcode,
                        'consignee' => TRUE,
                        'consignee_revenue' => $row->sum+$row->accessorial_sum
                    );

                    $all_customers[$row->consignee_code] = $consignee_customer;
                }
            }
        }
        //var_dump($all_customers);
        return $all_customers;
    }

    /*
     *
     * 
     * get all the unique commodities
     *
     */
    function get_all_commodities()
    {
        // get all the distinct lane_ids from the database
        $this->db->select(array('detail_from_tmw.commodity_code','COUNT(detail_from_tmw.commodity_code) as commoddity_frequency','commodity.commodity'));
        $this->db->from('detail_from_tmw');
         $this->db->join('commodity', 'detail_from_tmw.commodity_code = commodity.commodity_code','left');
        $this->db->group_by('commodity_code');
        $this->db->order_by('commoddity_frequency','desc');
        $this->db->limit(1);
        $unique_commodities = $this->db->get();
        
        $all_commodities = array();

        if ($unique_commodities->num_rows() > 0)
        {
            foreach ($unique_commodities->result() as $row)
            {  
                // create array
                $commodity_detail = array(
                    'commodity_code'        => $row->commodity_code,
                    'commodity'             => $row->commodity,
                    'commodity_frequency'  => $row->commoddity_frequency
                );

                $all_commodities[] = $commodity_detail;
            }
        }
        return $all_commodities;
    }

    /*
     *
     * 
     * get all the unique commodities and their top ten weights with the size of the trailer
     *
     */
    function get_all_commodities_and_top_ten_weights_with_trailer()
    {
        // get all the distinct trailer cubes from the database
        $this->db->select('trailer_cube');
        $this->db->from('trailers');
        $this->db->group_by('trailer_cube');
        $this->db->order_by('trailer_cube','desc');
        $unique_trailer_cube_size = $this->db->get();

        $all_trailer_cube_sizes = array();

        if($unique_trailer_cube_size->num_rows > 0)
        {
            foreach($unique_trailer_cube_size->result() as $row)
            {
                $all_trailer_cube_sizes[] = $row->trailer_cube;
            }
        } 
        

        // get all the distinct lane_ids from the database
        $this->db->select(array('detail_from_tmw.commodity_code','COUNT(detail_from_tmw.commodity_code) as commoddity_frequency','commodity.commodity'));
        $this->db->from('detail_from_tmw');
        $this->db->join('commodity', 'detail_from_tmw.commodity_code = commodity.commodity_code','left');
        $this->db->group_by('commodity_code');
        $this->db->order_by('commoddity_frequency','desc');
        $unique_commodities = $this->db->get();
        
        $all_commodities = array();

        if($unique_commodities->num_rows() > 0)
        {
            foreach($unique_commodities->result() as $row)
            {  
                                // create array
                $commodity_detail = array(
                    'commodity_code'        => $row->commodity_code,
                    'commodity_name'        => $row->commodity,
                    'commodity_frequency'   => $row->commoddity_frequency,
                    'commodity_info'        => null
                );

                foreach($all_trailer_cube_sizes as $size)
                {
                    $commodity_detail['commodity_info'][$size] = null;

                    $this->db->select(array('bill_date','trip_number','weight','tractor','trailer','trailer_cube','driver_id'));
                    $this->db->from('detail_from_tmw');
                    $this->db->where('commodity_code',$commodity_detail['commodity_code']);
                    $this->db->where('trailer_cube',$size);
                    $this->db->distinct();
                    $this->db->join('trailers', 'detail_from_tmw.trailer = trailers.trailer_number','left');
                    $this->db->order_by('weight','desc');
                    $this->db->limit(10);
                    $all_commodity_info = $this->db->get();

                    $commodity_info = array();

                    if($all_commodity_info->num_rows() > 0)
                    {
                        foreach($all_commodity_info->result() as $row)
                        { 
                            $unique_commodity_info = array(
                                'bill_date'     => $row->bill_date,
                                'trip_number'   => $row->trip_number,
                                'weight'        => $row->weight,
                                'tractor'       => $row->tractor,
                                'trailer'       => $row->trailer,
                                'trailer_cube'  => $row->trailer_cube,
                                'driver_id'     => $row->driver_id
                            );

                            $commodity_info[] = $unique_commodity_info;
                        }
                    }

                    $commodity_detail['commodity_info'][$size] = $commodity_info;
                }

                $all_commodities[] = $commodity_detail;
            }
        }
        //var_dump($all_commodities);
        return $all_commodities;
        //return null;
    }

}

/* End of file detail_from_tmw.php */
/* Location: ./application/models/detail_from_tmw.php */