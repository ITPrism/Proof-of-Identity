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
                        <?php echo JText::_("COM_IDENTITYPROOF_NOTICE"); ?><span class="star">&nbsp;*</span>
                    </label>
                </div>
                <div class="controls">
                    <textarea aria-required="true" required="" class="input-xxlarge required" id="iproof_form_note" name="note">

                    </textarea>
                </div>
            </div>

            <div class="alert alert-info">
                <i class="icon-info"></i>
                <?php echo JText::_("COM_IDENTITYPROOF_LEAVE_NOTE_INFO"); ?>
            </div>

            <input type="hidden" name="id" value="" id="js-iproof-note-file-id"/>
            <input type="hidden" name="format" value="raw"/>
            <input type="hidden" name="task" value="notification.save"/>
            <input type="hidden" value="1" name="" id="js-iproof-note-token">
        </form>
    </div>
    <div class="modal-footer">
        <a href="#" class="btn btn-primary" id="js-iproof-btn-modal-submit"><?php echo JText::_("COM_IDENTITYPROOF_SUBMIT");?></a>
        <a href="#" class="btn" id="js-iproof-btn-modal-cancel"><?php echo JText::_("COM_IDENTITYPROOF_CANCEL");?></a>
    </div>
</div>
