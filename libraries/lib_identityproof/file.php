<?php
/**
 * @package      ProofOfIdentity
 * @subpackage   Files
 * @author       Todor Iliev
 * @copyright    Copyright (C) 2015 Todor Iliev <todor@itprism.com>. All rights reserved.
 * @license      http://www.gnu.org/copyleft/gpl.html GNU/GPL
 */

defined('JPATH_PLATFORM') or die;

/**
 * This class provides functionality that manage a file.
 *
 * @package      ProofOfIdentity
 * @subpackage   Files
 */
class IdentityProofFile
{
    protected $id;
    protected $title;
    protected $filename;
    protected $private;
    protected $public;
    protected $meta_data;
    protected $state;
    protected $note;
    protected $record_date;
    protected $user_id;

    /**
     * Database driver.
     *
     * @var JDatabaseDriver
     */
    protected $db;

    /**
     * Initialize the object.
     *
     * <code>
     * $file    = new IdentityProofFile(JFactory::getDbo());
     * </code>
     *
     * @param JDatabaseDriver  $db
     */
    public function __construct(JDatabaseDriver $db = null)
    {
        $this->db = $db;
    }

    /**
     * Set the database object.
     *
     * <code>
     * $file    = new IdentityProofFile();
     * $file->setDb(JFactory::getDbo());
     * </code>
     *
     * @param JDatabaseDriver $db
     *
     * @return self
     */
    public function setDb(JDatabaseDriver $db)
    {
        $this->db = $db;

        return $this;
    }

    /**
     * Load user data from database.
     *
     * <code>
     * $keys = array(
     *    "id" => 1,
     *    "user_id" => 2
     * );
     *
     * $file    = new IdentityProofFile();
     * $file->setDb(JFactory::getDbo());
     * $file->load($keys);
     * </code>
     *
     * @param int|array $keys
     */
    public function load($keys)
    {
        $query = $this->db->getQuery(true);

        $query
            ->select(
                "a.id, a.title, a.filename, a.private, a.public, a.meta_data, " .
                "a.state, a.note, a.record_date, a.user_id"
            )
            ->from($this->db->quoteName("#__identityproof_files", "a"));

        if (is_array($keys) and !empty($keys)) {
            foreach ($keys as $key => $value) {
                $query->where($this->db->quoteName("a.".$key) . "=" . $this->db->quote($value));
            }
        } else {
            $query->where("a.id = " . (int)$keys);
        }

        $this->db->setQuery($query);
        $result = $this->db->loadAssoc();

        if (!$result) {
            $result = array();
        }

        if (!empty($result["meta_data"])) {
            $result["meta_data"] = json_decode($result["meta_data"], true);
        }

        $this->bind($result);
    }

    /**
     * Set data to object properties.
     *
     * <code>
     * $data = array(
     *    "user_id"  => 1,
     *    "title"    => "My ID"
     * );
     *
     * $file    = new IdentityProofFile();
     * $file->bind($data);
     * </code>
     *
     * @param array $data
     * @param array $ignored
     */
    public function bind($data, $ignored = array())
    {
        foreach ($data as $key => $value) {
            if (!in_array($key, $ignored)) {
                $this->$key = $value;
            }
        }
    }
    
    /**
     * Store the data in database.
     *
     * <code>
     * $data = (
     *    "user_id"  => 1,
     *    "title"    => "My ID"
     * );
     *
     * $user   = new IdentityProofFile(JFactory::getDbo());
     * $user->bind($data);
     * $user->store();
     * </code>
     */
    public function store()
    {
        if (!$this->id) { // Insert
            $this->insertObject();
        } else { // Update
            $this->updateObject();
        }
    }

    protected function insertObject()
    {
        $filename    = (!$this->filename) ? "NULL" : $this->db->quote($this->filename);
        $private     = (!$this->private) ? "NULL" : $this->db->quote($this->private);
        $public      = (!$this->public) ? "NULL" : $this->db->quote($this->public);
        $note        = (!$this->note) ? "NULL" : $this->db->quote($this->note);

        if (!$this->meta_data) {
            $metaData    = "NULL";
        } else {

            if (is_array($this->meta_data)) {
                $metaData = json_encode($this->meta_data);
                $metaData = $this->db->quote($metaData);
            } else {
                $metaData = $this->db->quote($this->meta_data);
            }
        }

        $query = $this->db->getQuery(true);
        $query
            ->insert($this->db->quoteName("#__identityproof_files"))
            ->set($this->db->quoteName("title") . "=" . $this->db->quote($this->title))
            ->set($this->db->quoteName("filename") . "=" . $filename)
            ->set($this->db->quoteName("private") . "=" . $private)
            ->set($this->db->quoteName("public") . "=" . $public)
            ->set($this->db->quoteName("meta_data") . "=" . $metaData)
            ->set($this->db->quoteName("state") . "=" . $this->db->quote($this->state))
            ->set($this->db->quoteName("note") . "=" . $note)
            ->set($this->db->quoteName("user_id") . "=" . (int)$this->user_id);

        $this->db->setQuery($query);
        $this->db->execute();

        $this->id = $this->db->insertid();
    }

    protected function updateObject()
    {
        $filename    = (!$this->filename) ? "NULL" : $this->db->quote($this->filename);
        $private     = (!$this->private) ? "NULL" : $this->db->quote($this->private);
        $public      = (!$this->public) ? "NULL" : $this->db->quote($this->public);
        $note        = (!$this->note) ? "NULL" : $this->db->quote($this->note);

        if (!$this->meta_data) {
            $metaData    = "NULL";
        } else {

            if (is_array($this->meta_data)) {
                $metaData = json_encode($this->meta_data);
                $metaData = $this->db->quote($metaData);
            } else {
                $metaData = $this->db->quote($this->meta_data);
            }

        }

        $query = $this->db->getQuery(true);
        $query
            ->update($this->db->quoteName("#__identityproof_files"))
            ->set($this->db->quoteName("title") . "=" . $this->db->quote($this->title))
            ->set($this->db->quoteName("filename") . "=" . $filename)
            ->set($this->db->quoteName("private") . "=" . $private)
            ->set($this->db->quoteName("public") . "=" . $public)
            ->set($this->db->quoteName("meta_data") . "=" . $metaData)
            ->set($this->db->quoteName("state") . "=" . $this->db->quote($this->state))
            ->set($this->db->quoteName("note") . "=" . $note)
            ->set($this->db->quoteName("record_date") . "=" . $this->db->quote($this->record_date))
            ->set($this->db->quoteName("user_id") . "=" . (int)$this->user_id);

        $this->db->setQuery($query);
        $this->db->execute();
    }

    /**
     * Return record ID.
     *
     * <code>
     * $fileId  = 1;
     *
     * $file    = new IdentityProofFile(JFactory::getDbo());
     * $file->load($fileId);
     *
     * if (!$file->getId()) {
     * ...
     * }
     * </code>
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Return state value.
     *
     * <code>
     * $fileId  = 1;
     *
     * $file    = new IdentityProofFile(JFactory::getDbo());
     * $file->load($fileId);
     *
     * $state = $file->getState();
     * </code>
     */
    public function getState()
    {
        return $this->state;
    }

    /**
     * Return the title of the file.
     *
     * <code>
     * $fileId  = 1;
     *
     * $file    = new IdentityProofFile(JFactory::getDbo());
     * $file->load($fileId);
     *
     * $title = $file->getTitle();
     * </code>
     */
    public function getTitle()
    {
        return (string)$this->title;
    }

    /**
     * Return the private key.
     *
     * <code>
     * $fileId  = 1;
     *
     * $file    = new IdentityProofFile(JFactory::getDbo());
     * $file->load($fileId);
     *
     * $privateKey = $file->getPrivate();
     * </code>
     *
     * @return mixed
     */
    public function getPrivate()
    {
        return $this->private;
    }

    /**
     * Return the public key.
     *
     * <code>
     * $fileId  = 1;
     *
     * $file    = new IdentityProofFile(JFactory::getDbo());
     * $file->load($fileId);
     *
     * $publicKey = $file->getPublic();
     * </code>
     *
     * @return mixed
     */
    public function getPublic()
    {
        return $this->public;
    }

    /**
     * Return the filename.
     *
     * <code>
     * $fileId  = 1;
     *
     * $file    = new IdentityProofFile(JFactory::getDbo());
     * $file->load($fileId);
     *
     * $filename = $file->getFilename();
     * </code>
     */
    public function getFilename()
    {
        return (string)$this->filename;
    }

    /**
     * Return the meta data value.
     *
     * <code>
     * $fileId  = 1;
     *
     * $file    = new IdentityProofFile(JFactory::getDbo());
     * $file->load($fileId);
     *
     * $data = $file->getMetaData();
     * </code>
     *
     * @param mixed $index That can be "filesize" or "mime_type".
     *
     * @return null|string
     */
    public function getMetaData($index)
    {
        return (isset($this->meta_data[$index])) ? $this->meta_data[$index] : null;
    }

    /**
     * Returns an associative array of object properties.
     *
     * <code>
     * $keys = array(
     *  "user_id" => 1
     * );
     *
     * $file    = new IdentityProofFile();
     * $file->setDb(JFactory::getDbo());
     * $file->load($keys);
     *
     * $properties = $file->getProperties();
     * </code>
     *
     * @return  array
     */
    public function getProperties()
    {
        $vars = get_object_vars($this);

        foreach ($vars as $key => $value) {
            if (strcmp("db", $key) == 0) {
                unset($vars[$key]);
                break;
            }
        }

        return $vars;
    }
}
