<?php defined('SYSPATH') or die('No direct script access.');


class   Controller_Ajax_Base_Sheet extends Controller_Ajax_Admin_Highliting_Sheet_Base{
    
    protected $_exeLogin = FALSE;
    
    protected $_inheritDatastructName;


    protected  $_multiFilesToSave = array(
                'front_image_highliting_poi' => 'Image_Highliting_Poi',
                'front_image_highliting_path' => 'Image_Highliting_Path',
                'front_image_highliting_area' => 'Image_Highliting_Area'
    );
    
    public function action_update() {
        throw new HTTP_Exception_500(SAFE::message('ehttp','invalid_operation'));
    }
    
    public function action_delete() {
       throw new HTTP_Exception_500(SAFE::message('ehttp','invalid_operation'));
    }
    
     public function action_index() {
       throw new HTTP_Exception_500(SAFE::message('ehttp','invalid_operation'));
    }
    
    public function before() {
        parent::before();
        if(Auth::instance()->logged_in())
            $this->user = Auth::instance ()->get_user ();
        // we set highliting state
        $_POST['highliting_state_id'] = HSTATE_IN_ACCETTAZIONE;
        // erase front from datastructname
        $this->_inheritDatastructName = substr($this->_datastructName, 6);
    }

    
     protected function _data_edit()
    {
        Filter::emptyPostDataToNULL();
        
        $this->_set_the_geom_edit();        
        
        if(isset($this->user))
            $this->_orm->highliting_user_id = $this->user->id;
        
        // publish by default
        $this->_orm->publish = TRUE;
        
         $this->_orm->values($_POST);
         $this->_orm->data_ins = $this->_orm->data_mod = time();
         $this->_orm->save();
         
          // for annonimous segnalation
         if(!isset($this->user))
         {
             $this->_orm->anonimous_data->values($_POST);
             $fk = strtolower($this->_inheritDatastructName).'_id';
             $this->_orm->anonimous_data->$fk = $this->_orm->id;
             $this->_orm->anonimous_data->save();
         }
         
         
         $this->_save_files_1XN();
         
         // WE SEND EMAIL FOR CONFIRM E ALERT TO PROTOCOL USER
         $mail = new Email_Newhighliting($this->_orm);
         $mail->send();
                  
    }
    
    protected function _validation()
    {

        $this->_vorm = Validation::factory($_POST);

        // si aggiungono le validazioni dell'orm
        foreach ($this->_orm->rules() as $col => $rule)
            $this->_vorm->rules($col, $rule);      

        // si aggiungono anche le labels
       $this->_vorm->labels($this->_orm->labels());
       // if user is anonimous
       
    

       if(!$this->user)
       {
           $anonimous_data = $this->_orm->anonimous_data;
            foreach ($anonimous_data->rules() as $col => $rule)
                 $this->_vorm->rules($col, $rule);      

             // si aggiungono anche le labels
            $this->_vorm->labels($anonimous_data->labels());
       }


      if(!$this->_vorm->check())
        $this->vErrors = Arr::push ($this->vErrors,$this->_vorm->errors('validation'));

    if(!empty($this->vErrors))
            throw new Validation_Exception($this->_vorm);
        
    }
    
    
}