<?php
defined('BASEPATH') or exit('No direct script access allowed');

require_once APPPATH . 'libraries/JSON_Model.php';

class Bbox extends JSON_Model
{

    public function __construct()
    {
        parent::__construct();
    }

    public $name = 'bbox';

    private $itemsPerPage = 0;

    public $attrs = [
        'south',
        'west',
        'north',
        'east',
    ];

    // public $belongsTo = [
    //     'region'
    // ];

}
