<?php
/**
 * @package      ProofOfIdentity
 * @subpackage   Component
 * @author       Todor Iliev
 * @copyright    Copyright (C) 2015 Todor Iliev <todor@itprism.com>. All rights reserved.
 * @license      GNU General Public License version 3 or later; see LICENSE.txt
 */

// no direct access
defined('_JEXEC') or die;

class IdentityProofViewUsers extends JViewLegacy
{
    /**
     * @var JDocumentHtml
     */
    public $document;

    /**
     * @var Joomla\Registry\Registry
     */
    protected $state;

    protected $items;
    protected $pagination;

    protected $option;
    protected $listOrder;
    protected $listDirn;
    protected $saveOrder;

    public $filterForm;

    protected $sidebar;

    public function __construct($config)
    {
        parent::__construct($config);
        $this->option = JFactory::getApplication()->input->get("option");
    }

    public function display($tpl = null)
    {
        $this->state      = $this->get('State');
        $this->items      = $this->get('Items');
        $this->pagination = $this->get('Pagination');

        // Get the ID of users which do not have records in Identity Proof table "users".
        $newUsers = array();
        foreach ($this->items as $item) {
            if (!$item->user_id) {
                $newUsers[] = $item->id;
            }
        }

        // Create users if they do not exist.
        if (!empty($newUsers)) {
            $model = $this->getModel();
            $model->createUsers($newUsers);
        }

        // Prepare sorting data
        $this->prepareSorting();

        // Prepare actions
        $this->addToolbar();
        $this->addSidebar();
        $this->setDocument();

        parent::display($tpl);
    }

    /**
     * Prepare sortable fields, sort values and filters.
     */
    protected function prepareSorting()
    {
        // Prepare filters
        $this->listOrder = $this->escape($this->state->get('list.ordering'));
        $this->listDirn  = $this->escape($this->state->get('list.direction'));
        $this->saveOrder = (strcmp($this->listOrder, 'a.ordering') != 0) ? false : true;

        $this->filterForm    = $this->get('FilterForm');
    }

    /**
     * Add a menu on the sidebar of page
     */
    protected function addSidebar()
    {
        IdentityProofHelper::addSubmenu($this->getName());
        $this->sidebar = JHtmlSidebar::render();
    }

    /**
     * Add the page title and toolbar.
     *
     * @since   1.6
     */
    protected function addToolbar()
    {
        // Set toolbar items for the page
        JToolBarHelper::title(JText::_('COM_IDENTITYPROOF_USERS_MANAGER'));
        JToolbarHelper::editList('user.edit');
        JToolBarHelper::custom('users.verify', "ok", "", JText::_("COM_IDENTITYPROOF_VERIFY"));
        JToolBarHelper::custom('users.unverify', "ban-circle", "", JText::_("COM_IDENTITYPROOF_UNVERIFY"));
        JToolbarHelper::divider();
        JToolBarHelper::custom('users.backToDashboard', "dashboard", "", JText::_("COM_IDENTITYPROOF_DASHBOARD"), false);
    }

    /**
     * Method to set up the document properties
     *
     * @return void
     */
    protected function setDocument()
    {
        $this->document->setTitle(JText::_('COM_IDENTITYPROOF_USERS_MANAGER'));

        // Scripts
        JHtml::_('behavior.multiselect');

        JHtml::_('bootstrap.tooltip');
        JHtml::_('formbehavior.chosen', 'select');
    }
}
