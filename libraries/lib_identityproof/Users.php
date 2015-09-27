<?php
/**
 * @package      ProofOfIdentity
 * @subpackage   Users
 * @author       Todor Iliev
 * @copyright    Copyright (C) 2015 Todor Iliev <todor@itprism.com>. All rights reserved.
 * @license      GNU General Public License version 3 or later; see LICENSE.txt
 */

namespace IdentityProof;

use Prism;
use Joomla\Utilities\ArrayHelper;

defined('JPATH_PLATFORM') or die;

/**
 * This class provides functionality that manage users.
 *
 * @package      ProofOfIdentity
 * @subpackage   Users
 */
class Users extends Prism\Database\ArrayObject
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
    public function load($options = array())
    {
        $ids = (!isset($options["ids"])) ? array() : $options["ids"];
        
        ArrayHelper::toInteger($ids);

        if (!empty($ids)) {
            $query = $this->db->getQuery(true);

            $query
                ->select("a.id, a.state, b.name, b.email")
                ->from($this->db->quoteName("#__identityproof_users", "a"))
                ->leftJoin($this->db->quoteName("#__users", "b") . " ON a.id = b.id")
                ->where("a.id IN ( " . implode(",", $ids) . " )");

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
            if ($id == $key) {
                $item = new User(\JFactory::getDbo());
                $item->bind($value);
                break;
            }
        }

        return $item;
    }
}
