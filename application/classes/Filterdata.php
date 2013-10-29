<?php defined('SYSPATH') or die('No direct script access.');

class Filterdata extends Kohana_Formstruct {
    
    protected $_filters;
    
    protected $_order_to_render;

    protected $_filter_default = array();



    protected function __construct() {
            
        $this->user = Auth::instance()->get_user();
        $this->_initialize();

    }
    
    /**
     * Si aggiungono quelli di default se ce ne sono
     */
    protected function _initialize()
    {
        foreach ($this->_filter_default as $nameFilter => $param)
            $this->add_filter ($nameFilter, $param);
    }

    public static function factory($nameFilter)
    {
        $class = "Filterdata_".Text::ucfirst($nameFilter, "_");
        return new $class();
    }
    
    public function add_filter($nameFilter, array $param)
    {
        $this->_filters[$nameFilter] = $param;
    }
    
    protected function _build_param()
    {
        $fparam = func_get_args();
        $param = array(
            "form_input_type" => $fparam[0],
            "label" => $fparam[1],
        );
        
        if(isset($fparam[2]))
            $param['values'] = $fparam[2];
        
        return $param;
    }


    /**
     *  Metodo che costruisce un array chiave valore per i filtri di tipo select
     * @param type $orm
     * @param type $name_col
     * @param type $value_col
     * @return array
     */
    protected function _build_values($orm, $name_col,$value_col)
    {
        $values = array();
         foreach($orm as $data)
        {
            $values[] = array(
                "value" => $data->$value_col,
                "name" => $data->$name_col,
            );
        }
        return $values;
    }


    public function render($mode = NULL,$data = NULL)
    {
     
        if(isset($mode))
        {
            $method = "_render_".$mode;
            if(method_exists($this, $method))
                    return $this->$method($data);
        }
        else
        {
            if(isset($this->_order_to_render))
            {
                return Arr::sort_by_keys($this->_filters, $this->_order_to_render);
            }
            else
            {
                return $this->_filters;
            }
            
        }
                
       
    }
    
    protected static function _values_to_options($values)
    {
        $options = array('' => '');
        foreach($values as $value)
        {
            $options[$value['value']] = $value['name'];
        }
        return $options;
    }

        // vari metodi per il rendering
    protected function _render_html($data = NULL)
    {
        $html = '';
        foreach ($this->_filters as $name => $params)
        {
            $default_value = (isset($data[$name]) AND $data[$name]) ? $data[$name] : '';
            switch ($params['form_input_type'])
            {
                case self::INPUT:
                case self::DATE:
                    $label = Form::label($name,$params['label'],array("class" => "control-label"));
                    $input = Form::input($name,$default_value);   
                break;
            
                case self::SELECT:
                    $label = Form::label($name,$params['label'],array("class" => "control-label"));
                    $input = Form::select($name, self::_values_to_options($params['values']),$default_value);
                break;
            
                
            }
            if(isset($input) AND $input)
            {
                $html .= "<div class=\"control-group\">";
                $html .= $label;
                $html .= "<div class=\"controls\">".$input."</div>";
                $html .= "</div>";
            }
                
                
        }
        
        if($html)
            $toret =  Form::open('',array("id" => "formfilter-data","class" => "form-horizontal"));
            $toret .= $html;
            $toret .= Form::close();
            $toret .= Form::open('',array("id" => "formfilter-submit","method" => "GET","class" => "form-horizontal"));
            $toret .= Form::hidden('filter');
            $toret .= "<div class=\"control-group\">";
            $toret .= "<div class=\"controls\">".Form::button ('applica', __('Applica'),array("class" => "btn btn-primary"))."</div>";
            $toret .= "</div>";
            $toret .= Form::close();
            
        return $toret;
    }
    
}