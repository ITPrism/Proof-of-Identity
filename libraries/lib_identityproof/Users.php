<?php
/**
 * @package      ProofOfIdentity
 * @subpackage   Users
 * @author       Todor Iliev
 * @copyright    Copyright (C) 2015 Todor Iliev <todor@itprism.com>. All rights reserved.
 * @license      http://www.gnu.org/copyleft/gpl.html GNU/GPL
 */

namespace Identityproof;

use Prism\Database;
use Joomla\Utilities\ArrayHelper;

defined('JPATH_PLATFORM') or die;

/**
 * This class provides functionality that manage users.
 *
 * @package      ProofOfIdentity
 * @subpackage   Users
 */
class Users extends Database\Collection
{
    /**
     * Load users data from database.
     *
     * <code>
     * $options = array(
     *     "ids" => array(1,2,3,4,5)
     * );
     * 
     * $users   = new ProofOfIdentity\Users(\JFactory::getDbo());
     * $users->load($options);
     *
     * foreach($users as $user) {
     *   echo $user["name"];
     *   echo $user["state"];
     * }
     * </code>
     *
     * @param array $options
     */
    public function load(array $options = array())
    {
        $ids = (array_key_exists('ids', $options)) ? $options['ids'] : array();
        $ids = ArrayHelper::toInteger($ids);

        if (count($ids) > 0) {
            $query = $this->db->getQuery(true);

            $query
                ->select('a.id, a.state, b.name, b.email')
                ->from($this->db->quoteName('#__identityproof_users', 'a'))
                ->leftJoin($this->db->quoteName('#__users', 'b') . ' ON a.id = b.id')
                ->where('a.id IN ( ' . implode(',', $ids) . ' )');

            $this->db->setQuery($query);
            $this->items = (array)$this->db->loadAssocList();
        }
    }

    /**
     * Create user object and return it.
     *
     * <code>
     * $options = array(
     *     "ids" => array(1,2,3,4,5)
     * );
     *
     * $users   = new ProofOfIdentity\Users(\JFactory::getDbo());
     * $users->load($ids);
     *
     * $userId = 1;
     * $user   = $users->getUser($userId);
     * </code>
     *
     * @param int $id
     *
     * @return null|User
     */
    public function getUser($id)
    {
        $item = null;

        foreach ($this->items as $key => $value) {
            if ((int)$id === (int)$key) {
                $item = new User(\JFactory::getDbo());
                $item->bind($value);
                break;
            }
        }

        return $item;
    }
}
