<?php
defined('BASEPATH') or exit('No direct script access allowed');

require_once APPPATH . 'libraries/JSON_Model.php';

class Road_type extends JSON_Model
{

    public function __construct()
    {
        parent::__construct();
    }

    public $name = 'road_type';

    public $attrs = [
        'name',
    ];

}
