<?php
defined('BASEPATH') or exit('No direct script access allowed');

require_once APPPATH . 'libraries/JSON_Model.php';

class Street extends JSON_Model
{

    public function __construct()
    {
        parent::__construct();
    }

    public $name = 'street';

    public $attrs = [
        'name',
    ];

    public $belongsTo = [
        'city' => [
            'targetModel'      => 'city',
        ],
    ];
}
