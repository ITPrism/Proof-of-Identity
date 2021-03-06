<?php
/**
 * @package      ProofOfIdentity
 * @subpackage   Components
 * @author       Todor Iliev
 * @copyright    Copyright (C) 2017 Todor Iliev <todor@itprism.com>. All rights reserved.
 * @license      GNU General Public License version 3 or later; see LICENSE.txt
 */

// No direct access
defined('_JEXEC') or die;

/**
 * Proof of Identity file controller class.
 *
 * @package      ProofOfIdentity
 * @subpackage   Components
 */
class IdentityproofControllerFile extends Prism\Controller\Form\Backend
{
    public function save($key = null, $urlVar = null)
    {
        JSession::checkToken() or jexit(JText::_('JINVALID_TOKEN'));

        $data   = $this->input->post->get('jform', array(), 'array');
        $itemId = Joomla\Utilities\ArrayHelper::getValue($data, 'id');

        $redirectOptions = array(
            'task' => $this->getTask(),
            'id'   => $itemId
        );

        $model = $this->getModel();
        /** @var $model IdentityproofModelFile */

        $form = $model->getForm($data, false);
        /** @var $form JForm */

        if (!$form) {
            throw new Exception(JText::_('COM_IDENTITYPROOF_ERROR_FORM_CANNOT_BE_LOADED'));
        }

        // Validate the form data
        $validData = $model->validate($form, $data);

        // Check for errors
        if ($validData === false) {
            $this->displayNotice($form->getErrors(), $redirectOptions);
            return;
        }

        try {

            $itemId = $model->save($validData);

            $redirectOptions['id'] = $itemId;

        } catch (Exception $e) {
            JLog::add($e->getMessage());
            throw new Exception(JText::_('COM_IDENTITYPROOF_ERROR_SYSTEM'));
        }

        $this->displayMessage(JText::_('COM_IDENTITYPROOF_FILE_SAVED'), $redirectOptions);
    }
}
