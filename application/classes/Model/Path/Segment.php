<?php defined('SYSPATH') OR die('No direct access allowed.');

class Model_Path_Segment extends ORMGIS
{
    protected $_primary_key = 'gid';

    public $geotype = ORMGIS::TP_MULTILINESTRING;

    public $epsg_db = 3004;
    public $epsg_out = 4326;

    protected $_belongs_to = array(
        'class_ril_desc' => array(
            'model' => 'Class_Ril_Segment',
            'foreign_key' => 'class_ril',
        ),
        'tp_trat_desc' => array(
            'model' => 'Tp_Trat_Segment',
            'foreign_key' => 'tp_trat',
        ),

    );

    public function rules()
    {
        return array(
            'the_geom' =>array(
                array('not_empty'),
            ),

        );
    }

}