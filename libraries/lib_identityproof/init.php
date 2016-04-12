<?php
/**
* @package      ProofOfIdentity
* @subpackage   Library
* @author       Todor Iliev
* @copyright    Copyright (C) 2015 Todor Iliev <todor@itprism.com>. All rights reserved.
* @license      http://www.gnu.org/copyleft/gpl.html GNU/GPL
*/

defined('JPATH_PLATFORM') or die;

if (!defined('IDENTITYPROOF_PATH_COMPONENT_ADMINISTRATOR')) {
    define('IDENTITYPROOF_PATH_COMPONENT_ADMINISTRATOR', JPATH_ADMINISTRATOR . '/components/com_identityproof');
}

if (!defined('IDENTITYPROOF_PATH_COMPONENT_SITE')) {
    define('IDENTITYPROOF_PATH_COMPONENT_SITE', JPATH_SITE . '/components/com_identityproof');
}

if (!defined('IDENTITYPROOF_PATH_LIBRARY')) {
    define('IDENTITYPROOF_PATH_LIBRARY', JPATH_LIBRARIES . '/Identityproof');
}

JLoader::registerNamespace('Identityproof', JPATH_LIBRARIES);

// Register helpers
JLoader::register('IdentityproofHelper', IDENTITYPROOF_PATH_COMPONENT_ADMINISTRATOR . '/helpers/identityproof.php');
JLoader::register('IdentityproofHelperRoute', IDENTITYPROOF_PATH_COMPONENT_SITE . '/helpers/route.php');

// Register Observers
JLoader::register('IdentityproofObserverFile', IDENTITYPROOF_PATH_COMPONENT_ADMINISTRATOR .'/tables/observers/file.php');
JObserverMapper::addObserverClassToClass('IdentityproofObserverFile', 'IdentityproofTableFile', array('typeAlias' => 'com_identityproof.file'));

// Register HTML helpers
JHtml::addIncludePath(IDENTITYPROOF_PATH_COMPONENT_SITE . '/helpers/html');
JLoader::register('JHtmlString', JPATH_LIBRARIES . '/joomla/html/html/string.php');

JLog::addLogger(
    array(
        // Sets file name
        'text_file' => 'com_identityproof.errors.php'
    ),
    // Sets messages of all log levels to be sent to the file
    JLog::CRITICAL + JLog::EMERGENCY + JLog::ERROR,
    // The log category/categories which should be recorded in this file
    // In this case, it's just the one category from our extension, still
    // we need to put it inside an array
    array('com_identityproof')
);
