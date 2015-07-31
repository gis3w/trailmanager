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
        $newExtent = $geo->bboxFromToSRS(array_values($path->bbox),$path->epsg_out,$path->epsg_db);

        $map = new Mapserver(NULL,NULL,$newExtent);
        $this->_xmlContentView->mapURL = $map->imageURL;
    }
}