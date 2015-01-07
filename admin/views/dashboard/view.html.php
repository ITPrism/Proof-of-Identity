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

class IdentityProofViewDashboard extends JViewLegacy
{
    /**
     * @var JDocumentHtml
     */
    public $document;

    protected $version;
    protected $itprismVersion;
    protected $uri;
    protected $uriHTTPS;

    protected $option;

    protected $sidebar;

    public function __construct($config)
    {
        parent::__construct($config);
        $this->option = JFactory::getApplication()->input->get("option");
    }

    public function display($tpl = null)
    {
        $this->version = new IdentityProofVersion();

        // Load ITPrism library version
        jimport("itprism.version");
        if (!class_exists("ITPrismVersion")) {
            $this->itprismVersion = JText::_("COM_IDENTITYPROOF_ITPRISM_LIBRARY_DOWNLOAD");
        } else {
            $itprismVersion       = new ITPrismVersion();
            $this->itprismVersion = $itprismVersion->getShortVersion();
        }

        // Get URI
        $this->uri        = JUri::getInstance();
        $uriCloned        = clone($this->uri);

        // Generate HTTPS URI.
        $uriCloned->setScheme("https");
        $this->uriHTTPS   = $uriCloned->toString();

        // Add submenu
        IdentityProofHelper::addSubmenu($this->getName());

        $this->addToolbar();
        $this->addSidebar();
        $this->setDocument();

        parent::display($tpl);
    }

    /**
     * Add a menu on the sidebar of page
     */
    protected function addSidebar()
    {
        $this->sidebar = JHtmlSidebar::render();
    }

    /**
     * Add the page title and toolbar.
     *
     * @since   1.6
     */
    protected function addToolbar()
    {
        JToolBarHelper::title(JText::_("COM_IDENTITYPROOF_DASHBOARD"));

        JToolbarHelper::preferences('com_identityproof');
        JToolbarHelper::divider();

        // Help button
        $bar = JToolBar::getInstance('toolbar');
        $bar->appendButton('Link', 'help', JText::_('JHELP'), JText::_('COM_IDENTITYPROOF_HELP_URL'));
    }

    /**
     * Method to set up the document properties
     *
     * @return void
     */
    protected function setDocument()
    {
        $this->document->setTitle(JText::_('COM_IDENTITYPROOF_DASHBOARD'));
    }
}
