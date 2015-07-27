<?php
/**
 * @package      ProofOfIdentity
 * @subpackage   Users
 * @author       Todor Iliev
 * @copyright    Copyright (C) 2015 Todor Iliev <todor@itprism.com>. All rights reserved.
 * @license      http://www.gnu.org/copyleft/gpl.html GNU/GPL
 */

namespace IdentityProof;

use Prism;

defined('JPATH_PLATFORM') or die;

/**
 * This class provides functionality that manage a user.
 *
 * @package      ProofOfIdentity
 * @subpackage   Users
 */
class User extends Prism\Database\Table
{
    protected $id;
    protected $name;
    protected $state = 0;

    /**
     * Load user data from database.
     *
     * <code>
     * $keys = array(
     *    "user_id" => 1
     * );
     *
     * $user    = new IdentityProof\User(\JFactory::getDbo());
     * $user->load($keys);
     * </code>
     *
     * @param array $keys
     * @param array $options
     */
    public function load($keys, $options = array())
    {
        $query = $this->db->getQuery(true);

        $query
            ->select(
                "a.id, a.state, " .
                "b.name, b.email"
            )
            ->from($this->db->quoteName("#__identityproof_users", "a"))
            ->leftJoin($this->db->quoteName("#__users", "b") . " ON a.id = b.id");

        if (is_array($keys) and !empty($keys)) {
            foreach ($keys as $key => $value) {
                $query->where($this->db->quoteName($key) ." = ". $this->db->quote($value));
            }
        } else {
            $query->where("a.id = " . (int)$keys);
        }

        $this->db->setQuery($query);
        $result = (array)$this->db->loadAssoc();

        $this->bind($result);
    }

    /**
     * Update the user state to verified.
     *
     * <code>
     * $keys = array(
     *    "user_id" => 1
     * );
     *
     * $user    = new IdentityProof\User(\JFactory::getDbo());
     * $user->load($keys);
     *
     * $user->toVerified();
     * </code>
     */
    public function toVerified()
    {
        $this->state = Prism\Constants::VERIFIED;
        $this->updateObject();
    }

    /**
     * Update the user state to NOT verified.
     *
     * <code>
     * $keys = array(
     *    "user_id" => 1
     * );
     *
     * $user    = new IdentityProof\User(\JFactory::getDbo());
     * $user->load($keys);
     *
     * $user->toVerified();
     * </code>
     */
    public function toNotVerified()
    {
        $this->state = Prism\Constants::NOT_VERIFIED;
        $this->updateObject();
    }

    /**
     * Store data to database.
     *
     * <code>
     * $data = array(
     *  "id" => 1,
     *  "state" => 2
     * );
     *
     * $user    = new IdentityProof\User(\JFactory::getDbo());
     * $user->bind($data);
     * $user->store();
     * </code>
     */
    public function store()
    {
        if (!$this->id) {
            throw new \InvalidArgumentException("Invalid ID (user ID).");
        }

        if (!$this->isExists()) { // Insert
            $this->insertObject();
        } else { // Update
            $this->updateObject();
        }
    }

    protected function insertObject()
    {
        $query = $this->db->getQuery(true);

        $query
            ->insert($this->db->quoteName("#__identityproof_users"))
            ->set($this->db->quoteName("id") . "=" . $this->db->quote($this->id))
            ->set($this->db->quoteName("state") . "=" . $this->db->quote($this->state));

        $this->db->setQuery($query);
        $this->db->execute();
    }

    protected function updateObject()
    {
        $query = $this->db->getQuery(true);

        $query
            ->update($this->db->quoteName("#__identityproof_users"))
            ->set($this->db->quoteName("state") . "=" . $this->db->quote($this->state))
            ->where($this->db->quoteName("id") ."=". (int)$this->id);

        $this->db->setQuery($query);
        $this->db->execute();
    }

    protected function isExists()
    {
        $query = $this->db->getQuery(true);

        $query
            ->select("COUNT(*)")
            ->from($this->db->quoteName("#__identityproof_users", "a"))
            ->where($this->db->quoteName("a.id") . " = " . $this->db->quote($this->id));

        $this->db->setQuery($query);
        return (bool)$this->db->loadResult();
    }

    /**
     * Return record ID.
     *
     * <code>
     * $keys = array(
     *    "user_id" => 1
     * );
     *
     * $user    = new IdentityProof\User(\JFactory::getDbo());
     * $user->load($keys);
     *
     * if (!$user->getId()) {
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
     * $keys = array(
     *    "user_id" => 1
     * );
     *
     * $user    = new IdentityProof\User(\JFactory::getDbo());
     * $user->load($keys);
     *
     * $state = $user->getState();
     * </code>
     */
    public function getState()
    {
        return $this->state;
    }

    /**
     * Return the name of a user.
     *
     * <code>
     * $keys = array(
     *    "user_id" => 1
     * );
     *
     * $user    = new IdentityProof\User(\JFactory::getDbo());
     * $user->load($keys);
     *
     * $user = $user->getName();
     * </code>
     */
    public function getName()
    {
        return (string)$this->name;
    }

    /**
     * Check if the user is verified.
     *
     * <code>
     * $keys = array(
     *    "user_id" => 1
     * );
     *
     * $user    = new IdentityProof\User();
     * $user->setDb(\JFactory::getDbo());
     * $user->load($keys);
     *
     * if (!$user->isVerified()) {
     * ...
     * }
     * </code>
     */
    public function isVerified()
    {
        return (!$this->state) ? false : true;
    }
}
