<?php
/**
 * @package      ProofOfIdentity
 * @subpackage   Helpers
 * @author       Todor Iliev
 * @copyright    Copyright (C) 2017 Todor Iliev <todor@itprism.com>. All rights reserved.
 * @license      GNU General Public License version 3 or later; see LICENSE.txt
 */

namespace Identityproof\Helper;

use Prism\Helper\HelperInterface;
use Prism\Utilities\ArrayHelper;
use Joomla\Registry\Registry;

defined('JPATH_PLATFORM') or die;

/**
 * This class provides functionality to prepare social profiles to the user items.
 *
 * @package      ProofOfIdentity
 * @subpackage   Helpers
 */
class PrepareUserProfilesHelper implements HelperInterface
{
    /**
     * @var \JDatabaseDriver
     */
    protected $db;

    /**
     * Initialize the object.
     *
     * @param \JDatabaseDriver $db
     */
    public function __construct(\JDatabaseDriver $db)
    {
        $this->db = $db;
    }

    /**
     * Prepare the access levels of the items.
     *
     * @param array $data
     * @param array $options
     */
    public function handle(&$data, array $options = array())
    {
        if (count($data) > 0) {
            $userIds = ArrayHelper::getIds($data, 'user_id');

            $query1 = $this->db->getQuery(true);

            $query1
                ->select('id, user_id')
                ->from($this->db->quoteName('#__identityproof_facebook'))
                ->where('user_id IN (' . implode(',', $userIds) . ')');

            $query2 = $this->db->getQuery(true);
            $query2
                ->select('id, user_id')
                ->from($this->db->quoteName('#__identityproof_google'))
                ->where('user_id IN (' . implode(',', $userIds) . ')');

            $query3 = $this->db->getQuery(true);
            $query3
                ->select('id, user_id')
                ->from($this->db->quoteName('#__identityproof_twitter'))
                ->where('user_id IN (' . implode(',', $userIds) . ')');

            $query1
                ->union($query2)
                ->union($query3);

            $this->db->setQuery($query1);
            $results = (array)$this->db->loadAssocList('user_id');

            if (count($results) > 0) {
                foreach ($data as $key => $item) {
                    if (property_exists($item, 'hasProfiles')) {
                        continue;
                    }

                    $item->hasProfiles = array_key_exists($item->id, $results) ? 1 : 0;
                }
            }
        }
    }
}
