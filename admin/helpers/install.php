<?php
/**
 * @package      ProofOfIdentity
 * @subpackage   Component
 * @author       Todor Iliev
 * @copyright    Copyright (C) 2015 Todor Iliev <todor@itprism.com>. All rights reserved.
 * @license      GNU General Public License version 3 or later; see LICENSE.txt
 */

// no direct access
defined('_JEXEC') or die;

/**
 * These class contains methods using for upgrading the extension.
 */
class IdentityProofInstallHelper
{
    public static function startTable()
    {
        echo '
        <div style="width: 600px;">
        <table class="table table-bordered table-striped">';
    }

    public static function endTable()
    {
        echo "</table></div>";
    }

    public static function addRowHeading($heading)
    {
        echo '
	    <tr class="info">
            <td colspan="3">' . $heading . '</td>
        </tr>';
    }

    /**
     * Display an HTML code for a row
     *
     * @param string $title
     * @param array  $result
     * @param string $info
     *
     * # Example
     *    array(
     *    type => success, important, warning,
     *    text => yes, no, off, on, warning,...
     *    )
     */
    public static function addRow($title, $result, $info)
    {
        $outputType = JArrayHelper::getValue($result, "type", "");
        $outputText = JArrayHelper::getValue($result, "text", "");

        $output = "";
        if (!empty($outputType) and !empty($outputText)) {
            $output = '<span class="label label-' . $outputType . '">' . $outputText . '</span>';
        }

        echo '
	    <tr>
            <td>' . $title . '</td>
            <td>' . $output . '</td>
            <td>' . $info . '</td>
        </tr>';
    }

    public static function generateFolderName()
    {
        // Generate string
        $hash = md5(uniqid(time() + mt_rand(), true));
        $hash = substr($hash, 0, rand(6, 12));

        // Add prefix
        $hash = "ip" . $hash;

        return $hash;
    }

    public static function createFolder($filesFolder)
    {
        if (true !== JFolder::create($filesFolder)) {
            JLog::add(JText::sprintf("COM_IDENTITYPROOF_ERROR_CANNOT_CREATE_FOLDER", $filesFolder));
        } else {

            // Create .htaccess file.
            $htaccessFile = JPath::clean($filesFolder."/.htaccess");

            $content = "## Restricted for all requests.\nDeny from all";

            if (true !== JFile::write($htaccessFile, $content)) {
                JLog::add(JText::sprintf("COM_IDENTITYPROOF_ERROR_CANNOT_CREATE_FILE", $htaccessFile));
            }

            // Replace default path to the files.
            $configFile = JPath::clean(JPATH_ADMINISTRATOR."/components/com_identityproof/config.xml");
            if (!JFile::exists($configFile)) {
                JLog::add(JText::sprintf("COM_IDENTITYPROOF_ERROR_CONFIG_NOT_EXISTS", $configFile));
            } else {
                $content = file_get_contents($configFile);
                $content = str_replace("{FILES_PATH}", $filesFolder, $content);

                file_put_contents($configFile, $content);

                // Set permissions for config.xml
                JPath::setPermissions($configFile);
            }

            // Set permissions.
            JPath::setPermissions($filesFolder);
        }

    }
}
