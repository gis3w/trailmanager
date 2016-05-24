<?php defined('SYSPATH') or die('No direct script access.');

return array(
    'js_compile' => FALSE,   // serve per il modulo jscompile per comprimere e unire in unico file i js
    'js_path'=> 'public/js/',
    'js_base' => array(
    		
		'../modules/jquery-1.11.1.min.js',
    	'../modules/underscore-min.js',
    	'../modules/backbone-min.js',
		'../modules/jquery-ui-1.10.3/ui/minified/jquery-ui.min.js',
    	'../modules/d3/d3.min.js',
    	'../modules/c3-0.4.10/c3.min.js',
		'../modules/tinymce/tinymce.min.js',
		//'../modules/tinymce/jquery.tinymce.min.js',
		//'../modules/AnimatedContentMenu/js/AnimatedContentMenu.js',
		//'../modules/jQuery-File-Upload-9.5.7/js/vendor/jquery.ui.widget.js',
		'../modules/JavaScript-Load-Image-1.11.1/js/load-image.min.js',
		'../modules/JavaScript-Canvas-to-Blob-master/js/canvas-to-blob.min.js',
		//'../modules/bootstrap3/js/bootstrap.min.js',
		//'../modules/bootstrap-3.1.1/js/bootstrap.min.js',
		'../modules/bootstrap-3.2.0/js/bootstrap.min.js',
		'../modules/Gallery-2.15.1/js/blueimp-helper.js',
		'../modules/Gallery-2.15.1/js/jquery.blueimp-gallery.min.js',
		/*'../modules/Gallery-2.15.1/js/blueimp-gallery-fullscreen.js',
		'../modules/Gallery-2.15.1/js/blueimp-gallery-indicator.js',
		'../modules/Gallery-2.15.1/js/blueimp-gallery-video.js',
		'../modules/Gallery-2.15.1/js/blueimp-gallery-vimeo.js',
		'../modules/Gallery-2.15.1/js/blueimp-gallery-youtube.js',*/
		//'../modules/bootstrap-modal/js/bootstrap-modalmanager.js',
		//'../modules/bootstrap-modal/js/bootstrap-modal.js',
		//'../modules/pikachoose/lib/jquery.jcarousel.min.js',
		//'../modules/pikachoose/lib/jquery.pikachoose.min.js',
		'../modules/leaflet-0.7.5/leaflet.js',
    	'../modules/Leaflet.Snap/Leaflet.GeometryUtil/leaflet.geometryutil.js',
    	'../modules/Leaflet.Snap/leaflet.snap.js',
    '../modules/Leaflet.BetterWMS.js',
		'../modules/Leaflet.EasyButton/src/easy-button.js',
		//'../modules/leaflet-hash/leaflet-hash.js',
    	'../modules//Leaflet.Coordinates/dist/Leaflet.Coordinates-0.1.4.min.js',
    	'../modules/Leaflet.label/dist/leaflet.label.js',
		'../modules/leaflet-locatecontrol/src/L.Control.Locate.js',
		'../modules/leaflet-sidebar/L.Control.Sidebar.js',
		'../modules/Leaflet.draw/dist/leaflet.draw.js',
		'../modules/Leaflet.FileLayer/leaflet.filelayer.js',
		'../modules/Leaflet.FileLayer/togeojson/togeojson.js',
		'../modules/leaflet.bouncemarker/bouncemarker.js',
		'../modules/Leaflet.defaultextent/dist/leaflet.defaultextent.js',
		'../modules/leaflet.bing/Bing.js',
		'../modules/Leaflet.TextPath-gh-pages/leaflet.textpath.js',
		'../modules/jQuery-File-Upload-9.5.7/js/jquery.iframe-transport.js',
		'../modules/jQuery-File-Upload-9.5.7/js/jquery.fileupload.js',
		'../modules/jQuery-File-Upload-9.5.7/js/jquery.fileupload-process.js',
		'../modules/jQuery-File-Upload-9.5.7/js/jquery.fileupload-image.js',
		'../modules/jQuery-File-Upload-9.5.7/js/jquery.fileupload-audio.js',
		'../modules/jQuery-File-Upload-9.5.7/js/jquery.fileupload-video.js',
		'../modules/jQuery-File-Upload-9.5.7/js/jquery.fileupload-validate.js',
		//'../modules/jQuery-File-Upload-9.5.7/js/jquery.fileupload-ui.js',
		'../modules/jquery.fileDownload.js',
		//'../modules/croppic/croppic.min.js',
		'../modules/jquery.blImageCenter.js',
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
    	'../modules/noty-master/js/noty/layouts/center.js',
		'../modules/noty-master/js/noty/themes/default.js',
		'../modules/DataTables-1.9.4/media/js/jquery.dataTables.min.js',
		'../modules/jquery-timepicker/jquery.timepicker.min.js',
		//'../modules/bootbox.min.js',
		/*'../modules/jQuery-File-Upload/js/jquery.fileupload-process.js',
		'../modules/jQuery-File-Upload/js/jquery.fileupload-image.js',
		'../modules/jQuery-File-Upload/js/jquery.fileupload-audio.js',
		'../modules/jQuery-File-Upload/js/jquery.fileupload-video.js',
		'../modules/jQuery-File-Upload/js/jquery.fileupload-validate.js',
		'../modules/jQuery-File-Upload/js/jquery.fileupload-ui.js',*/
		//'../modules/jQuery-File-Upload/js/main.js',
		'../modules/jquery_layout/jquery.layout-latest.min.js',
        //'../modules/jspdf.debug.js',
	//'../modules/leaflet-save-map/0.3.3/html2canvas.js',
   	'../modules/leaflet-save-map/0.5.0/html2canvas.min.js',
    '../modules/leaflet-save-map/0.5.0/html2canvas.svg.min.js',
    //'../modules/leaflet-save-map/0.5.0/jquery.plugin.html2canvas.js',
    //'../modules/leaflet-save-map/flashcanvas.min.js',
    
	//'../modules/leaflet-image/leaflet-image.js',
		'global.js',
		'i18n.js',
		'utils.js',
		'config.js',
		'filter.js',
    	'modals.js',
		'fileuploader3.js',
		'subforms.js',
		'anagrafica2.js',
		'multifields.js',
		'map.js',
    	'adminMap.js',
		'interactiveMap.js',
    	'ziploader.js',
		//'home.js',
		'main.js'
    ),
    'js_compiled' => array(
        '../modules/jquery-1.11.1.min.js',
    		'../modules/backbone-min.js',
        '../modules/jquery-ui-1.10.3/ui/minified/jquery-ui.min.js',
        '../modules/tinymce/tinymce.min.js',
        '../modules/JavaScript-Load-Image-1.11.1/js/load-image.min.js',
        '../modules/JavaScript-Canvas-to-Blob-master/js/canvas-to-blob.min.js',
        '../modules/bootstrap-3.2.0/js/bootstrap.min.js',
        '../modules/Gallery-2.15.1/js/blueimp-helper.js',
        '../modules/Gallery-2.15.1/js/jquery.blueimp-gallery.min.js',
        '../modules/leaflet-0.7.3/leaflet.js',
        '../modules/leaflet_plugins.min.js',
        '../modules/jquery_filedownload.min.js',
        '../modules/jquery.blImageCenter.js',
        '../modules/fullcalendar-1.6.1/fullcalendar/fullcalendar.min.js',
        '../modules/chosen_v1.0.0/chosen.jquery.min.js',
        '../modules/pixelmatrix-uniform-3e9cd85/jquery.uniform.min.js',
        '../modules/jWizard/jquery.jWizard.js',
        '../modules/Gritter-master/js/jquery.gritter.min.js',
        '../modules/noty.min.js',
        '../modules/DataTables-1.9.4/media/js/jquery.dataTables.min.js',
        '../modules/jquery-timepicker/jquery.timepicker.min.js',
         '../modules/jquery_layout/jquery.layout-latest.min.js',
        'turisticgis.min.js'
    ),
 );
