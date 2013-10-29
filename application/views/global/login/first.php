<?php defined('SYSPATH') OR die('No direct access allowed.'); 

if(!$change)
{
    $frm = Form::open();
    $frm .= "<div class=\"input-prepend\">";
    $frm .= "<span class=\"add-on\"><i class=\"icon-lock\"></i></span>";
    $frm .= Form::password('password','',array('class'=>'input-large span10','placeholder'=>__('NUOVA PASSWORD')));
    $frm .= "</div>";
    $frm .= "<div class=\"input-prepend\">";
    $frm .= "<span class=\"add-on\"><i class=\"icon-lock\"></i></span>";
    $frm .= Form::password('ripeti_password','',array('class'=>'input-large span10','placeholder'=>__('CONFERMA NUOVA PASSWORD')));
    $frm .= "</div>";
    $frm .= Form::submit('submit', 'Cambia',array('class'=>'btn'));
    $frm .= Form::close();
    echo $frm;
}
