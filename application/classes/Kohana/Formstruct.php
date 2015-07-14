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
    const HTMLTEXT = 'htmltext';
    const C3CHART = 'c3chart';

    const C3CHART_TYPE_LINECHART = 'linechart';
    const C3CHART_TYPE_TIMESERIES = 'timeseries';
    const C3CHART_TYPE_STEPCHART = 'stepchart';
    const C3CHART_AREA_TRUE = TRUE;
    const C3CHART_AREA_FALSE = FALSE;

    
    const GEOTYPE_POLYLINE = 'polyline';
    const GEOTYPE_POLYGON = 'polygon';
    const GEOTYPE_MARKER = 'marker';
    
    const STATE_INSERT = 'insert';
    const STATE_UPDATE = 'update';
    const STATE_DELETE = 'delete';
    const STATE_GET = 'get';
    const STATE_LIST = 'list';

    const SUBFORM = 'subform';
    const SUBTABLE = 'subtable';

    const AJAX_MODE_JSON = 'json';
    const AJAX_MODE_HTML = 'html';
    const AJAX_MODE_SCRIPT = 'script';
    const AJAX_MODE_JSONP = 'jsonp';
    const AJAX_MODE_XML = 'xml';
    const AJAX_MODE_TEXT = 'text';
    
    

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