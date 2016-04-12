<?php
/**
 * @package      ProofOfIdentity
 * @subpackage   Users
 * @author       Todor Iliev
 * @copyright    Copyright (C) 2015 Todor Iliev <todor@itprism.com>. All rights reserved.
 * @license      http://www.gnu.org/copyleft/gpl.html GNU/GPL
 */

namespace Identityproof\Profile;

use Prism\Database;

defined('JPATH_PLATFORM') or die;

/**
 * This class provides functionality that manage a user.
 *
 * @package      ProofOfIdentity
 * @subpackage   Users
 */
class Facebook extends Database\Table
{
    protected $id;
    protected $user_id;
    protected $facebook_id;
    protected $name;
    protected $gender;
    protected $hometown;
    protected $link;
    protected $picture;
    protected $verified;
    protected $website;

    /**
     * Load user data from database.
     *
     * <code>
     * $keys = array(
     *    "facebook_id" => 123456
     * );
     *
     * $user    = new Identityproof\Profile\Facebook(\JFactory::getDbo());
     * $user->load($keys);
     * </code>
     *
     * @param array|int $keys
     * @param array $options
     */
    public function load($keys, array $options = array())
    {
        $query = $this->db->getQuery(true);

        $query
            ->select(
                'a.id, a.user_id, a.facebook_id, a.name, a.gender, ' .
                'a.hometown, a.link, a.picture, a.verified, a.website'
            )
            ->from($this->db->quoteName('#__identityproof_facebook', 'a'));

        if (!is_array($keys)) {
            $query->where('a.id = ' . (int)$keys);
        } else {
            foreach ($keys as $key => $value) {
                $query->where($this->db->quoteName('a.'.$key) .' = '. $this->db->quote($value));
            }
        }

        $this->db->setQuery($query);
        $result = (array)$this->db->loadAssoc();

        $this->bind($result);
    }

    /**
     * Remove data from database.
     *
     * <code>
     * $keys = array(
     *     "user_id" => 12
     * );
     *
     * $user    = new Identityproof\Profile\Facebook(\JFactory::getDbo());
     * $user->load($keys);
     * $user->remove();
     * </code>
     */
    public function remove()
    {
        $query = $this->db->getQuery(true);

        $query
            ->delete($this->db->quoteName('#__identityproof_facebook'))
            ->where($this->db->quoteName('id') .'='. (int)$this->id);

        $this->db->setQuery($query);
        $this->db->execute();

        $this->reset();
    }

    /**
     * Store data to database.
     *
     * <code>
     * $data = array(
     *     "id" => 1,
     *     "user_id" => 12,
     *     "facebook_id" => 123,
     *     "name" => 'John Doe'
     * );
     *
     * $user    = new Identityproof\Profile\Facebook(\JFactory::getDbo());
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
        $website = (!$this->website) ? 'NULL' : $this->db->quote($this->website);
        $picture = (!$this->picture) ? 'NULL' : $this->db->quote($this->picture);

        $query = $this->db->getQuery(true);

        $query
            ->insert($this->db->quoteName('#__identityproof_facebook'))
            ->set($this->db->quoteName('user_id') . '=' . (int)$this->user_id)
            ->set($this->db->quoteName('facebook_id') . '=' . $this->db->quote($this->facebook_id))
            ->set($this->db->quoteName('name') . '=' . $this->db->quote($this->name))
            ->set($this->db->quoteName('gender') . '=' . $this->db->quote($this->gender))
            ->set($this->db->quoteName('hometown') . '=' . $this->db->quote($this->hometown))
            ->set($this->db->quoteName('link') . '=' . $this->db->quote($this->link))
            ->set($this->db->quoteName('website') . '=' . $website)
            ->set($this->db->quoteName('picture') . '=' . $picture)
            ->set($this->db->quoteName('verified') . '=' . (int)$this->verified)
            ->where($this->db->quoteName('id') .'='. (int)$this->id);

        $this->db->setQuery($query);
        $this->db->execute();
    }

    protected function updateObject()
    {
        $website = (!$this->website) ? 'NULL' : $this->db->quote($this->website);
        $picture = (!$this->picture) ? 'NULL' : $this->db->quote($this->picture);

        $query = $this->db->getQuery(true);

        $query
            ->update($this->db->quoteName('#__identityproof_facebook'))
            ->set($this->db->quoteName('user_id') . '=' . (int)$this->user_id)
            ->set($this->db->quoteName('facebook_id') . '=' . $this->db->quote($this->facebook_id))
            ->set($this->db->quoteName('name') . '=' . $this->db->quote($this->name))
            ->set($this->db->quoteName('gender') . '=' . $this->db->quote($this->gender))
            ->set($this->db->quoteName('hometown') . '=' . $this->db->quote($this->hometown))
            ->set($this->db->quoteName('link') . '=' . $this->db->quote($this->link))
            ->set($this->db->quoteName('website') . '=' . $website)
            ->set($this->db->quoteName('picture') . '=' . $picture)
            ->set($this->db->quoteName('verified') . '=' . (int)$this->verified)
            ->where($this->db->quoteName('id') .'='. (int)$this->id);

        $this->db->setQuery($query);
        $this->db->execute();
    }

    /**
     * Return record ID.
     *
     * <code>
     * $keys = array(
     *    "user_id" => 1
     * );
     *
     * $user    = new Identityproof\Profile\Facebook(\JFactory::getDbo());
     * $user->load($keys);
     *
     * if (!$user->getId()) {
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
     * Return user gender.
     *
     * <code>
     * $keys = array(
     *    "user_id" => 1
     * );
     *
     * $user    = new Identityproof\Profile\Facebook(\JFactory::getDbo());
     * $user->load($keys);
     *
     * echo $user->getGender();
     * </code>
     *
     * @return string
     */
    public function getGender()
    {
        return (string)$this->gender;
    }

    /**
     * Return user picture.
     *
     * <code>
     * $keys = array(
     *    "user_id" => 1
     * );
     *
     * $user    = new Identityproof\Profile\Facebook(\JFactory::getDbo());
     * $user->load($keys);
     *
     * echo $user->getPicture();
     * </code>
     *
     * @return string
     */
    public function getPicture()
    {
        return $this->picture;
    }

    /**
     * Return the name of a user.
     *
     * <code>
     * $keys = array(
     *    "user_id" => 1
     * );
     *
     * $user    = new Identityproof\Profile\Facebook(\JFactory::getDbo());
     * $user->load($keys);
     *
     * echo $user->getName();
     * </code>
     *
     * @return string
     */
    public function getName()
    {
        return (string)$this->name;
    }

    /**
     * Return the link to user profile.
     *
     * <code>
     * $keys = array(
     *    "user_id" => 1
     * );
     *
     * $user    = new Identityproof\Profile\Facebook(\JFactory::getDbo());
     * $user->load($keys);
     *
     * echo $user->getLink();
     * </code>
     *
     * @return string
     */
    public function getLink()
    {
        return $this->link;
    }

    /**
     * Return the website.
     *
     * <code>
     * $keys = array(
     *    "user_id" => 1
     * );
     *
     * $user    = new Identityproof\Profile\Facebook(\JFactory::getDbo());
     * $user->load($keys);
     *
     * echo $user->getWebsite();
     * </code>
     *
     * @return string
     */
    public function getWebsite()
    {
        return $this->website;
    }

    /**
     * Return the hometown.
     *
     * <code>
     * $keys = array(
     *    "user_id" => 1
     * );
     *
     * $user    = new Identityproof\Profile\Facebook(\JFactory::getDbo());
     * $user->load($keys);
     *
     * echo $user->getHometown();
     * </code>
     *
     * @return string
     */
    public function getHometown()
    {
        return $this->hometown;
    }

    /**
     * Return facebook ID of the user.
     *
     * <code>
     * $keys = array(
     *    "user_id" => 1
     * );
     *
     * $user    = new Identityproof\Profile\Facebook(\JFactory::getDbo());
     * $user->load($keys);
     *
     * echo $user->getFacebookId();
     * </code>
     *
     * @return string
     */
    public function getFacebookId()
    {
        return $this->facebook_id;
    }

    /**
     * Return user ID.
     *
     * <code>
     * $keys = array(
     *    "user_id" => 1
     * );
     *
     * $user    = new Identityproof\Profile\Facebook(\JFactory::getDbo());
     * $user->load($keys);
     *
     * echo $user->getUserId();
     * </code>
     *
     * @return string
     */
    public function getUserId()
    {
        return $this->user_id;
    }

    /**
     * Check if the user profile in Facebook has been verified.
     *
     * <code>
     * $keys = array(
     *    "user_id" => 1
     * );
     *
     * $user    = new Identityproof\Profile\Facebook();
     * $user->setDb(\JFactory::getDbo());
     * $user->load($keys);
     *
     * if (!$user->isVerified()) {
     * ...
     * }
     * </code>
     *
     * @return bool
     */
    public function isVerified()
    {
        return (bool)$this->verified;
    }
}
