<?php
defined('BASEPATH') or exit('No direct script access allowed');

class WmeData extends CI_Controller
{
    private $country = 37;

    public function getAllRegions()
    {
        $startTime = microtime(true);
        $now = new DateTime();
        print "Started all at " . $now->format('Y-m-d H:i:s') . "\n";

        $this->db->query('TRUNCATE TABLE segment');
        $this->db->query('TRUNCATE TABLE connection');
        $this->db->query('TRUNCATE TABLE street');
        $this->db->query('TRUNCATE TABLE city');
        $this->db->query('TRUNCATE TABLE user');
        print "Cleared in " . $this->showTime(microtime(true) - $startTime) . "\n";

        $this->db->select('id');
        $regions = $this->db->get('region')->result();
        foreach ($regions as $region) {
            $this->getRegion($region->id);
        }
        $this->divide();
        print "Completed in " . $this->showTime(microtime(true) - $startTime) . "\n";
    }

    public function getRegion($region = null)
    {
        if (!$region) {
            return;
        }

        $startTime = microtime(true);

        $name = '';
        $this->db->select('name');
        $this->db->where(['id' => $region]);
        $row = $this->db->get('region')->row();
        if (isset($row)) {
            $name = $row->name;
        }
        $now = new DateTime();
        print "================================================================\n";
        print "{$name} \n";
        print "================================================================\n";
        print " started at " . $now->format('Y-m-d H:i:s') . "\n";

        $this->db->query("DELETE segment, connection
        FROM segment
        JOIN connection ON connection.fromSegment = segment.id
        WHERE segment.region = {$region}");
        print " cleared in " . $this->showTime(microtime(true) - $startTime) . "\n";

        $this->db->where(['region' => $region]);
        $bboxes = $this->db->get('bbox')->result();

        if (count($bboxes) == 0) {
            print " bboxes not found.\n";
            return;
        } else {
            print " bboxes: " . count($bboxes) . "\n";
        }

        $streets = [];
        $cities = [];
        $users = [];

        $this->load->model('api/connection', 'connection');
        $this->load->model('api/segment', 'segment');

        $startTime2 = microtime(true);

        foreach ($bboxes as $bbox) {
            $data = $this->getBBoxData($bbox);

            if (isset($data->errorLevel) && $data->errorLevel == 'ERROR') {
                foreach ($data->errorList as $error) {
                    print "ERROR: {$error->details} \n";
                }
                return;
            }

            if (isset($data->connections)) {
                $chunks = array_chunk((array) $data->connections, 1000, true);
                foreach ($chunks as $chunk) {
                    $this->connection->setDataFromWME($chunk);
                }
            }
            if (isset($data->segments->objects)) {
                $chunks = array_chunk((array) $data->segments->objects, 1000, true);
                foreach ($chunks as $chunk) {
                    $this->segment->setDataFromWME($chunk, $region);
                }
            }
            if (isset($data->streets->objects)) {
                foreach ((array) $data->streets->objects as $key => $value) {
                    $streets[$value->id] = $value;
                }
            }
            if (isset($data->cities->objects)) {
                foreach ((array) $data->cities->objects as $key => $value) {
                    $cities[$value->id] = $value;
                }
            }
            if (isset($data->users->objects)) {
                foreach ((array) $data->users->objects as $key => $value) {
                    $users[$value->id] = $value;
                }
            }
        }
        print " loaded and updated segments with connections in " . $this->showTime(microtime(true) - $startTime2) . "\n";
        
        $startTime2 = microtime(true);
        if (count($streets) > 0) {
            $this->load->model('api/street', 'street');
            $chunks = array_chunk($streets, 1000, true);
            foreach ($chunks as $chunk) {
                $this->street->setDataFromWME($chunk);
            }
        }

        if (count($cities) > 0) {
            $this->load->model('api/city', 'city');
            $chunks = array_chunk($cities, 1000, true);
            foreach ($chunks as $chunk) {
                $this->city->setDataFromWME($chunk);
            }
        }

        if (count($users) > 0) {
            $this->load->model('api/user', 'user');
            $chunks = array_chunk($users, 1000, true);
            foreach ($chunks as $chunk) {
                $this->user->setDataFromWME($chunk);
            }
        }
        print " updated streets, cities and users in " . $this->showTime(microtime(true) - $startTime2) . "\n";

        $this->load->model('api/region', 'region');
        $this->region->finishData($region, $this->country);
        print "----------------------------------------------------------------\n";
        print "{$name} completed in " . $this->showTime(microtime(true) - $startTime) . "\n";
        print "----------------------------------------------------------------\n\n";
    }

    private function getBBoxData($bbox)
    {
        $url = "https://www.waze.com/row-Descartes/app/Features?language=en&bbox={$bbox->west},{$bbox->south},{$bbox->east},{$bbox->north}&roadTypes=1,2,3,4,6,7,8,9,11,12,13,14,15,17,19,20,21,22";
        $curl = curl_init($url);
        curl_setopt($curl, CURLOPT_HEADER, 1);
        curl_setopt($curl, CURLOPT_COOKIEJAR, 'cookies.txt');
        curl_setopt($curl, CURLOPT_COOKIEFILE, 'cookies.txt');
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        $response = curl_exec($curl);
        $header_size = curl_getinfo($curl, CURLINFO_HEADER_SIZE);
        $header = substr($response, 0, $header_size);
        $body = substr($response, $header_size);
        curl_close($curl);

        if ($body) {
            return json_decode($body);
        }

        return null;
    }

    public function setCookie($web_session = "")
    {
        $curl = curl_init('https://www.waze.com/login/get');
        curl_setopt($curl, CURLOPT_HEADER, 1);
        curl_setopt($curl, CURLOPT_COOKIE, "_web_session={$web_session}");
        curl_setopt($curl, CURLOPT_COOKIEJAR, 'cookies.txt');
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        $response = curl_exec($curl);
        $header_size = curl_getinfo($curl, CURLINFO_HEADER_SIZE);
        $header = substr($response, 0, $header_size);
        $body = substr($response, $header_size);
        $data = json_decode($body);
        $username = $data->reply->message;
        if ($username) {
            print "OK: Logged by {$username}\n";
        } else {
            print "ERROR: user is not logged in\n";
        }
        curl_close($curl);
    }

    private function divide()
    {
        print "\n";
        print "================================================================\n";
        print "\n";
    }

    private function showTime($duration)
    {
        $hours = (int) ($duration / 60 / 60);
        $minutes = (int) ($duration / 60) - $hours * 60;
        $seconds = (int) $duration - $hours * 60 * 60 - $minutes * 60;
        return ($hours == 0 ? "00" : $hours) . ":" . ($minutes == 0 ? "00" : ($minutes < 10 ? "0" . $minutes : $minutes)) . ":" . ($seconds == 0 ? "00" : ($seconds < 10 ? "0" . $seconds : $seconds));
    }

}
