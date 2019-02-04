<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Migration_Create_segment_table extends CI_Migration
{
    public function __construct()
    {
        parent::__construct();
        $this->load->dbforge();
    }

    public function up()
    {
        $fields = [
            'id'                    => [
                'type'     => 'INT',
                'unsigned' => true,
                'null'     => false,
                'default'  => 0,
            ],
            'allowNoDirection'      => [
                'type'     => 'TINYINT',
                'unsigned' => true,
                'null'     => false,
                'default'  => 0,
            ],
            'createdBy'             => [
                'type'    => 'INT',
                'null'    => false,
                'default' => 0,
            ],
            'createdOn'             => [
                'type'     => 'BIGINT',
                'unsigned' => true,
                'null'     => false,
                'default'  => 0,
            ],
            'flags'                 => [
                'type'    => 'TINYINT',
                'null'    => false,
                'default' => 0,
            ],
            'fromNodeId'            => [
                'type'     => 'INT',
                'unsigned' => true,
                'null'     => false,
                'default'  => 0,
            ],
            'fwdDirection'          => [
                'type'     => 'TINYINT',
                'unsigned' => true,
                'null'     => false,
                'default'  => 0,
            ],
            'fwdFlags'              => [
                'type'    => 'TINYINT',
                'null'    => false,
                'default' => 0,
            ],
            'fwdMaxSpeed'           => [
                'type'     => 'TINYINT',
                'unsigned' => true,
                'null'     => false,
                'default'  => 0,
            ],
            'fwdMaxSpeedUnverified' => [
                'type'     => 'TINYINT',
                'unsigned' => true,
                'null'     => false,
                'default'  => 0,
            ],
            'fwdToll'               => [
                'type'     => 'TINYINT',
                'unsigned' => true,
                'null'     => false,
                'default'  => 0,
            ],
            'fwdTurnsLocked'        => [
                'type'     => 'TINYINT',
                'unsigned' => true,
                'null'     => false,
                'default'  => 0,
            ],
            'hasClosures'           => [
                'type'     => 'TINYINT',
                'unsigned' => true,
                'null'     => false,
                'default'  => 0,
            ],
            'hasHNs'                => [
                'type'     => 'TINYINT',
                'unsigned' => true,
                'null'     => false,
                'default'  => 0,
            ],
            'length'                => [
                'type'     => 'SMALLINT',
                'unsigned' => true,
                'null'     => false,
                'default'  => 0,
            ],
            'level'                 => [
                'type'     => 'TINYINT',
                'unsigned' => true,
                'null'     => false,
                'default'  => 0,
            ],
            'lockRank'              => [
                'type'     => 'TINYINT',
                'unsigned' => true,
                'null'     => true,
            ],
            'street'                => [
                'type'     => 'INT',
                'unsigned' => true,
                'null'     => false,
                'default'  => 0,
            ],
            'rank'                  => [
                'type'     => 'TINYINT',
                'unsigned' => true,
                'null'     => false,
                'default'  => 0,
            ],
            'revDirection'          => [
                'type'     => 'TINYINT',
                'unsigned' => true,
                'null'     => false,
                'default'  => 0,
            ],
            'revFlags'              => [
                'type'    => 'TINYINT',
                'null'    => false,
                'default' => 0,
            ],
            'revMaxSpeed'           => [
                'type'     => 'TINYINT',
                'unsigned' => true,
                'null'     => false,
                'default'  => 0,
            ],
            'revMaxSpeedUnverified' => [
                'type'     => 'TINYINT',
                'unsigned' => true,
                'null'     => false,
                'default'  => 0,
            ],
            'revToll'               => [
                'type'     => 'TINYINT',
                'unsigned' => true,
                'null'     => false,
                'default'  => 0,
            ],
            'revTurnsLocked'        => [
                'type'     => 'TINYINT',
                'unsigned' => true,
                'null'     => false,
                'default'  => 0,
            ],
            'roadType'              => [
                'type'     => 'TINYINT',
                'unsigned' => true,
                'null'     => false,
                'default'  => 0,
            ],
            'routingRoadType'       => [
                'type'     => 'TINYINT',
                'unsigned' => true,
                'null'     => false,
                'default'  => 0,
            ],
            'separator'             => [
                'type'     => 'TINYINT',
                'unsigned' => true,
                'null'     => false,
                'default'  => 0,
            ],
            'toNodeId'              => [
                'type'    => 'INT',
                'null'    => false,
                'default' => 0,
            ],
            'updatedBy'             => [
                'type'    => 'INT',
                'null'    => false,
                'default' => 0,
            ],
            'updatedOn'             => [
                'type'     => 'BIGINT',
                'unsigned' => true,
                'null'     => false,
                'default'  => 0,
            ],
            'validated'             => [
                'type'     => 'TINYINT',
                'unsigned' => true,
                'null'     => false,
                'default'  => 0,
            ],
            'coordinates'           => [
                'type' => 'LINESTRING',
                'null' => true,
            ],
            'lon'                   => [
                'type'    => 'DECIMAL(11, 8)',
                'null'    => false,
                'default' => 0,
            ],
            'lat'                   => [
                'type'    => 'DECIMAL(10, 8)',
                'null'    => false,
                'default' => 0,
            ],
            'hasTransition'         => [
                'type'     => 'TINYINT',
                'unsigned' => true,
                'null'     => false,
                'default'  => 0,
            ],
            'startPoint'            => [
                'type'       => 'VARCHAR',
                'constraint' => 25,
                'null'       => false,
                'default'    => '',
            ],
            'endPoint'              => [
                'type'       => 'VARCHAR',
                'constraint' => 25,
                'null'       => false,
                'default'    => '',
            ],
            'region'                => [
                'type'     => 'INT',
                'unsigned' => true,
                'null'     => false,
                'default'  => 0,
            ],
        ];
        $this->dbforge->add_field($fields);
        $this->dbforge->add_key('id', true);
        $this->dbforge->create_table('segment', true);

        echo '<li>up 007 Create_segment_table</li>';
    }

    public function down()
    {
        $this->dbforge->drop_table('segment', true);
        echo '<li>down 007 Create_segment_table</li>';
    }
}
