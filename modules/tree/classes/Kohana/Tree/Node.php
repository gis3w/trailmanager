<?php defined('SYSPATH') or die('No direct script access.');

class Kohana_Tree_Node {
    
    public $id;
    public $children;
    public $level;
    public $parent;
    
    
    public function __construct($params = NULL)
    {
        foreach($params as $key=>$val)
            if($key !== 'children')
                $this->$key = $val;
        if (isset($this->parent))
            $this->parent->addChild($this);
    }
    
    public function addChild(Tree_Node $node)
    {
        $this->children[] = $node;
    }
    
    /**
     * Tree_Leaf::children()
     * returns flat (non-tree) array with ids of all this leaf's descendants
     * @return array
     */
    public function children()
    {
        $children = array($this->id);
        foreach($this->children as $child)
            $children = array_merge($children,$child->children());
        return $children;
    }
    
}