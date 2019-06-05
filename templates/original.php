<?php

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

print "<div class=\"toolbar\">";
printEditbar();
printListbar();
printSearch();
print "</div>\n";

print "<div class=\"main\">\n";
print "$html\n";
print "</div>\n";

print "</body>\n";
print "</html>\n";

