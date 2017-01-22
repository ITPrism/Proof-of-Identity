<?php
/**
 * @package      ProofOfIdentity
 * @subpackage   Component
 * @author       Todor Iliev
 * @copyright    Copyright (C) 2017 Todor Iliev <todor@itprism.com>. All rights reserved.
 * @license      GNU General Public License version 3 or later; see LICENSE.txt
 */

// No direct access
defined('_JEXEC') or die;

/**
 * Proof of Identity files controller class.
 *
 * @package        ProofOfIdentity
 * @subpackage     Component
 * @since          1.6
 */
class IdentityproofControllerFiles extends Prism\Controller\Admin
{
    public function __construct($config = array())
    {
        parent::__construct($config);

        // Define task mappings.

        $this->registerTask('pending', 'changeState');
        $this->registerTask('reviewed', 'changeState');
        $this->registerTask('trashed', 'changeState');
    }

    public function getModel($name = 'File', $prefix = 'IdentityproofModel', $config = array('ignore_request' => true))
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
        $value = Joomla\Utilities\ArrayHelper::getValue($data, $task, 0, 'int');

        $redirectOptions = array(
            'view' => 'files'
        );

        // Make sure the item ids are integers
        $cid = Joomla\Utilities\ArrayHelper::toInteger($cid);
        if (!$cid) {
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

        if ((int)$value === 1) {
            $msg = $this->text_prefix . '_N_FILES_REVIEWED';
        } elseif ((int)$value === -2) {
            $msg = $this->text_prefix . '_N_ITEMS_TRASHED';
        } else {
            $msg = $this->text_prefix . '_N_FILES_PENDING';
        }

        $this->displayMessage(JText::plural($msg, count($cid)), $redirectOptions);
    }
}
