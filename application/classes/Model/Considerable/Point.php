<?php defined('SYSPATH') OR die('No direct access allowed.');

class Model_Considerable_Point extends ORMGIS
{

    protected $_primary_key = 'gid';

    public $geotype = ORMGIS::TP_MULTIPOINT;

    public $epsg_db = 3004;
    public $epsg_out = 4326;

}