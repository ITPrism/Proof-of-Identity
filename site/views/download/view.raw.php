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

class IdentityproofViewDownload extends JViewLegacy
{
    protected $form;

    public function display($tpl = null)
    {
        $app    = JFactory::getApplication();
        
        $fileId = $app->input->getInt('id');
        $userId = JFactory::getUser()->get('id');

        $validator = new Identityproof\Validator\File\Owner(JFactory::getDbo(), $fileId, $userId);
        if (!$userId or !$validator->isValid()) {
            echo JText::_('COM_IDENTITYPROOF_ERROR_INVALID_REQUEST');
            JFactory::getApplication()->close(500);
            return;
        }

        // Load the form.
        JForm::addFormPath(IDENTITYPROOF_PATH_COMPONENT_SITE . '/models/forms');

        $this->form = JForm::getInstance('com_identityproof.download', 'download', array('control' => 'jform', 'load_data' => false));

        // Set item id to the form.
        $this->form->setValue('file_id', null, $fileId);

        parent::display($tpl);
    }
}
