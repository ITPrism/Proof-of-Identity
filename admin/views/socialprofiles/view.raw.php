<?php
/**
 * @package      ProofOfIdentity
 * @subpackage   Components
 * @author       Todor Iliev
 * @copyright    Copyright (C) 2017 Todor Iliev <todor@itprism.com>. All rights reserved.
 * @license      GNU General Public License version 3 or later; see LICENSE.txt
 */

// no direct access
defined('_JEXEC') or die;

class IdentityproofViewSocialprofiles extends JViewLegacy
{
    protected $profiles;
    protected $facebook;
    protected $twitter;
    protected $google;

    public function display($tpl = null)
    {
        $app            = JFactory::getApplication();

        $userId         = $app->input->getInt('id');
        $this->profiles = $this->get('Profiles');

        $this->facebook = new Identityproof\Profile\Facebook(JFactory::getDbo());
        $this->facebook->load(['user_id' => $userId]);

        $this->twitter = new Identityproof\Profile\Twitter(JFactory::getDbo());
        $this->twitter->load(['user_id' => $userId]);

        $this->google = new Identityproof\Profile\Google(JFactory::getDbo());
        $this->google->load(['user_id' => $userId]);

        parent::display($tpl);
    }
}
