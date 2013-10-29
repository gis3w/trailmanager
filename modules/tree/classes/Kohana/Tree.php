<?php defined('SYSPATH') or die('No direct script access.');

class Kohana_Tree {
    
    protected $_data;
    protected $_tree;
    protected $_tmp;
    
    protected $_root_node;
    
    public function __construct($data_arr = NULL)
    {
        if (is_array($data_arr) || ($data_arr instanceof Traversable))
        {
            $this->_data = $data_arr;
            $this->_build_tree();
        }
      
    }
    
    public static function factory($data_arr = NULL)
    {
        return new self($data_arr);
    }
    
    protected function _build_tree(Tree_Node $node_addto = NULL,$data =NULL)
    {
        if(!isset($data))
            $data = $this->_data;
        
        if(!isset($data['id']))
            $data['id'] = $data['datastruct'];

        $node = new Tree_Node($data);

        $this->add_node($node,$node_addto);

        if(isset($data['children']) AND is_array($data['children']) AND count($data['children']) !== 0)
            foreach($data['children'] as $child)
                $this->_build_tree($node,$child);
        
            
    }
    
    


    public function add_node(Tree_Node $node_toadd, Tree_Node $node_addto = NULL)
    {
        if(!isset($node_toadd->parent) AND !isset($node_addto) )
        {
            $this->_root_node = $node_toadd;
         }
         else
         {
             $node_addto->addChild($node_toadd);
             $node_toadd->parent = $node_addto;
         }
                       
        $this->_tree[$node_toadd->id] = $node_toadd;

    }
    
    public function data($data_arr)
    {
        $this->_data = $data_arr;    
        return $this;
    }
    
   
    public function get_tree_array()
    {
        return $this->_tree;
    }
    
        
    public function find($id)
    {
        return array_key_exists($id, $this->_tree) ? $this->_tree[$id] : NULL;
    }
    
    
    
   
    
   
    
    
    
    
}