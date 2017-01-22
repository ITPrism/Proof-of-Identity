<?php
/**
 * @package      ProofOfIdentity
 * @subpackage   Component
 * @author       Todor Iliev
 * @copyright    Copyright (C) 2017 Todor Iliev <todor@itprism.com>. All rights reserved.
 * @license      GNU General Public License version 3 or later; see LICENSE.txt
 */

// no direct access
defined('_JEXEC') or die;
?>

<div id="js-iproof-modal">
    <div class="panel panel-default">
        <div class="panel-heading">
            <h5><?php echo JText::_('COM_IDENTITYPROOF_DOWNLOAD_FILE');?></h5>
        </div>
        <div class="panel-body" id="js-iproof-modal-download-body">

        </div>
    </div>

    <button type="button" class="btn btn-primary" id="js-iproof-btn-modal-submit">
        <span class="fa fa-check-circle"></span>
        <?php echo JText::_('COM_IDENTITYPROOF_SUBMIT');?>
    </button>
    <button type="button" class="btn btn-default" id="js-iproof-btn-modal-cancel">
        <span class="fa fa-times-circle"></span>
        <?php echo JText::_('COM_IDENTITYPROOF_CANCEL');?>
    </button>
</div>