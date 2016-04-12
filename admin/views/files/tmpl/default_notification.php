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
<div id="js-iproof-modal" class="modal hide fade">
    <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
        <h3><?php echo JText::_("COM_IDENTITYPROOF_LEAVE_NOTE");?></h3>
    </div>
    <div class="modal-body">
        <form action="<?php echo JRoute::_('index.php?option=com_identityproof'); ?>" method="post" name="js-iproof-note-form" id="js-iproof-note-form" autocomplete="off">

            <div class="control-group ">
                <div class="control-label">
                    <label class="required" for="iproof_form_note" id="iproof_form_note-lbl">
                        <?php echo JText::_('COM_IDENTITYPROOF_NOTICE'); ?><span class="star">&nbsp;*</span>
                    </label>
                </div>
                <div class="controls">
                    <textarea aria-required="true" required="" class="input-xxlarge required" id="iproof_form_note" name="note">

                    </textarea>
                </div>
            </div>

            <div class="alert alert-info">
                <i class="icon-info"></i>
                <?php echo JText::_('COM_IDENTITYPROOF_LEAVE_NOTE_INFO'); ?>
            </div>

            <input type="hidden" name="id" value="" id="js-iproof-note-file-id"/>
            <input type="hidden" name="format" value="raw"/>
            <input type="hidden" name="task" value="notification.save"/>
            <input type="hidden" value="1" name="" id="js-iproof-note-token">
        </form>
    </div>
    <div class="modal-footer">
        <img src="../../media/com_identityproof/images/ajax-loader.gif" width="16" height="16" style="display: none;" id="js-iproof-loader"/>
        <button class="btn btn-primary" id="js-iproof-btn-modal-submit">
            <i class="icon-ok"></i> <?php echo JText::_('COM_IDENTITYPROOF_SUBMIT');?></button>
        <button class="btn" id="js-iproof-btn-modal-cancel">
            <i class="icon-cancel"></i> <?php echo JText::_('COM_IDENTITYPROOF_CANCEL');?></button>
    </div>
</div>

