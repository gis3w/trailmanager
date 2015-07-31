<?php defined('SYSPATH') OR die('No direct access allowed.'); ?>
<!DOCTYPE html>
<html>
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
        <meta name="viewport" content="initial-scale=1.0, user-scalable=no" />
		<meta http-equiv="X-UA-Compatible" content="IE=edge">
        <title><?php if (isset($title)) echo $title; ?></title>
        <link type="text/css" id="bootstrapCSS" href="/public/css/../modules/bootstrap-3.3.1/css/bootstrap.min.css" rel="stylesheet" media="screen" />
        <script type="text/javascript" src="/public/js/../modules/jquery-1.11.1.min.js"></script>
        <script type="text/javascript" src="/public/js/../modules/bootstrap-3.3.1/js/bootstrap.min.js"></script>
    </head>
    
   
    <body>
        <nav class="navbar navbar-inverse navbar-fixed-top" role="navigation">
            <div class="container-fluid">
            <div class="navbar-header">
                <button type="button" class="navbar-toggle" data-toggle="collapse" data-target="#main_navbar_admin">
                    <span class="sr-only">Toggle navigation</span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
              </button>
                <a class="navbar-brand" href="#" style="padding: 0px"><?php echo HTML::image($img_path.$logo_navbar,array('alt'=>'IoSegnalo'/*, 'class'=>'img-responsive'*/)); ?></a>
            </div>
            </div>
        </nav>
        <div style="margin-top:100px;">
            <h1><?php echo HTML::image($img_path.'work.png',array('alt'=>'Work in progress'/*, 'class'=>'img-responsive'*/)); ?> Servizio in manutenzione</h1>
            <h2>Il servizio sarà ripristinato quanto prima</h2>
        </div>
        
             
    </body>
</html>
