<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Migration_Create_city_table extends CI_Migration
{
    public function __construct()
    {
        parent::__construct();
        $this->load->dbforge();
    }

    public function up()
    {
        $fields = [
            'id'      => [
                'type'     => 'INT',
                'unsigned' => true,
                'null'     => false,
                'default'  => 0,
            ],
            'name'    => [
                'type'       => 'VARCHAR',
                'constraint' => 255,
                'null'       => true,
            ],
            'country' => [
                'type'     => 'INT',
                'unsigned' => true,
                'null'     => false,
                'default'  => 0,
            ],
            'isEmpty' => [
                'type'     => 'TINYINT',
                'unsigned' => true,
                'null'     => false,
                'default'  => 0,
            ],
            'lon'     => [
                'type'    => 'DECIMAL(11, 8)',
                'null'    => false,
                'default' => 0,
            ],
            'lat'     => [
                'type'    => 'DECIMAL(10, 8)',
                'null'    => false,
                'default' => 0,
            ],
        ];
        $this->dbforge->add_field($fields);
        $this->dbforge->add_key('id', true);
        $this->dbforge->create_table('city', true);

        echo '<li>up 005 Create_city_table</li>';
    }

    public function down()
    {
        $this->dbforge->drop_table('city', true);
        echo '<li>down 005 Create_city_table</li>';
    }
}
