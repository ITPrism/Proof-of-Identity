<?php
/**
 * @package      ProofOfIdentity
 * @subpackage   Files
 * @author       Todor Iliev
 * @copyright    Copyright (C) 2017 Todor Iliev <todor@itprism.com>. All rights reserved.
 * @license      GNU General Public License version 3 or later; see LICENSE.txt
 */

namespace Identityproof;

use Prism;

defined('JPATH_PLATFORM') or die;

/**
 * This class provides functionality that manage a file.
 *
 * @package      ProofOfIdentity
 * @subpackage   Files
 */
class File extends Prism\Database\Table
{
    protected $id;
    protected $title;
    protected $filename;
    protected $meta_data;
    protected $state;
    protected $note;
    protected $record_date;
    protected $user_id;

    /**
     * Load user data from database.
     *
     * <code>
     * $keys = array(
     *    "id" => 1,
     *    "user_id" => 2
     * );
     *
     * $file    = new Identityproof\File(\JFactory::getDbo());
     * $file->load($keys);
     * </code>
     *
     * @param int|array $keys
     * @param array $options
     */
    public function load($keys, array $options = array())
    {
        $query = $this->db->getQuery(true);

        $query
            ->select(
                'a.id, a.title, a.filename, a.meta_data, ' .
                'a.state, a.note, a.record_date, a.user_id'
            )
            ->from($this->db->quoteName('#__identityproof_files', 'a'));

        if (!is_array($keys)) {
            $query->where('a.id = ' . (int)$keys);
        } else {
            foreach ($keys as $key => $value) {
                $query->where($this->db->quoteName('a.'.$key) . '=' . $this->db->quote($value));
            }
        }

        $this->db->setQuery($query);
        $result = (array)$this->db->loadAssoc();

        if (!empty($result['meta_data'])) {
            $result['meta_data'] = json_decode($result['meta_data'], true);
        }

        $this->bind($result);
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
     * $user   = new Identityproof\File(\JFactory::getDbo());
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
        $filename    = (!$this->filename) ? 'NULL' : $this->db->quote($this->filename);
        $note        = (!$this->note) ? 'NULL' : $this->db->quote($this->note);

        if (!$this->meta_data) {
            $metaData    = 'NULL';
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
            ->insert($this->db->quoteName('#__identityproof_files'))
            ->set($this->db->quoteName('title') . '=' . $this->db->quote($this->title))
            ->set($this->db->quoteName('filename') . '=' . $filename)
            ->set($this->db->quoteName('meta_data') . '=' . $metaData)
            ->set($this->db->quoteName('state') . '=' . $this->db->quote($this->state))
            ->set($this->db->quoteName('note') . '=' . $note)
            ->set($this->db->quoteName('user_id') . '=' . (int)$this->user_id);

        $this->db->setQuery($query);
        $this->db->execute();

        $this->id = $this->db->insertid();
    }

    protected function updateObject()
    {
        $filename    = (!$this->filename) ? 'NULL' : $this->db->quote($this->filename);
        $note        = (!$this->note) ? 'NULL' : $this->db->quote($this->note);

        if (!$this->meta_data) {
            $metaData    = 'NULL';
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
            ->update($this->db->quoteName('#__identityproof_files'))
            ->set($this->db->quoteName('title') . '=' . $this->db->quote($this->title))
            ->set($this->db->quoteName('filename') . '=' . $filename)
            ->set($this->db->quoteName('meta_data') . '=' . $metaData)
            ->set($this->db->quoteName('state') . '=' . $this->db->quote($this->state))
            ->set($this->db->quoteName('note') . '=' . $note)
            ->set($this->db->quoteName('record_date') . '=' . $this->db->quote($this->record_date))
            ->set($this->db->quoteName('user_id') . '=' . (int)$this->user_id);

        $this->db->setQuery($query);
        $this->db->execute();
    }

    /**
     * Return record ID.
     *
     * <code>
     * $fileId  = 1;
     *
     * $file    = new Identityproof\File(\JFactory::getDbo());
     * $file->load($fileId);
     *
     * if (!$file->getId()) {
     * ...
     * }
     * </code>
     *
     * @return int
     */
    public function getId()
    {
        return (int)$this->id;
    }

    /**
     * Return state value.
     *
     * <code>
     * $fileId  = 1;
     *
     * $file    = new Identityproof\File(\JFactory::getDbo());
     * $file->load($fileId);
     *
     * $state = $file->getState();
     * </code>
     *
     * @return int File state - 0 = not reviewed; 1 = reviewed; -2 = trashed;
     */
    public function getState()
    {
        return (int)$this->state;
    }

    /**
     * Return the note left by the administrator.
     *
     * <code>
     * $fileId  = 1;
     *
     * $file    = new Identityproof\File(\JFactory::getDbo());
     * $file->load($fileId);
     *
     * $note = $file->getNote();
     * </code>
     *
     * @return string
     */
    public function getNote()
    {
        return (string)$this->note;
    }

    /**
     * Return the title of the file.
     *
     * <code>
     * $fileId  = 1;
     *
     * $file    = new Identityproof\File(\JFactory::getDbo());
     * $file->load($fileId);
     *
     * $title = $file->getTitle();
     * </code>
     *
     * @return string
     */
    public function getTitle()
    {
        return (string)$this->title;
    }

    /**
     * Return the filename.
     *
     * <code>
     * $fileId  = 1;
     *
     * $file    = new Identityproof\File(\JFactory::getDbo());
     * $file->load($fileId);
     *
     * $filename = $file->getFilename();
     * </code>
     *
     * @return string
     */
    public function getFilename()
    {
        return (string)$this->filename;
    }

    /**
     * Return user ID.
     *
     * <code>
     * $fileId  = 1;
     *
     * $file    = new Identityproof\File(\JFactory::getDbo());
     * $file->load($fileId);
     *
     * $userId = $file->getUserId();
     * </code>
     *
     * @return int
     */
    public function getUserId()
    {
        return (int)$this->user_id;
    }

    /**
     * Return the meta data value.
     *
     * <code>
     * $fileId  = 1;
     *
     * $file    = new Identityproof\File(\JFactory::getDbo());
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
        return array_key_exists($index, $this->meta_data) ? $this->meta_data[$index] : null;
    }
}
