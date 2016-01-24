/**
 * Created by ionut on 06.08.2015.
 */
function loadScript(script){

    var xscript = Document.createElement('script');
    xscript.src = script + '.js';

    var body = Document.getElementsByTagName('body');

    body.appendChild(xscript);
}