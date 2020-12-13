<?php
defined('BASEPATH') or exit('No direct script access allowed');

require_once APPPATH . 'libraries/JSON_Model.php';
require_once APPPATH . 'libraries/QueryBuilder.php';

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
            foreach ($streets as $key => &$street) {
                $street = (array) $street;

                $street['city'] = $street['cityID'];
                $street['name'] = $this->db->escape($street['name']);

                if (!$street['isEmpty']) {
                    $street['isEmpty'] = 0;
                }

                unset($street['cityID']);
                unset($street['englishName']);
                unset($street['signText']);
                unset($street['signType']);
            }

            $this->db->query(ReplaceBatch('street', $streets));
        }
    }
}
