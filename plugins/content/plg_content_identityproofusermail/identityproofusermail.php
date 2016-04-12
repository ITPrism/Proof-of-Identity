<?php
/**
 * @package         IdentityProof
 * @subpackage      Plugins
 * @author          Todor Iliev
 * @copyright       Copyright (C) 2016 Todor Iliev <todor@itprism.com>. All rights reserved.
 * @license         GNU General Public License version 3 or later; see LICENSE.txt
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

    protected $name = 'Content - Proof of Identity User Mail';

    public function init()
    {
        jimport('Emailtemplates.init');

        $this->loadLanguage();
    }

    /**
     * Send notification mail to a user
     * when the administrator set his state to VERIFIED.
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

        if (strcmp('com_identityproof.user', $context) !== 0) {
            return null;
        }

        // Initialize plugin
        $this->init();

        // Email template ID.
        $emailId = (int)$this->params->get('send_when_verified', 0);
        if (!$emailId) {
            JLog::add(JText::sprintf('PLG_CONTENT_IDENTITYPROOFUSERMAIL_ERROR_INVALID_EMAIL_TEMPLATE', $this->name), JLog::ERROR, 'com_identityproof');
            return null;
        }

        if (is_array($ids)) {
            $ids = Joomla\Utilities\ArrayHelper::toInteger($ids);

            if (count($ids) > 0 and (int)$state === Prism\Constants::APPROVED) {

                $users = new Identityproof\Users(JFactory::getDbo());
                $users->load(array('ids' => $ids));

                if (count($users) === 0) {
                    JLog::add(JText::sprintf('PLG_CONTENT_IDENTITYPROOFUSERMAIL_ERROR_INVALID_USERS_S', $this->name), JLog::ERROR, 'com_identityproof');
                    return null;
                }

                foreach ($users as $user) {

                    // Send email to users.
                    $return = $this->sendMail($user, $emailId, 'user');

                    // If there is an error, stop the loop.
                    // Let the administrator to look the errors.
                    if ($return !== true) {
                        return false;
                    }

                }

            }
        }

        return true;
    }

    /**
     * Send notification mail to a user
     * when the administrator leave a notice about a file.
     *
     * @param string $context
     * @param int $id File ID
     *
     * @return bool|null
     */
    public function onContentLeaveNote($context, $id)
    {
        $app = JFactory::getApplication();
        /** @var $app JApplicationSite */

        if (!$app->isAdmin()) {
            return null;
        }

        if (strcmp('com_identityproof.notification', $context) !== 0) {
            return null;
        }

        // Initialize plugin
        $this->init();

        // Email template ID.
        $emailId = $this->params->get('send_when_leave_notice', 0);
        if (!$emailId) {
            JLog::add(JText::sprintf('PLG_CONTENT_IDENTITYPROOFUSERMAIL_ERROR_INVALID_EMAIL_TEMPLATE', $this->name), JLog::ERROR, 'com_identityproof');
            return null;
        }

        if ($id > 0) {
            $file = new Identityproof\File(JFactory::getDbo());
            $file->load($id);

            if (!$file->getId()) {
                JLog::add(JText::sprintf('PLG_CONTENT_IDENTITYPROOFUSERMAIL_ERROR_INVALID_FILE_S', $this->name), JLog::ERROR, 'com_identityproof');
                return null;
            }

            // Send email to users.
            $return = $this->sendMail($file, $emailId, 'file');

            // If there is an error, stop the loop.
            // Let the administrator to look the errors.
            if ($return !== true) {
                return false;
            }
        }

        return true;
    }

    /**
     * @param Identityproof\File|array $item
     * @param int $emailId
     * @param string $type
     *
     * @return bool
     * @throws Exception
     */
    protected function sendMail($item, $emailId, $type)
    {
        $app = JFactory::getApplication();
        /** @var $app JApplicationSite */

        // Get website
        $emailMode = $this->params->get('email_mode', 'plain');

        // Prepare data for parsing
        $data = array(
            'site_name'    => $app->get('sitename'),
            'site_url'     => JUri::root(),
        );

        // Send mail to a user.
        if (!$emailId) {
            return false;
        }

        $email = new Emailtemplates\Email();
        $email->setDb(JFactory::getDbo());
        $email->load($emailId);

        // Prepare sender data.
        $componentParams = JComponentHelper::getParams('com_identityproof');
        /** @var  $componentParams Joomla\Registry\Registry */

        $senderId = (int)$componentParams->get('administrator_id', 0);
        if ($senderId > 0) {
            $sender     = JFactory::getUser($senderId);
            $email->setSenderName($sender->get('name'));
            $email->setSenderEmail($sender->get('email'));
        } else {
            if (!$email->getSenderName()) {
                $email->setSenderName($app->get('fromname'));
            }
            if (!$email->getSenderEmail()) {
                $email->setSenderEmail($app->get('mailfrom'));
            }
        }

        $data['administrator_name']     = $email->getSenderName();
        $data['administrator_email']    = $email->getSenderEmail();

        // Prepare data for parsing.
        switch ($type) {

            case 'file':

                $user = JFactory::getUser($item->getUserId());

                $data['user_name']      = $user->get('name');
                $data['user_email']     = $user->get('email');
                $data['note']           = $item->getNote();
                $data['filename']       = $item->getFilename();

                break;

            case 'user':
                $data['user_name']      = $item['name'];
                $data['user_email']     = $item['email'];
                break;

        }

        $email->parse($data);
        $subject = $email->getSubject();
        $body    = $email->getBody($emailMode);

        $mailer = JFactory::getMailer();
        if (strcmp('html', $emailMode) === 0) { // Send as HTML message
            $result = $mailer->sendMail($email->getSenderEmail(), $email->getSenderName(), $data['user_email'], $subject, $body, Prism\Constants::MAIL_MODE_HTML);
        } else { // Send as plain text.
            $result = $mailer->sendMail($email->getSenderEmail(), $email->getSenderName(), $data['user_email'], $subject, $body, Prism\Constants::MAIL_MODE_PLAIN);
        }

        // Log the error.
        if ($result !== true) {
            JLog::add(JText::sprintf('PLG_CONTENT_IDENTITYPROOFUSERMAIL_ERROR_SEND_MAIL', $this->name));
            return false;
        }

        return true;
    }
}
