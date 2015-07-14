<?php defined('SYSPATH') or die('No direct script access.');



class Kohana_SVG2PNGPinmap {
    
    public $highliting_state;
    public $typology;
    
    protected $_svg_file;
    protected $_base_svgfile = 'map_pin.svg';
    
    public static $image;
    public static $dim_pin = array(40, 48);
    public static $dim_icon = array(21,21);
    public static $padding_icon = array(9,9);


    protected function __construct($hstate_id,$typology_id) {
        
        $this->highliting_state = ORM::factory('Highliting_State',$hstate_id);
        $this->typology = ORM::factory('Highliting_Typology',$typology_id);
        $this->_svgfile = APPPATH.$this->_base_svgfile;
        
        $this->_image = new Imagick();
        $this->_set_backgroundcolor();
        $this->_load_svg();
        $this->_set_out_format();
        $this->_resize();
        $this->_add_typology_icon();
        //$this->_create_shadow();
        $this->_write_out_file();
    }


    public static function instance($hstate_id,$typology_id)
    {
        return new self($hstate_id,$typology_id);
    }
    
    protected function _load_svg()
    {
        // we try to change pin color
        $svgXML = new DOMDocument();
        $svgXML->loadXML(file_get_contents($this->_svgfile));
        $paths = $svgXML->getElementsByTagName('path');
        $paths->item(0)->setAttribute('style','fill:'.$this->highliting_state->color);
        // we save it temporary
        $tmpFile = APPPATH.'../upload/'.time().'_'.$this->_base_svgfile;
        $svgXML->save($tmpFile);
        //$this->_image->readimage($this->_svgfile);
        $this->_image->readimage($tmpFile);
        unlink($tmpFile);
    }
    
    protected function _set_backgroundcolor()
    {
        $this->_image->setBackgroundColor(new ImagickPixel("transparent"));
    }


    protected function _set_out_format()
    {
        $this->_image->setImageFormat("png24");
    }


    protected function _resize()
    {
        $this->_image->resizeimage(self::$dim_pin[0],self::$dim_pin[1], imagick::FILTER_LANCZOS, 1);
    }
    
    protected function _add_typology_icon()
    {
       
        if(file_exists(APPPATH.'../upload/highlitingtypologyicon/'.$this->typology->icon))
        {
             //get the image typology path
            $iconTypologyFile = new Imagick(APPPATH.'../upload/highlitingtypologyicon/'.$this->typology->icon);
            $iconTypologyFile->resizeimage(self::$dim_icon[0],self::$dim_icon[1]   , imagick::FILTER_LANCZOS, 1);
            $this->_image->compositeimage($iconTypologyFile, imagick::COMPOSITE_DEFAULT, self::$padding_icon[0],self::$padding_icon[1]);
        }
        
    }

    protected function _write_out_file()
    {
        $namefile = 'hs'.$this->highliting_state->id.'tp'.$this->typology->id.'.png';
        if(!file_exists(APPPATH.'../upload/mappin'))
            mkdir (APPPATH.'../upload/mappin');
        $this->_image->writeimage(APPPATH.'../upload/mappin/'.$namefile);
    }
    
    public static function createShadow()
    {
        //from first image we create shadow image
        $files = scandir(APPPATH.'../upload/mappin');
        $firstfile = $files[2];
        $image = new imagick( APPPATH.'../upload/mappin/'.$firstfile ); 
        $slideValue = 50;
        $shadow = clone $image;
        $shadow->shadowImage( 80, 10, 5, 5 );
//        $points = array( 
////                        0, 0,
////                        $slideValue, 0,
////                        
////                        0, $image->getImageHeight(),
////                        0, $image->getImageHeight());
//                        
//                        $image->getImageWidth(), 0,
//                        $image->getImageWidth(), 0,
//                        
//                        $image->getImageWidth(), $image->getImageHeight(),
//                        $image->getImageWidth()-$slideValue, $image->getImageHeight());
                      $points = array( 
                        1.945622, 0.071451, -12.187838, 0.799032, 
     1.276214, -24.470275, 0.006258, 0.000715);
        //$shadow->setimagebackgroundcolor("transparent");
        //$shadow->setImageVirtualPixelMethod( imagick::VIRTUALPIXELMETHOD_BACKGROUND );
        $shadow->distortImage( Imagick::DISTORTION_PERSPECTIVEPROJECTION, $points, TRUE ); 
        $shadow->writeimage(APPPATH.'../upload/mappin/shadow.png');

    }

}