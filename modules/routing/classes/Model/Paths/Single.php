<?php defined('SYSPATH') OR die('No direct access allowed.');

class Model_Paths_Single extends ORMGIS
{
    public $geotype = ORMGIS::TP_LINESTRING;
    public $epsg_db = 3004;
    public $epsg_out = 4326;

    protected $_table_name = 'paths_single';
}