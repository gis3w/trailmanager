<?php defined('SYSPATH') OR die('No direct access allowed.'); ?>
<!DOCTYPE html>
<html>
    <head>
        <style>
            body {
                font-size: 12px;
                font-family: sans-serif,monospace;
                -webkit-font-smoothing:antialiased;
                color:#444;
            }
            
            #content {
                margin-left:20px;
                width: 600px;
            }
          
        </style>
    </head>
    <body>
        <div id="content">
            <!--Logo-->
            <div id="logo" style="padding-bottom: 10px; border-bottom: 6px solid e6e6e6;">
            <img src="<?php echo $global_data['host_main']?>/public/img/<?php echo $layout['logo_email']; ?>">
            </div>
            <div id ="body" style="margin-top:20px; margin-bottom: 20px;">
                <?php echo  $body_content; ?>
            </div>
            <div id="footer" style="padding-top:10px; border-top:1px solid #e6e6e6;">
                <?php echo HTML::anchor($global_data['host_main']); ?>
                <p style="font-size: 90%; color:gray;">IoSegnalo gestisce i dati delle persone nel rispetto del Decreto Legislativo 30 giugno 2003, n. 196 "Codice in materia di protezione dei dati personali".</p>
            </div>
        </div>
    </body>
</html>