<?php
/**
* @package      ProofOfIdentity
* @subpackage   Library
* @author       Todor Iliev
* @copyright    Copyright (C) 2015 Todor Iliev <todor@itprism.com>. All rights reserved.
* @license      http://www.gnu.org/copyleft/gpl.html GNU/GPL
*/

defined('JPATH_PLATFORM') or die;

if (!defined("IDENTITYPROOF_PATH_COMPONENT_ADMINISTRATOR")) {
    define("IDENTITYPROOF_PATH_COMPONENT_ADMINISTRATOR", JPATH_ADMINISTRATOR . DIRECTORY_SEPARATOR . "components" . DIRECTORY_SEPARATOR . "com_identityproof");
}

if (!defined("IDENTITYPROOF_PATH_COMPONENT_SITE")) {
    define("IDENTITYPROOF_PATH_COMPONENT_SITE", JPATH_SITE . DIRECTORY_SEPARATOR . "components" . DIRECTORY_SEPARATOR . "com_identityproof");
}

if (!defined("IDENTITYPROOF_PATH_LIBRARY")) {
    define("IDENTITYPROOF_PATH_LIBRARY", JPATH_LIBRARIES . DIRECTORY_SEPARATOR . "identityproof");
}

// Register version and constants
JLoader::register("IdentityProofVersion", IDENTITYPROOF_PATH_LIBRARY . DIRECTORY_SEPARATOR . "version.php");
JLoader::register("IdentityProofConstants", IDENTITYPROOF_PATH_LIBRARY . DIRECTORY_SEPARATOR . "constants.php");

// Register helpers
JLoader::register("IdentityProofHelper", IDENTITYPROOF_PATH_COMPONENT_ADMINISTRATOR . DIRECTORY_SEPARATOR . "helpers" . DIRECTORY_SEPARATOR . "identityproof.php");
JLoader::register("IdentityProofHelperRoute", IDENTITYPROOF_PATH_COMPONENT_SITE . "/helpers/route.php");

// Register Observers
JLoader::register("IdentityProofObserverFile", IDENTITYPROOF_PATH_COMPONENT_ADMINISTRATOR ."/tables/observers/file.php");
JObserverMapper::addObserverClassToClass('IdentityProofObserverFile', 'IdentityProofTableFile', array('typeAlias' => 'com_identityproof.file'));

// Register HTML helpers
JHtml::addIncludePath(IDENTITYPROOF_PATH_COMPONENT_SITE . DIRECTORY_SEPARATOR . 'helpers' . DIRECTORY_SEPARATOR . 'html');
JLoader::register(
    'JHtmlString',
    JPATH_LIBRARIES . DIRECTORY_SEPARATOR . 'joomla'. DIRECTORY_SEPARATOR .'html'. DIRECTORY_SEPARATOR .'html'. DIRECTORY_SEPARATOR .'string.php'
);
