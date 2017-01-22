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
<div id="js-iproof-modal-note">
    <div class="panel panel-default">
        <div class="panel-body">
            <p id="js-iproof-modal-body"></p>
        </div>
    </div>

    <button type="button" class="btn btn-default pull-right" id="js-iproof-btn-note-close">
        <span class="fa fa-times-circle"></span>
        <?php echo JText::_('COM_IDENTITYPROOF_CLOSE');?>
    </button>
</div>


