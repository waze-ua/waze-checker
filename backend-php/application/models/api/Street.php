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
            'targetModel' => 'city',
        ],
    ];

    public function setDataFromWME($streets = [])
    {
        if (count($streets) > 0) {
            $ids = [];

            foreach ($streets as $key => $street) {
                $ids[] = $street->id;

                $street->city = $street->cityID;

                unset($street->cityID);
                unset($street->englishName);
                unset($street->signText);
                unset($street->signType);
            }

            $this->db->where_in('id', $ids);
            $this->db->delete('street');

            $this->db->insert_batch('street', $streets);
        }

    }
}
