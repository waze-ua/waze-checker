<?php
defined('BASEPATH') or exit('No direct script access allowed');

require_once APPPATH . 'libraries/JSON_Model.php';
require_once APPPATH . 'libraries/QueryBuilder.php';

class City extends JSON_Model
{

    public function __construct()
    {
        parent::__construct();
    }

    public $name = 'city';

    public $attrs = [
        'name',
        'lat',
        'lon',
    ];

    public function setDataFromWME($cities = [])
    {
        if (count($cities) > 0) {
            $cities = array_unique($cities, SORT_REGULAR);
            foreach ($cities as &$city) {
                $city->country = $city->countryID;

                if (isset($city->geometry)) {
                    $city->lon = $city->geometry->coordinates[0];
                    $city->lat = $city->geometry->coordinates[1];
                } else {
                    $city->lon = 0;
                    $city->lat = 0;
                }

                unset($city->englishName);
                unset($city->geometry);
                unset($city->countryID);
                unset($city->permissions);
                unset($city->rank);
                unset($city->stateID);

                $city = (array) $city;

                $city['name'] = $this->db->escape($city['name']);
                if (!$city['isEmpty']) {
                    $city['isEmpty'] = 0;
                }
            }

            $this->db->query(ReplaceBatch('city',  $cities));
        }
        return;
    }

}
