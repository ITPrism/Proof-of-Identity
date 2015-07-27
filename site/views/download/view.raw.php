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

class IdentityProofViewDownload extends JViewLegacy
{
    protected $form;

    public function display($tpl = null)
    {
        $app    = JFactory::getApplication();
        
        $fileId = $app->input->getInt("id");
        $userId = JFactory::getUser()->get("id");

        $validator = new IdentityProof\Validator\File\Owner(JFactory::getDbo(), $fileId, $userId);
        if (!$userId or !$validator->isValid()) {
            echo JText::_("COM_IDENTITYPROOF_ERROR_INVALID_REQUEST");
            JFactory::getApplication()->close(500);
            return;
        }

        // Load the form.
        JForm::addFormPath(IDENTITYPROOF_PATH_COMPONENT_SITE . '/models/forms');

        $this->form = JForm::getInstance('com_identityproof.download', 'download', array('control' => "jform", 'load_data' => false));

        // Set item id to the form.
        $this->form->setValue("file_id", null, $fileId);

        parent::display($tpl);
    }
}
