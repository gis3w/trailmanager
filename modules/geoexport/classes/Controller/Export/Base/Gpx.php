<?php defined('SYSPATH') or die('No direct script access.');

class Controller_Export_Base_Gpx extends Controller_Export_Main{

    public $file;
    public $gpx;
    public $global_bounds = array();


    public function after()
    {
        if(isset($this->file))
        {
            $this->response->send_file($this->file,NULL,array('delete' => TRUE));
        }
        
        $this->response->headers('Content-Type','text/xml');
//        $this->response->headers('Content-Disposition','attachment;filename="'.$this->filename.'"');
        $this->response->headers('Cache-Control','max-age=0');
        
        $this->response->body($this->gpx->render());
        
    }
    
    /**
     * Metodo per l'aggiornamento della bounds globale
     */
    protected function _update_global_bounds($lat,$lon)
    {
        $this->_update_bounds($this->global_bounds, $lat, $lon);
    }
    
    protected function _update_bounds(&$bounds,$lat,$lon)
    {
            if(!isset($bounds['minx']) OR $lat < $bounds['minlat'])
                 $bounds['minlat'] = (float)$lat;
             if(!isset($bounds['miny']) OR $lon < $bounds['minlon'])
                 $bounds['minlon'] = (float)$lon;
             if(!isset($bounds['maxlat']) OR $lat > $bounds['maxlat'])
                 $bounds['maxlat'] = (float)$lat;
             if(!isset($bounds['maxlon']) OR $lon > $bounds['maxlon'])
                 $bounds['maxlon'] = (float)$lon;
    }
}