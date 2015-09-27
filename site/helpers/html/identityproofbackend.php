<?php
/**
 * @package      ProofOfIdentity
 * @subpackage   Components
 * @author       Todor Iliev
 * @copyright    Copyright (C) 2015 Todor Iliev <todor@itprism.com>. All rights reserved.
 * @license      GNU General Public License version 3 or later; see LICENSE.txt
 */

// no direct access
defined('_JEXEC') or die;

/**
 * ProofOfIdentity Html Helper.
 * The methods generates HTML code based on Bootstrap 2
 * because they are used on front-end.
 *
 * @package        ProofOfIdentity
 * @subpackage     Component
 * @since          1.6
 */
abstract class JHtmlIdentityProofBackend
{
    public static function state($i, $value, $prefix, $checkbox = 'cb')
    {
        JHtml::_('bootstrap.tooltip');

        if (!$value) { // Disapproved
            $task  = $prefix . "verify";
            $title = "COM_IDENTITYPROOF_VERIFY_USER";
            $class = "ban-circle";
        } else {
            $task  = $prefix . "unverify";
            $title = "COM_IDENTITYPROOF_UNVERIFY_USER";
            $class = "ok";
        }

        $html[] = '<a class="btn btn-micro hasTooltip" href="javascript:void(0);" onclick="return listItemTask(\'' . $checkbox . $i . '\',\'' . $task . '\')" title="' . addslashes(htmlspecialchars(JText::_($title), ENT_COMPAT, 'UTF-8')) . '">';
        $html[] = '<i class="icon-' . $class . '"></i>';
        $html[] = '</a>';

        return implode("\n", $html);
    }

    public static function filestate($i, $value, $prefix, $checkbox = 'cb')
    {
        $html = array();

        switch ($value) {

            case 1: // Reviewed Successfully.
                $task  = $prefix . "pending";
                $title = "COM_IDENTITYPROOF_TOOLTIP_PENDING";
                $class = "ok";
                break;

            case -2: // Trashed.
                $task  = $prefix . "pending";
                $title = "COM_IDENTITYPROOF_TOOLTIP_PENDING";
                $class = "trash";
                break;

            default: // Pending
                $task  = $prefix . "reviewed";
                $title = "COM_IDENTITYPROOF_TOOLTIP_REVIEWED";
                $class = "clock";
                break;
        }

        $html[] = '<a class="btn btn-micro hasTooltip" href="javascript:void(0);" onclick="return listItemTask(\'' . $checkbox . $i . '\',\'' . $task . '\')" title="' . addslashes(htmlspecialchars(JText::_($title), ENT_COMPAT, 'UTF-8')) . '">';
        $html[] = '<i class="icon-' . $class . '"></i>';
        $html[] = '</a>';

        return implode("\n", $html);

    }
}
