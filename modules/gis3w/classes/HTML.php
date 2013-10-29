<?php defined('SYSPATH') or die('No direct script access.');

class HTML extends Kohana_HTML {

    const CRUD_ACTION_DEL = 0;
    const CRUD_ACTION_EDIT = 1;
    const CRUD_ACTION_ALL = 2;


    /**
     * Metodo che rente una tabella passandogli una serie di paramentri sotto forma di matrice
     * @global <type> $tpUtenteTb
     */
    public static function table($data,array $param=null,array $caption=NULL){
        $add = '';
        if(isset($param)){
            foreach($param as $key => $val){
                $add .= $key.'="'.$val.'" ';
            }
        }
        
        
        
        $output = "<table ".$add.">\n";
		// impostazione dei titoli
        $output .= "<thead><tr class='ui-widget-header head_table'>\n";

        //se esiste caption per navigare si usa quello altrimenti si usa data
        if (isset($caption) AND !empty($caption)){

            // si prende il primo risultato dell'array per vedere se ci sono tutti i campi
            $toconf = current($data);
            
            if($toconf instanceof ORM)
                $toconf = $toconf->as_array();

            foreach($caption as $c => $cd){
                if(array_key_exists($c, $toconf)){
                    
                    // per la laghezza
                    $style = '';
                    if(isset($cd['style']))
                    {
                        $style = 'style="';
                        
                        foreach($cd['style'] as $cs => $vs)
                        {
                            $style .= $cs.':'.$vs.';';
                        }
                        
                        $style .= '"';
                    }
                    
                    $class = '';
                     if(isset($cd['class']))
                    {
                                              
                        foreach($cd['class'] as $cs)
                        {
                            $class .= ' '.$cs;
                        }

                    }
                    
                    
                    $output .= '<th class="tb_head'.$class.'" '.$style.'>';
                    $output .= (isset($cd['title'])) ? $cd['title'] : ucfirst(str_replace("_", " ", Tradu::label($c)));
                    $output .= "</th>\n";
                }
            }
            $output .= "</tr></thead>\n";

            $output .= "<tbody>\n";

            foreach($data as $dt){
                
                
            if($dt instanceof ORM)
                $dt = $dt->as_array();

                $tr_class = "";

                
                $output .= "<tr ".$tr_class.">\n";

                  foreach($caption as $c => $cd){
                      
                             // per la laghezza
                            $style = '';
                            if(isset($cd['style']))
                            {
                                $style = 'style="';

                                foreach($cd['style'] as $cs => $vs)
                                {
                                    $style .= $cs.':'.$vs.';';
                                }

                                $style .= '"';
                            }

                            $class = '';
                             if(isset($cd['class']))
                            {

                                foreach($cd['class'] as $cs)
                                {
                                    $class .= ' '.$cs;
                                }

                            }
                      
                            if(array_key_exists($c, $dt)){
                                $output .= "<td class='".$class."'".$style.">" . $dt[$c] . "</td>\n";
                            }
                  }

              $output .= "</tr>\n";
            }

        }else{
            
            $toconf = $data[0];
            
//            var_dump($data[0]);
//            exit;
            
            if($toconf instanceof ORM)
                $toconf = $toconf->as_array();

            foreach($toconf as $c=>$v){
                $output .= '<th ';
                        //if(isset($wcol[$nc]) && $wcol[$nc] != -1) $output .= " style='width:".$wcol[$nc]."px;'";
                $output .= '>';
                $output .= ucfirst(str_replace("_", " ", $c));
                $output .= "</th>\n";
            }
            $output .= "</tr></thead>\n";

            $output .= "<tbody>\n";

            foreach($data as $n=>$dt){
                
                if($dt instanceof ORM)
                    $dt = $dt->as_array();
                
              $output .= "<tr>\n";
              foreach($dt as $c=>$v){

                  $output .= "<td class=\"td_".$c."\">" . $v . "</td>\n";

              }
              $output .= "</tr>\n";
            }

        }
    $output .= "</tbody>\n";
    $output .= "</table>\n";

    return $output;
    }

    public static function showErr($params){
        // ricostrusione del msg
        $msg = $params['msg'];
        if(is_array($msg)){
            $str = "<ul>";
            foreach ($msg as $f => $v){
                if($f == '_external' && is_array($v)){

                    foreach($v as $ef => $ev ){

                        $str .= "<li>".$ev."</li>";
                    }
                    
                }else{

                    $str .= "<li>".$v."</li>";

                }

            }
            $str .= "</ul>";
            $msg = $str;
        }
        switch ($params['type']){
		case 'hight':
			$title = "OK !!";
			$output = '<div id="msg" class="ui-widget">';
			$output .= '<div style="padding: 0pt 0.7em;" class="ui-state-highlight ui-corner-all">';
			$output .=	'<p><span style="float: left; margin-right: 0.3em;" class="ui-icon ui-icon-info"/></span>';
			$output .=	'<b>'.$title.'!</b><br />'.$msg.'</p>';
			$output .=	'</div>';
			$output .=	'</div>';
		break;
            
                      case 'msg':
			$output = '<div id="msg" class="ui-widget">';
			$output .= '<div style="padding: 0pt 0.7em;" class="ui-state-msg ui-corner-all">';
			$output .=	'<p>'.$msg.'</p>';
			$output .=	'</div>';
			$output .=	'</div>';
		break;

		default:
			$title = "Attenzione";
			$output = '<div id="msg" class="ui-widget">';
			$output .= '<div style="padding: 0pt 0.7em;" class="ui-state-error ui-corner-all">';
			$output .=	'<p><span style="float: left; margin-right: 0.3em;" class="ui-icon ui-icon-alert"/></span>';
			$output .=	'<b>'.$title.'!</b><br />'.$msg.'</p>';
			$output .=	'</div>';
			$output .=	'</div>';
	}
	return $output;
    }

    public static function block($title='blocco',$big_buttons = array()){
        $nbb = count($big_buttons);
        $w = $nbb * 155;
//        width:".$w."px;
        $out  = "<div id='block_'$title class='ui-widget ui-widget-content ui-corner-all ' style = ' float:left; margin:10px;'>";
            $out .= "<div class='ui-widget-header ui-corner-tl ui-corner-tr'>";
                $out .= strtoupper($title);
            $out  .= "</div>";
            $out .= "<div>";
                foreach($big_buttons as $bb)
                $out .= $bb;
            $out  .= "</div>";
        $out  .= "</div>";

        return $out;
    }

    public static function crud_action($controller,$id,$mod=HTML::CRUD_ACTION_ALL){

        $action = '';

        if ($mod == HTML::CRUD_ACTION_EDIT || $mod == HTML::CRUD_ACTION_ALL){

            $action .= " ".html::anchor('/'.$controller.'/edit/'.$id,'Modifica',array('class'=>'mod_button'));

        }

        if ($mod == HTML::CRUD_ACTION_DEL || $mod == HTML::CRUD_ACTION_ALL){
        
            $action .= " ".html::anchor('/'.$controller.'/del/'.$id,'Elimina',array('class'=>'del_button'));

            
        }

       return  $action;
    }

    public static function breadcrumbs($bcs){
        $out = "<div id=\"breadcrumbs\">";
        foreach($bcs as $n => $c){
            $pre = $n == 0 ? "::":" ->";
            $out .= $pre." ".self::anchor($c[1], $c[0]);
        }
        $out .= "</div>";
        return $out;
    }


    public static function html2rgb($color){
        if ($color[0] == '#')
            $color = substr($color, 1);

        if (strlen($color) == 6)
            list($r, $g, $b) = array($color[0].$color[1],
                                     $color[2].$color[3],
                                     $color[4].$color[5]);
        elseif (strlen($color) == 3)
            list($r, $g, $b) = array($color[0].$color[0], $color[1].$color[1], $color[2].$color[2]);
        else
            return false;

        $r = hexdec($r); $g = hexdec($g); $b = hexdec($b);

        return array($r, $g, $b);
    }

    public static function rgb2html($r, $g=-1, $b=-1){
        if (is_array($r) && sizeof($r) == 3)
            list($r, $g, $b) = $r;

        $r = intval($r); $g = intval($g);
        $b = intval($b);

        $r = dechex($r<0?0:($r>255?255:$r));
        $g = dechex($g<0?0:($g>255?255:$g));
        $b = dechex($b<0?0:($b>255?255:$b));

        $color = (strlen($r) < 2?'0':'').$r;
        $color .= (strlen($g) < 2?'0':'').$g;
        $color .= (strlen($b) < 2?'0':'').$b;
        return '#'.$color;
    }

    /**
     * Metodo statico per la costruzione dei menu
     * @param array $menu_link contiene un aserrie di array coppia con testo e link di collegamento
     */
    public static function menu(array $menu_link){

        $out = "<ul class=\"menu\">";

            foreach($menu_link as $item){
				$id = count($item)>=3?"id=\"".$item[2]."\"":"";
				$class = count($item)==4?"class=\"".$item[3]."\"":"";
                $out .= "<li ".$class."><a href=\"".$item[1]."\" ".$id.">".$item[0]."</a></li>";

            }

         $out .= "</ul>";

         return $out;

    }

    public static function form_target(){

        // calcolo del target
        $target = Request::$current->directory() ? Request::$current->directory()."/".Request::$current->controller(): Request::$current->controller();

        return $target."/".Request::$current->action();

    }

}