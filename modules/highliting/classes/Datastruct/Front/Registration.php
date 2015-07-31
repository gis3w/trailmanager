<?php defined('SYSPATH') or die('No direct script access.');

class Datastruct_Front_Registration extends Datastruct_User {
    
    protected $_nameORM = "User";
    
    public $formLyoutType = 'form-vertical';
    
      protected $_order_to_render = array(
        'id',
        'nome',
        'cognome',
        'data_nascita',
        'luogo_nascita',
        'via',
        'citta',
        'cap',
        'n_civ',
        'username',
        'password',
        'confirm_password',
        'email',
        'tel',
        'cell',
    );
    
    public $groups = array(
        array(
            'name' => 'registration-data-data',
            'position' => 'left',
            'fields' => array('id','nome','cognome','luogo_nascita','data_nascita','via','citta','cap','n_civ'),
        ),
        array(
            'name' => 'registration-data-login',
            'position' => 'right',
            'fields' => array('username','password','confirm_password','email' ),
        ),
         array(
            'name' => 'registration-data-contact',
            'position' => 'right',
            'fields' => array('tel','cell'),
        ),
    );
    
    protected function _columns_type() {
        $cls = parent::_columns_type();
        
        $cls['username']['required'] = TRUE;
        $cls['password']['required'] = TRUE;
        $cls['email']['required'] = TRUE;
        unset($cls['password']['default_value']);
        
        $cls['id']['form_input_type'] = self::HIDDEN;
        
        return $cls;
    }


    protected function _extra_columns_type() {
        $cls = parent::_extra_columns_type();
            
            $cls['confirm_password'] = array_replace($this->_columnStruct,array(
                'form_input_type' =>self::PASSWORD,
                'label' =>__('Confirm password'),
                "table_show" => FALSE,
                "required" => TRUE,
            ));
            
        return $cls;
    }
    
    protected function _apply_ACL() {
        
    }
    

    
}