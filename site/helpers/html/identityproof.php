<?php
/**
 * @package      ProofOfIdentity
 * @subpackage   Components
 * @author       Todor Iliev
 * @copyright    Copyright (C) 2015 Todor Iliev <todor@itprism.com>. All rights reserved.
 * @license      http://www.gnu.org/copyleft/gpl.html GNU/GPL
 */

// no direct access
defined('_JEXEC') or die;

/**
 * ProofOfIdentity Html Helper
 *
 * @package        ProofOfIdentity
 * @subpackage     Components
 * @since          1.6
 */
abstract class JHtmlIdentityProof
{
    /**
     * Display an icon for state.
     *
     * @param integer $value
     * @param string $iconPending
     * @param string $iconOk
     * @param string $iconWarning
     *
     * @return string
     */
    public static function state($value, $iconPending = "icon-time", $iconOk = "icon-ok-circle", $iconWarning = "icon-trash")
    {
        $html = '<button class="btn btn-mini hasTooltip" title="{TITLE}"><i class="{ICON}"></i></button>';

        switch ($value) {

            case 1: // Reviewed Successfully.
                $html = str_replace("{ICON}", $iconOk, $html);
                $html = str_replace("{TITLE}", JText::_("COM_IDENTITYPROOF_TOOLTIP_REVIEWED"), $html);
                break;

            case -2: // Warning.
                $html = str_replace("{ICON}", $iconWarning, $html);
                $html = str_replace("{TITLE}", JText::_("COM_IDENTITYPROOF_TOOLTIP_TRASHED"), $html);
                break;

            default: // Pending
                $html = str_replace("{ICON}", $iconPending, $html);
                $html = str_replace("{TITLE}", JText::_("COM_IDENTITYPROOF_TOOLTIP_PENDING"), $html);
                break;
        }

        return $html;

    }

    /**
     * Display an icon for note.
     *
     * @param integer $fileId
     *
     * @return string
     */
    public static function note($fileId)
    {
        $html = '
        <button class="btn btn-mini hasTooltip js-iproof-btn-note" data-file-id="'.(int)$fileId.'" title="'.JText::_("COM_IDENTITYPROOF_TOOLTIP_NOTE_BUTTON").'">
            <i class="icon-envelope"></i>
        </button>
        ';

        return $html;
    }
}
