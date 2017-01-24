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
 * File controller class.
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
    
    public function download()
    {
        // Check for request forgeries.
        JSession::checkToken('post') or jexit(JText::_('JINVALID_TOKEN'));

        $app    = JFactory::getApplication();

        $fileId   = $this->input->post->get('file_id', 0, 'int');
        $userId   = JFactory::getUser()->get('id');

        // Validate the user.
        if (!$userId) {
            $this->setRedirect(JRoute::_('index.php?option=com_identityproof&view=dashboard', false), JText::_('COM_IDENTITYPROOF_ERROR_SYSTEM'));
            return;
        }

        $params = JComponentHelper::getParams('com_identityproof');
        /** @var  $params Joomla\Registry\Registry */

        try {
            // Load file data.
            $file = new Identityproof\File(JFactory::getDbo());
            $file->load($fileId);

            // Prepare meta data
            $mimeType   = $file->getMetaData('mime_type');

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

            $output = file_get_contents($destinationFile);

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
}
