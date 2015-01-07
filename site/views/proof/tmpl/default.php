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
<div class="identity-proof<?php echo $this->pageclass_sfx; ?>">
    <?php if ($this->params->get('show_page_heading', 1)) { ?>
        <h1><?php echo $this->escape($this->params->get('page_heading')); ?></h1>
    <?php } ?>

    <?php if($this->params->get("display_https_warning", 1) and !$this->uri->isSSL()) {?>
        <div class="alert">
            <p>
                <i class="icon-warning-sign"></i>
                <?php echo JText::sprintf("COM_IDENTITYPROOF_WARNING_SSL_S", $this->uriHTTPS); ?>
            </p>
        </div>
    <?php } ?>

    <?php if($this->params->get("display_status", 1)) {?>
        <div class="alert alert-info">
            <?php if ($this->user->isVerified()) {?>
            <p>
                <i class="icon-info-sign"></i>
                <?php echo JText::_("COM_IDENTITYPROOF_STATUS_VERIFIED"); ?>
            </p>
            <?php } else { ?>
            <p>
                <i class="icon-info-sign"></i>
                <?php echo JText::_("COM_IDENTITYPROOF_STATUS_NOT_VERIFIED"); ?>
            </p>
            <?php } ?>
        </div>
    <?php } ?>

    <h3><?php echo JText::_("COM_IDENTITYPROOF_FILES");?></h3>
    <div class="row-fluid">
        <div class="span12 well">
            <form action="<?php echo JRoute::_('index.php?option=com_identityproof'); ?>" method="post" name="ipFilesForm" id="ipFilesForm" enctype="multipart/form-data">

                <?php echo $this->form->getControlGroup('title'); ?>

                <div class="control-group">
                    <div class="control-label"><?php echo $this->form->getLabel('file'); ?></div>
                    <div class="controls">
                        <div class="fileupload fileupload-new" data-provides="fileupload">
                            <span class="btn btn-file">
                                <span class="fileupload-new">
                                    <i class="icon-folder-open icon-white"></i>
                                    <?php echo JText::_("COM_IDENTITYPROOF_SELECT_FILE"); ?>
                                </span>
                                <span class="fileupload-exists">
                                    <i class="icon-folder-open icon-white"></i>
                                    <?php echo JText::_("COM_IDENTITYPROOF_CHANGE"); ?>
                                </span>
                                <?php echo $this->form->getInput('file'); ?>
                            </span>
                            <span class="fileupload-preview"></span>
                            <a href="#" class="close fileupload-exists" data-dismiss="fileupload" style="float: none">×</a>
                        </div>
                    </div>
                </div>
                
                <?php if ($this->params->get("display_note", 1)) { ?>
                    <div class="alert alert-info mt5">
                        <h4>
                            <i class="icon-info-sign"></i>
                            <?php echo JText::_("COM_IDENTITYPROOF_INFORMATION"); ?>
                        </h4>
                        <p><?php echo JText::sprintf("COM_IDENTITYPROOF_FILE_TYPES_NOTE", $this->params->get("legal_extensions")); ?></p>
                        <p><?php echo JText::sprintf("COM_IDENTITYPROOF_FILE_SIZE_NOTE", $this->params->get("max_size")); ?></p>

                        <?php if($this->params->get("additional_information", 0)) { ?>
                            <p><?php echo $this->escape($this->params->get("additional_information")); ?></p>
                        <?php } ?>
                    </div>
                <?php } ?>

                <input type="hidden" name="task" value="proof.save"/>
                <?php echo JHtml::_('form.token'); ?>

                <div class="clearfix"></div>
                <button type="submit" class="btn btn-prim">
                    <i class="icon-ok icon-white"></i>
                    <?php echo JText::_("COM_IDENTITYPROOF_SUBMIT")?>
                </button>
            </form>
        </div>
    </div>

    <?php if (!empty($this->files)) { ?>
        <div class="row-fluid">
            <div class="span12">
                <table class="table table-bordered">
                    <thead>
                    <tr>
                        <th class="span5 center"><?php echo JText::_("COM_IDENTITYPROOF_TITLE");?></th>
                        <th class="span2 center hidden-phone"><?php echo JText::_("COM_IDENTITYPROOF_DATE");?></th>
                        <th class="span2 center"><?php echo JText::_("COM_IDENTITYPROOF_STATE");?></th>
                        <th class="span3 hidden-phone">&nbsp;</th>
                    </tr>
                    </thead>
                    <tbody id="js-ipfile-list">
                    <?php foreach ($this->files as $file) {
                        $stateClass = ($file["state"] != 1) ? "" : 'class="success"';
                        ?>
                        <tr id="js-ipfile<?php echo (int)$file["id"];?>" <?php echo $stateClass; ?>>
                            <td><?php echo $this->escape($file["title"]); ?></td>
                            <td class="center hidden-phone"><?php echo JHtml::_('date', $file["record_date"], JText::_('DATE_FORMAT_LC3')); ?></td>
                            <td class="center">
                                <?php echo JHtml::_('identityproof.state', $file["state"]); ?>
                                <?php if (!empty($file["note"])) { ?>
                                    <?php echo JHtml::_('identityproof.note', $file["id"]); ?>
                                <?php } ?>

                            </td>
                            <td class="hidden-phone">
                                <button class="btn hidden-phone js-ipfile-btn-download" data-file-id="<?php echo (int)$file["id"]; ?>">
                                    <i class="icon-download"></i>
                                    <span class="hidden-phone"><?php echo JText::_("COM_IDENTITYPROOF_DOWNLOAD");?></span>
                                </button>

                                <button class="btn btn-danger js-ipfile-btn-remove" data-file-id="<?php echo (int)$file["id"]; ?>">
                                    <i class="icon-trash"></i>
                                    <span class="hidden-phone"><?php echo JText::_("COM_IDENTITYPROOF_DELETE");?></span>
                                </button>
                            </td>
                        </tr>
                    <?php } ?>

                    </tbody>
                </table>
            </div>
        </div>
    <?php } ?>

</div>

<?php
if (!empty($this->files)) {
    echo $this->loadTemplate("modal");
    echo $this->loadTemplate("modalnote");
}
