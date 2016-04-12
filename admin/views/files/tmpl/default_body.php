<?php
/**
 * @package      ProofOfIdentity
 * @subpackage   Component
 * @author       Todor Iliev
 * @copyright    Copyright (C) 2016 Todor Iliev <todor@itprism.com>. All rights reserved.
 * @license      GNU General Public License version 3 or later; see LICENSE.txt
 */

// no direct access
defined('_JEXEC') or die;
?>
<?php foreach ($this->items as $i => $item) {
    $stateClass = ($item->state != 1) ? "" : "success";?>
    <tr class="row<?php echo $i % 2; ?> <?php echo $stateClass; ?>">
        <td class="center hidden-phone">
            <?php echo JHtml::_('grid.id', $i, $item->id); ?>
        </td>
        <td class="center">
            <?php echo JHtml::_('identityproofbackend.filestate', $i, $item->state, "files."); ?>
        </td>
        <td>
            <a href="<?php echo JRoute::_('index.php?option=com_identityproof&view=file&layout=edit&id=' . $item->id); ?>">
                <?php echo $this->escape($item->title); ?>
            </a>
        </td>
        <td class="hidden-phone">
            <a href="javascript: void(0);" class="btn js-iproof-download-btn hasTooltip" data-file-id="<?php echo $item->id;?>" title="<?php echo JText::_('COM_IDENTITYPROOF_DOWNLOAD'); ?>">
                <i class="icon-download"></i>
            </a>
            <a href="javascript: void(0);" class="btn js-iproof-note-btn hasTooltip" data-file-id="<?php echo $item->id;?>" title="<?php echo (!empty($item->note)) ? JText::_('COM_IDENTITYPROOF_EDIT_NOTE') : JText::_('COM_IDENTITYPROOF_LEAVE_NOTE'); ?>">
                <i class="<?php echo !empty($item->note) ? 'icon-edit' : 'icon-pencil';?>"></i>
            </a>
        </td>
        <td class="hidden-phone">
            <?php echo $this->escape($item->filename); ?>
        </td>
        <td class="hidden-phone">
            <a href="<?php echo JRoute::_('index.php?option=com_identityproof&view=users&filter_search=id:' . $item->user_id); ?>">
                <?php echo $this->escape($item->name); ?>
            </a>
        </td>
        <td class="center hidden-phone">
            <?php echo $item->id;?>
        </td>
    </tr>
<?php }?>
