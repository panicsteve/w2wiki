<?php

/*
 * W2
 *
 * Copyright (C) 2007-2011 Steven Frank <http://stevenf.com/>
 *
 * Code may be re-used as long as the above copyright notice is retained.
 * See README.txt for full details.
 *
 * Written with Coda: <http://panic.com/coda/>
 *
 */
 
include_once "markdown.php";

// User configurable options:

include_once "config.php";

ini_set('session.gc_maxlifetime', W2_SESSION_LIFETIME);

session_set_cookie_params(W2_SESSION_LIFETIME);
session_name(W2_SESSION_NAME);
session_start();

if ( count($allowedIPs) > 0 )
{
	$ip = $_SERVER['REMOTE_ADDR'];
	$accepted = false;
	
	foreach ( $allowedIPs as $allowed )
	{
		if ( strncmp($allowed, $ip, strlen($allowed)) == 0 )
		{
			$accepted = true;
			break;
		}
	}
	
	if ( !$accepted )
	{
		print "<html><body>Access from IP address $ip is not allowed";
		print "</body></html>";
		exit;
	}
}

if ( REQUIRE_PASSWORD && !isset($_SESSION['password']) )
{
	if ( !defined('W2_PASSWORD_HASH') || W2_PASSWORD_HASH == '' )
		define('W2_PASSWORD_HASH', sha1(W2_PASSWORD));
	
	if ( (isset($_POST['p'])) && (sha1($_POST['p']) == W2_PASSWORD_HASH) )
		$_SESSION['password'] = W2_PASSWORD_HASH;
	else
	{
		print "<!DOCTYPE html PUBLIC \"-//W3C//DTD XHTML 1.0 Strict//EN\" \"http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd\">\n";
		print "<html>\n";
		print "<head>\n";
		print "<link rel=\"apple-touch-icon\" href=\"apple-touch-icon.png\"/>";
		print "<meta name=\"viewport\" content=\"width=device-width, initial-scale=1.0, minimum-scale=1.0, user-scalable=false\" />\n";
		
		print "<link type=\"text/css\" rel=\"stylesheet\" href=\"" . BASE_URI . "/" . CSS_FILE ."\" />\n";
		print "<title>Log In</title>\n";
		print "</head>\n";
		print "<body><form method=\"post\">";
		print "<input type=\"password\" name=\"p\">\n";
		print "<input type=\"submit\" value=\"Go\"></form>";
		print "</body></html>";
		exit;
	}
}

// Support functions

function printToolbar()
{
	global $upage, $page, $action;

	print "<div class=\"toolbar\">";
	print "<a class=\"tool first\" href=\"" . SELF . "?action=edit&amp;page=$upage\">Edit</a> ";
	print "<a class=\"tool\" href=\"" . SELF . "?action=new\">New</a> ";

	if ( !DISABLE_UPLOADS )
		print "<a class=\"tool\" href=\"" . SELF . VIEW . "?action=upload\">Upload</a> ";

 	print "<a class=\"tool\" href=\"" . SELF . "?action=all_name\">All</a> ";
	print "<a class=\"tool\" href=\"" . SELF . "?action=all_date\">Recent</a> ";
 	print "<a class=\"tool\" href=\"" . SELF . "\">". DEFAULT_PAGE . "</a>";
 	
	if ( REQUIRE_PASSWORD )
		print '<a class="tool" href="' . SELF . '?action=logout">Exit</a>';

	print "<form method=\"post\" action=\"" . SELF . "?action=search\">\n";
	print "<input class=\"tool\" placeholder=\"Search\" size=\"6\" id=\"search\" type=\"text\" name=\"q\" /></form>\n";
		
	print "</div>\n";
}


function descLengthSort($val_1, $val_2) 
{ 
	$retVal = 0;

	$firstVal = strlen($val_1); 
	$secondVal = strlen($val_2);

	if ( $firstVal > $secondVal ) 
		$retVal = -1; 
	
	else if ( $firstVal < $secondVal ) 
		$retVal = 1; 

	return $retVal; 
}


function toHTML($inText)
{
	global $page;
	
	$dir = opendir(PAGES_PATH);
	while ( $filename = readdir($dir) )
	{
		if ( $filename{0} == '.' )
			continue;
			
		$filename = preg_replace("/(.*?)\.txt/", "\\1", $filename);
		$filenames[] = $filename;
	}
	closedir($dir);
	
	uasort($filenames, "descLengthSort"); 

	if ( AUTOLINK_PAGE_TITLES )
	{	
		foreach ( $filenames as $filename )
		{
	 		$inText = preg_replace("/(?<![\>\[\/])($filename)(?!\]\>)/im", "<a href=\"" . SELF . VIEW . "/$filename\">\\1</a>", $inText);
		}
	}
	
 	$inText = preg_replace("/\[\[(.*?)\]\]/", "<a href=\"" . SELF . VIEW . "/\\1\">\\1</a>", $inText);
	$inText = preg_replace("/\{\{(.*?)\}\}/", "<img src=\"" . BASE_URI . "/images/\\1\" alt=\"\\1\" />", $inText);
	$inText = preg_replace("/message:(.*?)\s/", "[<a href=\"message:\\1\">email</a>]", $inText);

	$html = Markdown($inText);
	$inText = htmlentities($inText);

	return $html;
}

function sanitizeFilename($inFileName)
{
	return str_replace(array('..', '~', '/', '\\', ':'), '-', $inFileName);
}

function destroy_session()
{
	if ( isset($_COOKIE[session_name()]) )
		setcookie(session_name(), '', time() - 42000, '/');

	session_destroy();
	unset($_SESSION["password"]);
	unset($_SESSION);
}

// Support PHP4 by defining file_put_contents if it doesn't already exist

if ( !function_exists('file_put_contents') )
{
    function file_put_contents($n, $d)
    {
		$f = @fopen($n, "w");
		
		if ( !$f )
		{
			return false;
		}
		else
		{
			fwrite($f, $d);
			fclose($f);
			return true;
		}
    }
}

// Main code

if ( isset($_REQUEST['action']) )
	$action = $_REQUEST['action'];
else 
	$action = 'view';

// Look for page name following the script name in the URL, like this:
// http://stevenf.com/w2demo/index.php/Markdown%20Syntax
//
// Otherwise, get page name from 'page' request variable.

if ( preg_match('@^/@', @$_SERVER["PATH_INFO"]) ) 
	$page = sanitizeFilename(substr($_SERVER["PATH_INFO"], 1));
else 
	$page = sanitizeFilename(@$_REQUEST['page']);

$upage = urlencode($page);

if ( $page == "" )
	$page = DEFAULT_PAGE;

$filename = PAGES_PATH . "/$page.txt";

if ( file_exists($filename) )
{
	$text = file_get_contents($filename);
}
else
{
	if ( $action != "save" && $action != "all_name" && $action != "all_date" && $action != "upload" && $action != "new" && $action != "logout" && $action != "uploaded" && $action != "search" && $action != "view" )
	{
		$action = "edit";
	}
}

if ( $action == "edit" || $action == "new" )
{
	$formAction = SELF . (($action == 'edit') ? "/$page" : "");
	$html = "<form id=\"edit\" method=\"post\" action=\"$formAction\">\n";

	if ( $action == "edit" )
		$html .= "<input type=\"hidden\" name=\"page\" value=\"$page\" />\n";
	else
		$html .= "<p>Title: <input id=\"title\" type=\"text\" name=\"page\" /></p>\n";

	if ( $action == "new" )
		$text = "";

	$html .= "<p><textarea id=\"text\" name=\"newText\" rows=\"" . EDIT_ROWS . "\">$text</textarea></p>\n";
	$html .= "<p><input type=\"hidden\" name=\"action\" value=\"save\" />";
	$html .= "<input id=\"save\" type=\"submit\" value=\"Save\" />\n";
	$html .= "<input id=\"cancel\" type=\"button\" onclick=\"history.go(-1);\" value=\"Cancel\" /></p>\n";
	$html .= "</form>\n";
}
else if ( $action == "logout" )
{
	destroy_session();
	header("Location: " . SELF);
	exit;
}
else if ( $action == "upload" )
{
	if ( DISABLE_UPLOADS )
	{
		$html = "<p>Image uploading has been disabled on this installation.</p>";
	}
	else
	{
		$html = "<form id=\"upload\" method=\"post\" action=\"" . SELF . "\" enctype=\"multipart/form-data\"><p>\n";
		$html .= "<input type=\"hidden\" name=\"action\" value=\"uploaded\" />";
		$html .= "<input id=\"file\" type=\"file\" name=\"userfile\" />\n";
		$html .= "<input id=\"upload\" type=\"submit\" value=\"Upload\" />\n";
		$html .= "<input id=\"cancel\" type=\"button\" onclick=\"history.go(-1);\" value=\"Cancel\" />\n";
		$html .= "</p></form>\n";
	}
}
else if ( $action == "uploaded" )
{
	if ( !DISABLE_UPLOADS )
	{
		$dstName = sanitizeFilename($_FILES['userfile']['name']);
		$fileType = $_FILES['userfile']['type'];
		preg_match('/\.([^.]+)$/', $dstName, $matches);
		$fileExt = isset($matches[1]) ? $matches[1] : null;
		
		if (in_array($fileType, explode(',', VALID_UPLOAD_TYPES)) &&
			in_array($fileExt, explode(',', VALID_UPLOAD_EXTS)))
		{
			$errLevel = error_reporting(0);

			if ( move_uploaded_file($_FILES['userfile']['tmp_name'], 
				BASE_PATH . "/images/$dstName") === true ) 
			{
				$html = "<p class=\"note\">File '$dstName' uploaded</p>\n";
			}
			else
			{
				$html = "<p class=\"note\">Upload error</p>\n";
			}

			error_reporting($errLevel);
		} else {
			$html = "<p class=\"note\">Upload error: invalid file type</p>\n";
		}
	}

	$html .= toHTML($text);
}
else if ( $action == "save" )
{
	$newText = stripslashes($_REQUEST['newText']);

	$errLevel = error_reporting(0);
	$success = file_put_contents($filename, $newText);
 	error_reporting($errLevel);

	if ( $success )	
		$html = "<p class=\"note\">Saved</p>\n";
	else
		$html = "<p class=\"note\">Error saving changes! Make sure your web server has write access to " . PAGES_PATH . "</p>\n";

	$html .= toHTML($newText);
}
/*
else if ( $action == "rename" )
{
	$html = "<form id=\"rename\" method=\"post\" action=\"" . SELF . "\">";
	$html .= "<p>Title: <input id=\"title\" type=\"text\" name=\"page\" value=\"" . htmlspecialchars($page) . "\" />";
	$html .= "<input id=\"rename\" type=\"submit\" value=\"Rename\">";
	$html .= "<input id=\"cancel\" type=\"button\" onclick=\"history.go(-1);\" value=\"Cancel\" />\n";
	$html .= "<input type=\"hidden\" name=\"action\" value=\"renamed\" />";
	$html .= "<input type=\"hidden\" name=\"prevpage\" value=\"" . htmlspecialchars($page) . "\" />";
	$html .= "</p></form>";
}
else if ( $action == "renamed" )
{
	$pp = $_REQUEST['prevpage'];
	$pg = $_REQUEST['page'];

	$prevpage = sanitizeFilename($pp);
	$prevpage = urlencode($prevpage);
	
	$prevfilename = PAGES_PATH . "/$prevpage.txt";

	if ( rename($prevfilename, $filename) )
	{
		// Success.  Change links in all pages to point to new page
		if ( $dh = opendir(PAGES_PATH) )
		{
			while ( ($file = readdir($dh)) !== false )
			{
				$content = file_get_contents($file);
				$pattern = "/\[\[" . $pp . "\]\]/g";
				preg_replace($pattern, "[[$pg]]", $content);
				file_put_contents($file, $content);
			}
		}
	}
	else
	{
		$html = "<p class=\"note\">Error renaming file</p>\n";
	}
}
*/
else if ( $action == "all_name" )
{
	$dir = opendir(PAGES_PATH);
	$filelist = array();

	$color = "#ffffff";

	while ( $file = readdir($dir) )
	{
		if ( $file{0} == "." )
			continue;

		$afile = preg_replace("/(.*?)\.txt/", "<a href=\"" . SELF . VIEW . "/\\1\">\\1</a>", $file);
		$efile = preg_replace("/(.*?)\.txt/", "<a href=\"?action=edit&amp;page=\\1\">edit</a>", urlencode($file));

		array_push($filelist, "<tr style=\"background-color: $color;\"><td>$afile</td><td width=\"20\"></td><td>$efile</td></tr>");

		if ( $color == "#ffffff" )
			$color = "#f4f4f4";
		else
			$color = "#ffffff";
	}

	closedir($dir);

	natcasesort($filelist);
	
	$html = "<table>";


	for ($i = 0; $i < count($filelist); $i++)
	{
		$html .= $filelist[$i];
	}

	$html .= "</table>\n";
}
else if ( $action == "all_date" )
{
	$html = "<table>\n";
	$dir = opendir(PAGES_PATH);
	$filelist = array();
	while ( $file = readdir($dir) )
	{
		if ( $file{0} == "." )
			continue;
			
		$filelist[preg_replace("/(.*?)\.txt/", "<a href=\"" . SELF . VIEW . "/\\1\">\\1</a>", $file)] = filemtime(PAGES_PATH . "/$file");
	}

	closedir($dir);

	$color = "#ffffff";
	arsort($filelist, SORT_NUMERIC);

	foreach ($filelist as $key => $value)
	{
		$html .= "<tr style=\"background-color: $color;\"><td valign=\"top\">$key</td><td width=\"20\"></td><td valign=\"top\"><nobr>" . date(TITLE_DATE_NO_TIME, $value) . "</nobr></td></tr>\n";
		
		if ( $color == "#ffffff" )
			$color = "#f4f4f4";
		else
			$color = "#ffffff";
	}
	$html .= "</table>\n";
}
else if ( $action == "search" )
{
	$matches = 0;
	$q = $_REQUEST['q'];
	$html = "<h1>Search: $q</h1>\n<ul>\n";

	if ( trim($q) != "" )
	{
		$dir = opendir(PAGES_PATH);
		
		while ( $file = readdir($dir) )
		{
			if ( $file{0} == "." )
				continue;

			$text = file_get_contents(PAGES_PATH . "/$file");
			
			if ( eregi($q, $text) || eregi($q, $file) )
			{
				++$matches;
				$file = preg_replace("/(.*?)\.txt/", "<a href=\"" . SELF . VIEW . "/\\1\">\\1</a>", $file);
				$html .= "<li>$file</li>\n";
			}
		}
		
		closedir($dir);
	}

	$html .= "</ul>\n";
	$html .= "<p>$matches matched</p>\n";
}
else
{
	$html = toHTML($text);
}

$datetime = '';

if ( ($action == "all_name") || ($action == "all_date"))
	$title = "All Pages";
	
else if ( $action == "upload" )
	$title = "Upload Image";

else if ( $action == "new" )
	$title = "New";

else if ( $action == "search" )
	$title = "Search";

else
{
	$title = $page;

	if ( TITLE_DATE )
	{
		$datetime = "<span class=\"titledate\">" . date(TITLE_DATE, @filemtime($filename)) . "</span>";
	}
}

// Disable caching on the client (the iPhone is pretty agressive about this
// and it can cause problems with the editing function)

header("Cache-Control: no-cache, must-revalidate"); // HTTP/1.1
header("Expires: Mon, 26 Jul 1997 05:00:00 GMT"); // Date in the past

print "<!DOCTYPE html PUBLIC \"-//W3C//DTD XHTML 1.0 Strict//EN\" \"http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd\">\n";
print "<html>\n";
print "<head>\n";
print "<link rel=\"apple-touch-icon\" href=\"apple-touch-icon.png\"/>";
print "<meta name=\"viewport\" content=\"width=device-width, initial-scale=1.0, minimum-scale=1.0, user-scalable=false\" />\n";

print "<link type=\"text/css\" rel=\"stylesheet\" href=\"" . BASE_URI . "/" . CSS_FILE ."\" />\n";
print "<title>$title</title>\n";
print "</head>\n";
print "<body>\n";
print "<div class=\"titlebar\">$title <span style=\"font-weight: normal;\">$datetime</span></div>\n";

printToolbar();

print "<div class=\"main\">\n";
print "$html\n";
print "</div>\n";

print "</body>\n";
print "</html>\n";

?>
