<?php defined('SYSPATH') OR die('No direct access allowed.');
/**
 * Tabella di configurazione globale
 *
 * @package    TRACKOID/ORM
 * @author     Gis3w Team
 * @copyright  (c) 2012 Gis3w Team
 */
class Model_Background_Layer extends ORM {
    
    protected $_has_many = array(
        'sections' => array(
            'model'   => 'Section',
            'through' => 'sections_background_layers',
        ),
    );
    
    public function rules()
    {
        return array(
            'name' => array(
                array('not_empty'),
            ),
            'url' => array(
                array('not_empty'),

            ),
             'def' => array(
                array('not_empty'),

            ),
            
        );
    }
    
     public function extra_rules()
    {
        return array(
            'sections' => array(
                array('not_empty'),
            ),
        );
    }
    
    public function getLayersBySection($section)
    {
        $section = ORM::factory('Section')->where('section','=',$section)->find();
        return $this->join('sections_background_layers')
                ->on('sections_background_layers.background_layer_id','=','background_layer.id')
                ->where('sections_background_layers.section_id','=',$section->id)
                ->find_all();
    }
    
}