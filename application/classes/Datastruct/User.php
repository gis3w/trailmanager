<?php defined('SYSPATH') or die('No direct script access.');

class Datastruct_User extends Datastruct {

    public $formLyoutType = 'form-vertical';
    
    public $title = array(
        "title_toshow" => "$1 $2 ($3)",
        "title_toshow_params" => array(
            "$1" => "nome",
            "$2" => "cognome",
            "$3" => "username"
        )
    );
    
    protected $_order_to_render = array(
        'id',
        'nome',
        'cognome',
        'data_nascita',
        'luogo_nascita',
        'username',
        'password',
        'email',
        'tel',
        'cell',
        'roles',
    );
    
    public $groups = array(
        array(
            'name' => 'user-data-data',
            'position' => 'left',
            'fields' => array('id','nome','cognome','luogo_nascita','data_nascita' ),
        ),
          array(
            'name' => 'user-foreign-data',
            'position' => 'right',
            'fields' => array('roles'),
        ),
        array(
            'name' => 'user-data-login',
            'position' => 'right',
            'fields' => array('username','password','email' ),
        ),
        
         array(
            'name' => 'user-data-contact',
            'position' => 'left',
            'fields' => array('tel','cell'),
        ),
        
    );
    
    protected function _columns_type() {
        
        return array(
            "username" => array(
                "table_show" => FALSE,
            ),
            "password" => array(
                "form_input_type" => self::PASSWORD,
                "table_show" => FALSE,
                "default_value" => 'ZZZZZZZZZZ',
            ),
            "data_ins" => array(
                "table_show" => FALSE,
            ),
            "data_first_change_password" => array(
                "table_show" => FALSE,
            ),
            "data_mod" => array(
                "table_show" => FALSE,
            ),
            "logins" => array(
                "editable" => FALSE,
                "form_show" => FALSE,
                "table_show" => FALSE,
            ),
            "last_login" => array(
                "editable" => FALSE,
                "form_input_type" => "datebox",
            ),
           
            
        );
    }
    
     
    protected function _extra_columns_type()
    {
        
        $user_data = Datastruct::factory('User_Data')->render();
        $exc =  Arr::to_arrayjres($user_data['fields'], 'get_item', 'user_data');
        
        return $exc;
    }
    
    
     protected function _foreign_column_type() {
      
        $fct = array();
        
        //main_role
        $fcolumn = $this->_columnStruct;
        $fcolumn = array_replace($fcolumn,array(
            'data_type' => 'integer',
            'form_input_type' => self::SELECT,
            'foreign_key' => 'role',
            'foreign_mode' => self::MULTISELECT,
             'foreign_toshow' =>'$1 ($2)',
             'foreign_toshow_params' => array(
                '$1' => 'name',
                '$2' => 'description',
            ),
            'label' =>__('Roles'),
            'description' => __('User\'s roles inside Safe3'),
            "table_show" => TRUE,
            
        ));
        
         $fct['roles'] = $fcolumn;
        
     
       
           
        return $fct;
        
    }
    
    protected function _apply_ACL() {
        
        if(!$this->user->role->allow_capa('user-account'))
        {
            $this->_columns_to_remove[] = 'username';
            $this->_columns_to_remove[] = 'password';
        }
        
        foreach($this->_columns_to_remove as $col)
            unset($this->_columns[$col]);
    }
    
}
