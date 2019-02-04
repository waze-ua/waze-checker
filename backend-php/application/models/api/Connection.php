<?php
defined('BASEPATH') or exit('No direct script access allowed');

require_once APPPATH . 'libraries/JSON_Model.php';

class Connection extends JSON_Model
{

    public function __construct()
    {
        parent::__construct();
    }

    public $name = 'connection';


}
