<?php defined('SYSPATH') or die('No direct script access.');

class Kohana_Formstruct{
    
    const ECNTYPE_DEFAULT = "application/x-www-form-urlencoded";
    const ECNTYPE_MULTIPART = "multipart/form-data";
    
    const INPUT = 'input';
    const PASSWORD = 'password';
    const SELECT = 'combobox';
    const MULTISELECT = 'multiselect';
    const SINGLESELECT = 'singleselect';
    const CHECK = 'check';
    const RADIO = 'radio';
    const DATE = 'datebox';
    const TIME = 'timebox';
    const TEXTAREA = 'textarea';
    const HIDDEN = 'hidden';
    const FILE = 'file';
    const BUTTON = 'button';
    
    

    /**
     * Metodo per il filtraggio dei campi a seconda delle Capabilities
     */
    protected function _apply_ACL()
    {
        
    }
    
    public function render()
    {
        $this->_apply_ACL();
    }
}