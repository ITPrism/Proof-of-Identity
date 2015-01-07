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
 * Data controller class.
 *
 * @package        IdentityProof
 * @subpackage     Component
 * @since          1.6
 */
class IdentityProofControllerFile extends JControllerLegacy
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
    public function getModel($name = 'File', $prefix = 'IdentityProofModel', $config = array('ignore_request' => true))
    {
        $model = parent::getModel($name, $prefix, $config);

        return $model;
    }
    
    /**
     * Delete a file.
     */
    public function remove()
    {
        $app = JFactory::getApplication();

        // Create response object
        jimport("itprism.response.json");
        $response = new ITPrismResponseJson();

        $fileId = $this->input->post->get("id");

        $userId = JFactory::getUser()->get("id");

        // Create validator object.
        jimport("identityproof.validator.file.owner");
        $validator = new IdentityProofValidatorFileOwner(JFactory::getDbo(), $fileId, $userId);

        if (!$userId) {
            $response
                ->setTitle(JText::_('COM_IDENTITYPROOF_FAIL'))
                ->setText(JText::_('COM_IDENTITYPROOF_ERROR_NOT_LOG_IN'))
                ->failure();

            echo $response;
            $app->close();
        }

        if (!$validator->isValid()) {
            $response
                ->setTitle(JText::_('COM_IDENTITYPROOF_FAIL'))
                ->setText(JText::_('COM_IDENTITYPROOF_ERROR_INVALID_ITEM'))
                ->failure();

            echo $response;
            $app->close();
        }

        try {

            // Get the model
            $model = $this->getModel();
            /** @var $model IdentityProofModelFile */

            $model->remove($fileId, $userId);

        } catch (RuntimeException $e) {

            JLog::add($e->getMessage());
            $response
                ->setTitle(JText::_('COM_IDENTITYPROOF_FAIL'))
                ->setText(JText::_('COM_IDENTITYPROOF_ERROR_INVALID_ITEM'))
                ->failure();

            echo $response;
            $app->close();

        } catch (Exception $e) {
            JLog::add($e->getMessage());
            throw new Exception($e->getMessage());
        }

        $response
            ->setTitle(JText::_('COM_IDENTITYPROOF_SUCCESS'))
            ->setText(JText::_('COM_IDENTITYPROOF_FILE_DELETED'))
            ->setData(array("file_id" => $fileId))
            ->success();

        echo $response;
        $app->close();
    }

    public function download()
    {
        // Check for request forgeries.
        JSession::checkToken("post") or jexit(JText::_('JINVALID_TOKEN'));
        
        $user   = JFactory::getUser();
        
        $data   = $this->input->post->get("jform", array(), "array");

        $fileId = JArrayHelper::getValue($data, "file_id", 0, "int");
        $userId = $user->get("id");

        // Validate the user.
        if (!$userId) {
            $this->setRedirect(JRoute::_('index.php?option=com_users&view=login', false), JText::_('COM_IDENTITYPROOF_ERROR_NOT_LOG_IN'));
            return;
        }

        // Validate the item owner.
        jimport("identityproof.validator.file.owner");
        $validator = new IdentityProofValidatorFileOwner(JFactory::getDbo(), $fileId, $userId);
        if (!$validator->isValid()) {
            $this->setRedirect(JRoute::_(IdentityProofHelperRoute::getProofRoute(), false), JText::_('COM_IDENTITYPROOF_ERROR_INVALID_ITEM'));
            return;
        }

        // Validate the password.
        $password = JArrayHelper::getValue($data, "password", null, "string");
        $match    = JUserHelper::verifyPassword($password, $user->get("password"), $userId);
        if (!$match) {
            $this->setRedirect(JRoute::_(IdentityProofHelperRoute::getProofRoute(), false), JText::_('COM_IDENTITYPROOF_ERROR_INVALID_ITEM'));
            return;
        }

        $params = JComponentHelper::getParams("com_identityproof");
        /** @var  $params Joomla\Registry\Registry */

        try {

            // Load file data.
            jimport("identityproof.file");
            $file = new IdentityProofFile(JFactory::getDbo());

            $keys = array(
                "id" => $fileId,
                "user_id" => $userId
            );
            $file->load($keys);

            // Prepare keys.
            $keys      = array(
                "private" => $file->getPrivate(),
                "public"  => $file->getPublic()
            );

            // Prepare meta data
            $fileSize   = $file->getMetaData("filesize");
            $mimeType   = $file->getMetaData("mime_type");

            // Decrypt the file.
            $filePath   = JPath::clean($params->get("files_path") . DIRECTORY_SEPARATOR . $file->getFilename());
            $output     = file_get_contents($filePath);

            $output     = IdentityProofHelper::decrypt($keys, $output);

        } catch (Exception $e) {
            JLog::add($e->getMessage());
            throw new Exception(JText::_('COM_IDENTITYPROOF_ERROR_SYSTEM'));
        }
        
        $app = JFactory::getApplication();

        $app->setHeader('Content-Type', $mimeType, true);
        $app->setHeader('Cache-Control', 'must-revalidate, post-check=0, pre-check=0', true);
        $app->setHeader('Content-Transfer-Encoding', 'binary', true);
        $app->setHeader('Pragma', 'no-cache', true);
        $app->setHeader('Expires', '0', true);
        $app->setHeader('Content-Disposition', 'attachment; filename=' . $file->getFilename(), true);
        $app->setHeader('Content-Length', $fileSize, true);

        $doc = JFactory::getDocument();
        $doc->setMimeEncoding($mimeType);

        $app->sendHeaders();

        echo $output;
        $app->close();
    }

    /**
     * Return a not about resource.
     */
    public function note()
    {
        $app = JFactory::getApplication();

        // Create response object
        jimport("itprism.response.json");
        $response = new ITPrismResponseJson();

        $fileId = $this->input->get->get("id");

        $userId = JFactory::getUser()->get("id");

        // Create validator object.
        jimport("identityproof.validator.file.owner");
        $validator = new IdentityProofValidatorFileOwner(JFactory::getDbo(), $fileId, $userId);

        if (!$userId) {
            $response
                ->setTitle(JText::_('COM_IDENTITYPROOF_FAIL'))
                ->setText(JText::_('COM_IDENTITYPROOF_ERROR_NOT_LOG_IN'))
                ->failure();

            echo $response;
            $app->close();
        }

        if (!$validator->isValid()) {
            $response
                ->setTitle(JText::_('COM_IDENTITYPROOF_FAIL'))
                ->setText(JText::_('COM_IDENTITYPROOF_ERROR_INVALID_ITEM'))
                ->failure();

            echo $response;
            $app->close();
        }

        try {

            // Get the model
            $model = $this->getModel();
            /** @var $model IdentityProofModelFile */

            $note = $model->getNote($fileId, $userId);

        } catch (Exception $e) {
            JLog::add($e->getMessage());
            throw new Exception($e->getMessage());
        }

        $response
            ->setTitle(JText::_('COM_IDENTITYPROOF_SUCCESS'))
            ->setData(array("note" => $note))
            ->success();

        echo $response;
        $app->close();
    }
}
