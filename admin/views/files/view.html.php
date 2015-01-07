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

class IdentityProofViewFiles extends JViewLegacy
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
            'b.name'             => JText::_('COM_IDENTITYPROOF_USER'),
            'a.title'            => JText::_('COM_IDENTITYPROOF_TITLE'),
            'a.filename'         => JText::_('COM_IDENTITYPROOF_FILENAME'),
            'a.id'               => JText::_('JGRID_HEADING_ID')
        );
    }

    /**
     * Add a menu on the sidebar of page
     */
    protected function addSidebar()
    {
        JHtmlSidebar::setAction('index.php?option=' . $this->option . '&view=' . $this->getName());

        $statesOptions = array(
            JHtml::_("select.option", 0, JText::_("COM_IDENTITYPROOF_PENDING")),
            JHtml::_("select.option", 1, JText::_("COM_IDENTITYPROOF_VERIFIED")),
            JHtml::_("select.option", -2, JText::_("COM_IDENTITYPROOF_TRASHED")),
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
        JToolBarHelper::title(JText::_('COM_IDENTITYPROOF_FILES_MANAGER'));
        JToolbarHelper::editList('file.edit');
        JToolbarHelper::divider();
        JToolBarHelper::custom('files.reviewed', "ok", "", JText::_("COM_IDENTITYPROOF_REVIEWED"), false);
        JToolBarHelper::custom('files.pending', "clock", "", JText::_("COM_IDENTITYPROOF_PENDING"), false);
        JToolbarHelper::divider();

        if ($this->state->get('filter.state') == -2) {
            JToolbarHelper::deleteList(JText::_("COM_IDENTITYPROOF_DELETE_ITEMS_QUESTION"), 'files.delete', 'JTOOLBAR_EMPTY_TRASH');
        } else {
            JToolbarHelper::trash('files.trashed');
        }

        JToolbarHelper::divider();
        JToolBarHelper::custom('files.backToDashboard', "dashboard", "", JText::_("COM_IDENTITYPROOF_DASHBOARD"), false);
    }

    /**
     * Method to set up the document properties
     *
     * @return void
     */
    protected function setDocument()
    {
        $this->document->setTitle(JText::_('COM_IDENTITYPROOF_FILES_MANAGER'));

        // Scripts
        JHtml::_('behavior.multiselect');

        JHtml::_('bootstrap.tooltip');
        JHtml::_('formbehavior.chosen', 'select');

        JHtml::_("itprism.ui.pnotify");
        JHtml::_('itprism.ui.joomla_helper');
        JHtml::_('itprism.ui.joomla_list');

        $this->document->addScript(JURI::root() . 'media/' . $this->option . '/js/admin/' . JString::strtolower($this->getName()) . '.js');
    }
}
