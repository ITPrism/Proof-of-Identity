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
 * Proof of Identity user mail plugin
 *
 * @package        IdentityProof
 * @subpackage     Plugins
 */
class plgContentIdentityProofUserMail extends JPlugin
{
    /**
     * @var Prism\Log\Log
     */
    protected $log;

    /**
     * @var Joomla\Registry\Registry
     */
    public $params;

    protected $name = "Content - Proof of Identity User Mail";

    public function init()
    {
        jimport("EmailTemplates.init");

        $this->loadLanguage();
    }

    /**
     * Send notification mail to a user when his project be approved.
     * If I return NULL, an message will not be displayed in the browser.
     * If I return FALSE, an error message will be displayed in the browser.
     *
     * @param string $context
     * @param array $ids
     * @param int $state
     *
     * @return bool|null
     */
    public function onContentChangeState($context, $ids, $state)
    {
        $app = JFactory::getApplication();
        /** @var $app JApplicationSite */

        if (!$app->isAdmin()) {
            return null;
        }

        if (strcmp("com_identityproof.user", $context) != 0) {
            return null;
        }

        // Initialize plugin
        $this->init();

        // Check for enabled option for sending mail
        // when administrator approve project.
        $emailId = $this->params->get("send_when_verified", 0);
        if (!$emailId) {
            JLog::add(JText::sprintf("PLG_CONTENT_IDENTITYPROOFUSERMAIL_ERROR_INVALID_EMAIL_TEMPLATE", $this->name));
            return false;
        }

        Joomla\Utilities\ArrayHelper::toInteger($ids);

        if (!empty($ids) and $state == Prism\Constants::APPROVED) {

            $users = new IdentityProof\Users(JFactory::getDbo());
            $users->load(array("ids" => $ids));

            if (0 >= count($users)) {
                JLog::add(JText::sprintf("PLG_CONTENT_IDENTITYPROOFUSERMAIL_ERROR_INVALID_USERS_S", $this->name));
                return false;
            }

            foreach ($users as $user) {

                // Send email to users.
                $return = $this->sendMail($user, $emailId);

                // If there is an error, stop the loop.
                // Let the administrator to look the errors.
                if ($return !== true) {
                    return false;
                }

            }

        }

        return true;
    }

    protected function sendMail($user, $emailId)
    {
        $app = JFactory::getApplication();
        /** @var $app JApplicationSite */

        // Get website
        $emailMode = $this->params->get("email_mode", "plain");

        // Prepare data for parsing
        $data = array(
            "site_name"    => $app->get("sitename"),
            "site_url"     => JUri::root(),
        );

        // Send mail to a user.
        if (!$emailId) {
            return false;
        }

        $email = new EmailTemplates\Email();
        $email->setDb(JFactory::getDbo());
        $email->load($emailId);

        // Prepare sender data.
        $componentParams = JComponentHelper::getParams("com_identityproof");
        /** @var  $componentParams Joomla\Registry\Registry */

        $senderId = $componentParams->get("administrator_id");
        if (!empty($senderId)) {
            $sender     = JFactory::getUser($senderId);
            $email->setSenderName($sender->get("name"));
            $email->setSenderEmail($sender->get("email"));
        } else {
            if (!$email->getSenderName()) {
                $email->setSenderName($app->get("fromname"));
            }
            if (!$email->getSenderEmail()) {
                $email->setSenderEmail($app->get("mailfrom"));
            }
        }

        // Prepare data for parsing
        $data["administrator_name"]     = $email->getSenderName();
        $data["administrator_email"]    = $email->getSenderEmail();
        $data["user_name"]              = $user["name"];
        $data["user_email"]             = $user["email"];

        $email->parse($data);
        $subject = $email->getSubject();
        $body    = $email->getBody($emailMode);

        $mailer = JFactory::getMailer();
        if (strcmp("html", $emailMode) == 0) { // Send as HTML message
            $result = $mailer->sendMail($email->getSenderEmail(), $email->getSenderName(), $data["file_owner_email"], $subject, $body, Prism\Constants::MAIL_MODE_HTML);
        } else { // Send as plain text.
            $result = $mailer->sendMail($email->getSenderEmail(), $email->getSenderName(), $data["file_owner_email"], $subject, $body, Prism\Constants::MAIL_MODE_PLAIN);
        }

        // Log the error.
        if ($result !== true) {
            JLog::add(JText::sprintf("PLG_CONTENT_IDENTITYPROOFUSERMAIL_ERROR_SEND_MAIL", $this->name));
            return false;
        }

        return true;
    }
}
