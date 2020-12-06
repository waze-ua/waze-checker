<?php
defined('BASEPATH') or exit('No direct script access allowed');

class WmeData extends CI_Controller
{
    private $country = 232;

    public function getAllRegions($date = null)
    {
        $startTime = microtime(true);
        $now = new DateTime();
        print "Started all at " . $now->format('Y-m-d H:i:s') . "\n";

        if (!$date) {
            $this->db->query('TRUNCATE TABLE segment');
            $this->db->query('TRUNCATE TABLE connection');
            $this->db->query('TRUNCATE TABLE street');
            $this->db->query('TRUNCATE TABLE city');
            $this->db->query('TRUNCATE TABLE user');
            print "Cleared in " . $this->showTime(microtime(true) - $startTime) . "\n";
        }

        $this->deleteIndexes();

        $this->db->select(['id', 'name']);
        if ($date) {
            $this->db->where("DATE(FROM_UNIXTIME((lastUpdate / 1000))) <= DATE('{$date}')");
        }
        $regions = $this->db->get('region')->result();

        foreach ($regions as $region) {
            $this->getRegion($region->id, true);
            $this->updateRegionStats($region->id, $region->name);
        }

        $this->createIndexes();
        $this->deleteExtraRows();
        foreach ($regions as $region) {
            $this->finalizeRegion($region->id, $region->name);
        }
        $this->updateUsers();

        $this->divide();
        print "Completed in " . $this->showTime(microtime(true) - $startTime) . "\n";
    }

    public function getRegion($region = null, $all = false)
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

        if (!$all) {
            $this->db->query("DELETE connection FROM connection
            JOIN segment ON connection.fromSegment = segment.id
            WHERE segment.region = {$region}");

            $this->db->query("DELETE FROM segment WHERE region = {$region}");

            print " cleared in " . $this->showTime(microtime(true) - $startTime) . "\n";
        }

        !$all && $this->deleteIndexes();

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

        foreach ($bboxes as $index => $bbox) {
            print chr(27) . "[0G";
            print " bbox: " . count($bboxes) . "/" . ($index + 1);

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
        print "\n loaded and updated segments with connections in " . $this->showTime(microtime(true) - $startTime2) . "\n";

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

        if (!$all) {
            $this->createIndexes();
            $this->deleteExtraRows();
            $this->finalizeRegion($region);
            $this->updateUsers();
        }

        print "----------------------------------------------------------------\n";
        print "{$name} completed in " . $this->showTime(microtime(true) - $startTime) . "\n";
        print "----------------------------------------------------------------\n\n";
    }

    private function getBBoxData($bbox)
    {
        $url = "https://www.waze.com/row-Descartes/app/Features?language=en&bbox={$bbox->west},{$bbox->south},{$bbox->east},{$bbox->north}&roadTypes=1,2,3,4,6,7,8,9,11,12,13,14,15,17,20,21,22";
        $curl = curl_init($url);
        curl_setopt($curl, CURLOPT_HEADER, 1);
        curl_setopt($curl, CURLOPT_COOKIEJAR, 'cookies.txt');
        curl_setopt($curl, CURLOPT_COOKIEFILE, 'cookies.txt');
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        $data = $this->getResponseBody($curl);
        curl_close($curl);


        return $data;
    }

    public function setCookie($web_session = "", $csrf_token = "")
    {
        $curl = curl_init('https://www.waze.com/login/get');
        curl_setopt($curl, CURLOPT_HEADER, 1);
        curl_setopt($curl, CURLOPT_COOKIE, "_web_session={$web_session};_csrf_token={$csrf_token}");
        curl_setopt($curl, CURLOPT_COOKIEJAR, 'cookies.txt');
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        $data = $this->getResponseBody($curl);
        curl_close($curl);

        if ($data) {
            $username = $data->reply->message;
            if ($username) {
                return print "OK: Logged by {$username}\n";
            }
        }

        print "ERROR: user is not logged in\n";
    }

    public function refreshCookie()
    {
        $curl = curl_init('https://www.waze.com/login/get');
        curl_setopt($curl, CURLOPT_HEADER, 1);
        curl_setopt($curl, CURLOPT_COOKIEJAR, 'cookies.txt');
        curl_setopt($curl, CURLOPT_COOKIEFILE, 'cookies.txt');
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);

        $data = $this->getResponseBody($curl);
        curl_close($curl);

        if ($data) {
            $username = $data->reply->message;
            if ($username) {
                return print "OK: cookie updated for {$username}\n";
            }
        }

        print "ERROR: user is not logged in\n";
    }

    private function getResponseBody($curl)
    {
        $response = curl_exec($curl);
        $header_size = curl_getinfo($curl, CURLINFO_HEADER_SIZE);
        $body = substr($response, $header_size);

        if ($body) {
            return json_decode($body);
        }

        return null;
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

    private function createIndexes()
    {
        $startTime = microtime(true);

        $this->db->simple_query('CREATE INDEX `idx_city_country` ON `city` (country)');
        $this->db->simple_query('CREATE INDEX `idx_city_isEmpty` ON `city` (isEmpty)');

        $this->db->simple_query('CREATE INDEX `idx_street_isEmpty` ON `street` (isEmpty)');
        $this->db->simple_query('CREATE INDEX `idx_street_city` ON `street` (city)');

        $this->db->simple_query('CREATE INDEX `idx_segment_lockRank` ON `segment` (lockRank)');
        $this->db->simple_query('CREATE INDEX `idx_segment_region` ON `segment` (region)');
        $this->db->simple_query('CREATE INDEX `idx_segment_roadType` ON `segment` (roadType)');

        print " indexes was created " . $this->showTime(microtime(true) - $startTime) . "\n";
    }

    private function deleteIndexes()
    {
        $startTime = microtime(true);

        $this->db->simple_query('DROP INDEX `idx_city_country` ON `city`');
        $this->db->simple_query('DROP INDEX `idx_city_isEmpty` ON `city`');

        $this->db->simple_query('DROP INDEX `idx_street_isEmpty` ON `street`');
        $this->db->simple_query('DROP INDEX `idx_street_city` ON `street`');

        $this->db->simple_query('DROP INDEX `idx_segment_lockRank` ON `segment`');
        $this->db->simple_query('DROP INDEX `idx_segment_region` ON `segment`');
        $this->db->simple_query('DROP INDEX `idx_segment_roadType` ON `segment`');

        print " indexes was deleted " . $this->showTime(microtime(true) - $startTime) . "\n";
    }

    private function finalizeRegion($region, $name = '')
    {
        set_time_limit(0);

        $this->findNotConnectedSegments($region, $name);
        $this->findWithoutTurnsSegments($region, $name);
        $this->updateRegionStats($region, $name);

        if ($name !== '') {
            $this->divide();
        }
    }

    private function updateRegionStats($region, $name)
    {
        $startTime = microtime(true);

        $this->db->set(['lastUpdate' => (int) (microtime(true) * 1000)]);
        $this->db->where(['id' => $region]);
        $this->db->update('region');

        $this->load->model('api/region');

        $this->region->updateRegionStats($region);

        $preffix = $name === '' ? '' : " # {$name}: ";
        print $preffix . " updated stats in " . $this->showTime(microtime(true) - $startTime) . "\n";
    }

    private function findNotConnectedSegments($region, $name)
    {
        $startTime = microtime(true);

        $this->db->query("UPDATE segment SET segment.notConnected = 1
            WHERE segment.region = {$region} AND segment.id NOT IN
            (SELECT c.fromSegment
              FROM connection c
              WHERE c.fromSegment = segment.id)");

        $preffix = $name === '' ? '' : " # {$name}: ";
        print $preffix . " not connected segments was found in " . $this->showTime(microtime(true) - $startTime) . "\n";
    }

    private function findWithoutTurnsSegments($region, $name)
    {
        $startTime = microtime(true);

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
            $chunks = array_chunk($ids, 1000, true);
            foreach ($chunks as $chunk) {
                $this->db->set(['withoutTurns' => 1]);
                $this->db->where_in('id', $chunk);
                $this->db->update('segment');
            }
        }

        $preffix = $name === '' ? '' : " # {$name}: ";
        print $preffix . " segments without turns was found in " . $this->showTime(microtime(true) - $startTime) . "\n";
    }

    private function updateUsers()
    {
        $startTime = microtime(true);

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

        print  " updated users in " . $this->showTime(microtime(true) - $startTime) . "\n";
    }

    private function deleteExtraRows()
    {
        $startTime = microtime(true);

        $this->db->query("DELETE segment, connection, street, city
        FROM segment
        JOIN connection ON connection.fromSegment = segment.id
        JOIN street ON street.id = segment.street
        JOIN city ON city.id = street.city
        WHERE city.country != {$this->country}");

        print " deleted extra rows in " . $this->showTime(microtime(true) - $startTime) . "\n";
    }
}
