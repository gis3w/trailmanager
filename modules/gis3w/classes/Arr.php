<?php defined('SYSPATH') or die('No direct script access.');

class Arr extends Kohana_Arr {

    public static function push(array $a1){

        $result = array();
        
		for ($i = 0, $total = func_num_args(); $i < $total; $i++)
		{
			foreach (func_get_arg($i) as $key => $val)
			{
				if (isset($result[$key]))
				{
					if (is_array($val))
					{
                                                                                                                
						$result[$key] = Arr::merge($result[$key], $val);
                                                
					}
					elseif (is_int($key))
					{
						// Indexed arrays are appended
						array_push($result, $val);
					}
					else
					{
						// Associative arrays are replaced
						$result[$key] = $val;
					}
				}
				else
				{
					// New values are added
					$result[$key] = $val;
				}
			}
		}

		return $result;

    }
    
    public static function to_arrayjres($array,$method,$model,$to_remove = NULL)
    {
        if($to_remove === NULL)
        {
            // recuper array filed da cassare
            $to_remove = Kohana::$config->load('respost_ajax.method');
            $to_remove = isset($to_remove[$method][$model]['not_to_show']) ? $to_remove[$method][$model]['not_to_show'] : array();

        }
        
        foreach($to_remove as $field)
        {
            unset($array[$field]);
        }
        
        return $array;
        
    }
    
    public static function sort_by_keys($toSort, $keySort)
    {
        $toRet = array();
        foreach($keySort as $key)
            if(isset($toSort[$key]))
                $toRet[$key] = $toSort[$key];
        
        return $toRet;
        
    }
}