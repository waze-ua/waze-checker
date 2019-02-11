<?php
defined('BASEPATH') or exit('No direct script access allowed');
use Elkuku\Console\Helper\ConsoleProgressBar;

class WmeData extends CI_Controller
{

    private $country = 37;

    public function getAllRegions()
    {
        $this->db->select('id');
        $regions = $this->db->get('region')->result();
        foreach ($regions as $region) {
            $this->getRegion($region->id);
        }
    }

    public function getRegion($region = null)
    {
        if (!$region) {
            return;
        }

        $this->db->query("DELETE segment, connection
        FROM segment
        LEFT JOIN connection ON connection.fromSegment = segment.id
        WHERE segment.region = {$region}");

        $name = '';
        $this->db->select('name');
        $this->db->where(['id' => $region]);
        $row = $this->db->get('region')->row();
        if (isset($row)) {
            $name = $row->name;
        }

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

        $bar = new ConsoleProgressBar("{$name}: Loading data %fraction% [%bar%] %percent% %elapsed%", '=>', '-', 120, count($bboxes));
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
                $connections = array_merge($connections, (array) $data->connections);
            }
            if (isset($data->segments->objects)) {
                foreach ((array) $data->segments->objects as $key => $value) {
                    $segments[$value->id] = $value;
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
            $bar->update($i++);
        }
        print "\n";

        if (count($connections) > 0) {
            $this->load->model('api/connection', 'connection');
            $connections = array_unique($connections, SORT_REGULAR);
            $chunks = array_chunk($connections, 1000, true);
            $bar->reset("{$name}: Updating connections %fraction% [%bar%] %percent% %elapsed%", '=>', '-', 120, count($chunks));
            $i = 1;
            foreach ($chunks as $chunk) {
                $this->connection->setDataFromWME($chunk);
                $bar->update($i++);
            }
            print "\n";
        }

        if (count($segments) > 0) {
            $this->load->model('api/segment', 'segment');
            $chunks = array_chunk($segments, 1000, true);
            $bar->reset("{$name}: Updating segments %fraction% [%bar%] %percent% %elapsed%", '=>', '-', 120, count($chunks));
            $i = 1;
            foreach ($chunks as $chunk) {
                $this->segment->setDataFromWME($chunk, $region);
                $bar->update($i++);
            }
            print "\n";
        }

        if (count($streets) > 0) {
            $this->load->model('api/street', 'street');
            $chunks = array_chunk($streets, 1000, true);
            $bar->reset("{$name}: Updating streets %fraction% [%bar%] %percent% %elapsed%", '=>', '-', 120, count($chunks));
            $i = 1;
            foreach ($chunks as $chunk) {
                $this->street->setDataFromWME($chunk);
                $bar->update($i++);
            }
            print "\n";
        }

        if (count($streets) > 0) {
            $this->load->model('api/city', 'city');
            $chunks = array_chunk($cities, 1000, true);
            $bar->reset("{$name}: Updating cities %fraction% [%bar%] %percent% %elapsed%", '=>', '-', 120, count($chunks));
            $i = 1;
            foreach ($chunks as $chunk) {
                $this->city->setDataFromWME($chunk);
                $bar->update($i++);
            }
            print "\n";
        }

        if (count($users) > 0) {
            $this->load->model('api/user', 'user');
            $chunks = array_chunk($users, 1000, true);
            $bar->reset("{$name}: Updating users %fraction% [%bar%] %percent% %elapsed%", '=>', '-', 120, count($chunks));
            $i = 1;
            foreach ($chunks as $chunk) {
                $this->user->setDataFromWME($chunk);
                $bar->update($i++);
            }
            print "\n";
        }

        $this->load->model('api/region', 'region');
        $this->region->finishData($region, $this->country);
        $this->divide();
    }

    private function getBBoxData($bbox)
    {
        $url = "https://www.waze.com/row-Descartes/app/Features?language=en&bbox={$bbox->west},{$bbox->south},{$bbox->east},{$bbox->north}&roadTypes=1,2,3,4,5,6,7,8,9,10,11,12,13,14,15,16,17,18,19,20,21,22";
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
        print "===================================================================================================\n";
        print "\n";
    }

}
