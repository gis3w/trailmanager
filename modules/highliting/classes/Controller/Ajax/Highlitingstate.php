<?php defined('SYSPATH') or die('No direct script access.');


class Controller_Ajax_Highlitingstate extends Controller_Ajax_Base_Crud_GET{
    
    protected $_exeLogin = FALSE;

    protected $_pagination = FALSE;
    
    protected $_table = 'Highliting_State';
    

    

    protected function _get_data()
   {
       $orm = $this->_orm;

      if(isset($_GET['highliting_state_id']) AND $_GET['highliting_state_id'] != '')
      {
          if(is_numeric($_GET['highliting_state_id']))
          {
              $current_state = $_GET['highliting_state_id'];
              
              $this->user = Auth::instance()->get_user();
              
              switch($current_state)
              {
                  case HSTATE_IN_ACCETTAZIONE:
                      if($this->user->is_a('PROTOCOL') OR $this->user->is_a('ADMIN1') OR $this->user->is_a('ADMIN2'))
                        $states_to_filter = array(HSTATE_ACCETTATA,HSTATE_RIFIUTATA);
                  break;
              
                  case HSTATE_ACCETTATA:
                      if($this->user->is_a('PROTOCOL') OR $this->user->is_a('ADMIN1') OR $this->user->is_a('ADMIN2'))
                        $states_to_filter = array(HSTATE_ASSEGNATA_SUPERVISOR,HSTATE_PROGRAMMATA);
                  break;
              
                  case HSTATE_RIFIUTATA:
                  break;
              
                  case HSTATE_IN_ESECUZIONE:
                      if($this->user->is_a('EXECUTOR') OR $this->user->is_a('ADMIN1') OR $this->user->is_a('ADMIN2'))
                        $states_to_filter = array(HSTATE_SOSPESA,HSTATE_CHIUSA);
                  break;
              
                  case HSTATE_SOSPESA:
                      if($this->user->is_a('EXECUTOR') OR $this->user->is_a('ADMIN1') OR $this->user->is_a('ADMIN2'))
                        $states_to_filter = array(HSTATE_IN_ESECUZIONE);
                  break;
              
                  case HSTATE_PROGRAMMATA:
                      if($this->user->is_a('SUPERVISOR') OR $this->user->is_a('ADMIN1') OR $this->user->is_a('ADMIN2'))
                        $states_to_filter = array(HSTATE_CHIUSA);
                  break;
              
                  case HSTATE_ASSEGNATA_SUPERVISOR:
                      if($this->user->is_a('SUPERVISOR') OR $this->user->is_a('ADMIN1') OR $this->user->is_a('ADMIN2'))
                        $states_to_filter = array(HSTATE_IN_ESECUZIONE);
                  break;
              
                  case HSTATE_CHIUSA:
                      if($this->user->is_a('SUPERVISOR') OR $this->user->is_a('ADMIN1') OR $this->user->is_a('ADMIN2'))
                        $states_to_filter = array(HSTATE_NOTIFICATA);
                  break;
              
                  case HSTATE_NOTIFICATA:
                  break;
              }
              
              if(!isset($states_to_filter))
              {
                  $this->_orm->where('id','=',-1);
              }
              else
              {
                  $this->_orm->where('id','IN',DB::expr('('.implode(',', $states_to_filter).')'));
              }
              
          }
      }
       
       return $orm;
       
   }
    
}