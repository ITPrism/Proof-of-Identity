<?php
/**
 * @package      Identityproof
 * @subpackage   Component
 * @author       Todor Iliev
 * @copyright    Copyright (C) 2017 Todor Iliev <todor@itprism.com>. All rights reserved.
 * @license      GNU General Public License version 3 or later; see LICENSE.txt
 */

// no direct access
defined('_JEXEC') or die;

JLoader::register('IdentityproofModelFile', IDENTITYPROOF_PATH_COMPONENT_SITE.'/models/file.php');

/**
 * Get a list of items
 */
class IdentityproofModelDownload extends IdentityproofModelFile
{

}
