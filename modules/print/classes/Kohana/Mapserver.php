<?php defined('SYSPATH') or die('No direct script access.');

/**
 * Classe per la genesi di immagini con mapserver
 *
 * @package    Gis3W
 * @category   Mpaserver
 * @author     Walter Lorenzetti
 * @copyright  (c) 2011- 2015 Gis3W
 */

class Kohana_Mapserver {

    const MAPDIR = "mapserver";
    protected $_mapPath;
    protected $_mapFile = 'print.map';
    protected $_mapObj;


    protected $_markerWidth;
    protected $_markerHeight;

    protected $_baseLayerObj;
    protected $_poisLayerObj;
    protected $_pathsLayerObj;
    protected $_areasLayerObj;
    protected $_orderLayers;

    protected $_generatedImg;
    protected $_generatedImgUrl;

    protected $_scale;
    protected $_extent;
    protected $_center;

    private $_dbConnection;

    public function __construct($mapfile = NULL,$mappath = NULL,$tmp_dir = NULL,$image_base_url = NULL,$scale = NULL, $center = NULL, $extent = NULL, $baseLayerId = NULL )
    {
        if(isset($mapfile))
            $this->_mapFile = $mapfile;
        if(isset($mappath))
        {
            $this->_mapPath = $mappath;
        }
        else
        {
            $this->_mapPath = APPPATH.'../'.self::MAPDIR.'/';
        }

        $this->_mapObj = new mapObj($this->_mapPath.$this->_mapFile);

        if(isset($tmp_dir))
            $this->setTmpDir($tmp_dir);

        if(isset($image_base_url))
            $this->setImageBaseUrl($image_base_url);

        $this->_scale = $scale;
        $this->_extent = $extent;
        $this->_center = $center;


        $this->_dbConnection = SAFE::dbconn_params();


    }

    public function makeMap($poi_id = NULL, $path_id = NULL, $area_id = NULL,$base_layer_id = NULL)
    {
        $this->_makePoisSymbols();

        $this->_areasLayerObj = $this->_mapObj->getLayerByName('AREAS');
        $this->_pathsLayerObj = $this->_mapObj->getLayerByName('PATHS');
        $this->_poisLayerObj = $this->_mapObj->getLayerByName('POIS');

        $this->addBaseLayer(MS_ON,$base_layer_id);
        if(isset($area_id))
        {
            $this->addAreas(MS_ON,$area_id);
        }
        else
        {
            $this->_orderLayers[] = $this->_areasLayerObj->index;
        }

        if(isset($path_id))
        {
            $this->addPaths(MS_ON,$path_id);
        }
        else
        {
            $this->_orderLayers[] = $this->_pathsLayerObj->index;
        }

        if(isset($poi_id))
        {
            $this->addPois(MS_ON,$poi_id);
        }
        else
        {
            $this->_orderLayers[] = $this->_poisLayerObj->index;
        }


        $this->_calculateExtent();


        $this->_setOrderLayers();
        $this->_setScalebar();
        $this->generateImg();
    }

    public function __set($name,$value)
    {
        switch($name)
        {
            case "tmp_dir":
                $this->setTmpDir($value);
            break;
            case "image_base_url":
                $this->setImageBaseUrl($value);
            break;
            case "extent":
                $this->_extent = $value;
            break;
            case "center":
                $this->_center = $value;
            break;
            case "size":
                $this->_mapObj->setSize($value[0],$value[1]);
            break;
            default:
                $this->{$name} = $value;
        }
    }

    public function __get($param)
    {
        switch($param)
        {
            case 'imageURL':
                return $this->_generatedImgUrl;
            default:
                return parent::__get($param);
        }
    }


    public function setTmpDir($tmp_dir)
    {
        $this->_mapObj->web->set('imagepath',$tmp_dir);
    }

    public function setImageBaseUrl($image_base_url)
    {
        $this->_mapObj->web->set('imageurl',$image_base_url);
    }

    /**
     * Check parameter for scale center and extent ad flow to correct parameter
     * @throws Mapserver_Exception
     */
    protected function _calculateExtent()
    {
        if(isset($this->_extent))
        {
            $this->setExtent($this->_extent);
        }
        elseif (isset($this->_scale))
        {
            if(!isset($this->_center))
                throw new Mapserver_Exception("If you set \$scale, you have to set \$center",NULL,E_USER_ERROR);
            $this->setScaleCenter($this->_scale,$this->_center);
        }
        elseif (isset($this->_center))
        {
            if(!isset($this->_scale))
                throw new Mapserver_Exception("If you set \$center, you have to set \$scale",NULL,E_USER_ERROR);
            $this->setScaleCenter($this->_scale,$this->_center);
        }
        else
        {
            $this->setExtent();

        }

    }


    protected function _makePoisSymbols()
    {
        // we get typology symbols
        $typologies = ORM::factory('Typology')->find_all();
        foreach ($typologies as $typology)
        {
            $symbol = new symbolObj($this->_mapObj,(string)$typology->id);
            $symbol->set('type',MS_SYMBOL_PIXMAP);
            $markerPath = APPPATH."../".Controller_Download_Base::UPLOADPATH."/".Controller_Admin_Download_Typologymarker::$subpathUpload;
            #get the height and width for corrct positining
            if(!isset($this->_markerWidth))
            {
                $markerImage = Image::factory($markerPath."/".$typology->marker);
                $this->_markerWidth = $markerImage->width;
                $this->_markerHeight = $markerImage->height;
            }
            $symbol->setImagePath($markerPath."/".$typology->marker);
            $symbol->set('inmapfile',MS_TRUE);
        }
    }

    protected function _setOrderLayers()
    {

        //$this->_orderLayers = array_reverse($this->_orderLayers);
        $this->_mapObj->setLayersDrawingOrder($this->_orderLayers);

    }

    public function generateImg()
    {
        //echo $this->_mapObj->convertToString();
        //exit;
        $this->_generatedImage=$this->_mapObj->draw();
        $this->_generatedImgUrl=$this->_generatedImage->saveWebImage();
    }

    protected function _setMapFileConnectionDb($layerObj)
    {
        $connection = 'host='.$this->_dbConnection['host'].' port='.$this->_dbConnection['port'].' dbname='.$this->_dbConnection['dbname'].' user='.$this->_dbConnection['username'].' password='.$this->_dbConnection['password'];
        $layerObj->set('connection',$connection);
    }

    public function addBaseLayer($status,$base_layer_id = NULL)
    {
        if(isset($base_layer_id))
            $bl = ORM::factory('Background_Layer',$base_layer_id);
        $this->_baseLayerObj = new LayerObj($this->_mapObj);
        $this->_baseLayerObj->set('type',MS_LAYER_RASTER);
        $this->_baseLayerObj->set('status',$status);
        if(Kohana::$config->load('print')['mapproxy_url'] != '')
        {
            $mapproxy_url = Kohana::$config->load('print')['mapproxy_url'];
        }
        else
        {
            $mapproxy_url = 'http://'.$_SERVER['HTTP_HOST'].'/mapproxy_osm/service?';
        }
        $this->_baseLayerObj->set('connection',$mapproxy_url);
        $this->_baseLayerObj->setConnectionType(MS_WMS);
        $this->_baseLayerObj->setProjection("init=epsg:3857");

        $this->_baseLayerObj->setMetaData('DESCRIPTION','OSM layer');
        if(isset($bl) AND isset($bl->id) AND isset($bl->mapproxy_layer) AND $bl->mapproxy_layer != '')
        {
            $wms_name = $bl->mapproxy_layer;
        }
        else
        {
             $wms_name = 'osm_cyclemap';
        }
        $this->_baseLayerObj->setMetaData('wms_name',$wms_name);
        $this->_baseLayerObj->setMetaData('wms_server_version','1.1.1');
        $this->_baseLayerObj->setMetaData('wms_format','image/png');

        $this->_mapObj->moveLayerUp($this->_baseLayerObj->index);
        $this->_mapObj->moveLayerUp($this->_baseLayerObj->index);

        $this->_orderLayers[] = $this->_baseLayerObj->index;
    }


    public function addPaths($status,$path_id = NULL)
    {
        if(!isset($this->_pathsLayerObj))
            $this->_pathsLayerObj = $this->_mapObj->getLayerByName('PATHS');
        $this->_setMapfileConnectionDb($this->_pathsLayerObj);
        if(isset($path_id))
        {
            if(is_array($path_id))
            {
                $this->_pathsLayerObj->set("data", "the_geom from (select * from paths where publish is TRUE and id IN (".implode(',',$path_id).")) as p using unique id");
            }
            else
            {
                $this->_pathsLayerObj->set("data","the_geom from (select * from paths where publish is TRUE and id = ".$path_id.") as p using unique id");
            }

        }


        $this->_pathsLayerObj->set('status',$status);
        $this->_orderLayers[] = $this->_pathsLayerObj->index;

    }

    public function addPois($status,$poi_id = NULL)
    {
        if(!isset($this->_poisLayerObj))
            $this->_poisLayerObj = $this->_mapObj->getLayerByName('POIS');
        $this->_setMapfileConnectionDb($this->_poisLayerObj);
        if(isset($poi_id))
        {
            if(is_array($poi_id))
            {
                $this->_poisLayerObj->set("data", "the_geom from (select * from pois where publish is TRUE and id IN (".implode(',',$poi_id).")) as p using unique id");
            }
            else
            {
                $this->_poisLayerObj->set("data","the_geom from (select * from pois where publish is TRUE and id = ".$poi_id.") as p using unique id");
            }

        }
        $this->_poisLayerObj->set('status',$status);
        $class = $this->_poisLayerObj->getClass(0);
        $style = $class->getStyle(0);
        $style->set('offsety',-$this->_markerHeight/2);
        $this->_orderLayers[] = $this->_poisLayerObj->index;
    }

    public function addAreas($status, $area_id = NULL)
    {
        if(!isset($this->_areasLayerObj))
            $this->_areasLayerObj = $this->_mapObj->getLayerByName('AREAS');
        $this->_setMapfileConnectionDb($this->_areasLayerObj);
        if(isset($area_id))
        {
            if(is_array($area_id))
            {
                $this->_areasLayerObj->set("data", "the_geom from (select * from areas where publish is TRUE and id IN (".implode(',',$area_id).")) as p using unique id");
            }
            else
            {
                $this->_areasLayerObj->set("data","the_geom from (select * from areas where publish is TRUE and id = ".$area_id.") as p using unique id");
            }

        }
        $this->_areasLayerObj->set('status',$status);
        $this->_orderLayers[] = $this->_areasLayerObj->index;
    }

    public function setScaleCenter($scale,array $center)
    {
        $centerObj = new pointObj();
        $centerObj->setXY($center[0],$center[1]);
        $this->_mapObj->setCenter($centerObj);
        $this->_mapObj->ScaleExtent(0,$scale,0);
    }

    public function setExtent(array $exent = NULL)
    {
        if(!isset($exent))
        {
            // get extent from db
            if (defined('DEFAULT_EXTENT'))
            {
                $ext = preg_split('/,/',DEFAULT_EXTENT);
                $exent = array($ext[0],$ext[2],$ext[1],$ext[3]);
                $geo = GEO_Postgis::instance();
                $newExtent = $geo->bboxFromToSRS($exent,4326,3857);
            }

        }
        else
        {
            $newExtent = $exent;
        }
            $this->_mapObj->setExtent($newExtent['minx'],$newExtent['miny'],$newExtent['maxx'],$newExtent['maxy']);
    }

    protected function _setScalebar()
    {
        $scalebar = $this->_mapObj->scalebar;
        $scalebar->set('status',MS_EMBED);
        $scalebar->set('units',MS_METERS);
    }


}