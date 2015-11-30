<?php defined('SYSPATH') or die('No direct script access.');

class Controller_Export_Path_Shp extends Controller_Export_Main {

    protected $_exts = array('shp','shx','dbf','prj');

    protected $_dir_cache;

    protected $zipFileName;
    protected $_zipDirImageFiles = 'Foto/';

    protected $_images_file_names = [];

    protected $_dbconn;

    protected $_name_zip_file = "Sentiero-";

    protected $_nameOrm = 'Path';
    protected $_name_path_shp_file = 'sentiero_';
    protected $_name_poi_shp_file = 'puntinotevoli_';
    protected $_name_path_segment_shp_file = 'tratte_';


    protected $_tableName;
    protected $_tableNameNoPlural;

    protected $_path;



    public function before() {
        parent::before();

        $this->_path = ORM::factory('Path',$this->request->param('id'));
        if(!$this->_path->pk())
            throw new HTTP_Exception_500('Sorry but path not present into Database!');

        $this->_dir_cache = APPPATH."cache/";
        $this->_dbconn = SAFE::dbconn_params();

        $this->ts = date('Ymd-Hi',time());

        //get table name and no plural table
        $this->_tableNameNoPlural = strtolower($this->_nameOrm);
        $this->_tableName = $this->_tableNameNoPlural.'s';
        $this->_name_zip_file .= $this->_path->nome.'-';
    }

    public function action_index()
    {
        $queryPath = $this->_build_query_path();
        //paths
        $toExe[] = 'ogr2ogr -f "ESRI Shapefile" '.$this->_build_shpPathFileName().' PG:"host='.$this->_dbconn['host'].' user='.$this->_dbconn['username'].' dbname='.$this->_dbconn['dbname'].' password='.$this->_dbconn['password'].'" -sql "'.str_replace('"','',$queryPath).'"';

        $queryPois = $this->_build_query_pois();
        //paths
        $toExe[] = 'ogr2ogr -f "ESRI Shapefile" '.$this->_build_shpPoiFileName().' PG:"host='.$this->_dbconn['host'].' user='.$this->_dbconn['username'].' dbname='.$this->_dbconn['dbname'].' password='.$this->_dbconn['password'].'" -sql "'.str_replace('"','',$queryPois).'"';

        $queryPathSegments = $this->_build_query_path_segments();
        //paths
        $toExe[] = 'ogr2ogr -f "ESRI Shapefile" '.$this->_build_shpPathSegmentFileName().' PG:"host='.$this->_dbconn['host'].' user='.$this->_dbconn['username'].' dbname='.$this->_dbconn['dbname'].' password='.$this->_dbconn['password'].'" -sql "'.str_replace('"','',$queryPathSegments).'"';

        $this->_toexec($toExe);
        $this->_getPoisImageFiles();
        $this->_build_zipfile();
    }

    /**
     * Build the query for ogr2ogr cli command
     * @return string query compiled
     */

    protected function _build_query_path_segments()
    {

        $query = DB::select(
            'path_segments.id',
            'se',
            'bike',
            'cod_f1',
            'cod_f2',
            'data_ril',
            'condmeteo',
            'rilev',
            'qual_ril',
            'class_ril',
            'tp_trat',
            'tp_fondo',
            'diff',
            'percorr',
            'rid_perc',
            'morf',
            'ambiente',
            'cop_tel',
            'utenza',
            'ex_se',
            'id_tratta',
            'the_geom'
        )
            ->from('path_segments')
            ->where('se','=',$this->_path->se);
        return $query;
    }

    protected function _build_query_pois()
    {

        $query = DB::select(
            'pois.id',
            'se',
            'bike',
            'cod_f1',
            'cod_f2',
            'data_ril',
            'condmeteo',
            'rilev',
            'quali_ril',
            'class_ril',
            'pt_inter',
            'strut_ric',
            'aree_attr',
            'insediam',
            'pt_acqua',
            'tipo_segna',
            'stato_segn',
            'fatt_degr',
            'pt_socc',
            'photo',
            'coord_x',
            'coord_y',
            'quota',
            'coin_in_fi',
            'note',
            'nuov_segna',
            'prio_int',
            'note_man',
            'id_palo',
            'the_geom'
        )
            ->from('pois')
            ->where('se','=',$this->_path->se);
        return $query;
    }

    protected function _build_query_path()
    {


        $query = DB::select(
            $this->_tableName.'.id',
            'se',
            'bike',
            'cod_f1',
            'cod_f2',
            'descriz',
            'coordxini',
            'coordyini',
            'coordxen',
            'coordyen',
            'q_init',
            'q_end',
            'percorr',
            'rid_perc',
            'em_natur',
            'em_paes',
            'ev_stcul',
            'op_attr',
            'l',
            'time',
            'rev_time',
            'diff_q',
            'nome',
            'ex_se',
            'the_geom'
        )
            ->from($this->_tableName)
            ->where('se','=',$this->_path->se);
        return $query;
    }

    /**
     * Build the shape file name
     * @return string shape file name
     */
    protected function _build_shpPathFileName()
    {
        return $this->_build_shpFilename('path');
    }

    protected function _build_shpPoiFileName()
    {
        return $this->_build_shpFilename('poi');
    }

    protected function _build_shpPathSegmentFileName()
    {
        return $this->_build_shpFilename('path_segment');
    }

    protected function _build_shpFilename($nameorm)
    {
        $this->_file_names[] = $this->{'_name_'.$nameorm.'_shp_file'}.$this->_path->nome;
        return $this->_dir_cache.end($this->_file_names).".shp";
    }

    protected function _getPoisImageFiles()
    {
        //get all rows
        $rows = ORM::factory('Poi')->where('se','=',$this->_path->se)->find_all();
        foreach ($rows as $row)
        {
            $imageFiles = $row->images->find_all();
            foreach ($imageFiles as $imageFile)
            {
                //TODO: get upload directory by nameORM
                if(file_exists(APPPATH.'../upload/image/'.$imageFile->file))
                    $this->_images_file_names[] = array(APPPATH.'../upload/image/',$imageFile->file);
            }

        }

    }

    public function after()
    {
        $this->response->send_file($this->zipFileName,NULL,array('delete' => TRUE));
    }

    /**
     * Build zip file to export
     */
    protected function _build_zipfile()
    {
        $zip = new ZipArchive();
        $this->zipFileName = $this->_dir_cache.__($this->_name_zip_file).$this->ts.".zip";
        $zip->open($this->zipFileName, ZipArchive::CREATE);

        foreach($this->_file_names as $fn)
        {
            foreach($this->_exts as $ext)
            {
                $path = $this->_dir_cache.$fn.".".$ext;
                if(file_exists($path))
                    $zip->addFile($path, $fn.".".$ext);
            }
        }

        foreach($this->_images_file_names as $fn)
            $zip->addFile($fn[0].$fn[1],$this->_zipDirImageFiles.$fn[1]);



        $zip->close();
        $this->_delete_shp_files();
    }

    /**
     * Execute CLI commands
     * @param $toExe commands to execute
     * @throws HTTP_Exception
     */
    protected function _toexec($toExe)
    {
        foreach($toExe as $te)
        {
            $exe = Exe::factory($te);
            $res = $exe->run();


            if($exe->status === -1 OR $exe->status > 0)
            {
                $this->_delete_shp_files();
                throw HTTP_Exception::factory(500,__('Si sono verificati i seguenti errori:').$exe->error);
            }

        }
    }


    /**
     * Delete shape files and dbf from cache directory
     */
    protected function _delete_shp_files()
    {
        foreach($this->_file_names as $fn)
        {
            foreach($this->_exts as $ext)
            {
                $path = $this->_dir_cache.$fn.".".$ext;
                if(file_exists($path))
                    unlink ($path);
            }

        }
    }
}