<?php
/**
 * @package      ProofOfIdentity
 * @subpackage   Component
 * @author       Todor Iliev
 * @copyright    Copyright (C) 2017 Todor Iliev <todor@itprism.com>. All rights reserved.
 * @license      GNU General Public License version 3 or later; see LICENSE.txt
 */

// no direct access
defined('_JEXEC') or die;

class IdentityproofViewDashboard extends JViewLegacy
{
    /**
     * @var JDocumentHtml
     */
    public $document;

    protected $version;
    protected $prismVersion;
    protected $prismVersionLowerMessage;
    protected $uri;
    protected $uriHTTPS;

    protected $option;

    protected $sidebar;

    public function display($tpl = null)
    {
        $this->option = JFactory::getApplication()->input->get('option');
        
        $this->version = new Identityproof\Version();

        // Load Prism library version
        if (!class_exists('Prism\\Version')) {
            $this->prismVersion = JText::_('COM_IDENTITYPROOF_PRISM_LIBRARY_DOWNLOAD');
        } else {
            $prismVersion       = new Prism\Version();
            $this->prismVersion = $prismVersion->getShortVersion();

            if (version_compare($this->prismVersion, $this->version->requiredPrismVersion, '<')) {
                $this->prismVersionLowerMessage = JText::_('COM_IDENTITYPROOF_PRISM_LIBRARY_LOWER_VERSION');
            }
        }

        $params          = JComponentHelper::getParams('com_identityproof');
        $secretFolder    = JPath::clean($params->get('files_path'), '/');

        // Create a folder.
        if (!JFolder::exists($secretFolder)) {
            if (!JFolder::create($secretFolder, 0740)) {
                throw new RuntimeException(JText::sprintf('COM_IDENTITYPROOF_ERROR_FOLDER_CANNOT_BE_CREATED_S', $secretFolder));
            }
        }

        // Create .htaccess file to deny the access for that folder.
        $htaccessFile = JPath::clean($secretFolder . '/.htaccess', '/');
        if (!JFile::exists($htaccessFile)) {
            $fileContent = 'Deny from all';
            if (!JFile::write($htaccessFile, $fileContent)) {
                throw new RuntimeException(JText::sprintf('COM_IDENTITYPROOF_ERROR_FILE_CANNOT_BE_CREATED_S', $htaccessFile));
            }
        }

        // Get URI
        $this->uri        = JUri::getInstance();
        $uriCloned        = clone($this->uri);

        // Generate HTTPS URI.
        $uriCloned->setScheme('https');
        $this->uriHTTPS   = $uriCloned->toString();

        // Add submenu
        IdentityproofHelper::addSubmenu($this->getName());

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
        JToolbarHelper::title(JText::_('COM_IDENTITYPROOF_DASHBOARD'));

        JToolbarHelper::preferences('com_identityproof');
        JToolbarHelper::divider();

        // Help button
        $bar = JToolbar::getInstance('toolbar');
        $bar->appendButton('Link', 'help', JText::_('JHELP'), 'http://itprism.com/help/92-identity-proof-documentation');
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
