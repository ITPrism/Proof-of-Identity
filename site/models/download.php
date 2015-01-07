<?php
/**
 * @package      IdentityProof
 * @subpackage   Component
 * @author       Todor Iliev
 * @copyright    Copyright (C) 2015 Todor Iliev <todor@itprism.com>. All rights reserved.
 * @license      http://www.gnu.org/copyleft/gpl.html GNU/GPL
 */

// no direct access
defined('_JEXEC') or die;

JLoader::register("IdentityProofModelFile", IDENTITYPROOF_PATH_COMPONENT_SITE."/models/file.php");

/**
 * Get a list of items
 */
class IdentityProofModelDownload extends IdentityProofModelFile
{

}
