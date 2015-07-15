<?php defined('SYSPATH') or die('No direct script access.');


class   Controller_Ajax_Admin_Highliting_Sheet_Base extends Controller_Ajax_Base_Crud{

    protected $_typologies = array();

    protected $_upload_path = array(
        'image_highliting_poi' => 'image',
        'image_highliting_path' => 'image',
        'image_highliting_area' => 'image',
    );

    protected  $_subformToSave = array(
        'image_highliting_poi' => 'Image_Highliting_Poi',
        'image_highliting_path' => 'Image_Highliting_Path',
        'image_highliting_area' => 'Image_Highliting_Area'
    );

    protected $_url_multifield_postname;
    protected $_url_multifiled_value;
    protected $_url_multifield_nameORM;
    protected $_url_multifield_foreignkey;


    protected function _role_SUPERVISOR_filters($orm)
    {
        $orm->where('supervisor_user_id','=',$this->user->id);
    }

    protected function _role_EXECUTOR_filters($orm)
    {
        $orm->where('executor_user_id','=',$this->user->id);
    }

    protected function _set_the_geom_edit()
    {

        $geodata  = json_decode($_POST['the_geom']);


        if(!empty($geodata->features))
        {

            $fs = GEO::featurecollection2wkt($geodata);

            foreach($fs as $k => $f)
            {
                $reg = "(";
                $fs[$k] = strstr($f,$reg);
            }


            $parFrom = $parTo  = "";
            switch($this->_orm->geotype)
            {
                case "MULTILINESTRING":
                case "MULTIPOLYGON":
                    $parFrom = "(";
                    $parTo = ")";
                    break;
            }


            $_POST['the_geom'] = $this->_orm->geotype.$parFrom.  implode(",", $fs).$parTo;
        }
        else
        {
            //unset($_POST['the_geom']);
            $_POST['the_geom'] = NULL;
        }

        //print_r($_POST['the_geom']);

    }


    protected function _data_edit()
    {
        Filter::emptyPostDataToNULL();

        $this->from_state = $this->_orm->highliting_state_id;

        $this->_insert_reporter();

        $this->_set_the_geom_edit();

        $this->_orm->values($_POST);
        if(!isset($this->_orm->id))
        {
            $this->_orm->data_ins = $this->_orm->data_mod = time();
        }
        else
        {
            $this->_orm->data_mod = time();
        }
        $this->_orm->data_mod = time();
        $this->_orm->save();

        $this->_save_subforms_1XN();

        $this->_save_state_passage();

        $this->_send_email();


    }

    protected function _insert_reporter()
    {
        $capability = 'admin-'.strtolower($this->_datastructName).'-insert';
        if(isset($this->user)
            AND $this->user->main_role_id != ROLE_REPORTER
            AND $this->user->allow_capa($capability)
            AND !isset($this->_orm->highliting_user_id)
            AND !isset($this->_orm->anonimous_data)
        )
            $this->_orm->highliting_user_id = $this->user->id;

    }


    protected function _send_email()
    {
        // build calssname
        $from_state = ORM::factory('highliting_state',$this->from_state);
        $to_state = $this->_orm->state;

        $from_state_name = preg_replace('/\s+/', '', trim($from_state->name));
        $to_state_name = preg_replace('/\s+/', '', trim($to_state->name));

        $class_name = 'Email_Passagestate_'.strtoupper($from_state_name.$to_state_name);

        if(class_exists($class_name))
        {
            $email = new $class_name($this->_orm);
            $email->send();
        }

    }


    /**
     * To save passages state and note if present
     */
    protected function _save_state_passage()
    {
        $data = array(
            $this->_url_multifield_foreignkey => $this->_orm->id,
            'user_id' => $this->user->id,
            'from_state_id' => $this->from_state,
            'to_state_id' => $_POST['highliting_state_id'],
            'date' =>time(),
        );

        if(isset($_POST['note']) AND $_POST['note'] != '')
            $data['note'] = $_POST['note'];

        $this->_orm->states->values($data)->save();
    }


    /**
     * Save th eurl motifield values
     */
    protected function _save_url_multifiled()
    {
        // si salvano gli elementi del sub form
        if(isset($_POST[$this->_url_multifield_postname]))
            if(!isset($this->_url_multifiled_value))
                $this->_url_multifiled_value = $this->_get_subform_multifield_data($this->_url_multifield_postname);

        if(!empty($this->_url_multifiled_value))
        {
            foreach($this->_url_multifiled_value as $url)
            {
                if(!isset($url['stato']))
                    continue;

                // si forza unita_produttiva_id anche per lìinserimento
                $url[$this->_url_multifield_foreignkey] = $this->_orm->id;
                $id = (isset($url['id']) AND $url['id'] != '') ? $url['id'] : NULL;
                $oUrl = ORM::factory($this->_url_multifield_nameORM,$id);
                if($url['stato'] == 'I' OR $url['stato'] == 'U')
                    $oUrl->values($url)->save();
                if($url['stato'] == 'D')
                    $oUrl->delete();
            }
        }


    }

    protected function _validation()
    {
        // oltre alla non empty di dpi e mansioni è necessario
        // validare gli indroci per unità produttiva che non si devono sovrapporre ??? chiedere

        $this->_vorm = Validation::factory($_POST);

        // si aggiungono le validazioni dell'orm
        foreach ($this->_orm->rules() as $col => $rule)
            $this->_vorm->rules($col, $rule);

        // if state if ASSEGNATA SUPERVISOR / PROGRAMMATA
        if(isset($_POST['highliting_state_id']) AND in_array($_POST['highliting_state_id'],array(HSTATE_ASSEGNATA_SUPERVISOR,HSTATE_PROGRAMMATA)))
            $this->_vorm->rule('supervisor_user_id','not_empty');

        // if state if IN ESECUZIONE
        if(isset($_POST['highliting_state_id']) AND in_array($_POST['highliting_state_id'],array(HSTATE_IN_ESECUZIONE)))
            $this->_vorm->rule('executor_user_id','not_empty');



        // si aggiungono anche le labels
        $this->_vorm->labels($this->_orm->labels());


        if(!$this->_vorm->check())
            $this->vErrors = Arr::push ($this->vErrors,$this->_vorm->errors('validation'));

        if(!empty($this->vErrors))
            throw new Validation_Exception($this->_vorm);

    }



    protected function _edit()
    {
        try
        {

            //test per geo
            Database::instance()->begin();


            $this->_validation();

            $this->_data_edit();

            Database::instance()->commit();
        }
        catch (Database_Exception $e)
        {
            Database::instance()->rollback();
            throw $e;
        }
        catch (ORM_Validation_Exception $e)
        {
            Database::instance()->rollback();

            $this->_validation_error($e);
        }
        catch (Validation_Exception $e)
        {
            Database::instance()->rollback();

            $this->_validation_error($this->vErrors);

        }
    }

    protected function _single_request_row($orm) {
        $toRes = parent::_single_request_row($orm);

        $this->_unset_ORMGIS_geofield($toRes);
        $this->_set_the_geom($toRes, $orm);
        $this->_set_reporter($toRes,$orm);
        $this->_set_supervisor($toRes,$orm);
        $this->_set_executor($toRes,$orm);
        $this->_set_current_state($toRes,$orm);
        $this->_set_oldnotes($toRes,$orm);

        return $toRes;
    }

    /**
     * Set the old notes to show inside sheet
     * @param type $tores
     * @param type $orm
     */
    protected function _set_oldnotes(&$toRes, $orm)
    {
        $view = View::factory('data/oldnotes');
        $view->states = $orm->states
            ->order_by('date','DESC')
            ->find_all();
        $toRes['oldnotes'] = $view->render();
    }


    /**
     * Set data from current reporter if anonimous or registerd
     * @param type $toRes
     * @param type $orm
     */
    protected function _set_reporter(&$toRes,$orm)
    {
        if(isset($orm->id))
        {
            if(isset($orm->highliting_user_id))
            {
                $viewReporter = View::factory('data/reporter');
                $viewReporter->user = $orm->highliting_user;
            }
            else
            {
                $viewReporter = View::factory('data/anonimous');
                $viewReporter->anonimous = $orm->anonimous_data;
            }
            $toRes['reporter'] =  $viewReporter->render();

        }
        else
        {
            $toRes['reporter'] = '';
        }
    }

    /**
     *  Set data from current supervisor
     * @param type $toRes
     * @param type $orm
     */
    protected function _set_supervisor(&$toRes,$orm)
    {
        $viewSupervisor = View::factory('data/supervisor');
        $viewSupervisor->user = $orm->supervisor_user;
        $toRes['supervisor'] =  $viewSupervisor->render();
    }

    /**
     *  Set data from current executor
     * @param type $toRes
     * @param type $orm
     */
    protected function _set_executor(&$toRes,$orm)
    {
        $viewExectuor = View::factory('data/executor');
        $viewExectuor->user = $orm->executor_user;
        $toRes['executor'] =  $viewExectuor->render();
    }

    /**
     * Set the current highliting state ìfor human reading
     * @param type $toRes
     * @param type $orm
     */
    protected function _set_current_state(&$toRes,$orm)
    {
        if(isset($orm->state->name))
        {
            $view = View::factory('data/currentstate');
            $view->state = $orm->state;
            $toRes['current_highliting_sate'] = $view->render();
        }

    }


    protected function _unset_ORMGIS_geofield(&$toRes)
    {
        unset(
            $toRes['the_geom'],
            $toRes['astext'],
            $toRes['asgeojson'],
            $toRes['box2d'],
            $toRes['centroid'],
            $toRes['asbinary'],
            $toRes['x'],
            $toRes['y']
        );

    }

    protected function _set_typologies(&$toRes ,$orm)
    {
        foreach(array('typologies') as $alias)
        {
            if(!isset($orm->$alias))
                continue;
            $datas = $orm->$alias->find_all();
            foreach($datas as $data)
            {
                $toRes[$alias][] = array(
                    "id" => $data->id,
                );
            }
        }
    }

    protected function _set_the_geom(&$toRes, $orm)
    {
        if($orm instanceof ORMGIS)
            $toRes['the_geom'] = $orm->asgeojson_php;
    }

}