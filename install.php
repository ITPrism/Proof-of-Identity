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
 * Script file of the component
 */
class pkg_identityproofInstallerScript
{
    /**
     * Method to install the component.
     *
     * @param $parent
     *
     * @return void
     */
    public function install($parent)
    {
    }

    /**
     * Method to uninstall the component.
     *
     * @param $parent
     *
     * @return void
     */
    public function uninstall($parent)
    {
    }

    /**
     * Method to update the component.
     *
     * @param $parent
     *
     * @return void
     */
    public function update($parent)
    {
    }

    /**
     * Method to run before an install/update/uninstall method
     *
     * @param $type
     * @param $parent
     *
     * @return void
     */
    public function preflight($type, $parent)
    {
    }

    /**
     * Method to run after an install/update/uninstall method
     *
     * @param $type
     * @param $parent
     *
     * @throws \UnexpectedValueException
     *
     * @return void
     */
    public function postflight($type, $parent)
    {
        if (!defined('IDENTITYPROOF_PATH_COMPONENT_ADMINISTRATOR')) {
            define('IDENTITYPROOF_PATH_COMPONENT_ADMINISTRATOR', JPATH_ADMINISTRATOR . DIRECTORY_SEPARATOR . 'components' . DIRECTORY_SEPARATOR . 'com_identityproof');
        }

        // Register Component helpers
        JLoader::register('IdentityproofInstallHelper', IDENTITYPROOF_PATH_COMPONENT_ADMINISTRATOR . '/helpers/install.php');

        // Create folder if it does not exist.
        $params = JComponentHelper::getParams('com_identityproof');
        /** @var  $params Joomla\Registry\Registry */
        
        jimport('Prism.init');
        jimport('Identityproof.init');

        if (!$params->get('files_path') or strcmp('{FILES_PATH}', $params->get('files_path')) === 0) {
            $folder      = IdentityproofInstallHelper::generateFolderName();
            $filesFolder = JPath::clean(JPATH_ROOT .'/'. $folder, '/');

            IdentityproofInstallHelper::createFolder($filesFolder);
        } else {
            $filesFolder = JPath::clean($params->get('files_path'), '/');
        }

        // Start table with the information
        IdentityproofInstallHelper::startTable();

        // Requirements
        IdentityproofInstallHelper::addRowHeading(JText::_('COM_IDENTITYPROOF_MINIMUM_REQUIREMENTS'));

        // Display result about verification for existing folder
        $title = JText::_('COM_IDENTITYPROOF_FILES_FOLDER');
        $info  = $filesFolder;
        if (!is_dir($filesFolder)) {
            $result = array('type' => 'important', 'text' => JText::_('JNO'));
        } else {
            $result = array('type' => 'success', 'text' => JText::_('JYES'));
        }
        IdentityproofInstallHelper::addRow($title, $result, $info);

        // Display result about verification for writable folder
        $title = JText::_('COM_IDENTITYPROOF_WRITABLE_FOLDER');
        $info  = $filesFolder;
        if (!is_writable($filesFolder)) {
            $result = array('type' => 'important', 'text' => JText::_('JNO'));
        } else {
            $result = array('type' => 'success', 'text' => JText::_('JYES'));
        }
        IdentityproofInstallHelper::addRow($title, $result, $info);
        
        // Display result about verification for GD library
        $title = JText::_('COM_IDENTITYPROOF_GD_LIBRARY');
        $info  = '';
        if (!extension_loaded('gd') and function_exists('gd_info')) {
            $result = array('type' => 'important', 'text' => JText::_('COM_IDENTITYPROOF_WARNING'));
        } else {
            $result = array('type' => 'success', 'text' => JText::_('JON'));
        }
        IdentityproofInstallHelper::addRow($title, $result, $info);

        // Display result about verification for cURL library
        $title = JText::_('COM_IDENTITYPROOF_CURL_LIBRARY');
        $info  = '';
        if (!extension_loaded('curl')) {
            $info   = JText::_('COM_IDENTITYPROOF_CURL_INFO');
            $result = array('type' => 'important', 'text' => JText::_('JOFF'));
        } else {
            $result = array('type' => 'success', 'text' => JText::_('JON'));
        }
        IdentityproofInstallHelper::addRow($title, $result, $info);

        // Display result about verification Magic Quotes
        $title = JText::_('COM_IDENTITYPROOF_MAGIC_QUOTES');
        $info  = '';
        if (get_magic_quotes_gpc()) {
            $info   = JText::_('COM_IDENTITYPROOF_MAGIC_QUOTES_INFO');
            $result = array('type' => 'important', 'text' => JText::_('JON'));
        } else {
            $result = array('type' => 'success', 'text' => JText::_('JOFF'));
        }
        IdentityproofInstallHelper::addRow($title, $result, $info);

        // Display result about verification FileInfo
        $title = JText::_('COM_IDENTITYPROOF_FILEINFO');
        $info  = '';
        if (!function_exists('finfo_open')) {
            $info   = JText::_('COM_IDENTITYPROOF_FILEINFO_INFO');
            $result = array('type' => 'important', 'text' => JText::_('JOFF'));
        } else {
            $result = array('type' => 'success', 'text' => JText::_('JON'));
        }
        IdentityproofInstallHelper::addRow($title, $result, $info);

        // Display result about verification PHP Version.
        $title = JText::_('COM_IDENTITYPROOF_PHP_VERSION');
        $info  = '';
        if (version_compare(PHP_VERSION, '5.5.0') < 0) {
            $result = array('type' => 'important', 'text' => JText::_('COM_IDENTITYPROOF_WARNING'));
        } else {
            $result = array('type' => 'success', 'text' => JText::_('JYES'));
        }
        IdentityproofInstallHelper::addRow($title, $result, $info);

        // Display result about MySQL Version.
        $title = JText::_('COM_IDENTITYPROOF_MYSQL_VERSION');
        $info  = '';
        $dbVersion = JFactory::getDbo()->getVersion();
        if (version_compare($dbVersion, '5.5.3', '<')) {
            $result = array('type' => 'important', 'text' => JText::_('COM_IDENTITYPROOF_WARNING'));
        } else {
            $result = array('type' => 'success', 'text' => JText::_('JYES'));
        }
        IdentityproofInstallHelper::addRow($title, $result, $info);
        
        // Display result about verification of installed Prism Library
        $info  = '';
        if (!class_exists('Prism\\Version')) {
            $title  = JText::_('COM_IDENTITYPROOF_PRISM_LIBRARY');
            $info   = JText::_('COM_IDENTITYPROOF_PRISM_LIBRARY_DOWNLOAD');
            $result = array('type' => 'important', 'text' => JText::_('JNO'));
        } else {
            $prismVersion   = new Prism\Version();
            $text           = JText::sprintf('COM_IDENTITYPROOF_CURRENT_V_S', $prismVersion->getShortVersion());

            if (class_exists('Identityproof\\Version')) {
                $componentVersion = new Identityproof\Version();
                $title            = JText::sprintf('COM_IDENTITYPROOF_PRISM_LIBRARY_S', $componentVersion->requiredPrismVersion);

                if (version_compare($prismVersion->getShortVersion(), $componentVersion->requiredPrismVersion, '<')) {
                    $info   = JText::_('COM_IDENTITYPROOF_PRISM_LIBRARY_DOWNLOAD');
                    $result = array('type' => 'warning', 'text' => $text);
                }

            } else {
                $title  = JText::_('COM_IDENTITYPROOF_PRISM_LIBRARY');
                $result = array('type' => 'success', 'text' => $text);
            }
        }
        IdentityproofInstallHelper::addRow($title, $result, $info);

        // Installed extensions

        IdentityproofInstallHelper::addRowHeading(JText::_('COM_IDENTITYPROOF_INSTALLED_EXTENSIONS'));

        // Proof Of Identity Library
        $result = array('type' => 'success', 'text' => JText::_('COM_IDENTITYPROOF_INSTALLED'));
        IdentityproofInstallHelper::addRow(JText::_('COM_IDENTITYPROOF_IDENTITYPROOF_LIBRARY'), $result, JText::_('COM_IDENTITYPROOF_LIBRARY'));

        // Plugins

        // User - Proof of Identity
        $result = array('type' => 'success', 'text' => JText::_('COM_IDENTITYPROOF_INSTALLED'));
        IdentityproofInstallHelper::addRow(JText::_('COM_IDENTITYPROOF_USER_IDENTITYPROOF'), $result, JText::_('COM_IDENTITYPROOF_PLUGIN'));

        // Content - Proof of Identity Admin Mail
        $result = array('type' => 'success', 'text' => JText::_('COM_IDENTITYPROOF_INSTALLED'));
        IdentityproofInstallHelper::addRow(JText::_('COM_IDENTITYPROOF_CONTENT_IDENTITYPROOF_ADMIN_MAIL'), $result, JText::_('COM_IDENTITYPROOF_PLUGIN'));

        // Content - Proof of Identity User Mail
        $result = array('type' => 'success', 'text' => JText::_('COM_IDENTITYPROOF_INSTALLED'));
        IdentityproofInstallHelper::addRow(JText::_('COM_IDENTITYPROOF_CONTENT_IDENTITYPROOF_USER_MAIL'), $result, JText::_('COM_IDENTITYPROOF_PLUGIN'));

        // End table
        IdentityproofInstallHelper::endTable();

        echo JText::sprintf('COM_IDENTITYPROOF_MESSAGE_REVIEW_SAVE_SETTINGS', JRoute::_('index.php?option=com_identityproof'));

        if (!class_exists('Prism\\Version')) {
            echo JText::_('COM_IDENTITYPROOF_MESSAGE_INSTALL_PRISM_LIBRARY');
        } else {
            if (class_exists('Identityproof\\Version')) {
                $prismVersion     = new Prism\Version();
                $componentVersion = new Identityproof\Version();
                if (version_compare($prismVersion->getShortVersion(), $componentVersion->requiredPrismVersion, '<')) {
                    echo JText::_('COM_IDENTITYPROOF_MESSAGE_INSTALL_PRISM_LIBRARY');
                }
            }
        }
    }
}
