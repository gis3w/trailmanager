<?php defined('SYSPATH') or die('No direct script access.');


class Controller_Ajax_Admin_Poi extends Controller_Ajax_Admin_Sheet_Base{
    
    protected $_pagination = FALSE;
    
    protected $_datastruct = "Poi";



    protected function _set_typologies_edit()
    {
        $this->_set_typologies_by_fields();
    }

    protected function _get_other_typologies_by_fields($field_typology_id)
    {
        $TYPOLOGY_MAP = Kohana::$config->load('typologies')['TYPOLOGY_MAP'];
        $typology_ids = [];
        foreach($TYPOLOGY_MAP as $field => $tid)
        {
            if(isset($this->_orm->{$field}) AND $this->_orm->{$field} != '' AND $field != $field_typology_id)
                $typology_ids[] = $tid;
        }
        return $typology_ids;
    }

    /**
     * Set the typologies and main typology
     */
    protected function _set_typologies_by_fields()
    {
        $TYPOLOGY_MAP = Kohana::$config->load('typologies')['TYPOLOGY_MAP'];
        $PT_INTER_TYPOLOGY_ORDER = Kohana::$config->load('typologies')['PT_INTER_TYPOLOGY_ORDER'];

        #check order DEGRADO -> PUNTI INTERESSE -> SEGNALISTICA

        $checkPuntoInteresse = FALSE;
        $pt_inter_fields = array_keys($PT_INTER_TYPOLOGY_ORDER);
        $orm = $this->_orm->as_array();
        array_walk($pt_inter_fields,function($field,$key) use (&$checkPuntoInteresse,$orm)
        {
            $checkPuntoInteresse = ($checkPuntoInteresse OR (bool)$orm[$field]);
        });

        if(isset($this->_orm->fatt_degr) AND $this->_orm->fatt_degr != '')
        {
            $this->_orm->typology_id = $TYPOLOGY_MAP['fatt_degr'];
            $typology_ids = $this->_get_other_typologies_by_fields('fatt_degr');
        }
        elseif($checkPuntoInteresse)
        {
            //select typology by order
            $typology_order = 0;
            foreach($PT_INTER_TYPOLOGY_ORDER as $field => $order)
            {
                if(isset($this->_orm->{$field}) AND $PT_INTER_TYPOLOGY_ORDER[$field] >= $typology_order)
                {
                    $typology_order = $PT_INTER_TYPOLOGY_ORDER[$field];
                    $typology_id = $TYPOLOGY_MAP[$field];
                    $typology_key = $field;
                }

            }
            $this->_orm->typology_id = $typology_id;
            $typology_ids = $this->_get_other_typologies_by_fields($typology_key);
        }
        else
        {
            if(isset($this->_orm->nuov_segna) AND $this->_orm->nuov_segna != '')
            {
                $this->_orm->typology_id = $TYPOLOGY_MAP['nuov_segna'];
                $typology_ids = $this->_get_other_typologies_by_fields('nuov_segna');
            }
            elseif(isset($this->_orm->stato_segn) AND $this->_orm->stato_segn != '')
            {
                $this->_orm->typology_id = $TYPOLOGY_MAP['stato_segn'];
                $typology_ids = $this->_get_other_typologies_by_fields('stato_segn');

            }
            else
            {
                $this->_orm->typology_id = $TYPOLOGY_MAP['tipo_segna'];
                $typology_ids = $this->_get_other_typologies_by_fields('tipo_segna');
            }
        }

        // save orm
        $this->_orm->save();
        $_POST['typologies'] = $typology_ids;
        parent::_set_typologies_edit();
    }

    protected function _single_request_row($orm)
    {
        $toRes = parent::_single_request_row($orm);
        $toRes['path_id'] = $orm->paths->find()->id;
        $toRes['title'] = $orm->idwp;
        return $toRes;
    }
    
  
}