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

        // direction r = 0, f = 1
        $connections = $raw_data->data;
        if (count((array) $connections) > 0) {

            $connectionsForSave = [];
            $ids = [];
            $idsTo = [];

            foreach ($connections as $key => $connection) {
                $from = (int) substr($key, 0, -1);
                $direction = 0;
                if (substr($key, -1) === 'f') {
                    $direction = 1;
                }

                $ids[] = $from;

                foreach ($connection as $toKey => $isAllowed) {
                    $to = (int) substr($toKey, 0, -1);
                    if (is_object($isAllowed)) {
                        $isAllowed = $isAllowed->navigable;
                    }
                    $idsTo[] = $to;

                    $connectionsForSave[] = [
                        'fromSegment' => $from,
                        'toSegment'   => $to,
                        'direction'   => $direction,
                        'isAllowed'   => $isAllowed,
                    ];
                }
            }

            $this->db->where_in('fromSegment', $ids);
            $this->db->delete('connection');

            $this->db->insert_batch('connection', $connectionsForSave);
        }

        $this->output
            ->set_content_type('application/json')
            ->set_output(json_encode(['result' => 'OK'], JSON_HEX_TAG | JSON_UNESCAPED_UNICODE));

    }
}
