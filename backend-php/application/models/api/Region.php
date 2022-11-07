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

  public function updateRegionStats($region)
  {
    $this->load->driver('cache', ['adapter' => 'file']);
    $date = new DateTime();
    $this->load->model('api/segment');

    $amounts = $this->segment->getAmounts($region);

    $this->db->insert('statistic', ['region' => $region, 'date' => $date->format('Y-m-d'), 'type' => 'all', 'value' => $amounts['all']]);
    $this->db->insert('statistic', ['region' => $region, 'date' => $date->format('Y-m-d'), 'type' => 'length', 'value' => $amounts['length']]);
    $this->db->insert('statistic', ['region' => $region, 'date' => $date->format('Y-m-d'), 'type' => 'withoutSpeed', 'value' => $amounts['withoutSpeed']]);
    $this->db->insert('statistic', ['region' => $region, 'date' => $date->format('Y-m-d'), 'type' => 'speedMore90InCity', 'value' => $amounts['speedMore90InCity']]);
    $this->db->insert('statistic', ['region' => $region, 'date' => $date->format('Y-m-d'), 'type' => 'hasIntersection', 'value' => $amounts['hasIntersection']]);
    $this->db->insert('statistic', ['region' => $region, 'date' => $date->format('Y-m-d'), 'type' => 'withLowLock', 'value' => $amounts['withLowLock']]);
    $this->db->insert('statistic', ['region' => $region, 'date' => $date->format('Y-m-d'), 'type' => 'awithoutTurnsll', 'value' => $amounts['withoutTurns']]);
    $this->db->insert('statistic', ['region' => $region, 'date' => $date->format('Y-m-d'), 'type' => 'notConnected', 'value' => $amounts['notConnected']]);
    $this->db->insert('statistic', ['region' => $region, 'date' => $date->format('Y-m-d'), 'type' => 'short', 'value' => $amounts['short']]);
    $this->db->insert('statistic', ['region' => $region, 'date' => $date->format('Y-m-d'), 'type' => 'withNameWithoutCity', 'value' => $amounts['withNameWithoutCity']]);
    $this->db->insert('statistic', ['region' => $region, 'date' => $date->format('Y-m-d'), 'type' => 'unpaved', 'value' => $amounts['unpaved']]);
    $this->db->insert('statistic', ['region' => $region, 'date' => $date->format('Y-m-d'), 'type' => 'withAverageSpeedCamera', 'value' => $amounts['withAverageSpeedCamera']]);
    $this->db->insert('statistic', ['region' => $region, 'date' => $date->format('Y-m-d'), 'type' => 'new', 'value' => $amounts['new']]);
    $this->db->insert('statistic', ['region' => $region, 'date' => $date->format('Y-m-d'), 'type' => 'revDirection', 'value' => $amounts['revDirection']]);
    $this->db->insert('statistic', ['region' => $region, 'date' => $date->format('Y-m-d'), 'type' => 'toll', 'value' => $amounts['toll']]);

    $this->cache->delete("region_{$region}");
  }
}
