<?php
/**
 * Copyright (c) 2006-2008, Laurent Laville <pear@laurent-laville.org>
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
 * @copyright 2006-2008 Laurent Laville
 * @license   http://www.opensource.org/licenses/bsd-license.php  New BSD License
 * @version   CVS: $Id$
 * @link      http://pear.php.net/package/HTML_Progress2
 * @since     File available since Release 2.1.0
 */

require_once 'HTML/QuickForm/Action.php';

/**
 * Provides methods for dumping structured information about a variable
 *
 * @param mixed $var the variable to dump
 *
 * @return void
 */
function varDump($var)
{
    $available = HTML_Progress2_Generator::isIncludeable('Var_Dump.php');
    if ($available) {
        include_once 'Var_Dump.php';
        Var_Dump::display($var, false, array('display_mode' => 'HTML4_Table'));
    } else {
        echo '<pre style="background-color:#eee; color:#000; padding:1em;">';
        var_dump($var);
        echo '</pre>';
    }
}

/**
 * Interactive memory debugging tool.
 *
 * You can display contents of :
 * - default PEAR_PackageFileManager2 class options
 * - this frontend options
 * - all forms values, defaults and validation states
 * - the Warnings/Errors stack
 *
 * @category  HTML
 * @package   HTML_Progress2
 * @author    Laurent Laville <pear@laurent-laville.org>
 * @copyright 2006-2008 Laurent Laville
 * @license   http://www.opensource.org/licenses/bsd-license.php  New BSD License
 * @version   Release: 2.4.1
 * @link      http://pear.php.net/package/HTML_Progress2
 * @since     Class available since Release 2.1.0
 */

class ActionDump extends HTML_QuickForm_Action
{
    /**
     * Processes the request.
     *
     * @param object &$page      the current form-page
     * @param string $actionName Current action name,
     *                           as one Action object can serve multiple actions
     *
     * @return void
     * @since  version 2.1.0 (2006-08-12)
     * @access public
     */
    function perform(&$page, $actionName)
    {
        $page->isFormBuilt() or $page->buildForm();
        $page->handle('display');

        $sess =& $page->controller->container();

        $opt = $page->getSubmitValue('dumpOption');
        switch ($opt) {
        case '1':   // Progress2 dump info
            $arr = $page->controller->_progress->toArray();
            varDump($arr);
            break;
        case '2':   // Forms values container
            varDump($sess);
            break;
        case '3':   // Included files
            $includes = get_included_files();
            varDump($includes);
            break;
        case '4':   // declared classes
            $classes = get_declared_classes();
            varDump($classes);
            break;
        case '5':   // declared actions
            $actions = $page->controller->_actions;
            varDump($actions);
            break;
        }
    }
}
?>