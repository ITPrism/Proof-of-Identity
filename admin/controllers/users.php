<?php
/**
 * @package      ProofOfIdentity
 * @subpackage   Component
 * @author       Todor Iliev
 * @copyright    Copyright (C) 2015 Todor Iliev <todor@itprism.com>. All rights reserved.
 * @license      http://www.gnu.org/copyleft/gpl.html GNU/GPL
 */

// No direct access
defined('_JEXEC') or die;

/**
 * Proof of Identity users controller class.
 *
 * @package        ProofOfIdentity
 * @subpackage     Component
 * @since          1.6
 */
class IdentityProofControllerUsers extends Prism\Controller\Admin
{
    public function __construct($config = array())
    {
        parent::__construct($config);

        // Define task mappings.

        // Value = 0
        $this->registerTask('unverify', 'verify');
    }

    public function getModel($name = 'User', $prefix = 'IdentityProofModel', $config = array('ignore_request' => true))
    {
        $model = parent::getModel($name, $prefix, $config);

        return $model;
    }

    public function verify()
    {
        // Check for request forgeries
        JSession::checkToken() or jexit(JText::_('JINVALID_TOKEN'));

        // Get items to publish from the request.
        $cid  = $this->input->get('cid', array(), 'array');
        $data = array(
            'verify'    => 1,
            'unverify'  => 0
        );

        $task  = $this->getTask();
        $value = Joomla\Utilities\ArrayHelper::getValue($data, $task, 0, 'int');

        $redirectOptions = array(
            "view" => "users"
        );

        // Make sure the item ids are integers
        Joomla\Utilities\ArrayHelper::toInteger($cid);
        if (empty($cid)) {
            $this->displayNotice(JText::_($this->text_prefix . '_NO_ITEM_SELECTED'), $redirectOptions);

            return;
        }

        // Get the model.
        $model = $this->getModel();

        try {

            $model->verify($cid, $value);

        } catch (RuntimeException $e) {

            $this->displayWarning($e->getMessage(), $redirectOptions);
            return;

        } catch (Exception $e) {
            JLog::add($e->getMessage());
            throw new Exception(JText::_($this->text_prefix.'_ERROR_SYSTEM'));
        }

        if ($value == 1) {
            $msg = $this->text_prefix . '_N_USERS_VERIFIED';
        } else {
            $msg = $this->text_prefix . '_N_USERS_UNVERIFIED';
        }

        $this->displayMessage(JText::plural($msg, count($cid)), $redirectOptions);
    }
}
