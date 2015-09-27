<?php
/**
 * @package      ProofOfIdentity
 * @subpackage   Component
 * @author       Todor Iliev
 * @copyright    Copyright (C) 2015 Todor Iliev <todor@itprism.com>. All rights reserved.
 * @license      GNU General Public License version 3 or later; see LICENSE.txt
 */

// no direct access
defined('_JEXEC') or die;

class IdentityProofModelUser extends JModelAdmin
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
    public function getTable($type = 'User', $prefix = 'IdentityProofTable', $config = array())
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
        $form = $this->loadForm($this->option . '.user', 'user', array('control' => 'jform', 'load_data' => $loadData));
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
        $data = JFactory::getApplication()->getUserState($this->option . '.edit.user.data', array());
        if (empty($data)) {
            $data = $this->getItem();
        }

        return $data;
    }

    /**
     * Method to get a single record.
     *
     * @param   integer  $pk  The id of the primary key.
     *
     * @return  mixed    Object on success, false on failure.
     *
     * @since   12.2
     */
    public function getItem($pk = null)
    {
        $pk = (!empty($pk)) ? $pk : (int) $this->getState($this->getName() . '.id');

        $db = $this->getDbo();

        $query = $db->getQuery(true);

        $query
            ->select(
                "a.id, a.state, " .
                "b.name"
            )
            ->from($db->quoteName("#__identityproof_users", "a"))
            ->leftJoin($db->quoteName("#__users", "b") . " ON a.id = b.id")
            ->where("a.id = " . (int)$pk);

        $db->setQuery($query);
        $result = $db->loadObject();

        return $result;
    }

    /**
     * Save data into the DB
     *
     * @param array $data The data of item
     *
     * @return    int      Item ID
     */
    public function save($data)
    {
        $id          = Joomla\Utilities\ArrayHelper::getValue($data, "id");
        $state       = Joomla\Utilities\ArrayHelper::getValue($data, "state");

        // Load a record from the database
        $row = $this->getTable();
        $row->load($id);

        $row->set("state", $state);

        $row->store(true);

        return $row->get("id");
    }

    /**
     * Method to change the approved state of one or more records.
     *
     * @param   array   $pks   A list of the primary keys to change.
     * @param   integer $value The value of the approved state.
     *
     * @throws Exception
     */
    public function verify($pks, $value)
    {
        if (!is_array($pks)) {
            throw new InvalidArgumentException(JText::_("COM_IDENTITYPROOF_ERROR_INVALID_PARAMETER"));
        }

        Joomla\Utilities\ArrayHelper::toInteger($pks);

        if (!$pks) {
            return;
        }

        $db = JFactory::getDbo();

        $query = $db->getQuery(true);
        $query
            ->update($db->quoteName("#__identityproof_users"))
            ->set($db->quoteName("state") ." = " . (int)$value)
            ->where($db->quoteName("id") ." IN (" . implode(",", $pks) . ")");

        $db->setQuery($query);
        $db->execute();

        // Trigger change state event

        $context = $this->option . '.' . $this->name;

        // Include the content plugins for the change of state event.
        JPluginHelper::importPlugin('content');

        // Trigger the onContentChangeState event.
        $dispatcher = JEventDispatcher::getInstance();
        $result     = $dispatcher->trigger($this->event_change_state, array($context, $pks, $value));

        if (in_array(false, $result, true)) {
            throw new RuntimeException(JText::_("COM_IDENTITYPROOF_ERROR_TRIGGERING_PLUGIN"));
        }

        // Clear the component's cache
        $this->cleanCache();
    }
}
