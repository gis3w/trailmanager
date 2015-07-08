<?php defined('SYSPATH') or die('No direct script access.');


class Controller_Ajax_Userdata extends Controller_Ajax_Registration{

    protected $_datastruct = "Front_Userdata";


    public function before() {
        parent::before();
        // check if user is a reporter
        if(!$this->_orm->is_a(ROLE_REPORTER))
            throw new HTTP_Exception_500(SAFE::message('ehttp','invalid_operation'));
    }


    public function action_Create() {
         throw new HTTP_Exception_500(SAFE::message('ehttp','invalid_operation'));
    }
    
    public function action_update() {
        Controller_Ajax_Admin_User::action_update();
    }
    
    public function action_index() {
        if($this->id == 'list')
            throw new HTTP_Exception_500(SAFE::message('ehttp','invalid_operation'));
        
        // check is user_id request is the same fo asker
        if($this->id != Auth::instance()->get_user()->id)
            throw new HTTP_Exception_500(SAFE::message('ehttp','invalid_operation'));
        
        Controller_Ajax_Admin_User::action_index();
    }
     
    protected function _edit() {
        try{
            Database::instance()->begin();
            
                $this->_validation_user();
                
                $_POST['data_mod'] = time();
                
                $_tmp_post = array();
                foreach ($_POST as $k => $v)
                {
                    if($v != '')
                        $_tmp_post[$k] = $v;
                }
                $_POST = $_tmp_post;
                
                // si passa al slvataggio vero e proprio
                $this->_orm->values($_POST)->save();

                // salvataggio degli user_data
                $user_data  = $this->_orm->user_data;
                $user_data->values($_POST);
                $user_data->save();
                
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
    
    protected function _validation_user()
    {
        
        $this->_vorm = Validation::factory($_POST);
        
        // si aggiungono le rules per user
        foreach($this->_orm->rules() as $field => $rules)
            $this->_vorm->rules($field,$rules);


        foreach($this->_orm->update_my_data_rules() as $field => $rules)
            $this->_vorm->rules($field,$rules);
           
        
        // si aggiungono le rules per user_data
        foreach($this->_orm->user_data->rules() as $field => $rules)
            $this->_vorm->rules($field,$rules);
        
        foreach($this->_orm->user_data->extra_rules() as $field => $rules)
            $this->_vorm->rules($field,$rules);
        
        // add labels
        $this->_vorm->labels($this->_orm->labels());
        $this->_vorm->labels($this->_orm->user_data->labels());
        
          if(!$this->_vorm->check())
            $this->vErrors = Arr::push ($this->vErrors,$this->_vorm->errors('validation'));
        
        if(!empty($this->vErrors))
                throw new Validation_Exception($this->_vorm);
        
    }
      
  
}