<?php

print "<!DOCTYPE html PUBLIC \"-//W3C//DTD XHTML 1.0 Strict//EN\" \"http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd\">\n";
print '<html lang="' . W2_LOCALE . '">' . "\n";
print "<head>\n";
print '<meta charset="' . W2_CHARSET . '">' . "\n";
print "<link rel=\"apple-touch-icon\" href=\"apple-touch-icon.png\"/>";
print "<meta name=\"viewport\" content=\"width=device-width, initial-scale=1.0, minimum-scale=1.0, user-scalable=false\" />\n";

print "<link type=\"text/css\" rel=\"stylesheet\" href=\"" . BASE_URI . "/" . CSS_FILE ."\" />\n";
print "<title>" . __( $title ) . "</title>\n";
print "</head>\n";

print "<body>\n";

print "<div align=\"center\">\n";
print "<div margin=\"auto\" class=\"wrapper\">\n";

print "<div id=\"header\" style=\"display:grid; grid-template-columns: 1fr 1fr\">\n";

print "<div align=\"left\">\n";
print "<h1>$title</h1>\n";
print "</div>\n"; // title

print "<div align=\"right\">\n";
print "<br />\n";
print "<br />\n";
printHome();
print " - ";
printAll();
print " - ";
printRecent();
print "</div>\n"; // list

print "</div>\n"; // header

print "<div align=\"left\">\n";
print "<hr />\n";

printNavmenu();

print "$html\n";
print "<hr />";
print "</div>\n"; // main

print "<div id=\"footer\" style=\"display:grid; grid-template-columns: 1fr 1fr\">\n";

print "<div align=\"left\">\n";
printSearch();
print "</div>\n"; // search

print "<div align=\"right\">\n";
printEdit();
print " - ";
printNew();
print " - ";
printUpload();
print "</div>\n"; // edit

print "</div>\n"; // footer

print "</div>\n"; // wrapper
print "</div>\n"; // center

print "</body>\n";
print "</html>\n";

