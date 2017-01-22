<?php
/**
 * @package      Identityproof
 * @subpackage   Component
 * @author       Todor Iliev
 * @copyright    Copyright (C) 2017 Todor Iliev <todor@itprism.com>. All rights reserved.
 * @license      GNU General Public License version 3 or later; see LICENSE.txt
 */

// No direct access
defined('_JEXEC') or die;

/**
 * Data controller class.
 *
 * @package        Identityproof
 * @subpackage     Component
 * @since          1.6
 */
class IdentityproofControllerFile extends JControllerLegacy
{
    /**
     * Method to get a model object, loading it if required.
     *
     * @param    string $name   The model name. Optional.
     * @param    string $prefix The class prefix. Optional.
     * @param    array  $config Configuration array for model. Optional.
     *
     * @return   IdentityproofModelFile    The model.
     * @since    1.5
     */
    public function getModel($name = 'File', $prefix = 'IdentityproofModel', $config = array('ignore_request' => true))
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
        $response = new Prism\Response\Json();

        $fileId = $this->input->post->get('id');
        $userId = JFactory::getUser()->get('id');

        // Create validator object.
        $validator = new Identityproof\Validator\File\Owner(JFactory::getDbo(), $fileId, $userId);

        if (!$userId) {
            $response
                ->setTitle(JText::_('COM_IDENTITYPROOF_FAILURE'))
                ->setText(JText::_('COM_IDENTITYPROOF_ERROR_NOT_LOG_IN'))
                ->failure();

            echo $response;
            $app->close();
        }

        if (!$validator->isValid()) {
            $response
                ->setTitle(JText::_('COM_IDENTITYPROOF_FAILURE'))
                ->setText(JText::_('COM_IDENTITYPROOF_ERROR_INVALID_ITEM'))
                ->failure();

            echo $response;
            $app->close();
        }

        try {
            $model = $this->getModel();
            /** @var $model IdentityproofModelFile */

            $model->remove($fileId, $userId);

        } catch (RuntimeException $e) {
            JLog::add($e->getMessage(), JLog::ERROR, 'com_identityproof');
            $response
                ->setTitle(JText::_('COM_IDENTITYPROOF_FAILURE'))
                ->setText(JText::_('COM_IDENTITYPROOF_ERROR_INVALID_ITEM'))
                ->failure();

            echo $response;
            $app->close();

        } catch (Exception $e) {
            JLog::add($e->getMessage(), JLog::ERROR, 'com_identityproof');
            throw new Exception($e->getMessage());
        }

        $response
            ->setTitle(JText::_('COM_IDENTITYPROOF_SUCCESS'))
            ->setText(JText::_('COM_IDENTITYPROOF_FILE_DELETED'))
            ->setData(array('file_id' => $fileId))
            ->success();

        echo $response;
        $app->close();
    }

    public function download()
    {
        // Check for request forgeries.
        JSession::checkToken('post') or jexit(JText::_('JINVALID_TOKEN'));

        $app    = JFactory::getApplication();
        
        $user   = JFactory::getUser();
        
        $data   = $this->input->post->get('jform', array(), 'array');

        $fileId = Joomla\Utilities\ArrayHelper::getValue($data, 'file_id', 0, 'int');
        $userId = $user->get('id');

        // Validate the user.
        if (!$userId) {
            $this->setRedirect(JRoute::_('index.php?option=com_users&view=login', false), JText::_('COM_IDENTITYPROOF_ERROR_NOT_LOG_IN'), 'warning');
            return;
        }

        // Validate the item owner.
        $validator = new Identityproof\Validator\File\Owner(JFactory::getDbo(), $fileId, $userId);
        if (!$validator->isValid()) {
            $this->setRedirect(JRoute::_(IdentityproofHelperRoute::getProofRoute(), false), JText::_('COM_IDENTITYPROOF_ERROR_INVALID_ITEM'), 'warning');
            return;
        }

        // Validate the password.
        $password = Joomla\Utilities\ArrayHelper::getValue($data, 'password', null, 'string');
        $match    = JUserHelper::verifyPassword($password, $user->get('password'), $userId);
        if (!$match) {
            $this->setRedirect(JRoute::_(IdentityproofHelperRoute::getProofRoute(), false), JText::_('COM_IDENTITYPROOF_ERROR_INVALID_PASSWORD'), 'warning');
            return;
        }

        $params = JComponentHelper::getParams('com_identityproof');
        /** @var  $params Joomla\Registry\Registry */

        try {
            $keys = array(
                'id' => $fileId,
                'user_id' => $userId
            );

            // Load file data.
            $file = new Identityproof\File(JFactory::getDbo());
            $file->load($keys);

            // Decrypt the file.
            $sourceFile         = JPath::clean($params->get('files_path') .'/'. $file->getFilename(), '/');

            $generatedName      = Prism\Utilities\StringHelper::generateRandomString();
            $destinationFolder  = JPath::clean($app->get('tmp_path') .'/'. (string)$generatedName, '/');
            $destinationFile    = JPath::clean($destinationFolder .'/'. $file->getFilename(), '/');

            // Create a temporary folder.
            if (!JFolder::create($destinationFolder, 0740)) {
                throw new RuntimeException(JText::sprintf('COM_IDENTITYPROOF_ERROR_FOLDER_CANNOT_BE_CREATED_S', $destinationFolder));
            }

            Defuse\Crypto\File::decryptFileWithPassword($sourceFile, $destinationFile, $app->get('secret'));

            $output       = file_get_contents($destinationFile);

            // Prepare meta data
//            $fileSize   = $file->getMetaData('filesize');
            $mimeType   = $file->getMetaData('mime_type');

        } catch (Exception $e) {
            JLog::add($e->getMessage(), JLog::ERROR, 'com_identityproof');
            throw new Exception(JText::_('COM_IDENTITYPROOF_ERROR_SYSTEM'));
        }

        $app->setHeader('Content-Type', $mimeType, true);
        $app->setHeader('Cache-Control', 'must-revalidate, post-check=0, pre-check=0', true);
        $app->setHeader('Content-Transfer-Encoding', 'binary', true);
        $app->setHeader('Pragma', 'no-cache', true);
        $app->setHeader('Expires', '0', true);
        $app->setHeader('Content-Disposition', 'attachment; filename=' . $file->getFilename(), true);

        echo $output;

        $fileSize   = ob_get_length();
        $app->setHeader('Content-Length', $fileSize, true);

        $doc = JFactory::getDocument();
        $doc->setMimeEncoding($mimeType);

        $app->sendHeaders();

        if (JFolder::exists($destinationFolder)) {
            JFolder::delete($destinationFolder);
        }
        
        $app->close();
    }

    /**
     * Return a not about resource.
     */
    public function note()
    {
        $app = JFactory::getApplication();

        // Create response object
        $response = new Prism\Response\Json();

        $fileId = $this->input->get->get('id');

        $userId = JFactory::getUser()->get('id');

        // Create validator object.
        $validator = new Identityproof\Validator\File\Owner(JFactory::getDbo(), $fileId, $userId);

        if (!$userId) {
            $response
                ->setTitle(JText::_('COM_IDENTITYPROOF_FAILURE'))
                ->setText(JText::_('COM_IDENTITYPROOF_ERROR_NOT_LOG_IN'))
                ->failure();

            echo $response;
            $app->close();
        }

        if (!$validator->isValid()) {
            $response
                ->setTitle(JText::_('COM_IDENTITYPROOF_FAILURE'))
                ->setText(JText::_('COM_IDENTITYPROOF_ERROR_INVALID_ITEM'))
                ->failure();

            echo $response;
            $app->close();
        }

        try {
            $model = $this->getModel();
            /** @var $model IdentityproofModelFile */

            $note = $model->getNote($fileId, $userId);
        } catch (Exception $e) {
            JLog::add($e->getMessage(), JLog::ERROR, 'com_identityproof');
            throw new Exception($e->getMessage());
        }

        $response
            ->setTitle(JText::_('COM_IDENTITYPROOF_SUCCESS'))
            ->setData(array('note' => $note))
            ->success();

        echo $response;
        $app->close();
    }
}
