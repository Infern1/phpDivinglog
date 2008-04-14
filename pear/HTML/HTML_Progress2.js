/**
 * JavaScript functions to handle behaviors of a progress meter
 *
 * @category   HTML
 * @package    HTML_Progress2
 * @author     Laurent Laville <pear@laurent-laville.org>
 * @copyright  2007 Laurent Laville
 * @license    http://www.opensource.org/licenses/bsd-license.php  New BSD License
 * @version    CVS: $Id: HTML_Progress2.js,v 1.3 2007/06/12 07:42:00 farell Exp $
 * @since      File available since Release 2.3.0a2
 */

var HTML_Progress2 = {

    widgetId: false,
    widgetPercentLabel: 'pct1',
    cellClass: 'cell%s',
    cellCount: 10,
    minimum: 0,
    maximum: 100,
    defaultInterval: 2000,
    defaultEncoding: 'JSON',
    serverClassName: false,
    serverMethodName: false,
    serverCallback: false,
    requestArgs: false,
    onComplete: 'HTML_Progress2.redirectUrl',
    _inProgress: false,
    _statusInterval: null,

    start: function(pIdent, pInterval)
    {
        HTML_Progress2.widgetId = pIdent;
        if (!pInterval) {
            pInterval = HTML_Progress2.defaultInterval;
        }
        HTML_Progress2._statusInterval = window.setInterval(HTML_Progress2.statusCheck, pInterval);
    },

    statusCheck: function()
    {
        if (HTML_Progress2._inProgress) {
            return;
        }
        HTML_Progress2._inProgress = true;
        HTML_AJAX.defaultEncoding = HTML_Progress2.defaultEncoding;
        if (HTML_Progress2.serverCallback) {
            HTML_AJAX.callPhpCallback(HTML_Progress2.serverCallback, HTML_Progress2.statusCheckCallback);
        } else {
            if (HTML_Progress2.requestArgs != false) {
                var args = new Array();
                var url = HTML_AJAX.defaultServerUrl + '?' + HTML_Progress2.requestArgs;
                HTML_AJAX.fullcall(url, HTML_AJAX.defaultEncoding, HTML_Progress2.serverClassName, HTML_Progress2.serverMethodName, HTML_Progress2.statusCheckCallback, args);
            } else {
                HTML_AJAX.call(HTML_Progress2.serverClassName, HTML_Progress2.serverMethodName, HTML_Progress2.statusCheckCallback);
            }
        }
    },

    statusCheckCallback: function(result)
    {
        HTML_Progress2._inProgress = false;

        var cell = ((result.percentage - HTML_Progress2.minimum) * HTML_Progress2.cellCount)
                   / (HTML_Progress2.maximum - HTML_Progress2.minimum);
        cell = Math.floor(cell);
        HTML_Progress2.refresh(HTML_Progress2.widgetId, cell, 0);
        if (document.getElementById('plbl' + HTML_Progress2.widgetPercentLabel + HTML_Progress2.widgetId) != null) {
            HTML_Progress2.setLabelText(HTML_Progress2.widgetId, HTML_Progress2.widgetPercentLabel, result.percentage + '%');
        }

        for (var k in result.labels) {
            if (document.getElementById('plbl' + k + HTML_Progress2.widgetId) != null) {
                HTML_Progress2.setLabelText(HTML_Progress2.widgetId, k, result.labels[k]);
            }
        }

        if (result.percentage == 100) {
            window.clearInterval(HTML_Progress2._statusInterval);
            if (HTML_Progress2.onComplete) {
                HTML_Progress2._statusInterval = window.setTimeout(eval(HTML_Progress2.onComplete), 500);
            }
        }
    },

    redirectUrl: function()
    {
        window.clearTimeout(HTML_Progress2._statusInterval);
        window.location.replace(window.location.href + '?reload=true');
    },

    /**
     * - refresh -
     *
     * Highlight the right cells depending of progress current value
     *
     * @param      string   pIdent         progress meter html identifier
     * @param      int      pValue         cell number to hightlight
     * @param      int      pDeterminate   tell if we are in indeterminate mode
     *
     * @return     void
     * @public
     * @since      2.0.0
     */
    refresh: function(pIdent, pValue, pDeterminate)
    {
        var name  = 'pbar' + pIdent;
        var pbar  = document.getElementById(name);
        var cells = pbar.getElementsByTagName('div');

        if (pValue == pDeterminate) {
            for (var i = 0, length = cells.length; i < length; i++) {
                HTML_Progress2.showCell(i, pIdent, 'I');
            }
        }
        if ((pDeterminate > 0) && (pValue > 0)) {
            var i = (pValue - 1) % cells.length;
            HTML_Progress2.showCell(i, pIdent, 'A');
        } else {
            for (var i = pValue - 1; i >= 0; i--) {
                HTML_Progress2.showCell(i, pIdent, 'A');
            }
        }
    },

    /**
     * - showCell -
     *
     * Decide to highlight a cell depending of its status (active, inactive)
     *
     * @param      int      pCell          cell position (0 to cell count - 1) to highlight
     * @param      string   pIdent         progress meter html identifier
     * @param      string   pVisibility    'A' if we highlight active cell, 'I' for inactive cell
     *
     * @return     void
     * @public
     * @since      2.0.0
     */
    showCell: function(pCell, pIdent, pVisibility)
    {
        var name = 'pcel' + pCell + pIdent;
        var cellElement = document.getElementById(name);
        if (HTML_Progress2.cellClass.lastIndexOf('%') < 0) {
            cellElement.className = 'cell' + pVisibility;
        } else {
            cellElement.className = 'cell' + pIdent + pVisibility;
        }
    },

    /**
     * - hideProgress -
     *
     * Remove from display a progress meter
     *
     * @param      string   pIdent         progress meter html identifier
     *
     * @return     void
     * @public
     * @since      2.0.0
     */
    hideProgress: function(pIdent)
    {
        var tfrm = document.getElementById(pIdent);
        tfrm.style.visibility = "hidden";
    },

    /**
     * - setLabelText -
     *
     * Display new text value of a label
     *
     * @param      string   pIdent         progress meter html identifier
     * @param      string   pName          label identifier
     * @param      string   pText          new value of label to display
     *
     * @return     void
     * @public
     * @since      2.0.0
     */
    setLabelText: function(pIdent, pName, pText)
    {
        var name = 'plbl' + pName + pIdent;
        document.getElementById(name).firstChild.nodeValue = pText;
    },

    /**
     * - setElementStyle -
     *
     * Handle highlight of a smooth progress bar (without cell)
     *
     * @param      string   pIdent    progress meter html identifier
     * @param      string   pStyles   CSS string to apply
     *
     * @return     void
     * @public
     * @since      2.0.0
     */
    setElementStyle: function(pIdent, pStyles)
    {
        var name = 'pbar' + pIdent;
        var styles = pStyles.split(';');
        styles.pop();
        for (var i = 0, length = styles.length; i < length; i++) {
            var s = styles[i].split(':');
            var c = 'document.getElementById(name).style.' + s[0] + '="' + s[1] + '"';
            eval(c);
        }
    },

    /**
     * - setRotaryCross -
     *
     * Handle special effect for CROSS label type
     *
     * @param      string   pIdent    progress meter html identifier
     * @param      string   pName     cross label identifier
     *
     * @return     void
     * @public
     * @since      2.0.0
     */
    setRotaryCross: function(pIdent, pName)
    {
        var name = 'plbl' + pName + pIdent;
        var cross = document.getElementById(name).firstChild.nodeValue;
        switch(cross) {
            case "--": cross = "\\\\"; break;
            case "\\\\": cross = "|"; break;
            case "|": cross = "/"; break;
            default: cross = "--"; break;
        }
        document.getElementById(name).firstChild.nodeValue = cross;
    }
}