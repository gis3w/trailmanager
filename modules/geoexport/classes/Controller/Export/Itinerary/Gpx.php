<?php defined('SYSPATH') or die('No direct script access.');

class Controller_Export_Itinerary_Gpx extends Controller_Export_Base_Gpx{

    protected $_itinerary;
    protected $_paths;
    protected $_pois;

    public function action_index()
    {

        #get the sepcific itinerary
        $this->_itinerary = ORM::factory('Itinerary', $this->request->param('id'));
        #star GPXF Obeject
        $this->filename = 'Itinerary_'.Inflector::underscore($this->_itinerary->name).'_'.time().'.gpx';
        $this->gpx = GPXF::factory(GPXF::MODE_FILE,$this->filename);

        #get all path from itineraries
        $this->_paths = $this->_itinerary->paths->where('publish','IS',DB::expr('true'))->find_all();

        foreach($this->_paths as $path)
        {
            $trk = $this->gpx->addTrk([
                ['name', $path->nome],
                ['desc', $this->_cleanText($path->descriz)],
            ]);

            $trkseg = $this->gpx->addTrkseg($trk);
            $waypoints = $path->getWaypoints();
            foreach ($waypoints as $w) {
                $properties = [
                    ['ele', NULL],
                ];
                $trkpt = $this->gpx->addTrkpt($trkseg, $w['lat'], $w['lon'], $properties);
            }

            $this->gpx->addToTrkFile($trk);
        }

        #add dei poi
        $this->_pois = $this->_itinerary->pois->where('publish','IS',DB::expr('true'))->find_all();
        foreach($this->_pois as $poi)
        {

            $properties = [
                ['name',$poi->idwp],
                ['desc',$this->_cleanText($poi->note)],
            ];
            $poi->getLonLat(4326);
            $wpt = $this->gpx->addWpt($poi->lat,$poi->lon,$properties);
            $this->gpx->addToWptFile($wpt);

            $this->_update_global_bounds($poi->lat,$poi->lon);
        }



        if(!empty($this->global_bounds))
            $this->gpx->addBounds(NULL,$this->global_bounds);
        $this->file = $this->gpx->render();
    }



}