// main.js
$(document).ready(function(){ 
    APP.config.init();
    // per login si centra il logine nella pagina
    var hhtml = $('html').height();
    var login = $('#login');

    if(login.length >0)
        login.css('margin-top',(hhtml-login.height())/2);

    L.drawLocal = {
        draw: {
            toolbar: {
                actions: {
                    title: APP.i18n.translate('Cancel drawing'),
                    text: APP.i18n.translate('Cancel')
                },
                undo: {
                    title: APP.i18n.translate('Delete last point drawn'),
                    text: APP.i18n.translate('Delete last point')
                },
                buttons: {
                    polyline: APP.i18n.translate('Draw a polyline'),
                    polygon: APP.i18n.translate('Draw a polygon'),
                    rectangle: APP.i18n.translate('Draw a rectangle'),
                    circle: APP.i18n.translate('Draw a circle'),
                    marker: APP.i18n.translate('Draw a marker')
                }
            },
            handlers: {
                circle: {
                    tooltip: {
                        start: APP.i18n.translate('Click and drag to draw circle.')
                    }
                },
                marker: {
                    tooltip: {
                        start: APP.i18n.translate('Click map to place marker.')
                    }
                },
                polygon: {
                    tooltip: {
                        start: APP.i18n.translate('Click to start drawing shape.'),
                        cont: APP.i18n.translate('Click to continue drawing shape.'),
                        end: APP.i18n.translate('Click first point to close this shape.')
                    }
                },
                polyline: {
                    error: APP.i18n.translate('<strong>Error:</strong> shape edges cannot cross!'),
                    tooltip: {
                        start: APP.i18n.translate('Click to start drawing line.'),
                        cont: APP.i18n.translate('Click to continue drawing line.'),
                        end: APP.i18n.translate('Click last point to finish line.')
                    }
                },
                rectangle: {
                    tooltip: {
                        start: APP.i18n.translate('Click and drag to draw rectangle.')
                    }
                },
                simpleshape: {
                    tooltip: {
                        end: APP.i18n.translate('Release mouse to finish drawing.')
                    }
                }
            }
        },
        edit: {
            toolbar: {
                actions: {
                    save: {
                        title: APP.i18n.translate('Save changes.'),
                        text: APP.i18n.translate('Save')
                    },
                    cancel: {
                        title: APP.i18n.translate('Cancel editing, discards all changes.'),
                        text: APP.i18n.translate('Cancel')
                    }
                },
                buttons: {
                    edit: APP.i18n.translate('Edit layers.'),
                    editDisabled: APP.i18n.translate('No layers to edit.'),
                    remove: APP.i18n.translate('Delete layers.'),
                    removeDisabled: APP.i18n.translate('No layers to delete.')
                }
            },
            handlers: {
                edit: {
                    tooltip: {
                        text: APP.i18n.translate('Drag handles, or marker to edit feature.'),
                        subtext: APP.i18n.translate('Click cancel to undo changes.')
                    }
                },
                remove: {
                    tooltip: {
                        text: APP.i18n.translate('Click on a feature to remove')
                    }
                }
            }
        }
    };
});