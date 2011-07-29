<?php
/**
 * Copyright (c) 2005-2008, Laurent Laville <pear@laurent-laville.org>
 *
 * All rights reserved.
 *
 * Redistribution and use in source and binary forms, with or without
 * modification, are permitted provided that the following conditions
 * are met:
 *
 *     * Redistributions of source code must retain the above copyright
 *       notice, this list of conditions and the following disclaimer.
 *     * Redistributions in binary form must reproduce the above copyright
 *       notice, this list of conditions and the following disclaimer in the
 *       documentation and/or other materials provided with the distribution.
 *     * Neither the name of the authors nor the names of its contributors
 *       may be used to endorse or promote products derived from this software
 *       without specific prior written permission.
 *
 * THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS "AS IS"
 * AND ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT LIMITED TO, THE
 * IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS FOR A PARTICULAR PURPOSE
 * ARE DISCLAIMED. IN NO EVENT SHALL THE COPYRIGHT OWNER OR CONTRIBUTORS
 * BE LIABLE FOR ANY DIRECT, INDIRECT, INCIDENTAL, SPECIAL, EXEMPLARY, OR
 * CONSEQUENTIAL DAMAGES (INCLUDING, BUT NOT LIMITED TO, PROCUREMENT OF
 * SUBSTITUTE GOODS OR SERVICES; LOSS OF USE, DATA, OR PROFITS; OR BUSINESS
 * INTERRUPTION) HOWEVER CAUSED AND ON ANY THEORY OF LIABILITY, WHETHER IN
 * CONTRACT, STRICT LIABILITY, OR TORT (INCLUDING NEGLIGENCE OR OTHERWISE)
 * ARISING IN ANY WAY OUT OF THE USE OF THIS SOFTWARE, EVEN IF ADVISED OF THE
 * POSSIBILITY OF SUCH DAMAGE.
 *
 * PHP versions 4 and 5
 *
 * @category  HTML
 * @package   HTML_Progress2
 * @author    Laurent Laville <pear@laurent-laville.org>
 * @copyright 2005-2008 Laurent Laville
 * @license   http://www.opensource.org/licenses/bsd-license.php  New BSD License
 * @version   CVS: $Id$
 * @link      http://pear.php.net/package/HTML_Progress2
 * @since     File available since Release 2.0.0RC1
 */


/**
 * The ActionProcess class provides final step of ProgressBar creation.
 * Manage php/css source-code save and cancel action.
 *
 * @category  HTML
 * @package   HTML_Progress2
 * @author    Laurent Laville <pear@laurent-laville.org>
 * @copyright 2005-2008 Laurent Laville
 * @license   http://www.opensource.org/licenses/bsd-license.php  New BSD License
 * @version   Release: 2.4.1
 * @link      http://pear.php.net/package/HTML_Progress2
 * @since     Class available since Release 2.0.0RC1
 */

class ActionProcess extends HTML_QuickForm_Action
{
    /**
     * Performs an action on a page of the controller (wizard)
     *
     * @param string &$page      current page displayed by the controller
     * @param string $actionName page action asked
     *
     * @return     void
     * @since      version 2.0.0 (2005-10-01)
     * @access     public
     */
    function perform(&$page, $actionName)
    {
        if ($actionName == 'cancel') {
            echo '<h1>Progress2 Generator Task was canceled</h1>';
            echo '<p>None (PHP/CSS) source codes are available.</p>';
        } else {
            // Checks whether the pages of the controller are valid
            $page->isFormBuilt() or $page->buildForm();
            $page->controller->isValid();

            // what kind of source code is requested
            $code = $page->exportValue('phpcss');
            $pb   = $page->controller->createProgressBar();

            $phpCode = (isset($code['P']) === true);
            $cssCode = (isset($code['C']) === true);

            if ($cssCode) {
                $strCSS = $this->sprintCSS($pb);
                $this->exportOutput($strCSS);
            }
            if ($phpCode) {
                $strPHP = $this->sprintPHP($pb, $cssCode);
                $this->exportOutput($strPHP);
            }

            // reset session data
            $page->controller->container(true);
        }
    }

    /**
     * Returns a formatted string of the progress meter stylesheet
     *
     * @param object  &$pBar progress meter object reference
     * @param boolean $raw   (optional) decides whether to put html tags or not
     *
     * @return     string
     * @since      version 2.0.0 (2005-10-01)
     * @access     public
     */
    function sprintCSS(&$pBar, $raw = false)
    {
        return $pBar->getStyle($raw);
    }

    /**
     * Returns a formatted string of the progress meter php code
     *
     * @param object  &$pBar   progress meter object reference
     * @param boolean $cssCode returns css source code
     * @param boolean $raw     (optional) decides whether to put php tags or not
     *
     * @return     string
     * @since      version 2.0.0 (2005-10-01)
     * @access     public
     */
    function sprintPHP(&$pBar, $cssCode, $raw = false)
    {
        $structure = $pBar->toArray();

        if ($raw) {
            $strPHP = PHP_EOL;
        } else {
            $strPHP = '<?php'
                    . PHP_EOL;
        }
        $strPHP .= 'require_once \'HTML/Progress2.php\';'
                . PHP_EOL . PHP_EOL;
        $strPHP .= '$pb = new HTML_Progress2();'
                . PHP_EOL;
        $strPHP .= '$pb->setIdent(\'PB1\');'
                . PHP_EOL;

        if ($pBar->isIndeterminate()) {
            $strPHP .= '$pb->setIndeterminate(true);'
                    . PHP_EOL;
        }
        if ($pBar->isBorderPainted()) {
            $strPHP .= '$pb->setBorderPainted(true);'
                    . PHP_EOL;
        }
        if ($structure['animspeed'] > 0) {
            $strPHP .= '$pb->setAnimSpeed(' . $structure['animspeed'] . ');'
                    . PHP_EOL;
        }
        if ($structure['minimum'] != 0) {
            $strPHP .= '$pb->setMinimum(' . $structure['minimum'] . ');'
                    . PHP_EOL;
        }
        if ($structure['maximum'] != 100) {
            $strPHP .= '$pb->setMaximum(' . $structure['maximum'] . ');'
                    . PHP_EOL;
        }
        if ($structure['increment'] != 1) {
            $strPHP .= '$pb->setIncrement(' . $structure['increment'] . ');'
                    . PHP_EOL;
        }
        if ($structure['orientation'] == '2') {
            $strPHP .= '$pb->setOrientation(HTML_PROGRESS2_BAR_VERTICAL);'
                    . PHP_EOL;
        }
        if ($structure['fillway'] != 'natural') {
            $strPHP .= '$pb->setFillWay(\'' . $structure['fillway'] . '\');'
                    . PHP_EOL;
        }

        /* Page 1: Progress attributes ****************************************/
        $strPHP .= $this->_attributesArray('$pb->setProgressAttributes(',
                                           $structure['progress']);
        $strPHP .= PHP_EOL;

        /* Page 2: Cell attributes ********************************************/
        $strPHP .= '$pb->setCellCount(' . $structure['cellcount'] . ');'
                . PHP_EOL;
        $strPHP .= $this->_attributesArray('$pb->setCellAttributes(',
                                           $structure['cell']);
        $strPHP .= PHP_EOL;

        /* Page 3: Border attributes ******************************************/
        $strPHP .= $this->_attributesArray('$pb->setBorderAttributes(',
                                           $structure['border']);
        $strPHP .= PHP_EOL;

        /* Page 4: Label attributes *******************************************/
        foreach ($structure['label'] as $name => $data) {
            if ($data['type'] == HTML_PROGRESS2_LABEL_TEXT) {
                $strPHP .= '$pb->addLabel(HTML_PROGRESS2_LABEL_TEXT, \''
                        . $name .'\');';
                $strPHP .= PHP_EOL;
            }
            unset($data['type']);
            $strPHP .= $this->_attributesArray('$pb->setLabelAttributes(\''
                    . $name . '\', ', $data);
            $strPHP .= PHP_EOL;
        }

        $strPHP .= PHP_EOL;
        $strPHP .= '// code below is only for run demo';
        $strPHP .= PHP_EOL;
        $strPHP .= '// its not nececessary to create progress bar';
        $strPHP .= PHP_EOL;
        if (!$cssCode) {
            $strPHP .= 'echo \'<head>\' . PHP_EOL;' . PHP_EOL;
            $strPHP .= 'echo $pb->getStyle(false) . PHP_EOL;' . PHP_EOL;
        }
        $strPHP .= 'echo $pb->getScript(false) . PHP_EOL;' . PHP_EOL;
        if (!$cssCode) {
            $strPHP .= 'echo \'</head>\' . PHP_EOL;' . PHP_EOL;
            $strPHP .= 'echo \'<body>\' . PHP_EOL;' . PHP_EOL;
        }
        $strPHP .= '$pb->display();' . PHP_EOL;
        $strPHP .= '$pb->run();' . PHP_EOL;
        if (!$cssCode) {
            $strPHP .= 'echo \'</body>\' . PHP_EOL;' . PHP_EOL;
        }
        if (!$raw) {
            $strPHP .= '?>';
        }
        return $strPHP;
    }

    /**
     * Prints a string to standard output, with http headers if necessary
     *
     * @param string $str     string to print
     * @param string $mime    (optional) mime description
     * @param string $charset (optional) charset to use
     *
     * @return     void
     * @since      version 2.0.0 (2005-10-01)
     * @access     public
     */
    function exportOutput($str, $mime = 'text/plain', $charset = 'iso-8859-1')
    {
        if (!headers_sent()) {
            header("Expires: Tue, 1 Jan 1980 12:00:00 GMT");
            header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
            header("Cache-Control: no-cache");
            header("Pragma: no-cache");
            header("Content-Type: $mime; charset=$charset");
        }
        print $str;
    }

    /**
     * Complete a php function arguments line with appropriate attributes
     *
     * @param string $str        php function to complete
     * @param array  $attributes function arguments list of values
     *
     * @return     string
     * @since      version 2.0.0 (2005-10-01)
     * @access     private
     */
    function _attributesArray($str, $attributes)
    {
        $strPHP = $str . 'array(';
        foreach ($attributes as $attr => $val) {
            if (is_integer($val)) {
                $strPHP .= "'$attr'=>$val, ";
            } elseif (is_bool($val)) {
                $strPHP .= "'$attr'=>" . ($val ? 'true' : 'false') . ', ';
            } else {
                $strPHP .= "'$attr'=>'$val', ";
            }
        }
        $strPHP  = ereg_replace(', $', '', $strPHP);
        $strPHP .= '));';
        return $strPHP;
    }
}
?>