<?php defined('SYSPATH') or die('No direct script access.');
/**
 * Controller base per la stampa
 *
 * @package    Gis3W
 * @category   Controller
 * @author     Walter Lorenzetti
 * @copyright  (c) 2011- 2013 Gis3W
 * @license    http://kohanaframework.org/license
 */

abstract class Controller_Print_Base extends Controller_Base_Main {
    
    public $PHPPdf;
    protected $_pdfContent;
    
    protected $_pdfTemplate = "template_base.pdf";
    public $filename = "doc.pdf";

    protected $_xmlContentView;
    protected $_xmlHeader1View = 'print/header/header1';
    protected $_xmlCssView = 'print/stylesheet';
    protected $_imagesSheetView = 'print/modules/images/sheet';
    public $xmlContent;
    public $xmlCss = NULL;

    protected $_background_layer_id = NULL;

    protected $_mapFile;
    protected $_mapPath;
    protected $_tmp_dir;
    protected $_image_base_url;
    protected $_pdf_map_size;
    protected $_pdf_page_size = 'A4';
    protected $_pdf_page_orientation = 'P';

    protected $_img_base_dir = 'upload/image';
    protected $_img_marker_dir = 'upload/typologymarker';
    protected $_img_resized = [];

    public function action_index()
    {
        // set the header
        View::set_global('header1',View::factory($this->_xmlHeader1View)
            ->set('background_color','#dd4814'));

        if(isset($_GET['background_layer_id']))
            $this->_background_layer_id = (int)$_GET['background_layer_id'];


    }
    
    protected function _initialize()
    {
        $this->PHPPdf =PHPPdf\Core\FacadeBuilder::create()
            ->build();

        $this->_pdfTemplate = APPPATH."../public/pdf/".$this->_pdfTemplate;

        $printConfig = Kohana::$config->load('print');
        $this->_mapFile = $printConfig['mapfile'];
        $this->_mapPath = $printConfig['mappath'];
        $this->_tmp_dir = $printConfig['tmp_dir'];
        $this->_pdf_map_size = $printConfig['pdf_map_size'];
        $this->_image_base_url = $printConfig['image_base_url'];

        $this->_xmlContentView = View::factory($this->_xmlContentView);
        $this->_xmlContentView->template = $this->_pdfTemplate;
        if(isset($this->_xmlCssView))
            $this->_xmlCssView = View::factory($this->_xmlCssView);
        // set pdf page properti sieze, orentation ecc..
        $this->_xmlContentView->pdf_page_size = strtolower($this->_pdf_page_size);
        if(isset($this->_pdf_page_orientation) AND $this->_pdf_page_orientation == 'L')
            $this->_xmlContentView->pdf_page_size .= "-landscape";
        $this->_xmlContentView->tmp_dir = APPPATH.'..';

        // for images
        $this->_xmlContentView->img_base_dir = $this->_img_base_dir;
        $this->_xmlContentView->bind('images_resized',$this->_img_resized);

        // for typology icons
        $this->_xmlContentView->img_marker_dir = $this->_img_marker_dir;
    }
    
    public function after() {
        // si recuperano e si rendono i template
        try
        {
            $this->xmlContent = $this->_xmlContentView->render();
            if(Kohana::$environment === Kohana::DEVELOPMENT)
            {
                $_file_dev = APPPATH."cache/print_xml_.".$_SERVER['REMOTE_ADDR'].".html";
                $_file_res = fopen($_file_dev, "wr");
                fwrite ($_file_res, $this->xmlContent);
                fclose ($_file_res);
            }
            if(isset($this->_xmlCssView))
                $this->xmlCss = $this->_xmlCssView->render();

        }
        catch (Exception $e)
        {
            throw $e;
        }
        
        $this->_pdfContent = $this->PHPPdf->render($this->xmlContent, $this->xmlCss);

        // ERASE IMAGE TMP CHART
        if(isset($this->_xmlContentView->heights_profile_img))
            @unlink($this->_xmlContentView->heights_profile_img);
        // ERASE IMAGES SHEET RESIZED
        foreach($this->_img_resized as $file)
            @unlink($file);

        $this->response->headers('Content-Type', 'application/pdf');
        $this->response->headers('Content-Disposition','attachment; filename=\''.$this->filename.'\'');
        echo $this->_pdfContent;
    }

    protected function _setImageMapSize($map)
    {
        $size = $this->_pdf_map_size[$this->_pdf_page_size]['L'];
        $map->size = [$size['width'],$size['height']];
    }

    protected function _calculateExtentWithBuffer(ORMGIS $obj, $percBuffer,$epsgOut)
    {

        $geo = GEO_Postgis::instance();
        $extent = [
            $obj->bbox['minx'] - ($obj->bbox['maxx']-$obj->bbox['minx']) * $percBuffer,
            $obj->bbox['miny'] - ($obj->bbox['maxy']-$obj->bbox['miny']) * $percBuffer,
            $obj->bbox['maxx'] + ($obj->bbox['maxx']-$obj->bbox['minx']) * $percBuffer,
            $obj->bbox['maxy'] + ($obj->bbox['maxy']-$obj->bbox['miny']) * $percBuffer
        ];

        return $geo->bboxFromToSRS($extent,$obj->epsg_out,$epsgOut);
    }

    /**
     * Resize the image for print pdf
     * @param ORM $orm
     */
    protected function _resizeImage(ORM $orm)
    {
        # get all image
        $images = $orm->images->find_all();

        foreach($images as $image)
        {
            $imgObj = Image::factory(APPPATH.'../'.$this->_img_base_dir.'/'.$image->file);
            $imgObj->resize('210',NULL);
            $newImgFileName = APPPATH.'../public/imgtmp/'.$this->filename.$image->file;
            $imgObj->save($newImgFileName);
            $this->_img_resized[$image->file] = $newImgFileName;

        }

    }

    protected function _printImagesSheet($orm)
    {
        $imagesView = View::factory($this->_imagesSheetView);
        $imagesView->images = $orm->images->find_all();
        $imagesView->images_resized = $this->_img_resized;
        $this->_xmlContentView->images_sheet = $imagesView->render();
    }
}
