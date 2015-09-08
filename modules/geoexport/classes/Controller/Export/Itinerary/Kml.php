<?php defined('SYSPATH') or die('No direct script access.');

class Controller_Export_itinerary_Kml extends Controller_Export_Base_Kml{

    protected $_itinerary;
    protected $_paths;
    protected $_pois;

    public function action_index()
    {

        #get the sepcific itinerary
        $this->_itinerary = ORM::factory('Itinerary', $this->request->param('id'));

        $this->filename = 'Itinerary_'.Inflector::underscore($this->_itinerary->name).'_'.time().'.kml';
        $this->kml = KMLF::factory();

        $mainDocument = $this->kml->addDocument([
            ['name',$this->_itinerary->name],
            ['description', $this->_itinerary->description]
        ]);
        #get all path from itineraries
        $this->_paths = $this->_itinerary->paths->where('publish','IS',DB::expr('true'))->find_all();

        foreach($this->_paths as $path)
        {
            $placemark = $this->kml->addPlaceMark($mainDocument,[
                ['name',$path->title],
                ['description', $path->description]
            ]);

            $this->kml->addKMLString($placemark,$path->getKML());
        }

        #add dei poi
        $this->_pois = $this->_itinerary->pois->where('publish','IS',DB::expr('true'))->find_all();
        foreach($this->_pois as $poi)
        {

            $placemark = $this->kml->addPlaceMark($mainDocument,[
                ['name',$poi->title],
                ['description', $poi->description]
            ]);
            $this->kml->addKMLString($placemark,$poi->getKML());
        }

    }



}