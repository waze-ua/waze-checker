<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Migration_Create_road_type_table extends CI_Migration
{
    public function __construct()
    {
        parent::__construct();
        $this->load->dbforge();
    }

    public function up()
    {
        $fields = [
            'id'   => [
                'type'           => 'INT',
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'name' => [
                'type'       => 'VARCHAR',
                'constraint' => 50,
                'null'       => false,
                'default'    => '',
            ],
        ];
        $this->dbforge->add_field($fields);
        $this->dbforge->add_key('id', true);
        $this->dbforge->create_table('road_type', true);

        $roadTypes = [
            [
                'id'   => 1,
                'name' => 'Street',
            ],
            [
                'id'   => 2,
                'name' => 'Primary Street',
            ],
            [
                'id'   => 3,
                'name' => 'Freeway',
            ],
            [
                'id'   => 4,
                'name' => 'Ramp',
            ],
            [
                'id'   => 5,
                'name' => 'Major Highway',
            ],
            [
                'id'   => 6,
                'name' => 'Walking Trail',
            ],
            [
                'id'   => 7,
                'name' => 'Minor Highway',
            ],
            [
                'id'   => 8,
                'name' => 'Off-road / Not maintained',
            ],
            [
                'id'   => 10,
                'name' => 'Pedestrian Boardwalk',
            ],
            [
                'id'   => 15,
                'name' => 'Ferry',
            ],
            [
                'id'   => 16,
                'name' => 'Stairway',
            ],
            [
                'id'   => 17,
                'name' => 'Private Road',
            ],
            [
                'id'   => 18,
                'name' => 'Railroad',
            ],
            [
                'id'   => 19,
                'name' => 'Runway/Taxiway',
            ],
            [
                'id'   => 20,
                'name' => 'Parking Lot Road',
            ],
            [
                'id'   => 22,
                'name' => 'Narrow Street',
            ],
        ];

        $this->db->insert_batch('road_type', $roadTypes);

        echo '<li>up 008 Create_road_type_table</li>';
    }

    public function down()
    {
        $this->dbforge->drop_table('road_type', true);
        echo '<li>down 008 Create_road_type_table</li>';
    }
}
