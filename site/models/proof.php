<?php
/**
 * @package      Identityproof
 * @subpackage   Component
 * @author       Todor Iliev
 * @copyright    Copyright (C) 2017 Todor Iliev <todor@itprism.com>. All rights reserved.
 * @license      GNU General Public License version 3 or later; see LICENSE.txt
 */

use Joomla\Registry\Registry;
use Joomla\Utilities\ArrayHelper;
use Defuse\Crypto;

// no direct access
defined('_JEXEC') or die;

class IdentityproofModelProof extends JModelForm
{
    /**
     * Returns a reference to the a Table object, always creating it.
     *
     * @param   string $type    The table type to instantiate
     * @param   string $prefix A prefix for the table class name. Optional.
     * @param   array  $config Configuration array for model. Optional.
     *
     * @return  IdentityproofTableFile  A database object
     * @since   1.6
     */
    public function getTable($type = 'File', $prefix = 'IdentityproofTable', $config = array())
    {
        return JTable::getInstance($type, $prefix, $config);
    }

    /**
     * Method to auto-populate the model state.
     *
     * Note. Calling getState in this method will result in recursion.
     *
     * @since    1.6
     */
    protected function populateState()
    {
        parent::populateState();

        $app = JFactory::getApplication();
        /** @var $app JApplicationSite */

        // Load the parameters.
        $value = $app->getParams($this->option);
        $this->setState('params', $value);
    }

    /**
     * Method to get the record form.
     *
     * @param   array   $data     An optional array of data for the form to interrogate.
     * @param   boolean $loadData True if the form is to load its own data (default case), false if not.
     *
     * @return  JForm|bool   A JForm object on success, false on failure
     * @since   1.6
     */
    public function getForm($data = array(), $loadData = true)
    {
        // Get the form.
        $form = $this->loadForm($this->option . '.proof', 'proof', array('control' => 'jform', 'load_data' => $loadData));
        if (!$form) {
            return false;
        }

        return $form;
    }

    /**
     * Method to get the data that should be injected in the form.
     *
     * @return  mixed   The data for the form.
     * @since   1.6
     */
    protected function loadFormData()
    {
        // Check the session for previously entered form data.
        $data = JFactory::getApplication()->getUserState($this->option . '.edit.proof.data', array());
        if (empty($data)) {
            $data = $this->getItem();
        }

        return $data;
    }

    public function getItem()
    {
        return array();
    }

    public function getFiles($userId)
    {
        $db     = $this->getDbo();
        $query  = $db->getQuery(true);

        $query
            ->select('a.id, a.title, a.state, a.record_date, a.note')
            ->from($db->quoteName('#__identityproof_files', 'a'))
            ->where('a.user_id = '. (int)$userId);

        $db->setQuery($query);

        return (array)$db->loadAssocList();
    }

    /**
     * Upload the file.
     *
     * @param array $uploadedFileData
     *
     * @throws Exception
     *
     * @return array
     */
    public function uploadFile($uploadedFileData)
    {
        $app = JFactory::getApplication();
        /** @var $app JApplicationSite */

        $uploadedFile = ArrayHelper::getValue($uploadedFileData, 'tmp_name');
        $uploadedName = ArrayHelper::getValue($uploadedFileData, 'name');
        $errorCode    = ArrayHelper::getValue($uploadedFileData, 'error');

        // Load parameters.
        $params     = JComponentHelper::getParams($this->option);
        /** @var  $params Joomla\Registry\Registry */

        // Generate a folder name.
        $generatedName      = Prism\Utilities\StringHelper::generateRandomString();
        $destinationFolder  = JPath::clean($app->get('tmp_path') .'/'. (string)$generatedName, '/');

        // Create a temporary folder.
        if (!JFolder::create($destinationFolder, 0740)) {
            throw new RuntimeException(JText::sprintf('COM_IDENTITYPROOF_ERROR_FOLDER_CANNOT_BE_CREATED_S', $destinationFolder));
        }

        // Create .htaccess file to deny the access for that folder.
        $htaccessFile = JPath::clean($destinationFolder . '/.htaccess', '/');
        $fileContent  = 'Deny from all';
        if (!JFile::write($htaccessFile, $fileContent)) {
            throw new RuntimeException(JText::sprintf('COM_IDENTITYPROOF_ERROR_FILE_CANNOT_BE_CREATED_S', $htaccessFile));
        }
        
        // Prepare size validator.
        $KB            = pow(1024, 2);
        $fileSize      = (int)$app->input->server->get('CONTENT_LENGTH');
        $uploadMaxSize = $params->get('max_size') * $KB;

        // Prepare file size validator
        $sizeValidator   = new Prism\File\Validator\Size($fileSize, $uploadMaxSize);

        // Prepare server validator.
        $serverValidator = new Prism\File\Validator\Server($errorCode, array(UPLOAD_ERR_NO_FILE));

        // Prepare image validator.
        $typeValidator   = new Prism\File\Validator\Type($uploadedFile, $uploadedName);

        // Get allowed mime types from media manager options
        $mimeTypes = explode(',', $params->get('legal_types'));
        $mimeTypes = array_map('trim', $mimeTypes);

        $typeValidator->setMimeTypes($mimeTypes);

        // Get allowed image extensions from media manager options.
        $legalExtensions = explode(',', $params->get('legal_extensions'));
        $legalExtensions = array_map('trim', $legalExtensions);

        $typeValidator->setLegalExtensions($legalExtensions);

        $file = new Prism\File\File($uploadedFile);
        $file
            ->addValidator($sizeValidator)
            ->addValidator($typeValidator)
            ->addValidator($serverValidator);

        // Validate the file
        if (!$file->isValid()) {
            throw new RuntimeException($file->getError());
        }

        // Generate temporary file name
        $filename = Prism\Utilities\StringHelper::generateRandomString(16);
        
        // Upload the file.
        $filesystemOptions = new Registry;
        $filesystemOptions->set('filename', $filename);

        $filesystemLocal = new Prism\Filesystem\Adapter\Local($destinationFolder);
        $storedFile      = $filesystemLocal->upload($uploadedFileData, $filesystemOptions);

        if (!is_file($storedFile)) {
            throw new RuntimeException(JText::_('COM_IDENTITYPROOF_ERROR_FILE_CANT_BE_UPLOADED'));
        }

        // Set new permissions for the file.
        chmod($storedFile, 0640);

        // Remove the temporary folder.
        if (JFile::exists($uploadedFile)) {
            JFile::delete($uploadedFile);
        }

        return $storedFile;
    }

    /**
     * Save data in the database.
     *
     * @param array $data   The data of item
     *
     * @throws Exception
     * @return    int      Item ID
     */
    public function save($data)
    {
        $title       = ArrayHelper::getValue($data, 'title');
        $sourceFile  = ArrayHelper::getValue($data, 'file');
        $filename    = basename($sourceFile);

        $userId      = JFactory::getUser()->get('id');

        if (!JFile::exists($sourceFile)) {
            throw new RuntimeException(JText::_('COM_IDENTITYPROOF_ERROR_FILE_CANT_BE_UPLOADED'));
        }

        // Get mime type and file size.
        $file     = new Prism\File\File($sourceFile);
        $fileData = $file->extractFileData();

        $metaData = json_encode(array(
            'filesize'  => $fileData['filesize'],
            'mime_type' => $fileData['mime']
        ));

        $app             = JFactory::getApplication();

        $params          = JComponentHelper::getParams('com_identityproof');
        $destinationFile = JPath::clean($params->get('files_path') .'/'. $filename, '/');

        Crypto\File::encryptFileWithPassword($sourceFile, $destinationFile, $app->get('secret'));

        // Remove the temporary folder.
        if (JFile::exists($sourceFile)) {
            JFile::delete($sourceFile);
        }

        // Load a record from the database
        $row = $this->getTable();

        // Uploaded file will be always NEW.
        $isNew = true;

        $row->set('title', $title);
        $row->set('filename', $filename);
        $row->set('meta_data', $metaData);
        $row->set('user_id', $userId);

        $row->store(true);

        // Trigger the event onContentAfterSave.
        $this->triggerEventAfterSave($row, 'uploading', $isNew);

        return $row->get('id');
    }

    /**
     * This method executes the event onContentAfterSave.
     *
     * @param IdentityproofTableFile $table
     * @param string $context
     * @param bool $isNew
     *
     * @throws Exception
     */
    protected function triggerEventAfterSave($table, $context, $isNew = false)
    {
        // Get properties
        $file = $table->getProperties();
        $file = ArrayHelper::toObject($file);

        // Generate context
        $context = $this->option . '.' . $context;

        // Include the content plugins for the change of state event.
        $dispatcher = JEventDispatcher::getInstance();
        JPluginHelper::importPlugin('content');

        // Trigger the onContentAfterSave event.
        $results = $dispatcher->trigger('onContentAfterSave', array($context, &$file, $isNew));

        if (in_array(false, $results, true)) {
            throw new RuntimeException(JText::_('COM_IDENTITYPROOF_ERROR_DURING_UPLOADING_FILE_PROCESS'));
        }
    }
}
