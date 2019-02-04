<?php
defined('BASEPATH') or exit('No direct script access allowed');
require_once APPPATH . 'controllers/Json_api.php';

class Bboxes extends Json_api
{
    public $modelName = 'bbox';

    public function __construct()
    {
        parent::__construct();
    }

    public function DELETE_item($id)
    {
        $this->db->delete('bbox', array('id' => $id));

        $this->output
            ->set_content_type('application/json')
            ->set_output(json_encode([], JSON_HEX_TAG | JSON_UNESCAPED_UNICODE));
    }
}
