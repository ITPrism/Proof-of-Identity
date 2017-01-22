<?php
/**
 * @package      ProofOfIdentity
 * @subpackage   Component
 * @author       Todor Iliev
 * @copyright    Copyright (C) 2017 Todor Iliev <todor@itprism.com>. All rights reserved.
 * @license      GNU General Public License version 3 or later; see LICENSE.txt
 */

// no direct access
defined('_JEXEC') or die;

/**
 * Default Controller
 *
 * @package        ProofOfIdentity
 * @subpackage     Component
 */
class IdentityproofController extends JControllerLegacy
{
    public function display($cachable = false, $urlparams = array())
    {
        $app = JFactory::getApplication();
        /** @var $app JApplicationAdministrator */

        $viewName = $app->input->getCmd('view', 'dashboard');
        $app->input->set('view', $viewName);

        parent::display($cachable = false, $urlparams = array());

        return $this;
    }
}
