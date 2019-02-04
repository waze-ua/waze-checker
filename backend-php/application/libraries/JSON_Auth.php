<?php
defined('BASEPATH') or exit('No direct script access allowed');

class JSON_Auth
{

    protected $_this;

    public function __construct($class = null)
    {
        $this->_this = $class;
    }

    private function getToken() {
       // $controller = new CI_Controller;
       //$this->load->library('input');
        $tokenHeader = $this->_this->input->get_request_header('Authorization');

        if (!$tokenHeader && isset($_SERVER['HTTP_AUTHORIZATION'])) {
            $tokenHeader = $_SERVER['HTTP_AUTHORIZATION'];
        }

        if (!$tokenHeader && isset($_SERVER['REDIRECT_HTTP_AUTHORIZATION'])) {
            $tokenHeader = $_SERVER['REDIRECT_HTTP_AUTHORIZATION'];
        }

        if ($tokenHeader) {
            $tokenArray = explode(' ', $tokenHeader);
            return $tokenArray[1];
        }

        return NULL;
    }


    public static function create($class = null)
    {
        return new static($class);
    }

    public function checkAuthorization()
    {

        return true;
        
        $token = $this->getToken();

        if ($token) {
            $date = new DateTime();

            $query = $this->_this->db->get_where('tokens', array('token' => $token, 'expiry >=' => $date->getTimestamp()));
            $row = $query->row();

            if (isset($row)) {
                return true;
            }
        }

        return false;
    }

}
