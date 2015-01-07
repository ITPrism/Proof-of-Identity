<?php
/**
 * @package      IdentityProof
 * @subpackage   Component
 * @author       Todor Iliev
 * @copyright    Copyright (C) 2015 Todor Iliev <todor@itprism.com>. All rights reserved.
 * @license      http://www.gnu.org/copyleft/gpl.html GNU/GPL
 */

// no direct access
defined('_JEXEC') or die;

/**
 * Get a list of items
 */
class IdentityProofModelProof extends JModelForm
{
    /**
     * Returns a reference to the a Table object, always creating it.
     *
     * @param   string $type    The table type to instantiate
     * @param   string $prefix A prefix for the table class name. Optional.
     * @param   array  $config Configuration array for model. Optional.
     *
     * @return  JTable  A database object
     * @since   1.6
     */
    public function getTable($type = 'File', $prefix = 'IdentityProofTable', $config = array())
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
            ->select("a.id, a.title, a.state, a.record_date, a.note")
            ->from($db->quoteName("#__identityproof_files", "a"))
            ->where("a.user_id = ". (int)$userId);

        $db->setQuery($query);
        $results = $db->loadAssocList();

        if (!$results) {
            $results = array();
        }

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
        jimport("joomla.filesystem.folder");
        jimport("joomla.filesystem.file");

        $app = JFactory::getApplication();
        /** @var $app JApplicationSite */

        $uploadedFile = JArrayHelper::getValue($tmpFile, 'tmp_name');
        $uploadedName = JArrayHelper::getValue($tmpFile, 'name');
        $errorCode    = JArrayHelper::getValue($tmpFile, 'error');

        // Load parameters.
        $params     = JComponentHelper::getParams($this->option);
        /** @var  $params Joomla\Registry\Registry */

        // Generate a folder name.
        jimport("itprism.string");
        $generatedName = new ITPrismString();
        $generatedName->generateRandomString();

        $destFolder = JPath::clean($app->get("tmp_path") . DIRECTORY_SEPARATOR . (string)$generatedName);

        // Create a temporary folder.
        if (!JFolder::create($destFolder)) {
            throw new RuntimeException(JText::_('COM_IDENTITYPROOF_ERROR_FILE_CANT_BE_UPLOADED'));
        }

        // Create .htaccess file to deny the access for that folder.
        $htaccessFile = JPath::clean($destFolder . DIRECTORY_SEPARATOR . ".htaccess");
        $fileContent  = "Deny from all";
        if (!JFile::write($htaccessFile, $fileContent)) {
            throw new RuntimeException(JText::_('COM_IDENTITYPROOF_ERROR_FILE_CANT_BE_UPLOADED'));
        }

        jimport("itprism.file");
        jimport("itprism.file.uploader.local");
        jimport("itprism.file.validator.size");
        jimport("itprism.file.validator.type");
        jimport("itprism.file.validator.server");

        $file = new ITPrismFile();

        // Prepare size validator.
        $KB            = 1024 * 1024;
        $fileSize      = (int)$app->input->server->get('CONTENT_LENGTH');
        $uploadMaxSize = $params->get("max_size") * $KB;

        // Prepare file size validator
        $sizeValidator = new ITPrismFileValidatorSize($fileSize, $uploadMaxSize);

        // Prepare server validator.
        $serverValidator = new ITPrismFileValidatorServer($errorCode, array(UPLOAD_ERR_NO_FILE));

        // Prepare image validator.
        $typeValidator = new ITPrismFileValidatorType($uploadedFile, $uploadedName);

        // Get allowed mime types from media manager options
        $mimeTypes = explode(",", $params->get("legal_types"));
        $mimeTypes = array_map("trim", $mimeTypes);

        $typeValidator->setMimeTypes($mimeTypes);

        // Get allowed image extensions from media manager options.
        $legalExtensions = explode(",", $params->get("legal_extensions"));
        $legalExtensions = array_map("trim", $legalExtensions);

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
        $generatedName = new ITPrismString();
        $generatedName->generateRandomString();

        $tmpDestFile = JPath::clean($destFolder . DIRECTORY_SEPARATOR . $generatedName . "." . $ext);

        // Prepare uploader object.
        $uploader = new ITPrismFileUploaderLocal($uploadedFile);
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
        $title     = JArrayHelper::getValue($data, "title");
        $file      = JArrayHelper::getValue($data, "file");
        $filename  = basename($file);

        $userId    = JFactory::getUser()->get("id");

        if (!JFile::exists($file)) {
            throw new Exception(JText::_('COM_IDENTITYPROOF_ERROR_FILE_CANT_BE_UPLOADED'));
        }

        // Load the data from the file.
        $fileData = file_get_contents($file);

        if (!empty($fileData)) {
            $keysData    = $this->generateKeys();

            // Get mime type and file size.
            $fileInfo = new finfo(FILEINFO_MIME_TYPE);
            $metaData = array(
                "filesize"  => filesize($file),
                "mime_type" => $fileInfo->file($file)
            );

            $fileData    = IdentityProofHelper::encrypt($keysData, $fileData);

            $params      = JComponentHelper::getParams("com_identityproof");
            $destination = JPath::clean($params->get("files_path") . DIRECTORY_SEPARATOR . $filename);

            file_put_contents($destination, $fileData);
            JFile::delete($file);

        } else {
            $metaData  = null;
            $keysData  = array();
        }

        // Unset the file data and clean the memory.
        $fileData    = null;

        // Encode the options.
        if (!empty($metaData)) {
            $metaData = json_encode($metaData);
        }

        // Remove the file.
        JFile::delete($file);

        // Load a record from the database
        $row = $this->getTable();

        $row->set("title", $title);
        $row->set("filename", $filename);
        $row->set("private", (!isset($keysData["private"])) ? null : $keysData["private"]);
        $row->set("public", (!isset($keysData["public"])) ? null : $keysData["public"]);
        $row->set("meta_data", $metaData);
        $row->set("user_id", $userId);

        $row->store(true);

        return $row->get("id");
    }

    protected function generateKeys()
    {
        // Generate a password that will be used to encrypt the file.
        jimport("itprism.string");
        $length   = rand(16, 32);
        $password = new ITPrismString();
        $password->generateRandomString($length);

        // Generate a salt.
        $length   = rand(16, 32);
        $salt = new ITPrismString();
        $salt->generateRandomString($length);

        $options = array(
            "salt"     => (string)$salt,
            "password" => (string)$password
        );

        $chiper = new JCryptCipherRijndael256();
        $key    = $chiper->generateKey($options);

        return array(
            "private"  => $key->private,
            "public"   => $key->public
        );
    }
}
