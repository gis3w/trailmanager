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

        $extBuffer = 0.1;

        $geo = GEO_Postgis::instance();
        $extent = [
            $path->bbox['minx'] - ($path->bbox['maxx']-$path->bbox['minx']) * $extBuffer,
            $path->bbox['miny'] - ($path->bbox['maxy']-$path->bbox['miny']) * $extBuffer,
            $path->bbox['maxx'] + ($path->bbox['maxx']-$path->bbox['minx']) * $extBuffer,
            $path->bbox['maxy'] + ($path->bbox['maxy']-$path->bbox['miny']) * $extBuffer
        ];

        $newExtent = $geo->bboxFromToSRS($extent,$path->epsg_out,3857);
        $size = $this->_pdf_map_size['A4']['L'];

        $map = new Mapserver($this->_mapFile,$this->_mapPath,$this->_tmp_dir,$this->_image_base_url,NULL,NULL,$newExtent);
        $map->size = [$size['width'],$size['height']];
        $map->makeMap(NULL,$path->id,NULL);
        $this->_xmlContentView->mapURL = $map->imageURL;
    }
}