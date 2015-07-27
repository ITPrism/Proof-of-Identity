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
<div class="modal fade" id="js-iproof-modal">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="<?php echo JText::_("COM_IDENTITYPROOF_CLOSE");?>"><span aria-hidden="true">&times;</span></button>
                <h3><?php echo JText::_("COM_IDENTITYPROOF_DOWNLOAD_FILE");?></h3>
            </div>
            <div class="modal-body">

            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-primary" id="js-iproof-btn-modal-submit">
                    <span class="glyphicon glyphicon-ok-sign"></span>
                    <?php echo JText::_("COM_IDENTITYPROOF_SUBMIT");?>
                </button>
                <button type="button" class="btn btn-default" id="js-iproof-btn-modal-cancel">
                    <span class="glyphicon glyphicon-remove-sign"></span>
                    <?php echo JText::_("COM_IDENTITYPROOF_CANCEL");?>
                </button>
            </div>
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div><!-- /.modal -->