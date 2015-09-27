<?php
/**
 * @package      IdentityProof\Files
 * @subpackage   Validators
 * @author       Todor Iliev
 * @copyright    Copyright (C) 2015 Todor Iliev <todor@itprism.com>. All rights reserved.
 * @license      GNU General Public License version 3 or later; see LICENSE.txt
 */

namespace IdentityProof\Validator\File;

use Prism;

defined('JPATH_BASE') or die;

/**
 * This class provides functionality for validation file owner.
 *
 * @package      IdentityProof\Files
 * @subpackage   Validators
 */
class Owner implements Prism\Validator\ValidatorInterface
{
    protected $db;
    protected $fileId;
    protected $userId;

    /**
     * Initialize the object.
     *
     * <code>
     * $fileId = 1;
     * $userId = 2;
     *
     * $owner = new IdentityProof\Validator\File\Owner(JFactory::getDbo(), $fileId, $userId);
     * </code>
     *
     * @param \JDatabaseDriver $db     Database object.
     * @param int             $fileId File ID.
     * @param int             $userId User ID.
     */
    public function __construct(\JDatabaseDriver $db, $fileId, $userId)
    {
        $this->db     = $db;
        $this->fileId = $fileId;
        $this->userId = $userId;
    }

    /**
     * Validate file owner.
     *
     * <code>
     * $fileId = 1;
     * $userId = 2;
     *
     * $owner = new IdentityProof\Validator\File\Owner(JFactory::getDbo(), $fileId, $userId);
     * if(!$owner->isValid()) {
     * ......
     * }
     * </code>
     *
     * @return bool
     */
    public function isValid()
    {
        $query = $this->db->getQuery(true);

        $query
            ->select("COUNT(*)")
            ->from($this->db->quoteName("#__identityproof_files", "a"))
            ->where("a.id = " . (int)$this->fileId)
            ->where("a.user_id = " . (int)$this->userId);

        $this->db->setQuery($query, 0, 1);
        $result = $this->db->loadResult();

        return (bool)$result;
    }
}
