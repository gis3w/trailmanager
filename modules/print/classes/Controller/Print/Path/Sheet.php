<?php defined('SYSPATH') or die('No direct script access.');

use CpChart\Services\pChartFactory;

class Controller_Print_Path_Sheet extends Controller_Print_Base_Auth_Nostrict
{

    protected $_xmlContentView = 'print/path/sheet';
    public $filename = "Path_";
    public $path;


    public function action_index()
    {
        parent::action_index();
        // get the map extent for path
        $this->path = ORMGIS::factory('Path',$this->request->param('id'));
        View::set_global('sheetTitle',__('Path').' '.$this->path->nome);

        $newExtent = $this->_calculateExtentWithBuffer($this->path,0.1,3857);

        $map = new Mapserver($this->_mapFile,$this->_mapPath,$this->_tmp_dir,$this->_image_base_url,NULL,NULL,$newExtent);
        $this->_setImageMapSize($map);
        $map->makeMap(NULL,Mapserver::EVERY_FEATURE,NULL,$this->_background_layer_id);
        $this->_xmlContentView->mapURL = $map->imageURL;
        $this->_xmlContentView->path = $this->path;

        $images = $this->path->images->find_all();
        if(count($images) > 0)
        {
            $this->_resizeImage($this->path);
            $this->_printImagesSheet($this->path);
        }

        $this->_buildAltitudeGapChart();


        // set filename
        $this->filename .= Inflector::underscore($this->path->nome).'_'.time().'.pdf';
    }
    
    protected function _buildAltitudeGapChart()
    {
        $heights_profile_data = $this->path->heights_profile->find_all();


        if(count($heights_profile_data) == 0)
            return;

        $lhpd = count($heights_profile_data);
        switch($lhpd)
        {
            case $lhpd >= 1 AND $lhpd < 301:
                $limit_cont = 1;
                break;
            case $lhpd >= 300 AND $lhpd < 601:
                $limit_cont = 2;
                break;
            case $lhpd >= 600 AND $lhpd < 901:
                $limit_cont = 3;
                break;
            case $lhpd >= 900 AND $lhpd < 1201:
                $limit_cont = 4;
                break;
            case $lhpd >= 1200 AND $lhpd < 1501:
                $limit_cont = 5;
                break;
            case $lhpd >= 1500 AND $lhpd < 2001:
                $limit_cont = 10;
                break;
            case $lhpd >= 2000 AND $lhpd < 3001:
                $limit_cont = 10;
                break;
            case $lhpd >= 3000:
                $limit_cont = 20;
        }
        $cont = 0;
        $x = $z = [];
        foreach ($heights_profile_data as $height)
        {
            $cont++;
            if($cont == 1)
            {
                $z[] = (float)$height->z;
                $x[] = round((float)$height->cds2d,0);
            }

            if($cont == $limit_cont)
                $cont = 0;
        }

        $factory = new pChartFactory();

        // create and populate the pData class
        $myData = $factory->newData($z, "Heights");
        $myData->setAxisName(0,__('Height').'(m)');
        $serieSettings = array("R"=>221,"G"=>72,"B"=>20,"Alpha"=>90);
        $myData->setPalette("Height",$serieSettings);



        $myData->addPoints($x, "Distance");
        $myData->setSerieDescription("Distance",__("Distance")."(m)");
        $myData->setAbscissa("Distance");
        $myData->setAbscissaName(__("Distance")."(m)");

        // create the image and set the data
        $myPicture = $factory->newImage(700, 300, $myData);
        $myPicture->setGraphArea(60, 60, 640, 240);
        $myPicture->setFontProperties(
            array(
                "FontName" => "verdana.ttf",
                "FontSize" => 9
            )
        );

        // creating a pie chart - notice that you specify the type of chart, not class name.
        // not all charts need to be created through this method (ex. the bar chart),
        // some are created via the pImage class (check the documentation before drawing).
        $pieChart = $factory->newChart("pie", $myPicture, $myData);

        // do the drawing
        $myPicture->drawScale(array("DrawSubTicks"=>FALSE,"DrawSubTicks"=>FALSE,"LabelSkip"=>50));
        $myPicture->drawAreaChart("");
        $imgTmp = APPPATH."../public/imgtmp/".time().".png";
        $myPicture->render($imgTmp);
        #$myPicture->Stroke();
        $this->_xmlContentView->heights_profile_img = $imgTmp;




    }
}