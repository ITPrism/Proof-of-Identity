<?php
/**
 * @package      ProofOfIdentity
 * @subpackage   Plugins
 * @author       Todor Iliev
 * @copyright    Copyright (C) 2016 Todor Iliev <todor@itprism.com>. All rights reserved.
 * @license      GNU General Public License version 3 or later; see LICENSE.txt
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
        if (!$this->app->isAdmin() or !JComponentHelper::isEnabled('com_identityproof')) {
            return true;
        }

        // Get the number of days after the system have to remove records.
        $days = (int)$this->params->get('days', 14);

        if ($days > 0) {

            $today = new JDate();
            $today->modify('- ' .(int)$days . ' days');

            $db    = JFactory::getDbo();
            $query = $db->getQuery(true);
            $date  = $today->toSql();

            $query
                ->select('a.filename')
                ->from($db->quoteName('#__identityproof_files', 'a'))
                ->where('a.record_date <= ' . $db->quote($date));

            $db->setQuery($query);
            $results = (array)$db->loadColumn();

            if (count($results) > 0) {

                $params = JComponentHelper::getParams('com_identityproof');
                /** @var  $params Joomla\Registry\Registry */

                // Remove old key files
                jimport('joomla.filesystem.file');
                foreach ($results as $filename) {
                    $file = JPath::clean($params->get('files_path') . DIRECTORY_SEPARATOR . $filename);
                    if (JFile::exists($file)) {
                        JFile::delete($file);
                    }
                }

                // Remove old records.
                $query = $db->getQuery(true);
                $query
                    ->delete($db->quoteName('#__identityproof_files'))
                    ->where($db->quoteName('record_date') . ' <= ' . $db->quote($date));

                $db->setQuery($query);
                $db->execute();
            }

        }

        return true;
    }
}
