<?php
defined('BASEPATH') or exit('No direct script access allowed');

require_once APPPATH . 'libraries/JSON_Model.php';

class Region extends JSON_Model
{

    public function __construct()
    {
        parent::__construct();
    }

    public $name = 'region';

    public $attrs = [
        'name',
        'lastUpdate',
    ];

}
