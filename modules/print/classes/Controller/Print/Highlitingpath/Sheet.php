<?php defined('SYSPATH') or die('No direct script access.');

class Controller_Print_Highlitingpath_Sheet extends Controller_Print_Base_Auth_Strict
{

    protected $_xmlContentView = 'print/highlitingpath/sheet';
    public $filename = "Highliting Path";


    public function action_index()
    {
        parent::action_index();
        // get the map extent for path
        $this->path = ORMGIS::factory('Highliting_Path',$this->request->param('id'));
        View::set_global('sheetTitle',__('Path').' '.$this->path->subject);

        $this->filename = __($this->filename)."_";

        $newExtent = $this->_calculateExtentWithBuffer($this->path,0.1,3857);

        $map = new Highliting_Mapserver($this->_mapFile,$this->_mapPath,$this->_tmp_dir,$this->_image_base_url,NULL,NULL,$newExtent);
        $this->_setImageMapSize($map);
        $map->makeMap(NULL,Mapserver::EVERY_FEATURE,NULL,$this->_background_layer_id,NULL,NULL,FALSE);
        $map->makeMapHighliting(NULL,$this->path->id);
        $this->_xmlContentView->mapURL = $map->imageURL;
        $this->_xmlContentView->path = $this->path;

        $images = $this->path->images->find_all();
        if(count($images) > 0)
        {
            $this->_resizeImage($this->path);
            $this->_printImagesSheet($this->path);
        }
        // set filename
        $this->filename .= Inflector::underscore($this->path->subject).'_'.time().'.pdf';
    }
}