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

require_once 'HTML/QuickForm/Controller.php';
require_once 'HTML/QuickForm/Action/Submit.php';
require_once 'HTML/QuickForm/Action/Jump.php';
require_once 'HTML/QuickForm/Action/Display.php';
require_once 'HTML/QuickForm/Action/Direct.php';
require_once 'HTML/Progress2.php';
require_once 'HTML/Progress2/Generator/pages.php';

/**
 * The HTML_Progress2_Generator class provides an easy way to build
 * progress bar, show a preview, and save php/css code for a later reuse.
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

class HTML_Progress2_Generator extends HTML_QuickForm_Controller
{
    /**
     * The progress object renders into this generator.
     *
     * @var        object
     * @since      2.0.0
     * @access     private
     */
    var $_progress;

    /**
     * All default wizard pages definition
     *
     * @var        array
     * @since      2.1.0
     * @access     private
     */
    var $_tabs = array(
        array('@' => array(
                  'class' => 'Property1',
                  'id' => 'page1',
                  'name' => 'Progress')
            ),
        array('@' => array(
                  'class' => 'Property2',
                  'id' => 'page2',
                  'name' => 'Cell')
            ),
        array('@' => array(
                  'class' => 'Property3',
                  'id' => 'page3',
                  'name' => 'Border')
            ),
        array('@' => array(
                  'class' => 'Property4',
                  'id' => 'page4',
                  'name' => 'String')
            ),
        array('@' => array(
                  'class' => 'Preview',
                  'id' => 'page5',
                  'name' => 'Preview')
            ),
        array('@' => array(
                  'class' => 'Save',
                  'id' => 'page6',
                  'name' => 'Save')
            )
        );

    /**
     * All default wizard controller actions definition
     *
     * @var        array
     * @since      2.1.0
     * @access     private
     */
    var $_act = array(
        'preview' => 'ActionPreview',
        'display' => 'ActionDisplay',
        'process' => 'ActionProcess',
        'dump'    => false
    );

    /**
     * Constructor (ZE1)
     *
     * @param string $controllerName (optional) Name of generator wizard (QuickForm)
     * @param array  $attributes     (optional) List of renderer options
     * @param array  $errorPrefs     (optional) Hash of parameters
     *                                          to configure error handler
     *
     * @since      version 2.0.0 (2005-10-01)
     * @access     public
     */
    function HTML_Progress2_Generator($controllerName = 'ProgressGenerator',
                                      $attributes = array(),
                                      $errorPrefs = array())
    {
        $this->__construct($controllerName, $attributes, $errorPrefs);
    }

    /**
     * Constructor (ZE2) Summary
     *
     * o Creates a standard progress bar generator wizard.
     *   <code>
     *   $generator = new HTML_Progress2_Generator();
     *   </code>
     *
     * o Creates a progress bar generator wizard with
     *   customized actions: progress bar preview, form rendering, buttons manager
     *   <code>
     *   $controllerName = 'myPrivateGenerator';
     *   $attributes = array(
     *        'preview' => name of a HTML_QuickForm_Action instance
     *                     (default 'ActionPreview',
     *                      see 'HTML/Progress2/Generator/Preview.php')
     *        'display' => name of a HTML_QuickForm_Action_Display instance
     *                     (default 'ActionDisplay',
     *                      see 'HTML/Progress2/Generator/Default.php')
     *        'process' => name of a HTML_QuickForm_Action instance
     *                     (default 'ActionProcess',
     *                      see 'HTML/Progress2/Generator/Process.php')
     *   );
     *   $generator = new HTML_Progress2_Generator($controllerName, $attributes);
     *   </code>
     *
     * @param string $controllerName (optional) Name of generator wizard (QuickForm)
     * @param array  $attributes     (optional) List of renderer options
     * @param array  $errorPrefs     (optional) Hash of parameters
     *                                          to configure error handler
     *
     * @since      version 2.0.0 (2005-10-01)
     * @access     protected
     * @throws     HTML_PROGRESS2_ERROR_INVALID_INPUT
     */
    function __construct($controllerName = 'ProgressGenerator',
                         $attributes = array(),
                         $errorPrefs = array())
    {
        $this->_progress = new HTML_Progress2($errorPrefs,
                                              HTML_PROGRESS2_BAR_HORIZONTAL,
                                              0, 100);

        if (!is_string($controllerName)) {
            return $this->_progress->raiseError(HTML_PROGRESS2_ERROR_INVALID_INPUT,
                'exception',
                array('var' => '$controllerName',
                      'was' => gettype($controllerName),
                      'expected' => 'string',
                      'paramnum' => 1));

        } elseif (!is_array($attributes)) {
            return $this->_progress->raiseError(HTML_PROGRESS2_ERROR_INVALID_INPUT,
                'exception',
                array('var' => '$attributes',
                      'was' => gettype($attributes),
                      'expected' => 'array',
                      'paramnum' => 2));
        }

        // build a new modal controller
        parent::HTML_QuickForm_Controller($controllerName, true);

        // add all wizard default pages
        $this->addPages();

        // add all wizard default actions
        $this->addActions($attributes);

        // set ProgressBar default values on first run
        $sess =& $this->container();

        if (count($sess['defaults']) == 0) {
            $this->setDefaults(array(
                'shape'         => HTML_PROGRESS2_BAR_HORIZONTAL,
                'way'           => 'natural',
                'autosize'      => true,
                'progresssize'  => array('left' => 10, 'top' => 25,
                                         'position' => 'relative',
                                         'bgcolor' => '#FFFFFF'),
                'rAnimSpeed'    => 100,

                'borderpainted' => false,
                'borderclass'   => 'progressBorder%s',
                'borderstyle'   => array('style' => 'solid', 'width' => 0,
                                         'color' => '#000000'),

                'cellid'        => 'pcel%01s',
                'cellclass'     => 'cell%s',
                'cellvalue'     => array('min' => 0, 'max' => 100, 'inc' => 1),
                'cellsize'      => array('width' => 15, 'height' => 20,
                                         'spacing' => 2, 'count' => 10),
                'cellcolor'     => array('active' => '#006600',
                                         'inactive' => '#CCCCCC',
                                         'bgcolor' => '#FFFFFF'),
                'cellfont'      => array('family' => 'Courier, Verdana',
                                         'size' => 8, 'color' => '#000000'),

                'stringpainted' => false,
                'stringid'      => 'txt1',
                'stringclass'   => 'progressTextLabel%s',
                'stringvalue'   => 'something to display',
                'stringsize'    => array('left' => 5, 'top' => 5),
                'stringvalign'  => 'top',
                'stringalign'   => 'left',
                'stringfont'    => array('family' => 'Verdana, Tahoma, Arial',
                                         'size' => 11, 'color' => '#000000'),
                'stringweight'  => 'normal',

                'phpcss'        => array('P' => true)
            ));
        }
    }

    /**
     * Returns a unique instance of the given progress generator wizard.
     *
     * @param string $controllerName (optional) Name of generator wizard (QuickForm)
     * @param array  $attributes     (optional) List of renderer options
     * @param array  $errorPrefs     (optional) Hash of parameters
     *                                          to configure error handler
     *
     * @return     object    HTML_Progress2_Generator
     * @since      version 2.1.0 (2006-08-12)
     * @access     public
     */
    function &singleton($controllerName = 'ProgressGenerator',
                        $attributes = array(),
                        $errorPrefs = array())
    {
        static $generator;

        if (!isset($generator)) {
            $generator =& new HTML_Progress2_Generator($controllerName,
                                                       $attributes,
                                                       $errorPrefs);
        }

        return $generator;
    }

    /**
     * Adds all pages of wizard at once
     *
     * @param mixed $pages Wizard pages definition array or null if used defaults
     *
     * @return void
     * @access public
     * @since  version 2.1.0 (2006-08-12)
     */
    function addPages($pages = null)
    {
        if (!isset($pages)) {
            // default wizard pages
            $pages = $this->_tabs;
        }

        foreach ($pages as $page) {
            $this->addPage($page);
        }
    }

    /**
     * Add a specific page to wizard or each page one by one
     *
     * @param array $page a single Wizard page definition
     *
     * @return void
     * @access public
     * @since  version 2.1.0 (2006-08-12)
     */
    function addPage($page)
    {
        $className = $page['@']['class'];
        $pageName  = $page['@']['name'];

        $qfcPage =& new $className($pageName);
        parent::addPage($qfcPage);

        // adds additional action
        foreach ($page as $action => $attr) {
            if ($action == '#' || $action == '@') {
                continue;
            }
            $qfcPage->addAction($action, new $attr['@']['class']);
        }

        // adds common action on each page
        $this->addAction($pageName, new HTML_QuickForm_Action_Direct());
    }

    /**
     * Adds common actions for the frontend wizard
     *
     * @param mixed $actions (optional) Identify for each action a class handler
     *
     * @return void
     * @access public
     * @since  version 2.1.0 (2006-08-12)
     */
    function addActions($actions = null)
    {
        if (isset($actions) && is_array($actions)) {
            $this->_act = array_merge($this->_act, $actions);
        }

        // adds preview action
        $ActionPreview = $this->_act['preview'];
        if (!class_exists($ActionPreview)) {
            include_once 'HTML/Progress2/Generator/Preview.php';
            $ActionPreview = 'ActionPreview';
        }
        $this->addAction('apply', new $ActionPreview());

        // adds display driver
        $ActionDisplay = $this->_act['display'];
        if (!class_exists($ActionDisplay)) {
            include_once 'HTML/Progress2/Generator/Default.php';
            $ActionDisplay = 'ActionDisplay';
        }
        $this->addAction('display', new $ActionDisplay());

        // adds basic actions (abort, commit, reset)
        $ActionProcess = $this->_act['process'];
        if (!class_exists($ActionProcess)) {
            include_once 'HTML/Progress2/Generator/Process.php';
            $ActionProcess = 'ActionProcess';
        }
        $this->addAction('cancel', new $ActionProcess());
        $this->addAction('process', new $ActionProcess());

        // adds dump class action (if necessary)
        $ActionDump = $this->_act['dump'];
        if ($ActionDump) {
            if (!class_exists($ActionDump)) {
                include_once 'HTML/Progress2/Generator/Dump.php';
                $ActionDump = 'ActionDump';
            }
            $this->addAction('dump', new $ActionDump());
        }
    }

    /**
     * Registers a handler for a specific action.
     *
     * @param string $actionName name of the action
     * @param object &$action    the handler for the action
     *
     * @return     void
     * @access     public
     * @since      version 2.1.0 (2006-08-12)
     */
    function addAction($actionName, &$action)
    {
        $this->_act[$actionName] = get_class($action);
        parent::addAction($actionName, $action);
    }

    /**
     * Returns whether or not a file is in the include path.
     *
     * @param string $file filename to test
     *
     * @access     public
     * @return     boolean   true if the file is in the include path.
     * @since      version 2.1.0 (2006-08-12)
     * @static
     */
    function isIncludeable($file)
    {
        foreach (explode(PATH_SEPARATOR, ini_get('include_path')) as $path) {
            if (file_exists($path . DIRECTORY_SEPARATOR . $file)
                && is_readable($path . DIRECTORY_SEPARATOR . $file)) {
                return true;
            }
        }
        return false;
    }

    /**
     * Creates a progress bar with options choosen on all wizard tabs.
     *
     * @return     object
     * @since      version 2.0.0 (2005-10-01)
     * @access     public
     */
    function createProgressBar()
    {
        $structure = $this->createProgressStructure();

        $this->_progress->setIdent('PB1');
        // indeterminate
        $this->_progress->setBorderPainted($structure['borderpainted']);

        $this->_progress->setAnimSpeed($structure['animspeed']);
        $this->_progress->setOrientation($structure['orientation']);
        $this->_progress->setFillWay($structure['fillway']);
        $this->_progress->setCellAttributes($structure['cell']);
        $this->_progress->setCellCount($structure['cellcount']);
        $this->_progress->setBorderAttributes($structure['border']);
        // string
        if ($structure['stringpainted']) {
            $labelID = $structure['string']['name'];
            unset($structure['string']['name']);
            $this->_progress->addLabel(HTML_PROGRESS2_LABEL_TEXT, $labelID);
            $this->_progress->setLabelAttributes($labelID, $structure['string']);
        }
        $this->_progress->setProgressAttributes($structure['progress']);
        // script
        $this->_progress->setMinimum($structure['minimum']);
        $this->_progress->setMaximum($structure['maximum']);
        $this->_progress->setIncrement($structure['increment']);

        return $this->_progress;
    }

    /**
     * Creates a progress bar structure
     * with options choosen on all wizard tabs.
     *
     * @return     array
     * @since      version 2.0.0 (2005-10-01)
     * @access     public
     * @see        HTML_Progress2::toArray()
     */
    function createProgressStructure()
    {
        $structure = array();
        $progress  = $this->exportValues();

        $structure['animspeed']     = intval($progress['rAnimSpeed']);
        $structure['borderpainted'] = ($progress['borderpainted'] == '1');
        $structure['stringpainted'] = ($progress['stringpainted'] == '1');

        /* Page 1: Progress attributes ****************************************/
        if (strlen(trim($progress['progresssize']['bgcolor'])) > 0) {
            $structure['progress']['background-color']
                = $progress['progresssize']['bgcolor'];
        }
        if (strlen(trim($progress['progresssize']['width'])) > 0) {
            $structure['progress']['width'] = $progress['progresssize']['width'];
        }
        if (strlen(trim($progress['progresssize']['height'])) > 0) {
            $structure['progress']['height'] = $progress['progresssize']['height'];
        }
        if (strlen(trim($progress['progresssize']['left'])) > 0) {
            $structure['progress']['left'] = $progress['progresssize']['left'];
        }
        if (strlen(trim($progress['progresssize']['top'])) > 0) {
            $structure['progress']['top'] = $progress['progresssize']['top'];
        }
        $structure['progress']['auto-size'] = ($progress['autosize'] == '1');
        $structure['orientation']
            = ($progress['shape'] == '1') ?
                  HTML_PROGRESS2_BAR_HORIZONTAL : HTML_PROGRESS2_BAR_VERTICAL;
        $structure['fillway'] = $progress['way'];

        /* Page 2: Cell attributes ********************************************/
        if (strlen(trim($progress['cellid'])) > 0) {
            $structure['cell']['id'] = $progress['cellid'];
        }
        if (strlen(trim($progress['cellclass'])) > 0) {
            $structure['cell']['class'] = $progress['cellclass'];
        }
        if (strlen(trim($progress['cellvalue']['min'])) > 0) {
            $structure['minimum'] = intval($progress['cellvalue']['min']);
        }
        if (strlen(trim($progress['cellvalue']['max'])) > 0) {
            $structure['maximum'] = intval($progress['cellvalue']['max']);
        }
        if (strlen(trim($progress['cellvalue']['inc'])) > 0) {
            $structure['increment'] = intval($progress['cellvalue']['inc']);
        }
        if (strlen(trim($progress['cellsize']['width'])) > 0) {
            $structure['cell']['width'] = $progress['cellsize']['width'];
        }
        if (strlen(trim($progress['cellsize']['height'])) > 0) {
            $structure['cell']['height'] = $progress['cellsize']['height'];
        }
        if (strlen(trim($progress['cellsize']['spacing'])) > 0) {
            $structure['cell']['spacing'] = $progress['cellsize']['spacing'];
        }
        if (strlen(trim($progress['cellsize']['count'])) > 0) {
            $structure['cellcount'] = intval($progress['cellsize']['count']);
        }
        if (strlen(trim($progress['cellcolor']['active'])) > 0) {
            $structure['cell']['active-color'] = $progress['cellcolor']['active'];
        }
        if (strlen(trim($progress['cellcolor']['inactive'])) > 0) {
            $structure['cell']['inactive-color']
                = $progress['cellcolor']['inactive'];
        }
        if (strlen(trim($progress['cellcolor']['bgcolor'])) > 0) {
            $structure['cell']['background-color']
                = $progress['cellcolor']['bgcolor'];
        }
        if (strlen(trim($progress['cellfont']['family'])) > 0) {
            $structure['cell']['font-family'] = $progress['cellfont']['family'];
        }
        if (strlen(trim($progress['cellfont']['size'])) > 0) {
            $structure['cell']['font-size'] = $progress['cellfont']['size'];
        }
        if (strlen(trim($progress['cellfont']['color'])) > 0) {
            $structure['cell']['color'] = $progress['cellfont']['color'];
        }

        /* Page 3: Border attributes ******************************************/
        if (strlen(trim($progress['borderclass'])) > 0) {
            $structure['border']['class'] = $progress['borderclass'];
        }
        if (strlen(trim($progress['borderstyle']['width'])) > 0) {
            $structure['border']['width'] = $progress['borderstyle']['width'];
        }
        if (strlen(trim($progress['borderstyle']['style'])) > 0) {
            $structure['border']['style'] = $progress['borderstyle']['style'];
        }
        if (strlen(trim($progress['borderstyle']['color'])) > 0) {
            $structure['border']['color'] = $progress['borderstyle']['color'];
        }

        /* Page 4: Label attributes *******************************************/
        if (strlen(trim($progress['stringid'])) > 0) {
            $structure['string']['name'] = $progress['stringid'];
        }
        if (strlen(trim($progress['stringclass'])) > 0) {
            $structure['string']['class'] = $progress['stringclass'];
        }
        if (strlen(trim($progress['stringvalue'])) > 0) {
            $structure['string']['value'] = $progress['stringvalue'];
        }
        if (strlen(trim($progress['stringsize']['width'])) > 0) {
            $structure['string']['width'] = $progress['stringsize']['width'];
        }
        if (strlen(trim($progress['stringsize']['height'])) > 0) {
            $structure['string']['height'] = $progress['stringsize']['height'];
        }
        if (strlen(trim($progress['stringsize']['left'])) > 0) {
            $structure['string']['left'] = $progress['stringsize']['left'];
        }
        if (strlen(trim($progress['stringsize']['top'])) > 0) {
            $structure['string']['top'] = $progress['stringsize']['top'];
        }
        if (strlen(trim($progress['stringsize']['bgcolor'])) > 0) {
            $structure['string']['background-color']
                = $progress['stringsize']['bgcolor'];
        }
        if (strlen(trim($progress['stringalign'])) > 0) {
            $structure['string']['align'] = $progress['stringalign'];
        }
        if (strlen(trim($progress['stringvalign'])) > 0) {
            $structure['string']['valign'] = $progress['stringvalign'];
        }
        if (strlen(trim($progress['stringfont']['family'])) > 0) {
            $structure['string']['font-family'] = $progress['stringfont']['family'];
        }
        if (strlen(trim($progress['stringfont']['size'])) > 0) {
            $structure['string']['font-size'] = $progress['stringfont']['size'];
        }
        if (strlen(trim($progress['stringfont']['color'])) > 0) {
            $structure['string']['color'] = $progress['stringfont']['color'];
        }
        $structure['string']['font-weight'] = $progress['stringweight'];

        return $structure;
    }
}
?>