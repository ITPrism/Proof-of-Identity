<?php
/**
 * @package      Identityproof
 * @subpackage   Component
 * @author       Todor Iliev
 * @copyright    Copyright (C) 2016 Todor Iliev <todor@itprism.com>. All rights reserved.
 * @license      GNU General Public License version 3 or later; see LICENSE.txt
 */

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
     * @return  JForm   A JForm object on success, false on failure
     * @since   1.6
     */
    public function getForm($data = array(), $loadData = true)
    {
        // Get the form.
        $form = $this->loadForm($this->option . '.proof', 'proof', array('control' => 'jform', 'load_data' => $loadData));
        if (empty($form)) {
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
        $results = (array)$db->loadAssocList();

        return $results;
    }

    /**
     * Upload the file.
     *
     * @param array $tmpFile
     *
     * @throws Exception
     *
     * @return array
     */
    public function uploadFile($tmpFile)
    {
        jimport('joomla.filesystem.folder');
        jimport('joomla.filesystem.file');

        $app = JFactory::getApplication();
        /** @var $app JApplicationSite */

        $uploadedFile = Joomla\Utilities\ArrayHelper::getValue($tmpFile, 'tmp_name');
        $uploadedName = Joomla\Utilities\ArrayHelper::getValue($tmpFile, 'name');
        $errorCode    = Joomla\Utilities\ArrayHelper::getValue($tmpFile, 'error');

        // Load parameters.
        $params     = JComponentHelper::getParams($this->option);
        /** @var  $params Joomla\Registry\Registry */

        // Generate a folder name.
        $generatedName = Prism\Utilities\StringHelper::generateRandomString();
        $destFolder = JPath::clean($app->get('tmp_path') . DIRECTORY_SEPARATOR . (string)$generatedName);

        // Create a temporary folder.
        if (!JFolder::create($destFolder)) {
            throw new RuntimeException(JText::_('COM_IDENTITYPROOF_ERROR_FILE_CANT_BE_UPLOADED'));
        }

        // Create .htaccess file to deny the access for that folder.
        $htaccessFile = JPath::clean($destFolder . DIRECTORY_SEPARATOR . '.htaccess');
        $fileContent  = 'Deny from all';
        if (!JFile::write($htaccessFile, $fileContent)) {
            throw new RuntimeException(JText::_('COM_IDENTITYPROOF_ERROR_FILE_CANT_BE_UPLOADED'));
        }

        $file = new Prism\File\File();

        // Prepare size validator.
        $KB            = 1024 * 1024;
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

        $file
            ->addValidator($sizeValidator)
            ->addValidator($typeValidator)
            ->addValidator($serverValidator);

        // Validate the file
        if (!$file->isValid()) {
            throw new RuntimeException($file->getError());
        }

        // Generate temporary file name
        $ext = JFile::makeSafe(JFile::getExt($tmpFile['name']));

        // Generate a file name.
        $generatedName = Prism\Utilities\StringHelper::generateRandomString();
        $tmpDestFile   = JPath::clean($destFolder . DIRECTORY_SEPARATOR . $generatedName . '.' . $ext);

        // Prepare uploader object.
        $uploader = new Prism\File\Uploader\Local($uploadedFile);
        $uploader->setDestination($tmpDestFile);

        // Upload temporary file
        $file->setUploader($uploader);

        $file->upload();

        // Get file
        $tmpDestFile = JPath::clean($file->getFile());

        if (!is_file($tmpDestFile)) {
            throw new RuntimeException(JText::_('COM_IDENTITYPROOF_ERROR_FILE_CANT_BE_UPLOADED'));
        }

        // Set new permissions for the file.
        chmod($tmpDestFile, 0600);

        // Remove the temporary file that came from the form.
        if (JFile::exists($uploadedFile)) {
            JFile::delete($uploadedFile);
        }

        return $tmpDestFile;
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
        $title     = Joomla\Utilities\ArrayHelper::getValue($data, 'title');
        $file      = Joomla\Utilities\ArrayHelper::getValue($data, 'file');
        $filename  = basename($file);

        $userId    = JFactory::getUser()->get('id');

        if (!JFile::exists($file)) {
            throw new Exception(JText::_('COM_IDENTITYPROOF_ERROR_FILE_CANT_BE_UPLOADED'));
        }

        // Load the data from the file.
        $fileData = file_get_contents($file);

        if ($fileData !== null and $fileData !== '') {
            $keysData    = $this->generateKeys();

            // Get mime type and file size.
            $fileInfo = new finfo(FILEINFO_MIME_TYPE);
            $metaData = array(
                'filesize'  => filesize($file),
                'mime_type' => $fileInfo->file($file)
            );

            $fileData    = IdentityproofHelper::encrypt($keysData, $fileData);

            $params      = JComponentHelper::getParams('com_identityproof');
            $destination = JPath::clean($params->get('files_path') . DIRECTORY_SEPARATOR . $filename);

            file_put_contents($destination, $fileData);
            JFile::delete($file);

        } else {
            $metaData  = null;
            $keysData  = array();
        }

        // Unset the file data and clean the memory.
        $fileData    = null;

        // Encode the options.
        if ($metaData !== null and $metaData !== '') {
            $metaData = json_encode($metaData);
        }

        // Remove the file.
        JFile::delete($file);

        // Load a record from the database
        $row = $this->getTable();

        // Uploaded file will be always NEW.
        $isNew = true;

        $row->set('title', $title);
        $row->set('filename', $filename);
        $row->set('private', (array_key_exists('private', $keysData)) ? $keysData['private'] : null);
        $row->set('public', (array_key_exists('public', $keysData)) ? $keysData['public'] : null);
        $row->set('meta_data', $metaData);
        $row->set('user_id', $userId);

        $row->store(true);

        // Trigger the event onContentAfterSave.
        $this->triggerEventAfterSave($row, 'uploading', $isNew);

        return $row->get('id');
    }

    protected function generateKeys()
    {
        // Generate a password that will be used to encrypt the file.
        $length   = mt_rand(16, 32);
        $password = Prism\Utilities\StringHelper::generateRandomString($length);

        // Generate a salt.
        $length   = mt_rand(16, 32);
        $salt     = Prism\Utilities\StringHelper::generateRandomString($length);

        $options = array(
            'salt'     => (string)$salt,
            'password' => (string)$password
        );

        $chiper = new JCryptCipherRijndael256();
        $key    = $chiper->generateKey($options);

        return array(
            'private'  => $key->private,
            'public'   => $key->public
        );
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
        $file = Joomla\Utilities\ArrayHelper::toObject($file);

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
