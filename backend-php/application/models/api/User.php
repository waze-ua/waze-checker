<?php
defined('BASEPATH') or exit('No direct script access allowed');

require_once APPPATH . 'libraries/JSON_Model.php';

class User extends JSON_Model
{

    public function __construct()
    {
        parent::__construct();
    }

    public $name = 'user';

    public $attrs = [
        'userName',
        'rank',
        'firstEdit',
        'lastEdit',
    ];

    public function setDataFromWME($users = [])
    {
        if (count($users) > 0) {
            $users = array_unique($users, SORT_REGULAR);
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
    }

}
