<?php
defined('BASEPATH') or exit('No direct script access allowed');
require_once APPPATH . 'controllers/Json_api.php';
use Phayes\GeoPHP\Geometry\Point;
use Phayes\GeoPHP\GeoPHP;

class Regions extends Json_api
{
    public $modelName = 'region';

    public function __construct()
    {
        parent::__construct();
    }

    public function DELETE_item($id)
    {
        $this->db->delete('regions', ['id' => $id]);

        $this->db->query("DELETE segment, connection
        FROM segment
        JOIN connection ON connection.fromSegment = segment.id
        WHERE segment.region = {$id}");

        $this->output
            ->set_content_type('application/json')
            ->set_output(json_encode([], JSON_HEX_TAG | JSON_UNESCAPED_UNICODE));
    }

    public function prepaireData($id)
    {
        $this->db->query("DELETE segment, connection
        FROM segment
        JOIN connection ON connection.fromSegment = segment.id
        WHERE segment.region = {$id}");

        $this->output
            ->set_content_type('application/json')
            ->set_output(json_encode(['result' => 'OK'], JSON_HEX_TAG | JSON_UNESCAPED_UNICODE));
    }

    public function finishData($id, $country)
    {
        $this->load->model('api/region', 'region');
        $this->region->finishData($id, $country);

        $this->output
            ->set_content_type('application/json')
            ->set_output(json_encode(['result' => 'OK'], JSON_HEX_TAG | JSON_UNESCAPED_UNICODE));
    }

    public function uploadPolygon($region = 0)
    {
        $data = ['status' => 'error'];
        $name = explode('.', $_FILES['geo_data']['name']);
        $ext = end($name);
        if ($ext == 'kml' || $ext == 'wkt') {
            if ($_FILES['geo_data']['error'] == UPLOAD_ERR_OK && is_uploaded_file($_FILES['geo_data']['tmp_name'])) {
                $polygon = GeoPHP::load(file_get_contents($_FILES['geo_data']['tmp_name']), $ext);
                $result = $this->db->query("UPDATE region SET polygon = GeomFromText('{$polygon->out('wkt')}') WHERE id = {$region}");
                if ($result) {
                    $this->createBBoxes($region);
                    $data = ['status' => 'OK'];
                } else {
                    $error = $this->db->error();
                    $data['message'] = $error->message;
                }
            }
        }

        $this->output
            ->set_content_type('application/json')
            ->set_output(json_encode($data, JSON_HEX_TAG | JSON_UNESCAPED_UNICODE));
    }

    public function savePolygon($region = null)
    {
        if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
            return;
        }

        $data = ['status' => 'error'];
        if ($region) {
            $data = json_decode(file_get_contents('php://input'), true);
            $coordinates = $data['coordinates'];
            if ($coordinates[0] != $coordinates[count($coordinates) - 1]) {
                $coordinates[] = $coordinates[0];
            }
            $string = implode(',', $coordinates);
            $result = $this->db->query("UPDATE region SET polygon = GeomFromText('POLYGON(({$string}))') WHERE id = {$region}");
            if ($result) {
                $this->createBBoxes($region);
                $data = ['status' => 'OK'];
            } else {
                $error = $this->db->error();
                $data['message'] = $error->message;
            }
        }
        $this->output
            ->set_content_type('application/json')
            ->set_output(json_encode($data, JSON_HEX_TAG | JSON_UNESCAPED_UNICODE));
    }

    public function getPolygon($region = 0)
    {
        $this->db->select('AsBinary(polygon) polygon');
        $this->db->where(['id' => $region]);
        $region = $this->db->get('region')->row();

        if ($region->polygon) {
            $regionGeometry = geoPHP::load($region->polygon, 'wkb');
            $data = $regionGeometry->out('json');
        } else {
            $data = json_encode(null);
        }

        $this->output
            ->set_content_type('application/json')
            ->set_output($data);
    }

    public function createBBoxes($id = 0)
    {
        set_time_limit(300);
        $this->db->delete('bbox', ['region' => $id]);

        $this->db->select('AsBinary(polygon) polygon');
        $this->db->where(['id' => $id]);
        $region = $this->db->get('region')->row();

        if ($region->polygon) {
            $polygon = geoPHP::load($region->polygon, 'wkb');
            $bbox = $polygon->getBBox();
            $bboxes = [];
            $x = $bbox['minx'];
            $stepX = 0.15;
            $stepY = 0.1;
            while ($x <= $bbox['maxx']) {
                $y = $bbox['miny'];
                $west = $x;
                $east = $x + $stepX;
                while ($y <= $bbox['maxy']) {
                    $south = $y;
                    $north = $y + $stepY;
                    $result = false;

                    $point = new Point($west, $north);
                    if ($polygon->pointInPolygon($point)) {
                        $result = true;
                    }
                    if (!$result) {
                        $point = new Point($east, $north);
                        if ($polygon->pointInPolygon($point)) {
                            $result = true;
                        }
                    }
                    if (!$result) {
                        $point = new Point($east, $south);
                        if ($polygon->pointInPolygon($point)) {
                            $result = true;
                        }
                    }
                    if (!$result) {
                        $point = new Point($west, $south);
                        if ($polygon->pointInPolygon($point)) {
                            $result = true;
                        }
                    }
                    if ($result) {
                        $box = [
                            'south'  => $south,
                            'north'  => $north,
                            'west'   => $west,
                            'east'   => $east,
                            'region' => $id,
                        ];
                        if (!in_array($box, $bboxes, true)) {
                            $bboxes[] = $box;
                        }
                    }

                    $y = $y + $stepY;
                }
                $x = $x + $stepX;
            }

            //echo count($bboxes);
            if (count($bboxes) > 0) {
                $this->db->insert_batch('bbox', $bboxes);
            }

        } else {
            $data = null;
        }
    }

}
