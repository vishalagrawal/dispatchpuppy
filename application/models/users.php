<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Users extends CI_Model {

    var $title   = '';
    var $content = '';
    var $date    = '';

    function __construct()
    {
        // Call the Model constructor
        parent::__construct();
    }
    
    function get_user_details()
    {
        $query = $this->db->get('entries', 10);
        return $query->result();
    }
}

/* End of file users.php */
/* Location: ./application/models/users.php */