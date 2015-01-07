<?php
/**
 * @package      ProofOfIdentity
 * @subpackage   Constants
 * @author       Todor Iliev
 * @copyright    Copyright (C) 2015 Todor Iliev <todor@itprism.com>. All rights reserved.
 * @license      http://www.gnu.org/copyleft/gpl.html GNU/GPL
 */

defined('JPATH_PLATFORM') or die;

/**
 * Proof of Identity constants
 *
 * @package      ProofOfIdentity
 * @subpackage   Constants
 */
class IdentityProofConstants
{
    // States
    const PUBLISHED   = 1;
    const UNPUBLISHED = 0;
    const TRASHED     = -2;

    // Mail modes - html and plain text.
    const MAIL_MODE_HTML  = true;
    const MAIL_MODE_PLAIN = false;

    // User states
    const VERIFIED     = 1;
    const NOT_VERIFIED = 0;
}
