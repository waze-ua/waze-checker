<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Migration_Create_statistic_table extends CI_Migration
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
            'region' => [
                'type'     => 'SMALLINT',
                'unsigned' => true,
                'null'     => false,
                'default'  => 0,
            ],
            'type'   => [
                'type'       => 'VARCHAR',
                'constraint' => 50,
                'null'       => false,
                'default'    => '',
            ],
            'value'  => [
                'type'     => 'BIGINT',
                'unsigned' => true,
                'null'     => false,
                'default'  => 0,
            ],
            'date'   => [
                'type' => 'DATE',
                'null' => true,
            ],
        ];

        $this->dbforge->add_field($fields);
        $this->dbforge->add_key('id', true);
        $this->dbforge->create_table('statistic', true);

        echo '<li>up 011 Create_statistic_table</li>';
    }

    public function down()
    {
        $this->dbforge->drop_table('statistic', true);

        echo '<li>down 011 Create_statistic_table</li>';
    }
}
