<?php
defined('BASEPATH') or exit('No direct script access allowed');

require_once APPPATH . 'libraries/JSON_Model.php';
require_once APPPATH . 'libraries/QueryBuilder.php';

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

            foreach ($users as &$user) {
                if (isset($user->inactive)) {
                    unset($user->inactive);
                }
                $user = (array) $user;

                $user['userName'] = $this->db->escape($user['userName']);
            }

            $this->db->query(ReplaceBatch('user', $users));
        }
    }
}
