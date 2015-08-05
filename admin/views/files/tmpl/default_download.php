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
<form action="<?php echo JRoute::_('index.php?option=com_identityproof'); ?>" method="post" id="js-iproof-download-form">
    <input type="hidden" name="file_id" value="" id="js-iproof-download-id" />
    <input type="hidden" name="format" value="raw" />
    <input type="hidden" name="task" value="file.download" />

    <?php echo JHtml::_('form.token'); ?>
</form>

