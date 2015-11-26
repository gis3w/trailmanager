<?php defined('SYSPATH') or die('No direct script access.');

class Controller_Print_Highlitingpoi_Sheet extends Controller_Print_Base_Auth_Strict
{

    protected $_xmlContentView = 'print/highlitingpoi/sheet';
    public $filename = "Highliting Poi";
    protected $_img_marker_dir = 'upload/highlitingtypologyicon';

    public function action_index()
    {
        parent::action_index();
        // get the map extent for path
        $poi = ORMGIS::factory('Highliting_Poi',$this->request->param('id'));
        View::set_global('sheetTitle',$poi->subject);
        $this->filename = Inflector::underscore(__($this->filename)).'_';
        $this->_xmlContentView->lat = $poi->lat;
        $this->_xmlContentView->lon = $poi->lon;
        $poi->getLonLat(3857);
        $scale = 10000;
        $map = new Highliting_Mapserver($this->_mapFile,$this->_mapPath,$this->_tmp_dir,$this->_image_base_url,$scale,[$poi->x,$poi->y]);
        $this->_setImageMapSize($map);

        #try to add every path
        $paths = ORM::factory('Path')->find_all()->as_array('id');

        $map->makeMap(NULL,array_keys($paths),NULL,NULL,NULL,NULL,FALSE);
        $map->makeMapHighliting($poi->id);
        $this->_xmlContentView->mapURL = $map->imageURL;
        $this->_xmlContentView->poi = $poi;

        $images = $poi->images->find_all();
        if(count($images) > 0)
        {
            $this->_resizeImage($poi);
            $this->_printImagesSheet($poi);
        }

        // set filename
        $this->filename .= Inflector::underscore($poi->subject).'_'.time().'.pdf';
    }
}