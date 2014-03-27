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
            .monthcheck-table-scadenze td,
            .checklist-company-table-expired td{
                border-top: 1px solid #222;
                padding:3px;
                font-size:12px;
            }
            .monthcheck-td-tipo_scadenza,
            .monthcheck-td-data_scadenza{
                width:120px;
                text-align: center;
            }
            
            .monthcheck-table-scadenze,
            .checklist-company-table-expired {
                border-top: 1px solid gray;
                border-bottom: 1px solid gray;
                cellspacing: 0px;
                width:100%;
            }
            .monthcheck-table-scadenze th,
            .checklist-table th,
            .checklist-company-table-expired th
            {
                background-color: #E0E2FF;
                font-size:12px;
            }
            .monthcheck-azienda-title,
            .checklist-azienda-title{
                background-color: #aed0ea;
                padding: 4px;
            }
             .monthcheck-azienda {

                margin-bottom: 80px;
            }
        </style>
    </head>
    <body>
        <div id="content">
            <!--Logo-->
            <div id="logo" style="padding-bottom: 10px; border-bottom: 6px solid e6e6e6;">
            <!--<img src="<?php //echo $logo_email_absolute ?>">-->
            <img src="https://www.safe3.eu/public/img/<?php echo $layout['logo_email']; ?>">
            </div>
            <div id ="body" style="margin-top:20px; margin-bottom: 20px;">
                <?php echo  $body_content; ?>
            </div>
            <div id="footer" style="padding-top:10px; border-top:1px solid #e6e6e6;">
                <?php echo HTML::anchor(Kohana::$config->load('global.host_main')); ?>
                <p style="font-size: 90%; color:gray;">Safe3 gestisce i dati delle persone e delle aziende nel rispetto del Decreto Legislativo 30 giugno 2003, n. 196 "Codice in materia di protezione dei dati personali".</p>
            </div>
        </div>
    </body>
</html>