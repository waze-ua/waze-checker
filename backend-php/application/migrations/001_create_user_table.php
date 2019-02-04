<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Migration_Create_user_table extends CI_Migration
{
    public function __construct()
    {
        parent::__construct();
        $this->load->dbforge();
    }

    public function up()
    {
        $fields = [
            'id'        => [
                'type'    => 'INT',
                'null'    => false,
                'default' => 0,
            ],
            'rank'      => [
                'type'    => 'TINYINT',
                'null'    => false,
                'default' => 0,
            ],
            'userName'  => [
                'type'       => 'VARCHAR',
                'constraint' => 255,
                'null'       => false,
                'default'    => '',
            ],
            'firstEdit' => [
                'type'     => 'BIGINT',
                'unsigned' => true,
                'null'     => false,
                'default'  => 0,
            ],
            'lastEdit'  => [
                'type'     => 'BIGINT',
                'unsigned' => true,
                'null'     => false,
                'default'  => 0,
            ],
        ];
        $this->dbforge->add_field($fields);
        $this->dbforge->add_key('id', true);
        $this->dbforge->create_table('user', true);

        echo '<li>up 001 Create_user_table</li>';
    }

    public function down()
    {
        $this->dbforge->drop_table('user', true);
        echo '<li>down 001 Create_user_table</li>';
    }
}
