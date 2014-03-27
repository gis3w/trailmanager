<?php defined('SYSPATH') or die('No direct script access.');


class Controller_Ajax_Base_Crud_Vehicle extends Controller_Ajax_Base_Crud{

    protected $_join_inter_tb;
    
    protected $_join_inter_tb_fkey;




//    protected function _get_data() {
//
//         // per il _get_item()
//       if(is_numeric($this->id))
//           return parent::_get_data();
//       
//        $ormStart = DB::select($this->_table_rid.".id")
//            ->from($this->_table_rid);
//        
//        // si esegue il join con la tabella delle associazioni alla unita_rpoduttiva
//        $ormStart ->join($this->_join_inter_tb,'LEFT')
//                        ->on($this->_table_rid.'.id','=',$this->_join_inter_tb.'.'.$this->_table_rid.'_id')
//                         ->and_where_open()
//                            ->where($this->_join_inter_tb.'.'.$this->_join_inter_tb_fkey.'_id','IS',DB::expr('NULL'))
//                            ->or_where($this->_table_rid.'_id','IN',DB::expr(SAFEDB::joinTbAssoc($this->_table_rid, $this->_join_inter_tb)))
//                          ->and_where_close()
//                         ->group_by($this->_table_rid.".id");
//               
//        return $ormStart;
//        
//       
//        
//        
//    }
//    
//    public function _get_list()
//    {
//        
//        $ormStart = $this->_get_data();
//        
//        $orms = $this->_manage_orm_filter_page($ormStart,"execute");
//        
//        foreach($orms as $orm)
//            $this->_build_res($this->_single_request_row(ORM::factory($this->_table,$orm['id'])));
//
//       $this->jres->data->items = array_values($this->_res);
//
//    }
    
       /**
     * Metodo generale di validazione per 
     */
    protected function _general_validation()
    {
          $this->_vorm = Validation::factory($_POST);
        
        // si aggiungono le rules per user
//        $this->_vorm->rule('azienda_id','not_empty');
//        $this->_vorm->labels($this->_orm->labels());
        
          if(!$this->_vorm->check())
            $this->vErrors = Arr::push ($this->vErrors,$this->_vorm->errors('validation'));
        
        if(!empty($this->vErrors))
                throw new Validation_Exception($this->_vorm);
    }
    
}