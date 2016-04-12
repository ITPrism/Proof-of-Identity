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
<div class="modal fade" id="js-iproof-modal-note">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="<?php echo JText::_('COM_IDENTITYPROOF_CLOSE');?>"><span aria-hidden="true">&times;</span></button>
                <h3><?php echo JText::_('COM_IDENTITYPROOF_NOTICE_FOR_YOU');?></h3>
            </div>
            <div class="modal-body">

            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" id="js-iproof-btn-modal-note-close">
                    <span class="fa fa-times-circle"></span>
                    <?php echo JText::_('COM_IDENTITYPROOF_CLOSE');?>
                </button>
            </div>
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div><!-- /.modal -->