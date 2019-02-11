<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Migration_Insert_bboxes extends CI_Migration
{
    public function __construct()
    {
        parent::__construct();
        $this->load->helper('file');
    }

    public function up()
    {
        $json_files = get_filenames('./boxes/', false);
        foreach ($json_files as $file) {
            $jsonFile = read_file('./boxes/' . $file);
            $pathinfo = pathinfo($file);
            if ($pathinfo['extension'] === 'json') {
                $bboxex = json_decode($jsonFile);
                $this->db->insert_batch('bbox', $bboxex);
            }
        }

        echo '<li>up 004 Insert_bboxes</li>';

    }

    public function down()
    {
        $this->db->query('TRUNCATE TABLE bbox');
        echo '<li>down 004 Insert_bboxes</li>';
    }
}
