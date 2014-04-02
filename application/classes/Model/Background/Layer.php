<?php defined('SYSPATH') OR die('No direct access allowed.');
/**
 * Tabella di configurazione globale
 *
 * @package    TRACKOID/ORM
 * @author     Gis3w Team
 * @copyright  (c) 2012 Gis3w Team
 */
class Model_Background_Layer extends ORM {
    
    public function rules()
    {
        return array(
            'name' => array(
                array('not_empty'),
            ),
            'url' => array(
                array('not_empty'),

            ),
        );
    }
    
}