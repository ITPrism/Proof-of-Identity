<?php
/**
 * @package      ProofOfIdentity
 * @subpackage   Components
 * @author       Todor Iliev
 * @copyright    Copyright (C) 2016 Todor Iliev <todor@itprism.com>. All rights reserved.
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
abstract class JHtmlIdentityproofBackend
{
    public static function state($i, $value, $prefix, $checkbox = 'cb')
    {
        JHtml::_('bootstrap.tooltip');

        if (!$value) { // Disapproved
            $task  = $prefix . 'verify';
            $title = 'COM_IDENTITYPROOF_VERIFY_USER';
            $class = 'ban-circle';
        } else {
            $task  = $prefix . 'unverify';
            $title = 'COM_IDENTITYPROOF_UNVERIFY_USER';
            $class = 'ok';
        }

        $html[] = '<a class="btn btn-micro hasTooltip" href="javascript:void(0);" onclick="return listItemTask(\'' . $checkbox . $i . '\',\'' . $task . '\')" title="' . addslashes(htmlspecialchars(JText::_($title), ENT_COMPAT, 'UTF-8')) . '">';
        $html[] = '<i class="icon-' . $class . '"></i>';
        $html[] = '</a>';

        return implode("\n", $html);
    }

    public static function socialProfiles($socialProfiles, $id)
    {
        $html = array();

        if (array_key_exists($id, $socialProfiles)) {
            $html[] = '<a class="btn btn-micro hasTooltip js-socialprofiles" href="javascript:void(0);" data-id="'.$id.'" title="' . htmlspecialchars(JText::_('COM_IDENTITYPROOF_REVIEW_SOCIAL_PROFILES'), ENT_COMPAT, 'UTF-8') . '">';
            $html[] = '<i class="icon-eye"></i>';
            $html[] = '</a>';
        }

        return implode("\n", $html);
    }

    public static function profile($socialProfile)
    {
        $html = array();

        if ($socialProfile->getLink()) {

            if ($socialProfile->getPicture()) {
                $html[] = '<a href="'.$socialProfile->getLink().'" target="_blank">';
                $html[] = '<img src="'.$socialProfile->getPicture().'"/>';
                $html[] = '</a>';
            }

            $html[] = '<a href="'.$socialProfile->getLink().'" target="_blank">';
            $html[] = $socialProfile->getName();
            $html[] = '</a>';
        } else {

            if ($socialProfile->getPicture()) {
                $html[] = '<img src="'.$socialProfile->getPicture().'"/>';
            }

            $html[] = $socialProfile->getName();
        }

        return implode("\n", $html);
    }

    public static function website($url)
    {
        $html = array();

        if (!$url) {
            $html[] =  '--';
        } else {
            $html[] = '<a href="'.$url.'" target="_blank">';
            $html[] = $url;
            $html[] = '</a>';
        }

        return implode("\n", $html);
    }

    public static function filestate($i, $value, $prefix, $checkbox = 'cb')
    {
        $html = array();

        switch ($value) {

            case 1: // Reviewed Successfully.
                $task  = $prefix . 'pending';
                $title = 'COM_IDENTITYPROOF_TOOLTIP_PENDING';
                $class = 'ok';
                break;

            case -2: // Trashed.
                $task  = $prefix . 'pending';
                $title = 'COM_IDENTITYPROOF_TOOLTIP_PENDING';
                $class = 'trash';
                break;

            default: // Pending
                $task  = $prefix . 'reviewed';
                $title = 'COM_IDENTITYPROOF_TOOLTIP_REVIEWED';
                $class = 'clock';
                break;
        }

        $html[] = '<a class="btn btn-micro hasTooltip" href="javascript:void(0);" onclick="return listItemTask(\'' . $checkbox . $i . '\',\'' . $task . '\')" title="' . addslashes(htmlspecialchars(JText::_($title), ENT_COMPAT, 'UTF-8')) . '">';
        $html[] = '<i class="icon-' . $class . '"></i>';
        $html[] = '</a>';

        return implode("\n", $html);
    }
}
