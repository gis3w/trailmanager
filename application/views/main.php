<?php defined('SYSPATH') OR die('No direct access allowed.'); ?>
<!DOCTYPE html>
<html>
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
        <meta name="viewport" content="initial-scale=1.0, user-scalable=no" />
        <title><?php if (isset($title)) echo $title; ?></title>
        <link rel="shortcut icon" href="<?php echo $img_path ?>turisticgis.ico" type="image/x-icon" />

        <?php if(isset($jspre) && $jspre !=''): ?>
            <script type="text/javascript"><?php echo $jspre; ?></script>
        <?php endif; ?>
        <?php
                // css link
                if(isset($css_cache))
                {
                    echo $css_cache."\n";
                }
                elseif(isset($csss) && !empty($csss))
                    {
                    foreach ($csss as $file => $type)
                    {
                        switch($type)
                        {
                            case "directory":
                            break;
                            default:
                                echo HTML::style($css_path.$file, array('media' => $type)). "\n";
                        }
                        
                    }
                }
                
           ?>
        <?php
                // js link
                if(!empty($jss)){
                    foreach ($jss as $file) echo HTML::script($js_path.$file). "\n";
                }
                if(isset($js_cache))
                    echo $js_cache."\n";
            ?>
         <?php if(isset($jspge) && $jspage !=''): ?>
            <script type="text/javascript"><?php echo $jspage; ?></script>
         <?php endif ?>
            
            
    </head>
    
    <?php
        $bodyClass = isset($tlogin) ? '' : '';
    ?>

    <body>
        
        <?php
            if(isset($tnavbar))
                echo $tnavbar;
            ?>
        
        <?php
            if(isset($tlogin))
                echo $tlogin;
            ?>
        
        <?php
            if(isset($tcontent))
                echo $tcontent;
            ?>
        
        <?php if(isset($tnorth)): ?>
            <div class="ui-layout-north"> <!--data-options="region:'north',split:false" style="height:100px;"--> 
                <?php echo $tnorth; ?>
            </div>
        <?php endif; ?>
        <?php if(isset($tsouth)): ?>
            <div class="ui-layout-south"> <!--data-options="region:'south',split:true" style="height:50px;"--> 
                <?php echo $tsouth; ?>
            </div>
        <?php endif; ?>
        <?php if(isset($teast)): ?>
            <div class="ui-layout-east"> <!--data-options="region:'east',title:'East',collapsed: true,split:true" style="width:100px;"--> 
                <?php echo $teast; ?>
            </div>
        <?php endif; ?>
        <?php if(isset($twest)): ?>
            <div class="ui-layout-west"> <!--data-options="region:'west',title:'Sotto sezioni',split:true" style="width:200px;"--> 
                <?php echo $twest; ?>
            </div>
        <?php endif; ?>
        <?php if(isset($tcenter)): ?>
            <div class="ui-layout-center"> <!--data-options="region:'center',title:'center title'" style="padding:5px;background:#eee;"--> 
                <?php echo $tcenter; ?>
            </div>
        <?php endif; ?>
             
    </body>
</html>
