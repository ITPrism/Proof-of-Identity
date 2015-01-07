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
    protected $sortFields;
    protected $saveOrderingUrl;

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

        // Add submenu
        IdentityProofHelper::addSubmenu($this->getName());

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

        if ($this->saveOrder) {
            $this->saveOrderingUrl = 'index.php?option=' . $this->option . '&task=' . $this->getName() . '.saveOrderAjax&format=raw';
            JHtml::_('sortablelist.sortable', $this->getName() . 'List', 'adminForm', strtolower($this->listDirn), $this->saveOrderingUrl);
        }

        $this->sortFields = array(
            'a.id'      => JText::_('JGRID_HEADING_ID'),
            'a.name'    => JText::_('COM_IDENTITYPROOF_NAME'),
            'b.state'   => JText::_('JSTATUS')
        );
    }

    /**
     * Add a menu on the sidebar of page
     */
    protected function addSidebar()
    {
        JHtmlSidebar::setAction('index.php?option=' . $this->option . '&view=' . $this->getName());

        $statesOptions = array(
            JHtml::_("select.option", 0, JText::_("COM_IDENTITYPROOF_NOT_VERIFIED")),
            JHtml::_("select.option", 1, JText::_("COM_IDENTITYPROOF_VERIFIED"))
        );

        JHtmlSidebar::addFilter(
            JText::_('JOPTION_SELECT_PUBLISHED'),
            'filter_state',
            JHtml::_('select.options', $statesOptions, 'value', 'text', $this->state->get('filter.state'), true)
        );

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

        JHtml::_('itprism.ui.joomla_list');
    }
}
