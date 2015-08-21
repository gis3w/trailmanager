<?php defined('SYSPATH') or die('No direct script access.');

class Controller_Print_Area_Sheet extends Controller_Print_Base_Auth_Nostrict
{

    protected $_xmlContentView = 'print/area/sheet';
    public $filename = "Area_";


    public function action_index()
    {
        parent::action_index();
        // get the map extent for path
        $area = ORMGIS::factory('Area',$this->request->param('id'));

        $newExtent = $this->_calculateExtentWithBuffer($area,0.1,3857);

        $map = new Mapserver($this->_mapFile,$this->_mapPath,$this->_tmp_dir,$this->_image_base_url,NULL,NULL,$newExtent);
        $this->_setImageMapSize($map);
        $map->makeMap(NULL,NULL,$area->id);
        $this->_xmlContentView->mapURL = $map->imageURL;
        $this->_xmlContentView->area = $area;


        // set filename
        $this->filename .= Inflector::underscore($area->title).'_'.time().'.pdf';
    }
}