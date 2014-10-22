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
    const MAPBOX = 'mapbox';
    const MAPBOX_COLOR = 'mapbox_color';
    const COLORPICKER = 'colorpicker';
    
    const GEOTYPE_POLYLINE = 'polyline';
    const GEOTYPE_POLYGON = 'polygon';
    const GEOTYPE_MARKER = 'marker';
    
    const STATE_INSERT = 'insert';
    const STATE_UPDATE = 'update';
    const STATE_DELETE = 'delete';
    const STATE_GET = 'get';
    const STATE_LIST = 'list';
    
    

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