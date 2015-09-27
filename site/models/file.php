<?php
/**
 * @package      IdentityProof
 * @subpackage   Component
 * @author       Todor Iliev
 * @copyright    Copyright (C) 2015 Todor Iliev <todor@itprism.com>. All rights reserved.
 * @license      GNU General Public License version 3 or later; see LICENSE.txt
 */

// no direct access
defined('_JEXEC') or die;

/**
 * Get a list of items
 */
class IdentityProofModelFile extends JModelLegacy
{
    public function remove($fileId, $userId)
    {
        $db     = $this->getDbo();
        $query  = $db->getQuery(true);

        // Remove the key file.
        $query
            ->select("a.filename")
            ->from($db->quoteName("#__identityproof_files", "a"))
            ->where("a.id = ". (int)$fileId)
            ->where("a.user_id = ". (int)$userId);

        $db->setQuery($query, 0, 1);
        $result = $db->loadResult();

        if (!empty($result)) {
            $params = JComponentHelper::getParams($this->option);
            /** @var  $params Joomla\Registry\Registry */

            $file = JPath::clean($params->get("files_path") .DIRECTORY_SEPARATOR. $result);

            if (JFile::exists($file)) {
                JFile::delete($file);
            }
        }

        // Remove the record.
        $query  = $db->getQuery(true);
        $query
            ->delete($db->quoteName("#__identityproof_files"))
            ->where($db->quoteName("id") ." = ". (int)$fileId)
            ->where($db->quoteName("user_id") ." = ". (int)$userId);

        $db->setQuery($query);
        $db->execute();
    }

    /**
     * Load and return a note about a resource.
     *
     * @param int $fileId
     * @param int $userId
     *
     * @return string
     */
    public function getNote($fileId, $userId)
    {
        $db     = $this->getDbo();
        $query  = $db->getQuery(true);

        // Remove the key file.
        $query
            ->select("a.note")
            ->from($db->quoteName("#__identityproof_files", "a"))
            ->where("a.id = ". (int)$fileId)
            ->where("a.user_id = ". (int)$userId);

        $db->setQuery($query, 0, 1);
        $result = $db->loadResult();

        if (!$result) {
            $result = "";
        }

        return $result;
    }
}
