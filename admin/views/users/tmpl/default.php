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
<form action="<?php echo JRoute::_('index.php?option=com_identityproof&view=users'); ?>" method="post" name="adminForm" id="adminForm">
    <?php if(!empty( $this->sidebar)): ?>
    <div id="j-sidebar-container" class="span2">
        <?php echo $this->sidebar; ?>
    </div>
    <div id="j-main-container" class="span10">
    <?php else : ?>
    <div id="j-main-container">
    <?php endif;?>
        <?php echo JLayoutHelper::render('joomla.searchtools.default', array('view' => $this)); ?>
        <table class="table table-striped" id="usersList">
            <thead><?php echo $this->loadTemplate('head');?></thead>
            <tfoot><?php echo $this->loadTemplate('foot');?></tfoot>
            <tbody><?php echo $this->loadTemplate('body');?></tbody>
        </table>

        <input type="hidden" name="boxchecked" value="0" />
        <input type="hidden" name="task" value="" />
        <?php echo JHtml::_('form.token'); ?>
    </div>
</form>

<div class="modal hide fade" tabindex="-1" id="js-modal-socialprofiles" style="display: none; min-height: 500px;" aria-hidden="true">
	<div class="modal-header">
        <button data-dismiss="modal" class="close" type="button">×</button>
        <h3><?php echo JText::_('COM_IDENTITYPROOF_SOCIAL_PROFILES'); ?></h3>
	</div>
    <div class="modal-body" id="js-socialprofiles-body" style="max-height: 618px;">
	</div>
</div>