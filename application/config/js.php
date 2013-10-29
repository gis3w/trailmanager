<?php defined('SYSPATH') or die('No direct script access.');

return array(
    'js_compile' => FALSE,   // serve per il modulo jscompile per comprimere e unire in unico file i js
    'js_exlude_to_compile' => array(
        
    ),
    'js_path'=> 'public/js/',
    'js_base' => array(
		'../modules/jquery-1.10.1.min.js',
		'../modules/jquery-ui-1.10.3/ui/minified/jquery-ui.min.js',
		'../modules/bootstrap3/js/bootstrap.min.js',
		'../modules/jQuery-File-Upload-master/js/jquery.iframe-transport.js',
		'../modules/jQuery-File-Upload-master/js/jquery.fileupload.js',
		'../modules/jquery.fileDownload.js',
		'../modules/fullcalendar-1.6.1/fullcalendar/fullcalendar.min.js',
		'../modules/chosen_v1.0.0/chosen.jquery.min.js',
		'../modules/pixelmatrix-uniform-3e9cd85/jquery.uniform.min.js',
		'../modules/jWizard/jquery.jWizard.js',
		//'../modules/CLEditor1_3_0/jquery.cleditor.min.js',
		//'../modules/elfinder-2.0-rc1/js/elfinder.min.js',
		'../modules/Gritter-master/js/jquery.gritter.min.js',
		'../modules/noty-master/js/noty/jquery.noty.js',
		'../modules/noty-master/js/noty/layouts/top.js',
		'../modules/noty-master/js/noty/layouts/topCenter.js',
		'../modules/noty-master/js/noty/layouts/topRight.js',
		'../modules/noty-master/js/noty/layouts/topLeft.js',
		'../modules/noty-master/js/noty/themes/default.js',
		'../modules/DataTables-1.9.4/media/js/jquery.dataTables.min.js',
		'../modules/jquery-timepicker/jquery.timepicker.min.js',
		/*'../modules/jQuery-File-Upload/js/jquery.fileupload-process.js',
		'../modules/jQuery-File-Upload/js/jquery.fileupload-image.js',
		'../modules/jQuery-File-Upload/js/jquery.fileupload-audio.js',
		'../modules/jQuery-File-Upload/js/jquery.fileupload-video.js',
		'../modules/jQuery-File-Upload/js/jquery.fileupload-validate.js',
		'../modules/jQuery-File-Upload/js/jquery.fileupload-ui.js',*/
		//'../modules/jQuery-File-Upload/js/main.js',
		'../modules/jquery_layout/jquery.layout-latest.min.js',
		'global.js',
		'i18n.js',
		'utils.js',
		'config.js',
		'filter.js',
		'fileuploader2.js',
		'subforms.js',
		'anagrafica2.js',
		//'home.js',
		'main.js'
    ),
    'js_base_mobile' => array(
		'jquery-1.10.1.min.js',
        'bootstrap.min.js',
    ),
    'js_base_admin' => array(

     ),
);