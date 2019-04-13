<?php
defined('BASEPATH') or exit('No direct script access allowed');

require_once APPPATH . 'libraries/JSON_Model.php';

class Region extends JSON_Model
{

    public function __construct()
    {
        parent::__construct();
    }

    public $name = 'region';

    public $attrs = [
        'name',
        'lastUpdate',
    ];

    public function finishData($id, $country)
    {
        set_time_limit(0);
        $region = $id;
        $this->load->driver('cache', ['adapter' => 'file']);

        $this->db->set(['lastUpdate' => (int) (microtime(true) * 1000)]);
        $this->db->where(['id' => $id]);
        $this->db->update('region');

        $this->db->query("DELETE segment, connection, street, city
        FROM segment
        JOIN connection ON connection.fromSegment = segment.id
        JOIN street ON street.id = segment.street
        JOIN city ON city.id = street.city
        WHERE city.country != {$country}");

        $this->db->query("UPDATE segment SET segment.notConnected = 1
        WHERE segment.region = {$region} AND segment.id NOT IN
        (SELECT c.fromSegment
          FROM connection c
          WHERE c.fromSegment = segment.id)");

        $query = $this->db->query("(SELECT c.fromSegment id FROM connection c 
            JOIN segment ON segment.id = c.toSegment 
            WHERE c.isAllowed = 0 AND c.direction = 1 AND segment.fwdDirection = 1 AND segment.region = {$region})
          UNION ALL
          (SELECT c.fromSegment id FROM connection c 
            JOIN segment ON segment.id = c.toSegment 
            WHERE c.isAllowed = 0 AND c.direction = 0 AND segment.revDirection = 1 AND segment.region = {$region})");
     
        $ids = array_map(function ($item) {
            return $item->id;
        }, $query->result());

        if (count($ids) > 0) {
            $this->db->set(['withoutTurns' => 1]);
            $this->db->where_in('id', $ids);
            $this->db->update('segment');
        }

        $this->db->query("UPDATE user
        JOIN (
          SELECT g.userId userId, MAX(g.dt) value FROM (
                        SELECT s1.updatedBy userId, s1.updatedOn dt
                        FROM segment s1
                        WHERE  s1.updatedOn > 0
              UNION ALL
                        SELECT s2.createdBy userId, s2.createdOn dt
                        FROM segment s2
                        WHERE s2.createdOn > 0
          ) g GROUP BY userId
        ) a ON user.id = a.userId
        SET user.lastEdit=a.value");

        $this->db->query("UPDATE user
        JOIN (
          SELECT g.userId userId, MIN(g.dt) value FROM (
                        SELECT s1.updatedBy userId, s1.updatedOn dt
                        FROM segment s1
                        WHERE  s1.updatedOn > 0
              UNION ALL
                        SELECT s2.createdBy userId, s2.createdOn dt
                        FROM segment s2
                        WHERE s2.createdOn > 0
          ) g GROUP BY userId
        ) a ON user.id = a.userId
        SET user.firstEdit=a.value");

        $date = new DateTime();
        $this->load->model('api/segment');

        $amounts = $this->segment->getAmounts($region);

        $this->db->insert('statistic', ['region' => $id, 'date' => $date->format('Y-m-d'), 'type' => 'all', 'value' => $amounts['all']]);
        $this->db->insert('statistic', ['region' => $id, 'date' => $date->format('Y-m-d'), 'type' => 'length', 'value' => $amounts['length']]);
        $this->db->insert('statistic', ['region' => $id, 'date' => $date->format('Y-m-d'), 'type' => 'withoutSpeed', 'value' => $amounts['withoutSpeed']]);
        $this->db->insert('statistic', ['region' => $id, 'date' => $date->format('Y-m-d'), 'type' => 'speedMore90InCity', 'value' => $amounts['speedMore90InCity']]);
        $this->db->insert('statistic', ['region' => $id, 'date' => $date->format('Y-m-d'), 'type' => 'withLowLock', 'value' => $amounts['withLowLock']]);
        $this->db->insert('statistic', ['region' => $id, 'date' => $date->format('Y-m-d'), 'type' => 'awithoutTurnsll', 'value' => $amounts['withoutTurns']]);
        $this->db->insert('statistic', ['region' => $id, 'date' => $date->format('Y-m-d'), 'type' => 'notConnected', 'value' => $amounts['notConnected']]);
        $this->db->insert('statistic', ['region' => $id, 'date' => $date->format('Y-m-d'), 'type' => 'short', 'value' => $amounts['short']]);
        $this->db->insert('statistic', ['region' => $id, 'date' => $date->format('Y-m-d'), 'type' => 'withNameWithoutCity', 'value' => $amounts['withNameWithoutCity']]);
        $this->db->insert('statistic', ['region' => $id, 'date' => $date->format('Y-m-d'), 'type' => 'unpaved', 'value' => $amounts['unpaved']]);
        $this->db->insert('statistic', ['region' => $id, 'date' => $date->format('Y-m-d'), 'type' => 'withAverageSpeedCamera', 'value' => $amounts['withAverageSpeedCamera']]);
        $this->db->insert('statistic', ['region' => $id, 'date' => $date->format('Y-m-d'), 'type' => 'new', 'value' => $amounts['new']]);
        $this->db->insert('statistic', ['region' => $id, 'date' => $date->format('Y-m-d'), 'type' => 'revDirection', 'value' => $amounts['revDirection']]);
        $this->db->insert('statistic', ['region' => $id, 'date' => $date->format('Y-m-d'), 'type' => 'toll', 'value' => $amounts['toll']]);

        $this->cache->delete("region_{$region}");

        return;

    }

}
