<?php
/**
 * @package      Identityproof
 * @subpackage   Component
 * @author       Todor Iliev
 * @copyright    Copyright (C) 2017 Todor Iliev <todor@itprism.com>. All rights reserved.
 * @license      GNU General Public License version 3 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

/**
 * Method to build Route
 *
 * @param array $query
 *
 * @return array
 */
function IdentityproofBuildRoute(&$query)
{
    $segments = array();

    // get a menu item based on Itemid or currently active
    $app  = JFactory::getApplication();
    $menu = $app->getMenu();

    // we need a menu item.  Either the one specified in the query, or the current active one if none specified
    if (empty($query['Itemid'])) {
        $menuItem      = $menu->getActive();
        $menuItemGiven = false;
    } else {
        $menuItem      = $menu->getItem($query['Itemid']);
        $menuItemGiven = true;
    }

    // Check again
    if ($menuItemGiven and isset($menuItem) and strcmp('com_identityproof', $menuItem->component) !== 0) {
        $menuItemGiven = false;
        unset($query['Itemid']);
    }

    $mView   = (empty($menuItem->query['view'])) ? null : $menuItem->query['view'];

    // If is set view and Itemid missing, we have to put the view to the segments
    if (isset($query['view'])) {
        $view = $query['view'];
    } else {
        return $segments;
    }

    // Are we dealing with a category that is attached to a menu item?
    if (($menuItem instanceof stdClass) and isset($view) and ($mView == $view)) {

        unset($query['view']);

        if (isset($query['layout'])) {
            unset($query['layout']);
        }

        return $segments;
    }

    // Views
    if (isset($view)) {

        switch ($view) {

            case 'proof':
                if (isset($query['view'])) {
                    unset($query['view']);
                }
                break;

        }

    }

    // Layout
    if (isset($query['layout'])) {
        if ($menuItemGiven and isset($menuItem->query['layout'])) {
            if ($query['layout'] === $menuItem->query['layout']) {
                unset($query['layout']);
            }
        } else {
            if ($query['layout'] === 'default') {
                unset($query['layout']);
            }
        }
    }

    $total = count($segments);

    for ($i = 0; $i < $total; $i++) {
        $segments[$i] = str_replace(':', '-', $segments[$i]);
    }

    return $segments;
}

/**
 * Method to parse Route
 *
 * @param array $segments
 *
 * @return array
 */
function IdentityproofParseRoute($segments)
{
    $total = count($segments);
    $vars = array();

    for ($i = 0; $i < $total; $i++) {
        $segments[$i] = preg_replace('/-/', ':', $segments[$i], 1);
    }

    //Get the active menu item.
    $app  = JFactory::getApplication();
    $menu = $app->getMenu();
    $item = $menu->getActive();

    // Standard routing for articles.  If we don't pick up an Itemid then we get the view from the segments
    // the first segment is the view and the last segment is the id of the details, category or payment.
    if ($item === null) {
        $vars['view']  = $segments[0];
        return $vars;
    }

    $vars['view'] = 'proof';

    return $vars;
}
