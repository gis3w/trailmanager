<?php defined('SYSPATH') OR die('No direct access allowed.');

class Model_Highliting_Typology extends ORM {
    
    
    protected $_has_many = array(
        'highlitingpois' => array(
            'model'   => 'Highliting_Poi',
        ),
        'sections' => array(
         'model'   => 'Section',
         'through' => 'sections_highliting_typologies',
        ),
    );

    
    public function labels() {
        return array(
            "name" => __("Name"),
            "description" => __("Description"),
        );
    }
    
    public function rules()
    {
        return array(
            'name' => array(
                    array('not_empty'),
            ),
            'icon' => array(
                    array('not_empty'),
            ),
        );
    }

    public function getLayersBySection($section)
    {
        $section = ORM::factory('Section')->where('section','=',$section)->find();
        return $this->join('sections_highliting_typologies')
            ->on('sections_highliting_typologies.highliting_typology_id','=','highliting_typology.id')
            ->where('sections_highliting_typologies.section_id','=',$section->id)
            ->find_all();
    }

}