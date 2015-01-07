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
?>
<?php foreach ($this->items as $i => $item) {?>
    <tr class="row<?php echo $i % 2; ?>">
        <td class="center hidden-phone">
            <?php echo JHtml::_('grid.id', $i, $item->id); ?>
        </td>
        <td class="center">
            <?php echo JHtml::_('identityproofbackend.state', $i, $item->state, "users."); ?>
        </td>
        <td class="title">
            <a href="<?php echo JRoute::_("index.php?option=com_identityproof&view=user&layout=edit&id=" . $item->id); ?>">
            <?php echo $this->escape($item->name); ?>
            </a>
            <a href="<?php echo JRoute::_("index.php?option=com_users&view=users&filter_search=id:" . $item->id); ?>" class="btn btn-mini hasTooltip" title="<?php echo JText::_("COM_IDENTITYPROOF_ADDITIONAL_INFORMATION"); ?>">
                <i class="icon-user"></i>
            </a>
        </td>
        <td class="center hidden-phone">
            <?php echo $item->id;?>
        </td>
    </tr>
<?php }?>