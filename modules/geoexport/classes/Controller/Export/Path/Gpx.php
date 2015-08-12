<?php defined('SYSPATH') or die('No direct script access.');

class Controller_Export_Path_Gpx extends Controller_Export_Base_Gpx{

    protected $_path;

    public function action_index()
    {

        #get the sepcific path
        $this->_path = ORMGIS::factory('Path', $this->request->param('id'));
        #star GPXF Obeject
        $this->gpx = GPXF::factory(GPXF::MODE_FILE);

        #get vertexs of line
        $dumpPoints = DB::select(
            [DB::expr('ST_DumpPoints(the_geom)'), 'dp']
        )
            ->from('paths')
            ->where('id', '=', $this->_path->id);


        $waypoints = DB::select(
            [DB::expr('(dp).path[1]'), 'edge_id'],
            [DB::expr('ST_X(ST_Transform((dp).geom,' . $this->_path->epsg_out . '))'), 'lon'],
            [DB::expr('ST_Y(ST_Transform((dp).geom,' . $this->_path->epsg_out . '))'), 'lat']
        )
            ->from([$dumpPoints, 'foo'])
            ->execute();

        $trk = $this->gpx->addTrk([
            ['name', $this->_path->title],
            ['desc', $this->_path->description],
        ]);
        $trkseg = $this->gpx->addTrkseg($trk);
        foreach ($waypoints as $w) {
            $properties = [
                ['ele', NULL],
                #array('time',TRK::GMT_gpx_date_format($w['time_dataora']/1000))
            ];
            $trkpt = $this->gpx->addTrkpt($trkseg, $w['lat'], $w['lon'], $properties);
            /**
             * $toExtension = array(
             * array('velocita',$w['velocita']),
             * array('sessione_id',$w['sessione_id']),
             * );
             *
             * $this->gpx->addExtensions($trkpt,$toExtension,$this->gpx->extension_namespace);
             **/
        }

        $this->gpx->addToTrkFile($trk);

        if(!empty($this->global_bounds))
            $this->gpx->addBounds(NULL,$this->global_bounds);
        $this->file = $this->gpx->render();
    }



}