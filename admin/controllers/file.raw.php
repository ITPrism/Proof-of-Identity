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
 * File controller class.
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
    
    public function download()
    {
        // Check for request forgeries.
        JSession::checkToken("post") or jexit(JText::_('JINVALID_TOKEN'));
        
        $fileId   = $this->input->post->get("file_id", 0, "int");

        $userId   = JFactory::getUser()->get("id");

        // Validate the user.
        if (!$userId) {
            $this->setRedirect(JRoute::_('index.php?option=com_identityproof&view=dashboard', false), JText::_('COM_IDENTITYPROOF_ERROR_SYSTEM'));
            return;
        }

        $params = JComponentHelper::getParams("com_identityproof");
        /** @var  $params Joomla\Registry\Registry */

        try {

            // Load file data.
            jimport("identityproof.file");
            $file = new IdentityProofFile(JFactory::getDbo());
            $file->load($fileId);

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

    public function getFormToken()
    {
        // Create response object
        jimport("itprism.response.json");
        $response = new ITPrismResponseJson();

        $response
            ->setTitle(JText::_('COM_IDENTITYPROOF_SUCCESS'))
            ->setData(array("token" => JSession::getFormToken()))
            ->success();

        echo $response;
        JFactory::getApplication()->close();
    }
}
