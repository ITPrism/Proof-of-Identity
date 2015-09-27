<?php
/**
* @package      ProofOfIdentity
* @subpackage   Library
* @author       Todor Iliev
* @copyright    Copyright (C) 2015 Todor Iliev <todor@itprism.com>. All rights reserved.
* @license      GNU General Public License version 3 or later; see LICENSE.txt
*/

defined('JPATH_PLATFORM') or die;

if (!defined("IDENTITYPROOF_PATH_COMPONENT_ADMINISTRATOR")) {
    define("IDENTITYPROOF_PATH_COMPONENT_ADMINISTRATOR", JPATH_ADMINISTRATOR . "/components/com_identityproof");
}

if (!defined("IDENTITYPROOF_PATH_COMPONENT_SITE")) {
    define("IDENTITYPROOF_PATH_COMPONENT_SITE", JPATH_SITE . "/components/com_identityproof");
}

if (!defined("IDENTITYPROOF_PATH_LIBRARY")) {
    define("IDENTITYPROOF_PATH_LIBRARY", JPATH_LIBRARIES . "/IdentityProof");
}

JLoader::registerNamespace('IdentityProof', JPATH_LIBRARIES);

// Register helpers
JLoader::register("IdentityProofHelper", IDENTITYPROOF_PATH_COMPONENT_ADMINISTRATOR . "/helpers/identityproof.php");
JLoader::register("IdentityProofHelperRoute", IDENTITYPROOF_PATH_COMPONENT_SITE . "/helpers/route.php");

// Register Observers
JLoader::register("IdentityProofObserverFile", IDENTITYPROOF_PATH_COMPONENT_ADMINISTRATOR ."/tables/observers/file.php");
JObserverMapper::addObserverClassToClass('IdentityProofObserverFile', 'IdentityProofTableFile', array('typeAlias' => 'com_identityproof.file'));

// Register HTML helpers
JHtml::addIncludePath(IDENTITYPROOF_PATH_COMPONENT_SITE . '/helpers/html');
JLoader::register('JHtmlString', JPATH_LIBRARIES . '/joomla/html/html/string.php');
