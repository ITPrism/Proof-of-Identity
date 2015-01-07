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

jimport('itprism.controller.admin');

/**
 * Proof of Identity files controller class.
 *
 * @package        ProofOfIdentity
 * @subpackage     Component
 * @since          1.6
 */
class IdentityProofControllerFiles extends ITPrismControllerAdmin
{
    public function __construct($config = array())
    {
        parent::__construct($config);

        // Define task mappings.

        $this->registerTask('pending', 'changeState');
        $this->registerTask('reviewed', 'changeState');
        $this->registerTask('trashed', 'changeState');
    }

    public function getModel($name = 'File', $prefix = 'IdentityProofModel', $config = array('ignore_request' => true))
    {
        $model = parent::getModel($name, $prefix, $config);

        return $model;
    }

    public function changeState()
    {
        // Check for request forgeries
        JSession::checkToken() or jexit(JText::_('JINVALID_TOKEN'));

        // Get items to publish from the request.
        $cid  = $this->input->get('cid', array(), 'array');
        $data = array(
            'reviewed' => 1,
            'pending'  => 0,
            'trashed'  => -2
        );

        $task  = $this->getTask();
        $value = JArrayHelper::getValue($data, $task, 0, 'int');

        $redirectOptions = array(
            "view" => "files"
        );

        // Make sure the item ids are integers
        JArrayHelper::toInteger($cid);
        if (empty($cid)) {
            $this->displayNotice(JText::_($this->text_prefix . '_NO_ITEM_SELECTED'), $redirectOptions);
            return;
        }

        // Get the model.
        $model = $this->getModel();

        try {

            $model->changeState($cid, $value);

        } catch (RuntimeException $e) {

            $this->displayWarning($e->getMessage(), $redirectOptions);
            return;

        } catch (Exception $e) {
            JLog::add($e->getMessage());
            throw new Exception(JText::_($this->text_prefix.'_ERROR_SYSTEM'));
        }

        if ($value == 1) {
            $msg = $this->text_prefix . '_N_FILES_REVIEWED';
        } elseif ($value == -2) {
            $msg = $this->text_prefix . '_N_ITEMS_TRASHED';
        } else {
            $msg = $this->text_prefix . '_N_FILES_PENDING';
        }

        $this->displayMessage(JText::plural($msg, count($cid)), $redirectOptions);
    }
}