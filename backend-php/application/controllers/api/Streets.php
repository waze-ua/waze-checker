<?php
defined('BASEPATH') or exit('No direct script access allowed');
require_once APPPATH . 'controllers/Json_api.php';

class Streets extends Json_api
{
    public $modelName = 'street';

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

        // streets
        $streets = $raw_data->data;
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

        $this->output
            ->set_content_type('application/json')
            ->set_output(json_encode(['result' => 'OK'], JSON_HEX_TAG | JSON_UNESCAPED_UNICODE));

    }
}
