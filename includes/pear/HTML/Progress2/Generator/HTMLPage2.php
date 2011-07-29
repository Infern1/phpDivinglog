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

require_once 'HTML/Page2.php';

/**
 * The ActionDisplay class provides a HTML_Page2 form rendering.
 *
 * @category  HTML
 * @package   HTML_Progress2
 * @author    Laurent Laville <pear@laurent-laville.org>
 * @copyright 2005-2008 Laurent Laville
 * @license   http://www.opensource.org/licenses/bsd-license.php  New BSD License
 * @version   Release: @package_version@
 * @link      http://pear.php.net/package/HTML_Progress2
 * @since     Class available since Release 2.0.0RC1
 */

class ActionDisplay extends HTML_QuickForm_Action_Display
{
    /**
     * Style sheet for the custom layout
     *
     * @var    string
     * @access public
     * @since  2.1.0
     */
    var $css;

    /**
     * class constructor
     *
     * @param string $css custom stylesheet to apply, or default if not set
     *
     * @access public
     * @since  version 2.1.0 (2006-08-12)
     */
    function ActionDisplay($css = null)
    {
        // when no user-styles defined, used the default values
        $this->setStyleSheet($css);
    }

    /**
     * Outputs the form.
     *
     * @param object &$page the page being processed
     *
     * @return void
     * @access public
     * @since  version 2.0.0RC1 (2005-06-23)
     */
    function _renderForm(&$page)
    {
        $tab = '  ';

        $p = new HTML_Page2(array(
                 'lineend'  => PHP_EOL,
                 'tab'      => $tab,
                 'doctype'  => 'XHTML 1.0 Strict',
                 'language' => 'en',
                 'cache'    => 'false'
        ));
        $p->disableXmlProlog();
        $p->setTitle('PEAR::HTML_Progress2 - Generator');
        $p->setMetaData('author', 'Laurent Laville');

        $formTemplate = "\n<form{attributes}>"
                      . "\n<table class=\"maintable\">"
                      . "\n<caption>HTML_Progress2 Generator</caption>"
                      . "\n{content}"
                      . "\n</table>"
                      . "\n</form>";

        $headerTemplate = "\n<tr>"
                        . "\n\t<th colspan=\"2\">"
                        . "\n\t\t{header}"
                        . "\n\t</th>"
                        . "\n</tr>";

        $elementTemplate = "\n<tr valign=\"top\">"
                         . "\n\t<td class=\"qfLabel\">&nbsp;"
                         . "<!-- BEGIN required -->"
                         . "<span class=\"required\">*</span>"
                         . "<!-- END required -->"
                         . "{label}</td>"
                         . "\n\t<td class=\"qfElement\">"
                         . "\n{element}"
                         . "<!-- BEGIN label_2 -->&nbsp;"
                         . "<span class=\"qfLabel2\">{label_2}</span>"
                         . "<!-- END label_2 -->"
                         . "\n\t</td>"
                         . "\n</tr>";

        $groupTemplate = "\n\t\t<table class=\"group\">"
                       . "\n\t\t<tr>"
                       . "\n\t\t\t{content}"
                       . "\n\t\t</tr>"
                       . "\n\t\t</table>";

        $groupElementTemplate = "<td>{element}"
                              . "<!-- BEGIN label --><br/>"
                              . "<span class=\"qfLabel\">{label}</span>"
                              . "<!-- END label -->"
                              . "</td>";

        $renderer =& $page->defaultRenderer();

        $renderer->setFormTemplate($formTemplate);
        $renderer->setHeaderTemplate($headerTemplate);
        $renderer->setElementTemplate($elementTemplate);
        $renderer->setGroupTemplate($groupTemplate, 'name');
        $renderer->setGroupElementTemplate($groupElementTemplate, 'name');

        $styles = $this->getStyleSheet();
        $js     = '';

        // on preview tab, add progress bar javascript and stylesheet
        if ($page->getAttribute('id') == 'Preview') {
            $pb = $page->controller->createProgressBar();
            $pb->setTab($tab);

            $styles .= $pb->getStyle();
            $js      = $pb->getScript();

            $p->addStyleDeclaration($styles);
            $p->addScriptDeclaration($js);

            $pbElement =& $page->getElement('progressBar');
            $pbElement->setText($pb->toHtml() . '<br /><br />');
        } else {
            $p->addStyleDeclaration($styles);
        }
        $page->accept($renderer);

        $p->addBodyContent($renderer->toHtml());
        $p->display();
    }

    /**
     * Returns the custom style sheet to use for layout
     *
     * @param bool $content (optional) Either return css filename or string contents
     *
     * @return string
     * @access public
     * @since  version 2.1.0 (2006-08-12)
     */
    function getStyleSheet($content = true)
    {
        if ($content) {
            $styles = file_get_contents($this->css);
        } else {
            $styles = $this->css;
        }
        return $styles;
    }

    /**
     * Set the custom style sheet to use your own styles
     *
     * @param string $css (optional) File to read user-defined styles from
     *
     * @return bool    true if custom styles, false if default styles applied
     * @access public
     * @since  version 2.1.0 (2006-08-12)
     */
    function setStyleSheet($css = null)
    {
        // default stylesheet is into package data directory
        if (!isset($css)) {
            $this->css = '@data_dir@' . DIRECTORY_SEPARATOR
                 . '@package_name@' . DIRECTORY_SEPARATOR
                 . 'htmlpage2.css';
        }

        $res = isset($css) && file_exists($css);
        if ($res) {
            $this->css = $css;
        }
        return $res;
    }
}
?>