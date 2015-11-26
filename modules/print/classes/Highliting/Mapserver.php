<?php defined('SYSPATH') or die('No direct script access.');

class Highliting_Mapserver extends Kohana_Mapserver{

    protected $_highlitingPoisLayerObj;
    protected $_highlitingPathsLayerObj;


    public function makeMapHighliting($poi_id = NULL, $path_id = NULL, $area_id = NULL,$base_layer_id = NULL,$width=NULL, $height=NULL, $generateImage = TRUE)
    {
        $this->_makeHighlitingPoisSymbols();

        $this->_highlitingPathsLayerObj = $this->_mapObj->getLayerByName('HIGHLITING_PATHS');
        $this->_highlitingPoisLayerObj = $this->_mapObj->getLayerByName('HIGHLITING_POIS');


        if(isset($path_id))
        {
            $this->addHighlitingPaths(MS_ON,$path_id);
        }
        else
        {
            $this->_reorderLayers($this->_highlitingPathsLayerObj->index);
        }

        if(isset($poi_id))
        {
            $this->addHighlitingPois(MS_ON,$poi_id);
        }
        else
        {
            $this->_reorderLayers($this->_highlitingPoisLayerObj->index);
        }

        $this->_setOrderLayers();
        if($generateImage)
        {
            $this->_calculateExtent();
            $this->_setScalebar();
            $this->generateImg();
        }

    }

    public function addHighlitingPaths($status,$path_id = NULL)
    {
        if(!isset($this->_highlitingPathsLayerObj))
            $this->_highlitingPathsLayerObj = $this->_mapObj->getLayerByName('HIGHLITING_PATHS');
        $this->_setMapfileConnectionDb($this->_highlitingPathsLayerObj);
        if(isset($path_id))
        {
            if(is_array($path_id))
            {
                $this->_highlitingPathsLayerObj->set("data", "the_geom from (select * from highliting_paths id IN (".implode(',',$path_id).")) as p using unique id");
            }
            else
            {
                if($path_id == self::EVERY_FEATURE)
                {
                    $this->_highlitingPathsLayerObj->set("data","the_geom from (select * from highliting_paths) as p using unique id");
                }
                else
                {
                    $this->_highlitingPathsLayerObj->set("data","the_geom from (select * from highliting_paths where id = ".$path_id.") as p using unique id");
                }

            }

        }

        #add label


        $this->_highlitingPathsLayerObj->set('status',$status);
        $this->_reorderLayers($this->_highlitingPathsLayerObj->index);

    }

    public function addHighlitingPois($status,$poi_id = NULL)
    {
        if(!isset($this->_highlitingPoisLayerObj))
            $this->_highlitingPoisLayerObj = $this->_mapObj->getLayerByName('HIGHLITING_POIS');
        $this->_setMapfileConnectionDb($this->_highlitingPoisLayerObj);
        if(isset($poi_id))
        {
            if(is_array($poi_id))
            {
                $this->_highlitingPoisLayerObj->set("data", "the_geom from (select * from highliting_pois where id IN (".implode(',',$poi_id).")) as p using unique id");
            }
            else
            {
                $this->_highlitingPoisLayerObj->set("data","the_geom from (select * from highliting_pois where id = ".$poi_id.") as p using unique id");
            }

        }
        $this->_highlitingPoisLayerObj->set('status',$status);
        $class = $this->_highlitingPoisLayerObj->getClass(0);
        $style = $class->getStyle(0);
        $style->set('offsety',-$this->_markerHeight/2);
        $this->_reorderLayers($this->_highlitingPoisLayerObj->index);
    }

    protected function _makeHighlitingPoisSymbols()
    {
        // we get typology symbols
        $typologies = ORM::factory('Highliting_Typology')->find_all();
        foreach ($typologies as $typology)
        {
            $symbol = new symbolObj($this->_mapObj,"highliting_".(string)$typology->id);
            $symbol->set('type',MS_SYMBOL_PIXMAP);
            $markerPath = APPPATH."../".Controller_Download_Base::UPLOADPATH."/".Controller_Admin_Download_Highlitingtypologyicon::$subpathUpload;
            #get the height and width for corrct positining
            if(!isset($this->_markerWidth))
            {
                $markerImage = Image::factory($markerPath."/".$typology->icon);
                $this->_markerWidth = $markerImage->width;
                $this->_markerHeight = $markerImage->height;
            }
            $symbol->setImagePath($markerPath."/".$typology->icon);
            $symbol->set('inmapfile',MS_TRUE);
        }
    }

}