// main.js
$(document).ready(function(){ 
    APP.config.init();
    // per login si centra il logine nella pagina
    var hhtml = $('html').height();
    var login = $('#login');

    if(login.length >0)
        login.css('margin-top',(hhtml-login.height())/2);
});