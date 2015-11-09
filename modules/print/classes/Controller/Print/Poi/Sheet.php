<?php defined('SYSPATH') or die('No direct script access.');

class Controller_Print_Poi_Sheet extends Controller_Print_Base_Auth_Nostrict
{

    protected $_xmlContentView = 'print/poi/sheet';
    public $filename = "Poi_";


    public function action_index()
    {
        parent::action_index();
        // get the map extent for path
        $poi = ORMGIS::factory('Poi',$this->request->param('id'));
        View::set_global('sheetTitle',$poi->idwp);
        $this->_xmlContentView->lat = $poi->lat;
        $this->_xmlContentView->lon = $poi->lon;
        $poi->getLonLat(3857);
        $scale = 10000;
        $map = new Mapserver($this->_mapFile,$this->_mapPath,$this->_tmp_dir,$this->_image_base_url,$scale,[$poi->x,$poi->y]);
        $this->_setImageMapSize($map);
        $map->makeMap($poi->id,NULL,NULL);
        $this->_xmlContentView->mapURL = $map->imageURL;
        $this->_xmlContentView->poi = $poi;
        $this->_xmlContentView->typologies = $poi->typologies->find_all();

        $images = $poi->images->find_all();
        if(count($images) > 0)
        {
            $this->_resizeImage($poi);
            $this->_printImagesSheet($poi);
        }

        // set filename
        $this->filename .= Inflector::underscore($poi->idwp).'_'.time().'.pdf';
    }
}