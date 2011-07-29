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
 * @copyright 2007-2008 Laurent Laville
 * @license   http://www.opensource.org/licenses/bsd-license.php  New BSD License
 * @version   CVS: $Id$
 * @link      http://pear.php.net/package/HTML_Progress2
 * @since     File available since Release 2.3.0RC1
 */


/**
 * Backend for AJAX uploading bar.
 *
 * @category  HTML
 * @package   HTML_Progress2
 * @author    Laurent Laville <pear@laurent-laville.org>
 * @copyright 2007-2008 Laurent Laville
 * @license   http://www.opensource.org/licenses/bsd-license.php  New BSD License
 * @version   Release: 2.4.1
 * @link      http://pear.php.net/package/HTML_Progress2
 * @since     Class available since Release 2.3.0RC1
 */

class HTML_Progress2_Upload
{
    /**
     * Backend identifier are either :
     * 'none' - if nothing else below match
     * 'apc5' - APC php extension with PHP5.2.0 or greater
     * 'upm5' - upload progress meter php extension with PHP5.2.0 or greater
     *
     * @var        string
     * @since      2.3.0RC1
     * @access     public
     */
    var $backend;

    /**
     * Formatted strings for Progress bar labels that will display upload info.
     *
     * Each conversion specification consists of a percent sign (%), followed
     * by one or more of these elements.
     *  T - for display total file size
     *  C - for display current file size uploaded
     *  P - for display percentage of file size uploaded
     *  F - for display file name to upload
     *  E - for estimate time left
     *  S - for speed limit
     *
     * @var        string
     * @since      2.3.0RC1
     * @access     public
     * @see        HTML_Progress2_Upload::sprintf()
     */
    var $format;

    /**
     * Constructor (ZE2) Summary
     *
     * @param string $format (optional) conversion specifications
     *                                  for all progress bar labels
     *
     * @since      version 2.3.0RC1 (2007-02-12)
     * @access     protected
     * @link       http://pdoru.from.ro/upload-progress-meter/  Patch for PHP 4
     */
    function __construct($format = null)
    {
        $php5 = (version_compare(phpversion(), '5.2.0', 'ge') < 0) ? false : true;

        if (extension_loaded('uploadprogress') && $php5) {
            $this->backend = 'upm5';
        } elseif (extension_loaded('apc') && $php5) {
            $this->backend = 'apc5';
        } else {
            $this->backend = 'none';
        }

        if (!is_array($format)) {
            $format = array('uplStatus' => '%C / %T (%P%)');
        }
        $this->format = $format;
    }

    /**
     * Return hash of data with current file upload information, depending of
     * backend used.
     *
     * @param string $Id upload identifier
     *
     * @return     bool|array   false if info unavailable, hash of data otherwise
     * @since      version 2.3.0RC1 (2007-02-12)
     * @access     public
     */
    function getInfo($Id)
    {
        $info = array('time_start' => 0, 'time_last' => 0,
            'speed_average' => 0, 'speed_last' => 0,
            'est_sec' => -1,
            'bytes_uploaded' => 0,  'bytes_total' => 0,
            'files_uploaded' => 0,
            'cancel_upload' => 0
            );

        switch ($this->backend) {
        case 'upm5':
            $tmp = uploadprogress_get_info($Id);
            if (is_array($tmp)) {
                $info                 = array_merge($info, $tmp);
                $info['current_file'] = $tmp['filename'];
            } else {
                $info = false;
            }
            break;
        case 'apc5':
            $tmp = apc_fetch('upload_' . $Id);
            if (is_array($tmp)) {
                $info['bytes_uploaded'] = $tmp['current'];
                $info['bytes_total']    = $tmp['total'];
                $info['files_uploaded'] = $tmp['done'];
                $info['current_file']   = $tmp['filename'];
                if (isset($tmp['cancel_upload'])) {
                    $info['cancel_upload'] = $tmp['cancel_upload'];
                }
            } else {
                $info = false;
            }
            break;
        default:
        }

        return $info;
    }

    /**
     * Return a formatted string with current upload information
     *
     * Each conversion specification consists of a percent sign (%), followed
     * by one or more of these elements.
     *  T - for display total file size
     *  C - for display current file size uploaded
     *  P - for display percentage of file size uploaded
     *  F - for display file name to upload
     *  E - for estimate time left
     *  S - for speed limit
     *
     * @param string $format conversion specification
     * @param array  $info   current upload information
     *
     * @return     bool|string   false on error, formatted string with current info
     * @since      version 2.3.0RC1 (2007-02-12)
     * @access     public
     */
    function sprintf($format, $info)
    {
        if (!is_array($info)) {
            return false;
        }

        $pos = strpos($format, '%T');
        if ($pos !== false) {
            $format = str_replace('%T', $this->formatBytes($info['bytes_total']),
                                  $format);
        }
        $pos = strpos($format, '%C');
        if ($pos !== false) {
            $format = str_replace('%C', $this->formatBytes($info['bytes_uploaded']),
                                  $format);
        }
        $pos = strpos($format, '%P');
        if ($pos !== false) {
            if ($info['bytes_total'] < 1) {
                $percent = 100;
            } else {
                $percent = round($info['bytes_uploaded'] /
                                 $info['bytes_total'] * 100);
            }
            $format = str_replace('%P', $percent, $format);
        }
        $pos = strpos($format, '%F');
        if ($pos !== false) {
            $format = str_replace('%F', $info['current_file'], $format);
        }
        $pos = strpos($format, '%E');
        if ($pos !== false
            && $info['est_sec'] > 0) {
            $eta    = sprintf("%02d:%02d",
                              $info['est_sec'] / 60, $info['est_sec'] % 60);
            $format = str_replace('%E', $eta, $format);
        }

        $pos = strpos($format, '%S');
        if ($pos !== false) {
            $speed  = $this->formatBytes($info['speed_average']);
            $format = str_replace('%S', $speed, $format);
        }
        return $format;
    }

    /**
     * Get the status of an upload passed in
     *
     * @param string $uplId (optional) upload identifier
     *
     * @return     array
     * @since      version 2.3.0RC1 (2007-02-12)
     * @access     public
     */
    function getStatus($uplId = null)
    {
        if (!isset($uplId)) {
            $uplId = $_GET['upload_identifier'];
        }
        $tmp = $this->getInfo($uplId);
        if (!is_array($tmp)) {
            $ret = array('percentage' => 100, 'labels' => array());
            return $ret;
        }

        $percent = $this->sprintf('%P', $tmp);
        $labels  = array();
        foreach ($this->format as $lbl => $fmt) {
            $labels[$lbl] = $this->sprintf($fmt, $tmp);
        }

        $ret = array('percentage' => $percent, 'labels' => $labels,
            'bytes_uploaded' => $tmp['bytes_uploaded'],
            'bytes_total' => $tmp['bytes_total']
        );
        return $ret;
    }

    /**
     * Function to convert bytes to something larger
     *
     * @param int $x file size in bytes
     *
     * @return     string
     * @since      version 2.3.0RC1 (2007-02-12)
     * @access     public
     */
    function formatBytes($x)
    {
        if ($x < 1024) {
            return "$x bytes";
        } elseif ($x < (1024 * 1024)) {
            return sprintf("%d KB", $x/1024);
        } else {
            return sprintf("%d MB", $x/(1024 * 1024));
        }
    }
}
?>