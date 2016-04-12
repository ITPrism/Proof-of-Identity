<?php
/**
 * @package      CrowdFunding
 * @subpackage   Components
 * @author       Todor Iliev
 * @copyright    Copyright (C) 2016 Todor Iliev <todor@itprism.com>. All rights reserved.
 * @license      GNU General Public License version 3 or later; see LICENSE.txt
 */

// no direct access
defined('_JEXEC') or die;
?>
<div class="row-fluid">
    <div class="span6 form-horizontal">
        <form action="<?php echo JRoute::_('index.php?option=com_identityproof'); ?>" method="post" name="adminForm" id="adminForm" class="form-validate">

            <?php echo $this->form->getControlGroup('title'); ?>

            <div class="control-group ">
                <div class="control-label">
                    <?php echo $this->form->getLabel('filename'); ?>
                </div>
                <div class="controls">
                    <?php echo $this->form->getInput('filename'); ?>
                    <button class="btn" name="download" id="js-iproof-btn-download">
                        <i class="icon-download"></i>
                        <?php echo JText::_('COM_IDENTITYPROOF_DOWNLOAD'); ?>
                    </button>
                </div>
            </div>

            <?php echo $this->form->getControlGroup('key'); ?>
            <?php echo $this->form->getControlGroup('state'); ?>
            <?php echo $this->form->getControlGroup('note'); ?>
            <?php echo $this->form->getControlGroup('record_date'); ?>
            <?php echo $this->form->getControlGroup('user_id'); ?>
            <?php echo $this->form->getControlGroup('id'); ?>


            <input type="hidden" name="task" value=""/>
            <?php echo JHtml::_('form.token'); ?>
        </form>
    </div>
</div>

<?php echo $this->loadTemplate('download'); ?>