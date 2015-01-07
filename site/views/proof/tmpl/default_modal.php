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
        <h3><?php echo JText::_("COM_IDENTITYPROOF_DOWNLOAD_FILE");?></h3>
    </div>
    <div class="modal-body">

    </div>
    <div class="modal-footer">
        <a href="#" class="btn btn-primary" id="js-iproof-btn-modal-submit"><?php echo JText::_("COM_IDENTITYPROOF_SUBMIT");?></a>
        <a href="#" class="btn" id="js-iproof-btn-modal-cancel"><?php echo JText::_("COM_IDENTITYPROOF_CANCEL");?></a>
    </div>
</div>