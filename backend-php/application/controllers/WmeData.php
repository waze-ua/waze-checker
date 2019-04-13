<?php
defined('BASEPATH') or exit('No direct script access allowed');

use Elkuku\Console\Helper\ConsoleProgressBar;

class WmeData extends CI_Controller
{
    private $country = 37;

    public function getAllRegions($showProgressBar = false)
    {
        $startTime = microtime(true);

        $this->db->query('TRUNCATE TABLE segment');
        $this->db->query('TRUNCATE TABLE connection');
        $this->db->query('TRUNCATE TABLE street');
        $this->db->query('TRUNCATE TABLE city');
        $this->db->query('TRUNCATE TABLE user');

        $this->db->select('id');
        $regions = $this->db->get('region')->result();
        foreach ($regions as $region) {
            $this->getRegion($region->id, $showProgressBar);
        }
        print "All: " . $this->showTime(microtime(true) - $startTime) . "\n";
    }

    public function getRegion($region = null, $showProgressBar = false)
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

        if (!$showProgressBar) {
            print $name . ":\n";
        }

        $this->db->query("DELETE segment, connection
        FROM segment
        JOIN connection ON connection.fromSegment = segment.id
        WHERE segment.region = {$region}");

        $this->db->where(['region' => $region]);
        $bboxes = $this->db->get('bbox')->result();

        if (count($bboxes) == 0) {
            print "{$name}:  bboxes not found.\n";
            $this->divide();
            return;
        }

        $connections = [];
        $segments = [];
        $streets = [];
        $cities = [];
        $users = [];
        $bar;

        $this->load->model('api/connection', 'connection');
        $this->load->model('api/segment', 'segment');

        $startTime2 = microtime(true);
        if ($showProgressBar) {
            $bar = new ConsoleProgressBar("{$name}: Loading and saving %fraction% [%bar%] %percent% %elapsed%", '=>', '-', 100, count($bboxes));
        }

        $i = 1;
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
            if ($showProgressBar) {
                $bar->update($i++);
            }
        }
        if (!$showProgressBar) {
            print "  Load data: " . $this->showTime(microtime(true) - $startTime2) . "\n";
        }

        if (count($streets) > 0) {
            $startTime2 = microtime(true);
            $this->load->model('api/street', 'street');
            $chunks = array_chunk($streets, 1000, true);
            if ($showProgressBar) {
                $bar->reset("\n{$name}: Updating streets %fraction% [%bar%] %percent% %elapsed%", '=>', '-', 100, count($chunks));
            }
            $i = 1;
            foreach ($chunks as $chunk) {
                $this->street->setDataFromWME($chunk);
                if ($showProgressBar) {
                    $bar->update($i++);
                }
            }
            if (!$showProgressBar) {
                print "  Update streets: " . $this->showTime(microtime(true) - $startTime2) . "\n";
            }
        }

        if (count($streets) > 0) {
            $startTime2 = microtime(true);
            $this->load->model('api/city', 'city');
            $chunks = array_chunk($cities, 1000, true);
            if ($showProgressBar) {
            $bar->reset("\n{$name}: Updating cities %fraction% [%bar%] %percent% %elapsed%", '=>', '-', 100, count($chunks));
            }
            $i = 1;
            foreach ($chunks as $chunk) {
                $this->city->setDataFromWME($chunk);
                if ($showProgressBar) {
                    $bar->update($i++);
                }
            }
            if (!$showProgressBar) {
                print "  Update cities: " . $this->showTime(microtime(true) - $startTime2) . "\n";
            }
        }

        if (count($users) > 0) {
            $startTime2 = microtime(true);
            $this->load->model('api/user', 'user');
            $chunks = array_chunk($users, 1000, true);
            if ($showProgressBar) {
                $bar->reset("\n{$name}: Updating users %fraction% [%bar%] %percent% %elapsed%", '=>', '-', 100, count($chunks));
            }
            $i = 1;
            foreach ($chunks as $chunk) {
                $this->user->setDataFromWME($chunk);
                if ($showProgressBar) {
                    $bar->update($i++);
                }
            }

            if (!$showProgressBar) {
                print "  Update users: " . $this->showTime(microtime(true) - $startTime2) . "\n";
            }
        }

        $this->load->model('api/region', 'region');
        $this->region->finishData($region, $this->country);

        print "\n{$name}: " . $this->showTime(microtime(true) - $startTime);
        $this->divide();
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
        print "========================================================================\n";
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
