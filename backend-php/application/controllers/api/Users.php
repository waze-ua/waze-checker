<?php
defined('BASEPATH') or exit('No direct script access allowed');
require_once APPPATH . 'controllers/Json_api.php';

class Users extends Json_api
{
    public $modelName = 'user';

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
        // users
        $users = $raw_data->data;

    
        if (count($users) > 0) {
            $users = array_unique($users, SORT_REGULAR);
             print_r($users);
            $ids = [];

            foreach ($users as $user) {
                $ids[] = $user->id;
                if (isset($user->inactive)) {
                    unset($user->inactive);
                }
               // $user->userName = $this->db->escape_str($user->userName);
            }

            $this->db->where_in('id', $ids);
            $this->db->delete('user');

            $this->db->insert_batch('user', $users);
        }

        $this->output
            ->set_content_type('application/json')
            ->set_output(json_encode(['result' => 'OK'], JSON_HEX_TAG | JSON_UNESCAPED_UNICODE));

    }
}
