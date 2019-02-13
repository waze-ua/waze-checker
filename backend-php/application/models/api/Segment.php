<?php
defined('BASEPATH') or exit('No direct script access allowed');
require_once APPPATH . 'libraries/JSON_Model.php';

use Phayes\GeoPHP\GeoPHP;
use Phayes\GeoPHP\Adapters\GeoHash;

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

    ];

    public $belongsTo = [
        'street'    => [
            'targetModel' => 'street',
        ],
        'roadType'  => [
            'targetModel' => 'road_type',
        ],
        'updatedBy' => [
            'targetModel' => 'user',
        ],
        'region'    => [
            'targetModel' => 'region',
        ],
    ];

    public $hasMany = [
        'connections' => [
            'targetModel' => 'connection',
            'joinColumn'  => 'fromSegment',
        ],

    ];

    public function getAmounts($region)
    {
        $result = array(
            'all'                    => 0,
            'length'                 => 0,
            'withoutSpeed'           => 0,
            'speedMore90InCity'      => 0,
            'withLowLock'            => 0,
            'withoutTurn'            => 0,
            'notConnected'           => 0,
            'short'                  => 0,
            'withNameWithoutCity'    => 0,
            'unpaved'                => 0,
            'withAverageSpeedCamera' => 0,
            'new'                    => 0,
            'revDirection'           => 0,
            'toll'                   => 0,

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

        //withoutTurn
        $this->db->select('count(s.id) amount');
        $this->db->from('segment s');
        $this->db->where("s.region = {$region} AND (SELECT count(connection.id) FROM connection WHERE connection.fromSegment = s.id AND connection.isAllowed = 0 AND ((s.fwdDirection = 1 AND connection.direction = 1) OR (s.revDirection = 1 AND connection.direction = 0 ))) > 0");
        $query = $this->db->get();
        $row = $query->row();
        if (isset($row)) {
            $result['withoutTurn'] = $row->amount;
        }

        // //notConnected
        // $this->db->select('count(segment.id) amount');
        // $this->db->from('segments');
        // $this->db->join('region_segments', 'region_segment.segment = segment.id');
        // $this->db->where("region_segment.region = {$region} AND (SELECT count(connection.id) FROM connections WHERE connection.fromSegment = segment.id OR connection.toSegment = segment.id) > 0");
        // $query = $this->db->get();
        // $row = $query->row();
        // if (isset($row)) {
        //     $result['notConnected'] = $row->amount;
        // }

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
        $this->db->where("s.region = {$region} AND (s.flags = 17 OR s.flags = 16)");
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
        $GeoHash = new GeoHash();
        if (count($segments) > 0) {
            $ids = [];
            $segmentsForSave = [];

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

                    if ($segment->fwdMaxSpeed === null) {
                        $segment->fwdMaxSpeed = 0;
                    }

                    if ($segment->revMaxSpeed === null) {
                        $segment->revMaxSpeed = 0;
                    }

                    if ($segment->routingRoadType === null) {
                        $segment->routingRoadType = 0;
                    }

                    if ($segment->primaryStreetID === null) {
                        $segment->primaryStreetID = 0;
                    }

                    if ($segment->lockRank === null) {
                        $segment->lockRank = 0;
                    }

                    if ($segment->updatedOn === null) {
                        $segment->updatedOn = 0;
                    }

                    if ($segment->createdOn === null) {
                        $segment->createdOn = 0;
                    }

                    $segmentsForSave[] = [
                        'id'                    => $segment->id,
                        'allowNoDirection'      => $segment->allowNoDirection,
                        'createdBy'             => $segment->createdBy,
                        'createdOn'             => $segment->createdOn,
                        'flags'                 => $segment->flags,
                        'fromNodeId'            => $segment->fromNodeID,
                        'fwdDirection'          => $segment->fwdDirection,
                        'fwdFlags'              => $segment->fwdFlags,
                        'fwdMaxSpeed'           => $segment->fwdMaxSpeed,
                        'fwdMaxSpeedUnverified' => $segment->fwdMaxSpeedUnverified,
                        'fwdToll'               => $segment->fwdToll,
                        'fwdTurnsLocked'        => $segment->fwdTurnsLocked,
                        'hasClosures'           => $segment->hasClosures,
                        'hasHNs'                => $segment->hasHNs,
                        'length'                => $segment->length,
                        'level'                 => $segment->level,
                        'lockRank'              => $segment->lockRank,
                        'street'                => $segment->primaryStreetID,
                        'rank'                  => $segment->rank,
                        'revDirection'          => $segment->revDirection,
                        'revFlags'              => $segment->revFlags,
                        'revMaxSpeed'           => $segment->revMaxSpeed,
                        'revMaxSpeedUnverified' => $segment->revMaxSpeedUnverified,
                        'revToll'               => $segment->revToll,
                        'revTurnsLocked'        => $segment->revTurnsLocked,
                        'roadType'              => $segment->roadType,
                        'routingRoadType'       => $segment->routingRoadType,
                        'separator'             => $segment->separator,
                        'toNodeId'              => $segment->toNodeID,
                        'updatedBy'             => $segment->updatedBy,
                        'updatedOn'             => $segment->updatedOn,
                        'validated'             => $segment->validated,
                        'coordinates'           => null,
                        'lon'                   => $segment->lon,
                        'lat'                   => $segment->lat,
                        'hasTransition'         => 0,
                        'startPoint'            => $GeoHash->write($lineString->startPoint()),
                        'endPoint'              => $GeoHash->write($lineString->endPoint()),
                        'region'                => $regionId,
                    ];
                }
            }
            if (count($ids) > 0) {
                $this->db->where_in('id', $ids);
                $this->db->delete('segment');
                $this->db->insert_batch('segment', $segmentsForSave);
            }
        }
    }
}
