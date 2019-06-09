<?php
defined('BASEPATH') or exit('No direct script access allowed');
require_once APPPATH . 'libraries/JSON_Model.php';

use Phayes\GeoPHP\Adapters\GeoHash;
use Phayes\GeoPHP\GeoPHP;

class Segment extends JSON_Model
{

    public function __construct()
    {
        parent::__construct();
    }

    public $name = 'segment';

    public $attrs = [
        //  'allowNoDirection',
        //  'createdBy',
        //  'createdOn',
        'flags',
        //  'fromNodeId',
        'fwdDirection',
        //  'fwdFlags',
        'fwdMaxSpeed',
        //  'fwdMaxSpeedUnverified',
        'fwdToll',
        //  'fwdTurnsLocked',
        //  'hasClosures',
        //  'hasHNs',
        'length',
        //  'level',
        'lockRank',
        //  'rank',
        'revDirection',
        //  'revFlags',
        'revMaxSpeed',
        //  'revMaxSpeedUnverified',
        'revToll',
        //  'revTurnsLocked',
        //  'routingRoadType',
        //  'separator',
        //  'toNodeId',
        'updatedOn',
        //  'validated',
        'lat',
        'lon',
        'hasTransition',
        'notConnected',
    ];

    public $belongsTo = [
        'street' => [
            'targetModel' => 'street',
        ],
        'roadType' => [
            'targetModel' => 'road_type',
        ],
        'updatedBy' => [
            'targetModel' => 'user',
        ],
        'region' => [
            'targetModel' => 'region',
        ],
    ];

    // public $hasMany = [
    //     'connections' => [
    //         'targetModel' => 'connection',
    //         'joinColumn' => 'fromSegment',
    //     ],

    // ];

    public function getAmounts($region)
    {
        $result = array(
            'all' => 0,
            'length' => 0,
            'withoutSpeed' => 0,
            'speedMore90InCity' => 0,
            'withLowLock' => 0,
            'withoutTurns' => 0,
            'notConnected' => 0,
            'hasIntersection' => 0,
            'short' => 0,
            'withNameWithoutCity' => 0,
            'unpaved' => 0,
            'withAverageSpeedCamera' => 0,
            'new' => 0,
            'revDirection' => 0,
            'toll' => 0,
        );

        //all
        $this->db->select('count(s.id) amount');
        $this->db->from('segment s');
        $this->db->where("s.region = {$region}");
        $query = $this->db->get();
        $row = $query->row();
        if (isset($row)) {
            $result['all'] = $row->amount;
        }

        //length
        $this->db->select('sum(s.length) length');
        $this->db->from('segment s');
        $this->db->where("s.region = {$region}");
        $query = $this->db->get();
        $row = $query->row();
        if (isset($row)) {
            $result['length'] = (int) ($row->length / 1000);
        }

        //withoutSpeed
        $this->db->select('count(s.id) amount');
        $this->db->from('segment s');
        $this->db->where("s.region = {$region} AND ((s.fwdDirection = 1 AND s.fwdMaxSpeed = 0) OR (s.revDirection = 1 AND s.revMaxSpeed = 0))");
        $query = $this->db->get();
        $row = $query->row();
        if (isset($row)) {
            $result['withoutSpeed'] = $row->amount;
        }

        //speedMore90InCity
        $this->db->select('count(s.id) amount');
        $this->db->from('segment s');
        $this->db->join('street st', 's.street = st.id');
        $this->db->join('city c', 'st.city = c.id');
        $this->db->where("s.region = {$region} AND c.isEmpty = 0 AND (s.fwdMaxSpeed >= 90 OR s.revMaxSpeed >= 90)");
        $query = $this->db->get();
        $row = $query->row();
        if (isset($row)) {
            $result['speedMore90InCity'] = $row->amount;
        }

        //withLowLock
        $this->db->select('count(s.id) amount');
        $this->db->from('segment s');
        $this->db->where("s.region = {$region} AND (((s.roadType = 4 OR s.roadType = 7) AND s.lockRank < 2) OR ((s.roadType = 3 OR s.roadType = 6) AND s.lockRank < 3) )");
        $query = $this->db->get();
        $row = $query->row();
        if (isset($row)) {
            $result['withLowLock'] = $row->amount;
        }

        //withoutTurns
        $this->db->select('count(s.id) amount');
        $this->db->from('segment s');
        $this->db->where("s.region = {$region} AND s.withoutTurns = 1");
        $query = $this->db->get();
        $row = $query->row();
        if (isset($row)) {
            $result['withoutTurns'] = $row->amount;
        }

        //notConnected
        $this->db->select('count(s.id) amount');
        $this->db->from('segment s');
        $this->db->where("s.region = {$region} AND s.notConnected = 1");
        $query = $this->db->get();
        $row = $query->row();
        if (isset($row)) {
            $result['notConnected'] = $row->amount;
        }

        //hasIntersection
        $this->db->select('count(s.id) amount');
        $this->db->from('segment s');
        $this->db->where("s.region = {$region} AND s.hasIntersection = 1");
        $query = $this->db->get();
        $row = $query->row();
        if (isset($row)) {
            $result['hasIntersection'] = $row->amount;
        }

        //short
        $this->db->select('count(s.id) amount');
        $this->db->from('segment s');
        $this->db->where("s.region = {$region} AND s.length < 5");
        $query = $this->db->get();
        $row = $query->row();
        if (isset($row)) {
            $result['short'] = $row->amount;
        }

        //withNameWithoutCity
        $this->db->select('count(s.id) amount');
        $this->db->from('segment s');
        $this->db->join('street st', 's.street = st.id');
        $this->db->join('city c', 'st.city = c.id');
        $this->db->where("s.region = {$region} AND s.street != 0 AND c.isEmpty = 1 AND (st.name LIKE '%улица%' OR st.name LIKE '%iela%' OR st.name LIKE '%проспект%' OR st.name LIKE '%переулок%' OR st.name LIKE '%проезд%' OR st.name LIKE '%площадь%' OR st.name LIKE '%шоссе%' OR st.name LIKE '%тракт%')");
        $query = $this->db->get();
        $row = $query->row();
        if (isset($row)) {
            $result['withNameWithoutCity'] = $row->amount;
        }

        //unpaved
        $this->db->select('count(s.id) amount');
        $this->db->from('segment s');
        $this->db->where("s.region = {$region} AND (s.flags = 17 OR s.flags = 16 OR s.flags = 48)");
        $query = $this->db->get();
        $row = $query->row();
        if (isset($row)) {
            $result['unpaved'] = $row->amount;
        }

        //withAverageSpeedCamera
        $this->db->select('count(s.id) amount');
        $this->db->from('segment s');
        $this->db->where("s.region = {$region} AND (s.fwdFlags = 1 OR s.revFlags = 1)");
        $query = $this->db->get();
        $row = $query->row();
        if (isset($row)) {
            $result['withAverageSpeedCamera'] = $row->amount;
        }

        //new
        $this->db->select('count(s.id) amount');
        $this->db->from('segment s');
        $this->db->where("s.region = {$region} AND s.updatedBy = -1");
        $query = $this->db->get();
        $row = $query->row();
        if (isset($row)) {
            $result['new'] = $row->amount;
        }

        //revDirection
        $this->db->select('count(s.id) amount');
        $this->db->from('segment s');
        $this->db->where("s.region = {$region} AND s.fwdDirection = 0 AND s.revDirection = 1");
        $query = $this->db->get();
        $row = $query->row();
        if (isset($row)) {
            $result['revDirection'] = $row->amount;
        }

        //toll
        $this->db->select('count(s.id) amount');
        $this->db->from('segment s');
        $this->db->where("s.region = {$region} AND ((s.fwdDirection = 1 AND s.fwdToll = 1) OR (s.revDirection = 1 AND s.revToll = 1))");
        $query = $this->db->get();
        $row = $query->row();
        if (isset($row)) {
            $result['toll'] = $row->amount;
        }

        return $result;
    }

    public function setDataFromWME($segments = [], $regionId)
    {
        set_time_limit(0);
        $GeoHash = new GeoHash();
        if (count($segments) > 0) {
            $ids = [];
            $segmentsForSave = [];
            $geometries = [];

            $this->db->select('AsBinary(polygon) polygon');
            $this->db->where(['id' => $regionId]);
            $region = $this->db->get('region')->row();
            $regionGeometry = geoPHP::load($region->polygon, 'wkb');
            foreach ($segments as $segment) {
                $coordinates = [];
                foreach ($segment->geometry->coordinates as $coordinate) {
                    $coordinates[] = implode(' ', $coordinate);
                }
                $coordinates = implode(',', $coordinates);
                $lineString = GeoPHP::load("LINESTRING({$coordinates})", 'wkt');

                if ($regionGeometry->pointInPolygon($lineString->startPoint())) {
                    $ids[] = $segment->id;

                    if ($segment->length > 500) {
                        $centroid = $lineString->getCentroid();
                        $segment->lon = $centroid->getX();
                        $segment->lat = $centroid->getY();
                    } else {
                        $startPoint = $lineString->startPoint();
                        $segment->lon = $startPoint->getX();
                        $segment->lat = $startPoint->getY();
                    }

                    if ($segment->lockRank === null) {
                        $lockRank = 0;
                    } else {
                        $lockRank = (int) $segment->lockRank + 1;
                    }

                    $segmentsForSave[] = [
                        'id' => $segment->id,
                        'allowNoDirection' => (int) $segment->allowNoDirection,
                        'createdBy' => (int) $segment->createdBy,
                        'createdOn' => (int) $segment->createdOn,
                        'flags' => (int) $segment->flags,
                        'fromNodeId' => (int) $segment->fromNodeID,
                        'fwdDirection' => (int) $segment->fwdDirection,
                        'fwdFlags' => (int) $segment->fwdFlags,
                        'fwdMaxSpeed' => (int) $segment->fwdMaxSpeed,
                        'fwdMaxSpeedUnverified' => (int) $segment->fwdMaxSpeedUnverified,
                        'fwdToll' => (int) $segment->fwdToll,
                        'fwdTurnsLocked' => (int) $segment->fwdTurnsLocked,
                        'hasClosures' => (int) $segment->hasClosures,
                        'hasHNs' => (int) $segment->hasHNs,
                        'length' => (int) $segment->length,
                        'level' => (int) $segment->level,
                        'lockRank' => $lockRank,
                        'street' => (int) $segment->primaryStreetID,
                        'rank' => (int) $segment->rank,
                        'revDirection' => (int) $segment->revDirection,
                        'revFlags' => (int) $segment->revFlags,
                        'revMaxSpeed' => (int) $segment->revMaxSpeed,
                        'revMaxSpeedUnverified' => (int) $segment->revMaxSpeedUnverified,
                        'revToll' => (int) $segment->revToll,
                        'revTurnsLocked' => (int) $segment->revTurnsLocked,
                        'roadType' => (int) $segment->roadType,
                        'routingRoadType' => (int) $segment->routingRoadType,
                        '`separator`' => (int) $segment->separator,
                        'toNodeId' => (int) $segment->toNodeID,
                        'updatedBy' => (int) $segment->updatedBy,
                        'updatedOn' => (int) $segment->updatedOn,
                        'validated' => (int) $segment->validated,
                        'coordinates' => "GeomFromText('{$lineString->out('wkt')}')",
                        'startPoint' => "'{$GeoHash->write($lineString->startPoint(), 0.0001)}'",
                        'endPoint' => "'{$GeoHash->write($lineString->endPoint(), 0.0001)}'",
                        'lon' => "'{$segment->lon}'",
                        'lat' => "'{$segment->lat}'",
                        'hasTransition' => 0,
                        'region' => $regionId,
                    ];
                }
            }
            if (count($ids) > 0) {
                $this->db->where_in('id', $ids);
                $this->db->delete('segment');

                $this->db->insert_batch('segment', $segmentsForSave, false);
                $idsStr = implode(',', $ids);

                $query = $this->db->query("SELECT s1.id id FROM `segment` s1
                JOIN `segment` s2 ON s1.id < s2.id
                WHERE s1.startPoint != s2.startPoint AND s1.startPoint != s2.endPoint AND s1.endPoint != s2.startPoint AND s1.endPoint != s2.endPoint AND s1.`level` = s2.`level`
                AND ST_Intersects(s1.`coordinates`, s2.`coordinates`)
                AND s1.id IN ({$idsStr})
                AND s2.id IN ({$idsStr})");
                $result = $query->result();

                if (count($result) > 0) {
                    $ids = array_map(function ($item) {
                        return $item->id;
                    }, $result);

                    $this->db->set(['hasIntersection' => 1]);
                    $this->db->where_in('id', $ids);
                    $this->db->update('segment');
                }
            }
        }
    }
}
