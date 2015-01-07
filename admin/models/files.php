<?php
/**
 * @package      ProofOfIdentity
 * @subpackage   Component
 * @author       Todor Iliev
 * @copyright    Copyright (C) 2015 Todor Iliev <todor@itprism.com>. All rights reserved.
 * @license      http://www.gnu.org/copyleft/gpl.html GNU/GPL
 */

// no direct access
defined('_JEXEC') or die;

class IdentityProofModelFiles extends JModelList
{
    /**
     * Constructor.
     *
     * @param   array $config  An optional associative array of configuration settings.
     *
     * @see     JController
     * @since   1.6
     */
    public function __construct($config = array())
    {
        if (empty($config['filter_fields'])) {
            $config['filter_fields'] = array(
                'id', 'a.id',
                'title', 'a.title',
                'filename', 'a.filename',
                'state', 'a.state',
                'name', 'b.name',
            );
        }

        parent::__construct($config);
    }

    /**
     * Method to auto-populate the model state.
     *
     * Note. Calling getState in this method will result in recursion.
     * @param $ordering
     * @param $direction
     *
     * @since   1.6
     */
    protected function populateState($ordering = null, $direction = null)
    {
        // Load the component parameters.
        $params = JComponentHelper::getParams($this->option);
        $this->setState('params', $params);

        // Load the filter state.
        $value = $this->getUserStateFromRequest($this->context . '.filter.search', 'filter_search');
        $this->setState('filter.search', $value);

        // Load the transaction state filter.
        $value = $this->getUserStateFromRequest($this->context . '.filter.state', 'filter_state');
        $this->setState('filter.state', $value);

        // List state information.
        parent::populateState('a.id', 'desc');
    }

    /**
     * Method to get a store id based on model configuration state.
     *
     * This is necessary because the model is used by the component and
     * different modules that might need different sets of data or different
     * ordering requirements.
     *
     * @param   string $id A prefix for the store id.
     *
     * @return  string      A store id.
     * @since   1.6
     */
    protected function getStoreId($id = '')
    {
        // Compile the store id.
        $id .= ':' . $this->getState('filter.search');
        $id .= ':' . $this->getState('filter.state');

        return parent::getStoreId($id);
    }

    /**
     * Build an SQL query to load the list data.
     *
     * @return  JDatabaseQuery
     * @since   1.6
     */
    protected function getListQuery()
    {
        $db = $this->getDbo();
        /** @var $db JDatabaseDriver */

        // Create a new query object.
        $query = $db->getQuery(true);

        // Select the required fields from the table.
        $query->select(
            $this->getState(
                'list.select',
                'a.id, a.title, a.filename, a.user_id, a.state, a.note, ' .
                'b.name'
            )
        );
        $query->from($db->quoteName('#__identityproof_files', 'a'));
        $query->leftJoin($db->quoteName('#__users', 'b') . " ON a.user_id = b.id");

        // Filter by search in title
        $state = $this->getState('filter.state');
        if (is_numeric($state)) {
            $query->where('a.state = ' .(int)$state);
        } elseif ($state === "") {
            $query->where('(a.state IN (0,1))');
        }

        // Filter by search in title
        $search = $this->getState('filter.search');
        if (!empty($search)) {

            if (stripos($search, 'id:') === 0) {
                $query->where('a.id = ' . (int)substr($search, 3));
            } elseif (stripos($search, 'uid:') === 0) {
                $query->where('a.user_id = ' . (int)substr($search, 4));
            } else {
                $escaped = $db->escape($search, true);
                $quoted  = $db->quote("%" . $escaped . "%", false);
                $query->where('a.title LIKE ' . $quoted);
            }
        }

        // Add the list ordering clause.
        $orderString = $this->getOrderString();
        $query->order($db->escape($orderString));

        return $query;
    }

    protected function getOrderString()
    {
        $orderCol  = $this->getState('list.ordering');
        $orderDirn = $this->getState('list.direction');

        return $orderCol . ' ' . $orderDirn;
    }
}
