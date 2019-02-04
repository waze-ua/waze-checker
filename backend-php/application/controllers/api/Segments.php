<?php
defined('BASEPATH') or exit('No direct script access allowed');
require_once APPPATH . 'controllers/Json_api.php';

use Phayes\GeoPHP\Adapters\GeoHash;
use Phayes\GeoPHP\GeoPHP;

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

    public function setDataFromWME()
    {
        header('Access-Control-Allow-Origin: *');
        header('Access-Control-Allow-Methods: OPTIONS,POST');
        header('Access-Control-Allow-Credentials: true');

        if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
            return;
        }

        $GeoHash = new GeoHash();

        $post_data = file_get_contents("php://input");
        $raw_data = json_decode($post_data);

        $segments = $raw_data->data;
        if (count($segments) > 0) {
            $ids = [];
            $segmentsForSave = [];
            $regionId = $segments[0]->region;

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

                //$lineString = GeoPHP::load("LINESTRING({$coordinates})", 'wkt');

                if ($regionGeometry->pointInPolygon($lineString->startPoint())) {
                    $ids[] = $segment->id;

                    $centroid = $lineString->getCentroid();
                    $segment->lon = $centroid->getX();
                    $segment->lat = $centroid->getY();

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
                        'lon'                   => $centroid->getX(),
                        'lat'                   => $centroid->getY(),
                        'hasTransition'         => 0,
                        'startPoint'            => $GeoHash->write($lineString->startPoint()),
                        'endPoint'              => $GeoHash->write($lineString->startPoint()),
                        'region'                => $segment->region,
                    ];

                }

            }
            if (count($ids) > 0) {
                $this->db->where_in('id', $ids);
                $this->db->delete('segment');

                $this->db->insert_batch('segment', $segmentsForSave);
            }
        }

        $this->output
            ->set_content_type('application/json')
            ->set_output(json_encode(['result' => 'OK'], JSON_HEX_TAG | JSON_UNESCAPED_UNICODE));

    }

}
