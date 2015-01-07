<?php
/**
 * @package      ProofOfIdentity
 * @subpackage   Users
 * @author       Todor Iliev
 * @copyright    Copyright (C) 2015 Todor Iliev <todor@itprism.com>. All rights reserved.
 * @license      http://www.gnu.org/copyleft/gpl.html GNU/GPL
 */

defined('JPATH_PLATFORM') or die;

JLoader::register("IdentityProofConstants", dirname(__FILE__) . DIRECTORY_SEPARATOR . "constants.php");

/**
 * This class provides functionality that manage a user.
 *
 * @package      ProofOfIdentity
 * @subpackage   Users
 */
class IdentityProofUser
{
    protected $id;
    protected $name;
    protected $state = 0;

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
     * $user    = new IdentityProofUser(JFactory::getDbo());
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
     * $user    = new IdentityProofUser();
     * $user->setDb(JFactory::getDbo());
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
     *    "user_id" => 1
     * );
     *
     * $user    = new IdentityProofUser();
     * $user->setDb(JFactory::getDbo());
     * $user->load($keys);
     * </code>
     *
     * @param int $id Primary Key
     */
    public function load($id)
    {
        $query = $this->db->getQuery(true);

        $query
            ->select(
                "a.id, a.state, " .
                "b.name"
            )
            ->from($this->db->quoteName("#__identityproof_users", "a"))
            ->leftJoin($this->db->quoteName("#__users", "b") . " ON a.id = b.id")
            ->where("a.id = " . (int)$id);

        $this->db->setQuery($query);
        $result = $this->db->loadAssoc();

        if (!$result) {
            $result = array();
        }

        $this->bind($result);
    }

    /**
     * Set data to object properties.
     *
     * <code>
     * $data = array(
     *    "id"     => 1,
     *    "name"   => "John Dow",
     *    "state"  => 1,
     * );
     *
     * $user    = new IdentityProofUser();
     * $user->bind($data);
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
     * Update the user state to verified.
     *
     * <code>
     * $userId  = 1;
     * $user    = new IdentityProofUser(JFactory::getDbo());
     * $user->load($userId);
     *
     * $user->toVerified();
     * </code>
     */
    public function toVerified()
    {
        $this->updateState(IdentityProofConstants::VERIFIED);
    }

    /**
     * Update the user state to NOT verified.
     *
     * <code>
     * $userId  = 1;
     * $user    = new IdentityProofUser(JFactory::getDbo());
     * $user->load($userId);
     *
     * $user->toVerified();
     * </code>
     */
    public function toNotVerified()
    {
        $this->updateState(IdentityProofConstants::NOT_VERIFIED);
    }

    protected function updateState($state)
    {
        $query = $this->db->getQuery(true);

        $query
            ->update($this->db->quoteName("#__identityproof_users"))
            ->set($this->db->quoteName("state") . "=" . $this->db->quote($state))
            ->where($this->db->quoteName("id") ."=". (int)$this->id);

        $this->db->setQuery($query);
        $this->db->execute();
    }

    /**
     * Create user record in database.
     *
     * <code>
     * $data = array(
     *    "id"     => 1,
     *    "name"   => "John Dow",
     *    "state"  => 1,
     * );
     *
     * $user    = new IdentityProofUser(JFactory::getDbo());
     * $user->bind($data);
     * $user->createUser();
     * </code>
     */
    public function createUser()
    {
        $query = $this->db->getQuery(true);

        $query
            ->insert($this->db->quoteName("#__identityproof_users"))
            ->set($this->db->quoteName("id") . "=" . $this->db->quote($this->id))
            ->set($this->db->quoteName("state") . "=" . $this->db->quote($this->state));

        $this->db->setQuery($query);
        $this->db->execute();
    }

    /**
     * Return record ID.
     *
     * <code>
     * $userId  = 1;
     *
     * $user    = new IdentityProofUser(JFactory::getDbo());
     * $user->load($userId);
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
     * $userId  = 1;
     *
     * $user    = new IdentityProofUser(JFactory::getDbo());
     * $user->load($userId);
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
     * $userId  = 1;
     *
     * $user    = new IdentityProofUser(JFactory::getDbo());
     * $user->load($userId);
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
     * $userId  = 1;
     *
     * $user    = new IdentityProofUser();
     * $user->setDb(JFactory::getDbo());
     * $user->load($userId);
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

    /**
     * Returns an associative array of object properties.
     *
     * <code>
     * $keys = array(
     *  "user_id" => 1
     * );
     *
     * $user    = new IdentityProofUser();
     * $user->setDb(JFactory::getDbo());
     * $user->load($keys);
     *
     * $properties = $user->getProperties();
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
