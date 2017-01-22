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
 * It is Proof of Identity helper class
 */
class IdentityproofHelper
{
    protected static $extension = 'com_identityproof';

    /**
     * Configure the Linkbar.
     *
     * @param    string  $vName  The name of the active view.
     *
     * @since    1.6
     */
    public static function addSubmenu($vName = 'dashboard')
    {
        JHtmlSidebar::addEntry(
            JText::_('COM_IDENTITYPROOF_DASHBOARD'),
            'index.php?option=' . self::$extension . '&view=dashboard',
            $vName === 'dashboard'
        );

        JHtmlSidebar::addEntry(
            JText::_('COM_IDENTITYPROOF_FILES'),
            'index.php?option=' . self::$extension . '&view=files',
            $vName === 'records'
        );

        JHtmlSidebar::addEntry(
            JText::_('COM_IDENTITYPROOF_USERS'),
            'index.php?option=' . self::$extension . '&view=users',
            $vName === 'users'
        );

        JHtmlSidebar::addEntry(
            JText::_('COM_IDENTITYPROOF_PLUGINS'),
            'index.php?option=com_plugins&view=plugins&filter_search=' . rawurlencode('proof'),
            $vName === 'plugins'
        );
    }
}
