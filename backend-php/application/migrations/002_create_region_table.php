<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Migration_Create_region_table extends CI_Migration
{
    public function __construct()
    {
        parent::__construct();
        $this->load->dbforge();
    }

    public function up()
    {
        $fields = [
            'id'         => [
                'type'           => 'INT',
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'name'       => [
                'type'       => 'VARCHAR',
                'constraint' => 50,
                'null'       => false,
                'default'    => '',
            ],
            'polygon'    => [
                'type' => 'POLYGON',
                'null' => true,
            ],
            'lastUpdate' => [
                'type'     => 'BIGINT',
                'unsigned' => true,
                'null'     => false,
                'default'  => 0,
            ],
        ];
        $this->dbforge->add_field($fields);
        $this->dbforge->add_key('id', true);
        $this->dbforge->create_table('region', true);

        echo '<li>up 002 Create_region_table</li>';
    }

    public function down()
    {
        $this->dbforge->drop_table('region', true);
        echo '<li>down 002 Create_region_table</li>';
    }
}
