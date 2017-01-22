<?php
/**
 * @package      CrowdFunding
 * @subpackage   Components
 * @author       Todor Iliev
 * @copyright    Copyright (C) 2017 Todor Iliev <todor@itprism.com>. All rights reserved.
 * @license      GNU General Public License version 3 or later; see LICENSE.txt
 */

// no direct access
defined('_JEXEC') or die;
?>
<div class="row-fluid">
    <div class="span6" >
        <h4><img src="../../../media/com_identityproof/images/facebook-icon.png">&nbsp;<?php echo JText::_('COM_IDENTITYPROOF_FACEBOOK'); ?></h4>
        <?php if (!$this->facebook->getId()) {?>
        <p class="alert alert-warning"><?php echo JText::_('COM_IDENTITYPROOF_NO_FACEBOOK_DATA'); ?></p>
        <?php } else { ?>
        <table class="table table-bordered">
            <tr>
                <td class="span4"><?php echo JText::_('COM_IDENTITYPROOF_NAME'); ?></td>
                <td class="span8"><?php echo JHtml::_('identityproofbackend.profile', $this->facebook); ?></td>
            </tr>
            <tr>
                <td class="span4"><?php echo JText::_('COM_IDENTITYPROOF_FACEBOOK_ID'); ?></td>
                <td class="span8"><?php echo $this->facebook->getFacebookId(); ?></td>
            </tr>
            <tr>
                <td class="span4"><?php echo JText::_('COM_IDENTITYPROOF_HOMETOWN'); ?></td>
                <td class="span8"><?php echo $this->facebook->getHometown(); ?></td>
            </tr>
            <tr>
                <td class="span4"><?php echo JText::_('COM_IDENTITYPROOF_WEBSITE'); ?></td>
                <td class="span8"><?php echo JHtml::_('identityproofbackend.website', $this->facebook->getWebsite()); ?></td>
            </tr>
            <tr>
                <td class="span4"><?php echo JText::_('COM_IDENTITYPROOF_VERIFIED'); ?></td>
                <td class="span8"><?php echo JHtml::_('identityproof.status', $this->facebook->isVerified()); ?></td>
            </tr>
            <tr>
                <td class="span4"><?php echo JText::_('COM_IDENTITYPROOF_GENDER'); ?></td>
                <td class="span8"><?php echo $this->facebook->getGender(); ?></td>
            </tr>
        </table>
        <?php } ?>
    </div>

    <div class="span6">
        <h4><img src="../../../media/com_identityproof/images/twitter-icon.png">&nbsp;<?php echo JText::_('COM_IDENTITYPROOF_TWITTER'); ?></h4>
        <?php if (!$this->twitter->getId()) {?>
            <p class="alert alert-warning"><?php echo JText::_('COM_IDENTITYPROOF_NO_TWITTER_DATA'); ?></p>
        <?php } else { ?>
            <table class="table table-bordered">
                <tr>
                    <td class="span4"><?php echo JText::_('COM_IDENTITYPROOF_NAME'); ?></td>
                    <td class="span8"><?php echo JHtml::_('identityproofbackend.profile', $this->twitter); ?></td>
                </tr>
                <tr>
                    <td class="span4"><?php echo JText::_('COM_IDENTITYPROOF_TWITTER_ID'); ?></td>
                    <td class="span8"><?php echo $this->twitter->getTwitterId(); ?></td>
                </tr>
                <tr>
                    <td class="span4"><?php echo JText::_('COM_IDENTITYPROOF_LOCATION'); ?></td>
                    <td class="span8"><?php echo $this->twitter->getLocation(); ?></td>
                </tr>
                <tr>
                    <td class="span4"><?php echo JText::_('COM_IDENTITYPROOF_WEBSITE'); ?></td>
                    <td class="span8"><?php echo JHtml::_('identityproofbackend.website', $this->twitter->getWebsite()); ?></td>
                </tr>
                <tr>
                    <td class="span4"><?php echo JText::_('COM_IDENTITYPROOF_VERIFIED'); ?></td>
                    <td class="span8"><?php echo JHtml::_('identityproof.status', $this->twitter->isVerified()); ?></td>
                </tr>
            </table>
        <?php } ?>
    </div>
</div>

<div class="row-fluid">
    <div class="span6">
        <h4><img src="../../../media/com_identityproof/images/google-plus-icon.png">&nbsp;<?php echo JText::_('COM_IDENTITYPROOF_GOOGLE_PLUS'); ?></h4>
        <?php if (!$this->twitter->getId()) {?>
            <p class="alert alert-warning"><?php echo JText::_('COM_IDENTITYPROOF_NO_GOOGLE_DATA'); ?></p>
        <?php } else { ?>
            <table class="table table-bordered">
                <tr>
                    <td class="span4"><?php echo JText::_('COM_IDENTITYPROOF_NAME'); ?></td>
                    <td class="span8"><?php echo JHtml::_('identityproofbackend.profile', $this->google); ?></td>
                </tr>
                <tr>
                    <td class="span4"><?php echo JText::_('COM_IDENTITYPROOF_GOOGLE_ID'); ?></td>
                    <td class="span8"><?php echo $this->google->getGoogleId(); ?></td>
                </tr>
                <tr>
                    <td class="span4"><?php echo JText::_('COM_IDENTITYPROOF_LOCATION'); ?></td>
                    <td class="span8"><?php echo $this->google->getLocation(); ?></td>
                </tr>
                <tr>
                    <td class="span4"><?php echo JText::_('COM_IDENTITYPROOF_WEBSITE'); ?></td>
                    <td class="span8"><?php echo JHtml::_('identityproofbackend.website', $this->google->getWebsite()); ?></td>
                </tr>
                <tr>
                    <td class="span4"><?php echo JText::_('COM_IDENTITYPROOF_VERIFIED'); ?></td>
                    <td class="span8"><?php echo JHtml::_('identityproof.status', $this->google->isVerified()); ?></td>
                </tr>
            </table>
        <?php } ?>
    </div>
    </div>

    <div class="span6">

    </div>
</div>
