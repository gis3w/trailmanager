<?php defined('SYSPATH') or die('No direct script access.');

return array(
    'title_default' => 'TuristicGis',
    'main_tpl' => 'main',
    'mobile_main_tpl' => 'mobile/main',
    'img_path' => 'public/img/',
    'logo_main' => 'logo_turisticgis_50h.png',
    'logo_navbar' => 'logo_turisticgis_40h.png',
    'logo_print' => 'logo_turisticgis_50h.png',
    'logo_email' => 'logo_turisticgis_30h.png',
    'css_compile' => FALSE,
    'css_path' => 'public/css/',
    'css_base' => array(
		'../modules/jquery-ui-1.10.3/themes/base/minified/jquery-ui.min.css' => 'screen',
        '../modules/bootstrap3/css/bootstrap.min.css' => 'screen',
		//'../modules/bootstrap3/css/bootstrap-theme.min.css' => 'screen',
		'../modules/fullcalendar-1.6.1/fullcalendar/fullcalendar.css' => 'screen',
		'../modules/chosen_v1.0.0/chosen.css' => 'screen',
		'../modules/leaflet-0.7.2/leaflet.css' => 'screen',
		'../modules/Leaflet.draw/dist/leaflet.draw.css' => 'screen',
		//'../modules/pixelmatrix-uniform-3e9cd85/themes/default/css/uniform.default.css' => 'screen',
		'../modules/jQuery-File-Upload-master/css/jquery.fileupload-ui.css' => 'screen',
		'../modules/jWizard/jquery.jWizard.css' => 'screen',
		//'../modules/CLEditor1_3_0/jquery.cleditor.css' => 'screen',
		//'../modules/elfinder-2.0-rc1/css/elfinder.min.css' => 'screen',
		'../modules/Gritter-master/css/jquery.gritter.css' => 'screen',
		'../modules/DataTables-1.9.4/media/css/jquery.dataTables.css' => 'screen',
		'../modules/font-awesome/css/font-awesome.min.css' => 'screen',
		'../modules/font-awesome/css/font-awesome-ie7.css' => 'screen',
		'../modules/jquery-timepicker/jquery.timepicker.css' => 'screen',
		//'../modules/jquery_layout/layout-default-latest.css' => 'screen',
        'style.css' => 'screen',
        'layout.css' => 'screen',
    ),
    'css_base_mobile' => array(
        /*'../modules/bootstrap3/css/bootstrap.min.css' => 'screen',
        '../modules/bootstrap3/css/bootstrap-responsive.min.css' => 'screen',
        'layout_mobile.css' => 'screen',*/
    ),
    'css_base_admin' => array(
    ),
    
);
