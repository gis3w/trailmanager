<?php defined('SYSPATH') OR die('No direct access allowed.'); 

$frm = Form::open();
$frm .= "<div class=\"input-prepend\">";
$frm .= "<span class=\"add-on\"><i class=\"icon-user\"></i></span>";
$frm .= Form::input('username', $username,array('class'=>'input-large span10','placeholder'=>__('UTENTE')));
$frm .= "</div>";
$frm .= "<div class=\"input-prepend\">";
$frm .= "<span class=\"add-on\"><i class=\"icon-lock\"></i></span>";
$frm .= Form::password('password','',array('class'=>'input-large span10','placeholder'=>__('PASSWORD')));
$frm .= "</div>";
$frm .= "<div class=\"input-prepend\">";
$frm .= Form::submit('submit', 'Accedi',array('class'=>'btn button-login'));
$frm .= "</div>";
$frm .= Form::close();
echo $frm;
