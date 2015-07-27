<?php
/**
 * @package      ProofOfIdentity
 * @subpackage   Component
 * @author       Todor Iliev
 * @copyright    Copyright (C) 2015 Todor Iliev <todor@itprism.com>. All rights reserved.
 * @license      http://www.gnu.org/copyleft/gpl.html GNU/GPL
 */

// no direct access
defined('_JEXEC') or die;

class IdentityProofViewProof extends JViewLegacy
{
    /**
     * @var JDocumentHtml
     */
    public $document;

    /**
     * @var Joomla\Registry\Registry
     */
    protected $state;

    /**
     * @var Joomla\Registry\Registry
     */
    protected $params;

    protected $files;
    protected $form;
    protected $uri;
    protected $user;
    protected $uriHTTPS;

    protected $option;

    protected $pageclass_sfx;

    public function __construct($config)
    {
        parent::__construct($config);
        $this->option = JFactory::getApplication()->input->get("option");
    }

    public function display($tpl = null)
    {
        $app    = JFactory::getApplication();

        $userId = JFactory::getUser()->get("id");
        if (!$userId) {
            $app->enqueueMessage(JText::_("COM_IDENTITYPROOF_ERROR_NOT_LOG_IN"), "notice");
            $app->redirect(JRoute::_('index.php?option=com_users&view=login', false));
            return;
        }

        // Initialise variables
        $this->form       = $this->get("Form");
        $this->state      = $this->get('State');
        $this->params     = $this->state->get('params');

        // Get URI
        $this->uri        = JUri::getInstance();
        $uriCloned        = clone($this->uri);

        // Generate HTTPS URI.
        $uriCloned->setScheme("https");
        $this->uriHTTPS   = $uriCloned->toString();

        if ($this->params->get("auto_redirect", 0) and !$this->uri->isSSL()) {
            $app->redirect($this->uriHTTPS, false);
            return;
        }

        $model            = $this->getModel();
        $this->files      = $model->getFiles($userId);

        // Create a user record if it does not exist.
        $this->user = new IdentityProof\User(JFactory::getDbo());
        $this->user->load($userId);

        if (!$this->user->getId()) {
            $this->user->bind(array("id" => $userId));
            $this->user->store();
        }

        $this->prepareDocument();

        parent::display($tpl);
    }

    /**
     * Prepare document
     */
    protected function prepareDocument()
    {
        //Escape strings for HTML output
        $this->pageclass_sfx = htmlspecialchars($this->params->get('pageclass_sfx'));

        // Prepare page heading
        $this->preparePageHeading();

        // Prepare page heading
        $this->preparePageTitle();

        // Meta Description
        if ($this->params->get('menu-meta_description')) {
            $this->document->setDescription($this->params->get('menu-meta_description'));
        }

        // Meta keywords
        if ($this->params->get('menu-meta_keywords')) {
            $this->document->setMetadata('keywords', $this->params->get('menu-meta_keywords'));
        }

        if ($this->params->get('robots')) {
            $this->document->setMetadata('robots', $this->params->get('robots'));
        }

        // Include the translation of the confirmation question.
        JText::script('COM_IDENTITYPROOF_DELETE_QUESTION');
        JText::script('COM_IDENTITYPROOF_BROWSE');
        JText::script('COM_IDENTITYPROOF_REMOVE');

        JHtml::_('bootstrap.tooltip');
        JHtml::_('jquery.framework');
        JHtml::_('prism.ui.bootstrap3FileInput');
        JHtml::_("prism.ui.pnotify");
        JHtml::_('prism.ui.joomlaHelper');

        $this->document->addScript('media/' . $this->option . '/js/site/proof.js');
    }

    private function preparePageHeading()
    {
        $app = JFactory::getApplication();
        /** @var $app JApplicationSite */

        // Because the application sets a default page title,
        // we need to get it from the menu item itself
        $menus = $app->getMenu();
        $menu  = $menus->getActive();

        // Prepare page heading
        if ($menu) {
            $this->params->def('page_heading', $this->params->get('page_title', $menu->title));
        } else {
            $this->params->def('page_heading', JText::_('COM_IDENTITYPROOF_DEFAULT_PAGE_TITLE'));
        }
    }

    private function preparePageTitle()
    {
        $app = JFactory::getApplication();
        /** @var $app JApplicationSite */

        // Prepare page title
        $title = $this->params->get('page_title', '');

        // Add title before or after Site Name
        if (!$title) {
            $title = $app->get('sitename');
        } elseif ($app->get('sitename_pagetitles', 0) == 1) {
            $title = JText::sprintf('JPAGETITLE', $app->get('sitename'), $title);
        } elseif ($app->get('sitename_pagetitles', 0) == 2) {
            $title = JText::sprintf('JPAGETITLE', $title, $app->get('sitename'));
        }

        $this->document->setTitle($title);
    }
}
