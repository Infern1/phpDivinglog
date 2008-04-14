<?php
/**
 * Copyright (c) 2005-2007, Laurent Laville <pear@laurent-laville.org>
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
 * @category   HTML
 * @package    HTML_Progress2
 * @author     Laurent Laville <pear@laurent-laville.org>
 * @copyright  2005-2007 Laurent Laville
 * @license    http://www.opensource.org/licenses/bsd-license.php  New BSD License
 * @version    CVS: $Id: Progress2.php,v 1.47 2007/04/08 15:18:17 farell Exp $
 * @link       http://pear.php.net/package/HTML_Progress2
 * @since      File available since Release 2.0.0RC1
 */

require_once 'HTML/Common.php';
require_once 'HTML/CSS.php';
require_once 'Event/Dispatcher.php';

if (version_compare(phpversion(), '5.0.0', '<')) {
    include_once 'PHP/Compat.php';
    PHP_Compat::loadFunction('ob_get_clean');
    PHP_Compat::loadFunction('file_put_contents');
    PHP_Compat::loadConstant('PHP_EOL');
}

/**#@+
 * Progress Bar shape types
 *
 * @var        integer
 * @since      2.0.0
 */
define ('HTML_PROGRESS2_BAR_HORIZONTAL', 1);
define ('HTML_PROGRESS2_BAR_VERTICAL',   2);
define ('HTML_PROGRESS2_POLYGONAL',      3);
define ('HTML_PROGRESS2_CIRCLE',         4);
/**#@-*/

/**#@+
 * Progress Bar label types
 *
 * @var        string
 * @since      2.0.0
 */
define ('HTML_PROGRESS2_LABEL_TEXT',     'text');
define ('HTML_PROGRESS2_LABEL_BUTTON',   'button');
define ('HTML_PROGRESS2_LABEL_STEP',     'step');
define ('HTML_PROGRESS2_LABEL_PERCENT',  'percent');
define ('HTML_PROGRESS2_LABEL_CROSSBAR', 'crossbar');
/**#@-*/

/**
 * Basic error code that indicate an unknown message
 *
 * @var        integer
 * @since      2.0.0
 */
define ('HTML_PROGRESS2_ERROR_UNKNOWN',            -1);

/**
 * Basic error code that indicate a wrong input
 *
 * @var        integer
 * @since      2.0.0
 */
define ('HTML_PROGRESS2_ERROR_INVALID_INPUT',    -100);

/**
 * Basic error code that indicate a wrong callback definition.
 * Allows only function or class-method structure.
 *
 * @var        integer
 * @since      2.0.0
 */
define ('HTML_PROGRESS2_ERROR_INVALID_CALLBACK', -101);

/**
 * Basic error code that indicate a deprecated method
 * that may be removed at any time from a future version
 *
 * @var        integer
 * @since      2.0.0
 */
define ('HTML_PROGRESS2_ERROR_DEPRECATED',       -102);

/**
 * Basic error code that indicate an invalid option.
 *
 * @var        integer
 * @since      2.0.0
 */
define ('HTML_PROGRESS2_ERROR_INVALID_OPTION',   -103);

/**
 * Basic error code that indicate an invalid resource
 *
 * @var        integer
 * @since      2.3.0a1
 */
define ('HTML_PROGRESS2_ERROR_INVALID_RESOURCE', -104);


/**
 * HTML loading bar with only PHP and JS interface.
 *
 * The HTML_Progress2 class allow you to add a loading bar
 * to any of your xhtml document.
 * You should have a browser that accept DHTML feature.
 *
 * Here is a basic example:
 * <code>
 * <?php
 * require_once 'HTML/Progress2.php';
 *
 * $pb = new HTML_Progress2();
 * $pb->setAnimSpeed(50);
 * ?>
 * <html>
 * <head>
 * <?php
 * echo $pb->getStyle(false);
 * echo $pb->getScript(false);
 * ?>
 * </head>
 * <body>
 * <?php
 * $pb->display();
 * $pb->run();
 * ?>
 * </body>
 * </html>
 * </code>
 *
 * @category   HTML
 * @package    HTML_Progress2
 * @author     Laurent Laville <pear@laurent-laville.org>
 * @copyright  2005-2007 Laurent Laville
 * @license    http://www.opensource.org/licenses/bsd-license.php  New BSD License
 * @version    Release: @package_version@
 * @link       http://pear.php.net/package/HTML_Progress2
 * @since      Class available since Release 2.0.0RC1
 */

class HTML_Progress2 extends HTML_Common
{
    /**
     * Status of the progress bar (new, show, hide).
     *
     * @var        string
     * @since      2.0.0
     * @access     private
     */
    var $_status = 'new';

    /**
     * Whether the progress bar is in determinate or indeterminate mode.
     * The default is false.
     * An indeterminate progress bar continuously displays animation indicating
     * that an operation of unknown length is occuring.
     *
     * @var        boolean
     * @since      2.0.0
     * @access     public
     * @see        setIndeterminate(), isIndeterminate()
     */
    var $indeterminate;

    /**
     * Whether to display a border around the progress bar.
     * The default is false.
     *
     * @var        boolean
     * @since      2.0.0
     * @access     private
     * @see        setBorderPainted(), isBorderPainted()
     */
    var $_paintBorder;

    /**
     * The label that uniquely identifies this progress object.
     *
     * @var        string
     * @since      2.0.0
     * @access     public
     * @see        getIdent(), setIdent()
     */
    var $ident;

    /**
     * Stores the event dispatcher which handles notifications
     *
     * @var        array
     * @since      2.0.0RC2
     * @access     protected
     */
    var $dispatcher;

    /**
     * Count the number of observer registered.
     * The Event_Dispatcher will be add on first observer registration, and
     * will be removed with the last observer.
     *
     * @var        integer
     * @since      2.0.0RC2
     * @access     private
     */
    var $_observerCount;

    /**
     * Delay in millisecond before each progress cells display.
     * 1000 ms === sleep(1)
     * <strong>usleep()</strong> function does not run on Windows platform.
     *
     * @var        integer
     * @since      2.0.0
     * @access     public
     * @see        setAnimSpeed()
     */
    var $animSpeed;

    /**
     * Callback, either function name or array(&$object, 'method')
     *
     * @var        mixed
     * @since      2.0.0
     * @access     private
     * @see        setProgressHandler()
     */
    var $_callback = false;

    /**
     * The progress bar's minimum value.
     * The default is 0.
     *
     * @var        integer
     * @since      2.0.0
     * @access     public
     * @see        getMinimum(), setMinimum()
     */
    var $minimum;

    /**
     * The progress bar's maximum value.
     * The default is 100.
     *
     * @var        integer
     * @since      2.0.0
     * @access     public
     * @see        getMaximum(), setMaximum()
     */
    var $maximum;

    /**
     * The progress bar's increment value.
     * The default is +1.
     *
     * @var        integer
     * @since      2.0.0
     * @access     public
     * @see        getIncrement(), setIncrement()
     */
    var $increment;

    /**
     * The progress bar's current value.
     *
     * @var        integer
     * @since      2.0.0
     * @access     public
     * @see        getValue(), setvalue(), incValue()
     */
    var $value;

    /**
     * Whether the progress bar is horizontal, vertical, polygonal or circle.
     * The default is horizontal.
     *
     * @var        integer
     * @since      2.0.0
     * @access     public
     * @see        getOrientation(), setOrientation()
     */
    var $orientation;

    /**
     * Whether the progress bar is filled in 'natural' or 'reverse' way.
     * The default fill way is 'natural'.
     *
     * <ul>
     * <li>'way'  =  bar fill way
     *   <ul>
     *     <li>with Progress Bar Horizontal,
     *              natural way is : left to right
     *        <br />reverse way is : right to left
     *     <li>with Progress Bar Vertical,
     *              natural way is : down to up
     *        <br />reverse way is : up to down
     *     <li>with Progress Circle or Polygonal,
     *              natural way is : clockwise
     *        <br />reverse way is : anticlockwise
     *   </ul>
     * </ul>
     *
     * @var        string
     * @since      2.0.0
     * @access     public
     * @see        getFillWay(), setFillWay()
     */
    var $fillWay;

    /**
     * The cell count of the progress bar. The default is 10.
     *
     * @var        integer
     * @since      2.0.0
     * @access     public
     * @see        getCellCount(), setCellCount()
     */
    var $cellCount;

    /**
     * The cell coordinates for a progress polygonal shape.
     *
     * @var        array
     * @since      2.0.0
     * @access     private
     * @see        getCellCoordinates(), setCellCoordinates()
     */
    var $_coordinates;

    /**
     * The width of grid in cell-size of the polygonal shape.
     *
     * @var        integer
     * @since      2.0.0
     * @access     private
     * @see        getCellCoordinates(), setCellCoordinates()
     */
    var $_xgrid;

    /**
     * The height of grid in cell-size of the polygonal shape.
     *
     * @var        integer
     * @since      2.0.0
     * @access     private
     * @see        getCellCoordinates(), setCellCoordinates()
     */
    var $_ygrid;

    /**
     * Progress bar core properties
     *
     * <code>
     * $progress = array(
     *    'background-color' => '#FFFFFF',      # bar background color
     *    'background-image' => 'none',         # bar background image
     *    'background-repeat' => 'no-repeat',   # bar background image repeat option
     *    'background-position' => 'top left',  # bar background image start position
     *    'width' => 0,                     # bar width
     *    'height' => 0,                    # bar height
     *    'left' => 10,                     # position from left
     *    'top' => 25,                      # position from top
     *    'position' => 'relative'          # position absolute or relative
     * );
     * </code>
     *
     * @var        array
     * @since      2.0.0
     * @access     private
     * @see        getProgressAttributes(), setProgressAttributes()
     */
    var $_progress = array();

    /**
     * Progress bar frame properties
     *
     * <code>
     * $frame = array(
     *    'show' => false,      # frame show (true/false)
     *    'left' => 200,        # position from left
     *    'top' => 100,         # position from top
     *    'width' => 320,       # width
     *    'height' => 90,       # height
     *    'color' => '#C0C0C0', # color
     *    'border-width' => 2,                                   # border width
     *    'border-style' => 'solid',                             # border style
     *                                                           # (solid, dashed, dotted ...)
     *    'border-color' => '#DFDFDF #404040 #404040 #DFDFDF'    # border color (3dfx)
     * );
     * </code>
     *
     * @var        array
     * @since      2.0.0
     * @access     public
     * @see        getFrameAttributes(), setFrameAttributes()
     */
    var $frame = array();

    /**
     * Progress bar border properties
     *
     * <code>
     * $border = array(
     *    'class' => 'progressBorder%s',    # css class selector
     *    'width' => 0,                     # width size in pixel
     *    'style' => 'solid',               # style (solid, dashed, dotted ...)
     *    'color' => '#000000'              # color
     * );
     * </code>
     *
     * @var        array
     * @since      2.0.0
     * @access     public
     * @see        getBorderAttributes(), setBorderAttributes()
     */
    var $border = array();

    /**
     * Progress bar cells properties
     *
     * <code>
     * $cell = array(
     *    'class' => 'cell%s',                   # css class selector
     *    'active-color' => '#006600',           # active color
     *    'inactive-color' => '#CCCCCC',         # inactive color
     *    'font-family' => 'Courier, Verdana',   # font family
     *    'font-size' => 8,                      # font size
     *    'color' => '#000000',                  # foreground color
     *    'background-color' => '#FFFFFF',       # background color
     *    'background-image' => 'none',          # cell background image
     *    'background-repeat' => 'no-repeat',    # cell background image repeat option
     *    'background-position' => 'top left',   # cell background image start position
     *    'width' => 15,                         # cell width
     *    'height' => 20,                        # cell height
     *    'spacing' => 2                         # cell spacing
     * );
     * </code>
     *
     * @var        array
     * @since      2.0.0
     * @access     public
     * @see        getCellAttributes(), setCellAttributes()
     */
    var $cell = array();

    /**
     * Progress bar labels properties
     *
     * <code>
     * $label = array(
     *    'name' => array(                  # label name
     *      'type' => 'text',               # label type
     *                                      # (text,button,step,percent,crossbar)
     *      'value' => '&nbsp;',            # label value
     *      'left' => ($left),              # label position from left
     *      'top' => ($top - 16),           # label position from top
     *      'width' => 0,                   # label width
     *      'height' => 0,                  # label height
     *      'align' => 'left',              # label align
     *      'background-color' => 'transparent',          # label background color
     *      'font-family' => 'Verdana, Tahoma, Arial',    # label font family
     *      'font-size' => 11,                            # label font size
     *      'font-weight' => 'normal',                    # label font weight
     *      'color' => '#000000',                         # label font color
     *      'class' => 'progressPercentLabel%s'           # css class selector
     * );
     * </code>
     *
     * @var        array
     * @since      2.0.0
     * @access     public
     * @see        getLabelAttributes(), setLabelAttributes()
     */
    var $label = array();

    /**
     * External Javascript file to override internal default code
     *
     * @var        string
     * @since      2.0.0
     * @access     public
     * @see        getScript(), setScript()
     */
    var $script;

    /**
     * Resources to handle AJAX progress meter:
     *
     * @var        array
     * @since      2.3.0a2
     * @access     public
     * @see        registerAJAX(), setupAJAX()
     */
    var $ajax = array();

    /**
     * Resources to handle AFLAX upload progress meter:
     *
     * @var        array
     * @since      2.3.0a3
     * @access     public
     * @see        registerAFLAX(), setupAFLAX()
     * @link       http://www.aflax.org
     *             AFLAX: The AJAX library for the Adobe Flash platform
     * @link       http://www.flash-db.com/Tutorials/upload/
     *             Upload file with Flash 8
     */
    var $aflax = array();

    /**
     * Error message callback.
     * This will be used to generate the error message
     * from the error code.
     *
     * @var        false|string|array
     * @since      2.0.0
     * @access     private
     * @see        _initErrorHandler()
     */
    var $_callback_message = false;

    /**
     * Error context callback.
     * This will be used to generate the error context for an error.
     *
     * @var        false|string|array
     * @since      2.0.0
     * @access     private
     * @see        _initErrorHandler()
     */
    var $_callback_context = false;

    /**
     * Error push callback.
     * The return value will be used to determine whether to allow
     * an error to be pushed or logged.
     *
     * @var        false|string|array
     * @since      2.0.0
     * @access     private
     * @see        _initErrorHandler()
     */
    var $_callback_push = false;

    /**
     * Error handler callback.
     * This will handle any errors raised by this package.
     *
     * @var        false|string|array
     * @since      2.0.0
     * @access     private
     * @see        _initErrorHandler()
     */
    var $_callback_errorhandler = false;

    /**
     * Associative array of key-value pairs
     * that are used to specify any handler-specific settings.
     *
     * @var        array
     * @since      2.0.0
     * @access     private
     * @see        _initErrorHandler()
     */
    var $_errorhandler_options = array();

    /**
     * Error stack for this package.
     *
     * @var        array
     * @since      2.0.0
     * @access     private
     * @see        raiseError()
     */
    var $_errorstack = array();


    /**
     * Constructor (ZE1)
     *
     * @since      2.0.0
     * @access     public
     */
    function HTML_Progress2($errorPrefs = array(),
                            $orient = HTML_PROGRESS2_BAR_HORIZONTAL, $min = 0, $max = 100,
                            $percentLabel = 'pct1')
    {
        $this->__construct($errorPrefs, $orient, $min, $max, $percentLabel);
    }

    /**
     * Constructor (ZE2) Summary
     *
     *   Creates a natural horizontal progress bar that displays ten separated cells
     *   with no border and only percent label.
     *   The initial and minimum values are 0, and the maximum is 100.
     *
     * @param      array     $errorPrefs    (optional) Hash of params to configure error handler
     * @param      int       $orient        (optional) Orientation of progress bar
     * @param      int       $min           (optional) Minimum value of progress bar
     * @param      int       $max           (optional) Maximum value of progress bar
     * @param      mixed     $percentLabel  (optional) Progress bar percent label id.
     *
     * @since      2.0.0
     * @access     protected
     * @throws     HTML_PROGRESS2_ERROR_INVALID_INPUT
     * @see        setIndeterminate(), setIdent(), setAnimSpeed(),
     *             setOrientation(), setMinimum(), setMaximum(), addLabel()
     */
    function __construct($errorPrefs = array(),
                         $orient = HTML_PROGRESS2_BAR_HORIZONTAL, $min = 0, $max = 100,
                         $percentLabel = 'pct1')
    {
        $this->_initErrorHandler($errorPrefs);

        $this->_observerCount = 0;

        $this->value = 0;
        $this->minimum = 0;
        $this->maximum = 100;
        $this->increment = +1;

        $this->cellCount = 10;
        $this->orientation = HTML_PROGRESS2_BAR_HORIZONTAL;
        $this->fillWay = 'natural';         // fill bar from left to right
        $this->script = null;               // uses internal javascript code

        $this->frame  = array('show' => false);
        $this->_progress = array(
            'background-color' => '#FFFFFF',
            'background-image' => 'none',
            'background-repeat' => 'no-repeat',
            'background-position' => 'top left',
            'width' => 0,
            'height' => 0,
            'left' => 10,
            'top' => 25,
            'position' => 'relative'
        );
        $this->border = array(
            'class' => 'progressBorder%s',
            'width' => 0,
            'style' => 'solid',
            'color' => '#000000'
        );
        $this->cell = array(
            'class' => 'cell%s',
            'active-color' => '#006600',
            'inactive-color' => '#CCCCCC',
            'font-family' => 'Courier, Verdana',
            'font-size' => 8,
            'color' => '#000000',
            'background-color' => '#FFFFFF',
            'background-image' => 'none',
            'background-repeat' => 'no-repeat',
            'background-position' => 'top left',
            'width' => 15,
            'height' => 20,
            'spacing' => 2
        );

        $this->_updateProgressSize();   // updates the new size of progress bar

        if (!is_int($orient)) {
            return $this->raiseError(HTML_PROGRESS2_ERROR_INVALID_INPUT, 'exception',
                array('var' => '$orient',
                      'was' => gettype($orient),
                      'expected' => 'integer',
                      'paramnum' => 2));

        } elseif (!is_int($min)) {
            return $this->raiseError(HTML_PROGRESS2_ERROR_INVALID_INPUT, 'exception',
                array('var' => '$min',
                      'was' => gettype($min),
                      'expected' => 'integer',
                      'paramnum' => 3));

        } elseif (!is_int($max)) {
            return $this->raiseError(HTML_PROGRESS2_ERROR_INVALID_INPUT, 'exception',
                array('var' => '$max',
                      'was' => gettype($max),
                      'expected' => 'integer',
                      'paramnum' => 4));

        } elseif (!is_string($percentLabel) && !is_bool($percentLabel)) {
            return $this->raiseError(HTML_PROGRESS2_ERROR_INVALID_INPUT, 'exception',
                array('var' => '$percentLabel',
                      'was' => gettype($percentLabel),
                      'expected' => 'string | boolean',
                      'paramnum' => 5));
        }

        $this->setOrientation($orient);
        $this->setMinimum($min);
        $this->setMaximum($max);

        if ($percentLabel) {
            $this->addLabel(HTML_PROGRESS2_LABEL_PERCENT, $percentLabel);
        }
        $this->setBorderPainted(false);
        $this->setIndeterminate(false);
        $this->setIdent();
        $this->setAnimSpeed(0);

        // to fix a potential php config problem with PHP 4.2.0 : turn 'implicit_flush' ON
        ob_implicit_flush(1);
    }

    /**
     * Returns the current API version.
     *
     * This function return a "PHP-standardized" version number string.
     * This is useful if you would like to write programs working only on some
     * versions of HTML_Progress2. See also php version_compare() function.
     *
     * @link       http://www.php.net/manual/en/function.version-compare.php
     * @return     string
     * @since      2.0.0
     * @access     public
     */
    function apiVersion()
    {
        return '@api_version@';
    }

    /**
     * Returns mode of the progress meter.
     *
     * There are two types of progress meters: determinate and undeterminate.
     *
     * Determinate progress meters are used when you know the length of time
     * that an operation will take. The progress meter will fill up and,
     * once full, the operation should be finished.
     *
     * Undeterminate progress meters are used when you do not know the length
     * of time of an operation. The progress meter will have an animation
     * such a sliding box.
     *
     * @return     boolean
     * @since      2.0.0
     * @access     public
     * @see        setIndeterminate()
     */
    function isIndeterminate()
    {
        return $this->indeterminate;
    }

    /**
     * Sets the mode of progress meter.
     *
     * Default progress meters are in determinate mode ($continuous = TRUE), but
     * can be switched anytime in indeterminate mode ($continuous = FALSE).
     *
     * @param      boolean   $continuous    whether countinuously displays animation
     *
     * @return     void
     * @since      2.0.0
     * @access     public
     * @throws     HTML_PROGRESS2_ERROR_INVALID_INPUT
     * @see        isIndeterminate()
     */
    function setIndeterminate($continuous)
    {
        if (!is_bool($continuous)) {
            return $this->raiseError(HTML_PROGRESS2_ERROR_INVALID_INPUT, 'exception',
                array('var' => '$continuous',
                      'was' => gettype($continuous),
                      'expected' => 'boolean',
                      'paramnum' => 1));
        }
        $this->indeterminate = $continuous;
    }

    /**
     * Determines whether the progress bar border is painted or not.
     *
     * By default, each progress bar (horizontal, vertical) has no border.
     * This function gave ability to know if you've already set a border
     * (return TRUE), or not (return FALSE).
     *
     * @return     boolean
     * @since      2.0.0
     * @access     public
     * @see        setBorderPainted()
     */
    function isBorderPainted()
    {
        return $this->_paintBorder;
    }

    /**
     * Decides to paint or not a border to the progress bar.
     *
     * This function gave ability to set ($paint = TRUE) or remove ($paint = FALSE)
     * a border to the current progress bar.
     *
     * @param      boolean   $paint         whether the progress bar should paint its border
     *
     * @return     void
     * @since      2.0.0
     * @access     public
     * @throws     HTML_PROGRESS2_ERROR_INVALID_INPUT
     * @see        isBorderPainted()
     */
    function setBorderPainted($paint)
    {
        if (!is_bool($paint)) {
            return $this->raiseError(HTML_PROGRESS2_ERROR_INVALID_INPUT, 'exception',
                array('var' => '$paint',
                      'was' => gettype($paint),
                      'expected' => 'boolean',
                      'paramnum' => 1));
        }

        $this->_paintBorder = $paint;
    }

    /**
     * Returns the progress meter minimum value.
     *
     * Get the minimum value at which the progress meter will start.
     * The default value is zero and can't be negative.
     *
     * @return     integer
     * @since      2.0.0
     * @access     public
     * @see        setMinimum()
     */
    function getMinimum()
    {
        return $this->minimum;
    }

    /**
     * Sets the progress meter minimum value.
     *
     * Set the minimum value at which the progress meter will start.
     * The default value is zero and can't be negative.
     * If the new minimum is different than previous value, all listeners
     * are notified.
     *
     * @param      integer   $min           progress meter's minimal value
     *
     * @return     void
     * @since      2.0.0
     * @access     public
     * @throws     HTML_PROGRESS2_ERROR_INVALID_INPUT
     * @see        getMinimum()
     */
    function setMinimum($min)
    {
        if (!is_int($min)) {
            return $this->raiseError(HTML_PROGRESS2_ERROR_INVALID_INPUT, 'exception',
                array('var' => '$min',
                      'was' => gettype($min),
                      'expected' => 'integer',
                      'paramnum' => 1));

        } elseif ($min < 0) {
            return $this->raiseError(HTML_PROGRESS2_ERROR_INVALID_INPUT, 'error',
                array('var' => '$min',
                      'was' => $min,
                      'expected' => 'positive',
                      'paramnum' => 1));

        } elseif ($min > $this->maximum) {
            return $this->raiseError(HTML_PROGRESS2_ERROR_INVALID_INPUT, 'error',
                array('var' => '$min',
                      'was' => $min,
                      'expected' => 'less than $max = '.$this->maximum,
                      'paramnum' => 1));
        }
        $oldVal = $this->minimum;
        $this->minimum = $min;

        /* set current value to minimum if less than minimum */
        if ($this->value < $min) {
            $this->setValue($min);
        }

        if ($oldVal != $min) {
            $this->_postNotification('onChange', array('handler' => __FUNCTION__, 'value' => $min));
        }
    }

    /**
     * Returns the progress meter maximum value.
     *
     * Get the maximum value at which the progress meter will stop.
     * The default value is 100 and can't be less than minimum.
     *
     * @return     integer
     * @since      2.0.0
     * @access     public
     * @see        setMaximum()
     */
    function getMaximum()
    {
        return $this->maximum;
    }

    /**
     * Sets the progress meter maximum value.
     *
     * Set the maximum value at which the progress meter will stop.
     * The default value is 100 and can't be less than minimum.
     * If the new maximum is different than previous value, all listeners
     * are notified.
     *
     * @param      integer   $max           progress meter's maximal value
     *
     * @return     void
     * @since      2.0.0
     * @access     public
     * @throws     HTML_PROGRESS2_ERROR_INVALID_INPUT
     * @see        getMaximum()
     */
    function setMaximum($max)
    {
        if (!is_int($max)) {
            return $this->raiseError(HTML_PROGRESS2_ERROR_INVALID_INPUT, 'exception',
                array('var' => '$max',
                      'was' => gettype($max),
                      'expected' => 'integer',
                      'paramnum' => 1));

        } elseif ($max < 0) {
            return $this->raiseError(HTML_PROGRESS2_ERROR_INVALID_INPUT, 'error',
                array('var' => '$max',
                      'was' => $max,
                      'expected' => 'positive',
                      'paramnum' => 1));

        } elseif ($max < $this->minimum) {
            return $this->raiseError(HTML_PROGRESS2_ERROR_INVALID_INPUT, 'error',
                array('var' => '$max',
                      'was' => $max,
                      'expected' => 'greater than $min = '.$this->minimum,
                      'paramnum' => 1));
        }
        $oldVal = $this->maximum;
        $this->maximum = $max;

        /* set current value to maximum if greater to maximum */
        if ($this->value > $max) {
            $this->setValue($max);
        }

        if ($oldVal != $max) {
            $this->_postNotification('onChange', array('handler' => __FUNCTION__, 'value' => $max));
        }
    }

    /**
     * Returns the progress meter increment value.
     *
     * Get step of progress property of a progress meter.
     * The default value is +1.
     *
     * @return     integer
     * @since      2.0.0
     * @access     public
     * @see        setIncrement()
     */
    function getIncrement()
    {
        return $this->increment;
    }

    /**
     * Sets the progress meter increment value.
     *
     * Defines step of progress property of a progress meter.
     * Allows to ajust default value (+1).
     *
     * @param      integer   $inc           progress meter's increment value
     *
     * @return     void
     * @since      2.0.0
     * @access     public
     * @throws     HTML_PROGRESS2_ERROR_INVALID_INPUT
     * @see        getIncrement()
     */
    function setIncrement($inc)
    {
        if (!is_int($inc)) {
            return $this->raiseError(HTML_PROGRESS2_ERROR_INVALID_INPUT, 'exception',
                array('var' => '$inc',
                      'was' => gettype($inc),
                      'expected' => 'integer',
                      'paramnum' => 1));

        } elseif ($inc == 0) {
            return $this->raiseError(HTML_PROGRESS2_ERROR_INVALID_INPUT, 'error',
                array('var' => '$inc',
                      'was' => $inc,
                      'expected' => 'not equal zero',
                      'paramnum' => 1));
        }
        $this->increment = $inc;
    }

    /**
     * Returns the progress meter current value.
     *
     * Get the current value of the progress meter that is always between
     * the minimum and maximum values, inclusive.
     * The default value is equal to minimum.
     *
     * @return     integer
     * @since      2.0.0
     * @access     public
     * @see        setValue(), incValue()
     */
    function getValue()
    {
        return $this->value;
    }

    /**
     * Sets the progress meter current value.
     *
     * Set the current value of the progress meter that is always between
     * the minimum and maximum values, inclusive. If the new value is different
     * from previous, then all listeners are notified.
     *
     * @param      integer   $val           progress meter's current value
     *
     * @return     void
     * @since      2.0.0
     * @access     public
     * @throws     HTML_PROGRESS2_ERROR_INVALID_INPUT
     * @see        getValue(), incValue()
     */
    function setValue($val)
    {
        if (!is_int($val)) {
            return $this->raiseError(HTML_PROGRESS2_ERROR_INVALID_INPUT, 'exception',
                array('var' => '$val',
                      'was' => gettype($val),
                      'expected' => 'integer',
                      'paramnum' => 1));

        } elseif ($val < $this->minimum) {
            return $this->raiseError(HTML_PROGRESS2_ERROR_INVALID_INPUT, 'error',
                array('var' => '$val',
                      'was' => $val,
                      'expected' => 'greater than $min = '.$this->minimum,
                      'paramnum' => 1));

        } elseif ($val > $this->maximum) {
            return $this->raiseError(HTML_PROGRESS2_ERROR_INVALID_INPUT, 'error',
                array('var' => '$val',
                      'was' => $val,
                      'expected' => 'less than $max = '.$this->maximum,
                      'paramnum' => 1));
        }
        $oldVal = $this->value;
        $this->value = $val;

        if ($oldVal != $val) {
            $this->_postNotification('onChange', array('handler' => __FUNCTION__, 'value' => $val));
        }
    }

    /**
     * Updates the internal progress meter current value.
     *
     * The new current value is equal to previous value increase by
     * increment value. All listeners are notified.
     * See also moveNext() method if you want a refresh display too.
     *
     * @return     void
     * @since      2.0.0
     * @access     public
     * @see        getValue(), setValue(), moveNext()
     */
    function incValue()
    {
        $newVal = $this->value + $this->increment;
        $newVal = min($this->maximum, $newVal);
        $this->value = $newVal;

        $this->_postNotification('onChange', array('handler' => __FUNCTION__, 'value' => $newVal));
    }

    /**
     * Changes new value of the progress meter by step increase.
     *
     * Move current value to the next step of the progress meter
     * defined by its increment. If result value is different than previous,
     * then all listeners are notified.
     * Caution: a step is always between 1 and (maximum / taskcount) rounded fractions up.
     *
     * @param      integer   $step          new step value
     *
     * @return     void
     * @since      2.0.0
     * @access     public
     * @throws     HTML_PROGRESS2_ERROR_INVALID_INPUT
     * @see        moveNext()
     */
    function moveStep($step)
    {
        if (!is_int($step)) {
            return $this->raiseError(HTML_PROGRESS2_ERROR_INVALID_INPUT, 'exception',
                array('var' => '$step',
                      'was' => gettype($step),
                      'expected' => 'integer',
                      'paramnum' => 1));

        } elseif ($step < 0) {
            return $this->raiseError(HTML_PROGRESS2_ERROR_INVALID_INPUT, 'error',
                array('var' => '$step',
                      'was' => $step,
                      'expected' => 'greater than zero',
                      'paramnum' => 1));

        } elseif ($step > intval(ceil($this->maximum / $this->increment))) {
            return $this->raiseError(HTML_PROGRESS2_ERROR_INVALID_INPUT, 'error',
                array('var' => '$step',
                      'was' => $step,
                      'expected' => 'less than '. intval(ceil($this->maximum / $this->increment)),
                      'paramnum' => 1));
        }
        $oldVal = $this->value;
        $this->value = $step * $this->increment;
        $this->value = min($this->maximum, $this->value);

        if ($oldVal != $this->value) {
            $this->_refreshDisplay($this->value);
            $this->_postNotification('onChange', array('handler' => __FUNCTION__, 'value' => $step));
        }
    }

    /**
     * Changes new value of the progress meter by increment increase.
     *
     * Move current value to the next value of the progress meter
     * defined by its increment. If result value is different than previous,
     * then all listeners are notified.
     *
     * @return     void
     * @since      2.0.0
     * @access     public
     * @see        moveStep()
     */
    function moveNext()
    {
        $oldVal = $this->value;
        $this->value  = $oldVal + $this->increment;
        $this->value = min($this->maximum, $this->value);

        if ($oldVal != $this->value) {
            $this->_refreshDisplay($this->value);
            $this->_postNotification('onChange', array('handler' => __FUNCTION__, 'value' => $this->value));
        }
    }

    /**
     * Returns the percent complete of the progress meter.
     *
     * Note that this number is between 0.00 and 1.00 when $float = true.
     * And this number is between 0 and 100 when $float = false.
     *
     * @param      boolean   $float         (optional) float or integer format
     *
     * @return     float
     * @since      2.0.0
     * @access     public
     * @throws     HTML_PROGRESS2_ERROR_INVALID_INPUT
     * @see        getValue(), getMaximum()
     */
    function getPercentComplete($float = true)
    {
        if (!is_bool($float)) {
            return $this->raiseError(HTML_PROGRESS2_ERROR_INVALID_INPUT, 'exception',
                array('var' => '$float',
                      'was' => gettype($float),
                      'expected' => 'boolean',
                      'paramnum' => 1));
        }

        $min = $this->minimum;
        $max = $this->maximum;
        $val = $this->value;

        $percent = round((($val - $min) / ($max - $min)), 4);

        if ($float) {
            return $percent;
        } else {
            return intval($percent * 100);
        }
    }

    /**
     * Returns orientation of the progress bar.
     *
     * There are only two distinct orientations for a progress bar:
     * horizontal and vertical, identified by two constants:
     * HTML_PROGRESS2_BAR_HORIZONTAL and HTML_PROGRESS2_BAR_VERTICAL.
     * While circle and other polygonal progress meter are identified by
     * two other constants: HTML_PROGRESS2_CIRCLE and HTML_PROGRESS2_POLYGONAL.
     * The default orientation is horizontal.
     *
     * @return     integer
     * @since      2.0.0
     * @access     public
     * @see        setOrientation()
     */
    function getOrientation()
    {
        return $this->orientation;
    }

    /**
     * Sets orientation of the progress bar.
     *
     * There are only two distinct orientations for a progress bar:
     * horizontal and vertical, identified by two constants:
     * HTML_PROGRESS2_BAR_HORIZONTAL and HTML_PROGRESS2_BAR_VERTICAL.
     * While circle and other polygonal progress meter are identified by
     * two other constants: HTML_PROGRESS2_CIRCLE and HTML_PROGRESS2_POLYGONAL.
     * The default orientation is horizontal.
     *
     * @param      integer   $orient        Orientation (horizontal or vertical)
     *
     * @return     void
     * @since      2.0.0
     * @access     public
     * @throws     HTML_PROGRESS2_ERROR_INVALID_INPUT
     * @see        getOrientation()
     */
    function setOrientation($orient)
    {
        if (!is_int($orient)) {
            return $this->raiseError(HTML_PROGRESS2_ERROR_INVALID_INPUT, 'exception',
                array('var' => '$orient',
                      'was' => gettype($orient),
                      'expected' => 'integer',
                      'paramnum' => 1));

        } elseif (($orient != HTML_PROGRESS2_BAR_HORIZONTAL) &&
                  ($orient != HTML_PROGRESS2_BAR_VERTICAL) &&
                  ($orient != HTML_PROGRESS2_POLYGONAL) &&
                  ($orient != HTML_PROGRESS2_CIRCLE)) {
            return $this->raiseError(HTML_PROGRESS2_ERROR_INVALID_INPUT, 'error',
                array('var' => '$orient',
                      'was' => $orient,
                      'expected' => HTML_PROGRESS2_BAR_HORIZONTAL.' | '.
                                    HTML_PROGRESS2_BAR_VERTICAL.' | '.
                                    HTML_PROGRESS2_POLYGONAL.' | '.
                                    HTML_PROGRESS2_CIRCLE,
                      'paramnum' => 1));
        }

        $previous = $this->orientation;    // gets previous orientation
        $this->orientation = $orient;      // sets the new orientation

        if ($previous != $orient) {
            // if orientation has changed, we need to swap cell width and height
            $w = $this->cell['width'];
            $h = $this->cell['height'];

            $this->cell['width']  = $h;
            $this->cell['height'] = $w;

            $this->_updateProgressSize();   // updates the new size of progress bar
        }
    }

    /**
     * Returns fill option of the progress meter.
     *
     * Get 'natural' or 'reverse', depending of the fill way of progress meter.
     * For horizontal progress bar, natural way is from left to right, and reverse
     * way is from right to left.
     * For vertical progress bar, natural way is from down to up, and reverse
     * way is from up to down.
     * The default fill way is 'natural'.
     *
     * @return     string
     * @since      2.0.0
     * @access     public
     * @see        setFillWay()
     */
    function getFillWay()
    {
        return $this->fillWay;
    }

    /**
     * Sets fill option of the progress meter.
     *
     * Sets the progress meter fill option: must be 'natural' or 'reverse'.
     * The default fill way is 'natural'.
     *
     * @param      string    $way           fill direction (natural or reverse)
     *
     * @return     void
     * @since      2.0.0
     * @access     public
     * @throws     HTML_PROGRESS2_ERROR_INVALID_INPUT
     * @see        getFillWay()
     */
    function setFillWay($way)
    {
        if (!is_string($way)) {
            return $this->raiseError(HTML_PROGRESS2_ERROR_INVALID_INPUT, 'exception',
                array('var' => '$way',
                      'was' => gettype($way),
                      'expected' => 'string',
                      'paramnum' => 1));

        } elseif (($way != 'natural') && ($way != 'reverse')) {
            return $this->raiseError(HTML_PROGRESS2_ERROR_INVALID_INPUT, 'error',
                array('var' => '$way',
                      'was' => $way,
                      'expected' => 'natural | reverse',
                      'paramnum' => 1));

        }
        $this->fillWay = $way;
    }

    /**
     * Returns count of cell in the progress meter.
     *
     * Get the number of cell defined to a progress meter. It must be positive.
     * The default value is 10.
     *
     * @return     integer
     * @since      2.0.0
     * @access     public
     * @see        setCellCount()
     */
    function getCellCount()
    {
        return $this->cellCount;
    }

    /**
     * Sets count of cell in the progress meter.
     *
     * Defines the number of cell to a progress meter. It must be positive.
     * The default value is 10.
     *
     * @param      integer   $cells         Cell count on progress meter
     *
     * @return     void
     * @since      2.0.0
     * @access     public
     * @throws     HTML_PROGRESS2_ERROR_INVALID_INPUT
     * @see        getCellCount()
     */
    function setCellCount($cells)
    {
        if (!is_int($cells)) {
            return $this->raiseError(HTML_PROGRESS2_ERROR_INVALID_INPUT, 'exception',
                array('var' => '$cells',
                      'was' => gettype($cells),
                      'expected' => 'integer',
                      'paramnum' => 1));

        } elseif ($cells < 0) {
            return $this->raiseError(HTML_PROGRESS2_ERROR_INVALID_INPUT, 'error',
                array('var' => '$cells',
                      'was' => $cells,
                      'expected' => 'greater or equal zero',
                      'paramnum' => 1));
        }
        $this->cellCount = $cells;

        $this->_updateProgressSize();   // updates the new size of progress bar
    }

    /**
     * Returns cell attributes values.
     *
     * Get all cell attributes in a associative array with key-value couple
     * (by default). Get the attributes as string is also possible.
     *
     * @param      bool      $asString      (optional) whether to return the attributes as string
     *
     * @return     mixed
     * @since      2.0.0
     * @access     public
     * @throws     HTML_PROGRESS2_ERROR_INVALID_INPUT
     * @see        setCellAttributes()
     */
    function getCellAttributes($asString = false)
    {
        if (!is_bool($asString)) {
            return $this->raiseError(HTML_PROGRESS2_ERROR_INVALID_INPUT, 'exception',
                array('var' => '$asString',
                      'was' => gettype($asString),
                      'expected' => 'boolean',
                      'paramnum' => 1));
        }

        $attr = $this->cell;

        if ($asString) {
            return $this->_getAttrString($attr);
        } else {
            return $attr;
        }
    }

    /**
     * Sets cell attributes values.
     *
     * Sets cell attributes for all cells (default) or a specific an existing cell.
     *
     * Defaults are:
     *     <ul>
     *     <li>id             = pcel%01s
     *     <li>class          = cell
     *     <li>spacing        = 2
     *     <li>active-color   = #006600
     *     <li>inactive-color = #CCCCCC
     *     <li>font-family    = Courier, Verdana
     *     <li>font-size      = lowest value from cell width, cell height, and font size
     *     <li>color          = #000000
     *     <li>background-color    = #FFFFFF
     *     <li>background-image    = none
     *     <li>background-repeat   = no-repeat
     *     <li>background-position = top left
     *     <li>Horizontal Bar :
     *         <ul>
     *         <li>width      = 15
     *         <li>height     = 20
     *         </ul>
     *     <li>Vertical Bar :
     *         <ul>
     *         <li>width      = 20
     *         <li>height     = 15
     *         </ul>
     *     </ul>
     *
     * @param      mixed     $attributes    Associative array or string of HTML tag attributes
     * @param      int       $cell          (optional) Cell index
     *
     * @return     void
     * @since      2.0.0
     * @access     public
     * @throws     HTML_PROGRESS2_ERROR_INVALID_INPUT
     * @see        getCellAttributes()
     */
    function setCellAttributes($attributes, $cell = null)
    {
        if (!is_null($cell)) {
            if (!is_int($cell)) {
                return $this->raiseError(HTML_PROGRESS2_ERROR_INVALID_INPUT, 'exception',
                    array('var' => '$cell',
                          'was' => gettype($cell),
                          'expected' => 'integer',
                          'paramnum' => 1));

            } elseif ($cell < 0) {
                return $this->raiseError(HTML_PROGRESS2_ERROR_INVALID_INPUT, 'error',
                    array('var' => '$cell',
                          'was' => $cell,
                          'expected' => 'positive',
                          'paramnum' => 1));

            } elseif ($cell > $this->cellCount) {
                return $this->raiseError(HTML_PROGRESS2_ERROR_INVALID_INPUT, 'error',
                    array('var' => '$cell',
                          'was' => $cell,
                          'expected' => 'less or equal '.$this->cellCount,
                          'paramnum' => 1));
            }

            $this->_updateAttrArray($this->cell[$cell], $this->_parseAttributes($attributes));
        } else {
            $this->_updateAttrArray($this->cell, $this->_parseAttributes($attributes));
        }

        $font_size   = $this->cell['font-size'];
        $cell_width  = $this->cell['width'];
        $cell_height = $this->cell['height'];
        $margin = ($this->orientation == HTML_PROGRESS2_BAR_HORIZONTAL) ? 0 : 3;

        $font_size = min(min($cell_width, $cell_height) - $margin, $font_size);
        $this->cell['font-size'] = $font_size;

        $this->_updateProgressSize();   // updates the new size of progress bar
    }

    /**
     * Returns coordinates of each cell for a polygonal progress meter.
     *
     * Get array of all cell coordinates (x,y) that define a polygonal
     * progress meter.
     * For example, a 3x3 square as: (0,0) for top left corner,
     * (2,0) for top right corner, (2,2) for bottom right corner, and
     * (0,2) for bottom left corner.
     *
     * @return     array
     * @since      2.0.0
     * @access     public
     * @see        setCellCoordinates()
     */
    function getCellCoordinates()
    {
        return isset($this->_coordinates) ? $this->_coordinates : array();
    }

    /**
     * Sets coordinates of each cell for a polygonal progress meter.
     *
     * A polygonal progress meter is defined by its size (width, height) and its
     * cells coordinates (array of couple x,y screen coordinates).
     *
     * @param      integer   $xgrid     The grid width in cell size
     * @param      integer   $ygrid     The grid height in cell size
     * @param      array     $coord     (optional) Coordinates (x,y) in the grid, of each cell
     *
     * @return     void
     * @since      2.0.0
     * @access     public
     * @throws     HTML_PROGRESS2_ERROR_INVALID_INPUT
     * @see        getCellCoordinates()
     */
    function setCellCoordinates($xgrid, $ygrid, $coord = array())
    {
        if (!is_int($xgrid)) {
            return $this->raiseError(HTML_PROGRESS2_ERROR_INVALID_INPUT, 'exception',
                array('var' => '$xgrid',
                      'was' => gettype($xgrid),
                      'expected' => 'integer',
                      'paramnum' => 1));

        } elseif ($xgrid < 3) {
            return $this->raiseError(HTML_PROGRESS2_ERROR_INVALID_INPUT, 'error',
                array('var' => '$xgrid',
                      'was' => $xgrid,
                      'expected' => 'greater than 2',
                      'paramnum' => 1));

        } elseif (!is_int($ygrid)) {
            return $this->raiseError(HTML_PROGRESS2_ERROR_INVALID_INPUT, 'exception',
                array('var' => '$ygrid',
                      'was' => gettype($ygrid),
                      'expected' => 'integer',
                      'paramnum' => 2));

        } elseif ($ygrid < 3) {
            return $this->raiseError(HTML_PROGRESS2_ERROR_INVALID_INPUT, 'error',
                array('var' => '$ygrid',
                      'was' => $ygrid,
                      'expected' => 'greater than 2',
                      'paramnum' => 2));

        } elseif (!is_array($coord)) {
            return $this->raiseError(HTML_PROGRESS2_ERROR_INVALID_INPUT, 'exception',
                array('var' => '$coord',
                      'was' => gettype($coord),
                      'expected' => 'array',
                      'paramnum' => 3));
        }

        if (count($coord) == 0) {
            // Computes all coordinates of a standard polygon (square or rectangle)
            $coord = $this->_computeCoordinates($xgrid, $ygrid);
        } else {
            foreach ($coord as $id => $pos) {
                if (!is_array($pos)) {
                    return $this->raiseError(HTML_PROGRESS2_ERROR_INVALID_INPUT, 'exception',
                        array('var' => '$coord[,$pos]',
                              'was' => gettype($pos),
                              'expected' => 'array',
                              'paramnum' => 3));
                }
                if ($pos[0] >= $ygrid) {
                    return $this->raiseError(HTML_PROGRESS2_ERROR_INVALID_INPUT, 'error',
                        array('var' => '$pos[0]',
                              'was' => $pos[0],
                              'expected' => 'coordinate less than grid height',
                              'paramnum' => 2));
                }
                if ($pos[1] >= $xgrid) {
                    return $this->raiseError(HTML_PROGRESS2_ERROR_INVALID_INPUT, 'error',
                        array('var' => '$pos[1]',
                              'was' => $pos[1],
                              'expected' => 'coordinate less than grid width',
                              'paramnum' => 1));
                }
            }
        }
        $this->_coordinates = $coord;
        $this->_xgrid = $xgrid;
        $this->_ygrid = $ygrid;

        // auto-compute cell count
        $this->cellCount = count($coord);

        $this->_updateProgressSize();   // updates the new size of progress bar
    }

    /**
     * Returns progress bar's border attributes values.
     *
     * Get all border attributes in a associative array with key-value couple
     * (by default). Get the attributes as string is also possible.
     *
     * @param      bool      $asString      (optional) whether to return the attributes as string
     *
     * @return     mixed
     * @since      2.0.0
     * @access     public
     * @throws     HTML_PROGRESS2_ERROR_INVALID_INPUT
     * @see        setBorderAttributes()
     */
    function getBorderAttributes($asString = false)
    {
        if (!is_bool($asString)) {
            return $this->raiseError(HTML_PROGRESS2_ERROR_INVALID_INPUT, 'exception',
                array('var' => '$asString',
                      'was' => gettype($asString),
                      'expected' => 'boolean',
                      'paramnum' => 1));
        }

        $attr = $this->border;

        if ($asString) {
            return $this->_getAttrString($attr);
        } else {
            return $attr;
        }
    }

    /**
     * Sets the progress bar's border attributes.
     *
     * Defines all border attributes (color, size, ...) a progress bar
     * (only horizontal or vertical) can handle.
     *
     * Defaults are:
     * <ul>
     * <li>class   = progressBorder%s
     * <li>width   = 0
     * <li>style   = solid
     * <li>color   = #000000
     * </ul>
     *
     * @param      mixed     $attributes    Associative array or string of HTML tag attributes
     *
     * @return     void
     * @since      2.0.0
     * @access     public
     * @see        getBorderAttributes()
     */
    function setBorderAttributes($attributes)
    {
        $this->_updateAttrArray($this->border, $this->_parseAttributes($attributes));

        $this->_updateProgressSize();   // updates the new size of progress bar
    }

    /**
     * Returns frame attributes values.
     *
     * Get all frame attributes in a associative array with key-value couple
     * (by default). Get the attributes as string is also possible.
     *
     * @param      bool      $asString      (optional) whether to return the attributes as string
     *
     * @return     mixed
     * @since      2.0.0
     * @access     public
     * @throws     HTML_PROGRESS2_ERROR_INVALID_INPUT
     * @see        setFrameAttributes()
     */
    function getFrameAttributes($asString = false)
    {
        if (!is_bool($asString)) {
            return $this->raiseError(HTML_PROGRESS2_ERROR_INVALID_INPUT, 'exception',
                array('var' => '$asString',
                      'was' => gettype($asString),
                      'expected' => 'boolean',
                      'paramnum' => 1));
        }

        $attr = $this->frame;

        if ($asString) {
            return $this->_getAttrString($attr);
        } else {
            return $attr;
        }
    }

    /**
     * Sets the progress meter frame attributes.
     *
     * Allows to build a customisable frame (color, size) around the progress meter.
     *
     * Defaults are:
     * <ul>
     * <li>show          = true
     * <li>left          = 200
     * <li>top           = 100
     * <li>width         = 320
     * <li>height        = 90
     * <li>color         = #C0C0C0
     * <li>border-width  = 2
     * <li>border-style  = solid
     * <li>border-color  = #DFDFDF #404040 #404040 #DFDFDF
     * </ul>
     *
     * @param      null|array     $attributes    (optional) hash of style parameters
     *
     * @return     void
     * @since      2.0.0
     * @access     public
     * @throws     HTML_PROGRESS2_ERROR_INVALID_INPUT,
     *             HTML_PROGRESS2_ERROR_INVALID_OPTION
     */
    function setFrameAttributes($attributes = array())
    {
        if (!is_null($attributes) && !is_array($attributes)) {
            return $this->raiseError(HTML_PROGRESS2_ERROR_INVALID_INPUT, 'exception',
                array('var' => '$attributes',
                      'was' => gettype($attributes),
                      'expected' => 'array',
                      'paramnum' => 1));
        }

        $default = array(
            'show' => true,
            'left' => 200,
            'top' => 100,
            'width' => 320,
            'height' => 90,
            'color' => '#C0C0C0',
            'border-width' => 2,
            'border-style' => 'solid',
            'border-color' => '#DFDFDF #404040 #404040 #DFDFDF'
        );
        $allowed_options = array_keys($default);

        $options = array_merge($default, $attributes);

        foreach ($options as $prop => $val) {
            if (in_array($prop, $allowed_options)) {
                $this->frame[$prop] = $val;
            } else {
                $this->raiseError(HTML_PROGRESS2_ERROR_INVALID_OPTION, 'warning',
                    array('element' => 'frame', 'prop' => $prop)
                    );
            }
        }
    }

    /**
     * Returns attributes values of an existing label.
     *
     * Get all attributes of an existing label identified by $name argument.
     * This identifier must be the same as the one given to addLabel() method.
     * Return attributes values in a associative array with key-value couple
     * (by default). Get the attributes as string is also possible.
     *
     * @param      string    $name          progress label id.
     * @param      bool      $asString      (optional) whether to return the attributes as string
     *
     * @return     mixed
     * @since      2.0.0
     * @access     public
     * @throws     HTML_PROGRESS2_ERROR_INVALID_INPUT
     * @see        setLabelAttributes()
     */
    function getLabelAttributes($name, $asString = false)
    {
        if (!isset($this->label[$name])) {
            return $this->raiseError(HTML_PROGRESS2_ERROR_INVALID_INPUT, 'error',
                array('var' => '$name',
                      'was' => 'undefined',
                      'expected' => "label '$name' exists",
                      'paramnum' => 1));

        } elseif (!is_bool($asString)) {
            return $this->raiseError(HTML_PROGRESS2_ERROR_INVALID_INPUT, 'exception',
                array('var' => '$asString',
                      'was' => gettype($asString),
                      'expected' => 'boolean',
                      'paramnum' => 2));
        }

        $attr = $this->label[$name];

        if ($asString) {
            return $this->_getAttrString($attr);
        } else {
            return $attr;
        }
    }

    /**
     * Sets attributes values of an existing label.
     *
     * Defines attributes (font, color, size, alignement ...) of an existing label.
     * Whether an attribute is not specified, previous or default value
     * is used instead.
     *
     * Defaults are:
     * <ul>
     * <li>class             = progressPercentLabel%s
     * <li>width             = 50
     * <li>height            = 0
     * <li>font-size         = 11
     * <li>font-family       = Verdana, Tahoma, Arial
     * <li>font-weight       = normal
     * <li>color             = #000000
     * <li>background-color  = transparent
     * <li>align             = right
     * <li>valign            = right
     * </ul>
     *
     * @param      string    $name          progress label id.
     * @param      mixed     $attributes    Associative array or string of HTML tag attributes
     *
     * @return     void
     * @since      2.0.0
     * @access     public
     * @throws     HTML_PROGRESS2_ERROR_INVALID_INPUT
     * @see        getLabelAttributes(), addLabel()
     */
    function setLabelAttributes($name, $attributes)
    {
        if (!isset($this->label[$name])) {
            return $this->raiseError(HTML_PROGRESS2_ERROR_INVALID_INPUT, 'error',
                array('var' => '$name',
                      'was' => "label '$name' undefined",
                      'expected' => 'label already exists',
                      'paramnum' => 1));
        }

        $this->_updateAttrArray($this->label[$name], $this->_parseAttributes($attributes));

        if ($this->label[$name]['type'] == HTML_PROGRESS2_LABEL_TEXT) {
            if ($this->_status != 'new') {
                $this->_changeLabelText($name, $this->label[$name]['value']);
            }
        }
    }

    /**
     * Add a new label to the progress meter.
     *
     * Defines a new label identifier choosen between five categories:
     * <ul>
     * <li>HTML_PROGRESS2_LABEL_TEXT constant if you want a simple text zone
     * <li>HTML_PROGRESS2_LABEL_BUTTON constant if you want a form button
     * <li>HTML_PROGRESS2_LABEL_STEP constant if you want a step resume progress
     * <li>HTML_PROGRESS2_LABEL_PERCENT constant if you want current progress value
     * <li>HTML_PROGRESS2_LABEL_CROSSBAR constant if you want a little javascript animation
     * </ul>
     *
     * @param      string    $type          Label type (text,button,step,percent,crossbar)
     * @param      string    $name          Label name
     * @param      string    $value         (optional) default label value
     *
     * @return     void
     * @since      2.0.0
     * @access     public
     * @throws     HTML_PROGRESS2_ERROR_INVALID_INPUT
     * @see        setLabelAttributes(), removeLabel()
     */
    function addLabel($type, $name, $value = '&nbsp;')
    {
        if (($type != HTML_PROGRESS2_LABEL_TEXT) &&
            ($type != HTML_PROGRESS2_LABEL_BUTTON) &&
            ($type != HTML_PROGRESS2_LABEL_STEP) &&
            ($type != HTML_PROGRESS2_LABEL_PERCENT) &&
            ($type != HTML_PROGRESS2_LABEL_CROSSBAR)) {
            return $this->raiseError(HTML_PROGRESS2_ERROR_INVALID_INPUT, 'error',
                array('var' => '$type',
                      'was' => $type,
                      'expected' => 'HTML_PROGRESS2_LABEL_* constant value',
                      'paramnum' => 1));

        } elseif (!is_string($name)) {
            return $this->raiseError(HTML_PROGRESS2_ERROR_INVALID_INPUT, 'exception',
                array('var' => '$name',
                      'was' => gettype($name),
                      'expected' => 'string',
                      'paramnum' => 2));

        } elseif (isset($this->label[$name])) {
            return $this->raiseError(HTML_PROGRESS2_ERROR_INVALID_INPUT, 'error',
                array('var' => '$name',
                      'was' => 'label already exists',
                      'expected' => "label '$name' undefined",
                      'paramnum' => 2));

        } elseif (!is_string($value)) {
            return $this->raiseError(HTML_PROGRESS2_ERROR_INVALID_INPUT, 'exception',
                array('var' => '$value',
                      'was' => gettype($value),
                      'expected' => 'string',
                      'paramnum' => 3));
        }

        switch($type) {
        case HTML_PROGRESS2_LABEL_TEXT:
            $this->label[$name] = array(
                'type' => $type,
                'value' => $value,
                'left' => 5,
                'top' => 5,
                'width' => 0,
                'height' => 0,
                'align' => 'left',
                'valign' => 'top',
                'background-color' => 'transparent',
                'font-size' => 11,
                'font-family' => 'Verdana, Tahoma, Arial',
                'font-weight' => 'normal',
                'color' => '#000000',
                'class' => 'progressTextLabel%s'
            );
            break;
        case HTML_PROGRESS2_LABEL_BUTTON:
            $this->label[$name] = array(
                'type' => $type,
                'value' => $value,
                'action' => '',
                'target' => 'self',
                'left' => 0,
                'top' => 5,
                'width' => 0,
                'height' => 0,
                'align' => 'center',
                'valign' => 'bottom',
                'background-color' => 'transparent',
                'font-size' => 11,
                'font-family' => 'Verdana, Tahoma, Arial',
                'font-weight' => 'normal',
                'color' => '#000000',
                'class' => 'progressButtonLabel%s'
            );
            break;
        case HTML_PROGRESS2_LABEL_STEP:
            $this->label[$name] = array(
                'type' => $type,
                'value' => $value,
                'left' => 5,
                'top' => 5,
                'width' => 165,
                'height' => 0,
                'align' => 'right',
                'valign' => 'top',
                'background-color' => 'transparent',
                'font-size' => 11,
                'font-family' => 'Verdana, Tahoma, Arial',
                'font-weight' => 'normal',
                'color' => '#000000',
                'class' => 'progressStepLabel%s'
            );
            break;
        case HTML_PROGRESS2_LABEL_PERCENT:
            $this->label[$name] = array(
                'type' => $type,
                'value' => $value,
                'left' => 5,
                'top' => 5,
                'width' => 50,
                'height' => 0,
                'align' => 'right',
                'valign' => 'right',
                'background-color' => 'transparent',
                'font-size' => 11,
                'font-family' => 'Verdana, Tahoma, Arial',
                'font-weight' => 'normal',
                'color' => '#000000',
                'class' => 'progressPercentLabel%s'
            );
            break;
        case HTML_PROGRESS2_LABEL_CROSSBAR:
            $this->label[$name] = array(
                'type' => $type,
                'value' => $value,
                'left' => 5,
                'top' => 5,
                'width' => 20,
                'height' => 0,
                'align' => 'center',
                'valign' => 'top',
                'background-color' => 'transparent',
                'font-size' => 11,
                'font-family' => 'Verdana, Tahoma, Arial',
                'font-weight' => 'normal',
                'color' => '#000000',
                'class' => 'progressCrossbarLabel%s'
            );
            break;
        }
    }

    /**
     * Removes a label to the progress meter.
     *
     * The label identifier must exists or it cannot be remove. This is the same
     * identifier as the one given to addLabel() method.
     *
     * @param      string    $name          Label name
     *
     * @return     void
     * @since      2.0.0
     * @access     public
     * @throws     HTML_PROGRESS2_ERROR_INVALID_INPUT
     * @see        addLabel()
     */
    function removeLabel($name)
    {
        if (!is_string($name)) {
            return $this->raiseError(HTML_PROGRESS2_ERROR_INVALID_INPUT, 'exception',
                array('var' => '$name',
                      'was' => gettype($name),
                      'expected' => 'string',
                      'paramnum' => 1));

        } elseif (!isset($this->label[$name])) {
            return $this->raiseError(HTML_PROGRESS2_ERROR_INVALID_INPUT, 'notice',
                array('var' => '$name',
                      'was' => 'label does not exists',
                      'expected' => "label '$name' defined",
                      'paramnum' => 1));

        }

        unset($this->label[$name]);
    }

    /**
     * Returns the progress background attributes values.
     *
     * Get all background attributes in a associative array with key-value couple
     * (by default). Get the attributes as string is also possible.
     *
     * @param      bool      $asString      (optional) whether to return the attributes as string
     *
     * @return     mixed
     * @since      2.0.0
     * @access     public
     * @throws     HTML_PROGRESS2_ERROR_INVALID_INPUT
     * @see        setProgressAttributes()
     */
    function getProgressAttributes($asString = false)
    {
        if (!is_bool($asString)) {
            return $this->raiseError(HTML_PROGRESS2_ERROR_INVALID_INPUT, 'exception',
                array('var' => '$asString',
                      'was' => gettype($asString),
                      'expected' => 'boolean',
                      'paramnum' => 1));
        }

        $attr = $this->_progress;

        if ($asString) {
            return $this->_getAttrString($attr);
        } else {
            return $attr;
        }
    }

    /**
     * Sets the progress background attributes values.
     *
     * Background attributes are color and size, with default values:
     * <ul>
     * <li>background-color    = #FFFFFF
     * <li>background-image    = none
     * <li>background-repeat   = no-repeat
     * <li>background-position = top left
     * <li>Horizontal Bar :
     *     <ul>
     *     <li>width         = (cell_count * (cell_width + cell_spacing)) + cell_spacing
     *     <li>height        = cell_height + (2 * cell_spacing)
     *     </ul>
     * <li>Vertical Bar :
     *     <ul>
     *     <li>width         = cell_width + (2 * cell_spacing)
     *     <li>height        = (cell_count * (cell_height + cell_spacing)) + cell_spacing
     *     </ul>
     * </ul>
     *
     * @param      mixed     $attributes    Associative array or string of HTML tag attributes
     *
     * @return     void
     * @since      2.0.0
     * @access     public
     * @see        getProgressAttributes()
     */
    function setProgressAttributes($attributes)
    {
        $this->_updateAttrArray($this->_progress, $this->_parseAttributes($attributes));
    }

    /**
     * Returns javascript progress meter handler.
     *
     * Get the javascript URL or inline code that will handle the progress meter
     * refresh.
     *
     * @param      boolean   $raw           (optional) html output with script tags or just raw data
     * @param      string    $path          (optional) directory, with no trailing slash,
     *                                      where to get HTML_Progress2.js file
     *
     * @return     string
     * @since      2.0.0
     * @access     public
     * @throws     HTML_PROGRESS2_ERROR_INVALID_INPUT,
     *             HTML_PROGRESS2_ERROR_INVALID_RESOURCE
     * @see        setScript()
     */
    function getScript($raw = true, $path = null)
    {
        if (!is_bool($raw)) {
            return $this->raiseError(HTML_PROGRESS2_ERROR_INVALID_INPUT, 'exception',
                array('var' => '$raw',
                      'was' => gettype($raw),
                      'expected' => 'boolean',
                      'paramnum' => 1));

        } elseif (isset($path)) {
            if (!is_string($path)) {
                return $this->raiseError(HTML_PROGRESS2_ERROR_INVALID_INPUT, 'exception',
                    array('var' => '$path',
                          'was' => gettype($path),
                          'expected' => 'string',
                          'paramnum' => 2));

            } elseif (!is_dir($path)) {
                return $this->raiseError(HTML_PROGRESS2_ERROR_INVALID_RESOURCE, 'error',
                    array('var' => '$path',
                          'resource' => $path,
                          'expected' => 'directory',
                          'paramnum' => 2));

            } elseif (!file_exists($js = $path . DIRECTORY_SEPARATOR . 'HTML_Progress2.js')) {
                return $this->raiseError(HTML_PROGRESS2_ERROR_INVALID_RESOURCE, 'error',
                    array('var' => '$path',
                          'resource' => $js,
                          'expected' => 'directory with valid JS handler',
                          'paramnum' => 2));
            }
        }

        if (!is_null($this->script)) {
            if ($raw) {
                $js = $this->script;   // URL to the linked Progress JavaScript
            } else {
                $js = '<script type="text/javascript" src="' . $this->script
                    . '"></script>' . PHP_EOL;
            }
            return $js;
        }

        if (isset($path)) {
            $js = $path;
        } else {
            $js = '@data_dir@' . DIRECTORY_SEPARATOR . '@package_name@';

            if (strpos($js, '@'.'data_dir@') === 0) {
                $js = dirname(__FILE__);
            }
        }
        $js .= DIRECTORY_SEPARATOR . 'HTML_Progress2.js';
        $js = file_get_contents($js);

        if ($raw !== true) {
            $js = '<script type="text/javascript">'
                . PHP_EOL . '//<![CDATA['
                . PHP_EOL . $js
                . PHP_EOL . '//]]>'
                . PHP_EOL . '</script>'
                . PHP_EOL;
        }
        return $js;
    }

    /**
     * Sets the javascript progress meter handler.
     *
     * Defines the javascript source (URL or inline code) that will handle
     * the progress meter refresh.
     *
     * @param      string    $url           URL to the linked Progress JavaScript
     *
     * @return     void
     * @since      2.0.0
     * @access     public
     * @throws     HTML_PROGRESS2_ERROR_INVALID_INPUT
     * @see        getScript()
     */
    function setScript($url)
    {
        if (!is_null($url)) {
            if (!is_string($url)) {
                return $this->raiseError(HTML_PROGRESS2_ERROR_INVALID_INPUT, 'exception',
                    array('var' => '$url',
                          'was' => gettype($url),
                          'expected' => 'string',
                          'paramnum' => 1));

            } elseif (!is_file($url) || $url == '.' || $url == '..') {
                return $this->raiseError(HTML_PROGRESS2_ERROR_INVALID_INPUT, 'error',
                    array('var' => '$url',
                          'was' => $url.' file does not exists',
                          'expected' => 'javascript file exists',
                          'paramnum' => 1));
            }
        }

        /*
         - since version 0.5.0,
         - default javascript code comes from getScript() method
         - but may be overrided by external file.
        */
        $this->script = $url;
    }

    /**
     * Draw all circle segment pictures.
     *
     * This function build/prepare all circle segment PNG pictures that will be
     * send to browser output. Requires PEAR::Image_Color and PHP:gd extension.
     *
     * @param      string    $dir           (optional) Directory where pictures should be created
     * @param      string    $fileMask      (optional) sprintf format for pictures filename
     *
     * @return     array
     * @since      2.0.0
     * @access     public
     * @throws     HTML_PROGRESS2_ERROR_INVALID_INPUT
     * @see        setCellAttributes()
     */
    function drawCircleSegments($dir = '.', $fileMask = 'c%s.png')
    {
        if (!is_dir($dir)) {
            return $this->raiseError(HTML_PROGRESS2_ERROR_INVALID_INPUT, 'error',
                array('var' => '$dir',
                      'was' => $dir,
                      'expected' => 'directory exists',
                      'paramnum' => 1));
        }

        require_once 'Image/Color.php';

        $cellAttr  = $this->getCellAttributes();
        $w = $cellAttr['width'];
        $h = $cellAttr['height'];
        $s = $cellAttr['spacing'];
        $c = intval(360 / $this->cellCount);
        if (fmod($w,2) == 0) {
            $cx = floor($w / 2) - 0.5;
        } else {
            $cx = floor($w / 2);
        }
        if (fmod($h,2) == 0) {
            $cy = floor($h / 2) - 0.5;
        } else {
            $cy = floor($h / 2);
        }

        $image = imagecreate($w, $h);

        $bg     = Image_Color::allocateColor($image, $cellAttr['background-color']);
        $colorA = Image_Color::allocateColor($image, $cellAttr['active-color']);
        $colorI = Image_Color::allocateColor($image, $cellAttr['inactive-color']);

        imagefilledarc($image, $cx, $cy, $w, $h, 0, 360, $colorI, IMG_ARC_EDGED);
        $filename = $dir . DIRECTORY_SEPARATOR . sprintf($fileMask,0);
        imagepng($image, $filename);
        $this->setCellAttributes(array('background-image' => $filename),0);

        for ($i = 0; $i < $this->cellCount; $i++) {
            if ($this->fillWay == 'natural') {
                $sA = $i*$c;
                $eA = ($i+1)*$c;
                $sI = ($i+1)*$c;
                $eI = 360;
            } else {
                $sA = 360-(($i+1)*$c);
                $eA = 360-($i*$c);
                $sI = 0;
                $eI = 360-(($i+1)*$c);
            }
            if ($s > 0) {
                imagefilledarc($image, $cx, $cy, $w, $h, 0, $sA, $colorI, IMG_ARC_EDGED);
            }
            imagefilledarc($image, $cx, $cy, $w, $h, $sA, $eA, $colorA, IMG_ARC_EDGED);
            imagefilledarc($image, $cx, $cy, $w, $h, $sI, $eI, $colorI, IMG_ARC_EDGED);
            $filename = $dir . DIRECTORY_SEPARATOR . sprintf($fileMask,$i+1);
            imagepng($image, $filename);

            $this->setCellAttributes(array('background-image' => $filename),$i+1);
        }
        imagedestroy($image);
    }

    /**
     * Returns delay execution of the progress meter.
     *
     * Given a delay in process of the progress meter is only necessary
     * if you need demonstration or also smooth animation.
     * The default value is zero (no delay).
     *
     * @return     integer
     * @since      2.0.0
     * @access     public
     * @see        setAnimSpeed()
     */
    function getAnimSpeed()
    {
        return $this->animSpeed;
    }

    /**
     * Sets delay execution of the progress meter.
     *
     * The delay (in millisecond) cannot exceed 1000 (1 second), that is enough
     * to smooth an animation. User process should slow down animation and in most
     * case the default value (zero) will be efficient.
     *
     * @param      integer   $delay         Delay in millisecond.
     *
     * @return     void
     * @since      2.0.0
     * @access     public
     * @throws     HTML_PROGRESS2_ERROR_INVALID_INPUT
     * @see        getAnimSpeed()
     */
    function setAnimSpeed($delay)
    {
        if (!is_int($delay)) {
            return $this->raiseError(HTML_PROGRESS2_ERROR_INVALID_INPUT, 'exception',
                array('var' => '$delay',
                      'was' => gettype($delay),
                      'expected' => 'integer',
                      'paramnum' => 1));

        } elseif ($delay < 0) {
            return $this->raiseError(HTML_PROGRESS2_ERROR_INVALID_INPUT, 'error',
                array('var' => '$delay',
                      'was' => $delay,
                      'expected' => 'greater than zero',
                      'paramnum' => 1));

        } elseif ($delay > 1000) {
            return $this->raiseError(HTML_PROGRESS2_ERROR_INVALID_INPUT, 'error',
                array('var' => '$delay',
                      'was' => $delay,
                      'expected' => 'less or equal 1000',
                      'paramnum' => 1));
        }
        $this->animSpeed = $delay;
    }

    /**
     * Returns the cascading style sheet (CSS).
     *
     * Get the CSS required to display the progress meter in a HTML document.
     *
     * @param      boolean   $raw           (optional) html output with script tags or just raw data
     *
     * @return     string
     * @since      2.0.0
     * @access     public
     * @throws     HTML_PROGRESS2_ERROR_INVALID_INPUT
     */
    function getStyle($raw = true)
    {
        if (!is_bool($raw)) {
            return $this->raiseError(HTML_PROGRESS2_ERROR_INVALID_INPUT, 'exception',
                array('var' => '$raw',
                      'was' => gettype($raw),
                      'expected' => 'boolean',
                      'paramnum' => 1));
        }

        $progressAttr = $this->getProgressAttributes();
        $borderAttr = $this->getBorderAttributes();
        $cellAttr = $this->getCellAttributes();

        $css = new HTML_CSS();

        $borderCls = '.' . sprintf($borderAttr['class'], $this->ident);
        $css->setStyle($borderCls, 'width', $progressAttr['width'].'px');
        $css->setStyle($borderCls, 'height', $progressAttr['height'].'px');
        if ($this->isBorderPainted()) {
            $css->setStyle($borderCls, 'border-width', $borderAttr['width'].'px');
            $css->setStyle($borderCls, 'border-style', $borderAttr['style']);
            $css->setStyle($borderCls, 'border-color', $borderAttr['color']);
        }
        if ($progressAttr['background-image'] !== 'none') {
            $css->setStyle($borderCls, 'background-image', 'url("'. $progressAttr['background-image'] .'")');
            $css->setStyle($borderCls, 'background-repeat', $progressAttr['background-repeat']);
            $css->setStyle($borderCls, 'background-position', $progressAttr['background-position']);
        }
        if ($this->cellCount > 0) {
            $css->setStyle($borderCls, 'background-color', $progressAttr['background-color']);
        } else {
            $css->setStyle($borderCls, 'background-color', $cellAttr['inactive-color']);
        }

        foreach($this->label as $name => $data) {
            $style = '.' . sprintf($data['class'], $name . $this->ident);

            if ($data['width'] > 0) {
                $css->setStyle($style, 'width', $data['width'].'px');
            }
            if ($data['height'] > 0) {
                $css->setStyle($style, 'height', $data['height'].'px');
            }
            $css->setStyle($style, 'text-align', $data['align']);
            $css->setStyle($style, 'background-color', $data['background-color']);
            $css->setStyle($style, 'font-size', $data['font-size'].'px');
            $css->setStyle($style, 'font-family', $data['font-family']);
            $css->setStyle($style, 'font-weight', $data['font-weight']);
            $css->setStyle($style, 'color', $data['color']);
        }

        $cellClsI = '.' . sprintf($cellAttr['class'], $this->ident) . 'I';
        $cellClsA = '.' . sprintf($cellAttr['class'], $this->ident) . 'A';
        $css->setStyle($cellClsI, 'width', $cellAttr['width'].'px');
        $css->setStyle($cellClsI, 'height', $cellAttr['height'].'px');
        $css->setStyle($cellClsI, 'font-family', $cellAttr['font-family']);
        $css->setStyle($cellClsI, 'font-size', $cellAttr['font-size'].'px');

        if ($this->orientation == HTML_PROGRESS2_BAR_HORIZONTAL) {
            $css->setStyle($cellClsI, 'float', 'left');
        }
        if ($this->orientation == HTML_PROGRESS2_BAR_VERTICAL) {
            $css->setStyle($cellClsI, 'float', 'none');
        }
        $css->setSameStyle($cellClsA, $cellClsI);

        if ($this->orientation !== HTML_PROGRESS2_CIRCLE) {
            $css->setStyle($cellClsI, 'background-color', $cellAttr['inactive-color']);
        }

        $css->setStyle($cellClsA, 'background-color', $cellAttr['active-color']);
        if ($cellAttr['background-image'] !== 'none') {
            $css->setStyle($cellClsA, 'background-image', 'url("'. $cellAttr['background-image'] .'")');
            if ($this->orientation == HTML_PROGRESS2_CIRCLE) {
                $css->setStyle($cellClsA, 'background-repeat', 'no-repeat');
            } else {
                $css->setStyle($cellClsA, 'background-repeat', $cellAttr['background-repeat']);
                $css->setStyle($cellClsA, 'background-position', $cellAttr['background-position']);
            }
        }

        if ($this->orientation == HTML_PROGRESS2_CIRCLE) {
            $css->setStyle($cellClsI, 'background-image', 'url("'.$cellAttr[0]['background-image'].'")');
            $css->setStyle($cellClsI, 'background-repeat', 'no-repeat');
        }
        $styles = $css->toString();

        if ($raw !== true) {
            $styles = '<style type="text/css">' . PHP_EOL
                    . '<!--'    . PHP_EOL
                    . $styles   . PHP_EOL
                    . ' -->'    . PHP_EOL
                    . '</style>'. PHP_EOL;
        }
        return $styles;
    }

    /**
     * Import cascading style sheet (CSS) elements
     *
     * Set the CSS required to display the progress meter in a HTML document.
     *
     * @param      mixed     $styles        CSS elements reference to import
     *
     * @return     void|PEAR_Error
     * @since      2.2.0
     * @access     public
     * @throws     HTML_PROGRESS2_ERROR_INVALID_INPUT
     */
    function importStyle($styles)
    {
        if (is_string($styles)) {
            $styles = (array)$styles;
        }

        if (!is_array($styles)) {
            return $this->raiseError(HTML_PROGRESS2_ERROR_INVALID_INPUT, 'exception',
                array('var' => '$styles',
                      'was' => gettype($styles),
                      'expected' => 'array | string',
                      'paramnum' => 1));
        }

        $css = new HTML_CSS();

        $res = $css->parseData($styles);
        if ($css->isError()) {
            return $this->raiseError(HTML_PROGRESS2_ERROR_INVALID_INPUT, 'error',
                array('var' => '$styles',
                      'was' => 'unknown data source',
                      'expected' => 'valid CSS',
                      'paramnum' => 1));
        }

        if (strpos($this->border['class'], '%s') === false) {
            $pattern = $this->ident . '\.' . $this->border['class'];
        } else {
            $pattern = '\.' . sprintf($this->border['class'], $this->ident);
        }
        $border = $css->grepStyle("/$pattern/");

        foreach ($border as $b) {
            foreach ($b as $p => $v) {
                if (substr($p, 0, 6) == 'border') {
                    $n = str_replace('border-', '', $p);
                    if (isset($this->border[$n])) {
                        if (substr($v, -2) == 'px') {
                            $this->border[$n] = intval($v);
                        } else {
                            $this->border[$n] = $v;
                        }
                    }
                } else {
                    if ($p == 'background-color' && $this->cellCount == 0) {
                        $this->cell['inactive-color'] = $v;
                    } elseif (isset($this->_progress[$p])) {
                        if (substr($v, -2) == 'px') {
                            $this->_progress[$p] = intval($v);
                        } else {
                            $this->_progress[$p] = $v;
                        }
                    }
                }
            }
            if ($this->border['width'] > 0) {
                $this->_paintBorder = true;
            }
        }

        foreach ($this->label as $name => $data) {
            if (strpos($data['class'], '%s') === false) {
                $pattern = $name . $this->ident . '\.' . $data['class'];
            } else {
                $pattern = '\.' . sprintf($data['class'], $name . $this->ident);
            }
            $label = $css->grepStyle("/$pattern/");

            foreach ($label as $l) {
                foreach ($l as $p => $v) {
                    if (substr($p, 0, 4) == 'text') {
                        $n = str_replace('text-', '', $p);
                        if (isset($this->label[$name][$n])) {
                            $this->label[$name][$n] = $v;
                        }
                    } elseif (isset($this->label[$name][$p])) {
                        if (substr($v, -2) == 'px') {
                            $this->label[$name][$p] = intval($v);
                        } else {
                            $this->label[$name][$p] = $v;
                        }
                    }
                }
            }
        }

        $pcell = '.' . sprintf($this->cell['class'], $this->ident);
        if (strpos($this->cell['class'], '%s') === false) {
            $pattern = $this->ident . '\s*\.' . $this->cell['class'];
        } else {
            $pattern = '\.' . sprintf($this->cell['class'], $this->ident);
        }
        $cell = $css->grepStyle("/$pattern/");

        foreach ($cell as $c => $data) {
            foreach ($data as $p => $v) {
                if ($p == 'background-color') {
                    if (strpos($c, $pcell.'A') !== false) {
                        $this->cell['active-color'] = $v;
                    } else {
                        $this->cell['inactive-color'] = $v;
                    }
                } elseif ($p == 'background-image') {
                    $pattern = "\s*url\s*\([\s\"'`]*([\w:?=@&\/#._;-]+)[\s\"'`]*\)\s*";
                    $found = preg_match("/$pattern/", $v, $matches);
                    if ($found) {
                        $this->cell[$p] = $matches[1];
                    }
                } else {
                    if (substr($v, -2) == 'px') {
                        $this->cell[$p] = intval($v);
                    } else {
                        $this->cell[$p] = $v;
                    }
                }
            }
        }
    }

    /**
     * Returns the progress meter structure into an array.
     *
     * Get all progress meter properties, couple key-value, into a php array.
     * This structure is read-only, a dump-like information.
     *
     * @return     array
     * @since      2.0.0
     * @access     public
     */
    function toArray()
    {
        $structure = array(
            'id' => $this->ident,
            'indeterminate' => $this->indeterminate,
            'borderpainted' => $this->isBorderPainted(),
            'label' => $this->label,
            'animspeed' => $this->animSpeed,
            'orientation' => $this->orientation,
            'fillway' => $this->fillWay,
            'cell' => $this->cell,
            'cellcount' => $this->cellCount,
            'cellcoord' => $this->getCellCoordinates(),
            'border' => $this->border,
            'progress' => $this->_progress,
            'script' => (!is_null($this->script)) ? $this->script : false,
            'ajax' => $this->ajax,
            'aflax' => $this->aflax,
            'minimum' => $this->minimum,
            'maximum' => $this->maximum,
            'increment' => $this->increment,
            'value' => $this->value,
            'percent' => $this->getPercentComplete()
        );
        return $structure;
    }

    /**
     * Returns the progress meter structure as HTML.
     *
     * Get html code required to display the progress meter in any html document.
     *
     * @return     string
     * @since      2.0.0
     * @access     public
     */
    function toHtml()
    {
        $strHtml = '';
        $tabs = $this->_getTabs();
        $tab = $this->_getTab();
        $comment = $this->getComment();
        $progressAttr = $this->getProgressAttributes();
        $borderAttr = $this->getBorderAttributes();
        $cellAttr = $this->getCellAttributes();

        /**
         *  Adds a progress meter caption in html code is possible.
         *  See HTML_Common::setComment() method.
         */
        if (strlen($comment) > 0) {
            $strHtml .= $tabs . "<!-- $comment -->" . PHP_EOL;
        }

        //  Start of Top progress meter frame
        if ($this->frame['show']) {
            $topshift = $progressAttr['top'];
            $leftshift = $progressAttr['left'];
            $border = '';
            if ($this->frame['border-width'] > 0) {
                $border = 'border-width:' . $this->frame['border-width'] . 'px;'
                        . 'border-style:' . $this->frame['border-style'] . ';'
                        . 'border-color:' . $this->frame['border-color'] . ';';
            }
            if ($progressAttr['position'] == 'relative') {
                $_top = $_left = 0;
            } else {
                $_top = $this->frame['top'];
                $_left = $this->frame['left'];
            }
            $strHtml .= $tabs
                  .  '<div id="' . $this->ident . '" style="'
                  .  'position:' . $progressAttr['position'] . ';'
                  .  'top:' . $_top . 'px;'
                  .  'left:' . $_left . 'px;'
                  .  'width:' . $this->frame['width'] . 'px;'
                  .  'height:' . $this->frame['height'] . 'px;'
                  .  $border
                  .  'background-color:' . $this->frame['color'] . ';">'
                  .  PHP_EOL;

        } else {
            $topshift = $progressAttr['top'];
            $leftshift = 0;
            $strHtml .= $tabs
                 .  '<div id="' . $this->ident . '" style="'
                 .  'position:' . $progressAttr['position'] . ';'
                 .  'top:' . $progressAttr['top'] . 'px;'
                 .  'left:' . $progressAttr['left'] . 'px;'
                 .  'height:{_heightshift_}px;">'
                 .  PHP_EOL;
        }
        $topshift = 0;

        //  Start of progress meter border
        $strHtml .= $tabs
                 .  '<div id="pbrd' . $this->ident . '"'
                 .  ' style="position:absolute;top:{_topshift_}px;left:{_leftshift_}px;"'
                 .  ' class="' . sprintf($borderAttr['class'], $this->ident) . '">'
                 .  PHP_EOL;

        //  Start of progress meter
        if ($this->cellCount == 0) {
            $strHtml .= $tabs
                     .  '<div id="pbar' . $this->ident . '" style="'
                     .  'width:' . $progressAttr['width'] . 'px;'
                     .  'height:' . $progressAttr['height'] . 'px;'
                     .  'background-color:' . $cellAttr['active-color'] . ';">'
                     .  PHP_EOL;
        } else {
            $strHtml .= $tabs
                     .  '<div id="pbar' . $this->ident . '">'
                     .  PHP_EOL;
        }

        if ($this->orientation == HTML_PROGRESS2_BAR_HORIZONTAL) {
            $progressHtml = $this->_getProgressHbar_toHtml();
        }
        if ($this->orientation == HTML_PROGRESS2_BAR_VERTICAL) {
            $progressHtml = $this->_getProgressVbar_toHtml();
        }
        if ($this->orientation == HTML_PROGRESS2_POLYGONAL) {
            $progressHtml = $this->_getProgressPolygonal_toHtml();
        }
        if ($this->orientation == HTML_PROGRESS2_CIRCLE) {
            $cellAttr = $this->getCellAttributes();
            if (!isset($cellAttr[0]['background-image']) || !file_exists($cellAttr[0]['background-image'])) {
                // creates default circle segments pictures :
                // 'c0.png'->0% 'c1.png'->10%, 'c2.png'->20%, ... 'c10.png'->100%
                $this->drawCircleSegments();
            }
            $progressHtml = $this->_getProgressCircle_toHtml();
        }

        $strHtml .= $tabs
                 .  $progressHtml
                 .  PHP_EOL;

        //  Enf of progress meter
        $strHtml .= $tabs
                 .  '</div>'
                 .  PHP_EOL;

        //  Enf of progress meter border
        $strHtml .= $tabs
                 .  '</div>'
                 .  PHP_EOL;

        $heightshift = $topshift + $progressAttr['height'];
        $bottomdef = $topdef = false;

        //  Start of progress meter labels
        foreach ($this->label as $name => $data) {

            $width = $data['width'];
            $height = $data['height'];

            if ($progressAttr['position'] == 'relative') {
                switch ($data['valign']) {
                    case 'top':
                        $style_pos = 'top:0;left:{_leftshift_}px;';
                        if ($topdef == false){
                            if ($height == 0) {
                                $height = $progressAttr['height'];
                            }
                            $topshift += $height;
                            $heightshift += $height;
                            $topdef = true;
                        }
                        break;
                    case 'right':
                        $style_pos = 'top:{_topshift_}px;'
                                   . 'left:{_rxshift_}px;';
                        break;
                    case 'bottom':
                        $style_pos = 'top:{_bottomshift_}px;'
                                   . 'left:{_leftshift_}px;';
                        if ($bottomdef == false){
                            if ($height == 0) {
                                $height = $progressAttr['height'];
                            }
                            $heightshift += $height;
                            $bottomdef = true;
                        }
                        break;
                    case 'left':
                        $style_pos = 'top:{_topshift_}px;left:0;';
                        if ($data['width'] > 0) {
                            $leftshift = $data['width'];
                        } else {
                            $leftshift = $progressAttr['width'];
                        }
                        $leftshift += $data['left'];
                        break;
                    case 'center':
                        $style_pos = 'top:{_topshift_}px;'
                                   . 'left:{_leftshift_}px;';
                        $width = $progressAttr['width'];
                        break;
                }
                $style_pos .= 'margin-top:' . $data['top'] . 'px;'
                           .  'margin-left:' . $data['left'] . 'px;';
                if ($width > 0) {
                    $style_pos .= 'width:' . $width . 'px;';
                }
            } else {
                $style_pos = 'top:' . $data['top'] . 'px;'
                           . 'left:' . $data['left'] . 'px;';
            }
            $style_cls = sprintf($data['class'], $name . $this->ident);

            switch ($data['type']) {
                case HTML_PROGRESS2_LABEL_TEXT:
                    $strHtml .= $tabs
                             .  '<div id="plbl' . $name . $this->ident . '"'
                             .  ' style="position:absolute;' . $style_pos . '"'
                             .  ' class="' . $style_cls . '">'
                             .  $data['value']
                             .  '</div>'
                             .  PHP_EOL;
                    break;
                case HTML_PROGRESS2_LABEL_BUTTON:
                    $strHtml .= $tabs
                             .  '<div><input id="plbl' . $name . $this->ident
                             .  '" type="button" value="' . $data['value']
                             .  '" style="position:absolute;' . $style_pos
                             .  '" class="' . $style_cls
                             .  '" onclick="' . $data['target']
                             .  '.location.href=\'' . $data['action'] . '\'" />'
                             .  '</div>'
                             .  PHP_EOL;
                    break;
                case HTML_PROGRESS2_LABEL_STEP:
                    $strHtml .= $tabs
                             .  '<div id="plbl' . $name . $this->ident
                             .  '" style="position:absolute;' . $style_pos
                             .  '" class="' . $style_cls . '">&nbsp;'
                             .  '</div>'
                             .  PHP_EOL;
                    break;
                case HTML_PROGRESS2_LABEL_PERCENT:
                    $strHtml .= $tabs
                             .  '<div id="plbl' . $name . $this->ident . '"'
                             .  ' style="position:absolute;' .  $style_pos . '"'
                             .  ' class="' . $style_cls . '">&nbsp;'
                             .  '</div>'
                             .  PHP_EOL;
                    break;
                case HTML_PROGRESS2_LABEL_CROSSBAR:
                    $strHtml .= $tabs
                             .  '<div id="plbl' . $name . $this->ident . '"'
                             .  ' style="position:absolute;' .  $style_pos . '"'
                             .  ' class="' . $style_cls . '">'
                             .  $data['value']
                             .  '</div>'
                             .  PHP_EOL;
                    break;
            }
        }

        //  End of Top progress meter frame
        $strHtml .= $tabs
                 .  '</div>'
                 .  PHP_EOL;

        $placeHolders = array(
            '{_topshift_}', '{_leftshift_}', '{_heightshift_}', '{_rxshift_}',
            '{_bottomshift_}'
        );
        $htmlElement = array(
            $topshift, $leftshift, $heightshift, ($leftshift + $progressAttr['width']),
            ($topshift + $progressAttr['height'])
        );
        $strHtml = str_replace($placeHolders, $htmlElement, $strHtml);

        return $strHtml;
    }

    /**
     * Renders the new value of progress meter.
     *
     * This method should be used only to display initial state of the progress meter.
     * Next steps to refresh display must use either moveStep() or moveNext() methods.
     *
     * @return     void
     * @since      2.0.0
     * @access     public
     */
    function display()
    {
        $this->_status = 'show';
        echo $this->toHtml();
    }

    /**
     * Hides the progress meter.
     *
     * Once the process is over this method provides a solution
     * to remove/hide the progress meter of the browser screen.
     *
     * @return     void
     * @since      2.0.0
     * @access     public
     */
    function hide()
    {
        $bar = '<script type="text/javascript">'
             .  'HTML_Progress2.hideProgress("' . $this->ident . '");'
             .  '</script>';

        echo $bar . PHP_EOL;
    }

    /**
     * Delay execution.
     *
     * The HTML_Progress2::sleep() function delays program execution
     * for the given number of milliseconds.
     * This is the default user callback when none are defined.
     *
     * NOTE: The function {@link http://www.php.net/manual/en/function.usleep.php}
     *       did not work on Windows systems until PHP 5.0.0
     *
     * @return     void
     * @since      2.0.0
     * @access     public
     * @see        getAnimSpeed(), setAnimSpeed(), process()
     */
    function sleep()
    {
        // convert delay from milliseconds to microseconds
        $usecs = $this->animSpeed * 1000;

        if ((substr(PHP_OS, 0, 3) == 'WIN') && (substr(PHP_VERSION,0,1) < '5')) {
            for ($i = 0; $i < $usecs; $i++) {
            }
        } else {
            usleep($usecs);
        }
    }

    /**
     * Sets the user progress callback function.
     *
     * The process() function will call the user-callback defined here by this
     * setProgressHandler() method.
     *
     * The user-supplied progress function must return either positive
     * for a step progression, using moveStep() method,
     * or NULL for a standard progression, using moveNext() method.
     *
     * @param      mixed     $handler       Name of function or a class-method.
     *
     * @return     void
     * @since      2.0.0
     * @access     public
     * @throws     HTML_PROGRESS2_ERROR_INVALID_CALLBACK
     * @see        process()
     */
    function setProgressHandler($handler)
    {
        if (!is_callable($handler)) {
            return $this->raiseError(HTML_PROGRESS2_ERROR_INVALID_CALLBACK, 'warning',
                array('var' => '$handler',
                      'element' => 'valid Class-Method/Function',
                      'was' => 'callback',
                      'paramnum' => 1));
        }
        $this->_callback = $handler;
    }

    /**
     * Performs the progress user process.
     *
     * This function call the user-specified progress function, defined by
     * setProgressHandler() method. Default callback is the HTML_Progress2::sleep() method.
     *
     * @return     mixed
     * @since      2.0.0
     * @access     public
     * @see        sleep(), setProgressHandler()
     */
    function process()
    {
        if ($this->_callback) {
            return call_user_func_array($this->_callback, array($this->value, &$this));
        } else {
            // when there is no valid user callback then default is to sleep a bit ...
            $this->sleep();
        }
    }

    /**
     * Runs the progress meter.
     *
     * This function accept both modes: indeterminate and determinate,
     * and execute all actions defined in the user callback identified by
     * setProgressHandler() method.
     *
     * All observers are also notified of main changes (start, stop meter).
     *
     * @return     void
     * @since      2.0.0
     * @access     public
     * @see        process(), setProgressHandler()
     */
    function run()
    {
        $this->_postNotification('onSubmit', array('handler' => __FUNCTION__, 'value' => $this->getValue()));
        do {
            $ret = $this->process();
            if ($this->getPercentComplete() == 1) {
                if ($this->indeterminate) {
                    $this->setValue(0);
                } else {
                    break;
                }
            }
            if (is_null($ret)) {
                $this->moveNext();
            } else {
                $this->moveStep($ret);
            }
        } while (1);
        $this->_postNotification('onLoad', array('handler' => __FUNCTION__, 'value' => $this->getValue()));
    }

    /**
     * Returns the progress meter identifier.
     *
     * Each progress meter has its own identifier. That allows to display more than
     * only once meter at same time on same page.
     *
     * @return     string
     * @since      2.0.0
     * @access     public
     * @see        setIdent()
     */
    function getIdent()
    {
        return $this->ident;
    }

    /**
     * Sets the progress meter identifier.
     *
     * Each progress meter has its own identifier. That allows to display more than
     * only once meter at same time on same page.
     * If no identification string is given, then the default identifier will be
     * six first characters of md5 hash value of the current unix timestamp.
     *
     * @param      mixed     $ident         (optional) the new identification string.
     *
     * @since      2.0.0
     * @access     public
     * @see        getIdent()
     */
    function setIdent($ident = null)
    {
        if (is_null($ident)) {
            $this->ident = 'PB' . substr(md5(microtime()), 0, 6);
        } else {
            $this->ident = $ident;
        }
    }

    /**
     * Attachs a new observer.
     *
     * Adds a new observer to the Event Dispatcher that will listen
     * for all messages emitted by this HTML_Progress2 instance.
     *
     * @param      mixed     $callback      PHP callback that will act as listener
     * @param      string    $nName         Expected notification name, serves as a filter
     *
     * @return     void
     * @since      2.0.0
     * @access     public
     * @throws     HTML_PROGRESS2_ERROR_INVALID_CALLBACK,
     *             HTML_PROGRESS2_ERROR_INVALID_INPUT
     * @see        removeListener()
     */
    function addListener($callback, $nName = EVENT_DISPATCHER_GLOBAL)
    {
        if (!is_callable($callback)) {
            return $this->raiseError(HTML_PROGRESS2_ERROR_INVALID_CALLBACK, 'exception',
                array('var' => '$callback',
                      'element' => 'valid Class-Method/Function',
                      'was' => 'callback',
                      'paramnum' => 1));

        } elseif (!is_string($nName)) {
            return $this->raiseError(HTML_PROGRESS2_ERROR_INVALID_INPUT, 'exception',
                array('var' => '$nName',
                      'was' => gettype($nName),
                      'expected' => 'string',
                      'paramnum' => 2));
        }

        $this->dispatcher =& Event_Dispatcher::getInstance('ProgressMeter');
        $this->dispatcher->addObserver($callback, $nName);
        $this->_observerCount++;
    }

    /**
     * Removes a registered observer.
     *
     * Detachs a previously registered observer and remove the Event Dispatcher
     * if there is no more observer registered.
     *
     * @param      mixed     $callback      PHP callback that act as listener
     * @param      string    $nName         Expected notification name, serves as a filter
     *
     * @return     bool                     True if observer was removed, false otherwise
     * @since      2.0.0
     * @access     public
     * @throws     HTML_PROGRESS2_ERROR_INVALID_CALLBACK,
     *             HTML_PROGRESS2_ERROR_INVALID_INPUT
     * @see        addListener()
     */
    function removeListener($callback, $nName = EVENT_DISPATCHER_GLOBAL)
    {
        if (!is_callable($callback)) {
            return $this->raiseError(HTML_PROGRESS2_ERROR_INVALID_CALLBACK, 'exception',
                array('var' => '$callback',
                      'element' => 'valid Class-Method/Function',
                      'was' => 'callback',
                      'paramnum' => 1));

        } elseif (!is_string($nName)) {
            return $this->raiseError(HTML_PROGRESS2_ERROR_INVALID_INPUT, 'exception',
                array('var' => '$nName',
                      'was' => gettype($nName),
                      'expected' => 'string',
                      'paramnum' => 2));
        }

        $result = $this->dispatcher->removeObserver($callback, $nName);

        if ($result) {
            $this->_observerCount--;
            if ($this->_observerCount == 0) {
                unset($this->dispatcher);
            }
        }
        return $result;
    }

    /**
     * Register an external AJAX server to use for progress bar polling.
     *
     * Until version 2.2.0 Progress2 has only COMET (streaming) ability. With
     * first alpha of 2.3.0 version, Progress2 has now AJAX (polling) ability.
     * Use PEAR::HTML_AJAX package as backend.
     *
     * @param      string    $serverUrl     the url the client should be making a request to
     * @param      array     $stub          (optional) list of proxy definition for
     * @param      array     $client        (optional) list of client libraries to use
     *
     * @return     void
     * @since      2.3.0a1
     * @access     public
     * @throws     HTML_PROGRESS2_ERROR_INVALID_INPUT,
     *             HTML_PROGRESS2_ERROR_INVALID_RESOURCE
     * @see        setupAJAX()
     */
    function registerAJAX($serverUrl, $stub = array(), $client = array('all'))
    {
        if (!is_string($serverUrl)) {
            return $this->raiseError(HTML_PROGRESS2_ERROR_INVALID_INPUT, 'exception',
                array('var' => '$serverUrl',
                      'was' => gettype($serverUrl),
                      'expected' => 'string',
                      'paramnum' => 1));

        } elseif (!HTML_Progress2::fileExists($serverUrl)) {
            return $this->raiseError(HTML_PROGRESS2_ERROR_INVALID_RESOURCE, 'error',
                array('var' => '$serverUrl',
                      'resource' => $serverUrl,
                      'expected' => 'AJAX server defined',
                      'paramnum' => 1));

        } elseif (!is_array($stub)) {
            return $this->raiseError(HTML_PROGRESS2_ERROR_INVALID_INPUT, 'exception',
                array('var' => '$stub',
                      'was' => gettype($stub),
                      'expected' => 'array',
                      'paramnum' => 2));

        } elseif (!is_array($client)) {
            return $this->raiseError(HTML_PROGRESS2_ERROR_INVALID_INPUT, 'exception',
                array('var' => '$client',
                      'was' => gettype($client),
                      'expected' => 'array',
                      'paramnum' => 3));

        } elseif (count($client) == 0) {
            return $this->raiseError(HTML_PROGRESS2_ERROR_INVALID_INPUT, 'error',
                array('var' => '$client',
                      'was' => 'empty array',
                      'expected' => 'at least one client defined',
                      'paramnum' => 3));
        }

        $this->ajax = array('serverUrl' => $serverUrl,
            'client' => $client, 'stub' => $stub);
    }

    /**
     * Include all needed libraries, stubs, and set defaultServer
     *
     * @param      string    $serializer    (optional) What encoding you are going to use
     *                                      for serializing/unserializing data
     *
     * @return     string
     * @since      2.3.0a2
     * @access     public
     * @throws     HTML_PROGRESS2_ERROR_INVALID_INPUT
     * @see        registerAJAX()
     */
    function setupAJAX($serializer = null)
    {
        if (isset($serializer) && !is_string($serializer)) {
            return $this->raiseError(HTML_PROGRESS2_ERROR_INVALID_INPUT, 'exception',
                array('var' => '$serializer',
                      'was' => gettype($serializer),
                      'expected' => 'string',
                      'paramnum' => 1));
        }

        require_once 'HTML/AJAX/Helper.php';

        // auto-register default AJAX values (server, client)
        if (count($this->ajax) == 0) {
            $this->ajax = array('serverUrl' => 'server.php',
                'client' => array('all'), 'stub' => array()
                );
        }
        $ajaxHelper = new HTML_AJAX_Helper();
        $ajaxHelper->serverUrl = $this->ajax['serverUrl'];
        $ajaxHelper->jsLibraries = $this->ajax['client'];
        $ajaxHelper->stubs = $this->ajax['stub'];

        $ret = $ajaxHelper->setupAJAX();
        $setting = '';
        if ($this->cell['class'] != 'cell%s') {
            $setting .= PHP_EOL . 'HTML_Progress2.cellClass = ' .
                $ajaxHelper->escape($this->cell['class']) . ';' ;
        }
        if ($this->cellCount != 10) {
            $setting .= PHP_EOL . 'HTML_Progress2.cellCount = ' . $this->cellCount . ';';
        }
        if ($this->minimum != 0) {
            $setting .= PHP_EOL . 'HTML_Progress2.minimum = ' . $this->minimum . ';';
        }
        if ($this->maximum != 100) {
            $setting .= PHP_EOL . 'HTML_Progress2.maximum = ' . $this->maximum . ';';
        }
        if (isset($serializer) && $serializer != 'JSON') {
            $setting .= PHP_EOL . 'HTML_Progress2.defaultEncoding = ' .
                $ajaxHelper->escape($serializer) . ';';
        }
        $setting .= PHP_EOL;
        $ret .= $ajaxHelper->encloseInScript(PHP_EOL . '//<![CDATA[' . $setting .
            '//]]>' . PHP_EOL);
        return $ret;
    }

    /**
     * Register an external AFLAX server to upload file with a progress meter.
     *
     * Pure PHP solution of upload file with a progress bar is only possible since PHP 5.2.0
     * AFLAX stands for Asynchronous Flash and XML provides an upload file solution
     * with integration of progress feedback.
     *
     * @param      string    $serverAflaxUrl     the url of the Adobe Flash "aflax.swf" resource
     * @param      string    $serverUploadUri    the uri of the php script to handle uploaded files
     * @param      array     $callback          (optional) list of event handler for browse file dialog box
     * @param      array     $extension         (optional) list of file types for browse file dialog box
     *
     * @return     void
     * @since      2.3.0a3
     * @access     public
     * @throws     HTML_PROGRESS2_ERROR_INVALID_INPUT,
     *             HTML_PROGRESS2_ERROR_INVALID_RESOURCE
     * @see        setupAFLAX()
     */
    function registerAFLAX($serverAflaxUrl, $serverUploadUri,
                           $callback = array(), $extension = array())
    {
        if (!is_string($serverAflaxUrl)) {
            return $this->raiseError(HTML_PROGRESS2_ERROR_INVALID_INPUT, 'exception',
                array('var' => '$serverAflaxUrl',
                      'was' => gettype($serverAflaxUrl),
                      'expected' => 'string',
                      'paramnum' => 1));

        } elseif (!HTML_Progress2::fileExists($serverAflaxUrl)) {
            return $this->raiseError(HTML_PROGRESS2_ERROR_INVALID_RESOURCE, 'error',
                array('var' => '$serverAflaxUrl',
                      'resource' => $serverAflaxUrl,
                      'expected' => 'AFLAX server available',
                      'paramnum' => 1));

        } elseif (!is_string($serverUploadUri)) {
            return $this->raiseError(HTML_PROGRESS2_ERROR_INVALID_INPUT, 'exception',
                array('var' => '$serverUploadUri',
                      'was' => gettype($serverUploadUri),
                      'expected' => 'string',
                      'paramnum' => 2));

        } elseif (!HTML_Progress2::fileExists($serverUploadUri)) {
            return $this->raiseError(HTML_PROGRESS2_ERROR_INVALID_RESOURCE, 'error',
                array('var' => '$serverUploadUri',
                      'resource' => $serverUploadUri,
                      'expected' => 'Upload script handler available',
                      'paramnum' => 2));

        } elseif (!is_array($callback)) {
            return $this->raiseError(HTML_PROGRESS2_ERROR_INVALID_INPUT, 'exception',
                array('var' => '$callback',
                      'was' => gettype($callback),
                      'expected' => 'array',
                      'paramnum' => 3));

        } elseif (!is_array($extension)) {
            return $this->raiseError(HTML_PROGRESS2_ERROR_INVALID_INPUT, 'exception',
                array('var' => '$extension',
                      'was' => gettype($extension),
                      'expected' => 'array',
                      'paramnum' => 4));
        }

        $this->aflax = array('swf' => $serverAflaxUrl,
            'php' => 'http://' . $_SERVER['HTTP_HOST'] .
                dirname($_SERVER['PHP_SELF']) . '/' . $serverUploadUri,
            'fext' => $extension, 'jscb' => $callback
            );
    }

    /**
     * Include all needed JS libraries
     *
     * @param      boolean   $raw           (optional) html output with script tags or just JS links
     * @param      string    $path          (optional) directory, with no trailing slash,
     *                                      where to get HTML_Progress2_AFLAX.js and ajax.js files
     *
     * @return     string
     * @since      2.3.0a3
     * @access     public
     * @throws     HTML_PROGRESS2_ERROR_INVALID_INPUT,
     *             HTML_PROGRESS2_ERROR_INVALID_RESOURCE
     * @see        registerAFLAX()
     */
    function setupAFLAX($raw = false, $path = null)
    {
        if (!is_bool($raw)) {
            return $this->raiseError(HTML_PROGRESS2_ERROR_INVALID_INPUT, 'exception',
                array('var' => '$raw',
                      'was' => gettype($raw),
                      'expected' => 'boolean',
                      'paramnum' => 1));

        } elseif (isset($path)) {
            if (!is_string($path)) {
                return $this->raiseError(HTML_PROGRESS2_ERROR_INVALID_INPUT, 'exception',
                    array('var' => '$path',
                          'was' => gettype($path),
                          'expected' => 'string',
                          'paramnum' => 2));

            } elseif (!is_dir($path)) {
                return $this->raiseError(HTML_PROGRESS2_ERROR_INVALID_RESOURCE, 'error',
                    array('var' => '$path',
                          'resource' => $path,
                          'expected' => 'directory',
                          'paramnum' => 2));

            } elseif (!file_exists($js = $path . DIRECTORY_SEPARATOR . 'HTML_Progress2_AFLAX.js')) {
                return $this->raiseError(HTML_PROGRESS2_ERROR_INVALID_RESOURCE, 'error',
                    array('var' => '$path',
                          'resource' => $js,
                          'expected' => 'directory with valid JS AFLAX handler',
                          'paramnum' => 2));
            }
        }

        if (!$raw) {
            $js  = '<script type="text/javascript" src="' . $path . '/aflax.js'
                . '"></script>' . PHP_EOL;
            $js .= '<script type="text/javascript" src="' . $path . '/HTML_Progress2_AFLAX.js'
                . '"></script>' . PHP_EOL;

        } else {
            if (isset($path)) {
                $js = $path;
            } else {
                $js = '@data_dir@' . DIRECTORY_SEPARATOR . '@package_name@';

                if (strpos($js, '@'.'data_dir@') === 0) {
                    $js = dirname(__FILE__);
                }
            }

            $js = '<script type="text/javascript">'
                . PHP_EOL . '//<![CDATA['
                . PHP_EOL . file_get_contents($js . DIRECTORY_SEPARATOR . 'aflax.js')
                . PHP_EOL . file_get_contents($js . DIRECTORY_SEPARATOR . 'HTML_Progress2_AFLAX.js')
                . PHP_EOL . '//]]>'
                . PHP_EOL . '</script>'
                . PHP_EOL;
        }

        // auto-register default AFLAX values
        if (count($this->aflax) == 0) {
            $this->aflax = array('swf' => 'aflax.swf',
                'php' => 'http://' . $_SERVER['HTTP_HOST'] . dirname($_SERVER['PHP_SELF']) . '/upload.php'
                );
        }
        // default file types you can select on browse file dialog box
        if (count($this->aflax['fext']) == 0) {
            $extension = array(
                array("Archives (*.zip, *.tar, *.tar.gz)", "*.zip; *.tar; *.tar.gz"),
                array("Images (*.jpg, *.jpeg, *.gif, *.png)", "*.jpg; *.jpeg; *.gif; *.png")
                );
            $this->aflax['fext'] = $extension;
        }
        // default event handler JS function on browse file dialog box
        if (count($this->aflax['jscb']) == 0) {
            $callback = array(
                'HTML_Progress2_AFLAX_Select',
                'HTML_Progress2_AFLAX_Progress',
                'HTML_Progress2_AFLAX_Complete',
                'HTML_Progress2_AFLAX_HTTPError',
                'HTML_Progress2_AFLAX_SecurityError',
                'HTML_Progress2_AFLAX_IOError'
                );
            $this->aflax['jscb'] = $callback;
        }

        $js .= '<script type="text/javascript">'
            . PHP_EOL . '//<![CDATA['
            . PHP_EOL . "HTML_Progress2.widgetId = '" . $this->ident . "';"
            . PHP_EOL . 'var aflax = new AFLAX("' . $this->aflax['swf'] . '");'
            . PHP_EOL . "var HTML_Progress2_AFLAX_uri = '" . $this->aflax['php'] . "';";

        $max = count($this->aflax['fext']);
        for ($i = 0; $i < $max; $i++) {
            $js .= PHP_EOL . "HTML_Progress2_AFLAX_fileTypes[$i] = new Array('"
                . $this->aflax['fext'][$i][0] . "', '"
                . $this->aflax['fext'][$i][1] . "');";
        }

        $max = count($this->aflax['jscb']);
        for ($i = 0; $i < $max; $i++) {
            $js .= PHP_EOL . "HTML_Progress2_AFLAX_eventCallback[$i] = '" . $this->aflax['jscb'][$i] . "';";
        }

        $js .= ''
            . PHP_EOL . '//]]>'
            . PHP_EOL . '</script>'
            . PHP_EOL;

        return $js;
    }

    /**
     * Checks whether the file exists in the include path
     *
     * Method used to check if a file (Ajax engine driver) is available
     * and readable.
     *
     * @param      string    $fileName      file name
     *
     * @return     bool
     * @since      2.3.0a1
     * @access     protected
     * @static
     */
    function fileExists($fileName)
    {
        foreach (explode(PATH_SEPARATOR, get_include_path()) as $path) {
            if (file_exists($path . DIRECTORY_SEPARATOR . $fileName) &&
                  is_readable($path . DIRECTORY_SEPARATOR . $fileName)) {
                return true;
            }
        }
        return false;
    }

    /**
     * Refresh the progress meter display.
     *
     * @param      integer   $value         new value of the progress meter
     *
     * @return     void
     * @since      2.0.0RC2
     * @access     private
     * @see        moveStep(), moveNext()
     */
    function _refreshDisplay($value)
    {
        static $determinate;

        foreach ($this->label as $name => $data) {
            switch($data['type']) {
            case HTML_PROGRESS2_LABEL_STEP:
                if (!$this->indeterminate) {
                    $this->_changeLabelText($name, intval(ceil($value / $this->increment))
                                                 . '/'
                                                 . intval(ceil($this->maximum / $this->increment))
                    );
                }
                break;
            case HTML_PROGRESS2_LABEL_PERCENT:
                if (!$this->indeterminate) {
                    $this->_changeLabelText($name, $this->getPercentComplete(false) . '%');
                }
                break;
            case HTML_PROGRESS2_LABEL_CROSSBAR:
                $this->_changeCrossItem($name);
                break;
            }
        }

        $bar  = ob_get_clean();

        if ($this->cellCount > 0) {

            if ($this->indeterminate) {
                if (isset($determinate)) {
                    $determinate++;
                    $progress = $determinate;
                } else {
                    $progress = $determinate = 1;
                }
            } else {
                $progress = (($this->value - $this->minimum) * $this->cellCount)
                          / ($this->maximum - $this->minimum);
                $determinate = 0;
            }

            $bar .= '<script type="text/javascript">'
                 .  'HTML_Progress2.refresh'
                 .  '("' . $this->ident . '",'
                 .  intval($progress) . ',' . $determinate
                 .  ');'
                 .  '</script>';

        } else {

            $position = $this->_computePosition();

            $orient = $this->orientation;
            $cssText = '';
            if ($orient == HTML_PROGRESS2_BAR_HORIZONTAL) {
                if ($this->fillWay == 'reverse') {
                    $cssText .= 'left:' . $position['left'] . 'px;';
                }
                $cssText .= 'width:' . $position['width'] . 'px;';
            }
            if ($orient == HTML_PROGRESS2_BAR_VERTICAL) {
                if ($this->fillWay == 'natural') {
                    $cssText .= 'top:' . $position['top'] . 'px;';
                }
                $cssText .= 'height:' . $position['height'] . 'px;';
            }
            $bar .= $this->_changeElementStyle($cssText);
        }
        echo $bar . PHP_EOL;
        ob_start();
    }

    /**
     * Returns a horizontal progress bar structure as HTML.
     *
     * @return     string
     * @since      2.0.0
     * @access     private
     */
    function _getProgressHbar_toHtml()
    {
        $tabs = $this->_getTabs();
        $tab = $this->_getTab();
        $way_natural = ($this->fillWay == 'natural');
        $cellAttr = $this->getCellAttributes();
        $cellCls = sprintf($cellAttr['class'], $this->ident);
        $html = '';

        if ($way_natural) {
            $pos = $cellAttr['spacing'];
            for ($i = 0; $i < $this->cellCount; $i++) {
                $html .= $tabs . $tab
                      .  '<div id="pcel' . $i . $this->ident . '"'
                      .  ' class="' . $cellCls . 'I"'
                      .  ' style="position:absolute;'
                      .  'left:' . $pos . 'px;'
                      .  'top:' . $cellAttr['spacing'] . 'px;';
                if (isset($cellAttr[$i])) {
                    $html .= 'color:' . $cellAttr[$i]['color'] . ';';
                }
                $html .= '"></div>' . PHP_EOL;

                $pos += ($cellAttr['width'] + $cellAttr['spacing']);
            }
        } else {
            $pos = $cellAttr['spacing'];
            for ($i = $this->cellCount - 1; $i >= 0; $i--) {
                $html .= $tabs . $tab
                      .  '<div id="pcel' . $i . $this->ident . '"'
                      .  ' class="' . $cellCls . 'I"'
                      .  ' style="position:absolute;'
                      .  'left:' . $pos . 'px;'
                      .  'top:' . $cellAttr['spacing'] . 'px;';
                if (isset($cellAttr[$i])) {
                    $html .= 'color:' . $cellAttr[$i]['color'] . ';';
                }
                $html .= '"></div>' . PHP_EOL;

                $pos += ($cellAttr['width'] + $cellAttr['spacing']);
            }
        }
        return $html;
    }

    /**
     * Returns a vertical progress bar structure as HTML.
     *
     * @return     string
     * @since      2.0.0
     * @access     private
     */
    function _getProgressVbar_toHtml()
    {
        $tabs = $this->_getTabs();
        $tab = $this->_getTab();
        $way_natural = ($this->fillWay == 'natural');
        $cellAttr = $this->getCellAttributes();
        $cellCls = sprintf($cellAttr['class'], $this->ident);
        $html = '';

        if ($way_natural) {
            $pos = $cellAttr['spacing'];
            for ($i = $this->cellCount - 1; $i >= 0; $i--) {
                $html .= $tabs . $tab
                      .  '<div id="pcel' . $i . $this->ident . '"'
                      .  ' class="' . $cellCls . 'I"'
                      .  ' style="position:absolute;'
                      .  'left:' . $cellAttr['spacing'] . 'px;'
                      .  'top:' . $pos . 'px;';
                if (isset($cellAttr[$i])) {
                    $html .= 'color:' . $cellAttr[$i]['color'] . ';';
                }
                $html .= '"></div>' . PHP_EOL;

                $pos += ($cellAttr['height'] + $cellAttr['spacing']);
            }
        } else {
            $pos = $cellAttr['spacing'];
            for ($i = 0; $i < $this->cellCount; $i++) {
                $html .= $tabs . $tab
                      .  '<div id="pcel' . $i . $this->ident . '"'
                      .  ' class="' . $cellCls . 'I"'
                      .  ' style="position:absolute;'
                      .  'left:' . $cellAttr['spacing'] . 'px;'
                      .  'top:' . $pos . 'px;';
                if (isset($cellAttr[$i])) {
                    $html .= 'color:' . $cellAttr[$i]['color'] . ';';
                }
                $html .= '"></div>' . PHP_EOL;

                $pos += ($cellAttr['height'] + $cellAttr['spacing']);
            }
        }
        return $html;
    }

    /**
     * Returns a polygonal progress structure as HTML.
     *
     * @return     string
     * @since      2.0.0
     * @access     private
     */
    function _getProgressPolygonal_toHtml()
    {
        $tabs = $this->_getTabs();
        $tab = $this->_getTab();
        $way_natural = ($this->fillWay == 'natural');
        $cellAttr = $this->getCellAttributes();
        $cellCls = sprintf($cellAttr['class'], $this->ident);
        $coord = $this->getCellCoordinates();
        $html = '';

        if ($way_natural) {
            for ($i = 0; $i < $this->cellCount; $i++) {
                $top  = $coord[$i][0] * $cellAttr['width'];
                $left = $coord[$i][1] * $cellAttr['height'];
                $html .= $tabs . $tab
                      .  '<div id="pcel' . $i . $this->ident . '"'
                      .  ' class="' . $cellCls . 'I"'
                      .  ' style="position:absolute;'
                      .  'left:' . $left . 'px;'
                      .  'top:' . $top . 'px;';
                if (isset($cellAttr[$i])) {
                    $html .= 'color:' . $cellAttr[$i]['color'] . ';';
                }
                $html .= '"></div>' . PHP_EOL;
            }
        } else {
            $c = count($coord) - 1;
            for ($i = 0; $i < $this->cellCount; $i++) {
                $top  = $coord[$c-$i][0] * $cellAttr['width'];
                $left = $coord[$c-$i][1] * $cellAttr['height'];
                $html .= $tabs . $tab
                      .  '<div id="pcel' . $i . $this->ident . '"'
                      .  ' class="' . $cellCls . 'I"'
                      .  ' style="position:absolute;'
                      .  'left:' . $left . 'px;'
                      .  'top:' . $top . 'px;';
                if (isset($cellAttr[$i])) {
                    $html .= 'color:' . $cellAttr[$i]['color'] . ';';
                }
                $html .= '"></div>' . PHP_EOL;
            }
        }

        return $html;
    }

    /**
     * Returns a circle progress structure as HTML.
     *
     * @return     string
     * @since      2.0.0
     * @access     private
     */
    function _getProgressCircle_toHtml()
    {
        $tabs = $this->_getTabs();
        $tab = $this->_getTab();
        $way_natural = ($this->fillWay == 'natural');
        $cellAttr = $this->getCellAttributes();
        $cellCls = sprintf($cellAttr['class'], $this->ident);
        $html = '';

        if ($way_natural) {
            for ($i = 0; $i < $this->cellCount; $i++) {
                $html .= $tabs . $tab
                      .  '<div id="pcel' . $i . $this->ident . '"'
                      .  ' class="' . $cellCls . 'I"'
                      .  ' style="position:absolute;left:0;top:0;'
                      .  '"><img src="' . $cellAttr[$i+1]['background-image']
                      .  '" border="0" alt="" />'
                      .  '</div>'
                      .  PHP_EOL;
            }
        } else {
            for ($i = 0; $i < $this->cellCount; $i++) {
                $html .= $tabs . $tab
                      .  '<div id="pcel' . $i . $this->ident . '"'
                      .  ' class="' . $cellCls . 'I"'
                      .  ' style="position:absolute;left:0;top:0;'
                      .  '"><img src="' . $cellAttr[$i+1]['background-image']
                      .  '" border="0" alt="" />'
                      .  '</div>'
                      .  PHP_EOL;
            }
        }
        return $html;
    }

    /**
     * Computes all coordinates of a standard polygon (square or rectangle).
     *
     * @param      integer   $w             Polygon width
     * @param      integer   $h             Polygon height
     *
     * @return     array
     * @since      2.0.0
     * @access     private
     * @see        setCellCoordinates()
     */
    function _computeCoordinates($w, $h)
    {
        $coord = array();

        for ($y=0; $y<$h; $y++) {
            if ($y == 0) {
                // creates top side line
                for ($x=0; $x<$w; $x++) {
                    $coord[] = array($y, $x);
                }
            } elseif ($y == ($h-1)) {
                // creates bottom side line
                for ($x=($w-1); $x>0; $x--) {
                    $coord[] = array($y, $x);
                }
                // creates left side line
                for ($i=($h-1); $i>0; $i--) {
                    $coord[] = array($i, 0);
                }
            } else {
                // creates right side line
                $coord[] = array($y, $w - 1);
            }
        }
        return $coord;
    }

    /**
     * Updates the new size of progress bar, depending of cell size, cell count
     * and border width.
     *
     * @since      2.0.0
     * @access     private
     * @see        setOrientation(), setCellCount(), setCellAttributes(),
     *             setBorderAttributes()
     */
    function _updateProgressSize()
    {
        if ($this->cellCount == 0) {
            return;
        }

        $cell_width   = $this->cell['width'];
        $cell_height  = $this->cell['height'];
        $cell_spacing = $this->cell['spacing'];

        $border_width = $this->border['width'];

        $cell_count = $this->cellCount;

        if ($this->orientation == HTML_PROGRESS2_BAR_HORIZONTAL) {
            $w = ($cell_count * ($cell_width + $cell_spacing)) + $cell_spacing;
            $h = $cell_height + (2 * $cell_spacing);
        }
        if ($this->orientation == HTML_PROGRESS2_BAR_VERTICAL) {
            $w  = $cell_width + (2 * $cell_spacing);
            $h  = ($cell_count * ($cell_height + $cell_spacing)) + $cell_spacing;
        }
        if ($this->orientation == HTML_PROGRESS2_POLYGONAL) {
            $w  = $cell_width * $this->_xgrid;
            $h  = $cell_height * $this->_ygrid;
        }
        if ($this->orientation == HTML_PROGRESS2_CIRCLE) {
            $w  = $cell_width;
            $h  = $cell_height;
        }

        $attr = array ('width' => $w, 'height' => $h);

        $this->_updateAttrArray($this->_progress, $attr);
    }

    /**
     * Calculate the new position in pixel of the progress bar value.
     *
     * @return     void
     * @since      2.0.0
     * @access     private
     */
    function _computePosition()
    {
        $orient = $this->orientation;
        $progressAttr = $this->getProgressAttributes();
        $min = $this->minimum;
        $max = $this->maximum;
        $step = $this->value;
        $padding = 0;

        if ($orient == HTML_PROGRESS2_BAR_HORIZONTAL) {
            if ($this->fillWay == 'natural') {
                $direction = 'right';
            } else {
                $direction = 'left';
            }
        } else {
            if ($this->fillWay == 'natural') {
                $direction = 'up';
            } else {
                $direction = 'down';
            }
        }

        switch ($direction) {
            case 'right':
            case 'left':
                $bar = $progressAttr['width'];
                break;
            case 'down':
            case 'up':
                $bar = $progressAttr['height'];
                break;
        }
        $pixel = round(($step - $min) * ($bar - ($padding * 2)) / ($max - $min));
        if ($step <= $min) {
            $pixel = 0;
        }
        if ($step >= $max) {
            $pixel = $bar - ($padding * 2);
        }

        switch ($direction) {
            case 'right':
                $position['left'] = $padding;
                $position['top'] = $padding;
                $position['width'] = $pixel;
                $position['height'] = $progressAttr['height'] - ($padding * 2);
                break;
            case 'left':
                $position['left'] = $progressAttr['width'] - $padding - $pixel;
                $position['top'] = $padding;
                $position['width'] = $pixel;
                $position['height'] = $progressAttr['height'] - ($padding * 2);
                break;
            case 'down':
                $position['left'] = $padding;
                $position['top'] = $padding;
                $position['width'] = $progressAttr['width'] - ($padding * 2);
                $position['height'] = $pixel;
                break;
            case 'up':
                $position['left'] = $padding;
                $position['top'] = $progressAttr['height'] - $padding - $pixel;
                $position['width'] = $progressAttr['width'] - ($padding * 2);
                $position['height'] = $pixel;
                break;
        }
        return $position;
    }

    /**
     * Sends a DOM command (emulate firstChild.nodeValue) through a javascript function
     * to change label value of a progress bar's element.
     *
     * @param      string    $element       element name (label id.)
     * @param      string    $text          element value (label content)
     *
     * @return     void
     * @since      2.0.0
     * @access     private
     */
    function _changeLabelText($element, $text)
    {
        $cmd = '<script type="text/javascript">'
             . 'HTML_Progress2.setLabelText'
             . '("' . $this->ident . '","' . $element . '","' . $text . '");'
             . '</script>';

        echo $cmd;
    }

    /**
     * Sends a DOM command through a javascript function
     * to change the next frame animation of a cross bar's element.
     *
     * @param      string    $element       element name (cross id.)
     *
     * @return     string
     * @since      2.0.0
     * @access     private
     */
    function _changeCrossItem($element)
    {
        $cmd = '<script type="text/javascript">'
             . 'HTML_Progress2.setRotaryCross'
             . '("' . $this->ident . '","' . $element . '");'
             . '</script>';

        echo $cmd;
    }

    /**
     * Sends a DOM command (emulate cssText attribute) through a javascript function
     * to change styles of a progress bar's element.
     *
     * @param      string    $styles        styles of a DOM element
     *
     * @return     string
     * @since      2.0.0
     * @access     private
     */
    function _changeElementStyle($styles)
    {
        $cmd = '<script type="text/javascript">'
             . 'HTML_Progress2.setElementStyle'
             . '("' . $this->ident . '","' . $styles . '");'
             . '</script>';

        return $cmd;
    }

    /**
     * Post a new notification to all observers registered.
     * This notification occured only if a dispatcher exists. That means if
     * at least one observer was registered.
     *
     * @param      string    $event         Name of the notification handler
     * @param      array     $info          (optional) Additional information about the notification
     *
     * @return     void
     * @since      2.0.0RC2
     * @access     private
     */
    function _postNotification($event, $info = array())
    {
        if (isset($this->dispatcher)) {
            $info['sender'] = get_class($this);
            $info['time']   = microtime();
            $this->dispatcher->post($this, $event, $info);
        }
    }

    /**
     * Initialize Error Handler
     *
     * Parameter '$prefs' contains a hash of options to define the error handler.
     * You may find :
     *  'message_callback'  A callback to generate message body.
     *                      Default is:  HTML_Progress2_Error::_msgCallback()
     *  'context_callback'  A callback to generate context of error.
     *                      Default is:  HTML_Progress2_Error::getBacktrace()
     *  'push_callback'     A callback to determine whether to allow an error
     *                      to be pushed or logged.
     *                      Default is:  HTML_Progress2_Error::_handleError()
     *  'error_handler'     A callback to manage all error raised.
     *                      Default is:  HTML_Progress2::_errorHandler()
     *  'handler'           Hash of params to configure all handlers (display, file, mail ...)
     *                      There are only a display handler by default with options below:
     *
     * @param      array     $prefs         hash of params to configure error handler
     *
     * @return     void
     * @since      2.0.0
     * @access     private
     */
    function _initErrorHandler($prefs = array())
    {
        // error message mapping callback
        if (isset($prefs['message_callback']) && is_callable($prefs['message_callback'])) {
            $this->_callback_message = $prefs['message_callback'];
        } else {
            $this->_callback_message = array('HTML_Progress2_Error', '_msgCallback');
        }

        // error context mapping callback
        if (isset($prefs['context_callback']) && is_callable($prefs['context_callback'])) {
            $this->_callback_context = $prefs['context_callback'];
        } else {
            $this->_callback_context = array('HTML_Progress2_Error', 'getBacktrace');
        }

        // determine whether to allow an error to be pushed or logged
        if (isset($prefs['push_callback']) && is_callable($prefs['push_callback'])) {
            $this->_callback_push = $prefs['push_callback'];
        } else {
            $this->_callback_push = array('HTML_Progress2_Error', '_handleError');
        }

        // default error handler will use PEAR_Error
        if (isset($prefs['error_handler']) && is_callable($prefs['error_handler'])) {
            $this->_callback_errorhandler = $prefs['error_handler'];
        } else {
            $this->_callback_errorhandler = array(&$this, '_errorHandler');
        }

        // any handler-specific settings
        if (isset($prefs['handler'])) {
            $this->_errorhandler_options = $prefs['handler'];
        }
    }

    /**
     * Standard error handler that will use PEAR_Error object
     *
     * To improve performances, the PEAR.php file is included dynamically.
     * The file is so included only when an error is triggered. So, in most
     * cases, the file isn't included and perfs are much better.
     *
     * @param      integer   $code          Error code
     * @param      string    $level         Error level
     * @param      array     $params        Associative array of error parameters
     *
     * @return     PEAR_Error
     * @since      2.0.0
     * @access     private
     */
    function _errorHandler($code, $level, $params)
    {
        require_once 'HTML/Progress2/Error.php';

        $mode = call_user_func($this->_callback_push, $code, $level);

        $message = call_user_func($this->_callback_message, $code, $params);
        $userinfo['level'] = $level;

        if (isset($this->_errorhandler_options['display'])) {
            $userinfo['display'] = $this->_errorhandler_options['display'];
        } else {
            $userinfo['display'] = array();
        }
        if (isset($this->_errorhandler_options['log'])) {
            $userinfo['log'] = $this->_errorhandler_options['log'];
        } else {
            $userinfo['log'] = array();
        }

        return PEAR::raiseError($message, $code, $mode, null, $userinfo, 'HTML_Progress2_Error');
    }

    /**
     * A basic wrapper around the default PEAR_Error object.
     *
     * This method throws any internal API error that could be raised
     * due to a wrong programming. It will use your error handler system defined
     * at class construction (by first argument).
     *
     * @return     mixed
     * @since      2.0.0
     * @access     public
     * @see        _errorHandler()
     */
    function raiseError()
    {
        $args = func_get_args();
        $err = call_user_func_array($this->_callback_errorhandler, $args);
        if (is_null($err)) {
            $err = array('code' => $args[0], 'level' => $args[1], 'params' => $args[2]);
        }
        array_push($this->_errorstack, $err);
        return $err;
    }

    /**
     * Determine whether there are errors into the HTML_Progress2 stack.
     *
     * This function gave you ability to be aware of API errors presence
     * in user scripts.
     *
     * @return     integer
     * @since      2.0.0
     * @access     public
     * @see        getError(), raiseError()
     */
    function hasErrors()
    {
        return count($this->_errorstack);
    }

    /**
     * Pop an error off of the HTML_Progress2 stack.
     *
     * Get all error data (code, message, level, context) from an object (PEAR_Error, ...)
     * or a simple php array.
     *
     * @return     false|array|PEAR_Error
     * @since      2.0.0
     * @access     public
     * @see        hasErrors(), raiseError()
     */
    function getError()
    {
        return @array_shift($this->_errorstack);
    }
}
?>