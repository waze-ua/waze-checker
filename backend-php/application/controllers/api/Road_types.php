<?php
defined('BASEPATH') or exit('No direct script access allowed');
require_once APPPATH . 'controllers/Json_api.php';

class Road_types extends Json_api
{
    public $modelName = 'road_type';

    public function __construct()
    {
        parent::__construct();
    }
}
