<?php
#Author: Sunnefa Lind
#Project:
#Date:

$directory = 'icons/10001';

$host = 'localhost';
$user = 'icons';
$pass = 'icons';
$data = 'icons';

mysql_connect($host, $user, $pass);
mysql_select_db($data);

foreach(scandir($directory) as $file) {
	if($file != '.' || $file != '..') {
		$filepath = 'http://localhost/Icontastic/icons/10001/' . $file;
		$res = mysql_query("INSERT INTO icons (path, categories_id, users_id, date) VALUES('$filepath', 0, 1, 1953457839)");
		if($res) echo 'Success';
		else echo mysql_error() . '<br />';
	}
}
?>