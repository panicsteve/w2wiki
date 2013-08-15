<?php

/*
 * W2
 *
 * Copyright (C) 2007-2009 Steven Frank <http://stevenf.com/>
 * Code may be re-used as long as the above copyright notice is retained.
 * See README.txt for full details.
 *
 * Written with Coda: <http://panic.com/coda/>
 *
 */

// --------------------
// Site layout settings
// --------------------

// BASE_PATH
//
// The base system path to W2.  You only need to change this if we guess wrong.
// You should not use a trailing slash.

define('BASE_PATH', getcwd());

// PAGES_PATH
//
// The path to the raw text documents maintained by W2
// You should not use a trailing slash.

define('PAGES_PATH', BASE_PATH . '/pages');

// BASE_URI
//
// The base URI for this W2 installation.  You only need to change this if we guess wrong.
// You should not use a trailing slash.

define('BASE_URI', str_replace('/index.php', '', $_SERVER['SCRIPT_NAME']));

// SELF
//
// The path component of the URL to the main script, such as: /w2/index.php

define('SELF', $_SERVER['SCRIPT_NAME']);

// VIEW 
//
// Needed only if your web server spawns PHP as a CGI instead of an internal module.
// For example: define('VIEW', '?action=view&page=');

define('VIEW', '');

// DEFAULT_PAGE
//
// The name of the page to show as the "Home" page.
// Value is a string, the title of a page (case-sensitive!)

define('DEFAULT_PAGE', 'Home');

// CSS_FILE
//
// The CSS file to load to style the wiki, relative to BASE_URI

define('CSS_FILE', 'index.css');

// --------------------
// File upload settings
// --------------------

// DISABLE_UPLOADS
//
// Globally enable/disable file uploads

define('DISABLE_UPLOADS', false);

// VALID_UPLOAD_TYPES
//
// Acceptable file types for file uploads.  This is a good idea for security.
// Value is a comma-separated string of MIME types.

define('VALID_UPLOAD_TYPES', 'image/jpeg,image/pjpeg,image/png,image/gif,application/pdf,application/zip,application/x-diskcopy');

// VALID_UPLOAD_EXTS
//
// Acceptable filename extensions for file uploads
// Value is a comma-separated string of filename extensions (case-sensitive!)

define('VALID_UPLOAD_EXTS', 'jpg,jpeg,png,gif,pdf,zip,dmg');

// ------------------
// Interface settings
// ------------------

// TITLE_DATE
//
// The format to use when displaying page modification times.
// See the manual for the PHP 'date()' function for the specification:
// http://php.net/manual/en/function.date.php

define('TITLE_DATE', 'j-M-Y g:i A');
define('TITLE_DATE_NO_TIME', 'j-M-Y');

// EDIT_ROWS
//
// Default size of the text editing area in text rows.

define('EDIT_ROWS', 18);

// AUTOLINK_PAGE_TITLES
//
// Automatically converts any page titles appearing in text into links
// to the named page. This might degrade performance if you have many
// thousands of pages.

define('AUTOLINK_PAGE_TITLES', false);


// -----------------------------
// Security and session settings
// -----------------------------

// REQUIRE_PASSWORD
//
// Is a password required to access this wiki?

define('REQUIRE_PASSWORD', false);

// W2_PASSWORD
//
// The password for the wiki, if REQUIRE_PASSWORD is true
// Replace 'secret' with your password to set your password.

define('W2_PASSWORD', 'secret');

// W2_PASSWORD_HASH
//
// Alternate (more secure) password storage.
// To use a hashed password, Comment out the W2_PASSWORD definition above and uncomment
// this one, using the result of sha1('your_password') as the value.
//
// In Mac OS X, you can do this from the Terminal:   
//     echo -n 'your_password' | openssl sha1
//
// define('W2_PASSWORD_HASH', 'e5e9fa1ba31ecd1ae84f75caaa474f3a663f05f4');

define('W2_PASSWORD_HASH', '');

// allowedIPs
//
// A whitelist of IP addresses that are allowed access to the wiki. 
// If empty, all IPs are allowed.

$allowedIPs = array();

// W2_SESSION_LIFETIME
// 
// How long before a login session expires?  Default is 30 days

define('W2_SESSION_LIFETIME', 60 * 60 * 24 * 30);

// W2_SESSION_NAME
//
// Name for session (used in the cookie)

define('W2_SESSION_NAME', 'W2');

?>
