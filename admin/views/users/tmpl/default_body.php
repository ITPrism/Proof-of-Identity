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
<?php foreach ($this->items as $i => $item) {?>
    <tr class="row<?php echo $i % 2; ?>">
        <td class="center hidden-phone">
            <?php echo JHtml::_('grid.id', $i, $item->id); ?>
        </td>
        <td class="center">
            <?php echo JHtml::_('identityproofbackend.state', $i, $item->state, 'users.'); ?>
        </td>
        <td class="has-context">
            <a href="<?php echo JRoute::_('index.php?option=com_identityproof&view=user&layout=edit&id=' . $item->id); ?>">
            <?php echo $this->escape($item->name); ?>
            </a>
            <?php echo JHtml::_('identityproofbackend.socialProfiles', $this->socialProfiles, $item->id); ?>
            <div class="small">
                <?php echo JText::sprintf('COM_PROOFOFIDENITY_USERNAME_S_S', JRoute::_('index.php?option=com_users&view=users&filter_search=' . $this->escape($item->email)), $this->escape($item->username)); ?>
            </div>
        </td>
        <td class="center hidden-phone">
            <?php echo $item->id;?>
        </td>
    </tr>
<?php }?>
