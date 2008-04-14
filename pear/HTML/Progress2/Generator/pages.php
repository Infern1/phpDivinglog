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
 * @version    CVS: $Id: pages.php,v 1.6 2007/04/08 14:52:11 farell Exp $
 * @link       http://pear.php.net/package/HTML_Progress2
 * @since      File available since Release 2.0.0RC1
 */


/**
 * Common wizard presentation.
 *
 * Creates all tabs and buttons as common layout for the interactive tools:
 * HTML Progress2 Generator.
 *
 * @category   HTML
 * @package    HTML_Progress2
 * @author     Laurent Laville <pear@laurent-laville.org>
 * @copyright  2005-2007 Laurent Laville
 * @license    http://www.opensource.org/licenses/bsd-license.php  New BSD License
 * @version    CVS: $Id: pages.php,v 1.6 2007/04/08 14:52:11 farell Exp $
 * @link       http://pear.php.net/package/HTML_Progress2
 * @access     private
 * @since      Class available since Release 2.1.0
 */

class TabbedPage extends HTML_QuickForm_Page
{
    /**
     * Builds tabs of the Wizard.
     *
     * @return void
     * @since  2.1.0
     * @access public
     */
    function buildTabs()
    {
        $this->_formBuilt = true;

        // Here we get all page names in the controller
        $pages  = array();
        $myName = $current = $this->getAttribute('id');
        while (null !== ($current = $this->controller->getPrevName($current))) {
            $pages[] = $current;
        }
        $pages = array_reverse($pages);
        $pages[] = $current = $myName;
        while (null !== ($current = $this->controller->getNextName($current))) {
            $pages[] = $current;
        }
        // Here we display buttons for all pages, the current one's is disabled
        foreach ($pages as $pageName) {
            $tabs[] = $this->createElement(
                        'submit', $this->getButtonName($pageName), ucfirst($pageName),
                        array('class' => 'flat') + ($pageName == $myName? array('disabled' => 'disabled'): array())
                      );
        }
        $this->addGroup($tabs, 'tabs', null, '&nbsp;', false);
    }

    /**
     * Builds command buttons of the Wizard.
     *
     * @return void
     * @since  2.1.0
     * @access public
     */
    function buildButtons($disable = null, $commands = null)
    {
        $buttons = array('back', 'next', 'cancel', 'reset', 'dump', 'apply', 'process');
        if (isset($commands)) {
            $buttons = array_merge($buttons, $commands);
        }

        if (!isset($disable)) {
            $disable = array();
        } elseif (!isset($disable[0])) {
            $disable = array($disable);
        }

        $confirm = $attributes = array('class' => 'cmdButton');
        $confirm['onclick'] = "return(confirm('Are you sure ?'));";

        $prevnext = array();

        foreach ($buttons as $event) {
            switch ($event) {
                case 'cancel':
                    $type = 'submit';
                    $attrs = $confirm;
                    break;
                case 'reset':
                    $type = 'reset';
                    $attrs = $confirm;
                    break;
                default :
                    $type = 'submit';
                    $attrs = $attributes;
                    break;
            }
            if (in_array($event, $disable)) {
                $attrs['disabled'] = 'true';
            }
            if ($event == 'dump') {
                $dump = $this->controller->_act[$event];
                if ($dump === false) {
                    continue;
                }
                $opts = array(
                    '1' => 'Progress2 dump info',
                    '2' => 'Forms values container',
                    '3' => 'Included Files',
                    '4' => 'Declared Classes',
                    '5' => 'Declared Actions'
                    );
                $prevnext[] =&HTML_QuickForm::createElement('select', 'dumpOption', '', $opts);
            }
            $prevnext[] =&HTML_QuickForm::createElement($type, $this->getButtonName($event), ucfirst($event), HTML_Common::_getAttrString($attrs));
        }
        $this->addGroup($prevnext, 'buttons', '', '&nbsp;', false);
    }
}

/**
 *  Class for first Tab:
 *  Progress main properties
 *  @ignore
 */
class Property1 extends TabbedPage
{
    function buildForm()
    {
        $this->buildTabs();
        // tab caption
        $this->addElement('header', null, 'Progress2 Generator - Control Panel: main properties');

        $shape[] =& $this->createElement('radio', null, null, 'Horizontal', '1');
        $shape[] =& $this->createElement('radio', null, null, 'Vertical', '2');
        $this->addGroup($shape, 'shape', 'Shape:');

        $way[] =& $this->createElement('radio', null, null, 'Natural', 'natural');
        $way[] =& $this->createElement('radio', null, null, 'Reverse', 'reverse');
        $this->addGroup($way, 'way', 'Direction:');

        $autosize[] =& $this->createElement('radio', null, null, 'Yes', true);
        $autosize[] =& $this->createElement('radio', null, null, 'No', false);
        $this->addGroup($autosize, 'autosize', 'Best size:');

        $progresssize['width']   =& $this->createElement('text', 'width', 'width', array('size' => 4));
        $progresssize['height']  =& $this->createElement('text', 'height', 'height', array('size' => 4));
        $progresssize['left']    =& $this->createElement('text', 'left', 'left', array('size' => 4));
        $progresssize['top']     =& $this->createElement('text', 'top', 'top', array('size' => 4));
        $progresssize['position']=& $this->createElement('text', 'position', 'position', array('disabled' => 'true'));
        $progresssize['bgcolor'] =& $this->createElement('text', 'bgcolor', 'bgcolor', array('size' => 7));
        $this->addGroup($progresssize, 'progresssize', 'Size, position and color:', ' ');

        $this->addElement('text', 'rAnimSpeed', array('Animation speed :', '(0-1000 ; 0:fast, 1000:slow)'));
        $this->addRule('rAnimSpeed', 'Should be between 0 and 1000', 'rangelength', array(0,1000), 'client');

        // Buttons of the wizard to do the job
        $this->buildButtons(array('back','apply','process'));
    }
}

/**
 *  Class for second Tab:
 *  Cell properties
 *  @ignore
 */
class Property2 extends TabbedPage
{
    function buildForm()
    {
        $this->buildTabs();
        // tab caption
        $this->addElement('header', null, 'Progress2 Generator - Control Panel: cell properties');

        $this->addElement('text', 'cellid', 'Id mask:', array('size' => 32));
        $this->addElement('text', 'cellclass', 'CSS class:', array('size' => 32));

        $cellvalue['min'] =& $this->createElement('text', 'min', 'minimum', array('size' => 4));
        $cellvalue['max'] =& $this->createElement('text', 'max', 'maximum', array('size' => 4));
        $cellvalue['inc'] =& $this->createElement('text', 'inc', 'increment', array('size' => 4));
        $this->addGroup($cellvalue, 'cellvalue', 'Value:', ' ');

        $cellsize['width']   =& $this->createElement('text', 'width', 'width', array('size' => 4));
        $cellsize['height']  =& $this->createElement('text', 'height', 'height', array('size' => 4));
        $cellsize['spacing'] =& $this->createElement('text', 'spacing', 'spacing', array('size' => 2));
        $cellsize['count']   =& $this->createElement('text', 'count', 'count', array('size' => 2));
        $this->addGroup($cellsize, 'cellsize', 'Size:', ' ');

        $cellcolor['active']   =& $this->createElement('text', 'active', 'active', array('size' => 7));
        $cellcolor['inactive'] =& $this->createElement('text', 'inactive', 'inactive', array('size' => 7));
        $cellcolor['bgcolor']  =& $this->createElement('text', 'bgcolor', 'background', array('size' => 7));
        $this->addGroup($cellcolor, 'cellcolor', 'Color:', ' ');

        $cellfont['family'] =& $this->createElement('text', 'family', 'family', array('size' => 32));
        $cellfont['size']   =& $this->createElement('text', 'size', 'size', array('size' => 2));
        $cellfont['color']  =& $this->createElement('text', 'color', 'color', array('size' => 7));
        $this->addGroup($cellfont, 'cellfont', 'Font:', ' ');

        // Buttons of the wizard to do the job
        $this->buildButtons(array('apply','process'));
    }
}

/**
 *  Class for third Tab:
 *  Progress border properties
 *  @ignore
 */
class Property3 extends TabbedPage
{
    function buildForm()
    {
        $this->buildTabs();
        // tab caption
        $this->addElement('header', null, 'Progress2 Generator - Control Panel: border properties');

        $borderpainted[] =& $this->createElement('radio', null, null, 'Yes', true);
        $borderpainted[] =& $this->createElement('radio', null, null, 'No', false);
        $this->addGroup($borderpainted, 'borderpainted', 'Display the border:');

        $this->addElement('text', 'borderclass', 'CSS class:', array('size' => 32));

        $borderstyle['style'] =& $this->createElement('select', 'style', 'style', array('solid'=>'Solid', 'dashed'=>'Dashed', 'dotted'=>'Dotted', 'inset'=>'Inset', 'outset'=>'Outset'));
        $borderstyle['width'] =& $this->createElement('text', 'width', 'width', array('size' => 2));
        $borderstyle['color'] =& $this->createElement('text', 'color', 'color', array('size' => 7));
        $this->addGroup($borderstyle, 'borderstyle', null, ' ');

        // Buttons of the wizard to do the job
        $this->buildButtons(array('apply','process'));
    }
}

/**
 *  Class for fourth Tab:
 *  Label properties
 *  @ignore
 */
class Property4 extends TabbedPage
{
    function buildForm()
    {
        $this->buildTabs();
        // tab caption
        $this->addElement('header', null, 'Progress2 Generator - Control Panel: string properties');

        $stringpainted[] =& $this->createElement('radio', null, null, 'Yes', true);
        $stringpainted[] =& $this->createElement('radio', null, null, 'No', false);
        $this->addGroup($stringpainted, 'stringpainted', 'Render a custom string:');

        $this->addElement('text', 'stringid', 'Id:', array('size' => 32));
        $this->addElement('text', 'stringclass', 'CSS class:', array('size' => 32));
        $this->addElement('text', 'stringvalue', 'Content:', array('size' => 32));

        $stringsize['width']   =& $this->createElement('text', 'width', 'width', array('size' => 4));
        $stringsize['height']  =& $this->createElement('text', 'height', 'height', array('size' => 4));
        $stringsize['left']    =& $this->createElement('text', 'left', 'left', array('size' => 4));
        $stringsize['top']     =& $this->createElement('text', 'top', 'top', array('size' => 4));
        $stringsize['bgcolor'] =& $this->createElement('text', 'bgcolor', 'bgcolor', array('size' => 7));
        $this->addGroup($stringsize, 'stringsize', 'Size, position and color:', ' ');

        $stringvalign[] =& $this->createElement('radio', null, null, 'Left', 'left');
        $stringvalign[] =& $this->createElement('radio', null, null, 'Right', 'right');
        $stringvalign[] =& $this->createElement('radio', null, null, 'Top', 'top');
        $stringvalign[] =& $this->createElement('radio', null, null, 'Bottom', 'bottom');
        $this->addGroup($stringvalign, 'stringvalign', 'Vertical alignment:');

        $stringalign[] =& $this->createElement('radio', null, null, 'Left', 'left');
        $stringalign[] =& $this->createElement('radio', null, null, 'Right', 'right');
        $stringalign[] =& $this->createElement('radio', null, null, 'Center', 'center');
        $this->addGroup($stringalign, 'stringalign', 'Horizontal alignment:');

        $stringfont['family'] =& $this->createElement('text', 'family', 'family', array('size' => 40));
        $stringfont['size']   =& $this->createElement('text', 'size', 'size', array('size' => 2));
        $stringfont['color']  =& $this->createElement('text', 'color', 'color', array('size' => 7));
        $this->addGroup($stringfont, 'stringfont', 'Font:', ' ');

        $stringweight[] =& $this->createElement('radio', null, null, 'normal', 'normal');
        $stringweight[] =& $this->createElement('radio', null, null, 'Bold', 'bold');
        $this->addGroup($stringweight, 'stringweight', 'Font weight:');

        // Buttons of the wizard to do the job
        $this->buildButtons(array('apply','process'));
    }
}

/**
 *  Class for fifth Tab:
 *  Show a preview of your progress bar design.
 *  @ignore
 */
class Preview extends TabbedPage
{
    function buildForm()
    {
        $this->buildTabs();
        // tab caption
        $this->addElement('header', null, 'Progress2 Generator - Control Panel: run demo');

        $this->addElement('static', 'progressBar', 'Your progress meter looks like:');

        // Buttons of the wizard to do the job
        $this->buildButtons(array('reset','process'));
    }
}

/**
 *  Class for sixth Tab:
 *  Save PHP and/or CSS code
 *  @ignore
 */
class Save extends TabbedPage
{
    function buildForm()
    {
        $this->buildTabs();
        // tab caption
        $this->addElement('header', null, 'Progress2 Generator - Control Panel: save PHP/CSS code');

        $code[] =& $this->createElement('checkbox', 'P', null, 'PHP');
        $code[] =& $this->createElement('checkbox', 'C', null, 'CSS');
        $this->addGroup($code, 'phpcss', 'PHP and/or StyleSheet source code:');

        // Buttons of the wizard to do the job
        $this->buildButtons(array('next','apply'));
    }
}
?>