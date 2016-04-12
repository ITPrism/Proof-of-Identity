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
<div class="ip-proof<?php echo $this->pageclass_sfx; ?>">
    <?php if ($this->params->get('show_page_heading', 1)) { ?>
        <h1><?php echo $this->escape($this->params->get('page_heading')); ?></h1>
    <?php } ?>

    <?php if($this->params->get('display_https_warning', 1) and !$this->uri->isSSL()) {?>
        <div class="alert alert-warning">
            <p>
                <span class="fa fa-exclamation-triangle"></span>
                <?php echo JText::sprintf('COM_IDENTITYPROOF_WARNING_SSL_S', $this->uriHTTPS); ?>
            </p>
        </div>
    <?php } ?>

    <?php if($this->params->get('display_status', 1)) {?>
        <div class="alert alert-info">
            <?php if ($this->user->isVerified()) {?>
            <p>
                <span class="fa fa-info-circle"></span>
                <?php echo JText::_('COM_IDENTITYPROOF_STATUS_VERIFIED'); ?>
            </p>
            <?php } else { ?>
            <p>
                <span class="fa fa-info-circle"></span>
                <?php echo JText::_('COM_IDENTITYPROOF_STATUS_NOT_VERIFIED'); ?>
            </p>
            <?php } ?>
        </div>
    <?php } ?>

    <div class="panel panel-default">
        <div class="panel-heading">
            <h3><span class="fa fa-files-o"></span>&nbsp;<?php echo JText::_('COM_IDENTITYPROOF_FILES');?></h3>
        </div>

        <div class="panel-body">
            <div class="row">
                <div class="col-md-12">
                    <form action="<?php echo JRoute::_('index.php?option=com_identityproof'); ?>" method="post" name="ipFilesForm" id="ipFilesForm" enctype="multipart/form-data">

                        <div class="form-group">
                            <?php echo $this->form->getLabel('title'); ?>
                            <?php echo $this->form->getInput('title'); ?>
                        </div>

                        <div class="form-group">
                            <?php echo $this->form->getLabel('file'); ?>
                            <?php echo $this->form->getInput('file'); ?>
                        </div>
                        <p class="help-block-xsmall"><?php echo JText::sprintf('COM_IDENTITYPROOF_FILE_TYPES_NOTE', $this->params->get('legal_extensions')); ?> <?php echo JText::sprintf('COM_IDENTITYPROOF_FILE_SIZE_NOTE_D', $this->maxFileSize); ?></p>

                        <?php if ($this->params->get('display_note', 0)) { ?>
                            <div class="alert alert-info mt-5">
                                <h4>
                                    <span class="fa fa-info-circle"></span>
                                    <?php echo JText::_('COM_IDENTITYPROOF_INFORMATION'); ?>
                                </h4>
                                <?php if($this->params->get('additional_information', 0)) { ?>
                                    <p><?php echo $this->escape($this->params->get('additional_information')); ?></p>
                                <?php } ?>
                            </div>
                        <?php } ?>

                        <input type="hidden" name="task" value="proof.save"/>
                        <?php echo JHtml::_('form.token'); ?>

                        <button type="submit" class="btn btn-primary">
                            <span class="fa fa-check-circle"></span>
                            <?php echo JText::_('COM_IDENTITYPROOF_SUBMIT'); ?>
                        </button>
                    </form>
                </div>
            </div>

            <?php if (count($this->files) > 0) { ?>
            <table class="table table-bordered mt-20">
                <thead>
                <tr>
                    <th><?php echo JText::_('COM_IDENTITYPROOF_TITLE');?></th>
                    <th class="col-md-2 center hidden-xs"><?php echo JText::_('COM_IDENTITYPROOF_DATE');?></th>
                    <th class="col-md-2 center"><?php echo JText::_('COM_IDENTITYPROOF_STATE');?></th>
                    <th class="col-md-2 hidden-xs">&nbsp;</th>
                </tr>
                </thead>
                <tbody id="js-ipfile-list">
                <?php foreach ($this->files as $file) {
                    $stateClass = ((int)$file['state'] !== 1) ? '' : 'class="success"';
                    ?>
                    <tr id="js-ipfile<?php echo (int)$file['id'];?>" <?php echo $stateClass; ?>>
                        <td><?php echo $this->escape($file['title']); ?></td>
                        <td class="center hidden-xs"><?php echo JHtml::_('date', $file['record_date'], JText::_('DATE_FORMAT_LC3')); ?></td>
                        <td class="center">
                            <?php echo JHtml::_('identityproof.state', $file['state']); ?>
                            <?php if (!empty($file["note"])) { ?>
                                <?php echo JHtml::_('identityproof.note', $file['id']); ?>
                            <?php } ?>

                        </td>
                        <td class="hidden-phone">
                            <button class="btn btn-default hidden-phone js-ipfile-btn-download" data-file-id="<?php echo (int)$file['id']; ?>">
                                <i class="fa fa-download"></i>
                                <span class="hidden-xs"><?php echo JText::_('COM_IDENTITYPROOF_DOWNLOAD');?></span>
                            </button>

                            <button class="btn btn-danger js-ipfile-btn-remove" data-file-id="<?php echo (int)$file['id']; ?>">
                                <span class="fa fa-trash-o"></span>
                                <span class="hidden-xs"><?php echo JText::_('COM_IDENTITYPROOF_REMOVE');?></span>
                            </button>
                        </td>
                    </tr>
                <?php } ?>

                </tbody>
            </table>
        <?php } ?>

        </div>
    </div>

    <?php
    if (!empty($this->event->onPrepareVerification)) {
        echo $this->event->onPrepareVerification;
    }
    ?>

</div>

<?php
if (!empty($this->files)) {
    echo $this->loadTemplate('modal');
    echo $this->loadTemplate('modalnote');
}
