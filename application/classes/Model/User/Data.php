<?php defined('SYSPATH') OR die('No direct access allowed.');
/**
 *
 * @package    TRACKOID/ORM
 * @author     Gis3w Team
 * @copyright  (c) 2012 Gis3w Team
 */
class Model_User_Data extends ORM {

    protected $_belongs_to = array(
        'user' => array(),
    );

     protected $_has_many = array(
        'patenti' => array(
            'model'   => 'Tipologia_Patente',
            'through' => 'tipologia_patente_user_datas',
            'far_key' => 'tipologia_patente_id'
        ),
    );
 
     public function filters()
    {
        return array(
            // Field Filters
            // $field_name => array(mixed $callback[, array $params = array(':value')]),
            'data_nascita' => array(
                // PHP Function Callback, default implicit param of ':value'
                array('SAFE::date2unixts'),
            ),
            'scadenza_patente' => array(
                // PHP Function Callback, default implicit param of ':value'
                array('SAFE::date2unixts'),
            ),
        );
    }
    
     public function labels() {
            return array(
                "nome" => __("Name"),
                "cognome" => __("Surname"),
                "luogo_nascita" => __("Birth place"),
                "data_nascita" => __("Birth date"),
                "titolo_studio_id" => __("Educationl qualification"),
                "scadenza_patente" => __("Expiration drive's licence"),
            );
        }
    
    public function rules()
    {
        return array(
            'nome' => array(
                array('not_empty'),
            ),
            'cognome' => array(
                array('not_empty'),
            ),
//             'luogo_nascita' => array(
//                array('not_empty'),
//            ),
//              'data_nascita' => array(
//                    array('not_empty'),
//            ),
        );
    }
    
     public function extra_rules()
    {
        return array(
             'scadenza_patente' => array(
                    array('regex', array(':value', '/^[0-9]{2}\/[0-9]{2}\/[0-9]{4}$/')),
            ),
              'data_nascita' => array(
                    array('regex', array(':value', '/^[0-9]{2}\/[0-9]{2}\/[0-9]{4}$/')),
            ),
        );
    }
    
    
    

    
    public function get($column) {

        switch($column)
        {
            case "scadenza_patente":
            case "data_nascita":
                $value = parent::get($column);
                if(isset($value) AND $value !== '')
                {
                    $value = date(SAFE::date_mode(),$value);
                }
                else
                {
                    $value = '';
                }
              
            break;
        
            default:
                $value = parent::get($column);
        }
        return $value;
        
    }



}