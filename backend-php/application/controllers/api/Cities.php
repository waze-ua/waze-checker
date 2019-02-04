<?php
defined('BASEPATH') or exit('No direct script access allowed');
require_once APPPATH . 'controllers/Json_api.php';

class Cities extends Json_api
{
    public $modelName = 'city';

    public function __construct()
    {
        parent::__construct();
    }

    public function setDataFromWME()
    {
        header('Access-Control-Allow-Origin: *');
        header('Access-Control-Allow-Methods: OPTIONS,POST');
        header('Access-Control-Allow-Credentials: true');

        if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
            return;
        }

        $post_data = file_get_contents("php://input");
        $raw_data = json_decode($post_data);

        // cities
        $cities = $raw_data->data;
        if (count($cities) > 0) {
            $cities = array_unique($cities, SORT_REGULAR);
            $ids = [];
            foreach ($cities as $city) {
                $ids[] = $city->id;

                $city->country = $city->countryID;

                unset($city->englishName);
                unset($city->geometry);
                unset($city->countryID);
                unset($city->permissions);
                unset($city->rank);
                unset($city->stateID);
            }

            $this->db->where_in('id', $ids);
            $this->db->delete('city');

            $this->db->insert_batch('city', $cities);
        }

        $this->output
            ->set_content_type('application/json')
            ->set_output(json_encode(['result' => 'OK'], JSON_HEX_TAG | JSON_UNESCAPED_UNICODE));

    }
}
