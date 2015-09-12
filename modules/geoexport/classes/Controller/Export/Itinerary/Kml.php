<?php defined('SYSPATH') or die('No direct script access.');

class Controller_Export_itinerary_Kml extends Controller_Export_Base_Kml{

    protected $_itinerary;
    protected $_paths;
    protected $_pois;
    protected $_marker_uri ="download/typologymarker/index/";

    public function action_index()
    {

        #get the sepcific itinerary
        $this->_itinerary = ORM::factory('Itinerary', $this->request->param('id'));

        $this->filename = 'Itinerary_'.Inflector::underscore($this->_itinerary->name).'_'.time().'.kml';
        $this->kml = KMLF::factory();

        $mainDocument = $this->kml->addDocument([
            ['name',$this->_itinerary->name],
            ['description', strip_tags($this->_itinerary->description)]
        ]);

        #get all path from itineraries
        $this->_paths = $this->_itinerary->paths->where('publish','IS',DB::expr('true'))->find_all();

        foreach($this->_paths as $path)
        {

            $style = $this->kml->addStyle($mainDocument, 'pathStyle'.$path->id);
            $this->kml->addLineStyle($style, [
                ['color', KMLF::fromRGB2KMLColor(substr($path->color, 1))],
                ['width', $path->width]
            ]);
        }

        $typologies = ORM::factory('Typology')->find_all();
        foreach($typologies as $typology)
        {
            $style = $this->kml->addStyle($mainDocument, 'markerStyle'.$typology->id);
            $iconStyle = $this->kml->addIconStyle($style, [
                ['scale','1.0']
            ]);
            $this->kml->addIcon($iconStyle,[
                ['href','http://'.$_SERVER['HTTP_HOST'].'/'.$this->_marker_uri.$typology->marker]
            ]);

        }


        foreach($this->_paths as $path)
        {
            $placemark = $this->kml->addPlaceMark($mainDocument,[
                ['name',$path->title],
                ['description', strip_tags($path->description)],
                ['styleUrl','#pathStyle'.$path->id]
            ]);

            $this->kml->addKMLString($placemark,$path->getKML());
        }

        #add dei poi
        $this->_pois = $this->_itinerary->pois->where('publish','IS',DB::expr('true'))->find_all();
        foreach($this->_pois as $poi)
        {

            $placemark = $this->kml->addPlaceMark($mainDocument,[
                ['name',$poi->title],
                ['description', strip_tags($poi->description)],
                ['styleUrl','#markerStyle'.$poi->typology_id]
            ]);
            $this->kml->addKMLString($placemark,$poi->getKML());
        }

    }



}