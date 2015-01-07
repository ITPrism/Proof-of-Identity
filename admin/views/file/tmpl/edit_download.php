<?php
/**
 * @package      CrowdFunding
 * @subpackage   Components
 * @author       Todor Iliev
 * @copyright    Copyright (C) 2015 Todor Iliev <todor@itprism.com>. All rights reserved.
 * @license      http://www.gnu.org/copyleft/gpl.html GNU/GPL
 */

// no direct access
defined('_JEXEC') or die;
?>
<form action="<?php echo JRoute::_('index.php?option=com_identityproof'); ?>" method="post" id="js-iproof-download-form">

    <input type="hidden" name="file_id" value="<?php echo $this->form->getValue('id'); ?>" />
    <input type="hidden" name="format" value="raw" />
    <input type="hidden" name="task" value="file.download" />

    <input type="hidden" value="1" name="" id="js-iproof-download-token">
</form>