<?php
/**
 * @package      ProofOfIdentity
 * @subpackage   Component
 * @author       Todor Iliev
 * @copyright    Copyright (C) 2016 Todor Iliev <todor@itprism.com>. All rights reserved.
 * @license      GNU General Public License version 3 or later; see LICENSE.txt
 */

// no direct access
defined('_JEXEC') or die;

class IdentityproofModelNotification extends JModelAdmin
{
    /**
     * Returns a reference to the a Table object, always creating it.
     *
     * @param   string $type   The table type to instantiate
     * @param   string $prefix A prefix for the table class name. Optional.
     * @param   array  $config Configuration array for model. Optional.
     *
     * @return  JTable  A database object
     * @since   1.6
     */
    public function getTable($type = 'File', $prefix = 'IdentityproofTable', $config = array())
    {
        return JTable::getInstance($type, $prefix, $config);
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
        $form = $this->loadForm($this->option . '.notification', 'notification', array('control' => 'jform', 'load_data' => $loadData));
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
        $data = JFactory::getApplication()->getUserState($this->option . '.edit.notification.data', array());
        if (empty($data)) {
            $data = $this->getItem();
        }

        return $data;
    }

    /**
     * Method to get a single record.
     *
     * @param   integer $pk The id of the primary key.
     *
     * @return  mixed    Object on success, false on failure.
     *
     * @since   12.2
     */
    public function getItem($pk = null)
    {
        $pk = ($pk !== null and (int)$pk > 0) ? $pk : (int)$this->getState($this->getName() . '.id');

        $db = $this->getDbo();
        $query = $db->getQuery(true);

        $query
            ->select('a.id, a.note')
            ->from($db->quoteName('#__identityproof_files', 'a'))
            ->where('a.id = ' . (int)$pk);

        $db->setQuery($query);
        $result = $db->loadObject();

        return $result;
    }

    /**
     * Save data into the DB
     *
     * @param int $id
     * @param string $note
     */
    public function leaveNotice($id, $note)
    {
        $db     = $this->getDbo();

        $note   = (!$note) ? 'NULL' : $db->quote($note);

        $query  = $db->getQuery(true);

        $query
            ->update($db->quoteName('#__identityproof_files'))
            ->set($db->quoteName('note') .'='. $note)
            ->where($db->quoteName('id') .'='. (int)$id);

        $db->setQuery($query);
        $db->execute();

        // Trigger leaving notice event
        if (strcmp($note, 'NULL') !== 0) {
            $context = $this->option . '.' . $this->name;

            // Include the content plugins for the change of state event.
            JPluginHelper::importPlugin('content');

            // Trigger the onContentLeaveNote event.
            $dispatcher = JEventDispatcher::getInstance();
            $result     = $dispatcher->trigger('onContentLeaveNote', array($context, $id));

            if (in_array(false, $result, true)) {
                throw new RuntimeException(JText::_('COM_IDENTITYPROOF_ERROR_TRIGGERING_PLUGIN'));
            }
        }
    }

    /**
     * Load and return the notice from database.
     *
     * @param int $id
     *
     * @return string
     */
    public function getNotice($id)
    {
        $db     = $this->getDbo();

        $query  = $db->getQuery(true);

        $query
            ->select('a.note')
            ->from($db->quoteName('#__identityproof_files', 'a'))
            ->where('a.id = '. (int)$id);

        $db->setQuery($query, 0, 1);
        $result = $db->loadResult();

        if (!$result) {
            $result = '';
        }

        return $result;
    }
}
