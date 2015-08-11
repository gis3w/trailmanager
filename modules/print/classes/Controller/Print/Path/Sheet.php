<?php defined('SYSPATH') or die('No direct script access.');

class Controller_Print_Path_Sheet extends Controller_Print_Base_Auth_Nostrict
{

    protected $_xmlContentView = 'print/path/sheet';
    protected $_xmlCssView = 'print/csstest2';
    protected $_pdfPageSize = "a4";


    public function action_index()
    {
        parent::action_index();
        // get the map extent for path
        $path = ORMGIS::factory('Path',$this->request->param('id'));

        $geo = GEO_Postgis::instance();
        $extent = [
            $path->bbox['minx'],
            $path->bbox['miny'],
            $path->bbox['maxx'],
            $path->bbox['maxy']
        ];

        $newExtent = $geo->bboxFromToSRS($extent,$path->epsg_out,3857);

        $map = new Mapserver($this->_mapFile,$this->_mapPath,$this->_tmp_dir,$this->_image_base_url,NULL,NULL,$newExtent);
        $map->makeMap();
        $this->_xmlContentView->mapURL = $map->imageURL;
    }
}