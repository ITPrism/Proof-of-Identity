<?php
/**
 * @package      IdentityProof
 * @subpackage   Component
 * @author       Todor Iliev
 * @copyright    Copyright (C) 2015 Todor Iliev <todor@itprism.com>. All rights reserved.
 * @license      GNU General Public License version 3 or later; see LICENSE.txt
 */

// No direct access.
defined('_JEXEC') or die;

jimport("Prism.init");
jimport("IdentityProof.init");

$controller = JControllerLEgacy::getInstance('IdentityProof');
$controller->execute(JFactory::getApplication()->input->getCmd('task'));
$controller->redirect();
