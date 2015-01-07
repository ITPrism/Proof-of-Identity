<?php
/**
 * @package      ProofOfIdentity
 * @subpackage   Plugins
 * @author       Todor Iliev
 * @copyright    Copyright (C) 2015 Todor Iliev <todor@itprism.com>. All rights reserved.
 * @license      http://www.gnu.org/copyleft/gpl.html GNU/GPL
 */

// No direct access
defined('_JEXEC') or die;

class plgUserIdentityProof extends JPlugin
{
    /**
     * Application object.
     *
     * @var    JApplicationAdministrator
     */
    protected $app;

    /**
     * This method should handle any login logic and report back to the subject
     *
     * @param   array $options Array holding options (remember, return, entry_url, action, user, responseType)
     *
     * @return  boolean  True on success
     */
    public function onUserAfterLogin($options)
    {
        if (!$this->app->isAdmin() or !JComponentHelper::isEnabled("com_identityproof")) {
            return true;
        }

        // Get the number of days after the system have to remove records.
        $days = $this->params->get("days", 14);

        if (!empty($days)) {

            $today = new JDate();
            $today->modify("- " .(int)$days . " days");
            $date = $today->format("Y-m-d");

            $db    = JFactory::getDbo();
            $query = $db->getQuery(true);

            $query
                ->select("a.filename")
                ->from($db->quoteName("#__identityproof_files", "a"))
                ->where("a.record_date <= " . $db->quote($date));

            $db->setQuery($query);
            $results = $db->loadColumn();

            if (!empty($results)) {

                $params = JComponentHelper::getParams("com_identityproof");
                /** @var  $params Joomla\Registry\Registry */

                // Remove old key files
                jimport("joomla.filesystem.file");
                foreach ($results as $filename) {
                    $file = JPath::clean($params->get("files_path") . DIRECTORY_SEPARATOR . $filename);
                    if (JFile::exists($file)) {
                        JFile::delete($file);
                    }
                }

                // Remove old records.
                $query = $db->getQuery(true);
                $query
                    ->delete($db->quoteName("#__identityproof_files"))
                    ->where($db->quoteName("record_date") . " <= " . $db->quote($date));

                $db->setQuery($query);
                $db->execute();
            }

        }

        return true;
    }
}
