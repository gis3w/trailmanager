<?php defined('SYSPATH') or die('No direct script access.');

class Controller_Export_Path_Gpx extends Controller_Export_Base_Gpx{

    protected $_path;

    public function action_index()
    {

        #get the sepcific path
        $this->_path = ORMGIS::factory('Path', $this->request->param('id'));
        if(!$this->_path->publish)
            throw new HTTP_Exception_500('Sorry Path id not disponibled');
        #star GPXF Obeject
        $this->gpx = GPXF::factory(GPXF::MODE_FILE);

        $waypoints = $this->_path->getWaypoints();

        $trk = $this->gpx->addTrk([
            ['name', $this->_path->title],
            ['desc', $this->_cleanText($this->_path->description)],
        ]);
        $trkseg = $this->gpx->addTrkseg($trk);
        foreach ($waypoints as $w) {
            $properties = [
                ['ele', NULL],
                #array('time',TRK::GMT_gpx_date_format($w['time_dataora']/1000))
            ];
            $trkpt = $this->gpx->addTrkpt($trkseg, $w['lat'], $w['lon'], $properties);
        }

        $this->gpx->addToTrkFile($trk);

        if(!empty($this->global_bounds))
            $this->gpx->addBounds(NULL,$this->global_bounds);
        $this->file = $this->gpx->render();
    }



}