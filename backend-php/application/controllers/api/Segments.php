<?php
defined('BASEPATH') or exit('No direct script access allowed');
require_once APPPATH . 'controllers/Json_api.php';

class Segments extends Json_api
{
    public $modelName = 'segment';

    public function __construct()
    {
        parent::__construct();
    }

    public function DELETE_item($id)
    {
        $this->load->driver('cache', ['adapter' => 'file']);

        $this->db->select('region');
        $this->db->where(['id' => $id]);
        $query = $this->db->get('segment');
        $row = $query->row();
        if (isset($row)) {
            $this->cache->delete("region_{$row->region}");
        }

        $this->db->delete('segment', ['id' => $id]);
        $this->db->delete('connection', ['fromSegment' => $id]);
        $this->db->delete('connection', ['toSegment' => $id]);

        $this->output
            ->set_content_type('application/json')
            ->set_output(json_encode([], JSON_HEX_TAG | JSON_UNESCAPED_UNICODE));
    }

    public function getAmounts($region)
    {

        $this->load->driver('cache', ['adapter' => 'file']);
        $cache = $this->cache->get("region_{$region}");

        if ($cache) {
            $this->output
                ->set_content_type('application/json')
                ->set_output($cache);
        } else {
            $this->load->model('api/segment');
            $result = $this->segment->getAmounts($region);

            $this->cache->save("region_{$region}", json_encode($result, JSON_HEX_TAG | JSON_UNESCAPED_UNICODE), 604800);

            $this->output
                ->set_content_type('application/json')
                ->set_output(json_encode($result, JSON_HEX_TAG | JSON_UNESCAPED_UNICODE));
        }
    }

    public function setDataFromWME($region)
    {
        header('Access-Control-Allow-Origin: *');
        header('Access-Control-Allow-Methods: OPTIONS,POST');
        header('Access-Control-Allow-Credentials: true');

        if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
            return;
        }

        $post_data = file_get_contents("php://input");
        $raw_data = json_decode($post_data);
        $segments = $raw_data->data;

        $this->load->model('api/segment', 'segment');
        $this->segment->setDataFromWME($segments, $region);

        $this->output
            ->set_content_type('application/json')
            ->set_output(json_encode(['result' => 'OK'], JSON_HEX_TAG | JSON_UNESCAPED_UNICODE));

    }

}
