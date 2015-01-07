<?php
/**
 * @package      ProofOfIdentity
 * @subpackage   Component
 * @author       Todor Iliev
 * @copyright    Copyright (C) 2015 Todor Iliev <todor@itprism.com>. All rights reserved.
 * @license      http://www.gnu.org/copyleft/gpl.html GNU/GPL
 */

// no direct access
defined('_JEXEC') or die;

/**
 * It is Proof of Identity helper class
 */
class IdentityProofHelper
{
    protected static $extension = "com_identityproof";

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
            $vName == 'dashboard'
        );

        JHtmlSidebar::addEntry(
            JText::_('COM_IDENTITYPROOF_FILES'),
            'index.php?option=' . self::$extension . '&view=files',
            $vName == 'records'
        );

        JHtmlSidebar::addEntry(
            JText::_('COM_IDENTITYPROOF_USERS'),
            'index.php?option=' . self::$extension . '&view=users',
            $vName == 'users'
        );

        JHtmlSidebar::addEntry(
            JText::_('COM_IDENTITYPROOF_PLUGINS'),
            'index.php?option=com_plugins&view=plugins&filter_search=' . rawurlencode("proof"),
            $vName == 'plugins'
        );
    }

    /**
     * Encrypt data.
     *
     * @param array $keys That must be an array that contains private and public keys.
     * @param mixed $data The data that has to be encrypted.
     *
     * @return mixed
     */
    public static function encrypt(array $keys, $data)
    {
        $chiper = new JCryptCipherRijndael256();
        $key    = new JCryptKey("rijndael256", $keys["private"], $keys["public"]);

        $crypt  = new JCrypt($chiper, $key);

        return $crypt->encrypt($data);
    }

    /**
     * Decrypt data.
     *
     * @param array $keys That must be an array that contains private and public keys.
     * @param mixed $data Encrypted data that has to be decrypted.
     *
     * @return mixed
     */
    public static function decrypt(array $keys, $data)
    {
        $chiper = new JCryptCipherRijndael256();
        $key    = new JCryptKey("rijndael256", $keys["private"], $keys["public"]);

        $crypt  = new JCrypt($chiper, $key);

        return $crypt->decrypt($data);
    }
}
