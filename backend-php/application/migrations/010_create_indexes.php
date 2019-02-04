<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Migration_Create_indexes extends CI_Migration
{
    public function __construct()
    {
        parent::__construct();
        $this->load->dbforge();
    }

    public function up()
    {
        $this->db->query('CREATE INDEX `idx_city_country` ON `city` (country)');
        $this->db->query('CREATE INDEX `idx_city_isEmpty` ON `city` (isEmpty)');

        $this->db->query('CREATE INDEX `idx_street_isEmpty` ON `street` (isEmpty)');
        $this->db->query('CREATE INDEX `idx_street_city` ON `street` (city)');
        
        $this->db->query('CREATE INDEX `idx_segment_createdBy` ON `segment` (createdBy)');
        $this->db->query('CREATE INDEX `idx_segment_updatedBy` ON `segment` (updatedBy)');
        $this->db->query('CREATE INDEX `idx_segment_region` ON `segment` (region)');
        $this->db->query('CREATE INDEX `idx_segment_fwdDirection` ON `segment` (fwdDirection)');
        $this->db->query('CREATE INDEX `idx_segment_revDirection` ON `segment` (revDirection)');
        $this->db->query('CREATE INDEX `idx_segment_fwdDirection_revDirection` ON `segment` (fwdDirection, revDirection)');
        $this->db->query('CREATE INDEX `idx_segment_fwdSpeed` ON `segment` (fwdDirection)');
        $this->db->query('CREATE INDEX `idx_segment_revSpeed` ON `segment` (revDirection)');
        $this->db->query('CREATE INDEX `idx_segment_lockRank` ON `segment` (lockRank)');

        $this->db->query('CREATE INDEX `idx_segment_roadType` ON `segment` (roadType)');

        $this->db->query('CREATE UNIQUE INDEX `idx_connection_fromSegment_toSegment_direction` ON `connection` (fromSegment, toSegment, direction)');
        $this->db->query('CREATE INDEX `idx_connection_fromSegment` ON `connection` (fromSegment)');

        echo '<li>up 010 Create_indexes</li>';
    }

    public function down()
    {

        $this->db->query('DROP INDEX `idx_city_country` ON `city`');
        $this->db->query('DROP INDEX `idx_city_isEmpty` ON `city`');

        $this->db->query('DROP INDEX `idx_street_isEmpty` ON `street`');
        $this->db->query('DROP INDEX `idx_street_city` ON `street`');
        
        $this->db->query('DROP INDEX `idx_segment_createdBy` ON `segment`');
        $this->db->query('DROP INDEX `idx_segment_updatedBy` ON `segment`');
        $this->db->query('DROP INDEX `idx_segment_fwdDirection` ON `segment`');
        $this->db->query('DROP INDEX `idx_segment_revDirection` ON `segment`');
        $this->db->query('DROP INDEX `idx_segment_fwdDirection_revDirection` ON `segment`');
        $this->db->query('DROP INDEX `idx_segment_fwdSpeed` ON `segment`');
        $this->db->query('DROP INDEX `idx_segment_revSpeed` ON `segment`');
        $this->db->query('DROP INDEX `idx_segment_lockRank` ON `segment`');
        $this->db->query('DROP INDEX `idx_segment_region` ON `segment`');

        $this->db->query('DROP INDEX `idx_segment_roadType` ON `segment`');
        $this->db->query('DROP INDEX `idx_connection_fromSegment_toSegment_direction` ON `connection`');
        $this->db->query('DROP INDEX `idx_connection_fromSegment` ON `connection`');

        echo '<li>down 010 Create_indexes</li>';
    }
}
