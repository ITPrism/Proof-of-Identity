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
<?php if (!empty($this->sidebar)): ?>
<div id="j-sidebar-container" class="span2">
    <?php echo $this->sidebar; ?>
</div>
<div id="j-main-container" class="span10">
<?php else : ?>
<div id="j-main-container">
<?php endif; ?>
    <div class="span8">
        <?php if(!$this->uri->isSSL()) {?>
            <div class="alert alert-block">
                <h4><?php echo JText::_("COM_IDENTITYPROOF_WARNING"); ?></h4>
                <p><?php echo JText::sprintf("COM_IDENTITYPROOF_WARNING_SSL_S", $this->uriHTTPS); ?></p>
            </div>
        <?php } ?>
    </div>

    <div class="span4">
        <a href="#" target="_blank"><img src="../media/com_identityproof/images/logo.png" alt="<?php echo JText::_("COM_IDENTITYPROOF"); ?>"/></a>
        <a href="http://itprism.com" target="_blank" title="<?php echo JText::_("COM_IDENTITYPROOF_PRODUCT"); ?>">
            <img src="../media/com_identityproof/images/product_of_itprism.png" alt="<?php echo JText::_("COM_IDENTITYPROOF_PRODUCT"); ?>"/>
        </a>
        <p><?php echo JText::_("COM_IDENTITYPROOF_YOUR_VOTE"); ?></p>
        <p><?php echo JText::_("COM_IDENTITYPROOF_SUBSCRIPTION"); ?></p>
        <table class="table table-striped">
            <tbody>
            <tr>
                <td><?php echo JText::_("COM_IDENTITYPROOF_INSTALLED_VERSION"); ?></td>
                <td><?php echo $this->version->getShortVersion(); ?></td>
            </tr>
            <tr>
                <td><?php echo JText::_("COM_IDENTITYPROOF_RELEASE_DATE"); ?></td>
                <td><?php echo $this->version->releaseDate ?></td>
            </tr>
            <tr>
                <td><?php echo JText::_("COM_IDENTITYPROOF_PRISM_LIBRARY_VERSION"); ?></td>
                <td><?php echo $this->prismVersion; ?></td>
            </tr>
            <tr>
                <td><?php echo JText::_("COM_IDENTITYPROOF_COPYRIGHT"); ?></td>
                <td><?php echo $this->version->copyright; ?></td>
            </tr>
            <tr>
                <td><?php echo JText::_("COM_IDENTITYPROOF_LICENSE"); ?></td>
                <td><?php echo $this->version->license; ?></td>
            </tr>
            </tbody>
        </table>

        <?php if (!empty($this->prismVersionLowerMessage)) {?>
            <p class="alert alert-warning"><i class="icon-warning"></i> <?php echo $this->prismVersionLowerMessage; ?></p>
        <?php } ?>
    </div>
</div>
