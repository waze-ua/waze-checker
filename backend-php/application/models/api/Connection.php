<?php
defined('BASEPATH') or exit('No direct script access allowed');

require_once APPPATH . 'libraries/JSON_Model.php';
require_once APPPATH . 'libraries/QueryBuilder.php';

class Connection extends JSON_Model
{

    public function __construct()
    {
        parent::__construct();
    }

    public $name = 'connection';

    public function setDataFromWME($connections = [])
    {
        set_time_limit(0);
        // direction r = 0, f = 1
        if (count((array) $connections) > 0) {

            $connectionsForSave = [];
            foreach ($connections as $key => $connection) {
                $from = (int) substr($key, 0, -1);
                $direction = 0;
                if (substr($key, -1) === 'f') {
                    $direction = 1;
                }

                foreach ($connection as $toKey => $isAllowed) {
                    $to = (int) substr($toKey, 0, -1);
                    $isAllowed = 0;

                    if (is_object($isAllowed)) {
                        $isAllowed = $isAllowed->navigable;
                    }

                    $connectionsForSave[] = [
                        'fromSegment' => $from,
                        'toSegment'   => $to,
                        'direction'   => $direction,
                        'isAllowed'   => $isAllowed,
                    ];
                }
            }

            $this->db->query(ReplaceBatch('connection',  $connectionsForSave));
        }
    }
}
