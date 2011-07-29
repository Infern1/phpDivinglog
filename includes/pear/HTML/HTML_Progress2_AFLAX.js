/**
 * JavaScript functions to handle behaviors of an AFLAX upload progress meter
 *
 * @category   HTML
 * @package    HTML_Progress2
 * @author     Laurent Laville <pear@laurent-laville.org>
 * @copyright  2007-2008 Laurent Laville
 * @license    http://www.opensource.org/licenses/bsd-license.php  New BSD License
 * @version    CVS: $Id: HTML_Progress2_AFLAX.js,v 1.3 2008/03/16 09:30:58 farell Exp $
 * @since      File available since Release 2.3.0a3
 */

var HTML_Progress2_AFLAX_fileRef;
var HTML_Progress2_AFLAX_allTypes;
var HTML_Progress2_AFLAX_fileTypes = new Array();
var HTML_Progress2_AFLAX_eventCallback = new Array();

function HTML_Progress2_AFLAX_setupBrowse()
{
    HTML_Progress2_AFLAX_allTypes = new AFLAX.FlashObject(aflax, "Array");

    for (var e = 0, length = HTML_Progress2_AFLAX_fileTypes.length; e < length; e++) {
        var fileTypes = new AFLAX.FlashObject(aflax, "Object");
        fileTypes.exposeProperty("description", fileTypes);
        fileTypes.exposeProperty("extension", fileTypes);
        fileTypes.setDescription(HTML_Progress2_AFLAX_fileTypes[e][0]);
        fileTypes.setExtension(HTML_Progress2_AFLAX_fileTypes[e][1]);
        HTML_Progress2_AFLAX_allTypes.callFunction("push", fileTypes);
    }

    HTML_Progress2_AFLAX_fileRef = new AFLAX.FlashObject(aflax, "flash.net.FileReference");

    var event = new Array('onSelect', 'onProgress', 'onComplete', 'onHTTPError', 'onSecurityError', 'onIOError');
    for (var e = 0, length = event.length; e < length; e++) {
        if (HTML_Progress2_AFLAX_eventCallback != '') {
            aflax.attachEventListener(HTML_Progress2_AFLAX_fileRef, event[e], HTML_Progress2_AFLAX_eventCallback[e]);
        }
    }

    HTML_Progress2_AFLAX_disableButtons(false, true, true);
}

function HTML_Progress2_AFLAX_disableButtons(btn1Status, btn2Status, btn3Status)
{
    var btn1 = window.document.getElementById("plblbtn1" + HTML_Progress2.widgetId);
    if (btn1) {
        btn1.disabled = btn1Status;
    }
    var btn2 = window.document.getElementById("plblbtn2" + HTML_Progress2.widgetId);
    if (btn2) {
        btn2.disabled = btn2Status;
    }
    var btn3 = window.document.getElementById("plblbtn3" + HTML_Progress2.widgetId);
    if (btn3) {
        btn3.disabled = btn3Status;
    }
}

// Action when user click on Browse button of the progress meter
function HTML_Progress2_AFLAX_selectFile()
{
    HTML_Progress2_AFLAX_fileRef.callFunction("browse", HTML_Progress2_AFLAX_allTypes);
}

// Action when user click on Upload button of the progress meter
function HTML_Progress2_AFLAX_beginUpload()
{
    HTML_Progress2_AFLAX_fileRef.callFunction("upload", HTML_Progress2_AFLAX_uri);
    HTML_Progress2_AFLAX_disableButtons(true, true, false);
}

// Action when user click on Cancel button of the progress meter
function HTML_Progress2_AFLAX_cancelUpload()
{
    HTML_Progress2_AFLAX_fileRef.callFunction("cancel");
    HTML_Progress2_AFLAX_disableButtons(false, true, true);
}

// Event callback

// onSelect : Invoked when the user selects a file to upload from the file-browsing dialog box.
function HTML_Progress2_AFLAX_Select(data)
{
    if (document.getElementById('plblaflaxstatus' + HTML_Progress2.widgetId) != null) {
        var parts = data.split(/;/);
        var fileSize = parts[5].split(/=/)[1];
        if (fileSize > (1024 * 1024)) {
            fileSize = Math.round(fileSize/(1024*1024)) + "MB";
        } else {
            fileSize = Math.round(fileSize/1024) + " KB";
        }
        var fileName = parts[7].split(/=/)[1];

        HTML_Progress2.setLabelText(HTML_Progress2.widgetId, 'aflaxstatus', "Ready to upload : " + fileName + " (" + fileSize + ")");
    }
    HTML_Progress2_AFLAX_disableButtons(false, false, true);
}

// onProgress event : Invoked periodically during the file upload operation.
function HTML_Progress2_AFLAX_Progress(data)
{
    var parts = data.split("!!");
    var loaded = parseInt((parts[1].split(":"))[1]);
    var total = parseInt((parts[2].split(":"))[1]);
    var percentage = Math.round((loaded/total * 100 ));

    var cell = ((percentage - HTML_Progress2.minimum) * HTML_Progress2.cellCount)
               / (HTML_Progress2.maximum - HTML_Progress2.minimum);
    cell = Math.floor(cell);
    HTML_Progress2.refresh(HTML_Progress2.widgetId, cell, 0);
    HTML_Progress2.setLabelText(HTML_Progress2.widgetId, HTML_Progress2.widgetPercentLabel, percentage + '%');

    if (percentage == 100) {
        if (document.getElementById('plblaflaxstatus' + HTML_Progress2.widgetId) != null) {
            HTML_Progress2.setLabelText(HTML_Progress2.widgetId, 'aflaxstatus', "Upload completed");
        }
    }
}

// onComplete event : Invoked when the upload operation has successfully completed.
function HTML_Progress2_AFLAX_Complete(data)
{
    if (document.getElementById('plblaflaxstatus' + HTML_Progress2.widgetId) != null) {
        HTML_Progress2.setLabelText(HTML_Progress2.widgetId, 'aflaxstatus', "Document available");
    }
    HTML_Progress2_AFLAX_disableButtons(false, true, true);
}

// onHTTPError event : Invoked when an upload fails because of an HTTP error
if (typeof(HTML_Progress2_AFLAX_HTTPError) != 'function') {
    HTML_Progress2_AFLAX_HTTPError = function(data) {alert('HTTPError: ' + data);};
}
// onSecurityError event : Invoked when an upload fails because of a security error.
if (typeof(HTML_Progress2_AFLAX_SecurityError) != 'function') {
    HTML_Progress2_AFLAX_SecurityError = function(data) {alert('SecurityError: ' + data);};
}
// onIOError event : Invoked when an input/output error occurs.
if (typeof(HTML_Progress2_AFLAX_IOError) != 'function') {
    HTML_Progress2_AFLAX_IOError = function(data) {alert('IOError: ' + data);};
}