<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Migration_Add_notConnected_column_to_segment extends CI_Migration
{
    public function __construct()
    {
        parent::__construct();
        $this->load->dbforge();
    }

    public function up()
    {
        $fields = [
            'notConnected' => [
                'type' => 'SMALLINT',
                'unsigned' => true,
                'null' => false,
                'default' => 0,
            ],
        ];

        $this->dbforge->add_column('segment', $fields);

        echo '<li>up 012 Add_notConnected_column_to_segment</li>';
    }

    public function down()
    {
        $this->dbforge->drop_column('segment', 'notConnected');

        echo '<li>down 012 Add_notConnected_column_to_segment</li>';
    }
}
