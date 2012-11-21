<?php

function htmlHeaders() {
	echo "<html><head>";
	echo "<link type='text/css' rel='stylesheet' href='shared.css' />";
	echo "</head> <body>";
	
	echo "<div id='bodyWrapper'>";
	echo "<img src='orange-anchor.gif' class='anchor' />";
}


function htmlFooters() {
	echo "</div>";
	echo "</body></html>";
}

function getPassword(){
		//NOTE: To run this script, put a password.txt in your folder with the
	//db password in it.  I did it this way to avoid putting it on github!
	$pwfile = fopen('password.txt', 'r'); 
	$pw = fgets($pwfile);
	fclose($pwfile);
	return $pw;
}

?>