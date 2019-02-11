<?php
defined('BASEPATH') or exit('No direct script access allowed');
require_once APPPATH . 'controllers/Json_api.php';

class Connections extends Json_api
{
    public $modelName = 'connection';

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
        $connections = $raw_data->data;

        $this->load->model('api/connection', 'connection');
        $this->connection->setDataFromWME($connections);

        $this->output
            ->set_content_type('application/json')
            ->set_output(json_encode(['result' => 'OK'], JSON_HEX_TAG | JSON_UNESCAPED_UNICODE));

    }
}
