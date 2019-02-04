<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Migration_Create_bbox_table extends CI_Migration
{
    public function __construct()
    {
        parent::__construct();
        $this->load->dbforge();
    }

    public function up()
    {
        $fields = [
            'id'     => [
                'type'           => 'INT',
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'south'  => [
                'type'    => 'DECIMAL(10, 8)',
                'null'    => false,
                'default' => 0,
            ],
            'west'   => [
                'type'    => 'DECIMAL(11, 8)',
                'null'    => false,
                'default' => 0,
            ],
            'north'  => [
                'type'    => 'DECIMAL(10, 8)',
                'null'    => false,
                'default' => 0,
            ],
            'east'   => [
                'type'    => 'DECIMAL(11, 8)',
                'null'    => false,
                'default' => 0,
            ],
            'region' => [
                'type'    => 'SMALLINT',
                'null'    => false,
                'default' => 0,
            ],
        ];
        $this->dbforge->add_field($fields);
        $this->dbforge->add_key('id', true);
        $this->dbforge->create_table('bbox', true);

        echo '<li>up 003 Create_region_table</li>';
    }

    public function down()
    {
        $this->dbforge->drop_table('bbox', true);
        echo '<li>down 003 Create_region_table</li>';
    }
}
