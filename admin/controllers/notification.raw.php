<?php
/**
 * @package      IdentityProof
 * @subpackage   Component
 * @author       Todor Iliev
 * @copyright    Copyright (C) 2015 Todor Iliev <todor@itprism.com>. All rights reserved.
 * @license      http://www.gnu.org/copyleft/gpl.html GNU/GPL
 */

// No direct access
defined('_JEXEC') or die;

/**
 * Notification controller class.
 *
 * @package        IdentityProof
 * @subpackage     Component
 * @since          1.6
 */
class IdentityProofControllerNotification extends JControllerLegacy
{
    /**
     * Method to get a model object, loading it if required.
     *
     * @param    string $name   The model name. Optional.
     * @param    string $prefix The class prefix. Optional.
     * @param    array  $config Configuration array for model. Optional.
     *
     * @return    object    The model.
     * @since    1.5
     */
    public function getModel($name = 'Notification', $prefix = 'IdentityProofModel', $config = array('ignore_request' => true))
    {
        $model = parent::getModel($name, $prefix, $config);

        return $model;
    }

    /**
     * Save notice to a file.
     */
    public function save()
    {
        // Check for request forgeries.
        JSession::checkToken("post") or jexit(JText::_('JINVALID_TOKEN'));

        $app = JFactory::getApplication();

        // Create response object
        $response = new Prism\Response\Json();

        $fileId = $this->input->post->get("id");
        $note   = $this->input->post->getString("note");
        $userId = JFactory::getUser()->get("id");

        if (!$userId or !$fileId) {
            $response
                ->setTitle(JText::_('COM_IDENTITYPROOF_FAILURE'))
                ->setText(JText::_('COM_IDENTITYPROOF_ERROR_SYSTEM'))
                ->failure();

            echo $response;
            $app->close();
        }

        try {

            // Get the model
            $model = $this->getModel();
            /** @var $model IdentityProofModelNotification */

            $model->leaveNotice($fileId, $note);

        } catch (RuntimeException $e) {
            JLog::add($e->getMessage());

            $response
                ->setTitle(JText::_('COM_IDENTITYPROOF_FAILURE'))
                ->setText($e->getMessage())
                ->failure();

            echo $response;
            $app->close();

        } catch (Exception $e) {
            JLog::add($e->getMessage());
            throw new Exception($e->getMessage());
        }

        $response
            ->setTitle(JText::_('COM_IDENTITYPROOF_SUCCESS'))
            ->setText(JText::_('COM_IDENTITYPROOF_NOTICE_SAVED_SUCCESSFULLY'))
            ->success();

        echo $response;
        $app->close();
    }

    /**
     * Save notice to a file.
     */
    public function getNotice()
    {
        $app = JFactory::getApplication();

        // Create response object
        $response = new Prism\Response\Json();

        $fileId = $this->input->get->get("id");

        $userId = JFactory::getUser()->get("id");

        if (!$userId or !$fileId) {
            $response
                ->setTitle(JText::_('COM_IDENTITYPROOF_FAIL'))
                ->setText(JText::_('COM_IDENTITYPROOF_ERROR_SYSTEM'))
                ->failure();

            echo $response;
            $app->close();
        }

        try {

            // Get the model
            $model = $this->getModel();
            /** @var $model IdentityProofModelNotification */

            $note = $model->getNotice($fileId);

        } catch (Exception $e) {
            JLog::add($e->getMessage());
            throw new Exception($e->getMessage());
        }

        $response
            ->setTitle(JText::_('COM_IDENTITYPROOF_SUCCESS'))
            ->setData(array("note" => $note, "token" => JSession::getFormToken()))
            ->success();

        echo $response;
        $app->close();
    }
}
