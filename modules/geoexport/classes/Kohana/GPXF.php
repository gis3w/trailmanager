<?php defined('SYSPATH') or die('No direct script access.');

/**
 * Classe che costruisce un gpx
 *
 * @package    Gis3W
 * @category   GIS
 * @author     Walter Lorenzetti
 * @copyright  (c) 2013 Gis3W
 */

class Kohana_GPXF
{
    
    const MODE_PHP = 1;
    const MODE_FILE = 2;
    
    public $mode = self::MODE_PHP;
    public $encoding = "UTF-8";
    protected $_gpx;
    
    protected $_file_dev;
    protected $_file_res;
    
    protected $_file_dev_wpt;
    protected $_file_res_wpt;
    protected $_file_dev_rte;
    protected $_file_res_rte;
    protected $_file_dev_trk;
    protected $_file_res_trk;


    public $extension_namespace = 'trackoid';

    public function __construct($mode = self::MODE_PHP) {
        $this->mode = $mode;
        
        if($this->mode === self::MODE_FILE)
        {
            $this->_file_dev = APPPATH."cache/file_dev_gpx_".time().".gpx";
            $this->_file_res = fopen($this->_file_dev, "wr");
            
            $this->_file_dev_wpt = APPPATH."cache/file_dev_wpt_".time().".tmp";
            $this->_file_res_wpt = fopen($this->_file_dev_wpt, "wr");
            
            $this->_file_dev_rte = APPPATH."cache/file_dev_rte_".time().".tmp";
            $this->_file_res_rte = fopen($this->_file_dev_rte, "wr");
            
            $this->_file_dev_trk = APPPATH."cache/file_dev_trk_".time().".tmp";
            $this->_file_res_trk = fopen($this->_file_dev_trk, "wr");
        }
        
        $this->_init();
        
    }


    public static function factory($mode = self::MODE_PHP)
    {
        return new self($mode);
    }
    
    protected function _init()
    {
        $confGeoexport = Kohana::$config->load('geoexport');
       $this->_gpx = '<?xml version="1.0" encoding="'.$this->encoding.'"?>
            <gpx
                    version="1.1"
                    '.$confGeoexport['creator'].'
                    xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"
                    '.$confGeoexport['xmlns'].'
                    xmlns="http://www.topografix.com/GPX/1/1"
                    xsi:schemaLocation="http://www.topografix.com/GPX/1/1 http://www.topografix.com/GPX/1/1/gpx.xsd">';
        
       if($this->mode === self::MODE_FILE)
           fwrite ($this->_file_res, $this->_gpx);
       
       $this->_gpx = $this->_gpx."</gpx>";
       $this->_gpx= simplexml_load_string($this->_gpx);
       
       // si aggiunge il tempo di generazione
       $this->_gpx_metadata = $this->_gpx->addchild('metadata');
       $this->_gpx->metadata->addchild('time',date('Y-m-d\TH:m:s\Z'));
    }
    
    protected function _addSimpleNode($parent,$data,$namespace = NULL)
    {
        $parent->addChild($data[0],$data[1],$namespace);
    }
    
    public function addBounds($node,$bounds)
    {
        if(is_null($node))
            $node = $this->_gpx_metadata;
        
        $b = $node->addChild('bounds');
        foreach($bounds as $c => $v)
            $b->addAttribute($c,$v);

        
    }
    
    public function addToFile($node,$filetype = NULL)
    {
            $fileres = isset($filetype) ? '_file_res_'.$filetype : '_file_res';
            fwrite ($this->$fileres, preg_replace('/^.+\n/', '', $node->asXML()));
    }
    
    
    public function addToWptFile($node)
    {
            $this->addToFile($node,'wpt');
    }
    
    public function addToRteFile($node)
    {
            $this->addToFile($node,'rte');
    }
    
    public function addToTrkFile($node)
    {
            $this->addToFile($node,'trk');
    }
    


    public function addWpt($lat,$lon, $properties = array())
    {
        
        $wpt = $this->mode === self::MODE_FILE ? new SimpleXMLElement('<wpt></wpt>'): $this->_gpx->addChild('wpt');
        $wpt->addAttribute('lat',$lat);
        $wpt->addAttribute('lon',$lon);
        foreach($properties as $property)
            $this->_addSimpleNode($wpt,$property);
        
        return $wpt;
    }
    
    public function addExtensions(SimpleXMLElement $node, array $data, $namespace = NULL)
    {   
        $extensions = $node->addChild('extensions');
        foreach($data as $d)
            $this->_addSimpleNode($extensions,$d,$namespace);
    }
    
    public function addTrk($properties=array())
    {
        $trk = $this->mode === self::MODE_FILE ? new SimpleXMLElement('<trk></trk>'): $this->_gpx->addChild('trk');
        foreach($properties as $property)
            $this->_addSimpleNode($trk,$property);
        
        return $trk;
    }
    
    public function addTrkseg(SimpleXMLElement $trk,$properties=array())
    {
        $trkseg = $trk->addChild('trkseg');
        foreach($properties as $property)
            $this->_addSimpleNode($trkseg,$property);
        
        return $trkseg;

    }
    
    
    public function addTrkpt(SimpleXMLElement $trkseg,$lat,$lon,$properties = array())
    {
        $trkpt = $trkseg->addChild('trkpt');
        $trkpt->addAttribute('lat',$lat);
        $trkpt->addAttribute('lon',$lon);
        foreach($properties as $property)
            $this->_addSimpleNode($trkpt,$property);
        
        return $trkpt;
    }

    

    public function render()
    {
        
        if($this->mode === self::MODE_FILE)
        {

            // si aggiungono le proprieta del file root gpx
           fwrite($this->_file_res, preg_replace('/^.+\n/', '', $this->_gpx->children()->asXML())); 
           
            
            //si aggiungolo le varie componenti
            foreach(array('wpt','rte','trk') as $type)
            {
                $h = '_file_res_'.$type;
                $f = '_file_dev_'.$type;
                // prima si chide
                fclose($this->$h);
                fwrite($this->_file_res, file_get_contents($this->$f));
            }
            
            fwrite ($this->_file_res, "</gpx>");
            fclose ($this->_file_res);
            
            // si eliminano i file temporanei
            foreach(array('wpt','rte','trk') as $type)
            {
                $toDel = '_file_dev_'.$type;
                unlink($this->$toDel);
            }
                    
            return $this->_file_dev;
        }
        else
        {
            return $this->_gpx->asXML();
        }
            
            
    }

    public static function GMT_gpx_date_format($timestamp)
    {
        return gmdate("Y-m-d\TH:i:s\Z",$timestamp);
    }
    
}