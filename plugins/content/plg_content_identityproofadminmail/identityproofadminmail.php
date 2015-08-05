<?php
/**
 * @package         IdentityProof
 * @subpackage      Plugins
 * @author          Todor Iliev
 * @copyright       Copyright (C) 2015 Todor Iliev <todor@itprism.com>. All rights reserved.
 * @license         http://www.gnu.org/copyleft/gpl.html GNU/GPL
 */

// no direct access
defined('_JEXEC') or die;

/**
 * This plugin send notification mails to the administrator.
 *
 * @package        IdentityProof
 * @subpackage     Plugins
 */
class plgContentIdentityProofAdminMail extends JPlugin
{
    /**
     * @var Prism\Log\Log
     */
    protected $log;

    /**
     * @var Joomla\Registry\Registry
     */
    public $params;

    protected $name = "Content - Proof of Identity Admin Mail";

    public function init()
    {
        jimport('Prism.init');
        jimport("EmailTemplates.init");

        $this->loadLanguage();
    }

    /**
     * This method sends notification mail to the administrator when someone create a project.
     *
     * If I return NULL, an message will not be displayed in the browser.
     * If I return FALSE, an error message will be displayed in the browser.
     *
     * @param string  $context
     * @param object  $file
     * @param boolean $isNew
     *
     * @return null|boolean
     */
    public function onContentAfterSave($context, &$file, $isNew)
    {
        if (strcmp("com_identityproof.uploading", $context) != 0) {
            return null;
        }

        $app = JFactory::getApplication();
        /** @var $app JApplicationSite */

        if ($app->isAdmin()) {
            return null;
        }

        // Initialize plugin
        $this->init();

        // Check for enabled option for sending mail
        // when user upload a file.
        $emailId = $this->params->get("send_when_upload", 0);
        if (!$emailId) {
            JLog::add(JText::sprintf("PLG_CONTENT_IDENTITYPROOFADMINMAIL_ERROR_INVALID_EMAIL_TEMPLATE", $this->name), JLog::DEBUG);
            return null;
        }

        if (!empty($file->id) and $isNew) {

            // Send email to the administrator.
            $return = $this->sendMail($file, $emailId);

            // Check for error.
            if ($return !== true) {
                JLog::add(JText::sprintf("PLG_CONTENT_IDENTITYPROOFADMINMAIL_ERROR_INVALID_FILE", $this->name), JLog::ERROR);
                return null;
            }
        }

        return true;
    }

    protected function sendMail($file, $emailId)
    {
        $app = JFactory::getApplication();
        /** @var $app JApplicationSite */

        // Send mail to the administrator
        if (!$emailId) {
            return false;
        }

        $fileOwner     = JFactory::getUser($file->user_id);

        // Get website
        $uri     = JUri::getInstance();
        $website = $uri->toString(array("scheme", "host"));

        $emailMode = $this->params->get("email_mode", "plain");

        // Prepare data for parsing
        $data = array(
            "site_name"  => $app->get("sitename"),
            "site_url"   => JUri::root(),
            "item_title" => $file->title,
            "item_url"   => $website . "/administrator/index.php?option=com_identityproof&view=files&filter_search=id:".(int)$file->id,
            "user_name"  => $fileOwner->get("name"),
            "user_email" => $fileOwner->get("email")
        );

        $email = new EmailTemplates\Email();
        $email->setDb(JFactory::getDbo());
        $email->load($emailId);

        if (!$email->getSenderName()) {
            $email->setSenderName($app->get("fromname"));
        }
        if (!$email->getSenderEmail()) {
            $email->setSenderEmail($app->get("mailfrom"));
        }

        // Prepare recipient data.
        $componentParams = JComponentHelper::getParams("com_identityproof");
        /** @var  $componentParams Joomla\Registry\Registry */

        $recipientId = $componentParams->get("administrator_id");
        if (!empty($recipientId)) {
            $recipient     = JFactory::getUser($recipientId);
            $recipientMail = $recipient->get("email");
        } else {
            $recipientMail = $app->get("mailfrom");
        }

        $email->parse($data);
        $subject = $email->getSubject();
        $body    = $email->getBody($emailMode);

        $mailer = JFactory::getMailer();
        if (strcmp("html", $emailMode) == 0) { // Send as HTML message
            $result = $mailer->sendMail($email->getSenderEmail(), $email->getSenderName(), $recipientMail, $subject, $body, Prism\Constants::MAIL_MODE_HTML);
        } else { // Send as plain text.
            $result = $mailer->sendMail($email->getSenderEmail(), $email->getSenderName(), $recipientMail, $subject, $body, Prism\Constants::MAIL_MODE_PLAIN);
        }

        // Log the error.
        if ($result !== true) {
            JLog::add(JText::sprintf("PLG_CONTENT_IDENTITYPROOFADMINMAIL_ERROR_SEND_MAIL", $this->name));
            return false;
        }

        return true;
    }
}
