<?php
/**
 * @package      Identityproof
 * @subpackage   Components
 * @author       Todor Iliev
 * @copyright    Copyright (C) 2017 Todor Iliev <todor@itprism.com>. All rights reserved.
 * @license      GNU General Public License version 3 or later; see LICENSE.txt
 */

// no direct access
defined('_JEXEC') or die;

/**
 * Proof controller
 *
 * @package     Identityproof
 * @subpackage  Components
 */
class IdentityproofControllerProof extends Prism\Controller\Form\Frontend
{
    /**
     * Method to get a model object, loading it if required.
     *
     * @param    string $name   The model name. Optional.
     * @param    string $prefix The class prefix. Optional.
     * @param    array  $config Configuration array for model. Optional.
     *
     * @return   IdentityproofModelProof    The model.
     * @since    1.5
     */
    public function getModel($name = 'Proof', $prefix = 'IdentityproofModel', $config = array('ignore_request' => true))
    {
        $model = parent::getModel($name, $prefix, $config);
        return $model;
    }

    public function save($key = null, $urlVar = null)
    {
        // Check for request forgeries.
        JSession::checkToken() or jexit(JText::_('JINVALID_TOKEN'));

        $userId = JFactory::getUser()->get('id');
        if (!$userId) {
            $redirectOptions = array(
                'force_direction' => 'index.php?option=com_users&view=login'
            );
            $this->displayNotice(JText::_('COM_IDENTITYPROOF_ERROR_NOT_LOG_IN'), $redirectOptions);
            return;
        }

        // Get the data from the form POST
        $data   = $this->input->post->get('jform', array(), 'array');

        // Get the file.
        $file = $this->input->files->get('jform', array(), 'array');
        $file = Joomla\Utilities\ArrayHelper::getValue($file, 'file');

        $data['file'] = $file;

        $redirectOptions = array(
            'view' => 'proof'
        );

        $model = $this->getModel();
        /** @var $model IdentityproofModelProof */

        $form = $model->getForm($data, false);
        /** @var $form JForm */

        if (!$form) {
            throw new Exception(JText::_('COM_IDENTITYPROOF_ERROR_FORM_CANNOT_BE_LOADED'));
        }

        // Test if the data is valid.
        $validData = $model->validate($form, $data);

        // Check for errors.
        if ($validData === false) {
            $this->displayNotice($form->getErrors(), $redirectOptions);
            return;
        }

        try {
            if (!empty($file['name'])) {
                $file = $model->uploadFile($file);
                if ($file !== null and $file !== '') {
                    $validData['file'] = $file;
                }
            }

            $model->save($validData);
        } catch (RuntimeException $e) {
            $this->displayWarning($e->getMessage(), $redirectOptions);
            return;
        } catch (InvalidArgumentException $e) {
            JLog::add($e->getMessage(), JLog::ERROR, 'com_identityproof');
            $this->displayWarning(JText::_('COM_IDENTITYPROOF_ERROR_FILE_CANT_BE_UPLOADED'), $redirectOptions);
            return;
        } catch (Exception $e) {
            JLog::add($e->getMessage(), JLog::ERROR, 'com_identityproof');
            throw new Exception(JText::_('COM_IDENTITYPROOF_ERROR_SYSTEM'));
        }

        $this->displayMessage(JText::_('COM_IDENTITYPROOF_FILE_SUCCESSFULLY_UPLOADED'), $redirectOptions);
    }
}
