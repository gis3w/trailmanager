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

    public function makeMap()
    {
        $this->_makePoisSymbols();

        $this->addBaseLayer(MS_ON);
        $this->addAreas(MS_ON);
        $this->addPaths(MS_ON);
        $this->addPois(MS_ON);

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
        var_dump($this->_orderLayers);
        $this->_mapObj->setLayersDrawingOrder($this->_orderLayers);
        var_dump($this->_mapObj->getLayersDrawingOrder());
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

    public function addBaseLayer($status)
    {
        $this->_baseLayerObj = new LayerObj($this->_mapObj);
        $this->_baseLayerObj->set('type',MS_LAYER_RASTER);
        $this->_baseLayerObj->set('status',$status);
        $this->_baseLayerObj->set('connection','http://localhost/mapproxy_osm/service?');
        $this->_baseLayerObj->setConnectionType(MS_WMS);
        $this->_baseLayerObj->setProjection("init=epsg:3857");

        $this->_baseLayerObj->setMetaData('DESCRIPTION','OSM layer');
        $this->_baseLayerObj->setMetaData('wms_name','osm_cyclemap');
        $this->_baseLayerObj->setMetaData('wms_server_version','1.1.1');
        $this->_baseLayerObj->setMetaData('wms_format','image/png');

        $this->_mapObj->moveLayerUp($this->_baseLayerObj->index);
        $this->_mapObj->moveLayerUp($this->_baseLayerObj->index);

        $this->_orderLayers[] = $this->_baseLayerObj->index;
    }


    public function addPaths($status)
    {
        $this->_pathsLayerObj = $this->_mapObj->getLayerByName('PATHS');
        $this->_setMapfileConnectionDb($this->_pathsLayerObj);
        $this->_pathsLayerObj->set('status',$status);
        $this->_orderLayers[] = $this->_pathsLayerObj->index;

    }

    public function addPois($status)
    {
        $this->_poisLayerObj = $this->_mapObj->getLayerByName('POIS');
        $this->_setMapfileConnectionDb($this->_poisLayerObj);
        $this->_poisLayerObj->set('status',$status);
        $class = $this->_poisLayerObj->getClass(0);
        $style = $class->getStyle(0);
        $style->set('offsety',-$this->_markerHeight/2);
        $this->_orderLayers[] = $this->_poisLayerObj->index;
    }

    public function addAreas($status)
    {
        $this->_areasLayerObj = $this->_mapObj->getLayerByName('AREAS');
        $this->_setMapfileConnectionDb($this->_areasLayerObj);
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