<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Detail_from_tmw extends CI_Model {

    function __construct()
    {
        // Call the Model constructor
        parent::__construct();
    }

    function get_lanes_detail()
    {
        // get all the distinct lane_ids from the database
        $this->db->select(array('lane_id','COUNT(lane_id) as number_of_lanes'));
        $this->db->from('detail_from_tmw');
        $this->db->group_by('lane_id'); 
        $this->db->order_by('number_of_lanes','asc'); //total = 542
        //$this->db->limit(300); //set1
        //$this->db->limit(200,300); //set2
        //$this->db->limit(20,500); //set3
        $this->db->limit(22,520); //set4

        $unique_lane_ids = $this->db->get();

        //echo $unique_lane_ids->num_rows();
        $all_lanes = array();
        
        if ($unique_lane_ids->num_rows() > 0)
        {
            foreach ($unique_lane_ids->result() as $ids)
            {  
                // get all the lanes with the same lane_id
                $this->db->select(array(
                    'bill_date',
                    'trip_number',
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
                    'commodity_code', 
                    //'consignee_zipcode',
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

}

/* End of file detail_from_tmw.php */
/* Location: ./application/models/detail_from_tmw.php */